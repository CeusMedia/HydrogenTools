<?php
/**
 *	Viewer for JavaScript Dev Log File.
 *	@package		mv2.tools
 *	@uses			File_Log_JS_Reader
 *	@author			Christian Würker <Christian.Wuerker@CeuS-Media.de>
 *	@since			11.10.2007
 *	@version		0.1
 */
import( 'de.ceus-media.file.log.js.Reader' );
/**
 *	Viewer for JavaScript Dev Log File.
 *	@package		mv2.tools
 *	@uses			File_Log_JS_Reader
 *	@author			Christian Würker <Christian.Wuerker@CeuS-Media.de>
 *	@since			11.10.2007
 *	@version		0.1
 */
class JavaScriptDevLogViewer
{
	/**
	 *	Constructor.
	 *	@access		public
	 *	@param		string		$fileName			File Name of Log File
	 *	@param		string		$templateView		File Name of Main Template
	 *	@param		string		$templateTable		File Name of Table Template
	 *	@param		string		$templateRow		File Name of Table Row Template
	 *	@return		void
	 */
	public function __construct( $fileName, $templateMain = "main.phpt", $templateTable = "table.phpt", $templateRow = "row.phpt" )
	{
		if( !file_exists( $templateMain ) )
			throw new Exception( 'Main Template File "'.$templateMain.'" is not existing.' );
		if( !file_exists( $fileName ) )
			$table	= "Log File is not existing. No Dev Messages found.";
		else
		{
			$reader		= new File_Log_JS_Reader( $fileName );
			$data		= $reader->getList( TRUE );
			$table		= $this->getTable( $data, $templateTable, $templateRow );
		}
		return require_once( $templateMain );
	}
	
	/**
	 *	Constructor.
	 *	@access		private
	 *	@param		array		$data				Array of Log File Lines
	 *	@param		string		$templateTable		File Name of Table Template
	 *	@param		string		$templateRow		File Name of Table Row Template
	 *	@return		string
	 */
	private function getTable( $data, $templateTable = "table.phpt", $templateRow = "row.phpt" )
	{
		if( !file_exists( $templateTable ) )
			throw new Exception( 'Table Template File "'.$templateTable.'" is not existing.' );
		if( !file_exists( $templateRow ) )
			throw new Exception( 'Table Row Template File "'.$templateRow.'" is not existing.' );
		$list	= '<tr><td colspan="5"><b>No Log found.</b><br/>Maybe there is no Log (=no Dev Messages) or Log URL is misconfigured.</td></tr>';
		if( $data )
		{
			$list	= array();
			foreach( $data as $entry )
			{
				if( !$entry )
					continue;
				$entry['hash']	= md5( serialize( $entry ) );
				$date	= date( "d.m.Y", (int) $entry['timestamp'] );
				$time	= date( "H:i:s", (int) $entry['timestamp'] );
				$list[]	= require( $templateRow );
			}
			$list	= implode( "\n", $list );
		}
		return require_once( $templateTable );
	}
}
?>