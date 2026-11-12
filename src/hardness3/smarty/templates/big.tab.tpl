{if $formId}<form id="{$formId}">{/if}
<div class="tabs">
   <ul class="ui-tabs-nav-big">
		{foreach from=$tabs item=i}
		  <li><a href="#{$i.tabId}">{$i.title}</a></li>
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