<?

include( 'functions.php' );
include( 'player.php' );
include_js( 'js/skin.js' );
include_js( 'js/tooltips.php' );

f_MConnect( );

$ref_lnk = "";

$ipstr = AddSlashes( getenv( "REMOTE_ADDR" ) );
$ipxstr = AddSlashes( getenv( "HTTP_X_FORWARDED_FOR" ) );
f_MQuery( "LOCK TABLE ref_ips WRITE" );
$res = f_MQuery( "SELECT * FROM ref_ips WHERE ip='$ipstr' OR ip='$ipxstr'" );
$kra = false;
if( f_MNum( $res ) == 0 )
{
	f_MQuery( "INSERT INTO ref_ips VALUES( '$ipstr' )" );

	if( strlen( $ipxstr ) > 0 && $ipxstr != $ipstr ) f_MQuery( "INSERT INTO ref_ips VALUES( '$ipxstr' )" );
	f_MQuery( "UNLOCK TABLES" );
	$kra = true;
}
else f_MQuery( "UNLOCK TABLES" );

if( isset( $_GET['r'] ) )
{
	$id = $_GET['r'];
	settype( $id, 'integer' );
	$plr = new Player( $id );
	if( $kra ) $plr->AddMoney( mt_rand( 1, 5 ) );
	$ref_lnk = "?ref=$plr->login";
}

include_js( 'js/clans.php' );
include_js( 'js/ii.js' );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link rel="icon" type="image/png" href="favicon.png">
<link href="style_title.css" rel="stylesheet" type="text/css">
<head><title>Алидерия</title></head>
<bodu style='background-color:#e0c3a0;background-image:url(images/chat/chat_bg.gif)'>
<body style='background-color:#e0c3a0;background-image:url(images/bg7.jpg)'>

<?

include_js( 'js/ipng.js' );

function btn( $id, $i1, $i2, $url, $x, $y, $w, $h )
{
	echo "<div style='position:absolute;left:{$x}px;top:{$y}px;width:140px;height:{$h}px;cursor:pointer;' onclick=\"location.href='{$url}';\">";
	echo "<center><table style='width:{$w}px;height:{$h}px;'>";
	echo "<tr><td id=$id width=$w height=$h background='images/ttl/$i1' style='cursor:pointer;'>";
	echo "</td></tr>";
	echo "</table></center></div>";
}

?>

<script>

	function ge( a )
	{
		if( document.all ) return document.all[a];
		else return document.getElementById( a );
	}

	function reg( )
	{
		window.open('reg.php<?=$ref_lnk?>','_blank','scrollbars=no,width=400,height=300,resizable=no');
	}

	function rest( )
	{
		window.open('restore_pwd.php','_blank','scrollbars=no,width=400,height=300,resizable=no');
	}

var wide = screen_width() > 1050;
</script>

<center><script>if( wide ) { document.write( '<table height=100%><tr><td valign=middle><table><tr><td>' ); FUlm(); }</script><table cellspacing=0 cellpadding=0 border=0><tr><td><div style='position:relative;top:0px;left:0px;'><script>

var id = 0;
for( var i = 0; i < 3; ++ i )
{
   	document.write( '<nobr>' );
	for( var j = 0; j < 5; ++ j )
	{
		++ id;
		var w = 200; if( j == 4 ) w = 203;
		var h = 200; if( i == 2 ) h = 194;
		c = ''; if( i > 0 && j > 0 && j < 4 ) c = 's';
		document.write( '<img width=' + w + ' height=' + h + ' border=0 src=images/ttl/' + c + id + '.jpg>' );
	}
	document.write( '</nobr><br>' );
}

</script>
<div style='position:absolute;left:822px;top:240px;width:133;'>
	<center><table><tr><td>&nbsp;</td><td style='width:87px;height:34px;' background='images/ttl/rating.png'>&nbsp;
	</td></tr></table></center>
	<?
				$res = f_MQuery( "SELECT player_id FROM characters WHERE player_id >= 190 AND player_id <> 1296 AND player_id<>261770 AND length(login)<11 ORDER BY exp DESC LIMIT 6" );
				while( $arr = f_MFetch( $res ) )
				{
					$plr = new Player( $arr[0] );
					echo( "<script>document.write(".$plr->Nick().");</script><br>" );
				}
				?>

</div>
<div style='position:absolute;left:822px;top:404px;width:133;'>
	<center><table><tr><td>&nbsp;</td><td style='width:84px;height:31px;' background='images/ttl/news.png'>&nbsp;
	</td></tr></table></center>
	<center><small>
	<? include( "news.php" ); ?>	  
	</small></center>
</div>

<div style='position:absolute;left:35px;top:520px;'>
	<table background=images/ttl/admin.png style='width:121px;height:44px;' cellspacing=0 cellpadding=0><tr><td><img border=0 src=empty.gif width=121 height=44></td></tr></table>
