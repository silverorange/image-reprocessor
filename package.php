<?php

/**
 * This is the package.xml generator for the image reprocessor pacakge
 *
 * PHP version 5
 *
 * @package   ImageReprocessor
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */

require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$api_version     = '0.1.0';
$api_state       = 'beta';

$release_version = '0.1.0';
$release_state   = 'beta';
$release_notes   = 'beta release';

$description = "Processes missing image dimension bindings for an entire ".
	"set in the silverorange Site framework.";

$package = new PEAR_PackageFileManager2();

$package->setOptions(
	array(
		'filelistgenerator'       => 'file',
		'simpleoutput'            => true,
		'baseinstalldir'          => '/',
		'packagedirectory'        => './',
		'dir_roles'               => array(
			'ImageReprocessor'    => 'php',
			'data'                => 'data',
		),
		'exceptions'              => array(
			'README.md'           => 'doc',
			'bin/image-reprocessor.php' => 'script',
		),
		'ignore'                  => array(
			'package.php',
			'*.tgz',
		),
		'installexceptions'       => array(
			'bin/image-reprocessor.php' => '/',
		),
	)
);

$package->setPackage('ImageReprocessor');
$package->setSummary('Processes missing image dimension bindings.');
$package->setDescription($description);
$package->setChannel('pear.silverorange.com');
$package->setPackageType('php');
$package->setLicense(
	'LGPL License',
 	'http://www.gnu.org/copyleft/lesser.html LGPL License 2.1'
);

$package->setNotes($release_notes);
$package->setReleaseVersion($release_version);
$package->setReleaseStability($release_state);
$package->setAPIVersion($api_version);
$package->setAPIStability($api_state);

$package->addMaintainer(
	'lead',
	'gauthierm',
	'Mike Gauthier',
	'mike@silverorange.com'
);

$package->addReplacement(
	'bin/image-reprocessor.php',
	'pear-config',
	'@data-dir@',
	'data_dir'
);

$package->addReplacement(
	'bin/image-reprocessor.php',
	'package-info',
	'@package-name@',
	'name'
);

$package->addPackageDepWithChannel(
	'required',
	'Site',
	'pear.silverorange.com',
	'2.1.35'
);

$package->setPhpDep('5.3.0');
$package->addInstallAs('bin/image-reprocessor.php', 'image-reprocessor');
$package->setPearInstallerDep('1.4.0');
$package->generateContents();

if (   isset($_GET['make'])
	|| (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')
) {
	$package->writePackageFile();
} else {
	$package->debugPackageFile();
}

?>
