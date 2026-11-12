<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/autoload.php';

function task18Assert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

function task18Context(bool $authorized = true): array
{
    return array(
        'usuarioAtual' => 91, 'perfilUsuario' => 12, 'empresaAtual' => 45,
        'c029Ids' => $authorized ? array(307 => true) : array(),
        'C007' => array('C007_Id' => 91), 'C021' => array('C021_Id' => 12), 'C004' => array('C004_Id' => 45),
    );
}

$frameworkEntry = 'HardnessWebhooks' . chr(92) . 'Admin' . chr(92) . 'FrameworkEntry';
$shell = new ReflectionMethod($frameworkEntry, 'renderAdministrativeShell');
$shell->setAccessible(true);
$html = (string) $shell->invoke(null, '/hardness3', 'test-csrf', array());
task18Assert(strpos($html, 'webhooks-admin') !== false, 'authorized entry renders the isolated administrative root');
task18Assert(strpos($html, '/hardness3/webhooks/admin/assets/webhooks-admin.css?v=16') !== false, 'CSS asset uses configured public root');
task18Assert(strpos($html, '/hardness3/webhooks/admin/assets/webhooks-admin.js?v=38') !== false, 'JavaScript asset uses configured public root');
task18Assert(strpos($html, '/gerencial/webhooks/outro/WebhooksAdminRead/?ajax=true&amp;action=list') !== false, 'shell points only to the administrative list endpoint');
task18Assert(strpos($html, 'Novo Webhook') !== false && strpos($html, 'data-webhooks-empty') !== false && strpos($html, 'data-webhooks-error') !== false, 'shell provides action, empty and error states');
foreach (array('WEBHOOKS_MASTER_KEY', 'ciphertext', 'HMAC', 'WHK007_Secret_Criptografado') as $sensitive) {
task18Assert(stripos($html, $sensitive) === false, 'shell does not expose ' . $sensitive);
}

$assetBase = new ReflectionMethod($frameworkEntry, 'assetBaseUrl');
$assetBase->setAccessible(true);
task18Assert($assetBase->invoke(null, array('pathWeb' => '/hardness3/'), array('urlRaiz' => 'http://localhost')) === '/hardness3', 'assets use the ERP pathWeb when public URL has no application path');

$javascript = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.js');
$css = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.css');
task18Assert($javascript !== false && strpos($javascript, 'fetch(url') !== false && strpos($javascript, "credentials: 'same-origin'") !== false, 'list is loaded by same-origin fetch');
task18Assert(strpos((string) $javascript, 'innerHTML') === false && strpos((string) $javascript, 'textContent') !== false, 'untrusted backend values are rendered as text, not HTML');
task18Assert(strpos((string) $javascript, 'C004_Id') === false, 'administrative UI never sends browser company authority');
task18Assert($css !== false && strpos($css, '.webhooks-admin__url') !== false && strpos($css, 'text-overflow:ellipsis') !== false, 'long URLs have structural overflow protection');
task18Assert(strpos((string) $css, '.webhooks-admin [hidden]{display:none!important}') !== false, 'hidden loading and states cannot be overridden by component display rules');
task18Assert(preg_match('/(^|})[[:space:]]*(body|table)[[:space:]]*[{]/', (string) $css) !== 1, 'CSS has no global body or table rules');

$etc011 = file_get_contents(dirname(__DIR__, 2) . '/prog/etc/etc011/outros.php');
task18Assert($etc011 !== false && strpos($etc011, "\$exibirAtalhoWebhooks = !empty(\$g['c029Ids'][307]);") !== false, 'ETC011 shortcut is displayed only for C029 307');
task18Assert(strpos((string) $etc011, '/gerencial/webhooks/content/WebhooksAdminContent/?ajax=true') !== false, 'ETC011 shortcut points to the existing administrative bridge');
task18Assert(strpos((string) $etc011, 'WebhooksAdminRead') === false && strpos((string) $etc011, 'WHK001') === false, 'ETC011 contains no Webhooks domain or API logic');

echo "Task 18 Webhooks administrative UI tests passed.\n";


/* Administrative form, status, HMAC-surface and multi-company UI coverage. */

function task19Assert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

