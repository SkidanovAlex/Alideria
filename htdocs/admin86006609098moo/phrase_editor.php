<?                 
include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../guild.php' );

header("Content-type: text/html; charset=windows-1251");

$moo_guilds = Array( );
foreach( $guilds as $a=>$b ) $moo_guilds[$a] = $b[0];

$regimes = Array( 5 => "�������", 0 => "������� �� ������", 4 => "������� ������",  6 => "������� �� ������ (���� ����������)", 3 => "������� ����������", 1 => "��������", 7 => "�������� (���� ����������)", 2 => "����" );
$regimes2 = Array( 0 => "�������", 1 => "������� ����������", 2 => "�������������", 3 => "����������" );
$req_guild_slot_values = Array( -1 => "������� ����������", 0 => "-", 1 => "������� �������" );
$req_quest = Array(0 => "������� ����������", 1 => "������� �������");
$reg_sex = Array(0 => "�������� - ������", 1 => "������� - �������");
$req_clan_values = Array( 0=> "������� ���������� � �����", 1=>"������� ����������� � �����");
$guild_regimes = Array( 1 => "������� �������", 2 => "������� ���������� �������", 3 => "���������� � �������", 4 => "��������� �� �������" );
$guild_regimes_wv = Array( 5 => "������� ���� � ������� �� �����", 6 => "������� ���� � ������� ������ �����", 7 => "������� ���� � ������� �����",
                     8 => "������� ������� � ������� �� �����", 9 => "������� ������� � ������� ������ �����", 10 => "������� ������� � ������� �����", 11 => "�������� ���� � ������� ��", 12 => "�������� ������� � ������� ��" );
$value_regimes = Array( 1 => "������� �����", 2 => "������� ������ ���", 3 => "������� ������ ���", 4 => "����������� ��", 5 => "������ �����" );
$effect_regimes = Array (1 => "�����", 2 => "������ ���", 3 => "������ ���");
$premium_names = Array( 0 => "���", "������", "�����", "������", "�������", "�������" );


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
function moo( $a )
{
	if( $a == -5 ) return "�������";
	if( $a == -2 ) return( "���������" );
	if( $a == -1 ) return( "���������" );
	if( $a == 0 ) return( "����" );
	if( $a > 0 ) return( "������: $a" );
}

function moo1($a)
{
	if ($a <= -3) return (" ����� �����-�� ");
	if( $a == -3 ) return( "�������" );
	if( $a == -1 ) return( "������������" );
	if( $a == -2 ) return( "������������" );
	if( $a == 0 ) return( "��������" );
	if( $a > 0 ) return( "������: $a" );
}

f_MConnect( );

include( 'quest_header.php' );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">
<html><body>
<?

$id = $HTTP_GET_VARS[id];
$from = $HTTP_GET_VARS[from];

$expand_what = false;

