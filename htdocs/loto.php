<?

if( !isset( $mid_php ) ) die( );

include_js( 'js/timer.js' );

$day = date( "d" );
$month = date( "m" );
$year = date( "Y" );
$hr = date( "H" );
$min = date( "i" );

$res = f_MQuery( "SELECT * FROM loto_numbers ORDER BY id" );

$st = "";
$n = 0;
$wn = Array( );
while( $arr = f_MFetch( $res ) ) 
{
	$wn[$n ++] = $arr[val];
	$st .= ", ".$arr[val];
}
$st = substr( $st, 2 );

ScrollLightTableStart( );
if( f_MNum( $res ) == 7 )
{
	echo "<b>���������� ������ ����������� ���������: <big>$st</big></b><br>";

	$qres = f_MQuery( "SELECT * FROM loto_past WHERE player_id={$player->player_id}" );
	$qarr = f_MFetch(  $qres );
	if( $qarr )
	{
		$val = $qarr['val'];
    	$nums = Array( );
    	for( $i = 0; $i < 7; ++ $i )
    	{
    		$nums[6 - $i] = $val % 10;
    		$val = floor( $val / 10 );
    	}
    	$st = "";
		for( $i = 0; $i < 7; ++ $i )
		{
    		if( $nums[$i] != $wn[$i] ) $st .= ", ".$nums[$i];
    		else $st .= ", <u>".$nums[$i]."</u>";
    	}
    	$st = substr( $st, 2 );
    	echo "<b>�� ����������� � ���������. ��������� ���� �����: <big>$st</big></b><br>";
    	if( $qarr[winnings] > 0 ) echo "<b>�� �������� <font color=red>$qarr[winnings]</font> ��������</b><br>";
    }

	echo "<br>";
	$tm = strtotime( "$day.$month.$year 16:00:05" );
	if( $tm < time( ) )
	{
		$tm += 60*60*24;
    }
    $del = $tm - time( );
    echo "<script>document.write( InsertTimer( $del, '<b>��������� �������� �����: <br><big><big>', '</big></big></b>', 1, 'location.href=\"game.php\";' ) );</script>";

    $res = f_MQuery( "SELECT * FROM loto_players WHERE player_id={$player->player_id}" );
    $ok = false;
    if( !f_MNum( $res ) )
    {
		if( isset( $_POST['num0'] ) )
		{
			if( $player->SpendMoney( 100 ) )
			{
				$player->AddToLogPost( 0, - 100, 5, 3 );
				$val = 0;
				for( $i = 0; $i < 7; ++ $i )
				{
					$a = $_POST["num$i"];
					settype( $a, 'integer' );
					if( $a < 0 || $a > 9 ) RaiseError( "������� ������� ������������ ����� �� �������", "$i : $a" );
					$val = $val * 10 + $a;
				}
				f_MQuery( "INSERT INTO loto_players VALUES( {$player->player_id}, $val )" );
				$ok = true;
			} else $player->syst( "� ��� �� ������� ����� �� �������." );
		}
	}
	else
	{
		$arr = f_MFetch( $res );
		$val = $arr['val'];
    	$ok = true;
    }

	if( !$ok )
	{
    	echo "<br>�� ������ ������ ����� �� ��������� �������� (100 ��������). ��� ����� �������� 7 ����� �� 0 �� 10:<br><form action=game.php method=post>";

    	$nums = Array( 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 );
    	for( $i = 0; $i < 7; ++ $i )
	    	echo create_select_small( "num$i", $nums, mt_rand( 0, 9 ) );
	    echo "<input type=submit class=ss_btn value=������></form>";
    }
    else
    {
    	$nums = Array( );
    	for( $i = 0; $i < 7; ++ $i )
    	{
    		$nums[6 - $i] = $val % 10;
    		$val = floor( $val / 10 );
    	}
    	$st = "";
    	for( $i = 0; $i < 7; ++ $i ) $st .= ", ".$nums[$i];
    	$st = substr( $st, 2 );
    	echo "<br><b>�� ��� ������ �������. ��������� ���� �����: <big>$st</big></b><br>";
    }
}
else
{
	echo "<b>�������� ���� ����� ������. ������� �������� �����: <big>$st</big></b><br><br>";
	$q = f_MNum( $res );
	$tm = strtotime( "$day.$month.$year 16:0$q:05" );
    $del = $tm - time( );
    echo "<script>document.write( InsertTimer( $del, '<b>��������� ����� ������ �������� �����: <br><big><big>', '</big></big></b>', 0, 'location.href=\"game.php\";' ) );</script>";

    $res = f_MQuery( "SELECT * FROM loto_players WHERE player_id={$player->player_id}" );
    $arr = f_MFetch( $res );
    if( $arr )
    {
    	$val = $arr['val'];
    	$nums = Array( );
    	for( $i = 0; $i < 7; ++ $i )
    	{
    		$nums[6 - $i] = $val % 10;
    		$val = floor( $val / 10 );
    	}
    	$st = "";
    	for( $i = 0; $i < 7; ++ $i ) $st .= ", ".$nums[$i];
    	$st = substr( $st, 2 );
    	echo "<br><b>�� ���������� � ���������. ��������� ���� �����: <big>$st</big></b><br>";
    }
}

echo "<br>";
echo "������� ���� �� ������ ��������� <a target=_blank href=help.php?id=34312>�����</a>.";

ScrollLightTableEnd( );

?>
