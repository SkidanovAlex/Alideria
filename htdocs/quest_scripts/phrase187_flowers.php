<?

if( !$mid_php ) die( );

$fnames = Array (
	"romash.gif",
	"mak.gif",
	"lutik.gif",
	"vasil.gif",
	"narciss.gif"
);

$item_img = Array (
	"romaska.gif",
	"flo/mak.gif" ,
	"flo/lutik.gif" ,
	"flo/vasilok.gif",
	"flo/narcis.gif"
);

$item_ids = Array (
	15,
	12,
	13,
	11,
	14
);

$nm = Array (
	'ромашка',
	"мак",
	"лютик",
	"василек",
	"нарцисс"
);

if( array_key_exists('nm', $_POST) &&  isset( $_POST['nm'] ) )
{
	$pid = (int)$_POST['pid'];
	$nme = f_MEscape(htmlspecialchars($_POST['nm'],ENT_QUOTES)); 
	$txt = f_MEscape(htmlspecialchars($_POST['txt'],ENT_QUOTES));
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$nme'" );
	$arr = f_MFetch( $res );
	if( $arr )
	{
		if( $player->money >= 50 )
		{
			if( $player->DropItems( $item_ids[$pid], 31 ) )
			{
				$plr = new Player( $arr[0] );
				$player->SpendMoney( 50 );
				$player->AddToLogPost( 0, -50, 20 );
				$player->AddToLogPost( $item_ids[$pid], -31, 20 );
				$tm = time( ) + 3 * 24 * 3600;
				f_MQuery( "INSERT INTO player_presents ( player_id, img, txt, author, deadline ) VALUES ( {$plr->player_id}, '{$fnames[$pid]}', '$txt', '{$player->login}', $tm )" );
				$plr->syst3( "Персонаж {$player->login} подарил вам подарок" );
				echo "<script>update_money( $player->money, $player->umoney );</script>";
				echo "<b>Подарок персонажу {$plr->login} успешно доставлен</b><br><li><a href=game.php>Подарить еще один подарок</a><li><a href=game.php?phrase=450>Уйти</a>";
				
				checkZhorik( $player, 8, 5 ); // квест жорика подарить пять букетов

				// widow quest
	   			include_once( "quest_race.php" );
			   	updateQuestStatus ( $player->player_id, 2506 );
				
				return;
			} else echo "<font color=darkred>У вас не хватает цветов</font><br>";
		} else echo "<font color=darkred>У вас не хватает дублонов</font><br>";
	}
	else echo "<font color=darkred>Игрока с именем $nme не существует</font><br>";
}

echo "<li><a href=game.php?phrase=450>Уйти</a>";
echo "<table><tr><td>";
echo "<script>FLUc();</script>";
echo "<table><tr><td>";

foreach( $fnames as $a=>$b )
{
	echo "<tr><td width=170 height=100%><script>FUcm();</script><img width=150 height=150 src=images/presents/$b><script>FL();</script></td>";
	echo "<td width=350 height=100%><script>FUcm();</script><div id=flawor$a>Для создания букета понадобится 31 {$nm[$a]}<br><img width=50 height=50 src=images/items/{$item_img[$a]}><br>И за мои старания я возьму еще <img width=11 height=11 src=images/money.gif> 50<br><a href='javascript:buy($a)'>Купить</a></div><div id=frm$a style='display:none'><form action=game.php method=post><table><tr><td>Имя получателя:</td><td><input type=text name=nm class=m_btn></td><tr><td valign=top>Текст:</td><td><textarea class=te_btn name=txt rows=3 cols=15></textarea></td></tr><tr><td>&nbsp;</td><td><input type=submit class=m_btn value=Подарить></td></tr></tr></table><input type=hidden name=pid value=$a></form></div><script>FL();</script></td></tr>";
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
