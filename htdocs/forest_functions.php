<?

include_once( "items.php" );

$forest_names = Array
(
	0 => "Опушка Леса",
	1 => "Дремучий Лес",
	2 => "Зачарованная Поляна",
	3 => "Сад Колдуньи",
	4 => "Волчья Тропа",
	5 => "Берег Реки",
	6 => "Река",
	7 => "Деревня Эльфов",
	8 => "Поляна Единорогов",
	9 => "Проклятые Земли",
	10 => "Выход в Столицу",
	11 => "Хижина Колдуньи",
	12 => "Кустарник",
	13 => "Мелколесье",
	14 => "Обелиск",
	20 => "Муравейник",
	21 => "Змеиное Гнездо",
	22 => "Улей",
	23 => "Смоляная Роща",
	
	100 => "Непроходимый Ил",
	101 => "Глубоководье",
	102 => "Заросли Тростника",
	110 => "Река - Выход в Столицу",
	
	200 => "Каменная Стена",
	201 => "Соляное Озеро",
	202 => "Сад Сталактитов",
	203 => "Каменная лестница",
	204 => "Пропасть Мертвых Душ",
	205 => "Плато Огня",
	206 => "Небольшая Пещера",
	210 => "Подгорье - Выход в столицу",
	
	300 => "Урочище Истра"
);

