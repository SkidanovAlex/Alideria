<?

include_once( "help/guilds_common.php" );

		echo "<table><tr><td>";ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
		echo "<center><table id=s_table><tr><td>";

?>

<div id="header" align="left">������� �������</div><br />
�������� �������: <a href=help.php?id=34268>��������</a><br>
<? ShowGuildRating( 101 ); ?><br>
<br>

���� ������� �� ��� ������� ������. ������� ����� ������ �������, ��� �������� �����, � ��, ���� � ����������� ��������, ������ ����� � ������ ������ ������ ������� ������ �������. � ��� ������� ���������� � ��������� ������� �� ������ ������� ����, �� � ���� � ����� �������� ���, ��� ���-�� ���������� �� ���� ����, � ��������� ������ ����, ��������� ������� ����� �����; ������ �������, ����� �������� ���� ����� � ��������� ������, � �������� ����, ���������� � ����� ������ ������, � ��� ����� ������ ������ ���� � ����� ���������, �������� ��������, �������� � ����������� - ������ ������ ��� ������ ��������������� ��������.<br>
� ���� �� ���� � ����� ������� - ������ ����� ���������� ���������. ����������� ���� ������ � ���� � ���, ��� ����� ������ ������; � ���� �� �� ��������� ����� ��� ����� ������ ����, ����������� ��� �������� ������ �����.<br>
<br>
������� �������� � ������� �������, ����� ������������ � �������� �� ������ ���� � ����� ����.<br>
<? ShowProfExpText( "������� ����" ); ?>
<br>
������� ��������� ����� ���� ��� ����� �����������:<br>
1. ����� ����. � <a href=help.php?id=34268>��������</a> �� ������ ������ ����.
<? ShowKopkaText( ); ?><br>
2. ��������� �����. ��������� ����� 2 �� ������ ������� ����. ����
�������� �� 24 ����, � ������� ������� �� ������ ���������� ������ ������.
����� 24 ���� ��� ������� ��������� ���� �� ������� �����. ��� ����� �� ���������
����, ��� ������ ���� ����� ��� ������� ����.<br>
3. ����� ����. ��������� ����� 4 � �������� �������� �� ������ ������ ���� �� ����.<br>
<br>

�� ����� ������� � � ������� ����� �� ������ ������� ���������:<br>

<?

ShowGuildItems( 101 );

		echo "</td></tr></table></center>";
		echo "</div>";
		ScrollLightTableEnd();echo "</td></tr></table>";

?>
