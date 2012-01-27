<?php
class JsError_Dispatcher{

	protected $request	= NULL;

	public function __construct(){
		$this->request	= new Net_HTTP_Request_Receiver();
		if( $this->request->isAjax() ){
			$response	= (object) array(
				'status'	=> 'data',
				'data'		=> NULL,
				'error'		=> NULL
			);
			try{
				$response->data	= $this->dispatchAjax();
			}
			catch( Exception $e ){
				$response->status	= 'error';
				$response->error	= $e->getMessage();
			}
			print( json_encode( $response ) );
		}
		else{
			print( $this->dispatch() );
		}
	}

	protected function initConfig( $fileName = 'config.ini' ){
		if( !file_exists( $fileName ) )
			throw new RuntimeException( 'JsError: "'.$fileName.'" missing' );
		$this->config	= parse_ini_file( $fileName );
	}

	protected function initDatabase(){
		if( !$this->config )
			throw new RuntimeException( 'Configuration missing' );
		$c	= $this->config;
		$dsn	= new Database_PDO_DataSourceName( $c['database.driver'], $c['database.name'] );
		$dsn->setHost( $c['database.host'] );
		$dsn->setUsername( $c['database.username'] );
		$dsn->setPassword( $c['database.password'] );
		$this->database	= new Database_PDO_Connection( $dsn );
		$this->database->setErrorLogFile( 'db.error.log' );
		
	}
	
	protected function dispatchAjax(){
		$r	= $this->request;
		switch( $r->get( 'action' ) ){
			case 'catch':
				if( !($r->has( 'message' ) && $r->has( 'file' ) && $r->has( 'line' ) ) )
					throw new RuntimeException( 'insufficient data' );
				$this->initConfig();
				$this->initDatabase();

				$code	= $r->get( 'document' );
				$type	= 0;
				if( preg_match( '/\.js(\?.*)?$/', $r->get( 'line' ) ) ){
					$type	= 'file';
					$code	= Net_Reader::readUrl( $r->get( 'file' ) );
				}
				
				$data	= array(
					'uri'		=> $r->get( 'file' ),
					'line'		=> $r->get( 'line' ),
					'type'		=> $type,
					'message'	=> $r->get( 'message' ),
					'agent'		=> getEnv( 'HTTP_USER_AGENT' ),
					'code'		=> $code,
					'timestamp'	=> time(),
				);
				$model	= new JsError_Model( $this->database, $this->config );
				$data['jsErrorId']	= $model->add( $data, FALSE );
				unset( $data['code'] );
				return $data;
				break;
		}
	}

	protected function dispatch(){
		$r	= $this->request;
		switch( $r->get( 'action' ) ){
			case 'catch':
				break;
			default:
				$this->initConfig();
				$this->initDatabase();
				$view	= new JsError_Viewer( $this->database, $this->config );
		}
	}
}
?>