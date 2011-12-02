<?

if( !$mul ) die( );

$item_arr = ParseItemStr( $arr['effect'] );

foreach( $item_arr as $a=>$b )
{
	$player->AlterRealAttrib( $a, $b * $mul );
}

?>
