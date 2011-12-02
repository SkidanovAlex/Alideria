<?

include_once('functions.php');
include_once('player.php');
include_once('quest_race_update_status.php');

f_MConnect();

// Убей { двух, трех, четырех } ...
$questMobs = array 	( 	array ( "Бага", "Багов", "Багов" ),
						array ( "Вампира", "Вампиров", "Вампиров"),
						array ( "Вуглускра", "Вуглускров", "Вуглускров"),
						array ( "Гигантскую крысу", "Гигантских крыс", "Гигантских крыс"), 
						array ( "Гигантского паука", "Гигантских пауков", "Гигантских пауков"),
						array ( "Гнома", "Гномов", "Гномов"), 
						array ( "Крысу", "Крыс", "Крыс"),
						array ( "Льва", "Львов", "Львов"), 
						array ( "Летучую мышь", "Летучих мышей", "Летучих мышей"), 
						array ( "Мурену", "Мурен", "Мурен"),
						array ( "Пиранью", "Пираний", "Пираний"), 
						array ( "Пиявку", "Пиявок", "Пиявок"),
						array ( "Повелителя медведей", "Повелителей медведей", "Повелителей медведей"),
						array ( "Повелительницу волков", "Повелительниц волков", "Повелительниц волков"),
						array ( "Повелителя теней", "Повелителей теней", "Повелителей теней"),
						array ( "Тритона", "Тритонов", "Тритонов"),
						array ( "Ужа", "Ужей", "Ужей")
					);
					
// Подари { два, три } букета ...
$questFlowers = array ( "нарциссов", "лютиков", "васильков", "маков", "ромашек" );
					
// создание записи в таблице гонок о запущенном квесте
function createQuest ( $questType, $questDetails, $questAmount, $questText )
{
	f_MQuery( "INSERT INTO quest_race (race_type, race_details, race_amount, race_text) values ($questType, $questDetails, $questAmount, '$questText')" );
}

