<?

$dont_check_params = true;
include( '../functions.php' );

$id = $HTTP_POST_VARS['id'];

$name = $HTTP_POST_VARS['nm'];
$name_m = $HTTP_POST_VARS['nm_m'];
$name2 = $HTTP_POST_VARS['nm2'];
$name2_m = $HTTP_POST_VARS['nm2_m'];
$name3 = $HTTP_POST_VARS['nm3'];
$name3_m = $HTTP_POST_VARS['nm3_m'];
$name4 = $HTTP_POST_VARS['nm4'];
$name4_m = $HTTP_POST_VARS['nm4_m'];
$name5 = $HTTP_POST_VARS['nm5'];
$name5_m = $HTTP_POST_VARS['nm5_m'];
$name6 = $HTTP_POST_VARS['nm6'];
$name6_m = $HTTP_POST_VARS['nm6_m'];

$cast_description = $HTTP_POST_VARS['cast_description'];

$descr = $HTTP_POST_VARS['descr'];
$descr2 = $HTTP_POST_VARS['descr2'];
$genre = $HTTP_POST_VARS['genre'];
$cost = $HTTP_POST_VARS['cost'];
$image_small = $HTTP_POST_VARS['image_small'];
$image_large = $HTTP_POST_VARS['image_large'];
$req = $HTTP_POST_VARS['req'];
$effect = stripslashes( $HTTP_POST_VARS['effect'] );
$level = $HTTP_POST_VARS['level'];
$status = $HTTP_POST_VARS['status'];
$mk = $HTTP_POST_VARS['mk'];
$multy = $HTTP_POST_VARS['multy'];

$unlucky = $HTTP_POST_VARS['unlucky'];

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
{
	f_MQuery( "DELETE FROM cards WHERE card_id=$id" );
	f_MQuery( "DELETE FROM player_cards WHERE card_id=$id" );
	f_MQuery( "DELETE FROM player_selected_cards WHERE card_id=$id" );
	f_MQuery( "DELETE FROM mob_cards WHERE card_id=$id" );
	if( !file_exists( "../spell_effects/spell$id.php" ) )
		unlink( "../spell_effects/spell$id.php" );
}
else
{
	f_MQuery( "UPDATE cards SET name='$name', descr='$descr', descr2='$descr2', req='$req' WHERE card_id=$id" );
	f_MQuery( "UPDATE cards SET name_m='$name_m', name2='$name2', name2_m='$name2_m', name3='$name3', name3_m='$name3_m', name4='$name4', name4_m='$name4_m', name5='$name5', name5_m='$name5_m', name6='$name6', name6_m='$name6_m' WHERE card_id=$id" );
	f_MQuery( "UPDATE cards SET image_small='$image_small', image_large='$image_large' WHERE card_id=$id" );
	f_MQuery( "UPDATE cards SET genre='$genre', cast_description='$cast_description' WHERE card_id=$id" );
	f_MQuery( "UPDATE cards SET multy='$multy', mk='$mk', unlucky='$unlucky', status='$status', cost='$cost', level='$level' WHERE card_id=$id" );
	if( file_put_contents( "../spell_effects/spell$id.php", $effect ) === false )
	{
		print( "moo!<br>" );
		die( );
	}
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='editor.php?id=$id';\n" ); ?>
</script>
