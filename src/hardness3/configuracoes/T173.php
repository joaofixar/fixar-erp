<?php
$conf['triggers']['T173']['updateAfter'] = <<<'EOT'
	$T173=mysqli_query("select * from T173 where T173_Id='$chaveValor'");
	$mT173=mysql_fetch_array($T173);
	$frete=$mT173['T173_Frete'];
	$desconto=$mT173['T173_Desconto'];
	$icms=$mT173['T173_ICMS'];
	$T174=mysqli_query("select * from T174 where T174_T173_Id='$chaveValor'");
	while ($mT174=mysql_fetch_assoc($T174)){
		$subTotal=$mT174['T174_Quantidade']*$mT174['T174_Valor_Custo_Unitario'];
		$ipi=$mT174['T174_IPI'];
		if ($desconto>0 || $icms>0 || $ipi>0 || $frete>0)
			$subTotal=$subTotal*(1-(($desconto+$icms-$ipi-$frete)/100));									
		mysqli_query("UPDATE T174 SET T174_Valor_Total=$subTotal WHERE T174_Id='{$mT174['T174_Id']}'");		
	}	
EOT;

$conf['triggers']['T173']['deleteBefore'] = <<<'EOT'
	mysqli_query("DELETE FROM T174 WHERE T174_T173_Id='{$chaveValor}'");
EOT;
$conf['restricaoIncluirUpdate']['T173_D024_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D024_Id', $valor);
EOT;

$conf['restricaoTravar']['T173'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir' || $operacao == 'incluir') {
		if(!empty($valorPk)){
			$sqlT173 = "SELECT * FROM T173 WHERE T173_Id = '{$valorPk}'";
			$resultT173 = mysql_query($sqlT173);
			$linhaT173 = mysql_fetch_array($resultT173);
			
			//Verificando se já foi gerado uma ordem de compra para aquela cotação
			$sqlT011 = "SELECT * FROM T011 WHERE T011_T010_Id='{$linhaT173['T173_T010_Id']}' AND T011_D024_Id='{$linhaT173['T173_D024_Id']}'";
			$resultT011 = mysql_query($sqlT011);
			$totalT011 = mysql_num_rows($resultT011);
			$linhaT011 = mysql_fetch_array($resultT011);
			if($totalT011 > 0){
				$retorno = "Já foi gerada uma OC para esta cotação. Não é possível alterar";
			}
			
			// Verificando se a cotação já está finalizada
			//$sqlT010 = "SELECT T010_Flag_Cotacao_Finalizada FROM T010 WHERE T010_Id='{$linhaT173['T173_T010_Id']}'";
//			$resultT010 = mysql_query($sqlT010);
//			$linhaT010 = mysql_fetch_array($resultT010);
//			if ($linhaT010['T010_Flag_Cotacao_Finalizada'] == 'S') {
//				$retorno = 'Cotação Finalizada.';
//			}
		} else {
			//$sqlT010 = "SELECT T010_Flag_Cotacao_Finalizada FROM T010 WHERE T010_Id='{$valorPkParent}'";
//			$resultT010 = mysql_query($sqlT010);
//			$linhaT010 = mysql_fetch_array($resultT010);
//			if ($linhaT010['T010_Flag_Cotacao_Finalizada'] == 'S') {
//				$retorno = 'Cotação Finalizada.';
//			}
		}
		
	} else {
		$retorno = true;
	}
EOT;
?>