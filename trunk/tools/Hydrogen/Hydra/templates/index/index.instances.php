<?php
$list		= array();
foreach( $instances as $id => $entry ){
	$entry->configPath	= !empty( $entry->configPath ) ? $entry->configPath : 'config/';
	$entry->configFile	= !empty( $entry->configFile ) ? $entry->configFile : 'config.ini';

	$configFile	= $entry->uri.$entry->configPath.$entry->configFile;
	$class		= $instanceId === $id ? array( 'active' ) : array();
	$class[]	= file_exists( $configFile ) ? 'check-okay' : 'check-fail';
	$url		= './admin/instance/select/'.$id;
	$attributes	= array(
		'href'				=> $url,
		'class'				=> 'instance',
		'data-instance-id'	=> $id,
	);
	$link		= UI_HTML_Tag::create( 'a', $entry->title, $attributes );
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
</fieldset>
<script>
function checkForUpdates(){
	$("ul.instances li.check-okay").each(function(){
		var instanceId = $(this).children("a").data("instanceId");
		$.ajax({
			url: "./index/ajaxListModuleUpdates/?forceInstanceId="+instanceId,
			dataType: "json",
			context: $(this),
			success: function(json){
				if(json.missing.length)
					$(this).addClass("check-modules-missing");
				else if(json.updatable.length)
					$(this).addClass("check-modules-updatable");
//				console.log(json);
			},
			error: function(a){
//				console.log(a);
			}
		});
	});
}
$(document).ready(function(){
	checkForUpdates();
});
</script>
';
?>
