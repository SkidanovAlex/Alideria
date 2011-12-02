<?

require_once( 'tournament_functions.php' );
include_js( 'js/skin.js' );

if( !$mid_php ) die( );

$tm = time( );

if( $_GET['past'] )
{
	$pg = (int)$_GET['p']; $st = $pg * 20; $en = $st + 20;
	echo "<center><br><a href=game.php>Назад к списку ближайших турниров</a><br>";
	$res = f_MQuery( "SELECT r.*, a.date, a.name, a.type FROM tournament_results as r INNER JOIN tournament_announcements as a ON r.tournament_id=a.tournament_id ORDER BY a.date DESC LIMIT $st, $en" );
	echo "<center><table><tr><td><script>FLUl();</script><table><tr><td align=center><b>Название турнира</b></td><td align=center><b>Победитель</b></td><td align=center><b>Второе место</b></td><td align=center><b>Третье место</b></td></tr>";
	while( $arr = f_MFetch( $res ) )
	{
		$plr = array( );
		$plr[0] = new Player( $arr['champion'] );
		$plr[1] = new Player( $arr['second_place'] );
		$plr[2] = new Player( $arr['third_place'] );

		if( $arr['date'] < 1249578001 ) echo "<tr><td align=center><script>FUlm();</script>[".date( "d.m.Y", $arr['date'] )."] $arr[name]<script>FL();</script></td>";
		else echo "<tr><td align=left><script>FUlm();</script>[".date( "d.m.Y", $arr['date'] )."] <a href=tournament_net.php?id=$arr[tournament_id] target=_blank>$arr[name]</a><script>FL();</script></td>";
		if( $arr['type'] == 2 )
		{
			echo "<td colspan=3 align=center><script>FUcm();</script><b>Турнир Орденов</b><script>FL();</script></td>";
		}
		else
		{
			for( $i = 0; $i < 3; ++ $i )
				echo "<td><script>FUcm();document.write( ".$plr[$i]->Nick( )." );FL();</script></td>";
		}
	    echo "</tr>";
	}
	echo "</table>";
	echo "<center>Страница: ";
	$pnum = (int)(( f_MValue( "SELECT count( tournament_id ) FROM tournament_results" ) - 1 ) / 20) + 1;
	for( $i = 0; $i < $pnum; ++ $i )
	{
		if( $i == $pg ) echo "<b>";
		else echo "<a href=game.php?past=1&p=$i>";
		echo $i + 1;
		if( $i == $pg ) echo "</b>";
		else echo "</a>";
		echo " &nbsp; ";
	}
	echo "</center>";
	echo "<script>FLL();</script></center>";
	return;
}


$res = f_MQuery( "SELECT * FROM tournament_announcements WHERE date > $tm OR status = 4 ORDER BY date" );
if( !f_MNum( $res ) ) echo "<br><i>В ближайшее время не пройдет ни одного турнира</i>";
else
{
	echo "<br><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><b>В ближайшее время пройдут следующие турниры:</b></td><td style=\"text-align: right; padding-right: 15px;\"><i><b><a href=/game.php?past=1>Посмотреть прошедшие турниры</a></b></i></td></tr></table><br />";
	while( $arr = f_MFetch( $res ) )
	{
		echo "<div id=\"t$arr[tournament_id]\" style=\"margin-bottom: 3px; margin-right: 15px;\">";
		echo getTournamentDesc( $arr, 0 );
		echo "</div>";
	}
}

?>

<script>
function tp(id)
{
	window.open('tournament_participants.php?id=' + id,'_blank','toolbar=no,status=no,menubar=no,scrollbars=yes,width=400,height=420,resizeble=no');
}
</script>
