<?

if( !$mid_php ) die( );

if( $player->HasTrigger( 74 ) )
		die( "<script>location.href='game.php?phrase=585';</script>" );


?>

<b>���������:</b> ��� �� ������, ����� �����, ���� �� ����� - �������� ����.<br><br>

<table><tr><td><img src=images/misc/m2_case.gif width=50 height=50></td>
<td><input type=text id=num class=btn80></td><td><button class=n_btn onclick='vote()'>�������</button></td></tr></table>
<ul><li><a href=game.php?phrase=584>��������� �������� ���� � ����</a></li></ul>

<script>
function vote()
{
	query( 'quest_scripts/phrase249_ajax.php', '' + _( 'num' ).value );
}
</script>