<?

include( 'functions.php' );
include( 'player.php' );

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


?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style_title.css" rel="stylesheet" type="text/css">
<head><title>Алидерия</title></head>
</script>

<script>

	function ge( a )
	{
		if( document.all ) return document.all[a];
		else return document.getElementById( a );
	}

	function auth_err( a )
	{
		ge( 'err' ).innerHTML = '<font color=red><b>' + a + '</b></font>';
	}
	
	function begin_game( )
	{
		location.href = 'main.php';
	}
	
	function authi( )
	{
		ge( 'err' ).innerHTML = '<i>Идет авторизация</i>';
		ge( 'frm' ).submit( );
	}
	
	function auth( e )
	{
		e = e || window.event;
		if( e.keyCode == 13 ) authi();
	}
	
	function reg( )
	{
		window.open('reg.php<?=$ref_lnk?>','_blank','scrollbars=no,width=400,height=300,resizable=no');
	}

</script>

<?php

include_once( "skin.php" );

?>

<center><table width=1005 cellspacing=0 cellpadding=0 border=0>
<tr><td colspan=3><img src=images/title/a.jpg width=1005 height=327></td></tr>
<tr><td width=232 valign=top>

	<table id=tb1 width=232 cellspacing=0 cellpadding=0 border=0 background=images/title/bg2.jpg>
		<tr><td><img src=images/title/b.jpg width=232 height=66></td></tr>
		<tr><td><table width=232 cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td><img src=images/title/e.jpg width=50 height=27></td>
				<td><a href=help.php?id=1006 target=_blank><img border=0 src=images/title/f1.jpg width=138 height=27></td>
				<td><img src=images/title/g.jpg width=44 height=27></td>
			</tr>
			<tr>
				<td colspan=3><img src=images/title/h.jpg width=232 height=16></td>
			</tr>
			<tr>
				<td><img src=images/title/e.jpg width=50 height=27></td>
				<td><a href=help.php?id=500 target=_blank><img border=0 src=images/title/f2.jpg width=138 height=27></td>
				<td><img src=images/title/g.jpg width=44 height=27></td>
			</tr>
			<tr>
				<td colspan=3><img src=images/title/h.jpg width=232 height=16></td>
			</tr>
			<tr>
				<td><img src=images/title/e3.jpg width=50 height=27></td>
				<td><a href=help.php target=_blank><img border=0 src=images/title/f3.jpg width=138 height=27></td>
				<td><img src=images/title/g3.jpg width=44 height=27></td>
			</tr>
			<tr>
				<td colspan=3><img src=images/title/h1.jpg width=232 height=16></td>
			</tr>
			<tr>
				<td><img src=images/title/e4.jpg width=50 height=27></td>
				<td><a href=help.php?id=5 target=_blank><img border=0 src=images/title/f4.jpg width=138 height=27></td>
				<td><img src=images/title/g4.jpg width=44 height=27></td>
			</tr>
			<tr>
				<td colspan=3><img src=images/title/i.jpg width=232 height=175></td>
			</tr>
		</table></td></tr>
		<tr><td style='background-image: url("images/title/n.jpg");  background-position: top; background-repeat: no-repeat;' align=center>
			<br><br>
			<b>Администрация:</b><br>
			<a href=mailto:admin@alideria.ru>admin@alideria.ru</a><br><br>
			<b>Служба поддержки:</b><br>
			<a href=mailto:support@alideria.ru>support@alideria.ru</a><br>
			<img src=images/title/o.jpg width=232 height=121></td></tr>
		<tr height=100%><td background=images/title/bg4.jpg vAlign=bottom>
<?

// Последние темы форума

print( "<table width=195><tr><td align=right><b>Последние темы форума</b><br>" );
$res = f_MQuery( "SELECT * FROM forum_threads WHERE important=0 AND (room_id=2 OR room_id=4 OR room_id=5 OR room_id=6) ORDER BY last_post_made DESC LIMIT 5" );
while( $arr = f_MFetch( $res ) )
	print( "<a target=_blank href=forum.php?thread=$arr[thread_id]&f=0>$arr[title]</a><br>" );
print( "</td></tr></table>" );


