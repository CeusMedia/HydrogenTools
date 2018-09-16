<?php
class StaticCheckActions
{
	function act( $path )
	{
		$se	= new StaticEditor( $path );
		$g	= $_GET;
		switch( $g['action'] )
		{
			case "encodeStaticFile":	if( isset( $g['source'] ) && isset( $g['target'] ) && isset( $g['fileName'] ) )
										$se->encodeFile( $g['source'], $g['target'], $g['fileName'] );
										break;
			case "copyStaticFile":		if( isset( $g['source'] ) && isset( $g['target'] ) && isset( $g['fileName'] ) )
										$se->copyFile( $g['source'], $g['target'], $g['fileName'] );
										break;
		}
	}
}
?>