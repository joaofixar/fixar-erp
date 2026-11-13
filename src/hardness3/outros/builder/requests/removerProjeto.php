<?php
namespace hardness;
require_once('../conf.php');
if (empty($g['projeto'])) die('Erro ao carregar projeto.');

// Remove as planilhas (arquivos .CSV)
foreach ($g['projeto']['planilhas'] as $planilha) {
    $arquivo = $g['pathDadosArquivos'] . '/' . $planilha['arquivoId'];
    if (file_exists($arquivo)) {
        unlink($arquivo);
    }
}

// Remove o projeto
unlink($g['projetoPath']);

// Mudar projeto na sessão
unset($_SESSION['projeto']);

echo json_encode([
    "erro" => false,
]);
die();
