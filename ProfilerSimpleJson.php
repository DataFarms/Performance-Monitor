<?php
/**
 * A Profiler that provides profiling information (which functions and methods
 *  are called by which, when and how much time do they consume?).
 *  
 * The information is stored in logfiles. You can configure their path in your
 *  own ServerImplementation Class.
 *  
 * Profiler showing execution trace in Json format. The result will be
 *  adapted and visually displayed on .../index.php/Special:PerformanceMonitor
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Profiler
 */

/**
 * Execution trace
 * @todo document methods (?)
 * @ingroup Profiler
 */
class ProfilerSimpleJson extends ProfilerSimple {
	var $trace = "Beginning trace: \n";
	var $count = 2;
	var $level = 0;
	var $json = null;
	var $currentJson = null;
	var $memory = 0;
	var $profileInOut_stack = null;

	function __construct( $params ) {
		ini_set('memory_limit','32M');
		$this->json = new stdClass();
		$this->json->parent = "root";
		parent::__construct( $params );
	}
	
	// called by Profiler::construct
	protected function addInitialStack() {
		$this->profileInOut_stack = new LifoStack();
		$this->errorEntry = $this->zeroEntry;
		$this->errorEntry['count'] = 1;

		$this->currentJson = $this->json;

		$initialTime = $this->getInitialTime();
		$initialCpu = $this->getInitialTime( 'cpu' );
		if ( $initialTime !== null && $initialCpu !== null ) {
			$this->currentJson->level = 0;
			//$this->currentJson->startTime = $initialTime;
		} else {
			$this->currentJson->info = "no startTime provided";
		}

	}


	function profileIn( $functionname ) {
		if( self::isPerformanceMonitorSpecialPage() ) return;
		
		$this->count++;
		$this->level++;

		$parentJson = $this->currentJson;
		$this->currentJson = new stdClass();

		$this->currentJson->count = $this->count ;
		$this->currentJson->startTime = $this->getTime();
		$this->currentJson->function = htmlspecialchars( $functionname );	
		$this->currentJson->parent = $parentJson;
		//$this->currentJson->startTimeCPU = $this->getTime( 'cpu' );
		//$this->currentJson->memoryDiff = $this->memoryDiff();	
		//$this->currentJson->level = $this->level;
		$this->check_profileInOut_consistency();
		if( !isset( $parentJson->children ) ) $parentJson->children = array();
		array_push( $parentJson->children, $this->currentJson );
	}
	
	private function check_profileInOut_consistency() {
		return true;
	}
	
	private function check_profileIn_consistency() {
		
	}
		
	private function check_profileOut_consistency() {
		
	}
	
	function profileOut($functionname) {
		if( self::isPerformanceMonitorSpecialPage() ) return;
		$unclosedAncestors = array();
		//while( ( $this->currentJson !== $this->json ) && $this-> ){

		$this->currentJson->endTime = $this->getTime();
		if( isset( $this->currentJson->startTime ) ) {
			$this->currentJson->duration = $this->currentJson->endTime - $this->currentJson->startTime;
			//unset( $this->currentJson->startTime );
			//unset( $this->currentJson->endTime );
		} else { // root node: use overall time and use page title as method name
			if( isset( $wgTitle ) ){
				$this->json->page = htmlspecialchars( $wgTitle->getPrefixedText() );
			} else {
				$this->json->page = "Seite unbekannt";
			}
			$this->json->function = $this->json->page;
			$this->currentJson->startTime = $this->getInitialTime( false );	
			$this->currentJson->endTime = $this->getTime( false );	
			$this->currentJson->duration = $this->currentJson->endTime - $this->currentJson->startTime;
		}
		//$this->currentJson->endTimeCPU = $this->getTime( 'cpu' );
		if( !( $this->currentJson == "root" ) ){
			$parent = $this->currentJson->parent;
			$this->level--;
			unset( $this->currentJson->parent );
			$this->currentJson = $parent;
		} else {
			unset( $this->currentJson->parent );
		}
	}
	
	function memoryDiff() {
		$diff = memory_get_usage() - $this->memory;
		$this->memory = memory_get_usage();
		return $diff / 1024;
	}
	
	public static function isPerformanceMonitorSpecialPage() {
		// do not Profile activities when you are on the PerformanceMonitor Special Page
		global $wgTitle;
		if( isset( $wgTitle ) && method_exists( $wgTitle, 'getDBkey' ) ){
			$hasSpecialNS = $wgTitle->getNamespace() == -1;
			$hasPmPageName = $wgTitle->getDBkey() == 'PerformanceMonitor';
			return ( $hasSpecialNS && $hasPmPageName );
		} else if( $title = self::getTitelFromReferrer() ) {
			// Special:PerformanceMonitor bei load.php suchen wie unten
			// @todo: NS internationalisieren
			if( $title == 'Special:PerformanceMonitor' ||  $title == 'Spezial:PerformanceMonitor' )
				return true;
		}
		return false;
	}
	
	private static function getTitelFromReferrer() {
		global $_SERVER;
		
		if( is_array( $_SERVER ) ){
			if( isset( $_SERVER[ 'HTTP_REFERER' ] ) ) {
				if( preg_match( '/index.php\d*\/([^\?]*)/', $_SERVER[ 'HTTP_REFERER' ], $matches ) > 0 ) {
					return $matches[1]; // unlike json->page this also shows the page title for load.php calls
				}
			}
			if( isset( $_SERVER[ 'REQUEST_URI' ] ) ) {
				if( preg_match( '/index.php\/([^\?]*)/', $_SERVER[ 'REQUEST_URI' ], $matches ) > 0 ) {
					return $matches[1]; // unlike json->page this also shows the page title for load.php calls
				}
			}
		}
		return false;
	}	

