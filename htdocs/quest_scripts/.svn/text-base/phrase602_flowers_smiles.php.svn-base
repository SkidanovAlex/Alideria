<?

include_once( 'smiles_list.php' );

if( !$mid_php ) die( );

$item_ids = Array (
	15,
	12,
	13,
	11,
	14
);

$nm = Array (
	'ромашек',
	"маков",
	"лютиков",
	"васильков",
	"нарциссов"
);

if( isset( $_GET['chosen'] ) && !$player->HasTrigger( 216 ) )
{
	$id = (int)$_GET['chosen'];
	if( $id >= 0 && $id < 5 && $player->DropItems( $item_ids[$id], 200 ) || $player->player_id == 173 )
	{
		$sid = $id + 4;
		f_MQuery( "INSERT INTO paid_smiles ( player_id, set_id, expires ) VALUES ( {$player->player_id}, $sid, 2147483647 );" );
		$player->SetTrigger( 216 );
		$player->AddToLogPost( $item_ids[$id], - 200, 20 );
	} else echo "<font color=darkred>У вас не хватает цветов</font><br>";
}

if( !$player->HasTrigger( 216 ) )
{
	echo "<li><a href=game.php?phrase=1248>Уйти</a><br>";

    echo "<br><i>Торговец отдает максимум одного колобка в одни руки, будьте внимательны</i><br>";

    for( $i = 0; $i < 5; ++ $i )
    {
    	$si = $i + 4;
    	echo "<br>За <b>200 {$nm[$i]}</b> я готов навсегда подарить тебе вот этого колобка: <img src=images/smiles/{$vsmiles[$si][0]}.gif><br><li><a href='javascript:buy($i)'>Купить</a><br>";
    }
}
else
{
	echo "<b>Шамаханский торговец: </b>Вах, спасипа типе ".(($player->sex)?"добрая девущка":"добрий чилавек").". Что би ми делали без тебя в этот праздник. От имени всех женщин благодарю тепя...<br>Как бы это странно не звучало...<br><br>";
	echo "<li><a href=game.php?phrase=1248>Уйти</a><br>";

}

?>
<script>
function buy( id )
{
	if( confirm( 'Купить выбранный смайлик? Не забывайте, вы не сможете купить никакой другой смайлик у торговца после этого!' ) )
		location.href='game.php?chosen=' + id;
}
</script>