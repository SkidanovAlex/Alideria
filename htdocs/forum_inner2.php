<?

include_once( 'skin.php' );
include_once( 'clan.php' );



// копия массива встречается так же в admin_forum_ranks.php
$forum_room_names = Array( 22 => "Чисто для крутанов конкретных", 21 => "The TOP SECRET project IDE!", 0 => "Новости от Творцов", 1 => "Ошибки Игры", 2 => "Опросы от Администрации", 3 => "Общие Вопросы", 4 => "Прохождение Квестов", 5 => "Идеи и Предложения", 6 => "Персонажи", 7 => "Свободное Общение", 8 => "Творчество Игроков", 10 => "Объявления - продажа", 9 => "Объявления - покупка", 11 => "Жалобная книга", 20 => "Закрытый разрел модераторов", 19 => "Закрытый раздел модераторов чата" );

$authorized = false;
if( check_cookie( ) )
{
	$authorized = true;
	$player_id = $HTTP_COOKIE_VARS['c_id'];
	$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
}

$res = f_MQuery( "SELECT clan_id, name FROM clans" );
while( $arr = f_MFetch( $res ) )
	$forum_room_names[- $arr['clan_id']] = $arr['name']." - закрытый форум";

function thread_alowed( $room )
{
	global $authorized;
	global $player;
	global $forum_room_names;
	global $CAN_READ_FORUM;

	if( $authorized && $player->level < 2 ) return false;
	
	if( !$forum_room_names[$room] ) return false;
	
	if( !$authorized ) return false;
	if( $room == 19 && $player->Rank( ) == 0 ) return false;
	if( $room == 20 && ($player->Rank( ) == 0 || $player->Rank( ) == 3) ) return false;
	if( $room == 21 && $player->Rank( ) != 1 ) return false;
	if( $room == 22 && ( !$player || $player->player_id > 174 ) ) return false;
	if( $room == 0 && $player->Rank( ) != 1 ) return false;
	if( $room == 2 && $player->Rank( ) != 1 ) return false;

	if( $room < 0 ) return $player->clan_id == - $room && 0 != ( getPlayerPermitions( $player->clan_id, $player->player_id ) & $CAN_READ_FORUM );
	
	return true;
}

function post_alowed( $room, $thread )
{
	global $authorized;
	global $player;
	global $forum_room_names;
	global $CAN_READ_FORUM;
	
	if( $authorized && $player->level < 2 ) return false;

	if( !$forum_room_names[$room] ) return false;
	
	if( !$authorized ) return false;
	if( $room == 19 && $player->Rank( ) == 0 ) return false;
	if( $room == 20 && ($player->Rank( ) == 0 || $player->Rank( ) == 3) ) return false;
	if( $room == 21 && $player->Rank( ) != 1 ) return false;
	if( $room == 22 && ( !$player || ( $player->player_id > 174 || $player->login != 'undefined' ) ) ) return false;

	$res = f_MQuery( "SELECT closed, important FROM forum_threads WHERE thread_id = $thread" );
	$arr = f_MFetch( $res );
	if( !$arr ) RaiseError( "Ошибка post_alowed, нет темы. Комната: $room, ID: $thread" );
	if( $arr[closed] == 1 || $arr[important] == -1 ) return false;
	
	if( $room < 0 ) return $player->clan_id == - $room && 0 != ( getPlayerPermitions( $player->clan_id, $player->player_id ) & $CAN_READ_FORUM );

	return true;
}

function looking_alowed( $room )
{
	global $authorized;
	global $player;
	global $forum_room_names;
	global $CAN_READ_FORUM;

	if( $authorized && $player->Rank( ) == 1 ) return true;
	
	if( !$forum_room_names[$room] ) return false;
	
	if( $room == 19 && $player->Rank( ) == 0 ) return false;
	if( $room == 20 && ( !$authorized || $player->Rank( ) == 0 || $player->Rank( ) == 3 ) ) return false;
	if( $room == 21 && ( !$authorized || $player->Rank( ) != 1 ) ) return false;
	if( $room == 22 && ( !$player || $player->player_id > 174 ) ) return false;
	
	if( $room < 0 )
	{
		if( !$authorized ) return false;
		return $player->clan_id == - $room && 0 != ( getPlayerPermitions( $player->clan_id, $player->player_id ) & $CAN_READ_FORUM );
	}
	
	return true;
}

function moder_alowed( $room_id )
{
	global $authorized;
	global $player;
	global $CAN_MODERATE;
	
	if( !$authorized ) return false;
	if( $player->Rank( ) == 1 || $player->Rank( ) == 5 ) return true;
	$res = f_MQuery( "SELECT * FROM forum_ranks WHERE room_id = $room_id AND player_id = {$player->player_id}" );
	if( f_MNum( $res ) ) return true;

	if( $room_id < 0 )
	{
		$clan_id = - $room_id;
		include_once( "clan.php" );
		if( 0 != ( getPlayerPermitions( $player->clan_id, $player->player_id ) & $CAN_MODERATE ) )
		{
			return true;
		}
	}

	return false;
}

function edit_alowed( $room_id, $author )
{
	global $authorized;
	global $player;

	if ($author == 6825 && $player->player_id != 6825) return false;

	return moder_alowed( $room_id ) || ( $authorized && $author == $player->player_id );
}

function delete_alowed($room_id, $author)
{
	global $authorized;
	global $player;

	if ($author == 6825 && $player->player_id != 6825) return false;

	if ($player->player_id == 6825 || $player->player_id == 67573 || $player->player_id == 173 ) return true;
}

function forum_permission_denied( )
{
	RaiseError( "Несанкционированная попытка прочитать закрытый или не существующий раздел форума." );
}


// AJAX --------------------------------------------------------
if( isset( $_GET['ajax'] ) )
{
	header("Content-type: text/html; charset=windows-1251");
	if( $_GET['ajax'] === 'rooms' )
	{
		$res = f_MQuery( "SELECT * FROM forum_rooms ORDER BY id" );
	
	    echo "var st = '";
		while( $arr = f_MFetch( $res ) ) if( looking_alowed( $arr[id] ) )
		{
    		if( $arr['id'] == 0 ) echo "<b>Администраторская</b><br>";
    		if( $arr['id'] == 3 ) echo "<b>Игровые Комнаты</b><br>";
    		if( $arr['id'] == 7 ) echo "<b>Свободное Общение</b><br>";
    		if( $arr['id'] == 9 ) echo "<b>Торговля</b><br>";
		if( $arr['id'] == 11 ) echo "<b>Жалобы</b><br>";
    		if( $arr['id'] == 19 ) echo "<b>А это тока для конкретных пацанов</b><br>";
			echo "<a href=forum.php?room=$arr[id]>{$forum_room_names[$arr[id]]}</a><br>";
		}
			
		echo "';";
		echo "_( 'navi' ).innerHTML = st;";
	}
	else  if( $_GET['ajax'] < 0 )
	{
		$thread_id = - (int)$_GET['ajax'];
		$res = f_MQuery( "SELECT * FROM forum_threads WHERE thread_id = $thread_id AND important >= 0" );
		$arr = f_MFetch( $res ); if( !$arr || !looking_alowed( $arr['room_id'] ) ) die( );
		$res = f_MQuery( "SELECT text FROM forum_posts WHERE thread_id = $thread_id ORDER BY post_id LIMIT 1" );
		$arr = f_MFetch( $res );
		$txt = process_str_none( $arr[0] );
		$txt = str_replace( "\n", '<br>', $txt );
		$txt = str_replace( "\r", '', $txt );
        $txt = addslashes($txt);
		$cut = 500;
		$cut2 = strpos( strtolower( $txt ), '<table>' );
		if( $cut2 !== false ) $cut = $cut2;
		if( strlen( $txt ) > $cut ) $txt = substr( $txt, 0, $cut ).' ...';
		echo "if( cur_post_loading == $thread_id ) _( 'tooltip_inner' ).innerHTML = '$txt';";
	}
	else
	{
		$room_id = (int)$_GET['ajax'];
		if( !looking_alowed( $room_id ) ) die( );

		$res = f_MQuery( "SELECT * FROM forum_threads WHERE room_id = $room_id AND important >= 0 ORDER BY last_post_made DESC LIMIT 0, 20" );

	    echo "var st = '";
		while( $arr = f_MFetch( $res ) )
		{
			echo "<a href=forum.php?thread=$arr[thread_id]&f=0>".addslashes($arr[title])."</a><br>";
		}
			
		echo "';";
		echo "_( 'navi' ).innerHTML = st;";
	}
	die( );
}
// AJAX END ----------------------------------------------------

