<?

if( !$mid_php ) die( );

include( "quest_scripts/phrase575_functions.php" );

echo "<div style='position:relative;top:0px;left:0px;'>";
echo "<div style='position:absolute; left:0px; top:0px; width:420px; height:335px;'>";
echo "<script>FLUl();</script>";
echo "<div style='position:relative; left:0px; top:0px; width:410px; height:325px;background-color:black'>";

f_MQuery( "LOCK TABLE mahjong WRITE" );
$data = f_MValue( "SELECT data FROM mahjong WHERE player_id={$player->player_id}" );
if( !$data )
{
	$data = "";
	for( $i = 1; $i <= 36; ++ $i )
		for( $j = 0; $j < 4; ++ $j )
			$data .= numToChr( $i );
	for( $i = 0; $i < 144; ++ $i )
	{
		$j = mt_rand( 0, $i );
		if( $i != $j )
		{
			$t = $data[$i];
			$data[$i] = $data[$j];
			$data[$j] = $t;
		}
	}
	f_MQuery( "INSERT INTO mahjong( player_id, data ) VALUES ( {$player->player_id}, '{$data}' )" );
}
f_MQuery( "UNLOCK TABLES" );

for( $i = 143; $i >= 0; -- $i )
{
	$l = $mh_coords[$i][0] * 2 + $mh_coords[$i][2] + 10;
	$t = - $mh_coords[$i][0] * 2 + $mh_coords[$i][1] + 10;
/*	echo "<div style='position:absolute;left:{$l}px;top:{$t}px;width:28px;height:40px;border:1px solid black'><small>{$i}<br>";
	foreach( $mh_right[$i] as $a )
		echo $a."<br>";
	echo "</small></div>";*/
	$img = chrToNum( $data[$i] );
	$st = mhGetElemHtml( $i );
	if( $st != '' )
	{
    	echo "<div id=mh_{$i} style='position:absolute;left:{$l}px;top:{$t}px;width:28px;height:40px;'>";
    	echo $st;
    	echo "</div>";
	}
	else echo "<div id=mh_{$i} style='position:absolute;left:{$l}px;top:{$t}px;width:28px;height:40px;display:none;'>&nbsp;</div>";
}

echo "<div id=mh_a style='display:none;position:absolute;left:0px;top:0px;'><img onclick='unhighlight();' src=images/misc/m/0.png width=36 height=48 border=0></div>";

echo "</div><script>FLL();</script>";
echo "</div>";
echo "<div style='position:absolute; left:430px; top:0px; width: 300px;text-align: justify'>";
echo "¬олшебна€ дудочка лежит в тайнике под горой камней.  амни очень т€желые, чтобы подн€ть их любым известным заклинанием. ќднако, если создать несложное плетение, и прикоснутьс€ им к двум камн€м с одинаковым символом на них, они моментально превращаютс€ в пыль. „тобы достать дудочку, вам надо оставить не более дес€ти камней. „тобы к камню можно было прикоснутьс€ плетением, он должен быть не придавлен ничем сверху и хот€ бы с одной из двух сторон (слева или справа) он должен быть ничем не заблокирован.<br>";
echo "<ul><li><a href='javascript:restart()'>Ќачать заново</a><li><a href='game.php?phrase=1209'>”йти, продолжить позже</a></ul>";
echo "</div>";
echo "</div>";

?>

<script>
var cur_highlighted = -1;
function unhighlight( )
{
	if( cur_highlighted == -1 ) return;
//	_( 'mh_' + cur_highlighted ).style.opacity = 1.0;
	_( 'mh_a' ).style.display = 'none';
	cur_highlighted = -1;
}
function highlight( a )
{
	unhighlight( );
	cur_highlighted = a;
//	_( 'mh_' + cur_highlighted ).style.opacity = 0.8;
	_( 'mh_a' ).style.left = ( parseInt( _( 'mh_' + a ).style.left ) - 4 ) + 'px';
	_( 'mh_a' ).style.top = ( parseInt( _( 'mh_' + a ).style.top ) - 4 ) + 'px';
	_( 'mh_a' ).style.display = '';
}
function mhclick( a )
{
	if( cur_highlighted == a ) unhighlight( );
	else if( cur_highlighted == -1 )
	{
		highlight( a );
	}
	else
	{
		query( "quest_scripts/phrase575_ajax.php?id1="+cur_highlighted+"&id2="+a, '' );
	}
}
function restart( )
{
	if( confirm( 'Ќачать заново?' ) )
		query("quest_scripts/phrase575_ajax.php?restart=1","");
}
<?
mhFinishCheck( );
?>
</script>
