<?php
/**
 *	Abstract Basic Component for Actions and Views.
 *
 *	Copyright (c) 2007-2009 Christian Würker (ceus-media.de)
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *	@category		cmClasses
 *	@package		framework.xenon.core
 *	@uses			Framework_Xenon_Core_Registry
 *	@uses			Framework_Xenon_Core_Template
 *	@uses			View_Component_Elements
 *	@uses			File_Reader
 *	@uses			File_Writer
 *	@uses			Alg_Time_Converter
 *	@uses			Alg_InputFilter
 *	@uses			UI_HTML_WikiParser
 *	@author			Christian Würker <christian.wuerker@ceus-media.de>
 *	@copyright		2007-2009 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmclasses/
 *	@since			01.12.2005
 *	@version		0.6
 */
import( 'de.ceus-media.framework.xenon.core.Registry' );
import( 'de.ceus-media.framework.xenon.view.component.Template' );
import( 'de.ceus-media.file.Reader' );
import( 'de.ceus-media.ui.html.Elements' );
import( 'de.ceus-media.alg.TimeConverter' );
/**
 *	Generic View with Language Support.
 *	@category		cmClasses
 *	@package		framework.xenon.core
 *	@abstract
 *	@uses			Framework_Xenon_Core_Registry
 *	@uses			Framework_Xenon_Core_Template
 *	@uses			View_Component_Elements
 *	@uses			File_Reader
 *	@uses			File_Writer
 *	@uses			Alg_Time_Converter
 *	@uses			Alg_InputFilter
 *	@uses			UI_HTML_WikiParser
 *	@author			Christian Würker <christian.wuerker@ceus-media.de>
 *	@copyright		2007-2009 Christian Würker
 *	@license		http://www.gnu.org/licenses/gpl-3.0.txt GPL 3
 *	@link			http://code.google.com/p/cmclasses/
 *	@since			01.12.2005
 *	@version		0.6
 */
abstract class Framework_Xenon_Core_Module_Component
{
	/**	@var		Framework_Xenon_Core_Registry		$registry		Registry of Objects */
	protected $registry	= null;
	/**	@var		Net_HTTP_Request_Receiver			Request Receiver Object */
	protected $request		= NULL;
	/**	@var		UI_HTML_Elements	$html			HTML Elements */
	public $html			= NULL;
	/**	@var		Language			$language		Language Support */
	protected $language		= NULL;
	/**	@var		Messenger			$messenger		Messenger Object */
	protected $messenger	= NULL;
	/**	@var		Alg_Time_Converter	$tc				Time Converter Object */
	protected $tc			= NULL;
	/**	@var		array				$words			Array of defined Words */
	protected $words		= array();

	protected $module = NULL;

	/**
	 *	Constructor, references Output Objects.
	 *	@access		public
	 *	@return		void
	 */
	public function __construct()
	{
		$this->registry		= Framework_Xenon_Core_Registry::getInstance();
		$this->config		= $this->registry->get( 'config' );
		$this->request		= $this->registry->get( 'request' );
		$this->html			= new UI_HTML_Elements;
		$this->tc			= new Alg_Time_Converter;
		$this->messenger	= $this->registry->get( 'messenger' );
		$this->language		= $this->registry->get( 'language' );
		$this->words		=& $this->language->getWords();

		$pattern			= '/^Module_([a-z]+)_/i';
		$this->module		= preg_replace( $pattern, '\\1', get_class( $this ) );
	}

	/**
	 *	Shortens a string by a maximum length with a mask.
	 *	@access		public
	 *	@static
	 *	@param		string		$string		String to be shortened
	 *	@param		int			$length		Maximum length to cut at
	 *	@param		string		$mask		Mask to append to shortened string
	 *	@return		string
	 */
	public static function shortenString( $string, $length = 20, $mask = "..." )
	{
		$length	= abs( $length );
		if( $length )
		{
			$inner_length	= $length - strlen( $mask );
			$sting_length	= strlen( $string );
			if( $sting_length > $inner_length )
				$string	= substr( $string, 0, $inner_length ).$mask;
		}
		return $string;
	}
	