if( isset( $HTTP_POST_VARS[text] ) )
{
	$txt = htmlspecialchars( $HTTP_POST_VARS[text] );
	$attack_id = $HTTP_POST_VARS[attack_id];
	$minlevel = $HTTP_POST_VARS[minlevel];
	$maxlevel = $HTTP_POST_VARS[maxlevel];
	$drop_mines = (($_POST['drop_mines'] == 'on')?1:0);
	f_MQuery( "UPDATE phrases SET minlevel='$minlevel', maxlevel='$maxlevel', text='$txt', attack_id=$attack_id, drop_mines = $drop_mines WHERE phrase_id=$id" );
}
if( isset( $HTTP_POST_VARS[expr] ) )
{
	$gain_exp = $HTTP_POST_VARS[expr];
	$gain_prof = $HTTP_POST_VARS[pexpr];
	f_MQuery( "UPDATE phrases SET gain_exp = $gain_exp, gain_prof=$gain_prof WHERE phrase_id=$id" );
	$expand_what = "exp";
}
if( isset( $HTTP_POST_VARS[chance] ) )
{
	$chance = $HTTP_POST_VARS[chance];
	f_MQuery( "UPDATE phrases SET chance1000000 = $chance WHERE phrase_id=$id" );
	$expand_what = "rand";
}
if( isset( $HTTP_POST_VARS[warp_loc] ) )
{
	$warp_loc = $HTTP_POST_VARS[warp_loc];
	$warp_depth = $HTTP_POST_VARS[warp_depth];
	f_MQuery( "UPDATE phrases SET warp_loc = $warp_loc, warp_depth = $warp_depth WHERE phrase_id=$id" );
	$expand_what = "warp";
}
if( isset( $HTTP_POST_VARS[req_guild_slot] ) )
{
	$req_guild_slot = $HTTP_POST_VARS[req_guild_slot];
	f_MQuery( "UPDATE phrases SET req_guild_slot = $req_guild_slot WHERE phrase_id=$id" );
	$expand_what = "guilds";
}
if (isset($HTTP_POST_VARS[clan_id]))
{
	$value = (int)$_POST[clan_id];
	$reg = (int)$_POST[regime];
	if ($value >= 0 && ($reg==0 || $reg==1))
		f_MQuery("INSERT INTO phrase_clans (phrase_id, clan_id, action) VALUES ( $id, $value, $reg )");
	$expand_what = "clans";
}
if (isset($HTTP_GET_VARS[del_clan]))
{
	$value = (int)$_GET[del_clan];
	f_MQuery("DELETE FROM phrase_clans WHERE phrase_id=$id AND clan_id=".$value);
	$expand_what = "clans";
}
if (isset($HTTP_POST_VARS[hour1]) && isset($HTTP_POST_VARS[hour]))
{
	$h1=(int)$HTTP_POST_VARS[hour1];
	$h=(int)$HTTP_POST_VARS[hour];
	if ($h<0) $h=0;
	if ($h>23) $h=23;
	if ($h1<0) $h=0;
	if ($h1>23) $h=23;
	if ($h1>$h)
		f_MQuery("INSERT INTO phrase_hours (phrase_id, hour, hour1) VALUES ($id, $h, $h1)");
	else
		f_MQuery("INSERT INTO phrase_hours (phrase_id, hour, hour1) VALUES ($id, $h1, $h)");
	$expand_what = "hour";
}
if (isset($HTTP_GET_VARS[del_hour]))
{
	$value = $_GET[del_hour];
	f_MQuery("DELETE FROM phrase_hours WHERE phrase_id=$id AND hour=".$value);
	$expand_what = "hour";
}
if (isset($HTTP_POST_VARS[sex]))
{
	$value = $_POST[sex];
	f_MQuery("DELETE FROM phrase_sex WHERE phrase_id=".$id);
	f_MQuery("INSERT INTO phrase_sex (phrase_id, sex) VALUES ( $id, $value )");
	$expand_what = "sex";
}
if (isset($HTTP_GET_VARS[del_sex]))
{
	$value = $_GET[del_sex];
	f_MQuery("DELETE FROM phrase_sex WHERE phrase_id=".$value);
	$expand_what = "sex";
}
if( isset( $HTTP_POST_VARS[guild] ) )
{
	$value = $_POST[value];
	settype( $value, 'integer' );
	f_MQuery( "INSERT INTO phrase_guilds ( phrase_id, guild_id, action, value ) VALUES ( $id, $HTTP_POST_VARS[guild], $HTTP_POST_VARS[regime], $value )" );
	$expand_what = "guilds";
}
if( isset( $HTTP_GET_VARS[del_guild] ) )
{
	$value = $_GET[value];
	settype( $value, 'integer' );
	f_MQuery( "DELETE FROM phrase_guilds WHERE phrase_id=$id AND guild_id=$HTTP_GET_VARS[guild_id] AND action=$HTTP_GET_VARS[action] AND value=$value" );
	$expand_what = "guilds";
}
if( isset( $HTTP_POST_VARS[item_id] ) )
{
	f_MQuery( "INSERT INTO phrase_items ( phrase_id, item_id, number, regime ) VALUES ( $id, $HTTP_POST_VARS[item_id], $HTTP_POST_VARS[number], $HTTP_POST_VARS[regime] )" );	
	$expand_what = "items";
}
if( isset( $HTTP_GET_VARS[del_item] ) )
{
	f_MQuery( "DELETE FROM phrase_items WHERE ( phrase_id=$id AND item_id=$HTTP_GET_VARS[del_item] AND regime=$HTTP_GET_VARS[regime] )" );
	$expand_what = "items";
}
if( isset( $HTTP_POST_VARS[effect_id] ) )
{
	$duration = $HTTP_POST_VARS['effect_duration'] * 60;
	f_MQuery( "INSERT INTO phrase_effects ( phrase_id, effect_id, image, description, duration, effect, type ) VALUES ( $id, $_POST[effect_id], '$_POST[effect_image]', '$_POST[effect_descr]', $duration, '$_POST[effect_effect]', $_POST[effect_type] )" );
	f_MQuery( "UPDATE phrase_effects SET name='{$_POST[effect_name]}' WHERE entry_id=".mysql_insert_id( ) );
	$expand_what = "effects";
}
if( isset( $HTTP_GET_VARS[del_effect] ) )
{
	f_MQuery( "DELETE FROM phrase_effects WHERE entry_id=$HTTP_GET_VARS[del_effect]" );
	$expand_what = "effects";
}
if ( isset( $HTTP_POST_VARS[effect_if_id] ) )
{
	$eff_id=(int)$HTTP_POST_VARS[effect_if_id];
	$num=(int)$HTTP_POST_VARS[num];
	if ($num<0) $num=0;
	$st=(int)$HTTP_POST_VARS[status];
	if ($st<1) $st=1;
	if ($st>3) $st=3;
	f_MQuery("INSERT INTO phrase_effects_if (phrase_id, status, effect_id, num) VALUES ($id, $st, $eff_id, $num)");
	$expand_what = "effects_if";
}
if( isset( $HTTP_GET_VARS[del_effect_if] ) )
{
	f_MQuery( "DELETE FROM phrase_effects_if WHERE phrase_id=$id AND effect_id=".$HTTP_GET_VARS[del_effect_if] );
	$expand_what = "effects_if";
}
if( isset( $HTTP_POST_VARS[smile_id] ) )
{
	$duration = $HTTP_POST_VARS['smile_duration'] * 60;
	f_MQuery( "INSERT IGNORE INTO phrase_smiles ( phrase_id, smile_id, duration ) VALUES ( $id, $_POST[smile_id], $duration )" );
	$expand_what = "smiles";
}
if( isset( $HTTP_GET_VARS[del_smile] ) )
{
	f_MQuery( "DELETE FROM phrase_smiles WHERE phrase_id=$id AND smile_id=$HTTP_GET_VARS[del_smile]" );
	$expand_what = "smiles";
}
if( isset( $HTTP_POST_VARS[premium_id] ) )
{
	$duration = $HTTP_POST_VARS['premium_duration'];
	f_MQuery( "INSERT IGNORE INTO phrase_premiums ( phrase_id, premium_id, duration ) VALUES ( $id, $_POST[premium_id], $duration )" );
	$expand_what = "premiums";
}
if( isset( $HTTP_GET_VARS[del_premium] ) )
{
	f_MQuery( "DELETE FROM phrase_premiums WHERE phrase_id=$id AND premium_id=$HTTP_GET_VARS[del_premium]" );
	$expand_what = "premiums";
}
if( isset( $HTTP_POST_VARS[craft_id] ) )
{
	f_MQuery( "INSERT IGNORE INTO phrase_craft ( phrase_id, recipe_id, num, action_trigger_id ) VALUES ( $id, '$_POST[craft_id]', '$_POST[craft_num]', '$_POST[craft_trigger]' )" );
	$expand_what = "craft";
}
if (isset( $HTTP_GET_VARS['add_craft_action'] ))
{
    $craft_id = (int)$_GET['add_craft_action'];
    f_MQuery("LOCK TABLES phrases WRITE, phrase_craft WRITE");
    if (!f_MValue("SELECT action_phrase_id FROM phrase_craft WHERE phrase_id=$id AND recipe_id=$craft_id"))
    {
        f_MQuery( "INSERT INTO phrases ( text ) VALUES ( '�������� ��� ����� $id ����� ������ �����' )" );
        $q = mysql_insert_id( );
        f_MQuery("UPDATE phrase_craft SET action_phrase_id=$q WHERE phrase_id=$id AND recipe_id=$craft_id");
    }
    f_MQuery("UNLOCK TABLES");
	$expand_what = "craft";
}
if( isset( $HTTP_GET_VARS[del_craft] ) )
{
	f_MQuery( "DELETE FROM phrase_craft WHERE recipe_id=$HTTP_GET_VARS[del_craft] and phrase_id=$id" );
	$expand_what = "craft";
}
if( isset( $HTTP_POST_VARS[mine_id] ) )
{
	f_MQuery( "INSERT IGNORE INTO phrase_mine ( phrase_id, item_id, num, action_trigger_id ) VALUES ( $id, '$_POST[mine_id]', '$_POST[mine_num]', '$_POST[mine_trigger]' )" );
	$expand_what = "mine";
}
if (isset( $HTTP_GET_VARS['add_mine_action'] ))
{
    $mine_id = (int)$_GET['add_mine_action'];
    f_MQuery("LOCK TABLES phrases WRITE, phrase_mine WRITE");
    if (!f_MValue("SELECT action_phrase_id FROM phrase_mine WHERE phrase_id=$id AND item_id=$mine_id"))
    {
        f_MQuery( "INSERT INTO phrases ( text ) VALUES ( '�������� ��� ����� $id ����� ������ �����' )" );
        $q = mysql_insert_id( );
        f_MQuery("UPDATE phrase_mine SET action_phrase_id=$q WHERE phrase_id=$id AND item_id=$mine_id");
    }
    f_MQuery("UNLOCK TABLES");
	$expand_what = "mine";
}
if( isset( $HTTP_GET_VARS[del_mine] ) )
{
	f_MQuery( "DELETE FROM phrase_mine WHERE item_id=$HTTP_GET_VARS[del_mine] and phrase_id=$id" );
	$expand_what = "mine";
}
if( isset( $HTTP_POST_VARS[monster_id] ) )
{
	f_MQuery( "INSERT IGNORE INTO phrase_monsters ( phrase_id, mob_id, num, action_trigger_id ) VALUES ( $id, '$_POST[monster_id]', '$_POST[monster_num]', '$_POST[monster_trigger]' )" );
	$expand_what = "monsters";
}
if (isset( $HTTP_GET_VARS['add_monster_action'] ))
{
    $monster_id = (int)$_GET['add_monster_action'];
    f_MQuery("LOCK TABLES phrases WRITE, phrase_monsters WRITE");
    if (!f_MValue("SELECT action_phrase_id FROM phrase_monsters WHERE phrase_id=$id AND mob_id=$monster_id"))
    {
        f_MQuery( "INSERT INTO phrases ( text ) VALUES ( '�������� ��� ����� $id ����� �������� ��������' )" );
        $q = mysql_insert_id( );
        f_MQuery("UPDATE phrase_monsters SET action_phrase_id=$q WHERE phrase_id=$id AND mob_id=$monster_id");
    }
    f_MQuery("UNLOCK TABLES");
	$expand_what = "monsters";
}
if( isset( $HTTP_GET_VARS[del_monster] ) )
{
	f_MQuery( "DELETE FROM phrase_monsters WHERE mob_id=$HTTP_GET_VARS[del_monster] and phrase_id=$id" );
	$expand_what = "monsters";
}
if( isset( $HTTP_POST_VARS[trigger_id] ) )
{
	f_MQuery( "INSERT INTO phrase_triggers ( phrase_id, trigger_id, regime ) VALUES ( $id, $HTTP_POST_VARS[trigger_id], $HTTP_POST_VARS[regime] )" );
	$expand_what = "triggers";
}
if( isset( $HTTP_POST_VARS[value_id] ) )
{
	f_MQuery( "INSERT INTO phrase_values ( phrase_id, value_id, regime, value ) VALUES ( $id, $HTTP_POST_VARS[value_id], $HTTP_POST_VARS[regime], $HTTP_POST_VARS[value] )" );
	$expand_what = "values";
}
if( isset( $HTTP_GET_VARS[del_trigger] ) )
{
	f_MQuery( "DELETE FROM phrase_triggers WHERE ( phrase_id=$id AND trigger_id=$HTTP_GET_VARS[del_trigger] AND regime=$HTTP_GET_VARS[regime] )" );
	$expand_what = "triggers";
}
if( isset( $HTTP_GET_VARS[del_value] ) )
{
	f_MQuery( "DELETE FROM phrase_values WHERE ( phrase_id=$id AND value_id=$HTTP_GET_VARS[del_value] AND regime=$HTTP_GET_VARS[regime] )" );
	$expand_what = "values";
}
if( isset( $HTTP_POST_VARS[quest_id] ) )
{
	f_MQuery( "INSERT INTO phrase_quests ( phrase_id, quest_id, status ) VALUES ( $id, $HTTP_POST_VARS[quest_id], $HTTP_POST_VARS[status] )" );
	$expand_what = "quests";
}
if( isset( $HTTP_GET_VARS[del_quest] ) )
{
	f_MQuery( "DELETE FROM phrase_quests WHERE ( phrase_id=$id AND quest_id=$HTTP_GET_VARS[del_quest] AND status=$HTTP_GET_VARS[status] )" );
	$expand_what = "quests";
}