</div>                  

<div style='position:absolute;left:385px;top:263px;'>
<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><button class=n_btn id=rdy_td style='cursor: pointer' onClick='location.href="index.php";'><b>Назад</b></button></td><td><img src=images/top/c.png></td></tr></table>
</div>

<div style='position:absolute;left:813px;top:573px;width:154px;'>
<center><small><b>c www.alideria.ru, 2009</b></small></center>
</div>

<div style='position:absolute;left:813px;top:583px;width:154px;'>
<center><small><a href=help.php?id=500 target=_blank>Правила</a>&nbsp;&nbsp;&nbsp;<a href=help.php?id=2 target=_blank>Соглашение</a></small></center>
</div>


<?

$a = 1;

?>

<div style='position:absolute;left:238px;top:305px;'>
<a href=images/screenshots/<?=($a+0)?>_big.jpg target=_blank><img border=0 width=200 height=110 src=images/screenshots/<?=($a+0)?>_small.jpg>
</div>
<div style='position:absolute;left:464px;top:305px;'>
<a href=images/screenshots/<?=($a+1)?>_big.jpg target=_blank><img border=0 width=200 height=110 src=images/screenshots/<?=($a+1)?>_small.jpg>
</div>
<div style='position:absolute;left:238px;top:441px;'>
<a href=images/screenshots/<?=($a+2)?>_big.jpg target=_blank><img border=0 width=200 height=110 src=images/screenshots/<?=($a+2)?>_small.jpg>
</div>
<div style='position:absolute;left:464px;top:441px;'>
<a href=images/screenshots/<?=($a+3)?>_big.jpg target=_blank><img border=0 width=200 height=110 src=images/screenshots/<?=($a+3)?>_small.jpg>
</div>

<div style='position:absolute;left:220px;top:340px;width:450px;' align=justify>
<?


?>
</div>
                                   
<div style='position:absolute;left:24px;top:566px;'>


	<!--Rating@Mail.ru COUNTEr--><script language="JavaScript" type="text/javascript"><!--
	d=document;var a='';a+=';r='+escape(d.referrer)
	js=10//--></script><script language="JavaScript1.1" type="text/javascript"><!--
	a+=';j='+navigator.javaEnabled()
	js=11//--></script><script language="JavaScript1.2" type="text/javascript"><!--
	s=screen;a+=';s='+s.width+'*'+s.height
	a+=';d='+(s.colorDepth?s.colorDepth:s.pixelDepth)
	js=12//--></script><script language="JavaScript1.3" type="text/javascript"><!--
	js=13//--></script><script language="JavaScript" type="text/javascript"><!--
	d.write('<a href="http://top.mail.ru/jump?from=1336155"'+
	' target=_top><img src="http://d3.c6.b4.a1.top.list.ru/counter'+
	'?id=1336155;t=55;js='+js+a+';rand='+Math.random()+
	'" alt="Рейтинг@Mail.ru"'+' border=0 height=31 width=88/><\/a>')
	if(11<js)d.write('<'+'!-- ')//--></script><noscript><a
	target=_top href="http://top.mail.ru/jump?from=1336155"><img
	src="http://d3.c6.b4.a1.top.list.ru/counter?js=na;id=1336155;t=55"
	border=0 height=31 width=88
	alt="Рейтинг@Mail.ru"/></a></noscript><script language="JavaScript" type="text/javascript"><!--
	if(11<js)d.write('--'+'>')//--></script><!--/COUNTER-->


	<!--LiveInternet counter--><script type="text/javascript"><!--
	document.write("<a href='http://www.liveinternet.ru/click' "+
	"target=_blank><img src='http://counter.yadro.ru/hit?t52.6;r"+
	escape(document.referrer)+((typeof(screen)=="undefined")?"":
	";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
	screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
	";"+Math.random()+
	"' alt='' title='LiveInternet: показано число просмотров и"+
	" посетителей за 24 часа' "+
	"border=0 width=88 height=31><\/a>")//--></script><!--/LiveInternet-->








</div>

<?

btn( 'screens', 'o1l.png', 'o1l.png', 'screenshots.php', 38, 315, 106, 33 );
btn( 'reg', 'o2l.png', 'o2l.png', 'javascript:reg();', 38, 355, 123, 36 );
btn( 'help', 'o3l.png', 'o3l.png', 'help.php', 38, 417, 84, 33 );
btn( 'overview', 'o4l.png', 'o4l.png', 'help.php?id=5', 38, 457, 84, 33 );

?>

</div></td></tr></table><script>if( wide ) { FL(); document.write( '</td></tr></table></td></tr></table>' ); }</script></center>

<iframe style='border:0px;' name=auth_frame id=auth_frame width=0 height=0></iframe>

</body>

