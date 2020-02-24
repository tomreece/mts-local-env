<?php
namespace Pipeline\Steps;

class Main {
    const STEPS = ["STEP_1", "STEP_2-A", "STEP_3"];

    const STEP_TO_CLASS = [
        "STEP_1" => MagentoInstallRunner::class,
        "STEP_2-A" => GroupsRunner::class,
        "STEP_3" => ReportGenerationRunner::class,
    ];

    function __construct()
    {
        // empty constructor
    }

    public function triggerStep($step, $args = [])
    {
        $class = self::STEP_TO_CLASS[$step];
        $step = new $class($args);
        return $step->main();
    }
}
