<?php
$parts	= explode( "(", $entry['agent'] );
$userAgentLabel	= $parts[0];
$userAgentInfo	= "(".implode( "(", $parts );

$class	= 'goto-html';
$uri	= $file;
if( preg_match( '/\.js(\?.*)?$/', $file ) ){
	$uri	= './?source='.$file.'&line='.$entry['line'];
	$class	= 'goto-source';
}

return '
    <tr>
      <td><b>'.stripslashes( $entry['message'] ).'</b></td>
      <td>'.$entry['line'].'</td>
      <td><a href="'.$uri.'" class="'.$class.'">'.$file.'</a></td>
      <td>'.$date.'&nbsp;'.$time.'</td>
      <td><em><acronym title="'.$userAgentInfo.'">'.$userAgentLabel.'</acronym></em></td>
      <td><a class="remove" href="?remove='.$entry['jsErrorId'].'">[x]</a></td>
    </tr>';
?>