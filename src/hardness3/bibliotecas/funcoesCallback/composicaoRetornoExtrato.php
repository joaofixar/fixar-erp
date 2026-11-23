<?php
namespace hardness;

function composicaoRetornoExtrato($array)
{
    global $g;
    $movimentoId = (int) $array['todosCampos']['T216_Id'];
    if (empty($g['composicoesRetornoExtrato'][$movimentoId])) { return ''; }
    $grupo = $g['composicoesRetornoExtrato'][$movimentoId];
    if ($grupo['situacao'] !== 'encontrado') {
        return '<span title="Há mais de uma composição possível ou títulos compartilhados entre créditos.">Conferir composição</span>';
    }
    $retornoId = (int) $grupo['retorno_id'];
    $quantidade = count($grupo['membros']);
    $parametros = '&acaoId=' . (int) $g['composicaoRetornoExtratoId'] . '&T216_Id=' . $movimentoId
        . '&T037_Id=' . $retornoId . '&assinatura=' . $grupo['assinatura'];
    $onclick = "abrirJanela(false, divIdRootAberto(), gridPegarDivId($(this)), unique(), '', 'Composição do retorno', '/fin/fin003/content/fin003ContentComposicaoRetorno/', '{$parametros}', [1050,600]); return false;";
    return '<a href="#" title="Conferir os títulos que compõem este crédito" onclick="'
        . htmlspecialchars($onclick, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">Retorno ' . $retornoId
        . ' · ' . $quantidade . ($quantidade === 1 ? ' título' : ' títulos') . '</a>';
}
