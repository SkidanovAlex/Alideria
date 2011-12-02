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
        	$player->AddMoney( 1200 );
        	$player->AddToLogPost( 0, 1200, 29 );
        }
        if( $_GET['pick']  == 1 )  
        {
        	$player->AddItems( 75854, 4 );
        	$player->AddToLogPost( 75854, 4, 29 );
        }
        if( $_GET['pick']  == 2 )  
        {
        	$player->AddItems( 109, 24 );
        	$player->AddToLogPost( 109, 24, 29 );
        }
        if( $_GET['pick']  == 3 )  
        {
        	$player->AddItems( 75930, 1 );
        	$player->AddToLogPost( 75930, 1, 29 );
        }
        if( $_GET['pick']  == 4 )  
        {
        	$player->AddItems( 77029, 1 );
        	$player->AddToLogPost( 77029, 1, 29 );
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
	echo "<li><a href=game.php?e=$arr[entry_id]&pick=0>1200 дублонов</a>";
	echo "<li><a href=game.php?e=$arr[entry_id]&pick=1>4 Черепах</a>"; // 75854
	echo "<li><a href=game.php?e=$arr[entry_id]&pick=2>24 Сома</a>"; // 109
	echo "<li><a href=game.php?e=$arr[entry_id]&pick=3>1 Нектар</a>"; // 75930
	echo "<li><a href=game.php?e=$arr[entry_id]&pick=4>1 Таинственная награда</a>"; // 77029

//	echo "<li><a href=game.php?e=$arr[entry_id]&pick=1>8 кусков Медной руды</a>"; // 17
//	echo "<li><a href=game.php?e=$arr[entry_id]&pick=3>4 куска Оловянной руды</a>"; // 19
//	echo "<li><a href=game.php?e=$arr[entry_id]&pick=4>2 куска Серебряной руды</a>"; // 20


}

?>
