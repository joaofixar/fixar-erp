<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');
?>

<table class="ui table very basic compact selectable">
    <?php foreach ($g['projeto']['grids'] as $key => $grid) { ?>
        <tr>
            <td onclick="carregarGrid('<?= $key ?>')">
                <div style="display: flex; flex-wrap: nowrap; align-items: center;">
                    <div style="margin-left: 16px;">
                        <i class="table icon"></i>
                    </div>
                    <div style="margin-left: 4px;">
                        <span style="font-weight: bold;"><?= $grid["nome"] ?></span>
                    </div>
                </div>
            </td>
        </tr>
    <?php } ?>
</table>
