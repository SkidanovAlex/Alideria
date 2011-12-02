function show_rooms( )
{
	if( typeof _( 'forums_lnk' ) == 'undefined' ) return;
	var pos = getAp( _( 'forums_lnk' ) );
	var h = _( 'forums_lnk' ).offsetHeight;
	pos.y += h;
	_( 'navi' ).style.left = pos.x + 'px';
	_( 'navi' ).style.top = pos.y + 'px';
	_( 'navi' ).style.display = '';
	_( 'navi' ).innerHTML = '<i>Загрузка</i>';

	query( 'forum_query.php?ajax=rooms', '' );
}

function show_topics( room )
{
	if( typeof _( 'forums_lnk' ) == 'undefined' ) return;
	var pos = getAp( _( 'topics_lnk' ) );
	var h = _( 'topics_lnk' ).offsetHeight;
	pos.y += h;
	_( 'navi' ).style.left = pos.x + 'px';
	_( 'navi' ).style.top = pos.y + 'px';
	_( 'navi' ).style.display = '';
	_( 'navi' ).innerHTML = '<i>Загрузка</i>';

	query( 'forum_query.php?ajax=' + room, '' );
}

function hide_navi( )
{
	_( 'navi' ).style.display = 'none';
}

var cur_post_loading;
function show_post( e, id )
{
	if( cur_post_loading == id ) { showTooltipW( e, false, 500 ); return; }
	showTooltipW( e, '<i>Загрузка</i>', 500 );
	cur_post_loading = id;
	query( "forum_query.php?ajax=-" + id, '' );
}

function navi_down( e )
{
    if(!e) e = window.event;

    if(e.stopPropagation) e.stopPropagation();
    else e.cancelBubble = true;
    if(e.preventDefault) e.preventDefault();
    else e.returnValue = false;
    return false;
}
