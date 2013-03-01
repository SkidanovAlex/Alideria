<?

if( !isset( $mid_php ) ) die( );

?>

<center><table><tr><td colspan=3><script>FLUl();</script><div style='position:relative;top:0px;left:0px;overflow:hidden;width:256px;height:256px;'><img id=rh width=384 height=256 src='lab_img_dev.php' style='position:absolute;left:-64px;top:0px;z-index:1;'><img id=rh2 style='position:absolute;left:0px;top:0px;z-index:2;' src=empty.gif></div><script>FLL();</script></td><td><div style='position:relative;top:0px;left:0px;overflow:hidden;width:256px;height:256px;'>
<span id=coords>&nbsp;</span>&nbsp;<span><img width=15 height=15 id=diri border=0></span><br>
<br>
<li><a href=# onclick='query_left();'>Повернуть Налево</a>
<li><a href=# onclick='query_go();'>Идти Прямо</a>
<li><a href=# onclick='query_right();'>Повернуть Направо</a><br>
<span id=addinfo>&nbsp;</span>
<br><br>
<small>Вы также можете использовать клавиши со стрелками для передвижения по лабиринту.</small>
<br><br>
<div id=lab_msg>
&nbsp;
</div>

</div>
</td></tr></table></center>
<script>

var step = 64;
var moo = 0;
var furl;

var id1 = 'rh';
var id2 = 'rh2';

var img = new Image( );
img.src = 'empty.gif';

function process_go( )
{
	step += 4;

	if( step > 128 )
	{
		clearInterval( moo ); moo = 0;
		var tmp = id1; id1 = id2; id2 = tmp;
    	document.getElementById( id1 ).style.width='384px';
    	document.getElementById( id1 ).style.height='256px';
    	document.getElementById( id1 ).style.left='-64px';
    	document.getElementById( id1 ).style.top='0px';
    	return;
	}

	document.getElementById( id2 ).style.width=step*3 + 'px';
	document.getElementById( id2 ).style.height=step*2 + 'px';
	document.getElementById( id2 ).style.left=(128-step*1.5)+'px';
	document.getElementById( id2 ).style.top=(128-step)+'px';
	document.getElementById( id2 ).style.opacity=(step>96)?( step - 96 ) / 32.0 :0;
	document.getElementById( id2 ).style.filter='alpha(opacity=' + ((step>96)?( step - 96 ) / 32.0*100 :0) + ')';
	document.getElementById( id1 ).style.width=step*6 + 'px';
	document.getElementById( id1 ).style.height=step*4 + 'px';
	document.getElementById( id1 ).style.left=(128-3*step)+'px';
	document.getElementById( id1 ).style.top=(128-2*step)+'px';

}

function do_go( )
{
	step = 64;

    document.getElementById( id2 ).src = img.src;
    setTimeout(function(){
        document.getElementById( id1 ).style.zIndex=1;
        document.getElementById( id2 ).style.zIndex=2;

        furl = "lab_img_dev.php?rnd="+Math.random();
        document.getElementById( id2 ).style.width='192px';
        document.getElementById( id2 ).style.height='128px';
        document.getElementById( id2 ).style.left='32px';
        document.getElementById( id2 ).style.top='64px';
        document.getElementById( id2 ).src=furl;
        document.getElementById( id2 ).style.opacity=0;
        document.getElementById( id2 ).style.filter='alpha(opacity=0)';
        if( moo ) clearInterval( moo );
        moo = setInterval( 'process_go()', 50 );
    }, 1);
}

function query_left( )
{
	query( 'lab_do_dev.php?do=left', 'a' );
}

function query_right( )
{
	query( 'lab_do_dev.php?do=right', 'a' );
}

function query_up( )
{
	query( 'lab_do_dev.php?do=up', 'a' );
}

function query_down( )
{
	query( 'lab_do_dev.php?do=down', 'a' );
}

function query_go( )
{
	query( 'lab_do_dev.php?do=go', 'a' );
}

function query_quest_attack( )
{
	query( 'lab_do_dev.php?do=quest_attack', 'a' );
}

function query_leave( )
{
	query( 'lab_do_dev.php?do=leave', 'a' );
}

function key_handler( e )
{
	if( moo ) return;
	e = e || event;
	code = e.keyCode?e.keyCode:e.charCode;
	if( code == 37 ) query_left( );
	if( code == 38 ) query_go( );
	if( code == 39 ) query_right( );
	return false;
}
function refr( )
{
	document.getElementById( id1 ).src='lab_img_dev.php?rnd='+Math.random();
}
function key_dummy( e )
{
	return false;
}

function showDir( a )
{
	var src='';
	if( a == 0 ) src = 'labl.png';
	if( a == 1 ) src = 'labb.png';
	if( a == 2 ) src = 'labr.png';
	if( a == 3 ) src = 'labt.png';

	_( 'diri' ).src = 'images/misc/' + src;
}

document.onkeydown = key_handler;
document.onkeypress = key_dummy;
document.onkeyup = key_dummy;

<?

include( "lab.php" ) ;
include( "lab_functions.php" );

$lab_id = isLabLoc( $player->location, $player->depth );
if( $lab_id == -1 )
	die( );

$lab_id = 1;

f_MQuery( "LOCK TABLES lab WRITE, player_labs WRITE" );
$res = f_MQuery( "SELECT cell_id, dir FROM player_labs WHERE player_id={$player->player_id} AND lab_id={$lab_id}" );
$arr = f_MFetch( $res );
if( !$arr )
{
    enterLab($lab_id);
	$dir = 0;
}
else $dir = $arr[1];
f_MQuery( "UNLOCK TABLES" );

echo "showDir( $dir );";

$res = f_MQuery( "SELECT x, y, z FROM player_labs, lab WHERE player_id={$player->player_id} AND player_labs.cell_id=lab.cell_id" );
$arr = f_MFetch( $res );
echo "document.getElementById( 'coords' ).innerHTML = 'Этаж: <b>$arr[z]</b>; Координаты: <b>$arr[x]x$arr[y]</b>';";

getNextStepInfo( $lab_id, $arr[x], $arr[y], $arr[z], $dir );

?>

</script>
