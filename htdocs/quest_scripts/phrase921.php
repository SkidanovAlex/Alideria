<?
if( !$mid_php ) die( );

global $player;

if( isset( $_GET['chosen'] ) )
{
	$id = (int)$_GET['chosen'];
	if( $id >= 0 && $id < 5 )
	{
		if ($id==0) {$sid=151;$iid=76207;}
		if ($id==1) {$sid=50;$iid=76208;}
		if ($id==2) {$sid=83;$iid=76209;}
		if ($id==3) {$sid=22;$iid=76210;}
		if ($id==4) {$sid=129;$iid=76211;}
		
		if (f_MValue("SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=$sid")!=-1 && $player->NumberItems($iid)>=50)
		{
			$player->AddToLogPost( $iid, -50, 980);
			$player->DropItems($iid, 50);
			f_MQuery( "INSERT INTO paid_smiles ( player_id, set_id, expires ) VALUES ( {$player->player_id}, $sid, -1 );" );
			$numSm = f_MValue("SELECT COUNT(*) FROM paid_smiles WHERE player_id={$player->player_id} AND set_id>=10 AND expires=-1");
			$lck = 0;
			if ($numSm >= 150) $lck=15;
			elseif ($numSm >= 100) $lck=10;
			elseif ($numSm >= 60) $lck=7;
			elseif ($numSm >=40) $lck=5;
			elseif ($numSm >=25) $lck=3;
			elseif ($numSm >=10) $lck=2;
			elseif ($numSm >=5) $lck=1;
			$player->RemoveEffect(30, true);
			if ($lck)
				$player->AddEffect(30, 0, "Любитель улыбок", "Всего vip-смайлов: ".$numSm, "../../images/smiles/ura.gif", "13:".$lck.".", -1);
		}
	}
}

echo "<i>Торговец задумался на минутку...</i><br><br>";

echo "<b>Шамаханский торговец:</b> Да не вопрос! Могу за 50 игрушек продать тебе новогодний смайлик, если у тебя еще нет такого. Выбирай.<br><br>";

echo "<a href=game.php?phrase=1922>Уйти</a><br><br>";

echo "<table border=0>";

echo "<tr><td><img src='/images/smiles/sharik.gif'></td><td>&nbsp; всего за <b>50</b> &nbsp;</td><td><img src='/images/items/ny2011/ny_ball.png' style='width: 50px; height: 50px; border: 0px;'></td>";
if (f_MValue("SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=151")!=-1 && $player->NumberItems(76207)>=50)
echo "<td><a href='javascript:buy(0)'>Купить</a></td>";
echo "</tr>";
echo "<tr><td><img src='/images/smiles/snegurka.gif'></td><td>&nbsp; всего за <b>50</b> &nbsp;</td><td><img src='/images/items/ny2011/snowflake1.png' style='width: 50px; height: 50px; border: 0px;'></td>";
if (f_MValue("SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=50")!=-1 && $player->NumberItems(76208)>=50)
echo "<td><a href='javascript:buy(1)'>Купить</a></td>";
echo "</tr>";
echo "<tr><td><img src='/images/smiles/dedulya.gif'></td><td>&nbsp; всего за <b>50</b> &nbsp;</td><td><img src='/images/items/ny2011/ny_bell.png' style='width: 50px; height: 50px; border: 0px;'></td>";
if (f_MValue("SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=83")!=-1 && $player->NumberItems(76209)>=50)
echo "<td><a href='javascript:buy(2)'>Купить</a></td>";
echo "</tr>";
echo "<tr><td><img src='/images/smiles/dedmoroz.gif'></td><td>&nbsp; всего за <b>50</b> &nbsp;</td><td><img src='/images/items/ny2011/ny_cone.png' style='width: 50px; height: 50px; border: 0px;'></td>";
if (f_MValue("SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=22")!=-1 && $player->NumberItems(76210)>=50)
echo "<td><a href='javascript:buy(3)'>Купить</a></td>";
echo "</tr>";
echo "<tr><td><img src='/images/smiles/snejok.gif'></td><td>&nbsp; всего за <b>50</b> &nbsp;</td><td><img src='/images/items/ny2011/ny_star.png' style='width: 50px; height: 50px; border: 0px;'></td>";
if (f_MValue("SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=129")!=-1 && $player->NumberItems(76211)>=50)
echo "<td><a href='javascript:buy(4)'>Купить</a></td>";
echo "</tr>";

echo "</table>";


?>
<script>
function buy( id )
{
		location.href='game.php?chosen=' + id;
}
</script>