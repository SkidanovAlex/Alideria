<?

$manacost = Array( 3, 0, 6, 16, 7, 3, 14, 4, 0, 24, 0, 3, 15, 2, 1, 3, 17, 14, 7, 4, 5, 5, 3, 9, 2, 6, 0, 18, 2, 18, 8, 6, 25, 0, 0, 11, 1, 8, 3, 14, 11, 13, 6, 1, 4, 6, 6, 3, 17, 9, 18, 3, 15, 5, 4, 7, 12, 10, 5, 2, 16, 2, 21, 8, 13, 4, 7, 4, 4, 10, 8, 2, 14, 5, 3, 0, 12, 3, 6, 5, 17, 3, 0 );

$cards = Array(
	"Акведук",
	"Альтруизм",
	"Бешенство",
	"Благодать",
	"Вредители",
	"Всплеск",
	"Господство",
	"Диверсия",
	"Доброе Утро",
	"Дух Воды",
	"Жертвоприношение",
	"Засуха",
	"Источник",
	"Капля в Море",
	"Круги на Воде",
	"Легкий Бриз",
	"Миграция",
	"Мистификация",
	"Озарение",
	"Последний Шанс",
	"Равенство",
	"Роса",
	"Сердце Астаниэль",
	"Синее Пламя",
	"Сирена",
	"Тайник",
	"Утечка",
	"Ядовитые Водоросли",
	"Вера в Себя",
	"Голод",
	"Гремучая Смесь",
	"Дикие Звери",
	"Дух Огня",
	"Забота",
	"Забытие",
	"Злой Рок",
	"Искра",
	"Иссушитель",
	"Каприз",
	"Коварство",
	"Лавовый След",
	"Лучшая Защита",
	"Мотылек",
	"Находка",
	"Неуклюжесть",
	"Огнеборец",
	"Огонек",
	"Очаг",
	"Подлость",
	"Пожар",
	"Правосудие",
	"Сердце Пламени",
	"Страх и Радость",
	"Тайная Комната",
	"Феникс",
	"Хвала Небесам",
	"Хищение",
	"Ход Конем",
	"Чума",
	"Безсмертник",
	"Благая Весть",
	"Возмездие",
	"Дух Леса",
	"Жертва",
	"Знак Свыше",
	"Зной",
	"Изобилие",
	"Лесная Братия",
	"Немного Времени",
	"Новые Насаждения",
	"Орошение",
	"Пинок",
	"Пламя Природы",
	"Погибель",
	"Редкие Травы",
	"Росток",
	"Сатир",
	"Сердце Ка-Написа",
	"Сюрприз",
	"Тритон",
	"Триумф",
	"Хитрость",
	"Щедрость"
);