$forest_comments = Array
(
	0 => "Опушка Леса - тихое место. Вокруг вас растут редкие невысокие деревья, слышно пение птиц. Здесь можно найти грибы и лесные ягоды. Никто никогда не встречал диких зверей в этих местах, а особая волшебная аура опушки не позволяет людям проявлять насилие в адрес друг друга.",
	1 => "Дремучий Лес - место темное и опасное. Деревья густо сплетаются у вас над головой, не давая пройти солнечному свету. В этих местах обитают дикие звери, а тусклый свет и неровная земля не позволяют передвигаться достаточно быстро.",
	2 => "Зачарованная Поляна - волшебное место. Обитают на поляне волшебные существа, а растут диковинные растения, которые нигде больше нельзя встретить. Будьте осторожны, местные жители могут не всегда дружелюбно к вам относиться.",
	3 => "Вы находитесь в Саду Колдуньи. Люди опасаются этого места - ходят слухи, что Колдунья владеет черной магией и умеет призывать из темных миров различных демонических существ. Возможно, вам следует поскорее отсюда уйти.",
	4 => "Волчья Тропа - самый быстрый способ пересечь Дремучий Лес. И самый опасный - огромные лютоволки встречаются на этой тропе, норовя загрызть насмерть всякого, кто осмелится ступить сюда.",
	5 => "Вы находитесь на берегу реки. Похоже, перебраться на ту сторону вы не сможете - течение очень быстрое. Река обладает странной отталкивающей силой, пойти вдоль берега не получится.",
	6 => "Как вы смогли попасть на реку? :)",
	7 => "Вы находитесь в Деревне Эльфов. Эльфы - светлые существа, изучающие магию природы - живут высоко на деревьях, внимательно изучая всех, кто вторгается в их обитель. Ваша участь крайне не завидна, если вы пришли сюда с недобрыми намерениями.",
	8 => "Эта поляна - самое загадочное место в Западном Лесу. Многие говорят, что видели здесь единорогов, но кажется, что вокруг пусто и нет ни одной живой души. Возможно, все не так просто, как кажется на первый взгляд?",
	9 => "Проклятые Земли - страшное место. Кажется, что свет покинул его, и никогда не вернется назад. Вокруг только выжженная земля, а вдали слышатся душераздирающие крики. Умный человек не стал бы здесь задерживаться надолго.",
	10 => "Между двумя огромными деревьями идет тропинка, а вдали видны ярмарка и башня тайных знаний. Но если деревья обойти, никакой тропинки нет, только лес, насколько видит глаз. Этот лес поистине зачарованный.",
	11 => "Небольшая покосившаяся хижина спряталась в зарослях Опушки. Даже не заходя в нее, вы почувствовали терпкий запах сушеных трав, перебивающий аромат цветущих маков. Это обитель местной Колдуньи.",
	12 => "Небольшой Кустарник преградил Вам дорогу. Говорят, в таких зарослях часто можно встретить невиданных монстров. Они так хорошо прячутся в кустах, что их на самом деле мало кто видел.",
	13 => "Свежий воздух, мягкая трава и хорошая компания - что ещё нужно уставшему от трудовых будней Алидерийцу? Любимое место отдыха горожан. Вечерами они любят собираться здесь у костра, обсуждать последние новости Теллы, да и что скрывать - просто сплетничать.<br>Заслышав шум забредают сюда задорные мишки, готовые закусить отдыхающими, да белки так и наровят почистить орехи прямо на голову подгулявшего весельчака.",
	14 => "Вы видите здесь только обломки обелиска. Когда-то здесь была Великая Битва за Солнце.",
	20 => "Темный холм возвышается посреди небольшой полянки, у корней старого дуба. Это огромный дом лесных муравьев спрятался в тени листвы. Трудолюбивые насекомые здорово потрудились, возводя этот купол из веток, травинок, земли и прочих подходящих материалов. Если даже Вы просто мимо проходили и случайно набрели на муравейник, будьте осторожны. Иной раз его жители могут быть весьма агрессивны.",
	21 => "В овраге, вдали от солнечных лучей и ветра спряталась небольшая пещерка. Вход в нее скрыт от посторонних глаз огромным валуном. Только узкая тесная расщелина позволяет протиснуться внутрь. Однако покоящиеся рядом с ней кости не особо располагают к исследованию. Говорят, где-то в этих местах живую гремучие змеи. И похоже, это как раз их гнездо. Беспокоить или нет хозяев логова - решать Вам.",
	22 => "Жуткое гудение выдает это место, даже если отойти от него на значительное расстояние. Огромный наполовину находящийся в земле улей лучше обходить стороной. Дикие пчелы, обитающие тут, не любят незваных гостей. А если кто-то вознамерится покуситься на их мед, то вообще жди беды. Жала пчел очень остры, а яд крайне опасен. Приближаться к святая святых грозных насекомых можно только в надежной защите.",
	23 => "На берегу реки раскинулась небольшая сокровенная роща. Деревья, растущие в ней, необычные. Это волшебные сосны. И у каждой из них есть дух-хранитель - дриада. Когда хотя бы одно из деревьев или один из духов погибает, другие ...",
	
	100 => "Непроходимый Ил - описание",
	101 => "Глубоководье - Описание",
	102 => "В Алидерии много веков уже бытует одна загадка: у чего ноги в земле, тело в воде, а голова в воздухе. Ответ на нее очень прост - это тростник. Заросли его укрывают берега Лидеры, предпочитая почти наполовину прятаться в воде. Однако беззаботно гулять в этом часто достигающем двух метров травянистом лесу вряд ли получится. Обитающие тут дикие камышовые коты считают просто своим долгом нападать на зазевавшихся магов, пуская в ход весь арсенал кошачьего оружия - когти, зубы и, конечно же, хвост. И, что самое удивительное, даже не боятся замочить лапы.",
	110 => "Река - Выход в Столицу - Описание",
	
	200 => "Каменная Стена - Описание",
	201 => "О том, что в древности в этой пещере был подводный грот, теперь говорит мало что. Сейчас воды здесь практически нет: тонкий слой ее толщиной чуть более ладони покрывает почти все пространство подземного зала. От прежней жизни остались большие залежи соли, от которых даже стены пещеры кажутся белыми. Добывать тут этот ценный ресурс нужно осторожно. Того и гляди вместо обломка породы можно получить ворох неприятностей в виде напавшего на Вас соляного истукана. Уж очень не любят эти господа, когда их растаскивают по кусочкам на разные нужды.",
	202 => "Небольшая пещера открывается взору забредшего в эти глубины путника. Входя сюда, голову лучше беречь. С потолка то тут, то там опускаются вниз огромные сталактиты. Удариться лбом о почти невидимую в темноте громадину легче легкого. Однако подвоха стоит ждать не только сверху. Обитающие тут кобольды известны тем, что устраивают в своем жилище настоящий лабиринт из ловушек. В любой момент под ногами исследователя может оказаться яма с кольями или из-за очередного обломка горной породы вылететь шайка самих пещерных жителей. Нужно быть начеку.",
	203 => "Казалось бы, откуда здесь взяться этой лестнице. Однако огромный колодец, уходящий отвесно вниз, явно служил дорогой куда-то. Дна видно не было. Высеченные в скале ступени по кругу спускались во тьму. Изредка на стене встречались держатели для факелов, подтверждая, что эта шахта много лет назад использовалась. Вот только куда она вела? У Вас появилось стойкое желание узнать, что там на дне колодца. Но насколько безопасным будет спуск, Вы не знали. Но стоп! Что это пролетело мимо Вас?",
	204 => "Огромный провал преграждает Вам путь. Пропасть, прорезанная в породе магмой, разделяет горный массив на два берега. На один только что вышли Вы, второго даже не было видно - настолько широк был провал. Такой не перепрыгнешь. Огненная река, протекая вдалеке внизу, опаляет жаром до самого потолка. С краю открывшейся Вам каменной террасы Вы заметили начало узкого деревянного моста. Веревочные перила колебались, движимые потоками горячего воздуха. Куда он вел - было неизвестно. Но явно на другой берег.",
	205 => "Широкое плато застывшей магмы уходит в даль, насколько позволяет зрение. Из трещин его местами устремляются вверх странные деревья, листья которых похожи на языки пламени. Коснувшись осторожно ствола одного из таких созданий местной флоры, Вы почувствовали исходящий от него холод, противоречащий пылающей кроне. Подняв глаза, Вы заметили, что потолка этого огромного пещерного зала не было видно. Он прятался где-то в вышине во мраке. Свет от факела и от листвы огненных деревьев не доставал до него. Что скрывается в этой темноте - неизвестно.",
	206 => "Небольшая Пещера - Описание",
	210 => "Подгорье - Выход в Столицу - Описание",
	
	300 => "У самого берега реки, на краю Проклятых Земель стоят дома, жители которых давно оставили их. На площади виден старый храм, слышно как порывы ветра выдувают стоны из его пустых окон. Когда-то здесь была деревня, а теперь это место называют Урочище Истра, и никому не следовало бы сюда приходить."
);

