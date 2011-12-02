<?

include( 'locations/tailors_altar/stuff.php' );

function getAltarContent( )
{
	global $player;
	global $cvals, $cnames, $extracts;
	global $altar_attrs;
	$ret = '';
	$arr = f_MFetch( f_MQuery( "SELECT * FROM tailors_altar WHERE player_id={$player->player_id}" ) );
	if( $arr['item_level'] )
	{
    	$attr_id = $altar_attrs[$arr['color']][0];
    	$attr_val = $altar_attrs[$arr['color']][1];
		if( $arr['color'] != 14 ) $attr_val = ceil( $attr_val * $arr['item_level'] );
   		$req = getNatureReq( $arr['color'], $arr['item_level'] );

   		$er_str = "";
   		$er_str .= "<tr><td>Характеристики:&nbsp;</td><td align=right>&nbsp;</td><td><b>&nbsp;";
		$aarr = f_MFetch( f_MQuery( "SELECT * FROM attributes WHERE attribute_id=$attr_id" ) );
		$er_str .= "<font color=$aarr[color]>$aarr[name]</font>: +$attr_val";
		if( $arr['color'] == 14 ) $er_str .= "%";
		$er_str .= "</b></td></tr>";
		if( $req > 0 ) $er_str .= "<tr><td>Требования:&nbsp;</td><td>&nbsp;</td><td>&nbsp;<b><font color=green>Магия Природы: </font>$req</b></td></tr>";
    }
	if( $player->regime == 0 )
	{
    	if( !$arr['item_level'] )
    	{
    		$ret .= "Перед началом работы на алтаре выберите уровень вещи, для которой вы хотите приготовить смесь.<br>";
    		$ret .= "Обратите внимание, что смеси можно наложить только на вещи, одеваемые в слот накидки.<br>";
    		$ret .= "Вы можете готовить смеси <b>".($player->level-2)."-{$player->level}</b> уровней.<br><br>";
    		$ret .= "<table><colgroup><col width=100><col width=30><col width=10><col width=30><col width=10><col width=30><col width=10><tr>";
    		$ret .= "<td><b>Уровень: </b></td>";
    		for( $i = 0; $i < 3; ++ $i )
    		{
    			$lvl = $player->level - 2 + $i;
    			$ret .= "<td>\" + rFUcm() + \"<a href='javascript:__(1,$lvl)'><big><big>$lvl</big></big></a>\" + rFL() + \"</td>";
    		}
    		$ret .= "</tr></table>";
    	}
    	else
    	{
    		$ret .= "<table cellspacing=0 cellpadding=0><tr><td vAlign=top>";

    		// left column = what to do
    		$ret .= "<table cellspacing=1 cellpadding=0>";
    		$ret .= "<tr><td>Уровень смеси:&nbsp;</td><td>&nbsp;</td><td><b>&nbsp;$arr[item_level]</b></td></tr>";
    		$ret .= "<tr><td>Цвет смеси:&nbsp;</td><td style='width:30px;border:1px solid black;' bgcolor={$cvals[$arr[color]]}>&nbsp;</td><td><b>&nbsp;{$cnames[$arr[color]]}</b></td></tr>";
    		$ret .= $er_str;
    		$ret .= "</table><br>";

       		$ret .= "<b>Добавить экстракты:</b><br>";
       		$ret .= "<table><tr>";
       		$eid = 0;
       		$tclrs = array( "Лазурные", "Травяные", "Алые" );
       		foreach( $extracts as $item_id => $num )
       		{
       			$ret .= "<td width=100>\" + rFUcm() + \"<img width=50 height=50 src=images/items/res/ex".(1+$eid).".gif><br>В наличии: <b>".$player->NumberItems( $item_id )."</b><br>Надо: <b>".getExtractNum( $arr['item_level'] )."</b><br><a href='javascript:__(2,$item_id)'>{$tclrs[$eid]}</a>\" + rFL() + \"</td>";
       			++ $eid;
       		}
       		$ret .= "</tr></table>";

       		// separator
       		$ret .= "</td><td width=20>&nbsp;</td><td vAlign=top>";

       		// right column 
       		$ires = f_MQuery( "SELECT i.* FROM items as i INNER JOIN player_items as p ON i.item_id=p.item_id WHERE p.player_id={$player->player_id} AND i.type=13 AND i.improved=0 AND i.clan_marked=0 AND p.weared=0" );
       		$iarr = f_MFetch( $ires );
       		if( !$iarr ) $ret .= "<i>У вас нет ни одной накидки {$arr[item_level]}-ого уровня</i><br>";
       		else
       		{
       			$ret .= "<table cellspacing=0 cellpadding=0><tr><td valign=top><img width=50 height=50 src=images/items/$iarr[image]></td><td valign=top>&nbsp;<a target=_blank href=help.php?id=1010&item_id=$iarr[item_id]>$iarr[name]</a><br>&nbsp;Уровень: <b>$iarr[level]</b><br>";
       			$ret .= "&nbsp;<b>Стоимость наложения:</b> 10 ";
       			$fell_id = getFellId( $arr['item_level'] );
       			$farr = f_MFetch( f_MQuery( "SELECT name2_m FROM items WHERE item_id=$fell_id" ) );
       			$ret .= "<a target=_blank href=help.php?id=1010&item_id=$fell_id>$farr[0]</a>";
       			$ret .= "</td></tr></table>";
       			do
       			{
       				$ret .= "<li>Прочность: <b>$iarr[decay]/$iarr[max_decay]</b>. <a href='javascript:altar($iarr[item_id])'>Наложить смесь</a><br>";
       			} while( $iarr = f_MFetch( $ires ) );
       		}

       		// table finished
       		$ret .= "</td></tr></table>";
    	}
    }
    else
    {
    	$ret = "<center>";
    	$ret .= "<b>Вы накладываете смесь</b><br>";
    	$ret .= "<table><tr><td vAlign=top width=275 height=100>\"+rFUlt()+\"<b>Информация о смеси:</b><table>".$er_str."</table>\"+rFL()+\"</td>";
    	$ret .= "<td valign=top align=center width=150>";
    	$rem = $player->till - time( );
    	$ret .= "\" + NewTimer( $rem, 'Осталось: <b>', '</b>', 0, '__(7,0)' ) + \"";
    	$ret .= "<br><a href='javascript:__(6,0)'>Отменить</a>";
    	$ret .= "</td>";
		$ret .= "<td vAlign=top align=right width=275 height=100>\"+rFUrt()+\"<b>Информация о вещи:</b>";
		$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$arr[item_id]" ) );
		$ret .= "<table><tr><td vAlign=top><a target=_blank href=help.php?id=1010&item_id=$arr[item_id]>$iarr[name]</a><br>Прочнось: <b>$iarr[decay]/$iarr[max_decay]</b></td><td vAlign=top><img width=50 height=50 src=images/items/$iarr[image]></td></tr></table>";
		$ret .= "\"+rFL()+\"</td>";
		$ret .= "</tr></table>";
    	$ret .= "</center>";
    }

	return $ret;
}

