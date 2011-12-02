0<?

class RiddleGenerator
{
	var $number;
	var $text;
	
	function numberForm( $a )
	{
		if( $a % 100 - $a % 10 == 10 ) return 2;
		if( $a % 10 == 1 ) return 0;
		if( $a % 10 == 0 ) return 2;
		if( $a % 10 < 5 ) return 1;
		return 2;
	}
	
	function numberToStr( $a, $rod = 0 )
	{
		$st = '';
		if( $a == 0 ) return '����';
		$moo = Array( '������', "������", "�����" );
		if( $a >= 1000 ) $st .= $this->numberToStr( floor( $a / 1000 ), 1 ) . ' ' . $moo[$this->numberForm( floor( $a / 1000 ) )];
		$a %= 1000;
		if( !$a ) return $st;
		if( $st != '' ) $st .= ' ';
		
		if( $a >= 900 ) $st .= '���������';
		else if( $a >= 800 ) $st .= '���������';
		else if( $a >= 700 ) $st .= '�������';
		else if( $a >= 600 ) $st .= '��������';
		else if( $a >= 500 ) $st .= '�������';
		else if( $a >= 400 ) $st .= '���������';
		else if( $a >= 300 ) $st .= '������';
		else if( $a >= 200 ) $st .= '������';
		else if( $a >= 100 ) $st .= '���';
		$a %= 100;
		if( !$a ) return $st;
		if( $st != '' ) $st .= ' ';
		
		if( $a >= 90 ) $st .= '���������';
		else if( $a >= 80 ) $st .= '�����������';
		else if( $a >= 70 ) $st .= '���������';
		else if( $a >= 60 ) $st .= '����������';
		else if( $a >= 50 ) $st .= '���������';
		else if( $a >= 40 ) $st .= '�����';
		else if( $a >= 30 ) $st .= '��������';
		else if( $a >= 20 ) $st .= '��������';
		else if( $a >= 10 )
		{
			$a %= 10;
			if( $a >= 9 ) $st .= '������������';
			else if( $a >= 8 ) $st .= '������������';
			else if( $a >= 7 ) $st .= '����������';
			else if( $a >= 6 ) $st .= '�����������';
			else if( $a >= 5 ) $st .= '����������';
			else if( $a >= 4 ) $st .= '������������';
			else if( $a >= 3 ) $st .= '����������';
			else if( $a >= 2 ) $st .= '����������';
			else if( $a >= 1 ) $st .= '����������';
			else $st .= "������";
			return $st;
		}
	
		$a %= 10;
		if( !$a ) return $st;
		if( $st != '' ) $st .= ' ';

		if( $a >= 9 ) $st .= '������';
		else if( $a >= 8 ) $st .= '������';
		else if( $a >= 7 ) $st .= '����';
		else if( $a >= 6 ) $st .= '�����';
		else if( $a >= 5 ) $st .= '����';
		else if( $a >= 4 ) $st .= '������';
		else if( $a >= 3 ) $st .= '���';
		else if( $a >= 2 )
		{
			if( $rod == 0 ) $st .= '���';
			if( $rod == 1 ) $st .= '���';
			if( $rod == 2 ) $st .= '���';
		}
		else if( $a >= 1 )
		{
			if( $rod == 0 ) $st .= '����';
			if( $rod == 1 ) $st .= '����';
			if( $rod == 2 ) $st .= '����';
		}
		
		return $st;
	}

