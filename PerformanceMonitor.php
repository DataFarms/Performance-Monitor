<?php
/* PerformanceMonitor
 * This file provides basic settings that register
   * the extension
   * ResourceModules (CSS and JavaScript that is loaded by the resourceloader)
   * extension Classes (loaded by the autoloader)
   * global extension functions and variables
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgExtensionCredits['validextensionclass'][] = array(
    'path' => __FILE__,
    'name' => 'PerformanceMonitor',
    'author' => 'Achim Bode', 
    'url' => 'https://www.mediawiki.org/wiki/Extension:PerformanceMonitor', 
    'description' => 'Visualize where all the time is used up you need to wait for the server',
    'version'  => 0.1,
    'license-name' => "",   // Short name of the license, links LICENSE or COPYING file if existing - string, added in 1.23.0
);

$wgResourceModules['ext.performancemonitor.jit'] = array(
    'scripts' => [ 'js-includes/jit-2.0.1/jit.js',
				   'js-includes/bootstrap-3.1.1-dist/js/bootstrap.min.js' ],
    'styles' => [ 'js-includes/jit-2.0.1/Examples/css/base.css',
    			  'js-includes/bootstrap-3.1.1-dist/css/bootstrap.min.css', 
    			  'js-includes/bootstrap-3.1.1-dist/css/bootstrap-theme.min.css' ],
	
    'localBasePath' => __DIR__,
    'remoteExtPath' => 'PerformanceMonitor'
);		


# Special Page: Logfiles Ÿber Spezialseite auslesen

global $jsonStructureCode;
global $eg_PM_processes_Json, $eg_PM_processes_Json2;
global $eg_PM_filename, $eg_PM_filename2;
global $eg_PM_LocalServer;
global $eg_PM_Debug;

$eg_PM_Debug = true;

$eg_PM_processes_Json = ""; // global variable to pass results from the static method to the instance
$wgHooks['BeforePageDisplay'][] = 'SpecialPerformanceMonitor::onBeforePageDisplay';
$wgSpecialPageGroups[ 'PerformanceMonitor' ] = 'PerformanceMonitor';
$messages[ 'en' ] = array(
	'performancemonitor' => 'Performance Monitor', //Ignore
	'performancemonitor-desc' => "Visualizes Logfiles and helps tracking Performance Problems",
    'specialpages-group-performancemonitor' => 'PerformanceMonitor'
);
//$wgAutoloadClasses['LocalServer'] = __DIR__ . '/LocalServer.php'; // now in LocalSettings.php
$wgAutoloadClasses['SpecialPerformanceMonitor'] = __DIR__ . '/SpecialPerformanceMonitor.php';

$wgSpecialPages[ 'PerformanceMonitor' ] = 'SpecialPerformanceMonitor';

/*
// deferred execution (when everything is set up)
$wgExtensionFunctions[] = 'efFoobarSetup';

// move to includes/blah.php
function efFoobarSetup() {
   #do stuff that needs to be done after setup
}
*/
