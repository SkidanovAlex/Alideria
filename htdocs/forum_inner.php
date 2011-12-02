<?

include_once( 'skin.php' );
include_js( 'js/skin.js' );

echo "<script>var isForum = true;</script>";

// ����� ������� ����������� ��� �� � admin_forum_ranks.php
$forum_room_names = Array( 22 => "����� ��� �������� ����������", 21 => "The TOP SECRET project IDE!", 0 => "������� �� �������", 1 => "������ ����", 2 => "����� �������", 3 => "����������� �������", 4 => "���� � �����������", 5 => "���������", 6 => "��������� �������", 7 => "���������� �������", 9 => "���������� - �������", 8 => "���������� - �������", 20 => "�������� ������ �����������" );

$authorized = false;
if( check_cookie( ) )
{
	$authorized = true;
	$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
}

$res = f_MQuery( "SELECT clan_id, name FROM clans" );
while( $arr = f_MFetch( $res ) )
	$forum_room_names[- $arr['clan_id']] = $arr['name']." - �������� �����";

function thread_alowed( $room )
{
	global $authorized;
	global $player;
	global $forum_room_names;

	if( $authorized && $player->level < 2 ) return false;
	
	if( !$forum_room_names[$room] ) return false;
	
	if( !$authorized ) return false;
	if( $room == 20 && $player->Rank( ) == 0 ) return false;
	if( $room == 21 && $player->Rank( ) != 1 ) return false;
	if( $room == 22 && ( !$player || $player->player_id > 174 ) ) return false;
	if( $room == 0 && $player->Rank( ) != 1 ) return false;

	if( $room < 0 ) return $player->clan_id == - $room;
	
	return true;
}

function post_alowed( $room, $thread )
{
	global $authorized;
	global $player;
	global $forum_room_names;
	
	if( $authorized && $player->level < 2 ) return false;

	if( !$forum_room_names[$room] ) return false;
	
	if( !$authorized ) return false;
	if( $room == 20 && $player->Rank( ) == 0 ) return false;
	if( $room == 21 && $player->Rank( ) != 1 ) return false;
	if( $room == 22 && ( !$player || $player->player_id > 174 ) ) return false;

	$res = f_MQuery( "SELECT closed, important FROM forum_threads WHERE thread_id = $thread" );
	$arr = f_MFetch( $res );
	if( !$arr ) RaiseError( "������ post_alowed, ��� ����. �������: $room, ID: $thread" );
	if( $arr[closed] == 1 || $arr[important] == -1 ) return false;
	
	if( $room < 0 ) return $player->clan_id == - $room;

	return true;
}

function looking_alowed( $room )
{
	global $authorized;
	global $player;
	global $forum_room_names;

	if( $authorized && $player->Rank( ) == 1 ) return true;
	
	if( !$forum_room_names[$room] ) return false;
	
	if( $room == 20 && ( !$authorized || $player->Rank( ) == 0 ) ) return false;
	if( $room == 21 && ( !$authorized || $player->Rank( ) != 1 ) ) return false;
	if( $room == 22 && ( !$player || $player->player_id > 174 ) ) return false;
	
	if( $room < 0 )
	{
		if( !$authorized ) return false;
		return $player->clan_id == - $room;
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

	return moder_alowed( $room_id ) || ( $authorized && $author == $player->player_id );
}

function forum_permission_denied( )
{
	RaiseError( "������������������� ������� ��������� �������� ��� �� ������������ ������ ������." );
}

print( "<center><table width=90%><tr><td>" );

if( isset( $HTTP_GET_VARS[post] ) )
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
	if( !$arr ) print( "��� ������ �����" );
	else
	{
		$thread_id = $arr[thread_id];
		$res2 = f_MQuery( "SELECT room_id, title FROM forum_threads WHERE thread_id = $thread_id" );
		$arr2 = f_MFetch( $res2 );
		if( !$arr2 ) RaiseError( "������������ ���� � �������������� ����. ����: $post_id, ����: $thread_id" );
		$room_id = $arr2[0];
		
		if( !edit_alowed( $room_id, $arr[author_id] ) ) print( "�� �� ������ ������������� ���� ����" );
		else
		{
			print( "<b>$arr2[1]</b><br><br><a href=forum.php?thread=$thread_id&f=$ret_page&page=$page>����� (�� ������� ���������)</a><br><br><table width=70%><tr><td>" );
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

				print( "<br>$err<b>�������� ���������:</b><br>" );
				print( "<table border=1 cellspacing=0><form name=q action=forum.php?thread=$thread_id&page=$page&f=$ret_page method=post><tr><td align=right>" );
				print( "<input type=hidden name=edit_post value=$post_id>" );
				insert_text_edit( "q", "txt", process_str_inv( $arr[text] ) );
//				print( "<textarea rows=5 cols=60 name=txt class=te_btn>$text</textarea><br>" );
				print( "<center><input class=s_btn type=submit value='��������'><center>" );
				print( "</td></tr></form></table>" );
		}
	}
}

