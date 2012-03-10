<?php

$count	= 0;
$config	= '-';
if( $module->config ){
	$config	= array();
	foreach( $module->config as $key => $value ){
		$count++;
		$config[]	= UI_HTML_Tag::create( 'dt', $key ).UI_HTML_Tag::create( 'dd', $value );
	}
	$config	= UI_HTML_Tag::create( 'dl', join( $config ) );
}

return $config.'<div class="clearfix"></div>';
?>