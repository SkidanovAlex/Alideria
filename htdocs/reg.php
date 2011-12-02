<?



include( 'functions.php' );

f_MConnect( );

$ok = 0;
f_register_globals( );

if( isset( $g_ref ) && !isset( $p_ref ) )
{
	$s1 = iconv("UTF-8", "CP1251", $g_ref );
	if( $s1 == "" ) $s1 = $g_ref;
	$p_ref = $s1;
}

$ip = getenv( "HTTP_X_REAL_IP");
$ipx = getenv( "HTTP_X_FORWARDED_FOR" );
if( !$ipx ) $ipx = $ip;

if( strpos( $ip, '94.137.' ) !== false ) die( );
if( strpos( $ipx, '94.137.' ) !== false ) die( );

$can_reg = true;
$tm = time( ) - 14400;
f_MQuery( "DELETE FROM regs WHERE time < $tm" );
$res = f_MQuery( "SELECT max( time ) FROM regs WHERE ip='$ip' OR ip='$ipx'" );
$arr = f_MFetch( $res );
{
	if( $arr[0] )
	{
		$can_reg = false;
		$delay_str = "Следующая регистрация с этого айпи возможна только через<br>".my_time_str( $arr[0] + 14400 - time( ) );
	}
}
function reg_err( $a )
{
	global $msg;
	global $ok;
	
	$msg = $a;
	$ok = 0;
}

function reg_login_err( )
{
	reg_err( "Логин должен состоять только из русских или только из латинских букв, а так же цифр, знаков подчеркивания и тире." );
}

function correct_login( $a )
{	
	$res = f_MQuery("SELECT spam_name FROM spams");
	$i=0;
	while ($arr = f_MFetch($res)) {$badWords[$i] = $arr[0]; $i++;}
	$count = count( $badWords );
	for( $i = 0; $i < $count; ++ $i )
	{
		if( preg_match( $badWords[$i], $a ) > 0 )
		{
			reg_err( "Нельзя зарегистрировать игрока с таким именем." );
			return 0;
		}
	}
	if( ( $a[0] >= 'a' && $a[0] <= 'z' ) || ( $a[0] >= 'A' && $a[0] <= 'Z' ) )
		$eng = 1;
	else if( ( $a[0] >= 'а' && $a[0] <= 'я' ) || ( $a[0] >= 'А' && $a[0] <= 'Я' ) )
		$eng = 0;
	else
	{
		reg_err( "Первый символ логина должен быть буквой." );
		return 0;
	}
	
	$l = strlen( $a );
	for( $i = 1; $i < $l; ++ $i )
	{
		if( ( $a[$i] >= 'a' && $a[$i] <= 'z' ) || ( $a[$i] >= 'A' && $a[$i] <= 'Z' ) )
		{
			if( !$eng )
			{
				reg_login_err( );
				return 0;
			}
		}
		else if( ( $a[$i] >= 'а' && $a[$i] <= 'я' ) || ( $a[$i] >= 'А' && $a[$i] <= 'Я' ) )
		{
			if( $eng )
			{
				reg_login_err( );
				return 0;
			}
		}
		else if( $a[$i] != '-' && $a[$i] != '_' && ( $a[$i] < '0' || $a[$i] > '9' ) )
		{
			reg_login_err( );
			return 0;
		}
	}
	
	return 1;
}

