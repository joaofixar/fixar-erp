<?php
/**
* camposValorPadrao
*/
$conf['camposValorPadrao']['T013_Flag_Calcular'] = "S";
$conf['camposValorPadrao']['T013_Flag_Validacoes'] = "S";
$conf['camposValorPadrao']['T013_Flag_Obrigatorio_OC'] = $g['C031']['obrigatorioOCNotaFiscalCompra'];

/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T013']['insertAfter'] = <<<'EOT'
	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT013();
EOT;

$conf['triggers']['T013']['updateAfter'] = <<<'EOT'
	$sql = mysql_query("SELECT * FROM T013 WHERE T013_Id = '{$chaveValor}'");
	$res = mysql_fetch_array($sql);
	$sqlT084 = mysql_query("SELECT * FROM T084 WHERE T084_Numero_Conhecimento = '{$res['T013_Numero_Conhecimento']}' AND T084_D022_Id = '{$res['T013_D022_Id']}'");
	if(mysql_num_rows($sqlT084) > 0){
		$resT084 = mysql_fetch_array($sqlT084);
		mysqli_query("UPDATE T013 SET T013_T084_Id = '{$resT084['T084_Id']}' WHERE T013_Id = '{$chaveValor}'");
	} else {
		mysqli_query("UPDATE T013 SET T013_T084_Id = '0' WHERE T013_Id = '{$chaveValor}'");
	}

	//require_once('bibliotecas/classes/NOTIFICACOES.php');
	//$NOTIFICACOES = new NOTIFICACOES();
	//$NOTIFICACOES->atualizarNotificacoesT013();
EOT;


/**
* restricaoTravar
* Código PHP para validar se uma tabela pode: ser editada, incluida ou removida
* Para ver variáveis e funções disponíveis dentro do código, veja:
*    http://hardness/wiki/index.php?title=Restri%C3%A7%C3%B5es_de_Campos
*/
$conf['restricaoTravar']['T013'] = <<<'EOT'
	if(!isset($g['c029Ids'][189])){
		$retorno = "Não possui permissão.";
		return false;
	}
	$retorno = true;
	if($operacao == 'alterar' || $operacao == 'excluir') {
		$sql = mysql_query("SELECT * FROM T013 WHERE T013_Flag_Travar_Nota = 'S' AND T013_Id = '{$valorPk}'");
		if (mysql_num_rows($sql) > 0) {
			$mT013=mysql_fetch_array($sql);
			if($g['nivelAlcada'] >= 4 and ($campo == 'T013_Data_Entrada' || $campo == 'T013_Data_Entrada_Estoque')){
				return true;
			}
			if($g['nivelAlcada'] >= 2 and $mT013['T013_Flag_Estoque_Finalizado']!='S'){
				return true;
			} else {
				$retorno = 'Esta nota já foi finalizada';
			}
		}
	} else {
		$retorno = true;
	}
EOT;
