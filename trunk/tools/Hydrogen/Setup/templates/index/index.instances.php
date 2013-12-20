<?php
$list		= array();
foreach( $instances as $id => $entry ){
	if( empty( $entry->configPath ) )
		$entry->configPath	= 'config/';
	if( empty( $entry->configFile ) )
		$entry->configFile	= 'config.ini';
	$configFile	= $entry->uri.$entry->configPath.$entry->configFile;
	$class	= array( file_exists( $configFile ) ? 'check-okay' : 'check-fail' );
	if( $instanceId == $id )
		$class[]	= 'active';
	$link	= UI_HTML_Elements::Link( './admin/instance/select/'.$id, $entry->title );
	$list[$entry->title]	= UI_HTML_Tag::create( 'li', $link, array( 'class' => join( ' ', $class ) ) );
}
ksort( $list );

$list	= UI_HTML_Tag::create( 'ul', $list, array( 'class' => 'instances' ) );
return '
<fieldset>
	<legend>Instanzen</legend>
	'.$list.'
</fieldset>';
?>