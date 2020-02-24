<?php
require_once __DIR__ .'/vendor/autoload.php';
use Pipeline\Steps\Main;

$tests = ["Functional"];
//$functionalGroups = getopt(null, "--groups");

// Run the main loop
$pipeline = new Main();
$stepOneReturn = $pipeline->triggerStep(Main::STEPS[0]);
if (isset($tests['Functional'])) {
    $stepTwoReturn = $pipeline->triggerStep(Main::STEPS[1], ["PREVID", "999"]);
}
$stepThreeReturn = $pipeline->triggerStep(Main::STEPS[2]);
echo 'done';
