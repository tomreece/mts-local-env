<?php
namespace Pipeline\Steps;

use Aws\CodeBuild\CodeBuildClient;

class GroupsRunner extends Step{
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
        $start = microtime(true);

        $this->startStep();
        $this->pollBuilds();

        $stop = microtime(true);

        print("All done! Finished in " . round((($stop - $start) / 60), 2) . " minutes.\n");

        $buildIdToArtifact = [];
        foreach ($this->builds as $build) {
            $buildIdToArtifact[$build["build"]["id"]] = $build["build"]["artifacts"]["location"];
        }
    }

    /**
     * Triggers multiple code-coverage-two jobs
     */
    private function startStep()
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

            print("Triggered group$i\n");

            // Wait to avoid rate limiting
            sleep(10);
        }
    }
}
