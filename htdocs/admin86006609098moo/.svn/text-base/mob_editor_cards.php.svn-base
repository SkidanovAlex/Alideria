<?

include( '../functions.php' );

$mob_id = $HTTP_GET_VARS['mob_id'];
$card_id = $HTTP_GET_VARS['card_id'];

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['del'] ) )
	f_MQuery( "DELETE FROM mob_cards WHERE mob_id=$mob_id AND card_id=$card_id" );
else
	f_MQuery( "INSERT INTO mob_cards VALUES ( $mob_id, $card_id )" );

f_MClose( );

?>

<script>
parent.lst.location.reload( );
<? print( "location.href='mob_editor_mid.php?id=$mob_id';\n" ); ?>
</script>
