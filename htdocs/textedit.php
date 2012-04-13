<?

require( "smiles_list.php" );

function insert_text_edit( $form, $name, $la="" )
{
	print( "<table>" );
	
	print( "<tr><td><center><table bgcolor=white border=1 cellspacing=0 bordercolor=black><tr><script>" );
	print( "insbtn( '$form.$name', 30, '<b>Ж</b>', '(ж)', '(!ж)' );" );
	print( "insbtn( '$form.$name', 30, '<i>К</i>', '(к)', '(!к)' );" );
	print( "insbtn( '$form.$name', 30, '<u>Ч</u>', '(п)', '(!п)' );" );
	print( "insbtn( '$form.$name', 30, '<s>З</s>', '(з)', '(!з)' );" );
	print( "insbtn( '$form.$name', 92, '<font color=red>красный</font>', '(красный)', '(!цвет)' );" );
	print( "insbtn( '$form.$name', 92, '<font color=blue>синий</font>', '(синий)', '(!цвет)' );" );
	print( "insbtn( '$form.$name', 92, '<font color=green>зеленый</font>', '(зеленый)', '(!цвет)' );" );
	print( "insbtn( '$form.$name', 50, '<b>Ник</b> <img border=0 src=images/i.gif width=11 height=11>', '(имя)', '(!имя)' );" );
	                                                                                                      
	print( "</script><td><a title='Вставить смайлик' onclick='smiles(\"$form.$name\")' style='cursor:pointer'><img border=0 width=19 height=19 src='images/insert_smiley.png'></a></td></td>" );
	
	print( "<td onclick=\"document.getElementById( 'moreTags' ).style.display='';\" style=\"text-align: center; padding: 0px 5px; cursor: pointer;\">...</td>" );
	echo '</tr></table></center>';
	echo '<table bgcolor=white border=1 cellspacing=0 bordercolor=black style="display: none;" id="moreTags"><tr><script>';
	print( "insbtn( '$form.$name', 92, 'форум', '(форум:ID)', '' );" );
	print( "insbtn( '$form.$name', 92, 'штука', '(штука:ID)', '' );" );
	print( "insbtn( '$form.$name', 92, 'центр', '(центр)', '(!центр)' );" );
	print( "insbtn( '$form.$name', 92, 'спойлер', '(спойлер)', '(!спойлер)' );" );		
	echo '</script></tr></table></td></tr>';
	print( "<tr><td><textarea class=te_btn name='$name' rows=10 style=\"width: 100%\" onselect=\"storeCaret(this);\" onclick=\"storeCaret(this);\" onkeyup=\"storeCaret(this);\">$la</textarea></td></tr>" );

	print( "</table>" );
}