function create_game( $id1, $id2, $money )
{
	global $cards;

	$res = f_MQuery( "SELECT game_id FROM magic_players WHERE player_id in ( $id1, $id2 )" );
	$arr = f_MFetch( $res );
	if( $arr )
	{
		$old_id = $arr[0];
		$res = f_MQuery( "SELECT player_id FROM magic_players WHERE game_id=$old_id" );
		while( $arr = f_MFetch( $res ) )
		{
			f_MQuery( "UPDATE player_waste SET regime=0 WHERE player_id=$arr[0]" );
		}
		f_MQuery( "DELETE FROM magic WHERE game_id=$old_id" );
		f_MQuery( "DELETE FROM magic_players WHERE game_id=$old_id" );
		f_MQuery( "DELETE FROM magic_cards WHERE game_id=$old_id" );
	}

	f_MQuery( "DELETE FROM magic_players WHERE player_id in( $id1, $id2 )" );
	f_MQuery( "DELETE FROM magic_cards WHERE player_id in( $id1, $id2 )" );

	$tm = time( ) + 30;
	f_MQuery( "INSERT INTO magic( last_turn_made, money ) VALUES ( $tm, $money )" );
	$game_id = mysql_insert_id( );
	f_MQuery( "INSERT INTO magic_players( game_id, player_id, wp, np, fp, wm, nm, fm, len, def, my_turn ) VALUES ( $game_id, $id1, 2, 2, 2, 7, 7, 7, 20, 5, 1 )" );
	f_MQuery( "INSERT INTO magic_players( game_id, player_id, wp, np, fp, wm, nm, fm, len, def, my_turn ) VALUES ( $game_id, $id2, 2, 2, 2, 7, 7, 7, 20, 5, 0 )" );
    
    $arr = Array( );
    for( $i = 0; $i < count( $cards ); ++ $i )
    	$arr[] = $i;

    for( $i = 0; $i < 7; ++ $i )
    {
    	$id = mt_rand( 0, count( $arr ) - 1 );
    	f_MQuery( "INSERT INTO magic_cards( game_id, card_id, player_id ) VALUES ( $game_id, ".$arr[$id].", $id1 )" );
    	$arr[$id] = $arr[count( $arr ) - 1];
    	array_pop( $arr );
    }
    for( $i = 0; $i < 7; ++ $i )
    {
    	$id = mt_rand( 0, count( $arr ) - 1 );
    	f_MQuery( "INSERT INTO magic_cards( game_id, card_id, player_id ) VALUES ( $game_id, ".$arr[$id].", $id2 )" );
    	$arr[$id] = $arr[count( $arr ) - 1];
    	array_pop( $arr );
    }

    foreach( $arr as $val ) f_MQuery( "INSERT INTO magic_cards( game_id, card_id, player_id ) VALUES ( $game_id, ".$val.", 0 )" );
}

function refr( $player_id )
{
	global $manacost;

	f_MQuery( "DELETE FROM magic_animation WHERE player_id=$player_id" );

	$res1 = f_MQuery( "SELECT * FROM magic_players WHERE player_id=$player_id" );
	$arr1 = f_MFetch( $res1 );
	$game_id = $arr1['game_id'];
	
	


	$res2 = f_MQuery( "SELECT * FROM magic_players WHERE player_id <> $player_id AND game_id = $game_id" );
	$arr2 = f_MFetch( $res2 );
	$res3 = f_MQuery( "SELECT login FROM characters WHERE player_id=$player_id" );
	$arr3 = f_MFetch( $res3 );
	if( $arr2 )
	{
    	$res4 = f_MQuery( "SELECT login FROM characters WHERE player_id=$arr2[player_id]" );
    	$arr4 = f_MFetch( $res4 );
	} else
	{
		$arr2 = $arr1;
		$arr4 = Array( '---' );
	}
	echo "refr( '$arr3[0]', '$arr4[0]', $arr1[len], $arr2[len], $arr1[def], $arr2[def], $arr1[wp], $arr2[wp], $arr1[np], $arr2[np], $arr1[fp], $arr2[fp], $arr1[wm], $arr2[wm], $arr1[nm], $arr2[nm], $arr1[fm], $arr2[fm] );";

	if( $arr1['status'] == 1 )
	{
		echo "_( 'cards' ).style.display='none';";
		echo "_( 'finst' ).innerHTML = '<big><b>Вы выиграли!</b></big><br><li><a href=magic_leave.php>Покинуть игру</a>';";
		echo "_('cur_turn').innerHTML='';";
		echo "finished = true;";
	}
	else if( $arr1['status'] == 2 )
	{
		echo "_( 'cards' ).style.display='none';";
		echo "_( 'finst' ).innerHTML = '<big><b>Вы проиграли!</b></big><br><li><a href=magic_leave.php>Покинуть игру</a>';";
		echo "_('cur_turn').innerHTML='';";
		echo "finished = true;";
	}
	else
	{
		$res = f_MQuery( "SELECT card_id FROM magic_cards WHERE player_id=$player_id ORDER BY entry_id" );
    	$id = 0;
    	while( $arr = f_MFetch( $res ) )
    	{
    		$card_id = $arr[0];
    		if( $card_id < 28 ) $mana = $arr1['wm'];
            else if( $card_id < 59 ) $mana = $arr1['fm'];
            else $mana = $arr1['nm'];

            if( $manacost[$card_id] > $mana ) 
            	echo "card_a( ".($id ++).", $arr[0], 1 );";
           	else echo "card( ".($id ++).", $arr[0] );";
    	}

    	$res = f_MQuery( "SELECT card_id, alpha FROM magic_cards WHERE player_id < 0 AND game_id=$game_id ORDER BY player_id DESC" );
    	while( $arr = f_MFetch( $res ) )
    	{
    		if( !$arr['alpha'] ) echo "card( ".($id ++).", $arr[0] );";
    		else echo "card_a( ".($id ++).", $arr[0], 1 );";
    	}

    	while( $id < 17 ) echo "cl(".($id++).");";


    	if( $arr1['my_turn'] ) echo "_('cur_turn').innerHTML='<b><font color=lime>Ваш Ход</font></b>';";
    	else echo "_('cur_turn').innerHTML='<b><font color=red>Ход<br>Оппонента</font></b>';";
    }

	$res = f_MQuery( "SELECT last_turn_made FROM magic WHERE game_id=$game_id" );
	$arr = f_MFetch( $res );
    $dtm = time( ) - $arr[0];
    $dtm *= 1000;
    print( "var d0=new Date( );" );
    print( "tm = d0.getTime( ) - $dtm;" );
    print( "PingTimer( );" );

//	echo "_(  )";
}

