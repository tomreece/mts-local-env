<?php

require 'vendor/autoload.php';

use Aws\CodeBuild\CodeBuildClient;

$codebuild = new CodeBuildClient([
    'version' => 'latest',
    'region' => 'us-east-1'
]);

$done = false;
$nextToken = null;

while (!$done) {
    $currentBuilds = $codebuild->listBuildsForProject([
        'projectName' => 'code-coverage-two',
        'nextToken' => $nextToken
    ]);

//    foreach ($currentBuilds['ids'] as $buildId) {
//        print("Stopping ${buildId}\n");
//
//        $response = $codebuild->stopBuild([
//            'id' => $buildId
//        ]);
//
//        // Wait to avoid rate limiting
//        sleep(1);
//    }

    print("Deleting a batch of builds\n");
    var_dump($currentBuilds['ids']);

    $response = $codebuild->batchDeleteBuilds([
        'ids' => $currentBuilds['ids']
    ]);

    $nextToken = $currentBuilds['nextToken'];
    if (is_null($nextToken)) {
        $done = true;
    }
}

exit();
