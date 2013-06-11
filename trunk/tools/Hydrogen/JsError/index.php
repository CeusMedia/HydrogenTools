<?php
require_once 'cmClasses/trunk/autoload.php5';

$pathApp	= '/projects/Chat/client/';
$pathRoot	= getEnv( 'DOCUMENT_ROOT' );

CMC_Loader::registerNew( 'php5', 'JsError_', 'classes/' );
$fileConfig	= 'config.ini';

chdir( dirname( __FILE__ ) );
$d	= new JsError_Dispatcher();
?>



