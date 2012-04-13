<?

include_once( "functions.php" );
include_once( "player.php" );
include_once( "arrays.php" );
include_once( "quest_race.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$mid_php = 1;	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

print( "<div id=moo name=moo>" );

$id = $HTTP_GET_VARS[id];
settype( $id, 'integer' );

if( $id == -1000 )
{
	echo "<b>Задание от Фавна</b><br><br>";
	echo getRemainingActions ( $player->player_id );
}
else if( $id < 0 )
{
	$id = - $id;
	$res = f_MQuery( "SELECT * FROM player_government_work WHERE player_id={$player->player_id} AND guild_id=$id" );
	$arr = f_MFetch( $res );
	if( $arr )
	{
    	$rres = f_MQuery( "SELECT * FROM recipes WHERE recipe_id=$arr[recipe_id]" );
    	$rarr = f_MFetch( $rres );
    	echo "Рецепт: <a href=help.php?id=1015&recipe_id=$rarr[recipe_id] target=_blank>$rarr[name]</a><br>";
    	echo "Количество: <b>$arr[completed]/$arr[number]</b><br>";
    	echo "Компенсация: <img src=images/money.gif width=11 height=11>&nbsp;<b>$arr[prize]</b><br>";
    	if( $arr['completed'] == $arr['number'] )
	    	echo "<br>Вернитесь в зал гильдий, чтобы получить компенсацию за заказ";
    } else echo "Нет такого заказа";
}
else
{
    function showMonstersStatus($part, $opt)
    {
        global $player;
    	$ires = f_MQuery( "SELECT mobs.name, player_quest_monsters.* FROM mobs, player_quest_monsters WHERE quest_part_id = $part AND player_id={$player->player_id} AND mobs.mob_id = player_quest_monsters.mob_id $opt" );
        if (f_MNum($ires)) echo "<b>Убить:</b>";
    	while( $iarr = f_MFetch( $ires ) )
    	{
    		$num = $iarr['target'] - $iarr['togo'];
    		print( "<li> [$num/$iarr[target]]&nbsp;&nbsp;$iarr[name]<br>" );
    	}
    }
    function showMineStatus($part, $opt)
    {
        global $player;
    	$ires = f_MQuery( "SELECT items.name, player_quest_mine.* FROM items, player_quest_mine WHERE quest_part_id = $part AND player_id={$player->player_id} AND items.item_id = player_quest_mine.item_id $opt" );
        if (f_MNum($ires)) echo "<b>Добыть:</b>";
    	while( $iarr = f_MFetch( $ires ) )
    	{
    		$num = $iarr['target'] - $iarr['togo'];
    		print( "<li> [$num/$iarr[target]]&nbsp;&nbsp;$iarr[name]<br>" );
    	}
    }

    $res = f_MQuery( "SELECT * FROM player_quests WHERE player_id = {$player->player_id} AND quest_id = $id" );
    if( !mysql_num_rows( $res ) ) return;

    $qres = f_MQuery( "SELECT * FROM quests WHERE quest_id = $id" );
    $qarr = f_MFetch( $qres );

    print( "<b>$qarr[name]</b><br><br>" );

    $k = 1;
    $pres = f_MQuery( "SELECT quest_parts.* FROM quest_parts, player_quest_parts WHERE quest_parts.quest_part_id = player_quest_parts.quest_part_id AND player_id = {$player->player_id} AND quest_id = $id ORDER BY quest_part_id" );
    while( $parr = f_MFetch( $pres ) )
    {
    	for( $ii = 0; $ii < 100; ++ $ii )
    	{
    		$t = strpos( $parr['text'], '{' );
    		if( $t === false ) break;
    		$q = strpos( $parr['text'], '}', $t );
    		if( $q === false ) break;
    		$parr['text'] = substr( $parr['text'], 0, $t ) . $player->GetQuestValue( (int)substr( $parr['text'], $t + 1, $q - $t - 1 ) ).substr( $parr['text'], $q + 1 );
    	}
    	print( "<b>$k. </b>$parr[text]<br><br>" );
        showMonstersStatus($parr['quest_part_id'], "");
        showMineStatus($parr['quest_part_id'], "");
    	$larr = $parr;
    	++ $k;
    }

    if( $larr )
    {
    	$ires = f_MQuery( "SELECT items.name, quest_item_reqs.* FROM items, quest_item_reqs WHERE quest_part_id = $larr[quest_part_id] AND items.item_id = quest_item_reqs.item_id" );
        if (f_MNum($ires)) echo "<b>Найти:</b>";
    	while( $iarr = f_MFetch( $ires ) )
    	{
    		$num = $player->NumberItems( $iarr[item_id] );
    		print( "<li> [$num/$iarr[number]]&nbsp;&nbsp;$iarr[name]<br>" );
    	}
    }
}

print( "</div>" );

?>

<script>

parent.document.getElementById( 'qdescr' ).innerHTML = document.getElementById( 'moo' ).innerHTML;

</script>
