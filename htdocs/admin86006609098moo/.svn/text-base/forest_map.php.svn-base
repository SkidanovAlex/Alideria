<?

include( '../no_cache.php' );
include( '../functions.php' );

f_MConnect( );

include( 'admin_header.php' );

// передаем заголовок
Header("Content-Type: image/png");

$side = 6;

///Обработка
ShowImage();


function ShowImage()
{
	global $side;
  // создаем дескриптор изображения и регистрируем цвета
  $im = imagecreatetruecolor(100 * $side, 100 * $side);
  $clr[0] = imagecolorresolve($im, 0, 255, 0);
  $clr[1] = imagecolorresolve($im, 0, 128, 0);
  $clr[2] = imagecolorresolve($im, 0, 255, 255);
  $clr[3] = imagecolorresolve($im, 128, 0, 128);
  $clr[4] = imagecolorresolve($im, 128, 128, 128);
  $clr[5] = imagecolorresolve($im, 128, 128, 0);
  $clr[6] = imagecolorresolve($im, 0, 0, 255);
  $clr[7] = imagecolorresolve($im, 255, 255, 0);
  $clr[8] = imagecolorresolve($im, 255, 255, 255);
  $clr[9] = imagecolorresolve($im, 0, 0, 0);
  $clr[10] = imagecolorresolve($im, 255, 0, 0);

  // выполняем "заливку" рисунка
  ImageFill($im, 0, 0, $black);

    $tile = Array( );
    for( $i = 0; $i < 100; ++ $i )
    {
    	$tile[$i] = Array( );
    	for( $j = 0; $j < 100; ++ $j )
    		$tile[$i][$j] = 1;
    }
    
    $res = f_MQuery( "SELECT * FROM forest_tiles WHERE location = 1" );
    while( $arr = f_MFetch( $res ) )
    {
    	$x = $arr[depth] / 100;
    	$y = $arr[depth] % 100;
    	settype( $x, 'integer' );
    	$tile[$y][$x] = $arr[tile];
    }
    
    $tile[0][0] = 10;

    for( $i = 0; $i < 100; ++ $i )
    	for( $j = 0; $j < 100; ++ $j )
    	{
		    $x = ( $j + 50 ) % 100;
		    $y = ( $i + 50 ) % 100;
		    imagefilledrectangle( $im, $x * $side, $y * $side, $x * $side + $side, $y * $side + $side, $clr[$tile[$i][$j]] );
    	}

  ImagePng($im);
  ImageDestroy($im);
}

?> 

