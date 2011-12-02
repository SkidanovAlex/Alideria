<?

function mooClr( $a )
{
	if( $a < 0 ) return 0;
	if( $a > 255 ) return 255;
	return $a;
}

function sqrClr( $a ) { return abs( $a ); }

function checkClr( $r, $g, $b, $rr, $gg, $bb )
{
	$a = sqrClr( $r - $rr );
	$b = sqrClr( $g - $gg );
	$c = sqrClr( $b - $bb );
	if( $a + $b < 30 && $c < 90 || $a + $c < 30 && $b < 90 || $b + $c < 30 && $a < 90 ) return true;
	return false;
}

if( isset( $_GET['r'] ) )
{
	$r = mooClr( (int)$_GET['r'] );
	$g = mooClr( (int)$_GET['g'] );
	$b = mooClr( (int)$_GET['b'] );
	$s = sprintf( "%X%X%X%X%X%X", floor($r/16), $r % 16, floor($g/16), $g % 16, floor($b/16), $b % 16 );
	
	if( $player->player_id != 173 && checkClr( $r, $g, $b, 255, 255, 255 ) ) echo "<script>alert( 'Цвет слишком похож на белый. Нельзя купить белый цвет.' );</script>";
	else if( checkClr( $r, $g, $b, 226, 201, 168 ) ) echo "<script>alert( 'Цвет слишком похож на фон чата. Выберите другой цвет.' );</script>";
	else if( checkClr( $r, $g, $b, 229, 176, 107 ) ) echo "<script>alert( 'Цвет слишком похож на фон игры. Выберите другой цвет.' );</script>";
	else if( !$player->SpendUMoney( 125 ) ) echo "<script>alert( 'У вас недостаточно талантов.' );</script>";
	else
	{
		f_MQuery( "UPDATE characters SET nick_clr = '$s' WHERE player_id={$player->player_id}" );
		$player->nick_clr = $s;
		$player->UploadInfoToJavaServer( );
		$player->AddToLogPost( -1, -125, 21, 1000, 5 );
	}
}


?>

<table width=600><tr><td>
<script>FUct();</script>
<table width=100%><tr><td align=left>
<b>Предпросмотр:</b>
<table><tr><td><script>FUlt();</script>
<div id=prev1 style='color:#<?=$player->nick_clr ?>'><b><?=$player->login ?></b></div>
<script>FL();</script>
</td><td>
<script>FLUl();</script>
<div id=prev2 style='color:#<?=$player->nick_clr ?>'><b><?=$player->login ?></b></div>
<script>FLL();</script></td></tr></table>

</td><td align=right>

<b>Стоимость: </b><img src='images/umoney.gif' width=11 height=11> <b>125</b><br>
<button onclick='location.href="game.php";' class='n_btn'>Назад</button>
<button onclick='alert("Вы не выбрали никакой цвет");' id='btnChangeColor' class='n_btn'>Купить</button>

</td></tr></table>

<br>
<button class=n_btn onclick='mode(1)'>Обычные</button> <button class=n_btn onclick='mode(2)'>Палитра</button> <button class=n_btn onclick='mode(3)'>RGB</button><br>
<div id='pane1' style='display:none'>
	<table><tr><td><script>FLUl();</script>
		<div id='pane1_1'>&nbsp;</div>
	<script>FLL();</script></td></tr></table>
</div>
<div id='pane2' style='display:none'>
	<table><tr><td><script>FLUl();</script>
		<div id='pane2_1'>&nbsp;</div>
	<script>FLL();</script></td><td><script>FLUl();</script><div id='pane2_2'>&nbsp;</div><script>FLL();</script></td></tr></table>
</div>
<div id='pane3' style='display:none'>
<br>
<table><tr><td><script>FLUl();</script>
<table>
<tr><td>Красный:</td><td><input id='v_red' value='0' onkeyup='rgbProcess()'></td><td id='t_red' style='background-color:black;height:25px;width:50px;'>&nbsp;</td></tr>
<tr><td>Зеленый:</td><td><input id='v_green' value='0' onkeyup='rgbProcess()'></td><td id='t_green' style='background-color:black;height:25px;width:50px;'>&nbsp;</td></tr>
<tr><td>Синий:</td><td><input id='v_blue' value='0' onkeyup='rgbProcess()'></td><td id='t_blue' style='background-color:black;height:25px;width:50px;'>&nbsp;</td></tr>
<tr><td colspan=3>&nbsp;</td></tr>
<tr><td>Итог:</td><td>&nbsp;</td><td id='t_res' style='background-color:black;height:25px;width:50px;'>&nbsp;</td></tr>
</table><script>FLL();</script></td></tr></table>
</div>
<div id='pane4'>
<br>
<b>Обычные</b> - выбор из ограниченного набора цветов.<br>
<b>Палитра</b> - выбор цвета на палитре. Выберите оттенок, а затем интенсивность.<br>
<b>RGB</b> - выбор цвета путем указания его компонент.<br>
</div>
<script>FL();</script>
</td></tr></table>

