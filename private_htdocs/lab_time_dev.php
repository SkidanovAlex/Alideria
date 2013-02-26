<?
require_once("time_functions.php");


include( 'functions.php' );
include('player.php');

f_MConnect( );

$lab_id = 1;
$lab_num = 3;

for($z = 0; $z < $lab_num; $z++)
{
	$res = f_MQuery( "SELECT count( item_id ) FROM lab_items WHERE lab_id = $lab_id" );
	$arr = f_MFetch( $res );
	if( $arr[0] < 50*$lab_num )
	{
		$res = f_MQuery( "SELECT cell_id FROM lab WHERE lab_id=$lab_id AND z=$z AND tex=0 ORDER BY rand() LIMIT 1" );
		$arr = f_MFetch(  $res );
		$cell_id = $arr[0];
		$res = f_MQuery( "SELECT count( item_id ) FROM lab_items WHERE lab_id=$lab_id AND cell_id=$cell_id" );
		$arr = f_MFetch( $res );
		$res2 = f_MQuery( "SELECT count( mob_id ) FROM lab_mobs WHERE lab_id=$lab_id AND cell_id=$cell_id" );
		$arr2 = f_MFetch( $res2 );
		if( $arr[0] == 0 && $arr2[0] == 0 )
		{
//echo "begin\n";
			$res3 = f_MQuery( "SELECT item_id, prob FROM lab_spec_items WHERE lab_id=$lab_id AND z=$z" );
			$items = array();
			$is = 0;
			$iprob = 0;
			$num_items = f_MNum($res3);
			while($arr3 = f_MFetch($res3))
			{
				$iprob += $arr3[1];
				$items[$is] = $arr3[0];
				$probs[$is] = $arr3[1];
//echo $items[$is]." ".$probs[$is]."\n";
				$is++;
			}
			$item_rnd = mt_rand( 1, $iprob );
			$found = false;
			$tprob = 0;
			$item1 = 0;
			while(!$found && $item1 < $num_items)
			{
				$tprob = $tprob + $probs[$item1];
				if($tprob >= $item_rnd)
				{
					$found = true;
				}
				else
					$item1++;
			}
			if($found)
			{
				$item_id = $items[$item1];
				f_MQuery( "INSERT INTO lab_items (  lab_id, cell_id, item_id ) VALUES ( $lab_id, $cell_id, $item_id )" );
			}
		}
	}

	$res0 = f_MQuery("SELECT lab_spec_mob.mob_id, lab_spec_mob.prob, mobs.avatar FROM lab_spec_mob, mobs WHERE lab_spec_mob.lab_id=$lab_id AND lab_spec_mob.z=$z AND lab_spec_mob.mob_id=mobs.mob_id");
	$mobs = array();
	$mob_imgs = array();
	$i = 0;
	while($arr0 = f_MFetch($res0))
	{
		$mobs[$i] = $arr0[0];
		$mob_imgs[$mobs[$i]] = $arr0[2];
		$probs[$i] = $arr0[1];
		$i++;
	}
	
	$res = f_MQuery( "SELECT count( mob_id ) FROM lab_mobs WHERE lab_id = $lab_id" );
	$arr = f_MFetch( $res );
	if( $arr[0] < 50*$lab_num )
	{
		$res = f_MQuery( "SELECT cell_id FROM lab WHERE lab_id=$lab_id AND z=$z AND tex=0 ORDER BY rand() LIMIT 1" );
		$arr = f_MFetch(  $res );
		$cell_id = $arr[0];
		$res = f_MQuery( "SELECT count( item_id ) FROM lab_items WHERE lab_id=$lab_id AND cell_id=$cell_id" );
		$arr = f_MFetch( $res );
		$res2 = f_MQuery( "SELECT count( mob_id ) FROM lab_mobs WHERE lab_id=$lab_id AND cell_id=$cell_id" );
		$arr2 = f_MFetch( $res2 );
		if( $arr[0] == 0 && $arr2[0] == 0 )
		{
			$id = mt_rand( 0, count( $mobs ) - 1 );
			$mob_id = $mobs[$id];
			$temp_img = $mob_imgs[$mob_id];
			$mob_img = str_replace(".jpg", ".png", $temp_img);
			f_MQuery( "INSERT INTO lab_mobs (  lab_id, cell_id, mob_id, img ) VALUES ( $lab_id, $cell_id, $mob_id, '$mob_img' )" );
		}
	}

}

?>
