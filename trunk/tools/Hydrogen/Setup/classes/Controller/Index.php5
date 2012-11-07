<?php
class Controller_Index extends CMF_Hydrogen_Controller{

	/**	@var	Tool_Hydrogen_Setup_Environment		$env		Environment object */
	protected $env;

	public function index( $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL, $arg5 = NULL ){
		$instanceId		= $this->env->getSession()->get( 'instanceId' );
		$remote			= $this->env->getRemote();
		$this->env->clock->profiler->tick( 'Index::index: init remote' );
		$logic			= Logic_Module::getInstance( $this->env );
		$this->env->clock->profiler->tick( 'Index::index: init' );

		$listModulesMissing		= array();
		$listModulesPossible	= array();
		$modulesAll				= $logic->model->getAll();
		$this->env->clock->profiler->tick( 'Index::index: get all' );
		$modulesInstalled		= $remote->getModules()->getAll();
		$this->env->clock->profiler->tick( 'Index::index: get installed' );

		foreach( $modulesInstalled as $module ){
			foreach( $module->relations->needs as $need )
				if( !array_key_exists( $need, $modulesInstalled ) )
					$listModulesMissing[]	= $need;
			foreach( $module->relations->supports as $support )
				if( !array_key_exists( $support, $modulesInstalled ) )
					$listModulesPossible[]	= $support;
		}
		$this->env->clock->profiler->tick( 'Index::index: get more' );

		$model		= new Model_Instance( $this->env );

		$this->addData( 'instances', $model->getAll() );
		$this->addData( 'instanceId', $instanceId );
		$this->addData( 'instance', $model->get( $instanceId ) );
		$this->addData( 'modulesAll', $modulesAll);
		$this->addData( 'modulesInstalled', $modulesInstalled );
		$this->addData( 'modulesMissing', $listModulesMissing );
		$this->addData( 'modulesPossible', $listModulesPossible );
		$this->addData( 'remote', $remote );
		$this->addData( 'remoteConfig', $remote->getConfig() );
		$this->env->clock->profiler->tick( 'Index::index: done' );
	}
}
?>
