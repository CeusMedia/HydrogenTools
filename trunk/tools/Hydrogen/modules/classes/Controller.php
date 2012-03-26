<?php
class Controller {

	public function __construct( CMF_Hydrogen_Environment_Abstract $env ){
		$this->env			= $env;
		$this->words		= $env->words;
		$this->messenger	= $this->env->getMessenger();
		$this->view			= new View( $this->env );
		$this->logic		= new Logic( $this->env );
		$this->addData( 'words', $env->words );
	}

	public function add(){
		$request	= $this->env->getRequest();
		$model		= new Model( $this->env );
		$this->addData( 'request', $request );
		if( $request->get( 'add' ) ){
			print_m( $request->getAll() );
			
			try{
			
				$title			= $request->get( 'add_title' );
				$description	= $request->get( 'add_description' );
				$version		= $request->get( 'add_version' );
				$moduleId		= $request->get( 'add_id' );
				$path			= $request->get( 'add_path' );
				$route			= $request->get( 'add_route' );

				
				$this->logic->model->registerLocalFile( 'Users', 'class', 'Controller/Test.php5' );
				
				if( !strlen( $title ) )
					$this->messenger->noteError( $this->words['add']['msgNoTitle'] );
				$modules	= $this->logic->model->getAll();
				foreach( $modules as $module )
					if( $module->title == $title )
						$this->messenger->noteError( $this->words['add']['msgTitleExisting'] );
				if( in_array( $moduleId, array_keys( $modules ) ) )
					$this->messenger->noteError( $this->words['add']['msgIdExisting'] );

				if( !$this->messenger->gotError() ){
					$this->logic->createLocalModule( $moduleId, $title, $description, $version, $route );
					$this->messenger->noteSuccess( $this->words['add']['msgSuccessCreated'] );
					if( $request->get( 'add_scafold' ) ){
						$this->logic->scafoldLocalModule( $moduleId, $route );
						$this->messenger->noteSuccess( $this->words['add']['msgSuccessScafold'] );
					}
		#			if( $request->get( 'add_import' ) )
		#				$this->logic->importModuleFiles( $moduleId );
		#				$this->messenger->noteSuccess( $this->words['add']['msgSuccessImported'] );
					$this->restart( array( 'action' => 'details', 'moduleId' => $moduleId ) );
				}
				
			}
			catch( Exception $e ){
				die( $e->getMessage() );
			}
		}
	}
	
	protected function addData( $key, $value ){
		$this->view->addData( $key, $value );
	}

	public function ajaxEditConfig( $moduleId, $key, $value ){
		$fileName	= $this->env->pathConfig.'modules/'.$moduleId.'.xml';
		$module		= XML_ElementReader::readFile( $fileName );
		foreach( $module->config as $pair )
			if( $pair->getAttribute( 'name' ) == $key )
				$pair->{0}	= $value;
		return File_Editor::save( $fileName, $module->asXML() );
	}

	public function ajaxAddConfig( $moduleId, $key, $value ){
		
	}
	
	public function details( $moduleId ){
		$model		= new Model( $this->env );
		$module		= $model->get( $moduleId );
		$module->neededModules		= $model->getAllNeededModules( $moduleId );
		$module->supportedModules	= $model->getAllSupportedModules( $moduleId );
		$this->addData( 'module', $module );
		$this->addData( 'moduleId', $moduleId );
		$this->addData( 'pathModule', $model->getPath( $moduleId ) );
	}

	public function getView(){
		return $this->view;
	}

	public function edit( $moduleId = NULL ){
		$model	= new Model( $this->env );
		$this->addData( 'modules', $model->getAll() );
/*		$this->addData( 'modulesAvailable', $model->getAvailable() );
		$this->addData( 'modulesInstalled', $model->getInstalled() );
		$this->addData( 'modulesNotInstalled', $model->getNotInstalled() );
*/	}

	public function index( $moduleId = NULL ){
		$model	= new Model( $this->env );
		$this->addData( 'modules', $model->getAll() );
/*		$this->addData( 'modulesAvailable', $model->getAvailable() );
		$this->addData( 'modulesInstalled', $model->getInstalled() );
		$this->addData( 'modulesNotInstalled', $model->getNotInstalled() );
*/	}

