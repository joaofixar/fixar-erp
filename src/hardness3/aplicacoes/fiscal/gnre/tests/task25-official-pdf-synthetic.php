<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/OfficialPdfService.php';

function t25(bool $ok, string $message): void { if (!$ok) { fwrite(STDERR, "FALHOU: $message\n"); exit(1); } }
function t25soap(string $status, string $pdf): string { return '<soap:Envelope xmlns:soap="urn:test" xmlns="http://www.gnre.pe.gov.br"><soap:Body><TResultLote_GNRE><resultado><guia><situacaoGuia>'.$status.'</situacaoGuia></guia><pdfGuias>'.$pdf.'</pdfGuias></resultado></TResultLote_GNRE></soap:Body></soap:Envelope>'; }
function t25reject(string $soap, string $message): void { try { GnreOfficialPdfService::validateSoap($soap); } catch (RuntimeException $e) { return; } t25(false, $message); }

$valid=base64_encode("%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF\n");
$parsed=GnreOfficialPdfService::validateSoap(t25soap('0',$valid));
t25($parsed['mime']==='application/pdf' && $parsed['size']>5 && preg_match('/^[a-f0-9]{64}$/',$parsed['sha256'])===1,'PDF base64 aprovado deve ser aceito, identificado e hasheado.');
t25reject(t25soap('1',$valid),'situação individual diferente de 0 deve bloquear PDF.');
t25reject(t25soap('0','!base64!'),'base64 inválido deve ser rejeitado.');
t25reject(t25soap('0',base64_encode('texto comum')),'conteúdo não-PDF deve ser rejeitado.');
t25reject(t25soap('0',base64_encode('%PDF-'.str_repeat('A',GnreOfficialPdfService::MAX_BYTES))),'PDF acima do limite deve ser rejeitado.');
$ui=(string)file_get_contents(__DIR__.'/../public/index.php').(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
t25(strpos($ui,'obtain_official_pdf')!==false&&strpos($ui,'Obter PDF oficial')!==false,'guia emitida sem PDF oferece obtenção explícita.');
t25(strpos($ui,'A emissão foi confirmada, mas o PDF oficial não pôde ser obtido agora')===false,'consulta manual não deve chamar o Portal para obter PDF automaticamente.');
echo "OK Task 25: validações de PDF oficial sintéticas passaram.\n";
