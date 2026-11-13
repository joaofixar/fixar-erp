(() => {
  'use strict';
  const config = JSON.parse(document.body.dataset.layout || '{}');
  const blockClasses = { evolucao: '.evolution-card', distribuicao: '.situation-card', clientes: '.customers-card', acompanhamento: '.attention-card', segmentos: '.segments-card', cancelamentos: '.cancellations-card' };
  function theme() {
    const names = { destaque: '--portal-destaque', destaqueFinal: '--portal-destaque-final', fundo: '--portal-fundo', primaria: '--portal-primaria' };
    Object.entries(config.tema || {}).forEach(([key, value]) => { if (names[key]) document.documentElement.style.setProperty(names[key], value); });
  }
  function view(type) {
    const layout = config.visoes && config.visoes[type], grid = document.getElementById('analytics');
    if (!layout || !layout.blocos || !grid) return;
    grid.style.setProperty('--portal-coluna', `${layout.proporcaoGraficos}fr`);
    grid.style.setProperty('--portal-coluna-secundaria', `${100-layout.proporcaoGraficos}fr`);
    Object.entries(blockClasses).forEach(([key, selector]) => {
      const card = grid.querySelector(selector); if (!card) return;
      const index = layout.blocos.indexOf(key);
      card.dataset.layoutHidden = String(index < 0); card.style.order = index;
      card.classList.toggle('portal-block-wide', layout.blocos.length % 2 === 1 && index === layout.blocos.length-1);
    });
  }
  function home() {
    const layout = config.inicio, host = document.getElementById('homeView');
    if (!layout || !host) return;
    host.classList.add('portal-home-configured');
    host.style.setProperty('--portal-coluna', `${layout.proporcaoGraficos}fr`);
    host.style.setProperty('--portal-coluna-secundaria', `${100-layout.proporcaoGraficos}fr`);
    const nodes = { indicadores: document.getElementById('homeKpis'), evolucao: host.querySelector('.home-evolution'), desempenho: host.querySelector('.home-performance'), pendencias: document.getElementById('homeActions').closest('article'), clientes: document.getElementById('homeClients').closest('article'), mapa: document.getElementById('homeMapLink'), segmentos: host.querySelector('.home-segments'), cancelamentos: host.querySelector('.home-cancellations') };
    Object.entries(nodes).forEach(([key, node]) => {
      if (!node) return;
      const index = layout.blocos.indexOf(key); node.dataset.layoutHidden = String(index < 0); node.style.order = index;
      const title = node.querySelector('h3'); if (title && layout.titulos[key] != null) title.textContent = layout.titulos[key];
    });
  }
  theme();
  window.PortalPersonalizacao = { config, view, home };
})();
