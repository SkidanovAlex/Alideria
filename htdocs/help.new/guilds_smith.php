<?

include_once( "help/guilds_common.php" );

		echo "<table><tr><td>";ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
		echo "<center><table id=s_table><tr><td>";

?>

<div id="header" align="left">Гильдия Кузнецов</div><br />

Основная локация: <a href=help.php?id=34270>Мастерская</a><br>
<? ShowGuildRating( 104 ); ?><br>
<br>
Члены Гильдии Кузнецов могут ковать новые и ремонтировать поврежденные оружие, щиты, броню и шлемы.<br>
<? ShowProfExpText( "скованные и отремонтированные вещи" ); ?>
<br>
Гильдия открывает перед вами две основные возможности:<br>
1. Вы можете создавать оружие с уникальными характеристиками на <a href=help.php?id=50100>Алтаре Кузнецов</a>.<br>
2. Вы можете ковать вещи. <? ShowCraftText( "кузнецов", 50002 ); ?><br>
3. Вы можете чинить вещи. <? ShowRepairText( ); ?><br>
<br>

<? RecipesLink( 104 ); ?>

<?
		echo "</td></tr></table></center>";
		echo "</div>";
		ScrollLightTableEnd();echo "</td></tr></table>";

?>
