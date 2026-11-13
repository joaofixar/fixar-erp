<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['D006_Flag_Devolucao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Departamento'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Inativo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Deb_Crd_ICMS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Credita_IPI'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Forma_Calculo_PIS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Situacao_Tributaria_PIS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Forma_Calculo_COFINS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Situacao_Tributaria_COFINS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Codigo_CFOP'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Descricao'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Entrada_Saida'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Estado'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Venda_Compra_Outros'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Gera_Contas'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_MMF'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Atualiza_Custo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Estoque'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Retorno'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Isento_ICMS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Somar_IPI_Base_ICMS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Somar_Despesas_Base_IPI'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_D059_Id'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_D059_Id'] = <<<'EOT'
	if ($g['c020']['C020_Versao_NFe'] == '1.10') {
		if ($valor == 12 || $valor == 13 || $valor == 14) {
			$retorno = "* * * * A Situação Tributária que foi selecionada só é válida para NF-e versão 2.00 * * * * Por favor, selecione outra situação tributária, pois essa alteração NÃO foi efetuada.";
		}
	}
	$retorno = true;
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Isento_IPI'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Deb_Crd_IPI'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
// $conf['restricaoIncluirUpdate']['D006_Flag_Substituicao_Tributaria'] = <<<'EOT'
// 	$retorno = rRegx('naoVazio', $valor);
// EOT;
// $conf['restricaoIncluirUpdate']['D006_Flag_ST_VA'] = <<<'EOT'
// 	$retorno = rRegx('naoVazio', $valor);
// EOT;
$conf['restricaoIncluirUpdate']['D006_Flag_Isento_PIS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
/*$conf['restricaoIncluirUpdate']['D006_Situacao_Tributaria_PIS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;*/
$conf['restricaoIncluirUpdate']['D006_Flag_Isento_COFINS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
/*$conf['restricaoIncluirUpdate']['D006_Situacao_Tributaria_COFINS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;*/
$conf['restricaoIncluirUpdate']['D006_Flag_Isento_PIS'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D006_D014_Id'] = <<<'EOT'
	$retorno = true;
	if($valores['D006_Flag_Gera_Contas'] == 'P' || $valores['D006_Flag_Gera_Contas'] == 'R'){
		$retorno = rRegx('naoVazio', $valor);
	}
EOT;
$conf['restricaoIncluirUpdate']['D006_D073_Id'] = <<<'EOT'
	$retorno = true;
	if($valores['D006_Flag_Gera_Contas'] == 'P'){
		$retorno = rRegx('naoVazio', $valor);
	}
EOT;

/**
* triggers
*/
$conf['triggers']['D006']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['D006']['insertAfter'] = <<<'EOT'
	$sql="select D006_Flag_Venda_Compra_Outros,D006_Flag_Entrada_Saida from D006 where D006_Id ='$chaveValor'";
	$resultado=mysqli_query($sql);
	$row=mysql_fetch_array($resultado);
	if($row['D006_Flag_Venda_Compra_Outros']=='V'){
		$Tipo_CFOP="VENDA";
	}
	else if($row['D006_Flag_Venda_Compra_Outros']=='C'){
		$Tipo_CFOP="COMPRA";
	}
	else if($row['D006_Flag_Venda_Compra_Outros']=='T'){
		$Tipo_CFOP="TRANSFERÊNCIA";
	}
	else if($row['D006_Flag_Venda_Compra_Outros']=='O'){
		if($row['D006_Flag_Entrada_Saida']=='S'){
			$Tipo_CFOP="OUTRAS SAIDAS";
		}
		else if($row['D006_Flag_Entrada_Saida']=='E'){
			$Tipo_CFOP="OUTRAS ENTRADAS";
		}
	}
	else if($row['D006_Flag_Venda_Compra_Outros']=='DV'){
		$Tipo_CFOP="DEVOLUÇÃO VENDA";
	}
	else if($row['D006_Flag_Venda_Compra_Outros']=='DC'){
		$Tipo_CFOP="DEVOLUÇÃO COMPRA";
	}
	mysqli_query("update D006 
	                set D006_Tipo_CFOP='{$Tipo_CFOP}'
	              where D006_Id='$chaveValor'");
EOT;
$conf['triggers']['D006']['updateBefore'] = <<<'EOT'
EOT;
$conf['triggers']['D006']['updateAfter'] = <<<'EOT'
	$sql="select D006_Flag_Venda_Compra_Outros,D006_Flag_Entrada_Saida from D006 where D006_Id ='$chaveValor'";
	$resultado=mysqli_query($sql);
	$row=mysql_fetch_array($resultado);
	if($row['D006_Flag_Venda_Compra_Outros']=='V'){
		$Tipo_CFOP="VENDA";
	}
	else if($row['D006_Flag_Venda_Compra_Outros']=='C'){
		$Tipo_CFOP="COMPRA";
	}
	else if($row['D006_Flag_Venda_Compra_Outros']=='T'){
		$Tipo_CFOP="TRANSFERÊNCIA";
	}
	else if($row['D006_Flag_Venda_Compra_Outros']=='O'){
		if($row['D006_Flag_Entrada_Saida']=='S'){
			$Tipo_CFOP="OUTRAS SAIDAS";
		}
		else if($row['D006_Flag_Entrada_Saida']=='E'){
			$Tipo_CFOP="OUTRAS ENTRADAS";
		}
	}
	else if($row['D006_Flag_Venda_Compra_Outros']=='DV'){
		$Tipo_CFOP="DEVOLUÇÃO VENDA";
	}
	else if($row['D006_Flag_Venda_Compra_Outros']=='DC'){
		$Tipo_CFOP="DEVOLUÇÃO COMPRA";
	}
	mysqli_query("update D006 
	                set D006_Tipo_CFOP='{$Tipo_CFOP}'
	              where D006_Id='$chaveValor'");
EOT;
$conf['triggers']['D006']['deleteBefore'] = <<<'EOT'
EOT;
$conf['triggers']['D006']['deleteAfter'] = <<<'EOT'
EOT;
?>