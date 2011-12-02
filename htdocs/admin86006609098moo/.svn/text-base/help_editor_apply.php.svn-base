<?

include( '../functions.php' );

$id = $HTTP_POST_VARS['id'];
$new_id = $HTTP_POST_VARS['new_id'];
$title = $HTTP_POST_VARS['title'];
$url = $HTTP_POST_VARS['url'];
$parent_id = $HTTP_POST_VARS['parent_id'];

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
	f_MQuery( "DELETE FROM help_topics WHERE topic_id=$id" );
else
{
	if( $new_id != $id) 
	{
		$res = f_MQuery( "SELECT * FROM help_topics WHERE topic_id = $new_id" );
		$arr = f_MFetch( $res );
		if( $arr ) $new_id = $id;
	}
	f_MQuery( "UPDATE help_topics SET title='$title', url='$url', topic_id=$new_id, parent_id=$parent_id WHERE topic_id=$id" );
	f_MQuery( "UPDATE help_topics SET parent_id=$new_id WHERE parent_id=$id" );
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='help_editor_mid.php?id=$new_id';\n" ); ?>
</script>
