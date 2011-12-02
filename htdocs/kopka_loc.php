<?

$hodka_time = 10 /*min*/ * 60 /*sec*/;

include( 'prof_exp.php' );
include_once( 'items.php' );

if( !isset( $mid_php ) )
{
	include_once( "no_cache.php" );
	include_once( "functions.php" );
	include_once( "player.php" );

	f_MConnect( );
	
	echo '<META http-equiv=Content-Type content="text/html; charset=windows-1251">';

	if( !check_cookie( ) )
		die( "Неверные настройки Cookie" );
	
	$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

	if( $player->location != $kopka_loc || $player->depth != $kopka_depth )
		die( );
	$tm = time( );
	f_MQuery( "UPDATE online SET last_ping = $tm WHERE player_id = {$player->player_id}" );
}

$guild = new Guild( $guild_id );
if( !$guild->LoadPlayer( $player->player_id ) )
{
	if( !isset( $mid_php ) ) die( );
	
	echo "<br>Вы не состоите в <a href=help.php?id={$guilds[$guild_id][1]} target=_blank>Гильдии {$guilds[$guild_id][0]}</a> и не можете тут работать.<br>";
	echo "Вступить в гильдию можно в <a href=help.php?id=34274 target=_blank>Зале Гильдий</a> в <a href=help.php?id=34265 target=_blank>Городской Управе</a>.<br>";
	return;
}

if( isset( $mid_php ) )
{
?>

<script>
function reload () {

	var rndval = new Date().getTime(); 

	document.getElementById('num_img').innerHTML = '<img width=90 height=40 src=captcha/code.php?rnd=' + rndval + ' border=1 bordercolor=black>';
};
function show_lake( )
{
	show_timer_title = false;
	window.top.document.title = window.top.tstr;
	document.getElementById('lake').style.display = '';
	document.getElementById('work').style.display = 'none';
	reload( );
}
function show_work( a )
{
	var d0=new Date( );
	tm = d0.getTime( ) - a * 1000;
	document.getElementById('lake').style.display = 'none';
	document.getElementById('work').style.display = '';
	_( 'rests' ).innerHTML = '';
	PingTimer( );
	show_timer_title = true;
}
function show_auto( a )
{
	_( 'rests' ).innerHTML = 'У вас активирован Премиум-Свобода. Осталось перезапусков: <b>' + a + '</b>';
}
</script>

<script src=js/timer.js></script>

<div id=lake name=lake style='display:none'>

<?

echo $descr_text;

echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><div id=num_img>&nbsp;</div></td><td>&nbsp;";
$oncl = 'parent.game_ref.location.href = "'.$script_name.'?num=" + document.getElementById( "num" ).value;document.getElementById( "num" ).value="";';
echo "<input onkeydown='e = event || window.event;if( e.keyCode == 13 ) { $oncl }' type=text class=te_btn size=4 maxlength=4 name=num id=num></td><td>&nbsp;<button onClick='$oncl' class=ss_btn>{$btn_text}</button></td></tr></table>";
echo "(Если вы не можете разобрать цифр, нажмите <a href=# onclick='reload();'>сюда</a>, чтобы обновить картинку).<br>";
echo "<script src='js/numkeyboard.js'></script><script>showkeyboard('num');</script>";

?>

</div>

<div id=work name=work style='display:none'>

<?

print( $during_text );

?>

<script>document.write( InsertTimer( <? echo $hodka_time ?>, 'До окончания осталось: <b>', '</b>', 0, 'parent.game_ref.location.href=\"<? echo $script_name ?>\"' ) );</script>

<br>

<div id=rests>&nbsp;</div>


</div>


<?
}

