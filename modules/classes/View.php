<?php
class View extends CMF_Hydrogen_View{
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
