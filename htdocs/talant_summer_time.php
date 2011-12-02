<?

include_once( 'player.php' );

f_MConnect( );

f_MQuery( "LOCK TABLE statistics WRITE, player_log WRITE" );
$last_entry = f_MValue( "SELECT last_log_entry_talants FROM statistics" );
f_MQuery( "update statistics set last_log_entry_talants = (select max(entry_id) from player_log);" );
f_MQuery( "UNLOCK TABLES" );

$res = f_MQuery( "SELECT * FROM player_log WHERE entry_id > $last_entry AND type=22 AND item_id=-1" );
while( $arr = f_MFetch( $res ) )
{
	$player = new Player( $arr['player_id'] );
	$val = $arr['have'] - $arr['had'];
	$player->AddUMoney( $val );
	$player->syst3( "Вы приобрели <b>$val</b> ".my_word_str($val,'талант','таланта','талантов')." и получаете еще <b>$val</b> ".my_word_str($val,'талант','таланта','талантов')." в подарок!" );
}

?>
