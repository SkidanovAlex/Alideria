<?

header("Content-type: text/html; charset=windows-1251");

include_once( '../functions.php' );

f_MConnect( );

include( 'ranks_header.php' );

$str = $HTTP_RAW_POST_DATA;
list($id_, $check) = @explode( "|", $str );
settype($id_, "integer");
settype($check, "integer");
if($id_ > 0)
{
	f_MQuery("UPDATE coincidence_ip SET checked = ".$check." WHERE id = ".$id_);
}
?>