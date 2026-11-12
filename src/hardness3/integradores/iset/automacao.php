<?php
namespace hardness;

require_once dirname(__DIR__) . '/layout.php';
require_once dirname(dirname(__DIR__)) . '/importadores/importador_orcamento_pdf/inc/app.php';

importador_orcamento_pdf_bootstrap();
require_once dirname(dirname(__DIR__)) . '/bibliotecas/webservices/iset/iset.php';
integradores_exigir_permissao(INTEGRADORES_C029_CONFIGURACOES, 'Seu perfil não possui permissão para gerenciar a automação da integração ISET.');
global $g;
$empresa = (int) $g['empresaAtual'];
$protocolo = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
$baseSistema = preg_replace('#/integradores$#', '', integradores_base_path());
$urlWebhook = $protocolo . '://' . $_SERVER['HTTP_HOST'] . $baseSistema . '/api/rel/IsetPedido?empresa=' . $empresa;
if (empty($_SESSION['integradores_iset_automacao_csrf'])) $_SESSION['integradores_iset_automacao_csrf'] = bin2hex(random_bytes(24));
$mensagem = '';
$tipoMensagem = 'sucesso';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['integradores_iset_automacao_csrf'], isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        $mensagem = 'Não foi possível validar a solicitação.'; $tipoMensagem = 'erro';
    } else {
        $cliente = new ISET_CLIENTE();
        $resposta = isset($_POST['acao']) && $_POST['acao'] === 'remover'
            ? $cliente->removerWebhook('orders', 'change', $urlWebhook)
            : $cliente->criarWebhook('orders', 'change', $urlWebhook);
        $mensagem = $resposta['ok'] ? (isset($_POST['acao']) && $_POST['acao'] === 'remover' ? 'Solicitação de remoção enviada à ISET.' : 'Webhook de pedidos cadastrado na ISET.') : $resposta['mensagem'];
        $tipoMensagem = $resposta['ok'] ? 'sucesso' : 'erro';
    }
}
$resumoAutomacao = (new ISET_HISTORICO())->resumoAutomacao($empresa);
$logs = $resumoAutomacao['eventos'];
$ultimoWebhook = $resumoAutomacao['ultimoWebhook'];
$ultimoPedidoProcessado = $resumoAutomacao['ultimoPedidoProcessado'];
$ultimaImportacaoAutomatica = $resumoAutomacao['ultimaImportacaoAutomatica'];
$ultimoErro = $resumoAutomacao['ultimoErro'];
$ultimoPagamentoPendente = $resumoAutomacao['ultimoPagamentoPendente'];
$ultimoPedidoDuplicado = $resumoAutomacao['ultimoPedidoDuplicado'];
$formatarResumo = function ($log, $incluirMensagem = false) {
    if (!$log) {
        return 'Nenhum registro';
    }

    $texto = integradores_formatar_data_hora_log($log, 'Y-m-d H:i:s');
    if (!empty($log['INT001_Id_Externo'])) {
        $texto .= ' · Pedido ISET #' . $log['INT001_Id_Externo'];
    }
    if ($incluirMensagem && !empty($log['INT001_Mensagem'])) {
        $texto .= ' · ' . $log['INT001_Mensagem'];
    }

    return $texto;
};
integradores_header('ISET - Automação', 'automacao', 'iset');
?>
<style>.integradores-card.iset-automacao{max-width:900px;min-height:0;margin:0 0 20px}.iset-automacao-resumo{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.iset-automacao-resumo div{padding:14px;border-radius:10px;background:#f6f9fe}.iset-automacao-resumo small{display:block;color:#617495}.iset-automacao-acoes{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.iset-automacao-acoes .integradores-button{margin:0}.iset-automacao-url{display:block;overflow-wrap:anywhere;padding:12px;border:1px solid #cbd8ea;border-radius:9px;background:#f6f9fe;color:#07224d}.iset-automacao-lista{display:grid;gap:10px}.iset-automacao-log{display:flex;justify-content:space-between;gap:12px;padding:12px;border:1px solid #d6e0ef;border-radius:10px}.iset-automacao-log p{margin:3px 0 0;font-size:12px}.iset-automacao-status{grid-template-columns:repeat(2,minmax(0,1fr))}.iset-automacao-status .iset-automacao-status--url{grid-column:1/-1}.iset-automacao-status strong{overflow-wrap:anywhere}.iset-automacao-status--ok strong{color:#087e5b}@media(max-width:700px){.iset-automacao-resumo,.iset-automacao-status{grid-template-columns:1fr}.iset-automacao-status .iset-automacao-status--url{grid-column:auto}.iset-automacao-log{display:block}}</style>
<section class="integradores-card iset-automacao">
    <p class="integradores-eyebrow">Automação de pedidos</p>
    <h2>Automação ISET</h2>
    <p>O webhook consulta, valida e importa automaticamente os pedidos elegíveis da ISET.</p>
    <?php if ($mensagem): ?>
        <p class="integradores-flash integradores-flash--<?php echo integradores_h($tipoMensagem); ?>">
            <?php echo integradores_h($mensagem); ?>
        </p>
    <?php endif; ?>
</section>

<section class="integradores-card iset-automacao">
    <h3>Webhook</h3>
    <div class="iset-automacao-resumo iset-automacao-status">
        <div><small>Status</small><strong>Não verificável pela API</strong></div>
        <div><small>Evento configurado</small><strong>orders / change</strong></div>
        <div class="iset-automacao-status--url"><small>URL</small><strong><?php echo integradores_h($urlWebhook); ?></strong></div>
        <div><small>Último webhook recebido</small><strong><?php echo integradores_h($formatarResumo($ultimoWebhook)); ?></strong></div>
        <div><small>Último pedido processado</small><strong><?php echo integradores_h($formatarResumo($ultimoPedidoProcessado, true)); ?></strong></div>
        <div class="iset-automacao-status--ok"><small>Último pedido importado automaticamente</small><strong><?php echo integradores_h($formatarResumo($ultimaImportacaoAutomatica, true)); ?></strong></div>
        <div><small>Último erro</small><strong><?php echo integradores_h($formatarResumo($ultimoErro, true)); ?></strong></div>
        <div><small>Último pagamento pendente</small><strong><?php echo integradores_h($formatarResumo($ultimoPagamentoPendente, true)); ?></strong></div>
        <div><small>Último pedido ignorado por duplicidade</small><strong><?php echo integradores_h($formatarResumo($ultimoPedidoDuplicado, true)); ?></strong></div>
    </div>
    <p>A ISET documenta cadastro e remoção, mas não disponibiliza uma rota documentada para listar os webhooks existentes. Por isso, o status remoto não pode ser confirmado automaticamente.</p>
    <form method="post" class="iset-automacao-acoes">
        <input type="hidden" name="csrf_token" value="<?php echo integradores_h($_SESSION['integradores_iset_automacao_csrf']); ?>">
        <button class="integradores-button" name="acao" value="criar" type="submit">Criar webhook</button>
        <button class="integradores-button integradores-button--secundario" name="acao" value="remover" type="submit" onclick="return confirm('Remover o webhook de pedidos da ISET?')">Excluir webhook</button>
        <?php if ($ultimoWebhook): ?>
            <button class="integradores-button integradores-button--secundario" type="button" data-payload="<?php echo integradores_h($ultimoWebhook['INT001_Log']); ?>">Visualizar JSON</button>
        <?php endif; ?>
    </form>
</section>

<section class="integradores-card iset-automacao">
    <h3>Monitoramento</h3>
    <?php if (!$logs): ?>
        <p>Nenhum webhook ou resultado de importação automática foi registrado para esta empresa.</p>
    <?php else: ?>
        <div class="iset-automacao-lista">
            <?php foreach ($logs as $log): ?>
                <?php
                $dados = json_decode($log['INT001_Log'], true);
                $dados = is_array($dados) ? $dados : array();
                $requisicao = isset($dados['requisicao']) && is_array($dados['requisicao']) ? $dados['requisicao'] : array();
                $origem = $log['INT001_Operacao'] === INT_OPERACAO_IMPORTACAO_AUTOMATICA ? 'Importação automática' : 'Webhook';
                $descricao = integradores_formatar_data_hora_log($log, 'Y-m-d H:i:s')
                    . (!empty($log['INT001_Id_Externo']) ? ' · Pedido ISET #' . $log['INT001_Id_Externo'] : '')
                    . ' · ' . $log['INT001_Mensagem']
                    . (!empty($requisicao['ipRemoto']) ? ' · IP ' . $requisicao['ipRemoto'] : '');
                ?>
                <article class="iset-automacao-log">
                    <div>
                        <strong><?php echo integradores_h($origem . ' · ' . $log['INT001_Status'] . ' · ' . $log['INT001_Etapa']); ?></strong>
                        <p><?php echo integradores_h($descricao); ?></p>
                    </div>
                    <button class="integradores-button integradores-button--secundario" type="button" data-payload="<?php echo integradores_h($log['INT001_Log']); ?>">Ver detalhes</button>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<dialog class="integradores-log-dialog" id="iset-payload-dialog">
    <h3>Detalhes do evento</h3>
    <pre id="iset-payload-conteudo"></pre>
    <button class="integradores-button integradores-button--secundario" type="button" onclick="document.getElementById('iset-payload-dialog').close()">Fechar</button>
</dialog>
<script>
document.querySelectorAll('[data-payload]').forEach(function (botao) {
    botao.onclick = function () {
        var valor = this.dataset.payload || '{}';
        try {
            valor = JSON.stringify(JSON.parse(valor), null, 2);
        } catch (erro) {
        }
        document.getElementById('iset-payload-conteudo').textContent = valor;
        document.getElementById('iset-payload-dialog').showModal();
    };
});
</script>
<?php integradores_footer();
