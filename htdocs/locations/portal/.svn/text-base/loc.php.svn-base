<?

include_once( 'items.php' );
include_once( 'locations/portal/func.php' );
include_js( 'js/skin2.js' );
include_js( 'js/timer2.js' );

if( !isset( $mid_php ) ) die( );

?>

<table><tr><td valign='top'><table><tr><td><script>FLUl();</script><div id='pt_map'>&nbsp;</div><script>FLL();</script><br><img width='11' height='11' id='pt_follow_map' src='images/e_check.gif' onclick='ptFollowMapClick()'> Двигать карту за персонажем</td></tr></table></td><td valign='top'><div id='arrows'>&nbsp;</div><br><div style='text-align:center;' id='pt_keys'>&nbsp;</div><div id='pt_monsters'>&nbsp;</div></td><td valign='top'><b>Миникарта:</b><script>FLUl();</script><div id='pt_minimap'>&nbsp;</div><script>FLL();</script></td></tr></table>

<script>

var portal = {
	renderer: {
		rows: 12,
		cols: 18,
		renderMinimap: function() {
        	var table = document.createElement( 'div' );

			table.onclick = function( e )
			{
				var x, y;
				if( e )
				{
					var pos = getAp( table );
					x = Math.floor( ( e.pageX - pos.x ) / 2 );
					y = Math.floor( ( e.pageY - pos.y ) / 2 );
				}
				else
				{
					x = Math.floor( window.event.x / 2 );
					y = Math.floor( window.event.y / 2 );
				}

				portal.left = x - Math.floor(portal.renderer.cols / 2);
				portal.top = y - Math.floor(portal.renderer.rows / 2);
            	portal.left = Math.max( portal.left, 0 );
            	portal.top = Math.max( portal.top, 0 );
            	portal.left = Math.min( portal.left, portal.width - portal.renderer.cols );
            	portal.top = Math.min( portal.top, portal.height - portal.renderer.rows );
            	pt_map.innerHTML = '';
            	pt_map.appendChild( portal.renderer.render() );
            	pt_minimap.innerHTML = '';
            	pt_minimap.appendChild( portal.renderer.renderMinimap() );
			};
        	table.style.position = "relative";
        	table.style.left = "0px";
        	table.style.top = "0px";
        	table.style.width = (portal.width*2)+"px";
        	table.style.height = (portal.height*2)+"px";
        	table.style.overflow = "hidden";
        	for( var i = 0; i < portal.height; ++ i )
        	{
        		var lastClr = "";
        		var lastElem = null;
        		var lastWidth = 0;
        		for( var j = 0; j < portal.width; ++ j)
        		{
        			var vis = false;
  					if( j >= portal.left && j < portal.left + portal.renderer.cols && i >= portal.top && i < portal.top + portal.renderer.rows ) vis = true;
        			var w = 0;
        			w = portal.walls[i][j];
        			if( w == -1 )
        			{
        				if( vis ) clr = '#606060';
        				else clr = '#444444';
        			}
        			else
        			{
        				if( vis ) clr = '#202020';
        				else clr = 'black';
        			}    			
        			if( i == portal.mey && j == portal.mex )
        			{
        				clr = 'lime';
        			}
        			
        			if( clr == lastClr )
        			{
        				lastWidth += 2;
        				lastElem.style.width = lastWidth + "px";
        				continue;
        			}
        			
        			var col = document.createElement( 'span' );

       				lastClr = clr;
       				lastElem = col;
       				lastWidth = 2;
        			
        			col.innerHTML = '';

        			col.style.width = '2px';
        			col.style.height = '2px';
        			col.style.position = "absolute";
        			col.style.left = ( j * 2 ) + "px";
        			col.style.top = ( i * 2 ) + "px";
        			
       				col.style.backgroundColor = clr;
/*        			if( i == portal.top && j >= portal.left && j < portal.left + portal.renderer.cols ) col.style.borderTop = '1px solid white';
        			if( i == portal.top + portal.renderer.rows - 1 &&  j >= portal.left && j < portal.left + portal.renderer.cols ) col.style.borderBottom = '1px solid white';
        			if( j == portal.left && i >= portal.top && i < portal.top + portal.renderer.rows ) col.style.borderLeft = '1px solid white';
        			if( j == portal.left + portal.renderer.cols - 1 && i >= portal.top && i < portal.top + portal.renderer.rows ) col.style.borderRight = '1px solid white';
  */
					
/*        			col.onclick = function()
        			{
        				var x = j; var y = i;
        				return function() {
        				portal.left = x - Math.floor(portal.renderer.cols / 2);
        				portal.top = y - Math.floor(portal.renderer.rows / 2);
                    	portal.left = Math.max( portal.left, 0 );
                    	portal.top = Math.max( portal.top, 0 );
                    	portal.left = Math.min( portal.left, portal.width - portal.renderer.cols );
                    	portal.top = Math.min( portal.top, portal.height - portal.renderer.rows );
                    	pt_map.innerHTML = '';
                    	pt_map.appendChild( portal.renderer.render() );
                    	pt_minimap.innerHTML = '';
                    	pt_minimap.appendChild( portal.renderer.renderMinimap() );
                    	}
        			}( );*/
        			table.appendChild( col );
        		}
        	}

        	return table;
		},
		render: function() {
        	var table = document.createElement( 'table' );
        	var tbody = document.createElement( 'tbody' );
        	table.cellSpacing = 0;
        	table.cellPadding = 0;
        	for( var i = 0; i < portal.renderer.rows; ++ i )
        	{
        		var row = document.createElement( 'tr' );
        		for( var j = 0; j < portal.renderer.cols; ++ j)
        		{
        			var col = document.createElement( 'td' );
        			col.style.width = '20px';
        			col.style.height = '20px';
        			var w = 0;
        			if( i + portal.top >= 0 && i + portal.top < portal.height && j + portal.left >= 0 && j + portal.left < portal.width ) w = portal.walls[i + portal.top][j + portal.left];
        			if( w == -1 )
        			{
        				col.style.backgroundColor = '#444444';
        				col.style.border = '1px solid #444444';
        			}
        			else
        			{
        				col.style.backgroundColor = 'black';
        				col.style.borderTop = portal.renderer.getBorderColor(w & 7);
        				w >>= 3;
        				col.style.borderLeft = portal.renderer.getBorderColor(w & 7);
        				w >>= 3;
        				col.style.borderBottom = portal.renderer.getBorderColor(w & 7);
        				w >>= 3;
        				col.style.borderRight = portal.renderer.getBorderColor(w & 7);
        				w >>= 3;
        			}    			
        			col.innerHTML = '&nbsp;';
        			if( i + portal.top == portal.mey && j + portal.left == portal.mex )
        			{
        				col.innerHTML = '<center><font color=white>x</font></center>';
        			}
        			row.appendChild( col );
        		}
        		tbody.appendChild( row );
        	}
        	table.appendChild( tbody );
        	return table;
        },
        getBorderColor: function(a) {
        	if (a == 0) return '1px solid black';
        	if (a == 1) return '1px solid white';
        	if (a == 2) return '1px solid red';
        	if (a == 3) return '1px solid blue';
        	if (a == 4) return '1px solid lime';
        	if (a == 5) return '1px solid #606060';
        }
	},
	left: 0,
	top: 0,
	mex: 13,
	mey: 6,
	width: 0,
	height: 0,
	walls: [],
	r: function(a) { portal.walls = a; portal.height = a.length; portal.width = a[0].length; }
};

