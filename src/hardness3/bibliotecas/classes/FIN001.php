<?php
namespace hardness;
definirClasse('FIN001');
class FIN001_
{

    public function listaItensParaAgruparSmart($usarGroupBy = false, $extra){
        return "O processo não está ativado. <br> Habilite o processo de agrupamento.";
    }

    public function baixarSmart($D007_Id, $dataBaixa, $extra, $dataRecebimento){
        return "Parâmetro de baixa parcial habilitado.";
    }

    public function estornoSmart($valorEstorno, $D027_Id){
        return true;
    }
    
    
    /**
     * baixar
     * @param String $D007_Id
     * @param String $dataBaixa
     * @param String $extra
     * @param String $dataRecebimentoo
     * @return String/bool
     */
    public function baixar($D007_Id, $dataBaixa, $extra, $dataRecebimento, $T045_Id = 0)
    {
        //file_put_contents('/tmp/debug-baixa', "{$D007_Id}, {$dataBaixa}, {$extra}, {$dataRecebimento} \n", FILE_APPEND);
        global $g;

        $extraOriginal = base64_decode($extra);
        $extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}'"));

        // começa a TRANSACTION
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        // verificações
        $cClausula = <<<EOT
	        SELECT T103_Id
	          FROM T002 
	     LEFT JOIN T103 on T002_Id=T103_T002_Id 
	     LEFT JOIN D024 on D024_Id=T002_D024_Id 
	     LEFT JOIN D027 on D027_Id=T002_D027_Id 
	     LEFT JOIN T117 on T117_Id=T002_T117_Id
         LEFT JOIN D021 on T002_D021_Id=D021_Id
	     LEFT JOIN D014 on D014_Id=T002_D014_Id 
	     LEFT JOIN T007 on T007_Id=T002_T007_Id 
	     LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
         LEFT JOIN T005 ON T005_Id=T007_T005_Id
	         WHERE {$extra}
	      GROUP BY T002_Id
EOT;          
        $T103 = mysql_query($cClausula);

        while ($mT103 = mysql_fetch_array($T103)) {
            $this->insereAcrecimoDesconto($mT103['T103_Id']);
        }

        // verificações
        $cClausula = <<<EOT
	        SELECT T002_Numero_Duplicata,
	               (sum(T103_Valor_Total_Recebido)-(T002_Valor_Duplicata+T002_Valor_Juros-T002_Valor_Desconto-T002_Valor_Recebido)) Diferenca 
	        FROM T002 
	        LEFT JOIN T103 on T002_Id=T103_T002_Id 
	        LEFT JOIN D024 on D024_Id=T002_D024_Id 
	        LEFT JOIN D027 on D027_Id=T002_D027_Id 
	        LEFT JOIN T117 on T117_Id=T002_T117_Id
            LEFT JOIN D021 on T002_D021_Id=D021_Id
	        LEFT JOIN D014 on D014_Id=T002_D014_Id 
	        LEFT JOIN T007 on T007_Id=T002_T007_Id 
	        LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
            LEFT JOIN T005 ON T005_Id=T007_T005_Id 
	        WHERE {$extra}
	        #WHERE T002_Flag_Baixar_Lote='S' 
	        #AND T002_Data_Recebimento='0000-00-00'
	        #AND T002_C004_Id='{$g['empresaAtual']}' 
	        GROUP BY T002_Id
EOT;
        $T002 = mysql_query($cClausula);

        $erro = mysql_error();
        
        if (!empty($erro)) {
            return "<div class='msgError'>Erro na verificacao de divergencias no recebimento! Contacte o Suporte Técnico.<br />Mysql error: <br />" . htmlspecialchars($erro, ENT_QUOTES) . "</div>";
        }
        
        if (empty($D007_Id)) {
            return "Selecione um banco";
        }
        
        $Mensagem = '';
        while ($mT002 = mysql_fetch_array($T002)) {
            if ($mT002['Diferenca'] > 0) {
                $diferenca = gCorrigeNumero($mT002['Diferenca']);
                $Mensagem .= "- Titulo {$mT002['T002_Numero_Duplicata']}: Diferença de {$diferenca}<br />";
            }
        }
        
        $extra     = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}' AND T103_D027_Id is null"));
		$cClausula = <<<EOT
        	SELECT T002_Numero_Duplicata,
        	       T103_D027_Id
        	FROM T002 
        	LEFT JOIN T103 on T002_Id=T103_T002_Id 
        	LEFT JOIN D024 on D024_Id=T002_D024_Id 
        	LEFT JOIN D027 on D027_Id=T002_D027_Id
            LEFT JOIN D021 on T002_D021_Id=D021_Id 
        	LEFT JOIN T117 on T117_Id=T002_T117_Id 
        	LEFT JOIN D014 on D014_Id=T002_D014_Id 
        	LEFT JOIN T007 on T007_Id=T002_T007_Id 
        	LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
            LEFT JOIN T005 ON T005_Id=T007_T005_Id 
        	WHERE {$extra}
        	#WHERE T002_Flag_Baixar_Lote='S' 
        	#AND T002_Data_Recebimento='0000-00-00'
        	#AND T002_C004_Id='{$g['empresaAtual']}' 
        	#AND T103_D027_Id is null
EOT;
        $T002 = mysql_query($cClausula);
        $erro = mysql_error();
        
        if (!empty($erro)) {
            return "<div class='msgError'>Erro na verificacao de divergencias no recebimento! Contacte o Suporte Técnico.<br />Mysql error: <br />" . htmlspecialchars($erro, ENT_QUOTES) . "</div>";
        }
        
        while ($mT002 = mysql_fetch_array($T002)) {
            if ($mT002['T103_D027_Id'] <= 0) {
                $Mensagem .= "- Titulo: {$mT002['T002_Numero_Duplicata']} falta o Portador<br />";
            }
        }
        
        if (!empty($Mensagem)) {
            return "<div class='msgError'><span class='msgErrorTitle'>Titulos com problemas:</span> $Mensagem </div>";
        }
        
        $extra     = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}' AND T103_Valor_Recebido>0"));
        $cClausula = <<<EOT
	        SELECT  
                T002_Id,
                T103_D027_Id,
                T002_C004_Id,
                T002_D024_Id,
                T002_D014_Id,
                T002_Numero_Agencia,
                T002_Numero_Conta,
                T002_Numero_Cheque,
                T002_Numero_Duplicata,
                T103_Valor_Total_Recebido,
                T103_Id
	        FROM T002 
    	        LEFT JOIN T103 on T002_Id=T103_T002_Id 
    	        LEFT JOIN D024 on D024_Id=T002_D024_Id 
    	        LEFT JOIN D027 on D027_Id=T002_D027_Id 
    	        LEFT JOIN T117 on T117_Id=T002_T117_Id 
                LEFT JOIN D021 on T002_D021_Id=D021_Id
    	        LEFT JOIN D014 on D014_Id=T002_D014_Id 
    	        LEFT JOIN T007 on T007_Id=T002_T007_Id 
    	        LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                LEFT JOIN T005 ON T005_Id=T007_T005_Id 
	        WHERE {$extra}
	        #WHERE T002_Flag_Baixar_Lote='S' 
	        #AND T002_Data_Recebimento='0000-00-00'
	        #AND T002_C004_Id='{$g['empresaAtual']}' 
	        #AND T103_Valor_Recebido>0 
	        ORDER BY T002_Data_Vencimento,D024_Nome_Empresa
EOT;
        $T002 = mysql_query($cClausula);
        log("SELECIONE UM TITULO PARA BAIXAR: ".$cClausula);
        if (mysql_num_rows($T002) <= 0) {
            return "<div class='msgError'>Selecione um titulo para baixar</div>";
        }
                
        $nTotal_Baixas = 0;
        $T045_Id       = (int) $T045_Id;
        if ($T045_Id <= 0) {
            $cClausula = <<<EOT
        	INSERT INTO T045 ( 
	        	T045_C004_Id, 
	        	T045_C007_Id, 
	        	T045_Data 
        	) values( 
	        	'{$g['empresaAtual']}', 
	        	'{$g['usuarioAtual']}', 
	        	now() 
        	)
EOT;
            $cSql_T045 = mysql_query($cClausula);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
            }
            $T045_Id = $g['mysqlLastId'];
        }
        
        $nT002_Id = 'xxxxxxx';
        
        while ($mT002 = mysql_fetch_array($T002)) {
            if ($nT002_Id != $mT002['T002_Id']) {
                $nTotal_Baixas++;
                $nT002_Id = $mT002['T002_Id'];
                $cClausula = <<<EOT
                	UPDATE T002 set 
	                	T002_D007_Id='{$D007_Id}', 
	                	T002_T045_Id='{$T045_Id}', 
	                	T002_Flag_Baixar_Lote='N' 
                	WHERE T002_Id='{$mT002['T002_Id']}' 
EOT;
                $cSql_T045 = mysql_query($cClausula);
                $cErro     = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
                }
            }

            $this->insertAntesT019($mT002);

            $T002_Nome_Cliente = mysql_real_escape_string($mT002['T002_Nome_Cliente']);
            $cClausula = <<<EOT
                INSERT INTO T019 ( 
                    T019_T002_Id, 
                    T019_D007_Id, 
                    T019_D027_Id, 
                    T019_C004_Id, 
                    T019_D024_Id, 
                    T019_D014_Id, 
                    T019_Complemento, 
                    T019_Numero_Agencia, 
                    T019_Numero_Conta, 
                    T019_Numero_Cheque, 
                    T019_Data_Lancamento, 
                    T019_Referente, 
                    T019_Valor_Lancamento, 
                    T019_Flag_Deb_Crd 
                ) values ( 
                    '{$mT002['T002_Id']}', 
                    '{$D007_Id}', 
                    '{$mT002['T103_D027_Id']}', 
                    '{$mT002['T002_C004_Id']}', 
                    '{$mT002['T002_D024_Id']}', 
                    '{$mT002['T002_D014_Id']}', 
                    '{$T002_Nome_Cliente}', 
                    '{$mT002['T002_Numero_Agencia']}', 
                    '{$mT002['T002_Numero_Conta']}', 
                    '{$mT002['T002_Numero_Cheque']}', 
                    '{$dataRecebimento}', 
                    'DUPLI . No. {$mT002['T002_Numero_Duplicata']}', 
                    '{$mT002['T103_Valor_Total_Recebido']}', 
                    '1' 
                ) 
EOT;
            $cSql_T019 = mysqli_query($cClausula);
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
            }

            $this->lancarAcrescimoDesconto($mT002, $D007_Id, $dataBaixa, $dataRecebimento);
            
            mysql_query("CALL T002_Baixar_Titulo('{$mT002['T002_Id']}','{$dataBaixa}')");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
            }
            
            $atualizaPortadorBaixa = $g['C031']['atualizaPortadorPagamentoTitulo'] == 'S';

            if ($atualizaPortadorBaixa) {
                mysqli_query("UPDATE T002 SET T002_D027_Id = '{$mT002['T103_D027_Id']}' WHERE T002_Id = '{$mT002['T002_Id']}'");
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
                }
            }

            mysql_query("DELETE FROM T103 WHERE T103_Id='$mT002[T103_Id]'");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
            }
            
        }
        mysql_query("COMMIT");
        return true;
    }

    public function baixarEmLoteAjaxStart($D007_Id, $dataBaixa, $extra, $dataRecebimento, $limit = 20)
    {
        global $g;

        $D007_Id         = (int) $D007_Id;
        $dataBaixa       = trim((string) $dataBaixa);
        $dataRecebimento = trim((string) $dataRecebimento);
        $limit           = (int) $limit;

        if ($D007_Id <= 0) {
            return array('ok' => false, 'erro' => 'Selecione um banco');
        }
        if (empty($dataBaixa)) {
            return array('ok' => false, 'erro' => 'Informe a data de baixa');
        }
        if (empty($dataRecebimento)) {
            return array('ok' => false, 'erro' => 'Informe a data de recebimento');
        }
        if ($limit <= 0) {
            $limit = 20;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        $ids = $this->buscarIdsBaixaLoteAjax($extra);
        if (count($ids) <= 0) {
            return array('ok' => false, 'erro' => 'Selecione um titulo para baixar');
        }

        $T045_Id = $this->criarT045BaixaLoteAjax();
        if ($T045_Id <= 0) {
            return array('ok' => false, 'erro' => 'Nao foi possivel iniciar o processo de baixa.');
        }

        $token = sha1(
            $g['empresaAtual'] . '|' .
            $g['usuarioAtual'] . '|' .
            $D007_Id . '|' .
            $dataBaixa . '|' .
            $dataRecebimento . '|' .
            $extra . '|' .
            microtime(true)
        );

        if (!isset($_SESSION['fin001BaixaLoteAjax'])) {
            $_SESSION['fin001BaixaLoteAjax'] = array();
        }

        $_SESSION['fin001BaixaLoteAjax'][$token] = array(
            'token'           => $token,
            'empresaAtual'    => $g['empresaAtual'],
            'usuarioAtual'    => $g['usuarioAtual'],
            'D007_Id'         => $D007_Id,
            'dataBaixa'       => $dataBaixa,
            'dataRecebimento' => $dataRecebimento,
            'extra'           => $extra,
            'ids'             => $ids,
            'total'           => count($ids),
            'processado'      => 0,
            'ok'              => 0,
            'erros'           => 0,
            'lastIndex'       => 0,
            'lastId'          => 0,
            'lastErrors'      => array(),
            'T045_Id'         => $T045_Id,
            'done'            => false,
            'running'         => false,
            'limit'           => $limit,
        );

        log("FIN001 baixa lote start token={$token} total=" . count($ids) . " empresa={$g['empresaAtual']} usuario={$g['usuarioAtual']}");

        return array(
            'ok'   => true,
            'data' => $this->montarResumoBaixaLoteAjax($token, $_SESSION['fin001BaixaLoteAjax'][$token]),
        );
    }

    public function baixarEmLoteAjaxBatch($token, $limit = 20)
    {
        if (empty($token) || !isset($_SESSION['fin001BaixaLoteAjax'][$token])) {
            return array('ok' => false, 'erro' => 'Processo de baixa nao encontrado ou expirado.');
        }

        $ctx = &$_SESSION['fin001BaixaLoteAjax'][$token];
        if (!empty($ctx['done'])) {
            return array('ok' => true, 'data' => $this->montarResumoBaixaLoteAjax($token, $ctx));
        }
        if (!empty($ctx['running'])) {
            return array('ok' => false, 'erro' => 'Ja existe um lote em processamento. Aguarde a conclusao.');
        }

        $limit = (int) $limit;
        if ($limit <= 0) {
            $limit = (int) $ctx['limit'];
        }
        if ($limit <= 0) {
            $limit = 20;
        }
        if ($limit > 100) {
            $limit = 100;
        }

        $idsLote = array_slice($ctx['ids'], (int) $ctx['lastIndex'], $limit);
        if (count($idsLote) <= 0) {
            $ctx['done'] = true;
            $_SESSION['fin001BaixaLoteAjax'][$token] = $ctx;
            return array('ok' => true, 'data' => $this->montarResumoBaixaLoteAjax($token, $ctx));
        }

        $ctx['running']    = true;
        $ctx['lastErrors'] = array();
        $_SESSION['fin001BaixaLoteAjax'][$token] = $ctx;

        $ultimoId = (int) end($idsLote);
        log("FIN001 baixa lote batch token={$token} inicio=" . ((int) $ctx['lastIndex']) . " quantidade=" . count($idsLote) . " ultimoId={$ultimoId}");

        $this->prepararFlagsBaixaLoteAjax($ctx['ids'], $idsLote);
        $retorno = $this->baixar($ctx['D007_Id'], $ctx['dataBaixa'], $ctx['extra'], $ctx['dataRecebimento'], $ctx['T045_Id']);

        if ($retorno === true) {
            $ctx['processado'] += count($idsLote);
            $ctx['ok']         += count($idsLote);
            $ctx['lastIndex']  += count($idsLote);
            $ctx['lastId']      = $ultimoId;
            $ctx['done']        = ($ctx['processado'] >= $ctx['total']);
            $ctx['running']     = false;
            $_SESSION['fin001BaixaLoteAjax'][$token] = $ctx;

            return array('ok' => true, 'data' => $this->montarResumoBaixaLoteAjax($token, $ctx));
        }

        $ctx['erros']++;
        $ctx['running']    = false;
        $ctx['done']       = true;
        $ctx['lastErrors'] = array(
            array(
                'ids'  => $idsLote,
                'erro' => strip_tags($retorno),
            )
        );
        $_SESSION['fin001BaixaLoteAjax'][$token] = $ctx;

        $this->restaurarFlagsBaixaLoteAjax($ctx['ids']);
        log("FIN001 baixa lote erro token={$token} erro=" . strip_tags($retorno));

        return array('ok' => false, 'erro' => $retorno, 'data' => $this->montarResumoBaixaLoteAjax($token, $ctx));
    }

    public function buscarIdsBaixaLoteAjax($extra)
    {
        global $g;

        $extraOriginal = base64_decode($extra);
        $where = gPegarParteWhere(gInsertExtraWhere(
            $extraOriginal,
            "T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}' AND IFNULL(T103_Valor_Recebido,0)>0"
        ));

        $sql = <<<EOT
            SELECT DISTINCT T002_Id
              FROM T002
         LEFT JOIN T103 on T002_Id=T103_T002_Id
         LEFT JOIN D024 on D024_Id=T002_D024_Id
         LEFT JOIN D027 on D027_Id=T002_D027_Id
         LEFT JOIN T117 on T117_Id=T002_T117_Id
         LEFT JOIN D021 on T002_D021_Id=D021_Id
         LEFT JOIN D014 on D014_Id=T002_D014_Id
         LEFT JOIN T007 on T007_Id=T002_T007_Id
         LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
         LEFT JOIN T005 ON T005_Id=T007_T005_Id
             WHERE {$where}
          ORDER BY T002_Id
EOT;

        $res = mysql_query($sql);
        $ids = array();
        while ($row = mysql_fetch_array($res)) {
            $ids[] = (int) $row['T002_Id'];
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

        mysql_query("UPDATE T002 SET T002_Flag_Baixar_Lote='N' WHERE T002_Id IN (" . implode(',', $snapshotIds) . ")");
        if (count($idsAtivos) > 0) {
            mysql_query("UPDATE T002 SET T002_Flag_Baixar_Lote='S' WHERE T002_Id IN (" . implode(',', $idsAtivos) . ")");
        }

        return true;
    }

    public function restaurarFlagsBaixaLoteAjax($snapshotIds)
    {
        $snapshotIds = array_values(array_filter(array_map('intval', (array) $snapshotIds)));
        if (count($snapshotIds) <= 0) {
            return true;
        }

        $sql  = "UPDATE T002 ";
        $sql .= "SET T002_Flag_Baixar_Lote='S' ";
        $sql .= "WHERE T002_Id IN (" . implode(',', $snapshotIds) . ") ";
        $sql .= "AND T002_Data_Recebimento='0000-00-00'";
        mysql_query($sql);

        return true;
    }

    public function criarT045BaixaLoteAjax()
    {
        global $g;

        $sql = <<<EOT
            INSERT INTO T045 (
                T045_C004_Id,
                T045_C007_Id,
                T045_Data
            ) VALUES (
                '{$g['empresaAtual']}',
                '{$g['usuarioAtual']}',
                now()
            )
EOT;
        mysql_query($sql);
        if (mysql_error()) {
            return 0;
        }

        return (int) $g['mysqlLastId'];
    }

    public function montarResumoBaixaLoteAjax($token, $ctx = null)
    {
        if ($ctx === null) {
            $ctx = $_SESSION['fin001BaixaLoteAjax'][$token];
        }

        $total      = (int) $ctx['total'];
        $processado = (int) $ctx['processado'];
        $restante   = $total - $processado;
        if ($restante < 0) {
            $restante = 0;
        }

        return array(
            'processToken' => $token,
            'total'        => $total,
            'processado'   => $processado,
            'restante'     => $restante,
            'percentual'   => ($total > 0) ? (int) round(($processado * 100) / $total) : 100,
            'ok'           => (int) $ctx['ok'],
            'errosCount'   => (int) $ctx['erros'],
            'lastId'       => (int) $ctx['lastId'],
            'lastIndex'    => (int) $ctx['lastIndex'],
            'done'         => !empty($ctx['done']),
            'erros'        => isset($ctx['lastErrors']) ? $ctx['lastErrors'] : array(),
        );
    }

    public function prepararBaixaLoteAjaxDireto($D007_Id, $dataBaixa, $extra, $dataRecebimento)
    {
        $D007_Id         = (int) $D007_Id;
        $dataBaixa       = trim((string) $dataBaixa);
        $dataRecebimento = trim((string) $dataRecebimento);

        if ($D007_Id <= 0) {
            return array('ok' => false, 'erro' => 'Selecione um banco');
        }
        if (empty($dataBaixa)) {
            return array('ok' => false, 'erro' => 'Informe a data de baixa');
        }
        if (empty($dataRecebimento)) {
            return array('ok' => false, 'erro' => 'Informe a data de recebimento');
        }

        $ids = $this->buscarIdsBaixaLoteAjax($extra);
        error_log('[FIN001 baixa class] preparar ids=' . count($ids) . ' D007_Id=' . $D007_Id . ' dataBaixa=' . $dataBaixa . ' dataRecebimento=' . $dataRecebimento);
        if (count($ids) <= 0) {
            return array('ok' => false, 'erro' => 'Selecione um titulo para baixar');
        }

        $T045_Id = $this->criarT045BaixaLoteAjax();
        if ($T045_Id <= 0) {
            return array('ok' => false, 'erro' => 'Nao foi possivel iniciar o processo de baixa.');
        }

        log("FIN001 baixa lote preparar total=" . count($ids) . " T045_Id={$T045_Id}");

        return array(
            'ok' => true,
            'data' => array(
                'ids'         => $ids,
                'total'       => count($ids),
                'processado'  => 0,
                'restante'    => count($ids),
                'done'        => false,
                'T045_Id'     => $T045_Id,
                'erros'       => array(),
                'errosCount'  => 0,
            ),
        );
    }

    public function processarBaixaLoteAjaxDireto($snapshotIds, $idsLote, $D007_Id, $dataBaixa, $extra, $dataRecebimento, $T045_Id, $processadoAtual = 0)
    {
        $snapshotIds    = array_values(array_filter(array_map('intval', (array) $snapshotIds)));
        $idsLote        = array_values(array_filter(array_map('intval', (array) $idsLote)));
        $D007_Id        = (int) $D007_Id;
        $T045_Id        = (int) $T045_Id;
        $processadoAtual = (int) $processadoAtual;

        if (count($snapshotIds) <= 0) {
            return array('ok' => false, 'erro' => 'Nenhum titulo informado para o processamento.');
        }
        if (count($idsLote) <= 0) {
            error_log('[FIN001 baixa class] processar lote vazio total=' . count($snapshotIds) . ' processadoAtual=' . $processadoAtual);
            return array(
                'ok' => true,
                'data' => $this->montarResumoBaixaLoteAjaxDireto($snapshotIds, $processadoAtual, array()),
            );
        }

        error_log('[FIN001 baixa class] processar lote total=' . count($snapshotIds) . ' lote=' . count($idsLote) . ' processadoAtual=' . $processadoAtual . ' primeiroId=' . (int) reset($idsLote) . ' ultimoId=' . (int) end($idsLote));
        log("FIN001 baixa lote processar quantidade=" . count($idsLote) . " processadoAtual={$processadoAtual} T045_Id={$T045_Id}");

        $this->prepararFlagsBaixaLoteAjax($snapshotIds, $idsLote);
        $retorno = $this->baixar($D007_Id, $dataBaixa, $extra, $dataRecebimento, $T045_Id);
        if ($retorno === true) {
            $processadoNovo = $processadoAtual + count($idsLote);
            error_log('[FIN001 baixa class] lote ok processadoNovo=' . $processadoNovo . ' total=' . count($snapshotIds));
            return array(
                'ok' => true,
                'data' => $this->montarResumoBaixaLoteAjaxDireto($snapshotIds, $processadoNovo, array()),
            );
        }

        $this->restaurarFlagsBaixaLoteAjax($snapshotIds);
        error_log('[FIN001 baixa class] lote erro msg=' . strip_tags($retorno));
        log("FIN001 baixa lote erro direto=" . strip_tags($retorno));

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
            'total'       => $total,
            'processado'  => $processado,
            'restante'    => $restante,
            'done'        => ($processado >= $total),
            'erros'       => $erros,
            'errosCount'  => count($erros),
        );
    }
    
    /**
     * baixarRetorno
     *
     * @param String $D007_Id
     * @param String $dataBaixa
     * @param String $extra
     * @param String $dataRecebimento
     * @return String/bool
     */     
    public function baixarRetorno($D007_Id, $dataBaixa, $extra, $dataRecebimento)
    {
        global $g;
        $extraOriginal = base64_decode($extra);
        $extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T002_Data_Recebimento='0000-00-00'"));
        // verificações
        $cClausula = <<<EOT
            SELECT T002_Numero_Duplicata,
                (sum(T002_Valor_Saldo)-(T002_Valor_Duplicata+T002_Valor_Juros-T002_Valor_Desconto)) Diferenca 
            FROM T002 
                LEFT JOIN D024 on D024_Id=T002_D024_Id 
                LEFT JOIN D027 on D027_Id=T002_D027_Id 
                LEFT JOIN T117 on T117_Id=T002_T117_Id 
                LEFT JOIN D014 on D014_Id=T002_D014_Id 
                LEFT JOIN T007 on T007_Id=T002_T007_Id 
                LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
            WHERE {$extra}
            #WHERE T002_Flag_Baixar_Lote='S' 
            #AND T002_Data_Recebimento='0000-00-00'
            #AND T002_C004_Id='{$g['empresaAtual']}' 
            GROUP BY T002_Id
EOT;
        $T002 = mysql_query($cClausula);
        $erro = mysql_error();
        
        if (!empty($erro)) {
            return "<div class='msgError'>Erro na verificacao de divergencias no recebimento! Contacte o Suporte Técnico.<br />Mysql error: <br />" . htmlspecialchars($erro, ENT_QUOTES) . "</div>";
        }
        
        if (empty($D007_Id)) {
            return "Selecione um banco";
        }
        
        $Mensagem = '';
        while ($mT002 = mysql_fetch_array($T002)) {
            if ($mT002['Diferenca'] > 0) {
                $Mensagem .= "- Titulo: {$mT002['T002_Numero_Duplicata']} com diferença de {$mT002['Diferenca']}<br />";
            }
        }
        
        /* VERIFICA O PORTADOR (COMO O PROCESSO É DE BAIXA AUTOMATICA, O PORTADOR UTILIZADO SERÁ O MESMO DO TITULO)
        $extra = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T002_Data_Recebimento='0000-00-00'"));	    
        $cClausula  = " SELECT T002_Numero_Duplicata, T002_D027_Id";
        $cClausula .= " FROM T002 ";
        $cClausula .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $cClausula .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $cClausula .= " LEFT JOIN T117 on T117_Id=T002_T117_Id ";
        $cClausula .= " LEFT JOIN D014 on D014_Id=T002_D014_Id ";
        $cClausula .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $cClausula .= " LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " WHERE {$extra}";
        //$cClausula .= " WHERE T002_Flag_Baixar_Lote='S' ";
        //$cClausula .= "   AND T002_Data_Recebimento='0000-00-00'";
        //$cClausula .= "   AND T002_C004_Id='{$g['empresaAtual']}' ";
        //$cClausula .= "   AND T103_D027_Id is null";
        $T002=mysql_query($cClausula);
        
        $erro=mysql_error();
        
        if (!empty($erro))
        return "<div class='msgError'>Erro na verificacao de divergencias no recebimento! Contacte o Suporte Técnico.<br />Mysql error: <br />".htmlspecialchars($erro,ENT_QUOTES)."</div>";
        while ($mT002=mysql_fetch_array($T002))
        {
        if ($mT002['T002_D027_Id']<=0)
        $Mensagem.="- Titulo: $mT002[T002_Numero_Duplicata] falta o Portador<br />";
        }	
        
        if (!empty($Mensagem))	    	
        return "<div class='msgError'><span class='msgErrorTitle'>Titulos com problemas:</span> $Mensagem </div>";  		
        */
        $extra     = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "T002_Data_Recebimento='0000-00-00'"));
        $cClausula = <<<EOT
            SELECT *
            FROM T002 
            LEFT JOIN D024 on D024_Id=T002_D024_Id 
            LEFT JOIN D027 on D027_Id=T002_D027_Id 
            LEFT JOIN T117 on T117_Id=T002_T117_Id 
            LEFT JOIN D014 on D014_Id=T002_D014_Id 
            LEFT JOIN T007 on T007_Id=T002_T007_Id 
            LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
            WHERE {$extra}
            #WHERE T002_Flag_Baixar_Lote='S' 
            #AND T002_Data_Recebimento='0000-00-00'
            #AND T002_C004_Id='{$g['empresaAtual']}' 
            #AND T103_Valor_Recebido>0 
            ORDER BY T002_Data_Vencimento,D024_Nome_Empresa
EOT;
        $T002 = mysql_query($cClausula);
        
        if (mysql_num_rows($T002) <= 0) {
            return "<div class='msgError'>Selecione um titulo para baixar</div>";
        }
        
        // baixa de títulos
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $nTotal_Baixas = 0;
        $cClausula = <<<EOT
            INSERT INTO T045 ( 
                T045_C004_Id, 
                T045_C007_Id, 
                T045_Data 
            ) values( 
                '{$g['empresaAtual']}', 
                '{$g['usuarioAtual']}', 
                now() 
            )
EOT;
        $cSql_T045 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
        }
        $T045_Id = $g['mysqlLastId'];
        
        $nT002_Id = 'xxxxxxx';
        
        while ($mT002 = mysql_fetch_array($T002)) {
            if ($nT002_Id != $mT002['T002_Id']) {
                $nTotal_Baixas++;
                $nT002_Id = $mT002['T002_Id'];
                
                $cClausula = " UPDATE T002 set ";
                $cClausula .= " T002_D007_Id='$D007_Id', ";
                $cClausula .= " T002_T045_Id='$T045_Id', ";
                $cClausula .= " T002_Flag_Baixar_Lote='N' ";
                $cClausula .= " WHERE T002_Id='$mT002[T002_Id]' ";
                
                $cSql_T045 = mysql_query($cClausula);
                $cErro     = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
                }
            }

            $this->insertAntesT019($mT002);
            
            $T002_Nome_Cliente = mysql_real_escape_string($mT002['T002_Nome_Cliente']);
            $cClausula = <<<EOT
                INSERT INTO T019 ( 
                    T019_T002_Id, 
                    T019_D007_Id, 
                    T019_D027_Id, 
                    T019_C004_Id, 
                    T019_D024_Id, 
                    T019_D014_Id, 
                    T019_Complemento, 
                    T019_Numero_Agencia, 
                    T019_Numero_Conta, 
                    T019_Numero_Cheque, 
                    T019_Data_Lancamento, 
                    T019_Referente, 
                    T019_Valor_Lancamento, 
                    T019_Flag_Deb_Crd 
                ) values ( 
                    '{$mT002['T002_Id']}', 
                    '{$D007_Id}', 
                    '{$mT002['T002_D027_Id']}', 
                    '{$mT002['T002_C004_Id']}', 
                    '{$mT002['T002_D024_Id']}', 
                    '{$mT002['T002_D014_Id']}', 
                    '{$T002_Nome_Cliente}', 
                    '{$mT002['T002_Numero_Agencia']}', 
                    '{$mT002['T002_Numero_Conta']}', 
                    '{$mT002['T002_Numero_Cheque']}', 
                    '{$dataBaixa}', 
                    'DUPLI . No. {$mT002['T002_Numero_Duplicata']}', 
                    '{$mT002['T002_Valor_Saldo']}', 
                    '1' 
                ) 
EOT;
            $cSql_T019 = mysqli_query($cClausula);
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
            }

            $this->lancarAcrescimoDesconto($mT002, $D007_Id, $dataBaixa, $dataRecebimento);

            mysql_query("CALL T002_Baixar_Titulo('$mT002[T002_Id]','$dataRecebimento')");
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
     * estornar
     * 
     * @param String $T002_Id
     * @param String $observacao
     * @return String/bool
     */
    public function estornar($T002_Id, $observacao, $dataEstorno = '0000-00-00')
    {
        global $g;
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

               
        $cClausula = " SELECT * FROM T002 WHERE T002_Id='$T002_Id' ";
        $cT002     = mysql_query($cClausula);
        $mT002     = mysql_fetch_array($cT002);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p$cClausula";
        }

        $mT002['T002_Valor_Recebido'] = $mT002['T002_Valor_Recebido'] * -1;
        $valorEstorno = $mT002['T002_Valor_Recebido'];

        $T019 = mysql_query("SELECT COUNT(DISTINCT T019_D007_Id) AS qtdeBancosDiferentes,
                                    T019_D007_Id
                               FROM T019
                              WHERE T019_T002_Id='{$T002_Id}' 
                           ORDER BY T019_Id DESC");
        $mT019 = mysql_fetch_array($T019);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p$cClausula";
        }

        if($mT019['qtdeBancosDiferentes'] > 1){
            return "<font color='red'><b>Atenção!</b> Esse título foi dado baixa em bancos diferentes, operação de estorno abortada</font>";
        }  
        // Verifica se a seleção da data de estorno está habilitada.
        if($g['C031']['HabilitarDataAtualEstornoReceber'] == 'S'){
            $dataEstorno = date('Y-m-d'); //Pega a data atual
        }

        $this->antesRealizarEstorno($mT002); 

        
            $cClausula = <<<EOT
                INSERT INTO T019 (
                    T019_T002_Id, 
                    T019_D007_Id, 
                    T019_C004_Id, 
                    T019_D024_Id, 
                    T019_D014_Id, 
                    T019_Complemento, 
                    T019_Numero_Agencia, 
                    T019_Numero_Conta, 
                    T019_Numero_Cheque, 
                    T019_Data_Lancamento, 
                    T019_Referente, 
                    T019_Valor_Lancamento, 
                    T019_Flag_Deb_Crd, 
                    T019_Obs_Estorno,
                    T019_D027_Id
                ) values ( 
                    '$T002_Id', 
                    '{$mT019['T019_D007_Id']}', 
                    '$mT002[T002_C004_Id]', 
                    '$mT002[T002_D024_Id]', 
                    '$mT002[T002_D014_Id]', 
                    '$mT002[T002_Nome_Cliente]', 
                    '$mT002[T002_Numero_Agencia]', 
                    '$mT002[T002_Numero_Conta]', 
                    '$mT002[T002_Numero_Cheque]', 
                    '{$dataEstorno}', 
                    'ESTORNO DUPLI . No. $mT002[T002_Numero_Duplicata]', 
                    '{$mT002['T002_Valor_Recebido']}', 
                    '0', 
                    '{$observacao}',
                    '{$D027_Id}'
                ) 
