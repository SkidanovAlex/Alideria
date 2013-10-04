<?

include_once( 'locations/portal/func.php' );
include_once( "skin.php" );



if( !$mid_php ) die( );

// @dmitry: вырезал, потому что локация больше не связана с Орденами
/*
if( isset( $_GET['enter'] ) )
{
	$player->SetLocation( 2 );
	$player->SetDepth( 50 );
	if( portal_swap_items( $player->player_id ) )
	{
		echo "<script>location.href='game.php';</script>";
		die( );
	}
	else
	{
		$player->SetLocation( 5 );
		$player->SetDepth( 0 );
		echo "<script>setTimeout( function(){alert( 'Прежде чем выйти из запорталья, снимите все вещи' );}, 100 );</script>";
	}
}*/

?>

<?
if( $player->depth == 0 )
{
	echo "<center><table><tr><td>";
	ScrollLightTableStart("center");
?>

<table border="0" cellspacing="0" cellpadding="0" align="center" id="LocationTable">
<tr><td id="LocationCell">

<div id=capital_content style='position:relative;left:0px;top:0px;'>
<script language="javascript" src="js/portal.js" type="text/javascript"></script>
<script language="javascript" src="js/location_selection.js" type="text/javascript"></script>
<script>SetAnim( );</script>
<img style='position:absolute; z-index:100;' id="TransparentImage" src="images/empty.gif" width=720 height=200 usemap="#Map" border=0>
<img id="LocationImage" src="images/locations/portal_large.jpg" width=720 height=200 border=0><img id="lich_img" src="images/locations/portal_lich.png" width=199 height=200 border=0 style='position:absolute;left:292px;top:0px;'></div></td></tr>

<map name="Map">
	<area shape="poly"coords="232,10,211,77,208,164,279,158,279,107,259,105" onmousemove="SelShow(event,'Sel1');" onmouseout="SelHide('Sel1');" onclick="LocClick('Sel1');" style='cursor:pointer'>
	<area shape="poly"coords="397,12,381,21,287,41,366,54,373,73,353,70,346,21,312,35,342,75,375,174,353,188,405,197,477,187,434,180,401,93,408,79,423,127,428,118,416,65,426,45,404,58,416,41,418,20" onmousemove="SelShow(event,'Sel2');" onmouseout="SelHide('Sel2');" onclick="LocClick('Sel2');" style='cursor:pointer'>
	<area shape="poly"coords="612,100,602,109,603,153,626,150,627,109" onmousemove="SelShow(event,'Sel3');" onmouseout="SelHide('Sel3');" onclick="LocClick('Sel3');" style='cursor:pointer'>
	<area shape="poly"coords="115,101,97,107,97,147,133,144,132,108,114,102" onmousemove="SelShow(event,'Sel4');" onmouseout="SelHide('Sel4');" onclick="LocClick('Sel4');" style='cursor:pointer'>
</map>
<script>
window.onload = UpdatePositions;
window.onresize = UpdatePositions;

</script>
</table>
<?

	ScrollLightTableEnd();
	echo "</td></tr></table>";

	$sep = " &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; ";
	//echo "<a href='game.php?dir=10&tloc=5'>В старый храм</a>$sep<a href='game.php?dir=2&tloc=5'>В мастерскую</a>$sep<a href='game.php?dir=3&tloc=5'>В трактир</a>$sep<a href='game.php?talk=119'>Говорить с Людвигом</a>$sep<a href='game.php?dir=4791&loc=1'>Покинуть Урочище</a><br>";\
	echo "<a href='game.php?dir=10&tloc=5'>В старый храм</a>$sepВ трактир</a>$sep<a href='game.php?talk=119'>Говорить с Людвигом</a>$sep<a href='game.php?dir=4791&loc=1'>Покинуть Урочище</a><br>";
	echo "</center>";
}
else if( $player->depth == 2 )
{
	include( "locations/portal/workshipLoc.php" );
}
else if( $player->depth == 10 )
{
	include( "locations/portal/templeLoc.php" );
}

?>
<?
// @dmitry: вырезал, потому что локация больше не связана с Орденами

/*
<script>
function leave_portal( )
{
	if( confirm( 'Покинуть запорталье?' ) )
		location.href='game.php?enter=1';
}
</script>*/
?>
