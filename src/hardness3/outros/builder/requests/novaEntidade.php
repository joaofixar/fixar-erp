<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

// Criação da entidade
if ($_GET["criarEntidade"]) {
    $dataRaw = file_get_contents('php://input');
    $data = json_decode($dataRaw, true);
    $id = $data['id'] ? $data['id'] : uniqid();
    $data["id"] = $id;
    if (!$g['projeto']['entidades'][$id]) {
        $g['projeto']['entidades'][$id] = [];
    }
    $g['projeto']['entidades'][$id] = array_merge($g['projeto']['entidades'][$id], $data);
    Util::salvarProjeto();
    echo json_encode([
        "erro" => false,
        "id" => $id
    ]);
    die();
}

// Exclusão da entidade
if ($_GET["excluirEntidade"]) {
    // TODO: verificar se não está associada com alguém
    unset($g['projeto']['entidades'][$_POST['id']]);
    Util::salvarProjeto();
    echo json_encode([
        "erro" => false
    ]);
    die();
}

$entidade = $g['projeto']['entidades'][$_POST["entidade"]];
?>

<?php if ($entidade) { ?>
    <h2>Editar Entidade</h2>
<?php } else { ?>
    <h2>Nova Entidade</h2>
<?php } ?>

<div class="ui right labeled input">
    <input id="nomeEntidade" type="text" placeholder="Digite o nome..." value="<?= $entidade['nome'] ?>">
    <div class="ui basic label">
        Entidade
    </div>
</div>

<div style="margin-top: 16px;">
    Auditoria:&nbsp;
    <div class="ui selection dropdown dropdownTipo" style="width: 30px;">
        <input id="auditoriaEntidade" type="hidden" name="tipo" value="<?= !$entidade['auditoria'] || $entidade['auditoria'] == 'S' ? 'S' : 'N' ?>">
        <i class="dropdown icon"></i>
        <div class="default text">Tipo</div>
        <div class="menu">
            <div class="item" data-value="S" selected>Sim</div>
            <div class="item" data-value="N">Não</div>
        </div>
    </div>
</div>

<div style="text-align: center; padding-top: 32px;">
    <a href="#">
        <div class="ui primary icon button" onclick="finalizar();">
            Salvar
        </div>
    </a>
    &nbsp;
    <?php if ($entidade) { ?>

        <a href="#" onclick="excluir('<?=$entidade['id']?>');">
            <div class="ui red icon button">
                Excluir
            </div>
        </a>

    <?php } ?>
</div>


<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    $(document).ready(function() {
        $(".ui.dropdown").dropdown();
    });
    $('#nomeEntidade').keypress(function (e) {
        if (e.which == 13) {
            finalizar();
            return false;
        }
    }).focus();

    function finalizar() {
        const nomeEntidade = $('#nomeEntidade').val();
        if (nomeEntidade.length != 0) {
            $.ajax({
                url: '/hardness3/outros/builder/requests/novaEntidade.php?criarEntidade=true',
                dataType: 'json',
                type: 'POST',
                contentType : 'application/json',
                data: JSON.stringify({
                    id: '<?= $entidade ? $entidade['id'] : '' ?>',
                    nome: nomeEntidade,
                    auditoria: $('#auditoriaEntidade').val(),
                }),
                success: function (data) {
                    if (data["erro"]) {
                        alert(data["msg"]);
                    } else {
                        alert(`Entidade ${nomeEntidade} salva.`);
                        //carregarEntidade(data["id"]);
                        carregarEntidade();
                        carregarNomesEntidades();
                    }
                }
            });
        } else {
            alert("É necessário preencher o nome da entidade.");
        }
        return false;
    }

    function excluir(id) {
        $.ajax({
            url: '/hardness3/outros/builder/requests/novaEntidade.php?excluirEntidade=true',
            type: 'POST',
            data: { id: id },
            success: function (data) {
                if (data["erro"]) {
                    alert(data["msg"]);
                } else {
                    alert(`Entidade <?=$entidade['nome']?> removida.`);
                    carregarEntidade();
                    carregarNomesEntidades();
                }
            }
        });
        return false;
    }

} </script></div>
