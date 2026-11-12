<?php
 
/**
* restricaoIncluirUpdate
*/
$conf['restricaoIncluirUpdate']['T224_Data_Saida'] = <<<'EOT'
$retorno = rRegx('data', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T224_Observacao_Fatura_Emergencia'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;

$conf['restricaoIncluirUpdate']['T224_Flag_ACP'] = <<<'EOT'
	$retorno = rRegx('naoSelecionado', $valor);
EOT;


/**
* triggers
*/
$conf['triggers']['T224']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['T224']['insertAfter'] = <<<'EOT'
	mysqli_query("call T224_Gravar_Totalizacao_3('$chaveValor')");

	mysqli_query("UPDATE T224 SET T224_Prazos_Pagamentos = T224_Prazos(T224_Id) WHERE T224_Id = '{$chaveValor}'");



	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT224();
EOT;
$conf['triggers']['T224']['updateBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T224']['updateAfter'] = <<<'EOT'
	$T225=mysql_query("select T225_Id from T225 where T225_T224_Id='$chaveValor'");
	while ($mT225=mysql_fetch_array($T225)){
	    mysql_query("CALL T225_Gravar_Totalizacao('$mT225[T225_Id]')");
        mysqli_query("UPDATE T225 SET T225_Valor_Preco_Bruto='0.00',T225_Valor_Preco_Original_Bruto='0.00',T225_Valor_Desconto_Unitario_Bruto='0.00',T225_Percentual_Desconto_Bruto='0.00' WHERE T225_Id={$mT225['T225_Id']}");
	}

	mysqli_query("UPDATE T224 SET T224_Prazos_Pagamentos = IF(T224_Prazos(T224_Id) = '', T224_Prazos_Pagamentos, T224_Prazos(T224_Id)) WHERE T224_Id = '{$chaveValor}'");

	mysqli_query("UPDATE T224 SET T224_Flag_Status = '9', T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id) WHERE T224_Id = '{$chaveValor}' AND T224_Flag_Status='10' AND T224_Valor_Pendente>0");

	mysqli_query("call T224_Gravar_Totalizacao_3('$chaveValor')");

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT224();
EOT;
$conf['triggers']['T224']['deleteBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T224']['deleteAfter'] = <<<'EOT'
EOT;

/**
* camposValorPadrao
*/
$conf['camposValorPadrao']['T224_D036_Id'] = 1;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T224'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {

        $sql = "SELECT T224_Id FROM T224 WHERE T224_Id = '{$valorPk}' AND T224_Flag_Status != '0' AND T224_Flag_Status != '' AND T224_Flag_Status != '2'";
        $res = mysql_query($sql);
        $rows = mysql_num_rows($res);
        if ($rows > 0 and $g['nivelAlcada'] != 4 && (!isset($g['c029Ids'][199]))) {
	 		$retorno = "Ordem de Compra já está aprovada!";
		}

		/*
		SEGUI A MENSMA RESTRIÇÃO TRAVAR QUE ESTÁ NO T225.php POR ACHAR QUE ESTÁ MELHOR, ENTÃO RETIREI ESTA VALIDAÇÃO ABAIXO E AJUSTEI AS DEMAIS IGUAL AO T225.PHP
		$sql = "SELECT T224_Id FROM T224 WHERE T224_Id = '{$valorPk}' AND (T224_Flag_Status = '10')";
        $res = mysql_query($sql);
        $rows = mysql_num_rows($res);
        if ($rows > 0) {
           $retorno = 'Ordem de compra já está finalizada!';
		}*/

		$T013 = mysql_query("SELECT T013_Id,
									T013_Numero_Nota_Fiscal 
							   FROM T013 
						  LEFT JOIN T014 ON T014_T013_Id=T013_Id
						  LEFT JOIN T225 ON T225_Id=T014_T225_Id
						  LEFT JOIN T224 ON T224_Id=T225_T224_Id
							  WHERE T224_Id='{$valorPk}'
							    AND T013_Flag_Travar_Nota='S'");
		$mT013 = mysql_fetch_assoc($T013);
		if(mysql_num_rows($T013) > 0){
			$retorno = "Não é possível alterar OC vinculada a NF-e {$mT013['T013_Numero_Nota_Fiscal']} já finalizada.";
		}
    } else {
        $retorno = true;
	}	

    if (!empty($valorPk)) {
	    $sql = "SELECT T005_Id FROM T005 WHERE T005_T224_Id = '{$valorPk}' AND T005_Flag_Status != '8';";
	    $res = mysql_query($sql);
	    if (mysql_num_rows($res) > 0) {
	    	$retorno = 'Já foi gerado um Pedido para esta Ordem de Transferência.';
	    }
	}

	if($campo == 'T224_Observacao' || $campo == 'T224_Observacao_Comercial' || $campo == 'T224_Observacao_Nota_Fiscal' || $campo == 'T224_Observacao_1'){
		$retorno = true;
	}
EOT;

$conf['camposLabelForm']['T224_Observacao_1'] = 'Almoxarifado';

/**
* trocarEmpresa
*/
$conf['trocarEmpresa']['exibir'] = <<<'EOT'
	// Variáveis disponíveis: $r_tabela, $r_acaoId
	// Retornar 'true' irá ativar o ícone para trocar a empresa
	$sql = <<<SQL
		SELECT 
			group_concat(distinct T013_Id) as T013_Id
		FROM
			T224 
		left join T225 on T225_T224_Id=T224_Id AND T225_Flag_Cancelada != 'S'
		left join T014 on T014_T225_Id=T225_Id
		left join T013 on T013_Id=T014_T013_Id and T013_Flag_Travar_Nota='S'
		WHERE T224_Id = '{$r_acaoId}'
		GROUP BY T224_Id
SQL;
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if (empty($row['T013_Id'])) {
		return true;
	}
EOT;
$conf['trocarEmpresa']['tabelaFilho'] = 'T225';
$conf['trocarEmpresa']['executarNoFinal'] = <<<'EOT'
	// Variáveis disponíveis: $C004_Id_Novo, $Tabela, $Id
	require_once('bibliotecas/classes/EST016.php');
	$EST016 = new EST016();
	$EST016->est016atualizarCFOP($Id);
	mysql_query("call T224_Gravar_Totalizacao_3('{$Id}')");
	$EST016->atualizarAliquotasPedidos($Id, $C004_Id_Novo);
EOT;
