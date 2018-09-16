<?php
class LanguageCheckActions
{
	function act( $path )
	{
		$le	= new LanguageEditor( $path );
		$g	= $_GET;
		switch( $g['action'] )
		{
			case "copyFile":	if( isset( $g['source'] ) && isset( $g['target'] ) && isset( $g['fileName'] ) )
								$le->copyFile( $g['source'], $g['target'], $g['fileName'] );
								break;
			case "copyPair":	if( isset( $g['source'] ) && isset( $g['target'] ) && isset( $g['fileName'] ) && isset( $g['section'] ) && isset( $g['key'] ) )
								$le->copyPair( $g['source'], $g['target'], $g['fileName'], $g['section'], $g['key'] );
								break;
			case "copySection":	if( isset( $g['source'] ) && isset( $g['target'] ) && isset( $g['fileName'] ) && isset( $g['section'] ) )
								$le->copySection( $g['source'], $g['target'], $g['fileName'], $g['section'] );
								break;
			case "editValue": 	if( isset( $g['target'] ) && isset( $g['fileName'] ) && isset( $g['section'] ) && isset( $g['key'] ) && isset( $g['value'] ) )
								$le->editValue( $g['target'], $g['fileName'], $g['section'], $g['key'], $g['value'] );
								break;
			case "encodeFile": 	if( isset( $g['target'] ) && isset( $g['fileName'] ) && isset( $g['section'] ) && isset( $g['key'] ) && isset( $g['value'] ) )
								$le->encodeFile( $g['target'], $g['fileName'], $g['fileName'] );
								break;
			case "removePair":	if( isset( $g['target'] ) && isset( $g['fileName'] ) && isset( $g['section'] ) && isset( $g['key'] ) )
								$le->removePair( $g['target'], $g['fileName'], $g['section'], $g['key'] );
								break;
		}
	}
}
?>