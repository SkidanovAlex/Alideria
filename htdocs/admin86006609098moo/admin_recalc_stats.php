<?

// ������������� ��� �����
include_once( '../functions.php' );

include_once( '../attrib_relations.php' );
include_once( '../player.php' );
include_once( '../items.php' );
include_once( '../clan_wonders.php' );
include_once( '../feathers.php' );

$dont_check_feather = true;

f_MConnect( );

$pg = 0;
if( isset( $_GET['prev_pg'] ) ) $pg = 1 + (int)$_GET['prev_pg'];

$res = f_MQuery( "SELECT player_id, level FROM characters WHERE length(pswrddmd5) > 10 LIMIT ".( $pg * 1000 ).',1000' );
$moar = mysql_num_rows( $res ); // �������� �� ������?

while( $arr = f_MFetch( $res ) )
{
	echo "$arr[player_id]\n";
	$ares = f_MQuery( "SELECT * FROM player_attributes WHERE player_id = $arr[player_id]" );

	$attrs = Array( );
	$rattrs = Array( );

	$rress = f_MQuery( "SELECT * FROM attributes" );
	while( $aarr = f_MFetch( $rress ) )	
	{
		$attrs[$aarr['attribute_id']] = 0;
		$rattrs[$aarr['attribute_id']] = 0;
	}

	$attrs[101] = 100 * $arr[level];
	$rattrs[101] = 100 * $arr[level];

	while( $aarr = f_MFetch( $ares ) )
	{
		if( $aarr['attribute_id'] >= 30 &&  $aarr['attribute_id'] < 60 )
		{
			$attrs[$aarr['attribute_id']] += $aarr['actual_value'];
			$rattrs[$aarr['attribute_id']] += $aarr['actual_value'];
		}
		if( isset( $attrib_rels[$aarr[attribute_id]] ) )
		{
			foreach( $attrib_rels[$aarr[attribute_id]] as $v )
			{
				$attrs[$v] += $aarr[actual_value];
				$rattrs[$v] += $aarr[actual_value];
			}
		}
	}

	$ires = f_MQuery( "SELECT * FROM items, player_items WHERE items.item_id = player_items.item_id AND player_items.player_id = $arr[player_id] AND weared != 0" );
	while( $iarr = f_MFetch( $ires ) )
	{
		$v = ParseItemStr( $iarr[effect] );
		foreach( $v as $a=>$b ) 
		{
			if( isset( $attrs[$a] ) )
				$rattrs[$a] += $b;
			if( isset( $attrib_rels[$a] ) )foreach( $attrib_rels[$a] as $v2 )
				if( isset( $attrs[$v2] ) )
					$rattrs[$v2] += $b;

		}
	}

	$attrs[1] = $attrs[101];
	$rattrs[1] = $rattrs[101];

	$plr = new Player( $arr['player_id'] );

	$clan_id = $plr->clan_id;
	if( $clan_id )
	{
		$cres = f_MQuery( "SELECT wonder_id FROM clan_wonders WHERE clan_id=$clan_id AND stage=100" );
		while( $carr = f_MFetch( $cres ) )
			applyWonderArr( $carr[0], $rattrs, $plr );
	}

	$regime = $plr->regime;
	foreach( $attrs as $a=>$b ) if( $a < 1000 )
	{
//		echo "$a => $b <br>\n";
		if( $regime == 100 ) f_MQuery( "UPDATE player_attributes SET real_value={$rattrs[$a]}, actual_value=$b WHERE player_id=$arr[player_id] AND attribute_id=$a" );
		else f_MQuery( "UPDATE player_attributes SET value={$rattrs[$a]}, real_value={$rattrs[$a]}, actual_value=$b WHERE player_id=$arr[player_id] AND attribute_id=$a" );
	}
	
	if( $regime != 100 )
	{
    	$fres = f_MQuery( "SELECT * FROM player_feathers WHERE player_id = {$plr->player_id}" );
    	while( $farr = f_MFetch( $fres ) )
    	{
    		doFeather( $plr, $farr['feather_id'] );
    	}
	}
	
	// �������
	$playerEffects = f_MQuery( 'SELECT effect FROM player_effects WHERE player_id = '.$plr->player_id );
	while( $effect = f_MFetch( $playerEffects ) )
	{
		$item_arr = ParseItemStr( $effect[effect] );
		foreach( $item_arr as $a=>$b )
		{
			$plr->AlterRealAttrib( $a, $b );
		}
	}
}


if( $moar )
{
	echo "<a href=admin_recalc_stats.php?prev_pg=$pg>������...</a>
			<script>document.location.href = 'admin_recalc_stats.php?prev_pg=$pg';</script>";
}
else
{
	echo "<h3>Yeah!</h3>";
}
?>
