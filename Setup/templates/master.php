<?php
//	if( $env->getRequest()->isAjax() )								// this is an AJAX request
//		return $content;													// deliver content only

$links		= $words['links'];
$sublinks	= array( 'manage/module' => $words['links_manage_module'] );

$controller	= $env->request->get( 'controller' );
$action		= $env->request->get( 'action' );

$naviMain	= new CMF_Hydrogen_View_Helper_Navigation_SingleList( $links );
$naviMain	= $naviMain->render( $controller.'/'.$action, TRUE );

$naviSub	= "";
foreach( $sublinks as $path => $links ){
	if( substr( $controller.'/', 0, strlen( $path ) ) == $path ){
		$naviSub	= new CMF_Hydrogen_View_Helper_Navigation_SingleList( $links );
		$naviSub	= $naviSub->render( $controller, TRUE );
	}
}

$model			= new Model_Instance( $env );
$optInstance	= array();
foreach( $model->getAll() as $instance )
	$optInstance[$instance->id]	= $instance->title;
ksort( $optInstance );
$instanceId		= $env->getSession()->get( 'instanceId' );
$optInstance	= UI_HTML_Elements::Options( $optInstance, $instanceId );

$badges	= array();
$infos	= array(
	$words['footer_info']['copyright'],
	sprintf( $words['footer_info']['date'], date( 'd.m.Y') ),
	sprintf( $words['footer_info']['time'], date( 'H:i:s') ),
);
if( !$config->get( 'app.production' ) ){
	$infos[]	= sprintf( $words['footer_info']['time_stopped'], $env->getClock()->stop( 0, 2 ) );
	if( $env->has( 'dbc' ) && $env->getDatabase()->numberStatements )
		$infos[]	= sprintf( $words['footer_info']['requests'], $env->getDatabase()->numberStatements );
}

$userId		= (int) $session->get( 'userId' );
$roleId		= (int) $session->get( 'roleId' );
if( $userId ){
	$modelUser	= new Model_User( $env );
	$infos[]	= 'Benutzer: '.$modelUser->get( $userId, 'username' ).' <small>('.$userId.')</small>';
}
if( $roleId ){
	$modelRole	= new Model_Role( $env );
	$infos[]	= 'Rolle: '.$modelRole->get( $userId, 'title' ).' <small>('.$roleId.')</small>';
}

$badges[]	= '<a href="http://validator.w3.org/check?uri=referer"><img style="border: 0; width: 48px; height: 16px" src="http://www.w3.org/Icons/valid-xhtml10" alt="validate HTML (as XHTML 1.0 Strict)"/></a>';
$badges[]	= '<a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border: 0; width: 48px; height: 16px" src="http://jigsaw.w3.org/css-validator/images/vcss" alt="validate CSS"/></a>';
$infos		= '<span>'.join( '</span><span>', $infos ).'</span>';
$badges		= '<span>'.join( '</span><span>', $badges ).'</span>';

$path		= $env->getRequest()->get( 'path' );
$body		= '
<div id="layout-navigation-top" style="margin: 0px auto; max-width: 960px; position: relative;">
	<div style="position: absolute; top: 5px; right: 0px; z-index: 100;">
		<select onchange="document.location.href=\'./'.$path.'?selectInstanceId=\'+$(this).val();">'.$optInstance.'</select>
	</div>
</div>
<div id="layout-header"></div>
<div id="layout-navigation" style="border-bottom: 5px solid #008FAF; background-color: #F7F7F7;">
	<div id="layout-navigation-main" style="background-color: #F7F7F7; margin: 0px auto; max-width: 960px; position: relative; ">
		'.$naviMain.'
	</div>
</div>
<div id="layout-navigation-sub" style="margin: 0px auto; max-width: 960px">
	'.$naviSub.'
</div>
<div id="layout-field" style="margin: 0px auto; max-width: 960px">
	<div id="layout-messenger">'.$messenger->buildMessages().'</div>
	<div id="layout-content" style="padding: 1em 0em">'.$content.'</div>
</div>
<div id="layer-dev">
	'.$dev.'
</div>
<div id="layout-footer">
	<div id="layout-footer-inner">
		<div id="footer-info">
			'.$infos.'
		</div>
		<div id="footer-badges">
			'.$badges.'
		</div>
		<div style="clear: both"></div>
	</div>
</div>
<script>$(document).ready(function(){});</script>';

$pathJsLib		= $config->get( 'path.scripts.lib' );
$pathCssLib		= $config->get( 'path.styles.lib' );

$page->setTitle( $words['main']['title'] );
$page->addJavaScript( $pathJsLib.'jquery/1.7.min.js' );
$page->js->addUrl( $pathJsLib.'jquery/ui/1.8.4/min.js' );
$page->js->addUrl( 'javascripts/UI.Layer.js' );
$page->js->addScript( '$(document).ready(function(){Layer.init();});' );
$page->css->primer->addUrl( $pathJsLib.'jquery/ui/1.8.4/css/smoothness.css' );
$page->css->primer->addUrl( $pathCssLib.'blueprint/reset.css' );
$page->css->primer->addUrl( $pathCssLib.'blueprint/typography.css' );
$page->css->primer->addUrl( $pathCssLib.'xmp.formats.css' );
$page->css->primer->addUrl( $pathCssLib.'layout.column.css' );
$page->addPrimerStyle( 'layout.messenger.css' );
$page->addPrimerStyle( 'form.css' );
$page->addPrimerStyle( 'form.button.css' );
$page->addPrimerStyle( 'form.fieldset.css' );
$page->addPrimerStyle( 'pagination.css' );
$page->addPrimerStyle( 'layout.navigation.css' );
$page->addPrimerStyle( 'layout.footer.css' );
$page->addThemeStyle( 'layout.navigation.sub.css' );
$page->addThemeStyle( 'layout.footer.css' );
$page->addThemeStyle( 'layer.css' );
$page->addThemeStyle( 'table.css' );
$page->addThemeStyle( 'style.css' );
$page->addBody( $body );
$page->setPackaging( FALSE, FALSE );
return $page->build();
?>