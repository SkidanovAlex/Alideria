<?
/* @author = undefined
 * @version = 1.0.0.1
 * @date = 12 февраля 2011
 * @about = основной контролирующий звездолёт, который в основном контролирует
 */

	// Информация о кодировке нашего многонационального сервера
	Header( 'Content-Type: text/html; charset=cp1251' );

	// Подключение заголовочных файлов
	require_once( '../functions.php' );	// Клоака большинства технических функций игры
	require_once( '../player.php' );		// Душераздирающий класс, призванный выполнять действия над Пользователем

	// Проверка на принадлежность к Сану
	if( !check_cookie( ) )
	{
		die( );	
	}
	else
	{
		$Demiurg = new Player( $_COOKIE['c_id'] );

		if( $Demiurg->Rank( ) != 1 )
		{
			die( );
		}
	}
	
	// Массив заголовков сервисов {заодно используется для валидации загружаемого сервиса}
	$services = array( );
	$services['main'] = 'Админочка';
	// Персонажи
	$services['players-teleport'] = 'Телепорт';
	$services['players-freedom'] = 'Освобождение';
	$services['players-moders'] = 'Модераторы';
	$services['players-clearinfo'] = 'Чистильщик инфы';
	$services['players-penalty'] = 'Штрафователь';
	// Статистики
	$services['statistic-online'] = 'Онлайн игроков';
	$services['statistic-doubloons'] = 'Дублонные богачи';
	$services['statistic-locations'] = 'Посещаемость локаций';
	$services['statistic-regs'] = 'Статистика регистраций';
	$services['statistic-quests'] = 'Прохождение квестов';
	// Деньги
	$services['finance-report'] = 'Финансовый отчёт';
	$services['finance-partner'] = 'Отчёт партнёрки';
	$services['finance-talants-move'] = 'Движения талантов';
	// Тулзы
	$services['tools-premiator'] = 'Премиатор';
	$services['tools-gifter'] = 'Одаритель';
	$services['tools-changeClanLeader'] = 'Смена главы клана';
	$services['tools-deleteClan'] = 'Удалить Орден';
	$services['tools-effectools'] = 'Эффектитель';
	
	// На какую страничку пришли?
	$serviceIdentity = ( $services[$_GET['service']] ) ? $_GET['service'] : 'main';
	$serviceTitle = $services[$serviceIdentity];
?>
<html>
<head>
	<title><?=$serviceTitle.' - '?>Оружие Богов</title>
	<link rel="stylesheet" type="text/css" href="/css/default.css" />
	<link rel="stylesheet" type="text/css" href="/css/tgm.css" />
	<script src="/js/jquery/main.js"></script>
	<script src="/js/tgm/main.js"></script>
</head>
<body>
	<div id="head" class="container">
		<div id="title">
			<?=$serviceTitle?>
		</div>
		<div id="menu">
			<a href="#" id="menuEditors" onmouseover="submenu.editors.show( )">Редакторы</a>
			<div class="submenu" id="submenuEditors" onmouseout="submenu.editors.hide( event )">
				...
			</div>
			<a href="#" id="menuQuests" onmouseover="submenu.quests.show( )">Квесты</a>
			<div class="submenu" id="submenuQuests" onmouseout="submenu.quests.hide( event )">
				...
			</div>
			<a href="#" id="menuLocations" onmouseover="submenu.locations.show( )">Локации</a>
			<div class="submenu" id="submenuLocations" onmouseout="submenu.locations.hide( event )">
				...
			</div>
			<a href="#" id="menuPlayers" onmouseover="submenu.players.show( )">Персонажи</a>
			<div class="submenu" id="submenuPlayers" onmouseout="submenu.players.hide( event )">
				<a href="?service=players-teleport">Телепорт</a><br />
				<a href="?service=players-freedom">Освобождение</a><br />
				<a href="?service=players-moders">Модераторы</a><br />
				<a href="?service=players-clearinfo">Чистильщик инфы</a><br />
				<a href="?service=players-penalty">Штрафователь</a><br />
			</div>
			<a href="#" id="menuMoney" onmouseover="submenu.money.show( )">Деньги</a>
			<div class="submenu" id="submenuMoney" onmouseout="submenu.money.hide( event )">
				<a href="?service=finance-report">Финансовый отчёт</a><br />
				<a href="?service=finance-partner">Отчёт партнёрки</a><br />
				<a href="?service=finance-talants-move">Движения талантов</a><br />
			</div>
			<a href="#" id="menuStatistic" onmouseover="submenu.statistic.show( )">Статистики</a>
			<div class="submenu" id="submenuStatistic" onmouseout="submenu.statistic.hide( event )">
				<a href="?service=statistic-online">Онлайн игроков</a><br />
				<a href="?service=statistic-doubloons">Дублонные богачи</a><br />
				<a href="?service=statistic-locations">Посещаемость локаций</a><br />
				<a href="?service=statistic-regs">Статистика регистраций</a><br />
				<a href="?service=statistic-quests">Прохождение квестов</a><br />
			</div>
			<a href="#" id="menuTools" onmouseover="submenu.tools.show( )">Тулзы</a>
			<div class="submenu" id="submenuTools" onmouseout="submenu.tools.hide( event )">
				<a href="?service=tools-premiator">Премиатор</a><br />
				<a href="?service=tools-gifter">Одаритель</a><br />
				<a href="?service=tools-changeClanLeader">Смена главы клана</a><br />
				<a href="?service=tools-deleteClan">Удалить Орден</a><br />
				<a href="?service=tools-effectools">Эффектитель</a><br />
			</div>
		</div>
		<div style="clear: both"></div>
	</div>
	<script>
		var submenu = {};
		
		submenu.editors = new Submenu( $( '#menuEditors' ), $( '#submenuEditors' ) );
		submenu.quests = new Submenu( $( '#menuQuests' ), $( '#submenuQuests' ) );
		submenu.locations = new Submenu( $( '#menuLocations' ), $( '#submenuLocations' ) );
		submenu.players = new Submenu( $( '#menuPlayers' ), $( '#submenuPlayers' ) );
		submenu.money = new Submenu( $( '#menuMoney' ), $( '#submenuMoney' ) );
		submenu.statistic = new Submenu( $( '#menuStatistic' ), $( '#submenuStatistic' ) );
		submenu.tools = new Submenu( $( '#menuTools' ), $( '#submenuTools' ) );		
	</script>
	<div id="content" class="container">
		<?
			// Подключение основного кода сервиса
			if( !include_once( './services/'.$serviceIdentity.'.php' ) )
			{
				echo '- Хьюстон, у нас проблема.<br /> - Что у вас?<br />- Кажется, взорвался модуль <b>'.$serviceIdentity.'.</b><br />- PSSSSHHHHHHHHHHHHHHHH<br />- PSSHHHHHHHHHH<br />- PSSSHHHHHHHHHHHHH';
			}		
		?>
	</div>
</body>
</html>
