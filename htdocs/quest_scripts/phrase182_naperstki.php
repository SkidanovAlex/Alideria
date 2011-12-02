<?

if( !$mid_php ) die( );

?>

<center>

<div style='width:301px;height:219px;position:relative;left:0px;top:0px;'>

<img width=301 height=219 src=images/misc/naperstok_field.jpg>

<img id=ball style='position:absolute;left:130px;top:140px;width:28px;height:28px;' src='images/misc/naperstok_ball.png'>

<img id=nap1 style='position:absolute;left:11px;top:20px;width:95px;height:129px;' src='images/misc/naperstok.png'>
<img id=nap3 style='position:absolute;left:195px;top:20px;width:95px;height:129px;' src='images/misc/naperstok.png'>
<img id=nap2 style='position:absolute;left:103px;top:20px;width:95px;height:129px;' src='images/misc/naperstok.png'>

<img onclick='doPlay()' id=play_btn style='cursor:pointer;position:absolute;left:230px;top:190px;width:52px;height:18px;' src='images/misc/play_naperstok.png'>
<img onclick='doFinish()' id=select_btn style='display:none;cursor:pointer;position:absolute;left:190px;top:189px;width:91px;height:18px;' src='images/misc/select_naperstok.png'>

<img id=arrow style='display:none;position:absolute;left:123px;top:130px;width:42px;height:47px;' src='images/misc/naperstok_arrow.png'>

</div>

<li><a href=game.php?phrase=524>Уйти</a>

<div id=good style='display:none'>
Вы угадали! <a href=game.php>Продолжить</a>
</div>

<div id=bad style='display:none'>
Вы не угадали. <a href=game.php>Продолжить</a>
</div>

</center>

<script>

var step = 0;
var iv;
var chosen = 1;
var correct = 0;

function doSelect( id )
{
	chosen = id;
	document.getElementById( 'arrow' ).style.left = ( 11 + 20 + id * 92 ) + 'px';
}

function select1( ) { doSelect( 0 ); }
function select2( ) { doSelect( 1 ); }
function select3( ) { doSelect( 2 ); }

function anim1()
{
	if( step <= 50 )
	{
		document.getElementById( 'ball' ).style.top = ( 140 - step ) + 'px';
	}
	else if( step <= 50 + 93 )
	{
		document.getElementById( 'nap1' ).style.left = ( 11 + step - 50 ) + 'px';
		document.getElementById( 'nap3' ).style.left = ( 195 - step + 50 ) + 'px';
	}
	else if( step < 50 + 93 + 93 )
	{
		document.getElementById( 'nap1' ).style.left = ( 103 - step + 50 + 93 ) + 'px';
		document.getElementById( 'nap3' ).style.left = ( 103 + step - 50 - 93 ) + 'px';
	}
	else 
	{
		clearInterval( iv );
		document.getElementById( 'select_btn' ).style.display = '';
		document.getElementById( 'arrow' ).style.display = '';
		document.getElementById( 'nap1' ).style.cursor = 'pointer';
		document.getElementById( 'nap2' ).style.cursor = 'pointer';
		document.getElementById( 'nap3' ).style.cursor = 'pointer';
		document.getElementById( 'nap1' ).onclick = select1;
		document.getElementById( 'nap2' ).onclick = select2;
		document.getElementById( 'nap3' ).onclick = select3;
	}

	step += 3;
}

function anim2()
{
	if( step >= 0 )
	{
		document.getElementById( 'ball' ).style.top = ( 140 - step ) + 'px';
	}
	else 
	{
		clearInterval( iv );
		if( correct == chosen ) document.getElementById( 'good' ).style.display = '';
		else document.getElementById( 'bad' ).style.display = '';
	}

	step -= 3;
}

function doPlay()
{
	step = 0;
	document.getElementById( 'play_btn' ).style.display = 'none';
	iv = setInterval( 'anim1()', 50 );
}

function selectCallBack( id )
{
	correct = id;
	step = 50;
	document.getElementById( 'select_btn' ).style.display = 'none';
	document.getElementById( 'arrow' ).style.display = 'none';
	document.getElementById( 'ball' ).style.left = ( 130 - 93 + 93 * id ) + 'px';
	iv = setInterval( 'anim2()', 50 );
}

function doFinish( )
{
	query( "quest_scripts/phrase182_ajax.php", "" + chosen );
}

</script>
