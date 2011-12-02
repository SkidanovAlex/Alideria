<?

$dont_check_params = true;
include( "functions.php" );
include( "player.php" );
require_once( "referal_do_pay.php" );

f_MConnect( );

header( 'Content-type: text/xml' );

$check_ip="82.146.40.60";
$check_ip2="188.120.245.101";
$check_ip = "94.103.26.178";
$flag = $_REQUEST['command'];
$md5 = $_REQUEST['md5'];
$v1 = (int)$_REQUEST['v1'];	
$secret_key = "danil_k4";
$date=date("d.m.y");
$sum = (int)$_GET['sum'];
$id = (int)$_GET['id'];

$kod = 1;


if (($_SERVER['REMOTE_ADDR'] == $check_ip) || ($_SERVER['REMOTE_ADDR'] == $check_ip2) || 
( $_SERVER['REMOTE_ADDR'] == "89.108.73.136" ) || ( $_SERVER['HTTP_X_REAL_IP'] == "89.108.73.136" ) ||
   ($_SERVER['HTTP_X_REAL_IP'] == $check_ip) || ($_SERVER['HTTP_X_REAL_IP'] == $check_ip2)) {


if(($flag == 'check')&&($md5 == md5($flag.$v1.$secret_key)))
{
	$player_id = $v1;
	$res = f_MQuery( "SELECT count( player_id ) FROM characters WHERE player_id=$player_id" );
	$arr = f_MFetch( $res );
	if( !$arr[0] )
	{
		$desc = "Wrong player_id";
	}
	else 
	{
		$kod = 0;
		$desc = "Everything perfect";
	}
}
else if (($flag == 'pay')&&($md5 == md5($flag.$v1.$id.$secret_key)))
{
				$sql=f_MQuery("SELECT * FROM `2pay_payments` WHERE `id`='".((int)$_REQUEST['id'])."' LIMIT 0, 1");
				$rows = mysql_num_rows($sql);
// Если платеж уже был проведен
				if ($rows > 0) {
					$kod=0;
					$arr = f_MFetch( $sql );
					$sum = $arr['count'];
					$desc='Payment was send earlier';
				} else {
// Иначе пытаемся провести платеж
					$sql=f_MQuery("SELECT * FROM `characters` WHERE `player_id`='".$v1."' LIMIT 0, 1");
					$character=mysql_fetch_array($sql);
// Если персонаж найден				
					if(mysql_num_rows($sql)==1) {
// Добавляем персонажу количество купленных монет
                    	$player = new Player( $v1 );
                    	$player->AddUMoney( $sum );
                    	$player->AddToLogPost( -1, $sum, 22, 4 );
                    	$player->syst2( "Вы приобрели <b>$sum</b> ".my_word_str( $sum, "талант", "таланта", "талантов" ) );
                    	referalDoPay( $player->player_id, $sum );
						$sql=mysql_query("INSERT INTO `2pay_payments` (`count`,`date`,`id`) VALUES ('".(int)$sum."','".$date."','".(int)$_REQUEST['id']."')");
						$kod=0;
						$desc="ok";
	 				} else {
 						$kod=2;
 						$desc="Character not found";
 					}
				}


}
else $desc="Wrong command or MD5";

}


else {
		$desc="Parametrs or IP is not correct";
	}


$html="<?xml version=\"1.0\" encoding=\"windows-1251\"?><response><id>".$id."</id><result>".$kod."</result><sum>".$sum."</sum><comment>".$desc."</comment></response>";
//LogError( "2PAY: $id : PID: $v1 : SUM: $sum : kod : $kod" );
echo $html;


?>
