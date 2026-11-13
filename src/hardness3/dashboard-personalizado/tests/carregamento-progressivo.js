const fs=require('fs'),vm=require('vm'),assert=require('assert');
class AbortController { constructor(){this.signal={aborted:false};} abort(){this.signal.aborted=true;} }
class DOMException extends Error { constructor(message,name){super(message);this.name=name;} }
const window={},calls=[],token='a'.repeat(32),controller=new AbortController();let responses=[],progress=[];
const context={window,URL,location:{href:'http://localhost/index.html'},DOMException,setTimeout,Date,fetch:async(url,options)=>{calls.push({url,options});return responses.shift();}};
vm.runInNewContext(fs.readFileSync(__dirname+'/../carregamento-progressivo.js','utf8'),context);
function response(status,data){return {status,ok:status<400,json:async()=>data};}
(async()=>{
responses=[response(202,{carregamento:{token,intervalo:1,intervalos:25,registros:2000}}),response(202,{carregamento:{token,intervalo:2,intervalos:25,registros:4000}}),response(200,{total:5000,cargaToken:token})];
let data=await window.DashboardProgressivo.request('data.php?f_cliente=EPM',{onProgress:p=>progress.push(p)});
assert.equal(data.total,5000);assert.equal(calls.length,3);assert.equal(progress.length,2);
assert(calls.every(c=>new URL(c.url).searchParams.get('f_cliente')==='EPM'));
assert.equal(new URL(calls[1].url).searchParams.get('cargaToken'),token);
responses=[response(202,{carregamento:{token,intervalo:1,intervalos:25}})];let before=calls.length;
try {await window.DashboardProgressivo.request('data.php',{signal:controller.signal,onProgress:()=>controller.abort()});assert.fail('Esperava cancelamento');} catch(e){assert.equal(e.name,'AbortError');}
assert.equal(calls.length,before+1,'Cancelamento impede consultas seguintes');
responses=[response(422,{error:'Falha simulada'})];try {await window.DashboardProgressivo.request('data.php');assert.fail('Esperava erro');} catch(e){assert.equal(e.message,'Falha simulada');}
let from={},to={value:'2026-10-14',addEventListener:(e,f)=>{to.change=f;}};window.DashboardProgressivo.limitDates(from,to);assert.equal(from.min,'2024-10-14');to.value='2026-02-28';to.change();assert.equal(from.min,'2024-02-28');
console.log('OK: continuação sequencial, filtros, progresso, cancelamento, falha sem parcial e limite de datas.');
})().catch(e=>{console.error(e);process.exitCode=1;});
