<script>
function expand_pe( a )
{
	if( document.getElementById( 'd' + a ).style.display == "none" )
	{
		document.getElementById( 'd' + a ).style.display = "";
		document.getElementById( 'i' + a ).src = "../images/e_minus.gif";
	}
	else
	{
		document.getElementById( 'd' + a ).style.display = "none";
		document.getElementById( 'i' + a ).src = "../images/e_plus.gif";
	}
}

// fix http://alideria.ru/forum.php?thread=8457
function refBugfix( link )
{
	if( !document.getElementById( 'ref' ) )
	{
		setTimeout( 'refBugfix( "' + link + '" )', 250 );
	}
	else
	{
		document.getElementById( 'ref' ).src = link;
	}
	
	return false;
}
</script>

<?

include_once( "guild.php" );
include_once( "quest_race.php" );

function moo( $a, $b )
{
	global $player;
	global $guilds;
	$res = f_MQuery( "SELECT quests.name, player_quests.* FROM quests, player_quests WHERE status = $a AND player_quests.quest_id = quests.quest_id AND player_id={$player->player_id} ORDER BY time" );

	$gov_work = false;
	if( $a == 0 )
	{
		f_MQuery( "delete from player_government_work where player_id={$player->player_id} and guild_id NOT IN ( select guild_id from player_guilds where player_id={$player->player_id} );" );
		$gres = f_MQuery( "SELECT * FROM player_government_work WHERE player_id={$player->player_id}" );
		if( f_MNum( $gres ) ) $gov_work =true;
	}
	
	$raceQuestNow = ( $a == 0 && $player->HasTrigger( 262 ) && checkQuestActivity ( ) );
	
	if( !mysql_num_rows( $res ) && !$gov_work && !$raceQuestNow )
		print( "<i>Нет ни одного $b квеста</i><br>" );
	else
	{
		while( $arr = f_MFetch( $res ) )
		{
			print( "<li><a href=quest_info.php?id=$arr[quest_id] target=ref onclick=\"return refBugfix('quest_info.php?id=$arr[quest_id]');\">$arr[name]</a><br>" );
		}
		if($gov_work ) while( $arr = f_Mfetch(  $gres) )
		{
			print( "<li><a href=quest_info.php?id=-$arr[guild_id] target=ref onclick=\"return refBugfix('quest_info.php?id=$arr[quest_id]');\">>Гос.Заказ ".$guilds[$arr['guild_id']][0]."</a><br>" );
		}
		if( $raceQuestNow )
		{
			print( "<li><a href=quest_info.php?id=-1000 target=ref onclick=\"return refBugfix('quest_info.php?id=$arr[quest_id]');\">>Задание от Фавна</a><br>" );
		}
	}
}

print( "<table width=100%><tr><td>" );

ScrollLightTableStart( );

print( "<table width=100%><tr><td colspan=2>" );

ScrollTableStart( "left" );
print( "<b>Задания:</b><br>" );
ScrollTableEnd( );

print( "</td></tr><tr><td vAlign=top width=250  height=100%>" );
ScrollTableStart(  "left" );

print( "<div id=t_1 style='cursor:pointer' onclick=\"expand_pe('_1')\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img width=11 height=11 id=i_1 src='images/e_plus.gif'>&nbsp;<b>Текущие:</b></div><br><div id=d_1 style='display:none'>" );

//print( "<b>Текущие:</b><br>" );
moo( 0, 'текущего' );
print("</div>");

print( "<br><hr>" );
print("<script>expand_pe('_1');</script>");

//print( "<b>Выполненные:</b><br>" );
print( "<div id=t_2 style='cursor:pointer' onclick=\"expand_pe('_2')\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img width=11 height=11 id=i_2 src='images/e_plus.gif'>&nbsp;<b>Выполненные:</b></div><br><div id=d_2 style='display:none'>" );
moo( 1, 'выполненного' );
print("</div>");

print( "<br><hr>" );

//print( "<b>Проваленные:</b><br>" );

print( "<div id=t_3 style='cursor:pointer' onclick=\"expand_pe('_3')\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img width=11 height=11 id=i_3 src='images/e_plus.gif'>&nbsp;<b>Проваленные:</b></div><br><div id=d_3 style='display:none'>" );
moo( -1, 'проваленного' );
print("<hr></div>");
ScrollTableEnd( );
	
print( "</td><td vAlign=top height=100%>".GetScrollTableStart( "left" )."<div id=qdescr name=qdescr>&nbsp;</div>".GetScrollTableEnd( )."</td></tr></table>" );

ScrollLightTableEnd( );

?>

