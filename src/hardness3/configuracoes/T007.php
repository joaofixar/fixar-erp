<?php
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['T007_Serie'] = <<<'EOT'
	 $retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T007_Justificativa_Inutilizacao'] = <<<'EOT'
	 $retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T007_Flag_Almoxarifado'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T007_Flag_Impresso'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T007_Flag_ACP'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T007_Chave_Acesso_Nfe'] = <<<'EOT'
	$retorno = rDuplicidade($campo, $valor, $acaoId, 'Chave de acesso', true);
EOT;
$conf['restricaoIncluirUpdate']['T007_D027_Id']  = <<<'EOT'
	$retorno = rVerificaFK('D027_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T007_Cliente_CNPJ_CPF'] = <<<'EOT'
	global $r_fornecedor, $g;
	//$retorno = rValidarCpf($valor);
EOT;
$conf['restricaoIncluirUpdate']['T007_Flag_Indicador_Presenca'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T007']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['T007']['insertAfter'] = <<<'EOT'
	require_once('bibliotecas/classes/ETC017.php');
	$ETC017 = new ETC017();
	$ETC017->inserirRegistroTarefa($chaveValor, 'T007');
EOT;
$conf['triggers']['T007']['updateBefore'] = <<<'EOT'

	$sql = mysql_query("SELECT T007_D024_Id FROM T007 WHERE T007_Id = '{$chaveValor}'");
	$row = mysql_fetch_assoc($sql);	
	$g['updateBefore-T007_D024_Id'] = $row['T007_D024_Id'];

EOT;
$conf['triggers']['T007']['updateAfter'] = <<<'EOT'

	$sql = mysql_query("SELECT T007_D024_Id FROM T007 WHERE T007_Id = '{$chaveValor}'");
	$row = mysql_fetch_assoc($sql);	

	if($g['updateBefore-T007_D024_Id'] != $row['T007_D024_Id']){
		// Atualiza o crédito do cliente, teve casos que o valor estava desatualizado
		mysqli_query("CALL D024_Gravar_Totalizacao('{$row['T007_D024_Id']}')");
	}

	require_once('bibliotecas/classes/VEN001.php');
	$VEN001 = new VEN001();

	//calculo Frete
	$dadosTabela = array('tabela' => 'T007', 
						 'tabelaId' => 	$chaveValor,
						 'campos' => 'T007_Valor_Total as valorTotal,
                                     T007_Total_Peso as pesoTotal,
                                     T007_Peso_Bruto as valorBrutoTotal,
                                     T007_Total_Cubagem as totalCubagem');
	$valor_frete_calculado = $VEN001->calcularFrete($dadosTabela);

	$retorno = $VEN001->calcularComissaoTotal($chaveValor, 'T007');
	$upT007 = mysqli_query("UPDATE T007 SET T007_Percentual_Comissao = '{$retorno['percentualComissao']}',
											T007_Valor_Total_Comissao = '{$retorno['valorComissao']}',
											T007_Valor_Comissao_Representante = '{$retorno['valorComissaoRepresentante']}',
											T007_Valor_Total_Comissao_Externo = '{$retorno['valorComissaoExterno']}',
											T007_Valor_Total_Comissao_Usuario = '{$retorno['valorComissaoUsuario']}',
											T007_Valor_Frete_Calculado = '{$valor_frete_calculado}'
							WHERE T007_Id = '{$chaveValor}'");

	$T008=mysqli_query("select T008_Id from T008 where T008_T007_Id='$chaveValor'");
	while ($mT008=mysql_fetch_array($T008))
	{
	    mysqli_query("call T008_Gravar_Totalizacao_4('{$mT008['T008_Id']}')");
	    mysqli_query("UPDATE T007 SET T007_D006_Id='{$mT008['T008_D006_Id']}' WHERE T007_Id = '{$chaveValor}' AND (T007_D006_Id IS NULL OR T007_D006_Id <= 0)");
	}
	mysqli_query("call T007_Gravar_Totalizacao_4('$chaveValor')");

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT007();
EOT;
$conf['triggers']['T007']['deleteBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T007']['deleteAfter'] = <<<'EOT'
EOT;

$conf['restricaoTravar']['T007'] = <<<'EOT'
	global $g;
	$retorno = true;
	if ($operacao == 'alterar') {
		if (($campo=='T007_C007_Id_Vendedor_Interno' && $g['nivelAlcada'] == '4') || ($campo=='T007_C007_Id_Vendedor_Externo' && $g['nivelAlcada'] == '4') || ($campo == 'T007_Percentual_Comissao' && isset($g['c029Ids'][201])) || ($campo == 'T007_Percentual_Comissao_Representante' && isset($g['c029Ids'][202])) || $campo == 'T007_Codigo_Rastreio' || ($campo == 'T007_D021_Id' && isset($g['c029Ids'][202])) || ($campo == 'T007_Data_Entrega')){
			$retorno = true;
		}else{
			$sqlT007 = "SELECT T007_Numero_Protocolo_Nfe, T007_Flag_Analise_Credito, T007_Flag_Travar_Nota FROM T007 WHERE T007_Id='{$valorPk}'";
			$resultT007 = mysql_query($sqlT007);
			$linhaT007 = mysql_fetch_array($resultT007);
			if (!empty($linhaT007['T007_Numero_Protocolo_Nfe'])) {
				$retorno = 'Nota já transmitida. Não é possível alterar.';
			} else {
				if ($linhaT007['T007_Flag_Travar_Nota'] == 'S') {
					$retorno = 'Nota já finalizada, para altera-lá utilize o botão "Habilitar edição"';
					if($campo == 'T007_Chave_Acesso_Nfe' || $campo == 'T007_Numero_Recibo_Nfe' || $campo == 'T007_Numero_Nota_Fiscal' || $campo == 'T007_Serie' || $campo == 'T007_Flag_Almoxarifado' || $campo == 'T007_Flag_Impresso' || $campo == 'T007_Versao_NFe' || $campo == 'T007_TX_Resposta_Codigo' ){
						$retorno = true;
					}
				} else if ($linhaT007['T007_Flag_Analise_Credito'] == 1) {
					$retorno = 'Nota está aguardando liberação.';
				} else if ($linhaT007['T007_Flag_Analise_Credito'] == 2) {
					$retorno = 'Nota foi liberada pela análise de crédito.';
				}
			}
		}
	} else {
		$retorno = true;
	}
EOT;
