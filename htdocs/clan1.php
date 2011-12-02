<?

include_once( "clan_wonders.php" );

    		$orientations = Array( 0 => "�����������", "�������", "������" );
    		$elements = Array( 0 => "����", 1 => "�������", 2 => "�����", 3 => "��������" );

    
  		function orderBroadcast( $clan_id, $msg )
		{
			$res = f_MQuery( "SELECT player_id FROM player_clans WHERE clan_id=$clan_id" );	
			while( $arr = f_MFetch( $res ) )
			{
				$plr = new Player( $arr[0] );
				$plr->syst3( $msg );
			}
		}


$buildings = Array( 
	0 => "�������",
	1 => "������",
	3 => "�����",
	4 => "��������",
	5 => "��������",
	6 => "������",
	7 => "�������",
//	8 => "����",

	10 => "���������",
	11 => "����������",
	12 => "�������� ���",
	13 => "����"
);

$building_reqs = Array(
	10 => 5,
	11 => 5,
	12 => 5,
	13 => 5,
	6 => 4,
	4 => 0,
	7 => 3
);

// clan_camp arrays
$id2pl = array( 0 => 9, 1=>14, 3 => 11, 15, 7, 16, 1, 13, 10 => 3, 5, 4, 10 );
$pl2id = array( ); foreach( $id2pl as $a=>$b ) $pl2id[$b] = $a;
$pl2num = array( 1 => 6, 3 => 10, 4 => 5, 5 => 7, 7 => 7, 9 => 9, 10 => 7, 11 => 8, 13 => 8, 14 => 11, 15 => 4, 16 => 6 );


$stone_id = 479;
$clay_id = 114;
$wood_id = 36;
$hours = -1;
$food = -2;
$money = 0;

$CAN_ADMIT = 1;
$CAN_DISMISS = 2;
$CAN_BUILD = 4;
$CAN_CONTROL = 65536;
$CAN_CHANGE_PAGE = 4194304;
$CAN_PUT_TO_TREASURE = 8;
$CAN_TAKE_FROM_TREASURE = 16 + 32;
$CAN_TAKE_FROM_TREASURE_SAFE = 32;
$CAN_PUT_TO_SILO_RED = 131072;
$CAN_PUT_TO_SILO_PURPLE = 262144;
$CAN_PUT_TO_SILO_YELLOW = 524288;
$CAN_PUT_TO_SILO_BLUE = 1048576;
$CAN_PUT_TO_SILO_GREEN = 2097152;
$CAN_PUT_TO_SILO_ANY = 131072 + 262144 + 524288 + 1048576 + 2097152;
$CAN_TAKE_FROM_SILO_RED = 64 + 2048;
$CAN_TAKE_FROM_SILO_PURPLE = 128 + 4096;
$CAN_TAKE_FROM_SILO_YELLOW = 256 + 8192;
$CAN_TAKE_FROM_SILO_BLUE = 512 + 16384;
$CAN_TAKE_FROM_SILO_GREEN = 1024 + 32768;
$CAN_TAKE_FROM_SILO_ANY = 64 + 128 + 256 + 512 + 1024 + 2048 + 4096 + 8192 + 16384 + 32768;
$CAN_TAKE_FROM_SILO_RED_SAFE = 2048;
$CAN_TAKE_FROM_SILO_PURPLE_SAFE = 4096;
$CAN_TAKE_FROM_SILO_YELLOW_SAFE = 8192;
$CAN_TAKE_FROM_SILO_BLUE_SAFE = 16384;
$CAN_TAKE_FROM_SILO_GREEN_SAFE = 32768;
$CAN_TAKE_FROM_SILO_ANY_SAFE = 2048 + 4096 + 8192 + 16384 + 32768;
$CAN_EAT = 8388608;
$CAN_COOK = 16777216;
$CAN_CONTROL_SHOP = 33554432;
$CAN_WATCH_LOG = 67108864;
$CAN_MODERATE = 134217728;
$CAN_SEND_POST = 268435456;
$CAN_MARRY = 536870912;
$CAN_READ_FORUM = 1073741824;

