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

    if( isset( $_GET['buy'] ) )
    {
    	$id = (int)$_GET['buy'];
    	if( $id >= 0 && $id < 3 && $player->SpendMoney( 200 ) )
    	{
    		$arr = array( 'Воды', "Природы", "Огня" );
    		$player->AddItems( 12848 + $id );
    		$player->AddToLogPost( 12848 + $id, 1, 6, -1 );
		    echo "update_money( $player->money, $player->umoney );";
		    $player->UpdateWeightStr( false, '' );
		    echo "alert( 'Вы тратите 200 дублонов и покупаете Посох $arr[$id]' );";
    	}
    }
    else
    {
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
    	f_MQuery( "LOCK TABLE player_mines WRITE" );
    	$res = f_MQuery( "SELECT f, lost FROM player_mines WHERE player_id={$player->player_id}" );
    	$arr = f_MFetch( $res );
    	if( !$arr )
    	{
    		$lost = 0;
    		$f = '';
    		for( $i = 0; $i < 36; ++ $i ) $f .= ' ';
    		$f .= "0";
    		addStones( );
    		f_MQuery( "INSERT INTO player_mines( player_id, lost, f ) VALUES ( {$player->player_id}, 0, '$f' )" );
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
				RaiseError( "Попытка передвинуть шарик в линиях с или на неверную позицию.", "$from : $to" );
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
				f_MQuery( "UPDATE player_mines SET lost=$lost, f='{$f}{$score}' WHERE player_id={$player->player_id}" );
				echo "move( $from, $to, $lost, '$f', $score );";
			}
		}
		else
		{
			echo "redraw( $lost, '$f', $score );";
		}
	   	f_MQuery( "UNLOCK TABLES" );

		$target = $player->GetQuestValue( 36 );
		if( $score >= $target && $target > 0 )
		{
			$player->SetQuestValue( 36, 0 );
			// Зачаровали
			$t_lvl = $player->GetQuestValue( 33 );
			$t_ap = $player->GetQuestValue( 34 );
			$t_genre = $player->GetQuestValue( 35 );
			$player->DropItems( 12848 + $t_genre, 1 );
			$player->AddToLogPost( 12848 + $t_genre, -1, 31 );
			$item_id = copyItem( 12848 + $t_genre, true );
			f_MQuery( "UPDATE items SET charges_genre = $t_genre, charges_level = $t_lvl, charges_mk = $t_ap WHERE item_id=$item_id" );
			$player->AddItems( $item_id );
			$player->AddToLogPost( 12848 + $t_genre, 1, 31 );
			echo "alert( 'Вы набрали нужное количество очков и успешно зачаровали посох! При желании вы можете продолжить играть.' );";
		}
    }

	die( );
}


?>

<center><table width=100%><tr><td><script>FLUl();</script><table width=100%><colgroup><col width=170><col width=*><tbody><tr>
	<td height=100%><script>FUct();</script><b>Магазин</b><script>FL();</script></td>
	<td height=100%><script>FUct();</script><b>Мастерская</b><script>FL();</script></td>
</tr><tr>
	<td valign=top height=100%><script>FUlt();</script>
		<table>
		<?
		  $arr = array( 'Воды', "Природы", "Огня" );
		  $iarr = array( "water", "nature", "fire" );
		  for( $i = 0; $i < 3; ++ $i ) echo "<tr><td><img width=50 height=50 border=0 src=images/items/staffs/2{$iarr[$i]}.gif></td><td><b>Посох $arr[$i]</b><br><img width=11 height=11 src=images/money.gif> 200<br><a href='javascript:buyp($i)'>Купить</a></td></tr>";
		?>
		</table>
	<script>FL();</script></td>

	<td valign=top height=100%><script>FUlt();</script>
<?

