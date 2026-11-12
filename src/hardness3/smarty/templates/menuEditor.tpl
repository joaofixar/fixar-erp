<ul id="barra" class="menubar ui-widget-header ui-corner-bottom">
	{foreach from=$menuArray key=menu item=menuA}
		{if $menuArrayTotais[$menu]}
			<li class="menuvertical"><a href="#" class="ui-corner-top">{$menu}</a>
		{else}
			<li class="menuvertical"><a href="#" class="ui-corner-top" style="color: gray;">{$menu}</a> 
		{/if}
			<ul class="menu ui-corner-bottom ui-corner-tr">
				{foreach from=$menuA key=menu2 item=menuA2}
					{if $menuArrayPermissao[$menu][$menu2]}
						<li class="submenu"><a href="#" class="ui-corner-tr" onclick="menuPermissao(this); return false;" style="font-weight: bold;" ativo="1" c029_id="{$menuArrayIds[$menu][$menu2][0]}" c021_id="{$menuArrayIds[$menu][$menu2][1]}">{$menu2}<span style="float: right; border-bottom: 1px solid #1D5987;" c029_id="{$menuArrayIds[$menu][$menu2][0]}" onclick="perfisPermissao(this, '{$menu} - {$menu2}'); event.stopPropagation(); return false;">{$menuArrayTotaisPerfis[$menu][$menu2]}</span></a></li>
					{else}
						<li class="submenu"><a href="#" class="ui-corner-tr" onclick="menuPermissao(this); return false;" style="color: gray;" ativo="0" c029_id="{$menuArrayIds[$menu][$menu2][0]}" c021_id="{$menuArrayIds[$menu][$menu2][1]}">{$menu2}<span style="float: right; border-bottom: 1px solid #1D5987;" c029_id="{$menuArrayIds[$menu][$menu2][0]}" onclick="perfisPermissao(this, '{$menu} - {$menu2}'); event.stopPropagation(); return false;">{$menuArrayTotaisPerfis[$menu][$menu2]}</span></a></li>
					{/if}
				{/foreach}
			</ul>
	   </li>
	{/foreach}
	<li class="menuvertical" style="margin-top: 24px;"><span onclick="window.location = '/logout';" style="padding-left: 2px; cursor: pointer; font-weight: normal;">Sair</span></li>
</ul>
<script>
	function menuPermissao(elem) {
		var $elem = $(elem);
		if ($elem.attr('ativo') == '1') {
			selected = 'N';
			$elem.css({
				'font-weight': 'normal',
				'color': 'gray'
			}).attr('ativo', 0);
		} else {
			selected = 'S';
			$elem.css({
				'font-weight': 'bold',
				'color': '#1D5987'
			}).attr('ativo', 1);
		}
		if (isempty($elem.attr('c029_id'))) {
			alert("Está opção do menu não possui permissão.\nRemover do menu.php nas personalizações ou criar permissão para ele (pessoal do desenvolvimento).");
		} else {
			$.getJSON('/cad/cad032/grid_func-ajax/gravarCampo/?ajax=true&C029_Id=' + encodeURIComponent($elem.attr('c029_id')) + '&C021_Id=' + encodeURIComponent($elem.attr('c021_id')) + '&selected=' + selected + '&callback=?', function(request) {
				//
			});
		}
	}
	function perfisPermissao(elem, nome) {
		var $elem = $(elem);
		abrirJanela(false, divIdRootAberto(), '', menuId('cad031'), 'Item: ' + nome, '.: ' + nome + ' :.', '/cad/cad032/content/cad032ContentPerfisItem/', '&acaoId=' + $elem.attr('c029_id'), [850,450], false, false, 'center');
	}
</script>