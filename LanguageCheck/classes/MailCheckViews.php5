<?php
class MailCheckViews
{
	static public function buildContent( $path, $project, $source, $target )
	{
		$sc			= new MailCheck( $path.$source."/", $path.$target."/" );
		$results	= $sc->getMailDifference();
		
		$errors	= max( $results['fileList'], $results['encodeList'], $results['translateList'] );
		if( !$errors )
		{
			$fileList	= "<b style='color: green'>All Mail Components are OK</b>";
		}
		else
		{
			if( count( $results['fileList'] ) )
			{
				$fileList	= self::buildContentFileList( $path, $project, $source, $target, $results['fileList'] );
			}
			if( count( $results['encodeList'] ) )
			{
				$encodeList	= self::buildContentEncodingList( $path, $project, $source, $target, $results['encodeList'] );
			}
			if( count( $results['translateList'] ) )
			{
				$translateList	= self::buildContentToTranslate( $path, $project, $source, $target, $results['translateList'] );
			}
		}
		return require_once( "templates/mails.phpt" );
	}


	static private function buildContentEncodingList( $path, $project, $source, $target, $resultList )
	{
		$list	= $files	= array();
		$list[]	= "<h3><small><small><small>there are</small></small></small><br/>Encoding wrong:</h3>";
		foreach( $resultList as $fileName )
		{
			$icon		= UI_HTML_Elements::Image( "//icons.ceusmedia.com/famfamfam/mini/icon_wand.gif", "encode this File in UTF-8", 'icon' );
			$link		= UI_HTML_Elements::Link( "?project=".$project."&source=".$source."&target=".$target."&fileName=".$fileName."&action=encodeMailFile", $icon );
			$files[]	= UI_HTML_Elements::ListItem( $link.$fileName );
		}
		$list[]	= UI_HTML_Elements::unorderedList( $files );
		return implode( "", $list );
	}
	
	static private function buildContentFileList( $path, $project, $source, $target, $resultList )
	{
		$list	= $files	= array();
		$list[]	= "<h3><small><small><small>there are</small></small></small><br/>Files missing:</h3>";
		foreach( $resultList as $fileName )
		{
			$icon		= UI_HTML_Elements::Image( "//icons.ceusmedia.com/famfamfam/mini/copy.gif", "copy this File", 'icon' );
			$link		= UI_HTML_Elements::Link( "?project=".$project."&source=".$source."&target=".$target."&fileName=".$fileName."&action=copyMailFile", $icon );
			$files[]	= UI_HTML_Elements::ListItem( $link.$fileName );
		}
		$list[]	= UI_HTML_Elements::unorderedList( $files );
		return implode( "", $list );
	}
	
	static private function buildContentToTranslate( $path, $project, $source, $target, $resultList )
	{
		$list	= $files	= array();
		$list[]	= "<h3><small><small><small>in all existing Files is</small></small></small><br/>To translate:</h3>";
		foreach( $resultList as $fileName )
		{
			$icon		= UI_HTML_Elements::Image( "//icons.ceusmedia.com/famfamfam/mini/page_edit.gif", "edit value", 'icon' );
			$link		= UI_HTML_Elements::Link( "javascript: editMailValue( '?project=".$project."&source=".$source."&target=".$target."&fileName=".$fileName."&action=editMailValue', '".urlencode( addslashes( $value ) )."' );", $icon );
			$pairs[]	= UI_HTML_Elements::ListItem( $link.$fileName );
		}
		$list[]	= UI_HTML_Elements::unorderedList( $pairs );
		return implode( "", $list );
	}
}
?>