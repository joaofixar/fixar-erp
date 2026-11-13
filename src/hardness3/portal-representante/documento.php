<?php
namespace hardness;
require_once __DIR__ . '/bootstrap.php';
$documento = isset($_GET['documento']) && is_string($_GET['documento']) ? $_GET['documento'] : '';
$id = isset($_GET['id']) && is_string($_GET['id']) && preg_match('/^[1-9][0-9]{0,9}$/D', $_GET['id']) ? $_GET['id'] : '';
$erroDocumento = '';
if (!in_array($documento, array('danfe','orcamento','disponibilidade'), true) || $id === '') {
    http_response_code(422); $erroDocumento = 'Documento inválido.';
} elseif ($portalModoGestor && !$portalGestorContexto) {
    http_response_code(403); $erroDocumento = 'A visualização do gestor expirou. Abra novamente pelo painel.';
} elseif (!$portalModoGestor && empty($_SESSION['portal'])) {
    http_response_code(401); $erroDocumento = 'Identifique-se novamente no portal para abrir o documento.';
}
$tituloDocumento = $documento === 'danfe' ? 'DANFE' : ($documento === 'disponibilidade' ? 'Impressão de disponibilidade' : 'Impressão do orçamento');
// A API valida o vínculo da nota, orçamento ou pedido antes de entregar qualquer PDF.
// Libera a sessão antes de a nova aba iniciar sua requisição.
session_write_close();
header('Content-Type: text/html; charset=utf-8');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; object-src 'self' blob:; frame-src 'self' blob:; frame-ancestors 'none'; base-uri 'none'; form-action 'self'");
function portalDocumentoHtml($valor) { return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= portalDocumentoHtml($tituloDocumento) ?> | Hardness</title><link rel="stylesheet" href="../dashboard-personalizado/dashboard.css?v=88"><link rel="stylesheet" href="portal.css?v=37"><script src="documento.js?v=2" defer></script></head>
<body class="document-viewer" data-documento="<?= portalDocumentoHtml($documento) ?>" data-id="<?= portalDocumentoHtml($id) ?>" data-csrf="<?= portalDocumentoHtml($portalCsrfAtual) ?>" data-gestor="<?= portalDocumentoHtml($portalModoGestor ? $portalGestorToken : '') ?>" data-error="<?= portalDocumentoHtml($erroDocumento) ?>">
<header class="document-viewer-toolbar"><h1><?= portalDocumentoHtml($tituloDocumento) ?></h1><nav aria-label="Ações do documento"><button type="button" id="documentCancel">Cancelar</button><button type="button" id="documentRetry" hidden>Tentar novamente</button><a id="documentOpen" target="_blank" rel="noopener" hidden>Abrir PDF</a><a id="documentDownload" hidden>Baixar PDF</a></nav></header>
<main><section id="documentState" class="document-viewer-state" aria-live="polite"><h2 id="documentStateTitle">Preparando documento</h2><p id="documentStatus" role="status">Aguarde o carregamento do PDF.</p></section><object id="documentPdf" type="application/pdf" aria-label="Visualização do PDF" hidden><p>Use “Abrir PDF” ou “Baixar PDF” para visualizar o documento.</p></object></main>
</body></html>
