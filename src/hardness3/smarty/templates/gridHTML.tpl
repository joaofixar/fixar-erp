{if $mostrarAgrupamento}
<link rel=stylesheet href="/hardness3/static/bootstrapErp/css/bootstrap.css" type="text/css">
<script src="/hardness3/static/js/jquery-1.6.2.min.js"></script>
<script src="/hardness3/static/js/jquery-ui-1.8.16.custom.min.js"></script>
<script src="/hardness3/static/bootstrapErp/js/bootstrap.js"></script>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<div id="instructions">
	<div class="gridFiltro ui-corner-all gridFiltroHTML">
		<div class="agrupUI" style="padding:5px;">
			<a onclick="$('#instructions a').removeClass('ui-state-active-sistema'); $(this).addClass('ui-state-active-sistema'); mudarRelatorio('geralOriginal'); return false;" class="ui-state-active-sistema">
				Padrão
			</a>&nbsp;&nbsp;
			{foreach from=$agrupamentoDadosArray.dimensoes key=aKey item=aItem}
				<a onclick="$('#instructions a').removeClass('ui-state-active-sistema'); $(this).addClass('ui-state-active-sistema'); mudarRelatorio('{$aItem[0]}'); return false;">
					{$aItem[1]}
				</a>&nbsp;&nbsp;
			{/foreach}
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".agrupUI" ).buttonset();
			$("#instructions a").addClass('ui-corner-all');
		});
		function mudarRelatorio(dimensao) {
			$('#gridHTML').html('<div style="font-size: 1.1em; padding: 14px 19px; color: gray; font-weight: bold;">Carregando...</div>');
			$.ajax({
				url: (dimensao == 'geralOriginal') ? window.location : "/sistema/funcoes/agrupamento/grid/",
				dataType: 'html',
				type: 'post',
				data: (dimensao == 'geralOriginal') ? '' : "ajax=true&generateHTML=true&generateHTMLTodos=true&dados=" + encodeURIComponent('{$agrupamentoDados}') + "&href=" + encodeURIComponent(window.location) + "&dimensao=" + encodeURIComponent(dimensao),
				success: function(data) {
					var data = data.replace('$(".agrupUI" ).buttonset();', '');
					data = $('<div></div>').html(data);
					$('#gridHTML').html(data.find('#gridHTML').html());
				}
			});
		}
	</script>
</div>
{/if}
<style>
	table.grid thead td {
		padding: 9px 7px !important;
		margin: 0 !important;
	}
