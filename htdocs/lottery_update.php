<?

function update_lottery( )
{
	$res = f_MQuery( "SELECT * FROM lottery" );
	if( !f_MNum( $res ) ) $st = '<br><br><i>�� ����������� ������� ��� ������.</i>';
	else
	{
    	$st = '<table>';
    	$sum = 0;
    	while( $arr = f_MFetch( $res ) )
    	{
    		$plr = new Player( $arr[player_id] );
    		$st .= "<tr><td><script>document.write( ".$plr->Nick( )." );</script></td><td>$arr[value]</td></tr>";
    		$sum += "$arr[value]";
    	}
    	$st .= "<tr><td><b>�����:</b></td><td>$sum</td></tr>";
    	$st .= "</table>";
	}
	
	$file = fopen( 'lottery_bets.html', 'w+' );
	fwrite( $file, $st );
	fclose( $file );
}

?>