$permition_names = Array(
    $CAN_READ_FORUM => "������ �������� �����",
    $CAN_ADMIT => "��������� � �����",
    $CAN_DISMISS => "��������� �� ������",
    $CAN_CONTROL => "��������� ������� ������ ������",
    $CAN_SEND_POST => "��������� ����� ������ ������",
    $CAN_BUILD => "������� ������",
    $CAN_CHANGE_PAGE => "������������� �������� ������",
    $CAN_CONTROL_SHOP => "��������� ��������� � ������� ������",
    $CAN_PUT_TO_TREASURE => "������ ������ � �����",
    $CAN_TAKE_FROM_TREASURE => "�������� ������ �� �����",
    $CAN_TAKE_FROM_TREASURE_SAFE => "�������� ������ �� �����, ���� ����� ����� ������ ��������� �������������",
    16 => "�������� ������ �� �����, ���� ����� ����� ������ ��������� �������������",
    $CAN_PUT_TO_SILO_RED => "������ ���� �� ������� ����� ������",
    $CAN_PUT_TO_SILO_PURPLE => "������ ���� �� ��������� ����� ������",
    $CAN_PUT_TO_SILO_YELLOW => "������ ���� �� ������ ����� ������",
    $CAN_PUT_TO_SILO_BLUE => "������ ���� �� ����� ����� ������",
    $CAN_PUT_TO_SILO_GREEN => "������ ���� �� ������� ����� ������",
    $CAN_PUT_TO_SILO_ANY => "������ ���� �� �����",
    $CAN_TAKE_FROM_SILO_RED => "����� ���� � ������� ����� ������",
    $CAN_TAKE_FROM_SILO_PURPLE => "����� ���� � ��������� ����� ������",
    $CAN_TAKE_FROM_SILO_YELLOW => "����� ���� � ������ ����� ������",
    $CAN_TAKE_FROM_SILO_BLUE => "����� ���� � ����� ����� ������",
    $CAN_TAKE_FROM_SILO_GREEN => "����� ���� � ������� ����� ������",
    $CAN_TAKE_FROM_SILO_ANY => "����� ���� �� ������",
    $CAN_TAKE_FROM_SILO_RED_SAFE => "����� ���� � ������� ����� ������, ���� ����� ����� ������ ��������� �������������",
    $CAN_TAKE_FROM_SILO_PURPLE_SAFE => "����� ���� � ��������� ����� ������, ���� ����� ����� ������ ��������� �������������",
    $CAN_TAKE_FROM_SILO_YELLOW_SAFE => "����� ���� � ������ ����� ������, ���� ����� ����� ������ ��������� �������������",
    $CAN_TAKE_FROM_SILO_BLUE_SAFE => "����� ���� � ����� ����� ������, ���� ����� ����� ������ ��������� �������������",
    $CAN_TAKE_FROM_SILO_GREEN_SAFE => "����� ���� � ������� ����� ������, ���� ����� ����� ������ ��������� �������������",
    $CAN_TAKE_FROM_SILO_ANY_SAFE => "����� ���� �� ������, ���� ����� ����� ������ ��������� �������������",
    64 => "����� ���� � ������� ����� ������, ���� ����� ����� ������ ��������� �������������",
    128 => "����� ���� � ��������� ����� ������, ���� ����� ����� ������ ��������� �������������",
    256 => "����� ���� � ������ ����� ������, ���� ����� ����� ������ ��������� �������������",
    512 => "����� ���� � ����� ����� ������, ���� ����� ����� ������ ��������� �������������",
    1024 => "����� ���� � ������� ����� ������, ���� ����� ����� ������ ��������� �������������",
    ( 64+128+256+512+1024 ) => "����� ���� �� ������, ���� ����� ����� ������ ��������� �������������",
    $CAN_EAT => "��������������� �������� � ��������",
    $CAN_COOK => "�������� � ��������",
    $CAN_MODERATE => "������������ ����� ������",
    $CAN_MARRY => "��������� ��������������",
    $CAN_WATCH_LOG => "�������� ����"
);

$building_descriptions = Array( 
	0 => "������� ������ ������ �� ������������ ���������� ������ ������.",
	1 => "������ ��������� ������ ������ ��������� ��������� ��������������.",
	3 => "������� ������ ������ �� ������������ ���������� � ��� �����, ������� �� ������ �������.",
	4 => "������ ������� �������� ����������� ���������� ������� ������ ����.",
	5 => "��� ���� ������� ��������, ��� ���� � ������� ��� ���������� ���� ������ ����� ��������������� ��������� �������� � ���.",
	6 => "������ ��������� ������ �������� ������ � ��������� � ���������.",
	7 => "������� ��������� ������ ��������� ��������� ������ � �������� ����.",
	8 => "���� ��������� ������ ��������� ������ �� ������ �������� ������� ��������, � ����� ������ �� ��� �������� ��� ��������.",

	10 => "��������� �������� ������������ ���������� ��������� ������ �������.",
	11 => "���������� �������� ������������ ���������� ����� ������ �������.",
	12 => "�������� ��� �������� ������������ ���������� ����� ������ �������.",
	13 => "���� �������� ������������ ���������� ��� ������ �������."
);

