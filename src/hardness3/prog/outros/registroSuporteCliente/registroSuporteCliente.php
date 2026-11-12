<?php
namespace hardness;

/**
 * Atualiza o arquivo de filtros com base na busca
 */
require_once 'bibliotecas/funcoesCallback/statusDesenvolvimento.php';
require_once 'prog/outros/registroSuporteCliente/grid_func-callback.php';

/**
 * Ações
 */
switch ($g['r'][3]) {
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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tab/'),
        );
        echo gProcessaAcoes($actions);

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').parent().find('ui-widget-content').css('background', 'background: #444444 url(/hardness3/static/css/temas/padrao/images/ui-bg_inset-hard_55_ffeb80_1x100.png) 50% 50% repeat-x;');
			} </script></div>
EOT;
    break;

    case 'tab':
        global $sys;
        $nomeSuporte = explode(" ",$sys['nomeEmpresa']);
        $nomeSuporte = $nomeSuporte[0];

        // Definição das Tabs
        $tabs = array();
        $tabsList = array();

        if($g['C031']['usarSuporteInterno']=="S"){
            $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/contentSuporteCliente/', 'Suporte Interno');
            $tabsList[] = array(uniqid(), '/outros/registroSuporte/registroSuporte/contentSuporteTecnico/', 'Suporte Técnico');
        }else{
            $tabsList[] = array(uniqid(), '/outros/registroSuporte/registroSuporte/contentSuporteCliente/', 'Suporte Técnico');
        }

        if($sys['dbDatabase']==$confUsuario['dbDatabase']){
            $tabsList[]=array(uniqid(), '/outros/registroSuporte/registroSuporte/content/', 'Suporte Clientes');
        }

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'contentConfiguracaoSuporte':
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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabConfiguracaoSuporte/'),
        );
        echo gProcessaAcoes($actions);

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').parent().find('ui-widget-content').css('background', 'background: #444444 url(/hardness3/static/css/temas/padrao/images/ui-bg_inset-hard_55_ffeb80_1x100.png) 50% 50% repeat-x;');
			} </script></div>
EOT;
    break;

    case 'contentSuporteCliente':
        if (!empty($r_screenshot)) {
            $r_screenshot = urldecode($r_screenshot);
            echo <<<EOT
				<script>
					var canvasImg = '{$r_screenshot}';
				</script>
EOT;
        }
        /*
         * Divisão da Area em Conteúdos
         */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)

        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('content1Height', '45px;');
        $content2 = uniqid();
        $g['smarty']->assign('content2', $content2);
        echo $g['smarty']->fetch('content-2rows.tpl');
        // $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/cabecalhoRegistroSuporteClienteCliente/', '&acaoId=' . urlencode($r_acaoId)),
            array($content2, '/outros/registroSuporteCliente/registroSuporteCliente/tabSuporteCliente/', '&divContentId=' . urlencode($g['divId'])),
        );
        echo gProcessaAcoes($actions);

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']}').parent().find('ui-widget-content').css('background', 'background: #444444 url(/hardness3/static/css/temas/padrao/images/ui-bg_inset-hard_55_ffeb80_1x100.png) 50% 50% repeat-x;');
			} </script></div>
EOT;
    break;

    case 'contentAtualizarPacote':

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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/formAtualizarPacoteMelhorias/', '&tab=geral'),
        );
        echo gProcessaAcoes($actions);

        /**
         * Botões
         */
        // Definição dos botões
        $botoesMid = '';
        $botoesEsq = '';
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="btnAtualizarPacoteMelhoria">Atualizar</button>';
        $botoesEsq .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesMid', $botoesMid);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        // Ações dos botões
        $novoIdTelaForm = uniqid();
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#btnAtualizarPacoteMelhoria').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Sim": function() {
						var D166_Id = $('#{$g['divId']} #D166_Id').val();

						$('#{$g['divId']}').showLoading();
						$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/atualizarPacoteMelhorias/?ajax=true&D166_Id=' + D166_Id + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Ok', request.data, \$buttons);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
					    		dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#{$g['divId']}').hideLoading();
							fecharJanela('{$g['divId']}');
							divRefresh('{$g['divIdParent']}');
						});
						$(this).dialog("destroy").remove();
					},
					"Não": function() { $(this).dialog("destroy").remove(); }
				};
				dialogConfirm('Atualizar pacote', 'Atualizar pacote do quadro selecionado? ', \$buttons);
			});
			} </script></div>
EOT;

    break;

    case 'contentSelecionarPacote':

        /*
         * Divisão da Area em Conteúdos
         */
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;

        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabSelecionarPacote/', '&T234_Id=' . urlencode($T234_Id)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'formAtualizarPacoteMelhorias':

        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($r_tab)) ? $r_tab : false;
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
        // A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
        $form_tabela = 'D166';
        $form_campo_pk = 'D166_Id';
        // ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = '';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '';

        //form editor
        $form_editor = array('geral' => '{"livre":{"top":7,"left":5,"width":null,"hidden":0},"D166_Id":{"top":99,"left":15.6875,"width":191,"hidden":0}}');

        // Definição do Formulário
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'livre' => array('name' => 'linha', 'type' => 'comentario', 'value' => 'Atenção:<br><br> Lembre-se de sincronizar as melhorias e conferir os chamados antes de atualizar o pacote.', 'label' => '', 'reject' => true, 'style' => 'font-weight:bold; color: #FF0000; font-size: 14px;'),
                    'D166_Id' => array('name' => 'D166_Id', 'label' => 'Quadros', 'type' => 'select',
                        'select' => gGeraSelect('D166', 'D166_Id', 'D166_Grupo_Suporte', "WHERE D166_Flag_Melhoria='S'", true),
                    ),
                ),
            ),
        );

        $form = new Formulario('formAtualizarPacoteMelhorias');
        $form->definirFormularioArray($formArray);
        /**
         * PARTE 4: Gerar o formulário
         */
        $gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

        /**
         * PARTE 5: Exibir o formulário
         */
        $g['smarty']->assign('formularioID', $form->formularioID());
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

    break;

    case 'formSelecionarPacote':

        global $g;
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($r_tab)) ? $r_tab : false;
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
        // A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
        $form_tabela = 'T234';
        $form_campo_pk = 'T234_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'formSelecionarPacote_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formSelecionarPacote/';

        //form editor
        $form_editor = array('geral' => '{"introducao":{"top":-3,"left":28,"width":null,"hidden":0},"observacao":{"top":7,"left":5,"width":null,"hidden":1},"texto":{"top":67,"left":25,"width":null,"hidden":0},"textoSelecaoMes":{"top":181,"left":31,"width":null,"hidden":0},"atendimentoMelhoria":{"top":191,"left":400,"width":370,"hidden":0},"atencao":{"top":256,"left":20,"width":null,"hidden":0}}');

        $T135 = mysql_query("SELECT T135_Qtde_Maxima_Melhorias_Mes,
                                    T135_Qtde_Maxima_Chamados_Duvida,
		                            T135_Valor_Hora_Tecnica,
		                            CONCAT((DATE_FORMAT(T135_Franquia_Horas, '%H:%i')), ' horas')AS horasFranquia
			                   FROM T135
			              LEFT JOIN D024 ON T135_D024_Id=D024_Id
			                  WHERE T135_D024_Id='{$r_D024_Id}'
			                    AND T135_Flag_Cancelado != 'S'
			                    AND T135_Tipo='M'");
        $mT135 = mysql_fetch_array($T135);

        $maximoChamadosAtendimento = $mT135['T135_Qtde_Maxima_Melhorias_Mes']+$mT135['T135_Qtde_Maxima_Chamados_Duvida'];


        $introducao = "<p style='margin-bottom:20px; line-height:2.1'><span style='font-size:15px;'><b>Olá!</b></span><br><b>Nesta tela você pode abrir chamado de sugestão de melhoria. Esse chamado pode ser enviado imediatamente para fila de atendimento ou apenas registrar como chamado em aberto.</b><br></p>";

        $texto = "
        <br>
		<p style='margin-bottom:20px;'><br>
			• Você pode enviar até <b><font color='#3366ff'>{$maximoChamadosAtendimento}</font></b> chamados de melhoria/dúvida para fila de atendimento por vez.</font></br>
		</p>
		<p style='margin-bottom:20px;'>
			• Para maiores informações sobre as regras de abertura de chamados acesse: <a href ='https://drive.google.com/file/d/1Mg9_C9xeUqV45QiDAlHRPkSZxoifiuD0/view?usp=sharing' target='_blank'><font color='#3366ff'><b>Regras de abertura de chamados.pdf</b></font></a>
		</p>";

        $atencao = "<p style='margin-bottom:20px; padding:10px;  background-color:#f9f7bc;border-radius: 5px; line-height:1.5;'>
            <b>ATENÇÃO!</b><br>
			• Selecionando a opção <b>APENAS REGISTRAR</b> o seu chamado será apenas salvo na base de dados. Caso deseje que essa<br> melhoria seja analisada, realoque-o para atendimento (Você pode recuperar esse chamado na aba:<br><b>'Chamados em Aberto'</b> na tela anterior).
		</p>";

        $textoSelecaoMes = "<p><b>Selecione uma das opções para abertura de seu chamado:</b></p>";

        // Definição do Formulário
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'introducao' => array('name' => 'introducao', 'type' => 'comentario', 'value' => $introducao, 'label' => '', 'reject' => true, 'style' => 'font-size: 13px;'),
                    'texto' => array('name' => 'texto', 'type' => 'comentario', 'value' => $texto, 'label' => '', 'reject' => true, 'style' => 'font-size: 13px;'),
                    'textoSelecaoMes' => array('name' => 'textoSelecaoMes', 'type' => 'comentario', 'value' => $textoSelecaoMes, 'label' => '', 'reject' => true, 'style' => 'font-size: 13px;'),
                    'observacao' => array('name' => 'observacao', 'type' => 'comentario', 'value' => $observacao, 'label' => '', 'reject' => true, 'style' => 'font-size: 13px;'),
                    'atencao' => array('name' => 'atencao', 'type' => 'comentario', 'value' => $atencao, 'label' => '', 'reject' => true, 'style' => 'font-size: 13px;'),
                    'atendimentoMelhoria' => array('name' => 'atendimentoMelhoria', 'label' => '','reject' => true, 'type' => 'select', 'select' =>
						array(
							array('title' => 'APENAS REGISTRAR (não quero desenvolver ainda)', 'value' => 'B'),
							array('title' => 'ENVIAR FILA ATENDIMENTO', 'value' => 'A'),
						)	
					),
                ),
            ),
        );

        $form = new Formulario('formSelecionarPacote');
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
                // Insert ou Update?
                if (!empty($acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        //echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										divRefresh('{$g['divId']}');
									}, 150);
									});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        //Verifica a quantidade de chamados de melhoria que o cliente possue
                        $atendimentoMelhoria = $form->campoValorEnviado('atendimentoMelhoria');
                        if ($atendimentoMelhoria=="A") {
                            $tipoSuporte = "M";
                            $chamados = mysql_query("SELECT COUNT(T234_Id) as QtdeChamados,GROUP_CONCAT(' ',T234_Id) as Chamados
                                                        FROM T234
                                                        LEFT JOIN D166 ON T234_D166_Id=D166_Id
                                                        WHERE T234_D024_Id='{$r_D024_Id}'
                                                        AND (T234_Flag_Tipo_OS='M' OR T234_Flag_Tipo_OS='D')
                                                        AND T234_Flag_Finalizado != 'S'");

                            $mqtdChamados = mysql_fetch_array($chamados);
                            log("chamados abertos:".$mqtdChamados['QtdeChamados']);
                            log("chamados permitidos:".$maximoChamadosAtendimento);

                            if ($mqtdChamados['QtdeChamados'] >= $maximoChamadosAtendimento) {
                                $alert = gGeraAlertMsg('Erro', "<font color='red'>Você <b>excedeu</b> a quantidade de <b>{$maximoChamadosAtendimento}</b> chamados permitidos de <b>MELHORIA/DÚVIDA</b> em <b>FILA DE ATENDIMENTO</b>.
                                <br>Por favor, aguarde um de seus chamados ser concluído para selecionar novos chamados para atendimento.<br><br>Você possui os seguintes chamados pendentes:<br><b>{$mqtdChamados['Chamados']}</b></font>", false);
                                echo <<<EOT
                                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                    {$alert};
                                    fecharJanela('{$g['divId']}');
                                } </script></div>
EOT;
                            } else {
                                echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
											setTimeout(function() {
												abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', "Requisição de Suporte", '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=' + encodeURIComponent('{$tipoSuporte}')  + '&D024_Id=' + encodeURIComponent('{$r_D024_Id}')  +'&pacote='+encodeURIComponent('{$T234_T234_Id}') + '&divIdRefresh=' + encodeURIComponent('{$g['divId']}') + '{$suporteNota}', [825, 600]);
											}, 150);
										});
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										divRefresh('{$g['divIdParent']}', true);
									} </script></div>
EOT;
                            }
                        } else {
                            $tipoSuporte = "B";

                            echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', "Requisição de Suporte", '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=' + encodeURIComponent('{$tipoSuporte}')  + '&D024_Id=' + encodeURIComponent('{$r_D024_Id}')  +'&pacote='+encodeURIComponent('{$T234_T234_Id}') + '&divIdRefresh=' + encodeURIComponent('{$g['divId']}') + '{$suporteNota}', [825, 600]);
										}, 150);
									});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
								} </script></div>
EOT;
                        }
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
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
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="formSelecionarPacote_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
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
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/grid/', 'Ligações'),
            array(uniqid(), '/sistema/funcoes/queueNF/tempo-real/', 'NF-e'),
            // array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/contentDesenvolvimento/', 'Desenvolvimento'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteTriagem/', 'Triagem'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteArquivo/', 'Arquivos'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuportePacote/', 'Pacote atualização'),
            //array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteGrupo/', 'Quadros'),
            //array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteErro/', 'Erros'),
            //array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteDuvida/', 'Dúvidas'),
            //array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteMelhoria/', 'Melhorias'),
            //array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoTeste/', 'Teste', '&exibirOpcoesDev=' . false),
            //array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteFinalizado/', 'Finalizados'),
            //array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridImplementacoes/', 'Registro Implementações'),
            //array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridAtividadesUsuarios/', 'Atividade dos Usuários'),
            //array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/contentEmail/', 'Email'),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabSelecionarPacote':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/formSelecionarPacote/', 'Selecionar Mês de Desenvolvimento', '&tab=geral&acaoId=' . urlencode($acaoId) . '&T234_Id=' . urlencode($T234_Id)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabConfiguracaoSuporte':
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteGrupo/', 'Quadros'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteLista/', 'Listas'),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabSuporteClienteAberto':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteClienteAberto/', 'Melhorias', "&flagTipoOS=M"),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteClienteAberto/', 'Dúvidas', "&flagTipoOS=D"),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteClienteAberto/', 'Erros', "&flagTipoOS=E"),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'grid':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T234_Id', $g['divId'])),
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'Finalizado?', 'colunaEditar' => true, 'callback' => 'validaCampoFlagFinalizado', 'colunaEditarJSCallback' => "JSCallbackFlagFinalizado",
                'colunaEditarGeraSelect' => array(
                    array('title' => 'Sim', 'value' => 'S'),
                    array('title' => 'Não', 'value' => 'N'),
                ),
            ),
            array('colunaValor' => 'T234_Data_Hora_Finalizado', 'colunaTitulo' => 'Data finalizado', 'callback' => 'gCorrigeData'),
            array('colunaValor' => "(SELECT TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado)),'%H:%i:%s') FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id) as Duracao", 'colunaTitulo' => 'Duração'),
            array('colunaValor' => 'C007_Nome', 'colunaTitulo' => 'Usuário'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'D046_Nome', 'colunaTitulo' => 'Grupo Cadastro'),
            array('colunaValor' => 'T235_Descricao', 'colunaTitulo' => 'Grupo Suporte'),
            array('colunaValor' => 'T234_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'Acessar Local', 'colunaTitulo' => 'Local', 'callback' => 'validaCampoAcessoLocal', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Observacao_Finalizado', 'colunaTitulo' => 'Obs'),
            array('colunaValor' => 'C025_Banco_Dados', 'colunaTitulo' => 'C025_Banco_Dados'),
            array('colunaValor' => 'C025_Senha', 'colunaTitulo' => 'C025_Banco_Dados'),
            array('colunaValor' => 'D024_Link', 'colunaTitulo' => 'D024_Link'),
            array('colunaValor' => 'T234_T236_Id', 'colunaTitulo' => 'Implementação'),
            array('colunaValor' => 'D013_Nome_Contato', 'colunaTitulo' => 'Solicitante'),
            array('colunaValor' => '', 'colunaTitulo' => 'Chamado', 'colunaTipo' => 'livre', 'callback' => 'validaCampoChamado'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
		left join D024 on D024_Id=T234_D024_Id
		left join C007 on C007_Id=T234_C007_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T235 on T235_Id=T234_T235_Id
		left join C025 on C025_D024_Id=D024_Id
		left join D013 ON D013_Id=T234_D013_Id
		WHERE T234_Flag_Tipo_Suporte = 'T'
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'D046_Nome' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'T235_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'Sim', 'value' => 'S'),
                array('title' => 'Não', 'value' => 'N'),
            ), 'naoGerarExtra' => false),
            'T234_Data_Hora_Finalizado' => array('titulo' => 'Data finalizado', 'tipo' => 'data'),
            'T234_Observacao_Finalizado' => array('titulo' => 'Obs finalizado', 'tipo' => 'texto'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            if ($array['todosCampos']['T234_Flag_Finalizado'] == 'N') {
                return 'linhaVermelho';
            }
        }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
         * Botões (Release Acima do GRID)
         */
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="registroSuporteClienteBtnIncluir">Incluir <b>(F9)</b></button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#registroSuporteClienteBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluir/','', [600,500]);
				});

				function JSCallbackFlagFinalizado(T234_Id, campoEditado, valorAntigo, novoValor) {
					T234_Id = T234_Id.replace(/T234_Id-/, '');
						if(campoEditado == 'T234_Flag_Finalizado'){
							$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/updateFlagFinalizado/?ajax=true&T234_Id='    + encodeURIComponent(T234_Id) +
																						      '&campoEditado='		+ encodeURIComponent(campoEditado) +
																						      '&valorAntigo='		+ encodeURIComponent(valorAntigo) +
																						      '&novoValor='			+ encodeURIComponent(novoValor) +
																						      '&callback=?', function(request) {
								if (request.code) {
									divRefresh('{$g['divId']}', true, false, false, T234_Id, 'T234_Id'); // grid
									if(novoValor == 'S'){
										abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluir/', '&acaoId=' + encodeURIComponent(T234_Id) + '&tabela={$from}', [615,360]);
									}
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
						}
				}
			} </script></div>
EOT;
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
        $valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<span class="item" style="text-align:right">Duração Total<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorDuracao}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluir/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [615,360]);
			});
		} </script></div>
EOT;
        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuporteClienteAberto':
        //Caso o acesso esteja sendo realizado pelo sistema de nota
        if (!empty($r_noEmp) && !empty($r_p)) {
            $suporteNota = "&noEmp=true&p=$r_p&u=$r_u";
        }

        $C004 = mysql_query("select C004_Nome_Empresa from C004");
        $mC004 = mysql_fetch_array($C004);

        $flagTipoOS = $r_flagTipoOS;

        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_C007_Id_Cliente', 'colunaTitulo' => 'T234_C007_Id_Cliente', 'colunaExibir' => false),
            array('colunaValor' => 'T252_Id', 'colunaTitulo' => 'T252_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T252_Flag_Lido', 'colunaTitulo' => 'T252_Flag_Lido', 'colunaExibir' => false),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            // array('colunaValor' => "CONCAT(T272_Mes, '/', T272_Ano)", 'colunaTitulo' => 'Pacote', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
            // array('colunaValor' => 'T234_Prioridade_Cliente', 'colunaTitulo' => 'Prioridade', 'callback' => 'prioridadeChamado', 'colunaEditar' => ($g['C007_Local']['C007_Flag_Vendedor'] == 'D') ? true : false, 'colunaEditarTodos' => ($g['C007_Local']['C007_Flag_Vendedor'] == 'D') ? true : false, 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'colunaEditarJSCallback' => 'callbackEditarPrioridadeCliente'),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
            // array('colunaValor' => 'T234_Data_Hora_Inicio_Estimado', 'colunaTitulo' => 'Previsão', 'style' => 'width: 1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => '(SELECT CONCAT(GROUP_CONCAT(DISTINCT T252_Usuario ORDER BY T252_Data_Hora ASC SEPARATOR ", "),IF(COUNT(*) > 1, CONCAT(" (",COUNT(*),")"),"")) FROM T252 WHERE T252_T234_Id = T234_Id)', 'colunaTitulo' => 'Interações', 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
        );
        /**
         * Definição da SQL
         */
        $from = "T234";

        if (!empty($flagTipoOS)) {
            $filtroTipoOS = "AND T234_Flag_Tipo_OS = '$flagTipoOS'";
        }

        $extra = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
            LEFT JOIN C007 AS usuario on usuario.C007_Id = T234_C007_Id_Cliente
            LEFT JOIN C007 AS supervisor on supervisor.C007_Id = usuario.C007_C007_Id
            LEFT JOIN C007 AS supervisor2 on supervisor2.C007_Id = usuario.C007_C007_Id_2
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
			LEFT JOIN T272 ON T272_Id = T234_T272_Id
			WHERE D024_Id = '{$r_D024_Id}'
            AND T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Finalizado != 'S'
            AND (IFNULL(T234_T234_Vinculo_Id, 0) <= 0)
			AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V',usuario.C007_Id = '{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S',(supervisor.C007_Id = '{$g['usuarioAtual']}' OR supervisor2.C007_Id = '{$g['usuarioAtual']}' OR usuario.C007_Id = '{$g['usuarioAtual']}'),'1=1'))
			GROUP BY T234_Id
			ORDER BY IF(IFNULL(T252_Id,0) > 0, T252_Data_Hora, T234_Data_Hora_Abertura) DESC
EOT;
        $extraTotal = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
            LEFT JOIN C007 AS usuario on usuario.C007_Id = T234_C007_Id_Cliente
            LEFT JOIN C007 AS supervisor on supervisor.C007_Id = usuario.C007_C007_Id
            LEFT JOIN C007 AS supervisor2 on supervisor2.C007_Id = usuario.C007_C007_Id_2
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
			WHERE D024_Id = '{$r_D024_Id}'
            AND T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Finalizado != 'S'
            AND (
                IFNULL(T234_T234_Vinculo_Id, 0) <= 0
              )
			AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V',usuario.C007_Id = '{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S',(supervisor.C007_Id = '{$g['usuarioAtual']}' OR supervisor2.C007_Id = '{$g['usuarioAtual']}' OR usuario.C007_Id = '{$g['usuarioAtual']}'),'1=1'))
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
            'T252_Usuario' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'T252_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'T234_Data_Hora_Abertura' => array('titulo' => 'Data chamado', 'tipo' => 'data'),
            'T252_Data_Hora' => array('titulo' => 'Data interação', 'tipo' => 'data'),
            'T234_Priridade_Cliente' => array('titulo' => 'Prioridade', 'tipo' => 'numero'),
            'T234_Flag_Finalizado' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'PENDENTE', 'value' => 'N'),
                array('title' => 'CONCLUÍDO', 'value' => 'O'),
                array('title' => 'ATENDIMENTO', 'value' => 'A'),
            ), 'naoGerarExtra' => false),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'MELHORIA', 'value' => 'M'),
                array('title' => 'DUVIDA', 'value' => 'D'),
                array('title' => 'ERRO', 'value' => 'E'),
            ), 'naoGerarExtra' => false),
            'T272_Mes' => array('titulo' => 'Mês Pacote', 'tipo' => 'numero'),
            'T272_Ano' => array('titulo' => 'Ano Pacote', 'tipo' => 'numero'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            /*if($g['usuarioAtual'] == $array['todosCampos']['T234_C007_Id_Cliente'] && $array['todosCampos']['T252_Flag_Lido'] == 'N'){
            mysql_query("UPDATE T252 SET T252_Flag_Lido = 'S' WHERE T252_T234_Id = '{$array['todosCampos']['T234_Id']}'");
            }*/
            if ($array['todosCampos']['T234_Flag_Finalizado'] == 'S') {
                return 'linhaCinza';
            }
        }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        $extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);

        /**
         * Botões (Release Acima do GRID)
         */

        echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<button type="button" id="btnIncluirSuporteCliente" style="margin-top: 3px;">Incluir Chamado</button>-->
				<!--<a href="http://download.teamviewer.com/download/TeamViewerQS_pt.exe" target="_blank"><button>Baixar TeamViewer</button></a>-->
				<!--<button type="button" id="btnFilaAtendimentoCliente" style="margin-top: 3px;">Fila Atendimento</button>-->
				<!--<span style="background-color: #FFFFAD; margin: 3px; border-radius: 2px; font-size: 12px; width: 685px; padding:2px 2px 2px 2px; border:1px solid silver">Chamados CONCLUÍDOS ficarão abertos por 48 horas, caso não hajam novas interações serão finalizados automaticamente</spam>-->
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#btnIncluirSuporteCliente').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Informar erro": function() {
							abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Requisição de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=E&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&divIdRefresh=' + encodeURIComponent('{$g['divId']}') + '{$suporteNota}', [825, 600]);
							$(this).dialog("destroy").remove();
						},
						"Tirar dúvida": function() {
							abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Requisição de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=D&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&divIdRefresh=' + encodeURIComponent('{$g['divId']}') + '{$suporteNota}', [825, 600]);
							$(this).dialog("destroy").remove();
						},
						"Sugerir melhoria": function() {
							abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Requisição de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=M&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&divIdRefresh=' + encodeURIComponent('{$g['divId']}') + '{$suporteNota}', [825, 600]);
							$(this).dialog("destroy").remove();
						},
						"Cancelar": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Suporte', 'Selecione o motivo do suporte:', \$buttons);
				});

				function callbackEditarPrioridadeCliente(T234_Id, campoEditado, valorAntigo, novaOrdem) {
					T234_Id = T234_Id.replace(/T234_Id-/, '');
					$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/processarPrioridadeCliente/?ajax=true&T234_Id=' + encodeURIComponent(T234_Id) + '&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&novaOrdem=' + encodeURIComponent(novaOrdem) + '$suporteNota&callback=?', function(request) {
					});
				}

				$('#btnFilaAtendimentoCliente').unbind('click').bind('click', function(data) {
					abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Fila Atendimento', '/outros/registroSuporteCliente/registroSuporteCliente/contentFilaAtendimentoCliente/', '&acaoId=' + encodeURIComponent('{$r_D024_Id}'), [1000,600]);
				});
			} </script></div>
EOT;
        /*                        if (request.code) {
        divRefresh('{$g['divId']}', true);
        }
         */

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(DISTINCT T234_Id)", $from, $extraTotal, false, false);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Chamados<br><input type="text" value="{$totalRegistros}" class="whiteBg" id="" size="11" readonly></span>
				<div style="clear:both;"></div>
			</div>
EOT;
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        //$('#{$g['divId']} .grid th').removeAttr('onclick').css({'cursor': 'auto'}); #Retira acoes do titulo
        $novoIdTelaForm = uniqid();
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '$suporteNota&tabela={$from}', [800, 600]);
			});
		} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuporteClienteFinalizado':
        //Caso o acesso esteja sendo realizado pelo sistema de nota
        if (!empty($r_noEmp) && !empty($r_p)) {
            $suporteNota = "&noEmp=true&p=$r_p&u=$r_u";
        }
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Solicitante', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaExibir' => false),
            array('colunaValor' => 'T234_D024_Id', 'colunaExibir' => false),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Descricao', 'colunaTitulo' => 'Descrição', 'callback' => 'campoMensagensT234'),
            //array('colunaValor' => '(SELECT CONCAT(GROUP_CONCAT(DISTINCT T252_Usuario ORDER BY T252_Data_Hora ASC SEPARATOR ", "),IF(COUNT(*) > 1, CONCAT(" (",COUNT(*),")"),"")) FROM T252 WHERE T252_T234_Id = T234_Id)', 'colunaTitulo' => 'Interações', 'style' => 'width: 1%', 'colunaExibir' => false),
            //array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            //array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback'=>'gCorrigeData', 'style' => 'width: 1%', 'colunaExibir' => false),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";
        $extra = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
            LEFT JOIN C007 AS usuario on usuario.C007_Id = T234_C007_Id_Cliente
            LEFT JOIN C007 AS supervisor on supervisor.C007_Id = usuario.C007_C007_Id
            LEFT JOIN C007 AS supervisor2 on supervisor2.C007_Id = usuario.C007_C007_Id_2
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			WHERE D024_Id = '{$r_D024_Id}'
            AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V',usuario.C007_Id = '{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S',(supervisor.C007_Id = '{$g['usuarioAtual']}' OR supervisor2.C007_Id = '{$g['usuarioAtual']}' OR usuario.C007_Id = '{$g['usuarioAtual']}'),'1=1'))			AND T234_Flag_Finalizado = 'S'
            AND T234_Flag_Tipo_Suporte = 'S'
            AND (IFNULL(T234_T234_Vinculo_Id,0)<=0)
			GROUP BY T234_Id
			ORDER BY T234_Data_Hora_Abertura DESC
EOT;
        $extraTotal = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
            LEFT JOIN C007 AS usuario on usuario.C007_Id = T234_C007_Id_Cliente
            LEFT JOIN C007 AS supervisor on supervisor.C007_Id = usuario.C007_C007_Id
            LEFT JOIN C007 AS supervisor2 on supervisor2.C007_Id = usuario.C007_C007_Id_2
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
            WHERE D024_Id = '{$r_D024_Id}'
            AND (IFNULL(T234_T234_Vinculo_Id, 0) <= 0)
            AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V',usuario.C007_Id = '{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S',(supervisor.C007_Id = '{$g['usuarioAtual']}' OR supervisor2.C007_Id = '{$g['usuarioAtual']}' OR usuario.C007_Id = '{$g['usuarioAtual']}'),'1=1'))			AND T234_Flag_Finalizado = 'S'
			AND T234_Flag_Tipo_Suporte = 'S'
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'select', 'select' => array(
                array('title' => 'Todos', 'value' => ''),
                array('title' => 'ERRO', 'value' => 'E'),
                array('title' => 'DÚVIDA', 'value' => 'D'),
                array('title' => 'MELHORIA', 'value' => 'M'),
            ), 'naoGerarExtra' => false),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            if ($array['todosCampos']['T234_Flag_Finalizado'] == 'N') {
                return 'linhaVermelho';
            }
        }

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        $extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);

        $totalRegistros = mysql_query("SELECT T234_Id FROM " . $from . $extraTotal);
        $totalRegistros = mysql_num_rows($totalRegistros);

        

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Botões (Release Acima do GRID)
         */
        //$totalRegistros = $g['sqlAuto']->pegarSqlCampo("T234_Id", $from, $extra, false, false);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right; float: left; margin-right: 85px;">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '$suporteNota&tabela={$from}', [800, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuporteClientePacotes':
        global $g;

        //Caso o acesso esteja sendo realizado pelo sistema de nota
        if (!empty($r_noEmp) && !empty($r_p)) {
            $suporteNota = "&noEmp=true&p=$r_p&u=$r_u";
        }

        /**
         * Definição do Grid (Tabela)
         */

        $T135 = mysql_query("select T135_Franquia_Horas from T135 left join D024 on D024_Id=T135_D024_Id WHERE D024_Id = '{$r_D024_Id}'");
        $mT135 = mysql_fetch_array($T135);
        $T135_Franquia_Horas = $mT135['T135_Franquia_Horas'];

        echo mysql_error();

        $separator = "GROUP_CONCAT(CONCAT(T234_Id, ' - ', T234_Horas_Desenvolvimento) separator '<br>')";
        $saldoHoras = "TIMEDIFF('{$T135_Franquia_Horas}',SEC_TO_TIME(SUM(TIME_TO_SEC(T234_Horas_Desenvolvimento)))) as saldoHoras";
        $totalPagar = "if ((CAST('$T135_Franquia_Horas' AS TIME)-SEC_TO_TIME(SUM(TIME_TO_SEC(T234_Horas_Desenvolvimento))))* T135_Valor_Hora_Tecnica > 0, 0, (CAST('$T135_Franquia_Horas' AS TIME)-SEC_TO_TIME(SUM(TIME_TO_SEC(T234_Horas_Desenvolvimento)))) * T135_Valor_Hora_Tecnica*(-1))";
        $fontSize = "16px;";
        $grid = array(
            array('colunaValor' => 'T272_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T272_Mes', 'colunaTitulo' => 'Mês', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => 'T272_Ano', 'colunaTitulo' => 'Ano', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => 'livre', 'colunaTitulo' => 'Chamados', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'colunaTipo' => 'livre', 'callback' => 'tabelaChamadosCliente', 'callbackParameter' => $r_D024_Id,
                'extra' => 'colunaTabela="Tabela"'),
            array('colunaValor' => 'SEC_TO_TIME(SUM(TIME_TO_SEC(T234_Horas_Desenvolvimento))) AS T234_Horas_Desenvolvimento', 'colunaTitulo' => 'Horas Desen.', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => '"$T135_Franquia_Horas" as T135_Franquia_Horas', 'colunaTitulo' => 'Franquia Horas', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => $saldoHoras, 'colunaTitulo' => 'Saldo Horas', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => 'T135_Valor_Hora_Tecnica', 'colunaTitulo' => 'Valor hora', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => $totalPagar, 'colunaTitulo' => 'Total a Pagar', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Documento', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento', 'colunaAlinhar' => 'center', 'callbackParameter' => array($confUsuario['urlRaiz']), 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => 'T272_Nome_Arquivo', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => '{$r_D024_Id}-D024_Id', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
        );

        /**
         * Definição da SQL
         */
        $from = "T272";

        // select para identificar o id do suporte.
        $extra = <<<EOT
		LEFT JOIN T234 ON T234_T272_Id = T272_Id AND T234_D024_Id = '{$r_D024_Id}'
		LEFT JOIN D024 ON D024_Id = T234_D024_Id
		LEFT JOIN T135 ON T135_D024_Id = D024_Id and D024_C004_Id='{$g['empresaAtual']}'
		GROUP BY T272_Id
		ORDER BY T272_Ano, T272_Mes

EOT;
        $extraTotal = <<<EOT
		LEFT JOIN T234 ON T234_T272_Id = T272_Id and T234_D024_Id ='{$r_D024_Id}'
		LEFT JOIN D024 ON D024_Id = T234_D024_Id
		LEFT JOIN T135 ON T135_D024_Id = D024_Id
		WHERE T272_Mes >= MONTH(NOW()) AND T272_Ano >= YEAR(NOW())
EOT;

        $T135 = mysql_query("SELECT T135_Franquia_Horas, T135_Valor_Hora_Tecnica FROM T135 WHERE T135_D024_id = '{$r_D024_Id}'");
        $mT135 = mysql_fetch_assoc($T135);

        /**
         * Filtro
         */
        $gridFiltro = array(
            'T272_Id' => array('titulo' => 'Pacote', 'tipo' => 'numero'),
            'T272_Mes' => array('titulo' => 'Mês', 'tipo' => 'text'),
            'T272_Ano' => array('titulo' => 'Ano', 'tipo' => 'text'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);
/*
$totalHorasDesenvolvimento = $g['sqlAuto']->pegarSqlCampo("SUM(T234_Horas_desenvolvimento)", $from, $extraTotal);
//$franquiaHoras = $g['sqlAuto']->pegarSqlCampo("T135_Franquia_Horas)", $from, $extraTotal);
$totalPagar = $g['sqlAuto']->pegarSqlCampo("(T135_Franquia_Horas-SUM(T234_Horas_Desenvolvimento))*T135_Valor_Hora_Tecnica", $from, $extraTotal);
if($totalPagar > 0){
$totalPagar = 0;
}else{
$totalPagar = $totalPagar * (-1);
}
echo <<<EOT
<div class="buttonsBottomTop">
<!--<span class="item" style="text-align:right">Franquia Horas<br><input type="text" class="whiteBg" id="inputAReceber" value="{$mT135['T135_Franquia_Horas']}" size="11" readonly></span> -->
<!--<span class="item" style="text-align:right">Valor Hora<br><input type="text" class="whiteBg" id="inputAReceber" value="{$mT135['T135_Valor_Hora_Tecnica']}" size="11" readonly></span> -->
<span class="item" style="text-align:right">Horas Desen.<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalHorasDesenvolvimento}" size="11" readonly></span>
<span class="item" style="text-align:right">Total Pagar<br><input type="text" class="whiteBg" id="inputAReceber" value="{$totalPagar}" size="11" readonly></span>
<div id="pacotes" style="clear:both;"></div>
</div>

EOT;
 */
        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}

    break;

    case 'gridSuporteTriagem':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => "CONCAT(T272_Mes, '/', T272_Ano)", 'colunaTitulo' => 'Pacote', 'colunaExibir' => true),
            array('colunaValor' => '(SELECT CONCAT(GROUP_CONCAT(DISTINCT T252_Usuario ORDER BY T252_Data_Hora ASC SEPARATOR ", "),IF(COUNT(*) > 1, CONCAT(" (",COUNT(*),")"),"")) FROM T252 WHERE T252_T234_Id = T234_Id)', 'colunaTitulo' => 'Interações', 'style' => 'width: 1%'),
            array('colunaValor' => 'SUBSTRING(D024_Nome_Fantasia,1,25)', 'colunaTitulo' => 'Empresa', 'style' => 'width: 1%'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'where C007_Ativo="S" ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'C007_Primeiro_Nome', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => '(SELECT IF(T252_C007_Id > 0 AND T252_C007_Id_Cliente <= 0, T252_Flag_Lido, "") FROM T252 WHERE T252_T234_Id = T234_Id ORDER BY T252_Data_Hora DESC LIMIT 1)', 'colunaTitulo' => 'Lido', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T252_Data_Hora', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
            array('colunaValor' => 'T252_C007_Id', 'colunaTitulo' => 'T252_C007_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T252_C007_Id_Cliente', 'colunaTitulo' => 'T252_C007_Id_Cliente', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Email_Solicitante', 'colunaTitulo' => 'T234_Email_Solicitante', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
        );

        /**
         * Definição da SQL
         */
        $from = "T234";

        $extra = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join C007 on C007_Id=T234_C007_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
			LEFT JOIN T272 ON T272_Id = T234_T272_Id
			WHERE T234_Flag_Triagem = 'S'
			AND T234_Flag_Tipo_Suporte = 'S'
			AND T234_Flag_Finalizado != 'S'
			GROUP BY T234_Id
			ORDER BY IF(IFNULL(T252_Id,0) > 0, T252_Data_Hora, T234_Data_Hora_Abertura) DESC
EOT;
        $extraTotal = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join C007 on C007_Id=T234_C007_Id
			WHERE T234_Flag_Triagem = 'S'
			AND T234_Flag_Tipo_Suporte = 'S'
			AND T234_Flag_Finalizado != 'S'
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            //'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Chamado', 'tipo' => 'texto'),
            'T272_Mes' => array('titulo' => 'Mês Pacote', 'tipo' => 'numero'),
            'T272_Ano' => array('titulo' => 'Ano Pacote', 'tipo' => 'numero'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            if (strtotime($array['todosCampos']['T252_Data_Hora']) < strtotime(date('Y-m-d H:i:s', strtotime("-2 days")))) {
                if ($array['todosCampos']['T252_C007_Id'] > 0) {
                    return 'linhaVermelho';
                }
            }
            if ($array['todosCampos']['T234_C007_Id'] <= 0 || $array['todosCampos']['T252_C007_Id_Cliente'] > 0) {
                return 'linhaNegrito';
            }
        }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Botões (Release Acima do GRID)
         */

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extraTotal);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="btnIncluirSuporteTriagem">Incluir</button>
				<button type="button" id="btnAtualizarTrello">Atualizar Trello</button>
				<span class="item" style="text-align:right; float: right; margin-right: 85px;">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#btnIncluirSuporteTriagem').unbind('click').bind('click', function(data) {
					abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Requisição de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=E&acessoSuporte=true&D024_Id=' + encodeURIComponent('{$r_D024_Id}'), [990, 600]);
				});
				$('#btnAtualizarTrello').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/AtualizarTrelloTriagem/?ajax=true&organizations=' + encodeURIComponent('{$g['C031']['TrelloNomeTimeAnalise']}') + '&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									dialogConfirm('Atualização', request.data);
									divRefresh('{$g['divId']}');
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Atualizar dados', 'Enviar os dados dos chamados para o trello?', \$buttons);
					});
			} </script></div>
EOT;
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha') && data.target.nodeName != 'DIV') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
			});
		} </script></div>
EOT;
        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuportePacote':

        /**
         * Definição da SQL
         */
        $from = "T272";

        $extra = <<<EOT
		LEFT JOIN T234 ON T234_T272_Id = T272_Id
		LEFT JOIN D024 ON D024_Id = T234_D024_Id
		LEFT JOIN T135 ON T135_D024_Id = D024_Id AND D024_C004_Id={$g['empresaAtual']}
		GROUP BY T272_Id

EOT;
        $extraTotal = <<<EOT
		LEFT JOIN T234 ON T234_T272_Id = T272_Id
		LEFT JOIN D024 ON D024_Id = T234_D024_Id
		LEFT JOIN T135 ON T135_D024_Id = D024_Id

EOT;
        $linksAnexos = array();
        //$dir = "{$g['confUsuarioOriginal']['dbDatabase']}/tmp/AnexosOS_".$r_acaoId;
        $dir = "{$g['confUsuarioOriginal']['dbDatabase']}/atualizacoes";
        $conteudoAnexos = '';
        if (is_dir($dir)) {
            $dh = opendir($dir);
            while (false !== ($filename = readdir($dh))) {
                if ($filename == '.' || $filename == '..') {continue;}
                $caminhoArquivo = base64_encode($dir . '/' . $filename);
            }
        }

        /**
         * Filtro
         */
        $gridFiltro = array(
            'D024_Nome_Empresa, D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
            'T272_Id' => array('titulo' => 'Pacote', 'tipo' => 'texto'),
            'T272_Mes' => array('titulo' => 'Mês', 'tipo' => 'texto'),
            'T272_Ano' => array('titulo' => 'Ano', 'tipo' => 'texto'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
         * Botões (Release Acima do GRID)
         */
        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="btnIncluirSuportePacote">Incluir</button>
				<div id="" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#btnIncluirSuportePacote').unbind('click').bind('click', function(data) {
					abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Incluir Pacote', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirPacote/', '&acaoId=' + encodeURIComponent('{$T272_Id}'), [500,230]);
				});
			} </script></div>
EOT;

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Definição do Grid (Tabela)
         */

        if (!empty($g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa, D024_Nome_Fantasia'))) {
            $cliente = $g['ui']->getGridFiltroCampoBusca('D024_Nome_Empresa, D024_Nome_Fantasia');
            $filtro = " where D024_Nome_Empresa like '%$cliente%' or D024_Nome_Fantasia like '%$cliente%' ";
        }

        $T135 = mysql_query("select SEC_TO_TIME(SUM(TIME_TO_SEC(T135_Franquia_Horas))) as T135_Franquia_Horas from T135 left join D024 on D024_Id=T135_D024_Id $filtro");
        $mT135 = mysql_fetch_array($T135);
        $T135_Franquia_Horas = $mT135['T135_Franquia_Horas'];

        echo mysql_error();
        $fontSize = "16px;";

        $separator = "GROUP_CONCAT(CONCAT(T234_Id, ' - ', T234_Horas_Desenvolvimento) separator '<br>')";
        $totalPagar = "if ((CAST('$T135_Franquia_Horas' AS TIME)-SEC_TO_TIME(SUM(TIME_TO_SEC(T234_Horas_Desenvolvimento))))* T135_Valor_Hora_Tecnica > 0, 0, (CAST('$T135_Franquia_Horas' AS TIME)-SEC_TO_TIME(SUM(TIME_TO_SEC(T234_Horas_Desenvolvimento)))) * T135_Valor_Hora_Tecnica*(-1))";
        $saldoHoras = "TIMEDIFF('{$T135_Franquia_Horas}',SEC_TO_TIME(SUM(TIME_TO_SEC(T234_Horas_Desenvolvimento)))) as saldoHoras";
        $grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T272_Id', $g['divId'])),
            array('colunaValor' => 'T272_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T272_Mes', 'colunaTitulo' => 'Mês', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => 'T272_Ano', 'colunaTitulo' => 'Ano', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => 'tabelaChamados', 'colunaTitulo' => 'Chamados', 'colunaAlinhar' => 'center', 'colunaTipo' => 'livre', 'style' => 'width: 1%', 'colunaExibir' => true, 'callback' => 'tabelaChamadosHardness', 'callbackParameter' => $cliente, 'extra' => 'colunaTabela="Tabela"'),
            //array('colunaValor' => "GROUP_CONCAT(D024_Nome_Empresa separator '<br>')", 'colunaTitulo' => 'Empresa', 'colunaAlinhar' => 'left', 'colunaExibir' => true),
            array('colunaValor' => 'SEC_TO_TIME(SUM(TIME_TO_SEC(T234_Horas_Desenvolvimento))) AS T234_Horas_Desenvolvimento', 'colunaTitulo' => 'Total Horas', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => '"$T135_Franquia_Horas" as T135_Franquia_Horas', 'colunaTitulo' => 'Franquia Horas', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => $saldoHoras, 'colunaTitulo' => 'Saldo Horas', 'colunaAlinhar' => 'center', 'colunaExibir' => true, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => 'T135_Valor_Hora_Tecnica', 'colunaTitulo' => 'Valor hora', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => $totalPagar, 'colunaTitulo' => 'Total a Receber', 'colunaCasasDecimais' => 2, 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => 'abrirDocumento', 'colunaTitulo' => 'Documento', 'colunaTipo' => 'livre', 'callback' => 'campoAbrirDocumento', 'colunaAlinhar' => 'center', 'style' => 'font-size:{$fontSize}'),
            array('colunaValor' => 'T272_Nome_Arquivo', 'colunaTitulo' => '', 'colunaAlinhar' => 'center', 'colunaExibir' => false, 'style' => 'font-size:{$fontSize}'),
        );

        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

/*        $totalHorasDesenvolvimento = $g['sqlAuto']->pegarSqlCampo("SUM(T234_Horas_Desenvolvimento)", $from, $extraTotal);
$totalPagar = $g['sqlAuto']->pegarSqlCampo($totalPagar, $from, $extraTotal);

echo <<<EOT
<div class="buttonsBottomTop">
<span class="item" style="text-align:right">Horas Desen.<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalHorasDesenvolvimento}" size="11" readonly></span>
<span class="item" style="text-align:right">Total Receber<br><input type="text" class="whiteBg" id="inputAReceber" value="{$totalPagar}" size="11" readonly></span>
<div id="pacotes" style="clear:both;"></div>
</div>

EOT;*/

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody td').unbind('click').unbind('click').bind('click', function(data) {
				var tabela = $(this).closest('td').attr('colunaTabela');
				if ((data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha') && data.target.nodeName != 'DIV') || tabela=='Tabela') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T272_Id-/, '');

				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Pacote', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirPacote/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [500,230]);
			});
		} </script></div>
EOT;
        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuporteGrupo':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D166_Id', $g['divId'])),
            array('colunaValor' => 'D166_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'D166_Grupo_Suporte', 'colunaTitulo' => 'Quadro'),
            array('colunaValor' => 'D166_Flag_Ativo', 'colunaTitulo' => 'Quadro Ativo'),
            array('colunaValor' => 'D166_Flag_Processar_Feedback', 'colunaTitulo' => 'Processar Feedback'),
            array('colunaValor' => 'D166_Flag_Atendimento', 'colunaTitulo' => 'Fila Atendimento'),
            array('colunaValor' => 'D166_Flag_Melhoria', 'colunaTitulo' => 'Atualizar Pacote', 'colunaExibir' => false),
            array('colunaValor' => 'D167_Nome_Lista', 'colunaTitulo' => 'Lista Analise', 'colunaExibir' => false),
            array('colunaValor' => 'concat(T272_Mes, " - ",T272_Ano)', 'colunaTitulo' => 'Pacote', 'colunaExibir' => false),
            array('colunaValor' => 'D166_Flag_Sincronizar_Trello', 'colunaTitulo' => 'Sincronizar Trello'),
        );

        /**
         * Definição da SQL
         */
        $from = 'D166';
        $extra = <<<EOT
            LEFT JOIN D167 ON D167_Id=D166_Lista_Analise_Id
            LEFT JOIN T272 ON T272_Id=D166_T272_Id
EOT;

        function gridLinha($array) {
            global $g;
            if($array['todosCampos']['D166_Flag_Ativo']=='N'){
                return 'linhaCinza';
            }
        }
        /**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'GrupoSuporteBtnIncluir'),
		);
		echo Botoes::processar($botoes);

        /**
         * Botões (Versão Acima do GRID)
         */
        
			/*<div class="buttonsBottomTop">
				<button type="button" id="GrupoSuporteBtnIncluir">Incluir</button>
			</div>*/
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#GrupoSuporteBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Incluir Quadro', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirGrupo/', '&acaoId=' + encodeURIComponent('{$D166_Id}'), [500,300]);
				});
			} </script></div>
EOT;

        /**
         * Filtro
         */
        $gridFiltro = array(
            'D166_Id' => array('titulo' => 'Id', 'tipo' => 'texto'),
            'D166_Grupo_Suporte' => array('titulo' => 'Quadro', 'tipo' => 'texto'),
            'D166_Flag_Ativo' => array('titulo' => 'Ativo?', 'tipo' => 'select', 'select' => array(
                    array('title' => '...', 'value' => ''),
                    array('title' => 'SIM', 'value' => 'S'),
                    array('title' => 'NÃO', 'value' => 'N'),
                )
            ),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D166_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Quadro - ID: ' + acaoId, '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirGrupo/', '&acaoId=' + encodeURIComponent(acaoId) + '&tabela={$from}', [500,300]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Botões (Versão Acima do GRID)
         *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid
         */
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>
                <div style="clear:both;"></div>
            </div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}

    break;

    case 'gridSuporteLista':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D167_Id', $g['divId'])),
            array('colunaValor' => 'D167_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true),
            array('colunaValor' => 'D167_Nivel_Prioridade', 'colunaTitulo' => 'Prioridade', 'callback' => 'nivelPrioridade', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'D167_Horas_Validade', 'colunaTitulo' => 'Horas Validade'),
            array('colunaValor' => 'D167_Nome_Lista', 'colunaTitulo' => 'Lista'),
            array('colunaValor' => 'D166_Grupo_Suporte', 'colunaTitulo' => 'Quadro'),
            array('colunaValor' => 'D167_Mover_Lista_Id', 'colunaTitulo' => 'Mover lista', 'callback' => 'moverListaNome', 'colunaExibir' => false),
            array('colunaValor' => 'D167_Flag_Enviar_Detalhes', 'colunaTitulo' => 'Enviar detalhes', 'colunaExibir' => false),
            array('colunaValor' => 'D167_Flag_Finalizar_Chamado', 'colunaTitulo' => 'Finalizar chamado', 'colunaExibir' => false),
        );

        /**
         * Definição da SQL
         */
        $from = 'D167';
        $extra = <<<EOT
		    LEFT JOIN D166 ON D167_D166_Id=D166_Id
EOT;
        /**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'ListaBtnIncluir'),
		);
		echo Botoes::processar($botoes);


        /**
         * Botões (Versão Acima do GRID)
         */
        
			/*<div class="buttonsBottomTop">
				<button type="button" id="ListaBtnIncluir">Incluir</button>

			</div>*/
            echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#ListaBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Incluir Lista', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirLista/', '&acaoId=' + encodeURIComponent('{$D167_Id}'), [500,270]);
				});
			} </script></div>
EOT;

        /**
         * Filtro
         */
        $gridFiltro = array(
            'D167_Id' => array('titulo' => 'Id', 'tipo' => 'texto'),
            'D167_Nome_Lista' => array('titulo' => 'Lista', 'tipo' => 'texto'),
            'D167_Nivel_Prioridade' => array('titulo' => 'Prioridade', 'tipo' => 'select', 'select' => array(
                    array('title' => '...', 'value' => ''),
                    array('title' => 'ALTA', 'value' => '1'),
                    array('title' => 'MÉDIA', 'value' => '2'),
                    array('title' => 'BAIXA', 'value' => '3'),
                )
            ),
            'D166_Grupo_Suporte' => array('titulo' => 'Quadro', 'tipo' => 'texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'D167_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var acaoId = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Lista - ID: ' + acaoId, '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirLista/', '&acaoId=' + encodeURIComponent(acaoId) + '&tabela={$from}', [530,270]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;
        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Botões (Versão Acima do GRID)
         *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid
         */
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>
                <div style="clear:both;"></div>
            </div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}

    break;

    case 'contentIncluirPacote':
        /*
         * Divisão da Area em Conteúdos
         */
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabIncluirPacote/', '&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions, false);

    break;

    case 'contentIncluirGrupo':
        /*
         * Divisão da Area em Conteúdos
         */
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabIncluirGrupo/', '&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions, false);

    break;

    case 'contentIncluirHorasTrabalhadas':
        /*
         * Divisão da Area em Conteúdos
         */
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        log("chamado=:" . $T234_Id);

        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabIncluirHorasTrabalhadas/', '&acaoId=' . urlencode($r_acaoId) . '&T234_Id=' . urlencode($r_T234_Id)),
        );
        echo gProcessaAcoes($actions, false);

    break;

    case 'contentIncluirLista':
        /*
         * Divisão da Area em Conteúdos
         */
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabIncluirLista/', '&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions, false);

    break;

    case 'contentFilaAtendimento':
        /*
         * Divisão da Area em Conteúdos
         */
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabFilaAtendimento/', '&acaoId=' . urlencode($r_acaoId) . '&T234_Id=' . urlencode($r_T234_Id)),
        );
        echo gProcessaAcoes($actions, false);

    break;

    case 'contentFilaAtendimentoCliente':
        /*
         * Divisão da Area em Conteúdos
         */
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');

        $D166 = mysql_query("SELECT D166_Id, D166_Grupo_Suporte FROM D166 WHERE D166_Flag_Atendimento='S'");
        $mD166 = mysql_fetch_array($D166);
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabFilaAtendimentoCliente/', '&acaoId=' . urlencode($r_acaoId) . '&D166_Id=' . urlencode($mD166['D166_Id'])),
        );
        echo gProcessaAcoes($actions, false);

    break;

    case 'tabFilaAtendimento':
        global $g;
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array();

        $D166 = mysql_query("SELECT D166_Id, D166_Grupo_Suporte,D166_Nome_Time FROM D166 WHERE D166_Flag_Atendimento='S'");
        while ($mD166 = mysql_fetch_array($D166)) {

            if ($mD166['D166_Nome_Time'] == $g['C031']['TrelloNomeTimeAnalise']) {
                // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
                $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/outroFilaAtendimento/', "{$mD166['D166_Grupo_Suporte']}", '&tab=geral&D166_Id=' . urlencode($mD166['D166_Id']), '&divRefresh=' . urlencode($r_divIdRefresh));
            } else {
                // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
                $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/outroFilaDesenvolvimento/', "{$mD166['D166_Grupo_Suporte']}", '&tab=geral&D166_Id=' . urlencode($mD166['D166_Id']), '&divRefresh=' . urlencode($r_divIdRefresh));
            }
        }
        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabSuporteCliente':
        global $g;
        $divContentId = isset($r_divContentId) ? $r_divContentId : false;

        // Definição das Tabs
        $tabs = array();
        $tabList = array();

        $D166 = mysql_query("SELECT D166_Id, D166_Grupo_Suporte,D166_Nome_Time FROM D166 WHERE D166_Flag_Atendimento='S'");
        while ($mD166 = mysql_fetch_array($D166)) {
            if ($mD166['D166_Nome_Time'] == $g['C031']['TrelloNomeTimeAnalisex']) {
                // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
                $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/outroFilaAtendimentoCliente/', "{$mD166['D166_Grupo_Suporte']}", '&acaoId=' . urlencode($r_D024_Id) . '&D166_Id=' . urlencode($mD166['D166_Id']));
            }
        }

        $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/outroFilaAtendimentoCliente/', "Fila Atendimento", '&acaoId=' . urlencode($r_D024_Id).'&divContentId=' . urlencode($divContentId));
        // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
        // $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/tabFilaAtendimentoCliente/', 'Fila atendimento', '&acaoId=' . urlencode($r_D024_Id) );
        $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteClienteAberto/', 'Chamados em aberto');
        // $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteClienteAberto/', 'Melhorias', "&flagTipoOS=M");
        // $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteClienteAberto/', 'Dúvidas', "&flagTipoOS=D");
        // $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteClienteAberto/', 'Erros', "&flagTipoOS=E");
        // $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/tabSuporteClienteAberto/', 'Abertos');
        $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteClienteFinalizado/', 'Chamados finalizados');
        // $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteClientePacotes/', 'Pacote atualização');
        
        if ($r_flagVendedor == 'D') {
            $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteClienteSuporteTelefonico/', 'Ligações');
        }

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabFilaAtendimentoCliente':
        global $g;
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array();

        $D166 = mysql_query("SELECT D166_Id, D166_Grupo_Suporte,D166_Nome_Time FROM D166 WHERE D166_Flag_Atendimento='S'");
        while ($mD166 = mysql_fetch_array($D166)) {

            if ($mD166['D166_Nome_Time'] == $g['C031']['TrelloNomeTimeAnalise']) {
                // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
                $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/outroFilaAtendimentoCliente/', "{$mD166['D166_Grupo_Suporte']}", '&acaoId=' . urlencode($r_acaoId) . '&D166_Id=' . urlencode($mD166['D166_Id']));
            }
        }
        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'outroFilaAtendimento':

        $D166_Id = $r_D166_Id;

        echo <<<EOT
		<style>
			table a:link {
		    color: #666;
		    font-weight: bold;
		    text-decoration:none;
			}
			table#t1 a:visited {
			    color: #999999;
			    font-weight:bold;
			    text-decoration:none;
			}
			table#t1 a:active,
			table#t1 a:hover {
			    color: #bd5a35;
			    text-decoration:underline;
			}
			table#t1 {
			    width: 95%;
			    font-family:Arial, Helvetica, sans-serif;
			    color:#666;
			    font-size:12px;
			    background:#eaebec;
			    margin:20px;
			    border:#ccc 1px solid;

			    -moz-border-radius:3px;
			    -webkit-border-radius:3px;
			    border-radius:3px;
			}
			table#t1 th {
			    padding:21px 25px 22px 25px;
			    border-top:1px solid #fafafa;
			    border-bottom:1px solid #e0e0e0;

			    background: #ededed;
			    background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
			    background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
			}
			table#t1 th:first-child{
			    text-align: left;
			    padding-left:20px;
			}
			table#t1 tr:first-child th:first-child{
			    -moz-border-radius-topleft:3px;
			    -webkit-border-top-left-radius:3px;
			    border-top-left-radius:3px;
			}
			table#t1 tr:first-child th:last-child{
			    -moz-border-radius-topright:3px;
			    -webkit-border-top-right-radius:3px;
			    border-top-right-radius:3px;
			}
			table#t1 tr{
			    text-align: center;
			    padding-left:20px;
			}
			table#t1 tr td:first-child{
			    text-align: left;
			    padding-left:20px;
			    border-left: 0;
			}
			table#t1 tr td {
			    padding:18px;
			    border-top: 1px solid #ffffff;
			    border-bottom:1px solid #e0e0e0;
			    border-left: 1px solid #e0e0e0;

			    background: #fafafa;
			    background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
			    background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
			}
			table#t1 tr.even td{
			    background: #f6f6f6;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
			    background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
			}
			table#t1 tr:last-child td{
			    border-bottom:0;
			}
			table#t1 tr:last-child td:first-child{
			    -moz-border-radius-bottomleft:3px;
			    -webkit-border-bottom-left-radius:3px;
			    border-bottom-left-radius:3px;
			}
			table#t1 tr:last-child td:last-child{
			    -moz-border-radius-bottomright:3px;
			    -webkit-border-bottom-right-radius:3px;
			    border-bottom-right-radius:3px;
			}
			table#t1 tr:hover td{
			    background: #f2f2f2;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
			    background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);
			}
		</style>

		<table cellspacing='0' id="t1"> <!-- Não retirar essa linha --!>
			<!-- Cabeçalho -->
		    <tr>
                <th>Chamado</th>
                <th>Tipo</th>
		        <th>Prioridade</th>
		        <th>Cliente</th>
		        <th>Solicitante</th>
		       <!-- <th>Lista</th>-->
		        <th>Data abertura</th>
		        <th>Prazo</th>
		        <th>Aguardando<br> cliente</th>
		        <th>Data previsão</th>
		        <!--<th>Horas atendidas restantes</th>-->
		        <!--<th>Horas atendidas suporte</th>-->
		        <th>Faltam</th>
		    </tr>
EOT;
        $T234 = mysql_query("SELECT T234_Id,
								   T234_Data_Hora_Abertura,
								   D167_Nivel_Prioridade,
								   ADDTIME(T234_Data_Hora_Abertura, D167_Horas_Validade) as previsao,
								   T234_Solicitante,
								   TIMESTAMPDIFF(HOUR,CURRENT_TIMESTAMP(), ADDTIME(T234_Data_Hora_Abertura, D167_Horas_Validade)) AS horas,
								   D167_Nome_Lista,
								   D024_Nome_Empresa,
								   D167_Horas_Validade,
                                   T234_Flag_Tipo_OS,
								   T234_Horas_Atendimento
					 		  FROM T234
						 LEFT JOIN D167 ON D167_Id = T234_D167_Id
						 LEFT JOIN D024 ON D024_Id = T234_D024_Id
						     WHERE T234_D166_Id = '{$D166_Id}'
						       AND (T234_Flag_Finalizado = 'A' OR T234_Flag_Finalizado = 'N')
						  ORDER BY D167_Nivel_Prioridade, T234_Id");
        $T234_Flag_Tipo_OS = "";
        while ($rowT234 = mysql_fetch_array($T234)) {
            $chamado = str_pad($rowT234['T234_Id'], 8, "0", STR_PAD_LEFT);
            $solicitante = $rowT234['T234_Solicitante'];
            $dataAbertura = gCorrigeData($rowT234['T234_Data_Hora_Abertura']);
            $dataPrevisao = gCorrigeData($rowT234['previsao']);
            //$horas = $rowT234['horas'];
            $cliente = substr($rowT234['D024_Nome_Empresa'], 0, 15);
            $prioridade = "";
            if ($rowT234['D167_Nivel_Prioridade'] == '1') {
                // $prioridade = "<div style=\"background-color: #f45858; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px; font-size: 11px;\">ALTA</div>";
                $prioridade = "ALTA";
            } else if ($rowT234['D167_Nivel_Prioridade'] == '2') {
                // $prioridade = "<div style=\"background-color: #f2e087; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px; font-size: 11px;\">MÉDIA</div>";
                $prioridade = "MÉDIA";
            } else if ($rowT234['D167_Nivel_Prioridade'] == '3') {
                // $prioridade = "<div style=\"background-color: #97f4ed; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px; font-size: 11px;\">BAIXA</div>";
                $prioridade = "BAIXA";
            } else {
                $prioridade = $rowT234['D167_Nome_Lista'];
            }

            if($rowT234['T234_Flag_Tipo_OS']=="M"){
                $T234_Flag_Tipo_OS = "<div style=\"background-color: #C5F5B6; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px;\">MELHORIA</div>";
            }else if($rowT234['T234_Flag_Tipo_OS']=="E"){
                $T234_Flag_Tipo_OS = "<div style=\"background-color: #F48181; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px;\">ERRO</div>";
            }else if($rowT234['T234_Flag_Tipo_OS']=="D"){
                $T234_Flag_Tipo_OS = "<div style=\"background-color: #FFFFAD; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px;\">DÚVIDA</div>";
            }else{
                $T234_Flag_Tipo_OS = "<div style=\"background-color: #F48181; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px;\">ERRO</div>";
            }

            $calculoHoras = calcularHorasAtendimento($rowT234['T234_Id']);

            $from = "T234";

            echo <<<EOT
				<tr id='{$chamado}'style="cursor: pointer;">
                    <td>{$chamado}</td>
                    <td>{$T234_Flag_Tipo_OS}</td>
					<td>{$prioridade}</td>
					<td>{$cliente}</td>
					<td>{$solicitante}</td>
					<!--<td>{$rowT234['D167_Nome_Lista']}</td>-->
					<td>{$dataAbertura}</td>
					<td>{$calculoHoras['prazo']}</td>
					<td>{$calculoHoras['pausaHoras']}</td>
					<td>{$calculoHoras['previsaoData']}</td>
					<!--<td>{$calculoHoras['diferencaHoras']}</td>-->
					<!--<td>{$calculoHoras['atendimentoHoras']}</td>-->
					<td>{$calculoHoras['diferencaDatas']}</td>
				</tr>
EOT;
        }

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#t1 tr').unbind('click').unbind('click').bind('click', function(data) {
				var id = $(this).attr('id');
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}&divIdRefresh={$g['divId']}', [1000, 600]);
			});


		} </script></div>
EOT;
        echo "</table>";

    break;

    case 'outroFilaDesenvolvimento':

        $D166_Id = $r_D166_Id;
        $total = "00:00:00";

        echo <<<EOT
		<style>
			table a:link {
		    color: #666;
		    font-weight: bold;
		    text-decoration:none;
			}
			table#t1 a:visited {
			    color: #999999;
			    font-weight:bold;
			    text-decoration:none;
			}
			table#t1 a:active,
			table#t1 a:hover {
			    color: #bd5a35;
			    text-decoration:underline;
			}
			table#t1 {
			    width: 95%;
			    font-family:Arial, Helvetica, sans-serif;
			    color:#666;
			    font-size:12px;
			    background:#eaebec;
			    margin:20px;
			    border:#ccc 1px solid;

			    -moz-border-radius:3px;
			    -webkit-border-radius:3px;
			    border-radius:3px;
			}
			table#t1 th {
			    padding:21px 25px 22px 25px;
			    border-top:1px solid #fafafa;
			    border-bottom:1px solid #e0e0e0;

			    background: #ededed;
			    background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
			    background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
			}
			table#t1 th:first-child{
			    text-align: left;
			    padding-left:20px;
			}
			table#t1 tr:first-child th:first-child{
			    -moz-border-radius-topleft:3px;
			    -webkit-border-top-left-radius:3px;
			    border-top-left-radius:3px;
			}
			table#t1 tr:first-child th:last-child{
			    -moz-border-radius-topright:3px;
			    -webkit-border-top-right-radius:3px;
			    border-top-right-radius:3px;
			}
			table#t1 tr{
			    text-align: center;
			    padding-left:20px;
			}
			table#t1 tr td:first-child{
			    text-align: left;
			    padding-left:20px;
			    border-left: 0;
			}
			table#t1 tr td {
			    padding:18px;
			    border-top: 1px solid #ffffff;
			    border-bottom:1px solid #e0e0e0;
			    border-left: 1px solid #e0e0e0;

			    background: #fafafa;
			    background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
			    background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
			}
			table#t1 tr.even td{
			    background: #f6f6f6;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
			    background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
			}
			table#t1 tr:last-child td{
			    border-bottom:0;
			}
			table#t1 tr:last-child td:first-child{
			    -moz-border-radius-bottomleft:3px;
			    -webkit-border-bottom-left-radius:3px;
			    border-bottom-left-radius:3px;
			}
			table#t1 tr:last-child td:last-child{
			    -moz-border-radius-bottomright:3px;
			    -webkit-border-bottom-right-radius:3px;
			    border-bottom-right-radius:3px;
			}
			table#t1 tr:hover td{
			    background: #f2f2f2;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
			    background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);
			}
		</style>

		<table cellspacing='0' id="t1"> <!-- Não retirar essa linha --!>
			<!-- Cabeçalho -->
		    <tr>
		        <th>Chamado</th>
		        <th>Cliente</th>
		        <th>Solicitante</th>
		        <th>Lista</th>
		        <th>Data abertura</th>
		        <th>Prazo</th>
		        <th>Total</th>
		    </tr>
EOT;
        $T234 = mysql_query("SELECT T234_Id,
								    T234_Data_Hora_Abertura,
								    D167_Nivel_Prioridade,
								    ADDTIME(T234_Data_Hora_Abertura, D167_Horas_Validade) as previsao,
								    T234_Solicitante,
								    TIMESTAMPDIFF(HOUR,CURRENT_TIMESTAMP(), ADDTIME(T234_Data_Hora_Abertura, D167_Horas_Validade)) AS horas,
								    D167_Nome_Lista,
								    D024_Nome_Empresa,
								    D167_Horas_Validade,
								    T234_Horas_Atendimento,
								    T234_Horas_Desenvolvimento
					 		   FROM T234
						  LEFT JOIN D167 ON D167_Id = T234_D167_Id
						  LEFT JOIN D024 ON D024_Id = T234_D024_Id
						      WHERE T234_D166_Id = '{$D166_Id}'
						        AND T234_Flag_Finalizado = 'N'
						        AND D167_Nome_Lista NOT LIKE 'Concluído%'
						   ORDER BY D167_Nivel_Prioridade, T234_Id");
        $num = mysql_num_rows($T234);

        while ($rowT234 = mysql_fetch_array($T234)) {
            $from = "T234";
            $sql = mysql_query("SELECT ADDTIME('{$total}','{$rowT234['T234_Horas_Desenvolvimento']}') as Total");
            $result = mysql_fetch_array($sql);
            $total = $result['Total'];
            $chamado = str_pad($rowT234['T234_Id'], 8, "0", STR_PAD_LEFT);
            $solicitante = $rowT234['T234_Solicitante'];
            $dataAbertura = gCorrigeData($rowT234['T234_Data_Hora_Abertura']);
            $dataPrevisao = gCorrigeData($rowT234['previsao']);
            //$horas = $rowT234['horas'];
            $cliente = substr($rowT234['D024_Nome_Empresa'], 0, 15);

            echo <<<EOT
				<tr id='{$chamado}'style="cursor: pointer;">
					<td>{$chamado}</td>
					<td>{$cliente}</td>
					<td>{$solicitante}</td>
					<td>{$rowT234['D167_Nome_Lista']}</td>
					<td>{$dataAbertura}</td>
					<td>{$rowT234['T234_Horas_Desenvolvimento']}</td>
					<td>{$result['Total']}</td>
				</tr>
EOT;
        }

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#t1 tr').unbind('click').unbind('click').bind('click', function(data) {
				var id = $(this).attr('id');
				abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}&divIdRefresh={$g['divId']}', [1000, 600]);
			});
		} </script></div>
EOT;
        echo "</table>";

    break;

    case 'outroSuporteInfoParceiro':

        $T234_Id = isset($r_acaoId) ? $r_acaoId : false;

        $sqlT234 = mysql_query("SELECT T234_Info_Parceiro FROM T234 WHERE T234_Id='{$T234_Id}'");
        $T234 = mysql_fetch_array($sqlT234);

        echo <<<EOT
		<style>
			table#t2 a:link {
		    color: #666;
		    font-weight: bold;
		    text-decoration:none;
			}
			table#t2 a:visited {
			    color: #999999;
			    font-weight:bold;
			    text-decoration:none;
			}
			table#t2 a:active,
			table#t2 a:hover {
			    color: #bd5a35;
			    text-decoration:underline;
			}
			table#t2 {
			    width: 30%;
			    font-family:Arial, Helvetica, sans-serif;
			    color:#666;
			    font-size:15px;
			    background:#eaebec;
			    margin:20px;
			    border:#ccc 1px solid;
			    align:left;

			     -moz-border-radius:3px;
			    -webkit-border-radius:3px;
			    border-radius:3px;
			}

			table#t2 th {
			    padding:21px 25px 22px 25px;
			    border-top:1px solid #fafafa;
			    border-bottom:1px solid #e0e0e0;

			    background: #ededed;
			    background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
			    background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
			}
			table#t2 th:first-child{
			    text-align: center;
			    text-size:15px;
			    padding-left:20px;
			}
			table#t2 tr:first-child th:first-child{
			    -moz-border-radius-topleft:3px;
			    -webkit-border-top-left-radius:3px;
			    border-top-left-radius:3px;
			}
			table#t2 tr:first-child th:last-child{
			    -moz-border-radius-topright:3px;
			    -webkit-border-top-right-radius:3px;
			    border-top-right-radius:3px;
			}
			table#t2 tr{
			    text-align: left;
			    text-size:15px;
			    padding-left:20px;
			}
			table#t2 tr td:first-child{
			    text-align: left;
			    text-size:15px;
			    padding-left:20px;
			    border-left: 0;
			}
			table#t2 tr td {
			    padding:18px;
			    border-top: 1px solid #ffffff;
			    border-bottom:1px solid #e0e0e0;
			    border-left: 1px solid #e0e0e0;
			    background: #fafafa;
			    background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
			    background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
			}
			table#t2 tr.even td{
			    background: #f6f6f6;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
			    background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
			}
			table#t2 tr:last-child td{
			    border-bottom:0;
			}
			table#t2 tr:last-child td:first-child{
			    -moz-border-radius-bottomleft:3px;
			    -webkit-border-bottom-left-radius:3px;
			    border-bottom-left-radius:3px;
			}
			table#t2 tr:last-child td:last-child{
			    -moz-border-radius-bottomright:3px;
			    -webkit-border-bottom-right-radius:3px;
			    border-bottom-right-radius:3px;
			}
			table#t2 tr:hover td{
			    background: #f2f2f2;
			    background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
			    background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);
			}
		</style>

		<table cellspacing='0' id="t2" width='1000px'> <!-- Não retirar essa linha --!>
		    <tr>
		        <th>INFORMAÇÕES CHAMADO CLIENTE</th>
		    </tr>

EOT;

        echo <<<EOT
			<tr idCliente = '{$rowT234['T234_C007_Id_Cliente']}' id='{$chamado}' > <!-- style="cursor: pointer;" -->
				<td>{$T234['T234_Info_Parceiro']}</td>
			</tr>
EOT;

        echo "</table>";

    break;

    case 'outroFilaAtendimentoCliente':
        $D024_Id = $r_acaoId;
        $divContentId = $r_divContentId;

        if($g['C007']['C007_Flag_Vendedor']=="D"){
            $th = "<th>Suporte<br />técnico</th>";
        }

        echo <<<EOT
            <style>
                table#t2 a:link {
                    color: #666;
                    font-weight: bold;
                    text-decoration:none;
                }
                td:hover{
                    cursor:pointer;
                }
                table#t2 a:visited {
                    color: #999999;
                    font-weight:bold;
                    text-decoration:none;
                }
                table#t2 a:active,
                table#t2 a:hover {
                    color: #bd5a35;
                    text-decoration:underline;
                }
                table#t2 {
                    width: 95%;
                    font-family:Arial, Helvetica, sans-serif;
                    color:#666;
                    font-size:12px;
                    background:#eaebec;
                    margin:20px;
                    border:#ccc 1px solid;
                    align:center;

                    -moz-border-radius:3px;
                    -webkit-border-radius:3px;
                    border-radius:3px;
                }

                table#t2 th {
                    padding:21px 25px 22px 25px;
                    border-top:1px solid #fafafa;
                    border-bottom:1px solid #e0e0e0;

                    background: #ededed;
                    background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
                    background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
                }
                table#t2 th:first-child{
                    text-align: center;
                    padding-left:20px;
                }
                table#t2 tr:first-child th:first-child{
                    -moz-border-radius-topleft:3px;
                    -webkit-border-top-left-radius:3px;
                    border-top-left-radius:3px;
                }
                table#t2 tr:first-child th:last-child{
                    -moz-border-radius-topright:3px;
                    -webkit-border-top-right-radius:3px;
                    border-top-right-radius:3px;
                }
                table#t2 tr{
                    text-align: center;
                    padding-left:20px;
                }
                table#t2 tr td:first-child{
                    text-align: center;
                    padding-left:20px;
                    border-left: 0;
                }
                table#t2 tr td {
                    padding:18px;
                    border-top: 1px solid #ffffff;
                    border-bottom:1px solid #e0e0e0;
                    border-left: 1px solid #e0e0e0;

                    background: #fafafa;
                    background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
                    background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
                }
                table#t2 tr.even td{
                    background: #f6f6f6;
                    background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
                    background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
                }
                table#t2 tr:last-child td{
                    border-bottom:0;
                }
                table#t2 tr:last-child td:first-child{
                    -moz-border-radius-bottomleft:3px;
                    -webkit-border-bottom-left-radius:3px;
                    border-bottom-left-radius:3px;
                }
                table#t2 tr:last-child td:last-child{
                    -moz-border-radius-bottomright:3px;
                    -webkit-border-bottom-right-radius:3px;
                    border-bottom-right-radius:3px;
                }
                table#t2 tr:hover td{
                    background: #f2f2f2;
                    background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
                    background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);
                }
                button {
                    margin-top:5px;
                }
            </style>

            <table cellspacing='0' id="t2" width='1000px'> <!-- Não retirar essa linha --!>
                <!-- Cabeçalho -->
                <tr>
                    <th>Chamado</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Solicitante</th>
                    <th>Data abertura</th>
                    <th>Prazo<br />horas</th>
                    <th>Aguardando<br>cliente<br />horas</th>
                    <th>Data previsão</th>
                    <th>Faltam<br />horas</th>
                    {$th}
                </tr>
EOT;
        $T234 = mysql_query("SELECT T234_Id,
                                    T234_Data_Hora_Abertura,
                                    D167_Nivel_Prioridade,
                                    ADDTIME(T234_Data_Hora_Abertura, D167_Horas_Validade) as previsao,
                                    T234_Solicitante,
                                    TIMESTAMPDIFF(HOUR,CURRENT_TIMESTAMP(), ADDTIME(T234_Data_Hora_Abertura, D167_Horas_Validade)) AS horas,
                                    D167_Nome_Lista,
                                    T234_D024_Id,
                                    D167_Horas_Validade,
                                    T234_Descricao,
                                    T234_Horas_Atendimento,
                                    T234_C007_Id_Cliente,
                                    supervisor.C007_Id AS idSupervisor,
                                    supervisor2.C007_Id AS idSupervisor2,
                                    T234_Flag_Tipo_OS
                              FROM T234
                         LEFT JOIN D167 ON D167_Id = T234_D167_Id
                         LEFT JOIN D024 ON D024_Id = T234_D024_Id
                         LEFT JOIN C007 AS usuario on usuario.C007_Id = T234_C007_Id_Cliente
                         LEFT JOIN C007 AS supervisor on supervisor.C007_Id = usuario.C007_C007_Id
                         LEFT JOIN C007 AS supervisor2 on supervisor2.C007_Id = usuario.C007_C007_Id_2
                             WHERE (T234_Flag_Tipo_OS != 'B')
                               AND (T234_Flag_Finalizado != 'S')
                               AND (IFNULL(T234_T234_Vinculo_Id,0)<=0)
                               AND IF('{$g['C007']['C007_Flag_Vendedor']}' = 'V',usuario.C007_Id = '{$g['usuarioAtual']}',IF('{$g['C007']['C007_Flag_Vendedor']}' = 'S',(supervisor.C007_Id = '{$g['usuarioAtual']}' OR supervisor2.C007_Id = '{$g['usuarioAtual']}' OR usuario.C007_Id = '{$g['usuarioAtual']}'),'1=1'))
                          ORDER BY T234_Id");

        $T234_Flag_Tipo_OS = "";
        $cont = 0;

        while ($rowT234 = mysql_fetch_array($T234)) {
            $cont++;
            if ($rowT234['T234_D024_Id'] == $D024_Id) {
                $chamado = str_pad($rowT234['T234_Id'], 8, "0", STR_PAD_LEFT);
                $solicitante = $rowT234['T234_Solicitante'];
                $dataAbertura = gCorrigeData($rowT234['T234_Data_Hora_Abertura']);
                $dataPrevisao = gCorrigeData($rowT234['previsao']);
                $horas = $rowT234['horas'];
                if($rowT234['T234_Flag_Tipo_OS']=="M"){
                    $T234_Flag_Tipo_OS = "<div style=\"background-color: #C5F5B6; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px;\">MELHORIA</div>";
                }else if($rowT234['T234_Flag_Tipo_OS']=="E"){
                    $T234_Flag_Tipo_OS = "<div style=\"background-color: #F48181; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px;\">ERRO</div>";
                }else if($rowT234['T234_Flag_Tipo_OS']=="D"){
                    $T234_Flag_Tipo_OS = "<div style=\"background-color: #FFFFAD; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px;\">DÚVIDA</div>";
                }else{
                    $T234_Flag_Tipo_OS = "<div style=\"background-color: #F48181; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px;\">ERRO</div>";
                }
        
                $calculoHoras = calcularHorasAtendimento($rowT234['T234_Id']);

                //Se ultima interação feita foi do suporte não mostrar as horas que faltam para entrega do chamado e mostrar data previsao com fundo amarelo
                $sqlT252 = mysql_query("SELECT T252_Id,
                                               T252_C007_Id,
                                               T252_C007_Id_Cliente,
                                               T252_Descricao,
                                               T234_C007_Id_Cliente 
                                          FROM T252 
                                     LEFT JOIN T234 ON T252_T234_Id=T234_Id
                                         WHERE T252_T234_Id={$rowT234['T234_Id']} 
                                      ORDER BY T252_Id DESC LIMIT 1"); 
                $T252 = mysql_fetch_assoc($sqlT252);
                $previsaoData = $calculoHoras['previsaoData'];

                if($T252['T252_C007_Id_Cliente']!=$T252['T234_C007_Id_Cliente']){
                    $calculoHoras['diferencaDatas'] = "";
                    $previsaoData = "<div style=\"background-color: #FFFFAD; padding: 2px 3px; color: black; margin-top: 3px; border-radius: 2px;\">Nova interação</div>";
                }
                
                if($g['C007']['C007_Flag_Vendedor']=="D"){
                    $tdEnviar = "<td style='padding:18px 4px !important;'><button onclick='enviarChamadoHardness({$rowT234['T234_Id']})' clicado='false' id='enviarHardness-{$rowT234['T234_Id']}'>Transferir chamado</button></td>";
                }

                echo <<<EOT
                    <tr idSupervisor='{$rowT234['idSupervisor']}' idSupervisor2='{$rowT234['idSupervisor2']}' idCliente='{$rowT234['T234_C007_Id_Cliente']}' id='{$rowT234['T234_Id']}' tipoOS='{$rowT234['T234_Flag_Tipo_OS']}'> <!-- style="cursor: pointer;" -->
                        <td id='idChamado'>{$chamado}</td>
                        <td>{$T234_Flag_Tipo_OS}</td>
                        <td align=left>{$rowT234['T234_Descricao']}</td>
                        <td>{$solicitante}</td>
                        <td>{$dataAbertura}</td>
                        <td>{$calculoHoras['prazo']}</td>
                        <td>{$calculoHoras['pausaHoras']}</td>
                        <td>{$previsaoData}</td>
                        <td>{$calculoHoras['diferencaDatas']}</td>
                        {$tdEnviar}
                    </tr>
EOT;
            }
        }
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                function enviarChamadoHardness(T234_Id){
                    var id = document.getElementById('enviarHardness-'+T234_Id);
                    // id.setAttribute("clicado", "true");
                    var tipoOS = id.closest("tr").getAttribute('tipoOS');
                    var \$buttons = {
                        "Sim": function() {
                            $('#{$g['divId']}').showLoading();
                            $.getJSON('/outros/registroSuporte/registroSuporte/VerificarQtdeMaximaAtendimento/?ajax=true&tipo=' + tipoOS +'&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&callback=?', function(request) {
                                if (request.code) {
                                    $.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/enviarChamadoHardness/?ajax=true&T234_Id=' + T234_Id + '&callback=?', function(request) {
                                        $('#{$g['divId']}').hideLoading();
                                        if (request.code) {
                                            dialogConfirm('Enviado', "Chamado transferido com sucesso!");
                                            divRefresh('{$divContentId}');
                                        } else {
                                            dialogConfirm('Erro', request.data);
                                            divRefresh('{$divContentId}');
                                        }
                                    });
                                } else {
                                    dialogConfirm('Erro', request.data);
                                    divRefresh('{$divContentId}');
                                }
                            });
                            $(this).dialog("destroy").remove();
                        },
                        "Não": function() { $(this).dialog("destroy").remove(); }
                    };
                    dialogConfirm('Transferir chamado', 'Esse chamado será transferido para <b>suporte técnico</b> e não estará mais disponível para consulta no suporte interno.<br><br> Deseja continuar?', \$buttons);
                }

                $('#t2 tr').unbind('click').unbind('click').bind('click', function(data) {
                    var id = $(this).attr('id');
                    // if($('#enviarHardness-'+id).attr("clicado")=="false"){
                        var idCliente = $(this).attr('idCliente');
                        var idSupervisor = $(this).attr('idSupervisor');
                        var idSupervisor2 = $(this).attr('idSupervisor2');
                        if(idCliente=={$g['usuarioAtual']} || '{$g['C007']['C007_Flag_Vendedor']}'=='D' || {$g['usuarioAtual']}==63 || ('{$g['C007']['C007_Flag_Vendedor']}'=='S' && idSupervisor=={$g['C007']['C007_Id']}) || ('{$g['C007']['C007_Flag_Vendedor']}'=='S' && idSupervisor2=={$g['C007']['C007_Id']})){
                            abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id), [1000, 600]);
                        }
                    // }else{
                    //     $('#enviarHardness-'+id).attr("clicado","false");
                    // }
                });
            } </script></div>
EOT;
        echo "</table>";

    break;

    case 'tabIncluirPacote':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirPacote/', 'Pacote', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabIncluirGrupo':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirGrupo/', 'Quadro', '&tab=geral&acaoId=' . urlencode($acaoId), '&divRefresh=' . urlencode($r_divIdRefresh)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabIncluirHorasTrabalhadas':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirHorasTrabalhadas/', 'Horas desenvolvimento', '&tab=geral&acaoId=' . urlencode($r_acaoId) . '&T234_Id=' . urlencode($T234_Id), '&divRefresh=' . urlencode($r_divIdRefresh)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabIncluirLista':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirLista/', 'Lista', '&tab=geral&acaoId=' . urlencode($acaoId), '&divRefresh=' . urlencode($r_divIdRefresh)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'formIncluirPacote':
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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
        $form_tabela = 'T272';
        $form_campo_pk = 'T272_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'registroSuporteClienteIncluirPacote_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirPacote/';

        // novo formulário
        $form = new Formulario('formIncluirPacote');
        // Editor
        $form_editor = array(
            'geral' => '{"uploadArquivo":{"top":76,"left":107.109375,"width":245,"hidden":0},"T272_Id":{"top":7,"left":167.640625,"width":88,"hidden":0},"T272_Mes":{"top":30,"left":181.09375,"width":128,"hidden":0},"T272_Ano":{"top":53,"left":182.3125,"width":120,"hidden":0}}',
        );
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T272_Id' => array('name' => 'T272_Id', 'label' => 'Pacote', 'type' => 'text', 'readonly' => true, 'value' => $acaoId),
                    'T272_Mes' => array('name' => 'T272_Mes', 'label' => 'Mês', 'type' => 'select', 'select' => array(
                        array('title' => '...', 'value' => '', 'selected' => true),
                        array('title' => 'Janeiro', 'value' => '01'),
                        array('title' => 'Fevereiro', 'value' => '02'),
                        array('title' => 'Março', 'value' => '03'),
                        array('title' => 'Abril', 'value' => '04'),
                        array('title' => 'Maio', 'value' => '05'),
                        array('title' => 'Junho', 'value' => '06'),
                        array('title' => 'Julho', 'value' => '07'),
                        array('title' => 'Agosto', 'value' => '08'),
                        array('title' => 'Setembro', 'value' => '09'),
                        array('title' => 'Outubro', 'value' => '10'),
                        array('title' => 'Novembro', 'value' => '11'),
                        array('title' => 'Dezembro', 'value' => '12'),
                    ),
                    ),
                    'T272_Ano' => array('name' => 'T272_Ano', 'label' => 'Ano', 'type' => 'text'),
                    'uploadArquivo' => array('name' => 'uploadArquivo', 'label' => 'Arquivo documento', 'type' => 'file', 'length' => array(24, 24), 'reject' => true, 'uppercase' => false),
                    'T272_Nome_Arquivo' => array('name' => 'T272_Nome_Arquivo', 'type' => 'hidden', 'uppercase' => false),
                ),
            ),
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
                $ano = $form->campoValorEnviado('T272_Ano');
                $mes = $form->campoValorEnviado('T272_Mes');

                $mesAno = mysql_query("SELECT T272_Id FROM T272 WHERE T272_Ano = '{$ano}' AND T272_Mes = '{$mes}' AND T272_Id != '{$acaoId}'");
                $nMesAno = mysql_num_rows($mesAno);
                if ($nMesAno > 0) {
                    $alert = gGeraAlertMsg('ERRO', 'Mês e ano já cadastrados!', false);
                    echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    die();
                }

                // Insert ou Update?
                if (!empty($acaoId)) {
                    // Update

                    $arquivo = $form->campoValorEnviado('uploadArquivo');
                    if ($arquivo['error'] == UPLOAD_ERR_OK) {
                        // Neste caso, guardamos o nome do arquivo no banco
                        $pathinfo = pathinfo($arquivo['name']);
                        $pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];
                        $dir = "{$g['pathDados']}atualizacoes/";
                        is_dir($dir) or mkdir($dir, 0777, true);
                        $file = $dir . '/' . "Atualizacao_" . $form->campoValorEnviado('T272_Mes') . '_' . $form->campoValorEnviado('T272_Ano') . '.' . $pathinfo['extension'];
                        $form->campoSetarValor('T272_Nome_Arquivo', $file, true);
                        if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                            // Gravar o arquivo no disco
                            if (!move_uploaded_file($arquivo['tmp_name'], $file)) {
                                echo gGeraAlertMsg('Operação OK', 'Erro na gravação do arquivo! ' . $arquivo['name']);
                            } else {
                                echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                            }
                            // Imprime uma mensagem e fecha a janela
                            echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											{$alert}
										}, 150);
										});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
								} </script></div>
EOT;
                        }
                    }
/*
if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
// Imprime uma mensagem e fecha a janela
echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
setTimeout(function() {
{$alert}
}, 150);
});
$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
divRefresh('{$g['divIdParent']}', true);
} </script></div>
EOT;
}*/
                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        echo gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
									});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divId']}', true);
							} </script></div>
EOT;

                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                $form->campoSetarValor('T272_Id', htmlentities($form->campoValor('T272_Id'), ENT_COMPAT, 'UTF-8'));
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
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
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="registroSuporteClienteIncluirPacote_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'formIncluirGrupo':
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($r_tab)) ? $r_tab : false;

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
        // A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
        $form_tabela = 'D166';
        $form_campo_pk = 'D166_Id';
        // ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'GrupoSuporteBtnSubmit';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirGrupo/';

        //form editor
        $form_editor = array(
            'geral' => '{"D166_Grupo_Suporte":{"top":7,"left":85.1875,"width":232,"hidden":0},"D166_Flag_Processar_Feedback":{"top":30,"left":21,"width":64,"hidden":0},"D166_Flag_Atendimento":{"top":30,"left":215.9375,"width":64,"hidden":0},"D166_Flag_Ativo":{"top":53,"left":58.28125,"width":64,"hidden":0},"D166_Flag_Sincronizar_Trello":{"top":53,"left":212.90625,"width":64,"hidden":0},"D166_Flag_Melhoria":{"top":122,"left":217.78125,"width":64,"hidden":1},"D166_Lista_Analise_Id":{"top":145,"left":60.125,"width":239,"hidden":1},"D166_T272_Id":{"top":168,"left":87.640625,"width":239,"hidden":1}}',
        );

        $condicao = "";
        if (!empty($r_acaoId)) {
            $condicao = "WHERE D167_D166_Id='{$r_acaoId}'";
        }

        // Definição do Formulário
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'D166_Flag_Processar_Feedback' => array('name' => 'D166_Flag_Processar_Feedback', 'label' => 'Processar Feedback', 'type' => 'select', 'select' => array(
                        array('title' => 'Sim', 'value' => 'S', 'selected' => true),
                        array('title' => 'Não', 'value' => 'N'),
                    ),
                    ),
                    'D166_Grupo_Suporte' => array('name' => 'D166_Grupo_Suporte', 'label' => 'Quadro', 'type' => 'text'),
                    'D166_Flag_Ativo' => array('name' => 'D166_Flag_Ativo', 'label' => 'Quadro Ativo', 'type' => 'select', 'select' => array(
                        array('title' => 'Sim', 'value' => 'S', 'selected' => true),
                        array('title' => 'Não', 'value' => 'N'),
                    ),
                    ),
                    'D166_Flag_Atendimento' => array('name' => 'D166_Flag_Atendimento', 'label' => 'Fila Atendimento', 'type' => 'select', 'select' => array(
                        array('title' => 'SIM', 'value' => 'S'),
                        array('title' => 'NÃO', 'value' => 'N', 'selected' => true),
                    ),
                    ),
                    'D166_Flag_Melhoria' => array('name' => 'D166_Flag_Melhoria', 'label' => 'Atualizar Pacote', 'type' => 'select', 'select' => array(
                        array('title' => 'SIM', 'value' => 'S'),
                        array('title' => 'NÃO', 'value' => 'N', 'selected' => true),
                    ),
                    ),
                    'D166_T272_Id' => array('name' => 'D166_T272_Id', 'label' => 'Pacote', 'type' => 'select', 'select' =>
                        gGeraSelect('T272', 'T272_Id', 'T272_Mes,T272_Ano', '', true),
                    ),
                    'D166_Lista_Analise_Id' => array('name' => 'D166_Lista_Analise_Id', 'label' => 'Lista Análise', 'type' => 'select', 'select' =>
                        gGeraSelect('D167', 'D167_Id', 'D167_Nome_Lista', "{$condicao} order by D167_Nome_Lista", true),
                    ),
                    'D166_Flag_Sincronizar_Trello' => array('name' => 'D166_Flag_Sincronizar_Trello', 'label' => 'Sincronizar Trello', 'type' => 'select', 'select' => array(
                        array('title' => 'SIM', 'value' => 'S', 'selected' => true),
                        array('title' => 'NÃO', 'value' => 'N'),
                    ),
                    ),
                ),
            ),
        );
        $form = new Formulario('formIncluirGrupo');
        $form->definirFormularioArray($formArray);
        
        /**
         * PARTE 3: Processamento do formulário (se foi enviado)
         * E carregamento dos valores (se não foi enviado parametro acaoId)
         */
        if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
                // Form sem erros
            } else if ($formCodigo == 2) {
                // Insert ou Update?
                if (!empty($r_acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
                        // Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
                        $alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        // Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
                        $alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {

										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($r_acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
                // Mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
                }
            }
        }

        /**
         * PARTE 4: Gerar o formulário
         */
        $gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

        /**
         * PARTE 5: Exibir o formulário
         */
        $g['smarty']->assign('formularioID', $form->formularioID());
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesMid = $botoesEsq = $botoesDir = '';
        $botoesDir .= '<button type="submit" id="GrupoSuporteBtnSubmit">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesMid', $botoesMid);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'formIncluirHorasTrabalhadas':
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($r_tab)) ? $r_tab : false;
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;

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
        // A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
        $form_tabela = 'T274';
        $form_campo_pk = 'T274_Id';
        // ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'HorasTrabalhadasSuporteBtnSubmit';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirHorasTrabalhadas/';

        //form editor
        $form_editor = array(
            'geral' => '{"T274_Data":{"top":7,"left":34.640625,"width":120,"hidden":0},"T274_Hora_Inicio":{"top":30,"left":32.203125,"width":120,"hidden":0},"T274_Hora_Final":{"top":53,"left":34.03125,"width":120,"hidden":0}}',
        );

        // Definição do Formulário
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T274_T234_Id' => array('name' => 'T274_T234_Id', 'label' => '', 'type' => 'hidden', 'value' => $T234_Id),
                    'T274_Data' => array('name' => 'T274_Data', 'label' => 'Data', 'value' => date("d/m/Y"), 'type' => 'data', 'length' => array(15, 14)),
                    'T274_Hora_Inicio' => array('name' => 'T274_Hora_Inicio', 'label' => 'Inicio', 'type' => 'tempo'),
                    'T274_Hora_Final' => array('name' => 'T274_Hora_Final', 'label' => 'Final', 'type' => 'tempo'),
                ),
            ),
        );
        $form = new Formulario('formIncluirHorasTrabalhadas');
        $form->definirFormularioArray($formArray);

        /**
         * PARTE 3: Processamento do formulário (se foi enviado)
         * E carregamento dos valores (se não foi enviado parametro acaoId)
         */
        if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
                // Form sem erros
            } else if ($formCodigo == 2) {
                // Insert ou Update?
                if (!empty($r_acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
                        // Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
                        $alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                } else {
                    $sqlT234 = mysql_query("SELECT T234_Horas_Desenvolvimento FROM T234 WHERE T234_Id='{$T234_Id}'");
                    $T234 = mysql_fetch_array($sqlT234);

                    if ($T234['T234_Horas_Desenvolvimento'] == '00:00:00') {
                        $alert .= gGeraAlertMsg('Erro', 'Não foi preenchido o prazo de desenvolvimento desse chamado.', false);
                        echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									// Fecha a janela, recarrega o grid e abre a alteração
									setTimeout(function() {

										{$alert}
									}, 150);
									divRefresh('{$g['divIdParent']}', true);
								} </script></div>
EOT;
                    } else {
                        // Insert
                        if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                            // Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
                            $alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
                            echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									// Fecha a janela, recarrega o grid e abre a alteração
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {

											{$alert}
										}, 150);
		  							});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
								} </script></div>
EOT;
                        }
                    }

                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($r_acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
                // Mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
                }
            }
        }

        /**
         * PARTE 4: Gerar o formulário
         */
        $gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

        /**
         * PARTE 5: Exibir o formulário
         */
        $g['smarty']->assign('formularioID', $form->formularioID());
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesMid = $botoesEsq = $botoesDir = '';
        $botoesDir .= '<button type="submit" id="HorasTrabalhadasSuporteBtnSubmit">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesMid', $botoesMid);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'formIncluirLista':
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($r_tab)) ? $r_tab : false;

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
        // A qual tabela este form está relacionada, e a chave primária da tabela e o seu campo primário
        $form_tabela = 'D167';
        $form_campo_pk = 'D167_Id';
        // ID do botão que irá executar o envio desse formulário (as vezes o botão é criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'ListaSuporteBtnSubmit';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirLista/';

        //form editor
        $form_editor = array(
            'geral' => '{"D167_Nome_Lista":{"top":7,"left":82.640625,"width":312,"hidden":0},"D167_D166_Id":{"top":30,"left":69.1875,"width":319,"hidden":0},"D167_Nivel_Prioridade":{"top":53,"left":55.734375,"width":320,"hidden":0},"D167_Horas_Validade":{"top":76,"left":31.484375,"width":56,"hidden":0},"D167_Flag_Finalizar_Chamado":{"top":99,"left":404.609375,"width":64,"hidden":1},"D167_Mover_Lista_Id":{"top":122,"left":52.703125,"width":319,"hidden":1},"D167_Flag_Enviar_Detalhes":{"top":122,"left":279.9375,"width":64,"hidden":1}}',
        );

        // Definição do Formulário
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'D167_Nivel_Prioridade' => array('name' => 'D167_Nivel_Prioridade', 'label' => 'Prioridade', 'type' => 'select', 'select' => array(
                            array('title' => '...', 'value' => '0', 'selected' => true),
                            array('title' => 'Alta', 'value' => '1'),
                            array('title' => 'Média', 'value' => '2'),
                            array('title' => 'Baixa', 'value' => '3'),
                        ),
                    ),
                    'D167_Nome_Lista' => array('name' => 'D167_Nome_Lista', 'label' => 'Lista', 'type' => 'text'),
                    'D167_D166_Id' => array('name' => 'Consulta_D167_D166_Id', 'label' => 'Quadro', 'type' => 'select', 'select' =>
                        gGeraSelect('D166', 'D166_Id', 'D166_Grupo_Suporte', "order by D166_Id", true),
                    ),
                    'D167_Horas_Validade' => array('name' => 'D167_Horas_Validade', 'label' => 'Horas Validade', 'type' => 'text'),
                    //'divIdRefresh' => array('name' => 'divIdRefresh', 'label'=>'Implementacao', 'type' => 'hidden', 'value' => $r_divIdRefresh, 'reject' => true),
                    'D167_Mover_Lista_Id' => array('name' => 'D167_Mover_Lista_Id', 'label' => 'Mover lista', 'type' => 'select', 'select' =>
                        gGeraSelect('D167','D167_Id','D166_Grupo_Suporte, D167_Nome_Lista','LEFT JOIN D166 ON D167_D166_Id=D166_Id ORDER BY D166_Grupo_Suporte',true,$g['usuarioAtual']),
                    ),
                    'D167_Flag_Enviar_Detalhes' => array('name' => 'D167_Flag_Enviar_Detalhes', 'label' => 'Enviar detalhes?', 'type' => 'select', 'select' => array(
                        array('title' => 'Sim', 'value' => 'S', 'selected' => true),
                        array('title' => 'Não', 'value' => 'N'),
                    ),
                    ),
                    'D167_Flag_Finalizar_Chamado' => array('name' => 'D167_Flag_Finalizar_Chamado', 'label' => 'Permitir finalizar chamado?', 'type' => 'select', 'select' => array(
                        array('title' => 'Sim', 'value' => 'S'),
                        array('title' => 'Não', 'value' => 'N', 'selected' => true),
                    ),
                    ),
                ),
            ),
        );
        $form = new Formulario('formIncluirLista');
        $form->definirFormularioArray($formArray);

        /**
         * PARTE 3: Processamento do formulário (se foi enviado)
         * E carregamento dos valores (se não foi enviado parametro acaoId)
         */
        if ($formCodigo = $form->processarFormulario($form_tabela)) {
            // Form com erros
            if ($formCodigo == 1) {
                echo gGeraAlertMsgErroForm($form);
                // Form sem erros
            } else if ($formCodigo == 2) {
                // Insert ou Update?
                if (!empty($r_acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $r_acaoId, $form)) {
                        // Fecha a janela, recarrega o parent (grid) e emite uma mensagem de alerta
                        $alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        // Fecha a janela, recarrega o parent (grid), emite uma mensagem de alerta e abre o registro para alteração (o que acabou de ser incluido)
                        $alert .= gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
                        //$divIdRefresh = $form->campoValorEnviado('divIdRefresh');
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								// Fecha a janela, recarrega o grid e abre a alteração
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										{$alert}
									}, 150);
	  							});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								//divRefresh('{$divIdRefresh}');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($r_acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $r_acaoId);
                // Mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
                }
            }
        }

        /**
         * PARTE 4: Gerar o formulário
         */
        $gerado = gGeraCamposForm($form, $tab, $r_acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk);

        /**
         * PARTE 5: Exibir o formulário
         */
        $g['smarty']->assign('formularioID', $form->formularioID());
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesMid = $botoesEsq = $botoesDir = '';
        $botoesDir .= '<button type="submit" id="ListaSuporteBtnSubmit">' . (empty($r_acaoId) ? 'Incluir' : 'Alterar') . '</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\'); return false;" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesMid', $botoesMid);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'gridSuporteErro':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => '(SELECT CONCAT(GROUP_CONCAT(DISTINCT T252_Usuario ORDER BY T252_Data_Hora ASC SEPARATOR ", "),IF(COUNT(*) > 1, CONCAT(" (",COUNT(*),")"),"")) FROM T252 WHERE T252_T234_Id = T234_Id)', 'colunaTitulo' => 'Interações', 'style' => 'width: 1%'),
            array('colunaValor' => 'SUBSTRING(D024_Nome_Fantasia,1,25)', 'colunaTitulo' => 'Empresa', 'style' => 'width: 1%'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'C007_Primeiro_Nome', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => '(SELECT IF(T252_C007_Id > 0 AND T252_C007_Id_Cliente <= 0, T252_Flag_Lido, "") FROM T252 WHERE T252_T234_Id = T234_Id ORDER BY T252_Data_Hora DESC LIMIT 1)', 'colunaTitulo' => 'Lido', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T252_Data_Hora', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
            array('colunaValor' => 'T252_C007_Id', 'colunaTitulo' => 'T252_C007_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T252_C007_Id_Cliente', 'colunaTitulo' => 'T252_C007_Id_Cliente', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Email_Solicitante', 'colunaTitulo' => 'T234_Email_Solicitante', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
        );

        /**
         * Definição da SQL
         */
        $from = "T234";

        $extra = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join C007 on C007_Id=T234_C007_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
			WHERE T234_Flag_Tipo_OS = 'E'
			AND T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
			AND T234_Flag_Finalizado != 'S'
			GROUP BY T234_Id
			ORDER BY IF(IFNULL(T252_Id,0) > 0, T252_Data_Hora, T234_Data_Hora_Abertura) DESC
EOT;
        $extraTotal = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join C007 on C007_Id=T234_C007_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
			WHERE T234_Flag_Tipo_OS = 'E'
			AND T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
			AND T234_Flag_Finalizado != 'S'
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            //'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'D024_Nome_Fantasia' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Chamado', 'tipo' => 'texto'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            if (strtotime($array['todosCampos']['T252_Data_Hora']) < strtotime(date('Y-m-d H:i:s', strtotime("-2 days")))) {
                if ($array['todosCampos']['T252_C007_Id'] > 0) {
                    return 'linhaVermelho';
                }
            }
            if ($array['todosCampos']['T234_C007_Id'] <= 0 || $array['todosCampos']['T252_C007_Id_Cliente'] > 0) {
                return 'linhaNegrito';
            }
        }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Botões (Release Acima do GRID)
         */
        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(DISTINCT T234_Id)", $from, $extraTotal, false, false);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="btnIncluirSuporteErro">Incluir</button>
				<span class="item" style="text-align:right; float: right; margin-right: 85px;">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#btnIncluirSuporteErro').unbind('click').bind('click', function(data) {
					abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Requisição de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=E&acessoSuporte=true&D024_Id=' + encodeURIComponent('{$r_D024_Id}'), [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha') && data.target.nodeName != 'DIV') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [1000, 600]);
			});
		} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuporteDuvida':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => '(SELECT CONCAT(GROUP_CONCAT(DISTINCT T252_Usuario ORDER BY T252_Data_Hora ASC SEPARATOR ", "),IF(COUNT(*) > 1, CONCAT(" (",COUNT(*),")"),"")) FROM T252 WHERE T252_T234_Id = T234_Id)', 'colunaTitulo' => 'Interações', 'style' => 'width: 1%'),
            array('colunaValor' => 'SUBSTRING(D024_Nome_Fantasia,1,25)', 'colunaTitulo' => 'Empresa', 'style' => 'width: 1%'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'C007_Primeiro_Nome', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => '(SELECT IF(T252_C007_Id > 0 AND T252_C007_Id_Cliente <= 0, T252_Flag_Lido, "") FROM T252 WHERE T252_T234_Id = T234_Id ORDER BY T252_Data_Hora DESC LIMIT 1)', 'colunaTitulo' => 'Lido', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T252_Data_Hora', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
            array('colunaValor' => 'T252_C007_Id', 'colunaTitulo' => 'T252_C007_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T252_C007_Id_Cliente', 'colunaTitulo' => 'T252_C007_Id_Cliente', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Email_Solicitante', 'colunaTitulo' => 'T234_Email_Solicitante', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
        );

        /**
         * Definição da SQL
         */
        $from = "T234";

        $extra = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join C007 on C007_Id=T234_C007_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
			WHERE T234_Flag_Tipo_OS = 'D'
			AND T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
			AND T234_Flag_Finalizado != 'S'
			GROUP BY T234_Id
			ORDER BY IF(IFNULL(T252_Id,0) > 0, T252_Data_Hora, T234_Data_Hora_Abertura) DESC
EOT;
        $extraTotal = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join C007 on C007_Id=T234_C007_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
			WHERE T234_Flag_Tipo_OS = 'D'
			AND T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
			AND T234_Flag_Finalizado != 'S'
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            if (strtotime($array['todosCampos']['T252_Data_Hora']) < strtotime(date('Y-m-d H:i:s', strtotime("-2 days")))) {
                if ($array['todosCampos']['T252_C007_Id'] > 0) {
                    return 'linhaVermelho';
                }
            }
            if ($array['todosCampos']['T234_C007_Id'] <= 0 || $array['todosCampos']['T252_C007_Id_Cliente'] > 0) {
                return 'linhaNegrito';
            }
        }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        //echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Botões (Release Acima do GRID)
         */
        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(DISTINCT T234_Id)", $from, $extraTotal, false, false);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="btnIncluirSuporteDuvida">Incluir</button>
				<span class="item" style="text-align:right; float: right; margin-right: 85px;">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#btnIncluirSuporteDuvida').unbind('click').bind('click', function(data) {
					abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Requisição de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=D&acessoSuporte=true&D024_Id=' + encodeURIComponent('{$r_D024_Id}'), [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha') && data.target.nodeName != 'DIV') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
			});
		} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuporteMelhoria':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'colunaExibir' => false),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => '(SELECT CONCAT(GROUP_CONCAT(DISTINCT T252_Usuario ORDER BY T252_Data_Hora ASC SEPARATOR ", "),IF(COUNT(*) > 1, CONCAT(" (",COUNT(*),")"),"")) FROM T252 WHERE T252_T234_Id = T234_Id)', 'colunaTitulo' => 'Interações', 'style' => 'width: 1%'),
            array('colunaValor' => 'SUBSTRING(D024_Nome_Fantasia,1,25)', 'colunaTitulo' => 'Empresa', 'style' => 'width: 1%'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'C007_Primeiro_Nome', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => '(SELECT IF(T252_C007_Id > 0 AND T252_C007_Id_Cliente <= 0, T252_Flag_Lido, "") FROM T252 WHERE T252_T234_Id = T234_Id ORDER BY T252_Data_Hora DESC LIMIT 1)', 'colunaTitulo' => 'Lido', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'colunaExibir' => false),
            array('colunaValor' => 'T252_Data_Hora', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
            array('colunaValor' => 'T252_C007_Id', 'colunaTitulo' => 'T252_C007_Id', 'colunaExibir' => false),
            array('colunaValor' => 'T252_C007_Id_Cliente', 'colunaTitulo' => 'T252_C007_Id_Cliente', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Email_Solicitante', 'colunaTitulo' => 'T234_Email_Solicitante', 'colunaExibir' => false),
        );

        /**
         * Definição da SQL
         */
        $from = "T234";

        $extra = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join C007 on C007_Id=T234_C007_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
			WHERE T234_Flag_Tipo_OS = 'M'
			AND T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
			AND T234_Flag_Finalizado != 'S'
			GROUP BY T234_Id
			ORDER BY IF(IFNULL(T252_Id,0) > 0, T252_Data_Hora, T234_Data_Hora_Abertura) DESC
EOT;
        $extraTotal = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join C007 on C007_Id=T234_C007_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
			WHERE T234_Flag_Tipo_OS = 'M'
			AND T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
			AND T234_Flag_Finalizado != 'S'
EOT;
        /**
         * Filtro
         */
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            if (strtotime($array['todosCampos']['T252_Data_Hora']) < strtotime(date('Y-m-d H:i:s', strtotime("-2 days"))) && $array['todosCampos']['T252_C007_Id'] > 0) {
                //finalizarChamadoAbandonado($array['todosCampos']['T234_Id'], $array['todosCampos']['T234_Email_Solicitante']);
                //return 'linhaVermelho';
            }
            if ($array['todosCampos']['T234_C007_Id'] <= 0 || $array['todosCampos']['T252_C007_Id_Cliente'] > 0) {
                return 'linhaNegrito';
            }
        }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Botões (Release Acima do GRID)
         */
        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(DISTINCT T234_Id)", $from, $extraTotal, false, false);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="btnIncluirSuporteMelhoria">Incluir</button>
				<span class="item" style="text-align:right; float: right; margin-right: 85px;">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#btnIncluirSuporteMelhoria').unbind('click').bind('click', function(data) {
					abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Requisição de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=M&acessoSuporte=true&D024_Id=' + encodeURIComponent('{$r_D024_Id}'), [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha') && data.target.nodeName != 'DIV') { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
			});
		} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuporteFinalizado':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'retornar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'retornarDesenvolvimento', 'callbackParameter' => '0', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'prosseguir', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '2', 'colunaTipo' => 'livre'),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Nome técnico', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'C007_Id', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
		left join D024 on D024_Id=T234_D024_Id
		left join C007 on C007_Id=T234_C007_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T235 on T235_Id=T234_T235_Id
		left join C025 on C025_D024_Id=D024_Id
		LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
		WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
		AND T234_Flag_Finalizado = 'S'
		GROUP BY T234_Id
		ORDER BY T234_Id desc
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        $valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridInteracoes':
        log("acaoSuporte grid:" . $r_acessoSuporte);
        require_once "prog/outros/registroSuporteCliente/form_func-js.php";
        echo <<<EOT
  			<div id="divChat" style="box-sizing:border-box; padding: 10px; overflow-y: scroll;">
EOT;
        $sqlT252 = mysql_query("SELECT * FROM T252 LEFT JOIN T234 ON T234_Id=T252_T234_Id LEFT JOIN D024 ON D024_Id=T234_D024_Id WHERE T252_T234_Id = '{$r_acaoId}'");
        while ($rowT252 = mysql_fetch_array($sqlT252)) {
            $cor = '#FFF';
            if ($primeiroRegistro == false) {
                $cor = '#C1FFB6';
            }
            $conteudo = array(
                'mensagem' => $rowT252['T252_Descricao'],
                'data' => date("d/m/Y H:i:s", strtotime($rowT252['T252_Data_Hora'])),
                'responsavel' => $rowT252['T252_Usuario'],
                'empresa' => substr($rowT252['D024_Nome_Empresa'], 0, 20),
            );
            if ($ultimaInteracao != $rowT252['T252_C007_Id_Cliente']) {
                $logo = ($rowT252['T252_C007_Id_Cliente'] > 0) ? $rowT252['T234_Logo_Empresa'] : "http://hardness.hardness.com.br/hardness3/dados_usuarios/e001/imagens/logo/1.jpg";
            }

            echo montarBalaoInteracao($conteudo, ($rowT252['T252_C007_Id_Cliente'] > 0) ? 'R' : 'E', $logo, $cor, $rowT252, $r_acessoSuporte);
            $ultimaInteracao = $rowT252['T252_C007_Id_Cliente'];
            $primeiroRegistro = true;
        }
        echo <<<EOT
  			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

				$(document).ready(function(){
	            	$("#tabelaNotas td").click(function() {

						var nota = parseInt( $(this).index() ) + 1;

						var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/avaliacaoCliente/?ajax=true&nota='+ nota + '&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									divRefresh('{$g['divId']}');
									dialogConfirm('Avaliado', request.data);
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Avaliação atendimento', 'Gostaria de confirmar a avaliação?', \$buttons);
					});
              	});


  				$("#divChat").animate({ scrollTop: $('#divChat')[0].scrollHeight}, 1000);
  				setTimeout(function(){
  					var divHeight = parseFloat($('#{$g['divId']}').css('height'));
  					$('#divChat').css('height', divHeight);
  				}, 100);
			} </script></div>
EOT;
    break;

    case 'gridImplementacoes':
        /**
         * Definição do Grid (Tabela)
         */

        $grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T236_Id', $g['divId'])),
            array('colunaValor' => 'T236_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'Responsavel.C007_Nome', 'colunaTitulo' => 'Responsável'),
            array('colunaValor' => 'Inseriu.C007_Nome', 'colunaTitulo' => 'Registrou'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'T236_Descricao', 'colunaTitulo' => 'Descrição'),
            array('colunaValor' => 'T236_Solicitante', 'colunaTitulo' => 'Solicitante'),
            array('colunaValor' => 'T236_Data_Inserido', 'colunaTitulo' => 'Data inserido', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T236_Data_Inicio', 'colunaTitulo' => 'Data Início', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T236_Data_Finalizado', 'colunaTitulo' => 'Data finalizado', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T236_Tempo_Estimado', 'colunaTitulo' => 'Tempo Estimado', 'colunaEditar' => true),
            array('colunaValor' => 'T236_Valor_Hora', 'colunaTitulo' => 'Valor hora'),
            array('colunaValor' => "(SELECT TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, T236_Data_Inicio, T236_Data_Finalizado)),'%H:%i:%s') FROM T236 AS T236_2 WHERE T236_2.T236_Id = T236_1.T236_Id) as Duracao", 'colunaTitulo' => 'Duração'),
            array('colunaValor' => '', 'colunaTitulo' => 'Total R$', 'callback' => 'validaTotalReais', 'colunaTipo' => 'livre'),
        );

        /**
         * Definição da SQL
         */
        $from = "T236 AS T236_1";

        $extra = <<<EOT
		left join D024 on D024_Id=T236_D024_Id
		left join C007 AS Responsavel on Responsavel.C007_Id=T236_C007_Id_Responsavel
		left join C007 AS Inseriu on Inseriu.C007_Id=T236_C007_Id_Inserido
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T236_Data_Inserido' => array('titulo' => 'Cadastro', 'tipo' => 'data'),
            'Responsavel.C007_Nome' => array('titulo' => 'Responsável', 'tipo' => 'texto'),
            'Inseriu.C007_Nome' => array('titulo' => 'Registrou', 'tipo' => 'texto'),
            'T236_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'T236_Data_Inicio' => array('titulo' => 'Inicio', 'tipo' => 'data'),
            'T236_Data_Finalizado' => array('titulo' => 'Finalizado', 'tipo' => 'data'),
            'D024_Nome_Empresa' => array('titulo' => 'Empresa', 'tipo' => 'texto'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        // function gridLinha($array) {
        //     global $g;
        //     if($array['todosCampos']['T234_Flag_Finalizado']=='N'){
        //         return 'linhaVermelho';
        //     }
        // }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
         * Botões (Release Acima do GRID)
         */
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="registroSuporteClienteBtnIncluirImplementacao">Incluir</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#registroSuporteClienteBtnIncluirImplementacao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirPendencia/','', [1000,700]);
				});

			} </script></div>
EOT;
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
        $valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T236_Data_Inicio, T236_Data_Finalizado) FROM T236 AS T236_2 WHERE T236_2.T236_Id = T236_1.T236_Id))),'%H:%i:%s')", $from, $extra);
        $tempoEstimado = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIME_TO_SEC(T236_Tempo_Estimado) FROM T236 AS T236_2 WHERE T236_2.T236_Id = T236_1.T236_Id))),'%H:%i:%s')", $from, $extra);
        $totalReais = validaTotalReaisSum($extra);

        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<span class="item" style="text-align:right">Duração Total<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorDuracao}" size="11" readonly></span>
				<span class="item" style="text-align:right">Estimado Total<br><input type="text" class="whiteBg" id="inputRecebido" value="{$tempoEstimado}" size="11" readonly></span>
				<span class="item" style="text-align:right">Total Reais<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalReais}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T236_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirPendencia/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [1000,700]);
			});
		} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}

    break;

    case 'gridAtividadesUsuarios':
        /**
         * Definição do Grid (Tabela)
         */

        $grid = array(
            array('colunaValor' => 'P001_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'P001_Data', 'colunaTitulo' => 'Data', 'style' => 'width: 1%', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'width: 1%'),
            array('colunaValor' => 'P001_Login', 'colunaTitulo' => 'Login', 'style' => 'width: 1%'),
            array('colunaValor' => 'P001_Hits', 'colunaTitulo' => 'Requisições', 'style' => 'width: 1%', 'colunaAlinhar' => 'left', 'callback' => 'gCorrigeNumero', 'callbackParameter' => 0),
            array('colunaValor' => 'P001_Size', 'colunaTitulo' => 'Dados Transferidos', 'colunaAlinhar' => 'left', 'callback' => 'formatBytes'),
        );

        /**
         * Definição da SQL
         */
        $from = "P001";

        $extra = <<<EOT
		left join D024 on D024_Id=P001_D024_Id
		ORDER BY P001_Hits DESC
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'P001_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'text'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        // function gridLinha($array) {
        //     global $g;
        //     if($array['todosCampos']['T234_Flag_Finalizado']=='N'){
        //         return 'linhaVermelho';
        //     }
        // }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
         * Botões (Release Acima do GRID)
         */
        echo <<<EOT
			<div class="buttonsBottomTop">
				<!--<button type="button" id="registroSuporteClienteBtnIncluirImplementacao">Incluir</button>-->
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#registroSuporteClienteBtnIncluirImplementacao').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirPendencia/','', [550,700]);
				});

			} </script></div>
EOT;
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalHits = $g['sqlAuto']->pegarSqlCampo("SUM(P001_Hits)", $from, $extra);
        $totalHits = gCorrigeNumero($totalHits, 0);
        $totalSize = $g['sqlAuto']->pegarSqlCampo("SUM(P001_Size)", $from, $extra);
        $totalSize = formatBytes(array('campoAtual' => $totalSize));
        $totalUsuarios = $g['sqlAuto']->pegarSqlCampo("COUNT(DISTINCT P001_Login)", $from, $extra);
        $totalUsuarios = gCorrigeNumero($totalUsuarios, 0);
        $totalEmpresas = $g['sqlAuto']->pegarSqlCampo("COUNT(DISTINCT P001_D024_Id)", $from, $extra);
        $totalEmpresas = gCorrigeNumero($totalEmpresas, 0);

        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Requisições<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalHits}" size="11" readonly></span>
				<span class="item" style="text-align:right">Dados Transferidos<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalSize}" size="11" readonly></span>
				<span class="item" style="text-align:right">Usuários<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalUsuarios}" size="11" readonly></span>
				<span class="item" style="text-align:right">Empresas<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalEmpresas}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			/*$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T236_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirPendencia/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [650,700]);
			});*/
		} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}

    break;

    case 'contentPacotes':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tab/tabPacotes', '&tab=geral&T272_Id=' . urlencode($r_acaoId) . '&acaoId=' . urlencode($r_acaoId)),
        );
        echo gProcessaAcoes($actions);

    break;

    case 'contentInteracoes':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;
        $divIdContent = $g['divId'];

        $sql = mysql_query("SELECT * FROM T234 LEFT JOIN T252 ON T252_T234_Id=T234_Id WHERE T234_Id = '{$acaoId}' AND T252_Flag_Lido = 'N'");
        if (mysql_num_rows($sql) > 0) {
            while ($T234 = mysql_fetch_array($sql)) {
                if ($g['usuarioAtual'] == $T234['T234_C007_Id_Cliente']) {
                    mysql_query("UPDATE T252 SET T252_Flag_Lido = 'S' WHERE T252_Id = '{$T234['T252_Id']}'");
                }
            }
        }

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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabInteracoes/', '&acaoId=' . urlencode($acaoId) . '&divIdContent=' . urlencode($divIdContent) . '&flagVendedor=' . $g['C007']['C007_Flag_Vendedor']),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'contentGridInteracoes':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;
        $sql = mysql_query("SELECT * FROM T234 LEFT JOIN T252 ON T252_T234_Id=T234_Id WHERE T234_Id = '{$acaoId}' AND T252_Flag_Lido = 'N'");
        if (mysql_num_rows($sql) > 0) {
            while ($T234 = mysql_fetch_array($sql)) {
                $flagFinalizado = $T234['T234_Flag_Finalizado'];
                if ($g['usuarioAtual'] == $T234['T234_C007_Id_Cliente']) {
                    mysql_query("UPDATE T252 SET T252_Flag_Lido = 'S' WHERE T252_Id = '{$T234['T252_Id']}'");
                }
            }
        }
        $sql = mysql_query("SELECT T234_Flag_Finalizado FROM T234 WHERE T234_Id = '{$acaoId}'");
        $row = mysql_fetch_array($sql);
        /*
         * Divisão da Area em Conteúdos
         */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        if ($row['T234_Flag_Finalizado'] == 'S') {
            $content1 = uniqid();
            $g['smarty']->assign('content1', $content1);
            $g['smarty']->assign('tipo', 'unico');
            // O que será carregado em cada conteúdo?
            $actions = array(
                // identificador, url, paramentros get (opcional)
                array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/gridInteracoes/', '&acaoId=' . urlencode($acaoId)),
            );
        } else {
            $content1 = uniqid();
            $content2 = uniqid();
            $g['smarty']->assign('content1', $content1);
            $g['smarty']->assign('content2', $content2);
            $g['smarty']->assign('tipo', '2linhas');
            // O que será carregado em cada conteúdo?
            $actions = array(
                // identificador, url, paramentros get (opcional)
                array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/gridInteracoes/', '&acaoId=' . urlencode($acaoId)),
                array($content2, '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirInteracao/', '&T234_Id=' . urlencode($r_acaoId) . '&divRefresh=' . urlencode($r_divIdRefresh)),
            );
        }
        echo gProcessaAcoes($actions);
    break;

    case 'contentIncluirInteracao':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirInteracao/', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'contentIncluir':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabIncluir/', '&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'contentIncluirPendencia':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabIncluirPendencia/', '&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'contentVersao':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/formVersao/', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'contentChamadosVersao':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabChamadosVersao/', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'contentCabecalhoChamado':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/formCabecalhoChamado/', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'contentCabecalhoChamadoCliente':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/formCabecalhoChamadoCliente/', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'tabInteracoes':
        $acaoId = isset($r_acaoId) ? $r_acaoId : false;
        $divIdContent = isset($r_divIdContent) ? $r_divIdContent : false;

        if (isset($r_habilitarInclusao)) {
            /**
             * Cabeçalho (Release Acima da TAB)
             */
            $cabecalhoId = uniqid();
            echo '<div id="' . $cabecalhoId . '" class="contentCabecalho" style="height: 38px;"></div>';
            $actions = array(
                // identificador, url, paramentros get (opcional)
                array($cabecalhoId, '/outros/registroSuporteCliente/registroSuporteCliente/cabecalhoSuporte/', '&acaoId=' . urlencode($r_acaoId) . '&divIdContent=' . urlencode($r_divIdContent) . '&habilitarInclusao=' . $r_habilitarInclusao),
            );
            echo gProcessaAcoes($actions);
        } else {
            /**
             * Cabeçalho (Release Acima da TAB)
             */
            $cabecalhoId = uniqid();
            echo '<div id="' . $cabecalhoId . '" class="contentCabecalho" style="height: 38px;"></div>';
            $actions = array(
                // identificador, url, paramentros get (opcional)
                array($cabecalhoId, '/outros/registroSuporteCliente/registroSuporteCliente/cabecalhoSuporteCliente/',  '&divIdContent=' . urlencode($r_divIdContent) . '&acaoId=' . urlencode($r_acaoId), true),
            );
            echo gProcessaAcoes($actions);
        }
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);
        $contentId = uniqid();
        // Definição das Tabs
        log("Chamado:" . $acaoId);
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array($contentId, '/outros/registroSuporteCliente/registroSuporteCliente/contentGridInteracoes/', 'Interações', '&tab=geral&acaoId=' . urlencode($acaoId) . '&divIdRefresh=' . urlencode($contentId)),
        );
        if (isset($r_habilitarInclusao)) {
            $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/formAnaliseDesenvolvimento/', 'Análise', '&tab=geral&acaoId=' . urlencode($acaoId));
            $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridHistoricoDesenvolvimento/', 'Histórico', '&tab=geral&acaoId=' . urlencode($acaoId));
            $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/formTesteDesenvolvimento/', 'Teste', '&tab=geral&acaoId=' . urlencode($acaoId));
            $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDetalhesIntegracao/', 'Detalhes trello', '&tab=geral&acaoId=' . urlencode($acaoId));
            $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteRetrabalho/', 'Retrabalho', '&tab=geral&acaoId=' . urlencode($acaoId));
            $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteHorasTrabalhadas/', 'Horas desenvolvimento', '&tab=geral&acaoId=' . urlencode($acaoId));

            $sqlT234 = mysql_query("SELECT T234_Info_Parceiro FROM T234 WHERE T234_Id='{$acaoId}'");
            $T234 = mysql_fetch_array($sqlT234);

            if (!empty($T234['T234_Info_Parceiro'])) {
                $tabsList[] = array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/outroSuporteInfoParceiro/', 'Info', '&tab=geral&acaoId=' . urlencode($acaoId));
            }
        }

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabIncluir':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/form/', 'Registro Suporte', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabIncluirInteracao':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirInteracao/', 'Interação', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabIncluirPendencia':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirSuporte/', 'Registro Suporte', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'tabChamadosVersao':
        /**
         * Cabeçalho (Release Acima da TAB)
         */
        $cabecalhoId = uniqid();
        echo '<div id="' . $cabecalhoId . '" class="contentCabecalho" style="height: 38px;"></div>';
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($cabecalhoId, '/outros/registroSuporteCliente/registroSuporteCliente/cabecalhoVersao/', '&T267_Id=' . urlencode($r_T267_Id), true),
        );
        echo gProcessaAcoes($actions);
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridChamadosVersao/', 'Chamados', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'form':
        include 'prog/etc/etc008/grid_func-js.php';

        $sqlT234 = mysql_query("SELECT * FROM T234 WHERE T234_Id = '{$r_acaoId}'");
        if (mysql_num_rows($sqlT234) > 0) {
            $mT234 = mysql_fetch_array($sqlT234);
        }
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

        $T234_D024 = mysql_query("SELECT * FROM T234 WHERE T234_Id='{$acaoId}'");
        $mT234_D024 = mysql_fetch_array($T234_D024);

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
        $form_tabela = 'T234';
        $form_campo_pk = 'T234_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'incluirChamadoTelefonico_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/form/';

        // novo formulário
        $form = new Formulario('formRegistroSuporteCliente');
        // Editor
        $form_editor = array(
            'geral' => '{"T234_D024_Id":{"top":7,"left":71,"width":345,"hidden":0},"T234_D013_Id":{"top":30,"left":73,"width":367,"hidden":0},"T234_Descricao":{"top":53,"left":65,"height":108,"width":360,"hidden":0},"T234_Observacao_Finalizado":{"top":168,"left":7,"height":108,"width":360,"hidden":0},"T234_T235_Id":{"top":283,"left":91,"width":207,"hidden":0},"T234_Flag_Finalizado":{"top":306,"left":73,"width":59,"hidden":0},"T234_Solicitante":{"top":306,"left":601,"width":360,"hidden":1},"T234_C007_Id":{"top":329,"left":84,"width":207,"hidden":0},"T234_T236_Id":{"top":352,"left":48,"width":57,"hidden":0}}',
        );

        if (empty($r_acaoId)) {
            $WHERE_D013 = ' WHERE D013_Id=0 ';
        } else {
            $WHERE_D013 = " WHERE D013_D024_Id='{$mT234_D024['T234_D024_Id']}'";
        }

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T234_D024_Id' => array('name' => 'T234_D024_Id', 'label' => 'Empresa', 'type' => 'pesquisa', 'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa'), 'extra' => "onchange=\"carregaContatosSuporte(this, '{$g['divId']}');;\""),
                    'T234_C007_Id' => array('name' => 'T234_C007_Id', 'label' => 'Usuário', 'type' => 'select', 'select' =>
                        gGeraSelect('C007', 'C007_Id', 'C007_Nome', 'order by C007_Nome', true, $g['usuarioAtual']),
                    ),
                    'T234_Data_Hora_Abertura' => array('name' => 'T234_Data_Hora_Abertura', 'label' => '', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s')),
                    'T234_Flag_Tipo_Suporte' => array('name' => 'T234_Flag_Tipo_Suporte', 'label' => '', 'type' => 'hidden', 'value' => 'T'),
                    'T234_Solicitante' => array('name' => 'T234_Solicitante', 'label' => 'Solicitante', 'type' => 'text'),
                    'T234_Descricao' => array('name' => 'T234_Descricao', 'label' => 'Descrição', 'type' => 'textarea'),
                    'T234_T235_Id' => array('name' => 'T234_T235_Id', 'label' => 'Grupo', 'type' => 'select', 'select' =>
                        gGeraSelect('T235', 'T235_Id', 'T235_Descricao', 'order by T235_Descricao', true),
                    ),
                    'T234_Flag_Finalizado' => array('name' => 'T234_Flag_Finalizado', 'label' => 'Finalizado', 'type' => 'select', 'select' => array(
                        array('title' => 'Sim', 'value' => 'S'),
                        array('title' => 'Não', 'value' => 'N', 'selected' => true),
                    ),
                    ),
                    'T234_Observacao_Finalizado' => array('name' => 'T234_Observacao_Finalizado', 'label' => 'Observação finalização', 'type' => 'textarea'),
                    'T234_T236_Id' => array('name' => 'T234_T236_Id', 'label' => 'Implementacao', 'type' => 'pesquisa', 'pesquisa' => array('T236', 'T236_Id', 'T236_Id')),
                    'T234_Origem_Chamado' => array('name' => 'T234_Origem_Chamado', 'label' => 'Origem', 'type' => 'hidden', 'value' => "I"),
                    'T234_D013_Id' => array('name' => 'T234_D013_Id', 'label' => 'Solicitante', 'type' => 'select', 'select' =>
                        gGeraSelect('D013', 'D013_Id', 'D013_Nome_Contato,D013_E_Mail', $WHERE_D013, true),
                    ),
                ),
            ),
        );
        $form->definirFormularioArray($formArray);
        if (!empty($mT234['T234_Link_Screenshot'])) {
            echo "<a href='{$mT234['T234_Link_Screenshot']}'>Ver tela</a>";
        }
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
                // Insert ou Update?
                if (!empty($acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//divRefresh('{$g['divId']}');
									}, 150);
									});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        //echo gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
                        $D024_Id = $form->campoValorEnviado('T234_D024_Id');

                        $D024 = mysql_query("SELECT * FROM D024
							LEFT JOIN C025 ON C025_D024_Id=D024_Id
						WHERE D024_Id='{$D024_Id}'");
                        $mD024 = mysql_fetch_array($D024);

                        $mD024['D024_Link'] = strtolower($mD024['D024_Link']);

                        if (!empty($mD024['D024_Link'])) {

                            echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											divRefresh('{$g['divId']}');
											window.open('{$mD024['D024_Link']}', '', 'height=650, width=1200, top=20, left=20, scrollbars=no, resizable=no');
										}, 150);
									});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
								} </script></div>
EOT;
                        } else {
                            echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
											divRefresh('{$g['divId']}');
											acessar1('{$mD024['C025_Banco_Dados']}', 'root','root', '{$mD024['C025_Senha']}', '{$D024_Id}','{$g['usuarioAtual']}');
										}, 150);
									});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
								} </script></div>
EOT;

                        }

                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
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
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        if ($r_acaoId > 0) {
            $T234 = mysql_query("SELECT * FROM T234 WHERE T234_T234_Id='{$r_acaoId}'");
            $mT234 = mysql_fetch_array($T234);
            if (mysql_num_rows($T234) > 0) {
                $botoesDir .= "<button type='submit' id='abrirChamadoAberto'>Chamado {$mT234['T234_Id']}</button>";
            } else {
                $botoesDir .= '<button type="submit" id="abrirChamado">Abrir Chamado</button>';
            }
        }
        $botoesDir .= '<button type="submit" id="incluirChamadoTelefonico_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');

        // Ações dos botões
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#abrirChamado').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', '{$novoIdTelaForm}', '', 'Novo chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/','&T234_Id=' + encodeURIComponent('{$r_acaoId}'), [900,550]);
				});
				$('#abrirChamadoAberto').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent('{$mT234['T234_Id']}') + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;
    break;

    case 'formIncluirSuporte':
        include 'prog/etc/etc008/grid_func-js.php';
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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
        $form_tabela = 'T236';
        $form_campo_pk = 'T236_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'registroSuporteClienteIncluirPend_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirSuporte/';

        // novo formulário
        $form = new Formulario('formRegistroSuporteCliente');
        // Editor
        $form_editor = array(
            'geral' => '{"T236_D024_Id":{"top":7,"left":49,"width":393,"hidden":0},"T236_C007_Id_Responsavel":{"top":30,"left":27,"width":223,"hidden":0},"T236_Solicitante":{"top":53,"left":41,"width":216,"hidden":0},"T236_Data_Inicio":{"top":76,"left":40,"width":121,"hidden":0},"T236_Data_Finalizado":{"top":99,"left":17,"width":120,"hidden":0},"T236_Tempo_Estimado":{"top":122,"left":13,"width":120,"hidden":0},"T236_Valor_Hora":{"top":145,"left":39,"width":120,"hidden":0},"T236_Prioridade":{"top":168,"left":42,"width":216,"hidden":0},"T236_Descricao":{"top":191,"left":41,"height":60,"width":830,"hidden":0},"T236_Especificacoes_":{"top":260,"left":16,"height":300,"width":834,"hidden":0},"T236_Analise":{"top":421,"left":54,"height":152,"width":408,"hidden":1}}',
        );

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T236_D024_Id' => array('name' => 'T236_D024_Id', 'label' => 'Empresa', 'type' => 'pesquisa', 'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa')),
                    'T236_C007_Id_Responsavel' => array('name' => 'T236_C007_Id_Responsavel', 'label' => 'Responsável', 'type' => 'select', 'select' =>
                        gGeraSelect('C007', 'C007_Id', 'C007_Nome', 'order by C007_Nome', true),
                    ),
                    'T236_C007_Id_Inserido' => array('name' => 'T236_C007_Id_Inserido', 'label' => '', 'type' => 'hidden', 'value' => $g['usuarioAtual']),
                    'T236_Data_Inserido' => array('name' => 'T236_Data_Inserido', 'label' => '', 'type' => 'hidden', 'value' => date('Y-m-d')),
                    'T236_Data_Inicio' => array('name' => 'T236_Data_Inicio', 'label' => 'Data Início', 'type' => 'text'),
                    'T236_Data_Finalizado' => array('name' => 'T236_Data_Finalizado', 'label' => 'Data Finalizado', 'type' => 'text'),
                    'T236_Solicitante' => array('name' => 'T236_Solicitante', 'label' => 'Solicitante', 'type' => 'text'),
                    'T236_Descricao' => array('name' => 'T236_Descricao', 'label' => 'Descrição', 'type' => 'textarea'),
                    'T236_Especificacoes_' => array('name' => 'T236_Especificacoes_', 'type' => 'textarea', 'label' => 'Especificações', 'uppercase' => false, 'length' => array(7, 65), 'reject' => true),
                    'T236_Especificacoes' => array('name' => 'T236_Especificacoes', 'type' => 'hidden', 'uppercase' => false),
                    'T236_Analise' => array('name' => 'T236_Analise', 'label' => 'Análise', 'type' => 'textarea'),
                    'T236_Tempo_Estimado' => array('name' => 'T236_Tempo_Estimado', 'label' => 'Tempo Estimado', 'type' => 'text'),
                    'T236_Prioridade' => array('name' => 'T236_Prioridade', 'label' => 'Prioridade', 'type' => 'text'),
                    'T236_Valor_Hora' => array('name' => 'T236_Valor_Hora', 'label' => 'Valor Hora', 'type' => 'moeda'),
                ),
            ),
        );
        $form->definirFormularioArray($formArray);

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var myEditor = new nicEditor({
					iconsPath : '/hardness3/static/img/nicEditorIcons.gif',
					buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor','upload']
				}).panelInstance('T236_Especificacoes_');
				myEditor.addEvent('blur', function() {
					if (typeof(myEditor.instanceById('T236_Especificacoes_')) != 'undefined') {
						$('#T236_Especificacoes').val(myEditor.instanceById('T236_Especificacoes_').getContent());
					}
				});
				setTimeout(function() {
					$('.nicEdit-panelContain').css({'border': '1px solid #4297D7', 'background': 'url("/hardness3/static/img/jquery.ui/ui-bg_gloss-wave_55_5c9ccc_500x100.png") repeat-x scroll 50% 50% #5C9CCC'});
					$('.nicEdit-main').height(70).css('overflow', 'auto');
					if (myEditor.instanceById('T236_Especificacoes_') != 'undefined') {
						myEditor.instanceById('T236_Especificacoes_').setContent($('#T236_Especificacoes').val());
					}
				}, 100);
			} </script></div>
EOT;

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
                // Insert ou Update?
                if (!empty($acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//divRefresh('{$g['divId']}');
									}, 150);
									});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {

                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										divRefresh('{$g['divId']}');
										divRefresh('{$g['divIdParent']}', true);
									}, 150);
								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;

                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                $form->campoSetarValor('T236_Especificacoes', htmlentities($form->campoValor('T236_Especificacoes'), ENT_COMPAT, 'UTF-8'));
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
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
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="registroSuporteClienteIncluirPend_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'gridSuporteClienteSuporteTelefonico':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Chamado', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => 'T234_Data_Hora_Finalizado', 'colunaTitulo' => 'Data finalizado', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center', 'style' => 'width:1%'),
            array('colunaValor' => "(SELECT TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado)),'%H:%i:%s') FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id) as Duracao", 'colunaTitulo' => 'Duração', 'style' => 'width: 1%'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Técnico', 'style' => 'width: 1%'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'D013_Nome_Contato', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'T234_Descricao', 'colunaTitulo' => 'Descrição', 'style' => 'max-width: 350px; white-space: normal;'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
		left join D024 on D024_Id=T234_D024_Id
		left join C007 on C007_Id=T234_C007_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T235 on T235_Id=T234_T235_Id
		left join C025 on C025_D024_Id=D024_Id
		left join D013 ON D013_Id=T234_D013_Id
		WHERE T234_Flag_Tipo_Suporte = 'T'
		AND D024_Id = '{$r_D024_Id}'
		ORDER BY T234_Id DESC
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto', 'exibir' => false),
            'D013_Nome_Contato' => array('titulo' => 'Solicitante', 'tipo' => 'texto', 'exibir' => false),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            if ($array['todosCampos']['T234_Flag_Finalizado'] == 'N') {
                return 'linhaVermelho';
            }
        }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);
        $valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<span class="item" style="text-align:right">Duração Total<br><input type="text" class="whiteBg" id="inputRecebido" value="{$valorDuracao}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;
        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'formIncluirSuporteCliente':
        include 'prog/etc/etc008/grid_func-js.php';
        $g['confUsuarioOriginal'] = empty($g['confUsuarioOriginal']) ? $confUsuario : $g['confUsuarioOriginal'];
        $T234_T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $T234_T272_Id = isset($r_T234_T272_Id) ? $r_T234_T272_Id : false;

        if (!empty($T234_T234_Id)) {
            $T234 = mysql_query("SELECT * FROM T234 LEFT JOIN D013 on D013_Id=T234_D013_Id WHERE T234_Id='{$T234_T234_Id}'");
            $mT234 = mysql_fetch_array($T234);
            $r_D024_Id = $mT234['T234_D024_Id'];
            $T234_Mensagem = $mT234['T234_Descricao'];
            $nomeUsuario = $mT234['D013_Nome_Contato'];
        } else {
            $primeiroNome = explode(" ", empty($g['C007']['C007_Primeiro_Nome']) ? $_SESSION['Nome_Usuario'] : $g['C007']['C007_Primeiro_Nome']);
            $primeiroNome = $primeiroNome[0];
            $nomeUsuario = $primeiroNome;
        }

        if (!empty($r_acessoSuporte)) {
            $C007_Id = $g['usuarioAtual'];
            $C007_Id_Cliente = 0;
        } else {
            $C007_Id = 0;
            $C007_Id_Cliente = $g['usuarioAtual'];
        }

        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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
        $form_tabela = 'T234';
        $form_campo_pk = 'T234_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'registroSuporteClienteIncluir_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirSuporteCliente/';
        // novo formulário
        $form = new Formulario('formRegistroSuporteCliente');

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T234_C007_Id' => array('name' => 'T234_C007_Id', 'label' => 'Usuário', 'type' => 'hidden', 'value' => $C007_Id),
                    'T234_C007_Id_Cliente' => array('name' => 'T234_C007_Id_Cliente', 'label' => 'Usuário', 'type' => 'hidden', 'value' => $C007_Id_Cliente),
                    'T234_Data_Hora_Abertura' => array('name' => 'T234_Data_Hora_Abertura', 'label' => '', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s')),
                    'T234_Flag_Tipo_Suporte' => array('name' => 'T234_Flag_Tipo_Suporte', 'label' => '', 'type' => 'hidden', 'value' => 'S'),
                    'T234_Flag_Tipo_OS' => array('name' => 'T234_Flag_Tipo_OS', 'label' => '', 'type' => 'hidden', 'value' => $r_tipoSuporte),
                    'T234_Flag_Finalizado' => array('name' => 'T234_Flag_Finalizado', 'label' => '', 'type' => 'hidden', 'value' => "N"),
                    'T234_Solicitante' => array('name' => 'T234_Solicitante', 'label' => 'Solicitante', 'type' => 'hidden', 'value' => $nomeUsuario),
                    'T234_Email_Solicitante' => array('name' => 'T234_Email_Solicitante', 'label' => 'Email Solicitante', 'type' => 'hidden', 'value' => $g['C007']['C007_Email']),
                    'T234_Descricao_' => array('name' => 'T234_Descricao_', 'label' => '', 'type' => 'textarea', 'reject' => true, 'uppercase' => false, 'length' => array(7, 75)),
                    'T234_Descricao' => array('name' => 'T234_Descricao', 'label' => 'Mensagem', 'type' => 'hidden', 'uppercase' => false, 'value' => $T234_Mensagem),
                    'T234_Link_Screenshot' => array('name' => 'T234_Link_Screenshot', 'label' => 'T234_Link_Screenshot', 'type' => 'hidden', 'uppercase' => false),
                    'screenshot' => array('name' => 'screenshot', 'label' => 'Descrição', 'type' => 'hidden', 'reject' => true),
                    'dirAnexos' => array('name' => 'dirAnexos', 'label' => 'Descrição', 'type' => 'hidden', 'reject' => true, 'value' => $g['divId']),
                    'T234_D013_Id' => array('name' => 'T234_D013_Id', 'label' => 'Contato', 'type' => 'hidden', 'uppercase' => false, 'value' => $mT234['T234_D013_Id']),
                    'T234_Analise' => array('name' => 'T234_Analise', 'label' => 'Contato', 'type' => 'hidden', 'uppercase' => false, 'value' => $mT234['T234_Observacao_Finalizado']),
                    'T234_T234_Id' => array('name' => 'T234_T234_Id', 'label' => 'Contato', 'type' => 'hidden', 'uppercase' => false, 'value' => $T234_T234_Id),
                    'T234_Origem_Chamado' => array('name' => 'T234_Origem_Chamado', 'label' => 'Origem', 'type' => 'hidden', 'value' => "I"),
                    'T234_T272_Id' => array('name' => 'T234_T272_Id', 'label' => 'Pacote', 'type' => 'hidden', 'select' =>
                        gGeraSelect('T272', 'T272_Id', 'T272_Mes,T272_Ano', 'WHERE IF(T272_Ano>YEAR(NOW()),T272_Ano>YEAR(NOW()),T272_Mes>MONTH(NOW()) AND T272_Ano>=YEAR(NOW()))', true, $T234_T272_Id),
                    ),
                ),
            ),
        );
        if (!empty($r_acessoSuporte)) {
            $formArray['campos']['geral']['T234_D024_Id'] = array('name' => 'T234_D024_Id', 'label' => 'Empresa', 'type' => 'pesquisa', 'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa'));
            $form_editor = array('geral' => '{"T234_D024_Id":{"top":2,"left":7,"width":409,"hidden":0},"T234_Descricao_":{"top":27,"left":10,"height":100,"width":960,"hidden":0},"T234_Descricao":{"top":7,"left":5,"height":120,"width":810,"hidden":0}}');
        } else {
            $formArray['campos']['geral']['T234_D024_Id'] = array('name' => 'T234_D024_Id', 'type' => 'hidden', 'value' => $r_D024_Id);
            $form_editor = array('geral' => '{"T234_T272_Id":{"top":-16,"left":615.640625,"width":95,"hidden":0},"T234_Descricao_":{"top":7,"left":0,"height":120,"width":810,"hidden":0}}');
        }
        $form->definirFormularioArray($formArray);

        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var myEditor = new nicEditor({
					iconsPath : '/hardness3/static/img/nicEditorIcons.gif',
					buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor','upload']
				}).panelInstance('T234_Descricao_');
				myEditor.addEvent('blur', function() {
					if (typeof(myEditor.instanceById('T234_Descricao_')) != 'undefined') {
						$('#T234_Descricao').val(myEditor.instanceById('T234_Descricao_').getContent());
					}
				});
				setTimeout(function() {
					$('.nicEdit-panelContain').css({'border': '1px solid #4297D7', 'background': 'url("/hardness3/static/img/jquery.ui/ui-bg_gloss-wave_55_5c9ccc_500x100.png") repeat-x scroll 50% 50% #5C9CCC'});
					$('.nicEdit-main').height(70).css('overflow', 'auto');
					$('.nicEdit-main').height(70).css('background-color', '#FFF');
					if (myEditor.instanceById('T234_Descricao_') != 'undefined') {
						myEditor.instanceById('T234_Descricao_').setContent($('#T234_Descricao').val());
					}
				}, 100);
		    } </script></div>
EOT;
        echo <<<EOT
			<table style="margin-left: 12px;"><tr><td>Anexos:</td><td>
			<div id="file-uploader">
			    <noscript>
			        <p>Please enable JavaScript to use file uploader.</p>
			        <!-- or put a simple form for upload here -->
			    </noscript>
			</div>
			</td></tr></table>
EOT;
        $diretorioAnexos = $g['confUsuarioOriginal']['pathRaiz'] . "dados_usuarios/{$g['confUsuarioOriginal']['dbDatabase']}/tmp/";
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// File upload
				var uploader = new qq.FileUploader({
				    element: document.getElementById('file-uploader'),
				    action: '/etc/etc006/outro/',
				    params: {'ajax': 'true', 'divId': '{$g['divId']}', 'dir': '{$diretorioAnexos}'},
				    onSubmit: function(id, fileName){ fixEditorSize(); },
				    onCancel: function(id, fileName){ fixEditorSize(); },
				});
				setTimeout(function() {
					$('.qq-upload-list').html('{$conteudoAnexos}');
				}, 200);

				function fixEditorSize() {
					setTimeout(function() {
						var divId = $('#{$g['divId']}');
						var editor = $('#emailEditorDiv');
						var width = divId.width() - 2;
						editor.find('> div').width(width);
						divId.css('overflow', 'hidden')
						editor.find('.nicEdit-main').width(width-8).css('overflow', 'auto');
						var height = divId.height() - divId.find('form').height() - editor.find('> div:eq(0)').height() - 3;
						editor.find('> div:eq(1)').height(height-100);
						editor.find('.nicEdit-main').height(height-8-100);
					}, 100);
				}
			} </script></div>
EOT;
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
                // Insert ou Update?
                if (!empty($acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//divRefresh('{$g['divId']}');
									}, 150);
									});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                } else {
                    $T234_T272_Id = $form->campoValorEnviado('T234_T272_Id');
                    if ($r_tipoSuporte == 'M' && $T234_T272_Id > 0) { //se for melhoria faz validação de quantidade de chamados
                        //Verifica a quantidade de chamados de melhoria que o cliente possue
                        $qteMelhorias = mysql_query("SELECT T135_Qtde_Maxima_Melhorias_Mes
							                           FROM T135
							                      LEFT JOIN D024 ON T135_D024_Id=D024_Id
							                          WHERE T135_D024_Id='{$r_D024_Id}'
							                            AND T135_Flag_Cancelado != 'S'
							                            AND T135_Tipo='M'");
                        $mqteMelhorias = mysql_fetch_array($qteMelhorias);
                        $chamados = mysql_query("SELECT T234_Id, COUNT(T234_Id) as QtdeChamados
							                          FROM T234
							                         WHERE T234_D024_Id='{$r_D024_Id}'
							                           AND T234_T272_Id='{$T234_T272_Id}'
							                           AND T234_Flag_Finalizado != 'S'
							                           AND T234_Flag_Arquivado != 'S'");
                        $mqtdChamados = mysql_fetch_array($chamados);
                        
                        if ($mqtdChamados['QtdeChamados'] >= $mqteMelhorias['T135_Qtde_Maxima_Melhorias_Mes']) {
                            $alert = gGeraAlertMsg('Erro', '<font color="red">Você <b>excedeu</b> a quantidade permitida de chamados de <b>MELHORIA</b> para o mês selecionado<br>Por favor, verifique seus chamados no Pacote de Atualização ou selecione outro mês</font>', false);
                            echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								{$alert};
								fecharJanela('{$g['divId']}');
								abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Requisição de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=M&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&divIdRefresh=' + encodeURIComponent('{$g['divId']}') + '{$suporteNota}', [825, 600]);
							} </script></div>
EOT;
                        } else {
                            // Verifica se o chamado já foi inserido anteriormente para evitar duplicidade
                            $T234_D024_Id = $form->campoValorEnviado('T234_D024_Id');
                            $T234_Descricao = $form->campoValorEnviado('T234_Descricao');
                            $T234_Solicitante = $form->campoValorEnviado('T234_Solicitante');
                            $sqlCheck = mysql_query("SELECT T234_Id FROM T234 WHERE T234_Solicitante = '{$T234_Solicitante}' AND T234_Descricao = '{$T234_Descricao}' AND T234_D024_Id = '{$T234_D024_Id}' AND SUBSTRING(T234_Data_Hora_Abertura,1,10) = CURDATE()");
                            if (mysql_num_rows($sqlCheck) > 0) {
                                echo gGeraAlertMsg('Erro', 'O seu chamado já foi registrado!');
                            } else {
                                // Insert
                                $screenshot = $form->campoValorEnviado('screenshot');
                                $anexoDir = "dados_usuarios/{$g['confUsuarioOriginal']['dbDatabase']}/tmp/";
                                is_dir($anexoDir) or mkdir($anexoDir, 0777, true);
                                $file = $anexoDir . uniqid() . '.html';
                                file_put_contents($file, "<img src=\"{$screenshot}\" />");
                                $linkScreenshot = $g['confUsuarioOriginal']['urlRaiz'] . 'hardness3/' . $file;
                                $form->campoSetarValor('T234_Link_Screenshot', $linkScreenshot, true);
                                if (!empty($g['C004_Local']['C004_Logo_Marca'])) {
                                    $pathinfo = pathinfo($g['C004_Local']['C004_Logo_Marca']);
                                    $caminhoImagem = "{$g['confUsuarioOriginal']['urlRaiz']}hardness3/dados_usuarios/{$g['confUsuarioOriginal']['dbDatabase']}/imagens/logo/1.{$pathinfo['extension']}";
                                    $caminhoImagem = "data:image/" . $pathinfo['extension'] . ";base64," . base64_encode(file_get_contents($caminhoImagem));
                                }
                                $form->campoSetarValor('T234_Logo_Empresa', $caminhoImagem, true);

                                $dirAnexos = $form->campoValorEnviado('dirAnexos');
                                $T234_Solicitante = $form->campoValorEnviado('T234_Solicitante');
                                $T234_Descricao = $form->campoValorEnviado('T234_Descricao');
                                $T234_C007_Id_Cliente = $form->campoValorEnviado('T234_C007_Id_Cliente');

                                if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                                    $T234_Id = $g['mysqlLastId'];
                                    rename($diretorioAnexos . $dirAnexos, $g['confUsuarioOriginal']['pathRaiz'] . $anexoDir . '/AnexosOS_' . $T234_Id);
                                    $linksAnexos = array();
                                    $dir = $anexoDir . '/AnexosOS_' . $T234_Id;
                                    if (is_dir($dir)) {
                                        $dh = opendir($dir);
                                        while (false !== ($filename = readdir($dh))) {
                                            if ($filename == '.' || $filename == '..') {continue;}
                                            $caminhoArquivo = $dir . '/' . $filename;
                                            // Foi retirado a variável &u=63 do $linkArquivo, pois quando um usuário abria um novo anexo e abria uma nova aba mudava seu usuário para root, isso ocorreu porque estava sendo passado o $u como 63.
                                            $linkArquivo = $confUsuario['urlRaiz'] . '/outros/registroSuporteCliente/registroSuporteCliente/abrirAnexo/?ajax=true&noEmp=true&arquivo=' . base64_encode($caminhoArquivo);
                                            mysql_query("INSERT INTO T253 (T253_T234_Id, T253_Nome_Arquivo, T253_Caminho_Arquivo, T253_Link_Arquivo) VALUES ('{$T234_Id}', '{$filename}', '{$caminhoArquivo}', '{$linkArquivo}')");
                                        }
                                    }
                                    $T234_Solicitante = mysql_real_escape_string($T234_Solicitante);
                                    $T234_Descricao = mysql_real_escape_string($T234_Descricao);
                                    mysql_query("INSERT INTO T252 (T252_T234_Id, T252_C007_Id_Cliente, T252_Usuario, T252_Descricao, T252_Data_Hora) VALUES ('{$T234_Id}', '{$T234_C007_Id_Cliente}', '{$T234_Solicitante}', '{$T234_Descricao}', CURRENT_TIMESTAMP())");

                                    $alert = gGeraAlertMsg('Operação OK', 'Solicitação de suporte enviada com sucesso!', false);
                                    if ($T234_T234_Id) {
                                        $novoIdTelaForm = uniqid();
                                        $abrirJanela = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent('{$T234_Id}') + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);";
                                        $alert = '';
                                    } else {
                                        $abrirJanela = '';
                                    }
                                    echo <<<EOT
										<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
											$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
												setTimeout(function() {
													{$alert}
													{$abrirJanela}
												}, 250);
											});
											$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
											divRefresh('{$r_divIdRefresh}', true);
										} </script></div>
EOT;
                                }
                            }
                        }
                    } else { 
                        //se não for melhoria continua o processo normal
                        // Verifica se o chamado já foi inserido anteriormente para evitar duplicidade
                        $T234_D024_Id = $form->campoValorEnviado('T234_D024_Id');
                        $T234_Descricao = $form->campoValorEnviado('T234_Descricao');
                        $T234_Solicitante = $form->campoValorEnviado('T234_Solicitante');
                        $sqlCheck = mysql_query("SELECT T234_Id FROM T234 WHERE T234_Solicitante = '{$T234_Solicitante}' AND T234_Descricao = '{$T234_Descricao}' AND T234_D024_Id = '{$T234_D024_Id}' AND SUBSTRING(T234_Data_Hora_Abertura,1,10) = CURDATE()");
                        if (mysql_num_rows($sqlCheck) > 0) {
                            echo gGeraAlertMsg('Erro', 'O seu chamado já foi registrado!');
                        } else {
                            // Insert
                            $screenshot = $form->campoValorEnviado('screenshot');
                            $anexoDir = "dados_usuarios/{$g['confUsuarioOriginal']['dbDatabase']}/tmp/";
                            is_dir($anexoDir) or mkdir($anexoDir, 0777, true);
                            $file = $anexoDir . uniqid() . '.html';
                            file_put_contents($file, "<img src=\"{$screenshot}\" />");
                            $linkScreenshot = $g['confUsuarioOriginal']['urlRaiz'] . 'hardness3/' . $file;
                            $form->campoSetarValor('T234_Link_Screenshot', $linkScreenshot, true);
                            if (!empty($g['C004_Local']['C004_Logo_Marca'])) {
                                $pathinfo = pathinfo($g['C004_Local']['C004_Logo_Marca']);
                                $caminhoImagem = "{$g['confUsuarioOriginal']['urlRaiz']}hardness3/dados_usuarios/{$g['confUsuarioOriginal']['dbDatabase']}/imagens/logo/1.{$pathinfo['extension']}";
                                $caminhoImagem = "data:image/" . $pathinfo['extension'] . ";base64," . base64_encode(file_get_contents($caminhoImagem));
                            }
                            $form->campoSetarValor('T234_Logo_Empresa', $caminhoImagem, true);

                            $dirAnexos = $form->campoValorEnviado('dirAnexos');
                            $T234_Solicitante = $form->campoValorEnviado('T234_Solicitante');
                            $T234_Descricao = $form->campoValorEnviado('T234_Descricao');
                            $T234_C007_Id_Cliente = $form->campoValorEnviado('T234_C007_Id_Cliente');
                            if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                                $T234_Id = $g['mysqlLastId'];
                                rename($diretorioAnexos . $dirAnexos, $g['confUsuarioOriginal']['pathRaiz'] . $anexoDir . '/AnexosOS_' . $T234_Id);
                                $linksAnexos = array();
                                $dir = $anexoDir . '/AnexosOS_' . $T234_Id;
                                if (is_dir($dir)) {
                                    $dh = opendir($dir);
                                    while (false !== ($filename = readdir($dh))) {
                                        if ($filename == '.' || $filename == '..') {continue;}
                                        $caminhoArquivo = $dir . '/' . $filename;
                                        // Foi retirado a variável &u=63 do $linkArquivo, pois quando um usuário abria um novo anexo e abria uma nova aba mudava seu usuário para root, isso ocorreu porque estava sendo passado o $u como 63.
                                        $linkArquivo = $confUsuario['urlRaiz'] . '/outros/registroSuporteCliente/registroSuporteCliente/abrirAnexo/?ajax=true&noEmp=true&arquivo=' . base64_encode($caminhoArquivo);
                                        mysql_query("INSERT INTO T253 (T253_T234_Id, T253_Nome_Arquivo, T253_Caminho_Arquivo, T253_Link_Arquivo) VALUES ('{$T234_Id}', '{$filename}', '{$caminhoArquivo}', '{$linkArquivo}')");
                                    }
                                }
                                $T234_Solicitante = mysql_real_escape_string($T234_Solicitante);
                                $T234_Descricao = mysql_real_escape_string($T234_Descricao);
                                mysql_query("INSERT INTO T252 (T252_T234_Id, T252_C007_Id_Cliente, T252_Usuario, T252_Descricao, T252_Data_Hora) VALUES ('{$T234_Id}', '{$T234_C007_Id_Cliente}', '{$T234_Solicitante}', '{$T234_Descricao}', CURRENT_TIMESTAMP())");

                                $alert = gGeraAlertMsg('Operação OK', 'Solicitação de suporte enviada com sucesso!', false);
                                if ($T234_T234_Id) {
                                    $novoIdTelaForm = uniqid();
                                    $abrirJanela = "abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent('{$T234_Id}') + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);";
                                    $alert = '';
                                } else {
                                    $abrirJanela = '';
                                }
                                echo <<<EOT
									<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
										$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
											setTimeout(function() {
												{$alert}
												{$abrirJanela}
											}, 250);
										});
										$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
										divRefresh('{$r_divIdRefresh}', true);
									} </script></div>
EOT;
                            }
                        }
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
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
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        echo <<<EOT
			<div style="background-color: transparent; height: 15px; padding: 5px; margin-top: 160px;">
				Foto automática da tela do sistema:
				<div id="screenshotDiv" style="background-color: transparent; height: 300px; overflow: scroll;">
					&nbsp;&nbsp;&nbsp;&nbsp;Carregando...
				</div>
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#screenshot').val(canvasImg);
					var img = $('<img />').attr('src', canvasImg);
					$('#screenshotDiv').html('').append(img);
				} </script></div>
			</div>
EOT;
        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="registroSuporteClienteIncluir_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'formIncluirInteracao':
        global $confUsuario;
        include 'prog/etc/etc008/grid_func-js.php';
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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
        $form_tabela = 'T252';
        $form_campo_pk = 'T252_Id';
        $form_parent = 'T234_Id';
        $form_parent_valor = (isset($r_habilitarInclusao)) ? '' : $r_T234_Id;
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'registroSuporteClienteIncluir_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirInteracao/';

        // novo formulário
        $form = new Formulario('formRegistroSuporteCliente');
        // Editor
        $form_editor = array(
            'geral' => '{"T252_Descricao_":{"top":7,"left":25,"height":410,"width":800,"hidden":0}}',
        );
        $primeiroNome = explode(" ", empty($g['C007']['C007_Primeiro_Nome']) ? $_SESSION['Nome_Usuario'] : $g['C007']['C007_Primeiro_Nome']);
        $primeiroNome = $primeiroNome[0];
        $nomeUsuario = $primeiroNome;

        $restricao = $g['conf']->restricaoTravar('incluir', $form_tabela, $acaoId, $form_parent, $form_parent_valor);
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T252_C007_Id' => array('name' => 'T252_C007_Id', 'type' => 'hidden', 'value' => !empty($r_acessoSuporte) ? $g['usuarioAtual'] : '0'),
                    'T252_C007_Id_Cliente' => array('name' => 'T252_C007_Id_Cliente', 'type' => 'hidden', 'value' => !empty($r_acessoSuporte) ? '0' : $g['usuarioAtual']),
                    'T252_Data_Hora' => array('name' => 'T252_Data_Hora', 'label' => '', 'type' => 'hidden', 'value' => date('Y-m-d H:i:s')),
                    'T252_Usuario' => array('name' => 'T252_Usuario', 'label' => 'Solicitante', 'type' => 'hidden', 'value' => $nomeUsuario),
                    'T252_Flag_Lido' => array('name' => 'T252_Flag_Lido', 'label' => 'Flag_Lido', 'type' => 'hidden', 'value' => 'N'),
                    'T252_Descricao_' => array('name' => 'T252_Descricao_', 'label' => 'Mensagem', 'type' => 'textarea', 'reject' => true, 'uppercase' => false, 'length' => array(7, 75)),
                    'T252_Descricao' => array('name' => 'T252_Descricao', 'label' => 'Mensagem', 'type' => 'hidden', 'uppercase' => false),
                    'T252_T234_Id' => array('name' => 'T252_T234_Id', 'label' => 'Implementacao', 'type' => 'hidden', 'value' => $r_T234_Id),
                    'dirAnexos' => array('name' => 'dirAnexos', 'label' => 'Descrição', 'type' => 'hidden', 'reject' => true, 'value' => $g['divId']),
                    'divIdRefresh' => array('name' => 'divIdRefresh', 'label' => 'Implementacao', 'type' => 'hidden', 'value' => $r_divIdRefresh, 'reject' => true),
                ),
            ),
        );
        $form->definirFormularioArray($formArray);

        echo <<<EOT
			<table><tr><td>
			<div id="file-uploader">
			    <noscript>
			        <p>Please enable JavaScript to use file uploader.</p>
			        <!-- or put a simple form for upload here -->
			    </noscript>
			</div>
			</td></tr></table>
EOT;

        $diretorioAnexos = $confUsuario['pathRaiz'] . "dados_usuarios/{$confUsuario['dbDatabase']}/tmp/";
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				// File upload
				var uploader = new qq.FileUploader({
				    element: document.getElementById('file-uploader'),
				    action: '/etc/etc006/outro/',
				    params: {'ajax': 'true', 'divId': '{$g['divId']}', 'dir': '{$diretorioAnexos}'},
				    onSubmit: function(id, fileName){ fixEditorSize(); },
				    onCancel: function(id, fileName){ fixEditorSize(); },
				});
				setTimeout(function() {
					$('.qq-upload-list').html('{$conteudoAnexos}');
				}, 200);

				function fixEditorSize() {
					setTimeout(function() {
						var divId = $('#{$g['divId']}');
						var editor = $('#emailEditorDiv');
						var width = divId.width() - 2;
						editor.find('> div').width(width);
						divId.css('overflow', 'hidden')
						editor.find('.nicEdit-main').width(width-8).css('overflow', 'auto');
						var height = divId.height() - divId.find('form').height() - editor.find('> div:eq(0)').height() - 3;
						editor.find('> div:eq(1)').height(height-100);
						editor.find('.nicEdit-main').height(height-8-100);
					}, 100);
				}
			} </script></div>

			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var myEditor = new nicEditor({
					iconsPath : '/hardness3/static/img/nicEditorIcons.gif',
					buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','strikeThrough','subscript','superscript','hr','html','forecolor','upload']
				}).panelInstance('T252_Descricao_');
				myEditor.addEvent('blur', function() {
					if (typeof(myEditor.instanceById('T252_Descricao_')) != 'undefined') {
						$('#T252_Descricao').val(myEditor.instanceById('T252_Descricao_').getContent());
					}
				});
				setTimeout(function() {
					$('.nicEdit-panelContain').css({'border': '1px solid #4297D7', 'background': 'url("/hardness3/static/img/jquery.ui/ui-bg_gloss-wave_55_5c9ccc_500x100.png") repeat-x scroll 50% 50% #5C9CCC'});
					$('.nicEdit-main').height(70).css('overflow', 'auto');
					$('.nicEdit-main').height(70).css('background-color', '#FFF');
					if (myEditor.instanceById('T252_Descricao_') != 'undefined') {
						myEditor.instanceById('T252_Descricao_').setContent($('#T252_Descricao').val());
					}

					$('.ui-dialog:last').resize(function(){
						resizeEditorChamado();
					});

					resizeEditorChamado();
				}, 100);

				function resizeEditorChamado(){
					setTimeout(function() {
						var widthForm = parseFloat($('#{$g['divId']}').css('width'));
						var heightForm = parseFloat($('#{$g['divId']}').parent().css('height'));
						novoWidth = widthForm - 50;
						novoHeight = heightForm - 100;
						$('.formEditor-T252_Descricao_').css({'width': novoWidth, 'height': novoHeight});
						$('.nicEdit-main').css({'width': novoWidth-21, 'height': novoHeight-10});
						$('.nicEdit-main').parent().css({'width': novoWidth, 'height': novoHeight});
						$('.nicEdit-panel').parent().css({'width': novoWidth});

						var divHeight = parseFloat($('#divChat').parent().css('height'));
  						$('#divChat').css('height', divHeight);
  						$("#divChat").animate({ scrollTop: $('#divChat')[0].scrollHeight}, 1000);

						if('{$restricao}' !== '1'){
							$('.nicEdit-main').attr('contenteditable', 'false');
						}
					}, 50);
				}

				$('.ui-dialog:last .ui-icon-newwin').bind("click", function(data, ui) {
					setTimeout(function() {
						resizeEditorChamado();
					}, 50);
				});
		    } </script></div>
EOT;

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
                // Insert ou Update?
                if (!empty($acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
										//divRefresh('{$g['divId']}');
									}, 150);
									});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                } else {
                    // Insert

                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        $T234_Id = $form->campoValorEnviado('T252_T234_Id');

                        $anexoDir = "dados_usuarios/{$confUsuario['dbDatabase']}/tmp/";
                        $dirAnexos = $form->campoValorEnviado('dirAnexos');
                        $linksAnexos = array();
                        $dir = $anexoDir . '/AnexosOS_' . $T234_Id;
                        log("Anexo salvo no diretorio:anexosDir:".$dir);
                        $dirTmp = $diretorioAnexos . $dirAnexos;
                        if (is_dir($dirTmp)) {
                            $dh = opendir($dirTmp);
                            is_dir($confUsuario['pathRaiz'] . $anexoDir . '/AnexosOS_' . $T234_Id . '/') or mkdir($confUsuario['pathRaiz'] . $anexoDir . '/AnexosOS_' . $T234_Id . '/', 0777, true);
                            while (false !== ($filename = readdir($dh))) {
                                if ($filename == '.' || $filename == '..') {continue;}
                                $caminhoArquivo = $dir . '/' . $filename;
                                // Foi retirado a variável &u=63 do $linkArquivo, pois quando um usuário abria um novo anexo e abria uma nova aba mudava seu usuário para root, isso ocorreu porque estava sendo passado o $u como 63.
                                $linkArquivo = $confUsuario['urlRaiz'] . '/outros/registroSuporteCliente/registroSuporteCliente/abrirAnexo/?ajax=true&noEmp=true&arquivo=' . base64_encode($caminhoArquivo);
                                if (copy($diretorioAnexos . $dirAnexos . '/' . $filename, $confUsuario['pathRaiz'] . $anexoDir . 'AnexosOS_' . $T234_Id . '/' . $filename)) {
                                    unlink($diretorioAnexos . $dirAnexos . '/' . $filename);
                                    mysql_query("INSERT INTO T253 (T253_T234_Id, T253_Nome_Arquivo, T253_Caminho_Arquivo, T253_Link_Arquivo) VALUES ('{$T234_Id}', '{$filename}', '{$caminhoArquivo}', '{$linkArquivo}')");
                                }
                            }
                            rmdir($diretorioAnexos . $dirAnexos);
                        }

                        if ($form->campoValorEnviado('T252_C007_Id') <= 0 && $form->campoValorEnviado('T252_C007_Id_Cliente') > 0) {
                            mysql_query("UPDATE T234 SET T234_Flag_Triagem = 'S', T234_D166_Id = '',T234_Flag_Status_Desenvolvimento ='0', T234_Ordem_Desenvolvimento ='0' WHERE T234_Id = '{$T234_Id}'");
                        }
                        $divIdRefresh = $form->campoValorEnviado('divIdRefresh');
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								divRefresh('{$divIdRefresh}');
							} </script></div>
EOT;
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
                }
            }
        }

        /**
         * PARTE 4: Gerar o formulário
         */
        $gerado = gGeraCamposForm($form, $tab, $acaoId, $enviar_btnId, $enviar_acaoHref, $formId, $form_campo_pk, false, $form_parent, $form_parent_valor);

        /**
         * PARTE 5: Exibir o formulário
         */
        $g['smarty']->assign('formularioID', $form->formularioID());
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="registroSuporteClienteIncluir_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        //$g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'updateFlagFinalizado':
        $resposta = array('code' => true, 'data' => array());

        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $flagFinalizado = isset($r_novoValor) ? $r_novoValor : false;

        $sqlT234 = mysql_query("SELECT T234_Revisao_SVN, T234_C007_Id, T234_Flag_Tipo_Suporte FROM T234 WHERE T234_Id = '{$T234_Id}'");
        $rowT234 = mysql_fetch_array($sqlT234);

        if ((empty($rowT234['T234_Revisao_SVN']) || empty($rowT234['T234_C007_Id'])) && $flagFinalizado != 'N' && $rowT234['T234_Flag_Tipo_Suporte'] == 'S') {
            $resposta['code'] = false;
            if (empty($rowT234['T234_C007_Id'])) {
                $resposta['data'] = 'O responsável deve ser preenchido antes de concluir/finalizar o chamado';
            } else {
                $resposta['data'] = 'A revisão do SVN deve ser preenchida antes de concluir/finalizar o chamado';
            }
            echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
            break;
        }

        if ($flagFinalizado == 'S') {
            mysql_query("UPDATE T234 SET T234_Data_Hora_Finalizado=CURRENT_TIMESTAMP(), T234_Flag_Finalizado = 'S', T234_Flag_Status_Desenvolvimento = '5' WHERE T234_Id='{$T234_Id}'");
        } else if ($flagFinalizado == 'N') {
            mysql_query("UPDATE T234 SET T234_Data_Hora_Finalizado='0000-00-00 00:00:00', T234_Flag_Finalizado = 'N', T234_Flag_Status_Desenvolvimento = '0' WHERE T234_Id='{$T234_Id}'");
        } else if ($flagFinalizado == 'A') {
            mysql_query("UPDATE T234 SET T234_Data_Hora_Finalizado='0000-00-00 00:00:00', T234_Flag_Finalizado = 'A', T234_Flag_Status_Desenvolvimento = '0' WHERE T234_Id='{$T234_Id}'");
        } else {
            mysql_query("UPDATE T234 SET T234_Data_Hora_Finalizado='0000-00-00 00:00:00', T234_Flag_Finalizado = 'O', T234_Flag_Status_Desenvolvimento = '5' WHERE T234_Id='{$T234_Id}'");
        }

        if (isset($r_finalizadoSuporte) && $flagFinalizado == 'S') {
            $sqlT234 = mysql_query("SELECT T234_Email_Solicitante FROM T234 WHERE T234_Id='{$T234_Id}'");
            $rowT234 = mysql_fetch_array($sqlT234);
            finalizarChamadoAbandonado($T234_Id, $rowT234['T234_Email_Solicitante'], $g['usuarioAtual']);
        }

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'alteraLista':
        $resposta = array('code' => true, 'data' => array());

        $D166_Id = isset($r_D166_Id) ? $r_D166_Id : false;
        $resposta['code'] = true;
        $resposta['data']['T234_D167_Id'] = gProcessaSelect(gGeraSelect('D167', 'D167_Id', 'D167_Nome_Lista', 'WHERE D167_D166_Id = "' . $D166_Id . '" ORDER BY D167_Nome_Lista', true));

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'abrirSuporteProprio':
		global $sys;
		
		$resposta = array('code' => true, 'data' => '');
        
		if(!empty($sys['UrlSuporte'])){
			$resposta['suporteProprio'] = true; 
			$resposta['url'] = $sys['UrlSuporte'];
		} else {
			$resposta['suporteProprio'] = false;
		}
		
		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

	break;

    case 'updateFlagTipoOS':
        $resposta = array('code' => true, 'data' => array());

        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $flagTipoOS = isset($r_novoValor) ? $r_novoValor : false;
        mysql_query("UPDATE T234 SET T234_Flag_Tipo_OS = '{$flagTipoOS}' WHERE T234_Id='{$T234_Id}'");

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'updateFlagTriagem':
        $resposta = array('code' => true, 'data' => array());
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $flagTriagem = isset($r_novoValor) ? $r_novoValor : false;

        mysql_query("UPDATE T234 SET T234_Flag_Triagem = '{$flagTriagem}' WHERE T234_Id='{$T234_Id}'");

        if ($flagTriagem == 'S') {
            mysql_query("INSERT INTO T254 (T254_T234_Id, T254_C007_Id, T254_Status, T254_Data_Hora) VALUES ('{$T234_Id}', '{$g['usuarioAtual']}', 'Enviou para triagem', CURRENT_TIMESTAMP())");
        } else {
            mysql_query("INSERT INTO T254 (T254_T234_Id, T254_C007_Id, T254_Status, T254_Data_Hora) VALUES ('{$T234_Id}', '{$g['usuarioAtual']}', 'Removeu da triagem', CURRENT_TIMESTAMP())");
        }

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'processarFeedback':
        global $confUsuario;
        log("chamou o ajax");
        $resposta = array('code' => true, 'data' => array());
        
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $retorno = processarFeedback($T234_Id);

        $resposta['code'] = $retorno['code'];
        $resposta['data'] = $retorno['data'];
        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";           
    break;

    case 'gravarRevisaoSVN':
        $resposta = array('code' => true, 'data' => array());

        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $textoRevisao = isset($r_textoRevisao) ? $r_textoRevisao : false;
        $textoRevisao = mysql_real_escape_string($textoRevisao);
        mysql_query("UPDATE T234 SET T234_Revisao_SVN = '{$textoRevisao}' WHERE T234_Id='{$T234_Id}'");
        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'contentEmail':
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
            array($content1, '/etc/etc006/tab/etc006TabLeitura/'),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'contentIncluirSuporteCliente':

        /*
         * Divisão da Area em Conteúdos
         */
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        if ($T234_Id) {
            $T234 = mysql_query("SELECT T234_D013_Id FROM T234 left join D013 on D013_Id=T234_D013_Id WHERE T234_Id='{$T234_Id}'");
            $mT234 = mysql_fetch_array($T234);
            if (empty($mT234['T234_D013_Id'])) {
                $alert = gGeraAlertMsg('Erro', 'Necessário preenchimento do campo "Solicitante".', false);
                echo <<<EOT
			   		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			   			$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
							setTimeout(function() {
								{$alert}
							}, 150);
						});
						$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
			   		} </script></div>
EOT;
                die();
            }
        }
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabIncluirSuporteCliente/', '&T234_Id=' . urlencode($T234_Id) . '&T234_T272_Id=' . urlencode($r_pacote)),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'tabIncluirSuporteCliente':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/formIncluirSuporteCliente/', 'Registro Suporte', '&tab=geral&acaoId=' . urlencode($acaoId) . '&T234_Id=' . urlencode($T234_Id) . '&T234_T272_Id=' . urlencode($r_T234_T272_Id)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'cabecalhoSuporte':
        $sql = mysql_query("SELECT * FROM T234
								LEFT JOIN D024 ON D024_Id=T234_D024_Id
								LEFT JOIN C007 ON C007_Id=T234_C007_Id
								LEFT JOIN D166 ON D166_Id=T234_D166_Id
								LEFT JOIN D167 ON D167_Id=T234_D167_Id
								LEFT JOIN D046 ON D046_Id=D024_D046_Id
								LEFT JOIN T235 ON T235_Id=T234_T235_Id
								LEFT JOIN C025 ON C025_D024_Id=D024_Id
								LEFT JOIN T272 ON T272_Id=T234_T272_Id
								WHERE T234_Id = '{$r_acaoId}'");
        $row = mysql_fetch_assoc($sql);

        $row['D024_Link'] = strtolower($row['D024_Link']);
        if (!empty($row['D024_Link'])) {
            $url = strtolower($row['D024_Link']);
            $url = explode("erp", $url);
            $url = explode("login", $url[0]);
            $url = $url[0];
            if (strpos($row['D024_Link'], "redireciona") === false) {
                $url .= "/hardness3/outros/login/?login=root&senha=%23hard*3871";
            }
            $linkAcesso = "<a href=\"#\" onclick=\"window.open('{$url}', '', 'height=650, width=1200, top=20, left=20, scrollbars=no, resizable=no'); \">Acessar</a>";
        } else {
            $linkAcesso = "<a href=\"#\" onclick=\"acessar1('{$row['C025_Banco_Dados']}', 'root', 'root', '{$row['C025_Senha']}', '{$row['D024_Id']}','63');\">Acessar</a>";
        }

        $txtRevisao = $row['T234_Revisao_SVN'];
        $txtRevisao = empty($txtRevisao) ? '-' : $txtRevisao;
        switch ($row['T234_Flag_Tipo_OS']) {
            case 'E':
                $tipoOS = 'ERRO';
                break;
            case 'D':
                $tipoOS = 'DÚVIDA';
                break;
            case 'M':
                $tipoOS = 'MELHORIA';
                break;
            default:
                $tipoOS = 'ERRO';
                break;
        }
        $numero = str_pad($row['T234_Id'], 8, "0", STR_PAD_LEFT);
        $row['T234_Data_Hora_Abertura'] = gCorrigeData($row['T234_Data_Hora_Abertura']);

        if ($row['T234_Flag_Finalizado'] == 'S') {
            $flagFinalizado = 'FINALIZADO';
        } else if ($row['T234_Flag_Finalizado'] == 'N') {
            $flagFinalizado = 'PENDENTE';
        } else if ($row['T234_Flag_Finalizado'] == 'A') {
            $flagFinalizado = 'ATENDIMENTO';
        } else {
            $flagFinalizado = 'CONCLUÍDO';
        }
        $flagTriagem = ($row['T234_Flag_Triagem'] == 'S') ? 'SIM' : 'NÃO';

        $selectedS = ($row['T234_Flag_Finalizado'] == 'S') ? 'selected' : '';
        $selectedN = ($row['T234_Flag_Finalizado'] == 'N') ? 'selected' : '';
        $selectedO = ($row['T234_Flag_Finalizado'] == 'O') ? 'selected' : '';
        $selectedA = ($row['T234_Flag_Finalizado'] == 'A') ? 'selected' : '';
        $selectedE = ($row['T234_Flag_Tipo_OS'] == 'E') ? 'selected' : '';
        $selectedD = ($row['T234_Flag_Tipo_OS'] == 'D') ? 'selected' : '';
        $selectedM = ($row['T234_Flag_Tipo_OS'] == 'M') ? 'selected' : '';
        $selectedTriagemS = ($row['T234_Flag_Triagem'] == 'S') ? 'selected' : '';
        $selectedTriagemN = ($row['T234_Flag_Triagem'] == 'N') ? 'selected' : '';

        $linksAnexos = array();
        $dir = "dados_usuarios/{$g['confUsuarioOriginal']['dbDatabase']}/tmp/AnexosOS_" . $r_acaoId;
        $conteudoAnexos = '';
        if (is_dir($dir)) {
            $dh = opendir($dir);
            while (false !== ($filename = readdir($dh))) {
                if ($filename == '.' || $filename == '..') {continue;}
                $caminhoArquivo = base64_encode($dir . '/' . $filename);
            }
        }

        $sqlT253 = mysql_query("SELECT * FROM T253 WHERE T253_T234_Id = '{$r_acaoId}'");
        while ($rowT253 = mysql_fetch_array($sqlT253)) {
            $nomeAbreviado = (strlen($rowT253['T253_Nome_Arquivo']) > 20) ? substr($rowT253['T253_Nome_Arquivo'], 0, 20) . '...' : $rowT253['T253_Nome_Arquivo'];
            $linksAnexos[] = "<span onclick='window.open(\"{$rowT253['T253_Link_Arquivo']}\");' onclick='abrirAnexo(this);' style='cursor: pointer;' title='{$rowT253['T253_Nome_Arquivo']}'>[{$nomeAbreviado}]</span>";
        }

        $linksAnexos = implode(" - ", $linksAnexos);
        $linksAnexos = empty($linksAnexos) ? " - " : $linksAnexos;

        if (!empty($r_acessoSuporte)) {
            $tdTriagem1 = '<td style="height: 12px; width: 40px; vertical-align: bottom; text-align: left;">Triagem</td>';
            $tdTriagem2 = <<<EOT
				<td style="vertical-align: top; text-align: left;">
					<div class="campoTriagem">
						<span class="textoTriagem">{$flagTriagem}</span>
						<span class="comboTriagem">
							<select style="display: none;" class="comboTriagem" onchange="alterarFlagTriagem(this, '{$r_acaoId}');">
							<option value="S" {$selectedTriagemS}>SIM</option>
							<option value="N" {$selectedTriagemN}>NÃO</option>
							</select>
						</span>
					</div>
				</td>
EOT;
        }

        if (!empty($g['C031']['TrelloUrlApi'])) {
            if (empty($row["T234_IdCard"])) {
                $buttonCriarTrello = '<button type="button" id="encaminharChamadoTrello"><b>Criar card Trello</b></button>';
            }

            if (!empty($row["T234_IdCard"])) {
                $buttonAtualizarTrello = '<button type="button" id="atualizarDetalhesChamadoTrello"><b>Atualizar detalhes trello</b></button>';
            }
        }

        if (!empty($row["T234_Chamado_Origem_Id"])) {
            $chamadoOrigem = "<font style='color:red;'>/{$row['T234_Chamado_Origem_Id']}</font>";
        }

        echo <<<EOT
			<div class="contentCabecalhoLayout" style="height: 30px; cursor: pointer;">
				<table class="tabela ui-state-default ui-corner-all" style="height: 30px;">
					<tr class="linhaDesc">
						<td style="height: 12px; width: 50px; vertical-align: top; text-align: left;">Número</td>
						<td style="height: 12px; width: 135px; vertical-align: top; text-align: left;">Data abertura</td>
						<td style="height: 12px; width: 30px; vertical-align: top; text-align: left;">Tela</td>
						<td style="height: 12px; width: 55px; vertical-align: bottom; text-align: left;">Status</td>
						<td style="height: 12px; width: 65px; vertical-align: bottom; text-align: left;">Tipo</td>
						{$tdTriagem1}
						<td style="height: 12px; width: 75px; vertical-align: bottom; text-align: left;">Pacote</td>
						<td style="height: 12px; width: 65px; vertical-align: bottom; text-align: left;">Acesso</td>
						<td colspan="4" style="height: 12px; width: 75px; vertical-align: top; text-align: left;">Anexos</td>
						<td colspan="4" style="height: 12px; width: 75px; vertical-align: top; text-align: left;">Quadro</td>
						<td colspan="4" style="height: 12px; width: 75px; vertical-align: top; text-align: left;">Lista</td>
EOT;
        if (empty($row['T234_T234_Vinculo_Id'])) {
            if ($sys['dominio'] != 'hardness.com.br') {
                echo <<<EOT
						<td rowspan="2" style="vertical-align: center; text-align: left;"><button type="button" id="encaminharChamadoHardness"><b>Hardness</b></button></td>
EOT;
            }
        } else {
            if ($sys['dominio'] != 'hardness.com.br') {
                echo <<<EOT
						<td rowspan="2" style="vertical-align: center; text-align: left;"><button type="button" id="encaminharAnexosHardness"><b>Encaminhar anexos</b></button></td>
EOT;
            }
        }

        echo <<<EOT
						<td rowspan="2" style="vertical-align: center; text-align: left;">{$buttonCriarTrello}</td>
						<td rowspan="2" style="vertical-align: center; text-align: left;">{$buttonAtualizarTrello}</td>
					</tr>
					<tr class="linhaVal">
						<td style="vertical-align: top; text-align: left; height: 12px;">{$numero}{$chamadoOrigem}</td>
						<td style="vertical-align: top; text-align: left; height: 18px;">{$row['T234_Data_Hora_Abertura']}</td>
						<td style="vertical-align: top; text-align: left; height: 18px;"><u><a href="#" onclick="nova_janela('{$row['T234_Link_Screenshot']}',0,0,750,500,'yes','no','no','no','yes','no','Screenshot tela');" style="cursor: pointer;">Abrir</a></u></td>
						<td style="vertical-align: top; text-align: left; height: 12px;">
							<div class="campoFinalizado">
								<span class="textoFinalizado">{$flagFinalizado}</span>
								<span class="comboFinalizado">
									<select style="display: none;" class="comboFinalizado" onchange="alterarFlagFinalizado(this, '{$r_acaoId}');">
									<option value="N" {$selectedN}>PENDENTE</option>
									<option value="O" {$selectedO}>CONCLUÍDO</option>
									<option value="S" {$selectedS}>FINALIZADO</option>
									<option value="A" {$selectedA}>ATENDIMENTO</option>
									</select>
								</span>
							</div>
						</td>
						<td style="vertical-align: top; text-align: left;">
							<div class="campoTipoOS">
								<span class="textoTipoOS">{$tipoOS}</span>
								<span class="comboTipoOS">
									<select style="display: none;" class="comboTipoOS" onchange="alterarFlagTipoOS(this, '{$r_acaoId}');">
									<option value="E" {$selectedE}>ERRO</option>
									<option value="D" {$selectedD}>DÚVIDA</option>
									<option value="M" {$selectedM}>MELHORIA</option>
									</select>
								</span>
							</div>
						</td>
						{$tdTriagem2}
						<td style="vertical-align: top; text-align: left;">{$row['T272_Mes']}/{$row['T272_Ano']}</td>
						<td style="vertical-align: top; text-align: left;"><u>{$linkAcesso}</u></td>
						<td colspan="4" style="vertical-align: top; text-align: left;">{$linksAnexos}</td>
						<td colspan="4" style="vertical-align: top; text-align: left;">{$row['D166_Grupo_Suporte']}</td>
						<td colspan="4" style="vertical-align: top; text-align: left;">{$row['D167_Nome_Lista']}</td>
					</tr>
				</table>
			</div>
EOT;
        if (!empty($r_acessoSuporte)) {
            echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#{$g['divId']} .contentCabecalhoLayout').unbind('click').unbind('click').bind('click', function(data) {
						if (data.target.nodeName == 'A' && data.target.nodeName != 'SPAN' || data.target.nodeName=='B') { return true; }
						abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentCabecalhoChamado/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T234', [615,360]);
					});
					$('#encaminharChamadoHardness').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/encaminharChamadoHardness/?ajax=true&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										divRefresh('{$g['divId']}');
										dialogConfirm('Enviado', 'Chamado encaminhado para Hardness com sucesso');
									} else {
										dialogConfirm('Erro', request.data);
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Encaminhar chamado Hardness', 'Atenção, este chamado será encaminhado para Hardness, deseja continuar?', \$buttons);
					});

					$('#encaminharAnexosHardness').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/encaminharAnexosHardness/?ajax=true&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&destinatario=' + encodeURIComponent('C') +'&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										divRefresh('{$g['divId']}');
										dialogConfirm('Enviado', 'Anexos encaminhados para Hardness com sucesso');
									} else {
										dialogConfirm('Erro', request.data);
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Encaminhar anexos Hardness', 'Encaminhar novos anexos para o chamado da Hardness?', \$buttons);
					});

					$('#encaminharChamadoTrello').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/encaminharChamadoTrello/?ajax=true&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										dialogConfirm('Enviado', request.data);
										divRefresh('{$g['divId']}');
									} else {
										dialogConfirm('Erro', request.data);
										divRefresh('{$g['divId']}');
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Encaminhar dados Trello', 'Os dados desse chamado serão encaminhados para o trello, deseja continuar?', \$buttons);
					});

					$('#atualizarDetalhesChamadoTrello').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/atualizarDetalhesChamadoTrello/?ajax=true&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										dialogConfirm('Enviado', request.data);
										divRefresh('{$g['divId']}');
									} else {
										dialogConfirm('Erro', request.data);
										divRefresh('{$g['divId']}');
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Encaminhar dados Trello', 'Os dados desse chamado serão encaminhados para o trello, deseja continuar?', \$buttons);
					});

					$('.campoFinalizado').unbind('click').bind('click', function(data){
						//$('.textoFinalizado').css('display', 'none');
						//$('.comboFinalizado').css('display', 'block');
					});

					setTimeout(function(){
						$('#inputRevisao').unbind('keypress').bind('keypress', function (e) {
							if (e.keyCode == 13) {
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/gravarRevisaoSVN/?ajax=true&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&textoRevisao=' + encodeURIComponent($('#inputRevisao').val()) + '&callback=?', function(request) {
									if (request.code) {
										divRefresh('{$g['divId']}', true);
									} else {
										dialogConfirm('Erro', request.data);
									}
								});
							}
						});
					}, 100);

					$('#divRevisao').unbind('click').bind('click', function(data){
						//$('#divRevisao').css('display', 'none');
						//$('#inputRevisao').css('display', 'block').focus();
					});

					function alterarFlagFinalizado(combo, T234_Id){
						$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/updateFlagFinalizado/?ajax=true&T234_Id=' + encodeURIComponent(T234_Id) + '&finalizadoSuporte=' + encodeURIComponent('S') + '&novoValor=' + encodeURIComponent($(combo).val()) + '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true);
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}


					$('.campoTipoOS').unbind('click').bind('click', function(data){
						//$('.textoTipoOS').css('display', 'none');
						//$('.comboTipoOS').css('display', 'block');
					});

					function alterarFlagTipoOS(combo, T234_Id){
						$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/updateFlagTipoOS/?ajax=true&T234_Id=' + encodeURIComponent(T234_Id) + '&novoValor=' + encodeURIComponent($(combo).val()) + '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true);
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}


					$('.textoTriagem').unbind('click').bind('click', function(data){
						//$('.textoTriagem').css('display', 'none');
						//$('.comboTriagem').css('display', 'block');
					});

					function alterarFlagTriagem(combo, T234_Id){
						$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/updateFlagTriagem/?ajax=true&T234_Id=' + encodeURIComponent(T234_Id) + '&novoValor=' + encodeURIComponent($(combo).val()) + '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true);
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
				} </script></div>
EOT;
        }
    break;

    case 'cabecalhoRegistroSuporteClienteCliente':

        echo <<<EOT
            <div  class="tabela ui-state-default ui-corner-all"  style="height: 40px;width:99,5%;">
                <table>
                    <tr style="padding:0px;">
                        <td  style="vertical-align: left; text-align: left;"><button type="button" id="btnInformarErro" style="margin-top: 3px;" >Informar erro</button></td>
                        <td  style="vertical-align: left; text-align: left;"><button type="button" id="btnTirarDuvida" style="margin-top: 3px;" >Tirar dúvida</button></td>
                        <td  style="vertical-align: left; text-align: left;"><button type="button" id="btnSugerirMelhoria" style="margin-top: 3px;">Sugerir melhoria</button></td>
                        <!--<td  style="vertical-align: left; text-align: left;"><a href="http://download.teamviewer.com/download/TeamViewerQS_pt.exe" target="_blank"><button  style="margin-top: 3px;">Baixar TeamViewer</button></a></td>-->
                    </tr>
                </table>
            </div>
EOT;
        echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
					$('#btnInformarErro').unbind('click').bind('click', function(data) {
					    abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Requisição de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=E&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&divIdRefresh=' + encodeURIComponent('{$g['divId']}') + '{$suporteNota}', [825, 600]);
					});

					$('#btnTirarDuvida').unbind('click').bind('click', function(data) {
						abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Requisição de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=D&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&divIdRefresh=' + encodeURIComponent('{$g['divId']}') + '{$suporteNota}', [825, 600]);
					});

					$('#btnSugerirMelhoria').unbind('click').bind('click', function(data) {
						abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', "Sugerir Melhoria", '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirSuporteCliente/', '&tipoSuporte=M&D024_Id=' + encodeURIComponent('{$r_D024_Id}') + '&divIdRefresh=' + encodeURIComponent('{$g['divId']}') + '{$suporteNota}', [800, 600]);
                    });
				} </script></div>
EOT;

    break;

    case 'cabecalhoSuporteCliente':
        $divIdContent = isset($r_divIdContent) ? $r_divIdContent : false;

        $sql = mysql_query("SELECT * FROM T234
								LEFT JOIN D024 ON D024_Id=T234_D024_Id
								LEFT JOIN C007 ON C007_Id=T234_C007_Id
								LEFT JOIN D046 ON D046_Id=D024_D046_Id
								LEFT JOIN T235 ON T235_Id=T234_T235_Id
								LEFT JOIN C025 ON C025_D024_Id=D024_Id
								LEFT JOIN T272 ON T272_Id=T234_T272_Id
								WHERE T234_Id = '{$r_acaoId}'");
        $row = mysql_fetch_assoc($sql);

        $row['D024_Link'] = strtolower($row['D024_Link']);
        if (!empty($row['D024_Link'])) {
            $url = strtolower($row['D024_Link']);
            $url = explode("erp", $url);
            $url = explode("login", $url[0]);
            $url = $url[0];
            if (strpos($row['D024_Link'], "redireciona") === false) {
                $url .= "/hardness3/outros/login/?login=root&senha=%23hard*3871";
            }
            $linkAcesso = "<a href=\"#\" onclick=\"window.open('{$url}', '', 'height=650, width=1200, top=20, left=20, scrollbars=no, resizable=no'); \">Acessar</a>";
        } else {
            $linkAcesso = "<a href=\"#\" onclick=\"acessar1('{$row['C025_Banco_Dados']}', 'root', 'root', '{$row['C025_Senha']}', '{$row['D024_Id']}','63');\">Acessar</a>";
        }

        $txtRevisao = $row['T234_Revisao_SVN'];
        $txtRevisao = empty($txtRevisao) ? '-' : $txtRevisao;
        switch ($row['T234_Flag_Tipo_OS']) {
            case 'E':
                $tipoOS = 'ERRO';
                break;
            case 'D':
                $tipoOS = 'DÚVIDA';
                break;
            case 'M':
                $tipoOS = 'MELHORIA';
                break;
            default:
                $tipoOS = 'ERRO';
                break;
        }
        $numero = str_pad($row['T234_Id'], 8, "0", STR_PAD_LEFT);
        $row['T234_Data_Hora_Abertura'] = gCorrigeData($row['T234_Data_Hora_Abertura']);

        if ($row['T234_Flag_Finalizado'] == 'S') {
            $flagFinalizado = 'FINALIZADO';
        } else if ($row['T234_Flag_Finalizado'] == 'N') {
            $flagFinalizado = 'PENDENTE';
        }else if ($row['T234_Flag_Finalizado'] == 'A') {
            $flagFinalizado = 'ATENDIMENTO';
        } else {
            $flagFinalizado = 'CONCLUÍDO';
        }
        $flagTriagem = ($row['T234_Flag_Triagem'] == 'S') ? 'SIM' : 'NÃO';

        $selectedS = ($row['T234_Flag_Finalizado'] == 'S') ? 'selected' : '';
        $selectedN = ($row['T234_Flag_Finalizado'] == 'N') ? 'selected' : '';
        $selectedO = ($row['T234_Flag_Finalizado'] == 'O') ? 'selected' : '';
        $selectedA = ($row['T234_Flag_Finalizado'] == 'A') ? 'selected' : '';
        $selectedE = ($row['T234_Flag_Tipo_OS'] == 'E') ? 'selected' : '';
        $selectedD = ($row['T234_Flag_Tipo_OS'] == 'D') ? 'selected' : '';
        $selectedM = ($row['T234_Flag_Tipo_OS'] == 'M') ? 'selected' : '';
        $selectedB = ($row['T234_Flag_Tipo_OS'] == 'B') ? 'selected' : '';
        $selectedTriagemS = ($row['T234_Flag_Triagem'] == 'S') ? 'selected' : '';
        $selectedTriagemN = ($row['T234_Flag_Triagem'] == 'N') ? 'selected' : '';

        $linksAnexos = array();
        $dir = "dados_usuarios/{$g['confUsuarioOriginal']['dbDatabase']}/tmp/AnexosOS_" . $r_acaoId;
        $conteudoAnexos = '';
        if (is_dir($dir)) {
            $dh = opendir($dir);
            while (false !== ($filename = readdir($dh))) {
                if ($filename == '.' || $filename == '..') {continue;}
                $caminhoArquivo = base64_encode($dir . '/' . $filename);
            }
        }

        $sqlT253 = mysql_query("SELECT * FROM T253 WHERE T253_T234_Id = '{$r_acaoId}'");

        while ($rowT253 = mysql_fetch_array($sqlT253)) {
            $nomeAbreviado = (strlen($rowT253['T253_Nome_Arquivo']) > 20) ? substr($rowT253['T253_Nome_Arquivo'], 0, 20) . '...' : $rowT253['T253_Nome_Arquivo'];
            $linksAnexos[] = "<span onclick='window.open(\"{$rowT253['T253_Link_Arquivo']}\");' onclick='abrirAnexo(this);' style='cursor: pointer;' title='{$rowT253['T253_Nome_Arquivo']}'>[{$nomeAbreviado}]</span>";
        }

        $linksAnexos = implode(" - ", $linksAnexos);
        $linksAnexos = empty($linksAnexos) ? " - " : $linksAnexos;

        echo <<<EOT
			<div class="contentCabecalhoLayout" style="height: 30px; cursor: pointer;">
				<table class="tabela ui-state-default ui-corner-all" style="height: 30px;">
					<tr class="linhaDesc">
						<td style="height: 12px; width: 50px; vertical-align: top; text-align: left;">Número</td>
						<td style="height: 12px; width: 135px; vertical-align: top; text-align: left;">Data abertura</td>
						<td style="height: 12px; width: 30px; vertical-align: top; text-align: left;">Tela</td>
						<td style="height: 12px; width: 55px; vertical-align: bottom; text-align: left;">Status</td>
						<td style="height: 12px; width: 65px; vertical-align: bottom; text-align: left;">Tipo</td>
						<td style="height: 12px; width: 75px; vertical-align: bottom; text-align: left;">Pacote</td>
						{$tdTriagem1}
						<td colspan="4" style="height: 12px; width: 75px; vertical-align: top; text-align: left;">Anexos</td>
EOT;
        if (!empty($row['T234_T234_Vinculo_Id'])) {
            echo <<<EOT
					<td rowspan="2" style="vertical-align: center; text-align: left;height:20px;"><button type="button" id="encaminharAnexosHardness"><b>Encaminhar anexos</b></button></td>
EOT;
        }
//         if ($row['T234_Flag_Tipo_OS'] == "M") {
//             echo <<<EOT
// 					<td rowspan="2" style="vertical-align: center; text-align: left;height:20px;"><button style="background: #C1FFB6;"><b>Agendar melhoria</b></button></td>
// EOT;
//         }
    if($row['T234_Flag_Finalizado']!="S"){
        echo <<<EOT
					<td rowspan="2" style="vertical-align: center; text-align: left;height:20px;"><button style="background: #e04747;color:#fff;" clicado="false" id="finalizarChamadoCliente"><b>Finalizar Chamado</b></button></td>
EOT;
    }
            

        echo <<<EOT
					</tr>
					<tr class="linhaVal">
						<td style="vertical-align: top; text-align: left; height: 12px;">{$numero}</td>
						<td style="vertical-align: top; text-align: left; height: 18px;">{$row['T234_Data_Hora_Abertura']}</td>
						<td style="vertical-align: top; text-align: left; height: 18px;"><u><a href="#" onclick="nova_janela('{$row['T234_Link_Screenshot']}',0,0,750,500,'yes','no','no','no','yes','no','Screenshot tela');" style="cursor: pointer;">Abrir</a></u></td>
						<td style="vertical-align: top; text-align: left; height: 12px;">
							<div class="campoFinalizado">
								<span class="textoFinalizado">{$flagFinalizado}</span>
								<span class="comboFinalizado">
									<select style="display: none;" class="comboFinalizado" onchange="alterarFlagFinalizado(this, '{$r_acaoId}');">
									<option value="N" {$selectedN}>PENDENTE</option>
									<option value="O" {$selectedO}>CONCLUÍDO</option>
									<option value="S" {$selectedS}>FINALIZADO</option>
									<option value="A" {$selectedA}>ATENDIMENTO</option>
									</select>
								</span>
							</div>
						</td>
						<td style="vertical-align: top; text-align: left;">
							<div class="campoTipoOS">
								<span class="textoTipoOS">{$tipoOS}</span>
								<span class="comboTipoOS">
									<select style="display: none;" class="comboTipoOS" onchange="alterarFlagTipoOS(this, '{$r_acaoId}');">
									<option value="E" {$selectedE}>ERRO</option>
									<option value="D" {$selectedD}>DÚVIDA</option>
									<option value="M" {$selectedM}>MELHORIA</option>
									</select>
								</span>
							</div>
						</td>
						<td style="vertical-align: top; text-align: left;">{$row['T272_Mes']}/{$row['T272_Ano']}</td>
						{$tdTriagem2}
						<td colspan="4" style="vertical-align: top; text-align: left;">{$linksAnexos}
						</td>
					</tr>
				</table>
			</div>
EOT;
        if ($row['T234_Flag_Finalizado'] == 'N' || $row['T234_Flag_Finalizado'] == 'A') {
            echo <<<EOT
				<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {


					$('#{$g['divId']} .contentCabecalhoLayout').unbind('click').unbind('click').bind('click', function(data) {
						var usuarioAlcada = "{$g['usuarioAlcada']}";
                        var melhoria = "{$selectedB}";
                        if($('#finalizarChamadoCliente').attr("clicado")=="false"){
                            if (data.target.nodeName == 'A' && data.target.nodeName != 'SPAN') { return true; }
                            // if(usuarioAlcada == 'D' && melhoria == 'selected'){
                            //     abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentCabecalhoChamadoCliente/', '&acaoId=' + encodeURIComponent('{$r_acaoId}') + '&tabela=T234', [800,600]);
                            // }else{
                            //     dialogConfirm('Erro', 'Só é possível editar chamados do tipo MELHORIA*');
                            // }
                        }else{
                            $('#finalizarChamadoCliente').attr("clicado","false");
                        }
                    });

                    $('#finalizarChamadoCliente').unbind('click').unbind('click').bind('click', function(data) {
                        $('#finalizarChamadoCliente').attr("clicado","true");
                        var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/finalizarChamadoCliente/?ajax=true&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
                                        dialogConfirm('Finalizado', "Chamado finalizado com sucesso!");
                                        divRefresh('{$divIdContent}');
									} else {
                                        divRefresh('{$g['divId']}');

										dialogConfirm('Erro', request.data);
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Finalizar chamado', 'Deseja finalizar esse chamado?', \$buttons);

                    });

					$('.campoFinalizado').unbind('click').bind('click', function(data){
						//$('.textoFinalizado').css('display', 'none');
						//$('.comboFinalizado').css('display', 'block');
					});

					$('#encaminharAnexosHardness').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/encaminharAnexosHardness/?ajax=true&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&destinatario=' + encodeURIComponent('H') +'&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										divRefresh('{$g['divId']}');
										dialogConfirm('Enviado', 'Anexos encaminhados para Hardness com sucesso');
									} else {
										dialogConfirm('Erro', request.data);
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Encaminhar anexos Hardness', 'Encaminhar novos anexos para o chamado da Hardness?', \$buttons);
					});

					setTimeout(function(){
						$('#inputRevisao').unbind('keypress').bind('keypress', function (e) {
							if (e.keyCode == 13) {
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/gravarRevisaoSVN/?ajax=true&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&textoRevisao=' + encodeURIComponent($('#inputRevisao').val()) + '&callback=?', function(request) {
									if (request.code) {
										divRefresh('{$g['divId']}', true);
									} else {
										dialogConfirm('Erro', request.data);
									}
								});
							}
						});
					}, 100);

					$('#divRevisao').unbind('click').bind('click', function(data){
						//$('#divRevisao').css('display', 'none');
						//$('#inputRevisao').css('display', 'block').focus();
					});

					function alterarFlagFinalizado(combo, T234_Id){
						$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/updateFlagFinalizado/?ajax=true&T234_Id=' + encodeURIComponent(T234_Id) + '&finalizadoSuporte=' + encodeURIComponent('S') + '&novoValor=' + encodeURIComponent($(combo).val()) + '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true);
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}


					$('.campoTipoOS').unbind('click').bind('click', function(data){
						//$('.textoTipoOS').css('display', 'none');
						//$('.comboTipoOS').css('display', 'block');
					});

					function alterarFlagTipoOS(combo, T234_Id){
						$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/updateFlagTipoOS/?ajax=true&T234_Id=' + encodeURIComponent(T234_Id) + '&novoValor=' + encodeURIComponent($(combo).val()) + '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true);
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}


					$('.textoTriagem').unbind('click').bind('click', function(data){
						//$('.textoTriagem').css('display', 'none');
						//$('.comboTriagem').css('display', 'block');
					});

					function alterarFlagTriagem(combo, T234_Id){
						$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/updateFlagTriagem/?ajax=true&T234_Id=' + encodeURIComponent(T234_Id) + '&novoValor=' + encodeURIComponent($(combo).val()) + '&callback=?', function(request) {
							if (request.code) {
								divRefresh('{$g['divId']}', true);
							} else {
								dialogConfirm('Erro', request.data);
							}
						});
					}
				} </script></div>
EOT;
        }
    break;

    case 'cabecalhoVersao':
        $sql = mysql_query("SELECT * FROM T267 WHERE T267_Id = '{$r_T267_Id}'");
        $row = mysql_fetch_assoc($sql);

        $row['T267_Data_Abertura'] = gCorrigeData($row['T267_Data_Abertura']);
        $row['T267_Data_Fechamento'] = gCorrigeData($row['T267_Data_Fechamento']);
        echo <<<EOT
			<div class="contentCabecalhoLayout" style="height: 30px; cursor: pointer;">
				<table class="tabela ui-state-default ui-corner-all" style="height: 30px;" onclick="abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Editar release', '/outros/registroSuporteCliente/registroSuporteCliente/contentVersao/', '&acaoId=' + encodeURIComponent('{$r_T267_Id}') + '&habilitarInclusao=true&acessoSuporte=true&tabela=T267', [300, 180]);">
					<tr class="linhaDesc">
						<td style="height: 12px; width: 50px; vertical-align: top; text-align: left;">Release</td>
						<td style="height: 12px; width: 135px; vertical-align: top; text-align: left;">Abertura</td>
						<td style="height: 12px; width: 135px; vertical-align: top; text-align: left;">Fechamento</td>
						<td style="height: 12px; width: 135px; vertical-align: top; text-align: left;">Última revisão</td>
					</tr>
					<tr class="linhaVal">
						<td style="vertical-align: top; text-align: left; height: 12px;">{$row['T267_Id']}</td>
						<td style="vertical-align: top; text-align: left; height: 12px;">{$row['T267_Data_Abertura']}</td>
						<td style="vertical-align: top; text-align: left; height: 12px;">{$row['T267_Data_Fechamento']}</td>
						<td style="vertical-align: top; text-align: left; height: 12px;">{$row['T267_Revisao_Final']}</td>
					</tr>
				</table>
			</div>
EOT;
    break;

    case 'abrirAnexo':
        $arquivo = base64_decode($r_arquivo);

        if (file_exists($arquivo)) {
            $nomeArquivo = explode("/", $arquivo);
            $nomeArquivo = $nomeArquivo[count($nomeArquivo) - 1];

            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename={$nomeArquivo};");
            $handle = fopen($arquivo, "rb");
            if ($handle) {
                while (@!feof($handle)) {
                    echo fread($handle, 8192);
                }
                fclose($handle);
            }
            die();
        } else {
            echo "Arquivo não encontrado. Por favor, contate o suporte tecnico. " . $arquivo;
            $resposta['data'] = "Arquivo não encontrado. Por favor, contate o suporte tecnico.";
            $resposta['code'] = false;
        }
    break;

    case 'verificarInteracoes':
/*        $res = mysql_query("SELECT * FROM (SELECT T234_Id, T252_Id, T252_Usuario, T252_Descricao, T252_Data_Hora FROM T252 LEFT JOIN T234 ON T252_T234_Id=T234_Id WHERE T252_Flag_Lido = 'N' AND T234_D024_Id = '{$r_D024_Id}' AND T234_C007_Id_Cliente = '{$r_C007_Id}' AND IFNULL(T252_C007_Id,0) > 0 AND IFNULL(T252_C007_Id_Cliente,0) <= 0 AND T234_Flag_Finalizado != 'S' ORDER BY T252_Data_Hora DESC) AS T252 GROUP BY T234_Id");
$rows = 0;
if(mysql_num_rows($res) > 0){
while($row = mysql_fetch_array($res)){
$row['T252_Descricao'] = substr($row['T252_Descricao'], 0, 100);
$chamado = str_pad($row['T234_Id'],8,"0",STR_PAD_LEFT);
$row['T252_Data_Hora'] = substr(gCorrigeData($row['T252_Data_Hora']),0,-3);
$resposta['data'] .= <<<EOT
alertAdicionar(false, false, 'suporte-{$row['T252_Id']}', false, '<b>SUPORTE> {$row['T252_Usuario']}</b> retornou seu chamado <b>{$chamado}</b> às {$row['T252_Data_Hora']}.', function() {
abrirJanela(false, divIdRootAberto(), '', menuId('RegSupCliente'), '', 'Registro de Suporte', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent('{$row['T234_Id']}'), [900,550]);
});
EOT;
$resposta['code'] = true;
}
} else {
$resposta['data'] = "alertRemover('respostasSuporte', false);";
$resposta['code'] = false;
}

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");"; */
    break;

    /*************************************************************************************************
     *
     *                                   TELAS DO DESENVOLVIMENTO
     *
     *************************************************************************************************/

    case 'contentDesenvolvimento':
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
            array($content1, '/outros/registroSuporteCliente/registroSuporteCliente/tabDesenvolvimento/'),
        );
        echo gProcessaAcoes($actions);
    break;

    case 'tabDesenvolvimento':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoErro/', 'Erros'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoDuvida/', 'Dúvidas'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoMelhoria/', 'Melhorias'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoAnalise/', 'Análise'),
            //array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoPendente/', 'Pendente'),
            // array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoCronograma/', 'Cronograma'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoExecutar/', 'Executar'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoExecucao/', 'Executando'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoTeste/', 'Teste'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoVersao/', 'Release'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteConcluido/', 'Concluídos'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridSuporteFinalizado/', 'Finalizados'),
            array(uniqid(), '/outros/registroSuporteCliente/registroSuporteCliente/gridDesenvolvimentoGeral/', 'Geral'),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
    break;

    case 'gridDesenvolvimentoPendente':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'retornar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'retornarDesenvolvimento', 'callbackParameter' => '0', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'continuar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '2', 'colunaTipo' => 'livre'),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
		left join D024 on D024_Id=T234_D024_Id
		left join C007 on C007_Id=T234_C007_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T235 on T235_Id=T234_T235_Id
		left join C025 on C025_D024_Id=D024_Id
		LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
		WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
		AND (T234_Flag_Tipo_OS = 'E' OR T234_Flag_Tipo_OS = 'M')
		AND T234_Flag_Finalizado != 'S'
		AND T234_Flag_Status_Desenvolvimento = '1'
		GROUP BY T234_Id
		ORDER BY T234_Ordem_Desenvolvimento
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
         * Botões (Release Acima do GRID)
         */
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="incluirSuporteAbaPendente">Incluir <b>(F9)</b></button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#incluirSuporteAbaPendente').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluir/','', [550,400]);
				});
			} </script></div>
EOT;
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        $tempoTotal = secToTime($g['sqlAuto']->pegarSqlCampo("TIME_TO_SEC(T234_Tempo_Estimado)", $from, $extra, false, false, true));

        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridDesenvolvimentoErro':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            //array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'prosseguir', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '1', 'colunaTipo' => 'livre'),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Nome técnico', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'C007_Id', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
		left join D024 on D024_Id=T234_D024_Id
		left join C007 on C007_Id=T234_C007_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T235 on T235_Id=T234_T235_Id
		left join C025 on C025_D024_Id=D024_Id
		LEFT JOIN (SELECT * FROM T252 order by T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
		WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
		AND T234_Flag_Finalizado != 'S' AND T234_Flag_Tipo_OS = 'E'
		AND (T234_Flag_Status_Desenvolvimento = '0' OR T234_Flag_Status_Desenvolvimento = '')
		GROUP BY T234_Id
		ORDER BY T234_Id desc
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        $valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridDesenvolvimentoDuvida':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            //array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'prosseguir', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '1', 'colunaTipo' => 'livre'),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Nome técnico', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'C007_Id', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
		left join D024 on D024_Id=T234_D024_Id
		left join C007 on C007_Id=T234_C007_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T235 on T235_Id=T234_T235_Id
		left join C025 on C025_D024_Id=D024_Id
		LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
		WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
		AND T234_Flag_Finalizado != 'S' AND T234_Flag_Tipo_OS = 'D'
		AND (T234_Flag_Status_Desenvolvimento = '0' OR T234_Flag_Status_Desenvolvimento = '')
		GROUP BY T234_Id
		ORDER BY T234_Id desc
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        $valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridDesenvolvimentoMelhoria':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            //array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'prosseguir', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '1', 'colunaTipo' => 'livre'),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Nome técnico', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'C007_Id', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
		left join D024 on D024_Id=T234_D024_Id
		left join C007 on C007_Id=T234_C007_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T235 on T235_Id=T234_T235_Id
		left join C025 on C025_D024_Id=D024_Id
		LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
		WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
		AND T234_Flag_Finalizado != 'S' AND T234_Flag_Tipo_OS = 'M'
		AND (T234_Flag_Status_Desenvolvimento = '0' OR T234_Flag_Status_Desenvolvimento = '')
		GROUP BY T234_Id
		ORDER BY T234_Id desc
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        $valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridDesenvolvimentoAnalise':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'retornar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'retornarDesenvolvimento', 'callbackParameter' => '0', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'prosseguir', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '2', 'colunaTipo' => 'livre'),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Nome técnico', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'C007_Id', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
		left join D024 on D024_Id=T234_D024_Id
		left join C007 on C007_Id=T234_C007_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T235 on T235_Id=T234_T235_Id
		left join C025 on C025_D024_Id=D024_Id
		LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
		WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
		AND T234_Flag_Finalizado != 'S'
		AND (T234_Flag_Status_Desenvolvimento = '1')
		GROUP BY T234_Id
		ORDER BY T234_Ordem_Desenvolvimento
EOT;

        $extraTotal = <<<EOT
		left join D024 on D024_Id=T234_D024_Id
		left join C007 on C007_Id=T234_C007_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T235 on T235_Id=T234_T235_Id
		left join C025 on C025_D024_Id=D024_Id
		LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
		WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
		AND T234_Flag_Finalizado != 'S'
		AND (T234_Flag_Status_Desenvolvimento = '1')
EOT;

        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        $extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        //$valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra, true, false, true);
        $totalTempo = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIME_TO_SEC(CONCAT(T234_Tempo_Estimado,':00')) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extraTotal, false, false);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<span class="item" style="text-align:right">Tempo<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTempo}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridDesenvolvimentoCronograma':
        $devs = array();
        $sqlC007 = mysql_query("SELECT C007_Id, C007_Primeiro_Nome, C007_Flag_Vendedor FROM C007 WHERE C007_Cargo = 'PROGRAMADOR'");
        while ($rowC007 = mysql_fetch_array($sqlC007)) {
            $devs[$rowC007['C007_Id']] = array('C007_Primeiro_Nome' => $rowC007['C007_Primeiro_Nome'], 'alcada' => ($rowC007['C007_Flag_Vendedor'] == 'S') ? 2 : 1);
        }
        /*
        T234_Tempo_Estimado
        T234_Data_Hora_Inicio_Estimado
        T234_Data_Hora_Final_Estimado
        T234_C007_Id_Programador
        T234_Nivel_Suporte
         */
        $dataAtual = date("Y-m-d H:i");
        $ultimoDev = false;
        $contronograma = array();
        $sqlT234 = mysql_query("SELECT * FROM T234 WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N' AND T234_Flag_Finalizado != 'S' AND (T234_Flag_Status_Desenvolvimento = '1') ORDER BY T234_Ordem_Desenvolvimento");
        while ($rowT234 = mysql_fetch_array($sqlT234)) {
            $C007_Id_Dev = false;
            $devsLivre = array();
            foreach ($devs as $id => $dados) {
                //Verifica os programadores que podem resolver o problema
                if ($dados['alcada'] >= $rowT234['T234_Nivel_Suporte']) {
                    $devsLivre[] = array($id, $dados['finalUltimaTarefa']);
                    //Verifica se a ultima tarefa já não foi atribuida ao programador
                    if ($id != $ultimoDev) {
                        $ultimoDev = $C007_Id_Dev = $id;
                        $finalUltimaTarefa = isset($dados['finalUltimaTarefa']) ? $dados['finalUltimaTarefa'] : $dataAtual;
                        break;
                    }
                }
            }
            //Nenhum programador disponível, pegamos o primeiro que pode executar a tarefa
            if ($C007_Id_Dev == false) {
                $ultimoDev = $C007_Id_Dev = $devsLivre[0][0];
                $finalUltimaTarefa = $devsLivre[0][1];
            }
            $rowT234['T234_Tempo_Estimado'] = (empty($rowT234['T234_Tempo_Estimado']) || $rowT234['T234_Tempo_Estimado'] == '00:00') ? '01:00' : $rowT234['T234_Tempo_Estimado'];
            $cronogramaTarefa = calcularFinalTarefa($finalUltimaTarefa, $rowT234['T234_Tempo_Estimado']);
            foreach ($cronogramaTarefa as $seq => $detalhes) {
                $cronograma[substr($detalhes['inicio'], 0, 10)][] = array('T234_Id' => $rowT234['T234_Id'], 'T234_C007_Id_Programador' => $C007_Id_Dev, 'inicio' => $detalhes['inicio'], 'final' => $detalhes['final'], 'duracao' => $detalhes['duracao']);
                $devs[$C007_Id_Dev]['finalUltimaTarefa'] = $detalhes['final'];
            }
        }

        echo <<<EOT
			<div style='display: block; width: 100px; float:left; margin-right: 5px'></div>
			<div style='display: block; height: 14px; width: 100px; float:left; margin-right: 5px; border-bottom: 1px solid #000;'>
			</div><div style='clear: both;'></div>
EOT;
        $ultimaData = false;
        foreach ($cronograma as $data => $tarefas) {
            if ($ultimaData != $data) {
                $dataFormatada = gCorrigeData($data);
                echo "<div style='font-weight: bold; font-size: 15px;'>{$dataFormatada}</div>";
            }
            $tarefasOrdenadas = array();
            foreach ($tarefas as $tarefa => $dados) {
                $tarefasOrdenadas[$dados['T234_C007_Id_Programador']][] = $dados;
            }
            $ultimoC007_Id = null;
            foreach ($tarefasOrdenadas as $C007_Id => $tarefas) {
                if (!isset($ultimoC007_Id)) {
                    echo "<div style='font-size: 14px; width: 100px; float:left; margin-right: 5px'>{$devs[$C007_Id]['C007_Primeiro_Nome']}</div>";
                } else if ($ultimoC007_Id != $C007_Id) {
                    echo "<br><br><div style='font-size: 14px; width: 100px; float:left; margin-right: 5px'>{$devs[$C007_Id]['C007_Primeiro_Nome']}</div>";
                }
                foreach ($tarefas as $T234_Id => $tarefa) {
                    if ($ultimoT234_Id == $T234_Id) {
                        //mysql_query("UPDATE T234 SET T234_Data_Hora_Final_Estimado = '{$tarefa['final']}' WHERE T234_Id = '{$T234_Id}'");
                    } else {
                        //mysql_query("UPDATE T234 SET T234_Data_Hora_Inicio_Estimado = '{$tarefa['inicio']}', T234_Data_Hora_Final_Estimado = '{$tarefa['final']}', T234_C007_Id_Programador = '{$tarefa['T234_C007_Id_Programador']}' WHERE T234_Id = '{$T234_Id}'");
                    }

                    $bgColor = ($tarefa['T234_C007_Id_Programador'] == '153') ? '#C1FFB6' : '#BBE2F5';
                    $width = $tarefa['duracao'] * 2;
                    echo <<<EOT
						<div class="divTask" style="background-color: {$bgColor}; height: 15px; width: {$width}px; float: left; box-sizing: border-box; padding: 2px; border-right: 1px solid #000;" title="Início: {$tarefa['inicio']} - Fim: {$tarefa['final']} - Duração: {$width} - Dev: {$devs[$tarefa['T234_C007_Id_Programador']]['C007_Primeiro_Nome']}">
							{$tarefa['T234_Id']}
						</div>
EOT;
                    if (substr($tarefa['final'], -5) == '12:00') {
                        echo <<<EOT
							<div style="background-color: #FFF; height: 15px; width: 120px; float: left; box-sizing: border-box; padding: 2px; border-right: 1px solid #000;" title="Horário de Almoço">
								Almoço
							</div>
EOT;
                    }
                    $ultimoT234_Id = $T234_Id;
                }
                $ultimoC007_Id = $C007_Id;
            }
            if ($ultimaData != $data) {
                echo "<br><br>";
            }
            $ultimaData = $data;
        }
    break;

    case 'gridDesenvolvimentoExecutar':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'retornar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'retornarDesenvolvimento', 'callbackParameter' => '0', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'continuar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '3', 'colunaTipo' => 'livre'),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'C007_Primeiro_Nome', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
		left join D024 on D024_Id=T234_D024_Id
		left join C007 on C007_Id=T234_C007_Id
		left join D046 on D046_Id=D024_D046_Id
		left join T235 on T235_Id=T234_T235_Id
		left join C025 on C025_D024_Id=D024_Id
		LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
		WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
		AND (T234_Flag_Tipo_OS = 'E' OR T234_Flag_Tipo_OS = 'M')
		AND T234_Flag_Finalizado != 'S'
		AND (T234_Flag_Status_Desenvolvimento = '2')
		GROUP BY T234_Id
		ORDER BY T234_Ordem_Desenvolvimento
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        //$valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
        $totalTempo = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIME_TO_SEC(CONCAT(T234_Tempo_Estimado,':00')) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, gRetirarGroupBy($extra), false, false);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<span class="item" style="text-align:right">Tempo<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTempo}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridDesenvolvimentoExecucao':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'retornar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'retornarDesenvolvimento', 'callbackParameter' => '2', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'continuar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '4', 'colunaTipo' => 'livre'),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Responsável', 'style' => 'width:1%'),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
            left join D024 on D024_Id=T234_D024_Id
            left join C007 on C007_Id=T234_C007_Id
            left join D046 on D046_Id=D024_D046_Id
            left join T235 on T235_Id=T234_T235_Id
            left join C025 on C025_D024_Id=D024_Id
            LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
            WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
            AND (T234_Flag_Tipo_OS = 'E' OR T234_Flag_Tipo_OS = 'M')
            AND T234_Flag_Finalizado != 'S'
            AND (T234_Flag_Status_Desenvolvimento = '3')
            GROUP BY T234_Id
            ORDER BY T234_Ordem_Desenvolvimento
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        $totalTempo = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIME_TO_SEC(CONCAT(T234_Tempo_Estimado,':00')) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, gRetirarGroupBy($extra), false, false);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<span class="item" style="text-align:right">Tempo<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTempo}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridDesenvolvimentoTeste':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'retornar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'retornarDesenvolvimento', 'callbackParameter' => '3', 'colunaTipo' => 'livre', 'colunaExibir' => ($r_exibirOpcoesDev == false) ? false : true),
            //array('colunaValor' => 'continuar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '5', 'colunaTipo' => 'livre', 'colunaExibir' => ($r_exibirOpcoesDev == false) ? false : true),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Responsável', 'style' => 'width:1%'),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
            array('colunaValor' => 'T234_Teste', 'colunaTitulo' => 'Teste', 'style' => 'width: 1%', 'style' => 'max-width:200px;white-space:normal;'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
            left join D024 on D024_Id=T234_D024_Id
            left join C007 on C007_Id=T234_C007_Id
            left join D046 on D046_Id=D024_D046_Id
            left join T235 on T235_Id=T234_T235_Id
            left join C025 on C025_D024_Id=D024_Id
            LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
            WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N'
            AND (T234_Flag_Tipo_OS = 'E' OR T234_Flag_Tipo_OS = 'M')
            AND T234_Flag_Finalizado != 'S'
            AND (T234_Flag_Status_Desenvolvimento = '4')
            GROUP BY T234_Id
            ORDER BY T234_Ordem_Desenvolvimento
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        $totalTempo = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIME_TO_SEC(CONCAT(T234_Tempo_Estimado,':00')) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, gRetirarGroupBy($extra), false, false);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<span class="item" style="text-align:right">Tempo<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTempo}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridDesenvolvimentoVersao':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T267_Id', 'colunaTitulo' => 'Release', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T267_Revisao_Final', 'colunaTitulo' => 'Última revisão', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T267_Data_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T267_Data_Fechamento', 'colunaTitulo' => 'Fechamento', 'callback' => 'gCorrigeData'),
        );

        /**
         * Definição da SQL
         */
        $from = "T267";

        $extra = <<<EOT
		    ORDER BY T267_Id
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T267_Id' => array('titulo' => 'Release', 'tipo' => 'texto'),
            'T267_Revisao_Final' => array('titulo' => 'Última revisão', 'tipo' => 'texto'),
            'T267_Data_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'T267_Data_Fechamento' => array('titulo' => 'Fechamento', 'tipo' => 'data'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
        }

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(DISTINCT T267_Id)", $from, $extra);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="btnIncluirVersao">Incluir</button>
				<span class="item" style="text-align: right; float: right; margin-right: 85px;">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#btnIncluirVersao').unbind('click').bind('click', function(data) {
					abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir release', '/outros/registroSuporteCliente/registroSuporteCliente/contentVersao/', '&tipoSuporte=E&acessoSuporte=true&D024_Id=' + encodeURIComponent('{$r_D024_Id}'), [300, 180]);
				});
			} </script></div>
EOT;
        EOT;
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T267_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar release', '/outros/registroSuporteCliente/registroSuporteCliente/contentChamadosVersao/', '&T267_Id=' + encodeURIComponent(id) + '&tabela={$from}', [990, 500]);
				});
			} </script></div>
EOT;
        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuporteConcluido':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'retornar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'retornarDesenvolvimento', 'callbackParameter' => '4', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'continuar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '6', 'colunaTipo' => 'livre'),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Responsável', 'style' => 'width:1%'),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'T234_Email_Solicitante', 'colunaTitulo' => 'T234_Email_Solicitante', 'colunaExibir' => false),
            array('colunaValor' => 'T252_Data_Hora', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
            left join D024 on D024_Id=T234_D024_Id
            left join C007 on C007_Id=T234_C007_Id
            left join D046 on D046_Id=D024_D046_Id
            left join T235 on T235_Id=T234_T235_Id
            left join C025 on C025_D024_Id=D024_Id
            LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
            WHERE T234_Flag_Finalizado = 'O'
            GROUP BY T234_Id
            ORDER BY T234_Id desc
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            if (strtotime($array['todosCampos']['T252_Data_Hora']) < strtotime(date('Y-m-d H:i:s', strtotime("-3 days")))) {
                if ($array['todosCampos']['T234_Flag_Finalizado'] == 'O') {
                    finalizarChamadoAbandonado($array['todosCampos']['T234_Id'], $array['todosCampos']['T234_Email_Solicitante']);
                }
                if ($array['todosCampos']['T252_C007_Id'] > 0) {
                    return 'linhaVermelho';
                }
            }
            if ($array['todosCampos']['T234_C007_Id'] <= 0 || $array['todosCampos']['T252_C007_Id_Cliente'] > 0) {
                return 'linhaNegrito';
            }
        }

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        $valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridDesenvolvimentoGeral':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            //array('colunaValor' => 'retornar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'retornarDesenvolvimento', 'callbackParameter' => '1', 'colunaTipo' => 'livre'),
            //array('colunaValor' => 'continuar', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '1', 'colunaTipo' => 'livre'),
            array('colunaValor' => "T234_Id", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T252_Data_Hora', 'colunaTitulo' => 'Interação', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '<br>Ordem', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%', 'colunaEditar' => true, 'colunaEditarJSCallback' => 'callbackEditarOrdemDesenvolvimento'),
            //array('colunaValor' => "T234_Data_Hora_Inicio_Estimado", 'colunaTitulo' => 'Estimativa<br>Inicio', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
            //array('colunaValor' => "T234_Data_Hora_Final_Estimado", 'colunaTitulo' => 'Estimativa<br>Fim', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;', 'colunaAlinhar' => 'center', 'callback' => 'gCorrigeData'),
            array('colunaValor' => "T234_Flag_Status_Desenvolvimento", 'colunaTitulo' => 'Status', 'style' => 'width: 1%', 'callback' => 'statusDesenvolvimento'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'C007_Primeiro_Nome', 'colunaExibir' => false),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'T234_Descricao', 'colunaTitulo' => 'Chamado', 'style' => 'min-width:300px; max-width:300px; white-space:normal; word-wrap:break-word;', 'callback' => 'stripTags'),
            array('colunaValor' => 'T252_Descricao', 'colunaTitulo' => 'Resposta', 'style' => 'min-width:300px;  max-width:300px; white-space:normal; word-wrap:break-word;', 'callback' => 'stripTags'),
            //array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            //array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback'=>'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T252";

        $extra = <<<EOT
			LEFT JOIN T234 ON T234_Id=T252_T234_Id
			LEFT JOIN D024 ON D024_Id=T234_D024_Id
			LEFT JOIN C007 ON C007_Id=T234_C007_Id
			LEFT JOIN D046 ON D046_Id=D024_D046_Id
			LEFT JOIN T235 ON T235_Id=T234_T235_Id
			WHERE T234_Flag_Tipo_Suporte = 'S'
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'numero'),
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'T252_Data_Hora' => array('titulo' => 'Interação', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Chamado', 'tipo' => 'texto'),
            'T252_Descricao' => array('titulo' => 'Resposta', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Status_Desenvolvimento' => array('titulo' => 'Status', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'IF(IFNULL(T252_C007_Id[[virgula]]0) > 0[[virgula]] T252_Usuario[[virgula]] "")' => array('titulo' => 'Responsável', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
         * Botões (Release Acima do GRID)
         */
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="incluirSuporteAbaGeral">Incluir <b>(F9)</b></button>
				<button type="button" id="registroSuporteClienteBtnAtualizarCronograma">Atualizar cronograma</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#incluirSuporteAbaGeral').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluir/','', [550,400]);
				});

				$('#registroSuporteClienteBtnAtualizarCronograma').unbind('click').bind('click', function(data) {
					$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/atualizarCronogramaDesenvolvimento/?ajax=true&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						} else {
							dialogConfirm('Erro', request.data);
						}
					});
				});

				function callbackEditarOrdemDesenvolvimento(T234_Id, campoEditado, valorAntigo, novaOrdem) {
					T234_Id = T234_Id.replace(/T234_Id-/, '');
					$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/processarPrioridadeChamado/?ajax=true&T234_Id=' + encodeURIComponent(T234_Id) + '&novaOrdem=' + encodeURIComponent(novaOrdem) + '&callback=?', function(request) {
						if (request.code) {
							divRefresh('{$g['divId']}', true);
						}
					});
				}
			} </script></div>
EOT;
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        $tempoTotal = secToTime($g['sqlAuto']->pegarSqlCampo("TIME_TO_SEC(T234_Tempo_Estimado)", $from, $extra, false, false, true));
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<span class="item" style="text-align:right">Tempo estimado<br><input type="text" class="whiteBg" id="inputRecebido" value="{$tempoTotal}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'SPAN' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;
        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridChamadosVersao':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'T234_Flag_Finalizado', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Nome técnico', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'C007_Id', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
            left join D024 on D024_Id=T234_D024_Id
            left join C007 on C007_Id=T234_C007_Id
            left join D046 on D046_Id=D024_D046_Id
            left join T235 on T235_Id=T234_T235_Id
            left join C025 on C025_D024_Id=D024_Id
            LEFT JOIN (SELECT * FROM T252 ORDER BY T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
            WHERE T234_Flag_Tipo_Suporte = 'S'
            AND T234_T267_Id = '{$r_T267_Id}'
            GROUP BY T234_Id
            ORDER BY T234_Id desc
EOT;

        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Finalizado (S/N/O)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("T234_Id", $from, $extra, true);
        $totalTempo = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIME_TO_SEC(CONCAT(T234_Tempo_Estimado,':00')) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, gRetirarGroupBy($extra), false, false);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<span class="item" style="text-align:right">Tempo<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalTempo}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;
        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'SPAN' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;
        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'atualizarCronogramaDesenvolvimento':
        $resposta = array('code' => true, 'data' => '');

        $devs = array();
        $sqlC007 = mysql_query("SELECT C007_Id, C007_Primeiro_Nome, C007_Flag_Vendedor FROM C007 WHERE C007_Cargo = 'PROGRAMADOR'");
        while ($rowC007 = mysql_fetch_array($sqlC007)) {
            $devs[$rowC007['C007_Id']] = array('C007_Primeiro_Nome' => $rowC007['C007_Primeiro_Nome'], 'alcada' => ($rowC007['C007_Flag_Vendedor'] == 'S') ? 2 : 1);
        }

        $devs = array();
        $devs['999'] = array('C007_Primeiro_Nome' => 'Programador', 'alcada' => 2);
        unset($dados);
        $dataAtual = date("Y-m-d H:i");
        $ultimoDev = false;
        $contronograma = array();
        $sqlT234 = mysql_query("SELECT * FROM T234 WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N' AND T234_Flag_Finalizado != 'S' ORDER BY T234_Ordem_Desenvolvimento, T234_Data_Hora_Abertura");
        while ($rowT234 = mysql_fetch_array($sqlT234)) {
            $C007_Id_Dev = false;
            $devsLivre = array();
            foreach ($devs as $id => $dados) {
                //Verifica os programadores que podem resolver o problema
                if ($dados['alcada'] >= $rowT234['T234_Nivel_Suporte']) {
                    //$devsLivre[] = array($id, $dados['finalUltimaTarefa']);
                    //Verifica se a ultima tarefa já não foi atribuida ao programador
                    //if($id != $ultimoDev){
                    $C007_Id_Dev = $id;
                    $finalUltimaTarefa = isset($dados['finalUltimaTarefa']) ? $dados['finalUltimaTarefa'] : $dataAtual;
                    break 1;
                    //}
                }
            }
            ////Nenhum programador disponível, pegamos o primeiro que pode executar a tarefa
            //if($C007_Id_Dev == false){
            //    $ultimoDev = $C007_Id_Dev = $devsLivre[0][0];
            //    $finalUltimaTarefa = $devsLivre[0][1];
            //}
            $cronogramaTarefa = calcularFinalTarefa($finalUltimaTarefa, $rowT234['T234_Tempo_Estimado']);
            foreach ($cronogramaTarefa as $seq => $detalhes) {
                $cronograma[substr($detalhes['inicio'], 0, 10)][] = array('T234_Id' => $rowT234['T234_Id'], 'T234_C007_Id_Programador' => $C007_Id_Dev, 'inicio' => $detalhes['inicio'], 'final' => $detalhes['final'], 'duracao' => $detalhes['duracao']);
                $devs[$C007_Id_Dev]['finalUltimaTarefa'] = $detalhes['final'];
            }
        }

        foreach ($cronograma as $data => $tarefas) {
            foreach ($tarefas as $id => $tarefa) {
                if ($ultimoT234_Id == $tarefa['T234_Id']) {
                    $sql = "UPDATE T234 SET T234_Data_Hora_Final_Estimado = '{$tarefa['final']}' WHERE T234_Id = '{$tarefa['T234_Id']}'";
                } else {
                    $sql = "UPDATE T234 SET T234_Data_Hora_Inicio_Estimado = '{$tarefa['inicio']}', T234_Data_Hora_Final_Estimado = '{$tarefa['final']}', T234_C007_Id_Programador = '{$tarefa['T234_C007_Id_Programador']}' WHERE T234_Id = '{$tarefa['T234_Id']}'";
                }
                log($sql);
                mysql_query($sql);
                $ultimoT234_Id = $tarefa['T234_Id'];
            }
        }

        $resposta['code'] = true;
        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'formAnaliseDesenvolvimento':
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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
        $form_tabela = 'T234';
        $form_campo_pk = 'T234_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'formAnaliseDesenvolvimento_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formAnaliseDesenvolvimento/';

        // novo formulário
        $form = new Formulario('formAnaliseDesenvolvimento');
        // Editor
        $form_editor = array(
            'geral' => '{"T234_Analise":{"top":7,"left":23,"height":430,"width":872,"hidden":0}}',
        );

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T234_Id' => array('name' => 'T234_Id', 'type' => 'hidden', 'value' => $r_acaoId),
                    'T234_Analise' => array('name' => 'T234_Analise', 'label' => 'Análise', 'type' => 'textarea'),
                ),
            ),
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
                // Insert ou Update?
                if (!empty($acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
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
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="formAnaliseDesenvolvimento_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'formVersao':
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

        $sqlT267 = mysql_query("SELECT T267_Id FROM T267 ORDER BY T267_Id DESC LIMIT 1");
        $rowT267 = mysql_fetch_assoc($sqlT267);
        $rowT267['T267_Id']++;

        $T267_Id = !empty($acaoId) ? $acaoId : $rowT267['T267_Id'];

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
        $form_tabela = 'T267';
        $form_campo_pk = 'T267_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'formAnaliseDesenvolvimento_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formVersao/';

        // novo formulário
        $form = new Formulario('formVersao');
        // Editor
        $form_editor = array(
            'geral' => '{"T267_Id":{"top":7,"left":39.59375,"width":72,"hidden":0},"T267_Revisao_Final":{"top":30,"left":34,"width":152,"hidden":0},"T267_Data_Abertura":{"top":53,"left":32,"width":88,"hidden":0},"T267_Data_Fechamento":{"top":76,"left":13,"width":88,"hidden":0}}',
        );

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T267_Id' => array('name' => 'T267_Id', 'label' => 'Release', 'type' => 'text', 'readonly' => true, 'value' => $T267_Id, 'reject' => true),
                    'T267_Revisao_Final' => array('name' => 'T267_Revisao_Final', 'label' => 'Revisão', 'type' => 'text'),
                    'T267_Data_Abertura' => array('name' => 'T267_Data_Abertura', 'label' => 'Abertura', 'type' => 'data'),
                    'T267_Data_Fechamento' => array('name' => 'T267_Data_Fechamento', 'label' => 'Fechamento', 'type' => 'data'),
                ),
            ),
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
                // Insert ou Update?
                if (!empty($acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        $alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!', false);
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        $alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
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
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="formAnaliseDesenvolvimento_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'formCabecalhoChamado':
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        require_once "prog/outros/registroSuporteCliente/form_func-js.php";

        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

        $sqlT234 = mysql_query("SELECT * FROM T234 WHERE T234_Id = '{$acaoId}'");
        $rowT234 = mysql_fetch_assoc($sqlT234);
        $rowT234['T234_Data_Hora_Abertura'] = gCorrigeData($rowT234['T234_Data_Hora_Abertura']);
        $rowT234['T234_Data_Hora_Finalizado'] = gCorrigeData($rowT234['T234_Data_Hora_Finalizado']);

        $D166_Id = (!empty($rowT234['T234_D166_Id'])) ? $rowT234['T234_D166_Id'] : 0;

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
        $form_tabela = 'T234';
        $form_campo_pk = 'T234_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'formCabecalhoChamado_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formCabecalhoChamado/';

        // novo formulário
        $form = new Formulario('formCabecalhoChamado');
        // Editor
        $form_editor = array(
            'geral' => '{"T234_Id":{"top":7,"left":26.1875,"width":120,"hidden":0},"T234_Solicitante":{"top":7,"left":343.125,"width":152,"hidden":0},"T234_D024_Id":{"top":30,"left":22.53125,"width":458,"hidden":0},"T234_Data_Hora_Abertura":{"top":53,"left":31.6875,"width":120,"hidden":0},"T234_Data_Hora_Finalizado":{"top":53,"left":364.734375,"width":120,"hidden":0},"T234_Flag_Finalizado":{"top":76,"left":42.703125,"width":128,"hidden":0},"T234_Flag_Triagem":{"top":76,"left":449.953125,"width":64,"hidden":0},"T234_Flag_Tipo_OS":{"top":99,"left":52.890625,"width":128,"hidden":0},"T234_Flag_Personalizado":{"top":99,"left":420.171875,"width":64,"hidden":0},"T234_Revisao_SVN":{"top":122,"left":26.796875,"width":120,"hidden":0},"T234_Horas_Desenvolvimento":{"top":122,"left":428.75,"width":56,"hidden":0},"T234_T267_Id":{"top":145,"left":33.515625,"width":120,"hidden":0},"T234_T272_Id":{"top":145,"left":407.640625,"width":111,"hidden":0},"T234_C007_Id":{"top":168,"left":10.28125,"width":127,"hidden":0},"T234_D166_Id":{"top":168,"left":325.1875,"width":191,"hidden":0},"T234_Chamado_Origem_Id":{"top":191,"left":20.0625,"width":120,"hidden":0},"T234_D167_Id":{"top":191,"left":338.640625,"width":191,"hidden":0}}',
        );

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T234_Id' => array('name' => 'T234_Id', 'label' => 'Chamado', 'type' => 'text', 'readonly' => true),
                    'T234_Data_Hora_Abertura' => array('name' => 'T234_Data_Hora_Abertura', 'label' => 'Abertura', 'type' => 'text', 'readonly' => true, 'value' => $rowT234['T234_Data_Hora_Abertura'], 'reject' => true),
                    'T234_Data_Hora_Finalizado' => array('name' => 'T234_Data_Hora_Finalizado', 'label' => 'Fechamento', 'type' => 'text', 'readonly' => true, 'value' => $rowT234['T234_Data_Hora_Finalizado'], 'reject' => true),

                    'T234_D024_Id' => array('name' => 'T234_D024_Id', 'label' => 'Empresa', 'type' => 'pesquisa', 'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa')),
                    'T234_Solicitante' => array('name' => 'T234_Solicitante', 'label' => 'Solicitante', 'type' => 'text'),

                    'T234_Flag_Finalizado' => array('name' => 'T234_Flag_Finalizado', 'label' => 'Status', 'type' => 'select', 'select' => array(
                            array('title' => 'PENDENTE', 'value' => 'N'),
                            array('title' => 'ATENDIMENTO', 'value' => 'A'),
                            array('title' => 'CONCLUÍDO', 'value' => 'O'),
                            array('title' => 'FINALIZADO', 'value' => 'S'),
                        ),
                    ),
                    'T234_Flag_Tipo_OS' => array('name' => 'T234_Flag_Tipo_OS', 'label' => 'Tipo', 'type' => 'select', 'select' => array(
                        array('title' => 'ERRO', 'value' => 'E'),
                        array('title' => 'DÚVIDA', 'value' => 'D'),
                        array('title' => 'MELHORIA', 'value' => 'M'),
                        array('title' => 'BACKLOG', 'value' => 'B'),
                    ),
                    ),
                    'T234_Flag_Triagem' => array('name' => 'T234_Flag_Triagem', 'label' => 'Triagem', 'type' => 'select', 'select' => array(
                        array('title' => 'NÃO', 'value' => 'N'),
                        array('title' => 'SIM', 'value' => 'S'),
                    ),
                    ),
                    'T234_Flag_Personalizado' => array('name' => 'T234_Flag_Personalizado', 'label' => 'Personalizado', 'type' => 'select', 'select' => array(
                        array('title' => 'NÃO', 'value' => 'N'),
                        array('title' => 'SIM', 'value' => 'S'),
                    ),
                    ),
                    'T234_C007_Id' => array('name' => 'T234_C007_Id', 'label' => 'Responsável', 'type' => 'select', 'select' =>
                        gGeraSelect('C007', 'C007_Id', 'C007_Primeiro_Nome', '', true),
                    ),
                    'T234_Revisao_SVN' => array('name' => 'T234_Revisao_SVN', 'label' => 'Revisão', 'type' => 'text'),
                    'T234_T267_Id' => array('name' => 'T234_T267_Id', 'label' => 'Release', 'type' => 'text'),
                    'T234_Flag_Tipo_Suporte' => array('name' => 'T234_Flag_Tipo_Suporte', 'label' => 'Release', 'type' => 'hidden'),
                    'T234_Horas_Desenvolvimento' => array('name' => 'T234_Horas_Desenvolvimento', 'label' => 'Horas Prazo', 'type' => 'tempo'),
                    'T234_Valor_Hora_Tecnica' => array('name' => 'T234_Valor_Hora_Tecnica', 'label' => 'Valor Hora Técnica', 'type' => 'hidden'),
                    'T234_T272_Id' => array('name' => 'T234_T272_Id', 'label' => 'Pacote', 'type' => 'select', 'select' =>
                        gGeraSelect('T272', 'T272_Id', 'T272_Mes,T272_Ano', '', true),
                    ),
                    'T234_D166_Id' => array('name' => 'T234_D166_Id', 'label' => 'Quadro', 'type' => 'select', 'extra' => "onchange=\"alteraLista(this, '{$g['divId']}')\"", 'select' =>
                        gGeraSelect('D166', 'D166_Id', 'D166_Grupo_Suporte', 'ORDER BY D166_Grupo_Suporte', true),
                    ),
                    'T234_D167_Id' => array('name' => 'T234_D167_Id', 'label' => 'Lista', 'type' => 'select', 'align' => 'right', 'style' => 'float:right', 'select' =>
                        gGeraSelect('D167', 'D167_Id', 'D167_Nome_Lista', "WHERE D167_D166_Id = '{$D166_Id}' ORDER BY D167_Nome_Lista", true),
                    ),
                    'T234_Chamado_Origem_Id' => array('name' => 'T234_Chamado_Origem_Id', 'label' => 'Retrabalho', 'type' => 'text'),
                ),
            ),
        );
        $form->definirFormularioArray($formArray);
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#T234_D167_Id_refreshBtn').bind('click', function(){
					alteraLista($('#T234_D166_Id'), '{$g['divId']}');
				});
			} </script></div>
EOT;

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
                // Insert ou Update?
                if (!empty($acaoId)) {
                    $sqlT234 = mysql_query("SELECT * FROM T234 WHERE T234_Id = '{$acaoId}'");
                    $rowT234 = mysql_fetch_array($sqlT234);
                    // Update
                    $erro = false;
                    $form->campoSetarValor('T234_Revisao_SVN', trim($form->campoValorEnviado('T234_Revisao_SVN')), true);
                    $T234_Revisao_SVN = $form->campoValorEnviado('T234_Revisao_SVN');
                    $T234_C007_Id = $form->campoValorEnviado('T234_C007_Id');
                    $T234_T267_Id = $form->campoValorEnviado('T234_T267_Id');
                    $T234_D166_Id = $form->campoValorEnviado('T234_D166_Id');
                    $T234_D167_Id = $form->campoValorEnviado('T234_D167_Id');
                    $T234_Flag_Finalizado = $form->campoValorEnviado('T234_Flag_Finalizado');
                    $T234_Flag_Tipo_Suporte = $form->campoValorEnviado('T234_Flag_Tipo_Suporte');
                    $T234_Flag_Personalizado = $form->campoValorEnviado('T234_Flag_Personalizado');

                    if ((empty($T234_C007_Id) || ($T234_Flag_Personalizado == 'N' && (empty($T234_Revisao_SVN) || empty($T234_T267_Id) || empty($T234_D166_Id)))) && $T234_Flag_Finalizado != 'N' && $T234_Flag_Tipo_Suporte == 'S') {
                        if (empty($T234_C007_Id)) {
                            $erro = 'O responsável deve ser preenchido antes de concluir/finalizar o chamado';
                        }
                        if (empty($T234_D166_Id)) {
                            $erro = 'O grupo deve ser preenchido antes de concluir/finalizar o chamado';
                        } else {
                            $erro = 'Deve deve ser informado a revisão SVN e Release ou marcar se foi personalizado antes de concluir/finalizar o chamado';
                        }
                    }

                    //Valida a revisão caso seja alterada
                    if ($rowT234['T234_Revisao_SVN'] != $T234_Revisao_SVN && !empty($T234_Revisao_SVN)) {
                        $sqlRevisao = mysql_query("SELECT T234_Revisao_SVN FROM T234 WHERE T234_Revisao_SVN REGEXP '[0-9]' ORDER BY CAST(T234_Revisao_SVN AS UNSIGNED) DESC LIMIT 1");
                        $rowRevisao = mysql_fetch_assoc($sqlRevisao);
                        if ($T234_Revisao_SVN > ($rowRevisao['T234_Revisao_SVN'] + 50)) {
                            //$erro = 'A revisão informada é muito superior a última revisão registrada!';
                        } else if ($T234_Revisao_SVN < $rowRevisao['T234_Revisao_SVN']) {
                            //$erro = 'A revisão informada é inferior a última revisão registrada!';
                        }
                    }

                    //Valida o release caso seja alterada
                    if ($rowT234['T234_T267_Id'] != $T234_T267_Id && !empty($T234_T267_Id)) {
                        $sqlT267 = mysql_query("SELECT T267_Id, T267_Data_Fechamento FROM T267 WHERE T267_Id = '{$T234_T267_Id}'");
                        if (mysql_num_rows($sqlT267) > 0) {
                            $rowT267 = mysql_fetch_assoc($sqlT267);
                            if ($rowT267['T267_Data_Fechamento'] != '0000-00-00') {
                                $erro = "O release informado já está fechada!";
                            }
                        } else {
                            $erro = "O release informado não existe!";
                        }
                    }

                    $sqlD167 = mysql_query("SELECT D167_Flag_Finalizar_Chamado FROM D167 WHERE D167_Id = '{$T234_D167_Id}'");
                    $rowD167 = mysql_fetch_array($sqlD167);

                    //Preenche/limpa a data hora finalização caso o status seja alterado
                    if ($rowT234['T234_Flag_Finalizado'] != $T234_Flag_Finalizado) {
                        if ($T234_Flag_Finalizado == 'S') {
                            if ($rowD167['D167_Flag_Finalizar_Chamado'] == "S") {
                                mysql_query("UPDATE T234 SET T234_Data_Hora_Finalizado=CURRENT_TIMESTAMP(), T234_Flag_Status_Desenvolvimento = '5' WHERE T234_Id='{$acaoId}'");

                                //Será arquivado o card no trello ao ser finalizado no sistema
                                require_once 'bibliotecas/webservices/trello/apiTrello.php';
                                $Trello = new Trello();

                                $retorno = $Trello->arquivarCard($acaoId, true);

                                if ($retorno[0] === true) {
                                    $mensagem = $retorno[1];
                                } else {
                                    $erro = $retorno[1];
                                }

                            } else {
                                $erro = "Esse chamado está em uma lista sem permissão para finalizar, verifique com a equipe de desenvolvimento.";
                            }
                        } else if ($T234_Flag_Finalizado == 'N') {
                            mysql_query("UPDATE T234 SET T234_Data_Hora_Finalizado='0000-00-00 00:00:00', T234_Flag_Status_Desenvolvimento = '0' WHERE T234_Id='{$acaoId}'");
                        } else {
                            mysql_query("UPDATE T234 SET T234_Data_Hora_Finalizado='0000-00-00 00:00:00', T234_Flag_Status_Desenvolvimento = '5' WHERE T234_Id='{$acaoId}'");
                        }
                    }

                    if (!empty($erro)) {
                        echo gGeraAlertMsg('Erro', $erro);
                    } else {
                        $sqlT234 = mysql_query("SELECT T234_Email_Solicitante, T234_Flag_Finalizado FROM T234 WHERE T234_Id='{$acaoId}'");
                        $rowT234 = mysql_fetch_array($sqlT234);
                        if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                            if ($T234_Flag_Finalizado == 'S' and $rowT234['T234_Flag_Finalizado'] != 'S') {
                                finalizarChamadoAbandonado($acaoId, $rowT234['T234_Email_Solicitante'], $g['usuarioAtual']);
                            }

                            // Imprime uma mensagem e fecha a janela
                            $alert = gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!' . $mensagem, false);
                            echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
										{$alert}
										}, 150);
	  								});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
								} </script></div>
EOT;
                        }
                    }
                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        $alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
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
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="formCabecalhoChamado_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'formCabecalhoChamadoCliente':
        global $g;

        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

        $sqlT234 = mysql_query("SELECT * FROM T234 LEFT JOIN T272 ON T272_Id=T234_T272_Id WHERE T234_Id = '{$acaoId}'");
        $rowT234 = mysql_fetch_assoc($sqlT234);
        $rowT234['T234_Data_Hora_Abertura'] = gCorrigeData($rowT234['T234_Data_Hora_Abertura']);
        $rowT234['T234_Data_Hora_Finalizado'] = gCorrigeData($rowT234['T234_Data_Hora_Finalizado']);
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
        $form_tabela = 'T234';
        $form_campo_pk = 'T234_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'formCabecalhoChamado_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formCabecalhoChamadoCliente/';

        // novo formulário
        $form = new Formulario('formCabecalhoChamado');
        // Editor
        $form_editor = array(
            'geral' => '{"atendimentoMelhoria":{"top":237,"left":384,"width":370,"hidden":0},"T234_Id":{"top":7,"left":26.1875,"width":120,"hidden":0},"introducao":{"top":28,"left":23,"width":null,"hidden":0},"T234_Data_Hora_Abertura":{"top":30,"left":31.6875,"width":120,"hidden":0},"observacao":{"top":76,"left":5,"width":null,"hidden":1},"texto":{"top":104,"left":18,"width":null,"hidden":0},"textoSelecaoMes":{"top":226,"left":21,"width":null,"hidden":0},"atencao":{"top":270,"left":19,"width":null,"hidden":0}}',
        );

        if ($rowT234['T234_T272_Id'] <= 0 || ($rowT234['T272_Mes'] >= date('m') && $rowT234['T272_Ano'] >= date('y'))) {
            $disabled = false;
            $filtro = 'WHERE IF(T272_Ano>YEAR(NOW()),T272_Ano>YEAR(NOW()),T272_Mes>MONTH(NOW()) AND T272_Ano>=YEAR(NOW())) ORDER BY T272_Ano,T272_Mes';
        } else {
            $disabled = true;
        }

        $T135 = mysql_query("SELECT T135_Qtde_Maxima_Melhorias_Mes,
                                    T135_Qtde_Maxima_Chamados_Duvida,
		                            T135_Valor_Hora_Tecnica,
		                            CONCAT((DATE_FORMAT(T135_Franquia_Horas, '%H:%i')), ' horas')AS horasFranquia
			                   FROM T135
			              LEFT JOIN D024 ON T135_D024_Id=D024_Id
			                  WHERE T135_D024_Id='{$rowT234['T234_D024_Id']}'
			                    AND T135_Flag_Cancelado != 'S'
			                    AND T135_Tipo='M'");
        $mT135 = mysql_fetch_array($T135);

        $maximoChamadosAtendimento = $mT135['T135_Qtde_Maxima_Melhorias_Mes']+$mT135['T135_Qtde_Maxima_Chamados_Duvida'];

        $introducao = "<p style='margin-bottom:20px; line-height:2.1'><br><b>Nesta tela você abrir seu chamado de sugestão de melhoria. Esse chamado pode ser enviado imediatamente para fila de atendimento ou apensar registrar como chamamado em aberto.</b><br></p>";

        $texto = "
        <br>
		<p style='margin-bottom:20px;'><br>
			• Você pode enviar até <b><font color='#3366ff'>{$maximoChamadosAtendimento}</font></b> chamados de melhoria/dúvida para atendimento por vez.</font></br>
		</p>
		<p style='margin-bottom:20px;'>
			• Para maiores informações sobre as regras de abertura de chamados acesse: <a href ='https://drive.google.com/file/d/1Mg9_C9xeUqV45QiDAlHRPkSZxoifiuD0/view?usp=sharing' target='_blank'><font color='#3366ff'><b>Regras de abertura de chamados.pdf</b></font></a>
		</p>";

        $atencao = "<p style='margin-bottom:20px; padding:10px;  background-color:#f9f7bc;border-radius: 5px; line-height:1.5;'>
            <b>ATENÇÃO!</b><br>
			• Selecionando a opção <b>APENAS REGISTRAR</b> o seu chamado apenas ficará salvo na base de dados. Caso deseje que essa<br> melhoria seja analisada, realoque-o para atendimento (Você pode recuperar esse chamado na aba:<br><b>'Chamados em Aberto'</b> na tela anterior).
		</p>";

        $textoSelecaoMes = "<p><b>Selecione uma das opções para registar em seu chamado:</b></p>";

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T234_Id' => array('name' => 'T234_Id', 'label' => 'Chamado', 'type' => 'text', 'readonly' => true),
                    'T234_Data_Hora_Abertura' => array('name' => 'T234_Data_Hora_Abertura', 'label' => 'Abertura', 'type' => 'text', 'readonly' => true, 'value' => $rowT234['T234_Data_Hora_Abertura'], 'reject' => true),
                    'T234_Flag_Finalizado' => array('name' => 'T234_Flag_Finalizado', 'label' => 'Status', 'type' => 'hidden', 'select' => array(
                        array('title' => 'PENDENTE', 'value' => 'N'),
                        array('title' => 'FINALIZADO', 'value' => 'S'),
                    ),
                    ),
                    'T234_Origem_Chamado' => array('name' => 'T234_Origem_Chamado', 'label' => 'Tipo', 'type' => 'hidden', 'value' => "I"),
                    'T234_Flag_Tipo_OS' => array('name' => 'T234_Flag_Tipo_OS', 'label' => 'Tipo', 'type' => 'hidden', 'readonly' => true),
                    'T234_Flag_Triagem' => array('name' => 'T234_Flag_Triagem', 'label' => 'Tipo', 'type' => 'hidden', 'readonly' => true),
                    'introducao' => array('name' => 'introducao', 'type' => 'comentario', 'value' => $introducao, 'label' => '', 'reject' => true, 'style' => 'font-size: 13px;'),
                    'texto' => array('name' => 'texto', 'type' => 'comentario', 'value' => $texto, 'label' => '', 'reject' => true, 'style' => 'font-size: 13px;'),
                    'textoSelecaoMes' => array('name' => 'textoSelecaoMes', 'type' => 'comentario', 'value' => $textoSelecaoMes, 'label' => '', 'reject' => true, 'style' => 'font-size: 13px;'),
                    'observacao' => array('name' => 'observacao', 'type' => 'comentario', 'value' => $observacao, 'label' => '', 'reject' => true, 'style' => 'font-size: 13px;'),
                    'atencao' => array('name' => 'atencao', 'type' => 'comentario', 'value' => $atencao, 'label' => '', 'disabled' => $disabled, 'reject' => true, 'style' => 'font-size: 13px;'),
                    'atendimentoMelhoria' => array('name' => 'atendimentoMelhoria', 'label' => '','reject' => true, 'type' => 'select', 'select' =>
						array(
							array('title' => 'APENAS REGISTRAR (não quero desenvolver ainda)', 'value' => 'B'),
							array('title' => 'ENVIAR FILA ATENDIMENTO', 'value' => 'M'),
						)	
					),
                    //'T234_T272_Id' => array('name' => 'T234_T272_Id', 'label' => 'Pacote', 'type' => 'select', 'disabled' => $disabled, 'select' => gGeraSelect('T272', 'T272_Id', "T272_Mes,T272_Ano as ano", "$filtro ORDER BY T272_Ano,T272_Mes", true, false, false), ),
                ),
            ),
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
                // Insert ou Update?
                if (!empty($acaoId)) {
                    $sqlT234 = mysql_query("SELECT * FROM T234 WHERE T234_Id = '{$acaoId}'");
                    $rowT234 = mysql_fetch_array($sqlT234);
                    // Update
                    $erro = false;
                    $form->campoSetarValor('T234_Revisao_SVN', trim($form->campoValorEnviado('T234_Revisao_SVN')), true);
                    $T234_Revisao_SVN = $form->campoValorEnviado('T234_Revisao_SVN');
                    $T234_C007_Id = $form->campoValorEnviado('T234_C007_Id');
                    $T234_T267_Id = $form->campoValorEnviado('T234_T267_Id');
                    $T234_Flag_Finalizado = $form->campoValorEnviado('T234_Flag_Finalizado');
                    $T234_Flag_Tipo_Suporte = $form->campoValorEnviado('T234_Flag_Tipo_Suporte');
                    $T234_Flag_Personalizado = $form->campoValorEnviado('T234_Flag_Personalizado');
                    $T234_C007_Id = $form->campoValorEnviado('T234_C007_Id');
                    // $T234_T272_Id = $form->campoValorEnviado('T234_T272_Id');
                    $atendimentoMelhoria = $form->campoValorEnviado('atendimentoMelhoria');
                    $form->campoSetarValor('T234_Flag_Tipo_OS', $atendimentoMelhoria, true);

                    //Preenche/limpa a data hora finalização caso o status seja alterado
                    if ($rowT234['T234_Flag_Finalizado'] != $T234_Flag_Finalizado) {
                        if ($T234_Flag_Finalizado == 'S') {
                            mysql_query("UPDATE T234 SET T234_Data_Hora_Finalizado=CURRENT_TIMESTAMP(), T234_Flag_Status_Desenvolvimento = '5' WHERE T234_Id='{$acaoId}'");
                        } else if ($T234_Flag_Finalizado == 'N') {
                            mysql_query("UPDATE T234 SET T234_Data_Hora_Finalizado='0000-00-00 00:00:00', T234_Flag_Status_Desenvolvimento = '0' WHERE T234_Id='{$acaoId}'");
                        } else {
                            mysql_query("UPDATE T234 SET T234_Data_Hora_Finalizado='0000-00-00 00:00:00', T234_Flag_Status_Desenvolvimento = '5' WHERE T234_Id='{$acaoId}'");
                        }
                    }

                    if ($atendimentoMelhoria=="M") {
                        $qteMelhorias = mysql_query("SELECT T135_Qtde_Maxima_Melhorias_Mes
							                           FROM T135
							                      LEFT JOIN D024 ON T135_D024_Id=D024_Id
							                          WHERE T135_D024_Id='{$rowT234['T234_D024_Id']}'
							                            AND T135_Flag_Cancelado != 'S'
							                            AND T135_Tipo='M'");
                        $mqteMelhorias = mysql_fetch_array($qteMelhorias);
                        $chamados = mysql_query("SELECT T234_Id, COUNT(T234_Id) as QtdeChamados,GROUP_CONCAT(' ',T234_Id) AS Chamados
                                                    FROM T234
                                                    LEFT JOIN D166 ON T234_D166_Id=D166_Id
                                                    WHERE T234_D024_Id='{$rowT234['T234_D024_Id']}'
                                                    AND (T234_Flag_Tipo_OS='M' OR T234_Flag_Tipo_OS='D')
                                                    AND T234_Flag_Finalizado != 'S'");
                        $mqtdChamados = mysql_fetch_array($chamados);

                        if ($mqtdChamados['QtdeChamados'] >= $maximoChamadosAtendimento) {
                            $erro = "<font color='red'>Você <b>excedeu</b> a quantidade de <b>{$maximoChamadosAtendimento}</b> chamados permitidos de <b>MELHORIA/DÚVIDA</b> em <b>FILA DE ATENDIMENTO</b>.
                            <br>Por favor, aguarde um de seus chamados ser concluído para selecionar novos chamados para atendimento.<br><br>Você possui os seguintes chamados pendentes:<br><b>{$mqtdChamados['Chamados']}</b></font>";
                        }else{
                            $form->campoSetarValor('T234_Flag_Triagem', "S", true);
                            $T252_Descricao = "Chamado selecionado para atendimento pelo usuário {$g['C007']['C007_Primeiro_Nome']}";
                            mysql_query("INSERT INTO T252 (
                                                     T252_C007_Id, 
                                                     T252_Usuario, 
                                                     T252_T234_Id, 
                                                     T252_Descricao, 
                                                     T252_Data_Hora, 
                                                     T252_C007_Id_Cliente, 
                                                     T252_Flag_Lido, 
                                                     T252_Flag_Feedback) 
                                              VALUES (
                                                    '{$g['usuarioAtual']}',
                                                    '{$g['C007']['C007_Primeiro_Nome']}', 
                                                    '{$acaoId}',
                                                    '$T252_Descricao', 
                                                    now(),
                                                    '{$rowT234['T234_D024_Id']}', 
                                                    'N', 
                                                    'S')");
                        }
                    }

                    if (!empty($erro)) {
                        $alert = gGeraAlertMsg('Erro', $erro, false);
                        echo <<<EOT
                                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                                    {$alert};
                                    fecharJanela('{$g['divId']}');
                                } </script></div>
EOT;
                    } else {
                        if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                            if ($T234_Flag_Finalizado == 'S') {
                                $sqlT234 = mysql_query("SELECT T234_Email_Solicitante FROM T234 WHERE T234_Id='{$acaoId}'");
                                $rowT234 = mysql_fetch_array($sqlT234);
                                finalizarChamadoAbandonado($acaoId, $rowT234['T234_Email_Solicitante'], $g['usuarioAtual'], $g['C007']['C007_Primeiro_Nome']);
                            }
                            // Imprime uma mensagem e fecha a janela
                            $alert = gGeraAlertMsg('Operação OK', "Registro alterado com sucesso!", false);
                            echo <<<EOT
								<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
									$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
										setTimeout(function() {
										{$alert}
										}, 150);
	  								});
									$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
									divRefresh('{$g['divIdParent']}', true);
								} </script></div>
EOT;
                        }
                    }

                } else {
                    // Insert
                    if ($sql = $g['sqlAuto']->gerarSQLInsert($form_tabela, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        $alert = gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!', false);
                        echo <<<EOT
							<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
								$("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
									setTimeout(function() {
									{$alert}
									}, 150);
  								});
								$("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
								divRefresh('{$g['divIdParent']}', true);
							} </script></div>
EOT;
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
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
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="formCabecalhoChamado_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'gridHistoricoDesenvolvimento':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T254_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T254_Data_Hora', 'colunaTitulo' => 'Data hora', 'style' => 'width: 1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Técnico', 'style' => 'width: 1%'),
            array('colunaValor' => 'T254_Status', 'colunaTitulo' => 'Status'),
        );

        /**
         * Definição da SQL
         */
        $from = "T254";

        $extra = <<<EOT
			LEFT JOIN C007 ON C007_Id=T254_C007_Id
			WHERE T254_T234_Id = '{$r_acaoId}'
EOT;
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        //echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridDetalhesIntegracao':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T273_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => true),
            array('colunaValor' => 'T273_Data_Interacao', 'colunaTitulo' => 'Data', 'style' => 'width: 1%', 'callback' => 'gCorrigeData', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Usuário', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T273_Tipo', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T273_Descricao', 'colunaTitulo' => 'Descricao', 'style' => 'width: 1%'),
            array('colunaValor' => 'T273_Texto', 'colunaTitulo' => 'Texto', 'style' => 'width: 1%;max-width:200px;white-space:normal;'),
            array('colunaValor' => 'T273_Link_Anexo', 'colunaTitulo' => 'Anexo', 'style' => 'width: 1%', 'callback' => 'retornaImagemUrl', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T273_D167_Antes', 'colunaTitulo' => 'Lista antes', 'style' => 'width: 1%', 'callback' => 'retornaNomeListaAntes', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T273_D167_Depois', 'colunaTitulo' => 'Lista depois', 'style' => 'width: 1%', 'callback' => 'retornaNomeListaDepois', 'colunaAlinhar' => 'center'),
        );

        /**
         * Definição da SQL
         */
        $from = "T273";

        $extra = <<<EOT
			LEFT JOIN C007 ON C007_Id=T273_C007_Id
			WHERE T273_T234_Id = '{$r_acaoId}'
			ORDER BY T273_Data_Interacao DESC
EOT;
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        //echo $g['ui']->gridFiltroPrint($gridFiltro);
        /**
         * Geração: Monta o SQL e retorna o dados
         */

        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'alterarStatusDesenvolvimento':
        $T234_Id = $r_T234_Id;
        $flagStatus = $r_flagStatus;
        mysql_query("UPDATE T234 SET T234_Flag_Status_Desenvolvimento = '{$flagStatus}' WHERE T234_Id = '{$T234_Id}'");
        $erro = mysql_error();

        if (empty($erro)) {
            $resposta['code'] = true;
        } else {
            $resposta['data'] = "Erro ao continuar suporte: " . $erro;
            $resposta['code'] = false;
        }

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'processarPrioridadeChamado':
        processarPrioridadeChamado($r_T234_Id, $r_novaOrdem);
        $resposta['code'] = true;
        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'processarPrioridadeCliente':
        processarPrioridadeCliente($r_T234_Id, $r_D024_Id, $r_novaOrdem);
        $resposta['code'] = true;
        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'formTesteDesenvolvimento':
        /**
         * PARTE 1: Variavéis envidas para o formulário
         */
        // Variável indica qual tab do form será exibida
        $tab = (isset($_REQUEST['tab'])) ? $_REQUEST['tab'] : false;
        // Utilizada em UPDATE, é o ID do registro que está sendo editado
        $acaoId = (isset($r_acaoId)) ? $r_acaoId : false;

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
        $form_tabela = 'T234';
        $form_campo_pk = 'T234_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'formAnaliseDesenvolvimento_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/registroSuporteCliente/registroSuporteCliente/formTesteDesenvolvimento/';

        // novo formulário
        $form = new Formulario('formTesteDesenvolvimento');
        // Editor
        $form_editor = array(
            'geral' => '{"T234_Teste":{"top":7,"left":23,"height":430,"width":872,"hidden":0}}',
        );

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T234_Id' => array('name' => 'T234_Id', 'type' => 'hidden', 'value' => $r_acaoId),
                    'T234_Teste' => array('name' => 'T234_Teste', 'label' => 'Teste', 'type' => 'textarea'),
                ),
            ),
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
                // Insert ou Update?
                if (!empty($acaoId)) {
                    // Update
                    if ($sql = $g['sqlAuto']->gerarSQLUpdate($form_tabela, $form_campo_pk, $acaoId, $form)) {
                        // Imprime uma mensagem e fecha a janela
                        echo gGeraAlertMsg('Operação OK', 'Registro Alterado com Sucesso!');
                    }
                }
            }
        } else {
            // Carrega os valores caso seja um update
            if (!empty($acaoId)) {
                $processado = $form->carregarValoresCampos($form_tabela, $form_campo_pk, $acaoId);
                // mostra erros de formulário uteis ao desenvolvedor
                if ($processado !== true) {
                    reset($form->formArray['campos']);
                    if ($tab == key($form->formArray['campos'])) {
                        echo $processado;
                    }
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
        $g['smarty']->assign('table', $gerado['table']);
        $g['smarty']->assign('form_tabela', isset($form_tabela) ? $form_tabela : '');
        echo $gerado['pagina'];
        if (!$formMultiTab) {$g["smarty"]->assign("formId", $formId);}
        $g['smarty']->display('formEditor.tpl');

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '';
        $botoesDir .= '<button type="submit" id="formAnaliseDesenvolvimento_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
    break;

    case 'carregaContatosSuporte':
        $resposta = array('code' => true, 'data' => array());

        $T234_D024_Id = isset($r_T234_D024_Id) ? $r_T234_D024_Id : false;

        $resposta['data']['T234_D013_Id'] = gProcessaSelect(gGeraSelect('D013', 'D013_Id', 'D013_Nome_Contato,D013_E_Mail', 'WHERE D013_D024_Id=' . $T234_D024_Id . ' AND D013_Flag_Ativo = "S" ORDER BY D013_Nome_Contato', true));

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'gridSuporteArquivo':
        global $g, $confUsuario;

        $filtroObrigatorio = true;
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            //array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => 'prosseguir', 'colunaTitulo' => '', 'style' => 'width: 1%', 'callback' => 'continuarDesenvolvimento', 'callbackParameter' => '1', 'colunaTipo' => 'livre'),
            array('colunaValor' => "T234_Id", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => "CONCAT(T272_Mes, '/', T272_Ano)", 'colunaTitulo' => 'Pacote', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Nome técnico', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'C007_Id', 'colunaExibir' => false),
            array('colunaValor' => "interacoes", 'colunaTitulo' => 'Descrição', 'callback' => 'validaCampoMensagensSuporte', 'style' => 'padding: 0px;', 'colunaTipo' => 'livre'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
            array('colunaValor' => 'D166_Grupo_Suporte', 'colunaTitulo' => 'Quadro', 'colunaExibir' => false),
            array('colunaValor' => 'T234_D166_Id', 'colunaTitulo' => 'Quadro', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D166', 'D166_Id', 'D166_Grupo_Suporte'), 'callback' => 'T234_D166_Nome_Grupo', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D167_Nome_Lista', 'colunaTitulo' => 'Lista', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'F'),
            array('colunaValor' => 'T234_Flag_Arquivado', 'colunaTitulo' => 'A'),
            array('colunaValor' => 'T234_Avaliacao_Cliente', 'colunaTitulo' => 'Avaliação', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'callback' => 'retornaImagemAvaliacao'),
            array('colunaValor' => 'T234_C007_Id_Programador', 'colunaTitulo' => 'Membros'),
            array('colunaValor' => "CONCAT('P - ',T234_Horas_Desenvolvimento, '<br> D - ', T234_Horas_Trabalho, '<br> R - ',T234_Horas_ReTrabalho,'<br><br>A - ',T234_Total_Horas_Atendimento,'<br>P - ',T234_Horas_Pausado)", 'colunaTitulo' => 'Horas', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Chamado_Origem_Id', 'colunaTitulo' => 'Origem', 'colunaExibir' => false),
        );

        /**
         * Definição da SQL
         */
        $from = "T234 as T234_1";

        $extra = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join D167 on D167_Id=T234_D167_Id
			left join D166 on D166_Id=T234_D166_Id
			left join C007 on C007_Id=T234_C007_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on C025_D024_Id=D024_Id
			left join T272 on T272_Id=T234_T272_Id
			LEFT JOIN (SELECT * FROM T252 order by T252_Id DESC) T252 ON T234_Id = T252_T234_Id
			WHERE T234_Flag_Tipo_Suporte = 'S'
			  AND T234_Flag_Triagem = 'N'
			GROUP BY T234_Id
EOT;
        $extraTotal = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join D167 on D167_Id=T234_D167_Id
			left join D166 on D166_Id=T234_D166_Id
			left join C007 on C007_Id=T234_C007_Id
			left join D046 on D046_Id=D024_D046_Id
			left join T235 on T235_Id=T234_T235_Id
			left join C025 on D024_Id=C025_D024_Id
			left join T272 on T272_Id=T234_T272_Id
			LEFT JOIN (SELECT * FROM T252 order by T252_Id DESC limit 1) T252 ON T234_Id = T252_T234_Id
			WHERE T234_Flag_Tipo_Suporte = 'S'
			  AND T234_Flag_Triagem = 'N'
EOT;
        /**
         * Botões (Versão Acima do GRID)
         */
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="btnProcessarFeedback">Processar Feedback</button>
				<button type="button" id="btnFilaAtendimento">Fila Atendimento</button>
EOT;
        if (!empty($g['C031']['TrelloUrlApi'])) {
            echo <<<EOT
				<button type="button" id="btnSincronizarTrelloAtendimento">Sincronizar Atendimento</button>
				<button type="button" id="btnSincronizarTrello">Sincronizar Trello</button>
				<button type="button" id="btnSincronizarMelhorias">Sincronizar Melhorias</button>
				<button type="button" id="btnAtualizarPacote">Atualizar Pacote</button>
EOT;
        }

        echo <<<EOT
		</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#btnProcessarFeedback').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/processarFeedback/?ajax=true&T234_Id=' + encodeURIComponent('{$r_acaoId}')  + '&callback=?', function(request) {
								$('#{$g['divId']}').hideLoading();
								if (request.code) {
									dialogConfirm('Sucesso', request.data);
									divRefresh('{$g['divId']}', true);
								} else {
									dialogConfirm('Erro', request.data);
								}
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					}
					dialogConfirm('Processar Feedback', 'Processar feedback agora?', \$buttons);
				});
				$('#btnFilaAtendimento').unbind('click').bind('click', function(data) {
					abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Fila Atendimento', '/outros/registroSuporteCliente/registroSuporteCliente/contentFilaAtendimento/', '&acaoId=' + encodeURIComponent('{$T234_Id}'), [1200,600]);
				});
				$('#btnAtualizarPacote').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Atualizar Pacote', '/outros/registroSuporteCliente/registroSuporteCliente/contentAtualizarPacote/', '&tab=geral', [320,220]);
				});
				$('#btnSincronizarTrello').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/sincronizarTrello/?ajax=true&organizations=' + encodeURIComponent('{$g['C031']['TrelloNomeTimeAnalise']}') + '&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										dialogConfirm('Sincronizado', request.data);
										divRefresh('{$g['divId']}');
									} else {
										dialogConfirm('Erro', request.data);
										divRefresh('{$g['divId']}');
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Sincronizar dados', 'Os dados dos chamados serão sincronizados, continuar?', \$buttons);
					});

					$('#btnSincronizarTrelloAtendimento').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/sincronizarTrello/?ajax=true&organizations=' + encodeURIComponent('{$g['C031']['TrelloNomeTimeAnalise']}') + '&filaAtendimento=true&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										dialogConfirm('Sincronizado', request.data);
										divRefresh('{$g['divId']}');
									} else {
										dialogConfirm('Erro', request.data);
										divRefresh('{$g['divId']}');
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Sincronizar dados', 'Os dados dos chamados serão sincronizados, continuar?', \$buttons);
					});

					$('#btnSincronizarMelhorias').unbind('click').bind('click', function(data) {
						var \$buttons = {
							"Sim": function() {
								$('#{$g['divId']}').showLoading();
								$.getJSON('/outros/registroSuporteCliente/registroSuporteCliente/sincronizarTrello/?ajax=true&organizations=' + encodeURIComponent('{$g['C031']['TrelloNomeTimeDesenvolvimento']}') + '&T234_Id=' + encodeURIComponent('{$r_acaoId}') + '&callback=?', function(request) {
									$('#{$g['divId']}').hideLoading();
									if (request.code) {
										dialogConfirm('Sincronizado', request.data);
										divRefresh('{$g['divId']}');
									} else {
										dialogConfirm('Erro', request.data);
										divRefresh('{$g['divId']}');
									}
								});
								$(this).dialog("destroy").remove();
							},
							"Não": function() { $(this).dialog("destroy").remove(); }
						};
						dialogConfirm('Sincronizar dados', 'Os dados dos chamados serão sincronizados, continuar?', \$buttons);
					});

			} </script></div>
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'T234_Data_Hora_Finalizado' => array('titulo' => 'Finalizado', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T252_Descricao' => array('titulo' => 'Interação', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                    array('title' => 'Todos', 'value' => ''),
                    array('title' => 'PENDENTE', 'value' => 'N'),
                    array('title' => 'FINALIZADO', 'value' => 'S'),
                    array('title' => 'CONCLUÍDO', 'value' => 'O'),
                    array('title' => 'ATENDIMENTO', 'value' => 'A'),
                )
            ),
            'T234_Flag_Arquivado' => array('titulo' => 'Arquivado (S/N)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'T272_Mes' => array('titulo' => 'Mês Pacote', 'tipo' => 'numero'),
            'T272_Ano' => array('titulo' => 'Ano Pacote', 'tipo' => 'numero'),
            'D166_Grupo_Suporte' => array('titulo' => 'Quadro', 'tipo' => 'texto'),
            'D167_Nome_Lista' => array('titulo' => 'Lista', 'tipo' => 'texto'),
            'T234_Avaliacao_Cliente_Data' => array('titulo' => 'Data avaliação', 'tipo' => 'data'),
            'T234_Avaliacao_Cliente' => array('titulo' => 'Avaliação cliente', 'tipo' => 'numero'),
            'T234_C007_Id_Programador' => array('titulo' => 'Membros', 'tipo' => 'texto'),
            'T252_Data_Hora' => array('titulo' => 'Data interação', 'tipo' => 'data'),
            'T252_Usuario' => array('titulo' => 'Usuário interação', 'tipo' => 'texto'),
            'T234_IdCard' => array('titulo' => 'Id Card', 'tipo' => 'texto'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, null, null, true);
        $extraTotal = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extraTotal, null, null, true);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        //$totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true);

        $sqlTotal = $g['sqlAuto']->pegarSqlCampos("COUNT(*),
												   time_format( SEC_TO_TIME( SUM( TIME_TO_SEC( T234_Horas_Desenvolvimento ) ) ),'%H:%i:%s'),
			        		                       time_format( SEC_TO_TIME( SUM( TIME_TO_SEC( T234_Horas_Trabalho ) ) ),'%H:%i:%s'),
		        		                           time_format( SEC_TO_TIME( SUM( TIME_TO_SEC( T234_Horas_ReTrabalho ) ) ),'%H:%i:%s'),
		        		                           sum(if(T234_Avaliacao_Cliente=1,1,0)),
		        		                           sum(if(T234_Avaliacao_Cliente=2,1,0)),
		        		                           sum(if(T234_Avaliacao_Cliente=3,1,0)),
		        		                           sum(if(T234_Avaliacao_Cliente=4,1,0)),
		        		                           sum(if(T234_Avaliacao_Cliente=5,1,0))",
            $from,
            $extraTotal);

        list($totalRegistros,
            $totalHorasPrazos,
            $totalHorasTrabalhadas,
            $totalHorasRetrabalhadas,
            $avaliacaoMuitoRuim,
            $avaliacaoRuim,
            $avaliacaoRegular,
            $avaliacaoBom,
            $avaliacaoMuitoBom) = $sqlTotal;

        $urlImagem1 = "{$confUsuario["urlRaiz"]}hardness3/static/img/avaliacao1.png";
        $urlImagem2 = "{$confUsuario["urlRaiz"]}hardness3/static/img/avaliacao2.png";
        $urlImagem3 = "{$confUsuario["urlRaiz"]}hardness3/static/img/avaliacao3.png";
        $urlImagem4 = "{$confUsuario["urlRaiz"]}hardness3/static/img/avaliacao4.png";
        $urlImagem5 = "{$confUsuario["urlRaiz"]}hardness3/static/img/avaliacao5.png";

        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="8" readonly></span>
				<span class="item" style="text-align:right">Prazo<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalHorasPrazos}" size="8" readonly></span>
				<span class="item" style="text-align:right">Desenvolvimento<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalHorasTrabalhadas}" size="10" readonly></span>
				<span class="item" style="text-align:right">Retrabalho<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalHorasRetrabalhadas}" size="8" readonly></span>
				<span class="item" style="text-align:right">&nbsp;</span>
				<span class="item" style="text-align:right"><img src='$urlImagem1' width='30px'></span>
				<span class="item" style="text-align:right"><br><input type="text" class="whiteBg" id="inputRecebido" value="{$avaliacaoMuitoRuim}" size="1" readonly></span>
				<span class="item" style="text-align:right">&nbsp;</span>
				<span class="item" style="text-align:right"><img src='$urlImagem2' width='30px'></span>
				<span class="item" style="text-align:right"><br><input type="text" class="whiteBg" id="inputRecebido" value="{$avaliacaoRuim}" size="1" readonly></span>
				<span class="item" style="text-align:right">&nbsp;</span>
				<span class="item" style="text-align:right"><img src='$urlImagem3' width='30px'></span>
				<span class="item" style="text-align:right"><br><input type="text" class="whiteBg" id="inputRecebido" value="{$avaliacaoRegular}" size="1" readonly></span>
				<span class="item" style="text-align:right">&nbsp;</span>
				<span class="item" style="text-align:right"><img src='$urlImagem4' width='30px'></span>
				<span class="item" style="text-align:right"><br><input type="text" class="whiteBg" id="inputRecebido" value="{$avaliacaoBom}" size="1" readonly></span>
				<span class="item" style="text-align:right">&nbsp;</span>
				<span class="item" style="text-align:right"><img src='$urlImagem5' width='30px'></span>
				<span class="item" style="text-align:right"><br><input type="text" class="whiteBg" id="inputRecebido" value="{$avaliacaoMuitoBom}" size="1" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
					var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
				});
			} </script></div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuporteRetrabalho':
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T234_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            //array('colunaValor' => 'T234_Ordem_Desenvolvimento', 'colunaTitulo' => '', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'style' => 'width: 1%'),
            array('colunaValor' => "LPAD(T234_Id, 8, '0') as Numero", 'colunaTitulo' => 'Número', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => "CONCAT(T272_Mes, '/', T272_Ano)", 'colunaTitulo' => 'Pacote', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'colunaExibir' => true),
            array('colunaValor' => 'T234_Flag_Tipo_OS', 'colunaTitulo' => 'Tipo', 'style' => 'width: 1%', 'callback' => 'statusOS', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'T234_Data_Hora_Abertura', 'colunaTitulo' => 'Abertura', 'callback' => 'gCorrigeData', 'style' => 'min-width:50px; white-space:normal; word-wrap:break-word; text-align: center;'),
            array('colunaValor' => 'T234_Tempo_Estimado', 'colunaTitulo' => 'Tempo', 'colunaAlinhar' => 'center', 'colunaEditar' => true, 'callback' => 'gCorrigeTempo'),
            array('colunaValor' => 'T234_Solicitante', 'colunaTitulo' => 'Solicitante', 'style' => 'width: 1%'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente', 'style' => 'min-width:250px; white-space:normal; word-wrap:break-word;'),
            array('colunaValor' => 'T234_C007_Id', 'colunaTitulo' => 'Responsável', 'colunaEditar' => true, 'colunaEditarGeraSelect' => array('C007', 'C007_Id', 'C007_Primeiro_Nome', 'ORDER BY C007_Primeiro_Nome', true), 'style' => 'width: 1%', 'callback' => 'campoMostraValor', 'callbackParameter' => 'C007_Primeiro_Nome'),
            array('colunaValor' => 'C007_Primeiro_Nome', 'colunaTitulo' => 'Nome técnico', 'colunaExibir' => false),
            array('colunaValor' => 'C007_Id', 'colunaTitulo' => 'C007_Id', 'colunaExibir' => false),
            array('colunaValor' => "T234_Descricao", 'colunaTitulo' => 'Descrição', 'style' => 'padding: 0px;'),
            array('colunaValor' => 'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0, T252_Data_Hora,T234_Data_Hora_Abertura)', 'colunaTitulo' => 'Última interação', 'callback' => 'gCorrigeData', 'style' => 'width: 1%'),
            array('colunaValor' => 'D166_Grupo_Suporte', 'colunaTitulo' => 'Quadro', 'colunaExibir' => false),
            array('colunaValor' => 'D167_Nome_Lista', 'colunaTitulo' => 'Lista', 'colunaExibir' => false),
            array('colunaValor' => 'T234_Flag_Finalizado', 'colunaTitulo' => 'F'),
            array('colunaValor' => 'T234_Flag_Arquivado', 'colunaTitulo' => 'A'),
            array('colunaValor' => 'T234_Avaliacao_Cliente', 'colunaTitulo' => 'Avaliação', 'style' => 'width: 1%', 'colunaAlinhar' => 'center', 'callback' => 'retornaImagemAvaliacao'),
        );

        /**
         * Definição da SQL
         */
        $from = "T234";

        $extra = <<<EOT
			left join D024 on D024_Id=T234_D024_Id
			left join D167 on D167_Id=T234_D167_Id
			left join D166 on D166_Id=T234_D166_Id
			left join C007 on C007_Id=T234_C007_Id
			left join C025 on C025_D024_Id=D024_Id
			left join T272 on T272_Id=T234_T272_Id
			LEFT JOIN (SELECT * FROM T252 order by T252_Data_Hora DESC) T252 ON T234_Id = T252_T234_Id
			WHERE T234_Flag_Tipo_Suporte = 'S'
			AND T234_Chamado_Origem_Id={$r_acaoId}
			GROUP BY T234_Id
			ORDER BY T234_Id desc
EOT;

        /**
         * Filtro
         */
        $gridFiltro = array(
            'T234_Data_Hora_Abertura' => array('titulo' => 'Abertura', 'tipo' => 'data'),
            'C007_Nome' => array('titulo' => 'Usuário', 'tipo' => 'texto'),
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            'T252_Descricao' => array('titulo' => 'Interação', 'tipo' => 'texto'),
            'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            'T234_Flag_Tipo_OS' => array('titulo' => 'Tipo', 'tipo' => 'texto'),
            'T234_Id' => array('titulo' => 'Número', 'tipo' => 'texto'),
            'T234_Flag_Finalizado' => array('titulo' => 'Status', 'tipo' => 'select', 'select' => array(
                    array('title' => 'Todos', 'value' => ''),
                    array('title' => 'PENDENTE', 'value' => 'N'),
                    array('title' => 'CONCLUÍDO', 'value' => 'O'),
                    array('title' => 'ATENDIMENTO', 'value' => 'A'),
                )
            ),
            'T234_Flag_Arquivado' => array('titulo' => 'Arquivado (S/N)', 'tipo' => 'texto'),
            'T234_Ordem_Desenvolvimento' => array('titulo' => 'Ordem', 'tipo' => 'texto'),
            'IF((SELECT COUNT(*) FROM T252 WHERE T252_T234_Id = T234_Id) > 0[[virgula]]T252_Data_Hora[[virgula]]T234_Data_Hora_Abertura)' => array('titulo' => 'Últ. Interação', 'tipo' => 'data'),
            'T272_Mes' => array('titulo' => 'Mês Pacote', 'tipo' => 'numero'),
            'T272_Ano' => array('titulo' => 'Ano Pacote', 'tipo' => 'numero'),
            'D166_Grupo_Suporte' => array('titulo' => 'Quadro', 'tipo' => 'texto'),
            'D167_Nome_Lista' => array('titulo' => 'Lista', 'tipo' => 'texto'),
            'T234_Avaliacao_Cliente_Data' => array('titulo' => 'Data avaliação', 'tipo' => 'data'),
            'T234_Avaliacao_Cliente' => array('titulo' => 'Avaliação cliente', 'tipo' => 'numero'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra, true, false, true);
        $valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<div id="fin001ExtraBtns" style="clear:both;"></div>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
//         $novoIdTelaForm = uniqid();
        //         echo <<<EOT
        //             <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        //                 $('#{$g['divId']} .grid tbody tr').unbind('click').unbind('click').bind('click', function(data) {
        //                     if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && data.target.nodeName != 'DIV' && !$(event.target).hasClass('editar-linha')) { return true; }
        //                     var id = $(this).closest('tr').attr('id').replace(/T234_Id-/, '');
        //                     abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Chamado', '/outros/registroSuporteCliente/registroSuporteCliente/contentInteracoes/', '&acaoId=' + encodeURIComponent(id) + '&habilitarInclusao=true&acessoSuporte=true&tabela={$from}', [990, 600]);
        //                 });
        //             } </script></div>
        // EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'gridSuporteHorasTrabalhadas':

        $T234_Id = isset($r_acaoId) ? $r_acaoId : false;

        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => 'T274_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => "T274_Data", 'colunaTitulo' => 'Data', 'callback' => 'gCorrigeData', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => "T274_Hora_Inicio", 'colunaTitulo' => 'Inicio', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => "T274_Hora_Final", 'colunaTitulo' => 'Inicio', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => "T274_Total_Horas_Trabalhadas", 'colunaTitulo' => 'Total', 'style' => 'width: 1%', 'colunaAlinhar' => 'center'),
        );

        /**
         * Definição da SQL
         */
        $from = "T274";

        $extra = <<<EOT
			LEFT JOIN T234 ON T234_Id=T274_T234_Id
			WHERE T274_T234_Id={$T234_Id}
			ORDER BY T274_Data
EOT;
        /**
         * Botões (Versão Acima do GRID)
         */
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="HorasTrabalhadasSuporteBtnIncluir">Incluir</button>

			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#HorasTrabalhadasSuporteBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(false, divIdRootAberto(), '', menuId('email'), '', 'Incluir Horas', '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirHorasTrabalhadas/', '&acaoId=' + encodeURIComponent('{$T274_Id}') + '&T234_Id=' + encodeURIComponent('{$T234_Id}'), [300,200]);
				});
			} </script></div>
EOT;

        /**
         * Filtro
         */
        $gridFiltro = array(
            'T274_Data' => array('titulo' => 'Data', 'tipo' => 'data'),
        );

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        $totalRegistros = $g['sqlAuto']->pegarSqlCampo("COUNT(*)", $from, $extra);

        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
			</div>
EOT;

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				var dbclickPrevent = false;
				$('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
					// Previne duplo click (abrir duas janelas)
					if (dbclickPrevent) { return false; }
					dbclickPrevent = true;
					setTimeout(function() { dbclickPrevent = false; }, 500);
					// (substituir pelo mesmo ID da chave primária do grid)
					var chavePrimaria = 'T274_Id';
					if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
					var id = $(this).closest('tr').attr('id').split('|');
					var T274_Id = id[0].replace(chavePrimaria + '-', '');
					// Ação: abrir nova janela
	        		abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Horas - ID: ' + T274_Id, '/outros/registroSuporteCliente/registroSuporteCliente/contentIncluirHorasTrabalhadas/', '&acaoId=' + encodeURIComponent(T274_Id) + '&T234_Id=' + encodeURIComponent('{$T234_Id}') + '&tabela={$from}', [300,200]);
		        });
				$('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
			} </script></div>
EOT;

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Botões (Versão Acima do GRID)
         *Quando na tela nao houver botão de total ele irá manter um espaço entre os filtros e o Grid
         */
        echo <<<EOT
            <div class="buttonsBottomTop">
                <span class="item" style="text-align:right;margin-top:7px"><br/><br/></span>
                <div style="clear:both;"></div>
            </div>
EOT;

        /**
         * Geração: Grid
         */
        $gridTabela = $g['ui']->grid($grid, $geradoDados);
        $g['smarty']->assign('thead', $gridTabela['thead']);
        $g['smarty']->assign('tbody', $gridTabela['tbody']);
        $g['smarty']->assign('tfoot', $gridTabela['tfoot']);
        $g['smarty']->assign('gridId', md5($g['r_']));
        $g['smarty']->assign('gridPersonalizacao', json_encode(array($g['r_'], $g['divIdRoot'], $g['divId']), JSON_FORCE_OBJECT));
        gBotaoAuditoria($from);
        if (isset($r_generateHTML)) {ob_end_clean();
            ob_start();
            $g['smarty']->display('gridHTML.tpl');} else { $g['smarty']->display('grid.tpl');}
    break;

    case 'encaminharChamadoHardness':
        global $sys;
        $resposta = array('code' => true, 'data' => array());
        $erro = "";

        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $T234 = mysql_query("SELECT * FROM T234 WHERE T234_Id='{$T234_Id}'");
        $mT234 = mysql_fetch_assoc($T234);
        $T252 = mysql_query("SELECT T252.* FROM T252 LEFT JOIN T234 ON T234_Id=T252_T234_Id LEFT JOIN D024 ON D024_Id=T234_D024_Id WHERE T252_T234_Id ='{$T234_Id}'");
        $T253 = mysql_query("SELECT T253.* FROM T253 WHERE T253_T234_Id ='{$T234_Id}'");

        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
        $mC007 = mysql_fetch_array($C007);
        $Nome_Usuario = $mC007['C007_Primeiro_Nome'];

        $D024 = mysql_query("SELECT SUBSTRING(D024_Nome_Fantasia,1,25) as D024_Nome_Fantasia,D024_Link from D024 WHERE D024_Id='{$mT234['T234_D024_Id']}'");
        $mD024 = mysql_fetch_assoc($D024);

        if (strpos($_SERVER['HTTP_HOST'], 'premiersistemas') !== false) {
            $D024_Id = '601';
            $parceiro = "Premier Sistemas";
        } else if (strpos($_SERVER['HTTP_HOST'], 'cha') !== false) {
            $D024_Id = '599';
            $parceiro = "C.H.A Sistemas";
        }

        //Uso em teste
        // $D024_Id='599';
        // $parceiro = "C.H.A Sistemas";
        // ini_set('display_errors', 1);
        // error_reporting(E_ALL);
        // -------------------------------------------------
        // Conectando com a base e001 da hardness
        // -------------------------------------------------
        //Uso em produção
        $hostHardness = '23.23.29.24';
        $usuarioHardness = 'root';
        $senhaHardness = '=!H2r4n6s8!=';

        //Uso em teste
        // $hostHardness = '192.168.0.111'; $usuarioHardness = 'root'; $senhaHardness = 'root';

        //Conexão parceiro
        $hostParceiro = $sys['dbHost'];
        $usuarioParceiro = $sys['dbUser'];
        $senhaParceiro = $sys['dbPass'];

        //Conexao hardness
        $conHardness = @mysqli_connect($hostHardness, $usuarioHardness, $senhaHardness);
        $errorHardness = mysqli_connect_error();

        //Conexao Parceiro
        $conParceiro = @mysqli_connect($hostParceiro, $usuarioParceiro, $senhaParceiro);
        $errorParceiro = mysqli_connect_error();

        if (!empty($errorHardness) || !empty($errorParceiro)) {
            die('Não foi possível conectar banco hardness: ' . $errorHardness . "  ou banco Parceiro:" . $errorParceiro);
        } else {
            mysql_set_charset('utf8', $conHardness);
            // $dbsHardness = mysql_select_db('e001', $conHardness) or die(mysql_error());
            mysql_set_charset('utf8', $conParceiro);
            // $dbsParceiro = mysql_select_db($sys['dbDatabase'], $conParceiro) or die(mysql_error());
        }

        if (!empty($mT234['T234_T234_Vinculo_Id'])) {
            $resposta['code'] = false;
            $resposta['data'] = "Esse chamado já foi encaminhado para Hardness.<br><br>Número chamado Hardness:" . $mT234['T234_T234_Vinculo_Id'];
        } else {
            // -------------------------------------------------
            // Preparando o insert na T234
            // -------------------------------------------------
            $campos = "";
            $valores = "";
            foreach ($mT234 as $key => $value) {
                if ($key == 'T234_Id') {
                    continue;
                } else if ($key == 'T234_D024_Id') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($D024_Id,$conHardness) . "',";
                } else if ($key == 'T234_Solicitante') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($Nome_Usuario,$conHardness) . "',";
                } else if ($key == 'T234_D024_Parceiro_Nome') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string("Cliente Parceiro: " . $mD024['D024_Nome_Fantasia'],$conHardness) . "',";
                } else if ($key == 'T234_T234_Vinculo_Id') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($mT234['T234_Id'],$conHardness) . "',";
                } else if ($key == 'T234_D024_Parceiro_Link') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($mD024['D024_Link'],$conHardness) . "',";
                } else if ($key == 'T234_Info_Parceiro') {

                    $descricaoPrimeiraInteracao = "Chamado aberto por: <b>{$parceiro}</b></br> Cliente parceiro: <b>{$mD024['D024_Nome_Fantasia']} </b></br> Link acesso ao cliente do parceiro:<b<{$mD024['D024_Link']}</br></br> Chamado original parceiro:<b> {$mT234['T234_Id']}</b></br> Data abertura chamado original parceiro: <b>" . gCorrigeData($mT234['T234_Data_Hora_Abertura']) . "</b>";
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($descricaoPrimeiraInteracao,$conHardness) . "',";
                } else if ($key == 'T234_Dominio_Parceiro') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($sys['dominio'],$conHardness) . "',";
                } else if ($key == 'T234_T234_Data_Abertura_Parceiro') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($mT234['T234_Data_Hora_Abertura'],$conHardness) . "',";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value,$conHardness) . "',";
                }
            }

            $campos = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT234 = "INSERT INTO T234 ({$campos}) VALUES ({$valores})";
            log("insert feito:INSERT INTO T234 ({$campos}) VALUES ({$valores})");
            $resT234 = mysqli_query($insT234, $conHardness);
            if (!$resT234) {
                $erro = mysql_error($conHardness);
            } else {
                $T234_Id_Novo = mysql_insert_id($conHardness);
                mysqli_query("UPDATE T234 SET T234_T234_Vinculo_Id='{$T234_Id_Novo}' WHERE T234_Id='{$T234_Id}'", $conParceiro);
                log("Update T252:UPDATE T234 SET T234_T234_Vinculo_Id='{$T234_Id_Novo}' WHERE T234_Id='{$T234_Id}'");
            }

            // -------------------------------------------------
            // Preparando o Insert na T252
            // -------------------------------------------------

            while ($mT252 = mysql_fetch_assoc($T252)) {
                $campos = "";
                $valores = "";
                foreach ($mT252 as $key => $value) {

                    if ($key == 'T252_Id') {
                        continue;
                    } else if ($key == 'T252_Usuario') {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($Nome_Usuario,$conHardness) . "',";
                    } else if ($key == 'T252_T234_Id') {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($T234_Id_Novo,$conHardness) . "',";
                    } else if ($key == 'T252_T252_Vinculo_Id') {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($mT252['T252_Id'],$conHardness) . "',";
                    } else {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($value,$conHardness) . "',";
                    }
                    // log("Campo T252:".$campos."             Valor:".$valores);
                }

                $campos = trim($campos, ', ');
                $valores = trim($valores, ',');
                $insT252 = "INSERT INTO T252 ({$campos}) VALUES ({$valores})";

                $resT252 = mysqli_query($insT252, $conHardness);
                if (!$resT252) {
                    $erro = mysql_error($conHardness);
                } else {
                    $T252_Id_Novo = mysql_insert_id($conHardness);
                    mysqli_query("UPDATE T252 SET T252_T252_Vinculo_Id='{$T252_Id_Novo}' WHERE T252_Id='{$mT252['T252_Id']}'", $conParceiro);
                    log("Update T252:UPDATE T252 SET T252_T252_Vinculo_Id='{$T252_Id_Novo}' WHERE T252_Id='{$mT252['T252_Id']}'");
                }
            }

            // -------------------------------------------------
            // Preparando o Insert na T253
            // -------------------------------------------------

            while ($mT253 = mysql_fetch_assoc($T253)) {

                $campos = "";
                $valores = "";
                foreach ($mT253 as $key => $value) {
                    if ($key == 'T253_Id') {
                        continue;
                    } else if ($key == 'T253_T234_Id') {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($T234_Id_Novo,$conHardness) . "',";
                    } else if ($key == 'T253_T253_Vinculo_Id') {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($mT253['T253_Id'],$conHardness) . "',";
                    } else {
                        $campos .= $key . ", ";
                        $valores .= "'" . mysql_real_escape_string($value,$conHardness) . "',";
                    }
                }

                $campos = trim($campos, ', ');
                $valores = trim($valores, ',');
                $insT253 = "INSERT INTO T253 ({$campos}) VALUES ({$valores})";
                log("Insert T253:INSERT INTO T253 ({$campos}) VALUES ({$valores})");

                $resT253 = mysqli_query($insT253, $conHardness);
                if (!$resT253) {
                    $erro = mysql_error($conHardness);
                } else {
                    $T253_Id_Novo = mysql_insert_id($conHardness);
                    mysqli_query("UPDATE T253 SET T253_T253_Vinculo_Id='{$T253_Id_Novo}' WHERE T253_Id='{$mT253['T253_Id']}'", $conParceiro);
                    log("Update T252:UPDATE T253 SET T253_T253_Vinculo_Id='{$T253_Id_Novo}' WHERE T253_Id='{$mT253['T253_Id']}'");
                }
            }

            mysqli_close($conHardness);
            mysqli_close($conParceiro);
            log("erro final:" . $erro);
            if (empty($erro)) {
                $resposta['code'] = true;
            } else {
                // excluir
                $resposta['code'] = false;
                $resposta['data'] = $erro;
            }
        }

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'enviarChamadoHardness':

        $resposta = array('code' => true, 'data' => array());
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;

        $retorno = enviarChamadoClienteHardness($T234_Id);

        if ($retorno===true) {
            //Após o chamado ser transferido para o banco da hardness será finalizado no banco do cliente
            $sqlT234 = mysql_query("SELECT T234_Id,T234_IdCard FROM T234 WHERE T234_Id='{$T234_Id}'");
            $T234 = mysql_fetch_assoc($sqlT234);
            
            finalizarChamadoAbandonado($T234_Id, false, $g['usuarioAtual'],$g['C007']['C007_Primeiro_Nome'],false);
            
            //Caso o chamado no banco do cliente tiver vinculo com trello irá enviar a ultima interação (chamado transferido) e arquivar o card
            if(!empty($T234['T234_IdCard'])){
                require_once 'bibliotecas/webservices/trello/apiTrello.php';
                $Trello = new Trello();

                $Trello->atualizarDetalhesChamadoTrello($T234_Id);
                
                $Trello->arquivarCard($T234_Id, true);
            }

            $resposta['code'] = true;
        } else {
            $resposta['code'] = false;
            $resposta['data'] = $retorno;
        }

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'encaminharChamadoTrello':

        $resposta = array('code' => true, 'data' => array());
        require_once 'bibliotecas/webservices/trello/apiTrello.php';
        $Trello = new Trello();

        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;

        //Verifica os pedidos
        $retorno = $Trello->envioDeCard($T234_Id);

        $resposta['code'] = $retorno[0];
        $resposta['data'] = $retorno[1];

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'atualizarDetalhesChamadoTrello':

        $resposta = array('code' => true, 'data' => array());
        require_once 'bibliotecas/webservices/trello/apiTrello.php';
        $Trello = new Trello();

        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $retorno = array();

        $sqlT234 = mysql_query("SELECT D167_Flag_Enviar_Detalhes,D167_Nome_Lista,D166_Grupo_Suporte,T234_Flag_Arquivado From T234 Left join D167 on T234_D167_Id=D167_Id left join D166 on T234_D166_Id=D166_Id where T234_Id={$T234_Id}");
        $T234 = mysql_fetch_array($sqlT234);

        if ($T234['D167_Flag_Enviar_Detalhes'] == "N") {
            $retorno[0] = false;
            $retorno[1] = 'Os chamados da lista "' . $T234['D167_Nome_Lista'] . '" no quadro "' . $T234['D166_Grupo_Suporte'] . '" não tem permissão para enviar os detalhes para o trello.';
        } else if ($T234['T234_Flag_Arquivado'] == "S") {
            $retorno[0] = false;
            $retorno[1] = 'O chamado está arquivado.';
        } else {
            $retorno = $Trello->atualizarDetalhesChamadoTrello($T234_Id);
        }

        $resposta['code'] = $retorno[0];
        $resposta['data'] = $retorno[1];

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'VerificarQtdeMaximaAtendimento':

        $resposta = array('code' => true, 'data' => array());
        $D024_Id = isset($r_D024_Id) ? $r_D024_Id : false;
        $tipo = isset($r_tipo) ? $r_tipo : false;
        $mensagem = "";
        $quantidadeMaxima = 0;

        $clausula = "T234_Flag_Tipo_OS='{$tipo}'";
        if($tipo=="D"){
            $clausula="(". $clausula." OR T234_Flag_Tipo_OS='M')";
        }

		$sqlT135 = mysql_query("SELECT D024_Id,D024_Nome_Empresa,T135_Qtde_Maxima_Chamados_Atendimento,T135_Qtde_Maxima_Chamados_Duvida,T135_Qtde_Maxima_Melhorias_Mes
								FROM D024
								LEFT JOIN T135 ON D024_Id=T135_D024_Id
								WHERE D024_Id={$D024_Id}");
        $T135 = mysql_fetch_assoc($sqlT135);

        $sqlT234 = mysql_query("SELECT T234_Id,D166_Grupo_Suporte,D166_Flag_Atendimento
								FROM T234
								LEFT JOIN D166 ON T234_D166_Id=D166_Id
								WHERE T234_D024_Id={$D024_Id} 
                                AND {$clausula}
                                AND T234_Flag_Finalizado != 'S'");
        $chamadosAbertos = mysql_num_rows($sqlT234);
        
        if($tipo=="E"){
            $quantidadeMaxima=$T135['T135_Qtde_Maxima_Chamados_Atendimento'];
            $mensagem = "erro";
        }else if($tipo=="D"){
            $quantidadeMaxima=($T135['T135_Qtde_Maxima_Chamados_Duvida']+$T135['T135_Qtde_Maxima_Melhorias_Mes']);
            $mensagem = "dúvida/melhoria";
        }

        $chamadosFinalizar = $quantidadeMaxima - $chamadosAbertos;
        if($chamadosFinalizar==0){
            $chamadosFinalizar = 1;
        }else{
            $chamadosFinalizar = abs($chamadosFinalizar);
        }
        
        if ($quantidadeMaxima > $chamadosAbertos) {
            $code = true;
        } else {
			$code = false;
            $mensagem = "Olá!<br><br>Para agilizar o atendimento, limitamos o número de <b>{$quantidadeMaxima}</b> chamados de {$mensagem} em atendimento por vez.
            <br><br>Por favor aguarde a finalização de pelo menos <b>{$chamadosFinalizar}</b> dos chamados para abrir um novo.
            <br><br>Você tem a opção de finalizar chamados que já foram atendidos, através do botão no cabeçalho do chamado.
            <br><br><b>Você tem {$chamadosAbertos} chamados de {$mensagem} em atendimento.</b>";
        }

        $resposta['code'] = $code;
        $resposta['data'] = $mensagem;

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'avaliacaoCliente':

        $resposta = array('code' => true, 'data' => array());

        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $nota = isset($r_nota) ? $r_nota : false;

        if ($nota > 0 && !(empty($nota))) {
            $retorno = avaliacaoCliente($T234_Id, $nota);
        } else {
            $retorno[0] = false;
            $retorno[1] = "A nota da avaliação está inválida ou não foi enviada corretamente!.";
        }

        $resposta['code'] = $retorno[0];
        $resposta['data'] = $retorno[1];

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'enviarRespostaHardnessParceiro':

        $resposta = array('code' => true, 'data' => array());

        $retorno = encaminharChamadoParceiro($r_T252_Id, $r_T234_Id, $r_destinatario);

        $resposta['code'] = $retorno[0];
        $resposta['data'] = $retorno[1];

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

        case 'encaminharAnexosHardness':

        $resposta = array('code' => true, 'data' => array());
        $retorno = encaminharAnexosParceiro($r_T234_Id, $r_destinatario);

        $resposta['code'] = $retorno[0];
        $resposta['data'] = $retorno[1];

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'finalizarChamadoCliente':
        global $g;
        $resposta = array('code' => true, 'data' => "");
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $retorno = true;
        $mensagem = "";

        $sql = mysql_query("SELECT T234_C007_Id_Cliente,T234_Flag_Finalizado,D167_Flag_Finalizar_Chamado,D167_Id 
                              FROM T234
                         LEFT JOIN D167 ON T234_D167_Id=D167_Id
                             WHERE T234_Id='{$T234_Id}'");
        $T234 = mysql_fetch_assoc($sql);

        if(!(($T234['T234_C007_Id_Cliente']==$g['usuarioAtual']) || ($g['C007']['C007_Flag_Vendedor']=='D'))){
            $retorno = false;
            $mensagem = "Seu usuário não tem permissão para finalizar esse chamado.";
        }
        
        if($T234['T234_Flag_Finalizado']=="A"){
            $retorno = false;
            $mensagem = "Esse chamado se encontra em atendimento.";
        }

        if($T234['T234_Flag_Finalizado']=="S"){
            $retorno = false;
            $mensagem = "Esse chamado já está finalizado.";
        }

        // if($T234['D167_Id']>0 && $T234['D167_Flag_Finalizar_Chamado']=="N"){
        //     $retorno = false;
        //     $mensagem = "Esse chamado não tem permissão para ser finalizado, contacte o suporte técnico.";
        // }

        if($retorno===true){
            finalizarChamadoAbandonado($T234_Id, false, $g['usuarioAtual'],$g['C007']['C007_Primeiro_Nome'],false);
            //Será arquivado o card no trello ao ser finalizado no sistema
            require_once 'bibliotecas/webservices/trello/apiTrello.php';
            $Trello = new Trello();
            $Trello->arquivarCard($T234_Id, true,63);
            $retorno = true;
        }

        $resposta['code'] = $retorno;
        $resposta['data'] = $mensagem;

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'atualizarPacoteMelhorias':

        $resposta = array('code' => true, 'data' => array());

        $D166_Id = isset($r_D166_Id) ? $r_D166_Id : false;

        $sqlT234 = mysql_query("SELECT T234_D167_Id,D167_Flag_Finalizar_Chamado FROM T234 LEFT JOIN D167 ON T234_D167_ID=D167_Id WHERE T234_D166_Id='{$D166_Id}' GROUP BY T234_D167_Id");
        $rT234 = mysql_num_rows($sqlT234);
        $T234 = mysql_fetch_array($sqlT234);

        log("T234:" . $T234['D167_Flag_Finalizar_Chamado']);

        if ($rT234 > 1) {
            $resposta['code'] = false;
            $resposta['data'] = "Existem chamados em mais de uma lista!";
        } else {

            if ($T234['D167_Flag_Finalizar_Chamado'] == "N") {
                $resposta['code'] = false;
                $resposta['data'] = "Os chamados estão em uma lista sem permissão para finalizar, verifique com a equipe de desenvolvimento.";
            } else {
                $retorno = atualizarPacoteMes($D166_Id);

                $resposta['code'] = $retorno[0];
                $resposta['data'] = $retorno[1];
            }
        }

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'sincronizarTrello':

        $resposta = array('code' => true, 'data' => array());
        require_once 'bibliotecas/webservices/trello/apiTrello.php';
        $Trello = new Trello();

        $filaAtendimento = isset($r_filaAtendimento) ? true : false;
        $T234_Id = isset($r_T234_Id) ? $r_T234_Id : false;
        $organizations = isset($r_organizations) ? $r_organizations : false;
        log("organizations:$organizations");

        $retorno = $Trello->sincronizarTrello($organizations, $filaAtendimento);

        $resposta['code'] = $retorno[0];
        $resposta['data'] = $retorno[1];

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;

    case 'AtualizarTrelloTriagem':

        $resposta = array('code' => true, 'data' => array());
        require_once 'bibliotecas/webservices/trello/apiTrello.php';
        $Trello = new Trello();

        $retorno = $Trello->AtualizarChamadosTrello();
        $resposta['data'] = $retorno;

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
    break;
}

function validaCampoFlagFinalizado($array)
{
    if ($array['todosCampos']['T234_Flag_Finalizado'] == 'S') {
        return "Sim";
    } else {
        return "Não";
    }
}
function validaCampoChamado($array)
{
    global $g;
    $T234_Id = $array['todosCampos']['T234_Id'];
    $T234 = mysql_query("SELECT T234_Id from T234 WHERE T234_T234_Id='{$T234_Id}'");
    if (mysql_num_rows($T234) > 0) {
        $mT234 = mysql_fetch_array($T234);
        return $mT234['T234_Id'];
    }
}


function validaCampoTempoAtendimento($array)
{
    $inicio = $array['todosCampos']['T234_Data_Hora_Abertura'];
    $fim = $array['todosCampos']['T234_Data_Hora_Finalizado'];
}

function enviarChamadoClienteHardness($T234_Id){
    global $g,$sys,$confUsuario;
    $erro = "";

    $T234 = mysql_query("SELECT * FROM T234 WHERE T234_Id='{$T234_Id}'");
    $mT234 = mysql_fetch_assoc($T234);
    // $T252 = mysql_query("SELECT T252.* FROM T252 LEFT JOIN T234 ON T234_Id=T252_T234_Id LEFT JOIN D024 ON D024_Id=T234_D024_Id WHERE T252_T234_Id ='{$T234_Id}'");
    $T253 = mysql_query("SELECT T253.* FROM T253 WHERE T253_T234_Id ='{$T234_Id}'");

    $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id='{$g['usuarioAtual']}'");
    $mC007 = mysql_fetch_array($C007);

    $D024_Id_Hardness = $g['C004']['C004_D024_Id_Hardness'];
    
    // -------------------------------------------------
    // Conectando com a base da  hardness
    // -------------------------------------------------
    //Variaveis de conexao hardness
    $hostHardness = $sys['dbHost'];
    $usuarioHardness = $sys['dbUser'];
    $senhaHardness = $sys['dbPass'];

    //Variaveis de conexao cliente
    $hostCliente = $confUsuario['dbHost'];
    $usuarioCliente = $confUsuario['dbUser'];
    $senhaCliente = $confUsuario['dbPass'];

    //Conexao Hardness
    $conHardness = mysqli_connect($hostHardness, $usuarioHardness, $senhaHardness,$sys['dbDatabase']);
    $errorHardness = mysqli_connect_error();

    //Conexao cliente
    $conCliente = mysqli_connect($hostCliente, $usuarioCliente, $senhaCliente,$confUsuario['dbDatabase']);
    $errorCliente = mysqli_connect_error();

    if (!empty($errorHardness) || !empty($errorCliente)) {
        return '(1) Não foi possível conectar banco hardness: ' . $errorHardness . "  ou banco cliente:" . $errorCliente.". Informe o suporte técnico.";
    } else {
        mysql_set_charset('utf8', $conHardness);
        // $dbsHardness = mysql_select_db($sys['dbDatabase'], $conHardness);
        // $erroDbHardness = mysql_error();
        mysql_set_charset('utf8', $conCliente);
        // $dbsCliente = mysql_select_db($confUsuario['dbDatabase'], $conCliente);
        // $erroDbCliente = mysql_error();
        // if (!empty($erroDbHardness) || !empty($erroDbCliente)) {
            // return '(2) Não foi possível conectar banco hardness: ' . $erroDbHardness . "  ou banco cliente:" . $erroDbCliente.". Informe o suporte técnico.";
        // }
    }

    if (!empty($mT234['T234_T234_Vinculo_Id'])) {
        $resposta['code'] = false;
        $resposta['data'] = "Esse chamado já foi encaminhado para Hardness.<br><br>Número chamado Hardness:" . $mT234['T234_T234_Vinculo_Id'];
    } else {
        // -------------------------------------------------
        // Preparando o insert na T234
        // -------------------------------------------------
        $campos = "";
        $valores = "";
        foreach ($mT234 as $key => $value) {
            if ($key == 'T234_Id') {
                continue;
            } else if ($key == 'T234_D024_Id') {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($D024_Id_Hardness,$conHardness) . "',";
            } else if ($key == 'T234_Flag_Triagem') {
                $campos .= $key . ", ";
                $valores .= "'S',";
            } else if ($key == 'T234_IdCard') {
                $campos .= $key . ", ";
                $valores .= "'',";
            } else if ($key == 'T234_D166_Id') {
                $campos .= $key . ", ";
                $valores .= "'',";
            } else if ($key == 'T234_D167_Id') {
                $campos .= $key . ", ";
                $valores .= "'',";
            } else if ($key == 'T234_Origem_Chamado') {
                $campos .= $key . ", ";
                $valores .= "'T',";
            } else if ($key == 'T234_Info_Parceiro') {
                $descricaoPrimeiraInteracao = "Chamado original cliente:<b> {$mT234['T234_Id']}</b></br> Data abertura chamado original cliente: <b>" . gCorrigeData($mT234['T234_Data_Hora_Abertura']) . "</b>";
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($descricaoPrimeiraInteracao,$conHardness) . "',";
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value,$conHardness) . "',";
            }
        }

        $campos = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT234 = "INSERT INTO T234 ({$campos}) VALUES ({$valores})";
        $resT234 = mysqli_query($insT234, $conHardness);
        if (!$resT234) {
            $erro = mysql_error($conHardness);
        } else {
            $T234_Id_Novo = mysql_insert_id($conHardness);
            mysqli_query("UPDATE T234 SET T234_T234_Vinculo_Id='{$T234_Id_Novo}' WHERE T234_Id='{$T234_Id}'", $conCliente);

            //Ao inserir cabeçalho do chamado na Hardness é inserido uma nova interação no chamado no banco do cliente  
            $C007_Primeiro_Nome = $mC007['C007_Primeiro_Nome'];
            $interacao = "Chamado transferido para o suporte técnico pelo usuário:".$C007_Primeiro_Nome;
            mysqli_query("INSERT INTO T252 (T252_T234_Id, T252_Descricao, T252_Data_Hora, T252_Usuario, T252_C007_Id, T252_Flag_Finalizacao, T252_C007_Id_Cliente,T252_Flag_Feedback) VALUES ('{$T234_Id}', '{$interacao}', CURRENT_TIMESTAMP(), '{$C007_Primeiro_Nome}', '0', 'S','{$g['usuarioAtual']}','N')",$conCliente);
        }

        // -------------------------------------------------
        // Preparando o Insert na T252
        // -------------------------------------------------

        $T252 = mysqli_query("SELECT T252.* FROM T252 LEFT JOIN T234 ON T234_Id=T252_T234_Id LEFT JOIN D024 ON D024_Id=T234_D024_Id WHERE T252_T234_Id ='{$T234_Id}'",$conCliente);
        while ($mT252 = mysql_fetch_assoc($T252)) {
            $campos = "";
            $valores = "";
            foreach ($mT252 as $key => $value) {
                if ($key == 'T252_Id') {
                    continue;
                } else if ($key == 'T252_T234_Id') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T234_Id_Novo,$conHardness) . "',";
                } else if ($key == 'T252_Detalhe_Trello_Id') {
                    $campos .= $key . ", ";
                    $valores .= "'',";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value,$conHardness) . "',";
                }
            }

            $campos = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT252 = "INSERT INTO T252 ({$campos}) VALUES ({$valores})";
            log($insT252);
            $resT252 = mysqli_query($insT252, $conHardness);
            if (!$resT252) {
                $erro = mysql_error($conHardness);
            } else {
                $T252_Id_Novo = mysql_insert_id($conHardness);
                mysqli_query("UPDATE T252 SET T252_T252_Vinculo_Id='{$T252_Id_Novo}' WHERE T252_Id='{$mT252['T252_Id']}'", $conCliente);
            }
        }

        // -------------------------------------------------
        // Preparando o Insert na T253
        // -------------------------------------------------

        while ($mT253 = mysql_fetch_assoc($T253)) {

            $campos = "";
            $valores = "";
            foreach ($mT253 as $key => $value) {
                if ($key == 'T253_Id') {
                    continue;
                } else if ($key == 'T253_T234_Id') {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T234_Id_Novo,$conHardness) . "',";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value,$conHardness) . "',";
                }
            }

            $campos = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT253 = "INSERT INTO T253 ({$campos}) VALUES ({$valores})";
            $resT253 = mysqli_query($insT253, $conHardness);
            if (!$resT253) {
                $erro = mysql_error($conHardness);
            } else {
                $T253_Id_Novo = mysql_insert_id($conHardness);
                mysqli_query("UPDATE T253 SET T253_T253_Vinculo_Id='{$T253_Id_Novo}' WHERE T253_Id='{$mT253['T253_Id']}'", $conCliente);
            }
        }

        mysqli_close($conHardness);
        // mysql_close($conCliente);
        
        if(!empty($erro)){
            return $erro;
        }
        return true;    
    }
}

function encaminharChamadoParceiro($T252_Id, $T234_Id, $conexao)
{
    global $confUsuario, $sys, $g;

    $retorno = array();

    $sqlC007 = mysql_query("SELECT C007_Primeiro_Nome from C007 where C007_Id='{$g['usuarioAtual']}'");
    $C007 = mysql_fetch_array($sqlC007);

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    if ($conexao == "C") {
        //Conexao do parceiro para a hardness ou seja enviando a resposta do cliente para hardness
        //Conexão Parceiro
        $hostEnviar = $sys['dbHost'];
        $usuarioEnviar = $sys['dbUser'];
        $senhaEnviar = $sys['dbPass'];

        //Conexão Hardness - Uso em Teste
        // $hostReceber     = '192.168.0.102';
        // $usuarioReceber = 'root';
        // $senhaReceber   = 'root';

        //Conexão Hardness - Uso em produção
        $hostReceber = '23.23.29.24';
        $usuarioReceber = 'root';
        $senhaReceber = '=!H2r4n6s8!=';
    } else if ($conexao == "H") {
        //Conexao da hardness para parceiro ou seja enviando a resposta da hardness para cliente
        //Conexão Hardness - Uso em Teste
        // $hostEnviar    = '192.168.0.102';
        // $usuarioEnviar = 'root';
        // $senhaEnviar   = 'root';

        //Conexão Parceiro
        $hostReceber = $sys['dbHost'];
        $usuarioReceber = $sys['dbUser'];
        $senhaReceber = $sys['dbPass'];

        //Conexão Hardness - Uso em produção
        $hostEnviar = '23.23.29.24';
        $usuarioEnviar = 'root';
        $senhaEnviar = '=!H2r4n6s8!=';
    }

    //Conexao com o banco que esta recebendo
    $conReceber = @mysqli_connect($hostReceber, $usuarioReceber, $senhaReceber,$sys['dbDatabase']);
    $errorReceber = mysqli_connect_error($conReceber);

    //Conexao com o banco que esta enviando
    $conEnviar = @mysqli_connect($hostEnviar, $usuarioEnviar, $senhaEnviar,'e001');
    $errorEnviar = mysqli_connect_error($conEnviar);

    if (!empty($errorReceber) || !empty($errorEnviar)) {
        $retorno[0] = false;
        $retorno[1] = "Erro de conexão com mysql.<br>Erro receber:" . $errorReceber . "<br>Erro enviar:" . $errorEnviar;

        return $retorno;
    } else {
        mysql_set_charset('utf8', $conReceber);
        // $dbsReceber = mysql_select_db($sys['dbDatabase'], $conReceber) or die(mysql_error());
        mysql_set_charset('utf8', $conEnviar);
        // $dbsEnviar = mysql_select_db('e001', $conEnviar) or die(mysql_error());
    }

    $sqlT234 = mysqli_query("SELECT T234_Id,T234_T234_Vinculo_Id,T234_Info_Parceiro,T234_Dominio_Parceiro FROM T234 WHERE T234_Id = '{$T234_Id}'", $conEnviar);
    $T234 = mysql_fetch_array($sqlT234);

    $sqlT252 = mysqli_query("SELECT * FROM T252 WHERE T252_Id = '{$T252_Id}'", $conEnviar);

    $date = date("Y-m-d H:i:s");

    while ($mT252 = mysql_fetch_assoc($sqlT252)) {
        $campos = "";
        $valores = "";
        foreach ($mT252 as $key => $value) {

            if ($key == 'T252_Id') {
                continue;
            } else if ($key == 'T252_Usuario') {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($C007['C007_Primeiro_Nome'],$conEnviar) . "',";
            } else if ($key == 'T252_T234_Id') {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($T234['T234_T234_Vinculo_Id'],$conEnviar) . "',";
            } else if ($key == 'T252_T252_Vinculo_Id') {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($T252_Id,$conEnviar) . "',";
            } else if ($key == 'T252_Flag_Lido') {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string("N",$conEnviar) . "',";
            } else if ($key == 'T252_Data_Hora') {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($date,$conEnviar) . "',";
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value,$conEnviar) . "',";
            }
        }

        $campos = trim($campos, ', ');
        $valores = trim($valores, ',');
        log("INSERT INTO T252 ({$campos}) VALUES ({$valores})");
        $insT252 = "INSERT INTO T252 ({$campos}) VALUES ({$valores})";

        $resT252 = mysqli_query($insT252, $conReceber);
        $T252_Id_Novo = mysql_insert_id($conReceber);
        if (!$resT252) {
            $erro = mysql_error($conEnviar);
            $retorno[0] = false;
            $retorno[1] = "Erro de insert:" . $erro;
        } else {
            log("id do ultimo insert:" . $T252_Id_Novo);
            log("UPDATE T252 set T252_T252_Vinculo_Id='{$T252_Id_Novo}' WHERE T252_Id='{$T252_Id}'");
            mysqli_query("UPDATE T252 set T252_T252_Vinculo_Id='{$T252_Id_Novo}' WHERE T252_Id='{$T252_Id}'", $conEnviar);

            $retorno[0] = true;
            $retorno[1] = "Resposta enviada com sucesso.";
        }
        mysqli_close($conReceber);
        mysqli_close($conEnviar);
    }
    return $retorno;
}

function encaminharAnexosParceiro($T234_Id, $conexao)
{
    global $confUsuario, $sys, $g;

    $retorno = array();

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    if ($conexao == "C") {
        //Conexao do parceiro para a hardness ou seja enviando os anexos do cliente para hardness
        //Conexão Parceiro
        $hostEnviar = $sys['dbHost'];
        $usuarioEnviar = $sys['dbUser'];
        $senhaEnviar = $sys['dbPass'];

        //Conexão Hardness - Uso em Teste
        // $hostReceber     = '192.168.0.111';
        // $usuarioReceber = 'root';
        // $senhaReceber   = 'root';

        //Conexão Hardness - Uso em produção
        $hostReceber = '23.23.29.24';
        $usuarioReceber = 'root';
        $senhaReceber = '=!H2r4n6s8!=';
    } else if ($conexao == "H") {
        //Conexao da hardness para parceiro ou seja enviando os anexos da hardness para cliente
        //Conexão Hardness - Uso em Teste
        // $hostEnviar    = '192.168.0.111';
        // $usuarioEnviar = 'root';
        // $senhaEnviar   = 'root';

        //Conexão Parceiro
        $hostReceber = $sys['dbHost'];
        $usuarioReceber = $sys['dbUser'];
        $senhaReceber = $sys['dbPass'];

        //Conexão Hardness - Uso em produção
        $hostEnviar = '23.23.29.24';
        $usuarioEnviar = 'root';
        $senhaEnviar = '=!H2r4n6s8!=';
    }

    //Conexao com o banco que esta recebendo
    $conReceber = @mysqli_connect($hostReceber, $usuarioReceber, $senhaReceber,$sys['dbDatabase']);
    $errorReceber = mysqli_connect_error($conReceber);

    //Conexao com o banco que esta enviando
    $conEnviar = @mysqli_connect($hostEnviar, $usuarioEnviar, $senhaEnviar,'e001');
    $errorEnviar = mysqli_connect_error($conEnviar);

    if (!empty($errorReceber) || !empty($errorEnviar)) {
        $retorno[0] = false;
        $retorno[1] = "Erro de conexão com mysql.<br>Erro receber:" . $errorReceber . "<br>Erro enviar:" . $errorEnviar;

        return $retorno;
    } else {
        mysql_set_charset('utf8', $conReceber);
        // $dbsReceber = mysql_select_db($sys['dbDatabase'], $conReceber) or die(mysql_error());
        mysql_set_charset('utf8', $conEnviar);
        // $dbsEnviar = mysql_select_db('e001', $conEnviar) or die(mysql_error());
    }

    $sqlT234 = mysqli_query("SELECT T234_Id,T234_T234_Vinculo_Id,T234_Info_Parceiro,T234_Dominio_Parceiro FROM T234 WHERE T234_Id = '{$T234_Id}'", $conEnviar);
    $T234 = mysql_fetch_array($sqlT234);

    $sqlT253 = mysqli_query("SELECT * FROM T253 WHERE T253_T253_Vinculo_Id IS NULL AND T253_T234_Id='{$T234_Id}'", $conEnviar);

    if (mysql_num_rows($sqlT253) <= 0) {
        $retorno[0] = false;
        $retorno[1] = "Não há novos anexos para enviar.";

        return $retorno;
    }

    $date = date("Y-m-d H:i:s");

    while ($mT253 = mysql_fetch_assoc($sqlT253)) {
        $campos = "";
        $valores = "";
        foreach ($mT253 as $key => $value) {

            if ($key == 'T253_Id') {
                continue;
            } else if ($key == 'T253_T234_Id') {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($T234['T234_T234_Vinculo_Id'],$conEnviar) . "',";
            } else if ($key == 'T253_T253_Vinculo_Id') {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($mT253['T253_Id'],$conEnviar) . "',";
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value,$conEnviar) . "',";
            }
        }

        $campos = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT253 = "INSERT INTO T253 ({$campos}) VALUES ({$valores})";
        log($insT253);

        $resT253 = mysqli_query($insT253, $conReceber);
        $T253_Id_Novo = mysql_insert_id($conReceber);
        if (!$resT253) {
            $erro = mysql_error();
            $retorno[0] = false;
            $retorno[1] = "Erro de insert:" . $erro;
        } else {
            log("id do ultimo insert:" . $T253_Id_Novo);
            log("UPDATE T253 SET T253_T253_Vinculo_Id='{$T253_Id_Novo}' WHERE T253_Id='{$mT253['T253_Id']}'");
            mysqli_query("UPDATE T253 SET T253_T253_Vinculo_Id='{$T253_Id_Novo}' WHERE T253_Id='{$mT253['T253_Id']}'", $conEnviar);

            $retorno[0] = true;
            $retorno[1] = "Anexos enviada com sucesso.";
        }
    }

    mysqli_close($conReceber);
    mysqli_close($conEnviar);
    return $retorno;
}

function avaliacaoCliente($T234_Id, $nota)
{
    global $g, $confUsuario;

    $retorno = array();
    $mensagem = array();

    $mensagem[1] = "Muito Ruim";
    $mensagem[2] = "Ruim";
    $mensagem[3] = "Regular";
    $mensagem[4] = "Bom";
    $mensagem[5] = "Muito Bom";

    $sqlT234 = mysql_query("SELECT T234_Avaliacao_Cliente,T234_C007_Id_Cliente,T234_Solicitante,T234_C007_Id,T234_C007_Id_Cliente FROM T234 WHERE T234_Id='{$T234_Id}'");
    $T234 = mysql_fetch_array($sqlT234);

    if ($T234['T234_C007_Id_Cliente'] != $g['usuarioAtual']) {
        $retorno[0] = false;
        $retorno[1] = "A avaliação só é permitida ao usuário que abriu o chamado.";
        return $retorno;
    }

    log("Chamado: " . $T234_Id . "     nota:" . $nota . "    Usuario:" . $C007['C007_Id'] . "avaliacao banco: " . $T234['T234_Avaliacao_Cliente']);

    if ($T234['T234_Avaliacao_Cliente'] == 0) {

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        mysql_query("UPDATE T234 SET T234_Avaliacao_Cliente='{$nota}', T234_Avaliacao_Cliente_Data=CURRENT_TIMESTAMP() WHERE T234_Id='{$T234_Id}'");
        $erroUpdate = mysql_error();

        if (!empty($erroUpdate)) {
            $retorno[0] = false;
            $retorno[1] = "ERRO MYSQL: " . $erroUpdate;
            mysql_query('ROLLBACK');
            return $retorno;
        }

        mysql_query("INSERT INTO T252 (T252_T234_Id, T252_Descricao, T252_Data_Hora, T252_Usuario, T252_C007_Id, T252_Flag_Finalizacao, T252_C007_Id_Cliente,T252_Flag_Lido) VALUES ('{$T234_Id}', '{$T234['T234_Solicitante']} avaliou o atendimento como: <b>{$mensagem[$nota]}</b>', CURRENT_TIMESTAMP(), '{$T234['T234_Solicitante']}', '{$C007_Id_Finalizou}', 'S','{$T234['T234_C007_Id_Cliente']}','S')");
        $erroInsert = mysql_error();

        if (!empty($erroInsert)) {
            $retorno[0] = false;
            $retorno[1] = "ERRO MYSQL: " . $erroInsert;
            mysql_query('ROLLBACK');
            return $retorno;
        }

        $retorno[1] = "Agradecemos sua avaliação!";
        $retorno[0] = true;

        log("Deu certo!");

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

    } else {
        $retorno[1] = "Chamado já avaliado!";
        $retorno[0] = false;
    }

    return $retorno;
}

function atualizarPacoteMes($D166_Id)
{
    global $g, $confUsuario;

    $retorno = array();

    $sqlC007 = mysql_query("SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}'");
    $C007 = mysql_fetch_array($sqlC007);

    $sqlT234 = mysql_query("SELECT T234_Id,D166_Grupo_Suporte FROM T234 LEFT JOIN D166 ON T234_D166_Id=D166_Id LEFT JOIN D167 ON T234_D167_Id=D167_Id WHERE T234_D166_Id='{$D166_Id}' AND T234_Flag_Finalizado='N'");
    $rT234 = mysql_num_rows($sqlT234);

    if ($rT234 <= 0) {
        $retorno[0] = false;
        $retorno[1] = "Não há chamados desse quadro em aberto.";

        return $retorno;
    }

    while ($T234 = mysql_fetch_array($sqlT234)) {
        log("entrou no loop do chamado: " . $T234['T234_Id']);

        $erro = mysql_error();
        if (!$erro) {
            $retorno[0] = false;
            $retorno[1] = "Erro Update:" . $erro;
        }

        $T234['D166_Grupo_Suporte'] = strtoupper($T234['D166_Grupo_Suporte']);

        $T252_Descricao = <<<EOT
			<b>PACOTE MELHORIAS {$T234['D166_Grupo_Suporte']}</b><br>
     		Este chamado esta pronto e atualizado no seu sistema.<br>
     		Mais informacoes consulte a tela "Pacote atualizacao"<br>
     		<font style="font-size:11px;color:red;">(No caso de erro ou duvida favor abrir novo chamado)</font>
EOT;

        $T252_Descricao = utf8_encode($T252_Descricao . $T252_Descricao_Html);
        $T252_Descricao = str_replace("\n", '', $T252_Descricao);

        finalizarChamado($T234['T234_Id'], $T252_Descricao, $C007['C007_Primeiro_Nome'], "999", "");
    }

    $retorno[0] = true;
    $retorno[1] = "Chamados do pacote finalizados com sucesso!";

    return $retorno;
}

function finalizarChamadoAbandonado($T234_Id, $emailNotificacao, $C007_Id_Finalizou = false, $cliente = false, $mensagemAvaliacao = false)
{
    global $g, $confUsuario;

    $numeroChamado = str_pad($T234_Id, 8, "0", STR_PAD_LEFT);
    if ($C007_Id_Finalizou !== false) {
        $sqlC007 = mysql_query("SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id = '{$C007_Id_Finalizou}'");
        $C007 = mysql_fetch_array($sqlC007);
        if ($cliente !== false) {
            $C007['C007_Primeiro_Nome'] = $cliente;
        }

        $T252_Descricao = <<<EOT
     		Chamado finalizado por {$C007['C007_Primeiro_Nome']}
EOT;
        log("Descricao:".$T252_Descricao);

        finalizarChamado($T234_Id, $T252_Descricao, $C007['C007_Primeiro_Nome'], $C007_Id_Finalizou, "",false,$mensagemAvaliacao);

    } else {
        $T252_Descricao = <<<EOT
            Chamado finalizado por por {$C007['C007_Primeiro_Nome']}
EOT;

        $Assunto = "Hardness Sistemas - O chamado {$numeroChamado} foi finalizado automaticamente.";
        $Mensagem = "O chamado {$numeroChamado} foi concluído há alguns dias e como não tivemos mais interações suas estamos finalizando este chamado automaticamente.<br /><br /><br /><span style=\"font-size: 10px\">Este é um e-mail automático, por favor não responda.</span>";
        finalizarChamado($T234_Id, $T252_Descricao, "SISTEMA", "999", "");
        
    }

/*    $De = "Suporte Hardness";
$Para = strtolower($emailNotificacao);
$dataHora = date("d/m/Y H:i");
echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
$.ajax({
url: '/etc/etc006/form/etc006FormEmailEnviar/',
dataType: 'html',
data: 'ajax=true&divId=0&emailNotificacaoSuporte=true&emailEnviado=true&emailDe=' + encodeURIComponent('{$De}') + '&emailPara=' + encodeURIComponent('{$Para}') + '&emailAssunto=' + encodeURIComponent('{$Assunto}') + '&emailMensagem=' + encodeURIComponent('{$Mensagem}'),
success: function(data) {
return false;
}
});
} </script></div>
EOT; */
}

function finalizarChamado($T234_Id, $T252_Descricao, $C007_Primeiro_Nome, $T252_C007_Id, $T252_C007_Id_Cliente = false, $T252_Flag_Feedback = false,$mensagemAvaliacao = false)
{
    global $g, $confUsuario;

    $sqlT234 = mysql_query("SELECT T234_D167_Id,D167_Flag_Finalizar_Chamado FROM T234 LEFT JOIN D167 ON T234_D167_Id=D167_Id WHERE T234_Id='{$T234_Id}'");
    $T234 = mysql_fetch_array($sqlT234);

    if (!$T252_C007_Id_Cliente) {
        $T252_C007_Id_Cliente = "";
    }

    if (!$T252_Flag_Feedback) {
        $T252_Flag_Feedback = "N";
    }

    $T252_Descricao_Html="";
    $urlImagem = "{$confUsuario["urlRaiz"]}hardness3/static/img/avaliacao";
    if($mensagemAvaliacao===true){
        $T252_Descricao_Html = <<<EOT
            <br /><br />
            <table id="tabelaNotas" collspace="1" height="0px;" style="border-collapse: collapse;background:white;">
                <tr>
                    <td colspan=5 style="background:#383737;color:white;text-align:center;">Gostaria de avaliar nosso atendimento!?</td>
                </tr>
                <tr cellpadding="4" style="cursor:pointer;text-align:center;color:#383737;">
                    <td valign="top"><img src="{$urlImagem}1.png" id="trello"></td>
                    <td valign="top"><img src="{$urlImagem}2.png" id="trello"></td>
                    <td valign="top"><img src="{$urlImagem}3.png" id="trello"></td>
                    <td valign="top"><img src="{$urlImagem}4.png" id="trello"></td>
                    <td valign="top"><img src="{$urlImagem}5.png" id="trello"></td>
                </tr>
                <tr style="cursor:pointer;text-align:center;font-size:10px;color:#383737;">
                    <td valign="center">Muito ruim</td>
                    <td valign="center">Ruim</td>
                    <td valign="center">Regular</td>
                    <td valign="center">Bom</td>
                    <td valign="center">Muito bom</td>
                </tr>
            </table>
EOT;
        $T252_Descricao_Html = str_replace("\n", '', $T252_Descricao_Html);
    }
    

    $T252_Descricao = $T252_Descricao . $T252_Descricao_Html;

    mysql_query("INSERT INTO T252 (T252_T234_Id, T252_Descricao, T252_Data_Hora, T252_Usuario, T252_C007_Id, T252_Flag_Finalizacao, T252_C007_Id_Cliente,T252_Flag_Feedback) VALUES ('{$T234_Id}', '{$T252_Descricao}', CURRENT_TIMESTAMP(), '{$C007_Primeiro_Nome}', '{$T252_C007_Id}', 'S','{$T252_C007_Id_Cliente}','{$T252_Flag_Feedback}')");

    calcularHorasAtendimento($T234_Id);

    $sqlCal = mysql_query("SELECT CURRENT_TIMESTAMP() AS dataAtual,TIMEDIFF(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura,T234_Data_Hora_Finalizado)),T234_Horas_Pausado) AS TotalHoras FROM T234 WHERE T234_Id='{$T234_Id}'");
    $resultCal = mysql_fetch_array($sqlCal);

    mysql_query("UPDATE T234 SET T234_Flag_Triagem='N',T234_Flag_Finalizado = 'S', T234_Data_Hora_Finalizado = '{$resultCal['dataAtual']}',T234_Total_Horas_Atendimento='{$resultCal['TotalHoras']}' WHERE T234_Id = '{$T234_Id}'");
}

function validaTotalReais($array)
{
    $tempoEstimado = $array['todosCampos']['T236_Tempo_Estimado'];
    $tempoEstimado = explode(':', $tempoEstimado);
    $hora = $tempoEstimado[0] * 60;
    $minutos = $hora + $tempoEstimado[1];
    $horaDecimal = $minutos / 60;
    $totalReais = $horaDecimal * $array['todosCampos']['T236_Valor_Hora'];
    return gCorrigeNumero($totalReais);
}

function validaTotalReaisSum($extra)
{
    $T236 = mysql_query("SELECT * FROM T236 {$extra}");
    $Total = 0;
    while ($mT236 = mysql_fetch_array($T236)) {
        $tempoEstimado = $mT236['T236_Tempo_Estimado'];
        $tempoEstimado = explode(':', $tempoEstimado);
        $hora = $tempoEstimado[0] * 60;
        $minutos = $hora + $tempoEstimado[1];
        $horaDecimal = $minutos / 60;
        $totalReais = $horaDecimal * $mT236['T236_Valor_Hora'];
        $Total = $Total + $totalReais;
    }
    return gCorrigeNumero($Total);
}

function retornaNomeListaAntes($array)
{
    $T273_D167_Antes = $array['todosCampos']['T273_D167_Antes'];

    $D167 = mysql_query("select D167_Id,D167_Nome_Lista from D167 where D167_Id={$T273_D167_Antes}");
    $rD167 = mysql_fetch_array($D167);
    $numD167 = mysql_num_rows($D167);

    if ($numD167 > 0) {
        return $rD167["D167_Nome_Lista"];
    } else {
        return "-";
    }
}

function retornaImagemUrl($array)
{
    $T273_Link_Anexo = $array['todosCampos']['T273_Link_Anexo'];

    if (!empty($T273_Link_Anexo)) {
        return "<a href='{$T273_Link_Anexo}' target='_blank'>Anexo</a>";
    } else {
        return $T273_Link_Anexo;
    }

}

function retornaNomeListaDepois($array)
{
    $T273_D167_Depois = $array['todosCampos']['T273_D167_Depois'];

    $D167 = mysql_query("select D167_Id,D167_Nome_Lista from D167 where D167_Id={$T273_D167_Depois}");
    $rD167 = mysql_fetch_array($D167);
    $numD167 = mysql_num_rows($D167);

    if ($numD167 > 0) {
        return $rD167["D167_Nome_Lista"];
    } else {
        return "-";
    }
}

function formatBytes($array)
{
    $valor = $array['campoAtual'];
    $valor = $valor / 1024;
    if ($valor < 1030) {
        return gCorrigeNumero(round($valor, 2)) . " KB";
    } else {
        $valor = $valor / 1024;
        if ($valor < 1030) {
            return gCorrigeNumero(round($valor, 2)) . " MB";
        } else {
            $valor = $valor / 1024;
            return gCorrigeNumero(round($valor, 2)) . " GB";
        }
    }
}

function montarBalaoInteracao($conteudo, $tipo, $logo = false, $cor = false, $rowT252 = false, $acessoSuporte = false)
{
    global $g;
    if (!(stristr($conteudo['mensagem'], "id='trello'")) && !(stristr($conteudo['mensagem'], 'id="trello"'))) {
        $conteudo = str_replace(array('<div><br></div><div><img', '<div><br></div><div><br></div><div><img'), '<div style="float: left; margin: 3px;"><img', $conteudo);
        $conteudo = str_replace('<img', '<img style="width: 75px; height: 75px; border-radius: 3px; cursor: pointer;" onclick="zoomImagem(this); return false;"', $conteudo);
    }

    // if ((!empty($rowT252['T234_T234_Vinculo_Id']) && empty($rowT252['T252_T252_Vinculo_Id'])) && !$acessoSuporte) {
    //     $linkEnvioMensagem = "<a href=\"#\" onclick=\"enviarRespostaHardness('{$rowT252['T252_Id']}','{$rowT252['T252_T234_Id']}','H','{$g['divId']}');\" style='font-size:13px;'>Encaminhar resposta</a>";
    // }

    // if ((!empty($rowT252['T234_T234_Vinculo_Id']) && empty($rowT252['T252_T252_Vinculo_Id'])) && $acessoSuporte) {
    //     $linkEnvioMensagemCliente = "<a href=\"#\" onclick=\"enviarRespostaHardness('{$rowT252['T252_Id']}','{$rowT252['T252_T234_Id']}','C','{$g['divId']}');\" style='font-size:13px;'>Encaminhar resposta</a>";
    // }

    $boxShadow = "-webkit-box-shadow: -1px 1px 3px 0px rgba(50, 50, 50, 0.50); -moz-box-shadow: -1px 1px 3px 0px rgba(50, 50, 50, 0.50); box-shadow: -1px 1px 3px 0px rgba(50, 50, 50, 0.50);";
    if ($logo !== false) {
        $float = ($tipo == 'R') ? 'left' : 'right';
        $impressaoLogo = <<<EOT
			<div style="width: 50px; height: 50px; float: {$float}; background-color: #FFF; border-radius: 3px; vertical-align: center; display: inline-block;">
				<img src='{$logo}' style='max-width: 50px; max-height: 50px;'>
			</div>
EOT;
    }
    $impressaoLogo = '';
    //Interacao do usuario
    if ($tipo == 'R') {
        $ladoMargin = ($cor == "#FFF") ? 'left' : 'right';
        $retorno .= <<<EOT
			<div style="max-height:200px !important; overflow:auto !important;z-index:1;position:relative;box-sizing:border-box; margin: 5px 55px 1px 1px; background-color: #C1FFB6; border-radius: 3px; padding: 3px; 2px; {$boxShadow}">
				{$impressaoLogo}
				<div style="float: left; font-size: 14px;">
					<div style="font-size: 11px; font-family: Helvetica; color: #222; font-weight: normal; margin-bottom: 3px;">{$conteudo['data']} - {$conteudo['responsavel']} {$linkEnvioMensagemCliente}</div>
					{$conteudo['mensagem']}
				</div>
				<div style="clear: both;" />
			</div>
EOT;
    }
    //Interação suporte
    if ($tipo == 'E') {
        $retorno .= <<<EOT
			<div style="max-height:200px !important; overflow:auto !important;z-index:1;position:relative;box-sizing:border-box; margin: 5px 1px 1px 55px; background-color: #FFF; border-radius: 3px; padding: 3px; 2px; {$boxShadow}">
				<div style="font-size: 14px;">
					<div style="font-size: 11px; font-family: Helvetica; color: #222; font-weight: normal; margin-bottom: 3px; width: 100%">{$conteudo['data']} - {$conteudo['responsavel']} - {$linkEnvioMensagem}</div>
					{$conteudo['mensagem']}
				</div>
				{$impressaoLogo}
				<div style="clear: both;" />
			</div>
EOT;
    }
    return $retorno;
}

function continuarDesenvolvimento($array, $flagStatus)
{
    global $g;
    require_once 'bibliotecas/funcoesCallback/statusDesenvolvimento.php';
    $status = statusDesenvolvimento(false, $flagStatus);
    return <<<EOT
		<span onclick="continuarDesenvolvimento('{$array['todosCampos']['T234_Id']}', '{$flagStatus}', '{$status}', '{$g['divId']}');">
		   <div class="icone ui-state-default ui-corner-all">
				<span class="ui-icon ui-icon-circle-arrow-e" title="Enviar para {$status}"></span>
		   </div>
		</span>
EOT;
}

function retornarDesenvolvimento($array, $flagStatus)
{
    global $g;
    require_once 'bibliotecas/funcoesCallback/statusDesenvolvimento.php';
    $status = statusDesenvolvimento(false, $flagStatus);
    return <<<EOT
		<span onclick="retornarDesenvolvimento('{$array['todosCampos']['T234_Id']}', '{$flagStatus}', '{$status}', '{$g['divId']}');">
		   <div class="icone ui-state-default ui-corner-all">
				<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Enviar para {$status}"></span>
		   </div>
		</span>
EOT;
}

function calcularFinalTarefa($finalUltimaTarefa, $tempoTarefa)
{
    $inicio = '08:30';
    $final = '18:00';
    $inicioPausa = '12:00';
    $finalPausa = '13:00';

    $tempoTarefa = explode(':', $tempoTarefa);
    $duracao = ($tempoTarefa[0] * 60) + $tempoTarefa[1]; //Duração da tarefa em minutos

    $tarefaDistribuida = array();
    $proximoInicio = $finalUltimaTarefa_ = $finalUltimaTarefa;

    for ($i = 0; $i <= $duracao; $i++) {
        $finalUltimaTarefa = date('Y-m-d H:i', strtotime($finalUltimaTarefa . ' + ' . $i . ' minute'));
        if (strtotime($finalUltimaTarefa) > strtotime(date('Y-m-d ', strtotime($finalUltimaTarefa)) . $final)) {
            $tarefaDistribuida[] = array('inicio' => $proximoInicio, 'final' => $finalUltimaTarefa_, 'duracao' => $i - 1);
            $finalUltimaTarefa = $proximoInicio = date('Y-m-d ', strtotime($finalUltimaTarefa_ . ' +1 day')) . $inicio;
            $duracao = $duracao - $i;
            $i = 0;
        } else if (strtotime($finalUltimaTarefa) > strtotime(date('Y-m-d ', strtotime($finalUltimaTarefa)) . $inicioPausa) && strtotime($finalUltimaTarefa) <= strtotime(date('Y-m-d ', strtotime($finalUltimaTarefa)) . $finalPausa)) {
            $tarefaDistribuida[] = array('inicio' => $proximoInicio, 'final' => $finalUltimaTarefa_, 'duracao' => $i - 1);
            $finalUltimaTarefa = $proximoInicio = date('Y-m-d ', strtotime($finalUltimaTarefa_)) . $finalPausa;
            $duracao = $duracao - $i;
            $i = 0;
        } else {
            $finalUltimaTarefa_ = $finalUltimaTarefa;
            $finalUltimaTarefa = date('Y-m-d H:i', strtotime($finalUltimaTarefa . ' - ' . $i . ' minute'));
        }
    }
    $tarefaDistribuida[] = array('inicio' => $proximoInicio, 'final' => $finalUltimaTarefa_, 'duracao' => $i - 1);
    return $tarefaDistribuida;
}

function processarPrioridadeChamado($T234_Id, $novaOrdem)
{
    global $g;
    $ultimoItem = 0;
    $contadorOrdem = 1;
    $sql = mysql_query("SELECT * FROM T234 WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Triagem = 'N' AND T234_Flag_Finalizado != 'S' AND T234_Id != '{$T234_Id}' AND (T234_Flag_Tipo_OS = 'E' OR T234_Flag_Tipo_OS = 'M') ORDER BY T234_Ordem_Desenvolvimento, T234_Data_Hora_Abertura");
    while ($row = mysql_fetch_array($sql)) {
        if ($contadorOrdem == $novaOrdem) {$contadorOrdem++;}
        mysqli_query("UPDATE T234 SET T234_Ordem_Desenvolvimento = '{$contadorOrdem}' WHERE T234_Id = '{$row['T234_Id']}'");
        $contadorOrdem++;
    }
    return true;
}

function processarPrioridadeCliente($T234_Id, $D024_Id, $novaOrdem)
{
    global $g;
    $ultimoItem = 0;
    $contadorOrdem = 1;
    $sql = mysql_query("SELECT * FROM T234 WHERE T234_Flag_Tipo_Suporte = 'S' AND T234_Flag_Finalizado = 'N' AND T234_Id != '{$T234_Id}' AND T234_D024_Id = '{$D024_Id}' ORDER BY T234_Prioridade_Cliente, T234_Data_Hora_Abertura");
    while ($row = mysql_fetch_array($sql)) {
        if ($contadorOrdem == $novaOrdem) {$contadorOrdem++;}
        mysqli_query("UPDATE T234 SET T234_Prioridade_Cliente = '{$contadorOrdem}' WHERE T234_Id = '{$row['T234_Id']}'");
        $contadorOrdem++;
    }
    return true;
}

function prioridadeChamado($array)
{
    if ($array['todosCampos']['T234_Flag_Finalizado'] == 'N') {
        return $array['campoAtual'];
    } else {
        return '';
    }
}

function T234_D166_Nome_Grupo($array)
{
    return $array['todosCampos']['D166_Grupo_Suporte'];
}

function retornaImagemAvaliacao($array)
{
    global $g, $confUsuario;
    $T234_Avaliacao_Cliente = $array['todosCampos']['T234_Avaliacao_Cliente'];

    if ($T234_Avaliacao_Cliente > 0) {
        $urlImagem = "{$confUsuario["urlRaiz"]}hardness3/static/img/avaliacao" . $T234_Avaliacao_Cliente . ".png";
        return "<img src='$urlImagem' width='60px'>";
    } else {
        return ' - ';
    }
}

function processarFeedback($T234_Id=false){
    global $confUsuario;
    mysql_set_charset('utf8');

    $resposta = array();
    log("entrou na função");
    $sqlT252 = mysql_query("SELECT *
                              FROM (
                            SELECT T234_Id,T252_Id,T252_Data_Hora, T252_C007_Id_Cliente, T234_C007_Id_Cliente,T234_Solicitante,D167_Flag_Finalizar_Chamado
                              FROM T252
                         LEFT JOIN T234 ON T252_T234_Id=T234_Id
                         LEFT JOIN D166 ON T234_D166_Id= D166_Id
                         LEFT JOIN D167 ON T234_D167_Id=D167_Id
                             WHERE D166_Flag_Processar_Feedback = 'S'
                               AND T234_Flag_Finalizado = 'N'
                               AND T252_Flag_Feedback != 'S'
                          ORDER BY T252_Id DESC) as Conteudo
                          GROUP BY T234_Id");

    $erro = mysql_error();
    if (!empty($erro)) {
        $resposta['code'] = false;
        $resposta['data'] = 'Erro mysql: ' . $erro;
        return $resposta;
    }

    $interacoes['0'] = 0;
    $interacoes['24'] = 0;
    $interacoes['48'] = 0;
    $interacoes['72'] = 0;

    $sqlUsuario = mysql_query("SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';");
    $usuarioSuporte = mysql_fetch_array($sqlUsuario);

    if (mysql_num_rows($sqlT252) > 0) {
        while ($rowT252 = mysql_fetch_array($sqlT252)) {
            if ($rowT252['T252_C007_Id_Cliente'] != $rowT252['T234_C007_Id_Cliente']) {

                $interacoes['0']++;

                $chamado = str_pad($rowT252['T234_Id'], 8, "0", STR_PAD_LEFT);

                $sqlHorasUltimoFeedback = mysql_query("SELECT * FROM 
                                                             (SELECT T252_Data_Hora,
                                                                     TIMESTAMPDIFF(HOUR,T252_Data_Hora, CURRENT_TIMESTAMP()) as Horas,
                                                                     TIMESTAMPDIFF(MINUTE,T252_Data_Hora, CURRENT_TIMESTAMP()) as Minutos
                                                                FROM T252
                                                           LEFT JOIN T234 ON T252_T234_Id = T234_Id
                                                               WHERE T252_T234_Id='{$rowT252['T234_Id']}'
                                                                 AND T252_Flag_Feedback = 'S'
                                                            ORDER BY T252_Id DESC) as Conteudo");

                $erro = mysql_error();
                if (!empty($erro)) {
                    $resposta['code'] = false;
                    $resposta['data'] = 'Erro mysql: ' . $erro;
                   return $resposta;
                }

                $HorasUltimoFeedback = mysql_fetch_array($sqlHorasUltimoFeedback);

                if (!empty($HorasUltimoFeedback['T252_Data_Hora'])) {

                    if (date('w', date('Y-m-d')) < date('w', strtotime($HorasUltimoFeedback['T252_Data_Hora']))) {
                        $HorasUltimoFeedback['Horas'] -= 48;
                    }

                    // Não dar novo feedback antes de 24 horas do último.
                    if ($HorasUltimoFeedback['Minutos'] > 0 && $HorasUltimoFeedback['Horas'] < 24) {
                        continue;
                    }
                }

                $sqlHoras = mysql_query("SELECT TIMESTAMPDIFF(HOUR,'{$rowT252['T252_Data_Hora']}', CURRENT_TIMESTAMP()) as Horas");

                $erro = mysql_error();
                if (!empty($erro)) {
                    $resposta['code'] = false;
                    $resposta['data'] = 'Erro mysql: ' . $erro;
                   return $resposta;
                }

                $Horas = mysql_fetch_array($sqlHoras);

                if (date('w', strtotime(date('Y-m-d'))) < date('w', strtotime($rowT252['T252_Data_Hora']))) {
                    $Horas['Horas'] -= 48;
                }

                log("Feedback - chamado {$rowT252['T234_Id']} - Horas {$Horas['Horas']} - " . date('w', strtotime(date('Y-m-d'))) . '<' . date('w', strtotime($rowT252['T252_Data_Hora'])));

                if ($Horas['Horas'] >= 72) {
                    //Insere uma interação avisando que o chamado foi finalizado por falta de interação do cliente 72h ;
                    $resposta['code'] = true;
                    $texto = "Bom dia, como não tivemos mais interações suas neste chamado {$chamado} a mais de 72hs, o mesmo foi finalizado.";

                    // if ((mb_detect_encoding($texto)=='UTF-8') && (!mb_detect_encoding($texto,'UTF-8',true))) {
                    //     $texto = utf8_encode($texto);
                    // }
                    finalizarChamado($rowT252['T234_Id'], $texto, $usuarioSuporte['C007_Primeiro_Nome'], $rowT252['T252_C007_Id_Cliente'], "", "S");

                    $erro = mysql_error();
                    if (!empty($erro)) {
                        $resposta['code'] = false;
                        $resposta['data'] = 'Erro mysql: ' . $erro;
                        return $resposta;
                    }
                    //Será arquivado o card no trello ao ser finalizado no sistema
                    require_once 'bibliotecas/webservices/trello/apiTrello.php';
                    $Trello = new Trello();
                    $retorno = $Trello->arquivarCard($rowT252['T234_Id'], true);

                    log("Feedback - Chamado {$rowT252['T234_Id']} 72 horas");

                    $interacoes['72']++;

                } else if ($Horas['Horas'] >= 48) {
                    //Insere uma interação cobrando o cliente por uma resposta 48h
                    $resposta['code'] = true;
                    $texto = "Bom dia, sobre este chamado {$chamado}, responsável {$rowT252['T234_Solicitante']}, fazem 48hs do nosso retorno, caso não tenha ficado ok, favor realizar nova interação no chamado, obrigado.";
                    // if ((mb_detect_encoding($texto) == 'UTF-8') && (!mb_detect_encoding($texto, 'UTF-8', true))) {
                    //     $texto = utf8_encode($texto);
                    // }
                    mysql_query("INSERT INTO T252 (T252_C007_Id, T252_Usuario, T252_T234_Id, T252_Descricao, T252_Data_Hora, T252_C007_Id_Cliente, T252_Flag_Lido, T252_Flag_Feedback) VALUES ('{$g['usuarioAtual']}', '{$usuarioSuporte['C007_Primeiro_Nome']}', '{$rowT252['T234_Id']}','$texto', now(),'{$rowT252['T252_C007_Id_Cliente']}', 'N', 'S')");
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        $resposta['code'] = false;
                        $resposta['data'] = 'Erro mysql: ' . $erro;
                        return $resposta;
                    }
                    $interacoes['48']++;

                    log("Feedback - Chamado {$rowT252['T234_Id']} 48 horas");

                } else if ($Horas['Horas'] >= 24) {
                    //Insere uma interação cobrando o cliente por uma resposta 24h ;
                    $resposta['code'] = true;
                    $texto = "Bom dia, sobre este chamado {$chamado}, responsável {$rowT252['T234_Solicitante']}, fazem 24hs do nosso retorno, caso não tenha ficado ok, favor realizar nova interação no chamado, obrigado.";
                    // if ((mb_detect_encoding($texto) == 'UTF-8') && (!mb_detect_encoding($texto, 'UTF-8', true))) {
                    //     $texto = utf8_encode($texto);
                    // }
                    mysql_query("INSERT INTO T252 (T252_C007_Id, T252_Usuario, T252_T234_Id, T252_Descricao, T252_Data_Hora, T252_C007_Id_Cliente, T252_Flag_Lido, T252_Flag_Feedback) VALUES ('{$g['usuarioAtual']}', '{$usuarioSuporte['C007_Primeiro_Nome']}', '{$rowT252['T234_Id']}','$texto', now(),'{$rowT252['T252_C007_Id_Cliente']}', 'N', 'S')");
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        $resposta['code'] = false;
                        $resposta['data'] = 'Erro mysql: ' . $erro;
                        return $resposta;
                    }
                    $interacoes['24']++;

                    log("Feedback - Chamado {$rowT252['T234_Id']} 24 horas");                    
                }
            }
        }
        $resposta['code'] = true;
        $resposta['data'] = "Feedbacks enviados com sucesso!<br>Todos = {$interacoes['0']}<br>24 horas = {$interacoes['24']}<br>48 horas = {$interacoes['48']}<br>72 horas = {$interacoes['72']}";
    } else {
        $resposta['code'] = false;
        $resposta['data'] = "Não existem feedbacks pendentes para envio.";
    }
    return $resposta;
}

function calcularHorasAtendimento($T234_Id)
{
    $atendimento = 0;
    $ultimaInteracao = 0;
    $pausa = 0;
    $retorno = array();
    $sqlT252 = mysql_query("SELECT  T234_C007_Id_Cliente,
    							    T252_C007_Id_Cliente,
    							    T252_Data_Hora,
    							    ifnull(D167_Horas_Validade,'00:00:00') as D167_Horas_Validade,
    							    T234_Data_Hora_Abertura,
    							    ifnull(T234_Horas_Atendimento,'00:00:00') as T234_Horas_Atendimento,
    							    ifnull(T234_Horas_Pausado,'00:00:00') as T234_Horas_Pausado,
                                    ifnull(T234_Data_Previsao,'0000-00-00 00:00:00') as T234_Data_Previsao
    						   FROM T252
				    	  LEFT JOIN T234 ON T252_T234_Id=T234_Id
				    	  LEFT JOIN D167 ON T234_D167_Id=D167_Id
    	                      WHERE T252_T234_Id={$T234_Id}
                           ORDER BY T252_Id");

    while ($T252 = mysql_fetch_array($sqlT252)) {

        if ($T252['T234_C007_Id_Cliente'] == $T252['T252_C007_Id_Cliente']) {

            if ($ultimaInteracao != 1) {
                $dataCliente = $T252['T252_Data_Hora'];

                if ($ultimaInteracao == 2) {
                    // retorna número de horas de final de semana entre duas datas: Data inicial e Data final
                    $horasFDS = explode(':', horasFDS(substr($dataSuporte, 0, 10), substr($dataCliente, 0, 10)));

                    $sql = mysql_query("SELECT TIMESTAMPDIFF(MINUTE,'{$dataSuporte}', '{$dataCliente}') as Horas");
                    $result = mysql_fetch_array($sql);
                    $decimal = number_format($result['Horas'] / 60, 2) - $horasFDS[0];
                    $decimal = explode('.', $decimal);
                    $horas = $decimal[0];
                    $minutos = round($decimal[1] * 0.60);
                    $minutosPausa += $minutos;
                    $pausa += $horas;
                }
            }

            $ultimaInteracao = 1;

        } else {

            if ($ultimaInteracao != 2) {
                $dataSuporte = $T252['T252_Data_Hora'];
                $sql = mysql_query("SELECT TIMESTAMPDIFF(MINUTE,'{$dataCliente}', '{$dataSuporte}') as Horas");
                $result = mysql_fetch_array($sql);
                $minutosTotalAtendimento += $result['Horas'];
                $decimal = explode('.', number_format($result['Horas'] / 60, 2));
                $horas = $decimal[0];
                $minutos = round($decimal[1] * 0.60);
                $minutosAtendimento += $minutos;
                $atendimento += $horas;
            }

            $ultimaInteracao = 2;
        }

        $T234_Data_Hora_Abertura = $T252['T234_Data_Hora_Abertura'];
        $T234_Horas_Atendimento = $T252['T234_Horas_Atendimento'];
        $T234_Horas_Pausado = substr($T252['T234_Horas_Pausado'], 0, 5);
        $T234_Data_Previsao = $T252['T234_Data_Previsao'];
    }

    //calculando horas em atendimento
    $decimal = explode('.', number_format($minutosAtendimento / 60, 2));
    $atendimento += $decimal[0];
    $minutos = str_pad(round($decimal[1] * 0.60), 2, "0", STR_PAD_LEFT);
    $atendimento = str_pad($atendimento, 2, "0", STR_PAD_LEFT);
    $atendimento .= ':' . $minutos;

    //calculando horas pausadas
    $decimal = explode('.', number_format($minutosPausa / 60, 2));
    $pausa += $decimal[0];
    $pausaHoras = $pausa;
    $minutos = str_pad(round($decimal[1] * 0.60), 2, "0", STR_PAD_LEFT);
    $pausa = str_pad($pausa, 2, "0", STR_PAD_LEFT);
    $pausa .= ':' . $minutos;

    if ($T234_Horas_Pausado != $pausa) {
        mysql_query("UPDATE T234 SET T234_Horas_Pausado='{$pausa}' WHERE T234_Id='{$T234_Id}'");
    }

    //Convertendo a hora da lista em minutos
    $minutosLista = $T234_Horas_Atendimento * 60;

    //fazendo o calculo minutos_lista - minutos_atendimento
    $minutosAtendidos = $minutosLista - $minutosTotalAtendimento;

    //convertendo os minutos do calculo em formato horas:minutos
    $decimal = explode('.', number_format($minutosAtendidos / 60, 2));
    $horas = $decimal[0];
    $minutos = round($decimal[1] * 0.60);
    $horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
    $minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
    $diferencaHoras = $horas . ":" . $minutos;

    $prazo = $T234_Horas_Atendimento;

    if($T234_Data_Previsao!="0000-00-00 00:00:00"){
        $previsao = gCorrigeData($T234_Data_Previsao);

        $sqlA = mysql_query("SELECT TIMESTAMPDIFF(MINUTE, '{$T234_Data_Hora_Abertura}','{$T234_Data_Previsao}') as diferencaPrazo");
        $resultA = mysql_fetch_array($sqlA);

        // $horasFDS = horasFDS(substr($T234_Data_Hora_Abertura, 0, 10), substr($T234_Data_Previsao, 0, 10));

        //calculando prazo a partir de uma data de previsão fixa
        $decimal = explode('.', number_format($resultA['diferencaPrazo'] / 60, 2));
        $horas = $decimal[0];
        $horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
        $minutos = round($decimal[1] * 0.60);
        $minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
        $prazo = $horas . ":" . $minutos.":00";

        //retirando do calculo do prazo as horas do fim de semana
        $sql = mysql_query("SELECT SUBTIME('{$prazo}', '{$horasFDS}') as prazo");
        $result = mysql_fetch_array($sql);
        $prazo = gCorrigeData($result['prazo']);
       
        //calculando horas faltante para entrega
        $sqlA = mysql_query("SELECT TIMESTAMPDIFF(MINUTE, CURRENT_TIMESTAMP(),'{$T234_Data_Previsao}') as diferencaData");
        $resultA = mysql_fetch_array($sqlA);

        $decimal = explode('.', number_format($resultA['diferencaData'] / 60, 2));
        $horas = $decimal[0];
        $horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
        $minutos = round($decimal[1] * 0.60);
        $minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
        $diferencaDatas = $horas . ":" . $minutos;

    }else if(!empty($T234_Horas_Atendimento) && $T234_Horas_Atendimento != '00:00:00'){

        // retorna número de horas de final de semana entre duas datas: Data inicial e Data final
        $horasFDS = horasFDS(substr($T234_Data_Hora_Abertura, 0, 10), date("Y-m-d"));

        $sql = mysql_query("SELECT ADDTIME('{$T234_Data_Hora_Abertura}', ADDTIME(ADDTIME('{$T234_Horas_Atendimento}', concat('$pausa',':00')), '{$horasFDS}')) as previsao");
        $result = mysql_fetch_array($sql);
        $previsao = gCorrigeData($result['previsao']);

        $sqlA = mysql_query("SELECT TIMESTAMPDIFF(MINUTE, CURRENT_TIMESTAMP(),'{$result['previsao']}') as diferencaDatas");
        $resultA = mysql_fetch_array($sqlA);

        $decimal = explode('.', number_format($resultA['diferencaDatas'] / 60, 2));
        $horas = $decimal[0];
        $horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
        $minutos = round($decimal[1] * 0.60);
        $minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
        $diferencaDatas = $horas . ":" . $minutos;
    }else{
        $previsao = "Analisando";
    }

    $retorno['diferencaDatas'] = $diferencaDatas;
    $retorno['previsaoData'] = $previsao;
    $retorno['atendimentoHoras'] = $atendimento;
    $retorno['pausaHoras'] = $pausa;
    $retorno['diferencaHoras'] = $diferencaHoras;
    $retorno['prazo'] = $prazo;

    return $retorno;
}

function horasFDS($dataInicial, $dataFinal)
{
    while ($dataInicial != $dataFinal) {
        $diaSemana = date("w", strtotime($dataInicial));
        if ($diaSemana == 0 || $diaSemana == 6) {
            $horasFDS += 24;
        }
        // Incrementa 1 dia a data inicial
        $ano = substr($dataInicial, 0, 4);
        $mes = substr($dataInicial, 5, 2);
        $dia = substr($dataInicial, 8, 2);
        $dataInicial = date("Y-m-d", mktime(0, 0, 0, $mes, $dia + 1, $ano));
    }
    // Formata o retorno no formato "00:00:00"
    return str_pad($horasFDS, 2, "0", STR_PAD_LEFT) . ':00:00';
}
