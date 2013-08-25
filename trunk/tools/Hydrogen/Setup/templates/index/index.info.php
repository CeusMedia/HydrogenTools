<?php
if( empty( $remoteConfig ) )
	return "";
$name		= '<cite>'.$remoteConfig->get( 'app.name' ).'</cite>';
if( strlen( $remoteConfig->get( 'app.version' ) ) )
	$name	.= ' <span class="small">v'.$remoteConfig->get( 'app.version' ).'</span>';
if( strlen( $remoteConfig->get( 'app.revision' ) ) )
	$name	.= ' <span class="small">rev'.$remoteConfig->get( 'app.version' ).'</span>';

$instanceUrl		= $instance->protocol.$instance->host.$instance->path;
$instanceBase		= $remoteConfig->get( 'app.base.url' ) ? $remoteConfig->get( 'app.base.url' ) : "";

$linkInstanceUrl	= UI_HTML_Tag::create( 'a', $instanceUrl, array( 'href' => $instanceUrl ) );
$linkInstanceBase	= $instanceBase	? UI_HTML_Tag::create( 'a', $instanceBase, array( 'href' => $instanceBase ) ) : "<em>autodetect</em>";

return '
<fieldset style="position: relative">
	<legend class="info">Application Instance Information</legend>
	<div style="position: absolute; right: 8px; top: 16px;">
		'.UI_HTML_Elements::LinkButton( './admin/instance/edit/'.$instanceId, '', 'button tiny edit' ).'
	</div>
	<dl>
		<dt>Application Name</dt><dd>'.$name.'</cite></dd>
		<dt>Application Instance URL <small class="muted"><em>(defined by Hydra Instance)</em></small></dt><dd>'.$linkInstanceUrl.'</dd>
		<dt>Application Base URL <small class="muted"><em>(defined by application configuration)</em></small></dt><dd>'.$linkInstanceBase.'</dd>
		<dt>Path to Application Instance</dt><dd><a href="file://'.$remote->path.'" target="_blank">'.$remote->path.'</a></dd>
	</dl>
</fieldset>';
?>