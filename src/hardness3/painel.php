<?php
namespace hardness;

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Referrer-Policy: no-referrer');
header('X-Content-Type-Options: nosniff');
if (isset($_GET['acao'])) {
    require __DIR__ . '/painel-personalizado/api.php';
    exit;
}
header('Content-Type: text/html; charset=utf-8');
// O navegador recebe uma URL nova sempre que um arquivo visual é alterado.
$versaoCss = substr(hash_file('sha256', __DIR__ . '/painel-personalizado/painel.css'), 0, 12);
$versaoGraficos = substr(hash_file('sha256', __DIR__ . '/painel-personalizado/graficos.js'), 0, 12);
$versaoComercial = substr(hash_file('sha256', __DIR__ . '/painel-personalizado/comercial.js'), 0, 12);
$versaoPainel = substr(hash_file('sha256', __DIR__ . '/painel-personalizado/painel.js'), 0, 12);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PAINEL — Controles operacionais</title>
    <link rel="stylesheet" href="painel-personalizado/painel.css?v=<?= $versaoCss ?>">
    <script src="painel-personalizado/graficos.js?v=<?= $versaoGraficos ?>" defer></script>
    <script src="painel-personalizado/comercial.js?v=<?= $versaoComercial ?>" defer></script>
    <script src="painel-personalizado/painel.js?v=<?= $versaoPainel ?>" defer></script>
</head>
<body>
    <header class="cabecalho">
        <div class="marca"><div class="marca-hardness"><img src="static/img/logo_hardness.png" alt="Hardness" width="136" height="34"></div><div><span class="sobretitulo">PAINEL</span><h1 id="tituloGeral">Painéis de controle</h1></div></div>
        <div class="marca-empresa" id="marcaEmpresa" hidden><img id="logoEmpresa" alt="Logo da empresa atual"></div>
        <div class="cabecalho-direita">
            <div class="ferramentas">
                <label for="setor">Exibir setor</label>
                <select id="setor" disabled><option value="">Todos os setores</option></select>
                <button type="button" id="pausar" disabled aria-pressed="false">Pausar alternância</button>
                <span id="data"></span>
                <button type="button" id="telaCheia">Tela cheia</button>
            </div>
            <div class="relogio"><time id="hora"></time><span id="atualizacao" class="atualizacao-cabecalho" role="status">Aguardando configuração</span></div>
        </div>
    </header>
    <main>
        <div class="identificacao"><div><p class="sobretitulo" id="setorAtual">CONTROLE OPERACIONAL</p><h2 id="tituloPainel">Preparando o painel</h2></div><span class="situacao" id="situacao" role="status">Conectando…</span></div>
        <div id="aviso" class="aviso" role="alert" hidden></div>
        <section id="conteudo" aria-label="Conteúdo do painel" aria-busy="true"><div class="mensagem"><p>Carregando os painéis disponíveis.</p></div></section>
        <section id="carregamentoPainel" class="painel-carregando" aria-live="polite" aria-label="Carregamento do painel">
            <div class="painel-carregando-card" role="status">
                <strong>Carregando painel</strong>
                <small>Consultando e consolidando os dados. Aguarde um momento.</small>
                <span class="painel-carregando-progresso" role="progressbar" aria-label="Carregamento em andamento"><i></i></span>
            </div>
        </section>
    </main>
    <footer><nav aria-label="Navegar entre painéis"><button type="button" id="anterior" aria-label="Página ou painel anterior" disabled>←</button><span id="paginacao"></span><button type="button" id="proximo" aria-label="Próxima página ou painel" disabled>→</button></nav><div class="alternancia-info"><span id="alternancia">Modo TV</span><span id="progressoAlternancia" class="alternancia-progresso" aria-hidden="true" hidden><i></i></span></div></footer>
    <noscript><style>.painel-carregando { display: none; }</style>Ative o JavaScript para visualizar os painéis.</noscript>
</body>
</html>
