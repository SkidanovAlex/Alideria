<?

if( !$mid_php ) die( );

include_once( 'craft_functions.php' );


if( $player->level < 2 )
{
	echo "<center><i>Вы еще слишком малы. Возвращайтесь, когда достигнете 2-ого уровня.</i></center>";
	return;
}

include_once( "guild.php" );

$max_guilds_num = GuildsPerLevel( $player->level );
$extra_guilds = 0; // To be calculated in the next part of code

// 1. Load player guilds
$res = f_MQuery( "SELECT guild_id, rank, rating FROM player_guilds WHERE player_id = {$player->player_id}" );


if( !f_MNum( $res ) ) echo "Вы не состоите ни в одной гильдии. Вы можете состоять в $max_guilds_num ".my_word_str( $max_guilds_num, "гильдии", "гильдиях", "гильдиях" ).". ";
else
{
	echo "Вы состоите в <b>".f_MNum( $res )."</b> ".my_word_str( f_MNum( $res), "гильдии", "гильдиях", "гильдиях" ).". ";
	if( f_MNum( $res ) > $max_guilds_num )
	{
		LogError( "Игрок состоит более чем в $max_guilds_num гильдиях на {$player->level} уровне." );
		echo "Это больше допустимого. пожалуйста, сообщите администрации об ошибке.";
	}
	else if( f_MNum( $res ) == $max_guilds_num )
	{
		echo "Вы не можете вступить в другие гильдии.";
	}
	else
	{
		$extra_guilds = ($max_guilds_num - f_MNum( $res ));
		echo "Вы можете вступить еще в ".$extra_guilds." ".my_word_str( $extra_guilds, "гильдию", "гильдии", "гильдий" ).". ";
	}
}
echo "<br>";

