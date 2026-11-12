<?php
namespace hardness;

require_once dirname(__DIR__) . '/layout.php';
require_once dirname(dirname(__DIR__)) . '/importadores/importador_orcamento_pdf/inc/app.php';

importador_orcamento_pdf_bootstrap();
require_once dirname(dirname(__DIR__)) . '/bibliotecas/webservices/iset/iset.php';
integradores_exigir_permissao(INTEGRADORES_C029_ACESSAR_PAINEL, 'Seu perfil não possui permissão para acessar a integração ISET.');
integradores_exigir_permissao(INTEGRADORES_C029_CONFIGURACOES, 'Seu perfil não possui permissão para acessar as configurações das integrações.');

if (empty($_SESSION['integradores_iset_csrf'])) {
    $_SESSION['integradores_iset_csrf'] = bin2hex(random_bytes(32));
}

$configuracoes = new ISET_CONFIGURACOES();
$configuracao = $configuracoes->obter();
$mensagem = '';
$tipoMensagem = '';
$mensagemMapeamentoStatus = '';
$tipoMensagemMapeamentoStatus = '';
$jsonMapeamentoStatus = isset($configuracao['IsetStatusPedidosMapeamentoJson']) ? trim((string) $configuracao['IsetStatusPedidosMapeamentoJson']) : '';
if ($jsonMapeamentoStatus === '') {
    $jsonMapeamentoStatus = '[]';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
    if (!hash_equals($_SESSION['integradores_iset_csrf'], $csrf)) {
        $mensagem = 'Sua sessão expirou. Atualize a página e tente novamente.';
        $tipoMensagem = 'erro';
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'salvar') {
        $chaveAcesso = isset($_POST['chave_acesso']) ? (string) $_POST['chave_acesso'] : '';
        if ($chaveAcesso === '' && !empty($configuracao['IsetChaveAcesso'])) {
            $chaveAcesso = $configuracao['IsetChaveAcesso'];
        }
        list($ok, $mensagem) = $configuracoes->salvar(
            isset($_POST['url_base']) ? $_POST['url_base'] : '',
            isset($_POST['identificador']) ? $_POST['identificador'] : '',
            $chaveAcesso
            , isset($_POST['status_importacao']) ? $_POST['status_importacao'] : ''
            , isset($_POST['transportadora_id']) ? $_POST['transportadora_id'] : ''
            , isset($_POST['portador_pagamento_id']) ? $_POST['portador_pagamento_id'] : '', isset($_POST['modo_teste']) ? 'S' : 'N'
        );
        $tipoMensagem = $ok ? 'sucesso' : 'erro';
        $configuracao = $configuracoes->obter();
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'testar') {
        list($ok, $mensagem) = $configuracoes->testar($configuracao);
        $tipoMensagem = $ok ? 'sucesso' : 'erro';
    } elseif (isset($_POST['acao']) && in_array($_POST['acao'], array('validar_mapeamento_status', 'salvar_mapeamento_status', 'restaurar_mapeamento_status'), true)) {
        $acao = $_POST['acao'];
        $jsonMapeamentoStatus = $acao === 'restaurar_mapeamento_status'
            ? ISET_CONFIGURACOES::jsonStatusPedidosPadrao()
            : (isset($_POST['mapeamento_status_json']) ? (string) $_POST['mapeamento_status_json'] : '');
        $validacao = ISET_CONFIGURACOES::validarJsonStatusPedidos($jsonMapeamentoStatus);
        if ($acao === 'validar_mapeamento_status') {
            $mensagemMapeamentoStatus = $validacao['ok'] ? 'JSON válido e mapeamento aprovado.' : implode(' ', $validacao['erros']);
            $tipoMensagemMapeamentoStatus = $validacao['ok'] ? 'sucesso' : 'erro';
        } else {
            list($ok, $mensagemMapeamentoStatus) = $configuracoes->salvarStatusPedidosMapeamento($jsonMapeamentoStatus);
            $tipoMensagemMapeamentoStatus = $ok ? 'sucesso' : 'erro';
            if ($ok) {
                $configuracao = $configuracoes->obter();
                $jsonMapeamentoStatus = $configuracao['IsetStatusPedidosMapeamentoJson'];
            }
        }
    }
}