</style>
<div id="gridHTML">
	{$tituloHTML}
	<div style="clear: both; width: 0; height: 0;"></div>
	<table class="grid" cellspacing="1" style='border-bottom:1px dashed gray'>
		{if !$tbody[0].agrupar0.nome}
		<thead>
			{$primeiraLinhaGridHead}
			<tr>
				{foreach from=$thead item=i}
					<td {$i.extra} style="background-color: silver; cursor: auto; font-size:11px; -moz-border-radius: 0px; -webkit-border-radius: 0px; -khtml-border-radius: 0px; border-radius: 0px; -moz-border-radius-topleft: 4px; -webkit-border-top-left-radius: 4px; -khtml-border-top-left-radius: 4px; border-top-left-radius: 4px; -moz-border-radius-topright: 4px; -webkit-border-top-right-radius: 4px; -khtml-border-top-right-radius: 4px; border-top-right-radius: 4px; text-align: {$i.align};{if $i.ordem > 0} padding-right: 16px;{/if}{$i.style}">{if $i.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $i.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{$i.value}</td>
				{/foreach}
			</tr>
		</thead>
		{/if}
		<tbody>
			{$campoValorTotal = false}
			{foreach from=$tbody key=tbodykey item=i}
				{if is_numeric($tbodykey)}
					{if $i.agrupar0.nome and $i.agrupar0.total and $tbodykey != 0}
						<tr>
							<td colspan="{$i.agrupar0.total[0]['colspan']-1}" style="padding: 5px 10px; font-size: 15px; font-weight: bold;">Sub-total</td>
						{foreach from=$i.agrupar0.total key=totalKey item=totalItem}
							{$campoValorTotal = $totalItem.colspan}
							{if $totalKey eq 0}
								<td style="text-align: right; font-size: 1em; font-weight: bold;">{$totalItem.total}</td>
							{else}
								<td colspan="{$totalItem.colspan}" style="text-align: right; font-size: 1em; font-weight: bold;">{$totalItem.total}</td>
							{/if}
						{/foreach}
						</tr>
					{/if}

					{if $i.agrupar0.nome}
						{if $tbodykey}
							<tr>
								<td colspan="100"><div <div style="margin-top: -2px; height: 2px; width: 100%; background-color: #ABABAB;"></div></td>
							</tr>
						{/if}
						<tr>
							<td colspan="100"><div style="margin-bottom: 0px; margin-top: 0px; margin-left: 5px; font-size:15px; font-weight: bold;">{$i.agrupar0.nome}</div></td>
						</tr>
					<!--<thead>-->
						{if $i.agrupar1.nome}
							<tr>
								<td colspan="100"><div <div style="margin-top: 1px; height: 1px; width: 100%; background-color: #ABABAB;"></div></td>
							</tr>
						{/if}
						{if !$i.agrupar1.nome}
							<tr>
							{foreach from=$thead item=hi}
								<td {$hi.extra} style="background-color: silver; cursor: auto; text-align: {$hi.align};{if $hi.ordem > 0} padding-right: 16px;{/if}{$hi.style}">{if $hi.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $hi.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{$hi.value}</td>
							{/foreach}
							</tr>
						{/if}
					<!--</thead>-->
					{/if}

					{if $i.agrupar1.nome}
						<tr>
							<td colspan="100"><h3 style="margin-bottom: 0px; margin-top: 5px; margin-left: 18px; ">{$i.agrupar1.nome}</div></h3></td>
						</tr>
					<!--<thead class="theadagrupamento">-->
						<tr>
						{foreach from=$thead key=theadKey item=hi}
							{if $gridColunasFixo and $theadKey < $gridColunasFixo} {continue} {/if}
							<td {$hi.extra} style="background-color: silver; cursor: auto; text-align: {$hi.align};{if $hi.ordem > 0} padding-right: 16px;{/if}{$hi.style}"{if $hi.id} onclick="filtroOrdem(event, tdis, '{$gridId}', '{$hi.id}'); return false;"{/if}>{if $hi.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $hi.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{$hi.value}{if $i.editar == 1}<div class="icone ui-icon ui-icon-pencil" style="margin-left: -4px;" onclick="gEditarColuna('{$i.id}'); event.stopPropagation(); event.preventDefault(); return false;"></div>&nbsp;{/if}</td>
						{/foreach}
						</tr>
					<!--</thead>-->
					{/if}

					<tr id="{$i.pk.pkCampo}" {if ($tbodykey mod 2) eq 0} style="background-color: #F4F4F4;"{/if} class="{$i.linha.class}">
					{foreach from=$i.dados key=id item=ii}
						{if $i.colunaAgrupar[$id] == 1 or $i.colunaAgrupar[$id] == -1}
							<td{if $i.colunaAgrupar[$id] == 1} rowspan="{$i.agrupar0.linhas}"{/if} style="cursor: auto; font-size:11px; text-align: {$i.colunaAlinhar[$id]};{$i.style[$id]}">{$ii}</td>
						{/if}
					{/foreach}
					</tr>


					{if $i.agrupar0.totalFinal}
						<tr>
							<td colspan="{$i.agrupar0.totalFinal[0]['colspan']-1}" style="padding: 5px 10px; font-size: 15px; font-weight: bold;">Sub-total</td>
						{foreach from=$i.agrupar0.totalFinal key=totalKey item=totalItem}
							{$campoValorTotal = $totalItem.colspan}
							{if $totalKey eq 0}
								<td style="text-align: right; font-size: 1em; font-weight: bold;">{$totalItem.total}</td>
							{else}
								<td colspan="{$totalItem.colspan}" style="text-align: right; font-size: 1em; font-weight: bold;">{$totalItem.total}</td>
							{/if}
						{/foreach}
						</tr>
					{/if}
				{/if}
			{/foreach}
			{if $tbody.colunaTotalFinal[-1]}

				{if $tbody[0].agrupar0.nome}
					<tr>
						<td colspan="100"><div <div style="margin-top: -2px; height: 2px; width: 100%; background-color: #ABABAB;"></div></td>
					</tr>
					<tr>
						<td colspan="100"><div style="margin-bottom: 0px; margin-top: 0px; margin-left: 5px; font-size:15px; font-weight: bold;">TOTAIS</div></td>
					</tr>
				<!--<thead>-->
					<!--<tr>
					{foreach from=$thead item=hi}
						<td {$hi.extra} style="background-color: silver; cursor: auto; text-align: {$hi.align};{if $hi.ordem > 0} padding-right: 16px;{/if}{$hi.style}">{if $hi.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $hi.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{$hi.value}</td>
					{/foreach}
					</tr>-->
				<!--</thead>-->
				{/if}

				<!--<tr>
					<td colspan="100" style="padding: 5px 10px; font-size: 1.3em; font-weight: bold;">Totais</td>
				</tr>-->
				<tr>
				{foreach from=$tbody.colunaTotalFinal key=totalKey item=totalItem}
					{if $totalKey < 0} {continue} {/if}
					<td style="text-align: right; font-size: 1em; font-weight: bold; padding: 3px;">{$totalItem.colunaTotalFinal}</td>
				{/foreach}
				</tr>
			{/if}
		</tbody>
		<tfoot>
			{foreach from=$tfoot item=i}
				{$tfoot}
			{/foreach}
		</tfoot>
	</table>
	{php}
		global $totais;
		if (is_array($totais) && count($totais) > 0) {
			echo '<div class="buttonsBottomTop">';
			$totais_ = \hardness\Totais::imprimirTotais($totais);
			echo str_replace('total total-padrao', 'total total-impressao', $totais_);
			echo '</div>';
		} else {
			echo str_replace('input', 'input style="border: 0; font-weight: bold;"', $totais);
		}
	{/php}
	{$rodapeHTML}
</div>