<?php
namespace hardness;

echo <<<EOT
	<div data-role="footer" data-position="fixed" style="font-size: 110%;">
		<h3 style="margin: 0.3em 5px 0.3em;">
EOT;
if (!isset($confOrcamentoLista)) {
	if (isset($_REQUEST['T003_Id'])) {
		include('inc.conf.php');
		$T003_Data_Emissao = empty($T003['T003_Data_Emissao']) ? '' : gCorrigeData($T003['T003_Data_Emissao']);
		echo <<<EOT
			<span style="float: left;">
			Orçamento: {$T003_Id}&nbsp;&nbsp;&nbsp;&nbsp;Data: {$T003_Data_Emissao}&nbsp;&nbsp;&nbsp;&nbsp;Cliente: {$T003['D024_Nome_Fantasia']}&nbsp;&nbsp;&nbsp;&nbsp;Status: {$statusOrcamento} &nbsp;&nbsp;&nbsp;&nbsp;
			</span>
EOT;
	} else if (isset($_REQUEST['T005_Id'])) {
		include('pedido/inc.conf.php');
		$T005_Data_Emissao = empty($T005['T005_Data_Emissao']) ? '' : gCorrigeData($T005['T005_Data_Emissao']);
		echo <<<EOT
		<span style="float: left;">
		Pedido: {$T005_Id}&nbsp;&nbsp;&nbsp;&nbsp;Data: {$T005_Data_Emissao}&nbsp;&nbsp;&nbsp;&nbsp;Cliente: {$T005['D024_Nome_Fantasia']}&nbsp;&nbsp;&nbsp;&nbsp;Status: {$statusOrcamento} &nbsp;&nbsp;&nbsp;&nbsp;
		</span>
EOT;
	}
}else{
	echo <<<EOT
		<span style="float:left;">
			<a href="orcamentoLista.php?statusOrcamento=X" style="color:#FFF; text-decoration:none;">Todos&nbsp;&nbsp;</a>
			<a href="orcamentoLista.php?statusOrcamento=P" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/black-dot.png">&nbsp;Pendente&nbsp;&nbsp;</a>
			<a href="orcamentoLista.php?statusOrcamento=L" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/blue-dot.png">&nbsp;Em Análise&nbsp;&nbsp;</a>
			<a href="orcamentoLista.php?statusOrcamento=N" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/red-dot.png">&nbsp;Negado&nbsp;&nbsp;</a>
			<a href="orcamentoLista.php?statusOrcamento=A" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/green-dot.png">&nbsp;Aprovado&nbsp;&nbsp;</a>
			<a href="orcamentoLista.php?statusOrcamento=F" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/gray-dot.png">&nbsp;Faturado&nbsp;&nbsp;</a>
			<a href="orcamentoLista.php?statusOrcamento=V" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/orange-dot.png">&nbsp;Vencido&nbsp;&nbsp;</a>
		</span>
EOT;
}
$tipoUsuario = ($C007['C007_Flag_Vendedor'] == 'D') ? 'Diretor' : "Vendedor";

$linkImprimir = "";

//Verifica a página atual acessada
$paginaAtual = explode("/", $_SERVER['SCRIPT_FILENAME']);
$paginaAtual = $paginaAtual[sizeof($paginaAtual) - 1];

switch ($paginaAtual) {
	case "orcamentoLista.php":
		require_once("bibliotecas/classes/VEN001.php");
		$VEN001 = new VEN001();
		$montarPdfGridOrcamentos = $VEN001->VEN001montarPdfGrid($sql);
		$linkImprimir = <<<EOT
		<span style="float:right;">
			<a href="{$montarPdfGridOrcamentos['AnexosWeb'][0]}" target="_blank" style="margin-left:25px; color:#FFF; text-decoration:none;">&nbsp;&nbsp;&nbsp;<img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/print.png">&nbsp;&nbsp;&nbsp;</a>
		</span>	
EOT;
	;
	break;
	case "clientesLista.php":
		require_once("bibliotecas/classes/CAD001.php");
		$CAD001 = new CAD001();
		$montarPdfGridClientes = $CAD001->CAD001montarPdfGridClientes($sql);
		$linkImprimir = <<<EOT
		<span style="float:right;">
			<a href="{$montarPdfGridClientes['AnexosWeb'][0]}" target="_blank" style="margin-left:25px; color:#FFF; text-decoration:none;">&nbsp;&nbsp;&nbsp;<img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/print.png">&nbsp;&nbsp;&nbsp;</a>
		</span>	
EOT;
	;
	break;
	
	case "produtos.php":
		require_once("bibliotecas/classes/CAD002.php");
		$CAD002 = new CAD002();
		$montarPdfGridProdutos = $CAD002->CAD002montarPdfGridProdutos($sql);
		$linkImprimir = <<<EOT
		<span style="float:right;">
			<a href="{$montarPdfGridProdutos['AnexosWeb'][0]}" target="_blank" style="margin-left:25px; color:#FFF; text-decoration:none;">&nbsp;&nbsp;&nbsp;<img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/print.png">&nbsp;&nbsp;&nbsp;</a>
		</span>	
EOT;
	;
	break;
	
	case "requisicoes.php":
	require_once("bibliotecas/classes/EST014.php");
	$EST014 = new EST014();
	$montarPdfGridRequisicoes = $EST014->EST014montarPdfGridRequisicoes($sql);
	$linkImprimir = <<<EOT
	<span style="float:right;">
		<a href="{$montarPdfGridRequisicoes['AnexosWeb'][0]}" target="_blank" style="margin-left:25px; color:#FFF; text-decoration:none;">&nbsp;&nbsp;&nbsp;<img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/print.png">&nbsp;&nbsp;&nbsp;</a>
	</span>	
EOT;
	;
	break;
	
	case "nota_fiscalLista.php":
	require_once("bibliotecas/classes/VEN003.php");
	$VEN003 = new VEN003();
	$montarPdfGridNotasFiscais = $VEN003->VEN003montarPdfGridNotasFiscais($sql, $sqlTotalizadores);
	$linkImprimir = <<<EOT
	<span style="float:right;">
		<a href="{$montarPdfGridNotasFiscais['AnexosWeb'][0]}" target="_blank" style="margin-left:25px; color:#FFF; text-decoration:none;">&nbsp;&nbsp;&nbsp;<img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/print.png">&nbsp;&nbsp;&nbsp;</a>
	</span>	
EOT;
	;
	break;
	
	default:
		$linkImprimir = "";
		;
	break;
}

echo <<<EOT
		{$linkImprimir} 
		<span style="float:right;">{$tipoUsuario}: {$C007['C007_Primeiro_Nome']}</span> </h3> 
	</div> 
	    </body>
	</html>
EOT;
