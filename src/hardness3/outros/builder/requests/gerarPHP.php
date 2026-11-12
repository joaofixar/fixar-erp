<?php
//namespace hardness;
//require_once('../conf.php');
//if (empty($g['projeto'])) die('Erro ao carregar projeto.');
//
//if (empty($g['projeto']['forms']) && empty($g['projeto']['grids'])) {
//    echo json_encode([
//        "erro" => "Nenhum Grid ou Form para ser gerado."
//    ]);
//    die();
//}
//
//$progs = scandir("{$g['pathDadosProg']}/");
//$dir = 1;
//foreach ($progs as $arquivo) {
//    if ($arquivo == "." || $arquivo == "..") continue;
//    $count = intval(str_replace('per', '', $arquivo));
//    if ($count >= $dir) {
//        $dir++;
//    }
//}
//$dir = "per" . str_pad($dir, 3, '0', STR_PAD_LEFT);
//$nomeProjeto =  preg_replace("/[^a-zA-Z0-9]+/", "", gLimpaAcentosPuro($g['projeto']['nome']));
//
//$menu = '';
//
//// CONTENT & TAB
//$content = '';
//$tab = '';
//$content .= <<<'EOT'
//<?php
//namespace hardness;
//switch ($g['r'][3]) {
//
//EOT;
//$tab .= <<<'EOT'
//<?php
//namespace hardness;
//switch ($g['r'][3]) {
//
//EOT;
//
//// Forms
//foreach ($g['projeto']['forms'] as $form) {
//    $nomeForm = preg_replace("/[^a-zA-Z0-9]+/", "", gLimpaAcentosPuro($form["nome"]));
//    // CONTENT
//    $case = <<<'EOT'
//        case '***CASE***':
//            $content1 = uniqid();
//            $g['smarty']->assign('content1', $content1);
//            $g['smarty']->assign('tipo', 'unico');
//            $actions = array(
//                array($content1, '/per/***DIR***/tab/***TAB***/', '&acao=' . urlencode($r_acao) . '&acaoId=' . urlencode($r_acaoId))
//            );
//            echo gProcessaAcoes($actions);
//            break;
//
//
//EOT;
//    $case = str_replace("***CASE***", "content" . ucfirst($nomeForm) . "Form", $case);
//    $case = str_replace("***TAB***", "tab" . ucfirst($nomeForm) . "Form", $case);
//    $case = str_replace("***DIR***", $dir, $case);
//    // TAB
//    $content .= $case;
//    $case = <<<'EOT'
//        case '***CASE***':
//            $formId = uniqid();
//            $g['smarty']->assign('formId', $formId);
//            $tabs = array();
//            $tabsList = array(
//                array(uniqid(), '/per/***DIR***/form/***FORM***/', '***NOME***', '&tab=cadastro&acaoId=' . urlencode($_REQUEST['acaoId']) . '&formId=' . urlencode($formId))
//            );
//            echo gProcessaTabs($tabsList, $tabs);
//            $g['smarty']->assign('tabs', $tabs);
//            $g['smarty']->display('tab.tpl');
//            break;
//
//
//EOT;
//    $case = str_replace("***CASE***", "tab" . ucfirst($nomeForm) . "Form", $case);
//    $case = str_replace("***FORM***", "form" . ucfirst($nomeForm), $case);
//    $case = str_replace("***DIR***", $dir, $case);
//    $case = str_replace("***NOME***", $form["nome"], $case);
//    $tab .= $case;
//}
//
//// Grids
//$count = 0;
//foreach ($g['projeto']['grids'] as $grid) {
//    $count++;
//    $nomeGrid = preg_replace("/[^a-zA-Z0-9]+/", "", gLimpaAcentosPuro($grid["nome"]));
//    // CONTENT
//    $case = <<<'EOT'
//        case '***CASE***':
//            $content1 = uniqid();
//            $g['smarty']->assign('content1', $content1);
//            $g['smarty']->assign('tipo', 'unico');
//            $actions = array(
//                array($content1, '/per/***DIR***/tab/***TAB***/')
//            );
//            echo gProcessaAcoes($actions);
//            break;
//
//
//EOT;
//    $case = str_replace("***CASE***", "content" . ucfirst($nomeGrid) . "Grid", $case);
//    $case = str_replace("***TAB***", "tab" . ucfirst($nomeGrid) . "Grid", $case);
//    $case = str_replace("***DIR***", $dir, $case);
//    $content .= $case;
//    $menu .= <<<'EOT'
//$menu['***PROJETO***']['***NOME***'] = "abrirJanela(event, divIdRootAberto(), '', menuId('***DIR***-***COUNT***'), 'Grid: ***NOME***', '***NOME***', '/per/***DIR***/content/***CASE***/', '', 'auto', false, 'center');";
//
//EOT;
//    $menu = str_replace("***CASE***", "content" . ucfirst($nomeGrid) . "Grid", $menu);
//    $menu = str_replace("***DIR***", $dir, $menu);
//    $menu = str_replace("***NOME***", $grid["nome"], $menu);
//    $menu = str_replace("***PROJETO***", $g['projeto']["nome"], $menu);
//    $menu = str_replace("***COUNT***", $count, $menu);
//
//
//    // TAB
//    $case = <<<'EOT'
//        case '***CASE***':
//            $tabs = array();
//            $tabsList = array(
//                array(uniqid(), '/per/***DIR***/grid/***GRID***/', '***NOME***')
//            );
//            echo gProcessaTabs($tabsList, $tabs);
//            $g['smarty']->assign('tabs', $tabs);
//            $g['smarty']->display('tab.tpl');
//            break;
//
//
//EOT;
//    $case = str_replace("***CASE***", "tab" . ucfirst($nomeGrid) . "Grid", $case);
//    $case = str_replace("***GRID***", "grid" . ucfirst($nomeGrid), $case);
//    $case = str_replace("***DIR***", $dir, $case);
//    $case = str_replace("***NOME***", $grid["nome"], $case);
//    $tab .= $case;
//}
//
//$content .= <<<'EOT'
//}
//
//EOT;
//$tab .= <<<'EOT'
//}
//
//EOT;
//
//// GRID
//$gridCode = <<<'EOT'
//<?php
//namespace hardness;
//switch ($g['r'][3]) {
//
//EOT;
//
//foreach ($g['projeto']['grids'] as $grid) {
//    $nomeGrid = preg_replace("/[^a-zA-Z0-9]+/", "", gLimpaAcentosPuro($grid["nome"]));
//    $tabelaPrimaria = $grid["tabelas"][0];
//    $case = <<<'EOT'
//        case '***CASE***':
//            $grid = array(
//                array('colunaValor' => "***TABELA_PRIMARIA***_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false, ),
//
//EOT;
//
//    // Campos
//    foreach ($grid["grid"] as $campo) {
//        $gridItem = "                array(";
//        $gridItem .= "'colunaValor' => '{$campo["campo"]}', ";
//        $gridItem .= "'colunaTitulo' => '{$campo["titulo"]}', ";
//
//        foreach (explode(',', $campo["opcoes"]) as $opcao) {
//            if ($opcao == "colunaEditar") {
//                $gridItem .= "'colunaEditar' => true, ";
//            } else if ($opcao == "colunaExibirTitulo") {
//                $gridItem .= "'colunaExibirTitulo' => true, ";
//            } else if ($opcao == "colunaExibir") {
//                $gridItem .= "'colunaExibir' => false, ";
//            }
//        }
//        if ($campo["tipo"] == "Data" || $campo["tipo"] == "DataHora") {
//            $gridItem .= "'callback' => 'gCorrigeData', ";
//        }
//        $gridItem .= "),\n";
//        $case .= $gridItem;
//    }
//
//    // Extra
//    $extra = '';
//    $ultimaTabela = '';
//    foreach ($grid["tabelas"] as $key => $tabela) {
//        if (!$ultimaTabela) {
//            $ultimaTabela = $tabela;
//        } else {
//            $tabelaJoin = $grid["tabelasJoin"][$key] ? $grid["tabelasJoin"][$key] : $ultimaTabela;
//            $extra .= "\n\t LEFT JOIN {$tabela} ON {$tabela}_Id={$tabelaJoin}_{$tabela}_Id \n";
//        }
//    }
//    if (!empty($data["extra"])) {
//        $extra .= "\n" . $data["extra"];
//    }
//    $case .= <<<'EOT2'
//            );
//
//            $from = '***TABELA_PRIMARIA***';
//            $extra = <<<EOT
//***EXTRA***
//EOT;
//
//
//EOT2;
//
//    // Botões
//    $nomeForm = '';
//    if ($grid["form"]) {
//        $nomeForm = ucfirst(preg_replace("/[^a-zA-Z0-9]+/", "", gLimpaAcentosPuro($g['projeto']['forms'][$grid["form"]]['nome'])));
//        $case .= <<<'EOT2'
//            echo <<<EOT
//                <div class="buttonsBottomTop">
//                    <button type="button" id="builder_btn_incluir_***CASE***">Incluir</button>
//                </div>
//
//                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
//                    $('#builder_btn_incluir_***CASE***').unbind('click').bind('click', function(data) {
//                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir ***NOME***', '/per/***DIR***/content/***CONTENT_FORM***/', '', [550,550]);
//                    });
//                } </script></div>
//EOT;
//
//
//EOT2;
//
//    }
//
//    // Filtros
//    $case .= <<<'EOT'
//            $gridFiltro = array(
//
//EOT;
//
//    $gridFiltro = [];
//    foreach ($grid["grid"] as $item) {
//        if (in_array('filtro', explode(',', $item["opcoes"]))) {
//            $tipo = "texto";
//            switch ($item["titulo"]) {
//                case "Data":
//                case "DataHora":
//                    $tipo = "data";
//                    break;
//                case "Inteiro":
//                case "Decimal":
//                    $tipo = "numero";
//                    break;
//            }
//            $case .= "                '{$item["campo"]}' => array('titulo' => '{$item["titulo"]}', 'tipo' => '{$tipo}'),\n";
//        }
//    }
//
//    $case .= <<<'EOT'
//            );
//
//            $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
//            echo $g['ui']->gridFiltroPrint($gridFiltro);
//
//            list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
//
//
//EOT;
//
//    if ($grid['form']) {
//        $case .= <<<'EOT2'
//            echo <<<EOT
//                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
//                    var dbclickPrevent = false;
//                    $('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
//                        // Previne duplo click (abrir duas janelas)
//                        if (dbclickPrevent) { return false; }
//                        dbclickPrevent = true;
//                        setTimeout(function() { dbclickPrevent = false; }, 500);
//                        // (substituir pelo mesmo ID da chave primária do grid)
//                        var chavePrimaria = '***TABELA_PRIMARIA***_Id';
//                        if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
//                        var id = $(this).closest('tr').attr('id').split('|');
//                        var acaoId = id[0].replace(chavePrimaria + '-', '');
//                        // Ação: abrir nova janela
//                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar ***NOME***: ' + acaoId, '/per/***DIR***/content/***CONTENT_FORM***/', '&acao=alterar&acaoId=' + encodeURIComponent(acaoId), [600,300]);
//                    });
//                    $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
//                } </script></div>
//EOT;
//
//
//EOT2;
//
//    }
//
//    $case .= <<<'EOT'
//            $gridTabela = $g['ui']->grid($grid, $geradoDados);
//            $g['smarty']->assign('thead', $gridTabela['thead']);
//            $g['smarty']->assign('tbody', $gridTabela['tbody']);
//            $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
//            $g['smarty']->assign('gridId', md5($g['r_']));
//            $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
//            gBotaoAuditoria($from);
//            if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
//
//
//EOT;
//
//
//    $case .= <<<'EOT'
//            break;
//
//
//EOT;
//
//    $case = str_replace("***TABELA_PRIMARIA***", $tabelaPrimaria, $case);
//    $case = str_replace("***CASE***", "grid" . ucfirst($nomeGrid), $case);
//    $case = str_replace("***CONTENT_FORM***", "content" . $nomeForm . "Form", $case);
//    $case = str_replace("***DIR***", $dir, $case);
//    $case = str_replace("***EXTRA***", $extra, $case);
//    $case = str_replace("***NOME***", $grid["nome"], $case);
//    $gridCode .= $case;
//}
//
//$gridCode .= <<<'EOT'
//}
//
//EOT;
//
//// FORM
//// GRID
//$formCode = <<<'EOT'
//<?php
//namespace hardness;
//switch ($g['r'][3]) {
//
//EOT;
//
//foreach ($g['projeto']['forms'] as $form) {
//    $nomeForm = preg_replace("/[^a-zA-Z0-9]+/", "", gLimpaAcentosPuro($form["nome"]));
//    $case = <<<'EOT'
//        case '***CASE***':
//            $tab = (isset($r_tab)) ? $r_tab : false;
//            $acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
//
//            $formMultiTab = false;
//            if ($formMultiTab) {
//                $formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
//            } else {
//                $formId = uniqid();
//            }
//
//            $form_tabela = '***TABELA***';
//            $form_campo_pk = "***TABELA***_Id";
//            $enviar_btnId = "***CASE***BtnInsert";
//            $enviar_acaoHref = '/per/***DIR***/form/***CASE***/';
//
//            // Definição do Formulário
//            $form = new Formulario("***CASE***");
//
//            $formArray = array(
//                'campos' => array(
//                    'cadastro' => array(
//
//EOT;
//
//    foreach ($form["form"] as $campo) {
//        switch ($campo['tipo']) {
//            case "Select":
//                $case .= "                        '{$form["tabela"]}_{$campo["tabela"]}_Id' => array(";
//                $case .= "'name' => '{$form["tabela"]}_{$campo["tabela"]}_Id', ";
//                $case .= "'label' => '{$campo["titulo"]}', ";
//                $case .= "'type' => 'select', ";
//                $campos = implode(",", $campo["campo"]);
//                $case .= "'select' => gGeraSelect('{$campo["tabela"]}', '{$campo["tabela"]}_Id', '{$campos}', '', true), ";
//                break;
//            case "Moeda":
//                $case .= "                        '{$campo['campo']}' => array(";
//                $case .= "'name' => '{$campo['campo']}', ";
//                $case .= "'label' => '{$campo["titulo"]}', ";
//                $case .= "'type' => 'moeda', ";
//                break;
//            case "Data":
//                $case .= "                        '{$campo['campo']}' => array(";
//                $case .= "'name' => '{$campo['campo']}', ";
//                $case .= "'label' => '{$campo["titulo"]}', ";
//                $case .= "'type' => 'data', ";
//                break;
//            default:
//                $case .= "                        '{$campo['campo']}' => array(";
//                $case .= "'name' => '{$campo['campo']}', ";
//                $case .= "'label' => '{$campo["titulo"]}', ";
//                $case .= "'type' => 'text', ";
//                break;
//        }
//        foreach ($campo["opcoes"] as $opcao) {
//            if ($opcao["nome"] == "casasDecimais") {
//                $case .= "'casasDecimais' => '{$opcao["valor"]}', ";
//            }
//        }
//        $case .= "), \n";
//    }
//
//    $case .= <<<'EOT2'
//                    )
//                )
//            );
//
//            $form->definirFormularioArray($formArray);
//
//            if ($formCodigo = $form->processarFormulario($form_tabela)) {
//                // Form com erros
//                if ($formCodigo == 1) {
//                    echo gGeraAlertMsgErroForm($form);
//                    // Form sem erros
//                } else if ($formCodigo == 2) {
//                    // Insert ou Update?
//                    if (!empty($r_acaoId)) {
//                        // Update
//                        if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
//                            // Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
//                            //$alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
//                            echo <<<EOT
//                                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
//                                    $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
//                                        setTimeout(function() {
//                                        }, 150);
//                                    });
//                                    $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
//                                } </script></div>
//EOT;
//                        }
//                    } else {
//                        // Insert
//                        if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
//                            // Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
//                            $alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
//                            echo <<<EOT
//                                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
//                                    // Fecha a janela, recarrega o grid e abre a alteração
//                                    $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
//                                        setTimeout(function() {
//                                            {$alert}
//                                        }, 150);
//                                    });
//                                    $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
//                                    divRefresh('{$g['divIdParent']}', true);
//                                } </script></div>
//EOT;
//                        }
//                    }
//                }
//            } else {
//                // Carrega os valores caso seja um update
//                if (!empty($r_acaoId)) {
//                    $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
//                    // Mostra erros de formulário uteis ao desenvolvedor
//                    if ($processado !== true) {
//                        reset($form->formArray['campos']);
//                        if ($tab == key($form->formArray['campos'])) {
//                            echo $processado;
//                        }
//                    }
//                }
//            }
//
//            /**
//             * PARTE 4: Gerar o formulário
//             */
//            $gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);
//
//            /**
//             * PARTE 5: Exibir o formulário
//             */
//            $g['smarty']->assign('formularioID', $form->formularioID());
//            $g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
//            echo $gerado['pagina'];
//            if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
//            $g['smarty']->display('formEditor.tpl');
//
//            /**
//             * Botões
//             */
//            // Definição dos botões
//            $botoesMid = $botoesEsq = $botoesDir = '';
//            $botoesDir .= '<button type="submit" id="' . $enviar_btnId . '">Enviar</button>';
//            $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
//            $g['smarty']->assign('botoesId', $g['divId']);
//            $g['smarty']->assign('botoesEsq', $botoesEsq);
//            $g['smarty']->assign('botoesMid', $botoesMid);
//            $g['smarty']->assign('botoesDir', $botoesDir);
//            $g['smarty']->display('buttons-floatBottom.tpl');
//
//
//EOT2;
//
//    $case .= <<<'EOT'
//            break;
//
//
//EOT;
//
//    $case = str_replace("***CASE***", "form" . ucfirst($nomeForm), $case);
//    $case = str_replace("***DIR***", $dir, $case);
//    $case = str_replace("***TABELA***", $form["tabela"], $case);
//    $formCode .= $case;
//}
//
//$formCode .= <<<'EOT'
//}
//
//EOT;
//
//mkdir("{$g['pathDadosProg']}/{$dir}");
//file_put_contents("{$g['pathDadosProg']}/{$dir}/content.php", $content);
//file_put_contents("{$g['pathDadosProg']}/{$dir}/tab.php", $tab);
//file_put_contents("{$g['pathDadosProg']}/{$dir}/grid.php", $gridCode);
//file_put_contents("{$g['pathDadosProg']}/{$dir}/form.php", $formCode);
//
//if ($_SESSION['modoDesenvolvedor']) {
//    mkdir("ide/personalizacaoConf");
//    $arquivoMenu = "ide/personalizacaoConf/GLOBAL-menuBuilder.php";
//} else {
//    mkdir("{$g['pathDados']}/personalizacaoConf");
//    $arquivoMenu = "{$g['pathDados']}/personalizacaoConf/GLOBAL-menuBuilder.php";
//}
//if (!file_exists($arquivoMenu)) {
//    $tmp = <<<'DEFAULT'
//<?php
//namespace hardness;
///**
//* Alterações do menu, os itens aqui são incluídos automaticamente pelo Fast Crud
//*/
//
//DEFAULT;
//    file_put_contents($arquivoMenu, $tmp);
//}
//file_put_contents($arquivoMenu, $menu, FILE_APPEND);
//
//echo json_encode([
//    "erro" => false,
//    "dir" => $dir
//]);
//die();
