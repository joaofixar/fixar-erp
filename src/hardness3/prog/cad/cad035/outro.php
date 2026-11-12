<?php
namespace hardness;

switch ($g['r'][3]) {
	case 'cad035locacaoBensMoveis':
		// Qual template usar?
		$docNome = 'recibosDeLocacao.tpl';
		
		// Classes
		include 'bibliotecas/classes/CAD035.php';
		$CAD035 = new CAD035();
		
		$T156_Id = isset($r_T156_Id) ? $r_T156_Id : false ;
		
		// Atribuir os elementos do template (veja documentação dentro do próprio template)
		$g['smarty']->assign('cabecalho', $CAD035->getVar('CABECALHO'));
		$g['smarty']->assign('banco', $CAD035->gerarDadosBanco($T156_Id));
		$g['smarty']->assign('dadosEmpresaCabecalho', $CAD035->gerarDadosEmpresaCabecalho($T156_Id));
		$g['smarty']->assign('emissaoANDNum', $CAD035->emissaoANDNum($T156_Id));
		$g['smarty']->assign('textoTitle', $CAD035->getVar('RECIBO_LOCACAO_TITULO'));
		$g['smarty']->assign('tableEmpresa', $CAD035->docGerarDadosEmpresa($T156_Id));
		$g['smarty']->assign('corpoDcto', $CAD035->docGerarCorpoDcto($T156_Id));
		$g['smarty']->assign('rodape', $CAD035->docGerarRodape($T156_Id));
		// Template da empresa sobrescreve o templete padrão
		echo gProcessaDocumento($docNome);
	break;
}
?>