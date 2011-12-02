<?

if( !$mid_php ) die( );

$res = f_MQuery( "SELECT stavkas FROM player_casino WHERE player_id = {$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr ) $stavkas = 0;
else $stavkas = $arr[0];

$stavkas = 45 - $stavkas;

echo "<table><tr><td>";
ScrollLightTableStart( );
echo "<b>Результаты предыдущей лотереи:</b><br>";
include( 'lottery_winner.html' );
ScrollLightTableEnd( );
echo "<br>";
ScrollLightTableStart( );

$ok = false;
f_MQuery( "LOCK TABLE lottery WRITE, characters WRITE, player_log WRITE" );
$res2 = f_MQuery( "SELECT * FROM lottery WHERE player_id = {$player->player_id}" );
if( f_MNum( $res2 ) )
{
	f_MQuery( "UNLOCK TABLES" );
	$ok = true;
}
else
{
	if( isset( $_GET['bet'] ) )
	{
		settype( $_GET['bet'], 'integer' );
		if( $_GET['bet'] < 0 || $_GET['bet'] > 4 ) die( );
		$stavkas = Array( 20, 50, 100, 500, 1000 );
		$stavka = $stavkas[$_GET['bet']];
		if( $player->SpendMoney( $stavka ) )
		{
			$player->AddToLogPost( 0, - $stavka, 5, 2 );
			f_MQuery( "INSERT INTO lottery ( player_id, value ) VALUES ( $player->player_id, $stavka )" );
			f_MQuery( "UNLOCK TABLES" );
			include( 'lottery_update.php' );
			update_lottery( );
			$ok = true;
			checkZhorik( $player, 17, 3 ); // квест жорика трижды поучаствовать в лотерее
		}
		else
		{
			f_MQuery( "UNLOCK TABLES" );
			$player->syst( 'У вас недостаточно денег!' );
		}
	}
	else f_MQuery( "UNLOCK TABLES" );
}

if( $ok ) echo "<b>Ваша ставка на сегодняшнюю лотерею принята</b><br><br>";
else
{
	echo "<b>Поставить:&nbsp;</b>";
	echo "<button onclick='location.href=\"game.php?bet=0\"' class=ss_btn style='width:120px;'>20 дублонов</button>&nbsp;";
	echo "<button onclick='location.href=\"game.php?bet=1\"' class=ss_btn style='width:120px;'>50 дублонов</button>&nbsp;";
	echo "<button onclick='location.href=\"game.php?bet=2\"' class=ss_btn style='width:120px;'>100 дублонов</button>&nbsp;";
	echo "<button onclick='location.href=\"game.php?bet=3\"' class=ss_btn style='width:120px;'>500 дублонов</button>&nbsp;";
	echo "<button onclick='location.href=\"game.php?bet=4\"' class=ss_btn style='width:120px;'>1000 дублонов</button>&nbsp;";
}

include( 'lottery_bets.html' );

ScrollLightTableEnd( );
echo "</td></tr></table>";

?>
