<?

include_once( "skin.php" );



if( !$mid_php ) die( );

echo "<center><table><tr><td>";
ScrollLightTableStart("center");
?>

<table border="0" cellspacing="0" cellpadding="0" align="center" id="LocationTable">
<tr><td id="LocationCell">

<div id=capital_content style='position:relative;left:0px;top:0px;'>
<script language="javascript" src="js/almost_forest.js" type="text/javascript"></script>
<script language="javascript" src="js/location_selection.js" type="text/javascript"></script>
<script>SetAnim( );</script>
<img style='position:absolute; z-index:100;' id="TransparentImage" src="images/empty.gif" width=720 height=200 usemap="#Map" border=0>
<img id="LocationImage" src="images/locations/almost_forest.jpg" width=720 height=200 border=0>
<img id="lich_img" src="images/locations/almost_forest_npc.png" width=210 height=200 border=0 style='position:absolute;left:7px;top:0px;'>
</div></td></tr>
<map name="Map">
<?
if( !$player->HasTrigger( 253 ) && $player->level >= 3 )
{
?>
	<area shape="poly"coords="41,152,68,200,156,192,182,64,102,42,74,77,78,152" onmousemove="SelShow(event,'Sel0');" onmouseout="SelHide('Sel0');" onclick="LocClick('Sel0');" style='cursor:pointer'>
<?
}
else
{
?>
	<area shape="poly"coords="27,97,16,125,27,159,50,158,50,127" onmousemove="SelShow(event,'Sel5');" onmouseout="SelHide('Sel5');" onclick="LocClick('Sel5');" style='cursor:pointer'>
<?
}
?>
	<area shape="poly"coords="249,58,185,63,171,137,187,151,300,152,303,119" onmousemove="SelShow(event,'Sel1');" onmouseout="SelHide('Sel1');" onclick="LocClick('Sel1');" style='cursor:pointer'>
	<area shape="poly"coords="286,3,276,27,278,76,299,90,298,102,308,103,306,134,392,131,399,118,422,120,431,106,447,124,464,124,457,84,439,24,424,23,424,61,396,85,393,17,320,18,320,12,300,12,300,44,292,44,292,28" onmousemove="SelShow(event,'Sel2');" onmouseout="SelHide('Sel2');" onclick="LocClick('Sel2');" style='cursor:pointer'>
	<area shape="poly"coords="547,69,525,129,542,168,571,180,632,184,632,155,643,147,657,171,670,173,688,137,707,130,681,77,587,71,581,54,570,51,567,67" onmousemove="SelShow(event,'Sel3');" onmouseout="SelHide('Sel3');" onclick="LocClick('Sel3');" style='cursor:pointer'>
	<area shape="poly"coords="400,135,344,155,344,177,421,158,509,178,509,157,444,133" onmousemove="SelShow(event,'Sel4');" onmouseout="SelHide('Sel4');" onclick="LocClick('Sel4');" style='cursor:pointer'>
</map>

<script>
window.onload = UpdatePositions;
window.onresize = UpdatePositions;

</script>
</table>

<?php
ScrollLightTableEnd();
echo "</td></tr></table>";

?>

</center>
