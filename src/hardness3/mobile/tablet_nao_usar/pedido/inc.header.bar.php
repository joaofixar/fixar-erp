<?php
	if (isset($etapa)) {
		$request = isset($_REQUEST['T003_Id']) ? "T003_Id={$_REQUEST['T003_Id']}" : false;
		$request = ($request == false && isset($_REQUEST['T005_Id'])) ? "T005_Id={$_REQUEST['T005_Id']}" : $request;

?>
	<div data-role="collapsible" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-collapsed="true" data-theme="a" data-content-theme="a" style="position:absolute; z-index:1000; width:98%; margin-left:1%;  margin-top:3px;">
		<h3>
			<div style="float:left;" ><?=$g['C004']['C004_Nome_Abreviado']?></div>
	        <div style="float:right;" ><span style="color: #F15928;">HARDNESS</span> SISTEMAS</div>
        </h3>
		<div id="menuBar" data-role="navbar" data-theme="a" class="nav-glyphish"  data-grid="f">
			<ul class="menuPrincipal">
				<li><a data-theme="c" href="/hardness3/mobile/tablet/orcamentoLista.php" id="menu-orcamento" data-transition="fade" data-icon="custom" data-ajax="false" >Vendas</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/clientesLista.php" id="menu-cliente" data-transition="fade" data-icon="custom" data-ajax="false" >Clientes</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/produtos.php" id="menu-produto-precos" data-transition="fade" data-icon="custom" data-ajax="false" >Produtos</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/nota_fiscal/nota_fiscalLista.php" id="menu-nota-fiscal" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 8) { echo 'class="ui-btn-active"'; } ?>>Notas Fiscais</a></li>	
				<? if(isset($g['c029Ids'][110])){ ?>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/inventario.php" id="menu-estoque" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 13) { echo 'class="ui-btn-active"'; } ?>>Inventário</a></li>
				<? } ?>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/login.php" id="menu-sair" data-transition="fade" data-icon="custom" data-ajax="false">Sair</a></li>
			</ul>
				<!--<li><a data-theme="c" href="/hardness3/mobile/tablet/conversas.php" id="menu-conversas" data-transition="fade" data-icon="custom" data-ajax="false" <?php echo "style='$style_conversas'"; ?> >Conversas <?php echo $total_conversas;  ?></a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/caixa/caixaLista.php" id="menu-caixa" data-transition="fade" data-icon="custom" data-ajax="false" >Caixa</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/analise_credito/analise_credito.php" id="menu-analise-credito" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 9) { echo 'class="ui-btn-active"'; } ?>>Análise de Crédito</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/financeiro/contasReceber.php" id="menu-contas-receber" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 10) { echo 'class="ui-btn-active"'; } ?>>Contas a Receber</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/financeiro/contasPagar.php" id="menu-contas-pagar" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 11) { echo 'class="ui-btn-active"'; } ?>>Contas a Pagar</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/requisicoes.php" id="menu-requisicao" data-transition="fade" data-icon="custom" data-ajax="false" >Requisições</a></li>
				<li><a href="./relatorios.php" id="menu-relatorio" data-transition="fade" data-icon="custom" data-ajax="false" <?php //if ($etapa == 6) { echo 'class="ui-btn-active"'; } ?>>Relatórios</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/servicos.php" id="menu-servico" data-transition="fade" data-icon="custom" data-ajax="false" >Serviços</a></li>
				<li><a data-theme="c" href="/hardness3/mobile/tablet/crm/resumoClientes.php" id="menu-resumos-gerenciais" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 12) { echo 'class="ui-btn-active"'; } ?>>Resumos Gerênciais</a></li>-->
		</div>
	</div>