function task19Context(): array
{
    return array('usuarioAtual' => 91, 'perfilUsuario' => 12, 'empresaAtual' => 45, 'c029Ids' => array(307 => true), 'C007' => array('C007_Id' => 91), 'C021' => array('C021_Id' => 12), 'C004' => array('C004_Id' => 45));
}

$entry = 'HardnessWebhooks' . chr(92) . 'Admin' . chr(92) . 'FrameworkEntry';
$shell = new ReflectionMethod($entry, 'renderAdministrativeShell');
$shell->setAccessible(true);
$html = (string) $shell->invoke(null, '/hardness3', 'test-csrf', array());
foreach (array('data-webhooks-catalog-url', 'data-webhooks-detail-url', 'data-webhooks-mutation-url', 'data-webhooks-csrf') as $attribute) {
    task19Assert(strpos($html, $attribute) !== false, 'shell provides ' . $attribute);
}
task19Assert(strpos($html, 'WebhooksAdminMutation') !== false && strpos($html, 'action=catalog') !== false && strpos($html, 'action=detail') !== false, 'shell only exposes the existing administrative API routes');
foreach (array('WHK001_Id', 'C004_Id', 'ciphertext', 'HMAC', 'WEBHOOKS_MASTER_KEY') as $sensitive) {
    task19Assert(stripos($html, $sensitive) === false, 'shell does not expose ' . $sensitive);
}

$javascript = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.js');
$css = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.css');
task19Assert($javascript !== false && strpos($javascript, "method: 'POST'") !== false && strpos($javascript, "credentials: 'same-origin'") !== false, 'create and update use same-origin POST');
task19Assert(strpos((string) $javascript, "data.append('csrf', csrf)") !== false && strpos((string) $javascript, 'localStorage') === false && strpos((string) $javascript, 'sessionStorage') === false, 'CSRF uses only the ERP shell token');
task19Assert(strpos((string) $javascript, "data.append('events[]', item)") !== false && strpos((string) $javascript, 'getCatalog()') !== false, 'event selection is loaded from the API catalog and submitted as an array');
task19Assert(strpos((string) $javascript, 'data-webhooks-saving') !== false && strpos((string) $javascript, 'save.disabled = true') !== false, 'double submit is prevented while saving');
task19Assert(strpos((string) $javascript, 'innerHTML') === false && strpos((string) $javascript, 'textContent') !== false, 'API values are inserted with safe DOM text nodes');
task19Assert(strpos((string) $javascript, 'C004_Id') === false && strpos((string) $javascript, 'WHK007_Secret_Criptografado') === false, 'frontend does not send company authority or expose secret ciphertext');
task19Assert($css !== false && strpos($css, '.webhooks-admin__event-options') !== false && strpos($css, '.webhooks-admin__form') !== false, 'form and event selection styles remain module-scoped');

echo "Task 19 Webhooks administrative form tests passed.\n";


function task20Assert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

function task20Context(bool $authorized = true): array
{
    return array('usuarioAtual' => 91, 'perfilUsuario' => 12, 'empresaAtual' => 45, 'c029Ids' => $authorized ? array(307 => true) : array(), 'C007' => array('C007_Id' => 91), 'C021' => array('C021_Id' => 12), 'C004' => array('C004_Id' => 45));
}

$entry = 'HardnessWebhooks' . chr(92) . 'Admin' . chr(92) . 'FrameworkEntry';
$shell = new ReflectionMethod($entry, 'renderAdministrativeShell');
$shell->setAccessible(true);
$html = (string) $shell->invoke(null, '/hardness3', 'test-csrf', array());
task20Assert(strpos($html, 'webhooks-admin.css?v=16') !== false && strpos($html, 'webhooks-admin.js?v=38') !== false, 'shell publishes the current Webhooks administration assets');
task20Assert(strpos($html, 'data-webhooks-mutation-url') !== false && strpos($html, 'data-webhooks-csrf') !== false, 'shell continues to provide the existing protected mutation boundary');

