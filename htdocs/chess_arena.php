<?

if( !$mid_php ) die( );

?>

<center>
<table border=1><tr><td valign=top>
<a href=chess_arena_ref.php target=ref>Заявки на игру</a><br>
<a href=chess_arena_current.php target=ref>Текущие игры</a><br>
<a href=chess_arena_past.php target=ref>Завершенные игры</a><br>
</td>
<td valign=top width=500>
<div id=chs name=chs>&nbsp;</div>
</td>
</tr></table>

<script>

	var tmm;
	function moo_ref( )
	{
		clearTimeout( tmm );
		tmm = setTimeout( 'ref.location.href="chess_arena_ref.php"', 10000 );
	}
	function moo_clear( )
	{
		clearTimeout( tmm );
	}

</script>

<iframe width=0 height=0 id=ref name=ref src=chess_arena_ref.php></iframe>

