'use strict';
const assert=require('assert'), M=require('../painel-cotacoes-model');
const roster=[{id:10,nome:'Supervisor A',supervisorId:0},{id:20,nome:'Supervisor B',supervisorId:0},
 {id:1,nome:'Vendedor 1',supervisorId:10,supervisor:'Supervisor A',supervisor2Id:20},
 {id:2,nome:'Vendedor 2',supervisorId:20,supervisor:'Supervisor B'},{id:3,nome:'Sem supervisor',supervisorId:0,supervisor2Id:10}];
const docs=[{id:1,vendedorId:1,empresaId:1,representanteId:5,valor:100,ia:1,convertido:1,excluido:0,cliente:'Cliente A'},
 {id:2,vendedorId:10,empresaId:1,representanteId:5,valor:200,ia:0,convertido:0,excluido:0,cliente:'Cliente B'},
 {id:3,vendedorId:2,empresaId:2,representanteId:5,valor:300,ia:0,convertido:1,excluido:0,cliente:'Cliente A'},
 {id:4,vendedorId:3,empresaId:2,representanteId:6,valor:50,excluido:1,cliente:'Cancelado'}];
const f={team:'all',company:'all',seller:'all',representative:'all',search:''};
let s=M.summary(M.select(docs,f,roster));assert.equal(s.value,600);assert.equal(s.count,3);assert.equal(s.converted,2);assert.equal(s.conversion,200/3);assert.equal(s.cancelled,1);
s=M.summary(M.select(docs,Object.assign({},f,{team:'10'}),roster));assert.equal(s.value,300);assert.equal(s.count,2,'Supervisor incluído na própria equipe');
s=M.summary(M.select(docs,Object.assign({},f,{team:'20'}),roster));assert.equal(s.value,300,'Supervisor 2 não duplica vendedor');
assert.equal(M.select(docs,Object.assign({},f,{team:'none'}),roster)[0].vendedorId,3,'Supervisor 2 isolado não classifica equipe');
assert.equal(M.summary(M.select(docs,Object.assign({},f,{company:'1'}),roster)).value,300);
assert.equal(M.select(docs,Object.assign({},f,{seller:'1',company:'2'}),roster).length,0);
assert.equal(M.select(docs,Object.assign({},f,{representative:'6'}),roster).length,1);
const commercialTeams=[{id:'a',nome:'Equipe A',empresaId:1,supervisores:[10,20]},
 {id:'b',nome:'Equipe B',empresaId:1,supervisores:[30]},
 {id:'c',nome:'Equipe C',empresaId:1,supervisores:[40]},
 {id:'d',nome:'Equipe D',empresaId:2,supervisores:[50]}];
const extended=roster.concat([{id:99,nome:'Outro cadastro',supervisorId:999,supervisor:'Supervisor fora do comercial'},
 {id:50,nome:'Supervisor D',supervisorId:0},{id:4,nome:'Vendedor D',supervisorId:50,supervisor2Id:10}]);