<script>

mode = function(a) {
	for( var i = 1; i <= 3; ++ i ) {
		_( 'pane' + i ).style.display = ( ( i == a ) ? '' : 'none' );
	}
}

toHexSingle = function(a) {
	if( isNaN( a ) ) a = 0;
	a = Math.floor( a );
	var str = '0123456789ABCDEF';
	if( a > 255 ) a = 255;
	if( a < 0 ) a = 0;
	return str.charAt( Math.floor( a / 16 ) ) + "" + str.charAt( a % 16 );
}

toHex = function(r, g, b) {
	return toHexSingle( r ) + toHexSingle( g ) + toHexSingle( b );
}

createTable = function() {
	var table = document.createElement( 'table' );
	table.cellSpacing = 0;
	table.cellPadding = 0;
	for( var i = 0; i < 50; ++ i )
	{
		var g = i * 255 / 50;
		var tr = document.createElement( 'tr' );
		for( var j = 0; j < 50; ++ j )
		{
			var r = 255;
			var b = 255 * j / 49;
			var td = document.createElement( 'td' );
			td.style.width = '4px';
			td.style.height = '4px';
			td.style.backgroundColor = '#' + toHex( r, g, b );
			td.onclick = ( function(r,g,b) { return function() { createRightTable( r, g, b ); } } )( r, g, b )
			tr.appendChild( td );
		}
		for( var j = 0; j < 50; ++ j )
		{
			var r = 255 * (49 - j) / 49;
			var b = 255;
			var td = document.createElement( 'td' );
			td.style.width = '4px';
			td.style.height = '4px';
			td.style.backgroundColor = '#' + toHex( r, g, b );
			td.onclick = ( function(r,g,b) { return function() { createRightTable( r, g, b ); } } )( r, g, b )
			tr.appendChild( td );
		}
		table.appendChild( tr );
	}
	for( var i = 49; i >= 0; -- i )
	{
		var g = 255;
		var tr = document.createElement( 'tr' );
		for( var j = 0; j < 50; ++ j )
		{
			var r = 255 * i / 49;
			var b = 255 * j / 49 * i / 49;
			var td = document.createElement( 'td' );
			td.style.width = '4px';
			td.style.height = '4px';
			td.style.backgroundColor = '#' + toHex( r, g, b );
			td.onclick = ( function(r,g,b) { return function() { createRightTable( r, g, b ); } } )( r, g, b )
			tr.appendChild( td );
		}
		for( var j = 0; j < 50; ++ j )
		{
			var r = 255 * (49 - j) / 49 * i / 49;
			var b = 255 * i / 49;
			var td = document.createElement( 'td' );
			td.style.width = '4px';
			td.style.height = '4px';
			td.style.backgroundColor = '#' + toHex( r, g, b );
			td.onclick = ( function(r,g,b) { return function() { createRightTable( r, g, b ); } } )( r, g, b )
			tr.appendChild( td );
		}
		table.appendChild( tr );
	}
	_( 'pane2_1' ).innerHTML = '';
	_( 'pane2_1' ).appendChild( table );
	createRightTable( 255, 255, 255 );
}

createRightTable = function(rr,gg,bb)
{
	var table = document.createElement( 'table' );
	table.cellSpacing = 0;
	table.cellPadding = 0;
	for( var i = 99; i >= 0; -- i )
	{
		var tr = document.createElement( 'tr' );
		var r = rr * i / 99;
		var b = bb * i / 99;
		var g = gg * i / 99;
		var td = document.createElement( 'td' );
		td.style.width = '20px';
		td.style.height = '4px';
		td.style.backgroundColor = '#' + toHex( r, g, b );
		td.onclick = ( function(r,g,b) { return function() { setColor( r, g, b ); } } )( r, g, b )
		tr.appendChild( td );
		table.appendChild( tr );
	}
	_( 'pane2_2' ).innerHTML = '';
	_( 'pane2_2' ).appendChild( table );
}

setColor = function( r,g,b ) {
	r = Math.floor( r );
	g = Math.floor( g );
	b = Math.floor( b );
	_( 'btnChangeColor' ).onclick = function() { if( confirm( 'Сменить цвет ника на выбранный за 125 талантов?\nУбедитесь, что слева в предпросмотре вы видите тот цвет, который хотите купить.' ) ) location.href='game.php?nick_clr=1&r=' + r + '&g=' + g + '&b=' + b; }
	_( 'prev1' ).style.color = '#' + toHex( r, g, b );
	_( 'prev2' ).style.color = '#' + toHex( r, g, b );
}

