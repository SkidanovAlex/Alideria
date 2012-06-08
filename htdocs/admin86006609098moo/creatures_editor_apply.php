<?

include( '../functions.php' );

$id = $HTTP_POST_VARS['id'];
$name = $HTTP_POST_VARS['nm'];
$image = $_POST['image'];
$attack = $HTTP_POST_VARS['attack'];
$defence = $HTTP_POST_VARS['defence'];
$genre = $HTTP_POST_VARS['genre'];

$effect_dmg_p = "";//addslashes($HTTP_POST_VARS['effect_dmg_p']);
$effect_dmg_c = "";//addslashes($HTTP_POST_VARS['effect_dmg_c']);
$effect_die = "";//addslashes($HTTP_POST_VARS['effect_die']);
$effect_got_dmg = "";//addslashes($HTTP_POST_VARS['effect_got_dmg']);
$effect_always = addslashes($HTTP_POST_VARS['effect_always']);

$haste = ( $HTTP_POST_VARS['haste'] ? 1 : 0 );
$trample = ( $HTTP_POST_VARS['trample'] ? 1 : 0 );
$firststrike = ( $HTTP_POST_VARS['firststrike'] ? 1 : 0 );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
	f_MQuery( "DELETE FROM creatures WHERE creature_id=$id" );
else
{
	f_MQuery( "UPDATE creatures SET name='$name', image='$image', attack=$attack, defence=$defence, genre=$genre, haste=$haste, trample=$trample, firststrike=$firststrike WHERE creature_id=$id" );
	f_MQuery( "UPDATE creatures SET effect_dmg_p='$effect_dmg_p', effect_dmg_c='$effect_dmg_c', effect_die='$effect_die', effect_got_dmg='$effect_got_dmg', effect_always='$effect_always' WHERE creature_id=$id" );
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='creatures_editor_mid.php?id=$id';\n" ); ?>
</script>
