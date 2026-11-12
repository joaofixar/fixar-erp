<div id='container' class='container'>
	<tbody>
		{foreach from=$tbody key=tbodykey item=i}
			{if is_numeric($tbodykey)}
				<div class='row'> 
				
					{foreach from=$i.dados key=id item=ii}
						{if $id==1}
							{$T269_Id = $ii}
							{continue}		
						{/if}		
						{if $id==5}
							 {$D009_Quantidade_Estoque = $ii}
							 {continue}
						{/if}
						{if $id==6 && $mostrarPreco=='N'}
							{continue}
						{/if}	
						{$ii}<br/>			
					{/foreach}						
					{if $D009_Quantidade_Estoque<1}
						<p class='centro'><button type="button" class="btn btn-danger">Indisponível</button></p>
					{else}					
						{if $T269_Id > 0}						
							<p class='centro'><button type="button" class="btn btn-info">Carrinho</button></p>
						{else}
							<p class='centro'><button type="button" class="btn btn-success" id="{$i.pk.pkCampo}">Comprar</button></p>		
						{/if}
					{/if}		
				</div>
			{/if}
		{/foreach}
	</tbody>
</div>