	public function install( $moduleId ){
		$request	= $this->env->getRequest();
		$model		= new Model( $this->env );
		$module		= $model->get( $moduleId );
		$module->neededModules		= $model->getAllNeededModules( $moduleId );
		$module->supportedModules	= $model->getAllSupportedModules( $moduleId );
		if( $request->get( 'doInstall' ) ){
			if( $request->get( 'type' ) == 'copy' ){
				if( $this->logic->installModule( $moduleId, Logic::INSTALL_TYPE_COPY, TRUE ) )
					$this->messenger->noteSuccess( $this->words['msg']['moduleCopied'], $moduleId );
				else
					$this->messenger->noteError( $this->words['msg']['moduleNotCopied'], $moduleId );
				$this->restart( array( 'action' => 'details', 'moduleId' => $moduleId ) );
			}
			else if( $request->get( 'type' ) == 'link' ){
				if( $this->logic->installModule( $moduleId, Logic::INSTALL_TYPE_LINK, TRUE ) )
					$this->messenger->noteSuccess( $this->words['msg']['moduleLinked'], $moduleId );
				else
					$this->messenger->noteError( $this->words['msg']['moduleNotLinked'], $moduleId );
				$this->restart( array( 'action' => 'details', 'moduleId' => $moduleId ) );
			}
		}
		$this->addData( 'module', $module );
		$this->addData( 'moduleId', $moduleId );
	}

	protected function restart( $parameters = array() ){
		if( is_array( $parameters ) )
			$parameters	= http_build_query( $parameters, '', '&' );
		if( is_string( $parameters ) ){
			$parameters	= strlen( $parameters ) ? '?'.$parameters : '';
			header( 'Location: ./'.$parameters );
			exit;
		}
	}

	protected function setData( $data, $topic = NULL ){
		$this->view->setData( $data, $topic );
	}

	public function uninstall( $moduleId, $verbose = TRUE ){
		$module	= $this->logic->model->get( $moduleId );
		if( $this->logic->uninstallModule( $moduleId, $verbose ) ){
			$this->messenger->noteSuccess( $this->words['msg']['moduleUninstalled'], $moduleId );
			if( $module->type == Model::TYPE_CUSTOM )
				$this->restart();
		}
		else
			$this->messenger->noteError( $this->words['msg']['moduleNotUninstalled'], $moduleId );
		$this->restart( array( 'action' => 'details', 'moduleId' => $moduleId ) );
	}

	public function viewCode( $moduleId, $type, $fileName ){
		$pathModule	= $this->logic->getModulePath( $moduleId );
		$pathFile	= '';
		$xmpClass	= '';
		switch( $type ){
			case 'class':
				$pathFile	= 'classes/';
				$xmpClass	= 'php';
				break;
			case 'locale':
				$pathFile	= 'locales/';
				$xmpClass	= 'ini';
				break;
			case 'script':
				$pathFile	= 'js/';
				$xmpClass	= 'js';
				break;
			case 'style':
				$pathFile	= 'css/';
				$xmpClass	= 'css';
				break;
			case 'template':
				$pathFile	= 'templates/';
				$xmpClass	= 'php';
				break;
		}
		if( !file_exists( $pathModule.$pathFile.$fileName ) )
			die( 'Invalid file: '.$pathModule.$pathFile.$fileName );
		$content	= File_Reader::load( $pathModule.$pathFile.$fileName );
		$code		= UI_HTML_Tag::create( 'xmp', $content, array( 'class' => 'code '.$xmpClass ) );
		$body		= '<h2>'.$moduleId.' - '.$fileName.'</h2>'.$code;
		$page		= new UI_HTML_PageFrame();
		$page->addStylesheet( 'css/reset.css' );
		$page->addStylesheet( 'css/typography.css' );
		$page->addStylesheet( 'css/xmp.formats.css' );
		$page->addBody( $body );
		print( $page->build( array( 'style' => 'margin: 1em' ) ) );
		exit;
	}
}
?>
