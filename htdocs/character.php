<?

include_once( 'attrib_functions.php' );
include_once( 'profession_list.php' );

if( !$mid_php )
	die( );
	
include_js( 'js/cc.js' );
	
print( "<center><table><tr><td><script>FLUc();</script><table><tr><td height=100% vAlign=top width=230><script>FUlt();</script><div name=a2 id=a2>" );

$player->ShowSecondaryAttributes( );
//$player->ShowAttributes( 1 );

print( "</div><script>FL();</script></td><td rowspan=3 height=100% vAlign=top><script>FUlt();</script><div id=a1>" );

$player->ShowBattleAttributes( );

print( "</div><script>FL();</script></td></tr>" );

echo "<tr><td height=100%><script>FUlt();</script><div id=a5 name=a5>";

$player->ShowGlobalAttributes( );

print( "</div><script>FL();</script></td></tr><tr><td height=100%><script>FUlt();</script><div id=a3 name=a3>" );

$player->ShowPrimaryAttributes( );

echo "</div><script>FL();</script></td></tr>";

/* $res = f_MQuery( "SELECT * FROM player_profs WHERE player_id = $player->player_id" );
$str = "</table>";

$profs_num = 0;
while( $arr = f_MFetch( $res ) )
{
	$str = "<tr height=24><td height=24 align=right>{$professions[$arr[profession_id]]}</td></tr>".$str;
	++ $profs_num;
}
if( $profs_num )
{
	$str = "<tr height=24><td height=24 align=right>Боевой Опыт</td></tr>".$str;

	$str = "<table cellspacing=0 cellpadding=0 border=0><tr height=8><td height=8><ing width=0 height=8></td></td>".$str;

	print( "$str</td><td valign=top><b>Умения</b><br><img src=profs_img.php?player_id={$player->player_id} alt=Умения></td></tr>" );
} */

print( "</table><script>FLL();</script></td></tr></table>" );

if( $player->level == 1 )
{
	include_once( 'player_noobs.php' );
	echo "<script>";
	PingNoob( 4 );
	echo "</script>";
}

?>

<iframe name=ref id=ref width=0 height=0></iframe>
