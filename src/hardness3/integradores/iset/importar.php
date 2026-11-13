<?php
namespace hardness;

require_once dirname(__DIR__) . '/layout.php';
require_once dirname(dirname(__DIR__)) . '/importadores/importador_orcamento_pdf/inc/app.php';

importador_orcamento_pdf_bootstrap();
require_once dirname(dirname(__DIR__)) . '/bibliotecas/webservices/iset/iset.php';
integradores_exigir_permissao(INTEGRADORES_C029_ACESSAR_PAINEL, 'Seu perfil não possui permissão para importar pedidos pela integração ISET.');

if (session_id() === '') {
    session_start();
}
if (empty($_SESSION['integradores_iset_importar_csrf'])) {
    $_SESSION['integradores_iset_importar_csrf'] = bin2hex(random_bytes(32));
}

$pedido = null;
$validacao = null;
$mensagem = '';
$tipoMensagem = '';
$pedidoImportadoId = 0;
$resultadoImportacao = false;
$processamentoAliquotas = null;
$iset = new ISET();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['integradores_iset_importar_csrf'], isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '')) {
        $mensagem = 'Sua sessão expirou. Atualize a página e tente novamente.';
        $tipoMensagem = 'erro';
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'importar') {
        $pedido = isset($_SESSION['integradores_iset_preview']) ? $_SESSION['integradores_iset_preview'] : null;
        if (!$pedido) {
            $mensagem = 'A prévia expirou. Consulte o pedido novamente.';
            $tipoMensagem = 'erro';
            $resultadoImportacao = true;
        } else {
            try {
                $resultado = $iset->processarPedido($pedido['pedido']['id_externo']);
                unset($_SESSION['integradores_iset_preview']);
                $pedidoImportadoId = (int) $resultado['pedidoId'];
                $processamentoAliquotas = $resultado['processamento'];
                $pedido = null;
                $validacao = null;
                $mensagem = 'Pedido criado. Atualizando alíquotas dos itens antes da confirmação final.';
                $tipoMensagem = 'sucesso';
                $resultadoImportacao = true;
            } catch (\Throwable $e) {
                $mensagem = 'Não foi possível concluir a importação. ' . $e->getMessage();
                $tipoMensagem = 'erro';
                $resultadoImportacao = true;
            }
        }
    } else {
        $resposta = $iset->consultarPedidoParaImportacao(isset($_POST['pedido_id']) ? $_POST['pedido_id'] : '');
        if (!empty($resposta['ok'])) {
            $pedido = $resposta['pedido'];
            $_SESSION['integradores_iset_preview'] = $pedido;
            $validacao = $resposta['validacao'];
            $mensagem = 'Pedido consultado. Revise a prévia antes de confirmar a importação.';
            $tipoMensagem = 'sucesso';
        } else {
            $mensagem = isset($resposta['status']) && $resposta['status'] === 404 ? 'Pedido não encontrado na ISET.' : $resposta['mensagem'];
            $tipoMensagem = 'erro';
        }
    }
}

function integradores_iset_moeda($valor) { return 'R$ ' . number_format((float) $valor, 2, ',', '.'); }