if( isset( $HTTP_POST_VARS[quest_if_id] ) )
{
	f_MQuery( "INSERT INTO phrase_quests_if ( phrase_id, phrase_status, quest_id, quest_status ) VALUES ( $id, $HTTP_POST_VARS[status], $HTTP_POST_VARS[quest_if_id], $HTTP_POST_VARS[quest_status] )" );
	$expand_what = "quests_if";
}

if( isset( $HTTP_GET_VARS[del_quest_if] ) )
{
	f_MQuery( "DELETE FROM phrase_quests_if WHERE ( phrase_id=$id AND quest_id=$HTTP_GET_VARS[del_quest_if] AND phrase_status=$HTTP_GET_VARS[status] AND quest_status=$HTTP_GET_VARS[quest_status] )" );
	$expand_what = "quests_if";
}

$res = f_MQuery( "SELECT * FROM phrases WHERE phrase_id = $id" );

$arr = f_MFetch( $res );

?>


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
</script>

<?

$st_total = '';

if( $from[0] == 'f' && $from[1] == 'a' && $from[2] == 'a' )
{
	$moo = substr( $from, 3 );
	print( "<a href=forest_additional_actions_editor.php?id=$moo>����� � �������� ���. �������� � ����</a><br><br>" );
}
else if( $from[0] == 't' && $from[1] == 'a' && $from[2] == 'c' )
{
	$moo = substr( $from, 3 );
	print( "<a href=npc_editor_mid.php?id=$moo>����� � �������� NPC</a><br><br>" );
}
else if( $from[0] == 'p' && $from[1] == 'm' && $from[2] == 'n' )
{
	$moo = str_replace(",", "&from=", substr( $from, 3 ));
	print( "<a href=phrase_editor.php?id=$moo>����� � �������� �����</a><br><br>" );
}
else
	print( "<a href=talk_editor.php?talk_id=$from>����� � �������� ������</a><br><br>" );

