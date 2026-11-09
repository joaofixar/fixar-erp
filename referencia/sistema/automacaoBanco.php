<?php
/**
 * automacaoBanco.php
 *
 * Versão legível de sistema-legado/sistema/automacaoBanco.php.
 * O original foi ofuscado com desvios; aqui o fluxo foi reconstruído
 * em if/else/while/foreach, no mesmo comportamento.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */
namespace hardness;

class automacaoBanco
{
    function gerarSQLGrid($grid, $from, $extra = '', $limit = 0.0, $md5 = false, $returnCount = false, $returnSql = false, $time = 30000, $json = false)
    {
        global $g, $r_generateCSV, $r_generateHTML, $r_pegarColunas, $r_pegarHTMLColunas, $grid, $r_loading, $r_modoModelo, $r_generateEmail, $r_personalizacaoModoAntigo, $generateTimer, $r_generateAPI, $_buscarBancoAuditoria, $totais, $totaisLimit, $r_mostrarOriginal, $confUsuario, $r_callbackCarregar, $r_linhaGridId;
        $r_grid = preg_replace('/^\\//', '', $g['r_']);
        $time = '/*+max_execution_time(' . $time . ')*/';
        $gridId = $md5 ? $md5 : md5($r_grid);
        if (!($r_callbackCarregar && !$returnSql)) {
            $extraOriginal = $extra;
            $gridPHP = apc_fetch("grid-php-{$gridId}");
            if (!$gridPHP) {
                apc_store("grid-php-{$gridId}", serialize($grid), 3600);
            }
            $gridTotais = apc_fetch("grid-totais-{$gridId}");
            if (!$gridTotais) {
                if ($g['modoDesenvolvedor']) {
                } else {
                    if ($g['modoDesenvolvedorApp']) {
                        $totais_ = Personalizacoes::processarTotais($gridId, $totais, array('desenvolvedor'));
                    } else {
                        $totais_ = Personalizacoes::processarTotais($gridId, $totais, array('desenvolvedor', 'apps', 'apps-editor'));
                    }
                }
                apc_store("grid-totais-{$gridId}", serialize($totais_), 3600);
            }
            if ($g['modoDesenvolvedor']) {
                $totais = Personalizacoes::processarTotais($gridId, $totais, array('desenvolvedor'));
            } else {
                if ($g['modoDesenvolvedorApp']) {
                    $totais = Personalizacoes::processarTotais($gridId, $totais, array('desenvolvedor', 'apps', 'apps-editor'));
                } else {
                    $totais = Personalizacoes::processarTotais($gridId, $totais);
                }
            }
            $generateTimer = microtime(true);
            $arquivoConfigGridIde = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-config';
            $arquivoConfigGridApps = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-config';
            $arquivoConfigGridAppsEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-config';
            $arquivoConfigGridGlobal = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-config';
            $arquivoConfigGridPerfil = "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-config';
            $arquivoConfigGridUsuario = "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-config';
            $arquivoConfigGridModelo = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . ($md5 ? $md5 : md5($r_grid)) . '-config';
            $arquivoConfigGridModeloPerfil = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-config';
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
            if (!($g["personalizacoesCarregado-{$gridId}"])) {
                $g["personalizacoesCarregado-{$gridId}"] = true;
                $arquivoColunasIde = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid));
                $arquivoColunasApps = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid));
                $arquivoColunasAppsEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid));
                $arquivoColunasGlobal = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid));
                $arquivoColunasPerfil = "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid));
                $arquivoColunasUsuario = "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($r_grid));
                $arquivoColunasModelo = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . ($md5 ? $md5 : md5($r_grid));
                $arquivoColunasModeloPerfil = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid));
                $arquivoHTMLColunasIde = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
                $arquivoHTMLColunasApps = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
                $arquivoHTMLColunasAppsEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
                $arquivoHTMLColunasGlobal = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
                $arquivoHTMLColunasPerfil = "{$g['pathDados']}personalizacaoGrid/" . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
                $arquivoHTMLColunasUsuario = "{$g['pathDados']}personalizacaoGrid/u" . $g['C007']['C007_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
                $arquivoHTMLColunasModelo = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
                $arquivoHTMLColunasModeloPerfil = "{$g['pathDados']}personalizacaoGrid/modelo-" . (isset($r_modoModelo) ? $r_modoModelo : '') . '-' . $g['C021']['C021_Id'] . '-' . ($md5 ? $md5 : md5($r_grid)) . '-print-colunas';
                $arquivos = array(array($arquivoColunasIde, $arquivoHTMLColunasIde, 'ide'), array($arquivoColunasApps, $arquivoHTMLColunasApps, 'apps'), isset($r_pegarColunas) || isset($r_mostrarOriginal) ? null : array($arquivoColunasGlobal, $arquivoHTMLColunasGlobal, 'global'), isset($r_pegarColunas) || isset($r_mostrarOriginal) ? null : array($arquivoColunasPerfil, $arquivoHTMLColunasPerfil, 'perfil'), isset($r_pegarColunas) || isset($r_mostrarOriginal) ? null : array($arquivoColunasUsuario, $arquivoHTMLColunasUsuario, 'usuario'));
                if (!(!isset($r_modoModelo))) {
                    $arquivos = array(array($arquivoColunasModelo, $arquivoHTMLColunasModelo), array($arquivoColunasModeloPerfil, $arquivoHTMLColunasModeloPerfil));
                }
                if (!(!$g['modoDesenvolvedor'])) {
                    $arquivos = array(isset($r_pegarColunas) || isset($r_mostrarOriginal) ? null : array($arquivoColunasIde, $arquivoHTMLColunasIde, 'ide'));
                }
                if (!(!$g['modoDesenvolvedorApp'])) {
                    $arquivos = array(array($arquivoColunasIde, $arquivoHTMLColunasIde, 'ide'), array($arquivoColunasApps, $arquivoHTMLColunasApps, 'apps'), isset($r_pegarColunas) || isset($r_mostrarOriginal) ? null : array($arquivoColunasAppsEditor, $arquivoHTMLColunasAppsEditor, 'apps-editor'));
                }
                $arquivos = array_filter($arquivos);
                foreach ($arquivos as $arquivo) {
                    $arquivoColunas = $arquivo[0];
                    $arquivoHTMLColunas = $arquivo[1];
                    $nivel = $arquivo[2];
                    $arrayColunas = array();
                    $conteudo = false;
                    if ((isset($r_generateHTML) || isset($r_generateCSV) || isset($r_pegarHTMLColunas)) && file_exists($arquivoHTMLColunas)) {
                        $conteudo = file_get_contents($arquivoHTMLColunas);
                    } else {
                        if (!(!file_exists($arquivoColunas))) {
                            $conteudo = file_get_contents($arquivoColunas);
                        }
                    }
                    preg_match_all("/(.+)---/msU", $conteudo, $matches);
                    $count = 0;
                    foreach ($matches[0] as $value) {
                        $value = gSubstituirVariaveisParametros($value);
                        $arrayColunas[$count] = array();
                        $value = str_replace(array('[[[', ']]]', '[[', ']]'), array('[{{', '}}]', '{{', '}}'), $value);
                        preg_match_all("/([a-z]+) ?\\[(.*)]/msUi", $value, $matches2);
                        foreach ($matches2[1] as $key => $value) {
                            $arrayColunas[$count][$value] = trim($matches2[2][$key]);
                        }
                        $count++;
                    }
                    if (!isset($r_personalizacaoModoAntigo) || !$r_personalizacaoModoAntigo) {
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
                                                    if ($nivel == 'usuario' && $itemKey == 'colunaExibir') {
                                                        $item['ignorarOcultarColuna'] = true;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    unset($item);
                                }
                            }
                        }
                    } else {
                        $gridAntigoComKeys = array();
                        if ($grid) {
                            foreach ($grid as $key => $value) {
                                $colunaValor = $value['colunaValor'];
                                if (strpos($colunaValor, ' as ') !== false) {
                                    $colunaValor = explode(' as ', $colunaValor);
                                    $colunaValor = $colunaValor[count($colunaValor) - 1];
                                }
                                $colunaValor = trim($colunaValor);
                                $gridAntigoComKeys[$colunaValor] = $value;
                                if (!$conteudo) {
                                    $arrayColunas[] = $value;
                                    $arrayColunas[count($arrayColunas) - 1]['colunaValor'] = $colunaValor;
                                }
                            }
                        }
                        $count = 0;
                        foreach ($arrayColunas as $key => $value) {
                            $colunaValor = $value['colunaValor'];
                            if (strpos($colunaValor, ' as ') !== false) {
                                $colunaValor = explode(' as ', $colunaValor);
                                $colunaValor = $colunaValor[count($colunaValor) - 1];
                            }
                            $count++;
                            $new = false;
                            if (!(isset($gridAntigoComKeys[$value['colunaValor']]))) {
                                $gridAntigoComKeys[$value['colunaValor']] = array();
                                $new = true;
                            }
                            if (isset($gridAntigoComKeys[$value['colunaValor']])) {
                                $item = &$gridAntigoComKeys[$value['colunaValor']];
                                $item['ordem'] = $count;
                                foreach ($value as $itemKey => $itemValue) {
                                    if (!(!$new && $itemKey == 'colunaValor')) {
                                        if (!($itemValue == 'Array')) {
                                            if (!is_array($itemValue) && stripos($itemValue, 'array') !== false) {
                                                $itemValue = eval('$item[$itemKey] = ' . $itemValue . ';');
                                            } else {
                                                $item[$itemKey] = $itemValue;
                                            }
                                        }
                                    }
                                }
                                if (isset($r_generateHTML) || isset($r_generateCSV)) {
                                    $tmpVar = str_replace(' ', '_', $value['colunaValor']);
                                    $tmpVar = str_replace('.', '_', $tmpVar);
                                    if (isset($value['forcarExibirImpressao']) || isset($_REQUEST[$tmpVar]) || isset($_REQUEST['generateHTMLTodos'])) {
                                        $camposSelecionadosImpressao[] = $tmpVar;
                                        $item['colunaOK'] = true;
                                    }
                                } else {
                                    $item['colunaOK'] = true;
                                }
                            } else {
                                echo $value['colunaValor'];
                            }
                        }
                        foreach ($gridAntigoComKeys as $key => $value) {
                            if (strpos($key, ' as ') !== false) {
                                $key = explode(' as ', $key);
                                $key = $key[count($key) - 1];
                            }
                            $achou = false;
                            foreach ($arrayColunas as $key2 => $value2) {
                                $colunaValor = $value2['colunaValor'];
                                if (strpos($colunaValor, ' as ') !== false) {
                                    $colunaValor = explode(' as ', $colunaValor);
                                    $colunaValor = $colunaValor[count($colunaValor) - 1];
                                }
                                if ($key == $colunaValor) {
                                    $achou = true;
                                }
                            }
                            if (!$achou) {
                                unset($gridAntigoComKeys[$key]);
                            }
                        }
                        $grid = array_values($gridAntigoComKeys);
                        usort($grid, function($a, $b) {
                            if (!(isset($a['ordem']))) {
                                $a['ordem'] = 99999;
                            }
                            if (!(isset($b['ordem']))) {
                                $b['ordem'] = 99999;
                            }
                            if (!($a['ordem'] == $b['ordem'])) {
                                return $a['ordem'] < $b['ordem'] ? - 1 : 1;
                            } else {
                                return 0;
                            }
                        });
                        foreach ($grid as $key => &$gridValue) {
                            if (!isset($gridValue['colunaOK'])) {
                                $gridValue['colunaExibir'] = false;
                                if (!(isset($r_generateHTML))) {
                                    unset($grid[$key]);
                                }
                            } else {
                                if (isset($r_generateHTML) || isset($r_generateCSV)) {
                                    $gridValue['colunaExibir'] = true;
                                }
                            }
                        }
                    }
                }
            }
            if (!(!isset($r_pegarColunas))) {
                $g['ui']->pegarColunas();
            }
            $extraWhere = gPegarParteWhere($extra);
            $extraJoin = gPegarParteJoin($extra);
            $extraGroupBy = gPegarParteGroupBy($extra);
            foreach ($grid as $gridItemKey => $gridItem) {
                if (!(!$gridItem['colunaPorcentagemCampo'])) {
                    $grid[$gridItemKey]['colunaTipo'] = 'banco';
                    $grid[$gridItemKey]['callback'] = $gridItem['callback'] ? $gridItem['callback'] : 'gPorcentagem';
                    $grid[$gridItemKey]['colunaCasasDecimais'] = $gridItem['colunaCasasDecimais'] ? $gridItem['colunaCasasDecimais'] : '2';
                    $grid[$gridItemKey]['colunaNaoAtualizar'] = true;
                    $campo = $gridItem['colunaPorcentagemCampo'];
                    $extraJoin_ = !$g['usarClickHouse'] ? $extraJoin : '';
                    $campoJoin = $campo;
                    if (stripos($campo, 'sum') === false) {
                        $campoJoin = "sum({$campo})";
                        if (!(empty($extraGroupBy))) {
                            $campo = "sum({$campo})";
                        }
                    }
                    $joinIncluir = <<<EOT
                    /* [Porcentagem] {$gridItem['colunaValor']} (Total) */
                    CROSS JOIN (SELECT {$campoJoin} as valor FROM {$from} {$extraJoin_} WHERE [[filtro]]) as `{$gridItem['colunaValor']}_Total`
                    /* [/Porcentagem] */
EOT;
                    $grid[$gridItemKey]['colunaValorFiltro'] = $gridItem['colunaValor'];
                    $grid[$gridItemKey]['colunaValorCalculado'] = $g['usarClickHouse'] ? "{$campo} / any(`{$gridItem['colunaValor']}_Total`.valor) * 100" : "{$campo} / `{$gridItem['colunaValor']}_Total`.valor * 100";
                    $grid[$gridItemKey]['colunaValorCalculado'] .= " as `{$gridItem['colunaValor']}`";
                    $extraReplaceUniq = uniqid();
                    $extra = str_replace('WHERE [[filtro]]', $extraReplaceUniq, $extra);
                    $extra = substr_replace($extra, "\n{$joinIncluir}\n", strrpos($extra, 'WHERE'), 0);
                    $extra = str_replace($extraReplaceUniq, 'WHERE [[filtro]]', $extra);
                }
                if ($gridItem['colunaAbcCampo'] && $gridItem['colunaAbcLigacao']) {
                    $grid[$gridItemKey]['colunaTipo'] = 'banco';
                    $grid[$gridItemKey]['colunaNaoAtualizar'] = true;
                    $campo = $gridItem['colunaAbcCampo'];
                    $extraJoin_ = !$g['usarClickHouse'] ? $extraJoin : '';
                    if (stripos($campo, 'sum') === false) {
                        $campo = "sum({$campo})";
                    }
                    $joinIncluir = <<<EOT
                /* [CurvaABC] {$gridItem['colunaValor']} (Total) */
                CROSS JOIN (
                    SELECT {$campo} as valor FROM {$from} {$extraJoin_} WHERE [[filtro]]
                ) as `{$gridItem['colunaValor']}_Total`
                /* [/CurvaABC] */
EOT;
                    if ($g['usarClickHouse']) {
                        $joinIncluir2 = <<<EOT
                        /* [CurvaABC] {$gridItem['colunaValor']} (Acumulador) */
                        LEFT JOIN (
                            SELECT campo, SUM(valor) OVER (ORDER BY valor DESC) as valor FROM (
                                SELECT {$gridItem['colunaAbcLigacao']} as campo, {$campo} as valor FROM {$from} {$extraJoin_} WHERE [[filtro]] GROUP BY {$gridItem['colunaAbcLigacao']}
                            ) as `{$gridItem['colunaValor']}_Total_ABC_Interno`
                        ) as `{$gridItem['colunaValor']}_Total_ABC` ON {$gridItem['colunaAbcLigacao']} = `{$gridItem['colunaValor']}_Total_ABC`.campo
                        /* [/CurvaABC] */
EOT;
                    } else {
                        $curvaAbcVar = 'curva_abc_' . uniqid();
                        $joinIncluir2 = <<<EOT
                        /* [CurvaABC] {$gridItem['colunaValor']} (Acumulador) */
                        LEFT JOIN (
                            SELECT campo, (@{$curvaAbcVar}:=@{$curvaAbcVar} + valor) as valor FROM (
                                SELECT
                                    {$gridItem['colunaAbcLigacao']} as campo, IFNULL({$campo}, 0) as valor
                                FROM {$from}
                                {$extraJoin_}
                                JOIN (SELECT @{$curvaAbcVar}:=0) {$curvaAbcVar}
                                WHERE [[filtro]]
                                GROUP BY {$gridItem['colunaAbcLigacao']}
                                ORDER BY 2 DESC
                            ) as `{$gridItem['colunaValor']}_Total_ABC_Interno`
                        ) as `{$gridItem['colunaValor']}_Total_ABC` ON {$gridItem['colunaAbcLigacao']} = `{$gridItem['colunaValor']}_Total_ABC`.campo
                        /* [/CurvaABC] */
EOT;
                    }
                    $grid[$gridItemKey]['colunaValorFiltro'] = $gridItem['colunaValor'];
                    if ($g['usarClickHouse']) {
                        $grid[$gridItemKey]['colunaValorCalculado'] = "if(toFloat64(any(`{$gridItem['colunaValor']}_Total_ABC`.valor)) < (any(`{$gridItem['colunaValor']}_Total`.valor) * 0.80), 'A', (if(toFloat64(any(`{$gridItem['colunaValor']}_Total_ABC`.valor)) < (any(`{$gridItem['colunaValor']}_Total`.valor) * 0.90), 'B', 'C')))";
                    } else {
                        $grid[$gridItemKey]['colunaValorCalculado'] = "if(`{$gridItem['colunaValor']}_Total_ABC`.valor < (`{$gridItem['colunaValor']}_Total`.valor * 0.80), 'A', (if(`{$gridItem['colunaValor']}_Total_ABC`.valor < (`{$gridItem['colunaValor']}_Total`.valor * 0.90), 'B', 'C')))";
                    }
                    $grid[$gridItemKey]['colunaValorCalculado'] .= " as `{$gridItem['colunaValor']}`";
                    $extraReplaceUniq = uniqid();
                    $extra = str_replace('WHERE [[filtro]]', $extraReplaceUniq, $extra);
                    $extra = substr_replace($extra, "\n{$joinIncluir}\n", strrpos($extra, 'WHERE'), 0);
                    $extra = substr_replace($extra, "\n{$joinIncluir2}\n", strrpos($extra, 'WHERE'), 0);
                    $extra = str_replace($extraReplaceUniq, 'WHERE [[filtro]]', $extra);
                }
            }
            $extraWhere = gPegarParteWhere($extra);
            $extraJoin = gPegarParteJoin($extra);
            $extraGroupBy = gPegarParteGroupBy($extra);
            $camposPivoSubstituicoes = [];
            foreach ($grid as $gridItemKey => $gridItem) {
                if ($gridItem['colunaTipo'] == 'pivo') {
                    $grid[$gridItemKey]['colunaTipo'] = 'livre';
                    $grid[$gridItemKey]['colunaExibir'] = false;
                    $colunaPivoMostrar = $gridItem['colunaPivoMostrar'] ? $gridItem['colunaPivoMostrar'] : $gridItem['colunaValor'];
                    $colunaPivoMostrar = $g['usarClickHouse'] ? "any({$colunaPivoMostrar})" : $colunaPivoMostrar;
                    $colunaPivoOrdenacao = $gridItem['colunaPivoOrdenacao'] ? $gridItem['colunaPivoOrdenacao'] : 'ASC';
                    $colunaPivoLimite = $gridItem['colunaPivoLimite'] ? $gridItem['colunaPivoLimite'] : '25';
                    $extraJoin_ = !$g['usarClickHouse'] ? $extraJoin : '';
                    $sql = <<<EOT
                    SELECT
                        DISTINCT {$gridItem['colunaValor']} as chave,
                        {$colunaPivoMostrar} as valor
                    FROM {$from}
                    {$extraJoin_}
                    WHERE {$extraWhere}
                    GROUP BY {$gridItem['colunaValor']}
                    ORDER BY {$gridItem['colunaValor']} {$colunaPivoOrdenacao}
                    LIMIT {$colunaPivoLimite}
EOT;
                    $sql = str_replace('[[filtro]]', $extraWhere, $sql);
                    $sql = str_replace('[[join]]', $extraJoin, $sql);
                    log("SQL Coluna Pivo: {$sql}");
                    if (!(!$g['usarClickHouse'])) {
                        $sql = "/* ClickHouse */\n{$sql}";
                    }
                    $result = mysql_query($sql);
                    if ($result) {
                        $colunaPivoValor = $gridItem['colunaPivoValor'];
                        if (!(is_array($colunaPivoValor))) {
                            $colunaPivoValor = ['Valor' => $colunaPivoValor];
                        }
                        $primeiraLinhaGridHead = '';
                        $colunaPivoRemover = [];
                        while ($row = mysql_fetch_assoc($result)) {
                            if (!(!empty($row['valor']))) {
                            }
                            $colunaPivoCount = 0;
                            $colunaPivoJoin = array();
                            foreach ($colunaPivoValor as $itemPivoChave => $itemPivoValor) {
                                $valor = $itemPivoValor;
                                if (strpos($valor, ' as ') > 0) {
                                    $valor = explode(" as ", $valor);
                                    $valor = $valor[1];
                                }
                                $colunaPivoUniq = "`{$gridItem['colunaValor']}-{$row['chave']}`.{$valor}";
                                $colunaPivoPaiUniq = "{$gridItem['colunaValor']}-{$valor}";
                                $novaColuna = $gridItem;
                                if (count($colunaPivoValor) > 1) {
                                    foreach ($grid as $gridItemKey2 => $gridItem2) {
                                        if ($gridItem2['colunaValor'] == $colunaPivoPaiUniq) {
                                            $encontrou = true;
                                            $novaColuna = array_merge($novaColuna, $gridItem2);
                                            $colunaPivoRemover[] = $gridItemKey2;
                                        }
                                    }
                                }
                                if (!isset($novaColuna['colunaExibir']) || $novaColuna['colunaExibir']) {
                                    $colunaPivoCount++;
                                }
                                $novaColuna['colunaTipo'] = 'banco';
                                $novaColuna['colunaPivoPai'] = count($colunaPivoValor) > 1 ? $colunaPivoPaiUniq : $gridItem['colunaValor'];
                                $novaColuna['colunaValor'] = $g['usarClickHouse'] ? "any({$colunaPivoUniq})" : "{$colunaPivoUniq}";
                                $novaColuna['colunaTitulo'] = $itemPivoChave == 'Valor' ? $row['valor'] : $itemPivoChave;
                                $novaColuna['colunaPivoEditarCampo'] = $itemPivoValor;
                                if (count($colunaPivoValor) > 1) {
                                    $novaColuna['colunaDesabilitarOrdenacao'] = true;
                                }
                                unset($novaColuna['colunaPivoMostrar']);
                                unset($novaColuna['colunaPivoOrdenacao']);
                                unset($novaColuna['colunaPivoLigacao']);
                                unset($novaColuna['colunaPivoValor']);
                                $camposPivoSubstituicoes[$colunaPivoPaiUniq] = $novaColuna['colunaValor'];
                                $encontrou = false;
                                foreach ($grid as $gridItemKey2 => $gridItem2) {
                                    if ($gridItem2['colunaValor'] == $novaColuna['colunaValor']) {
                                        $encontrou = true;
                                        $grid[$gridItemKey2] = array_merge($novaColuna, $gridItem2);
                                    }
                                }
                                if (!$encontrou) {
                                    $grid[] = $novaColuna;
                                }
                                $colunaPivoJoin[] = $itemPivoValor;
                            }
                            $colunaPivoJoin = implode(',', $colunaPivoJoin);
                            $joinIncluir = <<<EOT
                    /* [PIVO] {$gridItem['colunaValor']} */
                    LEFT JOIN (
                        SELECT
                            {$gridItem['colunaPivoLigacao']} as campo,
                            {$colunaPivoJoin}
                        FROM {$from} {$extraJoin_} WHERE [[filtro]] AND {$gridItem['colunaValor']} = '{$row['chave']}' GROUP BY {$gridItem['colunaPivoLigacao']}
                    ) as `{$gridItem['colunaValor']}-{$row['chave']}` ON {$gridItem['colunaPivoLigacao']} = `{$gridItem['colunaValor']}-{$row['chave']}`.campo
                    /* [/PIVO] */
EOT;
                            $extraReplaceUniq = uniqid();
                            $extra = str_replace('WHERE [[filtro]]', $extraReplaceUniq, $extra);
                            $extra = substr_replace($extra, "\n{$joinIncluir}\n", strrpos($extraOriginal, 'WHERE'), 0);
                            $extra = str_replace($extraReplaceUniq, 'WHERE [[filtro]]', $extra);
                            while (!(count($colunaPivoValor) > 1)) {
                                foreach ($grid as $key => $value) {
                                    if ($value['colunaPivoPai']) {
                                        if (!(!$value['callbackParameter'])) {
                                            foreach ($value['callbackParameter'] as $key2 => $value2) {
                                                foreach ($camposPivoSubstituicoes as $subFrom => $subTo) {
                                                    $value2 = str_replace($subFrom, $subTo, $value2);
                                                }
                                                $grid[$key]['callbackParameter'][$key2] = $value2;
                                            }
                                        }
                                    }
                                }
                            }
                            $primeiraLinhaGridHead .= "<th colspan='{$colunaPivoCount}' class='ui-widget-header ui-corner-all' style='text-align: center; z-index: 2;'>{$row['valor']}</th>";
                        }
                        $colunaPivoRemover = array_unique($colunaPivoRemover);
                        foreach ($colunaPivoRemover as $value) {
                            unset($grid[$value]);
                        }
                        if (count($colunaPivoValor) > 1) {
                            $colspan = 1;
                            foreach ($grid as $gridItemKey2 => $gridItem2) {
                                if (!$gridItem2['colunaPivoPai']) {
                                    if (!isset($gridItem2['colunaExibir']) || $gridItem2['colunaExibir']) {
                                        $colspan++;
                                    }
                                }
                            }
                            $primeiraLinhaGridHead = "<tr><th colspan='{$colspan}' class='ui-widget-header ui-corner-all' style='text-align: center; z-index: 2;'>&nbsp;</th>{$primeiraLinhaGridHead}</tr>";
                            $g['smarty']->assign('primeiraLinhaGridHead', $primeiraLinhaGridHead);
                        }
                    } else {
                        die('Erro ao montar colunaPivo: ' . mysql_error() . ". SQL: {$sql}");
                    }
                }
            }
            $arquivoColunasOrdem = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ordem-' . $g['usuarioAtual'];
            $ordenacaoUsuario = true;
            $arquivoColunasOrdemIde = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ordem';
            if (!(!file_exists($arquivoColunasOrdemIde))) {
                $ordenacaoUsuario = false;
                $arquivoColunasOrdem = $arquivoColunasOrdemIde;
            }
            $arquivoColunasOrdemApps = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ordem';
            if (!$g['modoDesenvolvedor'] && file_exists($arquivoColunasOrdemApps)) {
                $ordenacaoUsuario = false;
                $arquivoColunasOrdem = $arquivoColunasOrdemApps;
            }
            $arquivoColunasOrdemAppsEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ordem';
            if ($g['modoDesenvolvedorApp'] && file_exists($arquivoColunasOrdemAppsEditor)) {
                $ordenacaoUsuario = false;
                $arquivoColunasOrdem = $arquivoColunasOrdemAppsEditor;
            }
            if (!(!file_exists($arquivoColunasOrdem))) {
                $ordem = file_get_contents($arquivoColunasOrdem);
                $ordem = explode("\n", $ordem);
                $arrayOrdem = array();
                $gridAntigoComKeys = array();
                foreach ($grid as $key => $value) {
                    $colunaValor = $value['colunaValor'];
                    if (strpos($colunaValor, ' as ') !== false) {
                        $colunaValor = explode(' as ', $colunaValor);
                        $colunaValor = $colunaValor[count($colunaValor) - 1];
                    }
                    $colunaValor = trim($colunaValor);
                    $nivel = 1;
                    if (isset($value['colunaExibirNivel']) && $value['colunaExibirNivel']) {
                        $nivel = $value['colunaExibirNivel'];
                    }
                    if (!($g['nivelAlcada'] < $nivel)) {
                        $value['tmpOrdem'] = $value['tmpOrdemOriginal'] = ($key + 1) * 100;
                        if (!array_key_exists($colunaValor, $gridAntigoComKeys)) {
                            $gridAntigoComKeys[$colunaValor] = $value;
                            $arrayOrdem[$value['tmpOrdem']] = $colunaValor;
                        } else {
                            $gridAntigoComKeys[$colunaValor]['colunasDuplicadas'][] = $value;
                        }
                    }
                }
                $ultimoCampo = '';
                $ultimoKey = 0;
                foreach ($ordem as $colunaValor) {
                    if (!(trim($colunaValor) == 'countLinha')) {
                        $modificado = false;
                        if (strpos($colunaValor, "\t") !== false) {
                            $modificado = true;
                        }
                        $colunaValor = trim($colunaValor, "\t");
                        if ($modificado) {
                            if (!$ultimoCampo) {
                                $ultimoKey++;
                                $arrayOrdem[$ultimoKey] = $colunaValor;
                                if (!(!isset($gridAntigoComKeys[$colunaValor]))) {
                                    $ultimoCampo = $colunaValor;
                                }
                                unset($arrayOrdem[$gridAntigoComKeys[$colunaValor]['tmpOrdemOriginal']]);
                            } else {
                                if (isset($gridAntigoComKeys[$colunaValor])) {
                                    unset($arrayOrdem[$gridAntigoComKeys[$colunaValor]['tmpOrdemOriginal']]);
                                    $posicao = ++$gridAntigoComKeys[$ultimoCampo]['tmpOrdem'];
                                    $arrayOrdem[$posicao] = $colunaValor;
                                    $gridAntigoComKeys[$colunaValor]['tmpOrdem'] = $gridAntigoComKeys[$colunaValor]['tmpOrdemOriginal'] = $posicao;
                                    $ultimoCampo = $colunaValor;
                                }
                            }
                        } else {
                            if (!(!isset($gridAntigoComKeys[$colunaValor]))) {
                                $ultimoCampo = $colunaValor;
                            }
                        }
                    }
                }
                $arquivoColunasOrdemUsuario = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ordem-' . $g['usuarioAtual'];
                if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoColunasOrdemUsuario) && $ordenacaoUsuario == false) {
                    $ordem = file_get_contents($arquivoColunasOrdemUsuario);
                    $ordem = explode("\n", $ordem);
                    $ultimoCampo = '';
                    $ultimoKey = 0;
                    foreach ($ordem as $colunaValor) {
                        if (!(trim($colunaValor) == 'countLinha')) {
                            $modificado = false;
                            if (strpos($colunaValor, "\t") !== false) {
                                $modificado = true;
                            }
                            $colunaValor = trim($colunaValor, "\t");
                            if ($modificado) {
                                if (!$ultimoCampo) {
                                    $ultimoKey++;
                                    $arrayOrdem[$ultimoKey] = $colunaValor;
                                    if (!(!isset($gridAntigoComKeys[$colunaValor]))) {
                                        $ultimoCampo = $colunaValor;
                                    }
                                    unset($arrayOrdem[$gridAntigoComKeys[$colunaValor]['tmpOrdemOriginal']]);
                                } else {
                                    if (isset($gridAntigoComKeys[$colunaValor])) {
                                        unset($arrayOrdem[$gridAntigoComKeys[$colunaValor]['tmpOrdemOriginal']]);
                                        $posicao = ++$gridAntigoComKeys[$ultimoCampo]['tmpOrdem'];
                                        $arrayOrdem[$posicao] = $colunaValor;
                                        $gridAntigoComKeys[$colunaValor]['tmpOrdem'] = $gridAntigoComKeys[$colunaValor]['tmpOrdemOriginal'] = $posicao;
                                        $ultimoCampo = $colunaValor;
                                    }
                                }
                            } else {
                                if (!(!isset($gridAntigoComKeys[$colunaValor]))) {
                                    $ultimoCampo = $colunaValor;
                                }
                            }
                        }
                    }
                }
                ksort($arrayOrdem);
                $colunas_ = array();
                $grid = array();
                foreach ($arrayOrdem as $colunaValor) {
                    $colunaValor = trim($colunaValor, "\t");
                    if (!(!isset($gridAntigoComKeys[$colunaValor]))) {
                        $colunas_[] = $colunaValor;
                        $grid[] = $gridAntigoComKeys[$colunaValor];
                        if (!(!isset($gridAntigoComKeys[$colunaValor]['colunasDuplicadas']))) {
                            foreach ($gridAntigoComKeys[$colunaValor]['colunasDuplicadas'] as $value) {
                                $grid[] = $value;
                            }
                        }
                    }
                }
            }
            $arquivoColunasOcultar = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ocultar-' . $g['usuarioAtual'];
            $ocultarColunasUsuario = true;
            $arquivoColunasOcultarIde = "ide/personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ocultar';
            if (!(!file_exists($arquivoColunasOcultarIde))) {
                $ocultarColunasUsuario = false;
                $arquivoColunasOcultar = $arquivoColunasOcultarIde;
            }
            $arquivoColunasOcultarApps = Apps::getDirAtivos() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ocultar';
            if (!$g['modoDesenvolvedor'] && file_exists($arquivoColunasOcultarApps)) {
                $ocultarColunasUsuario = false;
                $arquivoColunasOcultar = $arquivoColunasOcultarApps;
            }
            $arquivoColunasOcultarAppsEditor = Apps::getDirEditor() . "personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ocultar';
            if (!$g['modoDesenvolvedor'] && file_exists($arquivoColunasOcultarAppsEditor)) {
                $ocultarColunasUsuario = false;
                $arquivoColunasOcultar = $arquivoColunasOcultarAppsEditor;
            }
            $arquivoColunasOcultarUsuarioGlobal = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ocultar-global';
            if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp'] && file_exists($arquivoColunasOcultarUsuarioGlobal)) {
                $ocultarColunasUsuario = false;
                $arquivoColunasOcultar = $arquivoColunasOcultarUsuarioGlobal;
            }
            if (!(!file_exists($arquivoColunasOcultar))) {
                $ocultar = file($arquivoColunasOcultar);
                foreach ($ocultar as &$linha) {
                    $linha = explode("\t|\t", $linha);
                    $linha = $linha[0];
                }
                unset($linha);
                foreach ($grid as $key => $value) {
                    if (!$value['ignorarOcultarColuna']) {
                        $colunaValor = $value['colunaValor'];
                        if (strpos($colunaValor, ' as ') !== false) {
                            $colunaValor = explode(' as ', $colunaValor);
                            $colunaValor = $colunaValor[count($colunaValor) - 1];
                        }
                        $colunaValor = trim($colunaValor);
                        if (!(!in_array($colunaValor, $ocultar))) {
                            $grid[$key]['colunaExibir'] = false;
                        }
                    }
                }
            }
            $arquivoColunasOcultar = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-ocultar-' . $g['usuarioAtual'];
            if (file_exists($arquivoColunasOcultar) && $ocultarColunasUsuario == false) {
                $ocultar = file($arquivoColunasOcultar);
                foreach ($ocultar as &$linha) {
                    $linha = explode("\t|\t", $linha);
                    $linha = $linha[0];
                }
                unset($linha);
                foreach ($grid as $key => $value) {
                    if (!$value['ignorarOcultarColuna']) {
                        $colunaValor = $value['colunaValor'];
                        if (strpos($colunaValor, ' as ') !== false) {
                            $colunaValor = explode(' as ', $colunaValor);
                            $colunaValor = $colunaValor[count($colunaValor) - 1];
                        }
                        $colunaValor = trim($colunaValor);
                        if (!(!in_array($colunaValor, $ocultar))) {
                            $grid[$key]['colunaExibir'] = false;
                        }
                    }
                }
            }
            $orderCount = 0;
            $orderCountNaoExibir = 0;
            $orderValuesInUse = [];
            foreach ($grid as $value) {
                if ($value['colunaOrdem']) {
                    $orderValuesInUse[] = $value['colunaOrdem'];
                }
            }
            foreach ($grid as $key => $value) {
                $nivel = 1;
                if (isset($value['colunaExibirNivel']) && $value['colunaExibirNivel']) {
                    $nivel = $value['colunaExibirNivel'];
                }
                if (!(isset($value['colunaExibir']) && !$value['colunaExibir'] || $g['nivelAlcada'] < $nivel)) {
                    if (!($value['colunaOrdem'])) {
                        $orderCount = $orderCount + 1;
                        $orderCountNaoExibir = $orderCount;
                        while (in_array($orderCount, $orderValuesInUse)) {
                        }
                        $grid[$key]['colunaOrdem'] = $orderCount;
                    }
                } else {
                    $orderCountNaoExibir = $orderCountNaoExibir + 0.01;
                    $grid[$key]['colunaOrdem'] = $orderCountNaoExibir;
                }
            }
            usort($grid, function($a, $b) {
                return $a['colunaOrdem'] - $b['colunaOrdem'] > 0 ? 1 : (($a['colunaOrdem'] - $b['colunaOrdem'] > 0) < 0 ? - 1 : 0);
            });
            foreach ($grid as $key => $value) {
                if (!$grid[$key]['colunaValorJsonProcessado'] && strpos($value['colunaValor'], '->') !== false) {
                    $grid[$key]['colunaValorOriginal'] = $value['colunaValor'];
                    $value['colunaValor'] = gSubstituirCamposJson($value['colunaValor']);
                    if ($json !== true) {
                        $value['colunaValor'] .= " as `{$grid[$key]['colunaValorOriginal']}`";
                    }
                    $grid[$key]['colunaValor'] = $value['colunaValor'];
                    $grid[$key]['colunaValorJsonProcessado'] = true;
                }
                $colunaValor = $value['colunaValor'];
                if (strpos($colunaValor, ' as ') !== false) {
                    $colunaValor = explode(' as ', $colunaValor);
                    $colunaValor = $colunaValor[count($colunaValor) - 1];
                }
                $grid[$key]['colunaValor64'] = base64_encode($value['colunaValor']);
                foreach ($totais as &$total) {
                    $totalValor = $total['valor'];
                    $totalTipo = $total['tipo'] ? $total['tipo'] : 'sql';
                    if (!($totalTipo != 'sql')) {
                        if ($colunaValor == $totalValor) {
                            $total['ok'] = true;
                        }
                    }
                }
                if (isset($r_generateHTML) || isset($r_generateCSV)) {
                    if (!isset($value['forcarExibirImpressao']) && !isset($_REQUEST['generateHTMLTodos'])) {
                        if (isset($_REQUEST[base64_encode($colunaValor)])) {
                            $grid[$key]['colunaExibir'] = true;
                        } else {
                            $grid[$key]['colunaExibir'] = false;
                        }
                    }
                }
            }
            foreach ($totais as &$total) {
                $totalValor = $total['valor'];
                $totalNome = $total['nome'];
                $totalTipo = $total['tipo'] ? $total['tipo'] : 'sql';
                if (!($totalTipo != 'sql')) {
                    if (!$total['ok'] && !empty($totalValor)) {
                        $total['alias'] = "Total_{$total['nome']}";
                        $colunaValor = "{$totalValor} as {$total['alias']}";
                        $grid[] = array('colunaValor' => gSubstituirCamposJson($colunaValor), 'colunaValorOriginal' => $colunaValor, 'colunaTitulo' => $totalNome, 'colunaExibir' => false);
                    }
                }
            }
            if (isset($r_loading)) {
                if (!$limit) {
                    $limit = 100;
                }
                if ($limit == 1) {
                    $limit = $limit * $r_loading . ',50';
                } else {
                    $limit = $limit + 50 * ($r_loading - 1) . ',50';
                }
            } else {
                $limit = explode(".", $limit);
                if (!($limit[0])) {
                    $limit[0] = 100;
                }
                if (!($limit[1])) {
                    $limit[1] = 0;
                }
                $limit = $limit[1] . ',' . $limit[0];
            }
            $limit_ = explode(',', $limit);
            $g['linhaCount'] = $limit_[0] + 1;
            $g['smarty']->assign('linhaCount', $limit_[0] + 1);
            if (!isset($r_pegarHTMLColunas)) {
                $temDimensao = $temMetrica = array();
                foreach ($grid as $grid_key => $grid_valor) {
                    if (isset($grid_valor['colunaDimensao']) && $grid_valor['colunaDimensao']) {
                        $temDimensao[$grid_valor['colunaValor']] = array($grid_valor['colunaValor'], $grid_valor['colunaTitulo']);
                    }
                    if (isset($grid_valor['colunaMetrica']) && $grid_valor['colunaMetrica']) {
                        $temMetrica[$grid_valor['colunaValor']] = array($grid_valor['colunaValor'], $grid_valor['colunaTitulo']);
                    }
                }
                if (!empty($temDimensao) && !empty($temMetrica) && empty($r_generateEmail)) {
                    $g['smarty']->assign('mostrarAgrupamento', true);
                    $dados = array('dimensoes' => $temDimensao, 'metricas' => $temMetrica, 'from' => $from, 'extra' => $extra, 'textoFiltroImprimir' => $g['ui']->textoFiltroImprimir);
                    $g['smarty']->assign('agrupamentoDadosArray', $dados);
                    $dados = base64_encode(json_encode($dados));
                    $g['smarty']->assign('agrupamentoDados', $dados);
                } else {
                    $g['smarty']->assign('mostrarAgrupamento', false);
                }
                $sqlCampos = '';
                $sqlCamposComAlias = '';
                $sqlCallbacks = array();
                $countColunaFormula = 0;
                foreach ($grid as $grid_valor) {
                    $colunaValor = $grid_valor['colunaValorCalculado'] ? $grid_valor['colunaValorCalculado'] : $grid_valor['colunaValor'];
                    if (!(!isset($grid_valor['callback']))) {
                        $sqlCallbacks[$grid_valor['colunaTitulo']] = $grid_valor['callback'];
                    }
                    if (empty($grid_valor['colunaTipo']) || $grid_valor['colunaTipo'] == 'banco') {
                        if ($g['conf']->camposNaoUsar($grid_valor['colunaValor'])) {
                            if ($json === true) {
                                $colunaFormulaAlias = "{$colunaValor} as {$grid_valor['colunaTitulo']}";
                                $sqlCampos .= empty($sqlCampos) ? $colunaFormulaAlias : ', ' . $colunaFormulaAlias;
                            } else {
                                $sqlCampos .= empty($sqlCampos) ? $colunaValor : ', ' . $colunaValor;
                                $valor = explode(' as ', $colunaValor);
                                $alias = empty($grid_valor['colunaTitulo']) ? "{$valor[0]} as ``" : "{$valor[0]} as `{$grid_valor['colunaTitulo']}`";
                                $sqlCamposComAlias .= empty($sqlCamposComAlias) ? $alias : ', ' . $alias;
                            }
                        }
                    } else {
                        if ($grid_valor['colunaTipo'] == 'string') {
                        } else {
                            if ($grid_valor['colunaTipo'] == 'formula') {
                                $countColunaFormula++;
                                $colunaFormulaAlias = "'' as colunaFormula{$countColunaFormula}";
                                $sqlCampos .= empty($sqlCampos) ? $colunaFormulaAlias : ', ' . $colunaFormulaAlias;
                                $valor = explode(' as ', $colunaFormulaAlias);
                                $alias = empty($grid_valor['colunaTitulo']) ? "{$valor[0]} as ``" : "{$valor[0]} as `{$grid_valor['colunaTitulo']}`";
                                $sqlCamposComAlias .= empty($sqlCamposComAlias) ? $alias : ', ' . $alias;
                            }
                        }
                    }
                }
                if (!empty($sqlCampos)) {
                    if (!$returnCount) {
                        if (!$r_generateCSV && !$r_generateHTML && !$r_generateAPI) {
                            if (isset($r_loading)) {
                                $extra .= " LIMIT {$limit}";
                            } else {
                                $limit_ = explode(',', $limit);
                                $totaisLimit = $limit_[1];
                            }
                        } else {
                            if ($r_generateCSV) {
                                set_time_limit(0);
                            } else {
                                set_time_limit(0);
                            }
                        }
                        $tmp = function($sqlCampos) {
                            foreach ($GLOBALS as $key => $value) {
                                ${$key} = $value;
                            }
                            foreach ($g['ui']->gridFiltro['campos'] as $key => $value) {
                                $key = str_replace(".", "_", $key);
                                if (is_array($value['busca'])) {
                                    if (!empty($value['busca'][0]) && !empty($value['busca'][1])) {
                                        ${$key} = gCorrigeDataInverte($value['busca'][0]);
                                        $tmp = $key . '0';
                                        ${$tmp} = gCorrigeDataInverte($value['busca'][0]);
                                        $tmp = $key . '1';
                                        ${$tmp} = gCorrigeDataInverte($value['busca'][1]);
                                    }
                                } else {
                                    ${$key} = $value['busca'];
                                }
                            }
                            $sqlCampos = str_replace("\\", "\\\\", $sqlCampos);
                            $sqlCampos = str_replace('"', '\\"', $sqlCampos);
                            eval("\$sqlCampos = \"{$sqlCampos}\";");
                            return $sqlCampos;
                        };
                        $sqlCampos = $tmp($sqlCampos);
                        $sqlCamposComAlias = $tmp($sqlCamposComAlias);
                        $extra = str_replace('[[filtro]]', $extraWhere, $extra);
                        $extra = str_replace('[[join]]', $extraJoin, $extra);
                        $sql = 'SELECT ' . $time . ' ' . $sqlCampos . ' FROM ' . $from . ' ' . $extra;
                        $sqlComAlias = 'SELECT ' . $time . ' ' . $sqlCamposComAlias . ' FROM ' . $from . ' ' . $extra;
                        if (!(!$g['usarClickHouse'])) {
                            $sql = "/* ClickHouse */\n{$sql}";
                        }
                        if (!$returnSql) {
                            $g['smarty']->assign('gridExtra', $extra);
                            $g['smarty']->assign('gridFrom', $from);
                            $g['smarty']->assign('gridSql', $sql);
                            $g['smarty']->assign('gridSqlComAlias', $sqlComAlias);
                            $csvExportConnection = null;
                            $csvExportLock = null;
                            $usarIteradorEmDisco = !$json && !$g['usarClickHouse'] && !(isset($_buscarBancoAuditoria) && $_buscarBancoAuditoria === true) && !$r_cacheDados && $r_generateCSV;
                            if ($usarIteradorEmDisco) {
                                require_once __DIR__ . '/csvExportIterator.php';
                                list($csvExportConnection, $csvExportLock) = CsvExportIterator::openConnection($confUsuario, (bool)$r_generateCSV);
                            }
                            if (!$gridRetornouMuitosRegistros && !$g['ignorarGerarSQLGrid']) {
                                if (isset($_buscarBancoAuditoria) && $_buscarBancoAuditoria === true) {
                                    $mysqlQuery = mysqli_query($sql, $g['conexaoBancoAuditoria']);
                                    if (!$mysqlQuery) {
                                        $erro_sql = mysql_error($g['conexaoBancoAuditoria']);
                                    }
                                } else {
                                    if ($csvExportConnection) {
                                        $mysqlQuery = \mysqli_query($csvExportConnection, $sql, MYSQLI_USE_RESULT);
                                        if (!$mysqlQuery) {
                                            $erro_sql = \mysqli_error($csvExportConnection);
                                        }
                                    } else {
                                        $mysqlQuery = mysql_query($sql);
                                        if (!$mysqlQuery) {
                                            $erro_sql = mysql_error();
                                        }
                                    }
                                }
                                if (strpos("...{$erro_sql}", "Query execution was interrupted, maximum statement execution time exceeded") > 0 || strpos("...{$erro_sql}", "Lost connection to MySQL server during query") > 0) {
                                    $erro_sql = "<br><spam style='font-size:18px;color:red; position:relative; margin-top:50px;margin-left:40px;font-weight:bold'>Consulta cancelada!</spam><br><br><spam style='font-size:18px;color:red; position:relative; margin-top:40px;margin-left:40px;'>Sua consulta está demorando muito! Eu cancelei para não travar o sistema, ok! Você pode tentar novamente ou pode melhorar o filtro para trazer menos registros. Obrigado!</spam><br /><br /><spam style='font-size:11px;color:red; position:relative; margin-top:40px;margin-left:40px;'>Você pode reportar ao suporte técnico esta instrução:</spam><br /><br /><spam style='font-size:11px;color:red; position:relative; margin-top:40px;margin-left:40px;'>{$sql}</font></spam><br><br>" . $erro_sql;
                                }
                                if (empty($erro_sql)) {
                                    if ($csvExportConnection) {
                                        $resultado = new CsvExportIterator($mysqlQuery, $csvExportConnection, $csvExportLock);
                                        $csvExportConnection = null;
                                        $csvExportLock = null;
                                    } else {
                                        $resultado = array();
                                        if ($json === true) {
                                            while ($row = mysql_fetch_assoc($mysqlQuery)) {
                                                foreach ($sqlCallbacks as $idCallback => $callback) {
                                                    include "bibliotecas/funcoesCallback/sql/{$callback}.php";
                                                }
                                                $resultado[] = $row;
                                            }
                                        } else {
                                            while ($row = mysql_fetch_row($mysqlQuery)) {
                                                $resultado[] = $row;
                                            }
                                        }
                                        mysql_free_result($mysqlQuery);
                                    }
                                } else {
                                    if ($csvExportConnection) {
                                        CsvExportIterator::closeConnection($csvExportConnection, $csvExportLock);
                                    }
                                    die("Erro ao executar a consulta: {$erro_sql}.\nSQL: {$sql}");
                                }
                            }
                            if ($csvExportConnection) {
                                CsvExportIterator::closeConnection($csvExportConnection, $csvExportLock);
                            }
                            global $r_duplicarGrid, $r_duplicarGridTabHref, $gridFiltro, $botoes;
                            if ($r_duplicarGrid) {
                                if ($g['modoDesenvolvedor']) {
                                    Personalizacoes::$rootDir = 'ide/';
                                } else {
                                    if (!(!$g['modoDesenvolvedorApp'])) {
                                        Personalizacoes::$rootDir = Apps::getDirEditor();
                                    }
                                }
                                $md5 = md5(uniqid());
                                $grid = array('nome' => '', 'md5' => md5("sistema/personalizacao/grid/{$md5}/"), 'from' => $from, 'extra' => $extraOriginal, 'grid' => $grid, 'sql' => $sql, 'totais' => $totais, 'gridFiltro' => $gridFiltro, 'botoes' => $botoes, 'require' => "{$g['r'][0]}/{$g['r'][1]}");
                                $nome = strtolower(preg_replace('/[[:space:]]+/', ' ', gLimpaAcentosPuro($r_duplicarGrid)));
                                $count = 2;
                                $grids = Personalizacoes::listarGridsNovos();
                                foreach ($grids as $nomeArquivo) {
                                    $grid_ = Personalizacoes::getGridNovo($nomeArquivo);
                                    $nome_ = preg_replace('/\\-[0-9]+$/', '', $grid_['nome']);
                                    if (stripos($nome_, $nome) !== false) {
                                        $count++;
                                    }
                                }
                                $grid['nome'] = $nome . '-' . $count;
                                $arquivo = Personalizacoes::getDirGrids() . $md5;
                                file_put_contents($arquivo, serialize($grid));
                                $tabId = md5(trim($r_duplicarGridTabHref, '/') . '/');
                                $codigo = '';
                                $dir = Personalizacoes::getDirPersonalizacoesTab();
                                $pre = $g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp'] ? '' : "u{$g['C007']['C007_Id']}-";
                                $arquivo = $dir . $pre . $tabId;
                                if (!(!file_exists($arquivo))) {
                                    $codigo = file_get_contents($arquivo);
                                }
                                $titulo = "{$r_duplicarGrid} ({$count})";
                                $codigo .= <<<EOT

url [/sistema/personalizacao/grid/{$md5}/]
titulo [{$titulo}]
---
EOT;
                                file_put_contents($arquivo, $codigo);
                                foreach (array("", "u{$g['usuarioAtual']}-") as $prefixo) {
                                    foreach (scandir(Personalizacoes::getDirPersonalizacoesGrid()) as $arquivo) {
                                        if (strpos($arquivo, "{$prefixo}{$gridId}-callback-") !== false) {
                                            $arquivoSplit = explode('-callback-', $arquivo);
                                            $novoArquivo = Personalizacoes::getDirPersonalizacoesGrid() . $grid['md5'] . '-callback-' . $arquivoSplit[1];
                                            copy(Personalizacoes::getDirPersonalizacoesGrid() . $arquivo, $novoArquivo);
                                        }
                                    }
                                }
                            }
                            if (!$r_generateHTML && !$r_linhaGridId) {
                                $callbackCarregar = false;
                                foreach ($grid as $grid_valor) {
                                    if (!(!$grid_valor['callbackCarregar'])) {
                                        $callbackCarregar = true;
                                    }
                                }
                                if ($callbackCarregar) {
                                    $g['callbackCarregar'] = md5(uniqid());
                                    file_put_contents("/tmp/fw-{$confUsuario['dbDatabase']}-grid-callbackCarregar-{$g['callbackCarregar']}", serialize(array('grid' => $grid, 'sql' => $sql, 'resultado' => $resultado)));
                                    echo<<<EOT
                    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                        divSetarAcaoId('{$g['divId']}', '{$g['callbackCarregar']}', 'callbackCarregar');
                        divRefresh('{$g['divId']}', false, function(data) {
                            data = JSON.parse(data);
                            for (const key in data) {
                                if (data.hasOwnProperty(key)) {
                                    \$(`#{$g['divId']} .\${key}`).parent().html(data[key]);
                                }
                            }
                            corrigeGrid('{$g['divId']}');
                        }, false);
                        divSetarAcaoId('{$g['divId']}', '', 'callbackCarregar');
                    } </script></div>
EOT;
                                }
                            }
                            log("(tempo) ({$g['r_']}) gerarSQLGrid: " . gLogDiffTime());
                            return array($sql, $resultado);
                        } else {
                            return $sql;
                        }
                    } else {
                        $sqlCount = 'SELECT ' . $time . ' ' . $from . '_Id FROM ' . $from . ' ' . gRetirarOrderBy($extra);
                        $mysqlCount = mysql_query($sqlCount);
                        if ($mysqlCount) {
                            $mysqlCount = mysql_num_rows($mysqlCount);
                        }
                        $mysqlCount = empty($mysqlCount) ? 0 : $mysqlCount;
                        return $mysqlCount;
                    }
                } else {
                    return 'Erro - nenhum campo do grid associado a alguma tabela. Execute o UI->grid() direto.';
                }
            } else {
                $arquivoImpSelecao = "{$g['pathDados']}personalizacaoGrid/" . ($md5 ? $md5 : md5($r_grid)) . '-impselecao-' . $g['usuarioAtual'];
                $impSelecao = array();
                if (!(!file_exists($arquivoImpSelecao))) {
                    $impSelecao = unserialize(file_get_contents($arquivoImpSelecao));
                }
                $return = '';
                $camposErros = '';
                $duplicados = array();
                foreach ($grid as $value) {
                    if ($r_pegarColunas != 'ignorar' && $r_pegarHTMLColunas != 'ignorar') {
                        if (!(!(strpos($value['colunaValor'], ' as ') === false && (strpos($value['colunaValor'], '(') !== false || strpos($value['colunaValor'], '.') !== false)))) {
                            $camposErros .= "{$value['colunaValor']}<br />";
                        }
                        if (strpos($value['colunaValor'], ' as ') !== false) {
                            $value['colunaValor'] = explode(' as ', $value['colunaValor']);
                            $value['colunaValor'] = $value['colunaValor'][count($value['colunaValor']) - 1];
                        }
                        $value['colunaValor64'] = base64_encode($value['colunaValor']);
                        if (!(!isset($impSelecao[$value['colunaValor64']]))) {
                            if ($impSelecao[$value['colunaValor64']]) {
                                $value['colunaMarcado'] = true;
                            } else {
                                $value['colunaMarcado'] = false;
                            }
                        }
                    }
                    $colunaValor = trim($value['colunaValor']);
                    if (!(!isset($duplicados[$colunaValor]))) {
                        $camposErros .= "(NOME DUPLICADO) {$colunaValor}<br />";
                    }
                    $duplicados[$colunaValor] = true;
                    foreach ($value as $valueKey => $valueValue) {
                        if ($valueKey == 'colunaValor') {
                            $valueValue = $colunaValor;
                        }
                        if (!($valueKey == 'ordem' || $valueKey == 'colunaOK' || $r_mostrarOriginal && $valueKey == 'colunaValor64')) {
                            if (!(is_array($valueValue) || is_object($valueValue))) {
                                $valueValue = str_replace(array("\r\n", "\r", "\n"), ' ', $valueValue);
                                $return .= "{$valueKey} [{$valueValue}]\n";
                            }
                        }
                    }
                    $return .= "---\n";
                }
                if (!(empty($camposErros))) {
                }
                $conteudo = trim($return);
                echo '<pegarHTMLColunas>' . $conteudo . '</pegarHTMLColunas>';
                die;
            }
        } else {
            $callbackCarregarCache = file_get_contents("/tmp/fw-{$confUsuario['dbDatabase']}-grid-callbackCarregar-{$r_callbackCarregar}");
            $callbackCarregarCache = unserialize($callbackCarregarCache);
            $grid = $callbackCarregarCache['grid'];
            @unlink("/tmp/fw-{$confUsuario['dbDatabase']}-grid-callbackCarregar-{$r_callbackCarregar}");
            if (!$limit) {
                $limit = 100;
            }
            $totaisLimit = $limit;
            return array($callbackCarregarCache['sql'], $callbackCarregarCache['resultado']);
        }
    }

    function gerarSQLInsert($tabela, $Form, $extra = array())
    {
        global $g, $form_editor;
        $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false;
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
        $campos = '';
        $camposAuxiliar = array();
        $valores = '';
        $camposJson = array();
        foreach ($Form->formArray['campos'] as $tab => $tabCampos) {
            foreach ($tabCampos as $campoId => $campoValor) {
                if (!(isset($campoValor['reject']) && $campoValor['reject'])) {
                    if (!(isset($formEditorData[$campoId]) && isset($formEditorData[$campoId]['hidden']) && $formEditorData[$campoId]['hidden'])) {
                        if (!(preg_match('/^' . preg_quote($tabela) . 'A_/i', $campoId))) {
                            if (strpos($campoId, '->') === false) {
                                $campos .= empty($campos) ? $campoId : ", {$campoId}";
                            }
                        }
                        $valor_ = $Form->campoValorEnviado($campoId);
                        if (empty($valor_)) {
                        } else {
                            if ($campoValor['type'] == 'data') {
                                if (!(empty($valor_))) {
                                    $data = explode('/', $valor_);
                                    $valor_ = "{$data[2]}-{$data[1]}-{$data[0]}";
                                }
                            } else {
                                if ($campoValor['type'] == 'dataHora') {
                                    if (!(empty($valor_))) {
                                        $dataHora = explode(' ', trim($valor_));
                                        $data = explode('/', $dataHora[0]);
                                        $hora = isset($dataHora[1]) ? $dataHora[1] : '00:00:00';
                                        $valor_ = "{$data[2]}-{$data[1]}-{$data[0]} {$hora}";
                                    }
                                } else {
                                    if ($campoValor['type'] == 'moeda') {
                                        $valor_ = str_replace('.', '', $valor_);
                                        $valor_ = str_replace(',', '.', $valor_);
                                        $casas = isset($campoValor['casasDecimais']) ? $campoValor['casasDecimais'] : 2;
                                        $casasConf = $g['conf']->camposTipo($campoId);
                                        if ($casasConf) {
                                            $casas = $casasConf[1];
                                        }
                                        $valor_ = number_format($valor_, $casas, '.', '');
                                    } else {
                                        if ($campoValor['type'] == 'tempo') {
                                            $valor_ = $valor_ . ':00';
                                        } else {
                                            if (!isset($campoValor['uppercase']) || isset($campoValor['uppercase']) && !isset($campoValor['uppercase'])) {
                                                $valor_ = mb_strtoupper($valor_);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $valor_ = mysql_real_escape_string($valor_);
                        $valorItem = empty($valor_) && !is_numeric($valor_) ? "''" : "'{$valor_}'";
                        if (preg_match('/^' . preg_quote($tabela) . 'A_/i', $campoId)) {
                            if ($valorItem != "''") {
                                $camposAuxiliar[] = "INSERT INTO {$tabela}A ({$tabela}A_{$tabela}_Id, {$campoId}) VALUES ('[[PKVALOR]]', {$valorItem}) ON DUPLICATE KEY UPDATE {$campoId} = {$valorItem};";
                            }
                        } else {
                            if (strpos($campoId, '->') !== false) {
                                $chave_ = explode('->', $campoId);
                                $campoJson = trim($chave_[0]);
                                $itemJson = trim($chave_[1]);
                                $camposJson[$campoJson][$itemJson] = $valor_;
                            } else {
                                $valores .= empty($valores) ? $valorItem : (empty($valor_) && !is_numeric($valor_) ? ", ''" : ", '{$valor_}'");
                            }
                        }
                    }
                }
            }
        }
        foreach ($extra as $chave => $valor) {
            $campos .= empty($campos) ? $chave : ", {$chave}";
            $valores .= empty($valores) ? "{$valor}" : ", {$valor}";
        }
        foreach ($camposJson as $chave => $itens) {
            $campos .= empty($campos) ? $chave : ", {$chave}";
            $itens_ = '';
            foreach ($itens as $item => $valor) {
                if (!(strlen($valor) == 0)) {
                    $itens_ .= ", '\$.{$item}', '{$valor}'";
                }
            }
            if (strlen($itens_) != 0) {
                $valores .= empty($valores) ? "JSON_SET(COALESCE({$chave}, '{}'){$itens_})" : ", JSON_SET(COALESCE({$chave}, '{}'){$itens_})";
            } else {
                $valores .= empty($valores) ? "null" : ", null";
            }
        }
        $sql = "INSERT INTO {$tabela} ({$campos}) VALUES ({$valores});";
        $result = mysql_query($sql);
        if (mysql_error()) {
            $erro = mysql_error();
            if (preg_match('/Duplicate entry .* for key .*/i', $erro)) {
                $erro = explode('\'', $erro);
                echo gGeraAlertMsg('Operação não concluída', "Já existe um registro com o seguinte valor (duplicado):<br/>{$erro[1]}<br/><br/>Campo:<br/>{$erro[3]}");
            } else {
                echo gGeraAlertMsg('Erro em operação no banco de dados', "Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>{$erro}<br/><br/>SQL:<br/>{$sql}");
            }
            return false;
        } else {
            $mysqlLastId = $g['mysqlLastId'];
            if (!(empty($camposAuxiliar))) {
                foreach ($camposAuxiliar as $query) {
                    $query = str_replace('[[PKVALOR]]', $mysqlLastId, $query);
                    mysql_query($query);
                }
            }
            return $sql;
        }
    }

    function gerarSQLUpdate($tabela, $pkCampo, $pkValor, $Form, $extra = array(), $travar = true)
    {
        global $g, $form_editor;
        $tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : false;
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
        if ($travar) {
            if (!(($erro = $g['conf']->restricaoTravar('alterar', $pkCampo, $pkValor)) !== true)) {
            } else {
                echo gGeraAlertMsg('Erro de restrição', $erro);
                return false;
            }
        }
        $campos = '';
        $camposAuxiliar = array();
        $camposJson = array();
        foreach ($Form->formArray['campos'] as $tab => $tabCampos) {
            foreach ($tabCampos as $campoId => $campoValor) {
                if (!(isset($campoValor['reject']) && $campoValor['reject'])) {
                    $valor_ = $Form->campoValorEnviado($campoId);
                    if ($campoValor['type'] == 'data') {
                        if (!(empty($valor_))) {
                            $data = explode('/', $valor_);
                            $valor_ = "{$data[2]}-{$data[1]}-{$data[0]}";
                        }
                    } else {
                        if ($campoValor['type'] == 'dataHora') {
                            if (!(empty($valor_))) {
                                $dataHora = explode(' ', trim($valor_));
                                $data = explode('/', $dataHora[0]);
                                $hora = isset($dataHora[1]) ? $dataHora[1] : '00:00:00';
                                $valor_ = "{$data[2]}-{$data[1]}-{$data[0]} {$hora}";
                            }
                        } else {
                            if ($campoValor['type'] == 'moeda') {
                                $valor_ = str_replace('.', '', $valor_);
                                $valor_ = str_replace(',', '.', $valor_);
                                $casas = isset($campoValor['casasDecimais']) ? $campoValor['casasDecimais'] : 2;
                                $casasConf = $g['conf']->camposTipo($campoId);
                                if ($casasConf) {
                                    $casas = $casasConf[1];
                                }
                                if (!(!empty($valor_))) {
                                    $valor_ = 0;
                                }
                                $valor_ = number_format($valor_, $casas, '.', '');
                            } else {
                                if ($campoValor['type'] == 'tempo') {
                                    $valor_ = $valor_ . ':00';
                                } else {
                                    if (!isset($campoValor['uppercase']) || isset($campoValor['uppercase']) && !isset($campoValor['uppercase'])) {
                                        $valor_ = mb_strtoupper($valor_);
                                    }
                                }
                            }
                        }
                    }
                    $valor_ = mysql_real_escape_string($valor_);
                    if (isset($formEditorData[$campoId]) && isset($formEditorData[$campoId]['hidden']) && $formEditorData[$campoId]['hidden']) {
                    } else {
                        if (preg_match('/^' . preg_quote($tabela) . 'A_/i', $campoId)) {
                            $valorItem = $valor_ != '0' && empty($valor_) ? "''" : "'{$valor_}'";
                            $camposAuxiliar[] = "INSERT INTO {$tabela}A ({$tabela}A_{$tabela}_Id, {$campoId}) VALUES ('{$pkValor}', {$valorItem}) ON DUPLICATE KEY UPDATE {$campoId} = {$valorItem};";
                        } else {
                            if (strpos($campoId, '->') !== false) {
                                $chave_ = explode('->', $campoId);
                                $campoJson = trim($chave_[0]);
                                $itemJson = trim($chave_[1]);
                                $camposJson[$campoJson][$itemJson] = $valor_;
                            } else {
                                $campos .= empty($campos) ? $valor_ != '0' && empty($valor_) ? "{$campoId} = ''" : "{$campoId} = '{$valor_}'" : ($valor_ != '0' && empty($valor_) ? ", {$campoId} = ''" : ", {$campoId} = '{$valor_}'");
                            }
                        }
                    }
                }
            }
        }
        foreach ($extra as $chave => $valor) {
            $campos .= empty($campos) ? "{$chave} = {$valor}" : ", {$chave} = {$valor}";
        }
        foreach ($camposJson as $chave => $itens) {
            $itens_ = '';
            foreach ($itens as $item => $valor) {
                $itens_ .= ", '\$.{$item}', '{$valor}'";
            }
            $campos .= empty($campos) ? "{$chave} = JSON_SET(COALESCE({$chave}, '{}'){$itens_})" : ", {$chave} = JSON_SET(COALESCE({$chave}, '{}'){$itens_})";
        }
        $pkValor = urldecode($pkValor);
        $sql = "UPDATE {$tabela} SET {$campos} WHERE {$pkCampo} = '{$pkValor}';";
        $result = mysql_query($sql);
        if (mysql_error()) {
            $erro = mysql_error();
            if (preg_match('/Duplicate entry .* for key .*/i', $erro)) {
                $erro = explode('\'', $erro);
                echo gGeraAlertMsg('Operação não concluída', "Já existe um registro com o seguinte valor (duplicado):<br/>{$erro[1]}<br/><br/>Campo:<br/>{$erro[3]}");
            } else {
                echo gGeraAlertMsg('Erro em operação no banco de dados', "Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>{$erro}<br/><br/>SQL:<br/>{$sql}");
            }
            return false;
        } else {
            if (!(empty($camposAuxiliar))) {
                foreach ($camposAuxiliar as $query) {
                    mysql_query($query);
                }
            }
            return $sql;
        }
    }

    function pegarSqlCampo($campo, $from, $extra = '', $countCount = false, $retirarOrderBy = true, $sumSum = false)
    {
        global $r_loading;
        if (!isset($r_loading)) {
            if ($retirarOrderBy) {
                $extra = gRetirarOrderBy($extra);
            }
            if ($countCount) {
                $sql = "SELECT /*+max_execution_time(15000)*/ COUNT(*) FROM (SELECT {$campo} FROM {$from} {$extra}) as Total;";
            } else {
                $sql = "SELECT /*+max_execution_time(15000)*/ {$campo} FROM {$from} {$extra};";
            }
            $result = mysql_query($sql);
            if (mysql_error()) {
                $sql = addslashes(str_replace("\"", "'", str_replace("\r", ' ', str_replace("\n", ' ', $sql))));
                log("Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}");
                return false;
            } else {
                if ($sumSum) {
                    $sum = 0;
                    while ($row = mysql_fetch_array($result)) {
                        $sum += $row[0];
                    }
                    return $sum;
                } else {
                    $row = mysql_fetch_row($result);
                    return $row[0];
                }
            }
        } else {
            return false;
        }
    }

    function pegarSqlCampos($campos, $from, $extra = '')
    {
        global $r_loading;
        if (!isset($r_loading)) {
            $sql = "SELECT /*+max_execution_time(15000)*/ {$campos} FROM {$from} {$extra};";
            $result = mysql_query($sql);
            if (mysql_error()) {
                $sql = addslashes(str_replace("\"", "'", str_replace("\r", ' ', str_replace("\n", ' ', $sql))));
                log("Por favor, contate o suporte técnico.<br/><br/>Erro:<br/>" . mysql_error() . "<br/><br/>SQL:<br/>{$sql}");
                mysql_free_result($result);
                return false;
            } else {
                $row = mysql_fetch_array($result);
                mysql_free_result($result);
                return $row;
            }
        } else {
            return false;
        }
    }

    function gerarSQLGridMulti($grids, $from, $extras, $orderBy = false)
    {
        global $grid;
        $gridOriginal = $grids[0];
        $geradoSql = '';
        foreach ($grids as $key => $grid) {
            $sqlTmp = $this->gerarSQLGrid($grid, $from[$key], $extras[$key], 0, false, false, true);
            $geradoSql .= $geradoSql ? " UNION ({$sqlTmp})" : "({$sqlTmp})";
        }
        $grid = $gridOriginal;
        $mysqlQuery = mysql_query($geradoSql . $orderBy);
        $geradoDados = array();
        while ($row = mysql_fetch_row($mysqlQuery)) {
            $geradoDados[] = $row;
        }
        return array($geradoSql, $geradoDados);
    }

}
