<?

header("Content-type: text/html; charset=windows-1251");

include_once( "../no_cache.php" );
include_once( "../functions.php" );
include_once( "../player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT talk_id FROM player_talks WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr || $arr[0] != 278 ) die( );

$id = $HTTP_RAW_POST_DATA;

settype( $id, 'integer' );

if( $id < 0 || $id > 1 ) RaiseError( "Попытка выбрать неверное действие в астральном мире" );

mt_srand( time( ) );

f_MQuery( "LOCK TABLE player_mines WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

$default_field = '........................032500100';
//player coords (2)
//enemy coords (2)
//worlds finished
//move { 0 - you, 1 - your action, 2 - enemy, 3 - enemy action }
//move count { for move skips }
//finished
//need_portal

if( !$arr )
{
    $st = $default_field;
	f_MQuery( "INSERT INTO player_mines( player_id, f ) VALUES ( {$player->player_id}, '$st' )" );
	$f = $st;
}
else
{
	$f = $arr['f'];
}

//$f[31] = '2';
//$f = $default_field;
//echo "alert( '$f' );";
//die( );

$field = array(
	array( 0, 0, 0, 8, 4, 1, 4 ,2, 3, 1 ),
	array( 0, 0, 0, 0, 0, 0, 0 ,0, 0, 4 ),
	array( 1, 2, 4, 1, 3, 2, 3 ,1, 2, 2 ),
	array( 3, 0, 0, 0, 0, 0, 0 ,0, 0, 0 ),
	array( 2, 2, 1, 4, 2, 3, 4 ,2, 1, 9 )
);

$field_ids = array(
	array( -1, -1, -1, 0, 1, 2, 3, 4, 5, 6 ),
	array( -1, -1, -1, -1, -1, -1, -1, -1, -1, 7 ),
	array( 17, 16, 15, 14, 13, 12, 11, 10, 9, 8 ),
	array( 18, -1, -1, -1, -1, -1, -1, -1, -1, -1 ),
	array( 19, 20, 21, 22, 23, 24, 25, 26, 27, 28 )
);

$direction = array(
	array( -1, -1, -1, 0, 0, 0, 0, 0, 0, 3 ),
	array( -1, -1, -1, -1, -1, -1, -1, -1, -1, 3 ),
	array( 3, 2, 2, 2, 2, 2, 2, 2, 2, 2 ),
	array( 3, -1, -1, -1, -1, -1, -1, -1, -1, -1 ),
	array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 3 )
);


$actions =
array(
array(
 array(
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 6, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 511, 0, 2055, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 896, 0, 7303, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1016, 0, 15807, 159, 14343, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 896, 0, 16315, 443, 14523, 6147, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 897, 897, 15233, 15745, 129, 14337, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 104, 8, 104, 40, 8, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( )
 ),
 array(
  array( ),
  array( ),
  array( ),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 896, 0, 15751, 135, 14343, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1023, 511, 14591, 6151, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 903, 391, 14983, 14343, 135, 14343, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1015, 919, 15351, 16279, 407, 10391, 14343, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1023, 927, 15359, 16319, 959, 14751, 14495, 2055, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1019, 1023, 15359, 16383, 1023, 15359, 14847, 7423, 7, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 897, 903, 15239, 16263, 903, 11143, 15239, 15751, 135, 10246, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 888, 1019, 7167, 16383, 1023, 15359, 15359, 15871, 191, 14343, 0, 0, 0, 0, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 400, 2745, 16187, 955, 13243, 15291, 16315, 955, 11195, 386, 130, 12288, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 896, 16257, 897, 11137, 15233, 16257, 897, 15233, 129, 129, 8192, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 104, 104, 8, 104, 104, 72, 104, 104, 104, 0, 0, 0),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( )
 ),
 array(
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 903, 903, 15239, 16263, 903, 14727, 14471, 2055, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1019, 1023, 15359, 16383, 1023, 15359, 14847, 7423, 7, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 897, 903, 15239, 16263, 903, 11143, 15239, 15751, 135, 10246, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 880, 1011, 7159, 16375, 1015, 15351, 15351, 15863, 183, 14343, 0, 0, 0, 0, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 400, 2745, 16251, 1023, 13311, 15359, 16383, 1023, 11263, 390, 134, 12288, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 896, 16257, 899, 11139, 15239, 16263, 903, 15239, 135, 135, 8192, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1008, 1009, 7057, 15351, 16375, 983, 15351, 503, 247, 12288, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 784, 944, 3067, 16383, 1023, 11263, 255, 255, 8192, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 896, 2945, 16259, 903, 15239, 391, 135, 14340, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1016, 16377, 971, 15359, 507, 207, 14338, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 888, 985, 7163, 509, 223, 14341, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 400, 2745, 378, 251, 10318, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 640, 257, 129, 14339, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 888, 472, 2201, 2052, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 128, 2049, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 0),
  array( ),
  array( )
 ),
 array(
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 903, 900, 15239, 391, 132, 12288, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 899, 903, 15239, 391, 135, 14340, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 896, 16369, 963, 15351, 499, 199, 14338, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 880, 977, 7155, 501, 215, 14341, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 400, 2745, 378, 251, 10318, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 640, 257, 129, 14339, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 880, 464, 2193, 2052, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 128, 2049, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 16, 0),
  array( ),
  array( )
 )
),
array(
 array(
  array( ),
  array( ),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 896, 0, 3200, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 59, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 58, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 896, 385, 14976, 7424, 128, 12288, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( )
 ),
 array(
  array( ),
  array( ),
  array( ),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 126, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 896, 391, 14976, 5376, 128, 4096, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 23, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 63, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 123, 0, 0, 0, 0, 0, 0, 7, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 7, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 120, 0, 0, 0, 0, 0, 0, 123, 0, 0, 0, 0, 0, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 58, 0, 0, 2, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 640, 16128, 896, 8576, 14976, 16128, 385, 14464, 0, 1, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 0, 0, 72, 0, 0, 0),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( )
 ),
 array(
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 7, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 123, 0, 0, 0, 0, 0, 0, 7, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 7, 0, 0, 0, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 112, 0, 0, 0, 0, 0, 0, 115, 0, 0, 0, 0, 0, 0),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 126, 0, 0, 6, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 640, 16128, 896, 8576, 14976, 16128, 387, 14464, 0, 3, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 21, 0, 0, 85, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 126, 0, 0, 126, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 0, 0, 7, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 0, 0, 11, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 24, 0, 0, 25, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 48, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 640, 0, 128, 12288, 0, 0),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( )
 ),
 array(
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 6, 0, 0, 6, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 0, 0, 7, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 65, 0, 0, 67, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 16, 0, 0, 17, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 48, 0, 0, 0),
  array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 640, 0, 128, 12288, 0, 0),
  array( ),
  array( ),
  array( ),
  array( ),
  array( ),
  array( )
 )
)
);

