<?

include_once( "help/guilds_common.php" );

?>

<script>begin_help( '������� �������' );</script>


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

<script>end_help( );</script>
