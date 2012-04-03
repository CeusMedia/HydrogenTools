<?php
/**
 *	Main Script of Job Error Log Viewer.
 *	@package		mv2.tools
 *	@uses			JobErrorViewer
 *	@author			Christian Würker <christian.wuerker@ceusmedia.de>
 *	@since			11.10.2007
 *	@version		0.1
 */
require_once 'cmClasses/trunk/autoload.php5';
CMC_Loader::registerNew( 'php5', NULL, '..' );

$pathApp	= 'projects/Chat/client/';
$pathLog	= 'logs/js.error.log';

$pathRoot	= getEnv( 'DOCUMENT_ROOT' ).'/';
$fileName	= $pathRoot.$pathApp.$pathLog;

header( "Pragma: No-cache" );

if( isset( $_GET['rss'] ) )
{
	$feed	= new JsErrorLog_Feed();
//	$feed->logAccess();
	$feed->displayFeed();
	exit;
}
new JsErrorLog_Viewer( $fileName );
?>