echo "<script>var isForum = true;</script>";
include_js( 'js/skin.js' );
include_js( 'js/ajax.js' );
include_js( 'js/forum.js' );
include_js( 'js/tooltips.php' );

echo '<center><table style="width: 1000px;"><tr><td>';




print( "<center>" );

if( $authorized ) {
print( "<table cellspacing=0 cellpadding=0 border=0><tr>" );

for( $i = 0; $i < 3; ++ $i )
{
	print( "<td><img border=0 width=17 height=9 src=images/top/a.png></td>" );
	print( "<td><img border=0 width=92 height=9 src=images/top/e.png></td>" );
}
print( "<td><img border=0 width=17 height=9 src=images/top/a.png></td>" );

print( "</tr><tr>" );

$links = array( '<a href=forum.php?mode=search>Поиск</a>', '<a href=player_forum_history.php target=_blank>История</a>', '<a href=forum.php?mode=customize>Настройки</a>' );

foreach( $links as $a => $b )
{
	if( $a ) print( "<td><img border=0 width=17 height=21 src=images/top/d.png></td>" );
	else print( "<td><img border=0 width=17 height=21 src=images/top/b.png></td>" );
	print( "<td width=92 height=21 background=images/top/f.png align=center valign=middle>" );
	echo $b;
	print( "</td>" );
}
print( "<td><img border=0 width=17 height=21 src=images/top/c.png></td></tr></table><br>" );
}



