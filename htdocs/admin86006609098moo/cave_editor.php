<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

print( "<h1>�������� �����</h1>" );
print( "<br><br><a href=cave_editor.php target=_top>������� �������</a><br><a href=loc_editor.php target=_top>� �������� �������</a><br><a href=mob_editor.php target=_top>� �������� �����</a><br><a href=index.php target=_top>�� �������</a><br><br>" );

if( !isset( $HTTP_GET_VARS[depth] ) )
{
	print( "<b>������� �������:</b>" );
	for( $i = 0; $i <= 20; ++ $i ) print( " <a href=cave_editor.php?depth=$i>$i</a>" );
	die( );
}

$depth = $HTTP_GET_VARS['depth'];
settype( $depth, 'integer' );
if( $depth < 0 ) $depth = 0;
if( $depth > 20 ) $depth = 20;

?>

����� ���� ��� �������� ��������� �������� ��� ������������ � ������� ���������� ���������:<br>
1. ���� �������� ������� �� �������, �� ������� ������ ������� �� ���, ��� 100% ������� ���, � �������� ������ ������� ������� � ���� "�������� �������".<br>
2. �����, ���� �������� ��� ��� �� ���� �������, ��� �� ���� ��� �� �� ��������, ��<br>
2.1. � ������������ 30% �� ��������� �������� ���, ������� ������ ������� �� ���� ������� (���������� ��������)<br>
2.2. ��������� 70% ����� ������������ � ���� ��������� �� ��������� ����.<br><br>

<?

print( "<b>������, ������� $depth</b><br>" );

if( isset( $HTTP_GET_VARS[item_id] ) )
{
	$item_id = $HTTP_GET_VARS[item_id];
	
	settype( $item_id, 'integer' );
	
	f_MQuery( "INSERT INTO cave_items VALUES( $depth, $item_id )" );
}
if( isset( $HTTP_GET_VARS[del] ) )
{
	$item_id = $HTTP_GET_VARS[del];
	settype( $item_id, 'integer' );
	f_MQuery( "DELETE FROM cave_items WHERE item_id = $item_id AND depth = $depth" );
}

$res = f_MQuery( "SELECT cave_items.*, items.name, items.price FROM cave_items, items WHERE depth = $depth AND items.item_id = cave_items.item_id ORDER BY item_id" );
if( mysql_num_rows( $res ) )
{
	include_once("../kopka3.php");
	$kopka = new Kopka( );
	print( "<table border=1><tr><td align=center><b>�������� ����</b></td></tr>" );
	while( $arr = f_MFetch( $res ) )
	{
		$kopka->AddItem($arr[item_id], $arr[price]);
		print( "<tr><td>$arr[name]</td><td><a href=cave_editor.php?depth=$depth&del=$arr[item_id]>�������</a></td></tr>" );
	}
	print( "</table><br><br>" );
	$ret = $kopka->GetItemId( 15, 150 + $depth * 20 + ( $depth ? 75 : 0 ), false );
	foreach( $ret as $a ) echo "$a<br>";
}
else print( "<i>� ���� �������� �� ������������ ����</i><br>" );

?>

<br>
<b>�������� ����</b><br>
<table>
<form action=cave_editor.php method=get>
<tr><td><input type=hidden name=depth value=<? print $depth ?>>
���� ����: </td><td><input type=text name=item_id value=0></td></tr>
<tr><td>&nbsp;</td><td><input type=submit value='��������'></td></tr>
</form>
</table>
