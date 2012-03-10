<?php
class Logic {

	const INSTALL_TYPE_UNKNOWN	= 0;
	const INSTALL_TYPE_LINK		= 1;
	const INSTALL_TYPE_COPY		= 2;

	public function __construct( CMF_Hydrogen_Environment_Abstract $env ){
		$this->env	= $env;
		$this->words		= $env->words;
		$this->messenger	= $this->env->getMessenger();
	}

	protected function copyModuleFile( $moduleId, $fileIn, $fileOut ){
		$fileIn			= $this->env->pathModules.str_replace( '_', '/', $moduleId ).'/'.$fileIn;
		$fileOut		= $this->env->pathApp.$fileOut;
		$this->messenger->noteNotice( $fileIn." -> ".$fileOut );
		$pathNameIn		= realpath( $fileIn );
		if( !$pathNameIn ){
			$this->messenger->noteFailure( $this->words['msg']['resourceMissing'], $fileIn );
			return FALSE;
		}
		if( !is_readable( $pathNameIn ) ){
			$this->messenger->noteFailure( $this->words['msg']['resourceNotReadable'], $fileIn );
			return FALSE;
		}
		$pathOut	= dirname( $fileOut );
		if( !is_dir( $pathOut ) && !self::createPath( $pathOut ) ){
			$this->messenger->noteFailure( $this->words['msg']['pathNotCreatable'], $pathOut );
			return FALSE;
		}
		if( file_exists( $fileOut ) ){
			$this->messenger->noteFailure( $this->words['msg']['targetExisting'], $fileOut );
			return FALSE;
		}
		if( !copy( $pathNameIn, $fileOut ) ){
			$this->messenger->noteFailure( $this->words['msg']['copyFailed'], $fileOut );
			return FALSE;
		}
		return TRUE;
	}

	/**
	 *	Creates a Path by creating all Path Steps.
	 *	@access		protected
	 *	@param		string		$path				Path to create
	 *	@return		void
	 */
	protected static function createPath( $path ){
		$dirname	= dirname( $path );
		if( file_exists( $path ) && is_dir( $path ) )
			return;
		$hasParent	= file_exists( $dirname ) && is_dir( $dirname );
		if( $dirname != "./" && !$hasParent )
			self::createPath( $dirname );
		return mkdir( $path, 02770, TRUE );
	}

	protected function executeSql( $sql ){
		$lines	= explode( "\n", trim( $sql ) );
		$cmds	= array();
		$buffer	= array();
		if( !$this->env->has( 'dbc' ) )
			return;
		$prefix	= $this->env->config->get( 'database.prefix' );
		while( count( $lines ) ){
			$line = array_shift( $lines );
			if( !trim( $line ) )
				continue;
			$buffer[]	= UI_Template::renderString( trim( $line ), array( 'prefix' => $prefix ) );
			if( preg_match( '/;$/', trim( $line ) ) )
			{
				$cmds[]	= join( "\n", $buffer );
				$buffer	= array();
			}
			if( !count( $lines ) && $buffer )
				$cmds[]	= join( "\n", $buffer ).';';
		}
		$state	= NULL;
		foreach( $cmds as $command ){
			error_log( nl2br( $command )."\n", 3, 'a.log' );
			if( $state !== FALSE ){
				try{
					$this->env->dbc->exec( $command );
					$state	= TRUE;
				}
				catch( Exception $e )
				{
					$state	= FALSE;
					$this->messenger->noteFailure( $e->getMessage() );
				}
			}
		}
		return $state;
	}

	public function installModule( $moduleId, $installType = 0, $force = FALSE, $verbose = NULL ){
		$config		= $this->env->getConfig();
		$model		= new Model( $this->env );
		$module		= $model->get( $moduleId );
		$pathModule	= $model->getPath( $moduleId );
		$pathTheme	= $config->get( 'path.themes' ).$config->get( 'layout.theme' ).'/';
		$filesLink	= array();
		$filesCopy	= array();
		
		switch( $installType ){
			
			case self::INSTALL_TYPE_LINK:
				$array	= 'filesLink'; break;
			case self::INSTALL_TYPE_COPY:
				$array	= 'filesCopy'; break;
			default:
				throw new InvalidArgumentException( 'Unknown installation type' );
		}
		foreach( $module->files->classes as $class )
			${$array}['classes/'.$class]	= 'classes/'.$class;
		foreach( $module->files->templates as $template )
			${$array}['templates/'.$template]	= $config->get( 'path.templates' ).$template;
		foreach( $module->files->locales as $locale )
			${$array}['locales/'.$locale]	= $config->get( 'path.locales' ).$locale;
		foreach( $module->files->scripts as $script )
			${$array}['js/'.$script]	= $config->get( 'path.javascripts' ).$script;
		foreach( $module->files->styles as $style )
			${$array}['css/'.$style]	= $pathTheme.'css/'.$style;
		$filesCopy['module.xml']	= 'config/modules/'.$moduleId.'.xml';
		if( file_exists( $pathModule.'config.ini' ) )
			$filesCopy['config.ini']	= 'config/modules/'.$moduleId.'.ini';

		$state		= NULL;
		$listDone	= array();
		foreach( array( 'filesLink', 'filesCopy' ) as $type ){
			foreach( $$type as $fileIn => $fileOut ){
				if( $state !== FALSE ){
					$listDone[]	= $fileOut;
					if( $type == 'filesLink' )														//  @todo: OS check -> no links in windows <7
						$state	= $this->linkModuleFile( $moduleId, $fileIn, $fileOut, $force );
					else
						$state	= $this->copyModuleFile( $moduleId, $fileIn, $fileOut, $force );
				}
			}
		}

		//  --  SQL  --  //
		if( $state !== FALSE ){
			$driver	= $this->env->dbc->getDriver();
			if( $driver && !empty( $module->sql['install@'.$driver] ) )
				$state	= $this->executeSql( $module->sql['install@'.$driver] );
			else if( !empty( $module->sql['install@*'] ) )
				$state	= $this->executeSql( $module->sql['install@*'] );
		}
		if( $state === FALSE )
			foreach( $listDone as $fileName )
				@unlink( $fileName );
		else if( $verbose ){
			$list	= '<ul><li>'.join( '</li><li>', $listDone ).'</li></ul>';
			$this->messenger->noteNotice( 'Installed: '.$list );
		}
		return $state !== FALSE;
	}
	
