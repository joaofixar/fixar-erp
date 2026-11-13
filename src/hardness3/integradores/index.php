<?php
namespace hardness;

require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/importadores/importador_orcamento_pdf/inc/app.php';
importador_orcamento_pdf_bootstrap();
integradores_exigir_permissao(INTEGRADORES_C029_ACESSAR_PAINEL, 'Seu perfil não possui permissão para acessar o painel de integrações.');

integradores_header('Integrações');
?>
<section class="integradores-intro">
    <p class="integradores-eyebrow">Painel de integrações</p>
    <h2>Escolha uma integração</h2>
    <p>Selecione uma integração para acessar as opções e acompanhar sua configuração.</p>
</section>
<section class="integradores-grid" aria-label="Integrações disponíveis">
    <a class="integration-card" href="<?php echo integradores_h(integradores_base_path('iset/')); ?>">
        <img class="integration-card__logo" src="https://images.seeklogo.com/logo-png/40/1/iset-logo-png_seeklogo-407187.png" alt="ISET">
        <div class="integration-card__title"><strong>ISET</strong><span class="integration-card__type">Ecommerce</span></div>
        <span class="integration-card__arrow material-symbols-outlined" aria-hidden="true">arrow_forward</span>
    </a>
</section>
<?php integradores_footer(); ?>
