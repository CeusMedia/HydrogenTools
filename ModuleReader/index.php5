<?php
require_once 'cmClasses/trunk/autoload.php5';
require_once 'cmFrameworks/trunk/autoload.php5';

$fileName	= '/var/www/lib/cmFrameworks/trunk/modules/Hydrogen/Info/Pages/module.xml';
$fileName	= 'Test.xml';

$reader		= new CMF_Hydrogen_Environment_Resource_Module_Reader();
$module		= $reader->load( $fileName, 'Test' );
print_m( $module );
?>