$item_names = Array (
	$stone_id => "������",
	$clay_id => "�����",
	$wood_id => "������",
	$hours => "�����",
	$food => "���",
	$money => "�������"
);

function getSiloCapacity( $level )
{
	return 10 * ( $level + 1 );
}
function getSiloWeight( $level )
{
	return $level * 500 + 1000;
}
function getSiloCurCapacity( $clan_id )
{
	$res = f_MQuery( "select distinct parent_id from items as i inner join clan_items as c on i.item_id=c.item_id where clan_id=$clan_id" );
	return f_MNum( $res );
}
function getSiloCurWeight( $clan_id )
{
	$res = f_MQuery( "select sum( weight * number ) FROM items, clan_items WHERE clan_id=$clan_id AND clan_items.item_id=items.item_id;" );
	$arr = f_MFetch( $res );
	return $arr[0];
}
function itemsSiloNum( $clan_id, $item_id )
{
	$res = f_MQuery( "SELECT number FROM clan_items WHERE clan_id=$clan_id AND item_id=$item_id AND color=0" );
	$arr = f_MFetch( $res );
	if( !$arr ) return 0;
	return $arr[0];
}
function getBLevel( $id )
{
	global $clan_id;
	$res = f_MQuery( "SELECT level FROM clan_buildings WHERE clan_id = $clan_id AND building_id = $id" );
	$arr = f_MFetch( $res );
	if( !$arr ) return 0;
	return $arr[0];
}


function getBuildResources( $clan_id )
{
	global $stone_id;
	global $clay_id;
	global $wood_id;
	global $hours;
	global $food;
	global $money;

	$ret = Array( );
	$ret[$stone_id] = itemsSiloNum( $clan_id, $stone_id );
	$ret[$wood_id] = itemsSiloNum( $clan_id, $wood_id );
	$ret[$clay_id] = itemsSiloNum( $clan_id, $clay_id );

	$res = f_MQuery( "SELECT money, food FROM clans WHERE clan_id=$clan_id" );
	$arr = f_MFetch( $res );
	$ret[$food] = $arr['food'];
	$ret[$money] = $arr['money'];

	return $ret;
}

// returns array with item ids as keys and numbers as values
// item id 0 means money, item id -1 means time, item id -2 means food
function getBuildingCost( $building_id, $level )
{
	global $stone_id;
	global $clay_id;
	global $wood_id;
	global $hours;
	global $food;
	global $money;

	$ret = Array( );

	if( $building_id == 0 || $building_id == 3 || $building_id == 7 || $building_id == 8 ) // barracks or silo or shop or bank
	{
		-- $level;
		$ret[$stone_id] = 20 + 10 * $level;
		$ret[$clay_id] = 20 + 10 * $level;
		$ret[$wood_id] = 50 + 25 * $level;
		$ret[$food] = 20 + 100 * $level;
		$ret[$money] = 1000 + 500 * $level;
		$ret[$hours] = 2 + 6 * $level;

		return $ret;
	}
	if( $building_id == 1 ) // altar
	{
		-- $level;
		$ret[$stone_id] = 2 * pow( 2, $level );
		$ret[$clay_id] = 2 * pow( 2, $level );
		$ret[$wood_id] = 2 * pow( 2, $level );
		$ret[$food] = 200;
		$ret[$hours] = 6;
		if( $level == 0 ) { $ret[87] = 250; }
		if( $level == 2 ) { $ret[88] = 200; }
		if( $level == 4 ) { $ret[89] = 150; }
		if( $level == 6 ) { $ret[90] = 100; }
		if( $level == 8 ) { $ret[91] = 150; }
		if( $level == 10 ) { $ret[92] = 1; }
		if( $level == 1 ) { $ret[26] = 75; }
		if( $level == 3 ) { $ret[27] = 75; }
		if( $level == 5 ) { $ret[28] = 50; }
		if( $level == 7 ) { $ret[29] = 30; }
		if( $level == 9 ) { $ret[30] = 20; }

		return $ret;
	}
	if( $building_id == 4 || $building_id == 5 ) // cafe or school
	{
		$ret[$stone_id] = 20 * $level * $level;
		$ret[$clay_id] = 20 * $level * $level;
		$ret[$wood_id] = 50 * $level * $level;
		$ret[$food] = 200 + 100 * $level * $level;
		$ret[$money] = 1000 + 500 * $level;
		$ret[$hours] = 12 * $level;

		return $ret;
	}
	if( $building_id >= 10 && $building_id <= 13 || $building_id == 6 ) // workshops and portal
	{
		$level;
		$ret[$stone_id] = ( 50 * $level ) * ( $level + 1 ) / 2;
		$ret[$clay_id] = ( 50 * $level ) * ( $level + 1 ) / 2;
		$ret[$wood_id] = ( 125 * $level ) * ( $level + 1 ) / 2;
		$ret[$food] = ( 500 * $level ) * ( $level + 1 ) / 2;
		$ret[$hours] = 12 + ( $level * 12 );

		return $ret;
	}
}

