<?
/* @author = undefined, Ishamael
 * @date = 6 октября 2013
 * @about = Статистика онлайна игроков
*/
?>

<script src="/js/jquery/charts/jquery.jqplot.min.js"></script>
<script src="/js/jquery/charts/jqplot.canvasTextRenderer.min.js"></script>
<script src="/js/jquery/charts/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script src="/js/jquery/charts/jqplot.dateAxisRenderer.min.js"></script>
<script src="/js/jquery/charts/jqplot.highlighter.min.js"></script>
<script src="/js/jquery/charts/jqplot.pointLabels.min.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery/charts/jquery.jqplot.min.css" />

<script src="/js/jquery/datepicker/datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery/datepicker/datepicker.css" />


<div style="width: 96%; margin-left: 2%;">
	<div id="chart"></div>
	<div id="datepicker" style="float: right; margin: 10px 9px 16px 0px;"></div>
</div>

<?
	// last 30 days
	$secsInDay = 60 * 60 * 24;
	$begin = mktime( 0, 0, 0, date( 'n' ), date( 'j' ) ) - 20 * $secsInDay;
	$end = mktime( 0, 0, 0, date( 'n' ), date( 'j' ) + 1 );
?>

<script>
	function refresh( )
	{
		var begin = $( '#datepicker' ).DatePickerGetDate( true )[0].split( '-' ).reverse( ).join( '-' );
		var end = $( '#datepicker' ).DatePickerGetDate( true )[1].split( '-' ).reverse( ).join( '-' );

		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'begin=' + begin + '&end=' + end,
			success: function( Answer ){ eval( Answer ) },
			error: function( Answer ){ alert( 'Хьюстон, у нас проблема: ' + Answer.statusText + ' [' + Answer.status + ']' ) }
		});		
	}

	$( document ).ready( function()
	{
		$( '#datepicker').DatePicker(
		{
			flat: true,
			date: [ '<?=date( "Y-m-d", $begin )?>', '<?=date( "Y-m-d", $end )?>' ],
			current: '<?=date( "Y-m-d", $end )?>',
			calendars: 1,
			mode: 'range',
			starts: 1,
			onChange: refresh
		});
		
		refresh( );
	} );
</script>