if( isset( $_GET['mode'] ) )
{
	if( $_GET['mode'] == 'search' && $authorized )
	{
		print( "<table width=100%><tr><td>" );
		echo "<script>FLUl();</script>";

		echo "<a href=forum.php id=forums_lnk><b>Форумы Алидерии</b></a>&nbsp;<a href=# onclick='show_rooms();'>&#9660;</a>&nbsp;&raquo;&nbsp;<a href=forum.php?mode=search><b>Поиск</b></a>";

		echo "<table width=100%><tr><td><script>FUlt();</script>";

		echo "<form action=forum.php method=GET>";
			echo "<table><tr><td>Что искать:</td><td><input style='width:100%' class=m_btn name=q></td></tr>";
			echo "<tr><td valign=top>Как искать:</td><td>";
			echo "<input type=radio name=k value='0'> Темы, содержащие указанные слова в заголовке<br>";
			echo "<input type=radio name=k value='1'> Темы, содержащие указанные слова в тексте не зависимо от порядка<br>";
			echo "<input type=radio name=k value='2'> Темы, содержащие указанные слова в тексте в указанном порядке, но не обязательно подряд<br>";
			echo "<input type=radio name=k value='3' CHECKED> Темы, содержащие указанные слова в тексте строго подряд<br>";

			echo "</td></tr>";
			echo "<tr><td valign=top>Где искать:</td><td>";
			$res = f_MQuery( "SELECT id FROM forum_rooms WHERE id >= 0" );
			while( $arr = f_MFetch( $res ) ) if( looking_alowed( $arr[0] ) ) echo "<input type=checkbox name=room{$arr[0]} checked> ".$forum_room_names[$arr[0]]."<br>";
			echo "</td></tr>";
			echo "<tr><td>&nbsp;</td><td><input class=s_btn value=Искать type=submit></td></table>";
		echo "</form>";
		echo "<script>FL();</script></td></tr></table>";

		echo "<script>FLL();</script>";
	}
	else if( $_GET['mode'] == 'customize' && $authorized )
	{
		print( "<table width=100%><tr><td>" );
		echo "<script>FLUl();</script>";
		echo "<a href=forum.php id=forums_lnk><b>Форумы Алидерии</b></a>&nbsp;<a href=# onclick='show_rooms();'>&#9660;</a>&nbsp;&raquo;&nbsp;<a href=forum.php?mode=customize><b>Настройки</b></a>";
		echo "<table width=100%><tr><td><script>FUlt();</script>";
		echo "<b>Аватар</b><br>";

		$tm = time( );
		$arr = f_MFetch( f_MQuery( "SELECT expires, type FROM forum_avatars WHERE player_id={$player->player_id}" ) );

		if( isset( $_FILES['ava'] ) && $player->umoney >= 5 )
		{
			$registration_begun = true; $st = '';
        	list( $width, $height, $type, $attr ) = getimagesize( $_FILES['ava']['tmp_name'] );

        	if( !isset( $_FILES['ava'] ) )	
        	{
        		$st .= "Вы не приложили файл с аватаром.<br>";
        		$registration_begun = false;
        	}
        	else if( !$width )
        	{
        		$st .= "Аватар должен быть в формате GIF или JPEG. Посленный вами файл не распознан сервером как картинка.<br>";
        		$registration_begun = false;
            }
        	else if( $width > 100 || $height > 100 || $width < 50 || $height < 50 )
        	{
        		$st .= "Размер аватара должен быть 100х100 (размер посланной картинки {$width}x{$height})<br>";
        		$registration_begun = false;
            }
        	else if( $_FILES['ava']['size'] > 41*1024 )
        	{
        		$val = $_FILES['ava']['size'];
        		$st .= "Размер файла с иконкой не может превышать 40кб (размер посланного файла $val байт)<br>";
        		$registration_begun = false;
        	}
        	else if( $type != 1 && $type != 2 )
        	{
				$st .= "Иконка Ордена должна быть в формате GIF или JPEG<br>";
        		$registration_begun = false;
        	}

        	if( !$registration_begun ) echo "<font color=darkred>".$st."</font><br>";
			else
			{
				if( !$arr )
				{
					$player->SpendUMoney( 5 );
					$player->AddToLogPost( -1, -5, 30 );
    				$expires = time( ) + 50 * 24 * 60 * 60;
    				f_MQuery( "INSERT INTO forum_avatars ( player_id, expires, type ) VALUES ( {$player->player_id}, $expires, $type )" );
				}
				else
				{
					$player->SpendUMoney( 5 );
					$player->AddToLogPost( -1, -5, 30 );
					f_MQuery( "UPDATE forum_avatars SET type=$type WHERE player_id={$player->player_id}" );
				}

				if( $type == 1 ) $fname = "p{$player->player_id}.gif";
				else $fname = "p{$player->player_id}.jpg";
				$errno = unlink( "images/forum_avatars/$fname" );
//				exec("DEL /F/Q \"images/forum_avatars/$fname\"", $lines, $errno);
				if( $player->player_id == 173 || $player->player_id == 6825 || $player->player_id == 67573 || $player->player_id == 173 ) echo "[$errno]";
				$errno = move_uploaded_file($_FILES['ava']['tmp_name'], 'images/forum_avatars/'.$fname );
				if( $player->player_id == 173 || $player->player_id == 6825  || $player->player_id == 67573 || $player->player_id == 173 ) echo "[$errno]";
				if( $player->player_id != 173 || $player->player_id != 6825  || $player->player_id == 67573 || $player->player_id == 173 ) die( "<script>location.href='forum.php?mode=customize';</script>" );
			}
		}
		else if( isset( $_GET['prolong'] ) && $player->umoney >= 1 )
		{
			$player->SpendUMoney( 1 );
			$player->AddToLogPost( -1, -1, 30 );
			$expires = max( time( ), $arr['expires'] ) + 30 * 24 * 60 * 60;
			f_MQuery( "UPDATE forum_avatars SET expires=$expires WHERE player_id={$player->player_id}" );
			die( "<script>location.href='forum.php?mode=customize';</script>" );
		}

		if( !$arr )
		{
			echo "В настоящий момент у вас нет аватара на форуме<br>";
			echo "Вы можете купить аватар на <b>50 дней</b> за <img src=images/umoney.gif> <b>5</b><br>В будущем продление аватара на 30 дней будет стоить <img src=images/umoney.gif> <b>1</b><br><br>";
    		echo '<form enctype="multipart/form-data" action=forum.php?mode=customize method=post>';
    		echo "<table cellspacing=0 cellpadding=0 border=0>";
    		echo "<tr><td><input type=file class=m_btn name=ava value=''></td></tr>";
    		echo "<tr><td><input type=submit class=ss_btn value='Купить'></td></tr>";
    		echo "</table>";
    		echo "</form>";
    		echo "<small>Аватар должен быть картинкой в формате GIF или JPEG размером 100px на 100px не тяжелее 40кб</small>";

		}
		else
		{
			if( $arr[0] < $tm ) echo "У вас загружен аватар на сервер, но с момента последнего продления прошло больше 30 дней<br>";
			else
			{
				$till = date( "d.m.Y", $arr[0] );
				echo "Ваш аватар действует до <b>$till</b><br>";
			}
			echo "Вы можете <a href=forum.php?mode=customize&prolong=1>продлить</a> действие аватара на <b>30 дней</b> за <img src=images/umoney.gif> <b>1</b><br><br>";
            echo "<img src=images/forum_avatars/p{$player->player_id}.".(($arr['type'] == 1)?"gif":"jpg")." width=100 height=100><br>";
            echo "<b>Вы можете поменять аватар за <img src=images/umoney.gif> <b>5</b></b>";
    		echo '<form enctype="multipart/form-data" action=forum.php?mode=customize method=post>';
    		echo "<table cellspacing=0 cellpadding=0 border=0>";
    		echo "<tr><td><input type=file class=m_btn name=ava value=''></td></tr>";
    		echo "<tr><td><input type=submit class=ss_btn value='Поменять'></td></tr>";
    		echo "</table>";
    		echo "</form>";
    		echo "<small>Аватар должен быть картинкой в формате GIF или JPEG размером 100px на 100px не тяжелее 40кб</small>";
		}


		echo "<script>FL();</script></td></tr></table>";
		echo "<script>FLL();</script>";
	}
}
else if( isset( $_GET['q'] ) && $authorized ) // search
{
	include( 'forum_search.php' );

	$per_page = 20;

	$q = $_GET['q'];
	$type = (int)$_GET['k']; if( $type < 0 ) $typo = 0; if( $type > 3 ) $type = 3;
	$p = (int)$_GET['p']; $pp = $per_page * $p;
    $res = f_MQuery( "SELECT id FROM forum_rooms WHERE id >= 0" );
    $rooms = array( );
    while( $arr = f_MFetch( $res ) ) if( looking_alowed( $arr[0] ) ) if( $_GET["room$arr[0]"] == 'on' ) $rooms[] = $arr[0];

    $ok = true;

    if( $type == 0 )
    {
    	$f = new ForumSearch( '' );
    	$q = "%".implode( '%', $f->tokenize( $q ) )."%";
    	$rooms = implode( ',', $rooms );
    	if( strlen( $q ) < 5 ) $ok = false;
    	else
    	{
        	$arr1 = f_MFetch( f_MQuery( "SELECT count( thread_id ) FROM forum_threads WHERE title LIKE '$q' AND room_id IN ($rooms) AND important >= 0" ) );
        	$pages = (int)(( $arr1[0] + $per_page - 1 ) / $per_page);
        	$res = f_MQuery( "SELECT room_id, thread_id, title FROM forum_threads WHERE lower(title) LIKE '$q' AND room_id IN ($rooms) AND important >= 0 ORDER BY thread_id DESC LIMIT $pp, $per_page" );
        	while( $arr = f_MFetch( $res ) ) $result[] = array( $arr[0], $arr[1], $arr[2], array( -1 ) );
    	}
    }
    else
    {
       	$topics = array( );
       	foreach( $rooms as $room )
       	{
       		$ret = false;
       		$f = new ForumSearch( "forum_index/f{$room}.dat" );
       		if( $type == 1 ) $ret = $f->SearchAny( $q );
       		if( $type == 2 ) $ret = $f->SearchOrd( $q );
       		if( $type == 3 ) $ret = $f->SearchSeq( $q );
       		if( $ret === false ) $ok = false;
			else
			{
				foreach( $ret as $arr )
				{
					if( !$topics[$arr[0]] ) $topics[$arr[0]] = array( );
					$topics[$arr[0]][] = $arr[1];
				}
			}
       	}
       	if( $ok && count( $topics ) > 0 )
       	{
    		ksort( $topics );
    		$topics = array_reverse( $topics, true );
    		$pages = (int)(( count( $topics ) + $per_page - 1 ) / $per_page);
    		if( $p >= $pages ) { $p = pages - 1; $pp = $p * $per_page; }
    		$topics = array_slice( $topics, $pp, $per_page, true );
    		$moo = implode( ',', array_keys( $topics ) );
    		$res = f_MQuery( "SELECT room_id, thread_id, title FROM forum_threads WHERE thread_id IN ($moo) AND important >= 0 ORDER BY thread_id DESC" );
        	while( $arr = f_MFetch( $res ) ) $result[] = array( $arr[0], $arr[1], $arr[2], $topics[$arr[1]] );
		}
		else $ok = false;
    }

	print( "<table width=100%><tr><td>" );
	echo "<script>FLUl();</script>";

	echo "<a href=forum.php id=forums_lnk><b>Форумы Алидерии</b></a>&nbsp;<a href=# onclick='show_rooms();'>&#9660;</a>&nbsp;&raquo;&nbsp;<a href=forum.php?mode=search><b>Поиск</b></a>&nbsp;&raquo;&nbsp;<b>Результаты Поиска</b>";
	echo "<table width=100%><tr><td><script>FUlt();</script>";

	if( !$ok ) echo "<i>Ничего не найдено</i>";
	else
	{
		$hr = false;
		foreach( $result as $arr )
		{
			if( $hr ) echo "<hr color=black size=1 width=100%>"; $hr = true;
			echo "<a href=forum.php?thread=$arr[1] target=_blank><b>$arr[2]</b></a><br>";
			echo "Комната: <a href=forum.php?room=$arr[0] target=_blank>{$forum_room_names[$arr[0]]}</a><br>";
			echo "Найдено в ";
				$fs = true;
				sort( $arr[3] );
				foreach( $arr[3] as $a )
				{
					if( $a == -1 ) echo "заголовке";
					else
					{
						if( $fs ) echo "сообщениях: ";
						else echo ", "; $fs = false;

						$moo = f_MFetch( f_MQuery( "SELECT count( post_id ) FROM forum_posts WHERE thread_id=$arr[1] AND post_id <= $a" ) );
						echo "<a target=_blank href=forum.php?thread=$arr[1]&page=".((int)(($moo[0]-1)/20))."#p$moo[0]>".$moo[0]."</a>";
					}
				}
		}

		if( $pages > 1 )
		{
			$url = "forum.php?q=$q&k=$type";
			foreach( $rooms as $room ) $url .= "&room$room=on";
			echo "<br><br><center>Страница: ";
			for( $i = 0; $i < $pages; ++ $i )
			{
				if( $i ) echo ", ";
				if( $p == $i ) echo "<b>".($i+1)."</b>";
				else echo "<a href=$url&p=$i>".($i+1)."</a>";
			}
			echo "</center>";
		}
	}
	
	echo "<script>FL();</script></td></tr></table>";
    echo "<script>FLL();</script>";
}                                                                                                       
else if( isset( $_GET['post'] ) )
{
	$post_id = $HTTP_GET_VARS[post];
	settype( $post_id, 'integer' );
	$ret_page = $HTTP_GET_VARS['f'];
	settype( $ret_page, 'integer' );
	$page = $HTTP_GET_VARS[page];
	settype( $page, 'integer' );
	$res = f_MQuery( "SELECT * FROM forum_posts WHERE post_id = $post_id" );
	$arr = f_MFetch( $res );
	print( "<center>" );
	if( !$arr ) print( "Нет такого поста" );
	else
	{
		$thread_id = $arr[thread_id];
		$res2 = f_MQuery( "SELECT room_id, title FROM forum_threads WHERE thread_id = $thread_id" );
		$arr2 = f_MFetch( $res2 );
		if( !$arr2 ) RaiseError( "Существующий пост в несуществующей теме. Пост: $post_id, Тема: $thread_id" );
		$room_id = $arr2[0];
		
		if( !edit_alowed( $room_id, $arr[author_id] ) ) print( "Вы не можете редактировать этот пост" );
		else
		{
			print( "<b>$arr2[1]</b><br><br><a href=forum.php?thread=$thread_id&f=$ret_page&page=$page>Назад (не вносить изменения)</a><br><br><table width=70%><tr><td>" );
			echo "<script>FLUl( );</script>";
			print( "<table width=100%>" );

				$tm = date( "d.m.Y H:i", $arr[time] );
				print( "<tr><td>" );
				echo "<script>FUlt( );</script>";
				if( $new_post == $arr[post_id] ) print( "<a name=last_post></a>" );
				$moo_plr = new Player( $arr[author_id] );
				print( "<script>document.write( ".$moo_plr->Nick()." );</script>" );
				print( ", $tm"."<script>FL( );FUlt( );</script>$arr[text]<script>FL( );</script></td><tr>" );

			print( "</table>" );
			echo "<script>FLL( );</script>";
			print( "</td></tr></table>" );

				print( "<br>$err<b>Изменить сообщение:</b><br>" );
				print( "<table border=1 cellspacing=0><form name=q action=forum.php?thread=$thread_id&page=$page&f=$ret_page method=post><tr><td align=right>" );
				print( "<input type=hidden name=edit_post value=$post_id>" );
				insert_text_edit( "q", "txt", process_str_inv( $arr[text] ) );
//				print( "<textarea rows=5 cols=60 name=txt class=te_btn>$text</textarea><br>" );
				print( "<center><input class=s_btn type=submit value='Изменить'><center>" );
				print( "</td></tr></form></table>" );
		}
	}
}

