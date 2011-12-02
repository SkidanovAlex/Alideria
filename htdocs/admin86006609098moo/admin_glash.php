<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $_GET['del'] ) )
{
	$id = (int)$_GET['del'];
	f_MQuery( "DELETE FROM glash_phrases WHERE entry_id=$id" );
}

if( isset( $_GET['phrase'] ) )
{
	$phrase = $_GET['phrase'];
	$pri = (int)$_GET['pri'];
	$chat = (int)$_GET['chat'];
	f_MQuery( "INSERT INTO glash_phrases ( phrase, priority, chat ) VALUES ( '$phrase', '$pri', '$chat' )" );
	
	die( "<script>location.href='admin_glash.php';</script>" );
}

$res = f_MQuery( "SELECT * FROM glash_phrases" );

if( !f_MNum( $res ) ) echo "<i>Пока что нет ни одной реплики</i><br><br>";
else
{
    while( $arr = f_MFetch( $res ) )
    {
    	echo "Фраза: <i>{$arr[phrase]}</i><br>Приоритет: <b>{$arr[priority]}</b><br>Чат: <b>{$arr[chat]}</b><br><a href='admin_glash.php?del={$arr[entry_id]}'>Удалить</a><br><br>";
    }
}

echo "Добавить фразу:<br><form action='admin_glash.php' method='get'>";
echo "Фраза: <input type=text name=phrase><br>";
echo "Приоритет: <input type=text name=pri><br>";
echo "Номер чата: <input type=text name=chat><br>";
echo "0 - общий, > 0 - по номеру<br>";
echo "<input type='submit' value='Добавить'>";
echo "</form>";

echo "Приоритет 5 обозначает что шанс на фразу в 5 раз больше, чем у приоритета 1. То есть чем выше приоритет, тем выше шанс быть показанной<br>";
echo "При этом если фраза одна, то приоритет не играет роли, если их две, то приоритеты 2 и 5 будут иметь такой же эффект, как и 4 и 10. То есть влияет только относительная велиличина по отношению к другим фразам<br>";

echo "<br>В любом случае, приоритеты сейчас не работают, все равновероятно для каждого чата. В торговый тоже ничего писать не получится.<br>";
echo "<br />От Мая: Если не ошибаюсь, html-тэги работают. Помните, что некоторые тэги могут работать без использования двойных кавычек при указании параметров: не знаю, какие глюки они могут вызывать, но могут.<br />Учтите: <b>смайлы нужно указывать как изображения.</b>";

?>
