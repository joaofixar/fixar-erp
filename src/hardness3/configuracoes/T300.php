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
$conf['triggers']['T300']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['T300']['insertAfter'] = <<<'EOT'
EOT;
$conf['triggers']['T300']['updateBefore'] = <<<'EOT'
EOT;
$conf['triggers']['T300']['updateAfter'] = <<<'EOT'
EOT;
$conf['triggers']['T300']['deleteBefore'] = <<<'EOT'

require_once("bibliotecas/classes/IMP005.php");
$IMP005 = new IMP005();
$IMP005->processarOrdemDespesa($chaveValor, '0');

EOT;
$conf['triggers']['T300']['deleteAfter'] = <<<'EOT'
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['T300'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $T300 = mysqli_query("SELECT T300_Flag_Tipo,T300_Flag_ME FROM T300 WHERE T300_Id = '{$valorPk}'");
        $mT300 = mysqli_fetch_array($T300);

        if ($campo == 'T300_Valor') {
            if ($valor > 0 && $mT300['T300_Flag_Tipo'] == 'F'){
                $retorno = "Despesa tipo fórmula, campo valor não pode ser editado.";		
            } 
        }
        if ($campo == 'T300_Formula' ) {
            if ($valor !='' && $mT300['T300_Flag_Tipo'] == 'V'){
                $retorno = "Despesa tipo valor fixo, campo valor não pode ser editado.";		
            } 
        }

        if ($campo == 'T300_Aliquota') {
            if ($valor > 0 && $mT300['T300_Flag_Tipo'] == 'V'){
                $retorno = "Despesa tipo valor fixo, campo valor não pode ser editado.";		
            } 
        }

        if ($campo == 'T300_Cotacao_ME') {
            if ($valor > 0 && $mT300['T300_Flag_ME'] == 'N'){
                $retorno = "Despesa não é em moeda estrangeira, campo Cot.ME não pode ser editado.";		
            } 
        }
    }

EOT;

 
?>