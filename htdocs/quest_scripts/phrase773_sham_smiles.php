<?
if (!$mid_php) die();

include_once( 'smiles_list.php' );

if( isset( $_GET['chosen'] ) )
{
	$id = (int)$_GET['chosen'];
	$v_62 = $player->GetValue(62);
	if( $id >= 0 && $id < 6 && $v_62 < 4 )
	{
		$sid = $id + 75;
		if (!f_MValue("SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=$sid"))
		{
			f_MQuery( "INSERT INTO paid_smiles ( player_id, set_id, expires ) VALUES ( {$player->player_id}, $sid, -1 );" );
			$player->SetValue(62, $v_62+1);
			$numSm = f_MValue("SELECT COUNT(*) FROM paid_smiles WHERE player_id={$player->player_id} AND set_id>=10 AND expires=-1");
			$lck = 0;
			if ($numSm >= 60) $lck=7;
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

echo "<a href=game.php?phrase=1582>Уйти</a><br><br>";

if ($player->GetValue(62)>=4)
	echo "Вы уже выбрали четыре смайлика. Остальные убежали.";
else
{
	echo "Вы можете выбрать еще <b>".(4-$player->GetValue(62))."</b> смайлика.<br><br>";
	$issmile = 6;
	for ($i=0; $i<6; $i++)
	{
		$si = $i + 75;
		if (f_MValue("SELECT expires FROM paid_smiles WHERE set_id=".$si." AND player_id=".$player->player_id) != -1)
	    		echo "<a href='javascript:buy($i)'><img src=images/smiles/{$vsmiles[$si][0]}.gif></a>&nbsp;";
		else
			$issmile--;
	}
	if (!$issmile)
		echo "У меня нет для тебя ничего нового.";
}

?>
<script>
function buy( id )
{
	if( confirm( 'Выбираете этого колобка?' ) )
		location.href='game.php?chosen=' + id;
}
</script>