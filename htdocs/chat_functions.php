<?

function chat_who_list( $res )
{
	$st = "";
	while( $arr = f_MFetch( $res ) )
	{
		$rs = f_MQuery("SELECT st.* FROM player_status as st, characters as c WHERE st.player_id=c.player_id AND c.login='".$arr[login]."'");
		$ast = f_MFetch($rs);
		if ($ast)
		{
			$st_t=$ast[status_text];
			$st_i=$ast[status_image];
		}
		else
		{
			$st_t='';
			$st_i='';
		}
		$st .= "add_plr('$arr[login]','#$arr[nick_clr]',$arr[level],$arr[clan_id],$arr[sex], '$st_t', '$st_i');\n";
	}
		
	return $st;
}


function chat_update_who_global_list()
{
   $res = f_MQuery( "SELECT characters.login, characters.level, characters.player_id, characters.nick_clr, characters.clan_id, characters.sex FROM characters, online WHERE online.player_id=characters.player_id AND characters.player_id!=172" );
   $st = chat_who_list( $res );

   $key = 'chat.plist.who_global';
   SetCachedValue($key, $st, 60);
   
   
   return $st;
}


function chat_who_global_list()
{
   $key   = 'chat.plist.who_global';
   $value = -1; //GetCachedValue($key);
   
   
   if ($value === -1)
   {
      $value = chat_update_who_global_list();
   }

   return $value;
}



?>
