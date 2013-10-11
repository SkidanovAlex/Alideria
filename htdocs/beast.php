<?
	class Beast
	{
		var $mob_id;
		var $login;
		var $level;
		var $descr;
		var $mnd;
		var $mxd;
		var $dfd;
		var $loc;

		var $attrs;
		function Beast( $id )
		{
			$res = f_MQuery( "SELECT * FROM mobs WHERE mob_id=$id" );
			$arr = f_MFetch( $res );
			if( !$arr ) { $this->mob_id = 0; return; }
			$this->mob_id = $id;
			$this->login = $arr['name'];
			$this->level = $arr['level'];
			$this->descr = $arr['descr'];
			$this->mnd = $arr['min_depth'];
			$this->mxd = $arr['max_depth'];
			$this->dfd = $arr['defend_depth'];
			$this->loc = $arr['loc'];
			$this->avatar = $arr['avatar'];

			$this->attrs = array( );
			$res = f_MQuery( "SELECT * FROM mob_attributes WHERE mob_id=$id" );
			while( $arr = f_MFetch( $res ) )
				$this->attrs[$arr['attribute_id']] = $arr['value'];

		}
		function OutAttrStr2( $v, $a = '' )
    	{
    		global $stats, $aclrs, $aimgs;
    		
    		$val = $this->GetAttr( $v );
    		if( $v == 130 ) $val = "+".($this->GetAttr( 30 ) + $this->GetAttr( 33 ));
    		if( $v == 140 ) $val = "+".($this->GetAttr( 40 ) + $this->GetAttr( 42 ));
    		if( $v == 150 ) $val = "+".($this->GetAttr( 50 ) + $this->GetAttr( 51 ));
    		print( "<tr><td><img width=20 height=20 src='images/icons/attributes/$aimgs[$v]'></td><td width=150>&nbsp;<b><font color=$aclrs[$v]>$a$stats[$v]:&nbsp;</font></b></td><td align=right><b>".$val."</b></td><td>&nbsp;</td></tr>" );
    	}

    	function ShowPrimaryAttributes( $show_incs = true )
    	{
    		global $stats, $aclrs, $aimgs;
    		
    		$attrs = array( 5,6,7 );
    		
    		print( "<table cellspacing=0 cellpadding=0>" );
    		foreach( $attrs as $k => $v )
    		{
    			$this->OutAttrStr2( $v );
    		}
    		print( "<tr><td colspan=4>&nbsp;</td></tr>" );
    		print( "</table>" );
    	}

    	function ShowGlobalAttributes( )
    	{
    		global $stats;
    		
    		$attrs = Array( 13, 14, 15, 16, 222, 223, 502 );

    		print( "<table cellspacing=0 cellpadding=0>" );
    		foreach( $attrs as $k => $v )
    		{
    			$this->OutAttrStr2( $v );
    		}
    		print( "<tr><td colspan=4>&nbsp;</td></tr>" );
    		print( "</table>" );
    	}

    	function ShowBattleAttributes( )
    	{
    		global $stats;
    		
    		$attrs = Array( 130, 131, 132, 140, 141, 142, 150, 151, 152 );

    		print( "<table cellspacing=0 cellpadding=0>" );
    		foreach( $attrs as $k => $v )
    		{
    			$this->OutAttrStr2( $v );
    		}
    		print( "<tr><td colspan=4>&nbsp;</td></tr>" );
    		print( "</table>" );
    	}

    	function ShowSecondaryAttributes( )
    	{
    		global $stats, $aclrs, $aimgs;
    		
    		$attrs = array( 30,40,50 );
    		
    		print( "<table cellspacing=0 cellpadding=0>" );
    		foreach( $attrs as $k => $v )
    		{
    			$this->OutAttrStr2( $v );
    			
    			if( $this->GetAttr( $v ) )
    			{
    				$arr = array( $v + 1, $v + 2, $v + 3 );
    				foreach( $arr as $g )
    				{
    					$this->OutAttrStr2( $g );
    				}
    			}
    		}
    		print( "<tr><td colspan=4>&nbsp;</td></tr>" );
    		print( "</table>" );
    	}


		function GetAttr( $id )
		{
			if( $id == 101 ) $id = 1;
			return ( int )$this->attrs[$id];
		}
		function ARect( )
		{
    		print( "ccb( 0, '{$this->login}', {$this->level}, ".$this->GetAttr( 1 ).", ".$this->GetAttr( 101 ) );
    		print( ", ".$this->GetAttr( 30 ).", ".$this->GetAttr( 130 ).",".( $this->GetAttr( 30 ) + $this->GetAttr( 33 ) ).", ".$this->GetAttr( 131 ).", ".$this->GetAttr( 132 ) );
    		print( ", ".$this->GetAttr( 40 ).", ".$this->GetAttr( 140 ).",".( $this->GetAttr( 40 ) + $this->GetAttr( 42 ) ).", ".$this->GetAttr( 141 ).", ".$this->GetAttr( 142 ) );
    		print( ", ".$this->GetAttr( 50 ).", ".$this->GetAttr( 150 ).",".( $this->GetAttr( 50 ) + $this->GetAttr( 51 ) ).", ".$this->GetAttr( 151 ).", ".$this->GetAttr( 152 ) );
    		print( ", 0, '".$this->avatar."' )" );
		}
		function ShowCards( $pid = 0 )
		{
			if( !$pid ) $res = f_MQuery( "SELECT DISTINCT card_id FROM mob_cards WHERE mob_id={$this->mob_id}" );
			else $res = f_MQuery( "SELECT DISTINCT player_cards.card_id FROM player_cards INNER JOIN cards ON player_cards.card_id=cards.card_id WHERE player_id={$pid} ORDER BY cards.genre, cards.level" );
        	echo "<b>Заклинания:</b><br>";
        	echo "<script>";
        	while( $arr = f_MFetch( $res ) )
        	{
        		$c = new Card( $arr[0] );
        		echo "document.write(".$c->Text( )."+'<br>' );";
        	}
        	echo "</script>";
		}
		function ShowDrop( )
		{
			$res = f_MQuery( "SELECT i.*, m.number, m.chance FROM items as i INNER JOIN mob_items as m ON i.item_id = m.item_id WHERE m.mob_id={$this->mob_id} ORDER BY chance DESC, number DESC" );
        	echo "<b>Дроп:</b><br>";
        	echo "<table>";
        	$sum = 0;
        	if( !f_MNum( $res ) ) echo "<tr><td><i>Нет дропа</i></td></tr>";
        	else{ while( $arr = f_MFetch( $res ) )
        	{
        		$num = 1;
        		if( $arr['number'] > 1 ) $num = "1 - $arr[number]";
		if (!($arr[item_id] == 77083 || $arr[item_id]==76397 ))
	        		echo "<tr><td>[$num] <a href=help.php?id=1010&item_id=$arr[item_id] target=_blank>$arr[name]</a>&nbsp;</td><td align=right>&nbsp;".($arr['chance']/100)."%</td></tr>";	
        		$sum += $arr['price'] * ( $arr['number'] + 1 ) / 2 * $arr['chance'];
        	}  $sum /= 10000; echo "<tr><td><b>Средняя прибыль:</b></td><td align=right><img width=11 height=11 border=0 src=images/money.gif> $sum</td></tr>";  }
        	echo "</table>";
		}
	};
?>
