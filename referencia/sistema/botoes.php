<?php
/**
 * botoes.php
 *
 * Versão legível de sistema-legado/sistema/botoes.php.
 * O original foi ofuscado com desvios; aqui o fluxo foi reconstruído
 * em if/else/while/foreach, no mesmo comportamento.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */
namespace hardness;

class Botoes
{
    public static function processar(&$botoes, $tipo = 'geral', $id = '')
    {
        global $g, $geradoSql, $r_mostrarSomenteGrid;
        if (!$r_mostrarSomenteGrid) {
            $divId = md5($g['r_']);
            apc_store("botoes-php-{$divId}", serialize($botoes), 3600);
            $botoesPorId = array();
            foreach ($botoes as $key => $botao) {
                $botoesPorId[$botao['id']] = $key;
            }
            $personalizado = array();
            if ($g['modoDesenvolvedor']) {
                $arquivos = array("ide/personalizacaoBotoes/" . $divId);
            } else {
                if ($g['modoDesenvolvedorApp']) {
                    $arquivos = array("ide/personalizacaoBotoes/" . $divId, Apps::getDirAtivos() . "personalizacaoBotoes/" . $divId, Apps::getDirEditor() . "personalizacaoBotoes/" . $divId);
                } else {
                    $arquivos = array("ide/personalizacaoBotoes/" . $divId, Apps::getDirAtivos() . "personalizacaoBotoes/" . $divId, "{$g['pathDados']}personalizacaoBotoes/" . $divId);
                }
            }
            foreach ($arquivos as $arquivoPersonalizado) {
                if (!(!file_exists($arquivoPersonalizado))) {
                    $personalizado = unserialize(file_get_contents($arquivoPersonalizado));
                    foreach ($personalizado as $botao) {
                        if (isset($botoesPorId[$botao['id']])) {
                            $botoes[$botoesPorId[$botao['id']]] = $botao;
                        } else {
                            $botoes[] = $botao;
                            $botoesPorId[$botao['id']] = array_key_last($botoes);
                        }
                    }
                }
            }
            $retorno = '';
            $retornoEsquerda = '<div class="buttonsLeft">' . "\n";
            $retornoDireita = '<div class="buttonsRight">' . "\n";
            $id = $id ? 'id="' . $id . '"' : '';
            if ($tipo == 'content' || $tipo == 'form') {
                if (empty($botoes)) {
                    $retorno .= '<div ' . $id . ' class="frameworkBotoes">' . "\n";
                } else {
                    $retorno .= '<div ' . $id . ' class="buttonsBottom frameworkBotoes">' . "\n";
                }
            } else {
                $retorno .= '<div ' . $id . ' class="buttonsBottomTop frameworkBotoes">' . "\n";
            }
            foreach ($botoes as $botao) {
                $extra = '';
                $class = '';
                $style = '';
                $tipo = !$botao['tipo'] ? 'janela' : $botao['tipo'];
                if (!(!$botao['id'])) {
                    $extra .= " id=\"{$botao['id']}\"";
                }
                $parametros = $botao['janelaParametros'] ? $botao['janelaParametros'] : '';
                $parametros = gSubstituirVariaveisParametros($parametros, true);
                $parametros = str_replace("[[sql]]", urlencode($geradoSql), $parametros);
                if ($tipo == 'janela' && !empty($botao['janelaUrl'])) {
                    $titulo = $botao['janelaTitulo'] ? $botao['janelaTitulo'] : $botao['titulo'];
                    $tamanho = $botao['janelaTamanho'] ? $botao['janelaTamanho'] : 'auto';
                    if ($tamanho == 'auto') {
                        $tamanho = "'auto'";
                    }
                    $repassarParametros = gRepassarParametros(false, array('acaoId'));
                    $extra .= <<<EOT
 onclick="abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', '{$titulo}', '{$botao['janelaUrl']}', '{$parametros}' + '{$repassarParametros}', {$tamanho}); return false;"
EOT;
                } else {
                    if (($tipo == 'codigo' || $tipo == 'codigojs') && !empty($botao['callback'])) {
                        $titulo = $botao['janelaTitulo'] ? $botao['janelaTitulo'] : $botao['titulo'];
                        $recarregar = $botao['recarregar'] ? 1 : 0;
                        $lote = $botao['lote'] || $botao['lote'] == '0' ? $botao['lote'] : 1;
                        if ($tipo == 'codigojs') {
                            $lote = "'js'";
                        }
                        $urlForm = $botao['urlForm'] ? $botao['urlForm'] : '';
                        $confirmacao = isset($botao['confirmacao']) ? addslashes(str_replace(array("\r", "\n"), array('', '\\n'), $botao['confirmacao'])) : '';
                        $extra .= <<<EOT
 onclick="executarCallbackRegistroGrid(event, '{$titulo}', '{$divId}', '{$botao['id']}', {$recarregar}, {$lote}, '{$urlForm}', '', '{$parametros}', '{$confirmacao}'); return false;"
EOT;
                    }
                }
                $exibir = isset($botao['exibir']) ? $botao['exibir'] : true;
                if (!(empty($botao['exibirCallback']))) {
                    $callback = $botao['exibirCallback'];
                    if (file_exists("bibliotecas/funcoesCallback/{$callback}.php")) {
                        $callUserFunc = 'hardness\\' . $callback;
                        if (!function_exists($callUserFunc)) {
                            require_once "bibliotecas/funcoesCallback/{$callback}.php";
                        }
                    } else {
                        $codigo = $callback;
                        if (strpos($codigo, 'namespace hardness') === false) {
                            $codigo = <<<EOT
<?php
namespace hardness;
function callback(\$array = array()) {
    global \$g;
    {$codigo}
}
EOT;
                        }
                        $codigo = preg_replace('/function[^(]+\\(/', '$exibirCallback = function(', $codigo);
                        $codigo = str_replace('<?php', '', $codigo);
                        $codigo .= ';';
                        eval($codigo);
                        $callUserFunc = $exibirCallback;
                    }
                    $exibir = call_user_func($callUserFunc, gRepassarParametros(true));
                }
                if (!$exibir) {
                    $class .= "botaoOculto";
                    $style .= "display: none;";
                }
                $extra .= " botaoId='{$botao['id']}'";
                if (!(!$botao['desabilitar'])) {
                    $class .= "botaoDesabilitado";
                    $extra .= ' disabled="disabled"';
                }
                $botaoRetorno = <<<EOT
<button type="button" class="{$class}" style="{$style}"{$extra}>{$botao['titulo']}</button>

EOT;
                if (!$botao['posicao'] || $botao['posicao'] == 'esquerda') {
                    $retornoEsquerda .= $botaoRetorno;
                } else {
                    $retornoDireita .= $botaoRetorno;
                }
            }
            $retornoEsquerda .= <<<EOT
<a href="#" onclick="editarBotao(event); return false;" style="margin-top: 5px; opacity: 0.5; display: none;" class="botaoAdd">
  <span class="ui-icon ui-icon-plusthick">add</span>
</a>
EOT;
            $retorno .= "{$retornoEsquerda}</div>{$retornoDireita}</div><div style=\"clear:both;\"></div></div>\n";
            return $retorno;
        } else {
            return;
        }
    }

}
