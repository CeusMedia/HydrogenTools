<?php
require_once( "../../useContainer.php5" );
import( 'de.ceus-media.ui.DevOutput' );
import( 'de.ceus-media.StopWatch' );
import( 'classes.StaticCheck' );
import( 'classes.MailCheck' );
import( 'classes.MailSubjectCheck' );
import( 'classes.LanguageCheck' );

function getData( $projects )
{
	$count	= 0;
	foreach( $projects as $project )
	{
		$contentPath	= "../../".$project."/contents/";
		$languagePath	= $contentPath."languages/";
		$staticPath		= $contentPath."html/";
		$mailPath		= $contentPath."templates/mails/";
		$subjectPath	= $contentPath."templates/mailsubjects/";

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
		$source	= array_shift( $languages );
		foreach( $languages as $target )
		{
			//  --  LANGUAGE CHECK  --  //
			$count		+= 5;
			$lc			= new LanguageCheck( $languagePath.$source."/", $languagePath.$target."/" );
			$results	= $lc->getLanguageDifference( FALSE );
			foreach( $results as $topic => $result )
			{
				if( count( $result ) )
				{
					$found	+= (int) (bool) count( $result );
					$list[$project][$source."->".$target]['language'][] = $topic;
				}
			}

			//  --  MAIL CHECK  --  //
			$count		+= 3;
			$mc			= new MailCheck( $mailPath.$source."/", $mailPath.$target."/" );
			$results	= $mc->getMailDifference();
			foreach( $results as $topic => $result )
			{
				if( count( $result ) )
				{
					$found	+= (int) (bool) count( $result );
					$list[$project][$source."->".$target]['mail'][] = $topic;
				}
			}

			//  --  MAIL SUBJECT CHECK  --  //
			$count		+= 3;
			$mc			= new MailSubjectCheck( $subjectPath.$source."/", $subjectPath.$target."/" );
			$results	= $mc->getMailSubjectDifference();
			foreach( $results as $topic => $result )
			{
				if( count( $result ) )
				{
					$found	+= (int) (bool) count( $result );
					$list[$project][$source."->".$target]['subject'][] = $topic;
				}
			}

			//  --  STATIC CHECK  --  //
			$count		+= 3;
			$sc			= new StaticCheck( $staticPath.$source."/", $staticPath.$target."/" );
			$results	= $sc->getStaticDifference();
			foreach( $results as $topic => $result )
			{
				if( count( $result ) )
				{
					$found	+= (int) (bool) count( $result );
					$list[$project][$source."->".$target]['static'][] = $topic;
				}
			}
		}
	}
	$data	= array(
		'found'	=> $found,
		'count'	=> $count,
		'ratio'	=> $found / $count,
		'list'	=> $list
	);
	return $data;
}


$st	= new StopWatch();
$projects	= array(
	'Frontend',
	'Office',
);
if( !isset( $_REQUEST['verbose'] ) )
	$_REQUEST['verbose']	= TRUE;
if( !isset( $_REQUEST['format'] ) )
	$_REQUEST['format']	= "php";


$cacheFile	= "service.".strtolower( $_REQUEST['format'] ).".cache";
if( file_exists( $cacheFile ) && !isset( $_REQUEST['refresh'] ) )
{
	$data = file_get_contents( $cacheFile );
	if( $_REQUEST['verbose'] )
		die( $data );
	else
		return $data = unserialize( $data );
}
$data	= getData( $projects );
$serial['php']	= serialize( $data );
$serial['txt']	= $data['found']."/".$data['count'].":".$data['ratio'];
file_put_contents( $cacheFile, $serial[$_REQUEST['format']] );

if( $_REQUEST['verbose'] )
{

	if( !isset( $_REQUEST['format'] ) )
		$_REQUEST['format']	= "txt";
	switch( $_REQUEST['format'] )
	{
		case 'txt':
			die( $found."/".$count );
		case 'php':
			die( $serial['php'] );
	}
}
//remark( "time: ".$st->stop( 3, 0 ) );
//print_m( $data );
?>