<?php
/**
 *	Main Script of Job Error Log Viewer.
 *	@package		mv2.tools
 *	@uses			JobErrorViewer
 *	@author			Christian Würker <Christian.Wuerker@CeuS-Media.de>
 *	@since			11.10.2007
 *	@version		0.1
 */
require_once( "trunk/useClasses.php5" );
$fileName	= "../../logs/js/dev.log";
import( 'de.ceus-media.ui.DevOutput' );
import( 'de.ceus-media.xml.rss.Builder' );
import( 'de.ceus-media.file.log.js.Reader' );


import( 'de.ceus-media.file.log.Writer' );
$log	= new File_Log_Writer( "rss.access.log" );
$log->note( getEnv( 'REMOTE_ADDR').' '.getEnv( 'HTTP_REFERER' ).' "'.getEnv( 'HTTP_USER_AGENT' ).'"' );


$reader	= new File_Log_JS_Reader( $fileName );
$list	= $reader->getList( TRUE, 20 );

$config		= parse_ini_file( "rss.ini" );
$builder	= new XML_RSS_Builder();
foreach( $config as $key => $value )
	$builder->setChannelPair( $key, $value );
foreach( $list as $entry )
{
	$item	= array(
		"title"			=> $entry['message'],
		"description"	=> "Message: ".$entry['message']."<br/>Agent: ".$entry['userAgent']."<br/>URL: ".$entry['url'],
		"date"			=> (int)$entry['timestamp'],
		"link"			=> $entry['url'],
	);
	$builder->addItem( $item );
}
header( 'Content-Type: text/xml; charset=utf-8' );
$xml = $builder->build();
echo $xml;
?>