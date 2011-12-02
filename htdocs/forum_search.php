<?

include_once( "functions.php" );

class ForumSearch
{
	var $fname;

	var $node_pos;
	var $data_pos;

	function ForumSearch( $fname )
	{
		$this->fname = $fname;
	}

	function Index( $txt, $tid, $pid )
	{
		$words = $this->tokenize( $txt );
		$f = fopen( $this->fname, "r+b" );
		$id = 0;

		foreach( $words as $w )
		{
			$this->seekWord( $f, $w );
			$now = $this->node_pos;
			$data = $this->data_pos;
			// store data
			fseek( $f, 0, SEEK_END );
			$end = ftell( $f );
			fwrite( $f, $this->dword2str( $tid ).$this->dword2str( $pid ).$this->dword2str( $id ).$this->dword2str( $data ) );
			fseek( $f, $now + 9 ); fwrite( $f, $this->dword2str( $end ) );
            ++ $id;
		}

		fclose( $f );
	}

	function SearchSeq( $txt )
	{
		$words = $this->tokenize( $txt );
		if( count( $words ) == 0 ) return false;
		$f = fopen( $this->fname, "r+b" );
		$n = count( $words ); if( $n > 5 ) $n = 5;
		$words = array_reverse( $words );

		$ret = array( );
		$as = array( );
		$ai = array( );
		for( $i = 0; $i < $n; ++ $i ) { $as[$i] = $this->getWordSet( $words[$i] ); $ai[$i] = 0; }
		$stop = false;
		$ok = false;
		for( $ai[0] = 0; $ai[0] < count( $as[0] ) && !$stop; ++ $ai[0] )
		{
			if( $ok && $ai[0] && $as[0][$ai[0]][1] == $as[0][$ai[0] - 1][1] ) continue;
			$ok = true;
			for( $i = 1; $ok && $i < $n; ++ $i )
			{
				while( $ai[$i] < count( $as[$i] ) && ( $as[$i][$ai[$i]][1] > $as[$i - 1][$ai[$i - 1]][1] || $as[$i][$ai[$i]][1] == $as[$i - 1][$ai[$i - 1]][1] && $as[$i][$ai[$i]][2] > $as[$i - 1][$ai[$i - 1]][2] ) )
					++ $ai[$i];
				if( $ai[$i] == count( $as[$i] ) ) { $ok = false; $stop = true; }
				if( $as[$i][$ai[$i]][1] < $as[$i - 1][$ai[$i - 1]][1] ) $ok = false;
				else if( $as[$i - 1][$ai[$i - 1]][2] - $as[$i][$ai[$i]][2] > 1 ) $ok = false;
			}
			if( $ok ) $ret[] = array( $as[0][$ai[0]][0], $as[0][$ai[0]][1], $as[0][$ai[0]][2] );
		}

		return $ret;
	}

	function SearchOrd( $txt )
	{
		$words = $this->tokenize( $txt );
		if( count( $words ) == 0 ) return false;
		$f = fopen( $this->fname, "r+b" );
		$n = count( $words ); if( $n > 5 ) $n = 5;
		$words = array_reverse( $words );

		$ret = array( );
		$as = array( );
		$ai = array( );
		for( $i = 0; $i < $n; ++ $i ) { $as[$i] = $this->getWordSet( $words[$i] ); $ai[$i] = 0; }
		$stop = false;
		for( $ai[0] = 0; $ai[0] < count( $as[0] ) && !$stop; ++ $ai[0] )
		{
			if( $ai[0] && $as[0][$ai[0]][1] == $as[0][$ai[0] - 1][1] ) continue;
			$ok = true;
			for( $i = 1; $ok && $i < $n; ++ $i )
			{
				while( $ai[$i] < count( $as[$i] ) && ( $as[$i][$ai[$i]][1] > $as[$i - 1][$ai[$i - 1]][1] || $as[$i][$ai[$i]][1] == $as[$i - 1][$ai[$i - 1]][1] && $as[$i][$ai[$i]][2] > $as[$i - 1][$ai[$i - 1]][2] ) )
					++ $ai[$i];
				if( $ai[$i] == count( $as[$i] ) ) { $ok = false; $stop = true; }
				if( $as[$i][$ai[$i]][1] < $as[$i - 1][$ai[$i - 1]][1] ) $ok = false;
			}
			if( $ok ) $ret[] = array( $as[0][$ai[0]][0], $as[0][$ai[0]][1], $as[0][$ai[0]][2] );
		}

		return $ret;
	}

