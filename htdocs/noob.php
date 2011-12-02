<?

function add_noob_js( )
{
	echo "var n_els = new Array( );\n";
	echo "var n_pars = new Array( );\n";
	echo "function n_clear( ) { hideTooltip(); for( var i = 0; i < n_els.length; ++ i ) n_pars[i].removeChild( n_els[i] ); n_pars = new Array( ); n_els = new Array( ); }\n\n";
	echo "function follow(a){query( 'n_follow.php?a='+a,'' );}";
}

function do_noob( $parent, $x, $y, $abs, $txt, $follow = false, $fl = 0 )
{
	$abs = ( $abs ) ? 'absolute' : 'relative';
//	$ret = "<div style='z-index:150;position:$abs;top:{$y}px;left:{$x}px;width:282px;height:116px;'>";
	$ret .= "<table border=0 cellspacing=0 cellpadding=0 background=images/noob/ast.png width=282 height=116>";
	$ret .= "<tr><td style='width:108px;'>&nbsp;</td><td valign=top>";
	$ret .= "<div align=justify style='width:167px;height:100px;position:relative;top:7px;'><div id=n_text><small><b>".$txt."</b></small></div>";
	if( $follow ) $ret .= "<div id=n_follow style='position:absolute;right:2px;bottom:2px;'><a href='javascript:follow($fl)'><small><b>Дальше</b></small></a></div>";
	$ret .= "</div>";
	$ret .= "</td></tr>";
	$ret .= "</table>";
//	$ret .= "</div>";
	if( gettype($x) != "integer" || $x >= 0 ) $lft = "left='{$x}px'"; else $lft = "right='".(-$x)."px'";
	if( gettype($y) != "integer" || $y >= 0 ) $top = "top='{$y}px'"; else $top = "bottom='".(-$y)."px'";

	echo "el = document.createElement( 'div' ); el.style.zIndex=150; el.style.position='$abs'; el.style.$lft; el.style.$top; el.style.width='282px'; el.style.height='116px'; el.innerHTML = '".addslashes($ret)."'; _( '$parent' ).appendChild( el ); n_pars.push( _( '$parent' ) ); n_els.push( el );";
}

function strelka( $parent, $x, $y, $abs, $kind )
{
   	$abs = ( $abs ) ? 'absolute' : 'relative';
	$ret = "<img src=images/noob/{$kind}.gif>";
	if( gettype($x) != "integer" || $x >= 0 ) $lft = "left='{$x}px'"; else $lft = "right='".(-$x)."px'";
	if( gettype($y) != "integer" || $y >= 0 ) $top = "top='{$y}px'"; else $top = "bottom='".(-$y)."px'";

	echo "el = document.createElement( 'div' ); el.style.zIndex=150; el.style.position='$abs'; el.style.$lft; el.style.$top; el.innerHTML = '".addslashes($ret)."'; _( '$parent' ).appendChild( el ); n_pars.push( _( '$parent' ) ); n_els.push( el );";
}

