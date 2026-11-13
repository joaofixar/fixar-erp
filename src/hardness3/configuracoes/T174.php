<?php
$conf['triggers']['T174']['updateAfter'] = <<<'EOT'
	$T174=mysqli_query("select * from T174 where T174_Id='$chaveValor'");
	$mT174=mysql_fetch_array($T174);
	
	$T173=mysqli_query("select * from T173 where T173_Id='{$mT174['T174_T173_Id']}'");
	$mT173=mysql_fetch_array($T173);
	$frete=$mT173['T173_Frete'];
	$desconto=$mT173['T173_Desconto'];
	$icms=$mT173['T173_ICMS'];	
	
	$subTotal=$mT174['T174_Quantidade']*$mT174['T174_Valor_Custo_Unitario'];
	$ipi=$mT174['T174_IPI'];
	if ($desconto>0 || $icms>0 || $ipi>0 || $frete>0)
		$subTotal=$subTotal*(1-(($desconto+$icms-$ipi-$frete)/100));						
	mysqli_query("UPDATE T174 SET T174_Valor_Total=$subTotal WHERE T174_Id='$chaveValor'");		
		
EOT;

$conf['restricaoTravar']['T174'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {
		$sqlT174 = "SELECT * FROM T174 LEFT JOIN T173 ON T173_Id=T174_T173_Id WHERE T174_Id = '{$valorPk}'";
		$resultT174 = mysql_query($sqlT174);
		$linhaT174 = mysql_fetch_array($resultT174);
		
		//Verificando se já foi gerado uma ordem de compra para aquela cotação
		$sqlT011 = "SELECT * FROM T011 WHERE T011_T010_Id='{$linhaT174['T174_T010_Id']}' AND T011_D024_Id='{$linhaT174['T173_D024_Id']}'";
		$resultT011 = mysql_query($sqlT011);
		$totalT011 = mysql_num_rows($resultT011);
		$linhaT011 = mysql_fetch_array($resultT011);
		if($totalT011 > 0){
			$retorno = "Já foi gerada uma OC para esta cotação. Não é possível alterar";
		}
		
		// Verificando se a cotação já está finalizada.
		//$sqlT010 = "SELECT T010_Flag_Cotacao_Finalizada FROM T010 WHERE T010_Id='{$linhaT174['T174_T010_Id']}'";
//		$resultT010 = mysql_query($sqlT010);
//		$linhaT010 = mysql_fetch_array($resultT010);
//		if ($linhaT010['T010_Flag_Cotacao_Finalizada'] == 'S') {
//			$retorno = 'Cotação Finalizada.';
//		}
	} else {
		$retorno = true;
	}
EOT;
$conf['triggers']['T174']['insertAfter'] = <<<'EOT'
	mysqli_query("UPDATE T174 SET T174_IPV='{$g['conf']->ini('T174IpvPadrao')}' WHERE T174_Id='{$chaveValor}'");
EOT;
$conf['triggers']['T174']['updateAfter'] = <<<'EOT'
	$sql = mysql_query("SELECT * FROM T174 WHERE T174_Id='{$chaveValor}'");
	$mT174 = mysql_fetch_array($sql);
	$total = $mT174['T174_Valor_Custo_Unitario'] * $mT174['T174_IPV'];
	
	mysqli_query("UPDATE T174 SET T174_Preco_Venda='{$total}', T174_Valor_Total='{$total}' WHERE T174_Id='{$chaveValor}'");
EOT;
$conf['camposTipo']['T174_IPV'] = array('numero', 3);
?>