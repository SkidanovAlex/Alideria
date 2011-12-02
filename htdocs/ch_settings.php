<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">
<head><title>Настройки чата</title></head>

<?

include( 'functions.php' );
include_once( "player.php" );
f_register_globals( );
f_MConnect( );
if( !check_cookie( ) )
	die( 'Неверные настройки Cookie' );
$player = new Player( $HTTP_COOKIE_VARS[c_id] );
$clrs[0] = $player->nick_clr;
$clrs[1] = '000000';
$clrs[2] = '000084';
$clrs[3] = '840000';
$clrs[4] = 'FF0000';
$clrs[5] = '804020'; // was FF8833
$clrs[6] = '00AA00';
$clrs[7] = '0000FF';
$clrs[8] = '606000'; // was AAAA00
$clrs[9] = 'AA00AA';
$clrs[10] = '7a09a0';

f_MConnect( );

$ok = 0;
if( isset( $p_nick_clr ) )
{
	$ok = 1;
	
	settype( $p_nick_clr, 'integer' );
	settype( $p_text_clr, 'integer' );
	
	print( $p_nick_clr.":".$p_text_clr );
	
	if( $p_nick_clr < 0 || $p_nick_clr > 10 )
		$ok = 0;
	if( $p_text_clr < 0 || $p_text_clr > 10 )
		$ok = 0;
}
if( $ok )
{
	$nick_clr = $clrs[$p_nick_clr];
	$clrs[0] = $player->text_clr;
	$text_clr = $clrs[$p_text_clr];
	$clrs[0] = $player->nick_clr;
	f_MQuery( "UPDATE characters SET nick_clr='$nick_clr', text_clr='$text_clr' WHERE player_id=$HTTP_COOKIE_VARS[c_id];" );

	// ---------------------
	$player->nick_clr = $nick_clr;
	$player->text_clr = $text_clr;
	$player->UploadInfoToJavaServer( );
    // ---------------------
	$tm = date( 'H:i' );
	if( isset( $_POST['translit_mode'] ) )
	{
		if( $_POST['translit_mode'] )
		{
			$player->SetTrigger( 322, 1 );
			print( '<script>window.opener.parent.chat_in.translit=true;</script>' );
		}
		else
		{
			$player->SetTrigger( 322, 0 );
			print( '<script>window.opener.parent.chat_in.translit=false;</script>' );
		}
	}
//---------------------------------------
	if (isset($_POST['time_online_ref']))
	{
		$tor = (int)$_POST['time_online_ref'];
		if ($tor >= 15)
		{
			f_MQuery("UPDATE characters SET chat_ref_online=$tor WHERE player_id=".$HTTP_COOKIE_VARS[c_id]);
		}
	}

	die( '<script>window.opener.parent.chat.syst("'.$tm.'", "Настройки успешно изменены");window.close( );</script>' );
}
if( isset( $_POST['translit_mode'] ) || isset($_POST['time_online_ref']))
{
	$tm = date( 'H:i' );
	if( $_POST['translit_mode'] )
	{
		$player->SetTrigger( 322, 1 );
		print( '<script>window.opener.parent.chat_in.translit=true;</script>' );
	}
	else
	{
		$player->SetTrigger( 322, 0 );
		print( '<script>window.opener.parent.chat_in.translit=false;</script>' );
	}
	if (isset($_POST['time_online_ref']))
	{
		$tor = (int)$_POST['time_online_ref'];
		if ($tor >= 15)
		{
			f_MQuery("UPDATE characters SET chat_ref_online=$tor WHERE player_id=".$HTTP_COOKIE_VARS[c_id]);
		}
	}
	die( '<script>window.opener.parent.chat.syst("'.$tm.'", "Настройки успешно изменены");window.close( );</script>' );
}	

$res = f_MQuery( "SELECT login, nick_clr, text_clr FROM characters WHERE player_id=$HTTP_COOKIE_VARS[c_id]" );
$arr = f_MFetch( $res );

$nick_id = 1;
$text_id = 1;

foreach( $clrs as $key=>$value )
{
	if( $value == $arr[nick_clr] )
		$nick_id = $key;
	if( $value == $arr[text_clr] )
		$text_id = $key;
}

function write_list( $a, $b )
{
	global $clrs;
	
	print( "<table border=0 cellspacing=0 cellpadding=0><tr>" );
	
	foreach( $clrs as $key=>$value )
		print( "<td width=20 height=20 bgcolor=$value onClick=\"cl('$a', '$value', '$b', '$key')\">&nbsp;</td>" );
		
	print( "</tr></table>" );
}

?>

<script>
	
function cl( a, b, c, d )	
{
	var ee, cc;
	
	if( document.all )
	{
		ee = document.all[a];
		cc = document.all[c];
	}
	else
	{
		ee = document.getElementById( a );
		cc = document.getElementById( c );
	}
		
	ee.style.color = b;
	cc.value = d;
}

</script>

<center>
<table>
<tr><td align=right>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td align=right><div id=nickname style='color: #<? print( "$arr[nick_clr]" ); ?>'><b><? print( "$arr[0]" ); ?></b>:&nbsp;</div></td><td><div id=txt style='color: #<? print( "$arr[text_clr]" ); ?>'>Текст сообщения</div></td></tr>
<tr><td align=right>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td align=right>Цвет&nbsp;ника:&nbsp;</td><td>
<?
	write_list( 'nickname', 'nick_clr' );
?>
</td></tr>
<tr><td align=right>Цвет&nbsp;сообщения:&nbsp;</td><td>
<?
	$clrs[0] = $player->text_clr;
	write_list( 'txt', 'text_clr' );
?>
</td></tr>
<form action=ch_settings.php method=post>
<input type=hidden id=nick_clr name=nick_clr value=<? print( "$nick_id" ); ?>>
<input type=hidden id=text_clr name=text_clr value=<? print( "$text_id" ); ?>>
<tr><td align=right>&nbsp;</td><td>&nbsp;</td></tr>
<tr>
<td colspan=2 align=center>
<label><input type="radio" name="translit_mode" value="1" <? if( $player->HasTrigger( 322 ) ) echo 'checked=true' ?>> Включить транслитерацию</label>
<br>
<label><input type="radio" name="translit_mode" value="0" <? if( !$player->HasTrigger( 322 ) ) echo 'checked=true' ?>> Отключить транслитерацию</label>
<br><br>
Частота обновления списка игроков: <input class="m_btn" type="text" style="width:100px;" name="time_online_ref" /> секунд<br>
<small>Не менее 15 секунд</small><br><br>
<input type=submit class=m_btn value='Сохранить'>
<br>
<br>Внимание! Цвет ника, купленный у Фавна, потеряется, если вы смените его на один из имеющихся в этой таблице!</form><br><br>
</td></tr>
</table>
</center>


<?

f_MClose( );

?>