function show_noob( $noob, $add = 0 )
{
	global $player;
    if( $noob == 1 )
    {
    	do_noob( 'capital_content', 10, 190, true, "Приветствую тебя, {$player->login}! Я - одна из демиургов этого мира. Меня зовут Астаниэль. Я интересно и увлекательно познакомлю тебя со сказочным миром Алидерии. Приготовься, тебя ждет сказка!", true, $noob);
    	strelka( 'capital_content', 295, 267, true, 'left' );
    }
    if( $noob == 2 )
    {
    	do_noob( 'capital_content', 10, 10, true, "Перед тобой столица Алидерии - Телла. Дома, леса, пещеры и т.д. Наведи мышку на любой объект. Если он подсветится, то кликнув по нему ты сможешь перейти в другую локацию. Но пока не спеши, задержимся еще немного здесь.", true, $noob);
    	strelka( 'capital_content', 295, 87, true, 'left' );
    }
    if( $noob == 3 )
    {
    	do_noob( 'fixedBlock', 90, 177, true, "А это ты. Ты видишь свой образ. Вокруг тебя ячейки для вещей. Покупая, добывая, выигрывая вещи ты можешь одеваться. От этого ты станешь сильнее, быстрее, да и красивее. Сейчас на тебе всего лишь дырявый плащ.", true, $noob);
    	strelka( 'fixedBlock', 375, 254, true, 'left' );
    }
    if( $noob == 4 )
    {
    	do_noob( 'capital_content', 10, 10, true, "Давай купим что-то, чтобы тебя одеть. Не гоже герою ходить почти голым. А чтобы что-то купить, нам нужно перейти в Торговый Дом. Для этого нужно кликнуть по зданию, на которое указывает стрелка.", false, $noob);
    	strelka( 'capital_content', 165, 180, true, 'right' );
    	echo "selectArray[\"Sel11\"][6] = 'game.php?dir=9&tloc=2';";
    }
    if( $noob == 5 )
    {
    	do_noob( 'allContent', -240, 50, true, "Прекрасно. Мы в торговом доме. Как ты видишь, здесь много отделов. Но все товары, которые нам нужны сейчас, продаются тут, недалеко от входа.", true, $noob);
    	strelka( 'allContent', -200, 127, true, 'left' );
    }
    if( $noob == 6 )
    {
    	$st = "Нам нужно тебя одеть. Для первого уровня выбор невелик, но все же. Купи ";
    	if( $add & 1 ) $st .= "<font color=green>ветвь</font>";
    	else $st .= "<span id=nf1 style='color:darkred'>ветвь</span>";
    	$st .= ', ';
    	if( $add & 2 ) $st .= "<font color=green>тряпку</font>";
    	else $st .= "<span id=nf2 style='color:darkred'>тряпку</span>";
    	$st .= ', ';
    	if( $add & 16 ) $st .= "<font color=green>тапки</font>";
    	else $st .= "<span id=nf16 style='color:darkred'>тапки</span>";
    	$st .= ' и ';
    	if( $add & 32 ) $st .= "<font color=green>варежки</font>";
    	else $st .= "<span id=nf32 style='color:darkred'>варежки</span>";
    	$st .= ". Да уж, не особо геройская одежда. Но это хоть что-то. Нажми на кнопку Купить возле каждой вещи.";

    	do_noob( 'fixedBlock', 700, 50, true, $st, false, $noob);
//    	strelka( _( 'allContent' ), -190, 40, true, 'right' );
    }
    if( $noob == 7 )
    {
    	echo "scroll(0,0);";
    	echo "if( document.all ) _('allContent').scrollTop = 0;";
    	do_noob( 'srchg2', -1, 60, true, "Ну вот. Мы потратили  почти все твои деньги. Правда, скоро ты будешь выглядеть как с иголочки. Только нужно одеть вещи на себя. Для этого перейди в свой инвентарь.", false, $noob);
    	strelka( 'srchg2', 20, 15, true, 'top' );
    }
    if( $noob == 8 )
    {
    	echo "position = getAp( _( 'nvimg153' ) );";
    	do_noob( 'allContent', "'+(55+position.x)+'", "'+(12+position.y)+'", true, "Давай попробуем одеть варежки. Для этого быстро щелкни на них дважды мышкой.", false, $noob);
    	strelka( 'allContent', "'+position.x+'", "'+(position.y-50)+'", true, 'bot' );
    }
    if( $noob == 9 )
    {
    	do_noob( 'fixedBlock', 300, 50, true, "Другой способ одеть вещь - ухватиться за нее мышкой и перетащить на соответствующее место возле персонажа.", true, $noob);
    	strelka( 'fixedBlock', 585, 127, true, 'left' );
    }
    if( $noob == 10 )
    {
    	echo "position = getAp( _( 'nvimg133' ) );";
    	do_noob( 'allContent', "'+(55+position.x)+'", "'+(12+position.y)+'", true, "Попробуй перетащить ветвь на место оружия. Если не получится, можешь одеть ее двойным щелчком мышки, как варежки.", false, $noob);
    	strelka( 'allContent', "'+position.x+'", "'+(position.y-50)+'", true, 'bot' );
    	strelka( 'fixedBlock', 68, 60, true, 'left' );
    }
    if( $noob == 11 )
    {
    	do_noob( 'fixedBlock', 400, 80, true, "Попробуй одеть все остальные вещи, а потом вернись к обзору текущей локации. Для этого надо нажать кнопку Обзор.", false, $noob);
    	strelka( 'srchg0', -75, "-15", true, 'right' );
    }
    if( $noob == 12 )
    {
    	do_noob( 'fixedBlock', 400, 80, true, "Давай вернемся на главную улицу города, чтобы пройти к пещерам, где тебя ждет твой первый бой.", false, $noob);
    	strelka( 'n_go_to_main_street', "-50", "-15", true, 'right' );
    }
    if( $noob == 13 )
    {
    	do_noob( 'capital_content', 10, 10, true, "Пещеры - это место, полное монстров. Едва ли герой, жаждущий битвы, останется недовольным после их посещения. Проследуй к ним.", false, $noob);
    	strelka( 'capital_content', 155, 160, true, 'left' );
    	echo "selectArray[\"Sel8\"][6] = 'game.php?dir=5&tloc=2';";
    }
    if( $noob == 14 )
    {
    	do_noob( 'n_go_to_dungeon', -250, "0", true, "Сейчас ты находишься у входа в пещеры. Чтобы спуститься в них, нажми на соответствующий переход.", false, $noob);
    	strelka( 'n_go_to_dungeon', "-50", "-15", true, 'right' );
    }
    if( $noob == 15 )
    {
    	do_noob( 'go_further', "-360", "0", true, "Ты находишься на нулевой глубине пещер. Здесь еще достаточно безопасно, монстры подземелий не решаются подходить так близко к свету.", true, $noob);
    	strelka( 'go_further', "-75", "77", true, 'left' );
    }
    if( $noob == 16 )
    {
    	do_noob( 'go_further', "-385", "10", true, "Обрати внимание на незнакомца, который здесь сидит. После того, как мы проведем с тобой первый бой, я советую тебе вернуться сюда и поговорить с ним. Возможно, у него есть задание для тебя.", true, $noob);
    	strelka( 'go_further', "-100", "87", true, 'left' );
    }
    if( $noob == 17 )
    {
    	do_noob( 'go_further', "-360", "0", true, "Пока ты не достигнешь третьего уровня, все доступные задания в основном будут заключаться в поиске вещей и убийстве монстров. С третьего уровня доступны гораздо более интересные приключения.", true, $noob);
    	strelka( 'go_further', "-75", "77", true, 'left' );
    }
    if( $noob == 18 )
    {
    	do_noob( 'go_further', "-370", "20", true, "Не зависимо от того, где ты находишься: в городе, в пещерах, в лесу или на реке - все команды навигации всегда находятся справа сверху. Спустись глубже в пещеры.", false, $noob);
    	strelka( 'go_further', "-50", "-15", true, 'right' );
    	echo "ready_to_go_further = true;";
    }
    if( $noob == 19 )
    {
    	do_noob( 'my_login', 300, 45, true, "Итак, ты в бою. Система боя в Алидерии очень простая, в основе ее лежат заклинания трех стихий: воды, природы и огня. В отличие от многих других игр, в Алидерии стихия заклинания играет первичную роль в бою.", true, $noob);
    	strelka( 'my_login', 575, 122, true, 'left' );
    }
    if( $noob == 20 )
    {
    	do_noob( 'my_login', 315, 40, true, "Сейчас у тебя доступно три заклинания, по одному для каждой стихии. Каждое из этих заклинаний просто наносит урон противнику. Начиная со второго уровня, тебе будут доступны более интересные заклинания.", true, $noob);
    	strelka( 'my_login', 590, 117, true, 'left' );
    }
    if( $noob == 21 )
    {
    	do_noob( 'my_login', 290, 35, true, "Давай перейдем от теории к практике. Попробуй сколдовать заклинание Стрела Огня. Это сильное заклинание первого уровня, и оно может здорово пощипать твоего врага.", false, $noob);
    	strelka( 'crds56', 100, 50, true, 'right' );
    }
    if( $noob == 22 )
    {
    	do_noob( 'last_turn', "-375", 80, true, "Ты можешь наблюдать, как твоя Стрела Огня и заклинание оппонента летят друг на друга. Но в центре твое заклинание задерживается немного дольше.", true, $noob);
    	strelka( 'last_turn', "-90", 157, true, 'left' );
    }
    if( $noob == 23 )
    {
    	do_noob( 'last_turn', "-365", 85, true, "Это значит, что ты сколдовал заклинание, а твой оппонент&nbsp;- нет. Обрати внимание, как образ оппонента окрасился в красный&nbsp;- это урон, который ты ему нанес. Но почему его заклинание не возымело эффекта?", true, $noob);
    	strelka( 'last_turn', "-80", 162, true, 'left' );
    }
    if( $noob == 24 )
    {
    	do_noob( 'last_turn', "-375", 80, true, "Потому что твое заклинание имеет стихию Огня, а заклинание оппонента&nbsp;- стихию Природы. Стихия Огня сильнее, чем стихия Природы, а стихия Природы сильнее, чем стихия Воды.", true, $noob);
    	strelka( 'last_turn', "-90", 157, true, 'left' );
    }
    if( $noob == 25 )
    {
    	do_noob( 'last_turn', "-385", 90, true, "Ты можешь спросить меня: &laquo;Если огонь сильнее природы, а природа сильнее воды, почему бы мне всегда не колдовать заклинания огня?&raquo;", true, $noob);
    	strelka( 'last_turn', "-100", 167, true, 'left' );
    }
    if( $noob == 26 )
    {
    	do_noob( 'last_turn', "-375", 80, true, "Потому что стихия Воды, в свою очередь, сильнее, чем стихия Огня. Это может напомнить известную игру Камень-Ножницы-Бумага - только в роли камня, ножниц и бумаги выступают различные стихии.", true, $noob);
    	strelka( 'last_turn', "-90", 157, true, 'left' );
    }
    if( $noob == 27 )
    {
    	do_noob( 'last_turn', "-365", 85, true, "Давай попробуем теперь сколдовать Ледяную Стрелу.", false, $noob);
    	strelka( 'crds56', 100, 50, true, 'left' );
    }
    if( $noob == 28 )
    {
    	do_noob( 'last_turn', "-375", 80, true, "И ты и твой оппонент выбрали стихию воды. В этом случае ни один из вас не колдует заклинания, но, в тоже время, вы оба получаете небольшой урон.", true, $noob);
    	strelka( 'last_turn', "-90", 157, true, 'left' );
    }
    if( $noob == 29 )
    {
    	do_noob( 'last_turn', "-365", 90, true, "Твоя задача, нанося урон оппоненту, лишить его всего здоровья раньше, чем тоже самое сделает он. Сейчас у твоего оппонента 20 здоровья, это видно под его изображением.", true, $noob);
    	strelka( 'last_turn', "-80", 167, true, 'left' );
    }
    if( $noob == 30 )
    {
    	do_noob( 'last_turn', "-355", 100, true, "Любое из доступных тебе сейчас заклинаний нанесет 25 урона, если ты сумеешь его сколдовать. Давай проверим нашу удачу, выбрав Каменный Удар.", false, $noob);
    	strelka( 'crds57', 100, 50, true, 'right' );
    }
    if( $noob == 31 )
    {
    	do_noob( 'last_turn', "-355", 100, true, "В этот раз нам не повезло, твой оппонент выбрал стихию Огня и сколдовал заклинание, которое немного пощипало тебя.", true, $noob);
    	strelka( 'last_turn', "-70", 177, true, 'left' );
    }
    if( $noob == 32 )
    {
    	do_noob( 'last_turn', "-365", 80, true, "Давай попробуем в очередной раз выбрать заклинание Каменный Удар.", false, $noob);
    	strelka( 'crds57', 100, 50, true, 'right' );
    }
    if( $noob == 33 )
    {
    	do_noob( 'last_turn', "-355", 100, true, "Какая удача, ты смог прочитать заклинание, и выиграл бой. Ты получил небольшое количество опыта - точное количество можно увидеть внизу в системном чате.", true, $noob);
    	strelka( 'last_turn', "-70", 177, true, 'left' );
    }
    if( $noob == 34 )
    {
    	do_noob( 'last_turn', "-355", 100, true, "Когда ты наберешь 75 опыта, ты получишь второй уровень развития персонажа, на котором тебе отроется большое количество новых возможностей.", true, $noob);
    	strelka( 'last_turn', "-70", 177, true, 'left' );
    }
    if( $noob == 35 )
    {
    	do_noob( 'last_turn', "-345", 110, true, "Чтобы набирать опыт, исследуй первую и вторую глубину пещер. Не спеши спускаться глубже, монстры на третьей глубине могут быть слишком сильными для тебя.", true, $noob);
    	strelka( 'last_turn', "-60", 187, true, 'left' );
    }
    if( $noob == 36 )
    {
    	do_noob( 'last_turn', "-365", 80, true, "Сейчас я советую тебе вернуться в город и поисследовать его, ты найдешь несколько жителей, которые, возможно, имеют разные задания для тебя.", true, $noob);
    	strelka( 'last_turn', "-80", 157, true, 'left' );
    }
    if( $noob == 37 )
    {
    	do_noob( 'last_turn', "-355", 70, true, "На этом я вынуждена покинуть тебя, меня ждут другие новички. Удачи и до встречи!", true, $noob);
    	strelka( 'last_turn', "-70", 147, true, 'left' );
    }
}

?>
