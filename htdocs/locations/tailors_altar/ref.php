<?

include_once( 'items.php' );
include_once( 'guild.php' );
include_once( 'prof_exp.php' );
include_once( 'locations/tailors_altar/func.php' );

if( !isset( $mid_php ) ) die( );

$extracts = array( 8032 => 1, 8034 => 1, 8035 => 1 );
$guild_id = TAILORS_GUILD;
$teachers_id = ALCHEMY_GUILD;
$guild = new Guild( $guild_id );
$teachers = new Guild( $teachers_id );
if( !$guild->LoadPlayer( $player->player_id ) )
{
	if( !$teachers->LoadPlayer( $player->player_id ) )
	{
		die( );
    }
    else
    {
    	if( $_GET['act'] == 1 )
    		echo "_( 'tailors' ).innerHTML = ".getTailorsList( ).";";
    	else if( $_GET['act'] == 2 )
    	{
    		$pid = (int)$_GET['whom'];
    		$login = f_MValue( "SELECT login FROM characters WHERE player_id=$pid AND loc={$player->location} AND depth={$player->depth}" );
    		if( !$login ) echo "alert( 'Игрок успел уйти от алтаря' );";
    		else
    		{
    			if( $player->DropItemsArr( $extracts, 36, 2, 3 ) )
    			{
    				$success = false;
        			f_MQuery( "LOCK TABLE tailors_altar WRITE" );
    				$has = f_MValue( "SELECT count( player_id ) FROM tailors_altar WHERE player_id=$pid" );
    				if( $has ) echo "alert( 'Указанный игрок уже умеет варить смеси на алтаре.' );";
    				else
    				{
    					$success = true;
    					f_MQuery( "INSERT INTO tailors_altar( player_id ) VALUES ( $pid )" );
	        			echo "alert( 'Вы успешно передали игроку $login все необходимые знания' );";
    				}
        			f_MQuery( "UNLOCK TABLES" );
        			if( !$success )
        			{
        				foreach( $extracts as $item_id => $num )
        				{
        					$player->AddItems( $item_id, $num );
        					$player->AddToLogPost( $item_id, $num, 36, 2, 3 );
        				}
        			}
        			else
        			{
        				$plr = new Player( $pid );
        				$plr->syst2( "/items" );
        				$plr->syst2( "Персонаж <b>{$player->login}</b> обучил вас мастерству работы на Алтаре Портных." );
        			}
        		}
        		else echo "alert( 'Для начала обучения вам надо иметь по одному экстракту каждого цвета' );";
    		}
    	}
    }
   	return;
}

$res = f_MQuery( "SELECT * FROM tailors_altar WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	die( );
}

$a = (int)$_GET['a'];
$b = (int)$_GET['b'];

performAction( $a, $b );
echo "_( 'altar_content' ).innerHTML = \"".getAltarContent( )."\";"

?>
