<?php
require_once( "../../useContainer.php5" );
import( 'de.ceus-media.ui.DevOutput' );

function buildIndicator( $found, $count, $ratio, $length = 200 )
{
	$ratio	= ( $count - $found ) / $count;
	$length	= floor( $ratio * $length );

	$colorR	= ( 1 - $ratio ) > 0.5 ? 255 : round( ( 1 - $ratio ) * 2 * 255 );
	$colorG	= $ratio > 0.5 ? 255 : round( $ratio * 2 * 255 );
	$colorR	= str_pad( dechex( $colorR ), 2, "0" );
	$colorG	= str_pad( dechex( $colorG ), 2, "0" );
	$colorB	= "00";
	$color	= $colorR.$colorG.$colorB;

	return '<div class="indicator">
 <div class="indicator-outer">
   <div class="indicator-inner" style="width:'.$length.'px; background-color: #'.$color.'">
   </div>
 </div>
 <div class="indicator-percentage">
   '.floor( $ratio * 100 ).' %
 </div>
</div>';
}

$_REQUEST['verbose']	= FALSE;
require_once( "service.php5" );

/*	$data['found'] = 1;
	$data['count'] = 1000;
	$data['ratio'] = $data['found'] / $data['count'];
	print_m( $data );
*/

$indicator = buildIndicator( $data['found'], $data['count'], $data['ratio'], 200 );
echo '<link rel="stylesheet" href="indicator.css"/>';
echo $indicator;
?>