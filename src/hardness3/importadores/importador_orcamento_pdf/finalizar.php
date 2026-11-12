<?php
namespace hardness;

require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/layout.php';

$T003_Id = isset($_GET['T003_Id']) ? (int) $_GET['T003_Id'] : 0;
list($ok, $message) = importador_orcamento_pdf_validar_orcamento_editavel($T003_Id);

importador_orcamento_pdf_header('Finalizar importação', array('subtitle' => 'Orçamento ' . $T003_Id));
?>
<section class="panel">
    <?php if (!$ok): ?>
        <h2>Acesso indisponivel</h2>
        <p><?php echo importador_orcamento_pdf_h($message); ?></p>
    <?php else: ?>
        <h2>Inserção no orçamento</h2>
        <p>A insercao final usara VEN001-&gt;incluirProduto($form), sem insert direto em T004.</p>
    <?php endif; ?>
</section>
<?php importador_orcamento_pdf_footer(); ?>
