<?php
namespace Pipeline\Steps;

use Aws\CodeBuild\CodeBuildClient;

class ReportGenerationRunner extends Step {
    private $codebuild;
    private $builds;
    private $stepOneId;
    private $stepTwoId;

    /**
     * MagentoInstallRunner constructor.
     *
     * Notice we don't pass credentials to the CodeBuildClient class. These are picked up from environment variables.
     * Set these environment variables in your IDE or your shell before running this script:
     *   AWS_ACCESS_KEY_ID
     *   AWS_SESSION_TOKEN
     *   AWS_SECRET_ACCESS_KEY
     */
    function __construct($stepOneId, $stepTwoId)
    {
        $this->codebuild = new CodeBuildClient([
            'version' => 'latest',
            'region' => 'us-east-1'
        ]);
        $this->stepOneId = $stepOneId;
        $this->stepTwoId = $stepTwoId;
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
    }

    /**
     * Triggers single job
     */
    private function startStep()
    {
        $this->builds[] = $this->codebuild->startBuild([
            'projectName' => 'code-coverage-one',
            'environmentVariablesOverride' => [
                [
                    'name' => 'STEP_ONE_BUILD_ID',
                    'value' => $this->stepOneId
                ],
                [
                    'name' => 'STEP_ONE_BUILD_ID',
                    'value' => $this->stepTwoId
                ]
            ]
        ]);
        print("Triggered Report Generation (Step 3)\n");
    }
}
