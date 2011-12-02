<?

$feathers_regime0 = array( 0, 1 );
$feathers_combat = array( 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 29, 30, 31, 35 );
$feathers_hour = array( 2, 4 );
$feathers_2hour = array( 32, 33, 34 );

// 28 - dispell
// 36 - obereg

$featherErr = '';

function canUseFeather( $player, $silent = false )
{
	if( $player->regime != 0 )
	{
		if( !$silent ) echo "alert('Вы не свободны для использования перышек.');";
		return false;
	}
	if( $player->location == 2 && $player->depth == 43 )
	{
		if( !$silent ) echo "alert('Нельзя использовать перья в Зале Турниров!');";
		return false;
	}
	return true;
}

$dont_check_feather = false;
function featherCheckLimit( $lim, $player, $id )
{
	global $featherErr;
	global $dont_check_feather;
	if( $dont_check_feather ) return true;
	
	f_MQuery( "LOCK TABLE player_feathers WRITE" );
	$num = f_MValue( "SELECT count( feather_id ) FROM player_feathers WHERE player_id={$player->player_id} AND feather_id={$id}" );
	if( $num >= $lim )
	{
		$featherErr = "Вы не можете прицепить это перышко. На игрока уже прицеплено столько перышек этого типа, что его можно принять за большую заморскую птицу.";
		f_MQuery( "UNLOCK TABLES" );
		return false;
	}
	$tm = time( );
	f_MQuery( "INSERT INTO player_feathers ( player_id, feather_id, time ) VALUES ( {$player->player_id}, {$id}, {$tm} )" );
	f_MQuery( "UNLOCK TABLES" );
	return true;
}

function doFeather( $player, $id )
{
	if( $id == 0 )
	{
		if( featherCheckLimit( 1, $player, $id ) ) 
			$player->SetTrigger( 400 );
		else return false;
	}
	if( $id == 1 )
	{
		if( featherCheckLimit( 1, $player, $id ) ) 
			$player->SetTrigger( 401 );
		else return false;
	}
	if( $id == 2 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 402 );
		else return false;
	}
	if( $id == 3 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 403 );
		else return false;
	}
	if( $id == 4 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 404 );
		else return false;
	}
	if( $id == 5 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 150, 3 );
		else return false;
	}
	if( $id == 6 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 140, 3 );
		else return false;
	}
	if( $id == 7 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 130, 3 );
		else return false;
	}
	if( $id == 8 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 16, 1 );
		else return false;
	}
	if( $id == 9 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 15, 1 );
		else return false;
	}
	if( $id == 10 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 13, 1 );
		else return false;
	}
	if( $id == 11 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 16, -1 );
		else return false;
	}
	if( $id == 12 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 15, -1 );
		else return false;
	}
	if( $id == 13 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 13, -1 );
		else return false;
	}
	if( $id == 14 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 502, 1 );
		else return false;
	}
	if( $id == 15 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 405 );
		else return false;
	}
	if( $id == 16 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 131, 1 );
		else return false;
	}
	if( $id == 17 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 151, 1 );
		else return false;
	}
	if( $id == 18 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 141, 1 );
		else return false;
	}
	if( $id == 22 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 132, 1 );
		else return false;
	}
	if( $id == 23 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 152, 1 );
		else return false;
	}
	if( $id == 24 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 142, 1 );
		else return false;
	}
	if( $id == 19 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 131, -1 );
		else return false;
	}
	if( $id == 20 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 151, -1 );
		else return false;
	}
	if( $id == 21 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 141, -1 );
		else return false;
	}
	if( $id == 25 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 132, -1 );
		else return false;
	}
	if( $id == 26 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 152, -1 );
		else return false;
	}
	if( $id == 27 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 142, -1 );
		else return false;
	}

	if( $id == 29 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 406 );
		else return false;
	}
	if( $id == 30 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 407 );
		else return false;
	}
	if( $id == 31 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 408 );
		else return false;
	}
	if( $id == 32 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterRealAttrib( 30, 1 );
		else return false;
	}
	if( $id == 33 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterRealAttrib( 40, 1 );
		else return false;
	}
	if( $id == 34 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterRealAttrib( 50, 1 );
		else return false;
	}
	if( $id == 35 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 1, 50 );
		else return false;
	}
	if( $id == 36 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->SetTrigger( 409 );
		else return false;
	}
	return true;
}

