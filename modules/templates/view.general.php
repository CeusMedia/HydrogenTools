<?php

$neededModules	= '-';
if( count( $module->neededModules ) ){
	$neededModules	= array();
	foreach( $module->neededModules as $moduleName => $moduleStatus ){
		$class	= $moduleStatus ? 'icon module' : 'icon module disabled';
		$label	= UI_HTML_Tag::create( 'span', $moduleName, array( 'class' => $class ) );
		$link	= UI_HTML_Elements::Link( './?action=details&moduleId='.$moduleName, $label );
		$neededModules[]	= $link;
	}
	$neededModules	= join( ', ', $neededModules );
}

$supportedModules	= '-';
if( count( $module->supportedModules ) ){
	$supportedModules	= array();
	foreach( $module->supportedModules as $moduleName => $moduleStatus ){
		$class	= $moduleStatus ? 'icon module' : 'icon module disabled';
		$label	= UI_HTML_Tag::create( 'span', $moduleName, array( 'class' => $class ) );
		$link	= UI_HTML_Elements::Link( './?action=details&moduleId='.$moduleName, $label );
		$supportedModules[]	= $link;
	}
	$supportedModules	= join( ', ', $supportedModules );
}

return '
<dl>
	<dt>'.$w->title.'</dt>
	<dd>'.$module->title.'</dd>
	<dt>'.$w->description.'</dt>
	<dd>'.$module->description.'</dd>
	<dt>'.$w->versionAvailable.'</dt>
	<dd>'.( $module->versionAvailable ? $module->versionAvailable : '-' ).'</dd>
	<dt>'.$w->versionInstalled.'</dt>
	<dd>'.( $module->versionInstalled ? $module->versionInstalled : '-' ).'</dd>
	<dt>'.$w->type.'</dt>
	<dd><span class="module-type type-'.$module->type.'">'.$words['types'][$module->type].'</span></dd>
	<dt>related needed modules</dt>
	<dd>'.$neededModules.'</dd>
	<dt>related supported modules</dt>
	<dd>'.$supportedModules.'</dd>
</dl>
<div class="clearfix"></div>';

?>