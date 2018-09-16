<?php
class MailSubjectEditor
{
	private $path;

	public function __construct( $path )
	{
		$this->path	= $path;
	}
	
	static function createPath( $path )
	{
		if( is_dir( $path ) )
			return TRUE;
		if( $path == "./" )
			return TRUE;
		if( self::createPath( dirname( $path ) ) )
			return mkDir( $path );
	}

	public function copyFile( $source, $target, $fileName )
	{
		$this->createPath( dirname( $this->path.$target."/".$fileName ) );
		$content	= "__".file_get_contents( $this->path.$source."/".$fileName );
		file_put_contents( $this->path.$target."/".$fileName, $content );
	}

	public function encodeFile( $source, $target, $fileName )
	{
		$content	= file_get_contents( $this->path.$target."/".$fileName );
		$content	= utf8_encode( $content );
		file_put_contents( $this->path.$target."/".$fileName, $content );
	}
}
?>