<?php
/**
 * Tabela T002

 */
 
$conf['restricaoIncluirUpdate']['T002_C004_Id'] = <<<'EOT'
	$retorno = rVerificaFK('C004_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T002_D014_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D014_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T002_D032_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D032_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T002_D027_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D027_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T002_Numero_Duplicata'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T002_Data_Emissao'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T002_Data_Vencimento'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T002_Valor_Duplicata'] = <<<'EOT'
	$retorno = rNumeroMaiorQueZero($valor);
EOT;
$conf['camposTipo']['T002_Data_Vencimento'] = array('data');

/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T002']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['T002']['insertAfter'] = <<<'EOT'
	require_once('bibliotecas/classes/FIN009.php');
	$FIN009 = new FIN009();
	$sql = mysqli_query("SELECT T002_D014_Id, T002_Data_Vencimento, T002_Valor_Duplicata, D027_Taxa, D027_Descricao_Taxa FROM T002 LEFT JOIN D027 ON D027_Id=T002_D027_Id WHERE T002_Id = '{$chaveValor}'");
	$row = mysql_fetch_array($sql);

	//Inclusao da taxa do portador
	if($row['D027_Taxa'] > 0){
		$valorDescontoTaxa = ($row['D027_Taxa']/100) * $row['T002_Valor_Duplicata'];
		mysqli_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Data_Lancamento, T020_Historico, T020_Valor, T020_Flag_Deb_Cred) VALUES ('{$chaveValor}', '{$g['empresaAtual']}', CURDATE(), '{$row['D027_Descricao_Taxa']}', '{$valorDescontoTaxa}', 'T')");
		$T020_Id = mysql_insert_id();

		$T162_Extra = "Trigger PHP - Tabela T002";
		$camposAlterados = array();
		$camposAlterados = mysql_real_escape_string(serialize($camposAlterados));
		mysqli_query("INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$g['usuarioAtual']}', 'I', '{$T162_Extra}', '" . date('Y-m-d H:i:s') . "', 'T020', '{$T020_Id}', COMPRESS('{$camposAlterados}'), '{$_SERVER['REMOTE_ADDR']}');",$g['conexaoBancoAuditoria']);
	}

	mysqli_query("call T002_Gravar_Totalizacao('$chaveValor')");
	mysqli_query("call T002_Baixar_Titulo('$chaveValor','".date('Y-m-d')."')");

	$mes = date("m",strtotime($row['T002_Data_Vencimento']));
	$ano = date("Y",strtotime($row['T002_Data_Vencimento']));
	$periodo = array('mes' => $mes, 'mes_final' => $mes, 'ano_atual' => $ano, 'ano' => $ano, 'ano_final' => $ano);
	$FIN009->FIN009CalculaProjecao($row['T002_D014_Id'], $periodo, "{$chaveValor}");

	// Insere um registro na tabela estendida
	mysqli_query("INSERT INTO T002A (T002A_T002_Id) VALUES ('{$chaveValor}')");

EOT;

$conf['triggers']['T002']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T002']['updateAfter'] = <<<'EOT'
	mysqli_query("call T002_Gravar_Totalizacao('$chaveValor')");
	mysqli_query("call T002_Baixar_Titulo('$chaveValor','".date('Y-m-d')."')");
	require_once('bibliotecas/classes/FIN009.php');
	$FIN009 = new FIN009();
	$sql = mysqli_query("SELECT T002_D014_Id, T002_Data_Vencimento FROM T002 WHERE T002_Id = '{$chaveValor}'");
	$row = mysql_fetch_array($sql);
	$mes = date("n",strtotime($row['T002_Data_Vencimento']));
	$ano = date("Y",strtotime($row['T002_Data_Vencimento']));
	$periodo = array('mes' => $mes, 'mes_final' => $mes, 'ano_atual' => $ano, 'ano' => $ano, 'ano_final' => $ano);
	$FIN009->FIN009CalculaProjecao($row['T002_D014_Id'], $periodo, "{$chaveValor}");
	/*
	// CALCULO COMISSÃO
	require_once('bibliotecas/classes/VEN001.php');
	$VEN001  = new VEN001();
	$retorno = $VEN001->calcularComissaoTotal($chaveValor, 'T002');
	$upT002  = mysqli_query("UPDATE T002 SET T002_Valor_Comissao_Representante = '{$retorno['valorComissaoRepresentante']}',
											 T002_Valor_Total_Comissao_Externo = '{$retorno['valorComissaoExterno']}',
											 T002_Valor_Total_Comissao_Usuario = '{$retorno['valorComissaoUsuario']}',
											 T002_Percentual_Comissao          = '{$retorno['percentualComissao']}',
											 T002_Valor_Comissao               = '{$retorno['valorComissao']}'
							           WHERE T002_Id = '{$chaveValor}'");
						 
	// Verifica se existe um registro na tabela estentdida, se não houver faz o insert, se houver faz o UPDATE
	$T002A = mysqli_query("SELECT T002A_Id FROM T002A WHERE T002A_T002_Id = '{$chaveValor}'");
	if(mysql_num_rows($T002A) <=0){
		log("insert T002A");
		mysqli_query("INSERT INTO T002A (T002A_T002_Id, 
		 								 T002A_Percentual_Comissao_Supervisor,
										 T002A_Valor_Total_Comissao_Supervisor)
								  VALUES 
								  		('{$chaveValor}',
										 '{$retorno['percentualComissaoSupervisor']}',
										 '{$retorno['valorComissaoSupervisor']}')");

	} else {	
		log("update T002A");

		$upT002A = mysqli_query("UPDATE T002A 
							  LEFT JOIN T002 ON T002A_T002_Id = T002_Id
									SET T002A_Percentual_Comissao_Supervisor  = '{$retorno['percentualComissaoSupervisor']}',
										T002A_Valor_Total_Comissao_Supervisor = '{$retorno['valorComissaoSupervisor']}'
							      WHERE T002A_T002_Id = '{$chaveValor}'");	
	}
	// FIM CALCULO COMISSÃO	
	*/
	
EOT;

$conf['triggers']['T002']['deleteBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T002']['deleteAfter'] = <<<'EOT'
EOT;

$conf['restricaoTravar']['T002'] = <<<'EOT'
	if(!isset($g['c029Ids'][190])){
		$retorno = "Não possui permissão.";
		return false;
	}

	$retorno = true;
	if ($operacao == 'alterar') {
		$sqlT002 = "SELECT T002_Flag_Cancelada, IFNULL(T002_T002_Id_Agrupado,0) T002_T002_Id_Agrupado FROM T002 WHERE T002_Id='{$valorPk}'";
		$resultT002 = mysql_query($sqlT002);
		$linhaT002 = mysql_fetch_array($resultT002);
		
		if ($linhaT002['T002_Flag_Cancelada']=='S') {
			$retorno = 'Título cancelado, não é possível alterar.';
		}
		if($linhaT002['T002_T002_Id_Agrupado'] > 0){
			$retorno = 'Título reparcelado, não é possível alterar.';	
		}

	} else {
		$retorno = true;
	}
EOT;

?>