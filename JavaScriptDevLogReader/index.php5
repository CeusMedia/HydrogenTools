<?php
/**
 *	Main Script of Job Error Log Viewer.
 *	@package		mv2.tools
 *	@uses			JobErrorViewer
 *	@author			Christian Würker <Christian.Wuerker@CeuS-Media.de>
 *	@since			11.10.2007
 *	@version		0.1
 */
require_once 'cmClasses/trunk/autoload.php5';
require_once 'JavaScriptDevLogViewer.php5';

$fileName	= "../../logs/js/dev.log";

/*  --  REMOVE ROW  --  */
if( isset( $_REQUEST['remove'] ) )
{
	$list	= array();
	$lines	= file( $fileName );
	foreach( $lines as $line )
	{
		if( !trim( $line ) )
			continue;
		$line	= trim( $line );
		$json	= json_decode( $line, TRUE );
		$hash	= md5( serialize( $json ) );
		if( $hash == $_GET['remove'] )
			continue;
		$list[]	= $line;
	}
	if( count( $lines ) != count( $list ) )
	{
		$lines	= implode( "\n", $list );
		file_put_contents( $fileName, $lines."\n" );
		header( "Location: ./?updated" );
	}
}
else if( !isset( $_REQUEST['updated'] ) )
{
	$data	= array(
		'timestamp'	=> time(),
		'url'		=> 'http://'.getEnv( 'HTTP_HOST' ).getEnv('REQUEST_URI'),
		'message'	=> "test: ".time(),
		'userAgent'	=> getEnv('HTTP_USER_AGENT')
	);
	error_log(json_encode($data)."\n",3,$fileName);
}

header( "Pragma: No-cache" );
new JavaScriptDevLogViewer( $fileName );
?>
