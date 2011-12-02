<?

include_once( "functions.php" );
include_once( "creature.php" );
include_once( "items.php" );

class Card
{
	var $card_id;
	var $name;
	var $genre;
	var $price;
	var $cost;
	var $descr;
	var $req;
	var $level;
	var $img_small;
	var $img_large;
	var $cast_description;
	
	function Card( $id )
	{
		$this->card_id = $id;
		$res = f_MQuery( "SELECT * FROM cards WHERE card_id={$this->card_id}" );
		$arr = f_MFetch( $res );
		$this->name = $arr['name'];
		$this->genre = $arr['genre'];
		$this->price = $arr['price'];
		$this->cost = $arr['cost'];
		$this->descr = $arr['descr'];
		$this->req = $arr['req'];
		$this->level = $arr['level'];
		$this->img_small = $arr['image_small'];
		$this->img_large = $arr['image_large'];
		$this->cast_description = $arr['cast_description'];

		$str1 = $arr['descr'];
		$str1 = str_replace( "\r\n", '<br>', $str1 );
		$str1 = str_replace( "\r", '<br>', $str1 );
		$str1 = str_replace( "\n", '<br>', $str1 );

		$str2 = $arr['descr2'];
		$str2 = str_replace( "\r\n", '<br>', $str2 );
		$str2 = str_replace( "\r", '<br>', $str2 );
		$str2 = str_replace( "\n", '<br>', $str2 );

		$str3 = ""; echo "|".$this->req."|";
		$req = parseItemStr( $this->req );echo 1;
		if( $this->genre < 3 && $this->cost > 0 ) $req[100 + ( 3 + $this->genre ) * 10] = $this->cost;echo 1;
		$str3 = getReqStr( $req );                                                                           echo 1;
		if( $str3 != '' ) $str3 = "<br><b>Требует: $str3</b>";                                                      echo 1;

		$this->descr = "<b>$str2</b><br><i>$str1</i>$str3";
	}
	
	function Process( $my_id, $his_id, $slot, $combat_id )
	{
		include_once( 'combat_sdk.php' );
		$sdk = new sdk_t( $my_id, $his_id, $slot, $combat_id );

		if( file_exists( "spell_effects/spell{$this->card_id}.php" ) )
		{
			$str = file_get_contents( "spell_effects/spell{$this->card_id}.php" );
			$func = @create_function( '$sdk', $str );
			if( !function_exists( $func ) )
			{
				print( "<script>alert( 'Ошибка компиляции при попытке загрузить свиток \"{$this->name}\"\\nОтчет об ошибке занесен в лог, администрация игры попытается исправить ошибку в ближайшее время\\nПриносим извинения за доставленные неудобства.' );</script>" );
				LogError( "Ошибка компиляции при попытке загрузить свиток \"{$this->name}\"", $str );
			}
			else $func( $sdk );
		}
			
		return $sdk->log_msg;
	}
	
	function Process2( $me, $he, $we, $they, $combat_id, $turn )
	{
		include_once( 'combat_sdk2.php' );
		$sdk = new sdk_t( $me, $he, $we, $they, $turn, $combat_id );

		if( file_exists( "spell_effects/spell{$this->card_id}.php" ) )
		{
			$str = file_get_contents( "spell_effects/spell{$this->card_id}.php" );
			$func = @create_function( '$sdk', $str );
			if( !function_exists( $func ) )
			{
				print( "<script>alert( 'Ошибка компиляции при попытке загрузить свиток \"{$this->name}\"\\nОтчет об ошибке занесен в лог, администрация игры попытается исправить ошибку в ближайшее время\\nПриносим извинения за доставленные неудобства.' );</script>" );
				LogError( "Ошибка компиляции при попытке загрузить свиток \"{$this->name}\"", $str );
			}
			else $func( $sdk );
			$me->dmg_spells += $sdk->dmg;
		}
			
		return text_sex_parse( '[', '|', ']', text_sex_parse( '{', '|', '}', str_replace( "*victim*", $he->player->login, str_replace( "*player*", $me->player->login, $this->cast_description ) ), $me->player->sex ), $he->player->sex ) . $sdk->log_msg;
	}
	
	function Text( )
	{
		return "cn({$this->genre}, '{$this->name}', '{$this->descr}')";
	}

	function playerCanLearn( $player_id )
	{
		$req = parseItemStr( $this->req );
		foreach( $req as $stat=>$value )
		{
			$res = f_MQuery( "SELECT value FROM player_attributes WHERE player_id=$player_id AND attribute_id=$stat" );
			$arr = f_MFetch( $res );
			if( !$arr || $arr[0] < $value ) return false;
		}
		return true;
	}

	function Image( $id )
	{
		if( $id < 4 ) $q = 1;
		else $q = 0;
		return "cimg('{$this->img_large}', '{$this->name}', {$this->genre}, '{$this->descr}', $q )";
	}
};

function cardGetSmallIcon( $arr )
{
	$str1 = $arr['descr'];
	$str1 = str_replace( "\r\n", '<br>', $str1 );
	$str1 = str_replace( "\r", '<br>', $str1 );
	$str1 = str_replace( "\n", '<br>', $str1 );

	$str2 = $arr['descr2'];
	$str2 = str_replace( "\r\n", '<br>', $str2 );
	$str2 = str_replace( "\r", '<br>', $str2 );
	$str2 = str_replace( "\n", '<br>', $str2 );

    $descr = "<b>$str2</b><hr color=black size=1><i>$str1</i>";
    if( $arr['cost'] != 0 ) $descr = "$arr[cost] ".my_word_str( $arr['cost'], "мана", "маны", "маны" )."<br>".$descr;
	return( "csimg( $arr[genre], '$arr[name]', '$arr[image_small]', '$descr' )" );
}

function cardGetSmallIconEmpty( )
{
	return( "csimg( 3, 'Неизвестное заклинание', 'unknown_small.gif', '' )" );
}

?>
