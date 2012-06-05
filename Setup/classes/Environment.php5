<?php
class Tool_Hydrogen_Setup_Environment extends CMF_Hydrogen_Environment_Web{

	/**	@var	CMF_Hydrogen_Environment_Remote	$remote		Instance of remote environment */
	public $remote;
	
	public function __construct( $forceInstanceId = NULL ){

		self::$classRouter	= 'CMF_Hydrogen_Environment_Router_Recursive';
		self::$configFile	= "config/config.ini";
		
		if( !file_exists( self::$configFile ) )
			copy( self::$configFile.'.dist', self::$configFile );

		$pathModules	= CMF_PATH.'modules/Hydrogen/';
		if( !preg_match( '/^\//', $pathModules ) )
			$pathModules	= getEnv( 'DOCUMENT_ROOT' ).'/'.$pathModules;
		$this->pathModules	= $pathModules;

		$this->path			= dirname( getEnv( 'SCRIPT_FILENAME' ) ).'/';
		if( isset( $options['pathApp'] ) )
			$this->path		= $options['pathApp'];													//	@todo: is this needed after migration of setup to CMF/Tools/Hydrogen ?

		$this->initClock();																			//  setup clock
		$this->initConfiguration();																	//  setup configuration
		$this->initModules();																		//  setup module support

		if( !$this->getModules()->has( 'Admin_Module_Sources' ) )
			require_once $pathModules.'Admin/Module/Sources/classes/Model/ModuleSource.php5';
		if( !$this->getModules()->has( 'Admin_Instances' ) )
			require_once $pathModules.'Admin/Instances/classes/Model/Instance.php5';
		if( !$this->getModules()->has( 'Admin_Modules' ) ){
			require_once $pathModules.'Admin/Modules/classes/Model/Module.php5';
			require_once $pathModules.'Admin/Modules/classes/Logic/Module.php5';
		}
		
		$this->initSession();																		//  setup session support
		$this->initMessenger();																		//  setup user interface messenger
//		$this->initDatabase();																		//  setup database connection
		$this->initCache();																			//  setup cache support
		$this->initRequest();																		//  setup HTTP request handler
		$this->initResponse();																		//  setup HTTP response handler
		$this->initRouter();																		//  setup request router
		$this->initLanguage();																		//  setup language support
		$this->initPage();																			//  
		$this->initAcl();																			//  
		$this->initRemote( $forceInstanceId );														//  
	}

	public function getRemote(){
		return $this->remote;
	}
	
	protected function initRemote( $forceInstanceId = NULL ){
		$instance		= $this;
		$this->remote	= $this;																	//  use own environment by default

		if( class_exists( 'Model_Instance' ) ){														//  module for instance support is installed
			
			$model		= new Model_Instance( $this );												//  create model for reading instance settings
			$instances	= $model->getAll();															//  get all configured instances
			if( count( $instances ) == 1 )															//  only one instance is configured
				$instance	= array_pop( $instances );												//  get this instance's environment
			else if( $instances ){																	//  several instances are configured
				$requestedId	= $this->request->get( 'selectInstanceId' );								//  
				$sessionedId	= $this->session->get( 'instanceId' );								//  
				if( $forceInstanceId ){																//  an instance is forced
					if( !array_key_exists( $forceInstanceId, $instances ) )							//  but not configured
						throw new InvalidArgumentException( 'Forced instance "'.$forceInstanceId.'" is not existing' );
					$instance	=  $instances[$forceInstanceId];									//  get forced instance's environment
				}
				else if( $requestedId ){															//  an instance is requested
					if( !array_key_exists( $requestedId, $instances ) )								//  but not configured
						throw new InvalidArgumentException( 'Requested instance "'.$requestedId.'" is not existing' );
					$this->session->set( 'instanceId', $requestedId );								//  store instance ID in session
					$instance	=  $instances[$requestedId];										//  get forced instance's environment
					
				}
				else if( $sessionedId ){															//  an instance has been selected before
					if( !array_key_exists( $sessionedId, $instances ) ){							//  but is not configured anymore
						$this->session->remove( 'instanceId' );										//  remove selected instance ID from session
						throw new InvalidArgumentException( 'Selected instance "'.$sessionedId.'" is not existing anymore' );
					}
					$instance	= $instances[$sessionedId];											//  get instance environment
				}
			}
			$pathApp		= $instance->path;
			$pathConfig		= !empty( $instance->configPath ) ? $instance->configPath : "config/";
			$fileConfig		= !empty( $instance->configFile ) ? $instance->configFile : "config.ini";

			if( !preg_match( '/^\//', $pathApp ) )
				$pathApp	= getEnv( 'DOCUMENT_ROOT' ).'/'.$pathApp;

			$options	= array(
				'configFile'	=> $pathApp.$pathConfig.$fileConfig,
				'pathApp'		=> $pathApp
			);
			try{
				$this->remote		= new CMF_Hydrogen_Environment_Remote( $options );
			}
			catch( Exception $e )
			{
				$this->getMessenger()->noteError( $e->getMessage() );
				$this->remote		= new CMF_Hydrogen_Environment_Dummy( $options );
			}
			$this->pathApp		= $pathApp;
			$this->pathConfig	= $pathApp.$pathConfig;
		}
	}
}
?>