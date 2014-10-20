<?php
// ServerImplementation.php
// Serverweiche Ameise/localhost 
// derived classes/implementations: ServerAmeiseImplementation, ServerPropellerbookImplementation
// global $eg_PM_LocalServer;
// $folder = $eg_PM_LocalServer->getLogfilePath();

abstract class ServerImplementation {

	abstract public function getLogfileName( $keyword );
	
	public function __construct( /*$profileConfig*/ ) {
		//$a = $profileConfig;
	}
	
	public function isServer() {
		$logfileDir = $this->path_logfiles;
		$isValidDir = is_dir( $logfileDir );
		return $isValidDir;
	}

	public function getPhpVersion() {
		return $this->phpVersion;
	}

	public function getMonitorPath() {
		return $this->path_monitor;
	}

	public function getLogfilePath() {
		return $this->path_logfiles;
	}

	public function getName() {
		return $this->name;
	}
}