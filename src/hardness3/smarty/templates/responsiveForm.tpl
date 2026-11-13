{if $formId}<form id="{$formId}" class="responsive-form formFormulario tw-container tw-mx-auto tw-max-w-full" navSql="{$navSql}">{/if}
<div style="position: relative;" class="formFormatoEditor">
    <div class="responsive-form-flex tw-flex tw-flex-wrap tw-p-1 tw-@container">
        {foreach from=$table key=id item=j}
			{if $j.type eq 'button' || $j.type eq 'submit'}
			<div class="formEditor formEditor-{$j.id} tw-flex-none tw-basis-auto tw-my-2 tw-px-1 tw-box-border {$j.columns}" style="{if $j.hidden eq 1} display: none;{/if}" formEditorID="{$j.id}" formEditorHidden="{$j.hidden}">
			{else}
			<div class="formEditor formEditor-{$j.id} tw-flex-none tw-basis-auto tw-my-2 tw-px-1 tw-box-border {$j.columns}" style="{if $j.hidden eq 1} display: none;{/if}" formEditorID="{$j.id}" formEditorHidden="{$j.hidden}">
			{/if}
				{$j.label}
				<div>{$j.value}</div>
			</div>
        {/foreach}
        {$tableExtra}
    </div>
</div>
{$formularioID}
{if $formId}</form>{/if}
<div class="idTabela" tabela="{$form_tabela}" tipo="form"></div>