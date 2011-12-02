<?

class Profile
{
	var $player_id;
	var $name;
	var $city;
	var $quote;
	var $icq;
	var $skype;
	var $birthday;
	var $descr;
	var $email;
	var $show_email;
	var $title;
	
	function Profile( $id )
	{
		$this->player_id = $id;
		
		$res = f_MQuery( "SELECT email FROM characters WHERE player_id={$this->player_id}" );
		$arr = f_MFetch( $res );
		if( $arr ) $this->email = $arr[0];
		else $this->email = "";
		
		$res = f_MQuery( "SELECT * FROM player_profile WHERE player_id={$this->player_id}" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
			$this->name = $arr['name'];
			$this->city = $arr['city'];
			$this->quote = $arr['quote'];
			$this->icq = $arr['icq'];
			$this->skype = $arr['skype'];
			$this->birthday = $arr['birthday'];
			$this->descr = $arr['descr'];
			$this->show_email = $arr['show_email'];
			$this->title = $arr['title'];
		}
		else
		{
			$this->name = '';
			$this->city = '';
			$this->quote = '';
			$this->icq = '';
			$this->skype = '';
			$this->birthday = 0;
			$this->descr = '';
			$this->show_email = 0;
			$this->title = '';
		}
	}
};

?>
