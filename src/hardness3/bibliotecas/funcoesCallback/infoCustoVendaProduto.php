<?php
namespace hardness;

function infoCustoVendaProduto($array, $param = false, $center = 'N'){
	global $g;

	if($array !== false){
		$param = array();
		$param[0] = $array['todosCampos']['D009_Data_Alteracao_Custo'];
		$param[1] = $array['todosCampos']['D009_Data_Ultima_Venda'];
		$param[2] = $array['todosCampos']['D009_Flag_Custo_Utilizado'];
	}

	$dataAtualizacaoCusto = $param[0];
	$dataUltimaVenda = $param[1];
	$custoUtilizado = $param[2];

	$diasAtualizacaoCusto = round(abs(strtotime(date("Y-m-d"))-strtotime($dataAtualizacaoCusto))/86400);
	$diasUltimaVenda = round(abs(strtotime(date("Y-m-d"))-strtotime($dataUltimaVenda))/86400);

	$prazoValidadeCusto01 = $g['C031']['prazoValidadeCusto01'];
	$prazoValidadeCusto02 = $g['C031']['prazoValidadeCusto02'];
	$prazoValidadeCusto03 = $g['C031']['prazoValidadeCusto03'];
	$prazoUltimaVenda01 = $g['C031']['prazoUltimaVenda01'];
	$prazoUltimaVenda02 = $g['C031']['prazoUltimaVenda02'];
	$prazoUltimaVenda03 = $g['C031']['prazoUltimaVenda03'];

	if($diasAtualizacaoCusto <= $prazoValidadeCusto01 && $prazoValidadeCusto01 > 0){
		$backgroundCusto = " var(--celula-verde); color: var(--letra2)";
	} else if($diasAtualizacaoCusto <= $prazoValidadeCusto02 && $prazoValidadeCusto02 > 0){
		$backgroundCusto = "#FFFF94";
	} else if($diasAtualizacaoCusto <= $prazoValidadeCusto03 && $prazoValidadeCusto03 > 0){
		$backgroundCusto = "#FF6666";
	} else {
		$backgroundCusto = "#9166BC";
	}

	if($diasUltimaVenda <= $prazoUltimaVenda01 && $prazoUltimaVenda01 > 0){
		$backgroundVenda = " var(--celula-verde); color: var(--letra2)";
	} else if($diasUltimaVenda <= $prazoUltimaVenda02 && $prazoUltimaVenda02 > 0){
		$backgroundVenda = "#FFFF94";
	} else if($diasUltimaVenda <= $prazoUltimaVenda03 && $prazoUltimaVenda03 > 0){
		$backgroundVenda = "#FF6666";
	} else {
		$backgroundVenda = "#9166BC";
	}

	$url = '/cad/cad002/outro/cad002InfoCustoVendaProduto/';
	$extra = base64_encode(serialize($param));
	$width = "365";
	$id = 'popover-' . uniqid();
	return <<<EOT
		<div id="{$id}" style='width: 20px; height: 15px; border: 0px solid #000; border-radius: 3px; cursor: pointer; color: #000000; font-weight: normal; font-size: 13px;' onclick="infoCustoVendaProduto('{$id}', '{$url}', '{$extra}', '{$width}', '{$center}');">
			<div style='background-color: {$backgroundCusto}; width: 50%; height: 100%; float: left; font-size: 14px; text-align: center;'><font color='#000000;'>{$custoUtilizado}</font></div>
			<div style='background-color: {$backgroundVenda}; width: 50%; height: 100%; float: left; font-size: 14px; text-align: center;'><font color='#000000;'>V</font></div>
		</div>
EOT;
}
