<?

include( 'no_cache.php' );

// передаем заголовок
Header("Content-Type: image/png");


$code=rand(1000,9999);

$text = $code . "";


///Обработка
ShowImage($text);


function ShowImage($text)
{
  // создаем дескриптор изображения и регистрируем цвета
  $im = imagecreatetruecolor(90, 40);
  $black = imagecolorresolve($im, 231, 180, 113);

  // выполняем "заливку" рисунка
  ImageFill($im, 0, 0, $black);

  // Шрифты для отображения цифр
  $fonts= array("PALADPCR.TTF", "MTCORSVA.TTF", "WEDTXTN.TTF", "gothic.ttf", "cour.ttf", /*"georgia.ttf",*/ "impact.ttf", "latha.ttf", "mangal.ttf", "tahoma.ttf", "times.ttf", "trebuc.ttf", "tunga.ttf", "verdana.ttf");
  $qnFonts = 2; //count($fonts)-1;

  // Путь к шрифтам. Если они находятся в системном каталоге - оставить пустым, иначе указать полный путь к ним
  $pathFonts = "/home/alideri2/public_html/fonts/";

  for ($i = 0; $i < 4; $i++ )
  {
      $colR = 222 + mt_rand(0, 10);
      $colG = 195 + mt_rand(0, 10);
      $colB = 161 + mt_rand(0, 10);

//    $colG = $colR;
//    $colB = $colR;

    $color = imagecolorresolve($im, $colR, $colG, $colB);

    $fontName = $pathFonts . $fonts[mt_rand(0, $qnFonts)];

	$rot = - $i * 20 + 30;

    $maxSize = 29;

    $size = mt_rand(29, $maxSize);

    $num = $text{$i};

    // Вычисляем смещение для позиционирования цифры в цента своего места
    $arR = imagettfbbox($size, $rot, $fontName, $num);

    $xC = ($arR[4] + $arR[0]) / 2;
    $yC = ($arR[5] + $arR[1]) / 2;

    $xU = $arR[4];
    $yU = $arR[5];
    //////////////////////////

    $coef = 40;
    $offsX = ($maxSize - $size) / ( ( Coin() ) ? ($coef) : (-$coef) );

    $offsY = ($maxSize - $size) / ( ( Coin() ) ? ($coef) : (-$coef) );


    $x = (15 + $i*20) - ($xU - $xC) + $offsX;
    $y = 20 - ($yU - $yC) + $offsY;
    if( $i == 1 || $i == 2 ) $y -= 5;

      $scolR = 166 + mt_rand(-10, 0);
      $scolG = 146 + mt_rand(-10, 0);
      $scolB = 120 + mt_rand(-10, 0);
	$color_shadow = imagecolorresolve($im, $scolR, $scolG, $scolB);

    imagettftext($im, $size, $rot, $x+2, $y+2, $color_shadow, $fontName, $num);
    imagettftext($im, $size, $rot, $x, $y, $color, $fontName, $num);
  }

  // "Зашумливаем" картинку, меняя каждый пиксель на небольшое смещение в цвете
  for($x = 0; $x < 90; $x++ )
  {
    for($y = 0; $y < 40; $y++ )
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

