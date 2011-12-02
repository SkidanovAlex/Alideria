<?

$cnames = array( "Белый", "Синий", "Зеленый", "Красный", "Голубой", "Салатовый", "Бордовый", "Желтый", "Пурпурный", "Золотистый", "Коричневый", "Черный", "Серебристый", "Оранжевый", "Неоднозначный", "Небесный" );
$cvals  = array( "white", "blue",  "green",   "red",     "aqua",    "lime",      "maroon",   "yellow", "purple",    "gold",       "#330000",    "black",  "silver",      "ff3300",    "#996600",       "9999ff" );
$extracts = array( 8032 => 1, 8034 => 1, 8035 => 1 );
$altar_transforms = array(  // 0 means color of extract, not white
	0 => array( 1, 2, 3 ),   // 0     // 4,5 or 6 also means lighter color of extract, not exact color
	1 => array( 4, 4, 4 ),   // 1
	2 => array( 4, 4, 4 ),   // 1
	3 => array( 4, 4, 4 ),   // 1
	4 => array( 7, 0, 0 ),   // 2
	5 => array( 0, 8, 0 ),   // 2
	6 => array( 0, 0, 9 ),   // 2
	7 => array( 0, 4, 9 ),   // 3
	8 => array( 0, 4, 10 ),  // 3
	9 => array( 0, 12, 8 ),  // 3
	10 => array( 11, 8, 4 ), // 4
	11 => array( 13, 4, 0 ), // 5
	12 => array( 0, 0, 15 ), // 4
	13 => array( 14, 0, 0 ), // 6
	14 => array( 0, 0, 0 ),  // 7
	15 => array( 0, 0, 0 ),  // 5

	100 => 0
);

$altar_attrs = array(
	0 => array( 101, 10 ),
	1 => array( 132, 2 ),
	2 => array( 142, 2 ),
	3 => array( 152, 2 ),
	4 => array( 132, 3 ),
	5 => array( 142, 3 ),
	6 => array( 152, 3 ),
	7 => array( 101, 10 ),
	8 => array( 223, 0.5 ),
	9 => array( 101, 20 ),
	10 => array( 500, 1 ),
	11 => array( 500, 3 ),
	12 => array( 222, 1 ),
	13 => array( 500, 5 ),
	14 => array( 555, 25 ),
	15 => array( 222, 2 )
);
         
function getExtractNum( $lvl )
{
	if( $lvl <= 3 ) return 1;
	if( $lvl <= 10 ) return $lvl - 2;
	return ( $lvl - 2 ) + ( $lvl - 10 );
}

function getFellId( $lvl )
{
	if( $lvl <= 2 ) return 87;
	if( $lvl <= 5 ) return 88;
	if( $lvl <= 9 ) return 89;
	if( $lvl <= 14 ) return 90;
	if( $lvl <= 20 ) return 91;
	return 92;
}

function getNatureReq( $color, $level )
{
	if( $color == 10 ) return ceil( $level / 2 );
	if( $color == 11 ) return $level;
	if( $color == 13 ) return $level * 2;
	return 0;
}

?>
