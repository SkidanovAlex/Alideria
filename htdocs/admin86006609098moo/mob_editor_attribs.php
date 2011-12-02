<?

include( '../functions.php' );

$mob_id = $HTTP_GET_VARS['mob_id'];
$attrib_id = $HTTP_GET_VARS['attrib_id'];
$value = $HTTP_GET_VARS['value'];

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['del'] ) )
	f_MQuery( "DELETE FROM mob_attributes WHERE mob_id=$mob_id AND attribute_id=$attrib_id" );
else
	f_MQuery( "INSERT INTO mob_attributes VALUES ( $mob_id, $attrib_id, $value )" );

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='mob_editor_mid.php?id=$mob_id';\n" ); ?>
</script>