if( $can_reg && isset( $p_login ) )
{
	$ok = 1;

	settype( $p_sex, 'integer' );
	
	if( !$p_login )
		reg_err( "Не указан логин." );
	else if( strlen( $p_login ) > 16 )
		reg_err( "Логин не может быть больше 16 символов." );
	else if( !correct_login( $p_login ) ) ;
	else if( !$p_pwd )
		reg_err( "Не указан пароль." );
	else if( strlen( $p_pwd ) < 4 )
		reg_err( "Пароль не может быть меньше 4 символов." );
	else if( strlen( $p_pwd ) > 16 )
		reg_err( "Пароль не может быть больше 16 символов." );
	else if( $p_pwd !== $p_pwd_again )
		reg_err( "Пароли не совпадают." );
	else if( strlen( $p_email ) > 50 )
		reg_err( "e-mail не может быть больше 50 символов." );
	else if( $p_sex != 0 && $p_sex != 1 )
		reg_err( "Какое-то шаманство у вас с полом игрока :о)" );
		
	else
	{
		$ref_id = -1;

	

		$res = f_MQuery( "SELECT login FROM characters WHERE login='$p_login'" );
		if( strlen( $p_ref ) > 1 )
		{
			$res2 = f_MQuery( "SELECT player_id FROM characters WHERE login='$p_ref'" );
			$arr2 = f_MFetch( $res2 );
			if( !$arr2 ) $ref_id = -2;
			else $ref_id = $arr2[0];
		}
		if( f_MNum( $res ) > 0 )
			reg_err( "Персонаж с таким логином уже зарегистрирован!" );
	   	else if( $ref_id == -2 )
			reg_err( "В игре нет игрока <b>$p_ref</b>." );
		else                                    
		{
			$md = md5( $p_pwd );
			$p_email = AddSlashes( $p_email );		
			if( !f_MQuery( "INSERT INTO characters ( login, pswrddmd5, email, loc, depth, text_clr, nick_clr, sex, wear_level, regdate ) VALUES ( '$p_login', '$md', '$p_email', 2, 0, '000000', '000000', $p_sex, 1, ".time()." )" ) )
				reg_err( "Внутренняя ошибка сервера." );
				
			$q = mysql_insert_id( );
				
			if( !f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value ) VALUES ( $q, 1000, 3, 3 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value ) VALUES ( $q, 1001, 3, 3 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value ) VALUES ( $q, 1, 101, 101 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value ) VALUES ( $q, 101, 101, 101 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( $q, 56, 10 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( $q, 57, 10 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( $q, 58, 10 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO player_selected_cards ( player_id, card_id ) VALUES ( $q, 56 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO player_selected_cards ( player_id, card_id ) VALUES ( $q, 57 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO player_selected_cards ( player_id, card_id ) VALUES ( $q, 58 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO player_items ( player_id, item_id, number, weared ) VALUES ( $q, 154, 1, 13 )" ) )
				reg_err( "Внутренняя ошибка сервера." );
			if( !f_MQuery( "INSERT INTO noob VALUES( $q, 1, 0 )" ) )
//			if( !f_MQuery( "INSERT INTO player_noobs VALUES( $q, 0 )" ) )
				reg_err( "Внутренняя ошибка сервера." );

			$tm = time( );
			f_MQuery( "UPDATE statistics SET regs=regs+1" );
			f_MQuery( "INSERT INTO regs VALUES( $tm, '$ip' )" );
			if( $ip != $ipx ) f_MQuery( "INSERT INTO regs VALUES( $tm, '$ipx' )" );

			if( $ref_id != -1 ) f_MQuery( "INSERT INTO player_invitations ( player_id, ref_id ) VALUES ( $q, $ref_id )" );

		}

		f_MClose( );
	}
}

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">

<?

print( "<table width=100% height=100%><tr><td align=center valign=center>" );

if( !$can_reg )
{
	echo "$delay_str</td></tr></table>";
	die( );
}

	print( "<table>" );
	if( !$ok )
	{
		print( "<form acton='reg.php' method=post>" );
		
			print( "<tr><td width=240>&nbsp;</td><td><b>Регистрация</b></td></tr>" );
			
			if( isset( $msg ) )
			{
				print( "<tr><td>&nbsp;</td><td width=180><b><font color=red><b><small>$msg</small></b></font></td></tr>" );
			}
		
			$p_login = HtmlSpecialChars( $p_login );
			$p_email = HtmlSpecialChars( $p_email );
			print( "<tr><td align=right>Логин:&nbsp;</td><td><input type=text name=login class=m_btn maxlength=16 value=\"$p_login\"></td></tr>" );
			print( "<tr><td align=right>Пароль:&nbsp;</td><td><input type=password name=pwd class=m_btn maxlength=16></td></tr>" );
			print( "<tr><td align=right>Пароль&nbsp;еще&nbsp;раз:&nbsp;</td><td><input type=password name=pwd_again class=m_btn maxlength=16></td></tr>" );
			print( "<tr><td align=right>Пол:&nbsp;</td><td><select class=m_btn name=sex><option value=0>Мужской<option value=1>Женский</style></td></tr>" );
			print( "<tr><td align=right>e-mail:&nbsp;</td><td><input type=text name=email class=m_btn maxlength=50 value=\"$p_email\"></td></tr>" );
			print( "<tr><td align=right>Имя пригласившего:&nbsp;</td><td><input type=text name=ref class=m_btn maxlength=50 value=\"$p_ref\"></td></tr>" );
			print( "<tr><td>&nbsp;</td><td><input type=submit class=m_btn value=Зарегистрироваться><br><br><small>Регистрируясь в игре, вы принимаете условия <a href=help.php?id=2 target=_blank>Пользовательского Соглашения</a>.</small></td></tr>" );
			print( "<tr><td>&nbsp;</td><td width=280><br><br>");
			
			print( "</td></tr>" );
	
		print( "</form>" );
	}
	else
	{
		print( "<tr><td align=center><b>Персонаж $p_login успешно зарегистрирован!</b><br></td></tr>" );
	}
	print( "</table>" );

print( "</td></tr></table>" );

?>
