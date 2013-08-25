<?php
$list		= array();
foreach( $instances as $entry ){
#	print_m( $entry );
	if( empty( $entry->configPath ) )
		$entry->configPath	= 'config/';
	if( empty( $entry->configFile ) )
		$entry->configFile	= 'config.ini';
	$configFile	= $entry->uri.$entry->configPath.$entry->configFile;
	$class	= array( file_exists( $configFile ) ? 'check-okay' : 'check-fail' );
	if( $instanceId == $entry->id )
		$class[]	= 'active';
	$link	= UI_HTML_Elements::Link( './?selectInstanceId='.$entry->id, $entry->title );
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