<?php
namespace hardness;

switch ($g['r'][3]) {
    case 'builder':
        // Recupera grid construido com o Fast Crud
        $projeto = json_decode(file_get_contents("{$g['pathDados']}/builder/{$r_builderProjeto}.json"), true);
        $data = $projeto['grids'][$r_builderGrid];

        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/sistema/builder/grid/builder/', $data['nome'], '&builderProjeto=' . urlencode($r_builderProjeto) . '&builderGrid=' . urlencode($r_builderGrid))
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
        break;

    case 'builderForm':
        // Recupera grid construido com o Fast Crud
        $projeto = json_decode(file_get_contents("{$g['pathDados']}/builder/{$r_builderProjeto}.json"), true);
        $data = $projeto['forms'][$r_builderForm];

        // Form (caso seja o mesmo form em multiplas tabs)
        $formId = uniqid();
        $g['smarty']->assign('formId', $formId);

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/sistema/builder/form/builder/', $data['nome'], '&tab=cadastro&acaoId=' . urlencode($_REQUEST['acaoId']) . '&builderProjeto=' . urlencode($r_builderProjeto) . '&builderForm=' . urlencode($r_builderForm) . '&formId=' . urlencode($formId))
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
        break;
}
