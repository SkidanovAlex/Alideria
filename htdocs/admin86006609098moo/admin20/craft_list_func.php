<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( "../functions.php" );

if( isset( $HTTP_GET_VARS['create'] ) )
{
	f_MConnect( );
	
	include( 'admin_header.php' );
	
	f_MQuery( "INSERT INTO recipes ( ingridients, result, req, name, prof ) VALUES ( '', '', '', '����� ������', 1 );" );
	
	$z = mysql_insert_id( );
	print( "<script>parent.lst.document.getElementById( 'moo' ).innerHTML += '<a href=craft_editor_mid.php?id=$z target=mid>����� ������</a><br>';</script>" );
	
	f_MClose( );
}

?>

<center>
<button onClick='location.href="craft_list_func.php?create"' class=m_btn>�������</button>
<br><br><a href=index.php target=_top>�� �������</a>
</center>
