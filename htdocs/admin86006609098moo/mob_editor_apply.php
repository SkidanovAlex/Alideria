<?

include( '../functions.php' );

$id = $HTTP_POST_VARS['id'];
$name = $HTTP_POST_VARS['nm'];
$avatar = $HTTP_POST_VARS['avatar'];
$descr = $HTTP_POST_VARS['descr'];
$loc = $HTTP_POST_VARS['loc'];
$level = $HTTP_POST_VARS['level'];
$min_depth = $HTTP_POST_VARS['min_depth'];
$max_depth = $HTTP_POST_VARS['max_depth'];
$def_depth = $HTTP_POST_VARS['def_depth'];

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
	f_MQuery( "DELETE FROM mobs WHERE mob_id=$id" );
else
{
	f_MQuery( "UPDATE mobs SET name='$name', avatar='$avatar', descr='$descr', level=$level WHERE mob_id=$id" );
	f_MQuery( "UPDATE mobs SET loc='$loc', min_depth='$min_depth', max_depth='$max_depth', defend_depth='$def_depth' WHERE mob_id=$id" );
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='mob_editor_mid.php?id=$id';\n" ); ?>
</script>
