<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "�������� ��������� Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->location != 2 || $player->depth != 10 ) RaiseError( "������� ��������� ������ ������ �� �� �����", "Loc: {$player->location}; Depth: {$player->depth};" );

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
    	$msg = $msg;
    else $msg = $msg2;
    return $msg;
}

$target = conv( $target );
$title = conv( $title );
$text = conv( $text );

$title = mysql_real_escape_string(substr( $title, 0, 255 ));
$text = mysql_real_escape_string(substr( $text, 0, 1024 ));
settype( $money, 'integer' );
settype( $np1, 'integer' );
settype( $np2, 'integer' );

function err( $a )
{
	echo "alert( '$a' );";
	echo "document.getElementById( 'send_btn' ).disabled = false;";
	echo "document.getElementById( 'send_btn' ).innerHTML = '���������';";
}

if( $target == '' || $title == '' || $text == '' )
{
	err( '�� ��� ���� ���������' );
}
else if( $money < 0 || $np1 < 0 )
{
	err( '������������� ����� �����������' );
}
else if( $np1 > 0 && $np2 <= 0 )
{
	err( '���� �� ���������� �������� ����������� �������, ������� ������� � ���������� ���� �� �������� �����' );
}
else
{
	$price = 10;
	if( $money > 0 ) $price += 10;
	if( $np1 > 0 ) $price += 10;
	
	if( strpos( $target, ',' ) != false && 
		( isset( $_GET['att0'] ) == true || $np1 > 0 || $money > 0 )
		)
	{
		err( '������ ���������� �������� �������� �� ����������' );
	}
	else
	{
		$target = explode( ',', $target );
		$count = count( $target );
		
		for( $i = 0; $i < $count; ++ $i )
		{
			$res = f_MQuery( "SELECT player_id FROM characters WHERE login='".trim($target[$i])."'" );
			$arr = f_MFetch( $res );
			if( !$arr )
			{
				err( "������ � ������ {$target[$i]} �� ����������" );
			}
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
				if( !$ok )
					err( "�� ��� ����, ������� �� ��������� ��������� � ������, ���� � ��� �� �����" );
				elseif( $id == 0 && $np1 > 0 )
					err( "������ ��������� ���������� ������ �� ������, � �������� ������ �� ���������" );
				else
				{
					if( !$player->SpendMoney( $price + $money ) )
						err( "� ��� ������������ �������� (���������� ".( $price + $money ).")" );
					else
					{
						$player->AddToLogPost( 0, - ($price +  $money), 19, 0 );

						if( $np1 > 0 )
							$deadline = time( ) + $np2 * 60 * 60 * 24;
						else
							$deadline = 0;
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
								f_MQuery( "INSERT INTO post_items( entry_id, item_id, number ) VALUES( $entry_id, $item_id, $number )" );
								$player->AddToLogPost( $item_id, - $number, 19, 0 );
							}
							else
								LogError( "��� �������� ������ �� {$player->login} ������ {$target[$i]} �� ����������� [$number] $item_id" );
						}
						if( $i == $count - 1 )
						{
							if( $count == 1 )
								echo( "document.getElementById( 'post_content' ).innerHTML = '<br><b>������ ��� ��������� <font color=blue>{$target[$i]}</font> ������� ����������</b><br><a href=game.php>�������� ��� ���� ������</a></b>';" );
							else
								echo ( "document.getElementById( 'post_content' ).innerHTML = '<br><b>������ ��� <font color=blue>���� ����������</font> ������� ����������</b><br><a href=game.php>�������� ��� ���� ������</a></b>';" );
							echo( "document.getElementById( 'post_act' ).innerHTML = '����������';" );
						}
						echo "update_money( $player->money, $player->umoney );";

						$plr = new Player( $target_id );
						$plr->syst2( '� ��� � �������� ����� ���������' );
					}
				}
			}
		}
	}
}

?>
