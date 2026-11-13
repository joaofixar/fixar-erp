<?php
namespace hardness;
/**
 * Ações
 */
switch ($g['r'][3]) {
	/**
	 * Cabeçalho Cliente
	 * Chamado em: cad001tabCadastro
	 */
	case 'cad052Cabecalho':
		// Select
		$sql = <<<EOT
		SELECT
			*
		FROM
			D001
			LEFT JOIN D005  on D005_Id=D001_D005_Id
			LEFT JOIN D037  on D037_Id=D001_D037_Id
			LEFT JOIN D049  on D049_D001_Id=D001_Id
			LEFT JOIN D009  on D009_D049_Id=D049_Id
			LEFT JOIN D001A on D001A_D001_Id=D001_Id
		WHERE
			(D001_Id='{$r_D001_Id}')
EOT;
		$resultado = mysql_query($sql);
		$row = mysql_fetch_assoc($resultado);

		// Registro vazios = FALTA
		foreach ($row as &$row_) {
			if (empty($row_)) {
				$row_ = '<span style="color: var(--BGVermelho);">FALTA</span>';
			}
		}
		if($row['D001_Flag_Pre_Cadastro'] == "N"){
			$preCad = "Não";
		} else {
			$preCad = "Sim";
		}
		if($row['D001_Flag_Ativo'] == "N"){
			$ativo = "Não";
		} else {
			$ativo = "Sim";
		}
		$D005_Observacao = substr($row['D005_Observacao'], 0, 25);
		if(!empty($D005_Observacao)){
			$D005_Observacao = " - ".$D005_Observacao;
		}
		$row['D009_Valor_Custo_Unitario'] = gCorrigeNumero($row['D009_Valor_Custo_Unitario'], 4);
		$row['D001_Lote_Economico'] = gCorrigeNumero($row['D001_Lote_Economico']);
		$row['D001A_Flag_Engenharia_Liberada'] = $row['D001A_Flag_Engenharia_Liberada'];
		// Print
		echo <<<EOT
			<div class="contentCabecalhoLayout">
				<table class="tabela ui-state-default ui-corner-all">
					<tr class="linhaDesc">
						<td>Código</td>
						<td>Produto</td>
						<td>Custo</td>
						<td>Unidade</td>
						<td>Lote Econômico</td>
						<td>Engenharia Liberada</td>
						<td rowspan="2">
							<!-- <button type="button" id="demonstracaoCusto" style="margin-right: -50px;">Demonstração Custo</button>-->
						</td>
					</tr>
					<tr class="linhaVal">
						<td>{$row['D001_Codigo_Produto']}</td>
						<td>{$row['D001_Descricao_Produto']}</td>
						<td>{$row['D009_Valor_Custo_Unitario']}</td>
						<td>{$row['D037_Unidade']}</td>
						<td>{$row['D001_Lote_Economico']}</td>
						<td>{$row['D001A_Flag_Engenharia_Liberada']}</td>
					</tr>
				</table>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {	
				$('#demonstracaoCusto').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Demonstração Custo', '/cad/cad052/content/cad052ContentDemonstracao/', '&D001_Id=' + encodeURIComponent({$r_D001_Id}), [300,144]);
				});
				
				//$('.contentCabecalhoLayout').unbind('click').bind('click', function(data) {
				//	abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Engenharia do Produto', '/cad/cad052/content/cad052ContentProduto/', '&acaoId=' + encodeURIComponent({$r_D001_Id}), [300,300]);
				//});
			} </script></div>
