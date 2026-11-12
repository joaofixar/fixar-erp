{if $formId}<form id="{$formId}" class="formFormulario" navSql="{$navSql}" orderSql="{$orderSql}">{/if}
<table {$tableAlign} style="position: relative; margin-top: 4px;" cellpadding="0" cellspacing="0">
	<tbody>						
		{foreach from=$table key=id item=j}
			{if $j.align eq 'right'}
				<span><span class="form-label idLabel-{$id}">{$j.label}</span><span class="idForm" idform="{$id}">{$j.value}</span>
			{elseif $id eq 0}
				{if $j.type eq 'hidden'}
					<tr><td class="form-label idLabel-{$id}"><span></span></td><td class="idForm" idform="{$id}">{$j.value}
				{elseif $j.type eq 'titulo'}
					<tr class="form-titulo"><td colspan="2" class="idLabel-{$id}">{$j.label}</td>
				{else}
					<tr><td class="form-label"><span class="idLabel-{$id}">{$j.label}</span></td><td  class="idForm" idform="{$id}">{$j.value}
				{/if}
			{else}
				{if $j.type eq 'hidden'}
					</td></tr><tr><td><span></span></td><td class="idForm" idform="{$id}">{$j.value}
				{elseif $j.type eq 'titulo'}
					<tr class="form-titulo"><td colspan="2" class="idLabel-{$id}">{$j.label}</td>
				{else}
					</td></tr><tr><td class="form-label"><span class="idLabel-{$id}">{$j.label}</span></td><td class="idForm" idform="{$id}">{$j.value}
				{/if}
			{/if}
		{/foreach}
		{$tableExtra}							
	</tbody>
</table>
{$formularioID}
{if $formId}</form>{/if}
<div class="idTabela" tabela="{$form_tabela}" tipo="form"></div>