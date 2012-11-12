<?php
/*  --  BOOT  --  */
function _cmRealize( $constant, $variable, $default ){
	$var	= isset( $GLOBALS[$variable] ) ? $GLOBALS[$variable] : $default;
	if( strlen( $constant ) ){
		if( !defined( $constant ) )
			define( $constant, $var );
		$var	= constant( $constant );
	}
	return $var;
}
/*  --  LOAD LIBRARIES  --  */
_cmRealize( 'CML_PATH', 'pathLib', '/var/www/lib/' );
require_once CML_PATH.'cmClasses/'._cmRealize( 'CMC_VERSION', 'verCMC', 'trunk' ).'/autoload.php5';
require_once CML_PATH.'cmFrameworks/'._cmRealize( 'CMF_VERSION', 'verCMF', 'trunk' ).'/autoload.php5';
require_once CML_PATH.'cmModules/'._cmRealize( 'CMM_VERSION', 'verCMM', 'trunk' ).'/autoload.php5';

/*  --  LOAD CLASS PATHS  --  */
if( isset( $classes ) && is_array( $classes ) )
	foreach( $classes as $library )
		if( $library = (object) array_merge( array( 'path' => NULL, 'exts' => NULL, 'prefix' => NULL ), $library ) )	//  
			CMC_Loader::registerNew( NULL, $library->prefix, $library->path );

?>