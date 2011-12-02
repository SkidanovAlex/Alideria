<?
require_once('time_functions.php');

include_once( 'functions.php' );
include_once( 'player.php' );
include_once( "quest_race.php" );

f_MConnect( );

$tm = time( );
$res = f_MQuery( "SELECT * FROM auction WHERE deadline <= $tm + 2" );
while( $arr = f_MFetch( $res ) )
{
	$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$arr[item_id]" ) );
	if( '' == $iarr['name13'] ) $iarr['name13'] = $iarr['name'];
	if( '' == $iarr['name2_m'] ) $iarr['name2_m'] = $iarr['name'];
	$nm = my_word_str( $arr['number'], $iarr['name'], $iarr['name13'], $iarr['name2_m'] );
	if( $arr['number'] > 1 ) $nm = $arr['number'].' '.$nm; 


	$plr = new Player( $arr['player_id'] );
	if( $arr['last_bet_by'] == 0 )
	{
		$plr->syst3( "�� ������ ���� $nm �� ���� ������� �� ����� ������, ��� ��������� ���" );
		$plr->AddItems( $arr['item_id'], $arr['number'] );
		$plr->AddToLogPost( $arr['item_id'], $arr['number'], 15 );
	}
	else
	{
		$player = new Player( $arr['last_bet_by'] );

		$price = $arr['cur_price'];
		$ret = ceil( $price * 0.95 );

		$player->AddItems( $arr['item_id'], $arr['number'] );
		$player->AddToLogPost( $arr['item_id'], $arr['number'], 15 );

		$plr->AddMoney( $ret );
		$plr->AddToLogPost( 0, $ret, 15 );
		$plr->syst3( "����� {$player->login} �� ������ ������ ����� ��� ��� $nm �� $price. �� ��������� $ret ".my_word_str( $ret, "������", "�������", "��������" ) );
		$plr->syst2( "/items" );

		$player->syst3( "�����������, ���� ������ �� �������� ����� �� �������. �� ��������� $nm" );
		$player->syst2( "/items" );

		updateQuestStatus ( $player->player_id, 2507 );
	}
}
f_MQuery( "DELETE FROM auction WHERE deadline <= $tm + 2" );

?>
