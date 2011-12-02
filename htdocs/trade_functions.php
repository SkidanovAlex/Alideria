<?

include_once( "items.php" );

$stats = $player->getAllAttrNames( );

function OutCol( $money, $res, $inv = 0, $umoney = 0 )
{
	if( !$money && !f_MNum( $res ) && !$umoney )
	{
		if( $inv == 1 ) printf( "<i>Пусто</i>" );
		else printf( "<i>Ничего не поставлено</i>" );
		return;
	}
	
	print( "<table>" );
	if( $money )
	{
		printf( "<tr><td align=center><img width=11 height=11 src='images/money.gif'></td><td>[$money] <b>Дублоны</b></td>" );
		if( $inv == 1 )
		{
			printf( "<td align=right><table border=0 cellspacing=0 cellpadding=0><tr><td><input type=text class=btn40 value=1 name=place" . 0 . " id=place" . 0 . "></td><td><button onClick=place(" . 0 . ") class=sss_btn>>>></button></td></tr></table></td>" );
		}
		if( $inv == 2 )
		{
			printf( "<td align=right><table border=0 cellspacing=0 cellpadding=0><tr><td><input type=text class=btn40 value=1 name=rem" . 0 . " id=rem" . 0 . "></td><td><button onClick=rem(" . 0 . ") class=sss_btn>&lt;&lt;&lt;</button></td></tr></table></td>" );
		}
		printf( "</tr>" );
	}
	if( $umoney )
	{
		printf( "<tr><td align=center><img width=11 height=11 src='images/umoney.gif'></td><td>[$umoney] <b>Таланты</b></td>" );
		if( $inv == 1 )
		{
			printf( "<td align=right><table border=0 cellspacing=0 cellpadding=0><tr><td><input type=text class=btn40 value=1 name=place" . -1 . " id=place" . -1 . "></td><td><button onClick=place(" . -1 . ") class=sss_btn>>>></button></td></tr></table></td>" );
		}
		if( $inv == 2 )
		{
			printf( "<td align=right><table border=0 cellspacing=0 cellpadding=0><tr><td><input type=text class=btn40 value=1 name=rem" . -1 . " id=rem" . -1 . "></td><td><button onClick=rem(" . -1 . ") class=sss_btn>&lt;&lt;&lt;</button></td></tr></table></td>" );
		}
		printf( "</tr>" );
	}
	while( $arr = f_MFetch( $res ) )
	{
		global $player;
		$st = itemFullDescr( $arr );
		echo( "<tr><td align=center><img src=images/items/".itemImage( $arr )."></td><td valign=top>[$arr[number]] <span onmousemove='showTooltipW( event, \"".$st."\", 250 )' onmouseout='hideTooltip()'><b>$arr[name]</b></span></td>" );
		if( $inv == 1 )
		{
			echo( "<td valign=top align=right><table border=0 cellspacing=0 cellpadding=0><tr><td><input type=text class=btn40 value=1 name=place" . $arr[item_id] . " id=place" . $arr[item_id] . "></td><td><button onClick=place(" . $arr[item_id] . ") class=sss_btn>>>></button></td></tr></table></td>" );
		}
		else if( $inv == 2 )
		{
			echo( "<td valign=top align=right><table border=0 cellspacing=0 cellpadding=0><tr><td><input type=text class=btn40 value=1 name=rem" . $arr[item_id] . " id=rem" . $arr[item_id] . "></td><td><button onClick=rem(" . $arr[item_id] . ") class=sss_btn>&lt;&lt;&lt;</button></td></tr></table></td>" );
		}
		echo( "</tr>" );
	}
	printf( "</table>" );
}

function ExchangeGoods( $player1, $player2 )
{
	$res = f_MQuery( "SELECT * FROM trade_goods WHERE player_id = $player1->player_id" );
	$st = "";
	$cost = 0;
	while( $arr = f_MFetch( $res ) )
	{
		$ok = true;
		if( $arr[good_type] == 0 )
		{
			$item_name = "Дублонов";
			$cost += $arr[number];
			if( $player1->SpendMoney( $arr[number] ) )
			{
				$player1->AddToLogPost( 0, - $arr[number], 2, $player2->player_id );
				$player2->AddToLog( 0, $arr[number], 2, $player1->player_id );
				$player2->AddMoney( $arr[number] );
			}
			else { LogError( "При обмене {$player1->player_id} и {$player2->player_id} не передалось $arr[number] монет" ); $ok = false; }
		}
		else if( $arr[good_type] == -1 )
		{
			$item_name = "Талантов";
			$cost += $arr[number];
			if( $player1->SpendUMoney( $arr[number] ) )
			{
				$player1->AddToLogPost( -1, - $arr[number], 2, $player2->player_id );
				$player2->AddToLog( -1, $arr[number], 2, $player1->player_id );
				$player2->AddUMoney( $arr[number] );
			}
			else { LogError( "При обмене {$player1->player_id} и {$player2->player_id} не передалось $arr[number] талантов" ); $ok = false; }
		}
		else
		{
			$arr1 = f_MFetch( f_MQuery( "SELECT name, price FROM items WHERE item_id = $arr[good_id]" ) );
			if( !$arr1 ) $item_name = "Незвестная вещь";
			else
			{
				$item_name = $arr1[0];
				$cost += $arr1[1] * $arr[number];
			}
			if( $player1->DropItems( $arr[good_id], $arr[number] ) )
			{
				$player1->AddToLogPost( $arr[good_id], - $arr[number], 2, $player2->player_id );
				$player2->AddToLog( $arr[good_id], $arr[number], 2, $player1->player_id );
				$player2->AddItems( $arr[good_id], $arr[number] );
			}
			else { LogError( "При обмене {$player1->player_id} и {$player2->player_id} не передалось $arr[number] $item_name" ); $ok = false; }
		}
		
		$st .= "[{$arr[number]}] <b>$item_name</b>";
		if( !$ok ) $st .= " <font color=red>не передалось</font>";
		$st .= "<br>";
	}
	
	$st .= "Оценка: <u>$cost</u><br>";
	
	return $st;
}

?>
