<?

$pathFonts = "/home/alideri2/public_html/fonts/";

include_once( 'no_cache.php' );
include_once( "functions.php" );

f_MConnect( );

// передаем заголовок
Header("Content-Type: image/png");

$player_id = $_GET['player_id'];
settype( $player_id, 'integer' );

$res = f_MQuery( "SELECT * FROM player_profs WHERE player_id = $player_id" );
$num = f_MNum( $res );

$colors = Array(
	Array( 0, 255, 0 ),
	Array( 255, 0, 0 ),
	Array( 255, 255, 180 ),
	Array( 0, 255, 255 ),
	Array( 230, 160, 190 ),
	Array( 255, 255, 0 ),
	Array( 255, 0, 255 ),
	Array( 150, 255, 90 ),
	Array( 150, 90, 255 ),
	Array( 255, 90, 150 ),
	Array( 255, 180, 255 ),
	Array( 90, 255, 150 ),
	Array( 90, 150, 255 ),
	Array( 180, 255, 255 ),
	Array( 0, 0, 255 ),
 );
 
 $w = 100;
  for( $i = 0; $i < $num; ++ $i )
  {
  	$arrs[$i] = f_MFetch( $res );
  	$len = log( 1 + $arrs[$i]['value'] / 4000 ) * 70 / log( 4 ) + 120.9;
  	$w = max( $w, $len );
  }
  $res = f_MQuery( "SELECT exp as value FROM characters WHERE player_id = $player_id" );
  $arr = f_MFetch( $res );
  $arrs[$num ++] = $arr;

  // создаем дескриптор изображения и регистрируем цвета
  $h = $num * 24 + 8;
  $im = imagecreatetruecolor($w, $h );
  $bgcolor = imagecolorresolve($im, 231, 180, 113);
  $black = imagecolorresolve($im, 0, 0, 0);
  
  // выполняем "заливку" рисунка
  ImageFill($im, 0, 0, $bgcolor);
  
  for( $i = 0; $i < $num; ++ $i )
  {
  	$arr = $arrs[$i];
  	$clr = imagecolorresolve($im, $colors[$i % 15][0], $colors[$i % 15][1], $colors[$i % 15][2]);
  	$clr_top = imagecolorresolve($im, $colors[$i % 15][0] * 0.85, $colors[$i % 15][1] * 0.85, $colors[$i % 15][2] * 0.85);
  	$clr_right = imagecolorresolve($im, $colors[$i % 15][0] * 0.7, $colors[$i % 15][1] * 0.7, $colors[$i % 15][2] * 0.7);
  	$len = log( 1 + $arr['value'] / 4000 ) * 70 / log( 4 ) + 0.9;
  	settype( $len, 'integer' );
  	imagefilledrectangle( $im, 0, $h - 24 * ( $i + 1 ), $len, $h - 24 * $i - 1, $clr );
  	for( $j = 0; $j < 8; ++ $j )
  	{
  		imageline( $im, $j + 1, $h - 24 * ( $i + 1 ) - $j - 1, $len + $j + 1, $h - 24 * ( $i + 1 ) - $j - 1, $clr_top );
  		imageline( $im, $len + $j + 1, $h - 24 * ( $i + 1 ) - $j, $len + $j + 1, $h - 24 * ( $i ) - $j - 2, $clr_right );
  	}
    imagettftext($im, 10, 0, $len + 4, $h - 24 * ( $i ) - 12, $black, $pathFonts."MTCORSVA.TTF", $arr['value']);
  }	
  
  ImagePng($im);
  ImageDestroy($im);


?> 

