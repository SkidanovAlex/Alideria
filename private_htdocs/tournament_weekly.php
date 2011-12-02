<?

require_once("time_functions.php");


include( 'functions.php' );

f_MConnect( );

$t_day = 3600*24;
$t_hour = 3600;

$adds = array (
	$t_day * 4 + $t_hour * 15,
	$t_day * 4 + $t_hour * 20,
	$t_day * 5 + $t_hour * 15,
	$t_day * 5 + $t_hour * 20,
	$t_day * 6 + $t_hour * 15,
	$t_day * 6 + $t_hour * 20,
	$t_day * 7 + $t_hour * 15,
	$t_day * 7 + $t_hour * 20,
	$t_day * 8 + $t_hour * 15,
	$t_day * 8 + $t_hour * 20,
	$t_day * 9 + $t_hour * 15,
	$t_day * 9 + $t_hour * 20,
	$t_day * 10 + $t_hour * 15,
	$t_day * 10 + $t_hour * 20
);

$p = array( );

for( $lvl = 4; $lvl <= 20; ++ $lvl )
{
	$level = $lvl;

	$sql = new Sql( "tournament_ids" );
	$sql->fields = array( "id", "level" );
	$sql->get( "level=$lvl" );
	$sql->fetch( );
	$sql->arr['level'] = $lvl;
	++ $sql->arr['id'];
	$num = rome_number( $sql->arr['id'] );
	$sql->store( 'level' );

	do
	{
		$ai = mt_rand( 0, count( $adds ) - 1 );
		if( mt_rand( 1, 2 ) == 1 || $level == 14 ) $ai = (int)($ai / 2) * 2 + 1;
	} while( $p[$ai] );
	
	if( $level >= 15 ) $ai = 1;
	
//	$p[$ai] = 1;

	$date = time( ) + $adds[$ai];
	
	if( $level == 14 ) $date += 1;
	if( $level >= 15 ) $date -= $t_day - 1;
	

    f_MQuery( "INSERT INTO tournament_announcements ( name ) VALUES ( '$num Первенство Теллы среди $lvl-ых уровней' )" );
    $id = mysql_insert_id( );
    $mlvl = $lvl;
    if( $lvl == 20 ) $mlvl = 22;
    f_MQuery( "UPDATE tournament_announcements SET min_level=$lvl, max_level=$mlvl, prize={$lvl}000, date=$date WHERE tournament_id=$id" );
}

?>
