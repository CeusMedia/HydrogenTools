<?php
return '
<html>
	<head>
		<style>
ol {
	font-size: 13px; font-family: Sans Serif;
	}
ol li {
	line-height: 1em;
	min-height: 1em;
	border: 1px solid white;
	background-color: #FFF
	}
ol li pre {
	margin: 0px;
	padding: 1px;
	}
ol li.selected {
	border: 1px solid #777;
	background-color: #DDD;
	border-radius: 5px;
	}
.code-function-label {
	color: green;
	}
.code-function {
	font-weight: bold;
	}
		</style>
	</head>
	<body>
		<ol>
			'.join( $list ).'
		</ol>
	</body>
</html>';
?>