var wallImage = function( wallType, dir )
{
	if( wallType == 1 ) return "empty.gif";
	if( wallType == 0 )
	{
		if( dir == 0 ) return "but_2.png";
		if( dir == 1 ) return "but_3.png";
		if( dir == 2 ) return "but_1.png";
		return "but_4.png";
	}
	if( wallType == 2 ) return "misc/d1.gif";
	if( wallType == 3 ) return "misc/d2.gif";
	if( wallType == 4 ) return "misc/d3.gif";
	if( wallType == 5 ) return "misc/opend1.gif";
	if( wallType == 6 ) return "misc/opend2.gif";
	if( wallType == 7 ) return "misc/opend3.gif";
}

var wallImageLight = function( wallType, dir )
{
	if( wallType == 1 ) return "empty.gif";
	if( wallType == 0 )
	{
		if( dir == 0 ) return "but_l2.png";
		if( dir == 1 ) return "but_l3.png";
		if( dir == 2 ) return "but_l1.png";
		return "but_l4.png";
	}
	if( wallType == 2 ) return "misc/d1l.gif";
	if( wallType == 3 ) return "misc/d2l.gif";
	if( wallType == 4 ) return "misc/d3l.gif";
	if( wallType == 5 ) return "misc/openl1.gif";
	if( wallType == 6 ) return "misc/openl2.gif";
	if( wallType == 7 ) return "misc/openl3.gif";
}

var getWallHtml = function( wallType, dir )
{
	if( wallType == 1 ) return "<img width='28' height='28' src='empty.gif'>";
	else
	{
		return "<img onclick='__(1,"+dir+")' width='28' height='28' style='cursor:pointer;' onmouseout='this.src=\"images/" + wallImage( wallType, dir ) + "\"' onmouseover='this.src=\"images/" + wallImageLight( wallType, dir ) + "\"' src=\"images/" + wallImage( wallType, dir ) + "\">";
	}
}

