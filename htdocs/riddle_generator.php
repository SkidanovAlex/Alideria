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
		if( $a == 0 ) return 'ноль';
		$moo = Array( 'тысяча', "тысячи", "тысяч" );
		if( $a >= 1000 ) $st .= $this->numberToStr( floor( $a / 1000 ), 1 ) . ' ' . $moo[$this->numberForm( floor( $a / 1000 ) )];
		$a %= 1000;
		if( !$a ) return $st;
		if( $st != '' ) $st .= ' ';
		
		if( $a >= 900 ) $st .= 'девятьсот';
		else if( $a >= 800 ) $st .= 'восемьсот';
		else if( $a >= 700 ) $st .= 'семьсот';
		else if( $a >= 600 ) $st .= 'шестьсот';
		else if( $a >= 500 ) $st .= 'пятьсот';
		else if( $a >= 400 ) $st .= 'четыреста';
		else if( $a >= 300 ) $st .= 'триста';
		else if( $a >= 200 ) $st .= 'двести';
		else if( $a >= 100 ) $st .= 'сто';
		$a %= 100;
		if( !$a ) return $st;
		if( $st != '' ) $st .= ' ';
		
		if( $a >= 90 ) $st .= 'девяносто';
		else if( $a >= 80 ) $st .= 'восемьдесят';
		else if( $a >= 70 ) $st .= 'семьдесят';
		else if( $a >= 60 ) $st .= 'шестьдесят';
		else if( $a >= 50 ) $st .= 'пятьдесят';
		else if( $a >= 40 ) $st .= 'сорок';
		else if( $a >= 30 ) $st .= 'тридцать';
		else if( $a >= 20 ) $st .= 'двадцать';
		else if( $a >= 10 )
		{
			$a %= 10;
			if( $a >= 9 ) $st .= 'девятнадцать';
			else if( $a >= 8 ) $st .= 'восемнадцать';
			else if( $a >= 7 ) $st .= 'семнадцать';
			else if( $a >= 6 ) $st .= 'шестнадцать';
			else if( $a >= 5 ) $st .= 'пятнадцать';
			else if( $a >= 4 ) $st .= 'четырнадцать';
			else if( $a >= 3 ) $st .= 'тринадцать';
			else if( $a >= 2 ) $st .= 'двенадцать';
			else if( $a >= 1 ) $st .= 'одинадцать';
			else $st .= "десять";
			return $st;
		}
	
		$a %= 10;
		if( !$a ) return $st;
		if( $st != '' ) $st .= ' ';

		if( $a >= 9 ) $st .= 'девять';
		else if( $a >= 8 ) $st .= 'восемь';
		else if( $a >= 7 ) $st .= 'семь';
		else if( $a >= 6 ) $st .= 'шесть';
		else if( $a >= 5 ) $st .= 'пять';
		else if( $a >= 4 ) $st .= 'четыре';
		else if( $a >= 3 ) $st .= 'три';
		else if( $a >= 2 )
		{
			if( $rod == 0 ) $st .= 'два';
			if( $rod == 1 ) $st .= 'две';
			if( $rod == 2 ) $st .= 'два';
		}
		else if( $a >= 1 )
		{
			if( $rod == 0 ) $st .= 'один';
			if( $rod == 1 ) $st .= 'одна';
			if( $rod == 2 ) $st .= 'одно';
		}
		
		return $st;
	}

	function generate_F( )
	{
		$words = Array(
		  	Array( "hare", "hase", "lievre", "заяц" ),
		  	Array( "bear", "bar", "ours", "медведь" ),
		  	Array( "wolf", "wolf", "loup", "волк" ),
		  	Array( "fox", "fuchs", "renard", "лиса" ),
		  	Array( "pine", "kiefer", "pin", "сосна" ),
		  	Array( "fir", "tanne", "sapin", "ель" ),
		  	Array( "birch", "birke", "bouleau", "береза" ),
		  	Array( "oak", "eiche", "chene", "дуб" ),
		  	Array( "snow", "schnee", "neige", "снег" ),
		  	Array( "ice", "eis", "glace", "лед" ),
		  	Array( "grass", "gras", "herbe", "трава" ),
		  	Array( "sky", "himmel", "ciel", "небо" ),
		  	Array( "paradise", "paradies", "paradis", "рай" ),
		  	Array( "gold", "gold", "or", "золото" ),
		  	Array( "silver", "silber", "argent", "серебро" ),
		  	Array( "fire", "feuer", "feu", "огонь" ),
		  	Array( "water", "wasser", "eau", "вода" ),
		  	Array( "air", "luft", "air", "воздух" ),
		  	Array( "Monday", "Montag", "lundi", "понедельник" ),
		  	Array( "Sunday", "Sonntag", "dimanche", "воскресенье" ),
		  	Array( "January", "Januar", "Janvier", "январь" ),
		  	Array( "winter", "winter", "hiver", "зима" ),
		  	Array( "camomile", "kamille", "camomille", "ромашка" )
		);

		$id = mt_rand( 0, count( $words ) - 1 );
		$this->number = $words[$id][3];
		$this->text = "По-английски - {$words[$id][0]}, по-немецки - {$words[$id][1]}, по-французски - {$words[$id][2]}, а по-русски?";
	}

	function generate_E( )
	{
		$days = Array( "понедельник", "вторник", "среда", "четверг", "пятница", "суббота", "воскресенье" );
		$days2 = Array( "понедельника", "вторника", "среды", "четверга", "пятницы", "субботы", "воскресенья" );
		$val = mt_rand( 0, 6 );
		$this->number = $days[$val];

		$st = '';
		if( mt_rand( 1, 2 ) == 1 ) // завтра и послезавтра
		{
			if( mt_rand( 1, 2 ) == 1 ) // завтра
			{
				++ $val;
				$st = 'Завтра ';
			}
			else // послезавтра
			{
				$val += 2;
				$st = 'Послезавтра ';
			}
			$add = mt_rand( 2, 4 );
			if( mt_rand( 1, 2 ) == 1 ) // останется n дней до
			{
				$val += $add;
				$val %= 7;
				$st .= " останется $add дня до $days2[$val]";
			}
			else
			{
				$val -= $add;
				$val += 7;
				$val %= 7;
				$st .= " пройдет $add дня с";
				if( $val == 2 ) $st .= "о";
				$st .= " $days2[$val]";
			}
		}
		else // вчера и позавчера
		{
			if( mt_rand( 1, 2 ) == 1 ) // вчера
			{
				-- $val;
				$st = 'Вчера ';
			}
			else // позавчера
			{
				$val -= 2;
				$st = 'Позавчера ';
			}
			$add = mt_rand( 2, 4 );
			if( mt_rand( 1, 2 ) == 1 ) // оставалось до
			{
				$val += $add;
				$val %= 7;
				$st .= " оставалось $add дня до $days2[$val]";
			}
			else
			{
				$val -= $add;
				$val += 14;
				$val %= 7;
				$st .= " прошло $add дня с";
				if( $val == 2 ) $st .= "о";
				$st .= " $days2[$val]";
			}
		}

		$this->text = "$st. Какой день недели сегодня?";
	}

	function generate_D( )
	{
		$riddles = Array( 
			"На пеньке сидит, по-французски говорит. Кто это?", "француз",
			"Сидит в болоте, квакает. На ЛЯ начинается, на ГУШКА кончается, но не полотенце. Что это?", "бегемот",

			"Вы бежите марафон и обгоняете бегуна, который бежит вторым. Каким теперь бежите вы? (укажите числом)", "2",
			"У отца Мэри было пять дочерей: Чача, Чучу, Чичи, Чочо и... Как зовут пятую дочь?", "Мэри",
			"Если пять кошек ловят пять мышей за пять минут, то сколько минут нужно одной кошке, чтобы поймать одну мышку? (укажите числом)", "5",
			"У семерых братьев по одной сестре. Сколько детей в семье (укажите числом)?", "8",
			"Если в 12 часов ночи идет дождь, то можно ли ожидать, что через 72 часа будет солнечная погода?", "нет",
			"На столе лежат две монеты, в сумме они дают 3 рубля. Одна из них - не 1 рубль. Какие это монеты? (укажите два числа через пробел в порядке возрастания)", "1 2",
			"На столе стояло 3 стакана с вишней. Иши съел один стакан вишни, поставив пустой стакан на стол. Сколько стаканов осталось на столе?", "3",
			"У Вас есть только одна спичка. В темной комнате стоят керосиновая лампа, печь и свеча. Что следует зажечь в первую очередь?", "спичку",
			"В речке я люблю резвиться, В стайке плавать, ведь я - ... кто?", "рыба",
			"Иши идет к лесному озеру. Ему навстречу движется класс из 25 учеников и два учителя. Родители 10 детей также принимают участие в прогулке. Пять матерей еще везут своих детей на колясках. Преподаватель ведет с собой собаку, а двое детей ведут двух крыс. Сколько ног идут по дороге к лесному озеру? (укажите числом)", "2",
			"У фермера было 17 овец. Все, кроме девяти - белые, остальные черные. Сколько всего черных овец? (укажите числом)", "9",
			"У фермера было 17 овец. Все, кроме девяти - белые, остальные черные. Сколько всего белых овец? (укажите числом)", "8",
			"На двух руках десять пальцев. Сколько пальцев на десяти руках? (укажите числом)", "50",
			"Портной имеет кусок сукна в 16 метров, от которого он отрезает ежедневно по 2 метра. По истечении скольких дней он отрежет последний кусок? (укажите числом)", "7",

			"На землю упало пуховое одеяло. Лето настало, одеяло пропало. Что это?", "снег",
			"Набита пухом, лежит под ухом... Что это?", "подушка",
			"Не куст, а с листочками, не рубашка, а сшита, не человек, а рассказывает. Что это?", "книга",
			"На ночь два оконца сами закрываются, а с восходом солнца сами открываются. Что это?", "глаза",
			"Жидко, а не вода, бело, а не снег. Что это?", "молоко",
			"Сижу верхом, не знаю на ком, знакомца встречу - соскочу, привечу. Кто я?", "шапка",
			"Зимой спит - летом улья ворошит. Кто это?", "медведь",
			"Хожу в пушистой шубе, живу в густом лесу, В дупле на старом дубе орешки я грызу. Кто я?", "белка",
			"Не барашек и не кот, носит шубу целый год. Шуба серая - для лета, для зимы - другого цвета. Кто это?", "заяц",
			"Меня просят, меня ждут, покажусь - так прятаться начнут. Кто я?", "дождь",
			"Не конь, а бежит, не лес, а шумит. Кто это?", "ручей",
			"Ни в огне не горит, ни в воде не тонет. Что это?", "лед"
		);

		$id = mt_rand( 0, count( $riddles ) / 2 - 1 );
		$this->text = $riddles[$id * 2];
		$this->number = $riddles[$id * 2 + 1];
	}
	
	function generate_C( )
	{
		$moo[0] = Array( "Озеро", "Река", "Пруд", "Болото", "Ручей", "Океан" );
		$moo[1] = Array( "Эльф", "Гном", "Хоббит", "Тролль", "Орк", "Гоблин" );
		$moo[2] = Array( "Осина", "Дуб", "Баобаб", "Тополь", "Береза", "Липа", "Сосна" );
		$moo[3] = Array( "Волк", "Лось", "Олень", "Лисица", "Бурундук", "Хорек", "Хомяк", "Медведь", "Барсук", "Рысь", "Барс", "Кабан", "Шакал", "Лев", "Тигр" );
		$moo[4] = Array( "Синица", "Воробей", "Соловей", "Дятел", "Ласточка", "Тетерев", "Канарейка", "Ворон" );
		$moo[5] = Array( "Меч", "Булава", "Копье", "Секира", "Палица", "Кинжал", "Лук", "Арбалет" );
		$moo[6] = Array( "Железо", "Платина", "Серебро", "Бронза" );
		
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
		
		$st = 'Что из перечисленного лишнее: ';
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
			$st = 'половина от числа '.$this->numberToStr( $val );
		}
		if( $do == 1 )
		{
			$val -= $val % 3;
			$st = 'треть от числа '.$this->numberToStr( $val );
			$val /= 3;
		}
		else if( $do == 2 )
		{
			$val -= $val % 4;
			$st = 'четверть от числа '.$this->numberToStr( $val );
			$val /= 4;
		}
		else
		{
			$do2 = mt_rand( 0, 1 );
			if( $do2 == 0 )
			{
				$b = mt_rand( 10, 49 );
				$st = 'сумма чисел '.$this->numberToStr( $val ) . ' и ' . $this->numberToStr( $b );
				$val += $b;
			}
			if( $do2 == 1 )
			{
				$b = mt_rand( 5, $val - 2 );
				$st = 'разность чисел '.$this->numberToStr( $val ) . ' и ' . $this->numberToStr( $b );
				$val -= $b;
			}
			if( $do == 3 )
			{
				$st = 'удвоенная ' . $st;
				$val *= 2;
			}
			else
			{
				$st = 'утроенная ' . $st;
				$val *= 3;
			}
		}

		$do = mt_rand( 0, 1 );
		if( $do == 0 )
		{
			$mul = mt_rand( 2, 10 );
			$st = 'в ' . $this->numberToStr( $mul ) . ' '.my_word_str( $mul, 'раз', "раза", "раз" ).' больше чем ' . $st;
			$val *= $mul;
		}
		else
		{
			$add = mt_rand( 1, 40 );
			$st = 'на ' . $this->numberToStr( $add ) . ' больше чем ' . $st;
			$val += $add;
		}
		$st .= '?';
		
		$this->number = $val;
		$this->text = "Какое число " . $st;
	}
	
	function generate_A( )
	{
		$this->number = mt_rand( 10, 30 );

		$food[0] = Array( "орех", "огурец", "апельсин", "грейпфрут" );
		$food[1] = Array( "ореха", "огурца", "апельсина", "грейпфрута" );
		$food[2] = Array( "орехов", "огурцов", "апельсинов", "грейпфрутов" );

		$item[0] = Array( "кинжал", "меч", "браслет", "амулет" );
		$item[1] = Array( "кинжала", "меча", "браслета", "амулета" );
		$item[2] = Array( "кинжалов", "мечей", "браслетов", "амулетов" );

		$actor[0] = Array( "эльф", "гном", "единорог", "пегас" );
		$actor[1] = Array( "эльфа", "гнома", "единорога", "пегаса" );
		$actor[2] = Array( "эльфов", "гномов", "единорогов", "пегасов" );
		
		$owner = Array( "У эльфа", "У трактирщика", "У колдуна", "У менестреля", "У фермера", "У рыцаря", "У гнома", "У мастера" );
		$places = Array( "На поляне", "Возле озера", "У ручья", "В лесу" );
		
		$actions1 = Array( "было", "на столе лежало", "в мешке было" );
		$actions2 = Array( "отдыхало", "было" );
		
		$subtracts1 = Array( "он отдал другу", "он продал на рынке", "он потерял", "он проиграл в кости" );
		$subtracts2 = Array( "ушли на опушку", "пошли смотреть на закат" );
		
		$adds1 = Array( "он нашел", "он купил" );
		$adds2 = Array( "пришли" );
		
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
			$st .= 'Треть из них ' . $subtracts[$id1];
			$cur = $cur * 2 / 3;
		}
		else if( $do == 2 && $cur % 2 == 0 )
		{
			if( $type == 2 ) $st .= 'Половина из них ';
			else $st .= 'Половину из них ';
			$st .= $subtracts[$id1];
			$cur /= 2;
		}
		else
		{
			$val = mt_rand( 2, $cur - 2 );
			$tmp = $this->numberToStr( $val ) . ' из них ' . $subtracts[$id1];
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
				$st .= 'Треть из оставшихся ' . $subtracts[$id2];
				$cur = $cur * 2 / 3;
			}
			else if( $do == 2 && $cur % 2 == 0 )
			{
				if( $type == 2 ) $st .= 'Половина из оставшихся ';
				else $st .= 'Половину из оставшихся ';
				$st .= $subtracts[$id2];
				$cur /= 2;
			}
			else
			{
				$val = mt_rand( 2, $cur - 2 );
				$tmp = $this->numberToStr( $val ) . ' из оставшихся ' . $subtracts[$id2];
				$tmp = mb_strtoupper( $tmp[0] ).substr( $tmp, 1 );
				$st .= $tmp;
				$cur -= $val;
			}
			$st .= '. ';
		}
		
		if( $cur > $this->number ) $this->number = $cur + mt_rand( 10, 14 );
		
		$val = $this->number - $cur;
		$st .= 'Потом ' . $adds[mt_rand( 0, count( $adds ) - 1 )] . ' еще ' . $this->numberToStr( $val ) . ' ' . $operand[$this->numberForm( $val )] . '. ';
		$cur += $val;
		
		$st .= 'Сколько ' . $operand[2] . ' осталось ' . mb_strtolower( $operator ) . '?';
			
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

