<?php
/**
 * totais.php
 *
 * Versão legível de sistema-legado/sistema/totais.php.
 * O original foi ofuscado com desvios; aqui o fluxo foi reconstruído
 * em if/else/while/foreach, no mesmo comportamento.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */
namespace hardness;

class Totais
{
    public static function totalGenerico()
    {
        $parcial = array('count' => 0, 'soma' => 0, 'max' => 0, 'min' => false, 'distinct' => array());
        return function($linha, $fim = false, $formula = '') use(&$parcial) {
            if (!$fim) {
                $parcial['count']++;
                $valor = !empty($formula) ? Totais::formulaTipoFormula($formula, $linha) : $linha['campoAtual'];
                $parcial['soma'] += $valor;
                $parcial['max'] = $valor > $parcial['max'] ? $valor : $parcial['max'];
                $parcial['min'] = $parcial['min'] === false ? $valor : ($valor > $parcial['min'] ? $parcial['min'] : $valor);
                $parcial['distinct'][$valor]++;
            }
            return array('count' => $parcial['count'], 'soma' => $parcial['soma'], 'max' => $parcial['max'], 'min' => $parcial['min'], 'media' => $parcial['soma'] / $parcial['count'], 'distinct' => count($parcial['distinct']));
        };
    }

    public static function formulaTipoFormula($formula, $linha)
    {
        preg_match_all('/\\{(.*?)\\}/', $formula, $matches);
        foreach ($matches[1] as $var) {
            $formula = str_replace("{{$var}}", $linha['todosCampos'][$var], $formula);
        }
        return eval("return {$formula};");
    }

    public static function formulaTipoTotais($formula, $totais)
    {
        preg_match_all('/\\{(.*?)\\}/', $formula, $matches);
        foreach ($matches[1] as $var) {
            $formula = str_replace("{{$var}}", $totais[$var], $formula);
        }
        return eval("return {$formula};");
    }

    public static function imprimirTotais($totais)
    {
        global $g;
        $retorno = '';
        $temPermissao = $g['usuarioAtual'] == 63 || $g['C007']['C007_Usuario_Suporte'] == 'S';
        foreach ($totais as $total) {
            $totalNome = $total['nome'];
            $totalExibir = !is_null($total['exibir']) ? $total['exibir'] : true;
            if ($totalExibir) {
                $totalTitulo = $total['titulo'];
                $totalClasse = $total['class'] ? "total-{$total['class']}" : 'total-padrao';
                $valor = $total['totalPosCallback'];
                $onclick = $temPermissao ? "onclick=\"editarTotais(event, '{$totalNome}')\"" : '';
                $retorno .= <<<EOT
            <div {$onclick} class="total {$totalClasse}"><span class="titulo" style="text-align:right">{$totalTitulo}<br><input type="text" class="valor" value="{$valor}" readonly></span></div>
EOT;
            }
        }
        return $retorno;
    }

}
