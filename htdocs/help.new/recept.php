<?  include( 'functions.php' ); ?>
<?  include( 'player.php' ); ?>
<?

f_MConnect( );
$player = new Player( 172 );
$stats = $player->getAllAttrNames( );

		$levels = Array( 3 => 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16 );
		$ranks = Array( 0 => 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );


  include( 'craft_functions.php' ); ?>

<div id="content_text"><br />
	<div id="header" align="left">Рецепты</div><br />
	<img  align="right" src="images/icons/recept.png" id="label"/>
	<p align="left">
		Чтобы произвести вещь, Вам нужно сначала приобрести рецепт. Каждый рецепт подскажет что нужно для того, чтобы сделать вещь, зелье, полуфабрикат, что угодно. Для этого могут понадобиться как ресурсы, так и уровень игрока, или же особые его характеристики. Изучайте рецепты внимательно. <br /><br />
		Мы советуем пользоваться поиском и тщательней выставлять нужные Вам параметры запроса. Рецептов очень много – легко можно потерять голову.<br /><br />
		<table border=0 id="s_table" style="color:000000;"><tbody>
		<form action="help.php" method=get>
		
		<input type=hidden name=id value=1015>
		<?
function create_select( $nm, $arr, $val )
{
	$st = "<select class=m_btn name='$nm' style='width:170px;'>";
	
	foreach( $arr as $key=>$value )
	{
		$st .= "<option value=$key";
		if( $key == $val ) $st .= " selected";
		$st .= ">$value" ;
	}
	
	$st .= '</select>';
	
	return $st;
}
		?>
		
		<? echo "<tr><td><b>Гильдия: </b></td><td>".create_select( "prof", $prof_names, 104 )."</td></tr>"; ?>
		<? echo "<tr><td><b>Мин.уровень: </b></td><td>".create_select( "minlevel", $levels, 3 )."</td></tr>"; ?>
		<? echo "<tr><td><b>Макс.уровень: </b></td><td>".create_select( "maxlevel", $levels, 16 )."</td></tr>"; ?>
		<? echo "<tr><td><b>Мин.ранг: </b></td><td>".create_select( "minrank", $ranks, 0 )."</td></tr>"; ?>
		<? echo "<tr><td><b>Макс.ранг: </b></td><td>".create_select( "maxrank", $ranks, 10 )."</td></tr>"; ?>

		<tr><td>&nbsp;</td><td>
		<input value=Показать type="submit" style="width:170px;
			background-color : #e0c3a0;
			border : 1px solid #000000;
			color: #000000;
			height: 20;"></td></tr></form>
		</tbody></table>
		<br /><br />
		Показать все рецепты гильдии:
		<table><tbody>
		<?
		$i = 0;
		foreach( $prof_names as $a=>$b )
		{
			if( $i%3 == 0 ) echo "<tr>";
			echo "<td><button onclick='location.href=\"help.php?id=1015&prof=$a\"' type=submit style=\"width:126px; height:28px; background:url(/images/buttom.png); border: 0;\">$b</button></td>";
			if( $i%3 == 2 ) echo "</tr>";
			++ $i;
		}
		while( $i%3 )
		{
			echo "<td>&nbsp;</td>";
			if( $i % 3 == 2 ) echo "</tr>";
			++ $i;
		}
		?>
		</tbody></table>
	</p>
</div>
