(() => {
  'use strict';
  const el = (tag, text, cls) => { const n = document.createElement(tag); if (text != null) n.textContent = text; if (cls) n.className = cls; return n; };
  const svg = (tag, attrs = {}, text) => { const n = document.createElementNS('http://www.w3.org/2000/svg', tag); Object.entries(attrs).forEach(([k,v]) => n.setAttribute(k, v)); if (text != null) n.textContent = text; return n; };
  const num = v => new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 }).format(Number(v || 0));
  const money = v => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
  const pct = v => new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 1 }).format(v || 0) + '%';
  const ratio = (a,b) => Number(b) ? Number(a || 0) / Number(b) * 100 : 0;
  const day = v => v.slice(0,10).split('-').reverse().join('/');
  const unit = (n, one, many) => num(n) + ' ' + (Number(n) === 1 ? one : many);
  const icon = kind => {
    const paths = { orcamentos: 'M7 3h10v18H7zM10 8h4M10 12h4M10 16h2', pedidos: 'M3 7l9-4 9 4v10l-9 4-9-4zM3 7l9 4 9-4M12 11v10', notas: 'M5 21V3h14v18l-3-2-4 2-4-2zM8 8h8M8 12h5', boletos: 'M3 6h18v13H3zM3 10h18M7 15h3', mapa: 'M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18M3 12h18M12 3c5 5 5 13 0 18-5-5-5-13 0-18' };
    const n = svg('svg', {viewBox:'0 0 24 24', 'aria-hidden':'true'}); n.append(svg('path', {d: paths[kind], fill:'none', stroke:'currentColor', 'stroke-width':'1.5', 'stroke-linecap':'round', 'stroke-linejoin':'round'})); return n;
  };
  function create(navigate) {
    const host = document.getElementById('homeView');
    let panels, filters, timer;
    const commissions=(window.PortalPersonalizacao.config.visoes.boletos || {}).naturezaFinanceira==='comissoes';
    const commissionByNote=commissions && (window.PortalPersonalizacao.config.visoes.boletos || {}).comissaoPorNota===true;
    const periodField=(window.PortalPersonalizacao.config.visoes.boletos || {}).campoPeriodo || 'vencimento';
    const periodName={data:'emissão',vencimento:'vencimento',pagamento:'recebimento'}[periodField] || 'vencimento';
    const financialLabel=(standard,commission,perNote=commission)=>commissions?(commissionByNote?perNote:commission):standard;
    const seriesKeys=window.PortalPersonalizacao.config.inicio.series || ['pedidos','notas'];
    const seriesLabels={orcamentos:'Orçamentos',pedidos:'Pedidos',notas:'Faturamento'};
    const seriesColor=key=>(window.PortalPersonalizacao.config.coresSeries || {})[key] || (key==='orcamentos'?'#a4baff':key==='notas'?'#43dfb8':'#70a7ff');
    const comparison=seriesKeys.map(key=>seriesLabels[key]).join(' e ');
    const $ = id => document.getElementById(id);
    const go = (type, selection = {}) => navigate(type, selection);
    const action = (text, type, selection = {}, cls = 'home-link') => { const b = el('button', text, cls); b.type='button'; b.dataset.homeTarget=type; b.addEventListener('click', () => go(type, selection)); return b; };
    function loading() { panels=null; host.setAttribute('aria-busy','true'); if (!host.hasChildNodes()) host.replaceChildren(el('p','Reunindo sua visão geral…','home-loading')); }
    function error() {
      panels=null; host.setAttribute('aria-busy','false');
      host.replaceChildren(el('div','Não foi possível carregar a visão geral. Use Atualizar para tentar novamente.','home-loading'));
    }
    function groupsFor(data, period, field='valor') {
      const start=new Date(period.inicio+'T12:00:00Z'), end=new Date(period.fim+'T12:00:00Z');
      const days=Math.round((end-start)/86400000)+1, size=days>31?7:1, groups=[];
      const lookup = new Map(data.map(row => [row.data.slice(0,10),Number(row[field] || 0)]));
      for(let i=0;i<days;i++) {
        const key=new Date(start.getTime()+i*86400000).toISOString().slice(0,10);
        if(i%size===0) groups.push({start:key,end:key,value:0});
        const g=groups[groups.length-1]; g.end=key; g.value+=lookup.get(key)||0;
      }
      return groups;
    }
    function sparkline(data, field, color) {
      const values=groupsFor(data,filters,field).map(g=>g.value), low=Math.min(0,...values), span=Math.max(1,...values)-low;
      const points=values.map((v,i) => `${3+i/Math.max(1,values.length-1)*154},${37-(v-low)/span*30}`).join(' ');
      const n=svg('svg',{viewBox:'0 0 160 44',class:'home-spark','aria-hidden':'true'});
      n.style.color=color;
      n.append(svg('polyline',{points,fill:'none',stroke:'currentColor','stroke-width':2,'stroke-linejoin':'round','stroke-linecap':'round'})); return n;
    }
    function render(data, period) {
      panels=data; filters={...period}; host.setAttribute('aria-busy','false');
      host.innerHTML = `<div class="home-kpis" id="homeKpis"></div>
        <div class="home-main-grid">
          <article class="home-card home-evolution"><header class="home-heading"><h3>${comparison}</h3><div id="homeSeries" class="home-series" aria-label="Legenda do gráfico"></div></header><div id="homeChart" class="home-chart"></div><p class="home-note">Valores por emissão. Os vínculos entre documentos podem ser de outros períodos.</p></article>
          <article class="home-card home-performance"><header class="home-heading"><h3>Conversão e recebimentos</h3></header><div id="homeRates"></div><p class="home-note">${financialLabel('Conversão por quantidade; recebimento por valor das parcelas do período.', 'Comissão proporcional aos pagamentos dos clientes; não indica repasse ao representante.', 'Comissões liberadas conforme a quitação dos títulos elegíveis; não indica repasse ao representante.')}</p></article>
        </div>
        <div class="home-bottom-grid">
          <article class="home-card"><header class="home-heading"><h3>Pendências do período</h3></header><div id="homeActions" class="home-actions-list"></div></article>
          <article class="home-card"><header class="home-heading"><h3>Principais clientes</h3></header><div id="homeClients"></div><div id="homeClientFoot" class="home-client-foot"></div></article>
        </div><div id="homeMapLink" class="home-map-link"></div>
        <article class="home-card home-segments"><header class="home-heading"><h3>Vendas por distrito</h3></header><div id="homeSegments"></div></article>
        <article class="home-card home-cancellations"><header class="home-heading"><h3>Orçamentos cancelados</h3></header><div id="homeCancellations"></div><p class="home-note">Somente cancelamentos com motivo. Revisões substituídas não são contabilizadas. Principais motivos do período.</p></article>`;
      window.PortalPersonalizacao.home();
      window.PortalAnaliseComercial.segments($('homeSegments'), data.notas.segmentos, data.notas.resumo.valor);
      window.PortalAnaliseComercial.cancellations($('homeCancellations'), data.orcamentos.cancelamentos, data.orcamentos.resumo);
      const q=data.orcamentos.resumo,p=data.pedidos.resumo,n=data.notas.resumo,b=data.boletos.resumo;
      [
        ['orcamentos','Orçamentos',q.valor,unit(q.quantidade,'orçamento emitido','orçamentos emitidos'),'valor'],
        ['pedidos','Pedidos',p.valor,unit(p.quantidade,'pedido registrado','pedidos registrados'),'valor'],
        ['notas','Faturamento',n.valor,unit(n.quantidade,'nota autorizada','notas autorizadas'),'valor'],
        ['boletos',financialLabel('Recebido','Comissão sobre recebimentos','Comissão liberada'),b.recebido,commissionByNote ? `NF-e por ${periodName} no período; títulos elegíveis quitados` : periodField !== 'vencimento' ? `Títulos por ${periodName} no período` : financialLabel('Das parcelas com vencimento no período','Sobre pagamentos dos clientes; títulos do período'),'recebido']
      ].forEach(([type,label,value,caption,field]) => {
        const card=action('',type,{},'home-kpi home-kpi-'+type), top=el('span',null,'home-kpi-top');
        top.append(icon(type),el('span',label),el('span','↗','home-kpi-arrow'));
        card.append(top,el('strong',money(value)));
        if(type==='orcamentos') {
          const breakdown=el('span',null,'home-kpi-segments');
          breakdown.setAttribute('aria-label','Orçamentos por distrito: valor e percentual do total');
          const segments=new Map();
          (data.orcamentos.segmentos || []).forEach(row=>segments.set(row.segmento,(segments.get(row.segmento)||0)+Number(row.valor||0)));
          [['Obra','Obra'],['Construção','Construtora'],['Revenda','Revenda']].forEach(([key,title])=>{
            const amount=segments.get(key)||0,row=el('span',null,'home-kpi-segment');
            row.append(el('span',title),el('span',money(amount)),el('span',pct(ratio(amount,value))));breakdown.append(row);
          });
          card.append(breakdown);
        } else card.append(el('small',caption),sparkline(data[type].evolucao,field,seriesColor(type)));
        $('homeKpis').append(card);
      });
      const rate=(title, value, detail, type, selection, color) => {
        const row=action('',type,selection,'home-rate'), ring=svg('svg',{viewBox:'0 0 54 54','aria-hidden':'true'});
        const fraction=Math.max(0,Math.min(100,value)), c=2*Math.PI*22;
        ring.append(svg('circle',{cx:27,cy:27,r:22,class:'home-rate-track'}),svg('circle',{cx:27,cy:27,r:22,fill:'none',stroke:color,'stroke-width':4,'stroke-dasharray':`${fraction/100*c} ${c}`,'stroke-linecap':fraction?'round':'butt',transform:'rotate(-90 27 27)'}));
        const copy=el('span'); copy.append(el('b',title),el('small',detail)); row.append(ring,copy,el('strong',pct(value))); $('homeRates').append(row);
      };
      rate('Orçamentos convertidos',ratio(q.convertidos,q.quantidade),`${num(q.convertidos)} de ${num(q.quantidade)} com pedido gerado`,'orcamentos',{etapa:'convertido'},'#467eec');
      rate('Pedidos com NF',ratio(p.comNota,p.quantidade),`${num(p.comNota)} de ${num(p.quantidade)} com faturamento iniciado`,'pedidos',{etapa:'com_nota'},'#19a88f');
      rate(financialLabel('Recebimento das parcelas','Comissão sobre recebimentos','Comissão liberada'),ratio(b.recebido,b.valor),money(b.recebido)+' de '+money(b.valor),'boletos',{},'#9270d7');
      const attention=(value,label,detail,type,selection,tone,enabled) => {
        const row=action('',type,selection,'home-attention '+tone), copy=el('span'); row.disabled=!enabled;
        copy.append(el('b',label),el('small',detail)); row.append(el('strong',value),copy,el('span','→','home-arrow')); $('homeActions').append(row);
      };
      attention(money(b.vencido),financialLabel('Saldo vencido','Comissão em títulos vencidos'),financialLabel('Parcelas vencidas ainda com saldo no período.','Comissão vinculada ao saldo vencido dos clientes.'),'boletos',{status:'vencido'},'is-overdue',Number(b.vencido)>0);
      attention(num(q.pendentes),'Orçamentos pendentes',money(q.valorPendente)+' em propostas para acompanhar.','orcamentos',{etapa:'pendente'},'is-pending',Number(q.pendentes)>0);
      attention(num(p.naExpedicao),'Pedidos na expedição','Em preparação, ainda sem nota fiscal autorizada.','pedidos',{etapa:'expedicao'},'is-dispatch',Number(p.naExpedicao)>0);
      attention(num(p.atrasados),'Entrega prevista vencida','Pedidos com prazo anterior a hoje, sem NF autorizada.','pedidos',{etapa:'atrasado'},'is-pending',Number(p.atrasados)>0);
      if(!data.notas.clientes.length) $('homeClients').append(el('p','Nenhum cliente faturado neste período.','home-empty'));
      data.notas.clientes.slice(0,3).forEach((client,i) => {
        const row=action('','notas',{cliente:{id:Number(client.clienteId),grupoId:Number(client.grupoClienteId)||0,nome:client.cliente}},'home-customer');
        const copy=el('span'); copy.append(el('b',client.cliente),el('small',unit(client.quantidade,'nota autorizada','notas autorizadas')));
        row.append(el('span',String(i+1).padStart(2,'0'),'home-rank'),copy,el('strong',money(client.valor))); $('homeClients').append(row);
      });
      const customerStat=el('span');customerStat.append(el('strong',num(n.clientes)),el('small','clientes faturados'));
      const ticketStat=el('span');ticketStat.append(el('strong',money(Number(n.quantidade)?Number(n.valor)/Number(n.quantidade):0)),el('small','ticket médio por nota'));
      $('homeClientFoot').append(customerStat,ticketStat);
      const mapCopy=el('div');mapCopy.append(el('strong','Veja onde suas vendas acontecem'),el('p','Explore estados, municípios e oportunidades na sua carteira.'));
      $('homeMapLink').append(icon('mapa'),mapCopy,action('Explorar vendas por região →','regioes',{},'home-map-button'));
      renderSeries();renderChart();
    }
    function renderSeries() {
      $('homeSeries').replaceChildren();
      seriesKeys.forEach(type => {
        const label=seriesLabels[type];
        const item=el('span',null,'home-series-item '+type);
        const swatch=el('i');swatch.style.background=seriesColor(type);item.append(swatch,el('span',label));$('homeSeries').append(item);
      });
    }
    function renderChart() {
      if(!panels || host.hidden || !$('homeChart'))return;
      const target=$('homeChart'), series=Object.fromEntries(seriesKeys.map(key=>[key,groupsFor(panels[key].evolucao,filters)])), groups=series[seriesKeys[0]];
      if(!seriesKeys.some(key=>panels[key].evolucao.length)) { target.replaceChildren(el('p','Nenhum documento das séries selecionadas registrado neste período.','home-empty'));return; }
      const width=Math.max(250,target.clientWidth),height=260,left=52,right=width-16,top=16,bottom=223;
      const values=seriesKeys.flatMap(key=>series[key].map(g=>g.value)),low=Math.min(0,...values),high=Math.max(1,...values),span=high-low;
      const x=i=>left+(i+.5)*(right-left)/groups.length,y=v=>bottom-(v-low)/span*(bottom-top);
      const chart=svg('svg',{viewBox:`0 0 ${width} ${height}`,role:'group','aria-label':comparison+' por data de emissão'});
      for(let i=0;i<=4;i++) {
        const v=high-span*i/4;chart.append(svg('line',{x1:left,x2:right,y1:y(v),y2:y(v),class:'home-gridline'}),svg('text',{x:left-9,y:y(v)+3,'text-anchor':'end',class:'home-axis'},new Intl.NumberFormat('pt-BR',{notation:'compact',maximumFractionDigits:1}).format(v)));
      }
      window.PortalAnaliseComercial.trend(chart,seriesKeys.map(key=>({key,color:seriesColor(key),values:series[key].map(g=>g.value)})),x,y,'home');
      const tooltip=el('div',null,'home-chart-tooltip');tooltip.id='homeChartTooltip';tooltip.hidden=true;tooltip.setAttribute('role','tooltip');let active;
      const hide=()=>{tooltip.hidden=true;if(active){active.classList.remove('active');active.removeAttribute('aria-describedby');}active=null;};
      groups.forEach((g,i)=>{
        const key=seriesKeys.includes('notas')?'notas':seriesKeys[0],label=seriesLabels[key].toLocaleLowerCase('pt-BR'),hit=svg('g',{role:'button',tabindex:0,class:'home-chart-interval','aria-label':`${day(g.start)} a ${day(g.end)}. ${seriesKeys.map(k=>seriesLabels[k]+': '+money(series[k][i].value)).join('. ')}. Abrir ${label}.`});
        hit.append(svg('rect',{x:left+i*(right-left)/groups.length,y:top,width:(right-left)/groups.length,height:bottom-top,fill:'transparent'}),svg('line',{x1:x(i),x2:x(i),y1:top,y2:bottom,class:'home-crosshair'}));
        const show=()=>{
          hide();active=hit;hit.classList.add('active');hit.setAttribute('aria-describedby',tooltip.id);tooltip.replaceChildren(el('strong',day(g.start)+(g.start===g.end?'':' a '+day(g.end))));
          seriesKeys.forEach(seriesKey=>{const row=el('div');row.append(el('span',seriesLabels[seriesKey]),el('b',money(series[seriesKey][i].value)));tooltip.append(row);});
          tooltip.append(el('small','Clique para ver '+label));tooltip.hidden=false;
          const b=target.getBoundingClientRect(),r=hit.getBoundingClientRect(),center=r.x+r.width/2-b.x,tipWidth=tooltip.getBoundingClientRect().width;
          tooltip.style.left=Math.max(6,Math.min(b.width-tipWidth-6,center>b.width/2?center-tipWidth-12:center+12))+'px';tooltip.style.top='14px';
        };
        const activate=()=>{hide();go(key,{inicio:g.start,fim:g.end});};
        hit.addEventListener('pointerenter',show);hit.addEventListener('pointerleave',hide);hit.addEventListener('focus',show);hit.addEventListener('blur',hide);hit.addEventListener('click',activate);
        hit.addEventListener('keydown',event=>{if(event.key==='Enter'||event.key===' '){event.preventDefault();activate();}});chart.append(hit);
        const step=Math.max(1,Math.ceil(groups.length/5));
        if(i===0||i===groups.length-1||(i%step===0&&(groups.length-1-i)*(right-left)/groups.length>44))chart.append(svg('text',{x:x(i),y:bottom+22,'text-anchor':'middle',class:'home-axis'},day(g.start).slice(0,5)));
      });
      target.replaceChildren(chart,tooltip);target.onkeydown=event=>{if(event.key==='Escape')hide();};
    }
    window.addEventListener('resize',()=>{clearTimeout(timer);timer=setTimeout(renderChart,100);});
    return {loading,error,render};
  }
  window.PortalInicio={create};
})();
