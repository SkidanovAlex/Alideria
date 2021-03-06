<?


class Kopka
{
	var $item_prices;
	var $item_id;
	var $num;
	var $anum;
	
	function Kopka( )
	{
		$this->item_prices = Array( );
	}
	
	function AddItem( $item_id, $price )
	{
		$this->item_prices[$item_id] = $price;
	}

	function GetItemId( $time_in_seconds, $money_per_hour, $p = false )
	{
		$item_prices = $this->item_prices;
		asort( $item_prices );
		$len = count( $item_prices );
		if( mt_rand( 0, 1 ) == 0 ) $id = mt_rand( 0, ( $len - 1 ) / 2 );
		else $id = mt_rand( 0, $len - 1 );
		
		$item_ids = array_keys( $item_prices );
		$item_id = $item_ids[$id];
		$price = $item_prices[$item_id];
		
		$income = $money_per_hour * $time_in_seconds / 3600.0;
		
		$has_cheaper = false;
		$has_more_expensive = false;
		foreach( $item_prices as $a )
		{
			if( $a < $income - 1e-9 ) $has_cheaper = true;
			if( $a > $income + 1e-9 ) $has_more_expensive = true;
		}

		if( $has_cheaper && $has_more_expensive ) return $this->GetItemIdImba( $time_in_seconds, $money_per_hour, $p );
		else if( $has_more_expensive ) return $this->GetItemIdAllExpensive( $time_in_seconds, $money_per_hour, $p );
		else return $this->GetItemIdAllCheap( $time_in_seconds, $money_per_hour, $p );
	}

	function GetItemIdImba( $time_in_seconds, $money_per_hour, $p )
	{
		global $isOpush;
		
		$item_prices = $this->item_prices;
		$item_ids = array_keys( $item_prices );
		$item_id = $item_ids[$id];

		$income = $money_per_hour * $time_in_seconds / 3600.0;
		$price =  $item_prices[$item_id];

		$cheap = Array( );
		$expensive = Array( );
		$csum = 0; $esum = 0;

		foreach( $item_prices as $a=>$b )
		{
			if( $b < $income - 1e-9 ) { $cheap[] = $a; $csum += $b; }
			else { $expensive[] = $a; $esum += $b; }
		}

		$csum /= count( $cheap );
		$esum /= count( $expensive );

		$ret = Array( );
		$avg = 0;
		$po = 0;

		$prob = ( ( $esum - $income ) * 1000000 ) / ( $esum - $csum );
		foreach( $cheap as $id )
		{
			if( !$p ) $ret[] = $this->getItemName( $id ).": ".$this->moo( $prob / count( $cheap ) / 10000 ).'%' . ( " (".$this->POData( $item_prices[$id], $money_per_hour ) . "��)" );
			else $ret[] = $this->getItemName( $id )." [1 - 2��]: ".$this->moo( $prob / count( $cheap ) / 10000 ).'%' . ( " (".$this->POData( $item_prices[$id], $money_per_hour ) . "��)" );
			$avg += $prob / count( $cheap ) / 1000000 * $item_prices[$id];
			$po += $prob / count( $cheap ) / 1000000 * $this->PONumber( $item_prices[$id], $money_per_hour );
		}
		$prob = 1000000 - $prob;
		foreach( $expensive as $id )
		{
			if( !$p ) $ret[] = $this->getItemName( $id ).": ".$this->moo( $prob / count( $expensive ) / 10000 ).'%' . ( " (".$this->POData( $item_prices[$id], $money_per_hour ) . "��)" );
			else $ret[] = $this->getItemName( $id )." [1 - 2��]: ".$this->moo( $prob / count( $expensive ) / 10000 ).'%' . ( " (".$this->POData( $item_prices[$id], $money_per_hour ) . "��)" );
			$avg += $prob / count( $expensive ) / 1000000 * $item_prices[$id];
			$po += $prob / count( $expensive ) / 1000000 * $this->PONumber( $item_prices[$id], $money_per_hour );
		}
		if( $p ) $avg *= 1.5;
		if( $p ) $money_per_hour *= 1.5;
		if( $isOpush )
		{
			$money_per_hour *= 150 / 90;
//			$po *= 600 / 90;
		}
		$avg = $this->moo( $avg );
		$po = $this->moo( $po * 3600 / $time_in_seconds );
		$ret[] = "������� ������� �� �����: $avg";
		$ret[] = "������� ������� � ���: $money_per_hour";
		$ret[] = "������� �� � ���: $po";

		return $ret;
	}

