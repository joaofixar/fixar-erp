<?php
namespace hardness;

$exibirTabFinanceiro = '';
$arquivoTabs = $g['pathDados'].'/personalizacaoTab/tabsNaoUsar.serialized';
if(file_exists($arquivoTabs)){
	$tabsNaoUsar = unserialize(file_get_contents($arquivoTabs));
	foreach ($tabsNaoUsar as $tabs => $tab) {
		if(strpos($tabs, 'ven002tab02') === false) { continue; }
		$tabs = explode('-', $tabs);
		if(count($tabs) == 2){
			if($g['perfilUsuario'] == $tabs[0] && in_array('Pagamento', $tab)){
				$exibirTabFinanceiro = 'ui-disabled ';
			}
		} else if(in_array('Pagamento', $tab)){
			$exibirTabFinanceiro = 'ui-disabled ';
		}
	}
}

$urlPagamento = ($geradoFormaPagamento) ? "./pagamentoGrid.php?T005_Id={$T005_Id}" : "./pagamento.php?T005_Id={$T005_Id}";
?>
<div data-role="navbar" data-theme="a" class="nav-glyphish" data-grid="c" style="clear:both;">
	<ul>
		<li><a href="../orcamentoLista.php?T005_Id=<?=$T005_Id?>" id="voltar" data-transition="fade" data-icon="custom" <?php if ($etapa == 1) { echo 'class="ui-btn-active"'; } ?>>Voltar</a></li>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3;"; } else { echo "./pedido.php?T005_Id={$T005_Id}"; } ?>" id="menu-produto" data-transition="fade" data-icon="custom" <?php if ($etapa == 2) { echo 'class="ui-btn-active"'; } ?>>Produtos</a></li>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; } else { echo "./clientes.php?T005_Id={$T005_Id}"; } ?>" id="menu-cliente" data-transition="fade" data-icon="custom" <?php if ($etapa == 3) { echo 'class="ui-btn-active"'; } ?>>Cliente</a></li>
		<li>
			<a href=
<?php
		if(empty($exibirTabFinanceiro)){
			if (isset($confOrcamentoLista)) {
				echo "\"#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; 
			} else {
				echo "\"{$urlPagamento}";
			}
		} else {
			echo '"#';
		}
?>
			" id="menu-pagamento" data-transition="fade" data-icon="custom" class="<?php echo $exibirTabFinanceiro; if($etapa == 4) { echo 'ui-btn-active'; } echo '"';?>>Pagamento</a>
		</li>
		<!--<li><a href="#" onclick="faturarPedido(); return false;" id="gerarPedidoBarraIcon" data-transition="fade" data-icon="custom" data-rel="dialog" data-ajax="false">Faturar</a></li>-->
	</ul>
</div>

<script>
	function faturarPedido() {
		$.getJSON('./tablet_func-ajax.php?opcaoAcao=verificaAlcadaNovo&T005_Id=' + encodeURIComponent("<?=$T005_Id?>") + '&acao=faturar&callback=?', function(request) {
			if (request.code == 'erro' || request.code == 'analise') {
				$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
			} else if (request.code) {
				$('<div>').simpledialog2({
					animate: false, mode: 'button',
					headerText: 'Faturar Pedido<-', headerClose: false,
					buttonPrompt: 'Deseja faturar o pedido <?=$T005_Id?>',
					buttons : {
						'Sim': {
							click: function () {
								$.getJSON('./tablet_func-ajax.php?opcaoAcao=faturarPedido&T005_Id=' + encodeURIComponent("<?=$T005_Id?>") + '&callback=?', function(request2) {
									if (request2.code) {
										$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Ok', headerClose: false, buttonPrompt: request2.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
									} else {
										$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Erro', headerClose: false, buttonPrompt: request2.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
									}
								});
								setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300);
							}
						},
						'Não': {
							click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); },
							icon: "delete"
						}
					}
				});
			} else {
				$('<div>').simpledialog2({
					animate: false, mode: 'button',
					headerText: 'Notificação', headerClose: false,
					buttonPrompt: request.data,
					buttons : {
						'Sim': {
							click: function () {
								$.getJSON('./tablet_func-ajax.php?opcaoAcao=enviarParaAnalise&T005_Id=' + encodeURIComponent("<?=$T005_Id?>") + '&alcadaUpdate=' + encodeURIComponent(request.data) + '&callback=?', function(request2) {
									if (request2.code) {
										$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request2.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
									} else {
										$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request2.data, buttons : { 'Erro': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
									}
								});
								setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300);
							}
						},
						'Não': {
							click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); }
						}
					}
				});
			}
		});
		return false;
	}
</script>