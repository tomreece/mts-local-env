<?php
namespace Pipeline\Steps;

use Aws\CodeBuild\CodeBuildClient;

class MagentoInstallRunner extends Step {
    private $codebuild;
    private $builds;

    /**
     * MagentoInstallRunner constructor.
     *
     * Notice we don't pass credentials to the CodeBuildClient class. These are picked up from environment variables.
     * Set these environment variables in your IDE or your shell before running this script:
     *   AWS_ACCESS_KEY_ID
     *   AWS_SESSION_TOKEN
     *   AWS_SECRET_ACCESS_KEY
     */
    function __construct()
    {
        $this->codebuild = new CodeBuildClient([
            'version' => 'latest',
            'region' => 'us-east-1'
        ]);
    }

    /**
     * The main function, returns array of info required for other jobs
     */
    public function main()
    {
        $start = microtime(true);
        $this->startStep();
        $this->pollBuilds();
        $stop = microtime(true);

        print("All done! Finished in " . round((($stop - $start) / 60), 2) . " minutes.\n");
        return ["step_one_id" => $this->builds[0]["id"]];
    }

    /**
     * Triggers single job
     */
    private function startStep()
    {
        $this->builds[] = $this->codebuild->startBuild([
            'projectName' => 'code-coverage-one'
        ]);
        print("Triggered Magento Install (Step 1)\n");
    }
}