function performAction( $a, $b )
{
	global $player;
	global $extracts, $altar_transforms;
	$ext_to_id = array( 8032 => 0, 8034 => 1, 8035 => 2 );
	$arr = f_MFetch( f_MQuery( "SELECT * FROM tailors_altar WHERE player_id={$player->player_id}" ) );
	if( $player->regime == 0 )
	{
    	if( !$arr['item_level'] )
    	{
    		if( $a == 1 )
    		{
    			if( $b > $player->level || $b < $player->level - 2 )
    				RaiseError( "На алтаре портных при выборе уровня вещи параметр B не входит в установленные границы.", "LEVEL: {$player->level}, B: $b" );

    			f_MQuery( "UPDATE tailors_altar SET item_level = $b WHERE player_id={$player->player_id}" );
    		} else RaiseError( "На алтаре портных при выборе уровня вещи параметр А не равен единице.", "a=$a" );
    	}
    	else
    	{
    		if( $a == 2 )
    		{
    			$day_mod = date( "d" ) + date( "m" ) + date( "Y" );
    			$day_mod = ( $day_mod % 15 ) * 10 + $day_mod * $day_mod;
    			$day_mod %= 100;

    			$mod = ( ( $arr['color'] ) << 1 );
    			$mod += $day_mod * $arr['color'] + ( $day_mod );
    			$mod += ( ( $arr['color'] * $arr['color'] ) >> 1 );
    			$mod %= 3; $mod += 3; $mod %= 3;

    			$clr = (int)$ext_to_id[$b]; 
    			$tid = ( $clr + $mod ) % 3;
    			if( isset( $altar_transforms[$arr['color']][$tid] ) /*&& $player->DropItems( $b, getExtractNum( $arr['item_level'] ) ) */ )
    			{
    				$nclr = $altar_transforms[$arr['color']][$tid];
    				if( $nclr <= 3 ) $nclr = $clr + 1;
    				else if( $nclr <= 6 ) $nclr = $clr + 4;
    				$player->AddToLogPost( $b, -1, 36, 2, 0 );
    				f_MQuery( "UPDATE tailors_altar SET color=$nclr WHERE player_id={$player->player_id}" );
    			}
    			else echo "alert( 'У вас нет необходимого количества экстрактов' );";
    		}
    		if( $a == 5 )
    		{
    			$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$b AND improved=0 AND type=13" ) );
    			if( !$iarr ) return;

    			if( $player->DropItems( getFellId( $arr['item_level'] ), 10 ) )
    			{
        			if( $player->DropItems( $b ) )
        			{
        				$player->AddToLogPost( getFellId( $arr['item_level'] ), -10, 36, 2, 0 );
        				$player->AddToLogPost( $b, -1, 36, 2, 0 );
        				f_MQuery( "UPDATE tailors_altar SET item_id=$b WHERE player_id={$player->player_id}" );
        				$player->SetRegime( 119 );
        				$player->SetTill( time( ) + 10 * 60 );
        			}
        			else $player->AddItems( getFellId( $arr['item_level'] ), 10 );
    			} else echo "alert( 'У вас недостаточно шкурок' );";
    		}
    	}
    }
    else if( $player->regime == 119 )
    {
    	if( $a == 7 && $player->till < time( ) + 2 )
    	{
	    	global $altar_attrs;
        	$attr_id = $altar_attrs[$arr['color']][0];
        	$attr_val = $altar_attrs[$arr['color']][1];
    		if( $arr['color'] != 14 ) $attr_val = ceil( $attr_val * $arr['item_level'] );
       		$req = getNatureReq( $arr['color'], $arr['item_level'] );

    		$item_id = copyItem( $arr['item_id'], true );
    		$iarr = f_MFetch( f_MQuery( "SELECT effect, req FROM items WHERE item_id=$arr[item_id]" ) );
    		$s_effect = "$attr_id:$attr_val.";
    		if( strlen( $iarr['effect'] ) ) $s_effect = substr( $iarr['effect'], 0, strlen( $iarr['effect'] ) - 1 ) . ":".$s_effect;
    		$s_req = $iarr['req'];
    		if( $req )
    		{
    			$s_req = "40:$req.";
    			if( strlen( $iarr['req'] ) ) $s_req = substr( $iarr['req'], 0, strlen( $iarr['req'] ) - 1 ). ":" .$s_req;
    		}
    		f_MQuery( "UPDATE items SET effect='$s_effect', req='$s_req' WHERE item_id=$item_id" );
    		$player->AddItems( $item_id );
			$player->AddToLogPost( $item_id, 1, 36, 2, 2 );

			$player->SetRegime( 0 );
			$player->SetTill( 0 );
    	}
    	if( $a == 6 )
    	{
    		$player->AddItems( getFellId( $arr['item_level'] ), 10 );
    		$player->AddItems( $arr['item_id'] );
			$player->AddToLogPost( getFellId( $arr['item_level'] ), 10, 36, 2, 1 );
			$player->AddToLogPost( $arr['item_id'], 1, 36, 2, 1 );
			$player->SetRegime( 0 );
			$player->SetTill( 0 );
    	}
    }
    else
    {
    	$player->SetRegime( 0 );
    	$player->SetTill( 0 );
    }
}

function getTailorsList( )
{
	global $player;
	$res = f_MQuery( "SELECT p.player_id FROM characters as p INNER JOIN online as o ON p.player_id=o.player_id INNER JOIN player_guilds AS g ON p.player_id=g.player_id WHERE p.loc={$player->location} AND p.depth={$player->depth} AND g.guild_id=".TAILORS_GUILD );
	if( !f_MNum( $res ) ) return "'<i>В локации нет ни одного портного</i><br>'";
	$ret = "'<table cellspacing=0 cellpadding=0>'";
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr[0] );
		$ret .= "+'<tr><td>'+" . $plr->Nick( );
		$ret .= "+'</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:teach($arr[0])\">Учить</a></td></tr>'";
	}
	$ret .= "+'<table>'";
	return $ret;
}

?>
