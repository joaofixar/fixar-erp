<?php
namespace hardness;

/**
 * Atualiza o arquivo de filtros com base na busca
 */

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
            array($content1, '/outros/contratosClientes/contratosClientes/tab/'),
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
            array($content1, '/outros/contratosClientes/contratosClientes/tabIncluir/', '&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);

        break;

    case 'contentIncluirAnual':

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
            array($content1, '/outros/contratosClientes/contratosClientes/tabIncluirAnual/', '&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);

        break;

    case 'contentGerarAnuais':

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
            array($content1, '/outros/contratosClientes/contratosClientes/tabGerarAnuais/', '&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);

        break;

    case 'contentGerarMensais':

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
            array($content1, '/outros/contratosClientes/contratosClientes/tabGerarMensais/', '&acaoId=' . urlencode($acaoId)),
        );
        echo gProcessaAcoes($actions);

        break;

    case 'tab':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/contratosClientes/contratosClientes/gridAnuais/', 'Contratos Mensais'),
            array(uniqid(), '/outros/contratosClientes/contratosClientes/grid/', 'Contratos Anuais'),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');

        break;

    case 'tabGerarAnuais':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/contratosClientes/contratosClientes/formGerarAnual/', 'Mensal', '&tab=geral'),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');

        break;

    case 'tabGerarMensais':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/contratosClientes/contratosClientes/formGerarMensal/', 'Anual', '&tab=geral'),
        );

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
            array(uniqid(), '/outros/contratosClientes/contratosClientes/form/', 'Contratos Clientes', '&tab=geral&acaoId=' . urlencode($acaoId)),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
        break;

    case 'tabIncluirAnual':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        $acaoId = isset($r_acaoId) ? $r_acaoId : false;

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/contratosClientes/contratosClientes/formAnual/', 'Contratos Clientes', '&tab=geral&acaoId=' . urlencode($acaoId)),
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
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T135_Id', $g['divId'])),
            array('colunaValor' => 'T135_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T135_Flag_Gerar_Recebimento', 'colunaTitulo' => '', 'callback' => 'validaCampoGerarContrato', 'style' => 'width:1%;'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'T135_Data_Inicio', 'colunaTitulo' => 'Início', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T135_Data_Termino', 'colunaTitulo' => 'Término', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T135_Valor_Contrato', 'colunaTitulo' => 'Valor', 'callback' => 'gCorrigeNumero'),
            array('colunaValor' => 'IF(T135_Tipo="M", "Mensal", "Anual")', 'colunaTitulo' => 'Tipo'),
            array('colunaValor' => 'T135_Dia_Vencimento', 'colunaTitulo' => 'Dia Venci'),
            array('colunaValor' => 'T135_Mes_Inicial', 'colunaTitulo' => 'Mês Inicial'),
            array('colunaValor' => 'T135_Numero_Parcelas', 'colunaTitulo' => 'N. Parc'),
            array('colunaValor' => 'T135_Tipo_Documento', 'colunaTitulo' => 'Tipo Dcto.'),
            array('colunaValor' => 'T135_D032_Id', 'colunaTitulo' => 'Grupo', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D032', 'D032_Id', 'D032_Descricao'), 'style' => 'width:1%', 'callback' => 'retornaGrupo'),
            array('colunaValor' => 'T135_D014_Id', 'colunaTitulo' => 'Conta', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D014', 'D014_Id', 'D014_SubConta'), 'style' => 'width:1%', 'callback' => 'retornaConta'),
            array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'Conta', 'colunaExibir' => false),
            array('colunaValor' => 'T135_Qtde_Maxima_Melhorias_Mes', 'colunaTitulo' => 'Qtde Melhorias','colunaEditar' => true,'colunaEditarTodos' => true),
            array('colunaValor' => 'T135_Qtde_Maxima_Chamados_Atendimento', 'colunaTitulo' => 'Qtde Erros','colunaEditar' => true,'colunaEditarTodos' => true),
            array('colunaValor' => 'T135_Qtde_Maxima_Chamados_Duvida', 'colunaTitulo' => 'Qtde Duvidas','colunaEditar' => true,'colunaEditarTodos' => true),
        );

        /**
         * Definição da SQL
         */
        $from = "T135";

        $extra = <<<EOT
		left join D024 on D024_Id=T135_D024_Id
		left join D032 on D032_Id=T135_D032_Id
		left join D014 on D014_Id=T135_D014_Id
		WHERE T135_Tipo='A'
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T135_Data_Inicio' => array('titulo' => 'Início', 'tipo' => 'data'),
            'T135_Data_Termino' => array('titulo' => 'Término', 'tipo' => 'data'),
            'T135_Valor_Contrato' => array('titulo' => 'Valor', 'tipo' => 'numero'),
            'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),

            // 'T235_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            // 'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            // 'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            // 'T234_Flag_Finalizado' => array('titulo' => 'Finalizado', 'tipo' => 'select', 'select' => array(
            //     array('title' => 'Todos', 'value' => ''),
            //     array('title' => 'Sim', 'value' => 'S'),
            //     array('title' => 'Não', 'value' => 'N'),
            // ), 'naoGerarExtra' => false),
            // 'T234_Data_Hora_Finalizado' => array('titulo' => 'Data finalizado', 'tipo' => 'data'),
            // 'T234_Observacao_Finalizado' => array('titulo' => 'Obs finalizado', 'tipo' => 'texto')
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            // if($array['todosCampos']['T234_Flag_Finalizado']=='N'){
            //     return 'linhaVermelho';
            // }
        }
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
         * Botões (Versão Acima do GRID)
         */
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="registroSuporteBtnIncluir">Incluir</button>
				<button type="button" id="gerarRecebimentosMensais">Gerar Recebimentos</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#registroSuporteBtnIncluir').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Contrato Mensal', '/outros/contratosClientes/contratosClientes/contentIncluir/','', [550,450]);
				});
				$('#gerarRecebimentosMensais').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar recebimentos anuais', '/outros/contratosClientes/contratosClientes/contentGerarMensais/','', [300,150]);
				});
				$('#').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/outros/contratosClientes/contratosClientes/gerarContratosMensais/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Ok', request.data, \$buttons);
				    				divRefresh('{$g['divIdParent']}');
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Gerar recebimentos anuais', 'Você tem certeza que deseja gerar os recebimentos dos contratos selecionados?', \$buttons);
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
        //$valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
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
				if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR' && !$(event.target).hasClass('editar-linha')) { return true; }
				var id = $(this).closest('tr').attr('id').replace(/T135_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Contrato', '/outros/contratosClientes/contratosClientes/contentIncluir/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [550,450]);
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

    case 'gridAnuais':
        /**
         * Definição do Grid (Tabela)
         */

        $grid = array(
            //array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('T135_Id', $g['divId'])),
            array('colunaValor' => 'T135_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%', 'colunaExibir' => false),
            array('colunaValor' => 'T135_Flag_Gerar_Recebimento', 'colunaTitulo' => '', 'callback' => 'validaCampoGerarContrato', 'style' => 'width:1%;'),
            array('colunaValor' => 'D024_Nome_Empresa', 'colunaTitulo' => 'Cliente'),
            array('colunaValor' => 'T135_Data_Inicio', 'colunaTitulo' => 'Início', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T135_Data_Termino', 'colunaTitulo' => 'Término', 'callback' => 'gCorrigeData'),
            array('colunaValor' => 'T135_Valor_Contrato', 'colunaTitulo' => 'Valor', 'colunaCasasDecimais' => 2, 'colunaEditarOculto' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'IF(T135_Tipo="M", "Mensal", "Anual")', 'colunaTitulo' => 'Tipo'),
            array('colunaValor' => 'T135_Dia_Vencimento', 'colunaTitulo' => 'Dia Venci'),
            array('colunaValor' => 'T135_Mes_Inicial', 'colunaTitulo' => 'Mês Inicial'),
            array('colunaValor' => 'T135_Numero_Parcelas', 'colunaTitulo' => 'N. Parc'),
            array('colunaValor' => 'T135_Tipo_Documento', 'colunaTitulo' => 'Tipo Dcto.'),
            array('colunaValor' => 'T135_Franquia_Horas', 'colunaTitulo' => 'Franquia Horas', 'colunaEditar' => true),
            array('colunaValor' => 'T135_Valor_Hora_Tecnica', 'colunaTitulo' => 'Valor Hora Técnica', 'colunaEditar' => true, 'colunaCasasDecimais' => 2, 'callback' => 'gCorrigeNumero'),
            array('colunaValor' => 'T135_D032_Id', 'colunaTitulo' => 'Grupo', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D032', 'D032_Id', 'D032_Descricao'), 'style' => 'width:1%', 'callback' => 'retornaGrupo'),
            array('colunaValor' => 'T135_D014_Id', 'colunaTitulo' => 'Conta', 'colunaEditar' => true, 'colunaEditarTodos' => true, 'colunaEditarGeraSelect' => array('D014', 'D014_Id', 'D014_SubConta'), 'style' => 'width:1%', 'callback' => 'retornaConta'),
            array('colunaValor' => 'D032_Descricao', 'colunaTitulo' => 'Grupo', 'colunaExibir' => false),
            array('colunaValor' => 'D014_SubConta', 'colunaTitulo' => 'Conta', 'colunaExibir' => false),
            array('colunaValor' => 'T135_Qtde_Maxima_Melhorias_Mes', 'colunaTitulo' => 'Qtde Melhorias', 'colunaEditar' => true,'colunaEditarTodos' => true),
            array('colunaValor' => 'T135_Qtde_Maxima_Chamados_Atendimento', 'colunaTitulo' => 'Qtde Erros', 'colunaEditar' => true,'colunaEditarTodos' => true),
            array('colunaValor' => 'T135_Qtde_Maxima_Chamados_Duvida', 'colunaTitulo' => 'Qtde Duvidas','colunaEditar' => true,'colunaEditarTodos' => true),

        );

        /**
         * Definição da SQL
         */
        $from = "T135";

        $extra = <<<EOT
		left join D024 on D024_Id=T135_D024_Id
		left join D032 on D032_Id=T135_D032_Id
		left join D014 on D014_Id=T135_D014_Id
		WHERE T135_Tipo='M'
EOT;
        /**
         * Filtro
         */
        $gridFiltro = array(
            'D024_Nome_Empresa' => array('titulo' => 'Cliente', 'tipo' => 'texto'),
            'T135_Data_Inicio' => array('titulo' => 'Início', 'tipo' => 'data'),
            'T135_Data_Termino' => array('titulo' => 'Término', 'tipo' => 'data'),
            'T135_Valor_Contrato' => array('titulo' => 'Valor', 'tipo' => 'numero'),
            'D032_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            'D014_SubConta' => array('titulo' => 'Conta', 'tipo' => 'texto'),

            // 'T235_Descricao' => array('titulo' => 'Grupo', 'tipo' => 'texto'),
            // 'T234_Descricao' => array('titulo' => 'Descricao', 'tipo' => 'texto'),
            // 'T234_Solicitante' => array('titulo' => 'Solicitante', 'tipo' => 'texto'),
            // 'T234_Flag_Finalizado' => array('titulo' => 'Finalizado', 'tipo' => 'select', 'select' => array(
            //     array('title' => 'Todos', 'value' => ''),
            //     array('title' => 'Sim', 'value' => 'S'),
            //     array('title' => 'Não', 'value' => 'N'),
            // ), 'naoGerarExtra' => false),
            // 'T234_Data_Hora_Finalizado' => array('titulo' => 'Data finalizado', 'tipo' => 'data'),
            // 'T234_Observacao_Finalizado' => array('titulo' => 'Obs finalizado', 'tipo' => 'texto')
        );

        /**
         * Pode ser definido uma classe para cada linha do grid
         */
        function gridLinha($array)
    {
            global $g;
            // if($array['todosCampos']['T234_Flag_Finalizado']=='N'){
            //     return 'linhaVermelho';
            // }
        }

        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
         * Botões (Versão Acima do GRID)
         */
        echo <<<EOT
			<div class="buttonsBottomTop">
				<button type="button" id="registroSuporteBtnIncluirAnual">Incluir</button>
				<button type="button" id="registroSuporteBtnGerarContratosAnuais">Gerar Recebimentos</button>
			</div>
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				$('#registroSuporteBtnIncluirAnual').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir Contrato Anual', '/outros/contratosClientes/contratosClientes/contentIncluirAnual/','', [550,450]);
				});
				$('#registroSuporteBtnGerarContratosAnuais').unbind('click').bind('click', function(data) {
					abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Gerar recebimentos anuais', '/outros/contratosClientes/contratosClientes/contentGerarAnuais/','', [300,150]);
				});
				$('#').unbind('click').bind('click', function(data) {
					var \$buttons = {
						"Sim": function() {
							$('#{$g['divId']}').showLoading();
							$.getJSON('/outros/contratosClientes/contratosClientes/gerarContratosAnuais/?ajax=true&callback=?', function(request) {
								if (request.code) {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Ok', request.data, \$buttons);
				    				divRefresh('{$g['divIdParent']}');
				    				fecharJanela('{$g['divId']}');
								} else {
									var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
				    				dialogConfirm('Erro', request.data, \$buttons);
								}
								$('#{$g['divId']}').hideLoading();
							});
							$(this).dialog("destroy").remove();
						},
						"Não": function() { $(this).dialog("destroy").remove(); }
					};
					dialogConfirm('Gerar recebimentos Mensais', 'Você tem certeza que deseja gerar os recebimentos dos contratos selecionados?', \$buttons);
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
        $totalContratos = gCorrigeNumero($g['sqlAuto']->pegarSqlCampo("SUM(T135_Valor_Contrato)", $from, $extra));
        //$valorDuracao = $g['sqlAuto']->pegarSqlCampo("TIME_FORMAT(SEC_TO_TIME(SUM((SELECT TIMESTAMPDIFF(SECOND, T234_Data_Hora_Abertura, T234_Data_Hora_Finalizado) FROM T234 AS T234_2 WHERE T234_2.T234_Id = T234_1.T234_Id))),'%H:%i:%s')", $from, $extra);
        echo <<<EOT
			<div class="buttonsBottomTop">
				<span class="item" style="text-align:right">Registros<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalRegistros}" size="11" readonly></span>
				<span class="item" style="text-align:right">Total<br><input type="text" class="whiteBg" id="inputRecebido" value="{$totalContratos}" size="11" readonly></span>
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
				var id = $(this).closest('tr').attr('id').replace(/T135_Id-/, '');
				abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' ,'{$novoIdTelaForm}', '', 'Editar Contrato Anual', '/outros/contratosClientes/contratosClientes/contentIncluirAnual/', '&acaoId=' + encodeURIComponent(id) + '&tabela={$from}', [550,450]);
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

    case 'form':
        include 'prog/etc/etc008/grid_func-js.php';
        include 'bibliotecas/classes/SRV001.php';
        $SRV001 = new SRV001();
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
        $form_tabela = 'T135';
        $form_campo_pk = 'T135_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'contratosClientesIncluir_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/contratosClientes/contratosClientes/form/';

        // novo formulário
        $form = new Formulario('formContratosClientes');
        //Editor
        $form_editor = array(
            'geral' => '{"T135_D024_Id":{"top":7,"left":158.859375,"width":249,"hidden":0},"T135_Data_Inicio":{"top":30,"left":175.59375,"width":120,"hidden":0},"T135_Data_Termino":{"top":53,"left":161.53125,"width":120,"hidden":0},"T135_Valor_Contrato":{"top":76,"left":177.015625,"width":120,"hidden":0},"T135_Dia_Vencimento":{"top":99,"left":157.859375,"width":120,"hidden":0},"T135_Mes_Inicial":{"top":122,"left":-47.078125,"width":129,"hidden":1},"T135_Tipo":{"top":122,"left":180.890625,"width":128,"hidden":0},"T135_Franquia_Horas":{"top":145,"left":126.0625,"width":120,"hidden":0},"T135_Valor_Hora_Tecnica":{"top":168,"left":108.546875,"width":120,"hidden":0},"T135_D032_Id":{"top":191,"left":171.3125,"width":127,"hidden":0},"T135_D014_Id":{"top":214,"left":172.53125,"width":127,"hidden":0},"T135_Qtde_Maxima_Melhorias_Mes":{"top":237,"left":27.640625,"width":120,"hidden":0},"T135_Qtde_Maxima_Chamados_Atendimento":{"top":260,"left":28.84375,"width":120,"hidden":0},"T135_Qtde_Maxima_Chamados_Duvida":{"top":283,"left":16,"width":120,"hidden":0},"T135_Numero_Parcelas":{"top":306,"left":272.0625,"width":129,"hidden":1}}',
        );
        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T135_D024_Id' => array('name' => 'T135_D024_Id', 'label' => 'Empresa', 'type' => 'pesquisa', 'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa')),
                    'T135_Data_Inicio' => array('name' => 'T135_Data_Inicio', 'label' => 'Início', 'type' => 'data'),
                    'T135_Data_Termino' => array('name' => 'T135_Data_Termino', 'label' => 'Término', 'type' => 'data'),
                    'T135_Valor_Contrato' => array('name' => 'T135_Valor_Contrato', 'label' => 'Valor', 'type' => 'moeda'),
                    'T135_Tipo' => array('name' => 'T135_Tipo', 'label' => 'Tipo', 'type' => 'select', 'select' => array(
                        array('title' => 'Mensal', 'value' => 'M'),
                        array('title' => 'Anual', 'value' => 'A', 'selected' => true),
                    ),
                    ),
                    'T135_Dia_Vencimento' => array('name' => 'T135_Dia_Vencimento', 'label' => 'Dia Venc', 'type' => 'text'),
                    'T135_Mes_Inicial' => array('name' => 'T135_Mes_Inicial', 'label' => 'Mês Inic', 'type' => 'text'),
                    'T135_Numero_Parcelas' => array('name' => 'T135_Numero_Parcelas', 'label' => 'Nº Parcelas', 'type' => 'text'),
                    'T135_Franquia_Horas' => array('name' => 'T135_Franquia_Horas', 'label' => 'Franquia Horas', 'type' => 'text'),
                    'T135_Valor_Hora_Tecnica' => array('name' => 'T135_Valor_Hora_Tecnica', 'label' => 'Valor Hora Técnica', 'type' => 'moeda'),
                    'T135_D032_Id' => array('name' => 'T135_D032_Id', 'label' => 'Grupo', 'type' => 'select', 'extra' => "onchange=\"alteraConta(this, '{$g['divId']}')\"", 'select' =>
                        gGeraSelect('D032', 'D032_Id', 'D032_Descricao', 'WHERE D032_Flag_Ativo="S" ORDER BY D032_Descricao', true),
                    ),
                    'T135_D014_Id' => array('name' => 'T135_D014_Id', 'label' => 'Conta', 'type' => 'select', 'align' => 'right', 'style' => 'float:right', 'select' =>
                        gGeraSelect('D014', 'D014_Id', 'D014_SubConta', "WHERE D014_Flag_Ativo='S' ORDER BY D014_SubConta", true),
                    ),
                    'T135_Qtde_Maxima_Melhorias_Mes' => array('name' => 'T135_Qtde_Maxima_Melhorias_Mes', 'label' => 'Qtde máxima de melhorias por mês', 'type' => 'text'),
                    'T135_Qtde_Maxima_Chamados_Atendimento' => array('name' => 'T135_Qtde_Maxima_Chamados_Atendimento', 'label' => 'Qtde erros em atendimento por vez', 'type' => 'text'),
                    'T135_Qtde_Maxima_Chamados_Duvida' => array('name' => 'T135_Qtde_Maxima_Chamados_Duvida', 'label' => 'Qtde duvidas em atendimento por vez', 'type' => 'text'),
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
        $botoesDir .= '<button type="submit" id="contratosClientesIncluir_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        break;

    case 'formGerarAnual':
        include 'prog/etc/etc008/grid_func-js.php';
        include 'bibliotecas/classes/SRV001.php';
        $SRV001 = new SRV001();
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
        $form_tabela = '';
        $form_campo_pk = '';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'contratosClientesIncluir_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/contratosClientes/contratosClientes/formGerarAnual/';

        // novo formulário
        $form = new Formulario('formContratosClientes');
        // Editor
        $form_editor = array(
            'geral' => '',
        );

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'Mes' => array('name' => 'Mes', 'label' => 'Mês', 'type' => 'text', 'reject' => true),
                    'Ano' => array('name' => 'Ano', 'label' => 'Ano', 'type' => 'text', 'reject' => true),
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

                    // Imprime uma mensagem e fecha a janela
                    //echo gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
                    $retorno = $SRV001->incluirContratosMensais($form);

                    if ($retorno === true) {
                        echo gGeraAlertMsg('Operação OK', 'Recebimentos gerados com sucesso');

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
                    } else {
                        echo gGeraAlertMsg('Erro', $retorno);
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
        $botoesDir .= '<button type="submit" id="contratosClientesIncluir_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        break;

    case 'formGerarMensal':
        include 'prog/etc/etc008/grid_func-js.php';
        include 'bibliotecas/classes/SRV001.php';
        $SRV001 = new SRV001();
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
        $form_tabela = '';
        $form_campo_pk = '';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'contratosClientesIncluir_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/contratosClientes/contratosClientes/formGerarMensal/';

        // novo formulário
        $form = new Formulario('formContratosClientes');
        // Editor
        $form_editor = array(
            'geral' => '',
        );

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'Ano' => array('name' => 'Ano', 'label' => 'Ano', 'type' => 'text', 'reject' => true),
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

                    // Imprime uma mensagem e fecha a janela
                    //echo gGeraAlertMsg('Operação OK', 'Registro Incluido com Sucesso!');
                    $retorno = $SRV001->incluirContratosAnuais($form);

                    if ($retorno === true) {
                        echo gGeraAlertMsg('Operação OK', 'Recebimentos gerados com sucesso');

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
                    } else {
                        echo gGeraAlertMsg('Erro', $retorno);
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
        $botoesDir .= '<button type="submit" id="contratosClientesIncluir_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        break;

    case 'formAnual':
        include 'prog/etc/etc008/grid_func-js.php';
        include 'bibliotecas/classes/SRV001.php';
        $SRV001 = new SRV001();
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
        $form_tabela = 'T135';
        $form_campo_pk = 'T135_Id';
        // ID do botão que irá executar o envio desse formulário (o botão provavelmente foi criado dentro do content.php, use o mesmo ID de lá)
        $enviar_btnId = 'contratosClientesIncluir_btn';
        // Form que será executado ao clicar no botão
        $enviar_acaoHref = '/outros/contratosClientes/contratosClientes/form/';

        // novo formulário
        $form = new Formulario('formContratosClientes');
        // Editor
        $form_editor = array(
            'geral' => '{"T135_D024_Id":{"top":7,"left":158.859375,"width":249,"hidden":0},"T135_Data_Inicio":{"top":30,"left":175.59375,"width":120,"hidden":0},"T135_Data_Termino":{"top":53,"left":161.53125,"width":120,"hidden":0},"T135_Valor_Contrato":{"top":76,"left":177.015625,"width":120,"hidden":0},"T135_Dia_Vencimento":{"top":99,"left":157.859375,"width":120,"hidden":0},"T135_Tipo":{"top":122,"left":180.890625,"width":128,"hidden":0},"T135_Mes_Inicial":{"top":145,"left":-31.078125,"width":129,"hidden":1},"T135_Franquia_Horas":{"top":145,"left":126.0625,"width":120,"hidden":0},"T135_Valor_Hora_Tecnica":{"top":168,"left":108.546875,"width":120,"hidden":0},"T135_D032_Id":{"top":191,"left":171.3125,"width":127,"hidden":0},"T135_D014_Id":{"top":214,"left":172.53125,"width":127,"hidden":0},"T135_Qtde_Maxima_Melhorias_Mes":{"top":237,"left":27.640625,"width":120,"hidden":0},"T135_Qtde_Maxima_Chamados_Atendimento":{"top":260,"left":28.84375,"width":120,"hidden":0},"T135_Qtde_Maxima_Chamados_Duvida":{"top":283,"left":16,"width":120,"hidden":0},"T135_Numero_Parcelas":{"top":352,"left":224.0625,"width":129,"hidden":1}}',
        );

        $formArray = array(
            'campos' => array(
                'geral' => array(
                    'T135_D024_Id' => array('name' => 'T135_D024_Id', 'label' => 'Empresa', 'type' => 'pesquisa', 'pesquisa' => array('D024', 'D024_Id', 'D024_Nome_Empresa')),
                    'T135_Data_Inicio' => array('name' => 'T135_Data_Inicio', 'label' => 'Início', 'type' => 'data'),
                    'T135_Data_Termino' => array('name' => 'T135_Data_Termino', 'label' => 'Término', 'type' => 'data'),
                    'T135_Valor_Contrato' => array('name' => 'T135_Valor_Contrato', 'label' => 'Valor', 'type' => 'moeda'),
                    'T135_Tipo' => array('name' => 'T135_Tipo', 'label' => 'Tipo', 'type' => 'select', 'select' => array(
                            array('title' => 'Mensal', 'value' => 'M', 'selected' => true),
                            array('title' => 'Anual', 'value' => 'A'),
                        ),
                    ),
                    'T135_Dia_Vencimento' => array('name' => 'T135_Dia_Vencimento', 'label' => 'Dia Venc', 'type' => 'text'),
                    'T135_Mes_Inicial' => array('name' => 'T135_Mes_Inicial', 'label' => 'Mês Inic', 'type' => 'text'),
                    'T135_Numero_Parcelas' => array('name' => 'T135_Numero_Parcelas', 'label' => 'Nº Parcelas', 'type' => 'text'),
                    'T135_Franquia_Horas' => array('name' => 'T135_Franquia_Horas', 'label' => 'Franquia Horas', 'type' => 'tempo'),
                    'T135_Valor_Hora_Tecnica' => array('name' => 'T135_Valor_Hora_Tecnica', 'label' => 'Valor Hora Técnica', 'type' => 'moeda'),
                    'T135_D032_Id' => array('name' => 'T135_D032_Id', 'label' => 'Grupo', 'type' => 'select', 'extra' => "onchange=\"alteraConta(this, '{$g['divId']}')\"", 'select' =>
                        gGeraSelect('D032', 'D032_Id', 'D032_Descricao', 'WHERE D032_Flag_Ativo="S" ORDER BY D032_Descricao', true),
                    ),
                    'T135_D014_Id' => array('name' => 'T135_D014_Id', 'label' => 'Conta', 'type' => 'select', 'align' => 'right', 'style' => 'float:right', 'select' =>
                        gGeraSelect('D014', 'D014_Id', 'D014_SubConta', "WHERE D014_Flag_Ativo='S' ORDER BY D014_SubConta", true),
                    ),
                    'T135_Qtde_Maxima_Melhorias_Mes' => array('name' => 'T135_Qtde_Maxima_Melhorias_Mes', 'label' => 'Qtde máxima de melhorias por mês', 'type' => 'text'),
                    'T135_Qtde_Maxima_Chamados_Atendimento' => array('name' => 'T135_Qtde_Maxima_Chamados_Atendimento', 'label' => 'Qtde erros em atendimento por vez', 'type' => 'text'),
                    'T135_Qtde_Maxima_Chamados_Duvida' => array('name' => 'T135_Qtde_Maxima_Chamados_Duvida', 'label' => 'Qtde dúvidas em atendimento por vez', 'type' => 'text'),
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
                        $retorno = $SRV001->incluirContratos($form);
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
        $botoesDir .= '<button type="submit" id="contratosClientesIncluir_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\'' . $g['divId'] . '\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesId', $g['divId']);
        $g['smarty']->assign('botoesDir', $botoesDir);
        $g['smarty']->display('buttons-floatBottom.tpl');
        break;

    case 'marcarGerarContrato':

        $resposta = array('code' => true, 'data' => array());

        $T135_Id = isset($r_T135_Id) ? $r_T135_Id : false;
        $selected = isset($r_selected) ? $r_selected : false;

        mysql_query("UPDATE T135 SET T135_Flag_Gerar_Recebimento='{$selected}' WHERE T135_Id='{$T135_Id}'");

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
        break;

    case 'gerarContratosMensais':
        $resposta = array('code' => true, 'data' => array());
        require_once "bibliotecas/classes/SRV001.php";

        $SRV001 = new SRV001();
        $retorno = $SRV001->incluirContratosAnuais();

        if ($retorno === true) {
            $resposta['code'] = true;
            $resposta['data'] = "Recebimentos gerados com sucesso!";
        } else {
            $resposta['code'] = false;
            $resposta['data'] = $retorno;
        }

        echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
        die();
        break;
}

function validaCampoGerarContrato($array)
{
    $T135_Id = $array['todosCampos']['T135_Id'];
    $retorno = "<span style='width: 50px; display:block;'><input onclick=\"JSvalidaCampoGerarContrato(this, '{$T135_Id}', '{$g['divId']}')\" type='checkbox' id='T135_Flag_Gerar_Recebimento' title='Gerar Contrato?'";
    if ($array['todosCampos']['T135_Flag_Gerar_Recebimento'] == 'S') {
        $retorno .= "checked='checked'";
    }
    return $retorno .= " /></span>";
}

function retornaConta($array)
{
    $D014_SubConta = $array['todosCampos']['D014_SubConta'];

    return $D014_SubConta;
}

function retornaGrupo($array)
{
    $D032_Descricao = $array['todosCampos']['D032_Descricao'];

    return $D032_Descricao;
}

echo <<<EOT
<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

	function JSvalidaCampoGerarContrato(checkbox,T135_Id,divId) {
	    var selected;
	    if($(checkbox).is(':checked')) {
	        selected='S';
	    } else {
	        selected='N';
	    }
	    $.getJSON('/outros/contratosClientes/contratosClientes/marcarGerarContrato/?ajax=true&T135_Id=' + encodeURIComponent(T135_Id) + '&selected=' + selected + '&callback=?', function(request) {});
	}
} </script></div>
EOT;
