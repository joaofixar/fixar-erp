<?php
namespace hardness;

function colunaFornecedores ($array){
	global $g;
	
	$D001_Id = $array['todosCampos']['D001_Id'];
	if(empty($D001_Id)){
		return "<font style='color: var(--BGVermelho);'>Falta D001_Id</font>";
	}

	$sqlFornecedores = mysql_query("SELECT T241_Flag_Principal,T241_Flag_Homologado,IFNULL(D024_Flag_Contribuinte,0) as Flag_Contribuinte,D024_Nome_Empresa
									  FROM T241 
	                             LEFT JOIN D024 ON T241_D024_Id=D024_Id
	                                 WHERE T241_D001_Id={$D001_Id}");

	$totFornecedor = mysql_num_rows($sqlFornecedores);

	if ($totFornecedor>0) {
		$qteColunas = $totFornecedor/5;

		if ($qteColunas<1){
			$qteColunas=1;
		}
		
		$retorno = "<table style='border:none!important;padding:0!important;border-spacing:0!important;border-collapse:collapse!important;background:none!important;box-shadow:none!important;' cellspacing='0' onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Cadastrar Fornecedor', '/cad/cad002/content/cad002contentCadastroFornecedorCompras/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D001_Id']}'), [960,600]); return false;\">";

		$coluna = 1;

		while($fornecedores = mysql_fetch_assoc($sqlFornecedores)){

			if ($coluna==1){
				$retorno .= '<tr style="border:none!important;background:none!important;box-shadow:none!important;">';
			}

			$retorno .= '<td style="border:none!important;padding:1px 2px!important;background:none!important;box-shadow:none!important;vertical-align:top;">';

			$nomeFornecedor = $fornecedores['Flag_Contribuinte'].'-'.substr($fornecedores['D024_Nome_Empresa'], 0, 20);

			if($fornecedores['T241_Flag_Principal'] == 'S'){
				$retorno .= "<font style='color: var(--BGAzul);'>{$nomeFornecedor}</font>";
			} else if($fornecedores['T241_Flag_Homologado'] == 'N'){
				$retorno .= "<font style='color: var(--BGVermelho);'>{$nomeFornecedor}</font>";
			} else {
				$retorno .= "<font style='color: var(--letra2);'>{$nomeFornecedor}</font>";
			}

			$retorno .= '</td>';

			if ($coluna>=$qteColunas){
				$retorno .= '</tr>';
				$coluna = 1;
			} else {
				$coluna++;
			}
		}

		$retorno .= '</table>';

	} else {
		$retorno .= "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Cadastrar Fornecedor', '/cad/cad002/content/cad002contentCadastroFornecedorCompras/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['D001_Id']}'), [960,600]); return false;\"><font>INCLUIR</font></a>";
	}

	return $retorno;
}