<?php if($_REQUEST['acao'] == 'caixa'){ ?>
<div data-role="navbar" data-theme="a" class="nav-glyphish" data-grid="<?=$gridData?>" style="clear:both; margin-top:45px;">
	<ul>
		<li><a href="../caixa/caixaLista.php?T005_Id=<?=$T005_Id?>" id="menu-sair" data-transition="fade" data-icon="custom" <?php if ($etapa == 1) { echo 'class="ui-btn-active"'; } ?>>Voltar</a></li>
		<li><a href="../pedido/pedido.php?T005_Id=<?=$T005_Id?>&acao=caixa" id="menu-produto" data-transition="fade" data-icon="custom" <?php if ($etapa == 2) { echo 'class="ui-btn-active"'; } ?>>Produtos</a></li>
		<li><a href="./clientes.php?T005_Id=<?=$T005_Id?>&acao=caixa" id="menu-cliente" data-transition="fade" data-icon="custom" <?php if ($etapa == 3) { echo 'class="ui-btn-active"'; } ?>>Cliente</a></li>
		<li><a href="./pagamento.php?T005_Id=<?=$T005_Id?>&acao=caixa" id="menu-pagamento" data-transition="fade" data-icon="custom" <?php if ($etapa == 4) { echo 'class="ui-btn-active"'; } ?>>Pagamento</a></li>
	<?php //Verifica se o pedido esta no caixa, se não estiver permite re-abrir o pedido
	$T005 = mysql_query("SELECT T005_Flag_Esta_No_Caixa FROM T005 WHERE T005_Id = '{$T005_Id}'");
	$resT005 = mysql_fetch_array($T005);
	if($resT005['T005_Flag_Esta_No_Caixa'] != 'N'){ ?>
		<li><a href="../caixa/finalizar.php?T005_Id=<?=$T005_Id?>&acao=caixa" id="finalizarPedidoBarraIcon" data-transition="fade" data-icon="custom" data-rel="dialog" data-ajax="false" <?php if ($etapa == 5) { echo 'class="ui-btn-active"'; } ?>>Finalizar</a></li>
	<?php } else { ?>
		<li><a href="../caixa/reabrir.php?T005_Id=<?=$T005_Id?>&acao=caixa" id="finalizarPedidoBarraIcon" data-transition="fade" data-icon="custom" data-rel="dialog" data-ajax="false" <?php if ($etapa == 5) { echo 'class="ui-btn-active"'; } ?>>Reabrir Pedido</a></li>
	<?php } ?>
	</ul>
</div>
<?php } else { ?>
<div data-role="navbar" data-theme="a" class="nav-glyphish" data-grid="<?=$gridData?>" style="clear:both; margin-top:45px;">
	<ul>
		<li><a href="../orcamentoLista.php?T005_Id=<?=$T005_Id?>" id="voltar" data-transition="fade" data-icon="custom" <?php if ($etapa == 1) { echo 'class="ui-btn-active"'; } ?>>Voltar</a></li>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3;"; } else { echo "./pedido.php?T005_Id={$T005_Id}"; } ?>" id="menu-produto" data-transition="fade" data-icon="custom" <?php if ($etapa == 2) { echo 'class="ui-btn-active"'; } ?>>Produtos</a></li>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; } else { echo "./clientes.php?T005_Id={$T005_Id}"; } ?>" id="menu-cliente" data-transition="fade" data-icon="custom" <?php if ($etapa == 3) { echo 'class="ui-btn-active"'; } ?>>Cliente</a></li>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; } else { echo "./pagamento.php?T005_Id={$T005_Id}"; } ?>" id="menu-pagamento" data-transition="fade" data-icon="custom" <?php if ($etapa == 4) { echo 'class="ui-btn-active"'; } ?>>Pagamento</a></li>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; } else { echo "./enviarCaixa.php?T005_Id={$T005_Id}"; } ?>" id="gerarPedidoBarraIcon" data-transition="fade" data-icon="custom" data-rel="dialog" data-ajax="false" <?php if ($etapa == 5) { echo 'class="ui-btn-active"'; } ?>>Re-enviar p/ Caixa</a></li>
	</ul>
</div>
<?php } ?>

<?php } ?>
