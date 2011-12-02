<?

if( !$mid_php ) die( );

$fnames = Array (
	"valentine1.gif",
	"valentine2.gif",
	"valentine5.gif",
	"valentine3.gif",
	"valentine4.gif"
);

if( array_key_exists('nm', $_POST) &&  isset( $_POST['nm'] ) )
{
	$pid = (int)$_POST['pid'];
	if ($pid < 0 || $pid >= 5) RaiseError( "Неизвестный ID подарка $pid у лавочника" );

	$nme = f_MEscape(htmlspecialchars($_POST['nm'],ENT_QUOTES)); 
	$txt = f_MEscape(htmlspecialchars($_POST['txt'],ENT_QUOTES));
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$nme'" );
	$arr = f_MFetch( $res );
	if( $arr )
	{
		if( $pid < 3 && $player->SpendMoney( 500 ) || $pid >= 3 && $player->SpendUMoney( 1 ) )
		{
			$plr = new Player( $arr[0] );
			if( $pid < 3 ) $player->AddToLogPost( 0, -500, 20 );
			else $player->AddToLogPost( -1, -1, 20 );
			if( $pid < 3 ) $tm = time( ) + 15 * 24 * 3600;
			else $tm = time( ) + 30 * 24 * 3600;
			f_MQuery( "INSERT INTO player_presents ( player_id, img, txt, author, deadline ) VALUES ( {$plr->player_id}, '{$fnames[$pid]}', '$txt', '{$player->login}', $tm )" );
			$plr->syst3( "Персонаж {$player->login} подарил вам подарок" );
			echo "<script>update_money( $player->money, $player->umoney );</script>";
			echo "<b>Подарок персонажу {$plr->login} успешно доставлен</b><br><li><a href=game.php>Подарить еще один подарок</a><li><a href=game.php?phrase=1214>Уйти</a>";
			
			if( $player->HasTrigger( 210 ) && !$player->HasTrigger( 211 ) )
			{
				$player->SetTrigger( 211 );
				$player->SetTrigger( 214 );
				if( $plr->login == "Utka" ) $player->SetTrigger( 212 );
				echo "<li><a href=game.php?phrase=1239>Поговорить с купцом о пергаменте</a>";
			}
			
			return;
		} else echo "<font color=darkred>Вы не можете позволить себе этот подарок</font><br>";
	}
	else echo "<font color=darkred>Игрока с именем $nme не существует</font><br>";
}

echo "<li><a href=game.php?phrase=1214>Уйти</a>";
echo "<table><tr><td>";
echo "<script>FLUc();</script>";
echo "<table><tr><td>";

foreach( $fnames as $a=>$b )
{
	echo "<tr><td width=170 height=100%><script>FUcm();</script><img width=150 height=150 src=images/presents/$b><script>FL();</script></td>";
	echo "<td width=350 height=100%><script>FUcm();</script><div id=flawor$a>"; 
	if( $a < 3 ) echo "Стоимость подарка: <img width=11 height=11 src=images/money.gif> <b>500</b><br>Подарок дарится на <b>15</b> дней<br>";
	else echo "Стоимость подарка: <img width=11 height=11 src=images/umoney.gif> <b>1</b><br>Подарок дарится на <b>30</b> дней<br><small>Покупая этот подарок, вы помогаете проекту развиваться</small><br>";
	echo "<br><a href='javascript:buy($a)'>Купить</a></div><div id=frm$a style='display:none'><form action=game.php method=post><table><tr><td>Имя получателя:</td><td><input type=text name=nm class=m_btn></td><tr><td valign=top>Текст:</td><td><textarea class=te_btn name=txt rows=3 cols=15></textarea></td></tr><tr><td>&nbsp;</td><td><input type=submit class=m_btn value=Подарить></td></tr></tr></table><input type=hidden name=pid value=$a></form></div><script>FL();</script></td></tr>";
}

echo "</td></tr></table>";
echo "<script>FLL();</script>";
echo "</td></tr></table>";

?>
<script>

function buy( id )
{
	for( var i = 0; i < 5; ++ i )
	{
		if( i == id )
		{
			_( 'flawor' + i ).style.display = 'none';
			_( 'frm' + i ).style.display = '';
		}
		else
		{
			_( 'flawor' + i ).style.display = '';
			_( 'frm' + i ).style.display = 'none';
		}
	}
}

</script>
