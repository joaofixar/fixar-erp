<?php
namespace hardness;
definirClasse('FIN003');
class FIN003_
{
    /**
     * listaTotais
     *
     * @param String $T019_Id
     * @return String
     */ 
    public function listaTotais($T019_Id)
    {    
        global $g;
        $T019 = mysql_query("SELECT SUM(IF(T019_Valor_Lancamento>0,T019_Valor_Lancamento,0)) Recebimentos,
                                    SUM(IF(T019_Valor_Lancamento<0,T019_Valor_Lancamento,0)) Pagamentos,
                                    COUNT(*) Lancamentos
                                    FROM T019
                               LEFT JOIN D024 ON D024_Id=T019_D024_Id
                               LEFT JOIN D014 ON D014_Id=T019_D014_Id
                                    WHERE T019_D007_Id='$T019_Id';");
        echo mysql_error();
        
        $resultado = array();
        while ($row = mysql_fetch_assoc($T019)) {
            $resultado[] = $row;
        }
        return $resultado[0];
    }

    /**
     * importarExtratoBancario
     *
     * @param File $arquivo
     * @return String/array
     */
       public function importarExtratoBancario($arquivo, $D007_Id)
    {
		global $g;
        //$extensao    = explode(".", $arquivo['name']);
        //if ($extensao[1] != 'OFX' && $extensao[1] != 'ofx') {
        $extensao    = pathinfo($arquivo['name']);
        if ($extensao['extension'] != 'OFX' && $extensao['extension'] != 'ofx') {
            return "Formato de Arquivo não suportado, o arquivo a ser enviado deve estar no formato OFX";
        }
        $conteudoOFX = file_get_contents($arquivo['tmp_name']);
        $inicio      = strpos($conteudoOFX, "<OFX>");
        //$head      = substr($conteudoOFX, 0, $inicio - 2);
        $conteudo    = substr($conteudoOFX, $inicio - 1);       
        if (strpos($conteudo, "<OFX>") !== 1) {
            // Banco Daycoval tem o inicio do extrato OFX diferente dos demais bancos
            $conteudo = substr($conteudoOFX, $inicio);
        } 

		// Ajuste de um erro que ocorreu para o extrato santanter
        // importador OFX para aceitar campos vazios no arquivo, sem alterar a estrutura dos bancos que já funcionam. Exemplo: tags como <FITID> e <CHECKNUM> vazias agora são tratadas corretamente na importação, mantendo a compatibilidade do parser genérico.
        preg_match_all("/<\/([A-Z0-9_]+)>/i", $conteudo, $tagsComFechamento);
        $tagsComFechamento = array_flip(array_map('strtoupper', $tagsComFechamento[1]));
		// Fim Ajuste para o extrato santanter
        $conteudo = str_replace("\r", '', $conteudo);
        $conteudo = str_replace('&', '&', $conteudo);

        $conteudo = preg_replace("/<([^\/][^>]+)>([^<]+)\n/i", "<$1>$2</$1>\n", $conteudo);

		// Ajuste de um erro que ocorreu para o extrato santanter
        // importador OFX para aceitar campos vazios no arquivo, sem alterar a estrutura dos bancos que já funcionam. Exemplo: tags como <FITID> e <CHECKNUM> vazias agora são tratadas corretamente na importação, mantendo a compatibilidade do parser genérico.
        $linhas = explode("\n", $conteudo);
        foreach ($linhas as &$linha) {
            if (preg_match('/^(\s*)<([A-Z0-9_]+)>\s*$/i', $linha, $match)) {
                $tag = strtoupper($match[2]);
                error_log("tag : ".$tag);
                if (!isset($tagsComFechamento[$tag])) {
                    $linha = $match[1] . '<' . $match[2] . '></' . $match[2] . '>';
                    error_log("linha corrigida: ".$linha);
                }
            }
        }
        // Fim Ajuste para o extrato santanter
        unset($linha);
        $conteudo = implode("\n", $linhas);
		// Fim Ajuste para o extrato santanter
        $conteudo = '<?xml version="1.0" encoding="UTF-8"?>' . $conteudo; 
        
        if (!simplexml_load_string($conteudo)){
            // Se não conseguir converter em XML, tira os caracteres especiais que podem dar problema e tenta validar novamente
            $conteudo = iconv("UTF-8", "UTF-8//IGNORE", $conteudo);
            if (!simplexml_load_string($conteudo)){
                return "O arquivo não está no formato correto para importação";
            }
        }

        $xml      = new \SimpleXMLElement($conteudo);

        $codigoBanco     = trim($xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->BANKID);
        $conta           = trim($xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->ACCTID);
        $dataInicio      = trim($this->corrigeData($xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->DTSTART));
        $dataFim         = trim($xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->DTEND);
        $listaTransacao  = $xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST;
        $saldo           = trim($this->corrigeNumero($xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->BALAMT));
        $dataSaldo       = trim($this->corrigeData($xml->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->DTASOF));
        $dataAtualizacao = trim($this->corrigeData($xml->SIGNONMSGSRSV1->SONRS->DTACCTUP));
        $dataArquivo     = trim($this->corrigeData($xml->SIGNONMSGSRSV1->SONRS->DTSERVER));
        $numeroBanco     = trim($xml->SIGNONMSGSRSV1->SONRS->FI->FID);
        /*********************************************************\
        | 	VERIFICAÇÃO DA CONTA E BANCO PARA IMPORTAR O EXTRATO  |
        \*********************************************************/
        $dadosConta  = explode("-", $conta);
        $numeroConta = $dadosConta[0];
        $dvConta     = $dadosConta[1];

        $codigoBanco = (strlen($codigoBanco) > 3) ? trim($codigoBanco, '0') : $codigoBanco;
        //Verificação da caixa economica, pois o conteudo da tag ACCTID é diferente para cada banco
        $numeroConta = ($codigoBanco == '104' && substr($numeroConta, 0, 4) == '0000') ? substr($numeroConta, 4) : $numeroConta;
        //Verificação do Sicredi, pois o conteudo da tag ACCTID é diferente
        $numeroConta = ($codigoBanco == '748' && substr($numeroConta, 2, 7) === '0000000') ? '0' . substr_replace($numeroConta,'',2,7) : $numeroConta;

        $sqlD007 = mysql_query("SELECT * FROM D007 LEFT JOIN D062 ON D062_Id = D007_D062_Id WHERE D007_Id = '{$D007_Id}'");
        $rowD007 = mysql_fetch_array($sqlD007);

        if(strcmp($codigoBanco, $rowD007['D062_Numero_Banco']) !== 0 ||
            ($numeroConta != $rowD007['D007_Numero_Agencia'].$rowD007['D007_Numero_Conta'].$rowD007['D007_Digito_Verificador_Conta'] && //Formado 1111222223 agencia/conta/dv conta
            $numeroConta != $rowD007['D007_Numero_Agencia'].$rowD007['D007_Numero_Conta'] && //Formado 111122222 agencia/conta
            $numeroConta.'-'.$dvConta != $rowD007['D007_Numero_Conta'].'-'.$rowD007['D007_Digito_Verificador_Conta'] && //Formado 22222-3 conta/dv conta
            $numeroConta.$dvConta != $rowD007['D007_Numero_Conta'].$rowD007['D007_Digito_Verificador_Conta'] && //Formado 222223 conta/dv conta
            $numeroConta != $rowD007['D007_Numero_Conta'])) { //Formado 22222 conta
            return "As informações da conta do extrato não conferem com a conta selecionada.<br> Banco: {$codigoBanco} <> {$rowD007['D062_Numero_Banco']} - Conta/Conta: {$numeroConta} <> {$rowD007['D007_Numero_Agencia']}{$rowD007['D007_Numero_Conta']}{$rowD007['D007_Digito_Verificador_Conta']}";
        }

        $T215 = mysql_query("SELECT * FROM T215 WHERE T215_Arquivo = '{$conteudo}'");
        if (mysql_num_rows($T215) > 0) {
            return "Este arquivo já foi importado!";
        }

        // Escapando caracteres especiais que podem dar problema no insert
		$conteudo = mysql_real_escape_string($conteudo);

        $sql = <<<EOT
			INSERT INTO T215 (
				T215_C004_Id,
				T215_C007_Id,
				T215_D007_Id,
				T215_Data,
				T215_Nome_Arquivo,
				T215_Arquivo,
				T215_Data_Inicio,
				T215_Data_Fim,
				T215_Data_Atualizacao_Conta,
				T215_Data_Geracao_Banco,
				T215_Valor_Saldo,
				T215_Data_Saldo
			) VALUES (
				'{$g['empresaAtual']}',
				'{$g['usuarioAtual']}',
				'{$D007_Id}',
				CURRENT_TIMESTAMP(),
				'{$arquivo['name']}',
				'{$conteudo}',
				'{$dataInicio}',
				'{$dataFim}',
				'{$dataAtualizacao}',
				'{$dataArquivo}',
				'{$saldo}',
				'{$dataSaldo}'
			);
EOT;
        mysql_query($sql);

        $error = mysql_error();
        if(!empty($error)){
            return $error;
        }

        $T215_Id = $g['mysqlLastId'];

        foreach ($listaTransacao->STMTTRN as $LANCAMENTO) {
            $tipo      = trim($LANCAMENTO->TRNTYPE);
            $data      = trim($this->corrigeData($LANCAMENTO->DTPOSTED));
            $valor     = trim($this->corrigeNumero($LANCAMENTO->TRNAMT));
            $id        = trim($LANCAMENTO->FITID);
            $nroCheque = trim($LANCAMENTO->CHECKNUM);
            $memo      = utf8_encode(trim($LANCAMENTO->MEMO));
            $sql       = <<<EOT
				INSERT INTO T216 (
					T216_T215_Id,
					T216_Data,
					T216_Historico,
					T216_Valor
				) VALUES (
					'{$T215_Id}',
					'{$data}',
					'{$memo}',
					'{$valor}'
				);
EOT;
            mysql_query($sql);
        }
        /*Dados do Recebedor
        #Virá preenchido
        <NAME>
        OU
        <PAYEE>
        <NAME>
        <ADDR1>
        <CITY>
        <STATE>
        <POSTALCODE>
        <PHONE>
        </PAYEE>
        //Dados de transferencia
        #Virá preenchido
        <BANKACCTTO>
        <BANKID>
        <ACCTID>
        <ACCTTYPE>
        </BANKACCTTO>
        OU
        <CCACCTTO>
        </CCACCTTO>
        */
        $this->aplicarRegrasExtrato($T215_Id);
        return array(
            $T215_Id
        );
    }

    /**
     * processarExtrato
     *
     * @param String $T215_Id
     * @return String/bool
     */
    public function processarExtrato($T215_Id)
    {
        global $g;
        $sqlT216 = mysql_query("SELECT * 
                                  FROM T216 
                             LEFT JOIN T215 ON T215_Id = T216_T215_Id 
                             LEFT JOIN T015 ON T015_Id = T216_T015_Id
                             LEFT JOIN T002 ON T002_Id = T216_T002_Id 
                                 WHERE T215_Id = '{$T215_Id}' 
                                   AND T216_Flag_Importar = 'S' 
                                   AND T216_Flag_Excluido = 'N' 
                              ORDER BY T216_Data");

        if (mysql_num_rows($sqlT216) <= 0) {
            return "Não foi possível processar este extrato, nenhuma transação foi selecionada";
        }
        $logErro = '';

        while ($T216 = mysql_fetch_array($sqlT216)) {

            $T019_Flag_Deb_Crd = ($T216['T216_Valor'] < 0) ? 0 : 1;
            $D014              = mysql_query("SELECT * from D014 WHERE D014_Id='{$T216['T216_D014_Id']}'");
            $mD014             = mysql_fetch_array($D014);
            $dataTransacao     = explode("-", $T216['T216_Data']);
            $mes               = $dataTransacao[1];
            $ano               = $dataTransacao[0];
            $T018              = mysql_query("SELECT D014_Subconta, T018_Valor_Saldo, T018_Valor FROM D014 LEFT JOIN T018 ON T018_D014_Id=D014_Id WHERE D014_Id='{$T216['T216_D014_Id']}' AND T018_Mes='{$mes}' AND T018_Ano='{$ano}'");
            $T018              = mysql_fetch_array($T018);

            if ($T216['T015_Id'] > 0){
                $T216['T216_Descricao'] = "DUPLI . No. {$T216['T015_Numero_Duplicata']}";
                $T216['T216_Historico'] = $T216['T015_Observacao'];

            } else if ($T216['T002_Id'] > 0){
                $T216['T216_Descricao'] = "DUPLI . No. {$T216['T002_Numero_Duplicata']}";
                $T216['T216_Historico'] = $T216['T002_Observacao'];
            }

            if($mD014['D014_Flag_Registrar_Conta'] == 'S' && (empty($T216['T216_T015_Id']) && empty($T216['T216_T002_Id']))){
                $logErro .= "O lançamento {$T216['T216_Descricao']} para a conta {$mD014['D014_SubConta']} precisa estar vinculado a uma conta receber ou a pagar.<br /><br />";
                mysql_query("UPDATE T216 SET T216_Flag_Importar = 'A' WHERE T216_Id = '{$T216['T216_Id']}'");
                continue;
            }

            if ($g['C031']['verificarSaldoConta'] == 'S' && $mD014['D014_Flag_Verificar_Saldo'] == 'S') {
                $Saldo_Conta          = $T018['T018_Valor_Saldo']+$T216['T015_Valor_Saldo'];
                $Valor_Duplicata      = $T216['T216_Valor'];
                $T019_Data_Lancamento = $T216['T216_Data'];
                
                if ($Valor_Duplicata < 0) {
                    $Valor_Duplicata = $Valor_Duplicata * -1;
                }
                
                if ($Valor_Duplicata > $Saldo_Conta && $T019_Flag_Deb_Crd == 0) {
                    $valorNecessario = ($Valor_Duplicata - $Saldo_Conta);
                    $Saldo_Conta     = gCorrigeNumero($Saldo_Conta);
                    $valorLimite     = gCorrigeNumero($T018['T018_Valor'] + $valorNecessario);
                    $logErro .= "O valor da duplicata é maior que o saldo da conta " . $T018['D014_Subconta'] . " !  <br />Saldo Conta: R$ <b>" . $Saldo_Conta . "</b> Limite necessário: <b>" . $valorLimite . "</b><br /><br />";
                } else {
                    $sql = <<<EOT
						INSERT INTO T019 (
                            T019_D027_Id,
							T019_D031_Id,
							T019_D014_Id,
							T019_D024_Id,
							T019_D007_Id,
							T019_C004_Id,
                            T019_T015_Id,
							T019_T002_Id,
							T019_Data_Lancamento,
							T019_Referente,
							T019_Valor_Lancamento,
							T019_Complemento,
							T019_Flag_Deb_Crd
						) VALUES (
                            '{$T216['T216_D027_Id']}',
							'{$T216['T216_D031_Id']}',
							'{$T216['T216_D014_Id']}',
							'{$T216['T216_D024_Id']}',
							'{$T216['T215_D007_Id']}',
							'{$T216['T215_C004_Id']}',
                            '{$T216['T216_T015_Id']}',
							'{$T216['T216_T002_Id']}',
							'{$T216['T216_Data']}',
							'{$T216['T216_Descricao']}',
							'{$T216['T216_Valor']}',
							'{$T216['T216_Historico']}',
							'{$T019_Flag_Deb_Crd}'
						);
EOT;
                    mysql_query($sql);
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        return $erro;
                    }
                    $T019_Id = $g['mysqlLastId'];
                    mysql_query("UPDATE T216 SET T216_Flag_Importar = 'I', T216_T019_Id = '{$T019_Id}' WHERE T216_Id = '{$T216['T216_Id']}'");
                    if($T216['T216_Valor'] < 0){
                        mysql_query("call T015_Baixar_Titulo('{$T216['T216_T015_Id']}','" . date('Y-m-d') . "')");
                        mysql_query("UPDATE T015 SET T015_D007_Id = '{$T216['T215_D007_Id']}' WHERE T015_Id = '{$T216['T216_T015_Id']}' AND IFNULL(T015_D007_Id,0) <= 0");
                    } else {
                        mysql_query("call T002_Baixar_Titulo('{$T216['T216_T002_Id']}','" . date('Y-m-d') . "')");
                        mysql_query("UPDATE T002 SET T002_D007_Id = '{$T216['T215_D007_Id']}' WHERE T002_Id = '{$T216['T216_T002_Id']}' AND IFNULL(T002_D007_Id,0) <= 0");
                    }
                    
                    require_once("bibliotecas/classes/FIN009.php");
                    $FIN009  = new FIN009();
                    $retorno = $FIN009->FIN009RecalculaProjecao($T216['T216_D014_Id'],null,null, $mes);
                }
            } else {
                $sql = <<<EOT
					INSERT INTO T019 (
                        T019_D027_Id,
						T019_D031_Id,
						T019_D014_Id,
						T019_D032_Id,
						T019_D024_Id,
						T019_D007_Id,
						T019_C004_Id,
                        T019_T015_Id,
						T019_T002_Id,
						T019_Data_Lancamento,
						T019_Referente,
						T019_Valor_Lancamento,
						T019_Complemento,
						T019_Flag_Deb_Crd
					) VALUES (
                        '{$T216['T216_D027_Id']}',
						'{$T216['T216_D031_Id']}',
						'{$T216['T216_D014_Id']}',
						'{$T216['T216_D032_Id']}',
						'{$T216['T216_D024_Id']}',
						'{$T216['T215_D007_Id']}',
						'{$T216['T215_C004_Id']}',
                        '{$T216['T216_T015_Id']}',
						'{$T216['T216_T002_Id']}',
						'{$T216['T216_Data']}',
						'{$T216['T216_Descricao']}',
						'{$T216['T216_Valor']}',
						'{$T216['T216_Historico']}',
						'{$T019_Flag_Deb_Crd}'
					);
EOT;
                mysql_query($sql);
                $erro = mysql_error();
                if (!empty($erro)) {
                    return $erro;
                }
                $T019_Id = $g['mysqlLastId'];
                mysql_query("UPDATE T216 SET T216_Flag_Importar = 'I', T216_T019_Id = '{$T019_Id}' WHERE T216_Id = '{$T216['T216_Id']}'");
                if($T216['T216_Valor'] < 0){
                    mysql_query("call T015_Baixar_Titulo('{$T216['T216_T015_Id']}','" . date('Y-m-d') . "')");
                    mysql_query("UPDATE T015 SET T015_D007_Id = '{$T216['T215_D007_Id']}' WHERE T015_Id = '{$T216['T216_T015_Id']}' AND IFNULL(T015_D007_Id,0) <= 0");
                } else {
                    mysql_query("call T002_Baixar_Titulo('{$T216['T216_T002_Id']}','" . date('Y-m-d') . "')");
                    mysql_query("UPDATE T002 SET T002_D007_Id = '{$T216['T215_D007_Id']}' WHERE T002_Id = '{$T216['T216_T002_Id']}' AND IFNULL(T002_D007_Id,0) <= 0");
                }
            }
            
        }
        mysql_query("UPDATE T215 SET T215_Data_Processado = CURRENT_TIMESTAMP() WHERE T215_Id = '{$T215_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            return $erro;
        }
        if ($logErro == '') {
            return true;
        } else {
            return "<b>Atenção, o(s) registro(s) abaixo não puderam ser processados</b><br /><br />" . $logErro;
        }
    }
    
    /**
     * incluirRegraExtrato
     *
     * @param String $D007_Id
     * @return true
     */     
    public function incluirRegraExtrato($D007_Id)
    {
        mysql_query("INSERT INTO T217 (T217_D007_Id) VALUES ('{$D007_Id}')");
        return true;
    }
    
    /**
     * aplicarRegrasExtrato
     *
     * @param String $T215_Id
     * @return String/bool
     */     
    public function aplicarRegrasExtrato($T215_Id)
    {
        global $g;
        $T215               = mysql_fetch_array(mysql_query("SELECT T215_D007_Id FROM T215 WHERE T215_Id = '{$T215_Id}'"));
        $D007_Id            = $T215['T215_D007_Id'];
        $regrasNaoAplicadas = 0;
        $regrasAplicadas    = 0;
        $sqlT216            = mysql_query("SELECT * FROM T216 WHERE T216_T215_Id = '{$T215_Id}' AND (T216_D027_Id <= 0 OR T216_D027_Id IS NULL) AND (T216_D032_Id <= 0 OR T216_D032_Id IS NULL) AND (T216_D014_Id <= 0 OR T216_D014_Id IS NULL) AND (T216_D024_Id <= 0 OR T216_D024_Id IS NULL)");
        while ($T216 = mysql_fetch_array($sqlT216)) {
            $encontrado = false;
            $sqlHistorico = mysql_query("SELECT * FROM T217 WHERE T217_D007_Id = '{$D007_Id}' AND '%{$T216['T216_Historico']}%' LIKE CONCAT('%',REPLACE(T217_Historico, ' ', '%'),'%') AND T217_Historico != ''");
            if (mysql_num_rows($sqlHistorico) > 0) {
                $res = mysql_fetch_array($sqlHistorico);
                mysql_query("UPDATE T216 SET 
                                T216_D027_Id = '{$res['T217_D027_Id']}',
								T216_D031_Id = '{$res['T217_D031_Id']}',
								T216_D032_Id = '{$res['T217_D032_Id']}',
								T216_D014_Id = '{$res['T217_D014_Id']}',
								T216_D024_Id = '{$res['T217_D024_Id']}',
								T216_Descricao = '{$res['T217_Descricao']}' 
							WHERE T216_Id = '{$T216['T216_Id']}'");
                $regrasAplicadas++;
                $encontrado = true;
            } else {
                $sqlDescricao = mysql_query("SELECT * FROM T217 WHERE T217_D007_Id = '{$D007_Id}' AND '%{$T216['T216_Descricao']}%' LIKE CONCAT('%',REPLACE(T217_Descricao, ' ', '%'),'%') AND (T217_Historico IS NULL OR T217_Historico = '')");
                if (mysql_num_rows($sqlDescricao) > 0) {
                    $res = mysql_fetch_array($sqlDescricao);
                    mysql_query("UPDATE T216 SET 
                                    T216_D027_Id = '{$res['T217_D027_Id']}',
									T216_D031_Id = '{$res['T217_D031_Id']}',
									T216_D032_Id = '{$res['T217_D032_Id']}',
									T216_D014_Id = '{$res['T217_D014_Id']}',
									T216_D024_Id = '{$res['T217_D024_Id']}',
									T216_Descricao = '{$res['T217_Descricao']}'  
								WHERE T216_Id = '{$T216['T216_Id']}'");
                    $regrasAplicadas++;
                    $encontrado = true;
                } else {
                    $sqlT019 = mysql_query("SELECT * FROM T019 WHERE T019_Referente = '{$T216['T216_Descricao']}' AND T019_Referente != '' AND T019_Valor_Lancamento = '{$T216['T216_Valor']}'");
                    if (mysql_num_rows($sqlT019) > 0) {
                        $res = mysql_fetch_array($sqlT019);
                        mysql_query("UPDATE T216 SET T216_D031_Id = '{$res['T019_D031_Id']}',
										T216_D027_Id = '{$res['T019_D027_Id']}',
										T216_D032_Id = '{$res['T019_D032_Id']}',
										T216_D014_Id = '{$res['T019_D014_Id']}',
										T216_D024_Id = '{$res['T019_D024_Id']}',
										T216_Descricao = '{$res['T019_Referente']}'  
									WHERE T216_Id = '{$T216['T216_Id']}'");
                        $regrasAplicadas++;
                        $encontrado = true;
                    } else {
                        $regrasNaoAplicadas++;
                        $encontrado = false;
                    }
                }
            }

            if($encontrado === false){

                if($T216['T216_Valor'] < 0){
                    $T216['T216_Valor'] = $T216['T216_Valor'] * -1;

                    $sqlT015 = mysql_query("SELECT * FROM T015 WHERE T015_Valor_Saldo = '{$T216['T216_Valor']}' AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Data_Pagamento = '0000-00-00' AND T015_C004_Id='{$g['empresaAtual']}' ORDER BY T015_Data_Vencimento ASC LIMIT 1");
                    if(mysql_num_rows($sqlT015) == 1){
                        $T015 = mysql_fetch_array($sqlT015);

                        $verifica_T216 = mysql_query("SELECT * FROM T216 WHERE T216_T015_Id='{$T015['T015_Id']}'");
                        if(mysql_num_rows($verifica_T216) > 0){ continue; }

                        mysql_query("UPDATE T216 SET T216_D031_Id = '{$T015['T015_D031_Id']}',
                                        T216_D032_Id = '{$T015['T015_D032_Id']}',
                                        T216_D014_Id = '{$T015['T015_D014_Id']}',
                                        T216_D024_Id = '{$T015['T015_D024_Id']}',
                                        T216_T015_Id = '{$T015['T015_Id']}'  
                                    WHERE T216_Id = '{$T216['T216_Id']}'");
                        $regrasAplicadas++;
                    }
                } else {
                    $sqlT002 = mysql_query("SELECT * FROM T002 WHERE T002_Valor_Saldo = '{$T216['T216_Valor']}' AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND T002_Data_Recebimento  = '0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}' ORDER BY T002_Data_Vencimento ASC LIMIT 1");
                    if(mysql_num_rows($sqlT002) == 1){
                        $T002 = mysql_fetch_array($sqlT002);

                        $verifica_T216 = mysql_query("SELECT * FROM T216 WHERE T216_T002_Id='{$T002['T002_Id']}'");
                        if(mysql_num_rows($verifica_T216) > 0){ continue; }

                        mysql_query("UPDATE T216 SET T216_D027_Id = '{$T002['T002_D027_Id']}',
                                        T216_D032_Id = '{$T002['T002_D032_Id']}',
                                        T216_D014_Id = '{$T002['T002_D014_Id']}',
                                        T216_D024_Id = '{$T002['T002_D024_Id']}',
                                        T216_T002_Id = '{$T002['T002_Id']}'  
                                    WHERE T216_Id = '{$T216['T216_Id']}'");
                        $regrasAplicadas++;
                    }
                }
            }
        }
        if ($regrasNaoAplicadas > 0 && $regrasAplicadas <= 0) {
            return "Nenhuma regra foi aplicada!";
        }
        return true;
    }
    
    /**
     * incluirTransferencia
     *
     * @param Form $form
     * @return String/bool
     */    
    public function incluirTransferencia($form)
    {
        global $g;
        $D007_Id_Origem        = $form->campoValorEnviado('D007_Id_Origem');
        $D007_Id_Destino       = $form->campoValorEnviado('D007_Id_Destino');
        $T019_Data_Lancamento  = $form->campoValorEnviado('T019_Data_Lancamento');
        $T019_Numero_Cheque    = $form->campoValorEnviado('T019_Numero_Cheque');
        $T019_Referente        = $form->campoValorEnviado('T019_Referente');
        $T019_Complemento      = $form->campoValorEnviado('T019_Complemento');
        $T019_Valor_Lancamento = gCorrigeNumeroInverte($form->campoValorEnviado('T019_Valor_Lancamento'));
        $T019_Observacao       = $form->campoValorEnviado('T019_Observacao');
        $T019_D027_Id          = $form->campoValorEnviado('T019_D027_Id');
        $T019_D032_Id          = $form->campoValorEnviado('T019_D032_Id');
        $T019_D014_Id          = $form->campoValorEnviado('T019_D014_Id');
        $T019_D024_Id          = $form->campoValorEnviado('T019_D024_Id');
        // Faz o select na conta e testa se a o campo para verificar o saldo da conta está igual a SIM
        /*
        $D014 = mysql_query("SELECT D014_Flag_Verificar_Saldo from D014 WHERE D014_Id='{$D014_Id}'");
        $mD014 = mysql_fetch_array($D014);
        if($g['C031']['verificarSaldoConta'] == 'S' && $mD014['D014_Flag_Verificar_Saldo'] == 'S'){
        $Saldo_Conta = gCorrigeNumeroInverte($form->campoValorEnviado('Saldo_Conta'));
        $Valor_Duplicata = gCorrigeNumeroInverte($T019_Valor_Lancamento);
        $T019_Data_Lancamento  = explode("/", $T019_Data_Lancamento);
        $mes = $T019_Data_Lancamento[1];
        $ano = $T019_Data_Lancamento[2];
        
        if ($Valor_Duplicata < 0){
        $Valor_Duplicata = $Valor_Duplicata * -1;
        }
        
        if($Valor_Duplicata > $Saldo_Conta && $T019_Flag_Deb_Crd == 0){
        $valorNecessario = ($Valor_Duplicata - $Saldo_Conta);
        $Saldo_Conta = gCorrigeNumero($Saldo_Conta);
        
        $T018=mysql_query("SELECT T018_Valor FROM T018 WHERE T018_D014_Id='{$D014_Id}' AND T018_Mes='{$mes}' AND T018_Ano='{$ano}'");
        $mT018=mysql_fetch_array($T018);
        $valorLimite = gCorrigeNumero($mT018['T018_Valor'] + $valorNecessario);
        return "O valor da duplicata é maior que o saldo da conta! <br />Saldo Conta: R$ <b>" . $Saldo_Conta . "</b><br />Limite necessário: <b>" . $valorLimite . "</b>";
        } else {
        if ($T019_Flag_Deb_Crd == 0) {
        if ($T019_Valor_Lancamento > 0) {
        $T019_Valor_Lancamento = gCorrigeNumero(gCorrigeNumeroInverte($T019_Valor_Lancamento) * -1);
        }
        } else {
        if ($T019_Valor_Lancamento < 0) {
        $T019_Valor_Lancamento = gCorrigeNumero(gCorrigeNumeroInverte($T019_Valor_Lancamento) * -1);
        }
        }
        
        //*****************************************************************************************************
        // INSERE OS LANÇAMENTOS NA EMPRESA DESTINO E ORIGEM
        //*****************************************************************************************************
        $sql = "INSERT INTO T019 (
        
        ) VALUES (
        
        )";
        
        // Chamada de método que atualiza a projeção orçamentária da conta selecionada paa atualizar o saldo da conta
        $retorno = $FIN009->FIN009RecalculaProjecaoMesAtual($form->campoValorEnviado('T019_D014_Id'), $mes);
        if($retorno === true){
        return true;
        } else {
        return $retorno;
        }
        }
        } else {*/
        $T019_Data_Lancamento  = gCorrigeDataInverte($T019_Data_Lancamento);
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        //*****************************************************************************************************
        // INSERE OS LANÇAMENTOS NA CONTA ORIGEM
        if ($T019_Valor_Lancamento > 0) {
            $T019_Valor_Lancamento = $T019_Valor_Lancamento * -1;
        }
        $sql = "INSERT INTO T019 (
					T019_D007_Id,
					T019_Data_Lancamento,
					T019_Numero_Cheque,
					T019_Referente,
					T019_Complemento,
					T019_Valor_Lancamento,
					T019_Flag_Deb_Crd,
					T019_Observacao,
					T019_D027_Id,
					T019_D032_Id,
					T019_D014_Id,
					T019_D024_Id,
					T019_C004_Id
				) VALUES (
					'{$D007_Id_Origem}',
					'{$T019_Data_Lancamento}',
					'{$T019_Numero_Cheque}',
					'{$T019_Referente}',
					'{$T019_Complemento}',
					'{$T019_Valor_Lancamento}',
					'0',
					'{$T019_Observacao}',
					'{$T019_D027_Id}',
					'{$T019_D032_Id}',
					'{$T019_D014_Id}',
					'{$T019_D024_Id}',
					'{$g['empresaAtual']}'
				)";
        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Não foi possível realizar os lançamentos na conta de origem";
        }
        
        //*******************************************************************************************************
        // INSERE OS LANÇAMENTOS NA CONTA DESTINO
        if ($T019_Valor_Lancamento < 0) {
            $T019_Valor_Lancamento = $T019_Valor_Lancamento * -1;
        }
        
        $D007_Destino = mysql_fetch_array(mysql_query("SELECT * FROM D007 WHERE D007_Id = '{$D007_Id_Destino}'"));
        $sql          = "INSERT INTO T019 (
						T019_D007_Id,
						T019_Data_Lancamento,
						T019_Numero_Cheque,
						T019_Referente,
						T019_Complemento,
						T019_Valor_Lancamento,
						T019_Flag_Deb_Crd,
						T019_Observacao,
						T019_D027_Id,
						T019_D032_Id,
						T019_D014_Id,
						T019_D024_Id,
						T019_C004_Id
					) VALUES (
						'{$D007_Id_Destino}',
						'{$T019_Data_Lancamento}',
						'{$T019_Numero_Cheque}',
						'{$T019_Referente}',
						'{$T019_Complemento}',
						'{$T019_Valor_Lancamento}',
						'1',
						'{$T019_Observacao}',
						'{$T019_D027_Id}',
						'{$T019_D032_Id}',
						'{$T019_D014_Id}',
						'{$T019_D024_Id}',
						'{$D007_Destino['D007_C004_Id']}'
					)";
        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "Não foi possível realizar os lançamentos na conta de destino";
        }
        //*****************************************************************************************************
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        return true;
        //}
    }
    
    /**
     * desmembrarTransacao
     *
     * @param String $T216_Id
     * @param String $extra
     * @return String/bool
     */
    public function desmembrarTransacao($T216_Id, $extra)
    {
        $extra = gRetirarGroupBy($extra);
        $T216                      = mysql_fetch_array(mysql_query("SELECT * FROM T216 WHERE T216_Id = '{$T216_Id}'"));
        $T015                      = mysql_fetch_array(mysql_query("SELECT SUM(T015_Valor_Total) as valorTotalTitulos FROM T015 {$extra}"));
        $T015['valorTotalTitulos'] = $T015['valorTotalTitulos'] * -1;
        log("SELECT SUM(T015_Valor_Total) as valorTotalTitulos FROM T015 {$extra}");
        if ($T015['valorTotalTitulos'] != $T216['T216_Valor']) {
            return "A soma dos títulos selecionados está diferente do valor da transação!";
        }
        log("SELECT * FROM T015 {$extra}");
        $sqlT015 = mysql_query("SELECT * FROM T015 {$extra}");
        $titulos = mysql_num_rows($sqlT015);
        if($titulos == 1){
            $T015 = mysql_fetch_array($sqlT015);
            mysql_query("UPDATE T216 SET T216_T015_Id = '{$T015['T015_Id']}',
                                         T216_D024_Id = '{$T015['T015_D024_Id']}',
                                         T216_D032_Id = '{$T015['T015_D032_Id']}',
                                         T216_D014_Id = '{$T015['T015_D014_Id']}',
                                         T216_D031_Id = '{$T015['T015_D031_Id']}' WHERE T216_Id = '{$T216_Id}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                return $erro;
            }

            mysql_query("UPDATE T015 SET T015_Flag_Marcar_Lancamento = 'N' WHERE T015_Id = '{$T015['T015_Id']}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                return $erro;
            }
        } else {
            while ($T015 = mysql_fetch_array($sqlT015)) {
                $T216_Historico           = $T216['T216_Historico'] . " (DISM. " . gCorrigeNumero($T216['T216_Valor']) . ')';
                $T015['T015_Valor_Total'] = $T015['T015_Valor_Total'] * -1;
                $sql = "INSERT INTO T216 (
                            T216_T215_Id,
    						T216_D031_Id,
    						T216_D032_Id,
    						T216_D014_Id,
    						T216_D024_Id,
    						T216_T015_Id,
    						T216_Data,
    						T216_Historico,
    						T216_Valor,
    						T216_Descricao
    					) VALUES (
    						'{$T216['T216_T215_Id']}',
    						'{$T015['T015_D031_Id']}',
    						'{$T015['T015_D032_Id']}',
    						'{$T015['T015_D014_Id']}',
    						'{$T015['T015_D024_Id']}',
    						'{$T015['T015_Id']}',
    						'{$T216['T216_Data']}',
    						'{$T216_Historico}',
    						'{$T015['T015_Valor_Total']}',
    						'{$T015['T015_Nome_Cliente']}'
    					);";
                mysql_query($sql);
                $erro = mysql_error();
                if (!empty($erro)) {
                    return $erro;
                }
                mysql_query("UPDATE T015 SET T015_Flag_Marcar_Lancamento = 'N' WHERE T015_Id = '{$T015['T015_Id']}'");
                $erro = mysql_error();
                if (!empty($erro)) {
                    return $erro;
                }
            }
            mysql_query("UPDATE T216 SET T216_Flag_Excluido = 'S' WHERE T216_Id = '{$T216_Id}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                return $erro;
            }
        }
        return true;
    }

        /**
     * desmembrarTransacaoRecebida
     *
     * @param String $T216_Id
     * @param String $extra
     * @return String/bool
     */
    public function desmembrarTransacaoRecebida($T216_Id, $extra)
    {
        $T216                      = mysql_fetch_array(mysql_query("SELECT * FROM T216 WHERE T216_Id = '{$T216_Id}'"));
        $T002                      = mysql_fetch_array(mysql_query("SELECT SUM(T002_Valor_Total) as valorTotalTitulos FROM T002 {$extra}"));
        $T002['valorTotalTitulos'] = $T002['valorTotalTitulos'];
        log("SELECT SUM(T002_Valor_Total) as valorTotalTitulos FROM T002 {$extra}");
        log("SELECT * FROM T216 WHERE T216_Id = '{$T216_Id}'");
        $diferenca = $T002['valorTotalTitulos'] - $T216['T216_Valor'];
        
        if ($T002['valorTotalTitulos'] != $T216['T216_Valor']) {
			$totalTitulos = gCorrigeNumero($T002['valorTotalTitulos']);
			$totalExtrato = gCorrigeNumero($T216['T216_Valor']);
			$diferenca    = gCorrigeNumero($diferenca);
			$mensagemErro = "A soma dos títulos selecionados está diferente do valor da transação! <br>";
			$mensagemErro .= "Valor total selecionado: <b>R$" . $totalTitulos . "</b><br>";
			$mensagemErro .= "Valor do extrato: <b>R$" . $totalExtrato . "</b><br>";
			$mensagemErro .= "Diferença: <b>R$" . $diferenca . "</b>";
            return $mensagemErro;
        }
        log("SELECT * FROM T002 {$extra}");
        $sqlT002 = mysql_query("SELECT * FROM T002 {$extra}");
        $titulos = mysql_num_rows($sqlT002);
        if($titulos == 1){
            $T002 = mysql_fetch_array($sqlT002);
            mysql_query("UPDATE T216 SET T216_T002_Id = '{$T002['T002_Id']}',
                                         T216_D024_Id = '{$T002['T002_D024_Id']}',
                                         T216_D032_Id = '{$T002['T002_D032_Id']}',
                                         T216_D014_Id = '{$T002['T002_D014_Id']}',
                                         T216_D027_Id = '{$T002['T002_D027_Id']}' WHERE T216_Id = '{$T216_Id}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                return $erro;
            }

            mysql_query("UPDATE T002 SET T002_Flag_Marcar_Lancamento = 'N' WHERE T002_Id = '{$T002['T002_Id']}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                return $erro;
            }
        } else {
            while ($T002 = mysql_fetch_array($sqlT002)) {
                $T216_Historico           = $T216['T216_Historico'] . " (DISM. " . gCorrigeNumero($T216['T216_Valor']) . ')';
                $T002['T002_Valor_Total'] = $T002['T002_Valor_Total'];
                $sql = "INSERT INTO T216 (
                            T216_T215_Id,
                            T216_D027_Id,
                            T216_D032_Id,
                            T216_D014_Id,
                            T216_D024_Id,
                            T216_T002_Id,
                            T216_Data,
                            T216_Historico,
                            T216_Valor,
                            T216_Descricao
                        ) VALUES (
                            '{$T216['T216_T215_Id']}',
                            '{$T002['T002_D027_Id']}',
                            '{$T002['T002_D032_Id']}',
                            '{$T002['T002_D014_Id']}',
                            '{$T002['T002_D024_Id']}',
                            '{$T002['T002_Id']}',
                            '{$T216['T216_Data']}',
                            '{$T216_Historico}',
                            '{$T002['T002_Valor_Total']}',
                            '{$T002['T002_Nome_Cliente']}'
                        );";
                mysql_query($sql);
                $erro = mysql_error();
                if (!empty($erro)) {
                    return $erro;
                }
                mysql_query("UPDATE T002 SET T002_Flag_Marcar_Lancamento = 'N' WHERE T002_Id = '{$T002['T002_Id']}'");
                $erro = mysql_error();
                if (!empty($erro)) {
                    return $erro;
                }
            }
            mysql_query("UPDATE T216 SET T216_Flag_Excluido = 'S' WHERE T216_Id = '{$T216_Id}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                return $erro;
            }
        }
        return true;
    }

    /**
     * corrigeData
     *
     * @param String $data
     * @return String
     */
    function corrigeData($data)
    {
        $dia = substr($data, 6, 2);
        $mes = substr($data, 4, 2);
        $ano = substr($data, 0, 4);
        return $ano . "-" . $mes . "-" . $dia;
    }

    /**
     * corrigeNumero
     *
     * @param String $valor
     * @return String
     */
    function corrigeNumero($valor)
    {
        if(strpos($valor, ",") === false && strpos($valor, ".") === false){
            return substr_replace($valor, '.', -2, 0);
        }elseif(strpos($valor, ",") === false){
            return $valor;
        } else {
            return gCorrigeNumeroInverte($valor);
        }
    }
}
