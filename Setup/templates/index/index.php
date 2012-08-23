<?php

function countCores(){
	exec( 'cat /proc/cpuinfo | grep processor | wc -l', $processors );
	return array_shift( $processors );
}

function getRevision( $path = '' ){
	exec( 'svn info --xml '.$path, $a );
	$xml	= implode( "\n", $a );
	try {
		$parser	= new XML_DOM_Parser();
		$tree	= $parser->parse( $xml );
		$rev	= $tree->getChild( 'entry' )->getChild( 'commit' )->getAttribute( 'revision' );
		return (int) $rev;
	}
	catch( Exception $e ){
		return -1;
	}
}



$list		= array();
foreach( $instances as $instance ){
	if( empty( $instance->configPath ) )
		$instance->configPath	= 'config/';
	if( empty( $instance->configFile ) )
		$instance->configFile	= 'config.ini';
	$configFile	= getEnv( 'DOCUMENT_ROOT' ).'/'.$instance->path.$instance->configPath.$instance->configFile;
	$class	= array( file_exists( $configFile ) ? 'check-okay' : 'check-fail' );
	if( $instanceId == $instance->id )
		$class[]	= 'active';
	$link	= UI_HTML_Elements::Link( './?selectInstanceId='.$instance->id, $instance->title );
	$list[$instance->title]	= UI_HTML_Tag::create( 'li', $link, array( 'class' => join( ' ', $class ) ) );
}
ksort( $list );

$list	= UI_HTML_Tag::create( 'ul', $list, array( 'class' => 'instances' ) );
$panelList	= '
<fieldset>
	<legend>Instanzen</legend>
	'.$list.'
</fieldset>';

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
$panelConfig	= '
<fieldset>
	<legend class="info">Konfiguration</legend>
	<div style="/*max-height: 260px; overflow: auto*/">
		'.join( $l ).'
	</div>
</fieldset>';

/*
$listConfig	= array();
foreach( $remoteConfig->getAll() as $key => $value )
	if( !preg_match( '/password|secret/', $key ) )
		$listConfig[$key]	= '<dt>'.$key.'</dt><dd>'.$value.'</dd>';
natcasesort( $listConfig );
$panelConfig	= '
<fieldset>
	<legend class="info">Konfiguration</legend>
	<div style="max-height: 160px; overflow: auto">
		<dl>'.join( $listConfig ).'</dl>
	</div>
</fieldset>';
 */

/*  --  LIST: MODULES INSTALLED  --  */
$listModulesInstalled	= array();
foreach( $modulesInstalled as $moduleId => $module ){
	$desc	= trim( array_shift( explode( "\n", $module->description ) ) );
	$label	= $desc ? '<acronym title="'.$desc.'">'.$module->title.'</acronym>' : $module->title;
	$label	= '<span class="module">'.$label.'</span>';
	$link	= '<a href="./admin/module/editor/view/'.$moduleId.'">'.$label.'</a>';
	$listModulesInstalled[$module->title]	= '<li>'.$link.'</li>';	
}
natcasesort( $listModulesInstalled );
$panelModulesInstalled	= '
<fieldset style="position: relative">
	<legend class="info">Module installiert <span class="small">('.count( $listModulesInstalled ).')</span></legend>
	<div style="position: absolute; right: 8px; top: 16px;">
		'.UI_HTML_Elements::LinkButton( './admin/module/installer', '', 'button tiny icon add' ).'
	</div>
	<div style="max-height: 160px; overflow: auto">
		<ul>'.join( $listModulesInstalled ).'</ul>
	</div>
</fieldset>';

/*  --  LIST: MODULES MISSING  --  */
$listModulesMissing	= array();
$listModulesPossible	= array();
foreach( $modulesMissing as $moduleId ){
	$label	= $moduleId;
	if( array_key_exists( $moduleId, $modulesAll ) ){
		$desc	= trim( array_shift( explode( "\n", $module->description ) ) );
		$label	= $desc ? '<acronym title="'.$desc.'">'.$module->title.'</acronym>' : $module->title;
	}
	$label	= '<span class="module">'.$label.'</span>';
	$link	= '<a href="./admin/module/editor/view/'.$moduleId.'">'.$label.'</a>';
	$listModulesMissing[$module->title]	= '<li>'.$link.'</li>';	
}
foreach( $modulesPossible as $moduleId ){
	$label	= $moduleId;
	if( array_key_exists( $moduleId, $modulesAll ) ){
		$desc	= trim( array_shift( explode( "\n", $module->description ) ) );
		$label	= $desc ? '<acronym title="'.$desc.'">'.$module->title.'</acronym>' : $module->title;
	}
	$label	= '<span class="module">'.$label.'</span>';
	$link	= '<a href="./admin/module/editor/view/'.$moduleId.'">'.$label.'</a>';
	$listModulesPossible[$module->title]	= '<li>'.$link.'</li>';	
}
natcasesort( $listModulesMissing );
natcasesort( $listModulesPossible );

$list	= array();
if( $listModulesMissing )
	$list[]	= '<dt>Fehlen</dt><dd><ul>'.join( $listModulesMissing ).'</ul></dd>';
if( $listModulesPossible )
	$list[]	= '<dt>Unterstützt</dt><dd><ul>'.join( $listModulesPossible ).'</ul></dd>';

$panelModulesRelated	= '
<fieldset style="position: relative">
	<legend class="info">Modulebeziehungen <span class="small">('.count( $listModulesMissing ).')</span></legend>
	<div style="max-height: 160px; overflow: auto">
		<dl>'.join( $list ).'</dl>
	</div>
