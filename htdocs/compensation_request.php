<?
header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "arrays.php" );

f_MConnect( );

if( isset( $_GET['pb'] ) )
{
	$showImmediately = false;
	if( isset( $_GET['types'] ) )
	{
		$st = '';
		foreach( $item_types as $a=>$b )
		{
			$st .= "<li> <a href='javascript:selectType($a)'>$b</a></li>";
		}
		echo "document.getElementById('hru').innerHTML = '".addslashes($st)."';";
	}
	else if( isset( $_GET['type'] ) && isset( $_GET['list'] ) )
	{
		$type = (int)$_GET['type'];
		if( $type == 0 )
		{
    		$st = '';
    		foreach( $item_types2 as $a=>$b )
    		{
    			$st .= "<li> <a href='javascript:selectSubType($type,$a)'>$b</a></li>";
    		}
    		echo "document.getElementById('hru').innerHTML = '".addslashes($st)."';";
		}
		else
		{
    		$res = f_MQuery( "SELECT DISTINCT level FROM items WHERE type=$type AND parent_id=item_id ORDER BY level" );
    		if( f_MNum( $res ) == 0 )
    		{
    			echo "document.getElementById('hru').innerHTML = 'Нет таких вещей';";
    		}
    		else if( f_MNum( $res ) == 1 )
    		{
    			$showImmediately = true;
    		}
    		else
    		{
	    		$st = '';
        		while( $arr = f_MFetch( $res ) )
        		{
        			$st .= "<li> <a href='javascript:selectSubType($type,$arr[0])'>Уровень $arr[0]</a></li>";
        		}
        		echo "document.getElementById('hru').innerHTML = '".addslashes($st)."';";
    		}
		}
	}
	
	if( isset( $_GET['type'] ) && isset( $_GET['sub'] ) && isset( $_GET['items'] ) || $showImmediately )
	{
		$type = (int)$_GET['type'];
		$subtype = (int)$_GET['sub'];
		
		if( $showImmediately ) $res = f_MQuery( "SELECT * FROM items WHERE type=$type AND parent_id=item_id" );
		else if( $type == 0 ) $res = f_MQuery( "SELECT * FROM items WHERE type=$type AND type2=$subtype AND parent_id=item_id" );
		else $res = f_MQuery( "SELECT * FROM items WHERE type=$type AND level=$subtype AND parent_id=item_id" );

		$st = '';
		while( $arr = f_MFetch( $res ) )
		{
			$st .= "<li> <a href='javascript:selectItem($arr[item_id],\"$arr[name]\")'>$arr[name]</a></li>";
		}
		echo "document.getElementById('hru').innerHTML = '".addslashes($st)."';";
	}

	
	die( );
}

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link rel="icon" type="image/png" href="favicon.png">
<link href="style2.css" rel="stylesheet" type="text/css">

<?

include_js( "functions.js" );
include_js( "js/ajax.js" );

?>

<html>
<head>
<title>Алидерия - запрос компенсации за сбой игры</title>
<script>

function addItem( )
{
	query( "compensation_request.php?pb=1&types=1", "" );
}

function selectType( a )
{
	query( "compensation_request.php?pb=1&type="+a+"&list=1", "" );
}

function selectSubType( a, b )
{
	query( "compensation_request.php?pb=1&type="+a+"&sub="+b+"&items=1", "" );
}

var items = [];
var item_names = [];

function ref( )
{
	var s = '';
	for( var i in items )
	{
		num = items[i];
		name = item_names[i];
		if( num ) s += '['+num+'] ' + name + " (<a href='javascript:delItem("+i+")'>Удалить</a>)<br>" 
	}
	_( 'moo' ).innerHTML = s + '<br>';
}

function delItem( id ) { items[id] = 0; ref( ); }

function toInt( a )
{
	var ret = parseInt( a );
	if( isNaN( ret ) ) return 0;
	return ret;
}

function selectItem( a, b )
{
	var v;
	if( v = toInt( prompt( "Введите количество:" ) ) )
	{
		items[a] = toInt( items[a] );
		items[a] += v;
		item_names[a] = b;
	}
	ref( );
}

</script>
</head>

<body>

<br><center>
<table width='80%'><tr><td>

Если в результате сбоя игры у вас пропали или испортились вещи, сгорели премиумы, пропали монетки, или произошло любое другое событие, которое, с вашей
точки зрения может требовать от администрации компенсации, укажите пожалуйста в форме снизу размер компенсации, которую вы ожидаете. Администрация
рассмотрит заявку в течение двух суток (в большинстве случаев гораздо быстрее), вещи, таланты и монетки будут начислены сразу.<br>
Результат рассмотрения вашей заявки будет отправлен вам на почту.<br><br>
<table>
<tr><td>Монетки:</td><td><input class='m_btn' value='0'></td></tr>
<tr><td>Таланты:</td><td><input class='m_btn' value='0'></td></tr>
</table>

<div id='moo'>
&nbsp;
</div>

<li><a href='javascript:addItem()'>Добавить вещь</a></li><br>
<br>
<div id='hru'>
&nbsp;
</div>

</td></tr></table>

</center>

</body>
</html>
