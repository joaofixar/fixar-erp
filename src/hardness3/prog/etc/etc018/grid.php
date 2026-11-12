<?php
namespace hardness;
require_once('prog/etc/etc018/grid_func-callback.php');

// Modo desenvolvedor é utilizado a tabela D183, no modo normal a C039 - na execução é feito o merge das duas
$tabela = $g['modoDesenvolvedor'] || $g['modoDesenvolvedorApp'] ? 'D183' : 'C039';

switch($g['r'][3]){
	case 'etc018GridCrontab':
		/**
		* Definição do Grid (Tabela)
		*/
		$grid = array(
            array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array($tabela . '_Id', $g['divId'])),
            array('colunaValor' => $tabela . '_Id', 'chavePrimaria' => true, 'colunaTitulo' => 'Id', 'colunaExibir' => true, 'style' => 'width:1%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => $tabela . '_Nome', 'colunaTitulo' => 'Nome', 'style' => 'width: 10%;'),
            array('colunaValor' => $tabela . '_Codigo', 'colunaTitulo' => 'Código', 'style' => 'width: 10%;'),
            array('colunaValor' => $tabela . '_Crontab', 'colunaTitulo' => 'Intervalo', 'style' => 'width: 5%;'),
            array('colunaValor' => $tabela . '_Crontab_Extra', 'colunaTitulo' => 'Extra', 'style' => 'width: 5%;'),
            array('colunaValor' => $tabela . '_Grupo_Execucao', 'colunaTitulo' => 'Grupo', 'style' => 'width: 5%;', 'colunaEditar' => true, 'colunaAlinhar' => 'center'),
            array('colunaValor' => $tabela . '_Ultima_Execucao', 'colunaTitulo' => 'Última execução', 'callback' => 'gCorrigeData', 'style' => 'width: 5%;'),
            array('colunaValor' => $tabela . '_Tempo_Execucao', 'colunaTitulo' => 'Tempo execução', 'style' => 'width: 5%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'Log', 'colunaTitulo' => 'Opções', 'colunaTipo' => "livre", 'callback' => 'etc018Opcoes'),
            array('colunaValor' => $tabela . '_Flag_Execucao', 'colunaTitulo' => 'Executando', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => 'S'),
        );

		/**
		* Definição da SQL
		*/
		$from = $tabela;

        $extra = '';
        if ($g['modoDesenvolvedorApp']) {
            $extra .= <<<EOT
WHERE D183_App_Id = '{$g['modoDesenvolvedorApp']}' AND D183_Desenvolvimento = 1
EOT;
        } else if ($g['modoDesenvolvedor']) {
            $extra .= <<<EOT
WHERE (D183_App_Id IS NULL OR D183_App_Id = '') 
EOT;
        } else {
            $grid[] = array('colunaValor' => 'C039_Uuid', 'colunaTitulo' => 'C039_Uuid', 'colunaExibir' => false);
            $grid[] = array('colunaValor' => 'D183_Uuid', 'colunaTitulo' => 'D183_Uuid', 'colunaExibir' => false);
            $extra .= <<<EOT
LEFT JOIN D183 ON C039_Uuid = D183_Uuid AND (D183_Desenvolvimento IS NULL OR D183_Desenvolvimento = 0)
EOT;
        }

		/**
		* Filtro
		*/
		$gridFiltro = array(
			$tabela . '_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
		);
		// Gera o filtro e acrescenta ao $extra
		$extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
		//$extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);

  		// Imprime o filtro do GRID (normalmente executado acima do GRID)
		echo $g['ui']->gridFiltroPrint($gridFiltro);

        function gridLinha($array) {
            global $g;
            if (!$g['modoDesenvolvedorApp'] && !$g['modoDesenvolvedor'] && !empty($array['todosCampos']['C039_Uuid']) AND empty($array['todosCampos']['D183_Uuid'])) {
                return 'opacity: 0.5;';
            }
        }

        // Botões
        $botoes = array(
            array('titulo' => 'Incluir', 'id' => 'etc018IncluirCrontab', 'janelaUrl' => '/etc/etc018/content/etc018ContentEditarCrontab/', 'janelaTamanho' => '[900,650]'),
        );
        if (!$g['modoDesenvolvedor'] && !$g['modoDesenvolvedorApp']) {
            $botoes[] = array('titulo' => 'Personalizar', 'id' => 'etc018PersonalizarCrontab', 'janelaUrl' => '/etc/etc018/content/etc018ContentPersonalizarCrontab/', 'janelaTamanho' => '[900,500]', 'janelaTitulo' => 'Crontab para personalizar');
        }
        echo Botoes::processar($botoes);

		/**
		* Geração: Monta o SQL e retorna o dados
		*/
		list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

		//Define ação do click de abertura (ao clicar em uma linha do grid)
		$novoIdTelaForm = uniqid();
		echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				acaoClickLinha('{$g['divId']}', function(data, array) {
                    // array['todosCampos'], array['divId'], array['divIdRoot'], array['divIdParent']
                    abrirJanela(data, array['divIdRoot'], array['divId'], unique(), '', 'Editar Crontab: ' + array['todosCampos']['{$tabela}_Id'], '/etc/etc018/content/etc018ContentEditarCrontab/', '&acaoId=' + encodeURIComponent(array['todosCampos']['{$tabela}_Id']), [980,650]);
                });
                function crontabExecutar(acaoId) {
                    var \$buttons = {
                        "Sim": function() {
                            $('#{$g['divId']}').showLoading();
                            $.getJSON('/etc/etc018/grid_func-ajax/executarCrontab/?ajax=true&tabela=' + encodeURIComponent('{$tabela}') + '&acaoId=' + encodeURIComponent(acaoId) + '&callback=?', function(request) {
                                $('#{$g['divId']}').hideLoading();
                                divRefresh('{$g['divId']}', true);
                                dialogConfirm('Relatório da execução:', '<div id="textareaCrontabExecutar" style="overflow: auto; height: 500px; width: 900px; padding: 0;"></div>');
                                setTimeout(function() {
                                    $('#textareaCrontabExecutar').html(request.data);
                                }, 200);
                            });
                            \$(this).dialog("destroy").remove();
                        },
                        "Cancelar": function() { \$(this).dialog("destroy").remove(); }
                    }
                    dialogConfirm('Confirmação', "Você deseja executar este crontab?", \$buttons);
                }
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

    case 'etc018ContentPersonalizarCrontab':
        $tabela = 'D183';
        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => "campoExclusao", 'colunaTipo' => "livre", 'callback' => 'gExcluirLinha', 'callbackParameter' => array($tabela . '_Id', $g['divId'])),
            array('colunaValor' => $tabela . '_Id', 'chavePrimaria' => true, 'colunaTitulo' => 'Id', 'style' => 'width:0%', 'colunaAlinhar' => 'center'),
            array('colunaValor' => $tabela . '_Nome', 'colunaTitulo' => 'Nome', 'style' => 'width: 10%;'),
            array('colunaValor' => $tabela . '_App_Id', 'colunaTitulo' => 'App', 'style' => 'width: 10%;', 'callback' => 'etc018NomeApp'),
            array('colunaValor' => $tabela . '_Codigo', 'colunaTitulo' => 'Código', 'style' => 'width: 10%;'),
            array('colunaValor' => $tabela . '_Crontab', 'colunaTitulo' => 'Intervalo', 'style' => 'width: 5%;'),
            array('colunaValor' => $tabela . '_Crontab_Extra', 'colunaTitulo' => 'Extra', 'style' => 'width: 5%;'),
            array('colunaValor' => $tabela . '_Ultima_Execucao', 'colunaTitulo' => 'Última execução', 'callback' => 'gCorrigeData', 'style' => 'width: 5%;'),
            array('colunaValor' => $tabela . '_Grupo_Execucao', 'colunaTitulo' => 'Grupo', 'style' => 'width: 5%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => $tabela . '_Tempo_Execucao', 'colunaTitulo' => 'Tempo execução', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center'),
            array('colunaValor' => 'Log', 'colunaTitulo' => 'Opções', 'colunaTipo' => "livre", 'callback' => 'etc018Opcoes'),
            array('colunaValor' => $tabela . '_Flag_Execucao', 'colunaTitulo' => 'Executando', 'style' => 'width: 1%;', 'colunaAlinhar' => 'center', 'colunaEditarOculto' => 'S'),
        );

        /**
         * Definição da SQL
         */
        $from = $tabela;

        $extra = <<<EOT
