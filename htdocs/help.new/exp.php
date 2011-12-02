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

foreach( $exp_table as $a=>$b ) if( $a <= 15 ) print( "<tr><td align=right>".($a+1)."</td><td align=right>".moo( $b )."</td></tr>" );
?>
		</tbody></table>
		<table width="300" style="float:left" id="s_table" align=center><tbody><tr align="left"><td></td><td>
Опыт - определяющий параметр игроков. <br/>
Набирая опыт в боях, игрок совершенствуется,
становится сильнее, его уровень растет,
перед ним открываются новые возможности,
новые горизонты. <br/>
<br/>
Процесс набора опыта можно ускорить, если 
у Вас нету так много времени на любимую игру.
Для это Вам следует купить Премиум у Фавна,
который позволит получать максимум и ещё 
немножко от Ваших сражений.<br/>
<br/>
Есть и другие способы получения опыта: квесты и
клановые стройки. Не очень много, но все же хоть 
что-то. К тому же если Вы пацифист, то эти методы
исключительно для Вас. Но это способ явно 
медленнее.<br/>
<br/>
		</td></tr></tbody></table>
	<div id="buttons" align="right"><a href="/help.php?id=16002" class="rollover"></a></div>
	</p>
</div>
