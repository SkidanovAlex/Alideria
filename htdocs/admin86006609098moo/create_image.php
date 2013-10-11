<?

function create_image_spell( $b )
{
	if( !file_exists( "../images/$b" ) )  return "empty.gif";
	$target = imagecreatetruecolor( 50, 50 );
	$im = imagecreatefromgif( "../images/items/svitok2.gif" );
	$it = imagecreatefrompng( "../images/$b" );
	list($width, $height) = getimagesize("../images/$b");
	imagecopy( $target, $im, 0, 0, 0, 0, 50, 50 );
	imagecopyresampled  ( $target  , $it  , 10 , 11  , 0  , 0  ,  25  ,  25  ,  $width  ,  $height  );
	$name = "img".mt_rand( 0, 2000000000 ).".gif";
	imagecolortransparent  ( $target, imagecolorallocate($target, 0, 0, 0) );
	imagegif($target,"../images/items/auto/$name");
	return $name;
}

function create_image_num( $num )
{
	$target = imagecreatetruecolor( 50, 50 );
	$im = imagecreatefromgif( "../images/items/svitok2.gif" );
	imagecopy( $target, $im, 0, 0, 0, 0, 50, 50 );
	$fonts= array("PALADPCR.TTF", "MTCORSVA.TTF", "WEDTXTN.TTF", "gothic.ttf", "cour.ttf", /*"georgia.ttf",*/ "impact.ttf", "latha.ttf", "mangal.ttf", "tahoma.ttf", "times.ttf", "trebuc.ttf", "tunga.ttf", "verdana.ttf");
    $qnFonts = 2; //count($fonts)-1;

    $color = imagecolorallocate($target, 5, 5, 5);

    $pathFonts = "/srv/www/alideria/htdocs/fonts/";

    $fontName = $pathFonts . $fonts[0];

	$rot = 0;
    $size = 14;

    // Вычисляем смещение для позиционирования цифры в цента своего места
    $arR = imagettfbbox($size, $rot, $fontName, $num);

    $xC = ($arR[4] + $arR[0]) / 2;
    $yC = ($arR[5] + $arR[1]) / 2;

    $xU = $arR[4];
    $yU = $arR[5];
    //////////////////////////

    $x = 25 - ($xU - $xC);
    $y = 25 - ($yU - $yC);
    imagettftext($target, $size, $rot, $x, $y, $color, $fontName, $num);
    
	$name = "img".mt_rand( 0, 2000000000 ).".gif";
	imagecolortransparent  ( $target, imagecolorallocate($target, 0, 0, 0) );
	imagegif($target,"../images/items/auto/$name");
	return $name;
}

function create_image_item( $b )
{
	if( !file_exists( "../images/$b" ) )  
	{
		echo ">>>$b---<br>";
		return "empty.gif";
	}
	$target = imagecreatetruecolor( 50, 50 );
	$im = imagecreatefromgif( "../images/items/svitok1.gif" );
	$it = imagecreatefromgif( "../images/$b" );
	list($width, $height) = getimagesize("../images/$b");
	imagecopy( $target, $im, 0, 0, 0, 0, 50, 50 );
	imagecopyresampled  ( $target  , $it  , 13 , 14  , 0  , 0  ,  22  ,  22  ,  $width  ,  $height  );
	$name = "img".mt_rand( 0, 2000000000 ).".gif";
	imagecolortransparent  ( $target, imagecolorallocate($target, 0, 0, 0) );
	imagegif($target,"../images/items/auto/$name");
	return $name;
}

?>

