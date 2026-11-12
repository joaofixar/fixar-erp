<?php
namespace hardness;

require_once __DIR__ . '/app.php';

function importador_orcamento_pdf_require_auth()
{
    if (!importador_orcamento_pdf_bootstrap()) {
        header('HTTP/1.1 403 Forbidden');
        echo 'Sessao invalida ou expirada.';
        exit;
    }
}

function importador_orcamento_pdf_orcamento($T003_Id)
{
    importador_orcamento_pdf_require_auth();

    $T003_Id = (int) $T003_Id;
    if ($T003_Id <= 0) {
        return null;
    }

    $empresaAtual = importador_orcamento_pdf_company_id();
    $sql = "SELECT T003_Id,
                   T003_D024_Id,
                   T003_C004_Id,
                   T003_C007_Id_Vendedor_Interno,
                   T003_Flag_Perdido,
                   T003_Flag_Status_Orcamento,
                   T003_D006_Id,
                   T003_Flag_Destino_Produto,
                   T003_Flag_Lista_Preco
              FROM T003
             WHERE T003_Id = '{$T003_Id}'
               AND T003_C004_Id = '{$empresaAtual}'";

    $res = mysql_query($sql);
    if (!$res || mysql_num_rows($res) <= 0) {
        return null;
    }

    return mysql_fetch_assoc($res);
}

function importador_orcamento_pdf_validar_orcamento_editavel($T003_Id)
{
    $orcamento = importador_orcamento_pdf_orcamento($T003_Id);
    if (!$orcamento) {
        return array(false, 'Orcamento nao encontrado para a empresa atual.', null);
    }

    if ($orcamento['T003_Flag_Perdido'] === 'S') {
        return array(false, 'Orcamento perdido nao permite importacao de itens.', $orcamento);
    }

    global $g;
    if (isset($g['C031']['bloqueioOrcamentoConfirmado']) && $g['C031']['bloqueioOrcamentoConfirmado'] === 'S') {
        $T003_Id = (int) $orcamento['T003_Id'];
        $sql = "SELECT T005_Id FROM T005 WHERE T005_T003_Id = '{$T003_Id}' LIMIT 1";
        $res = mysql_query($sql);
        if ($res && mysql_num_rows($res) > 0) {
            return array(false, 'Este orcamento ja gerou pedido e nao permite importacao de itens.', $orcamento);
        }
    }

    return array(true, '', $orcamento);
}