$cy = array( );
$cx = array( );
$cact = array( );

for( $i = 0; $i < 5; ++ $i )
{
	for ( $j = 0; $j < 10; ++ $j )
	{
		if ( $field_ids[$i][$j] >= 0 )
		{
			$cact[$field_ids[$i][$j]] = $field[$i][$j];
			$cy[$field_ids[$i][$j]] = $i;
			$cx[$field_ids[$i][$j]] = $j;
		}
	}
}

function to_num( $c )
{
	if ( $c == '.' )
		return -1;
	return ord( $c ) - ord( '0' );
}

$py = to_num( $f[24] );
$px = to_num( $f[25] );
$ey = to_num( $f[26] );
$ex = to_num( $f[27] );
$worlds = to_num( $f[28] );
$move_a = to_num( $f[29] );
$move_count = to_num( $f[30] );
$finished = to_num( $f[31] );
$need_portal = to_num( $f[32] );
//echo "alert( '$f' );";
$leave_talk_id = 645;

if ( $finished == 1 )
{
	$f[31] = '2';
    f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );
	f_MQuery( "UNLOCK TABLES" );
	echo "_('AllDiv').innerHTML = \"<div style='width:226px; height:132px; position:absolute; left:230px; top:100px; background-image:url(images/nevesom/big_text_bg.png); padding:10px;background-repeat:no-repeat;font-size:10px;'>Злодей, оборотень перед Вашим носом юркнул во Врата. Вы уже были так близко, что даже протянули руку, чтоб схватить его, но единственное, что осталось в Ваших руках – волосинка. Один светлый волос. И всё. А как же сундук? Как же Говорун? Вы подвели его. Но делать нечего, пути назад нету, тут уже никакая магия не поможет. Теперь только вперед. Снова кружится голова...</div>\";";
	echo "setTimeout( \"moo( 0 );\", 7000 );";
	die( );
}
else
if ( $finished >= 2 )
{
	$addexp = false;
	if ( $finished == 2 )
	{
		$addexp = true;
	}
	$f[31] = '3';
    f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );
	f_MQuery( "UNLOCK TABLES" );
	if ( $addexp )
	{
		$val = 100 * $player->level;
		f_MQuery( "UPDATE characters SET exp=exp+$val WHERE player_id={$player->player_id}" );
		//neeed give some exp to player 100 * level
		//$player->Add
	}
	echo "location.href='game.php?phrase=$leave_talk_id';";
