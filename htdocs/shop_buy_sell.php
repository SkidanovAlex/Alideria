<?

include_once( "functions.php" );
include_once( "player.php" );
include_once( "shop.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "�������� ��������� Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$shop_id = $_GET['shop_id'];
settype( $shop_id, 'integer' );

$noob = 0;
if( $player->level == 1 || $player->player_id==173 )
{
    $res = f_MQuery( "SELECT a, b FROM noob WHERE player_id={$player->player_id}" );
    $arr = f_MFetch( $res );
    if( $arr ) { $noob = $arr[0]; $noob_param = $arr[1]; }
    if( $noob ) { include_once( 'noob.php' );  }
    if( $noob && $noob > 6 ) die( "<script>alert( '�� ��� ����� ���, ��� �������. ������ ���������� ����������� ���������.' );</script>" );
    else if( $noob && $noob < 6 ) die( "<script>alert( '�� �����, ������� ���������� ���������� �� ���������.' );</script>" );
}

$sres = f_MQuery( "SELECT * FROM shops WHERE shop_id = $shop_id AND location = {$player->location} AND place = {$player->depth}" );
if( mysql_num_rows( $sres ) )
{
	$item_id = (int)$HTTP_GET_VARS['item_id'];
	$number = (int)$HTTP_GET_VARS['number'];
	if( !$number ) die( );
	
	$sarr = f_MFetch( $sres );
	$shop = new Shop( $sarr['shop_id'] );
	$ok = 0;
	$alter_num = - $number;
	
	$cost = $shop->GetBuyOrSellCost( $item_id, $number );
	
	if( $cost == -1 ) die( "<script>alert( '� �������� ��� ������������ ���������� ������.' );</script>" );
	if( $cost == -2 ) die( "<script>alert( '�������� �������� �� ����� �������� ��������� ��������� ������� ������ � �������.' );</script>" );
	if( $cost == -3 ) die( "<script>alert( '������� �� ������� ����� �������!' );</script>" );
	if( $cost == -4 ) die( "<script>alert( '����� �������� �� ��������� ��������� ��������.' );</script>" );
	
	if( $number > 0 )
	{
		if( $noob )
		{
			if( $number != 1 ) die( "<script>alert( '���� ������ �� ������ ���������� ������ ����.' );</script>" );
			$moo = 0;
			if( $item_id >= 132 && $item_id <= 134 ) $moo = 1;
			if( $item_id >= 135 && $item_id <= 137 ) $moo = 2;
			if( $item_id >= 129 && $item_id <= 131 ) $moo = 4;
			if( $item_id >= 126 && $item_id <= 128 ) $moo = 8;
			if( $item_id == 155 ) $moo = 16;
			if( $item_id == 153 ) $moo = 32;
			if( !$moo ) die( "<script>alert( '���� ������ ��, ��� ���������� ���������.' );</script>" );
			if( $noob_param & $moo ) die( "<script>alert( '�� ��� ����� ��� ����.' );</script>" );
			f_MQuery( "UPDATE noob SET b=".($noob_param | $moo)." WHERE player_id={$player->player_id}" );
			if( 51 == ( $noob_param | $moo ) ) echo "<script>parent.all_bought = true; parent.query( 'n_follow.php?a=6', '' );</script>";
			echo "<script>parent._('nf$moo').style.color='green';</script>";
		}
		if( $player->SpendMoney( $cost ) )
		{
			if( !( $shop->DropItems( $item_id, $number ) ) )
			{
				$player->AddMoney( $cost );
				RaiseError( "� �������� �� ���������� ������. �������: {$shop->shop_id}, �����: $item_id, ����������: $number" );
			}

			if( $player->level == 1 )
			{
				include_once( 'player_noobs.php' );
				echo "<script>";
				PingNoob( 2 );
				echo "</script>";
			}

			$shop->AddMoney( $cost );
			$player->AddToLog( $item_id, $number, 6, $sarr['shop_id'] );
			$player->AddToLogPost( 0, - $cost, 6, $sarr['shop_id'] );
			$player->AddItems( $item_id, $number);
			$ok = 1;

			//����� � ���
			f_MQuery( "INSERT INTO shop_log( timestamp, item_id, shop_id, number, money, player_id ) VALUES ( ".time( ).", $item_id, {$shop->shop_id}, -$number, $cost, {$player->player_id} )" );
		}
		else die( "<script>alert( '� ��� �� ������� �����!' );</script>" );
	}
	else
	{
		if( $noob ) die( "<script>alert( '�� ��������� �� ������ �������. ����� ������ ����, ���� ������ �� ������ ������.' );</script>" );
		$number = - $number;
		if( $shop->SpendMoney( $cost ) )
		{
			if( !( $player->DropItems( $item_id, $number ) ) )
			{
				$shop->AddMoney( $cost );
				die( "<script>alert( '� ��� ��� ������ ���������� ������!' );</script>" );
			}
			else
			{
				$player->AddToLogPost( $item_id, - $number, 6, $sarr['shop_id'] );
				$player->AddToLog( 0, $cost, 6, $sarr['shop_id'] );
        		$player->AddMoney( $cost );
				$shop->AddItems( $item_id, $number);
				$ok = 1;

				//����� � ���
				f_MQuery( "INSERT INTO shop_log( timestamp, item_id, shop_id, number, money, player_id ) VALUES ( ".time( ).", $item_id, {$shop->shop_id}, $number, -$cost, {$player->player_id} )" );
			}
		}
		else die( "<script>alert( '� �������� ������������ ������.' );</script>" );
	}
	
	if( $ok )
	{
	    print( "<script>parent.shop_alterItem( $item_id, $alter_num );" );
	    $player->UpdateWeightStr( false, 'parent.' );
	    echo "parent.update_money( $player->money, $player->umoney );</script>";
    }
}
else RaiseError( "�� �� ���������� � �������� (�������: {$player->location}:{$player->depth})" );

?>
