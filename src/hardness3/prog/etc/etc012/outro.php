<?php
namespace hardness;

switch ($g['r'][3]) {                 
	case 'etc012OutroDashboard':
		// Data é passada para os grids
		$dataAtual = isset($r_data) ? $r_data : date('d/m/Y');
		$data = isset($r_data) ? gCorrigeDataInverte($r_data) : gCorrigeDataInverte($dataAtual);
		// Configurações
		$confDashboard = array(
			'coluna1' => array(
				'width' => '50%',	// acrescentar % ou 'px'
				'elementos' => array(
					array(
						'id' => 1, // Igual tabela C027_Id
						'titulo' => 'Vendas',
						'acao' => array(uniqid(), '/etc/etc012/grid/etc012Vendas/', '&data=' . urlencode($data)),
						'height' => '200'	// somente número (não acrescentar 'px')
					),
					array(
						'id' => 2,
						'titulo' => 'Financeiro',
						'acao' => array(uniqid(), '/etc/etc012/grid/etc012Financeiro/', '&data=' . urlencode($data)),
						'height' => '75'
					)
				)
			),
			'coluna2' => array(
				'width' => '50%',
				'elementos' => array(
					array(
						'id' => 3,
						'titulo' => 'Fluxo Caixa',
						'acao' => array(uniqid(), '/etc/etc012/graph/etc012GraphFluxoCaixa/', '&graph=true&data=' . urlencode($data)),
						'height' => '200'
					)
				)
			)
		);
	
		// Gera o Dashboard
		echo <<<EOT
<style>
	.column { float: left; padding-bottom: 3px; }
	.portlet { margin: 3px 3px 3px 3px; }
	.portlet-header { margin: 0.3em; padding-bottom: 4px; padding-left: 0.2em; cursor: move; }
	.portlet-header .ui-icon { float: right; cursor: pointer; }
	.portlet-content { padding: 0; }
	.ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
	.ui-sortable-placeholder * { visibility: hidden; }
</style>

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	$(function() {
		$( ".column" ).sortable({
			connectWith: ".column",
			handle: ".portlet-header"
		});

		$( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
			.find( ".portlet-header" )
				.addClass( "ui-widget-header ui-corner-all" )
				.prepend( "<span class='ui-icon ui-icon-minusthick'></span>")
				.end()
			.find( ".portlet-content" );

		$( ".portlet-header .ui-icon" ).click(function() {
			$( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
			$( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
			var height = $(this).closest('.portlet').data('height');
			if (isempty(height) || height == '') {
				$(this).closest('.portlet').data('height', $(this).closest('.portlet').css('height'));
				$(this).closest('.portlet').css('height', '40px');
			} else {
				$(this).closest('.portlet').css('height', $(this).closest('.portlet').data('height'));
				$(this).closest('.portlet').data('height', '');
			}
		});

		$( ".column" ).disableSelection();
		$( ".portlet" ).disableSelection();
	});
	// atualiza os grids com a nova data
	$('#etc012Data').change(function() {
		divSetarAcaoId('{$g['divId']}', $(this).val(), 'data');
		divRefresh('{$g['divId']}');
	});
	$('#etc012Atualizar').click(function() {
		divRefresh('{$g['divId']}');
	});
} </script></div>

<div style="font-size: 14px; margin: 7px 0 4px 12px; display: block; float: center;">
	<input type="hidden">
	Data: <input id="etc012Data" type="text" class="campo_input_data" maxsize="10" size="8" value="{$dataAtual}" style='text-align:center'>&nbsp;&nbsp;&nbsp;<button type="button" id="etc012Atualizar">Atualizar</button>
</div>

<div style="clear: both;"></div>

<div class="dashboard">
EOT;
		
		$actions = array();
		foreach ($confDashboard as $colunaKey => $coluna) {
			echo '<div class="column" style="width: '.$coluna['width'].'; position: relative;">';
				foreach ($coluna['elementos'] as $elementoKey => $elemento) {
					echo '<div class="portlet" style="height: '.$elemento['height'].'px;">';
						echo '<div class="portlet-header">'.$elemento['titulo'].'</div>';
						echo '<div class="portlet-content" style="height: '.($elemento['height']-30).'px;" id="'.$elemento['acao'][0].'"></div>';
						$actions[] = $elemento['acao'];
					echo '</div>';
				}
			echo '</div>';
		}
		
		// gera ações ajax para carregar cada item
		echo gProcessaAcoes($actions);

	break;
}
?>
