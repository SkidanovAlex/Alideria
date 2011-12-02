<?

$quiz = array(

array( "Над рекой стелится как коромысло писаное, а поднять нельзя", "Радуга", "Мост", "Волна", "Лёд", 0 ),
array( "Ты её догоняешь, она от тебя деться никуда не может", "Жена", "Городская Управа", "Тень", "Температура", 2 ),
array( "Из воды её берут, но воды она боится. Очень плохо без неё, ну а с ней - годится", "Кошка", "Соль", "Рыба", "Радуга", 1 ),
array( "Худой как держак, а голова как кулак", "Аист", "Молоток", "Вилка", "Колодец", 1 ),
array( "Вырос в лесу, а службу дома несу", "Волос", "Жук-Короед", "Веник", "Маугли", 2 ),

0

);

$deadline = $player->GetQuestValue( 29 );
if( $deadline > time( ) )
{
	echo "<b>Дракон:</b>: Ты совсем недавно ".($player->sex?"пыталась":"пытался")." угадать мою загадку, и не ".($player->sex?"угадала":"угадал").". Теперь надо подождать еще ".my_time_str( $deadline - time( ) )." прежде, чем ты сможешь получить ответ на свой вопрос";
	echo "<br><br><ul><li><a href=game.php?phrase=494>Поблагодарить дракона и уйти</a></ul>";
	return;
}

$n = count( $quiz ) - 1;

$id = $player->GetQuestValue( 27 );
if( !$id )
{
	$id = mt_rand( 1, $n );
	$player->SetQuestValue( 27, $id );
}

-- $id;

