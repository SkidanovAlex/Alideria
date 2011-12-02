<?

include_once( "help/guilds_common.php" );

		echo "<table><tr><td>";ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
		echo "<center><table id=s_table><tr><td>";

?>

<div id="header" align="left">Гильдия Ювелиров</div><br />


Основная локация: <a href=help.php?id=34270>Мастерская</a><br>
<? ShowGuildRating( 105 ); ?><br>
<br>
Члены Гильдии Ювелиров могут делать новые и ремонтировать поврежденные кольца, браслеты и амулеты.<br>
<? ShowProfExpText( "созданные и отремонтированные вещи" ); ?>
<br>
Гильдия открывает перед вами две основные возможности:<br>
1. Вы можете создавать вещи. <? ShowCraftText( "ювелиров", 50003 ); ?><br>
2. Вы можете чинить вещи. <? ShowRepairText( ); ?><br>
<br>

<? RecipesLink( 105 ); ?>

<?

		echo "</td></tr></table></center>";
		echo "</div>";
		ScrollLightTableEnd();echo "</td></tr></table>";

?>