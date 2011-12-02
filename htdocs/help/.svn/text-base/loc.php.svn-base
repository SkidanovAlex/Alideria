<?
if( !$help_php ) die( );
include_js( "js/ii.js" );
include_js( "js/clans.php" );
?>
<script>begin_help( 'Локатор' );</script>
<script>
var ar = new Array();
var counter = 0;
var online = 0;
var offline = 0;
var fighters = 0;
<?
	echo "var orders = '".htmlspecialchars( $_GET['orders'], ENT_QUOTES )."';\n";
	echo "var players = '".htmlspecialchars( $_GET['players'], ENT_QUOTES )."';\n";
	include_once "functions.php";
	include_once "arrays.php";
	f_MConnect( );
	echo "var loc = new Array( );\nvar depth = new Array( );\n";
	for ( $i = 0; $i < count( $loc_names ); $i++ )
		echo "\tloc[$i] = '{$loc_names[$i]}';\n\tdepth[$i] = new Array( );\n";
	$loc_text = f_MQuery("SELECT title,depth,loc FROM loc_texts");
	while ( $res = f_MFetch( $loc_text ) )
		echo "\tdepth[{$res['loc']}][{$res['depth']}] = '{$res['title']}';\n";
	for ( $i = 0; $i <= 20; $i++ )
		echo "\tdepth[0][$i] = 'Глубина $i';\n\tdepth[4][$i] = 'Глубина $i';\n";
	include_once( 'forest_functions.php' );
	foreach ( $forest_names as $n=>$v )
		echo "\tdepth[1][$n] = '$v';\n";
?>

function ge( a ) {
    return document.getElementById( a );
}

function load( url ) {
    ge( "locat" ).innerHTML = "<i>Идёт загрузка...</i>"; 
	req = null;
    if ( window.XMLHttpRequest ) {
        try	{
            req = new XMLHttpRequest( );
        }
		catch(e) { }
    }
    else
        if ( window.ActiveXObject ) {
            try {
                req = new ActiveXObject( 'Msxml2.XMLHTTP' );
			} catch( e ) {
                try {
                    req = new ActiveXObject( 'Microsoft.XMLHTTP' );
                }
				catch(e) { }
            }
        }
        if ( req ) {
            req.onreadystatechange = processReqChange;
            req.open( "GET", url + '&' + Math.random(), true );
            req.send( null );
            reqTimeout = setTimeout( "req.abort();", 15000 );
        }
        else
            ge( "locat" ).innerHTML = "<i>Браузер не поддерживает AJAX</i>";
}

function processReqChange( )
{
    if ( req.readyState == 4 ) {
        clearTimeout( reqTimeout );
        if ( req.status == 200 ) {
        	 clearTimeout( reqTimeout );
             proc( req.responseText );
        }
        else
           	ge( "locat" ).innerHTML = "<i>Повторите попытку</i>";
    }
}

function proc( req )
{
	online = 0;
	offline = 0;
	fighters = 0;
	lines = req.split("#");
	ar = new Array ( );
	for ( counter = 0; lines[ counter + 1 ]; counter++ )
	{
		ar[counter] = lines[counter].split("@");
		if ( ar[counter][0] == 0 ) offline++;
		if ( ar[counter][0] > 0 ) online++;
		if ( ar[counter][0] > 1000) fighters++;
	}
	sort( 0 );
}

vstatus = new Array ( );
vstatus[0] = "Вне игры";
vstatus[1] = "В игре";
vstatus[101] = "В сделке";
vstatus[102] = "В сделке";
vstatus[103] = "Делает вещи";
vstatus[104] = "Добывает ресурсы";
vstatus[106] = "Чинит вещи";
vstatus[108] = "В дозоре";
vstatus[110] = "Общается с NPC";
vstatus[111] = "Играет в Магию";
vstatus[250] = "Восстанавливается у Лекаря";

//alert( vstatus[1] );

function status_text( sid )
{
	if ( typeof vstatus[sid] !== "undefined" )
		return vstatus[sid];
	return "В бою [" + sid + "]";
}

