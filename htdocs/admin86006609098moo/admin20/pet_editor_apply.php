<?

include( '../functions.php' );

$id = $HTTP_POST_VARS['id'];
$name = $HTTP_POST_VARS['nm'];
$image = $HTTP_POST_VARS['image'];
$descr = $HTTP_POST_VARS['descr'];
$genre = $HTTP_POST_VARS['genre'];

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
	f_MQuery( "DELETE FROM pets WHERE pet_id=$id" );
else
{
	f_MQuery( "UPDATE pets SET name='$name', image='$image', descr='$descr', genre=$genre WHERE pet_id=$id" );
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='pet_editor_mid.php?id=$id';\n" ); ?>
</script>
