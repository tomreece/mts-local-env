<?php
/**
 * Public alias for the application entry point
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;

try {
    require __DIR__ . '/../app/bootstrap.php';
} catch (\Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    exit(1);
}

$params = $_SERVER;
$params[Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS] = array_replace_recursive(
    $params[Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS] ?? [],
    [
        DirectoryList::PUB => [DirectoryList::URL_PATH => ''],
        DirectoryList::MEDIA => [DirectoryList::URL_PATH => 'media'],
        DirectoryList::STATIC_VIEW => [DirectoryList::URL_PATH => 'static'],
        DirectoryList::UPLOAD => [DirectoryList::URL_PATH => 'media/upload'],
    ]
);

//Patch start
$driver = new pcov\Clobber\Driver\PHPUnit6();
$coverage = new \SebastianBergmann\CodeCoverage\CodeCoverage($driver);
$coverage->filter()->addDirectoryToWhitelist("app/code/Magento/*");
$coverage->filter()->removeDirectoryFromWhitelist("app/code/Magento/*/Test");
$testName = "NO_TEST_NAME";
if (file_exists(__DIR__ . '/CURRENT_TEST')) {
    $testName = file_get_contents(__DIR__ . '/CURRENT_TEST');
}
$id = !empty($testName) ? $testName : "NO_TEST_NAME";

$coverage->start($id);
//Patch end

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
$bootstrap->run($app);

// Patch start
$coverage->stop();
$writer = new \SebastianBergmann\CodeCoverage\Report\PHP();
$writer->process($coverage, 'cov/' . $id . "_" . md5(mt_rand()) . '.cov');
// Patch end
