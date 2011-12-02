<?
require_once("time_functions.php");


include_once( 'functions.php' );
include_once( 'clan.php' );
include_once( 'clan_wonders.php' );
include_once( 'player.php' );

f_MConnect( );

// wonders
if( isWonderNow( ) )
{
	$gres = f_MQuery( "SELECT clan_id, stage FROM clan_wonders WHERE work=750 AND stage < 10 OR work=2500 AND stage >= 10 AND stage < 9 + {$wonders[$cur_wonder][1]}" );
	while( $garr = f_MFetch( $gres ) )
	{
		$clan_id = $garr[0];
		$stage = $garr[1];
   		$ok = true;
		if( $stage < 10 )
		{
    		$arr = $wonder_res[$stage];
        	foreach( $arr as $item_id=>$number )
        	{
        		if( $item_id == 0 ) $have = f_MValue( "SELECT money FROM clans WHERE clan_id=$clan_id" );
        		else if( $item_id == -1 ) $have = f_MValue( "SELECT food FROM clans WHERE clan_id=$clan_id" );
        		else $have = (int)f_MValue( "SELECT number FROM clan_items WHERE clan_id=$clan_id AND item_id=$item_id AND color=0" );
        		if( $have < $number ) { $ok = false; break; }
        	}
        }
    	if( !$ok ) echo "CLAN ID: $clan_id; Not enough resources";
    	else
    	{
	    	echo "CLAN ID: $clan_id; Stage completed";
	    	if( $stage < 10 ) foreach( $arr as $item_id=>$number )
    		{
        		if( $item_id == 0 ) f_MQuery( "UPDATE clans SET money=money - $number WHERE clan_id=$clan_id" );
        		else if( $item_id == -1 ) f_MQuery( "UPDATE clans SET food=food - $number WHERE clan_id=$clan_id" );
        		else $have = f_MQuery( "UPDATE clan_items SET number=number-$number WHERE clan_id=$clan_id AND item_id=$item_id AND color=0" );
				f_MQuery( "INSERT INTO clan_wonder_items_spent ( clan_id, item_id, number ) VALUES ( $clan_id, $item_id, $number )" );
    		}
    		if( $stage < 9 + $wonders[$cur_wonder][1] - 1 ) f_MQuery( "UPDATE clan_wonders SET stage=stage+1, work=0 WHERE clan_id=$clan_id AND wonder_id=$cur_wonder" );
    		else
    		{
    			f_MQuery( "UPDATE clan_wonders SET stage=100, work=0 WHERE clan_id=$clan_id AND wonder_id=$cur_wonder" );
    			$res = f_MQuery( "SELECT player_id FROM player_clans WHERE clan_id=$clan_id" );
    			while( $arr = f_MFetch( $res ) )
    			{
    				$plr = new Player( $arr[0] );
    				applyWonder( $cur_wonder, $plr, 1 );
    			}
   				$nm = f_MValue( "SELECT name FROM clans WHERE clan_id=$clan_id" );
   				glashSay( "<b>$nm</b> завершил работу над Чудом Света <b>{$wonders[$cur_wonder][0]}</b>!!!" );

   				$res = f_MQuery( "SELECT clan_id, item_id, number FROM clan_wonder_items_spent WHERE clan_id <> $clan_id" );
   				while( $arr = f_MFetch( $res ) )
   				{
   					$number = ceil( $arr['number'] * 0.9 );
   					$item_id = $arr['item_id'];
   					$cid = $arr['clan_id'];
					if( $item_id == 0 ) f_MQuery( "UPDATE clans SET money=money+$number WHERE clan_id=$cid" );
					elseif( $item_id == -1 ) f_MQuery( "UPDATE clans SET food=food+$number WHERE clan_id=$cid" );
					else
					{
						f_MQuery( "LOCK TABLE clan_items WRITE" );
						$moo = f_MValue( "SELECT count( item_id ) FROM clan_items WHERE clan_id=$cid AND item_id=$item_id AND color=0" );
						if( $moo ) f_MQuery( "UPDATE clan_items SET number=number+$number WHERE clan_id=$cid AND item_id=$item_id AND color=0" );
						else f_MQuery( "INSERT INTO clan_items ( clan_id, item_id, number, color ) VALUES ( $cid, $item_id, $number, 0 )" );
						f_MQuery( "UNLOCK TABLES" );
					}
   				}
   				f_MQuery( "DELETE FROM clan_wonder_items_spent" );
    		}
    	}
	}
}


$gres = f_MQuery( "SELECT DISTINCT clan_id FROM clan_build_queue" );

