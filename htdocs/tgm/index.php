<?
/* @author = undefined
 * @version = 1.0.0.1
 * @date = 12 ������� 2011
 * @about = �������� �������������� ��������, ������� � �������� ������������
 */

	// ���������� � ��������� ������ ������������������ �������
	Header( 'Content-Type: text/html; charset=cp1251' );

	// ����������� ������������ ������
	require_once( '../functions.php' );	// ������ ����������� ����������� ������� ����
	require_once( '../player.php' );		// ��������������� �����, ���������� ��������� �������� ��� �������������

	// �������� �� �������������� � ����
	if( !check_cookie( ) )
	{
		die( );	
	}
	else
	{
		$Demiurg = new Player( $_COOKIE['c_id'] );

		if( $Demiurg->Rank( ) != 1 )
		{
			die( );
		}
	}
	
	// ������ ���������� �������� {������ ������������ ��� ��������� ������������ �������}
	$services = array( );
	$services['main'] = '���������';
	// ���������
	$services['players-teleport'] = '��������';
	$services['players-freedom'] = '������������';
	$services['players-moders'] = '����������';
	$services['players-clearinfo'] = '���������� ����';
	$services['players-penalty'] = '������������';
	// ����������
	$services['statistic-online'] = '������ �������';
	$services['statistic-doubloons'] = '��������� ������';
	$services['statistic-locations'] = '������������ �������';
	$services['statistic-regs'] = '���������� �����������';
	$services['statistic-quests'] = '����������� �������';
	// ������
	$services['finance-report'] = '���������� �����';
	$services['finance-partner'] = '����� ��������';
	$services['finance-talants-move'] = '�������� ��������';
	// �����
	$services['tools-premiator'] = '���������';
	$services['tools-gifter'] = '���������';
	$services['tools-changeClanLeader'] = '����� ����� �����';
	$services['tools-deleteClan'] = '������� �����';
	$services['tools-effectools'] = '�����������';
	
	// �� ����� ��������� ������?
	$serviceIdentity = ( $services[$_GET['service']] ) ? $_GET['service'] : 'main';
	$serviceTitle = $services[$serviceIdentity];
?>
<html>
<head>
	<title><?=$serviceTitle.' - '?>������ �����</title>
	<link rel="stylesheet" type="text/css" href="/css/default.css" />
	<link rel="stylesheet" type="text/css" href="/css/tgm.css" />
	<script src="/js/jquery/main.js"></script>
	<script src="/js/tgm/main.js"></script>
</head>
<body>
	<div id="head" class="container">
		<div id="title">
			<?=$serviceTitle?>
		</div>
		<div id="menu">
			<a href="#" id="menuEditors" onmouseover="submenu.editors.show( )">���������</a>
			<div class="submenu" id="submenuEditors" onmouseout="submenu.editors.hide( event )">
				...
			</div>
			<a href="#" id="menuQuests" onmouseover="submenu.quests.show( )">������</a>
			<div class="submenu" id="submenuQuests" onmouseout="submenu.quests.hide( event )">
				...
			</div>
			<a href="#" id="menuLocations" onmouseover="submenu.locations.show( )">�������</a>
			<div class="submenu" id="submenuLocations" onmouseout="submenu.locations.hide( event )">
				...
			</div>
			<a href="#" id="menuPlayers" onmouseover="submenu.players.show( )">���������</a>
			<div class="submenu" id="submenuPlayers" onmouseout="submenu.players.hide( event )">
				<a href="?service=players-teleport">��������</a><br />
				<a href="?service=players-freedom">������������</a><br />
				<a href="?service=players-moders">����������</a><br />
				<a href="?service=players-clearinfo">���������� ����</a><br />
				<a href="?service=players-penalty">������������</a><br />
			</div>
			<a href="#" id="menuMoney" onmouseover="submenu.money.show( )">������</a>
			<div class="submenu" id="submenuMoney" onmouseout="submenu.money.hide( event )">
				<a href="?service=finance-report">���������� �����</a><br />
				<a href="?service=finance-partner">����� ��������</a><br />
				<a href="?service=finance-talants-move">�������� ��������</a><br />
			</div>
			<a href="#" id="menuStatistic" onmouseover="submenu.statistic.show( )">����������</a>
			<div class="submenu" id="submenuStatistic" onmouseout="submenu.statistic.hide( event )">
				<a href="?service=statistic-online">������ �������</a><br />
				<a href="?service=statistic-doubloons">��������� ������</a><br />
				<a href="?service=statistic-locations">������������ �������</a><br />
				<a href="?service=statistic-regs">���������� �����������</a><br />
				<a href="?service=statistic-quests">����������� �������</a><br />
			</div>
			<a href="#" id="menuTools" onmouseover="submenu.tools.show( )">�����</a>
			<div class="submenu" id="submenuTools" onmouseout="submenu.tools.hide( event )">
				<a href="?service=tools-premiator">���������</a><br />
				<a href="?service=tools-gifter">���������</a><br />
				<a href="?service=tools-changeClanLeader">����� ����� �����</a><br />
				<a href="?service=tools-deleteClan">������� �����</a><br />
				<a href="?service=tools-effectools">�����������</a><br />
			</div>
		</div>
		<div style="clear: both"></div>
	</div>
	<script>
		var submenu = {};
		
		submenu.editors = new Submenu( $( '#menuEditors' ), $( '#submenuEditors' ) );
		submenu.quests = new Submenu( $( '#menuQuests' ), $( '#submenuQuests' ) );
		submenu.locations = new Submenu( $( '#menuLocations' ), $( '#submenuLocations' ) );
		submenu.players = new Submenu( $( '#menuPlayers' ), $( '#submenuPlayers' ) );
		submenu.money = new Submenu( $( '#menuMoney' ), $( '#submenuMoney' ) );
		submenu.statistic = new Submenu( $( '#menuStatistic' ), $( '#submenuStatistic' ) );
		submenu.tools = new Submenu( $( '#menuTools' ), $( '#submenuTools' ) );		
	</script>
	<div id="content" class="container">
		<?
			// ����������� ��������� ���� �������
			if( !include_once( './services/'.$serviceIdentity.'.php' ) )
			{
				echo '- �������, � ��� ��������.<br /> - ��� � ���?<br />- �������, ��������� ������ <b>'.$serviceIdentity.'.</b><br />- PSSSSHHHHHHHHHHHHHHHH<br />- PSSHHHHHHHHHH<br />- PSSSHHHHHHHHHHHHH';
			}		
		?>
	</div>
</body>
</html>
