<?

require( "smiles_list.php" );

function insert_text_edit( $form, $name, $la="" )
{
	print( "<table>" );
	
	print( "<tr><td><center><table bgcolor=white border=1 cellspacing=0 bordercolor=black><tr><script>" );
	print( "insbtn( '$form.$name', 30, '<b>�</b>', '(�)', '(!�)' );" );
	print( "insbtn( '$form.$name', 30, '<i>�</i>', '(�)', '(!�)' );" );
	print( "insbtn( '$form.$name', 30, '<u>�</u>', '(�)', '(!�)' );" );
	print( "insbtn( '$form.$name', 30, '<s>�</s>', '(�)', '(!�)' );" );
	print( "insbtn( '$form.$name', 92, '<font color=red>�������</font>', '(�������)', '(!����)' );" );
	print( "insbtn( '$form.$name', 92, '<font color=blue>�����</font>', '(�����)', '(!����)' );" );
	print( "insbtn( '$form.$name', 92, '<font color=green>�������</font>', '(�������)', '(!����)' );" );
	print( "insbtn( '$form.$name', 50, '<b>���</b> <img border=0 src=images/i.gif width=11 height=11>', '(���)', '(!���)' );" );
	                                                                                                      
	print( "</script><td><a title='�������� �������' onclick='smiles(\"$form.$name\")' style='cursor:pointer'><img border=0 width=19 height=19 src='images/insert_smiley.png'></a></td></td>" );
	
	print( "<td onclick=\"document.getElementById( 'moreTags' ).style.display='';\" style=\"text-align: center; padding: 0px 5px; cursor: pointer;\">...</td>" );
	echo '</tr></table></center>';
	echo '<table bgcolor=white border=1 cellspacing=0 bordercolor=black style="display: none;" id="moreTags"><tr><script>';
	print( "insbtn( '$form.$name', 92, '�����', '(�����:ID)', '' );" );
	print( "insbtn( '$form.$name', 92, '�����', '(�����:ID)', '' );" );
	print( "insbtn( '$form.$name', 92, '�����', '(�����)', '(!�����)' );" );
	print( "insbtn( '$form.$name', 92, '�������', '(�������)', '(!�������)' );" );		
	echo '</script></tr></table></td></tr>';
	print( "<tr><td><textarea class=te_btn name='$name' rows=10 style=\"width: 100%\" onselect=\"storeCaret(this);\" onclick=\"storeCaret(this);\" onkeyup=\"storeCaret(this);\">$la</textarea></td></tr>" );

	print( "</table>" );
}