while( $garr = f_MFetch( $gres ) )
{
	$clan_id = $garr[0];
	$res = f_MQuery( "SELECT * FROM clan_build_queue WHERE clan_id=$clan_id ORDER BY entry_id LIMIT 1" );
	$arr = f_MFetch( $res );
	$eid = $arr['entry_id'];
   	$id = $arr['building_id'];

	if( $arr['deadline'] == 0 )
	{
    	$level = getBLevel( $id );
    	$qarr = getBuildingCost( $id, $level + 1 );
    	$rarr = getBuildResources( $clan_id );
    	$ok = true;
    	foreach( $qarr as $a=>$b )
    	{
    		if( isset( $rarr[$a] ) ) 
    		{
    			if( $rarr[$a] < $b ) $ok = false;
    		}
    		else if( $a > 0 && itemsSiloNum( $clan_id, $a ) < $b ) $ok = false;
    	}
    	if( $ok )
    	{
    		$dl = time( ) + $qarr[$hours] * 3600;
        	foreach( $qarr as $a=>$b )
        	{
        		if( $a == $money ) 
        		{
        			f_MQuery( "UPDATE clans SET money=money - $b WHERE clan_id = $clan_id" );
        		}
        		else if( $a == $food )
        		{
        			f_MQuery( "UPDATE clans SET food=food - $b WHERE clan_id = $clan_id" );
        		}
        		else
        		{
        			f_MQuery( "UPDATE clan_items SET number=number-$b WHERE clan_id=$clan_id AND item_id=$a" );
        		}
	       	}
       		f_MQuery( "DELETE FROM clan_items WHERE clan_id=$clan_id AND number <= 0" );
    		f_MQuery( "UPDATE clan_build_queue SET deadline=$dl WHERE clan_id=$clan_id AND entry_id=$eid" );
    	}
    	else echo "Для Ордена $clan_id постройка в данный момент невозможна - недостаточно ресурсов.<br>\n";
	}
	else if( $arr['deadline'] < time( ) + 5 )
	{
		f_MQuery( "DELETE FROM clan_build_queue WHERE entry_id=$eid" );
		echo "Для Ордена $clan_id завершена работа над постройкой $id<br>";
		$arr = f_MFetch( f_MQuery( "SELECT level FROM clan_buildings WHERE clan_id=$clan_id AND building_id=$id" ) );
		if( $arr ) f_MQuery( "UPDATE clan_buildings SET level=level+1 WHERE clan_id=$clan_id AND building_id=$id" );
		else f_MQuery( "INSERT INTO clan_buildings( clan_id, building_id, level ) VALUES ( $clan_id, $id, 1 )" );

		// магаз
		if( $id == 7 )
		{
			if( !$arr )
			{
    			$ores = f_MQuery( "SELECT name FROM clans WHERE clan_id=$clan_id" );
    			$oarr = f_MFetch( $ores );
    			$name = $oarr[0];
    			f_MQuery( "INSERT INTO shops ( owner_id, buy_mul, sell_mul, regime, location, place, name, cost, capacity ) VALUES ( $clan_id, 50, 100, 1, 2, 101, '$name - Палатка', 0, 10 )" );
    		}
    		else f_MQuery( "UPDATE shops SET capacity = capacity + 10 WHERE owner_id=$clan_id AND location=2 AND place=101" );
		}
	}
}

$treeres = f_MQuery("SELECT * FROM clan_tree_uping");

while ($tree = f_MFetch($treeres))
{
	$clan_id = $tree[0];
	$dl = $tree[1];
	if ($dl < time() + 5)
	{
		f_MQuery("DELETE FROM clan_tree_uping WHERE clan_id=".$clan_id);
		echo "Для Ордена $clan_id завершен очередной этап с Древом Жизни.";
		$treelive = f_MValue("SELECT tree_active FROM clans WHERE clan_id=$clan_id");
		if ($treelive == -2) f_MQuery("UPDATE clans SET tree_active=-1 WHERE clan_id=$clan_id");
		elseif($treelive == -1) f_MQuery("UPDATE clans SET tree_active=100 WHERE clan_id=$clan_id");
		else
		{
			$treelvl = f_MValue("SELECT level FROM clan_buildings WHERE building_id=14 AND clan_id=$clan_id");
			if ($treelvl < $max_tree_lvl)
			{
				f_MQuery("UPDATE clan_buildings SET level=level+1 WHERE building_id=14 AND clan_id=$clan_id");
				checkTreeEffects($clan_id, $treelvl + 1);
				//надо бы еще эффекты накинуть
			}
		}
	}
}

?>
