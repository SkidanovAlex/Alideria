<?

include( 'functions.php' );
include( 'player.php' );
include( 'tella_assault.php' );

f_MConnect( );

$won = 0;
$lost = 0;
for( $i = 0; $i < 7; ++ $i )
{
	$mode = ta_check( $i );
	if( $mode == 2 ) ++ $won;
	if( $mode == 0 ) ++ $lost;
	print " $mode";
}

if( $won == 7 ) f_MQuery( "UPDATE clans SET ta_lost = ta_lost + 1 WHERE ta_lost > 0 AND ta_lost < 3" );
else if( $won + $lost == 7 ) f_MQuery( "UPDATE clans SET ta_lost = 1 WHERE ta_lost < 10 AND hascamp>0" );

?>
