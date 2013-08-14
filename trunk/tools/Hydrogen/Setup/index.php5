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
	Tool_Hydrogen_Setup_App::$classEnvironment		= "Tool_Hydrogen_Setup_Environment";
	Tool_Hydrogen_Setup_Environment::$configFile	= "config/config.ini";
	$app	= new Tool_Hydrogen_Setup_App();
	$app->run();
}
catch( Exception $e ){
	UI_HTML_Exception_Page::display( $e );
}
?>