else if(isset($HTTP_GET_VARS[delete]))
{
	$del_p = (int)$HTTP_GET_VARS[delete];
	$thread = (int)$HTTP_GET_VARS[thread];
	$f = (int)$HTTP_GET_VARS[f];
	$page = (int)$HTTP_GET_VARS[page];
	
	$res = f_MQuery( "SELECT * FROM forum_posts WHERE post_id = $del_p" );
	$arr = f_MFetch( $res );

	if( !$arr ) print( "Нет такого поста" );
	else
	{
		$thread_id = $arr[thread_id];
		if ($thread != $thread_id) RaiseError("Попытка удалить сообщение в другой теме. Пост: $del_p, Тема: $thread");
		$res2 = f_MQuery( "SELECT room_id, title FROM forum_threads WHERE thread_id = $thread_id" );
		$arr2 = f_MFetch( $res2 );
		$room_id = $arr2[0];
		if( !$arr2 ) RaiseError( "Попытка удалить существующий пост в несуществующей теме. Пост: $del_p, Тема: $thread_id" );
		if( !delete_alowed( $room_id, $arr[author_id] ) ) print( "Вы не можете удалить этот пост" );
		else if (f_MValue("SELECT MIN(post_id) FROM forum_posts WHERE thread_id=$thread") == $del_p )
			RaiseError("Попытка удалить первое сообщение в теме.");
		else
		{
			f_MQuery("DELETE FROM forum_posts WHERE post_id=$del_p");
			f_MQuery( "UPDATE forum_rooms SET posts = posts - 1 WHERE id = $room_id" );
			f_MQuery( "UPDATE forum_threads SET posts = posts - 1 WHERE thread_id = $thread_id" );

			die ("<script>location.href='forum.php?thread=$thread_id&f=$f&page=$page';</script>");
		}
	}
}

