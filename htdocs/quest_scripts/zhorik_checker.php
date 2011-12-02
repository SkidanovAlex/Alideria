<?

function checkZhorik( $player, $id, $num )
{
    if( !$player->HasTrigger( 102 ) && !$player->HasTrigger( 101 ) && $player->GetQuestValue( 102 ) == $id )
    {
    	if( $num == 1 )
    	{
    		$player->SetTrigger( 102 );
    		$player->syst2( 'Вы успешно выполнили задание фавна, пора вернуться к нему, чтобы получить награду' );
    		return true;
    	}
    	else
    	{
    		$player->AlterQuestValue( 42, 1 );
    		if( $player->GetQuestValue( 42 ) == $num )
    		{
        		$player->SetTrigger( 102 );
	    		$player->syst2( 'Вы успешно выполнили задание фавна, пора вернуться к нему, чтобы получить награду' );
        		return true;
    		}
    	}
    }
    return false;
}

?>
