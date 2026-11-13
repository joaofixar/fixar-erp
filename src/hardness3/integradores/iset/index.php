<?php
namespace hardness;

require_once dirname(__DIR__) . '/layout.php';
require_once dirname(dirname(__DIR__)) . '/importadores/importador_orcamento_pdf/inc/app.php';

importador_orcamento_pdf_bootstrap();
integradores_exigir_permissao(INTEGRADORES_C029_ACESSAR_PAINEL, 'Seu perfil não possui permissão para acessar a integração ISET.');
require_once dirname(dirname(__DIR__)) . '/bibliotecas/webservices/iset/iset.php';
$ultimosPedidos = !empty($g['empresaAtual']) ? (new ISET_HISTORICO())->ultimosPedidosImportados((int) $g['empresaAtual']) : array();

function integradores_iset_resumo_moeda($valor) { return 'R$ ' . number_format((float) $valor, 2, ',', '.'); }
function integradores_iset_resumo_data($valor) { $tempo = strtotime($valor); return $tempo ? date('d/m/Y', $tempo) : 'Não informada'; }

integradores_header('ISET', 'inicio', 'iset');
?>
<style>
    .iset-inicio { max-width: 1060px; }
    .iset-inicio.integradores-card { max-width: none; min-height: 0; }
    .iset-recentes { margin-top: 22px; }
    .iset-recentes h3 { margin-top: 0; }
    .iset-recentes__lista { display: grid; gap: 12px; margin-top: 18px; }
    .iset-pedido-recente { padding: 18px; border: 1px solid #d6e0ef; border-radius: 12px; background: #f9fbff; }
    .iset-pedido-recente--link { display: block; color: inherit; cursor: pointer; text-decoration: none; transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease; }
    .iset-pedido-recente--link:hover { border-color: #8aaffe; box-shadow: 0 10px 22px rgba(40,96,220,.12); transform: translateY(-1px); }
    .iset-pedido-recente--link:focus-visible { outline: 3px solid rgba(40,96,220,.22); outline-offset: 2px; }
    .iset-pedido-recente__topo { display: flex; align-items: start; justify-content: space-between; gap: 16px; }
    .iset-pedido-recente__titulo { margin: 0; color: #07224d; font-size: 16px; }
    .iset-pedido-recente__externo { color: #617495; font-size: 12px; }
    .iset-pedido-recente__total { color: #087858; font-size: 18px; font-weight: 800; white-space: nowrap; }
    .iset-pedido-recente__dados { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin: 15px 0 0; }
    .iset-pedido-recente__dados div { padding: 9px 10px; border-radius: 8px; background: #f1f5fb; }
    .iset-pedido-recente__dados dt { color: #617495; font-size: 11px; }
    .iset-pedido-recente__dados dd { margin: 2px 0 0; color: #07224d; font-weight: 700; }
    @media (max-width: 760px) { .iset-pedido-recente__dados { grid-template-columns: repeat(2, minmax(0, 1fr)); } .iset-pedido-recente__topo { display: block; } .iset-pedido-recente__total { display: block; margin-top: 10px; } }
</style>
<section class="integradores-card iset-inicio">
    <p class="integradores-eyebrow">Integração selecionada</p>
    <h2>ISET</h2>
    <p>Utilize o menu lateral para importar pedidos ou ajustar as configurações da integração.</p>
    <a class="integradores-button" href="<?php echo integradores_h(integradores_base_path('iset/importar.php')); ?>">Ir para importar</a>
</section>
<section class="integradores-card iset-inicio iset-recentes">
    <p class="integradores-eyebrow">Histórico de importação</p>
    <h3>Últimos pedidos importados</h3>
    <p>Os cinco pedidos importados atravês da ISET mais recentes na empresa atual.</p>
    <?php if (!$ultimosPedidos): ?>
        <p class="integradores-flash integradores-flash--sucesso">Ainda não há pedidos ISET importados para esta empresa.</p>
    <?php else: ?>
        <div class="iset-recentes__lista">
            <?php foreach ($ultimosPedidos as $pedido): ?>
                <a class="iset-pedido-recente iset-pedido-recente--link" href="/erp" target="_blank" rel="noopener noreferrer" title="Abrir pedido #<?php echo integradores_h($pedido['T005_Id']); ?> no sistema" onclick="return integradoresAbrirPedidoNoSistema(event, <?php echo (int) $pedido['T005_Id']; ?>);">
                    <div class="iset-pedido-recente__topo">
                        <div><h4 class="iset-pedido-recente__titulo">Pedido #<?php echo integradores_h($pedido['T005_Id']); ?></h4><span class="iset-pedido-recente__externo">Pedido ISET: <?php echo integradores_h($pedido['T005_Id_Pedido_Externo']); ?></span></div>
                        <strong class="iset-pedido-recente__total"><?php echo integradores_h(integradores_iset_resumo_moeda($pedido['T005_Valor_Total'])); ?></strong>
                    </div>
                    <dl class="iset-pedido-recente__dados">
                        <div><dt>Cliente</dt><dd><?php echo integradores_h($pedido['Cliente']); ?></dd></div>
                        <div><dt>Emissão</dt><dd><?php echo integradores_h(integradores_iset_resumo_data($pedido['T005_Data_Emissao'])); ?></dd></div>
                        <div><dt>Frete / desconto</dt><dd><?php echo integradores_h(integradores_iset_resumo_moeda($pedido['T005_Valor_Frete']) . ' / ' . integradores_iset_resumo_moeda($pedido['T005_Valor_Desconto'])); ?></dd></div>
                        <div><dt>Itens / quantidade</dt><dd><?php echo integradores_h($pedido['Quantidade_Itens'] . ' / ' . $pedido['Quantidade_Produtos']); ?></dd></div>
                    </dl>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php integradores_footer(); ?>
