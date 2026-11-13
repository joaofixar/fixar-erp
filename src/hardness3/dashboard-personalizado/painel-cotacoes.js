(function () {
'use strict';
function create(root,id,options) {
options=options||{};
const M=window.CotacoesModel, $=name=>name==='main'?root:root.querySelector('#cotacoes-'+name);
const controller=new AbortController();
let destroyed=false,loadRequest=null,comparisonRequest=null;
let diagnosticOpening=options.diagnosticoAbertura||'',firstDiagnosticOpening=diagnosticOpening;
const comparisonData=new Map(),customComparisons=[];
let trendCache=new WeakMap(),trendSequence=0,trendFrame=0,trendTimer=0;
const selectedCriteria=new Set();
const trendCriteria={
  count:{color:'#2860dc',unit:'count'},value:{color:'#d97706',unit:'value'},
  ticket:{name:'Ticket médio',color:'#9333ea',unit:'value'},sellers:{name:'Vendedores ativos',color:'#0891b2',unit:'count'},
  ia:{name:'Feito com IA',color:'#16a477',unit:'count'},conversion:{name:'Taxa de conversão',color:'#be185d',unit:'percent'},
  closed:{name:'Orçamentos fechados',color:'#4d7c0f',unit:'count'},toneladas:{name:'Volume de material',color:'#475569',unit:'toneladas'}
};
function updated(text){$('updatedAt').textContent=text;if(options.onUpdated)options.onUpdated(text);}

const esc=s=>String(s==null?'':s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const money=n=>n==null?'Não disponível':Number(n).toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
const compact=n=>n==null?'—':Math.abs(n)>=1e6?'R$ '+(n/1e6).toLocaleString('pt-BR',{maximumFractionDigits:1})+' mi':Math.abs(n)>=1000?'R$ '+(n/1000).toLocaleString('pt-BR',{maximumFractionDigits:1})+' mil':money(n);
const tons=n=>n==null?'Não disponível':Number(n).toLocaleString('pt-BR',{maximumFractionDigits:3})+' t';
const pct=n=>n==null?'—':n.toLocaleString('pt-BR',{maximumFractionDigits:1})+'%';
const date=s=>s.split('-').reverse().join('/');
const labels={orcamentos:'Orçamentos',pedidos:'Pedidos',notas:'Notas emitidas'};
const state={data:null,history:null,mode:'orcamentos',metric:'count',team:'all',company:'all',seller:'all',representative:'all',search:'',sort:'count',dir:-1,page:0,sequence:0,expanded:new Set(),projection:'current',projectionUnit:'reais',busy:false,referenceMonth:M.fmtDate(new Date()).slice(0,7),preset:'month'};
const goals=window.DashboardCotacoesGoal.create(root,id,{
  scope:(month,unit)=>({mes:month,empresa:state.company,equipe:state.team,vendedor:state.seller,representante:state.representative,unidade:unit}),
  label:()=>{const company=state.company==='all'?'Empresas consolidadas':(state.data.empresas.find(c=>String(c.id)===state.company)||{}).nome;return company+' · '+scopeName()+' · '+$('representativeFilter').selectedOptions[0].textContent;},
  chart:(dates,series,unit)=>svgChart('projectionChart',dates,series,unit)
});
const documentRoutes={orcamentos:['/ven/ven001/content/ven001ContentEditarOrcamento/','T003','Orçamento'],pedidos:['/ven/ven002/content/ven002content02/','T005','Pedido'],notas:['/ven/ven003/content/ven003contentIncluirNotaFiscal/','T007','Nota fiscal']};
let documentRequest=null,documentSequence=0,documentTrigger=null;
function clearDocument(){documentSequence++;if(documentRequest)documentRequest.abort();documentRequest=null;$('documentContent').textContent='';if(documentTrigger&&documentTrigger.isConnected)documentTrigger.focus();documentTrigger=null;}
async function openDocument(row){
  const type=state.mode,route=documentRoutes[type];
  if(!route||!state.data)return;
  const record=allRows(type).find(r=>String(r.id)===row.dataset.documentId&&String(r.empresaId)===row.dataset.documentCompany);
  if(!record)return;
  clearDocument();documentTrigger=row.querySelector('button');const sequence=++documentSequence;
  documentRequest=new AbortController();$('documentTitle').textContent=route[2]+' '+(record.numero||record.id);
  $('documentStatus').textContent='Carregando documento…';$('documentStatus').hidden=false;$('documentDialog').showModal();
  const params=new URLSearchParams({action:'cotacoesDocumentContext',id:id,documentoTipo:type,documentoId:record.id,documentoEmpresa:record.empresaId,f_dataInicio:state.data.periodo.inicio,f_dataFim:state.data.periodo.fim});
  try{
    const response=await fetch('data.php?'+params,{credentials:'same-origin',cache:'no-store',signal:documentRequest.signal}),context=await response.json();
    if(destroyed||sequence!==documentSequence)return;
    if(!response.ok)throw new Error(context.error||'Não foi possível abrir o documento.');
    const url=new URL(context.url,window.location.href);
    if(url.origin!==window.location.origin||url.pathname!==route[0]||context.tipo!==type||String(context.documentoId)!==String(record.id)||String(context.empresaSessao)!==String(record.empresaId)
      ||url.searchParams.get('acaoId')!==String(record.id)||url.searchParams.get('oneWindow')!=='true'||url.searchParams.get('tabela')!==route[1]
      ||Array.from(url.searchParams.keys()).sort().join(',')!=='acaoId,oneWindow,tabela')throw new Error('Não foi possível validar a abertura do documento.');
    const frame=document.createElement('iframe');frame.title=route[2]+' '+(record.numero||record.id);frame.style.visibility='hidden';
    frame.addEventListener('load',()=>{
      if(destroyed||sequence!==documentSequence)return;
      try{
        const native=frame.contentWindow;
        if(native.location.origin!==window.location.origin||!native.g||Number(native.g.usuarioAtual)!==Number(context.usuario)||Number(native.g.empresaAtual)!==Number(context.empresaSessao))throw new Error('A sessão do ERP mudou ou expirou. Feche esta janela e reabra o dashboard pelo sistema.');
        frame.style.visibility='visible';$('documentStatus').hidden=true;
      }catch(error){frame.remove();$('documentStatus').hidden=false;$('documentStatus').textContent=error.message;}
    });
    frame.src=url.href;$('documentContent').appendChild(frame);
  }catch(error){if(!destroyed&&sequence===documentSequence&&error.name!=='AbortError')$('documentStatus').textContent=error.message;}
}
function kpi(label,value,note,cls){return '<article class="commercial-kpi '+(cls||'')+'"><span>'+esc(label)+'</span><strong>'+esc(value)+'</strong>'+(note?'<small>'+esc(note)+'</small>':'')+'</article>';}
function allRows(type){return state.data?state.data.fontes[type]||[]:[];}
function filtered(type,history){const source=history?state.history:state.data;return M.select(history?source.fontes[type]||[]:allRows(type),state,source.vendedores,source.equipes);}
function tonsAvailable(){return state.data&&state.company!=='all'&&state.data.empresas.some(c=>String(c.id)===state.company&&c.toneladas);}
function teamList(){return M.teams(state.data.vendedores,state.data.equipes);}
function scopeName(){if(state.seller!=='all'){const v=state.data.vendedores.find(v=>String(v.id)===state.seller);return v?v.nome:'Vendedor '+state.seller;}const t=teamList().find(t=>t.id===state.team);return t?'Equipe · '+t.name:'Visão geral';}
function applyPreset(value){
  const period=M.monthPeriod(state.referenceMonth,value);if(!period)return;
  state.preset=value;$('dateFrom').value=period.inicio;$('dateTo').value=period.fim;
  $('year').value=state.referenceMonth.slice(0,4);
  const today=M.fmtDate(new Date()),year=state.referenceMonth.slice(0,4);
  $('months').innerHTML=['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'].map((name,index)=>{const month=year+'-'+String(index+1).padStart(2,'0'),active=month===state.referenceMonth;return '<button type="button" class="preset-btn'+(active?' active':'')+'" data-month="'+month+'" aria-pressed="'+active+'"'+(month>today.slice(0,7)?' disabled':'')+'>'+name+'</button>';}).join('');
  root.querySelectorAll('[data-preset]').forEach(b=>{const active=b.dataset.preset===value;b.classList.toggle('active',active);b.setAttribute('aria-pressed',String(active));});
  $('periodHint').textContent=date(period.inicio)+' a '+date(period.fim)+' · Os atalhos mostram os últimos dias do mês selecionado. No mês atual, o período termina hoje.';
}
function busy(value){state.busy=value;root.setAttribute('aria-busy',String(value));$('loading').hidden=!value;$('refresh').disabled=value;$('exportCsv').disabled=value;$('print').disabled=value;$('refresh').textContent=value?'Carregando…':'Aplicar';if(options.onBusy)options.onBusy(value||!state.data||!$('error').hidden);}
function api(projection,period,signal,types){
  const q=new URLSearchParams({action:'indicator',id:id||'',cotacoesDados:projection?'projecao':'periodo',f_dataInicio:period?period.inicio:$('dateFrom').value,f_dataFim:period?period.fim:$('dateTo').value});
  if(types){q.set('cotacoesDados','comparativo');q.set('f_cotacoesFontes',types.join(','));}
  if(diagnosticOpening)q.set('diagnosticoCarga',diagnosticOpening);
  const started=typeof performance!=='undefined'?performance.now():0;
  const result=window.DashboardProgressivo ? window.DashboardProgressivo.request('data.php?'+q,{signal:signal||loadRequest.signal}) : fetch('data.php?'+q,{credentials:'same-origin',cache:'no-store',signal:signal||loadRequest.signal}).then(async r=>{const d=window.DashboardSqlDiagnostics?await window.DashboardSqlDiagnostics.read(r,'data.php?'+q,started):await r.json();if(!r.ok)throw new Error(d.error||'Não foi possível carregar o painel.');return d;});
  return result.then(d=>{if(!d.painelCotacoes||!d.painelCotacoes.fontes)throw new Error('Atualize a configuração deste indicador com o JSON do Painel de Cotações.');return d;});
}
async function load(force){
  if(destroyed)return;
  if(!id){$('error').hidden=false;$('error').textContent='Abra este painel pelo indicador liberado no Hardness.';busy(false);return;}
  const diagnostics=window.DashboardSqlDiagnostics;
  if(diagnostics&&diagnosticOpening&&diagnosticOpening!==firstDiagnosticOpening)diagnostics.complete(diagnosticOpening,null,0,true);
  const opening=firstDiagnosticOpening||(diagnostics?diagnostics.begin({id:id,title:'Painel de Cotações',params:new URLSearchParams({f_dataInicio:$('dateFrom').value,f_dataFim:$('dateTo').value})}):'');
  firstDiagnosticOpening='';diagnosticOpening=opening;
  const seq=++state.sequence;if(loadRequest)loadRequest.abort();loadRequest=new AbortController();if(force){state.data=null;state.history=null;resetComparisons();}busy(true);$('error').hidden=true;$('salesView').hidden=true;$('projectionView').hidden=true;
  try{
    if(force||!state.data){const r=await api(false);if(seq!==state.sequence)return;state.data=r.painelCotacoes;goals.read(state.data);state.history=null;state.page=0;state.expanded.clear();updated('Atualizado em '+r.updatedAt);}
    if(state.mode==='projecao'&&!state.history){updated('Carregando histórico da projeção…');const r=await api(true);if(seq!==state.sequence)return;state.history=r.painelCotacoes;goals.read(state.history);updated('Atualizado em '+r.updatedAt);}
    if(seq===state.sequence){if(opening)diagnostics.phase(opening,'montando tela');render();if(opening)diagnostics.complete(opening);}
  }catch(e){if(opening)diagnostics.complete(opening,e.name==='AbortError'?null:e,0,e.name==='AbortError');if(seq===state.sequence){if(e.name==='AbortError'){state.data=state.history=null;updated('Consulta cancelada. Você pode aplicar outro período.');}else{$('error').hidden=false;$('error').textContent=e.message;updated('A consulta não foi concluída. Ajuste os filtros ou tente atualizar.');}}}
  finally{if(seq===state.sequence)busy(false);}
}
function chip(value,label,kind,active,extra){return '<button type="button" class="filter-chip'+(active?' active':'')+'" data-'+kind+'="'+esc(value)+'">'+esc(label)+(extra||'')+'</button>';}
function renderFilters(){
  const companies=state.data.empresas;
  if(state.company!=='all'&&!companies.some(c=>String(c.id)===state.company))state.company='all';
  $('companyFilter').innerHTML='<span class="filter-bar-label">Empresa:</span>'+chip('all','Consolidado','company',state.company==='all')+companies.map(c=>chip(c.id,c.nome,'company',String(c.id)===state.company)).join('');
  $('metricTons').hidden=!tonsAvailable();$('projectionUnits').hidden=!tonsAvailable();
  if(!tonsAvailable()){if(state.metric==='toneladas')state.metric='count';state.projectionUnit='reais';}
  root.querySelectorAll('[data-metric]').forEach(b=>b.classList.toggle('active',b.dataset.metric===state.metric));
  const ts=teamList().filter(t=>!t.empresaId||state.company==='all'||t.empresaId===state.company), stage=state.mode==='projecao'?'pedidos':state.mode;
  if(state.team!=='all'&&!ts.some(t=>t.id===state.team)){state.team='all';state.seller='all';}
  const universe=M.summary(M.select(allRows(stage),Object.assign({},state,{team:'all',seller:'all',search:''}),state.data.vendedores,state.data.equipes)).rows;
  $('teamFilter').innerHTML='<span class="filter-bar-label">Equipe:</span>'+chip('all','Todas','team',state.team==='all')+ts.map(t=>chip(t.id,t.name,'team',state.team===t.id,' <span class="chip-count">'+universe.filter(r=>M.inTeam(t,r)).length+'</span>')).join('');
  const unassigned=Array.isArray(state.data.equipes)?universe.filter(r=>!ts.some(t=>M.inTeam(t,r))).length:0;
  $('teamNote').hidden=!unassigned||state.team!=='all';$('teamNote').textContent=unassigned+' documento(s) sem vínculo com as equipes comerciais permanecem em Todas.';
  const team=ts.find(t=>t.id===state.team);
  const vendors=state.data.vendedores.filter(v=>!team||team.members.indexOf(String(v.id))>=0);
  if(state.seller!=='all'&&!vendors.some(v=>String(v.id)===state.seller))state.seller='all';
  $('sellerFilter').innerHTML='<option value="all">Todos</option>'+vendors.map(v=>'<option value="'+esc(v.id)+'">'+esc(v.nome)+'</option>').join('');$('sellerFilter').value=state.seller;
  const reps=new Map();['orcamentos','pedidos','notas'].forEach(t=>allRows(t).forEach(r=>reps.set(String(r.representanteId),r.representante)));
  $('representativeFilter').innerHTML='<option value="all">Todos</option>'+Array.from(reps).sort((a,b)=>a[1].localeCompare(b[1],'pt-BR')).map(r=>'<option value="'+esc(r[0])+'">'+esc(r[1])+'</option>').join('');$('representativeFilter').value=state.representative;
  $('accessLabel').textContent=state.data.escopo==='equipe'?'Acesso limitado à equipe liberada':state.data.escopo==='vendedor'?'Acesso limitado ao vendedor liberado':'Acesso aos vendedores das empresas liberadas';
}
function render(){if(!state.data)return;renderFilters();$('scopeTitle').textContent=scopeName();const p=state.data.periodo;$('scopeSub').textContent=date(p.inicio)+' a '+date(p.fim)+' · '+(state.company==='all'?'Empresas consolidadas':(state.data.empresas.find(c=>String(c.id)===state.company)||{}).nome);
  root.querySelectorAll('[data-mode]').forEach(b=>{b.classList.toggle('active',b.dataset.mode===state.mode);b.setAttribute('aria-selected',String(b.dataset.mode===state.mode));});
  $('salesView').hidden=state.mode==='projecao';$('projectionView').hidden=state.mode!=='projecao';
  $('clientSearch').disabled=state.mode==='projecao';
  if(state.mode==='projecao'){if(state.history)renderProjection();return;}
  const s=M.summary(filtered(state.mode)), q=M.summary(filtered('orcamentos')), name=labels[state.mode];
  $('kpis').innerHTML=kpi(name+' no período',s.count)+kpi(state.mode==='orcamentos'?'Valor orçado':state.mode==='pedidos'?'Valor dos pedidos':'Faturamento',compact(s.value),money(s.value))+kpi('Ticket médio',compact(s.ticket))+kpi('Vendedores ativos',s.sellers)+(state.mode==='orcamentos'&&state.data.origemIA?kpi('Feito com IA',s.ia+' · '+pct(s.count?100*s.ia/s.count:null),'Origem Assistente IA no cadastro','kpi-ia'):'')+kpi('Taxa de conversão',pct(q.conversion),q.converted+' de '+q.count+' orçamentos viraram pedidos');
  if(tonsAvailable())$('kpis').innerHTML+=kpi('Volume de material',tons(filtered(state.mode).filter(r=>!+r.transferencia).reduce((sum,r)=>sum+(Number(r.toneladas)||0),0)),'Somente KG; sem transferências ou serviços de industrialização.');
  $('excludedNote').textContent=(s.cancelled?s.cancelled+' documento(s) cancelado(s) ('+money(s.cancelledValue)+') excluído(s). ':'')+(state.mode==='notas'?'Valor total das notas de saída com protocolo e não canceladas, incluindo IPI e frete.':state.mode==='pedidos'?'Pedidos sem cancelados e transferências entre empresas.':'Orçamentos sem cancelados; transferências permanecem nos totais em R$.')+(s.rows.some(r=>+r.valor<0)?' Existem documentos com valor negativo, mantidos nos totais.':'');
  $('metricCount').textContent=name;$('iaLabel').hidden=state.mode!=='orcamentos'||!state.data.origemIA;
  $('chartTitle').textContent='Evolução diária — '+scopeName().toLowerCase();$('chartSub').textContent=(state.metric==='toneladas'?'Volume de material em toneladas':state.metric==='count'?'Quantidade de '+name.toLowerCase():'Valor dos documentos')+' · fins de semana e feriados nacionais agrupados nos dias úteis exibidos';
  renderTrend();renderRanking(s.rows);renderClients(s.rows.filter(r=>!+r.transferencia));
  renderAnalysis(s.rows);
  $('statusPanel').hidden=state.mode!=='pedidos';
  if(state.mode==='pedidos')$('statuses').innerHTML=M.group(s.rows,r=>r.status||'Sem situação',r=>r.status||'Sem situação').sort((a,b)=>b.value-a.value).map(g=>'<div><span>'+esc(g.name)+' · '+g.count+'</span><b class="mono">'+esc(money(g.value))+'</b></div>').join('')||'<p class="empty">Nenhum pedido neste período.</p>';
  $('funnel').innerHTML=['orcamentos','pedidos','notas'].map(t=>{const a=M.summary(filtered(t));return kpi(labels[t],compact(a.value),a.count+' documentos');}).join('');
}
function renderAnalysis(rows){
  const host=$('analysis');
  if(!host)return;
  host.hidden=!state.data.segmentos&&!state.data.cancelamentos;
  $('segmentsPanel').hidden=!state.data.segmentos;
  $('cancellationsPanel').hidden=state.mode!=='orcamentos'||!state.data.cancelamentos;
  const A=window.DashboardAnaliseComercial;
  if(!A)return;
  if(state.data.segmentos)A.segments($('segments'),rows.map(r=>({segmento:r.segmento,quantidade:1,valor:r.valor})));
  if(state.mode==='orcamentos'&&state.data.cancelamentos)A.cancellations($('cancellations'),filtered('orcamentos').filter(r=>+r.cancelamentoReal).map(r=>({motivo:r.motivoCancelamento,quantidade:1,valor:r.valor})));
}
function svgChart(target,dates,series,moneyMode){
  if(!dates.length){$(target).innerHTML='<div class="empty">Sem dias disponíveis neste período.</div>';return;}
  const defaultUnit=moneyMode==='toneladas'?'toneladas':moneyMode?'value':'count';
  const units=[...new Set(series.map(s=>s.unit||defaultUnit))];
  const format=(v,unit,short)=>unit==='toneladas'?tons(v):unit==='percent'?pct(v):unit==='value'?(short?compact(v):money(v)):Number(v).toLocaleString('pt-BR',{maximumFractionDigits:0});
  const width=1100,height=280,left=72+(units.length-1)*78,right=16,top=25,bottom=36,w=width-left-right,h=height-top-bottom;
  const scales={};units.forEach(unit=>{
    const nums=series.filter(s=>(s.unit||defaultUnit)===unit).flatMap(s=>s.values.filter(v=>v!=null));
    const min=Math.min(0,...nums),max=unit==='percent'?100:Math.max(1,...nums);
    scales[unit]={min,max,range:max-min};
  });
  const x=i=>left+(dates.length===1?w/2:i*w/(dates.length-1));
  const y=(v,unit)=>top+(scales[unit].max-v)*h/scales[unit].range;
  let html='<svg viewBox="0 0 '+width+' '+height+'" preserveAspectRatio="none" role="img" aria-label="Evolução dos indicadores por dia útil"><title>Evolução no período selecionado</title>';
  units.forEach((unit,index)=>{
    const scale=scales[unit],axis=left-8-index*78;
    if(units.length>1)html+='<text text-anchor="end" x="'+axis+'" y="12">'+esc({count:'Quantidade',value:'R$',percent:'%',toneladas:'Toneladas'}[unit])+'</text>';
    const step=unit==='count'?Math.max(1,Math.ceil(scale.range/5)):scale.range/5;
    for(let v=unit==='count'?Math.ceil(scale.min/step)*step:scale.min;v<=scale.max+step/100;v+=step){
      if(index===0)html+='<line x1="'+left+'" y1="'+y(v,unit)+'" x2="'+(width-right)+'" y2="'+y(v,unit)+'" stroke="var(--line)"/>';
      html+='<text text-anchor="end" x="'+axis+'" y="'+(y(v,unit)+4)+'">'+esc(format(v,unit,true))+'</text>';
    }
  });
  const step=Math.max(1,Math.ceil(dates.length/10));dates.forEach((d,i)=>{if(i%step===0||i===dates.length-1)html+='<text text-anchor="middle" x="'+x(i)+'" y="'+(height-12)+'">'+esc(series.some(s=>s.comparison)?(i+1)+'º DU':d.slice(5))+'</text>';});
  series.forEach((s,k)=>{
    const unit=s.unit||defaultUnit,points=s.values.map((v,i)=>v==null?null:[x(i),y(v,unit),i,v]);
    const valid=points.filter(Boolean);if(!valid.length)return;
    let connected=false;const path=points.map(p=>{if(!p){connected=false;return '';}const op=connected?'L':'M';connected=true;return op+p[0]+','+p[1];}).join(' ');
    if(k===0)html+='<path d="'+path+' L'+valid[valid.length-1][0]+','+y(0,unit)+' L'+valid[0][0]+','+y(0,unit)+' Z" fill="'+s.color+'" opacity=".14"/>';
    html+='<path d="'+path+'" fill="none" stroke="'+s.color+'" stroke-width="'+(s.goal?'3':'2')+'"'+(s.goal?' data-goal-line="true"':'')+(s.criterion?' data-trend-line="'+s.criterion+'" data-period="'+(s.comparison?'previous':'current')+'" data-comparison-key="'+esc(s.periodKey||'current')+'"':'')+(s.dashed?' stroke-dasharray="'+(s.dash||'7 5')+'"':'')+'/>';
    valid.forEach(p=>html+='<circle cx="'+p[0]+'" cy="'+p[1]+'" r="3" fill="'+s.color+'"><title>'+esc(date((s.dates||dates)[p[2]])+' · '+s.name+': '+format(p[3],unit,false))+'</title></circle>');
  });
  $(target).innerHTML=html+'</svg>';
}
const periodKey=p=>p.inicio+'|'+p.fim,periodLabel=p=>date(p.inicio)+' a '+date(p.fim);
function selectedComparisons(){
  if(!state.data)return [];
  const list=$('comparePrevious').checked?[{period:M.previousPeriod(state.data.periodo),name:'Anterior'}]:[];
  customComparisons.filter(c=>c.enabled).forEach(c=>list.push({period:c.period,name:'Comparativo'}));
  const seen=new Set([periodKey(state.data.periodo)]);
  return list.filter(c=>{c.key=periodKey(c.period);if(seen.has(c.key))return false;seen.add(c.key);return true;});
}
function resetComparisons(){
  if(comparisonRequest)comparisonRequest.controller.abort();
  comparisonRequest=null;comparisonData.clear();
  trendCache=new WeakMap();trendSequence++;cancelAnimationFrame(trendFrame);clearTimeout(trendTimer);
  if($('trendLoading'))$('trendLoading').hidden=true;
  if($('trend'))$('trend').setAttribute('aria-busy','false');
}
function activeTrendCriteria(){return [state.metric,...[...selectedCriteria].filter(c=>c!==state.metric&&(!(c==='ia')||(state.mode==='orcamentos'&&state.data.origemIA))&&(c!=='toneladas'||tonsAvailable()))];}
function comparisonTypes(active){return [...new Set([state.mode,...(active.some(c=>['ia','closed','conversion'].includes(c))?['orcamentos']:[])])];}
function comparisonComplete(entry,types){return !!entry&&!!entry.data&&types.every(type=>Array.isArray(entry.data.fontes[type]));}
function ensureComparisons(selected,types){
  if(comparisonRequest&&!selected.some(c=>c.key===comparisonRequest.key)){
    comparisonRequest.controller.abort();const cached=comparisonData.get(comparisonRequest.key);
    if(cached&&cached.data)comparisonData.set(comparisonRequest.key,{data:cached.data});else comparisonData.delete(comparisonRequest.key);
    comparisonRequest=null;
  }
  if(comparisonRequest)return;
  const next=selected.find(c=>{const entry=comparisonData.get(c.key);return !comparisonComplete(entry,types)&&!(entry&&entry.error);});if(!next)return;
  const cached=comparisonData.get(next.key),missing=types.filter(type=>!comparisonComplete(cached,[type]));
  const request={key:next.key,controller:new AbortController()};comparisonRequest=request;
  comparisonData.set(next.key,{data:cached&&cached.data,loading:true});
  api(false,next.period,request.controller.signal,missing).then(result=>{
    if(destroyed||request!==comparisonRequest)return;
    if(!missing.every(type=>Array.isArray(result.painelCotacoes.fontes[type])))throw new Error('Não foi possível concluir os dados do período. Tente novamente.');
    const data=result.painelCotacoes,previous=cached&&cached.data;
    if(previous){data.fontes=Object.assign({},previous.fontes,data.fontes);data.fontesToneladas=Object.assign({},previous.fontesToneladas,data.fontesToneladas);}
    comparisonData.set(next.key,{data});
  }).catch(error=>{
    if(destroyed||request!==comparisonRequest||error.name==='AbortError')return;
    if(window.DashboardSqlDiagnostics)window.DashboardSqlDiagnostics.clientError(error,{id:id,aberturaId:diagnosticOpening});
    comparisonData.set(next.key,{data:cached&&cached.data,error:periodLabel(next.period)+': '+error.message});
  }).finally(()=>{
    if(destroyed||request!==comparisonRequest)return;
    comparisonRequest=null;if(state.data&&state.mode!=='projecao')renderTrend();
  });
}
function comparisonColor(base,index){
  if(index<2)return base;
  const rgb=base.slice(1).match(/../g).map(v=>parseInt(v,16)/255),max=Math.max(...rgb),min=Math.min(...rgb),delta=max-min;
  const hue=delta===0?0:max===rgb[0]?((rgb[1]-rgb[2])/delta)%6:max===rgb[1]?(rgb[2]-rgb[0])/delta+2:(rgb[0]-rgb[1])/delta+4;
  return 'hsl('+((hue*60+(index-1)*47+360)%360)+',68%,43%)';
}
function renderComparisonControls(types){
  types=types||comparisonTypes(activeTrendCriteria());
  $('comparisonList').innerHTML=customComparisons.map(c=>{const entry=comparisonData.get(c.key),pending=c.enabled&&!comparisonComplete(entry,types)&&!(entry&&entry.error);return '<div class="cotacoes-comparison-item" aria-busy="'+pending+'"><label class="ia-toggle"><input type="checkbox" data-comparison-toggle="'+c.key+'"'+(c.enabled?' checked':'')+'>'+esc(periodLabel(c.period))+'</label>'+(pending?'<span class="cotacoes-comparison-progress">'+(entry&&entry.loading?'Carregando…':'Aguardando…')+'</span>':'')+'<button type="button" data-comparison-remove="'+c.key+'" aria-label="Remover período '+esc(periodLabel(c.period))+'">×</button></div>';}).join('');
  if(!$('compareFrom').value&&!$('compareTo').value){const p=M.previousPeriod(state.data.periodo);$('compareFrom').value=p.inicio;$('compareTo').value=p.fim;}
}
function addComparison(){
  const period={inicio:$('compareFrom').value,fim:$('compareTo').value};let error='';
  if(!period.inicio||!period.fim||!$('compareFrom').checkValidity()||!$('compareTo').checkValidity())error='Informe as duas datas, sem datas futuras.';
  else if(period.inicio>period.fim)error='A data inicial deve ser anterior ou igual à final.';
  else if((Date.parse(period.fim)-Date.parse(period.inicio))/86400000>365)error='Selecione um período de até 366 dias.';
  else if(periodKey(period)===periodKey(state.data.periodo))error='Esse já é o período atual do gráfico.';
  else if(customComparisons.some(c=>c.key===periodKey(period))||selectedComparisons().some(c=>c.key===periodKey(period)))error='Esse período já foi selecionado.';
  $('comparisonError').hidden=!error;$('comparisonError').textContent=error;if(error)return;
  customComparisons.push({key:periodKey(period),period,enabled:true});renderComparisonControls();renderTrend();
}
function renderTrend(){
  const selected=selectedComparisons(),active=activeTrendCriteria(),types=comparisonTypes(active);
  ensureComparisons(selected,types);renderComparisonControls(types);
  const period=state.data.periodo;
  $('comparisonPeriod').textContent='Atual: '+periodLabel(period)+selected.map(c=>' · '+c.name+': '+periodLabel(c.period)).join('')+' · '+scopeName();
  const waiting=selected.filter(c=>!comparisonComplete(comparisonData.get(c.key),types));
  const errors=waiting.map(c=>comparisonData.get(c.key)).filter(c=>c&&c.error).map(c=>c.error),pending=waiting.filter(c=>!(comparisonData.get(c.key)||{}).error).length;
  $('comparisonStatus').textContent=[pending?'Carregando '+pending+' período(s) comparativo(s)…':'',...errors].filter(Boolean).join(' ');$('comparisonRetry').hidden=!errors.length;
  const sequence=++trendSequence;cancelAnimationFrame(trendFrame);clearTimeout(trendTimer);
  $('trendLoading').hidden=false;$('trend').setAttribute('aria-busy','true');
  $('trendLoadingText').textContent='Atualizando gráfico…';
  $('countOption').textContent=labels[state.mode]+' no período';
  $('valueOption').textContent=state.mode==='orcamentos'?'Valor orçado':state.mode==='pedidos'?'Valor dos pedidos':'Faturamento';
  $('tonsOption').hidden=!tonsAvailable();
  root.querySelectorAll('[data-trend-criterion]').forEach(input=>{input.checked=active.includes(input.dataset.trendCriterion);input.disabled=input.dataset.trendCriterion===state.metric;});
  // Permite que o navegador desenhe o aviso antes de calcular as novas séries.
  trendFrame=requestAnimationFrame(()=>{trendTimer=setTimeout(()=>{
  if(destroyed||sequence!==trendSequence||state.mode==='projecao')return;
  const series=[];
  active.forEach(criterion=>{
    const config=trendCriteria[criterion],name=criterion==='count'?labels[state.mode]:criterion==='value'?$('valueOption').textContent:config.name;
    [{data:state.data},...selected.map(c=>({data:comparisonComplete(comparisonData.get(c.key),types)?comparisonData.get(c.key).data:null,...c}))].forEach((entry,index)=>{
      const source=entry.data;
      if(!source)return;
      let cache=trendCache.get(source);if(!cache){cache=new Map();trendCache.set(source,cache);}
      const key=JSON.stringify([state.company,state.team,state.seller,state.representative,state.search,state.mode,criterion]);
      if(!cache.has(key)){if(cache.size>=64)cache.delete(cache.keys().next().value);cache.set(key,M.dailyIndicator(source,state,state.mode,criterion));}
      const d=cache.get(key),p=source.periodo;
      series.push({name:name+' ('+{count:'quantidade',value:'R$',percent:'%',toneladas:'t'}[config.unit]+') · '+(index?entry.name:'Atual')+' · '+periodLabel(p),color:comparisonColor(config.color,index),unit:config.unit,values:d.values,dates:d.dates,comparison:!!index,dashed:!!index,dash:['','7 5','3 4','12 4 3 4','2 3 2 6'][(index-1)%4+1],periodKey:entry.key,criterion});
    });
  });
  const dates=series.reduce((longest,s)=>s.dates.length>longest.length?s.dates:longest,[]);
  svgChart('trend',dates,series,false);
  $('trendLegend').innerHTML=series.map(s=>'<div><i style="border-color:'+s.color+';border-top-style:'+(s.dashed?'dashed':'solid')+'"></i><span>'+esc(s.name)+'</span></div>').join('');
  $('trendLoading').hidden=!pending;$('trend').setAttribute('aria-busy',String(!!pending));
  },0);});
}
function renderRanking(rows){
  const by=$('groupBy').value, isRep=by==='representante', groups=M.group(rows,r=>String(r[by+'Id']),r=>r[by]);
  groups.sort((a,b)=>state.dir*(state.sort==='name'?a.name.localeCompare(b.name,'pt-BR'):a[state.sort]-b[state.sort]));
  $('rankName').textContent=isRep?'Representante':'Vendedor';$('rankCount').textContent=labels[state.mode];$('rankIaHead').textContent=state.mode==='orcamentos'?'Via IA':'Participação';$('rankSub').textContent=groups.length+' '+(isRep?'representante(s)':'vendedor(es)')+' no escopo atual';$('clearSeller').hidden=state.seller==='all'&&state.representative==='all';
  const ts=teamList(), total=groups.reduce((n,g)=>n+g.value,0);
  $('rankBody').innerHTML=groups.map(g=>{const teams=ts.filter(t=>g.rows.some(r=>M.inTeam(t,r)));return '<tr><td><button class="link-button" data-'+(isRep?'representative':'seller')+'="'+esc(g.id)+'">'+esc(g.name)+'</button></td><td class="muted">'+(teams.map(t=>esc(t.name)).join(', ')||'Não classificado')+'</td><td class="num mono">'+g.count+'</td><td class="num mono">'+esc(money(g.value))+'</td><td class="num mono">'+(state.mode==='orcamentos'?g.ia+' <span class="ia-pct">('+pct(100*g.ia/g.count)+')</span>':pct(total?100*g.value/total:null))+'</td></tr>';}).join('')||'<tr><td colspan="5" class="empty">Nenhum documento para os filtros selecionados.</td></tr>';
}
function documentRows(g){const docs=g.rows.slice().sort((a,b)=>b.data.localeCompare(a.data)||Number(b.id)-Number(a.id));return '<div class="expanded-docs"><p class="panel-sub">Clique na linha para abrir o documento.</p><table class="comparison-table"><thead><tr><th>Documento</th><th>Emissão</th><th>Empresa / vendedor</th><th>Situação</th><th class="num">Valor</th></tr></thead><tbody>'+docs.map(r=>{const co=state.data.empresas.find(c=>String(c.id)===String(r.empresaId)),title='Abrir '+documentRoutes[state.mode][2].toLowerCase()+' '+(r.numero||r.id);return '<tr class="cotacoes-document-row" data-document-id="'+esc(r.id)+'" data-document-company="'+esc(r.empresaId)+'" title="'+esc(title)+'"><td><button type="button" class="link-button" aria-label="'+esc(title)+'">'+esc(r.numero||r.id)+' ↗</button></td><td>'+esc(date(r.data))+'</td><td>'+esc(co?co.nome:r.empresaId)+'<br><span class="muted">'+esc(r.vendedor)+'</span></td><td>'+esc(r.status)+(state.mode==='orcamentos'&&+r.convertido?' · Gerou pedido':'')+'</td><td class="num mono">'+esc(money(r.valor))+'</td></tr>';}).join('')+'</tbody></table></div>';}
function renderClients(rows){const groups=M.group(rows,r=>M.normalized(M.groupName(r)),M.groupName).sort((a,b)=>b.value-a.value);const pages=Math.max(1,Math.ceil(groups.length/25));state.page=Math.min(state.page,pages-1);
  $('clientsTitle').textContent=state.mode==='orcamentos'?'Clientes cotando':state.mode==='pedidos'?'Clientes comprando':'Clientes faturados';$('clientsSub').textContent=groups.length+' cliente(s) / grupo(s) no período';
  $('clientsBody').innerHTML=groups.slice(state.page*25,state.page*25+25).map(g=>'<tr><td>'+esc(g.name)+'</td><td class="num mono">'+esc(money(g.value))+'</td><td class="num mono">'+g.count+'</td><td><button class="preset-btn" data-client="'+esc(g.id)+'" aria-expanded="'+state.expanded.has(g.id)+'" aria-label="Documentos de '+esc(g.name)+'">'+(state.expanded.has(g.id)?'−':'+')+'</button></td></tr>'+(state.expanded.has(g.id)?'<tr><td colspan="4">'+documentRows(g)+'</td></tr>':'')).join('')||'<tr><td colspan="4" class="empty">Nenhum cliente no período.</td></tr>';
  $('clientsPages').innerHTML='<button class="preset-btn" data-page="-1"'+(!state.page?' disabled':'')+'>Anterior</button><span class="muted">'+(state.page+1)+' / '+pages+'</span><button class="preset-btn" data-page="1"'+(state.page>=pages-1?' disabled':'')+'>Próxima</button>';
}
function scenarios(result,fmt){fmt=fmt||compact;return kpi('Conservador',fmt(result.low))+kpi('Provável',fmt(result.probable))+kpi('Otimista',fmt(result.high));}
function renderProjection(){
  const filters=Object.assign({},state,{search:''}), next=state.projection==='next',inTons=tonsAvailable()&&state.projectionUnit==='toneladas',short=inTons?tons:compact,full=inTons?tons:money;
  root.querySelectorAll('[data-projection-unit]').forEach(b=>b.classList.toggle('active',b.dataset.projectionUnit===state.projectionUnit));
  const rows=type=>M.select(state.history.fontes[type]||[],filters,state.history.vendedores,state.history.equipes);
  const cutoff=state.history.periodo.fim, orders=inTons?M.select((state.history.fontesToneladas||{}).pedidos||[],filters,state.history.vendedores,state.history.equipes):rows('pedidos'), cal=state.history.calendario;
  const p=M.projection(orders,cutoff,cal,next);
  $('projectionNote').textContent='Referência: '+p.month.split('-').reverse().join('/')+' · Base de cálculo: pedidos do escopo selecionado, até '+date(cutoff)+'. A projeção considera o mês inteiro, independentemente da data inicial do filtro. O filtro de cliente não se aplica à projeção. Histórico de pedidos inclui todas as situações, descontando transferências.';
  $('projectionKpis').innerHTML=kpi('Pedidos realizados'+(inTons?' (t)':''),short(p.real))+kpi('Projeção provável de pedidos',short(p.probable))+'<article id="cotacoes-goalCard" class="commercial-kpi"></article>'+kpi('Dias úteis',p.elapsed+' / '+p.days)+kpi('Ritmo por dia útil',short(p.pace));
  $('scenarios').innerHTML=scenarios(p,short);$('projectionChartSub').textContent=p.historyMonths+' meses com movimento no histórico de pedidos · '+(inTons?'toneladas de material':'valores em R$');
  let accumulated=0;const real=p.values.map((v,i)=>{accumulated+=v;return i<p.elapsed?accumulated:null;});
  const forecast=p.dates.map((d,i)=>{if(p.probable==null)return null;if(i<p.elapsed-1)return null;if(i===p.elapsed-1)return p.real;return p.real+(p.probable-p.real)*(i-p.elapsed+1)/Math.max(1,p.days-p.elapsed);});
  goals.render({p,inTons,real,forecast});
  const sums=new Map();orders.forEach(r=>{const id=String(r.vendedorId);if(!sums.has(id))sums.set(id,{id,real:0,base:0});const v=sums.get(id);if(r.data.slice(0,7)===p.month)v.real+=+r.valor;if(r.data< p.month+'-01'&&r.data>=M.fmtDate(new Date(Number(p.month.slice(0,4)),Number(p.month.slice(5))-4,1)))v.base+=+r.valor;});
  const sellers=Array.from(sums.values()).filter(s=>s.real||s.base), weight=sellers.reduce((a,s)=>a+s.real+s.base/3,0), remaining=p.probable==null?null:Math.max(0,p.probable-p.real);
  $('projectionSellers').innerHTML=sellers.sort((a,b)=>b.real-a.real).map(s=>{const v=state.history.vendedores.find(v=>String(v.id)===s.id);const prediction=remaining==null?null:s.real+(weight?remaining*(s.real+s.base/3)/weight:0);return '<tr><td>'+esc(v?v.nome:'Sem vendedor')+'</td><td class="num mono">'+esc(full(s.real))+'</td><td class="num mono">'+esc(full(prediction))+'</td><td class="num">Não definida</td><td class="num mono">'+esc(full(p.elapsed?s.real/p.elapsed:null))+'</td></tr>';}).join('')||'<tr><td colspan="5" class="empty">Sem histórico suficiente neste escopo.</td></tr>';
  const names={M0:'Base histórica',M1:'Ritmo linear',M2:'Curva histórica',M3:'Média móvel de 10 dias úteis'};
  $('modelDetails').innerHTML=Object.keys(p.models).map(k=>'<div><span>'+names[k]+(p.weights&&p.weights[k]!=null?' · peso '+pct(100*p.weights[k]):'')+'</span><b class="mono">'+esc(full(p.models[k]))+'</b></div>').join('')+'<p class="note">A previsão restante é distribuída entre os vendedores conforme participação no realizado e média dos três meses anteriores. Calendário comercial aplicado à projeção.</p>';
  root.querySelectorAll('[data-projection]').forEach(b=>b.classList.toggle('active',b.dataset.projection===state.projection));
}
function exportCsv(){if(!state.data)return;let rows;if(state.mode==='projecao'){rows=[['Projeção de pedidos',state.projection==='next'?'Próximo mês':'Mês de referência'],['Escopo',scopeName()],['Data de referência',state.data.periodo.fim],['Unidade',state.projectionUnit==='toneladas'?'Toneladas':'R$'],['Meta do mês',goals.getLabel()],[],['Vendedor','Pedidos realizados','Projeção provável','Meta','Ritmo por dia útil']];$('projectionSellers').querySelectorAll('tr').forEach(tr=>rows.push(Array.from(tr.querySelectorAll('td')).map(td=>td.textContent)));}else{rows=[['Etapa','Empresa','Documento','Emissão','Cliente','Grupo / proposta','Vendedor','Representante','Situação','Valor','Via IA','Gerou pedido','Toneladas']];(state.metric==='toneladas'?filtered(state.mode).filter(r=>!+r.transferencia):M.summary(filtered(state.mode)).rows).forEach(r=>rows.push([labels[state.mode],r.empresaId,r.numero||r.id,r.data,r.cliente,M.groupName(r),r.vendedor,r.representante,r.status,Number(r.valor).toFixed(2).replace('.',','),+r.ia?'Sim':'Não',+r.convertido?'Sim':'Não',r.toneladas==null?'':Number(r.toneladas).toFixed(3).replace('.',',')]));}const blob=new Blob(['\ufeff'+rows.map(r=>r.map(M.csvCell).join(';')).join('\r\n')],{type:'text/csv;charset=utf-8'}),url=URL.createObjectURL(blob),a=document.createElement('a');a.href=url;a.download='painel-cotacoes-'+state.mode+'-'+state.data.periodo.fim+'.csv';a.click();setTimeout(()=>URL.revokeObjectURL(url),1000);}
root.addEventListener('click',e=>{const row=e.target.closest('[data-document-id]');if(row){if(e.detail===0||!window.getSelection().toString())openDocument(row);return;}const b=e.target.closest('button');if(!b)return;const d=b.dataset;
  if(d.mode){state.mode=d.mode;state.page=0;state.expanded.clear();load(false);}
  else if(d.preset){applyPreset(d.preset);load(true);}
  else if(d.month){state.referenceMonth=d.month;applyPreset('month');load(true);}
  else if(d.comparisonRemove){const index=customComparisons.findIndex(c=>c.key===d.comparisonRemove);if(index>=0)customComparisons.splice(index,1);renderComparisonControls();renderTrend();}
  else if(d.team||d.company||d.seller||d.representative){if(d.team){state.team=d.team;state.seller='all';}if(d.company){state.company=d.company;state.metric=tonsAvailable()?'toneladas':'count';state.projectionUnit=tonsAvailable()?'toneladas':'reais';}if(d.seller)state.seller=d.seller;if(d.representative)state.representative=d.representative;state.page=0;state.expanded.clear();render();}
  else if(d.metric){state.metric=d.metric;root.querySelectorAll('[data-metric]').forEach(x=>x.classList.toggle('active',x===b));render();}
  else if(d.sort){state.dir=state.sort===d.sort?-state.dir:-1;state.sort=d.sort;render();}
  else if(d.client){if(state.expanded.has(d.client))state.expanded.delete(d.client);else state.expanded.add(d.client);render();}
  else if(d.page){state.page+=Number(d.page);render();}
  else if(d.projectionUnit){state.projectionUnit=d.projectionUnit;renderProjection();}
  else if(d.projection){state.projection=d.projection;renderProjection();}
  else if(d.goal==='edit'){goals.open();}
});
$('trendOptions').addEventListener('change',e=>{const criterion=e.target.dataset.trendCriterion;if(!criterion)return;if(e.target.checked)selectedCriteria.add(criterion);else selectedCriteria.delete(criterion);renderTrend();});
$('comparePrevious').addEventListener('change',renderTrend);
$('comparisonList').addEventListener('change',e=>{const c=customComparisons.find(c=>c.key===e.target.dataset.comparisonToggle);if(c){c.enabled=e.target.checked;renderTrend();}});
$('comparisonAdd').addEventListener('click',addComparison);
$('comparisonRetry').addEventListener('click',()=>{comparisonData.forEach((value,key)=>{if(value.error)comparisonData.set(key,{data:value.data});});renderTrend();});
$('dates').addEventListener('submit',e=>{e.preventDefault();load(true);});
$('year').addEventListener('change',()=>{if(!$('year').checkValidity())return;const today=M.fmtDate(new Date()),month=$('year').value+'-'+state.referenceMonth.slice(5);state.referenceMonth=month>today.slice(0,7)?today.slice(0,7):month;applyPreset('month');load(true);});
$('sellerFilter').addEventListener('change',()=>{state.seller=$('sellerFilter').value;state.page=0;render();});
$('representativeFilter').addEventListener('change',()=>{state.representative=$('representativeFilter').value;state.page=0;render();});
$('groupBy').addEventListener('change',render);
$('clientSearch').addEventListener('input',()=>{state.search=$('clientSearch').value;state.page=0;render();});
$('clearSeller').addEventListener('click',()=>{state.seller=state.representative='all';render();});
$('clearFilters').addEventListener('click',()=>{state.team=state.company=state.seller=state.representative='all';state.search='';$('clientSearch').value='';state.page=0;state.expanded.clear();state.referenceMonth=M.fmtDate(new Date()).slice(0,7);applyPreset('month');load(true);});
$('documentClose').addEventListener('click',()=>{$('documentDialog').close();});
$('documentDialog').addEventListener('close',clearDocument);
function printPanel(){if(state.busy||!state.data||!$('error').hidden)return;document.body.classList.add('cotacoes-print');try{window.print();}finally{document.body.classList.remove('cotacoes-print');}}
$('exportCsv').addEventListener('click',exportCsv);$('print').addEventListener('click',printPanel);
$('year').max=new Date().getFullYear();$('compareFrom').max=$('compareTo').max=M.fmtDate(new Date());applyPreset('month');load(true);
return {destroy:function(){if(window.DashboardSqlDiagnostics&&diagnosticOpening)window.DashboardSqlDiagnostics.complete(diagnosticOpening,null,0,true);destroyed=true;state.sequence++;if(loadRequest)loadRequest.abort();controller.abort();resetComparisons();goals.destroy();clearDocument();if($('documentDialog').open)$('documentDialog').close();},refresh:function(){return load(true);},exportCsv:function(){if(!state.busy&&$('error').hidden)exportCsv();},print:printPanel};
}
function mount(container,id,options){
  let instance=null,disposed=false;
  const request=new AbortController();
  container.innerHTML='<p class="cotacoes-mount-status" role="status">Carregando o indicador…</p>';
  fetch('painel-cotacoes.html?v=15',{credentials:'same-origin',cache:'no-cache',signal:request.signal}).then(r=>{if(!r.ok)throw new Error('Não foi possível carregar a interface do indicador.');return r.text();}).then(html=>{
    if(disposed)return;
    const parsed=new DOMParser().parseFromString(html,'text/html'),template=parsed.querySelector('[data-cotacoes-root]');
    if(!template)throw new Error('Atualize os arquivos da interface do Painel de Cotações.');
    const root=document.importNode(template,true);root.dataset.embedded='true';container.textContent='';container.appendChild(root);instance=create(root,id,options);
  }).catch(error=>{if(!disposed&&options&&options.onError)options.onError(error);});
  return {destroy:function(){disposed=true;request.abort();if(instance)instance.destroy();},refresh:function(){if(instance)return instance.refresh();},exportCsv:function(){if(instance)instance.exportCsv();},print:function(){if(instance)instance.print();}};
}
window.DashboardPainelCotacoes={mount:mount};
const standalone=document.querySelector('body.cotacoes-standalone [data-cotacoes-root]');
if(standalone)create(standalone,new URLSearchParams(location.search).get('id'));
})();
