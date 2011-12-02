<?

include_once( 'items.php' );
include_once('func_dun.php');
include_js( 'locations/dungeons/func.js' );
include_js( 'js/skin2.js' );
include_js( 'js/timer2.js' );

if( !isset( $mid_php ) ) die( );

?>

<?
global $player;

$res = f_MQuery("SELECT * FROM dungeon_players as p WHERE p.player_id=$player->player_id");
$arr_cur = f_MFetch($res);
if (!$arr_cur)
	RaiseError("Неверное местонахождение игрока в данже");


?>

<table>
<tr valign=top>
<td>
<script>FLUl();FUlt();</script>
<?
	echo showMap($arr_cur['group_number'], $arr_cur['dungeon_type'], $arr_cur['cell_num']);
?>
<script>FL();FLL();</script>
</td>
<td valign=top style='width:200px;height:300px;'>
<script>FLUl();</script><center>Игроки здесь</center><br>
<? echo showPlayers($arr_cur['group_number']); ?>
<script>FLL();</script>
</td>
</tr>
</table>

<script>

function refLock()
{
	document.location = "game.php";
}
//setTimeout( 'refLock( )', 10000 );

</script>