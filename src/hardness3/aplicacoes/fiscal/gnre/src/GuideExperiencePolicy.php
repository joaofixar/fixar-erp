<?php
declare(strict_types=1);

/**
 * Read-only policy for a GNRE guide page.  Callers must load every fact with
 * company-scoped queries before calling decide(); this class never opens a
 * connection, changes state, or interprets SOAP itself.
 */
final class GnreGuideExperiencePolicy
{
    public const TABS = ['summary', 'data_items', 'validation_xml', 'transmission_result', 'official_document', 'technical_history'];

    /** Conceptual routes for Tasks 26.2/26.5; public URLs remain untouched. */
    public static function routeContracts(): array
    {
        return [
            'overview'=>'/gnre', 'guides'=>'/gnre/guias', 'new_guide'=>'/gnre/nova',
            'summary'=>'/gnre/guias/{id}/resumo', 'data_items'=>'/gnre/guias/{id}/dados',
            'validation_xml'=>'/gnre/guias/{id}/validacao', 'transmission_result'=>'/gnre/guias/{id}/transmissao',
            'official_document'=>'/gnre/guias/{id}/documento', 'technical_history'=>'/gnre/guias/{id}/historico',
            'technical_configuration'=>'/gnre/configuracao',
        ];
    }

    /**
     * $facts is deliberately an already-authorized read model:
     * company_authorized, snapshot_owned, item_count, xml_valid_local,
     * result (GnreResultInterpreter output), attempts, queue, official_pdf_id,
     * correction_draft_id. Missing facts are treated conservatively.
     */
    public static function decide(array $guide, array $facts = []): array
    {
        $authorized = ($facts['company_authorized'] ?? true) === true;
        $snapshot = (int)($guide['GNRE001_GNRE007_Id'] ?? 0) > 0 && ($facts['snapshot_owned'] ?? true) === true;
        $type = trim((string)($guide['GNRE001_Tipo_GNRE'] ?? ''));
        $items = max(0, (int)($facts['item_count'] ?? 0));
        $xmlValid = ($facts['xml_valid_local'] ?? false) === true;
        $status = (string)($guide['GNRE001_Status'] ?? '');
        $result = is_array($facts['result'] ?? null) ? $facts['result'] : null;
        $codes = self::individualCodes($result);
        $pdfId = (int)($facts['official_pdf_id'] ?? 0);

        if (!$authorized) $state = 'inaccessible';
        elseif (!$snapshot || $type === '') $state = 'legacy_blocked';
        elseif (in_array('0', $codes, true)) $state = 'approved';
        elseif ($status === 'R' && self::isExclusiveFinalRejection($codes)) $state = 'draft';
        elseif ($status === 'F' && self::isExclusiveFinalRejection($codes)) $state = 'rejected';
        elseif (self::hasFinalRejection($codes)) $state = 'inconclusive';
        elseif (in_array('3', $codes, true) || $status === 'I' || self::attemptState($facts, 'INCONCLUSIVA')) $state = 'inconclusive';
        elseif ($status === 'P' || self::attemptState($facts, 'RECEBIDA')) $state = 'awaiting_result';
        elseif (self::attemptState($facts, 'REJEITADA')) $state = 'reception_rejected';
        elseif ($status === 'R') $state = 'draft';
        else $state = 'inconclusive';

        $allowed = self::allowed($state, $items, $xmlValid, $pdfId, (int)($facts['correction_draft_id'] ?? 0));
        $blocked = self::blocked($allowed, $state, $items, $xmlValid, $pdfId);
        $tabs = self::tabs($state, $allowed, $blocked);
        $primary = self::primary($state, $allowed, (int)($facts['correction_draft_id'] ?? 0));

        return [
            'state'=>$state,
            'state_label'=>self::label($state),
            'tabs'=>$tabs,
            'recommended_tab'=>$primary['tab'],
            'primary_action'=>$primary['action'],
            'allowed_actions'=>array_keys(array_filter($allowed)),
            'blocked_actions'=>$blocked,
            'can_edit_data'=>$allowed['edit_data'],
            'can_generate_xml'=>$allowed['generate_xml'],
            'can_transmit'=>$allowed['transmit'],
            'can_consult_result'=>$allowed['consult_result'],
            'can_obtain_official_pdf'=>$allowed['obtain_official_pdf'],
            'can_download_official_pdf'=>$allowed['download_official_pdf'],
            'can_start_correction'=>false,
            'can_reopen_rejected_guide'=>$allowed['reopen_rejected_guide'],
            'route_contracts'=>self::routeContracts(),
        ];
    }

