/* Agrupamentos reais e núcleo estatístico extraído do HTML de referência do cliente. */
(function (root) {
'use strict';
const fmtDate = d => d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
const parseDateStr = s => new Date(s+'T12:00:00');
const addDays = (d,n) => { const x=new Date(d);x.setDate(x.getDate()+n);return x; };
const localISO=fmtDate, localToday=()=>fmtDate(new Date()), ymOf=s=>s.slice(0,7);
const addDaysStr=(s,n)=>fmtDate(addDays(parseDateStr(s),n));
const prevYM=(ym,k)=>{const p=ym.split('-').map(Number);return fmtDate(new Date(p[0],p[1]-1-(k||1),1)).slice(0,7);};
const firstOfYM=ym=>ym+'-01', lastOfYM=ym=>{const p=ym.split('-').map(Number);return fmtDate(new Date(p[0],p[1],0));};
const PROJ_HOLIDAYS=new Set();
let proj;
const fromEntries=entries=>entries.reduce((o,p)=>{o[p[0]]=p[1];return o;},{});
const PROJ_MODELS = ['M0','M1','M2','M3'];
const PROJ_MODEL_LABEL = {M0:'Base histórica', M1:'Ritmo linear', M2:'Curva histórica', M3:'Média móvel 10 DU'};
const PROJ_BUCKETS = [
  {k:'b1', lo:0,   hi:.25,  label:'Até 25% do mês'},
  {k:'b2', lo:.25, hi:.5,   label:'25% a 50%'},
  {k:'b3', lo:.5,  hi:.75,  label:'50% a 75%'},
  {k:'b4', lo:.75, hi:1.01, label:'Acima de 75%'},
];
const PROJ_MIN_DEV = 0.10;
const MONTH_NAMES = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];
function projEaster(y){ const a=y%19,b=Math.floor(y/100),c=y%100,d=Math.floor(b/4),e=b%4,f=Math.floor((b+8)/25),g=Math.floor((b-f+1)/3),h=(19*a+b-d-g+15)%30,i=Math.floor(c/4),k=c%4,l=(32+2*e+2*i-h-k)%7,m=Math.floor((a+11*h+22*l)/451),mo=Math.floor((h+l-7*m+114)/31),dy=((h+l-7*m+114)%31)+1; return new Date(y,mo-1,dy); }
const PROJ_HOL_CACHE = {};
function projHolidays(y){
  if(PROJ_HOL_CACHE[y]) return PROJ_HOL_CACHE[y];
  const e = projEaster(y), off = n => { const d = new Date(e); d.setDate(d.getDate()+n); return fmtDate(d); };
  const md = ['01-01','03-09','04-21','05-01','09-07','10-12','11-02','11-15','12-25'].concat(y>=2024 ? ['11-20'] : []);
  return (PROJ_HOL_CACHE[y] = new Set(md.map(x=>y+'-'+x).concat([off(-48), off(-47), off(-2), off(60)])));
}
const PROJ_RECESSO_PADRAO = {de:'12-20', ate:'01-09'};
function projRecesso(){ const r = proj.recesso; return r === null ? null : (r || PROJ_RECESSO_PADRAO); }
function projInRecess(s){ const r = projRecesso(); if(!r || !r.de || !r.ate) return false; const md = s.slice(5); return r.de <= r.ate ? (md>=r.de && md<=r.ate) : (md>=r.de || md<=r.ate); }
function projIsBiz(s){ const wd=parseDateStr(s).getDay(); if(wd===0||wd===6) return false; return !PROJ_HOLIDAYS.has(s) && !projHolidays(+s.slice(0,4)).has(s) && !proj.extraHol.includes(s) && !projInRecess(s); }
function projBizDays(ym){ const out=[]; let d=firstOfYM(ym); const end=lastOfYM(ym); while(d<=end){ if(projIsBiz(d)) out.push(d); d=addDaysStr(d,1); } return out; }
const pSum = a => a.reduce((x,y)=>x+y,0);
const pMean = a => a.length ? pSum(a)/a.length : null;
function pQuantile(a,q){ if(!a.length) return null; const s=[...a].sort((x,y)=>x-y); const p=(s.length-1)*q; const i=Math.floor(p); const f=p-i; return s[i] + (s[Math.min(i+1,s.length-1)]-s[i])*f; }
function projBucket(f){ return PROJ_BUCKETS.find(b=>f>=b.lo && f<b.hi) || PROJ_BUCKETS[PROJ_BUCKETS.length-1]; }
function projSeries(ym){
  const doc = proj.hist[ym]; const days = (doc && doc.days) || {};
  const B = projBizDays(ym); const n = B.length;
  const du = new Array(n).fill(0), duc = new Array(n).fill(0), big = new Array(n).fill(0);
  const idx = d => { let k = B.findIndex(b=>b>=d); return k<0 ? n-1 : k; };
  if(!n) return {ym,B,n,du,duc,big,total:pSum(Object.values(days).map(x=>x.g-x.e)),count:0};
  Object.entries(days).forEach(([d,x])=>{ const k = idx(d); du[k] += Math.max(0, (x.g||0)-(x.e||0)); duc[k] += Math.max(0, (x.gc||0)-(x.ec||0)); });
  if(proj.baseActive && proj.large && proj.large.ok){
    Object.entries(proj.large.byDate).forEach(([d,v])=>{ if(d.slice(0,7)!==ym) return; const k = idx(d); const t = Math.min(du[k], v); du[k] -= t; big[k] += t; });
  }
  return {ym, B, n, du, duc, big, total:pSum(du), count:pSum(duc)};
}
function projCurveAt(s, f){
  if(!s.total) return null;
  const cum=[0]; let acc=0; s.du.forEach(v=>{ acc+=v; cum.push(acc); });
  const p=f*s.n; const i=Math.floor(p); const fr=p-i;
  const a=cum[Math.min(i,s.n)], b=cum[Math.min(i+1,s.n)];
  return (a+(b-a)*fr)/s.total;
}
function projMonthNet(ym){
  const h = proj.hist[ym];
  if(h && h.complete){ const s=projSeries(ym); return {v:s.total, c:s.count}; }
  const m = proj.monthly[ym];
  if(!m) return null;
  let v = Math.max(0,m.g-m.e);
  if(proj.baseActive && proj.large && proj.large.ok && proj.large.byMonth[ym]!=null) v = Math.max(0, v - proj.large.byMonth[ym]);
  return {v, c:Math.max(0,m.gc-m.ec)};
}
function projBase(ym){
  let v=0, c=0, du=0;
  for(let k=1;k<=3;k++){
    const m = prevYM(ym,k); const x = projMonthNet(m);
    if(!x) return null;
    v+=x.v; c+=x.c; du+=projBizDays(m).length;
  }
  return du && v>0 ? {rate:v/du, countRate:c/du, ticket: c? v/c : null} : null;
}
function projSeasonFactor(ym, netFn){
  const ratios = [];
  for(let y=1; y<=4; y++){
    const m = prevYM(ym, 12*y); const a = netFn(m); if(!a || !a.v) continue;
    let v = 0, du = 0, ok = true;
    for(let k=1; k<=3; k++){ const mm = prevYM(m,k); const x = netFn(mm); if(!x){ ok = false; break; } v += x.v; du += projBizDays(mm).length; }
    if(!ok || v<=0 || !du) continue;
    const r = (a.v/projBizDays(m).length) / (v/du);
    if(isFinite(r) && r>0) ratios.push({y:m.slice(0,4), r});
  }
  if(!ratios.length) return null;
  const srt = ratios.map(x=>x.r).sort((a,b)=>a-b), k = srt.length;
  const med = k%2 ? srt[(k-1)/2] : (srt[k/2-1]+srt[k/2])/2;
  return {factor:Math.min(2, Math.max(0.3, med)), years:k, ratios};
}
function projSeasonal(ym){ const f = projSeasonFactor(ym, projMonthNet); return f ? f.factor : null; }
function projRecent(ym, d, cache){
  const s = cache[ym] || projSeries(ym);
  const vals = s.du.slice(0,d);
  if(vals.length >= 10) return vals.slice(-10);
  const pm = prevYM(ym,1);
  if(proj.hist[pm]){
    const ps = cache[pm] || projSeries(pm);
    return ps.du.slice(-(10-vals.length)).concat(vals);
  }
  return vals;
}
function projModels(c){
  const rem = c.n - c.d; const cap = c.cap || Infinity; const out = {};
  const Rcap = pSum(c.duVals.map(v=>Math.min(v,cap)));
  if(c.base) out.M0 = c.R + c.base.rate*(c.seas||1)*rem;
  if(c.d>0) out.M1 = c.R + (Rcap/c.d)*rem;
  if(c.d>0 && c.curveFn){ const k=c.curveFn(c.d/c.n); if(k && k>0.02) out.M2 = c.R + Rcap*(1/k - 1); }
  if(c.recent && c.recent.length>=5) out.M3 = c.R + pMean(c.recent.map(v=>Math.min(v,cap)))*rem;
  return out;
}
function projCombine(m, w){
  let s=0, ws=0;
  if(w) for(const [k,v] of Object.entries(w)){ if(m[k]!=null){ s+=v*m[k]; ws+=v; } }
  if(ws>0) return {value:s/ws, calibrated:true};
  const vals = Object.values(m).filter(x=>x!=null);
  return vals.length ? {value:pMean(vals), calibrated:false} : null;
}
function projCalibrate(){
  const months = Object.values(proj.hist).filter(h=>h.complete).map(h=>h.month).sort();
  const cache = {};
  months.forEach(ym=>{ cache[ym] = projSeries(ym); });
  Object.keys(proj.hist).forEach(ym=>{ if(!cache[ym]) cache[ym]=projSeries(ym); });
  const curveOf = list => list.length ? (f=>pMean(list.map(o=>projCurveAt(cache[o],f)).filter(x=>x!=null))) : null;
  const capOf = list => { const vals=list.reduce((a,o)=>a.concat(cache[o].du),[]); return vals.length>=15 ? pQuantile(vals,.95) : Infinity; };
  const obs = [];
  for(const ym of months){
    const s = cache[ym]; if(!s.total) continue;
    const others = months.filter(x=>x<ym);
    if(others.length<2) continue;
    const curveFn = curveOf(others), cap = capOf(others), base = projBase(ym), seas = projSeasonal(ym);
    for(let d=1; d<s.n; d++){
      const duVals = s.du.slice(0,d);
      const m = projModels({n:s.n, d, R:pSum(duVals), duVals, curveFn, cap, base, seas, recent:projRecent(ym,d,cache)});
      obs.push({ym, d, n:s.n, b:projBucket(d/s.n).k, actual:s.total, m});
    }
  }
  const W={}, stats={};
  for(const b of PROJ_BUCKETS){
    const ob = obs.filter(o=>o.b===b.k); const mse={}, mape={}, cnt={};
    PROJ_MODELS.forEach(k=>{
      const errs = ob.filter(o=>o.m[k]!=null).map(o=>o.m[k]/o.actual-1);
      if(errs.length>=3){ mse[k]=pMean(errs.map(e=>e*e)); mape[k]=pMean(errs.map(Math.abs)); cnt[k]=errs.length; }
    });
    const inv = fromEntries(Object.entries(mse).map(([k,v])=>[k,1/Math.max(v,1e-6)]));
    const tot = pSum(Object.values(inv));
    W[b.k] = tot ? fromEntries(Object.entries(inv).map(([k,v])=>[k,v/tot])) : null;
    stats[b.k] = {mape, cnt, n:ob.length};
  }
  obs.forEach(o=>{ const c=projCombine(o.m, W[o.b]); o.comb = c ? c.value : null; });
  const extra = []; let extraMonths = 0;
  const pool = obs.filter(o=>o.comb).concat(extra);
  for(const b of PROJ_BUCKETS){
    const ratios = pool.filter(o=>o.b===b.k && o.comb).map(o=>o.actual/o.comb);
    stats[b.k].q20 = ratios.length>=5 ? pQuantile(ratios,.2) : null;
    stats[b.k].q80 = ratios.length>=5 ? pQuantile(ratios,.8) : null;
    stats[b.k].q50 = ratios.length>=5 ? pQuantile(ratios,.5) : null;
    stats[b.k].mapeComb = ratios.length ? pMean(ratios.map(r=>Math.abs(1/r-1))) : null;
    stats[b.k].nComb = ratios.length;
  }
  const checkpoints = [5,10,15,20].map(d=>{
    const e = pool.filter(o=>o.d===d && o.comb).map(o=>Math.abs(o.comb/o.actual-1));
    return {d, err:pMean(e), n:e.length};
  });
  // cobertura fora da amostra: para cada mês, faixa montada só com os outros meses
  let cin = 0, ctot = 0; const pm = [...new Set(pool.map(o=>o.ym))];
  if(pm.length>=3) for(const m of pm) for(const b of PROJ_BUCKETS){
    const tr = pool.filter(o=>o.b===b.k && o.ym!==m).map(o=>o.actual/o.comb); if(tr.length<5) continue;
    const lo = pQuantile(tr,.2), hi = pQuantile(tr,.8);
    pool.filter(o=>o.b===b.k && o.ym===m).forEach(o=>{ ctot++; const r = o.actual/o.comb; if(r>=lo && r<=hi) cin++; });
  }
  const coverage = ctot ? cin/ctot : null;
  const firstM0 = obs.find(o=>o.d===1 && o.m.M0!=null);
  return {months, cache, W, stats, checkpoints, obs, curveFn:curveOf(months), cap:capOf(months), hasM0:!!firstM0, extraMonths, coverage, poolMonths:pm.length};
}
function projRemainderBand(calib, n, d){
  const k = n - d; if(k<=0) return null;
  const months = calib.months.map(ym=>calib.cache[ym]).filter(x=>x && x.n>0 && x.total);
  if(months.length<2) return null;
  const pool = j => { const out = []; months.forEach(x=>{ for(let t=j-1; t<=j+1; t++){ if(t>=1 && t<=x.n) out.push(x.du[x.n - t]); } }); return out; };
  const pools = []; for(let j=1; j<=k; j++) pools.push(pool(j));
  if(pools.some(q=>!q.length)) return null;
  const mean = pSum(pools.map(q=>pMean(q)));
  let seed = (12345 + n*31 + d*7) >>> 0; const rnd = () => { seed = (seed*1664525 + 1013904223) >>> 0; return seed/4294967296; };
  const B = 1500, sums = new Array(B);
  for(let b=0; b<B; b++){ let t = 0; for(const q of pools) t += q[Math.floor(rnd()*q.length)]; sums[b] = t; }
  return {lo:pQuantile(sums,.2) - mean, hi:pQuantile(sums,.8) - mean, k};
}
function projAsOf(calib, ym, s, d){
  const duVals = s.du.slice(0,d); const R = pSum(duVals);
  const m = projModels({n:s.n, d, R, duVals, curveFn:calib.curveFn, cap:calib.cap, base:projBase(ym), seas:projSeasonal(ym), recent:projRecent(ym,d,calib.cache)});
  const b = projBucket(d/s.n); const c = projCombine(m, calib.W[b.k]);
  if(!c) return null;
  const st = calib.stats[b.k];
  const prov = Math.max(R, c.value*(st.q50!=null ? st.q50 : 1));
  let cons = st.q20!=null ? Math.max(R, c.value*st.q20) : null;
  let otim = st.q80!=null ? Math.max(R, c.value*st.q80) : null;
  const fl = projRemainderBand(calib, s.n, d);
  let floorUsed = false;
  if(fl){
    const lo = Math.max(R, prov + fl.lo), hi = prov + fl.hi;
    if(cons==null || lo < cons){ cons = lo; floorUsed = true; }
    if(otim==null || hi > otim){ otim = hi; floorUsed = true; }
  }
  return {d, R, m, raw:c.value, prov, calibrated:c.calibrated, bucket:b, cons, otim, floorUsed};
}

function projection(rows, cutoff, calendar, next) {
  calendar=calendar||{};
  proj={hist:{},monthly:{},extraHol:calendar.feriadosAdicionais||[],recesso:calendar.recesso||null};
  const ym=ymOf(cutoff), start=prevYM(ym,24);
  for(let m=start;m<=ym;m=prevYM(m,-1)) proj.hist[m]={month:m,complete:m<ym,days:{}};
  rows.forEach(r=>{if(r.data>cutoff||r.data<firstOfYM(start))return;const h=proj.hist[ymOf(r.data)];if(!h)return;const x=h.days[r.data]||(h.days[r.data]={g:0,gc:0,e:0,ec:0});x.g+=Number(r.valor)||0;x.gc+=Number(r.quantidade)||0;});
  const calib=projCalibrate();
  const target=next?prevYM(ym,-1):ym;
  if(next)proj.hist[target]={month:target,complete:false,days:{}};
  const series=projSeries(target), B=series.B, n=B.length;
  const elapsed=next?0:B.filter(d=>d<=cutoff).length;
  // Sem dado histórico anterior, o início do mês não cria previsão artificial.
  let now=n ? projAsOf(calib,target,series,elapsed) : null;
  if(next && n){
    const current=projSeries(ym), done=current.B.filter(d=>d<=cutoff).length;
    const base=projBase(ym), seasonal=projSeasonal(target), models={};
    if(base)models.M0=base.rate*(seasonal||1)*n;
    if(done)models.M1=current.total/done*n;
    const recent=projRecent(ym,done,calib.cache);
    if(recent.length>=5)models.M3=pMean(recent)*n;
    const combined=projCombine(models,calib.W.b1), st=calib.stats.b1;
    now=combined?{prov:combined.value*(st.q50||1),cons:st.q20!=null?combined.value*st.q20:null,otim:st.q80!=null?combined.value*st.q80:null,m:models,calibrated:combined.calibrated,bucket:{k:'b1'}}:null;
  }
  const historyMonths=calib.months.filter(m=>calib.cache[m].total>0).length;
  let probable=now?now.prov:null, low=now?now.cons:null, high=now?now.otim:null;
  if(!historyMonths && !series.total) probable=low=high=null;
  if(elapsed===n && n){probable=low=high=series.total;}
  const real=series.total;
  return {month:target,real,probable,low,high,elapsed,days:n,dates:B,values:series.du,
    historyMonths,models:now?now.m:{},weights:now?calib.W[now.bucket.k]:null,
    calibrated:now?now.calibrated:false,pace:elapsed?real/elapsed:null};
}
function normalized(s){return String(s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toUpperCase().replace(/[^A-Z0-9 ]+/g,' ').replace(/\s+/g,' ').trim();}
function rosterMap(roster){const map={};roster.forEach(v=>map[String(v.id)]=v);return map;}
function teams(roster,configured){
  if(Array.isArray(configured)){
    const owners={};configured.forEach(t=>(t.supervisores||[]).forEach(id=>owners[String(t.empresaId)+':'+id]=t.id));
    return configured.map(t=>({id:t.id,name:t.nome,empresaId:String(t.empresaId),members:roster.filter(v=>{
      const own=owners[String(t.empresaId)+':'+v.id];
      return own?own===t.id:(t.supervisores||[]).some(id=>String(id)===String(v.supervisorId));
    }).map(v=>String(v.id))}));
  }
  const all={};roster.forEach(v=>{if(+v.supervisorId)all[String(v.supervisorId)]={id:String(v.supervisorId),name:v.supervisor||('Supervisor '+v.supervisorId)};});
  const by=rosterMap(roster);Object.keys(all).forEach(id=>{if(by[id])all[id].name=by[id].nome;all[id].members=roster.filter(v=>String(v.id)===id||String(v.supervisorId)===id).map(v=>String(v.id));});
  const list=Object.keys(all).map(id=>all[id]).sort((a,b)=>a.name.localeCompare(b.name,'pt-BR'));
  list.push({id:'none',name:'Não classificado',members:roster.filter(v=>!+v.supervisorId&&!all[String(v.id)]).map(v=>String(v.id)).concat(['0'])});
  return list;
}
function inTeam(team,row){return (!team.empresaId||team.empresaId===String(row.empresaId))&&team.members.indexOf(String(row.vendedorId))>=0;}
function groupName(r){return (r.proposta||'').trim()?((r.grupoProposta||'').trim()||r.proposta.trim()):((r.grupo||'').trim()||(r.cliente||'Sem cliente'));}
const NATIONAL_HOL_CACHE = {};
function nationalHolidays(year){
  if(NATIONAL_HOL_CACHE[year])return NATIONAL_HOL_CACHE[year];
  // Mesmo calendário nacional do motor PHP; feriados locais e recessos da projeção não entram aqui.
  const fixed=['01-01','04-21','05-01','09-07','10-12','11-02','11-15','12-25'].concat(year>=2024?['11-20']:[]);
  const passion=addDays(projEaster(year),-2);
  return NATIONAL_HOL_CACHE[year]=new Set(fixed.map(day=>year+'-'+day).concat([fmtDate(passion)]));
}
function daily(rows,period,metric,ia){
  const dates=[];
  for(let d=period.inicio;d<=period.fim;d=addDaysStr(d,1)){const wd=parseDateStr(d).getDay();if(wd>0&&wd<6&&!nationalHolidays(+d.slice(0,4)).has(d))dates.push(d);}
  const totals={};dates.forEach(d=>totals[d]=0);
  rows.forEach(r=>{if(r.data<period.inicio||r.data>period.fim||(ia&&!+r.ia))return;const target=dates.find(d=>d>=r.data)||dates[dates.length-1];if(target)totals[target]+=metric==='count'?1:Number(r[metric==='toneladas'?'toneladas':'valor'])||0;});
  return {dates,values:dates.map(d=>totals[d])};
}
// Mês escolhido, com atalhos ancorados no último dia disponível desse mês.
function monthPeriod(month,preset,today){
  today=today||localToday();
  if(!/^\d{4}-(0[1-9]|1[0-2])$/.test(month)||month>today.slice(0,7))return null;
  const fim=month===today.slice(0,7)?today:lastOfYM(month),first=firstOfYM(month);
  const inicio=preset==='month'?first:addDaysStr(fim,1-Number(preset));
  return {inicio:inicio<first?first:inicio,fim};
}
// Intervalo anterior contíguo, com a mesma quantidade de dias corridos (inclusive).
function previousPeriod(period){
  const utc=s=>{const [y,m,d]=s.split('-').map(Number);return Date.UTC(y,m-1,d);};
  const days=Math.round((utc(period.fim)-utc(period.inicio))/86400000)+1;
  return {inicio:addDaysStr(period.inicio,-days),fim:addDaysStr(period.inicio,-1)};
}
function dailyIndicator(source,filters,mode,criterion){
  const type=['ia','closed','conversion'].includes(criterion)?'orcamentos':mode;
  const raw=select(source.fontes[type]||[],filters,source.vendedores,source.equipes);
  let rows=criterion==='toneladas'?raw.filter(r=>!+r.transferencia):summary(raw).rows;
  if(criterion==='ia')rows=rows.filter(r=>+r.ia);
  if(criterion==='closed')rows=rows.filter(r=>+r.convertido);
  const dates=daily([],source.periodo,'count',false).dates, buckets=new Map(dates.map(d=>[d,[]])), targets=new Map();
  rows.forEach(r=>{
    if(r.data<source.periodo.inicio||r.data>source.periodo.fim)return;
    if(!targets.has(r.data))targets.set(r.data,dates.find(d=>d>=r.data)||dates[dates.length-1]);
    const target=targets.get(r.data);if(target)buckets.get(target).push(r);
  });
  return {dates,values:dates.map(d=>{
    const bucket=buckets.get(d),s=summary(bucket);
    if(criterion==='ticket')return s.ticket;
    if(criterion==='sellers')return s.sellers;
    if(criterion==='conversion')return s.conversion;
    if(criterion==='value')return s.value;
    if(criterion==='toneladas')return bucket.reduce((sum,r)=>sum+(Number(r.toneladas)||0),0);
    return s.count;
  })};
}
// Comparações preservam o escopo escolhido e a data de emissão de cada etapa.
function comparisonDaily(source,filters,criterion,metric){
  const type=criterion==='closed'||criterion==='ia'?'orcamentos':criterion;
  let rows=summary(select(source.fontes[type]||[],filters,source.vendedores,source.equipes)).rows;
  if(criterion==='closed')rows=rows.filter(r=>+r.convertido);
  if(criterion==='ia')rows=rows.filter(r=>+r.ia);
  if(metric==='toneladas')rows=rows.filter(r=>!+r.transferencia);
  return daily(rows,source.periodo,metric,false);
}
function select(rows,filters,roster,configured){
  const list=teams(roster,configured), team=list.find(t=>t.id===filters.team);
  return rows.filter(r=>(filters.company==='all'||String(r.empresaId)===filters.company)
    && (filters.team==='all'||!!team&&inTeam(team,r))
    && (filters.seller==='all'||String(r.vendedorId)===filters.seller)
    && (filters.representative==='all'||String(r.representanteId)===filters.representative)
    && (!filters.search||normalized(groupName(r)+' '+(r.cliente||'')).indexOf(normalized(filters.search))>=0));
}
function summary(rows){
  const valid=rows.filter(r=>!+r.excluido), cancelled=rows.filter(r=>+r.excluido);
  const value=pSum(valid.map(r=>Number(r.valor)||0)), converted=valid.filter(r=>+r.convertido).length;
  return {rows:valid,count:valid.length,value,ticket:valid.length?value/valid.length:null,
    sellers:new Set(valid.filter(r=>+r.vendedorId).map(r=>String(r.vendedorId))).size,
    ia:valid.filter(r=>+r.ia).length,converted,conversion:valid.length?100*converted/valid.length:null,
    cancelled:cancelled.length,cancelledValue:pSum(cancelled.map(r=>Number(r.valor)||0))};
}
function group(rows,key,name){const map=new Map();rows.forEach(r=>{const k=key(r);if(!map.has(k))map.set(k,{id:k,name:name(r),count:0,value:0,ia:0,rows:[]});const g=map.get(k);g.count++;g.value+=Number(r.valor)||0;g.ia+=+r.ia?1:0;g.rows.push(r);});return Array.from(map.values());}
function csvCell(value){let s=String(value==null?'':value);if(/^[=+@\-\t\r]/.test(s))s="'"+s;return '"'+s.replace(/"/g,'""')+'"';}
function parseGoal(value,unit){
  const text=String(value).trim(), decimals=unit==='toneladas'?3:2;
  let number;
  if(/^\d{1,3}(\.\d{3})+(,\d+)?$/.test(text))number=Number(text.replace(/\./g,'').replace(',','.'));
  else if(/^\d+(,\d+)?$/.test(text))number=Number(text.replace(',','.'));
  else if(/^\d+\.\d+$/.test(text))number=Number(text);
  else return null;
  const fraction=text.includes(',')?text.split(',')[1]:/^\d{1,3}(\.\d{3})+$/.test(text)?'':text.includes('.')?text.split('.')[1]:'';
  if(fraction.length>decimals||!Number.isFinite(number)||number<=0||number>1e12)return null;
  return number;
}
function goalSeries(goal,days){return Array.from({length:days},(_,index)=>goal*(index+1)/days);}
root.CotacoesModel={projection,teams,inTeam,select,summary,group,groupName,normalized,daily,monthPeriod,previousPeriod,dailyIndicator,comparisonDaily,csvCell,fmtDate,addDays,firstOfYM,lastOfYM,parseGoal,goalSeries};
if(typeof module!=='undefined'&&module.exports)module.exports=root.CotacoesModel;
})(typeof window!=='undefined'?window:global);
