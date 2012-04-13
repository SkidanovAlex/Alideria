<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">
<script src=functions.js></script>
<script src=js/ajax.js></script>
<script src=js/tooltips.php></script>
<script src=js/combat_panel.js></script>
<script src=js/combat_log2.js></script>
<script src=js/skin2.js></script>
<table width=100%><tr><td>
<center><button class=m_btn onClick='location.reload( )'>Обновить</button></center><br>
<script>
function tstr( a ) { return ''; }
<?

include( 'functions.php' );

f_MConnect( );

$id = $HTTP_GET_VARS['id'];
settype( $id, "integer" );

if ($id==1753037)
	$res = f_MQuery( "SELECT * FROM combat_log WHERE combat_id=$id ORDER BY id DESC LIMIT 50" );
else
	$res = f_MQuery( "SELECT * FROM combat_log WHERE combat_id=$id ORDER BY id DESC" );
while( $arr = f_MFetch( $res ) )
{
	if( $arr['string'][0] == ',' ) $arr['string'] = substr( $arr['string'], 1 );
	else $arr['string'] = "[0,\"".$arr['string']."\"]";
	print( "document.write( c_log( [{$arr['string']}] ) );" );
}

?>
</script>
</td></tr></table>
