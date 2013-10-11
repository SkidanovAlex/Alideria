<?

function create_stat_image($stat, $value) {
    $bg = imagecreatefromgif( "../images/icons/attributes/{$stat}.gif" );

    $im = imagecreate( 25,25 );
    imagealphablending( $im, true );



    imagecopy( $im, $bg, 0, 0, 0, 0, 20, 20 );

    $black = imagecolorallocate(  $im, 5, 5, 5 );
    $white = imagecolorallocate(  $im, 255, 255, 255 );

	$fonts= array("PALADPCR.TTF", "MTCORSVA.TTF", "WEDTXTN.TTF", "gothic.ttf", "cour.ttf", /*"georgia.ttf",*/ "impact.ttf", "latha.ttf", "mangal.ttf", "tahoma.ttf", "times.ttf", "trebuc.ttf", "tunga.ttf", "verdana.ttf");
    $qnFonts = 2; //count($fonts)-1;

    $pathFonts = "/srv/www/alideria/htdocs/fonts/";

    $fontName = $pathFonts . $fonts[0];

	$rot = 0;
    $size = ($value < 100) ? 8 : 6;
    $num = "+{$value}";

    $arR = imagettfbbox($size, $rot, $fontName, $num);
    imagettftext($im, $size, $rot, 1 + 24 - $arR[4] + $arr[0], 1 + 24, $white, $fontName, $num);
    imagettftext($im, $size, $rot, 24 - $arR[2] + $arr[0], 24, $black, $fontName, $num);

	$name = "img_stat_".mt_rand( 0, 2000000000 ).".gif";
	imagecolortransparent  ( $im, imagecolorallocate($im, 0, 0, 0) );
	imagegif($im,"../images/items/auto/$name");
	return $name;
}

