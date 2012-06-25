<?php
class Todos_Tool{

	protected $found	= 0;
	protected $todos	= 0;
	protected $total	= 0;
	protected $lines	= 0;

	public function __construct( $path, $extensions = array() ){
		$request	= new Net_HTTP_Request_Receiver();
		$this->check( $path, $extensions );
		switch( $request->get( 'format' ) ){
			case 'json':
				$content	= json_encode( $this->getData() );
				break;
			case 'serial':
				$content	= serialize( $this->getData() );
				break;
			case 'indicator':
				$content	= $this->getIndicator( $request->get( 'width' ), $request->get( 'class' ) );
				break;
			default:
				$content	= $this->getHtml( $path );
		}
		Net_HTTP_Request_Response::sendContent( $content );
	}
	
	protected function check( $path, $extensions = array() ){
		$list	= array();
		$keys	= array( "//TODO", "@deprecated", "@todo" );
		$lister	= new File_RecursiveTodoLister( $extensions, $keys );
		$lister->scan( $path );
		$this->found	= $lister->getNumberFound();
		$this->todos	= $lister->getNumberTodos();
		$this->total	= $lister->getNumberScanned();
		$this->lines	= $lister->getNumberLines();
		$this->files	= $lister->getList( TRUE );
		$this->ratio	= $this->total ? 100 - round( $this->found / $this->total * 100, 2 ) : 0;
		$this->count	= count( $this->files );
		$data	= $this->getData();
	}

	protected function getData(){
		$data	= array(
			'total'	=> $this->total,
			'lines'	=> $this->lines,
			'found'	=> $this->found,
			'count'	=> $this->count,
			'todos'	=> $this->todos,
			'ratio'	=> $this->ratio,
			'files'	=> $this->files,
		);
		return $data;
	}

	protected function convertTabsToSpaces( $text ){
		$lines	= explode( "\n", $text );
		foreach( $lines as $nr => $line ){
			while( ( $pos = strpos( $lines[$nr], "\t" ) ) !== FALSE ){
				$left	= substr( $lines[$nr], 0, $pos );
				$right	= substr( $lines[$nr], $pos + 1 );
				$space	= str_repeat( ' ', 4 - strlen( $left ) % 4 );
				$lines[$nr]	= $left . $space . $right;
			}
		}
		return implode( "\n", $lines );
	}

	public function unitTestConvertTabsToSpaces(){
		$tests	= array(
			$this->convertTabsToSpaces( "^\t^\t^\t^\t^\t^\t^" ),

			$this->convertTabsToSpaces( "\t|" ),
			$this->convertTabsToSpaces( "1\t|" ),
			$this->convertTabsToSpaces( "12\t|" ),
			$this->convertTabsToSpaces( "123\t|" ),
			$this->convertTabsToSpaces( "1234|" ),

			$this->convertTabsToSpaces( "\t\t|" ),
			$this->convertTabsToSpaces( "1\t\t|" ),
			$this->convertTabsToSpaces( "12\t\t|" ),
			$this->convertTabsToSpaces( "123\t\t|" ),
			$this->convertTabsToSpaces( "1234\t|" ),

			$this->convertTabsToSpaces( "\t\t|" ),
			$this->convertTabsToSpaces( "\t1\t|" ),
			$this->convertTabsToSpaces( "\t12\t|" ),
			$this->convertTabsToSpaces( "\t123\t|" ),
			$this->convertTabsToSpaces( "\t1234|" ),

			$this->convertTabsToSpaces( "\t\t\t|" ),
			$this->convertTabsToSpaces( "\t1\t\t|" ),
			$this->convertTabsToSpaces( "\t12\t\t|" ),
			$this->convertTabsToSpaces( "\t123\t\t|" ),
			$this->convertTabsToSpaces( "\t1234\t|" ),

			$this->convertTabsToSpaces( "\t\t\t|\t\t|" ),
			$this->convertTabsToSpaces( "\t1\t\t|\t1\t|" ),
			$this->convertTabsToSpaces( "\t12\t\t|\t12\t|" ),
			$this->convertTabsToSpaces( "\t123\t\t|\t123\t|" ),
			$this->convertTabsToSpaces( "\t1234\t|\t1234|" ),

			$this->convertTabsToSpaces( "\t\t\t|\t\t\t|" ),
			$this->convertTabsToSpaces( "\t1\t\t|\t1\t\t|" ),
			$this->convertTabsToSpaces( "\t12\t\t|\t12\t\t|" ),
			$this->convertTabsToSpaces( "\t123\t\t|\t123\t\t|" ),
			$this->convertTabsToSpaces( "\t1234\t|\t1234\t|" ),


			$this->convertTabsToSpaces( "^\t^\t^\t^\t^\t^\t^" ),
		);		
		xmp( join( "\n", $tests ) );
		die;
	}
	
	protected function getHtml( $path ){
		$i		= 0;
		$list	= array();
		foreach( $this->files as $pathName => $fileData ){
			$i++;
			$pathName		= str_replace( "\\", "/", $pathName );
			$pathName		= str_replace( $path, "", $pathName );
			$fileName		= pathinfo( $pathName, PATHINFO_FILENAME );
			$fileExt		= pathinfo( $pathName, PATHINFO_EXTENSION );
			$filePath		= preg_replace( '@^../../@', '', dirname( $pathName ) );

			$hash			= md5( $pathName );
			$items			= array();
			foreach( $fileData['lines'] as $nr => $line )
				$items[]	= $this->convertTabsToSpaces( str_pad( $nr, 3, ' ', STR_PAD_LEFT ).":".$line );
			
			$attributes		= array(
				'id'		=> $hash,
				'class'		=> 'code'
			);
			$itemList		= UI_HTML_Tag::create( 'xmp', implode( "\n", $items ), $attributes );

			$attributes		= array(
				'onclick'	=> "$('#".$hash."').toggle();",
				'class'		=> 'file'
			);
			$spanPath		= $filePath ? UI_HTML_Tag::create( 'span', $filePath.'/', array( 'class' => 'path' ) ) : '';
			$spanFile		= UI_HTML_Tag::create( 'span', $fileName, array( 'class' => 'file' ) );
			$spanExt		= $fileExt ? UI_HTML_Tag::create( 'span', '.'.$fileExt, array( 'class' => 'ext' ) ) : '';
			$spanNumber		= UI_HTML_Tag::create( 'span', count( $items ), array( 'class' => 'number' ) );
			$list[]			= UI_HTML_Elements::ListItem( $spanPath.$spanFile.$spanExt.$spanNumber.$itemList, 0, $attributes );
		}
		$list	= UI_HTML_Elements::orderedList( $list, 0, array( 'class' => 'files' ) );
		$data	= array(
			'filesTotal'	=> $this->total,
			'filesFound'	=> $this->count,
			'filesRatio'	=> $this->total ? round( $this->count / $this->total * 100, 2 ) : 0,
			'linesTotal'	=> $this->lines,
			'linesFound'	=> $this->todos,
			'linesRatio'	=> $this->total ? round( $this->todos / $this->lines * 100, 2 ) : 0,
			'found'			=> $this->found,
			'indicator'		=> $this->getIndicator(),
			'list'			=> $list,
		);
		return UI_Template::render( 'template.html', $data );
	}

	protected function getIndicator( $width = 150, $class = 'indicator' ){
		$indicator	= new UI_HTML_Indicator();
		$indicator->setIndicatorClass( $class );
		$indicator->setOption( 'usePercentage', FALSE );
		return $indicator->build( $this->total - $this->found, $this->total, $width );
	}
}
?>