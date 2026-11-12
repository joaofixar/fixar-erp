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
$conf['triggers']['D192']['insertBefore'] = <<<'EOT'
	// Código PHP
EOT;
$conf['triggers']['D192']['insertAfter'] = <<<'EOT'
EOT;
$conf['triggers']['D192']['updateBefore'] = <<<'EOT'
EOT;
$conf['triggers']['D192']['updateAfter'] = <<<'EOT'
EOT;
$conf['triggers']['D192']['deleteBefore'] = <<<'EOT'

require_once("bibliotecas/classes/CAD017.php");
$CAD017 = new CAD017();
$CAD017->processarOrdemDespesa($chaveValor, '0');

EOT;
$conf['triggers']['D192']['deleteAfter'] = <<<'EOT'
EOT;

/**
* restricaoTravar
*/
$conf['restricaoTravar']['D192'] = <<<'EOT'
    $retorno = true;
    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $D192 = mysqli_query("SELECT D192_Flag_Tipo,D192_Flag_ME FROM D192 WHERE D192_Id = '{$valorPk}'");
        $mD192 = mysqli_fetch_array($D192);

        if ($campo == 'D192_Valor') {
            if ($valor > 0 && $mD192['D192_Flag_Tipo'] == 'F'){
                $retorno = "Despesa tipo fórmula, campo valor não pode ser editado.";		
            } 
        }
        if ($campo == 'D192_Formula' ) {
            if (($valor !='' || $valor >= 0) && $mD192['D192_Flag_Tipo'] == 'V'){
                $retorno = "Despesa tipo valor fixo, campo valor não pode ser editado.";		
            } 
        }

        if ($campo == 'D192_Aliquota') {
            if ($valor > 0 && $mD192['D192_Flag_Tipo'] == 'V'){
                $retorno = "Despesa tipo valor fixo, campo valor não pode ser editado.";		
            } 
        }

        if ($campo == 'D192_Cotacao_ME') {
            if ($valor > 0 && $mD192['D192_Flag_ME'] == 'N'){
                $retorno = "Despesa não é em moeda estrangeira, campo Cot.ME não pode ser editado.";		
            } 
        }
    }

EOT;

 
?>