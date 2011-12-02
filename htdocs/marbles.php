<?

if( !$mid_php )
{
	header("Content-type: text/html; charset=windows-1251");


	include( 'functions.php' );
	include( 'player.php' );

	f_MConnect( );
    if( !check_cookie( ) )
    	die( "Неверные настройки Cookie" );

    $player = new Player( $HTTP_COOKIE_VARS['c_id'] );

    	$p = array( );
    	function reachable( $from, $to )
    	{
    		global $p, $f;
    		if( $to == $from ) return true;
    		if( $f[$to] != ' ' ) return false;
    		if( $p[$to] ) return false;
    		$p[$to] = true;

    		if( $to >= 6 ) if( reachable( $from, $to - 6 ) ) return true;
    		if( $to < 30 ) if( reachable( $from, $to + 6 ) ) return true;
    		if( $to % 6 > 0 ) if( reachable( $from, $to - 1 ) ) return true;
    		if( $to % 6 < 5 ) if( reachable( $from, $to + 1 ) ) return true;
			return false;
    	}
    	function addStones( )
    	{
    		global $f, $lost;
    		$em = array( );
    		for( $i = 0; $i < strlen( $f ); ++ $i ) if( $f[$i] == ' ' )
    			$em[] = $i;
    		$moo = min( 3, count( $em ) );
    		for( $i = 0; $i < $moo; ++ $i )
    		{
    			$id = mt_rand( $i, count( $em ) - 1 );
    			$t = $em[$i];
    			$em[$i] = $em[$id];
    			$em[$id] = $t;
    			$f[$em[$i]] = ''.mt_rand( 0, 4 );
    		}
    		if( count( $em ) <= 3 ) $lost = 1;
    	}
    	function checkTable( )
    	{
    		global $f, $score;
    		$dx = array( 1, 0, 1, 1 );
    		$dy = array( 0, 1, 1, -1 );
    		$nf = '';
    		$ret = false;
    		for( $i = 0; $i < 36; ++ $i ) $nf .= $f[$i];
    		for( $i = 0; $i < 6; ++ $i )
    			for( $z = 0; $z < 6; ++ $z ) if( $f[$i * 6 + $z] != ' ' )
    			{
    				$ok = false;
    				for( $dir = 0; $dir < 4; ++ $dir )
    				{
    					for( $j = 1; ; ++ $j )
    					{
    						$x = $i + $dx[$dir] * $j;
    						$y = $z + $dy[$dir] * $j;
    						if( $x < 0 || $x >= 6 || $y < 0 || $y >= 6 || $f[$x * 6 + $y] != $f[$i * 6 + $z] ) break;
    					}
    					if( $j == 4 ) $score ++;
    					if( $j == 5 ) $score += 4; // + 1
    					if( $j == 6 ) $score += 15; // + 4 + 1
    					if( $j >= 4 )
    					{
           					for( $j = 1; ; ++ $j )
           					{
           						$x = $i + $dx[$dir] * $j;
           						$y = $z + $dy[$dir] * $j;
           						if( $x < 0 || $x >= 6 || $y < 0 || $y >= 6 || $f[$x * 6 + $y] != $f[$i * 6 + $z] ) break;
           						$nf[$x * 6 + $y] = ' ';
           					}
           					$ok = true;
           					$ret = true;
    					}
    				}
    				if( $ok ) $nf[$i * 6 + $z] = ' ';
    			}
    		$f = $nf;	
    		return $ret;
    	}
    	f_MQuery( "LOCK TABLE marbles WRITE" );
    	$res = f_MQuery( "SELECT f, lost FROM marbles WHERE player_id={$player->player_id}" );
    	$arr = f_MFetch( $res );
    	if( !$arr )
    	{
    		$lost = 0;
    		$f = '';
    		for( $i = 0; $i < 36; ++ $i ) $f .= ' ';
    		$f .= "0";
    		addStones( );
    		f_MQuery( "INSERT INTO marbles( player_id, lost, f ) VALUES ( {$player->player_id}, 0, '$f' )" );
    	}
    	else
    	{
    		$lost = $arr['lost'];
			$f = $arr['f'];
    	}
		$score = (int)substr( $f, 36 );
		$f = substr( $f, 0, 36 );
		if( isset( $_GET['from'] ) )
		{
			$from = (int)$_GET['from'];
			$to = (int)$_GET['to'];
			if( $from < 0 || $from >= 36 || $to < 0 || $to >= 36 )
				RaiseError( "Попытка передвинуть шарик в линиях (миниигры) с или на неверную позицию.", "$from : $to" );
			if( $lost || $f[$from] == ' ' || $f[$to] != ' ' ) echo "pending = false;";
			else if( !reachable( $from, $to ) ) echo "pending = false;";
			else
			{
				$f[$to] = $f[$from];
				$f[$from] = ' ';
	    		if( !checkTable( ) )
	    		{
					addStones( );
					if( checkTable( ) ) $lost = 0;
				}
				$atleastone = false;
				for( $i = 0; $i < 36; ++ $i ) if( $f[$i] != '.' ) $atleastone = true;
				if( !$atleastone ) addStones( );
				f_MQuery( "UPDATE marbles SET lost=$lost, f='{$f}{$score}' WHERE player_id={$player->player_id}" );
				echo "move( $from, $to, $lost, '$f', $score );";
			}
		}
		else
		{
			echo "redraw( $lost, '$f', $score );";
		}
	   	f_MQuery( "UNLOCK TABLES" );

	   	f_MQuery( "LOCK TABLE marble_top WRITE" );
	   	$res = f_MQuery( "SELECT count( player_id ) FROM marble_top WHERE player_id = {$player->player_id}" );
	   	$arr = f_MFetch( $res );
	   	if( $arr[0] ) f_MQuery( "UPDATE marble_top SET score=$score WHERE player_id={$player->player_id} AND score < $score" );
	   	else f_MQuery( "INSERT INTO marble_top ( player_id, score ) VALUES ( {$player->player_id}, $score )" );
	   	f_MQuery( "UNLOCK TABLES" );

	die( );
}

