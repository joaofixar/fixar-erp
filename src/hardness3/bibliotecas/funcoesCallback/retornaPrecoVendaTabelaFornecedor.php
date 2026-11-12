<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: retornaPrecoVenda()
*/

function retornaPrecoVendaTabelaFornecedor($array, $tabela) {
	global $g;
	// Preço e demonstrativo
	require_once('bibliotecas/classes/CAD002.php');
	$CAD002 = new CAD002();
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();
	$D069 = $array['todosCampos'];

	$sql = mysql_query("SELECT {$tabela}_C004_Id as C004_Id, {$tabela}_D024_Id as D024_Id, {$tabela}_Flag_Destino_Produto as Flag_Destino_Produto, {$tabela}_C004_Id_Estoque as C004_Id_Estoque FROM {$tabela} WHERE {$tabela}_Id = '{$D069[$tabela.'_Id']}'");
	$row = mysql_fetch_array($sql);

	$T003_Id = $T005_Id = false;
	if($tabela == 'T003'){
		$T003_Id = $D069[$tabela.'_Id'];
	} else if($tabela == 'T005'){
		$T005_Id = $D069[$tabela.'_Id'];
	}

	$D006_Id = $VEN001->retornaCFOPTabelaFornecedor($row['D024_Id'], $D069['D069_Id'], $row['C004_Id'], $row['Flag_Destino_Produto'], false, false, false, $row['C004_Id_Estoque']);
	$aliquotas = $VEN001->retornaAliquotasTabelaFornecedor($D069['D069_Id'], $T003_Id, $T005_Id, false, $D006_Id);
	$preco = $CAD002->cad002RetornaPrecoVendaTabelaFornecedor($D069['D069_Id'], $D069[$tabela.'_Flag_Lista_Preco'], $D069[$tabela.'_D024_Id'], $D069[$tabela.'_Flag_Acp'], $D006_Id, $aliquotas['Aliquota_ICMS'], $aliquotas['Aliquota_PIS'], $aliquotas['Aliquota_COFINS'], $aliquotas['Aliquota_IPI']);

	if($g['nivelAlcada'] < 4){
		return $preco[0];
	} else {
		$array['campoAtual'] = $preco[0];
		require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');
		return abrirNotificacao(
			$array,
			array(
				'Demonstrativo preço Tabela Fornecedor',
				'/ven/ven001/outro/demonstrativoPrecoTabelaFornecedor/',
				'campoAtual',
				'right',
				'',
				base64_encode(serialize(array($preco[0], $preco[1]))),
				false,
				'270',
			)
		);
	}
}