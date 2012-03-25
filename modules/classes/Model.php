<?php
class Model{

	const TYPE_UNKNOWN	= 0;
	const TYPE_CUSTOM	= 1;
	const TYPE_COPY		= 2;
	const TYPE_LINK		= 3;
	const TYPE_SOURCE	= 4;

	public function __construct( $env ){
		$this->env			= $env;
		$this->pathRepos	= $env->pathModules;
		$this->pathConfig	= $env->pathConfig.'modules/';
		$this->cache		= array();
		if( !file_exists( $this->pathRepos ) )
			throw new RuntimeException( 'Modules folder missing in "'.$this->pathRepos.'"', 1 );
		if( !file_exists( $this->pathConfig ) )
			if( !mkdir( $this->pathConfig ) )
				throw new RuntimeException( 'Modules configuration folder missing in "'.$this->pathConfig.'" and cannot be created', 2 );
	}

	public function getAll(){
		$globalModules	= $this->getAvailable();
		$localModules	= $this->getInstalled( $globalModules );
		$list			= $globalModules;
		foreach( $localModules as $moduleId => $module ){
			if( !array_key_exists( $moduleId, $list ) )
				$list[$moduleId]	= $module;
			else if( $module->type != self::TYPE_LINK )
				$module->type	= self::TYPE_COPY;
			switch( $module->type ){
				case self::TYPE_LINK:
					$module->versionAvailable	= $module->version;
					$module->versionInstalled	= $module->version;
					break;
				case self::TYPE_COPY:
					$module->versionInstalled	= $module->version;
					$module->versionAvailable	= $globalModules[$moduleId]->version;
					break;
				case self::TYPE_CUSTOM:
					$module->version			= $module->versionInstalled;
					$module->versionAvailable	= NULL;
					break;
			}
			$list[$moduleId]	= $module;
		}
		ksort( $list );
		return $list;
	}

	public function get( $moduleId ){
		$all	= $this->getAll();
		if( array_key_exists( $moduleId, $all ) )
			return $all[$moduleId];
		return NULL;
	}

	public function getPath( $moduleId = NULL ){
		if( $moduleId )
			return $this->pathRepos.str_replace( '_', '/', $moduleId ).'/';
		return $this->pathRepos;
	}

	public function getInstalled(){
		$list	= array();
		$index	= new File_RecursiveRegexFilter( $this->pathConfig, '/^\w+.xml$/' );
		foreach( $index as $entry )
		{
			$id	= preg_replace( '/\.xml$/i', '', $entry->getFilename() );
			try{
				$module	= $this->readXml( $entry->getPathname() );
			}
			catch( Exception $e ){
				$this->env->messenger->noteFailure( 'XML of installed Module "'.$id.'" is broken ('.$e->getMessage().').' );
			}
			$module->type	= self::TYPE_CUSTOM;
			if( is_link( 'config/modules/'.$id.'.xml' ) ){
				$module->type	= self::TYPE_LINK;
			}
			$module->id		= $id;
			$module->versionInstalled	= $module->version;
			$list[$id]		= $module;
		}
		ksort( $list );
		return $list;
	}

	public function getAvailable(){
		if( $this->cache )
			return $this->cache;
		$list	= array();
		$index	= new File_RecursiveNameFilter( $this->pathRepos, 'module.xml' );
		foreach( $index as $entry ){
			$id		= preg_replace( '@^'.$this->pathRepos.'@', '', $entry->getPath() );
			$id		= str_replace( '/', '_', $id );
			try{
				$obj	= $this->readXml( $entry->getPathname() );
				$obj->path	= $entry->getPath();
				$obj->file	= $entry->getPathname();
				$obj->type	= self::TYPE_SOURCE;
				$obj->id	= $id;
				$obj->versionAvailable	= $obj->version;
				$list[$id]	= $obj;
			}
			catch( Exception $e ){
				$this->env->messenger->noteFailure( 'XML of available Module "'.$id.'" is broken ('.$e->getMessage().').' );
			}
		}
		$this->cache	= $list;
		ksort( $list );
		return $list;
	}
	public function getNotInstalled(){
		$globalModules	= $this->getAvailable();
		$localModules	= $this->getInstalled( $globalModules );
		return array_diff_key( $globalModules, $localModules );
	}