if( isset( $_GET['finish'] ) )
{
	f_MQuery( "DELETE FROM marbles WHERE player_id={$player->player_id}" );
	die( "<script>location.href='waste.php';</script>" );
}


		echo "<table><tr><td valign=top><script>FLUl();</script>";

		echo "<center><b>Топ 10</b></center>";
		echo "<table>";
		$res = f_MQuery( "SELECT * FROM marble_top ORDER BY score DESC limit 10" );
		$i = 0;
		while( $arr = f_MFetch( $res ) )
		{
			++ $i;
			$plr = new Player( $arr['player_id'] );
			echo "<tr><td><b>$i.</b></td><td><script>document.write( ".$plr->Nick( ).");</script></td><td align=right><b>$arr[score]</b></td></tr>";
		}                                                                                                                  

		$res = f_MQuery( "SELECT score FROM marble_top WHERE player_id={$player->player_id}" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
    		$record = $arr[0];
    		$res = f_MQuery( "SELECT count( player_id ) FROM marble_top WHERE score > $record" );
    		$arr = f_MFetch( $res );
    		if( $arr[0] > 9 )
    		{
    			++ $arr[0];
    			echo "<tr><td><b>$arr[0].</b></td><td><script>document.write( ".$player->Nick( ).");</script></td><td align=right><b>$record</b></td></tr>";
    		}               
    	}
    	echo "</table>";

		echo "<script>FLL();</script></td><td><script>FLUl();</script><table cellspacing=0 cellpadding=0 style='border:1px solid black;'>";
	    $id = 0;
        for( $i = 0; $i < 6; ++ $i )
        {
        	echo "<tr>";
        	for( $j = 0; $j < 6; ++ $j )
        	{
        		$b = "1px solid black";
        		$border = "";
        		if( $j != 5 ) $border .= "border-right: $b;";
        		if( $i != 5 ) $border .= "border-bottom: $b;";
        		echo "<td align=center valign=middle style='width:48px;height:48px;cursor:pointer;{$border}background-color:#e0c3a0;'>";
        		echo "<div style='position:relative;top:0px;left:0px;width:48px;height:48px;'><img id=td$id onclick='tile_click( $id );' src=empty.gif width=48 height=48></div>";
        		echo "</td>";
        		++ $id;
        	}
        	echo "</tr>";
        }
		echo "</table><script>FLL();</script></td><td width=150 valign=top>";
		echo "<div id=dscore>&nbsp;</div></td></tr></table>";
		?>