	function generate_F( )
	{
		$words = Array(
		  	Array( "hare", "hase", "lievre", "����" ),
		  	Array( "bear", "bar", "ours", "�������" ),
		  	Array( "wolf", "wolf", "loup", "����" ),
		  	Array( "fox", "fuchs", "renard", "����" ),
		  	Array( "pine", "kiefer", "pin", "�����" ),
		  	Array( "fir", "tanne", "sapin", "���" ),
		  	Array( "birch", "birke", "bouleau", "������" ),
		  	Array( "oak", "eiche", "chene", "���" ),
		  	Array( "snow", "schnee", "neige", "����" ),
		  	Array( "ice", "eis", "glace", "���" ),
		  	Array( "grass", "gras", "herbe", "�����" ),
		  	Array( "sky", "himmel", "ciel", "����" ),
		  	Array( "paradise", "paradies", "paradis", "���" ),
		  	Array( "gold", "gold", "or", "������" ),
		  	Array( "silver", "silber", "argent", "�������" ),
		  	Array( "fire", "feuer", "feu", "�����" ),
		  	Array( "water", "wasser", "eau", "����" ),
		  	Array( "air", "luft", "air", "������" ),
		  	Array( "Monday", "Montag", "lundi", "�����������" ),
		  	Array( "Sunday", "Sonntag", "dimanche", "�����������" ),
		  	Array( "January", "Januar", "Janvier", "������" ),
		  	Array( "winter", "winter", "hiver", "����" ),
		  	Array( "camomile", "kamille", "camomille", "�������" )
		);

		$id = mt_rand( 0, count( $words ) - 1 );
		$this->number = $words[$id][3];
		$this->text = "��-��������� - {$words[$id][0]}, ��-������� - {$words[$id][1]}, ��-���������� - {$words[$id][2]}, � ��-������?";
	}

	function generate_E( )
	{
		$days = Array( "�����������", "�������", "�����", "�������", "�������", "�������", "�����������" );
		$days2 = Array( "������������", "��������", "�����", "��������", "�������", "�������", "�����������" );
		$val = mt_rand( 0, 6 );
		$this->number = $days[$val];

		$st = '';
		if( mt_rand( 1, 2 ) == 1 ) // ������ � �����������
		{
			if( mt_rand( 1, 2 ) == 1 ) // ������
			{
				++ $val;
				$st = '������ ';
			}
			else // �����������
			{
				$val += 2;
				$st = '����������� ';
			}
			$add = mt_rand( 2, 4 );
			if( mt_rand( 1, 2 ) == 1 ) // ��������� n ���� ��
			{
				$val += $add;
				$val %= 7;
				$st .= " ��������� $add ��� �� $days2[$val]";
			}
			else
			{
				$val -= $add;
				$val += 7;
				$val %= 7;
				$st .= " ������� $add ��� �";
				if( $val == 2 ) $st .= "�";
				$st .= " $days2[$val]";
			}
		}
		else // ����� � ���������
		{
			if( mt_rand( 1, 2 ) == 1 ) // �����
			{
				-- $val;
				$st = '����� ';
			}
			else // ���������
			{
				$val -= 2;
				$st = '��������� ';
			}
			$add = mt_rand( 2, 4 );
			if( mt_rand( 1, 2 ) == 1 ) // ���������� ��
			{
				$val += $add;
				$val %= 7;
				$st .= " ���������� $add ��� �� $days2[$val]";
			}
			else
			{
				$val -= $add;
				$val += 14;
				$val %= 7;
				$st .= " ������ $add ��� �";
				if( $val == 2 ) $st .= "�";
				$st .= " $days2[$val]";
			}
		}

		$this->text = "$st. ����� ���� ������ �������?";
	}

