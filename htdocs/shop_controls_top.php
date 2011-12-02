<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link rel="stylesheet" type="text/css" href="style2.css">
<html><body>

<?

include_once( "functions.php" );
include_once( "player.php" );
include_once( "shop.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "�������� ��������� Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$shop_id = $HTTP_GET_VARS['shop_id'];
settype( $shop_id, 'integer' );

if( !( $player->IsShopOwner( $shop_id ) ) )
	RaiseError( "�� �� ��������� ���������� ����� ��������" );
	
$shop = new Shop( $shop_id );
	
?>

<table>
<tr><td>��������:&nbsp;</td><td><input onChange='chgname( );' name=shpname id=shpname type=text size=50 maxlength=50 class=te_btn></td><td>&nbsp;</td></tr>
<tr><td>���������&nbsp;��&nbsp;����&nbsp;�������:&nbsp;</td><td><input onChange='chgsellmul( );' name=sellmul id=sellmul type=text size=7 maxlength=7 class=te_btn>&nbsp;%</td><td rowspan=2 valign=top>���� �� ������� ��� ������� ������ ���������� ���� ����� ��������� ��� ������������ ���. ���� � ���������������� ��������� <i>������ ����</i> � ����� ���� �������/���� ������� ���� ���� ����� ������ �������� -1</td></tr>
<tr><td>���������&nbsp;��&nbsp;����&nbsp;�������:&nbsp;</td><td><input onChange='chgbuymul( );' name=buymul id=buymul type=text size=7 maxlength=7 class=te_btn>&nbsp;%</td></tr>

<?
		$regime_names = array( 0 =>	"�������/�������",
			"������ �������",
			"������ �������",
			"������� ������",
			"������. ����������" );
		
		print("<TR><TD>����� ��������:</TD><TD>");

		/*$regimes = array( 1 =>	0,
			1,
			2,
			3,
			4 );*/
		
		
			
		print("<SELECT name='shprgm' id='shprgm' class=c_btn onChange='change_regime( );'>");
		
		for ( $i=0; $i<sizeof($regime_names); $i++ )
    	{

			print("<OPTION value='$i'");
			print(">$regime_names[$i]</OPTION>");

		}
		    
   		print("</SELECT></TD><td>&nbsp;</td></tr>");

		$shop_price = 2000;
		$shop->name = AddSlashes( $shop->name );
				
?>

<!--<tr><td valign=top>��������� ������� ��&nbsp;</td><td valign=top><input value=0 onChange='increace_cap( );' name=icap id=icap type=text size=7 maxlength=7 class=te_btn>&nbsp;����</td><td valign=top>

<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td valign=top>
<div id=capcost name=capcost>&nbsp;</div></td><td align=center>
<button onClick='sell_shop();' class=c_btn>������� �������</button><br>
<div id=scs name=scs>&nbsp;</div>
</td></tr></table>

</td></tr>-->
</table>

<script>

	var shop_name = <? print( "\"{$shop->name}\"" ); ?>;
	var price_buy_mul = <? print $shop->buy_mul; ?>;
	var price_sell_mul = <? print $shop->sell_mul; ?>;
	var shop_regime = <? print $shop->regime; ?>;
	var shop_capacity = <? print $shop->capacity; ?>;
	var capacity_cost = <? print $shop->cost; ?>;
	
	var old_name = shop_name;
	var old_buy_mul = price_buy_mul;
	var old_sell_mul = price_sell_mul;
	var old_regime = shop_regime;
	var old_capacity = shop_capacity;
	
	var max_mul = 100000;
	
	function ge( a )
	{
		return document.getElementById( a );
	}
	
	function toDouble( a )
	{
		a = parseFloat( a );
		if( isNaN( a ) ) return 0;
		return a;
	}
	
	function toInt( a )
	{
		a = parseInt( a );
		if( isNaN( a ) ) return 0;
		return a;
	}
	
	function chgname( )
	{
		shop_name = ge( 'shpname' ).value;
		parent.bt.refr( );
	}
	
	function chgbuymul( )
	{
		price_buy_mul = toDouble( ge( 'buymul' ).value );
		if( price_buy_mul < 0 ) { price_buy_mul = 0; refr( ); }
		if( price_buy_mul > max_mul ) { price_buy_mul = max_mul; refr( ); }
		parent.md.refr( );
		parent.bt.refr( );
	}
	
	function chgsellmul( )
	{
		price_sell_mul = toDouble( ge( 'sellmul' ).value );
		if( price_sell_mul < 0 ) { price_sell_mul = 0; refr( ); }
		if( price_sell_mul > max_mul ) { price_sell_mul = max_mul; refr( ); }
		parent.md.refr( );
		parent.bt.refr( );
	}

	function change_regime( )
	{
		shop_regime = ge( 'shprgm' ).selectedIndex;
		parent.md.refr( );
		parent.bt.refr( );
	}
	
/*	function increace_cap( )
	{
		shop_capacity = old_capacity + toInt( ge( 'icap' ).value );
		if( shop_capacity < old_capacity )
		{
			shop_capacity = old_capacity;
			refr( );
		}
		parent.bt.refr( );
	}
*/	
	function sell_shop( )
	{
		if( confirm( '�� ������� ��� ������ ������� �������?' ) )
		{
			if( confirm( '��� ���� � ������� � �������� ����� �������, �� �������, ��� ������ ������� �������?' ) )
			{
				if( confirm( '�� ����� �������, ��� ������ ������� �������?' ) )
				{
					parent.location.href='shop_controls_sell_shop.php';
				}
			}
		}
	}
	
	function refr( )
	{
		ge( 'shpname' ).value = shop_name;
		ge( 'buymul' ).value = price_buy_mul;
		ge( 'sellmul' ).value = price_sell_mul;
		ge( 'shprgm' ).selectedIndex = shop_regime;
//		ge( 'icap' ).value = ( shop_capacity - old_capacity );
		
//		ge( 'capcost' ).innerHTML = "(" + capacity_cost + " �� �����, ������ ����: " + old_capacity + ")"
	}
	
	refr( );
//	ge( 'scs' ).innerHTML = '(�� �������� ' + toInt( shop_capacity * capacity_cost * 0.5 ) + ' ������' + ')';
	<?
		print "parent.md.location.href='shop_controls_mid.php?shop_id=$shop_id';";
	?>

</script>

</body></html>
