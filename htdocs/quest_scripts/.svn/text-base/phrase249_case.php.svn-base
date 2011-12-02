<?

if( !$mid_php ) die( );

if( $player->HasTrigger( 74 ) )
		die( "<script>location.href='game.php?phrase=585';</script>" );


?>

<b>Разбойник:</b> вот он сундук, перед тобой, дело за малым - отгадать шифр.<br><br>

<table><tr><td><img src=images/misc/m2_case.gif width=50 height=50></td>
<td><input type=text id=num class=btn80></td><td><button class=n_btn onclick='vote()'>Назвать</button></td></tr></table>
<ul><li><a href=game.php?phrase=584>Отчаяться отгадать шифр и уйти</a></li></ul>

<script>
function vote()
{
	query( 'quest_scripts/phrase249_ajax.php', '' + _( 'num' ).value );
}
</script>