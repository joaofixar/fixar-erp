<?php
namespace hardness;
/*
 * contém os callbacks utilizados no pesquisaGrid
 */

 function validaCampoStatus($array) {
	if ($array['todosCampos']['D106_Flag_Status'] == 'A'){
	return "Ativo";
	}
	if ($array['todosCampos']['D106_Flag_Status'] == 'I'){
		return "Inativo";
	}
}

// utilizado em:
// /ven001/form/ven001FormProdutos/
function validaCampoEstoque($array, $casasDecimais = 2){
	global $g;
	$sql = "SELECT * FROM D009 WHERE D009_Id='{$array['todosCampos']['D009_Id']}'";
	$result = mysql_query($sql);
	$linha = mysql_fetch_array($result);
	if($linha['D009_Quantidade_Estoque_Liquido']<0){
		return "<font color='red'><b>" . gCorrigeNumero($linha['D009_Quantidade_Estoque_Liquido'], $casasDecimais) . "</b></font>";
	} else {
		return "<b>" . gCorrigeNumero($linha['D009_Quantidade_Estoque_Liquido'], $casasDecimais) . "</b>";
	}
}
function campoLinkProdutoPesquisaGrid($array){
			$sql = mysql_query("SELECT D001_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id
					LEFT JOIN D001 ON D001_Id=D049_D001_Id
					LEFT JOIN T144 ON T144_D001_Id=D001_Id WHERE D009_Id='{$array['todosCampos']['D009_Id']}'");
			$row = mysql_fetch_array($sql);
			$idProduto = $row['D001_Id'];

			$sql = mysql_query("SELECT T144_Url FROM T144 WHERE T144_D001_Id='{$idProduto}'");
			$row = mysql_fetch_array($sql);
			$url = $row['T144_Url'];

			if(!empty($url)){
				$retorno = "<a href=\"{$url}\" target=\"_blank\">Ver Produto</a>";
			}else{
				$retorno = "Não cadastrado";
			}
			return $retorno;
}

function validaEstoqueMP($array){
	global $g;
	$diasMP=$g['C031']['diasMedioPrazo'];
	$data = strtotime("+$diasMP day", strtotime('now'));
	$dataMP = date("Y-m-d", $data);	
	
	$retorno=mysql_query("select sum(T055_Quantidade) as QteOC from T055 left join T075 on T075_Id=T055_T075_Id where T055_D009_Id={$array['todosCampos']['D009_Id']} AND T075_Data_Estoque='0000-00-00' AND T075_Data_Previsao<='{$dataMP}' and T075_Data_Previsao>0 AND T075_Flag_MP = 1");        
	$retorno=mysql_fetch_array($retorno);

    $qteOC=$retorno['QteOC'];
	
	$retorno=mysql_query("SELECT T075_Id FROM T075 LEFT JOIN T055 ON T075_Id=T055_T075_Id LEFT JOIN D009 ON T055_D009_Id=D009_Id WHERE T055_D009_Id='{$array['todosCampos']['D009_Id']}' and T075_Data_Previsao<='{$dataMP}' AND T075_Flag_MP = 1 group by T075_Id");
	$qtePed=0;
	while ($ret=mysql_fetch_array($retorno)){
		$retorno1=mysql_query("select sum(T006_Quantidade) 
				from T006 
				left join T005 on T005_Id=T006_T005_Id 
				where T005_Flag_Status!=5
			    and T006_Flag_Status!='3'
			    and T005_Flag_Status!='7'
			    and T005_Flag_Status!='8'
			    and T005_Flag_Status!='11'
				and T006_T075_Id='{$ret['T075_Id']}'
				and T006_D009_Id='{$array['todosCampos']['D009_Id']}'");
		$ret1=mysql_fetch_array($retorno1);
		$qtePed+=$ret1[0];
	}
	$liquido=number_format($qteOC-$qtePed,2);    
	return $liquido;    
}
