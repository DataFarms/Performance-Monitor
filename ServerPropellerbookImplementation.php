<?php
// ServerImplementation.php
// Serverweiche Ameise/localhost 
// derived classes/implementations: ServerAmeiseImplementation, ServerPropellerbookImplementation

class ServerPropellerbookImplementation extends ServerImplementation {
	
	protected $name = "Propellerbook";
	protected $path_monitor = "/Applications/MAMP/htdocs/mediawiki-1.20.8/extensions/PerformanceMonitor/";
	protected $path_logfiles = "/Applications/MAMP/htdocs/mediawiki-1.20.8/logs/autologs/";
	protected $phpVersion = "5.4.4";
	
	public function __construct( /*$profileConfig*/ ) {
		parent::__construct( /*$profileConfig*/ );
	}

	public function getLogfileName( $keyword ) {
		return 'log-2014-4-3--22-22-17--1396556537';
	}
}