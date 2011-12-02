<?

include_once( 'locations/newarena/func.php' );
include_js( "js/skin2.js" );

?>

<div id='arena_holder'>
&nbsp;
</div>

<script>

var curRegime = 0;
function arenaRegime( a )
{
	if( a == 4 ) a = curRegime;
	query( "do.php?a="+a, "" );
}

function arenaAction( act, arg1, arg2, arg3, arg4, arg5 )
{
	query( "do.php?act=" + act + "&arg1=" + arg1 + "&arg2=" + arg2 + "&arg3=" + arg3 + "&arg4=" + arg4 + "&arg5=" + arg5, "" );
}

var scheduleTm = 0;
function arenaScheduleRef( )
{
	if( scheduleTm ) clearTimeout( scheduleTm );
	scheduleTm = setTimeout( 'arenaRegime( 4 )', 10000 );
}

arenaRegime( 0 );

</script>
