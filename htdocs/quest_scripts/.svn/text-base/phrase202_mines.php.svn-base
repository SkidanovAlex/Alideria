<?

if( !$mid_php ) die( );

if( $player->GetQuestValue( 25 ) > time( ) )
{
	echo "Перед вами сияют семь огромных столпов пламени. Похоже, сейчас пройти к отшельнику не удастся. Надо дождаться, пока огонь погаснет.<br><br><a href=game.php?phrase=476>Вернуться</a>";
	return;
}

echo "Перед вами 36 мест, где потенциально могут быть дыры. Ровно в семи местах эти дыры есть, в 29-ти - нету. Вы можете попытаться кинуть камень в любое место, если вы кинете камень в дыру, в течение 30 минут вы не сможете попасть к отшельнику. Если вам повезет, и камень упадет не в дыру, по эху вы сможете понять, сколько дыр рядом с тем местом, куда попал камень.<br><br>";

echo "<table><tr><td>";

echo "<table style='border:1px solid black' cellspacing=0 cellpadding=0>";

$id = 0;
for( $i = 0; $i < 6; ++ $i )
{
	echo "<tr>";
	for( $j = 0; $j < 6; ++ $j )
	{
		$b = "1px solid black";
		$border = "";
		if( $j != 5 ) $border .= "border-right: $b;";
		if( $i != 5 ) $border .= "border-bottom: $b;";
		echo "<td onclick='query( \"quest_scripts/phrase202_ajax.php\", \"$id\" )' align=center valign=middle id=td$i$j style='width:36px;height:36px;cursor:pointer;{$border}background-color:#e0c3a0;'>";
		echo "&nbsp;</td>";
		++ $id;
	}
	echo "</tr>";
}

echo "</table>";

echo "</td><td valign=top><div id=txt>&nbsp;</div></td></tr></table>";

?>

<script>

function out( s, m )
{
	var id = 0;
	for( var i = 0; i < 6; ++ i )
		for( var j = 0; j < 6; ++ j )
		{
			if( s.charAt( id ) == '.' ) ;
			else if( s.charAt( id ) == 'x' ) _( 'td' + i + '' + j ).style.backgroundColor = 'darkred';
			else { _( 'td' + i + '' + j ).innerHTML = s.charAt( id ); _( 'td' + i + '' + j ).style.backgroundColor = 'green' }
			++ id;
		}
	if( m == 1 ) _( 'txt' ).innerHTML = '<font color=darkred>Вы кинули камень прямо в дыру. Семь ярких столбов пламени вырвалось из недр пещеры.<br>В течение ближайших получаса пройти к отшельнику точно не получится.</font><br><a href=game.php?phrase=476>Выйти</a>';
	if( m == 2 ) _( 'txt' ).innerHTML = '<font color=darkred>Вы кинули последний камень и точно знаете расположение всех семи дыр. Теперь пройти к отшельнику не составит труда.</font><br><a href=game.php?phrase=477>Идти вперед</a>';
}

<?

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( $arr )
{
	$moo = 0;
	if( $arr['lost'] ) $moo = 1;
	else if( strpos( $arr['f'], '.' ) === false ) $moo = 2;
	if( $moo == 0 ) $arr['f'] = str_replace( 'x', '.', $arr['f'] );
	echo "out( '$arr[f]', $moo )";
}

?>

</script>
