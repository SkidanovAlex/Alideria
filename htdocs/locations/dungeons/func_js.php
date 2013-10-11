var cells = new Array();
var tm_ref;

function showMap()
{
	query('locations/dungeons/func_dun.php?showMap=1');
}

function showPlayers()
{
	document.getElementById('player_in_dun').innerHTML = 'Загрузка...';
	query('locations/dungeons/func_dun.php?showPlayers=1');
}

function showItems()
{
	document.getElementById('location_items').innerHTML = '';
	query('locations/dungeons/func_dun.php?showItems=1');
}

function showCombats()
{
  
}

function checkForMobs()
{
  query('locations/dungeons/func_dun.php?checkForMobs=1');
}

function refLock()
{
	showPlayers();
	showMap();
	showItems();
	checkForMobs();
}

function getYouStatus()
{
	clearTimeout( tm_ref );
//	document.getElementById('you_doing').innerHTML = 'Загрузка...';
	query('locations/dungeons/func_dun.php?checkStatus=1');
	tm_ref = setTimeout( 'getYouStatus( )', 60000 );
//	document.getElementById('you_doing').innerHTML = 'Вы стоите на месте';
}

function mvTo(mv, a)
{
	if (a == 0)
		query('locations/dungeons/func_dun.php?move='+mv);
	else
	{
		clearTimeout( tm_ref );
		document.getElementById('you_doing').innerHTML = NewTimer( mv, 'Вы идете на клетку "'+a+'"<br>Осталось: <b>', '</b>', 0, 'getYouStatus()' );
	}
}

function movingTo(a)
{
	if (document.getElementById('you_doing').innerHTML != 'Вы стоите на месте' ) return 1;
	if (a!=1 && a!=3 && a!=7 && a!=9 && a!=5 )
	{
//		alert(a);
		mvTo(a, 0);
		//document.getElementById('you_doing').innerHTML = NewTimer( 10, 'Вы идете на клетку "'+cells[a]+'"<br>Осталось: <b>', '</b>', 0, 'getYouStatus()' );
	}
	if (a==5)
		getYouStatus();
	return 0;
}

function oneCell(cell_num, cell_name, cell_img, emp)
{
	var r = "";
	
	if (emp)
	{
		r = "<table cellspacing=0 cellpadding=0 border=0 height=90 width=90><tr><td>";
		document.getElementById('cell_'+cell_num).style.cursor = '';
		r += rFUct()+'&nbsp;'+rFL();
		delete cells[cell_num];
	}
	else
	{
		//r += rFLUcf()+'&nbsp;'+rFLL();
		r = "<table background='/images/dungeons/"+cell_img+"' cellspacing=0 cellpadding=0 border=0 height=90 width=90><tr><td>";
		r += '<div id=cell_in_'+cell_num+'>&nbsp;</div>';
		document.getElementById('cell_'+cell_num).style.cursor = 'pointer';
		cells[cell_num] = cell_name;
	}


	r += "</td></tr></table>";
	
	document.getElementById('cell_'+cell_num).innerHTML = r;
}

function setEvns()
{
	for (var ic=2;ic<=8;ic+=2)
	{
		document.getElementById( 'cell_'+ic ).onmousemove = showingTooltip;
		document.getElementById( 'cell_'+ic ).onmouseout = hideTooltip;
	}
	document.getElementById( 'cell_5' ).onmousemove = showingTooltip;
	document.getElementById( 'cell_5' ).onmouseout = hideTooltip;
}

function showingTooltip(e)
{
	if( this.id.substr( 0, 5 ) == 'cell_' )
	{
		var id = this.id.substr(5);
		if (cells[id])
			showTooltipW(e, cells[id], 150);
	}
}