<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">
<html>
  <head>
<script src=js/ajax.js></script>
<script>
function TopList() // ���������� �������� ���=1
{
  query( "player_forum_act.php?act=1", "" );
}
//********
function MyTopAdd() // ���������� ��������� ������ ���=2
{
if(!_('ssylka').value){alert("�� ����� ��������� ������ !!!");_('ssylka').focus();return;}
//query( "player_forum_act.php?act=2&comm="+_('comment').value+"&lnk="+_('ssylka').value , "" );
query( "player_forum_act.php?act=2&comm="+encodeURIComponent(_('comment').value)+"&lnk="+encodeURIComponent(_('ssylka').value),"");



_('ssylka').value = "";
_('comment').value = "None"
}
//********
function DelTop(id) // �������� ������ ���=3
{
if (confirm('������� ������� � ������ ?'))
{
  query( "player_forum_act.php?act=3&del="+id, "" );
}
}
function LoadOldTop()
// �������� ���� ������� � �������� ������� ���������.
// �� ��������� ����� :o) ���������� �������� 2 ������.
{
  query( "player_forum_act.php?act=4", "" );
}

function DelAllTop()
{
if (confirm('�� ������������� ������ �������� ������� ?!'))
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
	die( "�������� ��������� Cookie" );
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
?>
<title> �������� ������� </title>
  </head>

 <body onload='TopList()'>
<?

//************************************************* <body onload='TopList();'>

$nkres = f_MQuery( "SELECT login FROM characters WHERE  player_id=$player->player_id" );
if($nk_dim = f_MFetch( $nkres ))
{
echo "<center><br><table width=600 style='border:1px solid black' background=images/chat/chat_bg.gif><tr><td align=center>";
echo "<b>�������� ������� ��������� ".$nk_dim['login'];
echo "</b></td></tr></table>";
}
else
{
echo "<center><br><table width=600 style='border:1px solid black' background=images/chat/chat_bg.gif><tr><td align=center>";
echo "<b>This player has not registered";
echo "</b></td></tr></table>";
}

echo "<br>�� ������ �������� � ������� ���� ������:<br>";
echo "<input id='ssylka' type='text'  size='126' value=''><br>";
echo "� ����������� � ����������� (�� �������):<br>";
echo "<input id='comment' type='text'  size='126' value='None'><br><br>";
echo "<input type=button value='��������...' onclick='MyTopAdd()'><br><br>";

echo "<input type=button value='+' onclick='LoadOldTop()'>";
echo " * �� ������ ������� ������� ��� ������������ �������, ��� ����� ������� �� ������ <b>[+]</b><br>";
echo " * <b>PS: ������� ����� ����������� � ������������� � 2 ������</b><br><br>";

echo "<input type=button value='-' onclick='DelAllTop()'>";
echo " * �� ������ ��������� �������� �������, ��� ����� ������� �� ������ <b>[-]</b><br>";
?>
     <div id="myTOP">
    <?php
    echo $Result;
    ?>
   </div>
 </body>
</html>
