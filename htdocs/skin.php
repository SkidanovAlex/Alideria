<?php

function GetScrollTableStart($align = "center",$valign="top")
{
	$s = "<table width=100% height=100% cellspacing=0 cellpadding=0 border=0>";
	$s .= "<tr>";

	$s .= "<td width=5 height=5 bgcolor=e3ac67><img src=/images/tooltip/tip_corner_0.gif width=5 height=5></td>";
				
	$s .= "<td height=5 background=/images/tooltip/tip_border_top.gif bgcolor=e3ac67></td>";
	$s .= "<td width=5 height=5 bgcolor=e3ac67><img src=/images/tooltip/tip_corner_1.gif width=5 height=5></td>";

	$s .= "</tr>";
	$s .= "<tr>";

	$s .= "<td width=5 background=/images/tooltip/tip_border_left.gif bgcolor=e3ac67></td>";
		
	$s .= "<td align=\"".$align."\" valign=$valign bgcolor=e3ac67 background=/images/tooltip/tip_bg.gif>";

	return $s;
}

function GetScrollTableEnd()
{
	$s = "</td>";
	$s .= "<td width=5 background=/images/tooltip/tip_border_right.gif bgcolor=e3ac67></td>";

	$s .= "</tr>";

	$s .= "<tr>";

	$s .= "<td width=5 height=5 bgcolor=e3ac67><img src=/images/tooltip/tip_corner_2.gif width=5 height=5></td>";
		
	
	$s .= "<td height=5 background=/images/tooltip/tip_border_bottom.gif bgcolor=e3ac67></td>";
	$s .= "<td width=5 height=5 bgcolor=e3ac67><img src=/images/tooltip/tip_corner_3.gif width=5 height=5></td>";
			
	$s .= "</tr>";
	$s .= "</table>";

	return $s;
}


function ScrollTableStart($align = "center",$valign='top')
{
	echo GetScrollTableStart($align,$valign);
}

function ScrollTableEnd()
{
	echo GetScrollTableEnd();	
}


function GetScrollLightTableStart($align = "center")
{
	$s = "<table width=100% cellspacing=0 cellpadding=0 border=0>";
	$s .= "<tr>";

	$s .= "<td width=5 height=5 bgcolor=e0c3a0><img src=/images/chat/chat_corner_0.gif width=5 height=5></td>";
				
	$s .= "<td height=5 background=/images/chat/chat_border_top.gif bgcolor=e0c3a0></td>";
	$s .= "<td width=5 height=5 bgcolor=e0c3a0><img src=/images/chat/chat_corner_1.gif width=5 height=5></td>";

	$s .= "</tr>";
	$s .= "<tr>";

	$s .= "<td width=5 background=/images/chat/chat_border_left.gif bgcolor=e0c3a0></td>";
		
	$s .= "<td align=\"".$align."\" valign=middle bgcolor=e0c3a0 background=/images/chat/chat_bg.gif>";

	return $s;
}

function GetScrollLightTableStart2($align = "center", $valign="middle")
{
	$s = "<table width=100% height=100% cellspacing=0 cellpadding=0 border=0>";
	$s .= "<tr>";

	$s .= "<td width=5 height=5 bgcolor=e0c3a0><img src=/images/chat/chat_corner_0.gif width=5 height=5></td>";
				
	$s .= "<td height=5 background=/images/chat/chat_border_top.gif bgcolor=e0c3a0></td>";
	$s .= "<td width=5 height=5 bgcolor=e0c3a0><img src=/images/chat/chat_corner_1.gif width=5 height=5></td>";

	$s .= "</tr>";
	$s .= "<tr>";

	$s .= "<td width=5 background=/images/chat/chat_border_left.gif bgcolor=e0c3a0></td>";
		
	$s .= "<td align=\"".$align."\" valign=\"".$valign."\" bgcolor=e0c3a0 background=/images/chat/chat_bg.gif>";

	return $s;
}

function GetScrollLightTableEnd()
{
	$s = "</td>";
	$s .= "<td width=5 background=/images/chat/chat_border_right.gif bgcolor=e0c3a0></td>";

	$s .= "</tr>";

	$s .= "<tr>";

	$s .= "<td width=5 height=5 bgcolor=e0c3a0><img src=/images/chat/chat_corner_2.gif width=5 height=5></td>";
		
	
	$s .= "<td height=5 background=/images/chat/chat_border_bottom.gif bgcolor=e0c3a0></td>";
	$s .= "<td width=5 height=5 bgcolor=e0c3a0><img src=/images/chat/chat_corner_3.gif width=5 height=5></td>";
			
	$s .= "</tr>";
	$s .= "</table>";

	return $s;
}


function ScrollLightTableStart($align = "center")
{
	echo GetScrollLightTableStart($align);
}


function ScrollLightTableEnd()
{
	echo GetScrollLightTableEnd();
}


