<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

// Verificar próximo nome de tabela disponível
$query = <<<EOT
SHOW TABLES LIKE 'P%';
EOT;
$tabelas = array_map(function ($a) { return $a[0]; }, mysqli_fetch_all(mysqli_query($query)));
if (empty($tabelas)) {
    $nomeTabela = 'P001';
} else {
    sort($tabelas);
    $nomeTabela = end($tabelas);;
    $nomeTabela = "P" . str_pad(intval(substr($nomeTabela, 1))+1, 3, "0", STR_PAD_LEFT);
}

// Criação da tabela
if ($_GET["criarTabela"]) {
    $data = json_decode(file_get_contents('php://input'), true);

    // Criação da tabela no banco
    $campos = [
        "\t{$nomeTabela}_Id int unsigned auto_increment primary key"
    ];
    foreach ($data['tabela'] as $campo) {
        // $campo["referencia"]
        $campo['nome'] = gLimpaAcentosPuro($campo['nome']);
        $campos[] = "\t{$nomeTabela}_{$campo['nome']} " . Campo::tipoCampoMysql($campo["tipo"]) . " null";
    }
    $campos_ = implode(",\n", $campos);

    // $data
    $comment = mysql_real_escape_string($g['projeto']['entidades'][$data['entidade']]['nome']);
    $sql = <<<EOT
CREATE TABLE {$nomeTabela}
(
$campos_
) DEFAULT CHARSET=latin1 COMMENT='{$comment}';
EOT;
    mysqli_query($sql);
    $erroSql = mysql_error();
    if (!empty($erroSql)) {
        mysqli_query("DROP TABLE {$nomeTabela};");
        echo json_encode([
            "erro" => true,
            "msg" => json_encode($erroSql)
        ]);
    } else {
        // Associar com a entidade
        if ($data['entidade']) {
            $g['projeto']['entidades'][$data['entidade']]['tabela'] = strtoupper($nomeTabela);
            Util::salvarProjeto();
        }

        echo json_encode([
            "erro" => false
        ]);
    }
    die();
}


$query = <<<EOT
SHOW TABLES;
EOT;
$tabelas = array_map(function ($a) { return $a[0]; }, mysqli_fetch_all(mysqli_query($query)));
?>

<h2>Novo Dados: <?= $nomeTabela ?></h2>

<h3>Entidade</h3>

<div class="ui search clearable selection dropdown dropdownEntidade">
    <i class="dropdown icon"></i>
    <div class="default text">Selecione...</div>
    <div class="menu">
        <?php foreach ($g['projeto']['entidades'] as $entidade) { ?>
            <div class="item <?= $entidade['tabela'] ? 'disabled' : '' ?>" data-value="<?= $entidade['id'] ?>"><?= $entidade['nome'] ?></div>
        <?php } ?>
    </div>
</div>

<h3>Campos</h3>

<table class="ui celled table novaTabela">
    <thead>
    <tr class="center aligned">
        <th>&nbsp;</th>
        <th>Campo</th>
        <th>Tipo</th>
        <th>Referência Dados</th>
    </tr>
    </thead>
    <tbody>
        <tr class="disabled">
            <td data-label="Opcoes"></td>
            <td data-label="Nome">Id</td>
            <td data-label="Tipo">Identificador Único</td>
            <td data-label="Tabela">&nbsp;</td>
        </tr>
        <tr id="novoCampo" class="divHidden">
            <td class="collapsing" data-label="Remover" style="padding: 8px 8px 8px 16px; width: 1%;">
                <i class="times circle outline icon link large" onclick="removerCampo(this);"></i>
            </td>
            <td data-label="Nome" style="padding: 0;">
                <div class="ui input" style="width: 100%;">
                    <input class="nomeCampo" type="text" placeholder="Nome do Campo" style="border: 0;">
                </div>
            </td>
            <td data-label="Tipo" style="padding: 0;">
                <div class="ui search selection dropdown dropdownTipo" style="width: 100%; border: 0;">
                    <input name="tipo" type="hidden" value="Texto">
                    <i class="dropdown icon"></i>
                    <div class="default text">Tipo</div>
                    <div class="menu">
                        <div class="item" data-value="Texto">Texto</div>
                        <div class="item" data-value="Inteiro">Inteiro</div>
                        <div class="item" data-value="Decimal">Decimal</div>
                        <div class="item" data-value="Data">Data</div>
                        <div class="item" data-value="DataHora">DataHora</div>
                        <div class="item" data-value="Dados">Dados</div>
                    </div>
                </div>
            </td>
            <td data-label="Tabela" style="padding: 0;">
                <div class="ui search selection dropdown dropdownTabela" style="width: 100%; border: 0; display: none;">
                    <i class="dropdown icon"></i>
                    <div class="default text">Referência Dados</div>
                    <div class="menu">
                        <?php foreach ($tabelas as $tabela) {
                            $tabela = strtoupper($tabela);
                            if (preg_match("/^P{$nomeTabela}/", $tabela)) continue;
                        ?>
                            <div class="item" data-value="<?= $tabela ?>"><?= $tabela ?></div>
                        <?php } ?>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
    <tfoot class="full-width">
    <tr>
        <th colspan="4">
            <a href="#">
                <div class="ui right floated primary labeled icon button" onclick="novoCampo();">
                    <i class="add icon"></i> Novo Campo
                </div>
            </a>
            <div class="ui secondary button" onclick="finalizar();">
                Finalizar
            </div>
        </th>
    </tr>
    </tfoot>
</table>

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    $('.dropdownEntidade').dropdown();

    function novoCampo() {
        const novoCampo = $("#novoCampo").clone();
        novoCampo
            .removeClass("divHidden")
            .removeAttr("id")
            .appendTo($(".novaTabela tbody"));
        novoCampo.find($(".dropdownTipo")).dropdown({
            onChange: (value, text, choice) => {
                if (value == "Dados") {
                    $(choice).closest("tr").find('.dropdownTabela').css("display", "block").find('.search').focus();
                } else {
                    $(choice).closest("tr").find('.dropdownTabela').css("display", "none");
                }
            }
        });
        novoCampo.find($(".dropdownTabela")).dropdown();
        $('.nomeCampo:last').focus();
    }

    function removerCampo(elem) {
        $(elem).closest("tr").remove();
    }

    function finalizar() {
        const tabela = [];
        $('.novaTabela tbody tr:gt(1)').each(function () {
            const inputs = $(this).find('input');
            tabela.push({
                nome: $(inputs[0]).val(),
                tipo: $(inputs[1]).parent().find('.menu .selected').attr("data-value"),
                referencia: $(inputs[2]).parent().find('.menu .selected').attr("data-value"),
            });
        });
        if (tabela.length != 0) {
            $.ajax({
                url: '/hardness3/outros/builder/requests/novaTabela.php?criarTabela=true',
                dataType: 'json',
                type: 'POST',
                contentType : 'application/json',
                data: JSON.stringify({
                    tabela: tabela,
                    entidade: $('.dropdownEntidade').find('.menu .selected').attr("data-value")
                }),
                success: function (data) {
                    if (data["erro"]) {
                        alert(data["msg"]);
                    } else {
                        alert("Dados <?= $nomeTabela ?> criado.");
                        carregarTabela('<?= $nomeTabela ?>');
                        carregarNomesTabelas();
                    }
                }
            });
        } else {
            alert("Dados vazio.");
        }
        return false;
    }
} </script></div>