LEFT JOIN C039 ON C039_Uuid = D183_Uuid
WHERE
    C039_Id IS NULL
    AND (D183_Desenvolvimento IS NULL OR D183_Desenvolvimento = 0)
EOT;

        /**
         * Filtro
         */
        $gridFiltro = array(
            $tabela . '_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
        );
        // Gera o filtro e acrescenta ao $extra
        $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
        //$extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);
        // Imprime o filtro do GRID (normalmente executado acima do GRID)
        echo $g['ui']->gridFiltroPrint($gridFiltro);

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra);

        //Define ação do click de abertura (ao clicar em uma linha do grid)
        $novoIdTelaForm = uniqid();
        echo <<<EOT
			<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
				acaoClickLinha('{$g['divId']}', function(data, array) {
                    // array['todosCampos'], array['divId'], array['divIdRoot'], array['divIdParent']
                    $.ajax({
                        url: '/etc/etc018/grid_func-ajax/personalizarCrontab/',
                        dataType: 'jsonp',
                        data: 'ajax=true&acaoId=' + encodeURIComponent(array['todosCampos']['D183_Id']) + '&callback=?',
                        type: 'get',
                        success: function(request) {
                            $("#{$g['divId']}").closest('.ui-dialog-content').bind( "dialogclose", function(data, ui) {
                                setTimeout(function() {
                                    divRefresh('{$g['divIdParent']}', true);
                                }, 150)
                            });
                            $("#{$g['divId']}").closest('.ui-dialog-content').dialog('close');
                        }
                    });
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
        if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
        break;

        case 'etc018GridCrontabUsuarios':

            /**
             * Definição do Grid (Tabela)
             */
            $grid = array(
                array('colunaValor' => 'if(C039_Id > 0, C039_Id, D183_Id) as C039_Id', 'chavePrimaria' => true, 'colunaTitulo' => 'Id', 'style' => 'width:0%', 'colunaAlinhar' => 'center'),
                array('colunaValor' => 'if(C039_Id > 0, C039_Nome, D183_Nome)', 'colunaTitulo' => 'Nome', 'style' => 'width: 10%;'),
                array('colunaValor' => 'if(C039_Id > 0, C039_Crontab, D183_Crontab)', 'colunaTitulo' => 'Intervalo', 'style' => 'width: 5%;'),
                array('colunaValor' => 'if(C039_Id > 0, C039_Crontab_Extra, D183_Crontab_Extra)', 'colunaTitulo' => 'Extra', 'style' => 'width: 5%;'),
                array('colunaValor' => 'if(C039_Id > 0, C039_Ultima_Execucao, D183_Ultima_Execucao)', 'colunaTitulo' => 'Última execução', 'callback' => 'gCorrigeData', 'style' => 'width: 5%;'),
                array('colunaValor' => 'Log', 'colunaTitulo' => 'Opções', 'colunaTipo' => "livre", 'callback' => 'etc018Opcoes'),
            );
     
            /**
             * Definição da SQL
             */
            $from = 'D183';
    
            $extra = <<<EOT
                LEFT JOIN C039 ON C039_Uuid = D183_Uuid 
    EOT;
    
            /**
             * Filtro
             */
/*             $gridFiltro = array(
                $tabela . '_Nome' => array('titulo' => 'Nome', 'tipo' => 'texto'),
            ); */
            // Gera o filtro e acrescenta ao $extra
            $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra);
            //$extraTotalAberto = $g['ui']->gridFiltroExtra(false, $gridFiltro, $extraTotalAberto, false, false, true);
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
            if (isset($r_generateHTML)) { ob_end_clean(); ob_start(); $g['smarty']->display('gridHTML.tpl'); } else { $g['smarty']->display('grid.tpl'); }
            break;
}
