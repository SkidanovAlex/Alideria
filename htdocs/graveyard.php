<?

$tm = $player->till - time( );
if( $tm <= 3 )
{
	$player->SetRegime( 0 );
	f_MQuery( "UPDATE characters SET go_till = 0 WHERE player_id = {$player->player_id}" );
	print( "<script>location.href = 'game.php';</script>" );
	die( );
}
else
{
	print( "<script src=js/timer.js></script>\n" );
	print( "<script>document.write( InsertTimer( $tm, '<center>Вы проиграли в кровавом бою и в данный момент находитесь в домике лекаря.<br>Вам предстоит провести здесь еще: <b>', '</b>', 1, 'location.href=\"game.php\"' ) );</script>" );
}

?>
