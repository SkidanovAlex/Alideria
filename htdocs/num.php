<?

include_once( 'no_cache.php' );
include_once( "functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
// передаем заголовок
Header("Content-Type: image/png");

$player_id = $HTTP_COOKIE_VARS['c_id'];

$code=rand(1000,9999);

$text = $code . "";

f_MQuery( "LOCK TABLE player_num WRITE" );
f_MQuery( "DELETE FROM player_num WHERE player_id = $player_id" );
f_MQuery( "INSERT INTO player_num VALUES ( $player_id, $code )" );
f_MQuery( "UNLOCK TABLES" );


///Обработка
ShowImage($text);


function ShowImage($text)
{
  // создаем дескриптор изображения и регистрируем цвета
  $im = imagecreatetruecolor(50, 20);
  $black = imagecolorresolve($im, 231, 180, 113);

  // выполняем "заливку" рисунка
  ImageFill($im, 0, 0, $black);

  // Шрифты для отображения цифр
  $fonts= array("PALADPCR.TTF", "MTCORSVA.TTF", "WEDTXTN.TTF", "gothic.ttf", "cour.ttf", /*"georgia.ttf",*/ "impact.ttf", "latha.ttf", "mangal.ttf", "tahoma.ttf", "times.ttf", "trebuc.ttf", "tunga.ttf", "verdana.ttf");
  $qnFonts = 2; //count($fonts)-1;

  for ($i = 0; $i < 4; $i++ )
  {
      $colR = 222 + mt_rand(0, 10);
      $colG = 205 + mt_rand(0, 10);
      $colB = 171 + mt_rand(0, 10);

//    $colG = $colR;
//    $colB = $colR;

    $color = imagecolorresolve($im, $colR, $colG, $colB);

    $pathFonts = "/srv/www/alideria/htdocs/fonts/";

    $fontName = $pathFonts . $fonts[mt_rand(0, $qnFonts)];

	$rot = - $i * 16 + 24;

    $maxSize = 16;

    $size = mt_rand(16, $maxSize);

    $num = $text{$i};

    // Вычисляем смещение для позиционирования цифры в цента своего места
    $arR = imagettfbbox($size, $rot, $fontName, $num);

    $xC = ($arR[4] + $arR[0]) / 2;
    $yC = ($arR[5] + $arR[1]) / 2;

    $xU = $arR[4];
    $yU = $arR[5];
    //////////////////////////

    $x = (7 + $i*12) - ($xU - $xC);
    $y = 10 - ($yU - $yC);
    if( $i == 1 || $i == 2 ) $y -= 3;

      $scolR = 50 + mt_rand(-10, 0);
      $scolG = 50 + mt_rand(-10, 0);
      $scolB = 50 + mt_rand(-10, 0);
	$color_shadow = imagecolorresolve($im, $scolR, $scolG, $scolB);

    imagettftext($im, $size, $rot, $x+1, $y+1, $color_shadow, $fontName, $num);
    imagettftext($im, $size, $rot, $x, $y, $color, $fontName, $num);
  }

  // "Зашумливаем" картинку, меняя каждый пиксель на небольшое смещение в цвете
  for($x = 0; $x < 50; $x++ )
  {
    for($y = 0; $y < 20; $y++ )
    {
      $col = imagecolorat($im, $x, $y);// mt_rand(110, 140);//, mt_rand(0, 255), mt_rand(0, 255)

      $arcol = imagecolorsforindex($im, $col);
      $sub = mt_rand(-10, 10);

      imagesetpixel($im, $x, $y,
        imagecolorresolve($im, $arcol["red"] + $sub, $arcol["green"] + $sub, $arcol["blue"] + $sub ) );

    }
  }

  ImagePng($im);
  ImageDestroy($im);
}

function Coin()
{
  return (mt_rand(0, 99) % 2) == 0;
}

?> 