print( "<table><tr><td>" );
print( "<b>Phrase UIN: $arr[phrase_id]</b><br>" );
print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( "<textarea name=text rows=10 cols=50>$arr[text]</textarea>" );
print( "<br><input type=checkbox name=drop_mines ".(($arr['drop_mines'])?'checked':'')."> ���������� ����-���� (���� �������� player_mines)" );
print( "<br>���������� �������: <input type=text name=minlevel value='$arr[minlevel]'> - <input type=text name=maxlevel value='$arr[maxlevel]'>" );
print( "<br>�������� �����: <input type=text name=attack_id value='$arr[attack_id]'>" );
print( "<br>�������� ������ ���� ������� �� ����� ����, ���� �������� ����� ��������.<br>�������� ������ ���� ������� �� �������� � ����� � ����, ������ -��������.<br>��������, ������ ����, ������� � ���������� ���������.<br>" );
print( "<br><input type=submit value='��������'>" );
print( "</form>" );

print( "<br>" );
print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('exp')\" id=iexp src='../images/e_plus.gif'>&nbsp;<b>�����</b><br>" );

print( "<div id=dexp style='display:none'><form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( "���� �����: <input type=text name=expr value='$arr[gain_exp]'> (������������� ��������, ���� ����� ������ ���� �����)<br>" );
print( "���� �����: <input type=text name=pexpr value='$arr[gain_prof]'> (������������� ��������, ���� ��������� ������ ���� �����)<br>" );
if( $arr['gain_exp'] != 0 ) $st_total .= "���� ����� <b>$arr[gain_exp]</b><br>";
if( $arr['gain_prof'] != 0 ) $st_total .= "���� ��������� <b>$arr[gain_prof]</b><br>";
print( "<br><input type=submit value='����������'>" );
print( "</form>" );

print( "<hr></div>" );

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('rand')\" id=irand src='../images/e_plus.gif'>&nbsp;<b>������</b><br>" );

print( "<div id=drand style='display:none'><form action=phrase_editor.php?id=$id&from=$from method=post>" );
$chance = $arr['chance1000000'] / 10000.0;
print( "���� ���� ���������� (� 1/1000000): <input type=text name=chance value='$arr[chance1000000]'> (������: $chance%)" );
if( $chance != 100 ) $st_total .= "���� ���� ����������: <b>$chance%</b><br>";
print( "<br><input type=submit value='����������'>" );
print( "</form>" );

print( "<hr></div>" );


//-----------------------------------

$loc_names2 = $loc_names;
$loc_names2[-1] = "�� ����������";

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('warp')\" id=iwarp src='../images/e_plus.gif'>&nbsp;<b>��������</b><br>" );

print( "<div id=dwarp style='display:none'><form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( "�������: ".create_select( "warp_loc", $loc_names2, $arr[warp_loc] ) );
print( "�������: <input type=text name=warp_depth class=m_btn value={$arr[warp_depth]}>" );
print( "<br><input type=submit value='����������'>" );
print( "</form>" );

if( $arr['warp_loc'] != -1 ) $st_total .= "������ � <b>{$loc_names2[$arr[warp_loc]]} : $arr[warp_depth]</b><br>";

print( "<hr></div>" );

// -------------------------

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('guilds')\" id=iguilds src='../images/e_plus.gif'>&nbsp;<b>�������</b><br>" );

