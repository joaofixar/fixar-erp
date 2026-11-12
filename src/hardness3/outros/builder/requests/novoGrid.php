<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

// Criação do grid
if ($_GET["criarGrid"]) {
    $dataRaw = file_get_contents('php://input');
    $data = json_decode($dataRaw, true);
    $id = $data['id'] ? $data['id'] : uniqid();
    $data["id"] = $id;
    $g['projeto']['grids'][$id] = $data;
    Util::salvarProjeto();
    echo json_encode([
        "erro" => false,
        "id" => $id
    ]);
    die();
}

// Exclusão do grid
if ($_GET["excluirGrid"]) {
    unset($g['projeto']['grids'][$_POST['id']]);
    Util::salvarProjeto();
    echo json_encode([
        "erro" => false
    ]);
    die();
}

// Carregar grid
$gridDados = false;
if ($_POST['grid']) {
    $gridId = $_POST['grid'];
    $gridDados = $g['projeto']['grids'][$gridId];
}

// Campos disponíveis (para o auto complete)
if ($_GET["camposDisponiveis"]) {
    $data = json_decode(file_get_contents('php://input'), true);
    $tabelasEmUso = $data["tabelas"];
    $tabelasEmUso_ = implode(',', array_map(function($item) { return "'{$item}'"; }, $tabelasEmUso));

    $query = <<<EOT
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE FROM information_schema.columns
WHERE TABLE_SCHEMA = '{$confUsuario["dbDatabase"]}' AND TABLE_NAME IN ({$tabelasEmUso_})
ORDER BY TABLE_NAME,ORDINAL_POSITION;
EOT;
    $camposDisponiveis = [];
    foreach (mysqli_fetch_all(mysqli_query($query), MYSQLI_ASSOC) as $item) {
        $camposDisponiveis[] = $item['COLUMN_NAME'];
    }
    echo json_encode($camposDisponiveis);
    die();
}
?>

<?php if ($gridDados) { ?>
    <h2>Editar Grid</h2>
<?php } else { ?>
    <h2>Novo Grid</h2>
<?php } ?>

<div class="ui right labeled input">
    <input id="nome" type="text" placeholder="Digite o nome..." value="<?= $gridDados['nome'] ?>">
    <div class="ui basic label">
        Grid
    </div>
</div>

<h3>Campos</h3>

<div style="padding: 0px 16px 0px 16px">
    <div class="ui search" style="text-align: center; padding: 16px;">
        <div class="ui icon input" style="width: 100%;">
            <input class="prompt" type="text" placeholder="Digite o nome do campo...">
            <i class="search icon"></i>
        </div>
        <div class="results" style="overflow-y: scroll; width: 80%; height: 500px;"></div>
    </div>
</div>

<table class="ui compact celled definition table novoGrid">
    <thead class="full-width">
        <tr class="center aligned">
            <th>&nbsp;</th>
            <th>Título</th>
            <th>Campo</th>
<!--            <th>Tipo</th>-->
            <th>Opções</th>
        </tr>
    </thead>
    <tbody>
        <tr id="novoCampo" class="divHidden">
            <td class="collapsing" data-label="Remover" style="padding: 8px 8px 8px 16px; width: 1%;">
                <i class="times circle outline icon link large" onclick="removerCampo(this);"></i>
                <input type="hidden" data-label="Tabela">
            </td>
            <td data-label="Titulo" style="padding: 0;">
                <div class="ui input" style="width: 100%;">
                    <input class="nomeCampo" type="text" placeholder="Título" style="border: 0;">
                </div>
            </td>
            <td data-label="Campo">Campo</td>
