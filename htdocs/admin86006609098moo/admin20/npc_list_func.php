<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( "../functions.php" );

if( isset( $HTTP_GET_VARS['create'] ) )
{
	f_MConnect( );
	
	include( 'quest_header.php' );


	f_MQuery( "INSERT INTO talks ( text ) VALUES ( 'Приветствие NPC' );" );
	$q = mysql_insert_id( );
	f_MQuery( "INSERT INTO npcs ( name, talk_id ) VALUES ( 'Новый NPC', $q );" );
	
	$z = mysql_insert_id( );
	print( "<script>parent.lst.document.getElementById( 'moo' ).innerHTML += '<a href=npc_editor_mid.php?id=$z target=mid>Новый NPC</a><br>';</script>" );
	
	f_MClose( );
}

if( isset( $HTTP_GET_VARS['create_talk'] ) )
{
	f_MConnect( );
	
	include( 'quest_header.php' );


	f_MQuery( "INSERT INTO talks ( text ) VALUES ( 'Новый Толк' );" );
	
	$z = mysql_insert_id( );
	print( "<script>parent.lst_talk.document.getElementById( 'moo' ).innerHTML += '<a href=talk_editor.php?talk_id=$z target=mid>$z</a> ';</script>" );
	
	f_MClose( );
}

?>

<center>
<button onClick='location.href="npc_list_func.php?create"' class=m_btn>Создать</button>
<button onClick='location.href="npc_list_func.php?create_talk"' class=m_btn>Создать Толк</button>
<br><br><a href=index.php target=_top>На главную</a><br><br>
</center>
