<?php
if( !$mid_php ) die( );
include_once( "skin.php" );


echo "<center><table><tr><td>";
ScrollLightTableStart("center");
?>
<!-- label -->
<table border="0" cellspacing="0" cellpadding="0" align="center" id="LocationTable">
<tr><td id="LocationCell">

<div id=capital_content style='position:relative;left:0px;top:0px;'>
<script language="javascript" src="js/capital_winter.js" type="text/javascript"></script>
<?
if( $noob )
{
?>
  <script>for( var a in selectArray ) selectArray[a][6] = 'javascript:alert( "<?=addslashes($be_noob)?>" );';</script>
<?
}
?>
<script language="javascript" src="js/location_selection.js" type="text/javascript"></script>
<script>SetAnim( );</script>
<img style='position:absolute; z-index:100;' id="TransparentImage" src="images/empty.gif" width=700 height=320 usemap="#Map" border=0>
<img id="LocationImage" src="images/locations/w/capital.jpg" width=700 height=320 border=0></div></td></tr>
<map name="Map">
	<area shape="poly"coords="85,227,94,227,94,233,112,232,127,263,119,265,120,289,129,303,115,302,92,270" onmousemove="SelShow(event,'Sel1');" onmouseout="SelHide('Sel1');" onclick="LocClick('Sel1');" style='cursor:pointer'>
	<area shape="poly"coords="33,176,51,175,53,170,56,169,57,174,75,172,83,197,80,216,68,219,57,241,26,235,6,225,19,185,29,185" onmousemove="SelShow(event,'Sel2');" onmouseout="SelHide('Sel2');" onclick="LocClick('Sel2');" style='cursor:pointer'>
	<area shape="poly"coords="427,134,417,144,411,138,404,130,392,125,387,115,376,113,375,106,369,99,359,99,345,99,339,96,325,91,330,90,330,85,341,85,341,92,350,92,350,85,361,85,361,92,365,92,365,87,378,87,378,91,385,91,386,89,399,89,402,92,413,99,427,100" onmousemove="SelShow(event,'Sel3');" onmouseout="SelHide('Sel3');" onclick="LocClick('Sel3');" style='cursor:pointer'>
	<area shape="poly"coords="320,117,329,117,333,126,346,132,348,138,353,135,358,144,365,161,373,175,378,165,389,165,394,161,399,167,405,179,409,190,405,194,405,204,388,210,375,204,369,198,363,204,361,212,341,199,329,184,322,169,316,161" onmousemove="SelShow(event,'Sel4');" onmouseout="SelHide('Sel4');" onclick="LocClick('Sel4');" style='cursor:pointer'>
	<area shape="poly"coords="275,205,283,185,287,176,287,167,294,167,296,172,316,173,331,203,323,205,323,217,307,218,301,225,299,219,294,222,294,229,283,226,284,207" onmousemove="SelShow(event,'Sel5');" onmouseout="SelHide('Sel5');" onclick="LocClick('Sel5');" style='cursor:pointer'>
	<area shape="poly"coords="459,179,471,179,480,175,489,181,496,179,502,181,516,192,522,198,531,201,539,211,542,219,527,219,513,224,508,219,497,215,488,216,483,210,470,212,459,212,456,203,454,186" onmousemove="SelShow(event,'Sel6');" onmouseout="SelHide('Sel6');" onclick="LocClick('Sel6');" style='cursor:pointer'>
	<area shape="poly"coords="182,134,204,139,204,132,199,132,209,98,218,132,214,132,215,159,217,165,209,179,204,185,203,202,195,201,186,196,178,207,178,221,175,234,168,243,158,238,154,232,160,223,156,217,156,208,161,199,169,191,169,184,164,179,164,173,167,170,164,165,175,147" onmousemove="SelShow(event,'Sel7');" onmouseout="SelHide('Sel7');" onclick="LocClick('Sel7');" style='cursor:pointer'>
	<area shape="poly"coords="141,162,151,168,155,181,155,191,140,196,129,197,123,181,124,172,130,166" onmousemove="SelShow(event,'Sel8');" onmouseout="SelHide('Sel8');" onclick="LocClick('Sel8');" style='cursor:pointer'>
	<area shape="poly"coords="604,211,607,193,606,176,599,167,582,163,567,162,551,171,550,190,561,195,567,204,568,215,578,218,592,218" onmousemove="SelShow(event,'Sel9');" onmouseout="SelHide('Sel9');" onclick="LocClick('Sel9');" style='cursor:pointer'>
	<area shape="poly"coords="493,82,502,110,498,110,499,151,508,153,513,153,521,158,523,165,511,174,506,178,501,169,493,160,493,147,485,139,476,129,478,123,487,128,488,113,482,112" onmousemove="SelShow(event,'Sel10');" onmouseout="SelHide('Sel10');" onclick="LocClick('Sel10');" style='cursor:pointer'>
	<area shape="poly"coords="239,126,247,146,250,165,257,177,258,209,237,212,233,227,214,219,214,207,208,207,224,163,232,163,230,146" onmousemove="SelShow(event,'Sel11');" onmouseout="SelHide('Sel11');" onclick="LocClick('Sel11');" style='cursor:pointer'>
	<area shape="poly"coords="635,0,626,38,648,34,643,87,654,107,699,102,699,0" onmousemove="SelShow(event,'Sel12');" onmouseout="SelHide('Sel12');" onclick="LocClick('Sel12');" style='cursor:pointer'>
	<area shape="poly"coords="518,108,527,97,527,115,542,137,571,134,597,194,586,137,610,149,614,135,583,116,580,96,543,87,550,71,542,59,518,86" onmousemove="SelShow(event,'Sel13');" onmouseout="SelHide('Sel13');" onclick="LocClick('Sel13');" style='cursor:pointer'>
	<area shape="poly"coords="251,233,284,233,284,273,251,273" onmousemove="SelShow(event,'Sel14');" onmouseout="SelHide('Sel14');" onclick="LocClick('Sel14');" style='cursor:pointer'>
	<?
	if( $player->level >= 3 )
	{
	?>
	<area shape="poly"coords="548,189,568,208,584,283,550,302,506,284,528,208" onmousemove="SelShow(event,'Sel18');" onmouseout="SelHide('Sel18');" onclick="LocClick('Sel18');" style='cursor:pointer'>
	<?
	}
	?>
