<?php

require 'vendor/autoload.php';

use Aws\CodeBuild\CodeBuildClient;

class GroupsRunner {
    private $prevBuildId;
    private $numGroups;
    private $codebuild;
    private $builds;

    function __construct($prevBuildId, $numGroups)
    {
        $this->prevBuildId = $prevBuildId;
        $this->numGroups = $numGroups;
        $this->codebuild = new CodeBuildClient([
            'version' => 'latest',
            'region' => 'us-east-1'
        ]);
    }

    public function main()
    {
        $this->startAllGroups();
        $this->pollBuilds();

        print('All done!');
    }

    private function startAllGroups()
    {
        for ($i = 1; $i <= $this->numGroups; $i++) {
            $this->builds[] = $this->codebuild->startBuild([
                'projectName' => 'code-coverage-two',
                'environmentVariablesOverride' => [
                    [
                        'name' => 'PREV_BUILD_ID',
                        'value' => $this->prevBuildId
                    ],
                    [
                        'name' => 'GROUP_NUMBER',
                        'value' => "$i"
                    ]
                ]
            ]);
        }
    }

    private function pollBuilds() {
        $numTotal = count($this->builds);

        while(true) {
            print('Polling...');

            $statuses = $this->codebuild->batchGetBuilds([
                'ids' => array_map(function($build) { return $build['build']['id']; }, $this->builds)
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
    }
}

// todo -- pass these in as arguments to the script
$groupRunner = new GroupsRunner('7e375873-1fbe-48d6-b555-80b90b768423', 2);
$groupRunner->main();
