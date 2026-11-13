<?php
namespace hardness;
/**
 * VEN - VEN010 - Contrato de Locação
 */

function campoExclusao($array) {
	global $g;
	return <<<EOT
		<a href="#" onclick="ven010CampoExclusao('{$array['todosCampos']['T141_Id']}', '{$g['divId']}')">
			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
		</a>
EOT;
}
function validaCampoStatus($array){
	if($array['todosCampos']['T141_Flag_Status'] == 'A'){
		return "Ativo";
	}elseif($array['todosCampos']['T141_Flag_Status'] == 'I'){
		return "Inativo";
	}
}

// Imagem para exclusão de contas a receber
function campoExclusaoContasReceber($array) {
		global $g;
    	return <<<EOT
    		<a href="#" onclick="fin001CampoExclusao('{$array['todosCampos']['T002_Id']}','{$array['todosCampos']['T002_T035_Id']}', '{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	
}

// Imagem para exclusão de logística
function campoExclusaoLogistica($array) {
	global $g;
	return <<<EOT
    		<a href="#" onclick="ven009CampoExclusao('{$array['todosCampos']['T143_Id']}','{$g['divId']}')">
    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
	
}

// gerar link para abertura/visualização do contrato de aluguel
function abreContrato($array) {
	global $g;
	
	// por enquanto não vai exibir o contrato, apenas mostrar o ID e o cliente...
	/*
	return <<<EOT
    		<a href="#" onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Contrato de Aluguel ID: ' + {$array['todosCampos']['T143_T141_Id']}, '/ven/ven010/content/ven010content02/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T143_T141_Id']})+'&readOnly=true', [500,260]);">
    			Ver</span>
			</a>	
		
EOT;
	*/
	require_once('bibliotecas/classes/VEN009.php');
	$VEN009 = new VEN009();	
	return $VEN009->buscaNomeCliente($array['campoAtual']);
	
}
?>