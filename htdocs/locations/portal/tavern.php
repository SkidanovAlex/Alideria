<?

if( !isset( $mid_php ) ) die( );

$havkas = Array (
	Array( "Каша", 45, 50, 'кашу' ),
	Array( "Жаркое", 120, 180, "жаркое" ),
	Array( "Обед путешественника", 350, 600, "обед путешественника" )
);

if( isset( $_GET['eat'] ) && $player->regime == 0 )
{
	$id = $_GET['eat'];
	settype( $id, 'integer' );
	if( $id < 0 || $id >= count( $havkas ) ) RaiseError( "Попытка съесть отбивную из крысиных хвостиков!", "$id" );

	if( !$player->SpendMoney( $havkas[$id][1] ) )
		$player->syst( 'У вас недостаточно дублонов' );
	else
	{
		$player->AddToLogPost( 0, -$havkas[$id][1], 11 );
		$player->syst( "Вы скушали <b>{$havkas[$id][3]}</b> и восстановили <b>{$havkas[$id][2]}</b> единиц здоровья" );
		$player->AlterRealAttrib( 1, $havkas[$id][2] );
	}
}


echo "<table><tr><td>";
ScrollLightTableStart( );
echo "<table><tr><td>&nbsp;</td><td height=100%>".GetScrollTableStart( )."<b>Стоимость</b>".GetScrollTableEnd( )."</td><td height=100%>".GetScrollTableStart( )."<b>Восстанавливает здоровья</b>".GetScrollTableEnd( )."</td><td>&nbsp;</td></tr>";
foreach( $havkas as $id=>$arr )
{
	echo "<tr><td height=100%>".GetScrollTableStart( 'left' )."$arr[0]".GetScrollTableEnd( )."</td><td height=100%>".GetScrollTableStart( 'right' )."$arr[1] <img width=11 height=11 border=0 src='images/money.gif'>".GetScrollTableEnd( )."</td><td height=100%>".GetScrollTableStart( 'right' )."$arr[2]".GetScrollTableEnd( )."</td><td>".GetScrollTableStart( )."<a href=game.php?eat=$id>Кушать</a>".GetScrollTableEnd( )."</td></tr>";
}
echo "</table>";
ScrollLightTableEnd( );
echo "</td></tr></table>";
echo "<br>";

?>
