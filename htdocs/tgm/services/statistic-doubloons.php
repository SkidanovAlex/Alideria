<?
/* @author = undefined
 * @date = 16 ������� 2011
 * @about = ���������� �� ����� ������� �������, ���� ������ ���
 */
?>
<script src='/js/clans.php'></script>
<script src='/js/ii_a.js'></script>
<table>
	<tbody>
	<?
		$riches = f_MQuery( 'SELECT player_id FROM `characters` ORDER BY money DESC LIMIT 25' );
		
		while( $rich = f_MFetch( $riches ) )
		{
			$Rich = new Player( $rich['player_id'] );
			
			echo '<tr><td><script>document.write( '.$Rich->Nick( ).' )</script></td><td><img src="/images/money.gif" /> '.$Rich->money.'</td><td><img src="/images/umoney.gif" /> '.$Rich->umoney.'</td></tr>';
		}
	?>
	</tbody>
</table>