$sides = Array
(
"Северо-Запад",
"Север",
"Северо-Восток",
"Запад",
"Moo",
"Восток",
"Юго-Запад",
"Юг",
"Юго-Восток"
);

$sides2 = Array
(
"Северо-Западe",
"Северe",
"Северо-Востокe",
"Западe",
"Moo",
"Востокe",
"Юго-Западe",
"Югe",
"Юго-Востокe"
);


class ForestUtils
{
	var $location;

	function getTile( $x, $y )
	{
		$x = ( $x % 100 + 100 ) % 100;
		$y = ( $y % 100 + 100 ) % 100;
		$cell_id = $x * 100 + $y;
		
		if( $cell_id == 0 )
		{
			if ($this->location == 1) return 10;
			if ($this->location == 6) return 110;
			if ($this->location == 7) return 210;
		}
		
		$res = f_MQuery( "SELECT * FROM forest_tiles WHERE location = {$this->location} AND depth = $cell_id" );
		$arr = f_MFetch( $res );
		if( !$arr )
		{
			if ($this->location == 1) return 1;
			if ($this->location == 6) return 100;
			if ($this->location == 7) return 200;
		}
		return $arr[tile];
	}
	
	function setTile( $x, $y, $tile )
	{
		$x = ( $x % 100 + 100 ) % 100;
		$y = ( $y % 100 + 100 ) % 100;
		$cell_id = $x * 100 + $y;
		
		$res = f_MQuery( "SELECT * FROM forest_tiles WHERE location = {$this->location} AND depth = $cell_id" );
		if( !f_MNum( $res ) ) f_MQuery( "INSERT INTO forest_tiles ( location, depth, tile ) VALUES ( {$this->location}, $cell_id, $tile )" );
		else f_MQuery( "UPDATE forest_tiles SET tile = $tile WHERE location = {$this->location} AND depth = $cell_id" );
	}
	
	function ForestUtils( $loc )
	{
		$this->location = $loc;
	}
};

class ForestPlayerData
{
	var $player_id;
	var $steps;
	var $status;
	var $goto;
	
	function IncSteps( )
	{
		++ $this->steps;
		f_MQuery( "UPDATE player_forest_data SET steps = steps + 1 WHERE player_id = {$this->player_id}" );
	}
	
	function ClearSteps( )
	{
		$this->steps = 0;
		f_MQuery( "UPDATE player_forest_data SET steps = 0 WHERE player_id = {$this->player_id}" );
	}
	
	function SetStatus( $a )
	{
		$this->status = $a;
		f_MQuery( "UPDATE player_forest_data SET status = $a WHERE player_id = {$this->player_id}" );
	}
	
	function SetGoto( $a )
	{
		$this->goto = $a;
		f_MQuery( "UPDATE player_forest_data SET goto = $a WHERE player_id = {$this->player_id}" );
	}
	
