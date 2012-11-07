<?php
class Tool_Hydrogen_Setup_Environment extends CMF_Hydrogen_Environment_Web{

	/**	@var	CMF_Hydrogen_Environment_Remote	$remote		Instance of remote environment */
	public $remote;

	public function __construct( $forceInstanceId = NULL ){

		self::$classRouter	= 'CMF_Hydrogen_Environment_Router_Recursive';
		self::$configFile	= "config/config.ini";

		date_default_timezone_set( "Europe/Berlin" );

		$this->detectSelf();

		$this->checkConfig();																		//  create main configuation if missing
		$this->checkInstances();																	//  create setup tool as first instance of none are defined yet
		$this->checkSources();																		//  create local cmFrameworks copy as first module source of none are defined yet
		$this->checkThemes();																		//  link in petrol theme is missing

		$this->pathConfig	= '';

		$pathModules	= CMF_PATH.'modules/Hydrogen/';												//  
		if( !preg_match( '/^\//', $pathModules ) )													//  module path is not absolute @todo kriss: remove
			$pathModules	= getEnv( 'DOCUMENT_ROOT' ).'/'.$pathModules;							//  prepend document root to module path @todo kriss: remove
		$this->pathModules	= $pathModules;															//  store module path @todo kriss: remove

		$this->path			= dirname( getEnv( 'SCRIPT_FILENAME' ) ).'/';
		if( isset( $options['pathApp'] ) )
			$this->path		= $options['pathApp'];													//	@todo: is this needed after migration of setup to CMF/Tools/Hydrogen ?

		$this->initClock();																			//  setup clock
		$this->initConfiguration();																	//  setup configuration
		$this->initModules();																		//  setup module support

		if( !$this->getModules()->has( 'Admin_Module_Sources' ) )									//  source administration module not installed yet
			require_once $pathModules.'Admin/Module/Sources/classes/Model/ModuleSource.php5';		//  load atleast module source model class
		if( !$this->getModules()->has( 'Admin_Instances' ) )										//  instance administration module not installed yet
			require_once $pathModules.'Admin/Instances/classes/Model/Instance.php5';				//  load atleast instance model class
		if( !$this->getModules()->has( 'Admin_Modules' ) ){											//  module administration module not installed yet
			require_once $pathModules.'Admin/Modules/classes/Model/Module.php5';					//  load atleast module model class
			require_once $pathModules.'Admin/Modules/classes/Logic/Module.php5';					//  and module logic class for installating missing modules
		}

		$this->initSession();																		//  setup session support
		$this->initMessenger();																		//  setup user interface messenger
		$this->initDatabase();																		//  setup database connection
		$this->initCache();																			//  setup cache support
		$this->initRequest();																		//  setup HTTP request handler
		$this->initResponse();																		//  setup HTTP response handler
		$this->initRouter();																		//  setup request router
		$this->initLanguage();																		//  setup language support
		$this->initPage();																			//  
		$this->initAcl();																			//  
		$this->initRemote( $forceInstanceId );														//  
		$this->checkModules();																		//  try to install missing modules
	}

	protected function checkConfig(){
		if( file_exists( self::$configFile ) )														//  config file is existing
			return;																					//  
		File_Writer::save( self::$configFile, File_Reader::load( self::$configFile.'.dist' ) );		//  copy config file
		$editor	= new File_INI_Editor( self::$configFile );											//  
		$editor->setProperty( 'app.base.url', $this->url );											//  
		$script	= '<script>document.location.reload()</script>';									//  
		$screen	= "#AppName# is installing. Please wait...".$script;								//  
		$locale	= $editor->getProperty( 'locale.default' );
		$file	= 'locales/'.$locale.'/html/env.installing.html';
		if( file_exists( $file ) )																	//  
			$screen	= File_Reader::load( $file );													//  
		$screen	= str_replace( "#AppName#", $editor->getProperty( 'app.name' ), $screen );			//  inset application name from config
		print( $screen );
		exit;
	}

	protected function checkInstances(){
		$fileName	= 'config/instances.ini';
		if( !file_exists( $fileName ) ){
			File_Writer::save( $fileName, File_Reader::load( $fileName.'.dist' ) );
			$editor	= new File_INI_Editor( $fileName, TRUE );
			$editor->setProperty( 'path', $this->uri, 'Setup' );
			$this->restart();
		}
	}

