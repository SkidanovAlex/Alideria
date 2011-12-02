<?
srand(8855);
$i = rand(1000, 9999)."<br>";
echo $i;
srand($i);
echo rand(1000, 9999)."<br>";
srand($i);
echo rand(1000, 9999)."<br>";


?>