function InsertSelector( $id, $arr, $callback = '' )
{
	echo "\n\n<script>var {$id}_a = ['".implode( $arr, "','" )."'];\n";
	echo "function {$id}_l_click() { -- $id; if( $id < 0 ) $id += {$id}_a.length; _( '{$id}_m' ).innerHTML = '<big><big><b>' + {$id}_a[$id] + '</b></big></big>'; $callback }";
	echo "function {$id}_r_click() { ++ $id; if( $id >= {$id}_a.length ) $id -= {$id}_a.length; _( '{$id}_m' ).innerHTML = '<big><big><b>' + {$id}_a[$id] + '</b></big></big>';  $callback }";
	echo "</script>\n";
	echo "<table><tr><td>&nbsp;<img onclick='{$id}_l_click();' src=images/rect/rarr.png style='cursor:pointer;top:5px;position:relative;'></td>";
	echo "<td><script>FUcm();</script><div style='width:50px;height:50px;'>";
		echo "<table width=50 height=50 cellspacing=0 cellpadding=0 border=0><tr><td width=50 height=50 align=center valign=middle id={$id}_m><big><big><b>$arr[0]</b></big></big></td></tr></table>";
	echo "</div><script>FL();</script></td><td>&nbsp;<img onclick='{$id}_r_click();' style='cursor:pointer;top:6px;position:relative;' src=images/rect/larr.png></td></tr></table>";
}

	$err = "";
	if( isset( $_GET['start'] ) && $player->regime == 0 )
	{
		$lvl = (int)$_GET['lvl'];
		$genre = (int)$_GET['genre'];
		$ap = (int)$_GET['ap'];
		if( $lvl < 0 || $lvl >= 24 || $genre < 0 || $genre >= 3 || $ap < 0 || $ap >= 4 ) RaiseError( "Возможна попытка подстановки параметров для четвертого этажа БТЗ", "$lvl : $genre : $ap" );

		if( $player->NumberItems( 12848 + $genre ) > 0 )
		{
    		$price = ($lvl+1)*1000*(1+$ap);
    		$score = 90+(1+$lvl)*5*(2+$ap);
    		if( $player->SpendMoney( $price ) )
    		{
    			$player->AddToLogPost( 0, -$price, 31 );
    			$player->SetRegime( 113 );
    			$player->SetQuestValue( 33, $lvl + 2 );
    			$player->SetQuestValue( 34, $ap + 1 );
    			$player->SetQuestValue( 35, $genre );
    			$player->SetQuestValue( 36, $score );
    		}
    		else $err = "У вас не хватает дублонов";
		} else $err = "У вас нет нужного посоха";
	}
	if( $player->regime == 113 && isset( $_GET['finish'] ) )
	{
		$player->SetRegime( 0 );
		f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
	}

	if( $player->regime == 0 )
	{
		echo "\n\n<script>var v_g = 0; var v_l = 0; var v_a = 0;\n";
		echo "function recalc( ) { \n";
		echo "	v_p = (v_l+1) * 1000 * (1+v_a);\n";
		echo "  v_s = 90 + (1+v_l) * 5 * (2+v_a);\n";
		echo "  _( 'price' ).innerHTML = '<img width=11 height=11 src=images/money.gif> <b>' + v_p + '</b>, <b>' + v_s + '</b> очков.';";
		echo "}\n";
		echo "</script>\n";
		echo "<br><center><b>Вы можете зачаровать посох для улучшения заклинания.</b><br><font color=darkred>$err</font><br>";
		echo "<table width=80%><colgroup><col width=33%><col width=33%><col width=*>";
		echo "<tr><td><script>FLUc();</script><b>Стихия</b><br>";
		$genrei = array( ); for( $i = 0; $i < 3; ++ $i ) $genrei[] = "<img width=50 height=50 src=images/items/staffs/2{$iarr[$i]}.gif>";
		InsertSelector( 'v_g', $genrei, "recalc();" );
		echo "<script>FLL();</script></td>";
		echo "<td><script>FLUc();</script><b>Уровень</b><br>";
		$lvls = array( ); for( $i = 2; $i <= 25; ++ $i ) $lvls[] = $i;
		InsertSelector( 'v_l', $lvls, "recalc();" );
		echo "<script>FLL();</script></td>";
		echo "<td><script>FLUc();</script><b>Улучшение</b><br>";
		InsertSelector( 'v_a', array( 1, 2, 3, 4 ), "recalc();" );
		echo "<script>FLL();</script></td>";
		echo "</tr></table>";
		echo "<span id=price></span> ";
		echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><button class=n_btn onclick='if( confirm( \"Вы готовы попытаться зачаровать посох? С вас будет снято \" + v_p + \" дублонов.\\nВам будет необходимо набрать \" + v_s + \" очков, чтобы зачаровать посох.\" ) ) location.href= \"game.php?start=1&lvl=\" + v_l + \"&genre=\" + v_g + \"&ap=\" + v_a;'>Зачаровать</button></td><td><img src=images/top/c.png></td></tr></table>";
		echo "<script>recalc();</script><br>";
	}
	else if( $player->regime == 113 )
	{
		echo "<table><tr><td><script>FLUl();</script><table cellspacing=0 cellpadding=0 style='border:1px solid black;'>";
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
var t_lvl = <?=$player->GetQuestValue( 33 );?>;
var t_ap = <?=$player->GetQuestValue( 34 );?>;
var t_genre = <?=$player->GetQuestValue( 35 );?>;
var score = 0;
var final_score = <?=$player->GetQuestValue( 36 );?>;
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
	var moo = '<br><li><a href="javascript:surrender()">Сдаться</a>';
	if( game_lost ) moo = '<br><b>Вы проиграли.</b><br><li><a href="game.php?finish=1">Выйти</a>';
	var arr = ['Вода', 'Природа', 'Огонь'];
	_( 'dscore' ).innerHTML = '<table><tr><td>Счет:</td><td><b>' + score + '</b></td></tr><tr><td>Цель:</td><td><b>' + final_score + '</b></td></tr><tr><td colspan=2>&nbsp;</td></tr><tr><td>Уровень:</td><td><b>' + t_lvl + '</b></td></tr><tr><td>Улучшение:</td><td><b>' + t_ap + '</b></td></tr><tr><td>Стихия:</td><td><b>' + arr[t_genre] + '</b></td></tr><tr><td colspan=2>' + moo + '</td></tr></table>';
}                                                                                                                                                         
function tile_click( id )
{
	if( pending ) return;
	if( cur_cell != -1 ) place( cur_cell, ff[cur_cell] );
	if( ff[id] != -1 && cur_cell != id ) cur_cell = id;
	else if( ff[id] == -1 && cur_cell != -1 )
	{
		pending = 1;
		query( "fourth_floor.php?from=" + cur_cell + "&to=" + id, '' );
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
	if( confirm( 'Вы уверены, что хотите сдаться? Деньги, потраченные на игру, возвращены не будут!' ) )
		location.href = 'game.php?finish=1';
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
		location.href='game.php';
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
query( 'fourth_floor.php', '' );
</script>

		<?
	}
	else
	{
		$player->SetRegime( 0 );
		RaiseError( "Неожиданный режим у игрока", "{$player->regime}" );
	}

?>
	<script>FL();</script></td>

</tr></table><script>FLL();</script></td></tr></table></center>

<script>
function buyp( id )
{
	query( "fourth_floor.php?buy=" + id, '' );
}
</script>
