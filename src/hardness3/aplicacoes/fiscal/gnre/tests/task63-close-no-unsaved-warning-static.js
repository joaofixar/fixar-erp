const fs=require('fs'),assert=require('assert');
const shell=fs.readFileSync(__dirname+'/../public/gnre-ui.js','utf8');
const queue=fs.readFileSync(__dirname+'/../public/task63-batch.js','utf8');
assert(shell.includes("form.matches('[data-gnre-company-form], [data-task63-form]')"),'seleção da Fila deve ser excluída do alerta de alterações não salvas');
assert(queue.includes("finish.onclick=function(){progress.close();window.location.reload();}"),'Fechar deve recarregar o resumo consolidado');
console.log('Task 63 close has no unsaved-warning check passed');
