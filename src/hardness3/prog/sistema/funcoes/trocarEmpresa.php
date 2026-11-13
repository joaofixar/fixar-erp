<?php
namespace hardness;
set_time_limit(0);

// vars
$vars = '';
foreach ($_REQUEST as $key => $value) {
    if ($key == 'r' || $key == 'ajax' || $key == 'divId' || $key == 'divIdRoot' || $key == 'divIdParent' || is_array($value)) { continue; }
    $vars .= "&{$key}=" . urlencode($value);
}

switch ($g['r'][3]) {
    /* GRID PRINCIPAL */

    case 'content':
        /*
        * Divisão da Area em Conteúdos
        */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/sistema/funcoes/trocarEmpresa/tab/', '&tabela=' . $r_tabela . '&divRefresh=' . urlencode($r_divRefresh)),
        );
        echo gProcessaAcoes($actions);
    
        /**
        * Botões
        */
        // Definição dos botões
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="TrocarEmpresaSelecionarEmpresa_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'tab':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);
        
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/sistema/funcoes/trocarEmpresa/form/', 'Empresas', '&tab=geral&tabela=' . $r_tabela . '&divRefresh=' . urlencode($r_divRefresh)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
     break;

     case 'form':

         /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($_REQUEST['acaoId'])) ? $_REQUEST['acaoId'] : false;
    
        $mudarFilial = (isset($r_mudarFilial)) ? $r_mudarFilial : false;
        $gerarNF = (isset($r_gerarNF)) ? $r_gerarNF : false;

        /**
         * PARTE 2: Definição do formulário
         */
        // Este mesmo form será exibido em multiplas tabs ou não?
        $formMultiTab = false;
        if ($formMultiTab) {
            $formId = (isset($_REQUEST['formId'])) ? $_REQUEST['formId'] : false;
        } else {
            $formId = uniqid();
        }

        // A qual tabela este form está relacionada, e a chave primária da tabela
        $form_tabela = '';
        $form_campo_pk = '';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'TrocarEmpresaSelecionarEmpresa_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/sistema/funcoes/trocarEmpresa/form/';

        $select = gGeraSelect('C004','C004_Id','C004_Nome_Abreviado, C004_Cnpj'," WHERE C004_Flag_Ativo='S'", false);
        $first = true;
        foreach ($select as &$item) {
            if ($item['value'] == $g['empresaAtual']) {
                $item['disabled'] = true;
            } else if ($first) {
                $item['selected'] = true;
                $first = false;
            }
        }

        // novo formulário
        $form = new Formulario('TrocarEmpresaFormSelecionarEmpresa');
        // Definição do Formulário (veja documentação dentro da classe Formulario para ver todas as opções disponiveis)
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'C004_Id_Novo' => array('name' => 'C004_Id_Novo','label'=>'Empresa', 'type' => 'select','extra' => 'style="width:450px;"' ,
                        'select' => $select
                    )
                )
            )
        );

        $form->definirFormularioArray($formArray);

        /**
         * PARTE 3: Processamento do formulário (se foi enviado)
         *            E carregamento dos valores (se não foi enviado e possui acaoId)
         */
        if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
                // Form sem erros
            } else if ($formCodigo == 2) {
                $C004_Id_Novo = $form->campoValorEnviado('C004_Id_Novo');
                $Tabela = $r_tabela;
                $Id = $r_id;

                $varVen  = ($Tabela == 'T005') ?  'ven002' : (($Tabela == 'T003') ? 'ven001' : (($Tabela == 'T218') ? 'est015' : 'est016'));
                $varVen1 = ($varVen == 'est015' || $varVen == 'est016') ? 'est' : 'ven';

                $g['conf']->carregaArquivoConf($Tabela . '_Id', 'trocarEmpresa');
            
                if ($Tabela == 'T005'){

                    require_once("bibliotecas/classes/VEN002.php");
                    $VEN002 = new VEN002();
                    $retorno = $VEN002 -> VEN002MudarEmpresaPedido($Id, 0, $C004_Id_Novo);
                    if ($retorno !== true ) {
                        // gGeraAlertMsg('ERRO', $retorno);
                        echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                            $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                            // $("#{$g['divIdParent']}").closest('.ui-dialog-content').dialog('close');
                            dialogConfirm('Erro!', '{$retorno}');
                        } </script></div>
EOT;
                    } else {
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                                $("#{$g['divIdParent']}").closest('.ui-dialog-content').dialog('close');
                                divRefresh('$r_divRefresh');
                                dialogConfirm('Sucesso!', "Troca de empresa do pedido $Id realizada.");
                            } </script></div>
EOT;
                    }
                        
                } else {

                    // Troca os C004 da tabela e tabelas filhos
                    $retorno =  gMudarC004($Tabela, $Id, $C004_Id_Novo);

                    if ($retorno !== true){
                        $retorno = htmlspecialchars($retorno);
                        echo <<<EOT
                            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                dialogConfirm('Erro!', "{$retorno}");
                            } </script></div>
EOT;
                        die();
                    }

                    // Fim
                    if ($g['conf']->conf['trocarEmpresa']['executarNoFinal']) {
                        eval("namespace hardness;\n{$g['conf']->conf['trocarEmpresa']['executarNoFinal']}");
                    }

                    echo <<<EOT
                        <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

                            $('#{$g['divId']}').showLoading();
                            idProgressBar1 = dialogAlert('Trocando de Filial',"<span id='progressBar1'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
                            AtualizarAliquotasPedidosCFOP();

                            function AtualizarAliquotasPedidosCFOP(){
                                    $.getJSON('/$varVen1/$varVen/form_func-ajax/atualizarCFOP/?ajax=true&{$Tabela}_Id=' + encodeURIComponent("{$Id}") + '&callback=?', function(request) {
                                        if (request.code) {
                                            T00X = $Id;
                                            $('#dc-' + idProgressBar1).dialog("destroy").remove(); 
                                            idProgressBar2 = dialogAlert('Atualizando CFOP e alíquotas',"<span id='progressBar2'>Processando 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />");
                                            AtualizarAliquotasPedidosCFOPLoop(idProgressBar2,T00X,1); 
                                        } else {
                                            $('#{$g['divId']}').hideLoading();
                                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                                            dialogConfirm('Erro', request.data, \$buttons);
                                        }
                                        $('#{$g['divId']}').hideLoading();
                                    });
                            }

                            function AtualizarAliquotasPedidosCFOPLoop(idProgressBar2,T00X, inicio){
                                    $.getJSON('/$varVen1/$varVen/form_func-ajax/atualizarAliquota/?ajax=true&{$Tabela}_Id=' + encodeURIComponent("{$Id}")  + '&inicio=' + inicio + '&callback=?', function(request) {
                                        if (request.concluido == 0) {
                                            $('#progressBar2').html('<b>Processado '+ request.percentual + '%</b>' + '</b><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                            AtualizarAliquotasPedidosCFOPLoop(idProgressBar2,T00X,0);
                                        }else{
                                            $('#dc-' + idProgressBar2).dialog("destroy").remove();
                                            
                                            dialogConfirm('Transferido para nova empresa com sucesso!','<b>CFOP e Alíquotas atualizadas<br/><br/><b>Processado '+ request.percentual + '%</b>' + '</b><br/><br/>Produtos atualizados: <b>' + request.ok + '</b><br/>Produtos com erro: <b> ' + request.erro + '</b><br /><br/><b> <font color="red">' + request.erros + '</font></b>');
                                            divRefresh('{$g['divIdParent']}', true);

                                            if (request.code) {
                                                $(this).dialog("destroy").remove();     
                                            } else {
                                                $(this).dialog("destroy").remove(); 
                                                dialogConfirm('Erro!', request.data);
                                            }
                                        } 
                                    });
                            }

                            $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                            $("#{$g['divIdParent']}").closest('.ui-dialog-content').dialog('close');

                        } </script></div>
EOT;
                }

            }
        }

        /**
        * PARTE 4: Gerar o formulário
        */
        $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

        /**
         * PARTE 5: Exibir o formulário
         */
        $g['smarty']->assign('formularioID', $form->formularioID());
        $g['smarty']->assign('table', $gerado['table']); $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) { $g["smarty"]->assign("formId", $formId); }
        $g['smarty']->display('form.tpl');
     break;
}
