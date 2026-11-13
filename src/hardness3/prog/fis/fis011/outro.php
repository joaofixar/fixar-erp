<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
    case 'fis011LogoSped':
        echo '<img src="/hardness3/static/img/sped.png" style="margin-top: 80px;width:150px;" />';
        if(!empty($r_spedTipo)){
        	echo <<<EOT
        		<span style="color: #DD9646; font-weight: bold; font-size: 14px; margin-left: 85px;">{$r_spedTipo}</span>
EOT;
		}
    break;
}
?>