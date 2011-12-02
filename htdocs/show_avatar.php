<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<?

$q = htmlspecialchars($_GET['i']);

$pos = strpos( $q, '.jpg' );
if( $pos === false )
{

	$pos = strpos( $q, '.png' );
	echo "<img width=200 height=450 src=".substr($q,0,$pos)."_.png border=0>";
	die( );
}
if( $pos === false ) die( );

echo "<img width=200 height=450 src=".substr($q,0,$pos)."_.jpg border=0>";

?>
