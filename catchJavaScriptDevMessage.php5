<?php
require_once 'cmClasses/0.7.0/autoload.php5';
$request	= new Net_HTTP_Request_Receiver;
$message	= trim( $request->get( 'message' ) );
$url		= trim( $request->get( 'url' ) );
if( !( $message && $url ) )
	die( 'insufficient data' );
$data	= array(
	'url'		=> $url,
	'message'	=> $message,
	'userAgent'	=> getEnv( 'HTTP_USER_AGENT' ),
);
$logFile	= "../logs/js/dev.log";
File_Log_JS_Writer::noteData( $logFile, $data );
?>