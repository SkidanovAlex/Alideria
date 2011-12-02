<?

if( !$mid_php ) die( );

include( "mob.php" );

f_MQuery( "DELETE FROM player_talks WHERE player_id={$player->player_id}" );
$mob = new Mob;
$mob->CreateMob( 22, 0, 46 );
$mob->AttackPlayer( $player->player_id, 5, 0, true /* нападаем кроваво */ );

?>
<script>
location.href='combat.php';
</script>
