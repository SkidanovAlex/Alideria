<?

$ipstr = getenv( "REMOTE_ADDR" );
$ipxstr = getenv( "HTTP_X_FORWARDED_FOR" );

echo "��� ������ ����: ".$ipstr;
echo "<br>";
echo "��� �������� ����: ".$ipxstr;

?>