function ScrollInnerTableStart($align = "center")
{
	echo "<table cellspacing=0 cellpadding=0 border=0>";
	echo "<tr>";

	echo "<td width=5 height=5 bgcolor=e3ac67><img src=images/tooltip/tip_corner_4.gif width=5 height=5></td>";
				
	echo "<td height=5 background=images/tooltip/tip_border_bottom.gif bgcolor=e3ac67></td>";
	echo "<td width=5 height=5 bgcolor=e3ac67><img src=images/tooltip/tip_corner_5.gif width=5 height=5></td>";

	echo "</tr>";
	echo "<tr>";

	echo "<td width=5 background=images/tooltip/tip_border_right.gif bgcolor=e3ac67></td>";
		
	echo "<td align=\"".$align."\" valign=middle bgcolor=e3ac67 background=images/tooltip/tip_bg.gif>";
}

function ScrollInnerTableEnd()
{
	echo "</td>";
	echo "<td width=5 background=images/tooltip/tip_border_left.gif bgcolor=e3ac67></td>";

	echo "</tr>";

	echo "<tr>";

	echo "<td width=5 height=5 bgcolor=e3ac67><img src=images/tooltip/tip_corner_6.gif width=5 height=5></td>";
		
	
	echo "<td height=5 background=images/tooltip/tip_border_top.gif bgcolor=e3ac67></td>";
	echo "<td width=5 height=5 bgcolor=e3ac67><img src=images/tooltip/tip_corner_7.gif width=5 height=5></td>";
			
	echo "</tr>";
	echo "</table>";
}


function ScrollLightTableStart2($align = "center",$valign='middle')
{
	$s = "<table width=100% height=100% cellspacing=0 cellpadding=0 border=0>";
	$s .= "<tr>";

	$s .= "<td width=5 height=5 bgcolor=e0c3a0><img src=/images/chat/chat_corner_0.gif width=5 height=5></td>";
				
	$s .= "<td height=5 background=/images/chat/chat_border_top.gif bgcolor=e0c3a0></td>";
	$s .= "<td width=5 height=5 bgcolor=e0c3a0><img src=/images/chat/chat_corner_1.gif width=5 height=5></td>";

	$s .= "</tr>";
	$s .= "<tr>";

	$s .= "<td width=5 background=/images/chat/chat_border_left.gif bgcolor=e0c3a0></td>";
		
	$s .= "<td align=\"".$align."\" valign=$valign bgcolor=e0c3a0 background=/images/chat/chat_bg.gif>";

	echo $s;
}

// Lighter version

function lsInit( $script = true )
{
	if( $script ) echo "<script>\n";
/*	echo "function FUlt() { document.write( '" . AddSlashes( GetScrollTableStart( "left", "top" ) ) . "' ); };\n";
	echo "function FUrt() { document.write( '" . AddSlashes( GetScrollTableStart( "right", "top" ) ) . "' ); };\n";
	echo "function FUct() { document.write( '" . AddSlashes( GetScrollTableStart( "center", "top" ) ) . "' ); };\n";
	echo "function FUlm() { document.write( '" . AddSlashes( GetScrollTableStart( "left", "middle" ) ) . "' ); };\n";
	echo "function FUrm() { document.write( '" . AddSlashes( GetScrollTableStart( "right", "middle" ) ) . "' ); };\n";
	echo "function FUcm() { document.write( '" . AddSlashes( GetScrollTableStart( "center", "middle" ) ) . "' ); };\n";
	echo "function FL() { document.write( '" . AddSlashes( GetScrollTableEnd( ) ) . "' ); };\n";
	echo "function FLUl() { document.write( '" . AddSlashes( GetScrollLightTableStart( "left" ) ) . "' ); };\n";
	echo "function FLUr() { document.write( '" . AddSlashes( GetScrollLightTableStart( "right" ) ) . "' ); };\n";
	echo "function FLUc() { document.write( '" . AddSlashes( GetScrollLightTableStart( "center" ) ) . "' ); };\n";
	echo "function FLL() { document.write( '" . AddSlashes( GetScrollLightTableEnd( ) ) . "' ); };\n";
*/
	echo "function rFUlt() { return( '" . AddSlashes( GetScrollTableStart( "left", "top" ) ) . "' ); };\n";
	echo "function rFUrt() { return( '" . AddSlashes( GetScrollTableStart( "right", "top" ) ) . "' ); };\n";
	echo "function rFUct() { return( '" . AddSlashes( GetScrollTableStart( "center", "top" ) ) . "' ); };\n";
	echo "function rFUlm() { return( '" . AddSlashes( GetScrollTableStart( "left", "middle" ) ) . "' ); };\n";
	echo "function rFUrm() { return( '" . AddSlashes( GetScrollTableStart( "right", "middle" ) ) . "' ); };\n";
	echo "function rFUcm() { return( '" . AddSlashes( GetScrollTableStart( "center", "middle" ) ) . "' ); };\n";
	echo "function rFL() { return( '" . AddSlashes( GetScrollTableEnd( ) ) . "' ); };\n";
	echo "function rFLUl() { return( '" . AddSlashes( GetScrollLightTableStart( "left" ) ) . "' ); };\n";
	echo "function rFLUr() { return( '" . AddSlashes( GetScrollLightTableStart( "right" ) ) . "' ); };\n";
	echo "function rFLUc() { return( '" . AddSlashes( GetScrollLightTableStart( "center" ) ) . "' ); };\n";
	echo "function rFLL() { return( '" . AddSlashes( GetScrollLightTableEnd( ) ) . "' ); };\n";

	if( $script ) echo "</script>\n";
}

?>