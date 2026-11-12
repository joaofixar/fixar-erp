<?php
namespace hardness;

/**
 * Eventos posteriores à autorização da NF-e.
 *
 * A classe concentra as consultas e regras de negócio dos eventos RTC.
 * A interface deve utilizá-la por endpoints AJAX; ela não gera HTML.
 */
class classeNFeEventos
{
    const TIPO_EVENTO_FORNECIMENTO_NAO_REALIZADO = '112140';
    const TIPO_EVENTO_CANCELAMENTO = '110001';
    const STATUS_AUTORIZADO = 'AUTORIZADO';

    const URL_EVENTOS_RTC_PRODUCAO = 'https://nfe.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx';
    const URL_EVENTOS_RTC_HOMOLOGACAO = 'https://nfe-homologacao.svrs.rs.gov.br/ws/recepcaoevento/recepcaoevento4.asmx';

    public function gerarXmlEvento($T321_Id)
    {
        $T321_Id = (int)$T321_Id;
        $sql = "SELECT
                    T321.*,
                    T007_C004_Id
                FROM T321
                INNER JOIN T007 ON T007_Id = T321_T007_Id
                WHERE T321_Id = '{$T321_Id}'";
        $evento = mysql_fetch_assoc(mysql_query($sql));

        if (empty($evento)) {
            return array('sucesso' => false, 'mensagem' => 'Evento não encontrado.');
        }

        if ($evento['T321_Status'] != 'RASCUNHO'
            && $evento['T321_Status'] != 'PRONTO_ENVIO'
            && $evento['T321_Status'] != 'REJEITADO'
            && $evento['T321_Status'] != 'ERRO_COMUNICACAO') {
            return array('sucesso' => false, 'mensagem' => 'O XML só pode ser gerado antes da transmissão do evento.');
        }

        switch ($evento['T321_Tipo_Evento']) {
            case self::TIPO_EVENTO_FORNECIMENTO_NAO_REALIZADO:
                return $this->gerarXml112140($evento);

            case self::TIPO_EVENTO_CANCELAMENTO:
                return $this->gerarXmlCancelamento($evento);

            default:
                return array('sucesso' => false, 'mensagem' => 'Tipo de evento ainda não suportado.');
        }
    }

    /**
     * Fluxo utilizado pela interface: sempre assina um XML novo antes de enviar.
     * A geração isolada continua disponível somente como etapa interna.
     */
    public function gerarETransmitirEvento($T321_Id)
    {
        $geracao = $this->gerarXmlEvento($T321_Id);
        if (empty($geracao['sucesso'])) {
            return $geracao;
        }

        return $this->transmitirEvento($T321_Id);
    }

    /** Cria o evento 110001 vinculado a um evento já autorizado. */
    public function criarCancelamentoEvento($T321_IdOriginal, $C007_Id)
    {
        $T321_IdOriginal = (int)$T321_IdOriginal;
        $C007_Id = (int)$C007_Id;
        $sql = "SELECT T321.*
                FROM T321
                WHERE T321_Id = '{$T321_IdOriginal}'";
        $original = mysql_fetch_assoc(mysql_query($sql));

        if (empty($original)) {
            return array('sucesso' => false, 'mensagem' => 'Evento original não encontrado.');
        }

        if ($original['T321_Tipo_Evento'] == self::TIPO_EVENTO_CANCELAMENTO) {
            return array('sucesso' => false, 'mensagem' => 'Não é permitido cancelar um evento de cancelamento.');
        }

        if ($original['T321_Status'] != self::STATUS_AUTORIZADO || empty($original['T321_Protocolo'])) {
            return array('sucesso' => false, 'mensagem' => 'Somente eventos autorizados podem ser cancelados.');
        }

        $sqlExistente = "SELECT T321_Id
                         FROM T321
                         WHERE T321_Tipo_Evento = '" . self::TIPO_EVENTO_CANCELAMENTO . "'
                           AND T321_T321_Evento_Original_Id = '{$T321_IdOriginal}'
                         LIMIT 1";
        if (mysql_num_rows(mysql_query($sqlExistente))) {
            return array('sucesso' => false, 'mensagem' => 'Já existe um evento de cancelamento para esta ocorrência.');
        }

        $sqlInserir = "INSERT INTO T321 (
                            T321_T007_Id,
                            T321_C007_Id,
                            T321_Tipo_Evento,
                            T321_Sequencial_Evento,
                            T321_Status,
                            T321_Ambiente,
                            T321_Chave_NFe,
                            T321_Data_Hora_Inclusao,
                            T321_T321_Evento_Original_Id
                        ) VALUES (
                            '{$original['T321_T007_Id']}',
                            '{$C007_Id}',
                            '" . self::TIPO_EVENTO_CANCELAMENTO . "',
                            '{$original['T321_Sequencial_Evento']}',
                            'RASCUNHO',
                            '{$original['T321_Ambiente']}',
                            '" . mysql_real_escape_string($original['T321_Chave_NFe']) . "',
                            NOW(),
                            '{$T321_IdOriginal}'
                        )";
        if (!mysql_query($sqlInserir)) {
            return array('sucesso' => false, 'mensagem' => 'Não foi possível criar o evento de cancelamento: ' . mysql_error());
        }

