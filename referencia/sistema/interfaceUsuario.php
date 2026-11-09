<?php
/**
 * interfaceUsuario.php
 *
 * Versão legível de sistema-legado/sistema/interfaceUsuario.php.
 * O original foi ofuscado com desvios; aqui o fluxo foi reconstruído
 * em if/else/while/foreach, no mesmo comportamento.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 *
 * O método grid() ficou de fora: ele monta o HTML da tabela, os gráficos
 * e o CSV. O contrato da grade está nos métodos abaixo.
 */
namespace hardness;

class interfaceUsuario
{
    public $gridFiltro, $colunasOcultar;

    public $textoFiltroImprimir;
    function gridFiltroExtra($personalizacaoExtra, $gridFiltroParam, $extraOriginal = '', $md5 = false, $filtroSalvo = false, $removeOrderBy = false, $limparCamposAusentes = true, $multiGridFiltroId = false, $extraLinha = false, $md5Global = false, $ignoreFiltroGlobal = false, &$filtroGlobal = false, $posicionarPrimeiroCampoPreenchido = false)
    {
        global $g, $r_pegarExtra, $r_pegarFiltros, $gridFiltro, $r_clearFILTER, $r_gridTemFiltro, $r_gridFiltrado, $filtroObrigatorio, $grid, $from, $r_linhaGridId, $r_generateHTML, $r_generateCSV, $r_opcaoDesagrupar, $r_modoModelo, $extraCount, $r_ocultarFiltro, $r_extraFixo, $gridFiltroValores, $r_callbackCarregar;
        log("(tempo) ({$g['r_']}) Até gridFiltroExtra: " . gLogDiffTime());
        if (!$r_callbackCarregar) {
            $gridID = $md5 ? $md5 : md5($g['r_']);
            $file = $g['pathDadosUsuario'] . 'gridFiltro/' . $gridID;
            if ($r_clearFILTER) {
                unlink($file);
                if (!($r_clearFILTER != 'continuar')) {
                } else {
                    die;
                }
            }
            $arquivoConfigGridIde = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-config';
            $arquivoConfigGridApps = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-config';
            $arquivoConfigGridAppsEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-config';
            $arquivoConfigGridGlobal = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-config';
            $arquivoConfigGridPerfil = "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-config';
            $arquivoConfigGridUsuario = "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-config';
            $arquivoConfigGridModelo = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-config';
            $arquivoConfigGridModeloPerfil = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-config';
            if ($g['modoDesenvolvedor']) {
                $arquivoConfigGrid = $arquivoConfigGridIde;
            } else {
                if ($g['modoDesenvolvedorApp']) {
                    $arquivoConfigGrid = $arquivoConfigGridIde;
                    $arquivoConfigGrid = file_exists($arquivoConfigGridApps) ? $arquivoConfigGridApps : $arquivoConfigGrid;
                    $arquivoConfigGrid = file_exists($arquivoConfigGridAppsEditor) ? $arquivoConfigGridAppsEditor : $arquivoConfigGrid;
                } else {
                    $arquivoConfigGrid = $arquivoConfigGridIde;
                    $arquivoConfigGrid = file_exists($arquivoConfigGridApps) ? $arquivoConfigGridApps : $arquivoConfigGrid;
                    $arquivoConfigGrid = file_exists($arquivoConfigGridGlobal) ? $arquivoConfigGridGlobal : $arquivoConfigGrid;
                    $arquivoConfigGrid = file_exists($arquivoConfigGridPerfil) ? $arquivoConfigGridPerfil : $arquivoConfigGrid;
                    $arquivoConfigGrid = file_exists($arquivoConfigGridUsuario) ? $arquivoConfigGridUsuario : $arquivoConfigGrid;
                    $arquivoConfigGrid = file_exists($arquivoConfigGridModelo) ? $arquivoConfigGridModelo : $arquivoConfigGrid;
                    $arquivoConfigGrid = file_exists($arquivoConfigGridModeloPerfil) ? $arquivoConfigGridModeloPerfil : $arquivoConfigGrid;
                }
            }
            if (file_exists($arquivoConfigGrid)) {
                $arquivoConfigGrid = file_get_contents($arquivoConfigGrid);
                $arquivoConfigGrid = unserialize($arquivoConfigGrid);
            } else {
                $arquivoConfigGrid = array();
            }
            if (!(!isset($arquivoConfigGrid['filtroObrigatorio']))) {
                $filtroObrigatorio = $arquivoConfigGrid['filtroObrigatorio'] == 'true' ? true : false;
            }
            if (!(!isset($arquivoConfigGrid['scrollLoading']))) {
                $removerScrollLoading = $arquivoConfigGrid['scrollLoading'] == 'true' ? false : true;
                $g['smarty']->assign('removerScrollLoading', $removerScrollLoading);
            }
            if (!(isset($extraCount))) {
                if ($g['modoDesenvolvedor']) {
                    $arquivos = array(isset($r_pegarExtra) ? null : "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-extra');
                } else {
                    if ($g['modoDesenvolvedorApp']) {
                        $arquivos = array("ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-extra', Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-extra', isset($r_pegarExtra) ? null : Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-extra');
                    } else {
                        $arquivos = array("ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-extra', Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-extra', isset($r_pegarExtra) ? null : "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-extra', isset($r_pegarExtra) ? null : "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-extra', isset($r_pegarExtra) ? null : "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-extra');
                        if (isset($r_modoModelo) && !empty($r_modoModelo)) {
                            $arquivos[] = "{$g['pathDados']}personalizacaoGrid/modelo-" . $r_modoModelo . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-extra';
                            $arquivos[] = "{$g['pathDados']}personalizacaoGrid/modelo-" . $r_modoModelo . '-' . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-extra';
                        }
                    }
                }
                $arquivos = array_filter($arquivos);
                foreach ($arquivos as $arquivo) {
                    if ($personalizacaoExtra && file_exists($arquivo)) {
                        $extraOriginal = Personalizacoes::extraMerge($extraOriginal, file_get_contents($arquivo));
                    }
                }
                if ($personalizacaoExtra) {
                    $extraCount = 1;
                }
            }
            if (!(isset($r_pegarExtra) && !empty($extraOriginal))) {
                $extraOriginal = preg_replace('/\\-\\- (JOIN|WHERE|GROUP BY|HAVING|ORDER BY)( (\\(REPLACE\\)|\\(OR\\)))*/i', '', $extraOriginal);
                if (isset($r_linhaGridId) && !empty($r_linhaGridId)) {
                    if (strpos($r_linhaGridId, '-') !== false) {
                        $linhaGrid_ = explode('|', $r_linhaGridId);
                        foreach ($linhaGrid_ as $linhaGridItem_) {
                            $linhaGridItem_ = explode('-', $linhaGridItem_);
                            if (!(empty($linhaGridItem_[1]))) {
                                $extraOriginal = gInsertExtraWhere($extraOriginal, "{$linhaGridItem_[0]} = '{$linhaGridItem_[1]}'");
                            }
                        }
                    } else {
                        $extraOriginal = gInsertExtraWhere($extraOriginal, "{$from}_Id = '{$r_linhaGridId}'");
                    }
                }
                $extraOriginal = gSubstituirVariaveisParametros($extraOriginal);
                if ($g['modoDesenvolvedor']) {
                    $arquivos = array(isset($r_pegarFiltros) ? null : "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-filtros');
                } else {
                    if ($g['modoDesenvolvedorApp']) {
                        $arquivos = array("ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-filtros', Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-filtros', isset($r_pegarFiltros) ? null : Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-filtros');
                    } else {
                        $arquivos = array("ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-filtros', Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-filtros', isset($r_pegarFiltros) ? null : "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-filtros', isset($r_pegarFiltros) ? null : "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-filtros', isset($r_pegarFiltros) ? null : "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-filtros');
                        if (isset($r_modoModelo) && !empty($r_modoModelo)) {
                            $arquivos[] = "{$g['pathDados']}personalizacaoGrid/modelo-" . $r_modoModelo . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-filtros';
                            $arquivos[] = "{$g['pathDados']}personalizacaoGrid/modelo-" . $r_modoModelo . '-' . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-filtros';
                        }
                    }
                }
                $arquivos = array_filter($arquivos);
                $gridFiltroParamMerge = $gridFiltroParam;
                foreach ($arquivos as $arquivo) {
                    if (!(!file_exists($arquivo))) {
                        $arrayFiltros = array();
                        $conteudo = file_get_contents($arquivo);
                        preg_match_all("/(.+)---/msU", $conteudo, $matches);
                        $count = 0;
                        foreach ($matches[0] as $value) {
                            $arrayFiltros[$count] = array();
                            preg_match_all("/([a-z]+) ?\\[(.*)\\][\n\r]/msUi", $value, $matches2);
                            foreach ($matches2[1] as $key => $value) {
                                $arrayFiltros[$count][$value] = trim($matches2[2][$key]);
                            }
                            $count++;
                        }
                        foreach ($arrayFiltros as $key => $value) {
                            if (!(isset($gridFiltroParamMerge[$value['colunaValor']]))) {
                                $gridFiltroParamMerge[$value['colunaValor']] = array();
                            }
                            $gridFiltroParamMerge[$value['colunaValor']] = array_merge($gridFiltroParamMerge[$value['colunaValor']], $value);
                            unset($gridFiltroParamMerge[$value['colunaValor']]['colunaValor']);
                            foreach ($gridFiltroParamMerge[$value['colunaValor']] as $itemKey => $itemValue) {
                                if (!($itemValue == 'Array')) {
                                    if (!is_array($itemValue)) {
                                        if (stripos($itemValue, 'array') !== false) {
                                            eval('$gridFiltroParamMerge[$value[\'colunaValor\']][$itemKey] = ' . $itemValue . ';');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $gridFiltro = $gridFiltroParam = $gridFiltroParamMerge;
                if (!isset($r_pegarFiltros)) {
                    $grid_ = array();
                    $exclude = true;
                    if (!(is_numeric(key($gridFiltroParam)))) {
                        $exclude = false;
                        foreach ($gridFiltroParam as $key => $value) {
                            $grid_[] = array('colunaValor' => $key, 'colunaTitulo' => $value['titulo'], 'colunaTipo' => $value['tipo'], 'usarHaving' => isset($value['having']) ? $value['having'] : false, 'colunaSelect' => isset($value['select']) ? $value['select'] : ($value['tipo'] == 'agrupar' ? $value['campos'] : false), 'colunaSelectNaoGerarExtra' => $value['tipo'] == 'agrupar' ? true : (isset($value['naoGerarExtra']) ? $value['naoGerarExtra'] : false), 'colunaExibir' => isset($value['exibir']) && !$value['exibir'] ? false : true, 'colunaValorFiltro' => isset($value['valor']) && $value['valor'] ? $value['valor'] : '');
                        }
                        $gridFiltroParam = $grid_;
                    }
                    if ($md5) {
                        $id = $md5;
                    } else {
                        $id = md5($g['r_']);
                    }
                    $dir = $g['pathDadosUsuario'] . 'gridFiltro/';
                    is_dir($dir) or mkdir($dir, 0777, true);
                    $file = $dir . $id;
                    if (file_exists($file)) {
                        $gridFiltroInternoOriginal = $gridFiltroInterno = unserialize(file_get_contents($file));
                        if ($multiGridFiltroId !== false) {
                            $gridFiltroInterno = $gridFiltroInterno[$multiGridFiltroId];
                            if (!(!empty($gridFiltroInterno[$multiGridFiltroId]))) {
                                $gridFiltroInterno[$multiGridFiltroId] = array('campos' => array(), 'extra' => '', 'busca' => array(), 'ordem' => '');
                            }
                        }
                        if (!(isset($gridFiltroInterno['ordem']))) {
                            $gridFiltroInterno['ordem'] = '';
                        }
                    } else {
                        $gridFiltroInterno = array('campos' => array(), 'extra' => '', 'busca' => array(), 'ordem' => '');
                    }
                    $gridFiltroValores = $gridFiltroInterno;
                    if ($md5Global) {
                        $fileGlobal = $dir . $md5Global;
                        if (!(!file_exists($fileGlobal))) {
                            $gridFiltroGlobal = unserialize(file_get_contents($fileGlobal));
                            foreach ($gridFiltroGlobal['campos'] as $key => $value) {
                                if (!(empty($value['busca']))) {
                                    if ($key != $ignoreFiltroGlobal) {
                                        $gridFiltroInterno['campos'][$key] = $value;
                                    } else {
                                        $filtroGlobal = is_array($value['busca']) ? $value['busca'][0] : $value['busca'];
                                    }
                                }
                            }
                        }
                    }
                    foreach ($gridFiltroParam as $grid_valor) {
                        if (!(isset($grid_valor['colunaExibir']) && !$grid_valor['colunaExibir'])) {
                            if (!(isset($grid_valor['colunaTipo']) && $grid_valor['colunaTipo'] == 'livre')) {
                                $tmp = explode('_', $grid_valor['colunaValor']);
                                if (!($exclude && isset($tmp[2]) && $tmp[2] == 'Id')) {
                                    if (!($exclude && (strpos($grid_valor['colunaValor'], ' as ') !== false || strpos($grid_valor['colunaValor'], '(') !== false || strpos($grid_valor['colunaValor'], '.') !== false))) {
                                        $tmp = $this->getGridFiltroCampoBusca($grid_valor['colunaValor'], $gridFiltroInterno);
                                        if ($tmp === false) {
                                            $grid_valor['colunaValorFiltro'] = isset($grid_valor['colunaValorFiltro']) ? $grid_valor['colunaValorFiltro'] : '';
                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']] = array('busca' => $grid_valor['colunaValorFiltro']);
                                            if (!(empty($grid_valor['colunaValorFiltro']))) {
                                                $gridFiltroInterno['busca'] = '!--advanced--!';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($limparCamposAusentes === true) {
                        foreach ($gridFiltroInterno['campos'] as $filtro_key => $filtro_) {
                            $found = false;
                            foreach ($gridFiltroParam as $grid_valor) {
                                if (!(isset($grid_valor['colunaExibir']) && !$grid_valor['colunaExibir'])) {
                                    if (!(isset($grid_valor['colunaTipo']) && $grid_valor['colunaTipo'] == 'livre')) {
                                        $tmp = explode('_', $grid_valor['colunaValor']);
                                        if (!($exclude && isset($tmp[2]) && $tmp[2] == 'Id')) {
                                            if (!($exclude && (strpos($grid_valor['colunaValor'], ' as ') !== false || strpos($grid_valor['colunaValor'], '(') !== false || strpos($grid_valor['colunaValor'], '.') !== false))) {
                                                if ($filtro_key == $grid_valor['colunaValor']) {
                                                    $found = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (!$found) {
                                unset($gridFiltroInterno['campos'][$filtro_key]);
                            }
                        }
                    }
                    foreach ($gridFiltroInterno['campos'] as $key => $value) {
                        if (!$filtroSalvo) {
                        } else {
                            if (!empty($filtroSalvo['campos'][$key]['busca'])) {
                                if (!(is_array($filtroSalvo['campos'][$key]['busca']) && empty(array_filter($filtroSalvo['campos'][$key]['busca'])))) {
                                    $gridFiltroInterno['campos'][$key]['busca'] = $filtroSalvo['campos'][$key]['busca'];
                                }
                            }
                        }
                    }
                    if ($filtroSalvo) {
                        $gridFiltroInterno['busca'] = $filtroSalvo['busca'];
                    }
                    $extra = '(1=1)';
                    $having = '(1=1)';
                    if (!isset($gridFiltroInterno['busca']) || $gridFiltroInterno['busca'] == '!--advanced--!') {
                        foreach ($gridFiltroParam as $grid_valor) {
                            $campo = $grid_valor['colunaValor'];
                            if (strpos($campo, '->') !== false) {
                                $campo = gSubstituirCamposJson($campo);
                                if (empty($grid_valor['colunaTipo']) || $grid_valor['colunaTipo'] == 'texto') {
                                    $campo = "LOWER({$campo})";
                                }
                            }
                            isset($grid_valor['colunaTipo']) || ($grid_valor['colunaTipo'] = '');
                            $grid_valor['colunaTipo'] = strtolower($grid_valor['colunaTipo']);
                            if (isset($gridFiltroInterno['campos'][$grid_valor['colunaValor']])) {
                                $filtro = $gridFiltroInterno['campos'][$grid_valor['colunaValor']];
                                $campoMais = explode(',', $campo);
                                $textBusca = isset($filtro['busca']) ? $filtro['busca'] : '';
                                if (!(!$g['usarClickHouse'])) {
                                    if (strpos($textBusca, 'SQL:=') === false) {
                                        $textBusca = strtoupper($textBusca);
                                    }
                                }
                                if (!(isset($grid_valor['colunaSelectNaoGerarExtra']) && $grid_valor['colunaSelectNaoGerarExtra'])) {
                                    if (!($exclude && (empty($textBusca) || strpos($campo, ' as ') !== false || strpos($campo, '(') !== false || strpos($campo, '.') !== false))) {
                                        $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] = '';
                                        if (strpos($campo, '->') !== false && (empty($grid_valor['colunaTipo']) || $grid_valor['colunaTipo'] == 'texto')) {
                                            $textBusca = strtolower($textBusca);
                                        }
                                        if (preg_match('/^SQL:/', is_array($textBusca) ? $textBusca[0] : $textBusca)) {
                                            $textBuscaTmp = is_array($textBusca) ? $textBusca[0] : $textBusca;
                                            $textBuscaTmp = preg_replace('/^SQL:/', '', $textBusca);
                                            foreach ($campoMais as $key => $campo) {
                                                $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                $logic = $key == 0 ? 'AND (' : 'OR';
                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} {$textBuscaTmp})";
                                                $extra .= " {$logic} ({$campo} {$textBuscaTmp})";
                                            }
                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                            $extra .= ')';
                                        } else {
                                            if (isset($grid_valor['colunaTipo']) && $grid_valor['colunaTipo'] == 'data') {
                                                if (!(is_array($textBusca))) {
                                                    $textBusca = array('', '');
                                                }
                                                if (!(empty($textBusca[0]) && empty($textBusca[1]))) {
                                                    if (empty($textBusca[0]) && !empty($textBusca[1])) {
                                                        $textBusca = $textBusca[1];
                                                    } else {
                                                        if (!empty($textBusca[0]) && empty($textBusca[1])) {
                                                            $textBusca = $textBusca[0];
                                                        }
                                                    }
                                                    if (!is_array($textBusca)) {
                                                        $textBusca = preg_replace('/([0-9]{2})\\/([0-9]{2})\\/([0-9]{4})/i', '$3-$2-$1', $textBusca);
                                                        $textBusca = preg_replace('/([0-9]{2})\\/([0-9]{4})/i', '$2-$1', $textBusca);
                                                        $textBusca = preg_replace('/([0-9]{2})\\/([0-9]{2})/i', '$2-$1', $textBusca);
                                                        $textBusca = mysql_real_escape_string($textBusca);
                                                        if (stripos($textBusca, '*vazio*') !== false) {
                                                            foreach ($campoMais as $key => $campo) {
                                                                $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                $logic = $key == 0 ? 'AND (' : 'OR';
                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} is NULL)";
                                                                $extra .= " {$logic} ({$campo} is NULL)";
                                                            }
                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                            $extra .= ')';
                                                        } else {
                                                            foreach ($campoMais as $key => $campo) {
                                                                $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                $logic = $key == 0 ? 'AND (' : 'OR';
                                                                $start = strpos($textBusca, '%') === false ? '%' : '';
                                                                if ($g['usarClickHouse']) {
                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} >= toDateTimeOrNull('{$textBusca} 00:00:00') AND {$campo} <= toDateTimeOrNull('{$textBusca} 23:59:59'))";
                                                                    $extra .= " {$logic} ({$campo} >= toDateTimeOrNull('{$textBusca} 00:00:00') AND {$campo} <= toDateTimeOrNull('{$textBusca} 23:59:59'))";
                                                                } else {
                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} >= '{$textBusca} 00:00:00' AND {$campo} <= '{$textBusca} 23:59:59')";
                                                                    $extra .= " {$logic} ({$campo} >= '{$textBusca} 00:00:00' AND {$campo} <= '{$textBusca} 23:59:59')";
                                                                }
                                                            }
                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                            $extra .= ')';
                                                        }
                                                    } else {
                                                        $textBusca[0] = preg_replace('/([0-9]{2})\\/([0-9]{2})\\/([0-9]{4})/i', '$3-$2-$1', $textBusca[0]);
                                                        $textBusca[1] = preg_replace('/([0-9]{2})\\/([0-9]{2})\\/([0-9]{4})/i', '$3-$2-$1', $textBusca[1]);
                                                        if (stripos($textBusca[0], '*vazio*') !== false || stripos($textBusca[1], '*vazio*') !== false) {
                                                            foreach ($campoMais as $key => $campo) {
                                                                $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                $logic = $key == 0 ? 'AND (' : 'OR';
                                                                if ($g['usarClickHouse']) {
                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} is NULL OR {$campo} = toDateTimeOrNull('0000-00-00') OR {$campo} = toDateTimeOrNull('0000-00-00 00:00:00'))";
                                                                    $extra .= " {$logic} ({$campo} is NULL OR {$campo} = toDateTimeOrNull('0000-00-00') OR {$campo} = toDateTimeOrNull('0000-00-00 00:00:00'))";
                                                                } else {
                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} is NULL OR {$campo} = '0000-00-00' OR {$campo} = '0000-00-00 00:00:00')";
                                                                    $extra .= " {$logic} ({$campo} is NULL OR {$campo} = '0000-00-00' OR {$campo} = '0000-00-00 00:00:00')";
                                                                }
                                                            }
                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                            $extra .= ')';
                                                        } else {
                                                            foreach ($campoMais as $key => $campo) {
                                                                $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                $logic = $key == 0 ? 'AND (' : 'OR';
                                                                if ($g['usarClickHouse']) {
                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} >= toDateTimeOrNull('{$textBusca[0]} 00:00:00') AND {$campo} <= toDateTimeOrNull('{$textBusca[1]} 23:59:59'))";
                                                                    $extra .= " {$logic} ({$campo} >= toDateTimeOrNull('{$textBusca[0]} 00:00:00') AND {$campo} <= toDateTimeOrNull('{$textBusca[1]} 23:59:59'))";
                                                                } else {
                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} >= '{$textBusca[0]} 00:00:00' AND {$campo} <= '{$textBusca[1]} 23:59:59')";
                                                                    $extra .= " {$logic} ({$campo} >= '{$textBusca[0]} 00:00:00' AND {$campo} <= '{$textBusca[1]} 23:59:59')";
                                                                }
                                                            }
                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                            $extra .= ')';
                                                        }
                                                    }
                                                }
                                            } else {
                                                if (isset($grid_valor['colunaTipo']) && $grid_valor['colunaTipo'] == 'dataHora') {
                                                    if (!(is_array($textBusca))) {
                                                        $textBusca = array('', '');
                                                    }
                                                    if (!(empty($textBusca[0]) && empty($textBusca[1]))) {
                                                        if (empty($textBusca[0]) && !empty($textBusca[1])) {
                                                            $textBusca = $textBusca[1];
                                                        } else {
                                                            if (!empty($textBusca[0]) && empty($textBusca[1])) {
                                                                $textBusca = $textBusca[0];
                                                            }
                                                        }
                                                        if (!is_array($textBusca)) {
                                                            $textBusca = preg_replace('/([0-9]{2})\\/([0-9]{2})\\/([0-9]{4}) ([0-9]{2}:[0-9]{2}:[0-9]{2})/i', '$3-$2-$1 $4', $textBusca);
                                                            $textBusca = preg_replace('/([0-9]{2})\\/([0-9]{2})\\/([0-9]{4})/i', '$3-$2-$1', $textBusca);
                                                            $textBusca = mysql_real_escape_string($textBusca);
                                                            if (stripos($textBusca, '*vazio*') !== false) {
                                                                foreach ($campoMais as $key => $campo) {
                                                                    $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                    $logic = $key == 0 ? 'AND (' : 'OR';
                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} is NULL)";
                                                                    $extra .= " {$logic} ({$campo} is NULL)";
                                                                }
                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                $extra .= ')';
                                                            } else {
                                                                $textBuscaInicio = strlen($textBusca) == 10 ? "{$textBusca} 00:00:00" : $textBusca;
                                                                $textBuscaFim = strlen($textBusca) == 10 ? "{$textBusca} 23:59:59" : $textBusca;
                                                                foreach ($campoMais as $key => $campo) {
                                                                    $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                    $logic = $key == 0 ? 'AND (' : 'OR';
                                                                    if ($g['usarClickHouse']) {
                                                                        $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} >= toDateTimeOrNull('{$textBuscaInicio}') AND {$campo} <= toDateTimeOrNull('{$textBuscaFim}'))";
                                                                        $extra .= " {$logic} ({$campo} >= toDateTimeOrNull('{$textBuscaInicio}') AND {$campo} <= toDateTimeOrNull('{$textBuscaFim}'))";
                                                                    } else {
                                                                        $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} >= '{$textBuscaInicio}' AND {$campo} <= '{$textBuscaFim}')";
                                                                        $extra .= " {$logic} ({$campo} >= '{$textBuscaInicio}' AND {$campo} <= '{$textBuscaFim}')";
                                                                    }
                                                                }
                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                $extra .= ')';
                                                            }
                                                        } else {
                                                            $textBusca[0] = preg_replace('/([0-9]{2})\\/([0-9]{2})\\/([0-9]{4}) ([0-9]{2}:[0-9]{2}:[0-9]{2})/i', '$3-$2-$1 $4', $textBusca[0]);
                                                            $textBusca[1] = preg_replace('/([0-9]{2})\\/([0-9]{2})\\/([0-9]{4}) ([0-9]{2}:[0-9]{2}:[0-9]{2})/i', '$3-$2-$1 $4', $textBusca[1]);
                                                            $textBusca[0] = preg_replace('/([0-9]{2})\\/([0-9]{2})\\/([0-9]{4})/i', '$3-$2-$1', $textBusca[0]);
                                                            $textBusca[1] = preg_replace('/([0-9]{2})\\/([0-9]{2})\\/([0-9]{4})/i', '$3-$2-$1', $textBusca[1]);
                                                            if (stripos($textBusca[0], '*vazio*') !== false || stripos($textBusca[1], '*vazio*') !== false) {
                                                                foreach ($campoMais as $key => $campo) {
                                                                    $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                    $logic = $key == 0 ? 'AND (' : 'OR';
                                                                    if ($g['usarClickHouse']) {
                                                                        $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} is NULL OR {$campo} = toDateTimeOrNull('0000-00-00') OR {$campo} = toDateTimeOrNull('0000-00-00 00:00:00'))";
                                                                        $extra .= " {$logic} ({$campo} is NULL OR {$campo} = toDateTimeOrNull('0000-00-00') OR {$campo} = toDateTimeOrNull('0000-00-00 00:00:00'))";
                                                                    } else {
                                                                        $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} is NULL OR {$campo} = '0000-00-00' OR {$campo} = '0000-00-00 00:00:00')";
                                                                        $extra .= " {$logic} ({$campo} is NULL OR {$campo} = '0000-00-00' OR {$campo} = '0000-00-00 00:00:00')";
                                                                    }
                                                                }
                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                $extra .= ')';
                                                            } else {
                                                                $textBuscaInicio = strlen($textBusca[0]) == 10 ? "{$textBusca[0]} 00:00:00" : $textBusca[0];
                                                                $textBuscaFim = strlen($textBusca[1]) == 10 ? "{$textBusca[1]} 23:59:59" : $textBusca[1];
                                                                foreach ($campoMais as $key => $campo) {
                                                                    $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                    $logic = $key == 0 ? 'AND (' : 'OR';
                                                                    if ($g['usarClickHouse']) {
                                                                        $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} >= toDateTimeOrNull('{$textBuscaInicio}') AND {$campo} <= toDateTimeOrNull('{$textBuscaFim}'))";
                                                                        $extra .= " {$logic} ({$campo} >= toDateTimeOrNull('{$textBuscaInicio}') AND {$campo} <= toDateTimeOrNull('{$textBuscaFim}'))";
                                                                    } else {
                                                                        $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} >= '{$textBuscaInicio}' AND {$campo} <= '{$textBuscaFim}')";
                                                                        $extra .= " {$logic} ({$campo} >= '{$textBuscaInicio}' AND {$campo} <= '{$textBuscaFim}')";
                                                                    }
                                                                }
                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                $extra .= ')';
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    if ($textBusca == '0') {
                                                        $textBusca = '*VAZIO*';
                                                    }
                                                    if (!empty($textBusca)) {
                                                        if (!(stripos($textBusca, '!*vazio*') !== false || $textBusca == '!')) {
                                                            if (!(stripos($textBusca, '*vazio*') !== false)) {
                                                                if (!(!empty($grid_valor['colunaTipo']))) {
                                                                    $grid_valor['colunaTipo'] = 'texto';
                                                                }
                                                                if (strpos($textBusca, '%') === false && ($grid_valor['colunaTipo'] == 'numero' || $grid_valor['colunaTipo'] == 'moeda') || $grid_valor['colunaTipo'] == 'select') {
                                                                    $textBusca_ = str_replace(',', '.', $textBusca);
                                                                    $textBuscaNormalizado = trim($textBusca_);
                                                                    if (!(!preg_match('/^==/', $textBuscaNormalizado))) {
                                                                        $textBuscaIn = preg_replace('/^==/', '', $textBuscaNormalizado);
                                                                        $textBuscaIn = explode(';', $textBuscaIn);
                                                                        $textBuscaIn = array_map('trim', $textBuscaIn);
                                                                        $textBuscaIn = array_filter($textBuscaIn, 'strlen');
                                                                        if (empty($textBuscaIn)) {
                                                                        } else {
                                                                            $textBuscaInEscapado = array();
                                                                            foreach ($textBuscaIn as $textBuscaInItem) {
                                                                                $textBuscaInEscapado[] = "'" . mysql_real_escape_string($textBuscaInItem) . "'";
                                                                            }
                                                                            $textBuscaInSql = implode(', ', $textBuscaInEscapado);
                                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " AND (";
                                                                            if ($grid_valor['usarHaving']) {
                                                                                $having .= " AND (";
                                                                            } else {
                                                                                $extra .= " AND (";
                                                                            }
                                                                            foreach ($campoMais as $key => $campo) {
                                                                                $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                                $logic = $key == 0 ? '' : ' OR';
                                                                                $inClause = count($textBuscaInEscapado) == 1 ? "= {$textBuscaInEscapado[0]}" : "IN ({$textBuscaInSql})";
                                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= "{$logic} ({$campo} {$inClause})";
                                                                                if ($grid_valor['usarHaving']) {
                                                                                    $having .= "{$logic} ({$campo} {$inClause})";
                                                                                } else {
                                                                                    $extra .= "{$logic} ({$campo} {$inClause})";
                                                                                }
                                                                            }
                                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ")";
                                                                            if ($grid_valor['usarHaving']) {
                                                                                $having .= ")";
                                                                            } else {
                                                                                $extra .= ")";
                                                                            }
                                                                        }
                                                                    }
                                                                    $textBusca_ = explode(';', $textBusca_);
                                                                    $textBuscaLogic = true;
                                                                    foreach ($textBusca_ as $textBuscaKey => $textBusca) {
                                                                        if (strlen($textBusca) > 0 && $textBusca[0] != '!') {
                                                                            $textBuscaLogic = false;
                                                                        }
                                                                    }
                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " AND (";
                                                                    if ($grid_valor['usarHaving']) {
                                                                        $having .= " AND (";
                                                                    } else {
                                                                        $extra .= " AND (";
                                                                    }
                                                                    $primeiroItem = true;
                                                                    foreach ($textBusca_ as $textBusca) {
                                                                        $textBusca = trim($textBusca);
                                                                        $textBusca = mysql_real_escape_string($textBusca);
                                                                        if ($textBusca[0] == '>' || $textBusca[0] == '<') {
                                                                            $simbolo = $textBusca[0];
                                                                            $textBusca_ = str_replace('<', '', str_replace('>', '', $textBusca));
                                                                            if ($textBusca[1] == '=') {
                                                                                $textBusca_ = str_replace('=', '', $textBusca_);
                                                                                $simbolo .= $textBusca[1];
                                                                            }
                                                                            foreach ($campoMais as $key => $campo) {
                                                                                $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                                $logic = $key == 0 ? 'OR (' : 'OR';
                                                                                if ($primeiroItem) {
                                                                                    $logic = '(';
                                                                                    $primeiroItem = false;
                                                                                }
                                                                                if ($grid_valor['usarHaving']) {
                                                                                    $having .= " {$logic} ({$campo} {$simbolo} '{$textBusca_}')";
                                                                                } else {
                                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} {$simbolo} '{$textBusca_}')";
                                                                                    $extra .= " {$logic} ({$campo} {$simbolo} '{$textBusca_}')";
                                                                                }
                                                                            }
                                                                            if ($grid_valor['usarHaving']) {
                                                                                $having .= ')';
                                                                            } else {
                                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                                $extra .= ')';
                                                                            }
                                                                        } else {
                                                                            if ($textBusca[0] == '=') {
                                                                                $textBusca_ = str_replace('=', '', $textBusca);
                                                                                foreach ($campoMais as $key => $campo) {
                                                                                    $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                                    $logic = $key == 0 ? 'OR (' : 'OR';
                                                                                    if ($primeiroItem) {
                                                                                        $logic = '(';
                                                                                        $primeiroItem = false;
                                                                                    }
                                                                                    if ($grid_valor['usarHaving']) {
                                                                                        $having .= " {$logic} ({$campo} = '{$textBusca_}')";
                                                                                    } else {
                                                                                        $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} = '{$textBusca_}')";
                                                                                        $extra .= " {$logic} ({$campo} = '{$textBusca_}')";
                                                                                    }
                                                                                }
                                                                                if ($grid_valor['usarHaving']) {
                                                                                    $having .= ')';
                                                                                } else {
                                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                                    $extra .= ')';
                                                                                }
                                                                            } else {
                                                                                if ($textBusca[0] == '!') {
                                                                                    $textBusca_ = str_replace('!', '', $textBusca);
                                                                                    foreach ($campoMais as $key => $campo) {
                                                                                        $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                                        $logic = $key == 0 ? 'AND (' : 'AND';
                                                                                        if ($primeiroItem) {
                                                                                            $logic = '(';
                                                                                            $primeiroItem = false;
                                                                                        }
                                                                                        if ($grid_valor['usarHaving']) {
                                                                                            $having .= " {$logic} ({$campo} != '{$textBusca_}')";
                                                                                        } else {
                                                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} != '{$textBusca_}')";
                                                                                            $extra .= " {$logic} ({$campo} != '{$textBusca_}')";
                                                                                        }
                                                                                    }
                                                                                    if ($grid_valor['usarHaving']) {
                                                                                        $having .= ')';
                                                                                    } else {
                                                                                        $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                                        $extra .= ')';
                                                                                    }
                                                                                } else {
                                                                                    if (stripos($textBusca, '*vazio*') !== false) {
                                                                                        foreach ($campoMais as $key => $campo) {
                                                                                            $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                                            $logic = $key == 0 ? 'OR (' : 'OR';
                                                                                            if ($primeiroItem) {
                                                                                                $logic = '(';
                                                                                                $primeiroItem = false;
                                                                                            }
                                                                                            if ($grid_valor['usarHaving']) {
                                                                                                $having .= " {$logic} ({$campo} is NULL OR {$campo} = '')";
                                                                                            } else {
                                                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} is NULL OR {$campo} = '')";
                                                                                                $extra .= " {$logic} ({$campo} is NULL OR {$campo} = '')";
                                                                                            }
                                                                                        }
                                                                                        if ($grid_valor['usarHaving']) {
                                                                                            $having .= ')';
                                                                                        } else {
                                                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                                            $extra .= ')';
                                                                                        }
                                                                                    }
                                                                                    foreach ($campoMais as $key => $campo) {
                                                                                        $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                                        if ($grid_valor['colunaTipo'] == 'numero' || $grid_valor['colunaTipo'] == 'select') {
                                                                                            $logic = $key == 0 ? 'OR (' : 'OR';
                                                                                            if ($primeiroItem) {
                                                                                                $logic = '(';
                                                                                                $primeiroItem = false;
                                                                                            }
                                                                                            if ($grid_valor['usarHaving']) {
                                                                                                $having .= " {$logic} ({$campo} = '{$textBusca}')";
                                                                                            } else {
                                                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} = '{$textBusca}')";
                                                                                                $extra .= " {$logic} ({$campo} = '{$textBusca}')";
                                                                                            }
                                                                                        } else {
                                                                                            if ($grid_valor['colunaTipo'] == 'moeda') {
                                                                                                $moreLess = 2;
                                                                                                if ($textBusca > 0) {
                                                                                                    $moreLess = round($textBusca * 0.1, 2);
                                                                                                }
                                                                                                $logic = $key == 0 ? 'OR (' : 'OR';
                                                                                                if ($primeiroItem) {
                                                                                                    $logic = '(';
                                                                                                    $primeiroItem = false;
                                                                                                }
                                                                                                if ($grid_valor['usarHaving']) {
                                                                                                    $having .= " {$logic} ({$campo} >= {$textBusca}-1 AND {$campo} <= {$textBusca}+1)";
                                                                                                } else {
                                                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} >= {$textBusca}-1 AND {$campo} <= {$textBusca}+1)";
                                                                                                    $extra .= " {$logic} ({$campo} >= {$textBusca}-1 AND {$campo} <= {$textBusca}+1)";
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                    if ($grid_valor['usarHaving']) {
                                                                                        $having .= ')';
                                                                                    } else {
                                                                                        $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                                        $extra .= ')';
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ")";
                                                                    if ($grid_valor['usarHaving']) {
                                                                        $having .= ")";
                                                                    } else {
                                                                        $extra .= ")";
                                                                    }
                                                                } else {
                                                                    if (!(!is_array($textBusca))) {
                                                                        $textBusca = '';
                                                                    }
                                                                    if (stripos($textBusca, '*vazio*') !== false) {
                                                                        foreach ($campoMais as $key => $campo) {
                                                                            $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                            $logic = $key == 0 ? 'AND (' : 'OR';
                                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} is NULL OR {$campo} = '')";
                                                                            if ($grid_valor['usarHaving']) {
                                                                                $having .= " {$logic} ({$campo} is NULL OR {$campo} = '')";
                                                                            } else {
                                                                                $extra .= " {$logic} ({$campo} is NULL OR {$campo} = '')";
                                                                            }
                                                                        }
                                                                        if ($grid_valor['usarHaving']) {
                                                                            $having .= ')';
                                                                        } else {
                                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                            $extra .= ')';
                                                                        }
                                                                    } else {
                                                                        if (!(!preg_match('/^\\s*==/', $textBusca))) {
                                                                            $textBuscaIn = preg_replace('/^\\s*==/', '', $textBusca);
                                                                            $textBuscaIn = explode(';', $textBuscaIn);
                                                                            $textBuscaIn = array_map('trim', $textBuscaIn);
                                                                            $textBuscaIn = array_filter($textBuscaIn, 'strlen');
                                                                            if (empty($textBuscaIn)) {
                                                                            } else {
                                                                                $textBuscaInEscapado = array();
                                                                                foreach ($textBuscaIn as $textBuscaInItem) {
                                                                                    $textBuscaInEscapado[] = "'" . mysql_real_escape_string($textBuscaInItem) . "'";
                                                                                }
                                                                                $textBuscaInSql = implode(', ', $textBuscaInEscapado);
                                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ' AND (';
                                                                                if ($grid_valor['usarHaving']) {
                                                                                    $having .= ' AND (';
                                                                                } else {
                                                                                    $extra .= ' AND (';
                                                                                }
                                                                                foreach ($campoMais as $key => $campo) {
                                                                                    $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                                    $logic = $key == 0 ? '' : ' OR';
                                                                                    $inClause = count($textBuscaInEscapado) == 1 ? "= {$textBuscaInEscapado[0]}" : "IN ({$textBuscaInSql})";
                                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= "{$logic} ({$campo} {$inClause})";
                                                                                    if ($grid_valor['usarHaving']) {
                                                                                        $having .= "{$logic} ({$campo} {$inClause})";
                                                                                    } else {
                                                                                        $extra .= "{$logic} ({$campo} {$inClause})";
                                                                                    }
                                                                                }
                                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                                if ($grid_valor['usarHaving']) {
                                                                                    $having .= ')';
                                                                                } else {
                                                                                    $extra .= ')';
                                                                                }
                                                                            }
                                                                        }
                                                                        if (stripos($textBusca, ';') !== false) {
                                                                            $textBuscaVarios = explode(';', $textBusca);
                                                                        } else {
                                                                            $textBuscaVarios = array($textBusca);
                                                                        }
                                                                        $textBuscaLogic = false;
                                                                        foreach ($textBuscaVarios as $textBuscaKey => $textBusca) {
                                                                            if (strlen($textBusca) > 0 && $textBusca[0] == '!') {
                                                                                $textBuscaLogic = true;
                                                                            }
                                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= $textBuscaKey == 0 ? ' AND (' : ($textBuscaLogic ? ' AND' : ' OR');
                                                                            if ($grid_valor['usarHaving']) {
                                                                                $having .= $textBuscaKey == 0 ? ' AND (' : ($textBuscaLogic ? ' AND' : ' OR');
                                                                            } else {
                                                                                $extra .= $textBuscaKey == 0 ? ' AND (' : ($textBuscaLogic ? ' AND' : ' OR');
                                                                            }
                                                                            foreach ($campoMais as $key => $campo) {
                                                                                $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                                if (strlen($textBusca) > 0 && $textBusca[0] == '!') {
                                                                                    $like = 'NOT LIKE';
                                                                                    $textBuscaTmp = str_replace('!', '', $textBusca);
                                                                                } else {
                                                                                    $like = 'LIKE';
                                                                                    $textBuscaTmp = $textBusca;
                                                                                }
                                                                                $logic = $key == 0 ? '(' : ' OR';
                                                                                $logic = $logic == ' OR' && $like == 'NOT LIKE' ? ' AND' : $logic;
                                                                                if (strpos($textBuscaTmp, '"') !== false || strpos($textBuscaTmp, '\'') !== false) {
                                                                                    $textBusca_ = str_replace('\'', '', $textBuscaTmp);
                                                                                } else {
                                                                                    $textBusca_ = str_replace(' ', '%', $textBuscaTmp);
                                                                                }
                                                                                if ($textBusca_[0] == '=') {
                                                                                    $textBusca_ = trim($textBusca_, '=');
                                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} {$like} '{$textBusca_}')";
                                                                                    $like = $like == 'LIKE' ? '=' : '!=';
                                                                                    $textBusca_ = str_replace('%', ' ', $textBusca_);
                                                                                    if ($grid_valor['usarHaving']) {
                                                                                        $having .= " {$logic} ({$campo} {$like} '{$textBusca_}')";
                                                                                    } else {
                                                                                        $extra .= " {$logic} ({$campo} {$like} '{$textBusca_}')";
                                                                                    }
                                                                                } else {
                                                                                    if (strpos($textBuscaTmp, '%') === false) {
                                                                                        if ($grid_valor['usarHaving']) {
                                                                                            $having .= " {$logic} ({$campo} {$like} '%{$textBusca_}%')";
                                                                                        } else {
                                                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} {$like} '%{$textBusca_}%')";
                                                                                            $extra .= " {$logic} ({$campo} {$like} '%{$textBusca_}%')";
                                                                                        }
                                                                                    } else {
                                                                                        if ($grid_valor['usarHaving']) {
                                                                                            $having .= " {$logic} ({$campo} {$like} '{$textBusca_}')";
                                                                                        } else {
                                                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} {$like} '{$textBusca_}')";
                                                                                            $extra .= " {$logic} ({$campo} {$like} '{$textBusca_}')";
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                            if ($grid_valor['usarHaving']) {
                                                                                $having .= ')';
                                                                            } else {
                                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                                $extra .= ')';
                                                                            }
                                                                        }
                                                                        if ($grid_valor['usarHaving']) {
                                                                            $having .= ')';
                                                                        } else {
                                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                            $extra .= ')';
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                foreach ($campoMais as $key => $campo) {
                                                                    $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                    $logic = $key == 0 ? 'AND (' : 'OR';
                                                                    $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} is NULL OR {$campo} = '' OR {$campo} = '0')";
                                                                    $extra .= " {$logic} ({$campo} is NULL OR {$campo} = '' OR {$campo} = '0')";
                                                                }
                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                                $extra .= ')';
                                                            }
                                                        } else {
                                                            foreach ($campoMais as $key => $campo) {
                                                                $campo = str_ireplace('[[virgula]]', ',', $campo);
                                                                $logic = $key == 0 ? 'AND (' : 'OR';
                                                                $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= " {$logic} ({$campo} is not NULL AND {$campo} != '' AND {$campo} != '0')";
                                                                $extra .= " {$logic} ({$campo} is not NULL AND {$campo} != '' AND {$campo} != '0')";
                                                            }
                                                            $gridFiltroInterno['campos'][$grid_valor['colunaValor']]['extra'] .= ')';
                                                            $extra .= ')';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $gridFiltroInterno['extra'] = $extra;
                    $gridFiltroInterno['having'] = $having;
                    if (!(!isset($r_extraFixo))) {
                        $gridFiltroInterno['extra'] = base64_decode($r_extraFixo);
                        $extra = base64_decode($r_extraFixo);
                    }
                    if ($multiGridFiltroId !== false) {
                        $gridFiltroInternoOriginal[$multiGridFiltroId] = $gridFiltroInterno;
                        $gridFiltroInterno = $gridFiltroInternoOriginal;
                    }
                    $h = fopen($file, 'w');
                    fwrite($h, serialize($gridFiltroInterno));
                    fclose($h);
                    if ($extraLinha) {
                        if (strpos($extraOriginal, 'where') !== false) {
                            $extraOriginal = explode('where', $extraOriginal);
                            $extraOriginal = $extraOriginal[0];
                        }
                        $extraOriginal .= $extraLinha;
                    } else {
                        $extraOriginal = gInsertExtraWhere($extraOriginal, $extra);
                    }
                    $agrupar = $this->getGridFiltroCampoBusca('Agrupar', $gridFiltroInterno);
                    if (!$agrupar) {
                        foreach ($gridFiltroParam as $grid_valor) {
                            if (isset($grid_valor['colunaTipo'])) {
                                if ($grid_valor['colunaTipo'] == 'agrupar') {
                                    foreach ($grid_valor['colunaSelect'] as $selectValue) {
                                        if (isset($selectValue['campoSelecionado']) && $selectValue['campoSelecionado']) {
                                            $agrupar = "{$selectValue['campoAgrupar']}|{$selectValue['campoMostrar']}|{$selectValue['campoOrdem']}";
                                            $gridFiltroInterno['campos']['Agrupar']['busca'] = $agrupar;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($agrupar) {
                        $agrupar_ = explode('|', $agrupar);
                        foreach ($gridFiltroParam as $grid_valor) {
                            if (isset($grid_valor['colunaTipo'])) {
                                if ($grid_valor['colunaTipo'] == 'agrupar') {
                                    foreach ($grid_valor['colunaSelect'] as $selectValue) {
                                        if (isset($selectValue['campoAgrupar']) && $selectValue['campoAgrupar'] == $agrupar_[0]) {
                                            $this->colunasOcultar = isset($selectValue['colunasOcultar']) ? $selectValue['colunasOcultar'] : array();
                                        }
                                    }
                                }
                            }
                        }
                        if (!empty($gridFiltroInterno['ordem'])) {
                            if (stripos($gridFiltroInterno['ordem'], ' as ')) {
                                $ordemTmp = preg_split('/as/i', $gridFiltroInterno['ordem']);
                                $ordemTmp = trim($ordemTmp[1]);
                            } else {
                                $ordemTmp = $gridFiltroInterno['ordem'];
                            }
                            $ordemTmp = $this->agruparOrderBy($agrupar_, $ordemTmp);
                        } else {
                            if (strpos($extraOriginal, 'ORDER BY') !== false) {
                                $extraOriginal_ = explode('ORDER BY', $extraOriginal);
                                $ordemTmp = $this->agruparOrderBy($agrupar_, $extraOriginal_[1]);
                            } else {
                                $ordemTmp = $this->agruparOrderBy($agrupar_, '');
                            }
                        }
                    } else {
                        if (stripos($gridFiltroInterno['ordem'], ' as ')) {
                            $ordemTmp = preg_split('/as/i', $gridFiltroInterno['ordem']);
                            $ordemTmp = trim($ordemTmp[1]);
                        } else {
                            $ordemTmp = $gridFiltroInterno['ordem'];
                        }
                    }
                    if (!$removeOrderBy && !empty($ordemTmp)) {
                        if (strpos($extraOriginal, 'ORDER BY') !== false) {
                            $extraOriginal_ = explode('ORDER BY', $extraOriginal);
                            array_pop($extraOriginal_);
                            $extraOriginal_ = implode('ORDER BY', $extraOriginal_);
                            $extraOriginal = $extraOriginal_ . " ORDER BY {$ordemTmp}";
                        } else {
                            $extraOriginal .= " ORDER BY {$ordemTmp}";
                        }
                    }
                    $this->gridFiltro = $gridFiltroInterno;
                    if (isset($filtroObrigatorio) && $filtroObrigatorio && !$r_generateHTML && !$r_generateCSV && !$r_ocultarFiltro) {
                        global $r_pesquisaGridId;
                        $filtroPrint = $this->gridFiltroPrint($gridFiltro, (!empty($r_pesquisaGridId) ? $r_pesquisaGridId : $md5) ? $md5 : false, $posicionarPrimeiroCampoPreenchido);
                        if (!(!isset($r_gridFiltrado) || !$r_gridTemFiltro)) {
                        } else {
                            echo $filtroPrint;
                            echo<<<EOT
    <div class="gridOptTop" style="margin-top: -25px; position: absolute; right: 17px;">
        <div class="btn-group">
            <button class="btn btn-mini" onclick="gridRefresh(this); return false;" title="Atualizar"><span class="ui-icon ui-icon-refresh" style="width: 14px; margin-left: -2px;"></span></button>
            <button data-toggle="dropdown" class="btn btn-mini dropdown-toggle" style="height: 20px; top: -12px;margin-right:5px"><span class="caret"></span></button>
            <ul class="dropdown-menu pull-left" style="margin-left: -110px; margin-top: -5px;">
                <li><a href="#" onclick="acoesGrid(this, false, false, true, false); return false;" class="acao-grid-imprimir">Imprimir</a></li>
                <li><a href="#" onclick="acoesGrid(this, false, false, false, true); return false;" class="acao-grid-email">Enviar via e-mail</a></li>
                <li><a href="#" onclick="acoesGrid(this, false, false, false, false, true, true); return false;" class="acao-grid-pdf">Gerar PDF</a></li>
                <li><a href="#" onclick="acoesGrid(this, false, true, false, false); return false;" class="acao-grid-csv">Gerar CSV (Excel)</a></li>
            </ul>
        </div>
    </div>
EOT;
                            if (strlen($filtroObrigatorio) > 2) {
                                $texto = $filtroObrigatorio;
                            } else {
                                $texto = "Por favor, utilize o filtro.";
                                echo<<<EOT
\t\t\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\t\t\tif (device == 'mobile') {
\t\t\t\t\t\t\t\t\$('#{$g['divId']} .gridFiltro:visible').css('overflow-y', 'auto');
\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\$('#{$g['divId']} .gridFiltro:visible .filtroOculto').css('display', 'inline');
\t\t\t\t\t\t\t\$('#{$g['divId']} .gridFiltro:visible .ocultarExibirFiltros').addClass('botaoOculto');
\t\t\t\t\t\t\t\$('#{$g['divId']} .gridOptTop').css('display', 'none');
\t\t\t\t\t\t} </script></div>
EOT;
                            }
                            echo '<div gridsql="" style="background-color: transparent;"><table class="grid" cellspacing="1" style="margin-top: 2px;" bgcolor="#EDE7DE"></table><div class="gridMsgFiltre">' . $texto . '</div></div>';
                            echo<<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\tcorrigeGrid('{$g['divId']}');
} </script></div>
EOT;
                            die;
                        }
                    }
                    if ($having != '(1=1)') {
                        $extraOriginal = gInsertExtraHaving($extraOriginal, $having);
                    }
                    if ($r_opcaoDesagrupar) {
                        $extraOriginal = preg_replace('/group by +[^ \\n]+/im', "GROUP BY {$from}_Id", $extraOriginal);
                    }
                    $extraOriginal = preg_replace('/->([a-z0-9_]+)/im', "->>'\$.\$1'", $extraOriginal);
                    log("(tempo) ({$g['r_']}) gridFiltroExtra: " . gLogDiffTime());
                    return $extraOriginal;
                } else {
                    $return = '';
                    foreach ($gridFiltroParam as $key => $value) {
                        $return .= "colunaValor [{$key}]\n";
                        foreach ($value as $valueKey => $valueValue) {
                            if (!is_array($valueValue)) {
                                $return .= "{$valueKey} [{$valueValue}]\n";
                            }
                        }
                        $return .= "---\n";
                    }
                    echo '<pegarFiltros>' . trim($return) . '</pegarFiltros>';
                    die;
                }
            } else {
                $extraOriginal = str_replace("\t", '', $extraOriginal);
                echo '<pegarExtra>' . trim($extraOriginal) . '</pegarExtra>';
                die;
            }
        } else {
            return;
        }
    }

    function agruparOrderBy($agrupar_, $ordem)
    {
        $ordemTmp = '';
        $campoAgrupar = explode(',', str_replace(' ', '', $agrupar_[0]));
        $campoMostrar = explode(',', str_replace(' ', '', $agrupar_[1]));
        $campoOrdem = explode(',', str_replace(' ', '', $agrupar_[2]));
        foreach ($campoAgrupar as $key => $value) {
            $ordemTmp .= "{$campoMostrar[$key]} {$campoOrdem[$key]}, ";
        }
        if (!empty($ordem)) {
            $ordemTmp .= "{$ordem}";
        } else {
            $ordemTmp = trim($ordemTmp, ', ');
        }
        return $ordemTmp;
    }

    function gridFiltroPrint($grid, $md5 = false, $posicionarPrimeiroCampoPreenchido = false)
    {
        global $g, $r_generateAgrupamento, $r_modoFiltro, $r_generateHTML, $r_gridTemFiltro, $r_ocultarFiltro, $filtroObrigatorio, $globalMostrarTodosFiltros, $r_mostrarSomenteGrid, $r_callbackCarregar;
        if (!($r_mostrarSomenteGrid || $r_callbackCarregar)) {
            $g['ui']->gridFiltroAtual = $grid;
            if (!(isset($r_generateAgrupamento) || isset($r_ocultarFiltro))) {
                $filtroUID = uniqid();
                $gridID = $md5 ? $md5 : md5($g['r_']);
                $file = $g['pathDadosUsuario'] . 'gridFiltro/' . $gridID;
                if (!file_exists($file)) {
                    return 'Arquivo para guardar o filtro não encontrado.<br />Verifique se foi executado: $g[\'ui\']->gridFiltroExtra($grid)';
                } else {
                    $gridFiltro = unserialize(file_get_contents($file));
                    if (!(isset($gridFiltro['campos']))) {
                        $gridFiltro = $gridFiltro[0];
                    }
                    $textFiltro = '';
                    if (!(!is_array($gridFiltro['busca']))) {
                        foreach ($gridFiltro['busca'] as $text) {
                            $textFiltro .= empty($textFiltro) ? $text : ' ' . $text;
                        }
                    }
                    $grid_ = array();
                    $exclude = true;
                    if (!(is_numeric(key($grid)))) {
                        $exclude = false;
                        foreach ($grid as $key => $value) {
                            if ($value['tipo'] == 'agrupar') {
                                $value['tipo'] = 'select';
                                $value['select'] = $value['campos'];
                                if ($value['select']) {
                                    foreach ($value['select'] as $key2 => $value2) {
                                        $value['select'][$key2]['value'] = "{$value2['campoAgrupar']}|{$value2['campoMostrar']}|{$value2['campoOrdem']}";
                                    }
                                    array_unshift($value['select'], array('title' => '-', 'value' => ''));
                                } else {
                                    $value['select'] = array('title' => '-', 'value' => '');
                                }
                            }
                            $grid_[] = array('colunaValor' => $key, 'colunaTitulo' => $value['titulo'], 'colunaTipo' => $value['tipo'], 'usarHaving' => isset($value['having']) ? $value['having'] : false, 'colunaSelect' => isset($value['select']) ? $value['select'] : ($value['tipo'] == 'agrupar' ? $value['campos'] : false), 'colunaSelectNaoGerarExtra' => $value['tipo'] == 'agrupar' ? true : (isset($value['naoGerarExtra']) ? $value['naoGerarExtra'] : false), 'colunaExibir' => isset($value['exibir']) && !$value['exibir'] ? false : true, 'colunaColunas' => isset($value['colunas']) ? $value['colunas'] : 1, 'grid' => isset($value['grid']) ? $value['grid'] : false);
                        }
                        $grid = $grid_;
                    }
                    $tabindex1 = count($grid) * 2 + 1;
                    $tabindex2 = count($grid) * 2 + 1;
                    $tabindex3 = count($grid) * 2 + 1;
                    $tabindex4 = count($grid) * 2 + 1;
                    $botoes = <<<EOT
\t\t\t<span class="gridFiltroBotoes gridFiltroBotoesAdv" style="margin-top: 0px;">
\t\t\t\t<button type="submit" value="" class="ui-state-default" title="Filtrar" style="width: 23px; height: 22px; float: left;" tabindex="{$tabindex1}">
\t\t\t\t\t<span class="ui-icon ui-icon-search" title="Filtrar" style="margin-left: -10px; margin-top: -3px;"></span>
\t\t\t\t</button>
\t\t\t\t<button type="submit" value="" class="ui-state-default limparFiltro" title="Limpar Filtro (F9)" style="width: 23px; height: 22px; float: left; margin-left: 4px;" tabindex="{$tabindex2}">
\t\t\t\t\t<span class="ui-icon ui-icon-closethick" title="Limpar Filtro (F9)" style="margin-left: -10px; margin-top: -3px;"></span>
\t\t\t\t</button>
\t\t\t\t<button type="submit" value="" class="amarela-mais ui-icon ui-icon-plusthick ocultarExibirFiltros" title="Exibir filtros" style="background-position: -30px -126px; float: left; background-color: rgba(255,255,0,0.4); width: 23px; height: 22px; margin-left: 4px;" tabindex="{$tabindex3}">
\t\t\t\t<button type="submit" value="" class="ui-state-default historicoAnterior" title="Histórico Anterior" style="width: 23px; height: 22px; float: left; margin-left: 4px;" tabindex="{$tabindex1}">
\t\t\t\t\t<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Histórico Anterior" style="margin-left: -10px; margin-top: -3px;"></span>
\t\t\t\t</button>
\t\t\t\t<!--<button type="submit" value="" class="ui-state-default historicoLista" title="Histórico" style="width: 23px; height: 22px; float: left; margin-left: 4px; padding: 0;" tabindex="{$tabindex1}">
\t\t\t\t\t<i class="fa fa-bars" style="margin: 2px -4px;" title="Histórico"></i>
\t\t\t\t</button>-->
\t\t\t\t<button type="submit" value="" class="ui-state-default historicoProximo" title="Histórico Próximo" style="width: 23px; height: 22px; float: left; margin-left: 4px;" tabindex="{$tabindex1}" disabled>
\t\t\t\t\t<span class="ui-icon ui-icon-arrowreturnthick-1-e" title="Histórico Próximo" style="margin-left: -10px; margin-top: -3px;"></span>
\t\t\t\t</button>
\t\t\t\t<button type="submit" value="" class="ui-state-default"  style="width: 23px; height: 22px; float: left; margin-left: 4px;" title="Ajuda do Filtro" tabindex="{$tabindex4}">
\t\t\t\t\t<span class="ui-icon ui-icon-help" title="Ajuda do Filtro" style="margin-left: -11px; margin-top: -3px;"></span>
\t\t\t\t</button>
\t\t\t</span>
EOT;
                    if (!(empty($r_generateHTML))) {
                        $botoes = '';
                    }
                    $r_gridTemFiltro = false;
                    foreach ($grid as $grid_valor) {
                        $value = $this->getGridFiltroCampoBusca($grid_valor['colunaValor'], $gridFiltro);
                        if (!is_array($value) && !empty($value)) {
                            $r_gridTemFiltro = true;
                        } else {
                            if (!empty($value[0]) || !empty($value[1])) {
                                $r_gridTemFiltro = true;
                            }
                        }
                        if ($r_modoFiltro) {
                            $r_gridTemFiltro = true;
                        }
                    }
                    $arquivoConfigGridIde = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-config';
                    $arquivoConfigGridApps = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-config';
                    $arquivoConfigGridAppsEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-config';
                    $arquivoConfigGridGlobal = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($g['r_'])) . '-config';
                    $arquivoConfigGridPerfil = "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-config';
                    $arquivoConfigGridUsuario = "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-config';
                    $arquivoConfigGridModelo = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-config';
                    $arquivoConfigGridModeloPerfil = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($g['r_'])) . '-config';
                    if ($g['modoDesenvolvedor']) {
                        $arquivoConfigGrid = $arquivoConfigGridIde;
                    } else {
                        if ($g['modoDesenvolvedorApp']) {
                            $arquivoConfigGrid = $arquivoConfigGridIde;
                            $arquivoConfigGrid = file_exists($arquivoConfigGridApps) ? $arquivoConfigGridApps : $arquivoConfigGrid;
                            $arquivoConfigGrid = file_exists($arquivoConfigGridAppsEditor) ? $arquivoConfigGridAppsEditor : $arquivoConfigGrid;
                        } else {
                            $arquivoConfigGrid = $arquivoConfigGridIde;
                            $arquivoConfigGrid = file_exists($arquivoConfigGridApps) ? $arquivoConfigGridApps : $arquivoConfigGrid;
                            $arquivoConfigGrid = file_exists($arquivoConfigGridGlobal) ? $arquivoConfigGridGlobal : $arquivoConfigGrid;
                            $arquivoConfigGrid = file_exists($arquivoConfigGridPerfil) ? $arquivoConfigGridPerfil : $arquivoConfigGrid;
                            $arquivoConfigGrid = file_exists($arquivoConfigGridUsuario) ? $arquivoConfigGridUsuario : $arquivoConfigGrid;
                            $arquivoConfigGrid = file_exists($arquivoConfigGridModelo) ? $arquivoConfigGridModelo : $arquivoConfigGrid;
                            $arquivoConfigGrid = file_exists($arquivoConfigGridModeloPerfil) ? $arquivoConfigGridModeloPerfil : $arquivoConfigGrid;
                        }
                    }
                    if (file_exists($arquivoConfigGrid)) {
                        $arquivoConfigGrid = file_get_contents($arquivoConfigGrid);
                        $arquivoConfigGrid = unserialize($arquivoConfigGrid);
                    } else {
                        $arquivoConfigGrid = array();
                    }
                    if (!(!isset($arquivoConfigGrid['filtroObrigatorio']))) {
                        $filtroObrigatorio = $arquivoConfigGrid['filtroObrigatorio'] == 'true' ? true : false;
                    }
                    $historicoFiltros = isset($arquivoConfigGrid['historicoFiltros']) && $arquivoConfigGrid['historicoFiltros'] == 'true';
                    $historicoFiltrosRequest = $historicoFiltros ? 'true' : 'false';
                    if (!$historicoFiltros) {
                        $botoes = str_replace('title="Histórico Anterior" style="', 'title="Histórico Anterior" style="display: none; ', $botoes);
                        $botoes = str_replace('title="Histórico Próximo" style="', 'title="Histórico Próximo" style="display: none; ', $botoes);
                    }
                    $camposAvancado = $camposAvancadoMobile = '';
                    $count = 0;
                    $first = true;
                    $tabindex = 0;
                    foreach ($grid as $grid_valor) {
                        $campoFiltrado = !$r_gridTemFiltro && $filtroObrigatorio ? true : false;
                        $tabindex += 2;
                        $input = '';
                        if (!(isset($grid_valor['colunaExibir']) && !$grid_valor['colunaExibir'])) {
                            if (!(isset($grid_valor['colunaTipo']) && $grid_valor['colunaTipo'] == 'livre')) {
                                $tmp = explode('_', $grid_valor['colunaValor']);
                                if (!($exclude && isset($tmp[2]) && $tmp[2] == 'Id')) {
                                    if (!($exclude && (strpos($grid_valor['colunaValor'], ' as ') !== false || strpos($grid_valor['colunaValor'], '(') !== false || strpos($grid_valor['colunaValor'], '.') !== false))) {
                                        if ($count >= (!empty($r_modoFiltro) ? 1 : 3)) {
                                            $hide = !empty($r_modoFiltro) ? ' display: none;' : '';
                                            $camposAvancado .= <<<EOT
\t\t\t\t\t<div style="float: left; vertical-align: bottom;{$hide}">{$botoes}</div>
EOT;
                                        }
                                        $value = $this->getGridFiltroCampoBusca($grid_valor['colunaValor'], $gridFiltro);
                                        if (!is_array($value) && strlen($value) > 0) {
                                            $campoFiltrado = true;
                                        } else {
                                            if (!empty($value[0]) || !empty($value[1])) {
                                                $campoFiltrado = true;
                                            }
                                        }
                                        if ($this->getGridFiltroCampoCount($grid_valor['colunaValor'], $gridFiltro) > 0 || $r_modoFiltro) {
                                            $campoFiltrado = true;
                                        }
                                        $colspan = ($grid_valor['colunaColunas'] - 1) * 2 + 1;
                                        if (isset($grid_valor['colunaTipo']) && ($grid_valor['colunaTipo'] == 'data' || $grid_valor['colunaTipo'] == 'dataHora')) {
                                            $value[0] = isset($value[0]) ? $value[0] : '';
                                            $value[1] = isset($value[1]) ? $value[1] : '';
                                            $isDateTimeField = $grid_valor['colunaTipo'] == 'dataHora';
                                            $class1 = $isDateTimeField ? 'campo_input_datahora' : 'campo_input_data';
                                            $class2 = $isDateTimeField ? 'campo_input_datahora' : 'campo_input_data';
                                            if (!$isDateTimeField && stripos($value[0], '*vazio*') !== false) {
                                                $class1 .= ' campo_input_data_vazio';
                                            }
                                            if (!$isDateTimeField && stripos($value[1], '*vazio*') !== false) {
                                                $class2 .= ' campo_input_data_vazio';
                                            }
                                            $class1 .= strlen($value[0]) > 0 ? " amarela" : "";
                                            $class2 .= strlen($value[1]) > 0 ? " amarela" : "";
                                            $size = $isDateTimeField ? '19' : (empty($r_generateHTML) ? '6' : '7');
                                            $tabindex1 = $tabindex + 1;
                                            if ($_SESSION['device'] == 'desktop') {
                                                $styleInput1 = $styleInput2 = $isDateTimeField ? 'width: 135px;' : 'width: 64px;';
                                            } else {
                                                $styleInput1 = $styleInput2 = $isDateTimeField ? 'width: 125px;' : 'width: 60px;';
                                            }
                                            $onkeypressData = $isDateTimeField ? '' : " onkeypress=\"return(Formatar_Data('0',this,event,'/'));\"";
                                            $maxsize = $isDateTimeField ? '19' : '10';
                                            $input = "<input tabindex=\"{$tabindex}\"{$onkeypressData} name=\"{$filtroUID}-" . base64_encode($grid_valor['colunaValor']) . "-d1\" type=\"text\" value=\"{$value[0]}\" size='{$size}' maxsize='{$maxsize}' class='{$class1}' style='font-weight: bold; font-size: 1.1em; padding: 2px 3px 0px 3px; {$styleInput1}'> até <input tabindex=\"{$tabindex1}\"{$onkeypressData} name=\"{$filtroUID}-" . base64_encode($grid_valor['colunaValor']) . "-d2\" type=\"text\" value=\"{$value[1]}\" size='{$size}' maxsize='{$maxsize}' class='{$class2}' style='font-weight: bold; font-size: 1.1em; padding: 2px 3px 0px 3px; {$styleInput2}'>";
                                            if (!(empty($r_generateHTML))) {
                                                if (!empty($value)) {
                                                    $value = empty($value) ? '-' : $value;
                                                    $input = "<span class=\"valorFiltro\">{$value[0]}</span> até <span class=\"valorFiltro\">{$value[1]}</span>";
                                                }
                                            }
                                            if (!(empty($r_generateHTML))) {
                                                $grid_valor['colunaTitulo'] .= ':';
                                            }
                                            $titulo = $grid_valor['colunaTitulo'];
                                            $displayFiltro = !$globalMostrarTodosFiltros && !$campoFiltrado ? 'filtroOculto' : '';
                                            $colunaValorBase64 = base64_encode($grid_valor['colunaValor']);
                                            $heightCampoFiltro = '20px';
                                            if ($_SESSION['device'] == 'mobile') {
                                                $heightCampoFiltro = '25px';
                                            }
                                            if ($grid_valor['grid']) {
                                                $tempId = md5(uniqid());
                                                $params = '&origemFiltro=true&origemFiltroUrl=' . urlencode($grid_valor['grid']['url']) . '&origemFiltroCampo=' . urlencode($grid_valor['grid']['campo']) . '&origemFiltroTamanho=' . urlencode($grid_valor['grid']['tamanho']) . '&origemFiltroId=' . $tempId;
                                                $titulo = "<a href=\"#\" onclick=\"\$(this).parent().next().find('.formInput:first').attr('id', '{$tempId}'); abrirJanela(event, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Busca de Filtro', '/sistema/funcoes/gridFiltro/contentFiltroGrid/', '{$params}', [900,600]); return false;\">{$titulo}</a>";
                                                $camposAvancado .= <<<EOT
\t\t\t\t<div class="campoFiltro {$displayFiltro}" style="min-width: 265px; float: left; text-align: right; height: {$heightCampoFiltro};">
\t\t\t\t\t<div style="display: table-cell; padding: 4px 4px 4px 0; text-align: right; float: left; cursor: pointer;" class="tituloFiltro">{$titulo}</div>
\t\t\t\t\t<div style="display: table-cell; width: 198px; float: left;">{$input}
\t\t\t\t\t\t<div title="Limpar" class="icone ui-icon ui-icon-close" style="float: right; margin-left: -4px;"></div>
\t\t\t\t\t\t<div title="Busca por vazio, campo sem informação registrada." class="icone ui-icon ui-icon-cancel" style="float: right;"></div>
\t\t\t\t\t</div>
\t\t\t\t</div>
EOT;
                                            } else {
                                                $camposAvancado .= <<<EOT
\t\t\t\t<div class="campoFiltro {$displayFiltro}" style="min-width: 265px; float: left; text-align: right; height: {$heightCampoFiltro};">
\t\t\t\t\t<div style="display: table-cell; padding: 4px 4px 4px 0; text-align: right; float: left; cursor: pointer;" class="tituloFiltro" onclick="wikiAjuda('{$g['divId']}', 'GRID', 'FILTRO', '{$colunaValorBase64}', '{$titulo}');">{$titulo} </div>
\t\t\t\t\t<div style="display: table-cell; width: 198px; float: left;">{$input}
\t\t\t\t\t\t<div title="Limpar" class="icone ui-icon ui-icon-close" style="float: right; margin-left: -4px;"></div>
\t\t\t\t\t\t<div title="Busca por vazio, campo sem informação registrada." class="icone ui-icon ui-icon-cancel" style="float: right;"></div>
\t\t\t\t\t</div>
\t\t\t\t</div>
EOT;
                                            }
                                            $camposAvancadoMobile .= "<tr><td style=\"padding: 4px 4px 4px 0; text-align: right;\" class=\"tituloFiltro\">{$grid_valor['colunaTitulo']}:</td><td>{$input}</td></tr>";
                                            $tituloHidden = strip_tags($grid_valor['colunaTitulo']);
                                            $camposAvancado .= "<input type=\"hidden\" name=\"{$filtroUID}-" . base64_encode($grid_valor['colunaValor']) . "-titulo\" value=\"{$tituloHidden}\" />";
                                        } else {
                                            if (isset($grid_valor['colunaTipo']) && $grid_valor['colunaTipo'] == 'select') {
                                                $class = strlen($value) > 0 ? "class='amarela'" : "";
                                                if (!(!$grid_valor['colunaSelect'])) {
                                                    foreach ($grid_valor['colunaSelect'] as $keyS => &$valueS) {
                                                        if ($valueS['value'] == $value) {
                                                            $valueS['selected'] = true;
                                                        } else {
                                                            unset($valueS['selected']);
                                                        }
                                                    }
                                                }
                                                $styleSelect = '';
                                                if ($_SESSION['device'] == 'mobile') {
                                                    $styleSelect = 'height: 23px;';
                                                }
                                                $options = gProcessaSelect($grid_valor['colunaSelect']);
                                                $input = "<select tabindex=\"{$tabindex}\" {$class} name=\"{$filtroUID}-" . base64_encode($grid_valor['colunaValor']) . "\" value=\"{$value}\" style=\"width: 165px; font-weight: bold; font-size: 1.1em; padding: 1px 3px 0px 3px; {$styleSelect}\">{$options}</select>";
                                                if (!(empty($r_generateHTML))) {
                                                    if (!empty($value)) {
                                                        $value = empty($value) ? '-' : $value;
                                                        $input = "<span class=\"valorFiltro\">{$value}</span>";
                                                    }
                                                }
                                            } else {
                                                if (!(!is_array($value))) {
                                                    $value = '';
                                                }
                                                $class = strlen($value) > 0 ? "class='amarela'" : "";
                                                if ($colspan == 1) {
                                                    $size = " style='width: 158px; font-weight: bold; font-size: 1.1em; padding: 2px 3px 0px 3px;{$style}'";
                                                } else {
                                                    if ($colspan == 3) {
                                                        $size = " style='width: 91%; font-weight: bold; font-size: 1.1em; padding: 2px 3px 0px 3px;{$style}'";
                                                    } else {
                                                        $size = " style='width: 94%; font-weight: bold; font-size: 1.1em; padding: 2px 3px 0px 3px;{$style}'";
                                                    }
                                                }
                                                $value = htmlentities($value);
                                                $input = "<input tabindex=\"{$tabindex}\" {$class} name=\"{$filtroUID}-" . base64_encode($grid_valor['colunaValor']) . "\" type=\"text\" value=\"{$value}\"{$size}>";
                                                if (!(empty($r_generateHTML))) {
                                                    if (!(strlen($value) == 0)) {
                                                        $value = htmlentities($value);
                                                        $value = empty($value) ? '-' : $value;
                                                        $input = "<span class=\"valorFiltro\">{$value}</span>";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $camposAvancado .= <<<EOT
\t\t\t<div style="float: right; vertical-align: bottom;{$hide}">{$botoes}</div>
EOT;
                    $mudarParaAvancado = '';
                    if (is_array($gridFiltro['busca']) && count($gridFiltro['busca']) > 0 && !empty($gridFiltro['busca'][0]) && $gridFiltro['busca'] != '!--advanced--!') {
                        $mudarParaAvancado = <<<EOT
\t\t\t\t\$('#{$g['divId']} .gridFiltro .gridFiltroText').trigger('click');
EOT;
                    }
                    $ajudaFiltro = <<<EOT
<div style='margin: 0 10px 10px 10px;'>
<table>
\t<tr>
\t\t<td>
<span style='font-size: 1.4em; font-weight: bold;'>Textos</span><br />
<span style='font-size: 1.2em; font-weight: bold; display: block; margin-top: 6px;'>Busca Normal</span>
Esta busca leva em conta a ordem em que as palavras aparecem no texto.<br />
Exemplos:<br />
&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family: courier;">BUSCA1</span><br />
&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family: courier;">BUSCA1 BUSCA2</span><br />
<span style='font-size: 1.2em; font-weight: bold; display: block; margin-top: 6px;'>Busca Exata</span>
Leva em conta a ordem e a posição em que as palavras aparecem no texto.<br />
Exemplos:<br />
&nbsp;&nbsp;&nbsp;&nbsp;"<span style="font-family: courier;">BUSCA1 BUSCA2"</span><br />
<span style='font-size: 1.2em; font-weight: bold; display: block; margin-top: 6px;'>Várias Buscas</span>
Para fazer várias buscas juntas, ignorando posição e ordem.<br />
Utiliza o caracter: <b>;</b>.<br />
Exemplos:<br />
&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family: courier;">BUSCA1;BUSCA2</span><br />
&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family: courier;">"BUSCA1 BUSCA2";BUSCA1 BUSCA2;BUSCA3</span><br />
<span style='font-size: 1.2em; font-weight: bold; display: block; margin-top: 6px;'>Negação</span>
Mostrar todos os resultados, menos os que foram filtrados.<br />
Exemplos:<br />
&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family: courier;">!BUSCA1</span><br />
&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family: courier;">!BUSCA1;!BUSCA2</span><br />
&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family: courier;">!"BUSCA1 BUSCA2"</span><br />
&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family: courier;">!"BUSCA1 BUSCA2";!BUSCA3 BUSCA4</span><br />
\t\t</td>
\t\t<td>&nbsp;&nbsp;&nbsp;</td>
\t\t<td style="vertical-align: top;">
<span style='font-size: 1.4em; font-weight: bold;'>Números</span><br />
<span style='font-size: 1.2em; font-weight: bold; display: block; margin-top: 6px;'>!X</span>
Todos os números que sejam diferente do filtrado.<br />
<span style='font-size: 1.2em; font-weight: bold; display: block; margin-top: 6px;'>&gt;X</span>
Todos os números maiores que.<br />
<span style='font-size: 1.2em; font-weight: bold; display: block; margin-top: 6px;'>&lt;X</span>
Todos os números menorer que.<br />
<span style='font-size: 1.2em; font-weight: bold; display: block; margin-top: 6px;'>=X</span>
Campos que são valores monetários, por padrão utilizam uma váriação de (0.1).<br />
Utilize este campo para tornar a busca exata.<br />
\t\t</td>
\t</tr>
</table>
</div>
EOT;
                    $ajudaFiltro = gGeraAlertMsg('Ajuda do Filtro', $ajudaFiltro, false);
                    $class = '';
                    if (!(empty($r_generateHTML))) {
                        $class = ' gridFiltroHTML';
                    }
                    $historico = array();
                    if ($historicoFiltros) {
                        $sql = <<<EOT
        \tSELECT 
        \t\tC034_Id
        \tFROM C034
        \tWHERE
        \t\tC034_Grid_Id = '{$gridID}' AND C034_C007_Id = '{$g['C007']['C007_Id']}'
        \tORDER BY C034_Data DESC
        \tLIMIT 1;
EOT;
                        $sqlReturn = mysql_query($sql);
                        $row = mysql_fetch_assoc($sqlReturn);
                        $sql = <<<EOT
        \tSELECT 
        \t\tC034_Id, C034_Filtro_Data, IF(C034_Id = '{$row['C034_Id']}', 9999, C034_Count) as C034_Count 
        \tFROM C034
        \tWHERE
        \t\tC034_Grid_Id = '{$gridID}' AND C034_C007_Id = '{$g['C007']['C007_Id']}'
        \tORDER BY C034_Count DESC, C034_Data DESC
        \tLIMIT 50;
EOT;
                        $sqlReturn = mysql_query($sql);
                        while ($row = mysql_fetch_assoc($sqlReturn)) {
                            $filtro = explode('||||', $row['C034_Filtro_Data']);
                            foreach ($filtro as $key => $value) {
                                $filtro[$key] = explode('|||', $value);
                                $filtro[$key][2] = base64_encode($filtro[$key][0]);
                            }
                            $row_ = array('id' => $row['C034_Id'], 'filtro' => $filtro);
                            $historico[] = $row_;
                        }
                    }
                    $historico = str_replace('"', '\\"', str_replace('\\', '\\\\', json_encode($historico)));
                    $historicoPos = 0;
                    foreach ($gridFiltro['campos'] as $key => $value) {
                        if (!(empty($value['busca']))) {
                            $valor = $value['busca'];
                            if (!(!is_array($valor))) {
                                if (!empty(implode('', $valor))) {
                                }
                            }
                            $historicoPos = 1;
                        }
                    }
                    $filtradoTxt = '';
                    $gridFiltro = $this->gridFiltro;
                    foreach ($gridFiltro['campos'] as $filtroKey => $filtro) {
                        if (is_array($filtro['busca'])) {
                            if (!empty($filtro['busca'][0]) || !empty($filtro['busca'][1])) {
                                $filtradoTxt .= $this->gridFiltroAtual[$filtroKey]['titulo'] . ': ' . $filtro['busca'][0] . ' - ' . $filtro['busca'][1] . '  &nbsp;';
                            }
                        } else {
                            if (!(empty($filtro['busca']))) {
                                if ($this->gridFiltroAtual[$filtroKey]['tipo'] == 'select') {
                                    foreach ($this->gridFiltroAtual[$filtroKey]['select'] as $keyOpcao => $opcao) {
                                        if ($opcao['value'] == $filtro['busca']) {
                                            $filtradoTxt .= $this->gridFiltroAtual[$filtroKey]['titulo'] . ': ' . $opcao['title'] . '  &nbsp;';
                                        }
                                    }
                                } else {
                                    if ($this->gridFiltroAtual[$filtroKey]['tipo'] == 'agrupar') {
                                    } else {
                                        $filtradoTxt .= $this->gridFiltroAtual[$filtroKey]['titulo'] . ': ' . $filtro['busca'] . '  &nbsp;';
                                    }
                                }
                            }
                        }
                    }
                    if ($filtradoTxt) {
                        $this->textoFiltroImprimir = 'Filtros  &nbsp;-&nbsp; <span style="font-weight: normal; font-size: 12px; margin-top: 12px;">' . $filtradoTxt . '</style>';
                    } else {
                        $this->textoFiltroImprimir = '';
                    }
                    if ($posicionarPrimeiroCampoPreenchido) {
                        $posicionarPrimeiroCampoPreenchido = 'sim';
                    }
                    if (false && $g['mobileDetect']->isMobile()) {
                        return<<<EOT
\t\t\t\t<div class="filtroMobile" style="display: none; height: 0px;">
\t\t\t\t\t<!--<div class="gridFiltroText">Filtro</div>-->
\t\t\t\t\t<form id="{$filtroUID}" class="filtroMobileForm" style="margin-top: 2px;">
\t\t\t\t\t\t<div style="clear: both;"></div>
\t\t\t\t\t\t<div class="gridFiltroAvancado">
\t\t\t\t\t\t\t<table cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-spacing: 0;">
\t\t\t\t\t\t\t\t{$camposAvancadoMobile}
\t\t\t\t\t\t\t</table>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<span class="gridFiltroBasico" style="display:none;">
\t\t\t\t\t\t\t<input type="text" value="{$textFiltro}" size="40" class="gridFiltroInput">&nbsp;
\t\t\t\t\t\t</span>
\t\t\t\t\t\t<div style="clear: both;"></div>
\t\t\t\t\t</form>
\t\t\t\t</div>
\t\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t\t\tif (\$('.filtroMobileFiltrar').size() == 0) {
\t\t\t\t\t\tvar buttonsBottomTop = \$('#{$g['divId']} .filtroMobile').parent().find('.buttonsBottomTop:first');
\t\t\t\t\t\tif (buttonsBottomTop.size() > 0) {
\t\t\t\t\t\t\t\$('<button type="button" class="filtroMobileFiltrar">Filtrar</button>').prependTo(buttonsBottomTop);
\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\$('<div class="buttonsBottomTop"><button type="button" class="filtroMobileFiltrar">Filtrar</button></div>').prependTo(\$('#{$g['divId']} .filtroMobile').parent());
\t\t\t\t\t\t}
\t\t\t\t\t\t\$('.filtroMobileFiltrar').unbind('click').bind('click', function() {
\t\t\t\t\t\t\tvar buttons = {
\t\t\t                    "Fechar": function() { \$(this).dialog("destroy").remove(); },
\t\t\t                    "Limpar": function() { },
\t\t\t                    "Filtrar": function() {
\t\t\t                        \$('#{$g['divId']}').showLoading();
\t\t\t                        \$.getJSON('/cad/cad002/grid_func-ajax/cad002AjaxGerarXmlProdutos/?extraSql={$extra_}&ajax=true&callback=?', function(request) {
\t\t\t                            \$('#{$g['divId']}').hideLoading();
\t\t\t                            if (request.code) {
\t\t\t                                dialogConfirm('Ok!', 'XML gerado com sucesso.');
\t\t\t                                divRefresh('{$g['divId']}');
\t\t\t                            } else {
\t\t\t                                dialogConfirm('Erro!', request.data);
\t\t\t                            }
\t\t\t                        });
\t\t\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t                        \$(this).dialog("destroy").remove();
\t\t\t                    }
\t\t\t                }
\t\t\t\t\t\t\tdialogConfirm('Filtro', '', buttons, false, false, \$('.filtroMobile'));
\t\t\t\t\t\t\tsetTimeout(function(){
\t\t\t\t\t\t\t\t//reloadUI('{$g['divId']}', true, 'filtroMobileForm');
\t\t\t\t\t\t\t\tvar \$dataInput = \$(".hasDatepicker");
\t\t\t\t\t\t\t\t\$dataInput.datepicker({ dateFormat: 'dd/mm/yy' });
\t\t\t\t\t\t\t\t\$dataInput.datepicker( "option", "dayNamesMin", ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'] );
\t\t\t\t\t\t\t\t\$dataInput.datepicker( "option", "monthNames", ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'] );
\t\t\t\t\t\t\t\tif (\$.timepicker && \$.timepicker.regional && \$.timepicker.regional['pt-BR']) {
\t\t\t\t\t\t\t\t\t\$.timepicker.setDefaults(\$.timepicker.regional['pt-BR']);
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t\t\$(".campo_input_datahora").datetimepicker({
\t\t\t\t\t\t\t\t\tdateFormat: 'dd/mm/yy',
\t\t\t\t\t\t\t\t\ttimeFormat: 'HH:mm:ss',
\t\t\t\t\t\t\t\t\tcontrolType: 'select',
\t\t\t\t\t\t\t\t\tcloseText: 'Enviar',
\t\t\t\t\t\t\t\t\tdayNamesMin: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
\t\t\t\t\t\t\t\t\tmonthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
\t\t\t\t\t\t\t\t\ttimeInput: true
\t\t\t\t\t\t\t\t});
\t\t\t\t\t\t\t}, 100);
\t\t\t\t\t\t});
\t\t\t\t\t}
\t\t\t\t} </script></div>
EOT;
                    } else {
                        return<<<EOT
\t\t\t<div class="gridFiltro ui-corner-all{$class}">
\t\t\t\t<!--<div class="gridFiltroText">Filtro</div>-->
\t\t\t\t<form id="{$filtroUID}" style="margin-top: 2px;">
\t\t\t\t\t<div style="clear: both;"></div>
\t\t\t\t\t<div class="gridFiltroAvancado">
\t\t\t\t\t\t{$camposAvancado}
\t\t\t\t\t</div>
\t\t\t\t\t<span class="gridFiltroBasico" style="display:none;">
\t\t\t\t\t\t<input type="text" value="{$textFiltro}" size="40" class="gridFiltroInput">&nbsp;
\t\t\t\t\t</span>
\t\t\t\t\t<div style="clear: both;"></div>
\t\t\t\t</form>
\t\t\t</div>
\t\t\t<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
\t\t\t    var hist{$filtroUID}count = {$historicoPos};
\t\t\t\tvar hist{$filtroUID} = JSON.parse("{$historico}");

\t\t\t\t\$(document).ready(function() {
\t\t\t\t\tvar maiorWidth = 0;
\t\t\t\t\t\$("#{$filtroUID} .tituloFiltro").each(function(i, j) {
\t\t\t\t\t\tif(\$(j).outerWidth() > maiorWidth){
\t\t\t\t\t\t\tmaiorWidth = \$(j).outerWidth();
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t\t\$("#{$filtroUID} .tituloFiltro").css('width', maiorWidth+'px');
\t\t\t\t\t\$('#{$filtroUID} .gridFiltroAvancado .filtroOculto').css('display', 'none');
\t\t\t\t\t
\t\t\t\t\t// Histórico
\t\t\t\t\tif (hist{$filtroUID}.length == 0 || hist{$filtroUID}count == hist{$filtroUID}.length) {
\t\t\t\t\t    \$('#{$filtroUID} .gridFiltroBotoes button:eq(3)').attr('disabled', 'disabled');
\t\t\t\t\t    \$('#{$filtroUID} .gridFiltroBotoes button:eq(3)').button('disable');
\t\t\t\t\t}
\t\t\t\t});

\t\t\t\t// Ajuda do Filtro
\t\t\t\t\$('#{$filtroUID} .gridFiltroBotoes button:eq(5)').unbind('click').bind('click', function(event) {
\t\t\t\t\t{$ajudaFiltro}
\t\t\t\t\t\$('.colunaAcoes').remove();
\t\t\t\t\treturn false;
\t\t\t\t});

\t\t\t\t// Botão ocultar/exibir campos do filtros\t\t\t\t
\t\t\t\t\$('#{$g['divId']} .gridFiltroBotoes button:eq(2)').unbind('click').bind('click', function(event) {
\t\t\t\t\tvar botaoOcultarExibir = \$(this);
\t\t\t\t\tif(\$(botaoOcultarExibir).hasClass('ui-icon-plusthick')){
\t\t\t\t\t\tvar filtroHeightInicial = parseFloat(\$('#{$g['divId']} .gridFiltro:visible:last').css('height'));

\t\t\t\t\t\tif (device == 'mobile') {
\t\t\t\t\t\t\t\$('#{$g['divId']} .gridFiltro:visible:last').css('overflow-y', 'auto');
\t\t\t\t\t\t}

\t\t\t\t\t\t\$('#{$g['divId']} .gridFiltroAvancado .filtroOculto').css('display', 'inline');

\t\t\t\t\t\tvar heightAjuste = parseFloat(\$('#{$g['divId']} .gridFiltro:visible:last').css('height')) - filtroHeightInicial;
\t\t\t\t\t\tvar gridMaximizarTop = parseFloat(\$('#{$g['divId']} .gridTheadMaximizar:visible').css('top'));

\t\t\t\t\t\t// Corrige altura do botão de maximizar e minimizar
\t\t\t\t\t\t\$('#{$g['divId']} .gridTheadMaximizar:visible').css('top', heightAjuste + gridMaximizarTop);

\t\t\t\t\t\t\$(botaoOcultarExibir).removeClass('ui-icon-plusthick').addClass('ui-icon-minusthick amarela-mais').css({'background-position': '-61px -126px', 'background-color': 'rgba(255,0,0,0.3)'});
\t\t\t\t\t} else {
\t\t\t\t\t\t\$(botaoOcultarExibir).removeClass('ui-icon-minusthick').addClass('ui-icon-plusthick amarela-mais').css({'background-position': '-30px -127px', 'background-color': 'rgba(255,255,0,0.4)'});

\t\t\t\t\t\tvar filtroHeightInicial = parseFloat(\$('#{$g['divId']} .gridFiltro:visible:last').css('height'));

\t\t\t\t\t\tif (device == 'mobile') {
\t\t\t\t\t\t\t\$('#{$g['divId']} .gridFiltro:visible:last').css('overflow-y', '');
\t\t\t\t\t\t}

\t\t\t\t\t\t\$('#{$filtroUID} .gridFiltroAvancado .filtroOculto').css('display', 'none');

\t\t\t\t\t\tvar heightAjuste = filtroHeightInicial - parseFloat(\$('#{$g['divId']} .gridFiltro:visible:last').css('height'));
\t\t\t\t\t\tvar gridMaximizarTop = parseFloat(\$('#{$g['divId']} .gridTheadMaximizar:visible').css('top'));
\t\t\t\t\t\t
\t\t\t\t\t\t// Corrige altura do botão de maximizar e minimizar
\t\t\t\t\t\t\$('#{$g['divId']} .gridTheadMaximizar:visible').css('top', gridMaximizarTop - heightAjuste);
\t\t\t\t\t}
\t\t\t\t\t\$('.colunaAcoes').remove();
\t\t\t\t\treturn false;
\t\t\t\t});
\t\t\t\t// Ação Filtro Normal
\t\t\t\t\$('#{$filtroUID} .gridFiltroBotoes button:eq(0)').bind('click', function(event) {
\t\t\t\t    abortarRequisicoesAjax(\$('#{$g['divId']}'));
\t\t\t\t\tif (\$('#{$filtroUID} .gridFiltroAvancado').is(':visible')) {
\t\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t\tvar dadosFiltro = 'ajax=true&filtroUID=' + encodeURIComponent('{$filtroUID}') + '&' + \$("#{$filtroUID}").serialize() + '&grid=' + encodeURIComponent('{$gridID}') + '&historicoFiltros=' + encodeURIComponent('{$historicoFiltrosRequest}') + '&callback=?';
\t\t\t\t\t\t\$.ajax({
\t\t\t\t\t\t\turl: '/sistema/funcoes/gridFiltro/filtrar/',
\t\t\t\t\t\t\ttype: 'POST',
\t\t\t\t\t\t\tdataType: 'jsonp',
\t\t\t\t\t\t\tdata: dadosFiltro,
\t\t\t\t\t\t}).done(function(request, temp1, temp2){
\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\tif (request.code) {
\t\t\t\t\t\t\t\t\$('#{$g['divId']}').data('loading', 0);
\t\t\t\t\t\t\t\tdivSetarAcaoId('{$g['divId']}', true, 'gridFiltrado');
\t\t\t\t\t\t\t\tdivRefresh('{$g['divId']}', true);
\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\tvar \$buttons2 = { "Fechar": function() { \$(this).dialog("destroy").remove(); } };
\t\t\t\t\t\t\t\tdialogConfirm('Erro', request.data, \$buttons2);
\t\t\t\t\t\t\t}
\t\t\t\t\t\t});
\t\t\t\t\t} else {
\t\t\t\t\t\t\$('#{$g['divId']}').showLoading();
\t\t\t\t\t\tvar dadosFiltro = 'ajax=true&filtro=' + encodeURIComponent(\$('#{$filtroUID} .gridFiltroBasico input:eq(0)').val()) + '&grid=' + encodeURIComponent('{$gridID}') + '&historicoFiltros=' + encodeURIComponent('{$historicoFiltrosRequest}') + '&callback=?';
\t\t\t\t\t\t\$.ajax({
\t\t\t\t\t\t\turl: '/sistema/funcoes/gridFiltro/filtrar/',
\t\t\t\t\t\t\ttype: 'POST',
\t\t\t\t\t\t\tdataType: 'jsonp',
\t\t\t\t\t\t\tdata: dadosFiltro,
\t\t\t\t\t\t}).done(function(request, temp1, temp2){
\t\t\t\t\t\t\t\$('#{$g['divId']}').hideLoading();
\t\t\t\t\t\t\tif (request.code) {
\t\t\t\t\t\t\t\t\$('#{$g['divId']}').data('loading', 0);
\t\t\t\t\t\t\t\tdivSetarAcaoId('{$g['divId']}', true, 'gridFiltrado');
\t\t\t\t\t\t\t\tdivRefresh('{$g['divId']}', true);
\t\t\t\t\t\t\t} else {
\t\t\t\t\t\t\t\tvar \$buttons2 = { "Fechar": function() { \$(this).dialog("destroy").remove(); } };
\t\t\t\t\t\t\t\tdialogConfirm('Erro', request.data, \$buttons2);
\t\t\t\t\t\t\t}
\t\t\t\t\t\t});
\t\t\t\t\t}
\t\t\t\t\t\$('.colunaAcoes').remove();
\t\t\t\t\treturn false;
\t\t\t\t});
\t\t\t\t
\t\t\t\t// Limpar
\t\t\t\t\$('#{$filtroUID} .gridFiltroBotoes button:eq(1)').unbind('click').bind('click', function(event) {
\t\t\t\t\t\$('#{$filtroUID} .ui-icon-close').trigger('click');
\t\t\t\t\t\$(this).closest('table').find('input:visible:first').focus();
\t\t\t\t\t\$('.colunaAcoes').remove();
\t\t\t\t\treturn false;
\t\t\t\t});
\t\t\t\t\$('#{$filtroUID} .ui-icon-close').unbind('click').bind('click', function(event) {
\t\t\t\t\t\$(this).parent().find('input').val('');
\t\t\t\t\t\$(this).parent().find('select').val('');
\t\t\t\t});
\t\t\t\t\$('#{$filtroUID} .ui-icon-cancel').unbind('click').bind('click', function(event) {
\t\t\t\t\tif (\$(this).parent().find('select').size() != 0) {
\t\t\t\t\t\t\$(this).parent().find('select').val('*VAZIO*');
\t\t\t\t\t} else {
\t\t\t\t\t\t\$(this).parent().find('input').val('*VAZIO*');
\t\t\t\t\t}
\t\t\t\t});
\t\t\t\t
\t\t\t\t// Mudar: Filtro Normal/Avançado
\t\t\t\t\$('#{$g['divId']} .gridFiltro .gridFiltroText').unbind('click').bind('click', function(event) {
\t\t\t\t\tif (\$('#{$filtroUID} .gridFiltroAvancado').is(':visible')) {
\t\t\t\t\t\t\$('#{$filtroUID} .gridFiltroBasico').show();
\t\t\t\t\t\t\$('#{$filtroUID} .gridFiltroAvancado').hide();
\t\t\t\t\t\t\$('#{$filtroUID} .gridFiltroBotoes').removeClass('gridFiltroBotoesAdv');
\t\t\t\t\t} else {
\t\t\t\t\t\t\$('#{$filtroUID} .gridFiltroBasico').hide();
\t\t\t\t\t\t\$('#{$filtroUID} .gridFiltroAvancado').show();
\t\t\t\t\t\t\$('#{$filtroUID} .gridFiltroBotoes').addClass('gridFiltroBotoesAdv');
\t\t\t\t\t}
\t\t\t\t\t\$('.colunaAcoes').remove();
\t\t\t\t});
\t\t\t\t
\t\t\t\t// Histórico anterior
\t\t\t\t\$('#{$filtroUID} .gridFiltroBotoes button:eq(3)').unbind('click').bind('click', function(event) {
\t\t\t\t    if (\$('#{$g['divId']} .gridFiltroBotoes button:eq(2)').hasClass('ui-icon-plusthick')) {
\t\t\t\t\t\t\$('#{$g['divId']} .gridFiltroBotoes button:eq(2)').trigger('click');
\t\t\t\t\t}
\t\t\t\t    // Habilita próximo
\t\t\t\t    \$('#{$filtroUID} .gridFiltroBotoes button:eq(4)').button('enable');
\t\t\t\t    // Verifica anterior
\t\t\t\t    hist{$filtroUID}count++;
\t\t\t\t    if (hist{$filtroUID}count == hist{$filtroUID}.length) {
\t\t\t\t        \$('#{$filtroUID} .gridFiltroBotoes button:eq(3)').button('disable');
\t\t\t\t    }
\t\t\t\t    // Popula filtros
\t\t\t\t    histPopular();
\t\t\t\t\t\$('.colunaAcoes').remove();
\t\t\t\t\treturn false;
\t\t\t\t});
\t\t\t\t
//\t\t\t\t// Histórico
//\t\t\t\t\$('#{$filtroUID} .gridFiltroBotoes button:eq(4)').unbind('click').bind('click', function(event) {
//\t\t\t\t\treturn false;
//\t\t\t\t});
\t\t\t\t
\t\t\t\t// Histórico próximo
\t\t\t\t\$('#{$filtroUID} .gridFiltroBotoes button:eq(4)').unbind('click').bind('click', function(event) {
\t\t\t\t    if (\$('#{$g['divId']} .gridFiltroBotoes button:eq(2)').hasClass('ui-icon-plusthick')) {
\t\t\t\t\t\t\$('#{$g['divId']} .gridFiltroBotoes button:eq(2)').trigger('click');
\t\t\t\t\t}
\t\t\t\t    // Habilita anterior
\t\t\t\t    \$('#{$filtroUID} .gridFiltroBotoes button:eq(3)').button('enable');
\t\t\t\t    // Verifica anterior
\t\t\t\t    hist{$filtroUID}count--;
\t\t\t\t    if (hist{$filtroUID}count == 1) {
\t\t\t\t        \$('#{$filtroUID} .gridFiltroBotoes button:eq(4)').button('disable');
\t\t\t\t    }
\t\t\t\t    // Popula filtros
\t\t\t\t    histPopular();
\t\t\t\t\t\$('.colunaAcoes').remove();
\t\t\t\t\treturn false;
\t\t\t\t});
\t\t\t\t
\t\t\t\tfunction histPopular() {
//\t\t\t\t    console.log(hist{$filtroUID}count);
// \t\t\t\t    console.log(hist{$filtroUID});
\t\t\t\t    \$('#{$filtroUID} .gridFiltroBotoes button:eq(1)').trigger('click');
\t\t\t\t    \$('#{$filtroUID} input').css('background', '');
\t\t\t\t    \$('#{$filtroUID} select').css('background', '');
\t\t\t\t    for (var item of hist{$filtroUID}[hist{$filtroUID}count-1]['filtro']) {
\t\t\t\t        // Input
\t\t\t\t        var \$item = \$('#{$filtroUID} input[name="{$filtroUID}-' + item[2] + '"]');
\t\t\t\t        if (\$item.length > 0) {
\t\t\t\t        \t\$item.val(item[1]);
\t\t\t\t        \t\$item.addClass('amarela');
\t\t\t\t        \tcontinue;
\t\t\t\t        }
\t\t\t\t        // Select
\t\t\t\t        \$item = \$('#{$filtroUID} select[name="{$filtroUID}-' + item[2] + '"]');
\t\t\t\t        if (\$item.length > 0) {
\t\t\t\t        \t\$item.val(item[1]);
\t\t\t\t        \t\$item.addClass('amarela');
\t\t\t\t        \tcontinue;
\t\t\t\t        }
\t\t\t\t        // Data
\t\t\t\t        \$item = \$('#{$filtroUID} input[name="{$filtroUID}-' + item[2] + '-d1"]');
\t\t\t\t        if (\$item.length > 0) {
\t\t\t\t        \tvar item_ = item[1].split(',');
\t\t\t\t        \tif (item_[0]) {
\t\t\t\t        \t    \$item.val(item_[0]);
\t\t\t\t        \t\t\$item.addClass('amarela');
\t\t\t\t        \t}
\t\t\t\t        \tif (item_[1]) {
\t\t\t\t        \t    \$item = \$('#{$filtroUID} input[name="{$filtroUID}-' + item[2] + '-d2"]');
\t\t\t\t        \t    \$item.val(item_[1]);
\t\t\t\t        \t\t\$item.addClass('amarela');
\t\t\t\t        \t}
\t\t\t\t        \tcontinue;
\t\t\t\t        }
\t\t\t\t    }
\t\t\t\t}
\t\t\t\t
\t\t\t\t{$mudarParaAvancado}
\t\t\t\tsetTimeout(function() {
\t\t\t\t\t\$('#{$g['divId']}').find('input.campo_input_data_vazio').each(function() {
\t\t\t\t\t\t\$(this).val('*VAZIO*');
\t\t\t\t\t});
\t\t\t\t\tif ('{$posicionarPrimeiroCampoPreenchido}' == 'sim') {
\t\t\t\t\t\t\$('#{$g['divId']}').find('input.amarela').first().select();
\t\t\t\t\t\t\$('#{$g['divId']}').find('input.amarela').first().focus();
\t\t\t\t\t}
\t\t\t\t}, 100);
\t\t\t} </script></div>
EOT;
                    }
                }
            } else {
                return false;
            }
        } else {
            return;
        }
    }

    function multiGridFiltroExtra($personalizacaoExtra, $gridFiltroParam, $extras = '', $md5 = false, $filtroSalvo = false, $removeOrderBy = false, $limparCamposAusentes = true)
    {
        global $g;
        $extras[0] = $this->gridFiltroExtra($personalizacaoExtra, $gridFiltroParam[0], $extras[0], $md5, $filtroSalvo, $removeOrderBy, $limparCamposAusentes, 0);
        foreach ($extras as $idFiltro => &$extra) {
            if (!($idFiltro == 0)) {
                $gridFiltroTemp = array();
                $gridFiltro = array_keys($gridFiltroParam[0]);
                foreach ($gridFiltroParam[$idFiltro] as $key => $campoExtra) {
                    $gridFiltroTemp[$campoExtra] = $gridFiltroParam[0][$gridFiltro[$key]];
                }
                $extra = $this->gridFiltroExtra($personalizacaoExtra, $gridFiltroTemp, $extra, $md5, $filtroSalvo, $removeOrderBy, $limparCamposAusentes, $idFiltro);
            }
        }
        return $extras;
    }

    function processaArrayDados($gridOriginal, &$dados)
    {
        global $g, $grid, $r_generateHTML, $r_generateCSV;
        $r_grid = preg_replace('/^\\//', '', $g['r_']);
        $arquivoColunasGlobal = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid));
        $arquivoColunasPerfil = "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid));
        $arquivoColunasUsuario = "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($r_grid));
        $arquivoColunasModelo = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . ($md5 ? $md5 : md5($r_grid));
        $arquivoColunasModeloPerfil = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid));
        $arquivoHTMLColunasGlobal = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
        $arquivoHTMLColunasPerfil = "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
        $arquivoHTMLColunasUsuario = "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
        $arquivoHTMLColunasModelo = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
        $arquivoHTMLColunasModeloPerfil = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
        $arquivos = array(array($arquivoColunasGlobal, $arquivoHTMLColunasGlobal), array($arquivoColunasPerfil, $arquivoHTMLColunasPerfil), array($arquivoColunasUsuario, $arquivoHTMLColunasUsuario));
        if (!(!isset($r_modoModelo))) {
            $arquivos = array(array($arquivoColunasModelo, $arquivoHTMLColunasModelo), array($arquivoColunasModeloPerfil, $arquivoHTMLColunasModeloPerfil));
        }
        if ($g['modoDesenvolvedor']) {
            $arquivoColunasGlobal = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid));
            $arquivoHTMLColunasGlobal = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
            $arquivos = array(array($arquivoColunasGlobal, $arquivoHTMLColunasGlobal));
        } else {
            if (!(!$g['modoDesenvolvedorApp'])) {
                $arquivoColunasGlobal = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid));
                $arquivoHTMLColunasGlobal = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
                $arquivoColunasGlobalEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid));
                $arquivoHTMLColunasGlobalEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
                $arquivos = array(array($arquivoColunasGlobal, $arquivoHTMLColunasGlobal), array($arquivoColunasGlobalEditor, $arquivoHTMLColunasGlobalEditor));
            }
        }
        foreach ($arquivos as $arquivo) {
            $arquivoColunas = $arquivo[0];
            $arquivoHTMLColunas = $arquivo[1];
            $arrayColunas = array();
            $conteudo = false;
            if ((isset($r_generateHTML) || isset($r_generateCSV)) && file_exists($arquivoHTMLColunas)) {
                $conteudo = file_get_contents($arquivoHTMLColunas);
            } else {
                if (!(!file_exists($arquivoColunas))) {
                    $conteudo = file_get_contents($arquivoColunas);
                }
            }
            if ($conteudo) {
                preg_match_all("/(.+)---/msU", $conteudo, $matches);
                $count = 0;
                foreach ($matches[0] as $value) {
                    $arrayColunas[$count] = array();
                    $value = str_replace(array('[[[', ']]]', '[[', ']]'), array('[{{', '}}]', '{{', '}}'), $value);
                    preg_match_all("/([a-z]+) ?\\[(.*)]/msUi", $value, $matches2);
                    foreach ($matches2[1] as $key => $value) {
                        $arrayColunas[$count][$value] = trim($matches2[2][$key]);
                    }
                    $count++;
                }
                foreach ($arrayColunas as $key => $value) {
                    $colunaValorPerso = $value['colunaValor'];
                    if (strpos($colunaValorPerso, ' as ') !== false) {
                        $colunaValorPerso = explode(' as ', $colunaValorPerso);
                        $colunaValorPerso = $colunaValorPerso[count($colunaValorPerso) - 1];
                    }
                    $new = true;
                    foreach ($grid as $gridKey => $gridValue) {
                        $colunaValorGrid = $gridValue['colunaValor'];
                        if (strpos($colunaValorGrid, ' as ') !== false) {
                            $colunaValorGrid = explode(' as ', $colunaValorGrid);
                            $colunaValorGrid = $colunaValorGrid[count($colunaValorGrid) - 1];
                        }
                        if ($colunaValorPerso == $colunaValorGrid) {
                            $new = false;
                        }
                    }
                    if ($new) {
                        $grid[] = array('colunaValor' => $value['colunaValor']);
                    }
                    $colunasMesmoKey = array();
                    foreach ($grid as $gridKey => $gridValue) {
                        $colunaValorGrid = $gridValue['colunaValor'];
                        if (strpos($colunaValorGrid, ' as ') !== false) {
                            $colunaValorGrid = explode(' as ', $colunaValorGrid);
                            $colunaValorGrid = $colunaValorGrid[count($colunaValorGrid) - 1];
                        }
                        if ($colunaValorPerso == $colunaValorGrid) {
                            if (!(!isset($colunasMesmoKey[$colunaValorGrid]))) {
                                $antigo = $colunasMesmoKey[$colunaValorGrid];
                                $grid[$antigo[0]] = $antigo[1];
                            }
                            $colunasMesmoKey[$colunaValorGrid] = array($gridKey, $grid[$gridKey]);
                            $item = &$grid[$gridKey];
                            foreach ($value as $itemKey => $itemValue) {
                                if (!(!$new && $itemKey == 'colunaValor' && strpos($value['colunaValor'], ' as ') === false)) {
                                    if (!($itemValue == 'Array')) {
                                        if (!is_array($itemValue) && stripos($itemValue, 'array') !== false) {
                                            $itemValue = eval('$item[$itemKey] = ' . $itemValue . ';');
                                        } else {
                                            $item[$itemKey] = $itemValue;
                                        }
                                    }
                                }
                            }
                            unset($item);
                        }
                    }
                }
            }
        }
        $colunasGridOriginal = array();
        foreach ($gridOriginal as $coluna => $campos) {
            $colunasGridOriginal[] = $campos['colunaValor'];
        }
        $colunasGrid = array();
        foreach ($grid as $coluna => $campos) {
            $colunasGrid[] = $campos['colunaValor'];
        }
        foreach ($dados as &$linha) {
            $i = 0;
            while ($i <= count($linha) - 1) {
                $tmp[$colunasGridOriginal[$i]] = $linha[$i];
                $i++;
            }
            $dados_[] = $tmp;
        }
        $dados = $dados_;
        foreach ($dados as &$linha) {
            $linha = array_merge(array_flip($colunasGrid), $linha);
        }
        foreach ($dados as &$linha) {
            $linha = array_values($linha);
        }
        return $dados;
    }

    public function gridFiltroLimpar()
    {
        global $g;
        $arquivo = $g['pathDadosUsuario'] . 'gridFiltro/' . md5($g['r_']);
        @unlink($arquivo);
    }

    public function getGridFiltroCampoBusca($campo, $gridFiltro = false)
    {
        $gridFiltro = $gridFiltro ? $gridFiltro : $this->gridFiltro;
        if (isset($gridFiltro['campos'][$campo])) {
            return isset($gridFiltro['campos'][$campo]['busca']) ? $gridFiltro['campos'][$campo]['busca'] : '';
        } else {
            return false;
        }
    }

    public function getGridFiltroCampoExtra($campo, $gridFiltro = false)
    {
        $gridFiltro = $gridFiltro ? $gridFiltro : $this->gridFiltro;
        if (isset($gridFiltro['campos'][$campo])) {
            return isset($gridFiltro['campos'][$campo]['extra']) ? $gridFiltro['campos'][$campo]['extra'] : '';
        } else {
            return false;
        }
    }

    public function getGridFiltroCampoCount($campo, $gridFiltro = false)
    {
        $gridFiltro = $gridFiltro ? $gridFiltro : $this->gridFiltro;
        if (isset($gridFiltro['campos'][$campo])) {
            return isset($gridFiltro['campos'][$campo]['count']) ? $gridFiltro['campos'][$campo]['count'] : '';
        } else {
            return false;
        }
    }

    function pegarColunas()
    {
        global $grid, $r_pegarColunas, $r_pegarHTMLColunas;
        $return = '';
        $camposErros = '';
        $duplicados = array();
        if ($grid) {
            foreach ($grid as $value) {
                if ($r_pegarColunas != 'ignorar' && $r_pegarHTMLColunas != 'ignorar') {
                    if (!(!(strpos($value['colunaValor'], ' as ') === false && (strpos($value['colunaValor'], '(') !== false || strpos($value['colunaValor'], '.') !== false)))) {
                        $camposErros .= "{$value['colunaValor']}<br />";
                    }
                    if (strpos($value['colunaValor'], ' as ') !== false) {
                        $value['colunaValor'] = explode(' as ', $value['colunaValor']);
                        $value['colunaValor'] = $value['colunaValor'][count($value['colunaValor']) - 1];
                    }
                }
                $colunaValor = trim($value['colunaValor']);
                if (!(!isset($duplicados[$colunaValor]))) {
                    $camposErros .= "(NOME DUPLICADO) {$colunaValor}<br />";
                }
                $duplicados[$colunaValor] = true;
                foreach ($value as $valueKey => $valueValue) {
                    if (!($valueKey == 'callbackParameter')) {
                        if ($valueKey == 'colunaValor') {
                            $valueValue = $colunaValor;
                        }
                        if (!($valueKey == 'ordem' || $valueKey == 'colunaOK')) {
                            if (!(is_array($valueValue) || is_object($valueValue))) {
                                $return .= "{$valueKey} [{$valueValue}]\n";
                            }
                        }
                    }
                }
                $return .= "---\n";
            }
        }
        if (!(empty($camposErros))) {
        }
        echo '<pegarColunas>' . trim($return) . '</pegarColunas>';
        die;
    }

}
