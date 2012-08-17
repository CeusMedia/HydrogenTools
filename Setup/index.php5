<?php
$pathCMC	= '/var/www/lib/cmClasses/trunk/';
$pathCMF	= '/var/www/lib/cmFrameworks/trunk/';
$pathCMM	= '/var/www/lib/cmModules/trunk/';

/*  --  NO CHANGES NEEDED BELOW  --  */
require_once $pathCMC.'autoload.php5';
require_once $pathCMF.'autoload.php5';
require_once $pathCMM.'autoload.php5';

$instanceId		= NULL;//'Setup';

CMC_Loader::registerNew( 'php5', 'Tool_Hydrogen_Setup_', 'classes/' );
CMC_Loader::registerNew( 'php5', NULL, 'classes/' );

try{
	$env	= new Tool_Hydrogen_Setup_Environment( $instanceId );
	$app	= new Tool_Hydrogen_Setup_App( $env );
	$app->run();
}
catch( Exception $e ){
	UI_HTML_Exception_Page::display( $e );
	exit;
}

?>