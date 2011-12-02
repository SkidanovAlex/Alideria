<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<head>
<title>Смайлики</title>
</head>
 
<html>
<body>

 <?

require( "smiles_list.php" );

echo "<center><table>";
for( $i = 0; $i < 7; ++ $i )
{
	echo "<tr style='height:30px'>";
	for( $j = 0; $j < 7; ++ $j )
	{
		$id = $i * 7 + $j;
		echo "<td align=center style='height:30px;width:70px'><a onclick='window.opener.parent.chat_in.smile_call_back(\"*{$smiles[$id]}*\");window.close();' title='*{$smiles[$id]}*' style='cursor:pointer'><img border=0 src='images/smiles/{$smiles[$id]}.gif' alt='*{$smiles[$id]}*'></a></td>";
	}
	echo "</tr>";
}

include_once( "functions.php" );
f_MConnect( );
if( check_cookie( ) )
{
	$pid = (int)$HTTP_COOKIE_VARS['c_id'];
	$res = f_MQuery( "SELECT set_id FROM paid_smiles WHERE player_id=$pid AND expires >= ".time( )." ORDER BY set_id" );
	$sml = array();
	while( $arr = f_MFetch( $res ) )
	{
		foreach( $vsmiles[$arr[0]] as $a ) $sml[] = $a;
	}
	
	
	while( count( $sml ) % 7 != 0 ) $sml[] = "";
	
	
    for( $i = 0; $i < (int)( count( $sml ) / 7 ); ++ $i )
    {
    	echo "<tr style='height:30px'>";
    	for( $j = 0; $j < 7; ++ $j )
    	{
    		$id = $i * 7 + $j;
    		if( $sml[$id] != "" ) echo "<td align=center style='height:30px;width:70px'><a onclick='window.opener.parent.chat_in.smile_call_back(\"*{$sml[$id]}*\");window.close();' title='*{$sml[$id]}*' style='cursor:pointer'><img border=0 src='images/smiles/{$sml[$id]}.gif' alt='*{$sml[$id]}*'></a></td>";
    		else echo "<td>&nbsp;</td>";
    	}
    	echo "</tr>";
    }
}

echo "</table></center>";

?>

</body>
</html>