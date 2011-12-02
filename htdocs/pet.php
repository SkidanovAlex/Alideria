<?

function PetGetDescr( $arr )
{
	$clr = array( "blue", "green", "red" );
	$st = "<center><font color={$clr[$arr[genre]]}><b>{$arr[nick]}, {$arr[name]}</b></font><br>Уровень: {$arr[level]}<br>";
	$st .= "<table><tr><td>' + rFUlt() + '<img width=200 height=280 src=images/pets/{$arr[image]}.jpg>' + rFL() + '</td></tr></table></center>";
	return $st;
}

?>
