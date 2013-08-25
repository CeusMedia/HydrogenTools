<?php
if( empty( $remoteConfig ) )
	return "";
$l	= array();
foreach( $modulesInstalled as $module ){
	$list	= array();
	foreach( $module->config as $item ){
		if( preg_match( '/password|secret/', $item->key ) )
			$item->value	= str_repeat( '*', strlen( $item->value ) );
		$value	= $item->value;
		switch( $item->type ){
			case 'boolean':
			case 'bool':
				$value	= '<em style="color: #444">'.( ( (bool) $value ) ? "yes" : "no" ).'</em>';
				break;
			case 'integer':
			case 'int':
			case 'float':
				$value	= '<span style="font-family: monospace; font-size: 1.2em;">'.$value.'</span>';
				break;
			default:
				$value	= strlen( trim( $value ) ) ? htmlentities( $value ) : '&empty;';
		}
		$list[$item->key]	= '<dt>'.$item->key.'</dt><dd>'.$value.'</dd>';
	}
	natcasesort( $list );
	if( $list )
		$l[]	= '<h4 class="index-config-module">'.$module->title.'</h4><dl class="index-config">'.join( $list ).'</dl>';
}
if( empty( $l ) )
	return "";

return '
<fieldset>
	<legend class="info">Konfiguration</legend>
	<div style="max-height: 320px; overflow: auto;">
		'.join( $l ).'
	</div>
</fieldset>';
?>