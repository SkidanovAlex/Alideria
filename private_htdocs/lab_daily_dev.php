<?
require_once("time_functions.php");


include( 'functions.php' );
include( 'lab.php' );

f_MConnect( );

LogError( "lab_daily" );

f_MQuery( "DELETE FROM player_labs" );
f_MQuery( "DELETE FROM lab_items" );
f_MQuery( "DELETE FROM lab_mobs" );


$labir_id = 1;
$labir_num = 3;

$lab = new Lab( $labir_id, $labir_num, 30, 30 );
$lab->Init( );
$lab->Generate( );
$lab->Store( );

// Удалить квесты подземелий
$rows = f_MQuery("SELECT trigger_id, player_id FROM player_triggers WHERE trigger_id IN (259, 261, 262)");
while ($row = f_MFetch($rows))
{
    if ($row[0] == 259) { $qid = 63; $qparts = "(269, 270, 271)"; }
    else if ($row[0] == 261) { $qid = 64; $qparts = "(272, 273, 274, 286)"; }
    else if ($row[0] == 262) { $qid = 65; $qparts = "(275, 276, 288)"; }

    f_MQuery("DELETE FROM player_quests WHERE player_id={$row[1]} AND quest_id={$qid}");
    f_MQuery("DELETE FROM player_quest_parts WHERE player_id={$row[1]} AND quest_part_id IN {$qparts}");
    f_MQuery("DELETE FROM player_triggers WHERE player_id={$row[1]} AND trigger_id = {$row[0]}");
}


?>
Moo!
