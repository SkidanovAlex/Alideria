<?

include_once( 'prof_exp.php' );

if( !isset( $mid_php ) ) die( );

echo "<b>�������� ������</b> - <a href=game.php?order=main>�����</a><br>";

$arr = f_MFetch( f_MQuery( "SELECT food FROM clans WHERE clan_id=$clan_id" ) );
$food = $arr[0];

$inner_food = array(
	array( "����� �� ����������", array( 111=>1, 112=>1, 113=>1 ), 6, 100, 14259 ),
	array( "����� � �����", array( 97 => 18 ), 6, 200, 14262 ),
	array( "������ �� ����", array( 106 => 16, 107 => 10, 108 => 8 ), 4, 500, 14263 )

);

$food_types = Array(
	Array( "������� ������", Array( 106 => 7 ), 7 ),
	Array( "��� �� �����", Array( 107 => 4 ), 8 ),
	Array( "���� �� �����", Array( 108 => 3 ), 9 ),
	Array( "���� �� ���������", Array( 109 => 2 ), 10 )
);

$item_names = Array(
	106 => "������", 107 => "����", 108 => "����", 109 => "���"
);
$item_images = Array(
	106 => "res/karas.gif", 107 => "res/carp.gif", 108 => "res/shuka.gif", 109 => "res/som.gif"
);

$item_names[111] = '�����';   $item_images[111] = 'res/bl_alga.gif';
$item_names[112] = '�������'; $item_images[112] = 'res/red_alga.gif';
$item_names[113] = '�������'; $item_images[113] = 'res/gr_alga.gif';
$item_names[97] = '����� ���'; $item_images[97] = 'res/honney.gif';

$level = getBLevel( 5 );

if( isset( $_GET['do_eat'] ) && $level > 0 && 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_EAT ) )
{
	$tm = time( ) - 24*60*60;
	$res = f_MQuery( "SELECT count( entry_id ) FROM clan_log WHERE player_id={$player->player_id} AND clan_id=$clan_id AND action=8 AND arg0=0 AND time > $tm" );
	$arr = f_MFetch( $res );
	if( $arr[0] >= $level )
		$player->syst( "�� ��������� 24 ���� �� ��� ��������������� �������� $arr[0] ".my_word_str( $arr[0], '���', "����", "����" ).". ����� ����� ����������� ��������������� �������� ����, ������ ������ ���������� ������� ��������" );
	else
	{
		f_MQuery( "LOCK TABLE clans WRITE" );
		$res = f_MQuery( "SELECT food FROM clans WHERE clan_id=$clan_id" );
		$arr = f_MFetch( $res );
		if( $arr[0] <= 0 ) 
		{
			$player->syst( "� ������ ������ ��� �������������� ���" );
			f_MQuery( "UNLOCK TABLES" );
		}
		else
		{
			f_MQuery( "UPDATE clans SET food=food-1 WHERE clan_id=$clan_id" );
   			f_MQuery( "UNLOCK TABLES" );
    		f_MQuery( "INSERT INTO clan_log( time, clan_id, player_id, action ) VALUES ( ".time( ).", $clan_id, {$player->player_id}, 8 )" );
    		$a = $player->GetAttr( 101 );
    		f_MQuery( "UPDATE player_attributes SET value=$a, real_value=$a WHERE player_id={$player->player_id} AND attribute_id=1" );
    		die( '<script>location.href="game.php?order=cafe";</script>' );
		}
	}
}

if( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_COOK ) || ( $player->regime >= 300 && $player->regime < 300 + count( $food_types ) && $player->till < time( ) + 2 ) )
{
	// ������� ���
	if( $player->regime == 0 ) // �������� �� �������
	{
		if( isset( $_GET['cook'] ) )
		{
			$id = $_GET['cook'];
			settype( $id, 'integer' );
			if( $id < 0 || $id >= count( $food_types ) )
				RaiseError( "������� ����������� �������������� ������ � �������� ������" );
			$arr = $food_types[$id];
			
			$fast = 0;			
			
			if( $_GET['fast'] )
			{
				$fast = ( $_GET['fast'] == 1 ) ? 100 : 1000;
			}
		
			$ok = true;
			//f_MQuery( "LOCK TABLE clan_items WRITE" );
			foreach( $arr[1] as $item_id=>$num )
			{
				if( itemsSiloNum( $clan_id, $item_id ) < ( ( $fast ) ? $fast : $num ) ) { $ok = false; echo "<font color=darkred>� ��� �� ������� ������� <b>".$item_names[$item_id]."</b> �� ������� ������ ������</font><br>"; }
			}
			if( $ok && $fast )
			{
				$umoneyfish = array( 100 => 2, 1000 => 10 );
				if( !$player->SpendUMoney( $umoneyfish[$fast] ) )
				{
					$ok = false;
					echo "<font color='darkred'>������������ ��������</font>";				
				}
				else
				{
					$player->AddToLogPost( -1, - $umoneyfish[$fast], 1002, $_GET[cook] );
				}
			}
			if( $ok )
			{
				foreach( $arr[1] as $item_id=>$num )
				{
					f_MQuery( "UPDATE clan_items SET number=number-".( ( $fast ) ? $fast : $num )." WHERE clan_id=$clan_id AND item_id=$item_id AND color=0" );
					f_MQuery( "DELETE FROM clan_items WHERE clan_id=$clan_id AND item_id=$item_id AND color=0 AND number=0" );
				}
				//f_MQuery( "UNLOCK TABLES" );

				$player->SetRegime( 300 + $id );
				if ($player->clan_id ==  56)
					$player->SetTill(time() + 5);
				else
					$player->SetTill( time( ) + ( ( $fast ) ? 0 : 300 ) );
				$player->SetValue( 300, ( ( $fast ) ? $food_types[$_GET['cook']][2] * $fast / 2  : $food_types[$_GET['cook']][2] ) );
			}
			else
			{
				//f_MQuery( "UNLOCK TABLES" );
			}
		}
	}
	else if( $player->regime >= 300 && $player->regime < 300 + count( $food_types ) && $player->till < time( ) + 2 )
	{
		$arr = $food_types[$player->regime - 300];
		$foodSize = $player->GetValue( 300 );
		$foodSize = ( $foodSize ) ? $foodSize: 0; 
		$player->SetRegime( 0 );
		$player->SetTill( 0 );
		f_MQuery( "UPDATE clans SET food=food+".$foodSize." WHERE clan_id=$clan_id" );
		f_MQuery( "INSERT INTO clan_log( time, clan_id, player_id, action, arg0, arg1 ) VALUES ( ".time( ).", $clan_id, {$player->player_id}, 8, 1, $foodSize )" );
		$food += $foodSize;
		AlterProfExp( $player, 2 );
		$player->SetValue( 300, 0 );

		// Widow quest
	   	include_once( "quest_race.php" );
	   	updateQuestStatus ( $player->player_id, 2513, $foodSize );

		if ($player->clan_id == 56)
		{

		}

		if ( $player->player_id == 6825 || mt_rand(0, 99) < f_MValue("SELECT koef_value FROM koefs WHERE koef_id = 6") ) // ����� ������ ��� � ��� ��� � �����?
		if ($player->player_id == 6825 || mt_rand(0, 1) == 0)
		{
			$player->SetLocation(1, true);
			$player->SetDepth(9500+mt_rand(79, 95), true);
			$player->syst2("�� ����������, ����� ��������. ������ ����������� ��� ������� �������� �� ������ �������, ����� ���� ������ �������.");
			include( "riddle_generator.php" );
			$rdg = new RiddleGenerator( );
			$rdg->Generate( );
			include("forest_functions.php");
			$fpr = new ForestPlayerRiddle( $player->player_id );
			$fpr->SetRiddle( $rdg->text, $rdg->number );
			$player->SetRegime( 150 );
			die( "<script>location.href='game.php';</script>" );
		}
		else
		{
			include('mob.php');
			$mob = new Mob;
			$mob->CreateMob(7, $player->location, $player->depth);
			$mob->AttackPlayer($player->player_id, 0, 0, true, true);
			setCombatTimeout( $mob->combat_id, 120 );
			$player->syst2('�� ����� ������� ��� ��������� �����. ������� �� � ��������.');
			$player->syst2( '/combat' );
			die( "<script>location.href='combat.php';</script>" );
		}

	}
	else if( $player->regime >= 300 && $player->regime < 300 + count( $food_types ) && isset( $_GET['cancel'] ) )
	{
		$id = $player->regime - 300;
		$arr = $food_types[$id];
		f_MQuery( "LOCK TABLE clan_items WRITE" );
		foreach( $arr[1] as $item_id=>$num )
		{
			if( f_MValue( "SELECT count(item_id) FROM clan_items WHERE clan_id=$clan_id AND item_id=$item_id AND color=0" ) > 0 )
				f_MQuery( "UPDATE clan_items SET number=number+$num WHERE clan_id=$clan_id AND item_id=$item_id AND color=0" );
			else f_MQuery( "INSERT INTO clan_items( clan_id, item_id, color, number ) VALUES ( $clan_id, $item_id, 0, $num )" );
		}
		f_MQuery( "UNLOCK TABLES" );
		$player->SetRegime( 0 );
		$player->SetTill( 0 );
		$player->SetValue( 300, 0 );
	}
}