	/**
	 *	Returns a float formated as Currency.
	 *	@access		public
	 *	@static
	 *	@param		mixed		$price			Price to be formated
	 *	@param		string		$separator		Separator
	 *	@return		string
	 */
	public static function formatPrice( $number, $decimals = 2, $separatorDecimals = NULL, $separatorThousands = NULL )
	{
		$sepDecimals	= $separatorDecimals ? $separatorDecimals : ",";
		$sepThousands	= $separatorThousands ? $separatorThousands : ".";
		return number_format( $number, $decimals, $sepDecimals, $sepThousands );
	}

	public static function getPriceFromString( $string )
	{
		$string = trim( $string );
		if( !preg_match( "~^(\+|-)?([0-9]+|(?:(?:[0-9]{1,3}([.,' ]))+[0-9]{3})+)(([.,])[0-9]{1,2})?$~", $string, $r ) )
			throw new InvalidArgumentException( "This String is not a formated Price." );
			
		$pre	= $r['1'].$r['2'];
		$post	= "";
		if( !empty( $r['3'] ) )
		{
			$pre = $r['1'].preg_replace( "~[".$r['3']."]~", "", $r['2'] );
		}
		if( !empty( $r['5'] ) )
		{
			$post = ".".preg_replace( "~[".$r['5']."]~", "", $r['4'] );
		}
		return (float) number_format( $pre.$post, 2, ".", "" );		
	}

	//  --  FILE URI GETTERS  --  //
	/**
	 *	Returns Cache File URI.
	 *	@access		public
	 *	@param		string		$fileKey		File Name of Cache File
	 *	@return		string
	 */
	public function getCacheUri( $fileKey )
	{
		$config		= $this->registry->get( "config" );
		$basePath	= "module/".$this->module."/cache/";			// $config['paths.cache'];
		$fileName	= $basePath.$fileKey;
		return $fileName;
	}

	/**
	 *	Returns Content File URI.
	 *	@access		public
	 *	@param		string		$fileKey		File Name of Content File
	 *	@return		string
	 */
	public function getContentUri( $fileKey )
	{
		$fileUri	= "module/".$this->module."/html/".str_replace( ".", "/", $fileKey ).".html";
		remark( "getContentUri: ".$fileUri );
		return $fileUri;
	}

	/**
	 *	Retursn HTTP Query String build from basic Parameter Pairs and additional Pairs, where a Pair will Value NULL will remove the Pair.
	 *	@access		public
	 *	@param		array		$basePairs		Array of basic Parameter Pairs
	 *	@param		array		$otherPairs		Arrayo of Pairs to add or remove (on Value NULL)
	 *	@return		string
	 */
	public function getQueryString( $basePairs, $otherPairs = array() )
	{
		foreach( $otherPairs as $key => $value )
		{
			if( $value === NULL )
			{
				unset( $basePairs[$key] );
				continue;
			}
			$basePairs[$key]	= $value;
		}
		$query	= http_build_query( $basePairs, '', "&" );
		return $query;
	}

	/**
	 *	Returns Template File URI.
	 *	@access		public
	 *	@param		string		$fileKey		File Name of Template File
	 *	@return		string
	 */
	public function getTemplateUri( $fileKey )
	{
		$basePath	= "module/".$this->module."/templates/";
		$baseName	= str_replace( ".", "/", $fileKey ).".html";
		return $basePath.$baseName;
	}

	//  --  EXCEPTION HANDLING  --  //
	/**
	 *	Handles different Exceptions by calling special Exception Handlers.
	 *	@access		public
	 *	@param	 	Exception	$exception			Exception to handle
	 *	@param	 	string		$languageKey		Language File Key with Error Messages and Form Fields
	 *	@param	 	string		$languageSection	Section Name within Language File.
	 *	@return		void
	 *	@todo		clean up after 0.6.6
	 */
	public function handleException( $exception, $languageKey = NULL, $languageSection = "msg" )
	{
		import( 'de.ceus-media.framework.xenon.exception.Logic' );
		switch( get_class( $exception ) )
		{
			case 'Framework_Xenon_Exception_Validation':										//  deprecated
				$this->handleValidationException( $exception, $languageKey, $languageSection );
				break;
			case 'Exception_Validation':
				$this->handleValidationException( $exception, $languageKey, $languageSection );
				break;
			case 'Framework_Xenon_Exception_Logic':
				$this->handleLogicExceptionOld( $exception, $languageKey );
				break;
			case 'Framework_Xenon_Exception_SQL':
				import( 'de.ceus-media.ui.html.exception.TraceViewer' );
				new UI_HTML_Exception_TraceViewer( $exception );
				$this->handleSqlException( $exception );
				break;
			case 'Framework_Xenon_Exception_Template':										//  deprecated
				$this->handleTemplateException( $exception );
				break;
			case 'Exception_Template':
				$this->handleTemplateException( $exception );
				break;
			case 'LogicException':
				$this->handleLogicException( $exception, $languageKey, 'exceptions' );
				break;
			case 'Exception_Service_Response':
				$type	= $exception->getType();
				$e		= new $type( $exception->getMessage() );
				$this->handleException( $e, $languageKey, $languageSection );
				break;
			case 'Exception':
				throw $exception;			
				break;
			default:
				import( 'de.ceus-media.ui.html.exception.TraceViewer' );
				new UI_HTML_Exception_TraceViewer( $exception );
				$this->messenger->noteFailure( $exception->getMessage() );
		}
	}