else if( isset( $HTTP_GET_VARS[thread] ) )
{
	$thread = $HTTP_GET_VARS[thread];
	settype( $thread, 'integer' );

	$avatars = array( );
	
	if( isset( $HTTP_GET_VARS[page] ) )
	{
		$page = $HTTP_GET_VARS[page];
		settype( $page, 'integer' );
	}
	else $page = 0;
	
	$ret_page = $HTTP_GET_VARS['f'];
	settype( $ret_page, 'integer' );
	
	$res = f_MQuery( "SELECT title, room_id, posts, important, closed, author_id, adult FROM forum_threads WHERE thread_id = $thread" );
	
	if( !mysql_num_rows( $res ) ) print( "Нет такой темы, <a href=forum.php>Вернуться на форум</a>" );
	else
	{
		$arr = f_MFetch( $res );
		$room_id = $arr[1];
		$post_num = $arr[2];
		
		if( !looking_alowed( $room_id ) ) forum_permission_denied( );
	

	    echo "<center>";

		print( "<table width=100%><tr><td>" );
		echo "<script>FLUl();</script>";

		echo "<a href=forum.php id=forums_lnk><b>Форумы Алидерии</b></a>&nbsp;<a href=# onclick='show_rooms();'>&#9660;</a>&nbsp;&raquo;&nbsp;<a href=forum.php?room=$room_id&page=$ret_page id=topics_lnk><b>$forum_room_names[$room_id]</b></a>&nbsp;<a href=# onclick='show_topics($room_id);'>&#9660;</a>&nbsp;&raquo;&nbsp;<a href=forum.php?thread=$thread&f=$ret_page>";
		if( $arr['important'] != -1 ) 
        {
            print( "<b>$arr[0]</b>" );
            print( "<script>document.title = '" . addslashes(strip_tags(htmlspecialchars_decode($arr[0]))) . " - Алидерия - Форум';</script>" );
        }
		else print( "<b>Удаленная тема</b>" );
		echo "</a>";
		
		if( moder_alowed( $room_id ) )
		{
			if( isset( $_GET['imp'] ) )
			{
				settype( $_GET['imp'], 'integer' );
				if( $_GET['imp'] == 0 || $_GET['imp'] == 1 || $_GET['imp'] == -1 )
				f_MQuery( "UPDATE forum_threads SET important = $_GET[imp] WHERE thread_id = $thread" );
				$res = f_MQuery( "SELECT title, room_id, posts, important, closed, author_id, adult FROM forum_threads WHERE thread_id = $thread" );
				$arr = f_MFetch( $res );
			}
			if( isset( $_GET['cls'] ) )
			{
				settype( $_GET['cls'], 'integer' );
				if( $_GET['cls'] == 0 || $_GET['cls'] == 1 )
				f_MQuery( "UPDATE forum_threads SET closed = $_GET[cls] WHERE thread_id = $thread" );
				$res = f_MQuery( "SELECT title, room_id, posts, important, closed, author_id, adult FROM forum_threads WHERE thread_id = $thread" );
				$arr = f_MFetch( $res );
			}
			if( isset( $_GET['adu'] ) )
			{
				settype( $_GET['adu'], 'integer' );
				if( $_GET['adu'] == 0 || $_GET['adu'] == 1 )
				f_MQuery( "UPDATE forum_threads SET adult = $_GET[adu] WHERE thread_id = $thread" );
				$res = f_MQuery( "SELECT title, room_id, posts, important, closed, author_id, adult FROM forum_threads WHERE thread_id = $thread" );
				$arr = f_MFetch( $res );
			}
			
			echo "&nbsp;&nbsp;&nbsp;(";
			if( $arr['important'] == -1 ) print( "Эта тема удалена. Вы можете <a href=forum.php?thread=$thread&f=$ret_page&imp=0>вернуть</a> ее" );
			else
			{
				if( $arr['important'] == 0 ) print( "<a href=forum.php?thread=$thread&f=$ret_page&imp=1>Прикрепить</a> | " );
				else print( "<a href=forum.php?thread=$thread&f=$ret_page&imp=0>Открепить</a> | " );
				if( $arr['closed'] == 0 ) print( "<a href=forum.php?thread=$thread&f=$ret_page&cls=1>Закрыть</a> | " );
				else print( "<a href=forum.php?thread=$thread&f=$ret_page&cls=0>Открыть</a> | " );
				if( $arr['adult'] == 0 ) print( "<a href=forum.php?thread=$thread&f=$ret_page&adu=1>Отметить как Агрессивную</a> | " );
				else print( "<a href=forum.php?thread=$thread&f=$ret_page&adu=0>Убрать статус Агрессивной</a> | " );
				print( "<a href=forum.php?thread=$thread&f=$ret_page&imp=-1>Удалить</a>" );
			}
			echo ")";
		}


		$new_post = -1;
		$err = "";
		if( $arr['important'] == -1 )
		{
			echo "<script>FLL();</script>";
			print( "</td></tr></table>" );
		}
		else
		{
			// votes begin -----------------------------
				if( $arr['author_id'] == $player_id )
				{
					f_MQuery( "LOCK TABLE forum_votes WRITE" );
					$vres = f_MFetch( f_MQuery( "SELECT count( entry_id ) FROM forum_votes WHERE thread_id=$thread" ) );
					if( !$vres[0] && isset( $_POST['ans0'] ) )
					{
						f_MQuery( "UNLOCK TABLES" );
						$anss = array( -1 => 0 );
						for( $i = 0; $i < 20 && isset( $_POST["ans$i"] ); ++ $i )
						{
							$prnt = (int)$_POST["prn$i"];
							if( $prnt != -1 && ( $prnt < 0 || $prnt >= $i ) ) RaiseError( "Ошибка в голосовании, неверный родитель у одного из вариантов ответа", "вариант: $i родитель: $prnt" );
							$prnt = $anss[$prnt];
							$txt = f_MEscape(htmlspecialchars( substr( $_POST["ans$i"], 0, 100 ), ENT_QUOTES ));
							f_MQuery( "INSERT INTO forum_votes( thread_id, txt, parent_id ) VALUES ( $thread, '$txt', $prnt )" );
							$anss[$i] = mysql_insert_id( );
						}
						$nm = f_MEscape(htmlspecialchars( substr( $_POST['nm'], 0, 100 ), ENT_QUOTES ));
						if( $_POST['closed'] ) $sec = 1; else $sec = 0;
						f_MQuery( "INSERT INTO forum_vote_titles ( thread_id, title, secret ) VALUES ( $thread, '$nm', '$sec' )" );
						die( "<script>location.href='forum.php?thread=$thread&f=$ret_page';</script>" );
					}
					else f_MQuery( "UNLOCK TABLES" );

					if( !$vres[0] ) echo " (<a href=forum.php?thread=$thread&f=$ret_page&vote=-1>Создать голосование</a>)";


					if( !$vres[0] && $_GET['vote'] == -1)
					{
						echo "<table width=100% border=0><tr><td><script>FUlt();</script>";
						echo "<form id=vfrm name=vfrm action=forum.php?thread=$thread&f=$ret_page method=POST>";
						echo "<script src=js/create_vote.js></script>";
						echo "</form><button class=s_btn onclick='create_vote()'>Создать</button>";
						echo "<script>FL();</script></td></tr></table><br>";
					}
				}

				if( $authorized )
				{
    				$vres = f_MQuery( "SELECT * FROM forum_vote_titles WHERE thread_id=$thread" );
    				$varr = f_MFetch( $vres );
    				if( $varr )
    				{
    					echo "<table width=100% border=0><tr><td><script>FUlt();</script>";
    					if( $varr['secret'] ) $sec = 1; else $sec = 0;
    					echo "<b>".($sec?"Закрытое ":'Открытое ')." голосование. $varr[title]</b><br>";
    					$vres = f_MQuery( "SELECT count( player_id ) FROM forum_voters WHERE thread_id=$thread AND player_id=$player_id" );
    					$varr = f_MFetch( $vres );
    					$ok = false;
    					if( !$varr[0] )
    					{
    						if( isset( $_GET['vote'] ) )
    						{
    							$vote = (int)$_GET['vote'];
    							$vres = f_MQuery( "SELECT count( entry_id ) FROM forum_votes WHERE thread_id=$thread AND entry_id=$vote" );
    							$varr = f_MFetch( $vres );
    							if( $varr[0] )
    							{
    								f_MQuery( "LOCK TABLE forum_voters WRITE" );
    								$vres = f_MQuery( "SELECT * FROM forum_voters WHERE thread_id=$thread AND player_id=$player_id" );
    								$varr = f_MFetch( $res );
    								if( $varr[0] ) f_MQuery( "UNLOCK TABLES" );
    								else
    								{
    									f_MQuery( "INSERT INTO forum_voters ( thread_id, entry_id, player_id ) VALUES ( $thread, $vote, $player_id )" );
	    								f_MQuery( "UNLOCK TABLES" );
	    								while( $vote > 0 )
	    								{
	    									f_MQuery( "UPDATE forum_votes SET votes=votes+1 WHERE thread_id=$thread AND entry_id=$vote" );
	    									$vres = f_MQuery( "SELECT parent_id FROM forum_votes WHERE thread_id=$thread AND entry_id=$vote" );
	    									$varr = f_MFetch( $vres );
	    									$vote = $varr[0];
	    								}
    								}
    							} else die( );
    							$ok = true;
								die ( "<script>location.href='forum.php?thread=$thread&page=$page&f=$ret_page';</script>" );
    						}
    						else
    						{
    							function out_answers( $id, $top )
    							{
    								global $thread, $ret_page;
    								$res = f_MQuery( "SELECT * FROM forum_votes WHERE thread_id=$thread AND parent_id=$id ORDER BY entry_id" );
    								if( !f_MNum( $res ) ) return '';
    								$ret = '';
    								if( !$top ) $ret .= "<table cellspacing=0 cellpadding=0><colgroup><col width=20><col width=*><tr><td style='width:20px;'>&nbsp;</td><td>";
    								while( $arr = f_MFetch( $res ) )
    								{
    									$st = out_answers( $arr['entry_id'], false );
    									if( $st == '' ) $ret .= "<li><a href=forum.php?thread=$thread&f=$ret_page&vote=$arr[entry_id]>$arr[txt]</a><br>";
    									else $ret .= "<li><b>$arr[txt]</b><br>$st";
    								}
    								if( !$top ) $ret .= "</td></tr></table>";
    								return $ret;
    							}                                                                                                                                     
    							echo out_answers( 0, true );
    						}
    					} else $ok = true;
    					if( $ok || $player->Rank() == 1 )
    					{
    						if( !$sec && $_GET['voters'] )
    						{
    							echo "<b>Список проголосовавших в обратном хронологическом порядке</b> - <a href=forum.php?thread=$thread&f=$ret_page>вернуться к просмотру результатов</a><br>";
    							$vres = f_MQuery( "SELECT player_id, txt FROM forum_voters as p INNER JOIN forum_votes as v ON p.entry_id=v.entry_id WHERE v.thread_id=$thread ORDER BY prim_id DESC" );
    							while( $varr = f_MFetch( $vres ) )
    							{
    								$plr = new Player( $varr[0] );
    								echo "<script>document.write( ".$plr->Nick()." );</script>: $varr[1]<br>";
    							}
    						}
    						else
    						{
    							function out_res( $id )
    							{
    								global $thread, $ret_page;
    								$res = f_MQuery( "SELECT * FROM forum_votes WHERE thread_id=$thread AND parent_id=$id ORDER BY entry_id" );
    								if( !f_MNum( $res ) ) return '';
    								$sres = f_MQuery( "SELECT sum( votes ) FROM  forum_votes WHERE thread_id=$thread AND parent_id=$id" );
    								$sarr = f_MFetch( $sres );
    								$sum = $sarr[0]; if( $sum == 0 ) $sum = 1;

       								$ret = '';
    								while( $arr = f_MFetch( $res ) )
    								{
    									$ret .= "<tr><td>$arr[txt]: </td><td><b>$arr[votes]</b></td><td>";
    									$per = ( $arr['votes'] / $sum );
    									$w = (int)($per*156); if( $w < 1 ) $w = 1;
    									$per = ((int)(10000*$per))/100.0;
    									$st = '';
    									$varr = f_MFetch( f_MQuery( "SELECT count( entry_id ) FROM forum_votes WHERE parent_id=$arr[entry_id]" ) );
    									if( $varr[0] ) $st = " (<a href=forum.php?thread=$thread&f=$ret_page&explain=$arr[entry_id]>подробнее</a>)";
    									$ret .= "<table style='width:156px;height:15px;' background='images/icons/hp_bg.gif' cellspacing=0 cellpadding=0><colgroup><col width=$w><col width=*><tr><td background='images/icons/hp_fg.gif'><img src=empty.gif width=1 height=1></td><td><img src=empty.gif width=1 height=1></td></tr></table></td><td><b>[{$per}%]</b>$st</td></tr>";
    								}
    								return $ret;
    							}

    							$vid = (int)$_GET['explain'];
       								if( $vid != 0 )
	  								{
    									$sres = f_MQuery( "SELECT * FROM forum_votes WHERE thread_id=$thread AND entry_id=$vid" );
	    								$sarr = f_MFetch( $sres );
	    								if( !$sarr ) die( );
	    								$st = ", $sarr[txt]</b> - <a href=forum.php?thread=$thread&f=$ret_page&explain=$sarr[parent_id]>назад</a>";
    								}
    								else $st = '</b>';

    							echo "<b>Результаты голосования$st"; if( !$sec ) echo " - <a href=forum.php?thread=$thread&f=$ret_page&voters=1>показать список проголосовавших</a><br>";
    							echo "<table>";
    							echo out_res( $vid );
    							echo "</table>";
    						}
    					}
						echo "<script>FL();</script></td></tr></table><br>";
    				}
				}
			// votes end -------------------------------


			if( post_alowed( $room_id, $thread ) )
			{
				if( isset( $HTTP_POST_VARS['add_post'] ) )
				{
					$text = trim( HtmlSpecialChars( $HTTP_POST_VARS['txt'] ) );
					if ($player->player_id == 6825 || $player->player_id == 67573 || $player->player_id == 173) $text = trim( $HTTP_POST_VARS['txt'] ) ;
					$text2 = str_replace( "\n", "<br>", $text );
					$text2 = f_MEscape(process_str( $text2 ));
					$author_id = $player->player_id;
					$time = time( );
					$thread_id = $thread;
					
					$arr2 = f_MFetch( f_MQuery( "SELECT silence, silence_reason FROM player_permissions WHERE player_id={$player->player_id}" ) );
					if( $arr2 && $arr2[0] > time( ) )
						$err = "На вас наложено молчание<br>";
					else if( $text == "" )
						$err = "Вы не ввели текст сообщения<br>";
					else
					{
						f_MQuery( "UPDATE forum_rooms SET posts = posts + 1, last_post = $time, last_post_by = {$player->player_id} WHERE id = $room_id" );
						f_MQuery( "UPDATE forum_threads SET posts = posts + 1, last_post_made = $time, last_post_author = {$player->player_id} WHERE thread_id = $thread_id" );
						f_MQuery( "INSERT INTO forum_posts ( author_id, time, text, thread_id ) VALUES ( $author_id, $time, '$text2', $thread_id )" );
						$new_post = mysql_insert_id( );
						++ $post_num;
						$page = $post_num / 20;
						settype( $page, 'integer' );

// Блок форумной истории при комментария в существующем топике
// проверка есть ли в истории топ с таким ИД и таким автором
$f_p_h = f_MFetch( f_MQuery( "select id_top, id_player_save from forum_player_history where id_top=$thread_id AND id_player_save=$author_id"));
if(!$f_p_h)// если $f_p_h пусто то вносим в историю посещение
{
 // проверяем на авторство
 $author = f_MFetch( f_MQuery( "select author_id from forum_threads where thread_id=$thread_id"));
 if($author['author_id']==$player->player_id)
  {// если свой топ то history_type=1
  $h_type=1;
  }
  else
  {// если чужой топ то history_type=2
    $h_type=2;
  }
 $h_title = f_MFetch( f_MQuery( "select title from forum_threads where thread_id=$thread_id"));
 $nm_lnk =  "<a href=forum.php?thread=$thread_id target=_blank>".addslashes($h_title['title'])."</a>";
  f_MQuery( "INSERT INTO forum_player_history (id_player_save, id_top, www_name, www_link, history_type, last_time_post)   VALUES ($player->player_id, ".$thread_id.", '".addslashes($h_title['title'])."', '".$nm_lnk."', ".$h_type.", ".$time.")");
}

// если $f_p_h не пусто, то нам нафиг ничо нинада делать
// конец типо

						die ( "<script>location.href='forum.php?thread=$thread&page=$page&f=0';</script>" );
					}
				}
			}
			if( isset( $HTTP_POST_VARS[edit_post] ) )
			{
				$post_id = $HTTP_POST_VARS[edit_post];
				settype( $post_id, 'integer' );
				
				$res12 = f_MQuery( "SELECT * FROM forum_posts WHERE post_id = $post_id" );
				$arr12 = f_MFetch( $res12 );
				if( !$arr12 ) RaiseError( "Попытка редактировать несуществующий пост. ИД: $post_id" );
				if( edit_alowed( $room_id, $arr12[author_id] ) )
				{
					$text = trim( HtmlSpecialChars( $HTTP_POST_VARS[txt] ) );
					if( $player->player_id == 173 || $player->player_id == 3264 || $player->player_id == 6825 || $player->player_id == 67573 || $player->player_id == 173 ) $text = trim( $HTTP_POST_VARS[txt] );
					$text2 = str_replace( "\n", "<br>", $text );
					$text2 = f_MEscape(process_str( $text2 ));
					$tm = date( "d.m.Y H:i", time( ) );
					$text2 .= "<br><br><i>Последний раз редактировался: <b>{$player->login}</b>, $tm</i>";
					f_MQuery( "UPDATE forum_posts SET text = '$text2' WHERE post_id = $post_id" );
					die ( "<script>location.href='forum.php?thread=$thread&page=$page&f=$ret_page';</script>" );
				}
			}
			
			$q = 20 * $page;
			$res = f_MQuery( "SELECT * FROM forum_posts WHERE thread_id = $thread ORDER BY post_id LIMIT $q, 20" );
			print( "<table width=100% border=0>" );
			$id = $q + 1;
			while( $arr = f_MFetch( $res ) )
			{
				$tm = date( "d.m.Y H:i", $arr[time] );
				print( "<tr><td valign=top width=160 height=100%>" );
				echo "<script>FUlt();</script>";
				if( $new_post == $arr[post_id] ) print( "<a name=last_post></a>" );
				$moo_plr = new Player( $arr[author_id] );
				echo "<center><i><b>#$id.</b></i>&nbsp;<a name=p$id></a>"; ++ $id;
				print( "<script>document.write( ".$moo_plr->Nick()." );</script>" );
				$pumpa = "";
				if( !$avatars[$arr[author_id]] )
				{
					$amoo = f_MFetch( f_MQuery( "SELECT type FROM forum_avatars WHERE player_id=$arr[author_id] AND expires > ".time( ) ) );
					if( !$amoo ) $avatars[$arr[author_id]] = -1;
					else $avatars[$arr[author_id]] = $amoo[0];
				}
				if( $avatars[$arr[author_id]] != -1 )
					$pumpa .= "<table><tr><td><script>FUlt();</script><img width=100 height=100 src=images/forum_avatars/p$arr[author_id].".(($avatars[$arr[author_id]] == 2)?'jpg':'gif')."><script>FL();</script></td></tr></table>";
				$pumpa .= "$tm";
				if( edit_alowed( $room_id, $arr[author_id] ) ) $pumpa .= "<br><a href=forum.php?post=$arr[post_id]&f=$ret_page&page=$page>Редактировать</a>";
				if( delete_alowed( $room_id, $arr[author_id] ) && $id > 2 ) $pumpa .= "<br><a href='#' onclick='if( confirm( \"Удалить сообщение?\" ) ) location.href=\"forum.php?delete=$arr[post_id]&thread=$thread&f=$ret_page&page=$page\";'>Удалить</a>";
//				if( post_alowed( $room_id, $thread ) ) $pumpa .= "<br><a href='javascript:void(0);' onclick='quote(\"".$moo_plr->login."\",$arr[post_id]);'>Цитировать</a>";
				print( "<br>$pumpa</center><script>FL();</script></td><td height=100% valign=top><script>FUlt();</script><div id=dv$arr[post_id] onmousedown='quoteUsername=\"".$moo_plr->login."\"'>$arr[text]</div><script>FL();</script></td><tr>" );
			}
			print( "</table>" );
			echo "Используйте тег &laquo;(форум:$thread)&raquo; без кавычек на форуме и в чате для размещения ссылки на эту тему.";
			echo "<script>FLL();</script>";
			print( "</td></tr></table>" );
			print( "<br><a href=forum.php?room=$room_id&page=$ret_page>Назад к темам раздела</a><br><br>" );
			
			if( $post_num >= 20 )
			{
				print( "Страница: " );
				print( "<script>\n" );
				$la = $post_num / 20 + 1;
				settype( $la, 'integer' );
				print( "for( i = 0; i < $la; ++ i )\n" );
				print( "if( i == $page ) document.write( '<b>' + ( i + 1 ) + '</b> ' );" );
				print( "else document.write( '<a href=forum.php?thread=$thread&page=' + i + '&f=$ret_page>' + ( i + 1 ) + '</a> ' );" );
				print( "</script>\n" );
				print( "<br><br>" );
			}
	
			if( post_alowed( $room_id, $thread ) )
			{
			?>

<script>
var quoteUsername = 'Кто-то загадочный';
function quoteSelection() 
{
  var sel;
  if (document.selection)
  {
    sel = document.selection.createRange().text;
    if (sel)
    {              
      f1( document.q.txt, '(автор)' + quoteUsername + '(цитата)' + sel + '(!цитата)\n','');
      return false;
    }
  }
  else if (document.getSelection)
  {
    sel = document.getSelection();
    if (sel)
    {
      f1( document.q.txt, '(автор)' + quoteUsername + '(цитата)' + sel + '(!цитата)\n','');
      return false;
    }
  }

  alert('Ничего не выделено');
  return false;
}
function quote(a,b) 
{
   f1( document.q.txt, '(автор)' + a + '(цитата)' + document.getElementById( 'dv' + b ).innerHTML + '(!цитата)\n','');
   return false;
}
</script>
			<?
				print( "<br>$err<b>Оставить сообщение:</b><br>" );
				print( "<table cellspacing=0><form name=q action=forum.php?thread=$thread&page=$page&f=$ret_page#last_post method=post><tr><td align=right>" );
				echo "<script>FUrt();</script>";
				print( "<input type=hidden name=add_post value=1>" );
				insert_text_edit( "q", "txt", "" );
//				print( "<textarea rows=5 cols=60 name=txt class=te_btn>$text</textarea><br>" );
				print( "<center><table><tr><td><input type=button class=s_btn onclick='quoteSelection();return false;' value='Цитировать выделенное'></td><td><input class=s_btn type=submit value='Ответить'></td></tr></table></center>" );
				echo "<script>FL();</script>";
				print( "</td></tr></form></table>" );
			}
			else print( "<br><i>Вы не можете отвечать в этой теме</i>" );
		}
	}
}

