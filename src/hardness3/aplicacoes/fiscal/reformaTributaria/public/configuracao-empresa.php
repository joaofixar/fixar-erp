<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';
require_once __DIR__ . '/../src/CompanyConfiguration.php';

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

try {
    $context = reforma_tributaria_require_access();
    $db = reforma_tributaria_connect();
    if (!$db) throw new RuntimeException('Não foi possível consultar a configuração da empresa agora.');
    try {
        $companies = ReformaTributariaCompanyConfiguration::listForUser($db, $context['user_id']);
        $companyId = filter_input(INPUT_GET, 'empresa', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;
        if ($companyId <= 0) {
            $summary = ['active' => 0, 'inactive' => 0, 'unconfigured' => 0];
            foreach ($companies as $company) {
                $class = $company['status']['class'];
                $summary[$class === 'status--active' ? 'active' : ($class === 'status--inactive' ? 'inactive' : 'unconfigured')]++;
            }
            echo json_encode(['companies' => $companies, 'summary' => $summary], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $configuration = ReformaTributariaCompanyConfiguration::findForUser($db, $context['user_id'], $companyId);
        if (!$configuration) {
            http_response_code(404);
            echo json_encode(['error' => 'A empresa selecionada não está disponível para consulta.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode(['configuration' => ['company_name' => (string) ($configuration['short_name'] ?: $configuration['company_name']), 'status' => ReformaTributariaCompanyConfiguration::status($configuration['filling_type'])]], JSON_UNESCAPED_UNICODE);
    } finally {
        mysqli_close($db);
    }
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode(['error' => 'Não foi possível consultar a configuração da empresa agora.'], JSON_UNESCAPED_UNICODE);
}
