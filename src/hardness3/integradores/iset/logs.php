<?php
namespace hardness;

require_once dirname(__DIR__) . '/layout.php';
require_once dirname(dirname(__DIR__)) . '/importadores/importador_orcamento_pdf/inc/app.php';

importador_orcamento_pdf_bootstrap();
integradores_exigir_permissao(INTEGRADORES_C029_ACESSAR_PAINEL, 'Seu perfil não possui permissão para acessar a integração ISET.');

global $g;
$empresa = (int) $g['empresaAtual'];
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$operacao = isset($_GET['operacao']) ? trim($_GET['operacao']) : '';
$inicio = isset($_GET['inicio']) ? trim($_GET['inicio']) : '';
$fim = isset($_GET['fim']) ? trim($_GET['fim']) : '';
require_once dirname(dirname(__DIR__)) . '/bibliotecas/webservices/iset/iset.php';
$filtros = array('status' => in_array($status, array(INT_STATUS_SUCESSO, INT_STATUS_ERRO, INT_STATUS_AVISO, INT_STATUS_INFO), true) ? $status : '', 'operacao' => $operacao, 'inicio' => $inicio, 'fim' => $fim);
$filtros['produto'] = isset($_GET['produto']) ? (int) $_GET['produto'] : 0;
$logs = (new ISET_HISTORICO())->logs($empresa, $filtros);
integradores_header('ISET - Logs', 'logs', 'iset');
?>
<style>
.integradores-content{width:min(1280px,calc(100% - 48px));max-width:1280px}.integradores-logs{max-width:none;min-height:0}.integradores-log-filtros{display:flex;flex-wrap:wrap;gap:10px;margin:18px 0}.integradores-log-filtros input,.integradores-log-filtros select{height:38px;padding:0 10px;border:1px solid #cbd8ea;border-radius:8px;color:#07224d}.integradores-log-filtros .integradores-button,.integradores-log-tabela .integradores-button{margin:0}.integradores-log-tabela{width:100%;border-collapse:collapse;table-layout:fixed;font-size:12px}.integradores-log-tabela th,.integradores-log-tabela td{padding:11px 8px;border-bottom:1px solid #d6e0ef;text-align:left;vertical-align:top}.integradores-log-tabela th{color:#617495;font-size:10px;text-transform:uppercase}.integradores-log-tabela td:last-child{text-align:right}.integradores-log-tabela .integradores-button--secundario{margin-left:0}.integradores-log-mensagem{overflow-wrap:anywhere;word-break:break-word}.integradores-log-status{font-weight:800}.integradores-log-status--S{color:#087858}.integradores-log-status--E{color:#b4233d}.integradores-log-dialog{max-width:700px;width:calc(100% - 32px);border:0;border-radius:14px;box-shadow:0 18px 50px rgba(7,34,77,.25)}.integradores-log-dialog::backdrop{background:rgba(7,34,77,.3)}.integradores-log-dialog pre{max-height:430px;overflow:auto;padding:15px;border-radius:9px;background:#f3f7fc;white-space:pre-wrap}@media(max-width:760px){.integradores-content{width:calc(100% - 24px)}}
</style>
<section class="integradores-card integradores-logs">
<p class="integradores-eyebrow">Histórico de integração</p><h2>Logs</h2><p>Operações concluídas ou interrompidas da integração selecionada.</p>
<form class="integradores-log-filtros" method="get"><input type="number" name="produto" placeholder="D009" aria-label="D009" value="<?php echo $filtros['produto'] ?: ''; ?>"><input type="date" name="inicio" value="<?php echo integradores_h($inicio); ?>"><input type="date" name="fim" value="<?php echo integradores_h($fim); ?>"><select name="status"><option value="">Todos os status</option><?php foreach(array(INT_STATUS_SUCESSO=>'Sucesso',INT_STATUS_ERRO=>'Erro',INT_STATUS_AVISO=>'Aviso',INT_STATUS_INFO=>'Informação') as $v=>$t): ?><option value="<?php echo $v; ?>"<?php echo $status===$v?' selected':''; ?>><?php echo $t; ?></option><?php endforeach; ?></select><input name="operacao" placeholder="Operação" value="<?php echo integradores_h($operacao); ?>"><button class="integradores-button" type="submit">Filtrar</button></form>
<table class="integradores-log-tabela"><colgroup><col style="width:78px"><col style="width:84px"><col style="width:168px"><col style="width:54px"><col style="width:166px"><col style="width:66px"><col style="width:82px"><col><col style="width:100px"></colgroup><thead><tr><th>Data/hora</th><th>Integração</th><th>Operação</th><th>Status</th><th>Etapa</th><th>ID externo</th><th>Referência</th><th>Mensagem</th><th></th></tr></thead><tbody>
<?php foreach($logs as $log): ?><tr><td><?php echo integradores_h(integradores_formatar_data_hora_log($log)); ?></td><td><?php echo integradores_h($log['INT001_Integracao']); ?></td><td><?php echo integradores_h($log['INT001_Operacao']); ?></td><td class="integradores-log-status integradores-log-status--<?php echo integradores_h($log['INT001_Status']); ?>"><?php echo integradores_h($log['INT001_Status']); ?></td><td><?php echo integradores_h($log['INT001_Etapa']); ?></td><td><?php echo integradores_h($log['INT001_Id_Externo']); ?></td><td><?php echo integradores_h($log['INT001_Tabela_Principal'] . ' #' . $log['INT001_Id_Principal']); ?></td><td class="integradores-log-mensagem"><?php echo integradores_h($log['INT001_Mensagem']); ?></td><td><button class="integradores-button integradores-button--secundario" type="button" data-log="<?php echo integradores_h($log['INT001_Log']); ?>">Detalhes</button></td></tr><?php endforeach; ?>
<?php if(!$logs): ?><tr><td colspan="9">Nenhum log encontrado.</td></tr><?php endif; ?></tbody></table>
</section>
<dialog class="integradores-log-dialog" id="integradores-log-dialog"><h3>Detalhes do log</h3><pre id="integradores-log-json"></pre><button class="integradores-button integradores-button--secundario" onclick="document.getElementById('integradores-log-dialog').close()">Fechar</button></dialog>
<script>document.querySelectorAll('[data-log]').forEach(function(b){b.onclick=function(){var v=this.dataset.log||'{}';try{v=JSON.stringify(JSON.parse(v),null,2)}catch(e){}document.getElementById('integradores-log-json').textContent=v;document.getElementById('integradores-log-dialog').showModal()}})</script>
<?php integradores_footer(); ?>