else if( isset( $HTTP_GET_VARS['room'] ) )
{
	$room = $HTTP_GET_VARS['room'];
	settype( $room, 'integer' );
	
	if( !looking_alowed( $room ) ) forum_permission_denied( );
	
	if( !isset( $forum_room_names[$room] ) ) die( "Нет такой комнаты" );
	
	if( isset( $HTTP_GET_VARS[page] ) )
	{
		$page = $HTTP_GET_VARS[page];
		settype( $page, 'integer' );
	}
	else $page = 0;
	
	$err = "";
	if( thread_alowed( $room ) )
	{
		if( isset( $HTTP_POST_VARS[create_thread] ) )
		{
			$title = f_MEscape(trim( HtmlSpecialChars( $HTTP_POST_VARS[title] ) ));
			$text = trim( HtmlSpecialChars( $HTTP_POST_VARS[txt] ) );
			if( $player->player_id == 173 || $player->player_id == 3264 || $player->player_id == 6825 || $player->player_id == 67573 || $player->player_id == 173 )
				$text = trim( $HTTP_POST_VARS[txt] );
			$text2 = str_replace( "\n", "<br>", $text );
			$text2 = f_MEscape(process_str( $text2 ));
			$author_id = $player->player_id;
			$time = time( );
			$room_id = $room;
			
			$arr2 = f_MFetch( f_MQuery( "SELECT silence, silence_reason FROM player_permissions WHERE player_id={$player->player_id}" ) );
			if( $arr2 && $arr2[0] > time( ) )
				$err = "На вас наложено молчание<br>";
			else if( $title == "" || $text == "" )
				$err = "Вы не указали заголовок или текст новой темы<br>";
			else
			{
				f_MQuery( "UPDATE forum_rooms SET threads = threads + 1, posts = posts + 1, last_post = $time, last_post_by = {$player->player_id} WHERE id = $room_id" );
				f_MQuery( "INSERT INTO forum_threads ( author_id, last_post_author, time, last_post_made, important, title, room_id ) VALUES ( $author_id, $author_id, $time, $time, 0, '$title', $room_id )" );
				$thread_id = mysql_insert_id( );
				f_MQuery( "INSERT INTO forum_posts ( author_id, time, text, thread_id ) VALUES ( $author_id, $time, '$text2', $thread_id )" );
//  тут же и заинсертим хистори по случаю нового топа
// id_player_save, id_top, www_name, www_link, history_type

// проверка есть ли в истории топ с таким ИД и таким автором

$f_p_h = f_MFetch( f_MQuery( "select id_top, id_player_save from forum_player_history where id_top=$thread_id AND id_player_save=$author_id"));
if(!$f_p_h)// если $f_p_h пусто то вносим в историю посещение
{
 // проверяем на авторство
 $author = f_MFetch( f_MQuery( "select author_id from forum_threads where thread_id=$thread_id"));
 if($author['author_id']==$player->player_id)
  {// если свой топ то history_type=1
  $h_type=1;
  }
  else
  {// если чужой топ то history_type=2
    $h_type=2;
  }
 $h_title = f_MFetch( f_MQuery( "select title from forum_threads where thread_id=$thread_id"));
 $nm_lnk =  "<a href=forum.php?thread=$thread_id target=_blank>".addslashes($h_title['title'])."</a>";
  f_MQuery( "INSERT INTO forum_player_history (id_player_save, id_top, www_name, www_link, history_type, last_time_post)   VALUES ($player->player_id, ".$thread_id.", '".addslashes($h_title['title'])."', '".$nm_lnk."', ".$h_type.", ".$time.")");

}
// если $f_p_h не пусто, то нам нафиг ничо нинада делать
// конец типо
				die ( "<script>location.href='forum.php?room=$room';</script>" );
			}
		}
	}
	
	$res = f_MQuery( "SELECT threads FROM forum_rooms WHERE id = $room" );
	$arr = f_MFetch( $res );
	$thread_num = $arr[0];

	print( "<center><table width=100%><tr><td>" );
	echo "<script>FLUl();</script>";

	echo "<a href=forum.php id=forums_lnk><b>Форумы Алидерии</b></a>&nbsp;<a href=# onclick='show_rooms();'>&#9660;</a>&nbsp;&raquo;&nbsp;<a href=forum.php?room=$room id=topics_lnk><b>$forum_room_names[$room]</b></a>&nbsp;<a href=# onclick='show_topics($room);'>&#9660;</a>";

	$q = $page * 20;
	$res = f_MQuery( "SELECT * FROM forum_threads WHERE room_id = $room ORDER BY important DESC, last_post_made DESC LIMIT $q, 20" );
	if( !mysql_num_rows( $res ) ) print( "<i>В этой комнате нет ни одной темы.</i><br>" );
	else
	{
		print( "<table border=0 width=100% onmousedown='hide_navi();'>" );
		echo "<colgroup><col width=*><col width=180><col width=80><col width=150>";
		echo "<tr><td><script>FUcm();</script><b>Название</b><script>FL();</script></td><td><script>FUcm();</script><b>Автор</b><script>FL();</script></td><td><script>FUcm();</script><b>Сообщений</b><script>FL();</script></td><td><script>FUcm();</script><b>Последнее сообщение</b><script>FL();</script></td></tr>";
		while( $arr = f_MFetch( $res ) )
		{
			print( "<tr><td height=100%>" );
			echo "<script>FUlt();</script>";
			print( "<a onmousemove='show_post( event, $arr[thread_id] )' onmouseout='hideTooltip();cur_post_loading=-1;' href=forum.php?thread=$arr[thread_id]&f=$page>" );
			if( $arr[important] == 1 ) print( "<b>Прикреплена:</b> " );
			$tm = date( "d.m.Y H:i", $arr[last_post_made] );
			$lres = f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[last_post_author]" );
			$larr = f_MFetch( $lres );
			if( $arr[important] == -1 ) print( "<font color=red>Тема удалена</font></a>" );
			else
			{
				print( "$arr[title]</a>" );
				if( $arr[closed] == 1 ) print( " (закрыта) " );
			}
			if( $arr[posts] >= 20 )
			{
				$pn = $arr[posts] / 20 + 1;
				settype( $pn, 'integer' );
				print( "<br>Перейти на страницу:&nbsp;[" );
				if( $pn <= 4 )
					for( $i = 0; $i < $pn; ++ $i )
					{
						if( $i ) print( ", " );
						print( "<a href=forum.php?thread=$arr[thread_id]&page=$i&f=$page>".( $i + 1 )."</a>" );
					}
					
				else
				{
					print( "<a href=forum.php?thread=$arr[thread_id]&page=0&f=$page>".( 1 )."</a>" );
					print( ", <a href=forum.php?thread=$arr[thread_id]&page=1&f=$page>".( 2 )."</a>" );
					print( " ... " );
					print( "<a href=forum.php?thread=$arr[thread_id]&page=".( $pn - 2 )."&f=$page>".( $pn - 1 )."</a>" );
					print( ", <a href=forum.php?thread=$arr[thread_id]&page=".( $pn - 1 )."&f=$page>".( $pn )."</a>" );
				}
					
				print( "]" );
			}
			if( $arr['adult'] )
			{
				echo "<br>";
				echo "<font color=darkred><b>Предупреждение: </b></font> Тема носит агрессивный характер";
			}
			echo "<script>FL();</script></td><td height=100%><script>FUcm();</script>";
			$moo_plr = new Player( $arr[author_id] );
			echo "<script>document.write( ".$moo_plr->Nick()." );</script>";
			echo "<script>FL();</script></td><td height=100%><script>FUcm();</script>";
			echo "$arr[posts]";
			echo "<script>FL();</script></td><td height=100%><script>FUcm();</script>";
			echo "$tm<br>$larr[0]";
			echo "<script>FL();</script></td></tr>";
		}	
		print( "</table>" );
	}

	echo "<script>FLL();</script>";
	print( "</td></tr></table>" );

	print( "<br><a href=forum.php>Назад к разделам форума</a><br>" );

	if( $thread_num > 20 )
	{
		print( "Страница: " );
		print( "<script>\n" );
		$la = ( $thread_num - 1 ) / 20 + 1;
		settype( $la, 'integer' );
		print( "for( i = 0; i < $la; ++ i )\n" );
		print( "if( i == $page ) document.write( '<b>' + ( i + 1 ) + '</b> ' );" );
		print( "else document.write( '<a href=forum.php?room=$room&page=' + i + '>' + ( i + 1 ) + '</a> ' );" );
		print( "</script>\n" );
		print( "<br><br>" );
	}

	if( thread_alowed( $room ) )
	{
		print( "<br>$err<b>Создать новую тему</b><br>" );
		print( "<table border=0 cellspacing=0><form name=q action=forum.php?room=$room method=post><tr><td align=right>" );
		echo "<script>FUrt();</script>";
		print( "<input type=hidden name=create_thread value=1>" );
		print( "<b>Название темы:</b> <input type=text class=l_btn name=title value='$title'><br>" );
//		print( "<textarea rows=5 cols=60 name=txt class=te_btn>$text</textarea><br>" );
		insert_text_edit( "q", "txt", "" );
		print( "<center><input class=s_btn type=submit value='Создать'><center>" );
		echo "<script>FL();</script>";
		print( "</td></tr></form></table>" );
	}
	else print( "<br><i>Вы не можете создавать новые темы</i>" );
}

