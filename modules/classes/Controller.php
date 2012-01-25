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

	public function copy( $moduleId ){
		if( $this->logic->installModule( $moduleId, Logic::INSTALL_TYPE_COPY ) )
			$this->messenger->noteSuccess( $this->words['msg']['moduleCopied'], $moduleId );
		else
			$this->messenger->noteError( $this->words['msg']['moduleNotCopied'], $moduleId );
		$this->restart( array( 'action' => 'details', 'moduleId' => $moduleId ) );
	}

	public function details( $moduleId ){
		$model	= new Model( $this->env );
		$this->addData( 'module', $model->get( $moduleId ) );
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

	public function link( $moduleId ){
		if( $this->logic->installModule( $moduleId, Logic::INSTALL_TYPE_LINK ) )
			$this->messenger->noteSuccess( $this->words['msg']['moduleLinked'], $moduleId );
		else
			$this->messenger->noteError( $this->words['msg']['moduleNotLinked'], $moduleId );
		$this->restart( array( 'action' => 'details', 'moduleId' => $moduleId ) );
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
}
?>
