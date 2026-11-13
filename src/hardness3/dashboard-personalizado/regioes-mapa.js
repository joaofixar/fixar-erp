(function () {
  'use strict';
  var cache = {}, ns = 'http://www.w3.org/2000/svg';
  function color(id) {
    var hash = 0;
    String(id).split('').forEach(function (c) { hash = (hash * 31 + c.charCodeAt(0)) >>> 0; });
    return 'hsl(' + (hash * 137.508 % 360) + ', 65%, 65%)';
  }
  function paint(host, uf, territories, regions, selected, choose, ready) {
    if (uf && !/^[A-Z]{2}$/.test(uf)) return;
    var keys = uf ? [uf] : window.BRAZIL_MAP.locations.map(function (item) { return item.id.toUpperCase(); });
    host.classList.add('is-loading'); host.setAttribute('aria-busy', 'true');
    var status = document.createElement('div'); status.className = 'commercial-map-loading';
    status.setAttribute('role', 'status');
    var spinner = document.createElement('span'); spinner.className = 'commercial-map-spinner'; spinner.setAttribute('aria-hidden', 'true'); status.appendChild(spinner);
    var label = document.createElement('strong'); label.textContent = 'Carregando mapa…'; status.appendChild(label);
    var detail = document.createElement('small'); detail.textContent = 'Preparando regiões cadastradas.'; status.appendChild(detail);
    status.setAttribute('data-region-map-status', 'true'); host.appendChild(status);
    var requests = keys.map(function (key) {
      if (!cache[key]) cache[key] = fetch('mapas-municipios/' + key + '.json?v=1').then(function (response) {
        if (!response.ok) throw new Error('Malha indisponível');
        return response.json();
      }).catch(function (error) { delete cache[key]; throw error; });
      return cache[key];
    });
    Promise.all(requests).then(function (maps) {
      // Permite pintar o carregamento antes de construir a malha, inclusive com cache.
      return new Promise(function (resolve) { window.requestAnimationFrame(function () { window.setTimeout(function () { resolve(maps); }, 0); }); });
    }).then(function (maps) {
      if (!host.isConnected) return;
      var byCity = {}, byRegion = {};
      territories.filter(function (item) { return !uf || item.uf === uf; }).forEach(function (item) {
        var id = String(item.cidadeIbge), existing = byCity[id];
        if (existing && (existing.conflict || String(existing.regiaoId) !== String(item.regiaoId))) byCity[id] = { conflict: true, regiao: 'Região divergente no cadastro municipal' };
        else byCity[id] = item;
      });
      regions.forEach(function (item) { byRegion[(item.uf || uf) + '|' + String(item.regiaoId)] = item; });
      var svg = document.createElementNS(ns, 'svg');
      var x0 = Math.min.apply(null, maps.map(function (map) { return map.origem[0]; }));
      var y0 = Math.min.apply(null, maps.map(function (map) { return map.origem[1]; }));
      var x1 = Math.max.apply(null, maps.map(function (map) { return map.origem[0] + (Number(map.viewBox.split(' ')[2]) - 20) / map.escala; }));
      var y1 = Math.max.apply(null, maps.map(function (map) { return map.origem[1] + (Number(map.viewBox.split(' ')[3]) - 20) / map.escala; }));
      var scale = 480 / Math.max(x1-x0, y1-y0);
      svg.setAttribute('viewBox', uf ? maps[0].viewBox : '0 0 ' + (20+(x1-x0)*scale) + ' ' + (20+(y1-y0)*scale));
      svg.setAttribute('role', 'group'); svg.setAttribute('aria-label', 'Regiões cadastradas em ' + (uf || 'Brasil'));
      maps.forEach(function (map, index) {
        var group = document.createElementNS(ns, 'g'), code = keys[index];
        if (!uf) {
          var factor = scale / map.escala;
          group.setAttribute('transform', 'translate(' + (10+(map.origem[0]-x0)*scale-10*factor) + ',' + (10+(map.origem[1]-y0)*scale-10*factor) + ') scale(' + factor + ')');
        }
        map.locations.forEach(function (item) {
        var territory = byCity[item.id], id = territory && String(territory.regiaoId), region = byRegion[code + '|' + id];
        var path = document.createElementNS(ns, 'path'); path.setAttribute('d', item.path);
        path.setAttribute('fill-rule', 'evenodd');
        path.setAttribute('fill', region && id !== '0' ? color(id) : '#365266');
        path.setAttribute('stroke', selected === id ? '#e3f7ff' : '#8faabf'); path.setAttribute('stroke-width', selected === id ? '1' : '.3');
        path.setAttribute('opacity', selected != null && selected !== id ? '.35' : '.85');
        var title = document.createElementNS(ns, 'title'); title.textContent = (territory ? territory.regiao : 'Sem região cadastrada') + ' • Município IBGE ' + item.id;
        if (region) {
          title.textContent += ' • ' + region.quantidade + ' clientes na região';
          path.setAttribute('role', 'button'); path.setAttribute('tabindex', '0'); path.setAttribute('data-region-polygon', id);
          path.setAttribute('aria-label', title.textContent); path.style.cursor = 'pointer';
          path.addEventListener('click', function () { choose(id, code); });
          path.addEventListener('keydown', function (event) { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); choose(id, code); } });
        }
        path.appendChild(title); group.appendChild(path);
        });
        svg.appendChild(group);
      });
      host.classList.remove('is-loading'); host.setAttribute('aria-busy', 'false');
      host.textContent = ''; host.appendChild(svg);
      var legend = document.createElement('div'); legend.className = 'commercial-region-breadcrumb';
      (uf ? regions : []).forEach(function (region) {
        var button = document.createElement('button'); button.type = 'button'; button.className = 'secondary';
        button.textContent = region.regiao; button.style.borderLeft = '5px solid ' + (String(region.regiaoId) === '0' ? '#cbd5e1' : color(region.regiaoId));
        button.addEventListener('click', function () { choose(String(region.regiaoId)); }); legend.appendChild(button);
      });
      host.appendChild(legend);
      var note = document.createElement('p'); note.textContent = 'Limites municipais: IBGE. Cores: regiões vinculadas às cidades no ERP. Cinza: sem região na carteira selecionada.'; host.appendChild(note);
      if (ready) ready();
    }).catch(function () {
      if (host.isConnected) {
        host.classList.remove('is-loading'); host.setAttribute('aria-busy', 'false');
        status.className = 'commercial-map-error';
        status.textContent = 'Malha municipal indisponível. As regiões e valores continuam disponíveis na listagem.';
      }
    });
  }
  window.DashboardRegioesMapa = { paint: paint };
}());