function getBuildingStatus( $id, $level )
{
	if( $id == 0 ) // barracks
	{
		$a = $level * 5 + 5;
		$b = $level * 5 + 10;
		return "������������ ���������� ������ ������: <b>$a</b>.<br>�� ��������� ������ ������: <b>$b</b>.";
	}
	if( $id == 1 ) // ������
	{
		$a = $level;
		$b = $level + 1;
		$st = "";
		if( $a < 11 ) return "�� ���� ��� �� ������ ��������� �������. ������� ����� ��������� ������ � ����������� ������ ������������� ������.";
		return "�� ������ ��������� �������.";
	}
	if( $id == 3 ) // �����
	{
		$a = getSiloCapacity( $level ); $x = getSiloWeight( $level );
		$b = getSiloCapacity( $level + 1 ); $y = getSiloWeight( $level + 1 );
		return "�� ������ ����� ��������� ���� <b>$a</b> ����� ����� ����� �� ����� <b>$x</b>�<br>�� ��������� ������ ������: <b>$b</b> ����� ����� ����� ����� <b>$y</b>.";
	}
	if( $id == 5 ) // ��������
	{
		$a = $level;
		$b = $level + 1;
		return "����� �������� <b>$a</b> ��� � ����.<br>�� ��������� ������ ��������: <b>$b</b> ��� � ����.";
	}
	if( $id == 4 ) // ��������
	{
		$a = 2 * $level;
		$b = 2 * $level + 2;
		return "����� ������ �������� �������� <b>$a%</b> ����� �� ���.<br>�� ��������� ������ ��������: <b>$b%</b> ��������� �����.";
	}
	if( $id == 6 ) // ������
	{
		$a = $level;
		$b = $level + 1;
		$ret = "";
		if( $a < 4 ) $ret .= "�� �� ������ �������� ����������.";
		else $ret .= "�� ������ �������� ���������� � <b>$a</b>".my_word_str( $a, '-�� ������', '-� �������', '-� �������' );
		if( $a < 3 ) $ret .= "<br>�� ������� �������� ����������, ����� ��������� ������ <b>4</b> ������.<br>";
		else $ret .= "<br>�� ��������� ������ �� ������� �������� ���������� � <b>$b</b>".my_word_str( $b, '-�� ������.', '-� �������.', '-� �������.' );
		return $ret;
	}
	if( $id == 7 ) // �����
	{
		$a = 4 * $level;
		$b = 4 * $level + 4;
		if( $a == 0 ) $st = "�� �� ������ ��������� ����.";
		else $st = "����� ��������� ���� <b>$a</b> �����.";
		return "$st.<br>�� ��������� ������ ��������: ����� ��������� ���� <b>$b</b> �����.";
	}
	if( $id == 8 ) // ����
	{
		$a = $level * 2500; $a1 = 20 - ( $level ) * 0.5; $a2 = 20 + ( $level ) * 0.5;
		$b = 2500 + $level * 2500; $b1 = 19.5 - ( $level ) * 0.5; $b2 = 20.5 + ( $level ) * 0.5;
		if( $a == 0 ) $st = "� ��� ��� ����������� �������� ������� ��� ��������� ������.";
		else $st = "���� ����� ����������� � <b>$a</b> ��������� ������������. ������ ���������� ������ ��� �������� � ������� <b>$a1%</b>-<b>$a2%</b>";
		return $st . "<br>�� ��������� ������ ���� ������ ����������� � <b>$b</b> ��������� ������������. ������ ���������� ������ �������� <b>$b1%</b>-<b>$b2%</b>";
	}
	if( $id == 10 ) // ���������
	{
		$a = $level;
		$b = $level + 1;
		return "�������� <b>$a</b> ������ ������ �������.<br>�� ��������� ������ ���������: <b>$b</b> ������ ������ �� �������.";
	}
	if( $id == 11 ) // ����������
	{
		$a = $level;
		$b = $level + 1;
		return "�������� <b>$a</b> ������ ����� ������ �������.<br>�� ��������� ������ ����������: <b>$b</b> ����� �� �������.";
	}
	if( $id == 12 ) // �����
	{
		$a = $level;
		$b = $level + 1;
		return "�������� <b>$a</b> ������ ����� ������ �������.<br>�� ��������� ������ �������� ���: <b>$b</b> ����� �� �������.";
	}
	if( $id == 13 ) // �����
	{
		$a = $level;
		$b = $level + 1;
		return "�������� <b>$a</b> ������ ��� ������ �������.<br>�� ��������� ������ �����: <b>$b</b> ������ ��� �� �������.";
	}

	return "";

}

