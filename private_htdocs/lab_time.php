<?
require_once("time_functions.php");


include( 'functions.php' );
include('player.php');

f_MConnect( );

$res = f_MQuery( "SELECT count( item_id ) FROM lab_items WHERE lab_id = 0" );
$arr = f_MFetch( $res );
if( $arr[0] < 50 )
{
	$res = f_MQuery( "SELECT cell_id FROM lab WHERE lab_id=0 AND tex=0 ORDER BY rand() LIMIT 1" );
	$arr = f_MFetch(  $res );
	$cell_id = $arr[0];
	$res = f_MQuery( "SELECT count( item_id ) FROM lab_items WHERE lab_id=0 AND cell_id=$cell_id" );
	$arr = f_MFetch( $res );
	$res2 = f_MQuery( "SELECT count( mob_id ) FROM lab_mobs WHERE lab_id=0 AND cell_id=$cell_id" );
	$arr2 = f_MFetch( $res2 );
	if( $arr[0] == 0 && $arr2[0] == 0 )
	{
		$item_id = mt_rand( 1, 5 );
		if( mt_rand( 1, 1440 ) == 5 ) $item_id = 6;
		if( mt_rand( 1, 100 ) == 5 ) $item_id = 8;
		if( mt_rand( 1, 100 ) == 5 ) $item_id = 7;
		f_MQuery( "INSERT INTO lab_items (  lab_id, cell_id, item_id ) VALUES ( 0, $cell_id, $item_id )" );
	}
}

$mobs = array( 24, 25, 26 );
$mob_imgs = array(
	24 => "vamp.png",
	25 => "spider.png",
	26 => "lord.png"
);

$res = f_MQuery( "SELECT count( mob_id ) FROM lab_mobs WHERE lab_id = 0" );
$arr = f_MFetch( $res );
if( $arr[0] < 50 )
{
	$res = f_MQuery( "SELECT cell_id FROM lab WHERE lab_id=0 AND tex=0 ORDER BY rand() LIMIT 1" );
	$arr = f_MFetch(  $res );
	$cell_id = $arr[0];
	$res = f_MQuery( "SELECT count( item_id ) FROM lab_items WHERE lab_id=0 AND cell_id=$cell_id" );
	$arr = f_MFetch( $res );
	$res2 = f_MQuery( "SELECT count( mob_id ) FROM lab_mobs WHERE lab_id=0 AND cell_id=$cell_id" );
	$arr2 = f_MFetch( $res2 );
	if( $arr[0] == 0 && $arr2[0] == 0 )
	{
		$id = mt_rand( 0, count( $mobs ) - 1 );
		$mob_id = $mobs[$id];
		$mob_img = $mob_imgs[$mob_id];
		f_MQuery( "INSERT INTO lab_mobs (  lab_id, cell_id, mob_id, img ) VALUES ( 0, $cell_id, $mob_id, '$mob_img' )" );
	}
}

?>
