<?php
?><script>begin_help( 'Примерочная' );</script>

<?

include_once( 'arrays.php' );

include_js( 'functions.js' );
include_js( 'js/clans.php' );
include_js( 'js/ii.js' );
include_js( 'js/cc.js' );
include_js( 'js/skin2.js' );
include_js( 'js/ajax.js' );

if( isset( $_COOKIE['c_id'] ) )
{
	$id = (int)$_COOKIE['c_id'];

    $res = f_MQuery( "SELECT login, level FROM characters WHERE player_id=$id" );
    $arr = f_MFetch( $res );
    if( !$arr )
    {
    	$id = 0;
    	$login = 'Имя';
    	$lvl = 1;
    }
    else
    {
    	$login = $arr[0];
    	$lvl = $arr[1];
    }
}
else
{
	$id = 0;
	$login = 'Имя';
	$lvl = 1;
}

$hp = 50 + $lvl * 50;

echo "<table><tr><td height=1>&nbsp;</td><td valign=top rowspan=2>";
echo "<div align=center id=cchere>\n";
echo "</div>\n\n";

?>
		<br><div style='width:248px;height:321px;background:url(images/ibg.jpg);position:relative;top:0px;left:0px;' id=moo name=moo width=248 height=200>


			<img style='position: absolute;' width=100 height=225 src=images/avatars/f0o.png id=avatar name=avatar>

			<div style='position: absolute;' width=50 height=50 id=item1 name=item1>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item2 name=item2>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item3 name=item3>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item4 name=item4>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=item5 name=item5>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=item6 name=item6>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=item7 name=item7>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=item8 name=item8>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=item9 name=item9>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item10 name=item10>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item11 name=item11>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item12 name=item12>&nbsp;</div>

<?
echo "<div onclick=show_pots(1) style='position: absolute;top:285px;left:30px;' width=25 height=25 id=pot1 name=pot1>";
	echo "<img src='images/items/bg/bg25pot.gif'>";
echo "</div>";
echo "<div id=pots1 name=pots1 style='position: absolute;top:320px;left:0px;display: none;'>";
	echo "<img src='images/rect/panel.jpg'>";
	echo "<div style='position: absolute;top:3;left:70;'><b>Зелья</b></div>";
	echo "<img onclick='hide_pots(1);' src='images/e_close.gif' style='position: absolute;top:4;right:4' title='Закрыть'>";
	echo "<div style='position: absolute;' width=50 height=50 id=item13 name=item13>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item14 name=item14>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item15 name=item15>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item16 name=item16>&nbsp;</div>";
echo "</div>";
echo "<div onclick=show_pots(2) style='position: absolute;top:285px;left:55px;' width=25 height=25 id=pot2 name=pot2>";
	echo "<img src='images/items/pot_sq/ten_talismana.png'>";
echo "</div>";
echo "<div id=pots2 name=pots2 style='position: absolute;top:320px;left:0px;display: none;'>";
	echo "<img src='images/rect/panel.jpg'>";
	echo "<div style='position: absolute;top:3;left:70;'><b>Талисманы</b></div>";
	echo "<img onclick='hide_pots(2);' src='images/e_close.gif' style='position: absolute;top:4;right:4' title='Закрыть'>";
	echo "<div style='position: absolute;' width=50 height=50 id=item17 name=item17>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item18 name=item18>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item19 name=item19>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item20 name=item20>&nbsp;</div>";
echo "</div>";
echo "<div onclick=show_pots(3) style='position: absolute;top:285px;left:80px;' width=25 height=25 id=pot3 name=pot3>";
	echo "<img src='images/items/pot_sq/ten_medaliona.png'>";
echo "</div>";
echo "<div id=pots3 name=pots3 style='position: absolute;top:320px;left:0px;display: none;'>";
	echo "<img src='images/rect/panel.jpg'>";
	echo "<div style='position: absolute;top:3;left:70;'><b>Медальоны</b></div>";
	echo "<img onclick='hide_pots(3);' src='images/e_close.gif' style='position: absolute;top:4;right:4' title='Закрыть'>";
	echo "<div style='position: absolute;' width=50 height=50 id=item21 name=item21>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item22 name=item22>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item23 name=item23>&nbsp;</div>";
	echo "<div style='position: absolute;' width=50 height=50 id=item24 name=item24>&nbsp;</div>";
echo "</div>";

