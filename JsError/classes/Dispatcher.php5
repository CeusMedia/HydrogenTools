<?php
class JsError_Dispatcher{

	protected $request	= NULL;

	public function __construct(){
		$this->request	= new Net_HTTP_Request_Receiver();
		if( !getEnv( 'HTTP_HOST' ) ){
			$this->initConfig();
			if( $this->config['uberlog.active'] ){
				$this->initDatabase();
				$model		= new JsError_Model( $this->database, $this->config );
				$records	= $model->index();
				foreach( $records as $record ){
					$data	= array(
						'client'	=> $record['agent'],
						'timestamp'	=> $record['timestamp'],
						'type'		=> $record['type'],
						'source'	=> $record['uri'],
						'line'		=> $record['line'],
						'message'	=> $record['message'],
						'category'	=> $this->config['uberlog.category'],
					);
					$curl	= new Net_CURL( $this->config['uberlog.url'].'?record' );
					$curl->setOption( CURLOPT_POST, TRUE );
					$curl->setOption( CURLOPT_RETURNTRANSFER, TRUE );
					$curl->setOption( CURLOPT_POSTFIELDS, $data );
					$curl->exec();
					$model->remove( $record['jsErrorId'] );
				}
			}
			exit;
		}
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
		$this->database->setErrorLogFile( 'logs/db.error.log' );
//		$this->database->setStatementLogFile( 'logs/db.statements.log' );
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
			case 'cleanup':
				$start	= microtime( TRUE );
				$limit	= (int) $this->request->get( 'keep' );
				$limit	= $limit > 100 ? $limit : 1000;
				$this->initConfig();
				$this->initDatabase();
				$model	= new JsError_Model( $this->database, $this->config );
				$model->cleanup( $limit );
				remark( microtime( TRUE ) - $start );
				exit;
			case 'purge':
				$start	= microtime( TRUE );
				$this->initConfig();
				$this->initDatabase();
				$model	= new JsError_Model( $this->database, $this->config );
				$model->purge();
				remark( microtime( TRUE ) - $start );
				exit;
			case 'vacuum':
				$start	= microtime( TRUE );
				$this->initConfig();
				$this->initDatabase();
				$model	= new JsError_Model( $this->database, $this->config );
				$model->vacuum();
				remark( microtime( TRUE ) - $start );
				exit;
			default:
				$this->initConfig();
				$this->initDatabase();
				$view	= new JsError_Viewer( $this->database, $this->config );
		}
	}
}
?>
