<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $_POST['nm'] ) )
{
	$pid = f_MValue( "SELECT player_id FROM characters WHERE login='$_POST[nm]'" );
	if( !$pid ) echo "<font color=darkred>Нет такого игрока</font><br><br>";
	else
	{
	   $p_duration = (int)$_POST['duration'];
		$till = $p_duration == 0 ? 2147483647 : (time( )  + $p_duration  * 3600 * 24);
		$img = $_POST['img'];
		if( !file_exists( "../images/presents/$img" ) )
			echo "<font color=darkred>Нет такой картинки</font><br><br>";
		else if($p_duration < 1 && $p_duration != 0 )
			echo "<font color=darkred>Срок не может быть меньше суток</font><br><br>";
		else
		{
			$txt = htmlspecialchars( $_POST['moo'], ENT_QUOTES );
			$sender = htmlspecialchars( $_POST['sender'], ENT_QUOTES );
			f_MQuery( "INSERT INTO player_presents ( player_id, img, txt, author, deadline ) VALUES ( $pid, '$img', '$txt', '$sender', '$till' )" );
		}	
	}
}

?>

<a href=index.php>На главную</a><br>
<form action=admin_present_present.php method=post>
Ник игрока: <input type=text name=nm class=m_btn value=<?=$_POST['nm']?>><br>
Картинка: <input type=text name=img class=m_btn value=<?=$_POST['img']?>><br>
Срок (в днях): <input type=text name=duration class=m_btn value=<?=$_POST['duration']?>><br>
Имя отправителя: <input type=text name=sender class=m_btn value=<?=$_POST['sender']?>><br>
<small><i>имя отправителя не обязательно должно быть именем игрока. это может быть любой текст, подходящий под фразу "Подарок от ..."</i></small><br>
Текст:<br><textarea rows=10 cols=40 name=moo><?=$_POST['moo']?></textarea><br>
<input class=s_btn value=Отправить type=submit>
</form>
