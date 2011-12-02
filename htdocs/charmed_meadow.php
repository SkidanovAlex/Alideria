<?

function processMeadow()
{
	global $player;
	global $till;
	global $fpd;

	$moo = array( array( 5, 3, 1 ), array( 6, 2, 1 ), array( 5, 4, 0 ), array( 6, 3, 0 ), array( 7, 2, 0 ), array( 5, 4, 0 ), array( 6, 3, 0 ), array( 7, 2, 0 ) );
	$id = mt_rand( 0, count( $moo ) - 1 );
	$n1 = $moo[$id][0];
	$n2 = $moo[$id][1];
	$n3 = $moo[$id][2];
	
	$i1 = f_MQuery( "SELECT item_id, effect FROM items WHERE type=25 AND price=100 ORDER BY rand() LIMIT $n1" );
	$i2 = f_MQuery( "SELECT item_id, effect FROM items WHERE type=25 AND price=300 ORDER BY rand() LIMIT $n2" );
	$i3 = f_MQuery( "SELECT item_id, effect FROM items WHERE type=25 AND price=1000 ORDER BY rand() LIMIT $n3" );
	f_MQuery( "LOCK TABLE player_revealed_feathers WRITE" );
	f_MQuery( "DELETE FROM player_revealed_feathers WHERE player_id={$player->player_id}" );
	while( $arr = f_MFetch( $i1 ) )
		f_MQuery( "INSERT INTO player_revealed_feathers( player_id, feather_id, item_id ) VALUES ( {$player->player_id}, $arr[effect], $arr[item_id] )" );
	while( $arr = f_MFetch( $i2 ) )
		f_MQuery( "INSERT INTO player_revealed_feathers( player_id, feather_id, item_id ) VALUES ( {$player->player_id}, $arr[effect], $arr[item_id] )" );
	while( $arr = f_MFetch( $i3 ) )
		f_MQuery( "INSERT INTO player_revealed_feathers( player_id, feather_id, item_id ) VALUES ( {$player->player_id}, $arr[effect], $arr[item_id] )" );
	f_MQuery( "UNLOCK TABLES" );
	$fpd->SetStatus( 0 );
	$player->SetRegime( 120 );
	$player->SetTill( 0 );
	$till = 0;
}

function clickMeadow()
{
	global $player;
	global $till;
	global $fpd;
	
	$gold = f_MValue( "SELECT gold FROM meadow_gold WHERE tile_id={$player->depth}" );
	if( $gold == 0 ) $add = 120;
	else if( $gold == 1 ) $add = 180;
	else if( $gold <= 3 ) $add = 300;
	else if( $gold <= 6 ) $add = 480;
	else $add = 900;

	$add *= 3;
	
	$tm = time( );
	$player->SetTill( $tm + $add );
	$till = $tm + $add;
	$fpd->SetStatus( 9 );
}

function renderMeadow()
{
	global $st, $st_act;
	global $player;
	
	$gold = f_MValue( "SELECT gold FROM meadow_gold WHERE tile_id={$player->depth}" );
	if( $gold == 0 ) $st .= "Вокруг огромное количество волшебной пыльцы. Похоже, здесь давно никто не проходил, и найти перышки не составит труда.<br>";
	else if( $gold == 1 ) $st .= "Вокруг есть достаточно много волшебной пыльцы, но встречаются и островки, где ее нет совсем. Поиски перышек тут займут немного больше времени.<br>";
	else if( $gold <= 3 ) $st .= "Вокруг есть несколько островков с волшебной пыльцой. Поиски перышек тут займут достаточно много времени.<br>";
	else if( $gold <= 6 ) $st .= "Вокруг есть совсем немного осровков с волшебной пыльцой. Поиски перышек тут займут очень много времени.<br>";
	else $st .= "Вокруг почти нет волшебной пыльцы. Поиски перышек тут займут вечность.<br>";
	
	$st_act .= '<a href="javascript:void(0)" onclick="forest_go( 2, 0 );" style="cursor:pointer"><li>Искать перышки</li></a>';
}

?>
