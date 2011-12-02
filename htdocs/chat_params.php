<?

include_once( "functions.php" );
include_once( "chat_functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "<script>window.top.location.href='index.php';</script>" );

$player_id = $HTTP_COOKIE_VARS['c_id'];

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">

<script>
function chs()
{
	var fs_main = parent.parent.parent.document.getElementById('fs_main');
	var chs_chat = document.getElementById('chs_chat');
	if (fs_main.rows == "0,0,*,30%")
	{
		fs_main.rows = "0,0,*,50%";
		chs_chat.innerHTML = '<img src=images/ch_down.png width=20 height=20>';
		chs_chat.title = 'Уменьшить чат';
	}
	else
	{
		fs_main.rows = "0,0,*,30%";
		chs_chat.innerHTML = '<img src=images/ch_up.png width=20 height=20>';
		chs_chat.title = 'Увеличить чат';
	}
}
</script>

<table width=100% cellpading=0 cellspacing=0 height=30>
<tr>
<td background="images/chat/line.gif" valign=middle>

	<table width=100% cellpading=0 cellspacing=0>
	<colgroup><col width=110><col width=5><col width=*><tbody>
	<td>
<?
	if ( strstr(getenv("HTTP_USER_AGENT"),"MSIE"))
	{
?>
		<a id='clean_chat' style='cursor: pointer' title='Очистить чат' onClick='window.top.cleanChat();'><img src=images/ch_red_cross.png width=20 height=20></a><a style='cursor: pointer' title='Настройки чата' onClick="window.open('ch_settings.php','_blank','scrollbars=no,width=400,height=300,resizable=no')"><img src=images/ch_settings.gif width=20 height=20></a><a style='cursor: pointer' title='Настройки доступа в закрытые комнаты' onClick="window.open('ch_rooms_control.php','_blank','scrollbars=no,width=400,height=300,resizable=no')"><img src=images/crooms.gif width=20 height=20></a><a style='cursor: pointer' title='Настройки фильтров и игнора' onClick="window.open('ch_ignore.php','_blank','scrollbars=no,width=400,height=300,resizable=no')"><img src=images/ignor.gif width=20 height=20></a><a id='chs_chat' style='cursor: pointer' title='Увеличить чат' onClick='chs()'><img src=images/ch_up.png width=20 height=20></a>
<?
	}
	else
	{
?>
		<a id='clean_chat' style='cursor: pointer' title='Очистить чат' onClick='window.top.cleanChat();'><img src=images/ch_red_cross.png width=20 height=20></a><a style='cursor: pointer' title='Настройки чата' onClick="window.open('ch_settings.php','_blank','scrollbars=no,width=400,height=300,resizable=no')"><img src=images/ch_settings.gif width=20 height=20></a><a style='cursor: pointer' title='Настройки доступа в закрытые комнаты' onClick="window.open('ch_rooms_control.php','_blank','scrollbars=no,width=400,height=300,resizable=no')"><img src=images/crooms.gif width=20 height=20></a><a style='cursor: pointer' title='Настройки фильтров и игнора' onClick="window.open('ch_ignore.php','_blank','scrollbars=no,width=400,height=300,resizable=no')"><img src=images/ignor.gif width=20 height=20></a><a id='chs_chat' style='cursor: pointer' title='Увеличить чат' onClick='chs()'><img src=images/ch_up.png width=20 height=20></a>
<? } ?>

	</td>
	<td>
		<div id=privates>&nbsp;</div>
	</td>
	<td align=right>

<?
print( "<table cellspacing=0 cellpadding=0 border=0><tr>" );

print( "<td><img border=0 width=17 height=21 src=images/top/b.png></td>" );
print( "<td width=92 height=21 background=images/top/f.png align=center valign=middle>" );
print( "<a href=help.php target=_blank>Помощь</a>" );
print( "</td>" );

print( "<td><img border=0 width=17 height=21 src=images/top/d.png></td>" );
print( "<td width=92 height=21 background=images/top/f.png align=center valign=middle>" );
print( "<a href=waste.php target=game>Мини-игры</a>" );
print( "</td>" );

print( "<td><img border=0 width=17 height=21 src=images/top/d.png></td>" );
print( "<td width=92 height=21 background=images/top/f.png align=center valign=middle>" );
print( "<a href=forum.php target=_blank>Форумы</a>" );
print( "</td>" );

//print( "<td><img border=0 width=17 height=21 src=images/top/c.png></td>" );
print( "</tr></table>" );


?>
	</td>
	</table>

</td>
</tr>
</table>

<table cellpadding=0 cellspacing=0 border=0 width=100%>
<tr>
<td width=5 bgcolor=#e0c3a0 background="images/chat/chat_corner_0.gif"><img src="empty.gif" width=5 height=5></td>
<td width=100% bgcolor=#e0c3a0 background="images/chat/chat_border_top.gif"><img src="empty.gif" width=5 height=5></td>
<td width=5 bgcolor=#e0c3a0 background="images/chat/chat_corner_1.gif"><img src="empty.gif" width=5 height=5></td>
<td width=17 bgcolor=#e3ac67 background="images/bg.gif"><img src="empty.gif" width=17 height=5></td>
<td width=5 bgcolor=#e0c3a0 background="images/chat/chat_corner_0.gif"><img src="empty.gif" width=5 height=5></td>
<td width=223 bgcolor=#e0c3a0 background="images/chat/chat_border_top.gif"><img src="empty.gif" width=223 height=5></td>
<td width=5 bgcolor=#e0c3a0 background="images/chat/chat_corner_1.gif"><img src="empty.gif" width=5 height=5></td>
<td width=17 bgcolor=#e3ac67 background="images/bg.gif"><img src="empty.gif" width=17 height=5></td>
</tr>
</table>

<?
include( "player_noobs.php" );
include( "player.php" );
$player = new Player( $player_id );
$res = f_MQuery( "SELECT stage FROM player_noobs WHERE player_id=$player_id" );
if( $player->level == 1 && f_MNum( $res ) )
{
?>
<script>
	parent.my_id = <?=$player_id;?>;
	parent.createPrivateRoom( 'Первые Шаги' );
	parent.createPrivateRoom( 'Общий' );
	parent.setPrivate( 0 );
	function abitlater(){
<?
$arr = f_MFetch( $res );
for( $i = 0; $i <= $arr[0]; ++ $i )
	NoobSendMsg( $i );
?>
}
setTimeout( 'abitlater()', 5000 );
</script>
<?
}
else
{
?>
	<script> parent.my_id = <?=$player_id;?>; parent.cleanPrivates( );</script>
<?
	$res = f_MQuery( "SELECT clan_id, level FROM characters WHERE player_id=$player_id" );
	$arr = f_MFetch( $res );
	if( $arr[1] > 1 )
		echo "<script>parent.createPrivateRoom( 'Торговый' );parent.setPrivate( 0 );</script>";
	if( $arr[0] ) echo "<script>parent.createPrivateRoom( 'Орден' );parent.setPrivate( 0 );</script>";
//	echo "<script>parent.createPrivateRoom( 'Системные' );parent.setPrivate( 0 );</script>";
	$res = f_MQuery( "SELECT channel_id FROM ch_channels WHERE player_id=$player_id" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr[0] < 1000000000 ) echo "<script>parent.createPrivateRoom( '#{$arr[0]}' );parent.setPrivate( 0 );</script>";
		else echo "<script>parent.createPrivateRoom( '@".($arr[0]-1000000000)."' );parent.setPrivate( 0 );</script>";
	}
}
?>
