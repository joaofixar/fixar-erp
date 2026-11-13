<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

$nomeTabela = $_POST['tabela'];
$nomeTabelaAntiga = $_POST['tabelaAntiga'];

// Entidades
$tabelaEntidade = [];
foreach ($g['projeto']['entidades'] as $entidade) {
    if (!$entidade['tabela']) continue;
    $tabelaEntidade[$entidade['tabela']] = $entidade['nome'];
}

// Criação da tabela
if ($_GET["editarEntidade"]) {
    $data = json_decode(file_get_contents('php://input'), true);

    // Limpar
    foreach ($g['projeto']['entidades'] as $key => $entidade) {
        if ($entidade['tabela'] == $data['tabela']) {
            unset($g['projeto']['entidades'][$key]['tabela']);
        }
    }
    // Associar
    if ($data['entidade']) {
        $g['projeto']['entidades'][$data['entidade']]['tabela'] = strtoupper($data['tabela']);
    }

    Util::salvarProjeto();

    echo json_encode([
        "erro" => false
    ]);
    die();
}

// Campos de uma tabela
$query = <<<EOT
select * from information_schema.columns
where table_schema = '{$confUsuario["dbDatabase"]}' AND table_name = '{$nomeTabela}'
order by table_name,ordinal_position;
EOT;
$campos = [];
foreach (mysqli_fetch_all(mysqli_query($query), MYSQLI_ASSOC) as $campo) {
    $ehChavePrimaria = preg_match("/^[^_]+_Id$/", $campo["COLUMN_NAME"], $matches) ? true : false;
    $ehChaveEstrangeira = !$ehChavePrimaria && preg_match("/_Id$/U", $campo["COLUMN_NAME"]);
    $chaveEstrangeiraTabela = null;
    if ($ehChaveEstrangeira) {
        preg_match("/^.*_(?<tabela>.+)_Id$/", $campo["COLUMN_NAME"], $matches);
        $chaveEstrangeiraTabela = $matches["tabela"];
    }
    $campos[] = new Campo(
        $campo["COLUMN_NAME"],
        Campo::tipoMysqlCampo($campo["COLUMN_TYPE"]),
        $ehChavePrimaria,
        $ehChaveEstrangeira,
        $chaveEstrangeiraTabela
    );
}

usort($campos, function ($c1, $c2) {
    if ($c1->ehChavePrimaria) {
        return -1;
    }
    if ($c2->ehChavePrimaria) {
        return 1;
    }
    if (!$c1->ehChaveEstrangeira && $c2->ehChaveEstrangeira) {
        return 1;
    } else if ($c1->ehChaveEstrangeira && !$c2->ehChaveEstrangeira) {
        return -1;
    } else {
        return strcmp($c1->nome, $c2->nome);
    }
});
?>

<?php if ($nomeTabelaAntiga) { ?>
    <h4><a href="#" onclick="carregarTabela('<?= strtoupper($nomeTabelaAntiga) ?>')"><- <?= $nomeTabelaAntiga ?></a></h4>
<?php } ?>
<h2>Dados: <?= $nomeTabela ?></h2>

<h3>Entidade</h3>

<?= Util::dropdownEntidade('dropdownEntidade', $nomeTabela); ?>

<br/><br/>

<a href="#">
    <div class="ui primary icon button" onclick="finalizar();">
        Salvar
    </div>
</a>

<h3>Dados</h3>

<table class="ui celled table">
    <thead>
    <tr class="center aligned">
        <th>Campo</th>
        <th>Tipo</th>
        <th>Referência Dados</th>
        <th>Amostragem</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($campos as $campo) {
        $amostragem = [];
        if ($campo->tipo == TipoCampo::Texto) {
            $query = "SELECT {$campo->nome} FROM {$nomeTabela} WHERE COALESCE({$campo->nome}, '') != '' ORDER BY {$nomeTabela}_Id DESC LIMIT 5";
            $amostragem = array_map(function ($a) {
                return $a[0];
            }, mysqli_fetch_all(mysqli_query($query)));
        }
    ?>
        <tr class="<?= $campo->ehChavePrimaria ? 'disabled' : '' ?>">
            <td data-label="Nome"><?= preg_replace("/^{$nomeTabela}_/", '', $campo->nome) ?></td>
            <td data-label="Tipo"><?= $campo->ehChaveEstrangeira ? 'Dados' : ($campo->ehChavePrimaria ? 'Identificador Único' : $campo->tipo) ?></td>
            <td data-label="Tabela">
                <?php if ($campo->chaveEstrangeiraTabela) { ?>
                    <a href="#" onclick="carregarTabela('<?= strtoupper($campo->chaveEstrangeiraTabela) ?>', '<?= $nomeTabela ?>')"><?= $campo->chaveEstrangeiraTabela ?></a>
                    <?php if ($tabelaEntidade[mb_strtoupper($campo->chaveEstrangeiraTabela)]) { ?>
                        &nbsp;<span style="opacity: 0.4; font-size: 0.9em;">(<?= $tabelaEntidade[mb_strtoupper($campo->chaveEstrangeiraTabela)] ?>)</span>
                    <?php } ?>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
            </td>
            <td data-label="Amostragem">
                <span style="opacity: 0.4;">
                    <?= implode('<br>', array_map(function ($item) { return strlen($item) > 20 ? substr($item,0,20) . "..." : $item; }, $amostragem)); ?>
                </span>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    $('.dropdownEntidade').dropdown();

    function finalizar() {
        $.ajax({
            url: '/hardness3/outros/builder/requests/editarTabela.php?editarEntidade=true',
            dataType: 'json',
            type: 'POST',
            contentType : 'application/json',
            data: JSON.stringify({
                tabela: '<?= $nomeTabela ?>',
                entidade: $('.dropdownEntidade').find('.menu .selected').attr("data-value")
            }),
            success: function (data) {
                if (data["erro"]) {
                    alert(data["msg"]);
                } else {
                    alert("Dados <?= $nomeTabela ?> alterado.");
                    carregarTabela('<?= $nomeTabela ?>');
                    carregarNomesTabelas();
                    carregarNomesEntidades();
                }
            }
        });
        return false;
    }
} </script></div>