print( "<div id=dguilds style='display:none'><form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( "��������� ���� ��� �������: ".create_select( 'req_guild_slot', $req_guild_slot_values, $arr[req_guild_slot] ) )."<br>";
if( $arr[req_guild_slot] == -1 ) $st_total .= "������� ���������� ���������� ����� ��� �������<br>";
if( $arr[req_guild_slot] == 1 ) $st_total .= "������� ������� ���������� ����� ��� �������<br>";
print( "<input type=submit value='����������'>" );
print( "</form>" );

$gres = f_MQuery( "SELECT phrase_guilds.* FROM phrase_guilds WHERE phrase_id = $id ORDER BY action" );
while( $garr = f_MFetch( $gres ) )
{
	$gname = $moo_guilds[$garr[guild_id]];
	if( !$gname ) $gname = "(����� �����-��)";
	if( $garr[action] <= 4 ) $act = $guild_regimes[$garr[action]];
	else $act = $guild_regimes_wv[$garr[action]];
	$st_cur = '';
	$st_cur .= "$act; �������: $gname;";
	if( $garr[action] > 4 ) $st_cur .= " ��������: $garr[value];";
	echo $st_cur .= " &nbsp;";
	$st_total .= $st_cur."<br>";
	echo $st_cur."<a href=phrase_editor.php?id=$id&from=$from&del_guild=1&guild_id=$garr[guild_id]&action=$garr[action]&value=$garr[value]>�������</a><br>";
}

print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( create_select( "regime", $guild_regimes, 0 ) );
print( "�������: ".create_select( "guild", $moo_guilds, 0 ) );
print( "<input class=m_btn type=submit>" );
print( "</form>" );
print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( create_select( "regime", $guild_regimes_wv, 0 ) );
print( " �������: ".create_select( "guild", $moo_guilds, 0 ) );
print( " ��������: <input type=text name=value class=m_btn value=0><input class=m_btn type=submit>" );
print( "</form><br>" );

print( "<hr></div>" );
print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('items')\" id=iitems src='../images/e_plus.gif'>&nbsp;<b>����</b><br><div id=ditems style='display:none'>" );

$res = f_MQuery( "SELECT items.name, phrase_items.* FROM items RIGHT JOIN phrase_items ON items.item_id = phrase_items.item_id WHERE phrase_items.item_id != 0 AND phrase_id = $id UNION SELECT '������', phrase_items.* FROM phrase_items WHERE phrase_id = $id AND phrase_items.item_id = 0 ORDER BY regime" );

while( $arr = f_MFetch( $res ) )
{
	if( $arr['name'] == null ) $arr['name'] = '(�����-�� �����)';
	$st_cur = "[$arr[number]] <b>$arr[name]</b> {$regimes[$arr[regime]]} ";
	print( "$st_cur(<a href=phrase_editor.php?id=$id&from=$from&del_item=$arr[item_id]&regime=$arr[regime]>�������</a>)<br>" );
	$st_total .= "$st_cur<br>";
}	
	
print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( create_select( "regime", $regimes, 0 ) );
print( " UIN ����: <input type=text name=item_id class=m_btn value=0>" );
print( " ���-��: <input type=text name=number class=m_btn value=0><input class=m_btn type=submit><br>" );
print( "</form>" );
print( "� ������� �������, ������� (���� ����������) � ������� ������ ���������� ������������. ���� ���� ��������� � ����������� - ������� ������������ ������� �� ������.<br>� ������ �������� (���� ����������) �� �������� �� ���������� ����� ������ ���� ���������" );


print( "<hr></div>" );

// - ������� - �������

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('effects')\" id=ieffects src='../images/e_plus.gif'>&nbsp;<b>������� - �������</b><br><div id=deffects style='display:none'>" );

$efres = f_MQuery( "SELECT * FROM phrase_effects WHERE phrase_id=$id" );
if( f_MNum( $efres ) )
{
	$st_total .= "<b>��������� �������</b>";
	$row1 = "<table cellspacing=0 cellpadding=0 border=0>";
	$row2 = "<table cellspacing=0 cellpadding=0 border=0>";
    while( $efarr = f_MFetch( $efres ) )
    {
    	$v = "<tr><td><img title='$arr[descr]' src='../images/effects/$efarr[image]'></td>";
    	$v .= "<td>$efarr[name]<br>".my_time_str( $efarr['duration'], /*show seconds=*/false )."<br>$efarr[effect]</td>";
    
    	$row1 .= $v;
    	$row2 .= $v;
    	
    	$row2 .= "<td><a href=phrase_editor.php?id=$id&from=$from&del_effect=$efarr[entry_id]>�������</a></td>";
    	
    	$row1 .= "</tr>";
    	$row2 .= "</tr>";
    }
    $row1 .= "</table>";
    $row2 .= "</table>";
    
    $st_total .= $row1;
    echo $row2;
}

print( "<table><form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( "<tr><td> UIN �������: </td><td><input type=text name=effect_id class=m_btn value=0></td></tr>" );
print( "<tr><td> ��������: </td><td><input type=text name=effect_name class=m_btn></td></tr>" );
print( "<tr><td> ��������: </td><td><input type=text name=effect_image class=m_btn></td></tr>" );
print( "<tr><td> ��������: </td><td><input type=text name=effect_descr class=m_btn></td></tr>" );
print( "<tr><td> ��� �������: </td><td><input type=text name=effect_type class=m_btn value=0>&nbsp;0 - ������� ������, 1 - ������</td></tr>" );
print( "<tr><td> ������: </td><td><input type=text name=effect_effect class=m_btn value='101:10.'></td></tr>" );
print( "<tr><td> ������������, ���: </td><td><input type=text name=effect_duration class=m_btn>&nbsp;-1 ��� ��������</td></tr>" );
print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='��������'><br></td></tr>" );
print( "</form></table>" );

print( "<hr></div>" );

// - ������� - ����������

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('effects_if')\" id=ieffects_if src='../images/e_plus.gif'>&nbsp;<b>������� - ����������</b><br><div id=deffects_if style='display:none'>" );

