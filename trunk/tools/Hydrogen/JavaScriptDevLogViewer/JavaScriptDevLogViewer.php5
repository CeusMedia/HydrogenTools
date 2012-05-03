<?php
class JavaScriptDevLogViewer
{
	public $template	= "template.phpt";
	public function __construct( $fileName, $template = NULL )
	{
		if( $template )
			$this->template	= $template;
		if( isset( $_REQUEST['removeEntry'] ) )
		{
			$lines	= file( $fileName );
			unset( $lines[(int) $_REQUEST['removeEntry']] );
			file_put_contents( $fileName, implode( "", $lines ) );
			header( "Location: ./" );
		}
		else if( isset( $_REQUEST['clear'] ) )
		{
			unlink( $fileName );
			header( "Location: ./" );
		}
		echo require_once( $this->template );
	}
}
?>