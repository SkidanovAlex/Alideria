<?

include_once( "help/guilds_common.php" );

		echo "<table><tr><td>";ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
		echo "<center><table id=s_table><tr><td>";

?>

<div id="header" align="left">������� ����������</div><br />


�������� �������: <a href=help.php?id=34259>������</a><br>
<? ShowGuildRating( 103 ); ?><br>
<br>
��� ����, ����� ����� ����� �� c���������, ��� ����� ����� ���� ��������� ������� ������� ������� � ��������� 200 �������� �� ����������.<br>
<br>
������� �������� � �������, ����� ������������ � ������ ���� ����������, ������������� �� ������� ������� �����, � ������ ����.<br>
<? ShowProfExpText( "��������� ����" ); ?>
<br>
������� ��������� ����� ���� ���� �������� �����������:<br>
1. � ������� ���� ���������� � <a href=help.php?id=34259>�������</a> �� ������ ������ ����.
<? ShowKopkaText( ); ?><br>
<br>

�� ����� ������ �� ������ ����� ���������:<br>

<?

ShowGuildItems( 103 );

		echo "</td></tr></table></center>";
		echo "</div>";
		ScrollLightTableEnd();echo "</td></tr></table>";

?>

