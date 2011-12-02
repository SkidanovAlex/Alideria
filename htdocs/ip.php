<?

$ipstr = getenv( "REMOTE_ADDR" );
$ipxstr = getenv( "HTTP_X_FORWARDED_FOR" );

echo "Ваш прокси айпи: ".$ipstr;
echo "<br>";
echo "Ваш реальный айпи: ".$ipxstr;

?>
