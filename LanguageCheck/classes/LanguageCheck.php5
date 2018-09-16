<?php
class LanguageCheck
{

	private $sourcePath;
	private $targetPath;
	
	public function __construct( $sourcePath, $targetPath )
	{
		$this->sourcePath	= $sourcePath;
		$this->targetPath	= $targetPath;
	}
	
	public function compareSectionsOfFile( $fileName )
	{
		return $this->getSectionDifference( $fileName, TRUE );
	}

	public function compareKeysOfSection( $fileName, $section )
	{
		return $this->getKeyDifference( $fileName, $section, TRUE );
	}
	
	public function hasToTranslate( $fileName )
	{
		return $this->getToTranslate( $fileName, TRUE );
	}
	
	public function getKeyDifference( $fileName, $section, $indicateOnly = FALSE )
	{
		$list	= array();
		$source	= $this->readLanguage( $this->sourcePath.$fileName ); 
		$target	= $this->readLanguage( $this->targetPath.$fileName ); 
		
		print_m( $source );
		print_m( $target );
		die;
		
		$keys1	= array_keys( $source[$section] );
		$keys2	= array_keys( $target[$section] );		
		sort( $keys1 );
		sort( $keys2 );
		$source	= array_diff( $keys2, $keys1 );
		$target	= array_diff( $keys1, $keys2 );
		if( $indicateOnly )
			return $source || $target;
		$array	= array( 'file' => $fileName, 'section' => $section );
		foreach( array( 'target', 'source' ) as $side )
		{
			$sideArray	= array_merge( $array, array( 'type' => $side ) );
			if( $$side )
				$list[]	= array_merge( $sideArray, array( 'keys' => $$side ) );
		}
		return $list;
	}

	function isUTF8( $string )
	{
		if (is_array($string))
		{
			$enc = implode('', $string);
			return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
		}
		else
		{
			return (utf8_encode(utf8_decode($string)) == $string);
		}  
    }

	public function getLanguageDifference( $characterCheck = FALSE )
	{
		$encodeList		= array();
		$fileList		= array();
		$keyList		= array();
		$sectionList	= array();
		$translateList	= array();
	
		$dir = new File_RegexFilter( $this->sourcePath, '@\.ini$@' );
		$fileList	= array();
		foreach( $dir as $entry )
		{
			if( $entry->isDir() )
				continue;
			$fileName	= str_replace( $this->sourcePath, "", str_replace( "\\", "/", $entry->getPathname() ) );
			if( !file_exists( $this->targetPath.$fileName ) )
				$fileList[]	= $fileName;
			else
			{
				$content	= file_get_contents( $this->targetPath.$fileName );
				if( !$this->isUTF8( $content ) )
				{
					if( !$characterCheck )
						$encodeList[]	= $fileName;
					else
					{
						$lines	= explode( "\n", $content );
							for( $i=0; $i<count( $lines ); $i++ )
							if( !$this->isUTF8( $lines[$i] ) )
								for( $j=0; $j<strlen( $lines[$i] ); $j++ )
									if( !$this->isUTF8( substr( $lines[$i], $j, 1 ) ) )
										$encodeList[]	= $fileName." <small>(line ".($i+1).", sign: ".($j+1)." => [".substr( $lines[$i], $j-1, 3 )."] )</small>";
					}
				}
				$list	= $this->getSectionDifference( $fileName );
				if( $list )
					$sectionList	= array_merge( $sectionList, $list );
				else
				{
					$file1 		= $this->readLanguage( $this->sourcePath.$fileName );
					foreach( array_keys( $file1 ) as $section )
					{
						$list	= $this->getKeyDifference( $fileName, $section );
						if( $list )
							$keyList	= array_merge( $keyList, $list );
					}
				}
				$list	= $this->getToTranslate( $fileName );
				if( $list )
					$translateList	= array_merge( $translateList, $list );
			}
		}
		$results	= array(
			'encodeList'	=> $encodeList,
			'fileList'		=> $fileList,
			'keyList'		=> $keyList,
			'sectionList'	=> $sectionList,
			'translateList'	=> $translateList,
		);
		return $results;
	}
	
	public function getSectionDifference( $fileName, $indicateOnly = FALSE )
	{
		$list	= array();
		$source	= $this->readLanguage( $this->sourcePath.$fileName );
		$target	= $this->readLanguage( $this->targetPath.$fileName ); 
		if( is_string( array_shift( $copy = $target ) ) )
		{
			$list[]	= array( 'file' => $fileName." is corrupt.", 'type' => "target", 'sections' => array() );
			return $list;
		}
		$sections1	= array_keys( $source );
		$sections2	= array_keys( $target );
		sort( $sections1 );
		sort( $sections2 );
		$source	= array_diff( $sections2, $sections1 );
		$target	= array_diff( $sections1, $sections2 );
		if( $indicateOnly )
			return $source || $target;
		$array	= array( 'file' => $fileName );
		foreach( array( 'target', 'source' ) as $side )
		{
			$sideArray	= array_merge( $array, array( 'type' => $side ) );
			if( $$side )
				$list[]	= array_merge( $sideArray, array( 'sections' => $$side ) );
		}
		return $list;
	}

	public function getToTranslate( $fileName, $indicateOnly = FALSE )
	{
		$list	= array();
		$file	= $this->readLanguage( $this->targetPath.$fileName );
		if( $indicateOnly )
			return preg_match( '@" *__@', serialize( $file ) );
		if( !$this->getToTranslate( $fileName, TRUE ) )
			return array();
		foreach( $file as $sectionKey => $sectionData )
		{
			foreach( $sectionData as $key => $value )
			{
				if( preg_match( "@ *__@", $value ) )
					$list[]	= array(
						'file'		=> $fileName,
						'section'	=> $sectionKey,
						'key'		=> $key,
						'value'		=> $value,
						);
			}
		}
		return $list;
	}

	private function readLanguage( $fileName )
	{
		if( !isset( $this->cache[$fileName] ) )
		{
			$ir	= new File_INI_Reader( $fileName, TRUE );
			$this->cache[$fileName] = $ir->toArray( TRUE );
			
		}
		return $this->cache[$fileName];
	}
}
?>
