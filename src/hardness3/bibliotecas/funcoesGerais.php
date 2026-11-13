<?php
/**
 * funcoesGerais.php
 *
 * Versão legível. O original foi ofuscado com desvios; o fluxo foi
 * reconstruído em if/else/while/foreach, no mesmo comportamento.
 */
namespace hardness;

use setasign\Fpdi\Tcpdf\Fpdi;

setlocale(LC_CTYPE, 'pt_BR.UTF-8');

function gInserePorcentagem($valor)
{
    if (!(!is_array($valor))) {
        if (!empty($valor['campoAtual'])) {
            $valor = $valor['campoAtual'];
        } else {
            return '';
        }
    }
    return $valor . '%';
}

function gDeletarArquivo($caminho)
{
    $extensao = pathinfo($caminho, PATHINFO_EXTENSION);
    if (!($extensao == "pdf")) {
    } else {
        if (file_exists($caminho)) {
            return unlink($caminho);
        } else {
            return "Arquivo não encontrado";
        }
    }
}

function gRedimensionarPDF($caminhoPdf, $caminhoOutput, $modoImagem, $dimensoes)
{
    global $g;
    $resposta = array("code" => false, "data" => "");
    if (empty($caminhoPdf) or empty($modoImagem)) {
        $resposta['data'] = "Caminho ou modo de imagem estão vazios.";
    }
    if (!isset($dimensoes['x']) or !isset($dimensoes['y'])) {
        $resposta['data'] = "As dimensões não foram especificadas.";
    }
    if ($modoImagem != 'L' and $modoImagem != 'P') {
        $resposta['data'] = "O modo de imagem não foi especificado corretamente";
    }
    if (!file_exists($caminhoPdf) or pathinfo($caminhoPdf)['extension'] != 'pdf') {
        $resposta['data'] = "O caminho não existe, ou não é um pdf";
    }
    if (pathinfo($caminhoOutput)['extension'] != 'pdf') {
        $resposta['data'] = "O caminho de saída deve especificar um arquivo pdf.";
    }
    if (empty($resposta['data'])) {
        $pdf = new Fpdi($modoImagem, 'mm', array($dimensoes['x'], $dimensoes['y']));
        $pdf->setSourceFile($caminhoPdf);
        $tplIdx = $pdf->importPage(1);
        $pdf->AddPage();
        $pdf->useTemplate($tplIdx);
        $pdf->Output($caminhoOutput, 'F');
        if (file_exists($caminhoOutput)) {
            $resposta['code'] = true;
            $reposta['data'] = "Sucesso ao redimensionar o PDF";
        } else {
            $reposta['data'] = "O pdf não pode ser redimensionado";
        }
        return $resposta;
    } else {
        return $resposta;
    }
}

function gCorrigeData($data, $removerTime = false)
{
    $valor = $data;
    if (!(!is_array($valor))) {
        if (!empty($valor['campoAtual'])) {
            $valor = $valor['campoAtual'];
        } else {
            return '';
        }
    }
    $valor = explode(' ', $valor);
    $hora = isset($valor[1]) ? ' ' . $valor[1] : '';
    $valor = explode('-', $valor[0]);
    if (!($valor[0] == '0000')) {
        if (isset($valor[0]) && isset($valor[1]) && isset($valor[2])) {
            $data = mktime(0, 0, 0, $valor[1], $valor[2], $valor[0]);
            if ($removerTime) {
                return $valor[2] . '/' . $valor[1] . '/' . $valor[0];
            } else {
                return $valor[2] . '/' . $valor[1] . '/' . $valor[0] . $hora;
            }
        } else {
            return $data;
        }
    } else {
        return '';
    }
}

function gCorrigeDataInverte($valor)
{
    if (!(empty($valor) || stripos($valor, '*VAZIO*') !== false)) {
        if (!(!is_array($valor))) {
            if (!empty($valor['campoAtual'])) {
                $valor = $valor['campoAtual'];
            } else {
                return '';
            }
        }
        $valor = trim($valor);
        $hora = NULL;
        if (strlen($valor) == 19) {
            $hora = trim(substr($valor, 10, 8));
        }
        $valor = trim(substr($valor, 0, 10));
        $valor = explode('/', $valor);
        $valor[2] = (int)$valor[2];
        if (!empty($hora)) {
            $hora = explode(':', $hora);
            $data = @mktime($hora[0], $hora[1], $hora[2], $valor[1], $valor[0], $valor[2]);
        } else {
            $data = @mktime(0, 0, 0, $valor[1], $valor[0], $valor[2]);
        }
        if (!empty($data)) {
            if (count($hora) > 0) {
                $data = date("Y-m-d h:i:s", $data);
            } else {
                $data = date("Y-m-d", $data);
            }
            return $data;
        } else {
            return '';
        }
    } else {
        return '';
    }
}

function gCorrigeNumero($valor, $decimal = 2)
{
    if (!(!is_array($valor))) {
        if (!empty($valor['campoAtual'])) {
            $valor = $valor['campoAtual'];
        } else {
            return '';
        }
    }
    if (!empty($valor) && is_numeric($valor)) {
        return number_format($valor, $decimal, ',', '.');
    } else {
        if (empty($decimal)) {
            return '0';
        } else {
            return str_pad('0,', $decimal + 2, "0", STR_PAD_RIGHT);
        }
    }
}

function gCorrigeNumeroInverte($valor)
{
    if (!(!is_array($valor))) {
        if (!empty($valor['campoAtual'])) {
            $valor = $valor['campoAtual'];
        } else {
            return '';
        }
    }
    $valor_ = str_replace('.', '', $valor);
    $valor_ = str_replace(',', '.', $valor_);
    return $valor_;
}

function gHtmlentities($valor)
{
    if (!(!is_array($valor))) {
        if (!empty($valor['campoAtual'])) {
            $valor = $valor['campoAtual'];
        } else {
            return '';
        }
    }
    return htmlentities($valor, ENT_COMPAT, 'utf-8');
}

function gExcluirLinha($valor, $array = false)
{
    global $g, $r_origemFiltro;
    if (!isset($r_origemFiltro)) {
        $id = $array[0];
        $divId = !$array[1] ? $g['divId'] : $array[1];
        $campoParent = isset($array[2]) ? $array[2] : false;
        $valorParent = isset($array[3]) ? $array[3] : false;
        $divReload = isset($array[4]) ? $array[4] : false;
        $pk = $valor['todosCampos'][$id];
        $restricao = $g['conf']->restricaoTravar('excluir', $id, $pk, $campoParent, $valorParent);
        if ($restricao === true) {
            return<<<EOT
\t\t\t<a href="#" onclick="gExcluirLinha('{$id}', '{$pk}', '{$divId}', '{$campoParent}', '{$valorParent}', '{$divReload}')">
\t\t\t\t<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
\t\t\t</a>
EOT;
        } else {
            return '&nbsp;';
        }
    } else {
        return '&nbsp;';
    }
}

function gLinhaCount($value)
{
    global $linhaCount;
    return '<span class="linhaCount" onclick="$(this).closest(\'tr\').trigger(\'click\');">' . $linhaCount++ . '</span>';
}

function gCorrigeTempo($valor)
{
    if (!(!is_array($valor))) {
        if (!empty($valor['campoAtual'])) {
            $valor = $valor['campoAtual'];
        } else {
            return '';
        }
    }
    $novoValor = explode(':', $valor);
    $novoValor = $novoValor[0] . ':' . $novoValor[1];
    return $novoValor;
}

function gSubstr($valor, $tamanho = 0, $ellipsis = false)
{
    if (!(!is_array($valor))) {
        if (!empty($valor['campoAtual'])) {
            $valor = $valor['campoAtual'];
        } else {
            return '';
        }
    }
    if ($ellipsis && strlen($valor) > $tamanho) {
        return substr($valor, 0, $tamanho) . '...';
    } else {
        return substr($valor, 0, $tamanho);
    }
}

function gProcessaTabs(array $tabsList, array &$tabs, bool $processAll = false)
{
    global $g, $r_titleTab;
    $tabId = md5(trim($g['r_'], '/') . '/');
    $tabsListUrl = array();
    foreach ($tabsList as $value) {
        $tabsListUrl[$value[1]] = $value;
    }
    $personalizado = array();
    if ($g['modoDesenvolvedor']) {
        $personalizado_ = Personalizacoes::processarTabs($tabId, array('desenvolvedor'));
    } else {
        if ($g['modoDesenvolvedorApp']) {
            $personalizado_ = Personalizacoes::processarTabs($tabId, array('desenvolvedor', 'apps', 'apps-editor'));
        } else {
            $personalizado_ = Personalizacoes::processarTabs($tabId);
        }
    }
    foreach ($personalizado_ as $value) {
        $personalizado[$value['url']] = $value;
        if (!($tabsListUrl[$value['url']])) {
            $tabsList[] = array(uniqid(), $value['url'], $value['titulo'], $value['parametros'], false, $value['carregarAoClicar']);
        }
    }
    $return = '';
    $count = 0;
    foreach ($tabsList as $value) {
        $tabPersonalizada = $personalizado[$value[1]];
        if ($tabPersonalizada) {
            if (!(!$tabPersonalizada['titulo'])) {
                $value[2] = $tabPersonalizada['titulo'];
            }
            if (!(isset($tabPersonalizada['exibir']) && $tabPersonalizada['exibir'] != 'S')) {
                if (!(isset($tabPersonalizada['exibirCallback']) && !executarCodigo($tabPersonalizada['exibirCallback']))) {
                }
            }
        }
        $count++;
        array_push($tabs, array('count' => $count, 'tabId' => $value[0], 'title' => $value[2], 'hide' => !$g['conf']->tabsNaoUsar($g['r'][3], $value[2])));
        if ($g['conf']->tabsNaoUsar($g['r'][3], $value[2])) {
            $vars = isset($value[3]) ? $value[3] : '';
            $vars = gRepassaRequest($vars, strpos($g['r_'], 'sistema/personalizacao/tab') !== false);
            $notProcess = isset($value[4]) ? $value[4] : false;
            $loadOnClick = isset($value[5]) && ($value[5] == 'S' || $value[5] === true) ? true : false;
            $callback = isset($value[6]) ? str_replace("'", "\\'", $value[6]) : '';
            $triggerTabShow = '';
            if ($count == 1) {
                $triggerTabShow = "moveBtnsToContent('{$value[0]}');";
            }
            array_unshift($g['divIdChain'], $value[0]);
            $divIdChain = urlencode(base64_encode(serialize($g['divIdChain'])));
            if (($count == 1 || !$notProcess && $processAll) && !$loadOnClick) {
                $bugGridEmBackground = "corrigeGrid('{$value[0]}');";
                if ($count != 1) {
                    $bugGridEmBackground = '';
                }
                $md5 = md5(trim($value[1], '/') . '/');
                $r_titleTab = explode('||', $r_titleTab);
                $r_titleTab = $r_titleTab[1];
                $titleTab = mysql_real_escape_string($r_titleTab . '||' . $value[2]);
                $return .= <<<EOT
\t\t\t<div class="divHidden"><script> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#{$value[0]}').parent().parent().showLoading();
\t\t\t\t\$('#{$value[0]}').data({
\t\t\t\t\t'divId': '{$value[0]}',
\t\t\t\t\t'divIdChain': '{$divIdChain}',
\t\t\t\t\t'divIdRoot': '{$g['divIdRoot']}',
\t\t\t\t\t'divIdParent': '{$g['divIdParent']}',
\t\t\t\t\t'divIdParentChain': '{$g['divIdParentChain_']}',
\t\t\t\t\t'vars': '{$vars}',
\t\t\t\t\t'href': '{$value[1]}',
\t\t\t\t\t'md5': '{$md5}',
\t\t\t\t\t'loaded': true,
\t\t\t\t\t'loadOnClick': '{$loadOnClick}',
\t\t\t\t\t'callback': '{$callback}',
\t\t\t\t\t'titleTab': '{$titleTab}'
\t\t\t\t});
\t\t\t\tsetTimeout(function() {
\t\t\t\t    if ('{$value[1]}'.indexOf('naocarregar') !== -1) {
\t\t\t\t        \$('#{$value[0]}').parent().parent().hideLoading();
\t\t\t\t        return;
\t\t\t\t    }
\t\t\t\t\tvar xhr = \$.ajax({
\t\t\t\t\t\turl: '{$value[1]}',
\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\tdata: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&divIdParentChain={$g['divIdParentChain_']}&divId={$value[0]}&divIdChain={$divIdChain}&titleTab={$titleTab}{$vars}',
\t\t\t\t\t\ttype: 'POST',
\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\$('#{$value[0]}').parent().parent().hideLoading();
\t\t\t\t\t\t\t\$('#{$value[0]}').html(data);
\t\t\t\t\t\t\treloadUI('{$value[0]}');
\t\t\t\t\t\t\t{$triggerTabShow}
\t\t\t\t\t\t\t{$bugGridEmBackground}
\t\t\t\t\t\t\tvar callback = '{$callback}';
\t\t\t\t\t\t\tif (!isempty(callback)) {
\t\t\t\t\t\t\t\teval(callback);
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t}
\t\t\t\t\t});
                    if (typeof requisicoesAjax['{$value[0]}'] == 'undefined') {
                        requisicoesAjax['{$value[0]}'] = [];
                    }
                    requisicoesAjax['{$value[0]}'].push(xhr);
\t\t\t\t}, {$g['lastTimeAjax']});
\t\t\t} </script></div>
EOT;
                $g['lastTimeAjax'] += 100;
            } else {
                $titleTab = $r_titleTab . '||' . $value[2];
                $titleTab = mysql_real_escape_string($titleTab);
                $vars_ = $vars . '&titleTab=' . $titleTab;
                $md5 = md5(trim($value[1], '/') . '/');
                $return .= <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript">if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#{$value[0]}').data({
\t\t\t\t\t'divId': '{$value[0]}',
\t\t\t\t\t'divIdChain': '{$divIdChain}',
\t\t\t\t\t'divIdRoot': '{$g['divIdRoot']}',
\t\t\t\t\t'divIdParent': '{$g['divIdParent']}',
\t\t\t\t\t'divIdParentChain': '{$g['divIdParentChain_']}',
\t\t\t\t\t'vars': '{$vars_}',
\t\t\t\t\t'href': '{$value[1]}',
\t\t\t\t\t'md5': '{$md5}',
\t\t\t\t\t'loaded': false,
\t\t\t\t\t'loadOnClick': '{$loadOnClick}',
\t\t\t\t\t'callback': '{$callback}',
\t\t\t\t\t'titleTab': '{$titleTab}'
\t\t\t\t});
\t\t\t} </script></div>
EOT;
            }
        } else {
            $count--;
        }
    }
    $tabsCache = apc_fetch("tabs-{$tabId}");
    if (!$tabsCache) {
        apc_store("tabs-{$tabId}", serialize($tabsList), 3600);
    }
    return $return;
}

function gProcessaAcoes($actions, $permitirEsc = true)
{
    global $g, $r_acaoId, $r_tabela, $r_titleTab;
    $return = '<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != "undefined") {';
    if (!$permitirEsc) {
        $url = '/' . trim($g['r_'], '/') . '/';
        $return .= <<<EOT
\t\t\tg.janelasIgnorarESC['{$url}'] = true;
EOT;
    }
    $pularDisplay = $actions[0][3];
    $contentId = md5(trim($g['r_'], '/') . '/');
    $objetosTemplate = is_array($g['smarty']->template_objects) ? $g['smarty']->template_objects : array();
    $ultimoTemplate = $objetosTemplate ? array_shift(array_values($objetosTemplate)) : null;
    $display = is_object($ultimoTemplate) ? $ultimoTemplate->resource_name : '';
    if (!$display) {
        $tipo = $g['smarty']->getTemplateVars('tipo');
        $contentOriginal = array('tipo' => $tipo ? $tipo : 'unico', 'url' => $actions[0][1], 'url2' => $actions[1][1], 'tamanho' => $tipo == '2linhas' ? $g['smarty']->getTemplateVars('content1Height') : $g['smarty']->getTemplateVars('content1Width'));
        if (!(!empty($contentOriginal['url2']))) {
            unset($contentOriginal['url2']);
        }
        if (!(!empty($contentOriginal['tamanho']))) {
            unset($contentOriginal['tamanho']);
        }
        $contentCache = apc_fetch("content-{$contentId}");
        if (!$contentCache) {
            apc_store("content-{$contentId}", serialize($contentOriginal), 3600);
        }
        $contentOriginal['parametros'] = $actions[0][2];
        $contentOriginal['parametros2'] = $actions[1][2];
        $contentOriginal['id'] = $actions[0][0];
        $contentOriginal['id2'] = $actions[1][0];
        $contentPersonalizado = Personalizacoes::processarContent($contentId);
        if ($g['modoDesenvolvedor']) {
            $contentPersonalizado = Personalizacoes::processarContent($contentId, array('desenvolvedor'));
        } else {
            if (!(!$g['modoDesenvolvedorApp'])) {
                $contentPersonalizado = Personalizacoes::processarContent($contentId, array('desenvolvedor', 'apps', 'apps-editor'));
            }
        }
        if (!(!$contentPersonalizado['parametros'])) {
            $contentPersonalizado['parametros'] = gSubstituirVariaveisParametros($contentPersonalizado['parametros']);
        }
        if (!(!$contentPersonalizado['parametros2'])) {
            $contentPersonalizado['parametros2'] = gSubstituirVariaveisParametros($contentPersonalizado['parametros2']);
        }
        $content = array_merge($contentOriginal, $contentPersonalizado);
        $content['id'] = $content['id'] ? $content['id'] : uniqid();
        $content['id2'] = $content['id2'] ? $content['id2'] : uniqid();
        $actions = array();
        $g['smarty']->assign('content1', $content['id']);
        $actions[] = array($content['id'], $content['url'], $content['parametros']);
        if (!(!$content['url2'])) {
            $g['smarty']->assign('content2', $content['id2']);
            $actions[] = array($content['id2'], $content['url2'], $content['parametros2']);
        }
        if (!(!$content['tamanho'])) {
            $content['tamanho'] = trim($content['tamanho'], 'px') . 'px';
            if ($content['tipo'] == '2linhas') {
                $g['smarty']->assign('content1Height', $content['tamanho']);
            } else {
                if ($content['tipo'] == '2colunas') {
                    $g['smarty']->assign('content1Width', $content['tamanho']);
                }
            }
        }
        if (!$pularDisplay) {
            if ($content['tipo'] == 'unico') {
                $g['smarty']->display('content-full.tpl');
            } else {
                if ($content['tipo'] == '2linhas') {
                    $g['smarty']->display('content-2rows.tpl');
                } else {
                    if ($content['tipo'] == '2colunas') {
                        $g['smarty']->display('content-2columns.tpl');
                    } else {
                        die("Tipo {$content['tipo']} não é uma opção válida.");
                    }
                }
            }
        }
    }
    $r_titleTab = mysql_real_escape_string($r_titleTab);
    foreach ($actions as $value) {
        $vars = isset($value[2]) ? $value[2] : '';
        $vars = gRepassaRequest($vars, strpos($g['r_'], 'sistema/personalizacao/content') !== false);
        array_unshift($g['divIdChain'], $value[0]);
        $divIdChain = urlencode(base64_encode(serialize($g['divIdChain'])));
        $md5 = md5(trim($value[1], '/') . '/');
        $return .= <<<EOT
\t\t\t\$('#{$value[0]}').showLoading();
\t\t\tvar xhr = \$.ajax({
\t\t\t\turl: '{$value[1]}',
\t\t\t\tdataType: 'html',
\t\t\t\tdata: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&divIdParentChain={$g['divIdParentChain_']}&divId={$value[0]}&divIdChain={$divIdChain}&titleTab={$r_titleTab}{$vars}',
\t\t\t\ttype: 'POST',
\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\$('#{$value[0]}').hideLoading();
\t\t\t\t\t\$('#{$g['divId']} #{$value[0]}').html(data);
\t\t\t\t\treloadUI('{$value[0]}');
\t\t\t\t\tcorrigeConteudo('{$value[0]}');
\t\t\t\t\tcorrigeGrid('{$value[0]}');
\t\t\t\t\t\$('#{$value[0]}').data({
\t\t\t\t\t\t'divId': '{$value[0]}',
\t\t\t\t\t\t'divIdChain': '{$divIdChain}',
\t\t\t\t\t\t'divIdRoot': '{$g['divIdRoot']}',
\t\t\t\t\t\t'divIdParent': '{$g['divIdParent']}',
\t\t\t\t\t\t'divIdParentChain': '{$g['divIdParentChain_']}.',
\t\t\t\t\t\t'vars': '{$vars}',
\t\t\t\t\t\t'md5': '{$md5}',
\t\t\t\t\t\t'href': '{$value[1]}'
\t\t\t\t\t});
\t\t\t\t\treturn false;
\t\t\t\t}
\t\t\t});
\t\t\tif (typeof requisicoesAjax['{$value[0]}'] == 'undefined') {
\t\t\t    requisicoesAjax['{$value[0]}'] = [];
\t\t\t}
\t\t\trequisicoesAjax['{$value[0]}'].push(xhr);
EOT;
    }
    $return .= <<<EOT
