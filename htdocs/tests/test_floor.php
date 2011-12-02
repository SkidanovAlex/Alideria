<?
$a = 40.50;

echo "a=".$a."<br>";

echo "a / 2.7 = ".($a / 2.7)."<br>";

if (is_int(40.50 / 2.7)) echo "(40.50 / 2.7) - целое<br>";
else echo "(40.50 / 2.7) - не целое<br>";

$i = ($a / 2.7);
$i = round(((int)($i*10))/10);
echo "i=".$i."<br>";
$i = (int)$i;
echo "i=".$i."<br>";
if (is_int($i)) echo "i целое<br>";
else echo "i не целое<br>";
$i = $i*2.7;
echo "i=".$i."<br>";


$a = floor($a / 2.7);

echo "floor(a / 2.7)=".$a."<br>";

echo "<br><br><br><br><br>";

$i = 14.9999999999999999*2.7;
echo $i."<br>";

echo (int)($i*100000000000000000.0);

?>