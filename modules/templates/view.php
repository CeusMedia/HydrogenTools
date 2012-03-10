<?php
$w	= (object) $words['view'];

UI_HTML_Tabs::$version	= 3;
$tabs	= new UI_HTML_Tabs();
$this->env->page->js->addScript( '$(document).ready(function(){'.$tabs->buildScript( '#tabs-module' ).'});' );
/*$this->env->page->js->addUrl( 'http://js.ceusmedia.com/jquery/ui/1.8.4/min.js' );
$this->env->page->css->theme->addUrl( 'http://js.ceusmedia.com/jquery/ui/1.8.4/css/smoothness.css' );
*/

$mapTabs	= array(
	'general'	=> 'tabGeneral',
	'resources'	=> 'tabResources',
	'config'	=> 'tabConfiguration',
	'database'	=> 'tabDatabase',
	'relations'	=> 'tabRelations',
);

foreach( $mapTabs as $key => $tabLabel ){
	$count		= 0;
	$content	= require_once( 'templates/view.'.$key.'.php' );
	$label		= $w->$tabLabel;
	$label		.= $count ? ' <small>('.$count.')</small>' : '';
	$tabs->addTab( $label, $content );
}

$disabled			= $module->type == 4 ? '' : 'disabled';
$buttonCancel		= UI_HTML_Elements::LinkButton( './', $w->buttonCancel, 'button cancel' );
$buttonInstall		= UI_HTML_Elements::LinkButton( './?action=install&moduleId='.$module->id, $w->buttonInstall, 'button add', NULL, $disabled );
$disabled			= $module->type == 4 ? 'disabled' : '';
$buttonEdit			= UI_HTML_Elements::LinkButton( './?action=edit&moduleId='.$module->id, $w->buttonEdit, 'button edit', NULL, $disabled );
$buttonUninstall	= UI_HTML_Elements::LinkButton( './?action=uninstall&moduleId='.$module->id, $w->buttonRemove, 'button remove', 'Die Modulkopie oder -referenz wird gelöscht. Wirklich?', $disabled );
$buttons			= '<div class="buttonbar">
	'.$buttonCancel.'
	'.$buttonInstall.'
	'.$buttonEdit.'
	'.$buttonUninstall.'
</div>';

return '
<div class="ui-widget ui-widget-content ui-corner-all">
	<div style="padding: 1em 2em">
		<h3>Modul: <cite>'.$module->title.'</cite></h3>
		<a href="./">&laquo;&nbsp;zurück zur Liste</a>
	<!--<fieldset>
		<legend>Test</legend>-->
		'.$tabs->buildTabs( 'tabs-module' ).'
	<!--</fieldset>-->
		'.$buttons.'
	</div>
</div>
';
?>
