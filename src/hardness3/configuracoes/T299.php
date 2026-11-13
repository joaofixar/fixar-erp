<?php
/**
 * Tabela T294

 */
/**
* triggers
* Execução de código PHP antes/após INSERT/UPDATE/DELETE
* A chave primária sempre estará disponível (exceto em beforeInsert) pela variavél: $chaveValor 
* Coloque um '\' na função mysql_query: mysqli_query() para utilizar a original do mysql
*/
$conf['triggers']['T299']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['T299']['insertAfter'] = <<<'EOT'
EOT;
$conf['triggers']['T299']['updateBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T299']['updateAfter'] = <<<'EOT'
EOT;
$conf['triggers']['T299']['deleteBefore'] = <<<'EOT'

require_once("bibliotecas/classes/IMP004.php");
$IMP004 = new IMP004();
$IMP004->processarOrdemDespesa($chaveValor, '0');

EOT;
$conf['triggers']['T299']['deleteAfter'] = <<<'EOT'
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T299'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $T299 = mysqli_query("SELECT T299_Flag_Tipo,T299_Flag_ME FROM T299 WHERE T299_Id = '{$valorPk}'");
        $mT299 = mysqli_fetch_array($T299);

        if ($campo == 'T299_Valor') {
            if ($valor > 0 && $mT299['T299_Flag_Tipo'] == 'F'){
                $retorno = "Despesa tipo fórmula, campo valor não pode ser editado.";		
            } 
        }
        if ($campo == 'T299_Formula' ) {
            if ($valor !='' && $mT299['T299_Flag_Tipo'] == 'V'){
                $retorno = "Despesa tipo valor fixo, campo valor não pode ser editado.";		
            } 
        }

        if ($campo == 'T299_Aliquota') {
            if ($valor > 0 && $mT299['T299_Flag_Tipo'] == 'V'){
                $retorno = "Despesa tipo valor fixo, campo valor não pode ser editado.";		
            } 
        }

        if ($campo == 'T299_Cotacao_ME') {
            if ($valor > 0 && $mT299['T299_Flag_ME'] == 'N'){
                $retorno = "Despesa não é em moeda estrangeira, campo Cot.ME não pode ser editado.";		
            } 
        }
    }

EOT;

 
?>