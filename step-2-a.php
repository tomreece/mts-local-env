<?php

require 'vendor/autoload.php';

use Aws\CodeBuild\CodeBuildClient;

$PREV_BUILD_ID = '7e375873-1fbe-48d6-b555-80b90b768423';
$NUM_GROUPS = 2;

$codebuild = new CodeBuildClient([
    'version' => 'latest',
    'region' => 'us-east-1'
]);

$builds = [];

for ($i = 1; $i <= $NUM_GROUPS; $i++) {
    $builds[] = $codebuild->startBuild([
        'projectName' => 'code-coverage-two',
        'environmentVariablesOverride' => [
            [
                'name' => 'PREV_BUILD_ID',
                'value' => $PREV_BUILD_ID
            ],
            [
                'name' => 'GROUP_NUMBER',
                'value' => "$i"
            ]
        ]
    ]);
}

$numTotal = count($builds);

while(true) {
    print("Polling...");

    $statuses = $codebuild->batchGetBuilds([
        'ids' => array_map(function($build) { return $build['build']['id']; }, $builds)
    ]);

    $allDone = true;
    $numInProgress = 0;

    foreach ($statuses['builds'] as $build) {
        $status = $build['buildStatus'];
        if ($status === 'IN_PROGRESS') {
            $allDone = false;
            $numInProgress += 1;
        }
    }

    $numFinished = $numTotal - $numInProgress;
    print("$numFinished out of $numTotal jobs are finished.\n");

    if ($allDone) {
        break;
    }

    sleep(30);
}

print("All Done!\n");
