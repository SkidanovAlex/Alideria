<?

if( !$mid_php ) die( );

?>

<br>
<div id=moo name=moo>&nbsp;</div>

<script src=js/timer.js></script>
<script>

show_timer_title = true;
document.write( InsertTimer( <? print ( $player->till - time( ) ); ?>, '<? echo $text ?><b>', '</b>', 0, 'location.href="game.php";' ) );

</script>