else
{
	// Начало Forum Rooms
	$forum_room_descs[0] = "В этой комнате можно найти официальную информацию о последних нововведениях от администрации";
	$forum_room_descs[1] = "Если вы нашли в игре различные ошибки, недоработки, опечатки или несоответствия, напишите про них в этой комнате форума.";
	$forum_room_descs[2] = "В этой комнате администрация публикует вопросы к игрокам, связанные с последними и готовящимися новвовведениями.";
	$forum_room_descs[3] = "Все вопросы, касающиеся непосредственно игры, обсуждаются в этой комнате";
	$forum_room_descs[4] = "Если у вас есть вопрос по прохождению того или иного квеста, задайте его в этой комнате.<br><font color=red>Внимание!</font> За пределами этой комнаты обсуждать прохождение квестов запрещено.";
	$forum_room_descs[5] = "В этой комнате вы можете высказать свои идеи и предложения по улучшению игрового процесса.";
	$forum_room_descs[6] = "Обращения и вопросы к конкретным персонажам следует размещать в этой комнате.";
	$forum_room_descs[7] = "В этой комнате можно обсуждать любые вопросы, связанные или не связанные с игрой. Пустой флуд не приветствуется.";
	$forum_room_descs[8] = "Если вы хотите поделиться вашими шедеврами с другими игроками, вы можете опубликовать их в этой комнате.<br>Не следует публиковать в этой комнате работы, выполненные не игроками алидерии";
	$forum_room_descs[9] = "В этой комнате вы можете оставить объявление о покупке.";
	$forum_room_descs[10] = "В этой комнате вы можете оставить объявление о продаже.";

	$forum_room_descs[19] = "Комната для модераторов чата.";
	$forum_room_descs[20] = "Доступ в эту комнату имеют только модераторы и администраторы.";
	$forum_room_descs[21] = "Репетиция рекламы соков. Дети:<br>- А я - вишенка..<br>- А я - яблочко...<br>- А я - томат...<br>- А я - долбоеб....<br>Мальчик!!! Ты - БАКЛАЖАН!!! Еще раз:<br>- А я - вишенка..<br>- А я - яблочко...<br>- А я - томат...<br>- А я - долбоеб....<br>Мальчик!!! Повторяю - Ты - БАКЛАЖАН!!! Еще раз:<br>- А я - вишенка..<br>- А я - яблочко...<br>- А я - баклажан!!!<br>Мальчик!!! Ты - долбоеб!!!! Сначала идет - томат!!.<br>";
		
	print( "<center><br><table width=100%><tr><td>" );
	echo "<script>FLUl();</script>";
	
	$res = f_MQuery( "SELECT * FROM forum_rooms ORDER BY id" );
	print( "<table width=100% border=0><colgroup><col width=*><col width=80><col width=80><col width=150>" );
	print( "<tr><td align=center height=100%><script>FUcm();</script><b>Название</b><script>FL();</script></td><td width=80 align=center height=100%><script>FUcm();</script><b>Тем</b><script>FL();</script></td><td width=80 align=center height=100%>".GetScrollTableStart( "center", "middle" )."<b>Сообщений</b>".GetScrollTableEnd( )."</td><td align=center height=100%>".GetScrollTableStart( "center", "middle" )."<b>Последнее<br>Сообщение</b>".GetScrollTableEnd( )."</td>" );
	
	while( $arr = f_MFetch( $res ) ) if( looking_alowed( $arr[id] ) )
	{
		if( $arr['id'] == 0 ) echo "<tr><td colspan=4 align=center><b>Администраторская</b></td></tr>";
		if( $arr['id'] == 3 ) echo "<tr><td colspan=4 align=center><b>Игровые Комнаты</b></td></tr>";
		if( $arr['id'] == 7 ) echo "<tr><td colspan=4 align=center><b>Свободное Общение</b></td></tr>";
		if( $arr['id'] == 9 ) echo "<tr><td colspan=4 align=center><b>Торговля</b></td></tr>";
		if( $arr['id'] == 11 ) echo "<tr><td colspan=4 align=center><b>Жалобы</b></td></tr>";
		if( $arr['id'] == 19 ) echo "<tr><td colspan=4><marquee><b>А это тока для конкретных пацанов</b></marquee></td></tr>";

if ($player->player_id==6825)
	$ssttrr = $arr[id];
else $ssttrr = "";

		print( "<tr><td height=100%><script>FUlt();</script><a href=forum.php?room=$arr[id]><b>{$ssttrr} {$forum_room_names[$arr[id]]}</b></a><br>{$forum_room_descs[$arr[id]]}<script>FL();</script></td><td align=center height=100%><script>FUcm();</script>$arr[threads]<script>FL();</script></td><td align=center height=100%><script>FUcm();</script>$arr[posts]<script>FL();</script></td>" );
		
		if( $arr[posts] )
		{
			$lres = f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[last_post_by]" );
			$larr = f_MFetch( $lres );
			$tm = date( "d.m.Y H:i", $arr[last_post] );
			
			print( "<td height=100% align=center><script>FUcm();</script>$tm<br>от $larr[0]<script>FL();</script></td>" );
		}
		else print( "<td height=100% align=center><script>FUcm();</script><i>Нет сообщений</i><script>FL();</script></td>" );
		
		print( "</tr>" );
	}
	
	print( "</table>" );
		
	// Конец Forum Rooms
	echo "<script>FLL();</script>";
	print( "</td></tr></table>" );
}

print( "</td></tr></table></center>" );

echo "<div onmousedown='navi_down(event);' id=navi style='display:none;position:absolute;left:0px;top:0px;border:1px solid black;background-color:#E0C3A0'></div>";

?>