	function generate_D( )
	{
		$riddles = Array( 
			"�� ������ �����, ��-���������� �������. ��� ���?", "�������",
			"����� � ������, �������. �� �� ����������, �� ����� ���������, �� �� ���������. ��� ���?", "�������",

			"�� ������ ������� � ��������� ������, ������� ����� ������. ����� ������ ������ ��? (������� ������)", "2",
			"� ���� ���� ���� ���� �������: ����, ����, ����, ���� �... ��� ����� ����� ����?", "����",
			"���� ���� ����� ����� ���� ����� �� ���� �����, �� ������� ����� ����� ����� �����, ����� ������� ���� �����? (������� ������)", "5",
			"� ������� ������� �� ����� ������. ������� ����� � ����� (������� ������)?", "8",
			"���� � 12 ����� ���� ���� �����, �� ����� �� �������, ��� ����� 72 ���� ����� ��������� ������?", "���",
			"�� ����� ����� ��� ������, � ����� ��� ���� 3 �����. ���� �� ��� - �� 1 �����. ����� ��� ������? (������� ��� ����� ����� ������ � ������� �����������)", "1 2",
			"�� ����� ������ 3 ������� � ������. ��� ���� ���� ������ �����, �������� ������ ������ �� ����. ������� �������� �������� �� �����?", "3",
			"� ��� ���� ������ ���� ������. � ������ ������� ����� ����������� �����, ���� � �����. ��� ������� ������ � ������ �������?", "������",
			"� ����� � ����� ���������, � ������ �������, ���� � - ... ���?", "����",
			"��� ���� � ������� �����. ��� ��������� �������� ����� �� 25 �������� � ��� �������. �������� 10 ����� ����� ��������� ������� � ��������. ���� ������� ��� ����� ����� ����� �� ��������. ������������� ����� � ����� ������, � ���� ����� ����� ���� ����. ������� ��� ���� �� ������ � ������� �����? (������� ������)", "2",
			"� ������� ���� 17 ����. ���, ����� ������ - �����, ��������� ������. ������� ����� ������ ����? (������� ������)", "9",
			"� ������� ���� 17 ����. ���, ����� ������ - �����, ��������� ������. ������� ����� ����� ����? (������� ������)", "8",
			"�� ���� ����� ������ �������. ������� ������� �� ������ �����? (������� ������)", "50",
			"������� ����� ����� ����� � 16 ������, �� �������� �� �������� ��������� �� 2 �����. �� ��������� �������� ���� �� ������� ��������� �����? (������� ������)", "7",

			"�� ����� ����� ������� ������. ���� �������, ������ �������. ��� ���?", "����",
			"������ �����, ����� ��� ����... ��� ���?", "�������",
			"�� ����, � � ����������, �� �������, � �����, �� �������, � ������������. ��� ���?", "�����",
			"�� ���� ��� ������ ���� �����������, � � �������� ������ ���� �����������. ��� ���?", "�����",
			"�����, � �� ����, ����, � �� ����. ��� ���?", "������",
			"���� ������, �� ���� �� ���, �������� ������� - �������, �������. ��� �?", "�����",
			"����� ���� - ����� ���� �������. ��� ���?", "�������",
			"���� � �������� ����, ���� � ������ ����, � ����� �� ������ ���� ������ � �����. ��� �?", "�����",
			"�� ������� � �� ���, ����� ���� ����� ���. ���� ����� - ��� ����, ��� ���� - ������� �����. ��� ���?", "����",
			"���� ������, ���� ����, �������� - ��� ��������� ������. ��� �?", "�����",
			"�� ����, � �����, �� ���, � �����. ��� ���?", "�����",
			"�� � ���� �� �����, �� � ���� �� �����. ��� ���?", "���"
		);

		$id = mt_rand( 0, count( $riddles ) / 2 - 1 );
		$this->text = $riddles[$id * 2];
		$this->number = $riddles[$id * 2 + 1];
	}
	
	function generate_C( )
	{
		$moo[0] = Array( "�����", "����", "����", "������", "�����", "�����" );
		$moo[1] = Array( "����", "����", "������", "������", "���", "������" );
		$moo[2] = Array( "�����", "���", "������", "������", "������", "����", "�����" );
		$moo[3] = Array( "����", "����", "�����", "������", "��������", "�����", "�����", "�������", "������", "����", "����", "�����", "�����", "���", "����" );
		$moo[4] = Array( "������", "�������", "�������", "�����", "��������", "�������", "���������", "�����" );
		$moo[5] = Array( "���", "������", "�����", "������", "������", "������", "���", "�������" );
		$moo[6] = Array( "������", "�������", "�������", "������" );
		
		$id1 = mt_rand( 0, count( $moo ) - 1 );
		$id2 = mt_rand( 0, count( $moo ) - 2 );
		if( $id2 >= $id1 ) ++ $id2;
		
		$pos = mt_rand( 0, 3 );
		$a[0] = mt_rand( 0, count( $moo[$id1] ) - 1 );
		$a[1] = mt_rand( 0, count( $moo[$id1] ) - 2 );
		if( $a[1] >= $a[0] ) ++ $a[1];
		$a[2] = mt_rand( 0, count( $moo[$id1] ) - 3 );
		if( $a[2] >= min( $a[0], $a[1] ) ) ++ $a[2];
		if( $a[2] >= max( $a[0], $a[1] ) ) ++ $a[2];
		
		$b = mt_rand( 0, count( $moo[$id2] ) - 1 );
		$cur = 0;
		
		$st = '��� �� �������������� ������: ';
		for( $i = 0; $i < 4; ++ $i )
		{
			if( $i ) $st .= ', ';
			if( $i != $pos ) $st .= $moo[$id1][$a[$cur ++]];
			else $st .= $moo[$id2][$b];
		}
		$st .= '?';
		
		$this->text = $st;
		$this->number = $moo[$id2][$b];
	}
	
