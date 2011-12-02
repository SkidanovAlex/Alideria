<?

if( !$mid_php )
	die( );
	
$value = $player->till;

if( $value == 0 && $player->level >= 6 )
{
	$value = 162;
	$player->SetTill( 162 );
}

$empties = 0;
$stats = Array( 0, 0, 0, 0, 0, 0, 0, 0 );
$id = 1;
for( $i = 0; $i < 3; ++ $i )
{
	for( $j = 0; $j < 3; ++ $j )
	{
		$moo = $value / $id;
		settype( $moo, 'integer' );
		$moo %= 3;
		
		if( $moo == 1 )
		{
			++ $stats[$i];
			++ $stats[$j + 3];
			if( $i == $j ) ++ $stats[6];
			if( $i == 2 - $j ) ++ $stats[7];
		}
		if( $moo == 2 )
		{
			-- $stats[$i];
			-- $stats[$j + 3];
			if( $i == $j ) -- $stats[6];
			if( $i == 2 - $j ) -- $stats[7];
		}
		if( $moo == 0 ) $empties ++;
		
		$id *= 3;
	}
}

$balance = 0;
for( $i = 0; $i < 8; ++ $i )
	if( $stats[$i] == 2 ) ++ $balance;
	else if( $stats[$i] == 3 ) 
	{ 
		$player->SetTill( 0 );
		f_MQuery( "UPDATE player_talks SET talk_id = 39 WHERE player_id = $player->player_id" );
		die( "<script>location.href='game.php';</script>" );
	}
	else if( $stats[$i] == -3 ) 
	{ 
		$player->SetTill( 0 );
		f_MQuery( "UPDATE player_talks SET talk_id = 40 WHERE player_id = $player->player_id" );
		die( "<script>location.href='game.php';</script>" );
	}

if( !$empties )
{
	$player->SetTill( 0 );
	f_MQuery( "UPDATE player_talks SET talk_id = 41 WHERE player_id = $player->player_id" );
	die( "<script>location.href='game.php';</script>" );
}

if( isset( $_GET['red_black_action'] ) )
{
	if( $balance > 1 || $balance == 1 && mt_rand( 1, 2 ) == 1 ) $mob_id = 14;
	else if( $balance == 1 && mt_rand( 1, 3 ) < 3 || $balance == 0 && mt_rand( 1, 3 ) == 1 ) $mob_id = 16;
	else $mob_id = 15;

	$act = $_GET['red_black_action'];
	
	if( $act == "surrender" )
	{
		$player->SetTill( 0 );
		f_MQuery( "UPDATE player_talks SET talk_id = 40 WHERE player_id = $player->player_id" );
		die( "<script>location.href='game.php';</script>" );
	}
	
	$ok = true;
	settype( $act, 'integer' );
	if( $act < 0 ) $ok = false;
	if( $act % 10 > 2 ) $ok = false;
	$tmp = $act / 10;
	settype( $tmp, 'integer' );
	if( $tmp > 2 ) $ok = false;
	
	$id = pow( 3, 3 * $tmp + $act % 10 );
	
	$ch = $value / $id;
	settype( $ch, 'integer' );
	if( $ch % 3 != 0 ) $ok = false;
	
	if( !$ok ) RaiseError( "Неверное значение параметра red_black_action: $act" );
	
	$player->SetTill( $value + $id * 2 );
	
	include( "mob.php" );
	$mob = new Mob;
	$mob->CreateMob( $mob_id, $loc, $player->depth );
	$mob->AttackPlayer( $player->player_id, /*win_action*/2, $id, false );
	
	die( "<script>location.href='combat.php';</script>" );
}

echo "Выбери клетку, в которую ты бы хотел поставить камень:<br><br>";

echo "<table width=240 height=240 cellspacing=0 cellpadding=0 border=0>";

$id = 1;
for( $i = 0; $i < 3; ++ $i )
{
	echo "<tr height=80>";
	for( $j = 0; $j < 3; ++ $j )
	{
		echo "<td width=80 height=80>";
		ScrollLightTableStart( );
		
		$moo = $value / $id;
		settype( $moo, 'integer' );
		$moo %= 3;
		
		if( $moo == 0 ) $src = "empty.gif";
		else if( $moo == 1 ) $src = "images/misc/red_stone.png";
		else $src = "images/misc/black_stone.png";
		
		$more = "";
		if( $moo == 0 ) $more = "style='cursor:pointer;' onclick='location.href=\"game.php?red_black_action=$i$j\"'";
		
		echo "<img width=70 height=70 src=$src $more border=0>";
		
		ScrollLightTableEnd( );
		echo "</td>";
		$id *= 3;
	}
	echo "</tr>";
}

echo "</table>";

?>

<br>
<li><a href=game.php?red_black_action=surrender>Сдаться чародею
