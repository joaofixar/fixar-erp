(function () {
'use strict';
function create(root,id,options){
  const M=window.CotacoesModel,$=name=>root.querySelector('#cotacoes-'+name),controller=new AbortController();
  let metas={},plot=null,context=null,saving=false,disposed=false,error='';
  const escape=value=>String(value).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const key=c=>[c.mes,c.empresa,c.equipe,c.vendedor,c.representante,c.unidade].join('|');
  const scope=()=>options.scope(plot.p.month,plot.inTons?'toneladas':'reais');
  const value=()=>{const n=Number(metas[key(scope())]);return Number.isFinite(n)&&n>0?n:null;};
  const format=n=>plot.inTons?Number(n).toLocaleString('pt-BR',{maximumFractionDigits:3})+' t':Number(n).toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
  function draw(){
    const p=plot.p,goal=value(),expected=goal!==null&&p.days?goal*p.elapsed/p.days:null,difference=expected===null?null:p.real-expected;
    const note=error||(goal===null?'Informe sua meta para este mês e escopo.':(100*p.real/goal).toLocaleString('pt-BR',{maximumFractionDigits:1})+'% atingido'+(p.elapsed&&difference!==null?' · '+format(Math.abs(difference))+(difference>=0?' acima':' abaixo')+' do ritmo esperado.':p.days?' · '+format(goal/p.days)+' por dia útil.':''));
    $('goalCard').innerHTML='<span>Meta do mês'+(plot.inTons?' (t)':'')+'</span><strong>'+escape(error?'Indisponível':goal===null?'Não definida':format(goal))+'</strong><small>'+escape(note)+'</small><button type="button" class="cotacoes-goal-edit secondary" data-goal="edit"'+(error?' disabled':'')+'>'+(goal===null?'Informar meta':'Editar meta')+'</button>';
    const series=[{name:'Realizado',color:'#2860dc',values:plot.real},{name:'Projeção',color:'#16a477',values:plot.forecast,dashed:true}];
    if(goal!==null)series.push({name:'Meta acumulada',color:'#b45309',values:M.goalSeries(goal,p.days),goal:true});
    $('goalLegend').hidden=goal===null;options.chart(p.dates,series,plot.inTons?'toneladas':true);
  }
  function open(){
    if(!plot||saving||error)return;context=scope();const goal=value(),tons=context.unidade==='toneladas';
    $('goalTitle').textContent='Meta de '+context.mes.split('-').reverse().join('/');$('goalScope').textContent=options.label();
    $('goalUnit').textContent=tons?'Valor em toneladas':'Valor em R$';
    $('goalInput').value=goal===null?'':goal.toLocaleString('pt-BR',{minimumFractionDigits:tons?3:2,maximumFractionDigits:tons?3:2});
    $('goalInput').placeholder=tons?'Ex.: 1.500,000':'Ex.: 5.000.000,00';$('goalRemove').disabled=goal===null;
    $('goalError').hidden=true;$('goalDialog').showModal();$('goalInput').focus();
  }
  async function save(remove){
    if(saving||!context)return;const amount=remove?null:M.parseGoal($('goalInput').value,context.unidade);
    if(!remove&&amount===null){$('goalError').textContent='Informe uma meta válida, maior que zero.';$('goalError').hidden=false;return;}
    const sent=Object.assign({},context);saving=true;$('goalError').hidden=true;
    ['goalSave','goalRemove','goalCancel','goalInput'].forEach(name=>$(name).disabled=true);$('goalSave').textContent='Salvando…';
    try{
      const response=await fetch('data.php?action=saveCotacoesGoal&id='+encodeURIComponent(id),{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.assign({},sent,{valor:amount})),signal:controller.signal});
      const result=await response.json().catch(()=>({}));if(!response.ok)throw new Error(result.error||'Não foi possível salvar a meta.');
      if(result.chave!==key(sent)||result.valor!==null&&(!Number.isFinite(result.valor)||result.valor<=0))throw new Error('Não foi possível validar a meta salva.');
      if(disposed)return;if(result.valor===null)delete metas[result.chave];else metas[result.chave]=result.valor;
      if(plot&&key(scope())===result.chave)draw();$('goalDialog').close();
    }catch(problem){if(!disposed){$('goalError').textContent=problem.message;$('goalError').hidden=false;}}
    finally{saving=false;if(!disposed){['goalSave','goalRemove','goalCancel','goalInput'].forEach(name=>$(name).disabled=false);$('goalSave').textContent='Salvar';}}
  }
  $('goalForm').addEventListener('submit',event=>{event.preventDefault();save(false);});
  $('goalRemove').addEventListener('click',()=>save(true));$('goalCancel').addEventListener('click',()=>{$('goalDialog').close();});
  $('goalDialog').addEventListener('cancel',event=>{if(saving)event.preventDefault();});
  $('goalDialog').addEventListener('close',()=>{context=null;const button=$('goalCard')&&$('goalCard').querySelector('button');if(button&&button.isConnected)button.focus();});
  return {open,read:data=>{if(data.metasUsuario)metas=Object.assign({},data.metasUsuario);error=data.metasErro||'';},render:next=>{plot=next;draw();},getValue:()=>plot?value():null,getLabel:()=>plot&&value()!==null?format(value()):'Não definida',destroy:()=>{disposed=true;controller.abort();if($('goalDialog').open)$('goalDialog').close();}};
}
window.DashboardCotacoesGoal={create};
}());
