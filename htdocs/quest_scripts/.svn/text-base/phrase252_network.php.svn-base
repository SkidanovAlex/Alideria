<?

if( !$mid_php ) die( );
include_js( 'js/ipng.js' );

?>

<div id=act>
<li><a href=game.php?phrase=591>Отчаяться и уйти</a>
</div>

<div id=field style='position:relative;top:0px;left:0px;'>
<img width=622 height=334 src=images/misc/netf.jpg>
<div style='position:absolute;top:226px;left:8px;'><? echo ipng( 't1', 'i1', 'images/misc/t1.png', 33, 27 ); ?></div>
<div style='position:absolute;top:91px;left:580px;'><? echo ipng( 't2', 'i2', 'images/misc/t2.png', 33, 27 ); ?></div>
<div style='position:absolute;top:199px;left:580px;'><? echo ipng( 't2', 'i2', 'images/misc/t2.png', 33, 27 ); ?></div>
<div style='position:absolute;top:37px;left:40px;' id=fld>&nbsp;</div>

</div>

<script>

function winact()
{
	_( 'act' ).innerHTML = '<li><a href=game.php?phrase=590>Трубы собраны! Вернуться к Ягайле и известить об этом великом событии</a>';
}

function refr( f )
{
	var st = '<table cellspacing=0 cellpadding=0 border=0>';
	var id = 0;
	for( var i = 0; i < 10; ++ i )
	{
		st += '<tr>';
		for( var j = 0; j < 20; ++ j )
		{
			if( f.charAt( id ) == '0' ) st += '<td style="width:27px;height:27px;">&nbsp;';
			else st += '<td id=td' + id  + ' onclick="chg(' + id + ')" style="cursor:pointer;width:27px;height:27px;" background=images/misc/t' + f.charAt( id ) + '.png>&nbsp;';
			++ id;
			st += '</td>';
		}
		st += '</tr>';
	}
	st += '</table>';
	_( 'fld' ).innerHTML = st;
}

function cone( id, c )
{
	_( 'td' + id ).style.background = "url(images/misc/t" + c + ".png)";
}

function chg( id )
{
	query( 'quest_scripts/phrase252_ajax.php?id=' + id, '' );
}

<?

include( 'phrase252_ajax.php' );

?>

</script>