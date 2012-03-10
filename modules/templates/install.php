<?php
$w	= (object) $words['install'];


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

$buttonCancel	= UI_HTML_Elements::LinkButton( './?action=details&moduleId='.$module->id, $w->buttonCancel, 'button cancel' );
$buttonInstall	= UI_HTML_Elements::Button( 'doInstall', $w->buttonInstall, 'button add' );

return '
<div class="ui-widget ui-widget-content ui-corner-all">
	<div style="padding: 1em 2em">
		<h3>Modul: <cite>'.$module->title.'</cite></h3>
		<a href="./?action=details&moduleId='.$module->id.'">&laquo;&nbsp;zurück zur Ansicht</a>
		<form action="./?action=install&moduleId='.$module->id.'" method="post">
			<dl>
				<dt>'.$w->labelTitle.'</dt>
				<dd>'.$module->title.'</dd>
				<dt>'.$w->labelDescription.'</dt>
				<dd>'.$module->description.'</dd>
				<dt>'.$w->labelVersionAvailable.'</dt>
				<dd>'.( $module->versionAvailable ? $module->versionAvailable : '-' ).'</dd>
				<dt>'.$w->labelVersionInstalled.'</dt>
				<dd>'.( $module->versionInstalled ? $module->versionInstalled : '-' ).'</dd>
				<dt>'.$w->labelType.'</dt>
				<dd><span class="module-type type-'.$module->type.'">'.$words['types'][$module->type].'</span></dd>
				<dt>related needed modules</dt>
				<dd>'.$neededModules.'</dd>
				<dt>related supported modules</dt>
				<dd>'.$supportedModules.'</dd>
			</dl>
			<div class="clearfix"></div>

			<input type="radio" name="type" id="input_type_link" value="link" checked="checked"/>
			<label for="input_type_link"><acronym title="'.$w->textLink.'">'.$w->labelLink.'</acronym></label><br/>
			<input type="radio" name="type" id="input_type_copy" value="copy"/>
			<label for="input_type_copy"><acronym title="'.$w->textCopy.'">'.$w->labelCopy.'</acronym></label><br/>
		<!--<fieldset>
			<legend>Test</legend>-->
		<!--</fieldset>-->
			<div class="buttonbar">
				'.$buttonCancel.'
				'.$buttonInstall.'
			</div>
		</form>
	</div>
</div>
';

?>