<?

include( "functions.php" );
include( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->player_id != 173 && $player->player_id != 265593 && $player->login != 'undefined' )
{
	echo "Сервис временно недоступен";
	die( );
}

if( isset( $_GET['select'] ) )
{
	$d = dir("images/avatars/improved" );
	while($moo=$d->read())  if( $moo[0] != '.' )
	{
		$sindex++;
		if( $sindex == $_GET['select'] )
		{
			$content = file_get_contents( "images/avatars/improved/$moo" );
			header("Pragma: public"); header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false);
            header("Content-Type: video/x-ms-wmv");
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=\"$moo\";");
    		header("Content-Transfer-Encoding: binary");
    		header("Content-Length: ".strlen($content));
    		echo $content;
    		die( );
		}
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<?

function uploadfile($origin, $dest, $tmp_name)
{
  $fulldest = $dest.$origin;
  $filename = $origin;
  for ($i=1; file_exists($fulldest); $i++)
  {
   $fileext = (strpos($origin,'.')===false?'':'.'.substr(strrchr($origin, "."), 1));
   $filename = substr($origin, 0, strlen($origin)-strlen($fileext)).'['.$i.']'.$fileext;
   $fulldest = $dest.$filename;
  }
  
  if (move_uploaded_file($tmp_name, $fulldest))
   return $filename;
  return "Moo!:$fulldest:";
}

if( isset( $_FILES['ava'] ) )
{
	uploadfile($_FILES['ava']['name'],'images/avatars/improved/',$_FILES['ava']['tmp_name']);
	die( '<script>location.href="avatar_do.php";</script>' );
}

if( isset( $_GET['del'] ) )
{
	$d = dir("images/avatars/improved" );
	while($moo=$d->read())  if( $moo[0] != '.' )
	{
		$sindex++;
		if( $sindex == $_GET['del'] )
		{
			unlink( "images/avatars/improved/$moo" );
		}
	}	
	die( '<script>location.href="avatar_do.php";</script>' );
}

echo "<table>";
$d = dir("images/avatars/improved" );
while($moo=$d->read())  if( $moo[0] != '.' )
{
$sindex++;
echo "<tr><td><a href=avatar_do.php?select=$sindex>$moo</a></td><td><a href=avatar_do.php?del=$sindex>Удалить</a></td></tr>";
}
echo "</table>";
echo "<br>";

echo '<form enctype="multipart/form-data" action=avatar_do.php method=post>';
echo "<table cellspacing=0 cellpadding=0 border=0>";
echo "<tr><td><input type=file class=m_btn name=ava value=''></td></tr>";
echo "<tr><td><input type=submit class=ss_btn value='Загрузить'></td></tr>";
echo "</table>";
echo "</form>";


?>

