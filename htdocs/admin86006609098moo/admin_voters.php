<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "<a href=index.php>На главную</a><br><br>";

?>
<table>
<form action='admin_voters.php' method=get>
<tr><td>ID темы:</td><td><input type=text name=thread_id></td></tr>
<tr><td><input type=submit value=Ok></td></tr>
</form>
</table>

<?

if( isset( $HTTP_GET_VARS['thread_id'] ) )
{
	$thread_id = (int)$HTTP_GET_VARS['thread_id'];
	$res = f_MQuery("SELECT entry_id, txt FROM forum_votes WHERE thread_id=$thread_id");
	$vote = Array();
	echo "Смотрим голоса";
	echo "<table border=1>";
	echo "<tr>";
	while ($arr = f_MFetch($res))
	{
		echo "<td valign=top><b>$arr[1]</b><br>";
		$res1 = f_MQuery("SELECT c.login FROM characters as c, forum_voters as f WHERE f.thread_id=$thread_id AND f.player_id=c.player_id AND f.entry_id=$arr[0]");
		while ($arr1 = f_MFetch($res1))
		{
			echo "$arr1[0]<br>";
		}
		echo "</td>";
	}
	echo "</tr></table>";
	
}


f_MClose( );

?>