?>
		</td></tr>
		<tr><td vAlign=bottom background=images/title/bg4.jpg><img src=images/title/bg4.jpg width=232 height=13></td></tr>

<map name="ForumMap">
	<area shape="poly"coords="135,10,195,10,195,30,135,30" style='cursor:pointer' href='forum.php' target=_blank>
</map>

		<tr><td vAlign=bottom background=images/title/bg4.jpg><img src=images/title/p.jpg width=232 height=216 usemap='#ForumMap' border=0></td></tr>
	</table>

</td><td width=541 valign=top>

	<table id=tb2 width=541 cellspacing=0 cellpadding=0 border=0 background=images/title/bg2.jpg>
		<tr height=13><td><img src=images/title/c.jpg width=541 height=13></td></tr>
		<tr><td><table width=541 cellspacing=0 cellpadding=0 border=0>
<script>
document.write( '<form method=post name=frm id=frm action=auth.php target=auth_frame>' );
</script>

			<tr>
				<td vAlign=top><img src=images/title/j.jpg width=195 height=74></td>
				<td vAlign=top width=182 background=images/title/bg1.jpg>
						<input style='width: 182px; height: 17px; border-width: 1; border-color: #BCB5A2;' type=text name=login id=login>
						<input style='position:relative; top: 9px; width: 182px; height: 17px; border-width: 1; border-color: #BCB5A2;' type=password name=pwd id=pwd>
						<div style='position:relative; top: 9px;' width=100% align=right><a href='javascript:reg();'>Зарегистрироваться</a>&nbsp;&nbsp;&nbsp;<a href='javascript:authi();'>Войти</a></div>
						<div style='position:relative; top: 7px;' id=err name=err>
				</td>
				<td vAlign=top><img src=images/title/k.jpg width=164 height=74></td>
			</tr>
			</form>
		</table></td></tr>
		<tr height=60><td><img src=images/title/l.jpg width=541 height=60></td></tr>
			<script src=js/clans.php></script>
			<script src=js/ii.js></script>

<?

// Новости

$res = f_MQuery( "SELECT * FROM forum_threads WHERE room_id = 0 AND important >= 0 ORDER BY thread_id DESC LIMIT 7" );

$first = true;
while( $arr = f_MFetch( $res ) )
{
	if( !$first ) print( "<tr><td style='background-image: url(\"images/title/bg3.jpg\");  background-position: top; background-repeat: no-repeat;'><br><br>" );
	else print( "<tr><td>" );
	print( "<center><table width=90%><tr><td>" );
	$first = false;
	$thread_id = $arr['thread_id'];
	$title = $arr['title'];
	$author_id = $arr['author_id'];
	print( "<b>$title</b><br>" );
	$ires = f_MQuery( "SELECT login FROM characters WHERE player_id = $author_id" );
	$iarr = f_MFetch( $ires );
	if( !$iarr ) $iarr[0] = "Unknown Admin";
	$tm = date( "d.m.Y", $arr['time'] );
	print( "</td><td align=right><i>$iarr[0], $tm</i></td></tr><tr><td colspan=2>" );
	
	$ires = f_MQuery( "SELECT * FROM forum_posts WHERE thread_id = $thread_id ORDER BY post_id LIMIT 1" );
	while( $iarr = f_MFetch( $ires ) )
	{
		$txt = $iarr[text];
		$pos = strpos( $txt, "<br><br><i>Последний раз редактировался: " );
		if( $pos !== false ) $txt = substr( $txt, 0, $pos );

		print( "<div align=justify>$txt<br><br></div>" );
	}
	print( "</td></tr></table></td></tr>" );
}

?>

		<tr height=100%><td background=images/title/bg2.jpg><img width=0 height=0></td></tr>
		<tr height=112><td><img src=images/title/m.jpg width=541 height=112></td></tr>
	</table>

