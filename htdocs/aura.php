<?

include_once( "functions.php" );

class Aura
{
	var $aura_id;
	var $name;
	var $genre;
	var $level;
	
	function Aura( $id )
	{
		$this->aura_id = $id;
		$res = f_MQuery( "SELECT * FROM auras WHERE aura_id={$this->aura_id}" );
		$arr = f_MFetch( $res );
		$this->name = $arr['name'];
		$this->genre = $arr['genre'];
		$this->level = $arr['level'];
	}
	
	function Enchant( $my_id, $combat_id, $duration )
	{
		include_once( 'combat_sdk.php' );
		$sdk = new sdk_t( $my_id, $my_id, 0, $combat_id );

		if( file_exists( "spell_effects/aura{$this->aura_id}.php" ) )
		{
			$str = file_get_contents( "spell_effects/aura{$this->aura_id}.php" );
			$func = @create_function( '$sdk', $str );
			if( !function_exists( $func ) )
			{
				print( "<script>alert( 'Ошибка компиляции при попытке загрузить ауру \"{$this->name}\"\\nОтчет об ошибке занесен в лог, администрация игры попытается исправить ошибку в ближайшее время\\nПриносим извинения за доставленные неудобства.' );</script>" );
				LogError( "Ошибка компиляции при попытке загрузить ауру \"{$this->name}\"", $str );
			}
			else $func( $sdk );
		}
		
		f_MQuery( "INSERT INTO combat_auras ( player_id, combat_id, aura_id, duration ) VALUES ( $my_id, $combat_id, {$this->aura_id}, $duration )" );
			
		return $sdk->log_msg;
	}
	
	function Enchant2( $me, $combat_id, $duration )
	{
		include_once( 'combat_sdk2.php' );
		$dummy = 0;
		$sdk = new sdk_t( $me, $me, Array(), Array(), $dummy, $combat_id, $this->genre, false );

		if( file_exists( "spell_effects/aura{$this->aura_id}.php" ) )
		{
			$str = file_get_contents( "spell_effects/aura{$this->aura_id}.php" );
			$func = @create_function( '$sdk', $str );
			if( !function_exists( $func ) )
			{
				print( "alert( 'Ошибка компиляции при попытке загрузить ауру \"{$this->name}\"\\nОтчет об ошибке занесен в лог, администрация игры попытается исправить ошибку в ближайшее время\\nПриносим извинения за доставленные неудобства.' );" );
				LogError( "Ошибка компиляции при попытке загрузить ауру \"{$this->name}\"", $str );
			}
			else $func( $sdk );
		}
		
        ++ $duration;
		f_MQuery( "INSERT INTO combat_auras ( player_id, combat_id, aura_id, duration ) VALUES ( {$me->player->player_id}, $combat_id, {$this->aura_id}, $duration )" );
			
		return $sdk->log_msg;
	}
	
	function Dispell( $my_id, $combat_id )
	{
		include_once( 'combat_sdk.php' );
		$sdk = new sdk_t( $my_id, $my_id, 0, $combat_id );

		if( file_exists( "spell_effects/aura{$this->aura_id}dispell.php" ) )
		{
			$str = file_get_contents( "spell_effects/aura{$this->aura_id}dispell.php" );
			$func = @create_function( '$sdk', $str );
			if( !function_exists( $func ) )
			{
				print( "<script>alert( 'Ошибка компиляции при попытке загрузить ауру \"{$this->name}\"\\nОтчет об ошибке занесен в лог, администрация игры попытается исправить ошибку в ближайшее время\\nПриносим извинения за доставленные неудобства.' );</script>" );
				LogError( "Ошибка компиляции при попытке загрузить диспелл ауры \"{$this->name}\"", $str );
			}
			else $func( $sdk );
		}
			
		return $sdk->log_msg;
	}

	function Dispell2( $me, $combat_id )
	{
		include_once( 'combat_sdk2.php' );
		$dummy = 0;
		$sdk = new sdk_t( $me, $me, Array(), Array(), $dummy, $combat_id, $this->genre, false );

		if( file_exists( "spell_effects/aura{$this->aura_id}dispell.php" ) )
		{
			$str = file_get_contents( "spell_effects/aura{$this->aura_id}dispell.php" );
			$func = @create_function( '$sdk', $str );
			if( !function_exists( $func ) )
			{
				print( "alert( 'Ошибка компиляции при попытке загрузить ауру \"{$this->name}\"\\nОтчет об ошибке занесен в лог, администрация игры попытается исправить ошибку в ближайшее время\\nПриносим извинения за доставленные неудобства.' );" );
				LogError( "Ошибка компиляции при попытке загрузить диспелл ауры \"{$this->name}\"", $str );
			}
			else $func( $sdk );
		}
			
		return $sdk->log_msg;
	}
};

?>
