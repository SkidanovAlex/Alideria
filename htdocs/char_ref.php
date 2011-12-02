<?

include_once( "functions.php" );
include_once( "player.php" );
include_once( "items.php" );
include_once( "card.php" );
include_once( "pet.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$stats = $player->getAllAttrNames( );

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">

<div id=moo name=moo>
	<img width=220 height=300 src=empty.gif>

	<img style='position: absolute;' width=100 height=225 id=avatar name=avatar>
	<img style='position: absolute;' width=64 height=90 id=pet_img name=pet_img src='empty.gif'>

	<div style='position: absolute;' width=50 height=50 id=item1 name=item1>&nbsp;</div>
	<div style='position: absolute;' width=50 height=50 id=item2 name=item2>&nbsp;</div>
	<div style='position: absolute;' width=50 height=50 id=item3 name=item3>&nbsp;</div>
	<div style='position: absolute;' width=50 height=50 id=item4 name=item4>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=item5 name=item5>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=item6 name=item6>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=item7 name=item7>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=item8 name=item8>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=item9 name=item9>&nbsp;</div>
	<div style='position: absolute;' width=50 height=50 id=item10 name=item10>&nbsp;</div>
	<div style='position: absolute;' width=50 height=50 id=item11 name=item11>&nbsp;</div>
	<div style='position: absolute;' width=50 height=50 id=item12 name=item12>&nbsp;</div>


<?
if (!($player->player_id == 6825 || $player->player_id == 868239 || $player->player_id == 67573 ))
{
	echo "<div style='position: absolute;' width=50 height=50 id=item13 name=item13>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item14 name=item14>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item15 name=item15>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item16 name=item16>&nbsp;</div>";
	
	echo "<div style='position: absolute;display: none;' width=50 height=50 id=item17 name=item17>&nbsp;</div>";
	echo "<div style='position: absolute;display: none;' width=50 height=50 id=item18 name=item18>&nbsp;</div>";
	echo "<div style='position: absolute;display: none;' width=50 height=50 id=item19 name=item19>&nbsp;</div>";
	echo "<div style='position: absolute;display: none;' width=50 height=50 id=item20 name=item20>&nbsp;</div>";

	echo "<div style='position: absolute;display: none;' width=50 height=50 id=item21 name=item21>&nbsp;</div>";
	echo "<div style='position: absolute;display: none;' width=50 height=50 id=item22 name=item22>&nbsp;</div>";
	echo "<div style='position: absolute;display: none;' width=50 height=50 id=item23 name=item23>&nbsp;</div>";
	echo "<div style='position: absolute;display: none;' width=50 height=50 id=item24 name=item24>&nbsp;</div>";
}
else
{
	echo "<div onmousemove=show_pots(1) style='position: absolute;top:285px;left:30px;' width=25 height=25 id=pot1 name=pot1>";
		echo "<img src='images/items/bg/bg25pot.gif'>";
		echo "<div onmouseout=hide_pots(1) id=pots1 name=pots1 style='position: absolute;top:-10px;left:-10px;display: none;z-index: 102;'>";
			echo "<img src='images/rect/panel.jpg'>";
			echo "<div style='position: absolute;' width=50 height=50 id=item13 name=item13>&nbsp;</div>";
			echo "<div style='position: absolute;' width=50 height=50 id=item14 name=item14>&nbsp;</div>";
			echo "<div style='position: absolute;' width=50 height=50 id=item15 name=item15>&nbsp;</div>";
			echo "<div style='position: absolute;' width=50 height=50 id=item16 name=item16>&nbsp;</div>";
		echo "</div>";
	echo "</div>";

	echo "<div onmousemove=show_pots(2) style='position: absolute;top:285px;left:55px;' width=25 height=25 id=pot2 name=pot2>";
		echo "<img src='images/items/pot_sq/ten_talismana.png'>";
		echo "<div onmouseout=hide_pots(2) id=pots2 name=pots2 style='position: absolute;top:-10px;left:-10px;display: none;z-index: 102;'>";
			echo "<img src='images/rect/panel.jpg'>";
			echo "<div style='position: absolute;' width=50 height=50 id=item17 name=item17>&nbsp;</div>";
			echo "<div style='position: absolute;' width=50 height=50 id=item18 name=item18>&nbsp;</div>";
			echo "<div style='position: absolute;' width=50 height=50 id=item19 name=item19>&nbsp;</div>";
			echo "<div style='position: absolute;' width=50 height=50 id=item20 name=item20>&nbsp;</div>";
		echo "</div>";
	echo "</div>";

	echo "<div onmousemove=show_pots(3) style='position: absolute;top:285px;left:80px;' width=25 height=25 id=pot3 name=pot3>";
		echo "<img src='images/items/pot_sq/ten_medaliona.png'>";
		echo "<div onmouseout=hide_pots(3) id=pots3 name=pots3 style='position: absolute;top:-10px;left:-10px;display: none;z-index: 102;'>";
			echo "<img src='images/rect/panel.jpg'>";
			echo "<div style='position: absolute;' width=50 height=50 id=item21 name=item21>&nbsp;</div>";
			echo "<div style='position: absolute;' width=50 height=50 id=item22 name=item22>&nbsp;</div>";
			echo "<div style='position: absolute;' width=50 height=50 id=item23 name=item23>&nbsp;</div>";
			echo "<div style='position: absolute;' width=50 height=50 id=item24 name=item24>&nbsp;</div>";
		echo "</div>";
	echo "</div>";

}
?>

	<div style='position: absolute;' width=25 height=25 id=csp0 name=csp0>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csp1 name=csp1>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csp2 name=csp2>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csp3 name=csp3>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csp4 name=csp4>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csp5 name=csp5>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csp6 name=csp6>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csp7 name=csp7>&nbsp;</div>

	<div style='position: absolute;' width=25 height=25 id=csps0 name=csps0>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csps1 name=csps1>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csps2 name=csps2>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csps3 name=csps3>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csps4 name=csps4>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csps5 name=csps5>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csps6 name=csps6>&nbsp;</div>
	<div style='position: absolute;' width=25 height=25 id=csps7 name=csps7>&nbsp;</div>

	<div style='position: absolute;' width=50 height=50 id=item_drag name=item_drag>&nbsp;</div>
</div>

<script src=functions.js></script>
<script src=js/char_inv.php></script>
<script src=js/skin2.js></script>

<script>

<?

$res = f_MQuery( "SELECT items.*, player_items.weared FROM items, player_items WHERE player_id = {$player->player_id} AND items.item_id = player_items.item_id AND player_items.weared > 0" );
while( $arr = f_MFetch( $res ) )
{
	$descr = itemFullDescr2( $arr, true );
	if ($player->player_id==6825 && ($arr[weared]>13 || $arr[weared] == 1))
		$im = $arr[image_large];
	else
		$im = $arr[image];
	print( "\twear( $arr[item_id], '$arr[name]', '$descr', '$im', $arr[weared] );\n" );
}

echo "set_avatar( '".str_replace( ".jpg", ".png", $player->getAvatar( ) )."' );";

		$pet_arr = f_MFetch( f_MQuery( "SELECT pets.*, player_pets.level, player_pets.name as nick FROM pets INNER JOIN player_pets ON pets.pet_id=player_pets.pet_id WHERE player_pets.player_id={$player->player_id} AND chosen=1" ) );
		if( $pet_arr )
		{
			$descr = PetGetDescr( $pet_arr );
			echo "set_pet( '{$pet_arr[image]}', '{$descr}' );";
		}

$res = f_MQuery( "SELECT cards.* FROM cards, player_selected_cards WHERE player_id = {$player->player_id} AND cards.card_id = player_selected_cards.card_id AND staff=0 ORDER BY player_selected_cards.entry_id" );
while( $arr = f_MFetch( $res ) )
{
	$descr = cardGetSmallIcon( $arr );
	print( "\tadd_spell( $descr );\n" );
}

$res = f_MQuery( "SELECT cards.* FROM cards, player_selected_cards WHERE player_id = {$player->player_id} AND cards.card_id = player_selected_cards.card_id AND staff=1 ORDER BY player_selected_cards.entry_id" );
while( $arr = f_MFetch( $res ) )
{
	$descr = cardGetSmallIcon( $arr );
	print( "\tadd_spell_s( $descr );\n" );
}

echo "window.top.my_login = '{$player->login}';\n";

?>

parent.game.location.href = 'game.php';

</script>