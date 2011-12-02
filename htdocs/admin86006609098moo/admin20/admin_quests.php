<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['trigger_id'] ) )
{
	$trigger = $HTTP_GET_VARS['item_id'];
	$set = $HTTP_GET_VARS['set'];
	$player->SetTrigger( $trigger_id, $set );
	printf( "<font color=blue>Сделано</font><br>" );
}

?>
<a href=index.php>На главную</a><br>
<b>Работа с квестами</b><br>

<?

function create_select( $nm, $arr, $val )
{
	$st = "<select name='$nm'>";
	
	foreach( $arr as $key=>$value )
	{
		$st .= "<option value=$key";
		if( $key == $val ) $st .= " selected";
		$st .= ">$value" ;
	}
	
	$st .= '</select>';
	
	return $st;
}

$statuses = Array( -1 => "Провален", 0 => "Текущий", 1 => "Выполнен" );

$ok = false;
if( $HTTP_GET_VARS[login] )
{
	$res = f_MQuery( "SELECT * FROM characters WHERE login='$HTTP_GET_VARS[login]'" );
	$arr = f_MFetch( $res );
	if( !$arr )
	{
		print( "Нет такого игрока" );
	}
	else
	{
		$ok = true;
		$login = $HTTP_GET_VARS[login];
		$player_id = $arr[player_id];
		
		if( isset( $HTTP_GET_VARS[add_quest] ) )
		{
			$tm = time( );
			f_MQuery( "INSERT INTO player_quests ( player_id, quest_id, status, time ) VALUES ( $player_id, $HTTP_GET_VARS[add_quest], 0, $tm )" );
		}
		if( isset( $HTTP_GET_VARS[del_quest] ) )
		{
			$moo = f_MQuery( "SELECT quest_part_id FROM quest_parts WHERE quest_id = $HTTP_GET_VARS[quest_id]" );
			while( $hru = f_MFetch( $moo ) )
				f_MQuery( "DELETE FROM player_quest_parts WHERE player_id = $player_id AND quest_part_id = $hru[0]" );
			f_MQuery( "DELETE FROM player_quests WHERE player_id = $player_id AND quest_id = $HTTP_GET_VARS[quest_id]" );
		}
		if( isset( $HTTP_GET_VARS[status] ) && isset( $HTTP_GET_VARS[quest_id] ) )
		{
			f_MQuery( "UPDATE player_quests SET status=$HTTP_GET_VARS[status] WHERE player_id = $player_id AND quest_id = $HTTP_GET_VARS[quest_id]" );
		}

		print( "Персонаж $login<br><br>" );
		print( "<b>Квесты</b><br>" );
		$has_quests = false;
		$res = f_MQuery( "SELECT * FROM player_quests WHERE player_id = $player_id" );
		while( $arr = f_MFetch( $res ) )
		{
			$res2 = f_MQuery( "SELECT * FROM quests WHERE quest_id = $arr[quest_id]" );
			$arr2 = f_MFetch( $res2 );
			if( !$arr2 ) $nm = "Не существующий квест с ID $arr[quest_id]";
			else $nm = $arr2[name];
			
			$quest_id = $arr[quest_id];
			$status = $statuses[$arr[status]];
			
			print( "<b>$nm</b> $status. <a href=admin_quests.php?login=$login&quest_id=$quest_id&del_quest>Удалить</a> :: <a href=admin_quests.php?login=$login&quest_id=$quest_id&status=-1>Сделать проваленным</a> :: <a href=admin_quests.php?login=$login&quest_id=$quest_id&status=0>Сделать текущим</a> :: <a href=admin_quests.php?login=$login&quest_id=$quest_id&status=1>Сделать выполненным</a><br>" );
			$has_quests = true;
		}
		if( !$has_quests ) print( "<i>Нет квестов</i><br>" );
		print( "<br>" );
		$res2 = f_MQuery( "SELECT * FROM quests" );
		$quests = Array( );
		while( $arr2 = f_MFetch( $res2 ) )
		{
			$quests[$arr2[quest_id]] = $arr2[name];
		}
		
		print( "Добавить квест:<br>" );
		print( "<form action=admin_quests.php method=get>" );
		print( "<input type=hidden name=login value='$login'>" );
		print( create_select( "add_quest", $quests, 1 ) );
		print( "<input type=submit value='Добавить'></form>" );
	}
}

if( !$ok )
{
?>

<form action=admin_quests.php method=get>
Логин персонажа: <input name=login><input type=submit value='Открыть'>
</form>

<?
}

/*
?>

<table>
<form action=admin_triggers.php method=get>
<tr><td>АйДи триггера: </td><td><input type=text name=trigger_id class=m_btn></td></tr>
<tr><td>Что сделать? </td><td><select name=set><option value=1>Установить<option value=0>Сбросить</select></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=Сделать></td></tr>
</form>
</table>

<?
*/

f_MClose( );

?>

