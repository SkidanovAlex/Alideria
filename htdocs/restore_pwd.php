<?

//ini_set("display_errors", 1);

include( 'functions.php' );

f_MConnect( );

$ok = 0;
f_register_globals( );

function reg_err( $a )
{
	global $msg;
	global $ok;
	
	$msg = $a;
	$ok = 0;
}

if( isset( $g_pid ) )
{
	settype( $g_pid, 'integer' );
	$res = f_MQuery( "SELECT restore_pwd FROM characters WHERE player_id='$g_pid'" );
	$arr = f_MFetch( $res );
	if( !$arr ) RaiseError( "Попытка восстановить пароль несуществующего персонажа", "$g_pid" );
	if( $arr[0] === $g_activate && strlen( $g_activate ) == strlen( 'd14d52a2ea13c4ea3bc075a47a785d51' ) )
		f_MQuery( "UPDATE characters SET pswrddmd5=restore_pwd WHERE player_id='$g_pid'" );
	else RaiseError( "Неверный активационный ключ при восстановлении пароля", "$g_activate, $g_pid" );
	$ok = 2;
}

if( isset( $p_login ) )
{
	$ok = 1;

	if( !$p_login )
		reg_err( "Не указан логин." );
		
	else
	{
		$login = HtmlSpecialChars( $p_login, ENT_QUOTES );
		$res = f_MQuery( "SELECT email, player_id FROM characters WHERE login='$login'" );
		$arr = f_MFetch( $res );
		if( $arr && strtolower( $arr[0] ) === strtolower( $p_email ) )
		{
			$new_pwd = '';
			for( $i = 0; $i < 7; ++ $i )
			{
				$moo = chr ( ord( 'a' ) + mt_rand( 0, 25 ) );
				$new_pwd .= $moo;
			}
			for( $i = 0; $i < 4; ++ $i )
			{
				$moo = chr ( ord( 'A' ) + mt_rand( 0, 25 ) );
				$new_pwd[mt_rand( 0, 5 )] = $moo;
			}
			for( $i = 0; $i < 2; ++ $i )
			{
				$moo = chr ( ord( '0' ) + mt_rand( 0, 9 ) );
				$new_pwd[mt_rand( 0, 5 )] = $moo;
			}
			if( mail( $p_email, "Алидерия - восстановление пароля", "Вы воспользовались автоматической системой восстановления пароля.\nВаш новый пароль: $new_pwd\nЧтобы пароль вступил в силу, перейдите по следующей ссылке: www.alideria.ru/restore_pwd.php?pid=$arr[1]&activate=".md5( $new_pwd ) , "From: Alideria.ru") )
				f_MQuery( "UPDATE characters SET restore_pwd=md5('$new_pwd') WHERE player_id=$arr[1]" );
			else reg_err( 'Не получилось отправить e-mail.' );
		}
		else if( !$arr ) reg_err( 'Нет такого игрока.' );
		else reg_err( 'E-mail не совпадает с указанным при регистрации.' );

		f_MClose( );
	}
}

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">

<?

print( "<table width=100% height=100%><tr><td align=center valign=center>" );

	print( "<table>" );
	if( !$ok )
	{
		print( "<form acton='restore_pwd.php' method=post>" );
		
			print( "<tr><td width=240>&nbsp;</td><td><b>Восстановление Пароля</b></td></tr>" );
			
			if( isset( $msg ) )
			{
				print( "<tr><td>&nbsp;</td><td width=180><b><font color=red><b><small>$msg</small></b></font></td></tr>" );
			}
		
			$p_login = HtmlSpecialChars( $p_login );
			$p_email = HtmlSpecialChars( $p_email );
			print( "<tr><td align=right>Логин:&nbsp;</td><td><input type=text name=login class=m_btn maxlength=16 value=\"$p_login\"></td></tr>" );
			print( "<tr><td align=right>e-mail:&nbsp;</td><td><input type=text name=email class=m_btn maxlength=50 value=\"$p_email\"></td></tr>" );
			print( "<tr><td>&nbsp;</td><td><input type=submit class=m_btn value=Восстановить></td></tr>" );
			print( "<tr><td>&nbsp;</td><td width=280><br><br>");
			
			print( "</td></tr>" );
	
		print( "</form>" );
	}
	else if( $ok == 2 )
	{
		print( "<tr><td align=center><b>Пароль успешно изменен!</b><br></td></tr>" );
	}
	else
	{
		print( "<tr><td align=center><b>Пароль выслан вам на почту.</b><br></td></tr>" );
	}
	print( "</table>" );

print( "</td></tr></table>" );

?>
