<?
include_once('functions.php');
include_js('js/ajax.js');

if( !check_cookie( ) )
		die( "Неверные настройки Cookie" );

if (isset($_GET['update']))
{
	if (!isset($_GET['st_text']))
		die();
	else
	{
		$st_text=$_GET['st_text'];
//		$st_text = iconv("UTF-8", "CP1251", $st_text );
	}

	if (!isset($_GET['st_image']))
		die();
	else
		$st_image=$_GET['st_image'];


	f_MConnect( );

	$player_id = $HTTP_COOKIE_VARS['c_id'];

	if ($st.length>140)
		$st = substr($st, 0, 140);

	if (f_MValue("SELECT COUNT(*) FROM player_status WHERE player_id=".$player_id))
		f_MQuery("UPDATE player_status SET status_text=$st_text, status_image=$st_image WHERE player_id=".$player_id);
	else
		f_MQuery("INSERT INTO player_status (player_id, status_image, status_text) VALUES ($player_id, $st_image, $st_text)");


	f_MClose();

	die();
}
?>

<script type="text/javascript" src="js/mousewheel.js"></script>
<script>

var status_images = Array();
var status_num = 0;
var cur_image = '';
var isOpen = 0;

function addStatus(im)
{
	status_images[status_num++] = im;
	return;
}

function setCurImage(e, im)
{
	e = document.getElementById(e);
	cur_image = im;
	e.innerHTML = '<img src='+im+' id=status_0>';
	st = prompt('Введите статус: ', '');
	isOpen = 2;
	handle = null;
	e.title=st;
	query("status.php?update=1&st_text='"+st+"'&st_image='"+cur_image+"'", '');
	return;
}

function showStatus( a, b)
{
	st_w = document.getElementById('stWindow');
	var x, y;

	e = window.event;

	x = e.pageX;
	y = e.pageY;
	
	s = "<table cellspacing=0 cellpadding=0 border=0 background=images/bg.gif>";
	s += "<tr><td><img src="+a+"></td><td width=100%>&nbsp;</td></tr>";
	s += "<tr><td colspan=2>"+b+"</td></tr>";
	s += "</table>";
	
	st_w.innerHTML = s;
	st_w.style.left=(parseInt(x)+10) + 'px';
	st_w.style.top=(parseInt(y)+10) + 'px';
	st_w.style.display = '';
	
	return;
}

function hideShowStatus()
{
	document.getElementById('stWindow').style.display='none';
	return;
}

function hideStatus()
{
	isOpen = 0;
	e = document.getElementById('selStatus');
	e.innerHTML = '<img src='+cur_image+' id=status_0>';
}

function dropDown(e, s, c)
{
	if (isOpen == 1) return;
	if (isOpen == 2) {isOpen=0; return;}
	isOpen = 1;
	status_num += c;
	if (status_images.length > 0)
		if (status_num>0 && status_num>status_images.length-s)
			status_num=status_images.length-s;
	if (status_num<0)
		status_num=0;
	c=status_num;
	e = document.getElementById(e);
	var ret = '<table background=images/bg.gif><tr><td>';
	var i1 = '';
	var i2 = '';
	if (c>0)
		i2 = '<img src=images/but_l2.png onclick="isOpen=0;dropDown(\''+e.id+'\', '+s+', -1);">';
	else
		i2 = '<img src=images/but_n2.png>';
	if (c+s<status_images.length)
		i1 = '<img src=images/but_l1.png onclick="isOpen=0;dropDown(\''+e.id+'\', '+s+', 1);">';
	else
		i1 = '<img src=images/but_n1.png>';
	ret += '<center>'+i2+"</center>";
	j = c;
	while (j<=c+s-1 && j<=status_images.length-1)
	{
		
		ret += '<img src='+status_images[j]+' id=status_'+j+' onclick="setCurImage(\''+e.id+'\', \''+status_images[j]+'\');"><br>';
		j++;
	}
	ret += '<center>'+i1+'</center>';
	ret += '</td></tr></table>';
	e.innerHTML = ret;
	return;
}

function wheeling(wheelDelta)
{
	if (isOpen==0) return;
	
	isOpen=0;
	if( wheelDelta < 0 )
		dropDown( 'selStatus', 5, 1 );
	else
		dropDown( 'selStatus', 5, -1 );
	return;
}


</script>

<?

function getAllStatusImage()
{
	$imageList = glob("images/status/*.gif");
	echo "<script>";
	foreach ($imageList as $i)
	{
		echo "addStatus('".$i."');";
	}
	echo "</script>";
}

function getStatusSelect($curImage, $ctitle)
{
	echo "<div title='".$ctitle."' id=selStatus onclick='javascript:dropDown(\"selStatus\", 5, 0);' style='width:38px;border:1px solid black; position:absolute; right:5px;top:5px;'>";
	echo "<img src=$curImage id=status_0>";
	echo "</div>";
	echo "<script>cur_image='".$curImage."';</script>";
	echo "<div id=stWindow style='width:150px;border:1px solid black; position:absolute; left:5px;top:5px; display:none;'>";
	echo "&nbsp;";
	echo "</div>";
}

getAllStatusImage();

$player_id = $HTTP_COOKIE_VARS['c_id'];
$res = f_MQuery("SELECT status_image, status_text FROM player_status WHERE player_id=".$player_id);
$arr=f_MFetch($res);
$ct = '';
$ci='images/status/draco_shocked.gif';
if ($arr)
{
	$ct = $arr[1];
	$ci = $arr[0];
}


getStatusSelect($ci, $ct);

?>

<script>
var main_e = document.getElementById('selStatus');
main_e.onmouseover = function(){
	handle = wheeling;
};

main_e.onmouseout = function(){
handle = null;
};
</script>