function undoFeather( $player, $id )
{
	if( $id == 0 )
	{
		$player->SetTrigger( 400, 0 );
	}
	if( $id == 1 )
	{
		$player->SetTrigger( 401, 0 );
	}
	if( $id == 2 )
	{
		$player->SetTrigger( 402, 0 );
	}
	if( $id == 3 )
	{
		$player->SetTrigger( 403, 0 );
	}
	if( $id == 4 )
	{
		$player->SetTrigger( 404, 0 );
	}
	if( $id == 5 )
	{
		$player->AlterAttrib( 150, -3 );
	}
	if( $id == 6 )
	{
		$player->AlterAttrib( 140, -3 );
	}
	if( $id == 7 )
	{
		$player->AlterAttrib( 130, -3 );
	}
	if( $id == 8 )
	{
		$player->AlterAttrib( 16, -1 );
	}
	if( $id == 9 )
	{
		$player->AlterAttrib( 15, -1 );
	}
	if( $id == 10 )
	{
		$player->AlterAttrib( 13, -1 );
	}
	if( $id == 11 )
	{
		$player->AlterAttrib( 16, 1 );
	}
	if( $id == 12 )
	{
		$player->AlterAttrib( 15, 1 );
	}
	if( $id == 13 )
	{
		$player->AlterAttrib( 13, 1 );
	}
	if( $id == 14 )
	{
		$player->AlterAttrib( 502, -1 );
	}
	if( $id == 15 )
	{
		$player->SetTrigger( 405, 0 );
	}
	if( $id == 16 )
	{
		$player->AlterAttrib( 131, -1 );
	}
	if( $id == 17 )
	{
		$player->AlterAttrib( 151, -1 );
	}
	if( $id == 18 )
	{
		$player->AlterAttrib( 141, -1 );
	}
	if( $id == 22 )
	{
		$player->AlterAttrib( 132, -1 );
	}
	if( $id == 23 )
	{
		$player->AlterAttrib( 152, -1 );
	}
	if( $id == 24 )
	{
		$player->AlterAttrib( 142, -1 );
	}
	if( $id == 19 )
	{
		$player->AlterAttrib( 131, 1 );
	}
	if( $id == 20 )
	{
		$player->AlterAttrib( 151, 1 );
	}
	if( $id == 21 )
	{
		$player->AlterAttrib( 141, 1 );
	}
	if( $id == 25 )
	{
		$player->AlterAttrib( 132, 1 );
	}
	if( $id == 26 )
	{
		$player->AlterAttrib( 152, 1 );
	}
	if( $id == 27 )
	{
		$player->AlterAttrib( 142, 1 );
	}

	if( $id == 29 )
	{
		$player->SetTrigger( 406, 0 );
	}
	if( $id == 30 )
	{
		$player->SetTrigger( 407, 0 );
	}
	if( $id == 31 )
	{
		$player->SetTrigger( 408, 0 );
	}
	if( $id == 32 )
	{
		$player->AlterRealAttrib( 30, -1 );
	}
	if( $id == 33 )
	{
		$player->AlterRealAttrib( 40, -1 );
	}
	if( $id == 34 )
	{
		$player->AlterRealAttrib( 50, -1 );
	}
	if( $id == 35 )
	{
		$player->AlterAttrib( 1, -50 );
	}
	if( $id == 36 )
	{
		$player->SetTrigger( 409, 0 );
	}
}


