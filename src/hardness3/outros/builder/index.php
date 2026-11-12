<?php
namespace hardness;
require_once('./conf.php');

// modoEdicao
if ($_SESSION['modoDesenvolvedorApp']) {
    $modoEdicao = "*** Editando App: {$g['app']['nome']} ({$g['app']['versao']}) ***";
} else if ($_SESSION['modoDesenvolvedor']) {
    $modoEdicao = "*** Editando IDE ***";
} else {
    $modoEdicao = '';
}
if ($modoEdicao) {
    $modoEdicao = <<<EOT
        <div class="item">
            <span id="modoDesenvolvedorApp" style="font-weight: bold; margin-right: 16px;background: red;color: #ffffff; padding: 4px; border-radius: 10px;"> &nbsp; {$modoEdicao} &nbsp; </span>
        </div>
EOT;
}
$projeto = $_SESSION['projeto'];
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- Site Properties -->
    <title>Fast Crud - Ferramenta para Criação e Customização de Sistemas</title>

    <!-- You MUST include jQuery before Fomantic -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="semantic/dist/semantic.min.css">
    <script src="semantic/dist/semantic.min.js"></script>
    <link href="static/dropzone/dropzone.css" type="text/css" rel="stylesheet" />
    <script src="static/dropzone/dropzone.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" type="text/css" rel="stylesheet" />
    <script src="static/textarea-helper.js"></script>

    <style type="text/css">
        body {
            background-color: #FFFFFF;
        }
        .ui.menu .item img.logo {
            margin-right: 1.5em;
        }
        .main.container {
            margin-top: 7em;
        }
        .wireframe {
            margin-top: 2em;
        }
        .ui.footer.segment {
            margin: 5em 0em 0em;
            padding: 5em 0em;
        }
        .loading {
            opacity: 0.5;
            text-align: center;
            padding: 16px;
        }
        .divHidden {
            height: 0px;
            width: 0px;
            border: 0;
            padding: 0;
            margin: 0;
            position: fixed;
            overflow: hidden;
        }
        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;   /* prevent horizontal scrollbar */
            overflow-x: hidden; /* add padding to account for vertical scrollbar */
            z-index:1000 !important;
        }

        /* loader */
        /* Absolute Center Spinner */
        .loader {
            position: fixed;
            z-index: 99999;
            height: 2em;
            width: 2em;
            overflow: show;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }

        /* Transparent Overlay */
        .loader:before {
            content: '';
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0, .8));

            background: -webkit-radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0,.8));
        }

        /* :not(:required) hides these rules from IE9 and below */
        .loader:not(:required) {
            /* hide "loader..." text */
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0;
        }

        .loader:not(:required):after {
            content: '';
            display: block;
            font-size: 10px;
            width: 1em;
            height: 1em;
            margin-top: -0.5em;
            -webkit-animation: spinner 150ms infinite linear;
            -moz-animation: spinner 150ms infinite linear;
            -ms-animation: spinner 150ms infinite linear;
            -o-animation: spinner 150ms infinite linear;
            animation: spinner 150ms infinite linear;
            border-radius: 0.5em;
            -webkit-box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;
            box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;
        }

        /* Animation */

        @-webkit-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @-moz-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @-o-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        // Globais utilizadas pelo novoGrid.php
        let gridTabelas = [];
        let gridTabelasJoin = [];
        let camposDisponiveis = [];
        // Globais utilizadas por listarPlanilhas.php
        let planilhaAberta = '';

        $(document).ready(() => {
            carregarProjeto('<?=$projeto?>');
        });

        function carregarNomesTabelas(busca) {
            if (!busca) {
                busca = $('#buscaDados').val();
            }
            $.ajax({
                url: '/hardness3/outros/builder/requests/nomesTabelas.php',
                dataType: 'html',
                type: 'POST',
                data: { busca: busca },
                success: function (data) {
                    $("#nomesTabelas").html(data);
                }
            });
        }

        function carregarNomesGrid(busca) {
            if (!busca) {
                busca = $('#buscaGrids').val();
            }
            $.ajax({
                url: '/hardness3/outros/builder/requests/nomesGrids.php',
                dataType: 'html',
                type: 'POST',
                data: { busca: busca },
                success: function (data) {
                    $("#nomesGrids").html(data);
                }
            });
        }

        function carregarNomesEntidades(busca) {
            if (!busca) {
                busca = $('#buscaEntidades').val();
            }
            $.ajax({
                url: '/hardness3/outros/builder/requests/nomesEntidades.php',
                dataType: 'html',
                type: 'POST',
                data: { busca: busca },
                success: function (data) {
                    $("#nomesEntidades").html(data);
                }
            });
        }

        function carregarNomesForms(busca) {
            if (!busca) {
                busca = $('#buscaForms').val();
            }
            $.ajax({
                url: '/hardness3/outros/builder/requests/nomesForms.php',
                dataType: 'html',
                type: 'POST',
                data: { busca: busca },
                success: function (data) {
                    $("#nomesForms").html(data);
                }
            });
        }

        function carregarTabela(tabela, tabelaAntiga) {
            $.ajax({
                url: '/hardness3/outros/builder/requests/editarTabela.php',
                dataType: 'html',
                type: 'POST',
                data: { tabela: tabela, tabelaAntiga: tabelaAntiga },
                success: function (data) {
                    $("#areaPrincipal").html(data);
                }
            });
        }

        function novaTabela() {
            $.ajax({
                url: '/hardness3/outros/builder/requests/novaTabela.php',
                dataType: 'html',
                type: 'POST',
                success: function (data) {
                    $("#areaPrincipal").html(data);
                }
            });
        }

        function novoGrid() {
            $.ajax({
                url: '/hardness3/outros/builder/requests/novoGrid.php',
                dataType: 'html',
                type: 'POST',
                success: function (data) {
                    $("#areaPrincipal").html(data);
                }
            });
        }

        function carregarGrid(grid) {
            $.ajax({
                url: '/hardness3/outros/builder/requests/novoGrid.php',
                dataType: 'html',
                type: 'POST',
                data: { grid: grid },
                success: function (data) {
                    $("#areaPrincipal").html(data);
                }
            });
        }

        function carregarEntidade(entidade) {
            $.ajax({
                url: '/hardness3/outros/builder/requests/novaEntidade.php',
                dataType: 'html',
                type: 'POST',
                data: { entidade: entidade },
                success: function (data) {
                    $("#areaPrincipal").html(data);
                }
            });
        }

        function carregarForm(form) {
            $.ajax({
                url: '/hardness3/outros/builder/requests/novoForm.php',
                dataType: 'html',
                type: 'POST',
                data: { form: form },
                success: function (data) {
                    $("#areaPrincipal").html(data);
                }
            });
        }

        function enviarPlanilha() {
            $.ajax({
                url: '/hardness3/outros/builder/requests/enviarPlanilha.php',
                dataType: 'html',
                type: 'POST',
                success: function (data) {
                    $("#areaPrincipal").html(data);
                }
            });
        }

        function carregarPlanilha(planilha) {
            $.ajax({
                url: '/hardness3/outros/builder/requests/editarPlanilha.php',
                dataType: 'html',
                type: 'POST',
                data: { planilha: planilha },
                success: function (data) {
                    $("#editarPlanilha").html(data);
                }
            });
        }

        function carregarProjeto(projeto) {
            $.ajax({
                url: '/hardness3/outros/builder/requests/projeto.php',
                dataType: 'html',
                type: 'GET',
                data: { projeto: projeto },
                success: function (data) {
                    $("#areaGlobal").html(data);
                }
            });
        }

        function carregarListarPlanilhas() {
            $.ajax({
                url: '/hardness3/outros/builder/requests/listarPlanilhas.php',
                dataType: 'html',
                type: 'POST',
                data: { planilhaAberta: planilhaAberta },
                success: function (data) {
                    $("#listarPlanilhas").html(data);
                }
            });
        }
    </script>

