<style>
.grid tbody td, .gridFixo tbody td {
	white-space: normal;
}
</style>
<div>
	{$tituloHTML}
	<div style="clear: both; width: 0; height: 0;"></div>	
	<table class="grid" cellspacing="1" style='border-bottom:1px dashed gray; padding: 0; column-count: 2;'>
		{if !$tbody[0].agrupar0.nome}
		<tdead>
			{$primeiraLinhaGridHead}
			<tr>
				{foreach from=$thead item=i}
					<td {$i.extra} style="background-color: silver; cursor: auto; font-size:9px; text-align: {$i.align};{if $i.ordem > 0} padding-right: 16px;{/if}{$i.style}">{if $i.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $i.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{$i.value}</td>
				{/foreach}
			</tr>
		</thead>
		{/if}
		<tbody>
			{$linhas = 0}
			{$count = 1}			
			<table class="grid" cellspacing="1" style='border-bottom:1px dashed gray; padding: 0; width: 49%; float: left; column-count: 2; clear: left;'>
			{foreach from=$tbody key=tbodykey item=i}									
				{if is_numeric($tbodykey)}
					{if $linhas > 800 or $linhas+5 > 800}
						{$count = $count + 1}
						{$linhas = 0}						
						</table>						
						{if ($count mod 2) eq 0}
							<table class="grid" cellspacing="1" style='border-bottom:1px dashed gray; padding: 0; width: 49%; float: left; column-count: 2;'>
						{else}
							<table class="grid" cellspacing="1" style='border-bottom:1px dashed gray; padding: 0; width: 49%; float: left; column-count: 2; clear: left;'>
							<br style="page-break-before: always;" />				
						{/if}						
					{/if}				
					{if $i.agrupar0.nome and $i.agrupar0.total and $tbodykey != 0}						
						<tr>
							<td colspan="{$i.agrupar0.total[0]['colspan']-1}" style="padding: 5px 10px; font-size: 1.1em; font-weight: bold;">Sub-total</td>
						{foreach from=$i.agrupar0.total key=totalKey item=totalItem}
							{if $totalKey eq 0}
								<td style="text-align: right; font-size: 1.2em; font-weight: bold;">{$totalItem.total}</td>
							{else}
								<td colspan="{$totalItem.colspan}" style="text-align: right; font-size: 1.1em; font-weight: bold;">{$totalItem.total}</td>
							{/if}
						{/foreach}
						</tr>
					{/if}
					
					{if $i.agrupar0.nome}
					{$linhas = $linhas + 28}									
					<thead>
						{if $tbodykey}
							<tr>
								<td colspan="100"><div <div style="margin-top: -2px; height: 2px; width: 100%; background-color: #ABABAB;"></div></td>
							</tr>
						{/if}
						<tr>
							<td colspan="100"><h3 style="margin-bottom: 0px; margin-top: 0px; margin-left: 5px;">{$i.agrupar0.nome}  </h3></td>
						</tr>
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
					</thead>
					{/if}
					
					{if $i.agrupar1.nome}					
					<thead class="theadagrupamento">
						<tr>
							<td colspan="100"><h4 style="margin-bottom: 0px; margin-top: 5px; margin-left: 18px; ">{$i.agrupar1.nome}</div></h4></td>
						</tr>
						<tr>
						{$linhas = $linhas + 17}
						{if $thead}
						{$linhas = $linhas + 17}
						{/if}
						{foreach from=$thead key=theadKey item=hi}
							{if $gridColunasFixo and $theadKey < $gridColunasFixo} {continue} {/if}							
							<th class="ui-widget-header ui-corner-all {$hi.class}" {$hi.extra} style="text-align: {$hi.align};{if $hi.ordem > 0} padding-right: 16px;{/if}{$hi.style}"{if $hi.id} onclick="filtroOrdem(event, this, '{$gridId}', '{$hi.id}'); return false;"{/if}>  {if $hi.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $hi.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{$hi.value}{if $i.editar == 1}<div class="icone ui-icon ui-icon-pencil" style="margin-left: -4px;" onclick="gEditarColuna('{$i.id}'); event.stopPropagation(); event.preventDefault(); return false;"></div>&nbsp;{/if}</th>
						{/foreach}
						</tr>
					</thead>
					{/if}
					<tr id="{$i.pk.pkCampo}" {if ($tbodykey mod 2) eq 0} style="background-color: #F4F4F4;"{/if}>
					{$linhas = $linhas + 12}
					{foreach from=$i.dados key=id item=ii}						
						{if $i.colunaAgrupar[$id] == 1 or $i.colunaAgrupar[$id] == -1}						
							<td{if $i.colunaAgrupar[$id] == 1} rowspan="{$i.agrupar0.linhas}"{/if} style="padding: 0 5px; white-space: normal; cursor: auto; font-size:9px; text-align: {$i.colunaAlinhar[$id]};{$i.style[$id]}">{$ii}  </td>
						{/if}
					{/foreach}
					</tr>
					
					{if !$i.agrupar0.nome and $i.agrupar0.total and $tbodykey != 0}
					{$linhas = $linhas + 1}
						<tr>
							<td colspan="{$i.agrupar0.total[0]['colspan']-1}" style="padding: 5px 10px; font-size: 1.3em; font-weight: bold;">Sub-total</td>
						{foreach from=$i.agrupar0.total key=totalKey item=totalItem}
							{if $totalKey eq 0}
								<td style="text-align: right; font-size: 1.2em; font-weight: bold;">{$totalItem.total}</td>
							{else}
								<td colspan="{$totalItem.colspan}" style="text-align: right; font-size: 1.2em; font-weight: bold;">{$totalItem.total}</td>
							{/if}
						{/foreach}
						</tr>
					{/if}
				{/if}								
			{/foreach}
			</table>
		</tbody>
		<tfoot>
			{foreach from=$tfoot item=i}
				{$tfoot}
			{/foreach}
		</tfoot>
	</table>
	{$rodapeHTML}
</div>