integradores_header('ISET - Importar pedido', 'importar', 'iset');
?>
<style>
.iset-preview{max-width:1060px}.iset-preview .integradores-card{max-width:none;min-height:0;margin-bottom:18px}.iset-preview-resultados{display:grid;gap:26px;margin-top:26px}.iset-preview-resultados .integradores-card h3{margin-top:0}.iset-preview-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.iset-validacao{order:-1}.iset-validacao:not(.iset-validacao--bloqueada){border-top-color:#087858!important}.iset-validacao--bloqueada{border-top-color:#d92d4a!important}.iset-validacao .iset-dados .iset-validacao-ok{border:1px solid #b9e4d6;background:#ecfaf5}.iset-validacao .iset-dados .iset-validacao-ok small{color:#087858}.iset-validacao .iset-dados .iset-validacao-erro{border:1px solid #f3c5cc;background:#fff1f3}.iset-validacao .iset-dados .iset-validacao-erro small{color:#b4233c}.iset-consulta-form{display:flex!important;align-items:end;gap:12px}.iset-consulta-form label{width:200px;flex:0 0 200px}.iset-consulta-form .integradores-button,.iset-dialog-acoes .integradores-button{margin-top:0}.iset-dados{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:15px}.iset-dados div{padding:10px 12px;border-radius:9px;background:#f6f9fe}.iset-dados small{display:block;color:#617495}.iset-validacao .iset-dados .iset-validacao-ok,.iset-validacao .iset-dados .iset-validacao-erro{display:flex;align-items:center;gap:7px}.iset-validacao .iset-dados strong,.iset-validacao .iset-dados span:last-child{font-size:12px}.iset-validacao-icone{font-size:16px}.iset-table{width:100%;border-collapse:collapse;margin-top:14px}.iset-table th,.iset-table td{padding:10px;text-align:left;border-bottom:1px solid #d6e0ef}.iset-table th{font-size:11px;color:#617495;text-transform:uppercase}.iset-table tr.iset-item-nao-localizado{background:#fff1f3;cursor:help}.iset-table tr.iset-item-nao-localizado td{border-bottom-color:#f3c5cc}.iset-table tr.iset-item-nao-localizado:hover{background:#ffe3e8}.iset-table .numero{text-align:right}.iset-dialog-fundo{position:fixed;z-index:1000;inset:0;display:grid;place-items:center;padding:24px;background:rgba(7,34,77,.42)}.iset-dialog{width:min(440px,100%);padding:28px;border:1px solid #d6e0ef;border-top:4px solid #087858;border-radius:17px;background:#fff;box-shadow:0 22px 58px rgba(7,34,77,.24)}.iset-dialog--erro{border-top-color:#d92d4a}.iset-dialog h3{margin:0 0 10px}.iset-dialog p{margin:0;color:#617495}.iset-dialog-acoes{display:flex;gap:10px;align-items:center;margin-top:22px}.iset-dialog-fechar{padding:10px 14px;border:1px solid #cbd8ea;border-radius:9px;background:#fff;color:#07224d;font:inherit;font-weight:700;cursor:pointer}@media(max-width:700px){.iset-preview-grid,.iset-dados{grid-template-columns:1fr}.iset-consulta-form{display:grid!important}.iset-consulta-form label{width:auto;flex:auto}.iset-table{font-size:12px}}
</style>
<section class="integradores-card iset-preview">
    <p class="integradores-eyebrow">Consulta e importação</p><h2>Consultar pedido ISET</h2><p>Informe o ID numérico do pedido na ISET para conferir os dados antes da importação.</p>
    <?php if ($mensagem && !$resultadoImportacao): ?><p class="integradores-flash integradores-flash--<?php echo integradores_h($tipoMensagem); ?>"><?php echo integradores_h($mensagem); ?></p><?php endif; ?>
    <form method="post" class="integradores-formulario iset-consulta-form"><input type="hidden" name="csrf_token" value="<?php echo integradores_h($_SESSION['integradores_iset_importar_csrf']); ?>"><label>Número do pedido ISET<input name="pedido_id" type="number" min="1" required value="<?php echo integradores_h(isset($_POST['pedido_id']) ? $_POST['pedido_id'] : ''); ?>"></label><button class="integradores-button" type="submit">Consultar pedido</button></form>
</section>
<?php if ($pedido): ?>
<div class="iset-preview-resultados">
<?php if ($validacao): ?><section class="integradores-card iset-preview iset-validacao<?php echo $validacao['bloqueado'] ? ' iset-validacao--bloqueada' : ''; ?>"><h3>Validação para importação</h3><div class="iset-dados"><?php foreach ($validacao['itens'] as $resultado): ?><div class="iset-validacao-<?php echo $resultado['ok'] ? 'ok' : 'erro'; ?>"><span class="material-symbols-outlined iset-validacao-icone" aria-hidden="true"><?php echo $resultado['ok'] ? 'check_circle' : 'cancel'; ?></span><strong><?php echo integradores_h($resultado['grupo']); ?>:</strong><span><?php echo integradores_h($resultado['texto']); ?></span></div><?php endforeach; ?></div><?php if ($validacao['bloqueado']): ?><p class="integradores-flash integradores-flash--erro">Importação bloqueada até que as pendências sejam resolvidas.</p><?php else: ?><form method="post"><input type="hidden" name="csrf_token" value="<?php echo integradores_h($_SESSION['integradores_iset_importar_csrf']); ?>"><button class="integradores-button" type="submit" name="acao" value="importar"><?php echo !empty($validacao['itens_nao_localizados']) ? 'Importar teste' : 'Confirmar importação'; ?></button></form><?php endif; ?></section><?php endif; ?>
<div class="iset-preview-grid"><section class="integradores-card"><h3>Pedido</h3><div class="iset-dados"><div><small>Número</small><?php echo integradores_h($pedido['pedido']['id_externo']); ?></div><div><small>Status</small><?php echo integradores_h($pedido['pedido']['status']); ?></div><div><small>Data da compra</small><?php echo integradores_h($pedido['pedido']['data_compra']); ?></div><div><small>Total</small><?php echo integradores_h(integradores_iset_moeda($pedido['totais']['total'])); ?></div></div></section><section class="integradores-card"><h3>Cliente e entrega</h3><div class="iset-dados"><div><small>Cliente</small><?php echo integradores_h($pedido['cliente']['nome']); ?></div><div><small>CPF/CNPJ</small><?php echo integradores_h($pedido['cliente']['documento']); ?></div><div><small>E-mail / Telefone</small><?php echo integradores_h($pedido['cliente']['email'] . ' · ' . $pedido['cliente']['telefone']); ?></div><div><small>Endereço</small><?php echo integradores_h($pedido['endereco']['rua'] . ', ' . $pedido['endereco']['numero'] . ' — ' . $pedido['endereco']['bairro'] . ', ' . $pedido['endereco']['cidade'] . '/' . $pedido['endereco']['uf'] . ' — ' . $pedido['endereco']['cep']); ?></div></div></section></div>
<section class="integradores-card iset-preview"><h3>Itens</h3><table class="iset-table"><thead><tr><th>SKU / referência</th><th>Código ISET</th><th>Produto</th><th class="numero">Qtd.</th><th class="numero">Unitário</th><th class="numero">Total</th></tr></thead><tbody><?php foreach ($pedido['itens'] as $indice => $item): $itemNaoLocalizado = !empty($validacao['itens_nao_localizados'][$indice]); ?><tr<?php echo $itemNaoLocalizado ? ' class="iset-item-nao-localizado" title="Produto não encontrado no sistema."' : ''; ?>><td><?php echo integradores_h($item['codigo_referencia']); ?></td><td><?php echo integradores_h($item['id_externo']); ?></td><td><?php echo integradores_h($item['nome']); ?></td><td class="numero"><?php echo integradores_h($item['quantidade']); ?></td><td class="numero"><?php echo integradores_h(integradores_iset_moeda($item['valor_unitario'])); ?></td><td class="numero"><?php echo integradores_h(integradores_iset_moeda($item['valor_total'])); ?></td></tr><?php endforeach; ?></tbody></table><div class="iset-dados"><div><small>Frete</small><?php echo integradores_h($pedido['totais']['frete_titulo'] . ' · ' . integradores_iset_moeda($pedido['totais']['frete'])); ?></div><div><small>Desconto</small><?php echo integradores_h(integradores_iset_moeda($pedido['totais']['desconto'])); ?></div><div><small>Pagamento</small><?php echo integradores_h($pedido['pagamento']['titulo'] ?: $pedido['pagamento']['metodo']); ?></div><div><small>Parcelas</small><?php echo integradores_h($pedido['pagamento']['parcelas']); ?></div></div></section>
</div>
<?php endif; ?>
<?php if ($resultadoImportacao): ?><div class="iset-dialog-fundo" role="dialog" aria-modal="true" aria-labelledby="iset-dialog-titulo"><section class="iset-dialog<?php echo $tipoMensagem === 'erro' ? ' iset-dialog--erro' : ''; ?>"><h3 id="iset-dialog-titulo"><?php echo $tipoMensagem === 'sucesso' ? 'Finalizando pedido' : 'Não foi possível importar o pedido'; ?></h3><p id="iset-importacao-mensagem"><?php echo integradores_h($mensagem); ?></p><p id="iset-importacao-progresso"<?php echo $processamentoAliquotas ? '' : ' hidden'; ?>></p><div class="iset-dialog-acoes" id="iset-importacao-acoes"><?php if (!$processamentoAliquotas && $pedidoImportadoId): ?><a class="integradores-button" href="/erp" onclick="return integradoresAbrirPedidoNoSistema(event, <?php echo (int) $pedidoImportadoId; ?>)"><span class="material-symbols-outlined" aria-hidden="true" style="margin-right:6px;vertical-align:-3px;font-size:17px">open_in_new</span>Abrir pedido no sistema</a><?php endif; ?><button class="iset-dialog-fechar" type="button" onclick="this.closest('.iset-dialog-fundo').remove()">Fechar</button></div></section></div><?php endif; ?>
<?php if ($processamentoAliquotas): ?><script>
(function () {
    var processamento = <?php echo json_encode($processamentoAliquotas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    var pedidoId = <?php echo (int) $pedidoImportadoId; ?>;
    var csrf = <?php echo json_encode($_SESSION['integradores_iset_importar_csrf']); ?>;
    var titulo = document.getElementById('iset-dialog-titulo');
    var mensagem = document.getElementById('iset-importacao-mensagem');
    var progresso = document.getElementById('iset-importacao-progresso');
    var acoes = document.getElementById('iset-importacao-acoes');

    function concluir(resultado) {
        var comFalhas = resultado.falhas > 0;
        titulo.textContent = comFalhas ? 'Pedido importado com aviso' : 'Pedido importado';
        mensagem.textContent = comFalhas
            ? 'Pedido ' + pedidoId + ' importado, mas ' + resultado.falhas + ' item(ns) precisam de revisão de alíquotas.'
            : 'Pedido importado com sucesso. Número do pedido: ' + pedidoId + '.';
        progresso.textContent = 'Itens atualizados: ' + resultado.sucessos + ' de ' + resultado.total + '.';
        if (comFalhas && resultado.erros) {
            progresso.textContent += ' Consulte os logs da integração para os detalhes.';
        }
        var abrir = document.createElement('a');
        abrir.className = 'integradores-button';
        abrir.href = '/erp';
        abrir.innerHTML = '<span class="material-symbols-outlined" aria-hidden="true" style="margin-right:6px;vertical-align:-3px;font-size:17px">open_in_new</span>Abrir pedido no sistema';
        abrir.onclick = function (evento) { return integradoresAbrirPedidoNoSistema(evento, pedidoId); };
        acoes.insertBefore(abrir, acoes.firstChild);
    }

    function liberarAberturaDoPedido() {
        if (acoes.querySelector('a.integradores-button')) {
            return;
        }
        var abrir = document.createElement('a');
        abrir.className = 'integradores-button';
        abrir.href = '/erp';
        abrir.innerHTML = '<span class="material-symbols-outlined" aria-hidden="true" style="margin-right:6px;vertical-align:-3px;font-size:17px">open_in_new</span>Abrir pedido no sistema';
        abrir.onclick = function (evento) { return integradoresAbrirPedidoNoSistema(evento, pedidoId); };
        acoes.insertBefore(abrir, acoes.firstChild);
    }

    function avancar() {
        var dados = new URLSearchParams();
        dados.append('csrf_token', csrf);
        dados.append('processamento_id', processamento.id);
        dados.append('pedido_id', pedidoId);
        fetch(<?php echo json_encode(integradores_base_path('iset/importar_aliquotas.php')); ?>, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
            body: dados.toString(),
            credentials: 'same-origin'
        }).then(function (resposta) {
            return resposta.json();
        }).then(function (resposta) {
            if (!resposta.ok) {
                throw new Error(resposta.mensagem || 'Não foi possível atualizar as alíquotas.');
            }
            var resultado = resposta.resultado;
            progresso.textContent = 'Atualizando alíquotas: ' + resultado.processados + ' de ' + resultado.total + ' itens (' + resultado.percentual + '%).';
            if (resultado.ocupado) {
                window.setTimeout(avancar, 350);
            } else if (resultado.concluido) {
                concluir(resultado);
            } else {
                window.setTimeout(avancar, 0);
            }
        }).catch(function (erro) {
            titulo.textContent = 'Pedido importado com aviso';
            mensagem.textContent = 'O pedido ' + pedidoId + ' foi gravado, mas a atualização de alíquotas não pôde continuar: ' + erro.message;
            progresso.textContent = 'Consulte os logs da integração antes de utilizar o pedido.';
            liberarAberturaDoPedido();
        });
    }

    avancar();
}());
</script><?php endif; ?>
<?php integradores_footer(); ?>
