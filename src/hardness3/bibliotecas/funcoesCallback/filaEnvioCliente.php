<?php
namespace hardness;

function filaEnvioCliente($array){
	global $g;
	$T007_Id = $array['todosCampos']['T007_Id'];
	$T007_Cliente_Razao = $array['todosCampos']['T007_Cliente_Razao'];
	$D022_Nome_Empresa = $array['todosCampos']['D022_Nome_Empresa'];
	$color = 'var(--BGAzul)';
	$T007_Observacao_Nota_Fiscal_1 = $array['todosCampos']['T007_Observacao_Nota_Fiscal_1'];
	$T007_Observacao_Nota_Fiscal_2 = $array['todosCampos']['T007_Observacao_Nota_Fiscal_2'];
	if (empty($T007_Observacao_Nota_Fiscal_1) && empty($T007_Observacao_Nota_Fiscal_2)) {
		$color = 'var(--BGVermelho)';
		$T007_Observacao_Nota_Fiscal_1 = '(sem observação fiscal)';
	}
	//$T007_Cliente_Razao = quebraBr($T007_Cliente_Razao,30);
	$T007_Observacao_Nota_Fiscal_1 = quebraBr($T007_Observacao_Nota_Fiscal_1);
	$T007_Observacao_Nota_Fiscal_1 = str_replace(';', '<br />', $T007_Observacao_Nota_Fiscal_1);
	$T007_Observacao_Nota_Fiscal_2 = quebraBr($T007_Observacao_Nota_Fiscal_2);
	$T007_Observacao_Nota_Fiscal_2 = str_replace(';', '<br />', $T007_Observacao_Nota_Fiscal_2);
	$novoIdTelaForm = uniqid();
	$from='T007';
	$retorno = <<<EOT
		<span onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: {$T007_Id}', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent('{$T007_Id}') + '&tabela={$from}', [990,605]);">
			{$T007_Cliente_Razao}
		</span>
		<br />
		<span style='background:var(--celula-amarelo)' onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: {$T007_Id}', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent('{$T007_Id}') + '&tabela={$from}', [990,605]);">
			{$D022_Nome_Empresa}
		</span>
		<br />
		<span style="font-size:11px;color: {$color};" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Nota Fiscal - ID: {$T007_Id}', '/ven/ven003/content/ven003contentIncluirNotaFiscal/', '&acaoId=' + encodeURIComponent('{$T007_Id}') + '&tabela={$from}', [990,605]);">
			{$T007_Observacao_Nota_Fiscal_1} {$T007_Observacao_Nota_Fiscal_2}
		</span>
EOT;
	return $retorno;
}