if ($player->player_id == 1)
{
	echo "<div style='position: absolute;display: none;' width=50 height=50 id=item25 name=item25>&nbsp;</div>";
	echo "<div id=pot4 style='display:none;'>&nbsp;</div>";
}
else
{
	echo "<div onclick=show_pots(4) style='position: absolute;top:285px;left:105px;' width=25 height=25 id=pot4 name=pot4>";
//		echo "<img src='images/items/pot_sq/balsam_ten.png'>";
		echo "<img src='images/items/bg/bg25pot.gif'>";
	echo "</div>";
	echo "<div id=pots4 name=pots4 style='position: absolute;top:320px;left:0px;display: none;'>";
		echo "<img src='images/rect/panel.jpg'>";
		echo "<div style='position: absolute;top:3;left:70;'><b>Бальзамы</b></div>";
		echo "<img onclick='hide_pots(4);' src='images/e_close.gif' style='position: absolute;top:4;right:4' title='Закрыть'>";
		echo "<div style='position: absolute;' width=50 height=50 id=item25 name=item25>&nbsp;</div>";
	echo "</div>";
}
?>



			<div style='position: absolute;' width=25 height=25 id=csp0 name=csp0>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=csp1 name=csp1>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=csp2 name=csp2>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=csp3 name=csp3>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=csp4 name=csp4>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=csp5 name=csp5>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=csp6 name=csp6>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=csp7 name=csp7>&nbsp;</div>

			<div style='position: absolute;' width=50 height=50 id=item_drag name=item_drag>&nbsp;</div>
		</div>

	</td><td valign=top>
		<div id=prim>
		</div>
	</td><td valign=top align=center>
    	<b>Показать вещи:</b><br>
    	<select id=itype class=m_btn>
    		<?

    		foreach( $item_types as $key=>$value ) if( $key > 0 && $key < 20 || $key == 30 || $key == 35 )
    			echo "<option value=$key>$value";

    		?>
    	</select><br>
    	<b>Уровень:</b><br>
    	<select id=ilvl class=m_btn>
    		<?
    		for( $i = 1; $i <= 25; ++ $i )
    			echo "<option value=$i>$i";
    		?>
    	</select><br>
    	<button onclick='show_items();' class=s_btn>Показать</button>
	</td>

	<td rowspan=2 valign=top>

	<div id=seco>&nbsp;
	</div>

	</tr>

	<tr><td height=300>&nbsp;</td><td colspan=2 width=400 valign=top>
	<div id=items>
	&nbsp;
	</div>
	</td></tr>

</table>

		<?
		
		include_js( 'js/tooltips.php' );
		include_js( 'js/char_inv.php' );
		include_js( 'js/char_inv3.php' );
		

echo "<script>\n";

echo "var id = $id;\nvar login = '$login';\nvar lvl = $lvl;\nvar p1 = lvl * 3; var p2 = lvl * 3;\n\n";
echo "var attrs = new Array( );\n";
echo "for( var i = 0; i < 300; ++ i ) attrs[i] = 0;\nattrs[1] = 100 * lvl; attrs[101] = attrs[1];\n\n";
echo "var rattrs = new Array( );\n";
echo "for( var i = 0; i < 300; ++ i ) rattrs[i] = 0;\nrattrs[1] = 100 * lvl; rattrs[101] = attrs[1];\n\n";

echo "set_avatar('f0o.jpg');\n\n";

?>

var stat_ids = [13,15,16,222,14,224,223];
var stat_imgs = ['luck','o','c','r','speed','e_ic4','v'];
var stat_names = ['Удача',"Отдача","Критический Удар","Регенерация","Скорость","Восстановление Жизни","Выносливость"];

function refr( )
{
	_( 'cchere' ).innerHTML = cc( id, login, lvl, attrs[1], attrs[101], attrs[30], attrs[130], attrs[30] + attrs[33], attrs[131], attrs[132], attrs[40], attrs[140], attrs[40] + attrs[42], attrs[141], attrs[142], attrs[50], attrs[150], attrs[50] + attrs[51], attrs[151], attrs[152], 0 );
	show_char( document.getElementById( 'moo' ) );
	char_set_events_noinv( );

	var st = rFUlt() + '<table cellspacing=0 cellpadding=0 border=0>';
	for( i in stat_ids )
	{
		st += '<tr><td><img src=images/icons/attributes/' + stat_imgs[i] + '.gif width=20 height=20></td><td>&nbsp;<b>' + stat_names[i] + ':</b></td><td>&nbsp;<b>' + attrs[stat_ids[i]] + '</b></td></tr>';
	}
	st += '</table>' + rFL();
	_( 'prim' ).innerHTML = st;

	st = rFUlt() + '<table cellspacing=0 cellpadding=0 border=0>';
	st += '<tr><td><img width=20 height=20 src=images/icons/attributes/bo.gif></td><td><b>Уровень:</b></td><td align=right>&nbsp;<b>' + lvl + '</b></td><td>&nbsp;<img width=11 height=11 onclick="if(lvl > 1){--lvl;inca(101,-100);p1-=3;p2-=3;refr();}" src=images/e_minus.gif style="cursor:pointer"><img onclick="++lvl;inca(101,100);p1+=3;p2+=3;refr();" width=11 height=11 src=images/e_plus.gif style="cursor:pointer"></td></tr>';

	st += '<tr><td><img width=20 height=20 src=images/icons/attributes/w_ic1.gif></td><td><font color=blue><b>Магия Воды:</b></font></td><td align=right>&nbsp;<b>' + attrs[30] + '</b></td>                  <td>&nbsp;<img width=11 height=11 onclick="decp(30);" src=images/e_minus.gif style="cursor:pointer"><img onclick="incp(30);" width=11 height=11 src=images/e_plus.gif style="cursor:pointer"></td></tr>';

	st += '<tr><td><img width=20 height=20 src=images/icons/attributes/e_ic1.gif></td><td><font color=green><b>Магия Природы:</b></font></td><td align=right>&nbsp;<b>' + attrs[40] + '</b></td>                  <td>&nbsp;<img width=11 height=11 onclick="decp(40);" src=images/e_minus.gif style="cursor:pointer"><img onclick="incp(40);" width=11 height=11 src=images/e_plus.gif style="cursor:pointer"></td></tr>';

	st += '<tr><td><img width=20 height=20 src=images/icons/attributes/f_ic1.gif></td><td><font color=red><b>Магия Огня:</b></font></td><td align=right>&nbsp;<b>' + attrs[50] + '</b></td>                  <td>&nbsp;<img width=11 height=11 onclick="decp(50);" src=images/e_minus.gif style="cursor:pointer"><img onclick="incp(50);" width=11 height=11 src=images/e_plus.gif style="cursor:pointer"></td></tr>';

	st += '<tr><td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;<b>Свободных магий:</b></td><td align=right>&nbsp;<b>' + p2 + '</b></td><td>&nbsp;</td></tr>';

	st += '</table>' + rFL();
	_( 'seco' ).innerHTML = st;

	for( var i = 1; i <= 25; ++ i ) if( ppp[i] )
	{
		document.getElementById( 'item' + (convert_slot( i )+1) ).onclick = make_undo_f( i );
		document.getElementById( 'item' + (convert_slot( i )+1) ).style.cursor = 'pointer';
	}
}

