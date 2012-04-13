var hide_iv;

function typenumber( id, num )
{
  var el = _( id );
  var st = el.value;
  setTimeout( "if( hide_iv ) clearTimeout( hide_iv );_( '" + id + "' ).focus();", 10 );
  if( st.length < 4 )
  {
    st += '' + num;
    el.value = st;
  }
}

function delnumber( id )
{
  var el = _( id );
  var st = el.value;
  setTimeout( "if( hide_iv ) clearTimeout( hide_iv );_( '" + id + "' ).focus();", 10 );
  if( st.length > 0 )
  {
    st = st.slice( 0, st.length - 1 );
    el.value = st;
  }
  el.focus( );
}

function showkeyboard( id )
{
  var el = _( id );
  var pos = getAp( el );
  var x = pos.x;
  var y = pos.y - 32*4;
  
  var st = "<div style='position:absolute;left:" + x + "px;top:" + y + "px;display:none;' id=" + id + "_moo>";
  st += "<table bgcolor=#E3AC67 style='border:1px solid black;width:96px;height:128px;' cellspacing=0 cellpadding=0>";
  for( var i = 0; i < 3; ++ i )
  {
    st += "<tr>";
    for( var j = 0; j < 3; ++ j )
    {
      var style = "cursor:pointer;border-bottom:1px solid black;";
      if( j < 2 ) style += "border-right:1px solid black;";
      st += "<td onmousedown='typenumber(\"" + id + "\", "+(i*3+j+1)+")' width=32 height=32 style='" + style + "' align=center valign=middle>";
      st += "<big><big><big><b>" + (i*3+j+1) + "</b></big></big></big>";
      st += "</td>";
    }
    st += "</tr>";
  }
  st += "<tr>";
  st += "<td style='cursor:pointer;border-right:1px solid black' onmousedown='typenumber(\"" + id + "\", 0)' align=center valign=middle><big><big><big><b>0</b></big></big></big></td>";
  st += "<td style='cursor:pointer' onmousedown='delnumber(\"" + id + "\")' colspan=2 align=center valign=middle><small><b>Backspace</b></small></td>";
  st += "</tr>";
  st += "</table>";
  st += "</div>";
  
  document.write( st );

  var el2 = _( id + "_moo" )
  function do_show( ) { 
  	  var pos = getAp( el );
	  var x = pos.x;
	  var y = pos.y - 32*4;
	  el2.style.left = x + 'px';
	  el2.style.top = y + 'px';
	  el2.style.display = '';
  }

  function do_hide( ) { el2.style.display = 'none'; }
  function do_hide_tm( ) { hide_iv = setTimeout( do_hide, 150 ); }
  el.onfocus = do_show;
  el.onclick = do_show;
  el.onblur = do_hide_tm;
}
