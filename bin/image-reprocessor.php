#!/usr/bin/php
<?php

ini_set('memory_limit', -1);
set_time_limit(30000);
proc_nice(19);

require_once 'Console/CommandLine.php';
require_once 'Site/SiteCommandLineLogger.php';
require_once 'ImageReprocessor/ImageReprocessor.php';

$parser = Console_CommandLine::fromXMLFile(
	__DIR__ . '/../data/image-reprocessor.xml'
);
$logger = new SiteCommandLineLogger($parser);
$app = new ImageReprocessor(
	$parser,
	$logger
);
$app();

?>
