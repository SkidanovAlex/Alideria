<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

$pth = $_GET['pth'];
if ($pth != '')
{
$imageList = glob("../images/".$pth."/*.png");

echo "<table border=1>";
echo "<tr><td>Картинка</td><td>Путь</td></tr>";
foreach ($imageList as $i)
{
	echo "<tr><td><img src='".$i."'></td><td>".$i."</td></tr>";
}
echo "</table>";

}
?>
