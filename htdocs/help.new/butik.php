<?

include( "arrays.php" );

?>


<div id="content_text"><br />
	<div id="header" align="left">Гардероб</div><br />
	<img  align="right" src="images/icons/cloth.png" id="label"/>
	<p align="left">
		Вещи – это неотъемлемый атрибут магов Алидерии: будь то готовые изделия, которые можно надеть и смело отправиться в бой или же это пока лишь ресурсы, полуфабрикаты, из которых в итоге сделают что-то мощное, крепкое, полезное.
		<br /><br />
		<table border=0 id="s_table" style="color:000000;"><tbody>
			<form action="help.php" method=get>
			<input type=hidden name=id value=1010>
			<tr><td><b>Тип: </b></td><td><select class=m_btn name='type' style="width:170px">
			<?
			foreach( $item_types as $a => $b ) echo "<option value=$a>$b";
			?>
			<option value=-1 selected>Любой</select></td></tr>
			<tr><td>
			<b>Мин.уровень: </b></td><td><select class=m_btn name='minlevel' style="width:170px" >
			<option value=1 selected>1
			<option value=2>2
			<option value=3>3
			<option value=4>4
			<option value=5>5
			<option value=6>6
			<option value=7>7
			<option value=8>8
			<option value=9>9
			<option value=10>10
			<option value=11>11
			<option value=12>12
			<option value=13>13
			<option value=14>14
			<option value=15>15
			<option value=16>16
			</select></td></tr><tr><td>
			<b>Макс.уровень: &nbsp </b></td><td>
			<select class=m_btn name='maxlevel' style="width:170px" >
			<option value=1>1
			<option value=2>2
			<option value=3>3
			<option value=4>4
			<option value=5>5
			<option value=6>6
			<option value=7>7
			<option value=8>8
			<option value=9>9
			<option value=10>10
			<option value=11>11
			<option value=12>12
			<option value=13>13
			<option value=14>14
			<option value=15>15
			<option value=16 selected>16
			</select></td></tr><tr><td></td><td>
			<input value=Показать type="submit" style="width:170px; background-color : #e0c3a0;	border : 1px solid #000000;	color: #000000;	height: 20;"></td></tr></form>
			</tbody></table>
		<br>Или же можете выбрать нужную Вам категорию:<br>
		Ресурсы:
		<table><tbody>
		<?
		$i = 0;
		foreach( $item_types2 as $a=>$b )
		{
			if( $i%5 == 0 ) echo "<tr>";
			echo "<td><button onclick='location.href=\"help.php?id=1010&type2=$a\"' type=submit style=\"width:126px; height:28px; background:url(/images/buttom.png); border: 0;\">$b</button></td>";
			if( $i%5 == 4 ) echo "</tr>";
			++ $i;
		}
		while( $i%5 )
		{
			echo "<td>&nbsp;</td>";
			if( $i % 5 == 4 ) echo "</tr>";
			++ $i;
		}
		?>
		</tbody></table>
		<br />Вещи:
		<table><tbody>
		<?
		$i = 0;
		foreach( $item_types as $a=>$b )
		{
			if( $i%5 == 0 ) echo "<tr>";
			echo "<td><button onclick='location.href=\"help.php?id=1010&type=$a\"' type=submit style=\"width:126px; height:28px; background:url(/images/buttom.png); border: 0;\">$b</button></td>";
			if( $i%5 == 4 ) echo "</tr>";
			++ $i;
		}
		while( $i%5 )
		{
			echo "<td>&nbsp;</td>";
			if( $i % 5 == 4 ) echo "</tr>";
			++ $i;
		}
		?>
		</tbody></table>
	</p>
</div>