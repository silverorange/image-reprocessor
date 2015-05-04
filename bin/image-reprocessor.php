#!/bin/env php
<?php

ini_set('memory_limit', -1);
set_time_limit(0);
proc_nice(19);

require_once 'Console/CommandLine.php';
require_once 'Site/SiteCommandLineLogger.php';
require_once 'ImageReprocessor/ImageReprocessor.php';

$dir = '@data-dir@/@package-name@/data';
if ($dir[0] == '@') {
	$dir = __DIR__ . '/../data';
}

$parser = Console_CommandLine::fromXMLFile($dir . '/image-reprocessor.xml');
$logger = new SiteCommandLineLogger($parser);
$app = new ImageReprocessor($parser, $logger);

$app();

?>