if( !$_GET['guild_id'] )
{
	if( f_MNum( $res) )
	{
		echo "<br><b>Вы состоите в следующих гильдиях:</b><br>";
		while( $arr = f_MFetch( $res ) )
		{
			$guild_id = $arr[guild_id];
			echo "<li><a href=game.php?guild_id=$guild_id>{$guilds[$guild_id][0]}</a><br>";
		}
	}
	echo "<br><b>Подойти к представителям гильдии</b><br>";
	foreach( $guilds as $a=>$b )
	{
		echo "<li><a href=game.php?guild_id=$a>$b[0]</a><br>";
	}
}
else if( isset( $_GET['government_work'] ) )
{
	$guild_id = $_GET['guild_id'];
	settype( $guild_id, 'integer' );
	if( !isset( $guilds[$guild_id] ) ) RaiseError( "Попытка получить гос.заказ от несуществующей гильдии", "guild_id: $guild_id" );
	if( !$guilds[$guild_id][3] || $guilds[$guild_id][4] )
		RaiseError( "Попытка получить гос.заказ от добывающей или не крафтерской гильдии", "Айди гильдии: $guild_id, Имя игрока: {$player->login}" );

	f_MQuery( "delete from player_government_work where player_id={$player->player_id} and guild_id NOT IN ( select guild_id from player_guilds where player_id={$player->player_id} );" );

	$guild = new Guild( $guild_id );
	if( $guild->LoadPlayer( $player->player_id ) )
	{
		echo "<br><b>Гильдия {$guilds[$guild_id][0]} - государственные заказы</b><br><br>";
		$tm = time( );
		$res = f_MQuery( "SELECT expires FROM player_government_delays WHERE player_id = {$player->player_id} AND expires > $tm + 2 AND guild_id=$guild_id" );
		$arr = f_MFetch( $res );

		if( !$arr && isset( $_GET['cancel'] ) )
		{
			$expires = time( ) + 4 * 60 * 60;
			f_MQuery( "LOCK TABLES player_government_work WRITE, player_government_delays WRITE" );
			f_MQuery( "DELETE FROM player_government_work WHERE player_id={$player->player_id} AND guild_id={$guild_id}" );
			f_MQuery( "INSERT INTO player_government_delays VALUES( {$player->player_id}, $expires, $guild_id )" );
			f_MQuery( "UNLOCK TABLES" );
			$res = f_MQuery( "SELECT expires FROM player_government_delays WHERE player_id = {$player->player_id} AND expires > $tm + 2 AND guild_id=$guild_id" );
			$arr = f_MFetch( $res );
		}
		if( !$arr && isset( $_GET['finish'] ) )
		{
			f_MQuery( "LOCK TABLES player_government_work WRITE, player_government_delays WRITE, recipes READ, items READ, player_items WRITE" );
			$res = f_MQuery( "SELECT * FROM player_government_work WHERE player_id={$player->player_id} AND guild_id=$guild_id" );
			$arr = f_MFetch( $res );
			$number = $arr['number'];
			$prize = $arr['prize'];
			if( !$arr ) RaiseError( "Попытка завершить гос.заказ, которого вообще говоря нет" );
			if( $arr['number'] != $arr['completed'] ) RaiseError( "Попытка завершить невыполненный гос.заказ" );
			$res = f_MQuery( "SELECT * FROM recipes WHERE prof=$guild_id AND recipe_id=$arr[recipe_id]" );
			$arr = f_MFetch( $res );
			$items = ParseItemStr( $arr['result'] );
			$ok = true;
			foreach( $items as $item_id=>$num )
			{
				$num *= $number;
				if( $player->NumberItems( $item_id ) < $num )
				{
					$ok = false;
					$player->syst( "Не все вещи, полученные при выполнении заказа, находятся у вас в инвентаре." );
					break;
				}
			}
			if( $ok )
			{
				$expires = time( ) + 4 * 60 * 60;
				f_MQuery( "DELETE FROM player_government_work WHERE player_id={$player->player_id} AND guild_id={$guild_id}" );
				f_MQuery( "INSERT INTO player_government_delays VALUES( {$player->player_id}, $expires, $guild_id )" );
				f_MQuery( "UNLOCK TABLES" );
				$res = f_MQuery( "SELECT expires FROM player_government_delays WHERE player_id = {$player->player_id} AND expires > $tm + 2 AND guild_id=$guild_id" );
				$arr = f_MFetch( $res );

				f_MQuery( "UNLOCK TABLES" );
				foreach( $items as $item_id=>$num )
				{
					$num *= $number;
					$player->DropItems( $item_id, $num );
				}
				$player->AddMoney( $prize );
				$player->syst( "Вы успешно завершаете государственный заказ и получаете <b>$prize</b> дублонов!" );
			}
			else
			{
				f_MQuery( "UNLOCK TABLES" );
				$arr = false;
			}
		}

		if( $arr && $arr['expires'] > time( ) - 2 )
		{
			echo "<i>Вы недавно завершили или отклонили государственный заказ.</i><br>";
			include_js( 'js/timer.js' );
			echo "<script>document.write( InsertTimer( ".($arr[0] - $tm).", '<i>Вы не можете получить новый заказ еще <b>', '</b></i>', 1, 'location.href=\"game.php?guild_id=$guild_id&government_work=1\"' ) );</script>";
			echo "<ul>";
			echo "<li><a href=game.php?guild_id=$guild_id>Вернуться</a>";
			echo "</ul>";
		}
		else
		{
			f_MQuery( "LOCK TABLES player_government_work WRITE, recipes READ, items READ" );
			$res = f_MQuery( "SELECT * FROM player_government_work WHERE player_id={$player->player_id} AND guild_id=$guild_id" );
			$arr = f_MFetch( $res );
			if( !$arr )
			{
				$res = f_MQuery( "SELECT * FROM recipes WHERE prof=$guild_id AND level <= {$player->level} AND rank <= {$guild->rank} ORDER BY rand() LIMIT 1" );
				$arr = f_MFetch( $res );
				if( !$arr ) RaiseError( "Не найдено ни одного подходящего рецепта для гос.заказа", "Уровень {$player->level}, Ранг {$guild->rank}, Гильдия $guild_id" );
				craftGetItemsList( ParseItemStr( $arr['result'] ) );
				$number = 5000 / $craft_cost;
				settype( $number, 'integer' );
				if( $number < 1 ) $number = 1;
				$number = mt_rand( $number, $number * 2 );
				$prize = floor( $number * $craft_cost * 1.25 );
				f_MQuery( "INSERT INTO player_government_work VALUES( {$player->player_id}, $guild_id, $arr[recipe_id], $number, 0, $prize )" );
				$res = f_MQuery( "SELECT * FROM player_government_work WHERE player_id={$player->player_id} AND guild_id=$guild_id" );
				$arr = f_MFetch( $res );
			}
			f_MQuery( "UNLOCK TABLES" );
			echo "Доступен следующий заказ:<br>";
			$rres = f_MQuery( "SELECT * FROM recipes WHERE recipe_id=$arr[recipe_id]" );
			$rarr = f_MFetch( $rres );
			echo "Рецепт: <a href=help.php?id=1015&recipe_id=$rarr[recipe_id] target=_blank>$rarr[name]</a><br>";
			echo "Количество: <b>$arr[completed]/$arr[number]</b><br>";
			echo "Компенсация: <img src=images/money.gif width=11 height=11>&nbsp;<b>$arr[prize]</b><br>";
			echo "<ul>";
			if( $arr['completed'] == $arr['number'] ) echo "<li><a href=# onclick='if( confirm( \"Вы уверены, что хотите отдать все произведеные в рамках гос.заказа вещи и получить компенсацию в размере $arr[prize] дублонов?\" ) ) location.href=\"game.php?guild_id=$guild_id&government_work=1&finish=1\"'>Завершить заказ</a>";
			echo "<li><a href=# onclick='if( confirm( \"Действительно отказаться от заказа?\" ) ) location.href=\"game.php?guild_id=$guild_id&government_work=1&cancel=1\"'>Отказаться от заказа</a>";
			echo "<li><a href=game.php?guild_id=$guild_id>Вернуться</a>";
			echo "</ul>";
			echo "<small>Если вам не нравится заказ, нажмите кнопку &quot;Отказаться&quot;.<br>В случае отказа вы не сможете получить новый заказ в течение 4-ех часов.<br>Если вы готовы работать над текущим заказом, нажмите кнопку &quot;Вернуться&quot;.<br>Для того чтобы выполнить заказ, необходимо соответствующее количество раз создать вещи по указанному рецепту, после чего принести их сюда.<br>Заказ обязательно должен быть выполнен вами в мастерской по указанному рецепту! Купленные или найденные вещи не могут быть сданы в зачет гос.заказа.<br>Информацию о всех гос.заказах можно найти в дневнике в списке текущих заданий.";
		}
	}
	else
		RaiseError( "Попытка получить гос.заказ от гильдии, в которой игрок не состоит", "Айди гильдии: $guild_id, Имя игрока: {$player->login}" );
}
else
{
	$guild_id = $_GET['guild_id'];
	settype( $guild_id, 'integer' );
	if( !isset( $guilds[$guild_id] ) ) RaiseError( "Попытка подойти к представителям несуществующей гильдии", "guild_id: $guild_id" );

	$player_loaded = false;
	$guild = new Guild( $guild_id );
	if( $guild->LoadPlayer( $player->player_id ) )
		$player_loaded = true;

	echo "<br><b>Гильдия {$guilds[$guild_id][0]}</b><br><br><li><a href=help.php?id={$guilds[$guild_id][1]} target=_blank>О Гильдии {$guilds[$guild_id][0]}</a><br><li><a href=guilds_table.php?page=$guild_id target=_blank>Стена гильдийной славы</a><li><a href=game.php?talk={$guilds[$guild_id][2]}>Поговорить с Мастером</a><br>";
	if( $player_loaded && $guilds[$guild_id][3] && !$guilds[$guild_id][4] )
		echo "<li><a href=game.php?guild_id=$guild_id&government_work=1>Государственные Заказы</a><br>";
	echo "<a href=game.php><li>Назад</a><br><br>";

	if( $player_loaded )
	{
		if( isset( $_GET['inc_rank'] ) )
		{
			$price = $rank_prices[$guild->rank];
			if ($guild->rank < $player->level)
			if( $player->prof_exp >= $price )
			{
				++ $guild->rank;
				$player->prof_exp -= $price;
				f_MQuery( "UPDATE player_guilds SET rank = rank + 1 WHERE player_id = {$player->player_id} AND guild_id = $guild_id" );
				f_MQuery( "UPDATE characters SET prof_exp = prof_exp - $price WHERE player_id = {$player->player_id}" );
				UpdateTitle( );
				checkZhorik( $player, 2, 1 ); // квест жорика повысить ранг в гильдии
			}
		}
		if( isset( $_GET['inc_rating'] ) && ( $guilds[$guild_id][4] == 1 /*|| $guild_id == SMITH_GUILD || $guild_id == JEWELRY_GUILD*/ ) )
		{
			$price = $rank_prices[$guild->rating];
			if ($guild->rating < $player->level * 2)
			if( $player->prof_exp >= $price )
			{
				++ $guild->rating;
				$player->prof_exp -= $price;
				f_MQuery( "UPDATE player_guilds SET rating = rating + 1 WHERE player_id = {$player->player_id} AND guild_id = $guild_id" );
				f_MQuery( "UPDATE characters SET prof_exp = prof_exp - $price WHERE player_id = {$player->player_id}" );
				UpdateTitle( );
				checkZhorik( $player, 3, 1 ); // квест жорика повысить рейтинг в гильдии
			}
		}

		echo "<b>Вы состоите в этой гильдии.</b><br>";
		echo "Уровень вашего профессионального опыта: <b>{$player->prof_exp}</b><br><br>";
		echo "Ваш Ранг: <b>{$guild->rank}</b><br>";
		echo "Стоимость повышения: <b>".$rank_prices[$guild->rank]."</b> единиц Проф. Опыта";
		if ($guild->rank < $player->level)
            echo "<img src=images/e_plus.gif width=11 height=11 border=0 title='Повысить' alt='Повысить' onclick='if( confirm( \"Повысить ранг?\" ) ) { location.href=\"game.php?inc_rank=1&guild_id=$guild_id\"; }' style='cursor:pointer'><br>";
		else
            echo ". Возвращайтесь, когда подрастете.<br>";
		echo "<small>Ранг влияет на ваши возможности в гильдии.<br>Подробнее читайте в <a href=help.php?id={$guilds[$guild_id][1]} target=_blank>энциклопедии</a><br><br></small>";

		if( $guilds[$guild_id][4] == 1 /*|| $guild_id == SMITH_GUILD || $guild_id == JEWELRY_GUILD*/ )
		{
			echo "Ваш Рейтинг: <b>{$guild->rating}</b><br>";
			echo "Стоимость повышения: <b>".$rank_prices[$guild->rating]."</b> единиц Проф. Опыта";
			if ($guild->rating < 2 * $player->level)
                echo "<img src=images/e_plus.gif width=11 height=11 border=0 title='Повысить' alt='Повысить' onclick='if( confirm( \"Повысить рейтинг?\" ) ) { location.href=\"game.php?inc_rating=1&guild_id=$guild_id\"; }' style='cursor:pointer'><br>";
            else
                echo ". Возвращайтесь, когда подрастете.<br>";
			if( $guilds[$guild_id][4] == 1 ) echo "<small>Рейтинг влияет на вашу среднюю прибыль.</small>";
			else echo "<small>Рейтинг влияет на вашу продуктивность у Алтаря.</small>";
		}
	}

}

?>


