<?

if( !$mid_php ) die( );

if( $quest_9m && $player->HasTrigger( 245 ) )
{
	include( 'arena_9m.php' );
	return;
}

?>

<script src=js/arena.php></script>
<script>
var arids = new Array( 0, 1, 2, 10, 11 );
function setType( a )
{
	for( i in arids ) document.getElementById( 'bt' + arids[i] ).style.fontWeight = '';
	document.getElementById( 'bt' + a ).style.fontWeight = 'bold';
}
</script>

<table width=100%><tr><td>
<? ScrollLightTableStart( ); ?>
<table border=0 width=100%><colgroup><col width=180><col width=*><tbody><tr><td valign=top height=100%>
<? ScrollTableStart( ); ?>
<button class=s_btn id=bt0 onClick='ar(0)'>Дуэли</button><br>
<button class=s_btn id=bt1 onClick='ar(1)'>Групповые&nbsp;бои</button><br>
<button class=s_btn id=bt2 onClick='ar(2)'>Хаотичные&nbsp;бои</button><br>
<br>
<button class=s_btn id=bt10 onClick='ar(10)'>Текущие&nbsp;бои</button><br>
<button class=s_btn id=bt11 onClick='ar(11)'>Прошедшие&nbsp;бои</button><br>
<br><br><br><br><br>
<? ScrollTableEnd( ); ?>
</td><td valign=top width=100% height=100%>
<? ScrollTableStart( "left" ); ?>
<div id=arena_body name=arena_body>&nbsp;</div>
<? ScrollTableEnd( ); ?>
</td></tr></table>
<? ScrollLightTableEnd( ); ?>
</td></tr></table>

<?

$tp = $player->getBetType( );
if( $tp == -1 ) $tp = 1;
-- $tp;

?>

<iframe name=arena_ref id=arena_ref width=0 height=0 style="border: 0;" src='arena_ref.php?a=<? print $tp; ?>'></iframe>
