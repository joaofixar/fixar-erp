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

$situacoesPedidos = array(
    'pendente' => 'Pendente',
    'expedicao' => 'Expedição (sem NF)',
    'nf-gerada' => 'NF gerada',
    'nf-transmitida' => 'NF transmitida / autorizada',
    'nf-cancelada' => 'NF cancelada',
    'nf-inutilizada' => 'NF inutilizada',
);
$tiposNota = array(
    '1' => 'Nota fiscal', '2' => 'Não faturar', '3' => 'ACP', '4' => 'Cupom fiscal',
    '5' => 'Nota D1', '6' => 'NF-e', '7' => 'NFS-e', '8' => 'NFC-e',
);

integradores_header('ISET', 'inicio', 'iset');
?>
<style>
    .iset-inicio { max-width: 1060px; }
    .iset-inicio.integradores-card { max-width: none; min-height: 0; }
    .iset-recentes { margin-top: 22px; }
    .iset-recentes h3 { margin-top: 0; }
    .iset-recentes__lista { display: grid; gap: 12px; margin-top: 18px; }
    .iset-pedido-recente { padding: 10px 16px; border: 1px solid #d6e0ef; border-radius: 12px; background: #f9fbff; }
    .iset-pedido-recente--link { display: block; color: inherit; cursor: pointer; text-decoration: none; transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease; }
    .iset-pedido-recente--link:hover { border-color: #8aaffe; box-shadow: 0 10px 22px rgba(40,96,220,.12); transform: translateY(-1px); }
    .iset-pedido-recente--link:focus-visible { outline: 3px solid rgba(40,96,220,.22); outline-offset: 2px; }
    .iset-situacao--pendente { --iset-fundo: #f2f4fa; --iset-etiqueta-fundo: #dee7f7; --iset-cor: #4e72c2; --iset-texto: #3c5091; }
    .iset-situacao--expedicao { --iset-fundo: #faf6e0; --iset-etiqueta-fundo: #f2e8b3; --iset-cor: #caa228; --iset-texto: #744c1f; }
    .iset-situacao--nf-gerada { --iset-fundo: #f7fcf4; --iset-etiqueta-fundo: #edf6e5; --iset-cor: #bfd9ab; --iset-texto: #354d1a; }
    .iset-situacao--nf-transmitida { --iset-fundo: #f0faeb; --iset-etiqueta-fundo: #daefcc; --iset-cor: #9dcc81; --iset-texto: #354d1a; }
    .iset-situacao--nf-cancelada { --iset-fundo: #fcf3f3; --iset-etiqueta-fundo: #f9dbdb; --iset-cor: #c33f3f; --iset-texto: #872d2d; }
    .iset-situacao--nf-inutilizada { --iset-fundo: #fcf0e2; --iset-etiqueta-fundo: #f8dbbc; --iset-cor: #d45f22; --iset-texto: #8c3b20; }
    .iset-pedido-recente--link { background: var(--iset-fundo); border-color: var(--iset-cor); border-left-width: 5px; }
    .iset-pedido-recente--link:hover { border-color: var(--iset-cor); }
    .iset-pedido-recente__situacao { display: inline-flex; align-items: center; padding: 5px 12px; border: 1px solid var(--iset-cor); border-radius: 6px; background: var(--iset-etiqueta-fundo); color: var(--iset-texto); font-size: 15px; line-height: 1.3; font-weight: 800; text-align: center; }
    .iset-pedido-recente__resumo { display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 12px 20px; }
    .iset-recentes__legenda { display: flex; flex-wrap: wrap; gap: 8px 16px; margin: 14px 0; padding: 0; list-style: none; font-size: 12px; }
    .iset-recentes__legenda li { display: flex; align-items: center; gap: 6px; color: #07224d; }
    .iset-recentes__legenda li::before { content: ''; width: 12px; height: 12px; border: 2px solid var(--iset-cor); border-radius: 3px; background: var(--iset-fundo); }
    .iset-pedido-recente__topo { display: flex; align-items: start; justify-content: space-between; gap: 16px; }
    .iset-pedido-recente__identificacao { display: flex; align-items: flex-start; flex-wrap: wrap; gap: 10px 20px; }
    .iset-pedido-recente__nota { display: flex; flex-wrap: wrap; gap: 4px 12px; padding: 6px 12px; border: 1px solid var(--iset-cor); border-radius: 6px; color: #07224d; font-size: 15px; line-height: 1.3; font-weight: 700; }
    .iset-pedido-recente__titulo { margin: 0; color: #07224d; font-size: 16px; }
    .iset-pedido-recente__externo { color: #617495; font-size: 12px; }
    .iset-pedido-recente__total { color: #087858; font-size: 18px; font-weight: 800; white-space: nowrap; }
    .iset-pedido-recente__dados { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 6px 10px; margin: 7px 0 0; }
    .iset-pedido-recente__dados div { padding: 3px 0 0; background: transparent; }
    .iset-pedido-recente__dados dt { color: #617495; font-size: 11px; }
    .iset-pedido-recente__dados dd { margin: 2px 0 0; color: #07224d; font-weight: 700; }
    @media (max-width: 760px) { .iset-pedido-recente__dados { grid-template-columns: repeat(2, minmax(0, 1fr)); } .iset-pedido-recente__topo { display: block; } .iset-pedido-recente__resumo { justify-content: flex-start; margin-top: 8px; } }
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
    <p>Os cinco pedidos não cancelados mais recentes importados através da ISET na empresa atual.</p>
    <ul class="iset-recentes__legenda" aria-label="Legenda das situações dos pedidos">
        <?php foreach ($situacoesPedidos as $situacao => $rotulo): ?>
            <li class="iset-situacao--<?php echo integradores_h($situacao); ?>"><?php echo integradores_h($rotulo); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php if (!$ultimosPedidos): ?>
        <p class="integradores-flash integradores-flash--sucesso">Não há pedidos ISET importados e não cancelados para esta empresa.</p>
    <?php else: ?>
        <div class="iset-recentes__lista">
            <?php foreach ($ultimosPedidos as $pedido):
                $situacao = isset($pedido['Situacao_Visual'], $situacoesPedidos[$pedido['Situacao_Visual']]) ? $pedido['Situacao_Visual'] : 'pendente';
                $nomeStatus = isset($pedido['T005_Nome_Status']) ? trim($pedido['T005_Nome_Status']) : '';
                if ($situacao === 'nf-inutilizada') {
                    $nomeStatus = 'NF Inutilizada';
                }
            ?>
                <a class="iset-pedido-recente iset-pedido-recente--link iset-situacao--<?php echo integradores_h($situacao); ?>" href="/erp" target="_blank" rel="noopener noreferrer" title="Abrir pedido #<?php echo integradores_h($pedido['T005_Id']); ?> no sistema" onclick="return integradoresAbrirPedidoNoSistema(event, <?php echo (int) $pedido['T005_Id']; ?>);">
                    <div class="iset-pedido-recente__topo">
                        <div class="iset-pedido-recente__identificacao">
                            <div><h4 class="iset-pedido-recente__titulo">Pedido #<?php echo integradores_h($pedido['T005_Id']); ?></h4><span class="iset-pedido-recente__externo">Pedido ISET: <?php echo integradores_h($pedido['T005_Id_Pedido_Externo']); ?></span></div>
                            <?php if (!empty($pedido['T007_Id'])): ?>
                                <div class="iset-pedido-recente__nota" aria-label="Nota fiscal vinculada">
                                    <span>NF: <?php echo integradores_h(trim((string) $pedido['T007_Numero_Nota_Fiscal']) !== '' ? $pedido['T007_Numero_Nota_Fiscal'] : 'Não informado'); ?></span>
                                    <span>Série: <?php echo integradores_h(trim((string) $pedido['T007_Serie']) !== '' ? $pedido['T007_Serie'] : 'Não informada'); ?></span>
                                    <span>Tipo: <?php echo integradores_h(isset($tiposNota[$pedido['T007_Flag_ACP']]) ? $tiposNota[$pedido['T007_Flag_ACP']] : 'Não informado'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="iset-pedido-recente__resumo">
                            <span class="iset-pedido-recente__situacao"><?php echo integradores_h($nomeStatus !== '' ? $nomeStatus : 'Status não informado'); ?></span>
                            <strong class="iset-pedido-recente__total"><?php echo integradores_h(integradores_iset_resumo_moeda($pedido['T005_Valor_Total'])); ?></strong>
                        </div>
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