if( $player->regime == 104 && ( $player->till - 2 <= time( ) || isset( $_GET['cancel'] ) ) )
{
	$till = $player->till;
	if( $till < time( ) ) $till = time( );
	if( abs( $till - time( ) ) < 4 ) $till = time( );
	$started = $till - $hodka_time;

	$res = f_MQuery( "SELECT items.item_id, items.price FROM lake_items, items WHERE lake_items.item_id=items.item_id AND lake_items.guild_id = $guild_id AND lake_items.rank <= {$guild->rank}" );
	include_once( "kopka.php" );
	$kopka = new Kopka( );
		
	while( $arr = f_MFetch( $res ) )
		$kopka->AddItem( $arr[0], $arr[1] );
	
	$per_hour = 200 + $guild->rating * 50;

	// auto
	$auto = false;
	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=4" ) );
	if( $player->HasTrigger( 227 ) ) $started = $till - $hodka_time * 12;
	$player->SetTrigger( 227, 0 );
	// premium
	$premium = false;
	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=1" ) );
	if( $barr[0] ) $premium = true;
	
	$total_work_time = time( ) - $started;

	if( $total_work_time < 30 ) $player->syst( "$spent_text меньше 30 секунд... За это время едва ли можно было сделать что-то полезное." );
	while( $total_work_time >= 30 )
	{
		$st = "";
		$val = min( $total_work_time, $hodka_time );
		$total_work_time -= $val;
		
		$st .= "$spent_text ".my_time_str2( $val ).". ";
		
    	$kopka->GetItemId( $val, $per_hour, $premium );
    	
    	if( !$kopka->num ) $st .= get_nothing_text( );
    	else
    	{
    		$player->AddToLog( $kopka->item_id, $kopka->num, 1, $guild_id );
    		$player->AddItems( $kopka->item_id, $kopka->num );
    		if( !isset( $mid_php ) ) $player->UpdateWeightStr( true, 'parent.game.' );
    		$f1 = getItemNameForm( $kopka->item_id, "" );
    		$f2 = getItemNameForm( $kopka->item_id, "2" );
    		$f4 = getItemNameForm( $kopka->item_id, "4" );
    		$f13 = getItemNameForm( $kopka->item_id, "13" );
    		$f2m = getItemNameForm( $kopka->item_id, "2_m" );

    		$tstr = "<a target=_blank href=help.php?id=1010&item_id={$kopka->item_id}><b>".my_word_form2( $kopka->num, $f4, $f13, $f2m )."</b></a>";
    		$tstr1 = "<a target=_blank href=help.php?id=1010&item_id={$kopka->item_id}><b>".my_word_form( $kopka->num, $f1, $f13, $f2m )."</b></a>";
    		$tstr2 = "<a target=_blank href=help.php?id=1010&item_id={$kopka->item_id}><b>".my_word_form3( $kopka->num, $f2, $f2m )."</b></a>";
    		$st .= get_finish_text( $kopka->item_id, $kopka->num, $tstr, $tstr1, $tstr2 );

    		// widow quest
		   	include_once( "quest_race.php" );
		   	updateQuestStatus ( $player->player_id, 2502 );
    	}
        // увеличь профу тут!
    	if( !$kopka->num )
    	{
    		if( $val == $hodka_time ) $st .= AlterProfExp( $player, 1 );
    	}
    	else $st .= AlterProfExp( $player, ceil( $kopka->item_prices[$kopka->item_id] * $kopka->avgnum * 50 / $per_hour ) );
		$player->syst( $st );
	}
	
	$player->SetTill( 0 );
	$player->SetRegime( 0 );
	
	$code=rand(1000,9999);

	f_MQuery( "LOCK TABlE player_num WRITE" );
	f_MQuery( "DELETE FROM player_num WHERE player_id = $player->player_id" );
	f_MQuery( "INSERT INTO player_num VALUES ( $player->player_id, $code )" );
	f_MQuery( "UNLOCK TABLES" );

//	$st .= AlterProfExp( $player, 8 );
	UpdateTitle( );
	if( !isset( $mid_php ) ) echo "<script>parent.game.update_exp( $player->exp, $player->prof_exp );</script>";
}

