<?
require_once("time_functions.php");


include( 'functions.php' );
include( 'forum_search.php' );
include( 'textedit.php' );

f_MConnect( );

$res = f_MQuery( "SELECT last_post_indexed FROM statistics" );
$arr = f_MFetch( $res );
$last_post = $arr[0];

/*$res = f_MQuery( "SELECT id FROM forum_rooms WHERE id >= 0" );
while( $arr = f_MFetch( $res ) )
{
	fclose( fopen( "forum_index/f{$arr[0]}.dat", 'w+' ) );
}*/


$res = f_MQuery( "SELECT * FROM forum_posts WHERE post_id > $last_post ORDER BY post_id LIMIT 50" );
while( $arr = f_MFetch( $res ) )
{
	$last_post = $arr['post_id'];

	$tarr = f_MFetch( f_MQuery( "SELECT room_id FROM forum_threads WHERE thread_id=$arr[thread_id]" ) );
	if( $tarr[0] < 0 ) continue;
	$f = new ForumSearch( "C:\\inetpub\\wwwroot\\alideria\\forum_index\\f{$tarr[0]}.dat" );
	$f->Index( process_str_none( $arr['text'] ), $arr['thread_id'], $arr['post_id'] );
}

f_MQuery( "UPDATE statistics SET last_post_indexed = $last_post" );

?>