<!--            <td data-label="Tipo">Tipo</td>-->
            <td data-label="Opcoes" style="padding: 0;">
                <div class="ui clearable multiple selection dropdown dropdownOpcoes" style="width: 100%; border: 0;">
                    <input type="hidden" name="opcoes">
                    <i class="dropdown icon"></i>
                    <div class="default text">Selecione...</div>
                    <div class="menu">
                        <div class="item" data-value="colunaEditar">Habilitar Edição</div>
                        <div class="item" data-value="colunaExibirTitulo">Ocultar Título</div>
                        <div class="item" data-value="filtro">Habilitar Filtro</div>
                        <div class="item" data-value="colunaExibir">Ocular Coluna</div>
                    </div>
                </div>
            </td>
        </tr>
        <?php foreach ($gridDados["grid"] as $item) { ?>
            <tr>
                <td class="collapsing" data-label="Remover" style="padding: 8px 8px 8px 16px; width: 1%;">
                    <i class="times circle outline icon link large" onclick="removerCampo(this);"></i>
                    <input type="hidden" data-label="Tabela">
                </td>
                <td data-label="Titulo" style="padding: 0;">
                    <div class="ui input" style="width: 100%;">
                        <input class="nomeCampo" type="text" placeholder="Título" style="border: 0;" value="<?= $item['titulo'] ?>">
                    </div>
                </td>
                <td data-label="Campo">
                    <?= $item['campo'] ?>
                </td>
<!--                <td data-label="Tipo">
                    <?= $item['tipo'] ?>
                </td>-->
                <td data-label="Opcoes" style="padding: 0;">
                    <div class="ui clearable multiple selection dropdown dropdownOpcoes" style="width: 100%; border: 0;">
                        <input type="hidden" name="opcoes" value="<?= $item['opcoes'] ?>">
                        <i class="dropdown icon"></i>
                        <div class="default text">Selecione...</div>
                        <div class="menu">
                            <div class="item" data-value="colunaEditar">Habilitar Edição</div>
                            <div class="item" data-value="colunaExibirTitulo">Ocultar Título</div>
                            <div class="item" data-value="filtro">Habilitar Filtro</div>
                            <div class="item" data-value="colunaExibir">Ocultar Coluna</div>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
<!--    <tfoot class="full-width">-->
<!--    <tr>-->
<!--        <th colspan="5">-->
<!--            <a href="#">-->
<!--                <div class="ui right floated secondary icon button" onclick="finalizar();">-->
<!--                    Finalizar-->
<!--                </div>-->
<!--            </a>-->
<!--        </th>-->
<!--    </tr>-->
<!--    </tfoot>-->
</table>

<h3>Extra</h3>

<div class="ui input" style="width: 100%;">
    <textarea id="textAreaExtra" rows="5" placeholder="Cláusula SQL (WHERE, GROUP BY, ORDER BY, etc.)" style="width: 100%;" <?= !$gridDados ? 'disabled="disabled"' : '' ?>><?= $gridDados ? $gridDados["extra"] : '' ?></textarea>
</div>

<h3>Formulário</h3>

<div class="ui search clearable selection dropdown dropdownForm">
    <i class="dropdown icon"></i>
    <div class="default text">Nenhum</div>
    <div class="menu">
        <?php foreach ($g['projeto']['forms'] as $form) {
            if ($gridDados['form'] == $form["id"]) $formValor = $form['id'];
            ?>
            <div class="item" data-value="<?= $form['id'] ?>"><?= $form['nome'] ?></div>
        <?php } ?>
    </div>
    <input type="hidden" name="language" value="<?= $formValor ?>">
</div>

<div style="text-align: center; padding-top: 32px;">
<a href="#">
    <div class="ui primary icon button" onclick="finalizar();">
        Salvar
    </div>
</a>
&nbsp;
<?php if ($gridDados) { ?>
    <a href="/erp" target="_blank">
        <div class="ui icon button">
            Visualizar
        </div>
    </a>
    &nbsp;
    <a href="#" onclick="excluir('<?=$gridDados['id']?>');">
        <div class="ui red icon button">
            Excluir
        </div>
    </a>
<?php } ?>
</div>


<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    gridTabelas = <?= $gridDados ? json_encode($gridDados["tabelas"]) : '[]' ?>;
    gridTabelasJoin = <?= $gridDados ? json_encode($gridDados["tabelasJoin"]) : '[]' ?>;
    camposDisponiveis = [];
    $('.ui.search')
        .search({
            apiSettings: {
                url: '/hardness3/outros/builder/requests/pesquisaCampoGrid.php?q={query}',
                data: {
                    tabelas: () => { return JSON.stringify(gridTabelas); }
                },
            },
            fields: {
                results : 'campos',
                title   : 'campo',
                description : 'tipo'
            },
            minCharacters : 2,
            maxResults: 50,
            cache: false,
            onSelect: (result) => {
                if (!gridTabelas.includes(result["tabela"])) {
                    gridTabelas.push(result["tabela"]);
                    gridTabelasJoin.push(result["join"]);
                }
                const novoCampo = $("#novoCampo").clone();
                novoCampo
                    .removeClass("divHidden")
                    .removeAttr("id")
                    .appendTo($(".novoGrid tbody"));
                // TODO: opção editarColuna somente pode ser na tabela primária
                novoCampo.find($(".dropdownOpcoes")).dropdown();
                let titulo = result["campo"].replace(result["tabela"], "");
                titulo = titulo.replaceAll("_", " ").trim();
                novoCampo.find('td[data-label="Titulo"] input').val(titulo);
                novoCampo.find('td[data-label="Campo"]').html(result["campo"]);
                novoCampo.find('td[data-label="Tipo"]').html(result["tipo"]);
                novoCampo.find('input[data-label="Tabela"]').val(result["tabela"]);
                $('.nomeCampo:last').focus();
                $('.ui.search input').val("");
                carregarCamposDisponiveis();
            }
        })
    ;

    // Baseado em http://jsfiddle.net/prgtrdr/pTt2L/
    $("#textAreaExtra")
        .autocomplete({
            minLength : 0,
            autoFocus : true,
            source : function(request, response) {
                // Use only the last entry from the textarea (exclude previous matches)
                lastEntry = request.term.split(/(,| |\n)/).pop();
                if (lastEntry.trim().length == 0) {
                    $("#textAreaExtra").autocomplete("close");
                    return false;
                }

                const filteredArray = $.map(camposDisponiveis, function(item) {
                    if (item == lastEntry) {
                        return null;
                    }
                    if (item.toLowerCase().indexOf(lastEntry.toLowerCase()) === 0) {
                        return item;
                    } else {
                        return null;
                    }
                });
                const filteredArray2 = $.map(camposDisponiveis, function(item) {
                    if (item == lastEntry) {
                        return null;
                    }
                    if (!filteredArray.includes(item) && item.toLowerCase().indexOf(lastEntry.toLowerCase()) !== -1) {
                        return item;
                    } else {
                        return null;
                    }
                });

                // delegate back to autocomplete, but extract the last term
                response($.ui.autocomplete.filter(filteredArray.concat(filteredArray2), lastEntry));
            },
            focus : function() {
                // prevent value inserted on focus
                return false;
            },
            select : function(event, ui) {
                const text = " " + this.value; // para funcionar com apenas uma palavra
                this.value = text.replace(/([, \n])([^, \n])+$/, "$1" + ui.item.value).trim();
                return false;
            }
        }).on("keydown", function(event) {
            // // don't navigate away from the field on tab when selecting an item
            // if (event.keyCode === $.ui.keyCode.TAB /** && $(this).data("ui-autocomplete").menu.active **/) {
            //     event.preventDefault();
            //     return;
            // }
            //
            // // Code to position and move the selection box as the user types
            // var newY = $(this).textareaHelper('caretPos').top + (parseInt($(this).css('font-size'), 10) * 1.5);
            // var newX = $(this).textareaHelper('caretPos').left;
            // var posString = "left+" + newX + "px top+" + newY + "px";
            // $(this).autocomplete("option", "position", {
            //     my : "left top",
            //     at : posString
            // });
        });

    function finalizar() {
        const grid = [];
        $('.novoGrid tbody tr:gt(0)').each(function () {
            grid.push({
                "titulo": $(this).find('td[data-label="Titulo"] input').val().trim(),
                "campo": $(this).find('td[data-label="Campo"]').text().trim(),
                "tipo": $(this).find('td[data-label="Tipo"]').text().trim(),
                "tabela": $(this).find('input[data-label="Tabela"]').val().trim(),
                "opcoes": $(this).find('td[data-label="Opcoes"] input').val().trim()
            });
        });
        const nome = $('#nome').val();
        if (grid.length != 0 && nome.length != 0) {
            $.ajax({
                url: '/hardness3/outros/builder/requests/novoGrid.php?criarGrid=true',
                dataType: 'json',
                type: 'POST',
                contentType : 'application/json',
                data: JSON.stringify({
                    id: '<?= $gridDados ? $gridDados['id'] : '' ?>',
                    nome: nome,
                    grid: grid,
                    tabelas: gridTabelas,
                    tabelasJoin: gridTabelasJoin,
                    extra: $("#textAreaExtra").val(),
                    form: $('.dropdownForm').find('.menu .selected').attr("data-value")
                }),
                success: function (data) {
                    if (data["erro"]) {
                        alert(data["msg"]);
                    } else {
                        alert(`Grid ${nome} salvo.`);
                        carregarGrid(data["id"]);
                        carregarNomesGrid();
                    }
                }
            });
        } else {
            alert("É necessário preencher o nome do grid e adicionar campos.");
        }
        return false;
    }

    function carregarCamposDisponiveis() {
        $.ajax({
            url: '/hardness3/outros/builder/requests/novoGrid.php?camposDisponiveis=true',
            dataType: 'json',
            type: 'POST',
            contentType : 'application/json',
            data: JSON.stringify({ tabelas: gridTabelas }),
            success: function (data) {
                camposDisponiveis = data;
                camposDisponiveis.push('WHERE', 'ORDER BY', 'GROUP BY', 'LIMIT', 'IS NULL', 'IS NOT NULL');
                $('#textAreaExtra').removeAttr("disabled");
            }
        });
    }

    function excluir(id) {
        $.ajax({
            url: '/hardness3/outros/builder/requests/novoGrid.php?excluirGrid=true',
            type: 'POST',
            data: { id: id },
            success: function (data) {
                if (data["erro"]) {
                    alert(data["msg"]);
                } else {
                    alert(`Grid <?=$gridDados['nome']?> removido.`);
                    carregarGrid();
                    carregarNomesGrid();
                }
            }
        });
        return false;
    }

    function removerCampo(elem) {
        $(elem).closest("tr").remove();
    }

    $(".dropdownOpcoes").dropdown();
    $('.dropdownForm').dropdown();
    carregarCamposDisponiveis();
} </script></div>
