<?

include( '../functions.php' );

$id = $HTTP_POST_VARS['id'];
$name = $HTTP_POST_VARS['nm'];
$location = $HTTP_POST_VARS['location'];
$depth = $HTTP_POST_VARS['depth'];

$image = $_POST['image'];
$image_w = $_POST['image_w'];
$image_h = $_POST['image_h'];
$image_right = (($_POST['image_right']==='on')?1:0);

f_MConnect( );

include( 'quest_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
{
	$res = f_MQuery( "SELECT condition_id FROM npcs WHERE npc_id=$id" );
	$arr = f_MFetch( $res );
	if( $arr && $arr[0] != -1 ) f_MQuery( "DELETE FROM phrases WHERE phrase_id=$arr[0]" );
	f_MQuery( "DELETE FROM npcs WHERE npc_id=$id" );
}	
else
{
	f_MQuery( "UPDATE npcs SET name='$name', location='$location', depth=$depth, image_w='$image_w', image_h='$image_h', image_right='$image_right', image='$image' WHERE npc_id=$id" );
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='npc_editor_mid.php?id=$id';\n" ); ?>
</script>
