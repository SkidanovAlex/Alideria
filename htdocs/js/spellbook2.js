var sb_act = 2;
var sb_page = 0;

function pageBack( )
{
	parent.spellbook_ref.location.href="spellbook_ref.php?action=" + sb_act + "&page=" + (sb_page - 1);
}

function pageNext( )
{
	parent.spellbook_ref.location.href="spellbook_ref.php?action=" + sb_act + "&page=" + (sb_page + 1);
}

function pageNeutral( )
{
	parent.spellbook_ref.location.href="spellbook_ref.php?action=10";
}

function pageAll( )
{
	parent.spellbook_ref.location.href="spellbook_ref.php?action=2";
}

function pageWater( )
{
	parent.spellbook_ref.location.href="spellbook_ref.php?action=3";
}

function pageFire( )
{
	parent.spellbook_ref.location.href="spellbook_ref.php?action=5";
}

function pageNature( )
{
	parent.spellbook_ref.location.href="spellbook_ref.php?action=4";
}

function cast_spell( a )
{
	query("spellbook_ref.php?cast=" + a,'');
}