rgbProcess = function() {
	r = Math.floor( parseInt( _( 'v_red' ).value ) );
	g = Math.floor( parseInt( _( 'v_green' ).value ) );
	b = Math.floor( parseInt( _( 'v_blue' ).value ) );
	_( 'btnChangeColor' ).onclick = function() { if( confirm( 'Сменить цвет ника на выбранный за 125 талантов?\nУбедитесь, что слева в предпросмотре вы видите тот цвет, который хотите купить.' ) ) location.href='game.php?nick_clr=1&r=' + r + '&g=' + g + '&b=' + b; }
	_( 'prev1' ).style.color = '#' + toHex( r, g, b );
	_( 'prev2' ).style.color = '#' + toHex( r, g, b );
	_( 't_res' ).style.backgroundColor = '#' + toHex( r, g, b );
	_( 't_red' ).style.backgroundColor = '#' + toHex( r, 0, 0 );
	_( 't_green' ).style.backgroundColor = '#' + toHex( 0, g, 0 );
	_( 't_blue' ).style.backgroundColor = '#' + toHex( 0, 0, b );
}

var clrs = [
'#FF0000',
'#00FFFF',
'#0000FF',
'#0000A0',
'#FF0080',
'#800080',
'#FFFF00',
'#00FF00',
'#FF00FF',
'#FFFFFF',
'#C0C0C0',
'#808080',
'#000000',
'#FF8040',
'#804000',
'#800000',
'#808000',
'#408080',
'#000000',
'#150517',
'#250517',
'#2B1B17',
'#302217',
'#302226',
'#342826',
'#34282C',
'#382D2C',
'#3b3131',
'#3E3535',
'#413839',
'#41383C',
'#463E3F',
'#4A4344',
'#4C4646',
'#4E4848',
'#504A4B',
'#544E4F',
'#565051',
'#595454',
'#5C5858',
'#5F5A59',
'#625D5D',
'#646060',
'#666362',
'#696565',
'#6D6968',
'#6E6A6B',
'#726E6D',
'#747170',
'#736F6E',
'#616D7E',
'#657383',
'#646D7E',
'#6D7B8D',
'#4C787E',
'#4C7D7E',
'#806D7E',
'#5E5A80',
'#4E387E',
'#151B54',
'#2B3856',
'#25383C',
'#463E41',
'#151B8D',
'#15317E',
'#342D7E',
'#2B60DE',
'#306EFF',
'#2B65EC',
'#2554C7',
'#3BB9FF',
'#38ACEC',
'#357EC7',
'#3090C7',
'#25587E',
'#1589FF',
'#157DEC',
'#1569C7',
'#153E7E',
'#2B547E',
'#4863A0',
'#6960EC',
'#8D38C9',
'#7A5DC7',
'#8467D7',
'#9172EC',
'#9E7BFF',
'#728FCE',
'#488AC7',
'#56A5EC',
'#5CB3FF',
'#659EC7',
'#41627E',
'#737CA1',
'#737CA1',
'#98AFC7',
'#F6358A',
'#F6358A',
'#E4317F',
'#F52887',
'#E4287C',
'#C12267',
'#7D053F',
'#CA226B',
'#C12869',
'#800517',
'#7D0541',
'#7D0552',
'#810541',
'#C12283',
'#E3319D',
'#F535AA',
'#FF00FF',
'#F433FF',
'#E238EC',
'#C031C7',
'#B048B5',
'#D462FF',
'#C45AEC',
'#A74AC7',
'#6A287E',
'#8E35EF',
'#893BFF',
'#7F38EC',
'#6C2DC7',
'#461B7E',
'#571B7e',
'#7D1B7E',
'#842DCE',
'#8B31C7',
'#A23BEC',
'#B041FF',
'#7E587E',
'#D16587',
'#F778A1',
'#E56E94',
'#C25A7C',
'#7E354D',
'#B93B8F',
'#F9B7FF',
'#E6A9EC',
'#C38EC7',
'#D2B9D3',
'#C6AEC7',
'#EBDDE2',
'#C8BBBE',
'#E9CFEC',
'#FCDFFF',
'#E3E4FA',
'#FDEEF4',
'#C6DEFF',
'#ADDFFF',
'#BDEDFF',
'#E0FFFF',
'#C2DFFF',
'#B4CFEC',
'#B7CEEC',
'#52F3FF',
'#00FFFF',
'#57FEFF',
'#50EBEC',
'#4EE2EC',
'#48CCCD',
'#43C6DB',
'#9AFEFF',
'#8EEBEC',
'#78c7c7',
'#46C7C7',
'#43BFC7',
'#77BFC7',
'#92C7C7',
'#AFDCEC',
'#3B9C9C',
'#307D7E',
'#3EA99F',
'#82CAFA',
'#A0CFEC',
'#87AFC7',
'#82CAFF',
'#79BAEC',
'#566D7E',
'#6698FF',
'#736AFF',
'#CFECEC',
'#AFC7C7',
'#717D7D',
'#95B9C7',
'#5E767E',
'#5E7D7E',
'#617C58',
'#348781',
'#306754',
'#4E8975',
'#254117',
'#387C44',
'#4E9258',
'#347235',
'#347C2C',
'#667C26',
'#437C17',
'#347C17',
'#348017',
'#4AA02C',
'#41A317',
'#4AA02C',
'#8BB381',
'#99C68E',
'#4CC417',
'#6CC417',
'#52D017',
'#4CC552',
'#54C571',
'#57E964',
'#5EFB6E',
'#64E986',
'#6AFB92',
'#B5EAAA',
'#C3FDB8',
'#00FF00',
'#87F717',
'#5FFB17',
'#59E817',
'#7FE817',
'#8AFB17',
'#B1FB17',
'#CCFB5D',
'#BCE954',
'#A0C544',
'#FFFF00',
'#FFFC17',
'#FFF380',
'#EDE275',
'#EDDA74',
'#EAC117',
'#FDD017',
'#FBB917',
'#E9AB17',
'#D4A017',
'#C7A317',
'#C68E17',
'#AF7817',
'#ADA96E',
'#C9BE62',
'#827839',
'#FBB117',
'#E8A317',
'#C58917',
'#F87431',
'#E66C2C',
'#F88017',
'#F87217',
'#E56717',
'#C35617',
'#C35817',
'#8A4117',
'#7E3517',
'#7E2217',
'#7E3117',
'#7E3817',
'#7F5217',
'#806517',
'#805817',
'#7F462C',
'#C85A17',
'#C34A2C',
'#E55B3C',
'#F76541',
'#E18B6B',
'#F88158',
'#E67451',
'#C36241',
'#C47451',
'#E78A61',
'#F9966B',
'#EE9A4D',
'#F660AB',
'#F665AB',
'#E45E9D',
'#C25283',
'#7D2252',
'#E77471',
'#F75D59',
'#E55451',
'#C24641',
'#FF0000',
'#F62217',
'#E41B17',
'#F62817',
'#E42217',
'#C11B17',
'#FAAFBE',
'#FBBBB9',
'#E8ADAA',
'#E7A1B0',
'#FAAFBA',
'#F9A7B0',
'#E799A3',
'#C48793',
'#C5908E',
'#B38481',
'#C48189',
'#7F5A58',
'#7F4E52',
'#7F525D',
'#817679',
'#817339',
'#827B60',
'#C9C299',
'#C8B560',
'#ECD672',
'#ECD872',
'#FFE87C',
'#ECE5B6',
'#FFF8C6',
'#FAF8CC'
];

