<?php
class Controller_Index extends CMF_Hydrogen_Controller{
	
	/**	@var	Tool_Hydrogen_Setup_Environment		$env		Environment object */
	protected $env;
	
	public function index( $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL, $arg5 = NULL ){
		$instanceId	= $this->env->getSession()->get( 'instanceId' );
		$remote		= $this->env->getRemote();
		$modules	= $remote->getModules()->getAll();
		$model		= new Model_Instance( $this->env );
		
		$this->addData( 'instances', $model->getAll() );
		$this->addData( 'instanceId', $instanceId );
		$this->addData( 'instance', $model->get( $instanceId ) );
		$this->addData( 'modules', $modules );
		$this->addData( 'remote', $remote );
		$this->addData( 'remoteConfig', $remote->getConfig() );
	}
}
?>
