<?

if( !$mid_php )
	die( );
	
//print( "<b> нига заклинаний:</b><br><br>" );

//$player->ShowCards( "cast_spell" );


echo "<table align='center' width='700' height='400' cellpadding='0' cellspacing='0' border='0'>";
echo "<tr>";
echo "<td width='700' height='400'>";
echo "<IFRAME id=sb_main name=sb_main marginwidth='0' marginheight='0' frameborder='0' scrolling='no'  width='700' height='400' border='0' src='spellbook_frame.php' ALLOWTRANSPARENCY='true'></IFRAME>";
echo "</td>";
echo "</tr>";
echo "</table>";

echo "<IFRAME id='spellbook_ref' name='spellbook_ref' marginwidth=0 marginheight=0 frameborder=0 scrolling=no height=0 width=0 border=1></IFRAME>";

?>

<script>

function sbDelSpell( e )
{
	if( this.id.substr( 0, 3 ) == 'csp' )
	{
		id = this.id.substr( 3 );
		query("spellbook_ref.php?del=" + id,'');
	}
}

function char_set_sb_events( ) // вызываетс€ в game.php внизу
{

	for( var i = 0; i < 8; ++ i )
	{
		document.getElementById( 'csp' + i ).style.cursor = 'pointer';
		document.getElementById( 'csp' + i ).onclick = sbDelSpell;
	}
}

</script>