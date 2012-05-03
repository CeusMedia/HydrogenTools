<?php

$count	= 0;
$config	= '-';
if( $module->config ){
	$config	= array();
	foreach( $module->config as $item ){
		$count++;
		$dt		= UI_HTML_Tag::create( 'dt', $item->key );
		$dd		= UI_HTML_Tag::create( 'dd', $item->value, array( 'rel' => $item->type ) );
		$config[]	= $dt.$dd;
	}
	$config	= UI_HTML_Tag::create( 'dl', join( $config ) );
}

return $config.'<div class="clearfix"></div>
<script>

$(document).ready(function(){
	$("dd").each(function(){
		$(this).click(function(){
			var dd = $(this);
			if(!dd.data("state")){
				dd.data("key",dd.prev().html());
				dd.data("type",dd.prop("rel"));
				dd.data("value",dd.html());
				dd.data("state",1);
			}
			if(dd.data("state") == 1){
				$("dd>input").each(function(){
					var dd = $(this).parent();
					dd.html(dd.data("value"));
				});
				if(dd.data("type") == "string")
					var input = $("<input/>").val(dd.data("value")).prop("type","text");
				else if(type == "boolean"){
					var input = $("<input/>").prop("type","checkbox");
					var checked = value == "yes" || value == "1";
					input.prop("checked",checked ? "checked" : "");
					input.val(1).prop("name",key).data("value",value);
				}
				$(this).html(input);
				dd.data("state",2);
			}
			else if(!dd.data("state") == 2){
				dd.data("state",1);
			}
		});
	});
});
</script>
';
?>