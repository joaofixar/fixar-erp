<?php
declare(strict_types=1);
require_once __DIR__ . '/EnvironmentResolver.php';

/** Documento original retornado pelo Portal, deliberadamente separado da fila. */
final class GnreOfficialPdfService
{
    public const MAX_BYTES = 10 * 1024 * 1024;
    /** @var callable|null */ private $consult;
    public function __construct(?callable $consult = null) { $this->consult = $consult; }

    /** @return array{status:string,message:string,document_id?:int} */
    public function obtain(int $companyId, int $guideId, int $userId): array
    {
        $source = $this->approvedSource($companyId, $guideId);
        $lockPath = sys_get_temp_dir() . '/gnre-pdf-' . $companyId . '-' . $guideId . '.lock';
        $lock = @fopen($lockPath, 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) { if ($lock) fclose($lock); return ['status'=>'busy','message'=>'Já existe uma obtenção de PDF oficial em andamento para esta guia.']; }
        try {
            if (($existing = $this->documentId($companyId, $guideId)) > 0) return ['status'=>'existing','message'=>'O PDF oficial já está armazenado.','document_id'=>$existing];
            $result = $this->consult ? call_user_func($this->consult, $companyId, $source['receipt'], $source['environment']) : (new GnreResultClient())->consultOfficialPdf($companyId, $source['receipt'], $source['environment']);
            if (($result['kind'] ?? '') !== 'final') throw new RuntimeException('O Portal não confirmou um resultado final para obter o PDF oficial.');
            $pdf = self::validateSoap((string)($result['soap'] ?? ''));
            return $this->persist($companyId, $guideId, $source, $pdf, $userId);
        } finally { flock($lock, LOCK_UN); fclose($lock); }
    }

    /** Valida o retorno da consulta com PDF sem interpretar ou transformar o binário. */
    public static function validateSoap(string $soap): array
    {
        if ($soap === '' || stripos($soap, '<!DOCTYPE') !== false) throw new RuntimeException('Resposta do Portal sem PDF oficial válido.');
        if (!class_exists('DOMDocument')) return self::validateSoapWithoutDom($soap);
        $old=libxml_use_internal_errors(true); $dom=new DOMDocument(); $ok=$dom->loadXML($soap, LIBXML_NONET); libxml_clear_errors(); libxml_use_internal_errors($old);
        if (!$ok) throw new RuntimeException('Resposta XML do Portal inválida para o PDF oficial.');
        $xp=new DOMXPath($dom); $guides=$xp->query('//*[local-name()="TResultLote_GNRE"]/*[local-name()="resultado"]/*[local-name()="guia"]');
        if (!$guides || $guides->length !== 1 || trim((string)$xp->evaluate('string(./*[local-name()="situacaoGuia"])',$guides->item(0))) !== '0') throw new RuntimeException('O Portal não confirmou individualmente a guia como aprovada.');
        $encoded=trim((string)$xp->evaluate('string(//*[local-name()="TResultLote_GNRE"]/*[local-name()="resultado"]/*[local-name()="pdfGuias"])'));
        if ($encoded === '') throw new RuntimeException('O Portal não retornou PDF oficial para esta guia.');
        $binary=base64_decode(preg_replace('/\s+/', '', $encoded), true);
        if ($binary === false || $binary === '') throw new RuntimeException('PDF oficial retornado pelo Portal possui base64 inválido.');
        if (strlen($binary) > self::MAX_BYTES) throw new RuntimeException('PDF oficial excede o limite de 10 MiB.');
        if (substr($binary, 0, 5) !== '%PDF-') throw new RuntimeException('Conteúdo retornado pelo Portal não é um PDF válido.');
        return ['binary'=>$binary,'mime'=>'application/pdf','size'=>strlen($binary),'sha256'=>hash('sha256',$binary)];
    }