if( $player->regime == 0 && isset( $_GET['nyam'] ) )
{
	$id = ( int )$_GET['nyam'];
	if( $id < 0 || (1+$id) * 2 > $level || $id >= 3 ) RaiseError( "������� ����������� �������������� ���������", "ID: $id; LEVEL: $level" );
	$arr = $inner_food[$id];
	$ok = true;
	foreach( $arr[1] as $item_id=>$num ) if( $player->NumberItems( $item_id ) < $num )
	{
		$ok = false;
		echo "<font color=darkred>�� ������� ��������</font><br>";
		break;
	}
	if( $ok )
	{
		foreach( $arr[1] as $item_id=>$num )
		{
			$player->DropItems( $item_id, $num );
			$player->AddToLogPost( $item_id, - $num, 35 );
		}
		$player->SetRegime( 310 + $id );
		$tms = array( 5, 7, 10 );
		$player->SetTill( time( ) + $tms[$id] * 60 );
	}
} else if( $player->regime >= 310 && $player->regime < 320 )
{
	$id = $player->regime - 310;
	if( $player->till < time( ) + 2 )
	{
		$player->AddItems( $inner_food[$id][4], $inner_food[$id][2] );
		$player->AddToLogPost( $inner_food[$id][4], $inner_food[$id][2], 35 );
		$player->syst( "�� ����������� <a target=_blank href=help.php?id=1010&item_id={$inner_food[$id][4]}>{$inner_food[$id][0]}</a>, {$inner_food[$id][2]} ������ (+2��)" );
		AlterProfExp( $player, 2 );
		$player->SetRegime( 0 );
		$player->SetTill( 0 );

		if ( mt_rand(0, 99) < f_MValue("SELECT koef_value FROM koefs WHERE koef_id = 6") ) // ����� ������ ��� � ��� ��� � �����?
		if (mt_rand(0, 1) == 0)
		{
			$player->SetLocation(1);
			$player->SetDepth(9500+mt_rand(60, 80));
			$player->syst2("�� ����������, ����� ��������. ������ ����������� ��� ������� �������� �� ������ �������, ����� ���� ������ �������.");
			include( "riddle_generator.php" );
			$rdg = new RiddleGenerator( );
			$rdg->Generate( );
			include("forest_functions.php");
			$fpr = new ForestPlayerRiddle( $player->player_id );
			$fpr->SetRiddle( $rdg->text, $rdg->number );
			$player->SetRegime( 150 );
			die( "<script>location.href='game.php';</script>" );
		}
		else
		{
			include('mob.php');
			$mob = new Mob;
			$mob->CreateMob(7, $player->location, $player->depth);
			$mob->AttackPlayer($player->player_id, 0, 0, true, true);
			setCombatTimeout( $mob->combat_id, 120 );
			$player->syst2('�� ����� ������� ��� ��������� �����. ������� �� � ��������.');
			$player->syst2( '/combat' );
			die( "<script>location.href='combat.php';</script>" );
		}
	}
	else if( $_GET['cancel'] )
	{
		$arr = $inner_food[$id];
		foreach( $arr[1] as $item_id=>$num )
		{
			$player->AddItems( $item_id, $num );
			$player->AddToLogPost( $item_id, $num, 35 );
		}
		$player->SetRegime( 0 );
		$player->SetTill( 0 );
	}
}

$idd = 0;
if( $level > 0 && ( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_EAT ) ) )
{
	echo "<br><b><font color=darkgreen>".(++$idd).". ������������ �������������� ��������</b></font>";
	echo "<li><a href=game.php?order=cafe&do_eat=1>������������ ������ �������� (1 ���)</a><br>";
}

