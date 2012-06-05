<?php
class Tool_Hydrogen_Setup_App extends CMF_Hydrogen_Application_Web_Site{
	
	public function __construct( $env = NULL ){
		$this->host	= getEnv( 'HTTP_HOST' );
		$this->root	= getEnv( 'DOCUMENT_ROOT' );
		$this->path	= dirname( getEnv( 'SCRIPT_NAME' ) ).'/';
		$this->uri	= $this->root.$this->path;
		$this->url	= 'http://'.$this->host.$this->path;
		parent::__construct( $env );
		$this->checkInstances();
		$this->checkSources();
		$this->checkThemes();
		$this->checkModules();
	}

	protected function checkModules(){
		CMC_Loader::registerNew( 'php5', NULL, 'classes/' );
		$modelSource	= new Model_ModuleSource( $this->env );
		$modelInstance	= new Model_Instance( $this->env );
		$logic			= new Logic_Module( $this->env );
		remark( "Sources:" );
		print_m( array_keys( $modelSource->getAll( FALSE ) ) );
		remark( "Instances:" );
		print_m( array_keys( $modelInstance->getAll( FALSE ) ) );
		remark( "Categories:" );
		print_m( $logic->getCategories() );
		remark( "Modules installed:" );
		print_m( array_keys( $logic->model->getInstalled() ) );

		$modules	= array(
			'Admin_Instances',
			'Admin_Modules',
			'Admin_Module_Sources',
			'Admin_Module_Installer',
			'Admin_Module_Editor',
			'Admin_Module_Creator',
		);
		
#		$logic->uninstallModule( $moduleId );

		$installed	= FALSE;
		foreach( $modules as $moduleId ){
			if( !$this->env->getModules()->has( $moduleId ) ){
				$logic->installModule( $moduleId, Logic_Module::INSTALL_TYPE_LINK, array(), TRUE );
				$this->restart();
			}
		}
	}

	protected function restart(){
		header( 'Location: ./' );
		exit;
	}
	
	protected function checkThemes(){
		if( !file_exists( 'themes/custom' ) ){
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
			Folder_Editor::createFolder( 'themes/custom/', 0770 );
		}
	}

	protected function checkInstances(){
		$fileName	= 'config/instances.ini';
		if( !file_exists( $fileName ) ){
			copy( $fileName.'.dist', $fileName );
			$editor	= new File_INI_Editor( $fileName, TRUE );
			$editor->setProperty( 'path', $this->uri, 'Setup' );
			$this->restart();
		}
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

/*	public function run(){
		if( !$this->env->getModules()->has( 'Admin_Module_Sources' ) ){
			remark( 'Root: '.$this->root );
			remark( 'Path: '.$this->path );
			remark( 'HOST: '.$this->host );
			remark( 'URI: '.$this->uri );
			remark( 'URL: '.$this->url );
			die( 'Module "Admin_Module_Sources" is missing' );
		}
		if( !$this->env->getModules()->has( 'Admin_Instances' ) ){
			die( 'Module "Admin_Instances" is missing' );
		}
		
		remark( getCwd() );
		remark( realpath( __FILE__ ) );
	}*/
}
?>
