<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
$context = balanco_require_access();
if (!balanco_can_process()) { header('Location: ' . balanco_url()); exit; }
$d009Id = (int) ($_GET['d009_id'] ?? $_POST['d009_id'] ?? 0);
$product = balanco_product_by_d009($d009Id);
if (!$product) { balanco_flash('error', 'Produto não encontrado na empresa atual.'); header('Location: ' . balanco_url()); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    balanco_require_csrf();
    $action = (string) ($_POST['action'] ?? '');
    if (in_array($action, ['create_location', 'create_master_location', 'update_location'], true)) {
        try {
            $permissionAction = ['create_location' => 'include', 'create_master_location' => 'include', 'update_location' => 'update'][$action];
            balanco_require_location_permission($permissionAction);
            global $g;
            $conn = $g['conexaoBanco'];
            $company = (int) $g['empresaAtual'];
            $t066Id = (int) ($_POST['t066_id'] ?? 0);
            $d004Id = (int) ($_POST['d004_id'] ?? 0);
            $minimum = str_replace(',', '.', trim((string) ($_POST['minimum'] ?? '0')));
            $maximum = str_replace(',', '.', trim((string) ($_POST['maximum'] ?? '0')));
            if (!is_numeric($minimum) || !is_numeric($maximum) || (float) $minimum < 0 || (float) $maximum < 0) {
                throw new RuntimeException('Informe mínimo e máximo com valores numéricos não negativos.');
            }
            $minimum = number_format((float) $minimum, 5, '.', '');
            $maximum = number_format((float) $maximum, 5, '.', '');
            if (in_array($action, ['create_location', 'update_location'], true)) {
                $locationSql = "SELECT D004_Id FROM D004 WHERE D004_Id='{$d004Id}' AND D004_C004_Id='{$company}' LIMIT 1";
                $locationResult = mysqli_query($conn, $locationSql);
                if (!$locationResult || !mysqli_fetch_assoc($locationResult)) throw new RuntimeException('Selecione uma locação válida da empresa atual.');
            }
            if ($action === 'create_master_location') {
                $local = trim((string) ($_POST['local'] ?? ''));
                $description = trim((string) ($_POST['description'] ?? ''));
                $type = (string) ($_POST['location_type'] ?? '');
                $warehouseId = (int) ($_POST['warehouse_id'] ?? 0);
                $sumPhysical = (string) ($_POST['sum_physical_stock'] ?? '');
                $ownership = (string) ($_POST['ownership'] ?? '');
                if ($local === '' || mb_strlen($local) > 15) throw new RuntimeException('Informe o Local com até 15 caracteres.');
                if (mb_strlen($description) > 200) throw new RuntimeException('A descrição pode ter no máximo 200 caracteres.');
                if (!in_array($type, ['1', '2'], true)) throw new RuntimeException('Selecione o tipo da locação.');
                if (!in_array($sumPhysical, ['', 'S', 'N'], true) || !in_array($ownership, ['', '0', '1', '2'], true)) throw new RuntimeException('Dados de locação inválidos.');
                if ($warehouseId > 0) {
                    $warehouseResult = mysqli_query($conn, "SELECT D146_Id FROM D146 WHERE D146_Id='{$warehouseId}' LIMIT 1");
                    if (!$warehouseResult || !mysqli_fetch_assoc($warehouseResult)) throw new RuntimeException('Selecione um armazém válido.');
                }
                $local = mysqli_real_escape_string($conn, $local);
                $description = mysqli_real_escape_string($conn, $description);
                $warehouseValue = $warehouseId > 0 ? "'{$warehouseId}'" : 'NULL';
                $sumValue = $sumPhysical === '' ? 'NULL' : "'{$sumPhysical}'";
                $ownershipValue = $ownership === '' ? 'NULL' : "'{$ownership}'";
                mysqli_begin_transaction($conn);
                $sql = "INSERT INTO D004 (D004_C004_Id, D004_C008_Id, D004_Local, D004_Descricao, D004_Flag_Tipo, D004_D146_Id, D004_Flag_Somar_Estoque_Fisico, D004_Indicador_Propriedade) VALUES ('{$company}', 0, '{$local}', '{$description}', '{$type}', {$warehouseValue}, {$sumValue}, {$ownershipValue})";
                if (!mysqli_query($conn, $sql)) throw new RuntimeException('Não foi possível cadastrar a nova locação.');
                $d004Id = (int) mysqli_insert_id($conn);
                $sql = "INSERT INTO T066 (T066_D048_Id, T066_D009_Id, T066_D004_Id, T066_Quantidade_Estoque, T066_Minimo, T066_Maximo) VALUES (0, '{$d009Id}', '{$d004Id}', 0, '{$minimum}', '{$maximum}')";
                if (!mysqli_query($conn, $sql)) throw new RuntimeException('Não foi possível vincular a nova locação ao produto.');
                mysqli_commit($conn);
                balanco_flash('success', 'Nova locação cadastrada e vinculada ao produto.');
            } else if ($action === 'create_location') {
                $duplicateSql = "SELECT T066_Id FROM T066 WHERE T066_D009_Id='{$d009Id}' AND T066_D004_Id='{$d004Id}' LIMIT 1";
                $duplicateResult = mysqli_query($conn, $duplicateSql);
                if ($duplicateResult && mysqli_fetch_assoc($duplicateResult)) throw new RuntimeException('Este produto já está cadastrado nessa locação.');
                $sql = "INSERT INTO T066 (T066_D048_Id, T066_D009_Id, T066_D004_Id, T066_Quantidade_Estoque, T066_Minimo, T066_Maximo) VALUES (0, '{$d009Id}', '{$d004Id}', 0, '{$minimum}', '{$maximum}')";
                if (!mysqli_query($conn, $sql)) throw new RuntimeException('Não foi possível incluir a locação.');
                balanco_flash('success', 'Locação incluída para o produto.');
            } else {
                $itemSql = "SELECT T066.T066_Id, T066.T066_Flag_Balanco, T066.T066_Quantidade_Estoque, T066.T066_Quantidade_Estoque_Liquido FROM T066 INNER JOIN D009 ON D009.D009_Id=T066.T066_D009_Id LEFT JOIN D004 ON D004.D004_Id=T066.T066_D004_Id WHERE T066.T066_Id='{$t066Id}' AND T066.T066_D009_Id='{$d009Id}' AND D009.D009_C004_Id='{$company}' AND (IFNULL(T066.T066_D004_Id, 0)=0 OR D004.D004_C004_Id='{$company}') LIMIT 1";
                $itemResult = mysqli_query($conn, $itemSql);
                $item = $itemResult ? mysqli_fetch_assoc($itemResult) : null;
                if (!$item) throw new RuntimeException('Locação não encontrada para este produto.');
                if ($item['T066_Flag_Balanco'] === 'S') throw new RuntimeException('A locação está em contagem e não pode ser alterada.');
                $duplicateSql = "SELECT T066_Id FROM T066 WHERE T066_D009_Id='{$d009Id}' AND T066_D004_Id='{$d004Id}' AND T066_Id!='{$t066Id}' LIMIT 1";
                $duplicateResult = mysqli_query($conn, $duplicateSql);
                if ($duplicateResult && mysqli_fetch_assoc($duplicateResult)) throw new RuntimeException('Este produto já está cadastrado nessa locação.');
                $sql = "UPDATE T066 SET T066_D004_Id='{$d004Id}', T066_Minimo='{$minimum}', T066_Maximo='{$maximum}' WHERE T066_Id='{$t066Id}' LIMIT 1";
                if (!mysqli_query($conn, $sql)) throw new RuntimeException('Não foi possível alterar a locação.');
                balanco_flash('success', 'Locação alterada com sucesso.');
            }
        } catch (Throwable $e) {
            if (isset($conn) && $conn instanceof mysqli) @mysqli_rollback($conn);
            balanco_flash('error', $e->getMessage());
        }
        header('Location: ' . balanco_url('locacoes.php?d009_id=' . $d009Id)); exit;
    }
    $location = $action === 'scan_location'
        ? balanco_location_by_scan($d009Id, (string) ($_POST['locacao'] ?? ''))
        : ['T066_Id' => (int) ($_POST['t066_id'] ?? 0)];
    if (!$location) { balanco_flash('error', 'Este produto não está cadastrado na locação lida.'); header('Location: ' . balanco_url('locacoes.php?d009_id=' . $d009Id)); exit; }
    try { $reservation = balanco_acquire_t066((int) $location['T066_Id'], $context); }
    catch (Throwable $e) { balanco_flash('error', $e->getMessage()); header('Location: ' . balanco_url('locacoes.php?d009_id=' . $d009Id)); exit; }
    if ($reservation['status'] === 'acquired' || $reservation['status'] === 'resumed') { header('Location: ' . balanco_url('contar.php?t066_id=' . (int) $location['T066_Id'])); exit; }
    if ($reservation['status'] === 'blocked') {
        balanco_flash('error', 'Esta locação está sendo contada por ' . ($reservation['owner_name'] ?: 'outro usuário') . '.');
    } else {
        balanco_flash('error', 'Registro de produto por locação não encontrado na empresa atual.');
    }
    header('Location: ' . balanco_url('locacoes.php?d009_id=' . $d009Id)); exit;
}
$locations = balanco_locations_for_product($d009Id);
$canIncludeLocation = balanco_can_manage_locations('include');
$canUpdateLocation = balanco_can_manage_locations('update');
$warehouses = $canIncludeLocation ? balanco_warehouses() : [];
balanco_header('Escolher locação', $context, [
    'extra_scripts' => ['https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js'],
]);
?>
<nav class="product-navigation"><a href="<?php echo balanco_h(balanco_url()); ?>">← Novo produto</a></nav><section class="product-strip"><div class="product-strip__identity"><strong><?php echo balanco_h($product['D001_Codigo_Produto']); ?></strong><h2 title="<?php echo balanco_h($product['D001_Descricao_Produto']); ?>"><?php echo balanco_h(balanco_truncate($product['D001_Descricao_Produto'], 48)); ?></h2><span><?php echo balanco_h($product['D037_Unidade']); ?> · <?php echo balanco_h($product['D082_Marca']); ?></span></div><dl class="product-strip__stock"><div><dt>Físico</dt><dd><?php echo balanco_h(balanco_format_quantity($product['D009_Quantidade_Estoque'], 2)); ?></dd></div><div><dt>Líquido</dt><dd><?php echo balanco_h(balanco_format_quantity($product['D009_Quantidade_Estoque_Liquido'], 2)); ?></dd></div></dl></section>
<section class="scan-stage scan-stage--compact"><h2>Bipe ou selecione a locação</h2><form class="scan-form" method="post"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><input type="hidden" name="d009_id" value="<?php echo $d009Id; ?>"><input type="hidden" name="action" value="scan_location"><div class="scan-field"><input id="location-scan" name="locacao" inputmode="none" autocomplete="off" autofocus placeholder="Aguardando leitura da locação"><button type="button" class="scan-field__camera" data-barcode-camera-open="true" data-barcode-label="locação" data-target-input="location-scan" aria-label="Ler locação pela câmera" title="Ler locação pela câmera"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3 7.2 5H4a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3.2L15 3H9Zm3 5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/></svg></button></div><button class="button" type="submit">Localizar</button></form></section>
<?php if ($canIncludeLocation): ?><div class="location-management-actions"><button class="button button--compact" type="button" data-open-dialog="include-location-dialog">Incluir locação existente</button><button class="button button--secondary button--compact" type="button" data-open-dialog="create-location-dialog">Cadastrar nova locação</button></div><dialog class="confirm-dialog location-manager-dialog" id="include-location-dialog"><h2>Incluir locação existente</h2><form class="location-manager__form" method="post"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><input type="hidden" name="d009_id" value="<?php echo $d009Id; ?>"><?php balanco_location_search_field($d009Id, 'include'); ?><label>Mínimo<input name="minimum" inputmode="decimal" value="0"></label><label>Máximo<input name="maximum" inputmode="decimal" value="0"></label><button class="button" name="action" value="create_location" type="submit">Incluir</button><button class="button button--secondary" type="button" data-close-dialog="true">Cancelar</button></form></dialog><dialog class="confirm-dialog location-manager-dialog" id="create-location-dialog"><h2>Cadastrar nova locação</h2><form class="location-manager__form location-manager__form--new" method="post"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><input type="hidden" name="d009_id" value="<?php echo $d009Id; ?>"><label>Local<input name="local" maxlength="15" required></label><label>Descrição<textarea name="description" maxlength="200"></textarea></label><label>Tipo<select name="location_type" required><option value="">Selecione</option><option value="1">1 - Permite apenas um produto</option><option value="2">2 - Permite mais de um produto</option></select></label><label>Armazém<select name="warehouse_id"><option value="">...</option><?php foreach ($warehouses as $warehouse): ?><option value="<?php echo (int) $warehouse['D146_Id']; ?>"><?php echo balanco_h($warehouse['D146_Descricao']); ?></option><?php endforeach; ?></select></label><label>Somar estoque<select name="sum_physical_stock"><option value="">...</option><option value="S">Sim</option><option value="N">Não</option></select></label><label>Indicador de propriedade<select name="ownership"><option value="">...</option><option value="0">0 - Do informante e em seu poder</option><option value="1">1 - Do informante em posse de terceiros</option><option value="2">2 - De terceiros em posse do informante</option></select></label><button class="button" name="action" value="create_master_location" type="submit">Cadastrar e vincular</button><button class="button button--secondary" type="button" data-close-dialog="true">Cancelar</button></form></dialog><?php endif; ?>
<section class="location-list">
<?php foreach ($locations as $location):
    $unlinked = (int) ($location['D004_Id'] ?? 0) <= 0;
    $locationLabel = $unlinked ? 'Sem locação vinculada' : (string) $location['D004_Local'];
    $own = !$unlinked && $location['T066_Flag_Balanco'] === 'S' && (int) $location['T066_C007_Id_Marcou_Balanco'] === $context['user_id'];
    $other = !$unlinked && $location['T066_Flag_Balanco'] === 'S' && !$own;
    $dialogId = 'manage-location-' . (int) $location['T066_Id'];
