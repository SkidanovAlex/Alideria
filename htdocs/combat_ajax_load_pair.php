<?

//mb_http_output('windows-1251');
header("Content-type: text/html; charset=windows-1251");

$id = $HTTP_RAW_POST_DATA;
settype( $id, 'integer' );

include( 'functions.php' );

f_MConnect( );
$res = f_MQuery( "SELECT * FROM combat_ajax_data WHERE note_id = $id" );
$arr = f_MFetch( $res );
if( !$arr ) $txt = ", [0,'<i>Ошибка загрузки фрагметра лога</i>']";
else $txt = $arr['data'];

$txt = str_replace( "\n", "", substr( $txt, 1 ) );

print( "if( document.getElementById( 'pair_div$id' ) ) document.getElementById( 'pair_div$id' ).innerHTML = \"<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td width=13><img width=13 height=0></td><td width=100%>\" + c_log([$txt]) + \"</td></tr></table>\";" );

?>
