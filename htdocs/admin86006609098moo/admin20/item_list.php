<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );
include( "../arrays.php" );

f_MConnect( );

if( isset( $_GET[type] ) )
{
	$type = $_GET[type];
	settype( $type, 'integer' );

	$res = f_MQuery( "SELECT item_id, name, level, type, image, price FROM items WHERE parent_id=item_id AND type = $type ORDER BY type, type2, level" );
}
else if( isset( $_GET[type2] ) )
{
	$type = $_GET[type2];
	settype( $type, 'integer' );

	$res = f_MQuery( "SELECT item_id, name, level, type, image, price FROM items WHERE parent_id=item_id AND type = 0 AND type2 = $type ORDER BY type, level" );
}

else
	$res = f_MQuery( "SELECT item_id, name, level, type, image, price FROM items WHERE parent_id=item_id ORDER BY type, type2, level" );
	
echo( '<a href=item_list.php><b>Все</b></a> &nbsp; ' );
foreach( $item_types as $a=>$b ) print( "<a href=item_list.php?type=$a><b>$b</b></a> &nbsp; \n" );
echo "<br><b>Ресурсы: </b>";
foreach( $item_types2 as $a=>$b ) print( "<a href=item_list.php?type2=$a><b>$b</b></a> &nbsp; \n" );
echo( "<br><br>" );
/*
while( $arr = mysql_fetch_array( $res ) )
{
	print( "<a href=item_editor_mid.php?id=$arr[item_id] target=mid>$arr[name] ($arr[type], lvl: $arr[level])</a><br>" );
}
*/


print "<table width=100% cellpadding=0 cellspacing=0 border=0>";
while( $arr = mysql_fetch_array( $res ) )
{
	print "<tr><td width=60 height=60 valign=top><table cellpadding=0 cellspacing=0 border=0 background='../images/items/bg.gif' width=60 height=60><tr><td align=center valign=middle><a href=item_editor_mid.php?id=$arr[item_id] target=mid><img border=0 src='../images/items/".$arr[image]."'></a></td></tr></table></td><td valign=top><a href=item_editor_mid.php?id=$arr[item_id] target=mid><b>$arr[name]</b></a><br><b>Тип:</b> {$item_types[$arr[type]]}<br> <b>Ур:</b> $arr[level] <img src='/images/money.gif'> $arr[price]</td></tr>";
}
print "</table>";



f_MClose( );

?>
</div>
