<?php
namespace hardness;

function campoMensagensT234($array) {
 	$boxShadow = "-webkit-box-shadow: -1px 1px 3px 0px rgba(50, 50, 50, 0.50); -moz-box-shadow: -1px 1px 3px 0px rgba(50, 50, 50, 0.50); box-shadow: -1px 1px 3px 0px rgba(50, 50, 50, 0.50);";
 	$retorno = '<div style="width:100%; white-space:normal; margin-bottom: 15px; font-family: Helvetica; font-weight: normal; color: #000;">';
	
	$texto = str_replace("&NBSP;", "&nbsp;", $array['todosCampos']['T234_Descricao']);
	$texto = str_replace(array("<div><br></div>", "<DIV><BR></DIV>"), " ", $texto);
	$texto = strip_tags($texto);

	$retorno .= <<<EOT
		<div style="box-sizing:border-box; background-color: #C1FFB6; border-radius: 3px; min-height: 23px; padding: 3px; 2px; color: #222; margin-'right': 20px; {$boxShadow}">
			<div style="font-size: 11px; margin-bottom: 3px;">{$array['todosCampos']['T234_Data_Hora_Abertura']} - {$array['todosCampos']['T234_Solicitante']} - {$array['todosCampos']['T234_D024_Id']}</div>
			$texto
		</div>
EOT;
 	
 	$retorno .= '</div>';
	return $retorno;
}
