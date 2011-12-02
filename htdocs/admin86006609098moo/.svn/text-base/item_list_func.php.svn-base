<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( "../functions.php" );

if( isset( $HTTP_GET_VARS['create'] ) )
{
	f_MConnect( );
	
	include( 'admin_header.php' );
	
	f_MQuery( "LOCK TABLES items WRITE" );
	f_MQuery( "INSERT INTO items ( name, effect ) VALUES ( 'Новая Вещь', '' );" );
	
	$z = mysql_insert_id( );
	f_MQuery( "UPDATE items SET parent_id=$z WHERE item_id=$z" );
	f_MQuery( "UNLOCK TABLES" );
	print( "<script>parent.lst.document.getElementById( 'moo' ).innerHTML += '<a href=item_editor_mid.php?id=$z target=mid>Новая Вещь (0, lvl: 0)</a><br>';</script>" );
	
	f_MClose( );
}

?>

<center>
<button onClick='location.href="item_list_func.php?create"' class=m_btn>Создать</button>
<br><br><a href=index.php target=_top>На главную</a>
<br><a href=item_table.php target=_blank>Таблица статов и цен</a>
<br><a href=item_calc.php target=_blank>Калькулятор</a>
</center>