	/**
	 *	Interprets Logic Exception and builds Error Message.
	 *	@access		protected
	 *	@param		LogicException		$exception			Exception to handle.
	 *	@param		string				$languageKey		Language File Key
	 *	@param		string				$languageSection	Section Name in Language Space
	 *	@return		void
	 */
	protected function handleLogicExceptionOld( Exception $exception, $languageKey, $languageSection = "msg" )
	{
		$words	= $this->words[$languageKey][$languageSection];
		if( isset( $words[$exception->key] ) )
			$msg	= $words[$exception->key];
		else
			$msg	= $exception->key;
		$this->messenger->noteError( $msg, $exception->subject );
	}

	/**
	 *	Interprets Logic Exception and builds Error Message.
	 *	@access		protected
	 *	@param		LogicException		$exception			Exception to handle.
	 *	@param		string				$languageKey		Language File Key
	 *	@param		string				$languageSection	Section Name in Language Space
	 *	@return		void
	 *	@todo		remove older Section, see below
	 */
	protected function handleLogicException( LogicException $exception, $languageKey, $languageSection = "exceptions" )
	{
		$words	= $this->words[$languageKey];										//  to be removed
		if( isset( $words[$languageSection] ) )										//  on 0.6.6
			$words	= $words[$languageSection];										//  because all logic messages
		else																		//  should be in
			$words	= $words['msg'];												//  Language Section 'exceptions'  

		if( isset( $words[$exception->getMessage()] ) )
			$msg	= $words[$exception->getMessage()];
		else
			$msg	= $exception->getMessage();
		$this->messenger->noteError( $msg, $exception->getCode() );
	}

	/**
	 *	Interprets Errors of Validation Exception and sets built Error Messages.
	 *	@access		protected
	 *	@param		Framework_Xenon_Exception_Validation	$exception			Exception to handle.
	 *	@param		string									$languageKey		Language File Key
	 *	@param		string									$languageSection	Section Name in Language Space
	 *	@return		void
	 */
	protected function handleValidationException( Exception $exception, $languageKey, $languageSection )
	{
		if( is_array( $languageSection ) )
		{
			$form	= $exception->getForm();
			if( $form && in_array( $form, array_keys( $languageSection ) ) )
				$languageSection	= $languageSection[$form];
			else
				$languageSection	= array_shift( $languageSection );
		}
		$labels		= $this->words[$languageKey][$languageSection];
		$messages	= $this->words['validator']['messages'];
		foreach( $exception->getErrors() as $error )
		{
			if( $error instanceOf Framework_Xenon_Logic_ValidationError )
			{
				$msg	= $messages[$error->key];
				if( $error->key == "isClass" )
					if( isset( $messages["is".ucfirst( $error->edge )] ) )
						$msg	= $messages["is".ucfirst( $error->edge )];
				$msg	= preg_replace( "@%label%@", $labels[$error->field], $msg );
				$msg	= preg_replace( "@%edge%@", $error->edge, $msg );
				$msg	= preg_replace( "@%field%@", $error->field, $msg );
				$msg	= preg_replace( "@%prefix%@", $error->prefix, $msg );
				$this->messenger->noteError( $msg );
			}
		}
	}
	
	/**
	 *	Interprets SQL Exception and sets built Error Messages.
	 *	@access		protected
	 *	@param		Framework_Xenon_Exception_SQL		$exception				Exception to handle.
	 *	@return		void
	 */
	protected function handleSqlException( Framework_Xenon_Exception_SQL $exception )
	{
		$message	= $exception->getMessage();
		$message	.= "<br/>".$exception->sqlMessage;
		$this->messenger->noteFailure( $message );
	}
	
