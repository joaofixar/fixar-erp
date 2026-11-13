<?php
namespace hardness;

switch($g['r'][3]){
	case 'texto':
		echo '<div style="width: 100%; max-height: 300px; overflow: auto;">';
		if ($r_extra) {
			echo str_replace($r_extra, $r_extra . '<br/>',  $r_acaoId);
		} else {
			echo $r_acaoId;
		}
		echo '</div>';
	break;
}
