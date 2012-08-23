<?php
class Tool_Hydrogen_Setup_App extends CMF_Hydrogen_Application_Web_Site{
	
	public function __construct( $env = NULL ){
		$this->host	= getEnv( 'HTTP_HOST' );
		$this->root	= getEnv( 'DOCUMENT_ROOT' );
		$this->path	= dirname( getEnv( 'SCRIPT_NAME' ) ).'/';
		$this->uri	= $this->root.$this->path;
		$this->url	= 'http://'.$this->host.$this->path;
		$env->clock->profiler->tick( 'app' );
		parent::__construct( $env );
	}
}
?>