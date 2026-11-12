<div class="filtro">{$filtro}</div>
<div>
	{if !$gridOptNoShow}
		<div {if $gridOptNoTop}class="gridOptTop" style="position: absolute; right: {if $gridOptRight}{$gridOptRight};{else}21px;{/if}"{else}class="gridOptTop" style="margin-top: -20px; position: absolute; right: {if $gridOptRight}{$gridOptRight};{else}8px;{/if}"{/if}>
			<div class="icone" title="Limpar Filtro/Ordenação" style="background-image: url('/hardness3/static/img/icone_info.png'); margin-right: 5px;" onclick="showInfoJanela($(this).closest('.ui-dialog-content')); return false;"></div>
			<div class="icone" title="Limpar Filtro/Ordenação" style="background-image: url('/hardness3/static/img/icone_limpar.png'); margin-right: 5px;" onclick="gerarCSV(this, true); return false;"></div>
			{if $gridSql}<div class="icone" title="SQL" style="background-image: url('/hardness3/static/img/icone_sql.png'); margin-right: 5px;" onclick="dialogConfirm('Debug SQL', '{$gridSql}'); return false;"></div>{/if}
			{if $gridTable}<div class="icone" title="Auditoria" style="background-image: url('/hardness3/static/img/icone_audit.png'); margin-right: 5px;" onclick="gridAudit(this, '{$gridTable}'); return false;"></div>{/if}
			<div class="icone" title="Gerar CSV" style="background-image: url('/hardness3/static/img/icone_csv.png'); margin-right: 5px;" onclick="gerarCSV(this); return false;"></div>
			<div class="icone" title="Atualizar Dados" style="background-image: url('/hardness3/static/img/icone_refresh.png');" onclick="gridRefresh(this); return false;"></div>
		</div>
	{/if}
	<table class="grid"{if $gridOptNoTop} style="margin-top: 16px;"{/if}>
		<thead>
			<tr>
				<th colspan="2" class="ui-widget-header ui-corner-all" style="text-align: center;"></th>
				<th colspan="5" class="ui-widget-header ui-corner-all" style="text-align: center;">Clientes</th>
				<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center;">Orçamentos</th>
				<th colspan="4" class="ui-widget-header ui-corner-all" style="text-align: center;">Pedidos</th>
				<th colspan="6" class="ui-widget-header ui-corner-all" style="text-align: center;">Notas Fiscais</th>
				<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center;">Devoluções</th>
				<th colspan="3" class="ui-widget-header ui-corner-all" style="text-align: center;">Compras</th>
			</tr>
			<tr>
				{foreach from=$thead item=i}
					<th class="ui-widget-header ui-corner-all {$i.class}" {$i.extra} style="text-align: {$i.align};{if $i.ordem > 0} padding-right: 16px;{/if}{$i.style}"{if $i.id} onclick="filtroOrdem(this, '{$gridId}', '{$i.id}'); return false;"{/if}>{if $i.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $i.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{$i.value}</th>
				{/foreach}
			</tr>
		</thead>
		<tbody>
			{foreach from=$tbody key=tbodykey item=i}
				{if is_numeric($tbodykey)}
					<tr id="{$i.pk.pkCampo}"{if $i.linha.class} class="{$i.linha.class}"{/if}>
					{foreach from=$i.dados key=id item=ii}
						<td{if $i.colunaEditar[$id]} onclick="gEditarCampo('{$tbody.keys[$id]}', $(this), '{$i.colunaEditar[$id][0]}', '{$i.colunaEditar[$id][1]}', '{$i.colunaEditar[$id][2]}', '{$i.colunaEditar[$id][3]}'); return false;" class="campoEditavel"{/if} style="text-align: {$i.colunaAlinhar[$id]};{$i.style[$id]}">{if $i.colunaEditar[$id]}<span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;"></span><span style="padding-right: 16px;">{$ii}</span></span>{else}{$ii}{/if}</td>
					{/foreach}
					</tr>
				{/if}
			{/foreach}
		</tbody>
		<tfoot>
			{foreach from=$tfoot item=i}
				{$tfoot}
			{/foreach}
		</tfoot>
	</table>
</div>
<div class="idTabela" tabela="{$grid_tabela}" tipo="grid"></div>