const fixed=M.teams(extended,commercialTeams);
assert.deepEqual(fixed.map(t=>t.name),['Equipe A','Equipe B','Equipe C','Equipe D'],'Somente equipes configuradas, sem todos os supervisores e sem botão Não classificado');
assert.deepEqual(fixed[0].members.sort(),['1','10','2','20'],'Dois códigos de supervisor podem compor uma equipe');
assert.equal(M.summary(M.select(docs,f,extended,commercialTeams)).value,600,'Configurar equipes não diminui o consolidado');
assert.equal(M.summary(M.select(docs,Object.assign({},f,{team:'a'}),extended,commercialTeams)).value,300,'Equipe limita a empresa, mesmo quando vendedor existe em outra empresa');
assert.equal(M.select(docs,Object.assign({},f,{team:'none'}),extended,commercialTeams).length,0);
assert(!fixed[0].members.includes('3')&&!fixed[0].members.includes('4'),'Supervisor 2 não interfere na equipe');
const owners=M.teams(extended.concat([{id:30,nome:'Supervisor B',supervisorId:10}]),commercialTeams);
assert(!owners[0].members.includes('30')&&owners[1].members.includes('30'),'Supervisor comercial tem sua própria equipe sem duplicidade');
assert.equal(M.select([{empresaId:1,vendedorId:99,valor:15}],f,extended,commercialTeams).length,1,'Movimento de equipe não configurada permanece em Todas');
assert.equal(M.groupName({proposta:'Projeto A',grupo:'Grupo B',cliente:'C'}),'Projeto A');
assert.equal(M.groupName({proposta:'Projeto A',grupoProposta:'Grupo A',grupo:'Grupo B',cliente:'C'}),'Grupo A');
assert.equal(M.normalized('  Açõs, S.A. '),'ACOS S A');
assert.equal(M.summary([{valor:-50,excluido:0}]).value,-50,'Orçamento negativo não é zerado');
assert.equal(M.group(docs,r=>M.normalized(M.groupName(r)),M.groupName).length,3);
assert.equal(M.summary([]).conversion,null);assert.equal(M.summary([]).ticket,null);
assert.equal(M.csvCell('=SUM(A1)'), '"\'=SUM(A1)"');
const daily=[];for(let d=new Date(2024,9,1);d<=new Date(2026,9,13);d.setDate(d.getDate()+1))if(d.getDay()>0&&d.getDay()<6)daily.push({data:M.fmtDate(d),valor:100,quantidade:1});
let p=M.projection(daily,'2026-10-13',{},false);assert.equal(p.real,900);assert.equal(p.historyMonths,24);assert(p.probable>=p.real&&p.low<=p.probable&&p.high>=p.probable);assert(p.calibrated);assert.equal(p.days,21);
assert.deepEqual(M.projection(daily.concat([{data:'2026-10-14',valor:99999}]),'2026-10-13',{},false),p,'Data posterior à referência não altera projeção');
const next=M.projection(daily,'2026-10-13',{},true);assert.equal(next.month,'2026-11');assert.equal(next.real,0);assert(next.probable>0);
const closed=M.projection(daily,'2026-09-30',{},false);assert.equal(closed.probable,closed.real);assert.equal(closed.low,closed.real);
const empty=M.projection([],'2026-10-13',{},false);assert.equal(empty.probable,null);assert.equal(empty.low,null);assert.equal(empty.high,null);
const shutdown=M.projection(daily,'2026-10-13',{recesso:{de:'10-01',ate:'10-31'}},false);assert.equal(shutdown.days,0);assert.equal(shutdown.probable,null);
const weekend=M.daily([{data:'2026-10-09',valor:10,toneladas:.1},{data:'2026-10-10',valor:20,toneladas:.2},{data:'2026-10-11',valor:30,toneladas:.3},{data:'2026-10-12',valor:40,toneladas:.4}],{inicio:'2026-10-09',fim:'2026-10-13'},'value',false);
assert.deepEqual(weekend,{dates:['2026-10-09','2026-10-13'],values:[10,90]},'Fim de semana e feriado seguem para o próximo dia útil, preservando valores');
assert.equal(M.daily([{data:'2026-10-10',toneladas:.5}],{inicio:'2026-10-09',fim:'2026-10-13'},'toneladas',false).values[1],.5);
assert.deepEqual(M.daily([{data:'2026-10-12',ia:1}],{inicio:'2026-10-09',fim:'2026-10-13'},'count',true),{dates:['2026-10-09','2026-10-13'],values:[0,1]},'Comparação IA usa os mesmos dias úteis');
assert.deepEqual(M.daily([],{inicio:'2026-10-10',fim:'2026-10-12'},'count',false),{dates:[],values:[]},'Não reintroduzir feriados quando o intervalo não tem dia útil');
assert(!M.daily([],{inicio:'2026-04-02',fim:'2026-04-06'},'count',false).dates.includes('2026-04-03'),'Sexta-feira Santa móvel segue o calendário do sistema');
const nextBusiness=M.projection([{data:'2026-10-10',valor:100,quantidade:1}],'2026-10-13',{},false);
assert.equal(nextBusiness.values[nextBusiness.dates.indexOf('2026-10-13')],100,'Projeção desloca sábado e feriado para o dia útil seguinte');
console.log('OK: equipes, Supervisor 1, consolidado, conversão, clientes, CSV e projeções com histórico.');
assert.equal(M.parseGoal('5.000.000,00','reais'),5000000);
assert.equal(M.parseGoal('1234.50','reais'),1234.5);
assert.equal(M.parseGoal('1.500,125','toneladas'),1500.125);
for(const bad of ['0','-5','NaN','1e9','<img>','0,001','1,234'])assert.equal(M.parseGoal(bad,'reais'),null);
assert.deepEqual(M.goalSeries(100,4),[25,50,75,100]);
assert.deepEqual(M.goalSeries(100,0),[]);
