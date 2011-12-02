<?

include_once( 'prof_exp.php' );

if( !isset( $mid_php ) ) die( );

echo "<b>¬ход в ѕортал</b> - <a href=game.php?order=main>Ќазад</a><br><br>";

$level = getBLevel( 6 );

if( $level < 4 )
{
	echo "<i>ƒл€ того, чтобы спуститьс€ в запорталье, надо иметь портал хот€ бы четвертого уровн€...</i>";
	return;
}

$moo = f_MValue( "SELECT count( player_id ) FROM player_portal_visits WHERE player_id={$player->player_id}" );
if( $moo )
{
	echo "<i>¬ы уже проходили через портал сегодн€. —нова вы сможете спуститьс€ только после полуночи.</i>";
	return;
}

if( $player->regime == 0 && isset( $_GET['enter'] ) )
{
	include_once( 'locations/portal/func.php' );
	if( $player->SetLocation( 5 ) )
	{
		$player->SetDepth( 0 );
		if( portal_swap_items( $player->player_id ) )
		{
			f_MQuery( "INSERT INTO player_portal_visits ( player_id ) VALUES ( {$player->player_id} )" );
			die( '<script>location.href="game.php";</script>' );
		}
		else
		{
			$player->SetLocation( 2 );
			$player->SetDepth( 50 );
			echo "<script>setTimeout( function(){alert( 'ѕрежде чем войти в портал, снимите все вещи' );}, 100 );</script>";
		}
	}
}

echo "<table width=90% cellspacing=0 cellpadding=0><tr><td align=justify>";
echo "«апорталье - совершенно другой мир с другими законами. ¬ запорталье нельз€ попасть с чем-то физически ощутимым из этого мира. »звестно, что когда чародей проходит через портал, он остаетс€ без оружи€, одежды и украшеий. ”мудреные опытом волшебники советуют никогда не пытатьс€ пройти через портал, име€ одетые вещи - процесс их исчезновени€ очень болезненен. ѕоэтому прежде, чем пройти через портал, необходимо все с себ€ сн€ть.  огда вы перейдете на ту сторону портала, все вещи из этого мира исчезнут из вашего инвентар€, но они вернутс€ обратно в тот же момент как вы пройдете через портал обратно. јналогично, все вещи, которые вы найдете за порталом, не получитс€ пронести в этот мир, но они будут каждый раз оказыватьс€ у вас в сумке, когда вы будете возвращатьс€ в запорталье.<br><br>";
echo "¬ы можете войти в портал только один раз в сутки. ѕри этом вы можете там провести столько времени, сколько вам хочетс€ - в отличие от подземелий у входа в пещеры, поражение в бою в запорталье не влечет за собой прекращение исследовани€.<br><br>";
echo "<li><a href='javascript:enter_portal()'>¬ойти в портал</a>";
echo "</td></tr></table>";

?>

<script>
function enter_portal()
{
	if( confirm( '¬ойти в портал?' ) )
		location.href='game.php?order=portal&enter=1';
}
</script>