	function CleanUp( )
	{
		f_MQuery( "DELETE FROM player_forest_data WHERE player_id = {$this->player_id}" );
	}
	
	function ForestPlayerData( $player_id )
	{
		$this->player_id = $player_id;
		f_MQuery( "LOCK TABLE player_forest_data WRITE" );
		$res = f_MQuery( "SELECT * FROM player_forest_data WHERE player_id = $player_id" );
		if( !f_MNum( $res ) )
		{
			f_MQuery( "INSERT INTO player_forest_data ( player_id ) VALUES ( $player_id )" );
			$this->steps = 0;
			$this->status = 0;
			$this->goto = 0;
		}
		else
		{
			$arr = f_MFetch( $res );
			$this->steps = $arr[steps];
			$this->status = $arr[status];
			$this->goto = $arr['goto'];
		}
		f_MQuery( "UNLOCK TABLES" );
	}
};

class ForestPlayerRiddle
{
	var $player_id;
	var $riddle;
	var $riddle_a;
	
	function CleanUp( )
	{
		f_MQuery( "DELETE FROM player_forest_riddle WHERE player_id = {$this->player_id}" );
	}
	
	function SetRiddle( $a, $b )
	{
		$this->riddle = $a;
		$this->riddle_a = $b;
		f_MQuery( "UPDATE player_forest_riddle SET riddle = '$a', riddle_a = '$b' WHERE player_id = {$this->player_id}" );
	}
	
	function ForestPlayerRiddle( $player_id )
	{
		$this->player_id = $player_id;
		f_MQuery( "LOCK TABLE player_forest_riddle WRITE" );
		$res = f_MQuery( "SELECT * FROM player_forest_riddle WHERE player_id = $player_id" );
		if( !f_MNum( $res ) )
		{
			f_MQuery( "INSERT INTO player_forest_riddle ( player_id ) VALUES ( $player_id )" );
		}
		else
		{
			$arr = f_MFetch( $res );
			$this->riddle = $arr[riddle];
			$this->riddle_a = $arr[riddle_a];
		}
		f_MQuery( "UNLOCK TABLES" );
	}
};


function GetTileImage( $tile, $depth=false )
{
	if( $depth == 600 ) return 'images/locations/altar.jpg';
	if( $tile == 0 || $tile == 10 ) return 'images/locations/opushka.jpg';
	if( $tile == 2 ) return 'images/locations/meadow.jpg';
	if( $tile == 5 ) return 'images/locations/river.jpg';
	else return 'images/empty.gif';
}

function GetTileImageLarge( $tile, $depth=false )
{
	if( $depth == 600 ) return 'images/locations/altar_.jpg';
	if( $tile == 0 || $tile == 10 ) return 'images/locations/opushka_.jpg';
	if( $tile == 2 ) return 'images/locations/meadow_.jpg';
	if( $tile == 5 ) return 'images/locations/river_.jpg';
	else return '';
}

function MakeStep( )
{
	global $fpd;
	global $player;
	global $till;
	global $loc;
	global $cur_tile;
	global $tm;
	global $x, $y;
	
	$fpd->IncSteps( );
	if (monstersCampAttack())
	{
		f_MQuery("LOCK TABLE player_triggers WRITE");
		$player->SetTrigger(12345, 0);
		f_MQuery("UNLOCK TABLES");
		die ("location.href='combat.php';");
	}
	if( $fpd->steps >= 20 && $cur_tile != 14 && $cur_tile != 7 )
	{
		$ttm=20;
		$player->SetTill( $tm + $ttm );
		$till = $tm + $ttm;
		$fpd->SetStatus( 1 );
		$fpd->ClearSteps( );
				
		include( "riddle_generator.php" );
		$rdg = new RiddleGenerator( );
		$rdg->Generate( );
		$fpr = new ForestPlayerRiddle( $player->player_id );
		$fpr->SetRiddle( $rdg->text, $rdg->number );
		$player->SetRegime( 150 );

		f_MQuery("LOCK TABLE player_triggers WRITE");
		$player->SetTrigger(12345, 0);
		f_MQuery("UNLOCK TABLES");

	}
	else if( mt_rand( 1, 5 ) == 1 )
	{
		$monsters = f_MFetch( f_MQuery( "SELECT * FROM forest_monster_camps WHERE min_level <= {$player->level} AND max_level >= {$player->level}" ) );
		if ($monsters && mt_rand(1,5) < 5) return;
		if ($cur_tile == 2 && $x == 95) return; // если на 45-й вертикали и это зачарованная поляна, то не атакуем
		
		$res = f_MQuery( "SELECT mob_id FROM mobs WHERE loc=$loc AND defend_depth = $cur_tile ORDER BY rand( )" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
			$attack = $arr['mob_id'];

			$player->syst( "Вы были атакованы на координатах <b>".(($x+50)%100).", $y</b>", false );
	
			include( "mob.php" );
		
			$iii=1;
			if ($attack==81)
			{
				if ($player->level<=9) $iii=1;
				elseif ($player->level<=12) $iii=2;
				elseif ($player->level<=18) $iii=3;
				else $iii=4;
			}
			for ($i=1;$i<=$iii;$i++)
			{
				$mob = new Mob;
				$mob->CreateMob( $attack, $loc, $player->depth );
				$mob->AttackPlayer( $player->player_id, 0, 0, true /* нападаем кроваво */ );
			}

			f_MQuery("LOCK TABLE player_triggers WRITE");
			$player->SetTrigger(12345, 0);
			f_MQuery("UNLOCK TABLES");

			die( "location.href = 'combat.php';" );
		}
	}
	f_MQuery("LOCK TABLE player_triggers WRITE");
	$player->SetTrigger(12345, 0);
	f_MQuery("UNLOCK TABLES");

}

