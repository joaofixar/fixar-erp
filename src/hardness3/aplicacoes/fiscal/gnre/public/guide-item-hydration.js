(function(){'use strict';
  var data=window.gnreGuideDraftDefinition,form=document.querySelector('[data-guide-draft-form]');if(!data||!form)return;
  var saved=data.saved||{},guide=data.guide||{},extras={};try{extras=JSON.parse(saved.GNRE003_Campos_Extras_JSON||'[]');}catch(e){extras={};}
  if(!Array.isArray(extras))extras=extras.gnre_campos_extras||[];
  if(Array.isArray(extras)){var indexed={};extras.forEach(function(x){if(x&&x.codigo!==undefined)indexed['additional_'+x.codigo]=x.valor||'';});extras=indexed;}
  /* Every persisted value is indexed by its rendered field name. The regular form mapping handles GNRE003/GNRE006; this supplements linked and JSON-backed values. */
  var values={payment_date:guide.payment_date||'',source_document_type:saved.GNRE003_Tipo_Documento_Origem||'',source_document_number:saved.GNRE002_Identificacao||'',source_document_description:saved.GNRE002_Descricao||''};
  Object.keys(extras).forEach(function(k){values[k]=extras[k];});
  Object.keys(values).forEach(function(name){var field=form.querySelector('[name="'+name+'"]');if(field&&values[name]!==''&&!field.value)field.value=values[name];});
  var rendered={};form.querySelectorAll('[name]').forEach(function(field){rendered[field.name]=true;});
  var missing=Object.keys(extras).filter(function(name){return extras[name]!==''&&!rendered[name];});
  if((saved.GNRE002_Identificacao||saved.GNRE003_Tipo_Documento_Origem)&&!rendered.source_document_number)missing.push('Documento de origem');
  if(missing.length){var warning=document.createElement('p');warning.className='error';warning.setAttribute('role','alert');warning.textContent='Há dados persistidos que não são compatíveis com o snapshot vinculado: '+missing.join(', ')+'. Revise a configuração antes de salvar.';form.insertBefore(warning,form.firstChild);var save=form.querySelector('[data-guide-draft-save]');if(save)save.disabled=true;form.dataset.guideItemHydrationIncompatible='true';}
  form.dataset.guideItemHydrated='true';
}());