        return array(
            'sucesso' => true,
            'mensagem' => 'Evento de cancelamento incluído como rascunho.',
            'T321_Id' => mysql_insert_id(),
        );
    }

    /**
     * Transmite um evento já gerado e assinado.
     *
     * O transporte é comum a qualquer evento. A definição de qual serviço
     * utilizar fica concentrada em resolverServicoEvento().
     */
    public function transmitirEvento($T321_Id)
    {
        $T321_Id = (int)$T321_Id;
        $sql = "SELECT
                    T321.*,
                    T007_C004_Id
                FROM T321
                INNER JOIN T007 ON T007_Id = T321_T007_Id
                WHERE T321_Id = '{$T321_Id}'";
        $evento = mysql_fetch_assoc(mysql_query($sql));

        if (empty($evento)) {
            return array('sucesso' => false, 'mensagem' => 'Evento não encontrado.');
        }

        if ($evento['T321_Status'] != 'PRONTO_ENVIO' && $evento['T321_Status'] != 'ERRO_COMUNICACAO') {
            return array('sucesso' => false, 'mensagem' => 'O evento não está disponível para transmissão.');
        }

        if (empty($evento['T321_XML_Envio'])) {
            return array('sucesso' => false, 'mensagem' => 'Gere o XML assinado antes de transmitir o evento.');
        }

        $servico = $this->resolverServicoEvento($evento['T321_Tipo_Evento'], $evento['T321_Ambiente']);
        if (!$servico['sucesso']) {
            return $servico;
        }

        mysql_query("UPDATE T321
                     SET T321_Status = 'PENDENTE',
                         T321_Data_Hora_Ultima_Tentativa = NOW(),
                         T321_Quantidade_Tentativas = T321_Quantidade_Tentativas + 1
                     WHERE T321_Id = '{$evento['T321_Id']}'");

        $lote = $this->montarLoteEvento($evento['T321_XML_Envio']);
        $retornoSoap = $this->enviarSoapEvento(
            $servico['url'],
            substr($evento['T321_Chave_NFe'], 0, 2),
            $lote,
            $evento['T007_C004_Id']
        );

        if (!$retornoSoap['sucesso']) {
            $this->atualizarRetornoEvento($evento['T321_Id'], 'ERRO_COMUNICACAO', '', $retornoSoap['mensagem'], $retornoSoap['xml']);
            return array('sucesso' => false, 'mensagem' => $retornoSoap['mensagem']);
        }

        return $this->processarRetornoEvento($evento, $retornoSoap['xml']);
    }

    /** Resolve a rota conforme a família do evento, sem acoplamento ao 112140. */
    private function resolverServicoEvento($tipoEvento, $ambiente)
    {
        $eventosRtc = array(
            '110001',
            '112110', '112120', '112130', '112140', '112150',
            '211110', '211120', '211124', '211128', '211130', '211140', '211150',
            '212110', '212120',
            '412120', '412130',
        );

        if (!in_array($tipoEvento, $eventosRtc)) {
            return array('sucesso' => false, 'mensagem' => 'Não existe rota de transmissão configurada para este tipo de evento.');
        }

        if ($ambiente != '1' && $ambiente != '2') {
            return array('sucesso' => false, 'mensagem' => 'Ambiente do evento não é válido.');
        }

        return array(
            'sucesso' => true,
            'url' => ($ambiente == '1') ? self::URL_EVENTOS_RTC_PRODUCAO : self::URL_EVENTOS_RTC_HOMOLOGACAO,
        );
    }

    /** Monta um lote de um único evento, facilitando o controle de retorno. */
    private function montarLoteEvento($xmlEvento)
    {
        $xmlEvento = preg_replace('/^\s*<\?xml[^>]*\?>\s*/', '', $xmlEvento);
        // O XML pode ser recuperado de arquivo ou banco com CR/LF ao redor
        // ou entre elementos. A SEFAZ rejeita esses caracteres de edição no
        // envEvento (588). Não altera conteúdo de tags, apenas formatação.
        $xmlEvento = preg_replace('/>\s+</', '><', trim($xmlEvento));

        return '<envEvento xmlns="http://www.portalfiscal.inf.br/nfe" versao="1.00">'
            . '<idLote>1</idLote>'
            . $xmlEvento
            . '</envEvento>';
    }

    /** Envia o lote pela comunicação SOAP 1.2 com certificado cliente. */
    private function enviarSoapEvento($url, $codigoUf, $lote, $C004_Id)
    {
        $certificado = $this->buscarCertificadoTlsEvento($C004_Id);
        if (!$certificado['sucesso']) {
            return $certificado;
        }

        $namespace = 'http://www.portalfiscal.inf.br/nfe/wsdl/NFeRecepcaoEvento4';
        $cabecalho = '<nfeCabecMsg xmlns="' . $namespace . '">'
            . '<cUF>' . $codigoUf . '</cUF>'
            . '<versaoDados>1.00</versaoDados>'
            . '</nfeCabecMsg>';
        $dados = '<nfeDadosMsg xmlns="' . $namespace . '">' . $lote . '</nfeDadosMsg>';
        $soap = '<?xml version="1.0" encoding="utf-8"?>'
            . '<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">'
            . '<soap12:Header>' . $cabecalho . '</soap12:Header>'
            . '<soap12:Body>' . $dados . '</soap12:Body>'
            . '</soap12:Envelope>';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $soap);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 25);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        // Mantém a mesma compatibilidade TLS do transporte de CC-e já usado
        // pelo projeto. A SVRS de homologação não apresenta cadeia completa
        // para o repositório de CAs deste ambiente legado.
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($curl, CURLOPT_SSLCERT, $certificado['certificado']);

        if (!empty($certificado['chave'])) {
            curl_setopt($curl, CURLOPT_SSLKEY, $certificado['chave']);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/soap+xml; charset=utf-8; action="' . $namespace . '/RecepcaoEvento"',
            'SOAPAction: "RecepcaoEvento"',
            'Content-Length: ' . strlen($soap),
        ));

        $xmlRetorno = curl_exec($curl);
        $erro = curl_error($curl);
        $httpCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($xmlRetorno === false) {
            return array('sucesso' => false, 'mensagem' => 'Falha de comunicação com a SEFAZ: ' . $erro, 'xml' => '');
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            return array('sucesso' => false, 'mensagem' => 'A SEFAZ respondeu HTTP ' . $httpCode . '.', 'xml' => $xmlRetorno);
        }

        return array('sucesso' => true, 'xml' => $xmlRetorno);
    }

    /** Localiza os arquivos PEM já mantidos pelo cadastro de certificado da empresa. */
    private function buscarCertificadoTlsEvento($C004_Id)
    {
        $C004_Id = (int)$C004_Id;
        $empresa = mysql_fetch_assoc(mysql_query("SELECT C004_Localizacao_Certificados FROM C004 WHERE C004_Id = '{$C004_Id}'"));
        if (empty($empresa)) {
            return array('sucesso' => false, 'mensagem' => 'Configuração de certificado não encontrada.');
        }

        $base = rtrim($empresa['C004_Localizacao_Certificados'], '/') . '/' . $C004_Id;
        $arquivoPem = $base . '.pem';
        $arquivoCert = $base . '_cert.pem';
        $arquivoChave = $base . '_key.pem';

        if (file_exists($arquivoCert) && file_exists($arquivoChave)) {
            return array('sucesso' => true, 'certificado' => $arquivoCert, 'chave' => $arquivoChave);
        }

        if (file_exists($arquivoPem)) {
            return array('sucesso' => true, 'certificado' => $arquivoPem, 'chave' => '');
        }

        return array('sucesso' => false, 'mensagem' => 'Arquivos PEM do certificado não encontrados para transmissão.');
    }

    /** Interpreta o retorno da SEFAZ e persiste o resultado da tentativa. */
    private function processarRetornoEvento($evento, $xmlRetorno)
    {
        $doc = new \DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;

        if (!@$doc->loadXML($xmlRetorno)) {
            $this->atualizarRetornoEvento($evento['T321_Id'], 'ERRO_COMUNICACAO', '', 'A resposta da SEFAZ não contém XML válido.', $xmlRetorno);
            return array('sucesso' => false, 'mensagem' => 'A resposta da SEFAZ não contém XML válido.');
        }

        $retEnvEvento = $doc->getElementsByTagName('retEnvEvento')->item(0);
        if (empty($retEnvEvento)) {
            $this->atualizarRetornoEvento($evento['T321_Id'], 'ERRO_COMUNICACAO', '', 'Resposta da SEFAZ sem retEnvEvento.', $xmlRetorno);
            return array('sucesso' => false, 'mensagem' => 'Resposta da SEFAZ sem retEnvEvento.');
        }

        $xmlRetornoEvento = $retEnvEvento->ownerDocument->saveXML($retEnvEvento);
        $codigoLote = $this->valorNode($retEnvEvento, 'cStat');
        $motivoLote = $this->valorNode($retEnvEvento, 'xMotivo');

        if ($codigoLote != '128') {
            $status = ($codigoLote == '108' || $codigoLote == '109') ? 'ERRO_COMUNICACAO' : 'REJEITADO';
            $this->atualizarRetornoEvento($evento['T321_Id'], $status, $codigoLote, $motivoLote, $xmlRetornoEvento);
            return array('sucesso' => false, 'mensagem' => $codigoLote . ' - ' . $motivoLote);
        }

        $retEvento = $retEnvEvento->getElementsByTagName('retEvento')->item(0);
        if (empty($retEvento)) {
            $this->atualizarRetornoEvento($evento['T321_Id'], 'ERRO_COMUNICACAO', $codigoLote, 'Resposta da SEFAZ sem retEvento.', $xmlRetornoEvento);
            return array('sucesso' => false, 'mensagem' => 'Resposta da SEFAZ sem retorno individual do evento.');
        }

        $codigoRetorno = $this->valorNode($retEvento, 'cStat');
        $motivoRetorno = $this->valorNode($retEvento, 'xMotivo');
        $protocolo = $this->valorNode($retEvento, 'nProt');
        $dataRegistro = $this->dataSefazParaMysql($this->valorNode($retEvento, 'dhRegEvento'));

        if ($codigoRetorno != '135') {
            $this->atualizarRetornoEvento($evento['T321_Id'], 'REJEITADO', $codigoRetorno, $motivoRetorno, $xmlRetornoEvento, $protocolo, $dataRegistro);
            return array('sucesso' => false, 'mensagem' => $codigoRetorno . ' - ' . $motivoRetorno);
        }

        $xmlProcessado = $this->montarXmlProcessadoEvento($evento['T321_XML_Envio'], $retEvento);
        $this->atualizarRetornoEvento($evento['T321_Id'], 'AUTORIZADO', $codigoRetorno, $motivoRetorno, $xmlRetornoEvento, $protocolo, $dataRegistro, $xmlProcessado);

        if ($evento['T321_Tipo_Evento'] == self::TIPO_EVENTO_CANCELAMENTO
            && !empty($evento['T321_T321_Evento_Original_Id'])) {
            mysql_query("UPDATE T321
                         SET T321_Status = 'CANCELADO',
                             T321_Data_Hora_Cancelamento = NOW()
                         WHERE T321_Id = '{$evento['T321_T321_Evento_Original_Id']}'");
        }

        $arquivoAutorizado = $this->salvarArquivoEvento('autorizado', $evento, $xmlProcessado);
        $mensagem = $codigoRetorno . ' - ' . $motivoRetorno;
        if (!$arquivoAutorizado['sucesso']) {
            $mensagem .= '. Aviso: o evento foi autorizado, mas não foi possível gravar o XML em arquivo: ' . $arquivoAutorizado['mensagem'];
        } else {
            $arquivoAssinado = $this->removerArquivoAssinadoEvento($evento);
            if (!$arquivoAssinado['sucesso']) {
                $mensagem .= '. Aviso: o XML autorizado foi gravado, mas não foi possível remover o XML assinado: ' . $arquivoAssinado['mensagem'];
            }
        }

        return array('sucesso' => true, 'mensagem' => $mensagem);
    }

    /** Persiste o resultado da última tentativa na própria T321. */
    private function atualizarRetornoEvento($T321_Id, $status, $codigo, $motivo, $xmlRetorno, $protocolo = '', $dataRegistro = '', $xmlProcessado = '')
    {
        $T321_Id = (int)$T321_Id;
        $codigo = mysql_real_escape_string($codigo);
        $motivo = mysql_real_escape_string($motivo);
        $xmlRetorno = mysql_real_escape_string($xmlRetorno);
        $protocolo = mysql_real_escape_string($protocolo);
        $xmlProcessado = mysql_real_escape_string($xmlProcessado);
        $dataRegistroSql = empty($dataRegistro) ? 'NULL' : "'" . mysql_real_escape_string($dataRegistro) . "'";

        mysql_query("UPDATE T321
                     SET T321_Status = '{$status}',
                         T321_Codigo_Retorno = '{$codigo}',
                         T321_Motivo_Retorno = '{$motivo}',
                         T321_XML_Retorno = '{$xmlRetorno}',
                         T321_Protocolo = " . (empty($protocolo) ? 'NULL' : "'{$protocolo}'") . ",
                         T321_Data_Hora_Registro = {$dataRegistroSql},
                         T321_XML_Processado = " . (empty($xmlProcessado) ? 'NULL' : "'{$xmlProcessado}'") . "
                     WHERE T321_Id = '{$T321_Id}'");
    }

    /** Monta o procEventoNFe persistido após uma autorização. */
    private function montarXmlProcessadoEvento($xmlEvento, $retEvento)
    {
        $eventoDoc = new \DOMDocument();
        $eventoDoc->preserveWhiteSpace = false;
        $eventoDoc->loadXML($xmlEvento);

        $processado = new \DOMDocument('1.0', 'UTF-8');
        $processado->preserveWhiteSpace = false;
        $processado->formatOutput = false;

        $raiz = $processado->createElement('procEventoNFe');
        $raiz->setAttribute('versao', '1.00');
        $raiz->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/nfe');
        $processado->appendChild($raiz);
        $raiz->appendChild($processado->importNode($eventoDoc->documentElement, true));
        $raiz->appendChild($processado->importNode($retEvento, true));

        return $processado->saveXML();
    }

    private function dataSefazParaMysql($dataSefaz)
    {
        if (empty($dataSefaz)) {
            return '';
        }

        try {
            $data = new \DateTime($dataSefaz);
            return $data->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return '';
        }
    }

    /** Salva o XML físico do evento sem substituir a persistência da T321. */
    private function salvarArquivoEvento($tipo, $evento, $conteudo)
    {
        global $g;

        if (!in_array($tipo, array('assinado', 'autorizado'))) {
            return array('sucesso' => false, 'mensagem' => 'Tipo de arquivo de evento inválido.');
        }

        $xml = new \DOMDocument();
        if (!$xml->loadXML($conteudo)) {
            return array('sucesso' => false, 'mensagem' => 'XML inválido para gravação em arquivo.');
        }

        $infEvento = $xml->getElementsByTagName('infEvento')->item(0);
        if (!$infEvento || !$infEvento->hasAttribute('Id')) {
            return array('sucesso' => false, 'mensagem' => 'Id do evento não encontrado no XML.');
        }

        $diretorio = rtrim($g['pathDadosAntigo'], '/') . '/nfe/xml/eventos/' . $tipo;
        if ($tipo == 'autorizado') {
            $dataArquivo = !empty($evento['T321_Data_Hora_Registro'])
                ? $evento['T321_Data_Hora_Registro']
                : $evento['T321_Data_Hora_Inclusao'];
            $anoMes = date('Y-m', strtotime(empty($dataArquivo) ? 'now' : $dataArquivo));
            $diretorio .= '/' . $anoMes;
        }
        if (!is_dir($diretorio) && !mkdir($diretorio, 0777, true)) {
            return array('sucesso' => false, 'mensagem' => 'Não foi possível criar o diretório de eventos.');
        }

        $arquivo = $diretorio . '/' . $infEvento->getAttribute('Id') . '_' . $evento['T321_Ambiente'] . '.xml';
        if (file_put_contents($arquivo, $conteudo) === false) {
            return array('sucesso' => false, 'mensagem' => 'Não foi possível gravar o arquivo do evento.');
        }

        return array('sucesso' => true, 'arquivo' => $arquivo);
    }

    /**
     * Após a autorização, o procEventoNFe é a cópia física definitiva.
     * O XML assinado continua preservado na T321, mas seu arquivo temporário
     * é removido para não manter duas cópias físicas do mesmo evento.
     */
    private function removerArquivoAssinadoEvento($evento)
    {
        global $g;

        $xml = new \DOMDocument();
        if (empty($evento['T321_XML_Envio']) || !$xml->loadXML($evento['T321_XML_Envio'])) {
            return array('sucesso' => false, 'mensagem' => 'XML assinado inválido para localizar o arquivo.');
        }

        $infEvento = $xml->getElementsByTagName('infEvento')->item(0);
        if (!$infEvento || !$infEvento->hasAttribute('Id')) {
            return array('sucesso' => false, 'mensagem' => 'Id do evento não encontrado no XML assinado.');
        }

        $anoMes = date('Y-m', strtotime(empty($evento['T321_Data_Hora_Inclusao']) ? 'now' : $evento['T321_Data_Hora_Inclusao']));
        $nomeArquivo = $infEvento->getAttribute('Id') . '_' . $evento['T321_Ambiente'] . '.xml';
        $pasta = rtrim($g['pathDadosAntigo'], '/') . '/nfe/xml/eventos/assinado';
        $caminhos = array(
            $pasta . '/' . $anoMes . '/' . $nomeArquivo,
            // Compatibilidade com arquivos gravados antes da separação mensal.
            $pasta . '/' . $nomeArquivo,
        );

        foreach ($caminhos as $caminho) {
            if (file_exists($caminho) && !unlink($caminho)) {
                return array('sucesso' => false, 'mensagem' => 'Arquivo não pôde ser excluído.');
            }
        }

        return array('sucesso' => true);
    }

    /** Retorna o XML físico apropriado para download, conforme situação do evento. */
    public function baixarXmlEvento($T321_Id)
    {
        global $g;

        $T321_Id = (int)$T321_Id;
        $sql = "SELECT *
                FROM T321
                WHERE T321_Id = '{$T321_Id}'";
        $evento = mysql_fetch_assoc(mysql_query($sql));
        if (empty($evento)) {
            return array('sucesso' => false, 'mensagem' => 'Evento não encontrado.');
        }

        $autorizado = !empty($evento['T321_Protocolo']);
        $tipo = $autorizado ? 'autorizado' : 'assinado';
        $xmlBanco = $autorizado ? $evento['T321_XML_Processado'] : $evento['T321_XML_Envio'];
        if (empty($xmlBanco)) {
            return array('sucesso' => false, 'mensagem' => 'O XML do evento ainda não foi gerado.');
        }

        $xml = new \DOMDocument();
        if (!$xml->loadXML($xmlBanco)) {
            return array('sucesso' => false, 'mensagem' => 'O XML armazenado para o evento é inválido.');
        }

        $infEvento = $xml->getElementsByTagName('infEvento')->item(0);
        if (!$infEvento || !$infEvento->hasAttribute('Id')) {
            return array('sucesso' => false, 'mensagem' => 'Id do evento não encontrado no XML.');
        }

        $dataArquivo = ($autorizado && !empty($evento['T321_Data_Hora_Registro']))
            ? $evento['T321_Data_Hora_Registro']
            : $evento['T321_Data_Hora_Inclusao'];
        $anoMes = date('Y-m', strtotime(empty($dataArquivo) ? 'now' : $dataArquivo));
        $nomeArquivo = $infEvento->getAttribute('Id') . '_' . $evento['T321_Ambiente'] . '.xml';
        $pasta = rtrim($g['pathDadosAntigo'], '/') . '/nfe/xml/eventos/' . $tipo;
        $caminhos = array(
            $pasta . '/' . $anoMes . '/' . $nomeArquivo,
            // Compatibilidade com XMLs gerados antes da organização por mês.
            $pasta . '/' . $nomeArquivo,
        );

        foreach ($caminhos as $caminho) {
            if (file_exists($caminho)) {
                return array('sucesso' => true, 'nomeArquivo' => $nomeArquivo, 'conteudo' => file_get_contents($caminho));
            }
        }

        // A T321 é a última garantia para download, caso o arquivo tenha sido removido.
        return array('sucesso' => true, 'nomeArquivo' => $nomeArquivo, 'conteudo' => $xmlBanco);
    }

    /** Gera o XML do evento 110001 para cancelamento de evento autorizado. */
    private function gerarXmlCancelamento($evento)
    {
        $T321_IdOriginal = (int)$evento['T321_T321_Evento_Original_Id'];
        $sqlOriginal = "SELECT T321_Tipo_Evento, T321_Sequencial_Evento, T321_Status, T321_Protocolo
                        FROM T321
                        WHERE T321_Id = '{$T321_IdOriginal}'";
        $original = mysql_fetch_assoc(mysql_query($sqlOriginal));

        if (empty($original)
            || $original['T321_Status'] != self::STATUS_AUTORIZADO
            || empty($original['T321_Protocolo'])) {
            return array('sucesso' => false, 'mensagem' => 'O evento original precisa estar autorizado para gerar seu cancelamento.');
        }

        if ($original['T321_Tipo_Evento'] == self::TIPO_EVENTO_CANCELAMENTO) {
            return array('sucesso' => false, 'mensagem' => 'Não é permitido cancelar um evento de cancelamento.');
        }

        $sqlEmpresa = "SELECT C004_Cnpj
                       FROM C004
                       WHERE C004_Id = '{$evento['T007_C004_Id']}'";
        $empresa = mysql_fetch_assoc(mysql_query($sqlEmpresa));
        $cnpj = preg_replace('/\D/', '', $empresa['C004_Cnpj']);
        if (strlen($cnpj) != 14) {
            return array('sucesso' => false, 'mensagem' => 'CNPJ do emitente não localizado.');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = false;

        $eventoXml = $xml->createElement('evento');
        $eventoXml->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/nfe');
        $eventoXml->setAttribute('versao', '1.00');
        $xml->appendChild($eventoXml);

        $inf = $xml->createElement('infEvento');
        $id = 'ID' . self::TIPO_EVENTO_CANCELAMENTO
            . $evento['T321_Chave_NFe']
            . str_pad($evento['T321_Sequencial_Evento'], 2, '0', STR_PAD_LEFT);
        $inf->setAttribute('Id', $id);
        $eventoXml->appendChild($inf);

        $codigoUfAutor = substr($evento['T321_Chave_NFe'], 0, 2);
        $dadosEvento = array(
            'cOrgao' => $codigoUfAutor,
            'tpAmb' => $evento['T321_Ambiente'],
            'CNPJ' => $cnpj,
            'chNFe' => $evento['T321_Chave_NFe'],
            'dhEvento' => date('c'),
            'tpEvento' => self::TIPO_EVENTO_CANCELAMENTO,
            // A NT determina a sequência do evento que está sendo cancelado.
            'nSeqEvento' => $original['T321_Sequencial_Evento'],
            'verEvento' => '1.00',
        );
        foreach ($dadosEvento as $tag => $valor) {
            $inf->appendChild($xml->createElement($tag, $valor));
        }

        $det = $xml->createElement('detEvento');
        $det->setAttribute('versao', '1.00');
        $inf->appendChild($det);
        // A SVRS valida o literal do schema e110001_v1.00.xsd.
        $det->appendChild($xml->createElement('descEvento', 'Cancelamento de Evento'));
        $det->appendChild($xml->createElement('cOrgaoAutor', $codigoUfAutor));
        $det->appendChild($xml->createElement('verAplic', 'Hardness'));
        $det->appendChild($xml->createElement('tpEventoAut', $original['T321_Tipo_Evento']));
        $det->appendChild($xml->createElement('nProtEvento', $original['T321_Protocolo']));

        $assinatura = $this->assinarXmlEvento($xml->saveXML(), $evento['T007_C004_Id']);
        if (!$assinatura['sucesso']) {
            return $assinatura;
        }

        $conteudo = $assinatura['xml'];
        $arquivoAssinado = $this->salvarArquivoEvento('assinado', $evento, $conteudo);
        if (!$arquivoAssinado['sucesso']) {
            return $arquivoAssinado;
        }

        mysql_query("UPDATE T321
                     SET T321_XML_Envio = '" . mysql_real_escape_string($conteudo) . "',
                         T321_Status = 'PRONTO_ENVIO'
                     WHERE T321_Id = '{$evento['T321_Id']}'");

        return array(
            'sucesso' => true,
            'mensagem' => 'XML de cancelamento gerado e assinado. Transmissão pendente.',
            'xml' => $conteudo,
        );
    }

    private function gerarXml112140($evento)
    {
        $sqlItens = "SELECT *
                     FROM T322
                     WHERE T322_T321_Id = '{$evento['T321_Id']}'
                     ORDER BY T322_NItem";
        $itens = mysql_query($sqlItens);

        if (!mysql_num_rows($itens)) {
            return array('sucesso' => false, 'mensagem' => 'Inclua ao menos um item no evento.');
        }

        $sqlEmpresa = "SELECT C004_Cnpj
                       FROM C004
                       WHERE C004_Id = '{$evento['T007_C004_Id']}'";
        $cnpj = mysql_fetch_assoc(mysql_query($sqlEmpresa));
        $cnpj = preg_replace('/\D/', '', $cnpj['C004_Cnpj']);

        if (strlen($cnpj) != 14) {
            return array('sucesso' => false, 'mensagem' => 'CNPJ do emitente não localizado.');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = false;

        $eventoXml = $xml->createElement('evento');
        $eventoXml->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/nfe');
        $eventoXml->setAttribute('versao', '1.00');
        $xml->appendChild($eventoXml);

        $inf = $xml->createElement('infEvento');
        $id = 'ID112140'
            . $evento['T321_Chave_NFe']
            . str_pad($evento['T321_Sequencial_Evento'], 2, '0', STR_PAD_LEFT);
        $inf->setAttribute('Id', $id);
        $eventoXml->appendChild($inf);

        // Nos eventos RTC, cOrgao é o código da UF do autor do evento.
        // A chave da NF-e inicia pelo mesmo código de UF.
        $codigoUfAutor = substr($evento['T321_Chave_NFe'], 0, 2);

        $dadosEvento = array(
            'cOrgao' => $codigoUfAutor,
            'tpAmb' => $evento['T321_Ambiente'],
            'CNPJ' => $cnpj,
            'chNFe' => $evento['T321_Chave_NFe'],
            'dhEvento' => date('c'),
            'tpEvento' => '112140',
            'nSeqEvento' => $evento['T321_Sequencial_Evento'],
            'verEvento' => '1.00',
        );

        foreach ($dadosEvento as $tag => $valor) {
            $inf->appendChild($xml->createElement($tag, $valor));
        }

        $det = $xml->createElement('detEvento');
        $det->setAttribute('versao', '1.00');
        $inf->appendChild($det);
        $det->appendChild($xml->createElement('descEvento', 'Fornecimento não realizado com pagamento antecipado'));
        $det->appendChild($xml->createElement('cOrgaoAutor', $codigoUfAutor));
        $det->appendChild($xml->createElement('tpAutor', '1'));
        $det->appendChild($xml->createElement('verAplic', 'Hardness'));

        while ($item = mysql_fetch_assoc($itens)) {
            $gItem = $xml->createElement('gItemNaoFornecido');
            $gItem->setAttribute('nItem', $item['T322_NItem']);
            $det->appendChild($gItem);

            $gItem->appendChild($xml->createElement('vIBS', number_format($item['T322_Valor_IBS'], 2, '.', '')));
            $gItem->appendChild($xml->createElement('vCBS', number_format($item['T322_Valor_CBS'], 2, '.', '')));

            $controle = $xml->createElement('gControleEstoque');
            $gItem->appendChild($controle);
            $controle->appendChild($xml->createElement('qNaoFornecida', number_format($item['T322_Quantidade_Nao_Fornecida'], 4, '.', '')));
            $controle->appendChild($xml->createElement('uNaoFornecida', $item['T322_Unidade_Nao_Fornecida']));
        }

        $conteudo = $xml->saveXML();
        $assinatura = $this->assinarXmlEvento($conteudo, $evento['T007_C004_Id']);
        if (!$assinatura['sucesso']) {
            return $assinatura;
        }

        $conteudo = $assinatura['xml'];
        $arquivoAssinado = $this->salvarArquivoEvento('assinado', $evento, $conteudo);
        if (!$arquivoAssinado['sucesso']) {
            return $arquivoAssinado;
        }

        $conteudoSql = mysql_real_escape_string($conteudo);
        $sqlAtualizar = "UPDATE T321
                         SET T321_XML_Envio = '{$conteudoSql}',
                             T321_Status = 'PRONTO_ENVIO'
                         WHERE T321_Id = '{$evento['T321_Id']}'";
        mysql_query($sqlAtualizar);

        return array(
            'sucesso' => true,
            'mensagem' => 'XML gerado e assinado. Transmissão pendente.',
            'xml' => $conteudo,
        );
    }

    /** Assina o infEvento. É comum a todos os eventos NF-e. */
    private function assinarXmlEvento($xml, $C004_Id)
    {
        $C004_Id = (int)$C004_Id;
        $sqlEmpresa = "SELECT
                           C004_Localizacao_Certificados,
                           C004_Senha_Certificados
                       FROM C004
                       WHERE C004_Id = '{$C004_Id}'";
        $empresa = mysql_fetch_assoc(mysql_query($sqlEmpresa));

        if (empty($empresa)) {
            return array('sucesso' => false, 'mensagem' => 'Configuração de certificado não encontrada.');
        }

        $certificado = rtrim($empresa['C004_Localizacao_Certificados'], '/') . '/' . $C004_Id . '.pfx';
        if (!file_exists($certificado)) {
            return array('sucesso' => false, 'mensagem' => 'Certificado PFX não encontrado para a empresa.');
        }

        $pfx = file_get_contents($certificado);
        if (!openssl_pkcs12_read($pfx, $certDados, $empresa['C004_Senha_Certificados'])) {
            return array('sucesso' => false, 'mensagem' => 'Não foi possível abrir o certificado da empresa.');
        }

        $chavePrivada = openssl_pkey_get_private($certDados['pkey']);
        if (!$chavePrivada) {
            return array('sucesso' => false, 'mensagem' => 'Chave privada do certificado inválida.');
        }

        // A SEFAZ rejeita qualquer caractere de edição dentro de
        // X509Certificate (rejeição 588). Mantém somente o alfabeto Base64,
        // removendo cabeçalho PEM, quebras de linha e eventuais caracteres
        // invisíveis retornados na extração do PFX.
        $certX509 = preg_replace(
            '/-----BEGIN CERTIFICATE-----|-----END CERTIFICATE-----/',
            '',
            $certDados['cert']
        );
        $certX509 = preg_replace('/[^A-Za-z0-9+\/=]/', '', $certX509);
        if (empty($certX509) || base64_decode($certX509, true) === false) {
            return array('sucesso' => false, 'mensagem' => 'Certificado inválido para assinatura do evento.');
        }

        $doc = new \DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;
        if (!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)) {
            return array('sucesso' => false, 'mensagem' => 'XML inválido para assinatura.');
        }

        $infEvento = $doc->getElementsByTagName('infEvento')->item(0);
        if (!$infEvento || !$infEvento->hasAttribute('Id')) {
            return array('sucesso' => false, 'mensagem' => 'infEvento não encontrado para assinatura.');
        }

        $id = $infEvento->getAttribute('Id');
        $digest = base64_encode(hash('sha1', $infEvento->C14N(false, false), true));
        $ds = 'http://www.w3.org/2000/09/xmldsig#';

        $signature = $doc->createElementNS($ds, 'Signature');
        // O SignedInfo precisa estar vinculado ao documento antes de sua
        // canonicalização; caso contrário, a assinatura pode ser calculada
        // sobre conteúdo vazio.
        $doc->documentElement->appendChild($signature);

        $signedInfo = $doc->createElementNS($ds, 'SignedInfo');
        $signature->appendChild($signedInfo);

        $canonicalization = $doc->createElementNS($ds, 'CanonicalizationMethod');
        $canonicalization->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $signedInfo->appendChild($canonicalization);

        $metodoAssinatura = $doc->createElementNS($ds, 'SignatureMethod');
        $metodoAssinatura->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');
        $signedInfo->appendChild($metodoAssinatura);

        $referencia = $doc->createElementNS($ds, 'Reference');
        $referencia->setAttribute('URI', '#' . $id);
        $signedInfo->appendChild($referencia);

        $transforms = $doc->createElementNS($ds, 'Transforms');
        $referencia->appendChild($transforms);

        $algoritmosTransformacao = array(
            'http://www.w3.org/2000/09/xmldsig#enveloped-signature',
            'http://www.w3.org/TR/2001/REC-xml-c14n-20010315',
        );
        foreach ($algoritmosTransformacao as $algoritmo) {
            $transform = $doc->createElementNS($ds, 'Transform');
            $transform->setAttribute('Algorithm', $algoritmo);
            $transforms->appendChild($transform);
        }

        $digestMethod = $doc->createElementNS($ds, 'DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $referencia->appendChild($digestMethod);
        $referencia->appendChild($doc->createElementNS($ds, 'DigestValue', $digest));

        $assinatura = '';
        $assinaturaGerada = openssl_sign($signedInfo->C14N(false, false), $assinatura, $chavePrivada, OPENSSL_ALGO_SHA1);
        if (!$assinaturaGerada) {
            return array('sucesso' => false, 'mensagem' => 'Não foi possível assinar o XML do evento.');
        }

        if (openssl_verify($signedInfo->C14N(false, false), $assinatura, $certDados['cert'], OPENSSL_ALGO_SHA1) !== 1) {
            return array('sucesso' => false, 'mensagem' => 'A assinatura gerada para o XML do evento não pôde ser validada.');
        }

        $signature->appendChild($doc->createElementNS($ds, 'SignatureValue', base64_encode($assinatura)));

        $keyInfo = $doc->createElementNS($ds, 'KeyInfo');
        $x509 = $doc->createElementNS($ds, 'X509Data');
        $x509->appendChild($doc->createElementNS($ds, 'X509Certificate', $certX509));
        $keyInfo->appendChild($x509);
        $signature->appendChild($keyInfo);

        return array('sucesso' => true, 'xml' => $doc->saveXML());
    }

    /**
     * Lista os eventos registrados para uma NF-e.
     *
     * @param int $T007_Id
     * @return array
     */
    public function listarEventos($T007_Id)
    {
        $T007_Id = (int)$T007_Id;

        $sql = "SELECT
                    T321_Id,
                    T321_Tipo_Evento,
                    T321_Sequencial_Evento,
                    T321_Status,
                    T321_Data_Hora_Inclusao,
                    T321_Data_Hora_Ultima_Tentativa,
                    T321_Quantidade_Tentativas,
                    T321_Protocolo,
                    T321_Data_Hora_Registro,
                    T321_Codigo_Retorno,
                    T321_Motivo_Retorno,
                    T321_T321_Evento_Original_Id,
                    T321_Data_Hora_Cancelamento
                FROM T321
                WHERE T321_T007_Id = '{$T007_Id}'
                ORDER BY T321_Tipo_Evento, T321_Sequencial_Evento, T321_Id";

        $resultado = mysql_query($sql);
        $eventos = array();

        while ($linha = mysql_fetch_assoc($resultado)) {
            $eventos[] = $linha;
        }

        return $eventos;
    }

    /**
     * Retorna os dados necessários para preencher a grid do evento 112140.
     *
     * O nItem, quantidade, unidade e tributos são extraídos do XML autorizado,
     * pois o nItem é a numeração do documento fiscal autorizado, não do T008.
     * Os totais já informados consideram somente eventos 112140 autorizados.
     *
     * @param int $T007_Id
     * @return array Estrutura com sucesso, mensagem e itens
     */
    public function buscarItensParaGrid112140($T007_Id)
    {
        $T007_Id = (int)$T007_Id;
        $nfe = $this->buscarNFe($T007_Id);

        if (empty($nfe)) {
            return array('sucesso' => false, 'mensagem' => 'NF-e não encontrada.', 'itens' => array());
        }

        if (empty($nfe['T007_Numero_Protocolo_Nfe'])) {
            return array('sucesso' => false, 'mensagem' => 'A NF-e ainda não possui protocolo de autorização.', 'itens' => array());
        }

        $caminhoXml = $this->localizarXmlAutorizado($nfe);
        if (empty($caminhoXml)) {
            return array('sucesso' => false, 'mensagem' => 'XML autorizado da NF-e não foi encontrado.', 'itens' => array());
        }

        $xml = new \DOMDocument();
        if (!@$xml->load($caminhoXml)) {
            return array('sucesso' => false, 'mensagem' => 'Não foi possível ler o XML autorizado da NF-e.', 'itens' => array());
        }

        $saldos = $this->buscarSaldos112140($T007_Id);
        $itens = array();

        foreach ($xml->getElementsByTagName('det') as $det) {
            $nItem = (int)$det->getAttribute('nItem');
            $produto = $det->getElementsByTagName('prod')->item(0);
            if (empty($produto)) {
                continue;
            }

            $quantidadeOriginal = $this->valorNode($produto, 'qCom');
            $valorIbsOriginal = $this->valorNode($det, 'vIBS');
            $valorCbsOriginal = $this->valorNode($det, 'vCBS');
            $saldo = isset($saldos[$nItem]) ? $saldos[$nItem] : array();

            $quantidadeJaInformada = (float)($saldo['quantidade'] ?? 0);
            $vIbsJaInformado = (float)($saldo['vIBS'] ?? 0);
            $vCbsJaInformado = (float)($saldo['vCBS'] ?? 0);

            $itens[] = array(
                'nItem' => $nItem,
                'codigoProduto' => $this->valorNode($produto, 'cProd'),
                'descricaoProduto' => $this->valorNode($produto, 'xProd'),
                'quantidadeOriginal' => (float)$quantidadeOriginal,
                'unidade' => $this->valorNode($produto, 'uCom'),
                'vIBSOriginal' => (float)$valorIbsOriginal,
                'vCBSOriginal' => (float)$valorCbsOriginal,
                'quantidadeJaInformada' => $quantidadeJaInformada,
                'quantidadeSaldo' => (float)$quantidadeOriginal - $quantidadeJaInformada,
                'vIBSJaInformado' => $vIbsJaInformado,
                'vCBSJaInformado' => $vCbsJaInformado,
                'vIBSSaldo' => (float)$valorIbsOriginal - $vIbsJaInformado,
                'vCBSSaldo' => (float)$valorCbsOriginal - $vCbsJaInformado,
            );
        }

        return array('sucesso' => true, 'mensagem' => '', 'itens' => $itens);
    }

    /** Retorna o nItem do XML autorizado correspondente ao item interno da NF-e. */
    public function buscarNItemPorItemOrigem112140($T007_Id, $T008_Id)
    {
        $T007_Id = (int)$T007_Id;
        $T008_Id = (int)$T008_Id;
        $sql = "SELECT T008_Id
                FROM T008
                WHERE T008_T007_Id = '{$T007_Id}'
                ORDER BY T008_Id";
        $resultado = mysql_query($sql);
        $nItem = 0;
        $nItemEncontrado = false;

        while ($linha = mysql_fetch_assoc($resultado)) {
            $nItem++;

            if ((int)$linha['T008_Id'] === $T008_Id) {
                $nItemEncontrado = $nItem;
                break;
            }
        }

        if (!$nItemEncontrado) {
            return false;
        }

        $retorno = $this->buscarItensParaGrid112140($T007_Id);
        if (!$retorno['sucesso']) {
            return false;
        }

        foreach ($retorno['itens'] as $item) {
            if ((int)$item['nItem'] === $nItemEncontrado) {
                return $nItemEncontrado;
            }
        }

        return false;
    }

    /**
     * Inclui, de uma vez, os itens ainda não vinculados ao rascunho do 112140.
     *
     * O percentual é aplicado sobre o saldo disponível de cada item: o saldo
     * considera somente ocorrências 112140 autorizadas e não canceladas.
     */
    public function incluirItens112140PorPercentual($T321_Id, $percentual)
    {
        $T321_Id = (int)$T321_Id;
        $percentual = (float)str_replace(',', '.', $percentual);

        if ($percentual <= 0 || $percentual > 100) {
            return array('sucesso' => false, 'mensagem' => 'Informe um percentual maior que zero e menor ou igual a 100.');
        }

        $evento = mysql_fetch_assoc(mysql_query("SELECT T321_Id, T321_T007_Id, T321_Tipo_Evento, T321_Status
                                                  FROM T321
                                                  WHERE T321_Id = '{$T321_Id}'"));
        if (empty($evento)) {
            return array('sucesso' => false, 'mensagem' => 'Evento não encontrado.');
        }

        if ($evento['T321_Tipo_Evento'] != self::TIPO_EVENTO_FORNECIMENTO_NAO_REALIZADO
            || $evento['T321_Status'] != 'RASCUNHO') {
            return array('sucesso' => false, 'mensagem' => 'Os itens só podem ser incluídos em um rascunho do evento 112140.');
        }

        $itensXml = $this->buscarItensParaGrid112140($evento['T321_T007_Id']);
        if (empty($itensXml['sucesso'])) {
            return $itensXml;
        }

        $resultadoItensOrigem = mysql_query("SELECT T008_Id, T008_Unidade
                                              FROM T008
                                              WHERE T008_T007_Id = '{$evento['T321_T007_Id']}'
                                              ORDER BY T008_Id");
        $itensOrigem = array();
        while ($itemOrigem = mysql_fetch_assoc($resultadoItensOrigem)) {
            $itensOrigem[] = $itemOrigem;
        }

        if (count($itensOrigem) != count($itensXml['itens'])) {
            return array('sucesso' => false, 'mensagem' => 'A quantidade de itens da NF-e não corresponde ao XML autorizado.');
        }

        $existentes = array();
        $resultadoExistentes = mysql_query("SELECT T322_T008_Id_Origem
                                             FROM T322
                                             WHERE T322_T321_Id = '{$T321_Id}'");
        while ($existente = mysql_fetch_assoc($resultadoExistentes)) {
            $existentes[(int)$existente['T322_T008_Id_Origem']] = true;
        }

        mysql_query('START TRANSACTION');
        $incluidos = 0;
        $ignorados = 0;

        foreach ($itensXml['itens'] as $posicao => $itemXml) {
            $itemOrigem = $itensOrigem[$posicao];
            $T008_Id = (int)$itemOrigem['T008_Id'];
            $quantidadeSaldo = max(0, (float)$itemXml['quantidadeSaldo']);

            if (isset($existentes[$T008_Id]) || $quantidadeSaldo <= 0) {
                $ignorados++;
                continue;
            }

            $quantidade = round($quantidadeSaldo * $percentual / 100, 4);
            if ($quantidade <= 0) {
                $ignorados++;
                continue;
            }

            $valorIbs = round(max(0, (float)$itemXml['vIBSSaldo']) * $percentual / 100, 2);
            $valorCbs = round(max(0, (float)$itemXml['vCBSSaldo']) * $percentual / 100, 2);
            $unidade = mysql_real_escape_string($itemOrigem['T008_Unidade']);

            $sqlInserir = "INSERT INTO T322 (
                                T322_T321_Id,
                                T322_T008_Id_Origem,
                                T322_NItem,
                                T322_Quantidade_Nao_Fornecida,
                                T322_Unidade_Nao_Fornecida,
                                T322_Valor_IBS,
                                T322_Valor_CBS
                            ) VALUES (
                                '{$T321_Id}',
                                '{$T008_Id}',
                                '" . (int)$itemXml['nItem'] . "',
                                '" . number_format($quantidade, 4, '.', '') . "',
                                '{$unidade}',
                                '" . number_format($valorIbs, 2, '.', '') . "',
                                '" . number_format($valorCbs, 2, '.', '') . "'
                            )";
            if (!mysql_query($sqlInserir)) {
                mysql_query('ROLLBACK');
                return array('sucesso' => false, 'mensagem' => 'Não foi possível incluir os itens: ' . mysql_error());
            }

            $incluidos++;
        }

        mysql_query('COMMIT');

        if (!$incluidos) {
            return array('sucesso' => false, 'mensagem' => 'Não há itens disponíveis para incluir neste evento.');
        }

        return array(
            'sucesso' => true,
            'mensagem' => $incluidos . ' item(ns) incluído(s) com ' . rtrim(rtrim(number_format($percentual, 4, ',', '.'), '0'), ',') . '%.'
                . ($ignorados ? ' ' . $ignorados . ' item(ns) já estavam vinculados ou sem saldo.' : ''),
            'incluidos' => $incluidos,
        );
    }

    private function buscarNFe($T007_Id)
    {
        $sql = "SELECT
                    T007_Id,
                    T007_Data_Emissao,
                    T007_Numero_Protocolo_Nfe,
                    T007_Chave_Acesso_Nfe
                FROM T007
                WHERE T007_Id = '{$T007_Id}'";

        $resultado = mysql_query($sql);
        return mysql_fetch_assoc($resultado);
    }

    private function buscarSaldos112140($T007_Id)
    {
        $tipoEvento = self::TIPO_EVENTO_FORNECIMENTO_NAO_REALIZADO;
        $statusAutorizado = self::STATUS_AUTORIZADO;

        $sql = "SELECT
                    T322_NItem,
                    SUM(T322_Quantidade_Nao_Fornecida) AS quantidade,
                    SUM(T322_Valor_IBS) AS vIBS,
                    SUM(T322_Valor_CBS) AS vCBS
                FROM T322
                INNER JOIN T321 ON T321_Id = T322_T321_Id
                WHERE T321_T007_Id = '{$T007_Id}'
                  AND T321_Tipo_Evento = '{$tipoEvento}'
                  AND T321_Status = '{$statusAutorizado}'
                  AND NOT EXISTS (
                      SELECT 1
                      FROM T321 AS Cancelamento
                      WHERE Cancelamento.T321_Tipo_Evento = '" . self::TIPO_EVENTO_CANCELAMENTO . "'
                        AND Cancelamento.T321_T321_Evento_Original_Id = T321.T321_Id
                        AND Cancelamento.T321_Status = '{$statusAutorizado}'
                  )
                GROUP BY T322_NItem";

        $resultado = mysql_query($sql);
        $saldos = array();

        while ($linha = mysql_fetch_assoc($resultado)) {
            $saldos[(int)$linha['T322_NItem']] = $linha;
        }

        return $saldos;
    }

    private function localizarXmlAutorizado($nfe)
    {
        global $g;

        $protocolo = $nfe['T007_Numero_Protocolo_Nfe'];
        $anoMes = substr($nfe['T007_Data_Emissao'], 0, 7);
        $pastaNfe = rtrim($g['pathDadosAntigo'], '/') . '/nfe/xml/autorizado';
        $caminhos = array(
            "{$pastaNfe}/{$anoMes}/{$protocolo}.xml",
            "{$pastaNfe}/{$protocolo}.xml",
        );

        // A data do T007 pode ter sido gravada em formato diferente do
        // utilizado na pasta mensal. O protocolo é único para esta busca.
        foreach (glob("{$pastaNfe}/*/{$protocolo}.xml") as $caminho) {
            $caminhos[] = $caminho;
        }

        foreach ($caminhos as $caminho) {
            if (file_exists($caminho)) {
                return $caminho;
            }
        }

        return '';
    }

    private function valorNode($node, $nome)
    {
        $elemento = $node->getElementsByTagName($nome)->item(0);
        return empty($elemento) ? '' : trim($elemento->nodeValue);
    }
}
