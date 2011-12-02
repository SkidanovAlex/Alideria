<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">
<head><title>Права</title></head>

<?

include( 'clan.php' );

$p = $_GET['p'];
settype( $p, 'integer' );

outControlsList( $p );

echo "<script>peReadOnly = true;</script>";

?>
