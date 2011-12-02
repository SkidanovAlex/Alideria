
function expand_pair( id )
{
	function moo( )
	{
		collapse_pair( id );
	}
	document.getElementById( 'pair_div' + id ).style.display = '';
	document.getElementById( 'pair_img' + id ).src = 'images/e_minus.gif';
	document.getElementById( 'pair_img' + id ).onclick = moo;
}

function upload_pair( id )
{
	query( "combat_ajax_load_pair.php", "" + id );
	expand_pair( id );
}

function collapse_pair( id )
{
	function moo( )
	{
		expand_pair( id );
	}
	document.getElementById( 'pair_div' + id ).style.display = 'none';
	document.getElementById( 'pair_img' + id ).src = 'images/e_plus.gif';
	document.getElementById( 'pair_img' + id ).onclick = moo;
}

