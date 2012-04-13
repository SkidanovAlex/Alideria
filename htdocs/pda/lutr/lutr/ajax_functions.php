<?
include_once("functions.php");

f_MConnect();

if (isset($_GET['show_tree']))
{
//	echo "alert('show tree');";
	$res = f_MQuery("SELECT * FROM owners");
	while ($arr = f_MFetch($res))
	{
	//	$arr[1] = iconv("UTF-8", "cp1251", $arr[1]);
		echo "addZak($arr[0], '$arr[2]', $arr[1]);\n";
	}
	$res = f_MQuery("SELECT * FROM places");
	while ($arr = f_MFetch($res))
		echo "addPlace($arr[0], '$arr[1]');\n";
	$res = f_MQuery("SELECT project_id, project_name, place_id, kust_number, skv_number FROM projects");
	while ($arr = f_MFetch($res))
		echo "addProject(pr_id, pr_name, pl_id, kust, skv);\n";
	echo "showZaks();\n";
	die();
}

if (isset($_GET['ownerName'])&&isset($_GET['owner_id_up']))
{
	f_MQuery("INSERT INTO owners (up_owner_id, name) VALUES (".$_GET['owner_id_up'].", '".$_GET['ownerName']."')");
	die();
}
?>