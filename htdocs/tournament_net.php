<?

//include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "tournament.php" );

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">


<?

$id = $_GET['id'];
settype( $id, 'integer' );

f_MConnect( );

$player_ids = Array( );

$res = f_MQuery( "SELECT name, status, type FROM tournament_announcements WHERE tournament_id = $id" );

$arr = f_MFetch( $res );
if( !$arr ) die( "Нет такого турнира" );
//if( $arr['status'] < 4 ) die( "Турнир еще не начат" );


include_js( "js/clans.php" );
include_js( "js/ii.js" );
echo "<head><title>Сетка турнира \"$arr[name]\"</title></head>";

if( $arr['type'] == 2 )
{
?>
	<script src="/js/skin.js"></script>
	<script>
		function Show( id )
		{
			if( _( 'c' + id ).style.display == 'none' )
				_( 'c' + id ).style.display = '';
			else
				_( 'c' + id ).style.display = 'none';
		}
		
		function ShowStep( step_id, order_id )
		{
			/*el1 = document.getElementsByClassName( 'cs' + step_id );
			el2 = document.getElementsByClassName( 'cca' + order_id );
			el3 = document.getElementsByClassName( 'ccb' + order_id );
			for( i = 0; i < el1.length; i++ )
			{
				for( j = 0; j < el2.length; j++ )
					if( el1[i] == el2[j] )
					{
						el1[i].style.border = '2px solid darkgreen';
						return;
					}
				for( j = 0; j < el3.length; j++ )
					if( el1[i] == el3[j] )
					{
						el1[i].style.border = '2px solid darkgreen';
						return;
					}
			}*/
		}
		
		function _( id )
		{
			return document.getElementById( id );
		}
	</script>
	<style>
		table td { vertical-align: top; }
		.tContainer { padding:5px 10px 0px; }
	</style>
	<div id="tTitle" style="width:250px;margin:10px 0 0 -125px;position:absolute;left:50%;">
		<script>FLUl();</script>
		<center><b>Турнир "<?=$arr[name]?>"</b></center>
		<script>FLL();</script>
	</div>
	<div id="tPlayers" style="position:absolute;width:200px;margin:15px;top:35px;">
		<script>FLUl();</script>
		<div class="tContainer">
			<b>Участники:</b>
			<br><br>
			<?
				$team_str = array( );
				$team_id = array( );
				
				$res = f_MQuery( "SELECT * FROM tournament_group_bets WHERE tournament_id = ".$id );
				while( $arr = f_MFetch( $res ) )
				{
					++ $team_id[$arr['clan_id']];
					$team_str[$arr['bet_id']] = '<script>document.write( "<img src=/images/clans/"+clans['.
						$arr['clan_id'].'][1]+" border=0 width=18 height=13 style='."\'".'position:relative;top:2px;'."\'".
						'>");</script>' . " команда {$team_id[$arr[clan_id]]}";
						
					echo '<script>FLUl();</script>';
					$r = f_MQuery( "SELECT * FROM tournament_group_scores WHERE tournament_id = ".$id." and bet_id = ".$arr['bet_id'] );
					$toe = f_MFetch( $r );
/*					echo '<a href="#"onmouseover="ShowStep( '.$toe['cur_step'].' )" onmouseout="ShowStep( '.$toe['cur_step'].
						' )" onclick="Show( '.$arr['bet_id'].' )"><b><i><script>document.write( "<img src=/images/clans/"+clans['.
						$arr['clan_id'].'][1]+" border=0 width=18 height=13 style='."\'".'position:relative;top:2px;'."\'".
						'>");</script>&nbsp;Команда '.rome_number( $arr['bet_id'] ).'</a> ('.$toe['score'].')</i></b>';*/
					echo '<a href="#"onmouseover="ShowStep( '.$toe['cur_step'].' )" onmouseout="ShowStep( '.$toe['cur_step'].
						' )" onclick="Show( '.$arr['bet_id'].' )"><b>'.$team_str[$arr['bet_id']].'</b></a> ('.$toe['score'].')';

					echo '<div id="c'.$arr['bet_id'].'" style="display:none;margin:7px 0 0 11px">';
					for( $ti = 0; $ti < 6; ++ $ti )
					{
						if( $arr['slot_'.$ti] )
						{
    						$r = f_MQuery( "SELECT * FROM characters WHERE player_id = '".$arr['slot_'.$ti]."'" );
    						$toE = f_MFetch( $r );
    						echo '<script>document.write( ii( '.$toE['level'].', "'.$toE['login'].'","'.$toE['nick_clr'].'", '.$arr['clan_id'].' ) );</script><br>';
    					} else echo  "<i>Свободно</i><br>";
					}
					echo '</div>';
					echo '<script>FLL();</script><br>';
				}
			?>
		</div>
		<script>FLL();</script>
	</div>
	<div id="tCombats" style="position:absolute;width:500px;margin:15px;top:35px;left:210px;">
		<script>FLUl();</script>
		<div class="tContainer">
			<b>Этапы:</b><br><br>
			<?
				$res = f_MQuery( "SELECT * FROM tournament_group_assignments WHERE tournament_id = ".$id );
				while( $arr = f_MFetch( $res ) )
				{
					echo '<script>FLUl();</script>';
					echo '<table class="cca'.$arr['a'].' ccb'.$arr['b'].' cs'.$arr['step_id'].'" style="width:99%;">';
					echo '<tr>';
					echo '<td style="width:25px;"><b>'.rome_number( $arr['step_id'] ).'</b></td>';
					echo '<td style="width:300px;"><b><a href="#" onclick="Show( '.
							$arr['a'].' )"><b>'.$team_str[$arr['a']].'</a> : <a href="#" onclick="Show( '.
							$arr['b'].' )">'.$team_str[$arr['b']].'</a></b>';

					echo '<td style="text-align:right;'.( ( !$arr['combat_id_0'] ) ? 'color:#999;' : 'color:#000' ).'">';
					if( $arr['combat_id'] )
						echo '<a href="/combat_log.php?id='.$arr['combat_id'].'" target="_blank">Групповой бой</a>';
					else
						echo 'Групповой бой';
					echo ', личные бои: ';
					for( $ci = 0; $ci < 3; ++ $ci )
						if( $arr['combat_id_'.$ci] )
							echo '<a href="/combat_log.php?id='.$arr['combat_id_'.$ci].'" target="_blank">['.($ci + 1).']</a>&nbsp;';
						else
							echo '['.$ci.']&nbsp;';
					echo '</td>';
					echo '</tr>';
					echo '</table>';
					echo '<script>FLL();</script><br>';
				}
			?>
			
		</div>
		<script>FLL();</script>
	</div>
	<script>
		_( 'tCombats' ).style.width = screen.width - 240 + 'px';
	</script>
<?
}
else
{

    $res = f_MQuery( "SELECT * FROM tournament_players WHERE tournament_id = $id" );
    while( $arr = f_MFetch( $res ) ) $player_ids[] = $arr['player_id'];

    $tournament = new Tournament( $id, $player_ids );
    $tournament->LoadState( );
    echo $tournament->Render( );/**/
}
    
?>
