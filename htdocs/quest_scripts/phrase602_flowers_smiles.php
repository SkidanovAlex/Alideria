<?

include_once( 'smiles_list.php' );

if( !$mid_php ) die( );

$item_ids = Array (
	15,
	12,
	13,
	11,
	14
);

$nm = Array (
	'�������',
	"�����",
	"�������",
	"���������",
	"���������"
);

if( isset( $_GET['chosen'] ) && !$player->HasTrigger( 216 ) )
{
	$id = (int)$_GET['chosen'];
	if( $id >= 0 && $id < 5 && $player->DropItems( $item_ids[$id], 200 ) || $player->player_id == 173 )
	{
		$sid = $id + 4;
		f_MQuery( "INSERT INTO paid_smiles ( player_id, set_id, expires ) VALUES ( {$player->player_id}, $sid, 2147483647 );" );
		$player->SetTrigger( 216 );
		$player->AddToLogPost( $item_ids[$id], - 200, 20 );
	} else echo "<font color=darkred>� ��� �� ������� ������</font><br>";
}

if( !$player->HasTrigger( 216 ) )
{
	echo "<li><a href=game.php?phrase=1248>����</a><br>";

    echo "<br><i>�������� ������ �������� ������ ������� � ���� ����, ������ �����������</i><br>";

    for( $i = 0; $i < 5; ++ $i )
    {
    	$si = $i + 4;
    	echo "<br>�� <b>200 {$nm[$i]}</b> � ����� �������� �������� ���� ��� ����� �������: <img src=images/smiles/{$vsmiles[$si][0]}.gif><br><li><a href='javascript:buy($i)'>������</a><br>";
    }
}
else
{
	echo "<b>����������� ��������: </b>���, ������� ���� ".(($player->sex)?"������ �������":"������ �������").". ��� �� �� ������ ��� ���� � ���� ��������. �� ����� ���� ������ ��������� ����...<br>��� �� ��� ������� �� �������...<br><br>";
	echo "<li><a href=game.php?phrase=1248>����</a><br>";

}

?>
<script>
function buy( id )
{
	if( confirm( '������ ��������� �������? �� ���������, �� �� ������� ������ ������� ������ ������� � �������� ����� �����!' ) )
		location.href='game.php?chosen=' + id;
}
</script>