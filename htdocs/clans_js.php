<?

include_once( 'functions.php' );

f_MConnect( );

$res = f_MQuery( "SELECT * FROM clans" );
$file = fopen( 'js/clans.php', 'wt+' );
fwrite( $file, "var clans = new Array( );\n" );

while( $arr = f_MFetch( $res ) )
{
	fwrite( $file, "clans[$arr[clan_id]] = new Array( '$arr[name]', '$arr[icon]', '$arr[clan_id]' );\n" );
}

fclose( $file );

?>
<script>
location.href='game.php';
</script>