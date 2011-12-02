<?

require_once("no_cache.php");
include_once("functions.php");
include_once("player.php");

$link = f_MConnect();

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$player_id = $player->player_id;

$myclr = 0;

function create_game( $a, $b, $money )
{
	$tm = time( );

	$res = f_MQuery( "SELECT id, player1, player2 FROM chess_opponents WHERE player1 IN ($a,$b ) OR player2 IN ($a,$b )" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr[1] != $a && $arr[1] != $b ) f_MQuery( "UPDATE player_waste SET regime=0 WHERE regime=2 AND player_id = $arr[1]" );
		if( $arr[2] != $a && $arr[2] != $b ) f_MQuery( "UPDATE player_waste SET regime=0 WHERE regime=2 AND player_id = $arr[2]" );

		f_MQuery( "DELETE FROM chess_tables WHERE game_id=$arr[0]" );
		f_MQuery( "DELETE FROM chess_opponents WHERE id=$arr[0]" );
	}

	f_MQuery( "INSERT INTO chess_opponents ( player1, player2, cur_turn, last_turn_made, empty_turns, status, ask_draw, money ) VALUES ( $a, $b, 0, $tm, 0, 0, 0, $money )" );
	$game_id = mysql_insert_id( );
	
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 0, 0, 1, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 0, 1, 1, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 0, 2, 1, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 0, 3, 1, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 0, 4, 1, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 0, 5, 1, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 0, 6, 1, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 0, 7, 1, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 1, 0, 0, 0, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 1, 0, 7, 0, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 2, 0, 1, 0, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 2, 0, 6, 0, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 3, 0, 2, 0, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 3, 0, 5, 0, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 4, 0, 3, 0, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 5, 0, 4, 0, 0 )" );

	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 1, 0, 6, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 1, 1, 6, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 1, 2, 6, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 1, 3, 6, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 1, 4, 6, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 1, 5, 6, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 1, 6, 6, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 0, 1, 7, 6, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 1, 1, 0, 7, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 1, 1, 7, 7, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 2, 1, 1, 7, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 2, 1, 6, 7, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 3, 1, 2, 7, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 3, 1, 5, 7, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 4, 1, 3, 7, 0 )" );
	f_MQuery( "INSERT INTO chess_tables VALUES ( $game_id, 5, 1, 4, 7, 0 )" );
	
	return $game_id;
}

function nick_by_id( $a )
{
	$plr = new Player( $a );
	return "<script>document.write( ".$plr->Nick()." );</script>";
}

function my_game_id( )
{
	global $player_id;
	global $myclr;
	
	$res = f_MQuery( "SELECT id FROM chess_opponents WHERE ( player1=$player_id OR player2=$player_id ) AND status < 2" );
	if( !mysql_num_rows( $res ) )
	{
		$res = f_MQuery( "SELECT max(id) FROM chess_opponents WHERE ( player1=$player_id OR player2=$player_id )" );
		if( !mysql_num_rows( $res ) )
			return -1;
	}
	$arr = mysql_fetch_array( $res );
	$res2 = f_MQuery( "SELECT player1 FROM chess_opponents WHERE id=$arr[0]" );
	$arr2 = mysql_fetch_array( $res2 );
	if( $arr2[player1] == $player_id ) $myclr = 0;
	else $myclr = 1;
		
	return $arr[0];
}

function in_game( )
{
	global $player_id;
	
	$res = f_MQuery( "SELECT id FROM chess_opponents WHERE ( player1=$player_id OR player2=$player_id ) AND status < 2" );
	if( mysql_num_rows( $res ) ) return 1;
	return 0;
}

function my_game_color( )
{
	global $myclr;
	
	return $myclr;
}

class game
{
	var $game_id;
	var $figs;
	var $clrs;
	var $virg;
	var $last_error;
	var $turn;
	var $empty_turns;
	var $status;
	var $ask_draw;
	var $last_turn_made;
		
