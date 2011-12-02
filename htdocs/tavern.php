<?

if( !isset( $mid_php ) ) die( );

$havkas = Array (
	Array( "Сырная похлебка", 6, 50, 'сырную похлебку' ),
	Array( "Котлета", 10, 100, "котлету" ),
	Array( "Отбивная по-королевски", 18, 200, "отбивную по-королевски" )
);

if( isset( $_GET['eat'] ) && $player->regime == 0 )
{
	$id = $_GET['eat'];
	settype( $id, 'integer' );
	if( $id < 0 || $id >= count( $havkas ) ) RaiseError( "Попытка съесть отбивную из крысиных хвостиков!", "$id" );

	if( !$player->SpendMoney( $havkas[$id][1] ) )
		$player->syst( 'У вас недостаточно дублонов' );
	else
	{
		$player->AddToLogPost( 0, -$havkas[$id][1], 11 );
		$player->syst( "Вы скушали <b>{$havkas[$id][3]}</b> и восстановили <b>{$havkas[$id][2]}</b> единиц здоровья" );
		$player->AlterRealAttrib( 1, $havkas[$id][2] );
		
		// Widow quest
	   	include_once( "quest_race.php" );
	   	updateQuestStatus ( $player->player_id, 2510, $havkas[$id][2] );
	}
}


echo "<table><tr><td>";
ScrollLightTableStart( );
echo "<table><tr><td>&nbsp;</td><td height=100%>".GetScrollTableStart( )."<b>Стоимость</b>".GetScrollTableEnd( )."</td><td height=100%>".GetScrollTableStart( )."<b>Восстанавливает здоровья</b>".GetScrollTableEnd( )."</td><td>&nbsp;</td></tr>";
foreach( $havkas as $id=>$arr )
{
	echo "<tr><td height=100%>".GetScrollTableStart( 'left' )."$arr[0]".GetScrollTableEnd( )."</td><td height=100%>".GetScrollTableStart( 'right' )."$arr[1] <img width=11 height=11 border=0 src='images/money.gif'>".GetScrollTableEnd( )."</td><td height=100%>".GetScrollTableStart( 'right' )."$arr[2]".GetScrollTableEnd( )."</td><td>".GetScrollTableStart( )."<a href=game.php?eat=$id>Кушать</a>".GetScrollTableEnd( )."</td></tr>";
}
echo "</table>";
ScrollLightTableEnd( );
echo "</td></tr></table>";
echo "<br>";


if( $player->HasTrigger( 231 ) )
{
	echo "<script>q7_login = '$player->login';</script>";
	include( 'quest_scripts/q7_tavern.php' );
}


// квест на вступление в собирателей

$fdm_places = Array(
	1 => "столом",
	"стулом",
	"бочкой пива",
	"париком трактирщика",
	"лестницей",
);



if( $player->till && time( ) >= $player->till - 2 && $player->regime >= 300 && $player->regime <= 310 )
{
	// закончили поиск монетки

	$val = $player->GetQuestValue( 16 );
	if( $val == 0 )
	{
		$val = mt_rand( 1, count( $fdm_places ) );
		$player->SetQuestValue( 16, $val );
	}
	$cur = $player->regime - 300;

	if( $val == $player->regime - 300 )
	{
		$player->syst( "Невероятно, после долгих поисков вы нашли под $fdm_places[$cur] спрятанную деревянную монету! Пора обрадовать мастера гильдии собирателей!" );
		$player->AddToLog( 219, 1, 1000000 );
		$player->AddItems( 219 );
		$player->SetTrigger( 18, 0 );
		$player->SetTrigger( 45, 1 );

		$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 31" );
		if( !mysql_num_rows( $qres ) )
		{
			$player->syst( "Информация о квесте <b>Поиск Деревянной Монеты</b> обновлена." );
			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 31 )" );
		}
	}
	else $player->syst( "Похоже, монета спрятана не под $fdm_places[$cur]" );

	$player->SetRegime( 0 );
	$player->SetTill( 0 );
	$regime = 0;
}



if( $player->HasTrigger( 18 ) && $player->regime == 0 )
{
	if( isset( $_GET['fdm'] ) )
	{
		$fdm = $HTTP_GET_VARS['fdm'];
		settype( $fdm, 'integer' );
		if( !isset( $fdm_places[$fdm] ) ) RaiseError( "Тут монетку искать нельзя", "$fdm" );
		$player->SetTill( time( ) + 90 );
		$player->SetRegime( 300 + $fdm );
		$regime = 300 + $fdm;
	}
	else
	{

		echo "<b>Искать деревянную монету:</b>";
		echo "<ul>";
		foreach( $fdm_places as $a=>$b )
		{
			echo "<li><a href=game.php?fdm=$a>Под $b</a></li>";
		}
		echo "</ul>";
	}	
}




if( $player->regime >= 300 && $player->regime <= 310 )
{
	$text = "<b>Вы ищете деревянную монету под ".$fdm_places[$player->regime - 300];
	$text .= ".</b><br>Осталось: ";
	include( 'action_timer.php' );
}

?>
