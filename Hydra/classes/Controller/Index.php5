<?php
class Controller_Index extends CMF_Hydrogen_Controller{

	/**	@var	Tool_Hydrogen_Setup_Environment		$env		Environment object */
	protected $env;

	public function index( $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL, $arg5 = NULL ){

		if( $this->env->getRequest()->has( 'resetInstanceId' ) ){
			$env->getSession()->remove( 'instanceId' );
			$this->restart( NULL );
		}

		$instanceId		= $this->env->getSession()->get( 'instanceId' );

		$model		= new Model_Instance( $this->env );
		$this->addData( 'instances', $model->getAll() );
		$this->addData( 'instanceId', $instanceId );

		if( $instanceId ){
			$remote			= $this->env->getRemote();
			$this->env->clock->profiler->tick( 'Index::index: init remote' );
#			print_m( $remote );
#			die;
			$logic			= Logic_Module::getInstance( $this->env );
			$this->env->clock->profiler->tick( 'Index::index: init' );

			$listModulesMissing		= array();
			$listModulesPossible	= array();
			$listModulesUpdate		= array();
			$modulesInstalled		= array();

			$logicModule			= Logic_Module::getInstance( $this->env );

			$modulesAll				= $logic->model->getAll();
			$this->env->clock->profiler->tick( 'Index::index: get all' );
			if( $remote instanceof CMF_Hydrogen_Environment_Remote ){
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

				foreach( $modulesInstalled as $module )
					if( $module->versionInstalled && $module->versionAvailable )
						if( version_compare( $module->versionAvailable, $module->versionInstalled ) > 0 )
							$listModulesUpdate[]	= $module;

				foreach( $listModulesMissing as $module ){
					$url	= './admin/module/installer/index/'.$module;
					$link	= UI_HTML_Tag::create( 'a', $module, array( 'href' => $url ) );
					$span	= UI_HTML_Tag::create( 'span', $link, array( 'class' => 'icon module module-status-4' ) );
					$this->env->getMessenger()->noteFailure( 'Modul '.$span.' ist nicht vollständig installiert.' );
				}
				$this->addData( 'remote', $remote );
				$this->addData( 'remoteConfig', $remote->getConfig() );
			}

			$this->addData( 'instance', $model->get( $instanceId ) );
			$this->addData( 'modulesAll', $modulesAll );
			$this->addData( 'modulesInstalled', $modulesInstalled );
			$this->addData( 'modulesMissing', $listModulesMissing );
			$this->addData( 'modulesPossible', $listModulesPossible );
			$this->addData( 'modulesUpdate', $listModulesUpdate );
		}
		
		$this->env->clock->profiler->tick( 'Index::index: done' );
	}
}
?>
