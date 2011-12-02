<?
require_once("time_functions.php");


include( 'functions.php' );
include( 'clan.php' );

f_MConnect( );

$res = f_MQuery( "SELECT clan_buildings.* FROM clan_buildings INNER JOIN clans ON clan_buildings.clan_id=clans.clan_id WHERE building_id IN ( 10, 11, 12, 13 ) AND ta_lost=0" );
f_MQuery( "LOCK TABLE clan_items WRITE, clans WRITE, items WRITE, clan_buildings WRITE" );
while( $arr = f_MFetch( $res ) )
{
	$clan_id = $arr['clan_id'];
	$weight1 = getSiloCurWeight($clan_id);
	$weight2 = getSiloWeight(getBLevel( 3 ));
	if ($weight1/100 < $weight2)
	{
		$item_id = $wood_id;
		if( $arr['building_id'] == 11 ) $item_id = $stone_id;
		if( $arr['building_id'] == 12 ) $item_id = $clay_id;
		if( $arr['building_id'] == 13 )
		{
			f_MQuery( "UPDATE clans SET food=food+$arr[level] WHERE clan_id=$clan_id" );
			continue;
		}
		$number = $arr['level'];

		$ires = f_MQuery( "SELECT number FROM clan_items WHERE clan_id=$clan_id AND item_id=$item_id AND color=0" );
		$iarr = f_MFetch( $ires );
		if( $iarr ) f_MQuery( "UPDATE clan_items SET number=number+$number WHERE clan_id=$clan_id AND item_id=$item_id AND color=0" );
		else f_MQuery( "INSERT INTO clan_items ( clan_id, item_id, number, color ) VALUES ( $clan_id, $item_id, $number, 0 )" ); 
	}
}
f_MQuery( "UNLOCK TABLES" );

?>
Moo!