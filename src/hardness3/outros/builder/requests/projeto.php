<?php
namespace hardness;
require_once('../conf.php');

// Criação da tabela
if ($_GET["criarProjeto"]) {
    $projetoId = uniqid();
    $projeto = [
        "id" => $projetoId,
        "nome" => $_POST["nomeProjeto"]
    ];
    file_put_contents("{$g['pathDadosBuilder']}/{$projetoId}.json", json_encode($projeto));
    echo json_encode([
        "erro" => false,
        "projeto" => $projetoId
    ]);
    $_SESSION["projeto"] = $projetoId;
    die();
}

$arquivos = scandir("{$g['pathDadosBuilder']}/");
$projetos = [];
foreach ($arquivos as $arquivo) {
    if ($arquivo == "." || $arquivo == ".." || is_dir($arquivo)) continue;
    $projetos[] = json_decode(file_get_contents("{$g['pathDadosBuilder']}/{$arquivo}"), true);
}
?>

<?php
$projeto = $_GET['projeto'];
if (!$projeto && !empty($projetos)) {
    $projeto = $projetos[0];
    $_SESSION["projeto"] = $projeto['id'];
}
if ($projeto && $projeto != 'novo') {
    $_SESSION["projeto"] = $projeto;
    $projeto = json_decode(file_get_contents("{$g['pathDadosBuilder']}/{$projeto}.json"), true);
?>

    <div class="ui large breadcrumb">
        <div id="dropdownProjetos" class="ui dropdown">
            <a class="section">Projetos</a>
            <i class="dropdown icon"></i>
            <div class="menu">
                <?php
                foreach ($projetos as $p) {
                    $style = $projeto["id"] == $p["id"] ? 'opacity: 0.5;' : '';
                ?>
                    <div class="item" onclick="carregarProjeto('<?=$p["id"]?>');" style="<?=$style?>"><?=$p["nome"]?></div>
                <?php } ?>
                <div class="divider" style="display: block;"></div>
                <div class="item" onclick="carregarProjeto('novo');">Novo Projeto</div>
            </div>
        </div>
        <i class="right angle icon divider"></i>
        <div class="active section"><?=$projeto["nome"]?></div>

        <button class="ui button tiny" style="margin-left: 8px; padding: 8px;" onclick="removerProjeto();">
            Remover
        </button>
        &nbsp;&nbsp;
        <button class="ui button tiny" style="padding: 8px;" onclick="gerarPHP();">
            Gerar IDE
        </button>
    </div>

    <br/><br/>

    <div class="ui grid">
        <div class="four wide column">
            <table class="ui celled table">
                <tbody>
                <tr style="background-color: #f9fafb;">
                    <td>
                        <div style="display: flex; justify-content: center;">
                            <div style="font-size: 1.2em; font-weight: bold; flex: 3; text-align: center;">
                                Entidades
                            </div>
                            <div stlye="flex: 1;">
                                <i class="icon add link" onclick="carregarEntidade();"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div class="ui left icon input" style="width: 100%;">
                            <i class="search icon"></i>
                            <input id="buscaEntidades" type="text" placeholder="Entidade..." style="border: 0;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div id="nomesEntidades" style="height: 130px; overflow-x: hidden; overflow-y: scroll;">
                            <div class="loading">Carregando...</div>
                        </div>
                    </td>
                </tr>
                <tr style="background-color: #f9fafb;">
                    <td>
                        <div style="display: flex; justify-content: center;">
                            <div style="font-size: 1.2em; font-weight: bold; flex: 3; text-align: center;">
                                Dados
                            </div>
                            <div stlye="flex: 1;">
                                <i class="icon add link" onclick="novaTabela();"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div class="ui left icon input" style="width: 100%;">
                            <i class="search icon"></i>
                            <input id="buscaDados" type="text" placeholder="Dado..." style="border: 0;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div id="nomesTabelas" style="height: 130px; overflow-x: hidden; overflow-y: scroll;">
                            <div class="loading">Carregando...</div>
                        </div>
                    </td>
                </tr>
                <tr style="background-color: #f9fafb;">
                    <td>
                        <div style="display: flex; justify-content: center;">
                            <div style="font-size: 1.2em; font-weight: bold; flex: 3; text-align: center;">
                                Grids
                            </div>
                            <div stlye="flex: 1;">
                                <i class="icon add link" onclick="novoGrid();"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div class="ui left icon input" style="width: 100%;">
                            <i class="search icon"></i>
                            <input id="buscaGrids" type="text" placeholder="Grid..." style="border: 0;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div id="nomesGrids" style="height: 130px; overflow-x: hidden; overflow-y: scroll;">
                            <div class="loading">Carregando...</div>
                        </div>
                    </td>
                </tr>
                <tr style="background-color: #f9fafb;">
                    <td>
                        <div style="display: flex; justify-content: center;">
                            <div style="font-size: 1.2em; font-weight: bold; flex: 3; text-align: center;">
                                Forms
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div class="ui left icon input" style="width: 100%;">
                            <i class="search icon"></i>
                            <input id="buscaForms" type="text" placeholder="Form..." style="border: 0;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <div id="nomesForms" style="height: 130px; overflow-x: hidden; overflow-y: scroll;">
                            <div class="loading">Carregando...</div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="areaPrincipal" class="twelve wide column">
            &nbsp;
        </div>
    </div>

    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        $('#dropdownProjetos').dropdown();
        carregarNomesEntidades();
        carregarNomesTabelas();
        carregarNomesGrid();
        carregarNomesForms();

        $("#buscaEntidades")
            .autocomplete({
                minLength : 0,
                autoFocus : true,
                source : function(request, response) {
                    carregarNomesEntidades(request.term);
                    return false;
                },
                focus : function() {
                    return false;
                },
                select : function(event, ui) {
                    return false;
                }
            });

        $("#buscaDados")
            .autocomplete({
                minLength : 0,
                autoFocus : true,
                source : function(request, response) {
                    carregarNomesTabelas(request.term);
                    return false;
                },
                focus : function() {
                    return false;
                },
                select : function(event, ui) {
                    return false;
                }
            });

        $("#buscaGrids")
            .autocomplete({
                minLength : 0,
                autoFocus : true,
                source : function(request, response) {
                    carregarNomesGrid(request.term);
                    return false;
                },
                focus : function() {
                    return false;
                },
                select : function(event, ui) {
                    return false;
                }
            });

        $("#buscaForms")
            .autocomplete({
                minLength : 0,
                autoFocus : true,
                source : function(request, response) {
                    carregarNomesForms(request.term);
                    return false;
                },
                focus : function() {
                    return false;
                },
                select : function(event, ui) {
                    return false;
                }
            });
    } </script></div>

