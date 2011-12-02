<?

include( '../functions.php' );

$id = $HTTP_POST_VARS['id'];
$name = $HTTP_POST_VARS['nm'];
$min_level = $HTTP_POST_VARS['min_level'];
$max_level = $HTTP_POST_VARS['max_level'];
$prize = $HTTP_POST_VARS['prize'];
$type = (int)$HTTP_POST_VARS['type'];
$date = mktime( $_POST['bdh'], $_POST['bdi'], 0, $_POST['bdm'], $_POST['bdd'], $_POST['bdy'] );

settype( $date, 'integer' );
if( $date < 0 ) $date = 0;

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_POST_VARS['del'] ) )
	f_MQuery( "DELETE FROM tournament_announcements WHERE tournament_id=$id" );
else
{
	f_MQuery( "UPDATE tournament_announcements SET type='$type', name='$name', min_level=$min_level, max_level=$max_level, prize=$prize, date=$date WHERE tournament_id=$id" );
}

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='tournament_editor_mid.php?id=$id';\n" ); ?>
</script>