function getPlayerPermitions( $clan_id, $player_id )
{
	$ret = 0;
	$res = f_MQuery( "SELECT rank, job FROM player_clans WHERE player_id=$player_id AND clan_id=$clan_id" );
	$arr = f_MFetch( $res );
	if( !$arr ) return 0;
	$rank = $arr['rank'];
	$job = $arr['job'];
	$res = f_MQuery( "SELECT permitions FROM clan_ranks WHERE clan_id=$clan_id AND rank=$rank" );
	$arr = f_MFetch( $res );
	if( $arr ) $ret |= $arr[0];
	$res = f_MQuery( "SELECT permitions FROM clan_jobs WHERE clan_id=$clan_id AND job=$job" );
	$arr = f_MFetch( $res );
	if( $arr ) $ret |= $arr[0];
	return $ret;
}

function getControlPoints( $player_id )
{
	global $clan_id;
	$res = f_MQuery( "SELECT control_points FROM player_clans WHERE player_id=$player_id AND clan_id=$clan_id" );
	$arr = f_MFetch( $res );
	return $arr[0];
}

function playerSpendControlPoint( $clan_id, $player_id, $script_tegs = true )
{
	global $player;
	f_MQuery( "LOCK TABLE player_clans WRITE" );
	$res = f_MQuery( "SELECT control_points FROM player_clans WHERE player_id=$player_id AND clan_id=$clan_id" );
	$arr = f_MFetch( $res );
	if( $arr[0] == 0 )
	{
		f_MQuery( "UNLOCK TABLES" );

		if( $script_tegs ) echo "<script>";
		echo "alert( '� ��� �� �������� �� ������ ���� ����������. �� �� ������ ��������� �����-���� �������� � ������.' );";
		if( $script_tegs ) echo "</script>";
		return false;
	}
	if( $arr[0] > 0 ) 
	{
		f_MQuery( "UPDATE player_clans SET control_points = control_points - 1 WHERE player_id=$player_id AND clan_id=$clan_id" );
		$player->syst2( "�� ������� ���� ���� ���������� �������. ��������: <b>".($arr[0]-1).".</b>" );
	}
	f_MQuery( "UNLOCK TABLES" );
	return true;
}

