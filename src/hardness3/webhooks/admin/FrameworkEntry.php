<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin;

use HardnessWebhooks\Admin\Authorization\WebhookAdminAuthorizationException;
use HardnessWebhooks\Admin\Context\ErpWebhookAdminContextFactory;
use HardnessWebhooks\Admin\Context\WebhookAdminCompanyResolver;
use HardnessWebhooks\Admin\Security\AdministrativeRequestException;
use HardnessWebhooks\Admin\Security\AdministrativeRequestGuard;
use HardnessWebhooks\Admin\Security\CsrfGuard;
use HardnessWebhooks\Admin\ErpDatabaseConnectionFactory;
use Throwable;

/** ERP-only administrative boundary. It intentionally contains no CRUD or UI framework. */
final class FrameworkEntry
{
    /** @param array<string,mixed> $frameworkContext */
    public static function render(array $frameworkContext, array $connectionConfiguration = array()): void
    {
        self::respondHtml($frameworkContext, $connectionConfiguration);
    }

    /** @param array<string,mixed> $frameworkContext */
    public static function read(array $frameworkContext): void
    {
        self::respondJson($frameworkContext, false);
    }

    /** @param array<string,mixed> $frameworkContext */
    public static function mutation(array $frameworkContext): void
    {
        self::respondJson($frameworkContext, true);
    }

    /** @param array<string,mixed> $frameworkContext */
    private static function respondHtml(array $frameworkContext, array $connectionConfiguration): void
    {
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: no-store');
        try {
            (new ErpWebhookAdminContextFactory())->create($frameworkContext);
            (new AdministrativeRequestGuard())->assertReadRequest();
            $connection = (new ErpDatabaseConnectionFactory())->create($frameworkContext);
            $companies = (new WebhookAdminCompanyResolver())->activeCompanies($connection);
            echo self::renderAdministrativeShell(self::assetBaseUrl($frameworkContext, $connectionConfiguration), (new CsrfGuard())->token(), $companies);
        } catch (WebhookAdminAuthorizationException $exception) {
            http_response_code(403);
            echo '<div class="webhooks-admin webhooks-admin--error">Acesso não autorizado.</div>';
        } catch (AdministrativeRequestException $exception) {
            http_response_code(405);
            echo '<div class="webhooks-admin webhooks-admin--error">Requisição administrativa inválida.</div>';
        } catch (Throwable $exception) {
            error_log('Hardness Webhooks administration boundary failure.');
            http_response_code(503);
            echo '<div class="webhooks-admin webhooks-admin--error">Não foi possível carregar a administração de Webhooks.</div>';
        }
    }

