<ul id="barra" class="menubar ui-widget-header ui-corner-bottom" style="{if $modoDesenvolvedor} background-color: rgb(232 55 4); {/if}">
	<li id="menuInicio" class="menuvertical">
		<a href="/erp/" class="ui-corner-top">Início</a>
	</li>
	{foreach from=$menuArray key=menu item=menuA}
		<li class="menuvertical"><a href="#" class="ui-corner-top">
				{if $menu == 'Cadastros'}
					<strong><u>C</u></strong>adastros
				{elseif $menu == 'Vendas'}
					<strong><u>V</u></strong>endas
				{elseif $menu == 'Estoque'}
					<strong><u>E</u></strong>stoque
				{elseif $menu == 'Financeiro'}
					<strong><u>F</u></strong>inanceiro
				{elseif $menu == 'Fiscal'}
					Fisca<strong><u>l</u></strong>
				{elseif $menu == 'Comex'}
					Come<strong><u>x</u></strong>
				{elseif $menu == 'Contábil'}
					Contá<strong><u>b</u></strong>il
				{elseif $menu == 'Gerencial'}
					<strong><u>G</u></strong>erencial
				{elseif $menu == 'Outros'}
					Ou<strong><u>t</u></strong>ros
				{elseif $menu == 'Configuração'}
					C<strong><u>o</u></strong>nfiguração
				{else}
					{$menu}
				{/if}
			</a>
			<ul class="menu ui-corner-bottom ui-corner-tr"> 
				{foreach from=$menuA key=menu2 item=menuA2}
					{if is_array($menuA2)}
						<li class="submenu">
							{* color: rgb(68, 118, 164); *}
							<span style="float: right; font-weight: bolder; margin: 10px 6px 0px 0px; font-size: 0.9em; color: rgba(0,0,0,0.40);">{$menuA2[0]}</span>
							<a href="#" class="ui-corner-tr" onclick="{$menuA2[1]} return false;">{$menu2}</a>
						</li>
					{else}
						<li class="submenu"><a href="#" class="ui-corner-tr" onclick="{$menuA2} return false;">{$menu2}</a></li>
					{/if}
				{/foreach}
			</ul>
	   </li>
	{/foreach}
	<li id="menuSair" class="menuvertical"><span onclick="window.location = '/logout';" style="padding-left: 5px; cursor: pointer; font-weight: normal;">Sair</span></li>
</ul>
