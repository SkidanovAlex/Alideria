<?

include( '../functions.php' );

$id = $HTTP_POST_VARS['id'];
$name = $HTTP_POST_VARS['nm'];

f_MConnect( );

include( 'quest_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
	f_MQuery( "DELETE FROM quests WHERE quest_id=$id" );
else
{
	f_MQuery( "UPDATE quests SET name='$name' WHERE quest_id=$id" );
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='quest_editor_mid.php?id=$id';\n" ); ?>
</script>
