<?

class Kopka
{
	var $item_prices;
	var $item_id;
	var $num;
	var $avgnum;
	
	function Kopka( )
	{
		$this->item_prices = Array( );
	}
	
	function AddItem( $item_id, $price )
	{
		$this->item_prices[$item_id] = $price;
	}

	function GetItemId( $time_in_seconds, $money_per_hour, $premium = false )
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

		if( $has_cheaper && $has_more_expensive ) return $this->GetItemIdImba( $time_in_seconds, $money_per_hour, $premium );
		else if( $has_more_expensive ) return $this->GetItemIdAllExpensive( $time_in_seconds, $money_per_hour, $premium );
		else return $this->GetItemIdAllCheap( $time_in_seconds, $money_per_hour, $premium );
	}

	function GetItemIdImba( $time_in_seconds, $money_per_hour, $premium )
	{
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


		$prob = ( ( $esum - $income ) * 1000000 ) / ( $esum - $csum );
		if( mt_rand( 0, 999999 ) < $prob )
		{
			$id = mt_rand( 0, count( $cheap ) - 1 );
			$this->item_id = $cheap[$id];
			$this->num = 1;
		}
		else
		{
			$id = mt_rand( 0, count( $expensive ) - 1 );
			$this->item_id = $expensive[$id];
			$this->num = 1;
		}
		$this->avgnum = 1;
		if( $premium ) $this->num = mt_rand( 1, 2 );
	}

	function GetItemIdAllCheap( $time_in_seconds, $money_per_hour, $premium )
	{
		$item_prices = $this->item_prices;
		$len = count( $item_prices );
		$id = mt_rand( 0, $len - 1 );
		$item_ids = array_keys( $item_prices );
		$item_id = $item_ids[$id];

		$income = $money_per_hour * $time_in_seconds / 3600.0;
		$price =  $item_prices[$item_id];

		if( fmod( $income, $price ) == 0 )
		{
			$this->num = (int)($income / $price);
		}
		else
		{
			$a = (int)($income / $price);
			$b = $a + 1;

			$prob = 1000000 * $income / $price - 1000000 * $a;
			if( mt_rand( 0, 999999 ) < $prob ) $this->num = $b;
			else $this->num = $a;
		}
		if( $premium ) $this->num = mt_rand( $this->num, $this->num * 2 );
		$this->avgnum = ($income / $price);

		$this->item_id = $item_id;
	}
	
	function GetItemIdAllExpensive( $time_in_seconds, $money_per_hour, $premium )
	{
		$item_prices = $this->item_prices;
//		asort( $item_prices ); - already done by GetItemId
		$len = count( $item_prices );
//		if( mt_rand( 0, 1 ) == 0 ) $id = mt_rand( 0, ( $len - 1 ) / 2 );
//		else $id = mt_rand( 0, $len - 1 );
		$id = mt_rand( 0, $len - 1 );
		
		$item_ids = array_keys( $item_prices );
		$item_id = $item_ids[$id];
		$price = $item_prices[$item_id];
		
		$income = $money_per_hour * $time_in_seconds / 3600.0;
		
		$this->item_id = $item_id;
		if( mt_rand( 0, 1000000 ) < floor( $income / $price * 1000000 ) )
		{
			if( !$premium ) $this->num = 1;
			else $this->num = mt_rand( 1, 2 );
			$this->avgnum = 1;
		}
		else
		{
			$this->num = 0;
			$this->avgnum = 0;
		}
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

?>

