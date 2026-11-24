/*
 * Mantem a sessao do apontamento viva enquanto a pagina estiver aberta.
 * O limite de inatividade (8h) e controlado no servidor (sessaoApontamento.php);
 * se ele estourar, o ping responde ok=false e a tela volta para o login.
 */
(function () {
    var INTERVALO_MS = 5 * 60 * 1000; // 5 minutos
    var URL_PING = './sessaoPing.php';
    var redirecionando = false;

    function irParaLogin() {
        if (redirecionando) {
            return;
        }
        redirecionando = true;
        window.location.href = './index.php';
    }

    function ping() {
        if (redirecionando || !window.fetch) {
            return;
        }
        window.fetch(URL_PING + '?t=' + Date.now(), {
            method: 'GET',
            credentials: 'same-origin',
            cache: 'no-store'
        }).then(function (resposta) {
            if (!resposta.ok) {
                return null; // erro de rede/servidor: tenta de novo no proximo ciclo
            }
            return resposta.json();
        }).then(function (dados) {
            if (dados && dados.ok === false) {
                irParaLogin();
            }
        }).catch(function () {
            // sem conexao no momento (ex.: coletor fora do wi-fi); tenta depois
        });
    }

    window.setInterval(ping, INTERVALO_MS);

    // Coletor/celular suspende timers com a tela apagada: ao voltar, pinga na hora.
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            ping();
        }
    });
})();
