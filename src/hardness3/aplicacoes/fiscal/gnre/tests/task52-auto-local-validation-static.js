const fs=require('fs'),vm=require('vm'),assert=require('assert');
const source=fs.readFileSync(__dirname+'/../public/task34-transmission-modal.js','utf8');
const endpoint=fs.readFileSync(__dirname+'/../public/task34-modal-state.php','utf8');
const index=fs.readFileSync(__dirname+'/../public/index.php','utf8');
const context=fs.readFileSync(__dirname+'/../public/task34-cycle-context.php','utf8');
const draft=fs.readFileSync(__dirname+'/../src/GuideDraftController.php','utf8');
const sandbox={window:{},URLSearchParams:URLSearchParams,location:{search:''}};vm.runInNewContext(source,sandbox);
const Flow=sandbox.window.GnreTask34Flow;
function store(){let values={};return{getItem:key=>values[key]||null,setItem:(key,value)=>values[key]=value,removeItem:key=>delete values[key]};}
function state(extra){return Object.assign({guide_status:'R',xml_valid:false,validation_errors:[],attempt:null,queue:null,authorized:false,pdf_id:0,terminal:''},extra||{});}
const fixtures=[
  {name:'um item',items:[{GNRE003_Codigo_Receita:'1000',GNRE003_Valor:'10.00'}]},
  {name:'múltiplos documentos',items:[{GNRE003_Codigo_Receita:'1000',GNRE003_Valor:'10.00',document:1},{GNRE003_Codigo_Receita:'1000',GNRE003_Valor:'20.00',document:2}]},
  {name:'múltiplas receitas',items:[{GNRE003_Codigo_Receita:'1000',GNRE003_Valor:'10.00'},{GNRE003_Codigo_Receita:'2000',GNRE003_Valor:'20.00'}]}
];
fixtures.forEach(fixture=>{
  const flow=new Flow(store(),1000),key='guide-'+fixture.name;
  flow.begin(key);
  let result=flow.step(key,state(),true);
  assert.equal(result.post,'generate_xml',fixture.name+' deve iniciar geração local automaticamente');
  result=flow.step(key,state({xml_valid:true,preview:{items:fixture.items,environment:'Homologação'}}),true);
  assert.equal(result.action,'transmit_homologation',fixture.name+' deve parar na confirmação');
  assert.equal(result.post,undefined,fixture.name+' não pode transmitir automaticamente');
});
const invalid=new Flow(store(),1000);invalid.begin('invalid');const failed=invalid.step('invalid',state({validation_errors:['Campo obrigatório ausente']}),true);
assert.equal(failed.action,'edit_data');assert.equal(failed.post,undefined);assert.equal(invalid.get('invalid'),null);
assert(source.includes("result.post='generate_xml'")&&source.includes('Validando dados e gerando XML…'));
assert(source.includes("out.action==='edit_data'")&&source.includes('Corrigir dados e itens'));
assert(endpoint.includes("'validation_errors'=>$validationErrors"));
assert(endpoint.includes("'cycle'=>['active'=>$cycleActive,'origin_tab'=>$originTab]"));
assert(endpoint.includes('GNRE001_GNRE007_Id')&&endpoint.includes('GnreConfigUfSnapshot::forGuide($db,$guide,$context)'));
assert(endpoint.includes("'0'=>'GNRE Simples'")&&endpoint.includes("'2'=>'Múltiplas receitas'"));
assert(source.includes('data.type_label||data.type||'));
assert(index.includes("if($action==='generate_xml')")&&index.includes("elseif($action==='transmit_homologation')"));
assert(index.includes("function gnre34_origin_tab")&&index.includes("'&tab='.$origin.'&gnre34=1"));
assert(source.includes("originTab=(originTab==='processar'?'processar':'dados')")&&!source.includes("tab=processar&gnre34=1"));
assert(context.includes("unset($_SESSION[GNRE_SESSION]['temporary']['gnre34_cycle']")&&!context.includes('GNRE004'));
/* Uma correção só descarta o diagnóstico após persistência: falha de gravação
   mantém a sessão que contém o erro; sucesso limpa XML, erro e ciclo. */
assert(index.includes("'gnre34_xml_result'][$company][$guideId]")&&index.includes("'gnre34_cycle'][$company][$guideId]"));
assert(index.includes("if(!$ok)gnre_abort('Não foi possível gravar o item.',409);gnre_invalidate_local_xml($context,$guideId);"));
assert(!draft.includes("if($action==='guide_draft_save_item')gnre_invalidate_local_xml"));
assert(draft.includes("$transaction->commit($db);mysqli_close($db);$db=null;gnre_invalidate_local_xml($context,$guideId);"));
const generateBranch=index.slice(index.indexOf("if($action==='generate_xml')"),index.indexOf("elseif($action==='transmit_homologation')"));
assert(!generateBranch.includes('gnre_transmit_atomic')&&!generateBranch.includes('GNRE004'));
console.log('Task 52 automatic local validation checks passed');
