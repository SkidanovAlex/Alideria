<?
require_once("time_functions.php");


include ("functions.php");
include ("player.php");

f_MConnect( );

f_MQuery( "DELETE FROM forest_monster_camps WHERE expires < ".time()." AND combat_id<=0" );


/* ������ � ������ ����� */
$lo_time = f_MValue( 'SELECT `time` FROM `lo` WHERE `time` < '.time().' AND `status` = 0' );
if( false && $lo_time )
{
	glashSay( "������ ��������� ����! �������, � ���� �������� ����!" );
	f_MQuery( 'UPDATE `lo` SET `status` = 1' );
}
/* !������ � ������ ����� */
//die();
//if (mt_rand(0, 2) == 2)
for ($i=0;$i<-1 ;$i++ )
{
	$mob_id=$i+70;
	if ($mob_id==70) $nm = "���������� ����������";
	if ($mob_id==71) $nm = "���������� ��������";
	if ($mob_id==72) $nm = "���������� �����������";
	if ($mob_id==73) $nm = "���������� ���������";
	if ($mob_id==74) $nm = "���������� �����������";

	$arr = f_MFetch(f_MQuery("SELECT * FROM forest_monster_camps WHERE mob_id=".$mob_id));
	if ($arr)
	{
		$min_level = $arr['min_level'];
		$max_level = $arr['max_level'];
		
		
		$cell_id = $arr['cell_id'];

		$x = 50 + (int)($cell_id / 100);
		$x %= 100;
		$y = $cell_id % 100;

	    	$st = "";
		if ($arr['combat_id']) $st = " ��� ���� ����� ������! <a target=_blank href=combat_log.php?id=".$arr['combat_id'].">��������</a>";
	    	if (((int)date("i")) % 30 == 0) glashSay( "�������� ������� <b>$min_level - $max_level</b> �������! �� ���������� �������� �����, � ���� �� ����������� <b>$x.$y</b> ���������� ������ <b>$nm</b>. ��������� ������ ������ ��� ��������������� � ��������� ������� �� ��������� �����������.$st" );

	if( $arr['strazha_helper'] > 0 && time( ) - $arr['strazha_helper'] > 1 * 60 )
	{
		$attach_whom = f_MValue( "SELECT player_id FROM combat_players WHERE combat_id=".$arr['combat_id']." AND side=1" );
		include_once( "mob.php" );
		
		$side0 = f_MValue( "SELECT count( player_id ) FROM combat_players WHERE combat_id=".$arr['combat_id']." AND side=0 AND ready < 2" );
		$side1 = f_MValue( "SELECT count( player_id ) FROM combat_players WHERE combat_id=".$arr['combat_id']." AND side=1 AND ready < 2" );
		if ($side1>$side0+5) $side1 = $side0 + 5;
		if( $side0 > 0 )
		{
			f_MQuery( "UPDATE forest_monster_camps SET strazha_helper = -1 WHERE mob_id=".$mob_id );
			for( $i = 0; $i < $side1 - $side0; ++ $i )
			{
    			$mob = new Mob;
				$mob->CreateDungeonMob( $min_level, 2, $min_level, $min_level, $min_level, 1, 10000, "�������", false );
				$player = new Player( $attach_whom );
    			$mob->AttackPlayer( $player->player_id, 6, 1, false );
			}
		}
	}
	}
	else
	{
		/*if ($mob_id==70 || $mob_id==71) $l=13;
		if ($mob_id==72 || $mob_id==73) $l=4;
		if ($mob_id==74) $l=2;
		$cell_id=f_MValue("SELECT depth FROM forest_tiles WHERE tile=$l ORDER BY RAND() LIMIT 1");
		*/
		
		if ($mob_id==70) $cell_id=8704;
		if ($mob_id==71) $cell_id=8686;
		if ($mob_id==72) $cell_id=7008;
		if ($mob_id==73) $cell_id=7695;
		if ($mob_id==74) $cell_id=458;

		$expires = time() + 6 * 3600;
		$x = 50 + (int)($cell_id / 100);
		$x %= 100;
		$y = $cell_id % 100;
		if ($mob_id==70)	{$min_level=3; $max_level=6;}
		if ($mob_id==71)	{$min_level=7; $max_level=10;}
		if ($mob_id==72)	{$min_level=11; $max_level=14;}
		if ($mob_id==73)	{$min_level=15; $max_level=18;}
		if ($mob_id==74)	{$min_level=19; $max_level=25;}
		f_MQuery( "INSERT INTO forest_monster_camps (cell_id, mob_id, min_level, max_level, expires, strazha_helper) VALUES ($cell_id, $mob_id, $min_level, $max_level, $expires, 0)" );
		if (((int)date("i")) % 30 == 0) glashSay( "�������� ������� <b>$min_level - $max_level</b> �������! �� ���������� �������� �����, � ���� �� ����������� <b>$x.$y</b> ���������� ������ <b>$nm</b>. ��������� ������ ������ ��� ��������������� � ��������� ������� �� ��������� �����������." );
	}
}