</td><td width=232 valign=top>

	<table id=tb3 width=232 cellspacing=0 cellpadding=0 border=0>
		<tr><td><img src=images/title/d.jpg width=232 height=84></td></tr>
		<tr><td><img src=images/title/q.jpg width=232 height=47></td></tr>
		<tr><td><div style='position:relative;top:0px;left:0px;'><table width=232 cellspacing=0 cellpadding=0 border=0>
			<tr><td width=51 background=images/title/bg5.jpg valign=bottom><img border=0 src=images/title/r.jpg width=51 height=170></td>
			<td width=123 background=images/title/bg2.jpg valign=top>
			&nbsp;	
			</td>
			<td width=58 background=images/title/bg6.jpg valign=bottom><img border=0 src=images/title/s.jpg width=58 height=140></td></tr>
		</table>
			<div style='position:absolute; top:-40px; left: 51px;'>
			<?
			
				$res = f_MQuery( "SELECT player_id FROM characters WHERE player_id >= 190 AND player_id <> 1296 AND length(login)<20 ORDER BY exp DESC LIMIT 10" );
				while( $arr = f_MFetch( $res ) )
				{
					$plr = new Player( $arr[0] );
					echo( "<script>document.write(".$plr->Nick().");</script><br>" );
				}
				
			?>
			</div></div>
		</td></tr>
		<tr><td><img src=images/title/t.jpg width=232 height=88></td></tr>
		<tr height=100%><td><table width=232 height=100% cellspacing=0 cellpadding=0 border=0>
			<tr><td width=46 background=images/title/bg2.jpg valign=top><img border=0 src=images/title/u.jpg width=46 height=113></td>
			<td width=123 background=images/title/bg2.jpg valign=top>
				<img src=images/title/v.jpg width=186 height=40 border=0><br>
<?

	$plr = new Player( 172 );
	echo( "<script>document.write(".$plr->Nick().");</script><br>" );
	echo( "<a target=_blank href=http://www.blogs.mail.ru/bk/shd/><b>Александр Скиданов</b></a><br>" );
	echo( "Программный код игры<br>" );
	echo( "<br>" );
	$plr = new Player( 174 );
	echo( "<script>document.write(".$plr->Nick().");</script><br>" );
	echo( "<b>Дмитрий Жемчужный</b><br>" );
	echo( "Работа с фрилансерами<br>" );
	echo( "<br>" );
	$plr = new Player( 186 );
	echo( "<script>document.write(".$plr->Nick().");</script><br>" );
	echo( "<b>Дмитрий Галяк</b><br>" );
	echo( "Дизайн игры и графика<br>" );
	echo( "<br>" );
	$plr = new Player( 3264 );
	echo( "<script>document.write(".$plr->Nick().");</script><br>" );
	echo( "<b>Данила Евстифеев</b><br>" );
	echo( "Текстовое оформление игры<br>" );
	echo( "<br>" );
	echo( "<a target=_blank href=http://www.free-lance.ru/users/234567/><b>Наталья Ильиных</b></a><br>" );
	echo( "Пейзажи Алидерии<br>" );
	echo( "<br>" );
	echo( "<b>Александра Ерохина</b><br>" );
	echo( "Пейзажи Алидерии<br>" );
	echo( "<br>" );
	echo( "<a target=_blank href=http://www.free-lance.ru/users/Birddesign/><b>Роман Лихман</b></a><br>" );
	echo( "Дизайн титульной страницы<br>" );
	echo( "<br>" );

?>
			</td>
		</table></td></tr>
		<tr><td><img src=images/title/w.jpg width=232 height=217></td></tr>
	</table>

</td></tr></table>




