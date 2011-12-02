<?
	include( 'functions.php' );

	f_MConnect( );
	if( !check_cookie( ) )
		die( 'Неверные настройки Cookie' );
	if( isset( $_POST['room_id'] ) )
	{
		$room_id = (int)$_POST['room_id'];
		$do = (int)$_POST['do'];
		switch( $do )
		{
			case 1:
				$msg = '/invite '.$_POST['username'];
				break;
			case 2:
				$msg = '/kick '.$_POST['username'];
				break;
			case 3:
				$msg = '/protect '.$room_id;
				break;
			case 4:
				$msg = '/delete';
				break;
			default:
				$error = 'Указан неверный режим работы';
		}
		if( $msg )
		{
			if( $do != 3 ) echo '<script>window.opener.parent.chat_ref.location.href="chat_say.php?msg='.$msg.'&where=@'.$room_id.'";</script>';
			else echo '<script>window.opener.parent.chat_ref.location.href="chat_say.php?msg='.$msg.'&where=Общий";</script>';
		}
	}
?>
<html>
<head>
	<title>Управление комнатами</title>
	<meta http-equiv=Content-Type content="text/html; charset=windows-1251" />
	<link href="style2.css" rel="stylesheet" type="text/css" />
	<script src="/functions.js"></script>
</head>
<body style="padding:5px 10px 0px;">
	<br>
	<b>Управление комнатами</b><br>
	<br>
	<form method="POST">
		<table style="border:0;">
			<tr>
				<td>Комната номер</td>
				<td>
					<input class="m_btn" type="text" style="width:100px;" name="room_id" /> <small>без @</small>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					<select class="m_btn" name="do" size="1" style="width:100px;">
						<option value="1" onclick="_( 'username' ).style.display = '';">Пригласить</option>
						<option value="2" onclick="_( 'username' ).style.display = '';">Исключить</option>
						<option value="3" onclick="_( 'username' ).style.display = 'none';">Создать</option>
						<option value="4" onclick="_( 'username' ).style.display = 'none';">Удалить</option>
					</select>
				</td>
				<td>
				<span id="username">
					<input class="m_btn" type="text" name="username" style="width:100px;" />
				</span>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr style="text-align:center;">
				<td colspan="2">
					<br>
					<input class="m_btn" type="submit" value="Выполнить" />
					<?
						if( isset( $error ) )
							echo '<br><br><b>'.$error.'</b>';
					?>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		</table>
		<div id="hint">
			<br>
			<b>Подсказка:</b>
			<br><br>
			Укажите номер комнаты, которой вы собираетесь управлять. Управлять можно только приватными комнатами (пример: &#64;31).
			<br><br>
			Во втором поле укажите имена игроков, которым вы предоставляете доступ в комнату. Имена разделяйте пробелами. Пример: "Ishamael Пламени Май".
		</div>
	</form>
</body>
</html>