<?php 

require_once 'useRateCheck/UseRateCheck.php';

$UseRateCheck = new UseRateCheck($SPIRAL);

var_dump($UseRateCheck->getUsingInfo());