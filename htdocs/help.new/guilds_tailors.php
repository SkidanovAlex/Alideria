<?

include_once( "help/guilds_common.php" );

		echo "<table><tr><td>";ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
		echo "<center><table id=s_table><tr><td>";

?>

<div id="header" align="left">Гильдия Портных</div><br />


Основная локация: <a href=help.php?id=34270>Мастерская</a><br>
<? ShowGuildRating( 109 ); ?><br>
<br>
Члены Гильдии Портных могут шить новые и чинить поврежденные перчатки, обувь и накидки.<br>
<? ShowProfExpText( "сшитые и починенные вещи" ); ?>
<br>
Гильдия открывает перед вами две основные возможности:<br>
1. Вы можете шить вещи. <? ShowCraftText( "портных", 50007 ); ?><br>
2. Вы можете чинить вещи. <? ShowRepairText( ); ?><br>
<br>

<? RecipesLink( 109 ); ?>

<?

		echo "</td></tr></table></center>";
		echo "</div>";
		ScrollLightTableEnd();echo "</td></tr></table>";

?>
