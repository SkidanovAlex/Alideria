<?

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
$name13 = $HTTP_POST_VARS['nm13'];
$word_form = $HTTP_POST_VARS['word_form'];
$kind = $HTTP_POST_VARS['kind'];
$kind_text = $HTTP_POST_VARS['kind_text'];


$effect = $HTTP_POST_VARS['effect'];
$req = $HTTP_POST_VARS['req'];
$level = $HTTP_POST_VARS['level'];
$price = $HTTP_POST_VARS['price'];
$charges = $HTTP_POST_VARS['charges'];
$type = $HTTP_POST_VARS['type'];
$type2 = $HTTP_POST_VARS['type2'];
$weight = $HTTP_POST_VARS['weight'];
$image = $HTTP_POST_VARS['image'];
$image_large = $HTTP_POST_VARS['image_large'];
$descr = $HTTP_POST_VARS['descr'];

$learn_spell_id = $HTTP_POST_VARS['learn_spell_id'];
$inner_spell_id = $HTTP_POST_VARS['inner_spell_id'];
$learn_recipe_id = $HTTP_POST_VARS['learn_recipe_id'];

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
{
	f_MQuery( "DELETE FROM items WHERE item_id=$id" );
	f_MQuery( "DELETE FROM player_items WHERE item_id=$id" );
	f_MQuery( "DELETE FROM location_items WHERE item_id=$id" );
	f_MQuery( "DELETE FROM phrase_items WHERE item_id=$id" );
	f_MQuery( "DELETE FROM cave_items WHERE item_id=$id" );
	f_MQuery( "DELETE FROM lake_items WHERE item_id=$id" );
	f_MQuery( "DELETE FROM forest_items WHERE item_id=$id" );
}
else
{
	f_MQuery( "UPDATE items SET name='$name', effect='$effect', req='$req', image='$image', image_large='$image_large', descr='$descr', level=$level WHERE item_id=$id" );
	f_MQuery( "UPDATE items SET name_m='$name_m', name2='$name2', name2_m='$name2_m', name3='$name3', name3_m='$name3_m', name4='$name4', name4_m='$name4_m', name5='$name5', name5_m='$name5_m', name6='$name6', name6_m='$name6_m', name13='$name13' WHERE item_id=$id" );
	f_MQuery( "UPDATE items SET kind_text='$kind_text', kind=$kind, word_form=$word_form, price='$price', type='$type', type2='$type2', weight='$weight' WHERE item_id=$id" );
	f_MQuery( "UPDATE items SET charges=$charges, max_charges = $charges, learn_spell_id='$learn_spell_id', learn_recipe_id='$learn_recipe_id', inner_spell_id='$inner_spell_id' WHERE item_id=$id" );
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='item_editor_mid.php?id=$id';\n" ); ?>
</script>
