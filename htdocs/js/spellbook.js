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
	if( a == 109 && !confirm( 'Вы уверены, что хотите переобучиться?' ) ) return;
	if( a == 109 && !confirm( 'Это очень ответственный шаг, Вы точно уверены, что хотите переобучиться?' ) ) return;
	if( a == 109 && !confirm( 'Вы не сможете переобучаться 50 дней после этого. Обдумайте свое решение. Вы хотите переобучиться?' ) ) return;
	if( a == 109 && !confirm( 'Утверждается, что если сделать три подтверждения, то люди будут трижды отвечать Да, а потом спрашивать, почему вы не сделали четыре подтверждения. Поэтому мы сделали четыре подтверждения. Вы хотите переобучиться?' ) ) return;
	if( a == 109 && !confirm( 'Вы на 100% уверены, что хотите переобучиться?' ) ) return;

	if( a == 367 && !confirm( 'Вы уверены, что хотите переквалифицироваться?' ) ) return;
	if( a == 367 && !confirm( 'Это очень ответственный шаг, Вы точно уверены, что хотите переквалифицироваться?' ) ) return;
	if( a == 367 && !confirm( 'Вы не сможете переквалифицироваться 100 дней после этого. Обдумайте свое решение. Вы хотите переквалифицироваться?' ) ) return;
	if( a == 367 && !confirm( 'Утверждается, что если сделать три подтверждения, то люди будут трижды отвечать Да, а потом спрашивать, почему вы не сделали четыре подтверждения. Поэтому мы сделали четыре подтверждения. Вы хотите переквалифицироваться?' ) ) return;
	if( a == 367 && !confirm( 'Вы на 100% уверены, что хотите переквалифицироваться?' ) ) return;

	query("spellbook_ref.php?cast=" + a,'');
}
