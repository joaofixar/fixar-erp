<?php
namespace hardness;
require_once dirname(__DIR__) . '/layout.php';
require_once dirname(dirname(__DIR__)) . '/importadores/importador_orcamento_pdf/inc/app.php';
importador_orcamento_pdf_bootstrap();
integradores_exigir_permissao(INTEGRADORES_C029_ACESSAR_PAINEL, 'Seu perfil não possui permissão para acessar as pendências ISET.');
require_once dirname(dirname(__DIR__)) . '/bibliotecas/webservices/iset/iset.php';
global $g;
$empresaId = (int) $g['empresaAtual'];
if (session_id() === '') session_start();
if (empty($_SESSION['iset_pendencias_csrf'])) $_SESSION['iset_pendencias_csrf'] = bin2hex(random_bytes(24));
$filtros = array();
foreach (array('codigo', 'descricao', 'situacao') as $campo) $filtros[$campo] = isset($_GET[$campo]) && is_string($_GET[$campo]) ? trim($_GET[$campo]) : '';
$pagina = max(1, isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1);
$servico = new ISET_PRODUTOS();
$mensagem = ''; $erro = '';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals($_SESSION['iset_pendencias_csrf'], $_POST['csrf'])) throw new \Exception('Sessão inválida. Atualize a página.');
        if (!isset($_POST['acao']) || $_POST['acao'] !== 'consultar') throw new \Exception('Ação inválida.');
        $mensagem = $servico->consultarPendencia($empresaId, isset($_POST['D009_Id']) ? (int) $_POST['D009_Id'] : 0);
    }
    $lista = $servico->listarPendencias($empresaId, $filtros, $pagina);
} catch (\Throwable $ex) {
    $erro = integracoesMascararTextoSensivel($ex->getMessage());
    $lista = array('pendencias' => array(), 'temMais' => false);
}
$situacoes = ISET_PRODUTOS::situacoesPendencias();
integradores_header('ISET - Pendências de produtos', 'produtos', 'iset');
integradores_iset_produtos_abas('pendencias');
?>
<style>
.iset-pendencias{overflow:auto;min-height:0}.iset-pendencias table{width:100%;border-collapse:collapse;font-size:12px}.iset-pendencias th,.iset-pendencias td{padding:10px;border-bottom:1px solid #d6e0ef;text-align:left;vertical-align:top}.iset-pendencias small{display:block;color:#617495}.iset-pendencias form.filtros{display:flex;gap:10px;flex-wrap:wrap;margin:16px 0}.iset-pendencias input,.iset-pendencias select{padding:8px;border:1px solid #cbd8ea;border-radius:6px}.iset-pendencias .acoes{min-width:150px}.iset-pendencias .acoes form{margin-top:8px}.iset-pendencias nav{display:flex;gap:18px;margin-top:18px}
</style>
<section class="integradores-card iset-pendencias">
<h2>Pendências de produtos</h2>
<p>Produtos cuja sincronização ainda não foi concluída. Consultar novamente verifica a ISET e pode preparar a pendência para o próximo processamento.</p>
<?php if ($mensagem): ?><p class="integradores-flash integradores-flash--sucesso"><?php echo integradores_h($mensagem); ?></p><?php endif; ?>
<?php if ($erro): ?><p class="integradores-flash integradores-flash--erro"><?php echo integradores_h($erro); ?></p><?php endif; ?>
<form class="filtros" method="get">
<input name="codigo" placeholder="Código" aria-label="Código" value="<?php echo integradores_h($filtros['codigo']); ?>">
<input name="descricao" placeholder="Descrição" aria-label="Descrição" value="<?php echo integradores_h($filtros['descricao']); ?>">
<select name="situacao" aria-label="Situação"><option value="">Todas as situações</option>
<?php foreach ($situacoes as $valor => $titulo): ?><option value="<?php echo integradores_h($valor); ?>"<?php echo $filtros['situacao'] === $valor ? ' selected' : ''; ?>><?php echo integradores_h($titulo); ?></option><?php endforeach; ?>
</select><button class="integradores-button" type="submit">Filtrar</button></form>
<table><thead><tr><th>Produto</th><th>SKU / ID ISET</th><th>Situação</th><th>Pendente desde</th><th>Última tentativa</th><th>Próxima tentativa</th><th>Última sincronização concluída</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($lista['pendencias'] as $INT006): ?>
<tr><td><?php echo integradores_h($INT006['D001_Codigo_Produto'] . ' — ' . $INT006['D001_Descricao_Produto']); ?><small>D009 #<?php echo (int) $INT006['INT006_D009_Id']; ?></small></td>
<td><?php echo integradores_h($INT006['INT003_SKU'] ?: (new ISET_SKU())->paraIset($INT006['D001_Codigo_Produto'])); ?><small>ID: <?php echo integradores_h($INT006['INT003_Id_Produto_ISET'] ?: ($INT006['Id_ISET_Conhecido'] ?: 'Não confirmado')); ?></small></td>
<td><?php echo integradores_h(isset($situacoes[$INT006['INT006_Motivo']]) ? $situacoes[$INT006['INT006_Motivo']] : $INT006['INT006_Motivo']); ?><small><?php echo integradores_h($INT006['INT006_Mensagem_Erro']); ?></small><?php if ($INT006['INT006_Flag_Processando'] === 'S'): ?><small>Em processamento</small><?php endif; ?></td>
<td><?php echo integradores_h($INT006['INT006_Data_Hora_Pendente']); ?></td>
<td><?php echo integradores_h($INT006['INT006_Data_Hora_Processando'] ?: ($INT006['INT006_Data_Hora_Processado'] ?: 'Sem tentativa')); ?><small><?php echo (int) $INT006['INT006_Tentativas']; ?> tentativa(s)</small></td>
<td><?php echo integradores_h($INT006['INT006_Proxima_Tentativa']); ?></td>
<td><?php echo integradores_h($INT006['Ultima_Sincronizacao'] ?: 'Não registrada'); ?></td>
<td class="acoes"><a class="integradores-button integradores-button--secundario" href="<?php echo integradores_h(integradores_base_path('iset/logs.php') . '?produto=' . (int) $INT006['INT006_D009_Id']); ?>">Ver logs</a>
<form method="post"><input type="hidden" name="csrf" value="<?php echo integradores_h($_SESSION['iset_pendencias_csrf']); ?>"><input type="hidden" name="acao" value="consultar"><input type="hidden" name="D009_Id" value="<?php echo (int) $INT006['INT006_D009_Id']; ?>"><button class="integradores-button integradores-button--secundario" type="submit">Consultar novamente</button></form></td></tr>
<?php endforeach; ?>
<?php if (!$lista['pendencias'] && !$erro): ?><tr><td colspan="8">Nenhuma pendência encontrada.</td></tr><?php endif; ?>
</tbody></table><nav aria-label="Paginação de pendências">
<?php if ($pagina > 1): ?><a href="?<?php echo integradores_h(http_build_query(array_merge($filtros, array('pagina' => $pagina - 1)))); ?>">Anterior</a><?php endif; ?>
<span>Página <?php echo $pagina; ?></span>
<?php if ($lista['temMais']): ?><a href="?<?php echo integradores_h(http_build_query(array_merge($filtros, array('pagina' => $pagina + 1)))); ?>">Próxima</a><?php endif; ?>
</nav></section>
<?php integradores_footer(); ?>
