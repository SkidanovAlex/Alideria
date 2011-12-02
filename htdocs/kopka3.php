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
			if( !$p ) $ret[] = $this->getItemName( $id ).": ".$this->moo( $prob / count( $cheap ) / 10000 ).'%' ;
			else $ret[] = $this->getItemName( $id )." [1 - 2шт]: ".$this->moo( $prob / count( $cheap ) / 10000 ).'%' ;
			$avg += $prob / count( $cheap ) / 1000000 * $item_prices[$id];
			$po += $prob / count( $cheap ) / 1000000 * $this->PONumber( $item_prices[$id], $money_per_hour );
		}
		$prob = 1000000 - $prob;
		foreach( $expensive as $id )
		{
			if( !$p ) $ret[] = $this->getItemName( $id ).": ".$this->moo( $prob / count( $expensive ) / 10000 ).'%'  ;
			else $ret[] = $this->getItemName( $id )." [1 - 2шт]: ".$this->moo( $prob / count( $expensive ) / 10000 ).'%';
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
		$ret[] = "—редн€€ прибыль за ходку: $avg";
		$ret[] = "—редн€€ прибыль в час: $money_per_hour";

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
    			if( !$p ) $ret[] = $this->getItemName( $item_id )." [".($income / $price)."шт]: ".$this->moo( 100.0 / count( $item_ids ) ).'%'  ;
    			else $ret[] = $this->getItemName( $item_id )." [".($income / $price)." - ".(2 * $income / $price)."шт]: ".$this->moo( 100.0 / count( $item_ids ) ).'%'  ;
    			$avg += $item_prices[$item_id] * ($income / $price) / count( $item_ids );
    			$po += 1.0 / count( $item_ids ) * $this->PONumber( ($income / $price) * $item_prices[$item_id], $money_per_hour );
    		}
    		else
    		{
    			$a = (int)($income / $price);
    			$b = $a + 1;

    			$prob = 1000000 * $income / $price - 1000000 * $a;
    			if( !$p ) $ret[] = $this->getItemName( $item_id )." [".$b."шт]: ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%'  ;
    			else $ret[] = $this->getItemName( $item_id )." [".$b." - ".($b*2)."шт]: ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%'  ;
    			$avg += $item_prices[$item_id] * $b * $prob / 1000000  / count( $item_ids );
    			$prob = 1000000 - $prob;
    			if( !$p ) $ret[] = $this->getItemName( $item_id )." [".$a."шт]: ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%'  ;
    			else $ret[] = $this->getItemName( $item_id )." [".$a." - ".($a*2)."шт]: ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%' ;
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
		$ret[] = "—редн€€ прибыль за ходку: $avg";
		$ret[] = "—редн€€ прибыль в час: $money_per_hour";

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
			if( $p ) $ret[] = $this->getItemName( $item_id )." [1 - 2шт]: ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%';
			else $ret[] = $this->getItemName( $item_id ).": ".$this->moo( $prob / 10000 / count( $item_ids ) ).'%';
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
		$ret[] = "¬еро€тность пустой ходки: {$unsuc}%";
		$ret[] = "—редн€€ прибыль за ходку: $avg";
		$ret[] = "—редн€€ прибыль в час: $money_per_hour";

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
		if( !$arr ) return "фигн€ кака€-то";
		return "<a href=../help.php?id=1010&item_id=$a target=_blank>".$arr[0]."</a>";
	}

}

?>