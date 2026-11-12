<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/layout.php';

tablet_separacao_bootstrap();

if (tablet_separacao_user() !== null) {
    header('Location: ' . app_base_path('pedidos.php'));
    exit;
}

$error = null;
$brand = tablet_separacao_system_logo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim((string) ($_POST['login'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));

    $error = tablet_separacao_login($login, $password);
    if ($error === null) {
        //tablet_separacao_set_flash('success', 'Sessão iniciada no módulo tablet.');
        header('Location: ' . app_base_path('pedidos.php'));
        exit;
    }
}

$brandMarkup = sprintf(
    '<div class="login-brand"><img class="login-brand__logo" src="%s" alt="%s"><p class="login-brand__label">Módulo Separação</p></div>',
    h($brand['src']),
    h($brand['alt'])
);

tablet_separacao_header('Login', [
    'page_class' => 'login-page',
    'show_eyebrow' => false,
    'brand_html' => $brandMarkup,
]);
?>
<section class="login-hero">
    <div class="login-card">
        <?php if ($error): ?>
            <div class="alert alert--error"><?php echo h($error); ?></div>
        <?php endif; ?>

        <form method="post" class="stack-lg">
            <label class="field">
                <span>Login</span>
                <input type="text" name="login" placeholder="Usuário" autocomplete="username">
            </label>

            <label class="field">
                <span>Senha</span>
                <input type="password" name="password" placeholder="Senha" autocomplete="current-password">
            </label>

            <button class="button button--primary button--block" type="submit">Entrar</button>
        </form>
    </div>
</section>
<?php tablet_separacao_footer(); ?>
