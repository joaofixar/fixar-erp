<?php
/**
 * Tabela T284
 */
global $idFormDecImport;
$conf['restricaoIncluirUpdate']['T284_Valor_Carga'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T284_Peso_Bruto_Total'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T284_Chave_De_Acesso_NFe'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T284_D018_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T284_D020_Id'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;

$conf['triggers']['T284']['updateAfter'] = <<<'EOT'
	log("UPDATE AFTER");
	mysql_query("call T276_Gravar_Totalizacao('$valorPkParent')");
EOT;

$conf['triggers']['T284']['insertAfter'] = <<<'EOT'
	log("INSERT AFTER");
	mysql_query("call T276_Gravar_Totalizacao('$valorPkParent')");

EOT;

$conf['restricaoTravar']['T284'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {

		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada 
		                            FROM T284
		                            LEFT JOIN T276 ON T276_Id=T284_T276_Id
		                            WHERE T284_Id='{$valorPk}'");
		$mT276 = mysql_fetch_assoc($T276);

    } if (($operacao == 'alterar' && !empty($valorPkParent)) || $operacao == 'incluir') {
		
		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, 
		                            T276_Flag_Travar_MDFe, 
		                            T276_Numero_Protocolo_Encerramento_MDFe, 
		                            T276_Flag_Cancelada FROM T276 WHERE T276_Id='{$valorPkParent}'");
		$mT276 = mysql_fetch_assoc($T276);
	} 

	if ($mT276['T276_Flag_Cancelada']=='S' && $operacao == 'excluir') {

		$retorno = true;

	} else if ($mT276['T276_Flag_Cancelada']=='S') {

		$retorno = 'MDF-e está cancelado!';

	} else if ($mT276['T276_Flag_Travar_MDFe'] == 'S') {
		
		if (!empty($mT276['T276_Numero_Protocolo_MDFe'])) {

			$retorno = utf8_encode('MDF-e já enviado, não é possível alterar.');
			
			if(!empty($mT276['T276_Numero_Protocolo_Encerramento_MDFe'])){
				$retorno = utf8_encode('MDF-e já encerrado, não é possível alterar.');
			} else {
				$retorno = utf8_encode('MDF-e já enviado, não é possível alterar.');
			}

		} else {

			$retorno = utf8_encode('MDF-e já finalizado, para altera-lo utilize o botão "Habilitar edição"');
			
		}
	}

EOT;
