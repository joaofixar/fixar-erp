<?php
namespace hardness;

// Carrega funções callback
//require_once('prog/cad/cad016/grid_func-callback.php');
require_once('prog/cad/cad019/grid_func-callback.php');
require_once('prog/cad/cad019/grid_func-js.php');
require_once('prog/cad/cad051/form_func-js.php');

switch ($g['r'][3]) {
	/**
	* @author Tiago Fischer
	* @since 13/04/2012
	*/
	case 'cad019GridLocacao':
        /**
        * Definição do Grid (Tabela)
        */
        mysql_query("SET SESSION group_concat_max_len = 10000;");
        $grid = array(
            array('colunaValor' => 'excluirLinha', 'colunaTipo' => 'livre', 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D004_Id', $g['divId'])),
            array('colunaValor' => 'impressaoEtiqueta', 'colunaTipo' => 'livre', 'callback' => 'abrirNotificacao', 'callbackParameter' => array('Imprimir Etiquetas', '/sistema/impressao/etiquetas/locacao/', 'D004_Id', 'right', false, '', '<span class="ui-icon ui-icon-tag"></span>', 370), 'style' => 'width: 1%;', 'colunaExibir' => false),
            array('colunaValor' => 'D004_Flag_Imprimir_Etiqueta', 'colunaTitulo' => 'Imprimir', 'callback' => 'validaCampoImprimir', 'style' => 'width:1%', 'colunaExibir' => false),
            array('colunaValor' => 'D004_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Local'),
            array('colunaValor' => 'D004_Flag_Somar_Estoque_Fisico', 'colunaTitulo' => 'Somar Estoque'),
            array('colunaValor' => 'D004_Descricao', 'colunaTitulo' => 'Descricao', 'colunaEditarOculto' => true, 'colunaEditarTodos' => true),
            array('colunaValor' => 'D004_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'callback' => 'tipoLocacao', 'colunaExibir' => false),
            array('colunaValor' => 'D146_Descricao', 'colunaTitulo' => 'Armazém'),
            array('colunaValor' => 'group_concat(concat(D001_Codigo_Produto,\' - \',D001_Descricao_Produto,\' - Qte: \',T066.T066_Quantidade_Estoque) SEPARATOR \'<br>\')', 'colunaTitulo' => 'produtos'),
        );

        /**
        * Definição da SQL
        */
        $from = 'D004';
        $extra = <<<EOT
            LEFT JOIN D146 ON D146_Id = D004_D146_Id
            LEFT JOIN T066 on T066_D004_Id=D004_Id
            LEFT JOIN D009 on D009_Id=T066_D009_Id 
            LEFT JOIN D049 on D049_Id=D009_D049_Id
            LEFT JOIN D001 on D001_Id=D049_D001_Id
            WHERE IFNULL(D009_C004_Id,'{$g['empresaAtual']}')='{$g['empresaAtual']}'
            AND D004_C004_Id = '{$g['empresaAtual']}'
            GROUP BY D004_Id
EOT;


        /**
        * Botões (Versão Acima do GRID)
        */
        echo <<<EOT
            <div class="buttonsBottomTop">
                <!--<button type="button" id="cad019FormCadastroLocacoesBtnIncluir">Incluir</button>-->
                <!--<button type="button" id="cad019FormCadastroLocacoesBtnImprimir">Imprimir</button>-->
                <!--<button type="button" id="cad019ImprimirEtiquetas">Imprimir Etiquetas</button>
                <button type="button" id="cad019DesmarcarTodos">Desmarcar Todos</button>-->
            </div>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#cad019FormCadastroLocacoesBtnIncluir').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Locações', '/cad/cad019/content/cad019ContentCadastroLocacoes/', '&tab=geral', [500,300]);
                });
                $('#cad019ImprimirEtiquetas').unbind('click').bind('click', function(data) {
                    $.getJSON('/cad/cad019/grid_func-ajax/cad019VerificaProdutosImprimir/?ajax=true&callback=?', function(request) {
                        if(request.code){
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imprimir Etiquetas', '/cad/cad019/content/cad019ContentImprimirEtiquetas/', '', [800,500]);
                        }else{
                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                            dialogConfirm('Erro', 'Não existem locações selecionados para impressão', \$buttons);
                        }
                    });
                });
                $('#cad019DesmarcarTodos').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/cad/cad019/grid_func-ajax/cad019DesmarcarImprimir/?ajax=true&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        divRefresh('{$g['divId']}', true);
                    });
                });
            } </script></div>
