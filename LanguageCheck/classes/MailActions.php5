<?php
class MailCheckActions
{
	function act( $path )
	{
		$me	= new MailEditor( $path );
		$g	= $_GET;
		switch( $g['action'] )
		{
			case "encodeMailFile":	if( isset( $g['source'] ) && isset( $g['target'] ) && isset( $g['fileName'] ) )
									$me->encodeFile( $g['source'], $g['target'], $g['fileName'] );
									break;
			case "copyMailFile":	if( isset( $g['source'] ) && isset( $g['target'] ) && isset( $g['fileName'] ) )
									$me->copyFile( $g['source'], $g['target'], $g['fileName'] );
									break;
		}
	}
}
?>