	function GetItemIdAllCheap( $time_in_seconds, $money_per_hour, $p )
	{
		global $isOpush;
		
		$item_prices = $this->item_prices;
		$len = count( $item_prices );
		$id = mt_rand( 0, $len - 1 );
		$item_ids = array_keys( $item_prices );
		$item_id = $item_ids[$id];

		$ret = Array( );
		$avg = 0;
		$po = 0;
		foreach( $item_ids as $item_id )
		{
    		$income = $money_per_hour * $time_in_seconds / 3600.0;
    		$price =  $item_prices[$item_id];

    		if( fmod( $income, $price ) == 0 ) 
    		{
    			if( !$p ) $ret[] = $this->getItemName( $item_id )." [".($income / $price)."��]: ".$this->moo( 100.0 / count( $item_ids ) ).'%' . ( " (".$this->POData( $item_prices[$item_id] * ($income / $price), $money_per_hour ) . "��)" );
    			else $ret[] = $this->getItemName( $item_id )." [".($income / $price)." - ".(2 * $income / $price)."��]: ".$this->moo( 100.0 / count( $item_ids ) ).'%' . ( " (".$this->POData( $item_prices[$item_id] * ($income / $price), $money_per_hour ) . "��)" );
    			$avg += $item_prices[$item_id] * ($income / $price) / count( $item_ids );
    			$po += 1.0 / count( $item_ids ) * $this->PONumber( ($income / $price) * $item_prices[$item_id], $money_per_hour );
    		}
    		else
    		{
    			$a = (int)($income / $price);
    			$b = $a + 1;

    			$prob = 1000000 * $income / $price - 1000000 * $a;
    			if( !$p ) $ret[] = $this->getItemName( $item_id )." [".$b."��]: ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%' . ( " (".$this->POData( $item_prices[$item_id] * ($income / $price), $money_per_hour ) . "��)" );
    			else $ret[] = $this->getItemName( $item_id )." [".$b." - ".($b*2)."��]: ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%' . ( " (".$this->POData( $item_prices[$item_id] * ($income / $price), $money_per_hour ) . "��)" );
    			$avg += $item_prices[$item_id] * $b * $prob / 1000000  / count( $item_ids );
    			$prob = 1000000 - $prob;
    			if( !$p ) $ret[] = $this->getItemName( $item_id )." [".$a."��]: ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%' . ( " (".$this->POData( $item_prices[$item_id] * ($income / $price), $money_per_hour ) . "��)" );
    			else $ret[] = $this->getItemName( $item_id )." [".$a." - ".($a*2)."��]: ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%' . ( " (".$this->POData( $item_prices[$item_id] * ($income / $price), $money_per_hour ) . "��)" );
    			$avg += $item_prices[$item_id] * $a * $prob / 1000000  / count( $item_ids );
    			$po += 1.0 / count( $item_ids ) * $this->PONumber( ($income / $price) * $item_prices[$item_id], $money_per_hour );
    		}
    	}
		if( $p ) $avg *= 1.5;
		if( $p ) $money_per_hour *= 1.5;
		if( $isOpush )
		{
			$money_per_hour *= 150 / 90;
//			$po *= 600 / 90;
		}
		$avg = $this->moo( $avg );
		$po = $this->moo( $po * 3600 / $time_in_seconds );
		$ret[] = "������� ������� �� �����: $avg";
		$ret[] = "������� ������� � ���: $money_per_hour";
		$ret[] = "������� �� � ���: $po";

		return $ret;
	}

