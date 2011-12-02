<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

function humanFSize($size)
{
    $filesizename = array("Байт", "Кб", "<font color=darkred>Мб</font>", "<font color=red><b>Гб</b></font>");
    return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2).$filesizename[$i];
}

$res = f_MQuery( "SHOW TABLES" );

while( $arr = f_MFetch( $res ) )
{
	$sql = "SHOW TABLE STATUS LIKE '".$arr[0]."'";
    $que = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_assoc($que);
    $moo = $row['Data_length']+$row['Index_length'];

    echo $arr[0].": <b>".humanFSize($moo)."</b><br>";
}

?>
