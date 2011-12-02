<?

if( !isset( $mid_php ) ) die( );

include_once( 'feathers.php' );

$item_id = (int)$_GET['feather_id'];

$arr = f_MFetch( f_MQuery( "SELECT image, name, type, descr, effect FROM items WHERE item_id={$item_id}" ) );
if( !$arr || $arr['type'] != 25 ) RaiseError( '������� ������������ �������, ������� �� �������', "ITEM_ID: $item_id" );

$num = $player->NumberItems( $item_id );

if( $num == 0 ) die( "<script>location.href='game.php';</script>" );

$show_dispell = false;
if( isset( $_GET['player_id'] ) )
{
	if( $arr['effect'] == '28' && !isset( $_GET['target'] ) ) $show_dispell = true;
	else
	{
    	$target_id = (int)$_GET['player_id'];
    	$parr = f_MFetch( f_MQuery( "SELECT loc, depth FROM characters WHERE player_id = {$target_id}" ) );
    	if( $parr[0] == $player->location && ( $parr[1] == $player->depth || $parr[0] == 4 ) )
    	{
    		if( f_MValue( "SELECT count( player_id ) FROM online WHERE player_id = {$target_id}" ) )
    		{
    			if( canUseFeather( $player, true ) )
    			{
    				if( $player->DropItems( $item_id ) )
    				{
               			$plr = new Player( $target_id );
    					if( $arr['effect'] == '28' )
    					{
    						-- $num;
    						$target = (int)$_GET['target'];
    						f_MQuery( "LOCK TABLE player_feathers WRITE" );
    						$numf = f_MValue( "SELECT count( feather_id ) FROM player_feathers WHERE player_id={$plr->player_id} AND feather_id={$target}" );
   							f_MQuery( "DELETE FROM player_feathers WHERE player_id={$plr->player_id} AND feather_id={$target}" );
   							f_MQuery( "UNLOCK TABLES" );
   							for( $i = 0; $i < $numf; ++ $i )
   								undoFeather( $plr, $target );
                			echo "<script>alert( '�� ������� ��������� ������� �� ������ {$plr->login}.' );</script>";
    					}
    					else
    					{
                			if( doFeather( $plr, (int)$arr['effect'] ) )
                			{
                				-- $num;
                				echo "<script>alert( '�� ������� ��������� ������� �� ������ {$plr->login}.' );</script>";
                				$plr->syst2( "�������� <b>{$player->login}</b> �������� � ��� <b>{$arr[name]}</b>." );

                				// widow quest
                        	   	include_once( "quest_race.php" );
                        	   	updateQuestStatus ( $player->player_id, 2509 );
                			}
            				else
            				{
            					echo "<script>alert( '�� ������ ���� ���������� ������� ����� ����� �������, ��������� ������� ������.' );</script>";
            					$player->AddItems( $item_id );
            				}
        				}
    				}
    				else echo "<script>alert( '� ��� ��� ����� �������' );</script>";
    			}
    			else echo "<script>alert( '� ��������� ������ �� �� ������ ������������ �������' );</script>";
    		}
    		else echo "<script>alert( '�����, ��������, ����� ����� �� ����' );</script>";
    	}
    	else echo "<script>alert( '�����, ��������, ����� ������� � ������ �������' );</script>";
	}
}

if( $num == 0 ) die( "<script>location.href='game.php';</script>" );

echo "<table><tr><td valign='top'><img width='50' height='50' src='images/items/{$arr[image]}'></td><td valign='top'><b>{$arr[name]}</b><br>{$arr[descr]}<br>����������: <b>{$num}</b></td></tr>";
echo "<tr><td colspan='2'>";

if( $show_dispell )
{
	echo "<b>�������� ��� ������� �����:</b><br>";
	$player_id = (int)$_GET['player_id'];
	$res = f_MQuery( "SELECT DISTINCT feather_id FROM player_feathers WHERE player_id=$player_id" );
	if( !f_MNum( $res ) ) echo "<i>�� ������ ��� �� ������ �������</i><br>";
	while( $varr = f_MFetch( $res ) ) echo "<a href='game.php?feather_id={$item_id}&player_id={$player_id}&target={$varr[feather_id]}'><img width=50 height=50 src='images/items/{$fthrs[$varr[feather_id]][1]}' border=0></a>";
}

else
{
    if( !canUseFeather( $player, true ) ) echo "�� �� ������ ������������ �������<br>";
    else
    {
    	$res = f_MQuery( "SELECT characters.player_id FROM characters INNER JOIN online ON characters.player_id = online.player_id WHERE characters.loc = {$player->location} AND ( characters.depth = {$player->depth} OR characters.loc = 4 ) AND characters.player_id <> {$player->player_id}" );
    	if( f_MNum( $res ) == 0 ) echo "<i>����� ��� �� ������ ������, ����� ���...</i><br>";
    	else
    	{
    		while( $arr = f_MFetch( $res ) )
    		{
    			$plr = new Player( $arr[0] );
    			echo "<li><a href='game.php?feather_id={$item_id}&player_id={$plr->player_id}'>��������� �: </a><script>document.write( ".$plr->Nick( )." );</script><br>\n";
    		}
    	}
    }
}

echo "<br><li><a href='game.php'>����� � ���������</a><br>";

echo "</td></tr></table>";

?>