function hares( )
{
	global $player;
	global $x;
	global $y;

	$hares = ( $player->HasTrigger( 19 ) );
	if( !$player->HasWearedItem( 220 ) ) $hares = false;
	if( $hares )
	{
		f_MQuery( "LOCK TABLES player_hare_coords WRITE" );
		$hres = f_MQuery( "SELECT * FROM player_hare_coords WHERE player_id={$player->player_id} AND expires > ".time( ) );
		$harr = f_MFetch( $hres );
		if( $harr )
		{
			// Проверим, вдруг заяц даже близко не тут
			if( $harr['x'] != $x || $harr['y'] != $y ) $hares = false;
    	}
		else
		{
			// С каким-то шансом родим зайца :о)
			if( mt_rand( 1, 5 ) == 1 )
			{
				$expires = time( ) + 120;
				$hare_x = $x;
				$hare_y = $y;
				f_MQuery( "DELETE FROM player_hare_coords WHERE player_id={$player->player_id}" );
				f_MQuery( "INSERT INTO player_hare_coords ( player_id, x, y, expires ) VALUES ( {$player->player_id}, $x, $y, $expires )" );
			}
			else $hares = false;
		}
		f_MQuery( "UNLOCK TABLES" );
	}

	return $hares;
}

function monstersCampAttack()
{
	global $depth, $player;
	
	f_MQuery( "LOCK TABLE forest_monster_camps WRITE" );
	$monsters = f_MFetch( f_MQuery( "SELECT * FROM forest_monster_camps WHERE cell_id=$depth" ) );
	if ($monsters && ( $monsters['min_level'] <= $player->level && $monsters['max_level'] >= $player->level && $monsters['combat_id'] != -1 || $player->player_id == 6825 ))
	{
		if ($monsters['combat_id'] == 0)
		{
			f_MQuery( "UPDATE forest_monster_camps SET combat_id = -1 WHERE cell_id=$depth" );
			f_MQuery( "UNLOCK TABLES" );
			include( "mob.php" );
			$mob = new Mob;
			$mob->CreateMob($monsters['mob_id'], 1, 10000);
			$mob->AttackPlayer( $player->player_id, 6, 1, true );
			f_MQuery( "UPDATE forest_monster_camps SET combat_id = {$mob->combat_id}, strazha_helper=".time( )." WHERE cell_id=$depth" );
			$ii=9;
			if ($monsters['mob_id']==73)
				if ((int)date("H") >=3 && (int)date("H") <=11) $ii=9;
				else $ii=14;
			if ($monsters['mob_id']==74) $ii=7;
			if ($monsters['mob_id']==71) $ii=6;
			if ($monsters['mob_id']==70) $ii=4;
			for ($i = 0; $i < $ii; ++ $i)
			{
    			$mob = new Mob;
    			$mob->CreateMob($monsters['mob_id'], 1, 10000);
    			$mob->AttackPlayer( $player->player_id, 6, 1, true );
			}
			setCombatTimeout( $mob->combat_id, 40 );
		}
		else
		{
			f_MQuery( "UNLOCK TABLES" );
			$whom = f_MValue("SELECT player_id FROM combat_players WHERE combat_id=$monsters[combat_id] AND side=1 LIMIT 1");
			include_once("create_combat.php");
			if (!ccAttackPlayer( $player->player_id, $whom, 0, true, true ))
			{
				echo("alert('$combat_last_error');");
				return false;
			}
			f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( $monsters[combat_id], '<b>{$player->login}</b> вмешивается в бой<br>' )" );
			f_MQuery( "UPDATE combat_players SET win_action=6, win_action_param=1 WHERE player_id={$player->player_id}" );
		}
	}
	else
	{
		f_MQuery( "UNLOCK TABLES" );
		return false;
	}
	return true;
}

