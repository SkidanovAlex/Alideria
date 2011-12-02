<?

include( 'functions.php' );

f_MConnect( );

include( 'clan_wonders.php' );

foreach( $wonder_res as $id => $arr )
{
	echo "<br><b>$id.</b><br>";
	foreach( $arr as $item_id => $num )
	{
		$name = f_MValue( "SELECT name FROM items WHERE item_id=$item_id" );
		echo "$item_id: $name<br>";
	}
}

?>
