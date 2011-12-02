<?

$clrs = array(
'2B60DE',
'408080',
'FF05D5',
'D36008',
'347235',
'CA226B',
'8B31C7',

'B38481',
'FFFF00',
'F665AB');

if( isset( $_GET['r'] ) )
{
	$r = (int)$_GET['r'];
	
	$price = 3;
	if( $r >= 7 ) $price = 10;
	if( $r >= 0 && $r < 10 && $player->SpendUMoney( $price ) )
	{
		$s = $clrs[$r];
		f_MQuery( "UPDATE characters SET nick_clr = '$s' WHERE player_id={$player->player_id}" );
		$player->nick_clr = $s;
		$player->UploadInfoToJavaServer( );
		$player->AddToLogPost( -1, -$price, 21, 1000, 5 );
	} else echo "<script>alert( 'Не хватает талантов.' );</script>";
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

<b>Стоимость: </b><img src='images/umoney.gif' width=11 height=11> <span id=prc><b>3</b></span><br>
<button onclick='location.href="game.php";' class='n_btn'>Назад</button>
<button onclick='alert("Вы не выбрали цвет!");' id='btnChangeColor' class='n_btn'>Купить</button>

</td></tr></table>

<br>
<div id='pane1'>
	<table><tr><td><script>FLUl();</script>
		<div id='pane1_1'>&nbsp;</div>
	<script>FLL();</script></td></tr></table>
</div>
<br>

<?
if( $player->umoney >= 100 )
{
?>
Для самых состоятельных магов Фавн может предложить любой цвет, даже если его нет в списке.<br>
<a href='game.php?nick_clr=1'>Перейти в режим покупки произвольного цвета</a>
<?
}
?>
<script>FL();</script>
</td></tr></table>

<script>

var price;

setColor = function( id ) {
	price = 3;
	if( id >= 7 ) price = 10;
	_( 'prc' ).innerHTML = '<b>' + price + '</b>';
	_( 'btnChangeColor' ).onclick = function() { if( confirm( 'Сменить цвет ника на выбранный за ' + price + ' талантов?\nУбедитесь, что слева в предпросмотре вы видите тот цвет, который хотите купить.' ) ) location.href='game.php?nick_clr=2&r=' + id; }
	_( 'prev1' ).style.color = '#' + clrs[id];
	_( 'prev2' ).style.color = '#' + clrs[id];
}

var clrs = [
'2B60DE',
'408080',
'FF05D5',
'D36008',
'347235',
'CA226B',
'8B31C7',

'B38481',
'FFFF00',
'F665AB'
];

var table = document.createElement( 'table' );
table.cellSpacing = 0;
table.cellPadding = 0;
for( var i = 0; i < 1; ++ i )
{
	var tr = document.createElement( 'tr' );
	for( var j = 0; j < 10; ++ j )
	{
		var td = document.createElement( 'td' );
		td.style.width = '20px';
		td.style.height = '20px';
		td.style.backgroundColor = '#' + clrs[j];
		td.onclick = ( function(id) { return function() { setColor( id ); } } )( j )
		tr.appendChild( td );
	}
	table.appendChild( tr );
}
	_( 'pane1_1' ).innerHTML = '';
	_( 'pane1_1' ).appendChild( table );

</script>
