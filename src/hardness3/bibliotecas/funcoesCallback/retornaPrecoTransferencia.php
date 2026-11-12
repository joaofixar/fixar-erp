<?php
namespace hardness;

function retornaPrecoTransferencia($array, $param){
	global $g;
	$campoD009_Id = isset($param[4]) ? $param[4] : 'D009_Id';
	$campoC004_Id = isset($param[5]) ? $param[5] : 'C004_Id';
	$D009_Id = $array['todosCampos'][$campoD009_Id];
	$C004_Id = $array['todosCampos'][$campoC004_Id];
	$D024_Id = $g['C004']['C004_D024_Id'];

	$T003_Id = $T005_Id = $T007_Id = false;
	if(isset($param[2]) && $param[2] > 0){
		$T007_Id = $param[2];
		$tabela = 'T008';
	} else if(isset($param[1]) && $param[1] > 0){
		$precoTransferencia[0] = gCorrigeNumero($array['todosCampos']['T006_Valor_Preco_Transferencia'],5);
		$precoTransferencia[1] = unserialize($array['todosCampos']['T006_Demonstrativo_Preco_Transferencia']);
		$precoTransferencia[4] = $array['todosCampos']['T006_Valor_Custo_Transferencia'];
		$precoTransferencia[5] = $array['todosCampos']['T006_Flag_Origem_Produto_Transferencia'];

		$T005_Id = $param[1];
		$tabela = 'T006';
	} else if(isset($param[0]) && $param[0] > 0){
		$precoTransferencia[0] = gCorrigeNumero($array['todosCampos']['T004_Valor_Preco_Transferencia'],5);
		$precoTransferencia[1] = unserialize($array['todosCampos']['T004_Demonstrativo_Preco_Transferencia']);
		$precoTransferencia[4] = $array['todosCampos']['T004_Valor_Custo_Transferencia'];
		$precoTransferencia[5] = $array['todosCampos']['T004_Flag_Origem_Produto_Transferencia'];

		$T003_Id = $param[0];
		$tabela = 'T004';
	}
	$idItemVenda = isset($param[3]) ? $param[3] : false;
	if(!is_numeric($idItemVenda)){
		$idItemVenda = $array['todosCampos'][$idItemVenda];
	}

	if(!isset($param[4]) || !isset($param[5])){
		require_once('bibliotecas/classes/CAD002.php');
	    $CAD002 = new CAD002();
    	$precoTransferencia = $CAD002->cad002RetornaPrecoTransferencia($D009_Id, $C004_Id, $D024_Id, $T003_Id, $T005_Id, $T007_Id);
    }

	$valorPrecoTransferencia = $precoTransferencia[0];
	if($idItemVenda === true){
		return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Estoque Fora', '/cad/cad002/content/cad002ContentEstoqueExterno/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D001_Id']}') + '&T003_Id=' + encodeURIComponent('{$T003_Id}') + '&T005_Id=' + encodeURIComponent('{$T005_Id}') + '&T007_Id=' + encodeURIComponent('{$T007_Id}'), [700,400]); return false;\">{$valorPrecoTransferencia}</a>";
	}

	if($g['nivelAlcada'] < 2){
		$retorno = $valorPrecoTransferencia;
	} else {
		$array['todosCampos']['campoOriginal'] = base64_encode(serialize($precoTransferencia[1]));
		$array['campoAtual'] = $array['todosCampos']['campoAtual'] = $precoTransferencia[0];
		require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');
		$retorno = abrirNotificacao(
			$array,
			array(
				'Demonstrativo preço transferência',
				'/ven/ven001/outro/demonstrativoPrecoTransferencia/',
				'campoAtual',
				'right',
				'',
				base64_encode(serialize($precoTransferencia[1])),
				false,
				'251',
				'S'
			)
		);
	}

	if(empty($idItemVenda)){
		return $retorno;
	}
	$precoTransferencia = base64_encode(serialize($precoTransferencia));
	return <<<EOT
		<div style="align: right;">
			<div class="icone ui-state-default ui-corner-all" style="float: right; margin-left: 5px;" onclick="utilizarPrecoVendaTransferencia('{$g['divId']}', '{$g['divIdParent']}', '{$idItemVenda}', '{$tabela}', '{$C004_Id}');">
				<span class="ui-icon ui-icon-cart" title="Utilizar preço transferência"></span>
			</div>
			<span style="float: right;">{$retorno}</span>
		</div>
EOT;
}
