(() => {
  'use strict';
  const $ = (id) => document.getElementById(id);
  const el = (tag, text, cls) => { const n = document.createElement(tag); if (text != null) n.textContent = String(text); if (cls) n.className = cls; return n; };
  const svgNode = (tag, attributes = {}, text) => { const n = document.createElementNS('http://www.w3.org/2000/svg', tag); Object.entries(attributes).forEach(([k,v]) => n.setAttribute(k,v)); if (text != null) n.textContent = text; return n; };
  const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase().replace(/[^A-Z0-9]/g, '');
  const colors = { Norte: '#36c9ae', Nordeste: '#f3b653', 'Centro-Oeste': '#a991ef', Sudeste: '#649bff', Sul: '#49c6e5' };
  const money = (v) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
  const number = (v) => new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 }).format(Number(v || 0));
  let geometryPromise;
  const detailCache = new Map();
  function municipalGeometry(uf) {
    if (!detailCache.has(uf)) detailCache.set(uf, fetch(`mapas/${uf}.json?v=1`, { credentials:'same-origin' }).then((r) => {
      if (!r.ok) throw new Error('Limites municipais indisponíveis'); return r.json();
    }).catch((error) => { detailCache.delete(uf); throw error; }));
    return detailCache.get(uf);
  }
  function geometry() {
    if (!geometryPromise) geometryPromise = fetch('geografia-brasil.json?v=1', { credentials: 'same-origin' }).then((r) => { if (!r.ok) throw new Error('Não foi possível carregar o mapa. Atualize para tentar novamente.'); return r.json(); }).catch((e) => { geometryPromise = null; throw e; });
    return geometryPromise;
  }
  function locate(client, geo) {
    const uf = String(client.uf || client.ufMunicipio || '').toUpperCase();
    if (client.uf && client.ufMunicipio && client.uf.toUpperCase() !== client.ufMunicipio.toUpperCase()) return { uf, reason: 'UF do cliente diverge da UF do município' };
    if (!geo.estados.some((s) => s.uf === uf)) return { uf: '', reason: 'UF não informada ou inválida' };
    const code = String(client.ibge || '').trim();
    // Código informado divergente não é substituído por um palpite pelo nome.
    const id = code || geo.nomes[uf + ':' + normalize(client.cidade)];
    const point = id && geo.municipios[id];
    if (!point || point[0] !== uf) return { uf, reason: code ? 'Código IBGE inválido ou incompatível com a UF' : 'Município não localizado na UF cadastrada' };
    return { uf, ibge: id, x: point[1], y: point[2], reason: '' };
  }
  window.PortalRegioes = { locate, create(request, updated, selectionChanged = () => {}) {
    let clients = [], selected = [], geo, states = {}, city = '', page = 1, svg, view, base, zoom = 1, loadId = 0, period = {}, mappedCities = new Set();
    const maxZoom = 128;
    const municipalZoom = 8;
    let viewRevision = 0, dialogPage = 1, dialogRows = [];
    let refreshTooltip = () => {};
    const metric = () => $('regionalMetric').value;
    const amount = (rows, key = metric()) => key === 'clientes' ? rows.length : rows.reduce((v,r) => v + Number(r[key] || 0), 0);
    const format = (v) => metric() === 'clientes' ? number(v) + ' clientes' : money(v);
    const regionName = () => $('regionalRegion').value;
    const ufName = () => $('regionalUf').value;
    function selectUf(uf) {
      if(!states[uf]) return;
      if(regionName() && states[uf].regiao!==regionName()) $('regionalRegion').value='';
      $('regionalUf').value=uf;city='';page=1;render();
      $('regionalFocus').focus({preventScroll:true});
    }
    function showStateClients() {
      const list=$('regionalRanking');list.scrollTop=0;list.tabIndex=-1;
      list.focus({preventScroll:true});list.scrollIntoView({block:'nearest'});
    }
    function selectCity(id) {
      const first = baseSelection().find((r) => !r.location.reason && r.location.ibge === id); if (!first) return;
      $('regionalUf').value = first.location.uf; city = id; page = 1; render();
      focusMap();
    }
    function baseSelection() {
      const coverage = $('regionalCoverage').value;
      return clients.filter((r) => (!regionName() || r.regiao === regionName()) && (!ufName() || r.location.uf === ufName())
        && (coverage !== 'com_venda' || r.notas > 0) && (coverage !== 'sem_venda' || r.notas === 0) && (coverage !== 'sem_localizacao' || r.location.reason));
    }
    function renderSummary() {
      const node = document.createDocumentFragment(), mapped = selected.filter((r) => !r.location.reason);
      const cards = [ ['Clientes na seleção',number(selected.length),`${number(selected.filter((r) => r.notas > 0).length)} com faturamento no período`],
        ['Faturamento autorizado',money(amount(selected,'faturamento')),'Notas emitidas no período · carteira atual'],
        ['Municípios no mapa',number(new Set(mapped.map((r) => r.location.ibge)).size),`${number(mapped.length)} clientes com localização válida`],
        ['Estados na seleção',number(new Set(selected.map((r) => r.location.uf).filter((uf) => states[uf])).size),'UF do endereço cadastrado'] ];
      cards.forEach(([label,value,caption]) => { const a = el('article',null,'portal-card'); a.append(el('p',label,'card-kicker'),el('strong',value),el('small',caption)); node.append(a); }); $('summary').replaceChildren(node);
    }
    function clientTable(list, onSelect) {
        const table = el('table'), head = el('thead'), tr = el('tr');
        ['Cliente','Município / UF','Endereço cadastrado','Faturamento','Pedidos','Situação'].forEach((s,index) => { const th = el('th',s); th.scope = 'col';if(index===3||index===4)th.className='numeric'; tr.append(th); }); head.append(tr); table.append(head);
        const body = el('tbody');
        list.forEach((r) => {
          const line = el('tr'), name = el('td',null,'regional-client-name'), link = el('button',r.cliente,'text-button'); link.type = 'button';
          link.disabled = !!r.location.reason; link.addEventListener('click',() => onSelect(r.location.ibge)); name.append(link,el('small',`Código ${r.id}${r.ativo !== 'S' ? ' · Inativo' : ''}`));
          const place = el('td',null,'wrap'); place.append(el('span',`${r.cidade || 'Município não informado'}${r.location.uf ? ' / '+r.location.uf : ''}`));
          if (r.location.reason) place.append(el('small',r.location.reason,'regional-location-error'));
          const address = el('td',null,'wrap regional-address'); address.append(el('span',[r.endereco,r.numero].filter(Boolean).join(', ') || 'Endereço não informado'),el('small',[r.bairro,r.cep ? 'CEP '+r.cep : ''].filter(Boolean).join(' · ')));
          const billed = el('td',money(r.faturamento),'numeric'), orders = el('td',money(r.valorPedidos),'numeric'); orders.append(el('small',number(r.pedidos)+' pedidos'));
          const status = el('td'); status.append(el('span',r.notas ? `${number(r.notas)} NF autorizadas` : 'Sem faturamento',`badge ${r.notas ? 'quitada' : 'pendente'}`));
          line.append(name,place,address,billed,orders,status); body.append(line);
        }); table.append(body); return table;
    }
    function renderClients() {
      const sorted = [...selected].sort((a,b) => amount([b])-amount([a]) || a.cliente.localeCompare(b.cliente,'pt-BR'));
      const title = city && clients.find((r) => r.location.ibge === city);
      $('regionalClientsTitle').textContent = title ? `${title.cidade} / ${title.location.uf}` : ufName() ? `Clientes em ${states[ufName()].nome}` : 'Carteira do representante';
      $('regionalClientsCaption').textContent = `${number(selected.length)} clientes · ${number(selected.filter((r) => !r.notas).length)} sem faturamento no período · Endereço principal do cadastro`;
      if (!selected.length) $('regionalClients').replaceChildren(el('p','Nenhum cliente nesta seleção. Altere o estado, a região ou a busca.','empty-state'));
      else {
        $('regionalClients').replaceChildren(clientTable(sorted.slice((page-1)*50,page*50),selectCity));
      }
      $('regionalPage').textContent = `Página ${page} de ${Math.max(1,Math.ceil(selected.length/50))}`;
      $('regionalPrevious').disabled = page <= 1; $('regionalNext').disabled = page*50 >= selected.length; $('regionalCsv').disabled = !selected.length;
    }
    function renderDetail(rows) {
      const first = city && clients.find((r) => r.location.ibge === city);
      $('regionalFocus').textContent = first ? `${first.cidade} / ${first.location.uf}` : ufName() ? states[ufName()].nome : regionName() || 'Brasil';
      $('regionalFocus').tabIndex = -1;
      $('regionalFocusCaption').textContent = first ? 'Clientes deste município na sua carteira' : 'Distribuição da carteira selecionada';
      const stats = document.createDocumentFragment();
      const cityCount=new Set(rows.filter(r=>!r.location.reason).map(r=>r.location.ibge)).size;
      const canExplore=!!ufName() && selected.length>0;
      [[number(selected.length),'clientes'],[money(amount(selected,'faturamento')),'faturamento'],[money(amount(selected,'valorPedidos')),'em pedidos']].forEach(([v,l],index) => {
        const box = el('div');
        if(index===0 && canExplore) {
          const button=el('button',null,'regional-client-count');button.type='button';button.setAttribute('aria-label',city ? `Ver ${v} clientes em ${first.cidade}.` : `Ver ${v} clientes em ${states[ufName()].nome}.`);
          if(city)button.setAttribute('aria-haspopup','dialog');
          button.append(el('strong',v),el('small',city ? 'clientes · ver lista →' : 'clientes · ver lista ↓'));button.addEventListener('click',()=>city ? openCityClients() : showStateClients());box.append(button);
        } else box.append(el('strong',v),el('small',l));
        stats.append(box);
      }); $('regionalFocusStats').replaceChildren(stats);
      $('regionalClearCity').hidden = !city;
      $('regionalRankingTitle').textContent=ufName() ? 'Clientes · '+states[ufName()].nome : 'Estados em destaque';
      $('regionalRankingCaption').hidden=!ufName();
      $('regionalRankingCaption').textContent=ufName() ? `${number(rows.length)} clientes · ${number(cityCount)} ${cityCount===1?'município localizado':'municípios localizados'}. Selecione um cliente para ver seu município no mapa.` : '';
      if(ufName()) {
        const list=document.createDocumentFragment();
        [...rows].sort((a,b)=>a.cliente.localeCompare(b.cliente,'pt-BR') || String(a.id).localeCompare(String(b.id))).forEach((r,index)=>{
          const button=el('button',null,'regional-client-rank');button.type='button';button.dataset.clientId=r.id;
          button.dataset.clientCity=r.location.reason?'':r.location.ibge;
          button.disabled=!!r.location.reason;
          button.classList.toggle('is-current-city',!r.location.reason && city===r.location.ibge);
          const name=el('span');name.append(el('b',r.cliente),el('small',r.location.reason || `${r.cidade} / ${r.location.uf}`));
          button.append(el('span',String(index+1).padStart(2,'0'),'regional-client-index'),name,el('strong',format(amount([r]))));
          button.addEventListener('click',()=>selectCity(r.location.ibge));list.append(button);
        });
        $('regionalRanking').replaceChildren(rows.length ? list : el('p','Nenhum cliente nesta seleção.','regional-muted'));
        return;
      }
      const groups = {};
      rows.forEach((r) => { if (states[r.location.uf]) (groups[r.location.uf] || (groups[r.location.uf] = [])).push(r); });
      const ranked = Object.entries(groups).sort((a,b) => amount(b[1])-amount(a[1]) || a[0].localeCompare(b[0])).slice(0,8), max = Math.max(1,...ranked.map(([,rs]) => amount(rs)));
      const ranking = document.createDocumentFragment();
      ranked.forEach(([uf,rs]) => {
        const button = el('button',null,'regional-rank'); button.type = 'button'; button.dataset.ufRank = uf;
        const name = el('span'); name.append(el('b',states[uf].nome),el('small',number(rs.length)+' clientes'));
        const bar = el('span',null,'regional-rank-track'), fill = el('i'); fill.style.width = Math.max(3,amount(rs)/max*100)+'%'; fill.style.backgroundColor = colors[states[uf].regiao]; bar.append(fill);
        button.append(el('span',uf,'regional-uf-tag'),name,el('strong',format(amount(rs))),bar); button.addEventListener('click',() => selectUf(uf)); ranking.append(button);
      });
      $('regionalRanking').replaceChildren(ranked.length ? ranking : el('p','Nenhuma UF com clientes nesta seleção.','regional-muted'));
    }
    function renderDialogClients() {
      const first=dialogRows[0];
      $('regionalDialogTitle').textContent=`Clientes em ${first.cidade} / ${first.location.uf}`;
      $('regionalDialogCaption').textContent=`${number(dialogRows.length)} ${dialogRows.length===1?'cliente':'clientes'} · ${money(amount(dialogRows,'faturamento'))} em faturamento · ${money(amount(dialogRows,'valorPedidos'))} em pedidos`;
      $('regionalDialogContent').replaceChildren(clientTable(dialogRows.slice((dialogPage-1)*50,dialogPage*50),()=>showMunicipality()));
      $('regionalDialogPage').textContent=`Página ${dialogPage} de ${Math.max(1,Math.ceil(dialogRows.length/50))}`;
      $('regionalDialogPrevious').disabled=dialogPage<=1;
      $('regionalDialogNext').disabled=dialogPage*50>=dialogRows.length;
    }
    function openCityClients() {
      if(!city || !selected.length)return;
      hideTooltip();fitCity(city);
      dialogRows=[...selected].sort((a,b)=>amount([b])-amount([a]) || a.cliente.localeCompare(b.cliente,'pt-BR'));dialogPage=1;
      renderDialogClients();
      if(!$('regionalClientsDialog').open)$('regionalClientsDialog').showModal();
    }
    function showMunicipality() {
      $('regionalClientsDialog').close();
      focusMap();
    }
    function focusMap() {
      requestAnimationFrame(()=>{
        if(!city || $('regionalView').hidden)return;
        const card=$('regionalMapCard'),bar=$('selectionBar');
        const offset=bar.hidden?16:bar.offsetHeight+(parseFloat(getComputedStyle(bar).top)||0)+14;
        card.focus({preventScroll:true});
        if(!document.fullscreenElement)window.scrollTo({top:Math.max(0,scrollY+card.getBoundingClientRect().top-offset),behavior:'instant'});
      });
    }
    $('regionalCloseClients').addEventListener('click',()=>$('regionalClientsDialog').close());
    $('regionalClientsDialog').addEventListener('close',()=>{dialogRows=[];$('regionalDialogContent').replaceChildren();const button=$('regionalFocusStats').querySelector('.regional-client-count');if(button)button.focus({preventScroll:true});});
    $('regionalLocateCity').addEventListener('click',showMunicipality);
    $('regionalDialogPrevious').addEventListener('click',()=>{if(dialogPage>1){dialogPage--;renderDialogClients();}});
    $('regionalDialogNext').addEventListener('click',()=>{if(dialogPage*50<dialogRows.length){dialogPage++;renderDialogClients();}});
    function renderMap(rows) {
      base = [...geo.viewBox]; view = [...base]; zoom = 1;
      svg = svgNode('svg', { viewBox:base.join(' '), role:'group', 'aria-label':'Mapa do Brasil com clientes do representante por município' });
      const background = svgNode('g', {'aria-hidden':'true',class:'regional-graticule'});
      for (let x = 70; x < base[2]; x+=110) background.append(svgNode('line',{x1:x,y1:20,x2:x,y2:base[3]-20}));
      for (let y = 70; y < base[3]; y+=110) background.append(svgNode('line',{x1:20,y1:y,x2:base[2]-20,y2:y}));
      svg.append(background);
      const totals = {}; rows.forEach((r) => { if (states[r.location.uf]) (totals[r.location.uf] || (totals[r.location.uf]=[])).push(r); });
      const maxState = Math.max(1,...Object.values(totals).map((rs) => amount(rs)));
      const polygons = svgNode('g'), labels = svgNode('g',{'aria-hidden':'true'});
      geo.estados.forEach((s) => {
        const rs = totals[s.uf] || [], path = svgNode('path',{d:s.path,'fill-rule':'evenodd',class:'regional-state'+(rs.length?' has-clients':'')+(ufName()===s.uf?' selected':''),fill:rs.length?colors[s.regiao]:'#25445e',tabindex:'0',role:'button','data-uf':s.uf,'aria-label':`${s.nome}: ${number(rs.length)} clientes. Selecionar estado.`});
        path.style.setProperty('--state-opacity',rs.length ? .38+.5*Math.sqrt(Math.max(0,amount(rs))/maxState) : .7);
        polygons.append(path);
        const offset = {DF:[15,-8],ES:[12,0],RJ:[10,7],SE:[10,3],AL:[14,0],PB:[13,0],RN:[10,-5]}[s.uf] || [0,0];
        if (offset[0]) labels.append(svgNode('line',{x1:s.label[0],y1:s.label[1],x2:s.label[0]+offset[0],y2:s.label[1]+offset[1],class:'regional-label-line'}));
        labels.append(svgNode('text',{x:s.label[0]+offset[0],y:s.label[1]+offset[1],class:'regional-state-label'},s.uf));
      }); svg.append(polygons,svgNode('g',{class:'regional-municipalities','aria-hidden':'true'}),labels);
      const cities = {};
      rows.filter((r) => !r.location.reason).forEach((r) => { (cities[r.location.ibge] || (cities[r.location.ibge]=[])).push(r); });
      const max = Math.max(1,...Object.values(cities).map((rs) => amount(rs))), markers = svgNode('g',{class:'regional-markers'});
      Object.entries(cities).sort((a,b) => amount(b[1])-amount(a[1])).forEach(([id,rs]) => {
        const r = rs[0], p = r.location, radius = 5 + Math.sqrt(Math.max(0,amount(rs))/max)*8;
        const marker = svgNode('g',{transform:`translate(${p.x},${p.y})`,class:'regional-marker'+(city===id?' selected':''),tabindex:'0',role:'button','data-city':id,'data-x':p.x,'data-y':p.y,'aria-label':`${r.cidade} / ${p.uf}: ${number(rs.length)} clientes, ${format(amount(rs))}. Ver município.`});
        marker.append(svgNode('circle',{r:radius+5,class:'regional-marker-halo'}),svgNode('circle',{r:radius,fill:colors[r.regiao],class:'regional-marker-dot'}),svgNode('circle',{r:2.2,class:'regional-marker-center'}));
        marker.append(svgNode('text',{x:radius+9,y:4,class:'regional-city-label'},r.cidade));
        markers.append(marker);
      }); svg.append(markers);
      mappedCities = new Set(Object.keys(cities));
      const compass = svgNode('g',{class:'regional-compass','aria-hidden':'true'}); compass.append(svgNode('text',{x:48,y:35},'N'),svgNode('path',{d:'M48 42L43 57L48 53L53 57Z'})); svg.append(compass);
      const ocean = svgNode('text',{x:base[2]-130,y:base[3]-160,class:'regional-ocean','aria-hidden':'true'},'OCEANO ATLÂNTICO'); svg.append(ocean);
      $('regionalMap').replaceChildren(svg); $('regionalMap').setAttribute('aria-busy','false');
      $('regionalMapMetric').textContent=$('regionalMetric').selectedOptions[0].textContent;
      $('regionalMapScope').textContent=city ? rows.find((r)=>r.location.ibge===city).cidade+' / '+ufName() : ufName() ? states[ufName()].nome : regionName() || 'Visão nacional';
      $('regionalMapDensity').textContent=`${number(Object.keys(cities).length)} municípios · ${number(rows.filter((r)=>!r.location.reason).length)} clientes no mapa`;
      $('regionalMapNote').textContent='Limites: IBGE · Posições aproximadas por município. Consulte o endereço completo na lista.';
      hideTooltip();
      let tooltipTarget;
      function tooltipFor(target) {
        const node=target.closest('[data-city],[data-uf]');
        if (!node || (zoom>=municipalZoom && node.dataset.uf)) { hideTooltip();return; }
        tooltipTarget=node;
        const rs=node.dataset.city ? cities[node.dataset.city] : totals[node.dataset.uf] || [];
        const title=node.dataset.city ? rs[0].cidade+' / '+rs[0].location.uf : states[node.dataset.uf].nome;
        const region=node.dataset.city ? rs[0].regiao : states[node.dataset.uf].regiao;
        const tip=$('regionalMapTooltip');tip.replaceChildren(el('small',region,'map-tip-region'),el('strong',title));
        [[number(rs.length),'Clientes'],[money(amount(rs,'faturamento')),'Faturamento'],[money(amount(rs,'valorPedidos')),'Pedidos']].forEach(([value,label])=>{const row=el('div');row.append(el('span',label),el('b',value));tip.append(row);});
        tip.append(el('small',node.dataset.city?'Clique para aproximar o município':'Clique para explorar o estado','map-tip-hint'));
        tip.style.setProperty('--tip-color',colors[region] || '#649bff');tip.hidden=false;
        const stage=$('regionalMap').getBoundingClientRect(),rect=node.getBoundingClientRect();
        tip.style.left=Math.min(stage.width-tip.offsetWidth-12,Math.max(12,rect.x-stage.x+rect.width/2+18))+'px';
        tip.style.top=Math.max(12,Math.min(stage.height-tip.offsetHeight-64,Math.max(12,rect.y-stage.y-24)))+'px';
      }
      refreshTooltip=()=>{if(tooltipTarget && tooltipTarget.isConnected && !$('regionalMapTooltip').hidden)tooltipFor(tooltipTarget);};
      svg.addEventListener('pointerover',(event)=>{if(event.pointerType!=='touch')tooltipFor(event.target);});
      svg.addEventListener('pointerout',(event)=>{if(!event.relatedTarget || !event.relatedTarget.closest || event.target.closest('[data-city],[data-uf]')!==event.relatedTarget.closest('[data-city],[data-uf]'))hideTooltip();});
      svg.addEventListener('focusin',(event)=>tooltipFor(event.target));svg.addEventListener('focusout',hideTooltip);
      function choose(target) { const node = target.closest('[data-city],[data-uf]'); if (!node) return; if (node.dataset.city) selectCity(node.dataset.city); else selectUf(node.dataset.uf); }
      let drag;
      svg.addEventListener('pointerdown',(event) => { if (event.button !== 0) return; hideTooltip();drag = {id:event.pointerId,x:event.clientX,y:event.clientY,view:[...view],moved:false,target:event.target}; });
      svg.addEventListener('pointermove',(event) => {
        if (!drag) { if(event.pointerType!=='touch' && $('regionalMapTooltip').hidden)tooltipFor(event.target);return; }
        if (event.pointerId !== drag.id) return;
        const dx=event.clientX-drag.x,dy=event.clientY-drag.y;
        if (!drag.moved && Math.hypot(dx,dy)<5) return;
        if (!drag.moved) { drag.moved=true;viewRevision++; svg.setPointerCapture(event.pointerId); }
        const unit = Math.max(view[2]/svg.clientWidth,view[3]/svg.clientHeight); view[0]=drag.view[0]-dx*unit; view[1]=drag.view[1]-dy*unit; applyView();
      });
      svg.addEventListener('pointerup',(event) => { if (!drag || event.pointerId!==drag.id) return; const saved=drag; drag=null; if (svg.hasPointerCapture(event.pointerId)) svg.releasePointerCapture(event.pointerId); if (!saved.moved) choose(saved.target); });
      svg.addEventListener('pointercancel',() => { drag=null; });
      // Ativa também cliques programáticos e por tecnologia assistiva sem duplicar o pointerup.
      svg.addEventListener('click',(event) => { if (event.detail===0) choose(event.target); });
      svg.addEventListener('keydown',(event) => { if (event.key==='Enter'||event.key===' ') { event.preventDefault(); choose(event.target); } });
      svg.addEventListener('wheel',(event) => { if (event.ctrlKey) { event.preventDefault(); const p=svg.createSVGPoint();p.x=event.clientX;p.y=event.clientY;changeZoom(event.deltaY<0?1.25:.8,p.matrixTransform(svg.getScreenCTM().inverse())); } },{passive:false});
      applyView();
    }
    function hideTooltip() { $('regionalMapTooltip').hidden=true; }
    function renderMunicipalities() {
      const layer=svg.querySelector('.regional-municipalities'),targetSvg=svg;
      const detailed=zoom>=2.2;
      layer.style.display=detailed?'':'none'; if(!detailed)return;
      const visible=geo.estados.filter((s)=>s.bounds[2]>view[0] && s.bounds[0]<view[0]+view[2] && s.bounds[3]>view[1] && s.bounds[1]<view[1]+view[3]);
      visible.sort((a,b)=>(a.uf===ufName()?-1:0)-(b.uf===ufName()?-1:0));
      visible.slice(0,8).forEach((s)=>{
        if(layer.querySelector(`[data-detail-uf="${s.uf}"]`))return;
        const group=svgNode('g',{'data-detail-uf':s.uf});layer.append(group);
        municipalGeometry(s.uf).then((data)=>{
          if(svg!==targetSvg)return;
          const fragment=document.createDocumentFragment();
          // O contorno selecionado fica acima dos limites vizinhos.
          data.municipios.slice().sort((a,b)=>(a.id===city?1:0)-(b.id===city?1:0)).forEach((m)=>fragment.append(svgNode('path',{d:m.path,'fill-rule':'evenodd',class:'regional-municipality'+(mappedCities.has(m.id)?' has-clients':'')+(city===m.id?' selected':''),'data-municipality':m.id})));
          group.replaceChildren(fragment);
        }).catch(()=>{if(svg===targetSvg)$('regionalMapNote').textContent='Detalhamento municipal indisponível. Atualize para tentar novamente. As marcações por município continuam disponíveis.';});
      });
    }
    function applyView() {
      if (!svg || !svg.clientWidth || !svg.clientHeight) return;
      zoom = base[2]/view[2];
      const municipalView=zoom>=municipalZoom;
      $('regionalMap').parentElement.classList.toggle('municipal-view',municipalView);
      svg.querySelectorAll('.regional-state').forEach((state)=>{
        state.setAttribute('tabindex',municipalView?'-1':'0');
        if(municipalView)state.setAttribute('aria-hidden','true');else state.removeAttribute('aria-hidden');
      });
      const scale=$('regionalLegend').querySelector('.regional-legend-scale');
      if(scale)scale.textContent=municipalView ? (city?'Contorno verde = município selecionado · ':'Limites municipais · ')+'Tamanho do ponto = medida selecionada' : 'Intensidade e tamanho = medida selecionada';
      view[0]=Math.max(base[0]-view[2]*.15,Math.min(base[2]-view[2]*.85,view[0]));
      view[1]=Math.max(base[1]-view[3]*.15,Math.min(base[3]-view[3]*.85,view[1]));
      svg.setAttribute('viewBox',view.join(' '));
      const unit=Math.max(view[2]/svg.clientWidth,view[3]/svg.clientHeight);
      svg.querySelectorAll('[data-city]').forEach((m) => m.setAttribute('transform',`translate(${m.dataset.x},${m.dataset.y}) scale(${unit})`));
      svg.querySelectorAll('.regional-state-label').forEach((t) => {t.style.fontSize=(11*unit)+'px';t.style.strokeWidth=(3*unit)+'px';});
      svg.classList.toggle('detail-visible',zoom>=2.2);
      const occupied=[];
      const markerNodes=[...svg.querySelectorAll('[data-city]')].sort((a,b)=>(b.dataset.city===city?1:0)-(a.dataset.city===city?1:0));
      markerNodes.forEach((marker)=>{
        const label=marker.querySelector('.regional-city-label');label.style.display=zoom>=2.2 || marker.dataset.city===city?'':'none';
        if(label.style.display==='none')return;
        const r=label.getBoundingClientRect();
        if(occupied.some((b)=>r.left<b.right+8 && r.right>b.left-8 && r.top<b.bottom+4 && r.bottom>b.top-4))label.style.display='none';
        else occupied.push(r);
      });
      $('regionalZoomIn').disabled=zoom>=maxZoom; $('regionalZoomOut').disabled=zoom<=1;
      $('regionalZoomLevel').textContent=new Intl.NumberFormat('pt-BR',{maximumFractionDigits:1}).format(zoom)+'×';
      refreshTooltip();renderMunicipalities();
    }
    function changeZoom(factor,anchor) {
      hideTooltip();viewRevision++;
      if (!svg) return; const target=Math.max(1,Math.min(maxZoom,zoom*factor)),w=base[2]/target,h=base[3]/target;
      const ax=anchor?anchor.x:view[0]+view[2]/2,ay=anchor?anchor.y:view[1]+view[3]/2;
      view=[ax-(ax-view[0])*w/view[2],ay-(ay-view[1])*h/view[3],w,h]; applyView();
    }
    function fitState(uf) {
      if (!svg || !uf || !states[uf]) return;
      const [x0,y0,x1,y1]=states[uf].bounds, target=Math.max(1,Math.min(maxZoom,Math.min(base[2]/(x1-x0+50),base[3]/(y1-y0+50))));
      view=[(x0+x1)/2-base[2]/target/2,(y0+y1)/2-base[3]/target/2,base[2]/target,base[3]/target]; applyView();
    }
    async function fitCity(id) {
      if(!svg || !geo.municipios[id])return;
      const [uf,x,y]=geo.municipios[id],target=24,current=++viewRevision,targetSvg=svg;
      view=[x-base[2]/target/2,y-base[3]/target/2,base[2]/target,base[3]/target];applyView();
      try {
        const data=await municipalGeometry(uf);
        if(current!==viewRevision || svg!==targetSvg || city!==id)return;
        const municipality=data.municipios.find(m=>m.id===id);if(!municipality)return;
        const outline=svgNode('path',{d:municipality.path,visibility:'hidden'});svg.append(outline);
        const bounds=outline.getBBox();outline.remove();
        if(!bounds.width || !bounds.height)return;
        const target=Math.max(1,Math.min(maxZoom,base[2]/(bounds.width*1.6),base[3]/(bounds.height*1.6)));
        view=[bounds.x+bounds.width/2-base[2]/target/2,bounds.y+bounds.height/2-base[3]/target/2,base[2]/target,base[3]/target];applyView();
      }catch(error){/* A marcação continua disponível se o contorno local não carregar. */}
    }
    function render(preserveView=false) {
      if (!geo) return;
      viewRevision++;
      const previousView=preserveView && view ? [...view] : null;
      const rows=baseSelection(); if (city && !rows.some((r) => r.location.ibge===city)) city='';
      selected=rows.filter((r) => !city || r.location.ibge===city);
      const missing=rows.filter((r) => r.location.reason).length;
      $('regionalQuality').replaceChildren();
      if (missing) { const b=el('button',`${number(missing)} cliente(s) sem localização válida. Ver cadastros.`, 'text-button'); b.type='button'; b.addEventListener('click',() => { $('regionalCoverage').value='sem_localizacao';city='';page=1;render();$('regionalClientsTitle').scrollIntoView({behavior:'smooth'}); }); $('regionalQuality').append(b); }
      else $('regionalQuality').textContent=rows.length?'Todos os clientes desta seleção possuem município localizado.':'Nenhum cliente nesta seleção.';
      renderSummary(); renderMap(rows); renderDetail(rows); renderClients();
      if(previousView){view=previousView;applyView();}else if(city)fitCity(city);else if (ufName()) fitState(ufName());
      const first=city && clients.find((r)=>r.location.ibge===city),labels=[];
      if(first)labels.push(first.cidade+' / '+first.location.uf);else if(ufName())labels.push(states[ufName()].nome);else if(regionName())labels.push(regionName());
      if($('regionalCoverage').value!=='todos')labels.push($('regionalCoverage').selectedOptions[0].textContent);
      selectionChanged(labels.join(' · '));
    }
    $('regionalRegion').addEventListener('change',() => { $('regionalUf').value='';city='';page=1;render(); });
    $('regionalUf').addEventListener('change',() => { if (ufName() && regionName() && states[ufName()].regiao!==regionName()) $('regionalRegion').value=''; city='';page=1;render(); });
    $('regionalMetric').addEventListener('change',()=>{page=1;render(true);});
    $('regionalCoverage').addEventListener('change',()=>{city='';page=1;render();});
    $('regionalClearCity').addEventListener('click',() => {city='';page=1;render();});
    $('regionalReset').addEventListener('click',() => { $('regionalRegion').value='';$('regionalUf').value='';city='';page=1;render(); });
    $('regionalZoomIn').addEventListener('click',() => changeZoom(1.5)); $('regionalZoomOut').addEventListener('click',() => changeZoom(1/1.5));
    $('regionalExpand').hidden=!document.fullscreenEnabled;
    $('regionalExpand').addEventListener('click',async()=>{
      try { if(document.fullscreenElement)await document.exitFullscreen();else await $('regionalMapCard').requestFullscreen(); }
      catch(error){$('regionalMapNote').textContent='O navegador não permitiu expandir. Você pode continuar usando os controles de zoom.';}
    });
    document.addEventListener('fullscreenchange',()=>{const expanded=document.fullscreenElement===$('regionalMapCard');$('regionalExpand').setAttribute('aria-label',expanded?'Sair da tela cheia':'Expandir mapa em tela cheia');$('regionalExpand').title=expanded?'Sair da tela cheia':'Expandir mapa';applyView();});
    if(window.ResizeObserver)new ResizeObserver(()=>applyView()).observe($('regionalMap'));
    $('regionalPrevious').addEventListener('click',() => {if(page>1){page--;renderClients();}}); $('regionalNext').addEventListener('click',() => {if(page*50<selected.length){page++;renderClients();}});
    $('regionalCsv').addEventListener('click',() => {
      const quote=(v) => { let s=String(v==null?'':v); if(/^\s*[=+\-@]|^[\t\r\n]/.test(s))s="'"+s; return '"'+s.replace(/"/g,'""')+'"'; };
      const rows=[['Código','Cliente','UF','Região','Município','IBGE','Endereço','Número','Bairro','CEP','Faturamento','Notas autorizadas','Valor em pedidos','Pedidos','Localização'],...selected.map((r) => [r.id,r.cliente,r.location.uf,r.regiao,r.cidade,r.ibge,r.endereco,r.numero,r.bairro,r.cep,r.faturamento.toFixed(2).replace('.',','),r.notas,r.valorPedidos.toFixed(2).replace('.',','),r.pedidos,r.location.reason||'Localizado por município'])];
      const url=URL.createObjectURL(new Blob(['\uFEFF',rows.map((r) => r.map(quote).join(';')).join('\r\n')],{type:'text/csv;charset=utf-8'})), a=el('a');
      a.href=url;a.download=`${document.body.dataset.demonstracao==='1'?'demonstracao-':''}representante-regioes-${period.inicio}.csv`;document.body.append(a);a.click();a.remove();setTimeout(() => URL.revokeObjectURL(url),1000);
    });
    return {
      clear(redraw=true) { $('regionalRegion').value='';$('regionalUf').value='';$('regionalCoverage').value='todos';city='';page=1;selectionChanged('');if(redraw)render(); },
      async load(filters,options) {
        viewRevision++;if($('regionalClientsDialog').open)$('regionalClientsDialog').close();
        const current=++loadId; period={...filters}; clients=[];selected=[];geo=null;svg=null;hideTooltip();
        $('regionalMapScope').textContent='Preparando mapa…';$('regionalMapDensity').textContent='';
        $('regionalView').inert=true; $('regionalCsv').disabled=true; $('regionalMap').setAttribute('aria-busy','true'); $('regionalMap').replaceChildren(el('p','Preparando o mapa e a carteira…','analytics-empty')); $('regionalClients').replaceChildren(); $('regionalRanking').replaceChildren();$('regionalFocusStats').replaceChildren();$('regionalQuality').textContent='';
        const params={inicio:filters.inicio,fim:filters.fim,cliente:filters.cliente};if(filters.clienteId)params.clienteId=filters.clienteId;if(filters.grupoClienteId)params.grupoClienteId=filters.grupoClienteId;
        const [data,map]=await Promise.all([request('regioes',params,options),geometry()]);
        if(current!==loadId || options.signal.aborted) return;
        geo=map;states=Object.fromEntries(map.estados.map((s) => [s.uf,s]));
        const legend=document.createDocumentFragment();
        Object.entries(colors).forEach(([name,color]) => { const item=el('span'),dot=el('i');dot.style.backgroundColor=color;item.append(dot,document.createTextNode(name));legend.append(item); });
        legend.append(el('span','Intensidade e tamanho = medida selecionada','regional-legend-scale'));$('regionalLegend').replaceChildren(legend);
        const previousUf=ufName();
        $('regionalUf').replaceChildren(new Option('Todas as UFs',''),...map.estados.slice().sort((a,b) => a.nome.localeCompare(b.nome,'pt-BR')).map((s) => new Option(`${s.uf} · ${s.nome}`,s.uf)));
        if(states[previousUf]) $('regionalUf').value=previousUf;
        clients=data.clientes.map((r) => { const location=locate(r,map); return {...r,location,regiao:states[location.uf]?states[location.uf].regiao:''}; });
        city='';page=1;render();$('regionalView').inert=false;updated(data.atualizado);
      },
      error(message) { svg=null;hideTooltip();$('regionalMapScope').textContent='Mapa indisponível';$('regionalMapDensity').textContent='';$('regionalView').inert=false;$('regionalMap').setAttribute('aria-busy','false');$('regionalMap').replaceChildren(el('p',message,'analytics-empty'));$('regionalClients').replaceChildren(el('p','Atualize para tentar novamente.','empty-state'));$('regionalCsv').disabled=true; }
    };
  } };
})();
