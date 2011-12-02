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
$position = $_GET['position'];
settype( $position, 'integer' );
$dir = $_GET['dir'];
settype( $dir, 'integer' );

// ����� ���������� ��� �������������� ����������
if ( isset ( $_GET['sort'] ) )
{
	$sort = $_GET['sort'];
	settype( $sort, 'integer' );
}

if( $player->IsShopOwner( $shop_id ) )
{
	if ( $sort == 1 ) // �������������� ���������� �� ���� ��������
	{
		f_MQuery( "lock table shop_goods write, items write" );
		
		$blah = f_MQuery( "select items.type, items.item_id, shop_goods.* from items, shop_goods where shop_goods.shop_id=$shop_id and items.item_id=shop_goods.item_id order by items.type, items.level" );

		f_MQuery( "delete from shop_goods where shop_id=$shop_id" );
		for ( $i = 0; $i < mysql_num_rows( $blah ); $i++ )
		{
			$rec = f_MFetch ( $blah );
			f_MQuery( "insert into shop_goods (shop_id, item_id, buy_price, sell_price, regime, number) values ($rec[shop_id], $rec[item_id], $rec[buy_price], $rec[sell_price], $rec[regime], $rec[number])" );
		}
		
		f_MQuery( "unlock tables" );
		print("<script>parent.location.href = 'game.php?shop_id=$shop_id';</script>");
	}
	if ( $sort == 2 ) // �� ������
	{
		f_MQuery( "lock table shop_goods write, items write" );
		
		$blah = f_MQuery( "select items.type, items.item_id, shop_goods.* from items, shop_goods where shop_goods.shop_id=$shop_id and items.item_id=shop_goods.item_id order by items.level, items.type" );

		f_MQuery( "delete from shop_goods where shop_id=$shop_id" );
		for ( $i = 0; $i < mysql_num_rows( $blah ); $i++ )
		{
			$rec = f_MFetch ( $blah );
			f_MQuery( "insert into shop_goods (shop_id, item_id, buy_price, sell_price, regime, number) values ($rec[shop_id], $rec[item_id], $rec[buy_price], $rec[sell_price], $rec[regime], $rec[number])" );
		}
		
		f_MQuery( "unlock tables" );
		print("<script>parent.location.href = 'game.php?shop_id=$shop_id';</script>");
	}
	if ( $sort == 3 ) // �� ������ �� ��������
	{
		f_MQuery( "lock table shop_goods write, items write" );
		
		$blah = f_MQuery( "select items.type, items.item_id, shop_goods.* from items, shop_goods where shop_goods.shop_id=$shop_id and items.item_id=shop_goods.item_id order by items.level DESC, items.type" );

		f_MQuery( "delete from shop_goods where shop_id=$shop_id" );
		for ( $i = 0; $i < mysql_num_rows( $blah ); $i++ )
		{
			$rec = f_MFetch ( $blah );
			f_MQuery( "insert into shop_goods (shop_id, item_id, buy_price, sell_price, regime, number) values ($rec[shop_id], $rec[item_id], $rec[buy_price], $rec[sell_price], $rec[regime], $rec[number])" );
		}
		
		f_MQuery( "unlock tables" );
		print("<script>parent.location.href = 'game.php?shop_id=$shop_id';</script>");
	}
	else
	{
		$pos = Array();
		$foo = -1;
		$blah = f_MQuery( "select position from shop_goods where shop_id=" . $shop_id . " order by position" );
		if( mysql_num_rows( $blah ) )
		{
			for ( $i = 0; $i < mysql_num_rows( $blah ); $i++ )
			{
				$rec = f_MFetch ( $blah );
				$pos[$i] = $rec['position']; // ����� ����� ���������� ������� �� �������, ����� �������� ������ � �������-1 � �������+1
				if( $pos[$i] == $position ) $foo = $i;
			}
			
			if( $foo == -1 ) die( );
			
			if( $dir == 1 ) // ���� ��������� �����
			{
				if( !array_key_exists( $foo-1, $pos ) )
					die( "<script>alert( '���� ������� ������ ������� ����!' );</script>" );
				$sum = $pos[$foo] + $pos[$foo - 1];
				f_MQuery( "lock table shop_goods write" );
				f_MQuery( "update shop_goods set position= - position where shop_id=" . $shop_id . " and ( position=" . $pos[$foo] . " OR position=" . ($pos[$foo - 1]) . ")");
				f_MQuery( "update shop_goods set position=$sum + position where shop_id=" . $shop_id . " and ( position=-" . $pos[$foo] . " OR position=-" . ($pos[$foo - 1]) . ")");
				f_MQuery( "unlock tables" );
				print("<script>parent.location.href = 'game.php?shop_id=$shop_id';</script>");
			}
			
			if( $dir == 2 ) // ���� �������� ����
			{
				if( !array_key_exists( $foo+1, $pos ) )
					die( "<script>alert( '���� ������� ������ �������� ����!' );</script>" );
				$sum = $pos[$foo] + $pos[$foo + 1];
				f_MQuery( "lock table shop_goods write" );
				f_MQuery( "update shop_goods set position= - position where shop_id=" . $shop_id . " and ( position=" . $pos[$foo] . " OR position=" . ($pos[$foo + 1]) . ")");
				f_MQuery( "update shop_goods set position=$sum + position where shop_id=" . $shop_id . " and ( position=-" . $pos[$foo] . " OR position=-" . ($pos[$foo + 1]) . ")");
				f_MQuery( "unlock tables" );
				print("<script>parent.location.href = 'game.php?shop_id=$shop_id';</script>");
			}
			
			// ����� ������ ���� ������� ��������� �������� ������ ����� Ajax, �� � �� �� �����
			// ����� ���������� ������ ���� ���� �������, ���� ������������ �������� ������ 2 ��������������� ����
			// �������� ����� ���������� ����� ���� ������� :-[
		}
	}
}
else
{
	die( "<script>alert( '�� �� ������ ����� ��������� ���� ���������!' );</script>" );
}

?>
