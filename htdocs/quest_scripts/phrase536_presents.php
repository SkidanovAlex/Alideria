<?

if( !$mid_php ) die( );

if( isset( $_POST['v1'] ) )
{
	$pids = array();
	$ok = true;
	for( $i = 1; $i <= 3; ++ $i )
	{
	
		$nme = f_MEscape(htmlspecialchars(trim($_POST['v' . $i]),ENT_QUOTES)); 

		$val = f_MValue( "SELECT player_id FROM characters WHERE login='". $nme ."';" );
		if( !$val )
		{
			$ok = false;
			echo "<font color=darkred><b>Игрока с именем ". $nme ." не существует</b></font><br>";
		}
		else $pids[$i] = $val;
	}
	if( $ok )
	{
		for( $i = 1; $i <= 3; ++ $i )
		{
    		$plr = new Player( $pids[$i] );
    		$tm = time( ) + 15 * 24 * 3600;
         $txt = f_MEscape(htmlspecialchars(substr($_POST['q' . $i],0,2500),ENT_QUOTES));
    		f_MQuery( "INSERT INTO player_presents ( player_id, img, txt, author, deadline ) VALUES ( {$plr->player_id}, 'p$i.gif', '". $txt ."', '{$player->login}', $tm )" );
    		$plr->syst3( "Персонаж {$player->login} подарил вам подарок" );
		}
		f_MQuery( "DELETE FROM player_triggers WHERE player_id={$player->player_id} AND trigger_id=86" );
		f_MQuery( "DELETE FROM player_talks WHERE player_id={$player->player_id}" );
		$player->SetRegime( 0 );
		die( "<script>location.href='game.php';</script>" );
	}
}

echo "<form action=game.php method=post><center><table><tr><td><script>FLUl();</script><table><tr>";

for ($i = 1; $i <= 3; ++ $i)
{
	echo "<td width=170 height=200><script>FUcm();</script>";
	echo "<img width=100 height=100 src=images/presents/p$i.gif><br><input type=text name=v$i class=m_btn><br><textarea name=q$i cols=18 rows=4 class=te_btn>С Новым Годом!!!</textarea>";
	echo "<script>FL();</script></td>";
}

echo "</tr><tr><td colspan=3 align=center><input class=n_btn type=submit value=Подарить></td></tr></table><script>FLL();</script></td></tr></table></center>";
echo "</form>";

?>
