<?

include_once( "help/guilds_common.php" );

?>

<script>begin_help( '������� ���������' );</script>


�������� �������: <a href=help.php?id=34270>����������</a><br>
<? ShowGuildRating( 106 ); ?><br>
<br>
����� ������� ��������� ����� ������ ����� � ������.<br>
����� ���� ��������� ����� � ��������������� ���������<br>
<? ShowProfExpText( "��������� ����� � ������" ); ?>
<br>
������� ��������� ����� ���� ���� �������� �����������:<br>
1. �� ������ ������ ����� � ������. <? ShowCraftText( "���������", 50013 ); ?><br>
<br>

<? RecipesLink( 106 ); ?>

<script>end_help( );</script>
