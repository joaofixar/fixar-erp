<?php
namespace hardness;
definirClasse('FIN002');
class FIN002_
{

    /**
     * excluir
     *
     * @param String $T015_Id
     * @return true
     */ 
    public function excluir($T015_Id)
    {
        global $g;
        /**
         * 
         * 'T019_T015_Id', 'T015_T013_Id', 'T015_T007_Id'
         * 
         */
        
        /**
         * Faz a veificação de integridade referencial antes de cancelar
         */
        $T015 = mysql_query("SELECT T019_Id, T013_Id, T007_Id 
                               FROM T015
                          LEFT JOIN T019 ON T019_T015_Id=T015_Id
                          LEFT JOIN T013 ON T013_Id=T015_T013_Id
                          LEFT JOIN T007 ON T007_Id=T015_T007_Id
                              WHERE T015_Id='{$T015_Id}'");

        $mT015 = mysql_fetch_array($T015);
        if ($mT015['T019_Id']>0) {
            return "Titulo não pode ser excluido pois possui lançamentos bancários. Favor verificar.";
        }
        if ($mT015['T013_Id']>0) {
            return "Titulo não pode ser excluido pois foi gerado a partir de uma nota fiscal recebida. Favor verificar.";
        }
        if ($mT015['T007_Id']>0) {
            return "Titulo não pode ser excluido pois foi gerado a partir de uma nota fiscal emitida. Favor verificar.";
        }
        
        $T015_Id = mysql_real_escape_string($T015_Id);
        $sql     = "UPDATE T015 set T015_Flag_Cancelada='S',T015_T015_Id_Agrupado=9999999999 WHERE T015_Id='{$T015_Id}'";
        mysql_query($sql);
        $sql = <<<EOT
            UPDATE T015 set 
                   T015_T015_Id_Agrupado=NULL,
                   T015_Flag_Cancelada='N',
                   T015_Data_Pagamento='0000-00-00',
                   T015_Flag_Reparcelar=''
             WHERE T015_T015_Id_Agrupado='{$T015_Id}'
EOT;
        mysql_query($sql);
        return true;
    }
    
    /**
     * excluirAcrescimoDesconto
     *
     * @param String $T033_Id
     * @param String $T033_T015_Id
     * @return true
     */     
    public function excluirAcrescimoDesconto($T033_Id, $T033_T015_Id)
    {
        global $g;
        
        $T033_Id = mysql_real_escape_string($T033_Id);
        $sql     = "DELETE FROM T033 WHERE T033_Id='{$T033_Id}'";
        mysql_query($sql);
        mysql_query("CALL T015_Gravar_Totalizacao('$T033_T015_Id')");
        return true;
    }
    
    /**
     * atualizaTitulosParaBaixarT103
     *
     * @return true
     */     
    public function atualizaTitulosParaBaixarT103()
    {
        /**
         * Lista todos os título que serão baixados e adiciona eles na tabela T103 (tabela temporaria utilizada no formulário)
         *
         * @return true/string(erro)
         */
        global $g;
        
        // Lista todos os registro que serão baixados
        $cClausula = " SELECT ";
        $cClausula .= " T015_D031_Id, ";
        $cClausula .= " T015_Valor_Saldo, ";
        $cClausula .= " T015_Id ";
        $cClausula .= " FROM T015 ";
        $cClausula .= " LEFT JOIN T103 ON T103_T015_Id=T015_Id ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $cClausula .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $cClausula .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $cClausula .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $cClausula .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $cClausula .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " WHERE T015_Flag_Baixar_Lote='S' ";
        $cClausula .= "   AND T015_Data_Pagamento='0000-00-00' ";
        $cClausula .= "   AND T103_Id is null ";
        $cClausula .= "   AND T015_Flag_Cancelada!='S' ";
        if($g['C031']['contasPagarUnificado'] != 'S'){
            $cClausula .= "   AND T015_C004_Id='{$g['empresaAtual']}' ";
        }
        $cClausula .= " ORDER BY T015_Data_Vencimento,D024_Nome_Empresa";
        $cSql_T015 = mysql_query($cClausula);
        if (mysql_error()) {
            return mysql_error();
        }
        
        while ($mT015 = mysql_fetch_array($cSql_T015)) {
            $cClausula = " INSERT INTO T103 (";
            $cClausula .= " T103_D031_Id, ";
            $cClausula .= " T103_T015_Id, ";
            $cClausula .= " T103_Valor_Recebido, ";
            $cClausula .= " T103_Valor_Total_Recebido, ";
            $cClausula .= " T103_Flag_Baixar_Lote ";
            $cClausula .= " ) VALUES ( ";
            $cClausula .= " '$mT015[T015_D031_Id]', ";
            $cClausula .= " '$mT015[T015_Id]', ";
            $cClausula .= " '$mT015[T015_Valor_Saldo]', ";
            $cClausula .= " '$mT015[T015_Valor_Saldo]', ";
            $cClausula .= " 'S' ";
            $cClausula .= " ); ";
            $cSql_T103 = mysql_query($cClausula);
            if (mysql_error()) {
                return mysql_error();
            }
        }
        return true;
    }
    
    /**
     * updateT015_Flag_Baixar_Lote
     *
     * @param String $T015_Id
     * @param String $selected
     * @return true
     */     
    public function updateT015_Flag_Baixar_Lote($T015_Id, $selected)
    {
        global $g;
        
        $T015_Id  = mysql_real_escape_string($T015_Id);
        $selected = mysql_real_escape_string($selected);
        mysqli_query("update T015 set T015_Flag_Baixar_Lote='$selected' WHERE T015_Id='$T015_Id'");
        return true;
    }

    /**
     * listaTotalBaixar
     *
     * @param String $extra
     * @return String
     */    
    public function listaTotalBaixar($extra)
    {
        global $g;
        
        $sql = " SELECT ";
        $sql .= " sum(if(T015_Data_Pagamento='0000-00-00',T015_Valor_Saldo,0)) Total_Pagar, ";
        $sql .= " sum(if(T015_Data_Pagamento!='0000-00-00',T015_Valor_Saldo,0)) Total_Pago, ";
        $sql .= " sum(if(T015_Data_Pagamento='0000-00-00' AND T015_Flag_Baixar_Lote='S',T015_Valor_Saldo,0)) Total_Baixar ";
        $sql .= " FROM T015 ";
        /*$sql .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $sql .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $sql .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $sql .= " LEFT JOIN D073 ON D073_Id=T015_D073_Id ";
        $sql .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $sql .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE T015_C004_Id='{$g['empresaAtual']}'";*/
        
        $extraNovo = <<<EOT
			(T015_C004_Id='{$g['empresaAtual']}')
EOT;
        $sql .= gInsertExtraWhere($extra, $extraNovo);
        
        $sqlReturn = mysql_query($sql);
        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado[0];
    }
    
    /**
     * estornar
     *
     * @param String $T015_Id
     * @param String $observacao
     * @return String/bool
     */  
    public function estornar($T015_Id, $observacao, $dataEstorno = '0000-00-00')
    {
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $cClausula = " SELECT * FROM T015 WHERE T015_Id='$T015_Id' ";
        $cT015     = mysql_query($cClausula);
        $mT015     = mysql_fetch_array($cT015);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p$cClausula";
        }

        if($g['C031']['HabilitarDataAtualEstornoPagar'] == 'S'){
            $dataEstorno = date('Y-m-d'); //Pega a data atual
        }

        $valorEstorno = $this->verificaValorEstorno($mT015);
        
        $cClausula = " INSERT INTO T019 (";
        $cClausula .= " T019_T015_Id, ";
        $cClausula .= " T019_D007_Id, ";
        $cClausula .= " T019_C004_Id, ";
        $cClausula .= " T019_D024_Id, ";
        $cClausula .= " T019_D014_Id, ";
        $cClausula .= " T019_Complemento, ";
        $cClausula .= " T019_Numero_Agencia, ";
        $cClausula .= " T019_Numero_Conta, ";
        $cClausula .= " T019_Numero_Cheque, ";
        $cClausula .= " T019_Data_Lancamento, ";
        $cClausula .= " T019_Referente, ";
        $cClausula .= " T019_Valor_Lancamento, ";
        $cClausula .= " T019_Flag_Deb_Crd, ";
        $cClausula .= " T019_Obs_Estorno ";
        $cClausula .= " ) VALUES ( ";
        $cClausula .= " '$T015_Id', ";
        $cClausula .= " '$mT015[T015_D007_Id]', ";
        $cClausula .= " '$mT015[T015_C004_Id]', ";
        $cClausula .= " '$mT015[T015_D024_Id]', ";
        $cClausula .= " '$mT015[T015_D014_Id]', ";
        $cClausula .= " '$mT015[T015_Nome_Cliente]', ";
        $cClausula .= " '$mT015[T015_Numero_Agencia]', ";
        $cClausula .= " '$mT015[T015_Numero_Conta]', ";
        $cClausula .= " '$mT015[T015_Numero_Cheque]', ";
        $cClausula .= " '{$dataEstorno}', ";
        $cClausula .= " 'ESTORNO DUPLI . No. $mT015[T015_Numero_Duplicata]', ";
        $cClausula .= " '{$valorEstorno}', ";
        $cClausula .= " '1', ";
        $cClausula .= " '{$observacao}' ";
        $cClausula .= " ) ";
        
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p$cClausula";
        }
        
