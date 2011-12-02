<?
	if( $_COOKIE['c_id'] != 286464 )
	{
		die( 'Пошёл нахуй, наркоман!' );	
	}
	
	 
	require_once( 'functions.php' );
	require_once( 'player.php' );
	f_MConnect( );
	
	$f = f_MQuery( 'SELECT * FROM `player_effects` WHERE effect_id = 3' );
	
	$par = array( );
	
	while( $a = f_MFetch( $f ) )
	{
		$Player = new Player( $a[player_id] );
		
		$Player->RemoveEffect( $f[id] );

		if( !isset( $par[$a[player_id]] ) )
		{
			continue;
			//$Player->AddEffect( 3, 0, iconv("utf-8", "CP1251",'Багоюзер'), iconv("utf-8", "CP1251",'Компенсация за шокирующие минуты, проведённые наедине с багом.'), 'smiley.png', '30:1:40:1:50:1.', time( ) + 60 * 60 * 24 * 3 );
			$par[$a[player_id]] = true;
		}
		break;
	}
	$f = f_MQuery( 'DELETE FROM `player_effects` WHERE expires > -1 AND expires < '.time( ) );
	
	$поимени = new Player( 807113 );
	$поимени->RemoveEffect( 30941 );
	
	echo "<h3>Yeah!</h3>";
?>
<div id=moo>&nbsp;</div>

<script>

document.onkeydown = function(e)
{
	e = e || window.event;
	document.getElementById( 'moo' ).innerHTML = e.keyCode;
}

</script>