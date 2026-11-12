<?php
namespace hardness;

require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/layout.php';

$T003_Id = isset($_GET['T003_Id']) ? (int) $_GET['T003_Id'] : 0;
list($ok, $message) = importador_orcamento_pdf_validar_orcamento_editavel($T003_Id);

importador_orcamento_pdf_header('Upload do PDF', array('subtitle' => 'Orçamento ' . $T003_Id));
?>
<section class="panel">
    <?php if (!$ok): ?>
        <h2>Acesso indisponível</h2>
        <p><?php echo importador_orcamento_pdf_h($message); ?></p>
    <?php else: ?>
        <h2>Enviar arquivo</h2>
        <p>Envie um PDF para iniciar o processo de importação. O arquivo será processado neste fluxo de revisão e inclusão no orçamento.</p>
        <form class="upload-box" method="post" enctype="multipart/form-data" action="<?php echo importador_orcamento_pdf_h(importador_orcamento_pdf_base_path('endpoints/upload.php?redirect=1')); ?>" data-processing-form="1" data-processing-text="Enviando PDF e iniciando a importação...">
            <input type="hidden" name="T003_Id" value="<?php echo importador_orcamento_pdf_h($T003_Id); ?>">
            <input type="file" name="arquivo_pdf" accept="application/pdf,.pdf">
            <button class="button button--primary" type="submit" data-processing-text="Enviando PDF e preparando a leitura...">Enviar PDF</button>
        </form>
    <?php endif; ?>
</section>
<?php importador_orcamento_pdf_footer(); ?>
