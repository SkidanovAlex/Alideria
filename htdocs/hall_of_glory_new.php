<script src='js/ajax.js'>
</script>
<script>
function show_info(a)
{
	document.getElementById('show_hall_of_glory').innerHTML = 'Подождите...'+a;
	query('hall_of_glory_func.php', a);
}
</script>

<?

include_once('player.php');

if( !isset( $mid_php ) ) die( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
$player_id = (int)$HTTP_COOKIE_VARS['c_id'];
$player = new Player( $player_id );

$res1 = f_MQuery("SELECT * FROM mobs ORDER BY level");
$res2=$res1;

$mob_id = 0;
if (isset($_GET['mob_id']))
	$mob_id = (int)$_GET['mob_id'];
if ($mob_id < 0) $mob_id = 0;

$hog = 0;
if (isset($_GET['hog']))
	$hog = (int)$_GET['hog'];
if ($hog < 0) $hog = 0;

echo "<br><table><tr><td align=left><script>FLUl();</script>";

// шапка BEGIN
$b1 = "border:1px solid black";
$b2 = "border-left:1px solid black;border-right:1px solid black;border-top:1px solid black";

echo "<table width=100% cellspacing=0 cellpadding=0 height=25><tr align=center>";

if ($hog!=0) $border=$b1; else $border=$b2;
//if ($hog!=0) echo "<td style='$border;width:110px;'><a href='game.php?hog=0'><b>Книга Монстров</b></a></td>";
echo "<td onclick='javascript:show_info(0)' style='$border;width:110px;'>Книга Монстров</td>";

if ($hog!=1) $border=$b1; else $border=$b2;
//if ($hog!=1) echo "<td style='$border;width:110px;'><a href='game.php?hog=1'><b>Книга Турниров</b></a></td>";
echo "<td onclick='javascript:show_info(1)' style='$border;width:110px;'>Книга Турниров</td>";

if ($hog!=2) $border=$b1; else $border=$b2;
//if ($hog!=2) echo "<td style='$border;width:110px;'><a href='game.php?hog=2'><b>Книга Дуэлянтов</b></a></td>";
echo "<td onclick='javascript:show_info(2)' style='$border;width:110px;'>Книга Дуэлянтов</td>";
/*
if ($hog!=3) $border=$b1; else $border=$b2;
if ($hog!=3) echo "<td style='$border;width:110px;'><a href='game.php?hog=3'><b>Книга еще</b></a></td>";
else echo "<td style='$border;width:110px;'>Книга еще</td>";
*/
if ($hog!=-1) echo "<td style='border-bottom:1px solid black;'></td>";

echo "</tr></table><br>";
// шапка END

echo "<div id='show_hall_of_glory'>&nbsp;</div>";

echo "<script>FLL();</script></td></tr></table>";
?>