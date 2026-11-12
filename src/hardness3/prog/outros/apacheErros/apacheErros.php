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
            array($content1, '/outros/apacheErros/apacheErros/tab/'),
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
            array(uniqid(), '/outros/apacheErros/apacheErros/outros/', 'Log erros PHP'),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');

    break;

    case 'outros':
        global $confUsuario,$g;

        $subDominio = $_SERVER['HTTP_HOST'] == 'localhost' ? $_SERVER['HTTP_HOST'] : substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], "."));
        $dominio    = $_SERVER['HTTP_HOST'] == 'localhost' ? $_SERVER['HTTP_HOST'] : substr($_SERVER['HTTP_HOST'], strpos($_SERVER['HTTP_HOST'], ".")+1);

        ob_start();

        $dirLog = "./dados_usuarios/log/";
        is_dir($dirLog) OR mkdir($dirLog, 0777, true);
        //passthru("tail -n 15 {$dirLog}{$subDominio}.{$dominio}-error.log");
        passthru("tail -n 15 {$dirLog}{$subDominio}.{$dominio}-error.log");

        $log = ob_get_contents();

        ob_end_clean();

        $dia = substr(date('l'),0,3);

        $log = explode('['.$dia,$log);

        unset($log[0]);

        krsort($log);

        foreach ($log as $key => $row){
                echo "<p style='font-size:14px;border-bottom:1px solid silver;padding:10px'>[".$dia.$row."</p>";
        }

        break;

}

