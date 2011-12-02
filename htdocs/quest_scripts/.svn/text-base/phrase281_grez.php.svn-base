<?

if( !$mid_php ) die( );

echo "<div style='width:691px; height:334px; position:absolute; background-image:url(\"images/nevesom/astral_bg.jpg\");'>";

echo "<div id='Div0' style=\"z-index: 1; width:310px; height:170px; position:absolute; left:22px; top:27px; background-image:url('images/grez/more_text_bg.png'); padding:10px;background-repeat:no-repeat;font-size:10px;\">";
echo "";
echo "</div>";

echo "<div id='Div1' style=\"z-index: 2; width:310px; height:170px; position:absolute; left:302px; top:10px; background-image:url('images/grez/more_text_bg.png'); padding:10px;background-repeat:no-repeat;font-size:10px;\">";
echo "";
echo "</div>";

echo "<div id='Div2' style=\"z-index: 3; width:310px; height:170px; position:absolute; left:322px; top:120px; background-image:url('images/grez/more_text_bg.png'); padding:10px;background-repeat:no-repeat;font-size:10px;\">";
echo "";
echo "</div>";

echo "<div style=\"width:145px; height:99px; position:absolute; left:52px; top:205px; background-image:url('images/nevesom/dragon_text_bg.png'); padding:5px 10px 5px 91px;background-repeat:no-repeat;\">";
echo "<table cellpadding=0 cellspacing=0 border=0 style='height:100%;'><tr valign=middle><td style='font-size:10px;'>";
echo "<div id='DivDragon' >Это мир грез. Чтобы выбраться отсюда, нужно ответить правильно на три вопроса. Но время, время ограничено.Тебе нужно спешить.</div>";
echo "<div id='DivTimer'></div>";
echo "</table></div>";

echo "</div>";
 ?>
<script>

function moo( cell )
{
	query( 'quest_scripts/phrase280_ajax.php', '' + cell );
}

var lastTO = -1;

function moove( cnt )
{
    var ncnt = cnt - 1;
    var text = "";
    if ( ncnt > 0 )
    	text = 'Осталось ' + ncnt + ' секунд';
    if ( ncnt == 1 )
    	text = text + 'а';
    if ( ncnt == 0 )
    	text = 'Время вышло';
    _('DivTimer').innerHTML = text;
	if ( ncnt > 0 )
		lastTO = setTimeout( "moove( " + ncnt + " );", 1000 );
}

function out( id, s, dragon )
{
	var i;
	for ( i = 0; i < 3; ++ i )
	{
		_( 'Div' + i ).innerHTML = '';
		_( 'Div' + i ).style.zIndex = '' + ( i + 1 );
	}
	_( 'Div' + id ).innerHTML = s;
	_( 'Div' + id ).style.zIndex = '5';
	if ( dragon != "" )
	{
		_( 'DivDragon' ).innerHTML = dragon;
	}
	clearTimeout( lastTO );
	moove( 10 );
}

<?
	echo "query( 'quest_scripts/phrase281_ajax.php', '-2' );";
?>

</script>