function write_info( )
{
	if ( counter )
	{
		content = "<table><thead><tr><td><img src=\"images/locator/1.gif\" widht=11px height=11px border=0 align=\"absmiddle\"onclick='sort(0);' style='cursor: pointer;' title='Статус'></b></td><td><a href='javascript://' onclick='sort(1);'>[Lvl]</a> <img src='http://alideria.ru/images/clans/849320120.gif' style='position: relative; top: 2px; cursor: pointer;' border='0' height='13' width='18' onclick='sort(4);'> <a href='javascript://' onclick='sort(2);'><font color='#ffffff'>Имя</font></a></td><td><a href='javascript://' onclick='sort(5);'>Место</a> - <a href='javascript://' onclick='sort(6);'>Локация</a></td></tr></thead><tbody>";
		for ( i = 0; i < counter; i++ )
		{
			if ( typeof vstatus[ar[i][0]] === "undefined" && ar[i][0] < 1000 )
				ar[i][0] = 1;
			content += "<tr>";
			content += "<td><img src=\"images/locator/" + ( ( ar[i][0] < 1000 ) ? ar[i][0] : "combat" ) + ".gif\" width=11px height=11px align=absmiddle border=0 title=\"" + status_text(ar[i][0]) + "\" " + ( ( ar[i][0] > 1000 ) ? "style=\"cursor: pointer;\" onclick=\"window.open('combat_log.php?id=" + ar[i][0] + "')\"" : "" ) + ">&nbsp;</td>";
			content += "<td>" + ii( ar[i][1], ar[i][2], ar[i][3], parseInt( ar[i][4] ) ) + "</td>";
			content += "<td><b>" + loc[ar[i][5]] + " - <i>" + depth[ar[i][5]][ar[i][6]] + "</i></b></td>";
			content += "</tr>";
		}
		content += "</tbody></table><br><br>";		
		if ( online ) content += "В игре: " + online + "/" + counter + "<br>";
		if ( offline ) content += "Вне игры: " + offline + "/" + counter + "<br>";
		if ( fighters ) content += "В бою: " + fighters + "/" + counter + "<br>";
		ge( "locat" ).innerHTML = content;
		content = "http://alideria.ru/help.php?id=36100" + ( ( orders ) ? ( "&orders=" + orders ) : "" ) + ( ( players ) ? ( "&players=" + players ) : "" );
		ge( "link" ).value = content;
		ge( "ref" ).style.display = "block";
	}
	else
	{
		ge( "ref" ).style.display = "none";
		ge( "link" ).value = "";
		ge( "locat" ).innerHTML = "";
	}
}

function show( a ) {
	ge( "ref" ).style.display = "none";
	if ( a == 1 ) {
		orders = ge( "orders" ).value;
		if ( !orders ) {
			alert( "Выберите орден из списка" );
			return;
		}
		players = "";
        load( "help/t_loc.php?orders=" + orders );
    } else if ( a == 2 ) {
			orders += ( ( orders ) ? "," : "" ) + ge( "orders" ).value;
			if ( !ge( "orders" ).value ) {
				alert( "Выберите орден из списка" );
				return;
			}			
            load( "help/t_loc.php?orders=" + orders + "&players=" + players );
        } else if ( a == 3 ) {
                players = ge( "players" ).value;
				if ( !players )	{
					alert( "Введите имя персонажа или нескольких персонажей, разделяя их запятыми");
					return;
				}
				ge( "players" ).value = "";
				orders = "";
				load( "help/t_loc.php?players=" + encodeURIComponent( players ) );
            } else if ( a == 4 ) {
					players += ( ( players ) ? "," : "" ) + ge( "players" ).value;
					if ( !ge( "players" ).value) {
						alert( "Введите имя персонажа или нескольких персонажей, разделяя их запятыми" );
						return;
					}	
					ge( "players" ).value = "";
                    load( "help/t_loc.php?orders=" + orders + "&players=" + encodeURIComponent( players ) );
				}
}

function refn( ) {
	load( "help/t_loc.php?orders=" + orders + "&players=" + players );
}

function sort( sid ) {
	tmp = new Array( );
	if( counter <= 0 ) return;
	checked = ar[0][sid] > ar[counter - 1][sid];
	if( sid <= 1 ) checked = parseInt( ar[0][sid] ) > parseInt( ar[counter - 1][sid] );
	if( sid <= 1 ) ar.sort( function(x,y) { return ( ( parseInt( x[sid] ) < parseInt( y[sid] ) ) ^ checked ); } );
	else ar.sort( function(x,y) { return ( ( x[sid] < y[sid] ) ^ checked ); } );
/*	for ( iter = 0; iter < counter; iter++ )
		for ( id = 0; id < counter-1; id++ )
			if ( ar[id][sid] > ar[id+1][sid] == checked )
			{
				tmp = ar[id + 1];
				ar[id + 1] = ar[id];
				ar[id] = tmp;
			}*/
	write_info( );
}
</script>
<table cellpadding=3px cellspacing=0 border=0>
    <tr>
        <td>
            Орден:
        </td>
        <td>
            <select size=1 class="m_btn" id="orders" style="width: 207px;">
                <?
					$orders = f_MQuery("SELECT clan_id,name FROM clans");
					while ( $res = f_MFetch( $orders ) )
                        echo "<option value='{$res['clan_id']}'>{$res['name']}</option>\n";
					f_MClose();
                ?>
            </select>
        </td>
        <td>
            <input type="button" value="Показать" class="ss_btn" onclick="show(1);">
            &nbsp;
            <input type="button" value="Добавить" class="ss_btn" onclick="show(2);">
        </td>
    </tr>
    <tr>
        <td>
            Персонаж:
        </td>
        <td>
            <input type="text" class="m_btn" value="" id="players" style="width: 207px;">
        </td>
        <td>
            <input type="button" value="Показать" class="ss_btn" onclick="show(3);">
            &nbsp;
            <input type="button" value="Добавить" class="ss_btn" onclick="show(4);">
        </td>
    </tr>
</table>
<table id="ref" style="display: none;">
	<tr>
		<td>
			Ссылка:
		</td>
		<td>
			<input type="text" class="m_btn" value="" id="link" style="width: 207px" onfocus="">
		</td>
		<td>
			<input type="button" value="Обновить" class="ss_btn" onclick="refn();">
		</td>
	</tr>
</table>
<div id="locat"></div>
<script>if (orders || players) refn();</script>
<script>end_help();</script>
