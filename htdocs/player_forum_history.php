<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">
<html>
  <head>
<script src=js/ajax.js></script>
<script>
function TopList() // обновление страницы АСТ=1
{
  query( "player_forum_act.php?act=1", "" );
}
//********
function MyTopAdd() // добавление сторонней ссылки АСТ=2
{
if(!_('ssylka').value){alert("Не верно заполнена ссылка !!!");_('ssylka').focus();return;}
//query( "player_forum_act.php?act=2&comm="+_('comment').value+"&lnk="+_('ssylka').value , "" );
query( "player_forum_act.php?act=2&comm="+encodeURIComponent(_('comment').value)+"&lnk="+encodeURIComponent(_('ssylka').value),"");



_('ssylka').value = "";
_('comment').value = "None"
}
//********
function DelTop(id) // удаление ссылки АСТ=3
{
if (confirm('Удалить историю о топике ?'))
{
  query( "player_forum_act.php?act=3&del="+id, "" );
}
}
function LoadOldTop()
// загрузка всех топиков с участием данного персонажа.
// по настаянию свИШИ :o) утверждена давность 2 недели.
{
  query( "player_forum_act.php?act=4", "" );
}

function DelAllTop()
{
if (confirm('Вы действительно хотите очистить историю ?!'))
{query( "player_forum_act.php?act=5", "" );}
}
</script>


<?php
//***********************************************************************
include_once( "functions.php" );
include_once( "player.php" );
include_js( 'functions.js' );
include_js( 'js/clans.php' );
include_js( 'js/ii.js' );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
?>
<title> Форумная история </title>
  </head>

 <body onload='TopList()'>
<?

//************************************************* <body onload='TopList();'>

$nkres = f_MQuery( "SELECT login FROM characters WHERE  player_id=$player->player_id" );
if($nk_dim = f_MFetch( $nkres ))
{
echo "<center><br><table width=600 style='border:1px solid black' background=images/chat/chat_bg.gif><tr><td align=center>";
echo "<b>Форумная история персонажа ".$nk_dim['login'];
echo "</b></td></tr></table>";
}
else
{
echo "<center><br><table width=600 style='border:1px solid black' background=images/chat/chat_bg.gif><tr><td align=center>";
echo "<b>This player has not registered";
echo "</b></td></tr></table>";
}

echo "<br>Вы можете добавить в историю свою ссылку:<br>";
echo "<input id='ssylka' type='text'  size='126' value=''><br>";
echo "И сопроводить её коментарием (по желанию):<br>";
echo "<input id='comment' type='text'  size='126' value='None'><br><br>";
echo "<input type=button value='Добавить...' onclick='MyTopAdd()'><br><br>";

echo "<input type=button value='+' onclick='LoadOldTop()'>";
echo " * Вы можете сделать выборку уже существующих топиков, для этого нажмите на кнопку <b>[+]</b><br>";
echo " * <b>PS: выборка будет произведена с актуальностью в 2 недели</b><br><br>";

echo "<input type=button value='-' onclick='DelAllTop()'>";
echo " * Вы можете полностью очистить историю, для этого нажмите на кнопку <b>[-]</b><br>";
?>
     <div id="myTOP">
    <?php
    echo $Result;
    ?>
   </div>
 </body>
</html>
