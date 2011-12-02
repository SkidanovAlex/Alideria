<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );
include( "../arrays.php" );

$genres[-1] = "Без&nbsp;Стихии";

f_MConnect( );

include( 'admin_header.php' );

if( isset( $_GET[genre] ) )
{
	$type = $_GET[genre];
	settype( $type, 'integer' );

	$res = f_MQuery( "SELECT aura_id, name FROM auras WHERE genre = $genre ORDER BY name" );
}
else
	$res = f_MQuery( "SELECT aura_id, name FROM auras ORDER BY name" );
	
echo( '<a href=auras_list.php><b>Все</b></a> &nbsp; ' );
foreach( $genres as $a=>$b ) print( "<a href=auras_list.php?genre=$a><b>$b</b></a> &nbsp; \n" );
echo( "<br><br>" );

while( $arr = mysql_fetch_array( $res ) )
{
	print( "<a href=auras_editor_mid.php?id=$arr[aura_id] target=mid>$arr[name]</a><br>" );
}

f_MClose( );

?>
</div>
