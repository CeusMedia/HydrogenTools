<?php
$pathCMC	= '/var/www/lib/cmClasses/trunk/';
$pathCMF	= '/var/www/lib/cmFrameworks/trunk/';
$pathCMM	= '/var/www/lib/cmModules/trunk/';

/*  --  NO CHANGES NEEDED BELOW  --  */
require_once $pathCMC.'autoload.php5';
require_once $pathCMF.'autoload.php5';
require_once $pathCMM.'autoload.php5';

$instanceId		= 'Setup';

CMC_Loader::registerNew( 'php5', 'Tool_Hydrogen_Setup_', 'classes/' );
CMC_Loader::registerNew( 'php5', NULL, 'classes/' );
$env	= new Tool_Hydrogen_Setup_Environment( $instanceId );
$app	= new Tool_Hydrogen_Setup_App( $env );
$app->run();
?>