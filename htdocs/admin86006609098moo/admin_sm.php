<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?
// поменяли немного смайлы

include_once( '../functions.php' );
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );
/*
$res = f_MQuery("SELECT * FROM paid_smiles WHERE set_id>=0 AND set_id <=3");
while ($arr = f_MFetch($res))
{
	if ($arr[1] == 0)
	{
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=60")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 60, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=61")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 61, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=62")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 62, ".$arr[2].")");
	}
	if ($arr[1] == 1)
	{
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=63")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 63, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=64")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 64, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=65")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 65, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=66")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 66, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=67")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 67, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=68")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 68, ".$arr[2].")");
	}
	if ($arr[1] == 2)
	{
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=69")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 69, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=70")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 70, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=71")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 71, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=72")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 72, ".$arr[2].")");
	}
	if ($arr[1] == 3)
	{
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=73")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 73, ".$arr[2].")");
		if (!f_MNum(f_MQuery("SELECT expires FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=74")))
			f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES (".$arr[0].", 74, ".$arr[2].")");
	}

	f_MQuery("DELETE FROM paid_smiles WHERE player_id=".$arr[0]." AND set_id=".$arr[1]);
}
*/
f_MClose( );

?>