	function game( $id )
	{
		$this->game_id = $id;
		
		if( $id == -1 )
		{
			$this->make_start_position( );
			return;
		}
		
		$res = f_MQuery( "SELECT * FROM chess_opponents WHERE id=$id" );
		$arr = mysql_fetch_array( $res );
		
		$this->turn = $arr[cur_turn];
		$this->empty_turns = $arr[empty_turns];
		$this->status = $arr[status];
		$this->ask_draw = $arr[ask_draw];
		$this->last_turn_made = $arr[last_turn_made];
		
		$res = f_MQuery( "SELECT * FROM chess_tables WHERE game_id=$id" );
		$this->figs = Array( );
		$this->clrs = Array( );
		$this->virg = Array( );
		for( $i = 0; $i < 8; ++ $i )
		{
			$this->figs[$i] = Array( );
			$this->clrs[$i] = Array( );
			$this->virg[$i] = Array( );
			for( $j = 0; $j < 8; ++ $j )
				$this->figs[$i][$j] = -1;
		}
		
		while( $arr = mysql_fetch_array( $res ) )
		{
			$this->figs[$arr[y]][$arr[x]] = $arr[fig_id];
			$this->clrs[$arr[y]][$arr[x]] = $arr[color];
			$this->virg[$arr[y]][$arr[x]] = $arr[moved];
		}
	}
	
	function compare( $gm )
	{
		$ok = 2;
		
		for( $i = 0; $i < 8; ++ $i )
			for( $j = 0; $j < 8; ++ $j )
			{				
				if( $this->figs[$i][$j] != $gm->figs[$i][$j] )
				{
					print( "q: $i $j ( {$this->figs[$i][$j]} : {$gm->figs[$i][$j]} )<br>" );
					return 0;
				}
				if( $this->figs[$i][$j] != -1 )
				{
					if( $this->clrs[$i][$j] != $gm->clrs[$i][$j] )
					{
						print( "c: $i $j<br>" );
						return 0;
					}
				}
			}
			
		return $ok;
	}
		
	function make_start_position( )
	{
		// Нужно для логов
		for( $i = 0; $i < 8; ++ $i )
			for( $j = 0; $j < 8; ++ $j )
			{
				if( $i == 1 || $i == 6 ) $this->figs[$i][$j] = 0;
				else if( $i == 0 || $i == 7 )
				{
					if( $j == 0 || $j == 7 ) $this->figs[$i][$j] = 1;
					if( $j == 1 || $j == 6 ) $this->figs[$i][$j] = 2;
					if( $j == 2 || $j == 5 ) $this->figs[$i][$j] = 3;
					if( $j == 3 ) $this->figs[$i][$j] = 5;
					if( $j == 4 ) $this->figs[$i][$j] = 4;
				}
				else $this->figs[$i][$j] = -1;
				
				if( $i <= 1 ) $this->clrs[$i][$j] = 0;
				else $this->clrs[$i][$j] = 1;
				$this->virg[$i][$j] = 1;
			}
	}
	
	function describe( )
	{
		for( $i = 0; $i < 8; ++ $i )
			for( $j = 0; $j < 8; ++ $j )
				if( $this->figs[$i][$j] != -1 )
				{
					$clr = 0;
					if( $this->clrs[$i][$j] )
						$clr = 1;
						
					print( "placefig( $i, $j, {$this->figs[$i][$j]}, $clr );" );
				}
	}
	
	function set_ask_draw( $a )
	{
		f_MQuery( "UPDATE chess_opponents SET ask_draw = $a WHERE id={$this->game_id}" );
		$this->ask_draw = $a;
	}
	
	function set_status( $a )
	{
		f_MQuery( "UPDATE chess_opponents SET status = $a WHERE id={$this->game_id}" );
		$this->status = $a;
	}
	
