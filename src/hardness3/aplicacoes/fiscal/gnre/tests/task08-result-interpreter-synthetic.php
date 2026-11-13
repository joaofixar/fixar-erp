<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/ResultInterpreter.php';

function task08_soap(string $guide, string $reasons = ''): string { return '<soap:Envelope xmlns:soap="urn:test" xmlns="http://www.gnre.pe.gov.br"><soap:Body><TResultLote_GNRE><ambiente>2</ambiente><situacaoProcess><codigo>403</codigo><descricao>Lote Processado com pendencias</descricao></situacaoProcess><resultado><guia><situacaoGuia>'.$guide.'</situacaoGuia>'.$reasons.'</guia></resultado></TResultLote_GNRE></soap:Body></soap:Envelope>'; }
$processing = GnreResultInterpreter::interpret(task08_soap('4'));
if (!$processing['valid'] || $processing['guides'][0]['outcome'] !== 'processing') throw new RuntimeException('Guia em processamento não interpretada.');
$issued = GnreResultInterpreter::interpret(task08_soap('0'));
if ($issued['guides'][0]['outcome'] !== 'issued' || $issued['guides'][0]['rejections']) throw new RuntimeException('Guia final sem rejeição não interpretada.');
$reasons = '<motivosRejeicao><motivo><codigo>210</codigo><descricao>Emitente ausente</descricao><campo>contribuinteEmitente</campo></motivo><motivo><codigo>211</codigo><descricao>Receita não gerada pela UF favorecida</descricao></motivo></motivosRejeicao>';
$rejected = GnreResultInterpreter::interpret(task08_soap('1', $reasons));
if ($rejected['guides'][0]['outcome'] !== 'rejected' || count($rejected['guides'][0]['rejections']) !== 2 || $rejected['guides'][0]['rejections'][1]['field'] !== '') throw new RuntimeException('Motivos de rejeição não interpretados.');
$invalid = GnreResultInterpreter::interpret('<soap>');
if ($invalid['valid']) throw new RuntimeException('SOAP inválido foi aceito.');
$absent = GnreResultInterpreter::interpret('');
if ($absent['valid']) throw new RuntimeException('SOAP ausente foi aceito.');
$ui = (string) file_get_contents(__DIR__ . '/../public/index.php') . (string) file_get_contents(__DIR__ . '/../public/task10-presentation.php');
foreach (['GnreResultInterpreter::interpret', 'Situação do lote', 'Situação individual da guia', 'Motivos de rejeição', 'situação individual abaixo define se houve emissão/autorização'] as $needle) if (strpos($ui, $needle) === false) throw new RuntimeException('Interface não exibe: '.$needle);
echo "Task 08 synthetic checks OK\n";
