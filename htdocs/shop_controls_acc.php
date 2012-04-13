<?

include_once("functions.php");
include_once("player.php");
include_once("shop.php");

f_Mconnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$shop_id = $HTTP_GET_VARS['shop_id'];
settype( $shop_id, 'integer' );

if( !( $player->IsShopOwner( $shop_id ) ) )
	RaiseError( "Вы не являетесь владельцем этого магазина" );
if( $player->depth != 101 && $player->Rank( ) != 1 )
	RaiseError( "Попытка управлять магазином вне магазина." );

	
$shop = new Shop( $shop_id );
	
$shop_price = $shop->cost;

$st = "";
$err = "";
$log = "";

for( $i = 0; ; ++ $i )
{
	if( !isset( $HTTP_GET_VARS["cmd".$i] ) )
		break;
		
	$cmd = $HTTP_GET_VARS["cmd".$i];
	$a1 = $HTTP_GET_VARS["arg".$i];
	$a2 = $HTTP_GET_VARS["opt".$i];

	if( $cmd == 0 )
	{
		$shop_name = $a1;
		if(strlen($shop_name)<5)
		{
			$err .= "Название маназина не должно быть короче пяти символов<br>";
		}
		elseif(strlen($shop_name)>100)
		{
			$err .= "Название маназина не должно быть длиннее 100 символов<br>";
		}
		else
		{
			if(!$shop->SetName($shop_name))
				$err .= "Магазин с названием $shop_name здесь уже есть...<br>";
			else
				$log .= "Изменено название магазина: <b>$shop_name</b><br>";
		}	
	}
	
	else if( $cmd == 1 )
	{
		settype( $a1, 'integer' );
		if( $a1 >= 0 && $a1 <= 100 )
		{
			$shop->SetSellPriceMul($a1);
			$log .= "Изменен множитель на продажу: <b>$a1</b><br>";
		}
		else $error .= "Неверно задан множитель на цену продажи<br>";
	}

	else if( $cmd == 2 )
	{
		settype( $a1, 'integer' );
		if( $a1 >= 0 && $a1 <= 100 )
		{
			$shop->SetBuyPriceMul($a1);
			$log .= "Изменен множитель на покупку: <b>$a1</b><br>";
		}
		else $error .= "Неверно задан множитель на цену покупки<br>";
	}
	
	else if( $cmd == 3 )
	{
		if( $a1 == 0 || $a1 == 1 || $a1 == 2 || $a1 == 3 || $a1 == 4 )
		{
			$regime_names = array( 0 =>	"Продажа/Покупка",
				"Только Продажа",
				"Только Покупка",
				"Магазин Закрыт",
				"Сохран. Последнего" );
			$log .= "Изменен режим работы магазина: <b>{$regime_names[$a1]}</b><br>";
			$shop->SetRegime($a1);
		}
		else $error .= "Неверно задан режим работы магазина<br>";
	}
	
/*	else if( $cmd == 4 )
	{
		settype( $a1, 'integer' );
		if( $a1 > 0 )
		{
			$price = $a1 * $shop_price;
			if( $player->SpendMoney( $price ) )
			{
				f_MQuery("UPDATE shops SET capacity=capacity+$a1 WHERE shop_id=$shop_id");
			}
			else $error .= "У вас не хватает монет на расширение магазина<br>";
		}
		else $error .= "Неверно задано количество мест в магазине<br>";
	}
*/	
	// Операции с добавлением и снятием товара
	else if( $cmd == 21 )
	{
		settype( $a1, 'integer' );
		settype( $a2, 'integer' );
		if( $a2 < 0 ) $error .= "Не удалось снять указанное количество товара.<br>";
		else
		{
			$item_id = $a1;
			$get_goods = $a2;
			$val = $shop->DropItems( $item_id, $get_goods );
			
			if( $val == false )
				$error .= "Не удалось снять указанное количество товара.<br>";
			else
			{
				$player->AddItems( $item_id, $get_goods );
				$name = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$item_id" ) );
				$have = $shop->NumberItems( $item_id );
				$had = $have + $get_goods;
				$log .= "Снято [$get_goods] <b>$name[0]</b> (было $had, стало $have)<br>";
			}
		}
	}
	else if( $cmd == 20 )
	{
		settype( $a1, 'integer' );
		settype( $a2, 'integer' );
		if( $a2 < 0 ) $error .= "Вы пытаетесь положить количество товара меньше нуля<br>";
		else
		{
			$item_id = $a1;
			$number = $a2;
			
			if($shop->CheckCapacity($item_id))
			{
				if (!checkCanDrop( $item_id )) $msg = "Этот предмет нельзя выставить в магазин.";
				// проверим попытку выставить орденскую вещь на продажу
				else if ( !checkOrderItem( $item_id ) )
				{
					if($player->DropItems($item_id,$number))
					{
						$shop->AddItems($item_id,$number);
						$name = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$item_id" ) );
						$have = $shop->NumberItems( $item_id );
						$had = $have - $number;
						$log .= "Добавлено [$number] <b>$name[0]</b> (было $had, стало $have)<br>";
					}
					else $error .= "У Вас нет столько вещей!<br>";
				}
				else
				{
					$error .= "Вы пытаетесь продать принадлежащую ордену вещь. Больше так делать не нужно.";
				}
				// -----8<----------
			}
			else $error .= "Максимальное число видов товаров в магазине достигнуто...<br>";
		}
	}

	// Операции с параметрами товара
	else if( $cmd == 10 )
	{
		settype( $a1, 'integer' );
		settype( $a2, 'float' );

		$item_id = $a1;
		$sell_price = $a2;
	
		f_MQuery("UPDATE shop_goods SET sell_price=$sell_price WHERE item_id=$item_id AND shop_id=$shop_id");
		$name = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$item_id" ) );
		$log .= "Установлена цена на продажу <b>$name[0]</b>: <b>$sell_price</b><br>";
	}
	else if( $cmd == 11 )
	{
		settype( $a1, 'integer' );
		settype( $a2, 'float' );

		$item_id = $a1;
		$buy_price = $a2;
	
		f_MQuery("UPDATE shop_goods SET buy_price=$buy_price WHERE item_id=$item_id AND shop_id=$shop_id");
		$name = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$item_id" ) );
		$log .= "Установлена цена на покупку <b>$name[0]</b>: <b>$buy_price</b><br>";
	}
	else if( $cmd == 12 )
	{
		settype( $a1, 'integer' );
		settype( $a2, 'integer' );
		if(( $a2 != -1 && $a2 != 0 && $a2 != 1 ))
			$error .= "Режим сохранения одного из товаров не является числом<br>";
		else
		{
			$item_id = $a1;
			$good_new_regime = $a2;
	
			f_MQuery("UPDATE shop_goods set regime=$good_new_regime WHERE item_id=$item_id AND shop_id=$shop_id");
			$name = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$item_id" ) );
			$regi = array( -1 => "По умолчанию", 0 => "Не сохранять", 1=> "Сохранять" );
			$log .= "Изменен режим сохранения товара <b>$name[0]</b>: <b>{$regi[$good_new_regime]}</b><br>";
		}
	}
	
	else if( $cmd == 100 )
	{
		settype( $a1, "integer" );
		if( $a1 > 0 ) // Добавить монетки
		{
			if( $player->SpendMoney( $a1 ) )
			{
				$had = $shop->money;
				$shop->AddMoney( $a1 );
				$have = $shop->money;
				$log .= "Добавлено $a1 дбл. Было: $had. Стало: $have.<br>";
			}
			else
				$error .= "У вас нет столько монет<br>";
		}
		else if( $a1 < 0 ) // Снять монетки
		{
			$a1 = - $a1;
			$had = $shop->money;
			if( $shop->SpendMoney( $a1 ) )
			{
				$player->AddMoney( $a1 );
				$have = $shop->money;
				$log .= "Снято $a1 дбл. Было: $had. Стало: $have.<br>";
			}
			else
				$error .= "В магазине нет столько монет<br>";
		}
	}
}

if( $error != '' ) print( "Во время применения изменений возникли следующие ошибки: <br>".$error."<br><a href=shop_controls.php>Назад</a><br>" );
else print( "<script>window.opener.location.reload( ); window.close( );</script>" );

if( $log != '' ) f_MQuery( "INSERT INTO shop_control_logs( shop_id, player_id, descr, time ) VALUES ( $shop_id, {$player->player_id}, '".addslashes($log)."', ".time( )." )" );

?>
