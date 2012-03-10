<?php
class Controller {

	public function __construct( CMF_Hydrogen_Environment_Abstract $env ){
		$this->env			= $env;
		$this->words		= $env->words;
		$this->messenger	= $this->env->getMessenger();
		$this->view		= new View( $this->env );
		$this->addData( 'words', $env->words );
		$this->logic		= new Logic( $this->env );
	}

	protected function addData( $key, $value ){
		$this->view->addData( $key, $value );
	}
	
	public function details( $moduleId ){
		$model		= new Model( $this->env );
		$module		= $model->get( $moduleId );
		$module->neededModules		= $model->getAllNeededModules( $moduleId );
		$module->supportedModules	= $model->getAllSupportedModules( $moduleId );
		$this->addData( 'module', $module );
		$this->addData( 'moduleId', $moduleId );
	}

	public function getView(){
		return $this->view;
	}

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
		if( $this->logic->uninstallModule( $moduleId, $verbose ) )
			$this->messenger->noteSuccess( $this->words['msg']['moduleUninstalled'], $moduleId );
		else
			$this->messenger->noteError( $this->words['msg']['moduleNotUninstalled'], $moduleId );
		$this->restart( array( 'action' => 'details', 'moduleId' => $moduleId ) );
	}

	public function viewCode( $moduleId, $type, $fileName ){
		$pathModule	= $this->env->pathModules.'/'.$moduleId.'/';
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
			die( 'Invalid file' );
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
