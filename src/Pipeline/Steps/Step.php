<?php
namespace Pipeline\Steps;

/**
 * Class Step
 * @package Pipeline\Steps
 *
 * Contains common functions for steps that involve running Codebuild jobs
 */
abstract class Step
{
    protected $codebuild;
    protected $builds;
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

            $ids = array_map(function($build) { return $build['build']['id']; }, $this->builds);

            // We have to do this chunk'ing because the batchGetBuilds() call will only accept
            // 100 build ids at a time. It's a limitation of the AWS SDK.
            $allChunksStatuses = [];
            $chunks = array_chunk($ids, 100);
            foreach ($chunks as $chunk) {
                $allChunksStatuses[] = $this->codebuild->batchGetBuilds([
                    'ids' => $chunk
                ]);
            }

            $allDone = true;
            $numInProgress = 0;

            foreach ($allChunksStatuses as $chunkStatuses) {
                foreach ($chunkStatuses['builds'] as $build) {
                    $buildStatus = $build['buildStatus'];
                    if ($buildStatus === 'IN_PROGRESS') {
                        $allDone = false;
                        $numInProgress += 1;
                    }
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
