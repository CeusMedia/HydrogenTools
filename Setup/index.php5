<?php
/*  --  LIBRARY SETTINGS  --  */
$verCMC		= '';
$verCMF		= '';
$verCMM		= '';

$classes	= array(
	array( 'path' => 'classes/', 'prefix' => 'Tool_Hydrogen_Setup_' ),
	array( 'path' => 'classes/' ),
);

/*  --  APPLICATION SETTINGS  --  */
$instanceId		= NULL;//'Setup';

require_once 'boot.php5';
	
/*  --  RUN APPLICATION  --  */
try{
	$env	= new Tool_Hydrogen_Setup_Environment( $instanceId );
	$app	= new Tool_Hydrogen_Setup_App( $env );
	$app->run();
}
catch( Exception $e ){
	UI_HTML_Exception_Page::display( $e );
}
?>