\t\t\$icon = \$("#{$g['divId']}").parent().find('.dialog-mais > .fa-bars');
EOT;
    log("Tabela = " . $r_tabela . ", r_acaoId = " . $r_acaoId);
    $return .= <<<EOT
\t\t\t\$icon.data({
\t\t\t\t'id': '{$r_acaoId}',
\t\t\t\t'table': '{$r_tabela}'
\t\t\t});
\t\t\t\$icon.data('exibirAuditoria', true);
\t\t\t\$icon.data('exibirAnterior', true);
\t\t\t\$icon.data('exibirProximo', true);
\t\t\t\$icon.data('exibirPrimeiro', true);
\t\t\t\$icon.data('exibirUltimo', true);
\t\t\t\$icon.data('exibirDuplicar', true);
\t\t\t\$icon.data('exibirBusca', true);
EOT;
    $trocarOk = false;
    $g['conf']->carregaArquivoConf($r_tabela . '_Id', 'trocarEmpresa');
    if (!(!$g['conf']->conf['trocarEmpresa']['exibir'])) {
        $trocarOk = eval("namespace hardness;\n{$g['conf']->conf['trocarEmpresa']['exibir']}");
    }
    if ($trocarOk) {
        $return .= <<<EOT
\t\t\t\t\$icon.data('exibirTrocarEmp', true);
EOT;
    }
    $return .= '} </script></div>';
    return $return;
}

function gBotaoAuditoria($from)
{
    global $g;
    $g['smarty']->assign('grid_tabela', $from);
    $g['smarty']->assign('gridTable', $from);
}

function gHabilitaNavegacao($divId, $classe = false, $method = false)
{
    global $g;
    $script = '';
    $dir = "{$g['pathDados']}personalizacaoConf/";
    $arquivoGlobal = $dir . $classe . '.PHP-alterarMetodos.php';
    $arquivoEmpresa = $dir . $g['empresaAtual'] . $classe . '.PHP-alterarMetodos.php';
    $caminho = file_exists($arquivoEmpresa) ? $arquivoEmpresa : $arquivoGlobal;
    include_once './bibliotecas/classes/' . $classe . '.php';
    $classe = "\\hardness\\{$classe}";
    $classe = new $classe();
    if (!(!in_array($method, get_class_methods($classe)))) {
        $script .= "\$('#{$divId}').closest('.ui-dialog').find('.dialog-incluir').css('display', 'inline-block');";
    }
    if (!(!in_array('duplicar_' . $method, get_class_methods($classe)))) {
        $script .= "\$('#{$divId}').closest('.ui-dialog').find('.dialog-duplicar').css('display', 'inline-block');";
    }
    if (!(!in_array('excluir_' . $method, get_class_methods($classe)))) {
        $script .= "\$('#{$divId}').closest('.ui-dialog').find('.dialog-excluir').css('display', 'inline-block');";
    }
    echo<<<EOT
\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != "undefined") {\t
\t\t\t{$script}
\t\t\tif(typeof \$('#{$divId}').data('labelPesquisa') != 'undefined' && typeof \$('#{$divId}').data('sqlPesquisa') != 'undefined'){
\t\t\t\t\$("#{$divId}").closest('.ui-dialog').find('.dialog-procurar').css('display', 'inline-block');
\t\t\t}\t\t\t
\t\t\t\$("#{$divId}"\t).closest('.ui-dialog').find('.dialog-anterior').css('display', 'inline-block');
\t\t\t\$("#{$divId}").closest('.ui-dialog').find('.dialog-primeiro').css('display', 'inline-block');
\t\t\t\$("#{$divId}").closest('.ui-dialog').find('.dialog-proximo').css('display', 'inline-block');
\t\t\t\$("#{$divId}").closest('.ui-dialog').find('.dialog-ultimo').css('display', 'inline-block');
\t\t} </script></div>
EOT;
}

function gGeraSelect($tabela, $campoId, $campoTxt, $sqlExtra = '', $first = false, $default = false, $formato = false, $firstTitle = false)
{
    $campoTxtTmp = str_ireplace('[[virgula]]', ',', $campoTxt);
    $sql = "SELECT {$campoId}, {$campoTxtTmp} FROM {$tabela} {$sqlExtra};";
    $result = mysql_query($sql);
    if (!mysql_error()) {
        $campoTxt_ = explode(',', $campoTxt);
        if ($first) {
            $firstTitle = !$firstTitle ? '...' : $firstTitle;
            $select = array(array('title' => $firstTitle, 'value' => 0));
            if (!$default) {
                $select[0]['selected'] = true;
            }
        } else {
            $select = array();
        }
        if (!empty($formato)) {
            while ($row = mysql_fetch_row($result)) {
                $cTxt = $formato;
                foreach ($row as $key => $value) {
                    if (!($key == 0)) {
                        $cTxt = str_replace("%{$key}", $value, $cTxt);
                    }
                }
                if ($default > 0 && $default == $row[0]) {
                    $select[] = array('title' => $cTxt, 'value' => $row[0], 'selected' => true);
                } else {
                    $select[] = array('title' => $cTxt, 'value' => $row[0]);
                }
            }
        } else {
            while ($row = mysql_fetch_assoc($result)) {
                if (count($campoTxt_) > 1) {
                    $cTxt = '';
                    foreach ($campoTxt_ as $value) {
                        $value = str_ireplace('[[virgula]]', ',', $value);
                        if (!(!stripos($value, ' as '))) {
                            $value = preg_split('/as/i', $value);
                            $value = trim($value[1]);
                        }
                        $cTxt .= $cTxt == '' ? $row[trim($value)] : ' - ' . $row[trim($value)];
                    }
                } else {
                    // Sem " as " o laço não terminava e a entrada estourava o tempo.
                    $nome = $campoTxt;
                    if (stripos($nome, ' as ') !== false) {
                        $partes = preg_split('/as/i', $nome);
                        $nome = trim($partes[1]);
                    }
                    $cTxt = $row[trim($nome)];
                }
                if ($default > 0 && $default == $row[$campoId]) {
                    $select[] = array('title' => $cTxt, 'value' => $row[$campoId], 'selected' => true);
                } else {
                    $select[] = array('title' => $cTxt, 'value' => $row[$campoId]);
                }
            }
        }
        if (!(empty($select))) {
            reset($select);
            $key = key($select);
            $select[$key]['gGeraSelect'] = array($tabela, $campoId, $campoTxt, $sqlExtra, $first, $default, $formato);
        }
        return $select;
    } else {
        return array(array('title' => mysql_error() . " - SQL: ({$sql})", 'value' => '', 'selected' => true));
    }
}

function gInsertExtraWhere($extraOriginal, $extra, $noFinal = false)
{
    if (!empty($extra)) {
        $extraOriginal = str_replace('where', 'WHERE', $extraOriginal);
        $extraOriginal = str_replace('order by', 'ORDER BY', $extraOriginal);
        $extraOriginal = str_replace('group by', 'GROUP BY ', $extraOriginal);
        $extraReplaceUniq = uniqid();
        $extraOriginal = str_replace('WHERE [[filtro]]', $extraReplaceUniq, $extraOriginal);
        if ($noFinal === true) {
            if (strpos($extraOriginal, 'ORDER BY') !== false) {
                if (strpos($extraOriginal, 'WHERE') !== false) {
                    $extraOriginal = str_replace('ORDER BY', " AND ({$extra}) ORDER BY", $extraOriginal);
                } else {
                    $extraOriginal = str_replace('ORDER BY', "WHERE ({$extra}) ORDER BY", $extraOriginal);
                }
            }
            if (strpos($extraOriginal, 'GROUP BY') !== false) {
                if (strpos($extraOriginal, 'WHERE') !== false) {
                    $extraOriginal = str_replace('GROUP BY', " AND ({$extra}) GROUP BY", $extraOriginal);
                } else {
                    $extraOriginal = str_replace('GROUP BY', "WHERE ({$extra}) GROUP BY", $extraOriginal);
                }
            } else {
                if (strpos($extraOriginal, 'WHERE') !== false) {
                    $extraOriginal .= " AND ({$extra}) ";
                } else {
                    $extraOriginal .= " WHERE {$extra} ";
                }
            }
        } else {
            if (strpos($extraOriginal, 'WHERE') !== false) {
                $extraOriginal = str_replace('WHERE', "WHERE ({$extra}) AND", $extraOriginal);
            } else {
                if (strpos($extraOriginal, 'GROUP BY') !== false) {
                    $extraOriginal = str_replace('GROUP BY', "WHERE ({$extra}) GROUP BY", $extraOriginal);
                } else {
                    if (strpos($extraOriginal, 'ORDER BY') !== false) {
                        $extraOriginal = str_replace('ORDER BY', "WHERE ({$extra}) ORDER BY", $extraOriginal);
                    } else {
                        $extraOriginal .= " WHERE ({$extra})";
                    }
                }
            }
        }
        $extraOriginal = str_replace($extraReplaceUniq, 'WHERE [[filtro]]', $extraOriginal);
        return ' ' . trim($extraOriginal);
    } else {
        return $extraOriginal;
    }
}

function gInsertGroupBy($extraOriginal, $groupBy)
{
    $extraOriginal = str_replace('where', 'WHERE', $extraOriginal);
    $extraOriginal = str_replace('order by', 'ORDER BY', $extraOriginal);
    $extraOriginal = str_replace('group by', 'GROUP BY ', $extraOriginal);
    $extraOriginal = str_replace('group by', 'LIMIT', $extraOriginal);
    if (strpos($extraOriginal, 'GROUP BY') !== false) {
        $posGroupBy = strpos($extraOriginal, 'GROUP BY');
        $posOrderBy = strpos($extraOriginal, 'ORDER BY');
        $posLimit = strpos($extraOriginal, 'LIMIT');
        $fimExtraOriginal = strlen($extraOriginal);
        if (strpos($extraOriginal, 'ORDER BY') !== false) {
            $extraOriginal = substr($extraOriginal, 0, $posGroupBy) . "GROUP BY {$groupBy}" . substr($extraOriginal, $posOrderBy, $fimExtraOriginal);
        } else {
            if (strpos($extraOriginal, 'LIMIT') !== false) {
                $extraOriginal = substr($extraOriginal, 0, $posGroupBy) . "GROUP BY {$groupBy}" . substr($extraOriginal, $posLimit, $fimExtraOriginal);
            } else {
                $extraOriginal = substr($extraOriginal, 0, $posGroupBy) . "GROUP BY {$groupBy}";
            }
        }
    } else {
        if (strpos($extraOriginal, 'ORDER BY') !== false) {
            $extraOriginal = str_replace('ORDER BY', "GROUP BY {$groupBy} ORDER BY", $extraOriginal);
        } else {
            if (strpos($extraOriginal, 'LIMIT') !== false) {
                $extraOriginal = str_replace('LIMIT', "GROUP BY {$groupBy} LIMIT", $extraOriginal);
            } else {
                $extraOriginal .= " GROUP BY {$groupBy}";
            }
        }
    }
    return $extraOriginal;
}

function gInsertExtraWherePrincipal($extraOriginal, $extra)
{
    $extraOriginal = str_replace('where', 'WHERE', $extraOriginal);
    $extraOriginal = str_replace('order by', 'ORDER BY', $extraOriginal);
    $extraOriginal = str_replace('group by', 'GROUP BY ', $extraOriginal);
    if (strpos($extraOriginal, 'WHERE') !== false) {
        $extraOriginal = substr_replace($extraOriginal, $extra, strpos($extraOriginal, 'WHERE'));
    } else {
        if (strpos($extraOriginal, 'GROUP BY') !== false) {
            $extraOriginal = str_replace('GROUP BY', "WHERE ({$extra}) GROUP BY", $extraOriginal);
        } else {
            if (strpos($extraOriginal, 'ORDER BY') !== false) {
                $extraOriginal = str_replace('ORDER BY', "WHERE ({$extra}) ORDER BY", $extraOriginal);
            } else {
                $extraOriginal .= " WHERE ({$extra})";
            }
        }
    }
    return $extraOriginal;
}

function gInsertExtraHaving($extraOriginal, $extra)
{
    $extraOriginal = str_replace('limit', 'LIMIT', $extraOriginal);
    $extraOriginal = str_replace('order by', 'ORDER BY', $extraOriginal);
    if (preg_match('/[^\\(]ORDER BY/', $extraOriginal, $matches)) {
        $extraOriginal = substr_replace($extraOriginal, "HAVING ({$extra}) ", strrpos($extraOriginal, 'ORDER BY'), 0);
    } else {
        if (strpos($extraOriginal, 'LIMIT') !== false) {
            $extraOriginal = str_replace('LIMIT', "HAVING ({$extra}) LIMIT", $extraOriginal);
        } else {
            $extraOriginal .= " HAVING ({$extra}) ";
        }
    }
    return $extraOriginal;
}

function gPegarParteWhere($extra, $once = false)
{
    if ($once) {
        $extra = preg_split('/WHERE/i', $extra, 2);
        $extra = $extra[1];
    } else {
        $extra = preg_split('/WHERE/i', $extra);
        $extra = $extra[count($extra) - 1];
    }
    $extra = preg_split('/GROUP BY/i', $extra, 2);
    $extra = $extra[0];
    $extra = preg_split('/HAVING/i', $extra, 2);
    $extra = $extra[0];
    $extra = preg_split('/ORDER BY/i', $extra, 2);
    $extra = $extra[0];
    return $extra;
}

function gPegarWhereAgrupamento($extra, $once = false)
{
    $extra = stristr($extra, "WHERE ((1=1");
    $extra = preg_split('/GROUP BY/i', $extra, 2);
    $extra = $extra[0];
    $extra = preg_split('/ORDER BY/i', $extra, 2);
    $extra = $extra[0];
    return $extra;
}

function gPegarParteJoin($extra)
{
    return substr($extra, 0, strrpos($extra, 'WHERE'));
    return $extra;
}

function gPegarParteGroupBy($extra)
{
    $extra = preg_split('/WHERE/i', $extra);
    $extra = $extra[count($extra) - 1];
    $extra = preg_split('/GROUP BY/i', $extra, 2);
    $extra = $extra[1];
    $extra = preg_split('/HAVING/i', $extra, 2);
    $extra = $extra[0];
    $extra = preg_split('/ORDER BY/i', $extra, 2);
    $extra = $extra[0];
    return trim($extra);
}

function gRetirarOrderBy($extra)
{
    $extra = str_replace('order by', 'ORDER BY', $extra);
    if (strpos($extra, 'ORDER BY') !== false) {
        $extra = explode('ORDER BY', $extra);
        unset($extra[count($extra) - 1]);
        $extra = implode(' ORDER BY ', $extra);
    }
    return $extra;
}

function gRetirarGroupBy($extra)
{
    $extra = strtoupper($extra);
    $extra = str_replace('group by', 'GROUP BY', $extra);
    if (strpos($extra, 'GROUP BY') !== false) {
        $extra = explode('GROUP BY', $extra);
        $extra = $extra[count($extra) - 2];
    }
    return $extra;
}

function gGeraAlertMsg($titulo = '', $msg = '', $includeJs = true)
{
    $titulo = str_replace("\"", "\\\"", $titulo);
    $msg = str_replace("\"", "\\\"", $msg);
    $msg = preg_replace("/[\r\n]+/s", "", $msg);
    $return = $includeJs ? '<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != "undefined") {' : '';
    $return .= <<<EOT
\t\tdialogConfirm("{$titulo}", "{$msg}");
EOT;
    $return .= $includeJs ? '} </script></div>' : '';
    return $return;
}

function gGeraAlertMsgErroForm($form)
{
    global $g;
    $msg = '';
    $erros = '';
    foreach ($form->formArray['campos'] as $tab => $tabCampos) {
        $first = true;
        foreach ($tabCampos as $key => $value) {
            if ($form->temErro($key)) {
                if ($first) {
                    $first = false;
                    $msg .= empty($msg) ? '' : '<br /><br />';
                    $msg .= "<b>ABA " . mb_strtoupper($tab) . ":</b><br /><br/>";
                    if ($value['type'] == 'pesquisa') {
                        $erros .= "\$('#{$g['divId']} .itemForm-{$key}_Pesquisa').trigger('focus');\n";
                    } else {
                        $erros .= "\$('#{$g['divId']} .itemForm-{$key}').trigger('focus');\n";
                    }
                }
                if ($value['type'] == 'pesquisa') {
                    $erros .= "\$('#{$g['divId']} .itemForm-{$key}_Pesquisa').addClass('form-input-error');\n";
                    $erros .= "\$('#{$g['divId']} .itemForm-{$key}_Pesquisa').addClass('ui-state-error');\n";
                } else {
                    $erros .= "\$('#{$g['divId']} .itemForm-{$key}').addClass('form-input-error');\n";
                    $erros .= "\$('#{$g['divId']} .itemForm-{$key}').addClass('ui-state-error');\n";
                }
                isset($value['label']) or $value['label'] = '';
                !empty($value['label']) or $value['label'] = $value['name'];
                $rLabel = $g['conf']->camposLabelForm($key);
                !$rLabel or $value['label'] = $rLabel;
                $msg .= "<b style='font-size: 1.3em;'>{$value['label']}</b>: <span style='font-size: 1.1em;'>{$form->pegarErro($key)}</span><br style='br' />";
            } else {
                $erros .= "\$('#{$g['divId']} .itemForm-{$key}').removeClass('form-input-error');\n";
                $erros .= "\$('#{$g['divId']} .itemForm-{$key}').removeClass('ui-state-error');\n";
            }
        }
    }
    $tmp = explode('<br', $msg);
    if (count($tmp) > 45) {
        $msg = str_replace("<br style='br' />", '&nbsp&nbsp&nbsp&nbsp;', $msg);
    }
    $return = gGeraAlertMsg('Existem erros! Por favor, verifique.', $msg . '<span id="gGeraAlertMsgErroForm" style="display: none;"></span>');
    $return .= "\n<div class='divHidden'><script type=\"text/javascript\"> if (typeof jQuery != 'undefined') { {$erros} } </script></div>";
    return $return;
}

function gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, $confirmacao = false, $form_parent = false, $form_parent_valor = false, $travar = true)
{
    global $g, $r_formEditorData, $form_editor, $formNaoRecarregar;
    global $r_modoFormLeve;
    global $r_campoRefresh;
    $g['smarty']->assign('isMobile', false);
    $arquivoIde = 'ide/personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
    $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
    $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
    if ($g['modoDesenvolvedor']) {
        $arquivo = $arquivoIde;
    } else {
        if ($g['modoDesenvolvedorApp']) {
            $arquivo = $arquivoAppsEditor;
        } else {
            $arquivo = $arquivoGlobal;
        }
    }
    if (!empty($r_formEditorData) && $g['r']['2'] != 'formDash') {
        if (isset($form_editor) && isset($form_editor[$tab]) && !empty($form_editor[$tab])) {
            $arrayNovo = array();
            $arrayOriginal = json_decode($form_editor[$tab], true);
            $arrayRecebido = json_decode($r_formEditorData, true);
            foreach ($arrayRecebido as $key => $value) {
                $diferente = false;
                if (!isset($arrayOriginal[$key])) {
                    $diferente = true;
                } else {
                    foreach ($value as $key2 => $value2) {
                        if (!isset($arrayOriginal[$key][$key2]) && isset($value2)) {
                            $diferente = true;
                        } else {
                            $valor = round($value2, 2);
                            $valorOriginal = round($arrayOriginal[$key][$key2], 2);
                            if (($key2 == 'hidden')) {
                                $limite = 0;
                            } elseif (($key2 == 'top')) {
                                $limite = 2;
                            } else {
                                $limite = 5;
                            }
                            $valor = abs($valor - $valorOriginal);
                            if ($valor > $limite) {
                                $diferente = true;
                            }
                        }
                    }
                }
                if ($diferente) {
                    $arrayNovo[$key] = $value;
                }
            }
            if (empty($arrayNovo)) {
                unlink($arquivo);
            } else {
                file_put_contents($arquivo, json_encode($arrayNovo));
            }
        } else {
            file_put_contents($arquivo, $r_formEditorData);
        }
    }
    $arquivoIde = 'ide/personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
    $arquivoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
    $arquivoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
    $arquivoGlobal = "{$g['pathDados']}personalizacaoForm/" . 'formEditorData-' . str_replace('/', '-', trim($g['r_'], '/')) . "-{$tab}";
    $arquivo = $arquivoIde;
    if (!$g['modoDesenvolvedor'] && file_exists($arquivoApps)) {
        $arquivo = $arquivoApps;
    }
    if ($g['modoDesenvolvedorApp'] && file_exists($arquivoAppsEditor)) {
        $arquivo = $arquivoAppsEditor;
    }
    if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal)) {
        $arquivo = $arquivoGlobal;
    }
    $formEditorData = array();
    $formEditorUtilizado = false;
    if (!(!file_exists($arquivo))) {
        $formEditorData = json_decode(file_get_contents($arquivo), true);
        foreach ($formEditorData as $key => $value) {
            $formEditorData[$key]['alterado'] = true;
        }
        $formEditorUtilizado = true;
    }
    if (isset($form_editor) && isset($form_editor[$tab]) && !empty($form_editor[$tab])) {
        if (!empty($formEditorData)) {
            $formEditorData_ = json_decode($form_editor[$tab], true);
            $formEditorData = array_merge($formEditorData_, $formEditorData);
        } else {
            $formEditorData = json_decode($form_editor[$tab], true);
        }
        $formEditorUtilizado = true;
    }
    $return = array('table' => array(), 'pagina' => '');
    $disabled = false;
    if ($travar) {
        $operacao = empty($acaoId) ? 'incluir' : 'alterar';
        $restricao = $g['conf']->restricaoTravar($operacao, $form_campo_pk, $acaoId, $form_parent, $form_parent_valor);
        if ($restricao !== true) {
            $disabled = true;
        }
    }
    $table = array();
    $tabCount = 0;
    $tabTotal = count($form->formArray['campos']);
    $editorPosAuto = array('top' => - 16, 'left' => 5, 'hidden' => 0);
    foreach ($form->formArray['campos'] as $tabCampo => $tabCampos) {
        $tabCount++;
        foreach ($tabCampos as $key => $value) {
            if ($tabCampo == $tab) {
                if ($value['type'] == 'hidden') {
                    $value['displayType'] = 'hidden';
                } else {
                    $value['displayType'] = 'normal';
                }
                $form->formArray['campos'][$tabCampo][$key]['displayType'] = $value['displayType'];
                $value['label'] = isset($value['label']) ? $value['label'] : false;
                $rLabel = $g['conf']->camposLabelForm($key);
                !$rLabel or $value['label'] = $rLabel;
                if (!isset($value['restricao']) || $value['restricao']) {
                    $value['label'] = $g['conf']->restricaoVerificaIncluirUpdate($key) && $value['type'] != 'hidden' && !empty($value['label']) ? '* ' . $value['label'] : $value['label'];
                }
                if ($value['label'][0] != '*') {
                    $arqPerCallbackRestricaoIde = 'ide/personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $key . '-callbackRestricao';
                    $arqPerCallbackRestricaoApps = Apps::getDirAtivos() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $key . '-callbackRestricao';
                    $arqPerCallbackRestricaoAppsEditor = Apps::getDirEditor() . 'personalizacaoForm/' . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $key . '-callbackRestricao';
                    $arqPerCallbackRestricaoGlobal = "{$g['pathDados']}personalizacaoForm/" . str_replace('/', '-', trim(@$g['r_'], '/')) . '-' . $key . '-callbackRestricao';
                    $arqPerCallbackRestricao = $arqPerCallbackRestricaoIde;
                    if (!$g['modoDesenvolvedor'] && file_exists($arqPerCallbackRestricaoApps)) {
                        $arqPerCallbackRestricao = $arqPerCallbackRestricaoApps;
                    }
                    if ($g['modoDesenvolvedorApp'] && file_exists($arqPerCallbackRestricaoAppsEditor)) {
                        $arqPerCallbackRestricao = $arqPerCallbackRestricaoAppsEditor;
                    }
                    if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arqPerCallbackRestricaoGlobal)) {
                        $arqPerCallbackRestricao = $arqPerCallbackRestricaoGlobal;
                    }
                    if (file_exists($arqPerCallbackRestricao) || $value['callbackRestricao']) {
                        $value['label'] = '* ' . $value['label'];
                    }
                }
                $value['label'] = $value['type'] == 'hidden' ? '' : $value['label'];
                $alterado = false;
                if (!isset($formEditorData[$key])) {
                    $value['type'] == 'hidden' or $editorPosAuto['top'] += 23;
                    $editorPos = $editorPosAuto;
                    if ($formEditorUtilizado) {
                        $editorPos['hidden'] = 1;
                    }
                } else {
                    $editorPos = $formEditorData[$key];
                    if (!(!isset($formEditorData[$key]['alterado']))) {
                        $alterado = true;
                    }
                }
                if (!(!isset($editorPos['width']))) {
                    isset($form->formArray['campos'][$tabCampo][$key]['style']) or $form->formArray['campos'][$tabCampo][$key]['style'] = '';
                    $form->formArray['campos'][$tabCampo][$key]['style'] = "width: {$editorPos['width']}px;" . $form->formArray['campos'][$tabCampo][$key]['style'];
                }
                if (!(!isset($editorPos['height']))) {
                    isset($form->formArray['campos'][$tabCampo][$key]['style']) or $form->formArray['campos'][$tabCampo][$key]['style'] = '';
                    $form->formArray['campos'][$tabCampo][$key]['style'] = "height: {$editorPos['height']}px;" . $form->formArray['campos'][$tabCampo][$key]['style'];
                }
                if (!(isset($editorPos['hidden']))) {
                    $editorPos['hidden'] = 0;
                }
                if (trim($value['label']) && strpos($value['label'], '<a') === false) {
                    $colunaValorBase64 = $value['name'];
                    $titulo = trim(strip_tags($value['label']), '* ');
                    $formLabelClass = $_SESSION['device'] == 'mobile' ? 'tw-form-label' : '';
                    $value['label'] = "<span class=\"form-rotulo {$formLabelClass}\"  style=\"cursor: pointer;\" onclick=\"wikiAjuda('{$g['divId']}', 'FORM', 'CAMPO', '{$colunaValorBase64}', '{$titulo}');\">{$value['label']}</span>";
                }
                $tailwind['columns'] = '';
                if (in_array($value['type'], ['button', 'submit'])) {
                    $tailwind['columns'] = 'tw-w-auto';
                } else {
                    $tailwind['columns'] = '@3xl:tw-w-1/2 tw-w-full';
                }
                $cell = array('id' => $key, 'label' => $value['label'], 'name' => $value['name'], 'value' => $form->campoCodigoHTML($key, isset($r_modoFormLeve) && $r_modoFormLeve && $acaoId ? true : $disabled, $alterado, $enviar_btnId), 'align' => isset($value['align']) ? $value['align'] : false, 'style' => isset($value['style']) ? $value['style'] : false, 'type' => $value['type'], 'editorPos' => $editorPos, 'tabindex' => (($value['type'] == 'button' || $value['type'] == 'submit' ? 2 : 0) + $editorPos['top']) * 1000 + $editorPos['left'], 'hidden' => $editorPos['hidden'], 'columns' => $tailwind['columns']);
                array_push($return['table'], $cell);
            }
        }
        if ($tabCampo == $tab && $tabCount == 1) {
            $return['pagina'] .= '<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != "undefined") { ';
            if (is_array($enviar_btnId)) {
                $btnAdd = isset($enviar_btnId[0]) ? $enviar_btnId[0] : '';
                $btnCln = isset($enviar_btnId[1]) ? $enviar_btnId[1] : '';
                $btnNew = isset($enviar_btnId[2]) ? $enviar_btnId[2] : '';
            } else {
                $btnAdd = $enviar_btnId;
                $btnCln = '';
                $btnNew = '';
            }
            if ($disabled) {
                $return['pagina'] .= <<<EOT
\t\t\t\t\t\$("#{$btnAdd}").unbind('click.enviaForm').css('opacity', '0.5');
\t\t\t\t\t\$("#{$btnCln}").unbind('click.limpaForm').css('opacity', '0.5');
EOT;
            } else {
                if (isset($r_modoFormLeve) && $r_modoFormLeve && $acaoId) {
                    $return['pagina'] .= <<<EOT
\t\t\t\t\t\$("#{$btnAdd}").unbind('click.enviaForm').bind('click.enviaForm', function() {
\t\t\t\t\t\tdivSetarAcaoId('{$g['divId']}', '', 'modoFormLeve');
\t\t\t\t\t\tdivRefresh('{$g['divId']}');
\t\t\t\t\t\t\$(this).find('span').css('font-weight', 'normal').html('Enviar');
\t\t\t\t\t}).find('span').css('font-weight', 'bold').html('Habilitar Edição');
\t\t\t\t\t\$("#{$btnCln}").unbind('click.limpaForm').css('opacity', '0.5');
EOT;
                } else {
                    if (!(!$form->verificaSeFoiEnviado())) {
                        $return['pagina'] .= "\$('#{$g['divId']}').find('> form > .buttonsBottom').remove();";
                    }
                    if (!(empty($btnAdd))) {
                        $func = uniqid();
                        $target = "target:     '#{$g['divId']}', ";
                        $reload = "reloadUI('{$g['divId']}');";
                        $extra = '';
                        if (!(!isset($formNaoRecarregar))) {
                            $target = '';
                            $reload = '';
                            $extra = <<<EOT
\t\t\t\t\t\t\t// CTRL + S
\t\t\t\t\t\t\t\$("#{$btnAdd}").addClass('ctrlsbtn');
EOT;
                        }
                        $acaoFunc = <<<EOT
\t\t\t\t\tfunction func{$func}() {
\t\t\t\t\t\tif (\$("#{$btnAdd}").data('processando') != 1) {
\t\t\t\t\t\t\t\$("#{$btnAdd}").data('processando', 1);

\t\t\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t\t\tvar fechaForm = \$('#{$g['divId']}').data('fechaForm');
\t\t\t\t\t\t\tif (typeof(fechaForm) == 'undefined') { fechaForm = 1; }
\t\t\t\t\t\t\tvar data = {'ajax': true, 'divIdRoot': '{$g['divIdRoot']}', 'divIdParent': '{$g['divIdParent']}', 'divIdParentChain': '{$g['divIdParentChain_']}', 'divId': '{$g['divId']}', 'divIdChain': '{$g['divIdChain_']}', 'tab': '{$tabCampo}', 'fechaForm': fechaForm}; // 'acaoId': '{$acaoId}'
\t\t\t\t\t\t\tvar vars = \$('#{$g['divId']}').data('vars');
\t\t\t\t\t\t\tif (typeof(vars) != 'undefined') {
\t\t\t\t\t\t\t\tvar vars_ = vars.split('&');
\t\t\t\t\t\t\t\t\$.each(vars_, function(index, value) {
\t\t\t\t\t\t\t\t\tif (isempty(value)) { return true; }
\t\t\t\t\t\t\t\t\tif (value == 'tab') { return true; }
\t\t\t\t\t\t\t\t\tvar param = value.split('=');
\t\t\t\t\t\t\t\t\tdata[param[0]] = param[1];
\t\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t// Form via IFRAME
\t\t\t\t\t\t\tvar options = { 
\t\t\t\t\t\t\t\t{$target}
\t\t\t\t\t\t\t\turl:        '{$enviar_acaoHref}',
\t\t\t\t\t\t\t\ttype:\t\t'POST',
\t\t\t\t\t\t\t\tdata:\t\tdata,
\t\t\t\t\t\t\t\tsuccess:\tfunction(data) {
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t\t\t/*var re = /<script\\b[^>]*>([\\s\\S]*?)<\\/script>/gim;
\t\t\t\t\t\t\t\t\tvar match;
\t\t\t\t\t\t\t\t\twhile (match = re.exec(data)) {
\t\t\t\t\t\t\t\t\t\teval(match[1]);
\t\t\t\t\t\t\t\t\t}*/
\t\t\t\t\t\t\t\t\t{$reload}
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']}').find('> .buttonsBottom').each( function() {
\t\t\t\t\t\t\t\t\t\tif (typeof(\$(this).attr('id')) != 'undefined') {
\t\t\t\t\t\t\t\t\t\t\tmoveBtnsToContent('{$g['divId']}');
\t\t\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\t\t/*\$('#{$g['divId']}').data({
\t\t\t\t\t\t\t\t\t\t'divId': '{$g['divId']}',
\t\t\t\t\t\t\t\t\t\t'divIdChain': '{$g['divIdChain_']}',
\t\t\t\t\t\t\t\t\t\t'divIdRoot': '{$g['divIdRoot']}',
\t\t\t\t\t\t\t\t\t\t'divIdParent': '{$g['divIdParent']}',
\t\t\t\t\t\t\t\t\t\t'divIdParentChain': '{$g['divIdParentChain_']}',
\t\t\t\t\t\t\t\t\t\t'vars': '&acaoId={$acaoId}&tab={$tabCampo}',
\t\t\t\t\t\t\t\t\t\t'href': '{$enviar_acaoHref}'
\t\t\t\t\t\t\t\t\t});*/
\t\t\t\t\t\t\t\t\t\$("#{$btnAdd}").data('processando', 0);
\t\t\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t};
\t\t\t\t\t\t\t\$('#{$formId}').ajaxSubmit(options);
\t\t\t\t\t\t} else {
\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t}
\t\t\t\t\t}
\t\t\t\t\t// pass options to ajaxForm 
\t\t\t\t\t\$('#{$formId}').data('func', 'func{$func}');
\t\t\t\t\t\$('#{$formId}').data('formNome', '{$form->formNome}');
\t\t\t\t\t{$extra}
\t\t\t\t\t
\t\t\t\t\t// para poder chamar o submit via trigger
\t\t\t\t\t\$('#{$formId}').bind('enviarForm', func{$func});
\t\t\t\t\t\t
\t\t\t\t\t/*
\t\t\t\t\t// Versão antigo (não é via iframe)
\t\t\t\t\t\$.ajax({
\t\t\t\t\t\turl: '{$enviar_acaoHref}',
\t\t\t\t\t\tdataType: 'html',
\t\t\t\t\t\ttype: 'POST',
\t\t\t\t\t\tcache: false,
\t\t\t\t\t\tdata: 'ajax=true&divIdRoot={$g['divIdRoot']}&divIdParent={$g['divIdParent']}&divIdParentChain={$g['divIdParentChain_']}&divId={$g['divId']}&divIdChain={$g['divIdChain_']}&acaoId={$acaoId}&tab={$tabCampo}&' + \$("#{$formId}").serialize(),
\t\t\t\t\t\tsuccess: function(data) {
\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\t\$("#{$btnAdd}").data('processando', 0);
\t\t\t\t\t\t\t\$('#{$g['divId']}').html(data);
\t\t\t\t\t\t\treloadUI('{$g['divId']}');
\t\t\t\t\t\t\t\$('#{$g['divId']}').find('> .buttonsBottom').each( function() {
\t\t\t\t\t\t\t\tif (typeof(\$(this).attr('id')) != 'undefined') {
\t\t\t\t\t\t\t\t\tmoveBtnsToContent('{$g['divId']}');
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t\$('#{$g['divId']}').data({
\t\t\t\t\t\t\t\t'divId': '{$g['divId']}',
\t\t\t\t\t\t\t\t'divIdChain': '{$g['divIdChain_']}',
\t\t\t\t\t\t\t\t'divIdRoot': '{$g['divIdRoot']}',
\t\t\t\t\t\t\t\t'divIdParent': '{$g['divIdParent']}',
\t\t\t\t\t\t\t\t'divIdParentChain': '{$g['divIdParentChain_']}',
\t\t\t\t\t\t\t\t'vars': '&acaoId={$acaoId}&tab={$tabCampo}',
\t\t\t\t\t\t\t\t'href': '{$enviar_acaoHref}'
\t\t\t\t\t\t\t});
\t\t\t\t\t\t\treturn false;
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t\t*/
EOT;
                        $acao = "func{$func}();";
                        if (!(empty($confirmacao))) {
                            $confirmacao = addslashes($confirmacao);
                            $acao = <<<EOT
\t\t\t\t\t\t\tvar \$buttons = {
\t\t\t\t\t\t\t\t"Sim": function() {
\t\t\t\t\t\t\t\t\t{$acao}
\t\t\t\t\t\t\t\t\t\$(this).dialog("destroy").remove();
\t\t\t\t\t\t\t\t},
\t\t\t\t\t\t\t\t"Não": function() { \$(this).dialog("destroy").remove(); }
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\tdialogConfirm('Atenção!', "{$confirmacao}", \$buttons);
EOT;
                        }
                        $return['pagina'] .= <<<EOT
\t\t\t\t\t\t{$acaoFunc}
\t\t\t\t\t\t\$("#{$btnAdd}").unbind('click.enviaForm'); // evita eventos duplicados
\t\t\t\t\t\t\$("#{$btnAdd}").bind('click.enviaForm', function() {
\t\t\t\t\t\t\t{$acao}
\t\t\t\t\t\t\treturn false; // preventDefault() & stopPropagation()
\t\t\t\t\t\t});
EOT;
                    }
                    if (!(empty($btnCln))) {
                        $return['pagina'] .= <<<EOT
\t\t\t\t\t\t\$("#{$btnCln}").unbind('click.limpaForm'); // evita eventos duplicados
\t\t\t\t\t\t\$("#{$btnCln}").bind('click.limpaForm', function() {
\t\t\t\t\t\t\t\$('#{$g['divId']}').find('form').limpaForm();
\t\t\t\t\t\t\treturn false; // preventDefault() & stopPropagation()
\t\t\t\t\t\t});
\t\t\t\t\t\t
EOT;
                    }
                    if (!(empty($btnNew))) {
                        $return['pagina'] .= <<<EOT
\t\t\t\t\t\t\$("#{$btnNew}").unbind('click.limpaForm'); // evita eventos duplicados
\t\t\t\t\t\t\$("#{$btnNew}").bind('click.limpaForm', function() {
\t\t\t\t\t\t\tdivSetarAcaoId('{$g['divId']}', '');
\t\t\t\t\t\t\tdivRefresh('{$g['divId']}', false);
\t\t\t\t\t\t\treturn false; // preventDefault() & stopPropagation()
\t\t\t\t\t\t});
\t\t\t\t\t\t
EOT;
                    }
                }
            }
            $return['pagina'] .= "\n} </script></div>\n";
        }
    }
    if ($formEditorUtilizado) {
        usort($return['table'], function($a, $b) {
            if (!($a['tabindex'] == $b['tabindex'])) {
                return $a['tabindex'] < $b['tabindex'] ? - 1 : 1;
            } else {
                return 0;
            }
        });
    }
    if (!$r_campoRefresh) {
        return $return;
    } else {
        ob_end_clean();
        foreach ($return['table'] as $campo) {
            if ($campo['id'] == $r_campoRefresh) {
                echo $campo['value'];
            }
        }
        die;
    }
}

function gProcessaSelect($options)
{
    $selectOptions = '';
    if ($options) {
        foreach ($options as $option) {
            if ($option['title'] == 'optgroup') {
                $selectOptions .= '<optgroup label="' . $option['value'] . '"></optgroup>';
            } else {
                $tmp = '';
                $tmp .= isset($option['value']) ? "value=\"{$option['value']}\" " : '';
                $tmp .= isset($option['tooltip']) ? "title=\"{$option['tooltip']}\" " : '';
                $tmp .= isset($option['selected']) && $option['selected'] ? 'selected="selected" ' : '';
                $selectOptions .= '<option ' . trim($tmp) . ">{$option['title']}</option>";
            }
        }
    }
    return $selectOptions;
}

function gProcessaDocumento($templateNome)
{
    global $g, $confUsuario;
    $template = $g['pathRaiz'] . 'configuracoes/configuracoesPorBanco/' . $confUsuario['dbDatabase'] . '/documentos/' . $templateNome;
    if (!(file_exists($template))) {
        $template = $g['pathRaiz'] . 'configuracoes/documentos/' . $templateNome;
    }
    $tmp = file_get_contents($template);
    preg_match('/<style.*>(.*)<\\/style>/sm', $tmp, $matches);
    if (!(!isset($matches))) {
        $toReplace = $matches[0];
        $css = $matches[1];
        $css = preg_replace("/\n/sm", " /\n", trim($css));
        $css = <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\$('head').append(' \\
\t\t<style type="text/css"> \\
\t\t\t{$css} \\
\t\t</style> \\
\t');
} </script></div>
EOT;
        $tmp = str_replace($toReplace, $css, $tmp);
    }
    return $g['smarty']->fetch($template);
}

function mysql_query($query, $executarTriggersPHP = true)
{
    global $g, $confUsuario;
    gVerificarConexao();
    $query = str_replace("\t", '', trim($query));
    $tmpTimeStart = microtime(true);
    session_start();
    preg_match('/FROM ([^ ]+) /Uis', $query, $tabela);
    if (!(!empty($tabela))) {
        preg_match('/UPDATE ([^ ]+) /Uis', $query, $tabela);
        if (!(!empty($tabela))) {
            preg_match('/INTO ([^ ]+) /Uis', $query, $tabela);
            if (!empty($tabela)) {
            } else {
                $mysql = mysqli_query($query);
                $tmpTimeEnd = microtime(true);
                $g['time']['sql'] += $tmpTimeEnd - $tmpTimeStart;
                return $mysql;
            }
        }
    }
    $t = $tabela;
    $tabela = $tabela[1];
    preg_match('/ *([^ ]+) /Uis', $query, $acao);
    $acao = $acao[1];
    if (stristr($acao, 'SELECT')) {
        $acao = 'S';
    } else {
        if (stristr($acao, 'INSERT')) {
            $acao = 'I';
        } else {
            if (stristr($acao, 'UPDATE')) {
                $acao = 'U';
            } else {
                if (!(!stristr($acao, 'DELETE'))) {
                    $acao = 'D';
                }
            }
        }
    }
    $T162_Registro_Id = 0;
    $T162_Registro = array();
    $tabelaAcaoId = 0;
    $T162_Registro_Tabela = $T162_Registro_CampoId = '';
    if ($acao == 'U' || $acao == 'D') {
        preg_match('/WHERE ?([^ =]+) ?= ?(\'|")?([0-9]+)(\'|")?/im', $query, $matches);
        if (!isset($matches[1]) || $matches[1] !== 0 && empty($matches[1])) {
        } else {
            $T162_Registro_CampoId = $matches[1];
            $T162_Registro_Tabela = explode('_', $T162_Registro_CampoId);
            $T162_Registro_Tabela = $T162_Registro_Tabela[0];
            $T162_Registro_Id = $matches[3];
            $tabelaAcaoId = $matches[3];
            $sql = "SELECT * FROM {$T162_Registro_Tabela} WHERE {$T162_Registro_CampoId} = '{$T162_Registro_Id}';";
            $resT162Registro = mysqli_query($sql);
            if ($resT162Registro) {
                if (mysql_num_rows($resT162Registro) == 1) {
                    $T162_Registro = @mysql_fetch_assoc($resT162Registro);
                }
            }
        }
    } else {
        if ($acao == 'I') {
            preg_match('/INSERT INTO (?<tabela>[^A]+A) \\((?<fk>[^,]+), (?<campo>[^\\)]+)\\) VALUES \\((?<id>[^,]+), [^\\)]+\\) ON DUPLICATE KEY UPDATE.*/Uis', $query, $tabelaAuxiliar);
            $tabelaAuxiliarId = trim($tabelaAuxiliar['id'], "'");
            if (!(empty($tabelaAuxiliarId))) {
                $tabela = $tabelaAuxiliar['tabela'];
                $tabelaAuxiliarRegistro = mysql_fetch_assoc(mysqli_query("SELECT {$tabelaAuxiliar['tabela']}_Id FROM {$tabelaAuxiliar['tabela']} WHERE {$tabelaAuxiliar['fk']} = '{$tabelaAuxiliarId}' AND {$tabelaAuxiliar['campo']} IS NOT NULL"));
                if (!(empty($tabelaAuxiliarRegistro))) {
                    $acao = 'U';
                    $tabelaAcaoId = $tabelaAuxiliarRegistro["{$tabelaAuxiliar['tabela']}_Id"];
                }
            }
        }
    }
    if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
        $h = fopen("/tmp/debug-{$_COOKIE['debug']}", 'a');
    }
    if ($executarTriggersPHP) {
        if ($acao == 'I') {
            if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                $log = "<br /><span style='color: blue; font-weight: bold;'>Trigger 'insertBefore' ativada! ({$tabela})</span><span style='color: gray;'> ({$query})</span><br />";
                $log .= "<span style='color: green; font-weight: bold;'>Início trigger 'insertBefore' ({$tabela})</span><br />";
                fwrite($h, $log . "\n");
            }
            $g['conf']->triggers($tabela, 'insertBefore');
            if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                $log = "<span style='color: green; font-weight: bold;'>Fim trigger 'insertBefore' ({$tabela})</span><br />";
                fwrite($h, $log . "\n");
            }
        } else {
            if ($acao == 'U') {
                if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                    $log = "<br /><span style='color: blue; font-weight: bold;'>Trigger 'updateBefore' ativada! ({$tabela})</span><span style='color: gray;'> ({$query})</span><br />";
                    $log .= "<span style='color: green; font-weight: bold;'>Início trigger 'updateBefore' ({$tabela}) ({$tabelaAcaoId})</span><br />";
                    fwrite($h, $log . "\n");
                }
                $g['conf']->triggers($tabela, 'updateBefore', $tabelaAcaoId);
                if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                    $log = "<span style='color: green; font-weight: bold;'>Fim trigger 'updateBefore' ({$tabela})</span><br />";
                    fwrite($h, $log . "\n");
                }
            } else {
                if ($acao == 'D') {
                    if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                        $log = "<br /><span style='color: blue; font-weight: bold;'>Trigger 'deleteBefore' ativada! ({$tabela})</span><span style='color: gray;'> ({$query})</span><br />";
                        $log .= "<span style='color: green; font-weight: bold;'>Início trigger 'deleteBefore' ({$tabela}) ({$tabelaAcaoId})</span><br />";
                        fwrite($h, $log . "\n");
                    }
                    $g['conf']->triggers($tabela, 'deleteBefore', $tabelaAcaoId);
                    if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                        $log = "<span style='color: green; font-weight: bold;'>Fim trigger 'deleteBefore' ({$tabela})</span><br />";
                        fwrite($h, $log . "\n");
                    }
                }
            }
        }
    }
    $time_start = microtime(true);
    $retorno = mysqli_query($query);
    if ($retorno) {
        if ($acao == 'I') {
            $g['mysqlLastId'] = mysql_insert_id();
        }
        $g['mysqlAffectedRows'] = mysql_affected_rows();
        $time_end = microtime(true);
        $time = round($time_end - $time_start, 2);
        if (in_array($tabela, $g['tabelasCache']) && $acao != 'S') {
            processarCacheTabela($tabela);
        }
        $T162_Extra = '';
        $debug = debug_backtrace();
        if (!(!isset($debug[0]))) {
            if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                if ($time < 1) {
                    $tempo = "<span style='color: grey;'>{$time}s</span>";
                } else {
                    if ($time < 3) {
                        $tempo = "<span style='color: blue;'>{$time}s</span>";
                    } else {
                        $tempo = "<span style='color: red; font-weight: bold;'>{$time}s</span>";
                    }
                }
                if (strpos($debug[0]['file'], "NOTIFICACOES") === false) {
                    $log = "<span style='color: gray; float: right;'>{$debug[0]['file']}:{$debug[0]['line']}</span><br />({$time}) <span style='color: var(--letra-log);'>{$query}</span><br />";
                    fwrite($h, $log . "\n");
                    $_SESSION['debugSqlCounter']++;
                }
            }
            $T162_Extra .= "{$debug[0]['file']}:{$debug[0]['line']}";
        }
        unset($debug);
        if ($executarTriggersPHP) {
            if ($acao == 'I') {
                if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                    $log = "<br /><span style='color: blue; font-weight: bold;'>Trigger 'insertAfter' ativada! ({$tabela})</span><span style='color: gray;'> ({$query})</span><br />";
                    $log .= "<span style='color: green; font-weight: bold;'>Início trigger 'insertAfter' ({$tabela}) ({$g['mysqlLastId']})</span><br />";
                    fwrite($h, $log . "\n");
                }
                $g['conf']->triggers($tabela, 'insertAfter', $g['mysqlLastId']);
                if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                    $log = "<span style='color: green; font-weight: bold;'>Fim trigger 'insertAfter' ({$tabela})</span><br />";
                    fwrite($h, $log . "\n");
                }
            } else {
                if ($acao == 'U') {
                    if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                        $log = "<br /><span style='color: blue; font-weight: bold;'>Trigger 'updateAfter' ativada! ({$tabela})</span><span style='color: gray;'> ({$query})</span><br />";
                        $log .= "<span style='color: green; font-weight: bold;'>Início trigger 'updateAfter' ({$tabela}) ({$tabelaAcaoId})</span><br />";
                        fwrite($h, $log . "\n");
                    }
                    $g['conf']->triggers($tabela, 'updateAfter', $tabelaAcaoId);
                    if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                        $log = "<span style='color: green; font-weight: bold;'>Fim trigger 'updateAfter' ({$tabela})</span><br />";
                        fwrite($h, $log . "\n");
                    }
                } else {
                    if ($acao == 'D') {
                        if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                            $log = "<br /><span style='color: blue; font-weight: bold;'>Trigger 'deleteAfter' ativada! ({$tabela})</span><span style='color: gray;'> ({$query})</span><br />";
                            $log .= "<span style='color: green; font-weight: bold;'>Início trigger 'deleteAfter' ({$tabela}) ({$tabelaAcaoId})</span><br />";
                            fwrite($h, $log . "\n");
                        }
                        $g['conf']->triggers($tabela, 'deleteAfter', $tabelaAcaoId);
                        if (isset($_COOKIE['debug']) && !empty($_COOKIE['debug'])) {
                            $log = "<span style='color: green; font-weight: bold;'>Fim trigger 'deleteAfter' ({$tabela})</span><br />";
                            fwrite($h, $log . "\n");
                        }
                    }
                }
            }
        }
        if ($acao == 'I') {
            $T162_Registro_Id = $g['mysqlLastId'];
        }
        if ($acao != 'S' && (!$g['tabelasAuditoria'][strtoupper($tabela)] || $g['tabelasAuditoria'][strtoupper($tabela)] == 'S')) {
            $arrayAlterados = array();
            if ($acao != 'D' && $acao == 'U' && !empty($T162_Registro)) {
                $T162_Registro_Depois = array();
                $sql = "SELECT * FROM {$T162_Registro_Tabela} WHERE {$T162_Registro_CampoId} = '{$T162_Registro_Id}';";
                $resT162RegistroDepois = mysqli_query($sql);
                if ($resT162RegistroDepois) {
                    if (mysql_num_rows($resT162RegistroDepois) == 1) {
                        $T162_Registro_Depois = @mysql_fetch_assoc($resT162RegistroDepois);
                    }
                }
                foreach ($T162_Registro_Depois as $keyDepois => $valueDepois) {
                    if ($T162_Registro[$keyDepois] != $valueDepois) {
                        $arrayAlterados[$keyDepois] = array($T162_Registro[$keyDepois], $valueDepois);
                    }
                }
            }
            if ($acao != 'U' || !empty($arrayAlterados)) {
                $query = mysql_real_escape_string($query);
                if ($acao == 'D') {
                    $arrayAlterados = $T162_Registro;
                }
                $arrayAlterados = mysql_real_escape_string(serialize($arrayAlterados), $g['conexaoBancoAuditoria']);
                $T162_Extra = mysql_real_escape_string($T162_Extra, $g['conexaoBancoAuditoria']);
                $sql = "INSERT INTO T162 (T162_C007_Id, T162_Acao, T162_Query, T162_DataHora, T162_Tabela, T162_TempoExecucao, T162_Registro_Id, T162_Registro, T162_IP) VALUES ('{$g['usuarioAtual']}', '{$acao}', '{$T162_Extra}', '" . date('Y-m-d H:i:s') . "', '{$tabela}', '{$time}', '{$T162_Registro_Id}', COMPRESS('{$arrayAlterados}'), '{$_SERVER['REMOTE_ADDR']}');";
                mysqli_query($sql, $g['conexaoBancoAuditoria']);
                $g['conf']->auditoriaCampos("{$tabela}_Id");
                if (!(!isset($g['conf']->conf['auditoriaCampos']))) {
                    foreach ($g['conf']->conf['auditoriaCampos'] as $value) {
                        if (strpos($value, $tabela) !== false) {
                            gExplodeAuditoria($value, mysql_insert_id($g['conexaoBancoAuditoria']));
                        }
                    }
                }
            }
        }
        $tmpTimeEnd = microtime(true);
        $g['time']['sql'] += $tmpTimeEnd - $tmpTimeStart;
        return $retorno;
    } else {
        return $retorno;
    }
}

