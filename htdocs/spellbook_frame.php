<body background=images/bg.gif>

<script src='functions.js'></script>
<script src='js/spellbook3.js'></script>
<script src='js/tooltips.php'></script>
<script src='js/timer.js'></script>
<script src='js/ajax.js'></script>

<table align='center' width='700' height='400' cellpadding='0' cellspacing='0' border='0'>
<tr>
<td id='SpellbookImage'>
<img src='images/spellbook/bg.jpg' width='700' height='400' usemap='#spellbook' border='0'>
</td>
</tr>
</table>

<script>
function spells_style( q, z )
{
	for( i = q * 4; i < q * 4 + 4; ++ i )
		document.getElementById( 'spell0' + i ).style.display = z;
}
</script>

<map name="spellbook">
<area shape="rect" coords="35,322,83,377" href="javascript:pageBack()" alt="Перевернуть страницу" title="Перевернуть страницу">
<area shape="rect" coords="605,322,652,377" href="javascript:pageNext()" alt="Перевернуть страницу" title="Перевернуть страницу">
<area shape="rect" coords="664,233,700,262" href="javascript:pageAll()" alt="Все заклинания" title="Все Заклинания">
<area shape="rect" coords="664,138,700,167" href="javascript:pageWater()" alt="Магия Воды" title="Магия Воды">
<area shape="rect" coords="664,202,700,230" href="javascript:pageFire()" alt="Магия Огня" title="Магия Огня">
<area shape="rect" coords="664,170,700,198" href="javascript:pageNature()" alt="Магия Природы" title="Магия Природы">
<area shape="rect" coords="515,373,555,399" href="javascript:pageNeutral()" alt="Нейтральная Магия" title="Нейтральная Магия">
</map>

<div id="spell00" style="position:absolute; z-index:1; left: 55;  top: 56" ><img src='images/spells/none.gif' width=125 height=125 border=0></div>
<div id="spell01" style="position:absolute; z-index:1; left: 194; top: 56" ><img src='images/spells/none.gif' width=125 height=125 border=0></div>
<div id="spell02" style="position:absolute; z-index:1; left: 55;  top: 194"><img src='images/spells/none.gif' width=125 height=125 border=0></div>
<div id="spell03" style="position:absolute; z-index:1; left: 194; top: 194"><img src='images/spells/none.gif' width=125 height=125 border=0></div>

<div id="spell04" style="position:absolute; z-index:1; left: 367; top: 56" ><img src='images/spells/none.gif' width=125 height=125 border=0></div>
<div id="spell05" style="position:absolute; z-index:1; left: 505; top: 56" ><img src='images/spells/none.gif' width=125 height=125 border=0></div>
<div id="spell06" style="position:absolute; z-index:1; left: 367; top: 194"><img src='images/spells/none.gif' width=125 height=125 border=0></div>
<div id="spell07" style="position:absolute; z-index:1; left: 505; top: 194"><img src='images/spells/none.gif' width=125 height=125 border=0></div>

<div id="hint0" style="position:absolute; z-index:2; left: 53; top: 34; width: 280; height: 300; display: none">&nbsp;</div>
<div id="hint1" style="position:absolute; z-index:2; left: 359; top: 34; width: 280; height: 300; display: none">&nbsp;</div>

<script>

pageAll();

</script>

