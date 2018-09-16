<?php
return '
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Language Check</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../../contents/base.css"/>
    <style>
a img {
	border: 0px;
	}
img.icon {
	float: left;
	display: block;
	width: 16px;
	height: 16px;
	padding-right: 4px;
	}
#msg {
	background: red;
	}
#footer {
	clear: both;
	border-top: 1px dotted gray;
	margin-top: 2em;
	}
    </style>
  </head>
  <body>
    <div id="header">
      <h2>Language Check</h2>
    </div>
    <div id="content">
      <fieldset>
        <legend>&nbsp;&nbsp;Languages&nbsp;&nbsp;</legend>
        <br/>
        <form method="GET">
          <label for="source">&nbsp;&nbsp;Source</label>
          <select name="source" style="width: 60px">'.UI_HTML_Elements::Options( $opt_source, $opt_source['_selected'] ).'</select>
          <label for="target">&nbsp;&nbsp;Target</label>
          <select name="target" style="width: 60px">'.UI_HTML_Elements::Options( $opt_target, $opt_target['_selected'] ).'</select>
          <input type="checkbox" name="characterCheck" '.(isset( $_REQUEST['characterCheck'] ) ? "checked" : "" ).'></input>
          <label for="characterCheck">check Characters</label>
          <button type="submit">compare</button>
        </form>
        <br/>
      </fieldset>
      <div id="msg">'.$msg.'</div>
      '.$statics.'
      '.$languages.'
      '.$mails.'
      '.$subjects.'
    </div>
    <div id="footer" style="clear: both">
      '.$clock->stop().' ms
    </div>
  </body>
  <script>
function editValue( link, oldValue )
{
	newValue	= prompt( "new value:", oldValue );
	if( newValue && newValue != oldValue )
		document.location.href	= link+"&value="+newValue;
}
  </script>
</html>';
?>
