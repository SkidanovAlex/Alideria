<?

include_once( 'items.php' );
include_once( 'guild.php' );
include_once( 'prof_exp.php' );
include_once( 'locations/tailors_altar/func.php' );
include_js( 'js/skin2.js' );
include_js( 'js/timer2.js' );

if( !isset( $mid_php ) ) die( );

$guild_id = TAILORS_GUILD;
$teachers_id = ALCHEMY_GUILD;
$guild = new Guild( $guild_id );
$teachers = new Guild( $teachers_id );
if( !$guild->LoadPlayer( $player->player_id ) )
{
	if( !$teachers->LoadPlayer( $player->player_id ) )
	{
    	echo "<br>Вы не состоите в <a href=help.php?id={$guilds[$guild_id][1]} target=_blank>Гильдии {$guilds[$guild_id][0]}</a> и не можете тут работать.<br>";
    	echo "Вступить в гильдию можно в <a href=help.php?id=34274 target=_blank>Зале Гильдий</a> в <a href=help.php?id=34265 target=_blank>Городской Управе</a>.<br>";
    }
    else
    {
    	echo "<br><b>Обучение мастерству создания волшебных смесей</b><br>";
    	echo "Работа на Алтаре портных требует от мастера знаний, которые известны только членам гильдии Алхимиков.<br>";
    	echo "Вы можете передать часть своих знаний, достаточную для выполнения работы на алтаре, любому портному.<br> ";
    	echo "Все, что Вам необходимо - пригласить его в эту локацию и взять с собой по одному экстракту каждого цвета.<br><br>";
    	echo "<b>Портные тут:</b> &nbsp; &nbsp; <a href='javascript:refTailors()'>Обновить список</a>";
    	echo "<div id=tailors>";
    	echo "<script>document.write( ".getTailorsList( )." );</script>";
    	echo "</div>";

    	echo "<script>";
    	echo "function refTailors( ) { _( 'tailors' ).innerHTML = '...'; query( 'do.php?act=1', '' ); }";
    	echo "function teach( pid ) { query( 'do.php?act=2&whom=' + pid, '' ); }";
    	echo "</script>";
    }
   	return;
}

$res = f_MQuery( "SELECT * FROM tailors_altar WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	if( !$teachers->LoadPlayer( $player->player_id ) )
	{
    	echo "<br>Работа на Алтаре Портных требует знаний, которые известны только членам гильдии алхимиков.<br>";
    	echo "Найдите представителя этой гильдии, готового обучить вас, и пригласите его в эту локацию.<br>";
    	return;
	}
	else
	{
    	echo "<br>Работа на Алтаре Портных требует знаний, которые известны только членам гильдии алхимиков.<br>";
    	echo "Вы являетесь представителем гильдии алхимиков, но не можете обучить себя или обучать других, так как, будучи портным, смотрите на вопрос с неверного ракурса.<br>";
    	echo "Найдите представителя гильдии алхимиков, не являющегося членом гильдии портных, готового обучить вас, и пригласите его в эту локацию.<br>";
    	return;
	}
}

// ок, мы портной и мы все умеем

echo "<div id=altar_content>";

echo "<script>";

echo "function altar( iid ) { if( confirm( 'Наложить смесь?' ) ) __( 5, iid ); }";
echo "document.write( \"";
echo getAltarContent( );
echo "\");</script>";

echo "</div>";

for( $i = 0; $i < 10; ++ $i ) echo "<br>";
for( $i = 0; $i < count( $cnames ); ++ $i )
	echo "<table><tr><td><b>$i.</b></td><td style='width:20px;border:1px solid black' bgcolor={$cvals[$i]}>&nbsp;</td><td>{$cnames[$i]}</td></tr></table>";

?>
