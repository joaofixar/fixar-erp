<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

$entidades = $g['projeto']['entidades'];
?>

<table class="ui table very basic compact selectable">
    <?php
    foreach ($entidades as $entidade) {
        if ($_POST["busca"]) {
            if (strpos(mb_strtoupper($entidade['nome']), mb_strtoupper($_POST["busca"])) === false) continue;
        }
    ?>
        <tr>
            <td onclick="carregarEntidade('<?= $entidade['id'] ?>')">
                <div style="display: flex; flex-wrap: nowrap; align-items: center;">
                    <div style="margin-left: 16px;">
                        <i class="shapes icon"></i>
                    </div>
                    <div style="margin-left: 4px;">
                        <span style="font-weight: bold;"><?= $entidade['nome'] ?></span>
                        <?php if ($entidade['tabela']) { ?>
                            &nbsp;<span style="opacity: 0.4; font-size: 0.9em;">(<?= $entidade['tabela'] ?>)</span>
                        <?php } ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php } ?>
</table>
