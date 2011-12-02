<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( "../functions.php" );

if( isset( $HTTP_GET_VARS['create'] ) )
{
	f_MConnect( );

	include( 'admin_header.php' );
	
	f_MQuery( "INSERT INTO pets ( name ) VALUES ( 'Новый пет' );" );
	
	$z = mysql_insert_id( );
	print( "<script>parent.lst.document.getElementById( 'moo' ).innerHTML += '<a href=pet_editor_mid.php?id=$z target=mid>Новый пет</a><br>';</script>" );
	
	f_MClose( );
}

?>

<center>
<button onClick='location.href="pet_list_func.php?create"' class=m_btn>Создать</button>
<br><br><a href=index.php target=_top>На главную</a>
</center>