	function check_turn( $y, $x, $ny, $nx, $rok = 0 )
	{
		$fig_id = $this->figs[$y][$x];
		$clr = $this->clrs[$y][$x];
		$virgin = !$this->virg[$y][$x];
		$enemy = 1 - $clr;
		
		if( $fig_id == 0 ) // Пешка
		{
			if( $clr == 0 && $ny == $y + 1 && $nx == $x && $this->figs[$ny][$nx] == -1 ) return 1;
			if( $clr == 1 && $ny == $y - 1 && $nx == $x && $this->figs[$ny][$nx] == -1 ) return 1;
			if( $clr == 0 && $y == 1 && $ny == $y + 2 && $nx == $x && $this->figs[$ny][$nx] == -1 ) return 1;
			if( $clr == 1 && $y == 6 && $ny == $y - 2 && $nx == $x && $this->figs[$ny][$nx] == -1 ) return 1;
			if( $clr == 0 && $ny == $y + 1 && ( $nx == $x - 1 || $nx == $x + 1 ) && $this->figs[$ny][$nx] != -1 && $this->clrs[$ny][$nx] == $enemy ) return 1;
			if( $clr == 1 && $ny == $y - 1 && ( $nx == $x - 1 || $nx == $x + 1 ) && $this->figs[$ny][$nx] != -1 && $this->clrs[$ny][$nx] == $enemy ) return 1;
			
			// Взятие на подходе
			if( $ny == 2 ) $qy = 3;
			if( $ny == 5 ) $qy = 4;
			if( ( $ny == 2 || $ny == 5 ) && ( $nx == $x - 1 || $nx == $x + 1 ) && $this->figs[$qy][$nx] == 0 && $this->clrs[$qy][$nx] == $enemy && $this->virg[$qy][$nx] == 2 )
			{
				if( $rok == 1 )
				{
					// Пешку убьем через жопу :оО
					f_MQuery( "DELETE FROM chess_tables WHERE game_id={$this->game_id} AND x=$nx AND y=$qy" );
					f_MQuery( "INSERT INTO chess_logs ( game_id, turn_id, sx, sy, ex, ey, fig ) VALUES ( {$this->game_id}, {$this->turn}, $nx, $qy, $nx, $qy, 0 )" );
				}
				return 1;
			}
			
			$this->last_error = "Пешка так не ходит";
			return 0;
		}
		if( $fig_id == 2 ) // Лошадь
		{
			$dx = $nx - $x;
			$dy = $ny - $y;
			if( $dx < 0 ) $dx = - $dx;
			if( $dy < 0 ) $dy = - $dy;
			
			if( ( $dx == 1 && $dy == 2 ) || ( $dx == 2 && $dy == 1 ) )
				if( $this->figs[$ny][$nx] == -1 || $this->clrs[$ny][$nx] == $enemy )
					return 1;
			
			$this->last_error = "Эта фигура так не ходит";
			return 0;
		}
		else if( $rok != -1 && $fig_id == 5 && $virgin && ( $nx == $x - 2 || $nx == $x + 2 ) && $ny == $y ) // Рокировка
		{
			if( $nx == $x - 2 ) { $qx = $x - 4; $zx = $x - 1; $tx = $x - 3; }
			else                { $qx = $x + 3; $zx = $x + 1; $tx = $x + 2; }
			
			if( $this->figs[$ny][$zx] != -1 || $this->figs[$ny][$nx] != -1 || $this->figs[$ny][$tx] != -1 )
			{
				$this->last_error = "Эта фигура так не ходит";
				return 0;
			}
			if( $this->virg[$ny][$qx] )
			{
				$this->last_error = "Нельзя сделать Рокировку если Ладья уже ходила!";
				return 0;
			}
			if( $this->under_attack( $x, $y, $clr ) )
			{
				$this->last_error = "Нельзя делать Рокировку если король под ударом!";
				return 0;
			}
			if( $this->under_attack( $zx, $y, $clr ) )
			{
				$this->last_error = "Нельзя делать Рокировку через битое поле!";
				return 0;
			}
			if( $this->under_attack( $nx, $y, $clr ) )
			{
				$this->last_error = "Ход ставит короля под удар!";
				return 0;
			}
			
			if( $rok == 1 )
			{
				// Ладью переставим через жопу :оО
				f_MQuery( "DELETE FROM chess_tables WHERE game_id={$this->game_id} AND x=$zx AND y=$y" );
				f_MQuery( "DELETE FROM chess_tables WHERE game_id={$this->game_id} AND x=$qx AND y=$y" );
				f_MQuery( "INSERT INTO chess_tables VALUES ( {$this->game_id}, 1, $clr, $zx, $y, 1 )" );
				f_MQuery( "INSERT INTO chess_logs ( game_id, turn_id, sx, sy, ex, ey, fig ) VALUES ( {$this->game_id}, {$this->turn}, $qx, $y, $zx, $y, 1 )" );
			}
			return 1;
		}
		else if( $fig_id == 1 || $fig_id == 3 || $fig_id == 4 || $fig_id == 5 ) // Ладья, слон, ферзь, король - все одно
		{
			if( ( $fig_id != 3 && ( $x == $nx || $y == $ny ) ) || ( $fig_id != 1 && ( $x + $y == $nx + $ny || $x - $y == $nx - $ny ) ) )
			{
				$dx = 0;
				$dy = 0;
				if( $x < $nx ) $dx = 1;
				if( $x > $nx ) $dx = -1;
				if( $y < $ny ) $dy = 1;
				if( $y > $ny ) $dy = -1;
				
				$cx = $x + $dx; $cy = $y + $dy;
				
				if( $fig_id == 5 && ( $cx != $nx || $cy != $ny ) )
				{ // Король далеко не гуляет :)
					$this->last_error = "Эта фигура так не ходит";
					return 0;
				}
				
				while( $cx != $nx || $cy != $ny )
				{
					if( $this->figs[$cy][$cx] != -1 )
					{
						$this->last_error = "Эта фигура не может прыгать через другие фигуры";
						return 0;
					}
					$cx += $dx; $cy += $dy;
				}
				if( $this->figs[$ny][$nx] == -1 || $this->clrs[$ny][$nx] == $enemy )
					return 1;
			}
			$this->last_error = "Эта фигура так не ходит";
			return 0;
		}
		
		$this->last_error = "Неизвестная ошибка";
		return 0;
	}
	
