<?php

require 'vendor/autoload.php';

use Aws\CodeBuild\CodeBuildClient;

$codebuild = new CodeBuildClient([
    'version' => 'latest',
    'region' => 'us-east-1'
]);

$builds = $codebuild->listBuildsForProject([
    'projectName' => 'code-coverage-two'
]);

foreach ($builds['ids'] as $buildId) {
    print("Stopping ${buildId}\n");

    $response = $codebuild->stopBuild([
        'id' => $buildId
    ]);

    // Wait to avoid rate limiting
    sleep(1);
}

print("Deleting all builds\n");

// We need to wait a bit to make sure everything was stopped before trying to delete.
sleep(30);

$response = $codebuild->batchDeleteBuilds([
    'ids' => $builds['ids']
]);

//print($response);

exit();
