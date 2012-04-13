<?
                
                function ShowAdditionalActions( )
                {
                	global $status, $player; 
                    $st_act = "";
                    if( $status != 4 && $status != 5 && $player->location != 1 && $player->location != 6 && $player->location != 7 && $player->regime == 0 ) // в лесу это сделано само
                    {
                    	$zres = f_MQuery( "SELECT * FROM forest_additional_actions WHERE cell_type = -1 AND loc = {$player->location} AND depth = {$player->depth}" );
    					while( $zarr = f_MFetch( $zres ) )
    					{
    						if( allow_phrase( $zarr[condition_id] ) )
    							$st_act .= "<a href=\"game.php?do=$zarr[entry_id]\"><li>$zarr[text]</a>";
    					}
                    }
                    if( $st_act !== "" )
                    {
                    	echo "<ul>$st_act</ul>";
                    }
                }


   				function ShowNPCs( )
				{
					global $status, $player, $noob;

					if( $player->location == 2 && $player->depth == 1001 && $player->sex == 1 ) return; // Не показываем девочкам квест на хоббита. Пока так.

    				if( $status != 4 && $status != 5 && $player->regime == 0 ) // отключено в залах кланов и гильдий
    				{
    					$nres = f_MQuery( "SELECT * FROM npcs WHERE location = {$player->location} AND depth = {$player->depth}" );
    					if( mysql_num_rows( $nres ) )
    					{
    						$stlk = '';
    						while( $narr = f_MFetch( $nres ) )
    							if( $narr[condition_id] == -1 || allow_phrase( $narr[condition_id], true ) )
    							{
    								if(  $noob) $stlk .= "<li><a href='#' onclick='alert(\"Поговори с Незнакомцем после того, как проведешь свой первый бой. Следуй указаниям Астаниэль.\");'>$narr[name]</a><br>";
    								else $stlk .= "<li><a href='game.php?talk=$narr[npc_id]'>$narr[name]</a><br>";
    							}
    						if( $stlk != '' )
    						{
    							print( "<b>Здесь можно поговорить с:</b><br><ul>" );
    							echo $stlk;
    							print( "</ul>" );
    						}
    					}
    				}
				}
				
				function showFights( )
				{
					global $player;
					
					$combats = f_MQuery( "SELECT * FROM `combats` WHERE `location` = {$player->location} AND `place` = {$player->depth}" );
					
					while( $combat = f_MFetch( $combats ) )
					{
						$fighters = f_MQuery( "SELECT `player_id` FROM `combat_players` WHERE `ai` = 1 AND `combat_id` = $combat[combat_id]" );
						
						$shamLogins = array( '', 'Шамаханин-капитан', 'Шамаханин-генерал', 'Шамаханин-боец' );
						while( $fighter = f_MFetch( $fighters ) )
						{
							$mobLogin = f_MValue( "SELECT `login` FROM `characters` WHERE `player_id` = $fighter[player_id]" );
							
							if( !array_search( $mobLogin, $shamLogins ) )
							{
								continue;							
							}
							
							echo "<a href=\"?att=$fighter[player_id]\">[x]</a> <a href=\"/player_info.php?id=$fighter[player_id]\" target=\"_blank\">$mobLogin</a><br />";
						} 					
					}
				}

?>