<?php } else { ?>

    <?php if (!empty($projetos)) { ?>
        <div class="ui large breadcrumb">
            <div id="dropdownProjetos" class="ui dropdown">
                <a class="section">Projetos</a>
                <i class="dropdown icon"></i>
                <div class="menu">
                    <?php foreach ($projetos as $p) { ?>
                        <div class="item" onclick="carregarProjeto('<?=$p["id"]?>');"><?=$p["nome"]?></div>
                    <?php } ?>
                </div>
            </div>
            <i class="right angle icon divider"></i>
            <div class="active section">Novo</div>
        </div>
    <?php } else { ?>
        <div class="ui large breadcrumb">
            <a class="section">Projetos</a>
            <i class="right angle icon divider"></i>
            <div class="active section">Novo</div>
        </div>
    <?php } ?>

    <br/><br/>

    <div class="ui grid">
        <div class="twelve wide column">

            <div class="ui huge form">
                <div class="two fields">
                    <div class="field">
                        <label>Novo Projeto</label>
                        <input id="nomeProjeto" placeholder="Digite o Nome" type="text">
                    </div>
                </div>
                <div class="ui submit button primary" onclick="finalizar();">Criar</div>
            </div>

        </div>
    </div>

    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
        $('#nomeProjeto').keypress(function (e) {
            if (e.which == 13) {
                finalizar();
                return false;
            }
        }).focus();
        $('#dropdownProjetos').dropdown();

        function finalizar() {
            const projeto = $('#nomeProjeto').val().trim();
            if (projeto.length != 0) {
                $.ajax({
                    url: '/hardness3/outros/builder/requests/projeto.php?criarProjeto=true',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        nomeProjeto: projeto
                    },
                    success: function (data) {
                        if (data["erro"]) {
                            alert(data["msg"]);
                        } else {
                            //alert(`Projeto ${projeto} criado.`);
                            carregarProjeto(data["projeto"]);
                        }
                    }
                });
            } else {
                alert("É necessário preencher o nome do projeto.");
            }
            return false;
        }

    } </script></div>

<?php } ?>

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    enviarPlanilha();

    function gerarPHP() {
        $.ajax({
            url: '/hardness3/outros/builder/requests/gerarIDE.php',
            dataType: 'json',
            type: 'GET',
            success: function (data) {
                if (data["erro"]) {
                    alert(data["msg"]);
                } else {
                    // alert(`Arquivos PHP gerados em ${data['dir']}.`);
                    alert(`Conteúdo gerado com sucesso em Configuração -> IDE.`);
                }
            }
        });
        return false;
    }

    function removerProjeto() {
        if (confirm("Esta ação irá remover o projeto e as planilhas enviadas. Deseja continuar?") == true) {
            $.ajax({
                url: '/hardness3/outros/builder/requests/removerProjeto.php',
                dataType: 'json',
                type: 'GET',
                success: function (data) {
                    if (data["erro"]) {
                        alert(data["msg"]);
                    } else {
                        carregarProjeto();
                    }
                }
            });
        }
        return false;
    }
} </script></div>