// генератор квестов, функция вызывается видимо с определенным интервалом из другого файла
function generateQuest ( )
{
	// ниже копипаста из аси с алгоритмом выбора трех случайных чисел
	$arr = array( );
	for( $i = 0; $i < 13; ++ $i )
		$arr[$i] = $i + 1;
	
	for( $i = 0; $i < 3; ++ $i )
	{
		$pos = mt_rand( $i, 12 );
		$t = $arr[$pos]; 
		$arr[$pos] = $arr[$i]; 
		$arr[$i] = $t;
	}
	
	// внутри $arr теперь первые 3 значения обозначают номера квестов
	
	// определим общее количество заданий в квесте: 2 или 3
	$questSize = mt_rand ( 2, 3 );
	
	// запустим генератор нужное количество раз
	for( $i = 0; $i < $questSize; ++ $i )
	{
		// обнулим на всякий случай
		$questDetails = 0;
		$questAmount = 0;
		$questText = "";
		
		// тип нашего задания
		$questType = $arr[$i] + 2500;
	
		if( $arr[$i] == 1 ) // убить монстров
		{
			global $questMobs; // массив объявлен в начале файла, не знаю почему
			
			// "Убить монстра, тип от 1 до 17, количество от 2 до 4"
			$questDetails = 100 + mt_rand( 1, 17 );
			$questAmount = mt_rand( 2, 4 );
			
			// простенькие преобразования для текстовой записи
			$amountText = "двух";
			if ( $questAmount == 3 )
				$amountText = "трех";
			if ( $questAmount == 4 )
				$amountText = "четырех";
			
			// Убей трех { 3, "Бага", "Багов", "Багов" }
			$questText = "Убей <b>$amountText " . my_word_str( $questAmount, $questMobs[$questDetails-101][0], $questMobs[$questDetails-101][1], $questMobs[$questDetails-101][2] ) . "</b>.";
		}
		
		if( $arr[$i] == 6 ) // подарить цветы
		{
			global $questFlowers;
			
			// "Подарить букет цветов, тип от 1 до 5, количество от 2 до 3
			$questDetails = 150 + mt_rand( 1, 5 );
			$questAmount = mt_rand( 2, 3 );
			
			$questText = "Подари <b>$questAmount любых " . my_word_str( $questAmount, "букет", "букета", "букетов" ) . /* " " . $questFlowers[$questDetails - 151] . */ "</b> своим друзьям.";
		}
		
		// добыча, крафт, дроп, аукцион и поиск перьев
		if( $arr[$i] == 2 || $arr[$i] == 3 || $arr[$i] == 5 || $arr[$i] == 7 || $arr[$i] == 8 )
		{
			$questAmount = mt_rand( 1, 2 );
			
			switch ( $arr[$i] )
			{
				case 2: // добыча
					$questText = "Добудь любой ресурс в любой гильдии <b>$questAmount " . my_word_str( $questAmount, "раз", "раза", "раз" ) . "</b>.";
					break;
				case 3: // крафт
					$questText = "Изготовь любой предмет <b>$questAmount " . my_word_str( $questAmount, "раз", "раза", "раз" ) . "</b>.";
					break;
				case 5: // дроп
					$questText = "Выбей что-нибудь из любого монстра <b>$questAmount " . my_word_str( $questAmount, "раз", "раза", "раз" ) . "</b>.";
					break;
				case 7: // аукцион
					$questText = "Купи на аукционе <b>$questAmount " . my_word_str( $questAmount, "любой лот", "любых лота", "любых лотов" ) . "</b>.";
					break;
				case 8: // поиск перышек
					$questText = "Найди на Зачарованной поляне <b>$questAmount " . my_word_str( $questAmount, "перышко", "перышка", "перышек" ) . "</b>.";
					break;	
			}
		}
		
		// старьевщик, столовая
		if( $arr[$i] == 4 || $arr[$i] == 13 ) 
		{
			$questAmount = mt_rand( 5, 9 );
			
			if( $arr[$i] == 4 ) // старьевщик
			{
				$questText = "Продай старьевщику <b>$questAmount " . my_word_str( $questAmount, "предмета", "предметов", "предметов" ) . "</b>.";
			}
			else // еда в столовой
			{
				$questText = "Приготовь в столовой своего ордена хотя бы <b>$questAmount " . my_word_str( $questAmount, "единицу", "единицы", "единиц" ) . " еды.</b>";
			}
		}
		
		// харчевня
		if( $arr[$i] == 10 )
		{
			$questAmount = mt_rand( 15, 30 ) * 100;
			$questText = "Восстанови <b>$questAmount единиц здоровья</b> в харчевне.";
		}
		
		// дозор
		if( $arr[$i] == 11)
		{
			$questAmount = 1;
			$questText = "Помоги защитить Теллу от врагов, <b>сходи в дозор</b>!";
		}
		
		// прикрепление перышек и хранилка
		if( $arr[$i] == 9 || $arr[$i] == 12 )
		{
			$questAmount = mt_rand( 2, 4 );
			if( $arr[$i] == 12 ) // хранилка 
			{
				$questText = "Продли аренду своего хранилища на <b>$questAmount " . my_word_str( $questAmount, "неделю", "недели", "недель" ) . "</b>.";
			}
			else // прикрепление перышек
			{
				$questText = "Прикрепи к кому-нибудь <b>$questAmount " . my_word_str( $questAmount, "перышко", "перышка", "перышек" ) . "</b>.";
			}
		}
		
		// создание записи о квесте в базе данных
		createQuest( $questType, $questDetails, $questAmount, $questText );
	}
}

// отображаем информацию о текущей гонке
function displayQuest ()
{
	$questInfo = "";
	
	if( checkQuestActivity() )
	{
		// выбираем из таблицы все текстовые описания квестов, они уже любезно предоставлены нам генератором
		$res = f_MQuery( "SELECT race_text FROM quest_race" );
		if ( mysql_num_rows( $res ) > 0 )
		{
			$questInfo .= "У меня для тебя задание!<br>";
			
			// каждое из описаний подставляем в незатейливое сообщение о новом задании, перечисляем этапы по очереди
			while ( $arr = f_MFetch( $res ) )
			{
				$questInfo .= $arr['race_text'] . " <br> ";
			}
			
			$questInfo .= "Поторопись, я награжу только пришедшего ко мне первым!";
		}
	}
	else
	{
		$questInfo = "В данный момент никаких соревнований нет.";
	}
	
	// возвращаемое значение должно быть отправлено игроку в системный чат или в дневник
	// это будет сделано вероятнее всего в файле, который будет запускаться по расписанию
	return $questInfo;
}

// проверка состояния "запущенности" квестов
// пригодно для применения в каждом месте, где проверяется выполнение заданий и прочие связанные с этим вещи

// - поправка: на смену регулярному вызову этой функции пришел вызов updateQuestStatus из каждого потенциально связанного с квестами места
// то есть теперь в каждом файле не нужно будет сначала проверять, а потом вызывать функции
// нужно будет просто всегда вызывать функцию, которая в самом начале выключится в случае неактивного квеста

// - можно этот вариант и изменить на наиболее оптимальный
// возвращает true при активной гонке и false при обычном, мирном положении дел
function checkQuestActivity ( ) 
{
	$res = f_MQuery( "SELECT * FROM quest_race" );
	if ( mysql_num_rows( $res ) > 0 )
		return true;
	else
		return false;
}

