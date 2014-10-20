<?php
// ServerImplementation.php
// Serverweiche Ameise/localhost 
// derived classes/implementations: ServerAmeiseImplementation, ServerPropellerbookImplementation

class ServerAmeiseImplementation extends ServerImplementation {
	
	protected $name = "Ameise";
	protected $path_monitor = "/var/www/html/ebs-ameise.wob.vw.vwg/httpdocs/vr-wiki/extensions/PerformanceMonitor/";
	protected $path_logfiles = "/var/www/html/ebs-ameise.wob.vw.vwg/httpdocs/vr-wiki/log/autologs/";
	protected $phpVersion = "5.3.3";
	protected $logfiles;
	
	public function __construct( /*$profileConfig*/ ) {
		parent::__construct( /*$profileConfig*/ );
		$this->logfiles = array(
			"werkzeuge" => "log-2014-4-4--15-22-35--1396617755",
			"testseite" => "log-2014-4-4--11-3-49--1396602229"
			);
	}
	
	public function getLogfileName( $keyword ) {
		if( isset( $keyword ) ) return $this->logfiles[ $keyword ];
		return 'log-2014-4-4--11-3-49--1396602229'; //'log-2014-4-4--8-47-37--1396594057';
	}
	
}