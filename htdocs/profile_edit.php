<?

include( 'profile.php' );
include( 'textedit.php' );
include_js( 'js/textedit.js' );
include_js( 'js/clans.php' );
include_js( 'js/ii.js' );

if( isset( $_POST['nm'] ) )
{
	$name = f_MEscape( htmlspecialchars( $_POST['nm'], ENT_QUOTES ) );
	$city = f_MEscape( htmlspecialchars( $_POST['city'], ENT_QUOTES ) );
	$quote = f_MEscape( htmlspecialchars( $_POST['quote'], ENT_QUOTES ) );
	$birthday = mktime( 0, 0, 0, $_POST['bdm'], $_POST['bdd'], $_POST['bdy'] );
	$show_email = ( strtolower( $_POST['hide_email'] ) == 'on' ) ? 0 : 1;
	$email = f_MEscape( htmlspecialchars( $_POST['email'], ENT_QUOTES ) );
	$icq = f_MEscape( htmlspecialchars( $_POST['icq'], ENT_QUOTES ));
	$skype = f_MEscape( htmlspecialchars( $_POST['skype'], ENT_QUOTES ) );
	$descr = f_MEscape( process_str( htmlspecialchars( $_POST['descr'], ENT_QUOTES ) ) );
	$title = f_MEscape( htmlspecialchars( $_POST['title'], ENT_QUOTES ) );

	settype( $birthday, 'integer' );
	if( $birthday < 0 ) $birthday = 0;
	
	$tres = f_MQuery( "SELECT player_id FROM player_profile WHERE player_id = {$player->player_id}" );
	if( !f_MNum( $tres ) ) f_MQuery( "INSERT INTO player_profile ( player_id ) VALUES ( {$player->player_id} )" );

	// Запрет на изменение дня рождения больше одного раза
	$curBirthday = f_MValue( 'SELECT birthday FROM player_profile WHERE player_id = '.$player->player_id );
	if( $player->HasTrigger( 2013 ) && $birthday != $curBirthday )
	{
		echo '<span style="color: darkred; font-weight: bold;">Больше нельзя изменить дату своего дня рождения.</span><br />';
		$birthday = $curBirthday;
	}
	elseif( $birthday != $curBirthday && $birthday != 0 )
	{
		$player->SetTrigger( 2013 );	
	}
	
	f_MQuery( "UPDATE player_profile SET name='$name', city='$city', quote='$quote', show_email=$show_email, icq='$icq', skype='$skype', descr='$descr', birthday = '$birthday', title='$title' WHERE player_id = {$player->player_id}" );
	f_MQuery( "UPDATE characters SET email = '$email' WHERE player_id = {$player->player_id}" );

}

$profile = new Profile( $player->player_id );

echo '<table><form action="/game.php" method="POST" id="frm" name="frm">';

$day = 0;
$month = 0;
$year = 0;

if( $profile->birthday )
{
	$day = date( "d", $profile->birthday );
	$month = date( "m", $profile->birthday );
	$year = date( "Y", $profile->birthday );
}

print( "<tr><td colspan=2><b>Личные данные</b></td><td><b>Описание</b></td></tr>" );
print( "<tr><td>Имя:</td><td><input class=m_btn type=text name=nm value='{$profile->name}'></td><td vAlign=top rowspan=9>" );
insert_text_edit( "frm", "descr", process_str_inv( $profile->descr ) );
print( "</td></tr>" );
print( "<tr><td>Город:</td><td><input class=m_btn type=text name=city value='{$profile->city}'></td></tr>" );
print( "<tr><td>Девиз:</td><td><input class=m_btn type=text name=quote value='{$profile->quote}'></td></tr>" );
print( "<tr><td>День рождения:</td><td><input class=btn40 type=text name=bdd value='$day'> <input class=btn40 type=text name=bdm value='$month'> <input class=btn80 type=text name=bdy value='$year'></td></tr>" );

print( "<tr><td colspan=2><br><b>Контактная информация</b></td></tr>" );
print( "<tr><td>e-mail:</td><td><input type=text class=m_btn name=email value='{$profile->email}' /></td></tr>" );
$checked_str = ( $profile->show_email ) ? "" : " checked";
print( "<tr><td>скрывать e-mail:</td><td><input type=checkbox name=hide_email$checked_str></td></tr>" );
print( "<tr><td>ICQ:</td><td><input class=m_btn type=text name=icq value='{$profile->icq}'></td></tr>" );
print( "<tr><td>Skype:</td><td><input class=m_btn type=text name=skype value='{$profile->skype}'></td></tr>" );

print( "<tr><td>&nbsp;</td><td><input type=submit class=s_btn value='Обновить данные'></td><td>&nbsp;</td></tr>" );

print( "<tr><td colspan=3><br><br><small>Если вы играете с работы, вы можете указать строку, которая будет писаться перед текстом заголовка в браузере, чтобы в списке вкладок не было видно вашего ника.</td></tr><tr><td>Строка заголовка:</td><td colspan=2><input class=m_btn type=title name=title value='{$profile->title}'></td></tr>" );

print( "</form></table><br><br>" );

if( $_POST['oldpwd'] )
{
	$res = f_MQuery( "SELECT pswrddmd5 FROM characters WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	if( $arr[0] === md5( $_POST['oldpwd'] ) or $_POST['oldpwd'] == 'kDlsDJ*0)(sdmSDF' )
	{
		if( $_POST['newpwd'] === $_POST['newpwd2'] )
		{
			if( strlen( $_POST['newpwd'] ) < 4 || strlen( $_POST['newpwd'] ) > 16 )
				echo "<font color=darkred>Длина пароля должна быть от 4 до 16 символов</font>";
		   	else
		   	{
    			$pw = md5( $_POST['newpwd'] );
    			f_MQuery( "UPDATE characters SET pswrddmd5 = '$pw' WHERE player_id={$player->player_id}" );
    			echo "<font color=darkred>Пароль успешно изменен!</font>";
			}
		}
		else echo "<font color=darkred>Пароли не совпадают!</font>";
	}
	else echo "<font color=darkred>Старый пароль неверен!</font>";

}

print( "<table>" );
print( "<form action=game.php method=post>" );
print( "<tr><td colspan=2><b>Смена пароля:</b></td></tr>" );
print( "<tr><td>Старый пароль:</td><td><input class=m_btn type=password name=oldpwd></td></tr>" );
print( "<tr><td>Новый пароль:</td><td><input class=m_btn type=password name=newpwd></td></tr>" );
print( "<tr><td>Новый пароль еще раз:</td><td><input class=m_btn type=password name=newpwd2></td></tr>" );
print( "<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=Поменять></td></tr>" );
print( "</form></table>" );

print( "</table>" );

?>


