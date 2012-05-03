<?php
return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>JavaScript DevLog Viewer</title>
    <link rel="stylesheet" type="text/css" media="screen" href="http://css.ceusmedia.com/blueprint/reset.css"/>
    <link rel="stylesheet" type="text/css" media="screen" href="http://css.ceusmedia.com/blueprint/typography.css"/>
    <link rel="stylesheet" type="text/css" media="screen" href="tablesorter.css"/>
    <script src="http://js.ceusmedia.com/jquery/1.2.3.pack.js"></script>
    <script src="tablesorter.pack.js"></script>
    <script src="read.js"></script>
    <script>
var urlDevLog = "'.$fileName.'"
    </script>
    <style>
/*table {
	border: 1px solid black;
	border-collapse: collapse;
	}
table td {
	border: 1px dotted #ddd;
	}*/
table a,
table a:hover {
	display: block;
	width: 14px;
	height: 14px;
	background-image: url(images/action_stop.gif);
	background-repeat: no-repeat;
	color: transparent;
	}
    </style>
  </head>
  <body style="margin: 1em">
    <h2>JavaScript DevLog Viewer</h2>
    <table class="tablesorter">
      <colgroup>
        <col width="2%"/>
        <col width="20%"/>
        <col width="25%"/>
        <col width="38%"/>
        <col width="15%"/>
      </colgroup>
      <thead>
        <tr>
          <th></th>
          <th>Time</th>
          <th>URL</th>
          <th>Message</th>
          <th>User Agent</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </body>
</html>';
?>