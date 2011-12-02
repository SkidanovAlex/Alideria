<?

define( 'FISHMEN_GUILD', 101 );
define( 'BERRYPICKERS_GUILD', 102 );
define( 'MINERS_GUILD', 103 );
define( 'SMITH_GUILD', 104 );
define( 'JEWELRY_GUILD', 105 );
//define( 'GLASSBLOWING_GUILD', 107 );
define( 'ALCHEMY_GUILD', 106 );
define( 'HUNTERS_GUILD', 108 );
define( 'TAILORS_GUILD', 109 );

$rank_prices = Array( 500, 1000, 2000, 4000, 6000, 8000, 8000, 8000, 10000, 10000, 10000, 10000, 10000, 10000, 12000, 12000, 12000, 12000, 12000, 12000, 12000, 14000, 16000, 18000, 20000, 21000, 22000, 23000, 24000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 25000, 1000000, 1000000 );

$guilds = Array
(	/* ID */                     /* Name */      /*help npc craft mine */
	FISHMEN_GUILD      => Array( "Рыбаков",      34276, 12, 1,    1 ),
	BERRYPICKERS_GUILD => Array( "Собирателей",  34277, 13, 0,    1 ),
	MINERS_GUILD       => Array( "Старателей",   34278, 14, 0,    1 ),
	SMITH_GUILD        => Array( "Кузнецов",     34279, 15, 1,    0 ),
	JEWELRY_GUILD      => Array( "Ювелиров",     34280, 16, 1,    0 ),
//	GLASSBLOWING_GUILD => Array( "Стеклодувов",  34281, 18, 1,    0 ),
	ALCHEMY_GUILD      => Array( "Алхимиков",    34282, 17, 1,    0 ),
	HUNTERS_GUILD      => Array( "Охотников",    34315, 20, 0,    1 ),
	TAILORS_GUILD      => Array( "Портных",      50006, 28, 1,    0 )
);

$kapkan_texts = Array( );
$kapkan_texts[FISHMEN_GUILD] = 'поставили сети';
$kapkan_texts[HUNTERS_GUILD] = 'установили капканы';

class Guild
{
	var $guild_id;
	
	var $rating;
	var $rank;

	function Guild( $id )
	{
		$this->guild_id = $id;
	}
	
	function LoadPlayer( $player_id )
	{
		$res = f_MQuery( "SELECT * FROM player_guilds WHERE player_id=$player_id AND guild_id={$this->guild_id}" );
		if( !f_MNum( $res ) ) return false;
		
		$arr = f_MFetch( $res );
		$this->rating = $arr[rating];
		$this->rank = $arr[rank];
		
		return true;
	}
}

function GuildsPerLevel( $level )
{
	if( $level < 5 ) return 1;
	if( $level < 12 ) return 2;
	if( $level < 25 ) return 3;
	return 4;
}

function guild_list( $clan_id )
{
	global $guilds;
	$ret = '';
    if(!f_MFetch(f_MQuery("SELECT * FROM player_clans WHERE clan_id=$clan_id")))
     {
      die( "<i>Нет такого Ордена</i>" );
     }
    else
    {
    $tit = Array( );
    foreach( $guilds as $k=>$v )
    	$tit[$k] = $v[0];
    $ret .= "<table width=700 background=images/chat/chat_bg.gif>"; /*style='border:1px solid black' */
    $ret .= "<tr><td align=center><i><b>#</b></i></td>";
    $ret .= "<td ><i><b>Ник</b></i></td>";
    $ret .= "<td align=center><i><b>Ранг</b></i></td>";
    $ret .= "<td align=center><i><b>Рейтинг</b></i></td></tr>";


    foreach( $tit as $nm_g=>$dummy )
    {

    $og_list = f_MQuery("SELECT * FROM player_guilds WHERE guild_id=$nm_g AND player_id IN (SELECT player_id from player_clans where clan_id=$clan_id)");
    $nm=1;
    if(f_MNum($og_list)){$ret .= "<tr><td align=center colspan=4 background=images/chat/line.gif><i><b>".$tit[$nm_g]."</b></i></td></tr>";}
    while( $arr = f_MFetch( $og_list ) )
    {
      $ret .= "<tr><td align=center>".$nm."</td>";

      $plr = new Player( $arr['player_id']);
      $ret .= "<td><script>document.write( ".$plr->Nick().") ;</script></td>";

      $ret .= "<td align=center>".$arr['rank']."</td>";
      $ret .= "<td align=center>".$arr['rating']."</td></tr>";
       
    ++$nm;
    }
    }
    $ret .= "</table>";
    }

    return $ret;
}


?>
