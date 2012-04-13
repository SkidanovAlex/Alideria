<?

if( !$mid_php )
	die( );

echo "<script src='js/numkeyboard2.js'></script>";
if( $noob ) echo "<script>var ready_to_go_further = false;</script>";

$till = $player->till;
$tm = time( );

if( $player->regime == 3 )
{
	include( "stone_table.php" );
	return;
}

$cant_move_wo_ = $player->depth >= 2 && !$player->HasWearedItem( 8 );

require_once( "danger_walk_functions.php" );
include_js( 'js/timer2.js' );
include_js( 'js/skin2.js' );

print( "<table width=100%><colgroup><col width=120><col width=*><col width=200><tbody><tr><td valign=top>");

ScrollLightTableStart();
echo "<img id=loc_img src=".getCaveImage( $depth )." width=170 height=127>";
ScrollLightTableEnd();

print( "</td><td valign=top>" );

print( "<center><b>{$loc_names[$loc]}, глубина: <span id=depth>$depth</span></b><hr width=40% color=gray size=1></center>" );
$res = f_MQuery( "SELECT text FROM loc_texts WHERE loc=$loc AND depth=$depth" );
$arr = f_MFetch( $res );
print( "<div align=justify id=loc_desc>$arr[0]</div>" );
print( "</td><td valign=top>" );

ScrollLightTableStart();

echo "<table width=190><tr><td>";

echo "<div id=d_acts>";
echo getPossibleDirections( );
echo "</div>";

echo "</td></tr></table>";

ScrollLightTableEnd();

echo "</td></tr></table>";

echo "<div id=cave_msg style='position:absolute;display:none;left:0px;top:0px;'>&nbsp;</div>";

?>
<script>
show_timer_title_2 = true;
function reload () {

	var rndval = new Date().getTime(); 

	document.getElementById('num_img').innerHTML = '<img width=90 height=40 src=captcha/code.php?rnd=' + rndval + ' border=1 bordercolor=black>';

};
function cave( a ) {
	query( "danger_walk_ref.php?" + a, '' );
}
function hideLastMsg( ) {
	_( 'cave_msg' ).style.display = 'none';
}
function showCaveMsg( a ) {
	_( 'cave_msg' ).style.left = -1000;
	_( 'cave_msg' ).style.display = '';
	_( 'cave_msg' ).innerHTML = "<table><tr><td>" + rFLUc() + a + rFLL() + "</td></tr></table>";
	_( 'cave_msg' ).style.top = ( ( screen_height( ) - _( 'cave_msg' ).offsetHeight ) / 2 ) + 'px';
	_( 'cave_msg' ).style.left = ( ( screen_width( ) - _( 'cave_msg' ).offsetWidth ) / 2 ) + 'px';
}
addHandler( document, 'click', hideLastMsg );
</script>
<?

echo "<div id=d_content>";
echo getMidContent( );
echo "</div>";

if( $player->regime != 0 )
{
	// тогда в exec лежит таймер
	echo "<script>$exec</script>";
}

?>
