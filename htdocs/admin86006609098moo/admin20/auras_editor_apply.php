<?

include( '../functions.php' );

$id = $HTTP_POST_VARS['id'];
$name = $HTTP_POST_VARS['nm'];
$effect = $HTTP_POST_VARS['effect'];
$dispell = $HTTP_POST_VARS['dispell'];
$level = $HTTP_POST_VARS['level'];
$genre = $HTTP_POST_VARS['genre'];

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
	f_MQuery( "DELETE FROM auras WHERE aura_id=$id" );
else
{
	f_MQuery( "UPDATE auras SET name='$name', level='$level', genre='$genre' WHERE aura_id=$id" );

	if( file_put_contents( "../spell_effects/aura$id.php", $effect ) === false )
	{
		print( "moo!<br>" );
		die( );
	}

	if( file_put_contents( "../spell_effects/aura{$id}dispell.php", $dispell ) === false )
	{
		print( "moo!<br>" );
		die( );
	}
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='auras_editor_mid.php?id=$id';\n" ); ?>
</script>
