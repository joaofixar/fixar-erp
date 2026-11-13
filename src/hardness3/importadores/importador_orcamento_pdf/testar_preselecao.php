<?php
namespace hardness;

require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/layout.php';

importador_orcamento_pdf_require_auth();

$T003_Id = isset($_GET['T003_Id']) ? (int) $_GET['T003_Id'] : 0;
$descricao = isset($_POST['descricao_teste']) ? trim((string) $_POST['descricao_teste']) : '';
$flashType = '';
$flashMessage = '';
$resultado = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($descricao === '') {
        $flashType = 'error';
        $flashMessage = 'Informe uma descrição para testar a busca.';
    } else {
        $erro = '';
        $resultado = importador_orcamento_pdf_busca_preselecao_test($descricao, $erro);
        if ($erro !== '') {
            $flashType = 'error';
            $flashMessage = $erro;
        } else {
            $flashType = 'success';
            $flashMessage = 'Teste executado com sucesso.';
        }
    }
}

$backHref = importador_orcamento_pdf_base_path($T003_Id > 0 ? 'configurar_busca.php?T003_Id=' . $T003_Id : 'configurar_busca.php');

importador_orcamento_pdf_header('Teste de Busca', array(
    'subtitle' => 'Validação manual do dicionário de busca',
));
?>
<section class="panel">
    <div class="section-head">
        <div>
            <h2>Teste manual de busca</h2>
        </div>
        <a class="button" href="<?php echo importador_orcamento_pdf_h($backHref); ?>">Voltar</a>
    </div>
    <p>
        Informe manualmente uma descrição para validar o fluxo
        <strong>alias → valor no cadastro → índice → interseção</strong>,
        sem usar parser de PDF, IA ou matching oficial.
    </p>

    <?php if ($flashMessage !== ''): ?>
        <div class="flash <?php echo $flashType === 'success' ? 'flash--success' : 'flash--error'; ?>">
            <?php echo importador_orcamento_pdf_h($flashMessage); ?>
        </div>
    <?php endif; ?>

    <form method="post" data-processing-form="1" data-processing-text="Executando teste de busca...">
        <div style="display:grid; gap:12px;">
            <label>
                <strong>Descrição manual</strong>
                <textarea class="input-inline" name="descricao_teste" style="min-height:120px;"><?php echo importador_orcamento_pdf_h($descricao); ?></textarea>
            </label>
            <div class="actions">
                <button class="button button--primary" type="submit" data-processing-text="Executando teste de busca...">Executar teste</button>
            </div>
        </div>
    </form>
</section>

