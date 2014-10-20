<?php
// PerformanceMonitor
// performancemonitor

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Forget it. Not an entry point.' );
}

class PerformanceMonitor {

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		PerformanceMonitor::includeJS();
		PerformanceMonitor::includeHtml();
		return true; // true = continue to process hooks
	}
	
	public static function includeHtml() {
		global $wgOut;
		$wgOut->addHTML('
			<script>
			mw.loader.using( ["ext.performancemonitor.jit", "ext.performancemonitor.diagram"], function () {
			  init();
			});
			mw.loader.load( "ext.performancemonitor.jit" );
			mw.loader.load( "ext.performancemonitor.diagram" );
			</script>
			
			<div id="container">
			
			<div id="left-container">
			
			<div class="text">
			
			  <h4>
			    Icicle Tree with static JSON data
			  </h4>
			  
			            <p>Some static JSON tree data is fed to this visualization.</p>
			            <p>
			              <b>Left click</b> to set a node as root for the visualization.
			            </p>
			            <p>
			              <b>Right click</b> to set the parent node as root for the visualization.
			            </p>
			            
			
			  <div>
			    <label for="s-orientation">Orientation: </label>
			    <select name="s-orientation" id="s-orientation">
			      <option value="h" selected>horizontal</option>
			      <option value="v">vertical</option>
			    </select>
			    <br>
			    <div id="max-levels">
			    <label for="i-levels-to-show">Max levels: </label>
			    <select  id="i-levels-to-show" name="i-levels-to-show" style="width: 50px">
			      <option>all</option>
			      <option>1</option>
			      <option>2</option>
			      <option selected="selected">3</option>
			      <option>4</option>
			      <option>5</option>
			    </select>
			    </div>
			  </div>
			</div>
			
			<a id="update" href="#" class="theme button white">Go to Parent</a>
			 
			<div id="id-list"></div>
			
			
			<div style="text-align:center;"><a href="example1.js">See the Example Code</a></div>            
			</div>
			
			<div id="center-container">
			    <div id="infovis"></div>    
			</div>
			
			<div id="right-container">
			
			<div id="inner-details"></div>
			
			</div>
			
			<div id="log"></div>
			</div>');
	}	
	
	public static function includeJS() {
		
		global $wgOut, $outputPage, $wgResourceModules;
		
		// muss das hier noch zusŠtzlich rein?
		//$wgOut->addScript( $PerformanceMonitorTemplate + '/jit-2.0.1/Examples/Icicle/example1.js' );
		
		$wgOut->addModules( 'ext.performancemonitor.jit', 'ext.performancemonitor.diagram' );
		
		// hilft das weiter?:
	    //SMWOutputs::requireHeadItem( SMW_HEADER_STYLE );
	
	    // MediaWiki >1.17 Resource Loader.
	    if ( method_exists( 'OutputPage', 'addModules' ) && method_exists( 'SMWOutputs', 'requireResource' ) ) {
	      SMWOutputs::requireResource( 'ext.performancemonitor.jit' );
	      SMWOutputs::requireResource( 'ext.performancemonitor.diagram' );
	    }
	}

} // class PerformanceMonitor