EOT;

        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p$cClausula";
        }


        $cClausula = " UPDATE T002 set T002_Data_Recebimento='0000-00-00' WHERE T002_Id='$T002_Id' ";
       
        
        
        $UPDATE = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p$cClausula";
        }

        $this->estornaAcrescimoDesconto($mT002,$mT019,$dataEstorno,$observacao);
        
        mysql_query("COMMIT");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro/p$cClausula";
        }
        
        $cClausula = "CALL T002_Gravar_Totalizacao('$T002_Id'); ";
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro/p$cClausula";
        }
        if ($g['C031']['baixaTitulos'] == 'S') {
            $this->estornoSmart($valorEstorno, $D027_Id);
        }
        
        return true;
    }
    
    /**
     * gerarRemessaBancaria
     * 
     * @param String $D007_Id
     * @return String/bool
     */
    public function gerarRemessaBancaria($D007_Id)
    {
        return 'A base local não registra boleto.';
        global $g;

        $where  = " T002_Flag_Enviar_Banco='S'";
        $where .= " AND T002_Flag_Cancelada!='S'";
        $where .= " AND IFNULL(T002_T035_Id,0)<=0";
        $where .= " AND IFNULL(T002_T002_Id_Agrupado,0)<=0";
        $where .= " AND T002_Data_Recebimento='0000-00-00'";
        $where .= " AND IFNULL(T002_T117_Id,0)>0";
        $where .= " AND IFNULL(T117_Id,0)>0";
        $where .= " AND T117_Flag_Cancelado!='S'";
        $where .= " AND T002_C004_Id='{$g['empresaAtual']}' ";

        //Verifica se os boletos estão com o valor preenchido na linha digitavel
        $sqlValidacao = mysql_query("SELECT * FROM T002 
						LEFT JOIN T117 ON T117_Id = T002_T117_Id 
						LEFT JOIN D024 ON D024_Id = T117_D024_Id 
						LEFT JOIN D020 ON D020_Id = D024_D020_Id 
						LEFT JOIN D018 ON D018_Id = D020_D018_Id 
						LEFT JOIN C004 ON C004_Id = T117_C004_Id 
						WHERE $where
						AND SUBSTRING(T117_Linha_Digitavel, -10) = '0000000000'");
        if (mysql_num_rows($sqlValidacao) > 0) {
            return "Há boletos com problemas na linha digitável, por favor verifique";
        }

        $cClausula  = " select ";
        $cClausula .= " T002_Numero_Duplicata, ";
        $cClausula .= " T002_T007_Id, ";
        $cClausula .= " D007_Flag_Ativo, ";
        $cClausula .= " T007_Id, ";
        $cClausula .= " T007_Numero_Protocolo_Nfe, ";
        $cClausula .= " T007_Numero_Nota_Fiscal, ";
        $cClausula .= " T007_Flag_ACP ";
        $cClausula .= " from T002 ";
        $cClausula .= " left join T117 on T117_Id=T002_T117_Id ";
        $cClausula .= " left join D024 on D024_Id=T002_D024_Id ";
        $cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
        $cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
        $cClausula .= " left join C004 on C004_Id=T002_C004_Id ";
        $cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
        $cClausula .= " left join T007 on T007_Id=T002_T007_Id ";
        $cClausula .= " where $where ";
        $cClausula .= " GROUP BY ifnull(T117_Id,T002_Id)";

        log("Sql gerar remessa: ".$cClausula);

        $T002_T007=mysql_query($cClausula);
        while($mT002_T007=mysql_fetch_array($T002_T007)){
            if($mT002_T007['D007_Flag_Ativo']!='S' && (!empty($mT002_T007['D007_Flag_Ativo']))){
                return "O título ".$mT002_T007['T002_Numero_Duplicata']." está com a conta inativa, favor verificar.";
            }
            if($mT002_T007['T002_T007_Id'] > 0 && $mT002_T007['T007_Id'] <= 0){
                return "A nota de ID: <b>".$mT002_T007['T002_T007_Id'] ."</b> vinculada ao título<b> ".$mT002_T007['T002_Numero_Duplicata']."</b> não existe. Favor verificar";
            }

            // Validação criada em 10/09/2019 chamado 38929
            // Criada validação pois não será possível gerar remessa de títulos nos quais a nota não foi enviada para a sefaz ainda
            if($mT002_T007['T002_T007_Id'] > 0 && empty($mT002_T007['T007_Numero_Protocolo_Nfe']) && ($mT002_T007['T007_Flag_ACP'] == 6 || $mT002_T007['T007_Flag_ACP'] == 8)){
                return "Não é possível gerar remessa dos títulos, a nota " .$mT002_T007['T007_Numero_Nota_Fiscal'] . " do título " . $mT002_T007['T002_Numero_Duplicata'] . " não possui protocolo de autorização.";
            }
        }

        //Verifica se os boletos pertencem ao mesmo banco da remessa
        $D007 = mysql_query("SELECT D007_Nome_Banco FROM D007 WHERE D007_Id='$D007_Id'");
        $mD007 = mysql_fetch_array($D007);
        $sqlValidacao = mysql_query("SELECT GROUP_CONCAT(T002_Numero_Duplicata ,' - ', D007_Nome_Banco) as duplicata
                                       FROM T002 
                                  LEFT JOIN T117 ON T117_Id = T002_T117_Id 
                                  LEFT JOIN D024 ON D024_Id = T117_D024_Id 
                                  LEFT JOIN D007 ON D007_Id = T117_D007_Id 
                                  LEFT JOIN D020 ON D020_Id = D024_D020_Id 
                                  LEFT JOIN D018 ON D018_Id = D020_D018_Id 
                                  LEFT JOIN C004 ON C004_Id = T117_C004_Id 
                                      WHERE $where
                                        AND T117_D007_Id <> '$D007_Id'");
        $sqlValidacaoBanco = mysql_fetch_array($sqlValidacao);
        if (!empty($sqlValidacaoBanco['duplicata'])) {
            return "Os boletos <strong>".$sqlValidacaoBanco['duplicata']. "</strong> pertencem a bancos diferentes do banco da remessa: <strong>". $mD007['D007_Nome_Banco']. "</strong>";
        }

        // Efetua Remessa
        if ($D007_Id <= 0) {
            return 'Selecione uma conta.';
        } else {
            $cSql_D007 = mysql_query("SELECT * 
                                        FROM D007 
                                   LEFT JOIN D062 ON D062_Id=D007_D062_Id 
                                       WHERE D007_Id='" . mysql_real_escape_string($D007_Id) . "';");
            $mD007     = mysql_fetch_array($cSql_D007);
            
            if ($mD007['D007_Flag_Layout'] != '1') {
                return 'Esta conta nao possui layout.';
                
            } else if ($mD007['D062_Id'] <= 0) {
                return 'Esta conta nao possui Banco.';
                
            } else if ($mD007['D007_Flag_Tipo_Registro'] == '1') {

                if($mD007['D062_Numero_Banco'] == '085' && $mD007['D007_Layout_Cobranca'] == '2'){
                    // Banco ailos foi feito no CNAB240 porém os nomes dos arquivos é o mesmo dos outros bancos que usam o CNAB400
                    // Os arquivos possuem o seguinte nome, diferenciando o nome com CNAB240 no final:
                    // bibliotecas/layouts_remessa/$mD007[D062_Numero_Banco]/remessa_$mD007[D062_Numero_Banco]_com_registro_CNAB240.php
                    // bibliotecas/layouts_remessa/$mD007[D062_Numero_Banco]/remessa_$mD007[D062_Numero_Banco]_com_registro.php
                    $mD007['D007_Layout_Cobranca'] = '1';
                }

                //-- PARA COBRANCAS COM BOLETO REGISTRADO
                if ($mD007['D062_Numero_Banco'] == '001' && strlen($mD007['D007_Numero_Convenio']) == 6) {
                    if (file_exists("bibliotecas/layouts_remessa/001/remessa_001_convenio_6.php")) {
                        $aux = include "bibliotecas/layouts_remessa/001/remessa_001_convenio_6.php";
                        return $aux;
                    } else {
                        return "Não existe layout de remessa (COM REGISTRO) desenvolvido para o banco: 001 - Banco do Brasil (Convenio de 6 dígitos)";
                    }
                } else {
                    if($mD007['D007_Layout_Cobranca'] == '2'){
                        if(file_exists("bibliotecas/layouts_remessa/$mD007[D062_Numero_Banco]/remessa_$mD007[D062_Numero_Banco]_com_registro_CNAB240.php")){
                            $aux = include "bibliotecas/layouts_remessa/$mD007[D062_Numero_Banco]/remessa_$mD007[D062_Numero_Banco]_com_registro_CNAB240.php";
                            return $aux;
                        }elseif(file_exists("bibliotecas/layouts_remessa/$mD007[D062_Numero_Banco]/remessa_$mD007[D062_Numero_Banco]_com_registro.php")){
                            return "O banco $mD007[D062_Nome_Banco] possue layout de remessa no modelo CNAB400, altere no cadastro do banco";
                        }else{
                            return "Não existe layout de remessa (COM REGISTRO) desenvolvido para o banco: $mD007[D062_Numero_Banco]-$mD007[D062_Nome_Banco]";
                        }
                    }elseif (file_exists("bibliotecas/layouts_remessa/$mD007[D062_Numero_Banco]/remessa_$mD007[D062_Numero_Banco]_com_registro.php")) {
                        $aux = include "bibliotecas/layouts_remessa/$mD007[D062_Numero_Banco]/remessa_$mD007[D062_Numero_Banco]_com_registro.php";
                        return $aux;
                    } else {
                        return "Não existe layout de remessa (COM REGISTRO) desenvolvido para o banco: $mD007[D062_Numero_Banco]-$mD007[D062_Nome_Banco]";
                    }
                }
                
            } else if ($mD007['D007_Flag_Tipo_Registro'] == '2') {
                //-- PARA COBRANCAS SEM REGISTRO
                return "Não existe layout de remessa (SEM REGISTRO) desenvolvido para o banco: {$mD007['D062_Numero_Banco']}-{$mD007['D062_Nome_Banco']}";
                
            } else if ($mD007['D007_Flag_Tipo_Registro'] == '3') {
                //-- PARA COBRANCAS COM DDA
                if (file_exists("bibliotecas/layouts_remessa/$mD007[D062_Numero_Banco]/remessa_$mD007[D062_Numero_Banco]_dda.php")) {
                    $aux = include "bibliotecas/layouts_remessa/$mD007[D062_Numero_Banco]/remessa_$mD007[D062_Numero_Banco]_dda.php";
                    if (!empty($aux))
                        return $aux;
                    else
                        return $lastId;
                } else {
                    return "Não existe layout de remessa (DDA) desenvolvido para o banco: $mD007[D062_Numero_Banco]-$mD007[D062_Nome_Banco]";     
                }
                //return "Não existe layout de remessa (DDA) desenvolvido para o banco: $mD007[D062_Numero_Banco]-$mD007[D062_Nome_Banco]";     
            } else {
                return "Não definido o tipo de registro na conta: $mD007[D007_Nome_Banco]";
                
            }
        }
        //return true;
    }
    
    /**
     * agrupar
     * 
     * @param String $T002_D027_Id
     * @param String $agruparNovoVencimento
     * @param String $extra
     * @return String/bool
     */
    public function agrupar($T002_D027_Id, $agruparNovoVencimento, $extra)
    {
        global $g;
        $extraOriginal = base64_decode($extra);
        $extra         = gPegarParteWhere(gInsertExtraWhere($extraOriginal, "(T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND ifnull(T002_T035_Id,0)<=0 AND T002_Flag_Cancelada<>'S' AND T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}'"));
        
        $agruparNovoVencimento = gCorrigeDataInverte($agruparNovoVencimento);
        $cClausula_T002  = <<<EOT
            SELECT * 
            FROM T002 
            LEFT JOIN D024 on D024_Id=T002_D024_Id 
            LEFT JOIN D027 on D027_Id=T002_D027_Id 
            LEFT JOIN T117 on T117_Id=T002_T117_Id 
            LEFT JOIN D014 on D014_Id=T002_D014_Id 
            LEFT JOIN D046 on D046_Id=D024_D046_Id 
            LEFT JOIN T007 on T007_Id=T002_T007_Id 
            LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
            LEFT JOIN T005 on T005_Id=T007_T005_Id
            #WHERE T002_C004_Id='{$g['empresaAtual']}' 
            #AND T002_Data_Recebimento='0000-00-00'
            #AND T002_Flag_Reparcelar='S'
            #AND T002_Flag_Cancelada<>'S'		
            #AND ifnull(T002_T035_Id,0)<=0
            #AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)
            WHERE {$extra}
            ORDER BY T002_Data_Vencimento 
EOT;
        $sql = mysql_query($cClausula_T002);
        
        $textGeradores = "";
        $idTitulosAgrupados = array();
        while ($row = mysql_fetch_assoc($sql)) {
            $textGeradores .= $row['T002_Id'] . ';' . $row['T002_Numero_Duplicata'] . ';' . $row['T002_Data_Emissao'] . ';' . $row['T002_Data_Vencimento'] . ';' . $row['T002_Valor_Duplicata'] . ';' . PHP_EOL;
            // Inserirndo Id's dos títulos a serem agrupados.
            array_push($idTitulosAgrupados, $row['T002_Id']);
            if($row['T002_T007_Id'] > 0  && empty($row['T007_Numero_Protocolo_Nfe']) && ($row['T007_Flag_ACP'] == 6 || $row['T007_Flag_ACP'] == 8)){
                return "Não é possível agrupar os títulos, a nota " .$row['T007_Numero_Nota_Fiscal'] . " não possui protocolo de autorização.";
            }
        }

        $dadosAutomacaoBoleto = array();

        // Bloco de código para buscar dados dos títulos a serem agrupados.
        $agrupadosD007 = array(); // Array para Id's dos bancos emissores dos boletos.
        $agrupadosDataVencimento = array(); // Array para vencimento dos boletos.

        foreach ($idTitulosAgrupados as $value) {
            $sqlTitulosAgrupados = mysql_query("SELECT T002_D024_Id, T117_Data_Vencimento, D007_Instrucoes_Boleto, D007_Id, D007_Percentual_Juros_Dia, D007_Percentual_Multa FROM T002 LEFT JOIN T117 ON T117_Id = T002_T117_Id AND T117_Flag_Cancelado = 'N' LEFT JOIN D007 ON D007_Id = T117_D007_Id WHERE T002_Id = $value");
            $rowTitulosAgrupados = mysql_fetch_assoc($sqlTitulosAgrupados);

            $dadosAutomacaoBoleto['D024_Id'] = $rowTitulosAgrupados['T002_D024_Id'];
            $dadosAutomacaoBoleto['Percentual_Juros_Dia'] = $rowTitulosAgrupados['D007_Percentual_Juros_Dia'];
            $dadosAutomacaoBoleto['Percentual_Multa'] = $rowTitulosAgrupados['D007_Percentual_Multa'];
            $dadosAutomacaoBoleto['Instrucoes_Boleto'] = $rowTitulosAgrupados['D007_Instrucoes_Boleto'];

            if ($rowTitulosAgrupados['T117_Data_Vencimento']) { // Array de vencimentos (datas).
                array_push($agrupadosDataVencimento, $rowTitulosAgrupados['T117_Data_Vencimento']);
            }

            if ($rowTitulosAgrupados['D007_Id']) { // Array de ID de bancos.
                array_push($agrupadosD007, $rowTitulosAgrupados['D007_Id']);
            }
        }

        // Bloco de código para tratar o vencimento do boleto resultante do agrupamento.
        $tmpDate = null;
      
        if ($g['C031']['vencimentoAgrupamentoBoleto'] == 1) { // Utilizar menor vencimento.
            foreach ($agrupadosDataVencimento as $date) {
                $curDate = $date;
                if ($curDate < $tmpDate or $tmpDate == null) {
                    $tmpDate = $curDate;
                }
            }
        } else { // Pegar a data do boleto com o vencimento mais tardar.
            foreach ($agrupadosDataVencimento as $date) {
                $curDate = $date;
                if ($curDate > $tmpDate or $tmpDate == null) {
                    $tmpDate = $curDate;
                }
            }
        }

        $dadosAutomacaoBoleto['Data_Vencimento'] = $tmpDate;

        $cSql_T002 = mysql_query($cClausula_T002);
        
        while ($mT002 = mysql_fetch_array($cSql_T002)) {
            if (!empty($mT002[T002_Numero_Duplicata])) {
                $mNumeroDuplicatas[] = $mT002[T002_Numero_Duplicata];
            }
            if (!empty($mT002[T002_Numero_Documento])) {
                $mNumeroDocumento[] = $mT002[T002_Numero_Documento];
            }
            // O novo título deve ter apenas os saldos pendentes.
            $nValor_Titulo += $mT002['T002_Valor_Saldo'];
            $nValor_Comissao += $mT002[T002_Valor_Comissao];
            $nValor_Titulo_Saldo += $mT002[T002_Valor_Saldo];
            $Data_Emissao = $mT002[T002_Data_Emissao];
            $nD024_Id     = $mT002[T002_D024_Id];
            $nC004_Id     = $mT002[T002_C004_Id];
            $nD014_Id     = $mT002[T002_D014_Id];
            $nD032_Id     = $mT002[T002_D032_Id];
            $nT007_Id     = $mT002[T002_T007_Id];
            $nD021_Id     = $mT002[T002_D021_Id];
            $nNomeEmpresa = $mT002[D024_Nome_Empresa];
        }
        
        $Numero_Duplicata = implode(",", $mNumeroDuplicatas);
        $Numero_Documento = implode(",", $mNumeroDocumento);
        
        mysql_query("START TRANSACTION");
        
        $cClausula = <<<EOT
            INSERT INTO T002 (
                T002_D024_Id, 
                T002_C004_Id, 
                T002_D027_Id, 
                T002_D014_Id, 
                T002_D032_Id, 
                T002_T007_Id,
                T002_D021_Id, 
                T002_Nome_Cliente, 
                T002_Numero_Duplicata, 
                T002_Numero_Documento, 
                T002_Data_Emissao, 
                T002_Data_Vencimento, 
                T002_Data_Recebimento, 
                T002_Valor_Duplicata, 
                T002_Valor_Saldo, 
                T002_Valor_Juros, 
                T002_Valor_Desconto, 
                T002_Valor_Total, 
                T002_Data_Lancamento, 
                T002_Hora_Lancamento, 
                T002_Numero_Parcela, 
                T002_Observacao_2, 
                T002_Valor_Comissao,
                T002_Flag_Agrupado_Reparcelado
            ) values ( 
                '$nD024_Id', 
                '$nC004_Id', 
                '$T002_D027_Id', 
                '$nD014_Id', 
                '$nD032_Id', 
                '$nT007_Id',
                '$nD021_Id', 
                '$nNomeEmpresa', 
                'AG $Numero_Duplicata', 
                '$Numero_Documento', 
                '$Data_Emissao', 
                '$agruparNovoVencimento', 
                '0000-00-00', 
                '$nValor_Titulo', 
                '$nValor_Titulo_Saldo', 
                '0', 
                '0', 
                '$nValor_Titulo', 
                current_date(), 
                current_time(), 
                '1', 
                '*** AGRUPAMENTO ***', 
                '$nValor_Comissao',
                'S'
            ) 
EOT;
        mysql_query($cClausula);
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p/pClausula: $cClausula";
        }
        
        $nT002_Id = $g['mysqlLastId'];
        
        $cSql_T002 = mysql_query($cClausula_T002);
        while ($mT002 = mysql_fetch_array($cSql_T002)) {
            $cClausula = " UPDATE T002 set  ";
            $cClausula .= " T002_Flag_Cancelada='S', ";
            $cClausula .= " T002_T002_Id_Agrupado='$nT002_Id', ";
            $cClausula .= " T002_Data_Recebimento=current_date(), ";
            $cClausula .= " T002_Observacao_2='*** AGRUPADO $Numero_Duplicata ***' ";
            $cClausula .= " WHERE T002_Id='$mT002[T002_Id]' ";
            mysql_query("$cClausula");
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/pClausula: $cClausula";
            }
        }
        
        $textGerados = "";
        $sql = mysql_query("SELECT * FROM T002 WHERE T002_Id='{$nT002_Id}'");
        while ($row = mysql_fetch_assoc($sql)) {
            $textGerados .= $row['T002_Id'] . ';' . $row['T002_Numero_Duplicata'] . ';' . $row['T002_Data_Emissao'] . ';' . $row['T002_Data_Vencimento'] . ';' . $row['T002_Valor_Duplicata'] . ';' . PHP_EOL;
            $T002_Id = $row['T002_Id'];
        }

        // insere registros na tabela de histórico de reparcelamento			
        mysql_query("INSERT INTO T166 
 								(T166_D024_Id,
								 T166_C004_Id,
								 T166_Data_Emissao,
								 T166_Valor_Total,
								 T166_Titulos_Geradores,								 
								 T166_Titulos_Gerados,
								 T166_C007_Id,
								 T166_Tipo)
						   values				 
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

        // Usado pelo formulário padrão para oferecer a abertura do título criado.
        // O retorno do método permanece true para manter compatibilidade.
        $g['FIN001TituloAgrupado'] = (int) $nT002_Id;
        
        // Bloco de código para geração automática do boleto de títulos agrupados.
        $D027_Id = (int) $T002_D027_Id;
        $sqlPortador = mysql_query("SELECT D027_Flag_Boleto FROM D027 WHERE D027_Id = {$D027_Id}");
        $mPortador = mysql_fetch_assoc($sqlPortador);
        if ($g['C031']['vencimentoAgrupamentoBoleto'] != 0 && !empty($mPortador['D027_Flag_Boleto']) && $mPortador['D027_Flag_Boleto'] == 'S') { //Se a opção "Não gerar boleto" não estiver selecionada e o portador emitir boleto.
            if (sizeof($agrupadosD007) == sizeof($idTitulosAgrupados)) {
                if (count(array_unique($agrupadosD007)) <= 1) {
                    $dadosAutomacaoBoleto['D007_Id'] = $agrupadosD007[0];
                    // Gerar boleto do agrupamento
                    require_once 'bibliotecas/classes/FIN005.php';
                    $FIN005 = new FIN005();

                    $FIN005->updateT002_Flag_Gerar_Boleto($T002_Id, 1);
                    $FIN005->gerarBoleto(null, $dadosAutomacaoBoleto);

                    echo gGeraAlertMsg('Boleto do agrupamento gerado!', 'Boleto do agrupamento de títulos gerado, automaticamente, com sucesso!');
                } else {
                    echo gGeraAlertMsg('ERRO!', '<font color="red">Boleto do agrupamento de títulos não gerado! Divergências entre os bancos dos títulos agrupados.</font>');
                }
            } else {
                echo gGeraAlertMsg('ERRO!', '<font color="red">Boleto do agrupamento de títulos não gerado! Um ou mais título(s) selecionado(s) não possui(em) boleto.</font>');
            }
        }

        return true;
    }
    
    /**
     * excluir
     * 
     * @param String $T002_Id
     * @return String/bool
     */
    public function excluir($T002_Id)
    {
        global $g;
        /**
         * 
         * 'T019_T002_Id', 'T020_T002_Id', 'T036_T002_Id', 'T038_T002_Id', 'T093_T002_Id', 'T097_T002_Id', 'T103_T002_Id'
         * 
         */
        
        /**
         * Faz a veificação de integridade referencial antes de cancelar
         */
        $T002       = <<<EOT
		SELECT T019_Id, T020_Id, T036_Id, T093_Id, T097_Id FROM T002 
		LEFT JOIN T019 ON T019_T002_Id=T002_Id
		LEFT JOIN T020 ON T020_T002_Id=T002_Id
		LEFT JOIN T036 ON T036_T002_Id=T002_Id
		LEFT JOIN T093 ON T093_T002_Id=T002_Id
		LEFT JOIN T097 ON T097_T002_Id=T002_Id
		WHERE (
		(T019_T002_Id IS NOT NULL OR T019_T002_Id = '' OR T019_T002_Id='0') OR 
		(T020_T002_Id IS NOT NULL OR T020_T002_Id = '' OR T019_T002_Id='0') OR 
		(T036_T002_Id IS NOT NULL OR T036_T002_Id = '' OR T019_T002_Id='0') OR
		(T093_T002_Id IS NOT NULL OR T093_T002_Id = '' OR T019_T002_Id='0') OR
		(T097_T002_Id IS NOT NULL OR T097_T002_Id = '' OR T019_T002_Id='0')
		) AND T002_Id='{$T002_Id}'
EOT;
        $resultT002 = mysql_query($T002);
        if (mysql_num_rows($resultT002) > 0) {
            $mResultT002 = mysql_fetch_array($resultT002);

            $mensagem = "Não foi possível cancelar título.";
            if($mResultT002['T019_Id'] != ''){
                $mensagem .= " Existem Recebimentos para esse título.";
            }
            if($mResultT002['T020_Id'] != ''){
                $mensagem .= " Existem Acrécimo/Desconto para esse título.";
            }
            if($mResultT002['T036_Id'] != ''){
                $mensagem .= " Existem itens nessa tabela, T036_id = {$mResultT002['T036_Id']}.";
            }
            if($mResultT002['T093_Id'] != ''){
                $mensagem .= " Existem itens nessa tabela, T093_id = {$mResultT002['T093_Id']}.";
            }
            if($mResultT002['T097_Id'] != ''){
                $mensagem .= " Existem itens nessa tabela, T097_id = {$mResultT002['T097_Id']}.";
            }
            
            return $mensagem;
        }
        
        $T002_Id = mysql_real_escape_string($T002_Id);
        $sql     = "UPDATE T002 set T002_Flag_Cancelada='S',T002_T002_Id_Agrupado=9999999999, T002_Data_Exclusao = CURDATE() WHERE T002_Id='{$T002_Id}'";
        mysql_query($sql);
        $sql = <<<EOT
			UPDATE T002 set 
				T002_T002_Id_Agrupado=NULL,
                T002_Flag_Cancelada='N',
                T002_Data_Recebimento='0000-00-00',
                T002_Flag_Reparcelar=''
                WHERE T002_T002_Id_Agrupado='{$T002_Id}'
EOT;
        mysql_query($sql);
        return true;
    }
    
    /**
     * excluirAcrescimoDesconto
     * 
     * @param String $T020_Id
     * @param String $T020_T002_Id
     * @return String/bool
     */
    public function excluirAcrescimoDesconto($T020_Id, $T020_T002_Id)
    {
        global $g;
        
        $T020_Id      = mysql_real_escape_string($T020_Id);
        $T020_T002_Id = mysql_real_escape_string($T020_T002_Id);
        $sql          = "DELETE FROM T020 WHERE T020_Id='{$T020_Id}'";
        mysql_query($sql);
        mysql_query("CALL T002_Gravar_Totalizacao('$T020_T002_Id')");
        return true;
    }
    
    /**
     * listaItensParaBaixar
     * 
     * @param bool $usarGroupBy
     * @param String $extra
     * @return array
     */
    public function listaItensParaBaixar($usarGroupBy = false, $extra)
    {
        global $g;

        $extra = base64_decode($extra);
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}'"));

        $sql = " SELECT ";
        $sql .= " T002_D027_Id, ";
        $sql .= " T002_Valor_Saldo, ";
        $sql .= " T002_Id ";
        $sql .= " FROM T002 ";
        $sql .= " LEFT JOIN T103 on T103_T002_Id=T002_Id ";
        $sql .= " LEFT JOIN D021 on T002_D021_Id=D021_Id ";
        $sql .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $sql .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $sql .= " LEFT JOIN T117 on T117_Id=T002_T117_Id ";
        $sql .= " LEFT JOIN D014 on D014_Id=T002_D014_Id ";
        $sql .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $sql .= " LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " LEFT JOIN T005 on T005_Id=T007_T005_Id ";
        $sql .= " WHERE {$extra};";
        //$sql .= " WHERE T002_Flag_Baixar_Lote='S' ";
        //$sql .= "   AND T002_Data_Recebimento='0000-00-00'";
        //$sql .= "   AND T002_C004_Id='{$g['empresaAtual']}' ";

        //if ($usarGroupBy) {
        //	$sql .= " GROUP BY T002_D027_Id";
        //}

        $dadosQuery = array('where' => "{$extra}");
        $retorno = $this->validacoesTravarT002($dadosQuery);

        if($retorno!==false){
            return $retorno;
        }

        $sqlReturn = mysql_query($sql);

        $erro = mysql_error();
        if(!empty($erro)){
            $erro = "ERRO MYSQL:".$erro;
            return $erro;
        }

        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado;
    }
    
    /**
     * listaItensParaBaixar
     * 
     * @param bool $usarGroupBy
     * @param String $extra
     * @return array
     */
    public function listaItensParaBaixaAgrupada($usarGroupBy = false, $extra) {
        global $g;

        // Realiza o agrupamento primeiro, para deixar chamar a baixa
        $retornoAgrupamento = $this->baixaAgrupada($extra);

        if ($retornoAgrupamento !== true) {
            return $retornoAgrupamento;
        }

        $extra = base64_decode($extra);
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}'"));

        $sqlBaixa  = " SELECT T002_D027_Id, ";
        $sqlBaixa .=        " T002_Valor_Saldo, ";
        $sqlBaixa .=        " T002_Id ";
        $sqlBaixa .=   " FROM T002 ";
        $sqlBaixa .=        " LEFT JOIN T103 ON T002_Id = T103_T002_Id ";
        $sqlBaixa .=        " LEFT JOIN D021 ON D021_Id = T002_D021_Id ";
        $sqlBaixa .=        " LEFT JOIN D024 ON D024_Id = T002_D024_Id ";
        $sqlBaixa .=        " LEFT JOIN D027 ON D027_Id = T002_D027_Id ";
        $sqlBaixa .=        " LEFT JOIN T117 ON T117_Id = T002_T117_Id ";
        $sqlBaixa .=        " LEFT JOIN D014 ON D014_Id = T002_D014_Id ";
        $sqlBaixa .=        " LEFT JOIN T007 ON T007_Id = T002_T007_Id ";
        $sqlBaixa .=        " LEFT JOIN C007 ON C007_Id = T007_C007_Id_Vendedor_Interno ";
        $sqlBaixa .=  " WHERE {$extra}; ";

        $dadosQuery = array('where' => "{$extra}");
        $retorno = $this->validacoesTravarT002($dadosQuery);

        if ($retorno !== false) {
            return $retorno;
        }

        $sqlReturn = mysql_query($sqlBaixa);

        $erro = mysql_error();
        if(!empty($erro)){
            $erro = "ERRO MYSQL:".$erro;
            return $erro;
        }

        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado;
    }
    
    /**
     * consultarSaldoCredito
     * 
     * @param String $extra
     * @return array
     */
    public function consultarSaldoCredito($extra) {
        global $g;

        if (empty($g['C031']['portadorCreditoBaixaAgrupada'])) {
            return "";
        }

        $retorno = '';
        $saldo   = '';

        $extraBaixa = base64_decode($extra);
        $extraBaixa = gPegarParteWhere(gInsertExtraWhere($extraBaixa, "T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}'"));

        $sqlBaixa  = " SELECT SUM(T002.T002_Valor_Saldo) AS T002_Valor_Saldo, ";
        $sqlBaixa .=        " T002.T002_D024_Id, ";
        $sqlBaixa .=        " D024.D024_Nome_Fantasia ";
        $sqlBaixa .=   " FROM T002 ";
        $sqlBaixa .=        " LEFT JOIN T103 ON T002.T002_Id = T103.T103_T002_Id ";
        $sqlBaixa .=        " LEFT JOIN D021 ON D021.D021_Id = T002.T002_D021_Id ";
        $sqlBaixa .=        " LEFT JOIN D024 ON D024.D024_Id = T002.T002_D024_Id ";
        $sqlBaixa .=        " LEFT JOIN D027 ON D027.D027_Id = T002.T002_D027_Id ";
        $sqlBaixa .=        " LEFT JOIN T117 ON T117.T117_Id = T002.T002_T117_Id ";
        $sqlBaixa .=        " LEFT JOIN D014 ON D014.D014_Id = T002.T002_D014_Id ";
        $sqlBaixa .=        " LEFT JOIN T007 ON T007.T007_Id = T002.T002_T007_Id ";
        $sqlBaixa .=        " LEFT JOIN C007 ON C007.C007_Id = T007.T007_C007_Id_Vendedor_Interno ";
        $sqlBaixa .=  " WHERE {$extraBaixa} ";
        $sqlBaixa .=  " GROUP BY T002.T002_D024_Id ";

        $queryBaixa = mysql_query($sqlBaixa);
        if (mysql_num_rows($queryBaixa) > 0) {
            while ($dadosBaixa = mysql_fetch_array($queryBaixa)) {
                $sqlSaldo  = " SELECT SUM( ";
                $sqlSaldo .=            " IF(D027.D027_Flag_Credito_Antecipado IN (1, 2), IF(T019_Valor_Lancamento < 0, T019_Valor_Lancamento * -1,T019_Valor_Lancamento) ,IF(D027.D027_Flag_Credito_Antecipado IN (3, 4), IF(T019_Valor_Lancamento < 0, T019_Valor_Lancamento,(T019_Valor_Lancamento * -1)), 0)) ";
                $sqlSaldo .=        " ) AS VALOR_SALDO ";
                $sqlSaldo .=   " FROM T019 ";
                $sqlSaldo .=        " LEFT JOIN T002 ON T002.T002_Id = T019.T019_T002_Id  ";
                $sqlSaldo .=        " LEFT JOIN D027 ON D027.D027_Id = T019.T019_D027_Id  ";
                $sqlSaldo .=        " LEFT JOIN T007 ON T007.T007_Id = T002.T002_T007_Id  ";
                $sqlSaldo .=        " LEFT JOIN D024 ON D024.D024_Id = T002.T002_D024_Id  ";
                $sqlSaldo .= " WHERE T002.T002_D024_Id = '{$dadosBaixa['T002_D024_Id']}' ";
                $sqlSaldo .=   " AND D027.D027_Flag_Credito_Antecipado IN (1, 2, 3, 4) ";
                $sqlSaldo .=   " AND T002.T002_Flag_Cancelada != 'S' ";
                $sqlSaldo .= " GROUP BY T002_D024_Id ";
                $querySaldo = mysql_query($sqlSaldo);
                $dadosSaldo = mysql_fetch_array($querySaldo);

                if ($dadosSaldo['VALOR_SALDO'] > 0) {
                    $saldo .= "<tr>";
                    $saldo .=   "<td style='text-align: left;'>{$dadosBaixa['D024_Nome_Fantasia']}</td>";
                    $saldo .=   "<td style='text-align: right;'>{$dadosSaldo['VALOR_SALDO']}</td>";
                    $saldo .=   "<td style='text-align: right;'>{$dadosBaixa['T002_Valor_Saldo']}</td>";
                    $saldo .= "</tr>";
                }
            }
        }

        if (!empty($saldo)) {
            $retorno  = "<table style='width: 100%'>";
            $retorno .=   "<tr>";
            $retorno .=     "<th style='text-align: left;'>Cliente</th>";
            $retorno .=     "<th style='text-align: right;'>Saldo Crédito</th>";
            $retorno .=     "<th style='text-align: right;'>Total Baixa</th>";
            $retorno .=   "</tr>";
            $retorno .=   $saldo;
            $retorno .= "<table>";
        }

        return $retorno;
    }

    /**
     * baixaAgrupada
     * 
     * @param bool $usarGroupBy
     * @param String $extra
     * @return array
     */
    public function baixaAgrupada($extra) {
        global $g;

        #region *** CARGA DOS DADOS
        $titulosAgrupados    = array();
        $textGerados         = "";
        $textGeradores       = "";
        $T002_Valor_Total    = 0;
        $T002_Valor_Comissao = 0;
        $T002_Valor_Saldo    = 0;
        $T002_Valor_Recebido = 0;
        $extraOriginal       = base64_decode($extra);
        $extraBaixa          = gPegarParteWhere(gInsertExtraWhere($extraOriginal, " T002_Flag_Baixar_Lote = 'S' AND T002_Data_Recebimento = '0000-00-00' AND T002_C004_Id = '{$g['empresaAtual']}' "));

        $sqlBaixa  = " SELECT * ";
        $sqlBaixa .=   " FROM T002 ";
        $sqlBaixa .=        " LEFT JOIN D024 ON D024_Id = T002_D024_Id ";
        $sqlBaixa .=        " LEFT JOIN D027 ON D027_Id = T002_D027_Id ";
        $sqlBaixa .=        " LEFT JOIN T117 ON T117_Id = T002_T117_Id ";
        $sqlBaixa .=        " LEFT JOIN D014 ON D014_Id = T002_D014_Id ";
        $sqlBaixa .=        " LEFT JOIN D046 ON D046_Id = D024_D046_Id ";
        $sqlBaixa .=        " LEFT JOIN T007 ON T007_Id = T002_T007_Id ";
        $sqlBaixa .=        " LEFT JOIN C007 ON C007_Id = T007_C007_Id_Vendedor_Interno ";
        $sqlBaixa .=  " WHERE {$extraBaixa} ";
        $sqlBaixa .=  " ORDER BY T002_Data_Vencimento ";
        $queryBaixa = mysql_query($sqlBaixa);

        while ($dadosTitulo = mysql_fetch_assoc($queryBaixa)) {
            array_push($titulosAgrupados, $dadosTitulo['T002_Id']);

            if (!empty($dadosTitulo['T002_Numero_Duplicata'])) {
                $listaNumeroDuplicata[] = $dadosTitulo['T002_Numero_Duplicata'];
            }
            if (!empty($dadosTitulo['T002_Numero_Documento'])) {
                $listaNumeroDocumento[] = $dadosTitulo['T002_Numero_Documento'];
            }

            $textGeradores       .= $dadosTitulo['T002_Id'] . ';' . $dadosTitulo['T002_Numero_Duplicata'] . ';' . $dadosTitulo['T002_Data_Emissao'] . ';' . $dadosTitulo['T002_Data_Vencimento'] . ';' . $dadosTitulo['T002_Valor_Duplicata'] . ';' . PHP_EOL;
            $T002_Valor_Total    += $dadosTitulo['T002_Valor_Total'];
            $T002_Valor_Comissao += $dadosTitulo['T002_Valor_Comissao'];
            $T002_Valor_Saldo    += $dadosTitulo['T002_Valor_Saldo'];
            $T002_Valor_Recebido += $dadosTitulo['T002_Valor_Recebido'];
            $T002_D024_Id         = $dadosTitulo['T002_D024_Id'];
            $T002_C004_Id         = $dadosTitulo['T002_C004_Id'];
            $T002_D014_Id         = $dadosTitulo['T002_D014_Id'];
            $T002_D032_Id         = $dadosTitulo['T002_D032_Id'];
            $T002_T007_Id         = $dadosTitulo['T002_T007_Id'];
            $T002_D021_Id         = $dadosTitulo['T002_D021_Id'];
            $T002_D027_Id         = $dadosTitulo['T002_D027_Id'];
            $T002_D007_Id         = $dadosTitulo['T002_D007_Id'];
            $T002_C004_Id         = $dadosTitulo['T002_C004_Id'];
            $T002_Numero_Agencia  = $dadosTitulo['T002_Numero_Agencia'];
            $T002_Numero_Conta    = $dadosTitulo['T002_Numero_Conta'];
            $T002_Numero_Cheque   = $dadosTitulo['T002_Numero_Cheque'];
            $nNomeEmpresa         = $dadosTitulo['D024_Nome_Empresa'];

            if ($dadosTitulo['T002_T007_Id'] > 0 && empty($dadosTitulo['T007_Numero_Protocolo_Nfe']) && ($dadosTitulo['T007_Flag_ACP'] == 6 || $dadosTitulo['T007_Flag_ACP'] == 8)) {
                return "Não é possível agrupar os títulos, a nota " .$dadosTitulo['T007_Numero_Nota_Fiscal'] . " não possui protocolo de autorização.";
            }
        }

        $T002_Numero_Duplicata = implode(",", $listaNumeroDuplicata);
        $T002_Numero_Documento = implode(",", $listaNumeroDocumento);
        #endregion CARGA DOS DADOS

        mysql_query("START TRANSACTION");

        #region *** INSERT DO NOVO TITULO
        $sqlInsert  = " INSERT INTO T002 ( ";
        $sqlInsert .=   " T002_D024_Id, ";
        $sqlInsert .=   " T002_C004_Id, ";
        $sqlInsert .=   " T002_D027_Id, ";
        $sqlInsert .=   " T002_D014_Id, ";
        $sqlInsert .=   " T002_D032_Id, ";
        $sqlInsert .=   " T002_T007_Id,";
        $sqlInsert .=   " T002_D021_Id, ";
        $sqlInsert .=   " T002_Nome_Cliente, ";
        $sqlInsert .=   " T002_Numero_Duplicata, ";
        $sqlInsert .=   " T002_Numero_Documento, ";
        $sqlInsert .=   " T002_Data_Emissao, ";
        $sqlInsert .=   " T002_Data_Vencimento, ";
        $sqlInsert .=   " T002_Data_Recebimento, ";
        $sqlInsert .=   " T002_Valor_Duplicata, ";
        $sqlInsert .=   " T002_Valor_Saldo, ";
        $sqlInsert .=   " T002_Valor_Recebido, ";
        $sqlInsert .=   " T002_Valor_Total, ";
        $sqlInsert .=   " T002_Valor_Comissao, ";
        $sqlInsert .=   " T002_Valor_Juros, ";
        $sqlInsert .=   " T002_Valor_Desconto, ";
        $sqlInsert .=   " T002_Data_Lancamento, ";
        $sqlInsert .=   " T002_Hora_Lancamento, ";
        $sqlInsert .=   " T002_Numero_Parcela, ";
        $sqlInsert .=   " T002_Observacao_2, ";
        $sqlInsert .=   " T002_Flag_Baixar_Lote, ";
        $sqlInsert .=   " T002_Flag_Agrupado_Reparcelado ";
        $sqlInsert .= " ) VALUES ( ";
        $sqlInsert .=   " '$T002_D024_Id', ";
        $sqlInsert .=   " '$T002_C004_Id', ";
        $sqlInsert .=   " '$T002_D027_Id', ";
        $sqlInsert .=   " '$T002_D014_Id', ";
        $sqlInsert .=   " '$T002_D032_Id', ";
        $sqlInsert .=   " '$T002_T007_Id',";
        $sqlInsert .=   " '$T002_D021_Id', ";
        $sqlInsert .=   " '$nNomeEmpresa', ";
        $sqlInsert .=   " 'AG $T002_Numero_Duplicata', ";
        $sqlInsert .=   " '$T002_Numero_Documento', ";
        $sqlInsert .=   " current_date(), ";
        $sqlInsert .=   " current_date(), ";
        $sqlInsert .=   " '0000-00-00', ";
        $sqlInsert .=   " '$T002_Valor_Total', ";
        $sqlInsert .=   " '$T002_Valor_Saldo', ";
        $sqlInsert .=   " '$T002_Valor_Recebido', ";
        $sqlInsert .=   " '$T002_Valor_Total', ";
        $sqlInsert .=   " '$T002_Valor_Comissao', ";
        $sqlInsert .=   " '0', ";
        $sqlInsert .=   " '0', ";
        $sqlInsert .=   " current_date(), ";
        $sqlInsert .=   " current_time(), ";
        $sqlInsert .=   " '1', ";
        $sqlInsert .=   " '*** AGRUPAMENTO ***', ";
        $sqlInsert .=   " 'S', ";
        $sqlInsert .=   " 'S' ";
        $sqlInsert .= " ) ";

        mysql_query($sqlInsert);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p/pClausula: $sqlInsert";
        }

        $T002_Id = $g['mysqlLastId'];

        $sql = mysql_query("SELECT * FROM T002 WHERE T002_Id='{$T002_Id}'");
        while ($row = mysql_fetch_assoc($sql)) {
            $textGerados .= $row['T002_Id'] . ';' . $row['T002_Numero_Duplicata'] . ';' . $row['T002_Data_Emissao'] . ';' . $row['T002_Data_Vencimento'] . ';' . $row['T002_Valor_Duplicata'] . ';' . PHP_EOL;
        }

        if ($T002_Valor_Recebido > 0) {
            $D007_Id = !empty($g['C031']['caixaPadraoBaixaAgrupada']) ? $g['C031']['caixaPadraoBaixaAgrupada'] : $T002_D007_Id;

            $sqlParcela  = " INSERT INTO T019 ( ";
            $sqlParcela .=   " T019_T002_Id, ";
            $sqlParcela .=   " T019_D007_Id, ";
            $sqlParcela .=   " T019_D027_Id, ";
            $sqlParcela .=   " T019_C004_Id, ";
            $sqlParcela .=   " T019_D024_Id, ";
            $sqlParcela .=   " T019_D014_Id, ";
            $sqlParcela .=   " T019_Complemento, ";
            $sqlParcela .=   " T019_Numero_Agencia, ";
            $sqlParcela .=   " T019_Numero_Conta, ";
            $sqlParcela .=   " T019_Numero_Cheque, ";
            $sqlParcela .=   " T019_Data_Lancamento, ";
            $sqlParcela .=   " T019_Referente, ";
            $sqlParcela .=   " T019_Valor_Lancamento, ";
            $sqlParcela .=   " T019_Flag_Deb_Crd ";
            $sqlParcela .= " ) VALUES ( ";
            $sqlParcela .=   " '$T002_Id', ";
            $sqlParcela .=   " '$D007_Id', ";
            $sqlParcela .=   " '$T002_D027_Id', ";
            $sqlParcela .=   " '$T002_C004_Id', ";
            $sqlParcela .=   " '$T002_D024_Id', ";
            $sqlParcela .=   " '$T002_D014_Id', ";
            $sqlParcela .=   " '$nNomeEmpresa', ";
            $sqlParcela .=   " '$T002_Numero_Agencia', ";
            $sqlParcela .=   " '$T002_Numero_Conta', ";
            $sqlParcela .=   " '$T002_Numero_Cheque', ";
            $sqlParcela .=   " current_date(), ";
            $sqlParcela .=   " 'DUPLI . No. {$T002_Numero_Duplicata}', ";
            $sqlParcela .=   " '$T002_Valor_Recebido', ";
            $sqlParcela .=   " '1' ";
            $sqlParcela .= " ) ";

            mysql_query($sqlParcela);

            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "<div class='msgError'>Erro: $cErro<br />Clausula: $sqlParcela</div>";
            }

            mysql_query("CALL T002_Baixar_Titulo('$T002_Id', current_date())");
        }
        #endregion *** INSERT DO NOVO TITULO

        #region *** UPDATE DOS TITULOS ANTIGOS
        foreach ($titulosAgrupados as $titulo) {
            $sqlUpdate  = " UPDATE T002 SET ";
            $sqlUpdate .=   " T002_Flag_Cancelada   = 'S', ";
            $sqlUpdate .=   " T002_T002_Id_Agrupado = '$T002_Id', ";
            $sqlUpdate .=   " T002_Data_Recebimento = current_date(), ";
            $sqlUpdate .=   " T002_Observacao_2     = '*** AGRUPADO $T002_Numero_Duplicata ***' ";
            $sqlUpdate .= " WHERE T002_Id = '$titulo' ";
            mysql_query($sqlUpdate);

            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/pClausula: $sqlUpdate";
            }
        }
        #region *** UPDATE DOS TITULOS ANTIGOS

        #region *** INSERT HISTORICO DE REPARCELAMENTO
        $sqlReparcelamento  = " INSERT INTO T166 ( ";
        $sqlReparcelamento .=   " T166_D024_Id, ";
        $sqlReparcelamento .=   " T166_C004_Id, ";
        $sqlReparcelamento .=   " T166_Data_Emissao, ";
        $sqlReparcelamento .=   " T166_Valor_Total, ";
        $sqlReparcelamento .=   " T166_Titulos_Geradores, ";
        $sqlReparcelamento .=   " T166_Titulos_Gerados, ";
        $sqlReparcelamento .=   " T166_C007_Id, ";
        $sqlReparcelamento .=   " T166_Tipo ";
        $sqlReparcelamento .= " ) VALUES ( ";
        $sqlReparcelamento .=   " '$T002_D024_Id', ";
        $sqlReparcelamento .=   " '$T002_C004_Id', ";
        $sqlReparcelamento .=   " curdate(), ";
        $sqlReparcelamento .=   " '{$T002_Valor_Total}', ";
        $sqlReparcelamento .=   " '{$textGeradores}', ";
        $sqlReparcelamento .=   " '{$textGerados}', ";
        $sqlReparcelamento .=   " '{$g['usuarioAtual']}', ";
        $sqlReparcelamento .=   " 'A' ";
        $sqlReparcelamento .= " ) ";
        mysql_query($sqlReparcelamento);

        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro 2: $cErro/p/pClausula: $sqlReparcelamento";
        }
        #region *** INSERT HISTORICO DE REPARCELAMENTO

        mysql_query("COMMIT");

        return true;
    }

    /**
     * baixarSaldoCredito
     * 
     * @param String $extra
     * @return String 'Mensagem de erro' | '0' - Nenhuma baixa com saldo | '1' - Baixa parcial com saldo | '2' - Baixa total com o saldo
     */
    public function baixarSaldoCredito($extra) {
        global $g;

        $mensagemSaldo = "0"; //Nenhuma baixa com saldo

        if (empty($g['C031']['portadorCreditoBaixaAgrupada'])) {
            return "";
        }

        $sqlUsuario  = " SELECT C007.C007_D007_Id ";
        $sqlUsuario .=   " FROM C007 ";
        $sqlUsuario .= " WHERE C007.C007_Id = '{$g['usuarioAtual']}' ";
        $queryUsuario = mysql_query($sqlUsuario);
        $dadosUsuario = mysql_fetch_array($queryUsuario);

        $portadorBaixa    = $g['C031']['portadorCreditoBaixaAgrupada'];
        $bancoBaixa       = $dadosUsuario['C007_D007_Id'];

        if (empty($bancoBaixa)) {
            return "<div class='msgError'><h3 style='color: red;'>Erro:</h3><p>O processo de baixa com crédito está ativo e o usuário não possui um banco padrão configurado.</p>Não é possível realizar a baixa dos títulos.</div>";
        }

        $extraBaixa = base64_decode($extra);
        $extraBaixa = gPegarParteWhere(gInsertExtraWhere($extraBaixa, "T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}'"));

        $sqlBaixa  = " SELECT T002.T002_Id, ";
        $sqlBaixa .=        " T002.T002_Valor_Saldo, ";
        $sqlBaixa .=        " T002.T002_Numero_Agencia, ";
        $sqlBaixa .=        " T002.T002_Numero_Conta, ";
        $sqlBaixa .=        " T002.T002_Numero_Cheque, ";
        $sqlBaixa .=        " T002.T002_Numero_Duplicata, ";
        $sqlBaixa .=        " T002.T002_D014_Id, ";
        $sqlBaixa .=        " T002.T002_D024_Id, ";
        $sqlBaixa .=        " T103.T103_Id, ";
        $sqlBaixa .=        " D024.D024_Nome_Fantasia ";
        $sqlBaixa .=   " FROM T002 ";
        $sqlBaixa .=        " LEFT JOIN T103 ON T002.T002_Id = T103.T103_T002_Id ";
        $sqlBaixa .=        " LEFT JOIN D021 ON D021.D021_Id = T002.T002_D021_Id ";
        $sqlBaixa .=        " LEFT JOIN D024 ON D024.D024_Id = T002.T002_D024_Id ";
        $sqlBaixa .=        " LEFT JOIN D027 ON D027.D027_Id = T002.T002_D027_Id ";
        $sqlBaixa .=        " LEFT JOIN T117 ON T117.T117_Id = T002.T002_T117_Id ";
        $sqlBaixa .=        " LEFT JOIN D014 ON D014.D014_Id = T002.T002_D014_Id ";
        $sqlBaixa .=        " LEFT JOIN T007 ON T007.T007_Id = T002.T002_T007_Id ";
        $sqlBaixa .=        " LEFT JOIN C007 ON C007.C007_Id = T007.T007_C007_Id_Vendedor_Interno ";
        $sqlBaixa .=  " WHERE {$extraBaixa} ";
        $sqlBaixa .=  " ORDER BY T002.T002_D024_Id ASC ";

        $queryBaixa = mysql_query($sqlBaixa);
        if (mysql_num_rows($queryBaixa) > 0) {
            while ($dadosBaixa = mysql_fetch_array($queryBaixa)) {
                $sqlSaldo  = " SELECT SUM( ";
                $sqlSaldo .=            " IF(D027.D027_Flag_Credito_Antecipado IN (1, 2), IF(T019_Valor_Lancamento < 0, T019_Valor_Lancamento * -1,T019_Valor_Lancamento) ,IF(D027.D027_Flag_Credito_Antecipado IN (3, 4), IF(T019_Valor_Lancamento < 0, T019_Valor_Lancamento,(T019_Valor_Lancamento * -1)), 0)) ";
                $sqlSaldo .=        " ) AS VALOR_SALDO ";
                $sqlSaldo .=   " FROM T019 ";
                $sqlSaldo .=        " LEFT JOIN T002 ON T002.T002_Id = T019.T019_T002_Id  ";
                $sqlSaldo .=        " LEFT JOIN D027 ON D027.D027_Id = T019.T019_D027_Id  ";
                $sqlSaldo .=        " LEFT JOIN T007 ON T007.T007_Id = T002.T002_T007_Id  ";
                $sqlSaldo .=        " LEFT JOIN D024 ON D024.D024_Id = T002.T002_D024_Id  ";
                $sqlSaldo .= " WHERE T002.T002_D024_Id = '{$dadosBaixa['T002_D024_Id']}' ";
                $sqlSaldo .=   " AND D027.D027_Flag_Credito_Antecipado IN (1, 2, 3, 4) ";
                $sqlSaldo .=   " AND T002.T002_Flag_Cancelada != 'S' ";
                $sqlSaldo .= " GROUP BY T002_D024_Id ";
                $querySaldo = mysql_query($sqlSaldo);
                $dadosSaldo = mysql_fetch_array($querySaldo);

                //region Caso o cliente tenha saldo, abate saldo no titulo
                $saldo = 0;
                if ($dadosSaldo['VALOR_SALDO'] > 0) {
                    $saldoCredito = $dadosSaldo['VALOR_SALDO'];

                    if ($saldoCredito >= $dadosBaixa['T002_Valor_Saldo']) {
                        $valorPagamento = $dadosBaixa['T002_Valor_Saldo'];
                        $flagBaixaLote  = 'N';
                    } else {
                        $valorPagamento = $saldoCredito;
                        $flagBaixaLote  = 'S';
                        $saldo          = $dadosBaixa['T002_Valor_Saldo'] - $valorPagamento;
                    }

                    mysql_query("SET AUTOCOMMIT=0");
                    mysql_query("START TRANSACTION");

                    //region Grava o log do lote
                    $sqlLote  = " INSERT INTO T045 ( ";
                    $sqlLote .=   " T045_C004_Id, ";
                    $sqlLote .=   " T045_C007_Id, ";
                    $sqlLote .=   " T045_Data ";
                    $sqlLote .= ") VALUES ( ";
                    $sqlLote .=   " '{$g['empresaAtual']}', ";
                    $sqlLote .=   " '{$g['usuarioAtual']}', ";
                    $sqlLote .=   " CURRENT_TIMESTAMP() ";
                    $sqlLote .= ")";

                    mysql_query($sqlLote);
                    $erroLote  = mysql_error();
                    if (!empty($erroLote)) {
                        mysql_query("ROLLBACK");
                        return "<div class='msgError'>Erro: $erroLote<br />Clausula: $sqlLote</div>";
                    }
                    $T045_Id = $g['mysqlLastId'];
                    //endregion

                    //region Atualiza o titulo com o id do log e a flag de baixa em lote
                    $sqlTitulo  = " UPDATE T002 SET ";
                    $sqlTitulo .=   " T002_D007_Id          = '{$bancoBaixa}', ";
                    $sqlTitulo .=   " T002_T045_Id          = '{$T045_Id}', ";
                    $sqlTitulo .=   " T002_Flag_Baixar_Lote = '{$flagBaixaLote}' ";
                    $sqlTitulo .= " WHERE T002_Id = '{$dadosBaixa['T002_Id']}' ";

                    mysql_query($sqlTitulo);
                    $erroTitulo  = mysql_error();
                    if (!empty($erroTitulo)) {
                        mysql_query("ROLLBACK");
                        return "<div class='msgError'>Erro: $erroTitulo<br />Clausula: $sqlTitulo</div>";
                    }
                    //endregion

                    //region Realiza o pagamento com o crédito
                    $nomeCliente = mysql_real_escape_string($dadosBaixa['D024_Nome_Fantasia']);

                    $sqlPagamento  = " INSERT INTO T019 ( ";
                    $sqlPagamento .=   " T019_T002_Id, ";
                    $sqlPagamento .=   " T019_D007_Id, ";
                    $sqlPagamento .=   " T019_D027_Id, ";
                    $sqlPagamento .=   " T019_C004_Id, ";
                    $sqlPagamento .=   " T019_D024_Id, ";
                    $sqlPagamento .=   " T019_D014_Id, ";
                    $sqlPagamento .=   " T019_Complemento, ";
                    $sqlPagamento .=   " T019_Numero_Agencia, ";
                    $sqlPagamento .=   " T019_Numero_Conta, ";
                    $sqlPagamento .=   " T019_Numero_Cheque, ";
                    $sqlPagamento .=   " T019_Data_Lancamento, ";
                    $sqlPagamento .=   " T019_Referente, ";
                    $sqlPagamento .=   " T019_Valor_Lancamento, ";
                    $sqlPagamento .=   " T019_Flag_Deb_Crd ";
                    $sqlPagamento .= " ) VALUES ( ";
                    $sqlPagamento .= "   '{$dadosBaixa['T002_Id']}', ";
                    $sqlPagamento .= "   '{$bancoBaixa}', ";
                    $sqlPagamento .= "   '{$portadorBaixa}', ";
                    $sqlPagamento .= "   '{$dadosBaixa['T002_C004_Id']}', ";
                    $sqlPagamento .= "   '{$dadosBaixa['T002_D024_Id']}', ";
                    $sqlPagamento .= "   '{$dadosBaixa['T002_D014_Id']}', ";
                    $sqlPagamento .= "   '{$nomeCliente}', ";
                    $sqlPagamento .= "   '{$dadosBaixa['T002_Numero_Agencia']}', ";
                    $sqlPagamento .= "   '{$dadosBaixa['T002_Numero_Conta']}', ";
                    $sqlPagamento .= "   '{$dadosBaixa['T002_Numero_Cheque']}', ";
                    $sqlPagamento .= "   CURRENT_DATE(), ";
                    $sqlPagamento .= "   'BAIXA COM SALDO DE CRÉDITO - DUPLI . No. {$dadosBaixa['T002_Numero_Duplicata']}', ";
                    $sqlPagamento .= "   '{$valorPagamento}', ";
                    $sqlPagamento .= "   '1' ";
                    $sqlPagamento .= " ) ";

                    mysql_query($sqlPagamento);
                    $erroPagamento  = mysql_error();
                    if (!empty($erroPagamento)) {
                        mysql_query("ROLLBACK");
                        return "<div class='msgError'>Erro: $erroPagamento<br />Clausula: $sqlPagamento</div>";
                    }

                    if ($saldo > 0) {
                        //region Atualiza o recebimento com o saldo a pagar
                        $sqlRecebido  = " UPDATE T103 SET ";
                        $sqlRecebido .=   " T103_Valor_Recebido       = '{$saldo}', ";
                        $sqlRecebido .=   " T103_Valor_Total_Recebido = '{$saldo}' ";
                        $sqlRecebido .= " WHERE T103_Id = '{$dadosBaixa['T103_Id']}' ";

                        mysql_query($sqlRecebido);
                        $erroRecebido  = mysql_error();
                        if (!empty($erroRecebido)) {
                            mysql_query("ROLLBACK");
                            return "<div class='msgError'>Erro: $erroRecebido<br />Clausula: $sqlRecebido</div>";
                        }
                        //endregion
                    }

                    $sqlProcedure = " CALL T002_Baixar_Titulo('{$dadosBaixa['T002_Id']}', CURRENT_DATE()) ";
                    mysql_query($sqlProcedure);
                    $erroProcedure = mysql_error();
                    if (!empty($erroProcedure)) {
                        mysql_query("ROLLBACK");
                        return "<div class='msgError'>Erro: $erroProcedure<br />Clausula: $sqlProcedure</div>";
                    }
                    //endregion

                    mysql_query("COMMIT");

                    $mensagemSaldo = '1'; //Baixa parical com saldo
                }
                //endregion
            }

            $sqlBaixaFinal  = " SELECT T002.T002_Id, ";
            $sqlBaixaFinal .=        " T002.T002_Valor_Saldo, ";
            $sqlBaixaFinal .=        " T002.T002_Numero_Agencia, ";
            $sqlBaixaFinal .=        " T002.T002_Numero_Conta, ";
            $sqlBaixaFinal .=        " T002.T002_Numero_Cheque, ";
            $sqlBaixaFinal .=        " T002.T002_Numero_Duplicata, ";
            $sqlBaixaFinal .=        " T002.T002_D014_Id, ";
            $sqlBaixaFinal .=        " T002.T002_D024_Id, ";
            $sqlBaixaFinal .=        " D024.D024_Nome_Fantasia ";
            $sqlBaixaFinal .=   " FROM T002 ";
            $sqlBaixaFinal .=        " LEFT JOIN T103 ON T002.T002_Id = T103.T103_T002_Id ";
            $sqlBaixaFinal .=        " LEFT JOIN D021 ON D021.D021_Id = T002.T002_D021_Id ";
            $sqlBaixaFinal .=        " LEFT JOIN D024 ON D024.D024_Id = T002.T002_D024_Id ";
            $sqlBaixaFinal .=        " LEFT JOIN D027 ON D027.D027_Id = T002.T002_D027_Id ";
            $sqlBaixaFinal .=        " LEFT JOIN T117 ON T117.T117_Id = T002.T002_T117_Id ";
            $sqlBaixaFinal .=        " LEFT JOIN D014 ON D014.D014_Id = T002.T002_D014_Id ";
            $sqlBaixaFinal .=        " LEFT JOIN T007 ON T007.T007_Id = T002.T002_T007_Id ";
            $sqlBaixaFinal .=        " LEFT JOIN C007 ON C007.C007_Id = T007.T007_C007_Id_Vendedor_Interno ";
            $sqlBaixaFinal .=  " WHERE {$extraBaixa} ";
            $sqlBaixaFinal .=  " ORDER BY T002.T002_D024_Id ASC ";

            $queryBaixaFinal = mysql_query($sqlBaixaFinal);

            if (mysql_num_rows($queryBaixaFinal) <= 0) {
                $mensagemSaldo = '2'; //Baixa total com saldo
            }
        }

        return $mensagemSaldo;
    }
    
    /**
     * listaItensParaRemessa
     * 
     * @param bool $usarGroupBy
     * @return array
     */
    public function listaItensParaRemessa($usarGroupBy = false)
    {
        global $g;

        $where  = " T002_Flag_Enviar_Banco='S'";
        $where .= " AND T002_Flag_Cancelada!='S'";
        $where .= " AND IFNULL(T002_T035_Id,0)<=0";
        $where .= " AND IFNULL(T002_T002_Id_Agrupado,0)<=0";
        $where .= " AND T002_Data_Recebimento='0000-00-00'";
        $where .= " AND IFNULL(T002_T117_Id,0)>0";
        $where .= " AND IFNULL(T117_Id,0)>0";
        $where .= " AND T117_Flag_Cancelado!='S'";
        $where .= " AND T002_C004_Id='{$g['empresaAtual']}' ";

        $sql = " SELECT ";
        $sql .= " T002_Id ";
        $sql .= " FROM T002 ";
        $sql .= " LEFT JOIN T103 on T103_T002_Id=T002_Id ";
        $sql .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $sql .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $sql .= " LEFT JOIN T117 on T117_Id=T002_T117_Id ";
        $sql .= " LEFT JOIN D014 on D014_Id=T002_D014_Id ";
        $sql .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $sql .= " LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE " . $where;

        $dadosQuery = array('where' => $where);
        $retorno = $this->validacoesTravarT002($dadosQuery);

        if($retorno!==false){
            return $retorno;
        }

        if ($usarGroupBy) {
            $sql .= " GROUP BY T002_D027_Id";
        }
        log("SQL: ".$sql);
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
     * @return array
     */
    public function listaItensParaAgrupar($usarGroupBy = false, $extra)
    {
        global $g;
        $extra = base64_decode($extra);
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T002_Data_Recebimento='0000-00-00' AND T002_Flag_Reparcelar='S' AND ifnull(T002_T035_Id,0)<=0 AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND T002_C004_Id='{$g['empresaAtual']}'"));
        $sql   = " SELECT ";
        $sql .= " * ";
        $sql .= " FROM T002 ";
        $sql .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $sql .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $sql .= " LEFT JOIN T117 on T117_Id=T002_T117_Id ";
        $sql .= " LEFT JOIN D014 on D014_Id=T002_D014_Id ";
        $sql .= " LEFT JOIN D046 on D046_Id=D024_D046_Id ";
        $sql .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $sql .= " LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " LEFT JOIN T005 on T005_Id=T007_T005_Id ";
        $sql .= " WHERE {$extra}";
        //$sql .= " WHERE T002_C004_Id='{$g['empresaAtual']}'";
        //$sql .= "   AND T002_Data_Recebimento='0000-00-00'";
        //$sql .= "   AND T002_Flag_Reparcelar='S'";
        //$sql .= "   AND ifnull(T002_T035_Id,0)<=0";
        //$sql .= "   AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";

        $dadosQuery = array('where' => "{$extra}");
        $retorno = $this->validacoesTravarT002($dadosQuery);

        if($retorno!==false){
            return $retorno;
        }

        if ($usarGroupBy) {
            $sql .= " GROUP BY SUBSTRING(D024_Cnpj, 1, 10)";
        }
        // 
        $sqlReturn = mysql_query($sql);
        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado;
    }
    
    /**
     * listaItensParaReparcelar
     * 
     * @param bool $usarGroupBy
     * @param String $extra     
     * @return array
     */
    public function listaItensParaReparcelar($usarGroupBy = false, $extra)
    {
        global $g;
        $extra = base64_decode($extra);
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T002_Data_Recebimento='0000-00-00' AND T002_Flag_Reparcelar='S' AND ifnull(T002_T035_Id,0)<=0 AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND T002_C004_Id='{$g['empresaAtual']}'"));
        $sql   = " SELECT ";
        $sql .= " T002_D024_Id,D024_Nome_Empresa ";
        $sql .= " FROM T002 ";
        //$sql .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        
        $sql .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $sql .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $sql .= " LEFT JOIN T117 on T117_Id=T002_T117_Id ";
        $sql .= " LEFT JOIN D014 on D014_Id=T002_D014_Id ";
        $sql .= " LEFT JOIN D046 on D046_Id=D024_D046_Id ";
        $sql .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $sql .= " LEFT JOIN T035 on T035_Id=T002_T035_Id ";
        $sql .= " LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " LEFT JOIN T103 on T002_Id=T103_T002_Id ";
        $sql .= " LEFT JOIN T005 on T005_Id=T007_T005_Id ";        
        $sql .= " WHERE {$extra}";
        //$sql .= " WHERE T002_C004_Id='{$g['empresaAtual']}'";
        //$sql .= "   AND T002_Data_Recebimento='0000-00-00'";
        //$sql .= "   AND T002_Flag_Reparcelar='S'";
        //$sql .= "   AND ifnull(T002_T035_Id,0)<=0 ";
        //$sql .= "   AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";

        $dadosQuery = array('where' => "{$extra}");
        $retorno = $this->validacoesTravarT002($dadosQuery);

        if($retorno!==false){
            return $retorno;
        }

        if ($usarGroupBy) {
            $sql .= " GROUP BY T002_D024_Id";
        }
        log("SQL: ", $sql);
        $sqlReturn = mysql_query($sql);
        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        return $resultado;
    }

    /**
     * updateT002_Flag_Enviar_Banco
     * 
     * @param String $T002_Id
     * @param String $selected
     * @return String/bool
     */
    public function updateT002_Flag_Enviar_Banco($T002_Id, $selected)
    {
        global $g;
        
        $T002_Id  = mysql_real_escape_string($T002_Id);
        $selected = mysql_real_escape_string($selected);

        if($select === 'N'){
            mysqli_query("UPDATE T002 set T002_Flag_Enviar_Banco='$selected' WHERE T002_Id='$T002_Id'");
            return true;
        }else{        
            //Verificação de vencimento, caso o vencimento seja superior a 2 dias da emissão da remessa, poderá ser enviado ao banco
            $sqlT002  = mysql_query("SELECT T002_T117_Id,T002_Id,D027_Flag_Tipo FROM T002 LEFT JOIN D027 ON D027_Id=T002_D027_Id WHERE T002_Id = '{$T002_Id}'");
            $T002     = mysql_fetch_array($sqlT002);
            
            $T117 = mysql_query("SELECT * FROM T117 WHERE T117_Id = '{$T002['T002_T117_Id']}' AND T117_Data_Vencimento < CURDATE() + INTERVAL 1 DAY");
            $mT117 = mysql_fetch_array($T117);
            // Se a data de vencimento estiver ERRADA, manda mensagem de erro
            if (mysql_num_rows($T117) > 0) {
                return "O boleto desse título tem vencimento no dia <b>".gCorrigeData($mT117['T117_Data_Vencimento'])."</b> portanto não poderá entrar na remessa.<br>Verifique o vencimento!";
            }else {
                mysqli_query("UPDATE T002 set T002_Flag_Enviar_Banco='$selected' WHERE T002_Id='$T002_Id'");
                return true;
            }
        }
    }
    
    /**
     * updateT002_Flag_Reparcelar
     * 
     * @param String $T002_Id
     * @param String $selected
     * @return String/bool
     */
    public function updateT002_Flag_Reparcelar($T002_Id, $selected)
    {
        global $g;
        
        $T002_Id  = mysql_real_escape_string($T002_Id);
        $selected = mysql_real_escape_string($selected);
        mysqli_query("UPDATE T002 set T002_Flag_Reparcelar='$selected' WHERE T002_Id='$T002_Id'");
        return true;
    }
    
    /**
     * updateT002_Flag_Baixar_Lote
     * 
     * @param String $T002_Id
     * @param String $selected
     * @return String/bool
     */
    public function updateT002_Flag_Baixar_Lote($T002_Id, $selected)
    {
        global $g;
        
        $T002_Id  = mysql_real_escape_string($T002_Id);
        $selected = mysql_real_escape_string($selected);
        mysqli_query("UPDATE T002 set T002_Flag_Baixar_Lote='$selected' WHERE T002_Id='$T002_Id'");
        return true;
    }
    
    /**
     * listaTotalBaixar
     * 
     * @param String $extra
     * @return array
     */
    public function listaTotalBaixar($extra)
    {
        global $g;
        
        $sql = " SELECT ";
        $sql .= " sum(if(T002_Data_Recebimento='0000-00-00',T002_Valor_Saldo,0)) Total_Receber, ";
        $sql .= " sum(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Saldo,0)) Total_Recebido, ";
        $sql .= " sum(if(T002_Data_Recebimento='0000-00-00' AND T002_Flag_Baixar_Lote='S',T002_Valor_Saldo,0)) Total_Baixar ";
        $sql .= " FROM T002 ";
        /*$sql .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $sql .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $sql .= " LEFT JOIN T117 on T117_Id=T002_T117_Id ";
        $sql .= " LEFT JOIN D014 on D014_Id=T002_D014_Id ";
        $sql .= " LEFT JOIN D046 on D046_Id=D024_D046_Id ";
        $sql .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $sql .= " LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE T002_C004_Id='{$g['empresaAtual']}'";
        $sql .= "   AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";*/
        
        $extraNovo = <<<EOT
			(T002_C004_Id='{$g['empresaAtual']}' AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null))
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
     * listaTotalReparcelar
     * 
     * @param String $extra     
     * @return array
     */
    public function listaTotalReparcelar($extra)
    {
        global $g;
        
        $sql = " SELECT ";
        $sql .= " sum(if(T002_Data_Recebimento='0000-00-00',T002_Valor_Saldo,0)) Total_Receber, ";
        $sql .= " sum(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Saldo,0)) Total_Recebido, ";
        $sql .= " sum(if(T002_Data_Recebimento='0000-00-00' AND T002_Flag_Reparcelar='S',T002_Valor_Saldo,0)) Total_Reparcelar ";
        $sql .= " FROM T002 ";
        /*$sql .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $sql .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $sql .= " LEFT JOIN T117 on T117_Id=T002_T117_Id ";
        $sql .= " LEFT JOIN D014 on D014_Id=T002_D014_Id ";
        $sql .= " LEFT JOIN D046 on D046_Id=D024_D046_Id ";
        $sql .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $sql .= " LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE T002_C004_Id='{$g['empresaAtual']}'";
        $sql .= "   AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";*/
        
        $extraNovo = <<<EOT
			(T002_C004_Id='{$g['empresaAtual']}' AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null))
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
     * @return array
     */
    public function listaTotalAgrupar($extra)
    {
        global $g;
        
        $sql = " SELECT ";
        $sql .= " sum(if(T002_Data_Recebimento='0000-00-00',T002_Valor_Saldo,0)) Total_Receber, ";
        $sql .= " sum(if(T002_Data_Recebimento!='0000-00-00',T002_Valor_Saldo,0)) Total_Recebido, ";
        $sql .= " sum(if(T002_Data_Recebimento='0000-00-00' AND T002_Flag_Reparcelar='S',T002_Valor_Saldo,0)) Total_Agrupar ";
        $sql .= " FROM T002 ";
        /*$sql .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $sql .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $sql .= " LEFT JOIN T117 on T117_Id=T002_T117_Id ";
        $sql .= " LEFT JOIN D014 on D014_Id=T002_D014_Id ";
        $sql .= " LEFT JOIN D046 on D046_Id=D024_D046_Id ";
        $sql .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $sql .= " LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE T002_C004_Id='{$g['empresaAtual']}'";
        $sql .= "   AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";*/
        
        $extraNovo = <<<EOT
			(T002_C004_Id='{$g['empresaAtual']}' AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null))
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
     * listaTotalRemessa
     * 
     * @param String $extra     
     * @return array
     */
    public function listaTotalRemessa($extra)
    {
        global $g;
        
        $sql = " SELECT ";
        $sql .= " sum(if(T002_Data_Recebimento='0000-00-00' AND ifnull(T002_T035_Id,0)<=0 AND T002_Flag_Enviar_Banco='S',T002_Valor_Total,0)) Total_Remessa ";
        $sql .= " FROM T002 ";
        /*$sql .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $sql .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $sql .= " LEFT JOIN T117 on T117_Id=T002_T117_Id ";
        $sql .= " LEFT JOIN D014 on D014_Id=T002_D014_Id ";
        $sql .= " LEFT JOIN D046 on D046_Id=D024_D046_Id ";
        $sql .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $sql .= " LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE T002_C004_Id='{$g['empresaAtual']}'";
        $sql .= "   AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";*/
        
        $extraNovo = <<<EOT
			(T002_C004_Id='{$g['empresaAtual']}' AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null))
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
     * listaRemessas
     * 
     * @param String $T035_Id
     * @return array
     */
    public function listaRemessas($T035_Id = 0)
    {
        global $g;
        
        $sql = "SELECT * FROM T035  ";
        if (!empty($T035_Id)) {
            $T035_Id = mysql_real_escape_string($T035_Id);
            $sql .= " WHERE T035_Id='$T035_Id' ";
        }
        $sqlReturn = mysql_query($sql);
        $resultado = array();
        while ($row = mysql_fetch_assoc($sqlReturn)) {
            $resultado[] = $row;
        }
        if (!empty($T035_Id)) {
            return $resultado[0];
        } else {
            return $resultado;
        }
    }
    
    /**
     * atualizaTitulosParaBaixarT103
     * 
     * @return String/bool
     */
    public function atualizaTitulosParaBaixarT103()
    {
        global $g;
        
        // Lista todos os registro que serão baixados
        $cClausula = " SELECT ";
        $cClausula .= " T002_D027_Id, ";
        $cClausula .= " T002_Valor_Saldo, ";
        $cClausula .= " IFNULL(T103_Id,0) as T103_Id, ";
        $cClausula .= " T103_Valor_Total_Recebido, ";
        $cClausula .= " T002_Id ";
        $cClausula .= " FROM T002 ";
        $cClausula .= " LEFT JOIN T103 on T103_T002_Id=T002_Id ";
        $cClausula .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $cClausula .= " LEFT JOIN D027 on D027_Id=T002_D027_Id ";
        $cClausula .= " LEFT JOIN T117 on T117_Id=T002_T117_Id ";
        $cClausula .= " LEFT JOIN D014 on D014_Id=T002_D014_Id ";
        $cClausula .= " LEFT JOIN T007 on T007_Id=T002_T007_Id ";
        $cClausula .= " LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
        $cClausula .= " WHERE T002_Flag_Baixar_Lote='S' ";
        $cClausula .= "   AND T002_Data_Recebimento='0000-00-00'";
        $cClausula .= "   AND T002_C004_Id='{$g['empresaAtual']}' ";
        $cClausula .= " ORDER BY T002_Data_Vencimento,D024_Nome_Empresa";
        $cSql_T002 = mysql_query($cClausula);
        if (mysql_error()) {
            return mysql_error();
        }
     
        while ($mT002 = mysql_fetch_array($cSql_T002)) {

            if ($mT002['T103_Id'] <= 0) {
                mysql_query("SET AUTOCOMMIT=0");
                mysql_query("START TRANSACTION");

                mysql_query("SELECT T002_Id FROM T002 WHERE T002_Id='{$mT002['T002_Id']}' FOR UPDATE");
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return mysql_error();
                }

                $cSql_T103 = mysql_query("SELECT T103_Id FROM T103 WHERE T103_T002_Id='{$mT002['T002_Id']}' LIMIT 1 FOR UPDATE");
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return mysql_error();
                }

                $mT103Existente = mysql_fetch_array($cSql_T103);

                if ($mT103Existente['T103_Id'] > 0) {
                    mysql_query("COMMIT");
                    continue;
                }

                $cClausula = " INSERT INTO T103 (";
                $cClausula .= " T103_D027_Id, ";
                $cClausula .= " T103_T002_Id, ";
                $cClausula .= " T103_Valor_Recebido, ";
                $cClausula .= " T103_Valor_Total_Recebido, ";
                $cClausula .= " T103_Flag_Baixar_Lote ";
                $cClausula .= " ) values ( ";
                $cClausula .= " '$mT002[T002_D027_Id]', ";
                $cClausula .= " '$mT002[T002_Id]', ";
                $cClausula .= " '$mT002[T002_Valor_Saldo]', ";
                $cClausula .= " '$mT002[T002_Valor_Saldo]', ";
                $cClausula .= " 'S' ";
                $cClausula .= " ); ";
                $cSql_T103 = mysql_query($cClausula);

                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return mysql_error();
                }

                mysql_query("COMMIT");
            } 
        }
        
        return true;
    }
    
    /**
     * carregaTotaisBaixar
     * 
     * @param String $extra
     * @return array
     */
    public function carregaTotaisBaixar($extra)
    {
        global $g;
        
        $sql = " SELECT sum(T103_Valor_Total_Recebido) TotalRecebido, ";
        $sql .= " sum(T002_Valor_Saldo) Total ";
        $sql .= " FROM T002 ";
        /*$sql .= " LEFT JOIN T103 on T002_Id=T103_T002_Id ";
        $sql .= " LEFT JOIN D024 on D024_Id=T002_D024_Id ";
        $sql .= " WHERE T002_Flag_Baixar_Lote='S' ";
        $sql .= " AND T002_Data_Recebimento='0000-00-00' ";        
        $sql .= " AND T002_C004_Id='{$g['empresaAtual']}' ";*/
        
        $extraNovo = <<<EOT
			(T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}')
EOT;
        $sql .= gInsertExtraWhere($extra, $extraNovo);
        
        $sqlReturn = mysql_query($sql);
        $row       = mysql_fetch_assoc($sqlReturn);
        return $row;
    }
    
    /**
     * fin001reparcelar
     * 
     * @param String $D024_Id
     * @param String $C007_Id
     * @param String $valorSomado
     * @param String $extra
     * @return String/bool
     */
    public function fin001reparcelar($D024_Id, $C007_Id, $valorSomado, $extra)
    {
        global $g;

        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T002_Flag_Reparcelar='S' AND T002_Flag_Cancelada<>'S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}' AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)"));
        $sql   = mysql_query("SELECT * FROM T002 
                                LEFT JOIN D024 on D024_Id=T002_D024_Id
                                LEFT JOIN D027 on D027_Id=T002_D027_Id
                                LEFT JOIN T117 on T117_Id=T002_T117_Id
                                LEFT JOIN D014 on D014_Id=T002_D014_Id
                                LEFT JOIN D046 on D046_Id=D024_D046_Id
                                LEFT JOIN T007 on T007_Id=T002_T007_Id
                                LEFT JOIN T035 on T035_Id=T002_T035_Id
                                LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                                LEFT JOIN T103 on T002_Id=T103_T002_Id
                                LEFT JOIN T005 on T005_Id=T007_T005_Id
                                WHERE {$extra}");
        $textGeradores = "";
        $numeroDuplicataAgrupamento = array();
        $numeroDocumentoAgrupamento = array();
        while ($row = mysql_fetch_assoc($sql)) {
            // Validação criada em 10/09/2019 chamado 38929
            // Criada validação pois não será possível reparcelar títulos nos quais a nota não foi enviada para a sefaz ainda
            if($row['T002_T007_Id'] > 0 && empty($row['T007_Numero_Protocolo_Nfe']) && ($row['T007_Flag_ACP'] == 6 || $row['T007_Flag_ACP'] == 8)){
                return "Não é possível reparcelar os títulos, a nota " .$row['T007_Numero_Nota_Fiscal'] . " do título " . $row['T002_Numero_Duplicata'] . " não possui protocolo de autorização.";
            }

            $textGeradores .= $row['T002_Id'] . ';' . $row['T002_Numero_Duplicata'] . ';' . $row['T002_Data_Emissao'] . ';' . $row['T002_Data_Vencimento'] . ';' . $row['T002_Valor_Duplicata'] . ';' . PHP_EOL;
            //return $textGeradores;
            $numeroDuplicataAgrupamento[] = $row['T002_Numero_Duplicata'];
            $numeroDocumentoAgrupamento[] = $row['T002_Numero_Documento'];
        }
        $numeroDuplicataAgrupamento = implode(",", $numeroDuplicataAgrupamento);
        $numeroDocumentoAgrupamento = implode(",", $numeroDocumentoAgrupamento);

        $sql = mysql_query("SELECT *,sum(T002_Valor_Saldo) TotalT002 FROM T002
			LEFT JOIN D024 on D024_Id=T002_D024_Id
			LEFT JOIN D027 on D027_Id=T002_D027_Id
			LEFT JOIN T117 on T117_Id=T002_T117_Id
			LEFT JOIN D014 on D014_Id=T002_D014_Id
			LEFT JOIN D046 on D046_Id=D024_D046_Id
			LEFT JOIN T007 on T007_Id=T002_T007_Id
			LEFT JOIN T035 on T035_Id=T002_T035_Id
			LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
			LEFT JOIN T103 on T002_Id=T103_T002_Id
            LEFT JOIN T005 on T005_Id=T007_T005_Id
  			WHERE {$extra}");
        $retorno = mysql_fetch_array($sql);

        $sql2      = "SELECT SUM(T180_Valor_Parcela) TotalT180 FROM T180 WHERE T180_D024_Id='{$D024_Id}'";
        $retorno2  = mysql_query($sql2);
        $linha     = mysql_fetch_array($retorno2);
        $totalT002 = explode(".", $retorno['TotalT002']);
        $totalT180 = explode(".", $linha['TotalT180']);

        if ($totalT002[0] != $totalT180[0]) {
            return 'Valor total não confere! ';
        } else {
            mysql_query("START TRANSACTION");
            $sql    = "SELECT * FROM T180 WHERE T180_D024_Id='{$D024_Id}'";
            $result = mysql_query($sql);
            if (mysql_num_rows($result) == 0) {
                return "Não foi gerado nenhuma parcelas, favor gerar";
            }
            $count         = '1';
            $count2        = '0';
            $preSequencial = '';
            $textGerados   = '';
            $numeroDuplicataGerados = array();
            while ($linha = mysql_fetch_array($result)) {
                if($count == '27'){
                    //Chegou a letra Z
                    $count2++;
                    $count = '1';
                    $preSequencial = chr($count2 + 64);
                }
                $Numero_Duplicata = $linha['T180_Numero_Documento'] . $preSequencial . chr($count + 64);

                if(mysql_num_rows($result) == 1){
                    $Numero_Duplicata = 'AG '. $numeroDuplicataAgrupamento;
                    $linha['T180_Numero_Documento'] = $numeroDocumentoAgrupamento;
                }

                // Cálculo o valor da comissão da parcela
                $nValorComissao = ($linha['T180_Valor_Parcela'] * $retorno['T002_Percentual_Comissao']) / 100;
                
                $queryT002 = "INSERT INTO T002
	                            (T002_D024_Id,
 								 T002_D021_Id,
								 T002_C004_Id,
								 T002_D027_Id,
								 T002_D014_Id,
                                 T002_D032_Id,
								 T002_Nome_Cliente,
								 T002_Numero_Duplicata,
								 T002_Numero_Documento,
								 T002_Data_Emissao,
								 T002_Data_Vencimento,
								 T002_Data_Recebimento,
								 T002_Valor_Duplicata,
								 T002_Valor_Saldo,
								 T002_Valor_Juros,
								 T002_Valor_Desconto,
								 T002_Valor_Total,
								 T002_Data_Lancamento,
								 T002_Hora_Lancamento,
								 T002_Numero_Parcela,
								 T002_Observacao_2,
								 T002_Numero_Cheque,
								 T002_Banco_Cheque,
								 T002_Agencia_Cheque,
								 T002_Conta_Corrente_Cheque,
								 T002_Nome_Cheque,
								 T002_Data_Cheque,
								 T002_Cidade_Cheque,
								 T002_T007_Id,
                                 T002_Flag_Agrupado_Reparcelado,
                                 T002_Observacao_Reparcelamento,
                                 T002_Percentual_Comissao,
                                 T002_Valor_Comissao
								 )
						   values
						       ('{$retorno['T002_D024_Id']}',
								'{$retorno['T002_D021_Id']}',
								'{$retorno['T002_C004_Id']}',
								'{$linha['T180_D027_Id']}',
								'{$retorno['T002_D014_Id']}',
                                '{$retorno['T002_D032_Id']}',
								'{$retorno['D024_Nome_Empresa']}',
								'{$Numero_Duplicata}',
								'{$linha['T180_Numero_Documento']}',
								'{$linha['T180_Data_Emissao']}',
								'{$linha['T180_Data_Vencimento']}',
								'0000-00-00',
								'{$linha['T180_Valor_Parcela']}',
								'{$linha['T180_Valor_Parcela']}',
								'0',
								'0',
								'{$linha['T180_Valor_Parcela']}',
								curdate(),
								'" . strftime('%T') . "',
								'{$linha['T180_Numero_Parcela']}',
								'*** REPARCELAMENTO ***',
								'{$linha['T180_Numero_Cheque']}',
								'{$linha['T180_Banco_Cheque']}',
								'{$linha['T180_Agencia_Cheque']}',
								'{$linha['T180_Conta_Corrente_Cheque']}',
								'{$linha['T180_Nome_Cheque']}',
								'{$linha['T180_Data_Cheque']}',
								'{$linha['T180_Cidade_Cheque']}',
								'{$retorno['T002_T007_Id']}',
                                'S',
                                '{$numeroDuplicataAgrupamento}',
                                '{$retorno['T002_Percentual_Comissao']}',
                                '{$nValorComissao}'
								)";
                mysql_query($queryT002);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro 2: $cErro<br>Clausula: $cClausula";
                }

                $nT002_Id = $g['mysqlLastId'];

                if (!empty($linha['T180_Valor_Acrescimo']) && $linha['T180_Valor_Acrescimo'] != '0.00' && $linha['T180_Valor_Acrescimo'] != '') {
                    $T020 = <<<EOT
						INSERT INTO T020 (
							T020_T002_Id,
							T020_C004_Id,
							T020_Data_Lancamento,
							T020_Hora_Lancamento,
							T020_Historico,
							T020_Valor,
							T020_Flag_Deb_Cred
						) VALUES (
							'{$nT002_Id}',
							'{$g['empresaAtual']}',
							CURDATE(),
							CURTIME(),
							'Acréscimo reparcelamento',
							'{$linha['T180_Valor_Acrescimo']}',
							'C'
						);
EOT;
                    //die($T020);
                    mysql_query($T020);
                }
                if (!empty($linha['T180_Valor_Desconto']) && $linha['T180_Valor_Desconto'] != '0.00' && $linha['T180_Valor_Desconto'] != '') {
                    $T020 = <<<EOT
						INSERT INTO T020 (
							T020_T002_Id,
							T020_C004_Id,
							T020_Data_Lancamento,
							T020_Hora_Lancamento,
							T020_Historico,
							T020_Valor,
							T020_Flag_Deb_Cred
						) VALUES (
							'{$nT002_Id}',
							'{$g['empresaAtual']}',
							CURDATE(),
							CURTIME(),
							'Desconto reparcelamento',
							'{$linha['T180_Valor_Desconto']}',
							'D'
						);
EOT;
                    mysql_query($T020);
                }

                mysql_query("CALL T002_Grava_Totalizacao('{$nT002_Id}')");

                //Gera boleto automáticamente
                $sqlD007 = mysql_query("SELECT * FROM D007 LEFT JOIN D027 ON D027_D007_Id=D007_Id WHERE D027_Id = '{$linha['T180_D027_Id']}' AND D027_Flag_Boleto = 'S'");
                $cErro   = mysql_error();
                if (!empty($cErro)) {
                    $retorno .= "ERRO: " . $cErro;
                    mysql_query("ROLLBACK");
                    return $retorno;
                }
                // se para esta parcela
                if (mysql_num_rows($sqlD007) > 0) {
                    $mD007 = mysql_fetch_array($sqlD007);

                    if (!empty($mD007['D007_Codigo_Cedente'])) {
                        $xCodigo_Cedente = $mD007['D007_Codigo_Cedente'];
                    } else {
                        $xCodigo_Cedente = $T005_D024_Id_Contas;
                    }

                    $T117_Flag_Layout = 'N';
                    if ($mD007['D007_Flag_Layout'] != 2) {
                        $T117_Flag_Layout = 'S';
                    }

                    if ($mD007['D007_Flag_Tipo_Registro'] == '1') {
                        $D007_Flag_Com_Registro = 'S';
                    } elseif ($mD007['D007_Flag_Tipo_Registro'] == '2') {
                        $D007_Flag_Com_Registro = 'N';
                    }

                    $cClausula = " INSERT INTO T117 ( ";
                    $cClausula .= " T117_C004_Id, ";
                    $cClausula .= " T117_D024_Id, ";
                    $cClausula .= " T117_D007_Id, ";
                    $cClausula .= " T117_T002_Id, ";
                    $cClausula .= " T117_Data_Emissao, ";
                    $cClausula .= " T117_Data_Vencimento, ";
                    $cClausula .= " T117_Valor_Total, ";
                    $cClausula .= " T117_Percentual_Juros_Dia, ";
                    $cClausula .= " T117_Percentual_Multa, ";
                    $cClausula .= " T117_Instrucoes, ";
                    $cClausula .= " T117_Nosso_Numero, ";
                    $cClausula .= " T117_Flag_Layout, ";
                    $cClausula .= " T117_Flag_Com_Registro, ";
                    $cClausula .= " T117_Codigo_Cedente, ";
                    $cClausula .= " T117_Aceite, ";
                    $cClausula .= " T117_Especie, ";
                    $cClausula .= " T117_Especie_Documento, ";
                    $cClausula .= " T117_Uso_Banco ";
                    $cClausula .= " ) values (";
                    $cClausula .= " '{$retorno['T002_C004_Id']}', ";
                    $cClausula .= " '{$retorno['T002_D024_Id']}', ";
                    $cClausula .= " '{$mD007['D007_Id']}', ";
                    $cClausula .= " '{$nT002_Id}', ";
                    $cClausula .= " current_date(), ";
                    $cClausula .= " '{$linha['T180_Data_Vencimento']}', ";
                    $cClausula .= " '{$linha['T180_Valor_Parcela']}', ";
                    $cClausula .= " '{$mD007['D007_Percentual_Juros_Dia']}', ";
                    $cClausula .= " '{$mD007['D007_Percentual_Multa']}', ";
                    $cClausula .= " '{$mD007['D007_Instrucoes_Boleto']}', ";
                    $cClausula .= " '{$mD007['D007_Nosso_Numero']}', ";
                    $cClausula .= " '$T117_Flag_Layout', ";
                    $cClausula .= " '$D007_Flag_Com_Registro', ";
                    $cClausula .= " '$xCodigo_Cedente', ";
                    $cClausula .= " '{$mD007['D007_Aceite']}', ";
                    $cClausula .= " '{$mD007['D007_Especie']}', ";
                    $cClausula .= " '{$mD007['D007_Especie_Documento']}', ";
                    $cClausula .= " '{$mD007['D007_Uso_Banco']}' ";
                    $cClausula .= " )";
                    mysql_query($cClausula);
                    $cErro    = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "ERRO: $cErro<br>Clausula: $cClausula";
                    }

                    $nT117_Id  = $g['mysqlLastId'];
                    $cClausula = " update T002 set ";
                    $cClausula .= " T002_T117_Id='{$nT117_Id}', ";
                    $cClausula .= " T002_D007_Id='{$mD007['D007_Id']}' ";
                    // clausula para tirar a flag de geração de boleto
                    $cClausula .= " where T002_Id='{$nT002_Id}' ";
                    //mysql_query("ROLLBACK");
                    //die($cClausula);
                    mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "ERRO: $cErro<br>Clausula: $cClausula";
                    }

                    //-- ESTE ARQUIVO IRÁ GERAR O CODIGO DE BARRAS A LINHA DIGITAVEL E O NOSSO NUMERO COM DIGITO VERIFICADOR
                    $cClausula = " select D062_Numero_Banco ";
                    $cClausula .= " from D062 ";
                    $cClausula .= " where D062_Id='{$mD007['D007_D062_Id']}' ";
                    $cSql_D062                  = mysql_query($cClausula);
                    $mD062                      = mysql_fetch_array($cSql_D062);
                    $mD007['D062_Numero_Banco'] = $mD062['D062_Numero_Banco'];

                    $T002=mysql_query("SELECT * FROM T002 WHERE T002_Id='{$nT002_Id}'");
                    $mT002=mysql_fetch_array($T002);
                    $T117=mysql_query("SELECT * FROM T117 WHERE T117_Id='{$nT117_Id}'");
                    $mT117=mysql_fetch_array($T117);
                    // parametros necessários para o include
                    $T117_Data_Vencimento = $mT117['T117_Data_Vencimento'];
                    $nValor_Total         = $mT002['T002_Valor_Duplicata'];
                    log("classe valor total: ".$nValor_Total);
                    if (empty($mD007['D007_Nosso_Numero'])) {
                        $mD007['D007_Nosso_Numero'] = 1;
                    } else {
                        $mD007['D007_Nosso_Numero']++;
                    }
                    if ($mD007['D062_Numero_Banco'] == '399') {
                        //if (strlen($mD007['D007_Nosso_Numero'])>5)
                        //{
                        //   mysql_query("ROLLBACK");
                        //    return "O campo Nosso Numero ultrapassou limite: 99999 entre em contato com o banco e solicite um novo Range + Nosso Numero e grave-os no cadastro de contas.";
                        //}
                        $mD007['D007_Nosso_Numero'] = STR_PAD($mD007['D007_Nosso_Numero'], 5, "0", STR_PAD_LEFT);
                    }
                    $cClausula = " update D007 set ";
                    $cClausula .= " D007_Nosso_Numero='{$mD007['D007_Nosso_Numero']}' ";
                    $cClausula .= " where D007_Id='{$mD007['D007_Id']}' ";
                    $cSql_D007 = mysql_query($cClausula);
                    $cErro     = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "ERRO: $cErro<br>Clausula: $cClausula";
                    }

                    chdir($g['pathRaiz'] . 'hardness/boleto/');
                    $cArquivo_Gerar = "./include/gerar_" . $mD007['D062_Numero_Banco'] . ".php";
                    if (file_exists($cArquivo_Gerar)) {
                        $arquivo = include($cArquivo_Gerar);
                        if (!empty($arquivo) && $arquivo !== 1) {
                            chdir($g['pathRaiz']);
                            return $arquivo;
                        }
                    } else {
                        mysql_query("ROLLBACK");
                        chdir($g['pathRaiz']);
                        return "Arquivo inválido: $cArquivo_Gerar";
                    }
                    chdir($g['pathRaiz']);
                }

                $sql = mysql_query("SELECT * FROM T002 WHERE T002_Id='{$nT002_Id}'");
                while ($row = mysql_fetch_assoc($sql)) {
                    $textGerados .= $row['T002_Id'] . ';' . $row['T002_Numero_Duplicata'] . ';' . $row['T002_Data_Emissao'] . ';' . $row['T002_Data_Vencimento'] . ';' . $row['T002_Valor_Duplicata'] . ';' . PHP_EOL;
                }
                $count++;
                $numeroDuplicataGerados[] = $Numero_Duplicata;
            }
            $numeroDuplicataGerados = implode(",", $numeroDuplicataGerados);

            // insere registros na tabela de histórico de reparcelamento
            $query = "INSERT INTO T166
 								(T166_D024_Id,
								 T166_C004_Id,
								 T166_Data_Emissao,
								 T166_Valor_Total,
								 T166_Titulos_Geradores,
								 T166_Titulos_Gerados,
								 T166_C007_Id,
								 T166_Tipo)
						   values
						       ('{$retorno['T002_D024_Id']}',
								'{$retorno['T002_C004_Id']}',
								curdate(),
								'{$retorno['TotalT002']}',
								'{$textGeradores}',
								'{$textGerados}',
								'{$g['usuarioAtual']}',
								'R')";
            //mysql_query("ROLLBACK");
            mysql_query($query);
            $T166_Id = $g['mysqlLastId'];
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 2: $cErro<br>Clausula: $query";
            }

            $arrayGeradores = explode("\n", $textGeradores);
            foreach($arrayGeradores as $linha){
                if(empty($linha)) { continue; }
                $linha = explode(";", $linha);
                mysql_query("INSERT INTO T244 (T244_T002_Id, T244_T166_Id) VALUES ('{$linha[0]}', '{$T166_Id}')");
                if (!empty($cErro)) { mysql_query("ROLLBACK"); return "Erro 7: $cErro<br>Clausula: $query"; }
            }

            $arrayGerados = explode("\n", $textGerados);
            foreach($arrayGerados as $linha){
                if(empty($linha)) { continue; }
                $linha = explode(";", $linha);
                mysql_query("INSERT INTO T244 (T244_T002_Id, T244_T166_Id) VALUES ('{$linha[0]}', '{$T166_Id}')");                
                if (!empty($cErro)) { mysql_query("ROLLBACK"); return "Erro 7: $cErro<br>Clausula: $query"; }
            }



            $sql4     = "SELECT T180_Numero_Documento FROM T180 WHERE T180_D024_Id='{$D024_Id}'";
            $retorno4 = mysql_query($sql4);
            while ($linha4 = mysql_fetch_array($retorno4)) {
                $numeroDocumento = $linha4['T180_Numero_Documento'] . "<br />";
            }
            //-- Cancelando Contas a Receber
            mysqli_query("UPDATE T002 
                LEFT JOIN D024 on D024_Id=T002_D024_Id
                LEFT JOIN D027 on D027_Id=T002_D027_Id
                LEFT JOIN T117 on T117_Id=T002_T117_Id
                LEFT JOIN D014 on D014_Id=T002_D014_Id
                LEFT JOIN D046 on D046_Id=D024_D046_Id
                LEFT JOIN T007 on T007_Id=T002_T007_Id
                LEFT JOIN T035 on T035_Id=T002_T035_Id
                LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
                LEFT JOIN T103 on T002_Id=T103_T002_Id
                LEFT JOIN T005 ON T005_Id=T007_T005_Id 
                SET T002_Data_Recebimento=current_date(),
                T002_T002_Id_Agrupado='$nT002_Id', 
                T002_Observacao_2='*** REPARCELADO {$numeroDocumento} ***',
                T002_Observacao_Reparcelamento='{$numeroDuplicataGerados}'
                WHERE {$extra}");
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 3: $cErro<br>Clausula: $cClausula";
            }
            
            $sql     = "DELETE FROM T180 WHERE T180_D024_Id='{$D024_Id}'";
            $retorno = mysql_query($sql);
            $erro    = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 4: $cErro<br>Clausula: $cClausula";
            }
            
            $retorno = mysql_query("COMMIT");
            if (!$retorno) {
                return "Erro 4: Falha no reparcelamento<br/>" . mysql_error();
            }
            return true;
        } 
    }
    
    /**
     * geraDownloadRemessa
     *
     * @param String $T002_T035_Id
     * @return array
     */
    public function geraDownloadRemessa($T002_T035_Id)
    {
        global $g;
        
        $cClausula = " SELECT ";
        $cClausula .= " T035_Conteudo_Arquivo,
                        T035_Nome_Arquivo,
                        D062_Numero_Banco  ";
        $cClausula .= " FROM T035  ";
        $cClausula .= " LEFT JOIN D007 ON D007_Id=T035_D007_Id ";
        $cClausula .= " LEFT JOIN D062 ON D062_Id=D007_D062_Id ";
        $cClausula .= " WHERE T035_Id='$T002_T035_Id' ";        
        $cSql_T035 = mysql_query($cClausula);
        $mT035     = mysql_fetch_array($cSql_T035);

        $temponto = strstr($mT035['T035_Nome_Arquivo'], '.');
        if($temponto==''){
            $nomeArquivo = $mT035['T035_Nome_Arquivo'] . '.txt';
        }else{
            $nomeArquivo = $mT035['T035_Nome_Arquivo'];
        }

        $arquivo = $mT035['T035_Conteudo_Arquivo'];

        if($mT035['D062_Numero_Banco'] == 756){
            log("entrou na codificação");
            // alterando a codificação para o Sicoob conforme padrão estabelecido pelo banco (ANSI)
            $arquivo = iconv("UTF-8", "WINDOWS-1252//TRANSLIT//IGNORE", $mT035['T035_Conteudo_Arquivo']);
        }

        header('Content-type: application/x-msdownload');
        header('Content-Disposition: attachment; filename=' . $nomeArquivo);
        header('Pragma: no-cache');
        header('Expires: 0');
        return $arquivo;
    }

    /**
     * geraDownloadRemessa
     *
     * @param String $T002_T035_Id
     * @return array
     */
    public function geraDownloadRetorno($T037_Id)
    {
        global $g;
        
        $cClausula = " SELECT ";
        $cClausula .= " T037_Conteudo_Arquivo,T037_Nome_Arquivo  ";
        $cClausula .= " FROM T037  ";
        $cClausula .= " WHERE T037_Id='$T037_Id' ";
        log($cClausula);
        
        $cSql_T037 = mysql_query($cClausula);
        $mT037     = mysql_fetch_array($cSql_T037);
        
        header('Content-type: application/x-msdownload');
        header('Content-Disposition: attachment; filename=' . $mT037['T037_Nome_Arquivo'] . '.txt');
        header('Pragma: no-cache');
        header('Expires: 0');
        return $mT037['T037_Conteudo_Arquivo'];
    }

    /**
     * buscaNomeArquivoRemessa
     *
     * @param String $T002_T035_Id
     * @return array
     */    
    public function buscaNomeArquivoRemessa($T002_T035_Id)
    {
        $cClausula = " SELECT ";
        $cClausula .= " T035_Nome_Arquivo  ";
        $cClausula .= " FROM T035  ";
        $cClausula .= " WHERE T035_Id='$T002_T035_Id' ";
        $cSql_T035 = mysql_query($cClausula);
        $mT035     = mysql_fetch_array($cSql_T035);
        return $mT035['T035_Nome_Arquivo'] . '.txt';
    }
    
    /**
     * montarEmailReparcelamento
     *
     * @param String $T166_Id
     * @return array
     */    
    public function montarEmailReparcelamento($T166_Id)
    {
        global $g, $confUsuario;
        $T166_Id = mysql_real_escape_string($T166_Id);
        $sql     = "SELECT * FROM T166 LEFT JOIN D024 on D024_Id=T166_D024_Id LEFT JOIN D020 on D020_Id=D024_D020_Id LEFT JOIN D018 on D018_Id=D024_D018_Id WHERE T166_Id='{$T166_Id}'";
        $T166    = mysql_query($sql);
        $T166    = mysql_fetch_array($T166);
        
        $array = array();
        
        $array['Para'] = $T166['D024_E_Mail'];
        if ($T166['T166_Tipo'] == "A") {
            $array['Assunto'] = "Agrupamento Nº[{$T166_Id}]";
        } elseif ($T166['T166_Tipo'] == "R") {
            $array['Assunto'] = "Reparcelamento Nº[{$T166_Id}]";
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
        $corpo = '<div style="border:2px solid #E0EEEE; padding-top:10px;padding-bottom:10px;font-size:12px;font-family:Sans-Serif;">';
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        $corpo .= '<img src="' . $caminhoImagem . '" style="margin-left:25px"><br /><br />';
        // <br />Seguem abaixo preços e condições<br />
        if ($T166['T166_Tipo'] == "A") {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">AGRUPAMENTO Nº: <span style="font-weight:normal;">' . $T166_Id . '</span></div>';
        } elseif ($T166['T166_Tipo'] == "R") {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">REPARCELAMENTO Nº: <span style="font-weight:normal;">' . $T166_Id . '</span></div>';
        }
        
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Cliente/Fornecedor: <span style="font-weight:normal;">' . $T166['D024_Nome_Empresa'] . '</span></div>';
        
        if (!empty($T166['D024_Cnpj'])) {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">CNPJ: <span style="font-weight:normal;">' . $T166['D024_Cnpj'] . '</span></div>';
        } elseif (!empty($T166['D024_Cpf'])) {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">CPF: <span style="font-weight:normal;">' . $T166['D024_Cpf'] . '</span></div>';
        }
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Cidade: <span style="font-weight:normal;">' . $T166['D020_Nome_Cidade'] . ' - ' . $T166['D018_UF'] . '</span></div>';
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Telefone: <span style="font-weight:normal;">(' . $T166['D024_DDD_Telefone_1'] . ') ' . $T166['D024_Telefone_1'] . '</span></div>';
        if ($T166['T166_Tipo'] == "A") {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Tipo: <span style="font-weight:normal;">Agrupamento</span></div>';
        } elseif ($T166['T166_Tipo'] == "R") {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Tipo: <span style="font-weight:normal;">Reparcelamento</span></div>';
        }
        
        // tabela de geradores
        $corpo .= '<div style="margin-left:25px;margin-top:25px;font-size:13px;font-family:Sans-Serif;font-weight:bold">Títulos Geradores</div>';
        $corpo .= '<table style="' . $tabela . '">';
        $corpo .= '<tbody><tr><th style="' . $th . '">Duplicata</th><th style="' . $th . '" align="center" width=80>Emissão</th></th><th style="' . $th . '" align="center" width=80>Vencimento</th><th style="' . $th . '" align="center" width=70px>Valor</th></tr>';
        
        $T166A   = mysql_query("SELECT T166_Titulos_Geradores FROM T166 WHERE T166_Id='{$T166_Id}'");
        $aT166   = mysql_fetch_array($T166A);
        $res     = explode("\n", $aT166['T166_Titulos_Geradores']);
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
        
        $T002A = mysql_query("SELECT T002_Id,T002_Numero_Duplicata,T002_Data_Vencimento,T002_Data_Emissao,T002_Valor_Duplicata FROM T002 WHERE T002_Id IN ({$indices})");
        while ($linha = mysql_fetch_assoc($T002A)) {
            
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . '">';
            $corpo .= $linha['T002_Numero_Duplicata'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T002_Data_Emissao']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T002_Data_Vencimento']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($linha['T002_Valor_Duplicata']);
            $corpo .= '</td>';
            $corpo .= '</tr>';
            
        }
        
        $corpo .= '</tbody>';
        $corpo .= '</table>';
        
        // tabela de gerados
        $corpo .= '<div style="margin-left:25px;margin-top:25px;font-size:13px;font-family:Sans-Serif;font-weight:bold">Títulos Gerados</div>';
        $corpo .= '<table style="' . $tabela . '">';
        $corpo .= '<tbody><tr><th style="' . $th . '">Duplicata</th><th style="' . $th . '" align="center" width=80>Emissão</th></th><th style="' . $th . '" align="center" width=80>Vencimento</th><th style="' . $th . '" align="center" width=70px>Valor</th></tr>';
        
        $T166A   = mysql_query("SELECT T166_Titulos_Gerados FROM T166 WHERE T166_Id='{$T166_Id}'");
        $aT166   = mysql_fetch_array($T166A);
        $res     = explode("\n", $aT166['T166_Titulos_Gerados']);
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
        
        $T002A = mysql_query("SELECT T002_Id,T002_Numero_Duplicata,T002_Data_Vencimento,T002_Data_Emissao,T002_Valor_Duplicata FROM T002 WHERE T002_Id IN ({$indices})");
        while ($linha = mysql_fetch_assoc($T002A)) {
            
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . '">';
            $corpo .= $linha['T002_Numero_Duplicata'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T002_Data_Emissao']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T002_Data_Vencimento']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($linha['T002_Valor_Duplicata']);
            $corpo .= '</td>';
            $corpo .= '</tr>';
            
        }
        
        $corpo .= '</tbody>';
        $corpo .= '</table>';
        
        // valor total
        $corpo .= '<div style="margin-left:25px;margin-top:25px;font-size:13px;font-family:Sans-Serif;font-weight:bold">Valor Total:<span style="color:black;margin-left:10px">' . gCorrigeNumero($T166['T166_Valor_Total']) . '</span></div>';
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
        $array['D024_Id'] = $T166['T166_D024_Id'];
        //$array['T139_Valor_Total'] = $T003['T003_Valor_Total'];
        // $sql = "SELECT COUNT(*) FROM T004 WHERE T004_T003_Id='{$T003_Id}'";
        // $registros = mysql_query($sql);
        // $registros = mysql_fetch_array($registros);
        // $array['T139_Numero_Registros'] = $registros[0];
        
        return $array;
    }
    
    /**
     * getVar
     *
     * @param String $nome
     * @return String
     */ 
    public function getVar($nome)
    {
        $sql       = "SELECT D130_Conteudo FROM D130 WHERE D130_Nome = '{$nome}'";
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        if (empty($row)) {
            return "Nome não existe: {$nome}";
        }
        return $row['D130_Conteudo'];
    }
    
    /**
     * gerarNumeroDuplicata
     *
     * @param String $T002_Id
     * @return String
     */     
    public function gerarNumeroDuplicata($T002_Id)
    {
        $sql    = "SELECT T002_Numero_Duplicata FROM T002 WHERE T002_Id='{$T002_Id}'";
        $result = mysql_query($sql);
        $linha  = mysql_fetch_array($result);
        
        if (!empty($linha['T002_Numero_Duplicata'])) {
            $retorno = <<<EOT
		Nº <span class="corFundo2">{$linha['T002_Numero_Duplicata']}</span>
EOT;
        } else {
            $retorno = <<<EOT
		Nº <span>SEM NÚMERO</span>
EOT;
        }
        
        return $retorno;
    }
    
    /**
     * gerarDiaVencimento
     *
     * @param String $T002_Id
     * @return String
     */    
    public function gerarDiaVencimento($T002_Id)
    {
        global $g;
        $sql     = "SELECT T002_Data_Vencimento FROM T002 WHERE T002_Id='{$T002_Id}'";
        $result  = mysql_query($sql);
        $linha   = mysql_fetch_array($result);
        $data    = explode("-", $linha['T002_Data_Vencimento']);
        $mes     = gRetornaMes($data[1]);
        $retorno = <<<EOT
		<b>Vencimento</b>:{$data[2]} de {$mes} de {$data[0]}
EOT;
        return $retorno;
    }
    
    /**
     * gerarConteudoPromissoria
     *
     * @param String $T002_Id
     * @return String
     */      
    public function gerarConteudoPromissoria($T002_Id)
    {
        require_once("bibliotecas/classes/GExtenso.php");
        $EXT = new GExtenso();
        global $g;
        $sql    = "SELECT * FROM T002 LEFT JOIN D024 on D024_Id=T002_D024_Id WHERE T002_Id='{$T002_Id}' ";
        $result = mysql_query($sql);
        $linha  = mysql_fetch_array($result);
        
        //buscando dados da empresa atual
        $sql2    = "SELECT * FROM C004 LEFT JOIN D018 on D018_Id=C004_D018_Id LEFT JOIN D020 on D020_Id=C004_D020_Id WHERE C004_Id='{$g['empresaAtual']}'";
        $result2 = mysql_query($sql2);
        $linha2  = mysql_fetch_array($result2);
        
        $data        = explode("-", $linha['T002_Data_Vencimento']);
        $dataEmissao = gCorrigeData($linha['T002_Data_Emissao_Promissoria']);
        
        $dia        = gRetornaDiaExtenso($data[2]);
        $dia        = strtoupper($dia);
        $mes        = gRetornaMes($data[1]);
        $mes        = strtoupper($mes);
        $diaEmissao = gRetornaDiaExtenso($dataEmissao[2]);
        
        if (!empty($linha['D024_Cnpj'])) {
            $cpfCnpj = "Cnpj: " . $linha['D024_Cnpj'];
        } else {
            $cpfCnpj = "Cpf: " . $linha['D024_Cpf'];
        }
        $valorExtenso = gValorPorExtenso($linha['T002_Valor_Saldo']);
        $valorExtenso = strtoupper($valorExtenso);
        $ano          = $EXT->numero($data[0]);
        $ano          = strtoupper($ano);
        $retorno      = <<<EOT
		No dia {$dia} de {$mes} de {$ano} pagarei por esta única via de <b>NOTA PROMISSÓRIA</b><br />
		a {$linha2['C004_Nome_Empresa']} CPF/CNPJ:{$linha2['C004_Cnpj']}<br /><br />
		ou à sua ordem, a quantia de <span class="corFundo">{$valorExtenso}</span> em moeda corrente deste país<br /><br />
		Local de pagamento: {$linha2['D020_Nome_Cidade']} - {$linha2['D018_UF']} Data da Emissão: {$dataEmissao}<br />
		Nome do Emitente: {$linha['D024_Nome_Empresa']}<br />
		{$cpfCnpj} Endereço: {$linha['D024_Endereco']}
		<div class="assinatura">
			__________________________________________________________________<br />
			Assinatura do Emitente
		</div>
EOT;
        return $retorno;
    }
    
    /**
     * gerarvalorPromissoria
     *
     * @param String $T002_Id
     * @return String
     */     
    public function gerarvalorPromissoria($T002_Id)
    {
        global $g;
        $sql     = "SELECT T002_Valor_Saldo FROM T002 WHERE T002_Id='{$T002_Id}'";
        $result  = mysql_query($sql);
        $linha   = mysql_fetch_array($result);
        $valor   = gCorrigeNumero($linha['T002_Valor_Saldo']);
        $retorno = <<<EOT
		<b>R\$ <span class="corFundo2">{$valor}</span></b>
EOT;
        return $retorno;
    }
    
    /**
     * fin001calcularParcelaCombo
     *
     * @param String $parcela
     * @param String $valorTotal
     * @return String
     */     
    public function fin001calcularParcelaCombo($parcela, $valorTotal)
    {
        return gCorrigeNumero($valorTotal / $parcela);
    }
    
    /**
     * fin001inserirParcela
     *
     * @param Form $form
     * @return String/bool
     */     
    public function fin001inserirParcela($form)
    {
        global $g;
        $D024_Id        = $form['D024_Id'];
        $intervaloDias  = $form['IntervalorDias'];
        $portador       = $form['D027_Id'];
        $Total_Parcelas = $form['Total_Parcelas'];
        $valorTotal     = $form['valorSomado'];
        
        $diaFixo                = $form['diaFixo'];
        $primeiroVencimentoNum  = $form['primeiroVencimentoNum'];
        $primeiroVencimentoData = $form['primeiroVencimentoData'];
        $Emissao                = $form['Emissao'];
        $Emissao                = gCorrigeDataInverte($Emissao);
        
        // se foi definido um intervalo de dias
        if ($intervaloDias > 0) {
            $valorNovo = 0;
            // $query = "SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'";
            // $retorno=mysql_query($query);		
            // $linha = mysql_fetch_assoc($retorno);
            // $valorTotal=$linha['T005_Valor_Total'];
            
            $query            = "SELECT * FROM T180 WHERE T180_D024_Id='{$D024_Id}' ORDER BY T180_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T180_Prazos_Dias'];
            $numeroUltima     = $linha['T180_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $vencimentoUltima = $linha['T180_Data_Vencimento'];
            $numeroDcto       = $linha['T180_Numero_Documento'];
            $dataEmissao      = $linha['T180_Data_Emissao'];
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
            
            $cClausula = "INSERT INTO T180 (";
            $cClausula .= "T180_D024_Id,";
            $cClausula .= "T180_C007_Id,";
            $cClausula .= "T180_D027_Id,";
            $cClausula .= "T180_Numero_Parcela,";
            $cClausula .= "T180_Prazos_Dias,";
            $cClausula .= "T180_Data_Vencimento,";
            $cClausula .= "T180_Valor_Parcela, ";
            $cClausula .= "T180_Numero_Documento, ";
            $cClausula .= "T180_Data_Emissao";
            $cClausula .= ") values (";
            $cClausula .= "'$D024_Id',";
            $cClausula .= "'{$g['usuarioAtual']}',";
            $cClausula .= "'$portador',";
            $cClausula .= "'$numeroNova',";
            $cClausula .= "'$prazoNovo',";
            $cClausula .= "DATE_ADD('$vencimentoUltima',INTERVAL $intervaloDias DAY),";
            $cClausula .= "'$valorNovo', ";
            $cClausula .= "'$numeroDcto', ";
            $cClausula .= "'$Emissao'";
            $cClausula .= ")";
            
            mysql_query($cClausula);
            $erro = mysql_error();
            
            // ou se foi colocado um dia fixo	
        } else if ($diaFixo > 0) {
            
            if ($diaFixo > 31)
                return "Por favor, insira um dia válido";
            
            $valorNovo = 0;
            // $query = "SELECT * FROM T005 WHERE T005_Id='{$T005_Id}'";
            // $retorno=mysql_query($query);		
            // $linha = mysql_fetch_assoc($retorno);
            // $valorTotal=$linha['T005_Valor_Total'];
            
            $query            = "SELECT * FROM T180 WHERE T180_D024_Id='{$D024_Id}' ORDER BY T180_Id DESC";
            $retorno          = mysql_query($query);
            $linha            = mysql_fetch_assoc($retorno);
            $prazoUltima      = $linha['T180_Prazos_Dias'];
            $numeroUltima     = $linha['T180_Numero_Parcela'];
            $numeroNova       = $numeroUltima + 1;
            $vencimentoUltima = $linha['T180_Data_Vencimento'];
            $numeroDcto       = $linha['T180_Numero_Documento'];
            $dataEmissao      = $linha['T180_Data_Emissao'];
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
            
            $cClausula = "INSERT INTO T180 (";
            $cClausula .= "T180_D024_Id,";
            $cClausula .= "T180_C007_Id,";
            $cClausula .= "T180_D027_Id,";
            $cClausula .= "T180_Numero_Parcela,";
            $cClausula .= "T180_Data_Vencimento,";
            $cClausula .= "T180_Prazos_Dias,";
            $cClausula .= "T180_Valor_Parcela, ";
            $cClausula .= "T180_Numero_Documento, ";
            $cClausula .= "T180_Data_Emissao";
            $cClausula .= ") values (";
            $cClausula .= "'$D024_Id',";
            $cClausula .= "'{$g['usuarioAtual']}',";
            $cClausula .= "'$portador',";
            $cClausula .= "'$numeroNova',";
            $cClausula .= "'$dataNova',";
            $cClausula .= "DATEDIFF('$dataNova',current_date()),";
            $cClausula .= "'$valorNovo', ";
            $cClausula .= "'$numeroDcto', ";
            $cClausula .= "'$Emissao'";
            $cClausula .= ")";
            mysql_query($cClausula);
            $erro = mysql_error();
        }
        
        if (empty($erro)) {
            return true;
        } else {
            return false;
        }
        
    }

    /**
     * fin001atualizarParcelas
     *
     * @param Form $form
     * @return String/bool
     */     
    public function fin001atualizarParcelas($form)
    {
        global $g;
        
        $D024_Id                = $form->campoValorEnviado('D024_Id');
        $Total_Parcelas         = $form->campoValorEnviado('Total_Parcelas');
        $selecionaDias         = $form->campoValorEnviado('selecionaDias');
        $IntervalorDias         = $form->campoValorEnviado('IntervalorDias');
        $diaFixo                = $form->campoValorEnviado('diaFixo');
        $selecionaPrimeiroVencimento = $form->campoValorEnviado('selecionaPrimeiroVencimento');
        $primeiroVencimentoNum  = $form->campoValorEnviado('primeiroVencimentoNum');
        $primeiroVencimentoData = $form->campoValorEnviado('primeiroVencimentoData');
        $numeroParcelas         = $form->campoValorEnviado('Total_Parcelas');
        $D027_Id                = $form->campoValorEnviado('Portador_D027_Id');
        $Valor_Total_Orcamento  = $form->campoValorEnviado('valorSomado');
        $Ndcto                  = $form->campoValorEnviado('Ndcto');
        $Emissao                = $form->campoValorEnviado('Emissao');
        $Emissao                = gCorrigeDataInverte($Emissao);
        $valorAcrescimo         = $form->campoValorEnviado('valorAcrescimo');
        $valorDesconto          = $form->campoValorEnviado('valorDesconto');
        $valorCompra            = $form->campoValorEnviado('ValorCompra');
        $valorCompra            = isset($valorCompra) ? $valorCompra : 0;
        $valorCompra            = gCorrigeNumeroInverte($valorCompra);
        
        //return "teste";
        $extra = "T002_Flag_Reparcelar='S' AND T002_Flag_Cancelada<>'S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}' AND T002_D024_Id = '{$D024_Id}'";
        $sql   = mysql_query("SELECT * FROM T002 LEFT JOIN D024 on D024_Id=T002_D024_Id WHERE {$extra}");
        
        $textGeradores = "";
        $nValor_Titulo = 0;
        while ($row = mysql_fetch_assoc($sql)) {
            $nValor_Titulo += $row['T002_Valor_Saldo'];
            $textGeradores .= $row['T002_Id'] . ';' . $row['T002_Numero_Duplicata'] . ';' . $row['T002_Data_Emissao'] . ';' . $row['T002_Data_Vencimento'] . ';' . $row['T002_Valor_Duplicata'] . ';' . PHP_EOL;
        }
        if (!empty($valorAcrescimo)) {
            $valorAcrescimo    = gCorrigeNumeroInverte($valorAcrescimo);
            $AcrescimoDividido = gCorrigeNumero($valorAcrescimo / $Total_Parcelas);
            $AcrescimoDividido = gCorrigeNumeroInverte($AcrescimoDividido);
        } else {
            $AcrescimoDividido = "";
        }
        if (!empty($valorDesconto)) {
            $valorDesconto    = gCorrigeNumeroInverte($valorDesconto);
            $DescontoDividido = gCorrigeNumero($valorDesconto / $Total_Parcelas);
            $DescontoDividido = gCorrigeNumeroInverte($DescontoDividido);
        } else {
            $DescontoDividido = "";
        }
        
        // $cSql_D024=mysql_query("SELECT * FROM D024 WHERE D024_Id='{$D024_Id}'");
        // $mD024=mysql_fetch_array($cSql_D024);
        
        // se for "a combinar" remove as parcelas que ja existem
        if ($numeroParcelas == '0') {
            mysql_query("DELETE T180 FROM T180 WHERE T180_D024_Id='{$D024_Id}'");
            if (mysql_error())
                return mysql_error();
            else
                return true;
        }
        if (((empty($Ndcto)) || (!isset($Ndcto)) || ($Ndcto == '')) && $Total_Parcelas > 1) {
            return "Insira um Número Documento";
        }
        if (empty($Emissao)) {
            return "Insira uma data de Emissão";
        }
        if ($selecionaPrimeiroVencimento == 'Dias' && empty($primeiroVencimentoNum)) {
            return "Insira quantos dias para o vencimento no campo Primeira parcela!";
        }
        if ($selecionaPrimeiroVencimento == 'Data' && empty($primeiroVencimentoData)) {
            return "Insira a data do vencimento no campo Primeira parcela!";
        } 
        if ($Total_Parcelas <= 1){
            $IntervalorDias = 1;
        } else {
            if ($selecionaDias == 'I' && empty($IntervalorDias)) {
                return "Insira o intervalo de dias para vencimento no campo Demais parcela!";
            }
            if ($selecionaDias == 'F' && empty($diaFixo)) {
                return "Insira o dia do vencimento no campo Demais parcela!";
            }  
        }
        $cSql_T180 = mysql_query("SELECT * FROM T180 LEFT JOIN D027 on D027_Id=T180_D027_Id WHERE T180_D024_Id='{$D024_Id}'");
        while ($mT180 = mysql_fetch_array($cSql_T180)) {
            $mPrazoAntigo[$mT180['T180_Numero_Parcela']]    = $mT180['T180_Prazos_Dias'];
            $mPortadorAntigo[$mT180['T180_Numero_Parcela']] = $mT180['T180_D027_Id'];
        }
        
        mysql_query("DELETE T180 FROM T180 LEFT JOIN D027 on D027_Id=T180_D027_Id WHERE T180_D024_Id='{$D024_Id}'");
        
        
        //$Valor_Total_IPI=$mD024['D024_Valor_Total_IPI'];
        $Valor_Total_IPI = 0;
        
        if ($Total_Parcelas > 0) {
            $Valor_Total_Orcamento      = $Valor_Total_Orcamento + $valorCompra;
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
            //$count='1';
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                //Concatenando um caracter da tabela ASCII para adicionar no tabela
                //$Numero_Duplicata = $Ndcto.chr($count+64);
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D027_Id > 0) {
                    $mPortadorAntigo[$x] = $D027_Id;
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

                if($g['C031']['gerarVencTituloReceberFinalSemana'] == 'N'){
                    $diaSemana  = mysql_query("SELECT DATE_ADD('$Emissao',INTERVAL $mPrazoAntigo[$x] DAY) as diaSemana");
                    $mDiaSemana = mysql_fetch_array(($diaSemana));
                    $mDiaSemana['diaSemana'] = date('N', strtotime($mDiaSemana['diaSemana']));
                    if ($mDiaSemana['diaSemana'] == 6) { 
                        // Se for Sábado, pula para segunda-feira
                        $mPrazoAntigo[$x] =  $mPrazoAntigo[$x] + 2; 
                    } elseif ($mDiaSemana['diaSemana'] == 7) { // 
                        // Se for Domingo, pula para segunda-feira
                        $mPrazoAntigo[$x] =  $mPrazoAntigo[$x] + 1; 
                    }
                }
                
                $cClausula = "INSERT INTO T180 (";
                $cClausula .= "T180_D024_Id,";
                $cClausula .= "T180_C007_Id,";
                $cClausula .= "T180_D027_Id,";
                $cClausula .= "T180_Numero_Parcela,";
                $cClausula .= "T180_Prazos_Dias,";
                $cClausula .= "T180_Data_Vencimento,";
                $cClausula .= "T180_Valor_Parcela,";
                $cClausula .= "T180_Numero_Documento,";
                $cClausula .= "T180_Data_Emissao,";
                $cClausula .= "T180_Valor_Acrescimo,";
                $cClausula .= "T180_Valor_Desconto";
                $cClausula .= ") values (";
                $cClausula .= "'$D024_Id',";
                $cClausula .= "'{$g['usuarioAtual']}',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "'$mPrazoAntigo[$x]',";
                $cClausula .= "DATE_ADD('$Emissao',INTERVAL $mPrazoAntigo[$x] DAY),";
                $cClausula .= "'$Valor_Titulo',";
                $cClausula .= "'$Ndcto',";
                $cClausula .= "'$Emissao',";
                $cClausula .= "'{$AcrescimoDividido}',";
                $cClausula .= "'{$DescontoDividido}'";
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
            
            //$count='1';
            for ($x = 1; $x <= $Total_Parcelas; $x++) {
                //Concatenando um caracter da tabela ASCII para adicionar na tabela
                //$Numero_Duplicata = $Ndcto.chr($count+64);
                if ($x == 1) {
                    //$Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
                    $Valor_Titulo = $Valor_Titulo_Parcela + $Diferenca_Primeira_Parcela;
                } else {
                    $Valor_Titulo = $Valor_Titulo_Parcela;
                }
                if ($D027_Id > 0) {
                    $mPortadorAntigo[$x] = $D027_Id;
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
                    // poder calcular os dias até o vencimento da parcela (T180_Prazos_Dias)			    		
                    $date           = strtotime("+$numMeses month", strtotime($dataVencimento));
                    $dataPrazoDias  = date("Y-m-d", $date);
                    $numMeses       = $x - 1;
                }
                
                $cClausula = "INSERT INTO T180 (";
                $cClausula .= "T180_D024_Id,";
                $cClausula .= "T180_C007_Id,";
                $cClausula .= "T180_D027_Id,";
                $cClausula .= "T180_Numero_Parcela,";
                $cClausula .= "T180_Data_Vencimento,";
                $cClausula .= "T180_Prazos_Dias,";
                $cClausula .= "T180_Valor_Parcela,";
                $cClausula .= "T180_Numero_Documento,";
                $cClausula .= "T180_Data_Emissao,";
                $cClausula .= "T180_Valor_Acrescimo,";
                $cClausula .= "T180_Valor_Desconto";
                $cClausula .= ") values (";
                $cClausula .= "'$D024_Id',";
                $cClausula .= "'{$g['usuarioAtual']}',";
                $cClausula .= "'$mPortadorAntigo[$x]',";
                $cClausula .= "'$x',";
                $cClausula .= "DATE_ADD('$dataVencimento',INTERVAL $numMeses MONTH),";
                $cClausula .= "DATEDIFF('$dataPrazoDias','$Emissao'),";
                $cClausula .= "'$Valor_Titulo',";
                $cClausula .= "'$Ndcto',";
                $cClausula .= "'$Emissao',";
                $cClausula .= "'{$AcrescimoDividido}',";
                $cClausula .= "'{$DescontoDividido}'";
                $cClausula .= ")";
                mysql_query($cClausula);
                
                if (mysql_error()) {
                    return mysql_error();
                }
                //$count++ ;
            }
        }
        $textGerados = "";
        $sql         = mysql_query("SELECT * FROM T180 WHERE T180_D024_Id='{$D024_Id}'");
        while ($row = mysql_fetch_assoc($sql)) {
            $textGerados .= $row['T180_Id'] . ';' . $row['T180_Data_Vencimento'] . ';' . $row['T180_Valor_Parcela'] . ';' . PHP_EOL;
        }
        mysql_query("DELETE FROM T166 WHERE T166_D024_Id = '{$D024_Id}' AND T166_Tipo = 'S'");
        mysql_query("INSERT INTO T166 
 								(T166_D024_Id,
								 T166_C004_Id,
								 T166_Data_Emissao,
								 T166_Valor_Total,
								 T166_Titulos_Geradores,								 
								 T166_Titulos_Gerados,
								 T166_C007_Id,
								 T166_Tipo)
						   values				 
						       ('$D024_Id',
								'{$g['empresaAtual']}',
								curdate(),
								'{$nValor_Titulo}',
								'{$textGeradores}',
								'{$textGerados}',
								'{$g['usuarioAtual']}',
								'S')");
        return true;
    }
    
    /**
     * fin001calcularParcelaSimulacao
     *
     * @param Form $form
     * @return String
     */     
    public function fin001calcularParcelaSimulacao($form)
    {
        global $g;
        $D024_Id     = $form['D024_Id'];
        $numParcelas = $form['Total_Parcelas'];
        $valorTotal  = $form['valorSomado'];
        
        if ($numParcelas == "0")
            return '';
        
        return gCorrigeNumero($valorTotal / $numParcelas);
    }

    /**
     * validacoesTravarT002
     * @param int $T002_Id
     * @return String
     */     
    public function validacoesTravarT002($dadosQuery)
    {
        global $g;

        $sql = "SELECT T002_T007_Id, T007_Id,T002_Numero_Duplicata FROM T002 LEFT JOIN T007 ON T002_T007_Id=T007_Id LEFT JOIN T117 ON T002_T117_Id=T117_Id WHERE {$dadosQuery['where']}";
        $sqlT002 = mysql_query($sql);
        
        log("Valida T002: ".$sql);
        
        $T002Errados = array();
        while($T002=mysql_fetch_array($sqlT002)){
            if((!empty($T002['T002_T007_Id'])) && (empty($T002['T007_Id']))){
               $T002Errados[] = "<b>".$T002['T002_Numero_Duplicata']."</b><font style='font-size:10px;'> (T002_T007_Id: ".$T002['T002_T007_Id'].")</font><br>";
            }
        }

        if(count($T002Errados)>0){
            $duplicatas = implode("", $T002Errados);
            // return "<font style='color:red;font-weight:bold;'>ATENÇÃO!!!</font><br><br>As duplicatas abaixo".$duplicatas." estão sem nota fiscal vinculada, comunique o suporte técnico.</b> ";
            return "<font style='color:red;font-weight:bold;'>ATENÇÃO!!!</font><br><br>As notas vinculadas as duplicatas abaixo não constam no sistema, comunique o suporte técnico:<br>".$duplicatas;
        }

        return false;
    }

    /**
     * calcularParcelaCombo
     *
     * @param String $parcela
     * @param String $valorTotal
     * @return String
     */     
    public function calcularParcelaCombo($parcela, $valorTotal)
    {
        return gCorrigeNumero($valorTotal / $parcela);
    }
    
    /**
     * calcularParcelaCombo
     *
     * @param String $T180_Id
     * @param String $campoEditado
     * @param String $valorAntigo
     * @param String $novoValor
     * @param String $valorTotal
     * @return String/bool
     */      
    public function fin001AtualizarParcelas2($T180_Id, $campoEditado, $valorAntigo, $novoValor, $valorTotal)
    {
        global $g;
        
        $query            = "SELECT * FROM T180 LEFT JOIN D024 on T180_D024_Id='{$T180_Id}' WHERE T180_Id='{$T180_Id}'";
        $retorno          = mysql_query($query);
        $linha            = mysql_fetch_assoc($retorno);
        //$dataVelhaD024 = $linha['T005_Data_Emissao'];
        $dataVelhaD024    = '';
        $prazosDiasAntigo = $linha['T180_Prazos_Dias'];
        
        if ($campoEditado == 'T180_Prazos_Dias') {
            // if($valorAntigo == $novoValor){
            // return "O prazo é o mesmo";
            // }
            
            $date = new \DateTime($dataVelhaD024);
            $date->modify('+' . $novoValor . ' days');
            $query = "UPDATE T180 SET T180_Data_Vencimento='{$date->format('Y-m-d')}' WHERE T180_Id='{$T180_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T180_Data_Vencimento') {
            
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
                $query = "UPDATE T180 SET T180_Prazos_Dias='{$total}' WHERE T180_Id='{$T180_Id}'";
                mysql_query($query);
                return true;
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
                $query    = "UPDATE T180 SET T180_Prazos_Dias='{$total}' WHERE T180_Id='{$T180_Id}'";
                mysql_query($query);
                return true;
            }
            
        } else if ($campoEditado == 'T180_Valor_Parcela') {
            
            $query      = "SELECT * FROM T180 WHERE T180_Id='{$T180_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $D024_Id    = $linha['T180_D024_Id'];
            $numParcela = $linha['T180_Numero_Parcela'];
            
            // $query = "SELECT D024_Valor_Total FROM D024 WHERE AND D024_Id='{$D024_Id}'";
            // $retorno=mysql_query($query);		
            // $linha = mysql_fetch_assoc($retorno);	
            $valorTotal = $valorTotal;
            
            $query                  = "SELECT SUM(T180_Valor_Parcela) FROM T180 WHERE T180_D024_Id='{$D024_Id}'AND T180_C007_Id='{$g['usuarioAtual']}'  AND T180_Numero_Parcela<='{$numParcela}'";
            $retorno                = mysql_query($query);
            $linha                  = mysql_fetch_assoc($retorno);
            $valorParcelasRestantes = $linha['SUM(T180_Valor_Parcela)'];
            $valorRestante          = $valorTotal - $valorParcelasRestantes;
            
            $query             = "SELECT * FROM T180 WHERE T180_D024_Id='{$D024_Id}' AND T180_C007_Id='{$g['usuarioAtual']}' AND T180_Numero_Parcela>'{$numParcela}'";
            $retorno           = mysql_query($query);
            $parcelasRestantes = mysql_num_rows($retorno);
            //se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
            if ($parcelasRestantes != 0) {
                $valorParcelasRestantes = $valorRestante / $parcelasRestantes;
                if ($valorParcelasRestantes < 0) {
                    $query = "UPDATE T180 SET T180_Valor_Parcela='{$valorAntigo}' WHERE T180_Id='{$T180_Id}'";
                    mysql_query($query);
                    return 'O valor inserido irá gerar parcelas negativas...<br>Favor alterar novamente';
                }
                
                while ($linha = mysql_fetch_assoc($retorno)) {
                    $query = "UPDATE T180 SET T180_Valor_Parcela='{$valorParcelasRestantes}' WHERE T180_Id='{$linha['T180_Id']}'";
                    mysql_query($query);
                }
            }
            return true;  
        }   
        return true;
    }
    
    /**
     * fin001removerRemessa
     *
     * @param String $T035_Id
     * @return bool
     */ 
    public function fin001removerRemessa($T035_Id)
    {
        
        mysql_query("START TRANSACTION");
        mysqli_query("UPDATE T117 SET T117_T035_Id=NULL WHERE T117_T035_Id='{$T035_Id}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "T117 " .mysql_error();
        }
        
        mysqli_query("UPDATE T002 SET T002_T035_Id=NULL WHERE T002_T035_Id='{$T035_Id}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "T002 " .mysql_error();
        }
        
        mysqli_query("UPDATE T035 SET T035_Numero_Titulos=0,T035_Total_Remessa=0.00 WHERE T035_Id='{$T035_Id}'");
        if (mysql_error()) {
            mysql_query("ROLLBACK");
            return "T035 " .mysql_error();
        }
        
        mysql_query("COMMIT");
        return true;   
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
    public function duplicarRegistros($acaoId, $numeroParcelas)
    {
        global $g;
        
        /* Atualiza o numero de parcelas da Conta Padrao */
        mysql_query("
						UPDATE T002 SET
							T002_Numero_Parcela = '1',
							T002_Total_Parcelas = '{$numeroParcelas}',
							T002_Id_Padrao = '{$acaoId}'
						WHERE T002_Id = '{$acaoId}'
			");
        
        for ($i = 1; $i <= ($numeroParcelas - 1); $i++) {
            $sql = mysql_query("SELECT * FROM T002 WHERE T002_Id='{$acaoId}'");
            
            $open = mysql_fetch_row($sql);
            
            /* Variavel que armazena os valores que nao serao alerados */
            $valores = '';
            $valores .= "'',";
            /* Para nao duplicar o ID */
            
            /* Lista todos os valores para duplicar*/
            for ($a = 1; $a < 89; $a++) {
                $valores .= "'" . $open[$a] . "'" . ',';
            }
            
            /* Trata os novos valores da parcela */
            $dataEmissao    = $this->validaDiaMes($open[22]);
            /* Data Emissao */
            $dataVencimento = $this->validaDiaMes($open[23]);
            /* Data Vencimento */
            
            $dataRecebimento = $open[24];
            /* Data Recebimento */
            
            /* Retira a ultima virgula */
            $valores = strrev(substr(strrev($valores), 1));
            
            mysql_query("
						INSERT INTO T002 VALUES
						(
							{$valores}
						); 
			");
            
            $idAtual      = $g['mysqlLastId'];
            $parcelaAtual = $i + 1;
            
            mysql_query("
						UPDATE T002 SET
							T002_Numero_Parcela = '{$parcelaAtual}',
							T002_Total_Parcelas = '{$numeroParcelas}',
							T002_Data_Emissao = '{$dataEmissao}',
							T002_Data_Vencimento = '{$dataVencimento}',
							T002_Data_Recebimento = '{$dataRecebimento}'
						WHERE T002_Id = '{$idAtual}'
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
					DELETE FROM T002 WHERE T002_Id_Padrao = '{$acaoId}' AND T002_Id !='{$acaoId}'
		");
        
        $this->duplicarRegistros($acaoId, $numeroParcelas);
    }
    
    /**
     * montarEmailSimulacaoReparcelamento
     *
     * @param String $D024_Id
     * @return array
     */     
    public function montarEmailSimulacaoReparcelamento($D024_Id)
    {
        global $g, $confUsuario;
        $array = array();
        
        $T166    = mysql_query("SELECT * FROM T166 LEFT JOIN D024 ON D024_Id = T166_D024_Id LEFT JOIN D020 on D020_Id=D024_D020_Id LEFT JOIN D018 on D018_Id=D024_D018_Id WHERE T166_D024_Id = '{$D024_Id}' AND T166_Tipo = 'S'");
        $T166    = mysql_fetch_array($T166);
        $T166_Id = $T166['T166_Id'];
        
        $array['Para']    = $T166['D024_E_Mail'];
        $array['Assunto'] = "Simulação de Reparcelamento";
        
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
        $corpo = '<div style="border:2px solid #E0EEEE; padding-top:10px;padding-bottom:10px;font-size:12px;font-family:Sans-Serif;">';
        if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } else {
            $caminhoImagem = "";
        }
        $corpo .= '<img src="' . $caminhoImagem . '" style="margin-left:25px"><br /><br />';
        // <br />Seguem abaixo preços e condições<br />
        if ($T166['T166_Tipo'] == "S") {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">SIMULAÇÃO DE REPARCELAMENTO Nº: <span style="font-weight:normal;">' . $T166_Id . '</span></div>';
        }
        
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Cliente/Fornecedor: <span style="font-weight:normal;">' . $T166['D024_Nome_Empresa'] . '</span></div>';
        
        if (!empty($T166['D024_Cnpj'])) {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">CNPJ: <span style="font-weight:normal;">' . $T166['D024_Cnpj'] . '</span></div>';
        } elseif (!empty($T166['D024_Cpf'])) {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">CPF: <span style="font-weight:normal;">' . $T166['D024_Cpf'] . '</span></div>';
        }
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Cidade: <span style="font-weight:normal;">' . $T166['D020_Nome_Cidade'] . ' - ' . $T166['D018_UF'] . '</span></div>';
        $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Telefone: <span style="font-weight:normal;">(' . $T166['D024_DDD_Telefone_1'] . ') ' . $T166['D024_Telefone_1'] . '</span></div>';
        if ($T166['T166_Tipo'] == "S") {
            $corpo .= '<div style="margin-left:25px;font-family:Sans-Serif;font-weight:bold">Tipo: <span style="font-weight:normal;">Reparcelamento</span></div>';
        }
        
        // tabela de geradores
        $corpo .= '<div style="margin-left:25px;margin-top:25px;font-size:13px;font-family:Sans-Serif;font-weight:bold">Títulos Geradores</div>';
        $corpo .= '<table style="' . $tabela . '">';
        $corpo .= '<tbody><tr><th style="' . $th . '">Duplicata</th><th style="' . $th . '" align="center" width=80>Emissão</th></th><th style="' . $th . '" align="center" width=80>Vencimento</th><th style="' . $th . '" align="center" width=80>Portador</th><th style="' . $th . '" align="center" width=70px>Valor</th></tr>';
        
        $T166A   = mysql_query("SELECT T166_Titulos_Geradores FROM T166 WHERE T166_Id='{$T166_Id}'");
        $aT166   = mysql_fetch_array($T166A);
        $res     = explode("\n", $aT166['T166_Titulos_Geradores']);
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
        $valorReparcelado = 0;
        $T002A            = mysql_query("SELECT T002_Id,T002_Numero_Duplicata,T002_Data_Vencimento,T002_Data_Emissao,T002_Valor_Duplicata,D027_Portador FROM T002 LEFT JOIN D027 on D027_Id=T002_D027_Id WHERE T002_Id IN ({$indices})");
        while ($linha = mysql_fetch_assoc($T002A)) {
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . '">';
            $corpo .= $linha['T002_Numero_Duplicata'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T002_Data_Emissao']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T002_Data_Vencimento']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= $linha['D027_Portador'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($linha['T002_Valor_Duplicata']);
            $corpo .= '</td>';
            $corpo .= '</tr>';
            $valorReparcelado += $linha['T002_Valor_Duplicata'];
        }
        
        // tabela de gerados
        $T166A       = mysql_query("SELECT T166_Titulos_Gerados FROM T166 WHERE T166_Id='{$T166_Id}'");
        $aT166       = mysql_fetch_array($T166A);
        $res         = explode("\n", $aT166['T166_Titulos_Gerados']);
        $indices     = "";
        $valorGerado = 0;
        foreach ($res as $elemento) {
            if ($elemento != "") {
                $part = explode(";", $elemento);
                $indices .= $part[0] . ",";
                $valorGerado += $part[2];
            }
        }
        while (substr($valorGerado, -1, 1) == ",") {
            $valorGerado = substr_replace($valorGerado, '', -1);
        }
        
        while (substr($indices, -1, 1) == ",") {
            $indices = substr_replace($indices, '', -1);
        }
        //Verifica se o valor reparcelado inclui uma nova compra
        $valorReparcelado = gCorrigeNumero($valorGerado);
        if (($valorReparcelado > gCorrigeNumero($T166['T166_Valor_Total']))) {
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . 'font-weight: bold;">';
            $corpo .= 'Subtotal';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= '';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= '';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;font-weight: bold;">';
            $corpo .= gCorrigeNumero($T166['T166_Valor_Total']);
            $corpo .= '</td>';
            $corpo .= '</tr>';
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . 'font-weight: bold;">';
            $corpo .= 'Nova Compra';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= '';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= '';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;font-weight: bold;">';
            $corpo .= gCorrigeNumero($valorGerado - $T166['T166_Valor_Total']);
            $corpo .= '</td>';
            $corpo .= '</tr>';
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . 'font-weight: bold;">';
            $corpo .= 'Total';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= '';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= '';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;font-weight: bold;">';
            $corpo .= gCorrigeNumero($valorGerado);
            $corpo .= '</td>';
            $corpo .= '</tr>';
        } else {
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . 'font-weight: bold;">';
            $corpo .= 'Total';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= '';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= '';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= '';
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;font-weight: bold;">';
            $corpo .= gCorrigeNumero($T166['T166_Valor_Total']);
            $corpo .= '</td>';
            $corpo .= '</tr>';
        }
        $corpo .= '</tbody>';
        $corpo .= '</table>';
        
        $corpo .= '<div style="margin-left:25px;margin-top:25px;font-size:13px;font-family:Sans-Serif;font-weight:bold">Títulos Gerados</div>';
        $corpo .= '<table style="' . $tabela . '">';
        $corpo .= '<tbody><tr><th style="' . $th . '">Duplicata</th><th style="' . $th . '" align="center" width=80>Emissão</th></th><th style="' . $th . '" align="center" width=80>Vencimento</th><th style="' . $th . '" align="center" width=80>Portador</th><th style="' . $th . '" align="center" width=70px>Valor</th></tr>';
        
        $valorGerado = 0;
        $T180        = mysql_query("SELECT * FROM T180 LEFT JOIN D027 on D027_Id=T180_D027_Id WHERE T180_Id IN ({$indices})");
        while ($linha = mysql_fetch_assoc($T180)) {
            
            $corpo .= '<tr>';
            $corpo .= '<td style="' . $td . '">';
            $corpo .= $linha['T180_Numero_Documento'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T180_Data_Emissao']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= gCorrigeData($linha['T180_Data_Vencimento']);
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:center;">';
            $corpo .= $linha['D027_Portador'];
            $corpo .= '</td>';
            $corpo .= '<td style="' . $td . 'text-align:right;">';
            $corpo .= gCorrigeNumero($linha['T180_Valor_Parcela']);
            $corpo .= '</td>';
            $corpo .= '</tr>';
            $valorGerado = $valorGerado + $linha['T180_Valor_Parcela'];
        }
        $corpo .= '<tr>';
        $corpo .= '<td style="' . $td . 'font-weight: bold;">';
        $corpo .= 'Total';
        $corpo .= '</td>';
        $corpo .= '<td style="' . $td . 'text-align:center;">';
        $corpo .= '';
        $corpo .= '</td>';
        $corpo .= '<td style="' . $td . 'text-align:center;">';
        $corpo .= '';
        $corpo .= '</td>';
        $corpo .= '<td style="' . $td . 'text-align:center;">';
        $corpo .= '';
        $corpo .= '</td>';
        $corpo .= '<td style="' . $td . 'text-align:right;font-weight: bold;">';
        $corpo .= gCorrigeNumero($valorGerado);
        $corpo .= '</td>';
        $corpo .= '</tr>';
        $corpo .= '</tbody>';
        $corpo .= '</table>';
        // valor total
        $corpo .= '<div style="width: 300px; margin: 80px 0 0 25px; border-top: 1px solid #000; text-align: center;">' . date("d/m/Y") . ', ' . $T166['D024_Nome_Empresa'] . '</div>';
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
        $array['D024_Id'] = $T166['T166_D024_Id'];
        //$array['T139_Valor_Total'] = $T003['T003_Valor_Total'];
        // $sql = "SELECT COUNT(*) FROM T004 WHERE T004_T003_Id='{$T003_Id}'";
        // $registros = mysql_query($sql);
        // $registros = mysql_fetch_array($registros);
        // $array['T139_Numero_Registros'] = $registros[0];
        
        return $array;
    }
    
    /**
     * FIN001AlterarDataVencimento
     *
     * @param String $data
     * @param String $r_extra
     * @return String/bool
     */     
    public function FIN001AlterarDataVencimento($data, $r_extra)
    {
        global $g;
        
        if (empty($data)) {
            return "Insira uma data";
        }
        if (empty($r_extra)) {
            return "Não foi recebido o parâmetro 'extra'. Por favor, contato o suporte técnico. FIN001->FIN001AlterarDataVencimento();";
        }
        $data   = gCorrigeDataInverte($data);
        $extra  = base64_decode($r_extra);
        $extra  = gInsertExtraWhere($extra, "T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}'");
        $result = "SELECT * FROM T002 " . $extra;
        $result = mysql_query($result);
        if (mysql_num_rows($result) == 0) {
            return "Não foi selecionado nenhum título para alterar a data.";
        }
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        while ($linha = mysql_fetch_array($result)) {
            $up      = <<<EOT
			UPDATE T002 set T002_Data_Vencimento='{$data}' WHERE T002_Id='{$linha['T002_Id']}';
EOT;
            $result2 = mysql_query($up);
            $erro    = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return "Erro 1: " . $erro;
            }
        }
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * montarEmailPdf
     *
     * @param String $T002_Id
     * @return array
     */ 
    public function montarEmailPdf($T002_Id)
    {
        global $g;
        
        $T002 = mysql_query("SELECT * FROM T002 
						LEFT JOIN D024 ON D024_Id = T002_D024_Id 
						LEFT JOIN T007 ON T007_Id = T002_T007_Id
						WHERE T002_Id = '{$T002_Id}'");
        $T002 = mysql_fetch_array($T002);
        
        $r_link = '/fin/fin001/outro/fin001Carta/?ajax=true&T002_Id=' . $T002_Id;
        $dir    = $g['pathDados'] . 'pdf/';
        is_dir($dir) or mkdir($dir, 0777, true);
        $filename = 'cobranca_' . $T002_Id . '.pdf';
        $file     = $dir . $filename;
        $fileFull = $g['pathRaiz'] . '/' . $file;
        
        $r_link = str_replace(' ', '+', $r_link);
        shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
        
        
        $array                = array();
        $array['Para']        = $T002['D024_E_Mail'];
        $array['Assunto']     = "Carta de Cobrança";
        $array['Corpo']       = <<<EOT
		<font style='font-size:14px'><b>{$T002['D024_Nome_Empresa']}</b><br /><br />
			Segue em anexo a Cobrança referente a NFe <b>{$T002['T007_Numero_Nota_Fiscal']}</b>.
		</font>
EOT;
        $array['Anexos'][]    = $file;
        $array['AnexosWeb'][] = '/hardness3/' . $file;
        
        return $array;
    }
    
    /**
     * duplicarTitulos
     *
     * @param String $dias
     * @param String $extra
     * @return String/bool
     */     
    public function duplicarTitulos($dias, $extra)
    {
        global $g;
        $extra = base64_decode(urldecode($extra));
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T002_Flag_Reparcelar='S'"));
        $res   = mysql_query("SELECT DISTINCT T002_Id FROM T002 
		LEFT JOIN D024 ON D024_Id = T002_D024_Id 
		LEFT JOIN D027 ON D027_Id = T002_D027_Id 
		LEFT JOIN T117 ON T117_Id = T002_T117_Id 
		LEFT JOIN D014 ON D014_Id = T002_D014_Id 
		LEFT JOIN D046 ON D046_Id = D024_D046_Id 
		LEFT JOIN T007 ON T007_Id = T002_T007_Id 
		LEFT JOIN T035 ON T035_Id = T002_T035_Id 
		LEFT JOIN C007 ON C007_Id = T007_C007_Id_Vendedor_Interno 
		LEFT JOIN T103 ON T002_Id = T103_T002_Id 
		WHERE {$extra}");
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($row = mysql_fetch_array($res)) {
            // Selecionando o título
            $T002  = mysql_query("SELECT * FROM T002 WHERE T002_Id='{$row['T002_Id']}'");
            $mT002 = mysql_fetch_assoc($T002);
            
            if (mysql_num_rows($T002) == 0) {
                return "Erro: Título não encontrado";
            }
            
            $campos  = "";
            $valores = "";
            foreach ($mT002 as $key => $value) {
                if ($key == 'T002_Id') {
                    continue;
                } else if ($key == 'T002_Data_Emissao') {
                    $Data_Emissao = date("Y-m-d", strtotime(date("Y-m-d", strtotime($value)) . " +{$dias} day"));
                    $campos .= $key . ", ";
                    $valores .= "'" . $Data_Emissao . "',";
                    
                } else if ($key == 'T002_Data_Vencimento') {
                    $Data_Vencimento = date("Y-m-d", strtotime(date("Y-m-d", strtotime($value)) . " +{$dias} day"));
                    $campos .= $key . ", ";
                    $valores .= "'" . $Data_Vencimento . "',";
                    
                } else if ($key == 'T002_Flag_Reparcelar') {
                    $campos .= $key . ", ";
                    $valores .= "'N',";
                    
                } else if ($key == 'T002_T117_Id' || $key == 'T002_T035_Id') {
                    $campos .= $key . ", ";
                    $valores .= "'',";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT002 = "INSERT INTO T002 ({$campos}) VALUES ({$valores})";
            $resT002 = mysql_query($insT002);
            $erro    = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "Erro 1: " . $erro;
            }
        }
        
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * cancelarTitulo
     *
     * @param String $T002_Id
     * @return String/bool
     */ 
    public function cancelarTitulo($T002_Id)
    {
        global $g;
        
        $sqlT002 = mysql_query("SELECT T002_Valor_Total FROM T002 WHERE T002_Id = '{$T002_Id}'");
        $erro    = mysql_error();
        if (!empty($erro)) {
            return $erro;
        }
        $T002 = mysql_fetch_array($sqlT002);
        
        $sql = "INSERT INTO T020 (T020_T002_Id,
								T020_C004_Id,
								T020_Data_Lancamento,
								T020_Hora_Lancamento,
								T020_Valor,
								T020_Flag_Deb_Cred,
								T020_Historico
							) VALUES (
								'{$T002_Id}',
								'{$g['empresaAtual']}',
								CURRENT_DATE(),
								CURRENT_TIME(),
								'{$T002['T002_Valor_Total']}',
								'D',
								'CANCELADO')";
        mysql_query($sql);
        $erro = mysql_error();
        if (!empty($erro)) {
            return $erro;
        }
        
        mysql_query("UPDATE T002 SET T002_Flag_Cancelada = 'S' WHERE T002_Id = '{$T002_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            return $erro;
        }
        return true;
        
    }
    
    /**
     * agrupamentoAutomatico
     *
     * @param String $extra
     * @return bool
     */ 
    public function agrupamentoAutomatico($extra)
    {
        return true;
    }
    
    /**
     * agrupamentoAutomatico
     *
     * @param String $dataInicio
     * @param String $dataFim
     * @param String $periodo
     * @return string/array
     */     
    public function serasaRelatoNormal($dataInicio, $dataFim, $periodo)
    {
        global $g;
        
        if (empty($dataInicio)) {
            return array(
                "Campo 'Data Início' não foi preenchido!"
            );
        }
        if (empty($dataFim)) {
            return array(
                "Campo 'Data Fim' não foi preenchido!"
            );
        }
        if (empty($periodo)) {
            return array(
                "Campo 'Periodicidade' não foi preenchido!"
            );
        }
        
        $dataInicio = substr(gCorrigeDataInverte($dataInicio),0,10);
        $dataFim    = substr(gCorrigeDataInverte($dataFim),0,10);
        
        $header        = '';
        $countTempoRel = 0;
        $detalhe       = '';
        $countTitulos  = 0;
        $trailler      = '';
        
        /*
        HEADER
        */
        $header = '00' . 'RELATO COMP NEGOCIOS' . str_replace(array(
            '.',
            '/',
            '-'
        ), array(
            '',
            '',
            ''
        ), $g['C004']['C004_Cnpj']) . str_replace('-', '', $dataInicio) . str_replace('-', '', $dataFim) . $periodo . str_pad('', 15) . str_pad('', 3) . str_pad('', 29) . 'V.' . '01' . str_pad('', 26);
        
        /*
        DETALHE
        */
        /*
        Tempo de relacionamento
        */
        $sqlD024 = <<<EOT
		SELECT
			*
		FROM
			D024
		WHERE
			(D024_Data_Cadastro >= '{$dataInicio}' AND D024_Data_Cadastro <= '{$dataFim}')
EOT;
        $resD024 = mysql_query($sqlD024);
        while ($rowD024 = mysql_fetch_assoc($resD024)) {
            $cnpj = str_replace(array(
                '.',
                '/',
                '-'
            ), array(
                '',
                '',
                ''
            ), $rowD024['D024_Cnpj']);
            if (strlen($cnpj) != 14) {
                continue;
            }
            $D024_Data_Cadastro = str_replace('-', '', $rowD024['D024_Data_Cadastro']);
            $D024_Data_Cadastro = explode(' ', $D024_Data_Cadastro);
            $D024_Data_Cadastro = $D024_Data_Cadastro[0];
            /*if ($D024_Data_Cadastro == '00000000' || empty($D024_Data_Cadastro)) {
            continue;
            }
            $datetime1 = new \DateTime($rowD024['D024_Data_Cadastro']);
            $datetime2 = new \DateTime();
            $interval = $datetime1->diff($datetime2);
            // ($interval->days > 365 ? '1' : '2') .
            */
            //
            $detalhe .= ($detalhe) ? "\r\n" : '';
            $detalhe .= '01' . $cnpj . '01' . $D024_Data_Cadastro . '2' . str_pad('', 38) . str_pad('', 34) . str_pad('', 01) . str_pad('', 30);
            //
            $countTempoRel++;
        }
        
        /*
        Pega todas as movimentações do perído:
        - Título novo
        - Alteração de vencimento
        - Data do pagamento
        - Exclusão do título
        */
        $sqlT002 = <<<EOT
		SELECT
			*
		FROM
			T002
		LEFT JOIN D024 ON D024_Id=T002_D024_Id
		WHERE
			(T002_Data_Emissao >= '{$dataInicio}' AND T002_Data_Emissao <= '{$dataFim}')
			OR
			(T002_Data_Vencimento >= '{$dataInicio}' AND T002_Data_Vencimento <= '{$dataFim}')
			OR
			(T002_Data_Recebimento >= '{$dataInicio}' AND T002_Data_Recebimento <= '{$dataFim}')
			OR
			(T002_Data_Exclusao >= '{$dataInicio}' AND T002_Data_Exclusao <= '{$dataFim}')
EOT;
        $resT002 = mysql_query($sqlT002);
        if (!mysql_num_rows($resT002)) {
            return array(
                'Sem lançamentos no período!'
            );
        }
        $dups = array();
        while ($rowT002 = mysql_fetch_assoc($resT002)) {
            $cnpj = str_replace(array(
                '.',
                '/',
                '-'
            ), array(
                '',
                '',
                ''
            ), $rowT002['D024_Cnpj']);
            if (strlen($cnpj) != 14) {
                continue;
                //die("Cliente '{$rowT002['D024_Nome_Empresa']}' sem CNPJ.");
            }
            $T002_Data_Emissao     = str_replace('-', '', $rowT002['T002_Data_Emissao']);
            $T002_Data_Vencimento  = str_replace('-', '', $rowT002['T002_Data_Vencimento']);
            $T002_Data_Recebimento = str_replace('-', '', $rowT002['T002_Data_Recebimento']);
            // Correção erro: "DATA DE PAGAMENTO SUPERIOR A DATA FINAL (DT ATE) DA REMESSA"
            $data = new \DateTime($rowT002['T002_Data_Recebimento']);
            $data2 = new \DateTime($dataFim);
            if ($T002_Data_Recebimento == '00000000' || empty($T002_Data_Recebimento) || $data > $data2) {
                $T002_Data_Recebimento = '        ';
            }
            $T002_Valor_Duplicata = $rowT002['T002_Valor_Duplicata'];
            $T002_Valor_Duplicata = str_replace('.', '', $rowT002['T002_Valor_Duplicata']);
            // Para excluir um título, enviar 9999999999999 no valor
            $T002_Data_Exclusao   = str_replace('-', '', $rowT002['T002_Data_Exclusao']);
            if ($T002_Data_Exclusao != '00000000' && !empty($T002_Data_Exclusao)) {
                $T002_Valor_Duplicata = '9999999999999';
            }

            $nr1 = str_pad(str_replace(',', '', substr($rowT002['T002_Numero_Duplicata'], 0,10)), 10);  
            $nr2 = str_pad('', 34);

            // Enviará sempre as dez primeiras posições do número do documento
            /*if (strlen($rowT002['T002_Numero_Duplicata']) <= 10) {
                $nr1 = str_pad($rowT002['T002_Numero_Duplicata'], 10);
                $nr2 = str_pad('', 34);
            } else {
                $nr1 = str_pad('', 10);
                $nr2 = '#D' . str_pad($rowT002['T002_Numero_Duplicata'], 32);
            }*/
            // Correção erro: "REGISTRO DE PAGTO INFORMADO EM DUPLICIDADE (VENC/NUM. DO TITULO)"
            if (isset($dups[$nr1])) {
                continue;
            }
            $dups[$nr1] = true;
            // Correção erro: "VALOR DO TITULO INVALIDO"
            if (empty($T002_Valor_Duplicata) || $T002_Valor_Duplicata <= 0) {
                continue;
            }
            $countTitulos++;
            $detalhe .= ($detalhe) ? "\r\n" : '';
            $detalhe .= '01' . $cnpj . '05' . $nr1 . str_pad($T002_Data_Emissao, 8) . str_pad($T002_Valor_Duplicata, 13, '0', STR_PAD_LEFT) . str_pad($T002_Data_Vencimento, 8) . str_pad($T002_Data_Recebimento, 8) . $nr2 . str_pad('', 1) . str_pad('', 24) . str_pad('', 2) . str_pad('', 1) . str_pad('', 1) . str_pad('', 2);
        }
        
        /*
        TRAILLER
        */
        $trailler = '99' . str_pad($countTempoRel, 11, '0', STR_PAD_LEFT) . str_pad('', 44) . str_pad($countTitulos, 11, '0', STR_PAD_LEFT) . str_pad('', 11) . str_pad('', 11) . str_pad('', 10) . str_pad('', 30);
        
        return $header . "\r\n" . $detalhe . "\r\n" . $trailler;
    }
    
    /**
     * serasaRelatoConciliacao
     *
     * @param String $arquivo
     * @return String
     */     
    public function serasaRelatoConciliacao($arquivo)
    {
        $retorno = '';
        
        $arquivo = explode("\n", $arquivo);
        foreach ($arquivo as $key => $linha) {
            $linha = str_replace(array(
                "\n",
                "\r"
            ), array(
                '',
                ''
            ), $linha);
            if ($key == 0) {
                $retorno .= $linha . "\r\n";
                $dataUltRemessa = new \DateTime(substr($linha, 44, 8));
                continue;
            } else if ($key == count($arquivo) - 1) {
                $retorno .= $linha;
                continue;
            } else if ( ($key == count($arquivo) - 2) && substr($linha, 0, 2) == '99') {
                $retorno .= $linha;
                continue;
            }
            
            $T002_Numero_Duplicata  = substr($linha, 18, 10);
            $T002_Numero_Duplicata_ = trim($T002_Numero_Duplicata);
            $D024_Cnpj              = substr($linha, 2, 14);
            $D024_Cnpj              = substr($D024_Cnpj, 0, 2) . "." . substr($D024_Cnpj, 2, 3) . "." . substr($D024_Cnpj, 5, 3) . "/" . substr($D024_Cnpj, 8, 4) . "-" . substr($D024_Cnpj, 12, 2);

            $sql                    = "SELECT T002_Data_Recebimento 
                                         FROM T002 
                                    LEFT JOIN D024 ON T002_D024_Id = D024_Id
                                        WHERE T002_Numero_Duplicata = '{$T002_Numero_Duplicata_}'
                                          AND D024_Cnpj = '{$D024_Cnpj}'";
            $res = mysql_query($sql);

            if (!mysql_num_rows($res)) {
                //echo $T002_Numero_Duplicata . "\n";
                $linhaNova = substr($linha, 0, 57) . '        ' . substr($linha, 65, -1);
                $retorno .= $linhaNova . "\r\n";
            } else {
                $row                   = mysql_fetch_assoc($res);
                $T002_Data_Recebimento = str_replace('-', '', $row['T002_Data_Recebimento']);
                if ($T002_Data_Recebimento == '00000000' || empty($T002_Data_Recebimento)) {
                    $T002_Data_Recebimento = '        ';
                } else {
                    // Correção para erro "DT PAGTO (ARQ.CORRECAO) MAIOR DT ULTIMA REMESSA PRECESSADA"
                    $data = new \DateTime($row['T002_Data_Recebimento']);
                    if ($dataUltRemessa < $data) {
                        $T002_Data_Recebimento = '        ';
                    }
                }
                $linhaNova = substr($linha, 0, 57) . $T002_Data_Recebimento . substr($linha, 65, -1);
                $retorno .= $linhaNova . "\r\n";
            }
        }
        return $retorno;
    }

    /**
     * processarSaldoCliente
     *
     * @param String $extra
     * @return String/Bool
     */
    public function processarSaldoCliente($extra)
    {
        global $g;
        $sqlD024 = mysql_query("SELECT D024_Id FROM T002 {$extra} GROUP BY D024_Id");
        while($rowD024 = mysql_fetch_array($sqlD024)){
            mysql_query("CALL D024_Gravar_Totalizacao('{$rowD024['D024_Id']}')");
            $erro = mysql_error();
            if (!empty($erro)) {
                return $erro;
            }
            
        }
        return true;
    }

	/**
	 * Usa o saldo positivo de crédito troca em um título a receber do mesmo
	 * cliente. O débito do crédito é registrado em um título auxiliar com
	 * portador flag 4, preservando a totalização e a rastreabilidade.
	 *
	 * @param int $clienteId
	 * @param int $tituloId
	 * @return array|string
	 */
	public function abaterSaldoCreditoTrocaEmTitulo($clienteId, $tituloId)
	{
		global $g;

		$clienteId = (int)$clienteId;
		$tituloId = (int)$tituloId;
		if ($clienteId <= 0 || $tituloId <= 0) {
			return 'Cliente ou título inválido para abatimento.';
		}

		$empresaAtual = (int)$g['empresaAtual'];
		$filtroEmpresaCliente = '';
		if ($g['C031']['separarClientePorEmpresa'] === 'S') {
			$filtroEmpresaCliente = " AND (IFNULL(D024_C004_Id, 0) = '{$empresaAtual}' OR IFNULL(D024_C004_Id, 0) = 0)";
		}

		$sqlPortador = mysql_query("SELECT D027_Id, D027_Portador, D027_D007_Id, D007_Numero_Agencia, D007_Numero_Conta
			FROM D027
			LEFT JOIN D007 ON D007_Id = D027_D007_Id
			WHERE D027_Flag_Credito_Antecipado = '4'
				AND D027_Flag_Ativo = 'S'
				AND D027_Flag_Baixa_Automatica = 'S'
				AND IFNULL(D027_Baixa_Automatica_Dias, 0) = 0
				AND IFNULL(D027_D007_Id, 0) > 0
				AND D007_Flag_Ativo = 'S'
				AND D007_C004_Id = '{$empresaAtual}'");
		if (!$sqlPortador) {
			return 'Não foi possível validar o portador de abatimento: ' . mysql_error();
		}
		if (mysql_num_rows($sqlPortador) !== 1) {
			return 'É necessário manter exatamente um portador ativo de Abatimento Crédito Troca, com baixa automática, zero dias e conta bancária ativa para a empresa atual.';
		}
		$portador = mysql_fetch_assoc($sqlPortador);

		if (!mysql_query('SET AUTOCOMMIT=0') || !mysql_query('START TRANSACTION')) {
			$erro = mysql_error();
			mysql_query('SET AUTOCOMMIT=1');
			return 'Não foi possível iniciar o abatimento: ' . $erro;
		}

		$sqlCliente = mysql_query("SELECT D024_Id, D024_Nome_Empresa
			FROM D024
			WHERE D024_Id = '{$clienteId}'
				AND D024_Flag_Ativo = 'S'
				AND D024_Flag_Cliente = 'S'
				{$filtroEmpresaCliente}
			FOR UPDATE");
		if (!$sqlCliente || mysql_num_rows($sqlCliente) !== 1) {
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'O cliente não existe, não está ativo ou não pertence à empresa atual.';
		}
		$cliente = mysql_fetch_assoc($sqlCliente);

		if (!mysql_query("CALL D024_Gravar_Totalizacao('{$clienteId}')")) {
			$erro = mysql_error();
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'Não foi possível recalcular o saldo de crédito troca: ' . $erro;
		}
		$sqlSaldo = mysql_query("SELECT D024_Valor_Credito_Troca FROM D024 WHERE D024_Id = '{$clienteId}'");
		if (!$sqlSaldo) {
			$erro = mysql_error();
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'Não foi possível consultar o saldo de crédito troca: ' . $erro;
		}
		$saldoCredito = round((float)mysql_result($sqlSaldo, 0), 2);
		if ($saldoCredito < 0.01) {
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'O cliente não possui mais saldo positivo de crédito troca.';
		}

		$sqlTitulo = mysql_query("SELECT T002_Id, T002_D024_Id, T002_D014_Id, T002_Numero_Duplicata,
			T002_Numero_Agencia, T002_Numero_Conta, T002_Numero_Cheque, T002_Valor_Saldo, T002_T117_Id,
			(SELECT T117_Id FROM T117 WHERE T117_T002_Id = T002.T002_Id AND IFNULL(T117_Flag_Cancelado, 'N') != 'S' LIMIT 1) AS T117_Id_Vinculado
			FROM T002
			WHERE T002_Id = '{$tituloId}'
				AND T002_D024_Id = '{$clienteId}'
				AND T002_C004_Id = '{$empresaAtual}'
				AND (T002_Data_Recebimento = '0000-00-00' OR T002_Data_Recebimento IS NULL)
				AND T002_Flag_Cancelada != 'S'
				AND (T002_T002_Id_Agrupado <= 0 OR T002_T002_Id_Agrupado IS NULL)
				AND IFNULL(T002_Valor_Saldo, 0) > 0
				AND IFNULL(T002_T035_Id, 0) <= 0
			FOR UPDATE");
		if (!$sqlTitulo || mysql_num_rows($sqlTitulo) !== 1) {
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'O título não está em aberto, não pertence ao cliente ou é um boleto que já possui remessa bancária.';
		}
		$titulo = mysql_fetch_assoc($sqlTitulo);
		$saldoTitulo = round((float)$titulo['T002_Valor_Saldo'], 2);
		$valorAbatimento = min($saldoCredito, $saldoTitulo);
		if ($valorAbatimento < 0.01) {
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'Não há valor disponível para abatimento neste título.';
		}

		$valorSql = number_format($valorAbatimento, 2, '.', '');
		$nomeCliente = mysql_real_escape_string($cliente['D024_Nome_Empresa']);
		$numeroDuplicata = mysql_real_escape_string($titulo['T002_Numero_Duplicata']);
		$portadorId = (int)$portador['D027_Id'];
		$d007Id = (int)$portador['D027_D007_Id'];
		$agencia = mysql_real_escape_string($portador['D007_Numero_Agencia']);
		$conta = mysql_real_escape_string($portador['D007_Numero_Conta']);
		$portadorNome = mysql_real_escape_string($portador['D027_Portador']);
		$d014Id = (int)$titulo['T002_D014_Id'];
		$cheque = mysql_real_escape_string($titulo['T002_Numero_Cheque']);
		$boletoId = (int)$titulo['T002_T117_Id'];
		if ($boletoId <= 0) $boletoId = (int)$titulo['T117_Id_Vinculado'];
		$boletoCancelado = false;

		if ($boletoId > 0) {
			$sqlBoleto = mysql_query("SELECT T117_Id FROM T117
				WHERE T117_Id = '{$boletoId}'
					AND T117_D024_Id = '{$clienteId}'
					AND IFNULL(T117_Flag_Cancelado, 'N') != 'S'
				FOR UPDATE");
			if (!$sqlBoleto || mysql_num_rows($sqlBoleto) !== 1) {
				mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
				return 'O boleto vinculado ao título não está disponível para cancelamento.';
			}
			if (!mysql_query("UPDATE T117 SET T117_Flag_Cancelado = 'S' WHERE T117_Id = '{$boletoId}'")
				|| !mysql_query("UPDATE T002 SET T002_T117_Id = '' WHERE T002_Id = '{$tituloId}'")) {
				$erro = mysql_error();
				mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
				return 'Erro ao cancelar o boleto vinculado: ' . $erro;
			}
			$historicoDesconto = mysql_real_escape_string('ABATIMENTO DE CRÉDITO TROCA - BOLETO ' . $boletoId . ' CANCELADO');
			$sqlDesconto = "INSERT INTO T020 (
				T020_T002_Id, T020_C004_Id, T020_Data_Lancamento,
				T020_Historico, T020_Valor, T020_Flag_Deb_Cred
			) VALUES (
				'{$tituloId}', '{$empresaAtual}', CURRENT_DATE(),
				'{$historicoDesconto}', '{$valorSql}', 'D'
			)";
			if (!mysql_query($sqlDesconto)) {
				$erro = mysql_error();
				mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
				return 'Erro ao lançar o desconto de crédito troca no título: ' . $erro;
			}
			$boletoCancelado = true;
		} else {
			$sqlPagamentoTitulo = "INSERT INTO T019 (
				T019_T002_Id, T019_D007_Id, T019_C004_Id, T019_D024_Id, T019_D014_Id,
				T019_D027_Id, T019_Complemento, T019_Numero_Agencia, T019_Numero_Conta,
				T019_Numero_Cheque, T019_Data_Lancamento, T019_Referente,
				T019_Valor_Lancamento, T019_Flag_Deb_Crd
			) VALUES (
				'{$tituloId}', '{$d007Id}', '{$empresaAtual}', '{$clienteId}', '{$d014Id}',
				'{$portadorId}', '{$nomeCliente}', '{$agencia}', '{$conta}', '{$cheque}', CURRENT_DATE(),
				'ABATIMENTO DE CRÉDITO TROCA - DUPLI. No. {$numeroDuplicata}', '{$valorSql}', '1'
			)";
			if (!mysql_query($sqlPagamentoTitulo)) {
				$erro = mysql_error();
				mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
				return 'Erro ao registrar o abatimento no título: ' . $erro;
			}
		}
		if (!mysql_query("CALL T002_Baixar_Titulo('{$tituloId}', CURRENT_DATE())")) {
			$erro = mysql_error();
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'Erro ao atualizar o título abatido: ' . $erro;
		}
		$sqlTituloFinal = mysql_query("SELECT T002_Valor_Saldo FROM T002 WHERE T002_Id = '{$tituloId}'");
		$saldoTituloFinal = $sqlTituloFinal ? round((float)mysql_result($sqlTituloFinal, 0), 2) : -1;
		if (abs($saldoTituloFinal - ($saldoTitulo - $valorAbatimento)) >= 0.01) {
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'O saldo do título não foi atualizado corretamente. Nenhum lançamento foi confirmado.';
		}

		$numeroAuxiliar = mysql_real_escape_string('ABCT-' . date('YmdHis') . '-' . $clienteId . '-' . $tituloId);
		$observacao = mysql_real_escape_string('ABATIMENTO DE CRÉDITO TROCA NO TÍTULO ' . $tituloId . ' - DUPLICATA ' . $titulo['T002_Numero_Duplicata'] . ' | USUÁRIO: ' . $g['usuarioAtual']);
		$sqlTituloAuxiliar = "INSERT INTO T002 (
			T002_D024_Id, T002_C004_Id, T002_D027_Id, T002_Nome_Cliente,
			T002_Numero_Duplicata, T002_Numero_Documento,
			T002_Data_Emissao, T002_Data_Vencimento, T002_Data_Vencimento_Original,
			T002_Data_Recebimento, T002_Valor_Duplicata, T002_Valor_Saldo,
			T002_Valor_Recebido, T002_Valor_Juros, T002_Valor_Desconto,
			T002_Valor_Total, T002_Valor_Original, T002_Data_Lancamento,
			T002_Hora_Lancamento, T002_Numero_Parcela, T002_Total_Parcelas,
			T002_Flag_Status, T002_Observacao, T002_Observacao_2
		) VALUES (
			'{$clienteId}', '{$empresaAtual}', '{$portadorId}', '{$nomeCliente}',
			'{$numeroAuxiliar}', '{$numeroAuxiliar}', CURRENT_DATE(), CURRENT_DATE(), CURRENT_DATE(),
			'0000-00-00', '{$valorSql}', '{$valorSql}', '0', '0', '0',
			'{$valorSql}', '{$valorSql}', CURRENT_DATE(), CURRENT_TIME(), '1', '1',
			'1', '{$observacao}', '{$observacao}'
		)";
		if (!mysql_query($sqlTituloAuxiliar)) {
			$erro = mysql_error();
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'Erro ao registrar o consumo do crédito troca: ' . $erro;
		}
		$tituloAuxiliarId = (int)$g['mysqlLastId'];
		if ($tituloAuxiliarId <= 0) $tituloAuxiliarId = (int)mysql_insert_id();
		if ($tituloAuxiliarId <= 0) {
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'Não foi possível identificar o lançamento de abatimento criado.';
		}

		$sqlBaixaAuxiliar = "INSERT INTO T019 (
			T019_T002_Id, T019_D007_Id, T019_C004_Id, T019_D024_Id, T019_D027_Id,
			T019_Complemento, T019_Numero_Agencia, T019_Numero_Conta, T019_Data_Lancamento,
			T019_Referente, T019_Valor_Lancamento
		) VALUES (
			'{$tituloAuxiliarId}', '{$d007Id}', '{$empresaAtual}', '{$clienteId}', '{$portadorId}',
			'{$nomeCliente}', '{$agencia}', '{$conta}', CURRENT_DATE(),
			'BAIXA AUTOMÁTICA - {$portadorNome} - TÍTULO {$tituloId}', '{$valorSql}'
		)";
		if (!mysql_query($sqlBaixaAuxiliar) || !mysql_query("CALL T002_Baixar_Titulo('{$tituloAuxiliarId}', CURRENT_DATE())")) {
			$erro = mysql_error();
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'Erro ao finalizar o lançamento de abatimento: ' . $erro;
		}
		$sqlAuxiliarFinal = mysql_query("SELECT T002_Valor_Saldo, T002_Data_Recebimento, T002_Data_Baixa FROM T002 WHERE T002_Id = '{$tituloAuxiliarId}'");
		$auxiliarFinal = $sqlAuxiliarFinal ? mysql_fetch_assoc($sqlAuxiliarFinal) : false;
		if (!$auxiliarFinal
			|| abs((float)$auxiliarFinal['T002_Valor_Saldo']) >= 0.01
			|| empty($auxiliarFinal['T002_Data_Recebimento'])
			|| $auxiliarFinal['T002_Data_Recebimento'] === '0000-00-00'
			|| empty($auxiliarFinal['T002_Data_Baixa'])
			|| $auxiliarFinal['T002_Data_Baixa'] === '0000-00-00') {
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'O lançamento de abatimento não foi baixado automaticamente. Nenhum lançamento foi confirmado.';
		}

		if (!mysql_query("CALL D024_Gravar_Totalizacao('{$clienteId}')")) {
			$erro = mysql_error();
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'Erro ao recalcular o saldo final de crédito troca: ' . $erro;
		}
		$sqlSaldoFinal = mysql_query("SELECT D024_Valor_Credito_Troca FROM D024 WHERE D024_Id = '{$clienteId}'");
		$saldoFinal = $sqlSaldoFinal ? round((float)mysql_result($sqlSaldoFinal, 0), 2) : -1;
		if (abs($saldoFinal - ($saldoCredito - $valorAbatimento)) >= 0.01) {
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'O saldo de crédito troca não foi atualizado corretamente. Nenhum lançamento foi confirmado.';
		}

		if (!mysql_query('COMMIT')) {
			$erro = mysql_error();
			mysql_query('ROLLBACK'); mysql_query('SET AUTOCOMMIT=1');
			return 'Não foi possível confirmar o abatimento: ' . $erro;
		}
		mysql_query('SET AUTOCOMMIT=1');

		$mensagem = 'Abatimento de R$ ' . gCorrigeNumero($valorAbatimento) . ' realizado no título ' . $tituloId . '. Saldo de crédito restante: R$ ' . gCorrigeNumero($saldoFinal) . '.';
		if ($boletoCancelado) {
			$mensagem .= ' O boleto vinculado foi cancelado e o valor foi lançado como desconto. Gere um novo boleto manualmente para o saldo remanescente do título.';
		}
		return array(
			'mensagem' => $mensagem,
			'valor' => $valorAbatimento,
			'titulo' => $tituloId,
			'lancamento' => $tituloAuxiliarId,
			'boletoCancelado' => $boletoCancelado,
		);
	}

	/**
	 * Zera saldos de crédito troca através de títulos financeiros rastreáveis.
	 * Saldo positivo gera abatimento (portador flag 4); saldo negativo legado
	 * gera crédito compensatório (portador flag 2).
	 *
	 * @param array $clientes
	 * @param string $tipo abatimento|ajuste
	 * @param string $justificativa
	 * @return array|string
	 */
	public function processarSaldosCreditoTroca($clientes, $tipo, $justificativa)
	{
		global $g;

		if (!is_array($clientes)) {
			return 'A seleção de clientes é inválida.';
		}

		$clientesValidos = array();
		foreach ($clientes as $clienteId) {
			$clienteId = (int)$clienteId;
			if ($clienteId > 0) {
				$clientesValidos[$clienteId] = $clienteId;
			}
		}
		$clientes = array_values($clientesValidos);
		sort($clientes, SORT_NUMERIC);

		if (empty($clientes)) {
			return 'Selecione ao menos um cliente.';
		}
		if (count($clientes) > 100) {
			return 'Processe no máximo 100 clientes por vez.';
		}
		if ($tipo !== 'abatimento' && $tipo !== 'ajuste') {
			return 'O tipo de processamento é inválido.';
		}

		$justificativa = trim($justificativa);
		if ($tipo === 'ajuste' && strlen($justificativa) < 10) {
			return 'Informe uma justificativa com pelo menos 10 caracteres para o ajuste negativo.';
		}
		if (strlen($justificativa) > 500) {
			$justificativa = substr($justificativa, 0, 500);
		}

		$flagPortador = ($tipo === 'abatimento') ? '4' : '2';
		$descricaoOperacao = ($tipo === 'abatimento')
			? 'ZERAR SALDO POSITIVO DE CRÉDITO TROCA'
			: 'AJUSTE DE SALDO NEGATIVO LEGADO DE CRÉDITO TROCA';
		$empresaAtual = (int)$g['empresaAtual'];

		$sqlPortador = mysql_query("SELECT D027_Id,
										D027_Portador,
										D027_D007_Id,
										D007_Numero_Agencia,
										D007_Numero_Conta
								   FROM D027
							  LEFT JOIN D007 ON D007_Id = D027_D007_Id
								  WHERE D027_Flag_Credito_Antecipado = '{$flagPortador}'
									AND D027_Flag_Ativo = 'S'
									AND D027_Flag_Baixa_Automatica = 'S'
									AND IFNULL(D027_Baixa_Automatica_Dias, 0) = 0
									AND IFNULL(D027_D007_Id, 0) > 0
									AND D007_Flag_Ativo = 'S'
									AND D007_C004_Id = '{$empresaAtual}'");
		if (!$sqlPortador) {
			return 'Não foi possível validar o portador: ' . mysql_error();
		}

		$quantidadePortadores = mysql_num_rows($sqlPortador);
		$nomeTipoPortador = ($tipo === 'abatimento') ? 'Abatimento Crédito Troca' : 'Crédito Troca';
		if ($quantidadePortadores === 0) {
			return "Nenhum portador ativo de {$nomeTipoPortador} foi encontrado com baixa automática, zero dias e conta bancária ativa para a empresa atual.";
		}
		if ($quantidadePortadores > 1) {
			return "Existe mais de um portador válido de {$nomeTipoPortador}. Mantenha apenas uma configuração para evitar lançamentos indevidos.";
		}
		$portador = mysql_fetch_assoc($sqlPortador);

		if (!mysql_query('SET AUTOCOMMIT=0')) {
			return 'Não foi possível preparar a transação: ' . mysql_error();
		}
		if (!mysql_query('START TRANSACTION')) {
			$erro = mysql_error();
			mysql_query('SET AUTOCOMMIT=1');
			return 'Não foi possível iniciar a transação: ' . $erro;
		}

		$totalProcessado = 0;
		$titulosCriados = array();
		$sequencia = 0;
		$filtroEmpresaCliente = '';
		if ($g['C031']['separarClientePorEmpresa'] === 'S') {
			$filtroEmpresaCliente = " AND (IFNULL(D024_C004_Id, 0) = '{$empresaAtual}' OR IFNULL(D024_C004_Id, 0) = 0)";
		}
		foreach ($clientes as $clienteId) {
			$sqlCliente = mysql_query("SELECT D024_Id, D024_Nome_Empresa
										FROM D024
									   WHERE D024_Id = '{$clienteId}'
										 AND D024_Flag_Ativo = 'S'
										 AND D024_Flag_Cliente = 'S'
										 {$filtroEmpresaCliente}
									   FOR UPDATE");
			if (!$sqlCliente || mysql_num_rows($sqlCliente) !== 1) {
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "O cliente {$clienteId} não existe ou não está ativo. Nenhum lançamento foi realizado.";
			}
			$cliente = mysql_fetch_assoc($sqlCliente);

			mysql_query("CALL D024_Gravar_Totalizacao('{$clienteId}')");
			if (mysql_error()) {
				$erro = mysql_error();
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "Não foi possível recalcular o cliente {$clienteId}: {$erro}";
			}

			$sqlSaldo = mysql_query("SELECT D024_Valor_Credito_Troca FROM D024 WHERE D024_Id = '{$clienteId}'");
			if (!$sqlSaldo) {
				$erro = mysql_error();
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "Erro ao conferir o saldo do cliente {$clienteId}: {$erro}";
			}
			$saldo = round((float)mysql_result($sqlSaldo, 0), 2);
			if (($tipo === 'abatimento' && $saldo <= 0) || ($tipo === 'ajuste' && $saldo >= 0)) {
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "O saldo do cliente {$clienteId} foi alterado e não corresponde mais à operação selecionada. Nenhum lançamento foi realizado.";
			}

			$valor = abs($saldo);
			if ($valor < 0.01) {
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "O cliente {$clienteId} não possui valor suficiente para processamento.";
			}

			$sequencia++;
			$nomeCliente = mysql_real_escape_string($cliente['D024_Nome_Empresa']);
			$observacao = mysql_real_escape_string($descricaoOperacao . ' | ' . $justificativa . ' | USUÁRIO: ' . $g['usuarioAtual']);
			$numero = mysql_real_escape_string('AJCT-' . date('YmdHis') . '-' . $clienteId . '-' . $sequencia);
			$valorSql = number_format($valor, 2, '.', '');
			$portadorId = (int)$portador['D027_Id'];

			$sqlTitulo = "INSERT INTO T002 (
					T002_D024_Id, T002_C004_Id, T002_D027_Id, T002_Nome_Cliente,
					T002_Numero_Duplicata, T002_Numero_Documento,
					T002_Data_Emissao, T002_Data_Vencimento, T002_Data_Vencimento_Original,
					T002_Data_Recebimento, T002_Valor_Duplicata, T002_Valor_Saldo,
					T002_Valor_Recebido, T002_Valor_Juros, T002_Valor_Desconto,
					T002_Valor_Total, T002_Valor_Original, T002_Data_Lancamento,
					T002_Hora_Lancamento, T002_Numero_Parcela, T002_Total_Parcelas,
					T002_Flag_Status, T002_Observacao, T002_Observacao_2
				) VALUES (
					'{$clienteId}', '{$empresaAtual}', '{$portadorId}', '{$nomeCliente}',
					'{$numero}', '{$numero}', CURRENT_DATE(), CURRENT_DATE(), CURRENT_DATE(),
					'0000-00-00', '{$valorSql}', '{$valorSql}', '0', '0', '0',
					'{$valorSql}', '{$valorSql}', CURRENT_DATE(), CURRENT_TIME(), '1', '1',
					'1', '{$observacao}', '{$observacao}'
			)";
			mysql_query($sqlTitulo);
			if (mysql_error()) {
				$erro = mysql_error();
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "Erro ao criar o título do cliente {$clienteId}: {$erro}";
			}
			$tituloId = (int)$g['mysqlLastId'];
			if ($tituloId <= 0) {
				$tituloId = (int)mysql_insert_id();
			}
			if ($tituloId <= 0) {
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "Não foi possível identificar o título criado para o cliente {$clienteId}. Nenhum lançamento foi realizado.";
			}

			$portadorNome = mysql_real_escape_string($portador['D027_Portador']);
			$agencia = mysql_real_escape_string($portador['D007_Numero_Agencia']);
			$conta = mysql_real_escape_string($portador['D007_Numero_Conta']);
			$d007Id = (int)$portador['D027_D007_Id'];
			$sqlLancamento = "INSERT INTO T019 (
					T019_T002_Id, T019_D007_Id, T019_C004_Id, T019_D024_Id,
					T019_D027_Id, T019_Complemento, T019_Numero_Agencia,
					T019_Numero_Conta, T019_Data_Lancamento, T019_Referente,
					T019_Valor_Lancamento
				) VALUES (
					'{$tituloId}', '{$d007Id}', '{$empresaAtual}', '{$clienteId}',
					'{$portadorId}', '{$nomeCliente}', '{$agencia}', '{$conta}',
					CURRENT_DATE(), 'BAIXA AUTOMÁTICA - {$portadorNome}', '{$valorSql}'
				)";
			mysql_query($sqlLancamento);
			if (mysql_error()) {
				$erro = mysql_error();
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "Erro ao baixar automaticamente o título {$tituloId}: {$erro}";
			}

			mysql_query("CALL T002_Baixar_Titulo('{$tituloId}', CURRENT_DATE())");
			if (mysql_error()) {
				$erro = mysql_error();
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "Erro ao totalizar o título {$tituloId}: {$erro}";
			}
			$sqlTituloBaixado = mysql_query("SELECT T002_Valor_Saldo, T002_Data_Recebimento, T002_Data_Baixa
											 FROM T002
											WHERE T002_Id = '{$tituloId}'");
			$tituloBaixado = $sqlTituloBaixado ? mysql_fetch_assoc($sqlTituloBaixado) : false;
			if (!$tituloBaixado
				|| abs((float)$tituloBaixado['T002_Valor_Saldo']) >= 0.01
				|| empty($tituloBaixado['T002_Data_Recebimento'])
				|| $tituloBaixado['T002_Data_Recebimento'] === '0000-00-00'
				|| empty($tituloBaixado['T002_Data_Baixa'])
				|| $tituloBaixado['T002_Data_Baixa'] === '0000-00-00') {
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "O título {$tituloId} não foi baixado automaticamente. Confira a configuração do portador. Nenhum lançamento foi confirmado.";
			}

			mysql_query("CALL D024_Gravar_Totalizacao('{$clienteId}')");
			if (mysql_error()) {
				$erro = mysql_error();
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "Erro ao recalcular o saldo final do cliente {$clienteId}: {$erro}";
			}
			$sqlSaldoFinal = mysql_query("SELECT D024_Valor_Credito_Troca FROM D024 WHERE D024_Id = '{$clienteId}'");
			if (!$sqlSaldoFinal) {
				$erro = mysql_error();
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "Erro ao conferir o saldo final do cliente {$clienteId}: {$erro}";
			}
			$saldoFinal = round((float)mysql_result($sqlSaldoFinal, 0), 2);
			if (abs($saldoFinal) >= 0.01) {
				mysql_query('ROLLBACK');
				mysql_query('SET AUTOCOMMIT=1');
				return "O saldo do cliente {$clienteId} não zerou após o lançamento. Nenhum lançamento foi confirmado.";
			}

			$totalProcessado += $valor;
			$titulosCriados[] = $tituloId;
		}

		mysql_query('COMMIT');
		if (mysql_error()) {
			$erro = mysql_error();
			mysql_query('ROLLBACK');
			mysql_query('SET AUTOCOMMIT=1');
			return 'Não foi possível confirmar os lançamentos: ' . $erro;
		}
		mysql_query('SET AUTOCOMMIT=1');

		return array(
			'mensagem' => count($titulosCriados) . ' cliente(s) processado(s). Total: R$ ' . gCorrigeNumero($totalProcessado) . '. Títulos: ' . implode(', ', $titulosCriados),
			'quantidade' => count($titulosCriados),
			'total' => $totalProcessado,
			'titulos' => $titulosCriados,
		);
	}

    public function fin001excluirRetorno($T037_Id){
        global $g;
        $T038 = mysql_query("SELECT T038_Id, T002_Id, T019_Id, T002_Data_Baixa 
                            FROM T038 
                                LEFT JOIN T002 on T002_Id=T038_T002_Id 
                                LEFT JOIN T019 ON T019_T002_Id=T002_Id 
                            WHERE T038_T037_Id='{$T037_Id}' AND T019_Id IS NOT NULL AND (T002_Data_Baixa != '0000-00-00' OR T002_Data_Baixa IS NOT NULL)");

        if(mysql_num_rows($T038) > 0){
            return "Não é possível excluir este retorno, existe títulos que já foram baixados.";
        }else{

            $T038=mysql_query("DELETE FROM T038 WHERE T038_T037_Id='{$T037_Id}'");
            $T037=mysql_query("DELETE FROM T037 WHERE T037_Id='{$T037_Id}'");

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
        $array_cols=array('EMPRESA','CPF','CNPJ', 'GRUPO', 'CONTA','COMPLEMENTO', 'DATA EMISSAO', 'DATA VENCIMENTO', 'NUMERO DOCUMENTO', 'NUMERO DUPLICATA','STATUS', 'PORTADOR', 'VALOR DUPLICATA', 'OBSERVACAO');
        //posicao do array  <0>      <1>    <2>      <3>      <4>      <5>              <6>              <7>                 <8>                 <9>           <10>       <11>            <12>           
        //     <13>
 
        //Deve começar na linha=1 devido a numeração do excel
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
                $array_linha[13] = trim($array_linha[13]);
            

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
                    $array_linha[5]= mb_strtoupper($array_linha[5]);
                    $array_linha[11]= mb_strtoupper($array_linha[11]);
                    $array_linha[13]= mb_strtoupper($array_linha[13]);

                   
                    //Valida Empresa
                    if(!empty($array_linha[0])){
                        $cSelectEmpresa = "SELECT C004_Id FROM C004 where C004_Id='$array_linha[0]'";
                        $cSQL_T002_C004 = mysql_query($cSelectEmpresa);
                        $mT002_C004 = mysql_fetch_array($cSQL_T002_C004);
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectEmpresa; 
                        }
                        if(mysql_num_rows($cSQL_T002_C004) == 0){
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
                        $cSelectConta = "SELECT D014_Id, D014_D032_Id FROM D014 where D014_Subconta='$array_linha[4]'AND D014_D032_Id='{$mD032['D032_Id']}' AND D014_Flag_Ativo='S' ";
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
                        $cSelectDuplicata = "SELECT T002_Numero_Duplicata FROM T002 where T002_Numero_Duplicata='$array_linha[9]'"; 
                        $cSQL_T002_Duplicata = mysql_query($cSelectDuplicata);
                        $mT002_Duplicata = mysql_fetch_array($cSQL_T002_Duplicata);
                        $resultDuplicata = $m['T002_Numero_Duplicata']; 
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectDuplicata; 
                        }
                        if (mysql_num_rows($cSQL_T002_Duplicata) > 0) {          
                             $return .=  $msgLinha . "A Duplicata " .$array_linha[9]. " já está cadastrada.\\n";
                        }                         
                    } else {
                        if(empty($array_linha[9])){
                        $return .=  $msgLinha . "A coluna NUMERO DUPLICATA está em branco.\\n";
                        } 
                    }  

                    //Valida status
                    if(!empty($array_linha[10])){
                        if(($array_linha[10] > 3) || ($array_linha[10] < 1 )){

                            $return .= $msgLinha . "O código do STATUS ". $array_linha[10] ." não é válido! Digite um valor entre 1 e 3.\\n";    
                        }
                    }                    
                    
                
                    //Valida Portador
                    if(!empty($array_linha[11])){
                        $cSelectPortador = "SELECT D027_Id FROM D027 where D027_Portador='$array_linha[11]' ";
                        $cSQL_D027 = mysql_query($cSelectPortador);
                        $mD027 = mysql_fetch_array($cSQL_D027);
                        $resultD027[$incrementaResult] = $mD027['D027_Id'];
                        $cErro = mysql_error();
                        if(!empty($cErro)){
                            $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectPortador; 
                        } else { 
                        if(mysql_num_rows($cSQL_D027) == 0){
                            $return .= $msgLinha . "O Portador ". $array_linha[11] ." não está cadastrado!\\n";
                            } 
                        }
                    } else {
                    	if(empty($array_linha[11])){
                        $return .=  $msgLinha . "A coluna PORTADOR está em branco.\\n";
                    	}   

                    }

                    //Valida Valor Duplicata
                    if(empty($array_linha[12])){
                        $return .=  $msgLinha . "A coluna VALOR DUPLICATA está em branco.\\n";
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
                    $array_linha[5]= mb_strtoupper($array_linha[5]);
                    $array_linha[11]= mb_strtoupper($array_linha[11]);
                    $array_linha[13]= mb_strtoupper($array_linha[13]);

                    //Corrige a data
                    $array_linha[6] = gCorrigeDataInverte($array_linha[6]);
                    $array_linha[7] = gCorrigeDataInverte($array_linha[7]);
             
                    $array_linha[12]= str_replace(',','.',$array_linha[12]);

                    $cInsert = "INSERT INTO T002 
                                      (T002_C004_Id, 
                                       T002_D024_Id,
                                       T002_D032_Id,  
                                       T002_D014_Id,
                                       T002_Nome_Cliente, 
                                       T002_Data_Emissao, 
                                       T002_Data_Vencimento, 
                                       T002_Numero_Documento, 
                                       T002_Numero_Duplicata,
                                       T002_Flag_Status,
                                       T002_D027_Id,
                                       T002_Valor_Duplicata,
                                       T002_Observacao) 
                               VALUES ('".mysql_real_escape_string($array_linha[0])."',
                                       '".mysql_real_escape_string($resultD024[$incrementaResult])."',
                                       '".mysql_real_escape_string($resultD032[$incrementaResult])."',
                                       '".mysql_real_escape_string($resultD014[$incrementaResult])."',
                                       '".mysql_real_escape_string($array_linha[5])."',
                                       '".mysql_real_escape_string($array_linha[6])."',
                                       '".mysql_real_escape_string($array_linha[7])."',
                                       '".mysql_real_escape_string($array_linha[8])."',
                                       '".mysql_real_escape_string($array_linha[9])."',
                                       '".mysql_real_escape_string($array_linha[10])."',
                                       '".mysql_real_escape_string($resultD027[$incrementaResult])."',
                                       '".mysql_real_escape_string($array_linha[12])."',
                                       '".mysql_real_escape_string($array_linha[13])."')";


                    $cSQL_T002 = mysql_query($cInsert);
                    $cErro = mysql_error();
            		if (!empty($cErro)) {
               			mysql_query("ROLLBACK");
                		return "Erro: $cErro \\n\\n $cInsert";
            		}
 log(" Linha: ".$linha);
                    $incrementaResult++;
                    $linha++;

            }

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
            log(" Linha: ".$linha);
            
            return "Processamento de arquivo concluído com sucesso!!\\n************************************************** \\n\\n Foram inseridas ".($linha-2)." Contas a Receber!";

      
        } else {

            return $return;

        }        
    }

     /**
     * duplicar_fin001form01
     * Chamada DUPLICAR do menu automatico da janela
     *
     * @param String $dias
     * @return String
     */    
	public Function duplicar_fin001form01($divId, $divIdRoot, $tabela, $id)
    {
        $mT002 = array("T002_Id" => $id);
        $mT002Anterior = array();

        $retorno = $this->FIN001DuplicarTitulosSelecionados($mT002, $mT002Anterior);

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
	public Function FIN001VerificaDuplicarTitulosSelecionados($mT002,$mT002Anterior) 
    {
        return true;
    }

    /**
     * duplicarPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
    public function FIN001DuplicarTitulosSelecionados($mT002, $mT002Anterior)
    {
        global $g;
        $T002 = mysql_query("select * from T002 where T002_Id='{$mT002['T002_Id']}'");
        $mT002 = mysql_fetch_assoc($T002);

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $campos         = "";
        $valores        = "";
        foreach ($mT002 as $key => $value) {
            if ($key == 'T002_Id') {
                continue;
            }elseif($key == 'T002_T007_Id'){
                $campos.=$key.", ";
                $valores.= "'',";
            }elseif($key == 'T002_T117_Id'){
                $campos.=$key.", ";
                $valores.= "'',";
            }elseif($key == 'T002_T035_Id'){
                $campos.=$key.", ";
                $valores.= "'',";
            }elseif($key == 'T002_Data_Recebimento'){
                $campos.=$key.", ";
                $valores.= "'0000-00-00',";
            }elseif($key == 'T002_Data_Baixa'){
                $campos.=$key.", ";
                $valores.= "'0000-00-00',";
            }elseif($key == 'T002_Valor_Desconto'){
                $campos.=$key.", ";
                $valores.= "0,";
            }elseif($key == 'T002_Valor_Recebido'){
                $campos.=$key.", ";
                $valores.= "0,";
            }elseif($key == 'T002_Valor_Juros'){
                $campos.=$key.", ";
                $valores.= "0,";
            }elseif($key == 'T002_Valor_Taxas'){
                $campos.=$key.", ";
                $valores.= "0,";
            }elseif($key == 'T002_Flag_Enviar_Banco'){
                $campos.=$key.", ";
                $valores.= "'N',";
            }elseif($key == 'T002_Numero_Duplicata'){
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
        $insT002 = "INSERT INTO T002 ({$campos}) VALUES ({$valores})";
        $sql = mysqli_query($insT002);
        $erro = mysql_error();
        if (!$sql) {
            mysql_query("ROLLBACK");
            return "Erro 2: $erro" ;
        }
        $T002_Id_Novo = mysql_insert_id();

        mysql_query("UPDATE T002 set T002_Valor_Total=0,T002_Id_Padrao='{$mT002['T002_Id']}' WHERE T002_Id={$T002_Id_Novo}");
            
        mysql_query("COMMIT");

        return true;
    }

    /**
     * duplicarPedidosSelecionados
     *
     * @param String $dias
     * @return String
     */    
    public function insereAcrecimoDesconto($T103_Id)
    {
        global $g;
        
        $T103 = mysql_query("SELECT T103_Valor_Desconto,
                                    T103_Valor_Juros,
                                    T103_Valor_Taxas,
                                    T103_T002_Id
                               FROM T103 
                              WHERE T103_Id='{$T103_Id}'");

        $mT103 = mysql_fetch_array($T103);
    
        if($mT103['T103_Valor_Desconto'] > 0){
            mysql_query("INSERT INTO T020 (T020_Valor, T020_Historico, T020_Flag_Deb_Cred, T020_Data_Lancamento, T020_T002_Id, T020_C004_Id) VALUES ('{$mT103['T103_Valor_Desconto']}','DESCONTO' , 'D', NOW(), '{$mT103['T103_T002_Id']}', '{$g['empresaAtual']}')");

        }
        if($mT103['T103_Valor_Juros'] > 0){
            mysql_query("INSERT INTO T020 (T020_Valor, T020_Historico, T020_Flag_Deb_Cred, T020_Data_Lancamento, T020_T002_Id, T020_C004_Id) VALUES ('{$mT103['T103_Valor_Juros']}','JUROS' , 'C', NOW(), '{$mT103['T103_T002_Id']}', '{$g['empresaAtual']}')");  

        }
        if($mT103['T103_Valor_Taxas'] > 0){
            mysql_query("INSERT INTO T020 (T020_Valor, T020_Historico, T020_Flag_Deb_Cred, T020_Data_Lancamento, T020_T002_Id, T020_C004_Id) VALUES ('{$mT103['T103_Valor_Taxas']}','TAXA' , 'T', NOW(), '{$mT103['T103_T002_Id']}', '{$g['empresaAtual']}')");
        }        

        mysqli_query("call T002_Gravar_Totalizacao('{$mT103['T103_T002_Id']}')"); 

        return true;
    }

    /**
     * enviarMultiEmailBoleto
     *
     * @param String $T002_Id
     * @param bool $enviarDanfe
     * @return bool
     */     
    public function enviarMultiEmailBoleto($T002_Id, $enviarDanfe = false, $emailPadrao = false, $emailLembrete = false, $emailCobranca = false)
    {
        global $g;
        require_once("bibliotecas/phpmailer/class.phpmailer.php");

        $C007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$g['usuarioAtual']}';");
        $C007 = mysql_fetch_assoc($C007);

        if (empty($C007['C007_Email'])) {
                $C007['C007_Email'] = 'nfe@hardness.com.br';
                $C007['C007_Primeiro_Nome'] = $C004['C004_Nome_Abreviado'];
                $C007['C007_Email_SMTP_Senha'] = 'hgrw nvpe xliv cluz';
                $C007['C007_Email_SMTP_Servidor'] = 'smtp.gmail.com';
                $C007['C007_Email_SMTP_Autenticacao'] = 'true';
                $C007['C007_Email_SMTP_Usuario'] = 'nfe@hardness.com.br';
                $C007['C007_Email_SMTP_Porta'] = '465';
        }

        if (empty($C007) || empty($C007['C007_Email_SMTP_Servidor'])) {
            return "Seu email de usuário deve estar configurado, favor verificar.";
        }

        if($emailPadrao === true){
            $monta_ = $this->montarEmail($T002_Id);
        } elseif($emailLembrete === true){
            $monta_ = $this->montarEmailLembrete($T002_Id);
        }elseif($emailCobranca === true){
            $monta_ = $this->montarEmailCobranca($T002_Id);
        }

        $monta_['Anexos'] = array();

        if (empty($monta_['Para'])) {
            return "Email do cadastro do cliente deve estar preenchido.";
        }
        
        if (!empty($C007['C007_Email_Assinatura']) && trim($C007['C007_Email_Assinatura']) != '<br>') {
            $monta_['Corpo'] .= "<br /><br />{$C007['C007_Email_Assinatura']}";
        }
        $T002 = mysql_query("SELECT T007_Numero_Protocolo_Nfe,
                                    T007_Data_Emissao,
                                    T002_D024_Id,
                                    T007_Id
                               FROM T002 
                          LEFT JOIN T007 ON T007_Id = T002_T007_Id 
                              WHERE T002_Id = '{$T002_Id}'");
        $mT002 = mysql_fetch_array($T002);
        
        $file = $g['pathDadosAntigo'] . 'nfe/danfe/danfe_' . $mT002['T007_Numero_Protocolo_Nfe'] . '.pdf';
        if (!file_exists($file) && $enviarDanfe === true) {
            //Geramos o arquivo PDF das notas fiscais
            require_once('bibliotecas/classes/VEN003.php');
            $VEN003 = new VEN003;
            $cAno_Mes_NFe  = substr($mT002['T007_Data_Emissao'], 0, 7);
            $Diretorio_NFe = $g['pathDadosAntigo'] . 'nfe';
            $cArquivo      = "$Diretorio_NFe/xml/autorizado/{$cAno_Mes_NFe}/" . $mT002['T007_Numero_Protocolo_Nfe'] . ".xml";
            $file          = $VEN003->gerarDanfe($mT002['T007_Id'], $cArquivo);
        }
        
        if ($enviarDanfe === true && file_exists($file)) {
            $monta_['Anexos'][] = $file;
        }
                
        $mail           = new \phpmailer;
        // Mail
        $mail->From     = $C007['C007_Email'];
        $mail->FromName = $C007['C007_Primeiro_Nome'];
        $mail->Host     = $C007['C007_Email_SMTP_Servidor'];
        $mail->Mailer   = "smtp";
        $mail->SMTPAuth = $C007['C007_Email_SMTP_Autenticacao'];
        $mail->Username = $C007['C007_Email_SMTP_Usuario'];
        $mail->Password = $C007['C007_Email_SMTP_Senha'];
        $mail->Subject  = '=?UTF-8?B?'.base64_encode($monta_['Assunto']).'?=';
        $mail->Body     = $monta_['Corpo'];
        $mail->AltBody  = "ALT BODY";
        $mail->CharSet  = 'utf-8';
        $mail->SetLanguage("br");
        $mail->IsHTML(true);
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.gmail.com")
        {
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.zoho.com")
        {
            $mail->SMTPSecurity = "ssl";
            $mail->SMTPSecure   = "ssl";
            $mail->Port         = "465";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.live.com")
        {
            $mail->SMTPSecure   = "ssl";
        }
        if ($C007['C007_Email_SMTP_Servidor'] == "smtp.office365.com")
        {
            $mail->SMTPSecure   = "tls";
            $mail->SMTPSecurity = 'tls';
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], "istyle.com.br") !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if (!empty($C007['C007_Email_SMTP_Porta']))
        {
            $mail->Port         = $C007['C007_Email_SMTP_Porta']; 
        }
        if (strpos($C007['C007_Email_SMTP_Servidor'], 'yahoo') !== false || strpos($C007['C007_Email_SMTP_Servidor'], 'comercialdex') !== false)
        {
            $mail->SMTPSecure   = "tls";
        }
        if(strpos($C007['C007_Email_SMTP_Servidor'], 'gpimports') !== false or strpos($C007['C007_Email_SMTP_Servidor'], '.gp') !== false) {
                $mail->SMTPSecure = "tls";
                $mail->SMTPSecurity = 'tls';
                //$mail->Port = '465';
        }
        if ($C007['C007_Email']=='nfe@hardness.com.br') {
                $mail->SMTPSecure = "ssl";
        }
        if (empty($monta_['Para']) && !empty($monta_['cc'])) {
            $monta_['Para'] = $monta_['cc'];
            $monta_['cc']   = '';
        }
        $todosEmailsEnviados = array();
        $mPara               = preg_split("/[;,]+/", $monta_['Para']);
        for ($x = 0; $x < count($mPara); $x++) {
            $email = $mPara[$x];
            if (strpos($email, '<') !== false) {
                $email = explode('<', $email);
                $email = explode('>', $email[1]);
                $email = $email[0];
            }
            $todosEmailsEnviados[] = strtolower(trim($email));
            $mail->AddAddress($email);
        }
        if (isset($monta_['cc'])) {
            $mPara = preg_split("/[;,]+/", $monta_['cc']);
            for ($x = 0; $x < count($mPara); $x++) {
                $email = $mPara[$x];
                if (strpos($email, '<') !== false) {
                    $email = explode('<', $email);
                    $email = explode('>', $email[1]);
                    $email = $email[0];
                }
                $todosEmailsEnviados[] = strtolower(trim($email));
                $mail->AddCC($email);
            }
        }
        
        $anexosGravar   = array();
        $T139_Estrutura = array(
            'anexosManual' => array()
        );
        if (isset($monta_['Anexos'])) {
            foreach ($monta_['Anexos'] as $path) {
                $anexosGravar[]                   = $path;
                $T139_Estrutura['anexosManual'][] = basename($path);
                $mail->AddAttachment($path, basename($path));
            }
        }
        
        if ($mail->Send()) {

            mysql_query("UPDATE T002A SET T002A_Data_Envio_Email = now(), T002A_Flag_Enviar_Email = 'N' WHERE T002A_T002_Id='{$T002_Id}'");
            
            // Gravar como e-mail enviado
            $T139_De        = mysql_real_escape_string("\"{$C007['C007_Primeiro_Nome']}\" <{$C007['C007_Email']}>");
            $T139_Estrutura = serialize($T139_Estrutura);
            $T139_Para      = !empty($monta_['cc']) ? ($monta_['Para'] . '; ' . $monta_['cc']) : $monta_['Para'];
            $sql            = "INSERT INTO T139 (T139_Pasta, T139_Uid, T139_Titulo, T139_De, T139_Para, T139_Data_Hora, T139_Estrutura, T139_Mensagem_Corpo, T139_Flag_Lido, T139_D024_Id, T139_C007_Id) VALUES ('S', 0, '" . mysql_real_escape_string($monta_['Assunto']) . "', '{$T139_De}', '" . mysql_real_escape_string($T139_Para) . "', '" . date('Y-m-d H:i:s') . "', '" . mysql_real_escape_string($T139_Estrutura) . "', '" . mysql_real_escape_string($monta_['Corpo']) . "', '1', '" . mysql_real_escape_string($mT002['T002_D024_Id']) . "', '" . mysql_real_escape_string($g['usuarioAtual']) . "');";
            mysqli_query($sql);
            $g['mysqlLastId'] = mysql_insert_id();
            
            // Salvar anexos
            $dir = $g['pathDados'] . 'anexosEmail/';
            is_dir($dir) or mkdir($dir, 0777, true);
            foreach ($anexosGravar as $key => $anexo) {
                $arquivo = "{$dir}{$g['mysqlLastId']}-{$key}";
                copy($anexo, $arquivo);
            }

            if (isset($monta_['Anexos'])) {
                foreach ($monta_['Anexos'] as $path) {
                    gDeletarArquivo($path);
                }
            }

            return true;
        } else {
            $cErro=str_replace(array("'",'"',"\r","\n"),"",$mail->ErrorInfo);
            $cErro=str_replace(array("<p>","</p>"),"<br />",$cErro);
            $cErro=$cErro . ' ' . $C007['C007_Email_SMTP_Usuario'];
            return $cErro;
        }    
    }

    /**
     * montarEmailLembrete
     *
     * @param String $T002_Id
     * @return array
     */     
    public function montarEmail($T002_Id)
    {
        global $g, $confUsuario;
        $T002_Id     = mysql_real_escape_string($T002_Id);
        $T002 = mysql_query("SELECT T002_Data_Vencimento,
                                    T002_Valor_Saldo,
                                    D024_E_Mail_Boleto,
                                    D024_E_Mail,
                                    T007_Id,
                                    T007_Numero_Nota_Fiscal,
                                    D024_Nome_Empresa,
                                    T002_Numero_Parcela,
                                    T002_Total_Parcelas,
                                    T002_Numero_Duplicata
                               FROM T002
                          LEFT JOIN D024 ON D024_Id=T002_D024_Id 
                          LEFT JOIN T007 ON T007_Id=T002_T007_Id
                              WHERE T002_Id = '{$T002_Id}'");


        $Consulta    = mysql_query($T002);
        $Consulta    = mysql_fetch_array($T002);
        $array['cc'] = '';
        /*$ccEmail = mysql_query("SELECT * FROM D013 WHERE D013_D024_Id = '{$Consulta['T117_D024_Id']}'");
        while($resCC = mysql_fetch_array($ccEmail)){
        $array['cc'] .= $resCC['D013_E_Mail'].';';
        }
        $array['cc'] = substr($array['cc'], 0, -1);*/
        
        $sql = "SELECT C004_Nome_Empresa,
                       C004_Site,
                       C004_Logo_Marca,
                       C004_Nome_Abreviado 
                  FROM C004 
                 WHERE C004_Id='{$g['empresaAtual']}'";
        $empresaAtual = mysql_query($sql);
        $empresaAtual = mysql_fetch_array($empresaAtual);
        
        $dataVencimento = gCorrigeData($Consulta['T002_Data_Vencimento']);
        $ValorTitulo    = gCorrigeNumero($Consulta['T002_Valor_Saldo']);
        $parcela        = $Consulta['T002_Numero_Parcela'].'/'.$Consulta['T002_Total_Parcelas'];

/*         if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } */
        
        $array['Para']    = (!empty($Consulta['D024_E_Mail_Boleto'])) ? $Consulta['D024_E_Mail_Boleto'] : $Consulta['D024_E_Mail'];
        $array['Assunto'] = "Lembrete de vencimento";

        $dadosNF1 = $dadosNF2 = '';
        if(!empty($Consulta['T007_Id'])){
            $dadosNF1 = <<<EOT
                <td style="background: #B9BEC1;" align="center"><b>Nota Fiscal</b></td>
EOT;
            $dadosNF2 = <<<EOT
                <td align="center">{$Consulta['T007_Numero_Nota_Fiscal']}</td>
EOT;
        }

        $dadosTitulo = <<<EOT
        <table cellpadding='5' border='1' style='border-style:solid; border-collapse: collapse;'>
            <tr>
                {$dadosNF1}                
                <td style="background: #B9BEC1;" align="center"><b>Duplicata</b></td>
                <td style="background: #B9BEC1;"align="center"><b>Parcela</b></td>
                <td style="background: #B9BEC1;"align="center"><b>Vencimento</b></td>
                <td style="background: #B9BEC1;"align="center"><b>Valor</b></td>
            </tr>
            <tr>
                {$dadosNF2}
                <td align="center">{$Consulta['T002_Numero_Duplicata']}</td>
                <td align="center">{$parcela}</td>
                <td align="center">{$dataVencimento}</td>
                <td align="center">R$ {$ValorTitulo}</td>
            </tr>
        </table>
EOT;

        $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
        $caminhoLogo   = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";

        $dadosEmpresa = <<<EOT
                <b>{$g['C004']['C004_Nome_Empresa']}</b><br />
                {$g['C004']['C004_Logradouro']}, {$g['C004']['C004_Numero']} {$g['C004']['C004_Complemento']}<br />
                {$g['C004']['C004_Cep']} - {$g['C004']['D020_Nome_Cidade']} - {$g['C004']['D018_UF']}<br />
                Fone: {$g['C004']['C004_Telefone']}<br />
                {$g['C004']['C004_Site']}<br />
EOT;

        $array['Corpo'] = <<<EOT
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left; padding:10px">
            <img style="float:left; margin-right:10px;" src="{$caminhoLogo}" />
        </div>
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left;padding:10px">
            {$dadosEmpresa}
        </div>
        <div style="clear:both;padding:10px"> 
            <font style='font-size:14px;'>Olá {$Consulta['D024_Nome_Empresa']},<br>Segue fatura do seu título para pagamento com vencimento em {$dataVencimento},</font><br /><br />
                {$dadosTitulo}<br />
                <div style="clear:both; padding-top:16px;"> 
                    <font style='font-size:14px;'>Caso o pagamento já tenha sido efetuado favor desconsiderar este e-mail.</font>
                </div>
            </div>
            <div style="font-size:10px; line-height:16px;">
                E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a ></a>
            </div>
EOT;
        return $array;   
    }

    /**
     * montarEmailLembrete
     *
     * @param String $T002_Id
     * @return array
     */     
    public function montarEmailLembrete($T002_Id)
    {
        global $g, $confUsuario;
        $T002_Id     = mysql_real_escape_string($T002_Id);

        $T002 = mysql_query("SELECT T002_Data_Vencimento,
                                    T002_Valor_Saldo,
                                    D024_E_Mail_Boleto,
                                    D024_E_Mail,
                                    T007_Id,
                                    T007_Numero_Nota_Fiscal,
                                    D024_Nome_Empresa,
                                    T002_Numero_Parcela,
                                    T002_Total_Parcelas,
                                    T002_Numero_Duplicata
                               FROM T002
                          LEFT JOIN D024 ON D024_Id=T002_D024_Id 
                          LEFT JOIN T007 ON T007_Id=T002_T007_Id
                              WHERE T002_Id = '{$T002_Id}'");


        $Consulta    = mysql_query($T002);
        $Consulta    = mysql_fetch_array($T002);
        $array['cc'] = '';
        /*$ccEmail = mysql_query("SELECT * FROM D013 WHERE D013_D024_Id = '{$Consulta['T117_D024_Id']}'");
        while($resCC = mysql_fetch_array($ccEmail)){
        $array['cc'] .= $resCC['D013_E_Mail'].';';
        }
        $array['cc'] = substr($array['cc'], 0, -1);*/
        
        $sql = "SELECT C004_Nome_Empresa,
                       C004_Site,
                       C004_Logo_Marca,
                       C004_Nome_Abreviado 
                  FROM C004 
                 WHERE C004_Id='{$g['empresaAtual']}'";
        $empresaAtual = mysql_query($sql);
        $empresaAtual = mysql_fetch_array($empresaAtual);
        
        $dataVencimento = gCorrigeData($Consulta['T002_Data_Vencimento']);
        $ValorTitulo    = gCorrigeNumero($Consulta['T002_Valor_Saldo']);
        $parcela        = $Consulta['T002_Numero_Parcela'].'/'.$Consulta['T002_Total_Parcelas'];

/*         if (!empty($g['c004']['C004_Logo_Marca'])) {
            $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
        } */
        
        $array['Para']    = (!empty($Consulta['D024_E_Mail_Boleto'])) ? $Consulta['D024_E_Mail_Boleto'] : $Consulta['D024_E_Mail'];
        $array['Assunto'] = "Lembrete de vencimento";

        $dadosNF1 = $dadosNF2 = '';
        if(!empty($Consulta['T007_Id'])){
            $dadosNF1 = <<<EOT
                <td style="background: #B9BEC1;" align="center"><b>Nota Fiscal</b></td>
EOT;
            $dadosNF2 = <<<EOT
                <td align="center">{$Consulta['T007_Numero_Nota_Fiscal']}</td>
EOT;
        }

        $dadosTitulo = <<<EOT
        <table cellpadding='5' border='1' style='border-style:solid; border-collapse: collapse;'>
            <tr>
                {$dadosNF1}
                <td style="background: #B9BEC1;" align="center"><b>Duplicata</b></td>
                <td style="background: #B9BEC1;" align="center"><b>Parcela</b></td>
                <td style="background: #B9BEC1;" align="center"><b>Vencimento</b></td>
                <td style="background: #B9BEC1;" align="center"><b>Valor</b></td>
            </tr>
            <tr>
                {$dadosNF2}
                <td align="center">{$Consulta['T002_Numero_Duplicata']}</td>
                <td align="center">{$parcela}</td>
                <td align="center">{$dataVencimento}</td>
                <td align="center">R$ {$ValorTitulo}</td>
            </tr>
        </table>
EOT;

        $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
        $caminhoLogo   = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";

        $dadosEmpresa = <<<EOT
                <b>{$g['C004']['C004_Nome_Empresa']}</b><br />
                {$g['C004']['C004_Logradouro']}, {$g['C004']['C004_Numero']} {$g['C004']['C004_Complemento']}<br />
                {$g['C004']['C004_Cep']} - {$g['C004']['D020_Nome_Cidade']} - {$g['C004']['D018_UF']}<br />
                Fone: {$g['C004']['C004_Telefone']}<br />
                {$g['C004']['C004_Site']}<br />
EOT;

        $array['Corpo'] = <<<EOT
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left; padding:10px">
            <img style="float:left; margin-right:10px;" src="{$caminhoLogo}" />
        </div>
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left;padding:10px">
            {$dadosEmpresa}
        </div>
        <div style="clear:both;padding:10px"> 
            <font style='font-size:14px;'>Olá {$Consulta['D024_Nome_Empresa']},<br>Viemos lembrar do vencimento do seu título para data {$dataVencimento}, segue os dados abaixo:</font><br /><br />
                {$dadosTitulo}<br />
                <div style="clear:both; padding-top:16px;"> 
                    <font style='font-size:14px;'>Caso o pagamento já tenha sido efetuado favor desconsiderar este e-mail.</font>
                </div>
            </div>
            <div style="font-size:10px; line-height:16px;">
                E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a ></a>
            </div>
EOT;
        return $array;   
    }
    
        /**
     * montarEmailLembrete
     *
     * @param String $T002_Id
     * @return array
     */     
    public function montarEmailCobranca($T002_Id)
    {
        global $g, $confUsuario;
        $T002_Id     = mysql_real_escape_string($T002_Id);

        $T002 = mysql_query("SELECT T002_Data_Vencimento,
                                    T002_Valor_Saldo,
                                    D024_E_Mail_Boleto,
                                    D024_E_Mail,
                                    T007_Id,
                                    T007_Numero_Nota_Fiscal,
                                    D024_Nome_Empresa,
                                    T002_Numero_Parcela,
                                    T002_Total_Parcelas,
                                    T002_Numero_Duplicata
                               FROM T002
                          LEFT JOIN D024 ON D024_Id=T002_D024_Id 
                          LEFT JOIN T007 ON T007_Id=T002_T007_Id
                              WHERE T002_Id = '{$T002_Id}'");


        $Consulta    = mysql_query($T002);
        $Consulta    = mysql_fetch_array($T002);
        $array['cc'] = '';
        
        $sql = "SELECT C004_Nome_Empresa,
                       C004_Site,
                       C004_Logo_Marca,
                       C004_Nome_Abreviado 
                  FROM C004 
                 WHERE C004_Id='{$g['empresaAtual']}'";
        $empresaAtual = mysql_query($sql);
        $empresaAtual = mysql_fetch_array($empresaAtual);
        
        $dataVencimento = gCorrigeData($Consulta['T002_Data_Vencimento']);
        $ValorTitulo    = gCorrigeNumero($Consulta['T002_Valor_Saldo']);
        $parcela        = $Consulta['T002_Numero_Parcela'].'/'.$Consulta['T002_Total_Parcelas'];

        
        $array['Para']    = (!empty($Consulta['D024_E_Mail_Boleto'])) ? $Consulta['D024_E_Mail_Boleto'] : $Consulta['D024_E_Mail'];
        $array['Assunto'] = "Título vencido";

        $dadosNF1 = $dadosNF2 = '';
        if(!empty($Consulta['T007_Id'])){
            $dadosNF1 = <<<EOT
                <td style="background: #B9BEC1;" align="center"><b>Nota Fiscal</b></td>
EOT;
            $dadosNF2 = <<<EOT
                <td align="center">{$Consulta['T007_Numero_Nota_Fiscal']}</td>
EOT;
        }

        $dadosTitulo = <<<EOT
        <table cellpadding='5' border='1' style='border-style:solid; border-collapse: collapse;'>
            <tr>
                {$dadosNF1}                
                <td style="background: #B9BEC1;" align="center"><b>Duplicata</b></td>
                <td style="background: #B9BEC1;" align="center"><b>Parcela</b></td>
                <td style="background: #B9BEC1;" align="center"><b>Vencimento</b></td>
                <td style="background: #B9BEC1;" align="center"><b>Valor</b></td>
            </tr>
            <tr>
                {$dadosNF2}
                <td align="center">{$Consulta['T002_Numero_Duplicata']}</td>
                <td align="center">{$parcela}</td>
                <td align="center">{$dataVencimento}</td>
                <td align="center">R$ {$ValorTitulo}</td>
            </tr>
        </table>
EOT;

        $pathinfo      = pathinfo($g['c004']['C004_Logo_Marca']);
        $caminhoLogo   = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";

        $dadosEmpresa = <<<EOT
                <b>{$g['C004']['C004_Nome_Empresa']}</b><br />
                {$g['C004']['C004_Logradouro']}, {$g['C004']['C004_Numero']} {$g['C004']['C004_Complemento']}<br />
                {$g['C004']['C004_Cep']} - {$g['C004']['D020_Nome_Cidade']} - {$g['C004']['D018_UF']}<br />
                Fone: {$g['C004']['C004_Telefone']}<br />
                {$g['C004']['C004_Site']}<br />
EOT;

        $array['Corpo'] = <<<EOT
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left; padding:10px">
            <img style="float:left; margin-right:10px;" src="{$caminhoLogo}" />
        </div>
        <div style="font-family:arial,verdana;font-size:10px;font-weight:bold;float: left;padding:10px">
            {$dadosEmpresa}
        </div>
        <div style="clear:both;padding:10px"> 
            <font style='font-size:14px;'>Olá {$Consulta['D024_Nome_Empresa']},<br>Não identificamos em nosso sistema o pagamento do(s) títulos(s) abaixo:</font><br /><br />
                {$dadosTitulo}<br />
                <div style="clear:both; padding-top:16px;"> 
                    <font style='font-size:14px;'>Caso o pagamento já tenha sido efetuado favor desconsiderar este e-mail.</font>
                </div>
            </div>
            <div style="font-size:10px; line-height:16px;">
                E-mail enviado por <br /> {$empresaAtual['C004_Nome_Empresa']} <br /> <a href="{$empresaAtual['C004_Site']}">{$empresaAtual['C004_Site']}<a ></a>
            </div>
EOT;
        return $array;   
    }

    /**
     * vinculaContaGrupoT020
     *
     * @param String $T020_Id
     * @return array
     */     
    public function vinculaContaGrupoT020($T020_Id, $form = false, $retornoBancario = false, $tipo = false)
    {
        return true;   
    }

    /**
     * insertAntesT019
     *
     * @param String $mT002
     * @return array
     */     
    public function insertAntesT019(&$mT002)
    {       
        //rebece parametro por referência para serem alterados aqui na função e retornar um novo valor
    }

    /**
     * lancarAcrescimoDesconto
     *
     * @param String $mT002
     * @return array
     */     
    public function lancarAcrescimoDesconto($mT002,$D007_Id, $dataBaixa, $dataRecebimento)
    {
        return true;   
    }

    /**
     * antesRealizarEstorno
     *
     * @param String $mT002
     * @return array
     */     
    public function antesRealizarEstorno(&$mT002)
    {
        //rebece parametro por referência para serem alterados aqui na função e retornar um novo valor
    }

    /**
     * estornaAcrescimoDesconto
     *
     * @param String $mT002
     * @return array
     */     
    public function estornaAcrescimoDesconto($mT002,$mT019,$dataEstorno,$observacao)
    {
        return true;
    }
    

    /**
     * calcularValorTroco
     *
     * @param Float $valorRecebido
     * @param String $extra
     * @return array
     */     
    public function calcularValorTroco($valorRecebido, $extra) {
        global $g;
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T002_Flag_Baixar_Lote='S' AND T002_Data_Recebimento='0000-00-00' AND T002_C004_Id='{$g['empresaAtual']}'"));

        $sql  = " SELECT IFNULL(SUM(T103.T103_Valor_Recebido), 0) AS ValorTotal ";
        $sql .=   " FROM T002 ";
        $sql .=        " LEFT JOIN T103 ON T002.T002_Id = T103.T103_T002_Id ";
        $sql .=        " LEFT JOIN D021 ON D021.D021_Id = T002.T002_D021_Id ";
        $sql .=        " LEFT JOIN D024 ON D024.D024_Id = T002.T002_D024_Id ";
        $sql .=        " LEFT JOIN D027 ON D027.D027_Id = T002.T002_D027_Id ";
        $sql .=        " LEFT JOIN T117 ON T117.T117_Id = T002.T002_T117_Id ";
        $sql .=        " LEFT JOIN D014 ON D014.D014_Id = T002.T002_D014_Id ";
        $sql .=        " LEFT JOIN T007 ON T007.T007_Id = T002.T002_T007_Id ";
        $sql .=        " LEFT JOIN C007 ON C007.C007_Id = T007.T007_C007_Id_Vendedor_Interno ";
        $sql .=  " WHERE {$extra} ";
        $query = mysql_query($sql);
        $dados = mysql_fetch_array($query);

        if ($dados['ValorTotal']) {
            $valorTotal = floatval($dados['ValorTotal']);
            $valorTroco = $valorRecebido - $valorTotal;

            return array(
                'ValorRecebido' => gCorrigeNumero($valorRecebido),
                'ValorTotal'    => gCorrigeNumero($valorTotal),
                'ValorTroco'    => gCorrigeNumero($valorTroco)
            );
        } else {
            return false;
        }
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
        $extra = gPegarParteWhere(gInsertExtraWhere($extra, "T002_Data_Recebimento='0000-00-00' AND T002_Flag_Cancelada!='S' AND T002_Flag_Reparcelar='S' AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)"));
        $sql   = " SELECT ";
        $sql .= " T002_Id,T002_D024_Id,D024_Nome_Empresa ";
        $sql .= " FROM T002 ";
        $sql .= " LEFT JOIN D024 ON D024_Id=T002_D024_Id ";
        $sql .= " LEFT JOIN D027 ON D027_Id=T002_D027_Id ";
        $sql .= " LEFT JOIN D014 ON D014_Id=T002_D014_Id ";
        $sql .= " LEFT JOIN D032 ON D032_Id=D014_D032_Id ";
        $sql .= " LEFT JOIN D154 ON D154_Id=D014_D154_Id ";
        $sql .= " LEFT JOIN T007 ON T007_Id=T002_T007_Id ";
        $sql .= " LEFT JOIN C004 ON C004_Id=T002_C004_Id ";
        $sql .= " LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno ";
        $sql .= " WHERE {$extra}";
        //$sql .= " WHERE T002_C004_Id='{$g['empresaAtual']}'";
        //$sql .= "   AND T002_Data_Recebimento='0000-00-00'";
        //$sql .= "   AND T002_Flag_Reparcelar='S'";
        //$sql .= "   AND ifnull(T002_T035_Id,0)<=0 ";
        //$sql .= "   AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)";                
        if ($usarGroupBy) {
            $sql .= " GROUP BY T002_D024_Id";
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
     * @param Form $form
     * @return bool
     */     
    public function gerarParcelasDivisao($form)
    {
        global $g;
        
        $quantidadeParcelas = $form->campoValorEnviado('Total_Parcelas');
        $valorTitulos       = $form->campoValorEnviado('valorSomado');
        $dataVencimento     = $form->campoValorEnviado('dataVencimento');
        $mT002              = unserialize($form->campoValorEnviado('registroT002'));

        mysql_query("DELETE T317 FROM T317 WHERE T317_C007_Id='{$g['usuarioAtual']}'");
        if (mysql_error()) {
            return mysql_error();
        }
        
        // se for "a combinar" remove as parcelas que ja existem
        if($quantidadeParcelas == '0') {
            return true;
        }        
        
        $date1    = new \DateTime($mT002['T002_Data_Emissao']);
        $date2    = new \DateTime($mT002['T002_Data_Vencimento']);
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

        $count  = 1;
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
            $Numero_Duplicata = $mT002['T002_Numero_Duplicata'] . $preSequencial . chr($count + 64);

            $count++;

            $cClausula = "INSERT INTO T317 (";
            $cClausula .= "T317_C007_Id,";
            $cClausula .= "T317_C004_Id,";
            $cClausula .= "T317_D027_Id,";
            $cClausula .= "T317_Numero_Parcela,";
            $cClausula .= "T317_Prazos_Dias,";
            $cClausula .= "T317_Data_Vencimento,";
            $cClausula .= "T317_Valor_Parcela,";
            $cClausula .= "T317_Numero_Documento,";
            $cClausula .= "T317_Numero_Duplicata,";
            $cClausula .= "T317_Data_Emissao";
            $cClausula .= ") VALUES (";
            $cClausula .= "'{$g['usuarioAtual']}',";
            $cClausula .= "'{$g['empresaAtual']}',";
            $cClausula .= "'{$mT002['T002_D027_Id']}',";
            $cClausula .= "'{$i}',";
            $cClausula .= "'{$prazo}',";
            $cClausula .= "'{$mT002['T002_Data_Vencimento']}',";
            $cClausula .= "'{$valorTotalParcela}',";
            $cClausula .= "'{$mT002['T002_Numero_Documento']}',";
            $cClausula .= "'{$Numero_Duplicata}',";
            $cClausula .= "'{$mT002['T002_Data_Emissao']}'";
            $cClausula .= ")";
            mysql_query($cClausula);
            
            if (mysql_error()) {
                return mysql_error();
            }
        }
        return true;
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
    public function atualizarParcelasDivisao($T317_Id, $campoEditado, $valorAntigo, $novoValor, $valorTotal)
    {
        $query            = "SELECT * FROM T317 WHERE T317_Id='{$T317_Id}'";
        $retorno          = mysql_query($query);
        $linha            = mysql_fetch_assoc($retorno);
        $dataVelhaD024    = '';
        $prazosDiasAntigo = $linha['T317_Prazos_Dias'];
        
        if ($campoEditado == 'T317_Prazos_Dias') {
            $date = new \DateTime($dataVelhaD024);
            $date->modify('+' . $novoValor . ' days');
            error_log("entrou aqui ".$date->format('Y-m-d'));
            $query = "UPDATE T317 SET T317_Data_Vencimento='{$date->format('Y-m-d')}' WHERE T317_Id='{$T317_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T317_Data_Vencimento') {
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
                $query = "UPDATE T317 SET T317_Prazos_Dias='{$total}' WHERE T317_Id='{$T317_Id}'";
                mysql_query($query);
                return true;
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
                $query    = "UPDATE T317 SET T317_Prazos_Dias='{$total}' WHERE T317_Id='{$T317_Id}'";
                mysql_query($query);
                return true;
            }
            
        } else if ($campoEditado == 'T317_Valor_Parcela') {
            $query      = "SELECT T317_C007_Id, T317_Numero_Parcela FROM T317 WHERE T317_Id='{$T317_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $C007_Id    = $linha['T317_C007_Id'];
            $numParcela = $linha['T317_Numero_Parcela'];
            $valorTotal = $valorTotal;
            $valorAntigo = gCorrigeNumeroInverte($valorAntigo);
            
            $query                  = "SELECT SUM(T317_Valor_Parcela) as Parcela FROM T317 WHERE T317_C007_Id='{$C007_Id}' AND T317_Numero_Parcela<='{$numParcela}'";
            $retorno                = mysql_query($query);
            $linha                  = mysql_fetch_assoc($retorno);
            $valorParcelaEditada    = $linha['Parcela'];
            $valorRestante          = $valorTotal - $valorParcelaEditada;
            
            $query             = "SELECT T317_Id FROM T317 WHERE T317_C007_Id='{$C007_Id}' AND T317_Numero_Parcela>'{$numParcela}'";
            $retorno           = mysql_query($query);
            $parcelasRestantes = mysql_num_rows($retorno);

            //se ainda tiverem parcelas depois da editada, divide o valor restante entre elas
            if ($parcelasRestantes != 0) {
                //calcula o valor da soma das próximas parcelas depois da que foi editada, pra ver se o valor bate
                $valorParcelasRestantes = $valorRestante / $parcelasRestantes;
                $valorParcelasRestantesFormatado = ($valorParcelasRestantes);
                $valorTotalParcelasRestantes = $valorParcelasRestantesFormatado * $parcelasRestantes;
                $valorDiferençaProximaParcela = $valorTotal - ($valorTotalParcelasRestantes + $valorParcelaEditada);

                if ($valorParcelasRestantes < 0) {
                    $query = "UPDATE T317 SET T317_Valor_Parcela='{$valorAntigo}' WHERE T317_Id='{$T317_Id}'";
                    mysql_query($query);
                    return 'O valor inserido irá gerar parcelas negativas...<br>Favor alterar novamente';
                } else {
                    //se a diferença faltar alguns centavos, esse valor é agregado na próxima parcela depois da editada
                    if(($valorDiferençaProximaParcela > 0 ) || $valorDiferençaProximaParcela < 0){


                        $valorProximaParcela = $valorParcelasRestantes + $valorDiferençaProximaParcela;

                        $linha = mysql_fetch_assoc($retorno);
                        $query = "UPDATE T317 SET T317_Valor_Parcela='{$valorProximaParcela}' WHERE T317_Id='{$linha['T317_Id']}'";
                        mysql_query($query);

                        while ($linha = mysql_fetch_assoc($retorno)) {
                            $query = "UPDATE T317 SET T317_Valor_Parcela='{$valorParcelasRestantes}' WHERE T317_Id='{$linha['T317_Id']}'";
                            mysql_query($query);
                        }
                    }else{                
                        while ($linha = mysql_fetch_assoc($retorno)) {
                            $query = "UPDATE T317 SET T317_Valor_Parcela='{$valorParcelasRestantes}' WHERE T317_Id='{$linha['T317_Id']}'";
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
     * fin001Dividir
     * 
     * @param String $C007_Id
     * @param String $valorSomado
     * @param String $extra
     * @return String/bool
     */
    public function fin001Dividir($C007_Id, $valorSomado, $extra)
    {
        global $g;
        
        $sql = <<<EOT
            SELECT *
              FROM T002 
         LEFT JOIN D024 ON D024_Id=T002_D024_Id 
         LEFT JOIN D027 ON D027_Id=T002_D027_Id 
         LEFT JOIN D014 ON D014_Id=T002_D014_Id 
         LEFT JOIN D032 ON D032_Id=D014_D032_Id 
         LEFT JOIN D046 ON D046_Id=D024_D046_Id 
         LEFT JOIN T007 ON T007_Id=T002_T007_Id 
         LEFT JOIN C004 ON C004_Id=T002_C004_Id 
         LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno 
            WHERE 
EOT;
        $sql .= gPegarParteWhere(gInsertExtraWhere($extra, "(T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND T002_Flag_Cancelada!='S' AND T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00'"));
        $sql = mysql_query($sql);
        
        $textGeradores = "";
        $numeroDuplicataAgrupamento = array();
        $numeroDocumentoAgrupamento = array();

        while ($row = mysql_fetch_assoc($sql)) {
            $textGeradores .= $row['T002_Id'] . ';' . $row['T002_Numero_Duplicata'] . ';' . $row['T002_Data_Emissao'] . ';' . $row['T002_Data_Vencimento'] . ';' . $row['T002_Valor_Duplicata'] . ';' . PHP_EOL;
            //return $textGeradores;
            $numeroDuplicataAgrupamento[] = $row['T002_Numero_Duplicata'];
            $numeroDocumentoAgrupamento[] = $row['T002_Numero_Documento'];
        }

        $numeroDuplicataAgrupamento = implode(",", $numeroDuplicataAgrupamento);
        $numeroDocumentoAgrupamento = implode(",", $numeroDocumentoAgrupamento);

         $sql = <<<EOT
            SELECT
            *, 
            sum(T002_Valor_Saldo) TotalT002
            FROM T002 
            left join D024 on D024_Id=T002_D024_Id 
            left join D027 on D027_Id=T002_D027_Id 
            left join D014 on D014_Id=T002_D014_Id 
            LEFT JOIN D032 ON D032_Id=D014_D032_Id 
            left join D046 on D046_Id=D024_D046_Id
            left join T007 on T007_Id=T002_T007_Id 
            LEFT JOIN C004 ON C004_Id=T002_C004_Id
            left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno 
            WHERE 
EOT;
        $sql .= gPegarParteWhere(gInsertExtraWhere($extra, "(T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND T002_Flag_Cancelada!='S' AND T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00'"));    
        $retorno = mysql_fetch_array(mysql_query($sql));
        
        $erro=mysql_error();
        if (!empty($erro)){
            return "ERRO: $erro";
        }
        
        $sql2      = "SELECT SUM(T317_Valor_Parcela) TotalT317 FROM T317 WHERE T317_C007_Id='{$g['usuarioAtual']}'";
        $retorno2  = mysql_fetch_array(mysql_query($sql2));

        $totalT002 = number_format($retorno['TotalT002'],2);
        $totalT317 = number_format($retorno2['TotalT317'],2);

        log($totalT002 .'='. number_format($retorno['TotalT002'],2));

        if ($totalT002 != $totalT317) {
            return "Valor total não confere! ({$totalT002} <> {$totalT317})";
        } else {

            mysql_query("START TRANSACTION");
            $sql         = "SELECT * FROM T317 WHERE T317_C007_Id='{$g['usuarioAtual']}'";
            $result      = mysql_query($sql);
            $textGerados = '';
            $count       = 0; 
            while ($linha = mysql_fetch_array($result)) {
                if($linha['T317_Valor_Parcela'] == 0){
                    return "Existe uma parcela com valor '0,00', favor verificar";
                }else{

                    $queryT002 = "INSERT INTO T002 
                                (T002_D024_Id,
                                 T002_D021_Id,
                                 T002_C004_Id,
                                 T002_D027_Id,
                                 T002_D014_Id,
                                 T002_D032_Id,
                                 T002_Nome_Cliente,
                                 T002_Numero_Duplicata,
                                 T002_Numero_Documento,
                                 T002_Data_Emissao,
                                 T002_Data_Vencimento,
                                 T002_Data_Recebimento,
                                 T002_Valor_Duplicata,
                                 T002_Valor_Saldo,
                                 T002_Valor_Juros,
                                 T002_Valor_Desconto,
                                 T002_Valor_Total,
                                 T002_Data_Lancamento,
                                 T002_Hora_Lancamento,
                                 T002_Numero_Parcela,
                                 T002_Observacao_2,
                                 T002_Observacao_Reparcelamento,
                                 T002_Observacao,
                                 T002_T007_Id)
                           VALUES
                               ('{$retorno['T002_D024_Id']}',
                                '{$retorno['T002_D021_Id']}',
                                '{$linha['T317_C004_Id']}',
                                '{$linha['T317_D027_Id']}',
                                '{$retorno['T002_D014_Id']}',
                                '{$retorno['T002_D032_Id']}',
                                '{$retorno['D024_Nome_Empresa']}',
                                '{$linha['T317_Numero_Duplicata']}',
                                '{$linha['T317_Numero_Documento']}',
                                '{$linha['T317_Data_Emissao']}',
                                '{$linha['T317_Data_Vencimento']}',
                                '0000-00-00',
                                '{$linha['T317_Valor_Parcela']}', 
                                '{$linha['T317_Valor_Parcela']}', 
                                '0',
                                '0',
                                '{$linha['T317_Valor_Parcela']}',
                                curdate(),
                                '" . strftime('%T') . "',
                                '{$linha['T317_Numero_Parcela']}',
                                '*** REPARCELAMENTO ***',
                                '{$numeroDuplicataAgrupamento}',
                                '{$retorno['T002_Observacao']}',
                                '{$retorno['T002_T007_Id']}')";
                                
                    mysql_query($queryT002);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro 2: $cErro<br>Clausula: $queryT002";
                    }
                
                    $nT002_Id = $g['mysqlLastId'];
                
                    $sql = mysql_query("SELECT * FROM T002 WHERE T002_Id='{$nT002_Id}'");
                    while ($row = mysql_fetch_assoc($sql)) {
                        $textGerados .= $row['T002_Id'] . ';' . $row['T002_Numero_Duplicata'] . ';' . $row['T002_Data_Emissao'] . ';' . $row['T002_Data_Vencimento'] . ';' . $row['T002_Valor_Duplicata'] . ';' . PHP_EOL;
                    }

                    $count++;
                }                    
            }
            
            // insere registros na tabela de histórico de reparcelamento
            
            $query = "INSERT INTO T166 
                                 (T166_D024_Id,
                                 T166_C004_Id,
                                 T166_Data_Emissao,
                                 T166_Valor_Total,
                                 T166_Titulos_Geradores,                                 
                                 T166_Titulos_Gerados,
                                 T166_C007_Id,
                                 T166_Tipo)
                           VALUES                 
                               ('{$retorno['T002_D024_Id']}',
                                '{$retorno['T002_C004_Id']}',
                                curdate(),
                                '{$retorno['TotalT002']}',
                                '{$textGeradores}',
                                '{$textGerados}',
                                '{$g['usuarioAtual']}',
                                'R')";
            mysql_query($query);
            $T166_Id = $g['mysqlLastId'];
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 2: $cErro/p/pClausula: $query";
            }

            $arrayGeradores = explode("\n", $textGeradores);
            foreach($arrayGeradores as $linha){
                if(empty($linha)) { continue; }
                $linha = explode(";", $linha);
                mysql_query("INSERT INTO T244 (T244_T002_Id, T244_T166_Id) VALUES ('{$linha[0]}', '{$T166_Id}')");
                if (!empty($cErro)) { mysql_query("ROLLBACK"); return "Erro 7: $cErro<br>Clausula: $query"; }
            }

            $arrayGerados = explode("\n", $textGerados);
            foreach($arrayGerados as $linha){
                if(empty($linha)) { continue; }
                $linha = explode(";", $linha);
                mysql_query("INSERT INTO T244 (T244_T002_Id, T244_T166_Id) VALUES ('{$linha[0]}', '{$T166_Id}')");                
                if (!empty($cErro)) { mysql_query("ROLLBACK"); return "Erro 7: $cErro<br>Clausula: $query"; }
            }

            $sql4     = "SELECT T317_Numero_Documento FROM T317 WHERE T317_C007_Id='{$C007_Id}' OR T317_C007_Id='{$g['usuarioAtual']}'";
            $retorno4 = mysql_query($sql4);
            while ($linha4 = mysql_fetch_array($retorno4)) {
                $numeroDocumento = $linha4['T317_Numero_Documento'] . "<br />";
            }
            //-- Cancelando Contas a Receber
            $sql = "UPDATE T002
                         LEFT JOIN D024 ON D024_Id=T002_D024_Id 
                         LEFT JOIN D027 ON D027_Id=T002_D027_Id 
                         LEFT JOIN D014 ON D014_Id=T002_D014_Id 
                         LEFT JOIN D032 ON D032_Id=D014_D032_Id 
                         LEFT JOIN D154 ON D154_Id=D014_D154_Id 
                         LEFT JOIN T007 ON T007_Id=T002_T007_Id 
                         LEFT JOIN C004 ON C004_Id=T002_C004_Id 
                         LEFT JOIN C007 ON C007_Id=T007_C007_Id_Vendedor_Interno
                         SET T002_Flag_Cancelada='S', T002_Data_Recebimento=current_date(),T002_T002_Id_Agrupado='$nT002_Id', T002_Observacao_2='*** REPARCELADO {$numeroDocumento} ***' WHERE ";
            $sql .= gPegarParteWhere(gInsertExtraWhere($extra, "(T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null) AND T002_Flag_Reparcelar='S' AND T002_Data_Recebimento='0000-00-00'"));    
            mysql_query($sql);        
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro 3: $cErro/p/pClausula: $cClausula";
            }
            
            $sql     = "DELETE FROM T317 WHERE T317_C007_Id='{$g['usuarioAtual']}'";
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
}
