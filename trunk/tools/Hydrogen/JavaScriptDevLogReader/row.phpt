<?php
$parts	= explode( "(", $entry['userAgent'] );
$userAgentLabel	= $parts[0];
$userAgentInfo	= "(".implode( "(", $parts );
return '
    <tr>
      <td><a class="remove" href="?remove='.$entry['hash'].'">[x]</a></td>
      <td>'.$date.'&nbsp;'.$time.'</td>
      <td><a href="'.$entry['url'].'">'.$entry['url'].'</a></td>
      <td><b>'.stripslashes( $entry['message'] ).'</b></td>
      <td><em><acronym title="'.$userAgentInfo.'">'.$userAgentLabel.'</acronym></em></td>
    </tr>';
?>
