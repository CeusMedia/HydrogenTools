<?php
class JsError_Model{
	public function __construct( $dbc, $config ){
		$tableName	= $config['database.prefix'].'log_js_errors';
		$columns	= array(
			'jsErrorId',
			'type',
			'uri',
			'line',
			'message',
			'code',
			'agent',
			'timestamp'
		);
		$this->table	= new Database_PDO_TableWriter( $dbc, $tableName, $columns, 'jsErrorId' );
		$this->dbc		= $dbc;
	}

	public function add( $data, $stripTags = TRUE ){
		return $this->table->insert( $data, $stripTags );
	}

	public function cleanup( $numberKeep ){
		$results	= $this->dbc->query( "SELECT jsErrorId FROM ".$this->table->getTableName()." ORDER BY timestamp DESC LIMIT ".$numberKeep );
		foreach( $results->fetchAll( PDO::FETCH_OBJ ) as $entry )
			$ids[]	= $entry->jsErrorId;
		$query	= "DELETE FROM ".$this->table->getTableName()." WHERE jsErrorId NOT IN (".join(",", $ids ).");";
		$this->dbc->query( $query );
		$this->vacuum();
	}

	public function edit( $id, $data, $stripTags = TRUE ){
		$this->table->defocus();
		$this->table->focusPrimary( $id );
		$status	= $this->table->update( $data, $stripTags );
		$this->table->defocus();
		return $status;
	}

	public function get( $id ){
		$this->table->defocus();
		$this->table->focusPrimary( $id );
		$data	= $this->table->get( TRUE );
		$this->table->defocus();
		return $data;
	}

	public function index( $conditions = array(), $orders = array(), $limits = array() ){
		$this->table->defocus();
		$data	= $this->table->find( NULL, $conditions, $orders, $limits );
		return $data;
	}

	public function remove( $id ){
		$this->table->defocus();
		$this->table->focusPrimary( $id );
		$data	= $this->table->get( TRUE );
		$this->table->delete();
		return $data;
	}

	public function purge(){
		$this->dbc->query( "DELETE FROM ".$this->table->getTableName()." WHERE 1" );
		$this->vacuum();
	}

	public function vacuum(){
		$this->dbc->query( "VACUUM ".$this->table->getTableName() );
	}
}
?>