</head>
<body>

<div class="ui fixed inverted menu">
    <div class="ui container">
        <a href="#" class="header item">
            <img class="logo" src="/hardness3/outros/builder/site/assets/images/logo.svg" style="width: auto; height: 33px;"><!-- Fast Crud -->
        </a>
        <a href="#" class="item" onclick="enviarPlanilha();">Enviar Planilhas</a>
        <a href="/erp" target="_blank" class="item">Ir para o Sistema</a>
        <?=$modoEdicao?>
    </div>
</div>

<div class="ui main container" id="areaGlobal" style="margin-top: 80px;">
    Carregando...
</div>

<div class="ui inverted vertical footer segment">
    <div class="ui center aligned container">
        <img src="/hardness3/outros/builder/site/assets/images/logo.svg" class="ui centered mini image" style="width: 150px;">
        <div class="ui horizontal inverted small divided link list">
            <a class="item" href="http://www.fastcrud.com.br/">Sobre</a>
            <a class="item" href="http://www.fastcrud.com.br/">Contato</a>
            <a class="item" href="http://www.fastcrud.com.br/">Suporte</a>
        </div>
    </div>
</div>

<div id="modalProcessamento" class="ui modal">
    <i class="close icon"></i>
    <div class="header">
        Log de Processamento
    </div>
    <div class="content">
    </div>
</div>

<div id="loader" class="loader" style="display: none;">Loading&#8230;</div>
</body>

</html>