EOT;
        
        /**
        * Filtro
        */
        $gridFiltro = array(
            'D004_Id' => array('titulo' => 'ID', 'tipo' => 'numero'),
            'D004_Local' => array('titulo' => 'Local', 'tipo' => 'texto'),
            'D004_Flag_Somar_Estoque_Fisico' => array('titulo' => 'Somar Estoque', 'tipo' => 'select', 'select' => array(
                    array('title' => '', 'value' => '', 'selected' => true),
                    array('title' => 'Sim', 'value' => 'S'),
                    array('title' => 'Não', 'value' => 'N'),
                ),
            ),
            'D004_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
            'D146_Descricao' => array('titulo' => 'Armazem', 'tipo' => 'texto'),
            "ifnull(D001_Codigo_Produto[[VIRGULA]]''),ifnull(D001_Descricao_Produto[[VIRGULA]]'')" => array('titulo' => 'Produto', 'tipo' => 'texto'),
            'T066_Quantidade_Estoque' => array('titulo' => 'Estoque', 'tipo' => 'numero'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        /**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad019FormCadastroLocacoesBtnIncluir',  'exibirCallback' => 'return isset($g["c029Ids"][262]);'),
		);

		echo Botoes::processar($botoes);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Totais Almir
         */
        $totais = array(
            // sql
            array('nome' => 'Count', 'titulo' => 'Registros', 'totalizador' => 'count', 'callbackParameter' => 0),
        );

        
        /**
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $sql_ = urlencode(base64_encode($geradoSql));
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                var dbclickPrevent = false;
                $('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
                    // Previne duplo click (abrir duas janelas)
                    if (dbclickPrevent) { return false; }
                    dbclickPrevent = true;
                    setTimeout(function() { dbclickPrevent = false; }, 500);
                    // (substituir pelo mesmo ID da chave primária do grid)
                    var chavePrimaria = 'D004_Id';    
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace(chavePrimaria + '-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Locações - ID: ' + acaoId, '/cad/cad019/content/cad019ContentCadastroLocacoes/', '&tabela=D004&acaoId=' + encodeURIComponent(acaoId), [500,250]);
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
            echo <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    /*
                    $('#cad019FormCadastroLocacoesBtnImprimir').unbind('click').bind('click', function(data) {
                        var \$buttons = {
                            "Sim": function() {
                                $('#{$g['divId']}').showLoading();
                                $.getJSON('/cad/cad019/grid_func-ajax/cad019CheckImpLoc/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
                                    $('#{$g['divId']}').hideLoading();
                                    if (request.code) {
                                        var \$buttons = {
                                                "Sim": function() {
                                                    var diaThis = $(this);
                                                    $('#{$g['divId']}').showLoading();
                                                    $.getJSON('/cad/cad019/grid_func-ajax/impTag/?ajax=true&extra={$sql_}&callback=?', function(request) {
                                                        $('#{$g['divId']}').hideLoading();
                                                        diaThis.dialog("destroy").remove();
                                                        if (request.code) {
                                                            impTags(request.data);
                                                        }
                                                    });
                                                },
                                                "Não": function() { $(this).dialog("destroy").remove(); } 
                                            };
                                            dialogConfirm('Confirmação', request.data, \$buttons);
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
                        dialogConfirm('Gerar Cotação', 'Deseja imprimir todas as etiquetas listadas?', \$buttons);
                    });
                    */
                } </script></div>
EOT;
        }
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
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;
    
    case 'cad019GridLocacoesImprimir':
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => 'D004_Flag_Imprimir_Etiqueta', 'colunaTitulo' => 'Imprimir', 'callback' => 'validaCampoImprimir', 'style' => 'width:1%'),
            array('colunaValor' => 'D004_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
            array('colunaValor' => 'D004_Local', 'colunaTitulo' => 'Local'),
            array('colunaValor' => 'D004_Descricao', 'colunaTitulo' => 'Descricao'),
            array('colunaValor' => 'D004_Flag_Tipo', 'colunaTitulo' => 'Tipo', 'callback' => 'tipoLocacao'),
        );

        /**
        * Definição da SQL
        */
        $from = 'D004';
        $extra = <<<EOT
            WHERE D004_Flag_Imprimir_Etiqueta='S'
