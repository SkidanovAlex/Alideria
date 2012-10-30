<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['orden_id'] ) )
{
	$clan_id = (int)$HTTP_GET_VARS['orden_id'];
	if($clan_id > 0)
	{
		$res = f_MQuery("SELECT player_id FROM characters WHERE clan_id=".$clan_id);
		while($arr = f_MFetch($res))
			f_MQuery("INSERT IGNORE INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", ".(10000+$clan_id).", -1)");
	}
}

?>

<a href=index.php>На главную</a><br>
<b>Added smile for orden:&nbsp;</b><br>
<form action=admin_add_orden_smile.php method=get>
<select name=orden_id>
<option selected value=-1>Select orden</option>
<?
$res = f_MQuery("SELECT clan_id, name FROM clans");
while($arr = f_MFetch($res))
    echo "<option value=".$arr[0].">".$arr[1]."</option>";
?>
</select>
&nbsp;<input type=submit class=s_btn value=Check>
</form>

<?

f_MClose( );

?>

