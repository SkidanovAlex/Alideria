<?

include_once( "items.php" );

function achieveStone( $id )
{
	global $player;
	$arr = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$id" ) );
	$player->AddToLog( $id, 1, 13 );
	$player->AddItems( $id );
	echo "document.getElementById( 'stone_msg' ).innerHTML = 'Вы нашли <b>$arr[0]</b>.';";
}

function getFieldItemImg( $id )
{
	$res = f_MQuery( "SELECT image, image_large FROM items WHERE item_id=$id" );
	return "'<img width=50 height=50 border=0 src=images/items/".itemImage( f_MFetch( $res ) ).">'";
}

function getField( $mask )
{
	global $arr;
	$field = Array( 1, 1, 1, 1, 1, 1, 1, 1, 1 );
	$field[$arr['coord0']] = getFieldItemImg( $arr['item_id0'] );
	$field[$arr['coord1']] = getFieldItemImg( $arr['item_id1'] );
	$field[$arr['coord2']] = getFieldItemImg( $arr['item_id2'] );

	for( $i = 0; $i < 9; ++ $i )
		if( ( $arr['mask'] & ( 1 << $i ) ) == 0 ) 
			$field[$i] = 0;

	$st = "field = [";
	for( $i = 0; $i < 9; ++ $i )
	{
		if( $i ) $st .= ",";
		$st .= $field[$i];
	}
	$st .= "];\nrender();\n";
	return $st;
}

if( !$mid_php )
{
	header("Content-type: text/html; charset=windows-1251");

    include_once( "functions.php" );
    include_once( "player.php" );

    f_MConnect( );

    if( !check_cookie( ) )
    	die( "Неверные настройки Cookie" );

    $player = new Player( $HTTP_COOKIE_VARS['c_id'] );
    $has_401 = $player->HasTrigger( 401 );

    f_MQuery( "LOCK TABLE player_stone_table WRITE" );
    $res = f_MQuery( "SELECT * FROM player_stone_table WHERE player_id={$player->player_id}" );
    $arr = f_MFetch( $res );
    if( !$arr ) 
    {
    	f_MQuery( "UNLOCK TABLES" );
    	$player->SetRegime( 0 );
    	RaiseError( "Игрок ищет камни, но записи об их расположении нет в БД", "ajax - то есть игрок мог руками вызвать скрипт" );
    }
	$id = $HTTP_RAW_POST_DATA;
	settype( $id, 'integer' );
	if( $id < 0 || $id >= 9 ) RaiseError( "При поиске камней выбрана несуществующая клетка", "$id" );

	if( $arr['mask'] & ( 1 << $id ) ) die( );
	$arr['mask'] |= ( 1 << $id );

	$num = 0;
	for( $i = 0; $i < 9; ++ $i )
		if( $arr['mask'] & ( 1 << $i ) )
			++ $num;
   	if( $num == 4 || $num == 3 && !$has_401 ) $arr['mask'] = ( 1 << 9 ) - 1;

	f_MQuery( "UPDATE player_stone_table SET mask=$arr[mask] WHERE player_id={$player->player_id}" );

	f_MQuery( "UNLOCK TABLES" );

	if( $arr['coord0'] == $id )
	{
		if ($player->HasTrigger(13105) && $arr['item_id0']==479)
		{
			$arr['item_id0']=79571;
			f_MQuery( "UPDATE player_stone_table SET item_id0=79571 WHERE player_id={$player->player_id}" );
			$player->SetTrigger(13105, 0);
			$player->SetTrigger(13110);
		}
		achieveStone( $arr['item_id0'] );
	}
	else if( $arr['coord1'] == $id )
	{
		if ($player->HasTrigger(13105) && $arr['item_id1']==479)
		{
			$arr['item_id1']=79571;
			f_MQuery( "UPDATE player_stone_table SET item_id1=79571 WHERE player_id={$player->player_id}" );
			$player->SetTrigger(13105, 0);
			$player->SetTrigger(13110);
		}
		achieveStone( $arr['item_id1'] );
	}
	else if( $arr['coord2'] == $id )
	{
		if ($player->HasTrigger(13105) && $arr['item_id2']==479)
		{
			$arr['item_id2']=79571;
			f_MQuery( "UPDATE player_stone_table SET item_id2=79571 WHERE player_id={$player->player_id}" );
			$player->SetTrigger(13105, 0);
			$player->SetTrigger(13110);
		}
		achieveStone( $arr['item_id2'] );
	}
	else echo "document.getElementById( 'stone_msg' ).innerHTML = 'Похоже, что тут камня не было.';";
}

$res = f_MQuery( "SELECT * FROM player_stone_table WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$mid_php )
{
	echo getField( $arr['mask'] );
	die( );
}

if( isset( $_GET['leave'] ) )
{
	$player->SetRegime( 0, true );
	f_MQuery( "DELETE FROM player_stone_table WHERE player_id={$player->player_id}" );
	die( "<script>location.href='game.php';</script>" );
}

echo "<script>\n";

echo "var UL = '".AddSlashes( GetScrollLightTableStart2('center') )."';\n";
echo "var FL = '".AddSlashes( GetScrollLightTableEnd() )."';\n";
echo "var UD = '".AddSlashes( GetScrollTableStart('center','middle') )."';\n";
echo "var FD = '".AddSlashes( GetScrollTableEnd() )."';\n";

echo "</script>\n";

?>

<center><b>Поиск Камней</b><br>
После долгих поисков вы почувствовали, что где-то здесь, рядом с вами, лежат три камня.<br>
К сожалению, из-за слабого освещения камней не видно, и искать их придется вслепую.<br>
При этом вы чувствуете, что не вы одни находитесь здесь: неподалёку слышен писк пещерных крыс, которые любят издеваться над искателями приключений.<br>
Перед вами девять зон. Вы успеете обыскать три из них, прежде чем камни окажутся раскиданными по пещере.<br>
<br>
<div id=stones>&nbsp;</div>
<div id=stone_msg>&nbsp;</div>
<div id=leave_msg>&nbsp;</div>

<script>

var field;
function render( )
{
	var st = '<table cellspacing=0 cellpadding=0 border=0>';
	var num = 0;
	for( var i = 0; i < 3; ++ i )
	{
		st += "<tr>";
		for( var j = 0; j < 3; ++ j ) 
		{
			st += "<td width=64 height=64>";
			if( field[i * 3 + j] == 0 ) st += UL + "<div style='width:100%;height:100%;cursor:pointer' onclick='query(\"stone_table.php\",\"" + (i*3+j) + "\");'>&nbsp;</div>" + FL;
			else if( field[i * 3 + j] == 1 ) st += UD + "&nbsp;" + FD;
			else st += UD + field[i * 3 + j] + FD;
			st += "</td>";
			if( field[i * 3 + j] == 0 ) ++ num;
		}
		st += "</tr>";
	}
	if( num == 0 ) document.getElementById( 'leave_msg' ).innerHTML = '<a href=game.php?leave=1>Выйти</a>';
	document.getElementById( 'stones' ).innerHTML = st;
}

<?=getField(0);?>

</script>

