<?  
namespace hardness;

include('conf.php');
include('inc.conf.php');

include('inc.header.php'); 
?>
<div data-theme="a" data-role="header" style="font-size: 110%;">
    <h3 style="margin: 0.3em 40px 0.3em;">
        <div class="hleft"><?=$g['C031']['tabletHeaderTexto']?></div>
        <div class="hright"><span style="color: #F15928;">HARDNESS</span> SISTEMAS</div>
    </h3>
</div>
<?php
/*$sqlConversasPendentes = <<<EOT
	SELECT count(distinct T191_Id) total
	FROM T191 
	LEFT JOIN T192 ON T192_T191_Id = T191_Id
	LEFT JOIN C007 AS DE ON DE.C007_Id=T192_C007_Id 
	LEFT JOIN C007 AS PARA ON PARA.C007_Id=T192_C007_Id_Destinatario 
	WHERE (T192_C007_Id='{$g['usuarioAtual']}' OR T192_C007_Id_Destinatario='{$g['usuarioAtual']}') AND T191_Flag_Lido != 'S' AND T191_C007_Id_Lido != '{$g['usuarioAtual']}'
EOT;
$resConversasPendentes = mysql_query($sqlConversasPendentes);
echo mysql_error();
$rowConversasPendentes = mysql_fetch_assoc($resConversasPendentes);
if ($rowConversasPendentes[total]>0)
{
	$blink_ini='<blink>';
	$blink_fim='</blink>';
	$style_conversas="color:blue;font-weight:bold;";
	$total_conversas="({$rowConversasPendentes['total']})";
}*/
?>
	<?php /*if (preg_match('/orcamentoLista\.php$/', $_SERVER['SCRIPT_FILENAME']) || preg_match('/requisicoes\.php$/', $_SERVER['SCRIPT_FILENAME']) || preg_match('/servicos\.php$/', $_SERVER['SCRIPT_FILENAME']) || preg_match('/produtos\.php$/', $_SERVER['SCRIPT_FILENAME']) || preg_match('/conversas\.php$/', $_SERVER['SCRIPT_FILENAME']) || preg_match('/conversaMensagens\.php$/', $_SERVER['SCRIPT_FILENAME']) || preg_match('/caixaLista\.php$/', $_SERVER['SCRIPT_FILENAME']) || preg_match('/relatorios\.php$/', $_SERVER['SCRIPT_FILENAME'])) { 
		$request = isset($_REQUEST['T003_Id']) ? "T003_Id={$_REQUEST['T003_Id']}" : false;
		$request = ($request == false && isset($_REQUEST['T005_Id'])) ? "T005_Id={$_REQUEST['T005_Id']}" : $request;
		//Determina Tamanhos dos Links do menu, variado por tipo de Usuário
		if($C007['C007_Flag_Vendedor']=='D'){
			$dataGrid = "b";
		}else if($C007['C007_Flag_Vendedor']=='N'){
			$dataGrid = "a";
		}else{
			$dataGrid = "c";
		}*/
		?>
		
		<div id="menuBar" data-theme="a" class="menuPrincipal" data-grid="<?php echo $dataGrid; ?>">
			<ul data-role="listview">
				<?php 
				if($C007['C007_Flag_Vendedor']!='N'){
				?>
				<li><a href="/hardness3/mobile/phone/menuOrcamentos.php" id="menu-orcamento" data-transition="fade" data-icon="custom" <?php if ($etapa == 1) { echo 'class="ui-btn-active"'; } ?>><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/icons/104-index-cards.png" class="ui-li-icon">Orçamentos/Pedidos </a></li>
				<li><a href="/hardness3/mobile/phone/requisicoes.php" id="menu-requisicao" data-transition="fade" data-icon="custom"  <?php if ($etapa == 2) { echo 'class="ui-btn-active"'; } ?>><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/icons/80-shopping-cart.png" class="ui-li-icon">Requisições</a></li>
				<li><a href="/hardness3/mobile/phone/servicos.php" id="menu-servico" data-transition="fade" data-icon="custom"  <?php if ($etapa == 3) { echo 'class="ui-btn-active"'; } ?>><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/icons/158-wrench-2.png" class="ui-li-icon">Serviços</a></li>
				<?php 	
				}
				?>
				<?php 
				if($C007['C007_Flag_Vendedor']=='D'){
				?>
				<li><a href="/hardness3/mobile/phone/produtos.php" id="menu-produto-precos" data-transition="fade" data-icon="custom" <?php if ($etapa == 4) { echo 'class="ui-btn-active"'; } ?>><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/icons/161-calculator.png" class="ui-li-icon">Tabela Preço</a></li>	
				<?php 	
				}
				?>
				<?php 
				if($C007['C007_Flag_Vendedor']=='D' or $C007['C007_Flag_Vendedor']=='N'){
				?>
				<li><a href="/hardness3/mobile/phone/caixa/caixaLista.php" id="menu-caixa" data-transition="fade" data-icon="custom" <?php if ($etapa == 6) { echo 'class="ui-btn-active"'; } ?>><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/icons/172-pricetag.png" class="ui-li-icon">Caixa</a></li>
				<?php 	
				}
				echo $blink_ini;
				?>
				<li><a href="/hardness3/mobile/phone/conversas.php" id="menu-conversas" data-transition="fade" data-icon="custom" <?php echo "style='$style_conversas'"; ?> <?php if ($etapa == 5) { echo 'class="ui-btn-active"'; } ?>><img src="/hardness3/mobile/phone/jquery.mobile-1.0.1/images/icons/09-chat-2.png" class="ui-li-icon">Conversas <?php echo $total_conversas;  ?></a></li>
				<?php 	
				echo $blink_fim;
				?>
				<!-- <li><a href="./relatorios.php" id="menu-relatorio" data-transition="fade" data-icon="custom" data-ajax="false" <?php //if ($etapa == 6) { echo 'class="ui-btn-active"'; } ?>>Relatórios</a></li> -->
				
			</ul>
		</div>
	<? 
	/*} else {
		if (isset($confOrcamentoLista)) { 
			$gerarPedido = "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; 
		} else {
			//Verifica alçadas e define o Botão Imprimir e r e-mail.
			$sqlValorParcelas=mysql_query("SELECT SUM(T089_Valor_Parcela) as Valor_Total FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
			$rowValorParcelas=mysql_fetch_array($sqlValorParcelas);
			$diferenca = abs($T003['T003_Valor_Total'] - $rowValorParcelas['Valor_Total']);
			if(($diferenca > '0.03') && (!empty($rowValorParcelas['Valor_Total']))){
				$gerarPedido = "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Valor total das parcelas geradas não está de acordo com o valor total do orçamento. Favor gerar parcelas novamente.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; 
			}else{
				require_once("bibliotecas/classes/VEN001.php");
				$VEN001 = new VEN001();
				
				$retorno = $VEN001->verificaAlcada($T003_Id,true);
				
				
				$ret=mysql_query("SELECT AVG(T089_Prazos_Dias) as Prazo_Medio, COUNT(T089_Id) as Total_Parcelas FROM T089 WHERE T089_T003_Id='{$T003_Id}'");
				$T089=mysql_fetch_array($ret);				
				$prazoMedio=$T089['Prazo_Medio'];
				$totalParcelas=$T089['Total_Parcelas'];
				$descontoOrcamento = ($T003['T003_Valor_Desconto_Manual']*100)/($T003['T003_Valor_Desconto_Manual']+$T003['T003_Valor_Total']);
				
				$tipoAlcada = $g['conf']->ini('usarAlcadaTipo');
				if($tipoAlcada == '1'){
					//Calcula Desconto por alçada
					$descontoVendedor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoVendedor'));
					$descontoSupervisor = gCorrigeNumeroInverte($g['conf']->ini('alcadaDescontoSupervisor'));
					//Calcula prazo médio por alçada
					$prazoMedioAlcadaVendedor = $VEN001->calculaPrazoMedioAlcadaVendedor($T003_Id,true);
					$prazoMedioAlcadaSupervisor = $VEN001->calculaPrazoMedioAlcadaSupervisor($T003_Id,true);
  					//Verificações de alçada
  					$alcadaNome = "";
					if(($descontoOrcamento <= $descontoVendedor) && ($prazoMedio <= $prazoMedioAlcadaVendedor)){
						$alcadaUpdate = "V";
					} 
					elseif(($descontoOrcamento <= $descontoSupervisor) && ($prazoMedio <= $prazoMedioAlcadaSupervisor)){
						$alcadaUpdate = "S";
					}
					else{
						 $alcadaUpdate = "D";
					}
				}elseif($tipoAlcada == '2'){
					
					$regrasUsuario=$g['conf']->ini('regrasVendedorPorParcelas');		
					$descontoMaximoAlcada = 0;			
					foreach ($regrasUsuario as $regra){
						if ($prazoMedio<=$regra['pm'] && $numeroParcelas<=$regra['parcelas']){
							$descontoMaximoAlcada=$regra['desconto'];				
							break;
						}
					}			
					$descontoMaximoAlcadaVendedor=$descontoMaximoAlcada; 
					
					
					$regrasUsuario=$g['conf']->ini('regrasSupervisorPorParcelas');			
					$descontoMaximoAlcada = 0;			
					foreach ($regrasUsuario as $regra){
						if ($prazoMedio<=$regra['pm'] && $numeroParcelas<=$regra['parcelas']){
							$descontoMaximoAlcada=$regra['desconto'];				
							break;
						}
					}			
					$descontoMaximoAlcadaSupervisor=$descontoMaximoAlcada; 
					
					$descontoOrcamento = number_format($descontoOrcamento,2); 
					$descontoMaximoAlcadaVendedor = gCorrigeNumeroInverte($descontoMaximoAlcadaVendedor);
					$descontoMaximoAlcadaSupervisor = gCorrigeNumeroInverte($descontoMaximoAlcadaSupervisor);  
					
					if ($descontoOrcamento  <= $descontoMaximoAlcadaVendedor){
						$alcadaUpdate = "V";
					}
					elseif($descontoOrcamento  <= $descontoMaximoAlcadaSupervisor){
						$alcadaUpdate = "S"; 
					}
					else{
						 $alcadaUpdate = "D";
					}
				}
				
				if ($retorno === true) {
					if((!empty($rowValorParcelas['Valor_Total'])) || ($rowValorParcelas['Valor_Total'] <= 0 && $T003['T003_Valor_Total'] <= 0)){
						//verificar o que está errado, pois não está fazendo o update
						$gerarPedido = "./gerarPedido.php?T003_Id={$T003_Id}";
					}else{
						$gerarPedido = "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Ainda não foram geradas parcelas para este orçamento. Favor definir forma de pagamento!', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; 
					}
				} elseif($retorno === false) {
					$gerarPedido = "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Você não tem permissão para gerar Orçamentos!', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; 
				} else {
					if((!empty($rowValorParcelas['Valor_Total'])) || ($rowValorParcelas['Valor_Total'] <= 0 && $T003['T003_Valor_Total'] <= 0)){
						$gerarPedido= "#\" onclick=\"\$.mobile.changePage( './orcamentoPedirLiberacao.php?T003_Id={$T003_Id}&alcadaUpdate={$alcadaUpdate}', { transition: 'fade'} );\"";
					}else{
						$gerarPedido = "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Ainda não foram geradas parcelas para este orçamento. Favor definir forma de pagamento!', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; 
					}
				}
			}
		}
	?>
		<div data-role="navbar" data-theme="b" class="nav-glyphish" data-grid="d">
			<ul>
				<li><a href="./orcamentoLista.php?T003_Id=<?=$T003_Id?>" id="menu-sair" data-transition="fade" data-icon="custom" <?php if ($etapa == 1) { echo 'class="ui-btn-active"'; } ?>>Voltar</a></li>
				<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3;"; } else { echo "./orcamento.php?T003_Id={$T003_Id}"; } ?>" id="menu-produto" data-transition="fade" data-icon="custom" <?php if ($etapa == 2) { echo 'class="ui-btn-active"'; } ?>>Produtos</a></li>
				<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; } else { echo "./clientes.php?T003_Id={$T003_Id}"; } ?>" id="menu-cliente" data-transition="fade" data-icon="custom" <?php if ($etapa == 3) { echo 'class="ui-btn-active"'; } ?>>Cliente</a></li>
				<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; } else { echo "./pagamento.php?T003_Id={$T003_Id}"; } ?>" id="menu-pagamento" data-transition="fade" data-icon="custom" <?php if ($etapa == 4) { echo 'class="ui-btn-active"'; } ?>>Pagamento</a></li>
				<li><a href="<?=$gerarPedido?>" id="gerarPedidoBarraIcon" data-transition="fade" data-icon="custom" data-rel="dialog" <?php if ($etapa == 5) { echo 'class="ui-btn-active"'; } ?>>Gerar Pedido</a></li>
			</ul>
		</div>
	<? }*/ ?>
