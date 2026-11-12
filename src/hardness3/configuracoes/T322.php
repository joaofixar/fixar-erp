<?php
/**
 * Itens do evento 112140.
 *
 * A trava é determinada pelo protocolo do evento pai (T321), tanto para
 * alteração/exclusão de um item já gravado quanto para inclusão de novo item.
 */
$conf['restricaoTravar']['T322'] = <<<'EOT'
    $retorno = true;

    if ($operacao == 'alterar' || $operacao == 'excluir') {
        $sql = "SELECT T321_Protocolo
                FROM T322
                INNER JOIN T321 ON T321_Id = T322_T321_Id
                WHERE T322_Id = '{$valorPk}'";
        $evento = mysql_fetch_assoc(mysql_query($sql));
    } elseif ($operacao == 'incluir') {
        $sql = "SELECT T321_Protocolo
                FROM T321
                WHERE T321_Id = '{$valorPkParent}'";
        $evento = mysql_fetch_assoc(mysql_query($sql));
    }

    if (!empty($evento['T321_Protocolo'])) {
        $retorno = 'Evento já autorizado pela SEFAZ. Não é possível alterar, excluir ou incluir itens.';
    }
EOT;