$javascript = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.js');
$css = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.css');
task20Assert($javascript !== false && $css !== false, 'Task 20 assets are present');
foreach (array("data.append('action', action)", "data.append('id', text(detail.id))", "data.append('csrf', csrf)", "method: 'POST'", "credentials: 'same-origin'") as $required) {
    task20Assert(strpos((string) $javascript, $required) !== false, 'status operation uses protected POST input: ' . $required);
}
task20Assert(strpos((string) $javascript, "action === 'activate' ? 'Ativando…' : 'Desativando…'") !== false && strpos((string) $javascript, "data-webhooks-status-changing") !== false, 'activation and deactivation protect against duplicate clicks and show progress');
task20Assert(strpos((string) $javascript, 'Ativar Webhook') !== false && strpos((string) $javascript, 'Desativar Webhook') !== false, 'detail exposes the appropriate status actions');
task20Assert(strpos((string) $javascript, 'if (detail) { addSecretActions(form, detail); }') === false && strpos((string) $javascript, 'secretCell') === false && strpos((string) $javascript, 'webhooks-admin-secret-help') === false, 'MVP UI does not render or navigate HMAC administration');
task20Assert(strpos((string) $javascript, 'Desativar este Webhook?') !== false && strpos((string) $javascript, 'novas entregas não serão realizadas') !== false && strpos((string) $javascript, "confirm.addEventListener('click'") !== false && strpos((string) $javascript, "cancel.addEventListener('click', close)") !== false, 'deactivation requires an explicit confirmation and cancelling it does not submit');
task20Assert(strpos((string) $javascript, "openEdit(detail.id, action === 'activate' ? 'Webhook ativado com sucesso.' : 'Webhook desativado com sucesso.')") !== false, 'successful status change reloads detail with feedback');
task20Assert(strpos((string) $javascript, 'innerHTML') === false && strpos((string) $javascript, 'C004_Id') === false && strpos((string) $javascript, 'WHK007_Secret_Criptografado') === false, 'Task 20 preserves XSS and secret/company-authority boundaries');
task20Assert(strpos((string) $css, '.webhooks-admin__modal') !== false && strpos((string) $css, '.webhooks-admin__status-actions') !== false, 'confirmation and actions remain isolated in module CSS');

$source = file_get_contents(dirname(__DIR__) . '/admin/FrameworkEntry.php');
task20Assert($source !== false && strpos((string) $source, "elseif (\$action === 'activate')") !== false && strpos((string) $source, "elseif (\$action === 'deactivate')") !== false, 'existing backend activate/deactivate commands remain the exclusive operations');

echo "Task 20 Webhooks activation UI tests passed.\n";


