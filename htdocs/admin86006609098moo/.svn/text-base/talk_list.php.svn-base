<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );

f_MConnect( );

include( 'quest_header.php' );

$p = Array( );

function rec( $talk_id )
{
	global $p;
	if( $p[$talk_id] ) return;
	$p[$talk_id] = 1;
	$res = f_MQuery( "SELECT attack_id FROM phrases, talk_phrases WHERE talk_id = $talk_id AND phrases.phrase_id = talk_phrases.phrase_id" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr[0] <= -1000000 ) $arr[0] += 1000000;
		if( $arr[0] < 0 ) rec( - $arr[0] );
	}
	$res = f_MQuery( "SELECT redir_to FROM talks WHERE talk_id = $talk_id" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr[0] != 0 ) rec( $arr[0] );
	}
}

if( !isset( $HTTP_GET_VARS['uin'] ) )
{
	$res = f_MQuery( "SELECT talk_id, text FROM talks ORDER BY talk_id" );

	while( $arr = mysql_fetch_array( $res ) )
	{
		print( "<a href=talk_editor.php?talk_id=$arr[talk_id] target=mid>$arr[talk_id]</a> " );
	}
}
else
{
	$uin = $HTTP_GET_VARS['uin'];
	settype( $uin, 'integer' );
	$res = f_MQuery( "SELECT name, talk_id FROM npcs WHERE npc_id = $uin" );
	$arr = f_MFetch( $res );
	if( !$arr ) print( "Нет такого NPC" );
	else
	{
		print( "Толки NPC <b>$arr[name]</b><br>" );
		rec( $arr[talk_id] );
		$res2 = f_MQuery( "SELECT talk_id FROM talk_redirects WHERE npc_id = $uin" );
		while( $arr2 = f_MFetch( $res2 ) ) rec( $arr2[talk_id] );
		ksort( $p );
		foreach( $p as $a=>$b )
		{
			print( "<a href=talk_editor.php?talk_id=$a target=mid>$a</a> " );
		}
	}
}

f_MClose( );

?>
</div>
