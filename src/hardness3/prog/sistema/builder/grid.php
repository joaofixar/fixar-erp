<?php
namespace hardness;

switch($g['r'][3]) {
    /**
     * Grid Principal
     */
    case 'builder':
        // Recupera grid construido com o Fast Crud
        $projeto = json_decode(file_get_contents("{$g['pathDados']}/builder/{$r_builderProjeto}.json"), true);
        $data = $projeto['grids'][$r_builderGrid];

        $tabelaPrimaria = $data["tabelas"][0];

        /**
         * Definição do Grid (Tabela)
         */
        $grid = array(
            array('colunaValor' => $tabelaPrimaria . "_Id", 'colunaTitulo' => 'ID', 'chavePrimaria' => true, 'colunaExibir' => false),
        );
        foreach ($data["grid"] as $item) {
            $gridItem = array(
                'colunaValor' => $item["campo"],
                'colunaTitulo' => $item["titulo"],
            );
            foreach (explode(',', $item["opcoes"]) as $opcao) {
                if ($opcao == "colunaEditar") {
                    $gridItem['colunaEditar'] = true;
                } else if ($opcao == "colunaExibirTitulo") {
                    $gridItem['colunaExibirTitulo'] = false;
                } else if ($opcao == "colunaExibir") {
                    $gridItem['colunaExibir'] = false;
                }
            }
            if ($item["tipo"] == "Data" || $item["tipo"] == "DataHora") {
                $gridItem['callback'] = 'gCorrigeData';
            }
            $grid[] = $gridItem;
        }

        /**
         * Definição da SQL
         */
        $from = $tabelaPrimaria;
        $extra = '';
        $ultimaTabela = '';
        foreach ($data["tabelas"] as $key => $tabela) {
            if (!$ultimaTabela) {
                $ultimaTabela = $tabela;
            } else {
                // TODO: somente irá funcionar para IDs com a nomenclatura padrão (T001_D024_Id)
                // TODO: corrigir (também no Fast Crud) para funcionar com outros padrões (Percentual_Desconto_X001_Id)
                $tabelaJoin = $data["tabelasJoin"][$key] ? $data["tabelasJoin"][$key] : $ultimaTabela;
                $extra .= "\n\t LEFT JOIN {$tabela} ON {$tabela}_Id={$tabelaJoin}_{$tabela}_Id \n";
            }
        }

        // Extra
        if (!empty($data["extra"])) {
            $extra .= "\n" . $data["extra"];
        }

        /**
         * Botões (Versão Acima do GRID)
         */
        if ($data["form"]) {
            echo <<<EOT
                <div class="buttonsBottomTop">
                    <button type="button" id="builder_btn_incluir_{$data["form"]}">Incluir</button>
                </div>
    
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
                    $('#builder_btn_incluir_{$data["form"]}').unbind('click').bind('click', function(data) {
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Incluir {$data["nome"]}', '/sistema/builder/content/builderForm/', '&builderProjeto={$r_builderProjeto}&builderForm={$data["form"]}', [550,550]);
                    });
                } </script></div>
EOT;
        }

        /**
         * Filtro
         */
        $gridFiltro = [];
        foreach ($data["grid"] as $item) {
            if (in_array('filtro', explode(',', $item["opcoes"]))) {
                $tipo = "texto";
                switch ($item["titulo"]) {
                    case "Data":
                    case "DataHora":
                        $tipo = "data";
                        break;
                    case "Inteiro":
                    case "Decimal":
                        $tipo = "numero";
                        break;
                }
                $gridFiltro[$item["campo"]] = [
                    "titulo" => $item["titulo"],
                    "tipo" => $tipo,
                ];
            }
        }
        if (!empty($gridFiltro)) {
            // Gera o filtro e acrescenta ao $extra
            $extra = $g['ui']->gridFiltroExtra(true, $gridFiltro, $extra, 'builder-' . $r_builderGrid);
            // Imprime o filtro do GRID (normalmente executado acima do GRID)
            echo $g['ui']->gridFiltroPrint($gridFiltro, 'builder-' . $r_builderGrid);
        }

        // TODO: click na linha?

        /**
         * Geração: Monta o SQL e retorna o dados
         */
        list($geradoSql, $geradoDados) = $g['sqlAuto']->gerarSQLGrid($grid, $from, $extra, 0.0, 'builder-' . $r_builderGrid);

        /**
         * Define ação do click de abertura (ao clicar em uma linha do grid)
         */
        if ($data["form"]) {
            echo <<<EOT
                <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
                    var dbclickPrevent = false;
                    $('#{$g['divId']} .grid tr').unbind('click').unbind('click').bind('click', function(data) {
                        // Previne duplo click (abrir duas janelas)
                        if (dbclickPrevent) { return false; }
                        dbclickPrevent = true;
                        setTimeout(function() { dbclickPrevent = false; }, 500);
                        // (substituir pelo mesmo ID da chave primária do grid)
                        var chavePrimaria = '{$data["tabelas"][0]}_Id';	
                        if (data.target.nodeName != 'TD' && data.target.nodeName != 'TR') { return true; }
                        var id = $(this).closest('tr').attr('id').split('|');
                        var acaoId = id[0].replace(chavePrimaria + '-', '');
                        // Ação: abrir nova janela
                        abrirJanela(data, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Alterar {$data["nome"]}: ' + acaoId, '/sistema/builder/content/builderForm/', '&acao=alterar' + '&builderProjeto=' + encodeURIComponent('{$r_builderProjeto}') + '&builderForm=' + encodeURIComponent('{$data["form"]}') + '&acaoId=' + encodeURIComponent(acaoId), [600,300]);
                    });
                    $('#{$g['divId']} .grid .campoEditavel').unbind('click').bind('click', function(event) { return false; });
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
}