// autogenerated code
$fthrs[0] = array( 'Белое Перышко', 'feathers/1a.gif', 'После следующего или уже начатого дозора время, потраченное на него, не вычтется из \"дозорного\" времени на этот день с шансом в 50%.' );
$fthrs[1] = array( 'Серое Перышко', 'feathers/1b.gif', 'При поиске камней игрок сможет вскрыть четыре клетки вместо трех. После поиска камней пёрышко пропадает.' );
$fthrs[10] = array( 'Оранжевое Перышко', 'feathers/3c.gif', 'Увеличивает игроку Удачу на 1 пункт в следующем или в текущем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[11] = array( 'Фиолетовое Перышко в Черную Полоску', 'feathers/4a.gif', 'Уменьшает игроку Критический удар на 1 пункт в следующем или в текущем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[12] = array( 'Серо-Голубое Перышко в Черную Полоску', 'feathers/4b.gif', 'Уменьшает игроку Отдачу на 1 пункт в следующем или в текущем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[13] = array( 'Оранжевое Перышко в Черную Полоску', 'feathers/4c.gif', 'Уменьшает игроку Удачу на 1 пункт в следующем или в текущем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[14] = array( 'Желтое Перышко', 'feathers/5a.gif', 'В следующем или в текущем бою при ударах вничью игрок будет получать на 1 единицу урона меньше. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[15] = array( 'Голубое Перышко', 'feathers/5b.gif', 'До конца следующего или текущего боя у игрока будут автоматически форсироваться ходы.' );
$fthrs[16] = array( 'Синее Перышко в Белую Полоску', 'feathers/6a.gif', 'Увеличивает игроку атаку магии Воды на 1 пункт в текущем или в следующем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[17] = array( 'Красное Перышко в Белую Полоску', 'feathers/6b.gif', 'Увеличивает игроку атаку магии Огня на 1 пункт в текущем или в следующем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[18] = array( 'Зеленое Перышко в Белую Полоску', 'feathers/6c.gif', 'Увеличивает игроку атаку магии Природы на 1 пункт в текущем или в следующем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[19] = array( 'Синее Перышко в Черную Полоску', 'feathers/7a.gif', 'Уменьшает игроку атаку магии Воды на 1 пункт в текущем или в следующем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[2] = array( 'Черное Перышко', 'feathers/1c.gif', 'Поиск цветов в лесу длится в два раза меньше. <br /><b>Перышко держится один час.</b>' );
$fthrs[20] = array( 'Красное Перышко в Черную Полоску', 'feathers/7b.gif', 'Уменьшает игроку атаку магии Огня на 1 пункт в текущем или в следующем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[21] = array( 'Зеленое Перышко в Черную Полоску', 'feathers/7c.gif', 'Уменьшает игроку атаку магии Природы на 1 пункт в текущем или в следующем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[22] = array( 'Синее Перышко в Белые Пятнышки', 'feathers/8a.gif', 'Увеличивает игроку защиту магии Воды на 1 пункт в текущем или в следующем бою. Можно прицепить на игрока уровня А не более А раз за один бой. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[23] = array( 'Красное Перышко в Белые Пятнышки', 'feathers/8b.gif', 'Увеличивает игроку защиту магии Огня на 1 пункт в текущем или в следующем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[24] = array( 'Зеленое Перышко в Белые Пятнышки', 'feathers/8c.gif', 'Увеличивает игроку защиту магии Природы на 1 пункт в текущем или в следующем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[25] = array( 'Синее Перышко в Черные Пятнышки', 'feathers/9a.gif', 'Уменьшает игроку защиту магии Воды на 1 пункт в текущем или в следующем бою.  Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[26] = array( 'Красное Перышко в Черные Пятнышки', 'feathers/9b.gif', 'Уменьшает игроку защиту магии Огня на 1 пункт в текущем или в следующем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[27] = array( 'Зеленое Перышко в Черные Пятнышки', 'feathers/9c.gif', 'Уменьшает игроку защиту магии Природы на 1 пункт в следующем или в текущем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[28] = array( 'Белоснежное Перышко', 'feathers/10.gif', 'Снимает с игрока эффект любого другого пёрышка.' );
$fthrs[29] = array( 'Восхитительное Фиолетовое Перышко', 'feathers/11.gif', 'В конце следующего или уже начатого боя у игрока гарантированно сломается <b>оружие в левой руке.</b> Можно прицепить только к игроку, к которому не прицеплено ни одного восхитительного фиолетового, оранжевого или темного-синего перышка.' );
$fthrs[3] = array( 'Черное Перышко в Белую Полоску', 'feathers/1d.gif', 'В конце следующего или текущего боя у игрока гарантированно не сломается никакая вещь.' );
$fthrs[30] = array( 'Восхитительное Оранжевое Перышко', 'feathers/12.gif', 'В конце следующего или уже начатого боя у игрока гарантированно сломается <b>оружие в правой руке.</b> Можно прицепить только к игроку, к которому не прицеплено ни одного восхитительного фиолетового, оранжевого или темного-синего перышка.' );
$fthrs[31] = array( 'Восхитительное Темно-Синее Перышко', 'feathers/13.gif', 'В конце следующего или уже начатого боя у игрока гарантированно сломается <b>амулет.</b> Можно прицепить только к игроку, к которому не прицеплено ни одного восхитительного фиолетового, оранжевого или темного-синего перышка.' );
$fthrs[32] = array( 'Восхитительное Синее Перышко', 'feathers/14.gif', 'Увеличивает игроку магию Воды на 1 пункт. Перышко держится <b>2 часа.</b><br />Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него <b>за день.</b>' );
$fthrs[33] = array( 'Восхитительное Зеленое Перышко', 'feathers/15.gif', 'Увеличивает игроку магию Природы на 1 пункт. Перышко держится <b>2 часа.</b><br />Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него <b>за день.</b>' );
$fthrs[34] = array( 'Восхитительное Красное Перышко', 'feathers/16.gif', 'Увеличивает игроку магию Огня на 1 пункт. Перышко держится <b>2 часа.</b> <br />Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него <b>за день.</b>' );
$fthrs[35] = array( 'Восхитительное Золотое Перышко', 'feathers/17.gif', 'Игрок восстанавливает 50 единиц здоровья. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[36] = array( 'Восхитительное Сияющее Перышко', 'feathers/17a.gif', 'При поражении во время исследования подземелий близ Пещер игрок с шансом 25% сможет продолжить исследование, если он ходит по подземелью <b>в одиночку</b> и у него не осталось оберегов. Перышко держится на игроке до тех пор, пока он не покинет подземелье.' );
$fthrs[4] = array( 'Белое Перышко в Черную Полоску', 'feathers/1e.gif', 'Любой начатый дозор с шансом в 50% будет длиться в 5 раз меньше. <br /><b>Перышко держится один час.</b>' );
$fthrs[5] = array( 'Красное Перышко', 'feathers/2a.gif', 'Увеличивает игроку Ману Огня на 3 пункта в следующем или в текущем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[6] = array( 'Зеленое Перышко', 'feathers/2b.gif', 'Увеличивает игроку Ману Природы на 3 пункта в следующем или в текущем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[7] = array( 'Синее Перышко', 'feathers/2c.gif', 'Увеличивает игроку Ману Воды на 3 пункта в следующем или в текущем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[8] = array( 'Фиолетовое Перышко', 'feathers/3a.gif', 'Увеличивает игроку Критический удар на 1 пункт в следующем или в текущем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );
$fthrs[9] = array( 'Серо-Голубое Перышко', 'feathers/3b.gif', 'Увеличивает игроку Отдачу на 1 пункт в следующем или в текущем бою. Чем больше уровень игрока, тем больше таких пёрышек можно прицепить на него.' );


?>
