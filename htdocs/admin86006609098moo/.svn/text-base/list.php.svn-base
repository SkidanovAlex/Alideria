<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );
include( "../arrays.php" );

f_MConnect( );

include( 'admin_header.php' );

print( "<a href=list.php>Показать все свитки</a><br>" );
print( "<b>Стихия:</b>" );
foreach( $genres as $a=>$b ) print( " &nbsp; <a href=list.php?genre=$a>$b</a>" );
print( "<br>" );
print( "<b>Уровень:</b>" );
for( $a = 1; $a <= 20; ++ $a ) print( " &nbsp; <a href=list.php?level=$a>$a</a>" );
print( "<hr>" );

if( isset( $_GET['genre'] ) )
{
	$genre = $_GET['genre'];
	settype( $genre, 'integer' );
	$res = f_MQuery( "SELECT card_id, name, genre FROM cards WHERE genre='$genre' AND mk=0 ORDER BY genre, name" );
	print( "<b>Текущий фильтр: стихия {$genres[$genre]}</b><br>" );
}

else if( isset( $_GET['level'] ) )
{
	$level = $_GET['level'];
	settype( $level, 'integer' );
	$res = f_MQuery( "SELECT card_id, name, genre FROM cards WHERE level='$level' AND mk=0 ORDER BY genre, name" );
	print( "<b>Текущий фильтр: уровень $level</b><br>" );
}

else $res = f_MQuery( "SELECT card_id, name, genre FROM cards WHERE mk=0 ORDER BY name" );

while( $arr = mysql_fetch_array( $res ) )
{
	print( "<a href=editor.php?id=$arr[card_id] target=mid>$arr[name]</a><br>" );
}

f_MClose( );

?>
</div>