<br><table width=100% cellspacing=0 cellpadding=0 border=0><tr><td width=50% valign=top>
<b>Пригласи друга - получи награду!</b><br><u>http://www.alideria.ru/?r=<?=$player->player_id?></u><br>
<a target=_blank href=help.php?id=50000>Узнай подробнее</a>


<br><br>
<b>История</b><br>
<a href=history_visits.php target=_blank>Информация о посещениях игры</a><br>
<a href=history_punishments.php target=_blank>Информация о наказаниях</a><br>
<a href=history_trades.php target=_blank>Информация о сделках на ярмарке</a><br>
<a href=history_post.php target=_blank>Информация о почтовых переводах</a><br>
<a href=history_fights.php target=_blank>Информация о боях</a><br>
<a href=show_refs.php target=_blank>Информация о приглашенных игроках</a><br>
<a href="/history_payments.php" target="_blank">Информация о покупках талантов</a><br />
<a href="/stat_mob_wins.php" target="_blank">Информация о статистике побед над мобами</a><br />

</td><td width=50% vAlign=top>

<script>

function openLetter( id )
{
	query( "post_read.php?id="+id, "" );
}

function takeAtt( id )
{
	if( confirm( 'Вы уверены, что хотите взять приложенные к письму вещи?' ) )
		query( "post_action.php?act=take&id=" + id, '' );
}

function refuseAtt( id )
{
	if( confirm( 'Вы уверены, что хотите вернуть приложенные к письму вещи отправителю?' ) )
		query( "post_action.php?act=refuse&id=" + id, '' );
}

function deleteLetter( id )
{
	if( confirm( 'Вы уверены, что хотите удалить письмо? Если к письму приложены вещи, они вернутся отправителю' ) )
		query( "post_action.php?act=del&id=" + id, '' );
}

</script>

<?

$folder_id = (int)$_GET['folder_id'];

if( $_GET['del_all_post'] )
{
	$res = f_MQuery( "SELECT p.* FROM post as p WHERE p.receiver_id = {$player->player_id} AND p.folder_id={$folder_id} ORDER BY entry_id DESC" );
	while( $arr = f_MFetch( $res ) ) if( !$arr['money'] )
	{
		$val = f_MValue( "SELECT count(entry_id) FROM post_items WHERE entry_id=$arr[entry_id]" );
		if( !$val ) f_MQuery( "DELETE FROM post WHERE entry_id=$arr[entry_id]" );
	}
}


$res = f_MQuery( "SELECT p.* FROM post as p WHERE p.receiver_id = {$player->player_id} AND p.folder_id={$folder_id} ORDER BY entry_id DESC" );

if ($player->Rank()==5 || $player->Rank()==2 || $player->player_id==6825)
{
	echo "<table border=1>";
	echo "<tr><td><a href='game.php?folder_id=0'>Общие</a></td>";
	echo "<td><a href='game.php?folder_id=1'>Служебные</a></td></tr>";
	echo "</table><br>";
}

if( f_MNum( $res ) )
{
	echo "<b>Входящие письма</b><br>";
	?>
	<script>function del_all_post()
	{
		if(confirm('Удалить все письма без вложений?')) location.href="game.php?folder_id=<?=$folder_id?>&del_all_post=1";
	}</script>
	<?
	echo "<li><a href='javascript:del_all_post();'>Удалить все письма без вложений</a><br>";
	echo "<table><tr><td width=15>&nbsp;</td><td align=center><b><small>От Кого</small></b></td><td align=center><small><b>Заголовок</b></small></td></tr>";
	while( $arr = f_MFetch( $res ) )
	{
		$title = $arr['title'];
		$title = str_replace( "\n", "<br>", str_replace( "\r", "", $title ) );

		$att = false;
		if( $arr['money'] > 0 ) $att = true;
		$ares = f_MQuery( "SELECT count( entry_id ) FROM post_items WHERE entry_id=$arr[entry_id]" );
		$aarr = f_MFetch( $ares );
		if( $aarr[0] ) $att = true;
		$t1 = ""; $t2 = "";
		if( !$arr['readed'] ) { $t1 = '<b>';  $t2 = '</b>'; }
		$plr = new Player( $arr['sender_id'] );
		echo "<tr><td>".(($att)?'<img src=images/attach.gif width=6 height=14>':'')."</td><td><script>document.write(".$plr->Nick().");</script></td><td><a href='javascript:openLetter($arr[entry_id])'><span id=ltr$arr[entry_id]>$t1{$title}$t2</span></a></td></tr>";
	}
	echo "</table>";
}

?>

</td></tr></table>

</td></tr></table>

<iframe name=ref id=ref width=0 height=0></iframe>
