<div id="content_text"><br />
	<div id="header" align="left">Заголовок</div><br />
	<img  align="right" src="images/icons/exp.png" id="label"/>
	<p align="left">
		<table id="s_table" style="float:left;" width="200" border=1><tbody>
			<tr><th>Уровень</th><th>Опыт</th></tr>
<?			
include_once( 'arrays.php' );

function moo( $a )
{
	$res = "";
	while( $a >= 1000 )
	{
		$res = ( ( $a ) % 10 ) . $res;
		$a /= 10;
		settype( $a, 'integer' );
		$res = ( ( $a ) % 10 ) . $res;
		$a /= 10;
		settype( $a, 'integer' );
		$res = ( ( $a ) % 10 ) . $res;
		$a /= 10;
		settype( $a, 'integer' );
		$res = " ".$res;
	}
	$res = $a.$res;
	return $res;
}

foreach( $exp_table as $a=>$b ) if( $a > 15 ) print( "<tr><td align=right>".($a+1)."</td><td align=right>".moo( $b )."</td></tr>" );
?>
		</tbody></table>
		<table width="300" style="float:left" id="s_table" align=center><tbody><tr align="left"><td></td><td>
Внимание ! Уровень Ваш уровень и текущий опыт Вы можете увидеть в
строке заголовка Вашего браузера. Это удобно, хоть и немножко
непривычно. Все же если Вам не нравится это, Вы можете смело изменить
текст (который отображается в браузере) на любой, который Вам нравится.
Для этого нужно воспользоваться вкладкой “Дневник”.<br/>
<br/>
<br/>

<i>Три пути ведут к знанию: путь размышления — это путь самый 
благородный, путь подражания — это путь самый легкий и путь опыта — 
это путь самый горький.</i><br> - Конфуций
		</td></tr></tbody></table>
	<div id="buttons" align="right"><a href="/help.php?id=16001" class="rolloverup"></a></div>
	</p>
</div>
