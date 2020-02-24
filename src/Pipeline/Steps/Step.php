<?php
namespace Pipeline\Steps;


abstract class Step
{
    /**
     * The main function, returns array of info required for other jobs
     */
    public function main()
    {
        return [];
    }

    /**
     * Checks the statuses of jobs that were triggered
     */
    protected function pollBuilds() {
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