	function generate_B( )
	{
		$val = mt_rand( 10, 49 );
		
		$do = mt_rand( 0, 5 );
		if( $do == 0 )
		{
			$val -= $val % 2;
			$st = '�������� �� ����� '.$this->numberToStr( $val );
		}
		if( $do == 1 )
		{
			$val -= $val % 3;
			$st = '����� �� ����� '.$this->numberToStr( $val );
			$val /= 3;
		}
		else if( $do == 2 )
		{
			$val -= $val % 4;
			$st = '�������� �� ����� '.$this->numberToStr( $val );
			$val /= 4;
		}
		else
		{
			$do2 = mt_rand( 0, 1 );
			if( $do2 == 0 )
			{
				$b = mt_rand( 10, 49 );
				$st = '����� ����� '.$this->numberToStr( $val ) . ' � ' . $this->numberToStr( $b );
				$val += $b;
			}
			if( $do2 == 1 )
			{
				$b = mt_rand( 5, $val - 2 );
				$st = '�������� ����� '.$this->numberToStr( $val ) . ' � ' . $this->numberToStr( $b );
				$val -= $b;
			}
			if( $do == 3 )
			{
				$st = '��������� ' . $st;
				$val *= 2;
			}
			else
			{
				$st = '��������� ' . $st;
				$val *= 3;
			}
		}

		$do = mt_rand( 0, 1 );
		if( $do == 0 )
		{
			$mul = mt_rand( 2, 10 );
			$st = '� ' . $this->numberToStr( $mul ) . ' '.my_word_str( $mul, '���', "����", "���" ).' ������ ��� ' . $st;
			$val *= $mul;
		}
		else
		{
			$add = mt_rand( 1, 40 );
			$st = '�� ' . $this->numberToStr( $add ) . ' ������ ��� ' . $st;
			$val += $add;
		}
		$st .= '?';
		
		$this->number = $val;
		$this->text = "����� ����� " . $st;
	}
	
