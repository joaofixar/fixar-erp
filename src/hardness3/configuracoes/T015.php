<?php
/**
 * Tabela T015

 */
global $idFormDecImport;
$conf['restricaoIncluirUpdate']['T015_D014_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D014_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T015_D073_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D073_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['$T015_D031_Id'] = <<<'EOT'
	$retorno = rVerificaFK('$D031_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T015_D032_Id'] = <<<'EOT'
	$retorno = rVerificaFK('D032_Id', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T015_Numero_Duplicata'] = <<<'EOT'
	$retorno = rRegx('naoVazio', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T015_Data_Emissao'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
$conf['restricaoIncluirUpdate']['T015_Data_Vencimento'] = <<<'EOT'
	$retorno = rRegx('data', $valor);
EOT;
// Se a global "usarMoedaEstrangeira" estiver habilitada e o "Contas a Pagar" for referente ao formulário da "Declaração de Importação"
if ($g['C031']['usarMoedaEstrangeira'] == 'S' && isset($idFormDecImport)) {
	$conf['restricaoIncluirUpdate']['T015_Valor_Duplicata_Moeda'] = <<<'EOT'
		$retorno = rNumeroMaiorQueZero($valor);
EOT;
	$conf['restricaoIncluirUpdate']['T015_Valor_Cambio_Moeda'] = <<<'EOT'
		$retorno = rNumeroMaiorQueZero($valor);
EOT;
} else {
	$conf['restricaoIncluirUpdate']['T015_Valor_Duplicata'] = <<<'EOT'
		$retorno = rNumeroMaiorQueZero($valor);
EOT;
}

/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query:mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T015']['insertBefore'] = <<<'EOT'
// Código PHP
EOT;
$conf['triggers']['T015']['insertAfter'] = <<<'EOT'
mysqli_query("call T015_Gravar_Totalizacao('$chaveValor')");
mysqli_query("call T015_Baixar_Titulo('$chaveValor','".date('Y-m-d')."')");
require_once('bibliotecas/classes/FIN009.php');
$FIN009 = new FIN009();
$sql =mysqli_query("SELECT T015_D014_Id, T015_Data_Vencimento FROM T015 WHERE T015_Id = '{$chaveValor}'");
$row = mysql_fetch_array($sql);
$mes = date("n",strtotime($row['T015_Data_Vencimento']));
$ano = date("Y",strtotime($row['T015_Data_Vencimento']));
$periodo = array('mes' => $mes, 'mes_final' => $mes, 'ano_atual' => $ano, 'ano' => $ano, 'ano_final' => $ano);
$FIN009->FIN009RecalculaProjecao($row['T015_D014_Id'], $periodo, null,null);
EOT;

$conf['triggers']['T015']['updateBefore'] = <<<'EOT'
EOT;

$conf['triggers']['T015']['updateAfter'] = <<<'EOT'
mysqli_query("call T015_Gravar_Totalizacao('$chaveValor')");
mysqli_query("call T015_Baixar_Titulo('$chaveValor','".date('Y-m-d')."')");
require_once('bibliotecas/classes/FIN009.php');
$FIN009 = new FIN009();
$sql =mysqli_query("SELECT T015_D014_Id, T015_Data_Vencimento FROM T015 WHERE T015_Id = '{$chaveValor}'");
$row = mysql_fetch_array($sql);
$mes = date("n",strtotime($row['T015_Data_Vencimento']));
$ano = date("Y",strtotime($row['T015_Data_Vencimento']));
$periodo = array('mes' => $mes, 'mes_final' => $mes, 'ano_atual' => $ano, 'ano' => $ano, 'ano_final' => $ano);
$FIN009->FIN009RecalculaProjecao($row['T015_D014_Id'], $periodo, null,null);
EOT;
$conf['triggers']['T015']['deleteBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T015']['deleteAfter'] = <<<'EOT'
EOT;

$conf['restricaoTravar']['T015'] = <<<'EOT'
	if(!isset($g['c029Ids'][191])){
		$retorno = "Não possui permissão.";
		return false;
	}
EOT;

?>