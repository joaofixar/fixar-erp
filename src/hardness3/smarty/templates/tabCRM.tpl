{if $formId}<form id="{$formId}">{/if}
<div class="tabs">
   <ul>
		{foreach from=$tabs item=i}
		  {if $i.title!='CRM' && $i.title!='+' && $i.title!='ERP'}<li id="{$i.tabId}-li" tabindex="{$i.count}"><a href="#{$i.tabId}">{$i.title}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a><a href="#" onclick="closeTab('{$i.tabId}'); return false;" style="cursor: pointer; margin: -1px -3px 0px -35px;">
		  <span class="ui-icon ui-icon-closethick">close</span>
		  </a></li>{/if}
		  {if $i.title=='+'}<li tabindex="{$i.count}"><a href="#" onclick="newTab(); return false;" style="cursor: pointer; margin: -1px -4px 0px -5px;">
		  <span class="ui-icon ui-icon-plusthick">add</span>
		  </a></li>{/if}
		  {if $i.title=='CRM' || $i.title=='ERP'}<li tabindex="{$i.count}"><a href="#{$i.tabId}">{$i.title}</a></li>{/if}
		{/foreach}	
   </ul>
	{foreach from=$tabs item=i}
		<div id="{$i.tabId}">
		</div>
	{/foreach}
</div>
{if $botoesEsq or $botoesDir}
<div class="formButtons">
	<div class="formButtonsLeft">
 		{$botoesEsq}
	</div>
 	<div class="formButtonsRight">
 		{$botoesDir}
 	</div>
</div>
{/if}
{if $formId}</form>{/if}

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	shortcut.remove("Ctrl+Space");
	shortcut.add("Ctrl+Space",function() {
		newTab();
	});
		
	function closeTab(tabId){
		recalculaTabPos();
		var id = $('#' + tabId + '-li').data('posicao');
		$.getJSON('/crm/crm001/tab_func-ajax/closeTab/?ajax=true&tabId=' + encodeURIComponent(id) + '&callback=?', function(request) {
			if (request.code) {
				$('#' + tabId + '-li').remove();
				$('.tabs').tabs('select', id);
			} else {
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    		dialogConfirm('Erro', request.data, buttons);
			}
		});
	}

	function newTab(){
		$.getJSON('/crm/crm001/tab_func-ajax/newTab/?ajax=true&divId=' + encodeURIComponent('{$g.divId}') + '&divIdRoot=' + encodeURIComponent('{$g.divIdRoot}') + '&callback=?', function(request) {
			if (request.code) {
				//divRefresh($('#' + '{$g['divId']}'), true);
				$('body').append(request.data);
			} else {
				var buttons = { "Fechar": function() { $(this).dialog("destroy").remove(); } }
	    		dialogConfirm('Erro', request.data, buttons);
			}
		});
	}
	
	function recalculaTabPos() {
		var x = 1;
		var $tab = $('.ui-tabs-nav:eq(0) li:eq(0)');
		while (x) {
		    if ($tab.size() == 0) { 
		        x = false; 
		    } else {
		        x++;
		        $tab.data('posicao', x-3);
		        $tab = $tab.next();
		    }
		}
	}	
}</script></div>