//	echo "out( $py, $px, $ey, $ex );";
	die( );
}

if ( $need_portal )
{
	if ( $worlds < 0 || $worlds >= 3 )
		RieseError( 'О_о в четвертый мир собрался' );
	if( $worlds == 0 ) //illus
	{
		$f = '000050060332071030020...' . substr( $f, 24 );
		echo "location.href='game.php?phrase=646';";
	}
	else
	if( $worlds == 1 ) //grez
	{
		$f = '3.......................' . substr( $f, 24 );
		echo "location.href='game.php?phrase=647';";
	}
	else
	if( $worlds == 2 ) //nevesom
	{
		$f = '00030633................' . substr( $f, 24 );
		echo "location.href='game.php?phrase=648';";
	}
    f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );
	f_MQuery( "UNLOCK TABLES" );
	die( );
}

if ( $py < 0 || $px < 0 || $py >= 5 || $px  >= 10 )
{
	f_MQuery( "UNLOCK TABLES" );
	RaiseError( 'There is a bug in player coords' );
}
if ( $ey < 0 || $ex < 0 || $ey >= 5 || $ex  >= 10 )
{
	f_MQuery( "UNLOCK TABLES" );
	RaiseError( 'There is a bug in enemy coords' );
}

$player_pos = $field_ids[$py][$px];
if ( $player_pos < 0 )
{
	f_MQuery( "UNLOCK TABLES" );
	RaiseError( 'There is a bug in player coords, (-1)' );
}
$enemy_pos = $field_ids[$ey][$ex];
if ( $enemy_pos < 0 )
{
	f_MQuery( "UNLOCK TABLES" );
	RaiseError( 'There is a bug in enemy coords, (-1)' );
}

if ( $move_a < 0 || $move_a > 3 )
{
	f_MQuery( "UNLOCK TABLES" );
	RaiseError( 'Strange move_id' );
}

$amask = $actions[$move_count - 1][$worlds][$player_pos][$enemy_pos];
//echo "alert( '$amask, $move_a, $player_pos, $enemy_pos' );";
//f_MQuery( "UNLOCK TABLES" );
//die( );
$new_player_pos = $player_pos;
$new_enemy_pos = $enemy_pos;
$new_worlds = $worlds;
$new_move_a = $move_a;
$new_move_count = $move_count;

$add_money = 0;
$portal = 0;
$hint = '';