	function SearchAny( $txt )
	{
		$words = $this->tokenize( $txt );
		if( count( $words ) == 0 ) return false;
		$f = fopen( $this->fname, "r+b" );
		$n = count( $words ); if( $n > 5 ) $n = 5;
		$words = array_reverse( $words );

		$ret = array( );
		$as = array( );
		$ai = array( );
		for( $i = 0; $i < $n; ++ $i ) { $as[$i] = $this->getWordSet( $words[$i] ); $ai[$i] = 0; }
		$stop = false;
		for( $ai[0] = 0; $ai[0] < count( $as[0] ) && !$stop; ++ $ai[0] )
		{
			if( $ai[0] && $as[0][$ai[0]][1] == $as[0][$ai[0] - 1][1] ) continue;
			$ok = true;
			for( $i = 1; $ok && $i < $n; ++ $i )
			{
				while( $ai[$i] < count( $as[$i] ) && ( $as[$i][$ai[$i]][1] > $as[$i - 1][$ai[$i - 1]][1] ) )
					++ $ai[$i];
				if( $ai[$i] == count( $as[$i] ) ) { $ok = false; $stop = true; }
				if( $as[$i][$ai[$i]][1] < $as[$i - 1][$ai[$i - 1]][1] ) $ok = false;
			}
			if( $ok ) $ret[] = array( $as[0][$ai[0]][0], $as[0][$ai[0]][1], $as[0][$ai[0]][2] );
		}

		return $ret;
	}

	function tokenize( $txt )
	{
		$txt .= ' ';
		$l = strlen( $txt );
		$txt = mb_strtolower( $txt );
		$word = ''; $wl = 0;
		$words = array( );
		for( $i = 0; $i < $l; ++ $i )
		{
			if( $txt[$i] == 'ё' || $txt[$i] >= 'а' && $txt[$i] <= 'я' || $txt[$i] >= 'a' && $txt[$i] <= 'z' )
			{
				$word .= $txt[$i];
                ++ $wl;
			}
			else
			{
				if( $wl >= 3 ) $words[] = $word;
				//if( count( $words ) > 5 ) break;
				$word = ''; $wl = 0;
			}
		}
		return $words;
	}

	function getWordSet( $word )
	{
		$f = fopen( $this->fname, "r+b" );

		$this->seekWord( $f, $word );
		fseek( $f, $this->data_pos );

		$ret = array( );
		while( 1 )
		{
			$tid = $this->get4bytes( $f );
			$pid = $this->get4bytes( $f );
			$off = $this->get4bytes( $f );
			$next = $this->get4bytes( $f );
			if( !$tid ) break;
			$ret[] = array( $tid, $pid, $off );
			fseek( $f, $next );
		}
		return $ret;
	}

	function seekWord( $f, $w )
	{
		fseek( $f, 0 );
		$wl = strlen( $w );
		for( $i = 0; $i < $wl; ++ $i )
		{
			$ok = false;
			while( 1 )
			{
				$now = ftell( $f );
				$ltr = fread( $f, 1 );
				if( $ltr == '' ) // special case - file is empty
				{
					fseek( $f, 0 );
					fwrite( $f, "\0\0\0\0\0\0\0\0\0\0\0\0\0" );
					fseek( $f, 13 );
					$ltr = 0;
				}
				else
				{
	   				$where = $this->get4bytes( $f );
    				$next = $this->get4bytes( $f );
					$data = $this->get4bytes( $f );
				}

				if( $ltr === 0 || $ltr === "\0" ) break;
				if( $ltr == $w[$i] ) { $ok = true; fseek( $f, $where ); break; }
				fseek( $f, $next );
			}
			if( !$ok )
			{
				fseek( $f, 0, SEEK_END );
				$end = ftell( $f );
				fseek( $f, $now );
				fwrite( $f, $w[$i].$this->dword2str( $end + 13 ).$this->dword2str( $end ).$this->dword2str( $end + 26 ) );
				fseek( $f, $end );
				fwrite( $f, "\0\0\0\0\0\0\0\0\0\0\0\0\0" ); // next
				fwrite( $f, "\0\0\0\0\0\0\0\0\0\0\0\0\0" ); // where
				fwrite( $f, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0" ); // data
				$data = $end + 26;
				fseek( $f, $end + 13 );
			}
		}
		$this->node_pos = $now;
		$this->data_pos = $data;
	}

	function dword2str( $a )
	{
		return chr( (int)($a / ( 1 << 24 )) ).chr( (int)($a / ( 1 << 16 )) & 255 ).chr( (int)($a / ( 1 << 8 )) & 255 ).chr( $a & 255 );
	}

	function get4bytes( $f )
	{
		$a = ord( fread( $f, 1 ) );
		$a = $a * 256 + ord( fread( $f, 1 ) );
		$a = $a * 256 + ord( fread( $f, 1 ) );
		$a = $a * 256 + ord( fread( $f, 1 ) );
		return $a;
	}
};

/*mb_internal_encoding( "CP1251" );
$f = new ForumSearch( "forum_search.bin" );
//$f->Index( "Привет, ромашки-партизанки!\nЧитаю сказки!!!<br>That's Great!\tЯ Ёлка!", 142, 1821 );

f_MConnect( );

$res = f_MQuery( "SELECT * FROM forum_posts ORDER BY post_id LIMIT 50" );
while( $arr = f_MFetch( $res ) )
{
//	echo "<small><hr>$arr[text]</small><hr>";
//	$f->Index( $arr['text'], $arr['thread_id'], $arr['post_id'] );
}


$f->Search( 'Последний раз редактировался' );

echo "<pre>";
$arr = $f->getWordSet( 'сделать' );
print_r( $arr );
$arr = $f->getWordSet( 'считаю' );
print_r( $arr );
$arr = $f->getWordSet( 'астаниэль' );
print_r( $arr );

echo "Moo!</pre>"; */

?>
