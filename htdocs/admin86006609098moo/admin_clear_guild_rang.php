<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?
/*
include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../guild.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "<a href=index.php>На главную</a><br><br>";
$i=0;
echo $
/*
$res = f_MQuery("SELECT * FROM player_guilds");
$arr = mysql_fetch_array($res);
$count=0;
while ($arr)
{
    $r1 = $arr[3]; $r2 = $arr[4];
    $prof_exp = $rank_prices_all[$r1] + $rank_prices_all[$r2];
    //f_MQuery("UPDATE player_guilds SET rank = 0, rating = 0 WHERE player_id = $arr[0] AND guild_id = $arr[1]" );
    //f_MQuery("UPDATE characters SET prof_exp = prof_exp + $prof_exp WHERE player_id = $arr[0]");
    $arr = mysql_fetch_array($res);
    $count++;
}
*/
f_MClose( );

echo "Все прошло успешно. Наверное...<br>";
echo "Всего обработано $count строк таблицы player_guilds";
*/
?>
