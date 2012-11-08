var Installer = {
	appName: '<em>unknown application<em>',
	labels: {
		en: {
			msgDone: '<b>Done.</b><br/>Loading <cite>#AppName#</cite> now ...',
			msgError: '<b>Error:</b> Installation failed. See response below!',
			msgErrorConfig: '<b>Error:</b> File <cite>config/config.ini</cite> could not be created.',
			msgStarted: '<b>Installation in progress.</b><br/>Please wait ...'
		},
		de: {
			msgDone: '<b>Fertig!</b><br/><cite>#AppName#</cite> wird nun gestartet ...',
			msgError: '<b>Fehler:</b> Installation fehlgeschlagen.',
			msgErrorConfig: '<b>Fehler:</b> Datei <cite>config/config.ini</cite> konnte nicht erzeugt werden.',
			msgStarted: '<b>Die Installation läuft.</b><br/>Bitte warten ...'
		}
	},
	init: function(locale,appName){
		if(typeof this.labels[locale] === "undefined")
			locale = 'en';
		this.labels	= this.labels[locale];
		for(var key in this.labels)
			if(typeof this.labels[key] == "string")
				this.labels[key] = this.labels[key].replace(/#AppName#/, appName);
		this.appName = appName;
		return this;
	},
	start: function(){
		$("#greeting").fadeIn();
		$("#status").removeClass("failed").addClass("installing").html(Installer.labels.msgStarted);
		$.ajax({
			url: './',
			success: function(response){
				$("#status").html(" ").removeClass("installing");
				if(response.match(/#greeting/)){
					$("#status").addClass("failed").html(Installer.labels.msgErrorConfig);
				}
				else if(response.match(/.\/?selectInstanceId=/)){
					$("#status").addClass("done").html(Installer.labels.msgDone);
					document.location.reload();
				}
				else{
					$("#status").addClass("failed").html(Installer.labels.msgError);
					response = response.replace(/<script.*?>([\w\W\d\D\s\S\0\n\f\r\t\v\b\B]*?)<\/script>/gi, '');		//  @see http://stackoverflow.com/questions/7965111/prevent-jquery-ajax-to-execute-javascript-from-script-or-html-response
					preview.document.open("text/html","replace").write(response);
					$("#response").show();
				}
			}
		});
	}
};