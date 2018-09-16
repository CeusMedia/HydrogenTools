<?php
class MailSubjectCheck
{
	private $sourcePath;
	private $targetPath;
	
	public function __construct( $sourcePath, $targetPath )
	{
		$this->sourcePath	= $sourcePath;
		$this->targetPath	= $targetPath;
	}
	
	public function compareFiles()
	{
		return $this->getFileDifference( TRUE );
	}

	public function hasToTranslate( $fileName )
	{
		return $this->getToTranslate( $fileName, TRUE );
	}
	
	public function getMailSubjectDifference()
	{
		$encodeList		= array();
		$fileList		= array();
		$translateList	= array();
	
		$it = new RecursiveDirectoryIterator( $this->sourcePath );
		foreach( new RecursiveIteratorIterator( $it ) as $fileName )
		{
			if( preg_match( "@.svn@", $fileName ) )
				continue;
			$fileName	= substr( $fileName, strlen( $this->sourcePath ) );
			if( !file_exists( $this->targetPath.$fileName ) )
				$fileList[]	= $fileName;
			else
			{
				if( $this->isToEncode( $this->targetPath.$fileName ) )
					$encodeList[]	= $fileName;
				if( $this->isToTranslate( $fileName ) )
					$translateList[]	= $fileName;
			}
		}
		$results	= array(
			'encodeList'	=> $encodeList,
			'fileList'		=> $fileList,
			'translateList'	=> $translateList,
		);
		return $results;
	}

	public function getFileDifference( $indicateOnly = FALSE )
	{
		$list	= array();
		$it = new RecursiveDirectoryIterator( $this->sourcePath );
		foreach( new RecursiveIteratorIterator( $it ) as $fileName )
		{
			if( preg_match( "@.svn@", $fileName ) )
				continue;
			if( $this->isToEncode( $fileName ) )
				$list[]	= substr( $fileName, strlen( $this->sourcePath ) );
		}
		return $list;
	}

	public function getToEncode( $indicateOnly = FALSE )
	{
		$list	= array();
		$it = new RecursiveDirectoryIterator( $path );
		foreach( new RecursiveIteratorIterator( $it ) as $fileName )
		{
			if( preg_match( "@.svn@", $fileName ) )
				continue;
			if( $this->isToEncode( $fileName ) )
				$list[]	= substr( $fileName, strlen( $this->targetPath ) + 1 );
		}
		return $list;
	}

	public function getToTranslate( $fileName, $indicateOnly = FALSE )
	{
		$list	= array();
		$it = new RecursiveDirectoryIterator( $path );
		foreach( new RecursiveIteratorIterator( $it ) as $fileName )
		{
			if( preg_match( "@.svn@", $fileName ) )
				continue;
			if( $this->isToTranslate( $fileName ) )
				$list[]	= substr( $fileName, strlen( $this->targetPath ) + 1 );
		}
		return $list;
	}
	
	private function isToEncode( $fileName )
	{
		return !$this->isUTF8( file_get_contents( $fileName ) );
	}
	
	public function isToTranslate( $fileName )
	{
		$content	= file_get_contents( $this->targetPath.$fileName );
		return substr( $content, 0, 2 ) == "__";
	}

	private function isUTF8( $string )
	{
		if( is_array( $string ) )
		{
			$enc = implode( '', $string );
			return @!( ( ord( $enc[0] ) != 239 ) && ( ord( $enc[1] ) != 187 ) && ( ord( $enc[2] ) != 191 ) );
		}
		else
		{
			return ( utf8_encode( utf8_decode( $string ) ) == $string );
		}  
    }
}
?>