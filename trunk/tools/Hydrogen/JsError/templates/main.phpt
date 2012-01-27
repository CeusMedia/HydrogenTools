<?php
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <title>JavaScript DevLog Reader</title>
    <link rel="stylesheet" type="text/css" media="screen" href="//css.ceusmedia.com/blueprint/reset.css"/>
    <link rel="stylesheet" type="text/css" media="screen" href="//css.ceusmedia.com/blueprint/typography.css"/>
    <link rel="stylesheet" type="text/css" media="screen" href="../../contents/base.css"/>
    <style>
table {
	border: 1px solid black;
	border-collapse: collapse;
	}
table td {
	border: 1px dotted #ddd;
	}
table a.remove,
table a.remove:hover {
	display: block;
	width: 14px;
	height: 14px;
	background-image: url(action_stop.gif);
	background-repeat: no-repeat;
	color: transparent;
	}
table a.goto-html,
table a.goto-source {
	background-repeat: no-repeat;
	padding-left: 25px;
	}
table a.goto-html{
	background-image: url(http://icons.ceusmedia.de/famfamfam/silk/arrow_right.png);
	}
table a.goto-source{
	background-image: url(http://icons.ceusmedia.de/famfamfam/silk/eye.png);
	}
    </style>
  </head>
  <body>
    <h2>JavaScript Error Log Reader</h2>
    '.$table.'
  </body>
</html>';
?>