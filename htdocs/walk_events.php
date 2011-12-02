<?

if( !$mid_php )
	die( );

function walk_event( $loc, $depth, $regime, $tags = true )
{
	global $player;
	$st = "";
	
	if( $regime == -1 || $regime == 1 || $regime == 0 )
	{
		include( "kopka.php" );
		$kopka = new Kopka( );
		$res = f_MQuery( "SELECT items.item_id, items.price FROM cave_items, items WHERE cave_items.depth = $depth AND cave_items.item_id=items.item_id" );
		while( $arr = f_MFetch( $res ) )
			$kopka->AddItem( $arr[0], $arr[1] );
			
		$kopka->GetItemId( 15, 150 + $depth * 20 + ( $depth ? 75 : 0 ) );
		
		if( !$kopka->num ) $st .= "К сожалению, Вы ничего не нашли.<br>";
		else
		{
			$res2 = f_MQuery( "SELECT name4, image FROM items WHERE item_id = {$kopka->item_id}" );
			if( !f_MNum( $res ) ) RaiseError( "Ошибка при поиске в пещерах. Найдена вещь, которой нет в базе", "Глубина: $depth; АйДи: {$kopka->item_id}" );
			
			$player->AddToLog( $kopka->item_id, $kopka->num, 7, $depth );
			$player->AddItems( $kopka->item_id, $kopka->num );
			$player->UpdateWeightStr( $tags );
			$arr2 = f_MFetch( $res2 );
			$st .= "Вы нашли<br><br><a target=_blank href=help.php?id=1010&item_id={$kopka->item_id}><img border=0 src=images/items/{$arr2[1]}><br><b>$arr2[0]</b></a>";
			if( $kopka->num > 1 ) $st .= " ({$kopka->num})";
			$st .= "<br>";
		}
	}
	
	return $st;
}

?>
