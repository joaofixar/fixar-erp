<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');

const INECEL_ROOT = __DIR__ . '/../..';

function inecel_json(array $payload): void
{
    echo json_encode($payload);
    exit;
}

function inecel_format_datetime($value): string
{
    if (empty($value) || $value === '0000-00-00 00:00:00') {
        return '';
    }

    $time = strtotime((string) $value);
    return $time ? date('d/m/Y H:i:s', $time) : '';
}

if (empty($_SESSION['Usuario_Atual']) || empty($_SESSION['Empresa_Atual'])) {
    inecel_json(['ok' => false, 'message' => 'Sessao expirada. Faca login novamente.']);
}

chdir(INECEL_ROOT);
require_once INECEL_ROOT . '/bibliotecas/confUsuario.php';
include_once INECEL_ROOT . '/configuracoes/sysConfig.php';
require_once INECEL_ROOT . '/bibliotecas/funcoesGerais.php';
require_once INECEL_ROOT . '/bibliotecas/conexaoBanco.php';

global $g;
$conn = $g['conexaoBanco'];
$codigo = trim((string) ($_POST['codigo'] ?? ''));
$pedidoId = (int) ($_POST['pedido_id'] ?? 0);

if ($codigo === '') {
    inecel_json(['ok' => false, 'message' => 'Codigo nao informado.']);
}

if ($pedidoId <= 0) {
    inecel_json(['ok' => false, 'message' => 'Pedido nao informado.']);
}

$empresaId = (int) $_SESSION['Empresa_Atual'];
$usuarioId = (int) $_SESSION['Usuario_Atual'];
$codigoSql = mysqli_real_escape_string($conn, $codigo);
$codigoNumero = ctype_digit($codigo) ? (int) $codigo : 0;
$whereId = $codigoNumero > 0 ? "OR T314.T314_Id = '{$codigoNumero}'" : '';

$sql = <<<SQL
SELECT
    T314.T314_Id,
    T314.T314_T005_Id,
    T314.T314_Volume,
    T314.T314_Identificador,
    T314A.T314A_T314_Id,
    IFNULL(T314A.T314A_Flag_Coletado, 'N') AS T314A_Flag_Coletado,
    T314A.T314A_C007_Id_Coletado,
    T314A.T314A_Data_Hora_Coletado,
    C007.C007_Nome AS C007_Nome_Coletado
FROM T314
LEFT JOIN T314A ON T314A.T314A_T314_Id = T314.T314_Id
LEFT JOIN C007 ON C007.C007_Id = T314A.T314A_C007_Id_Coletado
LEFT JOIN T005 ON T005.T005_Id = T314.T314_T005_Id
WHERE T005.T005_C004_Id = '{$empresaId}'
  AND T005.T005_Flag_Status IN (3, 4)
  AND T314.T314_T005_Id = '{$pedidoId}'
  AND (
      T314.T314_Volume = '{$codigoSql}'
      OR T314.T314_Identificador = '{$codigoSql}'
      {$whereId}
  )
ORDER BY T314.T314_Id DESC
LIMIT 1
SQL;

$result = mysqli_query($conn, $sql);
if (!$result) {
    inecel_json(['ok' => false, 'message' => 'Erro ao buscar volume: ' . mysqli_error($conn)]);
}

$volume = mysqli_fetch_assoc($result);
if (!$volume) {
    inecel_json(['ok' => false, 'message' => 'Nao existe o volume neste pedido.']);
}

$volumeId = (int) $volume['T314_Id'];
$alreadyCollected = ($volume['T314A_Flag_Coletado'] ?? 'N') === 'S';

if (empty($volume['T314A_T314_Id'])) {
    inecel_json(['ok' => false, 'message' => 'Volume sem registro em T314A para atualizacao.']);
}

$sqlUpdate = <<<SQL
UPDATE T314A
   SET T314A_Flag_Coletado = 'S',
       T314A_C007_Id_Coletado = '{$usuarioId}',
       T314A_Data_Hora_Coletado = NOW()
 WHERE T314A_T314_Id = '{$volumeId}'
 LIMIT 1
SQL;

if (!mysqli_query($conn, $sqlUpdate)) {
    inecel_json(['ok' => false, 'message' => 'Erro ao atualizar coleta: ' . mysqli_error($conn)]);
}

$volume['T314A_Flag_Coletado'] = 'S';
$volume['T314A_C007_Id_Coletado'] = (string) $usuarioId;
$volume['C007_Nome_Coletado'] = (string) ($_SESSION['Nome_Usuario'] ?? $_SESSION['Primeiro_Nome_Usuario'] ?? 'Usuario');
$volume['T314A_Data_Hora_Coletado'] = date('Y-m-d H:i:s');
$volume['T314A_Data_Hora_Coletado_Formatada'] = inecel_format_datetime($volume['T314A_Data_Hora_Coletado']);
inecel_json([
    'ok' => true,
    'already_collected' => $alreadyCollected,
    'message' => $alreadyCollected ? 'Volume ja estava coletado.' : 'Volume coletado com sucesso.',
    'volume' => $volume,
]);