        $cClausula = " update T015 set T015_Data_Pagamento='0000-00-00' WHERE T015_Id='$T015_Id' ";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p$cClausula";
        }

        $this->estornaAcrescimoDesconto($mT015,$dataEstorno,$observacao);

        mysql_query("COMMIT");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro/p$cClausula";
        }
        
        $cClausula = "CALL T015_Gravar_Totalizacao('$T015_Id'); ";
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro/p$cClausula";
        }
        return true;
    }
    
    /**
     * baixar
     *
     * @param String $D007_Id
     * @param String $dataBaixa
     * @param String $extra
     * @return String/bool
     */    
    public function baixar($D007_Id, $dataBaixa, $extra, $T045_Id = 0)
    {
        global $g;

        $extraOriginal = base64_decode($extra);
        $extraWhere    = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T015_Flag_Baixar_Lote='S' AND T015_Flag_Cancelada!='S' AND T015_Data_Pagamento='0000-00-00'"));

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $cClausula  = " SELECT T103_Id";
        $cClausula .= " FROM T015 ";
        $cClausula .= " LEFT JOIN T103 ON T015_Id=T103_T015_Id ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $cClausula .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $cClausula .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $cClausula .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $cClausula .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $cClausula .= " LEFT JOIN D073 ON D073_Id=T015_D073_Id ";
        $cClausula .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        if($g['C031']['contasPagarUnificado'] == 'S'){
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T015_C004_Id ";
        }
        $cClausula .= " WHERE {$extraWhere}";
        $cClausula .= " GROUP BY T015_Id";

        $T103 = mysql_query($cClausula);
        while ($mT103 = mysql_fetch_array($T103)) {
            $this->insereAcrescimoDesconto($mT103['T103_Id']);
        }

        $cClausula  = " SELECT T015_Numero_Duplicata,";
        $cClausula .= " (sum(T103_Valor_Total_Recebido)-(T015_Valor_Duplicata+T015_Valor_Juros-T015_Valor_Desconto)) Diferenca ";
        $cClausula .= " FROM T015 ";
        $cClausula .= " LEFT JOIN T103 ON T015_Id=T103_T015_Id ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $cClausula .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $cClausula .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $cClausula .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $cClausula .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $cClausula .= " LEFT JOIN D073 ON D073_Id=T015_D073_Id ";
        $cClausula .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        if($g['C031']['contasPagarUnificado'] == 'S'){
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T015_C004_Id ";
        }
        $cClausula .= " WHERE {$extraWhere}";
        $cClausula .= " GROUP BY T015_Id";
        log("sql 1: ".$cClausula);
        $T015 = mysql_query($cClausula);

        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro na verificacao de divergencias no recebimento! Contacte o Suporte Técnico.<br />Mysql error: <br />" . htmlspecialchars($erro, ENT_QUOTES) . "</div>";
        }

        $Mensagem = '';
        while ($mT015 = mysql_fetch_array($T015)) {
            if ($mT015['Diferenca'] > 0) {
                $diferenca = gCorrigeNumero($mT015['Diferenca']);
                $Mensagem .= "- $mT015[T015_Numero_Duplicata]: Diferença no valor de {$diferenca}<br />";
            }
        }

        $extraSemPortador = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T015_Flag_Baixar_Lote='S' AND T015_Flag_Cancelada!='S' AND T015_Data_Pagamento='0000-00-00' AND T103_D031_Id is null"));

        $cClausula = " SELECT T015_Numero_Duplicata,";
        $cClausula .= " T103_D031_Id";
        $cClausula .= " FROM T015 ";
        $cClausula .= " LEFT JOIN T103 ON T015_Id=T103_T015_Id ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $cClausula .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $cClausula .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $cClausula .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $cClausula .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $cClausula .= " LEFT JOIN D073 ON D073_Id=T015_D073_Id ";
        $cClausula .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        if($g['C031']['contasPagarUnificado'] == 'S'){
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T015_C004_Id ";
        }
        $cClausula .= " WHERE {$extraSemPortador}";
        log("sql 2: ".$cClausula);
        $T015 = mysql_query($cClausula);

        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro na verificacao de divergencias no recebimento! Contacte o Suporte Técnico.<br />Mysql error: <br />" . htmlspecialchars($erro, ENT_QUOTES) . "</div>";
        }

        while ($mT015 = mysql_fetch_array($T015)) {
            if ($mT015['T103_D031_Id'] <= 0) {
                $Mensagem .= "- Titulo: $mT015[T015_Numero_Duplicata] falta o Portador<br />";
            }
            if ($mT015['T103_D031_Id'] <= 0) {
                $Mensagem .= "- Titulo: $mT015[T015_Numero_Duplicata] falta a conta<br />";
            }
        }

        if (!empty($Mensagem)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'><span class='msgErrorTitle'>Titulos com problemas:</span> $Mensagem </div>";
        }

        $extraComValor = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T015_Flag_Baixar_Lote='S' AND T015_Flag_Cancelada!='S' AND T015_Data_Pagamento='0000-00-00' AND T103_Valor_Recebido>0"));
        $cClausula = " SELECT *";
        $cClausula .= " FROM T015 ";
        $cClausula .= " LEFT JOIN T103 ON T015_Id=T103_T015_Id ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $cClausula .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $cClausula .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $cClausula .= " LEFT JOIN D073 ON D073_Id=T015_D073_Id ";
        $cClausula .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $cClausula .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $cClausula .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        if($g['C031']['contasPagarUnificado'] == 'S'){
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T015_C004_Id ";
        }
        $cClausula .= " WHERE {$extraComValor}";
        $cClausula .= " ORDER BY T015_Data_Vencimento,D024_Nome_Empresa";
        $T015 = mysql_query($cClausula);

        if (mysql_num_rows($T015) <= 0) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Selecione um titulo para baixar</div>";
        }

        if ((int)$T045_Id <= 0) {
            $T045_Id = $this->criarT045BaixaLoteAjax();
            if ($T045_Id <= 0) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro ao criar T045 para baixa.</div>";
            }
        }

        $nT015_Id = 'xxxxxxx';
        while ($mT015 = mysql_fetch_array($T015)) {
            if ($nT015_Id != $mT015['T015_Id']) {
                $nT015_Id = $mT015['T015_Id'];

                $cClausula = " update T015 set ";
                $cClausula .= " T015_D007_Id='$D007_Id', ";
                $cClausula .= " T015_T045_Id='$T045_Id' ";
                $cClausula .= " WHERE T015_Id='$mT015[T015_Id]' ";
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
                }
            }

            $valorPago = $this->verificaValorPago($mT015);
            $valorPago = ($valorPago * -1);

            $mT015['T015_Nome_Cliente'] = mysql_real_escape_string($mT015['T015_Nome_Cliente']);

            $cClausula  = " INSERT INTO T019 ( ";
            $cClausula .= " T019_T015_Id, ";
            $cClausula .= " T019_D007_Id, ";
            $cClausula .= " T019_D031_Id, ";
            $cClausula .= " T019_C004_Id, ";
            $cClausula .= " T019_D024_Id, ";
            $cClausula .= " T019_D014_Id, ";
            $cClausula .= " T019_Complemento, ";
            $cClausula .= " T019_Numero_Agencia, ";
            $cClausula .= " T019_Numero_Conta, ";
            $cClausula .= " T019_Numero_Cheque, ";
            $cClausula .= " T019_Data_Lancamento, ";
            $cClausula .= " T019_Referente, ";
            $cClausula .= " T019_Flag_Deb_Crd, ";
            $cClausula .= " T019_Observacao, ";
            $cClausula .= " T019_Valor_Lancamento ";
            $cClausula .= " ) VALUES ( ";
            $cClausula .= " '$mT015[T015_Id]', ";
            $cClausula .= " '$D007_Id', ";
            $cClausula .= " '$mT015[T103_D031_Id]', ";
            $cClausula .= " '$mT015[T015_C004_Id]', ";
            $cClausula .= " '$mT015[T015_D024_Id]', ";
            $cClausula .= " '$mT015[T015_D014_Id]', ";
            $cClausula .= " '$mT015[T015_Observacao]', ";
            $cClausula .= " '$mT015[T015_Numero_Agencia]', ";
            $cClausula .= " '$mT015[T015_Numero_Conta]', ";
            $cClausula .= " '$mT015[T015_Numero_Cheque]', ";
            $cClausula .= " '$dataBaixa', ";
            $cClausula .= " 'DUPLI . No. $mT015[T015_Numero_Duplicata]', ";
            $cClausula .= " '0', ";
            $cClausula .= " '$mT015[T015_Observacao_1]', ";
            $cClausula .= " '{$valorPago}' ";
            $cClausula .= " ) ";
            mysqli_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
            }

            $this->lancarAcrescimoDesconto($mT015, $D007_Id, $dataBaixa);

            mysql_query("call T015_Baixar_Titulo('$mT015[T015_Id]','$dataBaixa')");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
            }

            mysql_query("DELETE FROM T103 WHERE T103_Id='$mT015[T103_Id]'");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
            }
        }

        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * listaItensParaBaixar
     *
     * @param bool $usarGroupBy
     * @return String
     */ 
    public function listaItensParaBaixar($usarGroupBy = false)
    {
        global $g;
        
        $sql = " SELECT ";
        $sql .= " T015_D031_Id, ";
        $sql .= " T015_Valor_Saldo, ";
        $sql .= " T015_Id ";
        $sql .= " FROM T015 ";
        $sql .= " LEFT JOIN T103 ON T103_T015_Id=T015_Id ";
        $sql .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $sql .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $sql .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $sql .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $sql .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $sql .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE T015_Flag_Baixar_Lote='S' ";
        $sql .= "   AND T015_Flag_Cancelada!='S' ";
        $sql .= "   AND T015_Data_Pagamento = '0000-00-00' ";
        //$sql .= "   AND T103_Id is null ";
        //$sql .= "   AND T015_C004_Id='{$g['empresaAtual']}' ";

        if ($usarGroupBy) {
            $sql .= " GROUP BY T015_D031_Id";
        }

        log($sql);

        $sqlReturn = mysql_query($sql);
        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado;
    }
    
    /**
     * carregaTotaisBaixar
     *
     * @param String $extra
     * @return String
     */ 
    public function carregaTotaisBaixar($extra)
    {
        global $g;
        $sql = " SELECT sum(T103_Valor_Recebido) TotalRecebido, ";
        $sql .= " sum(T015_Valor_Saldo) Total ";
        $sql .= " FROM T015 ";
        $sql .= " LEFT JOIN T103 ON T015_Id=T103_T015_Id ";
        //$sql .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        //$sql .= " WHERE T015_Flag_Baixar_Lote='S' ";
        //$sql .= " AND T015_Data_Pagamento='0000-00-00' ";        
        //$sql .= " AND T015_C004_Id='{$g['empresaAtual']}' ";
        
        $extraNovo = <<<EOT
			(T015_Flag_Baixar_Lote='S' AND T015_Flag_Cancelada!='S' AND T015_Data_Pagamento='0000-00-00' AND T015_C004_Id='{$g['empresaAtual']}')
EOT;
        $sql .= gInsertExtraWhere($extra, $extraNovo);
        
        $sqlReturn = mysql_query($sql);
        $row       = mysql_fetch_assoc($sqlReturn);
        return $row;
    }

    public function montarWhereBaixaLoteAjax($extraOriginal)
    {
        global $g;

        $whereExtra = "T015_Flag_Baixar_Lote='S' AND T015_Flag_Cancelada!='S' AND T015_Data_Pagamento='0000-00-00' AND IFNULL(T103_Valor_Recebido,0)>0";
        if ($g['C031']['contasPagarUnificado'] != 'S') {
            $whereExtra .= " AND T015_C004_Id='{$g['empresaAtual']}'";
        }

        return gPegarParteWhere(gInsertExtraWhere($extraOriginal, $whereExtra));
    }

    public function buscarIdsTitulosBaixaLote($where)
    {
        global $g;

        $sql  = " SELECT DISTINCT T015_Id";
        $sql .= " FROM T015 ";
        $sql .= " LEFT JOIN T103 ON T015_Id=T103_T015_Id ";
        $sql .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $sql .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $sql .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $sql .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $sql .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $sql .= " LEFT JOIN D073 ON D073_Id=T015_D073_Id ";
        $sql .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        if ($g['C031']['contasPagarUnificado'] == 'S') {
            $sql .= " LEFT JOIN C004 ON C004_Id=T015_C004_Id ";
        }
        $sql .= " WHERE {$where}";
        $sql .= " ORDER BY T015_Id";

        $res = mysql_query($sql);
        $ids = array();
        while ($row = mysql_fetch_array($res)) {
            $ids[] = (int) $row['T015_Id'];
        }

        return $ids;
    }

    public function prepararFlagsBaixaLoteAjax($snapshotIds, $idsAtivos)
    {
        $snapshotIds = array_values(array_filter(array_map('intval', (array) $snapshotIds)));
        $idsAtivos   = array_values(array_filter(array_map('intval', (array) $idsAtivos)));

        if (count($snapshotIds) <= 0) {
            return true;
        }

        mysql_query("UPDATE T015 SET T015_Flag_Baixar_Lote='N' WHERE T015_Id IN (" . implode(',', $snapshotIds) . ")");
        if (count($idsAtivos) > 0) {
            mysql_query("UPDATE T015 SET T015_Flag_Baixar_Lote='S' WHERE T015_Id IN (" . implode(',', $idsAtivos) . ")");
        }

        return true;
    }

    public function restaurarFlagsBaixaLoteAjax($snapshotIds)
    {
        $snapshotIds = array_values(array_filter(array_map('intval', (array) $snapshotIds)));
        if (count($snapshotIds) <= 0) {
            return true;
        }

        $sql  = "UPDATE T015 ";
        $sql .= "SET T015_Flag_Baixar_Lote='S' ";
        $sql .= "WHERE T015_Id IN (" . implode(',', $snapshotIds) . ") ";
        $sql .= "AND T015_Data_Pagamento='0000-00-00' ";
        $sql .= "AND T015_Flag_Cancelada!='S'";
        mysql_query($sql);

        return true;
    }

    public function prepararBaixaLoteAjaxDireto($D007_Id, $dataBaixa, $extra)
    {
        $D007_Id = (int) $D007_Id;

        if ($D007_Id <= 0) {
            return array('ok' => false, 'erro' => 'Banco não informado.');
        }
        if (empty($dataBaixa) || $dataBaixa == '0000-00-00') {
            return array('ok' => false, 'erro' => 'Data de baixa inválida.');
        }
        if (empty($extra)) {
            return array('ok' => false, 'erro' => 'Filtro da seleção não informado.');
        }

        $extraOriginal = base64_decode($extra);
        $where         = $this->montarWhereBaixaLoteAjax($extraOriginal);
        $ids           = $this->buscarIdsTitulosBaixaLote($where);
        $total         = count($ids);

        if ($total <= 0) {
            return array('ok' => false, 'erro' => 'Selecione um titulo para baixar');
        }

        $T045_Id = $this->criarT045BaixaLoteAjax();
        if ($T045_Id <= 0) {
            return array('ok' => false, 'erro' => 'Não foi possível iniciar o lote de baixa (T045).');
        }

        return array(
            'ok' => true,
            'data' => array(
                'ids'         => $ids,
                'total'       => $total,
                'processado'  => 0,
                'restante'    => $total,
                'done'        => false,
                'T045_Id'     => $T045_Id,
                'erros'       => array(),
                'errosCount'  => 0,
            ),
        );
    }

    public function processarBaixaLoteAjaxDireto($snapshotIds, $idsLote, $D007_Id, $dataBaixa, $extra, $T045_Id, $processadoAtual = 0)
    {
        $snapshotIds     = array_values(array_filter(array_map('intval', (array) $snapshotIds)));
        $idsLote         = array_values(array_filter(array_map('intval', (array) $idsLote)));
        $D007_Id         = (int) $D007_Id;
        $T045_Id         = (int) $T045_Id;
        $processadoAtual = (int) $processadoAtual;

        if (count($snapshotIds) <= 0) {
            return array('ok' => false, 'erro' => 'Nenhum titulo informado para o processamento.');
        }
        if (count($idsLote) <= 0) {
            return array(
                'ok' => true,
                'data' => $this->montarResumoBaixaLoteAjaxDireto($snapshotIds, $processadoAtual, array()),
            );
        }

        $this->prepararFlagsBaixaLoteAjax($snapshotIds, $idsLote);
        $retorno = $this->baixar($D007_Id, $dataBaixa, $extra, $T045_Id);
        if ($retorno === true) {
            $processadoNovo = $processadoAtual + count($idsLote);
            return array(
                'ok' => true,
                'data' => $this->montarResumoBaixaLoteAjaxDireto($snapshotIds, $processadoNovo, array()),
            );
        }

        $this->restaurarFlagsBaixaLoteAjax($snapshotIds);
        return array(
            'ok' => false,
            'erro' => $retorno,
            'data' => $this->montarResumoBaixaLoteAjaxDireto($snapshotIds, $processadoAtual, array(
                array('ids' => $idsLote, 'erro' => strip_tags($retorno))
            )),
        );
    }

    public function montarResumoBaixaLoteAjaxDireto($snapshotIds, $processado, $erros = array())
    {
        $total      = count((array) $snapshotIds);
        $processado = (int) $processado;
        $restante   = $total - $processado;
        if ($restante < 0) {
            $restante = 0;
        }

        return array(
            'total'      => $total,
            'processado' => $processado,
            'restante'   => $restante,
            'done'       => ($processado >= $total),
            'erros'      => $erros,
            'errosCount' => count($erros),
        );
    }

    public function criarT045BaixaLoteAjax()
    {
        global $g;
        $sql  = " INSERT INTO T045 (T045_C004_Id, T045_C007_Id, T045_Data) VALUES (";
        $sql .= " '{$g['empresaAtual']}', '{$g['usuarioAtual']}', now() )";
        mysql_query($sql);
        if (mysql_error()) {
            return 0;
        }
        return (int) $g['mysqlLastId'];
    }

    /**
     * updateT015_Flag_Reparcelar
     *
     * @param String $T015_Id
     * @param String $selected
     * @return true
     */
    public function updateT015_Flag_Reparcelar($T015_Id, $selected)
    {
        global $g;
        
        $T015_Id  = mysql_real_escape_string($T015_Id);
        $selected = mysql_real_escape_string($selected);
        mysqli_query("update T015 set T015_Flag_Reparcelar='$selected' WHERE T015_Id='$T015_Id'");
        return true;
    }
    
    /**
     * listaTotalReparcelar
     *
     * @param String $extra
     * @return true
     */
    public function listaTotalReparcelar($extra)
    {
        global $g;
        
        $sql = " SELECT ";
        $sql .= " sum(if(T015_Data_Pagamento='0000-00-00',T015_Valor_Saldo,0)) Total_Pagar, ";
        $sql .= " sum(if(T015_Data_Pagamento!='0000-00-00',T015_Valor_Saldo,0)) Total_Recebido, ";
        $sql .= " sum(if(T015_Data_Pagamento='0000-00-00' AND T015_Flag_Reparcelar='S',T015_Valor_Saldo,0)) Total_Reparcelar ";
        $sql .= " FROM T015 ";
        /*$sql .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $sql .= " LEFT JOIN D027 ON D027_Id=T015_D027_Id ";
        $sql .= " LEFT JOIN T117 ON T117_Id=T015_T117_Id ";
        $sql .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $sql .= " LEFT JOIN D046 ON D046_Id=D024_D046_Id ";
        $sql .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $sql .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE T015_C004_Id='{$g['empresaAtual']}'";
        $sql .= "   AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)";*/
        
        $extraNovo = <<<EOT
            (T015_C004_Id='{$g['empresaAtual']}' AND T015_Flag_Cancelada!='S' AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null))
EOT;
        $sql .= gInsertExtraWhere($extra, $extraNovo);
        
        $sqlReturn = mysql_query($sql);
        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado[0];
    }
    
    /**
     * listaTotalAgrupar
     *
     * @param String $extra
     * @return String
     */
    public function listaTotalAgrupar($extra)
    {
        global $g;
        
        $sql = " SELECT ";
        $sql .= " sum(if(T015_Data_Pagamento='0000-00-00',T015_Valor_Saldo,0)) Total_Pagar, ";
        $sql .= " sum(if(T015_Data_Pagamento!='0000-00-00',T015_Valor_Saldo,0)) Total_Recebido, ";
        $sql .= " sum(if(T015_Data_Pagamento='0000-00-00' AND T015_Flag_Reparcelar='S',T015_Valor_Saldo,0)) Total_Agrupar ";
        $sql .= " FROM T015 ";
        /*$sql .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $sql .= " LEFT JOIN D027 ON D027_Id=T015_D027_Id ";
        $sql .= " LEFT JOIN T117 ON T117_Id=T015_T117_Id ";
        $sql .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $sql .= " LEFT JOIN D046 ON D046_Id=D024_D046_Id ";
        $sql .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $sql .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE T015_C004_Id='{$g['empresaAtual']}'";
        $sql .= "   AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)";*/
        
        $extraNovo = <<<EOT
            (T015_C004_Id='{$g['empresaAtual']}' AND T015_Flag_Cancelada!='S' AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null))
