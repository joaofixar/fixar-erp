<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

// Entidades
$tabelaEntidade = [];
foreach ($g['projeto']['entidades'] as $entidade) {
    if (!$entidade['tabela']) continue;
    $tabelaEntidade[$entidade['tabela']] = $entidade['nome'];
}

// Nomes das tabelas
$query = trim($_POST["busca"]) ? "SHOW TABLES LIKE '%{$_POST["busca"]}%'" : "SHOW TABLES";
$tabelas = array_map(function ($a) { return $a[0]; }, mysqli_fetch_all(mysqli_query($query)));
?>

<table class="ui table very basic compact selectable">
    <?php foreach ($tabelas as $tabela) { ?>
        <tr>
            <td onclick="carregarTabela('<?= mb_strtoupper($tabela) ?>')">
                <div style="display: flex; flex-wrap: nowrap; align-items: center;">
                    <div style="margin-left: 16px;">
                        <i class="database icon"></i>
                    </div>
                    <div style="margin-left: 4px;">
                        <span style="font-weight: bold;"><?= mb_strtoupper($tabela) ?></span>
                        <?php if ($tabelaEntidade[mb_strtoupper($tabela)]) { ?>
                            &nbsp;<span style="opacity: 0.4; font-size: 0.9em;">(<?= $tabelaEntidade[mb_strtoupper($tabela)] ?>)</span>
                        <?php } ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php } ?>
</table>

