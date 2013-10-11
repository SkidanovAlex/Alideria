<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );
include_once( '../items.php' );

f_MConnect( );

include( 'admin_header.php' );

function create_select( $nm, $arr, $val )
{
	$st = "<select name='$nm'>";
	
	foreach( $arr as $key=>$value )
	{
		$st .= "<option value=$key";
		if( $key == $val ) $st .= " selected";
		$st .= ">$value" ;
	}
	
	$st .= '</select>';
	
	return $st;
}

$attributes = array();
$res = f_MQuery("SELECT * FROM attributes ORDER BY name");

while ($arr = f_MFetch($res)) if ($arr['attribute_id'] != 555) {
    $attributes[(int)$arr['attribute_id']] = $arr['name'];
}

$images = array(13 => 'luck', 15 => 'o', 16 => 'c', 222 => 'r', 
        131 => 'wa', 132 => 'wd', 30 => 'w_ic1',
        141 => 'na', 142 => 'nd', 40 => 'e_ic1',
        151 => 'fa', 152 => 'fd', 50 => 'f_ic1',
        101 => 'hp', 313 => 'e_ic4', 315 => 'w_ic4', 316 => 'f_ic4');

if (isset($_GET['attr'])) {
    $attr = (int)$_GET['attr'];
    $value = (int)$_GET['value'];
    if (!isset($images[$attr])) {
        echo "<b><font color=red>��� ����� ����� ��� ��������</font></b><br><br>";
    }
    else {
        include("stat_picture.php");
        include("create_image.php");

        if ($attr != 0) {
            $ares = f_MQuery("SELECT * FROM attributes WHERE attribute_id = $attr");
            $aarr = f_MFetch($ares);

            $attr_name = $aarr['name'];
            $stats = $aarr['stats'];
        }
        else {
            $attr_name = "�������";
            $stats = 1;
        }

        $url = "auto/".create_stat_image($images[$attr], $value);

        $name = "$attr_name +{$value}";
        $item_level = $stats * $value;

		$fields = Array( );
		$fields["name"] = "����: $name";
		$fields["name2"] = "����: $name";
		$fields["name3"] = "����: $name";
		$fields["name4"] = "����: $name";
		$fields["name5"] = "�����: $name";
		$fields["name6"] = "����: $name";
		$fields["name_m"] = "����: $name";
		$fields["name2_m"] = "���: $name";
		$fields["name3_m"] = "�����: $name";
		$fields["name4_m"] = "����: $name";
		$fields["name5_m"] = "������: $name";
		$fields["name6_m"] = "�����: $name";
		$fields["level"] = $item_level;
		$fields["type"] = 36;
		$fields["price"] = 2 * pow( 2, (int)( ( 1 + $item_level ) / 2 ) ) * ( 1.5 - 0.5 * ( $item_level % 2 ) );
		$fields["req"] = "";
		$fields["weight"] = 2 * $item_level;
		$fields["image"] = "auto/".create_image_item("items/".$url);

		$astr = "";
		$bstr = "";
		foreach( $fields as $a=>$b ) 
		{
			$astr .= ", $a";
			$bstr .= ", '$b'";
		}

		$astr = substr( $astr, 1 );
		$bstr = substr( $bstr, 1 );
		f_MQuery( "INSERT INTO items($astr ) VALUES ($bstr )" );
		$q = mysql_insert_id( );
		f_MQuery( "UPDATE items SET parent_id = item_id WHERE item_id = $q" );

        f_MQuery("INSERT INTO runes (attr_id, value, item_id, image) VALUES ($attr, $value, $q, '$url')");
    }
}
else if (isset($_GET['del'])) {
    $rune_id = (int)$_GET['del'];
    f_MQuery("DELETE FROM runes WHERE rune_id={$rune_id}");
}

?>

<a href=index.php>�� �������</a><br>
<b>�������� ���</b><br>
<br>
<b><small>�������� ����</small></b><br>
<form action='runes_editor.php' method='get'>

<table>
<tr><td>��������:</td><td><? echo create_select("attr", $attributes, (int)$_GET['attr']); ?></td></tr>
<tr><td>��������:</td><td><input type=text name=value></td></tr>
<tr><td>&nbsp;</td><td><input type=submit value='��������'></td></tr>
</table>

<table>
<?

$res = f_MQuery("SELECT * FROM runes ORDER BY attr_id, value");
while ($arr = f_MFetch($res))
{
    echo "<tr><td><img src='../images/items/{$arr[image]}'></td><td><b>".$attributes[$arr['attr_id']]." +{$arr[value]}</b></td><td><a href=item_editor_mid.php?id={$arr[item_id]} target=_blank>������ �� ����</a> &nbsp; <a href='runes_editor.php?del=$arr[rune_id]'>�������</a></td></tr>";
}

?>
</table>

</form>