function make_undo_f( id )
{
	function undo_item( )
    {
    	slot = id;
    	eff = ef[id];
    	var num = 0;
    	var moo = false;
    	var att = 0;
    	for( var i = 0; i < eff.length; ++ i )
    	{
    		if( eff.charAt( i ) == ':' || eff.charAt( i ) == '.' )
    		{
    			if( moo ) { inca( att, - num ); att = 0; num = 0; }
    			if( eff.charAt( i ) == '.' ) break;
    			moo = !moo;
    		}
    		else
    		{
    			if( !moo ) att = att * 10 + parseInt( eff.charAt( i ) );
    			else num = num * 10 + parseInt( eff.charAt( i ) );
    		}
    	}

    	ppp[slot] = 0;
    	unwear( id );
    	refr( );

	}
	return undo_item;
}

function incp( id )
{
	if( p2 <= 0 ) alert( "Недостаточно свободных навыков" );
	else
	{
		rattrs[id] ++;
		inca( id, 1 );
		-- p2;
		refr( );
	}
}
function decp( id )
{
	if( rattrs[id] > 0 )
	{
    	++ p2;
    	inca( id, -1 );
    	-- rattrs[id];
    	refr( );
	}
}

function incpp( id )
{
	if( p1 <= 0 ) alert( "Недостаточно свободных навыков" );
	else
	{
		rattrs[id] ++;
		inca( id, 1 );
		-- p1;
		refr( );
	}
}
function decpp( id )
{
	if( rattrs[id] > 0 )
	{
    	++ p1;
		inca( id, -1 );
    	-- rattrs[id];
    	refr( );
	}
}

function show_items( )
{
	var lvl = 1 + _( 'ilvl' ).selectedIndex;
	var type = _( 'itype' ).options[_( 'itype' ).selectedIndex].value;
	query( "help/butik_ref.php?type=" + type + "&lvl=" + lvl, "" );
}

function inca( att, num )
{
	attrs[att] += num;

	<?

	include( 'attrib_relations.php' );
	foreach( $attrib_rels as $a=>$b ) foreach( $b as $c )
		echo "if( att == $a ) inca( $c, num );\n";

	?>
}

var ppp = new Array( );
var ef = new Array( );
function do_item( eff, req, id, name, descr, img, slot )
{
	var ok = false;
	do
	{
		if( !ppp[slot] && slot!=30 && slot!=35 )
		{
			ok = true;
			break;
		}
		if( slot == 1 ) slot = 14;
		else if (slot == 30 ) slot = 17;
		else if (slot == 35 ) slot = 21;
		else if( slot == 13 ) break;
		else if ( slot == 20 ) break;
		else if ( slot == 25 ) break;
		else ++ slot;
	} while( slot == 3 || slot == 5 || slot == 7 || (slot >= 14 && slot <= 24) );

	if( !ok )
	{
		alert( "Все слоты для этой вещи уже заняты" );
		return;
	}

	var num = 0;
	var moo = false;
	var att = 0;
	for( var i = 0; i < eff.length; ++ i )
	{
		if( eff.charAt( i ) == ':' || eff.charAt( i ) == '.' )
		{
			if( moo ) { inca( att, num ); att = 0; num = 0; }
			if( eff.charAt( i ) == '.' ) break;
			moo = !moo;
		}
		else
		{
			if( !moo ) att = att * 10 + parseInt( eff.charAt( i ) );
			else num = num * 10 + parseInt( eff.charAt( i ) );
		}
	}

	ppp[slot] = 1;
	ef[slot] = eff;
	wear( id, name, descr, img, slot );
	refr( );
}

refr( );

<?

echo "</script>\n";

?>

<script>end_help();</script>
