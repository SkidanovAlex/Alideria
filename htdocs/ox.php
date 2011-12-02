<?

if( !$mid_php ) die( );

include( 'ox_functions.php' );
include_js( 'js/timer.js' );

?>

<br>
<div align=center style='position:relative;top:0px;left:0px;'>
<table style='position:relative;top:0px;left:0px;'><tr><td style='position:relative;top:0px;left:0px;'>
<div style='position:relative;top:0px;left:0px;'>

<img src=images/ox/field.jpg>
<div id=fld style='position:absolute;left:82px;top:17px;'>
</div>
<div align=center id=curt style='position:absolute;left:13px;top:275px;width:62px;height:23px'>
</div>

<div align=center style='position:absolute;left:13px;top:200px;width:62px;'>
<script>document.write( InsertTimer( 40, "<big><big>", "</big></big>", 0, 'query("ox_ref.php","");' ) );</script>
</div>

<div align=center style='position:absolute;left:13px;top:12px;width:62px;height:23px'>
<?

$res = f_MQuery( "SELECT * FROM ox WHERE p1={$player->player_id} OR p2={$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr ) die( );

$larr = f_MFetch( f_MQuery( "SELECT login FROM characters WHERE player_id={$arr[p1]}" ) );
if( $larr ) echo "<b>$larr[0]</b><br>";
else echo "<b>Глашатай</b><br>";
echo "<img src=images/ox/x.png>";
echo "<br><br>";
$larr = f_MFetch( f_MQuery( "SELECT login FROM characters WHERE player_id={$arr[p2]}" ) );
if( $larr ) echo "<b>$larr[0]</b><br>";
else echo "<b>Глашатай</b><br>";
echo "<img src=images/ox/o.png>";

?>
</div>

</div>
</td></tr></table>
</div>

<script>

function refr( f, x, t, won )
{
	var id = 0;
	var st = '';
	function $( a ) { st += a; }
	$( '<table cellspacing=0 cellpadding=0 border=0>' );
	for( var i = 0; i < 20; ++ i )
	{
		$( '<tr>' );
		for( var j = 0; j < 20; ++ j )
		{
			$( '<td ' );

			if( f.charAt( id ) == ' ' ) $( 'background=empty.gif style="width:15px;height:15px;cursor:pointer" onclick="'+"query( 'ox_ref.php?id="+id+"', '' )"+'">' );
			else $( 'style="width:15px;height:15px;" background=images/ox/' + f.charAt( id ) + '.png border=0>' );
			$( '<img src=empty.gif width=15 height=15></td>' );
			++ id;
        }
		$( '</tr>' );
	}

	$( '</table>' );

	if( won > 0 )
	{
		if( won == 5 ) _( 'curt' ).innerHTML = ( '<b>Ничья!</b><br><a href=ox_ref.php?leave=1>Выйти</a><br>' );
		else if( won == x + 1 ) _( 'curt' ).innerHTML = ( '<small><b>Вы победили!</b><br></small><a href=ox_ref.php?leave=1>Выйти</a><br>' );
		else _( 'curt' ).innerHTML = ( '<small><b>Вы проиграли!</b><br></small><a href=ox_ref.php?leave=1>Выйти</a><br>' );
	}

	else _( 'curt' ).innerHTML = ( ( x != t % 2 ) ? "<b>Ваш Ход</b>" : "<b>Ход<br>Оппонента</b>" );
/*	$( '</td><td valign=top style="width:250px">' );

	$( "Совершено ходов: <b>" + t + "</b><br>" );
	$( "Текущий ход: <b>" + ( ( x != t % 2 ) ? "ваш" : "оппонента" ) + "</b><br>" );

	if( won > 0 )
	{
		if( won == x + 1 ) $( '<b>Вы победили!</b> <a href=ox_ref.php?leave=1>Выйти</a><br>' );
		else $( '<b>Вы проиграли!</b> <a href=ox_ref.php?leave=1>Выйти</a><br>' );
	}

	$( '</td></tr></table>' );
*/
	_( 'fld' ).innerHTML = st;
}

<?

refr( $player->player_id );

?>

setInterval( "query( 'ox_ref.php', '' );", 5000 );

</script>