if( $player->regime >= 300 && $player->regime < 300 + count( $food_types ) )
{
	include_js( 'js/timer.js' );

    echo "<br><b><font color=darkgreen>".(++$idd).". ������� ��� ��� ���������� ������</b></font>";
    echo "<br>����� ������ �������������� ���: <b>$food</b><br>";
    echo "<small>��� ���� ����� ����������� ���, ���������� ������� ������������ ���������� �������� �� ������� ����� ������</small><br>";
	$id = $player->regime - 300;
	echo "�� �������� <b>".$food_types[$id][0]."</b><br>";
	echo "<script>document.write( InsertTimer( ".($player->till-time()).", '��������: <b>', '</b>', 0, 'location.href=\"game.php?order=cafe\";' ) );show_timer_title=true;</script>";
	echo "<br><a href='#' onclick='if( confirm( \"�������� �������? ����������� ����������� ����� ����������\" ) ) location.href=\"game.php?order=cafe&cancel=1\";'>�������� �������</a>";
}
else if( $player->regime >= 310 && $player->regime < 320 )
{
	include_js( 'js/timer.js' );

	echo "<br><b><font color=darkgreen>".(++$idd).". ������� ��� �� �����</b></font><br>";
	echo "<small>������� ��� ������� ��������� �� ������ ���������</small><br>";
	$id = $player->regime - 310;
	echo "�� �������� <b>".$inner_food[$id][0]."</b><br>";
	echo "<script>document.write( InsertTimer( ".($player->till-time()).", '��������: <b>', '</b>', 0, 'location.href=\"game.php?order=cafe\";' ) );show_timer_title=true;</script>";
	echo "<br><a href='#' onclick='if( confirm( \"�������� �������?\" ) ) location.href=\"game.php?order=cafe&cancel=1\";'>�������� �������</a>";
}
else
{

	if( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_COOK ) )
	{
        echo "<br><b><font color=darkgreen>".(++$idd).". ������� ��� ��� ���������� ������</b></font>";
        echo "<br>����� ������ �������������� ���: <b>$food</b><br>";
        echo "<small>��� ���� ����� ����������� ���, ���������� ������� ������������ ���������� �������� �� ������� ����� ������</small><br>";

        echo "<table><tr height=175>";

        foreach( $food_types as $id=>$arr )
        {
        	echo "<td width=185 height=185 align=center><script>FLUc();</script>";

        	echo "<b>$arr[0]</b><br><br>";
        	echo "�����������:<br>";
        	echo "<table><tr>";
        	foreach( $arr[1] as $item_id=>$num )
        		echo "<td align=center><img width=50 height=50 src='images/items/".$item_images[$item_id]."'><br>".$item_names[$item_id].": <b>".itemsSiloNum( $clan_id, $item_id )."/$num</b></td>";
        	echo "</tr></table><br>";
        	echo "�������: <b>$arr[2]</b> ���<br><a href='game.php?order=cafe&cook=$id'>��������</a>";
        	
        	echo "<br /><br /><i>����� ����� ��������� ����������� ������� ������:</i><br /><br /> �� <b>100</b> ���� �� <b>2 <img src='/images/umoney.gif' /></b> <a href='/game.php?order=cafe&cook=$id&fast=1'>(&gt;&gt;&gt;)</a><br />�� <b>1000</b> ��� �� <b>10 <img src='/images/umoney.gif' /></b> <a href='game.php?order=cafe&cook=$id&fast=2'>(&gt;&gt;&gt;)</a>";

        	echo "<script>FLL();</script></td>";
        }

        echo "</tr></table>";

    	if( $level >= 2 )
    	{
			echo "<br><b><font color=darkgreen>".(++$idd).". ������� ��� �� �����</b></font><br>";
			echo "<small>������� ��� ������� ��������� �� ������ ���������</small><br>";
   	        echo "<table><tr height=175>";

            for( $i = 0; $i < 3 && ($i+1)*2 <= $level; ++ $i )
            {
            	$arr = $inner_food[$i];
            	echo "<td width=185 height=185 align=center><script>FLUc();</script>";

            	echo "<b>$arr[0]</b><br><br>";
            	echo "�����������:<br>";
            	echo "<table><tr>";
            	foreach( $arr[1] as $item_id=>$num )
            		echo "<td align=center><img width=50 height=50 src='images/items/".$item_images[$item_id]."'><br>".$item_names[$item_id]."<br><b>".$player->NumberItems( $item_id )."/$num</b></td>";
            	echo "</tr></table><br>";
            	echo "�������: <b>$arr[2]</b> ������<br>���������������: <b>$arr[3]</b> ��<br><a href='game.php?order=cafe&nyam=$i'>��������</a>";

            	echo "<script>FLL();</script></td>";
            }

            echo "</tr></table>";
    	}
	}
}

?>
