<?php
require_once 'cmClasses/trunk/autoload.php5';
require_once 'cmFrameworks/trunk/autoload.php5';

$pathModules	= 'lib/cmFrameworks/trunk/modules/Hydrogen/';					//  must be absolute or relative to document root
$pathApp		= 'sandbox/Hydrogen/';										//  must be absolute or relative to document root
$pathConfig		= 'config/';													//  if configuration files of application are inside a folder, relative to app path, default: ./ 

error_reporting( E_ALL );

CMC_Loader::registerNew( 'php', NULL, 'classes/' );

$manager	= new Manager( $pathModules, $pathApp, $pathConfig );
$manager->run();
?>