class Magician
{
	var $player_id;
	var $len;
	var $def;
	var $wp;
	var $np;
	var $fp;
	var $wm;
	var $nm;
	var $fm;
	var $cards;
	var $game_id;

	function Magician( $player_id )
	{
		$res = f_MQuery( "SELECT * FROM magic_players WHERE player_id=$player_id" );
		$arr = f_MFetch( $res );

		$this->game_id = $arr['game_id'];
		$this->player_id = $player_id;
		$this->len = $arr['len'];
		$this->def = $arr['def'];
		$this->wp = $arr['wp'];
		$this->np = $arr['np'];
		$this->fp = $arr['fp'];
		$this->wm = $arr['wm'];
		$this->nm = $arr['nm'];
		$this->fm = $arr['fm'];

		$res = f_MQuery( "SELECT card_id FROM magic_cards WHERE player_id=$player_id ORDER BY entry_id" );
		$this->cards = array( );

		$i = 0;
		while( $arr = f_MFetch( $res ) )
		{
			$this->cards[$i] = $arr[0];
			++ $i;
		}
	}

	function Store( )
	{
		$player_id = $this->player_id;

		f_MQuery( "DELETE FROM magic_cards WHERE player_id=$player_id" );
		foreach( $this->cards as $val )
			f_MQuery( "INSERT INTO magic_cards ( player_id, game_id, card_id ) VALUES ( $player_id, {$this->game_id}, $val )" );

		f_MQuery( "UPDATE magic_players SET len={$this->len}
										  , def={$this->def}
										  , wp ={$this->wp}
										  , np ={$this->np}
										  , fp ={$this->fp}
										  , wm ={$this->wm}
										  , nm ={$this->nm}
										  , fm ={$this->fm}
							   WHERE player_id={$this->player_id}" );
	}

	function Process( )
	{
		$this->nm += $this->np;
		$this->fm += $this->fp;
		$this->wm += $this->wp;
	}

	function RemoveNegativeVals( )
	{
		if( $this->nm < 0 ) $this->nm = 0;
		if( $this->fm < 0 ) $this->fm = 0;
		if( $this->wm < 0 ) $this->wm = 0;

		if( $this->np < 0 ) $this->np = 0;
		if( $this->fp < 0 ) $this->fp = 0;
		if( $this->wp < 0 ) $this->wp = 0;

		if( $this->len < 0 ) $this->len = 0;
		if( $this->def < 0 ) $this->def = 0;
	}

	function dmg( $val )
	{
		if( $this->def > 0 )
		{
			$this->def -= $val;
			if( $this->def < 0 )
			{
				$val = - $this->def;
				$this->def = 0;
			}
			else $val = 0;
		}
		$this->len -= $val;
	}

	function won( $he )
	{
		if( $this->len >= 50 ) return true;
		if( $he->len <= 0 ) return true;
		if( $this->wm >= 100 && $this->fm >= 100 && $this->nm >= 100 ) return true;
		return false;
	}
};

function magicCard( $id, $me, $he )
{
	if( $id == 0 ) { if( $me->def == 0 ) $me->def = 5; else $me->def += 3; }
	elseif( $id == 1 ) { ++ $me->wp; ++ $he->wp; $me->wm += 4; }
	elseif( $id == 2 ) {
		if( $me->def <= $he->def ) { -- $me->fp; $me->len -= 2; }
		if( $he->def <= $me->def ) { -- $he->fp; $he->len -= 2; }
	}
	elseif( $id == 3 ) $me->def += 15;
	elseif( $id == 4 ) { $me->def -= 5; $he->def -= 5; return true; }
	elseif( $id == 5 ) { if( $me->def == 0 ) $me->def = 5; else $me->def += 3; }
	elseif( $id == 6 ) { $me->def += 7; $he->dmg( 6 ); }
	elseif( $id == 7 ) { -- $he->wp; }
	elseif( $id == 8 ) { $me->wm += 2; $me->nm += 2; return true; }
	elseif( $id == 9 ) { $me->def += 20; $me->len += 8; }
	elseif( $id == 10 ) { $me->wp --; $me->def += 10; $me->nm += 5; }
	elseif( $id == 11 ) { $me->def += 5; $me->nm -= 6; }
	elseif( $id == 12 ) { $me->def += 8; $me->len += 5; }
	elseif( $id == 13 ) { $me->def += 3; }
	elseif( $id == 14 ) { $me->def ++; return true; }
	elseif( $id == 15 ) { $me->def += 4; }
	elseif( $id == 16 ) { $t = $me->def; $me->def = $he->def; $he->def = $t; }
	elseif( $id == 17 ) { $me->def += 7; $he->dmg( 6 ); }
	elseif( $id == 18 ) { $me->def += 4; $me->wp ++; }
	elseif( $id == 19 ) { if( $me->def < $he->def ) $me->def += 2; else $me->def ++; }
	elseif( $id == 20 ) { if( $me->wp < $he->wp ) $me->wp = $he->wp; }
	elseif( $id == 21 ) { $me->def += 6; }
	elseif( $id == 22 ) { $me->wp ++; }
	elseif( $id == 23 ) { $me->fp ++; $me->def += 5; }
	elseif( $id == 24 ) { $me->wp ++; $he->wp ++; $me->nm += 4; }
	elseif( $id == 25 ) { $me->wp += 2; }
	elseif( $id == 26 ) { $me->wm -= 8; $he->wm -= 8; }
	elseif( $id == 27 ) { $me->def += 6; $he->dmg( 10 ); }
	elseif( $id == 28 ) { if( $me->def > $he->def ) $he->dmg( 3 ); else $he->dmg( 2 ); }
	elseif( $id == 29 ) { $he->len -= 12; }
	elseif( $id == 30 ) { $he->dmg(2); $me->len += 2; $me->def += 4; }
	elseif( $id == 31 ) { $he->dmg( 7 ); }
	elseif( $id == 32 ) { $he->dmg( 20 ); $he->fm -= 10; $he->fp --; }
	elseif( $id == 33 ) { $me->fp ++; $he->fp ++; $me->fm += 3; }
	elseif( $id == 34 ) { $me->fm -= 6; $he->fm -= 6; }
	elseif( $id == 35 ) { if( $he->def > 10 ) $he->dmg( 10 ); else $he->dmg(7); }
	elseif( $id == 36 ) { $he->dmg( 2 ); return true; }
	elseif( $id == 37 ) { if( $he->def == 0 ) $he->dmg( 10 ); else $he->dmg(7); }
	elseif( $id == 38 ) { $he->dmg( 6 ); $me->dmg( 3 ); }
	elseif( $id == 39 ) { $he->len -= 5; $he->fm -= 6; }
	elseif( $id == 40 ) { $he->dmg( 8 ); $he->wp --; }
	elseif( $id == 41 ) { $he->len -= 4; $me->len += 6; }
	elseif( $id == 42 ) { $he->len -= 2; return true; }
	elseif( $id == 43 ) { $he->dmg( 2 ); return true; }
	elseif( $id == 44 ) { $he->dmg( 8 ); $me->len -= 3; }
	elseif( $id == 45 ) { $he->dmg( 6 ); $he->fm -= 3; }
	elseif( $id == 46 ) { $he->len -= 4; }
	elseif( $id == 47 ) { $he->dmg( 5 ); }
	elseif( $id == 48 ) { $he->dmg( 10 ); $he->fm -= 5; $he->fp --; }
	elseif( $id == 49 ) { $he->dmg( 9 ); }
	elseif( $id == 50 ) { $he->len -= 12; }
	elseif( $id == 51 ) { $me->fp ++; }
	elseif( $id == 52 ) { $he->dmg( 10 ); $me->def += 4; }
	elseif( $id == 53 ) { $he->dmg( 4 ); $me->def += 3; }
	elseif( $id == 54 ) { $me->dmg( 1 ); $he->len -= 3; }
	elseif( $id == 55 ) { $me->fp += 2; }
	elseif( $id == 56 ) { $he->nm -= 10; $he->wm -= 5; $me->nm += 5; $me->wm += 2; }
	elseif( $id == 57 ) { if( $me->def > $he->def ) $he->len -= 6; else $he->dmg( 6 ); }
	elseif( $id == 58 ) { $me->wm -= 5;$me->nm -= 5;$me->fm -= 5; $he->wm -= 5;$he->nm -= 5;$he->fm -= 5; }
	elseif( $id == 59 ) { $me->len += 3; }
	elseif( $id == 60 ) { $me->len += 15; }
	elseif( $id == 61 ) { $he->len --; return true; }
	elseif( $id == 62 ) { $me->len += 20; }
	elseif( $id == 63 ) { $he->len -= 9; $me->np --; }
	elseif( $id == 64 ) { $me->len += 6; $he->len -= 4; }
	elseif( $id == 65 ) { $he->len -= 5; }
	elseif( $id == 66 ) { if( $me->np < $he->np ) $me->np = $he->np; else $he->np = $me->np; }
	elseif( $id == 67 ) { $he->len -= 6; }
	elseif( $id == 68 ) { $he->len -= 2; $me->len += 2; }
	elseif( $id == 69 ) { $me->len += 11; }
	elseif( $id == 70 ) { $me->len += 11; $me->def -= 5; }
	elseif( $id == 71 ) { $he->len -= 3; }
	elseif( $id == 72 ) { $me->len += 8; $me->fp ++; }
	elseif( $id == 73 ) { $me->len -= 7; $he->len -= 7; $me->np --; $he->np --; }
	elseif( $id == 74 ) { $me->len += 5; }
	elseif( $id == 75 ) { if( $me->len < $he->len ) $me->len += 2; else $me->len ++; }
	elseif( $id == 76 ) { $me->len += 8; $me->def += 3; }
	elseif( $id == 77 ) { $me->np ++; }
	elseif( $id == 78 ) { $me->len += 8; }
	elseif( $id == 79 ) { $me->len += 4; $me->fm -= 3; $he->len -= 2; }
	elseif( $id == 80 ) { $me->len += 12; $he->dmg( 6 ); }
	elseif( $id == 81 ) { $me->len -= 5; $me->np += 2; }
	elseif( $id == 82 ) { $me->len ++; $he->len ++; $me->nm += 3; }


	return false;
}

?>
