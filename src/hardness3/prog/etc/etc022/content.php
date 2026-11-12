<?php 

namespace hardness;

switch ($g['r'][3]) {

    case 'etc022ContentPrincipal':

        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');

        $actions = array(
            array($content1, '/etc/etc022/outros/codigoBarra/', "&acaoId=" . urlencode($r_acaoId))
        );

        echo gProcessaAcoes($actions, false);

    break;
}