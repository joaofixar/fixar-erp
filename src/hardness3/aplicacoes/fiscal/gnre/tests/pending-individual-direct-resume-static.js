const fs=require('fs'),vm=require('vm'),assert=require('assert');
const source=fs.readFileSync(__dirname+'/../public/task34-transmission-modal.js','utf8');
const endpoint=fs.readFileSync(__dirname+'/../public/task34-modal-state.php','utf8');
const sandbox={window:{},URLSearchParams:URLSearchParams,location:{search:''}};vm.runInNewContext(source,sandbox);
const Flow=sandbox.window.GnreTask34Flow;
function store(){let value={};return{getItem:k=>value[k]||null,setItem:(k,v)=>value[k]=v,removeItem:k=>delete value[k]};}
const flow=new Flow(store(),Date.now()),pending={guide_status:'P',resumable_pending:true,xml_valid:false,queue:{state:'PENDENTE',next:'1970-01-01 00:00:00',due:true,seconds_until:0},authorized:false,pdf_id:0,terminal:''};
const result=flow.step('138',pending,true);
assert.equal(result.post,'consult_result','abertura direta de P elegível consulta somente o recibo persistido');
assert.notEqual(result.post,'generate_xml');assert.notEqual(result.action,'transmit_homologation');
assert(endpoint.includes("COUNT(DISTINCT t.GNRE004_Id) AS total"),'read model deve exigir exatamente uma tentativa RECEBIDA');
assert(endpoint.includes("q.GNRE005_Situacao IN ('PENDENTE','PROCESSANDO')"),'read model deve aceitar somente fila pendente/processando');
assert(endpoint.includes("'resumable_pending'=>$resumablePending"),'read model deve expor retomada persistida');
console.log('Pending individual direct resume checks passed');
