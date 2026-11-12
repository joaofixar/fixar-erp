<?php
namespace hardness;

$exibirTabFinanceiro = '';
$arquivoTabs = $g['pathDados'].'/personalizacaoTab/tabsNaoUsar.serialized';
if(file_exists($arquivoTabs)){
	$tabsNaoUsar = unserialize(file_get_contents($arquivoTabs));
	foreach ($tabsNaoUsar as $tabs => $tab) {
		if(strpos($tabs, 'ven001TabEditarOrcamento') === false) { continue; }
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
?>
<div data-role="navbar" data-theme="b" class="nav-glyphish" data-grid="a">
	<ul>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3;"; } else { echo "./orcamento.php?T003_Id={$T003_Id}"; } ?>" id="menu-produto" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 2) { echo 'class="ui-btn-active"'; } ?>>Produtos</a></li>
		<li><a href="<?php if (isset($confOrcamentoLista)) { echo "#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; } else { echo "./clientes.php?T003_Id={$T003_Id}"; } ?>" id="menu-cliente" data-transition="fade" data-icon="custom" data-ajax="false" <?php if ($etapa == 3) { echo 'class="ui-btn-active"'; } ?>>Cliente</a></li>
		<li><a href=
<?php
		if(empty($exibirTabFinanceiro)){
			if (isset($confOrcamentoLista)) {
				echo "\"#\" onclick=\"\$('<div>').simpledialog2({animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: 'Por favor, selecione um orçamento.', buttons : { 'OK': { click: function () { setTimeout(function() { \$('#index-cards').addClass('ui-btn-active'); }, 300); } } } });\" style=\"opacity: 0.3"; 
			} else {
				echo "\"./pagamento.php?T003_Id={$T003_Id}";
			}
		} else {
			echo '"#';
		}
?>
			" id="menu-pagamento" data-transition="fade" data-icon="custom" data-ajax="false" class="<?php echo $exibirTabFinanceiro; if ($etapa == 4) { echo 'ui-btn-active'; } echo '"'; ?>>Pagamento</a></li>
		<li><a href="#" onclick="gerarPedido(); return false;" id="gerarPedidoBarraIcon" data-transition="fade" data-icon="custom" data-rel="dialog">Gerar Pedido</a></li>
	</ul>
</div>

<script>
	function gerarPedido() {
		$.getJSON('./phone_func-ajax.php?opcaoAcao=verificaAlcadaNovo&T003_Id=' + encodeURIComponent("<?=$T003_Id?>") + '&gerarPedido=true&callback=?', function(request) {
			if (request.code == 'erro' || request.code == 'analise') {
				$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request.data, buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
			} else if (request.code) {
				$('<div>').simpledialog2({
					animate: false, mode: 'button',
					headerText: 'Gerar Pedido', headerClose: false,
					buttonPrompt: request.data,
					buttons : {
						'Sim': {
							click: function () {
								$.getJSON('./phone_func-ajax.php?opcaoAcao=gerarPedido&T003_Id=' + encodeURIComponent("<?=$T003_Id?>") + '&alcadaUpdate=' + encodeURIComponent(request.data) + '&callback=?', function(request2) {
									if (request2.code) {

										if (request2.alerta) {
											$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request2.alerta[1], buttons : { 'OK': { click: function () { setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300); } } } });
											//abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' ,unique(), '', 'Editar Cliente/Fornecedor - ID: ' + request.alerta[0], '/cad/cad001/content/cad001contentCadastro/', '&acaoId=' + encodeURIComponent(request.alerta[0]) + '&tabela=D024', [930,583]);
										} else if(request2.notificacao) {
											$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request2.notificacao, buttons : { 'OK': { click: function () {
												setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300);
												$.mobile.changePage('./pedido/pedido.php?T005_Id=' + request2.T005_Id, { transition: 'fade'} );
											} } } });
										} else {
											$('<div>').simpledialog2({ animate: false, mode: 'button', headerText: 'Notificação', headerClose: false, buttonPrompt: request2.data, buttons : { 'OK': { click: function () {
												setTimeout(function() { $('#index-cards').addClass('ui-btn-active'); }, 300);
												$.mobile.changePage('./pedido/pedido.php?T005_Id=' + request2.T005_Id, { transition: 'fade'} );
											} } } });
										}
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
								$.getJSON('./tablet_func-ajax.php?opcaoAcao=enviarParaAnalise&T003_Id=' + encodeURIComponent("<?=$T003_Id?>") + '&alcadaUpdate=' + encodeURIComponent(request.data) + '&callback=?', function(request2) {
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