function mysqli_query($query, $conexao = false)
{
    global $g, $confUsuario;
    if (!($g['conexaoBanco'])) {
        $g['conexaoBanco'] = mysqli_init();
        if ($g['conexaoBanco']) {
            $g['conexaoBanco']->options(MYSQLI_OPT_CONNECT_TIMEOUT, 240);
            $g['conexaoBanco']->options(MYSQLI_OPT_READ_TIMEOUT, 240);
            $g['conexaoBanco']->real_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
        } else {
            die("mysqli_init failed");
        }
    }
    unset($g['conexaoLastQuery']);
    if ($conexao === false) {
        $conexao = $g['conexaoBanco'];
    }
    if (stripos($query, '/* ClickHouse */') !== false) {
        verificarConexaoClickhouse();
        if (!(stripos($query, '/* ClickHouse */') !== false && !isset($g['conexaoBancoClickHouse']))) {
            $conexao = $g['conexaoBancoClickHouse'];
            $g['conexaoLastQuery'] = $conexao;
        } else {
            die('Conexão ClickHouse não configurada (bibliotecas/conexaoBancoClickHouse.php), por favor, contate o suporte técnico.');
        }
    }
    return \mysqli_query($conexao, $query);
}

function mysql_result($query, $field = 0)
{
    global $g;
    $row = mysql_fetch_array($query);
    return $row[$field];
}

function mysql_insert_id($conexao = false)
{
    global $g;
    if ($conexao === false) {
        $conexao = $g['conexaoBanco'];
    }
    $retorno = \mysqli_insert_id($conexao);
    return $retorno;
}

function mysqli_insert_id($conexao = false)
{
    global $g;
    if ($conexao === false) {
        $conexao = $g['conexaoBanco'];
    }
    $retorno = \mysqli_insert_id($conexao);
    return $retorno;
}

function mysql_real_escape_string($string, $conexao = false)
{
    global $g;
    if ($conexao === false) {
        $conexao = $g['conexaoBanco'];
    }
    $retorno = \mysqli_real_escape_string($conexao, $string);
    return $retorno;
}

function mysqli_real_escape_string($string, $conexao = false)
{
    global $g;
    if ($conexao === false) {
        $conexao = $g['conexaoBanco'];
    }
    $retorno = \mysqli_real_escape_string($conexao, $string);
    return $retorno;
}

function mysql_fetch_assoc($result)
{
    global $g;
    $retorno = ($result instanceof \mysqli_result ? \mysqli_fetch_assoc($result) : null);
    return $retorno;
}

function mysql_fetch_row($result)
{
    global $g;
    $retorno = ($result instanceof \mysqli_result ? \mysqli_fetch_row($result) : null);
    return $retorno;
}

function mysql_num_rows($result)
{
    global $g;
    $retorno = ($result instanceof \mysqli_result ? \mysqli_num_rows($result) : 0);
    return $retorno;
}

function mysql_numrows($result)
{
    global $g;
    $retorno = ($result instanceof \mysqli_result ? \mysqli_num_rows($result) : 0);
    return $retorno;
}

function mysql_free_result($result)
{
    global $g;
    $retorno = \mysqli_free_result($result);
    return $retorno;
}

function mysql_error($conexao = false)
{
    global $g;
    if ($conexao === false) {
        if ($g['conexaoLastQuery']) {
            $conexao = $g['conexaoLastQuery'];
        } else {
            $conexao = $g['conexaoBanco'];
        }
    }
    $retorno = \mysqli_error($conexao);
    return $retorno;
}

function mysqli_error($conexao = false)
{
    global $g;
    if ($conexao === false) {
        $conexao = $g['conexaoBanco'];
    }
    $retorno = \mysqli_error($conexao);
    return $retorno;
}

function mysql_errno($conexao = false)
{
    global $g;
    if ($conexao === false) {
        $conexao = $g['conexaoBanco'];
    }
    $retorno = \mysqli_errno($conexao);
    return $retorno;
}

function mysql_data_seek($result, $offset)
{
    global $g;
    $retorno = \mysqli_data_seek($result, $offset);
    return $retorno;
}

function mysql_set_charset($string, $conexao = false)
{
    global $g;
    if ($conexao === false) {
        $conexao = $g['conexaoBanco'];
    }
    $retorno = \mysqli_set_charset($conexao, $string);
    return $retorno;
}

function mysql_fetch_array($result)
{
    global $g;
    $retorno = ($result instanceof \mysqli_result ? \mysqli_fetch_array($result) : null);
    return $retorno;
}

function sql_fetch_array($result)
{
    global $g;
    $retorno = ($result instanceof \mysqli_result ? \mysqli_fetch_array($result) : null);
    return $retorno;
}

function mysql_affected_rows($conexao = false)
{
    global $g;
    if ($conexao === false) {
        $conexao = $g['conexaoBanco'];
    }
    $retorno = \mysqli_affected_rows($conexao);
    return $retorno;
}

function mysqli_query_clickHoue($query)
{
    global $g;
    $retorno = \mysqli_query($g['conexaoBancoClickHouse'], $query);
    return $retorno;
}

function mysqli_query_clickHouse($query)
{
    global $g;
    $retorno = \mysqli_query($g['conexaoBancoClickHouse'], $query);
    return $retorno;
}

function mysqli_insert_id_clickHouse()
{
    global $g;
    $retorno = \mysqli_insert_id($g['conexaoBancoClickHouse']);
    return $retorno;
}

function mysqli_error_clickHouse()
{
    global $g;
    $retorno = \mysqli_error($g['conexaoBancoClickHouse']);
    return $retorno;
}

function mysqli_fetch_assoc_clickHouse($result)
{
    global $g;
    $retorno = ($result instanceof \mysqli_result ? \mysqli_fetch_assoc($result) : null);
    return $retorno;
}

function mysqli_num_rows_clickHouse($result)
{
    global $g;
    $retorno = ($result instanceof \mysqli_result ? \mysqli_num_rows($result) : 0);
    return $retorno;
}

function mysqli_numrows_clickHouse($result)
{
    global $g;
    $retorno = ($result instanceof \mysqli_result ? \mysqli_num_rows($result) : 0);
    return $retorno;
}

function gRedimensionaImagem($imagem, $largura, $pasta)
{
    if ($imagem['type'] == "image/jpeg") {
        $img = imagecreatefromjpeg($imagem['tmp_name']);
    } else {
        if ($imagem['type'] == "image/gif") {
            $img = imagecreatefromgif($imagem['tmp_name']);
        } else {
            if ($imagem['type'] == "image/png") {
                $img = imagecreatefrompng($imagem['tmp_name']);
            }
        }
    }
    $x = imagesx($img);
    $y = imagesy($img);
    $autura = $largura * $y / $x;
    $nova = imagecreatetruecolor($largura, $autura);
    imagecopyresampled($nova, $img, 0, 0, 0, 0, $largura, $autura, $x, $y);
    imagejpeg($nova, $pasta);
    imagedestroy($img);
    imagedestroy($nova);
}

function gRetornaMes($mes)
{
    if (($mes == '01')) {
        return 'janeiro';
    } elseif (($mes == '02')) {
        return 'fevereiro';
    } elseif (($mes == '03')) {
        return 'março';
    } elseif (($mes == '04')) {
        return 'abril';
    } elseif (($mes == '05')) {
        return 'maio';
    } elseif (($mes == '06')) {
        return 'junho';
    } elseif (($mes == '07')) {
        return 'julho';
    } elseif (($mes == '08')) {
        return 'agosto';
    } elseif (($mes == '09')) {
        return 'setembro';
    } elseif (($mes == '10')) {
        return 'outubro';
    } elseif (($mes == '11')) {
        return 'novembro';
    } elseif (($mes == '12')) {
        return 'dezembro';
    }
}

