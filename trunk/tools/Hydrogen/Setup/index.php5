<?php
/*  --  LIBRARY SETTINGS  --  */
$pathLibraries	= '';
$versionCMC		= '';
$versionCMF		= '';
$versionCMM		= '';
$autoloadPaths	= array(
	array( 'path' => 'classes/', 'prefix' => 'Tool_Hydrogen_Setup_' ),
	array( 'path' => 'classes/', 'prefix' => NULL ),
);

/*  --  APPLICATION SETTINGS  --  */
$instanceId		= NULL;//'Setup';

/*  --  RUN APPLICATION  --  */
try{
	require_once 'boot.php5';
	Environment::$configFile	= "config/config.ini";
	$env	= new Tool_Hydrogen_Setup_Environment( $instanceId );
	$app	= new Tool_Hydrogen_Setup_App( $env );
	$app->run();
}
catch( Exception $e ){
	UI_HTML_Exception_Page::display( $e );
}
?>