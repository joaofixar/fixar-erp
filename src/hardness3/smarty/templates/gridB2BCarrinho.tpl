<div id='container' class='container'>
	<tbody>
		{foreach from=$tbody key=tbodykey item=i}
			{if is_numeric($tbodykey)}
				<div class='row1' id="row{$tbodykey}">			
					{assign var="count" value="0"}
					{foreach from=$i.dados key=id item=ii}																		
						{assign var="`$thead.$count.value`" value="$ii"}
						{if $thead.$count.value == T269_Id || $thead.$count.value == Quantidade} {assign var="count" value="`$count+1`"} {continue} {/if}
						{assign var="count" value="`$count+1`"}
						{if $id==2}
							<div style='float:left;width:250px;'>{$ii}</div>
						{else}
							<div style='float:left;padding-left:10px;'>{$ii}</div>
						{/if}
					{/foreach}
					<div style='float:left;width:80px;padding-left:10px;'><input type='number' id="quantidade{$tbodykey}" value="{$Quantidade}" class='form-control'></div>
					<div style='float:left;padding-left:10px;'><button type="button" id="gravar" idProduto="{$i.pk.pkCampo}" class="btn btn-success" campoQuantidade="quantidade{$tbodykey}">Ok</button></div>
					<div style='float:left;padding-left:10px;'><button type="button" id='excluir' idProduto="{$i.pk.pkCampo}" class="btn btn-danger" row="row{$tbodykey}">Excluir</button></div>			
				</div>
			{/if}			
		{/foreach}

		{if $botaoOrcaPedido=='1'}
			<div style='float:left;margin-left:10px;margin-top:10px;'><button type="button" id='gerarOrcamento' class="btn btn-danger">Gerar Orçamento</button></div>	
		{elseif $botaoOrcaPedido=='2'}
			<div style='float:left;margin-left:10px;margin-top:10px;'><button type="button" id='gerarPedido' class="btn btn-danger">Gerar Pedido</button></div>
		{/if}
	</tbody>
</div>
