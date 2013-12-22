<?php
$list		= array();
foreach( $instances as $id => $entry ){
	$entry->configPath	= !empty( $entry->configPath ) ? $entry->configPath : 'config/';
	$entry->configFile	= !empty( $entry->configFile ) ? $entry->configFile : 'config.ini';

	$configFile	= $entry->uri.$entry->configPath.$entry->configFile;
	$class		= $instanceId === $id ? array( 'active' ) : array();
	$class[]	= file_exists( $configFile ) ? 'check-okay' : 'check-fail';
	$url		= './admin/instance/select/'.$id;
	$link		= UI_HTML_Elements::Link( $url, $entry->title, 'instance' );
	$attributes	= array(
		'class'		=> join( ' ', $class ),
		'data-url'	=> $entry->protocol.$entry->host.$entry->path
	);
	$item		= UI_HTML_Tag::create( 'li', $link, $attributes );
	$list[$entry->title]	= $item;
}
ksort( $list );

$list	= UI_HTML_Tag::create( 'ul', $list, array( 'class' => 'instances' ) );

return '
<fieldset>
	<legend>Instanzen</legend>
	<div style="position: absolute; right: 8px; top: 16px;">
		'.UI_HTML_Elements::LinkButton( './admin/instance/', '', 'button tiny edit' ).'
	</div>
	'.$list.'
</fieldset>';
?>
