<?

if( !$mid_php ) die( );

$clan_id = ($player->location == 5) ? -1 : $player->clan_id;

if( isset( $_GET['enterLab'] ) )
{
	include_once( "locations/portal/createMaze.php" );

	f_MQuery( "UPDATE characters SET depth=1 WHERE player_id={$player->player_id}" );
	
	$maze = new Maze( $portalSize, $portalSize );
	if( !$maze->CheckMaze( $clan_id, 1 ) )
	{
		echo "<b><font color='darkred'>Лабиринт в данный момент создается. Попробуйте войти через несколько секунд!</font></b><br>";
		f_MQuery( "UPDATE characters SET depth=10 WHERE player_id={$player->player_id}" );
	}
	else
	{
		$entrance = f_MValue( "SELECT cell_id FROM portal_maze WHERE z = 1 AND clan_id={$clan_id} AND type=1" );
		f_MQuery( "LOCK TABLE portal_players WRITE" );
		f_MQuery( "DELETE FROM portal_players WHERE player_id={$player->player_id}" );
		f_MQuery( "INSERT INTO portal_players ( player_id, clan_id, cell_id, keys_mask ) VALUES ( {$player->player_id}, {$clan_id}, {$entrance}, 0 )" );
		f_MQuery( "UNLOCK TABLES" );
	
		die( "<script>location.href='game.php';</script>" );
	}
}

$expires = array( );

for( $i = 1; $i <= $portalMaxDepth; ++ $i )
{
	$expires[$i] = -1;
}

$res = f_MQuery( "SELECT * FROM portal_state WHERE clan_id={$clan_id} AND created > " . ( time( ) - $portalLifeTime ) );
while( $arr = f_MFetch( $res ) )
{
	$expires[$arr['z']] = $arr['created'];
	if( $expires[$arr['z']] != -1 ) $expires[$arr['z']] += $portalLifeTime;
}

echo "<table><tr><td valign=top>";

echo "<table><tr><td><script>FLUc();</script><b>Информация о стабильности лабиринта</b><table>";

for( $i = 1; $i <= $portalMaxDepth; ++ $i )
{
	echo "<tr><td><script>FUlt();</script>$i-я глубина<script>FL();</script></td><td width=300><script>FUlt();</script>";
	if( $expires[$i] == -1 ) echo "<i>Нет информации</i>";
	else if( $expires[$i] - time( ) < 3600 ) echo "Просуществует <b>меньше часа</b>";
	else
	{
		$val = floor( ( $expires[$i] - time( ) ) / 3600 );
		echo "Просуществует около <b>$val</b> ".my_word_str( $val, "часа", "часов", "часов" );
	}
	echo "<script>FL();</script></td></tr>";
}

echo "</table><script>FLL();</script></td></tr></table>";

echo "</td><td width='20'><img height='0' width='20' src='empty.gif'></td><td valign=top width=200px>";

echo "<br>";
echo "<li STYLE='list-style-image: URL(\"images/dots/dot-special.gif\"); list-style-type: square'> <a href='game.php?enterLab=1'>Спуститься в Усыпальницу</a>";


echo "</td></tr></table>";

?>
<script>
SelShow(-1,'Sel1');
</script>