	protected function linkModuleFile( $moduleId, $fileIn, $fileOut, $force = FALSE ){
		$fileIn		= $this->env->pathModules.str_replace( '_', '/', $moduleId ).'/'.$fileIn;
		$fileOut	= $this->env->pathApp.$fileOut;
		$pathNameIn	= realpath( $fileIn );
		if( !$pathNameIn ){
			$this->messenger->noteFailure( $this->words['msg']['resourceMissing'], $fileIn );
			return FALSE;
		}
		if( !is_readable( $pathNameIn ) ){
			$this->messenger->noteFailure( $this->words['msg']['resourceNotReadable'], $fileIn );
			return FALSE;
		}
		if( !is_executable( $pathNameIn ) ){
			$this->messenger->noteFailure( $this->words['msg']['resourceNotExecutable'], $fileIn );
			return FALSE;
		}
		$pathOut	= dirname( $fileOut );
		if( !is_dir( $pathOut ) && !self::createPath( $pathOut ) ){
			$this->messenger->noteFailure( $this->words['msg']['pathNotCreatable'], $fileOut );
			return FALSE;
		}
		if( file_exists( $fileOut ) ){
			if( $force )
				@unlink( $fileOut );
			else{
				$this->messenger->noteFailure( $this->words['msg']['targetExisting'], $fileOut );
				return FALSE;
			}
		}
		if( !symlink( $pathNameIn, $fileOut ) ){
			$this->messenger->noteFailure( $this->words['msg']['linkFailed'], $fileOut );
			return FALSE;
		}
		return TRUE;
	}

	public function uninstallModule( $moduleId, $verbose = TRUE ){
		$config		= $this->env->getConfig();
		$pathTheme	= $this->env->pathApp.$config->get( 'path.themes' ).$config->get( 'layout.theme' ).'/';
		$model		= new Model( $this->env );
		$module		= $model->get( $moduleId );

		$files	= array();
#		try{
		//  --  FILES  --  //
		foreach( $module->files->classes as $class )
			$files[]	= $this->env->pathApp.'classes/'.$class;
		foreach( $module->files->templates as $template )
			$files[]	= $this->env->pathApp.$config->get( 'path.templates' ).$template;
		foreach( $module->files->locales as $locale )
			$files[]	= $this->env->pathApp.$config->get( 'path.locales' ).$locale;
		foreach( $module->files->scripts as $script )
			$files[]	= $this->env->pathApp.$config->get( 'path.javascripts' ).$script;
		foreach( $module->files->styles as $style )
			$files[]	= $pathTheme.'css/'.$style;

		//  --  CONFIG  --  //
		$files[]	= $this->env->pathConfig.'modules/'.$moduleId.'.xml';
		if( file_exists( $this->env->pathConfig.'modules/'.$moduleId.'.ini' ) )
			$files[]	= $this->env->pathConfig.'modules/'.$moduleId.'.ini';

		$state	= NULL;
		foreach( $files as $file )
			$state = @unlink( $file );

		if( $state !== FALSE ){
			//  --  SQL  --  //
			$driver	= $this->env->dbc->getDriver();
			$data	= array( 'prefix' => $config->get( 'database.prefix' ) );
			$sql	= "";
			if( $driver && !empty( $module->sql['uninstall@'.$driver] ) )
				$sql	= UI_Template::renderString( $module->sql['uninstall@'.$driver], $data );
			else if( !empty( $module->sql['uninstall@*'] ) )
				$sql	= UI_Template::renderString( $module->sql['uninstall@*'], $data );
			if( $sql )
				$state = $this->executeSql( $sql );
		}
		return $state;
	}

	protected function unlinkModuleFile( $moduleId, $fileName, $path )
	{
		$fileName	= $path.$fileName;
		if( file_exists( $fileName ) ){
			if( @unlink( $fileName ) )
				$this->messenger->noteSuccess( 'Removed "'.$fileName.'".' );
			else
				$this->messenger->noteFailure( 'Removal failed for "'.$fileName.'".' );
		}
	}
}
?>