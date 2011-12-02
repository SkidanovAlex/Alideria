<?

if( !$mid_php ) die( );

if( isset( $_GET['e'] ) )
{
	$entry_id = (int)$_GET['e'];
	f_MQuery( "LOCK TABLE player_ta_winnings WRITE" );
	$arr = f_MFetch( f_MQuery( "SELECT count( entry_id ) FROM player_ta_winnings WHERE player_id={$player->player_id} AND entry_id=$entry_id" ) );
	if( $arr[0] > 0 )
	{
		f_MQuery( "DELETE FROM player_ta_winnings WHERE player_id={$player->player_id} AND entry_id=$entry_id" );
		f_MQuery( "UNLOCK TABLES" );

        if( $_GET['pick']  == 0 )  
        {
        	$player->AddMoney( 500 );
        	$player->AddToLogPost( 0, 500, 29 );
        }
        if( $_GET['pick']  == 1 )  
        {
        	$player->AddItems( 17, 8 );
        	$player->AddToLogPost( 17, 8, 29 );
        }
        if( $_GET['pick']  == 2 )  
        {
        	$player->AddItems( 109, 8 );
        	$player->AddToLogPost( 109, 8, 29 );
        }
        if( $_GET['pick']  == 3 )  
        {
        	$player->AddItems( 19, 4 );
        	$player->AddToLogPost( 19, 4, 29 );
        }
        if( $_GET['pick']  == 4 )  
        {
        	$player->AddItems( 20, 2 );
        	$player->AddToLogPost( 20, 2, 29 );
        }
	}
	else f_MQuery( "UNLOCK TABLES" );
}

$res = f_MQuery( "SELECT entry_id FROM player_ta_winnings WHERE player_id={$player->player_id} LIMIT 1" );
$arr = f_MFetch( $res );

if( $arr )
{
	echo "<b>Вы имеете выдающиеся заслуги перед Теллой.</b><br>";
	echo "<i>Вы заслуживаете щедрой награды от Городской Управы. Какую награду вы выберете?</i><br>";
	echo "<li><a href=game.php?e=$arr[entry_id]&pick=0>500 дублонов</a>";
	echo "<li><a href=game.php?e=$arr[entry_id]&pick=1>8 кусков Медной руды</a>"; // 17
	echo "<li><a href=game.php?e=$arr[entry_id]&pick=3>4 куска Оловянной руды</a>"; // 19
	echo "<li><a href=game.php?e=$arr[entry_id]&pick=4>2 куска Серебряной руды</a>"; // 20
	echo "<li><a href=game.php?e=$arr[entry_id]&pick=2>8 Сомов</a>"; // 109

}

?>
