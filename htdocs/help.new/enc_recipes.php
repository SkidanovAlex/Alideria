<?

include_once( "functions.php" );
include_once( "items.php" );
include_once( "player.php" );
include_once( "skin.php" );

f_MConnect( );
$player = new Player( 172 );
$stats = $player->getAllAttrNames( );

include_once( "craft_functions.php" );

$where = "";
if( isset( $_GET['recipe_id'] ) )
{
	$id = $_GET['recipe_id'];
	settype( $id, 'integer' );
	$where .= " AND recipe_id=$id";
}
if( isset( $_GET['prof'] ) )
{
	$prof = $_GET['prof'];
	settype( $prof, 'integer' );
	$where .= " AND prof=$prof";
}
if( isset( $_GET['minrank'] ) )
{
	$minrank = $_GET['minrank'];
	settype( $minrank, 'integer' );
	$where .= " AND rank >= $minrank";
}
if( isset( $_GET['maxrank'] ) )
{
	$minrank = $_GET['maxrank'];
	settype( $maxrank, 'integer' );
	$where .= " AND rank <= $maxrank";
}
if( isset( $_GET['minlevel'] ) )
{
	$lvl = $_GET['minlevel'];
	settype( $lvl, 'integer' );
	$where .= " AND level>=$lvl";
}
if( isset( $_GET['maxlevel'] ) )
{
	$lvl = $_GET['maxlevel'];
	settype( $lvl, 'integer' );
	$where .= " AND level<=$lvl";
}

if( $where !== "" )
{
	$res = f_MQuery( "SELECT * FROM recipes WHERE 2=2 $where ORDER BY prof, rank, level" );
	if( !f_MNum( $res ) ) echo "<center><i>Нет таких рецептов</i></center>";
	else
	{
		echo "<table style='border:1px solid black'><tr><td>";//ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
		echo "<center><table id=s_table><tr><td>";
		
		echo outRecipes( $res, 0 );
		
		
		echo "</td></tr></table></center>";

//		ScrollLightTableEnd( );
//		echo "</td></tr></table>";
		echo "</div>";
		/*ScrollLightTableEnd();*/echo "</td></tr></table>";

	}
}

/*		echo "<center><table width=80%><tr><td>";
		ScrollLightTableStart();
		echo "<center><table><form action=help.php method=get>";

		$levels = Array( 3 => 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16 );
		$ranks = Array( 0 => 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );
		echo "<input type=hidden name=id value=1015>";
		echo "<tr><td><b>Гильдия: </b></td><td>".create_select_global( "prof", $prof_names, 104 )."</td></tr>";
		echo "<tr><td><b>Мин.уровень: </b></td><td>".create_select_global( "minlevel", $levels, 3 )."</td></tr>";
		echo "<tr><td><b>Макс.уровень: </b></td><td>".create_select_global( "maxlevel", $levels, 16 )."</td></tr>";
		echo "<tr><td><b>Мин.ранг: </b></td><td>".create_select_global( "minrank", $ranks, 0 )."</td></tr>";
		echo "<tr><td><b>Макс.ранг: </b></td><td>".create_select_global( "maxrank", $ranks, 10 )."</td></tr>";
		echo "<tr><td>&nbsp;</td><td><input class=m_btn value=Показать type=submit></td></tr>";

		echo "</form></table></center>";

		ScrollLightTableEnd( );
		echo "</td></tr></table></center>";
*/
?>
