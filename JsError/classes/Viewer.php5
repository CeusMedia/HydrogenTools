<?php
/**
 *	Viewer for JavaScript Error Log File.
 *	@package		mv2.tools
 *	@uses			File_Log_JS_Reader
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			11.10.2007
 *	@version		0.1
 */
/**
 *	Viewer for JavaScript Error Log File.
 *	@package		mv2.tools
 *	@uses			File_Log_JS_Reader
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			11.10.2007
 *	@version		0.1
 */
class JsError_Viewer
{
	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		string		$logFile			File Name of Log File
	 *	@param		string		$templateView		File Name of Main Template
	 *	@param		string		$templateTable		File Name of Table Template
	 *	@param		string		$templateRow		File Name of Table Row Template
	 *	@return		void
	 */
	public function __construct( $database, $config, $templateMain = "main.phpt", $templateTable = "table.phpt", $templateRow = "row.phpt" )
	{
		$this->config	= $config;
		$this->database	= $database;

		$this->model	= new JsError_Model( $database, $config );
		
		if( isset( $_GET['remove'] ) )
			$this->model->remove( $_GET['remove'] );
		$data	= $this->model->index();
		$table	= $this->getTable( $data, $templateTable, $templateRow );

		return require_once( 'templates/'.$templateMain );
	}

	private function showCode( $fileName, $lineNr ){
		$content	= Net_Reader::readUrl( $fileName );
		$lines		= explode( "\n", $content );
		$list		= array();
		foreach( $lines as $nr => $line ){
				$class	= ( $nr + 1 == $lineNr ) ? 'selected' : NULL;
//			$line	= preg_replace( '/([a-z0-9.]+)(\s*)(:|=)(\s*)/i', '<span style="color: red">\\1</span>\\2\\3\\4', $line );
			$line	= preg_replace( '/function\(/', '<span class="code-function">function</span>(', $line );
/*			$line	= preg_replace( '/([a-z0-9.]+)\(/i', '<span style="color: blue">\\1</span>(', $line );
*/			$line	= preg_replace( '/(},?)/', '<span style="color: gray">\\1</span>', $line );
			$line	= preg_replace( '/({)/', '<span style="color: gray">\\1</span>', $line );

			$line	= UI_HTML_Tag::create( 'pre', $line );
			$list[]	= UI_HTML_Tag::create( 'li', $line, array( 'class' => $class ) );
		}
		print( require_once 'code.phpt' );
		exit;
	}
	
	/**
	 *	Removes Document Root from File Name.
	 *	@access		private
	 *	@param		string		$fileName			File Name to clear
	 *	@return		string
	 */
	private function clearFileName( $fileName )
	{
		$root		= getEnv( 'DOCUMENT_ROOT' );
		$fileName	= str_replace( "\\", "/", $fileName );
		$fileName	= preg_replace( "@^".$root."@", "", $fileName );
		return $fileName;
	}

	/**
	 *	Constructor.
	 *	@access		private
	 *	@param		array		$data				Array of Log File Lines
	 *	@param		string		$templateTable		File Name of Table Template
	 *	@param		string		$templateRow		File Name of Table Row Template
	 *	@return		string
	 */
	private function getTable( $data, $templateTable = "table.phpt", $templateRow = "row.phpt" ){
		$list	= array();
		foreach( $data as $entry ){
			$file	= $this->clearFileName( $entry['uri'] );
			$date	= date( "d.m.Y", $entry['timestamp'] );
			$time	= date( "H:i:s", $entry['timestamp'] );

			$list[]	= require( 'templates/'.$templateRow );
		}
		$list	= implode( "", $list );
		return require_once( 'templates/'.$templateTable );
	}
}
?>