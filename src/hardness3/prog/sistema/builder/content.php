<?php
namespace hardness;
switch ($g['r'][3]) {
    /**
     * Grid Principal
     */
    case 'builder':
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
            array($content1, '/sistema/builder/tab/builder/', '&builderProjeto=' . urlencode($r_builderProjeto) . '&builderGrid=' . urlencode($r_builderGrid))
        );
        echo gProcessaAcoes($actions);
        break;

    case 'builderForm':
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
            array($content1, '/sistema/builder/tab/builderForm/', '&acao=' . urlencode($r_acao) . '&acaoId=' . urlencode($r_acaoId) . '&builderProjeto=' . urlencode($r_builderProjeto) . '&builderForm=' . urlencode($r_builderForm))
        );
        echo gProcessaAcoes($actions);
        break;
}
