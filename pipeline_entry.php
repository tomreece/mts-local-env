<?php
require_once __DIR__ .'/vendor/autoload.php';
use Pipeline\Main;

$tests = ["Functional" => true];
$magentoDestination = __DIR__ . '/magentoInstall';

// Run the main loop
$pipeline = new Main();
$stepOneReturn = $pipeline->triggerStep(Main::STEPS[0]);

if ($tests['Functional']) {
    $stepOneId = $stepOneReturn['step_one_id'];
    $functionalGroupCount = null;
    $functionalGroups = getopt(null, "--groups");
    if ($functionalGroupCount == null) {
        $bucketARN = "arn:aws:s3:::codebuild-stage-results-bucket/code-coverage-one/c2d12d82-c5b5-4653-9754-7aaac029f8d5/artifacts.zip";
        $pipeline->triggerStep(Main::STEPS[3], $bucketARN, $magentoDestination);
        $functionalGroupCount = count(glob($magentoDestination . "/html/dev/tests/acceptance/tests/functional/Magento/FunctionalTest/_generated/groups/" . "*"));
    }
    $stepTwoReturn = $pipeline->triggerStep(Main::STEPS[1], $stepOneId, $functionalGroupCount);
}

$stepThreeReturn = $pipeline->triggerStep(Main::STEPS[2]);
echo 'done';