var showArrows = function( mask ) {
	var types = [[1,1,1],[1,1,1],[1,1,1]];
	var dirs = [[-1,0,-1],[1,-1,3],[-1,2,-1]];
	types[0][1] = mask & 7; mask >>= 3;
	types[1][0] = mask & 7; mask >>= 3;
	types[2][1] = mask & 7; mask >>= 3;
	types[1][2] = mask & 7; mask >>= 3;
	
	var table = document.createElement( 'table' );
	var tbody = document.createElement( 'tbody' );
	for( var i = 0; i < 3; ++ i )
	{
		var row = document.createElement( 'tr' );
		for( var j = 0; j < 3; ++ j )
		{
			var cell = document.createElement( 'td' );
			cell.innerHTML = rFLUl() + getWallHtml( types[i][j], dirs[i][j] ) + rFLL();
			row.appendChild( cell );
		}
		
		tbody.appendChild( row );
	}
	table.appendChild( tbody );
	document.getElementById( 'arrows' ).innerHTML = '';
	document.getElementById( 'arrows' ).appendChild( table );
}

var portalState = [];
for( var i = 0; i < 50; ++ i )
{
	portalState[i] = [];
	for( var j = 0; j < 50; ++ j )
		portalState[i][j] = -1;
}

portal.r( portalState );

function v(x,y,m) {
	portal.walls[y][x] = m;
}

var followMap = true;
function ptFollowMapClick()
{
	followMap = !followMap;
	if( followMap )
	{
		UpdateLeftAndTop();
    	pt_map.innerHTML = '';
    	pt_map.appendChild( portal.renderer.render() );
    	pt_minimap.innerHTML = '';
    	pt_minimap.appendChild( portal.renderer.renderMinimap() );
	}
	if( followMap ) _( 'pt_follow_map' ).src = 'images/e_check.gif';
	else _( 'pt_follow_map' ).src = 'images/e_none.gif';
}

var monsters = [];

function cm() { monsters = []; }
function am(name,id) { monsters.push({'name':name,'id':id}); }

function UpdateLeftAndTop()
{
	x = portal.mex;
	y = portal.mey;
	portal.left = x - Math.floor( portal.renderer.cols / 2 );
	portal.top = y - Math.floor( portal.renderer.rows / 2 );
	portal.left = Math.max( portal.left, 0 );
	portal.top = Math.max( portal.top, 0 );
	portal.left = Math.min( portal.left, portal.width - portal.renderer.cols );
	portal.top = Math.min( portal.top, portal.height - portal.renderer.rows );
}
		
function refr( x, y, mask, keys )
{
	portal.mex = x;
	portal.mey = y;

	if( followMap )	
	{
		UpdateLeftAndTop();
	}
	
	portal.walls[y][x] = mask;
	pt_map.innerHTML = '';
	pt_map.appendChild( portal.renderer.render() );
   	pt_minimap.innerHTML = '';
   	pt_minimap.appendChild( portal.renderer.renderMinimap() );
	showArrows( mask );
	
	var ks = '';
	for( var i = 1; i <= 3; ++ i ) if( keys & ( 1 << i ) )
	{
		clrs = [0,'Красный ', "Синий", "Зеленый"];
		ks += '<img border="0" width="28" height="28" src="images/misc/k' + i + '.gif" title="'+clrs[i]+' ключ">';
	}
	_( 'pt_keys' ).innerHTML = ks;
	
	var ms = '';
	for( var i in monsters )
	{
		monster = monsters[i];
		ms += "<li STYLE='list-style-image: URL(\"images/dots/dot-attack.gif\"); list-style-type: square'> <a href='#' onclick='__(2," + monster.id + ")'>" + monster.name + '</a><br>';
	}
	if( ms != '' ) ms = '<b>Атаковать:</b><br>' + ms;
	_( 'pt_monsters' ).innerHTML = ms;
}

<?

$cell_id = f_MValue( "SELECT cell_id FROM portal_players WHERE player_id={$player->player_id}" );
$z = f_MValue( "SELECT z FROM portal_maze WHERE cell_id={$cell_id}" );
$res = f_MQuery( "SELECT portal_maze.* FROM portal_maze INNER JOIN portal_revealed_cells ON portal_maze.cell_id = portal_revealed_cells.cell_id WHERE player_id={$player->player_id} AND portal_maze.z={$z}" );
while( $arr = f_MFetch( $res ) )
{
	echo "v($arr[x], $arr[y], $arr[walls]);";
}

?>

__( 0, 0 );

</script>
