<?php
namespace hardness;
/**
 * Ações
 */
switch($g['r'][3]){
	case 'cad040OutroCertificado':
		// Qual template usar?
		$docNome = 'certificadoDeConformidade.tpl';
		
		// Classes
		include 'bibliotecas/classes/CAD040.php';
		include 'bibliotecas/classes/CAD044.php';
		include 'bibliotecas/classes/CAD008.php';
		$CAD040 = new CAD040();
		$CAD044 = new CAD044();
		$CAD008 = new CAD008();
		
		$D125_Id = isset($r_acaoId) ? $r_acaoId : false ;
		
		// Atribuir os elementos do template (veja documentação dentro do próprio template)
		$g['smarty']->assign('cabecalho', $CAD044->getVar('CABECALHO'));
		$g['smarty']->assign('titulo', $CAD044->getVar('CERT_CONFORMIDADE_TITULO'));
		$g['smarty']->assign('texto', $CAD044->getVar('CERT_CONFORMIDADE_TEXTO_BALSA'));
		$g['smarty']->assign('especificacoes', $CAD008->docGerarEspecificacoes($r_acaoId));
		$g['smarty']->assign('checklist', $CAD040->docGerarChecklist($r_acaoId,$CAD044->getVar('CERT_CONFORMIDADE_TEXTO4')));
		$g['smarty']->assign('cilindrosGas', $CAD040->docGerarCilindrosGas($r_acaoId, $CAD044->getVar('CERT_CONFORMIDADE_TEXTO2'), 
																					  $CAD044->getVar('CERT_CONFORMIDADE_TEXTO3')));
		$g['smarty']->assign('importante', sprintf($CAD044->getVar('CERT_CONFORMIDADE_TEXTO_IMPORTANTE'),date("d").' de '.gRetornaMes(date("m")).' de '.date("Y")));
		$g['smarty']->assign('texto2', $CAD044->getVar('CERT_CONFORMIDADE_TEXTO2'));
		$g['smarty']->assign('texto3', $CAD044->getVar('CERT_CONFORMIDADE_TEXTO3'));
		//$g['smarty']->assign('texto4', $CAD044->getVar('CERT_CONFORMIDADE_TEXTO4'));
		// Template da empresa sobrescreve o templete padrão
		echo gProcessaDocumento($docNome);
	break;
}
?>