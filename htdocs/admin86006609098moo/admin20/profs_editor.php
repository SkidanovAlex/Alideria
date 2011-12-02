<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../profession_list.php' );

$ok = false;
if( isset( $_GET['add_prof'] ) )
{
	$professions[$_GET['id']] = $_GET['add_prof'];
	$ok = true;
}

else if( $_GET['del_prof'] )
{
	unset( $professions[$_GET['del_prof']] );
	$ok = true;
}

if( $ok )
{
	$f = fopen( "../profession_list.php", "wt" );
	fwrite( $f, "<?\n".'$professions'." = Array(" );
	$ok = false;
	foreach( $professions as $a=>$b )
	{
		if( $ok ) fwrite( $f, "," );
		$ok = true;
		fwrite( $f, " $a=>\"$b\"" );
	}
	fwrite( $f, " );\n?>\n" );
	fclose( $f );
}

printf( "<br><b>Список профессий</b><br><a href=index.php>На главную</a><br><br>" );
foreach( $professions as $a=>$b )
	printf( "[ID: $a] $b (<a href=profs_editor.php?del_prof=$a>Удалить</a>)<br>\n" );
	
printf( "<form action=profs_editor.php method=get><br>Добавить профу:<br>" );
printf( "ID: <input type=text class=m_btn name=id value=0><br>" );
printf( "Название: <input type=text class=m_btn name=add_prof value='Новая профа'><br>" );
printf( "<input type=submit value='Добавить'><br></form>" );

?>
