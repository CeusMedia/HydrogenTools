<?php
class MailSubjectCheckActions
{
	function act( $path )
	{
		$mse	= new MailSubjectEditor( $path );
		$g	= $_GET;
		switch( $g['action'] )
		{
			case "encodeMailSubjectFile":	if( isset( $g['source'] ) && isset( $g['target'] ) && isset( $g['fileName'] ) )
											$mse->encodeFile( $g['source'], $g['target'], $g['fileName'] );
											break;
			case "copyMailSubjectFile":		if( isset( $g['source'] ) && isset( $g['target'] ) && isset( $g['fileName'] ) )
											$mse->copyFile( $g['source'], $g['target'], $g['fileName'] );
											break;
		}
	}
}
?>