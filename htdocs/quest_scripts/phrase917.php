<?

if( !$mid_php ) die( );

$player->SetLocation(1, true);
$player->SetDepth(4224, true);
$player->SetTrigger(12905);
f_MQuery("DELETE FROM player_talks WHERE player_id=".$player->player_id);
include_once("create_combat.php");
if (f_MValue("SELECT SUM(value) FROM player_quest_values WHERE value_id=12905")<1000)
ccAttackPlayer( $player->player_id, 2121002, 0, true, false );
else
ccAttackPlayer( $player->player_id, 2125260, 0, true, false );
echo "<script>location.href='combat.php';</script>";

?>