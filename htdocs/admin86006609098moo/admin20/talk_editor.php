<?

include_once( '../functions.php' );
include_once( '../arrays.php' );

if( !$moo )
{
	f_MConnect( );

	include( 'quest_header.php' );

	?>
	
<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

	<?
}

$id = $HTTP_GET_VARS[talk_id];

if( isset( $HTTP_POST_VARS[text] ) )
{
	$txt = ( $HTTP_POST_VARS[text] );
	$flavor = ( $HTTP_POST_VARS[flavor] );
	$postludium = ( $HTTP_POST_VARS[postludium] );
	$redir_timer = $_POST['redir_timer'];
	$redir_to = $_POST['redir_to'];

	f_MQuery( "UPDATE talks SET redir_timer=$redir_timer, redir_to=$redir_to, text='$txt', flavor_text='$flavor', postludium='$postludium' WHERE talk_id=$id" );
}

if( isset( $HTTP_GET_VARS[add_new_phrase] ) )
{
	f_MQuery( "INSERT INTO phrases ( text ) VALUES ( 'Новая фраза' )" );
	$q = mysql_insert_id( );
	f_MQuery( "INSERT INTO talk_phrases ( talk_id, phrase_id ) VALUES ( $id, $q )" );
}

if( isset( $HTTP_GET_VARS[add_phrase] ) )
{
	f_MQuery( "INSERT INTO talk_phrases ( talk_id, phrase_id ) VALUES ( $id, $HTTP_GET_VARS[add_phrase] )" );
}

if( isset( $HTTP_GET_VARS[del_phrase] ) )
{
	f_MQuery( "DELETE FROM talk_phrases WHERE talk_id=$id AND phrase_id= $HTTP_GET_VARS[del_phrase]" );
}

$res = f_MQuery( "SELECT * FROM talks WHERE talk_id = $id" );

$arr = f_MFetch( $res );

print( "<b>Talk UIN: $arr[talk_id]</b><br>" );
print( "<form action=talk_editor.php?talk_id=$id method=post>" );
print( "Описание обстановки:<br><textarea name=flavor rows=4 cols=50>$arr[flavor_text]</textarea><br>" );
print( "Реплика NPC:<br><textarea name=text rows=10 cols=50>$arr[text]</textarea><br>{муж|жен} = текст, зависящий от пола<br>{name} - имя игрока" );
print( "<br>Описание после реплики:<br><textarea name=postludium rows=10 cols=50>$arr[postludium]</textarea><br>{муж|жен} = текст, зависящий от пола<br>{name} - имя игрока" );
print( "<br>Редир-таймер: <input type=text name=redir_timer value=$arr[redir_timer]> Редир-куда: <input type=text name=redir_to value=$arr[redir_to]>"  );

print( "<br><input type=submit value='Изменить текст'>" );
print( "</form>" );

print( "<br><b>Фразы:</b><br>" );

$res = f_MQuery( "SELECT phrases.* FROM phrases, talk_phrases WHERE talk_id = $id AND phrases.phrase_id = talk_phrases.phrase_id ORDER BY phrases.phrase_id" );
while( $arr = f_MFetch( $res ) )
{
	print( "<b>UIN: $arr[phrase_id];</b> <a href=phrase_editor.php?id=$arr[phrase_id]&from=$id>$arr[text]</a> (<a href=talk_editor.php?talk_id=$id&del_phrase=$arr[phrase_id]>Удалить</a>)" );
	if( $arr[attack_id] < 0 )
		print( " -- <a href=talk_editor.php?talk_id=".(- $arr[attack_id]).">Перейти к толку</a>" );
	else if( $arr[attack_id] > 0 )
	{
		$qres = f_MQuery( "SELECT * FROM mobs WHERE mob_id = $arr[attack_id]" );
		$qarr = f_MFetch( $qres );
		
		if( !$qarr ) print( " -- лаг!!! :)" );
		else print( " -- $qarr[name]" );
	}
	print( "<br>" );
}
	
print( "<br>" );
print( "<a href=talk_editor.php?talk_id=$id&add_new_phrase>Добавить новую фразу</a><br>" );
print( "<form method=get action=talk_editor.php><input type=hidden name=talk_id value=$id><input class=m_btn name=add_phrase value=0><input class=m_btn type=submit value='Добавить фразу'></form>" );

?>