// уберем информацию о квесте с помощью грубой силы
// вызывается после завершения квеста любым персонажем
function dropQuest ( ) 
{
	f_MQuery( "TRUNCATE TABLE quest_race" );
	
	// еще нужно сбросить все Quest Value в рамках квестов, чтобы дальше не мешались
	f_MQuery( "DELETE FROM player_quest_values WHERE value_id > 2500 AND value_id < 2514" );
	f_MQuery( "DELETE FROM player_triggers WHERE trigger_id=260" );
}


// функция для вызова непосредственно из файлов с проверкой событий
// пока нет идей как объединить ее с чем-либо другим, поэтому вызываться будет отдельно
// возвращает сообщение, уведомляющее об оставшихся действиях в рамках текущей гонки
function getRemainingActions ( $player_id )
{
	// сразу выключаемся, если нет активного квеста
	// это делает функцию пригодной для постоянного запуска из файлов с обработкой отдельных событий
	if ( !checkQuestActivity( ) )
		return false;
	
	$tasks = array( );
	$i = 0;

	// сначала запоминаем суммарные данные текущего квеста
	$res = f_MQuery( "SELECT race_type, race_details, race_amount FROM quest_race" );
	while ( $arr = f_MFetch( $res ) )
	{
		$tasks[$i] = array (
			"race_type" => $arr['race_type'],
			"race_details" => $arr['race_details'],
			"race_amount" => $arr['race_amount']
		);
		$i ++;
	}

	$plr = new Player( $player_id );
	$value = "";

	// теперь посмотрим, сколько чего игрок уже успел выполнить
	for ( $i = 0; $i < count( $tasks ); ++ $i )
	{
		// для случаев, когда выполнено меньше необходимого, добавим к результату соответствующую строчку
		$remainingAmount = $tasks[$i]['race_amount'] - $plr->GetQuestValue( $tasks[$i]['race_type']);
		
		if ( $remainingAmount > 0 )
		{
			switch ( $tasks[$i]['race_type'] )
			{
				case 2501:
					global $questMobs;
					$value .= "Убить $remainingAmount " . my_word_str( $remainingAmount, $questMobs[$tasks[$i]['race_details']-101][0],  $questMobs[$tasks[$i]['race_details']-101][1],  $questMobs[$tasks[$i]['race_details']-101][2] ) . ". ";
					break;
				case 2502:
					$value .= "Добыть в любой гильдии $remainingAmount " . my_word_str( $remainingAmount, "предмет", "предмета", "предметов" ) . ". ";
					break;
				case 2503:
					$value .= "Изготовить в любой гильдии $remainingAmount " . my_word_str( $remainingAmount, "предмет", "предмета", "предметов" ) . ". ";
					break;
				case 2504:
					$value .= "Продать старьевщику $remainingAmount " . my_word_str( $remainingAmount, "предмет", "предмета", "предметов" ) . ". ";
					break;
				case 2505:
					$value .= "Выбить $remainingAmount " . my_word_str( $remainingAmount, "предмет", "предмета", "предметов" ) . " из любого монстра. ";
					break;
				case 2506:
					global $questFlowers;
					$value .= "Подарить $remainingAmount " . my_word_str( $remainingAmount, "букет", "букета", "букетов" ) /*. " " . $questFlowers[$tasks[$i]['race_details']-151]*/ . ". ";
					break;
				case 2507:
					$value .= "Купить $remainingAmount " . my_word_str( $remainingAmount, "лот", "лота", "лотов" ) . " на аукционе. ";
					break;
				case 2508:
					$value .= "Найти $remainingAmount " . my_word_str( $remainingAmount, "перышко", "перышка", "перышек" ) . " на Зачарованной поляне. ";
					break;
				case 2509:
					$value .= "Прикрепить $remainingAmount " . my_word_str( $remainingAmount, "перышко", "перышка", "перышек" ) . " к любому игроку. ";
					break;
				case 2510:
					$value .= "Восстановить $remainingAmount " . my_word_str( $remainingAmount, "единицу", "единицы", "единиц" ) . " здоровья в харчевне. ";
					break;
				case 2511:
					$value .= "Сходить в дозор. ";
					break;
				case 2512:
					$value .= "Продлить аренду хранилища на $remainingAmount " . my_word_str( $remainingAmount, "неделю", "недели", "недель" ) . ". ";;
					break;
				case 2513:
					$value .= "Приготовить $remainingAmount " . my_word_str( $remainingAmount, "единицу", "единицы", "единиц" ) . " еды в столовой ордена. ";
					break;
			}
		}
	}
	
	if( $value == "" ) $value = "Вернуться к Фавну!";

	$value = "Для завершения гонки осталось: " . $value;
	$value .= "Поспеши!";
	return $value;
}

