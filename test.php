<?php
$test = $_GET['test'] ?? "NO_TEST_SPECIFIED";
// Since this file is located at pub/test.php we need to create the CURRENT_TEST file one level up in the Magento root
file_put_contents('../CURRENT_TEST', $test);
echo 'SET CURRENT TEST TO ' . $test;