$efres = f_MQuery( "SELECT * FROM phrase_effects_if WHERE phrase_id=$id" );
while( $efarr = f_MFetch( $efres ) )
{
	$st_cur = "������� ������ UIN=$efarr[2] � ���������� ".$effect_regimes[$efarr[1]]." ".$efarr[3];
	echo $st_cur." <a href=phrase_editor.php?id=$id&from=$from&del_effect_if=$efarr[2]>�������</a><br>";
	$st_total .= $st_cur."<br>";
}

print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( "UIN �������: <input type=text name=effect_if_id class=m_btn value=0>" );
print ("&nbsp;�������&nbsp;");
print( create_select( "status", $effect_regimes, 1 ) );
print( "&nbsp;����������: <input type=text name=num class=m_btn value=0>" );
print( "<br><input class=m_btn type=submit value='��������'><br>" );
print( "</form>" );

print( "<hr></div>" );

// - SMILEYS -----------------------------------

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('smiles')\" id=ismiles src='../images/e_plus.gif'>&nbsp;<b>��������</b><br><div id=dsmiles style='display:none'>" );

$efres = f_MQuery( "SELECT * FROM phrase_smiles WHERE phrase_id=$id" );
if( f_MNum( $efres ) )
{
    while( $efarr = f_MFetch( $efres ) )
    {
    	$v = "$efarr[smile_id] �� ".my_time_str( $efarr['duration'], /*show seconds=*/false );
        $st_total .= "������� ".$v."<br>";
    	$v .= " -- <a href=phrase_editor.php?id=$id&from=$from&del_smile=$efarr[smile_id]>�������</a>";
    	echo $v."<br>";
    }
}

print( "<table><form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( "<tr><td> UIN ��������: </td><td><input type=text name=smile_id class=m_btn value=0></td></tr>" );
print( "<tr><td> ������������, ���: </td><td><input type=text name=smile_duration class=m_btn>&nbsp;-1 ��� ��������</td></tr>" );
print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='��������'><br></td></tr>" );
print( "</form></table>" );

print( "<hr></div>" );


// - PREMIUMS -----------------------------------

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('premiums')\" id=ipremiums src='../images/e_plus.gif'>&nbsp;<b>��������</b><br><div id=dpremiums style='display:none'>" );

$efres = f_MQuery( "SELECT * FROM phrase_premiums WHERE phrase_id=$id" );
if( f_MNum( $efres ) )
{
    while( $efarr = f_MFetch( $efres ) )
    {
    	$v = "{$premium_names[$efarr[premium_id]]} �� $efarr[duration] ".my_word_str( $efarr['duration'], "����", "���", "����" );
        $st_total .= "������� ".$v."<br>";
    	$v .= " -- <a href=phrase_editor.php?id=$id&from=$from&del_premium=$efarr[premium_id]>�������</a>";
    	echo $v."<br>";
    }
}

print( "<table><form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( "<tr><td> ����� �������: </td><td>".create_select( "premium_id", $premium_names, 0 )."<td></tr>" );
print( "<tr><td> ������������, ����: </td><td><input type=text name=premium_duration class=m_btn></td></tr>" );
print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='��������'><br></td></tr>" );
print( "</form></table>" );

print( "<hr></div>" );


// - QUEST VALS AND TRIGGERS -----------------------------------

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('values')\" id=ivalues src='../images/e_plus.gif'>&nbsp;<b>�������� ���������</b><br><div id=dvalues style='display:none'>" );

$res = f_MQuery( "SELECT * FROM phrase_values WHERE phrase_id = $id ORDER BY regime" );

while( $arr = f_MFetch( $res ) )
{
	$st_cur = "<b>$arr[value_id]</b> {$value_regimes[$arr[regime]]} <u>$arr[value]</u> ";
	print( "$st_cur(<a href=phrase_editor.php?id=$id&from=$from&del_value=$arr[value_id]&regime=$arr[regime]>�������</a>)<br>" );
	$st_total .= "�������� ".$st_cur."<br>";
}

print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( create_select( "regime", $value_regimes, 0 ) );
print( " ID ��������: <input type=text name=value_id class=m_btn value=0>" );
print( " ��������: <input type=text name=value class=m_btn value=0>" );
print( "<input class=m_btn type=submit><br>" );
print( "</form>" );

print( "<hr></div>" );
print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('triggers')\" id=itriggers src='../images/e_plus.gif'>&nbsp;<b>��������</b><br><div id=dtriggers style='display:none'>" );

$res = f_MQuery( "SELECT * FROM phrase_triggers WHERE phrase_id = $id ORDER BY regime" );

while( $arr = f_MFetch( $res ) )
{
	$st_cur = "<b>$arr[trigger_id]</b> {$regimes2[$arr[regime]]} ";
	print( "$st_cur(<a href=phrase_editor.php?id=$id&from=$from&del_trigger=$arr[trigger_id]&regime=$arr[regime]>�������</a>)<br>" );
	$st_total .= "������� $st_cur<br>";
}
	
print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( create_select( "regime", $regimes2, 0 ) );
print( " trigger_id: <input type=text name=trigger_id class=m_btn value=0>" );
print( "<input class=m_btn type=submit><br>������������ ������� �12345 <b>���������!!!</b>" );
print( "</form>" );


print( "<hr></div>" );


// ������ - ������

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('quests')\" id=iquests src='../images/e_plus.gif'>&nbsp;<b>������ - ������</b><br><div id=dquests style='display:none'>" );

$res = f_MQuery( "SELECT quests.name, phrase_quests.* FROM quests RIGHT JOIN phrase_quests ON quests.quest_id = phrase_quests.quest_id WHERE phrase_id = $id ORDER BY quest_id, status" );

$largest_quest_status = 0;
while( $arr = f_MFetch( $res ) )
{
	if( $arr['name'] == null ) $arr['name'] = '(�����-�� �����)';
	$st_cur = "<b>$arr[name]</b> {".moo( $arr[status] )."} ";
    if ((int)$arr['status'] > $largest_quest_status) $largest_quest_status = (int)$arr['status'];
	print( "$st_cur(<a href=phrase_editor.php?id=$id&from=$from&del_quest=$arr[quest_id]&status=$arr[status]>�������</a>)<br>" );
	$st_total .= "����� $st_cur <br>";
}
	