function outControlsList( $permitions = 0, $pr = 2147483647, $parent = 0 )
{
	global $permition_names;
	if( $parent == 0 )
	{
	echo "<script>\n";
	echo "permitions = $permitions;\n";

	?>

	var Pes = new Array( );
	var Pen = 0;

	function outPe( id, nm, parent, g )
	{
		var o = new Object( );
		o.id = id;
		o.nm = nm;
		o.p = parent;
		o.g = g;
		Pes[Pen ++] = o;
	}

	function expand( id )
	{
		if( document.getElementById( 'pev' + id ).style.display == 'none' )
		{
			document.getElementById( 'pev' + id ).style.display = '';
			document.getElementById( 'piv' + id ).src = 'images/e_minus.gif';
			document.getElementById( 'pev' + id ).innerHTML = renderPe( id );
		}
		else 
		{
			document.getElementById( 'pev' + id ).style.display = 'none';
			document.getElementById( 'piv' + id ).src = 'images/e_plus.gif';
		}
	}

	function redrawPe( )
	{
		for( var i = 0; i < Pen; ++ i ) if( document.getElementById( 'pav' + i ) )
		{
			var cim = 'images/e_grey.gif';
			if( ( permitions & Pes[i].id ) == Pes[i].id ) 
				cim = 'images/e_check.gif';
			if( ( permitions & Pes[i].id ) == 0 ) 
				cim = 'images/e_none.gif';
			document.getElementById( 'pav' + i ).src = cim;
		}
	}

	peReadOnly = false;
	function setPe( val )
	{
		if( peReadOnly ) return;
		if( permitions & val )
			permitions &= ~val;
		else
			permitions |= val;
		redrawPe( );
	}

	function renderPe( id )
	{
		var st = '<table cellspacing=0 cellpadding=0>';
		for( o in Pes ) if( id != -1 && Pes[o].p == Pes[id].id || id == -1 && Pes[o].p == 0 )
		{
			var img = '<img width=11 src=empty.gif>';
			var cim = '<img id=pav' + o + ' width=11 height=11 onclick=setPe('+Pes[o].id+') src=images/e_grey.gif>';
			if( Pes[o].g ) img = "<img onclick='expand(" + o + ")' id=piv" + o + " style='cursor:pointer' width=11 height=11 src=images/e_plus.gif>";
			if( ( permitions & Pes[o].id ) == Pes[o].id ) 
				cim = '<img id=pav' + o + ' width=11 height=11 onclick=setPe('+Pes[o].id+') src=images/e_check.gif>';
			if( ( permitions & Pes[o].id ) == 0 ) 
				cim = '<img id=pav' + o + ' width=11 height=11 onclick=setPe('+Pes[o].id+') src=images/e_none.gif>';
			st += "<tr><td valign=top>" + img + "&nbsp;</td><td>" + cim + "&nbsp;" + Pes[o].nm + "<br><div style='display:none;' id=pev" + o + "></div></td></tr>";
		}
		st += "</table>";
		return st;
	}


	<?
	}

	$ret = 0;
	foreach( $permition_names as $id=>$name )
	{
		if( ( $pr | $id ) != $pr || $pr == $id ) continue;
		$ok = true;
		foreach( $permition_names as $id2=>$dummy )
		{
			if( ( $pr | $id2 ) != $pr || $pr == $id2 ) continue;
			if( ( $id2 | $id ) != $id2 || $id2 == $id ) continue;
			$ok = false;
			break;
		}
		if( $ok )
		{
			$ret = 1;
			$good = outControlsList( $permitions, $id, $id );
			echo "outPe( $id, '$name', $parent, $good );\n";
		}
	}
	if( $parent == 0 )
	{
		echo "document.write( '<div id=pdiv>' + renderPe( -1 ) + '</div>' );";
		echo "</script>";
	}
	return $ret;
}

