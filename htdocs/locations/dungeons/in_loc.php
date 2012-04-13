<?

include_once( 'items.php' );
include_once('func_dun.php');
//include( 'locations/dungeons/func_js.php' );
include_js( 'js/skin2.js' );
include_js( 'js/timer2.js' );
include_js( 'js/location_items.js' );
echo "<script src=locations/dungeons/func_js.php></script>";

if( !isset( $mid_php ) ) die( );

?>

<?
global $player;
//$no_rest = true;

$res = f_MQuery("SELECT * FROM dungeon_players as p WHERE p.player_id=$player->player_id");
$arr_cur = f_MFetch($res);
if (!$arr_cur)
	RaiseError("Неверное местонахождение игрока в данже");


?>

<table>
<tr valign=top>
<td>
<script>FLUc();FUct();</script>
<?
//	echo showMap($arr_cur['group_number'], $arr_cur['dungeon_type'], $arr_cur['cell_num']);
?>
<table cellspacing=0 cellpadding=0 border=0>
<?
for ($i=0;$i<3;$i++)
{
	echo "<tr>";
	for ($j=1; $j<=3; $j++)
	{
		echo "<td align=center valign=middle style='width:90px;height:90px;'>";
		echo "<div onclick='movingTo(".($j+$i*3).");' id='cell_".($j+$i*3)."'>&nbsp;</div>";
		echo "</td>";
	}
	echo "</tr>";
}
?>
</table>
<script>FL();FLL();</script>
</td>
<td valign=top style='width:216px;height:292;'>
<table cellspacing=0 cellpadding=0 border=0 height=100% width=100%><tr valign=top><td>
<script>FLUl();</script><center><a title='Обновить' style='cursor: pointer;' onclick='refLock();'>Игроки здесь</a></center><br>
<div id=player_in_dun>&nbsp;</div>
<script>FLL();</script>
</td></tr><tr valign=bottom><td height=86>
<table cellspacing=0 cellpadding=0 border=0 width=216 height=86 background="images/backgrounds/3.jpg"><tr><td align=middle><center>Ваши действия:</center><br>
<div id=you_doing>&nbsp;</div>
</td></tr></table>
</td></tr></table>
</td>
</tr>
<tr><td><div id=location_items>&nbsp;</div></td></tr>
</table>
<script>setEvns();</script>
<script>

getYouStatus();

</script>