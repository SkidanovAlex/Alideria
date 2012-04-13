<?
if( !$mid_php ) die( );

include('../../functions.php');
include('../../player.php');
include('func.php');

if ($player->Rank() == 1)
{

if (!$player->HasTrigger(3000))
	die();
else
{

echo "Лучше уйди. Доставать вас я отсюда не буду. © Reincarnation<hr>";

if ($_GET['create'])
{
	if (canCreateBet($player->player_id, 1))
	{
		$grnum = createBet($player->player_id, 1, $player->level);
		$player->syst2('Вы создали группу номер '.$grnum);
		$player->SetRegime(118);
		die("<script>location.href=\"game.php\"</script>");
	}
	else
		$player->syst2('Не удалось создать заявку.');
}

if ($_GET['joinTo'])
{
	$grnum = $_GET['joinTo'];
	$res_join = joinToBet($player->player_id, $grnum, 1, $player->level);
	switch ($res_join)
	{
		case 1: { $player->syst2("Нет места в группе."); break;}
		case 2: { $player->syst2("Вы не подходите по уровню.".$player->level); break;}
		default: { $player->syst2("Вы успешно присоединились к группе номер ".$grnum."."); $player->SetRegime(118); die("<script>location.href=\"game.php\"</script>"); break;}
	}
}

if ($_GET['leave'] && $_GET['group'])
{
	$leave_pl = $_GET['leave'];
	$group =  $_GET['group'];
	LeaveFromBet($leave_pl, $group);
	die("<script>location.href=\"game.php\"</script>");
}

if ($_GET['start'])
{
	$res = f_MQuery("SELECT * FROM dungeons_groups WHERE leader_id={$player->player_id}");
	if ($arr = f_MFetch($res))
	{
		startDungeon($player->player_id, $arr[1]);
	}
}

?>

<div id='ShowBets' style='position: relative; top: 0px; left: 0px;width: 600px;'>
</div>

<?
	$sB = showBets($player->player_id, 1);
	echo "<script>ShowBets.innerHTML='".$sB."';</script>";
	
//	echo "<script>ShowBets.innerHTML = '<a href=\"game.php?create=1\">Создать заявку</a><br><hr>';</script><br>";

}
}
?>