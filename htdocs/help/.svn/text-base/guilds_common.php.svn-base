<?

include_once( 'skin.php' );
include_once( 'items.php' );
include_once( 'arrays.php' );

function ShowGuildRating( $guild_id )
{
	$res = f_MQuery( "SELECT * FROM player_guilds WHERE guild_id=$guild_id" );
	echo "Количество игроков в гильдии: <b>".f_MNum( $res ) . "</b>";
}

function ShowGuildItems( $guild_id )
{
	global $item_types;
	global $item_types2;
	$res = f_MQuery( "SELECT items.*, lake_items.rank FROM lake_items, items WHERE guild_id=$guild_id AND items.item_id=lake_items.item_id ORDER BY rank" );
	echo "<table><tr><td>";
	ScrollLightTableStart( );
	echo "<table>";
	$first = true;
	while( $arr = f_MFetch( $res ) )
	{
		if( !$first ) echo "<tr><td colspan=3><hr></td></tr>";
		$first = false;
		echo "<tr>";
		echo "<td valign=top width=150 align=center><img border=0 src='../images/items/$arr[image]'><br><a href=help.php?id=1010&item_id=$arr[item_id]>$arr[name]</a></td>";
		echo "<td valign=top align=left>";
		echo "<b>Тип: </b>{$item_types[$arr[type]]}<br>";
		if( $arr['type'] == 0 ) echo "<b>Подтип: </b> {$item_types2[$arr[type2]]}<br>";
		if( $arr[level] ) echo "<b>Уровень: </b>$arr[level]<br>";
		echo "<b>Вес: </b>".($arr[weight])/100.0."<br>";
		echo "<b>Гос.Цена: </b>".($arr[price])."<br><br>";
		echo itemDescr( $arr );
		echo "<img src=/images/empty.gif width=150 height=0>";
		echo "</td><td align=center valign=top><b>Требует ранг<br><font size=+3>$arr[rank]</font></b></td></tr>";
	}
	echo "</table>";
	ScrollLightTableEnd( );
	echo "</td></tr></table>";
}                   

function ShowKopkaText( )
{
echo "Это основная работа в гильдии: Вы <b>запускаете ходку и ждёте 10 минут</b>, после чего получаете результат. Плюсы - можно <b>не так часто отвлекаться</b> на игру (особенно полезно, <b>если Вы заняты в реальном мире</b>); минусы - такая работа в гильдии довольно&nbsp;нудная.<br />Если вы считаете, что ждать 10 минут слишком долго, вы можете остановить работу минимум через 30 секунд после её начала - и при этом даже что-то добыть! Но не думайте, что так вы сможете добыть что-то очень ценное или что вы получите какой-либо опыт, ничего не выловив за это короткое время...";
}

function ShowCraftText( $a, $b )
{
echo "Для создания новых вещей Вам не помешает изучить несколько рецептов, которые продаются непосредственно на месте работы. Достаточно приобрести <b>один рецепт для создания нужной вещи</b> - Вы его запомните на всю игровую жизнь. Сам по себе процесс работы прост: Вам нужны ресурсы, рецепт, навыки&nbsp;и&nbsp;время. <br /><i><b>Примечание:</b> Все рецепты гильдии можно посмотреть в&nbsp;<a href='help.php?id=$b'>соответствующем разделе&nbsp;Помощи</a>.</i>";
}

function ShowRepairText( )
{
echo "Чинить можно любую вещь, прочность которой <b>ниже максимальной хотя бы на два пункта (8 из 10, 7 из 9 и так далее)</b>. Следует помнить, что при починке <b>максимальная прочность вещи падает на&nbsp;1&nbsp;пункт.</b>";
}


function ShowProfExpText( $for_what )
{
	echo "За $for_what вы будете получать <b>Профессиональный Опыт</b>, который потом можно будет использовать для получения новых преимуществ в гильдиях - как в этой, так и в&nbsp;любой&nbsp;другой.<br>";
}


function ShowGuildOrdersText( $which="пополнения складов Торгового Дома", $res="вещей из этой гильдии" )
{
	echo "Для $which Городская Управа организовала для гильдии выдачу государственных заказов на изготовление {$res}. Поговорив с чиновником по своей профессии в Зале Гильдий, Вы можете получить государственный заказ на создание вещей или ресурсов.<br> Заказ даётся <b>без ресурсов на создание вещей</b>. <b>Срок выполнения заказа не ограничен</b>: Вы можете принести созданные вещи в любой момент. Не забывайте, что Управа решила щедро награждать тех ремесленников, которые помогают ей - <b>за выполенную работу вы получите вполовину больше гос. цены сделанных вещей.</b><br> <b><font color=\"blue\">Вы обязаны выполнить заказ самостоятельно: купленные у других людей вещи или ресурсы не&nbsp;пойдут в&nbsp;счёт&nbsp;заказа!</font></b><br><br>";
}

function RecipesLink( $guild_id )
{
	echo "<div style='margin: 5px 0 10px 20px;'><a href='help.php?id=1015&prof=" . $guild_id . "'>";
	echo "<img src='/help/etc/help-showrecipesclick-" . $guild_id . ".gif' alt='Просмотреть рецепты...' title='Просмотреть рецепты для этой гильдии!' style='border:0;' />";
	echo "</a></div>";
}

?>