function gRetornaDiaExtenso($dia)
{
    if (($dia == '01')) {
        return 'primeiro';
    } elseif (($dia == '02')) {
        return 'dois';
    } elseif (($dia == '03')) {
        return 'três';
    } elseif (($dia == '04')) {
        return 'quatro';
    } elseif (($dia == '05')) {
        return 'cinco';
    } elseif (($dia == '06')) {
        return 'seis';
    } elseif (($dia == '07')) {
        return 'sete';
    } elseif (($dia == '08')) {
        return 'oito';
    } elseif (($dia == '09')) {
        return 'nove';
    } elseif (($dia == '10')) {
        return 'dez';
    } elseif (($dia == '11')) {
        return 'onze';
    } elseif (($dia == '12')) {
        return 'doze';
    } elseif (($dia == '13')) {
        return 'treze';
    } elseif (($dia == '14')) {
        return 'quatorze';
    } elseif (($dia == '15')) {
        return 'quinze';
    } elseif (($dia == '16')) {
        return 'dezesseis';
    } elseif (($dia == '17')) {
        return 'dezessete';
    } elseif (($dia == '18')) {
        return 'dezoito';
    } elseif (($dia == '19')) {
        return 'dezenove';
    } elseif (($dia == '20')) {
        return 'vinte';
    } elseif (($dia == '21')) {
        return 'vinte e um';
    } elseif (($dia == '22')) {
        return 'vinte e dois';
    } elseif (($dia == '23')) {
        return 'vinte e quatro';
    } elseif (($dia == '25')) {
        return 'vinte e cinco';
    } elseif (($dia == '26')) {
        return 'vinte e seis';
    } elseif (($dia == '27')) {
        return 'vinte e sete';
    } elseif (($dia == '28')) {
        return 'vinte e oito';
    } elseif (($dia == '29')) {
        return 'vinte e nove';
    } elseif (($dia == '30')) {
        return 'trinta';
    } elseif (($dia == '31')) {
        return 'trinta e um';
    }
}

function gPegaRelacionamento($Entidade, $Id_Entidade, $Excecao = '')
{
    if ($Id_Entidade <= 0) {
        return false;
    } else {
        $cSql = mysql_query("show tables");
        while ($mTabela = mysql_fetch_array($cSql)) {
            $mTabela[0] = strtoupper($mTabela[0]);
            if ($mTabela[0] == $Entidade) {
            }
            $cSql2 = mysql_query("desc {$mTabela[0]} '%{$Entidade}%'");
            if ($mCampos = mysql_fetch_array($cSql2)) {
                while (!(strpos($mCampos[0], "{$Entidade}") !== false)) {
                }
                $mT[] = $mTabela[0];
                $mTC[] = $mCampos[0];
            }
        }
        $x = 0;
        while ($x < count($mT)) {
            $cSql = mysql_query("show create table {$mT[$x]}");
            $mSql = mysql_fetch_array($cSql);
            $cSql = "select {$mTC[$x]} from {$mT[$x]} where {$mTC[$x]}={$Id_Entidade} limit 0,1";
            $resultado = mysql_query("{$cSql}");
            while (!(mysql_num_rows($resultado) > 0)) {
                $x++;
            }
            if (strpos($mSql[1], 'ON DELETE CASCADE') != 0) {
            } else {
                while (!(strpos(" " . $Excecao, $mT[$x]) <= 0)) {
                }
                return $mT[$x];
            }
        }
    }
}

function gPegaRelacionamento2($tabela)
{
    global $g;
    if (!(isset($g['cacheRelacionamento']) && isset($g['cacheRelacionamento'][$tabela]))) {
        $relacionamentos = array();
        $sql = "SHOW TABLES;";
        $res = mysql_query($sql);
        while ($table = mysql_fetch_row($res)) {
            $table = $table[0];
            if (strtoupper($table) == $tabela . 'A') {
            }
            $sql = "DESC {$table}";
            $resDesc = mysql_query($sql);
            if ($column = mysql_fetch_row($resDesc)) {
                $column = $column[0];
                while (!preg_match('/^[^_]+_' . preg_quote($tabela, '/') . '_Id$/i', $column)) {
                }
                while (!($column != $tabela . '_Id')) {
                }
                $relacionamentos[] = $column;
            }
        }
        $g['cacheRelacionamento'][$tabela] = $relacionamentos;
        return $relacionamentos;
    } else {
        return $g['cacheRelacionamento'][$tabela];
    }
}

function gMudarC004($tabela, $id, $C004_Id)
{
    global $g;
    mysqli_query("SET AUTOCOMMIT=0");
    mysqli_query("START TRANSACTION");
    $mD009AtualizaEstoque = array();
    $sql = mysqli_query("UPDATE {$tabela} SET {$tabela}_C004_Id = '{$C004_Id}' WHERE {$tabela}_Id = '{$id}'");
    $erro = mysql_error();
    if (!$erro) {
        $sqlTables = mysqli_query("SHOW FULL TABLES");
        $erro = mysql_error();
        if (!$erro) {
            while ($table = mysql_fetch_array($sqlTables)) {
                $tableType = isset($table[1]) ? strtoupper(trim($table[1])) : '';
                if ($tableType !== 'BASE TABLE') {
                }
                $table = strtoupper($table[0]);
                $campoRelacionamento = "{$table}_{$tabela}_Id";
                $sql = mysqli_query("SHOW COLUMNS FROM `{$table}` WHERE Field = '{$campoRelacionamento}'");
                $erro = mysql_error();
                while (!$erro) {
                    while (!mysql_num_rows($sql)) {
                    }
                    $sql = mysqli_query("UPDATE {$table} SET {$table}_C004_Id = '{$C004_Id}' WHERE {$table}_{$tabela}_Id = '{$id}'");
                    $erro = mysql_error();
                    while (!($erro && !strpos(" {$erro} ", "Unknown"))) {
                        $campoD009 = "{$table}_D009_Id";
                        $sql = mysqli_query("SHOW COLUMNS FROM `{$table}` WHERE Field = '{$campoD009}'");
                        $erro = mysql_error();
                        while (!$erro) {
                            while (!mysql_num_rows($sql)) {
                            }
                            $sqlTableD009 = mysqli_query("SELECT {$table}_Id, {$table}_D009_Id FROM {$table} WHERE {$table}_{$tabela}_Id = '{$id}'");
                            $erro = mysql_error();
                            while (!$erro) {
                                if ($mSqlTableD009 = mysql_fetch_assoc($sqlTableD009)) {
                                    $tableD009_Id = $mSqlTableD009["{$table}_Id"];
                                    $D009_Id = $mSqlTableD009["{$table}_D009_Id"];
                                    $mD009AtualizaEstoque[$D009_Id] = true;
                                    $D049 = mysqli_query("SELECT D009_D049_Id,\n\t\t\t\t\t\t\t\t\t\t\t\t\tD001_Codigo_Produto \n\t\t\t\t\t                           FROM D009 \n\t\t\t\t\t\t\t\t   \t      LEFT JOIN D049 ON D049_Id=D009_D049_Id \n\t\t\t\t\t\t\t\t\t\t  LEFT JOIN D001 ON D001_Id=D049_D001_Id\n\t\t\t\t\t\t\t\t\t\t      WHERE D009_Id='{$D009_Id}'");
                                    $mD049 = mysql_fetch_assoc($D049);
                                    $erro = mysql_error();
                                    while (!$erro) {
                                        $D009 = mysqli_query("SELECT D009_Id, T066_Id FROM D009 LEFT JOIN T066 ON T066_D009_Id=D009_Id WHERE D009_D049_Id='{$mD049['D009_D049_Id']}' AND D009_C004_Id='{$C004_Id}'");
                                        $mD009 = mysql_fetch_assoc($D009);
                                        $erro = mysql_error();
                                        while (!$erro) {
                                            while (!($mD009['D009_Id'] <= 0)) {
                                                $sql = mysqli_query("UPDATE {$table} SET {$table}_D009_Id = '{$mD009['D009_Id']}' WHERE {$table}_Id = '{$tableD009_Id}'");
                                                $erro = mysql_error();
                                                while (!$erro) {
                                                    $sql = mysqli_query("UPDATE {$table} SET {$table}_T066_Id = '{$mD009['T066_Id']}' WHERE {$table}_Id = '{$tableD009_Id}'");
                                                }
                                                mysqli_query("ROLLBACK");
                                                return "Erro 9: {$erro}";
                                            }
                                            mysqli_query("ROLLBACK");
                                            return "Erro 9: Não foi encontrado o produto {$mD049['D001_Codigo_Produto']} em {$id}";
                                        }
                                        mysqli_query("ROLLBACK");
                                        return "Erro 8: {$erro}";
                                    }
                                    mysqli_query("ROLLBACK");
                                    return "Erro 7: {$erro}";
                                }
                            }
                            mysqli_query("ROLLBACK");
                            return "Erro 6: {$erro}";
                        }
                        mysqli_query("ROLLBACK");
                        return "Erro 5: {$erro}";
                    }
                    mysqli_query("ROLLBACK");
                    return "Erro 4: {$erro}";
                }
                mysqli_query("ROLLBACK");
                return "Erro 3: {$erro}";
            }
            mysqli_query("COMMIT");
            mysqli_query("SET AUTOCOMMIT=1");
            require_once "bibliotecas/classes/CAD002.php";
            $CAD002 = new CAD002();
            foreach ($mD009AtualizaEstoque as $D009_Id => $value) {
                $CAD002->D001_reprocessa_historico(0, $D009_Id, 20, true);
            }
            return true;
        } else {
            mysqli_query("ROLLBACK");
            return "Erro 2: {$erro}";
        }
    } else {
        mysqli_query("ROLLBACK");
        return "Erro 1: {$erro}";
    }
}

function gJuntarCodigos($tabela, $antigo, $novo, $tabelasBloqueio = array(), $tabelasRestricao = array(), $executarTriggersPHP = true)
{
    $antigo = (int)$antigo;
    $novo = (int)$novo;
    $tabela = strtoupper($tabela);
    if (!($antigo <= 0 || $novo <= 0)) {
        if (!($antigo == $novo)) {
            $resShowTables = mysql_query('SHOW FULL TABLES');
            if ($resShowTables) {
                while ($showTable = mysql_fetch_array($resShowTables)) {
                    $tableType = isset($showTable[1]) ? strtoupper(trim($showTable[1])) : '';
                    if ($tableType !== 'BASE TABLE') {
                    }
                    $showTable = strtoupper($showTable[0]);
                    if (strpos(" {$showTable} ", 'DIM0') || strpos(" {$showTable} ", 'FAC0')) {
                    }
                    $resDesc = mysqli_query("DESC `{$showTable}` '%{$tabela}_Id%'");
                    while ($resDesc) {
                        if ($desc = mysql_fetch_array($resDesc)) {
                            $desc = $desc[0];
                            if ($desc == "{$tabela}_Id") {
                            }
                            if (in_array($showTable, $tabelasBloqueio)) {
                                $resCount = mysql_query("SELECT {$desc} FROM {$showTable} WHERE {$desc} = '{$antigo}'");
                                while ($resCount) {
                                    while (!(mysql_num_rows($resCount) > 0)) {
                                    }
                                    return "BLOQUEADO-{$showTable}";
                                }
                                return 'Erro interno 3';
                            } else {
                                $sql = "UPDATE {$showTable} SET {$desc} = '{$novo}' WHERE {$desc} = '{$antigo}'";
                                $res = mysqli_query($sql);
                                if (!$res && in_array($showTable, $tabelasRestricao)) {
                                    mysql_query("DELETE FROM {$showTable} WHERE {$desc} = '{$antigo}'", $executarTriggersPHP);
                                } else {
                                    while ($res) {
                                    }
                                    return 'Erro interno 4 Erro: ' . mysql_error();
                                }
                            }
                        }
                    }
                    return 'Erro interno 22 ' . "DESC `{$showTable}` '%{$tabela}_Id%' " . mysql_error();
                }
                $sql = "DELETE FROM {$tabela} WHERE {$tabela}_Id = {$antigo}";
                $res = mysql_query($sql, $executarTriggersPHP);
                if ($res) {
                    return true;
                } else {
                    return 'Erro interno 5';
                }
            } else {
                return 'Erro interno 1';
            }
        } else {
            return 'Os códigos não podem ser o mesmo.';
        }
    } else {
        return 'Os dois códigos devem ser preenchidos.';
    }
}

function gPegaDiaSemana($data)
{
    $ano = substr("{$data}", 0, 4);
    $mes = substr("{$data}", 5, - 3);
    $dia = substr("{$data}", 8, 9);
    $diasemana = date("w", mktime(0, 0, 0, $mes, $dia, $ano));
    if (($diasemana == "0")) {
        $diasemana = "Domingo";
    } elseif (($diasemana == "1")) {
        $diasemana = "Segunda-Feira";
    } elseif (($diasemana == "2")) {
        $diasemana = "Terça-Feira";
    } elseif (($diasemana == "3")) {
        $diasemana = "Quarta-Feira";
    } elseif (($diasemana == "4")) {
        $diasemana = "Quinta-Feira";
    } elseif (($diasemana == "5")) {
        $diasemana = "Sexta-Feira";
    } elseif (($diasemana == "6")) {
        $diasemana = "Sábado";
    }
    return $diasemana;
}

function gValidaFeriado($data)
{
    $dia = 86400;
    $datas = array();
    $datas['pascoa'] = easter_date($ano);
    $datas['sexta_santa'] = $datas['pascoa'] - 2 * $dia;
    $datas['carnaval'] = $datas['pascoa'] - 47 * $dia;
    $datas['corpus_cristi'] = $datas['pascoa'] + 60 * $dia;
    $feriados = array('01/01', date('d/m', $datas['carnaval']), date('d/m', $datas['sexta_santa']), date('d/m', $datas['pascoa']), '21/04', '01/05', date('d/m', $datas['corpus_cristi']), '08/08', '07/09', '08/09', '12/10', '02/11', '15/11', '25/12');
    $retorno = false;
    if (!(!array_search($data, $feriados))) {
        $retorno = true;
    }
    return $retorno;
}

function gTemPermissao($C029_Id)
{
    global $g;
    if (!isset($g['c029Ids'][$C029_Id])) {
        return false;
    } else {
        return true;
    }
}

function gValorPorExtenso($valor)
{
    $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");
    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");
    $z = 0;
    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    $i = 0;
    while ($i < count($inteiro)) {
        $ii = strlen($inteiro[$i]);
        if ($ii < 3) {
            $inteiro[$i] = "0" . $inteiro[$i];
            $ii++;
        }
        $i++;
    }
    $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
    $rt = '';
    $i = 0;
    while ($i < count($inteiro)) {
        $valor = $inteiro[$i];
        $rc = $valor > 100 && $valor < 200 ? "cento" : $c[$valor[0]];
        $rd = $valor[1] < 2 ? "" : $d[$valor[1]];
        $ru = $valor > 0 ? $valor[1] == 1 ? $d10[$valor[2]] : $u[$valor[2]] : "";
        $r = $rc . ($rc && ($rd || $ru) ? " e " : "") . $rd . ($rd && $ru ? " e " : "") . $ru;
        $t = count($inteiro) - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000") {
            $z++;
        } else {
            if ($z > 0) {
                $z--;
            }
        }
        while (!($t == 1 && $z > 0 && $inteiro[0] > 0)) {
            while (!$r) {
                $i++;
            }
            $rt = $rt . ($i > 0 && $i <= $fim && $inteiro[0] > 0 && $z < 1 ? $i < $fim ? ", " : " e " : " ") . $r;
        }
        $r .= ($z > 1 ? " de " : "") . $plural[$t];
    }
    return $rt ? $rt : "zero";
}

function gJurosSimples($valor, $taxa, $parcelas)
{
    $taxa = $taxa / 100;
    $m = $valor * (1 + $taxa * $parcelas);
    $valParcela = number_format($m / $parcelas, 2, ",", ".");
    return $valParcela;
}

function gJurosCompostos($valor, $taxa, $parcelas)
{
    $taxa = $taxa / 100;
    $valParcela = $valor * pow(1 + $taxa, $parcelas);
    $valParcela = number_format($valParcela / $parcelas, 2, ",", ".");
    return $valParcela;
}

function gJurosCompostosTitulos($valor, $taxa, $parcelas)
{
    $taxa = $taxa / 100;
    $valParcela = $valor * pow(1 + $taxa, $parcelas);
    return $valParcela;
}

function gParcelaOriginal($valorFinal, $prazo)
{
    global $g;
    $taxa = $g['c004']['C004_Taxa_Juros'];
    $taxa = $taxa / 100;
    $parcelaOriginal = $valorFinal / pow(1 + $taxa, $prazo);
    return $parcelaOriginal;
}

function gLimpaTexto($string)
{
    return preg_replace("/[^a-z0-9 ]/imsu", ' ', $string);
}

function gRemoveCaracteresEspeciais($string)
{
    return str_replace(" ", " ", preg_replace("/&([a-z])[a-z]+;/i", "\$1", htmlentities(trim($string))));
}

