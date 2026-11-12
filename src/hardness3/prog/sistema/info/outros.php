<?php
namespace hardness;

/**
* Ações
*/
switch ($g['r'][3]) {

	case 'notifcaMelhoria':

        $idMelhoria = $g['C007']['C007_Id_Melhoria_Notificada'] + 1;
        $idMelhoria = str_pad($idMelhoria, 5, "0", STR_PAD_LEFT);

		if (file_exists("static/melhorias/notificacao_{$idMelhoria}.php")) {
			mysql_query("UPDATE C007 SET C007_Id_Melhoria_Notificada = '{$idMelhoria}' WHERE C007_Id = '{$g['usuarioAtual']}'");
		}

		$arquivo = "static/melhorias/notificacao_{$idMelhoria}.php";
        if (file_exists($arquivo)) {
            require_once($arquivo);
        }

		/**
		 * Botões
		 */
		// Definição dos botões
		$botoesMid = '';
		$botoesEsq = '';
		$botoesDir = '';
		$botoesDir .= '<button onclick="fecharJanela(\''.$g['divId'].'\'); return false;" class="btnFechar">Fechar</button>';
		$g['smarty']->assign('botoesId', $g['divId']);
		$g['smarty']->assign('botoesEsq', $botoesEsq);
		$g['smarty']->assign('botoesMid', $botoesMid);
		$g['smarty']->assign('botoesDir', $botoesDir);
		$g['smarty']->display('buttons-floatBottom.tpl');
	break;

}
?>