if ( $player_pos == 26 && $enemy_pos == 27 && $worlds == 3 && $move_a == 2 ) //win (end)
{
	$f[26] = '4';
	$f[27] = '9';
	$f[31] = '1';
    f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );
	f_MQuery( "UNLOCK TABLES" );
	$player->SetTrigger( 118 );
	echo "setTimeout( 'moo( 0 );', 1000 );";
	echo "move( '47..', 'Оборотень сбежал', 4, 8, 0, 1, 1 );"; //win here
	echo "setTimeout( \"out( '4791', '' );\", 666 );";
	die( );
}

$nothing = false;
if ( $move_a == 0 && $id == 0 )
{
	echo "activeMove( true );";
}
else
{
	echo "activeMove( false );";
}

if ( $move_a == 0 && $id == 1 )
{
	//player move
	$aa = array( );
	for ( $i = 0; $i < 3; ++ $i )
	{
		if ( ( ( 1 << $i ) & $amask ) )
		{
			$aa[] = $i + 1;
		}
	}
	$n = count( $aa );
	if ( $n == 0 )
	{
		f_MQuery( "UNLOCK TABLES" );
		RaiseError( 'No moves for player' );
	}
	$rid = mt_rand( ) % $n;
	$new_player_pos += $aa[$rid];
	$new_move_a = 1;
}
else
if ( $move_a == 1 )
{
	if ( $cact[$player_pos] == 1 || $cact[$player_pos] == 2 )
	{
		$aa = array( );
		$port = false;
		for ( $i = 0; $i < 4; ++ $i )
		{
			if ( ( ( 1 << ( $i + 3 ) ) & $amask ) )
			{
				if ( $i == 0 && $cact[$player_pos] == 2 )
					$port = true;
				if ( $i == 1 )
				{
					$aa[] = 5;
					$aa[] = 6;
					if ( $cact[$player_pos] == 2 )
						$aa[] = 7;
				}
				$aa[] = $i + 1;
			}
		}
		$n = count( $aa );
		if ( $n == 0 )
		{
			f_MQuery( "UNLOCK TABLES" );
			RaiseError( 'No actions for player' );
		}
		$rid = $aa[mt_rand( ) % $n];
		if ( $port )
			$rid = 1;
		if ( $cact[$player_pos] == 1 )
		{
			if ( $rid != 1 )
			{
				if ( $move_count == 2 )
				{
					$new_move_count = 1;
					$new_move_a = 0;
				}
				else
				{
					$new_move_a = 2;
				}
			}
			if ( $rid == 1 )
			{
				$hint = 'О, вот так везенье. Вы делаете ещё один ход!!';
				$new_move_a = 0;
			}
			else
			if ( $rid == 2 )
			{
				$add_money = 1;
			}
			else
			if ( $rid == 3 )
			{
				$hint = 'Повезло так повезло, вы передвигаетесь на одну плиту вперед!!';
				$new_player_pos += 1;
			}
			else
			if ( $rid == 4 )
			{
				$hint = 'Повезло так повезло, вы передвигаетесь на две плиты вперед!!';
				$new_player_pos += 2;
			}
			else
			if ( $rid == 5 )
			{
				$hint = 'Причиной любой удачи считай Богов, а не себя. (с) Биант';
			}
			else
			if ( $rid == 6 )
			{
				$hint = 'Везение - это удачи, к которым непричастен испытующий разум. (с) Аристотель';
			}
		}
		else
		if ( $cact[$player_pos] == 2 )
		{
			if ( $move_count == 2 )
			{
				$new_move_count = 1;
				$new_move_a = 0;
			}
			else
			{
				$new_move_a = 2;
			}
			if ( $rid == 1 )
			{
				$portal = 1;
			}
			else
			if ( $rid == 2 )
			{
				$add_money = -1;
			}
			else
			if ( $rid == 3 )
			{
				$hint = 'Какая неудача, Вы делаете шаг назад.';
				$new_player_pos -= 1;
			}
			else
			if ( $rid == 4 )
			{
				$hint = 'Какая неудача, Вы делаете два шага назад.';
				$new_player_pos -= 2;
			}
			else
			if ( $rid == 5 )
			{
				$hint = 'Неудача - не преступление; преступно ставить перед собой цели ниже своих возможностей.';
			}
			else
			if ( $rid == 6 )
			{
				$hint = 'Неудача - это тоже удача, но не твоя.';
			}
			else
			if ( $rid == 7 )
			{
				$hint = 'Ударив в грязь лицом, не говори, что жизнь - свинья.';
			}
		}
	}
	else
	if ( $cact[$player_pos] == 3 )
	{
		if ( $move_count == 2 )
		{
			$new_move_count = 1;
			$new_move_a = 0;
		}
		else
		{
			$new_move_a = 2;
		}
  		$hint = 'Вот это да, ветер подбросил Вас на один шаг вперед!';
  		$new_player_pos += 1;
	}
	else
	if ( $cact[$player_pos] == 4 )
	{
		$new_move_a = 2;
		$new_move_count = 3 - $new_move_count;
  		$hint = 'Какая досада, здесь придется провести ещё ход и смотреть, как оборотень убегает.';
	}
}
else
if ( $move_a == 2 )
{
	//enemy move
	$aa = array( );
	for ( $i = 0; $i < 3; ++ $i )
	{
		if ( ( ( 1 << ( $i + 7 ) ) & $amask ) )
		{
			$aa[] = $i + 1;
		}
	}
	$n = count( $aa );
	if ( $n == 0 )
	{
		f_MQuery( "UNLOCK TABLES" );
		RaiseError( 'No moves for enemy' );
	}
	$rid = mt_rand( ) % $n;
	$new_enemy_pos += $aa[$rid];
	$new_move_a = 3;
}
else
if ( $move_a == 3 )
{
	if ( $cact[$enemy_pos] == 1 || $cact[$enemy_pos] == 2 )
	{
		$aa = array( );
		for ( $i = 0; $i < 4; ++ $i )
		{
			if ( ( ( 1 << ( $i + 10 ) ) & $amask ) )
			{
				if ( $i == 1 )
				{
					$aa[] = 5;
					$aa[] = 6;
					if ( $cact[$enemy_pos] == 2 )
						$aa[] = 7;
				}
				$aa[] = $i + 1;
			}
		}
		$n = count( $aa );
		if ( $n == 0 )
		{
			f_MQuery( "UNLOCK TABLES" );
			RaiseError( 'No actions for enemy' );
		}
		$rid = $aa[mt_rand( ) % $n];
		if ( $cact[$enemy_pos] == 1 )
		{
			if ( $rid != 1 )
			{
				if ( $move_count == 2 )
				{
					$new_move_count = 1;
					$new_move_a = 2;
				}
				else
				{
					$new_move_a = 0;
				}
			}
			if ( $rid == 1 )
			{
				$new_move_a = 2;
			}
			else
			if ( $rid == 2 )
			{
			}
			else
			if ( $rid == 3 )
			{
				$new_enemy_pos += 1;
			}
			else
			if ( $rid == 4 )
			{
				$new_enemy_pos += 2;
			}
			else
			if ( $rid == 5 )
			{
			}
			else
			if ( $rid == 6 )
			{
			}
		}
		else
		if ( $cact[$enemy_pos] == 2 )
		{
			if ( $move_count == 2 )
			{
				$new_move_count = 1;
				$new_move_a = 2;
			}
			else
			{
				$new_move_a = 0;
			}
			if ( $rid == 1 )
			{
			}
			else
			if ( $rid == 2 )
			{
			}
			else
			if ( $rid == 3 )
			{
				$new_enemy_pos -= 1;
			}
			else
			if ( $rid == 4 )
			{
				$new_enemy_pos -= 2;
			}
			else
			if ( $rid == 5 )
			{
			}
			else
			if ( $rid == 6 )
			{
			}
			else
			if ( $rid == 7 )
			{
			}
		}
	}
	else
	if ( $cact[$enemy_pos] == 3 )
	{
		if ( $move_count == 2 )
		{
			$new_move_count = 1;
			$new_move_a = 2;
		}
		else
		{
			$new_move_a = 0;
		}
		$new_enemy_pos += 1;
	}
	else
	if ( $cact[$enemy_pos] == 4 )
	{
		$new_move_a = 0;
		$new_move_count = 3 - $new_move_count;
	}
}
else
{
	$nothing = true;
}

