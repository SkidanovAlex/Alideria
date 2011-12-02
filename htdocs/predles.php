<?

if( !isset( $mid_php ) ) die( );

include_once( 'tella_assault.php' );

if( $player->regime == 0 && ta_now( ) )
{
	echo "<font color=darkred><b>Внимание!</b></font> Монстры Дремучего Леса вышли из под контроля и угрожают спокойствию Теллы!!! Вы можете вступить в бой за любимый город!";
	ta_output( 6, "монстров", "Монстры Дремучего Леса повержены", "Бой с монстрами Дремучего Леса проигран" );
	return;
}


// квест на вступление в собирателей

$fdm_places = Array(
	1 => "первом",
	"втором",
	"третьем",
	"четвертом",
	"пятом",
	"шестом",
	"седьмом",
	"восьмом",
	"девятом",
	"десятом"
);



if( $player->till && time( ) >= $player->till - 2 && $player->regime >= 300 && $player->regime <= 310 )
{
	// закончили поиск иголки

	$val = $player->GetQuestValue( 18 );
	if( $val == 0 )
	{
		$val = mt_rand( 1, count( $fdm_places ) );
		$player->SetQuestValue( 18, $val );
	}
	$cur = $player->regime - 300;

	if( $val == $player->regime - 300 )
	{
		$player->syst( "Обшарив каждую соломинку в этом стоге, Вы стали подумывать так ли Вам нужна эта профессия, дабы протирать коленки в этом поле. Спина болит, суставы ломит и дело уже близиться к вечеру, а иглы нет и нет. Может бабулька перепутала или забыла спрятать иглу ? Вы уже почти потеряли веру в себя, как очередное движение рукой - и Вы вскрикнули от острой боли. Но это впервые Вы так радуетесь тому, что Вам больно - Вы укололись об иглу !! Ну и славно, теперь нужно отнести её в управу." );
		$player->SetTrigger( 33, 0 );
		$player->SetTrigger( 34, 1 );

		$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 47" );
		if( !mysql_num_rows( $qres ) )
		{
			$player->syst( "Информация о квесте <b>Иголка В Стоге Сена</b> обновлена." );
			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 47 )" );
		}
	}
	else $player->syst( "Обшарив каждую соломинку в этом стоге, Вам стало казаться, что здесь нет ничего кроме сена. Ваши глаза уже болят вглядываться в каждые стебелек сена с надеждой, что это такая желанная иголка. Но деваться некуда - сдаваться Вы не привыкли..." );

	$player->SetRegime( 0 );
	$player->SetTill( 0 );
	$regime = 0;
}



if( $player->HasTrigger( 33 ) && $player->regime == 0 )
{
	if( isset( $_GET['fdm'] ) )
	{
		$fdm = $HTTP_GET_VARS['fdm'];
		settype( $fdm, 'integer' );
		if( !isset( $fdm_places[$fdm] ) ) RaiseError( "Тут иголку искать нельзя", "$fdm" );
		$player->SetTill( time( ) + 90 );
		$player->SetRegime( 300 + $fdm );
		$regime = 300 + $fdm;
	}
	else
	{

		echo "<b>Искать иголку:</b>";
		echo "<ul>";
		foreach( $fdm_places as $a=>$b )
		{
			echo "<li><a href=game.php?fdm=$a>В $b стоге</a></li>";
		}
		echo "</ul>";
	}	
}




if( $player->regime >= 300 && $player->regime <= 310 )
{
	$text = "<b>Вы ищите иголку в ".$fdm_places[$player->regime - 300]." стоге";
	$text .= ".</b><br>Осталось: ";
	include( 'action_timer.php' );
}

?>