    /** @param array<string,mixed> $frameworkContext */
    private static function respondJson(array $frameworkContext, bool $mutation): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        header('Pragma: no-cache');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: no-referrer');
        try {
            $factory = new ErpWebhookAdminContextFactory();
            $factory->create($frameworkContext);
            $guard = new AdministrativeRequestGuard();
            if ($mutation) {
                $guard->assertMutationRequest();
            } else {
                $guard->assertReadRequest();
            }
            // Keeps the Task 16 bridge probe side-effect free when no API action was requested.
            if (($mutation ? ($_POST['action'] ?? null) : ($_GET['action'] ?? null)) === null) {
                self::jsonSuccess(200, array('status' => 'boundary_ready'));
                return;
            }
            $connection = (new ErpDatabaseConnectionFactory())->create($frameworkContext);
            $action = (string) ($mutation ? ($_POST['action'] ?? '') : ($_GET['action'] ?? 'list'));
            if (!$mutation && $action === 'companies') {
                self::jsonSuccess(200, (new WebhookAdminCompanyResolver())->activeCompanies($connection));
                return;
            }
            $companyId = $mutation ? ($_POST['company_id'] ?? null) : ($_GET['company_id'] ?? null);
            $context = $factory->createForCompany($frameworkContext, $connection, $companyId);
            if ($mutation) {
                $service = new WebhookAdminService();
                if ($action === 'create') {
                    $id = $service->create($connection, $context, $_POST);
                    self::jsonSuccess(201, array('id' => $id));
                } elseif ($action === 'update') {
                    $service->update($connection, $context, self::id($_POST), $_POST);
                    self::jsonSuccess(200, array('updated' => true));
                } elseif ($action === 'activate') {
                    $service->activate($connection, $context, self::id($_POST));
                    self::jsonSuccess(200, array('activated' => true));
                } elseif ($action === 'deactivate') {
                    $service->deactivate($connection, $context, self::id($_POST));
                    self::jsonSuccess(200, array('deactivated' => true));
                } elseif ($action === 'reschedule_delivery') {
                    $newDeliveryId = $service->rescheduleDelivery($connection, $context, self::id($_POST), self::deliveryId($_POST));
                    self::jsonSuccess(200, array('message' => 'Reenvio agendado', 'delivery_id' => $newDeliveryId));
                } else { self::jsonError(400, 'Operação administrativa inválida.'); }
                return;
            }
            $query = new WebhookAdminQueryService();
            if ($action === 'list') self::jsonSuccess(200, $query->list($connection, $context));
            elseif ($action === 'detail') { $detail = $query->detail($connection, $context, self::id($_GET), self::deliverySearch($_GET), self::deliveryStatus($_GET)); if ($detail === null) self::jsonError(404, 'Webhook não encontrado.'); else self::jsonSuccess(200, $detail); }
            elseif ($action === 'catalog') self::jsonSuccess(200, $query->catalog($connection, $context));
            else self::jsonError(400, 'Operação administrativa inválida.');
        } catch (WebhookAdminAuthorizationException $exception) {
            self::jsonError(403, 'Acesso não autorizado.');
        } catch (AdministrativeRequestException $exception) {
            self::jsonError(405, 'Requisição administrativa inválida.');
        } catch (\OutOfBoundsException $exception) {
            self::jsonError(404, 'Webhook não encontrado.');
        } catch (\InvalidArgumentException $exception) {
            self::jsonError(400, $exception->getMessage());
        } catch (\HardnessWebhooks\Delivery\DestinationValidationException $exception) {
            self::jsonError(400, 'URL do webhook é inválida.');
        } catch (Throwable $exception) {
            error_log('Hardness Webhooks administration boundary failure.');
            self::jsonError(503, 'Não foi possível processar a administração de Webhooks.');
        }
    }

    private static function jsonError(int $status, string $message): void
    {
        http_response_code($status);
        echo json_encode(array('ok' => false, 'message' => $message), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /** @param mixed $input */
    private static function id(array $input): int
    {
        $id = isset($input['id']) && is_scalar($input['id']) ? filter_var($input['id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) : false;
        if ($id === false) throw new \InvalidArgumentException('Webhook não encontrado.');
        return (int) $id;
    }
    private static function deliveryId(array $input): int
    {
        $id = isset($input['delivery_id']) && is_scalar($input['delivery_id']) ? filter_var($input['delivery_id'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) : false;
        if ($id === false) throw new \InvalidArgumentException('Webhook não encontrado.');
        return (int) $id;
    }
    private static function deliverySearch(array $input): ?string
    {
        if (!isset($input['delivery_search'])) return null;
        if (!is_scalar($input['delivery_search'])) throw new \InvalidArgumentException('Busca de entregas inválida.');
        return (string) $input['delivery_search'];
    }
    private static function deliveryStatus(array $input): ?string
    {
        if (!isset($input['delivery_status'])) return null;
        if (!is_scalar($input['delivery_status'])) throw new \InvalidArgumentException('Status de entrega inválido.');
        return (string) $input['delivery_status'];
    }



    /** @param mixed $data */
    private static function jsonSuccess(int $status, $data): void
    {
        http_response_code($status);
        echo json_encode(array('ok' => true, 'data' => $data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    private static function assetBaseUrl(array $frameworkContext, array $connectionConfiguration): string
    {
        $pathWeb = trim((string) ($frameworkContext['pathWeb'] ?? ''));
        if ($pathWeb !== '') {
            return '/' . trim($pathWeb, '/');
        }
        $base = trim((string) ($connectionConfiguration['urlRaiz'] ?? ''));
        $parts = parse_url($base);
        if (!is_array($parts) || !isset($parts['scheme'], $parts['host']) || !in_array(strtolower((string) $parts['scheme']), array('http', 'https'), true)) {
            return '';
        }
        $path = isset($parts['path']) ? rtrim((string) $parts['path'], '/') : '';
        return $path;
    }

    /** @param array<int,array{id:int,name:string,active_webhooks:int}> $companies */
    private static function renderAdministrativeShell(string $assetBase, string $csrf, array $companies): string
    {
        $prefix = $assetBase . '/webhooks/admin/assets/';
        $readUrl = '/gerencial/webhooks/outro/WebhooksAdminRead/?ajax=true&action=list';
        $catalogUrl = '/gerencial/webhooks/outro/WebhooksAdminRead/?ajax=true&action=catalog';
        $detailUrl = '/gerencial/webhooks/outro/WebhooksAdminRead/?ajax=true&action=detail';
        $mutationUrl = '/gerencial/webhooks/outro/WebhooksAdminMutation/?ajax=true';
        $css = htmlspecialchars($prefix . 'webhooks-admin.css?v=16', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $javascript = htmlspecialchars($prefix . 'webhooks-admin.js?v=38', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $logo = htmlspecialchars($assetBase . '/static/img/logo_hardness.png', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $readUrl = htmlspecialchars($readUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $catalogUrl = htmlspecialchars($catalogUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $detailUrl = htmlspecialchars($detailUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $mutationUrl = htmlspecialchars($mutationUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $csrf = htmlspecialchars($csrf, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $companyLinks = '';
        foreach ($companies as $company) {
            $id = (int) $company['id'];
            $name = htmlspecialchars($company['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $active = (int) $company['active_webhooks'];
            $companyLinks .= '<button type="button" class="webhooks-admin__company" data-webhooks-company="' . $id . '"><span>' . $name . '</span><small>Id ' . $id . ' · ' . $active . ' ativo' . ($active === 1 ? '' : 's') . '</small></button>';
        }
        return '<link rel="stylesheet" href="' . $css . '">'
            . '<section class="webhooks-admin" aria-labelledby="webhooks-admin-title" data-webhooks-list-url="' . $readUrl . '" data-webhooks-catalog-url="' . $catalogUrl . '" data-webhooks-detail-url="' . $detailUrl . '" data-webhooks-mutation-url="' . $mutationUrl . '" data-webhooks-csrf="' . $csrf . '">'
            . '<aside class="webhooks-admin__sidebar"><img class="webhooks-admin__brand" src="' . $logo . '" alt="Hardness"><nav aria-label="Administração de Webhooks"><button type="button" class="webhooks-admin__overview" data-webhooks-overview><span>Visão geral</span></button><p class="webhooks-admin__company-label">Empresas da instalação</p><div class="webhooks-admin__companies">' . $companyLinks . '</div></nav></aside>'
            . '<div class="webhooks-admin__workspace"><header class="webhooks-admin__header"><div><p class="webhooks-admin__eyebrow">INTEGRAÇÕES</p><h1 id="webhooks-admin-title">Webhooks</h1><p class="webhooks-admin__description" data-webhooks-company-name>Escolha uma empresa para administrar seus destinos.</p></div>'
            . '<div class="webhooks-admin__header-actions"><button class="webhooks-admin__button webhooks-admin__button--primary" type="button" data-webhooks-placeholder="new" hidden>Novo Webhook</button></div></header>'
            . '<main class="webhooks-admin__content" aria-live="polite"><section class="webhooks-admin__overview-page" data-webhooks-overview-page><div class="webhooks-admin__overview-hero"><p class="webhooks-admin__eyebrow">INTEGRAÇÕES</p><h2>Webhooks</h2><p>Webhooks permitem integrar o Hardness a sistemas externos, enviando notificações automaticamente quando eventos ocorrem no ERP.</p><p>Escolha uma empresa no menu para criar ou administrar seus Webhooks.</p></div><article class="webhooks-admin__overview-guide"><h3>Como funciona</h3><ol><li>Escolha a empresa que terá os Webhooks administrados.</li><li>Crie um Webhook e defina os eventos que serão enviados.</li><li>Ative o destino quando a configuração estiver pronta.</li></ol></article></section><div class="webhooks-admin__loading" data-webhooks-loading hidden><span class="webhooks-admin__spinner" aria-hidden="true"></span><span>Carregando webhooks…</span></div>'
            . '<div class="webhooks-admin__list" data-webhooks-list hidden></div>'
            . '<section class="webhooks-admin__state" data-webhooks-empty hidden><div class="webhooks-admin__state-icon" aria-hidden="true">↗</div><h2>Nenhum Webhook configurado</h2><p>Crie um Webhook para começar a receber eventos da sua empresa em outro sistema.</p><button class="webhooks-admin__button webhooks-admin__button--primary" type="button" data-webhooks-placeholder="new">Novo Webhook</button></section>'
            . '<section class="webhooks-admin__state webhooks-admin__state--error" data-webhooks-error hidden><div class="webhooks-admin__state-icon" aria-hidden="true">!</div><h2>Não foi possível carregar os Webhooks</h2><p>Verifique sua conexão e tente novamente.</p><button class="webhooks-admin__button webhooks-admin__button--secondary" type="button" data-webhooks-retry>Tentar novamente</button></section>'
            . '<p class="webhooks-admin__notice" data-webhooks-notice hidden role="status"></p></main></div></section>'
            . '<script src="' . $javascript . '" defer></script>';
    }
}
