<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
    case 'etc020TabEnviarEmail':
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/etc/etc020/form/etc020FormEnviarEmail/', 'E-mail', '&tab=geral' . $g['repassarParametros']),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
        break;
}