if ( $portal )
{
	$need_portal = 1;
	$hint = 'Тучи сгустились и Вас отбросило ветром. Мгновение и Вы уже в другом мире.';
	echo "setTimeout( 'moo( 0 );', 1000 );";
}

function to_char( $v )
{
	return chr( ord( '0' ) + $v );
}

$npy = $cy[$new_player_pos];
$npx = $cx[$new_player_pos];
$ney = $cy[$new_enemy_pos];
$nex = $cx[$new_enemy_pos];

$f[24] = to_char( $npy );
$f[25] = to_char( $npx );
$f[26] = to_char( $ney );
$f[27] = to_char( $nex );
$f[28] = to_char( $new_worlds );
$f[29] = to_char( $new_move_a );
$f[30] = to_char( $new_move_count );
$f[31] = to_char( $finished );
$f[32] = to_char( $need_portal );
$draw_f = $f[24] . $f[25] . $f[26] . $f[27];

$show = false;

if ( $new_player_pos != $player_pos || $new_enemy_pos != $enemy_pos )
{
	//draw
	if ( $new_player_pos != $player_pos )
	{
		$draw_f[0] = '.';
		$draw_f[1] = '.';
		$imgtype = 0;
		$npp = $new_player_pos;
		$pp = $player_pos;
	}
	else
	{
		$draw_f[2] = '.';
		$draw_f[3] = '.';
		$imgtype = 1;
		$npp = $new_enemy_pos;
		$pp = $enemy_pos;
	}
	if ( $npp > $pp )
	{
		$t = 0;
		for ( $i = $pp; $i < $npp; ++ $i )
		{
			$cury = $cy[$i];
			$curx = $cx[$i];
			$dir = $direction[$cury][$curx];
			echo "setTimeout( \"move( '$draw_f', '$hint', $cury, $curx, $dir, 1, $imgtype );\", $t );";
			$t += 666;
		}
		echo "setTimeout( \"moo( 0 );\", $t )";
	}
	else
	{
 		$t = 0;
		for ( $i = $pp; $i > $npp; -- $i )
		{
			$cury = $cy[$i];
			$curx = $cx[$i];
			$dir = ( $direction[$cy[$i - 1]][$cx[$i - 1]] + 2 ) % 4;
			echo "setTimeout( \"move( '$draw_f', '$hint', $cury, $curx, $dir, 1, $imgtype );\", $t );";
			$t += 666;
		}
		echo "setTimeout( \"moo( 0 );\", $t )";
	}
}
else
{
	$show = true;
}


f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );
f_MQuery( "UNLOCK TABLES" );

if ( $need_portal )
{
	if ( $worlds >= 0 && $worlds <= 2 )
	{
		$player->SetTrigger( 115 + $worlds );
	}
}

if ( $add_money )
{
	$cnt = $player->level * 20;
	if ( $add_money > 0 )
	{
		$player->AddMoney( $cnt );
		$hint = "Какая удача! Даже в астральном мире Вы умудрились найти $cnt монет!!";
	}
	else
	{
		if ( $player->money < $cnt )
			$cnt = $player->money;
		if ( $cnt > 0 )
		{
			$player->SpendMoney( $cnt );
			$hint = "Вот несчастье-то, Вы обронили $cnt монет в Астрал.";
		}
		else
			$hint = 'Какая неудача, у Вас нет монет, даже выронить нечего.';
	}
}

if ( $show )
{
	echo "out( '$draw_f', '$hint' );";
	if ( !$nothing )
		echo "setTimeout( \"moo( 0 );\", 600 )";
}

?>