?>
    <article class="location-row <?php echo $other ? 'location-row--blocked' : ''; ?> <?php echo $unlinked ? 'location-row--unlinked' : ''; ?>">
        <strong><?php echo balanco_h($locationLabel); ?></strong>
        <div class="location-stock"><span>Físico <b><?php echo balanco_h(balanco_format_quantity($location['T066_Quantidade_Estoque'], 2)); ?></b></span><span>Líquido <b><?php echo balanco_h(balanco_format_quantity($location['T066_Quantidade_Estoque_Liquido'], 2)); ?></b></span></div>
        <small>Último balanço: <?php echo balanco_h(balanco_format_balance_date($location['T066_Data_Balanco'])); ?></small>
        <?php if ($unlinked): ?><small class="location-unlinked">Defina uma locação para liberar a contagem.</small>
        <?php elseif ($other): ?><small class="location-lock">Em contagem por <?php echo balanco_h($location['C007_Primeiro_Nome'] ?: 'outro usuário'); ?></small>
        <?php else: ?><form class="location-action-form" method="post"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><input type="hidden" name="d009_id" value="<?php echo $d009Id; ?>"><input type="hidden" name="t066_id" value="<?php echo (int) $location['T066_Id']; ?>"><button class="location-action" name="action" value="enter_count" type="submit"><?php echo $own ? 'Retomar' : 'Contar'; ?></button></form><?php endif; ?>
        <?php if (!$other && $canUpdateLocation): ?>
            <button class="location-manage-button" type="button" data-open-dialog="<?php echo $dialogId; ?>">Gerenciar</button>
            <dialog class="confirm-dialog location-manager-dialog" id="<?php echo $dialogId; ?>"><h2>Gerenciar locação: <?php echo balanco_h($locationLabel); ?></h2><form class="location-manager__form" method="post"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><input type="hidden" name="d009_id" value="<?php echo $d009Id; ?>"><input type="hidden" name="t066_id" value="<?php echo (int) $location['T066_Id']; ?>"><?php balanco_location_search_field($d009Id, 'update', (int) $location['T066_Id'], (int) $location['D004_Id'], $unlinked ? '' : $locationLabel); ?><label>Mínimo<input name="minimum" inputmode="decimal" value="<?php echo balanco_h((string) $location['T066_Minimo']); ?>"></label><label>Máximo<input name="maximum" inputmode="decimal" value="<?php echo balanco_h((string) $location['T066_Maximo']); ?>"></label><button class="button" name="action" value="update_location" type="submit">Salvar alteração</button><button class="button button--secondary" type="button" data-close-dialog="true">Cancelar</button></form></dialog>
        <?php endif; ?>
    </article>
<?php endforeach; ?>
<?php if (!$locations): ?><div class="panel empty">Este produto não possui locações cadastradas nesta empresa.</div><?php endif; ?>
</section>
<?php balanco_footer();