EOT;
        $sql .= gInsertExtraWhere($extra, $extraNovo);
        
        $sqlReturn = mysql_query($sql);
        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado[0];
    }
    
    /**
     * listaItensParaReparcelar
     *
     * @param bool $usarGroupBy
     * @param String $extra
     * @return String
     */
    public function listaItensParaReparcelar($usarGroupBy = false, $extra)
    {
        global $g;
        $extra = base64_decode($extra);
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T015_Data_Pagamento='0000-00-00' AND T015_Flag_Cancelada!='S' AND T015_Flag_Reparcelar='S' AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_C004_Id='{$g['empresaAtual']}'"));
        $sql   = " SELECT ";
        $sql .= " T015_Id,T015_D024_Id,D024_Nome_Empresa ";
        $sql .= " FROM T015 ";
        $sql .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $sql .= " LEFT JOIN D073 ON D073_Id=T015_D073_Id ";
        $sql .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $sql .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $sql .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $sql .= " LEFT JOIN D154 ON D154_Id=D014_D154_Id ";
        $sql .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $sql .= " LEFT JOIN C004 ON C004_Id=T015_C004_Id ";
        $sql .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE {$extra}";
        //$sql .= " WHERE T015_C004_Id='{$g['empresaAtual']}'";
        //$sql .= "   AND T015_Data_Pagamento='0000-00-00'";
        //$sql .= "   AND T015_Flag_Reparcelar='S'";
        //$sql .= "   AND ifnull(T015_T035_Id,0)<=0 ";
        //$sql .= "   AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)";                
        if ($usarGroupBy) {
            $sql .= " GROUP BY T015_D024_Id";
        }
        $sqlReturn = mysql_query($sql);
        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado;
    }
    
    /**
     * listaItensParaAgrupar
     *
     * @param bool $usarGroupBy
     * @param String $extra
     * @return String
     */    
    public function listaItensParaAgrupar($usarGroupBy = false, $extra)
    {
        global $g;
        $extra = base64_decode($extra);
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T015_Data_Pagamento='0000-00-00' AND T015_Flag_Cancelada!='S' AND T015_Flag_Reparcelar='S' AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_C004_Id='{$g['empresaAtual']}'"));
        $sql   = " SELECT ";
        $sql .= " * ";
        $sql .= " FROM T015 ";
        $sql .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $sql .= " LEFT JOIN D073 ON D073_Id=T015_D073_Id ";
        $sql .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $sql .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $sql .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $sql .= " LEFT JOIN D154 ON D154_Id=D014_D154_Id ";
        $sql .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $sql .= " LEFT JOIN C004 ON C004_Id=T015_C004_Id ";
        $sql .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE {$extra}";
        //$sql .= " WHERE T015_C004_Id='{$g['empresaAtual']}'";
        //$sql .= "   AND T015_Data_Pagamento='0000-00-00'";
        //$sql .= "   AND T015_Flag_Reparcelar='S'";
        //$sql .= "   AND ifnull(T015_T035_Id,0)<=0";
        //$sql .= "   AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)";        
        if ($usarGroupBy) {
            $sql .= " GROUP BY T015_D024_Id";
        }
        
        $sqlReturn = mysql_query($sql);
        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado;
    }
    
    /**
     * agrupar
     *
     * @param String $T015_D027_Id
     * @param String $agruparNovoVencimento
     * @param String $extra
     * @return String/bool
     */     
    public function agrupar($T015_D027_Id, $agruparNovoVencimento, $extra)
    {
        global $g;
        $extraOriginal = base64_decode($extra);
        $extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Cancelada!='S' AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00' AND T015_C004_Id='{$g['empresaAtual']}'"));
        
        $agruparNovoVencimento = gCorrigeDataInverte($agruparNovoVencimento);
        $cClausula_T015  = " SELECT ";
        $cClausula_T015 .= " * ";
        $cClausula_T015 .= " FROM T015 ";
        $cClausula_T015 .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $cClausula_T015 .= " LEFT JOIN D073 ON D073_Id=T015_D073_Id ";
        $cClausula_T015 .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $cClausula_T015 .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $cClausula_T015 .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $cClausula_T015 .= " LEFT JOIN D154 ON D154_Id=D014_D154_Id ";
        $cClausula_T015 .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $cClausula_T015 .= " LEFT JOIN C004 ON C004_Id=T015_C004_Id ";
        $cClausula_T015 .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        //$cClausula_T015 .= " WHERE T015_C004_Id='{$g['empresaAtual']}'"; 
        //$cClausula_T015 .= "   AND T015_Data_Pagamento='0000-00-00'";
        //$cClausula_T015 .= "   AND T015_Flag_Reparcelar='S'";
        //$cClausula_T015 .= "   AND T015_Flag_Cancelada<>'S'";        
        //$cClausula_T015 .= "   AND ifnull(T015_T035_Id,0)<=0";
        //$cClausula_T015 .= "   AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)";
        $cClausula_T015 .= " WHERE {$extra}";
        $cClausula_T015 .= " ORDER BY T015_Data_Vencimento ";
        log($cClausula_T015);
        
        $sql = mysql_query($cClausula_T015);
        
        $textGeradores = "";
        while ($row = mysql_fetch_assoc($sql)) {
            $textGeradores .= $row['T015_Id'] . ';' . $row['T015_Numero_Duplicata'] . ';' . $row['T015_Data_Emissao'] . ';' . $row['T015_Data_Vencimento'] . ';' . $row['T015_Valor_Duplicata'] . ';' . PHP_EOL;
        }
        
        $cSql_T015 = mysql_query($cClausula_T015);
        
        while ($mT015 = mysql_fetch_array($cSql_T015)) {
            if (!empty($mT015['T015_Numero_Duplicata'])) {
                $mNumeroDuplicatas[] = $mT015['T015_Numero_Duplicata'];
            }
            if (!empty($mT015['T015_Numero_Documento'])) {
                $mNumeroDocumento[] = $mT015['T015_Numero_Documento'];
            }
            $nValor_Titulo += $mT015['T015_Valor_Total'];
            $nValor_Titulo_Saldo += $mT015['T015_Valor_Saldo'];
            $Data_Emissao = $mT015['T015_Data_Emissao'];
            $nD024_Id     = $mT015['T015_D024_Id'];
            $nC004_Id     = $mT015['T015_C004_Id'];
            $nD014_Id     = $mT015['T015_D014_Id'];
            $nD032_Id     = $mT015['T015_D032_Id'];
            $nD073_Id     = $mT015['T015_D073_Id'];
            $nNomeEmpresa = $mT015['D024_Nome_Empresa'];
        }
        
        $Numero_Duplicata = implode(",", $mNumeroDuplicatas);
        $Numero_Documento = implode(",", $mNumeroDocumento);
        
        mysql_query("START TRANSACTION");
        
        $cClausula = " INSERT INTO T015 (";
        $cClausula .= " T015_D024_Id, ";
        $cClausula .= " T015_C004_Id, ";
        $cClausula .= " T015_D031_Id, ";
        $cClausula .= " T015_D014_Id, ";
        $cClausula .= " T015_D032_Id, ";
        $cClausula .= " T015_D073_Id, ";
        $cClausula .= " T015_Nome_Cliente, ";
        $cClausula .= " T015_Numero_Duplicata, ";
        $cClausula .= " T015_Numero_Documento, ";
        $cClausula .= " T015_Data_Emissao, ";
        $cClausula .= " T015_Data_Vencimento, ";
        $cClausula .= " T015_Data_Pagamento, ";
        $cClausula .= " T015_Valor_Duplicata, "; // 1
        $cClausula .= " T015_Valor_Saldo, "; // 2
        $cClausula .= " T015_Valor_Juros, ";
        $cClausula .= " T015_Valor_Desconto, ";
        $cClausula .= " T015_Valor_Total, "; // 3
        $cClausula .= " T015_Data_Lancamento, ";
        $cClausula .= " T015_Hora_Lancamento, ";
        $cClausula .= " T015_Numero_Parcela, ";
        $cClausula .= " T015_Observacao_2 ";
        $cClausula .= " ) VALUES ( ";
        $cClausula .= " '$nD024_Id', ";
        $cClausula .= " '$nC004_Id', ";
        $cClausula .= " '$T015_D027_Id', ";
        $cClausula .= " '$nD014_Id', ";
        $cClausula .= " '$nD032_Id', ";
        $cClausula .= " '$nD073_Id', ";
        $cClausula .= " '$nNomeEmpresa', ";
        $cClausula .= " 'AG $Numero_Duplicata', ";
        $cClausula .= " '$Numero_Documento', ";
        $cClausula .= " '$Data_Emissao', ";
        $cClausula .= " '$agruparNovoVencimento', ";
        $cClausula .= " '0000-00-00', ";
        $cClausula .= " '$nValor_Titulo_Saldo', "; // 1
        $cClausula .= " '$nValor_Titulo_Saldo', "; // 2
        $cClausula .= " '0', ";
        $cClausula .= " '0', ";
        $cClausula .= " '$nValor_Titulo', "; // 3
        $cClausula .= " current_date(), ";
        $cClausula .= " current_time(), ";
        $cClausula .= " '1', ";
        $cClausula .= " '*** AGRUPAMENTO ***' ";
        $cClausula .= " ) ";
        
        mysql_query($cClausula);
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p/pClausula: $cClausula";
        }
        
        $nT015_Id = $g['mysqlLastId'];        
        
        $cSql_T015 = mysql_query($cClausula_T015);
        while ($mT015 = mysql_fetch_array($cSql_T015)) {
            $cClausula = " update T015 set  ";
            $cClausula .= " T015_Flag_Cancelada='S', ";
            $cClausula .= " T015_T015_Id_Agrupado='$nT015_Id', ";
            $cClausula .= " T015_Data_Pagamento=current_date(), ";
            $cClausula .= " T015_Observacao_2='*** AGRUPADO $Numero_Duplicata ***' ";
            $cClausula .= " WHERE T015_Id='$mT015[T015_Id]' ";
            mysql_query("$cClausula");
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/pClausula: $cClausula";
            }
        }
        
        $textGerados = "";
        $sql         = mysql_query("SELECT * FROM T015 WHERE T015_Id='{$nT015_Id}'");
        while ($row = mysql_fetch_assoc($sql)) {
            $textGerados .= $row['T015_Id'] . ';' . $row['T015_Numero_Duplicata'] . ';' . $row['T015_Data_Emissao'] . ';' . $row['T015_Data_Vencimento'] . ';' . $row['T015_Valor_Duplicata'] . ';' . PHP_EOL;
        }
        
        // insere registros na tabela de histórico de reparcelamento            
        mysql_query("INSERT INTO T196 
                                 (T196_D024_Id,
                                 T196_C004_Id,
                                 T196_Data_Emissao,
                                 T196_Valor_Total,
                                 T196_Titulos_Geradores,                                 
                                 T196_Titulos_Gerados,
                                 T196_C007_Id,
                                 T196_Tipo)
                           VALUES                 
                               ('$nD024_Id',
                                '$nC004_Id',
                                curdate(),
                                '{$nValor_Titulo}',
                                '{$textGeradores}',                                
                                '{$textGerados}',
                                '{$g['usuarioAtual']}',                                
                                'A')");
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro 2: $cErro/p/pClausula: $cClausula";
        }
        
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * montarEmailReparcelamentoPagar
     *
     * @param String $T196_Id
     * @return array
     */     
    public function montarEmailReparcelamentoPagar($T196_Id)
    {
        global $g, $confUsuario;
        $T196_Id = mysql_real_escape_string($T196_Id);
        $sql     = "SELECT * FROM T196 LEFT JOIN D024 ON D024_Id=T196_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id LEFT JOIN D018 ON D018_Id=D024_D018_Id WHERE T196_Id='{$T196_Id}'";
        $T196    = mysql_query($sql);
        $T196    = mysql_fetch_array($T196);
        
        $array = array();
        
        $array['Para'] = $T196['D024_E_Mail'];
        if ($T196['T196_Tipo'] == "A") {
            $array['Assunto'] = "Agrupamento Nº[{$T196_Id}]";
        } elseif ($T196['T196_Tipo'] == "R") {
            $array['Assunto'] = "Reparcelamento Nº[{$T196_Id}]";
        }
        
        $tabela = 'font-size: 12px;';
        $tabela .= 'margin: 10px 25px;';
        $tabela .= 'width: 500px;';
        $tabela .= 'text-align: left;';
        $tabela .= 'border:0px;';
        
        $th = 'font-size: 13px;';
        $th . '=font-weight: normal;';
        $th .= 'color: black;';
        $th .= 'background: #E1ECF1;';
        $th .= 'padding: 4px 4px;';
        $th .= 'border-bottom: 0px solid #6678b1;';
        
        $td = 'border-bottom: 0px solid #ccc;';
        $td .= 'color: #000000;';
        $td .= 'background: #fff;';
        $td .= 'padding: 4px 2px 4px 2px;';
        
        //CARREGANDO A LOGO DA EMPRESA ATUAL
        $corpo = '<div style="border:2px solid #E0EEEE; padding-top:10px;padding-bottom:10px;">';
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        $corpo .= '<img src="' . $caminhoImagem . '" style="margin-left:25px"><br /><br />';
        // <br />Seguem abaixo preços e condições<br />
        if ($T196['T196_Tipo'] == "A") {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">AGRUPAMENTO Nº: <span style="font-weight:normal;">' . $T196_Id . '</span></div>';
        } elseif ($T196['T196_Tipo'] == "R") {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">REPARCELAMENTO Nº: <span style="font-weight:normal;">' . $T196_Id . '</span></div>';
        }
        
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Cliente/Fornecedor: <span style="font-weight:normal;">' . $T196['D024_Nome_Empresa'] . '</span></div>';
        
        if (!empty($T196['D024_Cnpj'])) {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">CNPJ: <span style="font-weight:normal;">' . $T196['D024_Cnpj'] . '</span></div>';
        } elseif (!empty($T196['D024_Cpf'])) {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">CPF: <span style="font-weight:normal;">' . $T196['D024_Cpf'] . '</span></div>';
        }
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Cidade: <span style="font-weight:normal;">' . $T196['D020_Nome_Cidade'] . ' - ' . $T196['D018_UF'] . '</span></div>';
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Telefone: <span style="font-weight:normal;">(' . $T196['D024_DDD_Telefone_1'] . ') ' . $T196['D024_Telefone_1'] . '</span></div>';
        if ($T196['T196_Tipo'] == "A") {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Tipo: <span style="font-weight:normal;">Agrupamento</span></div>';
        } elseif ($T196['T196_Tipo'] == "R") {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Tipo: <span style="font-weight:normal;">Reparcelamento</span></div>';
        }
        
        // tabela de geradores
        $corpo .= '<div style="margin-left:25px;margin-top:25px;font-size:13px;font-family:Sans-Serif;font-weight:bold">Títulos Geradores</div>';
        $corpo .= '<table style="' . $tabela . '">';
        $corpo .= '<tbody><tr><th style="' . $th . '">Duplicata</th><th style="' . $th . '" align="center" width=80>Emissão</th></th><th style="' . $th . '" align="center" width=80>Vencimento</th><th style="' . $th . '" align="center" width=70px>Valor</th></tr>';
        
        $T196A   = mysql_query("SELECT T196_Titulos_Geradores FROM T196 WHERE T196_Id='{$T196_Id}'");
        $aT196   = mysql_fetch_array($T196A);
        $res     = explode("\n", $aT196['T196_Titulos_Geradores']);
        $indices = "";
        foreach ($res as $elemento) {
            if ($elemento != "") {
                $part = explode(";", $elemento);
                $indices .= $part[0] . ",";
            }
        }
        while (substr($indices, -1, 1) == ",") {
            $indices = substr_replace($indices, '', -1);
        }
        
        $T015A = mysql_query("SELECT T015_Id,T015_Numero_Duplicata,T015_Data_Vencimento,T015_Data_Emissao,T015_Valor_Duplicata FROM T015 WHERE T015_Id IN ({$indices})");
        while ($linha = mysql_fetch_assoc($T015A)) {
            
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . '">';
            $corpo .= $linha['T015_Numero_Duplicata'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T015_Data_Emissao']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T015_Data_Vencimento']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($linha['T015_Valor_Duplicata']);
            $corpo .= '</td>';
            $corpo .= '</tr>';
            
        }
        
        $corpo .= '</tbody>';
        $corpo .= '</table>';
        
        // tabela de gerados
        $corpo .= '<div style="margin-left:25px;margin-top:25px;font-size:13px;font-family:Sans-Serif;font-weight:bold">Títulos Gerados</div>';
        $corpo .= '<table style="' . $tabela . '">';
        $corpo .= '<tbody><tr><th style="' . $th . '">Duplicata</th><th style="' . $th . '" align="center" width=80>Emissão</th></th><th style="' . $th . '" align="center" width=80>Vencimento</th><th style="' . $th . '" align="center" width=70px>Valor</th></tr>';
        
        $T196A   = mysql_query("SELECT T196_Titulos_Gerados FROM T196 WHERE T196_Id='{$T196_Id}'");
        $aT196   = mysql_fetch_array($T196A);
        $res     = explode("\n", $aT196['T196_Titulos_Gerados']);
        $indices = "";
        foreach ($res as $elemento) {
            if ($elemento != "") {
                $part = explode(";", $elemento);
                $indices .= $part[0] . ",";
            }
        }
        while (substr($indices, -1, 1) == ",") {
            $indices = substr_replace($indices, '', -1);
        }
        
        $T015A = mysql_query("SELECT T015_Id,T015_Numero_Duplicata,T015_Data_Vencimento,T015_Data_Emissao,T015_Valor_Duplicata FROM T015 WHERE T015_Id IN ({$indices})");
        while ($linha = mysql_fetch_assoc($T015A)) {
            
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . '">';
            $corpo .= $linha['T015_Numero_Duplicata'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T015_Data_Emissao']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T015_Data_Vencimento']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($linha['T015_Valor_Duplicata']);
            $corpo .= '</td>';
            $corpo .= '</tr>';
            
        }
        
        $corpo .= '</tbody>';
        $corpo .= '</table>';
        
        // valor total
        $corpo .= '<div style="margin-left:25px;margin-top:25px;font-size:13px;font-family:Sans-Serif;font-weight:bold">Valor Total:<span style="color:black;margin-left:10px">' . gCorrigeNumero($T196['T196_Valor_Total']) . '</span></div>';
        $corpo .= '</div>';
        
        
        //$corpo.='<div style="margin-left:25px;font-family:Sans-Serif;"><br /><br /><br /><b>VALOR TOTAL DO ORÇAMENTO: </b>&nbsp;'.gCorrigeNumero($valorTotalOrcamento).'</div>';
        // $corpo.='<br />';
        // $corpo.='<div style="margin-left:25px"><table border=0 style="font-family:Sans-Serif;">';
        // $corpo.='<tr><td>Valor total Produtos: </td><td style="font-weight:bold">R$ '.gCorrigeNumero($T003['T003_Valor_Total_Produtos']).'</td></tr>';
        // $corpo.='<tr><td>Valor total IPI: </td><td style="font-weight:bold">R$ '.gCorrigeNumero($T003['T003_Valor_Total_IPI']).'</td></tr>';
        // $corpo.='<tr><td>Valor total Orçamento: </td><td style="font-weight:bold;font-size:14px;">R$ '.gCorrigeNumero($T003['T003_Valor_Total']).'</td></tr>';
        // $corpo.='<tr><td>Valor total ICM: </td><td style="font-weight:bold">R$ '.gCorrigeNumero($T003['T003_Valor_Total_ICMS']).'</td><td style="font-size:11px"><i>Já incluso no preço</i></td></tr>';
        // $corpo.='<tr><td colspan=3>&nbsp;</td></tr>';
        // if (!empty($T003["T003_Prazo_Entrega"]))
        // $corpo.='<tr><td>Prazo de Entrega </td><td style="font-weight:bold" colspan=2>'.$T003['T003_Prazo_Entrega'].'</td></tr>';
        // if (!empty($T003["T003_Frete"]))
        // $corpo.='<tr><td>Frete </td><td style="font-weight:bold" colspan=2>'.$T003['T003_Frete'].'</td></tr>';
        // if (!empty($T003["T003_Forma_Pagamento"]))
        // $corpo.='<tr><td>Forma de Pagamento </td><td style="font-weight:bold" colspan=2>'.$T003['T003_Forma_Pagamento'].'</td></tr>';
        // if (!empty($T003["T003_Observacao"]))
        // $corpo.='<tr><td>Observação </td><td style="font-weight:bold" colspan=2>'.$T003['T003_Observacao'].'</td></tr>';
        // $corpo.='</table></div>';
        
        $array['Corpo']   = $corpo;
        $array['D024_Id'] = $T196['T196_D024_Id'];
        //$array['T139_Valor_Total'] = $T003['T003_Valor_Total'];
        // $sql = "SELECT COUNT(*) FROM T004 WHERE T004_T003_Id='{$T003_Id}'";
        // $registros = mysql_query($sql);
        // $registros = mysql_fetch_array($registros);
        // $array['T139_Numero_Registros'] = $registros[0];
        
        return $array;
    }
    
    /**
     * fin002calcularParcelaCombo
     *
     * @param String $parcela
     * @param String $valorTotal
     * @return String
     */     
    public function fin002calcularParcelaCombo($parcela, $valorTotal)
    {
        return gCorrigeNumero($valorTotal / $parcela);
    }
    
    /**
     * fin002calcularParcelaSimulacao
     *
     * @param Form $form
     * @return String
     */     
    public function fin002calcularParcelaSimulacao($form)
    {
        global $g;
        $D024_Id     = $form['D024_Id'];
        $numParcelas = $form['Total_Parcelas'];
        $valorTotal  = $form['valorSomado'];
        
        if ($numParcelas == "0"){
            return '';
        }
        return gCorrigeNumero($valorTotal / $numParcelas);
    }
    
    /**
     * fin002AtualizarParcelas2
     *
     * @param String $T197_Id
     * @param String $campoEditado
     * @param String $valorAntigo
     * @param String $novoValor
     * @param String $valorTotal
     * @return String/bool
     */     
    public function fin002AtualizarParcelas2($T197_Id, $campoEditado, $valorAntigo, $novoValor, $valorTotal)
    {
        global $g;

        $query            = "SELECT * FROM T197 LEFT JOIN D024 ON T197_D024_Id='{$T197_Id}' WHERE T197_Id='{$T197_Id}'";
        $retorno          = mysql_query($query);
        $linha            = mysql_fetch_assoc($retorno);
        //$dataVelhaD024 = $linha['T005_Data_Emissao'];
        $dataVelhaD024    = '';
        $prazosDiasAntigo = $linha['T197_Prazos_Dias'];
        
        if ($campoEditado == 'T197_Prazos_Dias') {
            // if($valorAntigo == $novoValor){
            // return "O prazo é o mesmo";
            // }
            
            $date = new \DateTime($dataVelhaD024);
            $date->modify('+' . $novoValor . ' days');
            $query = "UPDATE T197 SET T197_Data_Vencimento='{$date->format('Y-m-d')}' WHERE T197_Id='{$T197_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T197_Data_Vencimento') {
            
            $dataAntiga = gCorrigeDataInverte($valorAntigo);
            $dataNova   = gCorrigeDataInverte($novoValor);
            
            $timeStampDataNova   = strtotime($dataNova);
            $timeStampDataAntiga = strtotime($dataAntiga);
            
            if ($timeStampDataAntiga == $timeStampDataNova) {
                return 'Datas Iguais';
            } else if ($timeStampDataAntiga < $timeStampDataNova) {
                $novaData = ($timeStampDataNova - $timeStampDataAntiga);
                $dias     = date('d', $novaData);
                
                $total = ($prazosDiasAntigo + $dias);
                $query = "UPDATE T197 SET T197_Prazos_Dias='{$total}' WHERE T197_Id='{$T197_Id}'";
                mysql_query($query);
                return true;
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
                $query    = "UPDATE T197 SET T197_Prazos_Dias='{$total}' WHERE T197_Id='{$T197_Id}'";
                mysql_query($query);
                return true;
            }
            
        } else if ($campoEditado == 'T197_Valor_Parcela') {
            
            $query      = "SELECT * FROM T197 WHERE T197_Id='{$T197_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $D024_Id    = $linha['T197_D024_Id'];
            $numParcela = $linha['T197_Numero_Parcela'];
            //$numeroProximaParcela = $numParcela + 1;
            
            // $query = "SELECT D024_Valor_Total FROM D024 WHERE AND D024_Id='{$D024_Id}'";
            // $retorno=mysql_query($query);        
            // $linha = mysql_fetch_assoc($retorno);    
            $valorTotal = $valorTotal;
            
            $query                  = "SELECT SUM(T197_Valor_Parcela) as Parcelas FROM T197 WHERE T197_D024_Id='{$D024_Id}' AND T197_C007_Id='{$g['usuarioAtual']}' AND T197_Numero_Parcela<='{$numParcela}'";
            $retorno                = mysql_query($query);
            $linha                  = mysql_fetch_assoc($retorno);
            $valorParcelaEditada    = $linha['Parcelas'];
            $valorRestante          = $valorTotal - $valorParcelaEditada;

            
            $query             = "SELECT * FROM T197 WHERE T197_D024_Id='{$D024_Id}' AND T197_C007_Id='{$g['usuarioAtual']}' AND T197_Numero_Parcela>'{$numParcela}'";
            //$query2             = "SELECT * FROM T197 WHERE T197_D024_Id='{$D024_Id}' AND T197_Numero_Parcela>'{$numeroProximaParcela}'";
            $retorno           = mysql_query($query);
            //$retorno2          = mysql_query($query2);
            $parcelasRestantes = mysql_num_rows($retorno);

            //se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
            if ($parcelasRestantes != 0) {
                //calcula o valor da soma das próximas parcelas depois da que foi editada, pra ver se o valor bate
                $valorParcelasRestantes = $valorRestante / $parcelasRestantes;
                $valorParcelasRestantesFormatado = round($valorParcelasRestantes,2);
                $valorTotalParcelasRestantes = $valorParcelasRestantesFormatado * $parcelasRestantes;
                $valorDiferençaProximaParcela = round($valorTotal - ($valorTotalParcelasRestantes + $valorParcelaEditada),2);

                if ($valorParcelasRestantes < 0) {
                    $query = "UPDATE T197 SET T197_Valor_Parcela='{$valorAntigo}' WHERE T197_Id='{$T197_Id}'";
                    mysql_query($query);
                    return 'O valor inserido irá gerar parcelas negativas...<br>Favor alterar novamente';
                } else {
                    //se a diferença faltar alguns centavos, esse valor é agregado na próxima parcela depois da editada
                    if(($valorDiferençaProximaParcela > 0) || $valorDiferençaProximaParcela < 0){
                        $valorProximaParcela = $valorParcelasRestantes + $valorDiferençaProximaParcela;
                        $linha = mysql_fetch_assoc($retorno);
                        $query = "UPDATE T197 SET T197_Valor_Parcela='{$valorProximaParcela}' WHERE T197_Id='{$linha['T197_Id']}'";
                        mysql_query($query);
                        while ($linha = mysql_fetch_assoc($retorno)) {
                            $query = "UPDATE T197 SET T197_Valor_Parcela='{$valorParcelasRestantes}' WHERE T197_Id='{$linha['T197_Id']}'";
                            mysql_query($query);
                        }
                    }else{
                        while ($linha = mysql_fetch_assoc($retorno)) {
                            $query = "UPDATE T197 SET T197_Valor_Parcela='{$valorParcelasRestantes}' WHERE T197_Id='{$linha['T197_Id']}'";
                            mysql_query($query);
                        }
                    }
                }
            }
            return true;  
        }
        return true;
    }
    
    /**
     * fin002atualizarParcelas
     *
     * @param Form $form
     * @return bool
     */     
    public function fin002atualizarParcelas($form)
    {
        global $g;
        
        $D024_Id                = $form->campoValorEnviado('D024_Id');
        $Total_Parcelas         = $form->campoValorEnviado('Total_Parcelas');
        $IntervalorDias         = $form->campoValorEnviado('IntervalorDias');
        $diaFixo                = $form->campoValorEnviado('diaFixo');
        $primeiroVencimentoNum  = $form->campoValorEnviado('primeiroVencimentoNum');
        $primeiroVencimentoData = $form->campoValorEnviado('primeiroVencimentoData');
        $numeroParcelas         = $form->campoValorEnviado('Total_Parcelas');
        $D031_Id                = $form->campoValorEnviado('Portador_D031_Id');
        $Valor_Total_Orcamento  = $form->campoValorEnviado('valorSomado');
        $Ndcto                  = strtoupper($form->campoValorEnviado('Ndcto'));
        $Nduplicata             = $form->campoValorEnviado('Nduplicata');
        $Emissao                = $form->campoValorEnviado('Emissao');
        $Emissao                = gCorrigeDataInverte($Emissao);
        
        // $cSql_D024=mysql_query("SELECT * FROM D024 WHERE D024_Id='{$D024_Id}'");
        // $mD024=mysql_fetch_array($cSql_D024);
        
        // se for "a combinar" remove as parcelas que ja existem
        if ($numeroParcelas == '0') {
            mysql_query("DELETE T197 FROM T197 WHERE T197_C007_Id='{$g['usuarioAtual']}' AND T197_D024_Id ='{$D024_Id}'");
            if (mysql_error()) {
                return mysql_error();
            } else {
                return true;
            }
        }
        if ((empty($Ndcto)) || (!isset($Ndcto)) || ($Ndcto == '')) {
            return "Insira um Número Documento";
        }
        if ((empty($Nduplicata)) || (!isset($Nduplicata)) || ($Nduplicata == '')) {
            return "Insira um Número Duplicata";
        }
        if (empty($Emissao)) {
            return "Insira uma data de Emissão";
        }
        /* campo T007_D024_Id não existe na tabela T007 (copiado do sistema antigo
         * 
         * ) 
         $sql=mysql_query("SELECT T007_Id FROM T007 WHERE T007_D024_Id='{$D024_Id}' AND T007_Flag_Cancelada!='S'");
         if (mysql_num_rows($sql)>0) {
         return 'Nota Fiscal já foi impressa, pedido não pode ser alterado';
         }
         */
        
        //-- PEGA O VALOR TOTAL A VISTA
        $cSql_T197           = mysql_query("SELECT SUM(T197_Valor_Parcela) FROM T197 LEFT JOIN D031 ON D031_Id=T197_D031_Id WHERE T197_C007_Id='{$g['usuarioAtual']}'");
        $mT197_Total_A_Vista = mysql_fetch_array($cSql_T197);
        
        log("Portador vindo do form = ". $D031_Id);
        $cSql_T197 = mysql_query("SELECT * FROM T197 LEFT JOIN D031 ON D031_Id=T197_D031_Id WHERE T197_C007_Id='{$g['usuarioAtual']}'");
        while ($mT197 = mysql_fetch_array($cSql_T197)) {
            $mPrazoAntigo[$mT197['T197_Numero_Parcela']]    = $mT197['T197_Prazos_Dias'];
            $mPortadorAntigo[$mT197['T197_Numero_Parcela']] = $mT197['T197_D031_Id'];
        }
        
        mysql_query("DELETE T197 FROM T197 WHERE T197_C007_Id='{$g['usuarioAtual']}' AND T197_D024_Id ='{$D024_Id}'");
        
        //$Valor_Total_IPI=$mD024['D024_Valor_Total_IPI'];
        $Valor_Total_IPI = 0;
        //$Valor_Total_Orcamento=$mD024['D024_Valor_Total']-$mT197_Total_A_Vista[0];
        
        if ($Total_Parcelas > 0) {
            $Valor_Titulo_Parcela       = ($Valor_Total_Orcamento - $Valor_Total_IPI) / $Total_Parcelas;
            $Valor_Total_Titulo         = ((round($Valor_Titulo_Parcela, 2) * $Total_Parcelas) + $Valor_Total_IPI);
            $Valor_Total_Titulo         = round($Valor_Total_Titulo, 2);
            $Diferenca_Primeira_Parcela = 0; // criado pra evitar erro de undefined no for
            if ($Valor_Total_Titulo != $Valor_Total_Orcamento) {
                $Diferenca_Primeira_Parcela = $Valor_Total_Orcamento - $Valor_Total_Titulo;
            }
        }
        // se foi definido um intervalo de dias
        if ($IntervalorDias > 0) {
            // variavel para guardar a diferença de dias entre o intervalo de dias e o dia da primeira parcela    
            $acrescimo = 0;

            //inicializo os contatores dos caracteres
            $count = 1;
            $count2 = 1;
            $preSequencial = '';
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                //Concatenando um caracter da tabela ASCII para adicionar no tabela
                if($count == '27'){
                    //Chegou a letra Z       
                    $count = 1;
                    $preSequencial = chr($count2 + 64);
                    $count2++;
                }
                $NumeroDuplicata = $Nduplicata . $preSequencial . chr($count + 64);
                $Numero_Duplicata = strtoupper($NumeroDuplicata);

                $count++;
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D031_Id > 0) {
                    $mPortadorAntigo[$x] = $D031_Id;
                }
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, atribui ele
                    if (trim($primeiroVencimentoNum) != "") {
                        $mPrazoAntigo[$x] = $primeiroVencimentoNum;
                        $acrescimo        = $primeiroVencimentoNum;
                        // se o primeiro vencimento foi preenchido com uma data, transforma em intervalo e atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $date1            = new \DateTime($Emissao);
                        $date2            = new \DateTime(gCorrigeDataInverte($primeiroVencimentoData));
                        $interval         = $date1->diff($date2);
                        $mPrazoAntigo[$x] = $interval->days;
                        $acrescimo        = $interval->days;
                    }
                } else {
                    $mPrazoAntigo[$x] = (($IntervalorDias * ($x - 1)) + $acrescimo);
                }
                $cClausula = "INSERT INTO T197 (";
                $cClausula .= "T197_D024_Id,";
                $cClausula .= "T197_C007_Id,";
                $cClausula .= "T197_D031_Id,";
                $cClausula .= "T197_Numero_Parcela,";
                $cClausula .= "T197_Prazos_Dias,";
                $cClausula .= "T197_Data_Vencimento,";
                $cClausula .= "T197_Valor_Parcela,";
                $cClausula .= "T197_Numero_Documento,";
                $cClausula .= "T197_Numero_Duplicata,";
                $cClausula .= "T197_Data_Emissao";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$D024_Id',";
                $cClausula .= "'{$g['usuarioAtual']}',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$mPrazoAntigo[$x]',";
                $cClausula .= "DATE_ADD('$Emissao',INTERVAL $mPrazoAntigo[$x] DAY),";
                $cClausula .= "'$Valor_Titulo',";
                $cClausula .= "'$Ndcto',";
                $cClausula .= "'$Numero_Duplicata',";
                $cClausula .= "'$Emissao'";
                $cClausula .= ")";
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return mysql_error();
                }
                //$count++;
            }
            // ou se foi colocado um dia fixo    
        } else if ($diaFixo > 0) {
            if ($diaFixo > 31) {
                return "Por favor, insira um dia válido";
            }
            // $mes=date("m");
            // $ano=date("Y");
            $EmissaoDiaFixo = explode('-', $Emissao);
            $ano            = $EmissaoDiaFixo[0];
            $mes            = $EmissaoDiaFixo[1];
            
            $validou = checkdate($mes, $diaFixo, $ano);
            while (!$validou) {
                $diaFixo--;
                $validou = checkdate($mes, $diaFixo, $ano);
            }
            $primeiraData = "$ano-$mes-$diaFixo";
            
            $count = 1;
            $count2 = 1;
            $preSequencial = '';
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                //Concatenando um caracter da tabela ASCII para adicionar na tabela
                //$Numero_Duplicata = $Ndcto.chr($count+64);

                if($count == '27'){
                    //Chegou a letra Z       
                    $count = 1;
                    $preSequencial = chr($count2 + 64);
                    $count2++;
                }
                $NumeroDuplicata = $Nduplicata . $preSequencial . chr($count + 64);
                $Numero_Duplicata = strtoupper($NumeroDuplicata);

                $count++;

                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D031_Id > 0) {
                    $mPortadorAntigo[$x] = $D031_Id;
                }
                
                $dataVencimento = "0";
                $dataPrazoDias  = $Emissao;
                $numMeses       = $x;
                
                // se for a primeira parcela
                if ($x == 1) {
                    // se o primeiro vencimento foi preenchido em dias, soma esses dias a data atual
                    if (trim($primeiroVencimentoNum) != "") {
                        $date           = strtotime("+$primeiroVencimentoNum day", strtotime($Emissao));
                        $dataVencimento = date("Y-m-d", $date);
                        // se o primeiro vencimento foi preenchido com uma data, atribui
                    } else if (trim($primeiroVencimentoData) != "") {
                        $dataVencimento = gCorrigeDataInverte($primeiroVencimentoData);
                    }
                    $dataPrazoDias = $dataVencimento;
                    // looping feito para quando o dia fixo é menor do que o intervalo dias da primeira parcela e fica no mesmo mes
                    $dataAux       = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    $mes1          = date("m", strtotime($dataAux));
                    $mes2          = date("m", strtotime($dataPrazoDias));
                    while ((strtotime($dataPrazoDias) > strtotime($dataAux)) || ($mes1 == $mes2)) {
                        $dataAux      = date("Y-m-d", strtotime("+1 month", strtotime($dataAux)));
                        $mes1         = date("m", strtotime($dataAux));
                        $primeiraData = date("Y-m-d", strtotime("+1 month", strtotime($primeiraData)));
                    }
                    $numMeses = "0";
                } else {
                    $dataVencimento = $primeiraData;
                    $numMeses       = $x - 1;
                    // calcula a data de vencimento para depois
                    // poder calcular os dias até o vencimento da parcela (T197_Prazos_Dias)                        
                    $date           = strtotime("+$numMeses month", strtotime($dataVencimento));
                    $dataPrazoDias  = date("Y-m-d", $date);
                    $numMeses       = $x - 1;
                }
                
                $cClausula = "INSERT INTO T197 (";
                $cClausula .= "T197_D024_Id,";
                $cClausula .= "T197_C007_Id,";
                $cClausula .= "T197_D031_Id,";
                $cClausula .= "T197_Numero_Parcela,";
                $cClausula .= "T197_Data_Vencimento,";
                $cClausula .= "T197_Prazos_Dias,";
                $cClausula .= "T197_Valor_Parcela,";
                $cClausula .= "T197_Numero_Documento,";
                $cClausula .= "T197_Numero_Duplicata,";
                $cClausula .= "T197_Data_Emissao";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$D024_Id',";
                $cClausula .= "'{$g['usuarioAtual']}',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "DATE_ADD('$dataVencimento',INTERVAL $numMeses MONTH),";
                $cClausula .= "DATEDIFF('$dataPrazoDias','$Emissao'),";
                $cClausula .= "'$Valor_Titulo',";
                $cClausula .= "'$Ndcto',";
                $cClausula .= "'$Numero_Duplicata',";
                $cClausula .= "'$Emissao'";
                $cClausula .= ")";
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return mysql_error();
                }
                //$count++ ;
            }
        }
        return true;
    }
    
    /**
     * fin002reparcelar
     * 
     * @param String $D024_Id
     * @param String $C007_Id
     * @param String $valorSomado
     * @param String $extra
     * @return String/bool
     */
    public function fin002reparcelar($D024_Id, $C007_Id, $valorSomado, $extra)
    {
        global $g;
        
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T015_Flag_Reparcelar='S' AND T015_Flag_Cancelada!='S' AND T015_Data_Pagamento='0000-00-00' AND T015_C004_Id='{$g['empresaAtual']}'"));
        $sql   = mysql_query("SELECT * FROM T015 
                                       LEFT JOIN D024 ON D024_Id=T015_D024_Id 
                                       LEFT JOIN D073 ON D073_Id=T015_D073_Id 
                                       LEFT JOIN D031 ON D031_Id=T015_D031_Id 
                                       LEFT JOIN D014 ON D014_Id=T015_D014_Id 
                                       LEFT JOIN D032 ON D032_Id=D014_D032_Id
                                       LEFT JOIN D154 ON D154_Id=D014_D154_Id 
                                       LEFT JOIN T007 ON T007_Id=T015_T007_Id 
                                       LEFT JOIN C004 ON C004_Id=T015_C004_Id 
                                       LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
                                       WHERE {$extra}");
        
        $textGeradores = "";
        $numeroDuplicataAgrupamento = array();
        $numeroDocumentoAgrupamento = array();
        while ($row = mysql_fetch_assoc($sql)) {
            $textGeradores .= $row['T015_Id'] . ';' . $row['T015_Numero_Duplicata'] . ';' . $row['T015_Data_Emissao'] . ';' . $row['T015_Data_Vencimento'] . ';' . $row['T015_Valor_Duplicata'] . ';' . PHP_EOL;
            //return $textGeradores;
            $numeroDuplicataAgrupamento[] = $row['T015_Numero_Duplicata'];
            $numeroDocumentoAgrupamento[] = $row['T015_Numero_Documento'];
        }
        $numeroDuplicataAgrupamento = implode(",", $numeroDuplicataAgrupamento);
        $numeroDocumentoAgrupamento = implode(",", $numeroDocumentoAgrupamento);

        $sql = mysql_query("SELECT *,sum(T015_Valor_Saldo) TotalT015 
                              FROM T015  
                              LEFT JOIN D024 ON D024_Id=T015_D024_Id 
                              LEFT JOIN D073 ON D073_Id=T015_D073_Id 
                              LEFT JOIN D031 ON D031_Id=T015_D031_Id 
                              LEFT JOIN D014 ON D014_Id=T015_D014_Id 
                              LEFT JOIN D032 ON D032_Id=D014_D032_Id
                              LEFT JOIN D154 ON D154_Id=D014_D154_Id 
                              LEFT JOIN T007 ON T007_Id=T015_T007_Id 
                              LEFT JOIN C004 ON C004_Id=T015_C004_Id 
                              LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                              WHERE {$extra}");
        $retorno = mysql_fetch_array($sql);
        
        $erro=mysql_error();
        if (!empty($erro)){
            log("$erro");
            return "ERRO: $erro";
        }
        
        $sql2      = "SELECT SUM(T197_Valor_Parcela) TotalT197, count(T197_Id) as totalParcelas FROM T197 WHERE T197_C007_Id='{$g['usuarioAtual']}' AND T197_D024_Id='{$D024_Id}'";
        log($sql2);
        $retorno2  = mysql_query($sql2);
        $linha     = mysql_fetch_array($retorno2);
        $totalT015 = number_format(explode(".", $retorno['TotalT015']),2);
        $totalT197 = number_format(explode(".", $linha['TotalT197']),2);
        $totalParcelas = $linha['totalParcelas'];
        if ($totalT015[0] != $totalT197[0]) {
            return "Valor total não confere! ('{$totalT015['0']}' <> '{$totalT197['0']}')";
        } else {
            mysql_query("START TRANSACTION");
            $sql           = "SELECT * FROM T197 WHERE T197_C007_Id='{$g['usuarioAtual']}' AND T197_D024_Id='{$D024_Id}'";
            $result        = mysql_query($sql);
            while ($linha = mysql_fetch_array($result)) {
                if($linha['T197_Valor_Parcela'] == 0){
                    return "Existe uma parcela com valor '0,00', favor verificar";
                }else{
                    $queryT015 = "INSERT INTO T015 
                                    (T015_D024_Id,
                                     T015_D021_Id,
                                     T015_C004_Id,
                                     T015_D031_Id,
                                     T015_D014_Id,
                                     T015_D032_Id,
                                     T015_D073_Id,
                                     T015_Nome_Cliente,
                                     T015_Numero_Duplicata,
                                     T015_Numero_Documento,
                                     T015_Data_Emissao,
                                     T015_Data_Vencimento,
                                     T015_Data_Pagamento,
                                     T015_Valor_Duplicata,
                                     T015_Valor_Saldo,
                                     T015_Valor_Juros,
                                     T015_Valor_Desconto,
                                     T015_Valor_Total,
                                     T015_Data_Lancamento,
                                     T015_Hora_Lancamento,
                                     T015_Numero_Parcela,
                                     T015_Observacao_2,
                                     T015_Observacao_Reparcelamento,
                                     T015_Total_Parcelas)
                               VALUES
                                   ('{$retorno['T015_D024_Id']}',
                                    '{$retorno['T015_D021_Id']}',
                                    '{$retorno['T015_C004_Id']}',
                                    '{$linha['T197_D031_Id']}',
                                    '{$retorno['T015_D014_Id']}',
                                    '{$retorno['T015_D032_Id']}',
                                    '{$retorno['T015_D073_Id']}',
                                    '{$retorno['D024_Nome_Empresa']}',
                                    '{$linha['T197_Numero_Duplicata']}',
                                    '{$linha['T197_Numero_Documento']}',
                                    '{$linha['T197_Data_Emissao']}',
                                    '{$linha['T197_Data_Vencimento']}',
                                    '0000-00-00',
                                    '{$linha['T197_Valor_Parcela']}', 
                                    '{$linha['T197_Valor_Parcela']}', 
                                    '0',
                                    '0',
                                    '{$linha['T197_Valor_Parcela']}',
                                    curdate(),
                                    '" . strftime('%T') . "',
                                    '{$linha['T197_Numero_Parcela']}',
                                    '*** REPARCELAMENTO ***',
                                    '{$numeroDuplicataAgrupamento}',
                                    '{$totalParcelas}')";
                    log($queryT015);
                    mysql_query($queryT015);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro 2: $cErro/p/pClausula: $queryT015";
                    }
                    
                    $nT015_Id = $g['mysqlLastId'];
                    
                    $sql = mysql_query("SELECT * FROM T015 WHERE T015_Id='{$nT015_Id}'");
                    while ($row = mysql_fetch_assoc($sql)) {
                        $textGerados .= $row['T015_Id'] . ';' . $row['T015_Numero_Duplicata'] . ';' . $row['T015_Data_Emissao'] . ';' . $row['T015_Data_Vencimento'] . ';' . $row['T015_Valor_Duplicata'] . ';' . PHP_EOL;
                    }
                $count++;
                }
            }
            
            // insere registros na tabela de histórico de reparcelamento
            
            $query = "INSERT INTO T196 
                                 (T196_D024_Id,
                                 T196_C004_Id,
                                 T196_Data_Emissao,
                                 T196_Valor_Total,
                                 T196_Titulos_Geradores,                                 
                                 T196_Titulos_Gerados,
                                 T196_C007_Id,
                                 T196_Tipo)
                           VALUES                 
                               ('{$retorno['T015_D024_Id']}',
                                '{$retorno['T015_C004_Id']}',
                                curdate(),
                                '{$retorno['TotalT015']}',
                                '{$textGeradores}',
                                '{$textGerados}',
                                '{$g['usuarioAtual']}',
                                'R')";
            mysql_query($query);
            $T196_Id = $g['mysqlLastId'];
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 3: $cErro/p/pClausula: $query";
            }

            $arrayGeradores = explode("\n", $textGeradores);
            foreach($arrayGeradores as $linha){
                if(empty($linha)) { continue; }
                $linha = explode(";", $linha);
                mysql_query("INSERT INTO T248 (T248_T015_Id, T248_T196_Id) VALUES ('{$linha[0]}', '{$T196_Id}')");
                if (!empty($cErro)) { mysql_query("ROLLBACK"); return "Erro 7: $cErro<br>Clausula: $query"; }
            }

            $arrayGerados = explode("\n", $textGerados);
            foreach($arrayGerados as $linha){
                if(empty($linha)) { continue; }
                $linha = explode(";", $linha);
                mysql_query("INSERT INTO T248 (T248_T015_Id, T248_T196_Id) VALUES ('{$linha[0]}', '{$T196_Id}')");                
                if (!empty($cErro)) { mysql_query("ROLLBACK"); return "Erro 7: $cErro<br>Clausula: $query"; }
            }

            $sql4     = "SELECT T197_Numero_Documento FROM T197 WHERE T197_C007_Id='{$C007_Id}' OR T197_C007_Id='{$g['usuarioAtual']}'";
            $retorno4 = mysql_query($sql4);
            while ($linha4 = mysql_fetch_array($retorno4)) {
                $numeroDocumento = $linha4['T197_Numero_Documento'] . "<br />";
            }
            //-- Cancelando Contas a Receber
            mysql_query("UPDATE T015
                         LEFT JOIN D024 ON D024_Id=T015_D024_Id 
                         LEFT JOIN D073 ON D073_Id=T015_D073_Id 
                         LEFT JOIN D031 ON D031_Id=T015_D031_Id 
                         LEFT JOIN D014 ON D014_Id=T015_D014_Id 
                         LEFT JOIN D032 ON D032_Id=D014_D032_Id 
                         LEFT JOIN D154 ON D154_Id=D014_D154_Id 
                         LEFT JOIN T007 ON T007_Id=T015_T007_Id 
                         LEFT JOIN C004 ON C004_Id=T015_C004_Id 
                         LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                         SET T015_Flag_Cancelada='S', T015_Data_Pagamento=current_date(),T015_T015_Id_Agrupado='$nT015_Id', T015_Observacao_2='*** REPARCELADO {$numeroDocumento} ***' WHERE {$extra}");
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 3: $cErro/p/pClausula: $cClausula";
            }
            
            $sql     = "DELETE FROM T197 WHERE T197_C007_Id='{$g['usuarioAtual']}' AND T197_D024_Id={$D024_Id}";
            $retorno = mysql_query($sql);
            $erro    = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 4: $cErro/p/pClausula: $cClausula";
            }
            
            $retorno = mysql_query("COMMIT");
            if (!$retorno) {
                return "Erro 5: Falha no reparcelamento<br/>" . mysql_error();
            }
            return true;
        }  
    }
    
    /**
     * fin002inserirParcela
     *
     * @param Form $form
     * @return bool
     */     
    public function fin002inserirParcela($form)
    {
        global $g;
        $D024_Id        = $form['D024_Id'];
        $intervaloDias  = $form['IntervalorDias'];
        $portador       = $form['D031_Id'];
        $Total_Parcelas = $form['Total_Parcelas'];
        $valorTotal     = $form['valorSomado'];
        
        $diaFixo                = $form['diaFixo'];
        $primeiroVencimentoNum  = $form['primeiroVencimentoNum'];
        $primeiroVencimentoData = $form['primeiroVencimentoData'];
        $Emissao                = $form['Emissao'];
        $Emissao                = gCorrigeDataInverte($Emissao);
        $vencimento             = gCorrigeDataInverte($form['Emissao']);
        $DataVencimento         = $vencimento;

        // se foi definido um intervalo de dias
        if ($intervaloDias > 0) {
            $valorNovo = 0;            
            $query            = "SELECT * FROM T197 WHERE T197_D024_Id='{$D024_Id}' ORDER BY T197_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);

            if(!empty($linha)){
                $prazoUltima      = $linha['T197_Prazos_Dias'];
                $numeroUltima     = $linha['T197_Numero_Parcela'];
                $numeroNova       = $numeroUltima + 1;
                $vencimentoUltima = $linha['T197_Data_Vencimento'];
                $dataEmissao      = $linha['T197_Data_Emissao'];
                $numeroDcto       = $linha['T197_Numero_Documento'];

                //se houver parcelas geradas pega a ultima letra da duplicata e soma 1
                if(!empty($linha['T197_Numero_Duplicata'])){
                    log("Entrou");
                    $letraDuplicata = substr("{$linha['T197_Numero_Duplicata']}", -1);
                    $Duplicata = substr("{$linha['T197_Numero_Duplicata']}", 0 ,-1);
                    $Numero_Duplicata = $Duplicata . chr(ord($letraDuplicata) + 1);
                    log("DUPLICATA = ".$Numero_Duplicata);
                }
                log($query);

                // se a parcela a ser inserida é a primeira, pega a data informada no campo "Primeiro Vencimento"
                // se não, a data é a ultima + intervalo de dias informado.
                if (mysql_num_rows($retorno) == 0) {
                    if (trim($primeiroVencimentoNum) != "") {
                        $prazoNovo = $primeiroVencimentoNum;
                    } else if (trim($primeiroVencimentoData) != "") {
                        $date1     = new \DateTime($dataEmissao);
                        $date2     = new \DateTime(gCorrigeDataInverte($primeiroVencimentoData));
                        $interval  = $date1->diff($date2);
                        $prazoNovo = $interval->days;
                    }
                    $valorNovo = $valorTotal;
                } else {
                    $prazoNovo = $prazoUltima + $intervaloDias;
                    $valorNovo = 0;
                }
            
                $cClausula = "INSERT INTO T197 (";
                $cClausula .= "T197_D024_Id,";
                $cClausula .= "T197_C007_Id,";
                $cClausula .= "T197_D031_Id,";
                $cClausula .= "T197_Numero_Parcela,";
                $cClausula .= "T197_Prazos_Dias,";
                $cClausula .= "T197_Data_Vencimento,";
                $cClausula .= "T197_Valor_Parcela, ";
                $cClausula .= "T197_Numero_Documento, ";
                $cClausula .= "T197_Numero_Duplicata, ";
                $cClausula .= "T197_Data_Emissao";
                $cClausula .= ") VALUES (";
                $cClausula .= "'$D024_Id',";
                $cClausula .= "'{$g['usuarioAtual']}',";
                $cClausula .= "'$portador',";
                $cClausula .= "'$numeroNova',";
                $cClausula .= "'$prazoNovo',";
                $cClausula .= "DATE_ADD('$vencimentoUltima',INTERVAL $intervaloDias DAY),";
                $cClausula .= "'$valorNovo', ";
                $cClausula .= "'$numeroDcto', ";
                $cClausula .= "'$Numero_Duplicata', ";
                $cClausula .= "'$Emissao'";
                $cClausula .= ")";
            
                mysql_query($cClausula);
                $erro = mysql_error();
                
                log($cClausula);
            }else{
                return "Gere ao menos uma parcela para adicionar uma nova";
            }
        // ou se foi colocado um dia fixo    
        } else if ($diaFixo > 0) {
            
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            
            $valorNovo = 0;
            // $query = "SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'";
            // $retorno=mysql_query($query);        
            // $linha = mysql_fetch_assoc($retorno);
            // $valorTotal=$linha['T005_Valor_Total'];
            
            $query            = "SELECT * FROM T197 WHERE T197_D024_Id='{$D024_Id}' ORDER BY T197_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T197_Prazos_Dias'];
            $numeroUltima     = $linha['T197_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $vencimentoUltima = $linha['T197_Data_Vencimento'];
            $numeroDcto       = $linha['T197_Numero_Documento'];
            $numeroDuplicata  = $linha['T197_Numero_Duplicata'];
            $dataEmissao      = $linha['T197_Data_Emissao'];
            if (mysql_num_rows($retorno) == 0) {
                if (trim($primeiroVencimentoNum) != "") {
                    $date     = strtotime("+$primeiroVencimentoNum day", strtotime(date("Y-m-d")));
                    $dataNova = date("Y-m-d", $date);
                } else if (trim($primeiroVencimentoData) != "") {
                    $dataNova = gCorrigeDataInverte($primeiroVencimentoData);
                }
                $valorNovo = $valorTotal;
            } else {
                $ano = date("Y", strtotime($vencimentoUltima));
                $mes = date("m", strtotime($vencimentoUltima));
                $mes++;
                if ($mes == 13) {
                    $mes = 1;
                    $ano++;
                }
                $dataSomar = "$ano-$mes-$diaFixo";
                $validou   = checkdate($mes, $diaFixo, $ano);
                while (!$validou) {
                    $diaFixo--;
                    $validou = checkdate($mes, $diaFixo, $ano);
                }
                $dataNova = "$ano-$mes-$diaFixo";
                
                $valorNovo = 0;
            }
            
            $cClausula = "INSERT INTO T197 (";
            $cClausula .= "T197_D024_Id,";
            $cClausula .= "T197_C007_Id,";
            $cClausula .= "T197_D031_Id,";
            $cClausula .= "T197_Numero_Parcela,";
            $cClausula .= "T197_Data_Vencimento,";
            $cClausula .= "T197_Prazos_Dias,";
            $cClausula .= "T197_Valor_Parcela, ";
            $cClausula .= "T197_Numero_Documento, ";
            $cClausula .= "T197_Numero_Duplicata, ";
            $cClausula .= "T197_Data_Emissao";
            $cClausula .= ") VALUES (";
            $cClausula .= "'$D024_Id',";
            $cClausula .= "'{$g['usuarioAtual']}',";
            $cClausula .= "'$portador',";
            $cClausula .= "'$numeroNova',";
            $cClausula .= "'$dataNova',";
            $cClausula .= "DATEDIFF('$dataNova',current_date()),";
            $cClausula .= "'$valorNovo', ";
            $cClausula .= "'$numeroDcto', ";
            $cClausula .= "'$numeroDuplicata', ";
            $cClausula .= "'$Emissao'";
            $cClausula .= ")";
            mysql_query($cClausula);
            $erro = mysql_error();
        }
        
        if (empty($erro))
            return true;
        else
            return false;
        
    }
    
    /**
     * ultimoDiaMes
     *
     * @param bool $d
     * @param String $tipo
     * @return String
     */     
    public function ultimoDiaMes($d = false, $tipo = "T")
    {
        if (!$d) {
            $d = date("Y-m-d");
        }
        list($a, $m, $d) = explode('-', $d);
        $ultDia = date("d", mktime(0, 0, 0, $m + 1, 1, $a) - 1);
        if ($tipo == "T") {
            return "$a-$m-$ultDia";
        } else {
            return $ultDia;
        }
    }
    
    /**
     * validaDiaMes
     *
     * @param String $data
     * @param String $numeroSoma
     * @return String
     */     
    public function validaDiaMes($data, $numeroSoma = 1)
    {
        $tmp = explode('-', $data);
        
        /* Valida o mes para nao ser maior que 12 */
        if (($tmp[1] + $numeroSoma) > 12) {
            $tmp[1]  = 0;
            $novoAno = $tmp[0] + 1;
        } else {
            $novoAno = $tmp[0];
        }
        
        /* Coloca nas normas da DB o mes, 02,08,09 ... */
        if (strlen(($tmp[1] + $numeroSoma)) == 1) {
            $novoMes = '0' . ($tmp[1] + $numeroSoma);
        } elseif (strlen(($tmp[1] + $numeroSoma)) == 2) {
            $novoMes = $tmp[1] + $numeroSoma;
        }
        
        /* Verificar qual e o dia, caso o mes tenha 30 e a data seje 31 ou caso seja em Fevereiro */
        if ($tmp[2] == '29' || $tmp[2] == '30' || $tmp[2] == '31') {
            /* Se o proximo mes, tiver a data especial (29,30,31), coloca a nova conta com o mesmo dia */
            $proximoMes          = explode('-', $this->ultimoDiaMes($novoAno . '-' . $novoMes . '-' . $tmp[2]));
            $ultimoDiaProximoMes = $proximoMes[2];
            
            if ($ultimoDiaProximoMes >= $tmp[2]) {
                return $novoAno . '-' . $novoMes . '-' . $tmp[2];
            } else {
                /* Se o proximo mes nao tiver a data especial, coloca para o ultimo dia do mes */
                return $novoAno . '-' . $novoMes . '-' . $ultimoDiaProximoMes;
            }
        } else {
            /* Se nao for dias especiais, apenas muda o mes */
            return $novoAno . '-' . $novoMes . '-' . $tmp[2];
        }
    }
    
    /**
     * duplicarRegistros
     *
     * @param String $acaoId
     * @param String $numeroParcelas
     */    
    public function duplicarRegistros($acaoId,$numeroParcelas)
    {
        global $g;
        
        /* Atualiza o numero de parcelas da Conta Padrao */
        mysql_query("
                        UPDATE T015 SET
                            T015_Numero_Parcela = '1',
                            T015_Total_Parcelas = '{$numeroParcelas}',
                            T015_Id_Padrao = '{$acaoId}'
                        WHERE T015_Id = '{$acaoId}'
            ");
        
        for( $i=1; $i<=($numeroParcelas-1); $i++ )
        {
            $sql = "SELECT * FROM T015 WHERE T015_Id='{$acaoId}'";
            //log("SELECT * FROM T015 WHERE T015_Id='{$acaoId}'");
            $res = mysql_query($sql);
            $row = mysql_fetch_assoc($res);
            $campos = $valores = '';
            
            foreach ($row as $key => $value) {
                if ($key == 'T015_Id') { continue; }
                $campos .= "{$key},";
                $value = mysql_real_escape_string($value);
                $valores .= "'{$value}',";
            }

            /* Trata os novos valores da parcela */     
            $dataEmissao = $this->validaDiaMes( $row['T015_Data_Emissao'] ); /* Data Emissao */
            $dataVencimento = $this->validaDiaMes( $row['T015_Data_Vencimento'] ); /* Data Vencimento */

            $dataPagamento = $row['T015_Data_Pagamento']; /* Data Pagamento */

            $campos = trim($campos, ',');
            $valores = trim($valores, ',');
            $insert = "INSERT INTO T015 ($campos) VALUES ($valores)";

            mysql_query($insert);
            $idAtual = $g['mysqlLastId'];
            $parcelaAtual = $i+1;
            
            mysql_query("
                        UPDATE T015 SET
                            T015_Numero_Parcela = '{$parcelaAtual}',
                            T015_Total_Parcelas = '{$numeroParcelas}',
                            T015_Data_Emissao = '{$dataEmissao}',
                            T015_Data_Vencimento = '{$dataVencimento}',
                            T015_Data_Pagamento = '{$dataPagamento}'
                        WHERE T015_Id = '{$idAtual}'
            ");
            $acaoId = $idAtual;
        }
    }
    
    /**
     * duplicarRegistrosUpdate
     *
     * @param String $acaoId
     * @param String $numeroParcelas
     */     
    public function duplicarRegistrosUpdate($acaoId, $numeroParcelas)
    {
        /* Deleta todas as parcelas, exceto a Primaria */
        mysql_query("
					DELETE FROM T015 WHERE T015_Id_Padrao = '{$acaoId}' AND T015_Id !='{$acaoId}'
		");
        
        $this->duplicarRegistros($acaoId, $numeroParcelas);
    }

    /**
     * listaItensParaDividir
     *
     * @param bool $usarGroupBy
     * @param String $extra
     * @return String
     */
    public function listaItensParaDividir($usarGroupBy = false, $extra)
    {
        global $g;
        $extra = base64_decode($extra);
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T015_Data_Pagamento='0000-00-00' AND T015_Flag_Cancelada!='S' AND T015_Flag_Reparcelar='S' AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)"));
        $sql   = " SELECT ";
        $sql .= " T015_Id,T015_D024_Id,D024_Nome_Empresa ";
        $sql .= " FROM T015 ";
        $sql .= " LEFT JOIN D024 ON D024_Id=T015_D024_Id ";
        $sql .= " LEFT JOIN D073 ON D073_Id=T015_D073_Id ";
        $sql .= " LEFT JOIN D031 ON D031_Id=T015_D031_Id ";
        $sql .= " LEFT JOIN D014 ON D014_Id=T015_D014_Id ";
        $sql .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $sql .= " LEFT JOIN D154 ON D154_Id=D014_D154_Id ";
        $sql .= " LEFT JOIN T007 ON T007_Id=T015_T007_Id ";
        $sql .= " LEFT JOIN C004 ON C004_Id=T015_C004_Id ";
        $sql .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE {$extra}";
        //$sql .= " WHERE T015_C004_Id='{$g['empresaAtual']}'";
        //$sql .= "   AND T015_Data_Pagamento='0000-00-00'";
        //$sql .= "   AND T015_Flag_Reparcelar='S'";
        //$sql .= "   AND ifnull(T015_T035_Id,0)<=0 ";
        //$sql .= "   AND (T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null)";                
        if ($usarGroupBy) {
            $sql .= " GROUP BY T015_D024_Id";
        }
        $sqlReturn = mysql_query($sql);
        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado;
    }

    /**
     * atualizarParcelasDivisao
     *
     * @param String $T247_Id
     * @param String $campoEditado
     * @param String $valorAntigo
     * @param String $novoValor
     * @param String $valorTotal
     * @return String/bool
     */     
    public function atualizarParcelasDivisao($T247_Id, $campoEditado, $valorAntigo, $novoValor, $valorTotal)
    {
        $query            = "SELECT * FROM T247 WHERE T247_Id='{$T247_Id}'";
        $retorno          = mysql_query($query);
        $linha            = mysql_fetch_assoc($retorno);
        $dataVelhaD024    = '';
        $prazosDiasAntigo = $linha['T247_Prazos_Dias'];
        
        if ($campoEditado == 'T247_Prazos_Dias') {
            $date = new \DateTime($dataVelhaD024);
            $date->modify('+' . $novoValor . ' days');
            $query = "UPDATE T247 SET T247_Data_Vencimento='{$date->format('Y-m-d')}' WHERE T247_Id='{$T247_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T247_Data_Vencimento') {
            $dataAntiga = gCorrigeDataInverte($valorAntigo);
            $dataNova   = gCorrigeDataInverte($novoValor);
            
            $timeStampDataNova   = strtotime($dataNova);
            $timeStampDataAntiga = strtotime($dataAntiga);
            
            if ($timeStampDataAntiga == $timeStampDataNova) {
                return 'Datas Iguais';
            } else if ($timeStampDataAntiga < $timeStampDataNova) {
                $novaData = ($timeStampDataNova - $timeStampDataAntiga);
                $dias     = date('d', $novaData);
                $total = ($prazosDiasAntigo + $dias);
                $query = "UPDATE T247 SET T247_Prazos_Dias='{$total}' WHERE T247_Id='{$T247_Id}'";
                mysql_query($query);
                return true;
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
                $query    = "UPDATE T247 SET T247_Prazos_Dias='{$total}' WHERE T247_Id='{$T247_Id}'";
                mysql_query($query);
                return true;
            }
            
        } else if ($campoEditado == 'T247_Valor_Parcela') {
            $query      = "SELECT T247_C007_Id, T247_Numero_Parcela FROM T247 WHERE T247_Id='{$T247_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $C007_Id    = $linha['T247_C007_Id'];
            $numParcela = $linha['T247_Numero_Parcela'];
            $valorTotal = $valorTotal;
            $valorAntigo = gCorrigeNumeroInverte($valorAntigo);
            
            $query                  = "SELECT SUM(T247_Valor_Parcela) as Parcela FROM T247 WHERE T247_C007_Id='{$C007_Id}' AND T247_Numero_Parcela<='{$numParcela}'";
            $retorno                = mysql_query($query);
            $linha                  = mysql_fetch_assoc($retorno);
            $valorParcelaEditada    = $linha['Parcela'];
            $valorRestante          = $valorTotal - $valorParcelaEditada;
            
            $query             = "SELECT T247_Id FROM T247 WHERE T247_C007_Id='{$C007_Id}' AND T247_Numero_Parcela>'{$numParcela}'";
            $retorno           = mysql_query($query);
            $parcelasRestantes = mysql_num_rows($retorno);
            log($query2);

            //se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
            if ($parcelasRestantes != 0) {
                //calcula o valor da soma das próximas parcelas depois da que foi editada, pra ver se o valor bate
                $valorParcelasRestantes = $valorRestante / $parcelasRestantes;
                $valorParcelasRestantesFormatado = ($valorParcelasRestantes);
                $valorTotalParcelasRestantes = $valorParcelasRestantesFormatado * $parcelasRestantes;
                $valorDiferençaProximaParcela = $valorTotal - ($valorTotalParcelasRestantes + $valorParcelaEditada);

                if ($valorParcelasRestantes < 0) {
                    $query = "UPDATE T247 SET T247_Valor_Parcela='{$valorAntigo}' WHERE T247_Id='{$T247_Id}'";
                    mysql_query($query);
                    return 'O valor inserido irá gerar parcelas negativas...<br>Favor alterar novamente';
                } else {
                    //se a diferença faltar alguns centavos, esse valor é agregado na próxima parcela depois da editada
                    if(($valorDiferençaProximaParcela > 0 ) || $valorDiferençaProximaParcela < 0){


                        $valorProximaParcela = $valorParcelasRestantes + $valorDiferençaProximaParcela;

                        $linha = mysql_fetch_assoc($retorno);
                        $query = "UPDATE T247 SET T247_Valor_Parcela='{$valorProximaParcela}' WHERE T247_Id='{$linha['T247_Id']}'";
                        log($query);
                        mysql_query($query);

                        while ($linha = mysql_fetch_assoc($retorno)) {
                            $query = "UPDATE T247 SET T247_Valor_Parcela='{$valorParcelasRestantes}' WHERE T247_Id='{$linha['T247_Id']}'";
                            log($query);
                            mysql_query($query);
                        }
                    }else{                
                        while ($linha = mysql_fetch_assoc($retorno)) {
                            $query = "UPDATE T247 SET T247_Valor_Parcela='{$valorParcelasRestantes}' WHERE T247_Id='{$linha['T247_Id']}'";
                            mysql_query($query);
                        }
                    }
                }
            }
            return true;  
        }
        return true;
    }


     /**
     * atualizarParcelasDivisao
     *
     * @param Form $form
     * @return bool
     */     
    public function gerarParcelasDivisao($form)
    {
        global $g;
        
        $quantidadeParcelas = $form->campoValorEnviado('Total_Parcelas');
        $valorTitulos       = $form->campoValorEnviado('valorSomado');
        $dataVencimento     = $form->campoValorEnviado('dataVencimento');
        $mT015              = unserialize($form->campoValorEnviado('registroT015'));

        mysql_query("DELETE T247 FROM T247 WHERE T247_C007_Id='{$g['usuarioAtual']}'");
        if (mysql_error()) {
            return mysql_error();
        }
        
        // se for "a combinar" remove as parcelas que ja existem
        if($quantidadeParcelas == '0') {
            return true;
        }        
        
        $date1    = new \DateTime($mT015['T015_Data_Emissao']);
        $date2    = new \DateTime($mT015['T015_Data_Vencimento']);
        $interval = $date1->diff($date2);
        $prazo    = $interval->days;

        $valorParcela      = $valorTitulos / $quantidadeParcelas;
        $valorSomaParcelas = (round($valorParcela, 2) * $quantidadeParcelas);
        $valorSomaParcelas = round($valorSomaParcelas, 2);
        $diferencaPrimeiraParcela = 0; // criado pra evitar erro de undefined no for
        log(number_format($valorSomaParcelas,2).' != '.number_format($valorTitulos,2));
        if (number_format($valorSomaParcelas,2) != number_format($valorTitulos,2)) {
            $diferencaPrimeiraParcela = round(round($valorTitulos,2) - round($valorSomaParcelas,2),2);
        }
        log("diferencaPrimeiraParcela: $diferencaPrimeiraParcela");

        $count = 1;
        $count2 = 1;
        $preSequencial = '';
        for($i = 1; $i <= $quantidadeParcelas; $i++){
            $valorTotalParcela = $valorParcela;
            if($i == 1 && ($diferencaPrimeiraParcela > 0 || $diferencaPrimeiraParcela < 0)){
                $valorTotalParcela = $valorParcela + $diferencaPrimeiraParcela;
            }
            if($count == '27'){
                //Chegou a letra Z       
                $count = 1;
                $preSequencial = chr($count2 + 64);
                $count2++;
            }
            $Numero_Duplicata = $mT015['T015_Numero_Duplicata'] . $preSequencial . chr($count + 64);

            $count++;

            $cClausula = "INSERT INTO T247 (";
            $cClausula .= "T247_C007_Id,";
            $cClausula .= "T247_C004_Id,";
            $cClausula .= "T247_D031_Id,";
            $cClausula .= "T247_D073_Id,";
            $cClausula .= "T247_Numero_Parcela,";
            $cClausula .= "T247_Prazos_Dias,";
            $cClausula .= "T247_Data_Vencimento,";
            $cClausula .= "T247_Valor_Parcela,";
            $cClausula .= "T247_Numero_Documento,";
            $cClausula .= "T247_Numero_Duplicata,";
            $cClausula .= "T247_Data_Emissao";
            $cClausula .= ") VALUES (";
            $cClausula .= "'{$g['usuarioAtual']}',";
            $cClausula .= "'{$g['empresaAtual']}',";
            $cClausula .= "'{$mT015['T015_D031_Id']}',";
            $cClausula .= "'{$mT015['T015_D073_Id']}',";
            $cClausula .= "'{$i}',";
            $cClausula .= "'{$prazo}',";
            $cClausula .= "'{$mT015['T015_Data_Vencimento']}',";
            $cClausula .= "'{$valorTotalParcela}',";
            $cClausula .= "'{$mT015['T015_Numero_Documento']}',";
            $cClausula .= "'{$Numero_Duplicata}',";
            $cClausula .= "'{$mT015['T015_Data_Emissao']}'";
            $cClausula .= ")";
            mysql_query($cClausula);
            
            if (mysql_error()) {
                return mysql_error();
            }
        }
        return true;
    }

    /**
     * fin002Dividir
     * 
     * @param String $C007_Id
     * @param String $valorSomado
     * @param String $extra
     * @return String/bool
     */
    public function fin002Dividir($C007_Id, $valorSomado, $extra)
    {
        global $g;
        
        $sql = <<<EOT
            SELECT *
              FROM T015 
         LEFT JOIN D024 ON D024_Id=T015_D024_Id 
         LEFT JOIN D031 ON D031_Id=T015_D031_Id 
         LEFT JOIN D014 ON D014_Id=T015_D014_Id 
         LEFT JOIN D032 ON D032_Id=D014_D032_Id 
         LEFT JOIN D046 ON D046_Id=D024_D046_Id 
         LEFT JOIN T007 ON T007_Id=T015_T007_Id 
         LEFT JOIN C004 ON C004_Id=T015_C004_Id 
         LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
            WHERE 
EOT;
        $sql .= gPegarParteWhere(gInsertExtraWhere($extra, "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Cancelada!='S' AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00'"));
        $sql = mysql_query($sql);
        
        $textGeradores = "";
        $numeroDuplicataAgrupamento = array();
        $numeroDocumentoAgrupamento = array();

        while ($row = mysql_fetch_assoc($sql)) {
            $textGeradores .= $row['T015_Id'] . ';' . $row['T015_Numero_Duplicata'] . ';' . $row['T015_Data_Emissao'] . ';' . $row['T015_Data_Vencimento'] . ';' . $row['T015_Valor_Duplicata'] . ';' . PHP_EOL;
            //return $textGeradores;
            $numeroDuplicataAgrupamento[] = $row['T015_Numero_Duplicata'];
            $numeroDocumentoAgrupamento[] = $row['T015_Numero_Documento'];
        }

        $numeroDuplicataAgrupamento = implode(",", $numeroDuplicataAgrupamento);
        $numeroDocumentoAgrupamento = implode(",", $numeroDocumentoAgrupamento);

         $sql = <<<EOT
            SELECT
            *, 
            sum(T015_Valor_Saldo) TotalT015
            FROM T015 
            left join D024 on D024_Id=T015_D024_Id 
            left join D031 on D031_Id=T015_D031_Id 
            left join D014 on D014_Id=T015_D014_Id 
            LEFT JOIN D032 ON D032_Id=D014_D032_Id 
            left join D046 on D046_Id=D024_D046_Id
            left join D073 ON D073_Id=T015_D073_Id 
            left join T007 on T007_Id=T015_T007_Id 
            LEFT JOIN C004 ON C004_Id=T015_C004_Id
            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
            WHERE 
EOT;
        $sql .= gPegarParteWhere(gInsertExtraWhere($extra, "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Cancelada!='S' AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00'"));    
        log($sql);
        log($extra);
        $retorno = mysql_fetch_array(mysql_query($sql));
        
        $erro=mysql_error();
        if (!empty($erro)){
            log("$erro");
            return "ERRO: $erro";
        }
        
        $sql2      = "SELECT SUM(T247_Valor_Parcela) TotalT247 FROM T247 WHERE T247_C007_Id='{$g['usuarioAtual']}'";
        $retorno2  = mysql_fetch_array(mysql_query($sql2));

        $totalT015 = number_format($retorno['TotalT015'],2);
        $totalT247 = number_format($retorno2['TotalT247'],2);

        log($totalT015 .'='. number_format($retorno['TotalT015'],2));

        if ($totalT015 != $totalT247) {
            return "Valor total não confere! ({$totalT015} <> {$totalT247})";
        } else {

            mysql_query("START TRANSACTION");
            $sql         = "SELECT * FROM T247 WHERE T247_C007_Id='{$g['usuarioAtual']}'";
            $result      = mysql_query($sql);
            $textGerados = '';
            $count       = 0; 
            while ($linha = mysql_fetch_array($result)) {
                if($linha['T247_Valor_Parcela'] == 0){
                    return "Existe uma parcela com valor '0,00', favor verificar";
                }else{

                    $queryT015 = "INSERT INTO T015 
                                (T015_D024_Id,
                                 T015_D021_Id,
                                 T015_C004_Id,
                                 T015_D031_Id,
                                 T015_D014_Id,
                                 T015_D032_Id,
                                 T015_D073_Id,
                                 T015_Nome_Cliente,
                                 T015_Numero_Duplicata,
                                 T015_Numero_Documento,
                                 T015_Data_Emissao,
                                 T015_Data_Vencimento,
                                 T015_Data_Pagamento,
                                 T015_Valor_Duplicata,
                                 T015_Valor_Saldo,
                                 T015_Valor_Juros,
                                 T015_Valor_Desconto,
                                 T015_Valor_Total,
                                 T015_Data_Lancamento,
                                 T015_Hora_Lancamento,
                                 T015_Numero_Parcela,
                                 T015_Observacao_2,
                                 T015_Observacao_Reparcelamento,
                                 T015_Observacao)
                           VALUES
                               ('{$retorno['T015_D024_Id']}',
                                '{$retorno['T015_D021_Id']}',
                                '{$linha['T247_C004_Id']}',
                                '{$linha['T247_D031_Id']}',
                                '{$retorno['T015_D014_Id']}',
                                '{$retorno['T015_D032_Id']}',
                                '{$linha['T247_D073_Id']}',
                                '{$retorno['D024_Nome_Empresa']}',
                                '{$linha['T247_Numero_Duplicata']}',
                                '{$linha['T247_Numero_Documento']}',
                                '{$linha['T247_Data_Emissao']}',
                                '{$linha['T247_Data_Vencimento']}',
                                '0000-00-00',
                                '{$linha['T247_Valor_Parcela']}', 
                                '{$linha['T247_Valor_Parcela']}', 
                                '0',
                                '0',
                                '{$linha['T247_Valor_Parcela']}',
                                curdate(),
                                '" . strftime('%T') . "',
                                '{$linha['T247_Numero_Parcela']}',
                                '*** REPARCELAMENTO ***',
                                '{$numeroDuplicataAgrupamento}',
                                '{$retorno['T015_Observacao']}')";
                    mysql_query($queryT015);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro 2: $cErro<br>Clausula: $queryT015";
                    }
                
                    $nT015_Id = $g['mysqlLastId'];
                
                    $sql = mysql_query("SELECT * FROM T015 WHERE T015_Id='{$nT015_Id}'");
                    while ($row = mysql_fetch_assoc($sql)) {
                        $textGerados .= $row['T015_Id'] . ';' . $row['T015_Numero_Duplicata'] . ';' . $row['T015_Data_Emissao'] . ';' . $row['T015_Data_Vencimento'] . ';' . $row['T015_Valor_Duplicata'] . ';' . PHP_EOL;
                    }

                    $count++;
                }                    
            }
            
            // insere registros na tabela de histórico de reparcelamento
            
            $query = "INSERT INTO T196 
                                 (T196_D024_Id,
                                 T196_C004_Id,
                                 T196_Data_Emissao,
                                 T196_Valor_Total,
                                 T196_Titulos_Geradores,                                 
                                 T196_Titulos_Gerados,
                                 T196_C007_Id,
                                 T196_Tipo)
                           VALUES                 
                               ('{$retorno['T015_D024_Id']}',
                                '{$retorno['T015_C004_Id']}',
                                curdate(),
                                '{$retorno['TotalT015']}',
                                '{$textGeradores}',
                                '{$textGerados}',
                                '{$g['usuarioAtual']}',
                                'R')";
            mysql_query($query);
            $T196_Id = $g['mysqlLastId'];
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 2: $cErro/p/pClausula: $query";
            }

            $arrayGeradores = explode("\n", $textGeradores);
            foreach($arrayGeradores as $linha){
                if(empty($linha)) { continue; }
                $linha = explode(";", $linha);
                mysql_query("INSERT INTO T248 (T248_T015_Id, T248_T196_Id) VALUES ('{$linha[0]}', '{$T196_Id}')");
                if (!empty($cErro)) { mysql_query("ROLLBACK"); return "Erro 7: $cErro<br>Clausula: $query"; }
            }

            $arrayGerados = explode("\n", $textGerados);
            foreach($arrayGerados as $linha){
                if(empty($linha)) { continue; }
                $linha = explode(";", $linha);
                mysql_query("INSERT INTO T248 (T248_T015_Id, T248_T196_Id) VALUES ('{$linha[0]}', '{$T196_Id}')");                
                if (!empty($cErro)) { mysql_query("ROLLBACK"); return "Erro 7: $cErro<br>Clausula: $query"; }
            }

            $sql4     = "SELECT T247_Numero_Documento FROM T247 WHERE T247_C007_Id='{$C007_Id}' OR T247_C007_Id='{$g['usuarioAtual']}'";
            $retorno4 = mysql_query($sql4);
            while ($linha4 = mysql_fetch_array($retorno4)) {
                $numeroDocumento = $linha4['T247_Numero_Documento'] . "<br />";
            }
            //-- Cancelando Contas a Receber
            $sql = "UPDATE T015
                         LEFT JOIN D024 ON D024_Id=T015_D024_Id 
                         LEFT JOIN D073 ON D073_Id=T015_D073_Id 
                         LEFT JOIN D031 ON D031_Id=T015_D031_Id 
                         LEFT JOIN D014 ON D014_Id=T015_D014_Id 
                         LEFT JOIN D032 ON D032_Id=D014_D032_Id 
                         LEFT JOIN D154 ON D154_Id=D014_D154_Id 
                         LEFT JOIN T007 ON T007_Id=T015_T007_Id 
                         LEFT JOIN C004 ON C004_Id=T015_C004_Id 
                         LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                         SET T015_Flag_Cancelada='S', T015_Data_Pagamento=current_date(),T015_T015_Id_Agrupado='$nT015_Id', T015_Observacao_2='*** REPARCELADO {$numeroDocumento} ***' WHERE ";
            $sql .= gPegarParteWhere(gInsertExtraWhere($extra, "(T015_T015_Id_Agrupado<=0 or T015_T015_Id_Agrupado is null) AND T015_Flag_Reparcelar='S' AND T015_Data_Pagamento='0000-00-00'"));    
            mysql_query($sql);        
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 3: $cErro/p/pClausula: $cClausula";
            }
            
            $sql     = "DELETE FROM T247 WHERE T247_C007_Id='{$g['usuarioAtual']}'";
            $retorno = mysql_query($sql);
            $erro    = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 4: $cErro/p/pClausula: $cClausula";
            }
            
            $retorno = mysql_query("COMMIT");
            if (!$retorno) {
                return "Erro 4: Falha no reparcelamento<br/>" . mysql_error();
            }
            return true;
        }  
    }

    public function importarCSV($arquivo)
    {
   
        global $g;
        
        $mArquivo = $arquivo;
        
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);

        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'csv') {
            return "O arquivo deve ser no formato CSV. Tipo do arquivo importado (.{$cTipo_Arquivo})";
        }
       
        //-- ABRE O ARQUIVO CSV
        $ponteiro      = fopen($mArquivo["tmp_name"], "r");

        if (!$ponteiro){
            return "ERRO - Não foi possível ler o arquivo $cArquivo ({$mArquivo['tmp_name']})";
        }

        //variavel criada para retornar os erros
        $return = "";
       
        //Array criado para a comparação dos nomes das colunas com o nome do documento
        $array_cols=array('EMPRESA','CPF','CNPJ','GRUPO','CONTA','CODIGO DE BARRAS','DATA EMISSAO','DATA VENCIMENTO','NUMERO DOCUMENTO', 'NUMERO DUPLICATA', 'PORTADOR', 'CENTRO CUSTO', 'VALOR TITULO', 'OBSERVACAO');
        //posicao do array <0>       <1>   <2>     <3>     <4>          <5>             <6>              <7>                 <8>                 <9>             <10>         <11>            <12>
        //    <13>        

        //linha dnão pode ser 0 pois no excel começa no 1 
        $linha = 1;       
        $incrementaResult=0;
        //o array_linha recebe a uma linha de cada vez e enquanto vai percorrendo cada linha vai executando as validações internas
        //as posições do array são as colunas de cada linha
        while ($array_linha = fgets($ponteiro)) {
                
                // Limpa characteres especiais do arquivo texto
                $array_linha = str_replace(array(chr(239),chr(187),chr(191)),'',$array_linha);

                $array_linha = explode(';',$array_linha);
                for ($count=0;$count<count($array_linha);$count++){
                     if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                         $array_linha[$count] = utf8_encode($array_linha[$count]);
                      
                    }
                }

                $array_linha[0] = trim($array_linha[0]);
                $array_linha[1] = trim($array_linha[1]);
                $array_linha[2] = trim($array_linha[2]);
                $array_linha[3] = trim($array_linha[3]);
                $array_linha[4] = trim($array_linha[4]);
                $array_linha[5] = trim($array_linha[5]);
                $array_linha[6] = trim($array_linha[6]);
                $array_linha[7] = trim($array_linha[7]);
                $array_linha[8] = trim($array_linha[8]);
                $array_linha[9] = trim($array_linha[9]);
                $array_linha[10] = trim($array_linha[10]);
                $array_linha[11] = trim($array_linha[11]);
                $array_linha[12] = trim($array_linha[12]);
                $array_linha[12] = trim($array_linha[12]);
                           

                $msgLinha = "Linha ".str_pad($linha, 5, "0", STR_PAD_LEFT)." - ";
               
                if ($linha==1){
                    // Validar número de colunas e o se o nome das colunas bate com o padrão  
                    $total_cols = count($array_cols);
                    if ($total_cols!=count($array_linha)) {
                        $return .= "Deve haver apenas {$total_cols} coluna(s) \\n \\n";
                    }
                    // Validação dos titulos das colunas
                    for ($count=0;$count<count($array_linha);$count++){
                        $array_linha[$count] = strtoupper($array_linha[$count]);
                        if (($count+1)>count($array_cols)) {     
                            $return .='A coluna "'.trim($array_linha[$count]).'" extrapola o número de colunas esperado.\\n \\n';  
                        } else if (trim($array_cols[$count]) != trim($array_linha[$count])) {     
                            $return .='O titulo "'.trim($array_linha[$count]).'" não confere com o titulo "'.$array_cols[$count].'" esperado.\\n \\n';                         
                        }
                    }


                } else { 
                    $array_linha[0]= mb_strtoupper($array_linha[0]);
                    $array_linha[3]= mb_strtoupper($array_linha[3]);
                    $array_linha[4]= mb_strtoupper($array_linha[4]);
                    $array_linha[10]= mb_strtoupper($array_linha[10]);
                    $array_linha[12]= mb_strtoupper($array_linha[12]); 
                    

                    //Valida Empresa
                    if(!empty($array_linha[0])){
                        $cSelectEmpresa = "SELECT C004_Id FROM C004 where C004_Id='$array_linha[0]'";
                        $cSQL_T015_C004 = mysql_query($cSelectEmpresa);
                        $mT015_C004 = mysql_fetch_array($cSQL_T015_C004);
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectEmpresa; 
                        }
                        if(mysql_num_rows($cSQL_T015_C004) == 0){
                            $return .= $msgLinha . "A Empresa ". $array_linha[0] ." não está cadastrada!\\n";
                        } 
                    }else{
                        if(empty($array_linha[0])){
                        $return .=  $msgLinha . "A coluna EMPRESA está em branco.\\n";
                        }
                    }   

                    // Valida a coluna 1 e 2 - CPF e CNPJ
                    if(empty($array_linha[1]) && (empty($array_linha[2]))){
                    // A coluna do cpf e cnpj não podem estar em branco
                        $return .=  $msgLinha . "O CPF e o CNPJ estão em branco. \\n";
                    } else if (!empty($array_linha[1]) && (!empty($array_linha[2]))){
                     // A coluna do cpf e cnpj não podem estar ambas preenchidas
                        $return .=  $msgLinha . "O CPF e o CNPJ estão preenchidos, preencha apenas um dos dois documentos. \\n";
                    } else {

                        if(!empty($array_linha[1])){
                            // Validar se código está duplicado
                            $cClausula = "SELECT D024_Id from D024 WHERE D024_Cpf='$array_linha[1]'";
                            $cSQL_D024 = mysql_query($cClausula);
                            $mD024     = mysql_fetch_array($cSQL_D024);
                            $resultD024[$incrementaResult] = $mD024['D024_Id'];
                            $cErro     = mysql_error();
                            if(!empty($cErro)){
                                $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                            }                              
                            if(mysql_num_rows($cSQL_D024) == 0){
                                $return .= $msgLinha . "O CPF ". $array_linha[1] ." não está cadastrado!\\n";
                            }

                        }else{
                            if(!empty($array_linha[2])){
                                // Validar se código está duplicado
                                $cClausula = "SELECT D024_Id from D024 WHERE D024_Cnpj='$array_linha[2]'";
                                $cSQL_D024 = mysql_query($cClausula);
                                $mD024     = mysql_fetch_array($cSQL_D024);
                                $resultD024[$incrementaResult] = $mD024['D024_Id'];
                                $cErro     = mysql_error();
                                if(!empty($cErro)){
                                    $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
                                }     
                                if(mysql_num_rows($cSQL_D024) == 0){
                                    $return .= $msgLinha . "O CNPJ ". $array_linha[2] ." não está cadastrado!\\n";
                                }    
                            } 
                        }                     
                    }                 

                    //Valida se o GRUPO existe
                    if(!empty($array_linha[3])){
                        $cSelectGrupo = "SELECT D032_Id FROM D032 where D032_Descricao='$array_linha[3]' ";
                        $cSQL_D032 = mysql_query($cSelectGrupo);
                        $mD032 = mysql_fetch_array($cSQL_D032);
                        $resultD032[$incrementaResult] = $mD032['D032_Id'];
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectGrupo; 
                        }
                        if(mysql_num_rows($cSQL_D032) == 0){
                            $return .= $msgLinha . "O GRUPO ". $array_linha[3] ." não está cadastrado!\\n";
                        } 
                    }

                    //Valida se a CONTA existe
                    if(!empty($array_linha[4])){
                        $cSelectConta = "SELECT D014_Id, D014_D032_Id FROM D014 where D014_Subconta='$array_linha[4]' AND D014_D032_Id='{$mD032['D032_Id']}' AND D014_Flag_Ativo='S'";
                        $cSQL_D014 = mysql_query($cSelectConta);
                        $mD014 = mysql_fetch_array($cSQL_D014);
                        $resultD014[$incrementaResult] = $mD014['D014_Id'];
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectGrupo; 
                        }
                        if(mysql_num_rows($cSQL_D014) == 0){
                            $return .= $msgLinha . "A CONTA ". $array_linha[4] ." para o GRUPO ". $array_linha[3]. " não está cadastrada ou está inativa!\\n";
                        }
                    } else {
                        if(empty($array_linha[4])){
                        $return .=  $msgLinha . "A coluna CONTA está em branco.\\n";
                        }
                    }

                
                    // Valida se a coluna 6 - Data emissão está em branco                   
                    if(empty($array_linha[6])){
                        $return .=  $msgLinha . "A coluna DATA EMISSAO está em branco.\\n";
                    }

                    // Valida se a coluna 7 - Data Vencimento está em branco
                    if(empty($array_linha[7])){
                        $return .=  $msgLinha . "A coluna DATA VENCIMENTO está em branco.\\n";
                    }

                    //Validar duplicação de registros
                    if(!empty($array_linha[9])){
                        $cSelectDuplicata = "SELECT T015_Numero_Duplicata FROM T015 where T015_Numero_Duplicata='$array_linha[9]'"; 
                        $cSQL_T015_Duplicata = mysql_query($cSelectDuplicata);
                        $mT015_Duplicata = mysql_fetch_array($cSQL_T015_Duplicata);
                        $resultDuplicata = $m['T015_Numero_Duplicata']; 
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectDuplicata; 
                        }
                        if(mysql_num_rows($cSQL_T015_Duplicata) > 0) {          
                             $return .=  $msgLinha . "A Duplicata " .$array_linha[9]. " já está cadastrada.\\n";
                        }                         
                    } else {
                        if(empty($array_linha[9])){
                            $return .=  $msgLinha . "A coluna NUMERO DUPLICATA está em branco.\\n";
                        }
                    }                      
                
                    //Valida Portador
                    if(!empty($array_linha[10])){
                        $cSelectPortador = "SELECT D031_Id FROM D031 where D031_Portador='$array_linha[10]' ";
                        $cSQL_D031 = mysql_query($cSelectPortador);
                        $mD031 = mysql_fetch_array($cSQL_D031);
                        $resultD031[$incrementaResult] = $mD031['D031_Id'];
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectPortador; 
                        } else { 
                        if(mysql_num_rows($cSQL_D031) == 0){
                            $return .= $msgLinha . "O PORTADOR ". $array_linha[10] ." não está cadastrado!\\n";
                            } 
                        }
                    } else {
                        if(empty($array_linha[10])){
                        $return .=  $msgLinha . "A coluna PORTADOR está em branco.\\n";
                        }   
                    }

                    // Valida Centro Custo
                    if(!empty($array_linha[11])){
                        if($array_linha[11]!="1"){
                            $return .= $msgLinha . "CENTRO CUSTO ". $array_linha[11] ." não é válido! Preencha com 1. \\n";    
                        }
                    } else {
                        if(empty($array_linha[11])){
                        $return .=  $msgLinha . "A coluna CENTRO CUSTO está em branco.\\n";
                        }  
                    }

                    //Valida Valor Título
                    if(empty($array_linha[12])){
                        $return .=  $msgLinha . "A coluna VALOR TÍTULO está em branco.\\n";
                    }           

                    $incrementaResult++;                
               }

               $linha++;         

        }
        


        //SE NÃO OCORREU NENHUM ERRO NAS VALIDAÇÕES
        if(empty($return)){

            //-- ABRE O ARQUIVO CSV
            $ponteiro      = fopen($mArquivo["tmp_name"], "r");

            //INICIA A TRANSAÇÃO DO BANCO
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            //linha=0 é o cabeçalho
            $linha=1;
            $incrementaResult=0;

            //PERCORRER AS LINHAS NOVAMENTE PARA REALIZAR O INSERT
            while (($array_linha = fgets($ponteiro))) {            
                    if($linha==1){
                        $linha++;
                        continue;
                    }                                    
                    
                    $array_linha = explode(';',$array_linha);
                    for ($count=0;$count<count($array_linha);$count++){
                         if ((mb_detect_encoding($array_linha[$count])=='UTF-8') && (!mb_detect_encoding($array_linha[$count],'UTF-8',true))) {     
                             $array_linha[$count] = utf8_encode($array_linha[$count]);
                         }
                    }

                    //LIMPAR ESPAÇOS ANTES E DEPOIS DA STRING
                    $array_linha[0] = trim($array_linha[0]);
                    $array_linha[1] = trim($array_linha[1]);
                    $array_linha[2] = trim($array_linha[2]);
                    $array_linha[3] = trim($array_linha[3]);
                    $array_linha[4] = trim($array_linha[4]);
                    $array_linha[5] = trim($array_linha[5]);
                    $array_linha[6] = trim($array_linha[6]);
                    $array_linha[7] = trim($array_linha[7]);
                    $array_linha[8] = trim($array_linha[8]);
                    $array_linha[9] = trim($array_linha[9]);
                    $array_linha[10] = trim($array_linha[10]);
                    $array_linha[11] = trim($array_linha[11]);
                    $array_linha[12] = trim($array_linha[12]);
                    $array_linha[13] = trim($array_linha[13]);
                                 
                    
                    //TRANSFORMA UMA STRING EM UPPERCASE
                    $array_linha[0]= mb_strtoupper($array_linha[0]);
                    $array_linha[3]= mb_strtoupper($array_linha[3]);
                    $array_linha[4]= mb_strtoupper($array_linha[4]);
                    $array_linha[10]= mb_strtoupper($array_linha[10]);
                    $array_linha[12]= mb_strtoupper($array_linha[12]); 

                    //Corrige a data
                    $array_linha[6] = gCorrigeDataInverte($array_linha[6]);
                    $array_linha[7] = gCorrigeDataInverte($array_linha[7]);
             

                    $cInsert = "INSERT INTO T015 
                                      (T015_C004_Id,
                                       T015_D024_Id,
                                       T015_D032_Id,  
                                       T015_D014_Id,
                                       T015_Codigo_Barras, 
                                       T015_Data_Emissao, 
                                       T015_Data_Vencimento, 
                                       T015_Numero_Documento, 
                                       T015_Numero_Duplicata,
                                       T015_D031_Id,
                                       T015_D073_Id,
                                       T015_Valor_Duplicata,
                                       T015_Observacao) 
                               VALUES ('$array_linha[0]',
                                       '$resultD024[$incrementaResult]',
                                       '$resultD032[$incrementaResult]',
                                       '$resultD014[$incrementaResult]',
                                       '$array_linha[5]',
                                       '$array_linha[6]',
                                       '$array_linha[7]',
                                       '$array_linha[8]',
                                       '$array_linha[9]',
                                       '$resultD031[$incrementaResult]',
                                       '$array_linha[11]',
                                       '$array_linha[12]',
                                       '$array_linha[13]')";


                    $cSQL_T015 = mysql_query($cInsert);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro: $cErro \\n\\n $cInsert";
                    }

                    $incrementaResult++;
                    $linha++;

            }
            log("linha: ".$linha);

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            
            return "Processamento de arquivo concluído com sucesso!!\\n************************************************** \\n\\n Foram inseridas ".($linha-2)." Contas a Pagar!";

      
        } else {

            return $return;

        }        
    }

     /**
     * duplicar_fin002form01
     * Chamada DUPLICAR do menu automatico da janela
     *
     * @param String $dias
     * @return String
     */    
	public Function duplicar_fin002form01($divId, $divIdRoot, $tabela, $id)
    {
        $mT015 = array("T015_Id" => $id);
        $mT015Anterior = array();

        $retorno = $this->FIN002DuplicarTitulosSelecionados($mT015, $mT015Anterior);

        if ($retorno === true){
            $retorno = "Título duplicado com sucesso.";
            $retorno = "dialogConfirm('DUPLICAR', '{$retorno}');";
        } else {
            $retorno = "dialogConfirm('ERRO', '{$retorno}');";
        }

        return $retorno;
    }


    /**
     * verificarDuplicarEmpresaPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
	public Function FIN002VerificaDuplicarTitulosSelecionados($mT015,$mT015Anterior) 
    {
        return true;
    }

    /**
     * duplicarPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
    public function FIN002DuplicarTitulosSelecionados($mT015, $mT015Anterior)
    {
        global $g;
        $T015 = mysql_query("select * from T015 where T015_Id='{$mT015['T015_Id']}'");
        $mT015 = mysql_fetch_assoc($T015);

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $campos         = "";
        $valores        = "";
        foreach ($mT015 as $key => $value) {
            if ($key == 'T015_Id') {
                continue;
            }elseif($key == 'T015_T117_Id'){
                $campos.=$key.", ";
                $valores.= "'',";
            }elseif($key == 'T015_T035_Id'){
                $campos.=$key.", ";
                $valores.= "'',";
            }elseif($key == 'T015_T013_Id'){
                $campos.=$key.", ";
                $valores.= "'',";
            }elseif($key == 'T015_Flag_Baixar_Lote'){
                $campos.=$key.", ";
                $valores.= "'',";
            }elseif($key == 'T015_Flag_Reparcelar'){
                $campos.=$key.", ";
                $valores.= "'',";
            }elseif($key == 'T015_Data_Pagamento'){
                $campos.=$key.", ";
                $valores.= "'0000-00-00',";
            }elseif($key == 'T015_Data_Baixa'){
                $campos.=$key.", ";
                $valores.= "'0000-00-00',";
            }elseif($key == 'T015_Valor_Desconto'){
                $campos.=$key.", ";
                $valores.= "0,";
            }elseif($key == 'T015_Valor_Recebido'){
                $campos.=$key.", ";
                $valores.= "0,";
            }elseif($key == 'T015_Valor_Juros'){
                $campos.=$key.", ";
                $valores.= "0,";
            }elseif($key == 'T015_Valor_Taxas'){
                $campos.=$key.", ";
                $valores.= "0,";
            }elseif($key == 'T015_Numero_Duplicata'){
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "+',";
            } elseif ($value==NULL) {
                $campos .= $key . ", ";
                $valores .= "'',";
            } else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT015 = "INSERT INTO T015 ({$campos}) VALUES ({$valores})";
        $sql = mysqli_query($insT015);
        $erro = mysql_error();
        if (!$sql) {
            mysql_query("ROLLBACK");
            return "Erro 2: $erro" ;
        }
        $T015_Id_Novo = mysql_insert_id();

        mysql_query("UPDATE T015 set T015_Valor_Total=0, T015_Id_Padrao='{$mT015['T015_Id']}' WHERE T015_Id={$T015_Id_Novo}");
            
        mysql_query("COMMIT");

        return true;
    }

        /**
     * duplicarPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
    public function insereAcrescimoDesconto($T103_Id)
    {
        global $g;

        $T103 = mysql_query("SELECT T103_Valor_Desconto,
                                    T103_Valor_Juros,
                                    T103_T015_Id
                               FROM T103 
                              WHERE T103_Id='{$T103_Id}'");

        $mT103 = mysql_fetch_array($T103);
    
        if($mT103['T103_Valor_Desconto'] > 0){
            mysql_query("INSERT INTO T033 (T033_Valor, T033_Historico, T033_Flag_Deb_Cred, T033_Data_Lancamento, T033_T015_Id, T033_C004_Id) VALUES ('{$mT103['T103_Valor_Desconto']}','DESCONTO' , 'D', NOW(), '{$mT103['T103_T015_Id']}', '{$g['empresaAtual']}')");
        }
        if($mT103['T103_Valor_Juros'] > 0){
            mysql_query("INSERT INTO T033 (T033_Valor, T033_Historico, T033_Flag_Deb_Cred, T033_Data_Lancamento, T033_T015_Id, T033_C004_Id) VALUES ('{$mT103['T103_Valor_Juros']}','JUROS' , 'C', NOW(), '{$mT103['T103_T015_Id']}', '{$g['empresaAtual']}')");  
        }    

        mysqli_query("call T015_Gravar_Totalizacao('{$mT103['T103_T015_Id']}')"); 

        return true;
    }
    
    /**
     * vinculaContaGrupoT033
     *
     * @param String $T033_Id
     * @return array
     */     
    public function vinculaContaGrupoT033($T033_Id, $form)
    {
        // FUNCAO PERSONALIZADO NO APP FINANCEIRO LANCAMENTO ACRESCIMOS/DESCONTOS EM BANCOS E CAIXAS
        return true;   
    }

    /**
     * verificaValorBaixa
     *
     * @param String $mT002
     * @return array
     */     
    public function verificaValorPago($mT015)
    {
        // FUNCAO PERSONALIZADO NO APP FINANCEIRO LANCAMENTO ACRESCIMOS/DESCONTOS EM BANCOS E CAIXAS
        return $mT015['T103_Valor_Total_Recebido'];   
    }

     /**
     * lancarAcrescimoDesconto
     *
     * @param String $mT015
     * @return array
     */     
    public function lancarAcrescimoDesconto($mT015, $D007_Id, $dataBaixa)
    {
        // FUNCAO PERSONALIZADO NO APP FINANCEIRO LANCAMENTO ACRESCIMOS/DESCONTOS EM BANCOS E CAIXAS
        return true;   
    }

    /**
     * verificaValorEstorno
     *
     * @param String $mT015
     * @return array
     */     
    public function verificaValorEstorno($mT015)
    {
        // FUNCAO PERSONALIZADO NO APP FINANCEIRO LANCAMENTO ACRESCIMOS/DESCONTOS EM BANCOS E CAIXAS
        return $mT015['T015_Valor_Pago'];
    }

    /**
     * estornaAcrescimoDesconto
     *
     * @param String $mT015
     * @return array
     */     
    public function estornaAcrescimoDesconto($mT015,$dataEstorno,$observacao)
    {
        // FUNCAO PERSONALIZADO NO APP FINANCEIRO LANCAMENTO ACRESCIMOS/DESCONTOS EM BANCOS E CAIXAS
        return true;
    }

}
