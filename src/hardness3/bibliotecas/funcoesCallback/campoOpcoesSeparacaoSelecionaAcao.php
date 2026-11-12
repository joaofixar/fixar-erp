<?php
namespace hardness;
function campoOpcoesSeparacaoSelecionaAcao($array){
	global $g;
	$novoIdTelaForm = uniqid();
	$id = 'id-' . uniqid();
	$imagem = <<<EOT
		<span id="$id"> 
		   <div class="icone ui-state-default ui-corner-all">
		   		<span class="ui-icon ui-icon-arrowreturnthick-1-w" title="Retornar pedido!"></span>
		   </div>
		</span>
		
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			$('#$id').unbind('click').bind('click', function(data) {
				var \$buttons = {
					"Retornar para o vendedor": function() {

						$('#' + '{$g['divId']}').showLoading();

						$.getJSON('/est/est002/grid_func-ajax/retornarPedido/?ajax=true&T005_Id=' + encodeURIComponent('{$array['todosCampos']['T005_Id']}') + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    dialogConfirm('Ok', 'Pedido retornado!', \$buttons);
							    divRefresh('{$g['divId']}', true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#' + '{$g['divId']}').hideLoading();
						});
						$(this).dialog("destroy").remove();
					},
					"Retornar para separação": function() { $(this).dialog("destroy").remove(); 

						$('#' + '{$g['divId']}').showLoading();

						$.getJSON('/est/est002/grid_func-ajax/retornarPedidoSeparacao/?ajax=true&T005_Id=' + encodeURIComponent('{$array['todosCampos']['T005_Id']}') + '&callback=?', function(request) {
							if (request.code) {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    dialogConfirm('Ok', 'Pedido retornado!', \$buttons);
							    divRefresh('{$g['divId']}', true);
							} else {
								var \$buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
							    dialogConfirm('Erro', request.data, \$buttons);
							}
							$('#' + '{$g['divId']}').hideLoading();
						});
						$(this).dialog("destroy").remove();	
					},
					"Cancelar": function() { $(this).dialog("destroy").remove(); }
				};
				dialogConfirm('Retornar pedido', 'Selecione o opção desejada: ', \$buttons);
			});
		} </script></div>
EOT;

	return $imagem;
}