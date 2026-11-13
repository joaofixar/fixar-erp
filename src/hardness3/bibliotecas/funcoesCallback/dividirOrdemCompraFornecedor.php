<?php
namespace hardness;
// Pedido, D049_Id
function dividirOrdemCompraFornecedor($array){
	global $g;
	$D024_Id = $array['todosCampos']['T224_D024_Id'];
	if(($array['todosCampos']['T224_Flag_Status'] == '' || $array['todosCampos']['T224_Flag_Status'] == '0' || $array['todosCampos']['T224_Flag_Status'] == '2' || $array['todosCampos']['T224_Flag_Status'] == '3'|| $array['todosCampos']['T224_Flag_Status'] == '12') && ($array['todosCampos']['T004_T004_Id_Origem'] <= 0 || empty($array['todosCampos']['T004_T004_Id_Origem']))){
		return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Dividir Ordem de Compra', '/est/est016/content/est016contentDividirOrdemCompra/', '&tab=geral&D024_Id=' + encodeURIComponent('{$D024_Id}'), [1000,600]); return false;">
				Dividir
			</a>
EOT;
	} else {
		return '';
	}
}