(() => {
  'use strict';
  const el=(tag,text,cls)=>{const node=document.createElement(tag);if(text!=null)node.textContent=String(text);if(cls)node.className=cls;return node;};
  const money=value=>new Intl.NumberFormat('pt-BR',{style:'currency',currency:'BRL'}).format(Number(value||0));
  const number=value=>new Intl.NumberFormat('pt-BR',{maximumFractionDigits:0}).format(Number(value||0));
  const percent=value=>value==null?'Sem base anterior':new Intl.NumberFormat('pt-BR',{maximumFractionDigits:1}).format(value)+'%';
  const date=value=>value.split('-').reverse().join('/');
  const csvCell=value=>{let text=String(value==null?'':value);if(/^\s*[=+\-@]|^[\t\r\n]/.test(text))text="'"+text;return '"'+text.replace(/"/g,'""')+'"';};
  function create(request,navigate) {
    const host=document.getElementById('comparisonView');
    let data=null,page=1,revision=0;
    const sources=el('fieldset',null,'comparison-sources');sources.id='comparisonSources';sources.hidden=true;
    sources.append(el('legend','Comparar'));
    [['notas','Faturamento'],['pedidos','Pedidos'],['orcamentos','Orçamentos']].forEach(([key,title],index)=>{
      const label=el('label'),input=el('input');input.type='radio';input.name='comparisonSource';input.value=key;input.checked=index===0;
      label.append(input,el('span',title));sources.append(label);
    });
    document.getElementById('filters').insertBefore(sources,document.getElementById('periodNote'));
    const caption=el('p',null,'comparison-periods'),summary=el('div',null,'portal-summary'),card=el('article',null,'wide-card comparison-clients-card');
    const header=el('header'),heading=el('div'),exportButton=el('button','Exportar CSV','secondary'),tableHost=el('div',null,'table-scroll');
    exportButton.type='button';exportButton.disabled=true;
    heading.append(el('h3','Comparativo por cliente'),el('p','Selecione um cliente para consultar o período atual. Os grupos seguem o cadastro do painel.'));
    header.append(heading,exportButton);card.append(header,tableHost);
    const footer=el('footer',null,'pagination'),previous=el('button','Anterior','secondary'),next=el('button','Próxima','secondary'),pageLabel=el('span');
    previous.type=next.type='button';footer.append(previous,pageLabel,next);card.append(footer);host.append(caption,summary,card);
    function render() {
      const periods=data.periodos;
      caption.textContent=`Atual: ${date(periods.atual.inicio)} a ${date(periods.atual.fim)} · Anterior: ${date(periods.anterior.inicio)} a ${date(periods.anterior.fim)}`;
      summary.replaceChildren();
      [['Período atual',data.totais.valorAtual,number(data.linhas.length)+' clientes/grupos'],['Mesmo período no ano anterior',data.totais.valorAnterior,'Inclui clientes sem vendas no período atual'],['Diferença',data.totais.diferenca,percent(data.totais.variacao)]].forEach(([title,value,detail])=>{
        const card=el('article',null,'portal-card');card.append(el('p',title,'card-kicker'),el('strong',money(value)),el('small',detail));summary.append(card);
      });
      exportButton.disabled=!data.linhas.length;
      const table=el('table'),head=el('thead'),header=el('tr'),body=el('tbody');
      ['Cliente / grupo','Qtd. anterior','Valor anterior','Qtd. atual','Valor atual','Diferença','Variação'].forEach((title,index)=>{const cell=el('th',title,index?'numeric':'');cell.scope='col';header.append(cell);});head.append(header);table.append(head,body);
      data.linhas.slice((page-1)*50,page*50).forEach(row=>{
        const line=el('tr'),name=el('td',null,'comparison-client'),button=el('button',row.cliente,'text-button');button.type='button';
        button.addEventListener('click',()=>navigate(data.tipo,{inicio:periods.atual.inicio,fim:periods.atual.fim,cliente:{id:row.clienteId,grupoId:row.grupoClienteId,nome:row.cliente}}));
        name.append(button,el('small',row.grupoClienteId>0?'Grupo '+row.grupoClienteId:row.clienteId>0?'Cód. '+row.clienteId:'Sem cadastro'));line.append(name);
        [number(row.quantidadeAnterior),money(row.valorAnterior),number(row.quantidadeAtual),money(row.valorAtual),money(row.diferenca),percent(row.variacao)].forEach(value=>line.append(el('td',value,'numeric')));body.append(line);
      });
      tableHost.replaceChildren(data.linhas.length?table:el('p','Nenhum cliente nos períodos selecionados.','analytics-empty'));
      previous.disabled=page<=1;next.disabled=page*50>=data.linhas.length;pageLabel.textContent='Página '+page+' de '+Math.max(1,Math.ceil(data.linhas.length/50));
    }
    let lastFilters,lastOptions;
    async function load(filters,options) {
      const current=++revision;lastFilters={...filters};lastOptions=options;data=null;page=1;
      host.setAttribute('aria-busy','true');exportButton.disabled=true;previous.disabled=next.disabled=true;summary.replaceChildren();caption.textContent='';tableHost.replaceChildren(el('p','Comparando os clientes…','analytics-empty'));
      try {
        const result=await request('comparativo',{...filters,tipo:sources.querySelector('input:checked').value},options);
        if(current!==revision)return null;
        data=result;render();return result;
      } catch(error) { if(current===revision){tableHost.replaceChildren(el('p',error.name==='AbortError'?'Atualizando comparação…':error.message,'form-status'));}throw error; }
      finally {if(current===revision)host.setAttribute('aria-busy','false');}
    }
    sources.addEventListener('change',()=>{if(lastFilters)load(lastFilters,lastOptions).catch(()=>{});});
    previous.addEventListener('click',()=>{if(data&&page>1){page--;render();}});
    next.addEventListener('click',()=>{if(data&&page*50<data.linhas.length){page++;render();}});
    exportButton.addEventListener('click',()=>{
      if(!data)return;
      const rows=[['Código cliente','Código grupo','Cliente / grupo','Qtd. anterior','Valor anterior','Qtd. atual','Valor atual','Diferença','Variação'],...data.linhas.map(row=>[row.clienteId,row.grupoClienteId,row.cliente,row.quantidadeAnterior,money(row.valorAnterior),row.quantidadeAtual,money(row.valorAtual),money(row.diferenca),percent(row.variacao)])];
      const url=URL.createObjectURL(new Blob(['\uFEFF',rows.map(row=>row.map(csvCell).join(';')).join('\r\n')],{type:'text/csv;charset=utf-8'})),link=el('a');
      link.href=url;link.download='comparativo-clientes-'+data.tipo+'-'+data.periodos.atual.inicio+'.csv';document.body.append(link);link.click();link.remove();setTimeout(()=>URL.revokeObjectURL(url),1000);
    });
    return {load};
  }
  window.PortalComparativo={create};
})();
