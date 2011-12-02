$p1 = $sdk->me;
$p2 = $sdk->he;

if( $p2->card !== null )
{

$sdk->combat_log_msg( $p2->card->Process2( $p1, $p2, $sdk->we, $sdk->they, $sdk->combat_id, $sdk->turn ) );

if( $sdk->turn % 5 == 0 )
{
$sdk->combat_log_msg( '<br>'. $p2->card->Process2( $p1, $p2, $sdk->we, $sdk->they, $sdk->combat_id, $sdk->turn ) );
}

}
