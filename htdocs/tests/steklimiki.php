<?
/* @author = undefined
 * @date = 1 ����� 2011
 * @about = ��������� ����, ���������� �������� ���� ����������� 
 */
 
	// ���������� ���������-�����������
	require_once( '../functions.php' );
	require_once( '../player.php' );
	
	f_MConnect( );
?>
<script src="/js/ii_a.js"></script>
<h1>����������-�������������</h1>
<?
	// �������� ������ �� ���� �����������-��������������
	$steklodyvu = f_MQuery( 'SELECT player_id, rank FROM player_guilds WHERE guild_id = 107 ORDER BY rank DESC' );

	// ����������� ��������������	
	while( $steklodyv = f_MFetch( $steklodyvu ) )
	{
		$Steklodyv = new Player( $steklodyv['player_id'] );
		
		// ���� �� ������������� �������
		echo $Steklodyv->login.' -> '.$steklodyv['rank'].'; ';
		
		// ��������� ���������� � ���������, ���� �� ��� �� ���� ���
		if( !f_MValue( 'SELECT * FROM player_guilds WHERE player_id = '.$Steklodyv->player_id.' AND guild_id = 106' ) )
		{
			echo '������� �� � ���������, ��������� ��� ���� �������������� ������� ';
			
			// ���������, ����������
			 f_MQuery( 'INSERT INTO player_guilds( player_id, guild_id ) VALUES( '.$Steklodyv->player_id.', 106 )' );
			
			// ���������, ������� �� �� ��������
			if( !f_MValue( 'SELECT * FROM player_guilds WHERE player_id = '.$Steklodyv->player_id.' AND guild_id = 106' ) )
			{
				// ��������� �� ������
				echo '[<span style="color: darkred; font-weight: bold;">FIAL</span>]';			
			}
		}
		
		// �������� ��!
		 f_MQuery( 'DELETE FROM player_guilds WHERE player_id = '.$Steklodyv->player_id.' AND guild_id = 107' );
		
		// � ��� ������� � ������� �� ��������, ������� ����������� ����� � ������, ��� �� ����� ����������� �����
		$profExp = array( 500, 1500, 3500, 7500, 13500, 21500 );
		f_MQuery( 'UPDATE characters SET prof_exp = prof_exp + '.$profExp[$steklodyv['rank']].' WHERE player_id = '.$Steklodyv->player_id );
		echo ' ; ��������� <b>'.$profExp[$steklodyv['rank']].'</b> �� � ���������';
		
		// ��������� � ���������� ��������
		echo '<br />';
	}
?>