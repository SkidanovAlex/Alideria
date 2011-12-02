<?php

include( "functions.php" );

f_MConnect( );

$res = f_MQuery( "SELECT * FROM player_selected_cards WHERE staff=1" );
while( $arr = f_MFetch( $res ) )
{
	$pname = f_MValue( "SELECT login FROM characters WHERE player_id={$arr[player_id]}" );
	$cname = f_MValue( "SELECT name FROM cards WHERE card_id={$arr[card_id]}" );
	$mk = f_MValue( "SELECT mk FROM cards WHERE card_id={$arr[card_id]}" );

	$q = f_MValue( "SELECT number FROM player_cards WHERE player_id={$arr[player_id]} AND card_id={$arr[card_id]}" );
	if( $q == 0 )
	{
		f_MQuery( "DELETE FROM player_selected_cards WHERE player_id={$arr[player_id]} AND card_id={$arr[card_id]} AND staff=1" );
		echo "$pname, $cname, $mk - нет вообще, но выбран на бой<br>";
	}
	else
	{
    	$v = f_MValue( "SELECT count(*) FROM player_items AS p INNER JOIN items AS i ON p.item_id=i.item_id WHERE i.inner_spell_id={$arr[card_id]} AND player_id={$arr[player_id]} AND weared>0" );
    	if( !$v )
    	{
    		if( $q == 1 )
    		{
    			f_MQuery( "DELETE FROM player_selected_cards WHERE player_id={$arr[player_id]} AND card_id={$arr[card_id]} AND staff=1" );
    			f_MQuery( "DELETE FROM player_cards WHERE player_id={$arr[player_id]} AND card_id={$arr[card_id]}" );
    			echo "$pname, $cname, $mk - не виден, но есть в книге, и есть в бою - выбрать нельзя<br>";
    		}
    		else if( $q == 11 )
    		{
    			f_MQuery( "DELETE FROM player_selected_cards WHERE player_id={$arr[player_id]} AND card_id={$arr[card_id]} AND staff=1" );
    			f_MQuery( "UPDATE player_cards SET number=10 WHERE player_id={$arr[player_id]} AND card_id={$arr[card_id]}" );
    			echo "$pname, $cname, $mk - есть в книге и в бою, но не в списке выбранных - выбрать можно<br>";
    		}
    		else if( $q == 10 )
    		{
    			f_MQuery( "DELETE FROM player_selected_cards WHERE player_id={$arr[player_id]} AND card_id={$arr[card_id]} AND staff=1" );
    		}
    		else echo "$pname, $cname, $mk - $q<br>";
    	}
    	else echo "<i>$pname, $cname, $mk - есть посох</i><br>";
	}
}

?>
