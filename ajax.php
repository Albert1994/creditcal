<?php
require 'functions.php' ;

$term = $_POST['term'];
$rate = $_POST['rate'];
$amount = $_POST['amount'];
$typecredit = $_POST['typecredit'];
$startmonth = $_POST['startmonth'];
$startyear = $_POST['startyear'];

$credit = credit($term, $rate, $amount, $startmonth, $startyear, $typecredit);

echo json_encode($credit);