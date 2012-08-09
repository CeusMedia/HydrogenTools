JsError = {
	pathTool: 'tools/JsError/',
	handleError: function(message, file, line){
		$.ajax({																	//  open AJAX POST request
			url: JsError.pathTool,													//  to error catching script
			data: {
				action: "catch",
				message: message,													//  error message
				file: file,															//  script URL
				line: line,															//  line in code
				document: document.documentElement.innerHTML
			},
			type: "post",
			dataType: "json",
			success: JsError.handleToolResponse
		});
		if( !( "console" in window && "firebug" in console ) )						//  if no Firebug available
			return true;															//  show no Error in Browser
		return false;
	},
	handleToolResponse: function(response){
		switch(response.status){
			case "error":
				alert(response.error);
				break;
			case "data":
				if( "console" in window )											//  if console available
					console.log(response.data);
				break;
		}
	}
};
