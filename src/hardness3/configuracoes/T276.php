<?php
/**
 * Tabela T276
 */
global $idFormDecImport;
$conf['restricaoIncluirUpdate']['T276_Serie'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T276_Versao_MDFe'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T276_TX_Resposta_Codigo'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T267_Data_Abertura'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T276_Emitente_Nome_Fantasia'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T276_Flag_Tipo_Emissao'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T276_Flag_Modalidade_Transporte'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T276_D018_Id_Carregamento'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T276_D020_Id_Carregamento'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
/*$conf['restricaoIncluirUpdate']['T276_D018_Id_Descarregamento'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;*/
$conf['restricaoIncluirUpdate']['T276_Data_Inicio_Viagem'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T276_Data_Emissao'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['triggers']['T276']['updateAfter'] = <<<'EOT'
	mysqli_query("call T276_Gravar_Totalizacao('$valorPk')");
EOT;

$conf['triggers']['T276']['insertAfter'] = <<<'EOT'
	mysqli_query("call T276_Gravar_Totalizacao('$valorPk')");
EOT;
$conf['restricaoTravar']['T276'] = <<<'EOT'
	global $g;

	$retorno = true;
	
	if ($operacao == 'alterar' || $operacao == 'incluir') {
		
		$T276 = mysql_query("SELECT T276_Numero_Protocolo_MDFe, T276_Flag_Travar_MDFe, T276_Numero_Protocolo_Encerramento_MDFe, T276_Flag_Cancelada FROM T276 WHERE T276_Id='{$valorPk}'");
		$mT276 = mysql_fetch_assoc($T276);
		
		if ($mT276['T276_Flag_Cancelada']=='S') {

			$retorno = utf8_encode('MDF-e est� cancelado!');

		} else if ($mT276['T276_Flag_Travar_MDFe'] == 'S') {

		    if (!empty($mT276['T276_Numero_Protocolo_MDFe'])) {

				if(!empty($mT276['T276_Numero_Protocolo_Encerramento_MDFe'])){
					$retorno = utf8_encode('MDF-e j� encerrado, n�o � poss�vel alterar.');
				} else {
					$retorno = true;
				}

			} else {

				$retorno = utf8_encode('MDF-e j� finalizado, para altera-l� utilize o bot�o "Habilitar edi��o"');
				
			}

		}
	
	}

EOT;