else if( isset( $HTTP_GET_VARS[thread] ) )
{
	$thread = $HTTP_GET_VARS[thread];
	settype( $thread, 'integer' );
	
	if( isset( $HTTP_GET_VARS[page] ) )
	{
		$page = $HTTP_GET_VARS[page];
		settype( $page, 'integer' );
	}
	else $page = 0;
	
	$ret_page = $HTTP_GET_VARS['f'];
	settype( $ret_page, 'integer' );
	
	$res = f_MQuery( "SELECT title, room_id, posts, important, closed FROM forum_threads WHERE thread_id = $thread" );
	
	if( !mysql_num_rows( $res ) ) print( "��� ����� ����, <a href=forum.php>��������� �� �����</a>" );
	else
	{
		$arr = f_MFetch( $res );
		$room_id = $arr[1];
		$post_num = $arr[2];
		
		if( !looking_alowed( $room_id ) ) forum_permission_denied( );
	
		if( $arr['important'] != -1 ) print( "<center><b>$arr[0]</b><br>" );
		else print( "<center><b>��������� ����</b><br>" );
		
		if( moder_alowed( $room_id ) )
		{
			if( isset( $_GET['imp'] ) )
			{
				settype( $_GET['imp'], 'integer' );
				if( $_GET['imp'] == 0 || $_GET['imp'] == 1 || $_GET['imp'] == -1 )
				f_MQuery( "UPDATE forum_threads SET important = $_GET[imp] WHERE thread_id = $thread" );
				$res = f_MQuery( "SELECT title, room_id, posts, important, closed FROM forum_threads WHERE thread_id = $thread" );
				$arr = f_MFetch( $res );
			}
			if( isset( $_GET['cls'] ) )
			{
				settype( $_GET['cls'], 'integer' );
				if( $_GET['cls'] == 0 || $_GET['cls'] == 1 )
				f_MQuery( "UPDATE forum_threads SET closed = $_GET[cls] WHERE thread_id = $thread" );
				$res = f_MQuery( "SELECT title, room_id, posts, important, closed FROM forum_threads WHERE thread_id = $thread" );
				$arr = f_MFetch( $res );
			}
			
			if( $arr['important'] == -1 ) print( "��� ���� �������. �� ������ <a href=forum.php?thread=$thread&f=$ret_page&imp=0>�������</a> ��<br>" );
			else
			{
				if( $arr['important'] == 0 ) print( "<a href=forum.php?thread=$thread&f=$ret_page&imp=1>����������</a> | " );
				else print( "<a href=forum.php?thread=$thread&f=$ret_page&imp=0>���������</a> | " );
				if( $arr['closed'] == 0 ) print( "<a href=forum.php?thread=$thread&f=$ret_page&cls=1>�������</a> | " );
				else print( "<a href=forum.php?thread=$thread&f=$ret_page&cls=0>�������</a> |" );
				print( "<a href=forum.php?thread=$thread&f=$ret_page&imp=-1>�������</a><br>" );
			}
		}
		
		print( "<br><a href=forum.php?room=$room_id&page=$ret_page>����� � ����� �������</a><br><br>" );
		
		$new_post = -1;
		$err = "";
		if( $arr['important'] != -1 )
		{
			if( post_alowed( $room_id, $thread ) )
			{
				if( isset( $HTTP_POST_VARS[add_post] ) )
				{
					$text = trim( HtmlSpecialChars( $HTTP_POST_VARS[txt] ) );
					$text2 = str_replace( "\n", "<br>", $text );
					$text2 = process_str( $text2 );
					$author_id = $player->player_id;
					$time = time( );
					$thread_id = $thread;
					
					if( $text == "" )
						$err = "�� �� ����� ����� ���������<br>";
					else
					{
						f_MQuery( "UPDATE forum_rooms SET posts = posts + 1, last_post = $time, last_post_by = {$player->player_id} WHERE id = $room_id" );
						f_MQuery( "UPDATE forum_threads SET posts = posts + 1, last_post_made = $time, last_post_author = {$player->player_id} WHERE thread_id = $thread_id" );
						f_MQuery( "INSERT INTO forum_posts ( author_id, time, text, thread_id ) VALUES ( $author_id, $time, '$text2', $thread_id )" );
						$new_post = mysql_insert_id( );
						++ $post_num;
						$page = $post_num / 20;
						settype( $page, 'integer' );
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
				if( !$arr12 ) RaiseError( "������� ������������� �������������� ����. ��: $post_id" );
				if( edit_alowed( $room_id, $arr12[author_id] ) )
				{
					$text = trim( HtmlSpecialChars( $HTTP_POST_VARS[txt] ) );
					if( $player->player_id == 6825 ) $text = trim( $HTTP_POST_VARS[txt] );
					if( $player->player_id == 173 ) $text = trim( $HTTP_POST_VARS[txt] );
					$text2 = str_replace( "\n", "<br>", $text );
					$text2 = process_str( $text2 );
					$tm = date( "d.m.Y H:i", time( ) );
					$text2 .= "<br><br><i>��������� ��� ��������������: <b>{$player->login}</b>, $tm</i>";
					f_MQuery( "UPDATE forum_posts SET text = '$text2' WHERE post_id = $post_id" );
					die ( "<script>location.href='forum.php?thread=$thread&page=$page&f=$ret_page';</script>" );
				}
			}
			
			$q = 20 * $page;
			$res = f_MQuery( "SELECT * FROM forum_posts WHERE thread_id = $thread ORDER BY post_id LIMIT $q, 20" );
			print( "<table width=70%><tr><td>" );
			echo "<script>FLUl();</script>";
			print( "<table width=100% border=0>" );
			$id = $q + 1;
			while( $arr = f_MFetch( $res ) )
			{
				$tm = date( "d.m.Y H:i", $arr[time] );
				print( "<tr><td valign=top width=160 height=100%>" );
				echo "<script>FUlt();</script>";
				if( $new_post == $arr[post_id] ) print( "<a name=last_post></a>" );
				$moo_plr = new Player( $arr[author_id] );
				echo "<i><b>#$id.</b></i>&nbsp;"; ++ $id;
				print( "<script>document.write( ".$moo_plr->Nick()." );</script>" );
				$pumpa = "";
				if( edit_alowed( $room_id, $arr[author_id] ) ) $pumpa = "<br><a href=forum.php?post=$arr[post_id]&f=$ret_page&page=$page>�������������</a>";
//				if( post_alowed( $room_id, $thread ) ) $pumpa .= "<br><a href='javascript:void(0);' onclick='quote(\"".$moo_plr->login."\",$arr[post_id]);'>����������</a>";
				print( "<br>$tm$pumpa<script>FL();</script></td><td height=100% valign=top><script>FUlt();</script><div id=dv$arr[post_id] onmousedown='quoteUsername=\"".$moo_plr->login."\"'>$arr[text]</div><script>FL();</script></td><tr>" );
			}
			print( "</table>" );
			echo "<script>FLL();</script>";
			print( "</td></tr></table>" );
			print( "<br><a href=forum.php?room=$room_id&page=$ret_page>����� � ����� �������</a><br><br>" );
			
			if( $post_num >= 20 )
			{
				print( "��������: " );
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
var quoteUsername = '���-�� ����������';
function quoteSelection() 
{
  var sel;
  if (document.selection)
  {
    sel = document.selection.createRange().text;
    if (sel)
    {              
      f1( document.q.txt, '(�����)' + quoteUsername + '(������)' + sel + '(!������)\n','');
      return false;
    }
  }
  else if (document.getSelection)
  {
    sel = document.getSelection();
    if (sel)
    {
      f1( document.q.txt, '(�����)' + quoteUsername + '(������)' + sel + '(!������)\n','');
      return false;
    }
  }

  alert('������ �� ��������');
  return false;
}
function quote(a,b) 
{
   f1( document.q.txt, '(�����)' + a + '(������)' + document.getElementById( 'dv' + b ).innerHTML + '(!������)\n','');
   return false;
}
</script>
			<?
				print( "<br>$err<b>�������� ���������:</b><br>" );
				print( "<table cellspacing=0><form name=q action=forum.php?thread=$thread&page=$page&f=$ret_page#last_post method=post><tr><td align=right>" );
				echo "<script>FUrt();</script>";
				print( "<input type=hidden name=add_post value=1>" );
				insert_text_edit( "q", "txt", "" );
//				print( "<textarea rows=5 cols=60 name=txt class=te_btn>$text</textarea><br>" );
				print( "<center><table><tr><td><input type=button class=s_btn onclick='quoteSelection();return false;' value='���������� ����������'></td><td><input class=s_btn type=submit value='��������'></td></tr></table></center>" );
				echo "<script>FL();</script>";
				print( "</td></tr></form></table>" );
			}
			else print( "<br><i>�� �� ������ �������� � ���� ����</i>" );
		}
	}
}

else if( isset( $HTTP_GET_VARS[room] ) )
{
	$room = $HTTP_GET_VARS[room];
	settype( $room, 'integer' );
	
	if( !looking_alowed( $room ) ) forum_permission_denied( );
	
	if( !isset( $forum_room_names[$room] ) ) die( "��� ����� �������" );
	
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
			$title = trim( HtmlSpecialChars( $HTTP_POST_VARS[title] ) );
			$text = trim( HtmlSpecialChars( $HTTP_POST_VARS[txt] ) );
			if( $player->player_id == 173 )
				$text = trim( $HTTP_POST_VARS[txt] );
			if( $player->player_id == 6825 )
				$text = trim( $HTTP_POST_VARS[txt] );
			$text2 = str_replace( "\n", "<br>", $text );
			$text2 = process_str( $text2 );
			$author_id = $player->player_id;
			$time = time( );
			$room_id = $room;
			
			if( $title == "" || $text == "" )
				$err = "�� �� ������� ��������� ��� ����� ����� ����<br>";
			else
			{
				f_MQuery( "UPDATE forum_rooms SET threads = threads + 1, posts = posts + 1, last_post = $time, last_post_by = {$player->player_id} WHERE id = $room_id" );
				f_MQuery( "INSERT INTO forum_threads ( author_id, last_post_author, time, last_post_made, important, title, room_id ) VALUES ( $author_id, $author_id, $time, $time, 0, '$title', $room_id )" );
				$thread_id = mysql_insert_id( );
				f_MQuery( "INSERT INTO forum_posts ( author_id, time, text, thread_id ) VALUES ( $author_id, $time, '$text2', $thread_id )" );
				die ( "<script>location.href='forum.php?room=$room';</script>" );
			}
		}
	}
	
	print( "<center><b>$forum_room_names[$room]</b><br><br>" );
	print( "<a href=forum.php>����� � �������� ������</a><br><br>" );
	
	$res = f_MQuery( "SELECT threads FROM forum_rooms WHERE id = $room" );
	$arr = f_MFetch( $res );
	$thread_num = $arr[0];

	$q = $page * 12;
	$res = f_MQuery( "SELECT * FROM forum_threads WHERE room_id = $room ORDER BY important DESC, last_post_made DESC LIMIT $q, 12" );
	if( !mysql_num_rows( $res ) ) print( "<i>� ���� ������� ��� �� ����� ����.</i><br>" );
	else
	{
		print( "<table width=70%><tr><td>" );
		echo "<script>FLUl();</script>";
		print( "<table border=0 width=100%>" );
		while( $arr = f_MFetch( $res ) )
		{
			print( "<tr><td>" );
			echo "<script>FUlt();</script>";
			print( "<a href=forum.php?thread=$arr[thread_id]&f=$page>" );
			if( $arr[important] == 1 ) print( "<b>�����������:</b> " );
			$tm = date( "d.m.Y H:i", $arr[last_post_made] );
			$lres = f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[last_post_author]" );
			$larr = f_MFetch( $lres );
			if( $arr[important] == -1 ) print( "<font color=red>���� �������</font></a>" );
			else
			{
				print( "$arr[title]</a>" );
				if( $arr[closed] == 1 ) print( " (�������) " );
			}
			if( $arr[posts] >= 20 )
			{
				$pn = $arr[posts] / 20 + 1;
				settype( $pn, 'integer' );
				print( "<br>������� �� ��������: [" );
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
			print( "<br>" );
			$moo_plr = new Player( $arr[author_id] );
			print( "�����: <script>document.write( ".$moo_plr->Nick()." );</script>, ���������: $arr[posts]<br>��������� ���������: $tm, $larr[0]<script>FL();</script></td></tr>" );
		}	
		print( "</table>" );
		echo "<script>FLL();</script>";
		print( "</td></tr></table>" );
	}
	
	print( "<br><a href=forum.php>����� � �������� ������</a><br>" );

	if( $thread_num > 12 )
	{
		print( "��������: " );
		print( "<script>\n" );
		$la = ( $thread_num - 1 ) / 12 + 1;
		settype( $la, 'integer' );
		print( "for( i = 0; i < $la; ++ i )\n" );
		print( "if( i == $page ) document.write( '<b>' + ( i + 1 ) + '</b> ' );" );
		print( "else document.write( '<a href=forum.php?room=$room&page=' + i + '>' + ( i + 1 ) + '</a> ' );" );
		print( "</script>\n" );
		print( "<br><br>" );
	}

	if( thread_alowed( $room ) )
	{
		print( "<br>$err<b>������� ����� ����</b><br>" );
		print( "<table border=0 cellspacing=0><form name=q action=forum.php?room=$room method=post><tr><td align=right>" );
		echo "<script>FUrt();</script>";
		print( "<input type=hidden name=create_thread value=1>" );
		print( "<b>�������� ����:</b> <input type=text class=l_btn name=title value='$title'><br>" );
//		print( "<textarea rows=5 cols=60 name=txt class=te_btn>$text</textarea><br>" );
		insert_text_edit( "q", "txt", "" );
		print( "<center><input class=s_btn type=submit value='�������'><center>" );
		echo "<script>FL();</script>";
		print( "</td></tr></form></table>" );
	}
	else print( "<br><i>�� �� ������ ��������� ����� ����</i>" );
}

else
{
	// ������ Forum Rooms
	$forum_room_descs[0] = "������� �� ������� ���� ����������� � ���� �������";
	$forum_room_descs[1] = "���� �� ����� � ���� ��������� ������, �����������, �������� ��� ��������������, �������� ��� ��� � ���� ������� ������.";
	$forum_room_descs[2] = "��� �������, ���������� ��������������� ����, ����������� � ���� �������";
	$forum_room_descs[3] = "���� � ��� ���� ������ �� ����������� ���� ��� ����� ������, ������� ��� � ���� �������.<br><font color=red>��������!</font> �� ��������� ���� ������� ��������� ����������� ������� ���������.";
	$forum_room_descs[4] = "� ���� ������� �� ������ ��������� ���� ���� � ����������� �� ��������� �������� ��������.";
	$forum_room_descs[5] = "��������� � ������� � ���������� ���������� ������� ��������� � ���� �������.";
	$forum_room_descs[6] = "� ���� ������� ����� ��������� ����� �������, ��������� ��� �� ��������� � �����. ������ ���� �� ��������������.";
	$forum_room_descs[7] = "���� �� ������ ���������� ������ ��������� � ������� ��������, �� ������ ������������ �� � ���� �������.<br>�� ������� ����������� � ���� ������� ������, ����������� �� �������� ��������";
	$forum_room_descs[8] = "� ���� ������� �� ������ �������� ���������� � �������.";
	$forum_room_descs[9] = "� ���� ������� �� ������ �������� ���������� � �������.";

	$forum_room_descs[20] = "������ � ��� ������� ����� ������ ���������� � ��������������.";
	$forum_room_descs[21] = "��������� ������� �����. ����:<br>- � � - �������..<br>- � � - �������...<br>- � � - �����...<br>- � � - �������....<br>�������!!! �� - ��������!!! ��� ���:<br>- � � - �������..<br>- � � - �������...<br>- � � - �����...<br>- � � - �������....<br>�������!!! �������� - �� - ��������!!! ��� ���:<br>- � � - �������..<br>- � � - �������...<br>- � � - ��������!!!<br>�������!!! �� - �������!!!! ������� ���� - �����!!.<br>";
		
	print( "<center><br><table><tr><td>" );
	echo "<script>FLUl();</script>";
	
	$res = f_MQuery( "SELECT * FROM forum_rooms ORDER BY id" );
	print( "<table border=0>" );
	print( "<tr><td align=center height=100%><script>FUcm();</script><b>��������</b><script>FL();</script></td><td width=80 align=center height=100%><script>FUcm();</script><b>���</b><script>FL();</script></td><td width=80 align=center height=100%>".GetScrollTableStart( "center", "middle" )."<b>���������</b>".GetScrollTableEnd( )."</td><td align=center height=100%>".GetScrollTableStart( "center", "middle" )."<b>���������<br>���������</b>".GetScrollTableEnd( )."</td>" );
	
	while( $arr = f_MFetch( $res ) ) if( looking_alowed( $arr[id] ) )
	{
		if( $arr['id'] == 0 ) echo "<tr><td colspan=4><script>FUcm();</script><b>�����������������</b><script>FL();</script></td></tr>";
		if( $arr['id'] == 2 ) echo "<tr><td colspan=4><script>FUcm();</script><b>������� �������</b><script>FL();</script></td></tr>";
		if( $arr['id'] == 6 ) echo "<tr><td colspan=4><script>FUcm();</script><b>��������� �������</b><script>FL();</script></td></tr>";
		if( $arr['id'] == 8 ) echo "<tr><td colspan=4><script>FUcm();</script><b>��������</b><script>FL();</script></td></tr>";
		if( $arr['id'] == 20 ) echo "<tr><td colspan=4><script>FUcm();</script><b>� ��� ���� ��� ���������� �������</b><script>FL();</script></td></tr>";

		print( "<tr><td height=100% width=400><script>FUlt();</script><a href=forum.php?room=$arr[id]><b>{$forum_room_names[$arr[id]]}</b></a><br>{$forum_room_descs[$arr[id]]}<script>FL();</script></td><td align=center height=100%><script>FUcm();</script>$arr[threads]<script>FL();</script></td><td align=center height=100%><script>FUcm();</script>$arr[posts]<script>FL();</script></td>" );
		
		if( $arr[posts] )
		{
			$lres = f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[last_post_by]" );
			$larr = f_MFetch( $lres );
			$tm = date( "d.m.Y H:i", $arr[last_post] );
			
			print( "<td height=100% align=center><script>FUcm();</script>$tm<br>�� $larr[0]<script>FL();</script></td>" );
		}
		else print( "<td height=100% align=center><script>FUcm();</script><i>��� ���������</i><script>FL();</script></td>" );
		
		print( "</tr>" );
	}
	
	print( "</table>" );
		
	// ����� Forum Rooms
	echo "<script>FLL();</script>";
	print( "</td></tr></table>" );
}

print( "</td></tr></table>" );

?>
