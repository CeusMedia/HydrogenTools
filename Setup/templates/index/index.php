<?php

$list		= array();
$model		= new Model_Instance( $this->env );
foreach( $model->getAll() as $instance ){
	$class	= $instanceId == $instance->id ? 'active' : "";
	$link	= UI_HTML_Elements::Link( './?instanceId='.$instance->id, $instance->title );
	$list[$instance->title]	= UI_HTML_Tag::create( 'li', $link, array( 'class' => $class ) );
}
ksort( $list );

$list	= UI_HTML_Tag::create( 'ul', $list, array( 'class' => 'instances' ) );
$panelList	= '
<fieldset>
	<legend>Instanzen</legend>
	'.$list.'
</fieldset>';


$panelRemote	= '';
if( $remote	= $this->env->getRemote() ){

	ob_start();
	$config	= $remote->getConfig()->getAll();
	if( isset( $config['database.password'] ) )
		unset( $config['database.password'] );
	print_m( $config );
	$config	= ob_get_clean();
	
	$panelRemote	= '
<fieldset>
	<legend class="info">Instanz</legend>
	'.$config.'
</fieldset>
';
}

return '
<style>
ul.instances {
	margin: 0px;
	padding: 0px;
	}
ul.instances li {
	padding: 1px 4px;
	margin: 0px;
	list-style: none;
	font-size: 1.1em;
	}
ul.instances li.active {
	background-color: #EEE;
	}
ul.instances li.active a {
	}
</style>
<br/>
<div class="column-left-25">
	'.$panelList.'
</div>
<div class="column-left-75">
	'.$panelRemote.'
</div>
<div class="column-clear"></div>
';
?>