function render_camp( $clan_id, $active = false, $select_wonder = false )
{
	global $id2pl, $pl2id, $pl2num, $buildings;
    $st = "'<table><tr><td>' + rFLUl() + '<div style=\"position:relative;top:0px;left:0px;\">";
    $st .= "<img width=700 height=320 src=images/camp/bg.jpg>";
    $bl[0] = 0; $bl[3] = 0; $bl[5] = 0;
    $res = f_MQuery( "SELECT * FROM clan_buildings WHERE clan_id=$clan_id" );
    while( $arr = f_MFetch( $res ) ) $bl[$arr['building_id']] = $arr['level'];
    foreach( $bl as $bid=>$lvl )
    {
    	$place = $id2pl[$bid];

    	if( $place < 16 )
    	{
        	-- $place;
        	$x = $place % 5;
        	$y = (int)($place / 5);
        	$l = $x * 140; $t = $y * 100 + 14;
            ++ $place;
        }
        else if( $place == 16 )
        {
        	$l = 90; $t = -15;
        }

    	$pic_id = $lvl;
    	if( $place == 16 ) $pic_id = $lvl;
    	else if( $pl2num[$place] <= 5 ) $pic_id = (int)( ($pic_id + 2 ) / 3 );
    	else if( $pl2num[$place] <= 8 ) $pic_id = (int)( ($pic_id + 1) / 2 );
    	if( $pic_id > $pl2num[$place] ) $pic_id = $pl2num[$place];
    	if( $pic_id < 1 ) $pic_id = 1;

    	$src = "images/camp/a/{$place}/{$pic_id}.png";
    	$st .= "' + ( ( document.all ) ? ";
    	$st .= "'<div style=\"position:absolute;left:{$l}px;top:{$t}px;width:140px; height:100px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\'$src\', sizingMethod=\'scale\');\"></div>' : ";
    	$st .= "'<img src=$src width=140 height=100 style=\"position:absolute;left:{$l}px;top:{$t}px\">' ) + '";

    	$src = "images/camp/b/{$place}/{$pic_id}.png";
    	$st .= "' + ( ( document.all ) ? ";
    	$st .= "'<div id=bldg$bid style=\"display:none;position:absolute;left:{$l}px;top:{$t}px;width:140px; height:100px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\'$src\', sizingMethod=\'scale\');\"></div>' : ";
    	$st .= "'<img id=bldg$bid src=$src width=140 height=100 style=\"display:none;position:absolute;left:{$l}px;top:{$t}px\">' ) + '";

    	$oncl = '';
    	if( $active )
    	{
    		if( $bid == 0 ) $oncl = "onclick=\'location.href=\"game.php?order=barracks\"\'";
    		if( $bid == 3 ) $oncl = "onclick=\'location.href=\"game.php?order=silo\"\'";
    		if( $bid == 5 ) $oncl = "onclick=\'location.href=\"game.php?order=cafe\"\'";
    		if( $bid == 6 ) $oncl = "onclick=\'location.href=\"game.php?order=portal\"\'";
    		if( $bid == 7 ) $oncl = "onclick=\'location.href=\"game.php?order=shop_log\"\'";
    	}

    	$ttp = "<b>{$buildings[$bid]}, ��. $lvl</b>";
    	if( $place == 16 && $lvl < 4 ) $ttp = "<b>{$buildings[$bid]}, �������� (��. $lvl)</b>";
    	$st .= "<img $oncl onmousemove=\"_(\'bldg$bid\').style.display=\'\';showTooltipW( event, \'$ttp\' )\" onmouseout=\"_(\'bldg$bid\').style.display=\'none\';hideTooltip();\" src=empty.gif width=140 height=100 style=\"cursor:pointer;position:absolute;left:{$l}px;top:{$t}px\">";
    }
    global $wonders;
    $res = f_MQuery( "SELECT * FROM clan_wonders WHERE clan_id=$clan_id" );
    while( $arr = f_MFetch( $res ) )
    {
    	$place = $arr['cell_id'];

    	$nm = $wonders[$arr[wonder_id]][0];
    	if( $arr['stage'] < 10 ) $nm = "�������";
    	else if( $arr['stage'] < 100 ) $nm .= ", �� ��������";

    	-- $place;
    	$x = $place % 5;
    	$y = (int)($place / 5);
    	$l = $x * 140; $t = $y * 100 + 14;
        if( $arr['stage'] < 10 ) $place = "b";
        else $place = "w{$arr[wonder_id]}";

    	$pic_id = 1 + $arr['stage'] % 10;
    	if( $arr['stage'] == 100 ) $pic_id = $wonders[$arr['wonder_id']][1];

    	$bid  = 1000 + $arr['wonder_id'];

    	global $_COOKIE;
    	$pid = (int)$_COOKIE['c_id'];

    	$src = "images/camp/a/{$place}/{$pic_id}.png";
    	if( $arr['wonder_id'] == 1 && f_MValue( "SELECT count( player_id ) FROM player_triggers WHERE player_id=$pid AND trigger_id=230" ) ) $src = "images/misc/q7/without_ball.png";
    	$st .= "' + ( ( document.all ) ? ";
    	$st .= "'<div style=\"position:absolute;left:{$l}px;top:{$t}px;width:140px; height:100px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\'$src\', sizingMethod=\'scale\');\"></div>' : ";
    	$st .= "'<img src=$src width=140 height=100 style=\"position:absolute;left:{$l}px;top:{$t}px\">' ) + '";

    	$src = "images/camp/b/{$place}/{$pic_id}.png";
    	if( $arr['wonder_id'] == 1 && f_MValue( "SELECT count( player_id ) FROM player_triggers WHERE player_id=$pid AND trigger_id=230" ) ) $src = "images/misc/q7/without_ball_light.png";
    	$st .= "' + ( ( document.all ) ? ";
    	$st .= "'<div id=bldg$bid style=\"display:none;position:absolute;left:{$l}px;top:{$t}px;width:140px; height:100px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\'$src\', sizingMethod=\'scale\');\"></div>' : ";
    	$st .= "'<img id=bldg$bid src=$src width=140 height=100 style=\"display:none;position:absolute;left:{$l}px;top:{$t}px\">' ) + '";

    	$oncl = '';
    	if( $active )
    	{
    		if( $arr['stage'] < 100 ) $oncl = "onclick=\'location.href=\"game.php?order=wonders\"\'";
    	}

    	$st .= "<img $oncl onmousemove=\"_(\'bldg$bid\').style.display=\'\';showTooltipW( event, \'<b>$nm</b>\' )\" onmouseout=\"_(\'bldg$bid\').style.display=\'none\';hideTooltip();\" src=empty.gif width=140 height=100 style=\"cursor:pointer;position:absolute;left:{$l}px;top:{$t}px\">";
    }
    if( $select_wonder )
    {
    	$moo = array( 6, 8, 12 );
    	for( $i = 0; $i < 3; ++ $i )
    	{
    		$place = $moo[$i];
        	-- $place;
	    	$x = $place % 5;
    		$y = (int)($place / 5);
    		$l = $x * 140; $t = $y * 100 + 14;
	        ++ $place;

           	$bid = 100 + $i;

	    	$src = "images/camp/a/b/10.png";
    	   	$st .= "' + ( ( document.all ) ? ";
    		$st .= "'<div style=\"position:absolute;left:{$l}px;top:{$t}px;width:140px; height:100px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\'$src\', sizingMethod=\'scale\');\"></div>' : ";
	    	$st .= "'<img src=$src width=140 height=100 style=\"position:absolute;left:{$l}px;top:{$t}px\">' ) + '";

	    	$src = "images/camp/b/b/10.png";
    	   	$st .= "' + ( ( document.all ) ? ";
    		$st .= "'<div id=bldg$bid style=\"display:none;position:absolute;left:{$l}px;top:{$t}px;width:140px; height:100px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\'$src\', sizingMethod=\'scale\');\"></div>' : ";
	    	$st .= "'<img id=bldg$bid src=$src width=140 height=100 style=\"display:none;position:absolute;left:{$l}px;top:{$t}px\">' ) + '";

           	$oncl = "onclick=\'location.href=\"game.php?order=wonders&place=$i\"\'";
	    	$st .= "<img $oncl onmousemove=\"_(\'bldg$bid\').style.display=\'\';showTooltipW( event, \'<b>�������</b>\' )\" onmouseout=\"_(\'bldg$bid\').style.display=\'none\';hideTooltip();\" src=empty.gif width=140 height=100 style=\"cursor:pointer;position:absolute;left:{$l}px;top:{$t}px\">";
		}
    }
    $st .= "</div>' + rFLL() + '</td></tr></table>'";

    return $st;
}

function deleteClan( $clanId, $cause = '' )
{
	require_once( '/srv/www/alideria/htdocs/functions.php' );
	require_once( '/srv/www/alideria/htdocs/player.php' );
	
	$clanPlayers = f_MQuery( 'SELECT player_id FROM characters WHERE clan_id = '.$clanId );
	while( $player_id = f_MFetch( $clanPlayers ) )
	{
		$ClanPlayer = new Player( $player_id[player_id] );
		
		$ClanPlayer->syst3( "��� ����� ������������� " );
		f_MQuery( 'UPDATE characters SET clan_id = 0 WHERE player_id = '.$ClanPlayer->player_id );
		f_MQuery( 'UPDATE characters SET regime=0, go_till=0, loc = 2, depth = 0 WHERE player_id='.$ClanPlayer->player_id.' AND loc=2 AND depth=19' );
	}

	// ���������������, ��������
	f_MQuery( 'DELETE FROM clans WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_bets WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_buildings WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_build_queue WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_items WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_jobs WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_log WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_ranks WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_shelf_names WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_wonders WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_wonder_ips WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_wonder_items_spent WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM player_clans WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM forum_rooms WHERE id = -'.$clanId );
}

?>