if( !isset( $mid_php ) )
{
	if( isset( $_GET['num'] ) && $player->regime == 0 )
	{
		$res = f_MQuery( "SELECT * FROM player_num WHERE player_id = $player->player_id" );
		$arr = f_MFetch( $res );
		if( !$arr ) $real_num = -1;
		else $real_num = $arr['number'];
		$num = $_GET['num'];
		settype( $num, 'integer' );
		if( $num < 0 ) $num = 0;
		else if( $num > 9999 ) $num = 9999;

		$auto = false;
		$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=4" ) );
		if( $barr[0] ) $auto = true;

		
		if( $num != $real_num ) $player->syst( 'Вы ввели неверное число' );
		else
		{
			$player->syst( $begin_text );
			$player->SetRegime( 104 );
			if( !$auto )
			{
				$player->SetTill( time( ) + $hodka_time );
				$player->SetTrigger( 227, 0 );
			}
			else
			{
				$player->SetTill( time( ) + $hodka_time * 12 );
				$player->SetTrigger( 227 );
			}
			echo "<script>parent.game.show_work( 0 );</script>";
		}
	}

	echo "<script>";
	
	if( $player->regime == 0 ) echo "parent.game.show_lake( );";
	else
	{
		$moo = $hodka_time - $player->till + time( );
		echo "parent.game.show_work( $moo );";
	}
	echo "location.href='empty.gif';";

	echo "</script>";
}
else
{

?>


<div id=kapkan>
<?

if( $kapkan_req_rank >= 0 )
{
    echo "<br><b>$kapkan_title</b><br>";
    if( $guild->rank < $kapkan_req_rank )
    {
    	echo "Достигнув ранга <b>$kapkan_req_rank</b> вы сможете $kapkan_what_to_do2.<br>";
    }
    else
    {
    	f_MQuery( "LOCK TABLES player_kapkans WRITE" );
    	$kres = f_MQuery( "SELECT * FROM player_kapkans WHERE player_id={$player->player_id}" );
    	$karr = f_MFetch( $kres );
    	if( !$karr && $_GET['act24'] )
    	{
    		$tm = time( );
    		f_MQuery( "INSERT INTO player_kapkans( player_id, guild_id, timestamp ) VALUES ( {$player->player_id}, {$guild->guild_id}, $tm )" );
    		$kres = f_MQuery( "SELECT * FROM player_kapkans WHERE player_id={$player->player_id}" );
    		$karr = f_MFetch( $kres );
    	}
    	f_MQuery( "UNLOCK TABLES" );
    	$can_set = true;
    	if( $karr )
    	{
    		$can_set = false;
    		if( $karr['guild_id'] != $guild->guild_id )
    			echo "Вы уже ".$kapkan_texts[$karr['guild_id']]." в гильдии ".$guilds[$karr['guild_id']][0].". Вы не можете $kapkan_what_to_do.";
    		else
    		{
    			$tm = $karr['timestamp'] + 24*60*60 - time( );
    			if( $tm > 0 ) echo "<script>document.write( InsertTimer( ".$tm.", 'Вы уже ".$kapkan_texts[$karr['guild_id']].". До окончания осталось: <b>', '</b>', 1, 'location.href=\"game.php\"' ) );</script>";
    			else
    			{
    				if( $_GET['act24'] )
    				{
    					$can_set = true;

                    	$res = f_MQuery( "SELECT items.item_id, items.price FROM lake_items, items WHERE lake_items.item_id=items.item_id AND lake_items.guild_id = $guild_id AND lake_items.rank <= {$guild->rank}" );
                    	$st = "";
                    	include_once( "kopka.php" );
                    	$kopka = new Kopka( );
                    		
                    	while( $arr = f_MFetch( $res ) )
                    		$kopka->AddItem( $arr[0], $arr[1] );
                    	
                    	$per_hour = 30 + $guild->rating * 5;
    					$ulov = Array( );
    					for( $i = 0; $i < 24; ++ $i )
    					{
    	                	$kopka->GetItemId( 60*60, $per_hour );
    						if( $kopka->num ) $ulov[$kopka->item_id] += $kopka->num;
    	                }
                    	
                    	$first_item = true;
                    	if( !count( $ulov ) ) $st .= $kapkan_nothing_text;
                    	else foreach( $ulov as $item_id=>$num )
                    	{
                    		$res2 = f_MQuery( "SELECT name FROM items WHERE item_id = $item_id" );
                    		if( !f_MNum( $res ) ) RaiseError( "Ошибка при копке. Найдена вещь, которой нет в базе", "АйДи: $item_id" );
                    		
							$player->AddToLog( $item_id, $num, 1, $guild_id, 1 );
                    		$player->AddItems( $item_id, $num );
                    		$arr2 = f_MFetch( $res2 );
                    		$tstr = "<a target=_blank href=help.php?id=1010&item_id=$item_id><b>$arr2[0]</b></a>";
                    		if( $num > 1 ) $tstr .= " ($num)";
                    		if( !$first_item ) $st .= ", ";
                    		else $st .= $kapkan_msg;
                    		$st .= $tstr;
                    		$first_item = false;
                    	}
                    	
                    	$player->syst( $st );
                    	f_MQuery( "DELETE FROM player_kapkans WHERE player_id={$player->player_id}" );
    				}
    				else echo "С момента, когда следовало $kapkan_check, уже прошло ".my_time_str( - $tm ).". Вы можете <a href=game.php?act24=1>$kapkan_check</a> сейчас";
    			}
    	    }
    	}

    	if( $can_set )
    	{
    		echo "Вы можете <a href=game.php?act24=1>$kapkan_what_to_do</a>.";
    	}
    }
}

?>
</div>

<script>

<?
	if( $player->regime == 0 ) echo "show_lake( );";
	else
	{
		$moo = $hodka_time - $player->till + time( );
		echo "show_work( $moo );";
	}

?>

</script>

<?

}

?>
