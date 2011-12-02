<?
/* @author = undefined
 * @version = 0.0.0.1
 * @date = 13 февраля 2011
 * @about = Магазин для желающих одаривать новобрачных
 */
 
$fnames = Array
(
	'buk.gif',
	'bukmale.gif'
);



if( array_key_exists( 'nm', $_POST ) &&  isset( $_POST['nm'] ) )
{
	$pid = (int)$_POST['pid'];
	$dura = (int)$_POST['dura'];
	$nme = f_MEscape( htmlspecialchars( $_POST['nm'],ENT_QUOTES ) ); 
	$txt = f_MEscape( htmlspecialchars($_POST['txt'],ENT_QUOTES ) );
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$nme'" );
	$arr = f_MFetch( $res );
	if( $arr && $dura >= 0 && $dura < 4 )
	{
		$duration = 0;
		$price = 0;
		$curre = 0;
		
		if( $dura == 0 ) { $duration = 2; $price = 3000; $curre = 0; }
		if( $dura == 1 ) { $duration = 7; $price = 5000; $curre = 0; }
		if( $dura == 2 ) { $duration = 31; $price = 2; $curre = -1; }
		if( $dura == 3 ) { $duration = 100; $price = 15; $curre = -1; }

		if( $curre == 0 && $player->SpendMoney( $price ) || $curre == -1 && $player->SpendUMoney( $price ) )
		{
			$plr = new Player( $arr[0] );
			$player->AddToLogPost( $curre, -$price, 20 );
			$tm = time( ) + $duration * 24 * 3600;
			if( $duration == 100 ) $tm = 2147483647;
			f_MQuery( "INSERT INTO player_presents ( player_id, img, txt, author, deadline ) VALUES ( {$plr->player_id}, '{$fnames[$pid]}', '$txt', '{$player->login}', $tm )" );
			$plr->syst3( "Персонаж {$player->login} подарил вам подарок" );
			echo "<script>update_money( $player->money, $player->umoney );</script>";
			echo "<b>Подарок персонажу {$plr->login} успешно доставлен</b><br><a href=game.php>Подарить еще один подарок</a>";
				
			return;
		} else echo "<font color=darkred>У вас не хватает денег</font><br>";
	}
	else echo "<font color=darkred>Игрока с именем $nme не существует</font><br>";
}

echo "";
echo "<table><tr><td>";
echo "<script>FLUc();</script>";
echo "<table><tr><td>";

foreach( $fnames as $a=>$b )
{
	echo "<tr><td width=170 height=100%><script>FUcm();</script><img width=150 height=150 src=images/presents/$b><script>FL();</script></td>";
	echo "<td width=350 height=100%><script>FUcm();</script><div id=frm$a><form action=game.php method=post><table><tr><td>Имя получателя:</td><td><input type=text name=nm class=m_btn></td><tr><tr><td>Длительность:</td><td><select onchange='dchange($a)' name='dura' id='dura{$a}' class=m_btn><option value=0>2 дня<option value=1>Неделя<option value=2>Месяц<option value=3>Навсегда</select></td></tr><tr><td>Стоимость:</td><td><div id=present{$a}_price>&nbsp;</div></td></tr><tr><td valign=top>Текст:</td><td><textarea class=te_btn name=txt rows=3 cols=15></textarea></td></tr><tr><td>&nbsp;</td><td><input type=submit class=m_btn value=Подарить></td></tr></tr></table><input type=hidden name=pid value=$a></form></div><script>FL();</script></td></tr>";
}

echo "</td></tr></table>";
echo "<script>FLL();</script>";
echo "</td></tr></table>";

?>
<script>

function dchange( id )
{
	var val = ''; var cur = '';
	var dur = _( 'dura'+id ).selectedIndex;
	if( dur == 0 ) { val = 3000; cur = 'money.gif'; }
	else if( dur == 1 ) { val = 5000; cur = 'money.gif'; }
	else if( dur == 2 ) { val = 2; cur = 'umoney.gif'; }
	else if( dur == 3 ) { val = 15; cur = 'umoney.gif'; }

	_( 'present'+id+'_price' ).innerHTML = "<img width=10 height=10 src='images/" + cur + "' border=0> <b>"+val+"</b>";
}

for( var i = 0; i < 2; ++ i ) dchange(i);

</script>