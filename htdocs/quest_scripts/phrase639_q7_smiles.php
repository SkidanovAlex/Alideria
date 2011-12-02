<?

include_once( 'smiles_list.php' );

if( !$mid_php ) die( );

if( isset( $_GET['chosen'] ) && !$player->HasTrigger( 235 ) )
{
	$id = (int)$_GET['chosen'];
	if( $id >= 0 && $id < 6 && $player->DropItems( 5, 200 ) || $player->player_id == 173 )
	{
		$sid = $id + 4;
		f_MQuery( "INSERT INTO paid_smiles ( player_id, set_id, expires ) VALUES ( {$player->player_id}, $sid, 2147483647 );" );
		$player->SetTrigger( 235 );
		$player->AddToLogPost( 5, - 200, 20 );
	} else echo "<font color=darkred>У вас не хватает хвостиков</font><br>";
}

if( !$player->HasTrigger( 235 ) )
{
	echo "<li><a href=game.php?phrase=1304>Уйти</a><br>";

    echo "<br><i>БезПонтов отдает максимум одного колобка в одни руки, будьте внимательны</i><br>";

    for( $i = 0; $i < 6; ++ $i )
    {
    	$si = $i + 4;
    	echo "<br>За <b>200 крысиных хвостиков</b> я готов навсегда подарить тебе вот этого колобка: <img src=images/smiles/{$vsmiles[$si][0]}.gif><br><li><a href='javascript:buy($i)'>Купить</a><br>";
    }
}
else
{
	echo "<b>БезПонтов: </b>Один колобок в одни руки. Ты уже забрал своего.<br><br>";
	echo "<li><a href=game.php?phrase=1304>Уйти</a><br>";

}

?>
<script>
function buy( id )
{
	if( confirm( 'Купить выбранный смайлик? Не забывайте, вы не сможете купить никакой другой смайлик у БезПонтов после этого!' ) )
		location.href='game.php?chosen=' + id;
}
</script>