if( isset( $_GET['quiz'] ) )
{
	$player->SetQuestValue( 27, 0 );
	$answer = (int)$_GET['quiz'];
	if( $answer < 0 || $answer >= 4 ) RaiseError( "Выходящая за границы отгадка в вопросах к дракону" );
	if( $answer == $quiz[$id][5] )
	{
		echo "Все верно! Не перевелись в Алидерии умники и умницы. Значит вот ответ на твой вопрос. ";
		$question = $player->GetQuestValue( 28 );
		if( $question == 0 )
		{
			echo text_sex_parse( "{", "|", "}", "Полоскухи - очень хитрые и коварные создания. Они обитают в Западном лесу, но никогда не выползают со своих убежищ, нор днем - всегда только ночью. Но тебе понадобится приманка и ловушка, чтобы изловить гадину. Только смотри не зашиби змеюку, их и так мало осталось в лесу. Такие браконьеры как ты постарались. В общем, ищи приманку - это лучше всего крыса. В пещерах их полно. Ты таких раньше точно {убивал|убивала}.", $player->sex );
			$player->SetTrigger( 55, 1 );
			$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 52" );
    		if( !mysql_num_rows( $qres ) )
    		{
    			$player->syst( "Информация о квесте <b>Лекарство для Шамахана</b> обновлена." );
    			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 52 )" );
    		}
		}
		if( $question == 1 )
		{
			echo text_sex_parse( "{", "|", "}", "Полоскуха - змея очень осторожная. Выходит на люди только ночью. Но чтобы словить её, нужно поставить ловушку с приманкой в западном лесу и ставить только после захода солнца, после шести часов вечера. Тебе лучше оставить ловушку до восхода солнца, до шести утра. И только после этого проверять <улов>. ", $player->sex );
			$player->SetTrigger( 56, 1 );
			$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 62" );
    		if( !mysql_num_rows( $qres ) )
    		{
    			$player->syst( "Информация о квесте <b>Лекарство для Шамахана</b> обновлена." );
    			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 62 )" );
    		}
		}
		if( $question == 2 )
		{
			echo text_sex_parse( "{", "|", "}", "Это очень простой вопрос. Ты {мог|могла} бы и {сам|сама} догадаться. Кто в Алидерии имеет дело с ядами, химикатами, реактивами, зельями, лекарствами? Правильно, алхимики. Вот там и ищи помощи, больше я тебе помочь ничем не смогу.", $player->sex );
			$player->SetTrigger( 59, 1 );
			$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 64" );
    		if( !mysql_num_rows( $qres ) )
    		{
    			$player->syst( "Информация о квесте <b>Лекарство для Шамахана</b> обновлена." );
    			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 64 )" );
    		}
		}
		if( $question == 3 )
		{
			echo text_sex_parse( "{", "|", "}", "Не каждый день увидишь человека с таким чудом. Яйца мурены - это любимое лакомство алидерийских зайцев. А вот теперь ты не спрашивай, как эти зайцы добывают яйца мурены. А никак, они их просто любят. Таким образом, раз у тебя есть яйцо, то, наверное, ты можешь поймать на него живого зайца. Ну а как ловить всякую живность на приманку ты уже знаешь.", $player->sex );
			$player->SetTrigger( 69, 1 );
			$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 72" );
    		if( !mysql_num_rows( $qres ) )
    		{
    			$player->syst( "Информация о квесте <b>Большие прятки в маленьком городе</b> обновлена." );
    			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 72 )" );
    		}
		}
		if( $question == 4 )
		{
			echo text_sex_parse( "{", "|", "}", "Надо было не брать иглу. {Взял|Взяла} - неси бремя до конца. А зайца надо ловить так же как полоскуху, только не в лесу в густом и дремучем, а сообрази, где их побольше, там и лови. Ты ток медом диким яйцо смаж, тогда заяц наверняка пойдет. ", $player->sex );
			$player->SetTrigger( 70, 1 );
			$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 74" );
    		if( !mysql_num_rows( $qres ) )
    		{
    			$player->syst( "Информация о квесте <b>Большие прятки в маленьком городе</b> обновлена." );
    			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 74 )" );
    		}
		}
		if( $question == 5 )
		{
			echo text_sex_parse( "{", "|", "}", "Вижу я что у тебя за утка, не простая это утка. Это очень ценная утка теперь и хранить её надо бережно. Видишь, за веткой около таверны спрятан сундук? Положи утку туда и никому не говори о том, что она здесь лежит. Расскажешь кому-то - будут у тебя тридцать три и три несчастья. Смотри не проболтайся, а сундук теперь я охранять буду. Ты славно {потрудился|потрудилась}, выдам я тебе в благодарность за это очень нужную нынче вещичку - Очищенное серебро. Ох и в нехорошую историю ты сегодня {вляпался|вляпалась}, {name}, ох и в нехорошую... ", $player->sex );
			if( !$player->HasTrigger( 79 ) )
			{
				$player->AddItems( 8031, 1 );
				$player->AddToLogPost( 8031, 1, 0, 595 );
				$player->DropItems( 9367, 1 );
				$player->AddToLogPost( 9367, -1, 0, 595 );
			}
			$player->SetTrigger( 79, 1 );
			$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 79" );
    		if( !mysql_num_rows( $qres ) )
    		{
    			$player->syst( "Информация о квесте <b>Большие прятки в маленьком городе</b> обновлена." );
    			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 79 )" );
    		}
		}
		if( $question == 6 )
		{
			echo text_sex_parse( "{", "|", "}", "Насколько мне известно, Башня Тайных Знаний закупает пергамент у купца, который последние пару дней занимается продажей подарков по грабительским ценам. ", $player->sex );
			$player->SetTrigger( 207, 1 );
		}

	}
	else
	{
		echo "<b>Дракон</b>: Ну что же ты так, а я так переживал за тебя. Ну, ничего, следующий раз точно отгадаешь, а пока иди съешь сладенького - это хорошо для работы мозга. Если нужна моя помощь, подходи ещё. Но не раньше чем через четверть часа.";
		$player->SetQuestValue( 29, time( ) + 15 * 60 );
	}

	echo "<br><br><ul><li><a href=game.php?phrase=494>Поблагодарить дракона и уйти</a></ul>";

	return;
}

if( isset( $_GET['phrase'] ) )
{
	$phrase = (int)$_GET['phrase'];
	if( $phrase == 493 ) $player->SetQuestValue( 28, 0 );
	if( $phrase == 495 ) $player->SetQuestValue( 28, 1 );
	if( $phrase == 501 ) $player->SetQuestValue( 28, 2 );
	if( $phrase == 562 ) $player->SetQuestValue( 28, 3 );
	if( $phrase == 568 ) $player->SetQuestValue( 28, 4 );
	if( $phrase == 595 ) $player->SetQuestValue( 28, 5 );
	if( $phrase == 1223 ) $player->SetQuestValue( 28, 6 );

}

if( isset( $_GET['quiz'] ) )
{
}

echo "<b>Дракон: </b> Итак, вот моя загадка: <i>".$quiz[$id][0]."</i>.<br><br>Ваш ответ:<ul>";

for( $i = 0; $i < 4; ++ $i )
{
	echo "<li><a href=game.php?quiz=$i>".$quiz[$id][$i + 1]."</a>";
}
echo "</ul>";

?>
