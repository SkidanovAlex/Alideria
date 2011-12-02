<?

include_once( "help/guilds_common.php" );

		echo "<table><tr><td>";ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
		echo "<center><table id=s_table><tr><td>";

?>

<div id="header" align="left">Гильдия Старателей</div><br />


Основная локация: <a href=help.php?id=34259>Пещеры</a><br>
<? ShowGuildRating( 103 ); ?><br>
<br>
Для того, чтобы стать одним из cтарателей, Вам нужно всего лишь выполнить задание мастера гильдии и заплатить 200 дублонов за вступление.<br>
<br>
Получив членство в гильдии, можно отправляться в прииск семи старателей, расположенный на нулевой глубине пещер, и искать руды.<br>
<? ShowProfExpText( "найденные руды" ); ?>
<br>
Гильдия открывает перед вами одну основную возможность:<br>
1. В прииске семи старателей в <a href=help.php?id=34259>пещерах</a> вы можете искать руды.
<? ShowKopkaText( ); ?><br>
<br>

Во время поиска вы можете найти следующее:<br>

<?

ShowGuildItems( 103 );

		echo "</td></tr></table></center>";
		echo "</div>";
		ScrollLightTableEnd();echo "</td></tr></table>";

?>