function getItems( )
{
	global $cur_tile;
	global $player;
	$st = '';
	$res = f_MQuery( "SELECT * FROM forest_items WHERE cell_type = $cur_tile" );
	$val = mt_rand(0,99);
	$cur = 0;
	$ok = false;
	while( $arr = f_MFetch( $res ) )
	{
		$cur += $arr[chance];
		if( $cur > $val )
		{
			$st .= "Вы нашли <a href=help.php?id=1010&item_id=$arr[item_id] target=_blank><b>".getItemNameForm( $arr[item_id], "4" )."</b></a>";
			if( $arr[number] > 1 ) $st .= " ($arr[number])";
			$player->AddToLog( $arr[item_id], $arr[number], 9, 2 );
			$player->AddItems( $arr[item_id], $arr[number] );
			$ok = true;
			break;
		}
	}
		
	if( !$ok ) $st .= "Вы ничего не нашли";
	
	return $st;
}

function move_hare(	$dir )
{
	global $player;
	global $x;
	global $y;
	$nx = $x;
	$ny = $y;

	$zz = 0;
	for( $yy = -1; $yy <= 1; ++ $yy )
		for( $xx = -1; $xx <= 1; ++ $xx )
		{
			if( $zz == $dir )
			{
				$nx += $xx;
				$ny += $yy;
			}
            ++ $zz;
		}

	$nx = ( $nx + 100 ) % 100;
	$ny = ( $ny + 100 ) % 100;

	$expires = time( ) + 120;
	f_MQuery( "LOCK TABLES player_hare_coords WRITE" );
	f_MQuery( "DELETE FROM player_hare_coords WHERE player_id={$player->player_id}" );
	f_MQuery( "INSERT INTO player_hare_coords ( player_id, x, y, expires ) VALUES ( {$player->player_id}, $nx, $ny, $expires )" );
	f_MQuery( "UNLOCK TABLES" );
}

function isRazbojnik( )
{
	global $x; 
	global $y;
	global $player; 

    $razbojnik = false;
    if( $x == 5 && $y == 5 && $player->regime == 0 )
    {
        if( $player->HasTrigger( 73 ) ) return true; // кощейный квест
    	$res = f_MQuery(  "SELECT * FROM player_cooldowns WHERE player_id={$player->player_id} AND spell_id=109" );
    	$arr = f_MFetch( $res );
    	if( $arr && $arr[0] && !$player->HasTrigger( 42 ) && $player->GetQuestValue( 21 ) < 3 )
    	{
    		$razbojnik = true;
    	}
	}

    return $razbojnik;
}

function isStarikKosh( )
{
	global $x; 
	global $y;
	global $player; 

    $razbojnik = false;
    if( $x == 99 && $y == 0 && $player->regime == 0 )
    {
    	if( $player->HasTrigger( 70 ) && !$player->HasTrigger( 72 ) )
    	{
    		$razbojnik = true;
    	}
    }
    return $razbojnik;
}

function isLeavesKeeper( )
{
	global $x; 
	global $y;
	global $player; 

    $razbojnik = false;
    if( $x == 95 && $y == 63 && $player->regime == 0 )
    {
    	if( $player->HasTrigger( 200 ) && !$player->HasTrigger( 201 ) )
    	{
    		$razbojnik = true;
    	}
    }
    return $razbojnik;
}

function isMahjong( )
{
	global $x; 
	global $y;
	global $player; 

    $razbojnik = false;
    if( $x == 95 && $y == 58 && $player->regime == 0 )
    {
    	if( $player->HasTrigger( 201 ) && !$player->HasTrigger( 202 ) )
    	{
    		$razbojnik = true;
    	}
    }
    return $razbojnik;
}

?>
