<?php
require_once( "cmClasses/trunk/autoload.php5" );

CMC_Loader::registerNew( 'php5', NULL, 'classes/' );


$clock		= new Alg_Time_Clock();

$request	= new Net_HTTP_Request_Receiver;
$source		= $request->get( 'source' );
$target		= $request->get( 'target' );
$check		= $request->get( 'characterCheck' );

$contentPath	= "../../contents/";
$contentPath	= "/var/www/Hydra/";
$languagePath	= $contentPath."locales/";
$staticPath		= $contentPath."html/";
$mailPath		= $contentPath."templates/mails/";
$subjectPath	= $contentPath."templates/mailsubjects/";

//  --  ACTIONS  --  //
$msg	= "";
if( $request->has( 'action' ) ){
	try{
		$lca	= new LanguageCheckActions();
		$sca	= new StaticCheckActions();
		$mca	= new MailCheckActions();
		$msca	= new MailSubjectCheckActions();
		$lca->act( $languagePath );
		$sca->act( $staticPath );
		$mca->act( $mailPath );
		$msca->act( $subjectPath );
	}
	catch( Exception $e ){
		$msg	= $e->getMessage();
	}
}

$languages	= array();
$dir	= new DirectoryIterator( $languagePath );
foreach( $dir as $entry )
{
	if( !$entry->isDir() )
		continue;
	if( $entry->isDot() )
		continue;
	if( $entry->getFilename() == ".svn" )
		continue;
	$languages[]	= $entry->getFilename();
}


foreach( $languages as $language )
{
	$opt_source[$language]	= $language;
	$opt_target[$language]	= $language;
}
unset( $languages );
$opt_source['_selected']	= $source;
$opt_target['_selected']	= $target;

$project = "";
$fileList = $sectionList = $keyList = "";

$statics	= $languages	= $mails	= $subjects	= "";
if( $source && $target )
{
	if( file_exists( $staticPath ) )
		$statics	= StaticCheckViews::buildContent( $staticPath, $project, $source, $target );
	if( file_exists( $languagePath ) )
		$languages	= LanguageCheckViews::buildContent( $languagePath, $project, $source, $target, $check );
	if( file_exists( $mailPath ) )
		$mails		= MailCheckViews::buildContent( $mailPath, $project, $source, $target );
	if( file_exists( $subjectPath ) )
		$subjects	= MailSubjectCheckViews::buildContent( $subjectPath, $project, $source, $target );
}
echo require_once( "templates/master.phpt" );
?>
