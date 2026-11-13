{if $formId}<form id="{$formId}" class="formFormulario" navSql="{$navSql}" orderSql="{$orderSql}">{/if}
<div class="tabs">
   <ul>
		{foreach from=$tabs item=i}
			{if $i.hide}
		  		<li style="display: none;" tabindex="{$i.count}"><a href="#{$i.tabId}">{$i.title}</a></li>
		  	{else}
		  		<li tabindex="{$i.count}"><a href="#{$i.tabId}">{$i.title}</a></li>
		  	{/if}
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