	function check_fig( $y, $x, $dy, $dx, $fig, $clr )
	{
		$y += $dy;
		$x += $dx;
		
		while( 1 )
		{
			if( $x < 0 || $y < 0 || $x >= 8 || $y >= 8 ) break;
			
			if( ( $this->figs[$y][$x] == $fig || $this->figs[$y][$x] == 4 ) && $this->clrs[$y][$x] == $clr )
				return 1;
				
			else if( $this->figs[$y][$x] != -1 )
				return 0;
			
			$y += $dy;
			$x += $dx;
		}
		
		return 0;
	}
	
	function check_single_fig( $y, $x, $fig, $clr )
	{
		if( $x < 0 || $y < 0 || $x > 7 || $y > 7 )
			return 0;
			
		if( $this->figs[$y][$x] != $fig || $this->clrs[$y][$x] != $clr ) return 0;
		
		return 1;
	}
	
	function under_attack( $x, $y, $clr )
	{
		$enemy = 1 - $clr;
		$ok = 1;
		if( $this->check_fig( $y, $x, 1, 1, 3, $enemy ) ) $ok = 0;
		else if( $this->check_fig( $y, $x, -1, 1, 3, $enemy ) ) $ok = 0;
		else if( $this->check_fig( $y, $x, 1, -1, 3, $enemy ) ) $ok = 0;
		else if( $this->check_fig( $y, $x, -1, -1, 3, $enemy ) ) $ok = 0;
		else if( $this->check_fig( $y, $x, 1, 0, 1, $enemy ) ) $ok = 0;
		else if( $this->check_fig( $y, $x, 0, 1, 1, $enemy ) ) $ok = 0;
		else if( $this->check_fig( $y, $x, -1, 0, 1, $enemy ) ) $ok = 0;
		else if( $this->check_fig( $y, $x, 0, -1, 1, $enemy ) ) $ok = 0;
		
		else if( $this->check_single_fig( $y - 1, $x, 5, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y + 1, $x, 5, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y, $x - 1, 5, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y, $x + 1, 5, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y - 1, $x - 1, 5, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y + 1, $x - 1, 5, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y - 1, $x + 1, 5, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y + 1, $x + 1, 5, $enemy ) ) $ok = 0;

		else if( $this->check_single_fig( $y - 2, $x - 1, 2, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y + 2, $x - 1, 2, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y - 2, $x + 1, 2, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y + 2, $x + 1, 2, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y - 1, $x - 2, 2, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y + 1, $x - 2, 2, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y - 1, $x + 2, 2, $enemy ) ) $ok = 0;
		else if( $this->check_single_fig( $y + 1, $x + 2, 2, $enemy ) ) $ok = 0;
		
		else if( $clr == 0 && $y < 7 && $x < 7 && $this->figs[$y + 1][$x + 1] == 0 && $this->clrs[$y + 1][$x + 1] == $enemy ) $ok = 0;
		else if( $clr == 0 && $y < 7 && $x > 0 && $this->figs[$y + 1][$x - 1] == 0 && $this->clrs[$y + 1][$x - 1] == $enemy ) $ok = 0;
		else if( $clr == 1 && $y > 0 && $x < 7 && $this->figs[$y - 1][$x + 1] == 0 && $this->clrs[$y - 1][$x + 1] == $enemy ) $ok = 0;
		else if( $clr == 1 && $y > 0 && $x > 0 && $this->figs[$y - 1][$x - 1] == 0 && $this->clrs[$y - 1][$x - 1] == $enemy ) $ok = 0;
		
		if( !$ok )
			return 1;
		
		return 0;
	}
	
