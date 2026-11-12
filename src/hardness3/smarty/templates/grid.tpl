{if $gridPersonalizacao}<script type="text/javascript">	var paramGrid{$gridId} = {$gridPersonalizacao};</script>{/if}
<div class="filtro">{$filtro}</div>
<div gridsql="{$gridSql}">
	{if !$gridOptNoShow}
		<div class="gridOptTop {$class}" style="margin-top: -25px; position: absolute; right: {if $gridOptRight}{$gridOptRight};{else}17px;{/if}">
			<!--<div class="icone" style="margin-right: 5px;" onclick="gridRefresh(this); return false;">
		   		<span title="Atualizar Dados" class="ui-icon ui-icon-refresh"></span>
		   	</div>
		   
			<div class="icone icone-grid-email" style="margin-right: 5px;" onclick="acoesGrid(this, false, false, false, true); return false;">
				<span title="Enviar via e-mail" class="ui-icon ui-icon-mail-closed"></span>
			</div>
			<div class="icone icone-grid-imprimir" style="margin-right: 5px;" onclick="acoesGrid(this, false, false, true, false); return false;">
				<span title="Imprimir" class="ui-icon ui-icon-print"></span>
			</div>
			<div class="icone icone-grid-pdf" style="margin-right: 5px;" onclick="acoesGrid(this, false, false, false, false, true, true); return false;">
				<span title="Gerar PDF" class="ui-icon ui-icon-document"></span>
			</div>
			<div class="icone icone-grid-csv" style="margin-right: 5px;" onclick="acoesGrid(this, false, true, false, false); return false;">
				<span title="Gerar CSV" class="ui-icon ui-icon-calculator"></span>
			</div>
			{if $mostrarAgrupamento}
				<div class="icone" style="margin-right: 5px;" onclick="abrirAgrupamento('{$agrupamentoDados}'); return false;">
					<span title="Agrupar Resultados" class="ui-icon ui-icon-plusthick"></span>
				</div>
			{/if}
			-->

			<!--<div class="icone" title="Personalizar do Grid" style="background-image: url('/hardness3/static/img/icone_ferramentas.png'); margin-right: 5px;" onclick="gridPersonalizacao(paramGrid{$gridId}); return false;"></div>-->
			<!--<div class="icone" title="Limpar Filtro/Ordenação" style="background-image: url('/hardness3/static/img/icone_info.png'); margin-right: 5px;" onclick="showInfoJanela($(this).closest('.ui-dialog-content')); return false;"></div>-->
			<!--<div class="icone" title="Limpar Filtro/Ordenação" style="background-image: url('/hardness3/static/img/icone_limpar.png'); margin-right: 5px;" onclick="acoesGrid(this, true); return false;"></div>-->
			<!--{if $gridSql}<div class="icone" title="SQL" style="background-image: url('/hardness3/static/img/icone_sql.png'); margin-right: 5px;" onclick="dialogConfirm('Debug SQL', '{$gridSql}'); return false;"></div>{/if}-->
			{if $gridTable}
				<!-- <div class="icone" style="margin-right: 5px;" onclick="gridAudit(this, '{$gridTable}'); return false;"><span title="Auditoria" class="ui-icon ui-icon-search"></span></div> -->
			{/if}

			<div class="btn-group">
				<button class="btn btn-mini" onclick="gridRefresh(this); return false;">Atualizar</button>
				<button data-toggle="dropdown" class="btn btn-mini dropdown-toggle"><span class="caret"></span></button>
				<ul class="dropdown-menu pull-left" style="margin-left: {if $visualizacao}-76px{else}-80px{/if}; margin-top: -5px;">
					<li><a href="#" onclick="acoesGrid(this, false, false, true, false); return false;" class="acao-grid-imprimir">Imprimir</a></li>
					<li><a href="#" onclick="acoesGrid(this, false, false, false, true); return false;" class="acao-grid-email">Enviar via e-mail</a></li>
					<li><a href="#" onclick="acoesGrid(this, false, false, false, false, true, true); return false;" class="acao-grid-pdf">Gerar PDF</a></li>
					<li><a href="#" onclick="acoesGrid(this, false, true, false, false); return false;" class="acao-grid-csv">Gerar CSV</a></li>
					<li class="divider"></li>
					<li {if !$mostrarAgrupamento}class="disabled"{/if}><a href="#" {if $mostrarAgrupamento}onclick="abrirAgrupamento('{$agrupamentoDados}'); return false;"{/if}>Agrupar resultados</a></li>
					<li {if !$gridTable}class="disabled"{/if}><a href="#" {if $gridTable}onclick="gridAudit(this, '{$gridTable}'); return false;"{/if}>Auditoria</a></li>
					<!--<li class="divider"></li>
					<li class="dropdown-submenu pull-left">
						<a href="#" tabindex="-1">Visualização</a>
						<ul class="dropdown-menu">
							<li><a href="#" tabindex="-1" onclick="$('#modal-filtroBasico').data('modofiltro', 1); $(this).find('i').show(); $(this).parent().next().find('i').hide();"><i class="icon-ok" style="margin-right: 6px;"></i>Padrão</a></li>
							<li><a href="#" tabindex="-1" onclick="$('#modal-filtroBasico').data('modofiltro', 0); $(this).find('i').show(); $(this).parent().prev().find('i').hide();"><i class="icon-ok" style="margin-right: 6px; display: none;"></i>Modelo XYZ</a></li>
						</ul>
					</li>-->
				</ul>
			</div>

		</div>
	{/if}
	{if $gridColunasFixo}
		<table class="gridFixo" cellspacing="1" style="width: 1%; position: absolute; z-index: 2">
			{if !$tbody[0].agrupar0.nome}
			<thead class="theadnormal">
				{$primeiraLinhaGridHead}
				<tr>
					{foreach from=$thead key=theadKey item=i}
						{if $gridColunasFixo and $theadKey >= $gridColunasFixo} {continue} {/if}
						<th class="ui-widget-header ui-corner-all {$i.class}" {$i.extra} style="text-align: {$i.align};{if $i.ordem > 0} padding-right: 16px;{/if}{$i.style}"{if $i.id} onclick="filtroOrdem(event, this, '{$gridId}', '{$i.id}'); return false;"{/if}>{if $i.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $i.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{if $i.editar == 1}<div class="icone ui-icon ui-icon-pencil" style="margin-left: -4px;" onclick="gEditarColuna('{$i.id}'); event.stopPropagation(); event.preventDefault(); return false;"></div>&nbsp;{/if}{$i.value}</th>
					{/foreach}
				</tr>
			</thead>
			{/if}
			<tbody>
				{foreach from=$tbody key=tbodykey item=i}
					{if is_numeric($tbodykey)}
					
						{if $i.agrupar0.nome}
						<thead class="theadagrupamento">
							<tr>
								<td colspan="100"><h2 style="margin-bottom: 0px; margin-top: 3px; margin-left: 7px;">{$i.agrupar0.nome}</h2></td>
							</tr>
							<tr>
							{foreach from=$thead key=theadKey item=hi}
								{if $gridColunasFixo and $theadKey >= $gridColunasFixo} {continue} {/if}
								<th class="ui-widget-header ui-corner-all {$hi.class}" {$hi.extra} style="text-align: {$hi.align};{if $hi.ordem > 0} padding-right: 16px;{/if}{$hi.style}"{if $hi.id} onclick="filtroOrdem(event, this, '{$gridId}', '{$hi.id}'); return false;"{/if}>{if $hi.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $hi.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{if $i.editar == 1}<div class="icone ui-icon ui-icon-pencil" style="margin-left: -4px;" onclick="gEditarColuna('{$i.id}'); event.stopPropagation(); event.preventDefault(); return false;"></div>&nbsp;{/if}{$hi.value}</th>
							{/foreach}
							</tr>
						</thead>
						{/if}
						
						<tr id="{$i.pk.pkCampo}" class="color{math equation="(x + 1)" x=$tbodykey mod 2}{if $i.linha.class} {$i.linha.class}{/if}">
						{foreach from=$i.dados key=id item=ii}
							{if $gridColunasFixo and $id >= $gridColunasFixo} {continue} {/if}
							{if $i.colunaAgrupar[$id] == 1 or $i.colunaAgrupar[$id] == -1}
								<td{if $i.colunaAgrupar[$id] == 1} rowspan="{$i.agrupar0.linhas}"{/if}{if $i.colunaEditar[$id]} onclick="gEditarCampo('{$tbody.keys[$id]}', $(this), '{$i.colunaEditar[$id][0]}', '{$i.colunaEditar[$id][1]}', '{$i.colunaEditar[$id][2]}', '{$i.colunaEditar[$id][3]}'); return false;"{/if} class="{if $i.colunaEditar[$id]}campoEditavel{/if}" style="text-align: {$i.colunaAlinhar[$id]};{$i.style[$id]}">{if $i.colunaEditar[$id]}<span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;"></span><span style="padding-right: 16px;">{$ii}</span></span>{else}{$ii}{/if}</td>
							{/if}
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
	{/if}
	<table class="grid" cellspacing="1" style="margin-top: 2px" bgcolor="#EDE7DE">
		{if !$tbody[0].agrupar0.nome}
		<thead class="theadnormal">
			{$primeiraLinhaGridHead}
			<tr>
				{foreach from=$thead key=theadKey item=i}
					{if $gridColunasFixo and $theadKey < $gridColunasFixo} {continue} {/if}
					<th class="ui-widget-header ui-corner-all {$i.class}" {$i.extra} style="text-align: {$i.align};{if $i.ordem > 0} padding-right: 16px;{/if}{$i.style}"{if $i.id} onclick="filtroOrdem(event, this, '{$gridId}', '{$i.id}'); return false;"{/if}>{if $i.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $i.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{if $i.editar == 1}<div class="icone ui-icon ui-icon-pencil" style="margin-left: 1px;" onclick="gEditarColuna('{$i.id}'); event.stopPropagation(); event.preventDefault(); return false;"></div>&nbsp;{/if}{$i.value}</th>
				{/foreach}
			</tr>
		</thead>
		{/if}
		<tbody>
			{foreach from=$tbody key=tbodykey item=i}
				{if is_numeric($tbodykey)}
				
					{if $i.agrupar0.nome and $i.agrupar0.total and $tbodykey != 0}
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
				
					{if $i.agrupar0.nome}
					<thead class="theadagrupamento">
						{if $tbodykey}
							<tr>
								<td colspan="100"><div <div style="margin-top: 1px; height: 1px; width: 100%; background-color: #ABABAB;"></div></td>
							</tr>
						{/if}
						<tr>
							<td colspan="100"><h2 style="margin-bottom: 0px; margin-top: {if $tbodykey} 15px; {else} 5px; {/if} margin-left: 7px; ">{$i.agrupar0.nome}</div></h2></td> <!--({$i.agrupar0.linhas})-->
						</tr>
						{if $i.agrupar1.nome}
							<tr>
								<td colspan="100"><div <div style="margin-top: 1px; height: 1px; width: 100%; background-color: #ABABAB;"></div></td>
							</tr>
						{/if}
						{if !$i.agrupar1.nome}
							<tr>
							{foreach from=$thead key=theadKey item=hi}
								{if $gridColunasFixo and $theadKey < $gridColunasFixo} {continue} {/if}
								<th class="ui-widget-header ui-corner-all {$hi.class}" {$hi.extra} style="text-align: {$hi.align};{if $hi.ordem > 0} padding-right: 16px;{/if}{$hi.style}"{if $hi.id} onclick="filtroOrdem(event, this, '{$gridId}', '{$hi.id}'); return false;"{/if}>{if $hi.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $hi.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{$hi.value}{if $i.editar == 1}<div class="icone ui-icon ui-icon-pencil" style="margin-left: -4px;" onclick="gEditarColuna('{$i.id}'); event.stopPropagation(); event.preventDefault(); return false;"></div>&nbsp;{/if}</th>
							{/foreach}
							</tr>
						{/if}
					</thead>
					{/if}
					
					{if $i.agrupar1.nome}
					<thead class="theadagrupamento" bgcolor='white'>
						<tr>
							<td colspan="100"><h3 style="margin-bottom: 0px; margin-top: 5px; margin-left: 18px; ">{$i.agrupar1.nome}</div></h3></td>
						</tr>
						<tr>
						{foreach from=$thead key=theadKey item=hi}
							{if $gridColunasFixo and $theadKey < $gridColunasFixo} {continue} {/if}
							<th class="ui-widget-header ui-corner-all {$hi.class}" {$hi.extra} style="text-align: {$hi.align};{if $hi.ordem > 0} padding-right: 16px;{/if}{$hi.style}"{if $hi.id} onclick="filtroOrdem(event, this, '{$gridId}', '{$hi.id}'); return false;"{/if}>{if $hi.ordem == 1}<div class="icone ui-icon ui-icon-circle-triangle-s" style="margin-left: -4px;"></div>&nbsp;{/if}{if $hi.ordem == 2}<div class="icone ui-icon ui-icon-circle-triangle-n" style="margin-left: -4px;"></div>&nbsp;{/if}{$hi.value}{if $i.editar == 1}<div class="icone ui-icon ui-icon-pencil" style="margin-left: -4px;" onclick="gEditarColuna('{$i.id}'); event.stopPropagation(); event.preventDefault(); return false;"></div>&nbsp;{/if}</th>
						{/foreach}
						</tr>
					</thead>
					{/if}
					
					<tr id="{$i.pk.pkCampo}" class="color{math equation="(x + 1)" x=$tbodykey mod 2}{if $i.linha.class} {$i.linha.class}{/if}">
					{foreach from=$i.dados key=id item=ii}
						{if $gridColunasFixo and $id < $gridColunasFixo} {continue} {/if}
						{if $i.colunaAgrupar[$id] == 1 or $i.colunaAgrupar[$id] == -1}
							<td{if $i.colunaAgrupar[$id] == 1} rowspan="{$i.agrupar0.linhas}"{/if}{if $i.colunaEditar[$id]} onclick="gEditarCampo('{$tbody.keys[$id]}', $(this), '{$i.colunaEditar[$id][0]}', '{$i.colunaEditar[$id][1]}', '{$i.colunaEditar[$id][2]}', '{$i.colunaEditar[$id][3]}'); return false;"{/if} class="{if $i.colunaEditar[$id]}campoEditavel{/if}" style="text-align: {$i.colunaAlinhar[$id]};{$i.style[$id]}">{if $i.colunaEditar[$id]}<span class="ui-icon ui-icon-pencil" style="margin-top: -1px; position: relative; float: left;"></span><span style="padding-right: 16px;">{$ii}</span></span>{else}{$ii}{/if}</td>
						{/if}
					{/foreach}
					</tr>

					{if $i.agrupar0.totalFinal}
						<tr>
							<td colspan="{$i.agrupar0.totalFinal[0]['colspan']-1}" style="padding: 5px 10px; font-size: 1.3em; font-weight: bold;">Sub-total</td>
						{foreach from=$i.agrupar0.totalFinal key=totalKey item=totalItem}
							{$campoValorTotal = $totalItem.colspan}
							{if $totalKey eq 0}
								<td style="text-align: right; font-size: 1.2em; font-weight: bold;">{$totalItem.total}</td>
							{else}
								<td colspan="{$totalItem.colspan-1}" style="text-align: right; font-size: 1.2em; font-weight: bold;">{$totalItem.total}</td>
							{/if}
						{/foreach}
						</tr>
					{/if}
					
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