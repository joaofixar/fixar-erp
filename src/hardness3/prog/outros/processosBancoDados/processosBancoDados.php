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
            array($content1, '/outros/processosBancoDados/processosBancoDados/tab/'),
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
            array(uniqid(), '/outros/processosBancoDados/processosBancoDados/outros/', 'Processos Banco Dados'),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');

    break;

    case 'outros':
        global $confUsuario,$g;

        $link = "showprocess/processosBancoCliente.php";

        header("Refresh: 1");

        $dbDatabase = md5($confUsuario['dbDatabase']);
        $urlRaiz = stristr($confUsuario['pathRaiz'],"hardness3",true);
        $link = $urlRaiz.$link;
        require_once ($link);
       
        break;

}

