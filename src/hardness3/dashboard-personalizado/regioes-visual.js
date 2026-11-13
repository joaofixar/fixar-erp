(function () {
  'use strict';
  var ns = 'http://www.w3.org/2000/svg';
  var colors = { Norte: '#36c9ae', Nordeste: '#f3b653', 'Centro-Oeste': '#a991ef', Sudeste: '#649bff', Sul: '#49c6e5' };
  function color(region) { return colors[region] || '#649bff'; }
  function node(tag, css, text) {
    var e = document.createElement(tag); e.className = css;
    if (text !== undefined) e.textContent = text;
    return e;
  }
  function vector(tag, attributes, text) {
    var e = document.createElementNS(ns, tag);
    Object.keys(attributes).forEach(function (key) { e.setAttribute(key, attributes[key]); });
    if (text !== undefined) e.textContent = text;
    return e;
  }
  function details(svg, map, data, selectedUf, choose) {
    var bounds = map.viewBox.split(' ').map(Number);
    svg.setAttribute('data-map-width', bounds[2]);
    var grid = vector('g', { 'class': 'commercial-territory-grid', 'aria-hidden': 'true' });
    for (var x = 70; x < bounds[2]; x += 110) grid.appendChild(vector('line', { x1: x, y1: 20, x2: x, y2: bounds[3] - 20 }));
    for (var y = 70; y < bounds[3]; y += 110) grid.appendChild(vector('line', { x1: 20, y1: y, x2: bounds[2] - 20, y2: y }));
    svg.prepend(grid);
    var labels = vector('g', { 'class': 'commercial-territory-labels', 'aria-hidden': 'true' });
    map.locations.forEach(function (location) {
      if (!location.label) return;
      var code = location.id.toUpperCase();
      var offset = { DF: [15, -8], ES: [12, 0], RJ: [10, 7], SE: [10, 3], AL: [14, 0], PB: [13, 0], RN: [10, -5] }[code] || [0, 0];
      labels.appendChild(vector('text', { x: location.label[0] + offset[0], y: location.label[1] + offset[1] }, code));
    });
    svg.appendChild(labels);
    var markers = vector('g', { 'class': 'commercial-territory-markers' });
    data.ufs.forEach(function (uf) {
      if (selectedUf && selectedUf !== uf.uf) return;
      var location = map.locations.filter(function (item) { return item.id.toUpperCase() === uf.uf; })[0];
      (uf.cidades || []).forEach(function (city) {
        var point = (map.municipios || {})[String(city.cidadeIbge)];
        if (!point || point[0] !== uf.uf) return;
        var label = city.cidade + ' / ' + uf.uf + ': ' + city.quantidade.toLocaleString('pt-BR') + ' clientes. Ver estado.';
        var marker = vector('g', { 'class': 'commercial-territory-marker', transform: 'translate(' + point[1] + ',' + point[2] + ')', role: 'button', tabindex: '0', 'aria-label': label, 'data-region-map-city': city.cidadeIbge });
        marker.appendChild(vector('title', {}, label));
        marker.appendChild(vector('circle', { r: 12, 'class': 'commercial-territory-halo' }));
        marker.appendChild(vector('circle', { r: 7, fill: color(location && location.regiao), 'class': 'commercial-territory-dot' }));
        marker.appendChild(vector('circle', { r: 2.6, fill: '#fff' }));
        marker.addEventListener('click', function () { choose(uf.uf); });
        marker.addEventListener('keydown', function (event) {
          if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); choose(uf.uf); }
        });
        markers.appendChild(marker);
      });
    });
    svg.appendChild(markers);
    svg.appendChild(vector('text', { x: 48, y: 40, 'class': 'commercial-territory-ocean', 'aria-hidden': 'true' }, 'N ↑'));
    svg.appendChild(vector('text', { x: bounds[2] - 150, y: bounds[3] - 140, 'class': 'commercial-territory-ocean', 'aria-hidden': 'true' }, 'OCEANO ATLÂNTICO'));
  }
  function mount(stage, host, options) {
    var hud = node('div', 'commercial-territory-hud');
    hud.appendChild(node('span', '', options.mode));
    hud.appendChild(node('strong', '', options.title));
    hud.appendChild(node('small', '', options.count.toLocaleString('pt-BR') + ' clientes na seleção'));
    stage.appendChild(hud);
    var controls = node('div', 'commercial-territory-controls');
    controls.setAttribute('role', 'group'); controls.setAttribute('aria-label', 'Navegação do mapa');
    function button(text, label, action) {
      var b = node('button', '', text); b.type = 'button'; b.title = label; b.setAttribute('aria-label', label);
      b.addEventListener('click', action); controls.appendChild(b); return b;
    }
    var svg, base, view, dragging = false;
    var plus = button('+', 'Ampliar mapa', function () { zoom(1.4); });
    var minus = button('−', 'Reduzir mapa', function () { zoom(1 / 1.4); });
    var level = node('output', '', '1×'); level.setAttribute('aria-label', 'Ampliação'); controls.appendChild(level);
    button('Ver Brasil', 'Ver Brasil', options.reset);
    var expand = button('⛶', 'Expandir mapa em tela cheia', function () {
      var action = document.fullscreenElement === stage ? document.exitFullscreen() : stage.requestFullscreen();
      if (action && action.catch) action.catch(function () {});
    });
    expand.textContent = '';
    var icon = vector('svg', { viewBox: '0 0 24 24', width: 18, height: 18, 'aria-hidden': 'true' });
    icon.appendChild(vector('path', { d: 'M8 3H3v5m13-5h5v5M3 16v5h5m13-5v5h-5', fill: 'none', stroke: 'currentColor', 'stroke-width': 1.7, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }));
    expand.appendChild(icon);
    expand.hidden = !stage.requestFullscreen;
    stage.appendChild(controls);
    stage.appendChild(node('span', 'commercial-territory-gesture', 'Arraste para explorar · Ctrl + rolagem para aproximar'));
    function update() {
      svg.setAttribute('viewBox', view.join(' '));
      svg.style.setProperty('--commercial-map-scale', view[2] / (Number(svg.getAttribute('data-map-width')) || base[2]));
      var value = base[2] / view[2];
      level.textContent = Number(value.toFixed(1)).toLocaleString('pt-BR') + '×';
      plus.disabled = value >= 32; minus.disabled = value <= 1;
    }
    function zoom(factor) {
      if (!svg || !base) return;
      var next = Math.max(1, Math.min(32, base[2] / view[2] * factor));
      var width = base[2] / next, height = base[3] / next;
      view = [view[0] + (view[2] - width) / 2, view[1] + (view[3] - height) / 2, width, height]; update();
    }
    function refresh() {
      var current = host.querySelector('svg');
      if (!current) return;
      var metadata = host.querySelector('.commercial-region-breadcrumb');
      if (metadata) {
        var footer = node('div', 'commercial-territory-meta');
        footer.appendChild(metadata);
        var note = host.querySelector('p'); if (note) footer.appendChild(note);
        stage.parentElement.appendChild(footer);
      }
      base = current.getAttribute('viewBox').split(/\s+/).map(Number); view = base.slice();
      if (current === svg) { update(); return; }
      svg = current; update();
      var start = null, matrix, origin;
      svg.addEventListener('wheel', function (event) {
        if (!event.ctrlKey) return;
        event.preventDefault(); zoom(event.deltaY < 0 ? 1.15 : 1 / 1.15);
      }, { passive: false });
      svg.addEventListener('pointerdown', function (event) {
        if (event.button !== 0 || !event.isPrimary) return;
        start = { x: event.clientX, y: event.clientY }; origin = view.slice(); matrix = svg.getScreenCTM().inverse(); dragging = false;
      });
      svg.addEventListener('pointermove', function (event) {
        if (!start) return;
        var dx = event.clientX - start.x, dy = event.clientY - start.y;
        if (!dragging && Math.hypot(dx, dy) < 5) return;
        if (!dragging) { dragging = true; svg.setPointerCapture(event.pointerId); }
        view = [origin[0] - dx * matrix.a - dy * matrix.c, origin[1] - dx * matrix.b - dy * matrix.d, origin[2], origin[3]]; update();
      });
      svg.addEventListener('pointerup', function () { start = null; });
      svg.addEventListener('pointercancel', function () { start = null; dragging = false; });
      svg.addEventListener('lostpointercapture', function () { start = null; });
      svg.addEventListener('click', function (event) {
        if (dragging) { event.preventDefault(); event.stopImmediatePropagation(); dragging = false; }
      }, true);
    }
    refresh();
    return { refresh: refresh };
  }
  window.DashboardRegioesVisual = { color: color, details: details, mount: mount };
}());
