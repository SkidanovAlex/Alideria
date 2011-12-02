<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251" />
<title>Алидерия</title>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<link rel="icon" type="image/png" href="favicon.png" />
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="/js/chat.php"></script>
<script src="/js/clans.php"></script>
<script src="/js/ii_a.js"></script>
</head>
<frameset rows=0,0,*,30% border=0 id='fs_main'>
	<frame name=chat_ref noresize>
	<frame name=chat_inf noresize>
	<frame name=game<? if (strstr(getenv("HTTP_USER_AGENT"),"MSIE")) print( " scrolling=no" ); ?>>
	<frameset rows=0,35,*,35 border=0 borderwidth=0>
	    <frameset cols=50%,* border=0>
			<frame name=char_ref src=char_ref.php scrolling="no" noresize>
			<frame name=game_ref scrolling="no" noresize>
		</frameset>

		<frame name=chat_at src=chat_params.php scrolling="no" noresize>

		<frameset cols=*,250 border=0>
			<frame scrolling="auto" name=chat src=chat.php marginheight="0" marginwidth="0">
			<frame scrolling="auto" name=chat_who src=chat_who.php marginheight="0" marginwidth="0">
		</frameset>

		<frame name=chat_in src=chat_in.php scrolling="no" noresize>
	</frameset>
</frameset>
</html>