	function logData() {
		
		$isMonitor = self::isPerformanceMonitorSpecialPage();
		if( $isMonitor ) {
			return;
		}
		
		global $wgTitle;
		
		$this->collectFileData( $localServer );         // page name, file type, ...
		$filename = $this->assembleFilename();   // create filename from timestamp and page title
		
		$version = $localServer->getPhpVersion();
		if( $version == "5.4.4" ) {
			$logtext = json_encode( $this->json, JSON_PRETTY_PRINT );
		} else {
			$logtext = json_encode( $this->json );
		}
		
		if( isset( $this->json->title ) ) {
			$titleLength = strlen( $this->json->title );
			if( $titleLength > 0 ) {
				$current = file_put_contents ( $filename , $logtext );
			}
		}
	}
	
	protected function assembleFilename() {
		global $efPerformanceMonitorLogfilesPath;
		
		$logfolder = $efPerformanceMonitorLogfilesPath;
		
		$date = new DateTime();
		$mytimestamp = $date->getTimestamp();

		$filenameCompliantTitle = preg_replace( "/\//", "_", $this->json->title ); // replace slashes
		$logfileNameStub = strftime("log-%Y-%m-%e--%H-%M-%S--", $mytimestamp) . $mytimestamp . "-" . $filenameCompliantTitle . "-";

		if( property_exists( $this->json, 'filetype' ) )
			$logfileNameStub .= $this->json->filetype;
		
		return $logfolder . $logfileNameStub;
	}
	
	private function collectFileData() {
		
		global $wgTitle;
		global $_SERVER;
		
		$matches = array();
		// caught a regular wiki page
		if( isset( $wgTitle ) ) {
			$this->json->page = htmlspecialchars( $wgTitle->getPrefixedText() );
			$this->json->title = $this->json->page;
			$this->json->filetype = "wikipage";
			$this->json->startTime = $this->getInitialTime( false );	
			$this->json->endTime = $this->getTime( false );	
			$this->json->duration = $this->currentJson->endTime - $this->currentJson->startTime;
		// caught a script request via load.php
		} elseif ( substr_count ( $_SERVER[ 'SCRIPT_NAME' ] , "load.php" ) > 0 ) {
			$qs = $_SERVER[ 'QUERY_STRING' ];
			$foundEnding = preg_match( '/\/([.\:\/_]*\.([css|js]))/', $qs, $matchesEnding );
			$foundModule = preg_match( '/&(modules=[\w.%]*)&/iU', $qs, $matchesModule );
			if( $foundEnding ) {
				$this->json->page = $matchesEnding[1];
				$this->json->filetype = $matchesEnding[2];
			} elseif( $foundModule ) {
				$this->json->page = $matchesModule[1];
				$foundOnly = preg_match( '/&only=(styles|scripts)&/iU', $qs, $matchesModule );
				if( $foundOnly ) $this->json->filetype = $matchesModule[1];
			} else {
				// caught something we urgently need to identify...
				wfDebug( "collectFileData(): $qs" );
			}
			// caught something we urgently need to identify...
		} else {
			$this->json->page = "unbekannt";
			$this->json->filetype = "unbekannt";
		}
		$this->json->startTime = $this->getInitialTime( false );	
		$this->json->endTime = $this->getTime( false );
		$this->json->duration = $this->currentJson->endTime - $this->currentJson->startTime;
		if( property_exists( $this->json, 'page' ) )
			$this->json->function = $this->json->page; // @todo: workaround, should be implemented in 
		$this->json->query = $_SERVER[ 'QUERY_STRING' ];// debug=false&lang=en&modules=site&only=styles&skin=vector&*
		$title = htmlspecialchars( self::getTitelFromReferrer() );
		if( isset( $title ) ) {
			$this->json->title = $title; // unlike json->page this also shows the page title for load.php calls
		}
		
		// catch errors (no profileOut) - isset/isnull/other? see https://twiki.twoonix.com/wiki/PHP#isset.28.29_vs._empty.28.29_vs._is_null.28.29
		if( empty( $this->json->endTime ) ) {
			// add duration to avoid endless recursion in monitor:
			$this->json->duration = 1;
			$this->json->error = "No profileOut"; 
		}
	}
	
}

class LifoStack {
	
    protected $stack;
    protected $limit;
     
    public function __construct($limit = 100) {
        // initialize the stack
        $this->stack = array();
        // stack can only contain this many items
        $this->limit = $limit;
    }
 
    public function push($item) {
        // trap for stack overflow
        if (count($this->stack) < $this->limit) {
            // prepend item to the start of the array
            array_unshift($this->stack, $item);
        } else {
			throw new RunTimeException( __METHOD__ . ': Stack is full!' );
        }
    }
 
    public function pop() {
        if ($this->isEmpty()) {
            // trap for stack underflow
          throw new RunTimeException( __METHOD__ . ': Stack is empty!' );
      } else {
            // pop item from the start of the array
            return array_shift($this->stack);
        }
    }
 
    public function top() {
        return current($this->stack);
    }
 
    public function isEmpty() {
        return empty($this->stack);
    }
}