// обработка победы игрока
// выдача награды, обновление количества побед в базе, выдача плюшки за N побед
function processPlayerWin ( $player_id, $player_level )
{
	// подсчитываем и выдаем игроку награду за победу в гонке
	giveQuestPrise( $player_id, $player_level );
	
	// прибавляем победу в базе
	updatePlayerWins( $player_id );
	
	// сбрасываем квест
	dropQuest( );
	
	// плюшки за многократные победы
	/*
	
		спросить у Иши:
		1. каким образом посчитать, что текущая победа кратна 25? 25, 50, 75, 100... с помощью какого оператора деления и как именно это делается
		2. как подключить премиум и какой премиум подключать?
	
	*/
	
	// возвращаем нужное для отображения результатов квеста значение
	// 10 - обычная победа, за ней следует оглашение имени победителя слова о завершении гонки
	// 20 - юбилейная победа, за ней следует другое сообщение с упоминанием премиума, можно изменить и сообщение в чат
	$victory = getPlayerWins( $player_id );
	if ( $victory % 25 )
		return 10;
	else
		return 20;
}

// подсчет количества побед выбранного игрока
// вернем 0 для непобеждавшего ранее игрока или количество побед для бывшего победителем хотя бы раз
function getPlayerWins ( $player_id )
{
	$res = f_MQuery( "SELECT win_count FROM quest_winners WHERE player_id=$player_id" );
	if ( mysql_num_rows( $res ) > 0 )
	{
		$arr = f_MFetch( $res );
		return $arr['win_count'];
	}
	else
		return 0;
}

// прибавлялка числа побед
// в нужном месте будет понятнее, пусть даже одна строчка
function updatePlayerWins ( $player_id ) 
{
	$res = f_MQuery( "SELECT * FROM quest_winners WHERE player_id=$player_id" );
	if ( mysql_num_rows( $res ) > 0 )
	{
		f_MQuery( "UPDATE quest_winners SET win_count=win_count+1 WHERE player_id=$player_id" );
	}
	else
	{
		f_MQuery( "INSERT INTO quest_winners (player_id, win_count) VALUES ($player_id, 1)" );
	}
}

// подсчет и выдача награды за квест
function giveQuestPrise ( $player_id, $player_level )
{
	// для начала узнаем, сколько заданий было в нашем квесте (от 2 до 3)
	$res = f_MQuery( "SELECT COUNT(*) FROM quest_race" );
	if ( $arr = f_MFetch( $res ) )
	{
		// собственно здесь и подсчитаем
		if ($arr[0] > 0)
		{
			/*
				Боевой опыт				100%		Уровень * 15 + Множитель до Уровень * 20 + Множитель
				Дублоны					100%		Уровень * 15 + Множитель до Уровень * 20 + Множитель
				Профессиональный опыт	50%			Уровень + 15 до Уровень + 30
				
				После первых запусков можно расширить список наград
			*/
			
			// для квеста из двух заданий множитель награды будет 7, для трех - 10
			$questSize = ( $arr[0] == 2 ) ? 7 : 10;
			
			// подсчет награды с учетом сложности квеста
			$battleExp = mt_rand( ( 15 * $player_level + $questSize * 10 ), ( 20 * $player_level + $questSize * 10 ) ); 
			$money = mt_rand( ( 15 * $player_level + $questSize * 10 ), ( 20 * $player_level + $questSize * 10 ) );
			$profExp = mt_rand( ( 15 + $player_level ), ( 30 + $player_level ) ) * mt_rand( 0, 1 ); // 50% умножение на ноль; без учета сложности квеста
			
			if ( updatePlayerValues ( $player_id, $battleExp, $money, $profExp ) )
			{
				global $questRacePrizeStr;
				$questRacePrizeStr = "<b>$battleExp</b> " . my_word_str( $battleExp, "единица", "единицы", "единиц" ) . " боевого опыта".($profExp?", <b>$profExp</b> " . my_word_str( $profExp, "единица", "единицы", "единиц" ) . " профессионального опыта":"")." и <b>$money</b> ". my_word_str( $money, "дублон", "дублона", "дублонов" );

				return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	else
		return false;
}

// фактическое добавление указанных значений в таблицу базы данных с параметрами игроков
function updatePlayerValues ( $player_id, $battleExp, $money, $profExp )
{
	// lock table, как обычно, я не умею :-[
	
	if ( $res = f_MQuery( "UPDATE characters SET exp=exp+$battleExp, prof_exp=prof_exp+$profExp, money=money+$money WHERE player_id=$player_id" ) )
	{
		/*
			----- код добавления информации о деньгах в админский лог -----
		*/
		
		return true;
	}
	else
		return false;
	
	// unlock table
}

?>
