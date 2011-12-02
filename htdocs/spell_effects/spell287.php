$value = 150;
$pid = $sdk->me->player->player_id;
$res = f_MQuery( "SELECT c.genre FROM cards as c INNER JOIN combat_players as p ON c.card_id=p.lcard WHERE p.player_id=$pid" );
$arr = f_MFetch( $res );
if( $arr && $arr[0] == 2 ) $sdk->damage(  $sdk->opponent, $value );
else if( $arr && $arr[0] == 1 ) $sdk->heal( $sdk->myself, $value );
else $sdk->damage( $sdk->myself, $value );
