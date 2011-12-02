<?

include_once( '../skin.php' );

?>

function begin_help( title )
{
	document.write( '<?echo AddSlashes( GetScrollTableStart( ) );?><h1><center>' + title + '</center></h1><table width=100%><tr><td>' );
}

function end_help( )
{
	document.write( "</td></tr></table><?echo AddSlashes( GetScrollTableEnd( ) );?>" );
}