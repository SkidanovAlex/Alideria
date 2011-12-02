	<div id="content_text"><br />
	<div id="header" align="left">Повареная книга или фокусы и прочие шалости</div><br />
	<img  align="right" src="images/icons/tricks.png" id="label"/>
	<p align="left">
		В игре есть четыре вида заклинаний: заклинания воды, природы, огня и нейтральные заклинания. Первые три вида – боевые заклинания, нейтральные же – заклинаниядля повседневное жизни, они что-то куда-то превращают, улучшают, изменяют. Такие заклинания характеризуются длительной перезарядкой (после применения заклинания приходится на самом деле долго ожидать, когда можно будет воспользоваться снова).<br /><br />
		<table border=0 id="s_table" style="color:000000;"><tbody>
			<form action="help.php" method=get>
			<input type=hidden name=id value=1011>
			<tr><td><b>Стихия: </b></td><td><select class=m_btn name='genre' style="width:170px">
			<option value=0>Вода
			<option value=1>Природа
			<option value=2>Огонь
			<option value=-1 selected>Любая</select></td></tr>
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
			<input value=Показать type=submit style="width:170px; background-color : #e0c3a0; border : 1px solid #000000; color: #000000; height: 20;"></td></tr></form>
		</tbody></table><br />
		<br>Или же можете посмотреть все заклинания одной стихии:<br /><br />
		<table><tbody>
			<tr><td><button onclick='location.href="help.php?id=1011&genre=0"' type=submit style="width:126px; height:28px; background:url(/images/buttom.png); border: 0;">Вода</button></td>
			    <td><button onclick='location.href="help.php?id=1011&genre=1"' type=submit style="width:126px; height:28px; background:url(/images/buttom.png); border: 0;">Природа</button></td>
			    <td><button onclick='location.href="help.php?id=1011&genre=2"' type=submit style="width:126px; height:28px; background:url(/images/buttom.png); border: 0;">Огонь</button></td></tr>
			<tr><td>&nbsp;</td>
			    <td><button onclick='location.href="help.php?id=1011&genre=3"' type=submit style="width:126px; height:28px; background:url(/images/buttom.png); border: 0;">Нейтральная</button></td>
			    <td>&nbsp;</td></tr>
		</tbody></table>
	</p>
</div>