//die();

if ($arr = f_MFetch(f_MQuery("SELECT * FROM forest_monster_camps ORDER BY rand() LIMIT 1")))
{
	$mob_id = $arr['mob_id'];	
	$min_level = $arr['min_level'];
	$max_level = $arr['max_level'];
	if( mt_rand( 1,3 ) == 1 )
	{
    	if ($mob_id == 11) $nm = "����������� ��������";
    	if ($mob_id == 13) $nm = "������������� ������";
    	if ($mob_id == 35) $nm = "�����";
    	if ($mob_id == 36) $nm = "������-�������";
	if ($mob_id == 53) $nm = "���������";
    	$cell_id = $arr['cell_id'];
    	
    	$x = 50 + (int)($cell_id / 100);
    	$x %= 100;
    	$y = $cell_id % 100;
    	
    	$st = "";
    	if ($arr['combat_id']) $st = " ��� ���� ����� ������! <a target=_blank href=combat_log.php?id=$arr[combat_id]>��������</a>";
    	glashSay( "�������� ������� <b>$min_level - $max_level</b> �������! �� ���������� �������� �����, � ���� �� ����������� <b>$x.$y</b> ���������� ������ <b>$nm</b>. ��������� ������ ������ ��� ��������������� � ��������� ������� �� ��������� �����������.$st" );
	}
	
	if( $arr['strazha_helper'] > 0 && time( ) - $arr['strazha_helper'] > 9 * 60 )
	{
		f_MQuery( "UPDATE forest_monster_camps SET strazha_helper = -1" );
		$attach_whom = f_MValue( "SELECT player_id FROM combat_players WHERE combat_id={$arr[combat_id]} AND side=1" );
		include_once( "mob.php" );
		
		$side0 = f_MValue( "SELECT count( player_id ) FROM combat_players WHERE combat_id={$arr[combat_id]} AND side=0 AND ready < 2" );
		$side1 = f_MValue( "SELECT count( player_id ) FROM combat_players WHERE combat_id={$arr[combat_id]} AND side=1 AND ready < 2" );
		if( $side0 > 0 )
		{
			for( $i = 0; $i < $side1 - $side0; ++ $i )
			{
    			$mob = new Mob;
				$mob->CreateDungeonMob( $min_level, 2, $min_level, $min_level, $min_level, 1, 10000, "�������", false );
				$player = new Player( $attach_whom );
    			$mob->AttackPlayer( $player->player_id, 6, 1, true );
			}
		}
	}
	
	die ();
}

$rnd = mt_rand(0,15);

//$rnd = 14;

if ($rnd == 14 && ( (int)date("H") >= 9 && (int)date("H") <= 24 ) )
{
	while (true)
	{
		$x = (mt_rand(25,34)+50)%100;
		$y = (mt_rand(-10,10)+100)%100;
		$cell_id = $x * 100 + $y;
		if (!f_MValue("SELECT count(tile) FROM forest_tiles WHERE location = 1 AND depth = $cell_id") && 
		    !f_MValue("SELECT count(cell_id) FROM forest_monster_camps WHERE cell_id = $cell_id")) break;
	}
	$kind = mt_rand(0,4);
	if( mt_rand( 1,2 ) == 1 ) $kind = min( $kind, mt_rand(0,4) );
//$kind = 4;
	if ($kind == 0)
	{
		$mob_id = 11;
		$min_level = 4;
		$max_level = 7;
		$nm = "����������� ��������";
	}
	else if ($kind == 1)
	{
		$mob_id = 13;
		$min_level = 6;
		$max_level = 11;
		$nm = "������������� ������";
	}
	else if ($kind == 2)
	{
		$mob_id = 35;
		$min_level = 10;
		$max_level = 15;
		$nm = "�����";
	}
	else if ($kind == 3)
	{
		$mob_id = 36;
		$min_level = 13;
		$max_level = 18;
		$nm = "������-�������";
	}
	else if ($kind == 4)
	{
		$mob_id = 53;
		$min_level = 17;
		$max_level = 25;
		$nm = "���������";
	}
	else die ();
	
	$expires = time() + 6 * 3600;
	$x = 50 + (int)($cell_id / 100);
	$x %= 100;
	$y = $cell_id % 100;
	f_MQuery( "INSERT INTO forest_monster_camps (cell_id, mob_id, min_level, max_level, expires) VALUES ($cell_id, $mob_id, $min_level, $max_level, $expires)" );
	glashSay( "�������� ������� <b>$min_level - $max_level</b> �������! �� ���������� �������� �����, � ���� �� ����������� <b>$x.$y</b> ���������� ������ <b>$nm</b>. ��������� ������ ������ ��� ��������������� � ��������� ������� �� ��������� �����������." );
} else echo $rnd;

?>
