<?

include_once( 'clan.php' );

function isPlayerMarried( )
{
	global $player;
	if( f_MValue( "SELECT count(*) FROM player_weddings WHERE p{$player->sex} = {$player->player_id}" ) ) return true;
	return false;
}


function hasPermissionToMarry( )
{
	global $player;
	global $CAN_MARRY;
	
	$lvl = f_MValue( "SELECT level FROM clan_buildings WHERE building_id=1 AND clan_id={$player->clan_id}" );
	if( $lvl >= 11 && 0 != ( getPlayerPermitions( $player->clan_id, $player->player_id ) & $CAN_MARRY ) ) return true;
	
	return false;
}

function marriageActions( )
{
	global $player;
	
	if( isPlayerMarried( ) ) return "";
	if( $player->sex == 1 ) return "";
	if( $player->HasTrigger( 223 ) || $player->HasTrigger( 224 ) || $player->HasTrigger( 225 ) ) return "";
	
	return addslashes( "<li><a href='game.php?talk=114'>�������� � ��������</a>" );
}

function marriageMiddle( )
{
	global $player;
	global $_GET;
	
	$st = "";
	
	if( !isPlayerMarried( ) )
	{
    	if( $player->sex == 0 )
    	{
    		if( !$player->HasTrigger( 223 ) ) $st = "������ ��� ������ ������ �� ��������������, ���������� ���������� � ��������.<br><br>";
    		else
    		{
        		
        		if( isset( $_GET['unmarry'] ) ) f_MQuery( "DELETE FROM player_wedding_bets WHERE p0={$player->player_id}" );
        		
        		$pid = f_MValue( "SELECT p1 FROM player_wedding_bets WHERE p0 = {$player->player_id}" );
        		if( !$pid )
        		{
        			if( isset( $_GET['marry'] ) )
        			{
        				$pid = f_MValue( "SELECT player_id FROM characters WHERE login='".conv_utf($_GET['marry'])."' AND sex=1" );
        				if( !$pid ) { $pid = 0; echo "<script>alert( '������ ".htmlspecialchars($_GET[marry],ENT_QUOTES)." �� ����������' );</script>"; }
        				else f_MQuery( "INSERT INTO player_wedding_bets( p0, p1 ) VALUES ( {$player->player_id}, $pid )" );
        			}
        		}
        		if( !$pid ) $st = addslashes( "�� ������ <a href='javascript:marry()'>������</a> ������ �� ��������������" );
        		else
        		{
        			$login = f_MValue( "SELECT login FROM characters WHERE player_id=$pid" );
        			$ver = f_MValue( "SELECT moo FROM player_wedding_bets WHERE p0 = {$player->player_id}" );
        			if( !$ver ) $st = "�� ������ ������ �� �������������� � $login, �� ��� ��� �� ����������� ������ �������. �� ������ <a href=game.php?unmarry=1>��������</a> ������.<br><br>";
        			else $st = "�� ������ ������ �� �������������� � $login, � ��� ����������� ���� �������. ������ ����� ������������� ������, �������� ������ 11 ������, ������ ��������� ������� ��������������. �� ������ <a href=game.php?unmarry=1>��������</a> ������.<br><br>";
        		}
        	}
    	}
    	else
    	{
    		if( isset( $_GET['unmarry'] ) ) f_MQuery( "UPDATE player_wedding_bets SET moo=0 WHERE p1={$player->player_id}" );
    		if( isset( $_GET['marry'] ) )
    		{
    			$pid = (int)$_GET['marry'];
    			f_MQuery( "UPDATE player_wedding_bets SET moo=0 WHERE p1={$player->player_id}" );
    			f_MQuery( "UPDATE player_wedding_bets SET moo=1 WHERE p1={$player->player_id} AND p0=$pid" );
    		}
    		$arr = f_MFetch( f_MQuery( "SELECT * FROM player_wedding_bets WHERE p1={$player->player_id} AND moo=1" ) );
    		if( $arr )
    		{
    			$login = f_MValue( "SELECT login FROM characters WHERE player_id={$arr[p0]}" );
    			$st = "�� ����������� ������ �� $login. ������ ����� ������������� ������, �������� ������ 11 ������, ������ ��������� ������� ��������������. <a href=game.php?unmarry=1>���������� �� ������</a><br><br>";
    		}
    		else
    		{
    			$st = '';
    			$res = f_MQuery( "SELECT * FROM player_wedding_bets WHERE p1={$player->player_id}" );
    			while( $arr = f_MFetch( $res ) )
    			{
    				$login = f_MValue( "SELECT login FROM characters WHERE player_id={$arr[p0]}" );
    				$st .= "�������� $login ���������� ��� ���� � ������. <a href=game.php?marry={$arr[p0]}>�������</a> �����������.<br><br>";
    			}
    		}
    	}
    }
    else if( $player->sex == 0 ) $st = "�� ��� ������!<br><br>";
    else $st = "�� ��� �������!<br><br>";

	if( hasPermissionToMarry( ) )
	{
		if( isset( $_GET['do_marry'] ) )
		{
			$pid = (int)$_GET['do_marry'];
			$arr = f_MFetch( f_MQuery( "SELECT p0, p1 FROM player_wedding_bets WHERE moo=1 AND p0=$pid" ) );
			if( $arr )
			{
				if( $arr['p0'] != $player->player_id && $arr['p1'] != $player->player_id )
				{
    				f_MQuery( "DELETE FROM player_wedding_bets WHERE p0=$pid" );
    				f_MQuery( "INSERT INTO player_weddings( p0, p1 ) VALUES ( $arr[p0], $arr[p1] )" );
    				$plr1 = new Player( $arr['p0'] );
    				$plr2 = new Player( $arr['p1'] );
    				$plr1->SetTrigger( 224, 1 );
    				$plr2->SetTrigger( 224, 1 );
    				$plr1->SetTrigger( 223, 0 );
    				$plr2->SetTrigger( 223, 0 );
    				$plr1->syst2( "�� ������ ��� ������� ���� ����� ����� � {$plr2->login}! �����������!!!" );
    				$plr2->syst2( "�� ������ ��� ������� ���� ����� ����� � {$plr1->login}! �����������!!!" );
    				glashSay( "������ ��� {$plr1->login} � {$plr2->login} ������� ���� ����� �����! ��������� ����� {$player->login}." );
				}
				else echo "<script>alert('������ �������� ����!');</script>";
			}
		}
		
		$res = f_MQuery( "SELECT p0, p1 FROM player_wedding_bets WHERE moo=1" );
		while( $arr = f_MFetch( $res ) )
		{
			$login1 = f_MValue( "SELECT login FROM characters WHERE player_id=$arr[p0]" );
			$login2 = f_MValue( "SELECT login FROM characters WHERE player_id=$arr[p1]" );
			$st .= "������ $login1 � $login2 ����� ����������. <a href=game.php?do_marry=$arr[p0]>�������� ��</a><br><br>";
		}
		$st .= "...";
	}
	return $st;
}

?>
