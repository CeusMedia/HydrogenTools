<?php

$count			= 0;

$classes		= '-';
if( $module->files->classes ){
	$classes	= array();
	foreach( $module->files->classes as $item ){
		$count++;
		$class	= NULL;
		if( 0 && !file_exists( $pathModule.'/classes/'.$item ) ){
			$this->env->messenger->noteError( 'Missing: '.$pathModule.'/classes/'.$item );
			$class	= 'missing';
		}
		$url		= './?action=viewCode&moduleId='.$moduleId.'&type=class&fileName='.$item;
		$label		= UI_HTML_Tag::create( 'span', $item, array( 'class' => 'icon class' ) );
		$link		= UI_HTML_Elements::Link( $url, $label, 'layer-html' );
		$classes[]	= UI_HTML_Elements::ListItem( $link, 1, array( 'class' => $class ) );
	}
	$classes	= UI_HTML_Elements::unorderedList( $classes, 1, array( 'class' => 'classes' ) );
}

//$classes	= xmp( CMF_Hydrogen_View_Helper_Diff::htmlDiff( file_get_contents( 'config.ini.inc' ), file_get_contents( 'config.ini.inc.dist' ) ) );
//die( $classes );

$locales	= '-';
if( $module->files->locales ){
	$locales	= array();
	foreach( $module->files->locales as $item ){
		$count++;
		$class	= NULL;
		if( 0 && !file_exists( $pathModule.'/locales/'.$item ) ){
			$this->env->messenger->noteError( 'Missing: '.$pathModule.'/locales/'.$item );
			$class	= 'missing';
		}
		$url		= './?action=viewCode&moduleId='.$moduleId.'&type=locale&fileName='.$item;
		$label		= UI_HTML_Tag::create( 'span', $item, array( 'class' => 'icon locale' ) );
		$link		= UI_HTML_Elements::Link( $url, $label, 'layer-html' );
		$locales[]	= UI_HTML_Elements::ListItem( $link, 1, array( 'class' => $class ) );
	}
	$locales		= UI_HTML_Elements::unorderedList( $locales, 1, array( 'class' => 'locales' ) );
}

$templates	= '-';
if( $module->files->templates ){
	$templates	= array();
	foreach( $module->files->templates as $item ){
		$count++;
		$class	= NULL;
		if( 0 && !file_exists( $pathModule.'/templates/'.$item ) ){
			$this->env->messenger->noteError( 'Missing: '.$pathModule.'/templates/'.$item );
			$class	= 'missing';
		}
		$url		= './?action=viewCode&moduleId='.$moduleId.'&type=template&fileName='.$item;
		$label		= UI_HTML_Tag::create( 'span', $item, array( 'class' => 'icon template' ) );
		$link		= UI_HTML_Elements::Link( $url, $label, 'layer-html' );
		$templates[]	= UI_HTML_Elements::ListItem( $link, 1, array( 'class' => $class ) );
	}
	$templates	= UI_HTML_Elements::unorderedList( $templates, 1, array( 'class' => 'templates' ) );
}

$styles	= '-';
if( $module->files->styles ){
	$styles	= array();
	foreach( $module->files->styles as $item ){
		$count++;
		$class	= NULL;
		if( 0 && !file_exists( $pathModule.'/css/'.$item ) ){
			$this->env->messenger->noteError( 'Missing: '.$pathModule.'/css/'.$item );
			$class	= 'missing';
		}
		$url		= './?action=viewCode&moduleId='.$moduleId.'&type=style&fileName='.$item;
		$label		= UI_HTML_Tag::create( 'span', $item, array( 'class' => 'icon style' ) );
		$link		= UI_HTML_Elements::Link( $url, $label, 'layer-html' );
		$styles[]	= UI_HTML_Elements::ListItem( $link, 1, array( 'class' => $class ) );
	}
	$styles		= UI_HTML_Elements::unorderedList( $styles, 1, array( 'class' => 'styles' ) );
}

$scripts	= '-';
if( $module->files->scripts ){
	$scripts	= array();
	foreach( $module->files->scripts as $item ){
		$count++;
		$class	= NULL;
		if( 0 && !file_exists( $pathModule.'/js/'.$item ) ){
			$this->env->messenger->noteError( 'Missing: '.$pathModule.'/js/'.$item );
			$class	= 'missing';
		}
		$url		= './?action=viewCode&moduleId='.$moduleId.'&type=script&fileName='.$item;
		$label		= UI_HTML_Tag::create( 'span', $item, array( 'class' => 'icon script' ) );
		$link		= UI_HTML_Elements::Link( $url, $label, 'layer-html' );
		$scripts[]	= UI_HTML_Elements::ListItem( $link, 1, array( 'class' => $class )  );
	}
	$scripts		= UI_HTML_Elements::unorderedList( $scripts, 1, array( 'class' => 'scripts' ) );
}

$images	= '-';
if( $module->files->images ){
	$images	= array();
	foreach( $module->files->images as $item ){
		$count++;
		$class	= NULL;
		if( 0 && !file_exists( $pathModule.'/images/'.$item ) ){
			$this->env->messenger->noteError( 'Missing: '.$pathModule.'/images/'.$item );
			$class	= 'missing';
		}
		$label		= UI_HTML_Tag::create( 'span', $item, array( 'class' => 'icon image' ) );
		$images[]	= UI_HTML_Elements::ListItem( $label, 1, array( 'class' => $class ) );
	}
	$images		= UI_HTML_Elements::unorderedList( $images, 1, array( 'class' => 'images' ) );
}

return '
<dl class="resources">
	<dt>'.$w->resourceClasses.'</dt>
	<dd>'.$classes.'</dd>
	<dt>'.$w->resourceLocales.'</dt>
	<dd>'.$locales.'</dd>
	<dt>'.$w->resourceTemplates.'</dt>
	<dd>'.$templates.'</dd>
	<dt>'.$w->resourceStyles.'</dt>
	<dd>'.$styles.'</dd>
	<dt>'.$w->resourceScripts.'</dt>
	<dd>'.$scripts.'</dd>
	<dt>'.$w->resourceImages.'</dt>
	<dd>'.$images.'</dd>
</dl>
<div class="clearfix"></div>
';

?>
