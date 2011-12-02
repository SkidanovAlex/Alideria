<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">

<?

include( "functions.php" );
include( "player.php" );
include_js( "js/skin.js" );
include_js( "js/clans.php" );
include_js( "js/ii.js" );
include_once( "guild.php" );

f_MConnect( );

echo "<head><title> Мастера Алидерии </title></head>";
echo "<center><br><table width=600 style='border:1px solid black' background=images/chat/chat_bg.gif><tr><td align=center>";
echo " <b>Славные труженики Алидерии</b></td></tr></table>";


$page_id = $_GET['page'];
$cur_id = 101;
settype( $page_id, 'integer' );

// заполнение $tit[]
$tit = Array( );
$tit [101]="Рыбаков";
$tit [102]="Собирателей";
$tit [103]="Старателей";
$tit [104]="Кузнецов";
$tit [105]="Ювелиров";
$tit [106]="Алхимиков";
$tit [107]="Стеклодувов";
$tit [108]="Охотников";

//********************* Начало шапочек таблицы :Ъ
echo "<br><br><table cellspacing=0 cellpadding=0 border=0 style='width:600px;height:40px;'><tr>";
for( $cur_id = 101; $cur_id <= 108; ++ $cur_id )
{
	if( $cur_id != $page_id ) $border = "border:1px solid black";
	else $border = "border-left:1px solid black;border-right:1px solid black;border-top:1px solid black";

	if( $cur_id != $page_id )
  {
  $tit[$cur_id] = "<a href=test_table.php?page=$cur_id>".$tit[$cur_id]."</a>";

  }
echo "<td background='images/chat/chat_bg.gif' style='$border;width:100px;' align=center valign=middle>{$tit[$cur_id]}</td>";
echo "<td style='border-bottom:1px solid black'>&nbsp;<br><br>";



}
echo "</tr>";
echo "</table>";

//**************** Дальше вывод таблички гильдии текущей страницы************************
echo "<table>";
      $guild_num=$page_id;
			$gres = f_MQuery( "SELECT * FROM player_guilds WHERE guild_id =$guild_num ORDER BY rank DESC,rating DESC" );
			if( f_MNum( $gres ) )
			{
      	while( $garr = f_MFetch( $gres ) )
				{
$pl = f_MQuery( "SELECT login FROM characters  WHERE  player_id=".$garr ['player_id'] );
$log = f_MFetch( $pl );
 			$ores = f_MQuery( "SELECT count(player_id) FROM online WHERE player_id=".$garr ['player_id'] );
 			$oarr = f_MFetch( $ores );
			if( $oarr[0] )
        {$nmprint="<font color=green title=OnLine>".$log['login']."</font>";}
	   	else
        {$nmprint="<font color=darkred title=OffLine>".$log['login']."</font>";}
          echo "<tr><td>".$nmprint." - ". $garr ['rank']." - ".$garr ['rating']."</td></tr>";
				}
				echo "</table>";
			}
       else
       {
        echo "<tr><td><center><b>В данной гильдии никто не состоит...</b></center></td></tr>";
       }


?>
