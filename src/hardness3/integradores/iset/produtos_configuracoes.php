<?php
namespace hardness;
require_once dirname(__DIR__) . '/layout.php';
require_once dirname(dirname(__DIR__)) . '/importadores/importador_orcamento_pdf/inc/app.php';
importador_orcamento_pdf_bootstrap();
require_once dirname(dirname(__DIR__)) . '/bibliotecas/webservices/iset/iset.php';
integradores_exigir_permissao(INTEGRADORES_C029_ACESSAR_PAINEL, 'Seu perfil não possui permissão para acessar a integração ISET.');
integradores_exigir_permissao(INTEGRADORES_C029_CONFIGURACOES, 'Seu perfil não possui permissão para alterar as configurações de produtos ISET.');
if (session_id() === '') session_start();
if (empty($_SESSION['integradores_iset_produtos_config_csrf'])) $_SESSION['integradores_iset_produtos_config_csrf'] = bin2hex(random_bytes(32));
$servico = new ISET_CONFIGURACOES(); $atual = $servico->obterProdutosConfiguracao();
$json = $atual['ok'] ? $atual['json'] : ISET_CONFIGURACOES::jsonProdutosPadrao(); $permitirCompraPorEstoque = $servico->permitirCompraPorEstoqueAtivo(); $mensagem = ''; $tipoMensagem = ''; $acao = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['integradores_iset_produtos_config_csrf'], isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '')) { $mensagem = 'Sua sessão expirou. Atualize a página e tente novamente.'; $tipoMensagem = 'erro'; }
    else {
        $acao = isset($_POST['acao']) ? $_POST['acao'] : '';
        if ($acao === 'salvar_permitir_compra_por_estoque') {
            list($ok, $mensagem) = $servico->salvarPermitirCompraPorEstoque(isset($_POST['permitir_compra_por_estoque']) ? 'S' : 'N');
            $tipoMensagem = $ok ? 'sucesso' : 'erro'; $permitirCompraPorEstoque = $servico->permitirCompraPorEstoqueAtivo();
        } else {
            $json = $acao === 'restaurar' ? ISET_CONFIGURACOES::jsonProdutosPadrao() : (isset($_POST['configuracao_json']) ? (string) $_POST['configuracao_json'] : '');
            $validacao = ISET_CONFIGURACOES::validarJsonProdutos($json);
            if ($acao === 'validar') { $mensagem = $validacao['ok'] ? 'JSON válido e schema aprovado.' : implode(' ', $validacao['erros']); $tipoMensagem = $validacao['ok'] ? 'sucesso' : 'erro'; }
            elseif ($acao === 'salvar' || $acao === 'restaurar') { list($ok, $mensagem) = $servico->salvarProdutosConfiguracao($json); $tipoMensagem = $ok ? 'sucesso' : 'erro'; if ($ok) $json = $servico->obterProdutosConfiguracao()['json']; }
        }
    }
}
integradores_header('ISET - Configurações de produtos', 'produtos', 'iset');
?>
<style>.integradores-card.iset-produtos-config{max-width:1040px;min-height:0;margin-bottom:18px}.iset-produtos-json{width:100%;min-height:480px;padding:13px;border:1px solid #cbd8ea;border-radius:9px;color:#07224d;background:#f8fbff;font:12px/1.45 monospace;resize:vertical}.iset-produtos-json:focus{outline:0;border-color:#2860dc;box-shadow:0 0 0 3px rgba(40,96,220,.1)}.iset-produtos-config-acoes{display:flex;flex-wrap:wrap;gap:9px}.iset-produtos-config-acoes .integradores-button{margin:0}.iset-produtos-exemplo{max-height:330px;overflow:auto;padding:14px;border-radius:9px;background:#f3f7fc;white-space:pre-wrap}.iset-produtos-exemplo-cabecalho{display:flex;align-items:center;justify-content:space-between;gap:16px}.iset-produtos-exemplo-cabecalho h3{margin-top:0}.iset-produtos-exemplo-copiar{margin:0}.iset-produtos-switch-campo{gap:8px;padding:12px 14px;border:1px solid #c8d8fa;border-radius:10px;background:#edf3ff}.iset-produtos-switch-linha{display:flex;align-items:center;justify-content:space-between;gap:16px}.iset-produtos-switch{cursor:pointer;position:relative;display:inline-flex;width:48px;height:26px;flex:0 0 48px}.iset-produtos-switch input{position:absolute;width:1px;height:1px;opacity:0}.iset-produtos-switch-slider{width:100%;height:100%;border-radius:999px;background:#cbd8ea;box-shadow:inset 0 0 0 1px #b9c7dc;transition:.2s}.iset-produtos-switch-slider:before{position:absolute;top:3px;left:3px;width:20px;height:20px;border-radius:50%;background:#fff;box-shadow:0 2px 5px rgba(7,34,77,.25);content:'';transition:.2s}.iset-produtos-switch input:checked + .iset-produtos-switch-slider{background:#2860dc}.iset-produtos-switch input:checked + .iset-produtos-switch-slider:before{transform:translateX(22px)}.iset-produtos-switch input:focus-visible + .iset-produtos-switch-slider{outline:3px solid rgba(40,96,220,.2);outline-offset:2px}.iset-produtos-crontab-instrucoes{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:16px 0}.iset-produtos-crontab-instrucoes div{padding:11px 13px;border:1px solid #cbd8ea;border-radius:9px;background:#f8fbff}.iset-produtos-crontab-instrucoes strong,.iset-produtos-crontab-instrucoes span{display:block}.iset-produtos-crontab-instrucoes span{margin-top:4px;color:#385987}.iset-produtos-crontab-codigo{margin:8px 0 16px;padding:14px;border-radius:9px;background:#f3f7fc;white-space:pre-wrap;overflow-wrap:anywhere;font:12px/1.5 Consolas,'Courier New',monospace;color:#1e1e1e}.iset-produtos-crontab-codigo .comentario{color:#6a9955}.iset-produtos-crontab-codigo .palavra{color:#0000ff}.iset-produtos-crontab-codigo .variavel{color:#001080}.iset-produtos-crontab-codigo .texto{color:#a31515}.iset-produtos-crontab-codigo .funcao{color:#795e26}.iset-produtos-crontab-codigo .numero{color:#098658}.iset-produtos-crontab-codigo .classe{color:#267f99}@media(max-width:700px){.iset-produtos-crontab-instrucoes{grid-template-columns:1fr}}</style>
<?php integradores_iset_produtos_abas('configuracoes'); ?>
<section class="integradores-card iset-produtos-config"><p class="integradores-eyebrow">Disponibilidade de compra</p><h2>Permitir compra pelo estoque</h2><p><b style="color: green">Ativado:</b> Permite que o cliente compre somente se o <code>estoque</code> for maior que zero.    <b style="color: red">Desativado:</b> mantém a compra permitida mesmo sem estoque.</p><?php if($mensagem && $acao === 'salvar_permitir_compra_por_estoque'): ?><p class="integradores-flash integradores-flash--<?php echo integradores_h($tipoMensagem); ?>"><?php echo integradores_h($mensagem); ?></p><?php endif; ?><form method="post" class="integradores-formulario"><input type="hidden" name="csrf_token" value="<?php echo integradores_h($_SESSION['integradores_iset_produtos_config_csrf']); ?>"><label class="iset-produtos-switch-campo"><span class="iset-produtos-switch-linha"><span>Permitir compra no site atrelado ao estoque</span><span class="iset-produtos-switch"><input name="permitir_compra_por_estoque" type="checkbox" value="S"<?php echo $permitirCompraPorEstoque ? ' checked' : ''; ?>><span class="iset-produtos-switch-slider"></span></span></span></label><div class="iset-produtos-config-acoes"><button class="integradores-button" type="submit" name="acao" value="salvar_permitir_compra_por_estoque">Salvar opção</button></div></form></section>
<section class="integradores-card iset-produtos-config"><p class="integradores-eyebrow">Por empresa</p><h2>Configurações de Produtos</h2><p>O JSON é salvo em <code>C031.IsetProdutosConfiguracaoJson</code> da empresa atual. Os grupos são achatados para formar a grade e o payload.</p><?php if($mensagem && $acao !== 'salvar_permitir_compra_por_estoque'): ?><p class="integradores-flash integradores-flash--<?php echo integradores_h($tipoMensagem); ?>"><?php echo integradores_h($mensagem); ?></p><?php endif; ?><form method="post" class="integradores-formulario"><input type="hidden" name="csrf_token" value="<?php echo integradores_h($_SESSION['integradores_iset_produtos_config_csrf']); ?>"><label>Configuração JSON<textarea class="iset-produtos-json" name="configuracao_json" spellcheck="false"><?php echo integradores_h($json); ?></textarea><small>Origens permitidas: <?php echo integradores_h(implode(', ', ISET_CONFIGURACOES::origensProdutosPermitidas())); ?>. Os payloads name, price, quantity e status precisam ser enviados e obrigatórios.</small></label><div class="iset-produtos-config-acoes"><button class="integradores-button" type="submit" name="acao" value="salvar">Salvar</button><button class="integradores-button integradores-button--secundario" type="submit" name="acao" value="validar">Validar JSON</button><button class="integradores-button integradores-button--secundario" type="submit" name="acao" value="restaurar">Restaurar padrão</button></div></form></section>
<section class="integradores-card iset-produtos-config"><p class="integradores-eyebrow">Automação</p><div class="iset-produtos-exemplo-cabecalho"><h2>Crontab de sincronização automática</h2><button class="integradores-button integradores-button--secundario iset-produtos-exemplo-copiar" id="iset-produtos-crontab-copiar" type="button">Copiar código</button></div><p>Para sincronizar as pendências da <code>INT006</code>, o desenvolvedor deve cadastrar uma tarefa de crontab com o código abaixo. A tarefa é executada uma vez para cada empresa retornada pela consulta SQL.</p><div class="iset-produtos-crontab-instrucoes"><div><strong>Intervalo</strong><span><code>MINUTO</code>, a cada <code>5</code> minutos</span></div><div><strong>Consulta SQL</strong><span><code>SELECT C004_Id FROM C004 WHERE C004_Flag_Ativo = 'S'</code></span></div></div><h3>Código da tarefa</h3><pre class="iset-produtos-crontab-codigo" id="iset-produtos-crontab-codigo"><span class="palavra">&lt;?php</span>
<span class="comentario">// $g, $array['{parametro}'], executarCodigo(id, vars[])</span>

<span class="comentario">// Carrega globais e classes utilizadas</span>
<span class="variavel">$g</span>[<span class="texto">'empresaAtual'</span>] = (<span class="palavra">int</span>) <span class="variavel">$array</span>[<span class="texto">'todosCampos'</span>][<span class="texto">'C004_Id'</span>];
<span class="variavel">$g</span>[<span class="texto">'usuarioAtual'</span>] = <span class="numero">63</span>;

<span class="palavra">require_once</span>(<span class="texto">'bibliotecas/classes/INDEX.php'</span>);
<span class="variavel">$INDEX</span> = <span class="palavra">new</span> <span class="classe">INDEX</span>();
<span class="variavel">$INDEX</span>-&gt;<span class="funcao">carragaVARSGlobal</span>();

<span class="palavra">require_once</span>(<span class="texto">'bibliotecas/composer/vendor/autoload.php'</span>);
<span class="palavra">require_once</span>(<span class="texto">'bibliotecas/webservices/iset/iset_produtos.php'</span>);

<span class="variavel">$ISET_PRODUTOS</span> = <span class="palavra">new</span> <span class="classe">ISET_PRODUTOS</span>();
<span class="variavel">$resumo</span> = <span class="variavel">$ISET_PRODUTOS</span>-&gt;<span class="funcao">sincronizarPendentesAutomaticos</span>(<span class="variavel">$g</span>[<span class="texto">'empresaAtual'</span>]);

<span class="palavra">return</span> <span class="funcao">json_encode</span>(<span class="variavel">$resumo</span>);</pre></section>
<section class="integradores-card iset-produtos-config"><p class="integradores-eyebrow">Integração no ERP</p><h2>Métodos da classe CAD002</h2><p>Adicione os dois métodos abaixo na classe <code>CAD002</code>. Eles acumulam os produtos reprocessados e registram a pendência local para a sincronização automática.</p><div class="iset-produtos-exemplo-cabecalho"><h3>triggerFimLoopReprocessa</h3><button class="integradores-button integradores-button--secundario iset-produtos-exemplo-copiar" id="iset-produtos-cad002-fim-copiar" type="button">Copiar código</button></div><pre class="iset-produtos-crontab-codigo" id="iset-produtos-cad002-fim-codigo"><span class="comentario">/**
 * Acumula o D009 já reprocessado para a integração ISET.
 *
 * A pendência permanece apenas em memória até o término de todos os
 * cálculos. Assim, alterações sucessivas no mesmo D009 não produzem
 * mais de uma marcação na fila.
 */</span>
<span class="palavra">protected function</span> <span class="funcao">triggerFimLoopReprocessa</span>(<span class="variavel">$mD009</span>) {
    <span class="variavel">$this</span>-&gt;D009IsetPendente[(<span class="palavra">int</span>) <span class="variavel">$mD009</span>[<span class="texto">'D009_Id'</span>]] = (<span class="palavra">int</span>) <span class="variavel">$mD009</span>[<span class="texto">'D009_C004_Id'</span>];
}</pre><div class="iset-produtos-exemplo-cabecalho"><h3>triggerAposReprocessa</h3><button class="integradores-button integradores-button--secundario iset-produtos-exemplo-copiar" id="iset-produtos-cad002-apos-copiar" type="button">Copiar código</button></div><pre class="iset-produtos-crontab-codigo" id="iset-produtos-cad002-apos-codigo"><span class="comentario">/**
 * Marca os produtos reprocessados para sincronização automática na ISET.
 *
 * Esta etapa grava somente a pendência local. A comunicação HTTP é feita
 * posteriormente pelo worker, fora da transação do reprocessamento.
 */</span>
<span class="palavra">protected function</span> <span class="funcao">triggerAposReprocessa</span>() {
    <span class="palavra">if</span> (!<span class="variavel">$this</span>-&gt;D009IsetPendente) {
        <span class="palavra">return</span>;
    }

    <span class="palavra">require_once</span>(<span class="texto">'bibliotecas/webservices/iset/iset_produtos.php'</span>);
    <span class="variavel">$ISET_PRODUTOS</span> = <span class="palavra">new</span> <span class="classe">ISET_PRODUTOS</span>();
    <span class="palavra">foreach</span> (<span class="variavel">$this</span>-&gt;D009IsetPendente <span class="palavra">as</span> <span class="variavel">$D009_Id_Iset</span> =&gt; <span class="variavel">$C004_Id_Iset</span>) {
        <span class="comentario">// Falhar ao marcar a integração nunca pode desfazer o reprocessamento
        // interno já concluído; a próxima alteração poderá criar a pendência.</span>
        <span class="palavra">if</span> (!<span class="variavel">$ISET_PRODUTOS</span>-&gt;<span class="funcao">marcarProdutoPendenteAutomatico</span>(<span class="variavel">$C004_Id_Iset</span>, <span class="variavel">$D009_Id_Iset</span>)) {
            <span class="funcao">error_log</span>(<span class="texto">'ISET: não foi possível marcar o produto D009 #'</span> . <span class="variavel">$D009_Id_Iset</span> . <span class="texto">' para sincronização automática.'</span>);
        }
    }
}</pre></section>
<section class="integradores-card iset-produtos-config"><p class="integradores-eyebrow">Referência</p><div class="iset-produtos-exemplo-cabecalho"><h3>Exemplo do JSON padrão</h3><button class="integradores-button integradores-button--secundario iset-produtos-exemplo-copiar" id="iset-produtos-exemplo-copiar" type="button">Copiar</button></div><pre class="iset-produtos-exemplo" id="iset-produtos-exemplo-json"><?php echo integradores_h(ISET_CONFIGURACOES::jsonProdutosPadrao()); ?></pre></section>
<script>(function(){function ativarCopiar(idBotao,idConteudo,textoOriginal){var botao=document.getElementById(idBotao),conteudo=document.getElementById(idConteudo);if(!botao||!conteudo)return;function concluir(){botao.textContent='Copiado';window.setTimeout(function(){botao.textContent=textoOriginal;},1800)}function copiarFallback(texto){var campo=document.createElement('textarea');campo.value=texto;campo.style.position='fixed';campo.style.opacity='0';document.body.appendChild(campo);campo.select();var copiado=document.execCommand('copy');document.body.removeChild(campo);if(!copiado)throw new Error();}botao.addEventListener('click',function(){var texto=conteudo.textContent||'';if(navigator.clipboard&&window.isSecureContext){navigator.clipboard.writeText(texto).then(concluir).catch(function(){try{copiarFallback(texto);concluir();}catch(erro){botao.textContent='Não foi possível copiar';}});}else{try{copiarFallback(texto);concluir();}catch(erro){botao.textContent='Não foi possível copiar';}}});}ativarCopiar('iset-produtos-crontab-copiar','iset-produtos-crontab-codigo','Copiar código');ativarCopiar('iset-produtos-cad002-fim-copiar','iset-produtos-cad002-fim-codigo','Copiar código');ativarCopiar('iset-produtos-cad002-apos-copiar','iset-produtos-cad002-apos-codigo','Copiar código');ativarCopiar('iset-produtos-exemplo-copiar','iset-produtos-exemplo-json','Copiar');}());</script>
<?php integradores_footer(); ?>
