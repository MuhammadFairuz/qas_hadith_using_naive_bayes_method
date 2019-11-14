<?php

$a = 0.0031326276219441;



echo round($a, 5, PHP_ROUND_HALF_DOWN);
echo "<br>";
echo round($a, 5, PHP_ROUND_HALF_EVEN);
echo "<br>";
echo round($a, 5, PHP_ROUND_HALF_ODD);
echo "<br>";
?>