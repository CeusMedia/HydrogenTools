<?php
class View extends CMF_Hydrogen_View{

	public function __construct( CMF_Hydrogen_Environment_Abstract $env ){
		$this->env	= $env;
		$this->env->getPage()->addJavaScript( 'http://js.ceusmedia.de/jquery/1.7.min.js' );
	}

	public function edit(){
		extract( $this->getData() );
		return require_once 'templates/edit.php';
	}

	public function index(){
		extract( $this->getData() );
		return require_once 'templates/index.php';
	}

	public function install(){
		extract( $this->getData() );
		return require_once 'templates/install.php';
	}

	public function details(){
		extract( $this->getData() );
		return require_once 'templates/view.php';
	}
}
?>
