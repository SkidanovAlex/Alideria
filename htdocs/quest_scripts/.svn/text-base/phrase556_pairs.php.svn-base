<?

if( !$mid_php ) die( );

echo "Перед вами 20 сундучков, в которых спрятаны по два экземпляра 10-ти разных ресурсов. Ищите одинаковые ресурсы";  if( !$player->HasTrigger( 95 ) ) echo ", потратьте на нахождение всех пар меньше 20-ти ходов, и вы получите щедрый подарок"; echo ".<br><br>";

echo "<table><tr><td><script>FLUl();</script>";

echo "<table>";

$id = 0;
for( $i = 0; $i < 4; ++ $i )
{
	echo "<tr>";
	for( $j = 0; $j < 5; ++ $j )
	{
		echo "<td>";
		echo "<table style='width:60px;height:60px;' background='images/misc/pairs_cell.jpg' border=0 cellspacing=0 cellpadding=0>";
		echo "<tr><td width=60 height=60>";
		echo "<div onclick='query(\"quest_scripts/phrase556_ajax.php\",\"$id\");' id=pr{$i}{$j}>&nbsp;</div>";
		echo "</td></tr>";
		echo "</table>";
		echo "</td>";
		++ $id;
	}
	echo "</tr>";
}

echo "</table>";

echo "Ходов потрачено: <span id=steps><b>--</b></span>";

echo "<script>FLL();</script></td><td valign=top><div id=txt>&nbsp;</div></td></tr></table>";

?>

<script>

var imgs = ['res/klukva.gif','res/cikoriy.gif','res/brusnika.gif','res/mushroom.gif','flo/romaska.gif','res/carrolit.gif','res/granat.gif','res/kedr_nut.gif','res/meteorit.gif','quest/torch.gif','res/titanit.gif'];
var pr_tmo = 0;

function out( s, m )
{
	if( pr_tmo ) clearTimeout( pr_tmo );
	var id = 0;
	for( var i = 0; i < 4; ++ i )
		for( var j = 0; j < 5; ++ j )
		{
			if( s.charAt( id ) == '.' ) _( 'pr' + i + '' + j ).innerHTML = '<center><img width=36 height=44 src="images/misc/qm.png"></center>';
			else
			{
				var v = parseInt( s.charAt(id) );
				_( 'pr' + i + '' + j ).innerHTML = '<center><img width=50 height=50 src=images/items/' + imgs[v] + '></center>';
			}
			++ id;
		}
	_( 'steps' ).innerHTML = '<b>' + s.substr(20) + '</b>';
	if( m ) _( 'steps' ).innerHTML = '<b>' + s.substr(20) + '</b>. <a href=game.php?phrase=1174>Выйти из игры</a>';
}

<?

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( $arr )
{
	$moo = 0;
	if( substr_count($arr['f'], '.') == 0 ) $moo = 1;
	echo "out( '".substr($arr['f'],20)."', $moo )";
}
else echo "out('....................0',0);";

?>

</script>
