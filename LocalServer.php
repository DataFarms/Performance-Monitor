<?php
// LocalServer.php
// @see: ServerImplementation.php
// Serverweiche remote/localhost 
// derived classes/implementations: ServerAmeiseImplementation, ServerPropellerbookImplementation
// require_once '$iw/ServerImplementation.php';
// $monitorPath = LocalServer::getImplementation()->getMonitorPath();
// $version = LocalServer::getImplementation()->getPhpVersion();

require_once __DIR__ . '/ServerImplementation.php';
require_once __DIR__ . '/ServerOutThereImplementation.php';
require_once __DIR__ . '/ServerMyMacbookImplementation.php';

class LocalServer {
	
	public static function getImplementation() {
		$imps = $serverImplementations = array(
		 'ServerAmeiseImplementation',
		 'ServerPropellerbookImplementation' );
		
		for( $i=0; $i< count( $imps ); $i++ ) {
			$strImp = $imps[ $i ];
			$oImp = new $strImp();
			if( $oImp->isServer() ) return $oImp;
		}
	}
	
}