    private static function individualCodes(?array $result): array
    {
        if (!$result || empty($result['valid'])) return [];
        $codes=[]; foreach ((array)($result['guides'] ?? []) as $row) { $code=trim((string)($row['code'] ?? '')); if ($code !== '') $codes[]=$code; }
        return $codes;
    }
    private static function hasFinalRejection(array $codes): bool { return (bool)array_intersect($codes, ['1', '2']); }
    private static function isExclusiveFinalRejection(array $codes): bool { return count($codes)===1&&in_array((string)$codes[0],['1','2'],true); }
    private static function attemptState(array $facts, string $wanted): bool
    {
        foreach ((array)($facts['attempts'] ?? []) as $attempt) if ((string)($attempt['GNRE004_Estado'] ?? $attempt['state'] ?? '') === $wanted) return true;
        return false;
    }
    private static function allowed(string $state, int $items, bool $xmlValid, int $pdfId, int $correctionId): array
    {
        $draft = $state === 'draft';
        return [
            'edit_data'=>$draft,
            'generate_xml'=>$draft && $items > 0,
            'transmit'=>$draft && $items > 0 && $xmlValid,
            'consult_result'=>$state === 'awaiting_result',
            'obtain_official_pdf'=>$state === 'approved' && $pdfId <= 0,
            'download_official_pdf'=>$state === 'approved' && $pdfId > 0,
            'reopen_rejected_guide'=>$state === 'rejected',
            'view_history'=>$state !== 'inaccessible',
        ];
    }
    private static function blocked(array $allowed, string $state, int $items, bool $xmlValid, int $pdfId): array
    {
        $reasons=[];
        foreach ($allowed as $action=>$ok) if (!$ok) $reasons[$action]=self::reason($action, $state, $items, $xmlValid, $pdfId);
        return $reasons;
    }
    private static function reason(string $action, string $state, int $items, bool $xmlValid, int $pdfId): string
    {
        if ($state === 'inaccessible') return 'A guia não pertence à empresa GNRE ativa ou não está autorizada.';
        if ($state === 'legacy_blocked') return 'A guia não possui tipo oficial e snapshot compatível vinculados.';
        if ($action === 'generate_xml' && $items === 0) return 'Inclua ao menos um item antes de gerar ou validar o XML.';
        if ($action === 'transmit' && !$xmlValid) return 'A transmissão exige XML local válido e atual.';
        if ($action === 'consult_result') return 'A consulta só é possível após recebimento de recibo e enquanto o resultado estiver pendente.';
        if ($action === 'obtain_official_pdf' && $state === 'approved' && $pdfId > 0) return 'O PDF oficial já está armazenado para esta guia.';
        if ($action === 'download_official_pdf') return 'Ainda não há PDF oficial persistido para download.';
        if ($action === 'reopen_rejected_guide' && $state === 'rejected') return 'A reabertura preserva a mesma guia e seu histórico técnico.';
        if ($state === 'awaiting_result') return 'A guia aguarda resultado; edição e retransmissão permanecem bloqueadas.';
        if ($state === 'reception_rejected') return 'A recepção foi rejeitada sem recibo; não há resultado individual para consulta.';
        if ($state === 'inconclusive') return 'O estado é inconclusivo; a política não libera reenvio automático.';
        if (in_array($state, ['approved', 'rejected'], true)) return 'A guia possui resultado individual final e preserva seu histórico.';
        return 'A ação não é aplicável ao estado atual da guia.';
    }
    private static function tabs(string $state, array $allowed, array $blocked): array
    {
        $out=[]; foreach (self::TABS as $tab) {
            $available = $tab === 'summary' || $tab === 'technical_history' || ($tab === 'data_items' && $allowed['edit_data']) || ($tab === 'validation_xml' && $state !== 'inaccessible') || ($tab === 'transmission_result' && !in_array($state, ['draft', 'legacy_blocked', 'inaccessible'], true)) || ($tab === 'official_document' && $state === 'approved');
            $out[$tab]=['visible'=>true, 'available'=>$available, 'reason'=>$available ? '' : self::tabReason($tab, $state, $blocked)];
        }
        return $out;
    }
    private static function tabReason(string $tab, string $state, array $blocked): string
    {
        if ($tab === 'data_items') return $blocked['edit_data'] ?? 'Dados e itens não podem ser editados neste estado.';
        if ($tab === 'official_document') return $blocked['obtain_official_pdf'] ?? 'Documento oficial disponível somente após aprovação individual.';
        if ($tab === 'transmission_result') return 'Transmissão e resultado serão disponibilizados quando a guia tiver uma tentativa aplicável.';
        return $state === 'legacy_blocked' ? 'A guia legada não possui snapshot/tipo compatível.' : 'A aba não é aplicável ao estado atual.';
    }
    private static function primary(string $state, array $allowed, int $correctionId): array
    {
        if ($state === 'draft') return ['action'=>'edit_data', 'tab'=>'data_items'];
        if ($state === 'awaiting_result') return ['action'=>'consult_result', 'tab'=>'transmission_result'];
        if ($state === 'approved') return ['action'=>$allowed['download_official_pdf']?'download_official_pdf':'obtain_official_pdf', 'tab'=>'official_document'];
        if ($state === 'rejected') return ['action'=>'reopen_rejected_guide', 'tab'=>'summary'];
        return ['action'=>'view_history', 'tab'=>'technical_history'];
    }
    private static function label(string $state): string
    {
        return ['draft'=>'Rascunho','awaiting_result'=>'Aguardando resultado','approved'=>'Aprovada/emitida','rejected'=>'Rejeitada','reception_rejected'=>'Recepção rejeitada','inconclusive'=>'Inconclusiva','legacy_blocked'=>'Legada/incompatível ou bloqueada','inaccessible'=>'Não acessível'][$state] ?? 'Inconclusiva';
    }
}
