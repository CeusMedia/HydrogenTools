<?php
class Manager extends CMF_Hydrogen_Application_Web_Site{
	
	public function __construct( $pathModules, $pathApp, $pathConfig = './', $fileConfig = 'config.ini' ){
		$env	= new Environment( $pathModules, $pathApp, $pathConfig, $fileConfig );
		parent::__construct( $env );
	}

	public function main(){
		ob_start();
		$request	= $this->env->getRequest();
		$action		= $request->has( 'action' ) ? $request->get( 'action' ) : 'index';
		$arguments	= $_REQUEST;
		unset( $arguments['action'] );
		try{
			$controller	= new Controller( $this->env );
			if( !method_exists( $controller, $action ) )
				throw new BadMethodCallException( 'Action "'.$action.'" is not existing in controller' );

			$result	= Alg_Object_MethodFactory::call( $controller, $action, $arguments );
			if( $request->isAjax() || preg_match( '/^ajax/', $action ) ){
				print( json_encode( array( 'data' => $result ) ) );
				exit;
			}
			$view	= $controller->getView();
			if( !method_exists( $view, $action ) )
				throw new BadMethodCallException( 'Action "'.$action.'" is not existing in view' );
			$content	= Alg_Object_MethodFactory::call( $view, $action );
		}
		catch( Exception $e ){
			switch( $e->getCode() ){
				case 1:
					$content	= '<b>You must install or link the <cite>Hydrogen Module Repository</cite> (to '.$this->env->pathApp.'modules/).</b>';
					break;
				default:
					if( $request->isAjax() ){
						print( json_encode( array( 'exception' => $e->getMessage() ) ) );
						exit;
					}
					throw $e;
			}
		}
		
		$dev		= ob_get_clean();
		$messages	= $this->env->getMessenger()->buildMessages();
		$appName	= $this->env->getConfig()->get( 'app.name' );

//		if( $this->env->getRequest()->isAjax() )								// this is an AJAX request
	//		return $content;													// deliver content only

		$config		= $this->env->getConfig();									// shortcut to configation object

		$page	= $this->env->getPage();
		$page->setTitle( 'Module Manager | Hydrogen Framework | Ceus Media' );
		$page->js->addUrl( 'http://js.ceusmedia.de/jquery/1.4.4.min.js' );
		$page->js->addUrl( 'http://js.ceusmedia.de/jquery/ui/1.8.4/min.js' );
		$page->js->addUrl( 'js/UI.Layer.js' );
		$page->js->addScript( '$(document).ready(function(){Layer.init();});' );
		$page->addStylesheet( 'http://js.ceusmedia.de/jquery/ui/1.8.4/css/smoothness.css' );
		$page->addStylesheet( 'http://css.ceusmedia.de/blueprint/reset.css' );
		$page->addStylesheet( 'http://css.ceusmedia.de/blueprint/typography.css' );
		$page->addStylesheet( 'http://css.ceusmedia.de/xmp.formats.css' );
		$page->addStylesheet( 'http://css.ceusmedia.de/layout.column.css' );
		$page->addStylesheet( 'css/plain/layout.messenger.css' );
		$page->addStylesheet( 'css/plain/form.css' );
		$page->addStylesheet( 'css/plain/form.button.css' );
		$page->addStylesheet( 'css/plain/form.fieldset.css' );
		$page->addStylesheet( 'css/plain/pagination.css' );
		$page->addStylesheet( 'css/plain/layer.css' );
		$page->addStylesheet( 'css/plain/table.css' );
		$page->addStylesheet( 'css/plain/style.css' );
		$page->addBody( require_once 'templates/main.php' );
		$page->setPackaging( FALSE, FALSE );
		return $page->build( array( 'class' => 'action-'.$action ) );
	}

	/**
	 *	Simple implementation of content response. Can be overridden for special moves.
	 *	@access		public
	 *	@param		string		$body		Response content body
	 *	@return		int			Number of sent bytes
	 */
	protected function respond( $body, $headers = array() )
	{
		$response	= $this->env->getResponse();

		$body		= ob_get_clean().$body;
		if( $body )
			$response->setBody( $body );

		foreach( $headers as $key => $value )
			if( $value instanceof Net_HTTP_Header_Field )
				$response->addHeader( $header );
			else
				$response->addHeaderPair( $key, $value );

		$type		= NULL;
		return Net_HTTP_Response_Sender::sendResponse( $response, $type, TRUE );
	}
}
?>
