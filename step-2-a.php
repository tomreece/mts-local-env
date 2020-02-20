<?php

require 'vendor/autoload.php';

use Aws\CodeBuild\CodeBuildClient;

class GroupsRunner {
    private $prevBuildId;
    private $numGroups;
    private $codebuild;
    private $builds;

    /**
     * GroupsRunner constructor.
     *
     * Notice we don't pass credentials to the CodeBuildClient class. These are picked up from environment variables.
     * Set these environment variables in your IDE or your shell before running this script:
     *   AWS_ACCESS_KEY_ID
     *   AWS_SESSION_TOKEN
     *   AWS_SECRET_ACCESS_KEY
     *
     * @param string $prevBuildId The id of the previous build (step 2). Example: 7e375873-1fbe-48d6-b555-80b90b768423
     * @param string $numGroups The number of groups to start.
     */
    function __construct($prevBuildId, $numGroups)
    {
        $this->prevBuildId = $prevBuildId;
        $this->numGroups = $numGroups;
        $this->codebuild = new CodeBuildClient([
            'version' => 'latest',
            'region' => 'us-east-1'
        ]);
    }

    /**
     * The main function
     */
    public function main()
    {
        $this->startAllGroups();
        $this->pollBuilds();

        print('All done!');
    }

    /**
     * Triggers multiple code-coverage-two jobs
     */
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

    /**
     * Checks the statuses of jobs that were triggered
     */
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

// Two command line arguments
$prevBuildId = $argv[1];
$numGroups = $argv[2];

// Help message
if ($prevBuildId === null || $numGroups === null) {
    print("How to use this script:\n");
    print("    argument 1 should be the previous build id\n");
    print("    argument 2 should be the number of groups to run\n");
    print("    example:\n");
    print("        php step-2-a.php 7e375873-1fbe-48d6-b555-80b90b768423 2\n");
    exit();
}

// Run the main loop
$groupRunner = new GroupsRunner($prevBuildId, $numGroups);
$groupRunner->main();