<iframe name=auth_frame id=auth_frame width=0 height=0></iframe><br>


	<!--begin of Rambler's Top100 code -->
	<a href="http://top100.rambler.ru/top100/">
	<img src="http://counter.rambler.ru/top100.cnt?1251668" alt="" width=1 height=1 border=0></a>
	<!--end of Top100 code-->

	<!--begin of Top100 logo-->
	<a href="http://top100.rambler.ru/top100/">
	<img src="http://top100-images.rambler.ru/top100/banner-88x31-rambler-brown2.gif" alt="Rambler's Top100" width=88 height=31 border=0></a>
	<!--end of Top100 logo -->




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








	<!-- HotLog -->

	<script type="text/javascript" language="javascript">
	hotlog_js="1.0";
	hotlog_r=""+Math.random()+"&s=472866&im=116&r="+escape(document.referrer)+"&pg="+
	escape(window.location.href);
	document.cookie="hotlog=1; path=/"; hotlog_r+="&c="+(document.cookie?"Y":"N");
	</script>
	<script type="text/javascript" language="javascript1.1">
	hotlog_js="1.1";hotlog_r+="&j="+(navigator.javaEnabled()?"Y":"N")
	</script>
	<script type="text/javascript" language="javascript1.2">
	hotlog_js="1.2";
	hotlog_r+="&wh="+screen.width+'x'+screen.height+"&px="+
	(((navigator.appName.substring(0,3)=="Mic"))?
	screen.colorDepth:screen.pixelDepth)</script>
	<script type="text/javascript" language="javascript1.3">hotlog_js="1.3"</script>
	<script type="text/javascript" language="javascript">hotlog_r+="&js="+hotlog_js;
	document.write("<a href='http://click.hotlog.ru/?472866' target='_top'><img "+
	" src='http://hit24.hotlog.ru/cgi-bin/hotlog/count?"+
	hotlog_r+"&' border=0 width=88 height=31 alt=HotLog><\/a>")
	</script>
	<noscript>
	<a href="http://click.hotlog.ru/?472866" target="_top">
	<img src="http://hit24.hotlog.ru/cgi-bin/hotlog/count?s=472866&amp;im=116" border="0" 
	 width="88" height="31" alt="HotLog"></a>
	</noscript>

	<!-- /HotLog -->







	<!-- SpyLOG -->
	<script src="http://tools.spylog.ru/counter2.2.js" type="text/javascript" id="spylog_code" counter="996714" ></script>
	<noscript>
	<a href="http://u9967.14.spylog.com/cnt?cid=996714&f=3&p=0" target="_blank">
	<img src="http://u9967.14.spylog.com/cnt?cid=996714&p=0" alt='SpyLOG' border='0' width=88 height=31 ></a> 
	</noscript>
	<!--/ SpyLOG -->


	<!-- Top Roleplay -->
	<a href="http://top.roleplay.ru/3301" target="top_">
	<img src="http://img.rpgtop.su/rpgtop1.gif" alt="Рейтинг Ролевых Ресурсов" border="0" width="88" height="31"></a> 
	<!--/ Top Roleplay -->



	<!-- Palantir -->
    <a title="Каталог фэнтези сайтов Палантир" href='http://palantir.in/?from=5848' target='_blank'>	
    <script type="text/javascript">
    Md=document;Mnv=navigator;
    Mrn=Math.random();Mn=(Mnv.appName.substring(0,2)=="Mi")?0:1;Mp=0;Mz="p="+Mp+"&";
    Ms=screen;Mz+="wh="+Ms.width+'x'+Ms.height;My="<img src='http://palantir.in/count.php?id=5848&cid=concept6.png";My+="&cntc=none&rand="+Mrn+"&"+Mz+"&referer="+escape(Md.referrer)+'&pg='+escape(window.location.href);My+="'  alt='Palantir' title='Каталог фэнтези сайтов Палантир' border='0' width='88px' height='31px'>";Md.write(My);</script>
    <noscript><img src="http://palantir.in/count.php?id=5848&cid=concept6.png" alt='Palantir' title="Каталог фэнтези сайтов Палантир" border=0 width="88px" height="31px"></noscript>
    </a>
	<!--/ Palantir -->


	<!-- Internet Map -->
	<a href="http://www.internetmap.info/cgi-bin/go.cgi?site_id=93883" target=_blank><img src="http://www.internetmap.info/images/im_88x31.gif" border=0 alt="Internet Map"></a>
	<!--/ Internet Map -->




</center>
<script>
	ge( 'frm' ).login.onkeydown = auth;
	ge( 'frm' ).pwd.onkeydown = auth;
	
	hg = Math.max( ge( 'tb2' ).clientHeight, Math.max( ge( 'tb1' ).clientHeight, ge( 'tb3' ).clientHeight ) );
	ge( 'tb1' ).style.height = hg;
	ge( 'tb2' ).style.height = hg;
	ge( 'tb3' ).style.height = hg;
</script>