<script>
var score = 0;
var game_lost = 0;
var cur_cell = -1;
var ff = [];
var pending = 0;
var images = [];
var images2 = [];
var empty = new Image( ); empty.src='images/empty.gif';
for( var i = 0; i < 36; ++ i ) ff[i] = -1;
for( var i = 0; i < 5; ++ i )
{
	images[i] = new Image( );
	images[i].src = 'images/misc/b' + i + '.gif';
	images2[i] = new Image( );
	images2[i].src = 'images/misc/b' + i + 'a.gif';
}
function refr( )
{
	var moo = '<br><li><a href="javascript:surrender()">Начать заново</a>';
	if( game_lost ) moo = '<br><b>Вы проиграли</b><br><li><a href="waste.php?finish=1">Начать заново</a>';
	_( 'dscore' ).innerHTML = '<table><tr><td>Счет:</td><td><b>' + score + '</b></td></tr><tr><td colspan=2>' + moo + '</td></tr></table><br><table><tr><td width=200><small>Вы можете выйти из игры и продолжить в любое удобное вам время, текущая позиция сохранится</small></td></tr></table>';
}                                                                                                                                                         
function tile_click( id )
{
	if( pending ) return;
	if( cur_cell != -1 ) place( cur_cell, ff[cur_cell] );
	if( ff[id] != -1 && cur_cell != id ) cur_cell = id;
	else if( ff[id] == -1 && cur_cell != -1 )
	{
		pending = 1;
		query( "marbles.php?from=" + cur_cell + "&to=" + id, '' );
		cur_cell = -1;
	}
	else cur_cell = -1;
	if( cur_cell == id ) _( 'td' + id ).src = images2[ff[id]].src;
}
function place( id, s )
{
	if( s == ' ' || s == -1 ) { _( 'td' + id ).src = empty.src; ff[id] = -1; }
	else { ff[id] = s; _( 'td' + id ).src = images[s].src; }
}
function redraw( lost, f, _score )
{
	for( var i = 0; i < f.length; ++ i )
		place( i, f.charAt( i ) );

	score = _score;
	game_lost = lost;
	refr( );
}
function surrender( )
{
	if( confirm( 'Вы уверены, что хотите сдаться?' ) )
		location.href = 'waste.php?finish=1';
}
function move( from, to, a1, a2, a3 )
{
	var wh = ff[from];
	var q = [];
	var qf = [];
	var p = []; var qn = 0;
	var moo = -1;
	function add( id, fr )
	{
		if( p[id] ) return;
		if( ff[id] != -1 && id != from ) return;
		p[id] = 1;
		q[qn] = id;
		qf[qn] = fr;
        ++ qn;
	}
	add( to );
	for( var i = 0; i < qn; ++ i )
	{
		var id = q[i];
		if( id == from )
		{
			moo = i;
			break;
		}
		if( id >= 6 ) add( id - 6, i );
		if( id < 30 ) add( id + 6, i );
		if( id % 6 > 0 ) add( id - 1, i );
		if( id % 6 < 5 ) add( id + 1, i );
	}
	if( moo == -1 )
	{
		location.href='waste.php';
		return;
	}
	var iv = 0;
	function anim( )
	{
		place( q[moo], -1 );
		if( q[moo] == to ) { clearInterval( iv ); pending = 0; redraw( a1, a2, a3 ); return; }
		else moo = qf[moo];
		place( q[moo], wh );
	}
	iv = setInterval( anim, 100 );
}
refr( );
query( 'marbles.php', '' );
</script>
