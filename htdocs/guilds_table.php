<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">

<?

include( "functions.php" );
include( "player.php" );
include_js( "js/clans.php" );
include_js( "js/ii.js" );
include_js( "js/skin.js" );
include_js( "js/clans.php" );
include_js( "js/ii.js" );
include_once( "guild.php" );

f_MConnect( );

echo "<head><title> Мастера Алидерии </title></head>";
echo "<center><br><table width=600 style='border:1px solid black' background=images/chat/chat_bg.gif><tr><td align=center>";
echo " <b>Славные труженики Алидерии</b></td></tr></table>";

$page_id = (int)$_GET['page'];
if ($page_id<101 || $page_id>109) $page_id=101;
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
$tit [109]="Портных";

//********************* Начало шапочек таблицы :Ъ
echo "<br><br><table cellspacing=0 cellpadding=0 border=0 style='width:600px;height:40px;'><tr>";
for( $cur_id = 101; $cur_id <= 109; ++ $cur_id )
{
	if( $cur_id != $page_id ) $border = "border:1px solid black";
	else $border = "border-left:1px solid black;border-right:1px solid black;border-top:1px solid black";

	if( $cur_id != $page_id )
  {
  $tit[$cur_id] = "<a href=guilds_table.php?page=$cur_id>".$tit[$cur_id]."</a>";

  }
echo "<td background='images/chat/chat_bg.gif' style='$border;width:100px;' align=center valign=middle>{$tit[$cur_id]}</td>";
echo "<td style='border-bottom:1px solid black'>&nbsp;<br><br>";



}
echo "</tr>";
echo "</table>";

//**************** Дальше вывод таблички гильдии текущей страницы************************
echo "<table width=600 border=0 style='border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black' background=images/chat/chat_bg.gif>";
      $guild_num=$page_id;
      $nm = 1;
			$gres = f_MQuery( "SELECT player_guilds.*, 0 as arg FROM player_guilds WHERE guild_id =$guild_num AND player_id IN ( SELECT player_id FROM online ) AND player_id NOT IN( 173, 172, 174, 3264, 1296 ) UNION ALL SELECT player_guilds.*, 1 as arg FROM player_guilds WHERE guild_id =$guild_num AND player_id NOT IN ( SELECT player_id FROM online ) AND player_id NOT IN( 173, 172, 174, 3264, 1296 ) ORDER BY arg, rank DESC,rating DESC LIMIT 0 , 50 " );
			if( f_MNum( $gres ) )
			{ echo "<tr>
      <td><b>#</b></td>
      <td><b>Ник</b></td>
      <td><b><center>Ранг</center></b></td>
      <td><b><center>Рейтинг</center></b></td>
      <td><b><center>Локация</center></b></td>
      </tr>";
      	while( $garr = f_MFetch( $gres ) )
				{
$pl = f_MQuery( "SELECT login FROM characters  WHERE  player_id=".$garr ['player_id'] );
$log = f_MFetch( $pl );
 			$ores = f_MQuery( "SELECT count(player_id) FROM online WHERE player_id=".$garr ['player_id'] );
 			$oarr = f_MFetch( $ores );

      if( $oarr[0] ) // проверка на онлайн
        {$name_col="<font color=green title=OnLine>".$nm.")</font>";}
	   	else
        {$name_col="<font color=darkred title=OffLine>".$nm.")</font>";}

          echo "<tr><td>".$name_col."</td>";

        	$player = new Player( $garr['player_id'] );
         echo "<td ><script>document.write( ".$player->Nick()." );</script></td>";
          echo "<td><center>".$garr ['rank']."</center></td><td><center>".$garr ['rating']."</center></td>";
          echo "<td><center>";
          $player = new Player($garr ['player_id']);
          echo $loc_names[$player->location];
          echo "</center></td></tr>";

          $nm++;
				}
				echo "</table>";
			}
       else
       {
        echo "<tr><td><center><b>В данной гильдии никто не состоит...</b></center></td></tr>";
       }
?>
