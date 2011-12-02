<?

	include_once( 'functions.php' );
	
	f_MConnect( );

?>

<script src=js/ii.js></script>
<script src=js/tooltips.php></script>
<script src=functions.js></script>

<div id="content_text"><br />

<?
if( !isset( $_GET['beast_id'] ) )
{
}
else
{
	$stats = array( );
	$aimgs = array( );
	$aclrs = array( );

	$res = f_MQuery( "SELECT * FROM attributes" );
	while( $arr = f_MFetch( $res ) )
	{
		$stats[$arr['attribute_id']] = $arr['name'];
		$aimgs[$arr['attribute_id']] = $arr['icon'];
		$aclrs[$arr['attribute_id']] = $arr['color'];
	}

	include_once( 'beast.php' );
	include_once( 'card.php' );

	$mob_id = ( int )$_GET['beast_id']; 
	$b = new Beast( $mob_id );
/*	echo "<center><a href=help.php?id=1016>Назад к списку</a><br><br><table><tr><td valign=top><script>document.write( "; $b->ARect( ); echo " );</script><br><center>"; $b->ShowGlobalAttributes( );

	echo "<br><br>";

	$res = f_MQuery( "SELECT i.*, m.number, m.chance FROM items as i INNER JOIN mob_items as m ON i.item_id = m.item_id WHERE m.mob_id=$mob_id" );
	echo "<table width=200><tr><td>";ScrollTableStart( );
	echo "<b>Где обитает:</b><br>";
	echo "<table>";
	echo "<tr><td align=center>";
	if( $b->loc == 2 ) echo "В Столице";
	else if( $b->loc == 3 )
	{
		echo "В реке, ";
		$larr = f_MFetch( f_MQuery( "SELECT title FROM  loc_texts WHERE loc=3 AND depth={$b->dfd}" ) );
		echo $larr[0];
	}
	else if( $b->loc == 1 )
	{
		include_once( 'forest_functions.php' );
		echo $forest_names[$b->dfd]." в западном лесу";
	}
	else if( $b->mnd == 33 ) echo "В Лабиринте Кошмаров";
	else if( $b->mnd == 1000 ) echo "<i>Информация скрыта</i>";
	else echo "В пещере<br>Глубина: {$b->mnd} - {$b->mxd}";
	echo "</td></tr>";
	echo "</table>";
	ScrollTableEnd( );echo "</td></tr></table>";



	 echo "</center></td><td valign=top>";


	echo "<table width=300><tr><td>";ScrollTableStart( );
	$b->ShowCards( );
	ScrollTableEnd( );echo "</td></tr></table>";

	echo "<br><br>";

	echo "<table width=300><tr><td>";ScrollTableStart( );
	$b->ShowDrop();
   	ScrollTableEnd( );echo "</td></tr></table>";

   	echo "<br><br>";
	echo "<table width=300><tr><td>";ScrollTableStart( );
	echo "<b>Описание:</b><br>";
	echo "<table>";
	echo "<tr><td><i>{$b->descr}</i></td></tr>";
	echo "</table>";
	ScrollTableEnd( );echo "</td></tr></table>";

    echo "</td></tr></table></center>"; */
    
    function moo( $a ) { return $a ? $a : 0; }

?>
    


	<div id="header" align="left"><?=$b->login?></div>
	<p align="left">
		<?=$b->descr?> <br /><br />
		<table id="s_table"><tr><td align=left width=250 valign=top>
			<table id="s_table" width=144><tr><td><img src=images/icons/attributes/hp.gif> <b><?=$b->attrs[1]?></b></td>
			                     <td align=right><b><?=moo($b->attrs[222])?>&nbsp;<img src=images/icons/attributes/r.gif></b></td></tr></table>
			<table width=144 height=137 id="s_table" style="background:url('images/backgrounds/zoobackground.jpg');	background-repeat: no-repeat; background-position: top;"><tbody>
			<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			<tr valign=bottom align="center"  style="color:0000ff; font-weight: bold;"><td></td><td><img src="images/icons/w_ic1.gif" /><br /><?=moo($b->attrs[30])?></td><td><img src="images/icons/water.gif" /><br /><?=moo($b->attrs[130])?></td><td><img src="images/icons/awater.gif" /><br /><?=moo($b->attrs[131])?></td><td><img src="images/icons/swater.gif" /><br /><?=moo($b->attrs[132])?></td><td><img src="images/icons/luck.gif" /><br /><?=moo($b->attrs[13])?></td><td></td></tr>
			<tr align="center" valign=bottom style="color:00aa00; font-weight: bold;"><td></td><td><img src="images/icons/e_ic1.gif" /><br /><?=moo($b->attrs[40])?></td><td><img src="images/icons/nature.gif" /><br /><?=moo($b->attrs[140])?></td><td><img src="images/icons/anature.gif" /><br /><?=moo($b->attrs[141])?></td><td><img src="images/icons/snature.gif" /><br /><?=moo($b->attrs[142])?></td><td><img src="images/icons/hand.gif" /><br /><?=moo($b->attrs[15])?></td><td></td></tr>
			<tr align="center" style="color:ff0000; font-weight: bold;"><td></td><td><img src="images/icons/f_ic1.gif" /><br /><?=moo($b->attrs[50])?></td><td><img src="images/icons/fire.gif" /><br /><?=moo($b->attrs[150])?></td><td><img src="images/icons/afire.gif" /><br /><?=moo($b->attrs[151])?></td><td><img src="images/icons/sfire.gif" /><br /><?=moo($b->attrs[152])?></td><td><img src="images/icons/criticalhit.gif" /><br /><?=moo($b->attrs[16])?></td><td></td></tr>
			<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>
			</tbody></table>
	 </td><td align=center width=150 valign=top>
		 <img align="right" src="images/avatars/<?=str_replace(".jpg",".png",$b->avatar);?>" id="label" />
	 </td><td align=right width=250 valign=top>
			
		<table width=200 id="s_table"><tbody>
			<tr><td><b>
			Где обитает:</b><br />
			<?
	if( $b->loc == 2 ) echo "В Столице";
	else if( $b->loc == 3 )
	{
		echo "В реке, ";
		$larr = f_MFetch( f_MQuery( "SELECT title FROM  loc_texts WHERE loc=3 AND depth={$b->dfd}" ) );
		echo $larr[0];
	}
	else if( $b->loc == 1 )
	{
		include_once( 'forest_functions.php' );
		echo $forest_names[$b->dfd]." в западном лесу";
	}
	else if( $b->mnd == 33 ) echo "В Лабиринте Кошмаров";
	else if( $b->mnd == 1000 ) echo "<i>Информация скрыта</i>";
	else echo "В пещере<br>Глубина: {$b->mnd} - {$b->mxd}";
			
			?>
			</td></tr>

			<tr><td></td><td>
			<tr><td><?$b->ShowCards( );?></td></tr>
		</tbody></table>
	 </td></tr></table>

		<table id="s_table"><tbody><tr><td>
			<?$b->ShowDrop();?>
		</td></tr></tbody></table>
	</p>
</div>

<?
}

?>