	function check_check( $clr )
	{
		$x = -1; $y = -1;
		
		for( $i = 0; $i < 8; ++ $i )
			for( $j = 0; $j < 8; ++ $j )
				if( $this->figs[$i][$j] == 5 && $this->clrs[$i][$j] == $clr )
				{
					$x = $j;
					$y = $i;
				}
				
		if( $x == -1 )
		{
			$this->last_error = "Э-э-э, а где король? :оО";
			return 0;
		}
		
		if( $this->under_attack( $x, $y, $clr ) )
		{
			$this->last_error = "Ход ставит короля под удар";
			return 0;
		}
		
		return 1;
	}
	
	function check_endshpiles_inner( $a, $b )
	{
		if( $a[3] == 1 && $b[3] == 1 && !$a[2] && !$b[2] ) return 1; // Кр+С - Кр+С
		
		if( $a[2] || $a[3] ) return 0;
		
		if( !$b[2] && $b[3] <= 1 ) return 1; // Кр - Кр + С
		if( !$b[3] && $b[2] <= 1 ) return 1; // Кр - Кр + К
		
		return 0;
	}
	
	function check_endshpiles( )
	{
		$w = Array( 0, 0, 0, 0, 0, 0 );
		$b = Array( 0, 0, 0, 0, 0, 0 );
		
		for( $i = 0; $i < 8; ++ $i )
			for( $j = 0; $j < 8; ++ $j )
				if( $this->figs[$i][$j] != -1 )
				{
					if( $this->clrs[$i][$j] == 0 )
						++ $w[$this->figs[$i][$j]];
					else
						++ $b[$this->figs[$i][$j]];
				}
				
		if( $w[0] || $w[1] || $w[4] ) return 0;
		if( $b[0] || $b[1] || $b[4] ) return 0;
				
		if( $this->check_endshpiles_inner( $w, $b ) ) return 1;
		if( $this->check_endshpiles_inner( $b, $w ) ) return 1;
		
		return 0;
	}
	
	function check_status( $clr )
	{
		$check = !$this->check_check( $clr );
		$noturn = 1;
		
		// Ассимптотика: 8 * 8 + ( 8 + 8 ) * ( 8 * 8 * 8 + 8 * 8 * 8 ) = O( 20К )
		// Это меньше чем 0.01 секунды :оО Теоретически :))
		
		for( $y = 0; $y < 8; ++ $y )
			for( $x = 0; $x < 8; ++ $x )
				if( $this->figs[$y][$x] != -1 && $this->clrs[$y][$x] == $clr )
					for( $ny = 0; $ny < 8; ++ $ny )
						for( $nx = 0; $nx < 8; ++ $nx )
							if( $this->check_turn( $y, $x, $ny, $nx, -1 ) )
							{
								$fig_id = $this->figs[$y][$x];
								$clr = $this->clrs[$y][$x];
								
								$old_fig_id = $this->figs[$ny][$nx];
								$old_clr = $this->clrs[$ny][$nx];
								
								$this->figs[$ny][$nx] = $fig_id;
								$this->clrs[$ny][$nx] = $clr;
								$this->figs[$y][$x] = -1;
								$this->clrs[$y][$x] = 0;
								
								if( $this->check_check( $clr ) ) $noturn = 0;
								
								$this->figs[$ny][$nx] = $old_fig_id;
								$this->clrs[$ny][$nx] = $old_clr;
								$this->figs[$y][$x] = $fig_id;
								$this->clrs[$y][$x] = $clr;
							}
							
		if( !$noturn && !$check ) return 0; // ОК
		if( !$noturn &&  $check ) return 1; // Шах
		if(  $noturn && !$check ) return 2; // Пат
		if(  $noturn &&  $check ) return 3; // Мат
	}
	
	function make_turn_safe( $y, $x, $ny, $nx, $fig )
	{
		print( "$y, $x, $ny, $nx, $fig\n" );
		$clr = $this->clrs[$y][$x];
		$this->figs[$y][$x] = -1;
		$this->figs[$ny][$nx] = -1;
		if( $y != $ny || $x != $nx )
		{
			$this->figs[$ny][$nx] = $fig;
			$this->clrs[$ny][$nx] = $clr;
		}
	}
	
