<?

include( '../functions.php' );

$id = $HTTP_POST_VARS['id'];
$name = $HTTP_POST_VARS['nm'];
$ingridients = $HTTP_POST_VARS['ingridients'];
$result = $HTTP_POST_VARS['result'];
$req = $HTTP_POST_VARS['req'];
$prof = $HTTP_POST_VARS['prof'];
$rank = $HTTP_POST_VARS['rank'];
$level = $HTTP_POST_VARS['level'];

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
	f_MQuery( "DELETE FROM recipes WHERE recipe_id=$id" );
else
{
	f_MQuery( "UPDATE recipes SET level=$level, rank=$rank, name='$name', ingridients='$ingridients', req='$req', result='$result', prof='$prof' WHERE recipe_id=$id" );
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='craft_editor_mid.php?id=$id';\n" ); ?>
</script>
