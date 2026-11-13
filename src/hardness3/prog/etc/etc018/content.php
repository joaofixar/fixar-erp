<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'etc018ContentCrontab':
		global $g;
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
			array($content1, '/etc/etc018/tab/etc018TabCrontab/', $g['repassarParametros'])
		);
		echo gProcessaAcoes($actions);
	break;

    case 'etc018ContentCrontabUsuarios':
		global $g;
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
			array($content1, '/etc/etc018/tab/etc018TabCrontabUsuarios/', $g['repassarParametros'])
		);
		echo gProcessaAcoes($actions);
	break;

    case 'etc018ContentEditarCrontab':
        global $g;
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
            array($content1, '/etc/etc018/tab/etc018TabEditarCrontab/', $g['repassarParametros'])
        );
        echo gProcessaAcoes($actions);

        /**
         * Botões
         */
        // Definição dos botões
        $botoesDir = '<button type="submit" id="etc018FormEditarCrontab_btn">Enviar</button>';
        $botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\');" class="btnFechar">Cancelar</button>';
        $g['smarty']->assign('botoesEsq', $botoesEsq);
        $g['smarty']->assign('botoesDir', $botoesDir);
        echo  $g['smarty']->fetch('buttons-floatBottom.tpl');
        break;

    case 'etc018ContentLog':
        global $g;
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
            array($content1, '/etc/etc018/tab/etc018TabLog/', $g['repassarParametros'])
        );
        echo gProcessaAcoes($actions);
        break;

    case 'etc018ContentPersonalizarCrontab':
        global $g;
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
            array($content1, '/etc/etc018/tab/etc018ContentPersonalizarCrontab/', $g['repassarParametros'])
        );
        echo gProcessaAcoes($actions);
        break;
}
