<?php
require_once 'cmClasses/trunk/autoload.php5';
require_once 'cmFrameworks/trunk/autoload.php5';

CMC_Loader::registerNew( 'php5', NULL, '..' );

$path	= dirname( dirname( dirname( getEnv( 'SCRIPT_FILENAME' ) ) ) ).'/';
$exts	= array( 'php', 'js' );

new Todos_Tool( $path, $exts );
?>