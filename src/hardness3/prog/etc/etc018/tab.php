<?php
namespace hardness;
/**
* Ações
*/
switch ($g['r'][3]) {
	case 'etc018TabCrontab':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc018/grid/etc018GridCrontab/', 'Crontab', $g['repassarParametros']),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

    case 'etc018TabCrontabUsuarios':
		// Definição das Tabs
		$tabs = array();
		$tabsList = array(
		// Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
			array(uniqid(), '/etc/etc018/grid/etc018GridCrontabUsuarios/', 'Processos Automáticos', $g['repassarParametros']),
		);

		// Processa Tabs
		echo gProcessaTabs($tabsList, $tabs);

		// Carrega Template
		$g['smarty']->assign('tabs', $tabs);
		$g['smarty']->display('tab.tpl');
	break;

    case 'etc018TabEditarCrontab':
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/etc/etc018/form/etc018FormEditarCrontab/', 'Editar Crontab', '&tab=geral' . $g['repassarParametros']),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
        break;

    case 'etc018TabLog':
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/etc/etc018/outro/etc018OutroLog/', 'Log', $g['repassarParametros']),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
        break;

    case 'etc018ContentPersonalizarCrontab':
        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/etc/etc018/grid/etc018ContentPersonalizarCrontab/', 'Crontab', $g['repassarParametros']),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');
        break;
}
