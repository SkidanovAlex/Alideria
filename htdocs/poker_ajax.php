<?php

if ( $mid_php )
	return;

header("Content-type: text/html; charset=windows-1251");

require_once("no_cache.php");
include_once("functions.php");
include_once("player.php");

$link = f_MConnect();

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

require_once("poker_class.php" );
require_once( "poker_func.php" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$player_id = $player->player_id;

$table_id = get_player_table( );
if ( $table_id >= 0 )
{
	$table = new PokerTable( $table_id );

	$id = $HTTP_RAW_POST_DATA;
	$pos = strpos( $id, ' ' );
	if ( $pos === false )
		return;
	$first_part = substr( $id, 0, $pos );
	$second_part = substr( $id, $pos + 1 );

	if ( strcmp( $first_part, 'refresh' ) == 0 )
	{
	}
	else
	if ( strcmp( $first_part, 'leave' ) == 0 )
	{
		$table->ActionLeave( );
		echo "location.href = location.href;";
		return;
	}
	else
	if ( strcmp( $first_part, 'fold' ) == 0 )
	{		$table->ActionFold( );
	}
	else
	if ( strcmp( $first_part, 'call' ) == 0 )
	{
		$table->ActionCall( );
	}
	else
	if ( strcmp( $first_part, 'raise' ) == 0 )
	{
		settype( $second_part, 'integer' );
		$table->ActionRaise( $second_part );
	}
	else
	if ( strcmp( $first_part, 'all_in' ) == 0 )
	{
		$table->ActionAllIn( );
	}
	else
	{		echo "alert( 'What?' );";	}
	$table->Draw( );
}
else
{
	$id = $HTTP_RAW_POST_DATA;
	$pos = strpos( $id, ' ' );
	if ( $pos === false )
		return;
	$first_part = substr( $id, 0, $pos );
	$second_part = substr( $id, $pos + 1 );
	if ( strcmp( $first_part, 'refresh' ) == 0 )
	{
		echo "if ( IsInTable( ) ) { location.href = location.href; } else { ";		echo "_( 'bets' ).innerHTML = '<center>". getPokerBets( ) ."</center>'; }";
	}
	else
	if ( strcmp( $first_part, 'join' ) == 0 )
	{
		settype( $second_part, 'integer' );
		$table_id = $second_part;
		if ( $table_id > 0 && $table_id < 1000000 )
		{
			$table = new PokerTable( $table_id );
			$table->AddPlayer( $player_id );
			echo "location.href = location.href;";
		}
		return;
	}
	else
	if ( strcmp( $first_part, 'create_table' ) == 0 )
	{
		settype( $second_part, 'integer' );
		$bet = $second_part;
		$table_id = create_new_table( $bet );
		if ( $table_id > 0 && $table_id < 1000000 )
		{
			$table = new PokerTable( $table_id );
			$table->AddPlayer( $player_id );
			echo "location.href = location.href;";
		}
		return;
	}
}
?>