function process_str( $str )
{
	global $_GET;
	global $smiles, $vsmiles;
	global $player, $player_id;
	
	// ������ �� �����
	$res = f_MQuery("SELECT spam_name FROM spams");
	$i=0;
	while ($arr = f_MFetch($res)) {$badWords[$i] = $arr[0]; $i++;}
	$count = count( $badWords );
	for( $i = 0; $i < $count; ++ $i )
	{
		if( preg_match( $badWords[$i], $str ) > 0 )
		{
			// ���� ���� ����-�����, ����� �� ��� ����
			$playerId = (int)$_COOKIE['c_id'];
			if( !f_MValue( 'SELECT player_id FROM player_permissions WHERE player_id = '.$playerId ) )
			{		
				f_MQuery( 'INSERT INTO player_permissions( player_id, ban, ban_reason ) VALUES( '.$playerId.', '.( time( ) + 1800 ).', "����" )' );
			}
			else
			{
				f_MQuery( 'UPDATE player_permissions SET ban = '.( time( ) + 1800 ).', ban_reason = "����" WHERE player_id = '.$playerId );
			}
			$komstr = substr($str, strpos($str, $badWords[$i])-100, 230);
			$komstr = preg_replace($badWords[$i], "<b>".$badWords[$i]."</b>", $komstr);
			f_MQuery( "INSERT INTO history_punishments ( time, moderator_login, player_id, reason, duration, type, comments ) VALUES ( ".time( ).", '�������', {$playerId}, '����', 1800, '������������', '��������: ".$komstr."' )" );
			// ���������� �� ������ �������
         $sock = socket_create(AF_INET, SOCK_STREAM, 0);
         socket_connect($sock, "127.0.0.1", 1100);
         $msg = "player\nOffline_{$playerId}\n".mt_rand()."\n{$playerId}\n000000\n000000\n0\n1\n";
         socket_write( $sock, $msg, strlen($msg) ); 
         socket_close( $sock );
			ClearCachedValue('USER:' . $playerId  . ':scrc_key');			
			f_MQuery( 'DELETE FROM online WHERE player_id = '.$playerId );
			
			// ������ � ����
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
			
			return '� �������� ����. �������� ��, ������, ��� �������� ������ ���������� ������� ����! �������� ��, ��� �����, ��� ������ �!';
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
	
	if( !isset( $_GET['where'] ) ) //-- ��� ���� �� ���
	{
		$res = str_replace( "(�)", '<b>', $res );
		$res = str_replace( "(!�)", '</b>', $res );
		$res = str_replace( "(�)", '<i>', $res );
		$res = str_replace( "(!�)", '</i>', $res );
		$res = str_replace( "(�)", '<u>', $res );
		$res = str_replace( "(!�)", '</u>', $res );
		$res = str_replace( "(�)", '<s>', $res );
		$res = str_replace( "(!�)", '</s>', $res );
		$res = str_replace( "\n", '<br>', $res );
		$res = str_replace( '(�������)', '<font color=darkred>', $res );
		$res = str_replace( '(�����)', '<font color=darkblue>', $res );
		$res = str_replace( '(�������)', '<font color=darkgreen>', $res );
		$res = str_replace( '(!����)', '</font>', $res );
		$res = str_replace( '(�����)', '<center>', $res );
		$res = str_replace( '(!�����)', '</center>', $res );
		$res = str_replace( '(�������)', '<span style="color: #cc9966; background: #cc9966;" onmouseover="this.style.color=\'white\'" onmouseout="this.style.color=\'#cc9966\'">', $res );
		$res = str_replace( '(!�������)', '</span>', $res );
		$res = str_replace( '(�������)', '<img src="/images/presents/8m1.gif" alt="(�������)" style="width: 75px; height: 75px;" />', $res );
			
		$p = 0;
		while( 1 )
		{
			$p = $t1 = strpos( $res, "(���)", $p );
			if( $t1 === false )
				break;
			$p = $t2 = strpos( $res, "(!���)", $p );
			if( $t2 === false )
				break;
			
			$t1 += 5;
			$nick = substr( $res, $t1, $t2 - $t1 );
			
			$nick = htmlspecialchars( $nick );
			$mres = f_MQuery( "SELECT player_id FROM characters WHERE login = '$nick'" );
			$marr = f_MFetch( $mres );
			if( !$marr ) $res = str_replace( '(���)'.$nick.'(!���)', "<b>[�� ������������ �����]</b>", $res );
			else
			{
				$plr = new Player( $marr[0] );
				$res = str_replace( '(���)'.$nick.'(!���)', '<script>document.write( '.$plr->Nick( ).' )</script>', $res );
			}
		}
		while( 1 )
		{
			$p = $t1 = strpos( $res, "(�����)", $p );
			if( $t1 === false )
				break;
			$p = $t2 = strpos( $res, "(������)", $p );
			if( $t2 === false )
				break;
			$p = $t3 = strpos( $res, "(!������)", $p );
			if( $t3 === false )
				break;

				
			$t1 += 7;
			$nick = substr( $res, $t1, $t2 - $t1 );
			$t2 += 8;
			$text = substr( $res, $t2, $t3 - $t2 );
			
			$moo = "<div><script>FLUl();</script><b>$nick</b> �����(�):<script>FUlt();</script>$text<script>FL();FLL();</script></div>";
			$res = substr( $res, 0, $t1 - 7 ).$moo.substr( $res, $t3 + 9 );
		}
	}

	// ���������� ������ � ������� ������
	// ���� �� ������
	$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/forum\.php\?thread\=(\d+)[a-z\=\&0-9\;]*/i", "(�����:$2)", $res ); // @��������
	// ��������� ��������
	$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/help\.php\?id\=1010(\&amp\;|&)item_id\=(\d+)/i", "(����:$3)", $res ); // @ &=..
	// ��������� � ������
	//$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/help\.php\?id\=(\d+)/i", "(������:$2)", $res );
	$res = preg_replace( "|http\:\/\/www\.alideria\.ru/help\.php\?id=(?!\d+&)(\d+)|i", '(������:$1)', $res );
	$res = preg_replace( "|http\:\/\/alideria\.ru/help\.php\?id=(?!\d+&)(\d+)|i", '(������:$1)', $res );
	$res = preg_replace( "|www\.alideria\.ru/help\.php\?id=(?!\d+&)(\d+)|i", '(������:$1)', $res );		
	// ��� ���
	$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/combat_log\.php\?id\=(\d+)/i", "(���:$2)", $res );
	// ��������� �����
	$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/tournament_net\.php\?id\=(\d+)/i", "(������:$2)", $res );
	// ��������� ������
	$res = preg_replace( "/(http\:\/\/www\.|http\:\/\/|www\.)alideria\.ru\/orderpage\.php\?id\=(\d+)/i", "(�����:$2)", $res ); // @��������
	
	

	// ���������� ������ � ������� �����
	if( preg_match_all( "/(\([�-�]+:\d+\))/", $res, $loclinks ) )
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
				case '������':
				{
					$r = f_MQuery( "SELECT title FROM help_topics WHERE topic_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id='.$ll[1].'" title="������ � ������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['title'].'</a>', $res );
					break;
				}
				case '�����':
					$r = f_MQuery( "SELECT title,room_id, posts FROM forum_threads WHERE thread_id = {$ll[1]} AND important != -1" );
					if( $arr = f_MFetch( $r ) )
					{
						require_once( 'player.php' );
						$Player = new Player( $_COOKIE['c_id'] );
						$PlayerRank = $Player->Rank( );
						
						// ������ �� �� ������� ����� ������� ������ ������ � ����� �������, ��������� ��
						if( $arr['room_id'] < 0 && ( $PlayerRank != 1 && $Player->clan_id != $arr['room_id'] * -1 ) )
						{
							break;
						}
						// ������ �� �� ������� ����� ������� ������ ������ � ������
						elseif( $arr['room_id'] == 20 and( $PlayerRank != 1 and $PlayerRank != 2 and $PlayerRank != 5 ) )
						{
							$Player->syst2( '2' );
							break;
						}
						// ���� ����� � �������� � �������������
						elseif( ( $arr['room_id'] == 21 or $arr['room_id'] == 22 ) and $PlayerRank != 1 )
						{
							break;
						}
						
						$res = str_replace( $loclinks[0][$i], '<a href="/forum.php?thread='.$ll[1].'&page='.(int)(($arr['posts']-1)/20).'" title="���� �� ������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['title'].'</a>', $res );
					}
					break;
				case '�����':
				{
					$r = f_MQuery( "SELECT name FROM clans WHERE clan_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/orderpage.php?id='.$ll[1].'" title="�������� ������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case '������':
				{
					$r = f_MQuery( "SELECT name FROM tournament_announcements WHERE tournament_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/tournament_net.php?id='.$ll[1].'" title="��������� �����" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case "���":
				{
					if( $ll[1] > 0 )
						$res = str_replace( $loclinks[0][$i], '<a href="/combat_log.php?id='.$ll[1].'" title="��� ���" target="_blank" moo="'.$loclinks[0][$i].'">��� #'.$ll[1].'</a>', $res );
					break;
				}	
				case '����':
				{
					$r = f_MQuery( "SELECT name FROM items WHERE item_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1010&item_id='.$ll[1].'" title="���� � ������������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case '���':
				{
					$r = f_MQuery( "SELECT name FROM mobs WHERE mob_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1016&beast_id='.$ll[1].'" title="��� � ���������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}				
				case '����������':
				{
					$r = f_MQuery( "SELECT name FROM cards WHERE card_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1011&spell_id='.$ll[1].'" title="���������� � ������������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}				
				case '������':
				{
					$r = f_MQuery( "SELECT name FROM recipes WHERE recipe_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1015&recipe_id='.$ll[1].'" title="������ � ������������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case '�����':
				{
					if( isset( $_GET['where'] ) == false ) // ���� �� � ���
					{
						$r = f_MQuery( 'SELECT name,image,image_large FROM items WHERE item_id = '.$ll[1] );
						if( $arr = f_MFetch( $r ) )
						{
							$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1010&item_id='.$ll[1].'" target="_blank" title="'.$arr[name].'" moo="'.$loclinks[0][$i].'"><img src="/images/items/'.( ( $arr[image_large] == '' ) ? $arr[image] : $arr[image_large] ).'" style="width: 50px; height: 50px; border: 0px;" alt="(�����:'.$ll[1].')" /></a>', $res );					
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
	
	if( !isset( $_GET['where'] ) ) //-- ��� ���� �� ���
	{
		$res = str_replace( "(�)", '<b>', $res );
		$res = str_replace( "(!�)", '</b>', $res );
		$res = str_replace( "(�)", '<i>', $res );
		$res = str_replace( "(!�)", '</i>', $res );
		$res = str_replace( "(�)", '<u>', $res );
		$res = str_replace( "(!�)", '</u>', $res );
		$res = str_replace( "(�)", '<s>', $res );
		$res = str_replace( "(!�)", '</s>', $res );
		$res = str_replace( "\n", '<br>', $res );
		$res = str_replace( '(�������)', '<font color=darkred>', $res );
		$res = str_replace( '(�����)', '<font color=darkblue>', $res );
		$res = str_replace( '(�������)', '<font color=darkgreen>', $res );
		$res = str_replace( '(!����)', '</font>', $res );
		$res = str_replace( '(�����)', '<center>', $res );
		$res = str_replace( '(!�����)', '</center>', $res );
		$res = str_replace( '(�������)', '<span style="color: #cc9966; background: #cc9966;" onmouseover=this.style.color=\"white\" onmouseout=this.style.color=\"#cc9966\">', $res );
		$res = str_replace( '(!�������)', '</span>', $res );
		$res = str_replace( '(�������)', '<img src="/images/presents/8m1.gif" alt="(�������)" style="width: 75px; height: 75px;" />', $res );		
			
		$p = 0;
		while( 1 )
		{
			$p = $t1 = strpos( $res, "(���)", $p );
			if( $t1 === false )
				break;
			$p = $t2 = strpos( $res, "(!���)", $p );
			if( $t2 === false )
				break;
			
			$t1 += 5;
			$nick = substr( $res, $t1, $t2 - $t1 );
			
			$nick = htmlspecialchars( $nick );
			$mres = f_MQuery( "SELECT player_id FROM characters WHERE login = '$nick'" );
			$marr = f_MFetch( $mres );
			if( !$marr ) $res = str_replace( '(���)'.$nick.'(!���)', "<b>[�� ������������ �����]</b>", $res );
			else
			{
				$plr = new Player( $marr[0] );
				$res = str_replace( '(���)'.$nick.'(!���)', '<script>document.write( '.$plr->Nick( ).' )</script>', $res );
			}
		}
		while( 1 )
		{
			$p = $t1 = strpos( $res, "(�����)", $p );
			if( $t1 === false )
				break;
			$p = $t2 = strpos( $res, "(������)", $p );
			if( $t2 === false )
				break;
			$p = $t3 = strpos( $res, "(!������)", $p );
			if( $t3 === false )
				break;

				
			$t1 += 7;
			$nick = substr( $res, $t1, $t2 - $t1 );
			$t2 += 8;
			$text = substr( $res, $t2, $t3 - $t2 );
			
			$moo = "<div><script>FLUl();</script><b>$nick</b> �����(�):<script>FUlt();</script>$text<script>FL();FLL();</script></div>";
			$res = substr( $res, 0, $t1 - 7 ).$moo.substr( $res, $t3 + 9 );
		}
	}

	//���������� ������
	if( preg_match_all( "/(\([�-�]+:\d+\))/", $res, $loclinks ) )
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
				case '������':
				{
					$r = f_MQuery( "SELECT title FROM help_topics WHERE topic_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id='.$ll[1].'" title="������ � ������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['title'].'</a>', $res );
					break;
				}
				case '�����':
					$r = f_MQuery( "SELECT title,room_id FROM forum_threads WHERE thread_id = {$ll[1]} AND important != -1" );
					if( $arr = f_MFetch( $r ) )
					{
						require_once( 'player.php' );
						$Player = new Player( $_COOKIE['c_id'] );
						$PlayerRank = $Player->Rank( );
						
						// ������ �� �� ������� ����� ������� ������ ������ � ����� �������, ��������� ��
						if( $arr['room_id'] < 0 && ( $PlayerRank != 1 && $Player->clan_id != $arr['room_id'] * -1 ) )
						{
							break;
						}
						// ������ �� �� ������� ����� ������� ������ ������ � ������
						elseif( $arr['room_id'] == 20 and( $PlayerRank != 1 and $PlayerRank != 2 and $PlayerRank != 5 ) )
						{
							$Player->syst2( '2' );
							break;
						}
						// ���� ����� � �������� � �������������
						elseif( ( $arr['room_id'] == 21 or $arr['room_id'] == 22 ) and $PlayerRank != 1 )
						{
							break;
						}
						
						$res = str_replace( $loclinks[0][$i], '<a href="/forum.php?thread='.$ll[1].'" title="���� �� ������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['title'].'</a>', $res );
					}
					break;
				case '�����':
				{
					$r = f_MQuery( "SELECT name FROM clans WHERE clan_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/orderpage.php?id='.$ll[1].'" title="�������� ������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case '������':
				{
					$r = f_MQuery( "SELECT name FROM tournament_announcements WHERE tournament_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/tournament_net.php?id='.$ll[1].'" title="��������� �����" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case "���":
				{
					if( $ll[1] > 0 )
						$res = str_replace( $loclinks[0][$i], '<a href="/combat_log.php?id='.$ll[1].'" title="��� ���" target="_blank" moo="'.$loclinks[0][$i].'">��� #'.$ll[1].'</a>', $res );
					break;
				}	
				case '����':
				{
					$r = f_MQuery( "SELECT name FROM items WHERE item_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1010&item_id='.$ll[1].'" title="���� � ������������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case '���':
				{
					$r = f_MQuery( "SELECT name FROM mobs WHERE mob_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1016&beast_id='.$ll[1].'" title="��� � ���������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}				
				case '����������':
				{
					$r = f_MQuery( "SELECT name FROM cards WHERE card_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1011&spell_id='.$ll[1].'" title="���������� � ������������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}				
				case '������':
				{
					$r = f_MQuery( "SELECT name FROM recipes WHERE recipe_id = {$ll[1]}" );
					if( $arr = f_MFetch( $r ) )
						$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1015&recipe_id='.$ll[1].'" title="������ � ������������" target="_blank" moo="'.$loclinks[0][$i].'">'.$arr['name'].'</a>', $res );
					break;
				}
				case '�����':
				{
					if( isset( $_GET['where'] ) == false ) // ���� �� � ���
					{
						$r = f_MQuery( 'SELECT name,image,image_large FROM items WHERE item_id = '.$ll[1] );
						if( $arr = f_MFetch( $r ) )
						{
							$res = str_replace( $loclinks[0][$i], '<a href="/help.php?id=1010&item_id='.$ll[1].'" target="_blank" title="'.$arr[name].'" moo="'.$loclinks[0][$i].'"><img src="/images/items/'.( ( $arr[image_large] == '' ) ? $arr[image] : $arr[image_large] ).'" style="width: 50px; height: 50px; border: 0px;" alt="(�����:'.$ll[1].')" /></a>', $res );					
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
		$p = $t2 = strpos( $res, "</b> �����(�):<script>FUlt();</script>", $p );
		if( $t2 === false )
			break;
		$p = $t3 = strpos( $res, "<script>FL();FLL();</script></div>", $p );
		if( $t3 === false )
			break;

		$a = strlen( "<div><script>FLUl();</script><b>" );
		$b = strlen( "</b> �����(�):<script>FUlt();</script>" );
		$c = strlen( "<script>FL();FLL();</script></div>" );

			
		$t1 += $a;
		$nick = substr( $res, $t1, $t2 - $t1 );
		$t2 += $b;
		$text = substr( $res, $t2, $t3 - $t2 );
		
		$moo = "(�����)$nick(������)$text(!������)";
		$res = substr( $res, 0, $t1 - $a ).$moo.substr( $res, $t3 + $c );
	}

	$res = str_replace( '<b>', '(�)', $res );
	$res = str_replace( '</b>', '(!�)', $res );
	$res = str_replace( '<i>', '(�)', $res );
	$res = str_replace( '</i>', '(!�)', $res );
	$res = str_replace( '<u>', '(�)', $res );
	$res = str_replace( '</u>', '(!�)', $res );
	$res = str_replace( '<s>', '(�)', $res );
	$res = str_replace( '</s>', '(!�)', $res );
	$res = str_replace( '<font color=red>', '(�������)', $res );
	$res = str_replace( '<font color=blue>', '(�����)', $res );
	$res = str_replace( '<font color=green>', '(�������)', $res );
	$res = str_replace( '<font color=darkred>', '(�������)', $res );
	$res = str_replace( '<font color=darkblue>', '(�����)', $res );
	$res = str_replace( '<font color=darkgreen>', '(�������)', $res );
	$res = str_replace( '</font>', '(!����)', $res );
	$res = str_replace( '<center>', '(�����)', $res );
	$res = str_replace( '</center>', '(!�����)', $res );
	$res = str_replace( '<span style="color: #cc9966; background: #cc9966;" onmouseover="this.style.color=\'white\'" onmouseout="this.style.color=\'#cc9966\'">', '(�������)', $res );
	$res = str_replace( '</span>', '(!�������)', $res );
	$res = str_replace( '<img src="/images/presents/8m1.gif" alt="(�������)" style="width: 75px; height: 75px;" />', '(�������)', $res );
	
	if( preg_match_all( "/<a(.*?) moo=\"(\([�-�]+:\d+\))\">(.*?)<\/a>/", $res, $loclinks ) )
	{
		$count = count( $loclinks );
		for( $i = 0; $i < $count; ++ $i )
		{
			preg_match( "(\([�-�]+:\d+\))", $loclinks[0][$i], $ll );
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
		
		$res = str_replace( substr( $res, $t1, $t4 - $t1 ), "(���)".$nick.'(!���)', $res );
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
		$p = $t2 = strpos( $res, "</b> �����(�):<script>FUlt();</script>", $p );
		if( $t2 === false )
			break;
		$p = $t3 = strpos( $res, "<script>FL();FLL();</script></div>", $p );
		if( $t3 === false )
			break;

		$a = strlen( "<div><script>FLUl();</script><b>" );
		$b = strlen( "</b> �����(�):<script>FUlt();</script>" );
		$c = strlen( "<script>FL();FLL();</script></div>" );

			
		$t1 += $a;
		$nick = substr( $res, $t1, $t2 - $t1 );
		$t2 += $b;
		$text = substr( $res, $t2, $t3 - $t2 );
		
		$moo = "(�����)$nick(������)$text(!������)";
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
	$res = str_replace( '<img src="/images/presents/8m1.gif" alt="(�������)" style="width: 75px; height: 75px;" />', '', $res );
	
	foreach( $smiles as $a )
		$res = str_replace( "<img src='/images/smiles/$a.gif' alt='*$a*' />", "", $res );
	for( $i = 0; $i < 4; ++ $i )
		foreach( $vsmiles[$i] as $a )
			$res = str_replace( "<img src='/images/smiles/$a.gif' alt='*$a*' />", "*$a*", $res );
 	
	if( preg_match_all( "/<a(.*?) moo=\"(\([�-�]+:\d+\))\">(.*?)<\/a>/", $res, $loclinks ) )
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
