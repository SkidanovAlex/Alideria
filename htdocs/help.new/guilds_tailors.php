<?

include_once( "help/guilds_common.php" );

		echo "<table><tr><td>";ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
		echo "<center><table id=s_table><tr><td>";

?>

<div id="header" align="left">������� �������</div><br />


�������� �������: <a href=help.php?id=34270>����������</a><br>
<? ShowGuildRating( 109 ); ?><br>
<br>
����� ������� ������� ����� ���� ����� � ������ ������������ ��������, ����� � �������.<br>
<? ShowProfExpText( "������ � ���������� ����" ); ?>
<br>
������� ��������� ����� ���� ��� �������� �����������:<br>
1. �� ������ ���� ����. <? ShowCraftText( "�������", 50007 ); ?><br>
2. �� ������ ������ ����. <? ShowRepairText( ); ?><br>
<br>

<? RecipesLink( 109 ); ?>

<?

		echo "</td></tr></table></center>";
		echo "</div>";
		ScrollLightTableEnd();echo "</td></tr></table>";

?>
