<?

include_once( "help/guilds_common.php" );

		echo "<table><tr><td>";ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
		echo "<center><table id=s_table><tr><td>";

?>

<div id="header" align="left">������� ��������</div><br />

�������� �������: <a href=help.php?id=34270>����������</a><br>
<? ShowGuildRating( 104 ); ?><br>
<br>
����� ������� �������� ����� ������ ����� � ������������� ������������ ������, ����, ����� � �����.<br>
<? ShowProfExpText( "��������� � ����������������� ����" ); ?>
<br>
������� ��������� ����� ���� ��� �������� �����������:<br>
1. �� ������ ��������� ������ � ����������� ���������������� �� <a href=help.php?id=50100>������ ��������</a>.<br>
2. �� ������ ������ ����. <? ShowCraftText( "��������", 50002 ); ?><br>
3. �� ������ ������ ����. <? ShowRepairText( ); ?><br>
<br>

<? RecipesLink( 104 ); ?>

<?
		echo "</td></tr></table></center>";
		echo "</div>";
		ScrollLightTableEnd();echo "</td></tr></table>";

?>
