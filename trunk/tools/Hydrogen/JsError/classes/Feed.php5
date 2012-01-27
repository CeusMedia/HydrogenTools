<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class JsError_Feed
{
	public function __construct( $logFile = '../../logs/js/errors.log' )
	{
		$this->logFile	= $logFile;
	}

	public function logAccess( $logFile = 'rss.access.log' )
	{
		$log	= new File_Log_Writer( $logFile );
		$note	= sprintf(
			'%1$s %2$s "%1$s"',
			getEnv( 'REMOTE_ADDR'),
			getEnv( 'HTTP_REFERER' ),
			getEnv( 'HTTP_USER_AGENT' )
		);
		$log->note( $note );
	}

	/**
	 *	@todo	use File_Log_JS_Reader instead !
	 */
	public function displayFeed()
	{
		try
		{
			$reader	= new File_Log_JSON_Reader( $this->logFile );
			$list	= $reader->getList( TRUE, 10 );
		}
		catch( Exception $e )
		{
			$list	= array();
		}

		$builder	= new XML_RSS_Builder();
		$builder->setChannelData( parse_ini_file( 'rss.ini' ) );

		foreach( $list as $entry )
		{
			$item	= array(
				"title"			=> $entry['message'],
				"description"	=> "Agent: ".$entry['userAgent']."<br/>URL: ".$entry['file']."<br/>Line: ".$entry['line'],
				"pubDate"		=> (int) $entry['timestamp'],
				"link"			=> "",
			);
			$builder->addItem( $item );
		}
		$xml = $builder->build();
		header( 'Content-Type: text/xml; charset=utf-8' );
		print( $xml );
	}
}
?>