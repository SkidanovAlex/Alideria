<?

header("Content-type: text/html; charset=windows-1251");

include( '../functions.php' );
include( '../arrays.php' );
include_once( '../forest_functions.php' );

f_MConnect( );

$letters = Array(
	0  => "<font color=lime><b>Î</b></font>",
	1  => "<font color=green><b>Ä</b></font>",
	2  => "<font color=aqua><b>Ç</b></font>",
	3  => "<font color=maroon><b>K</b></font>",
	4  => "<font color=grey><b>Â</b></font>",
	5  => "<font color=brown><b>Á</b></font>",
	6  => "<font color=blue><b>Ð</b></font>",
	7  => "<font color=yellow><b>Ý</b></font>",
	8  => "<font color=white><b>Å</b></font>",
	9  => "<font color=black><b>Ï</b></font>",
	10 => "<font color=red><b>C</b></font>",
	11 => "<font color=red><b>@</b></font>",
	12 => "<font color=#AA6446><b>#</b></font>",
	13 => "<font color=#646464><b>*</b></font>",
	14 => "<font color=#726693><b>&</b></font>",
	20 => "<font color=#FC0FC0><b>Ì</b></font>",
	21 => "<font color=#FF0000><b>Ç</b></font>",
	22 => "<font color=#F4C430><b>Ó</b></font>",
	23 => "<font color=#FF0000><b>U</b></font>",
	
	100 => "<font color=brown><b>R</b></font>",
	101 => "<font color=aqua><b>R</b></font>",
	102 => "<font color=#008800><b>R</b></font>",
	110 => "<font color=#00FF00><b>R</b></font>",
	
	200 => "<font color=brown><b>G</b></font>",
	201 => "<font color=white><b>G</b></font>",
	202 => "<font color=gray><b>G</b></font>",
	203 => "<font color=#0000FF><b>G</b></font>",
	204 => "<font color=black><b>G</b></font>",
	205 => "<font color=red><b>G</b></font>",
	206 => "<font color=yellow><b>G</b></font>",
	210 => "<font color=#00FF00><b>G</b></font>"
);

include( 'admin_header.php' );

$str = $HTTP_RAW_POST_DATA;
list($x, $y, $tile, $locat) = @explode( "|", $str );

if (!$locat) die();
$ut = new ForestUtils( $locat );
$ut->setTile( $x, $y, $tile );

echo( "set_tile( $x, $y, '{$letters[$tile]}', '{$forest_names[$tile]}' );" );

?>