$codigoCronStatus = <<<'PHP'
<?php
// $g, $array['{parametro}'], executarCodigo(id, vars[])

// A tarefa é executada uma vez para cada empresa retornada pela consulta SQL.
$g['empresaAtual'] = (int) $array['todosCampos']['C004_Id'];
$g['usuarioAtual'] = 63;

require_once('bibliotecas/classes/INDEX.php');
$INDEX = new INDEX();
$INDEX->carragaVARSGlobal();

require_once('bibliotecas/composer/vendor/autoload.php');
require_once('bibliotecas/webservices/iset/iset_status_pedidos.php');

$ISET_STATUS_PEDIDOS = new ISET_STATUS_PEDIDOS();
$resumo = $ISET_STATUS_PEDIDOS->sincronizarPendentesAutomaticos($g['empresaAtual']);

return json_encode($resumo);
PHP;

integradores_header('ISET - Configurações', 'configuracoes', 'iset');
?>
<style>.integradores-configuracoes{max-width:1040px}.iset-switch-campo{gap:8px;padding:12px 14px;border:1px solid #c8d8fa;border-radius:10px;background:#edf3ff}.iset-switch-linha{display:flex;align-items:center;justify-content:space-between;gap:16px}.iset-switch{cursor:pointer;position:relative;display:inline-flex;width:48px;height:26px;flex:0 0 48px}.iset-switch input{position:absolute;width:1px!important;height:1px!important;opacity:0}.iset-switch-slider{width:100%;height:100%;border-radius:999px;background:#cbd8ea;box-shadow:inset 0 0 0 1px #b9c7dc;transition:.2s}.iset-switch-slider:before{position:absolute;top:3px;left:3px;width:20px;height:20px;border-radius:50%;background:#fff;box-shadow:0 2px 5px rgba(7,34,77,.25);content:'';transition:.2s}.iset-switch input:checked + .iset-switch-slider{background:#2860dc}.iset-switch input:checked + .iset-switch-slider:before{transform:translateX(22px)}.iset-switch input:focus-visible + .iset-switch-slider{outline:3px solid rgba(40,96,220,.2);outline-offset:2px}</style>
<section class="integradores-card integradores-configuracoes">
    <p class="integradores-eyebrow">Autenticação da API</p>
    <h2>Configurações da ISET</h2>
    <p>Informe os dados fornecidos no painel da sua loja ISET. Eles são usados exclusivamente para obter o token de acesso da API.</p>

    <?php if ($mensagem !== ''): ?>
        <p class="integradores-flash integradores-flash--<?php echo integradores_h($tipoMensagem); ?>"><?php echo integradores_h($mensagem); ?></p>
    <?php endif; ?>

    <form method="post" class="integradores-formulario">
        <input type="hidden" name="csrf_token" value="<?php echo integradores_h($_SESSION['integradores_iset_csrf']); ?>">
        <label>Domínio da loja ISET
            <input name="url_base" type="url" required placeholder="https://www.sualoja.com.br" value="<?php echo integradores_h(isset($configuracao['IsetUrlBase']) ? $configuracao['IsetUrlBase'] : ''); ?>">
            <small>Usado para formar a URL da API: domínio + <code>/ws/v1</code>.</small>
        </label>
        <label>Identificador da API
            <input name="identificador" type="text" required autocomplete="username" value="<?php echo integradores_h(isset($configuracao['IsetIdentificador']) ? $configuracao['IsetIdentificador'] : ''); ?>">
            <small>É o ID de usuário exigido pela autenticação básica da ISET.</small>
        </label>
        <label>Chave de acesso da API
            <span class="integradores-campo-senha">
                <input id="iset-chave-acesso" name="chave_acesso" type="password" autocomplete="new-password" value="<?php echo integradores_h(isset($configuracao['IsetChaveAcesso']) ? $configuracao['IsetChaveAcesso'] : ''); ?>">
                <button type="button" aria-controls="iset-chave-acesso" aria-label="Mostrar chave de acesso" title="Mostrar chave de acesso" onclick="var campo=document.getElementById('iset-chave-acesso'); var visivel=campo.type==='text'; campo.type=visivel?'password':'text'; this.setAttribute('aria-label',visivel?'Mostrar chave de acesso':'Ocultar chave de acesso'); this.title=visivel?'Mostrar chave de acesso':'Ocultar chave de acesso'; this.textContent=visivel?'Mostrar':'Ocultar';">Mostrar</button>
            </span>
            <small>É o segredo usado junto ao identificador. Use “Mostrar” para visualizar a chave salva.</small>
        </label>
        <label>Status de pedido permitidos para importação
            <input name="status_importacao" type="text" placeholder="Ex.: 1, 5, 17" value="<?php echo integradores_h(isset($configuracao['IsetStatusImportacao']) ? $configuracao['IsetStatusImportacao'] : ''); ?>">
            <small>Informe os IDs de status da ISET separados por vírgula. Com este campo vazio, nenhum status será autorizado para importação.</small>
        </label>
        <label class="iset-switch-campo"><span class="iset-switch-linha"><span>Ativar modo de teste de importação</span><span class="iset-switch"><input name="modo_teste" type="checkbox" value="S"<?php echo !empty($configuracao['IsetModoTesteImportacao']) && $configuracao['IsetModoTesteImportacao'] === 'S' ? ' checked' : ''; ?>><span class="iset-switch-slider"></span></span></span><small>Itens não localizados usarão o produto cujo nome contém “teste hardness”.</small></label>
        <label>Transportadora padrão da ISET
            <input name="transportadora_id" type="number" min="1" placeholder="ID da transportadora no Hardness" value="<?php echo integradores_h(isset($configuracao['IsetD022IdTransportadora']) ? $configuracao['IsetD022IdTransportadora'] : ''); ?>">
            <small>Obrigatória apenas quando o pedido ISET possui frete. Informe o <code>D022_Id</code> que representará esse frete no ERP.</small>
        </label>
        <label>Portador padrão dos pagamentos ISET
            <input name="portador_pagamento_id" type="number" min="1" placeholder="ID do portador no Hardness" value="<?php echo integradores_h(isset($configuracao['IsetD027IdPagamento']) ? $configuracao['IsetD027IdPagamento'] : ''); ?>">
            <small>Informe o <code>D027_Id</code> usado quando a forma de pagamento da ISET não estiver mapeada em <code>D027_Descricao_Ecommerce</code>.</small>
        </label>
        <div class="integradores-acoes">
            <button class="integradores-button" type="submit" name="acao" value="salvar">Salvar configurações</button>
            <button class="integradores-button integradores-button--secundario" type="submit" name="acao" value="testar">Testar conexão</button>
        </div>
    </form>
</section>

<style>.iset-status-mapeamento{max-width:1040px;min-height:0;margin-top:18px}.iset-status-mapeamento-json{width:100%;min-height:250px;padding:13px;border:1px solid #cbd8ea;border-radius:9px;color:#07224d;background:#f8fbff;font:12px/1.45 monospace;resize:vertical}.iset-status-mapeamento-json:focus{outline:0;border-color:#2860dc;box-shadow:0 0 0 3px rgba(40,96,220,.1)}.iset-status-mapeamento-acoes{display:flex;flex-wrap:wrap;gap:9px}.iset-status-mapeamento-acoes .integradores-button{margin:0}</style>
<section class="integradores-card iset-status-mapeamento">
    <p class="integradores-eyebrow">Por empresa</p>
    <h2>Mapeamento de status dos pedidos</h2>
    <p>Defina quais status do Hardness serão enviados para a ISET. O worker usa o mapa salvo no próximo processamento da fila.</p>
    <?php if ($mensagemMapeamentoStatus !== ''): ?>
        <p class="integradores-flash integradores-flash--<?php echo integradores_h($tipoMensagemMapeamentoStatus); ?>"><?php echo integradores_h($mensagemMapeamentoStatus); ?></p>
    <?php endif; ?>
    <form method="post" class="integradores-formulario">
        <input type="hidden" name="csrf_token" value="<?php echo integradores_h($_SESSION['integradores_iset_csrf']); ?>">
        <label>Configuração JSON
            <textarea class="iset-status-mapeamento-json" name="mapeamento_status_json" spellcheck="false"><?php echo integradores_h($jsonMapeamentoStatus); ?></textarea>
            <small>Use uma lista de objetos com <code>statusHardness</code> e <code>statusISET</code>. Salve <code>[]</code> para desativar o envio de status desta empresa.</small>
        </label>
        <div class="iset-status-mapeamento-acoes">
            <button class="integradores-button" type="submit" name="acao" value="salvar_mapeamento_status">Salvar mapeamento</button>
            <button class="integradores-button integradores-button--secundario" type="submit" name="acao" value="validar_mapeamento_status">Validar JSON</button>
            <button class="integradores-button integradores-button--secundario" type="submit" name="acao" value="restaurar_mapeamento_status">Restaurar mapeamento confirmado</button>
        </div>
    </form>
</section>

<style>.iset-status-crontab{max-width:1040px;min-height:0;margin-top:18px}.iset-status-crontab-instrucoes{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:16px 0}.iset-status-crontab-instrucoes div{padding:11px 13px;border:1px solid #cbd8ea;border-radius:9px;background:#f8fbff}.iset-status-crontab-instrucoes strong,.iset-status-crontab-instrucoes span{display:block}.iset-status-crontab-instrucoes span{margin-top:4px;color:#385987}.iset-status-crontab-codigo{margin:8px 0 0;padding:14px;border-radius:9px;background:#f3f7fc;white-space:pre-wrap;overflow-wrap:anywhere;font:12px/1.5 Consolas,'Courier New',monospace;color:#1e1e1e}.iset-status-crontab-cabecalho{display:flex;align-items:center;justify-content:space-between;gap:16px}.iset-status-crontab-cabecalho h2{margin:0}@media(max-width:700px){.iset-status-crontab-instrucoes{grid-template-columns:1fr}}</style>
<section class="integradores-card iset-status-crontab">
    <p class="integradores-eyebrow">Automação de status</p>
    <div class="iset-status-crontab-cabecalho"><h2>Crontab de atualização de pedidos</h2><button class="integradores-button integradores-button--secundario" id="iset-status-crontab-copiar" type="button">Copiar código</button></div>
    <p>Cadastre a tarefa abaixo para processar a fila <code>INT008</code>. A atualização remota ocorre fora da trigger de pedido e os IDs enviados são definidos pelo mapeamento configurado acima.</p>
    <div class="iset-status-crontab-instrucoes"><div><strong>Intervalo</strong><span><code>MINUTO</code>, a cada <code>10</code> minutos</span></div><div><strong>Consulta SQL</strong><span><code>SELECT C004_Id FROM C004 WHERE C004_Flag_Ativo = 'S'</code></span></div></div>
    <h3>Código da tarefa</h3>
    <pre class="iset-status-crontab-codigo" id="iset-status-crontab-codigo"><?php echo integradores_h($codigoCronStatus); ?></pre>
</section>
<script>(function(){var botao=document.getElementById('iset-status-crontab-copiar'),codigo=document.getElementById('iset-status-crontab-codigo');if(!botao||!codigo)return;botao.addEventListener('click',function(){var texto=codigo.textContent||'',original=botao.textContent,copiar=function(){var campo=document.createElement('textarea');campo.value=texto;campo.style.position='fixed';campo.style.opacity='0';document.body.appendChild(campo);campo.select();var ok=document.execCommand('copy');document.body.removeChild(campo);if(!ok)throw new Error();};try{if(navigator.clipboard&&window.isSecureContext){navigator.clipboard.writeText(texto).then(function(){botao.textContent='Copiado';setTimeout(function(){botao.textContent=original;},1800);}).catch(function(){copiar();botao.textContent='Copiado';setTimeout(function(){botao.textContent=original;},1800);});}else{copiar();botao.textContent='Copiado';setTimeout(function(){botao.textContent=original;},1800);}}catch(erro){botao.textContent='Não foi possível copiar';setTimeout(function(){botao.textContent=original;},1800);}});}());</script>
<?php integradores_footer(); ?>
