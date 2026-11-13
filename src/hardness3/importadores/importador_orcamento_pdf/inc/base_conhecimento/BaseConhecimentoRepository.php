<?php
namespace hardness;

require_once dirname(__DIR__) . '/app.php';

class BaseConhecimentoRepository
{
    const MAX_MARKDOWN_BYTES = 262144;
    // O limite é do snapshot final, pois ele é enviado inteiro em cada chamada à IA.
    const MAX_MERGED_MARKDOWN_BYTES = 270336;

    protected $baseDirOverride;
    protected $catalogIdOverride;
    protected $userIdOverride;

    public function __construct($baseDirOverride = null, $catalogIdOverride = null, $userIdOverride = null)
    {
        $this->baseDirOverride = $baseDirOverride !== null ? rtrim((string) $baseDirOverride, '/') : null;
        $this->catalogIdOverride = $catalogIdOverride !== null ? (string) $catalogIdOverride : null;
        $this->userIdOverride = $userIdOverride !== null ? (int) $userIdOverride : null;
    }

    public function getPaths()
    {
        $dir = $this->baseDirOverride !== null
            ? $this->baseDirOverride
            : importador_orcamento_pdf_fs_base_conhecimento_dir();

        return array(
            'dir' => $dir,
            'active' => $dir . '/active.json',
            'versions_dir' => $dir . '/versions',
        );
    }

    public function ensureDirectories(&$error = '')
    {
        $error = '';
        $paths = $this->getPaths();
        if (!importador_orcamento_pdf_ensure_directory($paths['versions_dir'])) {
            $error = 'Não foi possível preparar o diretório da Base de Conhecimento.';
            return false;
        }
        return $this->assertSafeStorageStructure($error);
    }