	function make_turn( $y, $x, $ny, $nx, $make_fig = 0 )
	{
		if( $this->figs[$y][$x] == -1 ) return;
		if( $x < 0 || $y < 0 || $x >= 8 || $y >= 8 ) return;
		if( $x == $nx && $y == $ny ) return;
		
		if( !$this->check_turn( $y, $x, $ny, $nx, 1 ) )
		{
			print( "<script>alert( '{$this->last_error}'  );</script>" );
			return;
		}
		
		$fig_id = $this->figs[$y][$x];
		$clr = $this->clrs[$y][$x];
		
		$enemy = 1 - $clr;
		
		$old_fig_id = $this->figs[$ny][$nx];
		$old_clr = $this->clrs[$ny][$nx];
		
		$this->figs[$ny][$nx] = $fig_id;
		$this->clrs[$ny][$nx] = $clr;
		$this->figs[$y][$x] = -1;
		$this->clrs[$y][$x] = 0;
		
		if( !$this->check_check( $clr ) )
		{
			print( "<script>alert( '{$this->last_error}'  );</script>" );
			
			$this->figs[$ny][$nx] = $old_fig_id;
			$this->clrs[$ny][$nx] = $old_clr;
			$this->figs[$y][$x] = $fig_id;
			$this->clrs[$y][$x] = $clr;
			
			return;
		}
		
		if( $fig_id == 0 && ( $ny == 7 || $ny == 0 ) && !$make_fig )
		{
			print( "<script>window.open( 'chess_select_fig.php?x=$x&y=$y&nx=$nx&ny=$ny' );</script>" );
			return;
		}
		else if( $fig_id == 0 && ( $ny == 7 || $ny == 0 ) )
		{
			if( $make_fig < 1 || $make_fig > 4 )
				return;
			
			$fig_id = $make_fig;
		}

		// Убьем инфу о фигуре, которая ходила на прошлом ходу		
		f_MQuery( "UPDATE chess_tables SET moved = 1 WHERE game_id={$this->game_id} AND moved = 2" );

		f_MQuery( "DELETE FROM chess_tables WHERE game_id={$this->game_id} AND x=$x AND y=$y" );
		f_MQuery( "DELETE FROM chess_tables WHERE game_id={$this->game_id} AND x=$nx AND y=$ny" );
		f_MQuery( "INSERT INTO chess_tables VALUES ( {$this->game_id}, $fig_id, $clr, $nx, $ny, 2 )" );
		
		$status = $this->check_status( $enemy );

		if( $fig_id == 0 || $old_fig_id != -1 )
		{
			$this->empty_turns = 0;
			f_MQuery( "UPDATE chess_opponents SET empty_turns = 0 WHERE id={$this->game_id}" );
		}
		else
		{
			++ $this->empty_turns;
			f_MQuery( "UPDATE chess_opponents SET empty_turns = empty_turns + 1 WHERE id={$this->game_id}" );
		}
		
		if( $this->check_endshpiles( ) )
			$status = 4;
			
		$this->set_status( $status );
		if( $status == 3 )
		{
			include( "waste_stats.php" );
			$varr = f_MFetch( f_MQuery( "SELECT money, player1, player2 FROM chess_opponents WHERE id={$this->game_id}" ) );
			$money = $varr[0];
			if( $this->turn % 2 == 0 ) storeGame( 1, $varr[1], $varr[2], $money, $this->turn > 8 );
			if( $this->turn % 2 == 1 ) storeGame( 1, $varr[2], $varr[1], $money, $this->turn > 8 );
		}
		else if( $status == 2 || $status == 4 )
		{
			include( "waste_stats.php" );
			$varr = f_MFetch( f_MQuery( "SELECT money, player1, player2 FROM chess_opponents WHERE id={$this->game_id}" ) );
			$money = $varr[0];
			storeDraw( 1, $varr[2], $varr[1], $money );
		}

		$tm = time( );
		f_MQuery( "INSERT INTO chess_logs ( game_id, turn_id, sx, sy, ex, ey, fig ) VALUES ( {$this->game_id}, {$this->turn}, $x, $y, $nx, $ny, $fig_id )" );
		f_MQuery( "UPDATE chess_opponents SET last_turn_made=$tm, cur_turn = cur_turn + 1, ask_draw = 0 WHERE id={$this->game_id}" );
		++ $this->turn;
		
/*		print( "<script>\n" );
		print( "parent.removefig( $y, $x );\n" );
		print( "parent.removefig( $ny, $nx );\n" );
		print( "parent.placefig( $ny, $nx, $fig_id, $clr );\n" );
		print( "</script>\n" );*/
	}
};

?>