	/**
	 *	Interprets Template Exception and sets built Error Messages.
	 *	@access		protected
	 *	@param		Framework_Xenon_Exception_Template	$exception				Exception to handle.
	 *	@return		void
	 */
	protected function handleTemplateException( Exception $exception )
	{
		$list	= array();
		foreach( $exception->getNotUsedLabels() as $label )
			$list[]	= preg_replace( "@<%(.*)%>@", "\\1", $label );
		$labels	= implode( ",", $list );
		$labels	= htmlentities( $labels );
		$this->messenger->noteFailure( $exception->getMessage()."<br/><small>".$labels."</small>" );
	}

	//  --  FILE MANAGEMENT  --  //
	/**
	 *	Indicates whether a Cache File is existing.
	 *	@access		public
	 *	@param		string		$fileKey		File Name of Cache File
	 *	@return		bool
	 */
	public function hasCache( $fileKey )
	{
		$fileName	= $this->getCacheUri( $fileKey );
		return file_exists( $fileName );
	}
	
	/**
	 *	Indicates whether a Content File is existing.
	 *	@access		public
	 *	@param		string		$fileKey		File Name of Content File
	 *	@return		bool
	 */
	public function hasContent( $fileKey )
	{
		$fileName	= $this->getContentUri( $fileKey );
		return file_exists( $fileName );
	}

	/**
	 *	Loads File from Cache.
	 *	@access		public
	 *	@param		string		$fileName 			File Name of Cache File.
	 *	@return		string
	 */
#	public function loadCache( $fileName )
#	{
#		$config	= $this->registry->get( 'config' );
#		$url	= $config['paths.cache'].$fileName;
#		return File_Reader::load( $url );
#	}
	
	/**
	 *	Loads Content File in HTML or DokuWiki-Format returns Content.
	 *	@access		public
	 *	@param		string		$fileKey			File Name (with Extension) of Content File (HTML|Wiki|Text), i.E. home.html leads to {CONTENT}/{LANGUAGE}/home.html
	 *	@param		array		$data				Data for Insertion in Template
	 *	@param		bool		$verbose			Flag: remark File Name
	 *	@return		string
	 */
	public function loadContent( $fileKey, $data = array(), $verbose = false )
	{
		$fileName	= $this->getContentUri( $fileKey, $verbose );
		if( !file_exists( $fileName ) )							//  check file
			throw new Exception_IO( 'Content File "'.$fileKey.'" is not existing.', $fileName );

		//  --  FILE INTERPRETATION  --  //
		$file		= new File_Reader( $fileName );
		$content	= $file->readString();
#		foreach( $data as $key => $value )
#			$content	= str_replace( "[#".$key."#]", $value, $content );
		return $content;
	}

	/**
	 *	Loads a Language File into Language Space, needs Session.
	 *	@access		public
	 *	@param		string		$module				Module Name
	 *	@param		string		$fileName			File Name of Language File
	 *	@param		string		$section			Section Name in Language Space
	 *	@return		bool
	 */
	public function loadLanguage( $fileName, $section = FALSE, $verbose = FALSE )
	{
		return $this->language->loadModuleLanguage( $this->module, $fileName, $section, $verbose );
	}

	/**
	 *	Loads Template File and returns Content.
	 *	@access		public
	 *	@param		string		$fileKey			Template Name (namespace(.class).view, i.E. example.add)
	 *	@param		array		$data				Data for Insertion in Template
	 *	@param		bool		$verbose			Flag: remark File Name (no function yet)
	 *	@return		string
	 */
	public function loadTemplate( $fileKey, $data = array(), $verbose = FALSE )
	{
		$fileName	= $this->getTemplateUri( $fileKey );
		return UI_Template::render( $fileName, $data );
	}
	
	/**
	 *	Saves Content to a Cache File.
	 *	@access		public
	 *	@param		string		$fileName 			File Name of Cache File.
	 *	@param		string		$content			Content to save to Cache File
	 *	@return 	int
	 */
#	public function saveCache( $fileName, $content )
#	{
#		import( 'de.ceus-media.file.Writer' );
#		$config	= $this->registry->get( 'config' );
#		$url	= $config['paths.cache'].$fileName;
#		$file	= new File_Writer( $url, 0750 );
#		return $file->writeString( $content );
#	}
}
?>