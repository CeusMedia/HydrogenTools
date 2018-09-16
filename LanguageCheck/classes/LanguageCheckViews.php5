<?php
class LanguageCheckViews
{
	static public function buildContent( $path, $project, $source, $target, $characterCheck = FALSE )
	{
		$lc			= new LanguageCheck( $path.$source."/", $path.$target."/" );
		$results	= $lc->getLanguageDifference( $characterCheck );

		$fileList		= "";
		$encodeList		= "";
		$sectionList	= "";
		$keyList		= "";
		$translateList	= "";

		$errors	= max( $results['fileList'], $results['encodeList'], $results['sectionList'], $results['keyList'], $results['translateList'] );
		if( !$errors )
		{
			$fileList	= "<b style='color: green'>All Language Components are OK</b>";
		}
		else
		{
			if( count( $results['fileList'] ) )
			{
				$fileList	= self::buildContentFileList( $project, $source, $target, $results['fileList'] );
			}
			if( count( $results['encodeList'] ) )
			{
				$encodeList	= self::buildContentEncodingList( $project, $source, $target, $results['encodeList'] );
			}
			if( count( $results['sectionList'] ) )
			{
				$sectionList	= self::buildContentSectionList( $project, $source, $target, $results['sectionList'] );
			}
			if( count( $results['keyList'] ) )
			{
				$keyList	= self::buildContentKeyList( $project, $source, $target, $results['keyList'] );
			}
			if( count( $results['translateList'] ) )
			{
				$translateList	= self::buildContentToTranslate( $project, $source, $target, $results['translateList'] );
			}
		}
		return require_once( "templates/languages.phpt" );
	}


	static private function buildContentEncodingList( $project, $source, $target, $resultList )
	{
		$list	= $files	= array();
		$list[]	= "<h3><small><small><small>there are</small></small></small><br/>Encoding wrong:</h3>";
		foreach( $resultList as $file )
		{
			$icon		= UI_HTML_Elements::Image( "//icons.ceusmedia.com/famfamfam/mini/icon_wand.gif", "encode this File in UTF-8", 'icon' );
			$link		= UI_HTML_Elements::Link( "?project=".$project."&source=".$source."&target=".$target."&fileName=".$file."&action=encodeFile", $icon );
			$files[]	= UI_HTML_Elements::ListItem( $link.$file );
		}
		$list[]	= UI_HTML_Elements::unorderedList( $files );
		return implode( "", $list );
	}
	
	static private function buildContentFileList( $project, $source, $target, $resultList )
	{
		$list	= $files	= array();
		$list[]	= "<h3><small><small><small>there are</small></small></small><br/>Files missing:</h3>";
		foreach( $resultList as $file )
		{
			$icon		= UI_HTML_Elements::Image( "//icons.ceusmedia.com/famfamfam/mini/copy.gif", "copy this File", 'icon' );
			$link		= UI_HTML_Elements::Link( "?project=".$project."&source=".$source."&target=".$target."&fileName=".$file."&action=copyFile", $icon );
			$files[]	= UI_HTML_Elements::ListItem( $link.$file );
		}
		$list[]	= UI_HTML_Elements::unorderedList( $files );
		return implode( "", $list );
	}
	
	static private function buildContentKeyList( $project, $source, $target, $resultList )
	{
		$content	= "<h3><small><small><small>in all compatible Sections are</small></small></small><br/>Keys missing:</h3>";
		foreach( $resultList as $item )
		{
			$keys		= array();
			$content	.= "<h4>".$$item['type']."/".$item['file']."</h4>";
			$content	.= "<h5>[".$item['section']."]</h5>";
			foreach( $item['keys'] as $key )
			{
				if( $item['type'] == "source" )
				{
					$icon	= UI_HTML_Elements::Image( "//icons.ceusmedia.com/famfamfam/mini/page_cross.gif", "remove this Pair", 'icon' );
					$link	= UI_HTML_Elements::Link( "?project=".$project."&source=".$source."&target=".$target."&fileName=".$item['file']."&section=".$item['section']."&key=".$key."&action=removePair", $icon );
				}
				else if( $item['type'] == "target" )
				{
					$icon	= UI_HTML_Elements::Image( "//icons.ceusmedia.com/famfamfam/mini/copy.gif", "copy this Pair", 'icon' );
					$link	= UI_HTML_Elements::Link( "?project=".$project."&source=".$source."&target=".$target."&fileName=".$item['file']."&section=".$item['section']."&key=".$key."&action=copyPair", $icon );
				}
				$keys[]	= UI_HTML_Elements::ListItem( $link.$key );
			}
			$content	.= UI_HTML_Elements::unorderedList( $keys );
		}
		return $content;
	}
	
	static private function buildContentSectionList( $project, $source, $target, $resultList )
	{
		$content	= "<h3><small><small><small>in all compatible Files are</small></small></small><br/>Sections missing:</h3>";
		foreach( $resultList as $item )
		{
			$sections	= array();
			foreach( $item['sections'] as $section )
			{
				$icon	= UI_HTML_Elements::Image( "//icons.ceusmedia.com/famfamfam/mini/copy.gif", "copy section", 'icon' );
				$link	= UI_HTML_Elements::Link( "?project=".$project."&source=".$source."&target=".$target."&fileName=".$item['file']."&section=".$section."&action=copySection", $icon );
				$sections[]	= UI_HTML_Elements::ListItem( $link.$section );
			}
			$sections	= UI_HTML_Elements::unorderedList( $sections );
			$content	.= "<h4>".$$item['type']."/".$item['file']."</h4>".$sections;
		}
		return $content;
	}

	static private function buildContentToTranslate( $project, $source, $target, $resultList )
	{
		foreach( $resultList as $result )
		{
			if( !isset( $translateList[$result['file']] ) )
				$translateList[$result['file']]	= array();
			if( !isset( $translateList[$result['file']][$result['section']] ) )
				$translateList[$result['file']][$result['section']] = array();
			$translateList[$result['file']][$result['section']][$result['key']]	= $result['value'];
		}

		$content	= "<h3><small><small><small>in all existing Files is</small></small></small><br/>To translate:</h3>";
		foreach( $translateList as $fileName => $sectionList )
		{
			$content	.= "<h4>".$target."/".$fileName."</h4>";
			foreach( $sectionList as $sectionKey => $pairList )
			{
				$content	.= "<h5>".$sectionKey."</h5>";
				$pairs	= array();
				foreach( $pairList as $key => $value  )
				{
					$icon	= UI_HTML_Elements::Image( "//icons.ceusmedia.com/famfamfam/mini/page_edit.gif", "edit value", 'icon' );
					$link	= UI_HTML_Elements::Link( "javascript: editValue( '?project=".$project."&source=".$source."&target=".$target."&fileName=".$fileName."&section=".$sectionKey."&key=".$key."&action=editValue', '".addslashes( $value )."' );", $icon );
					$pairs[]	= UI_HTML_Elements::ListItem( $link.$key );
				}
				$pairs	= UI_HTML_Elements::unorderedList( $pairs );
				$content	.=	$pairs;
			}
		}
		return $content;
	}
}
?>