EOT;
	break;
	
	case 'cad052Demonstracao':
		$D001_Id = isset($r_D001_Id) ? $r_D001_Id : false;
		//Dados do Produto
		$D001 = "SELECT D001_Codigo_Produto, D001_Descricao_Produto FROM D001 WHERE D001_Id = '{$D001_Id}'";
		$D001 = mysql_query($D001);
		$produto = mysql_fetch_array($D001);
		$codigo = $produto[0];
		$nome = $produto[1];
		//Custo total da Composição
		$composicao = "SELECT SUM(D009_Valor_Custo_Unitario * D074_Quantidade) as Custo 
					     FROM D074
				    LEFT JOIN D001 ON D001_Id = D074_D001_Id_Composicao -- id dos produtos comprados (substituir por D074_D001_Id_Composicao)
				    LEFT JOIN D049 ON D001_Id = D049_D001_Id
					LEFT JOIN D009 ON D049_Id = D009_D049_Id
					LEFT JOIN D037 ON D037_Id = D001_D037_Id
						WHERE D074_D001_Id = '{$D001_Id}'
						  AND D009_C004_Id='{$g['empresaAtual']}'";
		$rComposicao = mysql_query($composicao);
		$composicao = mysql_fetch_array($rComposicao);
		$composicao = $composicao['Custo'];
		//Custo total e tempo dos processos
		$processo = "SELECT SUM(D075_Lead_Time) as Tempo, SUM(D075_Lead_Time * (D079_Custo_Minuto + D078_Custo_Minuto)) as Custo 
						FROM D075
						LEFT JOIN D078 on D078_Id = D075_D078_Id
						LEFT JOIN D079 on D079_Id = D075_D079_Id
						WHERE D075_D001_Id = '{$r_D001_Id}'";
		$rProcesso = mysql_query($processo);
		$processo = mysql_fetch_array($rProcesso);
		$custoProcesso = $processo['Custo'];
		$tempoProducao = $processo['Tempo'];
		//Custo total do material de consumo utilizado nos processos
		$consumo = "SELECT SUM(D076_Quantidade * D009_Valor_Custo_Unitario) as Custo
						FROM D076
						LEFT JOIN D009 ON D009_Id = D076_D009_Id
						LEFT JOIN D001 ON D001_Id = D009_D001_Id 
						LEFT JOIN D037 ON D037_Id = D001_D037_Id
						LEFT JOIN D075 ON D075_Id = D076_D075_Id		
						WHERE D075_D001_Id = '{$r_D001_Id}'";
		$rConsumo = mysql_query($consumo);
		$consumo = mysql_fetch_array($rConsumo);
		$consumo = $consumo['Custo'];
		//Custo final
		$custoFinal = $composicao + $custoProcesso + $consumo;
		$custoFinal = gCorrigeNumero($custoFinal);
		$composicao = gCorrigeNumero($composicao);
		$custoProcesso = gCorrigeNumero($custoProcesso);
		$consumo = gCorrigeNumero($consumo);
		echo <<<EOT
			<style type="text/css">
			.containerTop{
				width: 99%;
				height: 99%;
				float:left;
			}
			.tableDemonstracaoCusto{
				min-width:30%;
				margin:10px auto;
				border:1px solid #000;
				padding:5px;
				border-radius:5px;
			}
			</style>
			<div class="containerTop">
		 	<table class="tableDemonstracaoCusto">
		 		<tr>
		 			<td colspan="3" align="center">$codigo - $nome</td>
			 	</tr>
			 	<tr>
		 			<td>Custo da Composição</td>
		 			<td></td>
			 		<td align="right">$composicao</td>
			 	</tr>
			 	<tr>
			 		<td>Custo dos Processos</td>
			 		<td></td>
			 		<td align="right">$custoProcesso</td>
			 	</tr>
			 	<tr>
			 		<td>Custo dos Materiais de Consumo</td>
			 		<td></td>
			 		<td align="right">$consumo</td>
			 	</tr>
			 	<tr>
			 		<td>Custo Final</td>
			 		<td></td>
			 		<td align="right">$custoFinal</td>
			 	</tr>
			 	<tr>
			 		<td>Tempo de Produção</td>
			 		<td></td>
			 		<td align="right">$tempoProducao</td>
			 	</tr>
		 	</table>
		 	</div>
EOT;
		/**
		* Botões
		*/
		// Definição dos botões
		$botoesEsq = $botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;
}
?>