EOT;

        /*
        * Botões (Versão Acima do GRID)
        
        echo <<<EOT
            <div class="buttonsBottomTop">
                <button type="button" id="cad019FormCadastroLocacoesBtnIncluir">Incluir</button>
                <!--<button type="button" id="cad019FormCadastroLocacoesBtnImprimir">Imprimir</button>-->
                <button type="button" id="cad019ImprimirEtiquetas">Imprimir Etiquetas</button>
                <button type="button" id="cad019DesmarcarTodos">Desmarcar Todos</button>
            </div>
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#cad019FormCadastroLocacoesBtnIncluir').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Locações', '/cad/cad019/content/cad019ContentCadastroLocacoes/', '&tab=geral', [500,250]);
                });
                $('#cad019ImprimirEtiquetas').unbind('click').bind('click', function(data) {
                    $.getJSON('/cad/cad019/grid_func-ajax/cad019VerificaProdutosImprimir/?ajax=true&callback=?', function(request) {
                        if(request.code){
                            abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Imprimir Etiquetas', '/cad/cad019/content/cad019ContentImprimirEtiquetas/', '', [800,500]);
                        }else{
                            var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
                            dialogConfirm('Erro', 'Não existem produtos selecionados para impressão', \$buttons);
                        }
                    });
                });
                $('#cad019DesmarcarTodos').unbind('click').bind('click', function(data) {
                    $('#{$g['divId']}').showLoading();
                    $.getJSON('/cad/cad019/grid_func-ajax/cad019DesmarcarImprimir/?ajax=true&callback=?', function(request) {
                        $('#{$g['divId']}').hideLoading();
                        divRefresh('{$g['divId']}', true);
                    });
                });
            } </script></div>
EOT;
        
        /*
        * Filtro
        
        $gridFiltro = array(
            'D004_Id' => array('titulo' => 'ID', 'tipo' => 'numero'),
            'D004_Local' => array('titulo' => 'Local', 'tipo' => 'texto'),
            'D004_Descricao' => array('titulo' => 'Descrição', 'tipo' => 'texto'),
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
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        
        $sql_ = urlencode(base64_encode($geradoSql));

        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                var dbclickPrevent = false;
                $('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
                    // Previne duplo click (abrir duas janelas)
                    if (dbclickPrevent) { return false; }
                    dbclickPrevent = true;
                    setTimeout(function() { dbclickPrevent = false; }, 500);
                    // (substituir pelo mesmo ID da chave primária do grid)
                    var chavePrimaria = 'D004_Id';
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace(chavePrimaria + '-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Locações - ID: ' + acaoId, '/cad/cad019/content/cad019ContentCadastroLocacoes/', '&acaoId=' + encodeURIComponent(acaoId), [500,250]);
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
            } </script></div>
EOT;

        // Não executar quando houver o refresh de linha
        if (empty($r_linhaGridId)) {
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                /*
                $('#cad019FormCadastroLocacoesBtnImprimir').unbind('click').bind('click', function(data) {
                    var \$buttons = {
                        "Sim": function() {
                            $('#{$g['divId']}').showLoading();
                            $.getJSON('/cad/cad019/grid_func-ajax/cad019CheckImpLoc/?ajax=true&extra={$sql_}&operacao=1&callback=?', function(request) {
                                $('#{$g['divId']}').hideLoading();
                                if (request.code) {
                                    var \$buttons = {
                                            "Sim": function() {
                                                var diaThis = $(this);
                                                $('#{$g['divId']}').showLoading();
                                                $.getJSON('/cad/cad019/grid_func-ajax/impTag/?ajax=true&extra={$sql_}&callback=?', function(request) {
                                                    $('#{$g['divId']}').hideLoading();
                                                    diaThis.dialog("destroy").remove();
                                                    if (request.code) {
                                                        impTags(request.data);
                                                    }
                                                });
                                            },
                                            "Não": function() { $(this).dialog("destroy").remove(); } 
                                        };
                                        dialogConfirm('Confirmação', request.data, \$buttons);
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
                    dialogConfirm('Gerar Cotação', 'Deseja imprimir todas as etiquetas listadas?', \$buttons);
                });
                
            } </script></div>
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
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
    break;

    case 'cad019GridArmazem':
        /**
        * Definição do Grid (Tabela)
        */
        $grid = array(
            array('colunaValor' => "excluirLinha", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array('D146_Id', $g['divId'])),
            array('colunaValor' => 'D146_Id', 'colunaTitulo' => 'Id', 'chavePrimaria' => true, 'style' => 'width: 1%'),
            array('colunaValor' => 'D146_Descricao', 'colunaTitulo' => 'Armazém'),
            array('colunaValor' => 'D146_Flag_Principal', 'colunaTitulo' => 'Principal', 'colunaEditar' => true, 'colunaEditarTipo' => 'flag', 'colunaEditarExtra' => 'S|N'),
        );

        /**
        * Definição da SQL
        */
        $from = 'D146';
        $extra = <<<EOT
