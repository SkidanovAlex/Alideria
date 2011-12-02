<?

$a = file_get_contents('a.php');
$a = iconv("CP1251", "UTF-8", $a );
file_put_contents('a.php',$a);

?>