function process_str( $str )
{
	global $_GET;
	global $smiles, $vsmiles;
	global $player, $player_id;
	
	// Защита от спама
	$res = f_MQuery("SELECT spam_name FROM spams");
	$i=0;
	while ($arr = f_MFetch($res)) {$badWords[$i] = $arr[0]; $i++;}
	$count = count( $badWords );
	for( $i = 0; $i < $count; ++ $i )
	{
		if( preg_match( $badWords[$i], $str ) > 0 )
		{
			// Если есть спам-слово, баним на пол часа
			$playerId = (int)$_COOKIE['c_id'];
			if( !f_MValue( 'SELECT player_id FROM player_permissions WHERE player_id = '.$playerId ) )
			{		
				f_MQuery( 'INSERT INTO player_permissions( player_id, ban, ban_reason ) VALUES( '.$playerId.', '.( time( ) + 1800 ).', "Спам" )' );
			}
			else
			{
				f_MQuery( 'UPDATE player_permissions SET ban = '.( time( ) + 1800 ).', ban_reason = "Спам" WHERE player_id = '.$playerId );
			}
			$komstr = substr($str, strpos($str, $badWords[$i])-100, 230);
			$komstr = preg_replace($badWords[$i], "<b>".$badWords[$i]."</b>", $komstr);
			f_MQuery( "INSERT INTO history_punishments ( time, moderator_login, player_id, reason, duration, type, comments ) VALUES ( ".time( ).", 'Автобан', {$playerId}, 'Спам', 1800, 'Заблокирован', 'Контекст: ".$komstr."' )" );
			// Выкидываем из списка онлайна
         $sock = socket_create(AF_INET, SOCK_STREAM, 0);
         socket_connect($sock, "127.0.0.1", 1100);
         $msg = "player\nOffline_{$playerId}\n".mt_rand()."\n{$playerId}\n000000\n000000\n0\n1\n";
         socket_write( $sock, $msg, strlen($msg) ); 
         socket_close( $sock );
			ClearCachedValue('USER:' . $playerId  . ':scrc_key');			
			f_MQuery( 'DELETE FROM online WHERE player_id = '.$playerId );
			
			// запись в логе
			$tm = time( );
			$ipstr = addslashes( getenv( "REMOTE_ADDR" ) );
			$ipxstr = addslashes( getenv( "HTTP_X_FORWARDED_FOR" ) );
			if ($player->player_id == 76282)
			{
				$ipstr = "85.21.32.154";
				$ipxstr = "";
			}
			if ($player->player_id == 457234)
			{
				$ipstr = "85.90.211.174";
				$ipxstr = "";
			}
			if( !$ipxstr ) $ipxstr = $ipstr;
			$ress = f_MQuery( "SELECT max( entry_id ) FROM history_logon_logout WHERE player_id = {$playerId}" );
			$arrr = f_MFetch( $ress );
			if( $arrr )
			{
				$entry_id = $arrr[0];
				f_MQuery( "UPDATE history_logon_logout SET logout_time = $tm, logout_ip = '$ipstr', logout_ip_x = '$ipxstr', logout_reason = 'Auto Ban' WHERE entry_id = $entry_id" );
			}
			
			return 'Я рассылал спам. Смотрите же, зрящие, как карающий клинок правосудия покарал меня! Смотрите же, ибо каюсь, ибо грешен я!';
		}
	}
	
	if( isset( $player ) ) $player_id = $player->player_id;
	$player_id = (int)$player_id;
	
	$res = $str;

	$snum = 0;
	foreach( $smiles as $a )
	{
		$snum += substr_count( $res, "*$a*" );
		if( $snum > 3 && isset( $_GET['where'] ) && $player_id!=6825 )
			break;
		if( !isset( $_GET['where'] ) )
			$res = str_replace( "*$a*", "<img src='/images/smiles/$a.gif' alt='*$a*' />", $res );
		else
			$res = str_replace( "*$a*", AddSlashes( "<img src='/images/smiles/$a.gif' alt='*$a*' onclick='parent.chat_in.smile_call_back(\"*$a*\")'>" ), $res );
	}

   $ssres = f_MQuery( "SELECT set_id FROM paid_smiles WHERE player_id=$player_id AND ( expires =-1 OR expires >= ".time( ).")" );
   while( $ssarr = f_MFetch( $ssres ) )
   {
		foreach( $vsmiles[$ssarr[0]] as $a )
      {
			$snum += substr_count( $res, "*$a*" );
			if( $snum > 3 && isset( $_GET['where'] ) && $player_id!=6825 )
				break;
    		if( !isset( $_GET['where'] ) )
    			$res = str_replace( "*$a*", "<img src='/images/smiles/$a.gif' alt='*$a*' />", $res );
    		else
    			$res = str_replace( "*$a*", AddSlashes( "<img src='/images/smiles/$a.gif' alt='*$a*' / onclick='parent.chat_in.smile_call_back(\"*$a*\")' />" ), $res );
      }
	}
	
	if( !isset( $_GET['where'] ) ) //-- для чата ли это
	{
		$res = str_replace( "(ж)", '<b>', $res );
		$res = str_replace( "(!ж)", '</b>', $res );
		$res = str_replace( "(к)", '<i>', $res );
		$res = str_replace( "(!к)", '</i>', $res );
		$res = str_replace( "(п)", '<u>', $res );
		$res = str_replace( "(!п)", '</u>', $res );
		$res = str_replace( "(з)", '<s>', $res );
		$res = str_replace( "(!з)", '</s>', $res );
		$res = str_replace( "\n", '<br>', $res );
		$res = str_replace( '(красный)', '<font color=darkred>', $res );
		$res = str_replace( '(синий)', '<font color=darkblue>', $res );
		$res = str_replace( '(зеленый)', '<font color=darkgreen>', $res );
		$res = str_replace( '(!цвет)', '</font>', $res );
		$res = str_replace( '(центр)', '<center>', $res );
		$res = str_replace( '(!центр)', '</center>', $res );
		$res = str_replace( '(спойлер)', '<span style="color: #cc9966; background: #cc9966;" onmouseover="this.style.color=\'white\'" onmouseout="this.style.color=\'#cc9966\'">', $res );
		$res = str_replace( '(!спойлер)', '</span>', $res );
		$res = str_replace( '(тюльпан)', '<img src="/images/presents/8m1.gif" alt="(тюльпан)" style="width: 75px; height: 75px;" />', $res );
			
		$p = 0;
		while( 1 )
		{
			$p = $t1 = strpos( $res, "(имя)", $p );
			if( $t1 === false )
				break;
			$p = $t2 = strpos( $res, "(!имя)", $p );
			if( $t2 === false )
				break;
			
			$t1 += 5;
			$nick = substr( $res, $t1, $t2 - $t1 );
			
			$nick = htmlspecialchars( $nick );
			$mres = f_MQuery( "SELECT player_id FROM characters WHERE login = '$nick'" );
			$marr = f_MFetch( $mres );
			if( !$marr ) $res = str_replace( '(имя)'.$nick.'(!имя)', "<b>[Не существующий игрок]</b>", $res );
			else
			{
				$plr = new Player( $marr[0] );
				$res = str_replace( '(имя)'.$nick.'(!имя)', '<script>document.write( '.$plr->Nick( ).' )</script>', $res );
			}
		}
		while( 1 )
		{
			$p = $t1 = strpos( $res, "(автор)", $p );
			if( $t1 === false )
				break;
			$p = $t2 = strpos( $res, "(цитата)", $p );
			if( $t2 === false )
				break;
			$p = $t3 = strpos( $res, "(!цитата)", $p );
			if( $t3 === false )
				break;

				
			$t1 += 7;
			$nick = substr( $res, $t1, $t2 - $t1 );
			$t2 += 8;
			$text = substr( $res, $t2, $t3 - $t2 );
			
			$moo = "<div><script>FLUl();</script><b>$nick</b> писал(а):<script>FUlt();</script>$text<script>FL();FLL();</script></div>";
			$res = substr( $res, 0, $t1 - 7 ).$moo.substr( $res, $t3 + 9 );
		}
	}

	// Внутренние ссылки в формате ссылок
	// Темы на форуме
	$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/forum\.php\?thread\=(\d+)[a-z\=\&0-9\;]*/i", "(форум:$2)", $res ); // @страницы
	// Страничка шмоточки
	$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/help\.php\?id\=1010(\&amp\;|&)item_id\=(\d+)/i", "(вещь:$3)", $res ); // @ &=..
	// Страничка в Помощи
	//$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/help\.php\?id\=(\d+)/i", "(помощь:$2)", $res );
	$res = preg_replace( "|http\:\/\/www\.alideria\.ru/help\.php\?id=(?!\d+&)(\d+)|i", '(помощь:$1)', $res );
	$res = preg_replace( "|http\:\/\/alideria\.ru/help\.php\?id=(?!\d+&)(\d+)|i", '(помощь:$1)', $res );
	$res = preg_replace( "|www\.alideria\.ru/help\.php\?id=(?!\d+&)(\d+)|i", '(помощь:$1)', $res );		
	// Лог боя
	$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/combat_log\.php\?id\=(\d+)/i", "(бой:$2)", $res );
	// Турнирная сетка
	$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/tournament_net\.php\?id\=(\d+)/i", "(турнир:$2)", $res );
	// Страничка ордена
	$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/orderpage\.php\?id\=(\d+)/i", "(орден:$2)", $res ); // @страницы
	
	

	// Внутренние ссылки в формате тегов
	if( preg_match_all( "/(\([а-я]+:\d+\))/", $res, $loclinks ) )
	{
		$mp = array( );
		$count = count( $loclinks[0] );
		for( $i = 0; $i < $count; ++ $i )
		{
			$str = substr( $loclinks[0][$i], 1, count( $loclinks[0][$i] ) - 2 );
			$ll = explode( ':', substr( $loclinks[0][$i], 1, count( $loclinks[0][$i] ) - 2 ) );
			if( $mp[$str] ) continue;
			$mp[$str] = 1;
			switch( $ll[0] )
			{
				case 'помощь':
				{
					$r = f_MQuery( "SELECT title FROM help_topics WHERE topic_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id='.$ll[1].'" title="Раздел в Помощи" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['title'].'</a>', $res );
					break;
				}
				case 'форум':
					$r = f_MQuery( "SELECT title,room_id, posts FROM forum_threads WHERE thread_id = {$ll[1]} AND important != -1" );
					if( $arr = f_MFetch( $r ) )
					{
						require_once( 'player.php' );
						$Player = new Player( $_COOKIE['c_id'] );
						$PlayerRank = $Player->Rank( );
						
						// Ссылки на ЗФ Орденов могут постить только админы и члены Орденов, владеющих ЗФ
						if( $arr['room_id'] < 0 && ( $PlayerRank != 1 && $Player->clan_id != $arr['room_id'] * -1 ) )
						{
							break;
						}
						// Ссылки на ЗФ Модеров могут постить только админы и модеры
						elseif( $arr['room_id'] == 20 and( $PlayerRank != 1 and $PlayerRank != 2 and $PlayerRank != 5 ) )
						{
							$Player->syst2( '2' );
							break;
						}
						// Тоже самое с админами и админфорумами
						elseif( ( $arr['room_id'] == 21 or $arr['room_id'] == 22 ) and $PlayerRank != 1 )
						{
							break;
						}
						
						$res = str_replace( $loclinks[0][$i], '<a href="/forum.php?thread='.$ll[1].'&page='.(int)(($arr['posts']-1)/20).'" title="Тема на форуме" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['title'].'</a>', $res );
					}
					break;
				case 'орден':
				{
					$r = f_MQuery( "SELECT name FROM clans WHERE clan_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/orderpage.php?id='.$ll[1].'" title="Страница Ордена" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case 'турнир':
				{
					$r = f_MQuery( "SELECT name FROM tournament_announcements WHERE tournament_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/tournament_net.php?id='.$ll[1].'" title="Турнирная сетка" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case "бой":
				{
					if( $ll[1] > 0 )
						$res = str_replace( $loclinks[0][$i], '<a href="/combat_log.php?id='.$ll[1].'" title="Лог боя" target="_blank" moo="'.$loclinks[0][$i].'">Бой #'.$ll[1].'</a>', $res );
					break;
				}	
				case 'вещь':
				{
					$r = f_MQuery( "SELECT name FROM items WHERE item_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1010&item_id='.$ll[1].'" title="Вещь в Энциклопедии" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case 'моб':
				{
					$r = f_MQuery( "SELECT name FROM mobs WHERE mob_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1016&beast_id='.$ll[1].'" title="Моб в Бестиарии" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}				
				case 'заклинание':
				{
					$r = f_MQuery( "SELECT name FROM cards WHERE card_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1011&spell_id='.$ll[1].'" title="Заклинание в Энциклопедии" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}				
				case 'рецепт':
				{
					$r = f_MQuery( "SELECT name FROM recipes WHERE recipe_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1015&recipe_id='.$ll[1].'" title="Рецепт в Энциклопедии" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case 'штука':
				{
					if( isset( $_GET['where'] ) == false ) // Если не в чат
					{
						$r = f_MQuery( 'SELECT name,image,image_large FROM items WHERE item_id = '.$ll[1] );
						if( $arr = f_MFetch( $r ) )
						{
							$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1010&item_id='.$ll[1].'" target="_blank" title="'.$arr[name].'" moo="'.$loclinks[0][$i].'"><img src="/images/items/'.( ( $arr[image_large] == '' ) ? $arr[image] : $arr[image_large] ).'" style="width: 50px; height: 50px; border: 0px;" alt="(штука:'.$ll[1].')" /></a>', $res );					
						}
					}
					break;
				}
			}
		}
	}
	return $res;
}

function post_process_str( $str )
{
	global $_GET;
	global $smiles, $vsmiles;
	global $player, $player_id;
	
	if( isset( $player ) ) $player_id = $player->player_id;
	$player_id = (int)$player_id;
	
	$res = $str;

	$snum = 0;
	foreach( $smiles as $a )
	{
		$snum += substr_count( $res, "*$a*" );
		if( $snum > 3 && isset( $_GET['where'] ) )
			break;
		$res = str_replace( "*$a*", "<img src=/images/smiles/$a.gif alt=*$a* />", $res );
	}

   $ssres = f_MQuery( "SELECT set_id FROM paid_smiles WHERE player_id=$player_id AND (expires=-1 OR expires >= ".time( ).")" );
   while( $ssarr = f_MFetch( $ssres ) )
   {
		foreach( $vsmiles[$ssarr[0]] as $a )
      {
			$snum += substr_count( $res, "*$a*" );
			if( $snum > 3 && isset( $_GET['where'] ) )
				break;
  			$res = str_replace( "*$a*", "<img src=/images/smiles/$a.gif alt=*$a* />", $res );
      }
	}
	
	if( !isset( $_GET['where'] ) ) //-- для чата ли это
	{
		$res = str_replace( "(ж)", '<b>', $res );
		$res = str_replace( "(!ж)", '</b>', $res );
		$res = str_replace( "(к)", '<i>', $res );
		$res = str_replace( "(!к)", '</i>', $res );
		$res = str_replace( "(п)", '<u>', $res );
		$res = str_replace( "(!п)", '</u>', $res );
		$res = str_replace( "(з)", '<s>', $res );
		$res = str_replace( "(!з)", '</s>', $res );
		$res = str_replace( "\n", '<br>', $res );
		$res = str_replace( '(красный)', '<font color=darkred>', $res );
		$res = str_replace( '(синий)', '<font color=darkblue>', $res );
		$res = str_replace( '(зеленый)', '<font color=darkgreen>', $res );
		$res = str_replace( '(!цвет)', '</font>', $res );
		$res = str_replace( '(центр)', '<center>', $res );
		$res = str_replace( '(!центр)', '</center>', $res );
		$res = str_replace( '(спойлер)', '<span style="color: #cc9966; background: #cc9966;" onmouseover=this.style.color=\"white\" onmouseout=this.style.color=\"#cc9966\">', $res );
		$res = str_replace( '(!спойлер)', '</span>', $res );
		$res = str_replace( '(тюльпан)', '<img src="/images/presents/8m1.gif" alt="(тюльпан)" style="width: 75px; height: 75px;" />', $res );		
			
		$p = 0;
		while( 1 )
		{
			$p = $t1 = strpos( $res, "(имя)", $p );
			if( $t1 === false )
				break;
			$p = $t2 = strpos( $res, "(!имя)", $p );
			if( $t2 === false )
				break;
			
			$t1 += 5;
			$nick = substr( $res, $t1, $t2 - $t1 );
			
			$nick = htmlspecialchars( $nick );
			$mres = f_MQuery( "SELECT player_id FROM characters WHERE login = '$nick'" );
			$marr = f_MFetch( $mres );
			if( !$marr ) $res = str_replace( '(имя)'.$nick.'(!имя)', "<b>[Не существующий игрок]</b>", $res );
			else
			{
				$plr = new Player( $marr[0] );
				$res = str_replace( '(имя)'.$nick.'(!имя)', '<script>document.write( '.$plr->Nick( ).' )</script>', $res );
			}
		}
		while( 1 )
		{
			$p = $t1 = strpos( $res, "(автор)", $p );
			if( $t1 === false )
				break;
			$p = $t2 = strpos( $res, "(цитата)", $p );
			if( $t2 === false )
				break;
			$p = $t3 = strpos( $res, "(!цитата)", $p );
			if( $t3 === false )
				break;

				
			$t1 += 7;
			$nick = substr( $res, $t1, $t2 - $t1 );
			$t2 += 8;
			$text = substr( $res, $t2, $t3 - $t2 );
			
			$moo = "<div><script>FLUl();</script><b>$nick</b> писал(а):<script>FUlt();</script>$text<script>FL();FLL();</script></div>";
			$res = substr( $res, 0, $t1 - 7 ).$moo.substr( $res, $t3 + 9 );
		}
	}

	//внутренние ссылки
	if( preg_match_all( "/(\([а-я]+:\d+\))/", $res, $loclinks ) )
	{
		$mp = array( );
		$count = count( $loclinks[0] );
		for( $i = 0; $i < $count; ++ $i )
		{
			$str = substr( $loclinks[0][$i], 1, count( $loclinks[0][$i] ) - 2 );
			$ll = explode( ':', substr( $loclinks[0][$i], 1, count( $loclinks[0][$i] ) - 2 ) );
			if( $mp[$str] ) continue;
			$mp[$str] = 1;
			switch( $ll[0] )
			{
				case 'помощь':
				{
					$r = f_MQuery( "SELECT title FROM help_topics WHERE topic_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id='.$ll[1].'" title="Раздел в Помощи" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['title'].'</a>', $res );
					break;
				}
				case 'форум':
					$r = f_MQuery( "SELECT title,room_id FROM forum_threads WHERE thread_id = {$ll[1]} AND important != -1" );
					if( $arr = f_MFetch( $r ) )
					{
						require_once( 'player.php' );
						$Player = new Player( $_COOKIE['c_id'] );
						$PlayerRank = $Player->Rank( );
						
						// Ссылки на ЗФ Орденов могут постить только админы и члены Орденов, владеющих ЗФ
						if( $arr['room_id'] < 0 && ( $PlayerRank != 1 && $Player->clan_id != $arr['room_id'] * -1 ) )
						{
							break;
						}
						// Ссылки на ЗФ Модеров могут постить только админы и модеры
						elseif( $arr['room_id'] == 20 and( $PlayerRank != 1 and $PlayerRank != 2 and $PlayerRank != 5 ) )
						{
							$Player->syst2( '2' );
							break;
						}
						// Тоже самое с админами и админфорумами
						elseif( ( $arr['room_id'] == 21 or $arr['room_id'] == 22 ) and $PlayerRank != 1 )
						{
							break;
						}
						
						$res = str_replace( $loclinks[0][$i], '<a href="/forum.php?thread='.$ll[1].'" title="Тема на форуме" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['title'].'</a>', $res );
					}
					break;
				case 'орден':
				{
					$r = f_MQuery( "SELECT name FROM clans WHERE clan_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/orderpage.php?id='.$ll[1].'" title="Страница Ордена" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case 'турнир':
				{
					$r = f_MQuery( "SELECT name FROM tournament_announcements WHERE tournament_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/tournament_net.php?id='.$ll[1].'" title="Турнирная сетка" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case "бой":
				{
					if( $ll[1] > 0 )
						$res = str_replace( $loclinks[0][$i], '<a href="/combat_log.php?id='.$ll[1].'" title="Лог боя" target="_blank" moo="'.$loclinks[0][$i].'">Бой #'.$ll[1].'</a>', $res );
					break;
				}	
				case 'вещь':
				{
					$r = f_MQuery( "SELECT name FROM items WHERE item_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1010&item_id='.$ll[1].'" title="Вещь в Энциклопедии" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case 'моб':
				{
					$r = f_MQuery( "SELECT name FROM mobs WHERE mob_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1016&beast_id='.$ll[1].'" title="Моб в Бестиарии" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}				
				case 'заклинание':
				{
					$r = f_MQuery( "SELECT name FROM cards WHERE card_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1011&spell_id='.$ll[1].'" title="Заклинание в Энциклопедии" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}				
				case 'рецепт':
				{
					$r = f_MQuery( "SELECT name FROM recipes WHERE recipe_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1015&recipe_id='.$ll[1].'" title="Рецепт в Энциклопедии" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case 'штука':
				{
					if( isset( $_GET['where'] ) == false ) // Если не в чат
					{
						$r = f_MQuery( 'SELECT name,image,image_large FROM items WHERE item_id = '.$ll[1] );
						if( $arr = f_MFetch( $r ) )
						{
							$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1010&item_id='.$ll[1].'" target="_blank" title="'.$arr[name].'" moo="'.$loclinks[0][$i].'"><img src="/images/items/'.( ( $arr[image_large] == '' ) ? $arr[image] : $arr[image_large] ).'" style="width: 50px; height: 50px; border: 0px;" alt="(штука:'.$ll[1].')" /></a>', $res );					
						}
					}
					break;
				}
			}
		}
	}
	
	return str_replace( "'", "\"", $res );
}

function process_str_inv( $str )
{
	global $smiles, $vsmiles;

	$res = $str;

	$res = str_replace( '<br>', "\n", $res );

	while( 1 )
	{
		$p = $t1 = strpos( $res, "<div><script>FLUl();</script><b>" );
		if( $t1 === false )
			break;
		$p = $t2 = strpos( $res, "</b> писал(а):<script>FUlt();</script>", $p );
		if( $t2 === false )
			break;
		$p = $t3 = strpos( $res, "<script>FL();FLL();</script></div>", $p );
		if( $t3 === false )
			break;

		$a = strlen( "<div><script>FLUl();</script><b>" );
		$b = strlen( "</b> писал(а):<script>FUlt();</script>" );
		$c = strlen( "<script>FL();FLL();</script></div>" );

			
		$t1 += $a;
		$nick = substr( $res, $t1, $t2 - $t1 );
		$t2 += $b;
		$text = substr( $res, $t2, $t3 - $t2 );
		
		$moo = "(автор)$nick(цитата)$text(!цитата)";
		$res = substr( $res, 0, $t1 - $a ).$moo.substr( $res, $t3 + $c );
	}

	$res = str_replace( '<b>', '(ж)', $res );
	$res = str_replace( '</b>', '(!ж)', $res );
	$res = str_replace( '<i>', '(к)', $res );
	$res = str_replace( '</i>', '(!к)', $res );
	$res = str_replace( '<u>', '(п)', $res );
	$res = str_replace( '</u>', '(!п)', $res );
	$res = str_replace( '<s>', '(з)', $res );
	$res = str_replace( '</s>', '(!з)', $res );
	$res = str_replace( '<font color=red>', '(красный)', $res );
	$res = str_replace( '<font color=blue>', '(синий)', $res );
	$res = str_replace( '<font color=green>', '(зеленый)', $res );
	$res = str_replace( '<font color=darkred>', '(красный)', $res );
	$res = str_replace( '<font color=darkblue>', '(синий)', $res );
	$res = str_replace( '<font color=darkgreen>', '(зеленый)', $res );
	$res = str_replace( '</font>', '(!цвет)', $res );
	$res = str_replace( '<center>', '(центр)', $res );
	$res = str_replace( '</center>', '(!центр)', $res );
	$res = str_replace( '<span style="color: #cc9966; background: #cc9966;" onmouseover="this.style.color=\'white\'" onmouseout="this.style.color=\'#cc9966\'">', '(спойлер)', $res );
	$res = str_replace( '</span>', '(!спойлер)', $res );
	$res = str_replace( '<img src="/images/presents/8m1.gif" alt="(тюльпан)" style="width: 75px; height: 75px;" />', '(тюльпан)', $res );
	
	if( preg_match_all( "/<a(.*?) moo=\"(\([а-я]+:\d+\))\">(.*?)<\/a>/", $res, $loclinks ) )
	{
		$count = count( $loclinks );
		for( $i = 0; $i < $count; ++ $i )
		{
			preg_match( "(\([а-я]+:\d+\))", $loclinks[0][$i], $ll );
			$res = str_replace( $loclinks[0][$i], $ll[0], $res );
		}
	}
	
	foreach( $smiles as $a )
		$res = str_replace( "<img src='/images/smiles/$a.gif' alt='*$a*' />", "*$a*", $res );
	for( $i = 0; $i < 4; ++ $i )
		foreach( $vsmiles[$i] as $a )
			$res = str_replace( "<img src=/images/smiles/$a.gif alt='*$a*' />", "*$a*", $res );
 	
 	$p = 0;

	while( 1 )
	{
		$p = $t1 = strpos( $res, "<script>", $p );
		if( $t1 === false )
			break;
		$t2 = strpos( $res, "'", $p );
		if( $t2 === false )
			break;
		$t3 = strpos( $res, "'", $t2 + 1 );
		if( $t3 === false )
			break;
		$t4 = strpos( $res, "</script>", $t3 );
		if( $t4 === false )
			break;
			
		$t4 += 9;
		++ $t2;
		$nick = substr( $res, $t2, $t3 - $t2 );
		
		$res = str_replace( substr( $res, $t1, $t4 - $t1 ), "(имя)".$nick.'(!имя)', $res );
	}
	
	

	return $res;
}

function process_str_none( $str )
{
	global $smiles, $vsmiles;

	$res = $str;

	$res = str_replace( '<br>', "\n", $res );

	while( 1 )
	{
		$p = $t1 = strpos( $res, "<div><script>FLUl();</script><b>" );
		if( $t1 === false )
			break;
		$p = $t2 = strpos( $res, "</b> писал(а):<script>FUlt();</script>", $p );
		if( $t2 === false )
			break;
		$p = $t3 = strpos( $res, "<script>FL();FLL();</script></div>", $p );
		if( $t3 === false )
			break;

		$a = strlen( "<div><script>FLUl();</script><b>" );
		$b = strlen( "</b> писал(а):<script>FUlt();</script>" );
		$c = strlen( "<script>FL();FLL();</script></div>" );

			
		$t1 += $a;
		$nick = substr( $res, $t1, $t2 - $t1 );
		$t2 += $b;
		$text = substr( $res, $t2, $t3 - $t2 );
		
		$moo = "(автор)$nick(цитата)$text(!цитата)";
		$res = substr( $res, 0, $t1 - $a ).$moo.substr( $res, $t3 + $c );
	}

	$res = str_replace( '<b>', '', $res );
	$res = str_replace( '</b>', '', $res );
	$res = str_replace( '<i>', '', $res );
	$res = str_replace( '</i>', '', $res );
	$res = str_replace( '<u>', '', $res );
	$res = str_replace( '</u>', '', $res );
	$res = str_replace( '<s>', '', $res );
	$res = str_replace( '</s>', '', $res );
	$res = str_replace( '<font color=red>', '', $res );
	$res = str_replace( '<font color=blue>', '', $res );
	$res = str_replace( '<font color=green>', '', $res );
	$res = str_replace( '<font color=darkred>', '', $res );
	$res = str_replace( '<font color=darkblue>', '', $res );
	$res = str_replace( '<font color=darkgreen>', '', $res );
	$res = str_replace( '</font>', '', $res );
	$res = str_replace( '<center>', '', $res );
	$res = str_replace( '</center>', '', $res );
	$res = str_replace( '<span style="color: #cc9966; background: #cc9966;" onmouseover="this.style.color=\'white\'" onmouseout="this.style.color=\'#cc9966\'">', '', $res );
	$res = str_replace( '</span>', '', $res );
	$res = str_replace( '<img src="/images/presents/8m1.gif" alt="(тюльпан)" style="width: 75px; height: 75px;" />', '', $res );
	
	foreach( $smiles as $a )
		$res = str_replace( "<img src='/images/smiles/$a.gif' alt='*$a*' />", "", $res );
	for( $i = 0; $i < 4; ++ $i )
		foreach( $vsmiles[$i] as $a )
			$res = str_replace( "<img src='/images/smiles/$a.gif' alt='*$a*' />", "*$a*", $res );
 	
	if( preg_match_all( "/<a(.*?) moo=\"(\([а-я]+:\d+\))\">(.*?)<\/a>/", $res, $loclinks ) )
	{
		$count = count( $loclinks );
		for( $i = 0; $i < $count; ++ $i )
		{
			preg_match( "/(<a(.*)>)(.*)(<\/a>/)", $loclinks[0][$i], $ll );
			$res = str_replace( $loclinks[0][$i], $ll[1], $res );
		}
	}
	
 	$p = 0;

	while( 1 )
	{
		$p = $t1 = strpos( $res, "<script>", $p );
		if( $t1 === false )
			break;
		$t2 = strpos( $res, "'", $p );
		if( $t2 === false )
			break;
		$t3 = strpos( $res, "'", $t2 + 1 );
		if( $t3 === false )
			break;
		$t4 = strpos( $res, "</script>", $t3 );
		if( $t4 === false )
			break;
			
		$t4 += 9;
		++ $t2;
		$nick = substr( $res, $t2, $t3 - $t2 );
		
		$res = str_replace( substr( $res, $t1, $t4 - $t1 ), "".$nick.'', $res );
	}


	return $res;
}

?>
