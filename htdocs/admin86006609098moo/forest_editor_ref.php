<?

header("Content-type: text/html; charset=windows-1251");

include( '../functions.php' );
include( '../arrays.php' );
include_once( '../forest_functions.php' );

f_MConnect( );

$letters = Array(
	0  => "<font color=lime><b>�</b></font>",
	1  => "<font color=green><b>�</b></font>",
	2  => "<font color=aqua><b>�</b></font>",
	3  => "<font color=maroon><b>K</b></font>",
	4  => "<font color=grey><b>�</b></font>",
	5  => "<font color=brown><b>�</b></font>",
	6  => "<font color=blue><b>�</b></font>",
	7  => "<font color=yellow><b>�</b></font>",
	8  => "<font color=white><b>�</b></font>",
	9  => "<font color=black><b>�</b></font>",
	10 => "<font color=red><b>C</b></font>",
	11 => "<font color=red><b>@</b></font>",
	12 => "<font color=#AA6446><b>#</b></font>",
	13 => "<font color=#646464><b>*</b></font>"
);

include( 'admin_header.php' );

$str = $HTTP_RAW_POST_DATA;
list($x, $y, $tile) = @explode( "|", $str );

$ut = new ForestUtils( 1 );
$ut->setTile( $x, $y, $tile );

echo( "set_tile( $x, $y, '{$letters[$tile]}' );" );

?>
