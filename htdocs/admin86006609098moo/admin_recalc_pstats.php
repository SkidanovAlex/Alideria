<?
// фиксит количество первичных статов

include( '../functions.php' );
include( '../player.php' );

include_once( "../arrays.php" );
include_once( "../wear_items.php" );

f_MConnect( );

$res = f_MQuery( "SELECT player_id FROM characters" );

while( $arr = f_MFetch( $res ) )
{

	$player = new Player( $arr[0] );
	echo $arr[0];
			/* foreach( $item_types_all as $a=>$b )
				if( $a > 0 && HasItemInSlot( $a ) )
					UnWearItem( $a ) */;
			$aa = 0; $ab = 0;

			$attrs = Array( 30,40,50 );
			$sum = 0;
			foreach( $attrs as $a=>$b )
			{
				$val = $player->GetActualAttr( $b );
				$sum += $val;
//				$player->AlterActualAttrib( $b, -$val );
			}
			$cur = $player->GetRealAttr( 1000 ) ;
			$need = $player->level  * 3 ;
			$aa = $need - $sum - $cur;
//			$player->SetRealAttr( 1000, $need );

			if( $aa < 0 )
			{
/*    			foreach( $item_types_all as $a=>$b )
    				if( $a > 0 && HasItemInSlot( $a ) )
    					UnWearItem( $a );
    								foreach( $attrs as $a=>$b )
			{
				$val = $player->GetActualAttr( $b );
				$sum += $val;
				$player->AlterActualAttrib( $b, -$val );
			}
			$cur = $player->GetRealAttr( 1000 ) ;
			$need = $player->level * 3;
			$aa = $need - $sum - $cur;
			$player->SetRealAttr( 1000, $need );

			$player->syst2( "Вы были принудительно переобучены" );
				echo $player->login." - переобучить<br>";*/
			}
			else if( $aa ){
				echo "MOO!!!";
				$player->AlterRealAttrib( 1000, $aa );

			}
//			echo "$sum $cur $need";
			if( $aa )
				echo "{$player->login} : $aa<br>";
//			$player->SetRealAttr( 1001, $player->level * 2 + 1 );


}

die( );
?>