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
include_js( 'js/ii_ttl.js' );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<META NAME="webmoney.attestation.label" CONTENT="webmoney attestation label#8231D6C9-32F6-4BB8-8E4D-B6811F005EFD" /> 
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link rel="icon" type="image/png" href="favicon.png">
<link href="style_title.css" rel="stylesheet" type="text/css">
<head><title>��������</title></head>
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

	function auth_err( a )
	{
		if( a != 'confirm' )
		{
			ge( 'err' ).innerHTML = '<br><font color=darkred><b>' + a + '</b></font>';
		}
		else
		{
			document.getElementById( 'confirmAgr' ).style.display = '';
		}
	}
	
	function begin_game( )
	{
		location.href = 'main.php';
	}
	
	function authi( )
	{
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
		document.write( '<img width=' + w + ' height=' + h + ' border=0 src=images/ttl/' + id + '.jpg>' );
	}
	document.write( '</nobr><br>' );
}

</script>
<div style='position:absolute;left:822px;top:240px;width:133;overflow:hidden'>
	<center><table><tr><td>&nbsp;</td><td style='width:87px;height:34px;' background='images/ttl/rating.png'>&nbsp;
	</td></tr></table></center>
	<div style='width:300px;'>
	<?
				$res = f_MQuery( "SELECT player_id FROM characters WHERE player_id >= 190 AND player_id <> 1296 AND player_id<>261770 AND player_id<>1314512 AND player_id<>1308118  ORDER BY exp DESC LIMIT 6" );
				while( $arr = f_MFetch( $res ) )
				{
					$plr = new Player( $arr[0] );
					echo( "<script>document.write(".$plr->Nick().");</script><br>" );
				}
				?>
	</div>
	<div style='position:absolute;right:0px;top:36px;width:40px;height:200px;'>
	<script>
        function opng(src,w,h)
        {
        	if( document.all ) document.write( "<div style='width:" + w + "px; height:" + h + "px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\"" + src + "\", sizingMethod=\"scale\");'></div>" );
        	else document.write( "<img src=" + src + " width=" + w + " height=" + h + ">" );
        }
		opng("images/ttl/opa.png",40,200);
	</script>
	</div>
</div>
<div style='position:absolute;left:822px;top:404px;width:133;'>
	<center><table><tr><td>&nbsp;</td><td style='width:84px;height:31px;' background='images/ttl/news.png'>&nbsp;
	</td></tr></table></center>
	<center><small>
	<? include( "images/news.php" ); ?>	  
	</small></center>
</div>

<div style='position:absolute;left:35px;top:520px;'>
	<table background=images/ttl/admin.png style='width:121px;height:44px;' cellspacing=0 cellpadding=0><tr><td><img border=0 src=empty.gif width=121 height=44></td></tr></table>
</div>                  

<div style='position:absolute;left:218px;top:25<?if (strstr(getenv("HTTP_USER_AGENT"),"MSIE")) echo 3; else echo 4;?>px;'><table cellspacing=0 cellpadding=0 border=0><form method=post name=frm id=frm action=auth_2.php?q=<?=mt_rand( );?> target=auth_frame><tr>
		<td style='width:50px;height:23px;' align=right>�����:&nbsp;</td>
		<td valign=top><input style='width:184px;height:19px;border:1px solid #6e5335;background-color:#F1E4D3;' name=login id=login></td>
	</tr><tr>
		<td style='width:50px;height:19px;' align=right>������:&nbsp;</td>
		<td valign=top><input type=password style='width:184px;height:19px;border:1px solid #6e5335;background-color:#F1E4D3;' name=pwd id=pwd></td>
	</tr><tr>
		<td>&nbsp;</td><td><div id=err></td></tr>
		<tr id="confirmAgr" style="display: none;">
			<td style="vertical-align: top; background: url( /images/bg.gif ); border: 1px solid #651010; padding: 3px;" colspan="2">
				<input type='checkbox' name='confirmAgr' />� �������� <a href="/help.php?id=2" style="text-decoration: underline;" target="_blank">���������������� ����������</a>
			</td>
		</tr>
	</form></table>
</div>                  

<div style='position:absolute;left:813px;top:573px;width:154px;'>
<center><small><b>� www.alideria.ru, 2009</b></small></center>
</div>

<div style='position:absolute;left:813px;top:583px;width:154px;'>
<center><small><a href=help.php?id=500 target=_blank>�������</a>&nbsp;&nbsp;&nbsp;<a href=help.php?id=2 target=_blank>����������</a></small></center>
</div>


<div style='position:absolute;left:480px;top:275px;width:95px;height:20px;cursor:pointer;' onclick='authi();'><table cellspacing=0 cellpadding=0 width=100% height=100%><tr><td width=100% height=100% align=center valign=middle>
� ����!!!
</td></tr></table></div>

