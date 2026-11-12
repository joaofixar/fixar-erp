<?php
namespace hardness;

function campoMarcarConhecimento($array) {
	global $g;
	$T007_Id = $array['todosCampos']['T007_Id'];
	if(!empty($array['todosCampos']['T084_Id']) && empty($array['todosCampos']['T084_Numero_Conhecimento'] )){
		return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}',unique(), '', 'Editar Conhecimento ' + '{$array['todosCampos']['T084_Id']}', '/est/est005/content/est005ContentEditarConhecimento/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['T084_Id']}') + '&tabela=T084', [800,500]);">
				Sem número
			</a>
EOT;

	} else if($array['todosCampos']['T084_Numero_Conhecimento'] > 0){

		return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}',unique(), '', 'Editar Conhecimento ' + '{$array['todosCampos']['T084_Id']}', '/est/est005/content/est005ContentEditarConhecimento/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['T084_Id']}') + '&tabela=T084', [800,500]);">
				{$array['todosCampos']['T084_Numero_Conhecimento']}
			</a>
EOT;
	} else {
		if(!empty($array['todosCampos']['T007_Flag_Gerar_Conhecimento'])) {
			$retorno = "<input onclick=\"ven003MarcarNFConhecimento(this, '{$T007_Id}', '{$g['divId']}')\" type='checkbox' id='T007_Flag_Gerar_Conhecimento' title='Gerar conhecimento'";
			if ($array['todosCampos']['T007_Flag_Gerar_Conhecimento'] == 'S'){
			    return $retorno .="checked='checked'";
			}
			return $retorno .= " />";
		} else {
			return '';
		}
	}
}