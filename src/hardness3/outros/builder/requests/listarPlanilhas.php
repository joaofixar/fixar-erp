<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

if (empty($g['projeto']['planilhas'])) {
    die();
}

if ($_GET["removerPlanilha"]) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$g['projeto']['planilhas'][$data['id']]) {
        echo json_encode([
            "erro" => "Planilha não encontrada."
        ]);
        die();
    }

    unlink($g['pathDadosArquivos'] . '/' . $g['projeto']['planilhas'][$data['id']]['arquivoId']);
    unset($g['projeto']['planilhas'][$data['id']]);

    Util::salvarProjeto();

    echo json_encode([
        "erro" => false
    ]);
    die();
}

if ($_GET["associarPlanilha"]) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$g['projeto']['planilhas'][$data['idPlanilha']]) {
        echo json_encode([
            "erro" => "Planilha não encontrada."
        ]);
        die();
    }

    if ($data['idEntidade']) {
        $g['projeto']['planilhas'][$data['idPlanilha']]['entidade'] = $data['idEntidade'];
    } else {
        unset($g['projeto']['planilhas'][$data['idPlanilha']]['entidade']);
    }

    Util::salvarProjeto();

    echo json_encode([
        "erro" => false
    ]);
    die();
}
?>

<h2>Planilhas</h2>

<table class="ui celled table">
    <thead>
    <tr class="center aligned">
        <th>&nbsp;</th>
        <th>Planilha</th>
        <th>Entidade</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($g['projeto']['planilhas'] as $planilha) {
        $style = '';
        if ($planilha['id'] == $_POST["planilhaAberta"]) {
            $style = 'font-weight: bold';
        }
        ?>
        <tr data-id="<?=$planilha["id"]?>" data-id-entidade="<?=$planilha['entidade']?>">
            <td class="collapsing" data-label="Remover" style="padding: 8px 8px 8px 16px; width: 1%;">
                <i class="times circle outline icon link large" onclick="removerPlanilha(this);"></i>
                <input type="hidden" data-label="Tabela">
            </td>
            <td data-label="Nome" onclick="abrirPlanilha(this);" style="cursor: pointer; <?=$style?>"><?= $planilha['nomeArquivo'] ?></td>
            <td data-label="Entidade" style="padding: 0;">
                <?= Util::dropdownEntidade('dropdownEntidadePlanilha', $planilha['entidade'], 'width: 100%; border: 0;'); ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>


<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {

    $(".dropdownEntidadePlanilha").dropdown({
        onChange: function (value, text, $choice) {
            const idEntidade = value;
            $(this).closest('tr').data('id-entidade', idEntidade);
            const idPlanilha = $(this).closest('tr').data('id');
            $.ajax({
                url: '/hardness3/outros/builder/requests/listarPlanilhas.php?associarPlanilha=true',
                dataType: 'json',
                type: 'POST',
                contentType : 'application/json',
                data: JSON.stringify({
                    idEntidade: idEntidade,
                    idPlanilha: idPlanilha
                }),
                success: function (data) {
                    if (data["erro"]) {
                        alert(data["msg"]);
                    } else {
                        if (idPlanilha == planilhaAberta) {
                            enviarPlanilha();
                        } else {
                            carregarListarPlanilhas();
                        }
                    }
                }
            });
        }
    });

    function removerPlanilha(elem) {
        const id = $(elem).closest("tr").data("id");
        $.ajax({
            url: '/hardness3/outros/builder/requests/listarPlanilhas.php?removerPlanilha=true',
            dataType: 'json',
            type: 'POST',
            contentType : 'application/json',
            data: JSON.stringify({
                id: id
            }),
            success: function (data) {
                if (data["erro"]) {
                    alert(data["msg"]);
                } else {
                    if (id == planilhaAberta) {
                        planilhaAberta = '';
                    }
                    carregarListarPlanilhas(planilhaAberta);
                }
            }
        });
        return false;
    }

    function abrirPlanilha(elem) {
        if (!$(elem).closest("tr").data('id-entidade')) {
            alert('É necessário selecionar uma entidade para a planilha.')
        } else {
            $(elem).closest("tbody").find('td').css('font-weight', 'normal');
            $(elem).css('font-weight', 'bold');
            planilhaAberta = $(elem).closest("tr").data('id');
            carregarPlanilha(planilhaAberta);
        }
    }
} </script></div>
