<?

if( !$mid_php )
	die( );
	
	
require_once( 'phrase.php' );

// Вызывается из game.php, сразу после запроса на плеер_толк, запрос не повторяем
// player уже тоже создан
$arr = f_MFetch( $res );
$talk_id = $arr[talk_id];
$npc_id = $arr[npc_id];
$attack = 0;

if( isset( $HTTP_GET_VARS[phrase] ) )
{
	$phrase_id = $HTTP_GET_VARS[phrase];
	settype( $phrase_id, 'integer' );
	$pres = f_MQuery( "SELECT phrases.* FROM phrases, talk_phrases WHERE talk_phrases.talk_id = $talk_id AND talk_phrases.phrase_id = $phrase_id AND phrases.phrase_id = $phrase_id" );
	$parr = f_MFetch( $pres );


	if( $parr && allow_phrase( $phrase_id, false ) )
	{
		do_phrase( $phrase_id );

		//Особые фразы
		if( $phrase_id == 465 ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
		if( $phrase_id == 466 ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
		if( $phrase_id == 466 ) $player->syst2( '/items' );
		if( $phrase_id == 468 ) $player->syst2( '/items' );
		if( $phrase_id == 476 ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
		if( $phrase_id == 477 ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
		if( $phrase_id == 482 ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
		if( $phrase_id == 483 ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
		if( $phrase_id == 572 ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
		if( $phrase_id == 573 ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
		if( $phrase_id == 590 ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
		if( $phrase_id == 591 ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
		if( $parr['drop_mines'] ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
	
		// Покупка заклинания молчанки
		if( $phrase_id == 1332 )
		{
			if( $player->SpendUMoney( 10 ) )
			{
				$player->AddItems( 46608 );
				$player->syst2( 'Ты получаешь заклинание Молчание' );			
			}
			else
			{
				$player->syst2( 'У тебя нехватает талантов для покупки Молчания' );
			}
		}

		// Действие
		if( $parr['attack_id'] <= -1000000 )
			$parr['attack_id'] += 1000000;

		if( $parr['attack_id'] < 0 )
		{
			$talk_id = - $parr['attack_id'];
			if ($phrase_id == 1755)
				$npc_id = 153;
			if ($phrase_id == 1756)
				$npc_id = 154;
			if ($phrase_id == 1555)
				$npc_id = 135;
			f_MQuery( "UPDATE player_talks SET talk_id = $talk_id, npc_id = $npc_id WHERE player_id = {$player->player_id}" );
		}
		else if( $parr[attack_id] > 0 )
		{
			include( "mob.php" );
			
			// Выкидываем из разговора
			f_MQuery( "DELETE FROM player_talks WHERE player_id = {$player->player_id}" );
			$player->SetRegime( 0 );
			
			// И атакуем
			$mob = new Mob;
			$mob->CreateMob( $parr[attack_id], $loc, $player->depth );
			$mob->AttackPlayer( $player->player_id, 0, 0 );
			$st = "<br><font color=red><b>Внимание!</b> </font>Вас атакует <b>{$mob->name}</b>!!! <a href=combat.php>Продолжить</a><br>";
			$attack = 1;
		}
		else
		{
			f_MQuery( "DELETE FROM player_talks WHERE player_id = {$player->player_id}" );
			$player->SetRegime( 0 );
			die( "<script>location.href='game.php';</script>" );
		}
	}
}

$nres = f_MQuery( "SELECT * FROM npcs WHERE npc_id = $npc_id" );
$narr = f_MFetch( $nres );

$tres = f_MQuery( "SELECT * FROM talks WHERE talk_id = $talk_id" );
$tarr = f_MFetch( $tres );

//$timer_str = "";

if( $tarr['redir_to'] != 0 )
{
	$val = $player->GetQuestValue( 1 );
	if( !$val )
	{
		$val = time( ) + $tarr['redir_timer'];
		$player->SetQuestValue( 1, $val );
	}

	if( $val < time( ) + 2 )
	{
		$talk_id = $tarr['redir_to'];
		f_MQuery( "UPDATE player_talks SET talk_id=$talk_id WHERE player_id={$player->player_id}" );
		$player->SetQuestValue( 1, 0 );
        $tres = f_MQuery( "SELECT * FROM talks WHERE talk_id = $talk_id" );
        $tarr = f_MFetch( $tres );
	}

	else
	{
		include_js( 'js/timer.js' );
		$timer_str = "<script>document.write( InsertTimer( ".($val - time( )).", 'Подождите: <b>', '</b>', 1, 'location.href=\"game.php\"' ) );</script>";
	}
}

if( $talk_id == 219 ) // donat
{
	include( 'happiness.php' );
	return;
}

print( "<center><table width=90%><tr><td><script>FUct();</script>" );
print( "<b>{$player->login}</b> разговаривает с <b>$narr[name]</b><script>FL();</script><br>" );

if( $narr['image'] != '' )
{
	echo "<table><tr><td valign=top>";
	if( !$narr['image_right'] ) echo "<img width=$narr[image_w] height=$narr[image_h] src='images/npcs/$narr[image]'></td><td valign=top>";
}

$tarr['flavor_text'] = str_replace( '{name}', $player->login, $tarr[flavor_text] );
if( trim( $tarr['flavor_text'] ) != '' )
	print( "<i>".text_sex_parse( '{', '|', '}', $tarr[flavor_text], $player->sex )."</i><br><br>" );
	
// специальные толки - в специальных толках атак быть не должно, иначе будет баг :оО
if( $talk_id == 38 )        include( "red_black_game.php" );
else if( $talk_id == 162 )  include( "quest_scripts/phrase162_alchemy_ships.php" );
else if( $talk_id == 182 )  include( "quest_scripts/phrase182_naperstki.php" );
else if( $talk_id == 194 )  include( "quest_scripts/phrase194_flipflop.php" );
else if( $talk_id == 195 )  include( "quest_scripts/phrase195_quiz.php" );
else if( $talk_id == 196 )  include( "quest_scripts/phrase196_attack.php" );
else if( $talk_id == 202 )  include( "quest_scripts/phrase202_mines.php" );
else if( $talk_id == 206 )  include( "quest_scripts/phrase206_lock.php" );
else if( $talk_id == 211 )  include( "quest_scripts/phrase211_dragon.php" );
else if( $talk_id == 224 )  include( "quest_scripts/phrase224_ships_2.php" );
else if( $talk_id == 242 )  include( "quest_scripts/phrase242_harecage.php" );
else if( $talk_id == 249 )  include( "quest_scripts/phrase249_case.php" );
else if( $talk_id == 252 )  include( "quest_scripts/phrase252_network.php" );
else if( $talk_id == 259 )  include( "quest_scripts/phrase259_zhorik.php" );
else if( $talk_id == 274 )  include( "quest_scripts/phrase274_nevesom.php" );
else if( $talk_id == 280 )  include( "quest_scripts/phrase280_illusion.php" );
else if( $talk_id == 281 )  include( "quest_scripts/phrase281_grez.php" );
else if( $talk_id == 278 )  include( "quest_scripts/phrase278_werewolf.php" );
else if( $talk_id == 556 )  include( "quest_scripts/phrase556_pairs.php" );
else if( $talk_id == 557 )  include( "quest_scripts/phrase557_pairs_award.php" );

else if( $tarr['text'][0] == '/' )
{
	include( "quest_scripts" . $tarr['text'] );
}

else // нормальный толк
{
	
	$tarr['text'] = str_replace( '{name}', $player->login, $tarr[text] );
	$tarr['text'] = text_sex_parse( '{', '|', '}', $tarr[text], $player->sex );

	if( $talk_id == 82 ) // толк мастера собирателей про координаты ягод
	{
		$coors = f_MFetch( f_MQuery( "SELECT depth FROM forest_tiles WHERE tile=0 ORDER BY rand() LIMIT 1" ) );
		if( !$coors ) RaiseError( "Это удивительно, но в лесу НЕТ ОПУШКИ!!!", "" );
		f_MQuery( "LOCK TABLE player_berry_places WRITE" );
		f_MQuery( "DELETE FROM player_berry_places WHERE player_id={$player->player_id}" );
		$expires = time( ) + 3600;
		f_MQuery( "INSERT INTO player_berry_places VALUES ( {$player->player_id}, $coors[0], $expires )" );
		f_MQuery( "UNLOCK TABLES" );

		$x = $coors[0] / 100;
		$y = $coors[0] % 100;
		settype( $x, 'integer' );
		$x = ($x + 50) % 100;

		$tarr[text] = "Из того, что я слышал сегодня от других членов гильдии, стоит попытать счастья на $x:$y. Но поспеши, я думаю уже через час ягод там не будет и впомине.";
	}
	if( $talk_id == 671 ) // widow quest
	{
		include_once( "quest_race.php" );
		processPlayerWin( $player->player_id, $player->level );
		glashSay( "Персонаж <b>{$player->login}</b> выиграл всеалидерийские догонялки!" );
		$tarr[text] = str_replace( "*награда*", $questRacePrizeStr, $tarr[text] );
		f_MQuery( "DELETE FROM player_talks WHERE player_id={$player->player_id}" );
		$player->SetRegime( 0 );

    	$msg = "Персонаж <b>{$player->login}</b> выиграл догонялки! Ты, к сожалению, не успел. Ничего, повезет в следующий раз.";
    	$msg_all = "Персонаж <b>{$player->login}</b> выиграл догонялки!";
    	
    	$qvres = f_MQuery( "SELECT player_id FROM player_triggers WHERE trigger_id=262 AND player_id <> {$player->player_id}" );
    	
       	$plr = new Player( 1249423 );
        $plr->UploadInfoToJavaServer( );

    	while( $qvarr = f_MFetch( $qvres ) )
    	{
            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_connect($sock, "127.0.0.1", 1100);
            $tm = date( "H:i", time( ) );
            $st = "say\n{$msg}\n1249423\n{$qvarr[0]}\n-3333\n{$tm}\n";
            socket_write( $sock, $st, strlen($st) ); 
            socket_close( $sock );
    	}

            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_connect($sock, "127.0.0.1", 1100);
            $tm = date( "H:i", time( ) );
            $st = "say\n{$msg_all}\n1249423\n0\n0\n{$tm}\n";
            socket_write( $sock, $st, strlen($st) ); 
            socket_close( $sock );
	}

	if( $tarr['text'] != '' ) print( "<b>$narr[name]: </b>$tarr[text]<br><br>" );
	$tarr['postludium'] = str_replace( '{name}', $player->login, $tarr['postludium'] );
    if( trim( $tarr['postludium'] ) != '' )
    	print( "<i>".text_sex_parse( '{', '|', '}', $tarr['postludium'], $player->sex )."</i><br><br>" );

    // спец.толки, которые показываются после текста из админки
    echo $timer_str;

    if( $talk_id == 187 )  include( "quest_scripts/phrase187_flowers.php" );
    else if( $talk_id == 536 )  include( "quest_scripts/phrase536_presents.php" );
	else if( $talk_id == 1001 )  include( "quest_scripts/phrase771_presents.php" );
	else if( $talk_id == 602 )  include( "quest_scripts/phrase602_flowers_smiles.php" );

	else if( !$attack )
	{
		$qres = f_MQuery( "SELECT phrases.* FROM phrases, talk_phrases WHERE talk_id = $talk_id AND phrases.phrase_id = talk_phrases.phrase_id ORDER BY phrases.phrase_id" );
	
		print( "<ul>" );
		while( $qarr = f_MFetch( $qres ) )
		{
			if( allow_phrase( $qarr[phrase_id] ) )
			{
				$img = "generic";
				if( $qarr['attack_id'] > 0 ) $img='attack';
				if( $qarr['attack_id'] == 0 ) $img='exit';
				if( $qarr['attack_id'] <= -1000000 ) $img='special';

				print( "<li STYLE='list-style-image: URL(\"images/dots/dot-{$img}.gif\"); list-style-type: square'><a style='position:relative;top:-2px;' href=game.php?phrase=$qarr[phrase_id]>".text_sex_parse( '{', '|', '}', str_replace( '{name}', $player->login, $qarr[text] ), $player->sex )."</a><br>" );
		   	}
		}
		print( "</ul>" );
	}
	else
		print( $st );
	
}

if( $narr['image'] != '' )
{
	if( $narr['image_right'] ) echo "</td><td valign=top><img width=$narr[image_w] height=$narr[image_h] src='images/npcs/$narr[image]'>";
	echo "</td></tr></table>";
}

print( "</td></tr></table>" );

?>