    public function validateUploadedMarkdown($file, &$error = '', &$content = '', &$originalName = '')
    {
        $error = '';
        $content = '';
        $originalName = '';

        if (!is_array($file) || !isset($file['error'])) {
            $error = 'Arquivo Markdown não enviado.';
            return false;
        }
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Falha no upload do arquivo Markdown.';
            return false;
        }
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $error = 'Upload de Markdown inválido.';
            return false;
        }

        $originalName = basename((string) (isset($file['name']) ? $file['name'] : ''));
        if (strtolower(pathinfo($originalName, PATHINFO_EXTENSION)) !== 'md') {
            $error = 'Envie somente arquivos com extensão .md.';
            return false;
        }

        $size = isset($file['size']) ? (int) $file['size'] : 0;
        if ($size <= 0 || $size > self::MAX_MARKDOWN_BYTES) {
            $error = 'O Markdown deve ter entre 1 byte e ' . (int) self::MAX_MARKDOWN_BYTES . ' bytes.';
            return false;
        }

        $content = @file_get_contents($file['tmp_name']);
        return $this->validateMarkdownContent($content, $error);
    }

    public function validateMarkdownContent($content, &$error = '')
    {
        $error = '';
        if (!is_string($content)) {
            $error = 'Não foi possível ler o conteúdo do Markdown.';
            return false;
        }
        if (strlen($content) <= 0 || strlen($content) > self::MAX_MARKDOWN_BYTES) {
            $error = 'O tamanho do Markdown é inválido.';
            return false;
        }
        if (trim($content) === '') {
            $error = 'O Markdown não pode estar vazio.';
            return false;
        }
        if (strpos($content, "\0") !== false) {
            $error = 'O Markdown contém caracteres inválidos.';
            return false;
        }
        if (function_exists('mb_check_encoding') && !mb_check_encoding($content, 'UTF-8')) {
            $error = 'O Markdown deve estar codificado em UTF-8.';
            return false;
        }
        if (!function_exists('mb_check_encoding') && preg_match('//u', $content) !== 1) {
            $error = 'O Markdown deve estar codificado em UTF-8.';
            return false;
        }
        return true;
    }

    public function saveUploadedVersion($file, &$error = '')
    {
        $content = '';
        $originalName = '';
        if (!$this->validateUploadedMarkdown($file, $error, $content, $originalName)) {
            return array();
        }
        return $this->saveVersionFromContent($content, $originalName, $error);
    }

    public function saveVersionFromContent($content, $originalName, &$error = '')
    {
        return $this->withMutationLock(function (&$innerError) use ($content, $originalName) {
            return $this->saveVersionFromContentUnlocked($content, $originalName, $innerError);
        }, $error);
    }

    protected function saveVersionFromContentUnlocked($content, $originalName, &$error = '')
    {
        $error = '';
        if (!$this->validateMarkdownContent($content, $error)) {
            return array();
        }
        if (!$this->ensureDirectories($error)) {
            return array();
        }

        $hash = hash('sha256', $content);
        $markdownPath = $this->versionMarkdownPath($hash);
        $metadataPath = $this->versionMetadataPath($hash);

        $catalogId = $this->getCatalogId();
        if ($catalogId === '') {
            $error = 'Não foi possível identificar o catálogo atual da Base de Conhecimento.';
            return array();
        }

        $metadata = array(
            'version' => 1,
            'source_type' => 'markdown',
            'sha256' => $hash,
            'catalog_id' => $catalogId,
            'original_name' => $this->sanitizeOriginalName($originalName),
            'size_bytes' => strlen($content),
            'created_at' => date('c'),
            'created_by_user_id' => $this->getCurrentUserId(),
            'status' => 'available',
        );

        if (is_file($markdownPath)) {
            $existing = @file_get_contents($markdownPath);
            if (!is_string($existing) || !hash_equals($hash, hash('sha256', $existing))) {
                $error = 'A versão existente falhou na verificação de integridade.';
                return array();
            }
            if (!is_file($metadataPath)) {
                $metadataJson = $this->encodeJson($metadata, $error);
                if ($metadataJson === '' || !$this->atomicCreate($metadataPath, $metadataJson, $error)) {
                    return array();
                }
                $metadata['recovered_metadata'] = true;
                return $metadata;
            }
            $metadata = $this->loadVersion($hash, $error);
            if (empty($metadata)) {
                return array();
            }
            $metadata['already_exists'] = true;
            return $metadata;
        }

        if (!$this->atomicCreate($markdownPath, $content, $error)) {
            return array();
        }

        $metadataJson = $this->encodeJson($metadata, $error);
        if ($metadataJson === '') {
            return array();
        }
        if (!$this->atomicCreate($metadataPath, $metadataJson, $error)) {
            // O Markdown permanece imutável e poderá ser recuperado/reparado manualmente; não o removemos.
            return array();
        }

        return $metadata;
    }

    public function listVersions(&$error = '')
    {
        $error = '';
        if (!$this->ensureDirectories($error)) {
            return array();
        }

        $paths = $this->getPaths();
        $versions = array();
        $files = glob($paths['versions_dir'] . '/*.json');
        if (!is_array($files)) {
            return array();
        }

        foreach ($files as $file) {
            $hash = basename($file, '.json');
            if (!$this->isValidHash($hash)) {
                continue;
            }
            $versionError = '';
            $metadata = $this->loadVersion($hash, $versionError);
            if (empty($metadata)) {
                $versions[] = array('sha256' => $hash, 'valid' => false, 'validation_error' => $versionError);
                continue;
            }
            $metadata['valid'] = true;
            $versions[] = $metadata;
        }

        usort($versions, function ($a, $b) {
            return strcmp(
                isset($b['created_at']) ? (string) $b['created_at'] : '',
                isset($a['created_at']) ? (string) $a['created_at'] : ''
            );
        });
        return $versions;
    }

    public function activateVersion($hash, &$error = '')
    {
        return $this->activateVersions(array($hash), $error);
    }

    public function activateVersions(array $hashes, &$error = '')
    {
        return $this->withMutationLock(function (&$innerError) use ($hashes) {
            return $this->activateVersionsUnlocked($hashes, $innerError);
        }, $error);
    }

    public function replaceActiveVersion($oldHash, $file, &$error = '')
    {
        $content = ''; $originalName = '';
        if (!$this->validateUploadedMarkdown($file, $error, $content, $originalName)) {
            return false;
        }
        return $this->withMutationLock(function (&$innerError) use ($oldHash, $content, $originalName) {
            $active = $this->loadActive($innerError);
            if (empty($active) || empty($active['enabled'])) { $innerError = 'Não existe Base de Conhecimento ativa para substituir.'; return false; }
            $items = (int) $active['version'] === 1
                ? array(array('hash' => (string) $active['active_hash']))
                : $active['items'];
            $oldHash = strtolower((string) $oldHash); $found = false;
            foreach ($items as &$item) {
                if (isset($item['hash']) && hash_equals((string) $item['hash'], $oldHash)) { $found = true; break; }
            }
            if (!$found) { $innerError = 'O arquivo selecionado não faz parte da Base ativa.'; return false; }
            $metadata = $this->saveVersionFromContentUnlocked($content, $originalName, $innerError);
            if (empty($metadata)) { return false; }
            $item = array('hash' => (string) $metadata['sha256'], 'original_name' => (string) $metadata['original_name']);
            unset($item);
            $hashes = array(); foreach ($items as $activeItem) { $hashes[] = isset($activeItem['hash']) ? $activeItem['hash'] : ''; }
            return $this->activateVersionsUnlocked($hashes, $innerError);
        }, $error);
    }

    protected function activateVersionUnlocked($hash, &$error = '')
    {
        return $this->activateVersionsUnlocked(array($hash), $error);
    }

    protected function activateVersionsUnlocked(array $hashes, &$error = '')
    {
        $error = '';
        $catalogId = $this->getCatalogId();
        if ($catalogId === '') {
            $error = 'Não foi possível identificar o catálogo atual.';
            return false;
        }

        $items = array();
        $seen = array();
        foreach ($hashes as $hash) {
            $hash = strtolower(trim((string) $hash));
            if ($hash === '' || isset($seen[$hash])) {
                continue;
            }
            $metadata = $this->loadVersion($hash, $error);
            if (empty($metadata)) {
                return false;
            }
            if ((string) $metadata['catalog_id'] !== $catalogId) {
                $error = 'A versão não pertence ao catálogo atual.';
                return false;
            }
            $seen[$hash] = true;
            $items[] = array('hash' => $hash, 'original_name' => (string) $metadata['original_name']);
        }
        if (empty($items)) {
            $error = 'Selecione ao menos um arquivo para consolidar.';
            return false;
        }

        $snapshot = $this->buildCollectionSnapshot($items, $error);
        if (empty($snapshot)) {
            return false;
        }

        $active = array(
            'version' => 2,
            'enabled' => true,
            'source_type' => 'markdown_collection',
            'items' => $items,
            'snapshot_sha256' => $snapshot['sha256'],
            'snapshot_size_bytes' => $snapshot['size_bytes'],
            'catalog_id' => $catalogId,
            'activated_at' => date('c'),
            'activated_by_user_id' => $this->getCurrentUserId(),
        );
        return $this->writeActive($active, $error);
    }

    public function deactivate(&$error = '')
    {
        return $this->withMutationLock(function (&$innerError) {
            return $this->deactivateUnlocked($innerError);
        }, $error);
    }

    protected function deactivateUnlocked(&$error = '')
    {
        $error = '';
        $catalogId = $this->getCatalogId();
        if ($catalogId === '') {
            $error = 'Não foi possível identificar o catálogo atual.';
            return false;
        }
        return $this->writeActive(array(
            'version' => 2,
            'enabled' => false,
            'source_type' => 'markdown_collection',
            'items' => array(),
            'catalog_id' => $catalogId,
            'deactivated_at' => date('c'),
            'deactivated_by_user_id' => $this->getCurrentUserId(),
        ), $error);
    }

    public function getActiveSnapshot(&$error = '')
    {
        $error = '';
        $active = $this->loadActive($error);
        if (empty($active)) {
            return array();
        }
        if (empty($active['enabled'])) {
            return array();
        }
        if ((int) $active['version'] === 2) {
            $snapshot = $this->buildCollectionSnapshot(isset($active['items']) && is_array($active['items']) ? $active['items'] : array(), $error);
            if (empty($snapshot)) {
                return array();
            }
            if (!isset($active['snapshot_sha256']) || !hash_equals((string) $active['snapshot_sha256'], $snapshot['sha256'])) {
                $error = 'O conteúdo consolidado da Base de Conhecimento foi alterado.';
                return array();
            }
            return array(
                'source_type' => 'markdown_collection', 'content' => $snapshot['content'], 'document_sha256' => $snapshot['sha256'],
                'snapshot_schema_version' => 2, 'catalog_id' => (string) $active['catalog_id'],
                'source_created_at' => isset($active['activated_at']) ? (string) $active['activated_at'] : '',
                'source_created_by_user_id' => isset($active['activated_by_user_id']) ? (int) $active['activated_by_user_id'] : 0,
                'content_size_bytes' => $snapshot['size_bytes'], 'activated_at' => isset($active['activated_at']) ? (string) $active['activated_at'] : '',
                'activated_by_user_id' => isset($active['activated_by_user_id']) ? (int) $active['activated_by_user_id'] : 0,
                'original_name' => 'Base consolidada (' . count($snapshot['sources']) . ' arquivos)', 'sources' => $snapshot['sources']
            );
        }

        $hash = isset($active['active_hash']) ? (string) $active['active_hash'] : '';
        $metadata = $this->loadVersion($hash, $error);
        if (empty($metadata)) {
            return array();
        }
        if (!isset($active['catalog_id']) || (string) $active['catalog_id'] !== $this->getCatalogId()) {
            $error = 'A configuração ativa não pertence ao catálogo atual.';
            return array();
        }

        $paths = $this->getPaths();
        $markdownPath = $this->versionMarkdownPath($hash);
        if (!$this->assertSafeStorageStructure($error) || !$this->assertSafeFile($markdownPath, $paths['versions_dir'], $error)) {
            return array();
        }
        $content = @file_get_contents($markdownPath);
        if (!is_string($content) || !hash_equals($hash, hash('sha256', $content))) {
            $error = 'O conteúdo da Base de Conhecimento ativa falhou na verificação de integridade.';
            return array();
        }

        return array(
            'source_type' => 'markdown',
            'content' => $content,
            'document_sha256' => $hash,
            'snapshot_schema_version' => 1,
            'catalog_id' => (string) $metadata['catalog_id'],
            'source_created_at' => (string) $metadata['created_at'],
            'source_created_by_user_id' => (int) $metadata['created_by_user_id'],
            'content_size_bytes' => (int) $metadata['size_bytes'],
            'activated_at' => isset($active['activated_at']) ? (string) $active['activated_at'] : '',
            'activated_by_user_id' => isset($active['activated_by_user_id']) ? (int) $active['activated_by_user_id'] : 0,
            'original_name' => isset($metadata['original_name']) ? (string) $metadata['original_name'] : '',
        );
    }

    public function getVersionContent($hash, &$error = '')
    {
        $error = '';
        $metadata = $this->loadVersion($hash, $error);
        if (empty($metadata)) {
            return '';
        }
        $paths = $this->getPaths();
        $markdownPath = $this->versionMarkdownPath($hash);
        if (!$this->assertSafeStorageStructure($error) || !$this->assertSafeFile($markdownPath, $paths['versions_dir'], $error)) {
            return '';
        }
        $content = @file_get_contents($markdownPath);
        if (!is_string($content) || !hash_equals((string) $metadata['sha256'], hash('sha256', $content))) {
            $error = 'O conteúdo da versão falhou na verificação de integridade.';
            return '';
        }
        return $content;
    }

    public function getActiveMetadata(&$error = '')
    {
        return $this->loadActive($error);
    }

    protected function loadActive(&$error = '')
    {
        $error = '';
        $paths = $this->getPaths();
        if (!is_file($paths['active'])) {
            return array();
        }
        if (!$this->assertSafeFile($paths['active'], $paths['dir'], $error)) {
            return array();
        }
        $active = $this->readJsonFile($paths['active'], $error);
        if (empty($active)) {
            return array();
        }
        if (!isset($active['enabled']) || !is_bool($active['enabled'])) {
            $error = 'O metadado ativo da Base de Conhecimento é inválido.';
            return array();
        }
        if (!isset($active['version']) || !in_array((int) $active['version'], array(1, 2), true)
            || !isset($active['catalog_id']) || (string) $active['catalog_id'] !== $this->getCatalogId()) {
            $error = 'O metadado ativo da Base de Conhecimento é inválido.';
            return array();
        }
        if ((int) $active['version'] === 1 && (!isset($active['source_type']) || $active['source_type'] !== 'markdown')) {
            $error = 'O metadado ativo da Base de Conhecimento é inválido.';
            return array();
        }
        if ((int) $active['version'] === 2 && (!isset($active['source_type']) || $active['source_type'] !== 'markdown_collection'
            || !isset($active['items']) || !is_array($active['items']))) {
            $error = 'O metadado ativo da Base de Conhecimento é inválido.';
            return array();
        }
        if (!empty($active['enabled']) && (!isset($active['activated_at']) || !isset($active['activated_by_user_id'])
            || ((int) $active['version'] === 1 && (!isset($active['active_hash']) || !$this->isValidHash($active['active_hash'])))
            || ((int) $active['version'] === 2 && (!isset($active['snapshot_sha256']) || !$this->isValidHash($active['snapshot_sha256']))))) {
            $error = 'O metadado da versão ativa está incompleto.';
            return array();
        }
        return $active;
    }

    protected function buildCollectionSnapshot(array $items, &$error = '')
    {
        $error = '';
        if (empty($items)) { $error = 'A Base de Conhecimento consolidada não possui arquivos.'; return array(); }
        $parts = array(); $sources = array(); $seen = array();
        foreach ($items as $position => $item) {
            $hash = is_array($item) && isset($item['hash']) ? strtolower((string) $item['hash']) : '';
            if (!$this->isValidHash($hash) || isset($seen[$hash])) { $error = 'A lista de arquivos da Base de Conhecimento é inválida.'; return array(); }
            $metadata = $this->loadVersion($hash, $error);
            if (empty($metadata)) { return array(); }
            $content = $this->getVersionContent($hash, $error);
            if ($error !== '') { return array(); }
            $name = (string) $metadata['original_name'];
            $parts[] = "<!-- Base de Conhecimento | prioridade " . ((int) $position + 1) . " | arquivo: " . str_replace(array("\r", "\n", '-->'), ' ', $name) . " | sha256: " . $hash . " -->\n" . $content;
            $sources[] = array('hash' => $hash, 'original_name' => $name, 'priority' => (int) $position + 1, 'size_bytes' => strlen($content));
            $seen[$hash] = true;
        }
        $content = implode("\n\n", $parts);
        if (strlen($content) > self::MAX_MERGED_MARKDOWN_BYTES) { $error = 'A Base consolidada excede o limite de ' . (int) self::MAX_MERGED_MARKDOWN_BYTES . ' bytes.'; return array(); }
        return array('content' => $content, 'sha256' => hash('sha256', $content), 'size_bytes' => strlen($content), 'sources' => $sources);
    }

    protected function loadVersion($hash, &$error = '')
    {
        $error = '';
        if (!$this->isValidHash($hash)) {
            $error = 'Identificador de versão inválido.';
            return array();
        }
        $metadataPath = $this->versionMetadataPath($hash);
        $markdownPath = $this->versionMarkdownPath($hash);
        if (!is_file($metadataPath) || !is_file($markdownPath)) {
            $error = 'Versão da Base de Conhecimento não encontrada.';
            return array();
        }
        $paths = $this->getPaths();
        if (!$this->assertSafeStorageStructure($error)
            || !$this->assertSafeFile($metadataPath, $paths['versions_dir'], $error)
            || !$this->assertSafeFile($markdownPath, $paths['versions_dir'], $error)) {
            return array();
        }
        $metadata = $this->readJsonFile($metadataPath, $error);
        if (empty($metadata)) {
            return array();
        }
        $required = array('version', 'source_type', 'sha256', 'catalog_id', 'original_name', 'size_bytes', 'created_at', 'created_by_user_id', 'status');
        foreach ($required as $field) {
            if (!array_key_exists($field, $metadata)) {
                $error = 'Metadado da versão incompleto.';
                return array();
            }
        }
        if ($metadata['source_type'] !== 'markdown' || !$this->isValidHash($metadata['sha256']) || !hash_equals((string) $hash, (string) $metadata['sha256'])) {
            $error = 'Metadado da versão inválido.';
            return array();
        }
        if ((string) $metadata['catalog_id'] !== $this->getCatalogId()) {
            $error = 'A versão não pertence ao catálogo atual.';
            return array();
        }
        $content = @file_get_contents($markdownPath);
        if (!is_string($content) || !hash_equals($hash, hash('sha256', $content))) {
            $error = 'O hash do Markdown não confere.';
            return array();
        }
        if ((int) $metadata['size_bytes'] !== strlen($content)) {
            $error = 'O tamanho do Markdown não confere com o metadado.';
            return array();
        }
        return $metadata;
    }

    protected function writeActive(array $active, &$error = '')
    {
        $error = '';
        if (!$this->ensureDirectories($error)) {
            return false;
        }
        $json = $this->encodeJson($active, $error);
        if ($json === '') {
            return false;
        }
        $paths = $this->getPaths();
        return $this->atomicReplace($paths['active'], $json, $error);
    }

    protected function versionMarkdownPath($hash)
    {
        if (!$this->isValidHash($hash)) {
            return '';
        }
        $paths = $this->getPaths();
        return $paths['versions_dir'] . '/' . strtolower($hash) . '.md';
    }

    protected function versionMetadataPath($hash)
    {
        if (!$this->isValidHash($hash)) {
            return '';
        }
        $paths = $this->getPaths();
        return $paths['versions_dir'] . '/' . strtolower($hash) . '.json';
    }

    protected function getCatalogId()
    {
        return $this->catalogIdOverride !== null ? $this->catalogIdOverride : importador_orcamento_pdf_catalog_id();
    }

    protected function getCurrentUserId()
    {
        return $this->userIdOverride !== null ? $this->userIdOverride : importador_orcamento_pdf_user_id();
    }

    protected function isValidHash($hash)
    {
        return is_string($hash) && preg_match('/^[a-f0-9]{64}$/', $hash) === 1;
    }

    protected function sanitizeOriginalName($name)
    {
        $name = basename((string) $name);
        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name);
        return $name !== '' ? $name : 'base_conhecimento.md';
    }

    protected function readJsonFile($path, &$error = '')
    {
        $error = '';
        $raw = @file_get_contents($path);
        if (!is_string($raw) || trim($raw) === '') {
            $error = 'Arquivo JSON ausente, vazio ou ilegível.';
            return array();
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            $error = 'Arquivo JSON inválido.';
            return array();
        }
        return $decoded;
    }

    protected function encodeJson(array $payload, &$error = '')
    {
        $error = '';
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if (!is_string($json) || $json === '') {
            $error = 'Não foi possível serializar o metadado da Base de Conhecimento.';
            return '';
        }
        return $json;
    }

    protected function atomicCreate($target, $content, &$error = '')
    {
        $error = '';
        $dir = dirname($target);
        if (!$this->assertSafeStorageStructure($error) || !is_dir($dir) || !is_writable($dir) || is_link($target)) {
            $error = 'Diretório de armazenamento indisponível.';
            return false;
        }
        $tmp = tempnam($dir, '.tmp-base-conhecimento-');
        if ($tmp === false || @file_put_contents($tmp, $content, LOCK_EX) === false) {
            $error = 'Não foi possível preparar a gravação da versão.';
            if ($tmp !== false) {
                @unlink($tmp);
            }
            return false;
        }
        @chmod($tmp, 0660);
        if (@link($tmp, $target)) {
            @unlink($tmp);
            return true;
        }
        @unlink($tmp);
        if (is_file($target)) {
            $error = 'A versão já existe.';
        } else {
            $error = 'Não foi possível finalizar a gravação imutável da versão.';
        }
        return false;
    }

    protected function atomicReplace($target, $content, &$error = '')
    {
        $error = '';
        $dir = dirname($target);
        if (!$this->assertSafeStorageStructure($error) || !is_dir($dir) || !is_writable($dir) || is_link($target)) {
            $error = 'Diretório de armazenamento indisponível.';
            return false;
        }
        $tmp = tempnam($dir, '.tmp-base-conhecimento-');
        if ($tmp === false || @file_put_contents($tmp, $content, LOCK_EX) === false) {
            $error = 'Não foi possível preparar a atualização da configuração ativa.';
            if ($tmp !== false) {
                @unlink($tmp);
            }
            return false;
        }
        @chmod($tmp, 0660);
        if (!@rename($tmp, $target)) {
            @unlink($tmp);
            $error = 'Não foi possível ativar a versão da Base de Conhecimento.';
            return false;
        }
        return true;
    }

    protected function withMutationLock($callback, &$error = '')
    {
        $error = '';
        if (!$this->ensureDirectories($error)) {
            return false;
        }
        $paths = $this->getPaths();
        $lockPath = $paths['dir'] . '/base_conhecimento.lock';
        if (is_link($lockPath)) {
            $error = 'Estrutura de armazenamento inconsistente.';
            return false;
        }
        $fp = @fopen($lockPath, 'c');
        if (!$fp || !@flock($fp, LOCK_EX)) {
            if ($fp) { @fclose($fp); }
            $error = 'Não foi possível bloquear a Base de Conhecimento para atualização.';
            return false;
        }
        try {
            return call_user_func_array($callback, array(&$error));
        } finally {
            @flock($fp, LOCK_UN);
            @fclose($fp);
        }
    }

    protected function assertSafeStorageStructure(&$error = '')
    {
        $error = '';
        $paths = $this->getPaths();
        if (is_link($paths['dir']) || is_link($paths['versions_dir'])) {
            $error = 'Estrutura de armazenamento inconsistente.';
            return false;
        }
        $baseReal = @realpath($paths['dir']);
        $versionsReal = @realpath($paths['versions_dir']);
        if ($baseReal === false || $versionsReal === false || !$this->isPathInside($versionsReal, $baseReal)) {
            $error = 'Estrutura de armazenamento inconsistente.';
            return false;
        }
        return true;
    }

    protected function assertSafeFile($path, $allowedDir, &$error = '')
    {
        $error = '';
        if (is_link($path) || !is_file($path)) {
            $error = 'Estrutura de armazenamento inconsistente.';
            return false;
        }
        $real = @realpath($path);
        $allowedReal = @realpath($allowedDir);
        if ($real === false || $allowedReal === false || !$this->isPathInside($real, $allowedReal)) {
            $error = 'Estrutura de armazenamento inconsistente.';
            return false;
        }
        return true;
    }

    protected function isPathInside($path, $base)
    {
        $path = rtrim((string) $path, '/');
        $base = rtrim((string) $base, '/');
        return $path !== '' && $base !== '' && strpos($path . '/', $base . '/') === 0;
    }
}
