<?php
class Environment extends CMF_Hydrogen_Environment_Web{
	public function __construct( $pathModules, $pathApp, $pathConfig = './' ){
		if( !preg_match( '/^\//', $pathModules ) )
			$pathModules	= getEnv( 'DOCUMENT_ROOT' ).'/'.$pathModules;
		if( !preg_match( '/^\//', $pathApp ) )
			$pathApp	= getEnv( 'DOCUMENT_ROOT' ).'/'.$pathApp;
		$this->pathApp		= $pathApp;
		$this->pathConfig	= $pathApp.$pathConfig;
		$this->pathModules	= $pathModules;

		self::$configFile	= $this->pathConfig.'config.ini';
		
		$this->initClock();
		$this->initConfiguration();
		$this->initRequest();
		$this->initSession();
		$this->initDatabase();
		$this->initResponse();
		$this->initPage();
		$this->initMessenger();
		$this->words		= new ADT_List_Dictionary( parse_ini_file( 'locales/de.ini', TRUE ) );
	}
}
?>