	function POData( $a, $per_hour )
	{
		global $premium2;

		$ret = ceil( $a * 50 / $per_hour );
		if( $premium2 )  $ret *= 1.5;
		if( fmod( $ret, 1 ) != 0 )
		{
			$ret = (int)$ret . "-" . (int)($ret + 1);
		}

		return $ret;
	}

	function PONumber( $a, $per_hour )
	{
		global $premium2;

		$ret = ceil( $a * 50 / $per_hour );
		if( $premium2 )  $ret *= 1.5;

		return $ret;
	}
	
	function GetItemIdAllExpensive( $time_in_seconds, $money_per_hour, $p )
	{
		global $isOpush;
		
		$item_prices = $this->item_prices;
		$len = count( $item_prices );
		$id = mt_rand( 0, $len - 1 );
		
		$item_ids = array_keys( $item_prices );
		$ret = Array( );
		$avg = 0;
		$po = 0;
		$uncuc = 0;
		foreach( $item_ids as $item_id )
		{
    		$price = $item_prices[$item_id];
    		
    		$income = $money_per_hour * $time_in_seconds / 3600.0;
    		$num1 = ceil( $income / $price );
    		$price *= $num1;
    		
    		$this->item_id = $item_id;
			$prob = floor( $income / $price * 1000000 );
			if( $p ) $ret[] = $this->getItemName( $item_id )." [1 - 2��]: ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%' . ( " (".$this->POData( $item_prices[$item_id], $money_per_hour ) . "��)" );
			else $ret[] = $this->getItemName( $item_id ).": ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%' . ( " (".$this->POData( $item_prices[$item_id], $money_per_hour ) . "��)" );
			$avg += $num1 * $item_prices[$item_id] * $prob / 1000000  / count( $item_ids );;
			$po += $this->PONumber( $item_prices[$item_id], $money_per_hour ) * $prob / 1000000  / count( $item_ids );;

			$unsuc += ( 1000000 - $prob ) / count( $item_ids );
    	}
		if( $p ) $avg *= 1.5;
		if( $p ) $money_per_hour *= 1.5;
		if( $isOpush )
		{
			$money_per_hour *= 150 / 90;
//			$po *= 600 / 90;
		}
		$avg = $this->moo( $avg );
		$po = $this->moo( $po * 3600 / $time_in_seconds );
		$unsuc = $this->moo( $unsuc / 10000 );
		$ret[] = "����������� ������ �����: {$unsuc}%";
		$ret[] = "������� ������� �� �����: $avg";
		$ret[] = "������� ������� � ���: $money_per_hour";
		$ret[] = "������� �� � ���: $po";

		return $ret;
	}

	function moo( $a )
	{
		return floor( $a * 100 + 1e-9 ) / 100.0;
	}

	function getItemName( $a )
	{
		$res = f_MQuery( "SELECT name FROM items WHERE item_id=$a" );
		$arr = f_MFetch( $res );
		if( !$arr ) return "����� �����-��";
		return "<a href=help.php?id=1010&item_id=$a target=_blank>".$arr[0]."</a>";
	}

}


/*$kopka = new Kopka( );

$items = Array( 12=>1000, 14=>1500, 15=>15, 16=>20, 10000=>101 );

foreach( $items as $key=>$value ) $kopka->AddItem( $key, $value );

$moo = 0;
$a = 0; $b=  0;
for( $i = 0; $i < 6000; ++ $i )
{
	$kopka->GetItemId( 600, 3000 ); 
	$moo += $items[$kopka->item_id] * $kopka->num;
//	echo $kopka->item_id." ".$kopka->num."<br>";
	if( $kopka->num ) ++ $a;
	else ++ $b;
}
echo "$moo<br>";
echo $a / ( $a + $b );  */

