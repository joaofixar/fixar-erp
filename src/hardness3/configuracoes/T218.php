<?php
/**
 * Tabela T218
 */
/**
* camposNaoUsar
* Lista de campos que não serão utilizados. (No FORM, GRID, etc.)
*/
if($g['C031']['usarDescontoProdutos'] == 'N'){
$conf['camposNaoUsar'] = array_merge($conf['camposNaoUsar'], array(
	'T218_Valor_Desconto', 
	'T218_Valor_Desconto_Confirmado',
	'T218_Valor_Total_Produtos_Sem_Desconto',
	'T218_Valor_Total_Produtos_Sem_Desconto_Confirmado' 	
));
}
$conf['restricaoIncluirUpdate']['T218_Data_Emissao'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T218_D024_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D024_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['D001_Codigo_Produto'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T218_C007_Id_Vendedor_Interno'] = <<<'EOT'
	$retorno = rVerificaFK('C007_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T218_D006_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D006_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T218_Flag_Lista_Preco'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T218_Flag_Acp'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T218_D013_Id'] = <<<'EOT'
        $retorno = rRegx('naoSelecionado', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T218_D024_Id'] = <<<'EOT'
	$retorno = '';
	if (empty($valor)) {
		$retorno = 'Selecione um Fornecedor';
	} else {
		$result = mysql_query("SELECT D024_Id, D024_Flag_Contribuinte, D024_Flag_Lista_Preco from D024 WHERE D024_Id='{$valor}'");
		$D024 = mysql_fetch_assoc($result);
		$D024['D024_Flag_Lista_Preco'] = trim($D024['D024_Flag_Lista_Preco']);
		if (empty($retorno)) {
			$retorno = true;
		}
	}
EOT;

/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T218']['insertAfter'] = <<<'EOT'
	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT218();
EOT;

$conf['triggers']['T218']['updateBefore'] = <<<'EOT'
	$sql = mysql_query("SELECT T218_Valor_Frete FROM T218 WHERE T218_Id = '{$chaveValor}'");
	$row = mysql_fetch_assoc($sql);
	$g['updateBefore-T218_Valor_Frete'] = $row['T218_Valor_Frete'];
EOT;

$conf['triggers']['T218']['updateAfter'] = <<<'EOT'
	$T219=mysql_query("select T219_Id from T219 where T219_T218_Id='$chaveValor'");
	while ($mT219=mysql_fetch_array($T219)){
		mysql_query("CALL T219_Gravar_Totalizacao('$mT219[T219_Id]')");
	}
	$sql = "SELECT T218_Flag_Perdido,
				   T218_Flag_Status_Orcamento,
				   T224_Id,
				   T218_Valor_Frete 
			  FROM T218 
		 left join T224 on T224_T218_Id=T218_Id 
		     WHERE T218_Id = '{$chaveValor}'";

	$resultado = mysqli_query($sql);
	$row = mysql_fetch_assoc($resultado);

	if (($row['T218_Flag_Status_Orcamento'] != 'N') && ($row['T218_Flag_Status_Orcamento'] != 'A') && ($row['T218_Flag_Status_Orcamento'] != 'L')) {
		if (empty($row['T224_Id'])) {
			$sql = "UPDATE T218 SET T218_Flag_Status_Orcamento = 'P' WHERE T218_Id = '{$chaveValor}'";
			mysqli_query($sql);
			if($row['T218_Flag_Perdido'] == 'S'){
				$sql = "UPDATE T218 SET T218_Flag_Status_Orcamento = 'C' WHERE T218_Id = '{$chaveValor}'";
				mysqli_query($sql);
			}
		} else { 
			if ($row['T218_Flag_Perdido'] == 'S') {
				$sql = "UPDATE T218 SET T218_Flag_Status_Orcamento = 'C' WHERE T218_Id = '{$chaveValor}'";
				mysqli_query($sql);
			} else {
				$sql = "UPDATE T218 SET T218_Flag_Status_Orcamento = 'F' WHERE T218_Id = '{$chaveValor}'";
				mysqli_query($sql);
			}
		}
	}elseif($row['T218_Flag_Status_Orcamento'] == 'A' && $row['T218_Flag_Perdido'] == 'F'){
		$sql = "UPDATE T218 SET T218_Flag_Status_Orcamento = 'F' WHERE T218_Id = '{$chaveValor}'";
		mysqli_query($sql);
	}

 	if($row['T218_Valor_Frete'] != $g['updateBefore-T218_Valor_Frete']){
		// Se vir de um orcamento ou pedido, verifica o valor do frete, pois vai compor o valor do preco final da venda
		require_once('bibliotecas/classes/EST015.php');
		$EST015 = new EST015();

		$T219 = mysqli_query("SELECT T219_Id,
									 T219_T004_Id, 
									 T219_T006_Id
				   		        FROM T219
					           WHERE T219_T218_Id = '{$chaveValor}'
					             AND (T219_T004_Id > 0 OR T219_T006_Id > 0)");
		while($mT219 = mysql_fetch_Array($T219)){
			$EST015->atualizarPrecoNegociado($mT219['T219_Id'], $mT219['T219_T004_Id'], $mT219['T219_T006_Id']);

		}
	}

	mysql_query("call T218_Gravar_Totalizacao_3('$chaveValor')");

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT218();
EOT;

/**
* restricaoTravar
* Código PHP para validar se uma tabela pode: ser editada, incluida ou removida
* Para ver variáveis e funções disponíveis dentro do código, veja:
*	http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
$conf['restricaoTravar']['T218'] = <<<'EOT'
	$retorno = true;
	if ($operacao == 'alterar' || $operacao == 'excluir') {
		$sql = "SELECT T224_Id FROM T224 WHERE T224_T218_Id = '{$valorPk}'";
		$res = mysql_query($sql);
		$rows = mysql_numrows($res);
		if ($rows > 0) {
			$retorno = 'Este cotação já gerou ordem de compra.';
		}
	} else {
		$retorno = true;
	}

	if($campo == 'T218_Grupo'){
        $retorno = true;
    }
EOT;

/**
* trocarEmpresa
*/
$conf['trocarEmpresa']['exibir'] = <<<'EOT'
	// Variáveis disponíveis: $r_tabela, $r_acaoId
	// Retornar 'true' irá ativar o ícone para trocar a empresa
	$sql = <<<SQL
		SELECT 
			T224_Id 
		FROM T218
		left join T224 on T224_T218_Id=T218_Id AND T224_Flag_Status!='8'
		WHERE
			T218_Id = '{$r_acaoId}'
		group by T218_Id
SQL;
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if (empty($row['T224_Id'])) {
		return true;
	}
EOT;
$conf['trocarEmpresa']['tabelaFilho'] = 'T219';
$conf['trocarEmpresa']['executarNoFinal'] = <<<'EOT'
	// Variáveis disponíveis: $C004_Id_Novo, $Tabela, $Id
EOT;
