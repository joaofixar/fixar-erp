<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

// Carregar form
$formDados = false;
if ($_POST['form']) {
    $formId = $_POST['form'];
    $formDados = $g['projeto']['forms'][$formId];
}

// Exclusão do form
if ($_GET["excluirForm"]) {
    // Verifica se algum grid utiliza este formulário
    foreach ($g['projeto']['grids'] as $key => $value) {
        if ($value["form"] == $_POST['id']) {
            unset($g['projeto']['grids'][$key]["form"]);
        }
    }
    unset($g['projeto']['forms'][$_POST['id']]);
    Util::salvarProjeto();
    echo json_encode([
        "erro" => false
    ]);
    die();
}
?>

<?php if ($formDados) { ?>
    <h2>Visualizar Form</h2>
<?php } else { ?>
    <h2>Novo Form</h2>
<?php } ?>

<div class="ui right labeled input">
    <input id="nome" type="text" placeholder="Digite o nome..." value="<?= $formDados['nome'] ?>" disabled="disabled">
    <div class="ui basic label">
        Form
    </div>
</div>

<h3>Campos</h3>

<table class="ui compact celled definition table novoForm">
    <thead class="full-width">
    <tr class="center aligned">
        <th>Título</th>
        <th>Campo</th>
<!--        <th>Tipo</th>-->
        <th>Opções</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($formDados["form"] as $item) { ?>
        <tr>
            <td data-label="Titulo" style="padding: 0;">
                <div class="ui input" style="width: 100%;">
                    <input class="nomeCampo" type="text" placeholder="Título" style="border: 0;" value="<?= $item['titulo'] ?>">
                </div>
            </td>
            <td data-label="Campo">
                <?= $item['tipo'] == 'Select' ? implode(",", $item['campo']) : $item['campo'] ?>
            </td>
<!--            <td data-label="Tipo">
                <?= $item['tipo'] ?>
            </td>-->
            <td data-label="Tipo">
                <!-- TODO: ajustar caso tenha mais opções -->
                <?= empty($item['opcoes']) ? '' : "{$item['opcoes'][0]['nome']}: {$item['opcoes'][0]['valor']}" ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<div style="text-align: center; padding-top: 32px;">
    <a href="#" onclick="excluir('<?=$formDados['id']?>');">
        <div class="ui red icon button">
            Excluir
        </div>
    </a>
</div>

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

    function excluir(id) {
        $.ajax({
            url: '/hardness3/outros/builder/requests/novoForm.php?excluirForm=true',
            type: 'POST',
            data: { id: id },
            success: function (data) {
                if (data["erro"]) {
                    alert(data["msg"]);
                } else {
                    alert(`Form <?=$formDados['nome']?> removido.`);
                    $("#areaPrincipal").html("");
                    carregarNomesForms();
                }
            }
        });
        return false;
    }

} </script></div>
