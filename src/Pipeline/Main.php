<?php
namespace Pipeline;
use Pipeline\Steps\GroupsRunner;
use Pipeline\Steps\CopyMagentoInstall;
use Pipeline\Steps\MagentoInstallRunner;
use Pipeline\Steps\ReportGenerationRunner;


class Main {
    const STEPS = ["STEP_1", "STEP_2-A", "STEP_3", "COPY_ARTIFACTS"];

    const STEP_TO_CLASS = [
        "STEP_1" => MagentoInstallRunner::class,
        "STEP_2-A" => GroupsRunner::class,
        "STEP_3" => ReportGenerationRunner::class,
        "COPY_ARTIFACTS" => CopyMagentoInstall::class
    ];

    function __construct()
    {
        // empty constructor
    }

    public function triggerStep($step, ...$args)
    {
        $class = new \ReflectionClass(self::STEP_TO_CLASS[$step]);
        $step = $class->newInstanceArgs($args);
        return $step->main();
    }
}