function gLimpaAcentos($x)
{
    $stringSemAcentos = iconv('UTF-8', 'ASCII//TRANSLIT', $x);
    $x = $stringSemAcentos ? $stringSemAcentos : $x;
    $novoX = '';
    $i = 0;
    while ($i < strlen($x)) {
        $code = ord($x[$i]);
        if (!($code == 192)) {
            if (!($code == 193)) {
                if (!($code == 194)) {
                    if (!($code == 195)) {
                        if (!($code == 196)) {
                            if (!($code == 197)) {
                                if (!($code == 198)) {
                                    if (!($code == 199)) {
                                        if (!($code == 200)) {
                                            if (!($code == 201)) {
                                                if (!($code == 202)) {
                                                    if (!($code == 203)) {
                                                        if (!($code == 204)) {
                                                            if (!($code == 205)) {
                                                                if (!($code == 206)) {
                                                                    if (!($code == 207)) {
                                                                        if (!($code == 210)) {
                                                                            if (!($code == 211)) {
                                                                                if (!($code == 212)) {
                                                                                    if (!($code == 213)) {
                                                                                        if (!($code == 214)) {
                                                                                            if (!($code == 217)) {
                                                                                                if (!($code == 218)) {
                                                                                                    if (!($code == 219)) {
                                                                                                        if (!($code == 220)) {
                                                                                                            if (!($code == 224)) {
                                                                                                                if (!($code == 225)) {
                                                                                                                    if (!($code == 226)) {
                                                                                                                        if (!($code == 227)) {
                                                                                                                            if (!($code == 228)) {
                                                                                                                                if (!($code == 229)) {
                                                                                                                                    if (!($code == 231)) {
                                                                                                                                        if (!($code == 232)) {
                                                                                                                                            if (!($code == 233)) {
                                                                                                                                                if (!($code == 234)) {
                                                                                                                                                    if (!($code == 235)) {
                                                                                                                                                        if (!($code == 236)) {
                                                                                                                                                            if (!($code == 237)) {
                                                                                                                                                                if (!($code == 238)) {
                                                                                                                                                                    if (!($code == 239)) {
                                                                                                                                                                        if (!($code == 242)) {
                                                                                                                                                                            if (!($code == 243)) {
                                                                                                                                                                                if (!($code == 244)) {
                                                                                                                                                                                    if (!($code == 245)) {
                                                                                                                                                                                        if (!($code == 246)) {
                                                                                                                                                                                            if (!($code == 249)) {
                                                                                                                                                                                                if (!($code == 250)) {
                                                                                                                                                                                                    if (!($code == 251)) {
                                                                                                                                                                                                        if (!($code == 252)) {
                                                                                                                                                                                                            $novoX .= $x[$i];
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $novoX .= 'U';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $novoX .= 'U';
                                                                                                                                                                                                    }
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $novoX .= 'U';
                                                                                                                                                                                                }
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $novoX .= 'U';
                                                                                                                                                                                            }
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $novoX .= 'O';
                                                                                                                                                                                        }
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $novoX .= 'O';
                                                                                                                                                                                    }
                                                                                                                                                                                } else {
                                                                                                                                                                                    $novoX .= 'O';
                                                                                                                                                                                }
                                                                                                                                                                            } else {
                                                                                                                                                                                $novoX .= 'O';
                                                                                                                                                                            }
                                                                                                                                                                        } else {
                                                                                                                                                                            $novoX .= 'O';
                                                                                                                                                                        }
                                                                                                                                                                    } else {
                                                                                                                                                                        $novoX .= 'I';
                                                                                                                                                                    }
                                                                                                                                                                } else {
                                                                                                                                                                    $novoX .= 'I';
                                                                                                                                                                }
                                                                                                                                                            } else {
                                                                                                                                                                $novoX .= 'I';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $novoX .= 'I';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $novoX .= 'E';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $novoX .= 'E';
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $novoX .= 'E';
                                                                                                                                            }
                                                                                                                                        } else {
                                                                                                                                            $novoX .= 'E';
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        $novoX .= 'C';
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    $novoX .= 'A';
                                                                                                                                }
                                                                                                                            } else {
                                                                                                                                $novoX .= 'A';
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            $novoX .= 'A';
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        $novoX .= 'A';
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    $novoX .= 'A';
                                                                                                                }
                                                                                                            } else {
                                                                                                                $novoX .= 'A';
                                                                                                            }
                                                                                                        } else {
                                                                                                            $novoX .= 'U';
                                                                                                        }
                                                                                                    } else {
                                                                                                        $novoX .= 'U';
                                                                                                    }
                                                                                                } else {
                                                                                                    $novoX .= 'U';
                                                                                                }
                                                                                            } else {
                                                                                                $novoX .= 'U';
                                                                                            }
                                                                                        } else {
                                                                                            $novoX .= 'O';
                                                                                        }
                                                                                    } else {
                                                                                        $novoX .= 'O';
                                                                                    }
                                                                                } else {
                                                                                    $novoX .= 'O';
                                                                                }
                                                                            } else {
                                                                                $novoX .= 'O';
                                                                            }
                                                                        } else {
                                                                            $novoX .= 'O';
                                                                        }
                                                                    } else {
                                                                        $novoX .= 'I';
                                                                    }
                                                                } else {
                                                                    $novoX .= 'I';
                                                                }
                                                            } else {
                                                                $novoX .= 'I';
                                                            }
                                                        } else {
                                                            $novoX .= 'I';
                                                        }
                                                    } else {
                                                        $novoX .= 'E';
                                                    }
                                                } else {
                                                    $novoX .= 'E';
                                                }
                                            } else {
                                                $novoX .= 'E';
                                            }
                                        } else {
                                            $novoX .= 'E';
                                        }
                                    } else {
                                        $novoX .= 'C';
                                    }
                                } else {
                                    $novoX .= 'A';
                                }
                            } else {
                                $novoX .= 'A';
                            }
                        } else {
                            $novoX .= 'A';
                        }
                    } else {
                        $novoX .= 'A';
                    }
                } else {
                    $novoX .= 'A';
                }
            } else {
                $novoX .= 'A';
            }
        } else {
            $novoX .= 'A';
        }
        $i++;
    }
    $x = $novoX;
    $cCharPermitido = '&ABCDEFGHIJLKYMNOPQRSTUVXWZ 1234567890()[]{}.,+_-:/=%?;!@|$"';
    $n = 0;
    while ($n < strlen($x)) {
        $c = substr($x, $n, 1);
        if (strpos($cCharPermitido, strtoupper($c)) !== FALSE) {
            $r .= $c;
        } else {
            $r .= "";
        }
        $n++;
    }
    $x = $r;
    $x = str_replace("&", "&amp;", $x);
    return $x;
}

function gLimpaAcentosPuro($x)
{
    if (mb_detect_encoding($x) != 'UTF-8') {
        $x = utf8_encode($x);
    }
    $table = array('Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r');
    return strtr($x, $table);
}

function gCorrigeAssinatura($string)
{
    $string = preg_replace("/<font.*>/imsU", '', $string);
    $string = preg_replace("/<\\/font>/imsU", '', $string);
    return $string;
}

function gTruncar($str, $length = 10, $trailing = '...')
{
    if (!(!is_array($str))) {
        if (!empty($str['campoAtual'])) {
            $str = $str['campoAtual'];
        } else {
            return '';
        }
    }
    if (!empty($str)) {
        $length -= mb_strlen($trailing);
        if (mb_strlen($str) > $length) {
            return mb_substr($str, 0, $length) . $trailing;
        } else {
            $res = $str;
            return $res;
        }
    } else {
        return $str;
    }
}

function gRetornaSqlDuplicacao($tabela, $pk)
{
    $chave = $tabela . '_Id';
    $ret = mysql_query("SHOW COLUMNS FROM {$tabela} WHERE FIELD!='{$chave}'");
    $campos = "";
    while ($linha = mysql_fetch_assoc($ret)) {
        $campos .= $linha['Field'] . ',';
    }
    $campos = substr($campos, 0, - 1);
    $sql = "INSERT INTO {$tabela} ({$campos}) SELECT {$campos} FROM {$tabela} WHERE {$chave}={$pk}";
    return $sql;
}

function gGerarThumb($src, $dest, $desired_width)
{
    if (!(!file_exists($dest))) {
        unlink($dest);
    }
    $source_image = @imagecreatefromjpeg($src);
    if (!$source_image) {
        $source_image = imagecreatefrompng($src);
    }
    $width = imagesx($source_image);
    $height = imagesy($source_image);
    $desired_height = floor($height * ($desired_width / $width));
    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
    imagejpeg($virtual_image, $dest);
}

function gDiasUteis($data_ini, $data_fin)
{
    $dias = number_format(($data_fin - $data_ini) / 86400, 0);
    $naoutil = 0;
    while ($data_ini < $data_fin) {
        while (!(date("w", $data_ini) == 0 || date("w", $data_ini) == 6)) {
            $data_ini += 86400;
        }
        $naoutil++;
    }
    return $dias - $naoutil;
}

function gModificarFKGlobal($pk, $valor, $novo_valor)
{
    $pk = explode('_', $pk);
    $sql = "SHOW TABLES;";
    $res = mysql_query($sql);
    $count = 0;
    while ($table = mysql_fetch_row($res)) {
        $table = $table[0];
        $sql = "DESC {$table};";
        $resDesc = mysql_query($sql);
        if ($desc = mysql_fetch_row($resDesc)) {
            $fk = explode('_', $desc[0]);
            while (!(isset($fk[2]) && $fk[2] == 'Id' && $fk[1] == $pk[0])) {
            }
            $sql = "UPDATE {$fk[0]} SET {$desc[0]} = '{$novo_valor}' WHERE {$desc[0]} = '{$valor}';";
            $result = mysql_query($sql);
            while ($result) {
            }
            die('Invalid query: ' . mysql_error());
        }
    }
}

function definirClasse($classe)
{
    global $g;
    if (!isset($g)) {
        eval("namespace hardness;\nclass {$classe} extends {$classe}_ {}");
        return false;
    } else {
        $arquivos = array();
        $limparConteudo = function($conteudo) {
            $conteudo = preg_replace('/^\\<\\?(php)?/m', '', $conteudo);
            $conteudo = str_replace('namespace hardness;', '', $conteudo);
            $conteudo = preg_replace('/\\?\\>( |\\n)+?\\$/m', '', $conteudo);
            return $conteudo;
        };
        $arquivoIDE = "ide/personalizacaoConf/" . $classe . '.PHP-alterarMetodos.php';
        $arquivoApps = Apps::getDirAtivos() . "personalizacaoConf/" . $classe . '.PHP-alterarMetodos.php';
        $arquivoAppsEditor = Apps::getDirEditor() . "personalizacaoConf/" . $classe . '.PHP-alterarMetodos.php';
        $arquivoUsuario = "{$g['pathDados']}personalizacaoConf/" . $classe . '.PHP-alterarMetodos.php';
        $arquivoIDE = file_exists($arquivoIDE) ? file_get_contents($arquivoIDE) : false;
        $arquivoApps = file_exists($arquivoApps) ? file_get_contents($arquivoApps) : false;
        $arquivoAppsEditor = file_exists($arquivoAppsEditor) ? file_get_contents($arquivoAppsEditor) : false;
        $arquivoUsuario = file_exists($arquivoUsuario) ? file_get_contents($arquivoUsuario) : false;
        $arquivoEmpresaIDE = "ide/personalizacaoConf/" . $g['empresaAtual'] . $classe . '.PHP-alterarMetodos.php';
        $arquivoEmpresaApps = Apps::getDirAtivos() . "personalizacaoConf/" . $g['empresaAtual'] . $classe . '.PHP-alterarMetodos.php';
        $arquivoEmpresaAppsEditor = Apps::getDirEditor() . "personalizacaoConf/" . $g['empresaAtual'] . $classe . '.PHP-alterarMetodos.php';
        $arquivoEmpresaUsuario = "{$g['pathDados']}personalizacaoConf/" . $g['empresaAtual'] . $classe . '.PHP-alterarMetodos.php';
        $arquivoEmpresaIDE = file_exists($arquivoEmpresaIDE) ? file_get_contents($arquivoEmpresaIDE) : false;
        $arquivoEmpresaApps = file_exists($arquivoEmpresaApps) ? file_get_contents($arquivoEmpresaApps) : false;
        $arquivoEmpresaAppsEditor = file_exists($arquivoEmpresaAppsEditor) ? file_get_contents($arquivoEmpresaAppsEditor) : false;
        $arquivoEmpresaUsuario = file_exists($arquivoEmpresaUsuario) ? file_get_contents($arquivoEmpresaUsuario) : false;
        if (!(empty($arquivoIDE))) {
            $arquivos[] = $arquivoIDE;
        }
        if (!(empty($arquivoEmpresaIDE))) {
            $arquivos[] = $arquivoEmpresaIDE;
        }
        if (!$g['modoDesenvolvedor'] && !empty($arquivoApps)) {
            $arquivoAppsMerge = @unserialize($arquivoApps);
            if ($arquivoAppsMerge) {
                foreach ($arquivoAppsMerge as $value) {
                    $arquivos[] = $value;
                }
            } else {
                $arquivos[] = $arquivoApps;
            }
        }
        if (!$g['modoDesenvolvedor'] && !empty($arquivoEmpresaApps)) {
            $arquivoAppsMerge = @unserialize($arquivoEmpresaApps);
            if ($arquivoAppsMerge) {
                foreach ($arquivoAppsMerge as $value) {
                    $arquivos[] = $value;
                }
            } else {
                $arquivos[] = $arquivoEmpresaApps;
            }
        }
        if ($g['modoDesenvolvedorApp'] && !empty($arquivoAppsEditor)) {
            $arquivos[] = $arquivoAppsEditor;
        }
        if ($g['modoDesenvolvedorApp'] && !empty($arquivoEmpresaAppsEditor)) {
            $arquivos[] = $arquivoEmpresaAppsEditor;
        }
        if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && !empty($arquivoUsuario)) {
            $arquivos[] = $arquivoUsuario;
        }
        if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && !empty($arquivoEmpresaUsuario)) {
            $arquivos[] = $arquivoEmpresaUsuario;
        }
        if (empty($arquivos)) {
            eval("namespace hardness;\nclass {$classe} extends {$classe}_ {}");
        } else {
            if (count($arquivos) == 1) {
                log("arquivos <pre>" . htmlentities(print_r($arquivos, true)) . "</pre>");
                $arquivos[0] = $limparConteudo($arquivos[0]);
                eval("namespace hardness;\n{$arquivos[0]}");
            } else {
                $codigo = "namespace hardness;\n";
                foreach ($arquivos as $key => $arquivo) {
                    $arquivo = $limparConteudo($arquivo);
                    $keyAnterior = $key - 1;
                    if (count($arquivos) - 1 == $key) {
                        $arquivo = str_replace("class {$classe} extends {$classe}_", "class {$classe} extends {$classe}_{$keyAnterior}", $arquivo);
                    } else {
                        if ($key === 0) {
                            $arquivo = str_replace("class {$classe} extends {$classe}_", "class {$classe}_{$key} extends {$classe}_", $arquivo);
                        } else {
                            $arquivo = str_replace("class {$classe} extends {$classe}_", "class {$classe}_{$key} extends {$classe}_{$keyAnterior}", $arquivo);
                        }
                    }
                    $codigo .= $arquivo . "\n";
                }
                eval($codigo);
            }
        }
        return false;
    }
}

function incluirMenu()
{
    global $g, $menu;
    $arquivoGlobalIde = "ide/personalizacaoConf/" . 'GLOBAL-menu.php';
    $arquivoGlobalApps = Apps::getDirAtivos() . "personalizacaoConf/" . 'GLOBAL-menu.php';
    $arquivoGlobalAppsEditor = Apps::getDirEditor() . "personalizacaoConf/" . 'GLOBAL-menu.php';
    $arquivoGlobalGlobal = "{$g['pathDados']}personalizacaoConf/" . 'GLOBAL-menu.php';
    $arquivoGlobal = $arquivoGlobalIde;
    if (!$g['modoDesenvolvedor'] && file_exists($arquivoGlobalApps)) {
        $arquivoGlobal = $arquivoGlobalApps;
    }
    if ($g['modoDesenvolvedorApp'] && file_exists($arquivoGlobalAppsEditor)) {
        $arquivoGlobal = $arquivoGlobalAppsEditor;
    }
    if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoGlobalGlobal)) {
        $arquivoGlobal = $arquivoGlobalGlobal;
    }
    $arquivoEmpresaIde = "ide/personalizacaoConf/" . $g['empresaAtual'] . 'GLOBAL-menu.php';
    $arquivoEmpresaApps = Apps::getDirAtivos() . "personalizacaoConf/" . $g['empresaAtual'] . 'GLOBAL-menu.php';
    $arquivoEmpresaAppsEditor = Apps::getDirEditor() . "personalizacaoConf/" . $g['empresaAtual'] . 'GLOBAL-menu.php';
    $arquivoEmpresaGlobal = "{$g['pathDados']}personalizacaoConf/" . $g['empresaAtual'] . 'GLOBAL-menu.php';
    $arquivoEmpresa = $arquivoEmpresaIde;
    if (!$g['modoDesenvolvedor'] && file_exists($arquivoEmpresaApps)) {
        $arquivoEmpresa = $arquivoEmpresaApps;
    }
    if ($g['modoDesenvolvedorApp'] && file_exists($arquivoEmpresaAppsEditor)) {
        $arquivoEmpresa = $arquivoEmpresaAppsEditor;
    }
    if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoEmpresaGlobal)) {
        $arquivoEmpresa = $arquivoEmpresaGlobal;
    }
    if (!file_exists($arquivoEmpresa)) {
        if (!file_exists($arquivoGlobal)) {
            return false;
        } else {
            require_once $arquivoGlobal;
            return true;
        }
    } else {
        require_once $arquivoEmpresa;
        return true;
    }
}

function incluirColunaEditarRestricoes()
{
    global $g, $menu;
    require_once 'configuracoes/_colunaEditarRestricoes.php';
    $arquivoGlobal = "ide/personalizacaoConf/GLOBAL-colunaEditarRestricoes.php";
    if (!(!file_exists($arquivoGlobal))) {
        require_once $arquivoGlobal;
    }
    $arquivoEmpresa = "ide/personalizacaoConf/{$g['empresaAtual']}GLOBAL-colunaEditarRestricoes.php";
    if (!(!file_exists($arquivoEmpresa))) {
        require_once $arquivoEmpresa;
    }
    $arquivoGlobal = Apps::getDirAtivos() . "personalizacaoConf/GLOBAL-colunaEditarRestricoes.php";
    if (!$g['modoDesenvolvedor'] && file_exists($arquivoGlobal)) {
        require_once $arquivoGlobal;
    }
    $arquivoEmpresa = Apps::getDirAtivos() . "personalizacaoConf/{$g['empresaAtual']}GLOBAL-colunaEditarRestricoes.php";
    if (!$g['modoDesenvolvedor'] && file_exists($arquivoEmpresa)) {
        require_once $arquivoEmpresa;
    }
    $arquivoGlobal = Apps::getDirEditor() . "personalizacaoConf/GLOBAL-colunaEditarRestricoes.php";
    if ($g['modoDesenvolvedorApp'] && file_exists($arquivoGlobal)) {
        require_once $arquivoGlobal;
    }
    $arquivoEmpresa = Apps::getDirEditor() . "personalizacaoConf/{$g['empresaAtual']}GLOBAL-colunaEditarRestricoes.php";
    if ($g['modoDesenvolvedorApp'] && file_exists($arquivoEmpresa)) {
        require_once $arquivoEmpresa;
    }
    if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp']) {
        $arquivoGlobal = "{$g['pathDados']}personalizacaoConf/GLOBAL-colunaEditarRestricoes.php";
        if (!(!file_exists($arquivoGlobal))) {
            require_once $arquivoGlobal;
        }
        $arquivoEmpresa = "{$g['pathDados']}personalizacaoConf/{$g['empresaAtual']}GLOBAL-colunaEditarRestricoes.php";
        if (!(!file_exists($arquivoEmpresa))) {
            require_once $arquivoEmpresa;
        }
    }
}

function personalizacaoBotoes($md5 = false)
{
    global $g, $r_origemFiltro;
    if (!isset($r_origemFiltro)) {
        foreach ($GLOBALS as $name => $value) {
            global ${$name};
        }
        $r_grid = preg_replace('/^\\//', '', $g['r_']);
        $arquivoIde = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-botoes';
        $arquivoApps = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-botoes';
        $arquivoAppsEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-botoes';
        $arquivoGlobal = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-botoes';
        $arquivoPerfil = "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-botoes';
        $arquivoUsuario = "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-botoes';
        if ($g['modoDesenvolvedor']) {
            $arquivo = $arquivoIde;
        } else {
            if ($g['modoDesenvolvedorApp']) {
                $arquivo = $arquivoIde;
                $arquivo = file_exists($arquivoApps) ? $arquivoApps : $arquivo;
                $arquivo = file_exists($arquivoAppsEditor) ? $arquivoAppsEditor : $arquivo;
            } else {
                $arquivo = $arquivoIde;
                $arquivo = file_exists($arquivoApps) ? $arquivoApps : $arquivo;
                $arquivo = file_exists($arquivoGlobal) ? $arquivoGlobal : $arquivo;
                $arquivo = file_exists($arquivoPerfil) ? $arquivoPerfil : $arquivo;
                $arquivo = file_exists($arquivoUsuario) ? $arquivoUsuario : $arquivo;
            }
        }
        if (!file_exists($arquivo)) {
            return false;
        } else {
            include $arquivo;
            return true;
        }
    } else {
        return true;
    }
}

function personalizacaoTotais($md5 = false, $ajax = false)
{
    global $g, $r_exibirTotais;
    if ($ajax && (!isset($r_exibirTotais) || empty($r_exibirTotais))) {
        echo<<<EOT
<div class="buttonsBottomTop exibirTotais" style="height: 34px;">
<img src="/hardness3/static/img/loading.gif" style="width: 24px; height: 24px; margin: 6px; float: left;" />
<span style="margin: 12px 2px; float: left; color: gray; font-weight: bold;">Carregando totais...</span>
</div>
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\tcarregarTotais('{$g['divId']}');
} </script></div>
EOT;
        return true;
    } else {
        if ($ajax) {
            session_write_close();
            ob_end_clean();
            ob_start();
        }
        foreach ($GLOBALS as $name => $value) {
            global ${$name};
        }
        $r_grid = preg_replace('/^\\//', '', $g['r_']);
        $arquivoIde = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-totais';
        $arquivoApps = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-totais';
        $arquivoAppsEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-totais';
        $arquivoGlobal = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-totais';
        $arquivoPerfil = "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-totais';
        $arquivoUsuario = "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-totais';
        if ($g['modoDesenvolvedor']) {
            $arquivo = $arquivoIde;
        } else {
            if ($g['modoDesenvolvedorApp']) {
                $arquivo = $arquivoIde;
                $arquivo = file_exists($arquivoApps) ? $arquivoApps : $arquivo;
                $arquivo = file_exists($arquivoAppsEditor) ? $arquivoAppsEditor : $arquivo;
            } else {
                $arquivo = $arquivoIde;
                $arquivo = file_exists($arquivoApps) ? $arquivoApps : $arquivo;
                $arquivo = file_exists($arquivoGlobal) ? $arquivoGlobal : $arquivo;
                $arquivo = file_exists($arquivoPerfil) ? $arquivoPerfil : $arquivo;
                $arquivo = file_exists($arquivoUsuario) ? $arquivoUsuario : $arquivo;
            }
        }
        if (!file_exists($arquivo)) {
            return false;
        } else {
            include $arquivo;
            return true;
        }
    }
}

function fimTotais()
{
    global $g, $r_exibirTotais;
    if (!(isset($r_exibirTotais) && !empty($r_exibirTotais))) {
    } else {
        die;
    }
}

function log($mensagem, $a = false, $b = false)
{
    global $g;
    if (isset($_COOKIE['debuglog']) && !empty($_COOKIE['debuglog'])) {
        $backtrace = array();
        $backtrace_ = debug_backtrace();
        foreach ($backtrace_ as $back) {
            $backtrace[] = array('file' => isset($back['file']) ? $back['file'] : '', 'line' => isset($back['line']) ? $back['line'] : '', 'function' => isset($back['function']) ? $back['function'] : '', 'class' => isset($back['class']) ? $back['class'] : '', 'args' => isset($back['args']) ? $back['args'] : '');
        }
        $h = fopen("/tmp/debuglog-{$_COOKIE['debuglog']}", 'a');
        if (!(!is_array($mensagem))) {
            $mensagem = print_r($mensagem, true);
        }
        fwrite($h, '<span style="color: gray; float: right;">' . $backtrace[0]['file'] . ':' . $backtrace[0]['line'] . '</span><br/><span style="color: var(--letra-log);">' . $mensagem . '</span><br/>');
        fclose($h);
    }
    return true;
}

function gErrorHandler($errno, $errstr, $errfile, $errline)
{
    global $g, $confUsuario;
    $backtrace = array();
    $backtrace_ = debug_backtrace();
    foreach ($backtrace_ as $back) {
        $backtrace[] = array('file' => isset($back['file']) ? $back['file'] : '', 'line' => isset($back['line']) ? $back['line'] : '', 'function' => isset($back['function']) ? $back['function'] : '', 'class' => isset($back['class']) ? $back['class'] : '', 'args' => isset($back['args']) ? $back['args'] : '');
    }
    $erro = array('errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline, 'dbDatabase' => $confUsuario['dbDatabase'], 'empresaAtual' => $g['empresaAtual'], 'usuarioAtual' => $g['usuarioAtual'], 'timestamp' => time(), 'backtrace' => $backtrace);
    if (!($errno == E_NOTICE)) {
        if (!($errno == E_DEPRECATED)) {
            if (!(strpos($errstr, 'imap_open()') !== false)) {
                if (!(strpos($errstr, 'array_merge(): Argument #2 is not an array') !== false)) {
                    if (!(strpos($errstr, 'htmlentities(): Invalid multibyte sequence in argument') !== false)) {
                        $arquivo = '/tmp/gErrorHandler';
                        if (file_exists($arquivo)) {
                            $array = unserialize(file_get_contents($arquivo));
                        } else {
                            $array = array();
                        }
                        $array[] = $erro;
                        if (count($array) > 50) {
                            $array = array_slice($array, count($array) - 50);
                        }
                        file_put_contents($arquivo, serialize($array));
                        return false;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function gExplodeAuditoria($campo, $T162_Id = '')
{
}

function gRepassaRequest($vars = '', $repassarTudo = false)
{
    // Com $repassarTudo falso, acaoId, tabela e tab ficam de fora.
    // Valor que não é escalar (o formulário chega como array) não entra na query:
    // no PHP 8 o urlencode recusa array.
    $excluir = array('r', 'ajax', 'divIdRoot', 'divIdParent', 'divId', 'oneWindow', 'titleTab', 'divIdChain', 'divIdParentChain');
    if (!$repassarTudo) {
        $excluir[] = 'acaoId';
        $excluir[] = 'tabela';
        $excluir[] = 'tab';
    }
    foreach ($_REQUEST as $rkey => $rvalue) {
        if (in_array($rkey, $excluir) || !is_scalar($rvalue)) {
            continue;
        }
        if (strpos($vars, "&{$rkey}=") !== false) {
            continue;
        }
        if (!empty($vars) && $vars[0] != '&') {
            $vars = '&' . $vars;
        }
        $vars = "&{$rkey}=" . urlencode((string) $rvalue) . $vars;
    }
    return $vars;
}

function gDicionarioTabelas($tabela)
{
    $dic = <<<EOT
c001
c002
c003
c004 Empresas do Sistema
c005
c006
c007 Usuários do Sistema
c008 Tipos
c009 
c010
c011
c012
c013
c014
c015
c016
c017
c018
c019
c020
c021 Perfis de usuários
c022
c024 Banco de Dados Clientes
c025 Usuários Clientes
c026 Contadores Clientes
c027
c028
c029 Permissões de Acesso
c030
c031 Configurações Globais
c032 Avisos do sistema
d001 Produtos
d002 Sub-Grupos de Produtos
d003 Linhas de Produtos
d004 Locações do Estoque
d005 NCM
d006 CFOP
d007 Contas Bancárias e Caixa
d008
d009 Produtos - Empresa
d010
d011
d012
d013 Contatos dos Clientes / Fornecedores
d014 Plano de Contas
d015
d016
d017
d018 Estados (UF)
d019
d020 Cidades
d021 Representantes
d022 Transportadoras
d023
d024 Clientes / Fornecedores
d025
d026
d027 Portadores (Receber)
d028
d029
d030 Países
d031 Portadores (Pagar)
d032
d033
d034
d035
d036 Tipos de Transporte
d037 Unidades
d038
d039
d040
d041
d042
d043
d044
d045
d046
d047
d048
d049 Produtos / Marcas
d050
d051
d052
d053 ICMS
d054
d055
d056
d057
d058
d059
d060
d061
d062
d063
d064
d065
d066
d067
d068
d069
d070
d071
d072
d073 Centros de Custos
d074
d075
d076
d077
d078
d079
d080
d081
d082 Marcas
d083
d084
d085
d086
d087
d088
d089
d090
d100
d101
d105
d106 Bens
d107 Catálogos
d108 Ítens do Catálogo
d109 Funcionários
d110 Competências
d111 Materiais
d112 Serviços
d113 Processos
d114 Serviços/Materiais
d115 Competências / Funcionários
d116 Cadastro de veículos
d117 Agenda de contatos
d118 Portos
d119 Despachantes
d120 Checklist dos equipamentos
d121 Especificações dos equipamentos
d122 Processos da DI
d123 Documentos dos funcionários
d124
d125
d126
d127
d128
d129
d130
d131
d132
d133
d134 Cores
d135
d136 Produto E-commerce
d137 Regras CFOP
d138
d139
d140
d141
d142 Atalhos Teclas
d143 Excessões de ICMS
d144
d145
d146
d147
d148 Endereços cliente
d149 NCM por empresa
d150 
d151
d152
d153
d154
d155
d156
d157
d158
d159
d160
d161
d162
d163 Status OC
d164 Tipo RMA
d165
d166 Quadro Suporte
d167 Lista Suporte
d168 Transportadora E-commerce
d169
d170 Cadastro Veículos
d171 Cadastro Lacres
d172 Cadastro Seguradoras
d173 Cadastro de Apólices
d174 Cadastro de Averbação
d175 Impressoras
d176 Relatórios BI
d177 Relatórios BI / Perfis
dim001
dim002
dim003
dim004
dim005
dim006
dim007
dim008
dim009
dim010
dim011
dim012
dim013
f001
f002
f003
f004
f005
f006
f007
f008
f009
f010
f011
f012
fac001
i001
i002
i003
l001
manual
n001 Notificações
r001
r002
r003
r004
s001
s002
s003
s006
s007
s008
s009
t001 Histórico de Movimentações (Entradas/Saídas) do Produto
t002 Contas a Receber
t003 Orçamentos
t004 Itens do Orçamento
t005 Pedidos
t006 Itens do Pedido
t007 Notas Fiscais
t008 Itens das Notas Fiscais
t009
t010 Cotações de Compra
t011 Ordens de Compra
t012 Itens das Ordens de Compra
t013 Notas Fiscais Recebidas
t014 Itens das Notas Fiscais Recebidas
t015 Contas a Pagar
t016
t017
t018
t019 Lançamentos Bancários / Caixa
t020
t021
t022
t023
t024
t025
t026
t027
t028
t029
t030
t031
t032
t033
t034
t035 Remessas Bancárias
t036
t037 Header Retorno Bancário
t038
t039
t040
t041
t042
t043
t044
t045
t046
t047 Montagens
t048 Itens das Montagens
t049
t050
t051
t052
t053
t054
t055 Itens das Declarações de Importação (DI)
t056
t057
t058
t059 Itens das Ordens de Compra
t060
t061
t062
t063
t064
t065
t066
t067
t068
t069
t070
t071
t072
t073
t074
t075 Declarações de Importação (DI)
t076
t077
t078
t079
t080
t081
t082
t083
t084 Conhecimentos
t085
t086
t087
t088
t089 Pagamentos Orçamento
t090 Pagamentos Pedido
t091
t092
t093
t094
t095
t096
t097
t098
t099
t100
t101
t102
t103
t104
t105
t106
t107
t108
t109
t110
t111
t112
t113
t114
t115
t116
t117 Boletos Bancários
t118 Pagamentos Nota Fiscal
t119
t120
t121
t122
t123
t124
t125
t126
t127
t128
t129
t130
t131
t132
t133
t134 Simples Nacional
t135
t136
t137
t138
t139 Emails
t140
t141
t142
t143
t144
t145
t146
t147
t148
t149
t152
t153
t154 Pagamento Nota Recebidas
t155 Documentos da DI
t156
t157
t158
t159
t160
t161
t162 Auditoria
t163
t164
t165
t166 Histórico Reparcelamento Contas (Receber)
t167 Documentos Pedidos
t168 Despesas DI
t169
t170
t171
t172
t173
t174
t175
t177
t178 Histórico Pedidos
t179
t180 Reparcelamento Contas (Receber)
t181
t182 Controle RMA
t183
t184
t185
t186
t187
t188 Documentos Orçamento
t189
t190
t191
t192
t193
t194
t195
t196
t197 Reparcelamento Contas (Pagar)
t198
t199
t200
t201
t202
t203 Nota Fiscal Serviço
t204
t205
t206
t208
t209
t210
t211
t212
t213
t215 Cabeçalho Extratos Bancarios
t216 Itens Extratos Bancarios
t217 Regras Extratos Bancarios
t218 Cabeçalho Cotação de Compra
t219 Itens Cotação de Compra
t220 Pagamento Cotação de Compra
t221 Documentos Cotação de Compra
t222 Trocas Cotação de Compra
t223 Serviços Cotação de Compra
t224 Cabeçalho Ordem de compra
t225 Itens Ordem de Compra
t226 Pagamento Ordem de Compra
t227 Documentos Ordem de Compra
t228 Histórico Cotação de Compra
t229 Trocas Ordem de Compra
t230 Serviços Ordem de Compra
t231
t232
t233
t234 Chamados Suporte
t235 Categoria de Chamados Suporte
t236
t237
t238 Separação por Locação
t239 Preço Loja
t240
t241 Fornecedores por Produto
t242 Documentos Contas a Receber
t243 Documentos Contas a Pagar
t244
t245
t246
t247
t248
t249
t250
t251
t252 Interações Chamados
t253 Anexos Suporte
t254 Histórico Desenvolvimento
t255 Cabeçalho Ordem de Produção
t256 Itens Ordem de Produção
t257 Entrada de Produção
t258 Email Whitelist
t259 Tarefas
t260
t261
t262
t263
t264
t265 Log Crontab
t266
t267
t268 Postagem Portal Postal
t269 Carrinho B2B
t270
t271 Documentos RMA
t272 Pacote de melhorias
t273 Detalhes Trello
t274
t275
t276 Cabeçalho MDF-e
t277 Percurso/ Carregamento
t278 Condutores MDF-e
t279 Seguradora MDF-e
t280 Tipo Lacre MDF-e
t281 Tipo Unidade Transporte MDF-e
t282 Tipo Unidade Carga MDF-e
t283 Modal Rodoviário
t284 Notas MDF-e
t285 Log MDF-e
t286 Lacre Modal Rodoviário
t287 Lacre Transporte
t288 Lacre Carga
t289 FIFO Entrada
t290 FIFO Saída
t291 Usuário/Representante
x001
x002
x003
EOT;
    $tabela = strtolower($tabela);
    preg_match("#^{$tabela}(.*)\$#m", $dic, $matches);
    if (isset($matches[1])) {
        $matches[1] = trim($matches[1]);
        if (empty($matches[1])) {
            return strtoupper($tabela);
        } else {
            return $matches[1];
        }
    } else {
        return strtoupper($tabela);
    }
}

function gCarregarPrograma($prog, $varRetorno = 'output')
{
    global $g;
    $g['r'] = $prog;
    $g['r_'] = $g['r'];
    $g['r_'] = preg_replace('/^\\//', '', $g['r_']);
    $g['r'] = explode('/', trim($g['r'], '/'));
    $programaPhp = "prog/{$g['r'][0]}/{$g['r'][1]}/{$g['r'][2]}.php";
    if (file_exists($programaPhp)) {
        ob_start();
        $programa = trim($g['r_'], '/');
        $programa = str_replace('/', '-', $programa);
        $programaGlobalIde = "ide/personalizacaoConf/{$programa}-alterarPrograma.php";
        $programaGlobalApps = Apps::getDirAtivos() . "personalizacaoConf/{$programa}-alterarPrograma.php";
        $programaGlobalAppsEditor = Apps::getDirEditor() . "personalizacaoConf/{$programa}-alterarPrograma.php";
        $programaGlobalGlobal = "{$g['pathDados']}personalizacaoConf/{$programa}-alterarPrograma.php";
        $programaGlobal = $programaGlobalIde;
        if (!$g['modoDesenvolvedor'] && file_exists($programaGlobalApps)) {
            $programaGlobal = $programaGlobalApps;
        }
        if ($g['modoDesenvolvedorApp'] && file_exists($programaGlobalAppsEditor)) {
            $programaGlobal = $programaGlobalAppsEditor;
        }
        if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($programaGlobalGlobal)) {
            $programaGlobal = $programaGlobalGlobal;
        }
        $programaEmpresaIde = "ide/personalizacaoConf/{$g['empresaAtual']}{$programa}-alterarPrograma.php";
        $programaApps = Apps::getDirAtivos() . "personalizacaoConf/{$g['empresaAtual']}{$programa}-alterarPrograma.php";
        $programaAppsEditor = Apps::getDirEditor() . "personalizacaoConf/{$g['empresaAtual']}{$programa}-alterarPrograma.php";
        $programaEmpresaGlobal = "{$g['pathDados']}personalizacaoConf/{$g['empresaAtual']}{$programa}-alterarPrograma.php";
        $programaEmpresa = $programaEmpresaIde;
        if (!$g['modoDesenvolvedor'] && file_exists($programaApps)) {
            $programaEmpresa = $programaApps;
        }
        if ($g['modoDesenvolvedorApp'] && file_exists($programaAppsEditor)) {
            $programaEmpresa = $programaAppsEditor;
        }
        if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($programaEmpresaGlobal)) {
            $programaEmpresa = $programaEmpresaGlobal;
        }
        if (!(!file_exists($programaEmpresa))) {
            $programaGlobal = $programaEmpresa;
        }
        if (file_exists($programaGlobal)) {
            $tmp = $g['r'][3];
            $g['r'][3] = '';
            include $programaPhp;
            ob_end_clean();
            ob_start();
            $g['r'][3] = $tmp;
            include $programaGlobal;
        } else {
            include $programaPhp;
        }
        $output = ob_get_contents();
        ob_end_clean();
        return ${$varRetorno};
    } else {
        die('Erro interno - Por favor contate o suporte técnico.');
    }
}

function gCodigoEtiqueta($txt)
{
    global $g;
    $retorno = '';
    $i = 0;
    while ($i < strlen($txt)) {
        $retorno .= ord($txt[$i]) . " ";
        $i++;
    }
    return trim($retorno);
}

function getD004($T066_Id)
{
    $sql = "SELECT T066_D004_Id FROM T066 WHERE T066_Id = '{$T066_Id}'";
    $res = mysql_query($sql);
    if (mysql_num_rows($res) > 0) {
        $row = mysql_fetch_assoc($res);
        $tmpD004_Id = $row['T066_D004_Id'];
    } else {
        $tmpD004_Id = '';
    }
    return $tmpD004_Id;
}

function reprocessarEmpresaAtual($id)
{
    global $g;
    $g['empresaAtual'] = $id;
    $sql = <<<EOT
\t\tselect C021_Id from C007
\t\tleft join C019 on C019_C007_Id=C007_Id and C019_C004_Id='{$id}'
\t\tleft join C021 on C021_Id=C019_C021_Id
\t\twhere C007_Id = '{$g['usuarioAtual']}'
\t\tand (C007_D024_Id=0 or C007_D024_Id is null)
EOT;
    $res = mysql_query($sql);
    $row = mysql_fetch_assoc($res);
    $g['perfilUsuario'] = $row['C021_Id'];
    $check = recuperarCacheTabela('C020', array('C020_C004_Id' => $g['empresaAtual']));
    if ($check === false) {
        $g['c020'] = $g['C020'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C020 WHERE C020_C004_Id = '{$g['empresaAtual']}';"));
    }
    $check = recuperarCacheTabela('C004', array('C004_Id' => $g['empresaAtual']));
    if ($check === false) {
        $g['c004'] = $g['C004'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id LEFT JOIN D018 on D018_Id=C004_D018_Id WHERE C004_Id='{$g['empresaAtual']}';"));
    }
    $check = recuperarCacheTabela('C007', array('C007_Id' => $g['usuarioAtual']));
    if ($check === false) {
        $g['c007'] = $g['C007'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}';"));
    }
    $check = recuperarCacheTabela('C021', array('C021_Id' => $g['perfilUsuario']));
    if ($check === false) {
        $g['c021'] = $g['C021'] = mysql_fetch_assoc(mysql_query("SELECT * FROM C021 WHERE C021_Id='{$g['perfilUsuario']}';"));
    }
    $check = recuperarCacheTabela('C030', array('C030_C021_Id' => $g['perfilUsuario'], 'C030_Acesso_Liberado' => 'S'));
    if ($check === false) {
        $g['c029Ids'] = array();
        $tmp = mysql_query("SELECT C030_C029_Id FROM C030 WHERE C030_C021_Id='{$g['perfilUsuario']}' AND C030_Acesso_Liberado = 'S';");
        if ($tmp) {
            while ($row = mysql_fetch_row($tmp)) {
                $g['c029Ids'][$row[0]] = true;
            }
        }
    }
    $check = recuperarCacheTabela('C031', array('C031_C004_Id' => $g['empresaAtual']));
    if ($check === false) {
        $sqlC031 = mysql_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
        while ($rowC031 = mysql_fetch_assoc($sqlC031)) {
            $g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            $g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
        }
        $sqlC031 = mysql_query("SELECT C031_C004_Id, C031_Campo, C031_Valor FROM C031 WHERE C031_Campo IN \n\t\t\t\t\t\t\t\t\t('mensagemLiberacaoDeCredito', 'desabilitarCotacaoEmergencia', 'desabilitarCompraEmergencia', 'desabilitarMontagemEmergencia', 'desabilitarServicoEmergencia', 'desabilitarProducaoEmergencia')\n\t\t\t\t\t\t\t\tORDER BY C031_C004_Id;");
        while ($rowC031 = mysql_fetch_assoc($sqlC031)) {
            $g['c031_global'][$rowC031['C031_C004_Id']][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
            $g['C031_global'][$rowC031['C031_C004_Id']][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
        }
    }
    if ($g['c007']['C007_Id'] == 63) {
        $g['isRoot'] = true;
        $g['c029Ids'][53] = true;
        $g['c029Ids'][54] = true;
        $g['c029Ids'][84] = true;
    }
}

function modoModelo($modelo)
{
    global $r_modoModelo, $g;
    if (!(isset($r_modoModelo))) {
        $id = md5($g['r_']);
        $dir = $g['pathDadosUsuario'] . 'gridModelo/';
        $file = $dir . $id;
        if (!(!file_exists($file))) {
            $r_modoModelo = file_get_contents($file);
        }
    }
    if (!(isset($r_modoModelo) && $r_modoModelo == $modelo)) {
        return false;
    } else {
        return true;
    }
}

function quebraBr($texto, $tamanho = 70, $char = ';')
{
    $novo = '';
    $count = 0;
    $i = 0;
    while ($i < strlen($texto)) {
        $count++;
        while (!($texto[$i] == $char)) {
            if ($count > $tamanho && $texto[$i] == ' ') {
                $count = 0;
                $novo .= '<br />';
            } else {
                $novo .= $texto[$i];
            }
            $i++;
        }
        $count = 0;
    }
    return $novo;
}

function retirarLimit($sql)
{
    $sql = preg_split('#LIMIT [0-9]+\\,#', $sql);
    return $sql[0];
}

function mb_str_pad($input, $pad_length, $pad_string = " ", $pad_style = STR_PAD_RIGHT, $encoding = "UTF-8")
{
    return str_pad($input, strlen($input) - mb_strlen($input, $encoding) + $pad_length, $pad_string, $pad_style);
}

function registrarLog($param)
{
    global $g;
    $tabela = $param[0];
    $registroId = $param[1];
    $C004_Id = isset($param[2]) ? $param[2] : $g['empresaAtual'];
    $referencia = mysql_real_escape_string($param[3]);
    $descricao = mysql_real_escape_string($param[4]);
    $log = mysql_real_escape_string($param[5]);
    $flagErro = $param[6];
    $tipoLog = $param[7];
    $hash = md5(implode("", $param));
    $sql = mysqli_query("SELECT T245_Id FROM T245 WHERE T245_Hash = '{$hash}'", $g['conexaoBancoAuditoria']);
    if (mysql_num_rows($sql) <= 0) {
        mysqli_query("INSERT INTO T245 (T245_C007_Id,\n\t\t\t\t\t\t\t\t\t   T245_C004_Id,\n\t\t\t\t\t\t\t\t\t   T245_DataHora,\n\t\t\t\t\t\t\t\t\t   T245_Registro_Id,\n\t\t\t\t\t\t\t\t\t   T245_Tabela,\n\t\t\t\t\t\t\t\t\t   T245_Referencia,\n\t\t\t\t\t\t\t\t\t   T245_Descricao,\n\t\t\t\t\t\t\t\t\t   T245_Log,\n\t\t\t\t\t\t\t\t\t   T245_Flag_Erro,\n\t\t\t\t\t\t\t\t\t   T245_Tipo_Log,\n\t\t\t\t\t\t\t\t\t   T245_Hash\n\t\t\t\t\t\t\t\t\t  ) VALUES (\n\t\t\t\t\t\t\t\t\t  \t'{$g['usuarioAtual']}',\n\t\t\t\t\t\t\t\t\t  \t'{$C004_Id}',\n\t\t\t\t\t\t\t\t\t  \tnow(),\n\t\t\t\t\t\t\t\t\t  \t'{$registroId}',\n\t\t\t\t\t\t\t\t\t  \t'{$tabela}',\n\t\t\t\t\t\t\t\t\t  \t'{$referencia}',\n\t\t\t\t\t\t\t\t\t  \t'{$descricao}',\n\t\t\t\t\t\t\t\t\t  \t'{$log}',\n\t\t\t\t\t\t\t\t\t  \t'{$flagErro}',\n\t\t\t\t\t\t\t\t\t  \t'{$tipoLog}',\n\t\t\t\t\t\t\t\t\t  \t'{$hash}'\n\t\t\t\t\t\t\t\t\t  )", $g['conexaoBancoAuditoria']);
    }
    return true;
}

function secToTime($tempoSegundos)
{
    $horas = floor($tempoSegundos / 3600);
    $minutos = floor(($tempoSegundos - $horas * 3600) / 60);
    $segundos = floor($tempoSegundos % 60);
    $horas = $horas == 0 ? '00' : $horas;
    $minutos = $minutos == 0 ? '00' : $minutos;
    $segundos = $segundos == 0 ? '00' : $segundos;
    return "{$horas}:{$minutos}:{$segundos}";
}

function inteliSubstr($text, $length, $end = '...')
{
    $text = trim(preg_replace('~(\\s+)~', ' ', strip_tags($text)));
    if (strlen($text) > $length) {
        $cut = substr($text, 0, $length);
        if (substr($text, $length, 1) != ' ') {
            $cut = substr($cut, 0, strrpos($cut, ' '));
        }
        $text = $cut . $end;
    }
    return $text;
}

function processarCacheTabela($tabela)
{
    global $confUsuario;
    $cache = array();
    if ($tabela == "C004") {
        $sqlCache = mysqli_query("SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id LEFT JOIN D018 on D018_Id=C004_D018_Id");
    } else {
        if ($tabela == "C031") {
            $sqlCache = mysqli_query("SELECT C031_Id, C031_C004_Id, C031_Campo, C031_Valor FROM C031");
        } else {
            $sqlCache = mysqli_query("SELECT * FROM {$tabela}");
        }
    }
    while ($rowCache = mysql_fetch_assoc($sqlCache)) {
        $cache[] = $rowCache;
    }
    apc_delete("cache{$tabela}-{$confUsuario['dbDatabase']}");
    apc_store("cache{$tabela}-{$confUsuario['dbDatabase']}", serialize($cache));
    log("vai dar um store no cache");
}

function recuperarCacheTabela($tabela, $parametros, $ignorarCacheVazio = false)
{
    global $g, $confUsuario;
    $arrayC031 = array('mensagemLiberacaoDeCredito', 'desabilitarCotacaoEmergencia', 'desabilitarCompraEmergencia', 'desabilitarMontagemEmergencia', 'desabilitarServicoEmergencia', 'desabilitarProducaoEmergencia');
    if (in_array($tabela, $g['tabelasCache'])) {
        $cache = apc_fetch("cache{$tabela}-{$confUsuario['dbDatabase']}");
        if (!(empty($cache) && $ignorarCacheVazio == false)) {
            $cache = unserialize($cache);
            foreach ($cache as $key => $rowDB) {
                if ($tabela == 'C031' && in_array($rowDB['C031_Campo'], $arrayC031)) {
                    $g['c031_global'][$rowDB['C031_C004_Id']][$rowDB['C031_Campo']] = $rowDB['C031_Valor'];
                    $g['C031_global'][$rowDB['C031_C004_Id']][$rowDB['C031_Campo']] = $rowDB['C031_Valor'];
                }
                foreach ($parametros as $variavel => $valor) {
                    if ($rowDB[$variavel] != $valor) {
                        continue 2;
                    }
                }
                if ($tabela == 'C019') {
                    $g['C019'][$rowDB['C019_C004_Id']] = true;
                    $g['c019'][$rowDB['C019_C004_Id']] = true;
                } else {
                    if ($tabela == 'C031') {
                        $g[strtolower($tabela)][$rowDB['C031_Campo']] = $rowDB['C031_Valor'];
                        $g[strtoupper($tabela)][$rowDB['C031_Campo']] = $rowDB['C031_Valor'];
                    } else {
                        if ($tabela == 'C030') {
                            $g['c029Ids'][$rowDB['C030_C029_Id']] = true;
                            $g['c029Ids'][$rowDB['c030_C029_Id']] = true;
                        } else {
                            $g[strtolower($tabela)] = $g[strtoupper($tabela)] = $rowDB;
                        }
                    }
                }
            }
            return true;
        } else {
            processarCacheTabela($tabela);
            recuperarCacheTabela($tabela, $parametros, true);
            return true;
        }
    } else {
        return false;
    }
}

function abreviarNumero($numero)
{
    $valores = array('1000000000' => 'bi', '1000000' => 'mi', '1000' => 'mil');
    foreach ($valores as $valor => $abreviacao) {
        if (!($numero > $valor && $numero / $valor > 1)) {
        } else {
            $numero_ = gCorrigeNumero($numero);
            return "<span title=\"{$numero_}\">" . number_format($numero / $valor, 2, '.', '') . " {$abreviacao}</span>";
        }
    }
    return gCorrigeNumero($numero);
}

function msg_alerta($Msg, $Tipo = 'S')
{
    $Msg2 = $Msg;
    $Msg = str_replace('/p', '\\n', $Msg);
    ?>
	  <script>
	  alert("ATENÇÃO!!\n\n<?php echo $Msg; ?>");
	  </script>
	<?php
    if ($Tipo == 'S') {
        $Msg2 = str_replace('/p', '<BR>', $Msg2);
        $Msg2 = str_replace('\\t', '', $Msg2);
    }
}

function msg_erro_boleto($msg)
{
    echo "\n\n" . $msg;
}

function recursive_array_search($needle, $haystack, $column = '')
{
    foreach ($haystack as $key => $value) {
        $current_key = $key;
        if (is_array($value)) {
            $current_key = recursive_array_search($needle, $value, $column);
            if (!(empty($column) || trim($current_key) == trim($column))) {
            } else {
                return $current_key;
            }
        } else {
            if (trim($needle) === trim($value)) {
                if (!(empty($column) || trim($current_key) == trim($column))) {
                } else {
                    return $current_key;
                }
            }
        }
    }
    return false;
}

function gRetornaVazio($valor)
{
    return '&nbsp;';
}

function gProcessaEtapas($titulo, $classe, $metodo, $sql, $idBotao = false, $divId = false, $autoExec = false)
{
    global $g;
    if ($divId === false) {
        $divId = $g['divId'];
    }
    $autoExec = $autoExec ? 'true' : 'false';
    if ($idBotao === false) {
        return<<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\tprocessaEtapas("{$titulo}","{$classe}", "{$metodo}", "{$sql}", 0, "{$divId}", null, null, {$autoExec});
\t\t\t} </script></div>
EOT;
    } else {
        return<<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#{$idBotao}').bind('click', function(data) {
\t\t\t\t\tprocessaEtapas("{$titulo}","{$classe}", "{$metodo}", "{$sql}", 0, "{$divId}", null, null, {$autoExec});
\t\t\t\t});
\t\t\t} </script></div>
EOT;
    }
}

function gGeraBotao($label, $id, $funcao = false, $permissao = false, $valorPermissao = false)
{
    global $g;
    if ($permissao !== false && $permissao != $valorPermissao) {
        $disabled = 'DISABLED';
    }
    $return = "<button type='button' id='{$id}' {$disabled}>{$label}</button>";
    if ($funcao !== false) {
        $return .= <<<EOT
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\$('#{$id}').unbind('click').bind('click', function(data) {
\t\t\t\t\t{$funcao};
\t\t\t\t});
\t\t\t} </script></div>
EOT;
    }
    return $return;
}

function gComprimeSql($sql)
{
    $sql = str_replace(chr(9), '', $sql);
    $sql = str_replace(chr(10), ' ', $sql);
    $sql = str_replace(chr(13), '', $sql);
    $sql = str_replace(', ', ',', $sql);
    $sql = str_replace('  ', ' ', $sql);
    $sql = str_replace(' left join ', '[L]', $sql);
    $sql = base64_encode($sql);
    return $sql;
}

function gDescomprimeSql($sql)
{
    return str_replace('[L]', ' left join ', base64_decode($sql));
}

function gFecharJanela($divId)
{
    echo<<<EOT
\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\tfecharJanela("{$divId}");
\t\t}</script></div>
EOT;
}

function gPopularExtraBI($extra)
{
    global $g;
    if ($g['C007']['C007_Flag_Vendedor'] != 'D') {
        if ($g['C007']['C007_Flag_Vendedor'] == 'S') {
            $extra = gInsertExtraWhere($extra, " (D4Supervisor.DIM004_C007_Id = '{$g['C007']['C007_C007_Id']}' OR D4Vendedor.DIM004_C007_Id = '{$g['C007']['C007_Id']}') ");
        } else {
            $extra = gInsertExtraWhere($extra, " D4Vendedor.DIM004_C007_Id = '{$g['C007']['C007_Id']}' ");
        }
    }
    return $extra;
}

function gVerificaRoot()
{
    global $g;
    $liberado = $g['usuarioAtual'] == '63' || $g['C007']['C007_Usuario_Suporte'] == 'S' || $_SESSION['usuarioLogadoComPermissao'];
    if (!(isset($_SESSION['usuarioLogadoComPermissao']))) {
        $_SESSION['usuarioLogadoComPermissao'] = $liberado;
    }
    return $liberado;
}

function gRepassarParametros($modoArray = false, $excluirParametros = array())
{
    $retorno = $modoArray ? array() : '';
    $excluir = array_merge(array('divId', 'divIdRoot', 'divIdParent', 'r', 'ajax', 'divIdChain', 'divIdParentChain'), $excluirParametros);
    foreach ($_REQUEST as $key => $value) {
        if (!(in_array($key, $excluir))) {
            if ($modoArray) {
                $retorno[$key] = $value;
            } else {
                if (!(is_array($value) || is_object($value))) {
                    $retorno .= "&{$key}=" . urlencode($value);
                }
            }
        }
    }
    return $retorno;
}

function verificarPrefixoTabela($tabela)
{
    global $g, $sys;
    if ($g['modoDesenvolvedor']) {
        if ($sys['prefixoTabelas']) {
            return preg_match('/^' . preg_quote($sys['prefixoTabelas']) . '[0-9]{3}$/i', strtoupper($tabela));
        } else {
            return false;
        }
    } else {
        if ($g['modoDesenvolvedorApp']) {
            $app = Apps::getApp($g['modoDesenvolvedorApp']);
            if (!empty($app['prefixoTabela'])) {
                return preg_match('/^' . preg_quote($app['prefixoTabela']) . '[0-9]{3}$/i', strtoupper($tabela));
            } else {
                return false;
            }
        } else {
            return preg_match('/^P[0-9]{3}$/i', strtoupper($tabela));
        }
    }
}

function unserialize($content)
{
    $retorno = \unserialize($content);
    if (!$retorno) {
        return \unserialize(str_replace("\r", '', $content));
    } else {
        return $retorno;
    }
}

function cacheBotaoLote($array, $valor = false, $ttl = 300)
{
    $uniqId = $array['uniqId'];
    if ($valor) {
        apc_store($uniqId, $valor, $ttl);
    } else {
        $retorno = apc_fetch($uniqId);
        if ($array['registroAtual'] == $array['totalRegistros']) {
            apc_delete($uniqId);
        }
        return $retorno;
    }
}

function executarCodigo($id, $params = array(), $sql = '')
{
    global $g;
    $voltarModo = Personalizacoes::$rootDir;
    $metodo = false;
    Personalizacoes::$rootDir = 'ide/';
    foreach (Personalizacoes::listarCodigosNovos() as $codigo) {
        $metodo_ = Personalizacoes::getMetodoNovo($codigo, $id);
        $metodo = $metodo_ ? $metodo_ : $metodo;
    }
    if (!($g['modoDesenvolvedor'])) {
        Personalizacoes::$rootDir = Apps::getDirAtivos();
        foreach (Personalizacoes::listarCodigosNovos() as $codigo) {
            $metodo_ = Personalizacoes::getMetodoNovo($codigo, $id);
            $metodo = $metodo_ ? $metodo_ : $metodo;
        }
    }
    if (!(!$g['modoDesenvolvedorApp'])) {
        Personalizacoes::$rootDir = Apps::getDirEditor();
        foreach (Personalizacoes::listarCodigosNovos() as $codigo) {
            $metodo_ = Personalizacoes::getMetodoNovo($codigo, $id);
            $metodo = $metodo_ ? $metodo_ : $metodo;
        }
    }
    if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp']) {
        Personalizacoes::$rootDir = $g['pathDados'];
        foreach (Personalizacoes::listarCodigosNovos() as $codigo) {
            $metodo_ = Personalizacoes::getMetodoNovo($codigo, $id);
            $metodo = $metodo_ ? $metodo_ : $metodo;
        }
    }
    if ($metodo) {
        $codigo = $metodo['codigo'];
        $codigo = preg_replace('/^\\<\\?php/', '', $codigo);
        $codigo = str_replace('namespace hardness;', '', $codigo);
        $codigo = trim($codigo);
        $return = '';
        if (empty($sql)) {
            $return = Personalizacoes::carregarExecutarCallback($codigo, $params);
        } else {
            $resultado = array();
            $registros = mysql_query($sql);
            while ($registro = mysql_fetch_assoc($registros)) {
                $paramsRegistro = array_merge($params, array('todosCampos' => $registro));
                $retornoCallback = Personalizacoes::carregarExecutarCallback($codigo, $paramsRegistro);
                while (!$retornoCallback) {
                }
                $resultado[] = $retornoCallback;
            }
            $return = empty($resultado) ? '' : $resultado;
        }
        Personalizacoes::$rootDir = $voltarModo;
        return $return;
    } else {
        Personalizacoes::$rootDir = $voltarModo;
        return false;
    }
}

function gGerarUrlBackend($url, $parametros = '')
{
    global $g;
    $url = ltrim($url, '/');
    return "{$g['urlRaiz']}{$url}?ajax=true&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}{$parametros}";
}

function verificarConexaoClickhouse()
{
    global $g, $confUsuario, $confUsuarioClickHouse;
    if (!isset($g['conexaoBancoClickHouse']) && file_exists('bibliotecas/conexaoBancoClickHouse.php')) {
        include_once 'bibliotecas/conexaoBancoClickHouse.php';
    }
}

function gSubstituirVariaveisParametros($texto, $urlencode = false)
{
    foreach ($GLOBALS as $key => $value) {
        if (!(is_array($value) || is_object($value))) {
            $texto = str_replace('[[' . $key . ']]', $urlencode ? urlencode($value) : $value, $texto);
            $texto = str_replace('{' . $key . '}', $urlencode ? urlencode($value) : $value, $texto);
        }
    }
    foreach ($GLOBALS['g'] as $key => $value) {
        if (!(is_array($value) || is_object($value))) {
            $texto = str_replace('[[g-' . $key . ']]', $urlencode ? urlencode($value) : $value, $texto);
            $texto = str_replace('{g-' . $key . '}', $urlencode ? urlencode($value) : $value, $texto);
        }
    }
    foreach ($_REQUEST as $key => $value) {
        if (!(is_array($value) || is_object($value))) {
            $texto = str_replace('[[' . $key . ']]', $urlencode ? urlencode($value) : $value, $texto);
            $texto = str_replace('{' . $key . '}', $urlencode ? urlencode($value) : $value, $texto);
        }
    }
    return $texto;
}

function gSubstituirCamposJson($campo)
{
    $campo = preg_replace('/([^a-z0-9_])(.+)\\-\\>(.+)([^a-z0-9_])/imsU', '$1$2->>\'\\$.$3\'$4', " {$campo} ");
    return trim($campo);
}

function gLogDiffTime()
{
    global $g;
    $agora = microtime(true);
    $diff = $agora - $g['microtime'];
    $g['microtime'] = $agora;
    return number_format($diff, 3);
}

function gVerificarConexao()
{
    if (!connection_aborted()) {
    } else {
        die;
    }
}

function jsRecarregarCampo($campo, $vars = array())
{
    global $g;
    $varsTexto = '';
    foreach ($vars as $key => $value) {
        $varsTexto .= "&{$key}=" . urlencode($value);
    }
    echo<<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            recarregarCampo('{$g['divId']}', '{$campo}', '{$varsTexto}');
        } </script></div>
EOT;
}

function recarregarCampo($campo, $vars = array())
{
    return jsRecarregarCampo($campo, $vars);
}

function jsAlterarCampoValor($campo, $valor)
{
    global $g;
    $valorBoolean = $valor == 'toggle' ? 'toggle' : ($valor ? 'true' : 'false');
    echo<<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            if (\$('#{$g['divId']} .itemForm-{$campo}').is(':checkbox')) {
                if ('{$valorBoolean}' == 'toggle') {
                    \$('#{$g['divId']} .itemForm-{$campo}').prop('checked', !\$('#{$g['divId']} .itemForm-{$campo}').prop('checked'));
                } else {
                    \$('#{$g['divId']} .itemForm-{$campo}').prop('checked', {$valorBoolean});
                }
            } else {
                \$('#{$g['divId']} .itemForm-{$campo}').val('{$valor}');
            }
        } </script></div>
EOT;
}

function alterarCampoValor($campo, $valor)
{
    return jsAlterarCampoValor($campo, $valor);
}

function jsRecarregarPagina($id = false)
{
    global $g;
    if (!$id) {
        $id = $g['divId'];
    }
    echo<<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            divRefresh('{$id}');
        } </script></div>
EOT;
}

function recarregarPagina()
{
    return jsRecarregarPagina();
}

function jsAlert($mensagem)
{
    $mensagem = addslashes($mensagem);
    global $g;
    echo<<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            alert('{$mensagem}');
        } </script></div>
EOT;
}

function jsExecutarCodigo($codigo)
{
    echo<<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            {$codigo}
        } </script></div>
EOT;
}

function executarCodigoJS()
{
    return jsExecutarCodigo();
}

function jsConsole($mensagem)
{
    $mensagem = addslashes($mensagem);
    global $g;
    echo<<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            console.log('{$mensagem}');
        } </script></div>
EOT;
}

function jsAbrirJanela($url, $titulo, $parametros, $tamanho = '[900,600]')
{
    global $g;
    if ($tamanho == 'auto') {
        $tamanho = "'auto'";
    }
    echo<<<EOT
        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
            abrirJanela(false, divIdRootAberto(), '{$g['divId']}' , unique(), '', '{$titulo} ', '{$url}', '{$parametros}', {$tamanho});
        } </script></div>
EOT;
}