EOT;
        /**
         * Botões Almir
         */
        $botoes = array(
			array('titulo' => 'Incluir', 'id' => 'cad019FormCadastroArmazemBtnIncluir'),
			);
		echo Botoes::processar($botoes);

        /**
        * Botões (Versão Acima do GRID)
        */
        
            /*<div class="buttonsBottomTop">
                <button type="button" id="cad019FormCadastroArmazemBtnIncluir">Incluir</button>
            </div>*/
            echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                $('#cad019FormCadastroArmazemBtnIncluir').unbind('click').bind('click', function(data) {
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cadastro Armazém', '/cad/cad019/content/cad019ContentCadastroArmazem/', '&tab=geral', [450,90]);
                });
            } </script></div>
EOT;
        
        /**
        * Filtro
        */
        $gridFiltro = array(
            'D146_Descricao' => array('titulo' => 'Armazém', 'tipo' => 'texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

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
        * Define ação do click de abertura (ao clicar em uma linha do grid)
        */
        $sql_ = urlencode(base64_encode($geradoSql));
        echo <<<EOT
            <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                var dbclickPrevent = false;
                $('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
                    // Previne duplo click (abrir duas janelas)
                    if (dbclickPrevent) { return false; }
                    dbclickPrevent = true;
                    setTimeout(function() { dbclickPrevent = false; }, 500);
                    // (substituir pelo mesmo ID da chave primária do grid)   
                    if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                    var id = $(this).closest('tr').attr('id').split('|');
                    var acaoId = id[0].replace('D146_Id-', '');
                    // Ação: abrir nova janela
                    abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Editar Armazém', '/cad/cad019/content/cad019ContentCadastroArmazem/', '&acaoId=' + encodeURIComponent(acaoId), [450,90]);
                });
                $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }

    break;
}

?>
