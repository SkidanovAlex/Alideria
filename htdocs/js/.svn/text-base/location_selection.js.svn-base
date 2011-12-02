function SelShow(e,selName)
{
	if (selName && selName != '')
	{
		var sel = document.getElementById(selName);

		if(sel.style.visibility == 'hidden')
		{
			sel.style.left = selectArray[selName][0];
			sel.style.top = selectArray[selName][1];

			sel.style.visibility = 'visible';
		}

		if( e && e != -1 ) showTooltip( (e)?e:0, "<b>" + selectArray[selName][5] + "</b>" );
	}
}

function SelHide(selName)
{
	if (selName && selName != '')
	{
		document.getElementById(selName).style.visibility = 'hidden';
		hideTooltip( );
	}
}

function LocClick(locName)
{
	location.href = selectArray[locName][6];
}

function SetSel(selectArray)
{
	for (var name in selectArray)
 	{
		var sel = selectArray[name];
		var filename = sel[2];
		var w = sel[3];
		var h = sel[4];

		document.writeln('<div id = "' + name + '" style="position:absolute; z-index:1; visibility:hidden; left: 0px; top: 0px;"><img src="'+ filename + '" width="' + w + '" height="' + h + '" border="0" alt=""></div>');
 	}
}

function SetAnim()
{
	for (var name in animArray)
 	{
		var sel = animArray[name];
		var filename = sel[2];
		var x = sel[0];
		var y = sel[1];
		var w = sel[3];
		var h = sel[4];

		document.write ('<div id = "' + name + '" style="position:absolute; z-index:2; left: '+x+'; top: '+y+'"><img src="'+ filename + '" width="' + w + '" height="' + h + '" border="0" alt=""></div>');
 	}

	UpdatePositions(animArray);
}

function UpdatePositions()
{
	for (var name in animArray)
 	{
		var sel = document.getElementById(name);
		var x = animArray[name][0];
		var y = animArray[name][1];

		sel.style.left = x + 'px';// + pos.x;
		sel.style.top = y + 'px';// + pos.y;
 	}
}

SetSel(selectArray);