include_once( 'functions.php' );
include_once( 'guild.php' );
include_once( 'skin.php' );

		echo "<table><tr><td>";ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
		echo "<center><table id=s_table><tr><td>";
?>
<div id="header" align="left">������ ������</div><br />
<?


$guild_id = 101;
$rank = 0;
$rating = 0;
$premium2 = false;
$sc = 600;
$isOpush = false;

if( isset( $_GET['guild_id'] ) )
{
    $guild_id = $_GET['guild_id'];
    $rank = $_GET['rank'];
    $rating = $_GET['rating'];
    if( $_GET['premium'] == 'on' )
    	$premium = true;
    if( $_GET['premium2'] == 'on' )
    	$premium2 = true;

    settype( $guild_id, 'integer' );
    settype( $rank, 'integer' );
    settype( $rating, 'integer' );

   	$moo = false;
	if( $guild_id == 1020 )
	{
		$guild_id = 102;
		$moo = true;
	}
	else if( $guild_id == 102 ) $isOpush = true;

    if( $guilds[$guild_id][4] )
    {
    	echo "<b>������� ".$guilds[$guild_id][0]."</b><br>";
   		$kopka = new Kopka( );

		$res = f_MQuery( "SELECT items.item_id, items.price FROM lake_items, items WHERE lake_items.item_id=items.item_id AND lake_items.guild_id = $guild_id AND lake_items.rank <= {$rank}" );

    	while( $arr = f_MFetch( $res ) )
    		$kopka->AddItem( $arr[0], $arr[1] );
    	
    	$per_hour = 200 + $rating * 50;
    	$sc = (int)($_GET['seconds']);
    	if( $sc < 1 ) $sc = 1;
    	if( $isOpush ) $sc = 150;
    	$ret = $kopka->GetItemId( $sc, $per_hour, $premium );
    	if( $isOpush ) $sc = 90;

    	foreach( $ret as $a ) echo "$a<br>";

    	if( $moo ) $guild_id = 1020;
    }
    echo "<br><br>";
}

	$gnames = Array( );
	foreach( $guilds as $id=>$arr ) if( $arr[4] && $id != 102 )
	{
		$gnames[$id] = $arr[0];
	}
	else if( $id == 102 )
	{
    	$gnames[102] = '����������� - ������';
    	$gnames[1020] = '����������� - ����';
	}

	echo "<form action=help.php method=get><input type=hidden name=id value=1046>";
	echo "<table id='s_table'><tr><td>�������: </td><td>".create_select_global( 'guild_id', $gnames, $guild_id )."</td></tr>";
	echo "<tr><td>����:</td><td><input class=m_btn type=text name=rank value=$rank></td></tr>";
	echo "<tr><td>�������:</td><td><input class=m_btn type=text name=rating value=$rating></td></tr>";
	echo "<tr><td>�����:</td><td><input class=m_btn type=text name=seconds value=$sc> ������</td></tr>";
	echo "<tr><td>&nbsp;</td><td><small>1.5 ������ = 90 ������<br>10 ����� = 600 ������<br>��� �������� ��������-������� ������ �������������� ��� 12 ��� �� 10 �����, � �� ���� ��� ��� ����.</td></tr>";
	echo "<tr><td>&nbsp;</td><td><input type=checkbox name=premium ".($premium?"CHECKED":"")."> �������-������</td></tr>";
	echo "<tr><td>&nbsp;</td><td><input type=checkbox name=premium2 ".($premium2?"CHECKED":"")."> �������-������</td></tr>";
	echo "<tr><td>&nbsp;</td><td><input class=s_btn type=submit value='��������'></td></tr>";
	echo "</table>";
	echo "</form>";

		echo "</td></tr></table></center>";
		echo "</div>";
		ScrollLightTableEnd();echo "</td></tr></table>";

?>


