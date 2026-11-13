const fs=require('fs'),vm=require('vm'),assert=require('assert');
const source=fs.readFileSync(__dirname+'/../public/task34-transmission-modal.js','utf8');
const endpoint=fs.readFileSync(__dirname+'/../public/task34-modal-state.php','utf8');
const worker=fs.readFileSync(__dirname+'/../src/QueueWorker.php','utf8');
const presentation=fs.readFileSync(__dirname+'/../public/task10-presentation.php','utf8');
const layout=fs.readFileSync(__dirname+'/../public/task10-layout.css','utf8');
const sandbox={window:{},URLSearchParams:URLSearchParams,location:{search:''}};vm.runInNewContext(source,sandbox);
const Flow=sandbox.window.GnreTask34Flow;
const shouldAutoOpen=sandbox.window.GnreTask34ShouldAutoOpen;
const clearUrl=sandbox.window.GnreTask34ClearUrl;
function store(){let v={};return{getItem:k=>v[k]||null,setItem:(k,x)=>v[k]=x,removeItem:k=>delete v[k]};}
function state(x){return Object.assign({guide_status:'R',xml_valid:false,attempt:null,queue:null,authorized:false,pdf_id:0,terminal:''},x||{});}
let s=store(),f=new Flow(s,1000),key='g';
/* Explicit start creates the sole browser authorization and immediately starts local XML generation. */
f.begin(key);let r=f.step(key,state(),true);assert.equal(r.post,'generate_xml');
assert.equal(r.action,undefined);
/* Valid XML pauses at an explicit confirmation; it never posts transmission by itself. */
r=f.step(key,state({xml_valid:true}),true);assert.equal(r.action,'transmit_homologation');assert.equal(r.post,undefined);
let c=f.get(key);c.transmitConfirmed=true;f.set(key,c);
/* A future persisted schedule never consults; a due schedule consults exactly once. */
r=f.step(key,state({guide_status:'P',queue:{next:'1970-01-01 00:00:02',due:false}}),true);assert.equal(r.post,undefined);
f.now=9999999999999;r=f.step(key,state({guide_status:'P',queue:{next:'1970-01-01 00:00:02',due:true}}),true);assert.equal(r.post,'consult_result');
r=f.step(key,state({guide_status:'P',queue:{next:'1970-01-01 00:00:02',due:true}}),true);assert.equal(r.post,undefined);
/* The authorization survives the consultation redirect and permits one PDF acquisition. */
r=f.step(key,state({guide_status:'F',authorized:true}),true);assert.equal(r.post,'obtain_official_pdf');
r=f.step(key,state({guide_status:'F',authorized:true}),true);assert.equal(r.post,undefined);
['reception_rejected','inconclusive','blocked','technical_error','final_not_authorized'].forEach(t=>{f.begin(key);r=f.step(key,state({terminal:t}),true);assert.equal(r.post,undefined);assert.equal(f.get(key),null);});
/* Opening/reopening without a cycle and closing never produce effects. */
r=f.step(key,state({guide_status:'P',queue:{next:'1970-01-01 00:00:00'}}),true);assert.equal(r.post,undefined);
f.begin(key);f.stop(key);r=f.step(key,state(),false);assert.equal(r.post,undefined);
/* Uma sessão local perdida reinicia somente a validação local, sem etapa manual nem transmissão. */
f.begin(key);let old=f.get(key);old.xmlRequested=true;f.set(key,old);r=f.step(key,state({xml_valid:false}),true);assert.equal(r.post,'generate_xml');assert.equal(r.action,undefined);
/* Falhas locais encerram o ciclo sem tentar transmissão e apontam para correção. */
f.begin(key);r=f.step(key,state({validation_errors:['Receita obrigatória']}),true);assert.equal(r.action,'edit_data');assert.equal(r.post,undefined);assert.equal(r.error,true);assert.equal(f.get(key),null);
/* Closing removes both the cycle and the modal flag; redirects reopen only active cycles. */
f.stop(key);assert.equal(shouldAutoOpen('?edit=9&tab=processar&gnre34=1'),true);
f.resume(key,'dados');r=f.step(key,state({cycle:{active:true,origin_tab:'dados'}}),true);assert.equal(r.post,'generate_xml');
f.resume(key,'dados');r=f.step(key,state({cycle:{active:false}}),true);assert.equal(r.error,true);assert.equal(r.post,undefined);assert.equal(f.get(key),null);
let replaced='';clearUrl({search:'?edit=9&tab=processar&foo=x&gnre34=1',pathname:'/gnre/index.php',hash:'#a'},{state:null,replaceState:(_,__,url)=>replaced=url});assert.equal(replaced,'/gnre/index.php?edit=9&tab=processar&foo=x#a');
assert(source.includes('<li><span>1</span>Validar XML</li>')&&source.includes('<li><span>5</span>PDF oficial</li>'));
assert(source.includes('grid-template-columns:repeat(5,minmax(0,1fr))')&&source.includes('@media(max-width:680px)'));
assert(source.includes('Prévia da transmissão')&&source.includes('Visualizar XML')&&source.includes('showPreview(state.xml_valid&&state.preview?state.preview:null)'));
assert(source.includes('xml.textContent=data.xml')&&!source.includes('xml.innerHTML=data.xml'));
assert(endpoint.includes("'preview'=>$preview")&&endpoint.includes("GNRE003_Codigo_Receita")&&endpoint.includes("gnre34_xml_result"));
assert(endpoint.includes("'validation_errors'=>$validationErrors"));
assert(source.includes('Validando dados e gerando XML…')&&source.includes("out.action==='edit_data'")&&source.includes('Corrigir dados e itens'));
assert(source.includes('task34-cycle-context.php')&&source.includes('gnre34_origin_tab:originTab'));
assert(worker.includes('GnrePendingResultPolicy::nextDelaySeconds')&&worker.includes("$pending?GnrePendingResultPolicy::nextDelaySeconds")&&worker.includes('hasConclusiveIndividualResult'));
/* Task 57: Processar só informa estado local/público; ações Portal ficam no modal. */
assert(presentation.includes("$manualShowValidation=$guide&&$guide['GNRE001_Status']==='R'&&!$xmlValidated&&!$manualShowTransmission"));
assert(presentation.includes("$manualShowTransmission=$guide&&$guide['GNRE001_Status']==='R'&&($xmlValidated||$manualHasTransmitFeedback)"));
assert(presentation.includes("$manualShowResult=$guide&&($guide['GNRE001_Status']!=='R'||$consultFlash||($storedResult&&$storedResult['valid']))"));
assert(!layout.includes('[data-gnre-process][data-gnre-manual-view="validation"]'), 'Processar não pode ocultar a conferência local de XML.');
assert(presentation.includes('Próxima consulta permitida em ')&&presentation.includes('O resultado pode ser consultado agora.'));
assert(presentation.includes("preg_replace('~<article class=\"gnre-process-step\" data-gnre-transmission>.*?</article>~s'")&&presentation.includes('gnre-guided-follow-up'));
assert(presentation.includes('$manualFinalAuthorized=$manualStatus===\'F\'&&$pdfEligible')&&presentation.includes('Guia autorizada com sucesso pelo Portal.'));
console.log('Task 34 behavioral checks passed');