<?php if (!empty($resultado)): ?>
    <?php
    $aliases = isset($resultado['aliases_encontrados']) && is_array($resultado['aliases_encontrados']) ? $resultado['aliases_encontrados'] : array();
    $canonicos = isset($resultado['canonicos_encontrados']) && is_array($resultado['canonicos_encontrados']) ? $resultado['canonicos_encontrados'] : array();
    $buckets = isset($resultado['buckets']) && is_array($resultado['buckets']) ? $resultado['buckets'] : array();
    $passos = isset($resultado['passos_intersecao']) && is_array($resultado['passos_intersecao']) ? $resultado['passos_intersecao'] : array();
    $candidatos = isset($resultado['candidatos']) && is_array($resultado['candidatos']) ? $resultado['candidatos'] : array();
    $motivoResultado = isset($resultado['motivo']) ? (string) $resultado['motivo'] : '';
    ?>
    <section class="panel" style="margin-top:16px;">
        <h2>Resumo do Diagnóstico</h2>
        <div class="summary">
            <div><strong>Texto informado:</strong> <?php echo importador_orcamento_pdf_h($descricao); ?></div>
            <div><strong>Descrição normalizada:</strong> <?php echo importador_orcamento_pdf_h(isset($resultado['descricao_normalizada']) ? $resultado['descricao_normalizada'] : ''); ?></div>
            <div><strong>Motivo do resultado:</strong> <?php echo importador_orcamento_pdf_h($motivoResultado !== '' ? $motivoResultado : 'ok'); ?></div>
            <div><strong>Como pode vir no PDF detectado:</strong> <?php echo (int) count($aliases); ?></div>
            <div><strong>Valores no cadastro detectados:</strong> <?php echo (int) count($canonicos); ?></div>
            <div><strong>Total inicial do catálogo:</strong> <?php echo (int) (isset($resultado['total_inicial_catalogo']) ? $resultado['total_inicial_catalogo'] : 0); ?></div>
            <div><strong>Total final de candidatos:</strong> <?php echo (int) (isset($resultado['total_final_candidatos']) ? $resultado['total_final_candidatos'] : 0); ?></div>
            <?php if (isset($resultado['zero_at_step']) && $resultado['zero_at_step'] !== null): ?>
                <div><strong>Interseção zerou no passo:</strong> <?php echo (int) $resultado['zero_at_step']; ?></div>
            <?php endif; ?>
        </div>
        <div class="summary" style="margin-top:12px;">
            <div><strong>Como o teste funciona:</strong> o sistema normaliza o texto, procura variações do PDF por correspondência textual, converte para valores no cadastro e faz interseção dos buckets do índice.</div>
            <div><strong>Importante:</strong> se uma variação não aparecer em <em>Como Pode Vir no PDF Detectado</em>, ela não participou do filtro.</div>
        </div>
    </section>

    <section class="panel" style="margin-top:16px;">
        <h2>Como Pode Vir no PDF Detectado</h2>
        <?php if (empty($aliases)): ?>
            <p class="muted">Nenhuma variação do PDF encontrada. A busca não foi executada.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="grid" style="min-width:0;">
                    <thead>
                        <tr>
                            <th>Texto do PDF</th>
                            <th>Valor no Cadastro</th>
                            <th>Tamanho</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aliases as $alias): ?>
                            <tr>
                                <td><?php echo importador_orcamento_pdf_h(isset($alias['alias_normalizado']) ? $alias['alias_normalizado'] : ''); ?></td>
                                <td><?php echo importador_orcamento_pdf_h(isset($alias['canonico']) ? $alias['canonico'] : ''); ?></td>
                                <td><?php echo (int) (isset($alias['length']) ? $alias['length'] : 0); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel" style="margin-top:16px;">
        <h2>Valores no Cadastro Encontrados</h2>
        <?php if (empty($canonicos)): ?>
            <p class="muted">Nenhum valor no cadastro identificado.</p>
        <?php else: ?>
            <div class="summary">
                <div><?php echo importador_orcamento_pdf_h(implode(' | ', $canonicos)); ?></div>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel" style="margin-top:16px;">
        <h2>Produtos por Valor no Cadastro</h2>
        <?php if (empty($buckets)): ?>
            <p class="muted">Nenhum conjunto de produtos carregado.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="grid" style="min-width:0;">
                    <thead>
                        <tr>
                            <th>Valor no Cadastro</th>
                            <th>Total de Produtos</th>
                            <th>Primeiros IDs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($buckets as $bucket): ?>
                            <tr>
                                <td><?php echo importador_orcamento_pdf_h(isset($bucket['canonico']) ? $bucket['canonico'] : ''); ?></td>
                                <td><?php echo (int) (isset($bucket['total']) ? $bucket['total'] : 0); ?></td>
                                <td><?php echo importador_orcamento_pdf_h(implode(', ', isset($bucket['sample_ids']) && is_array($bucket['sample_ids']) ? $bucket['sample_ids'] : array())); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel" style="margin-top:16px;">
        <h2>Interseção Progressiva</h2>
        <div class="summary">
            <div><strong>Ordem original:</strong> <?php echo importador_orcamento_pdf_h(implode(' → ', isset($resultado['ordem_original']) && is_array($resultado['ordem_original']) ? $resultado['ordem_original'] : array())); ?></div>
            <div><strong>Ordem efetiva:</strong> <?php echo importador_orcamento_pdf_h(implode(' → ', isset($resultado['ordem_efetiva']) && is_array($resultado['ordem_efetiva']) ? $resultado['ordem_efetiva'] : array())); ?></div>
        </div>
        <div class="summary" style="margin-top:12px;">
            <div><strong>Como ler:</strong> "Antes" mostra quantos produtos ainda estavam na seleção naquele momento, "Produtos com este valor" mostra quantos produtos do catálogo inteiro possuem esse valor, e "Depois" mostra quantos sobraram após aplicar esse filtro.</div>
        </div>
        <?php if (empty($passos)): ?>
            <p class="muted">Nenhuma interseção executada.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="grid" style="min-width:0;">
                    <thead>
                        <tr>
                            <th>Passo</th>
                            <th>Valor no Cadastro</th>
                            <th>Produtos Antes</th>
                            <th>Produtos com este Valor</th>
                            <th>Produtos Depois</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($passos as $passo): ?>
                            <tr>
                                <td><?php echo (int) (isset($passo['passo']) ? $passo['passo'] : 0); ?></td>
                                <td><?php echo importador_orcamento_pdf_h(isset($passo['canonico']) ? $passo['canonico'] : ''); ?></td>
                                <td><?php echo (int) (isset($passo['antes']) ? $passo['antes'] : 0); ?></td>
                                <td><?php echo (int) (isset($passo['bucket_total']) ? $passo['bucket_total'] : 0); ?></td>
                                <td><?php echo (int) (isset($passo['depois']) ? $passo['depois'] : 0); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel" style="margin-top:16px;">
        <h2>Candidatos Finais</h2>
        <p class="muted">Exibindo até 100 registros.</p>
        <?php if (empty($candidatos)): ?>
            <p class="muted">Nenhum candidato sobrevivente.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="grid" style="min-width:0;">
                    <thead>
                        <tr>
                            <th>D001_Id</th>
                            <th>Código</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidatos as $candidato): ?>
                            <tr>
                                <td><?php echo (int) (isset($candidato['D001_Id']) ? $candidato['D001_Id'] : 0); ?></td>
                                <td><?php echo importador_orcamento_pdf_h(isset($candidato['codigo']) ? $candidato['codigo'] : ''); ?></td>
                                <td><?php echo importador_orcamento_pdf_h(isset($candidato['descricao']) ? $candidato['descricao'] : ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php importador_orcamento_pdf_footer(); ?>
