<div class="filtro">{$filtro}</div>
<div>
	<table class="grid grid-select">
		<thead class="big">
			<tr>
				{foreach from=$thead item=i}
					<th class="ui-corner-all">{$i.value}</th>
				{/foreach}
			</tr>
		</thead>
		<tbody class="big">
			{foreach from=$tbody key=tbodykey item=i}
				{if is_numeric($tbodykey)}
					<tr id="{$i.pk.pkCampo}">
					{foreach from=$i.dados key=id item=ii}
						<td{if $i.colunaEditar[$id]} onclick="gEditarCampo('{$tbody.keys[$id]}', $(this), '{$i.colunaEditar[$id][0]}', '{$i.colunaEditar[$id][1]}', '{$i.colunaEditar[$id][2]}'); return false;" class="campoEditavel"{/if} style="text-align: {$i.colunaAlinhar[$id]};{$i.style[$id]}">{if $i.colunaEditar[$id]}<span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;"></span><span style="padding-right: 16px;">{$ii}</span></span>{else}{$ii}{/if}</td>
					{/foreach}
					</tr>
				{/if}
			{/foreach}
		</tbody>
		<tfoot class="big">
			{foreach from=$tfoot item=i}
				{$tfoot}
			{/foreach}
		</tfoot>
	</table>
</div>