<?php
/**
 *	Reader for JavaScript Error Log File.
 *	@package		mv2.tools
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			11.10.2007
 *	@version		0.1
 */
/**
 *	Reader for JavaScript Error Log File.
 *	@package		mv2.tools
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			11.10.2007
 *	@version		0.1
 */
class JsError_Reader
{
	/**	@var		string		$fileName		File Name of Log File */
	private $fileName;
	/**	@var		string		$pattern		Reg Ex for every Line */
	private $pattern	= "";

	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		string		$fileName	File Name of Log File
	 *	@return		void
	 */
	public function __construct( $fileName = "logs/js_errors.log" )
	{
		$this->fileName	= $fileName;	
		$this->pattern	= "@^([0-9]+)\|(.+)\[([0-9]+)\]:(.+)\|([a-z0-9=]+)$@i";
	}

	/**
	 *	Returns List of parsed Lines.
	 *	@access		public
	 *	@return		array
	 */
	public function getList()
	{
		$data	= array();
		if( !file_exists( $this->fileName ) )
			throw new Exception( "Log File is not existing." );
		$lines		= file( $this->fileName );
		foreach( $lines as $line )
		{
			$line	= trim( $line );
			if( !$line )
				continue;
			$data[]	= $this->parseLine( $line );
		}
		return $data;
	}

	/**
	 *	Parses Log File.
	 *	@access		protected
	 *	@return		array
	 */
	protected function parseLine( $line )
	{
		$pattern	= "";
		$data	= preg_replace_callback( $this->pattern, array( $this, 'callback' ), $line );
		return unserialize( $data );
	}

	/**
	 *	Callback for Line Parser.
	 *	@access		protected
	 *	@return		string
	 */
	protected function callback( $matches )
	{
		$data	= array(
			'raw'		=> $matches[0],
			'timestamp'	=> $matches[1],
			'datetime'	=> date( "j.n.y H:i", $matches[1] ),
			'file'		=> $matches[2],
			'line'		=> $matches[3],
			'message'	=> $matches[4],
			'agent'		=> $matches[5],
		);
		return serialize( $data );
	}
}
?>