	public function install( $moduleId ){
	}

	public function isInstalled( $moduleId ){
		$list	= array();
		$index	= new File_RecursiveRegexFilter( $this->pathConfig, '/^\w+.xml$/' );
		foreach( $index as $entry ){
			$id	= preg_replace( '/\.xml$/i', '', $entry->getFilename() );
			if( $id == $moduleId )
				return TRUE;
		}
		return FALSE;
	}
	
	public function uninstall( $moduleId ){
	}

	public function getAllNeededModules( $moduleId, $list = array() ){
		$module	= $this->get( $moduleId );
		foreach( $module->relations->needs as $moduleName ){
			if( array_key_exists( $moduleName, $list ) )
				continue;
			$list[$moduleName]	= $this->isInstalled( $moduleName );
			foreach( $this->getAllNeededModules( $moduleName, $list ) as $id => $status )
				if( $id !== $moduleId)
					$list[$id]	= $status;
		}
		return $list;
	}

	public function getAllSupportedModules( $moduleId, $list = array() ){
		$module	= $this->get( $moduleId );
		foreach( $module->relations->supports as $moduleName ){
			if( array_key_exists( $moduleName, $list ) )
				continue;
			$list[$moduleName]	= $this->isInstalled( $moduleName );
			foreach( $this->getAllSupportedModules( $moduleName, $list ) as $id => $status )
				if( $id !== $moduleId)
					$list[$id]	= $status;
		}
		return $list;
	}
	
	protected function readXml( $fileName ){
		$clock	= new Alg_Time_Clock();
		$xml	= XML_ElementReader::readFile( $fileName );
		$obj	= new stdClass();
		$obj->title				= (string) $xml->title;
		$obj->description		= (string) $xml->description;
		$obj->files				= new stdClass();
		$obj->files->classes	= array();
		$obj->files->locales	= array();
		$obj->files->templates	= array();
		$obj->files->styles		= array();
		$obj->files->scripts	= array();
		$obj->files->images		= array();
		$obj->config			= array();
		$obj->version			= (string) $xml->version;
		$obj->versionAvailable	= NULL;
		$obj->versionInstalled	= NULL;
		$obj->relations			= new stdClass();
		$obj->relations->needs		= array();
		$obj->relations->supports	= array();
		$obj->sql				= array();
		foreach( $xml->files->class as $link )
			$obj->files->classes[]	= (string) $link;
		foreach( $xml->files->locale as $link )
			$obj->files->locales[]	= (string) $link;
		foreach( $xml->files->template as $link )
			$obj->files->templates[]	= (string) $link;
		foreach( $xml->files->style as $link )
			$obj->files->styles[]	= (string) $link;
		foreach( $xml->files->script as $link )
			$obj->files->scripts[]	= (string) $link;
		foreach( $xml->files->image as $link )
			$obj->files->images[]	= (string) $link;
		foreach( $xml->config as $pair ){
			$key	= $pair->getAttribute( 'name' );
			$obj->config[$key]	= (object) array(
				'key'	=> $key,
				'type'	=> $pair->getAttribute( 'type' ),
				'value'	=> (string) $pair,
			);
		}
		if( $xml->relations ){
			foreach( $xml->relations->needs as $moduleName )
				$obj->relations->needs[]	= (string) $moduleName;
			foreach( $xml->relations->supports as $moduleName )
				$obj->relations->supports[]	= (string) $moduleName;
		}
		foreach( $xml->sql as $sql ){
			$event	= $sql->getAttribute( 'on' );
			$type	= $sql->hasAttribute( 'type' ) ? $sql->getAttribute( 'type' ) : '*';
			foreach( explode( ',', $type ) as $type ){
				$key	= $event.'@'.$type;
				$obj->sql[$key]	= (string) $sql;
			}
		}
#		remark( $fileName.': '.$clock->stop( 3, 1 ).'ms' );
		return $obj;
	}
}
?>