	function generate_A( )
	{
		$this->number = mt_rand( 10, 30 );

		$food[0] = Array( "����", "������", "��������", "���������" );
		$food[1] = Array( "�����", "������", "���������", "����������" );
		$food[2] = Array( "������", "�������", "����������", "�����������" );

		$item[0] = Array( "������", "���", "�������", "������" );
		$item[1] = Array( "�������", "����", "��������", "�������" );
		$item[2] = Array( "��������", "�����", "���������", "��������" );

		$actor[0] = Array( "����", "����", "��������", "�����" );
		$actor[1] = Array( "�����", "�����", "���������", "������" );
		$actor[2] = Array( "������", "������", "����������", "�������" );
		
		$owner = Array( "� �����", "� �����������", "� �������", "� ����������", "� �������", "� ������", "� �����", "� �������" );
		$places = Array( "�� ������", "����� �����", "� �����", "� ����" );
		
		$actions1 = Array( "����", "�� ����� ������", "� ����� ����" );
		$actions2 = Array( "��������", "����" );
		
		$subtracts1 = Array( "�� ����� �����", "�� ������ �� �����", "�� �������", "�� �������� � �����" );
		$subtracts2 = Array( "���� �� ������", "����� �������� �� �����" );
		
		$adds1 = Array( "�� �����", "�� �����" );
		$adds2 = Array( "������" );
		
		$type = mt_rand( 0, 2 );
		if( $type == 0 )
		{
			$operand = $food;
			$operator = $owner;
			$actions = $actions1;
			$subtracts = $subtracts1;
			$adds = $adds1;
		}
		else if( $type == 1 )
		{
			$operand = $item;
			$operator = $owner;
			$actions = $actions1;
			$subtracts = $subtracts1;
			$adds = $adds1;
		}
		else if( $type == 2 )
		{
			$operand = $actor;
			$operator = $places;
			$actions = $actions2;
			$subtracts = $subtracts2;
			$adds = $adds2;
		}
		
		$operand_id = mt_rand( 0, count( $operand[0] ) - 1 );
		$operand[0] = $operand[0][$operand_id];
		$operand[1] = $operand[1][$operand_id];
		$operand[2] = $operand[2][$operand_id];
		
		$operator = $operator[mt_rand( 0, count( $operator ) - 1 )];
		$action = $actions[mt_rand( 0, count( $actions ) - 1 )];
		
		do { $cur = $this->number - 3 + mt_rand( 0, 20 ); } while( $this->numberForm( $cur ) == 0 );
		$st = $operator . ' ' . $action . ' ' . $this->numberToStr( $cur ) . ' ' . $operand[$this->numberForm( $cur )] . '. ';
		
		$do = mt_rand( 1, 3 );
		$id1 = mt_rand( 0, count( $subtracts ) - 1 );
		do{ $id2 = mt_rand( 0, count( $subtracts ) - 1 ); } while( $id1 == $id2 );
		if( $do == 1 && $cur % 3 == 0 )
		{
			$st .= '����� �� ��� ' . $subtracts[$id1];
			$cur = $cur * 2 / 3;
		}
		else if( $do == 2 && $cur % 2 == 0 )
		{
			if( $type == 2 ) $st .= '�������� �� ��� ';
			else $st .= '�������� �� ��� ';
			$st .= $subtracts[$id1];
			$cur /= 2;
		}
		else
		{
			$val = mt_rand( 2, $cur - 2 );
			$tmp = $this->numberToStr( $val ) . ' �� ��� ' . $subtracts[$id1];
			$tmp = mb_strtoupper( $tmp[0] ).substr( $tmp, 1 );
			$st .= $tmp;
			$cur -= $val;
		}
		$st .= '. ';
		
		if( mt_rand( 0, 1 ) == 1 )
		{
			$do = mt_rand( 1, 3 );
			if( $do == 1 && $cur % 3 == 0 )
			{
				$st .= '����� �� ���������� ' . $subtracts[$id2];
				$cur = $cur * 2 / 3;
			}
			else if( $do == 2 && $cur % 2 == 0 )
			{
				if( $type == 2 ) $st .= '�������� �� ���������� ';
				else $st .= '�������� �� ���������� ';
				$st .= $subtracts[$id2];
				$cur /= 2;
			}
			else
			{
				$val = mt_rand( 2, $cur - 2 );
				$tmp = $this->numberToStr( $val ) . ' �� ���������� ' . $subtracts[$id2];
				$tmp = mb_strtoupper( $tmp[0] ).substr( $tmp, 1 );
				$st .= $tmp;
				$cur -= $val;
			}
			$st .= '. ';
		}
		
		if( $cur > $this->number ) $this->number = $cur + mt_rand( 10, 14 );
		
		$val = $this->number - $cur;
		$st .= '����� ' . $adds[mt_rand( 0, count( $adds ) - 1 )] . ' ��� ' . $this->numberToStr( $val ) . ' ' . $operand[$this->numberForm( $val )] . '. ';
		$cur += $val;
		
		$st .= '������� ' . $operand[2] . ' �������� ' . mb_strtolower( $operator ) . '?';
			
		$this->text = $st;
	}
	
	function generate( )
	{
		$type = mt_rand( 0, 5 );
		if( $type == 0 ) $this->generate_A( );
		else if( $type == 1 ) $this->generate_B( );
		else if( $type == 2 ) $this->generate_C( );
		else if( $type == 3 ) $this->generate_D( );
		else if( $type == 4 ) $this->generate_E( );
		else if( $type == 5 ) $this->generate_F( );
	}
};

if( false )
{
	$test = new RiddleGenerator( );
	$test->generate( );
	echo( $test->text.'('.$test->number.')' );
}

?>

