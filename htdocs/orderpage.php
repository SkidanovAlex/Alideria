<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">



<?

include( "functions.php" );
include( "player.php" );
include( "clan.php" );
include_js( "js/skin.js" );
include_js( "js/skin2.js" );
include_js( "js/clans.php" );
include_js( "js/ii.js" );
include_js( "functions.js" );
include_js( "js/tooltips.php" );

f_MConnect( );

$clan_id = $_GET['id'];
$page_id = $_GET['page'];
settype( $clan_id, 'integer' );
settype( $page_id, 'integer' );

$res = f_MQuery( "SELECT * FROM clans WHERE clan_id = $clan_id" );
$arr = f_MFetch( $res );
if( !$arr ) die( "<i>Ордена, которого вы ищете, не существует.</i>" );

echo "<head><title>$arr[name]</title></head>";

echo "<center><br><table width=710 style='border:1px solid black' background=images/chat/chat_bg.gif><tr><td align=center><b>$arr[name]</b></td></tr></table>";

echo "<br><table width=710 style='border:1px solid black' background=images/chat/chat_bg.gif><tr>";
echo "<td width=33% align=center>Направленность: <b>".$orientations[$arr['orientation']]."</b></td>";
echo "<td width=33% align=center>Слава: <b>".$arr['glory']."</b></td>";
echo "<td width=33% align=center>Стихия: <b>".$elements[$arr['element']]."</b></td>";
echo "</tr></table>";

if( $arr['hascamp'] )
{
	echo "<br>";
	echo "<script>document.write(".render_camp( $clan_id ).");</script>";
}                 


$res = f_MQuery( "SELECT * FROM clan_pages WHERE clan_id=$arr[clan_id] AND ( title NOT LIKE '(Новая)' OR is_title != 0 ) ORDER BY page_id" );
if( $page_id < 0 || $page_id > f_MNum( $res ) )
	$page_id = 0;

$content = "";

echo "<br><br><table cellspacing=0 cellpadding=0 border=0 style='width:710px;height:40px;'><tr>";

$cur_id = 0;
$titles = Array( );
while( $arr = f_MFetch( $res ) )
{
	if( $cur_id == $page_id ) $content = $arr['text'];
	$titles[$cur_id] = $arr['title'];
	++ $cur_id;
	if( $cur_id == 1 ) ++ $cur_id;
}

$titles[1] = "Состав";

$n = $cur_id;
for( $cur_id = 0; $cur_id < $n; ++ $cur_id )
{
	if( $cur_id != $page_id ) $border = "border:1px solid black";
	else $border = "border-left:1px solid black;border-right:1px solid black;border-top:1px solid black";

	if( $cur_id != $page_id ) $titles[$cur_id] = "<a href=orderpage.php?id=$clan_id&page=$cur_id>".$titles[$cur_id]."</a>";
	echo "<td background='images/chat/chat_bg.gif' style='$border;width:100px;' align=center valign=middle>{$titles[$cur_id]}</td>";
}
echo "<td style='border-bottom:1px solid black'>&nbsp;</td>";
echo "</tr></table>";

if( $page_id == 1 ) // состав
{
	$ranks = Array( );
	$jobs = Array( 0 => "---" );
	$res = f_MQuery( "SELECT rank, name FROM clan_ranks WHERE clan_id=$clan_id" );
	while( $arr = f_MFetch( $res ) ) $ranks[$arr[0]] = $arr[1];
	$res = f_MQuery( "SELECT job, name FROM clan_jobs WHERE clan_id=$clan_id" );
	while( $arr = f_MFetch( $res ) ) $jobs[$arr[0]] = $arr[1];

	$res = f_MQuery( "SELECT * FROM player_clans WHERE clan_id=$clan_id ORDER BY rank DESC, job DESC" );


	$num = 1;
	$content = "<center><table><tr><td width=1>&nbsp;</td><td width=200 align=center><b>Игрок</b></td><td width=100 align=center><b>Звание</b></td><td width=100 align=center><b>Должность</b></td></tr><script src=js/clans.php></script><script src=js/ii.js></script><script>";
	while( $arr = f_MFetch( $res ) )
	{
		$rank = $ranks[$arr['rank']];
		$job = $jobs[$arr['job']];
 		$plr = new Player( $arr['player_id'] );

 		$ores = f_MQuery( "SELECT count(player_id) FROM online WHERE player_id!=172 AND player_id={$plr->player_id}" );
 		$oarr = f_MFetch( $ores );
    	if( $oarr[0] )
        	$nmprint="<font color=green title=OnLine>".$num."</font>";
    	else
  			$nmprint="<font color=darkred title=OffLine>".$num."</font>";

		
		$content .= "document.write( '<tr><td align=right><b>$nmprint. </b></td><td>' + ".$plr->Nick( )." + '</td><td align=center>$rank</td><td align=center>$job</td></tr>' );\n";
		++ $num;
	}
	$content .= "</script></table></center>";
}

$content = trim( $content );
if( strpos( $content, '(гильдии)' ) !== false )
{
	include_once( "guild.php" );
	$content = str_replace( '(гильдии)', guild_list( $clan_id ), $content );
}
if( $content == "" ) $content = "<center><i>Описание отсутствует</i></center>";

echo "<table background='images/chat/chat_bg.gif' style='border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;width:710px;height:100px;'><tr><td valign=top>";
echo "$content";
echo "</tr></tr></table>";

echo "</center>";

?>
