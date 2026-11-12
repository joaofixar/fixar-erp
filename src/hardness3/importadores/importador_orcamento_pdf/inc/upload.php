<?php
namespace hardness;

require_once __DIR__ . '/app.php';

define('IMPORTADOR_ORCAMENTO_PDF_MAX_UPLOAD_BYTES', 20 * 1024 * 1024);

function importador_orcamento_pdf_upload_dir()
{
    return importador_orcamento_pdf_fs_module_dir();
}

function importador_orcamento_pdf_validar_upload_pdf($file)
{
    if (!is_array($file) || !isset($file['error'])) {
        return array(false, 'Arquivo nao enviado.');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return array(false, importador_orcamento_pdf_upload_error_message((int) $file['error']));
    }

    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return array(false, 'Upload invalido.');
    }

    $size = (int) $file['size'];
    if ($size <= 0) {
        return array(false, 'Arquivo vazio.');
    }

    if ($size > IMPORTADOR_ORCAMENTO_PDF_MAX_UPLOAD_BYTES) {
        return array(false, 'Arquivo excede o limite de 20 MB.');
    }

    $originalName = (string) $file['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        return array(false, 'Somente arquivos PDF sao permitidos.');
    }

    $mime = importador_orcamento_pdf_detect_mime($file['tmp_name']);
    $allowedMimes = array('application/pdf', 'application/x-pdf');
    if ($mime !== '' && !in_array($mime, $allowedMimes, true)) {
        return array(false, 'Tipo de arquivo invalido: ' . $mime);
    }

    return array(true, '');
}

function importador_orcamento_pdf_salvar_upload_pdf($file, $orcamento)
{
    list($ok, $message) = importador_orcamento_pdf_validar_upload_pdf($file);
    if (!$ok) {
        return array(false, $message, array());
    }

    if (!is_array($orcamento) || empty($orcamento['T003_Id']) || empty($orcamento['T003_D024_Id'])) {
        return array(false, 'Orcamento invalido para upload.', array());
    }

    $hash = hash_file('sha256', $file['tmp_name']);
    $T003_Id = (int) $orcamento['T003_Id'];
    $D024_Id = (int) $orcamento['T003_D024_Id'];
    $uploadDir = '';
    $lastError = '';
    $candidates = importador_orcamento_pdf_fs_orcamento_dir_candidates($D024_Id, $T003_Id);
    foreach ($candidates as $candidateDir) {
        if (importador_orcamento_pdf_ensure_directory($candidateDir)) {
            $uploadDir = $candidateDir;
            break;
        }

        $error = error_get_last();
        $lastError = isset($error['message']) ? (string) $error['message'] : '';
    }

    if ($uploadDir === '') {
        return array(false, 'Nao foi possivel criar o diretorio de upload. Caminhos tentados: ' . implode(' | ', $candidates) . ($lastError !== '' ? ' | Erro: ' . $lastError : ''), array());
    }

    // O novo PDF é salvo primeiro com nome temporário. Somente depois disso
    // descartamos os dados da importação anterior, para não perder a revisão
    // atual quando o upload falhar.
    try {
        $temporaryName = '.upload-' . bin2hex(random_bytes(16)) . '.pdf';
    } catch (\Exception $e) {
        $temporaryName = '.upload-' . uniqid('', true) . '.pdf';
    }
    $temporaryPath = $uploadDir . '/' . $temporaryName;

    if (!move_uploaded_file($file['tmp_name'], $temporaryPath)) {
        return array(false, 'Nao foi possivel salvar o arquivo enviado.', array());
    }

    @chmod($temporaryPath, 0660);

    list($resetOk, $resetMessage) = importador_orcamento_pdf_resetar_importacao_anterior($uploadDir, $temporaryName);
    if (!$resetOk) {
        @unlink($temporaryPath);
        return array(false, $resetMessage, array());
    }

    $storedName = 'original.pdf';
    $destination = $uploadDir . '/' . $storedName;
    if (!@rename($temporaryPath, $destination)) {
        @unlink($temporaryPath);
        return array(false, 'Nao foi possivel finalizar o salvamento do novo PDF.', array());
    }

    @chmod($destination, 0660);

    return array(true, '', array(
        'original_name' => (string) $file['name'],
        'stored_name' => $storedName,
        'stored_path' => $destination,
        'sha256' => $hash,
        'size_bytes' => (int) $file['size'],
        'mime_type' => importador_orcamento_pdf_detect_mime($destination),
        'T003_Id' => $T003_Id,
        'D024_Id' => $D024_Id,
        'work_dir' => $uploadDir,
    ));
}

/**
 * O diretório D024/T003 contém somente os dados de uma importação daquele
 * orçamento. Ao subir outro PDF, remove preview, revisões, sugestões e estados
 * anteriores, mantendo apenas o arquivo temporário que acabou de ser enviado.
 */
function importador_orcamento_pdf_resetar_importacao_anterior($dir, $preservarNome)
{
    if (!is_dir($dir)) {
        return array(false, 'Diretorio da importacao nao encontrado para reiniciar o processo.');
    }

    $entries = @scandir($dir);
    if ($entries === false) {
        return array(false, 'Nao foi possivel acessar os dados da importacao anterior.');
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..' || $entry === $preservarNome) {
            continue;
        }

        if (!importador_orcamento_pdf_remover_artefato_importacao($dir . '/' . $entry)) {
            return array(false, 'Nao foi possivel remover todos os dados da importacao anterior.');
        }
    }

    return array(true, '');
}

function importador_orcamento_pdf_remover_artefato_importacao($path)
{
    // Links são removidos sem seguir o destino.
    if (is_link($path) || is_file($path)) {
        return @unlink($path);
    }

    if (!is_dir($path)) {
        return false;
    }

    $entries = @scandir($path);
    if ($entries === false) {
        return false;
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        if (!importador_orcamento_pdf_remover_artefato_importacao($path . '/' . $entry)) {
            return false;
        }
    }

    return @rmdir($path);
}

function importador_orcamento_pdf_detect_mime($path)
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = finfo_file($finfo, $path);
            finfo_close($finfo);
            return (string) $mime;
        }
    }

    return '';
}

function importador_orcamento_pdf_upload_error_message($errorCode)
{
    $messages = array(
        UPLOAD_ERR_INI_SIZE => 'Arquivo excede o limite configurado no PHP.',
        UPLOAD_ERR_FORM_SIZE => 'Arquivo excede o limite do formulario.',
        UPLOAD_ERR_PARTIAL => 'Upload incompleto.',
        UPLOAD_ERR_NO_FILE => 'Nenhum arquivo enviado.',
        UPLOAD_ERR_NO_TMP_DIR => 'Diretorio temporario indisponivel.',
        UPLOAD_ERR_CANT_WRITE => 'Falha ao gravar arquivo temporario.',
        UPLOAD_ERR_EXTENSION => 'Upload bloqueado por extensao do PHP.',
    );

    return isset($messages[$errorCode]) ? $messages[$errorCode] : 'Erro de upload desconhecido.';
}
