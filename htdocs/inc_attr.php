<?

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "arrays.php" );
include_once( 'attrib_functions.php' );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$attr_id = $HTTP_GET_VARS['a'];
settype( $attr_id, 'integer' );

if( $player->GetAttr( 1000 ) <= 0 )
	die( "<script>alert( 'У вас нет свободных очков распределения!' );</script>" );

$value = $player->GetActualAttr( $attr_id );
$new_value = $value + 1;

$stats = $player->getAllAttrNames( );
$attrs = $player->getSecondaryAttrs( );

$ok = 0;
if( contains( $attrs, $attr_id ) ) $ok = 1;

if( !$ok )
	die( "<script>alert( 'Вы пытаетесь повысить неизвестный параметр. Вы точно не адепт тёмных искусств?' );</script>" );

if( $player->GetActualAttr( $attr_id ) == 0 )
{
	if( $attr_id == 30 ) $spell_id = 185;
	if( $attr_id == 40 ) $spell_id = 186;
	if( $attr_id == 50 ) $spell_id = 187;

	$rres = f_MQuery( "SELECT count( card_id ) FROM player_cards WHERE player_id={$player->player_id} AND card_id=$spell_id" );
	$rarr = f_MFetch( $rres );
	if( !$rarr[0] )
		f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( {$player->player_id}, $spell_id, 10 )" );
}
	
$player->AlterActualAttrib( $attr_id, 1 );
$player->AlterRealAttrib( 1000, -1 );

print( '<META http-equiv=Content-Type content="text/html; charset=windows-1251">' );

echo "<script src=js/cc.js></script>";
print( "<div id=a1 name=a1>" );
$player->ShowBattleAttributes( );
print( "</div><div id=a2 name=a2>" );
$player->ShowSecondaryAttributes( );
print( "</div>" );

?>

<script>

var aa1 = document.getElementById( 'a1' ).innerHTML;
var aa2 = document.getElementById( 'a2' ).innerHTML
parent.document.getElementById( 'a1' ).innerHTML = aa1;
parent.document.getElementById( 'a2' ).innerHTML = aa2;

</script>

<?

?>
