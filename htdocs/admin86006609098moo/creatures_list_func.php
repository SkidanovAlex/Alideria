<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( "../functions.php" );

if( isset( $HTTP_GET_VARS['create'] ) )
{
	f_MConnect( );

	include( 'admin_header.php' );
	
	f_MQuery( "INSERT INTO creatures ( name, attack, defence, genre ) VALUES ( 'Новое существо', 0, 0, 0 );" );
	
	$z = mysql_insert_id( );
	print( "<script>parent.lst.document.getElementById( 'moo' ).innerHTML += '<a href=creatures_editor_mid.php?id=$z target=mid>(0) Новое существо</a><br>';</script>" );
	
	f_MClose( );
}

?>

<center>
<button onClick='location.href="creatures_list_func.php?create"' class=m_btn>Создать</button><br>
<br><br><a href=index.php target=_top>На главную</a><br><br>
</center>