	protected function checkModules(){
		CMC_Loader::registerNew( 'php5', NULL, 'classes/' );
		$modelSource	= new Model_ModuleSource( $this );
		$modelInstance	= new Model_Instance( $this );
		$logic			= Logic_Module::getInstance( $this );
/*		remark( "Sources:" );
		print_m( array_keys( $modelSource->getAll( FALSE ) ) );
		remark( "Instances:" );
		print_m( array_keys( $modelInstance->getAll( FALSE ) ) );
		remark( "Categories:" );
		print_m( $logic->getCategories() );
		remark( "Modules installed:" );
		print_m( array_keys( $logic->model->getInstalled() ) );

#		$logic->uninstallModule( $moduleId );
*/
		try{
			$modules	= array(
				'Admin_Instances'			=> array(),
				'Admin_Modules'				=> array(),
				'Admin_Module_Sources'		=> array(),
				'Admin_Module_Installer'	=> array(),
				'Admin_Module_Editor'		=> array(),
				'Admin_Module_Creator'		=> array(),
				'UI_Helper_Content'			=> array(),
				'UI_CSS_Reset'				=> array(),
				'UI_Indicator'				=> array(),
				'JS_jQuery'					=> array(),
				'JS_jQuery_UI'				=> array(),
				'JS_Layer'					=> array(),
				'Resource_Cache'			=> array(
					'type'		=> 'Folder',
					'resource'	=> 'tmp/cache/'
				),
			);
			foreach( $modules as $moduleId => $settings ){
				if( !$this->getModules()->has( $moduleId ) ){
					$logic->installModule( $moduleId, Logic_Module::INSTALL_TYPE_LINK, $settings, TRUE );
					$this->restart();
				}
			}
		}
		catch( Exception_Logic $e ){
			die( UI_HTML_Exception_Page::display( $e ) );
		}
		catch( Exception $e ){
			die( UI_HTML_Exception_Page::display( $e ) );
		}
		$this->clock->profiler->tick( 'env: check: modules' );
	}

	protected function checkSources(){
		$fileName	= 'config/modules/sources.ini';
		if( !file_exists( $fileName ) ){
			copy( $fileName.'.dist', $fileName );
			$editor	= new File_INI_Editor( $fileName, TRUE );
			$editor->setProperty( 'path', CMF_PATH.'modules/Hydrogen/', 'Local_CM_Public' );
			$this->restart();
		}
	}

	protected function checkThemes(){
		if( !file_exists( 'themes/petrol' ) ){
			$source	= CMF_PATH.'themes/Hydrogen/petrol';
			$target	= $this->uri.'themes/petrol';
			if( !file_exists( 'themes' ) )
				if( !mkdir( 'themes', 0770 ) )
					throw new RuntimeException( 'Could not create themes folder' );
			if( !file_exists( 'themes/petrol' ) ){
				if( !file_exists( $source ) )
					throw new RuntimeException( 'Could not find Hydrogen theme "petrol" in '.$source );
				if( !symlink( $source, $target ) )
					throw new RuntimeException( 'Could not create link to petrol theme' );
			}
		}
	}

	public function getRemote(){
		return $this->remote;
	}

	protected function initRemote( $forceInstanceId = NULL ){
		$messenger		= $this->getMessenger();
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
					if( $requestedId != $sessionedId )
						$messenger->noteNotice( 'Instanz ausgewählt: <cite>'.$instance->title.'</cite>' );
				}
				else if( $sessionedId ){															//  an instance has been selected before
					if( !array_key_exists( $sessionedId, $instances ) ){							//  but is not configured anymore
						$this->session->remove( 'instanceId' );										//  remove selected instance ID from session
						throw new InvalidArgumentException( 'Selected instance "'.$sessionedId.'" is not existing anymore' );
					}
					$instance	= $instances[$sessionedId];											//  get instance environment
				}
				else
					$instance	=  $instances[array_shift( array_keys( $instances ) )];
			}
			$this->session->set( 'instanceId', $instance->id );										//  store instance ID in session
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
		$this->clock->profiler->tick( 'env: remote' );
	}

	protected function restart(){
		header( 'Location: ./' );
		exit;
	}
}
?>
