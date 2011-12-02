<?

include_once( 'guild.php' );

$script_name = "lake.php";
$guild_id = FISHMEN_GUILD;
$cancel_text = 'Вы досрочно прекратили рыбалку.';
$begin_text = 'Вы забрасываете удочку в реку...';
if( $player->player_id == 67573 ) $begin_text = 'Вы выбрасываете удочку нахрен, прыгаете в реку и, дико хохоча, начинаете ловить рыбу руками.';
$descr_text = "Сегодня река тихая и спокойная. У Вас есть все шансы пополнить свой инвентарь хорошим уловом.<br>";
$btn_text = "Рыбачить";
$during_text = "Вы увлечены рыбалкой. При желании вы можете <a href=lake.php?cancel target=game_ref>прекратить</a> рыбачить.<br><br>";
$spent_text = "Вы провели за рыбалкой";

$kopka_loc = 2;
$kopka_depth = 4;

$kapkan_req_rank = 2;
$kapkan_title = "<b>Установка сетей</b>";
$kapkan_what_to_do = "поставить сети";
$kapkan_what_to_do2 = "ставить сети";
$kapkan_check = "проверить сети";
$kapkan_msg = "Вы проверяете сети. На этот раз вы поймали ";
$kapkan_nothing_text = "В это невозможно поверить, но сети пусты!";

function get_finish_text( $item_id, $num, $str, $str1, $str2 )
{
	// Водоросли
	global $guild_id;
	if( $item_id == 111 || $item_id == 112 || $item_id == 113 )
	{
		$st = "";
		if( $item_id == 111 ) $st = "<font color=blue><b>синей водоросли</b></font>";
		if( $item_id == 112 ) $st = "<font color=red><b>красной водоросли</b></font>";
		if( $item_id == 113 ) $st = "<font color=green><b>зеленой водоросли</b></font>";
		
		return "К сожалению, Вы ничего не поймали. Достав снасти, Вы обреченно осматриваете их. Рыбы, конечно, не видать, но зато Вы достали немного водорослей!! Вы становитесь обладателем редкостной в этой местности <a href=help.php?id=1010&item_id=$item_id target=_blank>$st</a> ($num)";
	}
	
	if( $item_id == 115 )
	{
		return "К сожалению, Вы ничего не поймали. Но, твердо решив на этот раз уйти не с пустыми руками, Вы берете немного чистого <a href=help.php?id=1010&item_id=$item_id target=_blank><b>речного песка</b></a> ($num)";
	}
	
	if( $item_id == 114 )
	{
		return "К сожалению, Вы ничего не поймали. Но, твердо решив на этот раз уйти не с пустыми руками, Вы берете немного <a href=help.php?id=1010&item_id=$item_id target=_blank><b>бережной глины</b></a> ($num)";
	}
	
	if( $item_id == 479 )
	{
		return "К сожалению, Вы ничего не поймали. Но, твердо решив на этот раз уйти не с пустыми руками, Вы берете несколько <a href=help.php?id=1010&item_id=$item_id target=_blank><b>камней</b> с берега</a> ($num)";
	}
	

	if( mt_rand( 1, 20 ) == 1 )
	{
		global $player;
		$player->AddToLog( 36, 1, 1, $guild_id, 2 );
		$player->AddItems( 36, 1 );
		return "Наконец-то клюет! На сей раз Вы выловили $str. Хорош улов, хороша рыбка, теперь будет что рассказать своим друзьям-рыбакам. Нужно обязательно снять мерку с неё. А вот как раз и небольшая палочка, пожалуй, стоит её взять себе для мерки. Вы находите <a href=help.php?id=1010&item_id=36 target=_blank><b>дерево</b></a>";
	}
	
	return "Наконец-то клюет! На сей раз Вы выловили $str"; 
}

function get_nothing_text( )
{
	global $player;
	global $guild_id;

	// Квест мифрильного с жмутом шерсти
	if( $player->HasTrigger( 1500 ) && mt_rand( 1, 5 ) == 1 )
	{
		$player->SetTrigger( 1500, 0 );
		$player->AddToLog( 124, 1, 1, $guild_id, 2 );
		$player->AddItems( 124, 1 );
		return "К сожалению, Вы ничего не поймали. Вы обратили внимание на <a href=help.php?id=1010&item_id=124 target=_blank><b>жмут черной и мерзкой шерсти</b></a>, зацепившийся за крючок удочки. Как запасливый рыбак, Вы эту гадость тихонечко прячете за углом рыбацкой, авось пригодится.";
	}
	
	if( mt_rand( 1, 120 ) == 1 )
	{
		$player->AddToLog( 110, 1, 1, $guild_id, 2 );
		$player->AddItems( 110, 1 );
		return "К сожалению, Вы ничего не поймали. Только за одно громадное <a href=help.php?id=1010&item_id=110 target=_blank><b>бревно</b></a> зацепилась ваша удочка, собственно это сначала и побудило Вас к мысли о хорошем улове. Ну что же, разочарование велико, но компенсацию Вы все же заберете себе.";
	}
	return "К сожалению, Вы ничего не поймали…";
}

include( 'kopka_loc.php' );

?>