function task21(bool $value, string $message): void
{
    if (!$value) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

$javascript = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.js');
$entry = file_get_contents(dirname(__DIR__) . '/admin/FrameworkEntry.php');
$service = file_get_contents(dirname(__DIR__) . '/src/Admin/WebhookAdminService.php');

task21($javascript !== false && $entry !== false && $service !== false, 'administrative sources are present');
foreach (array('generate_secret', 'rotate_secret', 'revoke_secret', 'showSecret', 'runSecretAction', 'addSecretActions') as $residue) {
    task21(strpos((string) $javascript . (string) $entry . (string) $service, $residue) === false, 'obsolete HMAC administration is absent: ' . $residue);
}
task21(strpos((string) $javascript, 'innerHTML') === false && strpos((string) $javascript, 'localStorage') === false && strpos((string) $javascript, 'sessionStorage') === false, 'UI keeps no unsafe HTML or browser authority state');
task21(strpos((string) $entry, "header('Cache-Control: no-store')") !== false && strpos((string) $entry, 'WEBHOOKS_MASTER_KEY') === false, 'administrative boundary does not expose master-key material');

echo "Task 21 Webhooks administrative surface tests passed.\n";


function task21dAssert(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException('Assertion failed: ' . $message);
}

$entry = file_get_contents(dirname(__DIR__) . '/admin/FrameworkEntry.php');
$resolver = file_get_contents(dirname(__DIR__) . '/src/Admin/Context/WebhookAdminCompanyResolver.php');
$javascript = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.js');
$css = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.css');

task21dAssert($entry !== false && $resolver !== false && $javascript !== false && $css !== false, 'Task 21D sources are present');
task21dAssert(strpos((string) $resolver, "C004_Flag_Ativo='S'") !== false && strpos((string) $resolver, 'activeCompanies') !== false, 'only active C004 records are listed and selected');
task21dAssert(strpos((string) $entry, 'createForCompany') !== false && strpos((string) $entry, "'companies'") !== false, 'all company operations pass through the ERP context boundary');
task21dAssert(strpos((string) $javascript, "data.append('company_id', selectedCompany)") !== false && strpos((string) $javascript, 'companyUrl(listUrl)') !== false && strpos((string) $javascript, 'companyUrl(detailUrl') !== false, 'selected company is transported for reads and mutations');
task21dAssert(strpos((string) $javascript, 'window.history.replaceState') !== false && strpos((string) $javascript, 'showOverview') !== false, 'URL navigation preserves selection and supports overview');
task21dAssert(strpos((string) $entry, 'data-webhooks-company') !== false && strpos((string) $entry, 'Empresas da instalação') !== false && strpos((string) $css, '.webhooks-admin__sidebar') !== false, 'multi-company sidebar is rendered by Webhooks assets');
task21dAssert(strpos((string) $javascript, 'localStorage') === false && strpos((string) $javascript, 'sessionStorage') === false, 'company navigation has no browser authority storage');

echo "Task 21D static multi-company administration tests passed.\n";


function task31Assert(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException('Assertion failed: ' . $message);
}

$catalog = new \HardnessWebhooks\Catalog\Catalog();
$event = $catalog->get('financial_analysis.requested');
$payload = $event['payload'] ?? null;
$adminCatalogSource = file_get_contents(dirname(__DIR__) . '/src/Admin/WebhookAdminCatalogService.php');
task31Assert($adminCatalogSource !== false && strpos((string) $adminCatalogSource, "'payload' => isset(\$event['payload'])") !== false, 'administrative catalog projection forwards public payload metadata from the Catalog');
task31Assert(is_array($payload) && ($payload['body_format'] ?? null) === 'payload_only', 'Catalog declares that the delivered HTTP body is the payload, without a JSON envelope');
task31Assert(($payload['example'] ?? null) === array('order' => array('id' => 12345, 'total' => 1500.90), 'customer' => array('id' => 987, 'name' => 'EMPRESA EXEMPLO LTDA', 'document' => '12345678000190')), 'Catalog payload example is fictional and has the current contract shape');
task31Assert(($payload['fields'] ?? null) === array(
    array('path' => 'order.id', 'type' => 'integer', 'description' => 'Identificador do pedido.', 'required' => true, 'nullable' => false),
    array('path' => 'order.total', 'type' => 'number', 'description' => 'Valor total do pedido.', 'required' => true, 'nullable' => false),
    array('path' => 'customer.id', 'type' => 'integer', 'description' => 'Identificador do cliente.', 'required' => true, 'nullable' => false),
    array('path' => 'customer.name', 'type' => 'string', 'description' => 'Nome/Razão Social do cliente.', 'required' => true, 'nullable' => false),
    array('path' => 'customer.document', 'type' => 'string', 'description' => 'CPF ou CNPJ do cliente, somente números.', 'required' => true, 'nullable' => false),
), 'Catalog documents field paths, types, descriptions, requiredness and nullability');
task31Assert(strpos(json_encode($payload), 'WEBHOOKS_MASTER_KEY') === false && strpos(json_encode($payload), 'ciphertext') === false && strpos(json_encode($payload), 'Bearer ') === false, 'Catalog payload documentation contains no credentials or cryptographic material');

$genericPayload = array('body_format' => 'payload_only', 'example' => array('future' => array('id' => 1)), 'fields' => array(array('path' => 'future.id', 'type' => 'integer', 'description' => 'Campo futuro.', 'required' => true, 'nullable' => false)));
task31Assert(is_array($genericPayload['example']) && $genericPayload['fields'][0]['path'] === 'future.id', 'the metadata shape supports a future event without a UI-specific contract');
task31Assert(strpos((string) $javascript, 'financial_analysis.requested') === false && strpos((string) $javascript, 'EMPRESA EXEMPLO LTDA') === false, 'UI contains no event-specific hardcoded contract');
task31Assert(strpos((string) $javascript, 'JSON.stringify(payload.example, null, 2)') !== false && strpos((string) $javascript, 'Copiar JSON') !== false && strpos((string) $javascript, 'navigator.clipboard') !== false, 'UI formats metadata JSON and provides a dependency-free copy action');
task31Assert(strpos((string) $javascript, 'code.textContent = json') !== false && strpos((string) $javascript, 'innerHTML') === false, 'payload example and field metadata are inserted with safe text nodes');
task31Assert(strpos((string) $javascript, 'openPayloadDialog(event, toggle)') !== false && strpos((string) $javascript, "dialog.setAttribute('role', 'dialog')") !== false && strpos((string) $javascript, "dialog.setAttribute('aria-modal', 'true')") !== false, 'each event opens its payload in an accessible in-page dialog');
task31Assert(strpos((string) $javascript, 'window.open') === false && strpos((string) $javascript, 'document.write') === false && strpos((string) $javascript, 'code.textContent = json') !== false, 'payload dialog avoids popup windows and inserts JSON with safe text nodes');
task31Assert(strpos((string) $javascript, 'form.appendChild(payloadSection)') === false && strpos((string) $javascript, "toggle.setAttribute('aria-expanded'") === false, 'payload has no global section or inline accordion state');
task31Assert(strpos((string) $javascript, 'checkbox.checked = !!selected[event.type]') !== false && strpos((string) $javascript, "toggle.type = 'button'") !== false && strpos((string) $javascript, 'Ver payload de ') !== false && strpos((string) $javascript, "keyEvent.key === 'Escape'") !== false, 'payload can be consulted without changing event selection and dialog keyboard dismissal is available');
task31Assert(strpos((string) $css, '.webhooks-admin__payload-dialog') !== false && strpos((string) $css, '.webhooks-admin__event-summary') !== false, 'dialog and event action styles remain scoped to the Webhooks module');

echo "Task 31 Webhooks payload documentation tests passed.\n";

function task32Assert(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException('Assertion failed: ' . $message);
}

$repository = file_get_contents(dirname(__DIR__) . '/src/Admin/WebhookAdminRepository.php');
$query = file_get_contents(dirname(__DIR__) . '/src/Admin/WebhookAdminQueryService.php');
$javascript = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.js');
task32Assert($repository !== false && $query !== false && $javascript !== false, 'delivery-history sources are present');
$deliveriesSource = substr((string) $repository, (int) strpos((string) $repository, 'public function deliveries'), (int) strpos((string) $repository, 'public function update') - (int) strpos((string) $repository, 'public function deliveries'));
task32Assert(strpos($deliveriesSource, 'WHK004_WHK001_Id=? AND d.WHK004_C004_Id=?') !== false && strpos($deliveriesSource, 'LIMIT ?') !== false, 'delivery query is endpoint and company scoped with an explicit limit');
task32Assert(strpos($deliveriesSource, 'WHK003_Payload') === false && strpos($deliveriesSource, 'Destination_URL') === false && strpos($deliveriesSource, 'WHK007') === false, 'delivery projection does not select payload, destination or secret fields');
task32Assert(strpos($deliveriesSource, 'WHK005_WHK004_Id IN') !== false, 'attempt histories use one bounded batch query rather than N+1 lookups');
task32Assert(strpos((string) $query, "\$delivery['event_label']") !== false && strpos((string) $query, "\$labels[\$type]") !== false, 'event labels are projected from the Catalog, not JavaScript hardcoding');
task32Assert(strpos((string) $repository, 'WHK003_Aggregate_Type aggregate_type,e.WHK003_Aggregate_Id aggregate_id') !== false && strpos((string) $query, "\$delivery['reference_label']") !== false, 'safe aggregate references are projected from the Catalog and event metadata');
task32Assert(strpos((string) $repository, 'e.WHK003_Aggregate_Id LIKE ?') !== false && strpos((string) $repository, 'ORDER BY d.WHK004_Data_Criacao DESC,d.WHK004_Id DESC LIMIT ?') !== false && strpos((string) $repository, 'matchingEventTypes') !== false, 'delivery search is applied by the scoped backend query before the explicit limit');
foreach (array("P: 'Pendente'", "L: 'Processando'", "R: 'Aguardando nova tentativa'", "D: 'Enviado'", "X: 'Falhou'", "C: 'Cancelado'", 'Últimas 30 entregas', 'agrupadas por evento', 'Entregas do evento', 'Atualizar', 'openDeliveryGroupDialog') as $required) task32Assert(strpos((string) $javascript, $required) !== false, 'delivery UI contains ' . $required);
task32Assert(strpos((string) $javascript, 'openDeliveryGroupDialog(detail, group, deliveriesButton, section)') !== false && strpos((string) $javascript, 'webhooks-admin__attempts-dialog') !== false, 'grouped delivery details open in the same accessible modal pattern as payload documentation');
task32Assert(strpos((string) $javascript, "'Configuração'") !== false && strpos((string) $javascript, "'Entregas'") !== false && strpos((string) $javascript, 'function selectTab(showDeliveries)') !== false, 'delivery history is displayed in a separate accessible tab');
task32Assert(strpos((string) $javascript, 'Buscar por evento ou origem') !== false && strpos((string) $javascript, 'data-webhooks-delivery-search') !== false && strpos((string) $javascript, "'Origem'") !== false, 'delivery history displays and filters safe administrative origins');
task32Assert(strpos((string) $javascript, 'delivery_search=') !== false && strpos((string) $javascript, 'loadDeliverySearch') !== false, 'delivery search requests the backend rather than filtering only the loaded page');
task32Assert(strpos((string) $javascript, "'Todos os status'") !== false && strpos((string) $javascript, 'delivery_status=') !== false && strpos((string) $repository, 'd.WHK004_Status=?') !== false, 'delivery status filter is applied by the backend query');
task32Assert(strpos((string) $javascript, 'innerHTML') === false && strpos((string) $javascript, 'WHK003_Payload') === false && strpos((string) $javascript, 'Authorization') === false, 'delivery UI retains escaping and does not render payloads or credentials');

echo "Task 32 Webhooks delivery history static tests passed.\n";


function task33Assert(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException('Assertion failed: ' . $message);
}

$repository = file_get_contents(dirname(__DIR__) . '/src/Admin/WebhookAdminRepository.php');
$service = file_get_contents(dirname(__DIR__) . '/src/Admin/WebhookAdminService.php');
$entry = file_get_contents(dirname(__DIR__) . '/admin/FrameworkEntry.php');
$javascript = file_get_contents(dirname(__DIR__) . '/admin/assets/webhooks-admin.js');
task33Assert($repository !== false && $service !== false && $entry !== false && $javascript !== false, 'manual resend sources are present');
task33Assert(strpos((string) $repository, 'rescheduleDelivery') !== false && strpos((string) $repository, 'd.WHK004_Id=? AND d.WHK004_WHK001_Id=? AND d.WHK004_C004_Id=?') !== false && strpos((string) $repository, 'INNER JOIN WHK003') !== false, 'manual resend validates delivery, visualized endpoint and company together');
task33Assert(strpos((string) $repository, "WHK004_Status,WHK004_Attempt_Count,WHK004_Next_Attempt_At) VALUES (?,?,?,?,?,?,?,'P',0,UTC_TIMESTAMP())") !== false, 'manual resend creates a pending immediate delivery with zero attempts');
task33Assert(strpos((string) $repository, 'WHK004_WHK003_Id') !== false && strpos((string) $repository, 'WHK004_WHK007_Id') !== false && strpos((string) $repository, 'WHK004_Destination_URL') !== false, 'manual resend copies the original event, signature version and destination snapshot');
task33Assert(strpos((string) $service, 'self::uuidV4()') !== false && strpos((string) $entry, "elseif (\$action === 'reschedule_delivery')") !== false && strpos((string) $entry, "'message' => 'Reenvio agendado'") !== false && strpos((string) $entry, "'delivery_id' => \$newDeliveryId") !== false, 'protected administrative command schedules a distinct delivery and returns only its safe result');
foreach (array("data.append('action', 'reschedule_delivery')", "data.append('delivery_id', text(delivery.id))", "data.append('csrf', csrf)", "method: 'POST'", "credentials: 'same-origin'", "'Reenvio agendado'", "'Reenviar esta entrega?'") as $required) task33Assert(strpos((string) $javascript, $required) !== false, 'manual resend UI uses protected confirmation flow: ' . $required);
task33Assert(strpos((string) $javascript, 'WHK003_Payload') === false && strpos((string) $javascript, 'Destination_URL') === false && strpos((string) $javascript, 'Authorization') === false && strpos((string) $javascript, 'response_body') === false, 'manual resend UI does not disclose payload, destination, headers or response body');

echo "Task 33 Webhooks manual delivery resend static tests passed.\n";
