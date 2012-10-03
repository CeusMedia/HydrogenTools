<?php
if( empty( $pathLibs ) )
	$pathLibs		= '';
if( empty( $versionCMC ) )
	$versionCMC		= 'trunk';
if( empty( $versionCMF ) )
	$versionCMF		= 'trunk';

require_once $pathLibs.'cmClasses/'.$versionCMC.'/autoload.php5';
require_once $pathLibs.'cmFrameworks/'.$versionCMF.'/autoload.php5';
require_once dirname( __FILE__ ).'/Tool.php5';
if( empty( $path ) )
	$path	= dirname( dirname( dirname( getEnv( 'SCRIPT_FILENAME' ) ) ) ).'/';
if( empty( $exts ) )
	$exts	= array( 'php', 'js' );

new Todos_Tool( $path, $exts );
?>