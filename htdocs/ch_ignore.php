<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">
<head><title>Настройки чата</title></head>

<br>
<b>Настройки фильтров и игнора в чате</b><br>
<br>
<?

include( 'functions.php' );
include( 'player.php' );

f_register_globals( );
f_MConnect( );
if( !check_cookie( ) ) die( 'Неверные настройки Cookie' );
$player_id = $HTTP_COOKIE_VARS[c_id];
$err = '';

if( isset( $_GET['switch'] ) )
{
	f_MQuery( "DELETE FROM ch_ignore WHERE player_id=$player_id AND target=0" );
	if( $_GET['switch'] == 'off' )
	{
		// ---------------------
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $msg = "ignore\n$player_id\n0\n";
        socket_write( $sock, $msg, strlen($msg) ); 
        socket_close( $sock );
        // ---------------------

		f_MQuery( "INSERT INTO ch_ignore VALUES( $player_id, 0 )" );
    }
    else
    {
		// ---------------------
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $msg = "ignore\n$player_id\n-1\n";
        socket_write( $sock, $msg, strlen($msg) ); 
        socket_close( $sock );
        // ---------------------
    }
}
if( isset( $_GET['switch2'] ) )
{
	f_MQuery( "DELETE FROM ch_ignore WHERE player_id=$player_id AND target=-1" );
	if( $_GET['switch2'] == 'off' )
	{
		// ---------------------
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $msg = "ignore\n$player_id\n-2\n";
        socket_write( $sock, $msg, strlen($msg) ); 
        socket_close( $sock );
        // ---------------------

		f_MQuery( "INSERT INTO ch_ignore VALUES( $player_id, -1 )" );
    }
    else
    {
		// ---------------------
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $msg = "ignore\n$player_id\n-3\n";
        socket_write( $sock, $msg, strlen($msg) ); 
        socket_close( $sock );
        // ---------------------
    }
}
else if( isset( $_POST['nm'] ) )
{
	$name = HtmlSpecialChars( $_POST['nm'], ENT_QUOTES );
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$name'" );
	$arr = f_MFetch( $res );
	if( !$arr ) $err = "<font color=darkred>Игрока &quot;".$name."&quot; не существует</font><br>";
	else if( $arr[0] == $player_id ) $err = "<font color=darkred>Нельзя добавить в игнор самого себя!</font><br>";
	else
	{
		// ---------------------
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $msg = "ignore\n$player_id\n$arr[0]\n";
        socket_write( $sock, $msg, strlen($msg) ); 
        socket_close( $sock );
        // ---------------------

		f_MQuery( "DELETE FROM ch_ignore WHERE player_id=$player_id AND target=$arr[0]" );
		f_MQuery( "INSERT INTO ch_ignore VALUES( $player_id, $arr[0] )" );
		die( '<script>location.href="ch_ignore.php";</script>' );
	}
}
else if( isset( $_GET['del'] ) )
{
	$del = $_GET['del'];
	settype( $del, 'integer' );
	f_MQuery( "DELETE FROM ch_ignore WHERE player_id=$player_id AND target=$del" );

	// ---------------------
    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($sock, "127.0.0.1", 1100);
    $msg = "ignore\n$player_id\n-$del\n";
    socket_write( $sock, $msg, strlen($msg) ); 
    socket_close( $sock );
    // ---------------------
}

$ignore_main = false;
$ignore_trade = false;

$st = '<script src=js/clans.php></script><script src=js/ii.js></script><table cellspacing=0 cellpadding=0>';
$num = 0;
$res = f_MQuery( "SELECT * FROM ch_ignore WHERE player_id=$HTTP_COOKIE_VARS[c_id]" );
while( $arr = f_MFetch( $res ) )
{
	if( $arr['target'] == 0 ) $ignore_main = true;
	else if( $arr['target'] == -1 ) $ignore_trade = true;
	else
	{
		++ $num;
		$plr = new Player( $arr['target'] );
		$st .= "<tr><td><script>document.write( ".($plr->Nick( ))." );</script>&nbsp;</td><td><a href=ch_ignore.php?del=$arr[target]>Удалить</a></td></tr>";
	}
}
if( $num == 0 ) $st = '<i>Ваш список игнора пуст.</i><br>';
else $st .= '</table>';

echo "<b>Общий чат</b><br>";
if( $ignore_main ) echo "В настоящий момент вы не получаете сообщения из Общего чата.<br><a href=ch_ignore.php?switch=on>Начать получать сообщения</a><br><br>";
else echo "В настоящий момент вы получаете сообщения из Общего чата.<br><a href=ch_ignore.php?switch=off>Прекратить получать сообщения</a><br><br>";

$res = f_MQuery( "SELECT level FROM characters WHERE player_id=$player_id" );
$arr = f_MFetch( $res );
if( $arr[0] > 1 )
{
    echo "<b>Торговый чат</b><br>";
    if( $ignore_trade ) echo "В настоящий момент вы не получаете сообщения из Торгового чата.<br><a href=ch_ignore.php?switch2=on>Начать получать сообщения</a><br><br>";
    else echo "В настоящий момент вы получаете сообщения из Торгового чата.<br><a href=ch_ignore.php?switch2=off>Прекратить получать сообщения</a><br><br>";
}

echo "<b>Настройки игнора</b><br>";
echo $st;

echo "<br>$err<form action=ch_ignore.php method=post>";
echo "Добавить игрока в игнор:<br>";
echo "<input type=text class=m_btn name=nm><br>";
echo "<small>Вы перестанете получать любые сообщения от указанного игрока</small><br>";
echo "<input type=submit class=ss_btn value='Добавить'>";
echo "</form>";

?>