print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( " quest_id: <input type=text name=quest_id class=m_btn value=0>" );
print( " status: <input type=text name=status class=m_btn value=0>" );
print( "<input class=m_btn type=submit><br>(0 - ����, -1 - ���������, -2 - ���������, -5 - �������)" );
print( "</form><hr></div>" );

// ������ - �������� ����������
print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('quests_if')\" id=iquests_if src='../images/e_plus.gif'>&nbsp;<b>������ - �������� ����������</b><br><div id=dquests_if style='display:none'>" );

$res = f_MQuery( "SELECT quests.name, phrase_quests_if.* FROM quests RIGHT JOIN phrase_quests_if ON quests.quest_id = phrase_quests_if.quest_id WHERE phrase_id = $id ORDER BY quests.quest_id" );


while( $arr = f_MFetch( $res ) )
{
	if( $arr['name'] == null ) $arr['name'] = '(�����-�� �����)';
	
	$st_cur = $req_quest[$arr[2]]." ".moo1( $arr[4] )." <b>$arr[name]</b> ";

	print( "$st_cur(<a href=phrase_editor.php?id=$id&from=$from&del_quest_if=$arr[3]&status=$arr[2]&quest_status=$arr[4]>�������</a>)<br>" );
	$st_total .= $st_cur."<br>";
}
	
print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( create_select( "status", $req_quest, 0 ) );
print( "<br>quest_id: <input type=text name=quest_if_id class=m_btn value=0>" );
print( "<br>status: <input type=text name=quest_status class=m_btn value=0> (-2 - ��������, -1 - ��������, 0 - �������, >0 - ������)" );
print( "<br><input class=m_btn type=submit><br>" );
print( "</form><hr></div>" );

$has_cont_act = false;
// �������

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('monsters')\" id=imonsters src='../images/e_plus.gif'>&nbsp;<b>������� -- �����</b><br><div id=dmonsters style='display:none'>" );
print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );

$res = f_MQuery( "SELECT mobs.name, phrase_monsters.* FROM mobs RIGHT JOIN phrase_monsters ON mobs.mob_id = phrase_monsters.mob_id WHERE phrase_id = $id ORDER BY mob_id, num" );

while( $arr = f_MFetch( $res ) )
{
	if( $arr['name'] == null ) $arr['name'] = '(�����-�� �����)';
	$st_cur = "<b>$arr[name]</b> [$arr[num]]. ������ �������: $arr[action_trigger_id]. ";
	$st_total .= "����� $st_cur ";
    if ($arr['action_phrase_id'] == 0)
    {
        $st_cur .= "<a href=phrase_editor.php?id=$id&from=$from&add_monster_action=$arr[mob_id]>�������� ������ ��������</a> ";
    }
    else 
    {
        $st_cur .= "<a href=phrase_editor.php?id=$arr[action_phrase_id]&from=pmn$id,$from>������������� ������ ��������</a> ";
        $st_total .= "+������ ��������";
    }
    $st_total .= "<br>";
	print( "$st_cur(<a href=phrase_editor.php?id=$id&from=$from&del_monster=$arr[mob_id]>�������</a>)<br>" );
    $has_cont_act = true;
}

print( " ���� ����: <input type=text name=monster_id class=m_btn value=0><br>" );
print( " ������� �����: <input type=text name=monster_num class=m_btn value=0><br>" );
print( " ����� ������� ��������� ����� �����: <input type=text name=monster_trigger class=m_btn value=0><br>" );
print( "<input class=m_btn type=submit><br>" );
print( "</form><hr></div>" );

// ���� ������

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('mine')\" id=imine src='../images/e_plus.gif'>&nbsp;<b>���� -- ������</b><br><div id=dmine style='display:none'>" );
print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );

$res = f_MQuery( "SELECT items.name, phrase_mine.* FROM items RIGHT JOIN phrase_mine ON items.item_id = phrase_mine.item_id WHERE phrase_id = $id ORDER BY item_id, num" );

while( $arr = f_MFetch( $res ) )
{
	if( $arr['name'] == null ) $arr['name'] = '(�����-�� �����)';
	$st_cur = "<b>$arr[name]</b> [$arr[num]]. ������ �������: $arr[action_trigger_id]. ";
	$st_total .= "������ $st_cur ";
    if ($arr['action_phrase_id'] == 0)
    {
        $st_cur .= "<a href=phrase_editor.php?id=$id&from=$from&add_mine_action=$arr[item_id]>�������� ������ ��������</a> ";
    }
    else 
    {
        $st_cur .= "<a href=phrase_editor.php?id=$arr[action_phrase_id]&from=pmn$id,$from>������������� ������ ��������</a> ";
        $st_total .= "+������ ��������";
    }
    $st_total .= "<br>";
	print( "$st_cur(<a href=phrase_editor.php?id=$id&from=$from&del_mine=$arr[item_id]>�������</a>)<br>" );
    $has_cont_act = true;
}

print( " ���� ����: <input type=text name=mine_id class=m_btn value=0><br>" );
print( " ������� ������: <input type=text name=mine_num class=m_btn value=0><br>" );
print( " ����� ������� ��������� ����� ������: <input type=text name=mine_trigger class=m_btn value=0><br>" );
print( "<input class=m_btn type=submit><br>" );
print( "</form><hr></div>" );

// ���� ���������

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('craft')\" id=icraft src='../images/e_plus.gif'>&nbsp;<b>���� -- ���������</b><br><div id=dcraft style='display:none'>" );
print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );

