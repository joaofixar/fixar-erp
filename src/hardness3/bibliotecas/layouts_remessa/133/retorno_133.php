<?php
namespace hardness;

class RETORNO_REMESSA
{
    public function importarRetorno($arquivo)
    {
        global $g;
        $fileName = $arquivo;
        $mArquivo = $arquivo;
        $cArquivo = $mArquivo["name"];

        if (empty($arquivo['name'])) {
            return 'Selecione o arquivo';
        }

        $arquivo = file($mArquivo['tmp_name']);
        $linhas = array();

        foreach ($arquivo as $nr => $linha) {
            $linha = rtrim($linha, "\r\n");

            if ($nr == 0) { // Header
                // Header CNAB400
                // [1] 001-001 Identificação do Registro
                // [2] 002-002 Identificação do Arquivo Retorno
                // [3] 003-009 Literal Retorno
                // [4] 010-011 Código de Serviço
                // [5] 012-026 Literal Serviço
                // [6] 027-046 Código da Empresa
                // [7] 047-076 Nome da Empresa
                // [8] 077-079 Código do Banco
                // [9] 080-094 Nome do Banco
                // [10] 095-100 Data de Geração do Arquivo
                // [11] 101-108 Complemento
                // [12] 109-113 Prefixo da Agência
                // [13] 114-379 Uso Exclusivo
                // [14] 380-385 Data do Crédito
                // [15] 386-394 Complemento
                // [16] 395-400 Número Sequencial do Registro
                preg_match("/(.{1})(.{1})(.{7})(.{2})(.{15})(.{20})(.{30})(.{3})(.{15})(.{6})(.{8})(.{5})(.{266})(.{6})(.{9})(.{6})/", $linha, $posicoes);
            } elseif ($nr == (count($arquivo) - 1)) { // Trailer
                // Trailer CNAB400
                // [1] 001-001 Identificação do Registro
                // [2] 002-002 Identificação do Arquivo
                // [3] 003-004 Código de Serviço
                // [4] 005-007 Código do Banco
                // [5] 008-394 Uso Exclusivo
                // [6] 395-400 Número Sequencial do Registro
                preg_match("/(.{1})(.{1})(.{2})(.{3})(.{387})(.{6})/", $linha, $posicoes);
            } else { // Detalhe
                // Detalhe CNAB400
                // [1] 001-001 Identificação do Registro
                // [2] 002-003 Tipo de Inscrição da Empresa
                // [3] 004-017 Número de Inscrição da Empresa
                // [4] 018-020 Código do Banco
                // [5] 021-037 Identificação da Empresa Beneficiário no Banco
                // [6] 038-062 Uso da Empresa
                // [7] 063-070 Nosso Número Complementar
                // [8] 071-081 Nosso Número
                // [9] 082-082 DV do Nosso Número
                // [10] 083-092 Desconto Bonificação por Dia
                // [11] 093-104 Reserva
                // [12] 105-105 Rateio de Crédito
                // [13] 106-107 Condição para Emissão da Papeleta
                // [14] 108-108 Carteira
                // [15] 109-110 Código de Ocorrência / Natureza de Recebimento
                // [16] 111-116 Data da Ocorrência / Recebimento
                // [17] 117-126 Número do Documento
                // [18] 127-146 Nosso Número Completo
                // [19] 147-152 Data de Vencimento
                // [20] 153-165 Valor do Título
                // [21] 166-168 Banco Recebedor
                // [22] 169-173 Agência Recebedora
                // [23] 174-175 Espécie do Título
                // [24] 176-188 Tarifa / Outras Despesas
                // [25] 189-201 IOF / Outras Despesas Complementares
                // [26] 202-214 Juros Operação / Outras Despesas
                // [27] 215-227 Valor do IOF
                // [28] 228-240 Valor do Abatimento
                // [29] 241-253 Valor do Desconto
                // [30] 254-266 Valor Recebido
                // [31] 267-279 Juros de Mora
                // [32] 280-292 Outros Créditos Recebidos
                // [33] 293-294 Código da Origem do Pagamento
                // [34] 295-295 Origem do Pagamento
                // [35] 296-301 Data do Crédito
                // [36] 302-304 Código do Cheque
                // [37] 305-314 Número do Cheque
                // [38] 315-318 Motivo / Rejeição
                // [39] 319-328 Código do Motivo / Número do Cartório
                // [40] 329-342 Número do Protocolo
                // [41] 343-348 Complemento
                // [42] 349-400 Número Sequencial do Registro
                preg_match("/(.{1})(.{2})(.{14})(.{3})(.{17})(.{25})(.{8})(.{11})(.{1})(.{10})(.{12})(.{1})(.{2})(.{1})(.{2})(.{6})(.{10})(.{20})(.{6})(.{13})(.{3})(.{5})(.{2})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{13})(.{2})(.{1})(.{6})(.{3})(.{10})(.{4})(.{10})(.{40})(.{2})(.{10})(.{14})(.{6})/", $linha, $posicoes);
            }

            $linhas[] = $posicoes;
        }

        $conteudo = '';
        $titulos = 0;
        $linhaHeader = '';
        $linhaTrailler = '';
        $dataRetorno = '0000-00-00';
        $codigoBanco = '133';
        $codigoEmpresaHeader = '';
        $cooperativa = '';
        $conta = '';
        $dvConta = '';

        foreach ($linhas as $key => $linha) {
            if ($key == 0) {
                // Header
                // [10] 095-100 Data de Geração do Arquivo
                $dataRetorno = $this->converteData($linha[10]);
                // [8] 077-079 Código do Banco
                $codigoBanco = trim($linha[8]);
                // [6] 027-046 Código da Empresa
                $codigoEmpresaHeader = trim($linha[6]);
                $linhaHeader = $linha[0];
            }

            if ($key != 0 && $key != (count($linhas) - 1)) {
                $conteudo .= $linha[0] . PHP_EOL;
                $titulos = ($linha[42] - 1);

                if (empty($cooperativa)) {
                    $dadosConta = $this->parseIdentificacaoEmpresaBeneficiario($linha[5]);
                    $cooperativa = $dadosConta['agencia'];
                    $conta = $dadosConta['conta'];
                    $dvConta = $dadosConta['dv_conta'];
                }
            }

            if ($key == (count($linhas) - 1)) {
                $linhaTrailler = $linha[0];
            }
        }

        $sql = mysql_query("SELECT * FROM T037 WHERE T037_Linha_Header = '" . mysql_real_escape_string($linhaHeader) . "'");
        if (mysql_num_rows($sql) > 0) {
            // Mantido comportamento dos outros bancos: não bloqueia por padrão.
        }

        $conteudoArquivo = $linhaHeader . PHP_EOL . $conteudo . $linhaTrailler;

        $codigoEmpresaHeaderNumero = preg_replace('/[^0-9]/', '', $codigoEmpresaHeader);
        $sqlD007 = false;

        if (!empty($cooperativa) && !empty($conta)) {
            $sqlD007 = mysql_query("
                SELECT D007_Id
                  FROM D007
             LEFT JOIN D062 ON D062_Id = D007_D062_Id
                 WHERE D062_Numero_Banco = '133'
                   AND D007_Numero_Agencia = '{$cooperativa}'
                   AND D007_Numero_Conta = '{$conta}'
                   AND D007_Digito_Verificador_Conta = '{$dvConta}'
            ");
        }

        $rows = $sqlD007 ? mysql_num_rows($sqlD007) : 0;
        if ($rows == 0 && !empty($codigoEmpresaHeaderNumero)) {
            $sqlD007 = mysql_query("
                SELECT D007_Id
                  FROM D007
             LEFT JOIN D062 ON D062_Id = D007_D062_Id
                 WHERE D062_Numero_Banco = '133'
                   AND REPLACE(REPLACE(REPLACE(IFNULL(D007_Codigo_Cedente,''), '/', ''), '-', ''), ' ', '') = '{$codigoEmpresaHeaderNumero}'
            ");
            $rows = mysql_num_rows($sqlD007);
        }

        if ($rows == 0) {
            return "Não foi possível localizar a conta bancária Cresol do retorno, por favor verifique o cadastro.";
        } elseif ($rows > 1) {
            return "Foi identificada mais de uma conta bancária Cresol para esse retorno, por favor verifique.";
        }

        $rowD007 = mysql_fetch_array($sqlD007);

        $conteudoArquivo = mysql_real_escape_string($conteudoArquivo);
        $T037 = "INSERT INTO T037 (
                        T037_C004_Id, T037_Nome_Arquivo, T037_Data_Processamento, T037_Numero_Titulos,
                        T037_Linha_Header, T037_Linha_Trailler, T037_Codigo_Banco, T037_Conteudo_Arquivo, T037_D007_Id
                    ) VALUES (
                        '{$g['empresaAtual']}', '{$fileName['name']}', '{$dataRetorno}', '{$titulos}',
                        '{$linhaHeader}', '{$linhaTrailler}', '{$codigoBanco}', '{$conteudoArquivo}', '{$rowD007['D007_Id']}'
                    )";
        mysql_query($T037);
        $T037_Id = $g['mysqlLastId'];

        foreach ($linhas as $key => $linha) {
            if ($key == 0 || $key == (count($linhas) - 1)) {
                continue;
            }

            $identificacaoEmpresaBanco = $this->parseIdentificacaoEmpresaBeneficiario($linha[5]);
            // [6] 038-062 Uso da Empresa
            $usoEmpresa = trim($linha[6]);

            // [8] 071-081 Nosso Número
            $nossoNumero = trim($linha[8]);
            // [9] 082-082 DV do Nosso Número
            $digitoNossoNumero = trim($linha[9]);
            // [14] 108-108 Carteira
            $carteira = trim($linha[14]);

            // [15] 109-110 Código de Ocorrência / Natureza de Recebimento
            $codigoNaturezaRec = trim($linha[15]);
            // [16] 111-116 Data da Ocorrência / Recebimento
            $dataRecebimento = $this->converteData($linha[16]);
            // [17] 117-126 Número do Documento
            $numeroDocumento = trim($linha[17]);
            // [19] 147-152 Data de Vencimento
            $dataVencimento = $this->converteData($linha[19]);
            // [20] 153-165 Valor do Título
            $valorTitulo = $this->converteNumero($linha[20]);
            // [21] 166-168 Banco Recebedor
            $codigoBancoRecebedor = trim($linha[21]);
            // [22] 169-173 Agência Recebedora
            $agenciaRecebedora = trim($linha[22]);
            $dvAgenciaRecebedora = '';
            // [23] 174-175 Espécie do Título
            $especieTitulo = trim($linha[23]);
            // [24] 176-188 Tarifa / Outras Despesas
            $valorOutrasDespesas = $this->converteNumero($linha[24]);
            // [26] 202-214 Juros Operação / Outras Despesas
            $valorJurosOperacao = $this->converteNumero($linha[26]);
            // [27] 215-227 Valor do IOF
            $valorIOF = $this->converteNumero($linha[27]);
            // [28] 228-240 Valor do Abatimento
            $valorAbatimento = $this->converteNumero($linha[28]);
            // [29] 241-253 Valor do Desconto
            $valorDesconto = $this->converteNumero($linha[29]);
            // [30] 254-266 Valor Recebido
            $valorRecebido = $this->converteNumero($linha[30]);
            // [31] 267-279 Juros de Mora
            $valorJurosMora = $this->converteNumero($linha[31]);
            // [32] 280-292 Outros Créditos Recebidos
            $valorOutrosCreditos = $this->converteNumero($linha[32]);
            // [39] 319-328 Código do Motivo / Número do Cartório
            $codigoMotivo = trim($linha[39]);
            // [35] 296-301 Data do Crédito
            $dataCredito = $this->converteData($linha[33]);

            // [18] 127-146 Nosso Número Completo
            $nossoNumeroCompleto20 = trim($linha[18]);
            // [34] 295-295 Origem do Pagamento
            $origemPagamento = trim($linha[34]);
            // [36] 302-304 Código do Cheque
            $codigoChequeCresol = trim($linha[36]);
            // [39] 319-328 Código do Motivo / Número do Cartório
            $numeroCartorio = trim($linha[39]);
            // [40] 329-342 Número do Protocolo
            $numeroProtocolo = trim($linha[40]);

            // Mantém o mesmo conceito dos outros layouts: despesas podem compor o recebido.
            $valorRecebido = $this->converteNumero($linha[30] + $linha[24]);

            $aliasesNossoNumero = $this->montaAliasesNossoNumero($nossoNumero, $digitoNossoNumero, $carteira, $nossoNumeroCompleto20);
            $whereNossoNumero = $this->montaWhereNossoNumero($aliasesNossoNumero);

            $sql = mysql_query("SELECT * FROM T117
                                  LEFT JOIN T002 ON T002_T117_Id = T117_Id
                                 WHERE T117_C004_Id = '{$g['empresaAtual']}'
                                   AND T117_D007_Id = '{$rowD007['D007_Id']}'
                                   AND (
                                        {$whereNossoNumero}
                                     OR T002_Numero_Duplicata = '{$numeroDocumento}'
                                     OR T117_Id = '{$usoEmpresa}'
                                   )");
            if (mysql_num_rows($sql) > 0) {
                $res = mysql_fetch_array($sql);
                $T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
            } else {
                $sql = mysql_query("SELECT * FROM T117
                                      LEFT JOIN T002 ON T002_T117_Id = T117_Id
                                     WHERE T117_D007_Id = '{$rowD007['D007_Id']}'
                                       AND (
                                            {$whereNossoNumero}
                                         OR T002_Numero_Duplicata = '{$numeroDocumento}'
                                         OR T117_Id = '{$usoEmpresa}'
                                       )");
                if (mysql_num_rows($sql) > 0) {
                    $res = mysql_fetch_array($sql);
                    $T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
                } else {
                    $T002_Id = null;
                    $res = array();
                }
            }

            if ($T002_Id <= 0) {
                $sql = mysql_query("SELECT * FROM T117
                                      LEFT JOIN T002 ON T002_T117_Id = T117_Id
                                     WHERE (
                                            {$whereNossoNumero}
                                         OR T002_Numero_Duplicata = '{$numeroDocumento}'
                                         OR T117_Id = '{$usoEmpresa}'
                                       )");
                if (mysql_num_rows($sql) > 0) {
                    $res = mysql_fetch_array($sql);
                    $T002_Id = isset($res['T002_Id']) ? $res['T002_Id'] : null;
                }
            }

            $T038_Flag_Autorizado = '';
            if (!empty($T002_Id) && isset($res['T002_Valor_Saldo']) && $res['T002_Valor_Saldo'] != $valorRecebido && $valorRecebido > 0) {
                $T038_Flag_Autorizado = 'N';
            }

            $sql = "INSERT INTO T038 (
                            T038_T037_Id, T038_T002_Id, T038_C004_Id, T038_Numero_Documento, T038_Nosso_Numero,
                            T038_Digito_Verificador_Nosso_Numero, T038_Codigo_Natureza_Recebimento, T038_Carteira, T038_Data_Recebimento, T038_Codigo_Motivo,
                            T038_Data_Vencimento, T038_Valor_Titulo, T038_Codigo_Banco_Recebedor, T038_Prefixo_Agencia_Recebedora, T038_Digito_Verificador_Agencia_Recebedora,
                            T038_Especie_Titulo, T038_Data_Credito, T038_Valor_Outros_Recebimentos, T038_Valor_Taxa_IOF, T038_Valor_Abatimento, T038_Valor_Outras_Despesas,
                            T038_Valor_Desconto, T038_Valor_Recebido, T038_Valor_Juros_Mora, T038_Numero_Linha, T038_String,
                            T038_Info_Complementar_1, T038_Info_Complementar_2, T038_Info_Complementar_3, T038_Flag_Autorizado
                        ) VALUES (
                            '{$T037_Id}', '{$T002_Id}', '{$g['empresaAtual']}', '{$numeroDocumento}', '{$nossoNumero}',
                            '{$digitoNossoNumero}', '{$codigoNaturezaRec}', '{$carteira}', '{$dataRecebimento}', '{$codigoMotivo}',
                            '{$dataVencimento}', '{$valorTitulo}', '{$codigoBancoRecebedor}', '{$agenciaRecebedora}', '{$dvAgenciaRecebedora}',
                            '{$especieTitulo}', '{$dataCredito}', '{$valorOutrosCreditos}', '{$valorIOF}', '{$valorAbatimento}', '{$valorOutrasDespesas}',
                            '{$valorDesconto}', '{$valorRecebido}', '{$valorJurosMora}', '{$linha[42]}', '{$linha[0]}',
                            '{$origemPagamento}', '{$codigoChequeCresol}', '{$numeroProtocolo}', '{$T038_Flag_Autorizado}'
                        )";
            mysql_query($sql);
        }

        $dir = $g['pathDadosUsuario'] . "arq_temp/";
        is_dir($dir) or mkdir($dir, 0777, true);
        $cArquivoTemp = $g['pathDadosUsuario'] . "arq_temp/" . basename($cArquivo);
        if (!@move_uploaded_file($mArquivo["tmp_name"], $cArquivoTemp)) {
            return "Erro ao tentar copiar arquivo temporario: " . $mArquivo['tmp_name'] . " para: " . $cArquivoTemp;
        }

        return true;
    }

    public function processarRetorno($T037_Id, $opcoes = array(), &$detalhe = null)
    {
        require_once('bibliotecas/classes/FIN001.php');
        $FIN001 = new FIN001();
        global $g;

        $limite = isset($opcoes['limite']) ? (int)$opcoes['limite'] : 0;
        $ultimoId = isset($opcoes['ultimoId']) ? (int)$opcoes['ultimoId'] : 0;
        $ultimoIdLido = $ultimoId;
        $lidos = 0;
        $extraWhere = '';
        if ($ultimoId > 0) {
            $extraWhere .= " AND T038_Id > '{$ultimoId}'";
        }
        $extraLimit = '';
        if ($limite > 0) {
            $extraLimit = " LIMIT {$limite}";
        }
        $T038 = mysql_query("SELECT *
                               FROM T038
                          LEFT JOIN T037 ON T037_Id = T038_T037_Id
                          LEFT JOIN T002 ON T002_Id = T038_T002_Id
                          LEFT JOIN D027 ON D027_Id = T002_D027_Id
                          LEFT JOIN D007 ON D007_Id = D027_D007_Id
                              WHERE T038_T037_Id = '{$T037_Id}'
                                AND (T002_Data_Recebimento='0000-00-00' OR T002_Data_Recebimento IS NULL){$extraWhere}
                           ORDER BY T038_Id ASC{$extraLimit}");
        while ($res = mysql_fetch_array($T038)) {
            $lidos++;
            $ultimoIdLido = $res['T038_Id'];
            $codigoBanco = $res['T037_Codigo_Banco'];
            $dataBaixa = $res['T038_Data_Credito'];
            $dataRecebimento = $res['T038_Data_Recebimento'];
            if ($res['T038_Valor_Juros_Mora'] <= 0) {
                $res['T038_Valor_Juros_Mora'] = '';
            }
            if (!empty($res['T002_Id'])) {
                $T002_Id = $res['T002_Id'];
                $valorCalculado = $res['T038_Valor_Juros_Mora'] + $res['T002_Valor_Saldo'] + $res['T038_Valor_Outros_Recebimentos'] + $res['T038_Valor_Taxa_IOF'];

                $outrosJuros = false;
                if ($res['T038_Flag_Autorizado'] != 'N' && (($res['T038_Valor_Recebido'] > $valorCalculado) && $valorCalculado > 0)) {
                    $outrosJuros = true;
                    $valorCalculado = $res['T038_Valor_Recebido'];
                    $valorDiferenca = $res['T038_Valor_Recebido'] - $res['T002_Valor_Saldo'];
                }
                $valorCalculado = number_format($valorCalculado, 2, '.', '');
                if (($res['T002_Valor_Saldo'] == $res['T038_Valor_Recebido'] || $valorCalculado == $res['T038_Valor_Recebido']) && $res['T038_Flag_Autorizado'] != 'N' && ($res['T002_Data_Baixa'] == '0000-00-00' || empty($res['T002_Data_Baixa']))) {
                    if ($res['T038_Codigo_Natureza_Recebimento'] == '06' || $res['T038_Codigo_Natureza_Recebimento'] == '08') {
                        if ($res['T038_Valor_Juros_Mora'] > 0) {
                            mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
                                                ) VALUES (
                                                    '{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - Juros Mora', CURDATE(), '{$res['T038_Valor_Juros_Mora']}', 'C'
                                                )");

                            $T020_Id = $g['mysqlLastId'];
                            $FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'JM');
                        }

                        if ($res['T038_Valor_Taxa_IOF'] > 0) {
                            mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
                                                ) VALUES (
                                                    '{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - IOF', CURDATE(), '{$res['T038_Valor_Taxa_IOF']}', 'C'
                                                )");

                            $T020_Id = $g['mysqlLastId'];
                            $FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'I');
                        }

                        if ($res['T038_Valor_Outros_Recebimentos'] > 0) {
                            mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
                                                ) VALUES (
                                                    '{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - Outros créditos', CURDATE(), '{$res['T038_Valor_Outros_Recebimentos']}', 'C'
                                                )");

                            $T020_Id = $g['mysqlLastId'];
                            $FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'OR');
                        }

                        if ($outrosJuros === true && empty($res['T038_Valor_Juros_Mora']) && $res['D007_Baixar_Automatico_Valor_Excedido_Retorno'] == 'S') {
                            mysql_query("INSERT INTO T020 (T020_T002_Id, T020_C004_Id, T020_Historico, T020_Data_Lancamento, T020_Valor, T020_Flag_Deb_Cred
                                                ) VALUES (
                                                    '{$res['T002_Id']}', '{$g['empresaAtual']}', '{$res['T037_Nome_Arquivo']} - JUROS', CURDATE(), '{$valorDiferenca}', 'C'
                                                )");

                            $T020_Id = $g['mysqlLastId'];
                            $res['outrosJuros'] = $valorDiferenca;
                            $FIN001->vinculaContaGrupoT020($T020_Id, false, $res, 'OJ');
                        }

                        mysql_query("UPDATE T038 SET T038_C007_Id_Processado = '{$g['usuarioAtual']}', T038_Data_Hora_Processado = CURRENT_TIMESTAMP() WHERE T038_Id = '{$res['T038_Id']}'");
                        mysqli_query("CALL T002_Gravar_Totalizacao('{$res['T002_Id']}')");
                        $extra = base64_encode("WHERE (T002_Id = '{$T002_Id}')");
                        $retorno = $FIN001->baixarRetorno($res['T037_D007_Id'], $dataBaixa, $extra, $dataRecebimento);
                        if ($retorno !== true) {
                            return $retorno;
                        }
                    }
                }
            }
        }
        $detalhe = array(
            'ultimoId' => $ultimoIdLido,
            'lidos' => $lidos,
        );
        return true;
    }

    public function converteData($data)
    {
        if ($data > 0) {
            $dia = substr($data, 0, 2);
            $mes = substr($data, 2, 2);
            $ano = substr($data, 4, 2);
            return date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano));
        } else {
            return "0000-00-00";
        }
    }

    public function converteNumero($numero)
    {
        if (isset($numero)) {
            $numero = preg_replace('/[^0-9]/', '', (string)$numero);
            if ($numero === '') {
                return 0;
            }
            $decimal = substr($numero, -2);
            $numero = substr($numero, 0, -2);
            if ($numero === '') {
                $numero = '0';
            }
            return $numero . '.' . $decimal;
        } else {
            return 0;
        }
    }

    public function formataNossoNumero($numero, $digito, $carteira)
    {
        if (isset($numero)) {
            $carteira = str_pad(preg_replace('/[^0-9]/', '', (string)$carteira), 2, '0', STR_PAD_LEFT);
            return $carteira . '/' . $numero . '-' . $digito;
        } else {
            return 0;
        }
    }

    private function montaAliasesNossoNumero($numero, $digito, $carteira, $nossoNumeroCompleto20)
    {
        $aliases = array();
        $numero = preg_replace('/[^0-9]/', '', (string)$numero);
        $numero = str_pad(substr($numero, -11), 11, '0', STR_PAD_LEFT);
        $digito = strtoupper(substr(trim((string)$digito), 0, 1));
        $carteira2 = str_pad(preg_replace('/[^0-9]/', '', (string)$carteira), 2, '0', STR_PAD_LEFT);
        $carteira3 = str_pad($carteira2, 3, '0', STR_PAD_LEFT);

        $aliases[] = $numero;
        if ($digito !== '') {
            $aliases[] = $numero . '-' . $digito;
            $aliases[] = $carteira2 . '/' . $numero . '-' . $digito;
            $aliases[] = $carteira3 . '/' . $numero . '-' . $digito;
            $aliases[] = $carteira2 . $numero . $digito;
            $aliases[] = $carteira3 . $numero . $digito;
        }

        $nossoNumeroCompleto20 = trim((string)$nossoNumeroCompleto20);
        if ($nossoNumeroCompleto20 !== '') {
            $aliases[] = $nossoNumeroCompleto20;
        }

        return array_values(array_unique(array_filter($aliases, 'strlen')));
    }

    private function montaWhereNossoNumero($aliases)
    {
        $comparacoes = array();

        foreach ($aliases as $alias) {
            $alias = mysql_real_escape_string($alias);
            $comparacoes[] = "T117_Nosso_Numero = '{$alias}'";
        }

        if (empty($comparacoes)) {
            return "1 = 0";
        }

        return implode(' OR ', $comparacoes);
    }

    private function parseIdentificacaoEmpresaBeneficiario($valor)
    {
        $valor = trim($valor);
        return array(
            'carteira' => substr($valor, 1, 3),
            'agencia' => ltrim(substr($valor, 4, 5), '0'),
            'conta' => ltrim(substr($valor, 9, 7), '0'),
            'dv_conta' => substr($valor, 16, 1),
        );
    }
}
