<?php
class Controller_Index extends CMF_Hydrogen_Controller{
	public function index( $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL, $arg5 = NULL ){
		$this->addData( 'instanceId', $this->env->getSession()->get( 'instanceId' ) );
	}
}
?>