</fieldset>';


$panelModules	= '
<fieldset style="position: relative">
	<legend class="info">Module</legend>
<!--	<div style="position: absolute; right: 8px; top: 16px;">
		'.UI_HTML_Elements::LinkButton( './admin/module/installer', '', 'button tiny icon add' ).'
	</div>
	<div style="max-height: 160px; overflow: auto">-->
		<ul>
			<li>'.count( $modulesInstalled ).' installiert</li>
			<li>'.count( $modulesMissing ).' fehlen</li>
			<li>'.count( $modulesPossible ).' möglich</li>
		</ul>
<!--	</div>
--></fieldset>';


$name	= '<cite>'.$remoteConfig->get( 'app.name' ).'</cite>';
if( strlen( $remoteConfig->get( 'app.version' ) ) )
	$name	.= ' <span class="small">v'.$remoteConfig->get( 'app.version' ).'</span>';
if( strlen( $remoteConfig->get( 'app.revision' ) ) )
	$name	.= ' <span class="small">rev'.$remoteConfig->get( 'app.version' ).'</span>';

$link	= '<a href="'.$remoteConfig->get( 'app.base.url' ).'">'.$remoteConfig->get( 'app.base.url' ).'</a>';
$panelInfo	= '
<fieldset style="position: relative">
	<legend class="info">Application Instance Information</legend>
	<div style="position: absolute; right: 8px; top: 16px;">
		'.UI_HTML_Elements::LinkButton( './admin/instance/edit/'.$instanceId, '', 'button tiny edit' ).'
	</div>
	<dl>
		<dt>Application Name</dt><dd>'.$name.'</cite></dd>
		<dt>Application URL</dt><dd>'.$link.'</dd>
		<dt>Path to Application Instance</dt><dd><a href="file://'.$remote->path.'" target="_blank">'.$remote->path.'</a></dd>
	</dl>
</fieldset>';

$diskTotal	= (double) disk_total_space( __DIR__ );
$diskFree	= (double) disk_free_space( __DIR__ );
$diskRatio	= round( $diskFree / $diskTotal * 100, 1 ); 

$configCMC	= parse_ini_file( CMC_PATH.'../cmClasses.ini', TRUE );
$versionCMC	= $configCMC['project']['version'];

$panelSystem	= '
<fieldset>
	<legend class="info">Server</legend>
	<dl>
		<dt>CPU Load</dt><dd>'.array_shift( sys_getloadavg() ).' @ '.countCores().' cores</dd>
		<dt>Disk Space</dt><dd>'.Alg_UnitFormater::formatBytes( $diskFree, 1 ).' / '.Alg_UnitFormater::formatBytes( $diskTotal, 1 ).' ('.$diskRatio.'% frei)</dd>
		<dt>Server Software</dt><dd>
			<ul>
				<li>'.$_SERVER['SERVER_SOFTWARE'].'</li>
				<li>PHP/'.phpversion().'</li>
				<li>cmClasses/'.$versionCMC.' <span class="small">(rev '.getRevision( CMC_PATH ).')</span></li>
				<li>cmFrameworks <span class="small">(rev '.getRevision( CMF_PATH ).')</span></li>
				<li>cmModules <span class="small">(rev '.getRevision( CMM_PATH ).')</span></li>
				<li>Hymn/'.$config->get( 'app.version' ).' <span class="small">(rev '.getRevision().')</span></li>
			</ul>
		</dd>
	</dl>
</fieldset>';

return '
<style>
ul.instances {
	margin: 0px;
	padding: 0px;
	}
ul.instances li {
	min-height: 18px;
	padding: 1px 4px;
	margin: 0px;
	list-style: none;
	font-size: 1.1em;
	}
ul.instances li.active {
	background-color: #EEE;
	}
ul.instances li.check-okay,
ul.instances li.check-fail {
	background-repeat: no-repeat;
	background-position: 3px 2px;
	padding-left: 22px;
	}
ul.instances li.check-okay {
	background-image: url(http://img.int1a.net/famfamfam/silk/tick.png);
	}
ul.instances li.check-fail {
	background-image: url(http://img.int1a.net/famfamfam/silk/cross.png);
	}
ul.instances li.active a {
	}

.index-config-module {
	cursor: pointer;
	background-color: #EFEFEF;
	padding: 4px 10px 2px 10px;
	height: 1.5em;
	line-height: 1.5em;
	}

dl.index-config {
	display: none;
	}
dl.index-config dt {
	clear: both;
	float: left;
	margin: 0px;
	padding: 1px 2px 1px 10px;
	font-weight: normal;
	font-size: 1.0em;
	}
dl.index-config dd {
	margin: 0px;
	padding: 0px 6px 0px 0px;
	border-bottom: 1px solid #DDD;
	text-align: right;
	min-height: 18px;
	font-size: 1.1em;
	}


</style>
<script>
$(document).ready(function(){
	$(".index-config-module").bind("click",function(){$(this).next().slideToggle();});
});
</script>
<br/>
<div class="column-left-25">
	'.$panelList.'
	'.$panelSystem.'
</div>
<div class="column-right-75">
	<div class="column-left-50">
		'.$panelInfo.'
	</div>
	<div class="column-right-50">
		'.$panelModules.'
	</div>
	<div class="column-clear">
		<div class="column-left-50">
			'.$panelModulesInstalled.'
		</div>
		<div class="column-right-50">
			'.$panelModulesRelated.'
		</div>
	</div>
	<div class="column-clear">
		'.$panelConfig.'
	</div>
</div>
<div class="column-clear"></div>
';
?>