    /** Fallback compatível com instalações PHP do ERP sem ext-dom; entidades já são recusadas acima. */
    private static function validateSoapWithoutDom(string $soap): array
    {
        if (!preg_match('~<(?:[A-Za-z_][\\w.-]*:)?TResultLote_GNRE\\b[^>]*>(.*?)</(?:[A-Za-z_][\\w.-]*:)?TResultLote_GNRE\\s*>~si',$soap,$root)) throw new RuntimeException('Resposta XML do Portal inválida para o PDF oficial.');
        if (!preg_match('~<(?:[A-Za-z_][\\w.-]*:)?resultado\\b[^>]*>(.*?)</(?:[A-Za-z_][\\w.-]*:)?resultado\\s*>~si',$root[1],$result)) throw new RuntimeException('O Portal não confirmou individualmente a guia como aprovada.');
        if (!preg_match_all('~<(?:[A-Za-z_][\\w.-]*:)?guia\\b[^>]*>(.*?)</(?:[A-Za-z_][\\w.-]*:)?guia\\s*>~si',$result[1],$guides) || count($guides[1])!==1 || !preg_match('~<(?:[A-Za-z_][\\w.-]*:)?situacaoGuia\\b[^>]*>\\s*0\\s*</(?:[A-Za-z_][\\w.-]*:)?situacaoGuia\\s*>~si',$guides[1][0])) throw new RuntimeException('O Portal não confirmou individualmente a guia como aprovada.');
        if (!preg_match('~<(?:[A-Za-z_][\\w.-]*:)?pdfGuias\\b[^>]*>(.*?)</(?:[A-Za-z_][\\w.-]*:)?pdfGuias\\s*>~si',$result[1],$match)) throw new RuntimeException('O Portal não retornou PDF oficial para esta guia.');
        return self::validateEncodedPdf($match[1]);
    }
    private static function validateEncodedPdf(string $encoded): array
    {
        $encoded=trim($encoded); if ($encoded === '') throw new RuntimeException('O Portal não retornou PDF oficial para esta guia.');
        $binary=base64_decode(preg_replace('/\\s+/', '', $encoded), true);
        if ($binary === false || $binary === '') throw new RuntimeException('PDF oficial retornado pelo Portal possui base64 inválido.');
        if (strlen($binary) > self::MAX_BYTES) throw new RuntimeException('PDF oficial excede o limite de 10 MiB.');
        if (substr($binary, 0, 5) !== '%PDF-') throw new RuntimeException('Conteúdo retornado pelo Portal não é um PDF válido.');
        return ['binary'=>$binary,'mime'=>'application/pdf','size'=>strlen($binary),'sha256'=>hash('sha256',$binary)];
    }

