<?

if( !$mid_php ) die( );

$ok = true;
$msg = '';
function reg_err( $a )
{
	global $msg;
	global $ok;
	
	$msg = $a;
	$ok = 0;
}

function reg_login_err( )
{
	reg_err( "Имя существа должно состоять только из русских или только из латинских букв, а так же цифр, знаков подчеркивания и тире" );
}

function correct_login( $a )
{	
	if( ( $a[0] >= 'a' && $a[0] <= 'z' ) || ( $a[0] >= 'A' && $a[0] <= 'Z' ) )
		$eng = 1;
	else if( ( $a[0] >= 'а' && $a[0] <= 'я' ) || ( $a[0] >= 'А' && $a[0] <= 'Я' ) )
		$eng = 0;
	else
	{
		reg_err( "Первый символ имени должен быть буквой" );
		return 0;
	}
	
	$l = strlen( $a );
	for( $i = 1; $i < $l; ++ $i )
	{
		if( ( $a[$i] >= 'a' && $a[$i] <= 'z' ) || ( $a[$i] >= 'A' && $a[$i] <= 'Z' ) )
		{
			if( !$eng )
			{
				reg_login_err( );
				return 0;
			}
		}
		else if( ( $a[$i] >= 'а' && $a[$i] <= 'я' ) || ( $a[$i] >= 'А' && $a[$i] <= 'Я' ) )
		{
			if( $eng )
			{
				reg_login_err( );
				return 0;
			}
		}
		else if( $a[$i] != '-' && $a[$i] != '_' && ( $a[$i] < '0' || $a[$i] > '9' ) )
		{
			reg_login_err( );
			return 0;
		}
	}
	
	return 1;
}

if( isset( $_GET['pet_chose'] ) )
{
	$name = conv_utf( $_GET[name] );
	if( !correct_login( $name ) )
		echo "<script>alert( '$msg' );</script>";
	else
	{
    	$pet_id = (int)$_GET['pet_chose'];
    	if( $pet_id < 1 || $pet_id > 9 ) RaiseError( "Попытка завести несуществуюшего Пета", "phrase576, $pet_id" );
    	f_MQuery( "UPDATE player_pets SET chosen=0 WHERE player_id={$player->player_id}" );
    	f_MQuery( "INSERT INTO player_pets( player_id, pet_id, name, level, chosen ) VALUES ( {$player->player_id}, $pet_id, '$name', 1, 1 )" );
    	$player->SetTrigger( 202 );
    	$player->SetRegime( 0 );
    	f_MQuery( "UPDATE player_quests SET status=1 WHERE quest_id=34 AND player_id={$player->player_id}" );
    	f_MQuery( "DELETE FROM player_talks WHERE player_id={$player->player_id}" );
    	f_MQuery( "DELETE FROM mahjong WHERE player_id={$player->player_id}" );
    	die( "<script>parent.char_ref.location.href='char_ref.php?rnd=".mt_rand()."';</script>" );
	}
}

echo "<b>Хранитель листьев:</b> На зачарованной поляне водится много диковинных зверей. Для каждого существует мелодия, которая привяжет его к тебе. Я знаю девять мелодий, и могу научить играть одну любую. Выбирай.<br><br>";
echo "<table><tr><td><script>FLUl();</script><table>";
$res = f_MQuery( "SELECT * FROM pets WHERE pet_id <= 9 ORDER BY pet_id" );
for( $i = 0; $i < 1; ++ $i )
{
	echo "<tr>";
	for( $j = 0; $j < 9; ++ $j )
	{
		echo "<td>";
		$arr = f_MFetch( $res );
		echo "<script>FUlt();</script>";
		echo "<img width=64 height=90 onclick='if( name = prompt( \"Укажите имя для питомца, будьте внимательны, не допускайте опечаток\", \"имя\" ) ) if(confirm(\"Вы уверены, что {$arr[name]} по имени \" + name + \" - это тот питомец, которого вы хотите?\")) location.href=\"game.php?pet_chose={$arr[pet_id]}&name=\"+encodeURIComponent(name);' style='cursor:pointer' src='images/pets/{$arr[image]}.png' alt='{$arr[name]}' title='{$arr[name]}'>";
		echo "<script>FL();</script>";
		echo "</td>";
	}
	echo "</tr>";
}
echo "</table><script>FLL();</script></td></tr></table>";

?>
