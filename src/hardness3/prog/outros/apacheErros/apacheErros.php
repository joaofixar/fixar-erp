<?php
namespace hardness;

/**
 * Atualiza o arquivo de filtros com base na busca
 */

/**
 * Ações
 */
switch ($g['r'][3]) {
    case 'content':
        /*
         * Divisão da Area em Conteúdos
         */
        // Como será dividio o conteúdo? (veja em: /smarty/templates/content-*.tpl)
        $content1 = uniqid();
        $g['smarty']->assign('content1', $content1);
        $g['smarty']->assign('tipo', 'unico');
        // O que será carregado em cada conteúdo?
        $actions = array(
            // identificador, url, paramentros get (opcional)
            array($content1, '/outros/apacheErros/apacheErros/tab/'),
        );
        echo gProcessaAcoes($actions);

        break;

    case 'tab':
        // Form (caso seja o mesmo form em multiplas tabs)
        //$formId = uniqid();
        //$g['smarty']->assign('formId', $formId);

        // Definição das Tabs
        $tabs = array();
        $tabsList = array(
            // Identificador Unico, Programa a ser desenhado na Tab, Titulo, Variaveis GET (opcional)
            array(uniqid(), '/outros/apacheErros/apacheErros/outros/', 'Log erros PHP'),
        );

        // Processa Tabs
        echo gProcessaTabs($tabsList, $tabs);

        // Carrega Template
        $g['smarty']->assign('tabs', $tabs);
        $g['smarty']->display('tab.tpl');

    break;

    case 'outros':
    case 'standalone':
        global $confUsuario,$g;

        $modoStandalone = $g['r'][3] === 'standalone';

        $hostAtual = !empty($confUsuario['urlRaiz']) ? parse_url($confUsuario['urlRaiz'], PHP_URL_HOST) : '';
        $hostAtual = preg_match('/^[a-z0-9.-]+$/i', $hostAtual) ? $hostAtual : 'cliente não identificado';
        $endpointLog = '/outros/apacheErros/apacheErros/log/?ajax=true';
        $urlStandalone = '/outros/apacheErros/apacheErros/standalone/?ajax=true';
        if ($modoStandalone) {
            ?>
            <!doctype html>
            <html lang="pt-BR">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Log de erros · <?= htmlspecialchars($hostAtual, ENT_QUOTES, 'UTF-8') ?></title>
            </head>
            <body class="apache-log-standalone">
            <?php
        }
        ?>
        <style>
            .apache-log-viewer { background:#101827; border-radius:10px; color:#dbe5f0; font-family:Arial,sans-serif; margin:10px 0; overflow:hidden; }
            .apache-log-viewer * { box-sizing:border-box; }
            .apache-log-header { align-items:center; background:linear-gradient(135deg,#17233a,#172d4d); display:flex; gap:15px; justify-content:space-between; padding:18px 20px; }
            .apache-log-title { color:#fff; font-size:19px; font-weight:600; margin:0; }
            .apache-log-subtitle { color:#9db1c9; font-size:12px; margin-top:5px; }
            .apache-log-status { align-items:center; color:#b9c7d8; display:flex; font-size:12px; gap:7px; white-space:nowrap; }
            .apache-log-status-dot { background:#6b7280; border-radius:50%; height:9px; width:9px; }
            .apache-log-status.is-ok .apache-log-status-dot { background:#35c889; box-shadow:0 0 0 4px rgba(53,200,137,.12); }
            .apache-log-status.is-error .apache-log-status-dot { background:#ff667a; box-shadow:0 0 0 4px rgba(255,102,122,.12); }
            .apache-log-toolbar { align-items:center; background:#142033; border-bottom:1px solid #263852; display:flex; flex-wrap:wrap; gap:10px; padding:12px 16px; }
            .apache-log-toolbar input { background:#0d1726; border:1px solid #304660; border-radius:5px; color:#eef5ff; flex:1 1 230px; font-size:13px; outline:0; padding:8px 10px; }
            .apache-log-toolbar input:focus { border-color:#4d9fff; }
            .apache-log-toolbar button { background:#263c58; border:0; border-radius:5px; color:#eaf2fc; cursor:pointer; font-size:12px; padding:8px 11px; }
            .apache-log-toolbar button:hover { background:#345376; }
            .apache-log-toolbar button.is-active { background:#1f8f64; }
            .apache-log-meta { color:#91a6bf; font-size:12px; padding:10px 16px 0; }
            .apache-log-content { background:#0b1220; color:#d5dfeb; font:12px/1.55 Consolas,Monaco,'Courier New',monospace; margin:10px 16px 16px; max-height:560px; min-height:230px; overflow:auto; padding:14px; white-space:pre-wrap; }
            .apache-log-line { border-left:3px solid transparent; display:block; min-height:18px; padding-left:8px; }
            .apache-log-line.is-error { border-left-color:#ff5f70; color:#ffd4d9; }
            .apache-log-line.is-warning { border-left-color:#f4bd50; color:#ffe9b8; }
            .apache-log-empty { color:#91a6bf; padding:30px 10px; text-align:center; }
            body.apache-log-standalone { background:#101827; margin:0; overflow:hidden; }
            body.apache-log-standalone .apache-log-viewer { border-radius:0; display:flex; flex-direction:column; height:100vh; margin:0; }
            body.apache-log-standalone .apache-log-content { flex:1; max-height:none; min-height:0; }
            @media (max-width:600px) { .apache-log-header { align-items:flex-start; flex-direction:column; } .apache-log-content { margin:10px; } }
        </style>
        <div class="apache-log-viewer" id="apache-log-viewer">
            <div class="apache-log-header">
                <div>
                    <h2 class="apache-log-title">Log de erros PHP</h2>
                    <div class="apache-log-subtitle">Cliente atual: <strong><?= htmlspecialchars($hostAtual, ENT_QUOTES, 'UTF-8') ?></strong></div>
                </div>
                <div class="apache-log-status" id="apache-log-status"><span class="apache-log-status-dot"></span><span id="apache-log-status-text">Conectando…</span></div>
            </div>
            <div class="apache-log-toolbar">
                <input type="search" id="apache-log-filter" placeholder="Filtrar linhas do log">
                <button type="button" id="apache-log-refresh">Atualizar agora</button>
                <button type="button" id="apache-log-pause">Pausar</button>
                <button type="button" id="apache-log-bottom">Ir para o final</button>
                <?php if (!$modoStandalone) { ?><button type="button" id="apache-log-open">Abrir em nova aba</button><?php } ?>
            </div>
            <div class="apache-log-meta" id="apache-log-meta">Carregando as últimas linhas…</div>
            <div class="apache-log-content" id="apache-log-content" aria-live="polite"></div>
        </div>
        <script>
        (function () {
            var endpoint = <?= json_encode($endpointLog) ?>;
            var standaloneUrl = <?= json_encode($urlStandalone) ?>;
            var content = document.getElementById('apache-log-content');
            var filter = document.getElementById('apache-log-filter');
            var meta = document.getElementById('apache-log-meta');
            var status = document.getElementById('apache-log-status');
            var statusText = document.getElementById('apache-log-status-text');
            var paused = false;
            var carregando = false;
            var entries = [];

            function render(acompanhandoFinal) {
                var scrollAnterior = content.scrollTop;
                var term = filter.value.toLowerCase();
                var visible = entries.filter(function (line) { return !term || line.toLowerCase().indexOf(term) !== -1; });
                content.innerHTML = '';
                if (!visible.length) {
                    var empty = document.createElement('div');
                    empty.className = 'apache-log-empty';
                    empty.textContent = term ? 'Nenhuma linha encontrada para este filtro.' : 'Nenhum erro registrado ainda.';
                    content.appendChild(empty);
                    return;
                }
                visible.forEach(function (line) {
                    var row = document.createElement('span');
                    row.className = 'apache-log-line';
                    if (/\b(error|fatal|critical|alert|emerg|\bAH\d+)/i.test(line)) row.className += ' is-error';
                    else if (/\b(warn|warning|notice)\b/i.test(line)) row.className += ' is-warning';
                    row.textContent = line;
                    content.appendChild(row);
                });
                content.scrollTop = acompanhandoFinal ? content.scrollHeight : scrollAnterior;
            }

            function setStatus(type, text) {
                status.className = 'apache-log-status ' + type;
                statusText.textContent = text;
            }

            function load() {
                if (paused || carregando) return;
                carregando = true;
                fetch(endpoint, { cache: 'no-store', credentials: 'same-origin' })
                    .then(function (response) { if (!response.ok) throw new Error('HTTP ' + response.status); return response.json(); })
                    .then(function (data) {
                        if (!data.ok) throw new Error(data.message || 'Não foi possível ler o log.');
                        var acompanhandoFinal = content.scrollHeight - content.scrollTop - content.clientHeight < 25;
                        entries = data.lines || [];
                        render(acompanhandoFinal);
                        meta.textContent = entries.length + ' linhas exibidas · arquivo atualizado em ' + (data.modifiedAt || 'agora') + ' · leitura: ' + (data.readAt || 'agora');
                        setStatus('is-ok', 'Atualizado');
                    })
                    .catch(function (error) {
                        meta.textContent = error.message;
                        setStatus('is-error', 'Indisponível');
                    })
                    .then(function () {
                        carregando = false;
                    });
            }

            filter.addEventListener('input', function () { render(false); });
            document.getElementById('apache-log-refresh').addEventListener('click', load);
            document.getElementById('apache-log-bottom').addEventListener('click', function () { content.scrollTop = content.scrollHeight; });
            var openButton = document.getElementById('apache-log-open');
            if (openButton) {
                openButton.addEventListener('click', function () {
                    window.open(standaloneUrl, '_blank', 'noopener');
                });
            }
            document.getElementById('apache-log-pause').addEventListener('click', function (event) {
                paused = !paused;
                event.target.textContent = paused ? 'Retomar' : 'Pausar';
                event.target.className = paused ? 'is-active' : '';
                if (!paused) load();
            });
            load();
            (function agendarProximaLeitura() {
                window.setTimeout(function () {
                    load();
                    agendarProximaLeitura();
                }, 1000);
            }());
        }());
        </script>
        <?php
        if ($modoStandalone) {
            echo '</body></html>';
        }

        break;

    case 'log':
        global $confUsuario,$g;

        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store, private');

        $hostAtual = !empty($confUsuario['urlRaiz']) ? parse_url($confUsuario['urlRaiz'], PHP_URL_HOST) : '';
        if (empty($hostAtual) || !preg_match('/^[a-z0-9.-]+$/i', $hostAtual)) {
            echo json_encode(array('ok' => false, 'message' => 'Não foi possível identificar o cliente atual.'));
            break;
        }

        $pathRaiz = !empty($confUsuario['pathRaiz']) ? rtrim($confUsuario['pathRaiz'], '/') : dirname(__DIR__, 3);
        if (!empty($confUsuario['apacheErrorLog'])) {
            $arquivoLog = $confUsuario['apacheErrorLog'];
        } elseif ($hostAtual === 'localhost' && is_file('/www/apache-log-error.log')) {
            // Ambiente local: o VirtualHost padrão grava neste arquivo.
            $arquivoLog = '/www/apache-log-error.log';
        } else {
            $arquivoLog = $pathRaiz . '/dados_usuarios/log/' . $hostAtual . '-error.log';
        }
        if (!is_file($arquivoLog) || !is_readable($arquivoLog)) {
            echo json_encode(array('ok' => false, 'message' => 'O log deste cliente ainda não está disponível.'));
            break;
        }

        $tamanho = filesize($arquivoLog);
        $inicio = max(0, $tamanho - 65536);
        $arquivo = fopen($arquivoLog, 'r');
        if ($arquivo === false) {
            echo json_encode(array('ok' => false, 'message' => 'Não foi possível abrir o log deste cliente.'));
            break;
        }
        fseek($arquivo, $inicio);
        if ($inicio > 0) {
            fgets($arquivo);
        }
        $conteudo = stream_get_contents($arquivo);
        fclose($arquivo);

        $linhas = preg_split('/\r?\n/', trim($conteudo));
        if (count($linhas) === 1 && $linhas[0] === '') {
            $linhas = array();
        }
        $linhas = array_slice($linhas, -300);
        echo json_encode(array(
            'ok' => true,
            'lines' => $linhas,
            'modifiedAt' => date('d/m/Y H:i:s', filemtime($arquivoLog)),
            'readAt' => date('d/m/Y H:i:s')
        ));

        break;

}

