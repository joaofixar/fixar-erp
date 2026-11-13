<?php
namespace hardness;

switch ($g['r'][3]) {

	case 'cad037CertificadoInspecao':
		
		// Qual template usar?
		$docNome = 'certificadoDeInspecaoInicial.tpl';
		
		// Classes
		include 'bibliotecas/classes/CAD037.php';
		include 'bibliotecas/classes/CAD044.php';
		include 'bibliotecas/classes/CAD008.php';
		$CAD037 = new CAD037();
		$CAD044 = new CAD044();
		$CAD008 = new CAD008();		
		
		$D124_Id = isset($r_D124_Id) ? $r_D124_Id : false ;
		
		// Atribuir os elementos do template (veja documentação dentro do próprio template)
		$g['smarty']->assign('cabecalho1', $CAD037->getVar('CERT_INSPECAO_CABECALHO1'));
		$g['smarty']->assign('logo', $CAD037->getVar('CABECALHO'));
		$g['smarty']->assign('NumAno', $CAD037->docGerarNumAno($D124_Id));
		$g['smarty']->assign('titleCert', $CAD037->getVar('CERT_INSPECAO_TITLE'));
		$g['smarty']->assign('subTitle', $CAD037->getVar('CERT_INSPECAO_SUB_TITLE'));
		$g['smarty']->assign('title2', $CAD037->getVar('CERT_INSPECAO_TITLE2'));
		$g['smarty']->assign('subTitle2', $CAD037->getVar('CERT_INSPECAO_SUBTITLE2'));
		$g['smarty']->assign('tableInspecao', $CAD037->docGerarTableInspecao($D124_Id)); 
		$g['smarty']->assign('especificacoes', $CAD037->docGerarEspecificacoes($D124_Id,$CAD044->getVar('CERT_INSPECAO_TEXTO4')));
		$g['smarty']->assign('cilindrosGas', $CAD037->docGerarCilindrosGas($D124_Id, $CAD044->getVar('CERT_INSPECAO_TEXTO2'), 
																					  $CAD044->getVar('CERT_INSPECAO_TEXTO3')));
		//$g['smarty']->assign('tableInspecao3', $CAD037->docGerarTableInspecao3($D124_Id));
		//$g['smarty']->assign('tableInspecao4', $CAD037->docGerarTableInspecao4($D124_Id));
		//$g['smarty']->assign('divFooterLeft', $CAD037->docGerarRodapeEsquerda($D124_Id));
		$g['smarty']->assign('divFooterRight', $CAD037->docGerarRodapeDireita($D124_Id));
		
		
		//$g['smarty']->assign('dadosEmpresaCabecalho', $CAD037->gerarDadosEmpresaCabecalho($T156_Id));
		// $g['smarty']->assign('emissaoANDNum', $CAD037->emissaoANDNum($T156_Id));
		// $g['smarty']->assign('textoTitle', $CAD037->getVar('RECIBO_LOCACAO_TITULO'));
		// $g['smarty']->assign('tableEmpresa', $CAD037->docGerarDadosEmpresa($T156_Id));
		// $g['smarty']->assign('corpoDcto', $CAD037->docGerarCorpoDcto($T156_Id));
		// $g['smarty']->assign('rodape', $CAD037->docGerarRodape($T156_Id));
		// Template da empresa sobrescreve o templete padrão
		echo gProcessaDocumento($docNome);
		
		

	break;
	
	
}
?>