<?

include_once( "card.php" );


$Player = new Player( $_COOKIE['c_id'] );

// Водопад
if( $Player->HasTrigger( 46 ) )
{// Для нубиков, проходящих квест Дракона
?>
	<area shape="poly"coords="99,131,105,137,105,152,122,166,121,198,130,215,85,200,77,181,92,151,92,136" onmousemove="SelShow(event,'Sel15');" onmouseout="SelHide('Sel15');" onclick="LocClick('Sel15');" style='cursor:pointer'>
<?
}
elseif( $Player->HasTrigger( 51 ) or $Player->HasTrigger( 50 ) or $Player->HasTrigger( 49 ) )
{// Для настоящих мужиков, женящихся на прекрасных Дамах и уже прошедших квест Дракона
?>
	<area shape="poly"coords="99,131,105,137,105,152,122,166,121,198,130,215,85,200,77,181,92,151,92,136" onmousemove="SelShow(event,'Sel1001');" onmouseout="SelHide('Sel1001');" onclick="LocClick('Sel1001');" style='cursor:pointer'>
<?
}
// Конец Водопада

if( true )
{
?>
	<area shape="poly"coords="642,268,648,232,661,229,667,236,667,251,643,277" onmousemove="SelShow(event,'Sel16');" onmouseout="SelHide('Sel16');" onclick="LocClick('Sel16');" style='cursor:pointer'>
<?
}
?>

	<area shape="poly"coords="157,258,177,254,227,267,192,289,157,262" onmousemove="SelShow(event,'Sel17');" onmouseout="SelHide('Sel17');" onclick="LocClick('Sel17');" style='cursor:pointer'>

</map>

<script>
window.onload = UpdatePositions;
window.onresize = UpdatePositions;

</script>
</table>

<?php
ScrollLightTableEnd();
echo "</td></tr></table>";

if( $noob )echo "<a href=# onclick=\"alert( '$be_noob' );\">Перейти в текстовый режим</a>";
else echo "<a href=game.php?text_mode>Перейти в текстовый режим</a>";

?>

</center>