function fromHex(c)
{
	if( c == '0' ) return 0;
	if( c == '1' ) return 1;
	if( c == '2' ) return 2;
	if( c == '3' ) return 3;
	if( c == '4' ) return 4;
	if( c == '5' ) return 5;
	if( c == '6' ) return 6;
	if( c == '7' ) return 7;
	if( c == '8' ) return 8;
	if( c == '9' ) return 9;
	if( c == 'A' ) return 10;
	if( c == 'B' ) return 11;
	if( c == 'C' ) return 12;
	if( c == 'D' ) return 13;
	if( c == 'E' ) return 14;
	if( c == 'F' ) return 15;
	return 0;
}

var table = document.createElement( 'table' );
table.cellSpacing = 0;
table.cellPadding = 0;
var id = 0;
for( var i = 0; i < 15; ++ i )
{
	var tr = document.createElement( 'tr' );
	for( var j = 0; j < 21; ++ j )
	{
		var r = fromHex( clrs[id].charAt( 1 ) ) * 16 + fromHex( clrs[id].charAt( 2 ) );
		var g = fromHex( clrs[id].charAt( 3 ) ) * 16 + fromHex( clrs[id].charAt( 4 ) );
		var b = fromHex( clrs[id].charAt( 5 ) ) * 16 + fromHex( clrs[id].charAt( 6 ) );
		var td = document.createElement( 'td' );
		td.style.width = '20px';
		td.style.height = '20px';
		td.style.backgroundColor = '#' + toHex( r, g, b );
		td.onclick = ( function(r,g,b) { return function() { setColor( r, g, b ); } } )( r, g, b )
		tr.appendChild( td );
		++ id;
	}
	table.appendChild( tr );
}
	_( 'pane1_1' ).innerHTML = '';
	_( 'pane1_1' ).appendChild( table );

createTable( );

</script>