    private function approvedSource(int $companyId, int $guideId): array
    {
        $db=gnre_connect(); if(!$db) throw new RuntimeException('Base ERP indisponível.');
        try { $s=mysqli_prepare($db,"SELECT t.GNRE004_Id,t.GNRE004_Recibo,q.GNRE005_Ultima_Consulta_SOAP,s.GNRE007_Ambiente FROM GNRE001 g JOIN GNRE007 s ON s.GNRE007_Id=g.GNRE001_GNRE007_Id AND s.GNRE007_C004_Id=g.GNRE001_C004_Id JOIN GNRE004 t ON t.GNRE004_GNRE001_Id=g.GNRE001_Id AND t.GNRE004_Estado='RECEBIDA' JOIN GNRE005 q ON q.GNRE005_GNRE004_Id=t.GNRE004_Id AND q.GNRE005_Recibo=t.GNRE004_Recibo AND q.GNRE005_Situacao='FINALIZADA' WHERE g.GNRE001_Id=? AND g.GNRE001_C004_Id=? ORDER BY q.GNRE005_Id DESC LIMIT 1"); if(!$s) throw new RuntimeException('Não foi possível validar a recepção da guia.'); mysqli_stmt_bind_param($s,'ii',$guideId,$companyId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s); }
        finally { mysqli_close($db); }
        if(!$row) throw new RuntimeException('A guia não possui resultado individual final confirmado para esta empresa.');
        $parsed=GnreResultInterpreter::interpret((string)$row['GNRE005_Ultima_Consulta_SOAP']);
        if(!$parsed['valid'] || count($parsed['guides'])!==1 || (string)$parsed['guides'][0]['code']!=='0') throw new RuntimeException('PDF oficial só pode ser obtido após a aprovação individual (situação 0).');
        return ['attempt_id'=>(int)$row['GNRE004_Id'],'receipt'=>(string)$row['GNRE004_Recibo'],'environment'=>GnreEnvironmentResolver::assertOperationalAllowed((string)$row['GNRE007_Ambiente'])];
    }
    private function documentId(int $companyId,int $guideId): int { $db=gnre_connect();if(!$db)throw new RuntimeException('Base ERP indisponível.');try{$s=mysqli_prepare($db,'SELECT GNRE008_Id FROM GNRE008 WHERE GNRE008_C004_Id=? AND GNRE008_GNRE001_Id=? ORDER BY GNRE008_Id DESC LIMIT 1');mysqli_stmt_bind_param($s,'ii',$companyId,$guideId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$x=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);return (int)($x['GNRE008_Id']??0);}finally{mysqli_close($db);} }
    private function persist(int $companyId,int $guideId,array $source,array $pdf,int $userId): array { $db=gnre_connect();if(!$db)throw new RuntimeException('Base ERP indisponível.');try{mysqli_begin_transaction($db);$s=mysqli_prepare($db,"SELECT 1 FROM GNRE001 g JOIN GNRE004 t ON t.GNRE004_GNRE001_Id=g.GNRE001_Id AND t.GNRE004_Id=? AND t.GNRE004_Recibo=? JOIN GNRE005 q ON q.GNRE005_GNRE004_Id=t.GNRE004_Id AND q.GNRE005_Situacao='FINALIZADA' WHERE g.GNRE001_Id=? AND g.GNRE001_C004_Id=? FOR UPDATE");mysqli_stmt_bind_param($s,'isii',$source['attempt_id'],$source['receipt'],$guideId,$companyId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$valid=$r&&mysqli_fetch_row($r);mysqli_stmt_close($s);if(!$valid)throw new RuntimeException('O vínculo da guia foi alterado durante a obtenção do PDF.');$s=mysqli_prepare($db,"INSERT IGNORE INTO GNRE008 (GNRE008_C004_Id,GNRE008_GNRE001_Id,GNRE008_GNRE004_Id,GNRE008_Recibo,GNRE008_Tipo,GNRE008_Origem,GNRE008_Conteudo,GNRE008_Mime,GNRE008_Tamanho,GNRE008_SHA256,GNRE008_Data_Hora_Obtencao,GNRE008_C007_Id_Solicitante) VALUES (?,?,?,?,'PDF_OFICIAL','PORTAL_GNRE',?,?,?, ?,NOW(),?)");mysqli_stmt_bind_param($s,'iiisssisi',$companyId,$guideId,$source['attempt_id'],$source['receipt'],$pdf['binary'],$pdf['mime'],$pdf['size'],$pdf['sha256'],$userId);if(!mysqli_stmt_execute($s))throw new RuntimeException('Não foi possível armazenar o PDF oficial.');$id=(int)mysqli_insert_id($db);mysqli_stmt_close($s);if(!$id){$s=mysqli_prepare($db,'SELECT GNRE008_Id FROM GNRE008 WHERE GNRE008_GNRE004_Id=? AND GNRE008_SHA256=? LIMIT 1');mysqli_stmt_bind_param($s,'is',$source['attempt_id'],$pdf['sha256']);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);$id=(int)($row['GNRE008_Id']??0);}mysqli_commit($db);return ['status'=>'stored','message'=>'PDF oficial retornado pelo Portal Nacional GNRE foi armazenado.','document_id'=>$id];}catch(Throwable $e){mysqli_rollback($db);throw $e;}finally{mysqli_close($db);} }
    public static function download(int $companyId,int $guideId,int $documentId): ?array { $db=gnre_connect();if(!$db)throw new RuntimeException('Base ERP indisponível.');try{$s=mysqli_prepare($db,"SELECT d.GNRE008_Conteudo,d.GNRE008_Mime,d.GNRE008_SHA256 FROM GNRE008 d JOIN GNRE001 g ON g.GNRE001_Id=d.GNRE008_GNRE001_Id WHERE d.GNRE008_Id=? AND d.GNRE008_GNRE001_Id=? AND d.GNRE008_C004_Id=? AND g.GNRE001_C004_Id=? AND d.GNRE008_Tipo='PDF_OFICIAL' AND d.GNRE008_Origem='PORTAL_GNRE' LIMIT 1");mysqli_stmt_bind_param($s,'iiii',$documentId,$guideId,$companyId,$companyId);mysqli_stmt_execute($s);$r=mysqli_stmt_get_result($s);$row=$r?mysqli_fetch_assoc($r):null;mysqli_stmt_close($s);return $row?:null;}finally{mysqli_close($db);} }
}
