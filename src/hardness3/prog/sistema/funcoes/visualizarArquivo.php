<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'visualizacao':
		
		if(!empty($r_urlVideo)){
            echo "<div style='display: flex; justify-content: center; align-items: flex-start;'><iframe width='930' height='450' src='{$r_urlVideo}' frameborder='0' allowfullscreen></iframe></div>";
		}elseif(!empty($r_urlLink)){
			echo <<<EOT
    			<table>
        			<tr>
            			<td>Endereço:</td>
            			<td style="font-weight: bold;">{$r_urlLink}</td> 
        			</tr>
    			</table>
    			<div style="margin-top: 5px; width: 100%; height: 2px; background-color: #A6C9E2;"></div>
    			<iframe id="links_iframe" src="{$r_urlLink}" style="margin-top: 3px; border: none; width: 100%; height: 100%;"></iframe> 
EOT;
		} else {
			$extensao = strrev(substr(strrev($r_urlArquivo), 0, 4));
			if(in_array($extensao, array('.gif', '.jpg', 'jpeg', '.png'))) {
				//echo "<img src=\"{$r_urlArquivo}\" style='max-width: 100%; max-height: 100%;/>";
				echo <<<EOT
                	<div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                    	<img src='{$r_urlArquivo}'style='max-width: 100%; max-height: 100%;'/>
                	</div>  
EOT;
			} else {
				echo "<iframe src=\"http://docs.google.com/viewer?embedded=true&url={$r_urlArquivo}\" style=\"width:100%; height:100%;\" frameborder=\"0\"></iframe>";
			}
		}
	break;
}
