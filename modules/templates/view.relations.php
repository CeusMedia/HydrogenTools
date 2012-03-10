<?php

$count	= 0;
$relationsNeeded		= '-';
$relationsSupported	= '-';
if( $module->relations->needs ){
	$relationsNeeded	= array();
	foreach( $module->relations->needs as $moduleName ){
		$count++;
		$label	= UI_HTML_Tag::create( 'span', $moduleName, array( 'class' => "icon module" ) );
		$relationsNeeded[]	= UI_HTML_Elements::ListItem( $label, 1 );
	}
	$relationsNeeded		= UI_HTML_Elements::unorderedList( $relationsNeeded, 1, array( 'class' => 'relations-needed' ) );
}
if( $module->relations->supports ){
	$relationsSupported	= array();
	foreach( $module->relations->supports as $moduleName ){
		$count++;
		$label	= UI_HTML_Tag::create( 'span', $moduleName, array( 'class' => "icon module" ) );
		$relationsSupported[]	= UI_HTML_Elements::ListItem( $label, 1 );
	}
	$relationsSupported		= UI_HTML_Elements::unorderedList( $relationsSupported, 1, array( 'class' => 'relations-supported' ) );
}
return '
<dl>
	<dt>'.$words['view']['relationsNeeded'].'</dt>
	<dd>'.$relationsNeeded.'</dd>
	<dt>'.$words['view']['relationsSupported'].'</dt>
	<dd>'.$relationsSupported.'</dd>
</dl><div class="clearfix"></div>';
?>