$res = f_MQuery( "SELECT recipes.name, phrase_craft.* FROM recipes RIGHT JOIN phrase_craft ON recipes.recipe_id = phrase_craft.recipe_id WHERE phrase_id = $id ORDER BY recipe_id, num" );

while( $arr = f_MFetch( $res ) )
{
	if( $arr['name'] == null ) $arr['name'] = '(�����-�� �����)';
	$st_cur = "<b>$arr[name]</b> [$arr[num]]. ������ �������: $arr[action_trigger_id]. ";
	$st_total .= "��������� $st_cur ";
    if ($arr['action_phrase_id'] == 0)
    {
        $st_cur .= "<a href=phrase_editor.php?id=$id&from=$from&add_craft_action=$arr[recipe_id]>�������� ������ ��������</a> ";
    }
    else 
    {
        $st_cur .= "<a href=phrase_editor.php?id=$arr[action_phrase_id]&from=pmn$id,$from>������������� ������ ��������</a> ";
        $st_total .= "+������ ��������";
    }
    $st_total .= "<br>";
	print( "$st_cur(<a href=phrase_editor.php?id=$id&from=$from&del_craft=$arr[recipe_id]>�������</a>)<br>" );
    $has_cont_act = true;
}

print( " ���� �������: <input type=text name=craft_id class=m_btn value=0><br>" );
print( " ������� ��� ���������: <input type=text name=craft_num class=m_btn value=0><br>" );
print( " ����� ������� ��������� ����� ����������: <input type=text name=craft_trigger class=m_btn value=0><br>" );
print( "<input class=m_btn type=submit><br>" );
print( "</form><hr></div>" );

if ($has_cont_act)
{
    if ($largest_quest_status == 0)
    {
        $st_total .= "<b><font color=darkred>��������!</font></b> ����� ���� ������� �� �����, ������ ��� �������� ��������,<br> �� �� ��������� ����� ������� � �������! ������� �� ����� �������� ���<br> ������, ������ ��� ��������!<br>";
    }
    else
    {
        $st_total .= "��� ������� �� �����/������/�������� ����� ���������� � <b>��������� ����� $largest_quest_status</b><br>";
    }
}


// Clans

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('clans')\" id=iclans src='../images/e_plus.gif'>&nbsp;<b>�����</b><br><div id=dclans style='display:none'>" );

$res = f_MQuery("SELECT * FROM phrase_clans WHERE phrase_id=".$id);

while ($arr = f_MFetch($res))
{
	$cl_name = f_MValue("SELECT name FROM clans WHERE clan_id=".$arr['clan_id']);
	$st_cur = $req_clan_values[$arr['action']]." <b>".$cl_name." ({$arr[clan_id]})</b>";
	$st_total .= $st_cur."<br>";
	print($st_cur."&nbsp;");
	print("<a href=phrase_editor.php?id=$id&from=$from&del_clan=$arr[clan_id]>�������</a><br>");
}

print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( " clan_id: <input type=text name=clan_id class=m_btn value=0>" );
print( create_select("regime", $req_clan_values, 0) );
print( "<input class=m_btn type=submit><br>" );
print( "</form><hr></div>" );

// Sex

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('sex')\" id=isex src='../images/e_plus.gif'>&nbsp;<b>����</b><br><div id=dsex style='display:none'>" );

$res = f_MQuery("SELECT * FROM phrase_sex WHERE phrase_id=".$id);

if (!f_MNum($res))
	print("������ �������<br>");

while ($arr = f_MFetch($res))
{
	print($reg_sex[$arr['sex']]."&nbsp;<a href=phrase_editor.php?id=$id&from=$from&del_sex=$id>�������</a>");
	$st_total .= $reg_sex[$arr['sex']]."<br>";
}

print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( create_select("sex", $reg_sex, 0) );
print( "<input class=m_btn type=submit><br>" );
print( "</form><hr></div>" );

// Hour

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('hour')\" id=ihour src='../images/e_plus.gif'>&nbsp;<b>����</b><br><div id=dhour style='display:none'>" );

$res = f_MQuery("SELECT * FROM phrase_hours WHERE phrase_id=".$id);

if (!f_MNum($res))
	print("�� ����� ���������<br>");
else
	print("���������<br>");

while ($arr = f_MFetch($res))
{
	print("� ".$arr['hour']." �� ".$arr[2]."&nbsp;<a href=phrase_editor.php?id=$id&from=$from&del_hour=$arr[1]>�������</a><br>");
	$st_total .= "����. ��������� � ".$arr[1]." �� ".$arr[2]."<br>";
}

print("<br>���������<br>");
print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );
print( "� <input type=text class=m_btn value=0 name=hour>");
print( " �� <input type=text class=m_btn value=0 name=hour1>");
print( "<input class=m_btn type=submit><br>" );
print( "</form><hr></div>" );

// - ����

print( "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('data_req')\" id=idata_req src='../images/e_plus.gif'>&nbsp;<b>����(��� �� ������)</b><br><div id=ddata_req style='display:none'>" );

$efres = f_MQuery( "SELECT * FROM phrase_data_req WHERE phrase_id=$id" );
while( $efarr = f_MFetch( $efres ) )
{
	$st_cur = "������� ������ UIN=$efarr[2] � ���������� ".$effect_regimes[$efarr[1]]." ".$efarr[3];
	echo $st_cur." <a href=phrase_editor.php?id=$id&from=$from&del_effect_if=$efarr[2]>�������</a><br>";
	$st_total .= $st_cur."<br>";
}

print( "<form action=phrase_editor.php?id=$id&from=$from method=post>" );

print( "<br><input class=m_btn type=submit value='��������'><br>" );
print( "</form>" );

print( "<hr></div>" );

print( "</td><td valign=top>$st_total</td></tr></table>" );

if( $expand_what !== false ) echo "<script>expand_pe( '$expand_what' );</script>";

?>
</body></html>
