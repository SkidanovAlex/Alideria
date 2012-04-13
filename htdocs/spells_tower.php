<?

if( !$mid_php ) die( );

include_once( "card.php" );
include_once( "localization/spells_tower.php" );

?>

<script>

function buy( id )
{
	if( confirm( 'Вы уверены, что хотите изучить выбранное заклинание?' ) )
	{
		query( "spells_tower_buy.php", '' + id );
	}
}

</script>

<?

// the copy of this array is located in spells_tower_ref
$spell_ids = Array (

/* neutral */ 103, 109, 151,
               /* w */ /* n */ /* f */
    /* 2 */      152,    157,    162,
    /* 3 */      170,    236,    180,
    /* 4 */      193,    188,    202,
    /* 5 */      207,    212,    217

);

$stats = Array( 30, 40, 50 );

function outSpell( $id )
{
	global $spell_ids;
	global $str_learn;
	global $str_st_cant_learn;
	global $str_st_already_learned;
	global $player;
	$spell_id = $spell_ids[$id];

	$clrs = Array( "blue", "green", "red", "grey" );

	$card = new Card( $spell_id );
	$price = 50 * pow( (int)( ( 1 + $card->level ) / 2 ), 2 ) * ( 1.5 - 0.5 * ( $card->level % 2 ) );
	if( $spell_id == 151 ) $price = 5000;

	$style = "-moz-opacity: 0.4;-khtml-opacity: 0.4;opacity: 0.4;";
	$moo = "<table width=100% cellspacing=0 cellpadding=0><tr><td align=left><b><img width=11 height=11 src=images/money.gif>&nbsp;".$price."</b></td><td align=right><div id=towerbtn$id><a href='javascript:buy($id)'>".$str_learn."</a></div></td></tr></table>";
	$res = f_MQuery( "SELECT count( card_id ) FROM player_cards WHERE player_id={$player->player_id} AND ( card_id=$spell_id OR card_id IN ( SELECT card_id FROM cards WHERE parent = $spell_id ) )" );
	$arr = f_MFetch( $res );
	if( $arr[0] > 0 )
		$moo = "<small><font color=green>".$str_st_already_learned."</font></small>";
	else if( !$card->playerCanLearn( $player->player_id ) )
		$moo = "<small><font color=red>".$str_st_cant_learn."</font></small>";
	else $style = '';

	return "<script>FUcm();</script><img onmousemove=\"showTooltipW(event,'<font color=".$clrs[$card->genre]."><b>".$card->name."</b></font><br>".addslashes(str_replace("\"","'",$card->descr))."', 250)\" onmouseout=\"hideTooltip()\" style=\"$style\" width=141 height=141 src=images/spells/".$card->img_large."><br>$moo<script>FL();</script>";
}

echo "<div id=everything style='position:relative;top:5px;left:0px;'>";

echo "<div style='position:absolute; left:120px;top:0px;width:160px;height:160px;'>";
echo outSpell( 0 );
echo "</div>";

echo "<div style='position:absolute; left:304px;top:0px;width:160px;height:160px;'>";
echo outSpell( 1 );
echo "</div>";

echo "<div style='position:absolute; left:488px;top:0px;width:160px;height:160px;'>";
echo outSpell( 2 );
echo "</div>";

$id = 3;

for( $lvl = 2; $lvl <= 5; ++ $lvl )
{
	$i = $lvl - 2;
	$text = $lvl." $str_level";
	echo "<div style='width:100px;position:absolute; left:0px;top:".(240 + 75 + $i * 184)."px;'>";
    echo "<center><b>".$text."</b></center>";
    echo "</div>";

	for( $i = 0; $i < 3; ++ $i )
	{
		echo "<div style='position:absolute; left:".(120 + ( $i ) * 184)."px;top:".(240 + ($lvl-2) * 184)."px;width:160px;height:160px;'>";
        echo outSpell( $id ++ );
        echo "</div>";
	}
}

for( $i = 0; $i < 3; ++ $i )
{
	echo "<div style='position:absolute; left:".(120 + ( $i ) * 184)."px;top:190px;width:160px;height:40px;'><center>";
	$res = f_MQuery( "SELECT * FROM attributes WHERE attribute_id=".$stats[$i] );
	$arr = f_MFetch( $res );
	echo "<img width=20 height=20 src=images/icons/attributes/".$arr['icon']."><br><b><font color=".$arr['color'].">".$arr['name']."</font></b>";
	echo "</center></div>";
}

echo "</div>";

$no_rest =true;

?>
