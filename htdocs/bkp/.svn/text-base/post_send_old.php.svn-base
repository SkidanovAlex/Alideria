<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->location != 2 || $player->depth != 10 ) RaiseError( "Попытка отправить письмо будучи не на почте", "Loc: {$player->location}; Depth: {$player->depth};" );

$text = $HTTP_RAW_POST_DATA;
$title = trim( $_GET['title'] );
$target = trim( $_GET['target'] );
$money = $_GET['money'];
$np1 = $_GET['np1'];
$np2 = $_GET['np2'];

function conv( $msg )
{
    $msg2 = iconv("UTF-8", "CP1251", $msg );
    if( $msg2 === false || $msg != iconv("CP1251", "UTF-8", $msg2 ) )
    	$msg = HtmlSpecialChars( $msg );
    else $msg = HtmlSpecialChars( $msg2 );
    return $msg;
}

$target = conv( $target );
$title = conv( $title );
$text = conv( addslashes( $text ) );

$title = htmlspecialchars(substr( $title, 0, 255 ), ENT_QUOTES);
$text = htmlspecialchars(substr( $text, 0, 1024 ),ENT_QUOTES);
settype( $money, 'integer' );
settype( $np1, 'integer' );
settype( $np2, 'integer' );

function err( $a )
{
	echo "alert( '$a' );";
	echo "document.getElementById( 'send_btn' ).disabled = false;";
	echo "document.getElementById( 'send_btn' ).innerHTML = 'Отправить';";
}

if( $target == '' || $title == '' || $text == '' )
{
	err( 'Не все поля заполнены' );
}
else if( $money < 0 || $np1 < 0 )
{
	err( 'Отрицательные суммы недопустимы' );
}
else if( $np1 > 0 && $np2 <= 0 )
{
	err( 'Если вы указываете величину наложенного платежа, следует указать и количество дней до возврата вещей.' );
}
else
{
	$price = 10;
	if( $money > 0 ) $price += 10;
	if( $np1 > 0 ) $price += 10;
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$target'" );
	$arr = f_MFetch( $res );
	if( !$arr ) err( "Игрока с именем $target не существует" );
	else
	{
		$ok = true;
		$target_id = $arr[0];
		for( $id = 0; isset( $_GET["att$id"] ); ++ $id )
		{
			$item_id = $_GET["att$id"];
			$number = $_GET["num$id"];
			settype( $item_id, 'integer' );
			settype( $number, 'integer' );
			if( $player->NumberItems( $item_id ) < $number )
				$ok = false;
			if( $number <= 0 )
				$ok = false;
			$price += 10;
		}
		if( !$ok ) err( "Не все вещи, которые вы пытаетесь приложить к письму, есть у вас на руках!" );
		else if( $id == 0 && $np1 > 0 ) err( "Нельзя выставить наложенный платеж на письмо, к которому ничего не приложено!" );
		else
		{
			if( !$player->SpendMoney( $price + $money ) )
				err( "У вас недостаточно дублонов (необходимо ".($price+$money).")" );
			else
			{
				$player->AddToLogPost( 0, - ($price +  $money), 19, 0, $target_id );

				$to_log = '';

				if( $np1 > 0 ) $deadline = time( ) + $np2 * 60 * 60 * 24;
				else $deadline = 0;
				f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( $player->player_id, $target_id, '$title', '$text', '$money', '$np1', '$deadline' )" );
				$entry_id = mysql_insert_id( );
        		for( $id = 0; isset( $_GET["att$id"] ); ++ $id )
        		{
        			$item_id = $_GET["att$id"];
        			$number = $_GET["num$id"];
        			settype( $item_id, 'integer' );
        			settype( $number, 'integer' );
        			if( $player->DropItems( $item_id, $number ) )
        			{
        				$to_log .= f_MValue( "SELECT name FROM items WHERE item_id=$item_id" ) . ": $number<br>";
  						f_MQuery( "INSERT INTO post_items( entry_id, item_id, number ) VALUES( $entry_id, $item_id, $number )" );
						$player->AddToLogPost( $item_id, - $number, 19, 0, $target_id );
  					}
        		   	else LogError( "При отправке письма от {$player->login} игроку $target не приложилось [$number] $item_id" );
        		}
        		if( $money > 0 ) $to_log .= "Дублоны: $money<br>";
        		echo( "document.getElementById( 'post_content' ).innerHTML = '<br><b>Письмо для персонажа <font color=blue>$target</font> успешно отправлено!</b><br><a href=game.php>Написать еще одно письмо</a></b>';" );
        		echo( "document.getElementById( 'post_act' ).innerHTML = 'отправлено';" );
				echo "update_money( $player->money, $player->umoney );";

				if( $to_log != '' )
				{
					$to_log = addslashes($to_log);
					f_MQuery( "INSERT INTO history_post ( post_entry_id, player_id1, player_id2, val, time, type ) VALUES ( $entry_id, {$player->player_id}, $target_id, '$to_log', ".time().", 0 )" );
				}

				$plr = new Player( $target_id );
				$plr->syst2( 'Вам прислали письмо! Откройте свой Дневник, чтобы прочесть его.' );
			}
		}
	}
}

?>
