<?php
$w	= (object) $words['add'];
$panelAdd	= '
	<form action="./?action=add" method="post">
		<ul class="input">
			<li class="column-right-50">
				<label for="input_add_description">'.$w->labelDescription.'</label><br/>
				<textarea name="add_description" id="input_add_description" rows="10">'.$request->get( 'add_description' ).'</textarea>
			</li>
			<li class="column-left-50">
				<label for="input_add_title">'.$w->labelTitle.'</label><br/>
				<input type="text" name="add_title" id="input_add_title" value="'.$request->get( 'add_title' ).'"/>
			</li>
			<li class="column-left-25">
				<label for="input_add_id">'.$w->labelModuleId.'</label><br/>
				<input type="text" name="add_id" id="input_add_id" readonly="readonly" style="background-color: #EEE; border-color: #BBB;"/>
			</li>
			<li class="column-left-25">
				<label for="input_add_path">'.$w->labelModulePath.'</label><br/>
				<input type="text" name="add_path" id="input_add_path" readonly="readonly" style="background-color: #EEE; border-color: #BBB;"/>
			</li>
			<li class="column-left-50">
				<label for="input_add_route">'.$w->labelRoute.'</label><br/>
				<input type="text" name="add_route" id="input_add_route" value="'.$request->get( 'add_route' ).'"/>
			</li>
			<li class="column-left-20">
				<label for="input_add_version">'.$w->labelVersion.'</label><br/>
				<input type="text" name="add_version" id="input_add_version" value="'.$request->get( 'add_version' ).'"/>
			</li>
			<li class="column-left-50">
				<br/>
				<label for="input_add_scafold">
					<input type="checkbox" name="add_scafold" id="input_add_scafold"/>
					'.$w->labelScafold.'
				</label><br/>
				<strike><label for="input_add_import">
					<input type="checkbox" name="add_import" id="input_add_import" disabled="disabled"/>
					'.$w->labelImport.'
				</label></strike><br/>
			</li>
		</ul>
		<div class="buttonbar">
			'.UI_HTML_Elements::Button( 'add', 'erstellen', 'button add' ).'
		</div>
	</form>
	<script>
$(document).ready(function(){
	$("#input_add_title").bind("keydown keyup",function(){
		var id = $.trim($(this).val());
		if(!id.match(/^[a-z].+[a-z0-9]$/i))
			id	= "";
		id = id.replace(/[^a-z0-9]+/ig,"_");
		id = id.replace(/_+/,"_");
		$("#input_add_id").val(id);
		$("#input_add_path").val(id.replace(/_/g,"/"));
	}).trigger("keyup");
});
	</script>
';
return '
<div class="ui-widget ui-widget-content ui-corner-all">
	<div style="padding: 1em 2em">
		<h3>Neues Modul</h3>
		<a href="./">&laquo;&nbsp;zurück zur Liste</a>
		'.$panelAdd.'
	</div>
</div>';
?>