<div style='position:absolute;left:220px;top:340px;width:450px;' align=justify>
<?

if( mt_rand( 1,3 ) == 1 )
{
	$ttl = '������� ��������� � ������������� ����-��������? ����� ���������� � ��������!';
	$txt = '���-�� � ��������� ��������� ���� ��������� ������� � ������, ������ � ������. ������������ � ������� ������ ����� ���������� ������ � ����������. � ��� ��������� ��� ��� ������! ��� ��� ����� ��� ��������� ������ ����� ������� ���� ����� ���� ����� ������ �����.<br><br>����� ���������� � ������ �������� �����!';
}
elseif( mt_rand( 1,2 ) == 1 )
{
	$ttl = '� ��� �� ������ ��� �����?';
	$txt = '����� ������� �������� �� ����� ����� ���������: ������� �� MTG, ������� � ������ �������� Astral Tournament, ��������� ���� ��������� ������� �����, ���������� �������� ����������� ���������� � ���������� �������, �������� �� ����� ������������� rpg-�������� � �������� �� ��������� � ���������� �������. � ����� ����� �������� ���������� � ���� ����������� � ������ ������. �������� ���������?<br>';
}
else
{
	$ttl = '���������� ���� ������?';
	$txt = '� �� ���������. �� ���������, ��� ���� ������� ���� �������, ���������, ��� ���� ����� ������ � ����, � ���� ��� ����� ����. �� ���������, ��� ��������� ������������ ����� ����, ���� � �������, ��� ������� ���� ������ � ����������� ������� ���������. �� ���������, ��� �������� ����������� �������� � ������� ���������, ��� ����� ������ � ������, ��������� ������ ���� ������. ������ ���������? ��������������<br>';
}

echo "<div align=right><font color=darkred size=+1><b>$ttl</b></font></div><br>";
echo "$txt<br><br>";
echo "<div align=right><a href='javascript:reg();'><font color=steelblue size=+1><u><b>������������������</b></u></font></a></div>";

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
	'" alt="�������@Mail.ru"'+' border=0 height=31 width=88/><\/a>')
	if(11<js)d.write('<'+'!-- ')//--></script><noscript><a
	target=_top href="http://top.mail.ru/jump?from=1336155"><img
	src="http://d3.c6.b4.a1.top.list.ru/counter?js=na;id=1336155;t=55"
	border=0 height=31 width=88
	alt="�������@Mail.ru"/></a></noscript><script language="JavaScript" type="text/javascript"><!--
	if(11<js)d.write('--'+'>')//--></script><!--/COUNTER-->


	<!--LiveInternet counter--><script type="text/javascript"><!--
	document.write("<a href='http://www.liveinternet.ru/click' "+
	"target=_blank><img src='http://counter.yadro.ru/hit?t52.6;r"+
	escape(document.referrer)+((typeof(screen)=="undefined")?"":
	";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
	screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
	";"+Math.random()+
	"' alt='' title='LiveInternet: �������� ����� ���������� �"+
	" ����������� �� 24 ����' "+
	"border=0 width=88 height=31><\/a>")//--></script><!--/LiveInternet-->


	<!-- Top Roleplay -->
	<a href="http://top.roleplay.ru/3301" target="top_">
	<img src="http://img.rpgtop.su/rpgtop1.gif" alt="������� ������� ��������" border="0" width="88" height="31"></a> 
	<!--/ Top Roleplay -->



	<!-- Palantir -->
    <a title="������� ������� ������ ��������" href='http://palantir.in/?from=5848' target='_blank'>	
    <script type="text/javascript">
    Md=document;Mnv=navigator;
    Mrn=Math.random();Mn=(Mnv.appName.substring(0,2)=="Mi")?0:1;Mp=0;Mz="p="+Mp+"&";
    Ms=screen;Mz+="wh="+Ms.width+'x'+Ms.height;My="<img src='http://palantir.in/count.php?id=5848&cid=concept6.png";My+="&cntc=none&rand="+Mrn+"&"+Mz+"&referer="+escape(Md.referrer)+'&pg='+escape(window.location.href);My+="'  alt='Palantir' title='������� ������� ������ ��������' border='0' width='88px' height='31px'>";Md.write(My);</script>
    <noscript><img src="http://palantir.in/count.php?id=5848&cid=concept6.png" alt='Palantir' title="������� ������� ������ ��������" border=0 width="88px" height="31px"></noscript>
    </a>
	<!--/ Palantir -->
	
	<!-- begin WebMoney Transfer : accept label -->
	<a href="http://www.megastock.ru/" target="_blank"><img src="http://www.megastock.ru/Doc/88x31_accept/orange_rus.gif" alt="www.megastock.ru" border="0"></a>
	<!-- end WebMoney Transfer : accept label -->
		







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

<script>
	ge( 'frm' ).pwd.onkeydown = auth;
	ge( 'login' ).focus();
</script>
