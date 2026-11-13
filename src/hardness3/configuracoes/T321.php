<?php
/**
 * Evento NF-e.
 *
 * Após o registro na SEFAZ, o evento e seus dados não podem mais ser
 * alterados pela interface. Atualizações de retorno feitas pela integração
 * usam SQL direto e permanecem permitidas.
 */
$conf['restricaoTravar']['T321'] = <<<'EOT'
    $retorno = true;

    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = "SELECT T321_Protocolo
                FROM T321
                WHERE T321_Id = '{$valorPk}'";
        $evento = mysql_fetch_assoc(mysql_query($sql));

        if (!empty($evento['T321_Protocolo'])) {
            $retorno = 'Evento já autorizado pela SEFAZ. Não é possível alterar ou excluir.';
        }
    }
EOT;
