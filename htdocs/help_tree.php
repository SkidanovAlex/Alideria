<?

include ( 'functions.php' );
include ( 'skin.php' );
f_MConnect( );

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">

<html>
<body style='background-color: #BBBBBB'>

<? ScrollTableStart( 'left', 'top' ); ?>

<script>

var st = '';
var ex = new Array( );

function dw( a )
{
	st += a;
}

function div_begin( q )
{
	z = "";
	if( q ) z = " style='display: none'";
	dw( "<div id=dv" + q + z + "><table cellspacing=0 cellpadding=0 border=0>\n" );
}

function div_end( q )
{
	dw( "</table></div>\n" );
}

function expand( id )
{
	if( ex[id] )
	{
		document.getElementById( 'dv' + id ).style.display = 'none';
		document.getElementById( 'im' + id ).src = 'images/e_plus.gif';
		ex[id] = 0;
	}
	else
	{
		document.getElementById( 'dv' + id ).style.display = '';
		document.getElementById( 'im' + id ).src = 'images/e_minus.gif';
		ex[id] = 1;
	}
}

function topic( offset, leaf, id, title, url )
{
	dw( "<tr><td><img height=0 width=" + ( offset ? 13 : 0 ) + "></td><td>" );
	if( leaf ) dw( "<img src=images/e_dot.gif height=11 width=11>&nbsp;" );
	else dw( "<img src=images/e_plus.gif id=im" + id + " height=11 width=11 border=0 style='cursor: pointer' onclick='expand( " + id + " )'>&nbsp;" );
	if( url != "" ) dw( "<a href=help/" + url + " target=help>" );
	dw( "<nobr>" + title + "</nobr>" );
	if( url != "" ) dw( "</a><br>" );
}

function end_topic( )
{
	dw( "</td></tr>\n" );
}

<?

if( isset( $_GET['id'] ) )
	$target_id = $_GET['id'];
else $target_id = -1;
$p = Array( );
$st = '';
function rec( $id, $s )
{
	global $p;
	global $target_id;
	global $st;
	if( $p[$id] )
		return false; // не должно пригодиться, но лучше перестраховаться
	$p[$id] = 1;
	$ret = false;
	
	if( $id == $target_id )
	{
		$ret = true;
	}
	
	$res = f_MQuery( "SELECT topic_id, title, url FROM help_topics WHERE parent_id = $id ORDER BY topic_id" );
	if( mysql_num_rows( $res ) )
	{
		print( "div_begin( $id );\n" );
		while( $arr = mysql_fetch_array( $res ) )
		{
			$res2 = f_MQuery( "SELECT topic_id FROM help_topics WHERE parent_id = $arr[topic_id]" );
			if( mysql_num_rows( $res2 ) ) $z = 0;
			else $z = 1;
			
			print( "topic( $s, $z, $arr[topic_id], '$arr[title]', '$arr[url]' );\n" );
			if( !$z ) if( rec( $arr[topic_id], $s + 1 ) )
			{
				$ret = true;
				if( $id ) $st .= "expand( $id );";
			}
			if( $arr[topic_id] == $target_id )
 			{
				if( !$ret && $id ) $st .= "expand( $id );";
				$ret = true;
				if( $arr[url] != '' ) $st .= "parent.help.location.href='help/$arr[url]';";
			}
			print( "end_topic( );\n" );
		}
		print( "div_end( );\n" );
	}
	
	return $ret;
}

rec( 0, 0 );
if( $target_id == -1 )
	$st .= "parent.help.location.href='help/faq.php';";

?>

//alert( st );
document.write( st );

<?

echo $st;

?>

</script>

<? ScrollTableEnd( ); ?>

</body>
</html>
