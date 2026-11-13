<div data-theme="a" data-role="header" style="font-size: 110%;">
    <h3 style="margin: 0.3em 40px 0.3em;">
        <div class="hleft"><?=$g['C031']['tabletHeaderTexto']?></div>
        <div class="hright"><span style="color: #F15928;">HARDNESS</span> SISTEMAS</div>
    </h3>
</div>
<?php if (isset($etapa)) { ?>
	<?php
		$gridData = 'c';
		if ($T005['T005_Flag_Status'] <= 0) {
			$gridData = 'd';
		}
	?>
<div data-role="navbar" data-theme="a" class="nav-glyphish" data-grid="<?=$gridData?>">
	<ul>
		<li><a href="../orcamentoLista.php?T005_Id=<?=$T005_Id?>" id="menu-sair" data-transition="fade" data-icon="custom" <?php if ($etapa == 1) { echo 'class="ui-btn-active"'; } ?>>Voltar</a></li>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3;"; } else { echo "./pedido.php?T005_Id={$T005_Id}"; } ?>" id="menu-produto" data-transition="fade" data-icon="custom" <?php if ($etapa == 2) { echo 'class="ui-btn-active"'; } ?>>Produtos</a></li>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; } else { echo "./clientes.php?T005_Id={$T005_Id}"; } ?>" id="menu-cliente" data-transition="fade" data-icon="custom" <?php if ($etapa == 3) { echo 'class="ui-btn-active"'; } ?>>Cliente</a></li>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; } else { echo "./pagamento.php?T005_Id={$T005_Id}"; } ?>" id="menu-pagamento" data-transition="fade" data-icon="custom" <?php if ($etapa == 4) { echo 'class="ui-btn-active"'; } ?>>Pagamento</a></li>
		<?php if ($T005['T005_Flag_Status'] <= 0) { ?>
			<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; } else { echo "./enviarCaixa.php?T005_Id={$T005_Id}"; } ?>" id="gerarPedidoBarraIcon" data-transition="fade" data-icon="custom" data-rel="dialog" <?php if ($etapa == 5) { echo 'class="ui-btn-active"'; } ?>>Re-enviar p/ Caixa</a></li>
		<?php } ?>
	</ul>
</div>

<?php } ?>
