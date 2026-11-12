<?php
namespace hardness;
// Pedido, D049_Id
function dividirPedidoCliente($array){
	global $g;
	$D024_Id = $array['todosCampos']['D024_Id'];
	if(($array['todosCampos']['T005_Flag_Status'] == '' || $array['todosCampos']['T005_Flag_Status'] == '0' || $array['todosCampos']['T005_Flag_Status'] == '9' || $array['todosCampos']['T005_Flag_Status'] == '26'|| $array['todosCampos']['T005_Flag_Status'] == '14') && ($array['todosCampos']['T005_T005_Id_Origem'] <= 0 || empty($array['todosCampos']['T005_T005_Id_Origem']))){
		return <<<EOT
			<a href="#" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Dividir Pedido', '/ven/ven012/content/ven012ContentDividirPedidoEmpresa/', '&tab=geral&D024_Id=' + encodeURIComponent('{$D024_Id}'), [1000,600]); return false;">
				Dividir
			</a>
EOT;
	} else {
		return '';
	}
}