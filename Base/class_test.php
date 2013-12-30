<?php

include_once 'Base_Dynamic.php';
include_once 'Test.php';
 
echo '<pre>';
 
$obj = new Test;
 
echo $obj->var_1.PHP_EOL;
$obj->var_1 = 'var test 1';
echo $obj->var_1.PHP_EOL;
 
$obj->pvar3 += 11;
$obj->pvar3 = $obj->pvar3 - 2;
echo $obj->pvar3.PHP_EOL;
 
echo $obj->get_var5().PHP_EOL;
$obj->var5(134);
echo $obj->get_var5().PHP_EOL;
 
echo '</pre>';