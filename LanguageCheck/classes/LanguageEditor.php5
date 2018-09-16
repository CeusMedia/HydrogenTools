<?php
import( 'de.ceus-media.file.ini.Reader' );
import( 'de.ceus-media.file.ini.Editor' );
class LanguageEditor
{
	private $path;

	public function __construct( $path )
	{
		$this->path	= $path;
	}
	
	static function createPath( $path )
	{
		remark( $path );
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
		$content	= file_get_contents( $this->path.$source."/".$fileName );
		$content	= preg_replace( "@([\t| ]+)?=@", "\\1=__", $content );
		file_put_contents( $this->path.$target."/".$fileName, $content );
	}
	
	public function copyPair( $source, $target, $fileName, $section, $key )
	{
		$ir		= new File_INI_Reader( $this->path.$source."/".$fileName, TRUE );
		$data	= $ir->toArray( TRUE );
		$value	= "__".$data[$section][$key];
		$iw		= new File_INI_Editor( $this->path.$target."/".$fileName, TRUE );
		$iw->setProperty( $key, $value, $section );
	}
	
	public function copySection( $source, $target, $fileName, $section )
	{
		$ir		= new File_INI_Reader( $this->path.$source."/".$fileName, TRUE );
		$pairs	= $ir->getProperties( TRUE, $section );
		$iw		= new File_INI_Editor( $this->path.$target."/".$fileName, TRUE );
		$iw->addSection( $section );
		foreach( $pairs[$section] as $key => $value )
			$iw->addProperty( $key, "__".$value, "", TRUE, $section );
	}
	
	public function editValue( $target, $fileName, $section, $key, $value )
	{
		$ir	= new File_INI_Editor( $this->path.$target."/".$fileName, TRUE );
		$ir->setProperty( $key, stripslashes( $value ), $section );
	}

	public function encodeFile( $source, $target, $fileName )
	{
		$content	= file_get_contents( $this->path.$target."/".$fileName );
		$content	= utf8_encode( $content );
		file_put_contents( $this->path.$target."/".$fileName, $content );
	}
	
	public function removePair( $target, $fileName, $section, $key )
	{
		$ir	= new File_INI_Editor( $this->path.$target."/".$fileName, TRUE );
		$ir->deleteProperty( $key, $section );
	}
}
?>