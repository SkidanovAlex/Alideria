<?
/* @author = undefined
 * @date = 1 марта 2011
 * @about = Временный файл, призванный ПОБЕДИТЬ ВСЕХ СТЕКЛОДУВОВ 
 */
 
	// Подключаем заголовки-заголовочки
	require_once( '../functions.php' );
	require_once( '../player.php' );
	
	f_MConnect( );
?>
<script src="/js/ii_a.js"></script>
<h1>Стеклодувы-стеклодувчики</h1>
<?
	// Получаем список из всех стеклодувов-стеклодувчиков
	$steklodyvu = f_MQuery( 'SELECT player_id, rank FROM player_guilds WHERE guild_id = 107 ORDER BY rank DESC' );

	// Обсчитываем стеклодувчиков	
	while( $steklodyv = f_MFetch( $steklodyvu ) )
	{
		$Steklodyv = new Player( $steklodyv['player_id'] );
		
		// Инфа об обсчитываемом играчке
		echo $Steklodyv->login.' -> '.$steklodyv['rank'].'; ';
		
		// Перевозим гражданина в Алхимиков, если он ещё не есть там
		if( !f_MValue( 'SELECT * FROM player_guilds WHERE player_id = '.$Steklodyv->player_id.' AND guild_id = 106' ) )
		{
			echo 'поциент не в Алхимиках, перевозим его туда насильственным образом ';
			
			// Перевозим, собственно
			 f_MQuery( 'INSERT INTO player_guilds( player_id, guild_id ) VALUES( '.$Steklodyv->player_id.', 106 )' );
			
			// Проверяем, успешно ли он добрался
			if( !f_MValue( 'SELECT * FROM player_guilds WHERE player_id = '.$Steklodyv->player_id.' AND guild_id = 106' ) )
			{
				// Потерялся по дороге
				echo '[<span style="color: darkred; font-weight: bold;">FIAL</span>]';			
			}
		}
		
		// Высираем же!
		 f_MQuery( 'DELETE FROM player_guilds WHERE player_id = '.$Steklodyv->player_id.' AND guild_id = 107' );
		
		// И тут зарубка о подарке ПО долбоёбам, которые прокачивали профу в гильде, где не нужно прокачивать профу
		$profExp = array( 500, 1500, 3500, 7500, 13500, 21500 );
		f_MQuery( 'UPDATE characters SET prof_exp = prof_exp + '.$profExp[$steklodyv['rank']].' WHERE player_id = '.$Steklodyv->player_id );
		echo ' ; начислено <b>'.$profExp[$steklodyv['rank']].'</b> ПО в копилочку';
		
		// Переходим к следующему поциенту
		echo '<br />';
	}
?>