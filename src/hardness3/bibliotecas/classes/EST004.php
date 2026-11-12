<?php
namespace hardness;

require_once("old/lib/funcoes.php");

definirClasse('EST004');
class EST004_
{

    /**
     * excluirProduto
     *
     * @param String $T014_Id
     * @param String $T013_Id
     * @return String/bool
     */     
    public function excluirProduto($T014_Id, $T013_Id) {
        global $g;

        if (empty($T014_Id)) {
            return "Não informado Id do produto!";
        }

        $sqlT014  = "SELECT T014_D009_Id FROM T014 WHERE T014_Id = '{$T014_Id}'";
        $T014 = mysql_fetch_assoc(mysql_query($sqlT014));
        
        $sqlT001  = "SELECT T001_Id FROM T001 WHERE T001_T014_Id = '{$T014_Id}'";
        $T001 = mysql_fetch_assoc(mysql_query($sqlT001));

        mysql_query("START TRANSACTION");

        if ($T001 > 0) {
            mysql_query("DELETE FROM T001 WHERE T001_Id='{$T001['T001_Id']}'");
            if ($erro = mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro ao excluir histórico do produto.<br>{$erro}";
            }
        }

        mysql_query("DELETE FROM T014A WHERE T014A_T014_Id='{$T014_Id}'");        
        if ($erro = mysql_error()) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir a tabela estendida.<br>{$erro}";
        }
        
        mysql_query("DELETE FROM T014 WHERE T014_Id='{$T014_Id}'") ;
        if ($erro = mysql_error()) {
            mysql_query("ROLLBACK");
            return "Erro ao excluir produto.<br>{$erro}";
        }
        
        // Processa estoque do produto na empresa
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        $retorno = ($T014['T014_D009_Id']>0) ? $CAD002->D001_reprocessa_historico(false, $T014['T014_D009_Id'], 10, true) : true;
        
        if ($retorno !== true) {
            mysql_query("ROLLBACK");
            return $retorno;
        }

        mysql_query("COMMIT");
        return true;
    }

    public function acoesAposAlterarNota($T013_Id){
        return true; // Personalizar
    }

    public function recebimentoMateriais($nT013_Id){
        global $g;
        
        if($g['C031']['cyberlogInvoiceMethod'] == '1' || $g['C031']['cyberlogInvoiceMethod'] == '3') {
            require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
            $WMS = new WMS();

            if($WMS->isEnabled()) {
                if(!$WMS->integrarRecebimento('INSERT', $nT013_Id)) {
                    error_log("WMS: Erro ao integrar recebimento da nota {$nT013_Id}: " . $WMS->getErrorMessage());
                    return false;
                }
            }
        }
    }   

    public function atualizaLote($T013_Id){

    }

    public function conferenciaMateriais($T013_Id){
        return array('code' => true, 'data' => '');
    }

    public function excluirMaterial($ID, $tabela){

    }

    public function atualizaQuantidadeConferencia($novaQuantidade, $T014_Id){

    }
    
    /**
     * Calcular
     *
     * @param int $T013_Id
     */
    public function Calcular($T013_Id)
    {
        $T014 = mysql_query("select T014_Id, T014_T225_Id from T014  where T014_T013_Id='{$T013_Id}'");
        
        while ($mT014 = mysql_fetch_array($T014)) {
            mysql_query("call T014_Gravar_Totalizacao('$mT014[T014_Id]')");
            mysql_query("call T014_Gravar_Totalizacao_Custo('$mT014[T014_Id]')");
            mysql_query("call T225_Gravar_Totalizacao('$mT014[T014_T225_Id]')");
       }
        return true;
    }

    /**
     * incluirProdutoNota
     *
     * @param int $D009_Id
     * @param int $T013_Id
     * @param int $quantidade
     * @param decimal $preco
     * @param int $T012_Id
     * @return true/string
     */
    public function incluirProdutoNota($D009_Id, $T013_Id, $quantidade = '', $preco = '', $T225_Id = '')
    {
        global $g;
        
        $nT013_Id = $T013_Id;        
        $nD009_Id = $D009_Id;
        
        if (empty($nT013_Id)) {
            return "Nao foi recebido o id da nota = T013_Id";
        }
        
        $T013  = mysql_query("select T013_D006_Id,T013_Aliquota_ICMS,T013_C004_Id from T013 where T013_Id='{$nT013_Id}'");
        $mT013 = mysql_fetch_array($T013);
        
        $cClausula = <<<EOT
			SELECT 
				D009_Id, 
				D001_D037_Id, 
				D001_Codigo_Produto, 
				D049_Codigo_Produto_Fornecedor, 
				D037_Unidade, 
				D037_Id, 
				D005_Classificacao_Fiscal, 
				D005_Aliquota_IPI, 
				D001_Descricao_Produto, 
				D005_Id,
                D149_Aliquota_IPI,
                D049_Origem_Mercadoria
			from D009 
			left join D049 on D049_Id=D009_D049_Id 
			left join D001 on D001_Id=D049_D001_Id 
			left join D037 on D037_Id=D001_D037_Id 
			left join D005 on D009_D005_Id(D009_Id)=D005_Id 
            left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
			where
				D009_Id = '{$nD009_Id}' 
EOT;
        $T014  = mysql_query($cClausula);
        //echo mysql_error();
        $mT014 = mysql_fetch_array($T014);
        if (mysql_num_rows($T014) == 0) {
            return "Erro! Por favor, contato o suporte técnico! " . mysql_error();
        }
        
        /*
        if (!empty($mT014[D049_Codigo_Produto_Fornecedor]))
        {
        $mT014[D001_Codigo_Produto]=$mT014[D049_Codigo_Produto_Fornecedor];
        }
        */

        $mT014['D001_Descricao_Produto'] = mysql_real_escape_string($mT014['D001_Descricao_Produto']);
        
        $cClausula = <<<EOT
			insert into T014 (
				T014_T013_Id, 
				T014_D009_Id, 
				T014_D006_Id, 
				T014_D037_Id, 
				T014_C004_Id, 
				T014_T225_Id, 
				T014_Codigo_Produto, 
				T014_Codigo_Produto_Fornecedor, 
				T014_Quantidade, 
				T014_Aliquota_ICMS, 
				T014_Aliquota_IPI, 
				T014_Classificacao_Fiscal, 
				T014_Descricao_Produto, 
                T014_Valor_Custo_Unitario,
				T014_Flag_Origem_Produto 
			) values ( 
				'$nT013_Id', 
				'$nD009_Id', 
				'{$mT013['T013_D006_Id']}', 
				'{$mT014['D037_Id']}', 
				'{$mT013['T013_C004_Id']}', 
				'{$T225_Id}', 
				'{$mT014['D001_Codigo_Produto']}', 
				'{$mT014['D049_Codigo_Produto_Fornecedor']}', 
				'{$quantidade}', 
				'{$mT013['T013_Aliquota_ICMS']}', 
				'{$mT014['D149_Aliquota_IPI']}', 
				'{$mT014['D005_Classificacao_Fiscal']}', 
                '{$mT014['D001_Descricao_Produto']}', 
                '{$preco}',
				'{$mT014['D049_Origem_Mercadoria']}'
			) 
EOT;
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro <br /> $cClausula";
        } else {
            return true;
        }
    }
    
    /**
     * pagamentoNota
     *
     * @param Form $form
     * @param int $T013_Id
     * @return true/string
     */
    public function pagamentoNota($form, $T013_Id)
    {
        $Total_Parcelas = $form->campoValorEnviado('Total_Parcelas');
        $IntervalorDias = $form->campoValorEnviado('IntervalorDias');
        $D031_Id        = $form->campoValorEnviado('Portador_D031_Id');
        
        $cSql_T013 = mysql_query("select * from T013 where T013_Id='$T013_Id'");
        $mT013     = mysql_fetch_array($cSql_T013);

        //-- PEGA O VALOR TOTAL A VISTA
        $cSql_T154           = mysql_query("select SUM(T154_Valor_Parcela) from T154 left join D031 on D031_Id=T154_D031_Id where T154_T013_Id='$T013_Id'");
        $mT154_Total_A_Vista = mysql_fetch_array($cSql_T154);
        
        $cSql_T154 = mysql_query("select * from T154 where T154_T013_Id='$T013_Id'");
        while ($mT154 = mysql_fetch_array($cSql_T154)) {
            $mPrazoAntigo[$mT154['T154_Numero_Parcela']]    = $mT154['T154_Prazos_Dias'];
            $mPortadorAntigo[$mT154['T154_Numero_Parcela']] = $mT154['T154_D031_Id'];
        }
        
        mysql_query("delete from T154 where T154_T013_Id='$T013_Id'");
        
        $Valor_Total_IPI       = $mT013['T013_Valor_Total_IPI'];
        $Valor_Total_Orcamento = $mT013['T013_Valor_Total'];
        //$Valor_Total_Orcamento=$mT013['T013_Valor_Total']-$mT154_Total_A_Vista[0];
        
        if ($Total_Parcelas > 0) {
            $Valor_Titulo_Parcela       = ($Valor_Total_Orcamento - $Valor_Total_IPI) / $Total_Parcelas;
            $Valor_Total_Titulo         = ((round($Valor_Titulo_Parcela, 2) * $Total_Parcelas) + $Valor_Total_IPI);
            $Valor_Total_Titulo         = round($Valor_Total_Titulo, 2);
            $Diferenca_Primeira_Parcela = 0; // criado pra evitar erro de undefined no for
            if ($Valor_Total_Titulo != $Valor_Total_Orcamento) {
                $Diferenca_Primeira_Parcela = $Valor_Total_Orcamento - $Valor_Total_Titulo;
            }
        }
        
        for ($x = 1; $x <= $Total_Parcelas; $x++) {
            if ($x == 1) {
                $Valor_Titulo = ($Valor_Titulo_Parcela + $Valor_Total_IPI) + $Diferenca_Primeira_Parcela;
            } else {
                $Valor_Titulo = $Valor_Titulo_Parcela;
            }
            
            if ($D031_Id > 0) {
                $mPortadorAntigo[$x] = $D031_Id;
            }
            $mPrazoAntigo[$x] = ($IntervalorDias * $x);
            
            $cClausula = <<<EOT
				insert into T154 (
					T154_T013_Id,
					T154_D031_Id,
					T154_Numero_Parcela,
					T154_Prazos_Dias,
					T154_Data_Vencimento,
					T154_Valor_Parcela
				) values (
					'$T013_Id',
					'$mPortadorAntigo[$x]',
					'$x',
					'$mPrazoAntigo[$x]',
					DATE_ADD('{$mT013['T013_Data_Emissao']}',INTERVAL $mPrazoAntigo[$x] DAY),
					'$Valor_Titulo'
				)
EOT;
            mysql_query($cClausula);
            
            if (mysql_error()) {
                return mysql_error();
            }
        }
        return true;
    }
    
    /**
     * prepararImportarXML
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function prepararImportarXML($arquivo, $incluirItens, $cadastrarCFOP)
    {
        global $g;
        $mArquivo = $arquivo;
        $cArquivo          = $mArquivo["name"];
        $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);
        $nTotal_Importados = 0;
        $relatorio         = array();
        $relatorioCance    = array();
        
        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'xml' and $cTipo_Arquivo != 'zip') {
            return "O arquivo deve ser no formato XML ou ZIP ({$cTipo_Arquivo})";
        }
        
        if ($cTipo_Arquivo == 'zip') {
            if (!extension_loaded('zip')) {
                return "Nao esta habilitado php_zip nas configuracoes do PHP (php.ini)";
            }
            
            if (empty($mArquivo["tmp_name"])) {
                return "Upload não pode ser concluido, provavelmente o tamanho do arquivo tenha excedido o limite padrão definido nos parametros do PHP. Diminua o tamanho do arquivo.";
            }
            
            $dir = $g['pathDadosUsuario'] . "arq_temp/";
            is_dir($dir) or mkdir($dir, 0777, true);
            $cArquivoTemp = $g['pathDadosUsuario'] . "arq_temp/" . basename($cArquivo);
            if (!@move_uploaded_file($mArquivo["tmp_name"], $cArquivoTemp)) {
                return "Erro ao tentar copiar arquivo temporario: " . $mArquivo['tmp_name'] . "para: " . $cArquivoTemp;
            }
            
            $zip = zip_open($cArquivoTemp);
            if ($zip) {
                $retorno = '';
                $count   = 0;
                $erro    = 0;
                //-- INICIA UMA TRANSA��O NO BANCO DE DADOS
                while ($zip_entry = zip_read($zip)) {
                    $arquivo = basename(zip_entry_name($zip_entry));
                    
                    if (strtoupper(substr($arquivo, -4)) == '.XML') {
                        
                        $fp = fopen($g['pathDadosUsuario'] . "arq_temp/" . basename($arquivo), "w+");
                        if (zip_entry_open($zip, $zip_entry, "r")) {
                            $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                            zip_entry_close($zip_entry);
                        }
                        fwrite($fp, $buf);
                        fclose($fp);
                        
                        $zipArquivo = array(
                            'name' => $arquivo,
                            'type' => 'text/xml',
                            'tmp_name' => $g['pathDadosUsuario'] . "arq_temp/" . basename($arquivo),
                            'error' => false,
                            'size' => filesize($g['pathDadosUsuario'] . "arq_temp/" . basename($arquivo))
                        );
                        $tmp = $this->ImportarXML($zipArquivo, $incluirItens, '', '',  $cadastrarCFOP);
                        if ($tmp !== true) {
                            $erro++;
                            $retorno .= "{$arquivo}:\\n        {$tmp}\\n\\n";
                        } else {
                            $count++;
                        }
                    } else {
                        $erro++;
                        $retorno .= "{$arquivo}:\\n        ERRO: Arquivo não é um XML.\\n\\n";
                    }
                }
                if (!$count && !$erro) {
                    return 'Sem arquivos encontrados no ZIP.';
                } else {
                    return "Notas Importadas com Sucesso: {$count}\\nNotas Com Erro: {$erro}\\n\\n" . $retorno;
                }
                
            }
            zip_close($zip);
            
        } else {
            return $this->ImportarXML($arquivo, $incluirItens, '', '', $cadastrarCFOP);
        }
    }
    
    /**
     * ImportarXML
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function ImportarXML($arquivo, $incluirItens, $vinculoOcAuto = false, $D006_Id = false, $cadastrarCFOP = false)
    {
        global $g, $nT013_Id;
        require_once("bibliotecas/classes/VEN003.php");
        $VEN003 = new VEN003();

        $mArquivo      = $arquivo;
        $cArquivo      = $mArquivo["name"];
        $cTipo_Arquivo = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo = StrToLower($cTipo_Arquivo);
        
        if (empty($cArquivo)) {
            return "Selecione o arquivo";
        }
        
        if ($cTipo_Arquivo != 'xml') {
            return "O arquivo deve ser no formato XML.";
        }
        
        //-- ABRE O ARQUIVO XML
        $Ponteiro      = fopen($mArquivo["tmp_name"], "r");
        $cConteudo_XML = fread($Ponteiro, filesize($mArquivo["tmp_name"]));
        
        $T013_Arquivo_XML = addslashes($cConteudo_XML);
        
        //-- INSTANCIA A CLASSE "DOM" DO PHP PARA LER OS "N�S" DO XML
        $dom = new \DomDocument;
        $dom->loadXML($cConteudo_XML);
        $infNFe     = $dom->getElementsByTagName("infNFe")->item(0);
        $ide        = $dom->getElementsByTagName("ide")->item(0);
        $emit       = $dom->getElementsByTagName("emit")->item(0);
        $dest       = $dom->getElementsByTagName("dest")->item(0);
        $enderEmit  = $dom->getElementsByTagName("enderEmit")->item(0);
        $enderDest  = $dom->getElementsByTagName("enderDest")->item(0);
        $det        = $dom->getElementsByTagName("det");
        $cobr       = $dom->getElementsByTagName("cobr");
        $dup        = $dom->getElementsByTagName("dup");
        $ICMSTot    = $dom->getElementsByTagName("ICMSTot")->item(0);
        $transp     = $dom->getElementsByTagName("transp")->item(0);
        $transporta = $dom->getElementsByTagName("transporta")->item(0);
        $veicTransp = $dom->getElementsByTagName("veicTransp")->item(0);
        $vols       = $dom->getElementsByTagName("vol")->item(0);
        $infAdic    = $dom->getElementsByTagName("infAdic")->item(0);
        $protNFe    = $dom->getElementsByTagName("protNFe")->item(0);
        $natOp      = $dom->getElementsByTagName("natOp")->item(0)->nodeValue;
        
        if (empty($protNFe)) {
            $protNFe = $dom->getElementsByTagName("protNfe")->item(0);
        }
        if (empty($protNFe)) {
            return "Este arquivo XML não está completo, favor verificar.";
        }
        //-- PEGA O CNPJ DO DESTINATARIO
        $nC004_Cnpj = $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue;
        if (empty($nC004_Cnpj)) {
            $nCpf = $dest->getElementsByTagName("CPF")->item(0)->nodeValue;
            if (!empty($nCpf)) {
                return "Esta nota fiscal foi emitida para Pessoa F�sica. CPF: " . $nCpf;
            }
        }
        
        //-- VERIFICA SE O CNPJ QUE ESTA NO XML � O MESMO DA EMPRESA USUARIA
        $cClausula = "select C004_Cnpj from C004 where C004_Id='{$g['empresaAtual']}'";
        $cSql_C004 = mysql_query($cClausula);
        $mC004     = mysql_fetch_array($cSql_C004);
        
        $xC004_Cnpj = substr($nC004_Cnpj, 0, 2) . "." . substr($nC004_Cnpj, 2, 3) . "." . substr($nC004_Cnpj, 5, 3) . "/" . substr($nC004_Cnpj, 8, 4) . "-" . substr($nC004_Cnpj, 12, 2);
        if ($mC004['C004_Cnpj'] != $xC004_Cnpj) {
            return "CNPJ do XML: " . $xC004_Cnpj . " não é o mesmo CNPJ de sua Empresa: " . $mC004['C004_Cnpj'];
            exit;
        }
        //-- PEGA OS DADOS DA SEFAZ
        $Protocolo = $protNFe->getElementsByTagName("nProt")->item(0)->nodeValue;
        
        if (empty($Protocolo)) {
            return "Este arquivo XML não possui protocolo de autorização, favor verificar.";
        }
        
        //-- PEGA OS DADOS DA SEFAZ
        $Ambiente          = $protNFe->getElementsByTagName("tpAmb")->item(0)->nodeValue;
        $Chave_Acesso      = $protNFe->getElementsByTagName("chNFe")->item(0)->nodeValue;
        $Data_Recibo       = $protNFe->getElementsByTagName("dhRecbto")->item(0)->nodeValue;
        $Numero_Retorno    = $protNFe->getElementsByTagName("cStat")->item(0)->nodeValue;
        $Descricao_Retorno = $protNFe->getElementsByTagName("xMotivo")->item(0)->nodeValue;
        
        
        //-- FAZ UMA COPIA DO CERTIFICADO DIGITAL NO DISCO      
        $cCaminho_Destino = $g['pathDadosAntigo'] . "/nfe/xml/recebido/" . $Protocolo . ".xml";
        if (!is_dir("{$g['pathDadosAntigo']}/nfe/xml/recebido/"))
            mkdir("{$g['pathDadosAntigo']}/nfe/xml/recebido/", 0777, true);
        if (isset($mArquivo["interno"]) || !move_uploaded_file($mArquivo["tmp_name"], $cCaminho_Destino)) {
            if (!copy($mArquivo["tmp_name"], $cCaminho_Destino)) {
                return "Nao foi possivel completar o upload do arquivo. Arquivo: ".$mArquivo["tmp_name"]." Caminho: ".$cCaminho_Destino;
            }
        }
        
        //-- PEGA OS DADOS DO EMITENTE
        $CNPJ    = utf8_decode($emit->getElementsByTagName("CNPJ")->item(0)->nodeValue);
        $CPF     = utf8_decode($emit->getElementsByTagName("CPF")->item(0)->nodeValue);
        $IE      = utf8_decode($emit->getElementsByTagName("IE")->item(0)->nodeValue);
        $xNome   = utf8_decode($emit->getElementsByTagName("xNome")->item(0)->nodeValue);
        $xFant   = utf8_decode(mysql_real_escape_string($emit->getElementsByTagName("xFant")->item(0)->nodeValue));
        $xLgr    = utf8_decode(mysql_real_escape_string($enderEmit->getElementsByTagName("xLgr")->item(0)->nodeValue));
        $nro     = utf8_decode($enderEmit->getElementsByTagName("nro")->item(0)->nodeValue);
        $xBairro = utf8_decode($enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue);
        $cMun    = utf8_decode($enderEmit->getElementsByTagName("cMun")->item(0)->nodeValue);
        $cPais   = utf8_decode($enderEmit->getElementsByTagName("cPais")->item(0)->nodeValue);
        $UF      = utf8_decode($enderEmit->getElementsByTagName("UF")->item(0)->nodeValue);
        $CEP     = utf8_decode($enderEmit->getElementsByTagName("CEP")->item(0)->nodeValue);
        
        //-- PEGA O TELEFONE DO EMITENTE
        $fone        = utf8_decode($enderEmit->getElementsByTagName("fone")->item(0)->nodeValue);
        $DDDTelefone = "";
        $Telefone    = $fone;
        if (strlen($fone) > 8) {
            //-- SEPARA O DDD DO NUMERO DO TELEFONE
            $Telefone    = substr($fone, -8);
            $DDDTelefone = str_replace($Telefone, "", $fone);
        }
        
        //-- PEGA OS TOTAIS DA NOTA FISCAL
        $nNF           = utf8_decode($ide->getElementsByTagName("nNF")->item(0)->nodeValue);
        $dEmi          = utf8_decode($ide->getElementsByTagName("dEmi")->item(0)->nodeValue);
        $dhEmi         = utf8_decode($ide->getElementsByTagName("dhEmi")->item(0)->nodeValue);
        $serie         = utf8_decode($ide->getElementsByTagName("serie")->item(0)->nodeValue);
        $finalidadeNfe = utf8_decode($ide->getElementsByTagName("finNFe")->item(0)->nodeValue);
        $vNF           = utf8_decode($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue);
        $vOutro        = utf8_decode($ICMSTot->getElementsByTagName("vOutro")->item(0)->nodeValue);
        $vProd         = utf8_decode($ICMSTot->getElementsByTagName("vProd")->item(0)->nodeValue);
        $vDesc         = utf8_decode($ICMSTot->getElementsByTagName("vDesc")->item(0)->nodeValue);
        $vBC           = utf8_decode($ICMSTot->getElementsByTagName("vBC")->item(0)->nodeValue);
        $vICMS         = utf8_decode($ICMSTot->getElementsByTagName("vICMS")->item(0)->nodeValue);
        $vBCST         = utf8_decode($ICMSTot->getElementsByTagName("vBCST")->item(0)->nodeValue);
        $vST           = utf8_decode($ICMSTot->getElementsByTagName("vST")->item(0)->nodeValue);
        //$vIPI          = utf8_decode($ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue);
        $vIPITot       = utf8_decode($ICMSTot->getElementsByTagName("vIPI")->item(0)->nodeValue);
        $vFrete        = utf8_decode($ICMSTot->getElementsByTagName("vFrete")->item(0)->nodeValue);
        $T013A_Flag_Destaca_Frete_SPED = ($vFrete > 0) ? 'S' : 'N';
        $vSeg          = utf8_decode($ICMSTot->getElementsByTagName("vSeg")->item(0)->nodeValue);
        $vFCP          = utf8_decode($ICMSTot->getElementsByTagName("vFCP")->item(0)->nodeValue);
        $vFCPST        = utf8_decode($ICMSTot->getElementsByTagName("vFCPST")->item(0)->nodeValue);
        $vFCPSTRet     = utf8_decode($ICMSTot->getElementsByTagName("vFCPSTRet")->item(0)->nodeValue);
        $vFCPUFDest    = utf8_decode($ICMSTot->getElementsByTagName("vFCPUFDest")->item(0)->nodeValue);
        $vIPIDevol     = utf8_decode($ICMSTot->getElementsByTagName("vIPIDevol")->item(0)->nodeValue);
        //**Essas duas tags abaixo não vem em todo XML */
        $qBCMonoRet    = utf8_decode($ICMSTot->getElementsByTagName("qBCMonoRet")->item(0)->nodeValue);
        $qBCMonoRet    = (empty($qBCMonoRet)) ? '0.00' : $qBCMonoRet;
        $vICMSMonoRet  = utf8_decode($ICMSTot->getElementsByTagName("vICMSMonoRet")->item(0)->nodeValue);
        $vICMSMonoRet  = (empty($vICMSMonoRet)) ? '0.00' : $vICMSMonoRet;
        
        //SOMANDO O VALOR DO vIPITot + vIPIDevol,VAMOS UTILIZAR O MESMO CAMPO T013_Valor_Total_IPI
        $vIPITot = $vIPITot + $vIPIDevol;       
        
        $T013_Valor_Total_PIS    = utf8_decode($ICMSTot->getElementsByTagName("vPIS")->item(0)->nodeValue);
        $T013_Valor_Total_COFINS = utf8_decode($ICMSTot->getElementsByTagName("vCOFINS")->item(0)->nodeValue);
        
        if (!empty($vols)) {
            $T013_Peso_Liquido = utf8_decode($vols->getElementsByTagName("pesoL")->item(0)->nodeValue);
            $T013_Peso_Bruto   = utf8_decode($vols->getElementsByTagName("pesoB")->item(0)->nodeValue);
        } else {
            $T013_Peso_Liquido = "";
            $T013_Peso_Bruto + "";
        }
        
        //-- PEGA A MODALIDADE DO FRETE E CONVERTE NO PADRAO QUE JA FUNCIONA NO SISTEMA
        $modFrete     = utf8_decode($transp->getElementsByTagName("modFrete")->item(0)->nodeValue);
        $mModFrete[9] = "0";
        $mModFrete[0] = "1";
        $mModFrete[1] = "2";
        $mModFrete[2] = "3";
        
        $NomeTransportadora = utf8_decode($transp->getElementsByTagName("xNome")->item(0)->nodeValue);
        $NomeTransportadora = str_replace(array("'",'"',","),"",$Nome_Transportadora);
        $CNPJTransp         = utf8_decode($transp->getElementsByTagName("CNPJ")->item(0)->nodeValue);
        
        //-- VERIFICA SE O FORNECEDOR JA EXISTE ATRAVES DO CNPJ
        $xCNPJTransp = substr($CNPJTransp, 0, 2) . "." . substr($CNPJTransp, 2, 3) . "." . substr($CNPJTransp, 5, 3) . "/" . substr($CNPJTransp, 8, 4) . "-" . substr($CNPJTransp, 12, 2);
        $cClausula   = "select D022_Id, D022_Nome_Empresa from D022 where D022_Cnpj='$xCNPJTransp'";
        $cSql_D022 = mysql_query($cClausula);
        $mD022     = mysql_fetch_array($cSql_D022);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            return "Erro: " . $cErro . "<br />" . $cClausula;
        }
        $nD022_Id = 0;
        if (mysql_num_rows($cSql_D022) > 0) {
            $NomeTransportadora = $mD022['D022_Nome_Empresa'];
            $nD022_Id           = $mD022['D022_Id'];
        }
        
        //-- PEGA AS OBSERVACOES FISCAIS E DO CLIENTE PARA GRAVAR NO CAMPO OBSERVACAO
        if (!empty($infAdic)) {
            $infAdFisco = utf8_decode($infAdic->getElementsByTagName("infAdFisco")->item(0)->nodeValue);
            $infCpl     = utf8_decode($infAdic->getElementsByTagName("infCpl")->item(0)->nodeValue);
        }
        
        $Obs = $infAdFisco;
        if (!empty($Obs)) {
            $Obs .= "\n\n";
        }
        $Obs .= $infCpl;
        
        //-- PEGA O TOTAL DE PARCELAS
        $nTotal_Parcelas = 0;
        foreach ($dup as $d) {
            $nTotal_Parcelas++;
        }
        
        //-- PEGA O TOTAL DE ITENS
        $nTotal_Itens_Nota = 0;
        foreach ($det as $d) {
            $nTotal_Itens_Nota++;
        }
        
        
        //-- PEGA A BASE DO ICMS A CFOP E A ALIQUOTA DO ICMS
        for ($x = 0; $x < $nTotal_Itens_Nota; $x++) {
            $prod    = $det->item($x)->getElementsByTagName("prod")->item(0);
            $imposto = $det->item($x)->getElementsByTagName("imposto")->item(0);
            
            $CFOP = $prod->getElementsByTagName("CFOP")->item(0)->nodeValue;
            if ($imposto->getElementsByTagName("ICMS")->item(0)) {
                $Red_ICMS  = $imposto->getElementsByTagName("ICMS")->item(0)->getElementsByTagName("pRedBC")->item(0)->nodeValue;
                $Aliq_ICMS = $imposto->getElementsByTagName("ICMS")->item(0)->getElementsByTagName("pICMS")->item(0)->nodeValue;
                $iCST      = $imposto->getElementsByTagName("ICMS")->item(0)->getElementsByTagName("CST")->item(0)->nodeValue;
            }
            
            $TAG_IPI = $imposto->getElementsByTagName("IPI")->item(0);
            
            $vBC_IPI = 0;
            
            if (!empty($TAG_IPI)) {
                $TAG_IPITrib = $imposto->getElementsByTagName("IPI")->item(0)->getElementsByTagName("IPITrib")->item(0);
                
                if (!empty($TAG_IPITrib)) {
                    $vBC_IPI = $imposto->getElementsByTagName("IPI")->item(0)->getElementsByTagName("IPITrib")->item(0)->getElementsByTagName("vBC")->item(0)->nodeValue;
                }
            }
            $vTBC_IPI += $vBC_IPI;
        }
        
        //-- ENCONTRA A CFOP
        if(!$D006_Id){
            $D006  = mysql_query("SELECT D006_Id, 
                                         D006_Flag_Gera_Contas, 
                                         D006_D014_Id, 
                                         D006_D073_Id, 
                                         D006_Codigo_CFOP 
                                    FROM D006 
                                   WHERE (replace(replace(replace(D006_Codigo_CFOP,'.',''),',',''),'-','')='$CFOP' OR LOCATE('$CFOP', REPLACE(REPLACE(D006_Codigo_CFOP_Contra_Partida,'.',''),'-','')) > 0)
                                     AND D006_Flag_Entrada_Saida = 'E'
                                     AND D006_Flag_Inativo != 'S'");
            $mD006 = mysql_fetch_array($D006);
        } else {
            $D006  = mysql_query("SELECT D006_Id, 
                                         D006_Flag_Gera_Contas, 
                                         D006_D014_Id, 
                                         D006_D073_Id, 
                                         D006_Codigo_CFOP 
                                    FROM D006 
                                   WHERE D006_Id='{$D006_Id}'");
            $mD006 = mysql_fetch_array($D006);
        }
        /*
        if (empty($mD006[D006_D006_Id])) {
        $mCFOP[5]=1;
        $mCFOP[6]=2;
        $CFOP=$mCFOP[substr($CFOP,0,1)].'.'.substr($CFOP,-3);
        }
        
        $cClausula  = " select ";
        $cClausula .= " D006_Id ";
        $cClausula .= " from D006 ";
        $cClausula .= " where D006_Id='$mD006[D006_D006_Id]' ";
        $cClausula .= "    or D006_Codigo_CFOP='$CFOP' ";
        $cSql_D006 = mysql_query($cClausula);
        $mD006 = mysql_fetch_array($cSql_D006);
        $cErro=mysql_error();
        if (!empty($cErro))
        {
        return "Erro: " . $cErro ."<br />". $cClausula;
        }*/
        
        // Cadastrando CFOP
        if ($mD006['D006_Id'] <= 0 AND $cadastrarCFOP == "on") {
            $digito = substr($CFOP, 0, 1);
                    switch ($digito){
                        case 5:
                            $cfopCompra = '1.'.substr($CFOP, 1, 3);
                            break;
                        case 6:
                            $cfopCompra = '2.'.substr($CFOP, 1, 3);
                            break;
                        case 7:
                            $cfopCompra = '3.'.substr($CFOP, 1, 3);
                            break;
                    }
                    $CFOP = substr($CFOP, 0, 1) . '.' . substr($CFOP, -3);
                    $mD006['D006_Id'] = $VEN003->cadastrarCFOP($cfopCompra, $natOp, $vICMS, $iCST, $vIPITot, $T013_Valor_Total_PIS, $T014_Situacao_Tributaria_PIS, $T013_Valor_Total_COFINS, $T014_Situacao_Tributaria_COFINS, $CFOP);
        }else if($mD006['D006_Id'] <= 0){
            return "A CFOP " . $CFOP . " não está cadastrada como ENTRADA ou não está vinculada pelo campo CONTRA PARTIDA na cfop de ENTRADA correspondente. Verifique e tente novamente.";
        } 
        
        if(!empty($CNPJ)){
            //-- VERIFICA SE O FORNECEDOR JA EXISTE ATRAVES DO CNPJ
            $xCNPJ     = substr($CNPJ, 0, 2) . "." . substr($CNPJ, 2, 3) . "." . substr($CNPJ, 5, 3) . "/" . substr($CNPJ, 8, 4) . "-" . substr($CNPJ, 12, 2);
            $xCPF      = '';
            $D024_Flag_Pessoa_Fisica_Juridica = 'J';

            $cClausula = " select ";
            $cClausula .= " D024_Id, ";
            $cClausula .= " D024_Flag_Ativo ";
            $cClausula .= " from D024 ";
            $cClausula .= " WHERE D024_Cnpj='{$xCNPJ}' ";
            $cSql_D024 = mysql_query($cClausula);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: " . $cErro . "<br />" . $cClausula;
            }

        } else {
            $xCPF  = substr($CPF, 0, 3) . "." . substr($CPF, 3, 3) . "." . substr($CPF, 6, 3) . "-" . substr($CPF, 9, 2);
            $xCNPJ = '';
            $D024_Flag_Pessoa_Fisica_Juridica = 'F';

            $cClausula = " select ";
            $cClausula .= " D024_Id, ";
            $cClausula .= " D024_Flag_Ativo ";
            $cClausula .= " from D024 ";
            $cClausula .= " WHERE D024_Cpf='{$xCPF}' ";
            $cSql_D024 = mysql_query($cClausula);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: " . $cErro . "<br />" . $cClausula;
            }
        }
        
        if (mysql_num_rows($cSql_D024) > 0) {
            //-- CASO JA EXISTA UM FORNECEDOR COM MESMO CNPJ, PEGA O ID DO D024
            $mD024    = mysql_fetch_array($cSql_D024);
            $nD024_Id = $mD024['D024_Id'];

            if ($mD024['D024_Flag_Ativo'] != 'S') {
                return "O fornecedor/cliente está como inativo no sistema, favor verificar.";
            }
            
            //-- NAO PERMITE INCLUIR A MESMA NOTA COM MESMO NUMERO PARA O MESMO FORNECEDOR
            $cClausula = " select T013_Id,T013_Chave_Acesso_Nfe from T013 where T013_Numero_Nota_Fiscal='$nNF' and T013_D024_Id='$nD024_Id' and T013_Serie='$serie' and T013_Flag_Cancelada!='S'";
            $cSql_T013 = mysql_query($cClausula);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                return "Erro: " . $cErro . "<br />" . $cClausula;
            }
            
            if (mysql_num_rows($cSql_T013) > 0) {
                $mT013 = mysql_fetch_array($cSql_T013);
                if (empty($mT013['T013_Chave_Acesso_Nfe'])) {
                    mysql_query("
                    	update
                    		T013
                    	set
	                    	T013_Chave_Acesso_Nfe='$Chave_Acesso',
	                        T013_Numero_Recibo_Nfe='$Numero_Recibo',
	                        T013_Numero_Protocolo_Nfe='$Protocolo',
	                        T013_Data_Hora_Protocolo_Nfe='$Data_Recibo',
	                        T013_Ambiente_Nfe='$Ambiente',
	                        T013_Retorno_Nfe='$Numero_Retorno-$Descricao_Retorno'
						where
							T013_Id='{$mT013['T013_Id']}'");
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        return "Erro: " . $cErro . "<br />" . $cClausula;
                    }
                }
                return "Já existe uma Nota Fiscal lançada com o número " . $nNF . " para esse fornecedor. Importação cancelada!";
            }
        }
        
        //-- INICIA UMA TRANSA��O NO BANCO DE DADOS
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        if (mysql_num_rows($cSql_D024) == 0) {
            //-- PEGA A CIDADE E O ESTADO CONFORME O CODIGO IBGE
            $cClausula = "select D020_Id, D020_D018_Id from D020 where D020_Codigo_IBGE='$cMun'";
            $cSql_D020 = mysql_query($cClausula);
            $mD020     = mysql_fetch_array($cSql_D020);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: " . $cErro . "<br />" . $cClausula;
            }
            
            //-- PEGA O PAIS CONFORME O CODIGO BACEN
            $cClausula = "select D030_Id from D030 where D030_Codigo_Pais='$cPais'";
            $cSql_D030 = mysql_query($cClausula);
            $mD030     = mysql_fetch_array($cSql_D030);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: " . $cErro . "<br />" . $cClausula;
            }
            
            //-- FAZ A INSERCAO DO FORNECEDOR
            $cClausula = <<<EOT
				insert into D024 (
					D024_Nome_Empresa, 
					D024_Flag_Pessoa_Fisica_Juridica, 
					D024_Nome_Fantasia, 
					D024_Flag_Revenda_Consumidor, 
					D024_Flag_Ativo, 
					D024_Data_Irregular, 
					D024_Inscricao_Estadual, 
					D024_Cnpj,
                    D024_Cpf, 
					D024_Endereco, 
					D024_Numero, 
					D024_D018_Id, 
					D024_D020_Id, 
					D024_D030_Id, 
					D024_Bairro, 
					D024_Cep, 
					D024_DDD_Telefone_1, 
					D024_Telefone_1, 
					D024_Flag_Pre_Cadastro, 
					D024_Observacoes, 
					D024_Data_Cadastro, 
					D024_Flag_Comercial, 
					D024_Flag_Cliente, 
					D024_Flag_Fornecedor 
				) values ( 
					'{$xNome}', 
					'{$D024_Flag_Pessoa_Fisica_Juridica}', 
					'{$xFant}', 
					'R', 
					'S', 
					'0000-00-00', 
					'{$IE}', 
					'{$xCNPJ}', 
                    '{$xCPF}', 
					'{$xLgr}', 
					'{$nro}', 
					'{$mD020['D020_D018_Id']}', 
					'{$mD020['D020_Id']}', 
					'{$mD030['D030_Id']}', 
					'{$xBairro}', 
					'{$CEP}', 
					'{$DDDTelefone}', 
					'{$Telefone}', 
					'N', 
					'FORNECEDOR INCLUIDO AUTOMATICAMENTE AO IMPORTAR XML', 
					now(), 
					'A', 
					'N', 
					'S' 
				)
EOT;
            $cSql  = mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: " . $cErro . "<br />" . $cClausula;
            } else {
                $nD024_Id = $g['mysqlLastId'];
                if (empty($nD024_Id)) {
                    $nD024_Id = mysql_insert_id();
                }
            }
        }

        //Se a CFOP cadastrada gera contas, é inserido no T013 a conta cadastrada
        /* Não vao mais popular o campo T013_D014_Id (28/10/2021)
        $Conta = 0;
        if($mD006['D006_Flag_Gera_Contas'] == 'P' || $mD006['D006_Flag_Gera_Contas'] == 'R') {
            if($mD006['D006_Flag_Gera_Contas'] == 'P' && (empty($mD006['D006_D073_Id']) || $mD006['D006_D073_Id'] <= 0)) {
                return "A CFOP ".$mD006['D006_Codigo_CFOP']." (id ".$mD006['D006_Id'].") gera contas a pagar e está sem um centro de custo definido! Verifique o cadastro da CFOP.";
            }
            if(empty($mD006['D006_D014_Id']) || $mD006['D006_D014_Id'] <= 0) {
                $tipoConta = ($mD006['D006_Flag_Gera_Contas'] == 'P') ? 'pagar' : 'receber';
                return "A CFOP " . $CFOP . " gera contas a {$tipoConta} e está sem uma conta definida! Verifique o cadastro da CFOP.";
            } else {
                $Conta = $mD006['D006_D014_Id'];
            }
        }*/
        

        $Obs       = mysql_real_escape_string($Obs);
        //-- FAZ A INSER��O DO CABECALHO DA NOTA FISCAL

        $fusoHorarioEmissao = '';
        if(empty($dEmi)){
            $fusoHorarioEmissao = substr($dhEmi, 19, 6);
            $dEmi = substr($dhEmi, 0, 19);
        }

        $cClausula = <<<EOT
			insert into T013 (
				T013_C008_Id, 
				T013_D006_Id, 
				T013_Tipo, 
				T013_D024_Id, 
				T013_C004_Id, 
				T013_Numero_Nota_Fiscal, 
				T013_Data_Emissao,
                T013_Fuso_Horario_Emissao, 
				T013_Data_Entrada, 
				T013_Codigo_CFOP, 
				T013_Numero_Controle, 
				T013_Parcelas, 
				T013_Valor_Encargos, 
				T013_Valor_Total_Produtos, 
				T013_Valor_Desconto_Produtos, 
				T013_Valor_Total, 
				T013_Valor_Desconto, 
				T013_Numero_Ordem_Compra, 
				T013_Observacao_1, 
				T013_Valor_Total_Base_ICMS, 
				T013_Valor_Total_Base_ICMS_Diferido, 
				T013_Valor_Total_ICMS, 
				T013_Aliquota_ICMS, 
				T013_Casas_Decimais_Valor_ICMS, 
				T013_Valor_Total_Base_ICMS_Substituicao, 
				T013_Valor_ICMS_Substituicao, 
				T013_Valor_Total_Base_IPI, 
				T013_Valor_Total_IPI, 
				T013_Flag_Calcular_ICMS_IPI, 
				T013_Flag_Arredondar_Valor_IPI, 
				T013_Numero_Conhecimento, 
				T013_Flag_Frete, 
				T013_Tipo_Transporte, 
				T013_Valor_Frete, 
				T013_Flag_Calcular_ICMS_Frete, 
				T013_Flag_Calcular_IPI_Frete, 
				T013_Nome_Transportadora, 
				T013_D022_Id, 
				T013_D031_Id, 
				T013_Numero_Parcelas, 
				T013_Numero_Dias, 
				T013_Valor_Juros, 
				T013_T085_Id, 
				T013_Valor_Despesas_Acessorias, 
				T013_Flag_Calcular_ICMS_Despesas_Acessorias, 
				T013_Flag_Calcular_IPI_Despesas_Acessorias, 
				T013_Mao_Obra_1, 
				T013_Mao_Obra_2, 
				T013_Valor_Mao_Obra, 
				T013_Aliquota_ISS, 
				T013_Valor_ISS, 
				T013_Valor_Seguro, 
				T013_Flag_Calcular_ICMS_Seguro, 
				T013_Flag_Calcular_IPI_Seguro, 
				T013_Valor_Dolar,  
				T013_Chave_Acesso_Nfe,  
				T013_Numero_Recibo_Nfe,  
				T013_Numero_Protocolo_Nfe,  
				T013_Data_Hora_Protocolo_Nfe,  
				T013_Retorno_Nfe,  
				T013_Ambiente_Nfe,  
				T013_Flag_Calcular,  
				T013_Flag_Validacoes,  
				T013_Valor_Total_PIS,  
				T013_Valor_Total_COFINS,  
				T013_Peso_Liquido,  
				T013_Peso_Bruto,  
				T013_CNPJ_Fornecedor,  
                T013_Arquivo_XML,
				T013_Serie,
                T013_Flag_Finalidade_NFe,
                T013_Flag_Rateio_Frete,
                T013_Valor_Total_FCP,
                T013_Valor_Total_FCP_ST,
                T013_Valor_Total_FCP_Ret,
                T013_Valor_Total_FCP_Dest,
                T013_Flag_Obrigatorio_OC
			) values ( 
				'0', 
				'{$mD006['D006_Id']}', 
				'1', 
				'$nD024_Id', 
				'{$g['empresaAtual']}', 
				'$nNF', 
				'$dEmi',
                '$fusoHorarioEmissao', 
				current_date(), 
				'$CFOP', 
				'0', 
				'$nTotal_Parcelas', 
				'0', 
				'$vProd', 
				'0', 
				'$vNF', 
				'$vDesc', 
				'', 
				'$Obs', 
				'$vBC', 
				'0', 
				'$vICMS', 
				'$Aliq_ICMS', 
				'5', 
				'$vBCST', 
				'$vST', 
				'$vTBC_IPI', 
				'$vIPITot', 
				'N', 
				'S', 
				'', 
				'" . $mModFrete[$modFrete] . "', 
				'', 
				'$vFrete', 
				'S', 
				'S', 
				'$NomeTransportadora', 
				'$nD022_Id', 
				'0', 
				'$nTotal_Parcelas', 
				'30', 
				'0', 
				'0', 
				'$vOutro', 
				'S', 
				'S', 
				'', 
				'', 
				'0', 
				'0', 
				'0', 
				'$vSeg', 
				'S', 
				'S', 
				'0', 
				'$Chave_Acesso',  
				'$Recibo',  
				'$Protocolo',  
				'$Data_Recibo',  
				'$Numero_Retorno-$Descricao_Retorno', 
				'$Ambiente', 
				'N', # T013_Flag_Calcul
				'S', # T013_Flag_Calcul
				'{$T013_Valor_Total_PIS}',  
				'{$T013_Valor_Total_COFINS}',  
				'{$T013_Peso_Liquido}',  
				'{$T013_Peso_Bruto}',  
				'{$CNPJ}', 
                '{$T013_Arquivo_XML}',
				'{$serie}',
                '{$finalidadeNfe}',
                '{$g['C031']['formaRateioFreteNotaRecebida']}',
                '{$vFCP}',
                '{$vFCPST}',
                '{$vFCPSTRet}',
                '{$vFCPUFDest}',
                '{$g['C031']['obrigatorioOCNotaFiscalCompra']}'
			)
EOT;
        $cSql  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cErro . "<br />" . $cClausula;
        } else {
            $nT013_Id = $g['mysqlLastId'];
            if (empty($nT013_Id)) {
                $nT013_Id = mysql_insert_id();
            }
        }
        
        
        // Insere registro tabela estendida
        mysql_query("INSERT INTO T013A(T013A_T013_Id,
                                       T013A_Valor_Total_ICMS_Mono_Retido,
                                       T013A_Valor_Total_Base_ICMS_Mono_Retido,
                                       T013A_Flag_Destaca_Frete_SPED
                               )VALUES('{$nT013_Id}',
                                       '{$vICMSMonoRet}',
                                       '{$qBCMonoRet}',
                                       '{$T013A_Flag_Destaca_Frete_SPED}')");
        $cErro = mysql_error();                     
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro T013A: " . $cErro;
        }
        
        //-- INCLUIR AS PARCELAS DE PAGAMENTO
        $numParcela = 1;
        for ($x = 0; $x < $nTotal_Parcelas; $x++) {
            $dupl  = $dup->item($x);
            $nDup  = $dupl->getElementsByTagName("nDup")->item(0)->nodeValue;
            $dVenc = $dupl->getElementsByTagName("dVenc")->item(0)->nodeValue;
            $vDupl = $dupl->getElementsByTagName("vDup")->item(0)->nodeValue;

            $dataEmissao = strtotime($dEmi);
            $dataVencimento = strtotime($dVenc);
            $prazoDias = floor(($dataVencimento-$dataEmissao)/(60*60*24));
            mysql_query("insert into T154 (T154_T013_Id, T154_Numero_Parcela, T154_Data_Vencimento, T154_Valor_Parcela, T154_Prazos_Dias) values ('$nT013_Id', '$numParcela', '$dVenc', '$vDupl', '$prazoDias')");
            log("insert into T154 (T154_T013_Id, T154_Numero_Parcela, T154_Data_Vencimento, T154_Valor_Parcela, T154_Prazos_Dias) values ('$nT013_Id', '$numParcela', '$dVenc', '$vDupl', '$prazoDias')");
            $numParcela++;
        }
        
        if ($incluirItens == "on") {            
            
            //-- FAZ A INCLUSAO DOS ITENS
            for ($x = 0; $x < $nTotal_Itens_Nota; $x++) {
                $prod         = $det->item($x)->getElementsByTagName("prod")->item(0);
                $imposto      = $det->item($x)->getElementsByTagName("imposto")->item(0);
                $impostoDevol = $det->item($x)->getElementsByTagName("impostoDevol")->item(0);
                $ICMS         = $imposto->getElementsByTagName("ICMS")->item(0);
                $PIS          = $imposto->getElementsByTagName("PIS")->item(0);
                $COFINS       = $imposto->getElementsByTagName("COFINS")->item(0);
                $TAGIPI       = $imposto->getElementsByTagName("IPI")->item(0);                 
                if(!empty($impostoDevol)){
                    $IPIDev = $impostoDevol->getElementsByTagName("IPI")->item(0);     
                }            
                
                $CodigoProd           = $prod->getElementsByTagName("cProd")->item(0)->nodeValue;
                $Descricao            = $prod->getElementsByTagName("xProd")->item(0)->nodeValue;
                $Quantidade           = $prod->getElementsByTagName("qCom")->item(0)->nodeValue;
                $nValor_Unitario      = $prod->getElementsByTagName("vUnCom")->item(0)->nodeValue;
                $nValor_Total_Produto = $prod->getElementsByTagName("vProd")->item(0)->nodeValue;
                $ClassFiscal          = $prod->getElementsByTagName("NCM")->item(0)->nodeValue;
                $Unidade              = $prod->getElementsByTagName("uCom")->item(0)->nodeValue;
                $vDesc                = $prod->getElementsByTagName("vDesc")->item(0)->nodeValue;
                $numeroPedido         = $prod->getElementsByTagName("xPed")->item(0)->nodeValue;
                $itemPedido           = $prod->getElementsByTagName("nItemPed")->item(0)->nodeValue;
                $cEAN                 = $prod->getElementsByTagName("cEAN")->item(0)->nodeValue;
                $cEANTrib             = $prod->getElementsByTagName("cEANTrib")->item(0)->nodeValue;
                $nFCI                 = $prod->getElementsByTagName("nFCI")->item(0)->nodeValue;
                $cEAN = trim($cEAN);
                $cEANTrib = trim($cEANTrib);
                $cEAN = ($cEAN == "SEM GTIN" ? "" : $cEAN);
                $cEANTrib = ($cEANTrib == "SEM GTIN" ? "" : $cEANTrib);

                $numeroPedido         = (!empty($numeroPedido)) ? $numeroPedido : '';
                $itemPedido           = (!empty($itemPedido)) ? $itemPedido : '';

                $Descricao  = str_replace("'", "", $Descricao);
                $CodigoProd = str_replace("'", "", $CodigoProd);
                $Unidade    = str_replace("'", "", $Unidade);

                if (!empty($ICMS)) {
                    $AliqICMS  = $ICMS->getElementsByTagName("pICMS")->item(0)->nodeValue;
                    $RedICMS   = $ICMS->getElementsByTagName("pRedBC")->item(0)->nodeValue;
                    $RedICMSST = $ICMS->getElementsByTagName("pRedBCST")->item(0)->nodeValue;
                    $vBC_ICMS  = $ICMS->getElementsByTagName("vBC")->item(0)->nodeValue;
                    $vICMS     = $ICMS->getElementsByTagName("vICMS")->item(0)->nodeValue;
                    $Orig      = $ICMS->getElementsByTagName("orig")->item(0)->nodeValue;
                    
                    $T014_Valor_ICMS_Substituicao      = $ICMS->getElementsByTagName("vICMSST")->item(0)->nodeValue; // T014_Valor_ICMS_Substituicao
                    $T014_Flag_Situacao_Tributaria     = $ICMS->getElementsByTagName("CST")->item(0)->nodeValue; // T014_Flag_Situacao_Tributaria regime normal
                    $CSOSN                             = $ICMS->getElementsByTagName("CSOSN")->item(0)->nodeValue; // T014_Flag_Situacao_Tributaria do simples
                    $T014_Modalidade_ICMS              = $ICMS->getElementsByTagName("modBC")->item(0)->nodeValue; // T014_Modalidade_ICMS
                    $T014_Modalidade_ICMS_Substituicao = $ICMS->getElementsByTagName("modBCST")->item(0)->nodeValue; // T014_Modalidade_ICMS_Substituicao

                    // Situacao Tributária ICMS simples nacional
                    if(!empty($CSOSN)){                        
                        $T014_Flag_Situacao_Tributaria = $CSOSN;                       
                    } 

                    // T014_ICMS_Inter_Estadual passa a usar o pICMS
                    $T014_ICMS_Inter_Estadual          = $AliqICMS; // T014_ICMS_Inter_Estadual
                    // E o pICMSST passa do T014_ICMS_Inter_Estadual para T014_Aliquota_ICMS_Interno
                    $T014_Aliquota_ICMS_Interno        = $ICMS->getElementsByTagName("pICMSST")->item(0)->nodeValue; // T014_Aliquota_ICMS_Interno
                    $T014_Valor_Base_ICMS_Substituicao = $ICMS->getElementsByTagName("vBCST")->item(0)->nodeValue; // T014_Valor_Base_ICMS_Substituicao
                    $T014_MVA_Ajustada                 = $ICMS->getElementsByTagName("pMVAST")->item(0)->nodeValue; // T014_MVA_Ajustada

                    $vBCFCP                            = $ICMS->getElementsByTagName("vBCFCP")->item(0)->nodeValue; // T014_Valor_Base_FCP
                    $pFCP                              = $ICMS->getElementsByTagName("pFCP")->item(0)->nodeValue; // T014_Percentual_FCP
                    $vFCP                              = $ICMS->getElementsByTagName("vFCP")->item(0)->nodeValue; // T014_Valor_FCP 
                    $vBCFCPST                          = $ICMS->getElementsByTagName("vBCFCPST")->item(0)->nodeValue; // T014_Valor_Base_FCP_ST 
                    $pFCPST                            = $ICMS->getElementsByTagName("pFCPST")->item(0)->nodeValue; // T014_Percentual_FCP_ST 
                    $vFCPST                            = $ICMS->getElementsByTagName("vFCPST")->item(0)->nodeValue; // T014_Valor_FCP_ST  
                    $vBCFCPSTRet                       = $ICMS->getElementsByTagName("vBCFCPSTRet")->item(0)->nodeValue; // T014_Valor_Base_FCP_Ret  
                    $pFCPSTRet                         = $ICMS->getElementsByTagName("pFCPSTRet")->item(0)->nodeValue; // T014_Percentual_FCP_Ret  
                    $vFCPSTRet                         = $ICMS->getElementsByTagName("vFCPSTRet")->item(0)->nodeValue; // T014_Valor_FCP_Ret  
                    $vBCFCPUFDest                      = $ICMS->getElementsByTagName("vBCFCPUFDest")->item(0)->nodeValue; // T014_Valor_Base_FCP_Dest  
                    $pFCPUFDest                        = $ICMS->getElementsByTagName("pFCPUFDest")->item(0)->nodeValue; // T014_Percentual_FCP_Dest
                    $vFCPUFDest                        = $ICMS->getElementsByTagName("vFCPUFDest")->item(0)->nodeValue; // T014_Valor_FCP_Dest
                    //**Essas duas tags abaixo não vem em todo XML */
                    $qBCMonoRet                        = $ICMS->getElementsByTagName("qBCMonoRet")->item(0)->nodeValue; // T014A_Valor_Base_ICMS_Mono_Retido
                    $qBCMonoRet                        = (empty($qBCMonoRet)) ? '0.00' : $qBCMonoRet;
                    $vICMSMonoRet                      = $ICMS->getElementsByTagName("vICMSMonoRet")->item(0)->nodeValue; // T014A_Valor_ICMS_Mono_Retido
                    $vICMSMonoRet                      = (empty($vICMSMonoRet)) ? '0.00' : $vICMSMonoRet;
                }
                
                $pIPI = $vBC_IPI = $vIPI = $T014_Situacao_Tributaria_IPI = '';
                if (!empty($TAGIPI)) {
                    $pIPI                         = $TAGIPI->getElementsByTagName("pIPI")->item(0)->nodeValue;
                    $vBC_IPI                      = $TAGIPI->getElementsByTagName("vBC")->item(0)->nodeValue;
                    $vIPI                         = $TAGIPI->getElementsByTagName("vIPI")->item(0)->nodeValue;
                    $T014_Situacao_Tributaria_IPI = $TAGIPI->getElementsByTagName("CST")->item(0)->nodeValue; //T014_Situacao_Tributaria_IPI
                }
                
                // PIS
                if (!empty($PIS)) {
                    $T014_Situacao_Tributaria_PIS = $PIS->getElementsByTagName("CST")->item(0)->nodeValue; // T014_Situacao_Tributaria_PIS
                    $T014_Valor_Base_PIS          = $PIS->getElementsByTagName("vBC")->item(0)->nodeValue; // T014_Valor_Base_PIS
                    $T014_Aliquota_PIS            = $PIS->getElementsByTagName("pPIS")->item(0)->nodeValue; // T014_Aliquota_PIS
                    $T014_Valor_PIS               = $PIS->getElementsByTagName("vPIS")->item(0)->nodeValue; // T014_Valor_PIS       
                }
                
                // COFINS
                if (!empty($COFINS)) {
                    $T014_Situacao_Tributaria_COFINS = $COFINS->getElementsByTagName("CST")->item(0)->nodeValue; // T014_Situacao_Tributaria_COFINS
                    $T014_Valor_Base_COFINS          = $COFINS->getElementsByTagName("vBC")->item(0)->nodeValue; // T014_Valor_Base_COFINS
                    $T014_Aliquota_COFINS            = $COFINS->getElementsByTagName("pCOFINS")->item(0)->nodeValue; // T014_Aliquota_COFINS
                    $T014_Valor_COFINS               = $COFINS->getElementsByTagName("vCOFINS")->item(0)->nodeValue; // T014_Valor_COFINS
                }
 
                if (!empty($IPIDev)) {
                    $vIPIDev = $IPIDev->getElementsByTagName("vIPIDevol")->item(0)->nodeValue;
                }          

                // SOMANDO O VALOR vIPI + vIPIDevol, VAMOS UTILIZAR O MESMO CAMPO T014_Valor_IPI
                $vIPI = $vIPI + $vIPIDev;
                
                $CFOP  = $prod->getElementsByTagName("CFOP")->item(0)->nodeValue;
                if(!$D006_Id){
                    $D006  = mysql_query("SELECT D006_Id, 
                                                 D006_Flag_Gera_Contas, 
                                                 D006_D014_Id, 
                                                 D006_D073_Id, 
                                                 D006_Codigo_CFOP 
                                            FROM D006 
                                           WHERE (replace(replace(replace(D006_Codigo_CFOP,'.',''),',',''),'-','')='$CFOP' OR LOCATE('$CFOP', REPLACE(REPLACE(D006_Codigo_CFOP_Contra_Partida,'.',''),'-','')) > 0) 
                                             AND D006_Flag_Entrada_Saida = 'E'
                                             AND D006_Flag_Inativo != 'S'");
                    $mD006 = mysql_fetch_array($D006);                    
                } else {
                    $mD006['D006_Id'] = $D006_Id;
                }
                
                if ($mD006['D006_Id'] <= 0 AND $cadastrarCFOP == 'on') {
                    $digito = substr($CFOP, 0, 1);
                    switch ($digito){
                        case 5:
                            $cfopCompra = '1.'.substr($CFOP, 1, 3);
                            break;
                        case 6:
                            $cfopCompra = '2.'.substr($CFOP, 1, 3);
                            break;
                        case 7:
                            $cfopCompra = '3.'.substr($CFOP, 1, 3);
                            break;
                    }
                    $CFOP = substr($CFOP, 0, 1) . '.' . substr($CFOP, -3);
                    $mD006['D006_Id'] = $VEN003->cadastrarCFOP($cfopCompra, $natOp, $vICMS, $iCST, $vIPITot, $T013_Valor_Total_PIS, $T014_Situacao_Tributaria_PIS, $T013_Valor_Total_COFINS, $T014_Situacao_Tributaria_COFINS, $CFOP);
                    
                }else if($mD006['D006_Id'] <= 0){
                    return "A CFOP " . $CFOP . " não está cadastrada. Cadastre e tente novamente.  SELECT D006_Id FROM D006 WHERE (replace(replace(replace(D006_Codigo_CFOP,'.',''),',',''),'-','')='$CFOP' OR LOCATE('$CFOP', REPLACE(REPLACE(D006_Codigo_CFOP_Contra_Partida,'.',''),'-','')) > 0) AND (D006_Flag_Venda_Compra_Outros='C' OR D006_Flag_Venda_Compra_Outros='DV' OR D006_Flag_Venda_Compra_Outros='DC' OR (D006_Flag_Venda_Compra_Outros='O' AND D006_Flag_Entrada_Saida = 'E') OR (D006_Flag_Venda_Compra_Outros='T' AND D006_Flag_Entrada_Saida='E'))";
                }

                $CFOP = mysql_real_escape_string($CFOP);
                /*
                if (empty($mD006[D006_D006_Id])) {
                $mCFOP[5]=1;
                $mCFOP[6]=2;
                $CFOP=$mCFOP[substr($CFOP,0,1)].'.'.substr($CFOP,-3);
                }
                
                $cClausula  = " select ";
                $cClausula .= " D006_Id ";
                $cClausula .= " from D006 ";
                $cClausula .= " where D006_Id='{$mD006['D006_D006_Id']}' ";
                $cClausula .= "    or D006_Codigo_CFOP='$CFOP' ";
                $cSql_D006 = mysql_query($cClausula);
                $mD006 = mysql_fetch_array($cSql_D006);
                $cErro=mysql_error();
                if (!empty($cErro))
                {
                mysql_query("ROLLBACK");
                return "Erro: " . $cErro ."<br />". $cClausula;
                }*/

                if (substr($mC004['C004_Cnpj'],0,10) == substr($xCNPJ,0,10)){
                    // Importação de notas fiscais entre filiais
                    $cClausula = <<<EOT
                        select
                            D009_Id 
                        from D009 
                        left join D049 on D009_D049_Id=D049_Id 
                        left join D001 on D049_D001_Id=D001_Id
                        left join D083 on D083_D001_Id=D001_Id and D083_D024_Id='$nD024_Id'
                        where
                            D009_C004_Id='{$g['empresaAtual']}' 
                            and D001_Codigo_Produto='$CodigoProd' 
EOT;
                } else {
                    // Importação de notas fiscais de terceiros
                    $cClausula = <<<EOT
                        select
                            D009_Id 
                        from D009 
                        left join D049 on D009_D049_Id=D049_Id 
                        left join D001 on D049_D001_Id=D001_Id
                        left join D083 on D083_D001_Id=D001_Id and D083_D024_Id='$nD024_Id'
                        where
                            D009_C004_Id='{$g['empresaAtual']}' 
                            and D083_Codigo_Produto_Fornecedor='$CodigoProd' 
EOT;
                }
                $cSql_D009 = mysql_query($cClausula);
                $mD009     = mysql_fetch_array($cSql_D009);
                $cErro     = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . $cErro . "<br />" . $cClausula;
                }
                
                $cClausula = <<<EOT
	                insert into T014 (
		                T014_T013_Id, 
		                T014_D009_Id, 
		                T014_D006_Id, 
                        T014_D006_Id_Original,
                        T014_CFOP_Xml,
		                T014_D037_Id, 
		                T014_C004_Id, 
		                T014_Unidade, 
		                T014_Codigo_Produto, 
		                T014_Descricao_Produto, 
		                T014_Quantidade, 
		                T014_Valor_Custo_Unitario, 
		                T014_Valor_Total_Custo, 
		                T014_Aliquota_IPI, 
		                T014_Valor_Base_IPI, 
		                T014_Valor_IPI, 
		                T014_Classificacao_Fiscal, 
		                T014_Aliquota_ICMS, 
		                T014_Percentual_Reducao_ICMS, 
		                T014_Percentual_Reducao_ICMS_ST, 
		                T014_Valor_Base_ICMS, 
		                T014_Valor_ICMS, 
		                T014_Valor_Total_Desconto, 
		                T014_Valor_Desconto_Unitario, 
		                T014_Valor_ICMS_Substituicao, 
		                T014_Flag_Situacao_Tributaria, 
		                T014_Modalidade_ICMS, 
		                T014_Modalidade_ICMS_Substituicao, 
		                T014_ICMS_Inter_Estadual, 
		                T014_Aliquota_ICMS_Interno, 
		                T014_Valor_Base_ICMS_Substituicao, 
		                T014_Situacao_Tributaria_IPI, 
		                T014_Situacao_Tributaria_PIS, 
		                T014_Valor_Base_PIS, 
		                T014_Aliquota_PIS, 
		                T014_Valor_PIS, 
		                T014_Situacao_Tributaria_COFINS, 
		                T014_Valor_Base_COFINS, 
		                T014_Aliquota_COFINS, 
		                T014_Valor_COFINS, 
		                T014_MVA_Ajustada, 
		                T014_Flag_Origem_Produto,
                        T014_Numero_Pedido_Compra,
                        T014_Item_Pedido_Compra,
                        T014_Valor_Base_FCP, 
                        T014_Percentual_FCP,
                        T014_Valor_FCP, 
                        T014_Valor_Base_FCP_ST, 
                        T014_Percentual_FCP_ST,
                        T014_Valor_FCP_ST, 
                        T014_Valor_Base_FCP_Ret, 
                        T014_Percentual_FCP_Ret,
                        T014_Valor_FCP_Ret, 
                        T014_Valor_Base_FCP_Dest, 
                        T014_Percentual_FCP_Dest,
                        T014_Valor_FCP_Dest,
                        T014_Codigo_Barras
	                ) values ( 
		                '$nT013_Id', 
		                '{$mD009['D009_Id']}', 
		                '{$mD006['D006_Id']}', 
                        '{$mD006['D006_Id']}', 
                        '{$CFOP}',
		                '0', 
		                '{$g['empresaAtual']}', 
		                '$Unidade', 
		                '$CodigoProd', 
		                '$Descricao', 
		                '$Quantidade', 
		                '$nValor_Unitario', 
		                '$nValor_Total_Produto', 
		                '$pIPI', 
		                '$vBC_IPI', 
		                '$vIPI', 
		                '$ClassFiscal', 
		                '$AliqICMS', 
		                '$RedICMS', 
		                '$RedICMSST', 
		                '$vBC_ICMS', 
		                '$vICMS', 
		                '$vDesc', 
		                T014_Valor_Total_Desconto / T014_Quantidade, 
		                '{$T014_Valor_ICMS_Substituicao}', 
		                '{$T014_Flag_Situacao_Tributaria}', 
		                '{$T014_Modalidade_ICMS}', 
		                '{$T014_Modalidade_ICMS_Substituicao}', 
		                '{$T014_ICMS_Inter_Estadual}', 
		                '{$T014_Aliquota_ICMS_Interno}', 
		                '{$T014_Valor_Base_ICMS_Substituicao}', 
		                '{$T014_Situacao_Tributaria_IPI}', 
		                '{$T014_Situacao_Tributaria_PIS}', 
		                '{$T014_Valor_Base_PIS}', 
		                '{$T014_Aliquota_PIS}', 
		                '{$T014_Valor_PIS}', 
		                '{$T014_Situacao_Tributaria_COFINS}', 
		                '{$T014_Valor_Base_COFINS}', 
		                '{$T014_Aliquota_COFINS}', 
		                '{$T014_Valor_COFINS}', 
		                '{$T014_MVA_Ajustada}', 
		                '{$Orig}',
                        '{$numeroPedido}',
                        '{$itemPedido}',
                        '{$vBCFCP}',
                        '{$pFCP}',
                        '{$vFCP}',
                        '{$vBCFCPST}',
                        '{$pFCPST}',
                        '{$vFCPST}',
                        '{$vBCFCPSTRet}',
                        '{$pFCPSTRet}',
                        '{$vFCPSTRet}',
                        '{$vBCFCPUFDest}',
                        '{$pFCPUFDest}',
                        '{$vFCPUFDest}',
                        '{$cEAN}'
	                ) 
EOT;
                mysql_query($cClausula);
                $cErro = mysql_error();
                //echo $cClausula;
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . $cErro . "<br />" . $cClausula;
                }
                $T014_Id = $g['mysqlLastId'];
                if (empty($T014_Id)) {
                    $T014_Id = mysql_insert_id();
                }
                
                if($vinculoOcAuto && !empty($numeroPedido) && !empty($itemPedido)){

                    $sqlT225 = mysql_query("SELECT T225_Id 
                                              FROM T225 
                                         LEFT JOIN T224 ON T224_Id=T225_T224_Id 
                                             WHERE T225_T224_Id = '{$numeroPedido}' 
                                               AND T225_Item = '{$itemPedido}' 
                                               AND T224_D024_Id = '{$nD024_Id}' 
                                               AND T225_Quantidade_Pendente_3(T225_Id) >= '{$Quantidade}'
                                               AND T224_C004_Id = '{$g['empresaAtual']}'");
                    
                    if(mysql_num_rows($sqlT225) == 1){
                        $rowT225 = mysql_fetch_assoc($sqlT225);
                        mysql_query("UPDATE T014 SET T014_T225_Id = '{$rowT225['T225_Id']}' WHERE T014_Id = '{$T014_Id}'");
                        mysql_query("call T225_Gravar_Totalizacao({$rowT225['T225_Id']});");
                    }
                }
                
                $cClausula = "INSERT INTO T014A(T014A_T014_Id, 
                                                T014A_Quantidade, 
                                                T014A_Valor_Custo_Unitario, 
                                                T014A_Aliquota_IPI, 
                                                T014A_Aliquota_PIS, 
                                                T014A_Aliquota_COFINS,
                                                T014A_Flag_Destino_Produto,
                                                T014A_Aliquota_ICMS,
                                                T014A_ICMS_Inter_Estadual,
                                                T014A_Aliquota_ICMS_Interno,
                                                T014A_Valor_ICMS_Substituicao,
                                                T014A_Valor_FCP_ST,
                                                T014A_Codigo_Barras_Tributavel,
                                                T014A_Valor_Base_ICMS_Mono_Retido,
                                                T014A_Valor_ICMS_Mono_Retido,
                                                T014A_Ficha_Conteudo_Importacao
                                       )VALUES (
                                                '{$T014_Id}', 
                                                '{$Quantidade}', 
                                                '{$nValor_Unitario}',  
                                                '{$pIPI}',
                                                '{$T014_Aliquota_PIS}', 
                                                '{$T014_Aliquota_COFINS}',
                                                '2',
                                                '{$AliqICMS}',
                                                '{$T014_ICMS_Inter_Estadual}',
                                                '{$T014_Aliquota_ICMS_Interno}',
                                                '{$T014_Valor_ICMS_Substituicao}',
                                                '{$vFCPST}',
                                                '{$cEANTrib}',
                                                '{$qBCMonoRet}',
                                                '{$vICMSMonoRet}',
                                                '{$nFCI}')";
                    
                mysql_query($cClausula);
                $cErro = mysql_error();
                                
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . $cErro . "<br />" . $cClausula;
                }  
                
                mysql_query("CALL T014_Gravar_Totalizacao_Custo('{$T014_Id}')");
            }  
            

            if($g['C031']['atualizarAliquotasAoImportarXML'] === 'S')  {      
                
                //Verifica se tem itens na nota sem vinculo com D009
                $itensComVinculo = $this->EST004ValidarVinculoProduto(null,$nT013_Id);

                if($itensComVinculo === true) {  
                    $T014  = mysql_query("SELECT T014_Id FROM T014 WHERE T014_T013_Id = '{$nT013_Id}'");                    
                    while($mT014 = mysql_fetch_array($T014)) {
                    
                        $cClausula = mysql_query("SELECT T013_Flag_Travar_Nota,
                                                         D009_Id,
                                                         T014_Id,
                                                         T013_Id,
                                                         T014_Valor_Frete,
                                                         T014_Valor_Despesas_Acessorias,
                                                         T014_Valor_Seguro,
                                                         T014_Valor_Total_Custo,
                                                         T014_Quantidade,
                                                         T014_Valor_Total_Custo_Final,
                                                         T013_Valor_Frete,
                                                         T013_Valor_Seguro,
                                                         T013_Valor_Despesas_Acessorias,
                                                         T014A_Flag_Destino_Produto,
                                                         Informada.D006_Flag_Somar_Despesas_Base_IPI,
                                                         Informada.D006_Flag_Ajustar_MVA,
                                                         Informada.D006_Flag_Somar_IPI_Base_ICMS,
                                                         Informada.D006_Flag_Somar_Fator_ICMS_Dif_Base,
                                                         Informada.D006_Modalidade_ICMS_Substituicao,
                                                         Informada.D006_Flag_Calcula_FCP_ST,
                                                         Informada.D006_Flag_Forma_Calculo_COFINS,
                                                         Informada.D006_Flag_Deduz_ICMS_Base_COFINS,
                                                         Informada.D006_Flag_Forma_Calculo_PIS,
                                                         Informada.D006_Flag_Deduz_ICMS_Base_PIS,
                                                         D009_Preco_Pauta_ST,
                                                         D053_Calculo_ST_Por_Dentro_Compra,
                                                         T014_Valor_Total_Desconto,
                                                         Informada.D006_Flag_Somar_Despesas_Base_PIS,
                                                         Informada.D006_Flag_Somar_Despesas_Base_COFINS,
                                                         T013_Flag_Calcular_ICMS_Despesas_Acessorias
                                                    FROM T014
                                               LEFT JOIN T013 ON T013_Id = T014_T013_Id
                                               LEFT JOIN D009 ON D009_Id = T014_D009_Id  
                                               LEFT JOIN T014A ON T014A_T014_Id = T014_Id
                                               LEFT JOIN D006 as Informada ON Informada.D006_Id=T014_D006_Id
                                               LEFT JOIN D005 ON D005_Classificacao_Fiscal = T014_Classificacao_Fiscal
                                               LEFT JOIN D149 ON D149_D005_Id = D005_Id AND D149_C004_Id = '{$g['empresaAtual']}'
                                               LEFT JOIN C004 ON C004_Id = '{$g['empresaAtual']}'   
                                               LEFT JOIN D024 ON D024_Id = T013_D024_Id
                                               LEFT JOIN D053 ON D053_D018_Id = D024_D018_Id AND D053_C004_Id = T013_C004_Id
                                                   WHERE T014_Id = '{$mT014['T014_Id']}'");

                        $cSql_T014  = mysql_fetch_array($cClausula);   
                        $this->EST004AtualizarCFOP(false,$nT013_Id, $mT014['T014_Id']); 
                        $this->EST004AtualizarAliquotas($cSql_T014);                                
                    }               
                                         
                    $this->EST004AtualizarCabecalhoNota(null, $nT013_Id);   
                }         
            }
            
        }

        $parametrosGatilhos = array('T013_Id' => $nT013_Id);
        $this->gatilhoAposFinalizarInclusaoNota($parametrosGatilhos);
        
        mysql_query("COMMIT");

        mysql_query("SET AUTOCOMMIT=1");

        return true;
    }
    
    /**
     * receberNota
     *
     * @param int $T007_Id
     * @return true/string
     */
    public function receberNota($T007_Id)
    {
        global $g, $nT013_Id;
        $retorno  = mysql_query("SELECT * from T007 WHERE T007_Id='{$T007_Id}'");
        $T007     = mysql_fetch_array($retorno);

        $cAno_Mes_NFe  = substr($T007['T007_Data_Emissao'], 0, 7);
        $cArquivo      = "{$g['pathDadosAntigo']}/nfe/xml/autorizado/{$cAno_Mes_NFe}/" . $T007['T007_Numero_Protocolo_Nfe'] . ".xml";

        $arquivo = explode('/', $cArquivo);
        $arquivo = array(
            'name' => $arquivo[count($arquivo)-1],
            'tmp_name' => $cArquivo,
            'interno' => true
        );

        $nT013_Id = false;

        foreach ($g['conf']->ini('faturamentoMultiEmpresaCFOP') as $regra) {
            if ($T007['T007_D006_Id'] == $regra['origem']) {
                $D006_Id = $regra['destino'];
                break;
            }
        }

        if($T007['T007_Flag_ACP'] == 6){

            $retorno = $this->ImportarXML($arquivo, 'on', false, $D006_Id);

            //Vincula os produtos automaticamente
            mysql_query("UPDATE T014
                      LEFT JOIN D001 ON D001_Codigo_Produto=T014_Codigo_Produto
                      LEFT JOIN D049 ON D049_D001_Id=D001_Id
                      LEFT JOIN D009 ON D009_D049_Id=D049_Id AND D009_C004_Id = '{$g['empresaAtual']}'
                      LEFT JOIN T066 ON T066_D009_Id=D009_Id
                            SET T014_D009_Id = D009_Id,
                                T014_T066_Id = T066_Id
                          WHERE T014_T013_Id = '{$nT013_Id}'");

            return $retorno;
        }

        /*
        *   METODO ANTIGO - IMPORTAVA O XML ATRAVÉS DO REGISTRO T007
        */
        $idMatriz = $g['conf']->ini('faturamentoMultiEmpresaIdMatriz');

        if ($idMatriz > 0) {
            $retorno = mysql_query("SELECT C004_D024_Id from C004 WHERE C004_Id='{$idMatriz}'");
            $D024    = mysql_fetch_array($retorno);
            if ($D024['C004_D024_Id'] <= 0)
                return "A matriz não possui cliente/fornecedor associado, favor verificar";
        } else {            
            return 'Não foi definido o ID da matriz, favor verificar o _GLOBAL.php';
        }

        // o fornecedor é a matriz
        $row['T013_D024_Id'] = $D024['C004_D024_Id'];
        
        // a CFOP deve vir da relação entre CFOP entrada/saida definido no global		
        $row['T013_D006_Id'] = 0;
        foreach ($g['conf']->ini('faturamentoMultiEmpresaCFOP') as $regra ) {
            if ($T007['T007_D006_Id'] == $regra['origem']) {
                $row['T013_D006_Id'] = $regra['destino'];
                break;
            }
        }
        
        if ($row['T013_D006_Id'] == 0) {
            return 'Nenhuma CFOP relacionada no arquivo de configuração, verifique';
        }

        $D006                                               = mysql_query("SELECT D006_Flag_Somar_IPI_Base_ICMS FROM D006 WHERE D006_Id='{$row['T013_D006_Id']}'");
        $mD006                                              = mysql_fetch_array($D006);
        $row['T013_Tipo']                                   = '1'; // sempre tipo NOTA FISCAL
        $row['T013_Numero_Nota_Fiscal']                     = $T007['T007_Numero_Nota_Fiscal'];
        $row['T013_Data_Emissao']                           = gCorrigeData($T007['T007_Data_Emissao']);
        $row['T013_Data_Entrada']                           = gCorrigeData(date("Y-m-d"));
        $row['T013_Valor_Total_Produtos']                   = gCorrigeNumero($T007['T007_Valor_Total_Produtos']);
        $row['T013_Valor_Desconto_Produtos']                = gCorrigeNumero($T007['T007_Valor_Desconto']); // a confimar
        $row['T013_Valor_Total']                            = gCorrigeNumero($T007['T007_Valor_Total']);
        $row['T013_Valor_Desconto']                         = gCorrigeNumero($T007['T007_Valor_Desconto']);
        $row['T013_Observacao_1']                           = "Nota entrada automaticamente (matriz->filial)";
        $row['T013_Valor_Total_Base_ICMS']                  = gCorrigeNumero($T007['T007_Valor_Total_Base_ICMS']);
        $row['T013_Valor_Total_Base_ICMS_Diferido']         = gCorrigeNumero($T007['T007_Valor_Total_Base_ICMS_Diferido']);
        $row['T013_Valor_Total_ICMS']                       = gCorrigeNumero($T007['T007_Valor_Total_ICMS']);
        $row['T013_Aliquota_ICMS']                          = gCorrigeNumero($T007['T007_Aliquota_ICMS']);
        $row['T013_Valor_Total_Base_ICMS_Substituicao']     = gCorrigeNumero($T007['T007_Valor_Base_ICMS_Substituicao']);
        $row['T013_Valor_ICMS_Substituicao']                = gCorrigeNumero($T007['T007_Valor_ICMS_Substituicao_Retencao']);
        $row['T013_Valor_Total_IPI']                        = gCorrigeNumero($T007['T007_Valor_Total_IPI']);
        $row['T013_Flag_Calcular_ICMS_IPI']                 = $mD006['D006_Flag_Somar_IPI_Base_ICMS']; // vem da regra CFOP configurada
        $row['T013_Flag_Arredondar_Valor_IPI']              = "S"; // default "S" no form, "N" na base
        $row['T013_Numero_Conhecimento']                    = $T007['T007_Numero_Conhecimento'];
        $row['T013_Flag_Frete']                             = $T007['T007_Flag_Frete'];
        $row['T013_D022_Id']                                = $T007['T007_D022_Id'];
        $row['T013_Tipo_Transporte']                        = $T007['T007_Tipo_Transporte'];
        $row['T013_Valor_Frete']                            = gCorrigeNumero($T007['T007_Valor_Frete']);
        $row['T013_Flag_Calcular_ICMS_Frete']               = "N"; // default "N" no form, "N" na base		
        $row['T013_Flag_Calcular_IPI_Frete']                = "N"; // default "N" no form, "N" na base
        $row['T013_Mao_Obra_1']                             = $T007['T007_Mao_Obra_1'];
        $row['T013_Codigo_Servico']                         = "";
        $row['T013_Valor_Mao_Obra']                         = gCorrigeNumero($T007['T007_Valor_Mao_Obra']);
        $row['T013_Aliquota_ISS']                           = gCorrigeNumero($T007['T007_Aliquota_ISS']);
        $row['T013_Valor_ISS']                              = gCorrigeNumero($T007['T007_Valor_ISS']);
        $row['T013_Valor_Credito_IPTU']                     = gCorrigeNumero("0.00");
        $row['T013_D014_Id']                                = 0; // plano de contas, default 0 na base
        $row['T013_D031_Id']                                = 0; // portador a pagar, está comentado no form
        $row['T013_Numero_Parcelas']                        = $T007['T007_Numero_Parcelas'];
        $row['T013_Numero_Dias']                            = $T007['T007_Numero_Dias'];
        $row['T013_Valor_Juros']                            = gCorrigeNumero("0.00"); // default 0.00 na base
        $row['T013_Flag_Validacoes']                        = "N"; // default "S" na base e no form
        $row['T013_Flag_Calcular']                          = "N";
        $row['tituloDespesasAcessorias']                    = ""; // só um titulo trazido do form desnecessariamente no cadastrarNota
        $row['T013_Valor_Despesas_Acessorias']              = gCorrigeNumero($T007['T007_Valor_Despesas_Acessorias']); // default 0.00 na base
        $row['T013_Flag_Calcular_ICMS_Despesas_Acessorias'] = "S"; // default "S" na base e no form
        $row['T013_Flag_Calcular_IPI_Despesas_Acessorias']  = "S"; // default "S" na base e no form				
        $row['tituloSeguro']                                = ""; // só um titulo trazido do form desnecessariamente no cadastrarNota
        $row['T013_Valor_Seguro']                           = gCorrigeNumero($T007['T007_Valor_Seguro']);
        $row['T013_Flag_Calcular_ICMS_Seguro']              = "S"; // default "S" no form, "N" na base
        $row['T013_Flag_Calcular_IPI_Seguro']               = "S"; // default "S" no form, "N" na base
        $row['T013_Numero_Nota_Fiscal_Devolucao']           = null; // hidden no form, default NULL na base
        $row['T013_Codigo_CFOP']                            = $T007['T007_Codigo_CFOP'];
        $row['T013_Numero_Controle']                        = $T007['T007_Numero_Controle'];
        $row['T013_Parcelas']                               = null; // hidden no form, default NULL na base
        $row['T013_Valor_Encargos']                         = gCorrigeNumero("0.00"); // hidden no form, default 0.000000 na base
        $row['T013_Numero_Ordem_Compra']                    = 0; // hidden no form, default 0 na base
        $row['T013_Valor_Total_Base_IPI']                   = gCorrigeNumero($T007['T007_Valor_Total_Base_IPI']);
        $row['T013_Nome_Transportadora']                    = $T007['T007_Nome_Transportadora'];
        $row['T013_T085_Id']                                = null; // hidden no form, default NULL na base
        $row['T013_Mao_Obra_2']                             = $T007['T007_Mao_Obra_2'];
        $row['T013_Valor_Dolar']                            = gCorrigeNumero($T007['T007_Valor_Dolar']);
        $row['T013_C008_Id']                                = $T007['T007_C008_Id'];
        $row['T013_Casas_Decimais_Valor_ICMS']              = 2; // default 2 no form e na base
        $row['T013_C004_Id']                                = $g['empresaAtual'];
        
        require_once('bibliotecas/classes/GFormWrap.php');
        $form          = new GFormWrap($row);
        $cabecalhoNota = $this->cadastrarNota($form, null);
        if ($cabecalhoNota !== true) {
            return $cabecalhoNota;
        }
        
        $T013_Id  = $g['mysqlLastId'];
        
        if (empty($T013_Id)) {
            $T013_Id = mysql_insert_id();
        }

        $nT013_Id = $g['mysqlLastId'];
        
        $retorno = mysql_query("
						SELECT 
							T008_D009_Id,
							T008_Quantidade,
							T008_Valor_Preco_Sem_Desconto_Unitario,
							T008_Valor_Total_Preco_Sem_Desconto,
							T008_Aliquota_ICMS,
							T008_Aliquota_IPI,
							T008_Valor_ICMS,
							T008_Valor_IPI 
						from T008 
						WHERE
							T008_T007_Id='{$T007_Id}'");

        while ($T008 = mysql_fetch_assoc($retorno)) {
            // busca o D009 da filial e troca pelo da matriz
            $buscaD009 = mysql_query("select D009_D049_Id from D009 where D009_Id='{$T008['T008_D009_Id']}'");
            $buscaD009 = mysql_fetch_array($buscaD009);
            $buscaD009 = mysql_query("select D009_Id from D009 where D009_D049_Id='{$buscaD009['D009_D049_Id']}' and D009_C004_Id='{$g['empresaAtual']}'");
            $buscaD009 = mysql_fetch_array($buscaD009);
            
            $ret = $this->incluirProdutoNota($buscaD009['D009_Id'], $T013_Id);
            if ($ret !== true) {
                mysql_query("DELETE FROM T014 where T014_T013_Id='{$T013_Id}'");
                mysql_query("DELETE FROM T013 where T013_Id='{$T013_Id}'");
                return $ret;
            }
            $T014_Id = $g['mysqlLastId'];
            if (empty($T014_Id)) {
                $T014_Id = mysql_insert_id();
            }
            
            $sql = <<<EOT
                update 
                	T014
                set
                	T014_Quantidade='{$T008['T008_Quantidade']}',
                	T014_Valor_Custo_Unitario='{$T008['T008_Valor_Preco_Sem_Desconto_Unitario']}',	                                      
                	T014_Valor_Total_Custo='{$T008['T008_Valor_Total_Preco_Sem_Desconto']}',
                	T014_Aliquota_ICMS='{$T008['T008_Aliquota_ICMS']}',
                	T014_Aliquota_IPI='{$T008['T008_Aliquota_IPI']}',
                	T014_Valor_ICMS='{$T008['T008_Valor_ICMS']}',
                	T014_Valor_IPI='{$T008['T008_Valor_IPI']}'
                where
                	T014_Id='{$T014_Id}'
EOT;
            $cSql = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("DELETE FROM T014 where T014_T013_Id='{$T013_Id}'");
                mysql_query("DELETE FROM T013 where T013_Id='{$T013_Id}'");
                return 'Erro ao duplicar itens da T008 para T014, entre em contato com o suporte';
            }
            
        }
        
        return true;
    }
    
    /**
     * cadastrarNota
     *
     * @param Form $form
     * @param int $acao_Id
     * @param boolean $GerarNFOC
     * @param string $extra
     * @return true/string
     */
    public function cadastrarNota($form, $acao_Id, $GerarNFOC=false, $extra="")
    {
        global $g, $ultimoT013_Id;
        
        $T013_Id                                     = $acao_Id;
        $T013_D024_Id                                = $form->campoValorEnviado('T013_D024_Id');
        $T013_D006_Id                                = $form->campoValorEnviado('T013_D006_Id');
        $T013_Tipo                                   = $form->campoValorEnviado('T013_Tipo');
        $T013_Numero_Nota_Fiscal                     = $form->campoValorEnviado('T013_Numero_Nota_Fiscal');
        $T013_Data_Emissao                           = $form->campoValorEnviado('T013_Data_Emissao');
        $T013_Data_Entrada                           = $form->campoValorEnviado('T013_Data_Entrada');
        $T013_Valor_Total_Produtos                   = $form->campoValorEnviado('T013_Valor_Total_Produtos');
        $T013_Valor_Desconto_Produtos                = $form->campoValorEnviado('T013_Valor_Desconto_Produtos');
        $T013_Valor_Total                            = $form->campoValorEnviado('T013_Valor_Total');
        $T013_Valor_Desconto                         = $form->campoValorEnviado('T013_Valor_Desconto');
        $T013_Observacao_1                           = $form->campoValorEnviado('T013_Observacao_1');
        $T013_Valor_Total_Base_ICMS                  = $form->campoValorEnviado('T013_Valor_Total_Base_ICMS');
        $T013_Valor_Total_Base_ICMS_Diferido         = $form->campoValorEnviado('T013_Valor_Total_Base_ICMS_Diferido');
        $T013_Valor_Total_ICMS                       = $form->campoValorEnviado('T013_Valor_Total_ICMS');
        $T013_Aliquota_ICMS                          = $form->campoValorEnviado('T013_Aliquota_ICMS');
        $T013_Valor_Total_Base_ICMS_Substituicao     = $form->campoValorEnviado('T013_Valor_Total_Base_ICMS_Substituicao');
        $T013_Valor_ICMS_Substituicao                = $form->campoValorEnviado('T013_Valor_ICMS_Substituicao');
        $T013_Valor_Total_IPI                        = $form->campoValorEnviado('T013_Valor_Total_IPI');
        $T013_Flag_Calcular_ICMS_IPI                 = $form->campoValorEnviado('T013_Flag_Calcular_ICMS_IPI');
        $T013_Flag_Arredondar_Valor_IPI              = $form->campoValorEnviado('T013_Flag_Arredondar_Valor_IPI');
        $T013_Numero_Conhecimento                    = $form->campoValorEnviado('T013_Numero_Conhecimento');
        $T013_Flag_Frete                             = $form->campoValorEnviado('T013_Flag_Frete');
        $T013_D022_Id                                = $form->campoValorEnviado('T013_D022_Id');
        $T013_Tipo_Transporte                        = $form->campoValorEnviado('T013_Tipo_Transporte');
        $T013_Valor_Frete                            = $form->campoValorEnviado('T013_Valor_Frete');
        $T013_Flag_Calcular_ICMS_Frete               = $form->campoValorEnviado('T013_Flag_Calcular_ICMS_Frete');
        $T013_Flag_Calcular_IPI_Frete                = $form->campoValorEnviado('T013_Flag_Calcular_IPI_Frete');
        $T013A_Flag_Destaca_Frete_SPED               = $form->campoValorEnviado('T013A_Flag_Destaca_Frete_SPED');
        $T013_Mao_Obra_1                             = $form->campoValorEnviado('T013_Mao_Obra_1');
        $T013_Codigo_Servico                         = $form->campoValorEnviado('T013_Codigo_Servico');
        $T013_Valor_Mao_Obra                         = $form->campoValorEnviado('T013_Valor_Mao_Obra');
        $T013_Aliquota_ISS                           = $form->campoValorEnviado('T013_Aliquota_ISS');
        $T013_Valor_ISS                              = $form->campoValorEnviado('T013_Valor_ISS');
        $T013_Valor_Credito_IPTU                     = $form->campoValorEnviado('T013_Valor_Credito_IPTU');
        $T013_D014_Id                                = $form->campoValorEnviado('T013_D014_Id');
        $T013_D031_Id                                = $form->campoValorEnviado('T013_D031_Id');
        $T013_Numero_Parcelas                        = $form->campoValorEnviado('T013_Numero_Parcelas');
        $T013_Numero_Dias                            = $form->campoValorEnviado('T013_Numero_Dias');
        $T013_Valor_Juros                            = $form->campoValorEnviado('T013_Valor_Juros');
        $T013_Flag_Validacoes                        = $form->campoValorEnviado('T013_Flag_Validacoes');
        $T013_Flag_Calcular                          = $form->campoValorEnviado('T013_Flag_Calcular');
        $tituloDespesasAcessorias                    = $form->campoValorEnviado('tituloDespesasAcessorias');
        $T013_Valor_Despesas_Acessorias              = $form->campoValorEnviado('T013_Valor_Despesas_Acessorias');
        $T013_Flag_Calcular_ICMS_Despesas_Acessorias = $form->campoValorEnviado('T013_Flag_Calcular_ICMS_Despesas_Acessorias');
        $T013_Flag_Calcular_IPI_Despesas_Acessorias  = $form->campoValorEnviado('T013_Flag_Calcular_IPI_Despesas_Acessorias');
        $tituloSeguro                                = $form->campoValorEnviado('tituloSeguro');
        $T013_Valor_Seguro                           = $form->campoValorEnviado('T013_Valor_Seguro');
        $T013_Flag_Calcular_ICMS_Seguro              = $form->campoValorEnviado('T013_Flag_Calcular_ICMS_Seguro');
        $T013_Flag_Calcular_IPI_Seguro               = $form->campoValorEnviado('T013_Flag_Calcular_IPI_Seguro');
        $T013_Numero_Nota_Fiscal_Devolucao           = $form->campoValorEnviado('T013_Numero_Nota_Fiscal_Devolucao');
        $T013_Codigo_CFOP                            = $form->campoValorEnviado('T013_Codigo_CFOP');
        $T013_Numero_Controle                        = $form->campoValorEnviado('T013_Numero_Controle');
        $T013_Parcelas                               = $form->campoValorEnviado('T013_Parcelas');
        $T013_Valor_Encargos                         = $form->campoValorEnviado('T013_Valor_Encargos');
        $T013_Numero_Ordem_Compra                    = $form->campoValorEnviado('T013_Numero_Ordem_Compra');
        $T013_Valor_Total_Base_IPI                   = $form->campoValorEnviado('T013_Valor_Total_Base_IPI');
        $T013_Nome_Transportadora                    = $form->campoValorEnviado('T013_Nome_Transportadora');
        $T013_T085_Id                                = $form->campoValorEnviado('T013_T085_Id');
        $T013_Mao_Obra_2                             = $form->campoValorEnviado('T013_Mao_Obra_2');
        $T013_Valor_Dolar                            = $form->campoValorEnviado('T013_Valor_Dolar');
        $T013_C008_Id                                = $form->campoValorEnviado('T013_C008_Id');
        $T013_C004_Id                                = $form->campoValorEnviado('T013_C004_Id');
        $T013_Casas_Decimais_Valor_ICMS              = $form->campoValorEnviado('T013_Casas_Decimais_Valor_ICMS');
        $T013_Flag_Obrigatorio_OC                    = $form->campoValorEnviado('T013_Flag_Obrigatorio_OC');
        
        $operacao = (empty($T013_Id)) ? 1 : 2;
        
        if (empty($T013_Id) and $operacao == 2) {
            return "T013_Id não existe";
        }
        
        //-- INCREMENTA BARRA INVERTIDA PARA CARACTERES ESPECIAIS
        $T013_C008_Id                                = addslashes($T013_C008_Id);
        $T013_C004_Id                                = addslashes($T013_C004_Id);
        $T013_D006_Id                                = addslashes($T013_D006_Id);
        $T013_Tipo                                   = addslashes($T013_Tipo);
        $T013_D024_Id                                = addslashes($T013_D024_Id);
        //$T013_Nome_Empresa=addslashes($T013_Nome_Empresa);
        $T013_Numero_Nota_Fiscal_Devolucao           = addslashes($T013_Numero_Nota_Fiscal_Devolucao);
        $T013_Numero_Nota_Fiscal                     = addslashes($T013_Numero_Nota_Fiscal);
        $T013_Parcelas                               = addslashes($T013_Parcelas);
        $T013_Observacao_1                           = addslashes($T013_Observacao_1);
        $T013_Casas_Decimais_Valor_ICMS              = addslashes($T013_Casas_Decimais_Valor_ICMS);
        $T013_Flag_Calcular_ICMS_IPI                 = addslashes($T013_Flag_Calcular_ICMS_IPI);
        $T013_Flag_Arredondar_Valor_IPI              = addslashes($T013_Flag_Arredondar_Valor_IPI);
        $T013_Numero_Conhecimento                    = addslashes($T013_Numero_Conhecimento);
        $T013_Flag_Frete                             = addslashes($T013_Flag_Frete);
        $T013_Tipo_Transporte                        = addslashes($T013_Tipo_Transporte);
        $T013_Flag_Calcular_ICMS_Frete               = addslashes($T013_Flag_Calcular_ICMS_Frete);
        $T013_Flag_Calcular_IPI_Frete                = addslashes($T013_Flag_Calcular_IPI_Frete);
        $T013A_Flag_Destaca_Frete_SPED               = addslashes($T013A_Flag_Destaca_Frete_SPED);
        $T013_Nome_Transportadora                    = addslashes($T013_Nome_Transportadora);
        $T013_D022_Id                                = addslashes($T013_D022_Id);
        $T013_D031_Id                                = addslashes($T013_D031_Id);
        $T013_Flag_Calcular_ICMS_Despesas_Acessorias = addslashes($T013_Flag_Calcular_ICMS_Despesas_Acessorias);
        $T013_Flag_Calcular_IPI_Despesas_Acessorias  = addslashes($T013_Flag_Calcular_IPI_Despesas_Acessorias);
        $T013_Mao_Obra_1                             = addslashes($T013_Mao_Obra_1);
        $T013_Mao_Obra_2                             = addslashes($T013_Mao_Obra_2);
        $T013_Flag_Calcular_ICMS_Seguro              = addslashes($T013_Flag_Calcular_ICMS_Seguro);
        $T013_Flag_Calcular_IPI_Seguro               = addslashes($T013_Flag_Calcular_IPI_Seguro);
        $T013_D014_Id                                = addslashes($T013_D014_Id);
        
        //-- ELIMINA OS ESPA�OS DOS CAMPOS:
        $T013_D006_Id                                = trim($T013_D006_Id);
        $T013_Numero_Nota_Fiscal                     = trim($T013_Numero_Nota_Fiscal);
        $T013_Numero_Nota_Fiscal_Devolucao           = trim($T013_Numero_Nota_Fiscal_Devolucao);
        $T013_Nome_Transportadora                    = trim($T013_Nome_Transportadora);
        $T013_Tipo_Transporte                        = trim($T013_Tipo_Transporte);
        $T013_Observacao_1                           = trim($T013_Observacao_1);
        //$T013_Observacao_2=trim($T013_Observacao_2);
        //$T013_Observacao_3=trim($T013_Observacao_3);
        $T013_Mao_Obra_1                             = trim($T013_Mao_Obra_1);
        $T013_Mao_Obra_2                             = trim($T013_Mao_Obra_2);
        $T013_Parcelas                               = trim($T013_Parcelas);
        $T013_Numero_Conhecimento                    = trim($T013_Numero_Conhecimento);
        $T013_C008_Id                                = trim($T013_C008_Id);
        $T013_C004_Id                                = trim($T013_C004_Id);
        $T013_Flag_Calcular_ICMS_Frete               = trim($T013_Flag_Calcular_ICMS_Frete);
        $T013_Flag_Calcular_IPI_Frete                = trim($T013_Flag_Calcular_IPI_Frete);
        $T013A_Flag_Destaca_Frete_SPED               = trim($T013A_Flag_Destaca_Frete_SPED);
        $T013_Flag_Calcular_ICMS_Seguro              = trim($T013_Flag_Calcular_ICMS_Seguro);
        $T013_Flag_Calcular_IPI_Seguro               = trim($T013_Flag_Calcular_IPI_Seguro);
        $T013_Flag_Calcular_ICMS_Despesas_Acessorias = trim($T013_Flag_Calcular_ICMS_Despesas_Acessorias);
        $T013_Flag_Calcular_IPI_Despesas_Acessorias  = trim($T013_Flag_Calcular_IPI_Despesas_Acessorias);
        $T013_Casas_Decimais_Valor_ICMS              = trim($T013_Casas_Decimais_Valor_ICMS);
        //$T013_D031_Id_Entrada=trim($T013_D031_Id_Entrada);
        $T013_D031_Id                                = trim($T013_D031_Id);
        $T013_Flag_Calcular_ICMS_IPI                 = trim($T013_Flag_Calcular_ICMS_IPI);
        $T013_Flag_Arredondar_Valor_IPI              = trim($T013_Flag_Arredondar_Valor_IPI);
        $T013_D024_Id                                = trim($T013_D024_Id);
        $T013_D014_Id                                = trim($T013_D014_Id);
        $T013_D022_Id                                = trim($T013_D022_Id);
        $T013_Flag_Frete                             = trim($T013_Flag_Frete);
        
        //-- CONVERTE EM MINUSCULO E MAIUSCULO:
        $T013_C008_Id                                = strtoupper($T013_C008_Id);
        $T013_C004_Id                                = strtoupper($T013_C004_Id);
        $T013_D006_Id                                = strtoupper($T013_D006_Id);
        $T013_Tipo                                   = strtoupper($T013_Tipo);
        $T013_D024_Id                                = strtoupper($T013_D024_Id);
        //$T013_Nome_Empresa=strtoupper($T013_Nome_Empresa);
        $T013_Numero_Nota_Fiscal_Devolucao           = strtoupper($T013_Numero_Nota_Fiscal_Devolucao);
        $T013_Numero_Nota_Fiscal                     = strtoupper($T013_Numero_Nota_Fiscal);
        $T013_Parcelas                               = strtoupper($T013_Parcelas);
        $T013_Observacao_1                           = strtoupper($T013_Observacao_1);
        $T013_Casas_Decimais_Valor_ICMS              = strtoupper($T013_Casas_Decimais_Valor_ICMS);
        $T013_Flag_Calcular_ICMS_IPI                 = strtoupper($T013_Flag_Calcular_ICMS_IPI);
        $T013_Flag_Arredondar_Valor_IPI              = strtoupper($T013_Flag_Arredondar_Valor_IPI);
        $T013_Numero_Conhecimento                    = strtoupper($T013_Numero_Conhecimento);
        $T013_Flag_Frete                             = strtoupper($T013_Flag_Frete);
        $T013_Tipo_Transporte                        = strtoupper($T013_Tipo_Transporte);
        $T013_Flag_Calcular_ICMS_Frete               = strtoupper($T013_Flag_Calcular_ICMS_Frete);
        $T013_Flag_Calcular_IPI_Frete                = strtoupper($T013_Flag_Calcular_IPI_Frete);
        $T013A_Flag_Destaca_Frete_SPED               = strtoupper($T013A_Flag_Destaca_Frete_SPED);
        $T013_Nome_Transportadora                    = strtoupper($T013_Nome_Transportadora);
        $T013_D022_Id                                = strtoupper($T013_D022_Id);
        $T013_D031_Id                                = strtoupper($T013_D031_Id);
        $T013_Flag_Calcular_ICMS_Despesas_Acessorias = strtoupper($T013_Flag_Calcular_ICMS_Despesas_Acessorias);
        $T013_Flag_Calcular_IPI_Despesas_Acessorias  = strtoupper($T013_Flag_Calcular_IPI_Despesas_Acessorias);
        $T013_Mao_Obra_1                             = strtoupper($T013_Mao_Obra_1);
        $T013_Mao_Obra_2                             = strtoupper($T013_Mao_Obra_2);
        $T013_Flag_Calcular_ICMS_Seguro              = strtoupper($T013_Flag_Calcular_ICMS_Seguro);
        $T013_Flag_Calcular_IPI_Seguro               = strtoupper($T013_Flag_Calcular_IPI_Seguro);
        $T013_D014_Id                                = strtoupper($T013_D014_Id);
        
        //-- CONVERTE OS CAMPOS DO TIPO DATA E NUMERO PARA GRAVAR NO BANCO:
        $T013_Data_Emissao                       = gCorrigeDataInverte($T013_Data_Emissao);
        $T013_Data_Entrada                       = gCorrigeDataInverte($T013_Data_Entrada);
        $T013_Codigo_CFOP                        = gCorrigeNumeroInverte($T013_Codigo_CFOP, 4);
        $T013_Numero_Controle                    = gCorrigeNumeroInverte($T013_Numero_Controle, 1);
        $T013_Valor_Encargos                     = gCorrigeNumeroInverte($T013_Valor_Encargos, 7);
        $T013_Valor_Total_Produtos               = gCorrigeNumeroInverte($T013_Valor_Total_Produtos, 3);
        $T013_Valor_Desconto_Produtos            = gCorrigeNumeroInverte($T013_Valor_Desconto_Produtos, 3);
        $T013_Valor_Total                        = gCorrigeNumeroInverte($T013_Valor_Total, 3);
        $T013_Valor_Desconto                     = gCorrigeNumeroInverte($T013_Valor_Desconto, 3);
        $T013_Numero_Ordem_Compra                = gCorrigeNumeroInverte($T013_Numero_Ordem_Compra, 1);
        $T013_Valor_Total_Base_ICMS              = gCorrigeNumeroInverte($T013_Valor_Total_Base_ICMS, 3);
        $T013_Valor_Total_Base_ICMS_Diferido     = gCorrigeNumeroInverte($T013_Valor_Total_Base_ICMS_Diferido, 3);
        $T013_Valor_Total_ICMS                   = gCorrigeNumeroInverte($T013_Valor_Total_ICMS, 3);
        $T013_Aliquota_ICMS                      = gCorrigeNumeroInverte($T013_Aliquota_ICMS, 3);
        $T013_Valor_Total_Base_ICMS_Substituicao = gCorrigeNumeroInverte($T013_Valor_Total_Base_ICMS_Substituicao, 3);
        $T013_Valor_ICMS_Substituicao            = gCorrigeNumeroInverte($T013_Valor_ICMS_Substituicao, 3);
        $T013_Valor_Total_Base_IPI               = gCorrigeNumeroInverte($T013_Valor_Total_Base_IPI, 3);
        $T013_Valor_Total_IPI                    = gCorrigeNumeroInverte($T013_Valor_Total_IPI, 3);
        $T013_Valor_Frete                        = gCorrigeNumeroInverte($T013_Valor_Frete, 3);
        $T013_Numero_Parcelas                    = gCorrigeNumeroInverte($T013_Numero_Parcelas, 1);
        $T013_Numero_Dias                        = gCorrigeNumeroInverte($T013_Numero_Dias, 1);
        $T013_Valor_Juros                        = gCorrigeNumeroInverte($T013_Valor_Juros, 3);
        $T013_T085_Id                            = gCorrigeNumeroInverte($T013_T085_Id, 1);
        $T013_Valor_Despesas_Acessorias          = gCorrigeNumeroInverte($T013_Valor_Despesas_Acessorias, 3);
        $T013_Valor_Mao_Obra                     = gCorrigeNumeroInverte($T013_Valor_Mao_Obra, 3);
        $T013_Aliquota_ISS                       = gCorrigeNumeroInverte($T013_Aliquota_ISS, 3);
        $T013_Valor_ISS                          = gCorrigeNumeroInverte($T013_Valor_ISS, 3);
        $T013_Valor_Seguro                       = gCorrigeNumeroInverte($T013_Valor_Seguro, 3);
        $T013_Valor_Dolar                        = gCorrigeNumeroInverte($T013_Valor_Dolar, 5);
        $T013_Valor_Credito_IPTU                 = gCorrigeNumeroInverte($T013_Valor_Credito_IPTU, 3);
        
        //-- VALIDA��ES DOS CAMPOS:
        /*
        if (strlen(trim($T013_C008_Id))==0)
        {
        //echo "<script>";
        //echo "parent.document.getElementById('T013_C008_Id').focus();";
        //echo "</script>";
        msg_alerta("Necessario o preenchimento do campo: Tipo Produtos");
        exit;
        }
        */
        
        
        if ($T013_Flag_Validacoes != 'S' and $T013_Flag_Validacoes != 'N') {
            return 'Selecione a opção Validações';
        }
        
        if ($T013_Flag_Calcular != 'S' and $T013_Flag_Calcular != 'N') {
            return 'Selecione a opção Realizar Calculos';
        }

        if ($T013A_Flag_Destaca_Frete_SPED != 'S') {
            $T013A_Flag_Destaca_Frete_SPED = 'N';
        }
        
        if ($T013_D006_Id <= 0) {
            //return "Necessario o preenchimento do campo: Natureza da Operação";
        }
        if (strlen(trim($T013_Tipo)) == 0) {
            return "Necessario o preenchimento do campo: Tipo Documento";
        }
        if (strlen(trim($T013_D024_Id)) == 0) {
            return "Necessario o preenchimento do campo: CNPJ/CPF";
        }
        if (strlen(trim($T013_Numero_Nota_Fiscal)) == 0) {
            return "Necessario o preenchimento do campo: Numero Nota Fiscal";
        }
        if (strlen(trim($T013_Data_Emissao)) == 0) {
            return "Necessario o preenchimento do campo: Data Emissao";
        }
        if (strlen(trim($T013_Data_Entrada)) == 0) {
            return "Necessario o preenchimento do campo: Data Entrada";
        }
        If ($T013_Data_Emissao > $T013_Data_Entrada) {
            return "A data de Entrada não pode ser inferior a data de Emissão";
        }
        If ($T013_Data_Entrada > gCorrigeDataInverte(date('d/m/Y'))) {
            return "A data de Entrada não pode ser superior a data de Hoje";
        }
        if ($T013_Valor_Total == 0 and $T013_Flag_Validacoes == 'S') {
            return "Campo: Valor Total Nota Fiscal deve ser diferente de zero";
        }
        if (strlen(trim($T013_Casas_Decimais_Valor_ICMS)) == 0) {
            return "Necessario o preenchimento do campo: Casas Decimais Valor ICMS";
        }
        if (strlen(trim($T013_Flag_Calcular_ICMS_IPI)) == 0) {
            return "Necessario o preenchimento do campo: Calcular ICMS sobre IPI";
        }
        if (strlen(trim($T013_Flag_Arredondar_Valor_IPI)) == 0) {
            return "Necessario o preenchimento do campo: Arredondar Valor IPI";
        }
        if (strlen(trim($T013_Flag_Frete)) == 0) {
            return "Necessario o preenchimento do campo: Frete por Conta";
        }
        if (strlen(trim($T013_Flag_Calcular_ICMS_Frete)) == 0) {
            return "Necessario o preenchimento do campo: Calcular ICMS sobre Frete";
        }
        if (strlen(trim($T013_Flag_Calcular_IPI_Frete)) == 0) {
            return "Necessario o preenchimento do campo: Calcular IPI sobre Frete";
        }
        // if ($T013_Numero_Dias==0)
        // {
        // return "Campo: Dias entre as parcelas deve ser diferente de zero";
        // }
        if (strlen(trim($T013_Flag_Calcular_ICMS_Despesas_Acessorias)) == 0) {
            return "Necessario o preenchimento do campo: Calcular ICMS Despesas Acessorias";
        }
        if (strlen(trim($T013_Flag_Calcular_IPI_Despesas_Acessorias)) == 0) {
            return "Necessario o preenchimento do campo: Calcular IPI sobre Despesas Acessorias";
        }
        if (strlen(trim($T013_Flag_Calcular_ICMS_Seguro)) == 0) {
            return "Necessario o preenchimento do campo: Calcular ICMS sobre Seguro";
        }
        if (strlen(trim($T013_Flag_Calcular_IPI_Seguro)) == 0) {
            return "Necessario o preenchimento do campo: Calcular IPI sobre Seguro";
        }
        if (($T013_Valor_Mao_Obra <= 0) and (!empty($T013_Mao_Obra_1))) {
            return 'Informe o valor dos serviços';
        }
        
        
        if (($T013_Valor_Mao_Obra > 0) and (empty($T013_Mao_Obra_1))) {
            return 'Informe os serviços';
        }
        
        if (($T013_Valor_Mao_Obra > 0) or (!empty($T013_Mao_Obra_1))) {
            if ($T013_Aliquota_ISS == 0) {
                //return 'Não foi informado o % do ISS';
            }
            
            if ($T013_Valor_ISS == 0) {
                //return 'Não foi informado o Valor do ISS';
            }
        }
        
        if (($T013_Aliquota_ISS > 0) or ($T013_Valor_ISS > 0)) {
            if ($T013_Valor_Mao_Obra <= 0) {
                return 'Informe o Valor dos Serviços';
            }
        }
        
        if (($T013_Aliquota_ISS > 0) or ($T013_Valor_ISS > 0)) {
            $Valor_ISS = round($T013_Valor_Mao_Obra * ($T013_Aliquota_ISS / 100), 2);
            
            if ($Valor_ISS != $T013_Valor_ISS) {
                //return "Dados divergentes nos campos ISS/pValor Serviço:  $mGravar_Atributos['T013_Valor_Mao_Obra']/p% ISS: $mGravar_Atributos[T013_Aliquota_ISS]/p/pFormula: VALOR SERVI�O * ISS = VALOR ISS/p/pVALOR ISS: ".number_format($Valor_ISS,2,',','.'));
                return "Dados divergentes nos campos ISS<br />Formula: VALOR SERVIÇO * ISS = VALOR ISS<br />VALOR ISS: " . number_format($Valor_ISS, 2, ',', '.');
            }
        }
        
        // retirado no novo
        /*
        if ($_SESSION['Modulo_NFe']!='S')
        {
        return 'Esta empresa está inativa';
        }
        */
        
        // bloco retirado, pois a validação de CPF/CNPJ já é feita no próprio form quando seleciona-se um valor no campo pesquisa
        
        
        /*
        if (strlen($T013_D024_Id)==18)
        {
        //-- TRANSFORMA O CNPJ EM ID
        $cSql_CNPJ=mysql_query("SELECT D024_Id, D024_Cnpj FROM D024 WHERE D024_Flag_Comercial='A' and (replace(replace(replace(D024_Cnpj,'.',''),'/',''),'-','')=replace(replace(replace('$T013_D024_Id','.',''),'/',''),'-','') or replace(replace(replace(D024_Cpf,'.',''),'/',''),'-','')=replace(replace(replace('$T013_D024_Id','.',''),'/',''),'-',''))");
        if (mysql_num_rows($cSql_CNPJ)<=0)
        {
        //-- TRANSFORMA O CNPJ EM ID
        $cSql_CNPJ=mysql_query("SELECT D024_Id, D024_Cnpj FROM D024 WHERE (replace(replace(replace(D024_Cnpj,'.',''),'/',''),'-','')=replace(replace(replace('$T013_D024_Id','.',''),'/',''),'-','') or replace(replace(replace(D024_Cpf,'.',''),'/',''),'-','')=replace(replace(replace('$T013_D024_Id','.',''),'/',''),'-',''))");
        if (mysql_num_rows($cSql_CNPJ)<=0)
        {
        return 'CNPJ não cadastrado';
        }
        else
        {
        if ($_SESSION['Modulo_NFe']!='S')
        {
        return 'Esta empresa está inativa';
        }
        }
        }
        $mCNPJ=mysql_fetch_array($cSql_CNPJ);
        }
        elseif (strlen($T013_D024_Id)==14)
        {
        //-- TRANSFORMA O CNPJ EM ID
        $cSql_CNPJ=mysql_query("SELECT D024_Id, D024_Cpf FROM D024 WHERE D024_Flag_Comercial='A' and (replace(replace(replace(D024_Cpf,'.',''),'/',''),'-','')=replace(replace(replace('$T013_D024_Id','.',''),'/',''),'-','') or replace(replace(replace(D024_Cpf,'.',''),'/',''),'-','')=replace(replace(replace('$T013_D024_Id','.',''),'/',''),'-',''))");
        if (mysql_num_rows($cSql_CNPJ)<=0)
        {
        return 'O CPF não foi reconhecido';
        }
        $mCNPJ=mysql_fetch_array($cSql_CNPJ);
        }
        else
        {
        return 'O CNPJ/CPF esta incorreto';
        }
        
        $T013_D024_Id = $mCNPJ['D024_Id'];
        */
        
        //-- VERIFICO SE A NOTA JA FOI LANCADA
        $cClausula = "SELECT T013_Id 
                        FROM T013 
                       WHERE T013_Numero_Nota_Fiscal = '{$T013_Numero_Nota_Fiscal}' 
                         AND T013_D024_Id = '{$T013_D024_Id}'";
        if ($operacao == '2') {
            $cClausula .= " AND T013_Id != '{$T013_Id}' ";
        }
        $cClausula .= " AND T013_C004_Id = '{$g['empresaAtual']}' ";
        $cClausula .= " AND T013_Tipo = '{$T013_Tipo}' ";
        $cClausula .= " AND T013_Flag_Cancelada != 'S' ";
        
        $cSql_T013 = mysql_query($cClausula);
        if (mysql_num_rows($cSql_T013) > 0) {
            
            $tipoDocumento = array(1 => 'NF-e', 
                                   2 => 'ACP',
                                   3 => 'NFC-e',
                                   4 => 'Cupom Fiscal',
                                   5 => 'NF',
                                   6 => 'NF/Conta de Energia Elétrica',
                                   7 => 'NFS de Comunicação',
                                   8 => 'NFS de Telecomunicação',
                                   9 => 'Outros',
                                  10 => 'NFS');

            return "Já existe um documento do tipo <b>{$tipoDocumento[$T013_Tipo]}</b> com o numero <b>{$T013_Numero_Nota_Fiscal}</b> para esse fornecedor";
        }
        
        
        //-- VERIFICA SE A NATUREZA DA OPERACAO � DENTRO OU FORA DO ESTADO
        $cSql_D006 = mysql_query("select D006_Flag_Estado,D006_Flag_Substituicao_Tributaria,D006_Flag_Venda_Compra_Outros from D006 where D006_Id='$T013_D006_Id'");
        $mD006     = mysql_fetch_array($cSql_D006);
        
        $cSql_C004 = mysql_query("select D018_UF from C004 left join D020 on D020_Id=C004_D020_Id left join D018 on D018_Id=C004_D018_Id where C004_Id='{$g['empresaAtual']}'");
        $mC004     = mysql_fetch_array($cSql_C004);
        
        $cSql_D024 = mysql_query("select D018_UF from D024 left join D020 on D020_Id=D024_D020_Id left join D018 on D018_Id=D024_D018_Id WHERE D024_Id='$T013_D024_Id'");
        $mD024     = mysql_fetch_array($cSql_D024);
        
        if (($mC004['D018_UF'] == $mD024['D018_UF']) and ($mD006['D006_Flag_Estado'] == 'F')) {
            return "CFOP é fora do estado, verifique !!!/pNossa UF " . $mC004['D018_UF'] . " UF Fornecedor: " . $mD024['D018_UF'];
        }
        
        if (($mC004['D018_UF'] != $mD024['D018_UF']) and ($mD006['D006_Flag_Estado'] == 'D')) {
            return "CFOP é dentro do estado, por favor verifique<br><br>Nossa UF " . $mC004['D018_UF'] . "<br>UF Fornecedor: " . $mD024['D018_UF'];
        }
        
        //-- VALIDO OS CALCULOS QUANDO FOR SUBSTITUICAO TRIBUTARIA
        if ($mD006['D006_Flag_Substituicao_Tributaria'] == 'S') {
            if ($T013_Valor_ICMS_Substituicao > 0 and empty($T013_Valor_Total_Base_ICMS_Substituicao)) {
                return "DIGITE O VALOR DA BASE DO ICMS DE SUBSTITUIÇÃO TRIBUT�RIA";
            }
            
            if (empty($T013_Valor_ICMS_Substituicao) and $T013_Valor_Total_Base_ICMS_Substituicao > 0) {
                return "DIGITE O VALOR DO ICMS DE SUBSTITUIÇÃO TRIBUTÁRIA";
            }
        }
        
        // VALIDACOES DO TOTAL DA NOTA
        if ($T013_Numero_Conhecimento == 'NAO TEM' and $T013_Valor_Frete > 0) {
            $valor_total_soma = $T013_Valor_Frete + $T013_Valor_Total_Produtos + $T013_Valor_Seguro + $T013_Valor_Despesas_Acessorias + $T013_Valor_Total_IPI + $T013_Valor_Mao_Obra + $T013_Valor_ICMS_Substituicao; //-$T013_Valor_Total_ICMS;
            $Msg_Erro         = "Valor Produtos + Valor Frete + Valor Seguro + Valor IPI + Servi�os + Despesas + ST - Desconto";
        } else {
            $valor_total_soma = $T013_Valor_Total_Produtos + $T013_Valor_Seguro + $T013_Valor_Despesas_Acessorias + $T013_Valor_Total_IPI + $T013_Valor_Mao_Obra + $T013_Valor_ICMS_Substituicao; //-$T013_Valor_Total_ICMS;
            $Msg_Erro         = "Valor Produtos + Valor Seguro + Valor IPI + Serviços + Despesas + ST - Desconto";
        }
        
        $valor_resultado = $valor_total_soma - $T013_Valor_Desconto;
        
        if ((number_format($valor_resultado, 0) != number_format($T013_Valor_Total, 0)) and $T013_Flag_Validacoes == 'S') {
            $valor_resultado  = number_format($valor_resultado, 2);
            $T013_Valor_Total = number_format($T013_Valor_Total, 2);
            return "Valores Divergentes<br />Total da Nota é diferente de: " . $Msg_Erro . "<br />Valor digitado: " . $T013_Valor_Total . "  Valor Apurado: " . $valor_resultado;
        }
        
        //-- VALIDACAO DA DATA DE EMISSAO
        $cSql_Dias_Dif   = mysql_query("select datediff(current_date(),'$Data_Emissao')");
        $mDias_Diferenca = mysql_fetch_array($cSql_Dias_Dif);
        if ($mDias_Diferenca[0] > 60) {
            return "Data de emissão da nota é inferior a 60 dias. <br />VERIFIQUE SE VOCE DIGITOU CORRETAMENTE A DATA DE EMISS�O DA NOTA FISCAL. <br />ISTO É APENAS UM AVISO, VOCE PODERÁ PROSSEGUIR COM O LANCAMENTO DESTA NOTA.";
        }
        if ($mDias_Diferenca[0] < 0) {
            return "Data de emissão é superior a data de hoje";
        }
        
        //-- VALIDACAO DO CONHECIMENTO E PREENCHIMENTO DO VALOR DO FRETE
        if ($T013_Flag_Frete == 2) {
            if (empty($T013_Numero_Conhecimento)) {
                return "Digite o numero do conhecimento";
            }
            if (empty($T013_D022_Id)) {
                return "Não foi selecionado uma transportadora";
                //      msg_alerta("Selecione uma transportadora");
                //      exit;
            }
            
            $cSql_D022 = mysql_query("select D022_Id,D022_Nome_Empresa from D022 where D022_Id='$T013_D022_Id'");
            $mD022     = mysql_fetch_array($cSql_D022);
            
            $cSql_D024 = mysql_query("select D024_Id,D024_Nome_Empresa from D024 WHERE and D024_Id='$T013_D024_Id'");
            $mD024     = mysql_fetch_array($cSql_D024);
            
            if ($T013_Numero_Conhecimento != 'NAO TEM') {
                $mNumero_Conhecimento = explode(',', $T013_Numero_Conhecimento);
                $T013_Valor_Frete     = 0;
            }
            
            for ($x = 0; $x < count($mNumero_Conhecimento); $x++) {
                $Clausula = "select T084_Id,T084_Flag_Cancelado ";
                $Clausula .= "from T084 ";
                $Clausula .= "where T084_Numero_Conhecimento='$mNumero_Conhecimento[$x]' ";
                if (count($mNumero_Conhecimento) == 1) {
                    $Clausula .= "and T084_D022_Id='$T013_D022_Id' ";
                }
                $Clausula .= "and T084_Flag_Cancelado!='S' ";
                $Clausula .= "and T084_D024_Id='$T013_D024_Id' ";
                $Clausula .= "and T084_C004_Id='$T013_C004_Id' ";
                $cSql_T084 = mysql_query($Clausula);
                $mT084     = mysql_fetch_array($cSql_T084);
                
                if ($mT084['T084_Flag_Cancelado'] == 'S') {
                    return "CONHECIMENTO $mNumero_Conhecimento[$x] ESTA CANCELADO";
                }
                
                if (mysql_num_rows($cSql_T084) == 0) {
                    $msg = "CONHECIMENTO NÃO ENCONTRADO!/p/p";
                    $msg .= "Verifique se existe o conhecimento: $mNumero_Conhecimento[$x]/p";
                    if ($x == 1) {
                        $msg .= "para a transportadora: " . $mD022['D022_Id'] . " - " . substr($mD022['D022_Nome_Empresa'], 0, 20) . " e<br />";
                    }
                    $msg .= "para fornecedor:  " . $mD024['D024_Id'] . " - " . substr($mD024['D024_Nome_Empresa'], 0, 20) . "<br />";
                    $msg .= "na tela de Conhecimentos./p";
                    return $msg;
                }
                
                $cSql_T085 = mysql_query("
                                select
                                    T085_Valor_Rateio,
                                    T085_Valor_Total_Nota,
                                    T085_Id,
                                    T085_Flag_Fechado
                                from T085
                                where
                                    T085_T084_Id='{$mT084['T084_Id']}'
                                    and T085_Numero_Nota_Fiscal='$T013_Numero_Nota_Fiscal'");
                
                $mT085 = mysql_fetch_array($cSql_T085);
                
                if (mysql_num_rows($cSql_T085) == 0) {
                    $msg = "NOTA FISCAL NÃO ENCONTRADA PARA ESTE CONHECIMENTO!/p/p";
                    $msg .= "Verifique se no conhecimento: " . $mNumero_Conhecimento[$x];
                    //$msg.="existe a nota fiscal: $mGravar_Atributos[T013_Numero_Nota_Fiscal]/p";
                    $msg .= "existe esta nota fiscal";
                    return $msg;
                }
                
                if ($mT085['T085_Valor_Total_Nota'] != $T013_Valor_Total) {
                    return "VALOR TOTAL DA NOTA NAO CONFERE COM OS LANCAMENTOS DO CONHECIMENTO " . $mNumero_Conhecimento[$x];
                }
                $T013_Valor_Frete += $mT085['T085_Valor_Rateio'];
            }
        }
        
        if ($operacao == '1') {
            $cClausula = <<<EOT
                insert into T013 (
                    T013_C008_Id, 
                    T013_D006_Id, 
                    T013_Tipo, 
                    T013_D024_Id, 
                    T013_C004_Id, 
                    T013_Numero_Nota_Fiscal_Devolucao, 
                    T013_Numero_Nota_Fiscal, 
                    T013_Data_Emissao, 
                    T013_Data_Entrada, 
                    T013_Codigo_CFOP, 
                    T013_Numero_Controle, 
                    T013_Parcelas, 
                    T013_Valor_Encargos, 
                    T013_Valor_Total_Produtos, 
                    T013_Valor_Desconto_Produtos, 
                    T013_Valor_Total, 
                    T013_Valor_Desconto, 
                    T013_Numero_Ordem_Compra, 
                    T013_Observacao_1, 
                    T013_Valor_Total_Base_ICMS, 
                    T013_Valor_Total_Base_ICMS_Diferido, 
                    T013_Valor_Total_ICMS, 
                    T013_Aliquota_ICMS, 
                    T013_Casas_Decimais_Valor_ICMS, 
                    T013_Valor_Total_Base_ICMS_Substituicao, 
                    T013_Valor_ICMS_Substituicao, 
                    T013_Valor_Total_Base_IPI, 
                    T013_Valor_Total_IPI, 
                    T013_Flag_Calcular_ICMS_IPI, 
                    T013_Flag_Arredondar_Valor_IPI, 
                    T013_Numero_Conhecimento, 
                    T013_Flag_Frete, 
                    T013_Flag_Validacoes, 
                    T013_Flag_Calcular, 
                    T013_Tipo_Transporte, 
                    T013_Valor_Frete, 
                    T013_Flag_Calcular_ICMS_Frete, 
                    T013_Flag_Calcular_IPI_Frete, 
                    T013_Nome_Transportadora, 
                    T013_D022_Id, 
                    T013_D031_Id, 
                    T013_Numero_Parcelas, 
                    T013_Numero_Dias, 
                    T013_Valor_Juros, 
                    T013_T085_Id, 
                    T013_Valor_Despesas_Acessorias, 
                    T013_Flag_Calcular_ICMS_Despesas_Acessorias, 
                    T013_Flag_Calcular_IPI_Despesas_Acessorias, 
                    T013_Mao_Obra_1, 
                    T013_Mao_Obra_2, 
                    T013_Valor_Mao_Obra, 
                    T013_Aliquota_ISS, 
                    T013_Valor_ISS, 
                    T013_Valor_Seguro, 
                    T013_Flag_Calcular_ICMS_Seguro, 
                    T013_Flag_Calcular_IPI_Seguro, 
                    T013_D014_Id, 
                    T013_Valor_Dolar,  
                    T013_Valor_Credito_IPTU,  
                    T013_Codigo_Servico,
                    T013_Flag_Rateio_Frete,
                    T013_Flag_Obrigatorio_OC  
                ) values ( 
                    '$T013_C008_Id', 
                    '$T013_D006_Id', 
                    '$T013_Tipo', 
                    '$T013_D024_Id', 
                    '$T013_C004_Id', 
                    '$T013_Numero_Nota_Fiscal_Devolucao', 
                    '$T013_Numero_Nota_Fiscal', 
                    '$T013_Data_Emissao', 
                    '$T013_Data_Entrada', 
                    '$T013_Codigo_CFOP', 
                    '$T013_Numero_Controle', 
                    '$T013_Parcelas', 
                    '$T013_Valor_Encargos', 
                    '$T013_Valor_Total_Produtos', 
                    '$T013_Valor_Desconto_Produtos', 
                    '$T013_Valor_Total', 
                    '$T013_Valor_Desconto', 
                    '$T013_Numero_Ordem_Compra', 
                    '$T013_Observacao_1', 
                    '$T013_Valor_Total_Base_ICMS', 
                    '$T013_Valor_Total_Base_ICMS_Diferido', 
                    '$T013_Valor_Total_ICMS', 
                    '$T013_Aliquota_ICMS', 
                    '$T013_Casas_Decimais_Valor_ICMS', 
                    '$T013_Valor_Total_Base_ICMS_Substituicao', 
                    '$T013_Valor_ICMS_Substituicao', 
                    '$T013_Valor_Total_Base_IPI', 
                    '$T013_Valor_Total_IPI', 
                    '$T013_Flag_Calcular_ICMS_IPI', 
                    '$T013_Flag_Arredondar_Valor_IPI', 
                    '$T013_Numero_Conhecimento', 
                    '$T013_Flag_Frete', 
                    '$T013_Flag_Validacoes', 
                    '$T013_Flag_Calcular', 
                    '$T013_Tipo_Transporte', 
                    '$T013_Valor_Frete', 
                    '$T013_Flag_Calcular_ICMS_Frete', 
                    '$T013_Flag_Calcular_IPI_Frete', 
                    '$T013_Nome_Transportadora', 
                    '$T013_D022_Id', 
                    '$T013_D031_Id', 
                    '$T013_Numero_Parcelas', 
                    '$T013_Numero_Dias', 
                    '$T013_Valor_Juros', 
                    '$T013_T085_Id', 
                    '$T013_Valor_Despesas_Acessorias', 
                    '$T013_Flag_Calcular_ICMS_Despesas_Acessorias', 
                    '$T013_Flag_Calcular_IPI_Despesas_Acessorias', 
                    '$T013_Mao_Obra_1', 
                    '$T013_Mao_Obra_2', 
                    '$T013_Valor_Mao_Obra', 
                    '$T013_Aliquota_ISS', 
                    '$T013_Valor_ISS', 
                    '$T013_Valor_Seguro', 
                    '$T013_Flag_Calcular_ICMS_Seguro', 
                    '$T013_Flag_Calcular_IPI_Seguro', 
                    '$T013_D014_Id', 
                    '$T013_Valor_Dolar',  
                    '$T013_Valor_Credito_IPTU',  
                    '$T013_Codigo_Servico',
                    '{$g['C031']['formaRateioFreteNotaRecebida']}',
                    '$T013_Flag_Obrigatorio_OC'
                )
EOT;
            $cSql  = mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "Ocorreu um erro ao tentar incluir o registro/p/pErro:" . $cErro . "/p/pClausula:" . $cClausula;
            }
            $T013_Id = $g['mysqlLastId'];

            // Insere registro tabela estendida
            mysqli_query("INSERT INTO T013A(T013A_T013_Id, T013A_Flag_Destaca_Frete_SPED) VALUES ('{$T013_Id}', '{$T013A_Flag_Destaca_Frete_SPED}')");
            
        } elseif ($operacao == '2') {
            $cClausula = <<<EOT
                update
                    T013
                set 
                    T013_C008_Id='$T013_C008_Id', 
                    T013_D006_Id='$T013_D006_Id', 
                    T013_Tipo='$T013_Tipo', 
                    T013_D024_Id='$T013_D024_Id', 
                    T013_Numero_Nota_Fiscal_Devolucao='$T013_Numero_Nota_Fiscal_Devolucao', 
                    T013_Numero_Nota_Fiscal='$T013_Numero_Nota_Fiscal', 
                    T013_Data_Emissao='$T013_Data_Emissao', 
                    T013_Data_Entrada='$T013_Data_Entrada', 
                    T013_Codigo_CFOP='$T013_Codigo_CFOP', 
                    T013_Numero_Controle='$T013_Numero_Controle', 
                    T013_Parcelas='$T013_Parcelas', 
                    T013_Valor_Encargos='$T013_Valor_Encargos', 
                    T013_Valor_Total_Produtos='$T013_Valor_Total_Produtos', 
                    T013_Valor_Desconto_Produtos='$T013_Valor_Desconto_Produtos', 
                    T013_Valor_Total='$T013_Valor_Total', 
                    T013_Valor_Desconto='$T013_Valor_Desconto', 
                    T013_Numero_Ordem_Compra='$T013_Numero_Ordem_Compra', 
                    T013_Observacao_1='$T013_Observacao_1', 
                    T013_Valor_Total_Base_ICMS='$T013_Valor_Total_Base_ICMS', 
                    T013_Valor_Total_Base_ICMS_Diferido='$T013_Valor_Total_Base_ICMS_Diferido', 
                    T013_Valor_Total_ICMS='$T013_Valor_Total_ICMS', 
                    T013_Aliquota_ICMS='$T013_Aliquota_ICMS', 
                    T013_Casas_Decimais_Valor_ICMS='$T013_Casas_Decimais_Valor_ICMS', 
                    T013_Valor_Total_Base_ICMS_Substituicao='$T013_Valor_Total_Base_ICMS_Substituicao', 
                    T013_Valor_ICMS_Substituicao='$T013_Valor_ICMS_Substituicao', 
                    T013_Valor_Total_Base_IPI='$T013_Valor_Total_Base_IPI', 
                    T013_Valor_Total_IPI='$T013_Valor_Total_IPI', 
                    T013_Flag_Calcular_ICMS_IPI='$T013_Flag_Calcular_ICMS_IPI', 
                    T013_Flag_Arredondar_Valor_IPI='$T013_Flag_Arredondar_Valor_IPI', 
                    T013_Numero_Conhecimento='$T013_Numero_Conhecimento', 
                    T013_Flag_Frete='$T013_Flag_Frete', 
                    T013_Flag_Validacoes='$T013_Flag_Validacoes', 
                    T013_Flag_Calcular='$T013_Flag_Calcular', 
                    T013_Tipo_Transporte='$T013_Tipo_Transporte', 
                    T013_Valor_Frete='$T013_Valor_Frete', 
                    T013_Flag_Calcular_ICMS_Frete='$T013_Flag_Calcular_ICMS_Frete', 
                    T013_Flag_Calcular_IPI_Frete='$T013_Flag_Calcular_IPI_Frete', 
                    T013_Nome_Transportadora='$T013_Nome_Transportadora', 
                    T013_D022_Id='$T013_D022_Id', 
                    T013_D031_Id='$T013_D031_Id', 
                    T013_Numero_Parcelas='$T013_Numero_Parcelas', 
                    T013_Numero_Dias='$T013_Numero_Dias', 
                    T013_Valor_Juros='$T013_Valor_Juros', 
                    T013_T085_Id='$T013_T085_Id', 
                    T013_Valor_Despesas_Acessorias='$T013_Valor_Despesas_Acessorias', 
                    T013_Flag_Calcular_ICMS_Despesas_Acessorias='$T013_Flag_Calcular_ICMS_Despesas_Acessorias', 
                    T013_Flag_Calcular_IPI_Despesas_Acessorias='$T013_Flag_Calcular_IPI_Despesas_Acessorias', 
                    T013_Mao_Obra_1='$T013_Mao_Obra_1', 
                    T013_Mao_Obra_2='$T013_Mao_Obra_2', 
                    T013_Valor_Mao_Obra='$T013_Valor_Mao_Obra', 
                    T013_Aliquota_ISS='$T013_Aliquota_ISS', 
                    T013_Valor_ISS='$T013_Valor_ISS', 
                    T013_Valor_Seguro='$T013_Valor_Seguro', 
                    T013_Flag_Calcular_ICMS_Seguro='$T013_Flag_Calcular_ICMS_Seguro', 
                    T013_Flag_Calcular_IPI_Seguro='$T013_Flag_Calcular_IPI_Seguro', 
                    T013_D014_Id='$T013_D014_Id', 
                    T013_Valor_Dolar='$T013_Valor_Dolar'  
                where
                    T013_Id='$T013_Id' 
EOT;
            $cSql  = mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "Ocorreu um erro ao tentar alterar o registro/p/pErro:" . $cErro . "/p/pClausula:" . $cClausula;
            }
        }

        mysqli_query("UPDATE T013A
                         SET T013A_Flag_Destaca_Frete_SPED = '{$T013A_Flag_Destaca_Frete_SPED}'
                       WHERE T013A_T013_Id = '{$T013_Id}'");
        
        $ultimoT013_Id = $g['mysqlLastId'];
        
        if ($T013_Numero_Conhecimento != 'NAO TEM' and $T013_Flag_Frete == 2) {
            $mNumero_Conhecimento = explode(',', $T013_Numero_Conhecimento);
            for ($x = 0; $x < count($mNumero_Conhecimento); $x++) {
                $Clausula = "select T084_Id ";
                $Clausula .= "from T084 ";
                $Clausula .= "where T084_Numero_Conhecimento='$mNumero_Conhecimento[$x]' ";
                if ($x == 0) {
                    $Clausula .= "and T084_D022_Id='$T013_D022_Id' ";
                }
                $Clausula .= "and T084_Flag_Cancelado!='S' ";
                $Clausula .= "and T084_D024_Id='$T013_D024_Id' ";
                $Clausula .= "and T084_C004_Id='$T013_C004_Id' ";
                
                $cSql_T084 = mysql_query($Clausula);
                $mT084     = mysql_fetch_array($cSql_T084);
                
                $cClausula = "update T085 set T085_T013_Id='$T013_Id' where T085_T084_Id='{$mT084['T084_Id']}' and T085_Numero_Nota_Fiscal='$T013_Numero_Nota_Fiscal'";
                $cSql_T085 = mysql_query($cClausula);
            }
        }
        
        if ($GerarNFOC == true) {
            $extra  = base64_decode(urldecode($extra));
            $extra  = gInsertExtraWhere($extra, '(T086_Quantidade > 0)');
            $sql    = <<<EOT
                SELECT 
                    T224_Id,
                    T225_Id,
                    D009_Id,
                    T086_Quantidade,
                    T086_Id,
                    T225_Valor_Preco_Unitario 
                FROM
                    T225
                {$extra}
EOT;
            $result = mysql_query($sql);
            
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
            }


            while ($linha = mysql_fetch_array($result)) {
               
                $this->incluirProdutoNota($linha['D009_Id'], $ultimoT013_Id, $linha['T086_Quantidade'], $linha['T225_Valor_Preco_Unitario'], $linha['T225_Id']);
                
                $sqlT086 = "UPDATE T086 SET T086_T013_Id='{$ultimoT013_Id}' WHERE T086_Id='{$linha['T086_Id']}'";
                mysql_query($sqlT086);
                
                mysql_query("call T225_Gravar_Totalizacao({$linha['T224_Id']})");
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
                }
            }
            
            $this->EST004AtualizarCabecalhoNota(null, $ultimoT013_Id);   

            mysql_query("delete from T086 where T086_T013_Id='{$ultimoT013_Id}'");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
            }
        }
        
        return true;
    }
    
    /**
     * atualizarPedidosVenda
     *
     * @return true/string
     */
    public function atualizarPedidosVenda()
    {
        
        global $g;
        
        if ($g['c020']['C020_Expedicao_Direcionar_Compra'] != 'S') {
            return "Opcao desabilitada (C020_Expedicao_Direcionar_Compra)";
        }
        
        /*
        $T006=mysql_query("select T006_Id,
        T006_Quantidade,
        D009_Id,
        (D009_Quantidade_Estoque-D009_Quantidade_Pedido_Separar(D009_Id,1)) D009_Quantidade_Estoque_Liquido,
        T006_T005_Id
        from T006
        left join D009 on D009_Id=T006_D009_Id
        left join T008 on T008_T006_Id=T006_Id
        where (T006_Flag_Tipo_Produto='CP' or T006_Flag_Tipo_Produto='MD')
        and T008_Id is null
        and T006_Flag_Status!='3'
        order by T006_D009_Id,T006_Id");
        */
        
        $T006 = mysql_query("
                    select T006_Id,
                        T006_Id,
                        T006_T006_Id_Dividir,
                        T006_Quantidade,
                        T006_Quantidade_Separacao,
                        T006_Quantidade_Estoque,
                        T006_Quantidade_Comprar,
                        T006_Codigo_Produto,
                        D082_Marca,
                        D009_Id,
                        D009_Quantidade_Estoque,
                        D009_Quantidade_Estoque_Loja,
                        T006_T005_Id,
                        T005_Id,
                        T005_Flag_Status,
                        D049_Flag_Tipo,
                        T006_Flag_Divergencia,
                        T006_Flag_Tipo_Produto,
                        T006_D049_Id
                    from T006
                    left join D009 on D009_Id=T006_D009_Id
                    left join D049 on D049_Id=D009_D049_Id
                    left join D082 on D082_Id=D049_D082_Id
                    left join T005 on T005_Id=T006_T005_Id
                    left join T008 on T008_T006_Id=T006_Id
                    left join T007 on T007_Id=T008_T007_Id and T007_Flag_Cancelada!='S'
                    where
                        T007_Id is null
                        and T005_Flag_Status!=5
                        and T005_Flag_Status!='8'
                        and T006_Flag_Status!='3'
                        and T006_Quantidade>0
                    order by 
                        T006_D009_Id,if(T006_T006_Id_Dividir>0,T006_T006_Id_Dividir,T006_Id),T006_Id");
        
        $D009_Id = 0;
        while ($mT006 = mysql_fetch_array($T006)) {
            if ($D009_Id != $mT006['D009_Id']) {
                $Estoque = $mT006['D009_Quantidade_Estoque']-$mT006['D009_Quantidade_Estoque_Loja'];
                $D009_Id = $mT006['D009_Id'];
            }
            //echo ' T005_Id='.$mT006['T005_Id'].' T005_Flag_Status: '.$mT006['T005_Flag_Status'].' T006_Id: '.$mT006['T006_Id'].' T006_Id_Dividir'.$mT006['T006_T006_Id_Dividir'].' '.$mT006['T006_Codigo_Produto'].' '.$mT006['D082_Marca']."($Estoque>={$mT006['T006_Quantidade']})".'<br>';
            if (($Estoque > $mT006['T006_Quantidade']) or (number_format($Estoque, 2) == number_format($mT006['T006_Quantidade'], 2))) {
                //echo 'Possui estoque'.'<br>';
                if (($mT006['T005_Flag_Status'] == 22) or ($mT006['T005_Flag_Status'] == 23) or ($mT006['T005_Flag_Status'] == 1)) {
                    //echo 'Status 22, 23 ou 1'.'<br>';
                    if (($mT006['T006_Flag_Tipo_Produto'] != 'AA') or (number_format($mT006['T006_Quantidade_Estoque'], 2) != number_format($Estoque, 2))) {
                        //echo 'Mudando T006 Status Separacao'.'<br>';
                        mysql_query("update T006 set T006_Flag_Tipo_Produto='AA', T006_Quantidade_Estoque='$Estoque' where T006_Id='{$mT006['T006_Id']}'");
                        
                        $mT006['T006_Flag_Tipo_Produto'] = 'AA';
                    }
                }
                if (($mT006['T006_Quantidade_Separacao'] < $mT006['T006_Quantidade']) and (($mT006['T005_Flag_Status'] == 22) or ($mT006['T005_Flag_Status'] == 23)) and ($mT006['T006_Flag_Divergencia'] != 'S')) {
                    //echo 'Mudando T005 Status Separacao'.'<br>';
                    mysql_query("update T005 set T005_Flag_Status=1, T005_Flag_Almoxarifado='S' where T005_Id='{$mT006['T006_T005_Id']}' and T005_Flag_Status!='1'");
                    //Insere registros no histórico do pedido (T178)
                    mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) values ({$mT006['T006_T005_Id']},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Aguardando Impressão','')");
                }
                
                $Estoque -= $mT006['T006_Quantidade'];
            } else {
                //echo 'N�o possui Estoque'.'<br>';
                if ($mT006['T006_Quantidade_Separacao'] < $mT006['T006_Quantidade']) {
                    //echo 'Item N�o est� separado'.'<br>';
                    if ($Estoque <= 0) {
                        //echo 'Estoque zero'.'<br>';
                        if (($mT006['T006_Flag_Tipo_Produto'] != $mT006['D049_Flag_Tipo']) or (number_format($mT006['T006_Quantidade_Estoque'], 2) != number_format($Estoque, 2)) or (number_format($mT006['T006_Quantidade_Comprar'], 2) != number_format($mT006['T006_Quantidade'], 2))) {
                            //echo 'Mudando T006 status para Compra/Servico'.'<br>';
                            mysql_query("update T006 set T006_Quantidade_Estoque='$Estoque', T006_Flag_Tipo_Produto='{$mT006['D049_Flag_Tipo']}', T006_Quantidade_Comprar=T006_Quantidade where T006_Id='{$mT006['T006_Id']}'");
                        }
                    } else {
                        //echo 'Possui quantidade parcial'.'<br>';
                        if (($mT006['T006_Flag_Tipo_Produto'] != $mT006['D049_Flag_Tipo']) or (number_format($mT006['T006_Quantidade_Estoque'], 2) != number_format($Estoque, 2))) {
                            //echo 'Mudando T006 status para Compra/Servico com quantidade parcial'.'<br>';
                            mysql_query("update T006 set T006_Quantidade_Estoque='$Estoque', T006_Flag_Tipo_Produto='{$mT006['D049_Flag_Tipo']}', T006_Quantidade_Comprar=T006_Quantidade-if(T006_Quantidade_Estoque<0,0,T006_Quantidade_Estoque) where T006_Id='{$mT006['T006_Id']}'");
                        }
                    }
                } else {
                    if (number_format($mT006['T006_Quantidade_Estoque'], 2) != number_format($Estoque, 2)) {
                        mysql_query("update T006 set T006_Quantidade_Estoque='$Estoque', T006_Quantidade_Comprar=T006_Quantidade-if(T006_Quantidade_Estoque<0,0,T006_Quantidade_Estoque) where T006_Id='{$mT006['T006_Id']}'");
                    }
                }
                $Estoque -= $mT006['T006_Quantidade'];
            }
            if (($mT006['T006_Quantidade_Separacao'] >= $mT006['T006_Quantidade']) and ($mT006['T006_Flag_Tipo_Produto'] != 'AA')) {
                mysql_query("update T006 set T006_Flag_Tipo_Produto='AA' where T006_Id='{$mT006['T006_Id']}'");
            }
        }
        
        // Atualiza servicos
        $T006 = mysql_query("
                    select T006_Id,
                        T006_Quantidade,
                        T006_Quantidade_Separacao,
                        T006_Quantidade_Estoque,
                        T006_Quantidade_Estoque_Padrao,
                        T006_Quantidade_Comprar,
                        T006_Flag_Tipo_Produto,
                        D009_Id,
                        D009_Quantidade_Estoque_Liquido,
                        T006_T005_Id,
                        T005_Flag_Status,
                        D049_Flag_Tipo,
                        D049_Id,
                        T006_D049_Id,
                        T006_Flag_Divergencia,
                        T129_Id,
                        D049_D001_Id
                    from T006
                    left join D009 on D009_Id=T006_D009_Id
                    left join D049 on D049_Id=D009_D049_Id
                    left join T005 on T005_Id=T006_T005_Id
                    left join T129 on T129_T006_Id=T006_Id
                    left join T008 as T0081 on T0081.T008_T006_Id=T006_Id
                    left join T008 as T0082 on T0082.T008_T129_Id=T129_Id
                    left join T007 as T0071 on T0071.T007_Id=T0081.T008_T007_Id and T0071.T007_Flag_Cancelada!='S'
                    left join T007 as T0072 on T0072.T007_Id=T0082.T008_T007_Id and T0072.T007_Flag_Cancelada!='S'
                    where
                        T0071.T007_Id is null
                        and T0072.T007_Id is null
                        and T006_Flag_Status!='3'
                        and T006_Quantidade>0
                        and D049_Flag_Tipo='MD'
                        and T006_Flag_Tipo_Produto!='AA'
                    order
                        by T006_D009_Id,T006_Id");
        
        $D009_Id = 0;
        while ($mT006 = mysql_fetch_array($T006)) {
            if ($D009_Id != $mT006['D009_Id']) {
                $D049    = mysql_query("
                            select D009_Quantidade_Estoque_Liquido,
                                D009_Id,
                                D049_Id
                            from D049
                            left join D009 on D009_D049_Id=D049_Id and D009_C004_Id='{$g['empresaAtual']}'
                            left join D082 on D082_Id=D049_D082_Id
                            where 
                                D049_D001_Id='{$mT006['D049_D001_Id']}'
                                and D049_Flag_Tipo='CP'");
                $mD049   = mysql_fetch_array($D049);
                $Estoque = $mD049['D009_Quantidade_Estoque_Liquido'];
                $D009_Id = $mT006['D009_Id'];
            }
            if ($Estoque >= $mT006['T006_Quantidade']) {
                if (($mT006['T006_Flag_Tipo_Produto'] != $mT006['D049_Flag_Tipo']) or ($mT006['T006_D049_Id'] != $mD049['D049_Id']) or (number_format($mT006['T006_Quantidade_Estoque_Padrao'], 2) != number_format($Estoque, 2))) {
                    mysql_query("update T006 set T006_Quantidade_Estoque_Padrao='$Estoque',
		                                             T006_Flag_Tipo_Produto='{$mT006['D049_Flag_Tipo']}',
		                                             T006_D049_Id='{$mD049['D049_Id']}',
		                                             T006_Quantidade_Comprar=T006_Quantidade-if(T006_Quantidade_Estoque<0,0,T006_Quantidade_Estoque)
		                                       where T006_Id='{$mT006['T006_Id']}'");
                }
                $Estoque -= $mT006['T006_Quantidade'];
            } else {
                if ($mT006['T129_Id'] <= 0) {
                    if (($mT006['T006_Flag_Tipo_Produto'] != 'CP') or ($mT006['T006_D049_Id'] != $mD049['D049_Id']) or (number_format($mT006['T006_Quantidade_Estoque_Padrao'], 2) != number_format($Estoque, 2))) {
                        mysql_query("update T006 set T006_Quantidade_Estoque_Padrao='$Estoque',
		                                            T006_Flag_Tipo_Produto='CP',
		                                            T006_Flag_Comprar='',
		                                            T006_D049_Id='{$mD049['D049_Id']}',
		                                            T006_Quantidade_Comprar=T006_Quantidade-if(T006_Quantidade_Estoque<0,0,T006_Quantidade_Estoque),
		                                            T006_D009_Id_Comprar='{$mD049['D009_Id']}'
		                                 where T006_Id='{$mT006['T006_Id']}'");
                    }
                    $Estoque -= $mT006['T006_Quantidade'];
                }
            }
        }
        
        mysql_query("update T005
		                set T005_Flag_Status=22, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1)
		              where (T005_Flag_Status=1 or T005_Flag_Status=2 or T005_Flag_Status=23)
		                and (select count(*) from T006 where T006_T005_Id=T005_Id and T006_Flag_Tipo_Produto='AA' and T006_Quantidade>0 and T006_Quantidade>ifnull(T006_Quantidade_Separacao,0) and T006_Quantidade<=T006_Quantidade_Estoque and T006_Flag_Divergencia!='S')<=0
		                and (select count(*) from T006 where T006_T005_Id=T005_Id and T006_Flag_Tipo_Produto='AA' and T006_Quantidade>0 and T006_Quantidade_Separacao>0)>0");
        
        mysql_query("update T005
		                set T005_Flag_Status=23, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1)
		              where (T005_Flag_Status=1 or T005_Flag_Status=2 or T005_Flag_Status=22)
		                and (select count(*) from T006 where T006_T005_Id=T005_Id and T006_Flag_Tipo_Produto='AA' and T006_Quantidade>0 and T006_Quantidade>ifnull(T006_Quantidade_Separacao,0) and T006_Quantidade<=T006_Quantidade_Estoque and T006_Flag_Divergencia!='S')<=0
		                and (select count(*) from T006 where T006_T005_Id=T005_Id and T006_Flag_Tipo_Produto='AA' and T006_Quantidade>0 and T006_Quantidade_Separacao>0)<=0");
        
        mysql_query("update T005
		                set T005_Flag_Status=8,
                            T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1),
		                    T005_Flag_Almoxarifado='N'
		              where (T005_Flag_Status=1 or T005_Flag_Status=2 or T005_Flag_Status=22 or T005_Flag_Status=23)
		                and (select count(*) from T006 where T006_T005_Id=T005_Id and T006_Quantidade>0 and T006_Flag_Status!=3)<=0");
        
        mysql_query("update T006 set T006_Flag_Divergencia='N' where T006_Flag_Divergencia='S' and T006_Flag_Tipo_Produto!='AA'");
        
        return true;
    }
    
    /**
     * finalizarNotaEntrada
     *
     * @param int $T013_Id
     * @return true/string
     */
    public function xfinalizarNotaEntrada($T013_Id)
    {
        global $g;
        //-- Conex�o com o banco de dados
        require "old/lib/gravar_entidade.php3";
        
        //-- INCLUSAO DA CLASSE DE PROCESSAMENTOS
        require "old/lib/processamentos.class.php3";
        
        // Classe produtos 
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        
        if (empty($T013_Id)) {
            return "Erro: T013_Id não encontrado.";
        }
        $cSql_T013 = mysql_query("select * 
                                from T013 
                           left join D006 on D006_Id=T013_D006_Id
                           left join D024 on D024_Id=T013_D024_Id                   
                               where T013_Id='$T013_Id'");
        $mT013     = mysql_fetch_array($cSql_T013);
        
        //Trocado valor da variável $g['empresaAtual']. Pois há empresas que vão administrar todas as notas de uma vez
        $g['empresaAtual'] = $mT013['T013_C004_Id'];
        
        //-- INSTANCIA CLASSE DE PROCESSAMENTOS
        $Pendencias = NEW \Processamentos($g['empresaAtual']);
        
        //echo "<script src='../func/funcoes_genericas.js'></script>";
        
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Atualizacao dos calculos dos itens
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $Msg       = "";
        $cSql_T014 = mysql_query("select T014_Id
		                        from T014
		                       where T014_T013_Id='$T013_Id' and T014_Quantidade=0");
        if (mysql_num_rows($cSql_T014) > 0) {
            return "Existem ítens sem quantidade. Verfifique!";
        }
        
        $cSql_T014 = mysql_query("select T014_Id
		                        from T014
		                   left join D009 on D009_Id=T014_D009_Id
		                       where T014_T013_Id='$T013_Id' and D009_C004_Id!='{$g['empresaAtual']}'");
        
        if (mysql_num_rows($cSql_T014) > 0) {
            return 'EXISTEM ITENS COM D009 INCORRETO<br />* * * * POR FAVOR, AVISE O SUPORTE TÉCNICO * * * *';
        }
        
        $D048_Id = 1;
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Atualizacao dos calculos dos itens
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $Msg       = "";
        $cSql_T014 = mysql_query("select T014_Id,
		                             T014_Aliquota_IPI,
		                             T014_Classificacao_Fiscal,
		                             T014_Codigo_Produto
		                        from T014
		                       where T014_T013_Id='$T013_Id'
		                         and T014_C004_Id='{$g['empresaAtual']}'
		                         and T014_Quantidade>0");
        
        $nTotal_Itens_Nota = mysql_num_rows($cSql_T014);
        
        while ($mT014 = mysql_fetch_array($cSql_T014)) {
            //    sql_query("call T014_Gravar_Totalizacao($mT014[T014_Id])");
            if (($mT014['T014_Aliquota_IPI'] > 0) and (empty($mT014['T014_Classificacao_Fiscal']))) {
                //$Msg.="/pItem: $mT014[T014_Codigo_Produto]";
            }
            //if ( ( $mT014[T014_Aliquota_IPI]==0 ) and ( !empty($mT014[T014_Classificacao_Fiscal] ) ) ) {
            //$Msg.="/pItem: $mT014[T014_Codigo_Produto]";
            //}
        }
        if (!empty($Msg)) {
            return "Os itens abaixo não possuem Classifi��o/IPI/p" . $Msg;
        }
        
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Verifica��o dos Valores do cabecalho
        ////////////////////////////////////////////////////////////////////////////////////////////////
        if ($mT013['T013_Valor_Dolar'] <= 0) {
            //   msg_alerta("Dolar n�o encontrado!");
            //   exit;
        }
        if ($mT013['T013_C004_Id'] != $g['empresaAtual']) {
            return "A Empresa que esta selecionada não é a mesma da nota fiscal. <br />Selecione a empresa correta: {$mT013['T013_C004_Id']}";
            exit;
        }
        
        If ($mT013['D006_Id'] <= 0) {
            return "Informe a CFOP no cabe�alho da NF";
        }
        $cSql_T014 = mysql_query("select T014_D006_Id
		                        from T014
		                       where T014_T013_Id='$T013_Id'
		                         and ifnull(T014_D006_Id,0)<=0");
        if (mysql_num_rows($cSql_T014) > 0) {
            return "Existe itens sem CFOP, verifique.";
        }
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Verifica se o item esta cadastrado e se a natureza gera estoque
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $cSql_T014 = mysql_query("select T014_Id
		                        from T014
		                       left join D006 on D006_Id=T014_D006_Id        
		                           where T014_T013_Id='$T013_Id'
		                             and T014_C004_Id='{$g['empresaAtual']}'
		                             and T014_D009_Id=0
		                             and T014_Quantidade>0
		                             and D006_Flag_Estoque!='N'");
        if (mysql_num_rows($cSql_T014) > 0) {
            return "Devido a natureza da operação estar definida para <br />movimentar o estoque e existirem itens sem cadastro, <br />você não podera finalizar esta nota. <br />Cadastre os itens e tente novamente!";
        }
        
        if ($mT013['T013_Flag_Travar_Nota'] == 'S') {
            
            return "Nota Fiscal já foi finalizada, não pode ser finalizada novamente!";
        }
        
        mysql_query("call T013_Gravar_Totalizacao('$T013_Id')");
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE FORAM SELECIONADOS ITENS DE SUBS.TRIBUTARIA
        ////////////////////////////////////////////////////////////////////////////////////////////////
        if ($mT013['D006_Flag_Substituicao_Tributaria'] == 'S') {
            $cSql_T014 = mysql_query("select T014_Id
									from T014
								left join T013 on T013_Id=T014_T013_Id
								left join D006 on D006_Id=T013_D006_Id
									where T014_T013_Id='$T013_Id'
										and T014_C004_Id='{$g['empresaAtual']}'
										and (SELECT D067_Simbolo
												FROM D009
											LEFT JOIN D049 ON D049_Id=D009_D049_Id 
											LEFT JOIN D001 ON D001_Id=D049_D001_Id 
											LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id 
											LEFT JOIN D068 ON D068_D005_Id=T025_D005_Id
											LEFT JOIN D067 ON D067_Id=D068_D067_Id AND D068_C004_Id=D009_C004_Id
												WHERE D009_Id=69316
												AND D068_C004_Id=D009_C004_Id
												LIMIT 1)!='ST'");
            if (mysql_num_rows($cSql_T014) > 0) {
                //		msg_alerta("AVISO! A CFOP DESTA NOTA � SUBS.TRIB. E EXISTEM ITENS QUE NAO S�O SUBS.TRIB./pVERIFIQUE!!!");
                
            }
            
        } else if ($mT013['D006_Flag_Devolucao'] != 'S') {
            if ($mT013['D006_Flag_Substituicao_Tributaria'] == 'S') {
                $cSql_T014 = mysql_query("select T014_Id
										from T014
									left join D009 on D009_Id=T014_D009_Id
									left join D049 on D049_Id=D009_D049_Id                            
										where T014_T013_Id='$T013_Id'
											and T014_C004_Id='{$g['empresaAtual']}'
											and D049_Flag_Substituicao_Tributaria!='S'");
                if (mysql_num_rows($cSql_T014) > 0) {
                    return "AVISO! A CFOP DESTA NOTA É SUBS.TRIB. E EXISTEM ITENS QUE NAO S�O SUBS.TRIB. <br />VERIFIQUE!!!";
                }
            } else {
                
                $cSql_T014 = mysql_query("select T014_Id
										from T014
								left join D009 on D009_Id=T014_D009_Id
								left join D049 on D049_Id=D009_D049_Id                            
									where T014_T013_Id='$T013_Id'
										and T014_C004_Id='{$g['empresaAtual']}'
										and D049_Flag_Substituicao_Tributaria='S'");
                if (mysql_num_rows($cSql_T014) > 0) {
                    return "AVISO! EXISTEM ITENS QUE SÃO SUBS.TRIB. E A CFOP DESTA NOTA NÃO É SUBS.TRIB. <br />VERIFIQUE!!!";
                    
                }
            }
        }
        
        $Total_Geral = 0;
        $Total_Geral += $mT013['T013_Valor_Total_Produtos'];
        
        if (($mT013['T013_Numero_Conhecimento'] == 'NAO TEM') and ($mT013['T013_Valor_Frete'] > 0)) {
            $Total_Geral += $mT013['T013_Valor_Frete'];
            $Msg_Erro = "Valor Produtos + Valor Frete + Valor Seguro + Valor IPI + Despesas + Mao Obra + ST - Desconto";
        } else {
            $Msg_Erro = "Valor Produtos + Valor Seguro + Valor IPI + Despesas + Mao Obra + ST - Desconto";
        }
        
        $Total_Geral += $mT013['T013_Valor_Seguro'];
        //$Total_Geral+=$mT013['T013_Valor_Embalagem'];
        $Total_Geral += $mT013['T013_Valor_Total_IPI'];
        $Total_Geral += $mT013['T013_Valor_Mao_Obra'];
        $Total_Geral += $mT013['T013_Valor_Despesas_Acessorias'];
        $Total_Geral += $mT013['T013_Valor_ICMS_Substituicao'];
        $Total_Geral -= $mT013['T013_Valor_Desconto'];
        //$Total_Geral-=$mT013['T013_Valor_Total_ICMS'];
        
        if ((number_format($mT013['T013_Valor_Total'], 0) != number_format($Total_Geral, 0)) and $mT013['D006_Flag_Atualiza_Custo'] == 'S') {
            return "Valores Divergentes <br />Total da Nota é diferente de: <br />Valor Produtos + Valor Frete + Valor Seguro + Valor IPI + Despesas - Desconto <br />Valor digitado: " . number_format($mT013['T013_Valor_Total'], 2, ',', '.') . " Valor Apurado: " . number_format($Total_Geral, 2, ',', '.');
        }
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Verifica o custo
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $T014Custo      = mysql_query("SELECT * FROM T014 
								LEFT JOIN D006 ON D006_Id=T014_D006_Id 
								LEFT JOIN D009 ON D009_Id=T014_D009_Id
								LEFT JOIN D049 ON D049_Id=D009_D049_Id
								LEFT JOIN D001 ON D001_Id=D049_D001_Id
								WHERE T014_T013_Id='{$T013_Id}'");
        $validacaoCusto = "";
        while ($mT014Custo = mysql_fetch_array($T014Custo)) {
            
            if ($mT014Custo['D006_Flag_Atualiza_Custo'] == "S") {
                
                // SQL que calcula o custo
                // T014_Valor_Custo_Final_Unitario = ((T014_Valor_Total_Custo + IF(xFlag_Substituicao_Tributaria='S',T014_Valor_ICMS_Substituicao,0) + T014_Valor_Frete + IF(xFlag_Credita_IPI='N',T014_Valor_IPI,0) + T014_Valor_Seguro + T014_Valor_Despesas_Acessorias - IF(xFlag_Deb_Crd_ICMS='S', T014_Valor_ICMS, 0)) - T014_Valor_Total_Desconto)/T014_Quantidade,
                // T014_Valor_Total_Custo_Final    = T014_Valor_Custo_Final_Unitario * T014_Quantidade
                $mT014Custo['T014_Valor_ICMS_Substituicao'] = $mT014Custo['D006_Flag_Substituicao_Tributaria'] == 'S' ? $mT014Custo['T014_Valor_ICMS_Substituicao'] : 0.00;
                $mT014Custo['T014_Valor_IPI']               = $mT014Custo['D006_Flag_Credita_IPI'] == 'N' ? $mT014Custo['T014_Valor_IPI'] : 0.00;
                $mT014Custo['T014_Valor_ICMS']              = $mT014Custo['D006_Flag_Deb_Crd_ICMS'] == 'S' ? $mT014Custo['T014_Valor_ICMS'] : 0.00;
                
                $Custo_Nota      = $mT014Custo['T014_Valor_Custo_Final_Unitario'];                
                // A função ROUND arredonda o número para 6, como acontece ao gravar o campo T014_Valor_Custo_Final_Unitario.
                $custo_Calculado = round((( ($mT014Custo['T014_Valor_Custo_Unitario'] * $mT014Custo['T014_Quantidade']) + $mT014Custo['T014_Valor_ICMS_Substituicao'] + $mT014Custo['T014_Valor_Frete'] + $mT014Custo['T014_Valor_IPI'] + $mT014Custo['T014_Valor_Seguro'] +  $mT014Custo['T014_Valor_Despesas_Acessorias']) - $mT014Custo['T014_Valor_Total_Desconto']) / $mT014Custo['T014_Quantidade'],6);

                
                log("Total: {$custo_Calculado} = (( ({$mT014Custo['T014_Valor_Custo_Unitario']} * {$mT014Custo['T014_Quantidade']}) + {$mT014Custo['T014_Valor_ICMS_Substituicao']} + {$mT014Custo['T014_Valor_Frete']} + {$mT014Custo['T014_Valor_IPI']} + {$mT014Custo['T014_Valor_Seguro']} +  {$mT014Custo['T014_Valor_Despesas_Acessorias']}) - {$mT014Custo['T014_Valor_Total_Desconto']}) / {$mT014Custo['T014_Quantidade']}");
                
                $Custo_Nota      = number_format($Custo_Nota, 2);
                $custo_Calculado = number_format($custo_Calculado, 2);
                
                if ($Custo_Nota <= 0) {
                    $validacaoCusto .= "O produto código {$mT014Custo['D001_Codigo_Produto']} está com o valor do custo igual á zero.<br />";
                    continue;
                }
                
                if ($Custo_Nota != $custo_Calculado) {
                    $validacaoCusto .= "O produto código {$mT014Custo['D001_Codigo_Produto']} está com o valor do custo errado. Valor calculado: {$custo_Calculado} Valor apurado: {$Custo_Nota}<br />";
                }
            }
        }
        log("Custo nota: " . $Custo_Nota . " Custo calc: " . $custo_Calculado);
        
        if ($validacaoCusto != '') {
            $validacaoCusto .= "Utilize o botão 'Calcular' para refazer o cálculo do custo.";
            return $validacaoCusto;
        }
        
        // Fim Veriicação
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Compara
        ////////////////////////////////////////////////////////////////////////////////////////////////
        
        if ($nTotal_Itens_Nota == 0) {
            $mT014['T014_Valor_Base_ICMS']      = 0;
            $mT014['T014_Valor_ICMS']           = 0;
            $mT014['T014_Valor_Base_IPI']       = 0;
            $mT014['T014_Valor_IPI']            = 0;
            $mT014['T014_Valor_Total_Custo']    = 0;
            $mT014['T014_Valor_Total_Desconto'] = 0;
        } else {
            $cSql_T014 = mysql_query("select sum(T014_Valor_Base_ICMS) as T014_Valor_Base_ICMS,
		                                round(sum(T014_Valor_ICMS),2) as T014_Valor_ICMS,
		                                sum(T014_Valor_Base_IPI) as T014_Valor_Base_IPI,
		                                sum(T014_Valor_IPI) as T014_Valor_IPI,
		                                sum(T014_Valor_Total_Custo) as T014_Valor_Total_Custo,
		                                sum(T014_Valor_Total_Desconto) as T014_Valor_Total_Desconto
		                           from T014
		                          where T014_T013_Id='$T013_Id'
		                            and T014_Quantidade>0");
            $mT014     = mysql_fetch_array($cSql_T014);
        }
        
        if (($mT013['T013_Valor_Total_Produtos'] == 0) && ($mT013['T013_Valor_Mao_Obra'] == 0) && $mT013['T013_Flag_Validacoes'] == 'S') {
            return "Não foi informado o valor total dos produtos e/ou valor da mão-de-obra";
        }
        
        if (($mT013['T013_Valor_Total_Produtos'] == 0) && ($mT013['T013_Valor_Mao_Obra'] > 0) && $mT013['T013_Flag_Validacoes'] == 'S') {
            if (empty($mT013['T013_Mao_Obra_1'])) {
                return "Não foi informado a descrição da mão-de-obra";
            }
        }
        
        $mT014['T014_Valor_Total_Custo']    = number_format($mT014['T014_Valor_Total_Custo'] - $mT014['T014_Valor_Total_Desconto'], 2, '.', '');
        $mT014['T014_Valor_Total_Desconto'] = round($mT014['T014_Valor_Total_Desconto'], 2);
        
        $Msg_Erro = "";
        
        $Total_Desconto = $mT013['T013_Valor_Desconto_Produtos'] + $mT013['T013_Valor_Desconto'];
        
        if ($mT014['T014_Valor_Total_Desconto'] != $Total_Desconto) {
            $Msg_Erro .= "VALOR DOS DESCONTOS/p";
            $Msg_Erro .= "Valor Digitado: $Total_Desconto/p";
            $Msg_Erro .= "Valor Apurado: {$mT014['T014_Valor_Total_Desconto']} <br />";
        }
        
        if ($mT013['T013_Valor_Total_Base_ICMS_Diferido'] > 0) {
            $mT013['T013_Valor_Total_Base_ICMS'] = $mT013['T013_Valor_Total_Base_ICMS'] - $mT013['T013_Valor_Total_Base_ICMS_Diferido'];
        }
        
        if ($mT014['T014_Valor_ICMS'] != $mT013['T013_Valor_Total_ICMS'] && $mT013['T013_Flag_Validacoes'] == 'S') {
            if (($mT014['T014_Valor_ICMS'] >= ($mT013['T013_Valor_Total_ICMS'] + 0.05)) or ($mT014['T014_Valor_ICMS'] <= ($mT013['T013_Valor_Total_ICMS'] - 0.05))) {
                return "VALOR DO ICMS <br />Valor Digitado: {$mT013['T013_Valor_Total_ICMS']} <br />Valor Apurado: {$mT014['T014_Valor_ICMS']}<br />SUGESTÃO: Ajuste as casas decimais do valor do ICMS para corrigir.<br />" . ($mT013['T013_Valor_Total_ICMS'] - 0.05);
            } else {
                //		       $Msg_Erro.= 'Diferença desconsiderada no valor de até 5 centavos para o valor do ICMS';
            }
        }
        
        /*
        if ($mT014[T014_Valor_ICMS]!=$mT013[T013_Valor_Total_ICMS]) {
        $Msg_Erro.="VALOR DO ICMS/pValor Digitado: $mT013[T013_Valor_Total_ICMS]/pValor Apurado: $mT014[T014_Valor_ICMS]/pSUGEST�O: Ajuste as casas decimais do valor do ICMS para corrigir./p/p";
        }
        */
        
        /*
        if ($mT014[T014_Valor_Base_IPI]!=$mT013[T013_Valor_Total_Base_IPI]) {
        $Msg_Erro.="VALOR BASE IPI/pValor Digitado: $mT013[T013_Valor_Total_Base_IPI]/pValor Apurado: $mT014[T014_Valor_Base_IPI]/p/p";
        }
        */
        
        if ($mT014['T014_Valor_IPI'] != $mT013['T013_Valor_Total_IPI']) {
            
            $Tolerancia  = 0.10;
            $Valor_Maior = round($mT013['T013_Valor_Total_IPI'] + $Tolerancia, 2);
            $Valor_Menor = round($mT013['T013_Valor_Total_IPI'] - $Tolerancia, 2);
            
            if ($mT013['T013_Flag_Validacoes'] == 'S') {
                if (($mT014['T014_Valor_IPI'] <= $Valor_Maior) && ($mT014['T014_Valor_IPI'] >= $Valor_Menor)) {
                    //return "Diferença desconsiderada no valor de até $Tolerancia centavo para o valor do IPI";
                } else {
                    $Msg_Erro .= "VALOR DO IPI<br />";
                    $Msg_Erro .= "Valor Digitado: {$mT013['T013_Valor_Total_IPI']}<br />";
                    $Msg_Erro .= "Valor Apurado: {$mT014['T014_Valor_IPI']}<br />";
                }
            }
        }
        /*
        if ($mT014[T014_Valor_IPI]!=$mT013[T013_Valor_Total_IPI]) {
        $Msg_Erro.="VALOR DO IPI/pValor Digitado: $mT013[T013_Valor_Total_IPI]/pValor Apurado: $mT014[T014_Valor_IPI]/p/p";
        }
        */
        $Valor_Produto_Desconto = 0;
        if ($mT013['T013_Valor_Total_Produtos'] > 0) {
            $Valor_Produto_Desconto = $mT013['T013_Valor_Total_Produtos'] - $Total_Desconto;
        }
        
        if (number_format($mT014['T014_Valor_Total_Custo'], 2) != number_format($Valor_Produto_Desconto, 2)) {
            $Msg_Erro .= "VALOR DOS PRODUTOS<br />Valor Digitado: " . $Valor_Produto_Desconto . " <br />Valor Apurado: " . $mT014['T014_Valor_Total_Custo'];
        }
        
        /*
        $cSql_T013=sql_query("select (sum(T014_Valor_Total_Custo_Final)+if(C004_Flag_Empresa_Simples!='1',T013_Valor_Total_ICMS,0)) as Custo_Final_Produtos,
        T013_Valor_Total+T013_Valor_Frete+T013_Valor_Mao_Obra+T013_Valor_ICMS_Substituicao) as Total_Nota
        from T014
        left join T013 on T013_Id=T014_T013_Id
        left join D006 on D006_Id=T013_D006_Id
        left join C004 on C004_Id=T013_C004_Id
        where T013_Id=$T013_Id
        group by T013_Id");
        $mDiferenca=sql_fetch_array($cSql_T013);
        
        //$mDiferenca[Custo_Final_Produtos] -= $mT013[T013_Valor_Desconto];
        
        if ($mDiferenca[Custo_Final_Produtos]>$mDiferenca[Total_Nota]) {
        $nDiferenca=round($mDiferenca[Custo_Final_Produtos]-$mDiferenca[Total_Nota],2);
        } else {
        $nDiferenca=round($mDiferenca[Total_Nota]-$mDiferenca[Custo_Final_Produtos],2);
        }
        
        if ($nDiferenca>0.10) {
        $Msg_Aviso.="CUSTO FINAL/p";
        $Msg_Aviso.="Custo final produtos: $mDiferenca[Custo_Final_Produtos]/p";
        $Msg_Aviso.="Custo total nota: $mDiferenca[Total_Nota] /p";
        $Msg_Aviso.="Diferen�a encontrada: $nDiferenca/p/p";
        }
        
        if ($mT013[D006_Flag_Substituicao_Tributaria] == 'S' && 
        $mT013[D024_Flag_ICMS_Substituicao_Tributaria_Substituto]=='N' &&
        $mT013[D006_Flag_Devolucao]!='S')
        {
        $cSql=sql_query("select D053_Aliquota_ICMS_1,
        D053_Aliquota_ICMS_2                            
        from T013
        left join D024 on D024_Id=T013_D024_Id
        left join D020 on D020_Id=D024_D020_Id
        left join D018 on D018_Id=D020_D018_Id
        left join D053 on D053_D018_Id=D018_Id and D053_C004_Id=T013_C004_Id
        where T013_Id='$T013_Id'");
        $mSql_ICMS=mysql_fetch_array($cSql);
        
        $cSql=sql_query("select round(sum(T014_Valor_Total_Custo+T014_Valor_IPI)*.35,2) as Valor_IVA,
        sum(T014_Valor_Total_Custo+T014_Valor_IPI) as Valor_Produtos,
        sum(T014_Valor_Total_Custo) as T014_Valor_Total_Custo
        from T014
        where T014_T013_Id='$T013_Id'");
        $mSql=mysql_fetch_array($cSql);
        $Valor_Base_Substituicao=$mSql[Valor_Produtos]+$mSql[Valor_IVA];
        $ICMS_Interno=$Valor_Base_Substituicao*($mSql_ICMS[D053_Aliquota_ICMS_1]/100);
        $ICMS_Externo=$mSql[T014_Valor_Total_Custo]*($mSql_ICMS[D053_Aliquota_ICMS_2]/100);    
        $ICMS_Substituicao=round($ICMS_Interno-$ICMS_Externo,2);
        
        $nDiferenca=abs(round($mT013[T013_Valor_Total_Base_ICMS_Substituicao]-$Valor_Base_Substituicao,2));
        if ($nDiferenca>0) {
        $Msg_Erro.="BASE ICMS SUBSTITUI��O/p";
        $Msg_Erro.="Valor Digitado: $mT013[T013_Valor_Total_Base_ICMS_Substituicao]/p";
        $Msg_Erro.="Valor Apurado: $Valor_Base_Substituicao /p";
        $Msg_Erro.="Diferen�a encontrada: $nDiferenca/p/p";
        
        }
        
        $nDiferenca=abs(round($mT013[T013_Valor_ICMS_Substituicao]-$ICMS_Substituicao,2));
        if ($nDiferenca>0) {
        $Msg_Erro.="ICMS SUBSTITUI��O/p";
        $Msg_Erro.="Valor Digitado: $mT013[T013_Valor_ICMS_Substituicao]/p";
        $Msg_Erro.="Valor Apurado: $ICMS_Substituicao /p";
        $Msg_Erro.="Diferen�a encontrada: $nDiferenca/p/p";    	
        }
        }
        
        if ($mT013[D006_Flag_Substituicao_Tributaria] == 'S' 
        && $mT013[D024_Flag_ICMS_Substituicao_Tributaria_Substituto]=='S') 
        {
        if ($mT013[T013_Valor_ICMS_Substituicao]>0 || $mT013[T013_Valor_Total_Base_ICMS_Substituicao]>0) 
        {
        if ($mT013[D006_Flag_Venda_Compra_Outros] != 'T' 
        && $mT013[T013_C004_Id] != 2) 
        {
        $Msg_Erro.="FORNECEDOR ESTA DEFINIDO COM ICMS SUBS.TRIB.SUBSTITUTO, OS VALORES DE SUBSTITUI��O N�O DEVEM SER PREENCHIDOS/p";
        }
        }
        }
        
        
        */
        
        if (!empty($Msg_Erro) && $mT013['T013_Flag_Validacoes'] == 'S') {
            return "Foram encontradas divergencias:<br />" . $Msg_Erro;
        }
        
        if (!empty($Msg_Aviso)) {
            return "AVISO!<br />" . $Msg_Aviso;
        }
        
        $Mensagem = "";
        
        if ($mT014['T014_Valor_Base_ICMS'] != $mT013['T013_Valor_Total_Base_ICMS']) {
            $Mensagem .= "VALOR BASE ICMS/pValor Digitado: {$mT013['T013_Valor_Total_Base_ICMS']}<br />Valor Apurado: {$mT014['T014_Valor_Base_ICMS']}<br />";
        }
        if (!empty($Mensagem) && $mT013['T013_Flag_Validacoes'] == 'S') {
            return $Mensagem;
        }
        $Mensagem = "";
        
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VALIDACAO DO CONHECIMENTO
        ////////////////////////////////////////////////////////////////////////////////////////////////
        if (($mT013['T013_Numero_Conhecimento'] != 'NAO TEM') && ($mT013['T013_Numero_Conhecimento'] > 0)) {
            
            $FechouConhecimento = 'S';
            
            $cSql_T085_1 = mysql_query("select * from T085 
		    						left join T084 on T084_Id=T085_T084_Id 
		    					where 
			    					T085_Numero_Nota_FIscal='{$mT013['T013_Numero_Nota_Fiscal']}'");
            
            if (mysql_num_rows($cSql_T085_1) == 0) {
                return "Não foram inseridas as notas relacionadas no conhecimento.";
            }
            
            $T084  = mysql_query("SELECT * FROM T084
		    					WHERE 
		    					T084_Numero_Conhecimento='{$mT013['T013_Numero_Conhecimento']}';
		    					");
            $mT084 = mysql_fetch_array($T084);
            
            
            if ($mT084['T084_D022_Id'] != $mT013['T013_D022_Id']) {
                return "A transportadora informada na nota é diferente da informada no conhecimento.";
            }
            
            while ($mT085 = mysql_fetch_array($cSql_T085_1)) {
                
                $cSql_T085 = mysql_query("select T085_Id 
		                                        from T085 
		                                       where T085_T084_Id='{$mT085['T085_T084_Id']}' 
		                                         and T085_D024_Id!='{$mT013['T013_D024_Id']}' 
		                                         and T085_Numero_Nota_FIscal!='{$mT013['T013_Numero_Nota_Fiscal']}'
		                                         and T085_Flag_Fechado='N'");
                
                if (mysql_num_rows($cSql_T085) > 0) {
                    
                    mysql_query("update T085 set T085_Flag_Fechado='S' 
                                                where T085_T084_Id='{$mT085['T085_T084_Id']}'
		                                         and T085_D024_Id='{$mT013['T013_D024_Id']}' 
		                                         and T085_Numero_Nota_FIscal='{$mT013['T013_Numero_Nota_Fiscal']}'");
                    $FechouConhecimento = 'N';
                    
                } else {
                    mysql_query("update T085 set T085_Flag_Fechado='S' 
		                                        where T085_T084_Id='{$mT085['T085_T084_Id']}'
		                                          and T085_D024_Id='{$mT013['T013_D024_Id']}' 
		                                          and T085_Numero_Nota_FIscal='{$mT013['T013_Numero_Nota_Fiscal']}'");
                    mysql_query("update T084 set T084_Flag_Fechado='S' where T084_Id='{$mT085['T085_T084_Id']}'");
                }
            }
            if ($FechouConhecimento == 'N') {
                //-- CHAMA O FORMULARIO DE NOTAS PARA DIGITA��O DA PROXIMA NOTA FISCAL	
                return "OK, Para finalizar esta nota, termine de lancar as outras notas que compoem este conhecimento";
                
                //echo "parent.location.replace('../monta/monta_formularios.php3?Entidade=T013&operacao=1&Codigo_Consulta=001&Id_Entidade_Principal=$mT013[T013_C008_Id]&Entidade_Principal=C008');";
            } else {
                //-- VERIFICA SE O CONHECIMENTO FOI LANCANDO CORRETAMENTE JUNTO COM A NOTA FISCAL
                if (empty($mT013['T013_D022_Id'])) {
                    return "VOCE DEVE SELECIONAR UMA TRANSPORTADORA";
                }
                if (empty($mT013['T013_D024_Id'])) {
                    return "VOCE DEVE SELECIONAR UM CLIENTE";
                }
                if (empty($mT013['T013_Numero_Conhecimento'])) {
                    return "Digite o numero do conhecimento";
                }
                
                $cSql_D022 = mysql_query("select D022_Id,D022_Nome_Empresa from D022 where D022_Id='{$mT013['T013_D022_Id']}'");
                $mD022     = mysql_fetch_array($cSql_D022);
                
                $cSql_D024 = mysql_query("select D024_Id,D024_Nome_Empresa from D024 WHERE D024_Id='{$mT013['T013_D024_Id']}'");
                $mD024     = mysql_fetch_array($cSql_D024);
                
                $mNumero_Conhecimento = explode(',', $mT013['T013_Numero_Conhecimento']);
                $nT013_Valor_Frete    = 0;
                
                for ($x = 0; $x < count($mNumero_Conhecimento); $x++) {
                    
                    $Clausula = "select T084_Id,T084_Flag_Cancelado,T084_Valor_ICMS ";
                    $Clausula .= "from T084 ";
                    $Clausula .= "where T084_Numero_Conhecimento='$mNumero_Conhecimento[$x]' ";
                    if (count($mNumero_Conhecimento) == 1) {
                        $Clausula .= "and T084_D022_Id='{$mT013['T013_D022_Id']}' ";
                    }
                    $Clausula .= "and T084_Flag_Cancelado!='S' ";
                    $Clausula .= "and T084_D024_Id='{$mT013['T013_D024_Id']}' ";
                    $Clausula .= "and T084_C004_Id='{$mT013['T013_C004_Id']}' ";
                    
                    $cSql_T084 = mysql_query($Clausula);
                    $mT084     = mysql_fetch_array($cSql_T084);
                    if ($mT084['T084_Flag_Cancelado'] == 'S') {
                        return "CONHECIMENTO " . $mNumero_Conhecimento[$x] . "ESTA CANCELADO";
                    }
                    if (mysql_num_rows($cSql_T084) == 0) {
                        $msg = "CONHECIMENTO NÃO ENCONTRADO!<br />";
                        $msg .= "Verifique se existe o conhecimento: " . $mNumero_Conhecimento[$x] . "<br />";
                        if ($x == 1) {
                            $msg .= "para a transportadora: {$mD022['D022_Id']} - " . substr($mD022['D022_Nome_Empresa'], 0, 20) . " e<br >";
                        }
                        $msg .= "para fornecedor:  {$mD024['D024_Id']} - " . substr($mD024['D024_Nome_Empresa'], 0, 20) . "<br />";
                        $msg .= "na tela de Conhecimentos.<br />";
                        return $msg;
                    }
                    
                    $cSql_T085 = mysql_query("select T085_Valor_Rateio,
												   T085_Valor_Total_Nota,
												   T085_Id,
												   T085_Flag_Fechado
											from T085
											where T085_T084_Id='{$mT084['T084_Id']}'
											and T085_Numero_Nota_Fiscal='{$mT013['T013_Numero_Nota_Fiscal']}'");
                    $mT085     = mysql_fetch_array($cSql_T085);
                    if (mysql_num_rows($cSql_T085) == 0) {
                        $msg = "NOTA FISCAL NÃO ENCONTRADA PARA ESTE CONHECIMENTO!<br />";
                        $msg .= "Verifique se no conhecimento: " . $mNumero_Conhecimento[$x] . "<br />";
                        $msg .= "existe a nota fiscal: " . $mT013['T013_Numero_Nota_Fiscal'];
                        return $msg;
                    }
                    if ($mT085['T085_Valor_Total_Nota'] != $mT013['T013_Valor_Total']) {
                        return "VALOR TOTAL DA NOTA NAO CONFERE COM OS LANCAMENTOS DO CONHECIMENTO " . $mNumero_Conhecimento[$x];
                    }
                    $nT013_Valor_Frete += $mT085['T085_Valor_Rateio'];
                }
                
                $nDiferenca_Frete = abs(bcsub($mT013['T013_Valor_Frete'], $nT013_Valor_Frete, 2));
                
                if ($nDiferenca_Frete > 0) {
                    $nDiferenca_Frete1 = abs(bcsub($mT013['T013_Valor_Frete'], $nT013_Valor_Frete - $mT084[T084_Valor_ICMS], 2));
                    if ($nDiferenca_Frete1 > 0) {
                        return "VALOR DO FRETE NÃO CONFERE COM O CONHECIMENTO. FAVOR VERIFICAR! Diferenca: $nDiferenca_Frete";
                    }
                }
            }
        }
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VALIDACAO DO ULTIMO NUMERO DE CONTROLE
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $cSql_C004 = mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
        $mC004     = mysql_fetch_array($cSql_C004);
        
        $cSql  = mysql_query("select T013_Numero_Controle
		                  from T013
		                  where T013_C004_Id='{$g['empresaAtual']}'
		              order by T013_Numero_Controle desc limit 0,1");
        $mT013 = mysql_fetch_array($cSql);
        
        if ($mT013['T013_Numero_Controle'] != $mC004['C004_Ultimo_Numero_Controle']) {
            //return "Numero de controle esta incorreto!!!/pInforme o ultimo numero de controle!!! ";
            
            //echo "<script>parent.location.replace('T013_formulario_informe_controle.php3');</script>";
            
        }
        
        $mC004['C004_Ultimo_Numero_Controle'] = $mT013['T013_Numero_Controle'];
        
        mysql_query("LOCK TABLE C004");
        mysql_query("LOCK TABLE T013");
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $cSql_C004 = mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
        $mC004     = mysql_fetch_array($cSql_C004);
        
        $cSql_T013 = mysql_query("select * 
		                        from T013 
		                   left join D006 on D006_Id=T013_D006_Id
		                   left join D024 on D024_Id=T013_D024_Id                   
		                       where T013_Id='$T013_Id'");
        
        $mT013 = mysql_fetch_array($cSql_T013);
        
        $D006_Flag_Gera_Contas = $mT013['D006_Flag_Gera_Contas'];
        $D006_D014_Id          = $mT013['D006_D014_Id'];
        $D006_D073_Id          = $mT013['D006_D073_Id'];
        
        $T014 = mysql_query("select D006_Flag_Gera_Contas, D006_D014_Id, D006_D073_Id
		                        from T014 
		                   left join D006 on D006_Id=T014_D006_Id
		                       where T014_T013_Id='$T013_Id'
		                         and D006_Flag_Gera_Contas!='N'");
        
        $mT014 = mysql_fetch_array($T014);
        
        if (mysql_num_rows($T014) > 0) {
            $D006_Flag_Gera_Contas = $mT014['D006_Flag_Gera_Contas'];
            $D006_D014_Id          = $mT014['D006_D014_Id'];
            $D006_D073_Id          = $mT014['D006_D073_Id'];
        }

        if($D006_Flag_Gera_Contas != 'N') {
            if($D006_Flag_Gera_Contas == 'P'){
                if(empty($D006_D073_Id) || $D006_D073_Id <= 0){
                    return "A CFOP gera contas a pagar e está sem um centro de custo definido! Verifique o cadastro da CFOP.";
                }
            }
            //Verifica se a CFOP gera contas e se a conta está definida
            if($D006_Flag_Gera_Contas == 'P' || $D006_Flag_Gera_Contas == 'R') {
                if(empty($mT013['T013_D014_Id']) || $mT013['T013_D014_Id'] <= 0){
                    if(empty($D006_D014_Id) || $D006_D014_Id <= 0) {
                        $tipoConta = ($D006_Flag_Gera_Contas == 'P') ? 'pagar' : 'receber';
                        return "A CFOP gera contas a {$tipoConta} e está sem uma conta definida! Verifique o cadastro da CFOP.";
                    } else {
                        $sqlD032 = mysql_query("SELECT D014_D032_Id FROM D014 WHERE D014_Id = '{$D006_D014_Id}'");
                        $D032 = mysql_fetch_array($sqlD032);
                        $D014_D032_Id = $D032['D014_D032_Id'];
                    }
                } else {
                    $sqlD032 = mysql_query("SELECT D014_D032_Id FROM D014 WHERE D014_Id = '{$mT013['T013_D014_Id']}'");
                    $D032 = mysql_fetch_array($sqlD032);
                    $D014_D032_Id = $D032['D014_D032_Id'];
                    $D006_D014_Id = $mT013['T013_D014_Id'];
                }
            }

            ////////////////////////////////////////////////////////////////////////////////////////////////
            //-- CONFIRMACAO DOS VENCIMENTOS E VALORES PARA CRIAR AS PARCELAS 
            ////////////////////////////////////////////////////////////////////////////////////////////////
            
            /*
            if (empty($mT013['T013_Numero_Dias'])) {
            mysql_query("ROLLBACK");
            return "Não foi preenchido os numero de dias entre as parcelas,verifique!";
            }
            */
            
            
            if ($mT013['T013_Valor_Entrada'] > $mT013['T013_Valor_Total']) {
                mysql_query("ROLLBACK");
                return "Valor da entrada é maior que o valor total da nota fiscal";
            }
            
            if ($D006_Flag_Gera_Contas == 'P') {
                if (($mT013['T013_Valor_Entrada'] > 0) and (empty($mT013['T013_D031_Id_Entrada']))) {
                    mysql_query("ROLLBACK");
                    return "Selecione o portador referente ao valor de entrada";
                }
                
                /*
                 * as parcelas agora são feitas em local diferente
                 */
                /*
                if ((($mT013['T013_Valor_Total']-$mT013['T013_Valor_Entrada'])>0) && (empty($mT013['T013_D031_Id']))) {
                mysql_query("ROLLBACK");          
                return "Selecione o portador referente as parcelas";
                }
                */
            }
            
            // if (empty($Parte)) {
            // mysql_query("ROLLBACK");  
            // 				          	
            // //echo "<script>parent.location.replace('T014_formulario_vencimento.php3?T013_Id=$T013_Id');</script>";
            // 		        
            // exit; 
            // }
            
            // Foi acrescentada uma linha no formulario T014_formulario_vencimento
            // para testar se o numero da nota foi informado corretamente
            // Abaixo ser� testado este numero fornecido pelo numero gravado na tabela.
            
            // PS verificação já realizada na interface
            /*
            if($mT013['T013_Numero_Nota_Fiscal'] != strtoupper($Confirmacao_Nota_Fiscal))
            {
            mysql_query("ROLLBACK");
            return "Numero da Nota Fiscal não confere!";
            }
            */
            
            $T013_Valor_Total = $mT013['T013_Valor_Total'];
            /*
            if ( [T013_Valor_Entrada]>0) {
            $T013_Valor_Total=$T013_Valor_Total-$mT013[T013_Valor_Entrada];
            $Total_Duplicatas++;        
            }
            */
            if ($T013_Valor_Total > 0) {
                $Total_Duplicatas = $mT013['T013_Numero_Parcelas'];
            }
            
            
            ////////////////////////////////////////////////////////////////////////////////////////////////
            //-- VALIDACAO DO VALOR TOTAL DA NOTA COM O VALOR DA SOMA DOS TITULOS E DATAS VENCIMENTO
            ////////////////////////////////////////////////////////////////////////////////////////////////
            list($Dia_Anterior, $Mes_Anterior, $Ano_Anterior) = explode("/", hoje());
            $Data_Vencimento_Anterior = mktime(0, 0, 0, $Mes_Anterior, $Dia_Anterior, $Ano_Anterior);
            $Total_Geral += $mT013['T013_Valor_Juros'];
            
            /* 
             * este for foi retirado, pois a validação das parcelas foi alterada
             */
            /*
            for ($i=1; $i<=$Total_Duplicatas; $i++){
            $Valor_Cada_Titulo="Valor_Vencimento_$i";
            $$Valor_Cada_Titulo=formata_valor_gravar($$Valor_Cada_Titulo,3);
            if ($$Valor_Cada_Titulo==0) {
            mysql_query("ROLLBACK");                 
            return "Valor do Titulo n.$i � zero! <br />VERIFIQUE";
            }
            $Total_Geral_Pagamentos+=$$Valor_Cada_Titulo;
            
            eval('list($Dia_Atual,$Mes_Atual,$Ano_Atual)=explode("/",$Data_Vencimento_'.$i.');');
            $Data_Vencimento_Atual = mktime (0,0,0,$Mes_Atual,$Dia_Atual,$Ano_Atual);
            
            if ($Data_Vencimento_Anterior>$Data_Vencimento_Atual) {
            return "Vencimento atrasado: " . $Dia_Atual . "/" . $Mes_Atual . "/" . $Ano_Atual;
            //          mysql_query("ROLLBACK");
            //          exit;
            } 
            eval('list($Dia_Anterior,$Mes_Anterior,$Ano_Anterior)=explode("/",$Data_Vencimento_'.$i.');');
            $Data_Vencimento_Anterior = mktime (0,0,0,$Mes_Anterior,$Dia_Anterior,$Ano_Anterior);
            }
            
            if ("$Total_Geral_Pagamentos"!="$Total_Geral" && $mT013['T013_Flag_Validacoes']=='S') {
            mysql_query("ROLLBACK");              
            return "Valores Divergentes!<br />A soma total dos titulos é: ".number_format($Total_Geral_Pagamentos,2,',','.')."<br />O Valor Total da Nota é: ".number_format($Total_Geral,2,',','.')."<br />VERIFIQUE!!!";
            }
            */
            
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // GERAR CONTAS A RECEBER E A PAGAR
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            $mLetras[1]  = "A";
            $mLetras[2]  = "B";
            $mLetras[3]  = "C";
            $mLetras[4]  = "D";
            $mLetras[5]  = "E";
            $mLetras[6]  = "F";
            $mLetras[7]  = "G";
            $mLetras[8]  = "H";
            $mLetras[9]  = "I";
            $mLetras[10] = "J";
            $mLetras[11] = "K";
            $mLetras[12] = "L";
            $mLetras[13] = "M";
            $mLetras[14] = "N";
            $mLetras[15] = "O";
            $mLetras[16] = "P";
            $mLetras[17] = "Q";
            $mLetras[18] = "R";
            $mLetras[19] = "S";
            $mLetras[20] = "T";
            $mLetras[21] = "U";
            $mLetras[22] = "V";
            $mLetras[23] = "W";
            $mLetras[24] = "X";
            $mLetras[25] = "Y";
            $mLetras[26] = "Z";
            $mLetras[27] = "AA";
            $mLetras[28] = "AB";
            $mLetras[29] = "AC";
            $mLetras[30] = "AD";
            $mLetras[31] = "AE";
            $mLetras[32] = "AF";
            $mLetras[33] = "AG";
            $mLetras[34] = "AH";
            $mLetras[35] = "AI";
            $mLetras[36] = "AJ";
            $mLetras[37] = "AK";
            $mLetras[38] = "AL";
            $mLetras[39] = "AM";
            $mLetras[40] = "AN";
            $mLetras[41] = "AO";
            $mLetras[42] = "AP";
            $mLetras[43] = "AQ";
            $mLetras[44] = "AR";
            $mLetras[45] = "AS";
            $mLetras[46] = "AT";
            $mLetras[47] = "AU";
            $mLetras[48] = "AV";
            
            
            
            /*
             * retirado pois não possui nenhum uso (copiado do antigo)
             */
            
            /*
            if (empty($mT013['T013_D014_Id'])) {
            if ($g['empresaAtual']!=6) {
            if (substr($mT007['Proximo_Numero'],0,1)=='A') {
            $Conta=73;
            } else if ($mD006['D006_Flag_Venda_Compra_Outros']=='T') {
            $Conta=155;
            } else if ($mD006['D006_Flag_Estado']=='I') {
            $Conta=371;
            } else if ($mD006['D006_Flag_Venda_Maquina']=='S') {
            $Conta=213;
            } else {
            $Conta=370;
            }
            } else {
            if (substr($mT007['Proximo_Numero'],0,1)=='A') {
            $Conta=292;
            } else if ($mD006['D006_Flag_Estado']=='I') {
            $Conta=231;
            } else if ($mD006['D006_Flag_Venda_Maquina']=='S') {
            $Conta=205;
            } else {
            $Conta=225;
            }
            }
            }
            */
            
            $Valor_Total_Base_Comissao  = $mT013["T013_Valor_Total_Produtos"] + $mT013["T013_Valor_Mao_Obra"];
            $Valor_Base_Comissao_Titulo = $Valor_Total_Base_Comissao / $Total_Duplicatas;
            
            
            /*
             * Esta parte foi refeita, pois no sistema antigo havia outro form para definir o pagamento
             * agora as parcelas a gerar contas vem da tabela T154, e são cadastradas no form de notas recebidas (aba pagamento)
             */
            
            $cSql_T154    = mysql_query("SELECT * FROM T154 LEFT JOIN D031 ON D031_Id=T154_D031_Id WHERE T154_T013_Id='$T013_Id'");
            $multiEmpresa = $g['conf']->ini('faturamentoMultiEmpresa');
            if (mysql_num_rows($cSql_T154) <= 0 && $multiEmpresa == false) {
                return 'Não foram gerados os pagamentos para esta nota';
            }
            
            if ($D006_Flag_Gera_Contas == 'R') {
                
                mysql_query("START TRANSACTION");
                
                while ($mT154 = mysql_fetch_array($cSql_T154)) {
                    
                    //unset($mGravar);
                    $i              = $mT154['T154_Numero_Parcela'];
                    $horaLancamento = strftime('%T');
                    $dataLancamento = troca_data_amd(hoje());
                    mysql_query("insert into T002 (T002_D024_Id,
		                                                      T002_T013_Id,
		                                                      T002_C004_Id,
		                                                      T002_D062_Id,
                                                              T002_D021_Id,
                                                              T002_D014_Id,
		                                                      T002_D032_Id,
		                                                      T002_Nome_Cliente,
		                                                      T002_Numero_Duplicata,
		                                                      T002_Numero_Documento,
		                                                      T002_Data_Emissao,
		                                                      T002_Data_Vencimento,
		                                                      T002_Data_Recebimento,
		                                                      T002_Valor_Duplicata,
		                                                      T002_Valor_Juros,
		                                                      T002_Valor_Desconto,
		                                                      T002_Valor_Total,
		                                                      T002_Valor_Saldo,
		                                                      T002_Valor_Base_Comissao,
		                                                      T002_Percentual_Comissao,
		                                                      T002_Data_Lancamento,
		                                                      T002_Hora_Lancamento,
		                                                      T002_Numero_Parcela
		                                                      )
		                                                      values 
		                                                      ('{$mT013['T013_D024_Id']}',
		                                                      '{$T013_Id}',
		                                                      '{$mT013['T013_C004_Id']}',
		                                                      '{$mT013['D024_D062_Id']}',
		                                                      '{$mT013['T013_D021_Id']}',
                                                              '{$D014_D032_Id}',
                                                              '{$D006_D014_Id}',
		                                                      '',
		                                                      '{$mT013['T013_Numero_Nota_Fiscal']}" . "$mLetras[$i]',
		                                                      '{$mT013['T013_Numero_Nota_Fiscal']}',
		                                                      '{$mT013['T013_Data_Emissao']}',
		                                                      '{$mT154['T154_Data_Vencimento']}',
		                                                      '0000-00-00',
		                                                      '{$mT154['T154_Valor_Parcela']}',
		                                                      '{$mT154['T154_Valor_Parcela']}',
		                                                      '0',
		                                                      '0',
		                                                      '{$mT154['T154_Valor_Parcela']}',
		                                                      '$Valor_Base_Comissao_Titulo',
		                                                      '{$mT013['T013_Percentual_Comissao_Representante']}',
		                                                      '{$dataLancamento}',
		                                                      '{$horaLancamento}',
		                                                      $i)");
                    
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return 'falha na geração de contas a receber';
                    }
                    $T002_Id = $g['mysqlLastId'];
                    
                    if ($mT154['D031_Flag_Baixa_Automatica'] == 'S') {
                        
                        $cClausula = " insert into T045 ( ";
                        $cClausula .= " T045_C004_Id, ";
                        $cClausula .= " T045_C007_Id, ";
                        $cClausula .= " T045_Data ";
                        $cClausula .= " ) values( ";
                        $cClausula .= " '{$g['empresaAtual']}', ";
                        $cClausula .= " '{$g['usuarioAtual']}', ";
                        $cClausula .= " now() ";
                        $cClausula .= " )";
                        $cSql_T045 = mysql_query($cClausula);
                        $cErro     = mysql_error();
                        if (!empty($cErro)) {
                            mysql_query("ROLLBACK");
                            return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
                            exit;
                        }
                        $T045_Id = $g['mysqlLastId'];
                        
                        $cClausula = " update T002 set ";
                        $cClausula .= " T002_T045_Id='$T045_Id', ";
                        $cClausula .= " T002_Flag_Baixar_Lote='N', ";
                        $cClausula .= " T002_Data_Baixa=CURDATE() ";
                        $cClausula .= " where T002_Id='{$T002_Id}' ";
                        $cSql_T045 = mysql_query($cClausula);
                        $cErro     = mysql_error();
                        if (!empty($cErro)) {
                            mysql_query("ROLLBACK");
                            return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
                            exit;
                        }
                        $dataRecebimento = date("Y-m-d");
                        mysql_query("call T002_Baixar_Titulo('{$T002_Id}','$dataRecebimento')");
                    }
                    /*            
                    $mGravar['T002_D024_Id']="{$mT013['T013_D024_Id']}";
                    $mGravar['T002_T013_Id']="$T013_Id";
                    $mGravar['T002_C004_Id']="{$mT013['T013_C004_Id']}";
                    $mGravar['T002_D062_Id']="{$mT013['D024_D062_Id']}";
                    $mGravar['T002_D021_Id']="{$mT013['T013_D021_Id']}";
                    $mGravar['T002_Nome_Cliente']="";
                    $mGravar['T002_Numero_Duplicata']="{$mT013['T013_Numero_Nota_Fiscal']}"."$mLetras[$i]";
                    $mGravar['T002_Numero_Documento']="{$mT013['T013_Numero_Nota_Fiscal']}";
                    $mGravar['T002_Data_Emissao']="{$mT013['T013_Data_Emissao']}";
                    $mGravar['T002_Data_Vencimento']="{$mT154['T154_Data_Vencimento']}";
                    $mGravar['T002_Data_Recebimento']="0000-00-00";
                    $mGravar['T002_Valor_Duplicata']="{$mT154['T154_Valor_Parcela']}";
                    $mGravar['T002_Valor_Juros']="0";
                    $mGravar['T002_Valor_Desconto']="0";
                    $mGravar['T002_Valor_Total']="{$mT154['T154_Valor_Parcela']}";
                    $mGravar['T002_Valor_Base_Comissao']=$Valor_Base_Comissao_Titulo;
                    $mGravar['T002_Percentual_Comissao']=$mT013['T013_Percentual_Comissao_Representante'];
                    $mGravar['T002_Data_Lancamento']=troca_data_amd(hoje());
                    $mGravar['T002_Hora_Lancamento']=strftime('%T');
                    $mGravar['T002_Numero_Parcela']=$i;
                    
                    gravar_entidade($mGravar,'T002','','','','','MANUAL','001');
                    */
                }
                mysql_query("COMMIT");
                
            } elseif ($D006_Flag_Gera_Contas == 'P') {
                $mT013['D024_Nome_Empresa'] = mysql_real_escape_string($mT013['D024_Nome_Empresa']);
                mysql_query("START TRANSACTION");
                $teste = '';
                while ($mT154 = mysql_fetch_array($cSql_T154)) {
                    
                    //unset($mGravar);
                    $i              = $mT154['T154_Numero_Parcela'];
                    $horaLancamento = strftime('%T');
                    mysql_query("insert into T015 (T015_D031_Id,
		                                                      T015_D014_Id,
		                                                      T015_D024_Id,
		                                                      T015_T013_Id,
		                                                      T015_C004_Id,
                                                              T015_D021_Id,
                                                              T015_D014_Id,
		                                                      T015_D032_Id,
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
		                                                      T015_Valor_Original,
		                                                      T015_Valor_Base_Comissao,
		                                                      T015_Percentual_Comissao,
		                                                      T015_Data_Lancamento,
		                                                      T015_Hora_Lancamento,
		                                                      T015_Numero_Parcela
		                                                      )
		                                                      values 
		                                                      ('{$mT154['T154_D031_Id']}',
		                                                      '{$mT013['T013_D014_Id']}',
		                                                      '{$mT013['T013_D024_Id']}',
		                                                      '{$T013_Id}',
		                                                      '{$mT013['T013_C004_Id']}',
		                                                      '{$mT013['T013_D021_Id']}',
                                                              '{$D014_D032_Id}',
                                                              '{$D006_D014_Id}',
		                                                      '{$mT013['D024_Nome_Empresa']}',
		                                                      '{$mT013['T013_Numero_Nota_Fiscal']}" . "$mLetras[$i]',
		                                                      '{$mT013['T013_Numero_Nota_Fiscal']}',
		                                                      '{$mT013['T013_Data_Emissao']}',
		                                                      '{$mT154['T154_Data_Vencimento']}',
		                                                      '0000-00-00',
		                                                      '{$mT154['T154_Valor_Parcela']}',
		                                                      '{$mT154['T154_Valor_Parcela']}',
		                                                      '0',
		                                                      '0',
		                                                      '{$mT154['T154_Valor_Parcela']}',
		                                                      '{$mT154['T154_Valor_Parcela']}',
		                                                      '$Valor_Base_Comissao_Titulo',
		                                                      '{$mT013['T013_Percentual_Comissao_Representante']}',
		                                                      '{$mT013['T013_Data_Entrada']}',
		                                                      '{$horaLancamento}',
		                                                      $i)");
                    
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return 'falha na geração de contas a pagar: ' . $erro;
                    }
                    $T015_Id = $g['mysqlLastId'];
                    
                    if ($mT154['D031_Flag_Baixa_Automatica'] == 'S') {
                        $cClausula = " insert into T045 ( ";
                        $cClausula .= " T045_C004_Id, ";
                        $cClausula .= " T045_C007_Id, ";
                        $cClausula .= " T045_Data ";
                        $cClausula .= " ) values( ";
                        $cClausula .= " '{$g['empresaAtual']}', ";
                        $cClausula .= " '{$g['usuarioAtual']}', ";
                        $cClausula .= " now() ";
                        $cClausula .= " )";
                        $cSql_T045 = mysql_query($cClausula);
                        $cErro     = mysql_error();
                        if (!empty($cErro)) {
                            mysql_query("ROLLBACK");
                            return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
                            exit;
                        }
                        $T045_Id = $g['mysqlLastId'];
                        
                        $cClausula = " update T015 set ";
                        $cClausula .= " T015_D007_Id='$D007_Id', ";
                        $cClausula .= " T015_T045_Id='$T045_Id', ";
                        $cClausula .= " T015_Data_Baixa=CURDATE(), ";
                        $cClausula .= " T015_Data_Pagamento=CURDATE(), ";
                        $cClausula .= " T015_Valor_Pago='{$mT154['T154_Valor_Parcela']}', ";
                        $cClausula .= " T015_Valor_Juros='0', ";
                        $cClausula .= " T015_Valor_Desconto='0' ";
                        $cClausula .= " where T015_Id='{$T015_Id}' ";
                        $teste .= $cClausula . "<br />";
                        $cSql_T045 = mysql_query($cClausula);
                        $cErro     = mysql_error();
                        if (!empty($cErro)) {
                            mysql_query("ROLLBACK");
                            return "<div class='msgError'>Erro: $cErro<br />Clausula: $cClausula</div>";
                            exit;
                        }
                        $dataBaixa = date("Y-m-d");
                        //mysql_query("call T015_Baixar_Titulo('$T015_Id','$dataBaixa')");
                    }
                    /*                                          		            
                    $mGravar['T015_D031_Id']="{$mT154['T154_D031_Id']}";
                    $mGravar['T015_D014_Id']="{$mT013['T013_D014_Id']}"; 
                    $mGravar['T015_D024_Id']="{$mT013['T013_D024_Id']}";
                    $mGravar['T015_T013_Id']="$T013_Id";
                    $mGravar['T015_C004_Id']="{$mT013['T013_C004_Id']}";
                    $mGravar['T015_D021_Id']="{$mT013['T013_D021_Id']}";
                    $mGravar['T015_Nome_Cliente']="{$mT013['D024_Nome_Empresa']}";
                    $mGravar['T015_Numero_Duplicata']="{$mT013['T013_Numero_Nota_Fiscal']}"."$mLetras[$i]";
                    $mGravar['T015_Numero_Documento']="{$mT013['T013_Numero_Nota_Fiscal']}";
                    $mGravar['T015_Data_Emissao']="{$mT013['T013_Data_Emissao']}";
                    $mGravar['T015_Data_Vencimento']="{$mT154['T154_Data_Vencimento']}";
                    $mGravar['T015_Data_Pagamento']="0000-00-00";
                    $mGravar['T015_Valor_Duplicata']="{$mT154['T154_Valor_Parcela']}";
                    $mGravar['T015_Valor_Juros']="0";
                    $mGravar['T015_Valor_Desconto']="0";
                    $mGravar['T015_Valor_Total']="{$mT154['T154_Valor_Parcela']}";
                    $mGravar['T015_Valor_Base_Comissao']=$Valor_Base_Comissao_Titulo;
                    $mGravar['T015_Percentual_Comissao']=$mT013['T013_Percentual_Comissao_Representante'];
                    $mGravar['T015_Data_Lancamento']="$mT013[T013_Data_Entrada]";
                    $mGravar['T015_Hora_Lancamento']=strftime('%T');
                    $mGravar['T015_Numero_Parcela']=$i;
                    
                    gravar_entidade($mGravar,'T015','','','','','MANUAL','001');
                    */
                }
                mysql_query("COMMIT");
                //return $teste;
            }
        } else {
            $Msg = "<br />Não foram geradas contas a pagar e receber;";
        }
        
        ///////////////////////////////////////////////////////////////////////////////////
        //-- LANCAMENTO NO HISTORICO E NO ESTOQUE
        ///////////////////////////////////////////////////////////////////////////////////
        
        $cSql_T014 = mysql_query("select *
		                              from T014
		                         left join D009 on D009_Id=T014_D009_Id
		                         left join D049 on D049_Id=D009_D049_Id
		                         left join D006 on D006_Id=T014_D006_Id
		                            where T014_T013_Id='$T013_Id'
		                              and D006_Flag_Estoque!='N'");
        
        $D006_Flag_Estoque = "N";
        
        while ($mT014 = mysql_fetch_array($cSql_T014)) {
            
            
            ///////////////////////////////////////////////////////////////////////////////////
            // ENCONTRA O ID DO T066
            ///////////////////////////////////////////////////////////////////////////////////
            $cSql_T066 = mysql_query("select T066_Id from T066 where T066_D048_Id='$D048_Id' and T066_D009_Id='{$mT014['T014_D009_Id']}'");
            $mT066     = mysql_fetch_array($cSql_T066);
            
            ///////////////////////////////////////////////////////////////////////////////////
            // MOVIMENTA��O DO ESTOQUE
            ///////////////////////////////////////////////////////////////////////////////////
            
            if ($mT014['D006_Flag_Estoque'] == 'D') {
                $Flag_Operacao_Historico = "S";
                $D006_Flag_Estoque       = "S";
                
            } elseif ($mT014['D006_Flag_Estoque'] == 'C') {
                
                $Flag_Operacao_Historico = "E";
                $D006_Flag_Estoque       = "S";
                
            } elseif ($mT014['D006_Flag_Estoque'] == 'N') {
                
                $Flag_Operacao_Historico = "N";
                
            }
            
            unset($mDemonstrativo_Custo);
            $mDemonstrativo_Custo[] = " ,,Preço,," . number_format($mT014['T014_Valor_Custo_Unitario'], 2, ',', '.');
            $mDemonstrativo_Custo[] = "-,,Desconto nos Itens,," . number_format($mT014['T014_Valor_Desconto_Unitario'], 2, ',', '.');
            $mDemonstrativo_Custo[] = " ,,Preço Liquido,," . number_format($mT014['T014_Valor_Custo_Unitario'] - $mT014['T014_Valor_Desconto_Unitario'], 2, ',', '.');
            if ($mC004['C004_Flag_Empresa_Simples'] != '1') {
                $mDemonstrativo_Custo[] = "-,,{$mT014['T014_Aliquota_ICMS']}% ICMS,," . number_format($mT014['T014_Valor_ICMS'] / $mT014['T014_Quantidade'], 2, ',', '.');
            }
            
            if ($mT014['D006_Flag_Credita_IPI'] != 'S') {
                $mDemonstrativo_Custo[] = "+,,{$mT014['T014_Aliquota_IPI']}% IPI,," . number_format($mT014['T014_Valor_IPI'] / $mT014['T014_Quantidade'], 2, ',', '.');
            }
            $mDemonstrativo_Custo[] = "-,,ICMS Dif.Aliquota,," . number_format($mT014['T014_Valor_ICMS_Diferenca_Aliquotas'] / $mT014['T014_Quantidade'], 2, ',', '.');
            if ($mT013['D006_Flag_Substituicao_Tributaria'] == 'S' && $mT013['D006_Flag_Venda_Compra_Outros'] == 'T' && $mT013['T013_C004_Id'] == 2) {
                $mDemonstrativo_Custo[] = " ,,ICMS Substituição,," . number_format($mT014['T014_Valor_ICMS_Substituicao_Unitario'], 2, ',', '.');
            } else {
                $mDemonstrativo_Custo[] = "+,,ICMS Substituição,," . number_format($mT014['T014_Valor_ICMS_Substituicao_Unitario'], 2, ',', '.');
            }
            $mDemonstrativo_Custo[] = "+,,Frete,," . number_format($mT014['T014_Valor_Frete'] / $mT014['T014_Quantidade'], 2, ',', '.');
            $mDemonstrativo_Custo[] = "+,,Seguro,," . number_format($mT014['T014_Valor_Seguro'] / $mT014['T014_Quantidade'], 2, ',', '.');
            $mDemonstrativo_Custo[] = "+,,Despesas Acessórias,," . number_format($mT014['T014_Valor_Despesas_Acessorias'] / $mT014['T014_Quantidade'], 2, ',', '.');
            
            $mDemonstrativo_Custo[] = "=,,Custo Final,," . number_format($mT014['T014_Valor_Custo_Final_Unitario'], 2, ',', '.');
            
            if ($mT014['D049_Flag_Nacional_Importado'] == 'I') {
                $mT014['T014_Valor_Custo_Final_Unitario'] = $mT014['T014_Valor_Custo_Final_Unitario'] / $mT013['T013_Valor_Dolar'];
                $mDemonstrativo_Custo[]                   = "=,,Dólar,," . number_format($mT013['T013_Valor_Dolar'], 4, ',', '.');
                $mDemonstrativo_Custo[]                   = "=,,Custo Final US,," . number_format($mT014['T014_Valor_Custo_Final_Unitario'], 2, ',', '.');
            }
            
            
            $mDemonstrativo_Custo[] = "";
            $mDemonstrativo_Custo[] = ",,% Redução ICMS,," . number_format($mT014['T014_Percentual_Reducao_ICMS'], 2, ',', '.');
            $mDemonstrativo_Custo[] = ",,Base ICMS,," . number_format($mT014['T014_Valor_Base_ICMS'] / $mT014['T014_Quantidade'], 2, ',', '.');
            
            $mDemonstrativo_Custo[] = "";
            $mDemonstrativo_Custo[] = ",,% Redução IPI,," . number_format($mT014['T014_Percentual_Reducao_IPI'], 2, ',', '.');
            $mDemonstrativo_Custo[] = ",,Base IPI,," . number_format($mT014['T014_Valor_Base_IPI'] / $mT014['T014_Quantidade'], 2, ',', '.');
            
            $Demonstrativo_Custo = implode("\n", $mDemonstrativo_Custo);
            
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // HISTORICO DO PRODUTO
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            $tmpD004_Id = getD004($mT066['T066_Id']);
            $T001_Id    = mysql_query("insert into T001 (T001_D024_Id,
		                                                      T001_T014_Id,
		                                                      T001_T013_Id,
		                                                      T001_D009_Id,
		                                                      T001_C004_Id,
		                                                      T001_D006_Id,
		                                                      T001_T066_Id,
		                                                      T001_Codigo_Produto,
		                                                      T001_Data_Lancamento,
		                                                      T001_Descricao_Operacao,
		                                                      T001_Quantidade,
		                                                      T001_Flag_Operacao,
		                                                      T001_Valor_Preco_Unitario,
		                                                      T001_Valor_Custo_Unitario,
		                                                      T001_Flag_Atualiza_Custo,
		                                                      T001_Quantidade_Saldo,
		                                                      T001_Demonstrativo_Custo,
		                                                      T001_Flag_Devolucao,
		                                                      T001_D004_Id
		                                                      )
		                                                      values 
		                                                      ('{$mT013['T013_D024_Id']}',
		                                                      '{$mT014['T014_Id']}',
		                                                      '{$mT013['T013_Id']}',
		                                                      '{$mT014['T014_D009_Id']}',
		                                                      '{$mT014['T014_C004_Id']}',
		                                                      '{$mT014['T014_D006_Id']}',
		                                                      '{$mT066['T066_Id']}',
		                                                      '{$mT014['T014_Codigo_Produto']}',
		                                                      '{$mT013['T013_Data_Entrada']}',
		                                                      'NF-{$mT013['T013_Numero_Nota_Fiscal']}',
		                                                      '{$mT014['T014_Quantidade']}',
		                                                      '$Flag_Operacao_Historico',
		                                                      '{$mT014['T014_Valor_Custo_Final_Unitario']}',
		                                                      '{$mT014['T014_Valor_Custo_Final_Unitario']}',
		                                                      '{$mT013['D006_Flag_Atualiza_Custo']}',
		                                                      '0',
		                                                      '$Demonstrativo_Custo',
		                                                      '{$mT013['D006_Flag_Devolucao']}',
		                                                      '{$tmpD004_Id}')");
            
            // $CAD002->D001_reprocessa_historico(0, $mT014['T014_D009_Id'],100);
            $retornoProcessa = $CAD002->D001_reprocessa_historico(0, $mT014['T014_D009_Id'],100);
            if($retornoProcessa !== true){
                registrarLog(array("D009", $mT014['T014_D009_Id'], $g['empresaAtual'], " Erro ao reprocessar histórico - EST004 - FINALIZAR NOTA ENTRADA:", $retornoProcessa, "S",  3));
				$CAD002->D001_reprocessa_historico(0, $mT014['T014_D009_Id']);
			}
            
            mysql_query("update D049 set D049_ICF=0,D049_Flag_ICF_Temporario='X' where D049_Id='{$mT014['D049_Id']}' and D049_Flag_ICF_Temporario='S'");
            
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return 'Atualizando ICF Temporario';
            }
            
            /*
            $D083 = mysql_query("select D083_Id,D083_Flag_Fornecedor from D083 where D083_D001_Id='{$mT014['D049_D001_Id']}' and D083_Flag_Fornecedor='S' and D083_D049_Id='{$mT014['D049_Id']}' and D083_D024_Id='{$mT013['T013_D024_Id']}'");
            if (mysql_num_rows($D083) <= 0) {
                mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Fornecedor,D083_Codigo_Produto_Fornecedor) values ('{$mT014['D049_Id']}','{$mT014['D049_D001_Id']}','{$mT013['T013_D024_Id']}',curdate(),'S','{$mT014['T014_Codigo_Produto_Fornecedor']}')");
            } else {
                $mD083 = mysql_fetch_array($D083);
                mysql_query("update D083 set D083_Flag_Fornecedor='S',D083_Codigo_Produto_Fornecedor='{$mT014['T014_Codigo_Produto_Fornecedor']}' where D083_Id='{$mD083['D083_Id']}'");
            }
            */
            require_once("bibliotecas/classes/CAD002.php");
            $CAD002 = new CAD002();
            $CAD002->definirCodigoFornecedor($mT014['T014_D009_Id'], $mT013['T013_D024_Id'], $mT014['T014_Codigo_Produto_Fornecedor'], '', false, true);
            
            
        }
        
        //////////////////////////////////////////////////////////////////
        //                                                              //
        //       ATUALIZA CLASSIFICACOES DA NOTA RECEBIDA               //
        //                                                              //
        //////////////////////////////////////////////////////////////////
        $Empresa_Grupo = 'N';
        
        if ((substr($mT013['D024_Nome_Empresa'], 0, 6) == "D.M.R.") OR (substr($mT013['D024_Nome_Empresa'], 0, 9) == "ENGEPECAS")) {
            $Empresa_Grupo = 'S';
        }
        // COMPRA
        if (($mT013['D006_Flag_Entrada_Saida'] == 'E') && ($mT013['D006_Flag_Devolucao'] != 'S') && (($mT013['D006_Flag_Venda_Compra_Outros'] == 'C') || ($mT013['D006_Flag_Venda_Compra_Outros'] == 'T')) && ($mT013['D006_Flag_Gera_Contas'] == 'P')) {
            $Classificacao = 1;
        }
        
        // DEVOLUCAO VENDA
        if (($mT013['D006_Flag_Entrada_Saida'] == 'E') && ($mT013['D006_Flag_Venda_Maquina'] != 'S') && ($mT013['D006_Flag_Devolucao'] == 'S') && ($mT013['D006_Flag_Venda_Compra_Outros'] == 'C') && ($mT013['D024_Flag_Mono'] != '1') && ($Empresa_Grupo == 'N')) {
            $Classificacao = 2;
        }
        
        
        
        $mC004['C004_Ultimo_Numero_Controle']++;
        mysql_query("update T013 set T013_Flag_Classificacao='$Classificacao',T013_Flag_Travar_Nota='S',T013_Numero_Controle='{$mC004['C004_Ultimo_Numero_Controle']}' where T013_Id='$T013_Id'");
        mysql_query("update C004 set C004_Ultimo_Numero_Controle='{$mC004['C004_Ultimo_Numero_Controle']}' where C004_Id='{$g['empresaAtual']}'");
        // mysql_query("update T014
        //                left join D009 on D009_Id=T014_D009_Id
        //                left join D049 on D049_Id=D009_D049_Id
        //                left join D001 on D001_Id=D049_D001_Id
        //                left join T013 on T013_Id=T014_T013_Id
        //                set T014_D003_Id=D001_D003_Id,T014_Flag_Classificacao=T013_Flag_Classificacao
        //                where T014_T013_Id='$T013_Id'");
        
        $D001 = mysql_query("SELECT * FROM T014 
			   left join D009 on D009_Id=T014_D009_Id
	           left join D049 on D049_Id=D009_D049_Id
	           left join D001 on D001_Id=D049_D001_Id
			WHERE T014_T013_Id='{$T013_Id}'");
        
        while ($mD001 = mysql_fetch_array($D001)) {
            mysql_query("UPDATE T014 
						left join D001 on D001_Id='{$mD001['D001_Id']}'
		               	left join T013 on T013_Id=T014_T013_Id
		               	set T014_D003_Id=D001_D003_Id,T014_Flag_Classificacao=T013_Flag_Classificacao
		               	WHERE T014_Id='{$T014_Id}'
		               	");
            
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return 'Erro ao atualizar pedidos de venda';
            }
        }
        
        if ($D006_Flag_Estoque == 'N') {
            $Msg .= "<br />Não foram gerados movimentaçães no estoque";
        }
        
        
        // Inserção de produtos com número de série
        $T014Serial = mysql_query("SELECT * FROM T014 
								LEFT JOIN D006 ON D006_Id=T014_D006_Id 
								LEFT JOIN D009 ON D009_Id=T014_D009_Id
								LEFT JOIN D049 ON D049_Id=D009_D049_Id
								LEFT JOIN D001 ON D001_Id=D049_D001_Id
								WHERE T014_T013_Id='{$T013_Id}'");
        
        while ($mT014Serial = mysql_fetch_array($T014Serial)) {
            
            if ($mT014Serial['D001_Flag_Produto_Serie'] == 'S') {
                
                $quantidade = $mT014Serial['T014_Quantidade'];
                
                for ($i = 1; $i <= $quantidade; $i++) {
                    $D144    = <<<EOT
						INSERT INTO D144 (
							D144_D049_Id,
							D144_T014_Id
						) VALUES (
							'{$mT014Serial['D049_Id']}',
							'{$mT014Serial['T014_Id']}'
						);
EOT;
                    $retorno = mysql_query($D144);
                    if ($retorno != true) {
                        mysql_query("ROLLBACK");
                        return "Erro ao inserir o número de série.";
                    }
                }
            }
        }
        
        
        mysql_query("COMMIT");
        mysql_query("UNLOCK TABLE C004");
        mysql_query("UNLOCK TABLE T013");
        
        //atualiza os codigos dos forncedores
        
        $T014 = mysql_query("select T014_Codigo_Produto,
		                          D001_Codigo_Produto,
		                          D001_Id,
		                          D049_Id,
		                          D024_Id,
                                  T014_D009_Id
		                     from T014
		                left join D009 on D009_Id=T014_D009_Id
		                left join D049 on D049_Id=D009_D049_Id
		                left join D001 on D001_Id=D049_D001_Id
		                left join T013 on T013_Id=T014_T013_Id
		                left join D024 on D024_Id=T013_D024_Id
		                where T014_T013_Id='$T013_Id'
		                  and T014_Codigo_Produto!=D001_Codigo_Produto
		                 group by T014_Codigo_Produto");
        
        while ($mT014 = mysql_fetch_array($T014)) {
            /*
            $D083 = mysql_query("select D083_Id,D083_Flag_Cliente from D083 where D083_D001_Id='{$mT014['D001_Id']}' and D083_D049_Id='{$mT014['D049_Id']}' and D083_D024_Id='{$mT014['D024_Id']}'");
            if (mysql_num_rows($D083) <= 0) {
                mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Fornecedor,D083_Codigo_Produto_Fornecedor) values ('{$mT014['D049_Id']}','{$mT014['D001_Id']}','{$mT014['D024_Id']}',curdate(),'S','{$mT014['T014_Codigo_Produto']}')");
            }
            */
            require_once("bibliotecas/classes/CAD002.php");
            $CAD002 = new CAD002();
            $CAD002->definirCodigoFornecedor($mT014['T014_D009_Id'], $mT014['D024_Id'], $mT014['T014_Codigo_Produto'], '', false, true);
        }
        
        
        //-- PROCESSA AS PENDENCIAS
        $Pendencias->processarPendencias();
        
        if (!empty($Msg)) {
            return "Devido a Natureza da operação que foi selecionada," . $Msg;
        }
        
        /*
        //-- LIMPA A SESSAO PARA QUE O USUARIO NAO FIQUE RETORNANDO NA NOTA JA FINALIZADA
        session_start();
        $_SESSION['URL_Historico']='';
        $_SESSION['URL_Tipo_Historico']='';
        $_SESSION['URL_Descricao_Historico']='';
        $_SESSION['URL_Entidade_Principal']='';
        
        
        return "Nota finalizada com sucesso!<br />CONTROLE: " . $mC004['C004_Ultimo_Numero_Controle'];
        
        echo "nova_janela('T013_imprimir_almoxarifado.php3?T013_Id=$T013_Id',0,0,10,10);";
        
        echo "parent.location.replace('../nota_recebida/principal.php?D024_Id=$D024_Id&Data=$cData');";
        * */

        mysql_query("SET AUTOCOMMIT=1");
        
        return true;
    }
    
    /**
     * est004AtualizarTotal
     *
     * @param int $T014_Id
     * @param string $campoEditado
     * @param string $valorAntigo
     * @param string $novoValor
     * @return true/string
     */
    public function est004AtualizarTotal($T014_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        
        global $g;
        $query         = "SELECT * FROM T014 WHERE T014_Id='{$T014_Id}'";
        $retorno       = mysql_query($query);
        $linha         = mysql_fetch_assoc($retorno);
        $quantidade    = $linha['T014_Quantidade'];
        $custoUnitario = $linha['T014_Valor_Custo_Unitario'];
        $IPI           = $linha['T014_Aliquota_IPI'];
        $ValorTotal    = $linha['T014_Valor_Total_Custo'];
        
        //$custoTotal = $linha['T014_Valor_Total_Custo'];
        if ($campoEditado == 'T014_Quantidade') {
            //return "Quantidade: " . $quantidade ."<br />Custo Unitário: ". $custoUnitario;
            // if($valorAntigo == $novoValor){
            // return "O prazo é o mesmo";
            // }
            
            $novoValor      = ($quantidade * $custoUnitario);
            //$valorFormatado = number_format($novoValor, 2, '.', ',');
            $valorFormatado = gCorrigeNumeroInverte($novoValor);
            $query          = "UPDATE T014 SET T014_Valor_Total_Custo='$valorFormatado' WHERE T014_Id='{$T014_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T014_Valor_Custo_Unitario') {
            $novoValor      = ($quantidade * $custoUnitario);
            //$valorFormatado = number_format($novoValor, 2, '.', ',');
            $valorFormatado = gCorrigeNumeroInverte($novoValor);
            $query          = "UPDATE T014 SET T014_Valor_Total_Custo='$valorFormatado' WHERE T014_Id='{$T014_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T014_Aliquota_IPI') {
            $novoValor      = (($ValorTotal * $IPI) / 100);
            //$valorFormatado = number_format($novoValor, 2, '.', ',');
            $valorFormatado = gCorrigeNumero($novoValor);
            $query          = "UPDATE T014 SET T014_Valor_IPI='$valorFormatado' WHERE T014_Id='{$T014_Id}'";
            mysql_query($query);
        }
        
        mysql_query("CALL T014_Gravar_Totalizacao_Custo('{$T014_Id}')");
        return true;
    }
    
    /**
     * excluirNota
     *
     * @param int $T013_Id
     * @return true/string
     */
    public function excluirNota($T013_Id)
    {
        global $g;
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        // Retorna as ordens de compra caso estejam com status finalizado para status aprovado
        $cClausula = " select ";
        $cClausula .= " T225_T224_Id as T224_Id";
        $cClausula .= " from T014 ";
        $cClausula .= " left join T225 on T225_Id = T014_T225_Id ";
        $cClausula .= " where T014_T013_Id='$T013_Id' ";
        $cClausula .= " group by T225_T224_Id ";
        $T224 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p/p$cClausula";
        }

        while ($mT224 = mysql_fetch_array($T224)) {
             mysql_query("update T224 set T224_Flag_Status='3', T224_Nome_Status=T224_Status_Pedido(3,T224_Id) where T224_Id='{$mT224['T224_Id']}'");
        }
        
        $cClausula = " select ";
        $cClausula .= " T014_Id ";
        $cClausula .= " from T014 ";
        $cClausula .= " where T014_T013_Id='$T013_Id' ";
        $cSql_T013 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p/p$cClausula";
        }
        
        while ($mT013 = mysql_fetch_array($cSql_T013)) {


            $this->excluirMaterial($mT013['T014_Id'], 'T014');

            $cTabela_Relacinamento = gPegaRelacionamento('T014', $mT013['T014_Id'], 'T001,T014A');
            
            if (!empty($cTabela_Relacinamento)) {
                mysql_query("ROLLBACK");
                return 'Nota não pode ser excluida<br>Existem itens vinculados a registros de outras tabelas (' . $cTabela_Relacinamento . ')';
            } else {
                $excluir = mysql_query("delete from T001 where T001_T014_Id='{$mT013['T014_Id']}'");
                $cErro   = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: $cErro/p/p$cClausula";
                }
                mysql_query("delete from T014A where T014A_T014_Id = '{$mT013['T014_Id']}'");
                $cErro   = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: $cErro/p/p$cClausula";
                }
                $excluir = mysql_query("delete from T014 where T014_Id='{$mT013['T014_Id']}'");
                $cErro   = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: $cErro/p/p$cClausula";
                }
            }
        }

        $this->excluirMaterial($T013_Id, 'T013');

        $cTabela_Relacinamento = gPegaRelacionamento('T013', $T013_Id, 'T015,T085,T086,T154,T013A');
        if (!empty($cTabela_Relacinamento)) {
            mysql_query("ROLLBACK");
            return "Nota não pode ser excluída<br>Existem registros em outras tabelas ($cTabela_Relacinamento)";
        } else {
            // Exclui itens das "Importar OC" pendentes para esta Nota Fiscal
            $excluir = mysql_query("delete from T086 where T086_T013_Id='$T013_Id'");
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/p$cClausula";
            }
            
            // Exclui itens da tab "Pagamento" desta Nota Fiscal
            $excluir = mysql_query("delete from T154 where T154_T013_Id='$T013_Id'");
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/p$cClausula";
            }
            
            $excluir = mysql_query("delete from T085 where T085_T013_Id='$T013_Id'");
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/p$cClausula";
            }
            $excluir = mysql_query("delete from T015 where T015_T013_Id='$T013_Id'");
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/p$cClausula";
            }
            $excluir = mysql_query("delete from T013A where T013A_T013_Id='$T013_Id'");
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/p$cClausula";
            }
            $excluir = mysql_query("delete from T013 where T013_Id='$T013_Id'");
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/p$cClausula";
            }
            mysql_query("COMMIT");
            return true;
        }
    }
    
    /**
     * associarProdutoT008
     *
     * @param Form $form
     * @return true/string
     */
    public function associarProdutoT008($form)
    {
        global $g;
        $D009_Id = $form->campoValorEnviado('D009_Id');
        $T014_Id = $form->campoValorEnviado('T014_Id');
        $T008_Id = $form->campoValorEnviado('T008_Id');
        
        $cClausula = " select ";
        $cClausula .= " * ";
        $cClausula .= " from D009 ";
        $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
        $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
        $cClausula .= " left join D005 on D009_D005_Id(D009_Id)=D005_Id ";
        $cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
        $cClausula .= " where D009_Id='{$D009_Id}' ";
        $T014 = mysql_query("{$cClausula}");
        echo mysql_error();
        $mT014 = mysql_fetch_array($T014);
        if (mysql_num_rows($T014) == 0) {
            return "Erro: O produto não foi encontrado.";
        }
        
        $sqlT008 = mysql_query("SELECT ROUND(IF(T008A_D184_Id > 0 , T008A_Preco_Unitario_Comercial,T008_Valor_Preco_Unitario) -(T008_Valor_Total_Desconto / IF(T008A_D184_Id > 0 ,T008A_Quantidade_Comercial,T008_Quantidade)),8  ) AS T008_Valor_Preco_Unitario,
                                       IF(T008A_D184_Id > 0 ,T008A_Quantidade_Comercial,T008_Quantidade) AS T008_Quantidade, 
                                       T008_Quantidade_Falta_Lancar(T008_Id) as Saldo 
                                  FROM T008 
                             LEFT JOIN T008A ON T008A_T008_Id = T008_Id
                                 WHERE T008_Id = '{$T008_Id}'");

        $rowT008 = mysql_fetch_array($sqlT008);
        
        $sqlT014 = mysql_query("SELECT round(T014_Valor_Custo_Unitario-(T014_Valor_Total_Desconto/T014_Quantidade),8) as T014_Valor_Custo_Unitario, 
                                       T014_Quantidade, 
                                       T014_T008_Id 
                                  FROM T014 
                                 WHERE T014_Id = '{$T014_Id}'");
        $rowT014 = mysql_fetch_array($sqlT014);

        if($rowT014['T014_T008_Id'] > 0){
            return "O item da nota fiscal recebida já está vinculado a um produto, desvincule o produto e tente novamente.";
        }
        
        $toleranciaDiferenca = $rowT008['T008_Valor_Preco_Unitario'] - $rowT014['T014_Valor_Custo_Unitario'];

        if($rowT008['T008_Valor_Preco_Unitario'] != $rowT014['T014_Valor_Custo_Unitario'] && ($toleranciaDiferenca > 0.05 || $toleranciaDiferenca < -0.05)){
            $toleranciaDiferenca       = gCorrigeNumero($toleranciaDiferenca,6);
            $T008_Valor_Preco_Unitario = gCorrigeNumero($rowT008['T008_Valor_Preco_Unitario'],6);
            $T014_Valor_Custo_Unitario = gCorrigeNumero($rowT014['T014_Valor_Custo_Unitario'],6);
            return "O preço unitário do produto selecionado tem uma diferença de  que R$ {$toleranciaDiferenca} entre NF recebida e emitida <br><br>NF Emitida: {$T008_Valor_Preco_Unitario}<br>NF Recebida:{$T014_Valor_Custo_Unitario}";
        } else if($rowT014['T014_Quantidade'] > $rowT008['Saldo']){

            $notas = mysql_query("SELECT GROUP_CONCAT(DISTINCT T013_Numero_Nota_Fiscal) as notas
                                    FROM T008
                               LEFT JOIN T014 ON T014_T008_Id=T008_Id
                               LEFT JOIN T013 ON T013_Id=T014_T013_Id  
                                   WHERE T008_Id='{$T008_Id}'");
            
            $mNotas = mysql_fetch_array($notas);

			$saldoDisponivel = gCorrigeNumero($rowT008['Saldo']);

            $mensagem = "Saldo disponível: {$saldoDisponivel}<br>Notas de entrada já vinculadas: {$mNotas['notas']}";

            return "A quantidade do produto selecionado é menor que o produto nota fiscal recebida!<br><br>".$mensagem;
        }


        $cClausula = " update T014 set ";
        $cClausula .= " T014_D009_Id='{$D009_Id}', ";
        $cClausula .= " T014_T008_Id='{$T008_Id}', ";
        $cClausula .= " T014_C004_Id='{$g['empresaAtual']}', ";
        $cClausula .= " T014_T066_Id='0', ";
        $cClausula .= " T014_Codigo_Produto_Fornecedor=T014_Codigo_Produto ";
        //$cClausula .= " T014_Codigo_Produto='$mT014[D001_Codigo_Produto]' ";
        $cClausula .= " where T014_Id='{$T014_Id}' ";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: Erro ao vincular item da nota fiscal.";
        }
        
        $sql    = mysql_query("SELECT T013_D024_Id, T014_Codigo_Produto FROM T014 LEFT JOIN T013 on T013_Id=T014_T013_Id WHERE T014_Id='{$T014_Id}'");
        $result = mysql_fetch_array($sql);
        
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $CAD002->definirCodigoFornecedor($mT014['D009_Id'], $result['T013_D024_Id'], $result['T014_Codigo_Produto'], '', false, true);
        
        return true;
    }
    
    /**
     * associarProduto
     *
     * @param Form $form
     * @return true/string
     */
    public function associarProduto($form)
    {
        global $g;
        $D009_Id = $form->campoValorEnviado('D009_Id');
        $T014_Id = $form->campoValorEnviado('T014_Id');
        $T008_Id = $form->campoValorEnviado('T008_Id');      

        $T013=mysql_query("SELECT T013_Flag_Travar_Nota from T014 LEFT JOIN T013 ON T013_Id=T014_T013_Id WHERE T014_Id='{$T014_Id}'");
        $mT013=mysql_fetch_array($T013);
        if($mT013['T013_Flag_Travar_Nota']=='S'){
            return "Não é possível alterar o produto, nota fiscal já finalizada.";
        }

        
        $cClausula = " select ";
        $cClausula .= " * ";
        $cClausula .= " from D009 ";
        $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
        $cClausula .= " left join D001 on D001_Id=D049_D001_Id ";
        $cClausula .= " left join D005 on D009_D005_Id(D009_Id)=D005_Id ";
        $cClausula .= " left join D037 on D037_Id=D001_D037_Id ";
        $cClausula .= " where D009_Id='{$D009_Id}' ";
        $T014 = mysql_query("{$cClausula}");
        echo mysql_error();
        $mT014 = mysql_fetch_array($T014);
        if (mysql_num_rows($T014) == 0) {
            return "Erro: O produto não foi encontrado.";
        }
        
        $cClausula = " update T014 set ";
        $cClausula .= " T014_D009_Id='{$D009_Id}', ";
        $cClausula .= " T014_T008_Id='{$T008_Id}', ";
        $cClausula .= " T014_C004_Id='{$g['empresaAtual']}', ";
        $cClausula .= " T014_T066_Id='0', ";
        $cClausula .= " T014_Codigo_Produto_Fornecedor=T014_Codigo_Produto ";
        //$cClausula .= " T014_Codigo_Produto='$mT014[D001_Codigo_Produto]' ";
        $cClausula .= " where T014_Id='{$T014_Id}' ";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: Erro ao vincular item da nota fiscal.";
        }
        
        $sql    = mysql_query("SELECT T013_D024_Id, T014_Codigo_Produto FROM T014 LEFT JOIN T013 on T013_Id=T014_T013_Id WHERE T014_Id='{$T014_Id}'");
        $result = mysql_fetch_array($sql);
        
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $CAD002->definirCodigoFornecedor($mT014['D009_Id'], $result['T013_D024_Id'], $result['T014_Codigo_Produto'], '', false, true);
        
        return true;
    }

    /**
     * est004AtualizarParcelas
     *
     * @param int $T154_Id
     * @param string $campoEditado
     * @param string $valorAntigo
     * @param string $novoValor
     * @return true/string
     */
    public function est004AtualizarParcelas($T154_Id, $campoEditado, $valorAntigo, $novoValor)
    {
        $query            = "SELECT * FROM T154 left join T013 on T154_T013_Id='{$T154_Id}' WHERE T154_Id='{$T154_Id}'";
        $retorno          = mysql_query($query);
        $linha            = mysql_fetch_assoc($retorno);
        $dataVelhaT013    = $linha['T013_Data_Emissao'];
        $prazosDiasAntigo = $linha['T154_Prazos_Dias'];
        
        if ($campoEditado == 'T154_Prazos_Dias') {
            // if($valorAntigo == $novoValor){
            // return "O prazo é o mesmo";
            // }
            
            $date = new \DateTime($$dataVelhaT013);
            $date->modify('+' . $novoValor . ' days');
            $query = "UPDATE T154 SET T154_Data_Vencimento='{$date->format('Y-m-d')}' WHERE T154_Id='{$T154_Id}'";
            mysql_query($query);
            
        } else if ($campoEditado == 'T154_Data_Vencimento') {
            
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
                $query = "UPDATE T154 SET T154_Prazos_Dias='{$total}' WHERE T154_Id='{$T154_Id}'";
                mysql_query($query);
                return true;
            } else if ($timeStampDataAntiga > $timeStampDataNova) {
                $novaData = ($timeStampDataAntiga - $timeStampDataNova);
                $dias     = date('d', $novaData);
                $total    = ($prazosDiasAntigo - $dias);
                $query    = "UPDATE T154 SET T154_Prazos_Dias='{$total}' WHERE T154_Id='{$T154_Id}'";
                mysql_query($query);
                return true;
            }
            
        } else if ($campoEditado == 'T154_Valor_Parcela') {
            
            $query      = "SELECT * FROM T154 WHERE T154_Id='{$T154_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $T013_Id    = $linha['T154_T013_Id'];
            $numParcela = $linha['T154_Numero_Parcela'];
            
            $query      = "SELECT T013_Valor_Total FROM T013 WHERE T013_Id='{$T013_Id}'";
            $retorno    = mysql_query($query);
            $linha      = mysql_fetch_assoc($retorno);
            $valorTotal = $linha['T013_Valor_Total'];
            
            $query                  = "SELECT SUM(T154_Valor_Parcela) FROM T154 WHERE T154_T013_Id='{$T013_Id}' AND T154_Numero_Parcela<='{$numParcela}'";
            $retorno                = mysql_query($query);
            $linha                  = mysql_fetch_assoc($retorno);
            $valorParcelasRestantes = $linha['SUM(T154_Valor_Parcela)'];
            $valorRestante          = $valorTotal - $valorParcelasRestantes;
            
            $query                  = "SELECT * FROM T154 WHERE T154_T013_Id='{$T013_Id}' AND T154_Numero_Parcela>'{$numParcela}'";
            $retorno                = mysql_query($query);
            $parcelasRestantes      = mysql_num_rows($retorno);
            if($parcelasRestantes > 0 ){
                
                $valorParcelasRestantes = $valorRestante / $parcelasRestantes;

            }else{
                
                $valorParcelasRestantes = 0;
            }

            if ($valorParcelasRestantes < 0) {
                $query = "UPDATE T154 SET T154_Valor_Parcela='{$valorAntigo}' WHERE T154_Id='{$T154_Id}'";
                mysql_query($query);

                return 'O valor inserido irá gerar parcelas negativas...<br>Favor alterar novamente';
            }
            //return 'valorTotal='.$valorTotal.'<br>valorRestante='.$valorRestante.'<br>ValorParcelasRestantes='.$valorParcelasRestantes;
            while ($linha = mysql_fetch_assoc($retorno)) {
                $query = "UPDATE T154 SET T154_Valor_Parcela='{$valorParcelasRestantes}' WHERE T154_Id='{$linha['T154_Id']}'";
                mysql_query($query);
            }
            return true;
        }
        
        return true;
    }
    
    /**
     * est004ConcluirInclusaoItens
     *
     * @param int $T013_Id
     * @param string $extraSql
     * @return true/string
     */
    public function xxxest004ConcluirInclusaoItens($T013_Id, $extraSql)
    {
        global $g;
        $nT013_Id = $T013_Id;
        
        $extra = base64_decode($extraSql);
        
        if (empty($nT013_Id)) {
            return 'T013_Id não recebido';
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        //-- CONSULTA DA NOTA FISCAL
        $cClausula = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T013  ";
        $cClausula .= " left join C004 on C004_Id=T013_C004_Id ";
        $cClausula .= " where T013_Id='$nT013_Id' ";
        $cSql_T013 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
        }
        $mT013 = mysql_fetch_array($cSql_T013);
        
        //-- VERIFICA SE A NOTA FISCAL PODE SER ALTERADA
        if ($mT013['T013_Flag_Travar_Nota'] == 'S') {
            return "ESSA NOTA JA FOI FINALIZADA!";
        }
        
        $cClausula = " select ";
        $cClausula .= " D018_Id, ";
        $cClausula .= " D018_UF ";
        $cClausula .= " from D024 ";
        $cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
        $cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
        $cClausula .= " where D024_Id='{$mT013['T013_D024_Id']}' ";
        $cSql_D024 = mysql_query("$cClausula");
        $mD024     = mysql_fetch_array($cSql_D024);
        
        
        $cClausula = " select ";
        $cClausula .= " T011_C004_Id ";
        $cClausula .= " from T012  ";
        $cClausula .= " left join T011 on T011_Id=T012_T011_Id ";
        $cClausula .= " left join D009 on D009_Id=T012_D009_Id ";
        $cClausula .= " WHERE T012_C004_Id!='{$mT013['T013_C004_Id']}' ";
        $cSql_T086 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
        }
        
        if (mysql_num_rows($cSql_T086) > 0) {
            //return "EXISTEM ITENS QUE SAO DE OUTRAS EMPRESAS NESTA(S) O.C.(S)!";
        }
        
        
        //-- BUSCA OS ITENS QUE SERAO ADICIONADOS NA NOTA
        // $cClausula  = " select ";
        // $cClausula .= " T012_D009_Id, ";
        // $cClausula .= " T011_Id, ";
        // $cClausula .= " T011_C004_Id, ";
        // $cClausula .= " T086_T012_Id, ";
        // $cClausula .= " T086_Id, ";
        // $cClausula .= " D001_Codigo_Produto, ";
        // $cClausula .= " D083_Codigo_Produto_Fornecedor, ";
        // $cClausula .= " D001_Descricao_Produto, ";
        // $cClausula .= " T012_Valor_Custo_Unitario, ";
        // $cClausula .= " T012_Aliquota_IPI, ";
        // $cClausula .= " T012_Aliquota_ICMS, ";
        // $cClausula .= " D005_Classificacao_Fiscal, ";
        // $cClausula .= " T086_Quantidade ";
        // $cClausula .= " from T086 ";
        // $cClausula .= " left join T012 on T012_Id=T086_T012_Id ";
        // $cClausula .= " left join T011 on T011_Id=T012_T011_Id ";
        // $cClausula .= " left join D009 on T012_D009_Id=D009_Id ";
        // $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
        // $cClausula .= " left join D001 on D049_D001_Id=D001_Id ";
        // $cClausula .= " left join D005 on D009_D005_Id(T012_D009_Id)=D005_Id ";
        // $cClausula .= " left join D083 on D049_Id=D083_D049_Id and D083_D024_Id='{$mT013['T013_D024_Id']}' ";
        // $cClausula .= " where T086_T013_Id='$nT013_Id' ";
        // $cClausula .= " and T086_Quantidade>0 ";
        // $cClausula .= " group by T012_Id";
        
        $extraNovo = gInsertExtraWhere($extra, "T012_Quantidade_Recebida>0");
        
        $cClausula = " SELECT * FROM T012 " . $extraNovo;
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
        }
        
        $cSql_T012 = mysql_query($cClausula);
        if (mysql_num_rows($cSql_T012) == 0) {
            return " * * * * Nenhum item foi selecionado * * * *";
        }
        
        while ($mT012 = mysql_fetch_array($cSql_T012)) {
            /**
             * Se a nota veio de um XML, verifica se tem algum produto para importar que já tenha sido importado pelo XML e apenas associa.
             */
            $T014 = mysql_query("SELECT * FROM T014 WHERE T014_D009_Id='{$mT012['T012_D009_Id']}' AND T014_T013_Id='$nT013_Id'");
            $rows = mysql_num_rows($T014);
            if ($mT013['T013_Chave_Acesso_Nfe'] != "" && $rows > 0) {
                $mT014 = mysql_fetch_array($T014);
                
                mysql_query("UPDATE T014 SET T014_T012_Id='{$mT012['T012_Id']}' WHERE T014_Id='{$mT014['T014_Id']}'");
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
                }
                $T014_Id = $mT014['T014_Id'];
                
                mysql_query("UPDATE T012 SET T012_Quantidade_Recebida=0 WHERE T012_Id='{$mT012['T012_Id']}'");
                
            } else {
                
                //////////////////////////////////////////////////////////
                //-- FAZ A INCLUSAO DOS ITENS
                $cClausula = " insert into T014 ( ";
                $cClausula .= " T014_C004_Id, ";
                $cClausula .= " T014_T013_Id, ";
                $cClausula .= " T014_D006_Id, ";
                $cClausula .= " T014_D009_Id, ";
                $cClausula .= " T014_T012_Id, ";
                $cClausula .= " T014_Codigo_Produto, ";
                $cClausula .= " T014_Codigo_Produto_Fornecedor, ";
                $cClausula .= " T014_Descricao_Produto, ";
                $cClausula .= " T014_Quantidade, ";
                $cClausula .= " T014_Valor_Custo_Unitario, ";
                $cClausula .= " T014_Aliquota_IPI, ";
                $cClausula .= " T014_Aliquota_ICMS, ";
                $cClausula .= " T014_Classificacao_Fiscal, ";
                $cClausula .= " T014_Unidade ";
                $cClausula .= " ) values ( ";
                $cClausula .= " '{$mT012['T011_C004_Id']}', ";
                $cClausula .= " '{$nT013_Id}', ";
                $cClausula .= " '{$mT013['T013_D006_Id']}', ";
                $cClausula .= " '{$mT012['T012_D009_Id']}', ";
                $cClausula .= " '{$mT012['T012_Id']}', ";
                $cClausula .= " '{$mT012['D001_Codigo_Produto']}', ";
                $cClausula .= " '{$mT012['D083_Codigo_Produto_Fornecedor']}', ";
                $cClausula .= " '{$mT012['D001_Descricao_Produto']}', ";
                $cClausula .= " '{$mT012['T012_Quantidade_Recebida']}', ";
                $cClausula .= " '{$mT012['T012_Valor_Custo_Unitario']}', ";
                $cClausula .= " '{$mT012['T012_Aliquota_IPI']}', ";
                $cClausula .= " '{$mT012['T012_Aliquota_ICMS']}', ";
                $cClausula .= " '{$mT012['D005_Classificacao_Fiscal']}', ";
                $cClausula .= " ' ' ";
                $cClausula .= " ) ";
                
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
                }
                $T014_Id = $g['mysqlLastId'];
                
                mysql_query("UPDATE T012 SET T012_Quantidade_Recebida=0 WHERE T012_Id='{$mT012['T012_Id']}'");
            }
            
            
            mysql_query("call T014_Gravar_Totalizacao($T014_Id)");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
            }
            
            mysql_query("call T011_Gravar_Totalizacao_3({$mT012['T011_Id']})");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
            }
            
        }
        
        mysql_query("COMMIT");
        
        return true;
    }

     /**
     * est004ConcluirInclusaoItens
     *
     * @param int $T013_Id
     * @param string $extraSql
     * @return true/string
     */
    public function est004ConcluirInclusaoItens($T013_Id, $extraSql)
    {
        global $g;
        $nT013_Id = $T013_Id;
        
        $extra = base64_decode($extraSql);
        
        if (empty($nT013_Id)) {
            return 'T013_Id não recebido';
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        //-- CONSULTA DA NOTA FISCAL
        $cClausula = " select ";
        $cClausula .= " T013_Flag_Travar_Nota, ";
        $cClausula .= " T013_Chave_Acesso_Nfe, ";
        $cClausula .= " T013_Tipo, ";
        $cClausula .= " T013_D006_Id ";
        $cClausula .= " from T013  ";
        $cClausula .= " left join C004 on C004_Id=T013_C004_Id ";
        $cClausula .= " where T013_Id='$nT013_Id' ";
        $cSql_T013 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
        }
        $mT013 = mysql_fetch_array($cSql_T013);
        
        //-- VERIFICA SE A NOTA FISCAL PODE SER ALTERADA
        if ($mT013['T013_Flag_Travar_Nota'] == 'S') {
            return "ESSA NOTA JA FOI FINALIZADA!";
        }
        
/*         $cClausula = " select ";
        $cClausula .= " D018_Id, ";
        $cClausula .= " D018_UF ";
        $cClausula .= " from D024 ";
        $cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
        $cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
        $cClausula .= " where D024_Id='{$mT013['T013_D024_Id']}' ";
        $cSql_D024 = mysql_query("$cClausula");
        $mD024     = mysql_fetch_array($cSql_D024); */
        
        
/*         $cClausula = " select ";
        $cClausula .= " T224_C004_Id ";
        $cClausula .= " from T224  ";
        $cClausula .= " left join T224 on T224_Id=T225_T224_Id ";
        $cClausula .= " left join D009 on D009_Id=T225_D009_Id ";
        $cClausula .= " WHERE T225_C004_Id!='{$mT013['T013_C004_Id']}' ";
        $cSql_T086 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
        }
        
        if (mysql_num_rows($cSql_T086) > 0) {
            //return "EXISTEM ITENS QUE SAO DE OUTRAS EMPRESAS NESTA(S) O.C.(S)!";
        } */
        
        
        //-- BUSCA OS ITENS QUE SERAO ADICIONADOS NA NOTA
        // $cClausula  = " select ";
        // $cClausula .= " T225_D009_Id, ";
        // $cClausula .= " T224_Id, ";
        // $cClausula .= " T224_C004_Id, ";
        // $cClausula .= " T086_T225_Id, ";
        // $cClausula .= " T086_Id, ";
        // $cClausula .= " D001_Codigo_Produto, ";
        // $cClausula .= " D083_Codigo_Produto_Fornecedor, ";
        // $cClausula .= " D001_Descricao_Produto, ";
        // $cClausula .= " T225_Valor_Custo_Unitario, ";
        // $cClausula .= " T225_Aliquota_IPI, ";
        // $cClausula .= " T225_Aliquota_ICMS, ";
        // $cClausula .= " D005_Classificacao_Fiscal, ";
        // $cClausula .= " T086_Quantidade ";
        // $cClausula .= " from T086 ";
        // $cClausula .= " left join T225 on T225_Id=T086_T225_Id ";
        // $cClausula .= " left join T224 on T224_Id=T225_T224_Id ";
        // $cClausula .= " left join D009 on T225_D009_Id=D009_Id ";
        // $cClausula .= " left join D049 on D049_Id=D009_D049_Id ";
        // $cClausula .= " left join D001 on D049_D001_Id=D001_Id ";
        // $cClausula .= " left join D005 on D009_D005_Id(T225_D009_Id)=D005_Id ";
        // $cClausula .= " left join D083 on D049_Id=D083_D049_Id and D083_D024_Id='{$mT013['T013_D024_Id']}' ";
        // $cClausula .= " where T086_T013_Id='$nT013_Id' ";
        // $cClausula .= " and T086_Quantidade>0 ";
        // $cClausula .= " group by T225_Id";
        
        $extraNovo = gInsertExtraWhere($extra, "T225A_Quantidade_Recebida>0");
        
        $cClausula = " SELECT T225_D009_Id,
                              T225_Id,
                              T225A_Id,
                              T224_C004_Id,
                              T225_D009_Id,
                              T224_Id,
                              T225_D006_Id,
                              D001_Codigo_Produto,
                              D083_Codigo_Produto_Fornecedor,
                              D001_Descricao_Produto,
                              T225A_Quantidade_Recebida,
                              T225_Valor_Preco_Unitario,
                              D005_Classificacao_Fiscal,
                              T225_Aliquota_IPI,
                              T225_Aliquota_ICMS,
                              T225_Aliquota_ICMS_Diferenca_Aliquotas,
                              T225_Percentual_Reducao_ICMS,
                              T225_Aliquota_PIS,
                              T225_Aliquota_COFINS,
                              T225_ICMS_Inter_Estadual,
                              T225_ST_ICMS_Interno,
                              T225_ST_VA_Ajustada 
                         FROM T225 " . $extraNovo;
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
        }
        
        $cSql_T225 = mysql_query($cClausula);
        if (mysql_num_rows($cSql_T225) == 0) {
            return " * * * * Nenhum item foi selecionado * * * *";
        }

        if($mT013['T013_Tipo'] == 2){
            mysqli_query("UPDATE T013 SET T013_Flag_Calcular = 'N', T013_Flag_Validacoes = 'N' WHERE T013_Id = '{$nT013_Id}'");
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
            }
        }

        $arrayT224 = array();
        while ($mT225 = mysql_fetch_array($cSql_T225)) {
            /**
             * Se a nota veio de um XML, verifica se tem algum produto para importar que já tenha sido importado pelo XML e apenas associa.
             */
            
            $T014 = mysql_query("SELECT T014_Id,
                                        T014_Quantidade 
                                   FROM T014 
                                  WHERE T014_T013_Id = '{$nT013_Id}'
                                    AND T014_D009_Id = '{$mT225['T225_D009_Id']}'
                                    AND (T014_T225_Id <= 0 OR T014_T225_Id IS NULL)");
            $rows = mysql_num_rows($T014);
            
            if ($mT013['T013_Chave_Acesso_Nfe'] != "" && $rows > 0) {
                
                $quantidadeRecebida = $mT225['T225A_Quantidade_Recebida'];
                while($mT014 = mysql_fetch_array($T014)){

                    if($quantidadeRecebida <= 0){
                        continue;
                    }

                    mysql_query("UPDATE T014 SET T014_T225_Id          = '{$mT225['T225_Id']}', 
                                                 T014_Flag_Divergencia = 'N'
                                           WHERE T014_Id = '{$mT014['T014_Id']}'");
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
                    }

                    $quantidadeRecebida -= $mT014['T014_Quantidade'];
                }
                    
                mysqli_query("UPDATE T225A SET T225A_Quantidade_Recebida = 0 WHERE T225A_T225_Id='{$mT225['T225_Id']}'");
                mysqli_query("UPDATE T225 SET T225_Flag_Autorizado = 'S' WHERE T225_Id='{$mT225['T225_Id']}'");
                
            } else {
                
                //////////////////////////////////////////////////////////
                //-- FAZ A INCLUSAO DOS ITENS
                $cClausula = " insert into T014 ( ";
                $cClausula .= " T014_C004_Id, ";
                $cClausula .= " T014_T013_Id, ";
                $cClausula .= " T014_D006_Id, ";
                $cClausula .= " T014_D009_Id, ";
                $cClausula .= " T014_T225_Id, ";
                $cClausula .= " T014_Codigo_Produto, ";
                $cClausula .= " T014_Codigo_Produto_Fornecedor, ";
                $cClausula .= " T014_Descricao_Produto, ";
                $cClausula .= " T014_Quantidade, ";
                $cClausula .= " T014_Valor_Custo_Unitario, ";
                $cClausula .= " T014_Classificacao_Fiscal, ";
                $cClausula .= " T014_Unidade ";
               /* $cClausula .= " T014_Aliquota_IPI, ";
                $cClausula .= " T014_Aliquota_ICMS, ";
                $cClausula .= " T014_Aliquota_ICMS_Diferenca_Aliquotas, ";
                $cClausula .= " T014_Percentual_Reducao_ICMS, ";
                $cClausula .= " T014_Aliquota_PIS, ";
                $cClausula .= " T014_Aliquota_COFINS, ";
                $cClausula .= " T014_ICMS_Inter_Estadual, ";
                $cClausula .= " T014_Aliquota_ICMS_Interno, ";
                $cClausula .= " T014_MVA_Ajustada ";
                 $cClausula .= " T014_Valor_ICMS, ";
                $cClausula .= " T014_Valor_IPI, ";
                $cClausula .= " T014_Valor_ICMS_Substituicao, ";
                $cClausula .= " T014_Valor_PIS, ";
                $cClausula .= " T014_Valor_COFINS, ";
                $cClausula .= " T014_Valor_Base_ICMS_Substituicao, ";
                $cClausula .= " T014_Valor_Base_ICMS, ";
                $cClausula .= " T014_Valor_Base_IPI, ";
                $cClausula .= " T014_Valor_Base_PIS, ";
                $cClausula .= " T014_Valor_Base_COFINS ";    */         
                $cClausula .= " ) values ( ";
                $cClausula .= " '{$mT225['T224_C004_Id']}', ";
                $cClausula .= " '{$nT013_Id}', ";
                $cClausula .= " '{$mT013['T013_D006_Id']}', ";
                $cClausula .= " '{$mT225['T225_D009_Id']}', ";
                $cClausula .= " '{$mT225['T225_Id']}', ";
                $cClausula .= " '{$mT225['D001_Codigo_Produto']}', ";
                $cClausula .= " '{$mT225['D083_Codigo_Produto_Fornecedor']}', ";
                $cClausula .= " '{$mT225['D001_Descricao_Produto']}', ";
                $cClausula .= " '{$mT225['T225A_Quantidade_Recebida']}', ";
                $cClausula .= " '{$mT225['T225_Valor_Preco_Unitario']}', ";
                $cClausula .= " '{$mT225['D005_Classificacao_Fiscal']}', ";
                $cClausula .= " ' ' ";
                /*$cClausula .= " '{$mT225['T225_Aliquota_IPI']}', ";
                $cClausula .= " '{$mT225['T225_Aliquota_ICMS']}', ";
                $cClausula .= " '{$mT225['T225_Aliquota_ICMS_Diferenca_Aliquotas']}', ";
                $cClausula .= " '{$mT225['T225_Percentual_Reducao_ICMS']}', ";
                $cClausula .= " '{$mT225['T225_Aliquota_PIS']}', ";
                $cClausula .= " '{$mT225['T225_Aliquota_COFINS']}', ";
                $cClausula .= " '{$mT225['T225_ICMS_Inter_Estadual']}', ";
                $cClausula .= " '{$mT225['T225_ST_ICMS_Interno']}', ";
                $cClausula .= " '{$mT225['T225_ST_VA_Ajustada']}' ";                
                 $cClausula .= " '{$mT225['T225_Valor_ICMS']}', ";
                $cClausula .= " '{$mT225['T225_Valor_IPI']}', ";
                $cClausula .= " '{$mT225['T225_Valor_ICMS_Substituicao_Retencao']}', ";
                $cClausula .= " '{$mT225['T225_Valor_PIS']}', ";
                $cClausula .= " '{$mT225['T225_Valor_COFINS']}', ";
                $cClausula .= " '{$mT225['T225_Valor_Base_ICMS_Substituicao']}', ";
                $cClausula .= " '{$mT225['T225_Valor_Base_ICMS']}', ";
                $cClausula .= " '{$mT225['T225_Valor_Base_IPI']}', ";
                $cClausula .= " '{$mT225['T225_Valor_Base_PIS']}', ";
                $cClausula .= " '{$mT225['T225_Valor_Base_COFINS']}' ";   */     
                $cClausula .= " ) ";
                
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
                }
                $T014_Id = $g['mysqlLastId'];
               
                mysqli_query("UPDATE T225A SET T225A_Quantidade_Recebida=0 WHERE T225A_Id='{$mT225['T225A_Id']}'");
                mysqli_query("UPDATE T225 SET T225_Flag_Autorizado = 'S' WHERE T225_Id = '{$mT225['T225_Id']}'");
            }

            $arrayT224[] = $mT225['T224_Id'];


/*             $cClausula = mysql_query("SELECT T013_Flag_Travar_Nota,
                                             D009_Id,
                                             T014_Id,
                                             T013_Id,
                                             T014_Valor_Frete,
                                             T014_Valor_Despesas_Acessorias,
                                             T014_Valor_Total_Custo,
                                             T014_Quantidade,
                                             T014_Valor_Total_Custo_Final,
                                             T013_Valor_Frete,
                                             T013_Valor_Seguro,
                                             T013_Valor_Despesas_Acessorias,
                                             T014A_Flag_Destino_Produto,
                                             Informada.D006_Flag_Somar_Despesas_Base_IPI,
                                             Informada.D006_Flag_Ajustar_MVA,
                                             Informada.D006_Flag_Somar_IPI_Base_ICMS,
                                             Informada.D006_Flag_Somar_Fator_ICMS_Dif_Base,
                                             Informada.D006_Modalidade_ICMS_Substituicao,
                                             Informada.D006_Flag_Calcula_FCP_ST,
                                             Informada.D006_Flag_Forma_Calculo_COFINS,
                                             Informada.D006_Flag_Deduz_ICMS_Base_COFINS,
                                             Informada.D006_Flag_Forma_Calculo_PIS,
                                             Informada.D006_Flag_Deduz_ICMS_Base_PIS,
                                             D009_Preco_Pauta_ST,
                                             D053_Calculo_ST_Por_Dentro_Compra,
                                             T014_Valor_Total_Desconto,
                                             Informada.D006_Flag_Somar_Despesas_Base_PIS,
                                             Informada.D006_Flag_Somar_Despesas_Base_COFINS,
                                             T013_Flag_Calcular_ICMS_Despesas_Acessorias
                                        FROM T014
                                   LEFT JOIN T013 ON T013_Id = T014_T013_Id
                                   LEFT JOIN D009 ON D009_Id = T014_D009_Id  
                                   LEFT JOIN T014A ON T014A_T014_Id = T014_Id
                                   LEFT JOIN D006 as Informada ON Informada.D006_Id=T014_D006_Id
                                   LEFT JOIN D005 ON D005_Classificacao_Fiscal = T014_Classificacao_Fiscal
                                   LEFT JOIN D149 ON D149_D005_Id = D005_Id AND D149_C004_Id = '{$g['empresaAtual']}'
                                   LEFT JOIN C004 ON C004_Id = '{$g['empresaAtual']}'   
                                   LEFT JOIN D024 ON D024_Id = T013_D024_Id
                                   LEFT JOIN D053 ON D053_D018_Id = D024_D018_Id AND D053_C004_Id = T013_C004_Id
                                       WHERE T014_Id = '{$T014_Id}'");

            $cSql_T014  = mysql_fetch_array($cClausula);  
            $this->EST004AtualizarAliquotas($cSql_T014); */
                       
/*             mysql_query("call T014_Gravar_Totalizacao($T014_Id)");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
            } */   

        }

        foreach(array_unique($arrayT224) as $T224_Id){
            mysql_query("call T224_Gravar_Totalizacao_3({$T224_Id})");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
            }
        }

        //$this->EST004AtualizarCabecalhoNota(null, $nT013_Id);   

        mysql_query("COMMIT");
        
        return true;
    }
    
    /**
     * est004_alterar_quantidade
     *
     * @param Form $form
     * @param int $T012_Id
     * @param int $T013_Id
     * @return true/string
     */
    public function est004_alterar_quantidade($form, $T012_Id, $T013_Id)
    {
        global $g;
        $nT013_Id             = $T013_Id;
        $nT012_Id             = $T012_Id;
        $nQuantidade_Recebida = $form->campoValorEnviado('quantidade');
        $nQuantidade_Recebida = formata_valor_gravar($nQuantidade_Recebida, 4);
        
        if (empty($nT012_Id)) {
            return "Erro: T012_Id não recebido";
        }
        
        if (empty($nT013_Id)) {
            return "Erro: T013_Id não recebido";
        }
        
        if (empty($nQuantidade_Recebida)) {
            mysql_query("delete from T086 where T086_T013_Id='$nT013_Id' and T086_T012_Id='$nT012_Id'");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "ERRO: $cErro";
            }
        }
        
        //--PESQUISA O ID DA TABELA TEMPORARIA
        $cSql_T008 = mysql_query("select * from T086 where T086_T013_Id='$nT013_Id' and T086_T012_Id='$nT012_Id'");
        $mT086     = mysql_fetch_array($cSql_T008);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            return "Erro:" . $cErro;
        }
        
        
        $cSql_T012 = mysql_query("select T011_C004_Id,T012_D009_Id,T012_Quantidade_Falta_Lancar(T012_Id,'{$mT086['T086_Id']}') as T012_Quantidade from T012 left join T011 on T011_Id=T012_T011_Id where T012_Id='{$nT012_Id}'");
        $mT012     = mysql_fetch_array($cSql_T012);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            return "Erro:" . $cErro;
        }
        
        
        $cSql_T013 = mysql_query("select * 
		                        from T013 
		                       where T013_Id='$nT013_Id'");
        $mT013     = mysql_fetch_array($cSql_T013);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            return "Erro:" . $cErro;
        }
        
        //return "Quant Recebida: ".$nQuantidade_Recebida."<br /> outra: ".$mT012['T012_Quantidade'];
        if ($nQuantidade_Recebida > $mT012['T012_Quantidade']) {
            return "Quantidade Recebida não pode ser maior que a quantidade da Ordem de Compra";
        }
        
        mysql_query("update T012 set T012_Flag_Travar_Pedido='S' where T012_Id='{$nT012_Id}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro:" . $cErro;
        }
        
        if (empty($mT012['T012_D009_Id'])) {
            return "Item não cadastrado!<br />Para efetuar o lançamento deste item o mesmo deve estar cadastrado";
        }
        
        
        $cClausula = " select  ";
        $cClausula .= " * ";
        $cClausula .= " from T086 ";
        $cClausula .= " where T086_T013_Id='{$nT013_Id}' ";
        $cClausula .= " and T086_T012_Id='{$nT012_Id}' ";
        $cSql_T086 = mysql_query($cClausula);
        
        
        if (mysql_num_rows($cSql_T086) > 0) {
            $cClausula = " update T086 set ";
            $cClausula .= " T086_Quantidade='{$nQuantidade_Recebida}' ";
            $cClausula .= " where T086_Id='{$mT086['T086_Id']}' ";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "Erro:" . $cErro;
            }
            
        } else {
            
            $cClausula = " insert into T086 ( ";
            $cClausula .= " T086_T013_Id, ";
            $cClausula .= " T086_C004_Id, ";
            $cClausula .= " T086_T012_Id, ";
            $cClausula .= " T086_Classificacao_Fiscal, ";
            $cClausula .= " T086_Aliquota_IPI, ";
            $cClausula .= " T086_Quantidade ";
            $cClausula .= " ) values ( ";
            $cClausula .= " '{$nT013_Id}', ";
            $cClausula .= " '{$mT012['T011_C004_Id']}', ";
            $cClausula .= " '{$nT012_Id}', ";
            $cClausula .= " '{$T086_Classificacao_Fiscal}', ";
            $cClausula .= " '{$mT012['T012_Aliquota_IPI']}', ";
            $cClausula .= " '{$nQuantidade_Recebida}' ";
            $cClausula .= " )";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "Erro:" . $cErro;
            }
            
        }
        return true;
    }
    
    /**
     * est004_alterar_quantidade2
     *
     * @param Form $form
     * @param int $T012_Id
     * @return true/string
     */
    public function est004_alterar_quantidade2($form, $T012_Id)
    {
        global $g;
        $nT012_Id             = $T012_Id;
        $nQuantidade_Recebida = $form->campoValorEnviado('quantidade');
        
        $nQuantidade_Recebida = formata_valor_gravar($nQuantidade_Recebida, 4);
        
        if (empty($nT012_Id)) {
            return "Erro: T012_Id não recebido";
        }
        
        if (empty($nQuantidade_Recebida) || $nQuantidade_Recebida <= 0) {
            mysql_query("delete from T086 where (T086_T013_Id = '' or T086_T013_Id is null) and T086_T012_Id='$nT012_Id'");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "ERRO: $cErro";
            }
            return true;
        }
        
        //--PESQUISA O ID DA TABELA TEMPORARIA
        $cSql_T008 = mysql_query("select * from T086 where (T086_T013_Id = '' or T086_T013_Id is null) and T086_T012_Id='$nT012_Id'");
        $mT086     = mysql_fetch_array($cSql_T008);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            return "Erro:" . $cErro;
        }
        
        
        $cSql_T012 = mysql_query("select T011_C004_Id,T012_D009_Id,T012_Quantidade_Falta_Lancar(T012_Id,'{$mT086['T086_Id']}') as T012_Quantidade from T012 left join T011 on T011_Id=T012_T011_Id where T012_Id='{$nT012_Id}'");
        $mT012     = mysql_fetch_array($cSql_T012);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            return "Erro:" . $cErro;
        }
        
        //return "Quant Recebida: ".$nQuantidade_Recebida."<br /> outra: ".$mT012['T012_Quantidade'];
        if ($nQuantidade_Recebida > $mT012['T012_Quantidade']) {
            return "Quantidade Recebida não pode ser maior que a quantidade da Ordem de Compra";
        }
        
        mysql_query("update T012 set T012_Flag_Travar_Pedido='S' where T012_Id='{$nT012_Id}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro:" . $cErro;
        }
        
        if (empty($mT012['T012_D009_Id'])) {
            return "Item não cadastrado!<br />Para efetuar o lançamento deste item o mesmo deve estar cadastrado";
        }
        
        
        $cClausula = " select  ";
        $cClausula .= " * ";
        $cClausula .= " from T086 ";
        $cClausula .= " where (T086_T013_Id = '' or T086_T013_Id is null) ";
        $cClausula .= " and T086_T012_Id='{$nT012_Id}' ";
        $cSql_T086 = mysql_query($cClausula);
        
        
        if (mysql_num_rows($cSql_T086) > 0) {
            $cClausula = " update T086 set ";
            $cClausula .= " T086_Quantidade='{$nQuantidade_Recebida}' ";
            $cClausula .= " where T086_Id='{$mT086['T086_Id']}' ";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "Erro:" . $cErro;
            }
            
        } else {
            
            $cClausula = " insert into T086 ( ";
            $cClausula .= " T086_T013_Id, ";
            $cClausula .= " T086_C004_Id, ";
            $cClausula .= " T086_T012_Id, ";
            $cClausula .= " T086_Classificacao_Fiscal, ";
            $cClausula .= " T086_Aliquota_IPI, ";
            $cClausula .= " T086_Quantidade ";
            $cClausula .= " ) values ( ";
            $cClausula .= " '', ";
            $cClausula .= " '{$mT012['T011_C004_Id']}', ";
            $cClausula .= " '{$nT012_Id}', ";
            $cClausula .= " '{$T086_Classificacao_Fiscal}', ";
            $cClausula .= " '{$mT012['T012_Aliquota_IPI']}', ";
            $cClausula .= " '{$nQuantidade_Recebida}' ";
            $cClausula .= " )";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "Erro:" . $cErro;
            }
            
        }
        return true;
    }
    
    /**
     * est004VerificaItens
     *
     * @param string $extra
     * @return true/string
     */
    function est004VerificaItens($extra)
    {
        global $nLinhas;
        $extra   = base64_decode($extra);
        $extra   = gInsertExtraWhere($extra, '(T086_Quantidade > 0)');
        $sql     = <<<EOT
            SELECT T225_Id 
            FROM T225
            {$extra}
EOT;
        $result  = mysql_query($sql);
        $nLinhas = mysql_num_rows($result);
        if ($nLinhas > 0) {
            return true;
        } else {
            return "Não há itens para gerar a NF.";
        }
    }

    /**
     * EST004AtualizarEstoqueProdutos
     *
     * @param int $T013_Id
     * @return true/string
     */
    public function EST004AtualizarEstoqueProdutos($T013_Id)
    {
        global $g;
        
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $T014   = mysql_query("SELECT T014_D009_Id from T014 where T014_T013_Id='{$T013_Id}'");
        
        mysql_query("START TRANSACTION");
        mysql_query("SET AUTOCOMMIT=0");
        
        $erro  = "";
        $erros = 0;
        while ($mT014 = mysql_fetch_array($T014)) {
            
            // $retorno = $CAD002->D001_reprocessa_historico(false, $mT014['T014_D009_Id'],100);
            $retornoProcessa = $CAD002->D001_reprocessa_historico(0, $mT014['T014_D009_Id'],100);
            if($retornoProcessa !== true){
                registrarLog(array("D009", $mT014['T014_D009_Id'], $g['empresaAtual'], " Erro ao reprocessar histórico - EST004 - ATUALIZAR ESTOQUE PRODUTOS:", $retornoProcessa, "S",  3));
                $retorno = $CAD002->D001_reprocessa_historico(0, $mT014['T014_D009_Id']);

                if ($retorno !== true) {
                    mysql_query("ROLLBACK");
                    $erros++;
                    $erro .= "D009_Id: " . $mT014['T014_D009_Id'] . " Erro: " . $retorno . "<br />";
                }
			} 
            
        }
        
        if ($erros != 0) {
            return "<b>Houve os seguintes erros na atualização de estoque:</b><br /><br />" . $erro;
        } else {
            mysql_query("COMMIT");
            return true;
        }
        
    }
    
    /**
     * est004DefinirQuantidadeRecebidaOC
     *
     * @param int $T013_Id
     * @param string $extraSql
     */
    public function xxxest004DefinirQuantidadeRecebidaOC($T013_Id, $extraSql)
    {
        global $g;
        
        $extra = base64_decode($extraSql);
        
        $T012 = mysql_query("SELECT * FROM T012 " . $extra);
        
        while ($mT012 = mysql_fetch_array($T012)) {
            
            mysql_query("update T012 SET T012_Quantidade_Recebida=T012_Quantidade_Pendente_3('{$mT012['T012_Id']}') WHERE T012_Id='{$mT012['T012_Id']}'");
        }
        return true;
    }

    /**
     * est004DefinirQuantidadeRecebidaOC
     *
     * @param int $T013_Id
     * @param string $extraSql
     */
    public function est004DefinirQuantidadeRecebidaOC($T013_Id, $extraSql)
    {
        global $g;
        
        $extra = base64_decode($extraSql);
        
        $T225 = mysql_query("SELECT T225_Id,T225A_Id FROM T225 " . $extra);
        
        while ($mT225 = mysql_fetch_array($T225)) {
            $T225A = mysql_query("SELECT T225A_Id FROM T225A WHERE T225A_T225_Id = '{$mT225['T225_Id']}'");
            if(mysql_num_rows($T225A) <= 0){
                mysql_query("INSERT INTO T225A (T225A_T225_Id) VALUES ('{$mT225['T225_Id']}')");
                $mT225['T225A_Id'] = $g['mysqlLastId'];
            }
            
            mysql_query("UPDATE T225A SET T225A_Quantidade_Recebida=T225_Quantidade_Pendente_3('{$mT225['T225_Id']}') WHERE T225A_Id='{$mT225['T225A_Id']}'");
        }
        return true;
    }
    
    /**
     * est004DefinirQuantidadeRecebidaOCNovo
     *
     * @param int $T013_Id
     * @param string $extraSql
     */
    public function est004DefinirQuantidadeRecebidaOCNovo($T013_Id, $extraSql)
    {
        global $g;
        
        $extra = base64_decode($extraSql);
        
        $T225 = mysql_query("SELECT * FROM T225 " . $extra);
        
        while ($mT225 = mysql_fetch_array($T225)) {
            mysql_query("update T225 SET T225_Quantidade_Recebida=T225_Quantidade_Pendente_3('{$mT225['T225_Id']}') WHERE T225_Id='{$mT225['T225_Id']}'");
        }
        return true;
    }
    
    /**
     * est004DefinirQuantidadeRecebidaOS
     *
     * @param int $T013_Id
     * @param string $extraSql
     */
    public function est004DefinirQuantidadeRecebidaOS($T013_Id, $extraSql)
    {
        global $g;
        
        $extra = base64_decode($extraSql);
        
        $T129 = mysql_query("SELECT * FROM T129 " . $extra);
        
        while ($mT129 = mysql_fetch_array($T129)) {
            
            mysql_query("update T129 SET T129_Quantidade_Recebida=T129_Quantidade_Pendente_3('{$mT129['T129_Id']}') WHERE T129_Id='{$mT129['T129_Id']}'");
        }
        return true;
    }
    
    /**
     * est004LimparQuantidadeRecebidaOC
     *
     * @param int $T013_Id
     * @param string $extraSql
     */
    public function est004LimparQuantidadeRecebidaOC($T013_Id, $extraSql)
    {
        global $g;
        
        $extra = base64_decode($extraSql);
        
        $T225 = mysql_query("SELECT T225A_Id FROM T225 " . $extra);
        
        while ($mT225 = mysql_fetch_array($T225)) {
            
            mysql_query("update T225A SET T225A_Quantidade_Recebida=0 WHERE T225A_Id='{$mT225['T225A_Id']}'");
        }
        return true;
    }

        /**
     * est004LimparQuantidadeRecebidaOC
     *
     * @param int $T013_Id
     * @param string $extraSql
     */
    public function xxxxest004LimparQuantidadeRecebidaOC($T013_Id, $extraSql)
    {
        global $g;
        
        $extra = base64_decode($extraSql);
        
        $T012 = mysql_query("SELECT * FROM T012 " . $extra);
        
        while ($mT012 = mysql_fetch_array($T012)) {
            
            mysql_query("update T012 SET T012_Quantidade_Recebida=0 WHERE T012_Id='{$mT012['T012_Id']}'");
        }
        return true;
    }
    
    /**
     * est004LimparQuantidadeRecebidaOCNovo
     *
     * @param int $T013_Id
     * @param string $extraSql
     */
    public function est004LimparQuantidadeRecebidaOCNovo($T013_Id, $extraSql)
    {
        global $g;
        
        $extra = base64_decode($extraSql);
        
        $T225 = mysql_query("SELECT * FROM T225 " . $extra);
        
        while ($mT225 = mysql_fetch_array($T225)) {
            
            mysql_query("update T225 SET T225_Quantidade_Recebida=0 WHERE T225_Id='{$mT225['T225_Id']}'");
        }
        return true;
    }
    
    /**
     * est004LimparQuantidadeRecebidaOS
     *
     * @param int $T013_Id
     * @param string $extraSql
     */
    public function est004LimparQuantidadeRecebidaOS($T013_Id, $extraSql)
    {
        global $g;
        
        $extra = base64_decode($extraSql);
        
        $T129 = mysql_query("SELECT * FROM T129 " . $extra);
        
        while ($mT129 = mysql_fetch_array($T129)) {
            
            mysql_query("update T129 SET T129_Quantidade_Recebida=0 WHERE T129_Id='{$mT129['T129_Id']}'");
        }
        return true;
    }
    
    /**
     * est004AlterarQuantidadeRecebidaNF
     *
     * @param int $T013_Id
     * @param int $T008_Id
     * @param int $quantidade
     * @return true/string
     */
    public function est004AlterarQuantidadeRecebidaNF($T013_Id, $T008_Id, $quantidade)
    {
        if ($quantidade == 0) {
            mysql_query("DELETE FROM T086 WHERE T086_T013_Id = '{$T013_Id}' AND T086_T008_Id = '{$T008_Id}'");
            return true;
        }
        
        $sqlT086 = mysql_query("SELECT * FROM T086 WHERE T086_T013_Id = '{$T013_Id}' AND T086_T008_Id = '{$T008_Id}'");
        $T086    = mysql_fetch_array($sqlT086);
        
        $sqlT008 = mysql_query("SELECT *,T008_Quantidade_Falta_Lancar(T008_Id) as T008_Quantidade FROM T008 WHERE T008_Id = '{$T008_Id}'");
        $T008    = mysql_fetch_array($sqlT008);
        
        if ($quantidade > $T008['T008_Quantidade']) {
            mysql_query("UPDATE T086 SET T086_Quantidade = '' WHERE T086_Id = '{$T086['T086_Id']}'");
            return "Quantidade Recebida não pode ser maior que o saldo da nota de origem";
        }
        
        if (empty($T008['T008_D009_Id'])) {
            return "Item não cadastrado!<br />Para efetuar o lançamento deste item o mesmo deve estar cadastrado";
        }
        
        $sqlT086 = mysql_query("SELECT * FROM T086 WHERE T086_T013_Id='{$T013_Id}' AND T086_T008_Id='{$T008_Id}'");
        if (mysql_num_rows($sqlT086) > 0) {
            $T086 = mysql_fetch_array($sqlT086);
            mysql_query("UPDATE T086 SET T086_Quantidade = '{$quantidade}' WHERE T086_Id = '{$T086['T086_Id']}'");
            return true;
        } else {
            mysql_query("INSERT INTO T086 (T086_T013_Id, T086_T008_Id, T086_C004_Id, T086_Quantidade) VALUES ('{$T013_Id}', '{$T008_Id}', '{$T008['T008_C004_Id']}', '{$quantidade}')");
            return true;
        }
    }

    /**
     * est004AlterarQuantidadeRecebidaOC
     *
     * @param int $T225_Id
     * @param int $quantidade
     * @return true/string
     */
    public function est004AlterarQuantidadeRecebidaOC($T225_Id, $quantidade)
    {
        
        $sqlT086 = mysql_query("SELECT * FROM T086 WHERE T086_T225_Id = '{$T225_Id}'");
        $T086    = mysql_fetch_array($sqlT086);
        
        $T225  = mysql_query("SELECT *,
                                     T225_Quantidade_Pendente_3(T225_Id) as T225_Quantidade_Pendente 
                                FROM T225 
                               WHERE T225_Id = '{$T225_Id}'");

        $mT225 = mysql_fetch_array($T225);
        
        if ($quantidade > $mT225['T225_Quantidade_Pendente']) {
            mysql_query("UPDATE T086 SET T086_Quantidade = '0' WHERE T086_Id = '{$T086['T086_Id']}'");
            return "Quantidade Recebida não pode ser maior que o saldo da OC";
        }
        
        if (empty($mT225['T225_D009_Id'])) {
            return "Item não cadastrado!<br />Para efetuar o lançamento deste item o mesmo deve estar cadastrado";
        }
        
        $sqlT086 = mysql_query("SELECT * FROM T086 WHERE T086_T225_Id = '{$T225_Id}'");
        if (mysql_num_rows($sqlT086) > 0) {
            $T086 = mysql_fetch_array($sqlT086);
            mysql_query("UPDATE T086 SET T086_Quantidade = '{$quantidade}' WHERE T086_Id = '{$T086['T086_Id']}'");
            return true;
        } else {
            mysql_query("INSERT INTO T086 (T086_T225_Id, T086_C004_Id, T086_Quantidade) VALUES ('{$T225_Id}', '{$mT225['T225_C004_Id']}', '{$quantidade}')");
            return true;
        }
    }
    
    /**
     * est004IncluirItensNF
     *
     * @param int $T013_Id
     * @return true/string
     */
    public function est004IncluirItensNF($T013_Id)
    {
        global $g;
        
        $T013 = mysql_query("SELECT T013_D006_Id,
                                    T013_Tipo 
                               FROM T013 
                              WHERE T013_Id = '{$T013_Id}'");
                               
        $mT013 = mysql_fetch_array($T013); 
        
        $sqlT086 = mysql_query("SELECT * 
                                  FROM T086 
                             LEFT JOIN T008 ON T008_Id=T086_T008_Id
                                 WHERE T086_T013_Id = '{$T013_Id}'");

        if (mysql_num_rows($sqlT086) == 0) {
            return "Nenhum item foi selecionado!";
        }

        if($mT013['T013_Tipo'] == 2){
            mysqli_query("UPDATE T013 SET T013_Flag_Calcular = 'N', T013_Flag_Validacoes = 'N' WHERE T013_Id = '{$T013_Id}'");
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro:" . $cErro;
            }
        }

        while ($T086 = mysql_fetch_array($sqlT086)) {

            $T086['T008_Descricao_Produto'] = addslashes($T086['T008_Descricao_Produto']);
            $T086['T008_Codigo_Produto']    = addslashes($T086['T008_Codigo_Produto']);
            
            if (empty($T086['T008_Classificacao_Fiscal'])) {

                $sqlD005 = mysql_query("SELECT D005_Classificacao_Fiscal
										  FROM D009
									 LEFT JOIN D001 ON D001_Id=D009_D001_Id
									 LEFT JOIN D002 ON D002_Id=D001_D002_Id
									 LEFT JOIN T025 ON T025_D002_Id=D002_Id AND T025_D015_Id=D001_D015_Id
									 LEFT JOIN D005 ON D005_Id=T025_D005_Id
										 WHERE D009_Id='{$T086['T008_D009_Id']}'");
                $D005 = mysql_fetch_array($sqlD005);
                $T086['T008_Classificacao_Fiscal'] = $D005['D005_Classificacao_Fiscal'];
            }

            mysql_query("INSERT INTO T014 (T014_T013_Id,
									       T014_T008_Id,
									       T014_D009_Id,
									       T014_C004_Id,
									       T014_D005_Id,
                                           T014_D006_Id,
									       T014_Quantidade,
									       T014_Valor_Custo_Unitario,
									       T014_Valor_Unitario_Desp_Nota_Origem,
									       T014_Aliquota_IPI,
									       T014_Percentual_Reducao_IPI,
									       T014_Aliquota_ICMS,
									       T014_Percentual_Reducao_ICMS,
									       T014_Descricao_Produto,
									       T014_Codigo_Produto,
									       T014_Classificacao_Fiscal
								 ) VALUES (
									       '{$T013_Id}',
									       '{$T086['T008_Id']}',
									       '{$T086['T008_D009_Id']}',
									       '{$T086['T008_C004_Id']}',
									       '{$T086['T008_D005_Id']}',
                                           '{$mT013['T013_D006_Id']}',
									       '{$T086['T086_Quantidade']}',
									       '{$T086['T008_Valor_Preco_Unitario']}',
									       '{$T086['T008_Valor_Despesas_Unitario']}',
									       '{$T086['T008_Aliquota_IPI']}',
									       '{$T086['T008_Percentual_Reducao_IPI']}',
									       '{$T086['T008_Aliquota_ICMS']}',
									       '{$T086['T008_Percentual_Reducao_ICMS']}',
									       '{$T086['T008_Descricao_Produto']}',
									       '{$T086['T008_Codigo_Produto']}',
									       '{$T086['T008_Classificacao_Fiscal']}')");

            $T014_Id = $g['mysqlLastId'];            
            mysql_query("CALL T014_Gravar_Totalizacao('{$T014_Id}')");
        }

        mysql_query("DELETE FROM T086 WHERE T086_T013_Id = '{$T013_Id}'");

        return true;
    }
    
    /**
     * EST004CancelarNotaEntrada
     *
     * @param From form
     * @param int $T013_Id
     * @return true/string
     */
    public function EST004CancelarNotaEntrada($form, $T013_Id)
    {
        
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        
        if (empty($T013_Id)) {
            return "T013_Id não recebido. Por favor, contate o suporte técnico.";
        }
        
        $motivo_Cancelamento = $form->campoValorEnviado('Motivo_Cancelamento');
        
        if (empty($motivo_Cancelamento)) {
            return "Preencha o motivo de cancelamento!";
        }
        
        $mMensagem_Erro = "";
        $cSql_T013      = mysql_query("select T013_Id from T013 where T013_Id='$T013_Id' and T013_Flag_Travar_Nota='N'");
        if (mysql_num_rows($cSql_T013) > 0) {
            $mMensagem_Erro .= "* Nota ainda não foi finalizada<br />";
        }
        
        $cSql_T013 = mysql_query("select T013_Id from T013 where T013_Id='$T013_Id' and T013_Flag_Cancelada='S'");
        if (mysql_num_rows($cSql_T013) > 0) {
            $mMensagem_Erro .= "* Essa nota fiscal ja foi cancelada<br />";
        }
        
        $cSql_T002 = mysql_query("select T002_Id from T002 where T002_T013_Id='$T013_Id' and T002_Data_Recebimento!='0000-00-00';");
        if (mysql_num_rows($cSql_T002) > 0) {
            $mMensagem_Erro .= "* Existem titulos a receber baixados!<br />";
        }
        
        $cSql_T015 = mysql_query("select T015_Id from T015 where T015_T013_Id='$T013_Id' and T015_Data_Pagamento!='0000-00-00' and T015_Flag_Cancelada!='S';");
        if (mysql_num_rows($cSql_T015) > 0) {
            $mMensagem_Erro .= "* Existem titulos a pagar baixados!<br />";
        }

        $cSql_T015 = mysql_query("select T015_Id from T015 where T015_T013_Id='$T013_Id' and T015_T015_Id_Agrupado>0 and T015_Flag_Cancelada!='N'");
        if (mysql_num_rows($cSql_T015) > 0) {
            $mMensagem_Erro .= "* Existem titulos a pagar reparcelados, favor excluir o reparcelamento e tente novamente!<br />";
        }
        
        $cSql_T013 = mysql_query("select * from T013 where T013_Id='$T013_Id'");
        $mT013     = sql_fetch_array($cSql_T013);
        
        if (!empty($mMensagem_Erro)) {
            return $mMensagem_Erro;
        }
        
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        // Retorna as ordens de compra caso estejam com status finalizado para status aprovado
        $cClausula = " select ";
        $cClausula .= " T225_T224_Id as T224_Id";
        $cClausula .= " from T014 ";
        $cClausula .= " left join T225 on T225_Id = T014_T225_Id ";
        $cClausula .= " where T014_T013_Id='$T013_Id' ";
        $cClausula .= " group by T225_T224_Id ";
        $T224      = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro/p/p$cClausula";
        }

        while ($mT224 = mysql_fetch_array($T224)) {
                mysql_query("update T224 set T224_Flag_Status='3', T224_Nome_Status=T224_Status_Pedido(3,T224_Id) where T224_Id='{$mT224['T224_Id']}'");
        }
        
        //-- Cancelando Nota Fiscal
        mysql_query("update T013 set T013_Flag_Cancelada='S',
			T013_Flag_Classificacao=0,
			T013_Observacao_Cancelamento='$motivo_Cancelamento',
			T013_Valor_Total=0
			where T013_Id='{$T013_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "1. Erro: " . $erro;
        }
        
        //-- Limpa o Flag de Classificacao dos itens 
        $sql  = mysql_query("update T014 set T014_Flag_Classificacao=0, T014_Flag_Cancelada='S', T014_T008_Id=0, T014_T225_Id=0 where T014_T013_Id='{$T013_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "2. Erro: " . $erro;
        }
        
        //-- Cancelando Contas a Receber 
        mysql_query("update T002 set T002_Flag_Cancelada='S',
			T002_Data_Recebimento=current_date(), 
			T002_Valor_Duplicata=0,
			T002_Valor_Total=0
			where T002_T013_Id='{$T013_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "3. Erro: " . $erro;
        }
        
        //-- Cancelando Contas a Pagar
        mysql_query("update T015 set T015_Flag_Cancelada='S',
			T015_Data_Pagamento=current_date(),
			T015_Valor_Duplicata=0,
			T015_Valor_Total=0
			where T015_T013_Id='{$T013_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "4. Erro: " . $erro;
        }
        
        
        $cSql = mysql_query("update T001 set T001_Flag_Operacao='C',T001_Valor_Custo_Medio_Unitario=0 where T001_T013_Id='{$T013_Id}'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "5. Erro: " . $erro;
        }
        
        $cSql_T014 = mysql_query("select T014_D009_Id
			from T014 
			where T014_T013_Id='{$T013_Id}'");
        
        while ($mT014 = mysql_fetch_array($cSql_T014)) {
            
            // $retorno = $CAD002->D001_reprocessa_historico(false, $mT014['T014_D009_Id'],500);
            $retornoProcessa = $CAD002->D001_reprocessa_historico(0, $mT014['T014_D009_Id'],500);
            if($retornoProcessa !== true){
                registrarLog(array("D009", $mT014['T014_D009_Id'], $g['empresaAtual'], " Erro ao reprocessar histórico - EST004 - CANCELAR NOTA ENTRADA:", $retornoProcessa, "S",  3));
                $retorno = $CAD002->D001_reprocessa_historico(0, $mT014['T014_D009_Id']);

                if ($retorno != true) {
                    mysql_query("ROLLBACK");
                    return "Erro no reprocessa histórico.<br />Erro: " . $retorno;
                }
            }
        }
        mysql_query("update T072
			left join T013 on T013_Id=T072_T013_Id
			set T072_Flag_Atualizado='S'
			where T013_Flag_Cancelada='S'");
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return "6. Erro: " . $erro;
        }
        
        mysql_query("COMMIT");

        // Função personalizada conforme a necessidade
        $this->acoesPosCancelamentoNotaEntrada($T013_Id);

        return true;
    }
    
    public function acoesPosCancelamentoNotaEntrada($T013_Id)
    {
        require_once("bibliotecas/webservices/cyberlog/integracaoWMS.php");
        $WMS = new WMS();

        if($WMS->isEnabled()) {
            $WMS->cancelarRecebimento($T013_Id);
        }

        return false;
    }

    /**
     * est004ReverterCancelamento
     *
     * @param int $T013_Id
     * @return true/string
     */
    public function est004ReverterCancelamento($T013_Id)
    {
        
        
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        
        if (empty($T013_Id)) {
            return "T013_Id não recebido. Por favor, contate o suporte técnico.";
        }
        
        // $mMensagem_Erro="";
        // $cSql_T013=mysql_query("select T013_Id from T013 where T013_Id='$T013_Id' and T013_Flag_Travar_Nota='N'");
        // if (mysql_num_rows($cSql_T013)>0) {
        // 	$mMensagem_Erro.="* Nota ainda não foi finalizada<br />";
        // }
        
        // $cSql_T013=mysql_query("select T013_Id from T013 where T013_Id='$T013_Id' and T013_Flag_Cancelada='S'");
        // if (mysql_num_rows($cSql_T013)>0) {
        // 	$mMensagem_Erro.="* Essa nota fiscal ja foi cancelada<br />";
        // }
        
        // $cSql_T002=mysql_query("select T002_Id from T002 where T002_T013_Id='$T013_Id' and T002_Data_Recebimento!='0000-00-00';");
        // if (mysql_num_rows($cSql_T002)>0) {
        // 	$mMensagem_Erro.="* Existem titulos a receber baixados!<br />";
        // }
        
        // $cSql_T015=mysql_query("select T015_Id from T015 where T015_T013_Id='$T013_Id' and T015_Data_Pagamento!='0000-00-00' and T015_Flag_Cancelada!='S';");
        // if (mysql_num_rows($cSql_T015)>0) {
        // 	$mMensagem_Erro.="* Existem titulos a pagar baixados!<br />";
        // }
        
        $cSql_T013 = mysql_query("SELECT SUM(T013_Valor_Total_Produtos + 
                                             T013_Valor_Frete + 
                                             T013_Valor_Seguro + 
                                             T013_Valor_Despesas_Acessorias + 
                                             T013_Valor_Total_IPI + 
                                             T013_Valor_Mao_Obra + 
                                             T013_Valor_ICMS_Substituicao) as T013_Valor_Total
                                        FROM T013 
                                       WHERE T013_Id='{$T013_Id}'");
        $mT013     = sql_fetch_array($cSql_T013);
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        //-- Reabrindo Nota Fiscal
        mysql_query("UPDATE T013 SET T013_Valor_Total                = '{$mT013['T013_Valor_Total']}',
                                     T013_Flag_Cancelada             = 'N',
                                     T013_Flag_Classificacao         = '',
                                     T013_Observacao_Cancelamento    = '',
                                     T013_Flag_Travar_Nota           = 'N',
                                     T013_Flag_Estoque_Finalizado    = 'N',
                                     T013_Data_Entrada_Estoque       = '0000-00-00',
                                     T013_Flag_Financeiro_Finalizado = 'N',
                                     T013_Data_Financeiro            = '0000-00-00'
                               WHERE T013_Id='{$T013_Id}'");

            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "1. Erro: " . $erro;
            }

            $cSql_T014 = mysql_query("SELECT T014_Id
                                        FROM T014 
                                       WHERE T014_T013_Id='{$T013_Id}'");
        
            while ($mT014 = mysql_fetch_array($cSql_T014)) {
                
                mysql_query("UPDATE T014 SET T014_Flag_Cancelada = 'N' WHERE T014_Id = '{$mT014['T014_Id']}'");
                mysql_query("T014_Gravar_Totalizacao('{$mT014['T014_Id']}')");
                mysql_query("T014_Gravar_Totalizacao_Custo('{$mT014['T014_Id']}')");

                mysql_query("DELETE FROM T001 WHERE T001_T014_Id='{$mT014['T014_Id']}'");
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: $cErro/p/p$cClausula";
                }

                mysql_query("DELETE FROM T289 WHERE T289_T014_Id='{$mT014['T014_Id']}'");
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: $cErro/p/p$cClausula";
                }                               
            }

            mysql_query("CALL T013_Gravar_Totalizacao('{$T013_Id}')");

                       
            mysql_query("DELETE FROM T015 WHERE T015_T013_Id='{$T013_Id}'");
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/p$cClausula";
            }        
            
            mysql_query("DELETE FROM T002 WHERE T002_T013_Id='{$T013_Id}'");
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro/p/p$cClausula";
            }    

            mysql_query("COMMIT"); 

             // ------
           /* mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            
            //-- Cancelando Nota Fiscal
            mysql_query("update T013 set T013_Flag_Cancelada='N',
                                         T013_Flag_Classificacao=1,
                                         T013_Observacao_Cancelamento=''
                                   where T013_Id='{$T013_Id}'");

            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "1. Erro: " . $erro;
            }
                
            //-- Limpa o Flag de Classificacao dos itens 
            $sql  = mysql_query("update T014 set T014_Flag_Classificacao=1 where T014_T013_Id='{$T013_Id}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "2. Erro: " . $erro;
            }
            
            //-- Cancelando Contas a Receber 
            mysql_query("update T002 set T002_Flag_Cancelada='N',
                                         T002_Data_Recebimento='', 
                                         T002_Valor_Duplicata=0,
                                         T002_Valor_Total=0
                                   where T002_T013_Id='{$T013_Id}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "3. Erro: " . $erro;
            }
            
            //-- Cancelando Contas a Pagar
            mysql_query("update T015 set T015_Flag_Cancelada='N',
                                         T015_Data_Pagamento='',
                                         T015_Valor_Duplicata=0,
                                         T015_Valor_Total=0
                                   where T015_T013_Id='{$T013_Id}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "4. Erro: " . $erro;
            }
            
            
            $cSql = mysql_query("update T001 set T001_Flag_Operacao='' where T001_T013_Id='{$T013_Id}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "5. Erro: " . $erro;
            }
            
            $cSql_T014 = mysql_query("select T014_D009_Id
                from T014 
                where T014_T013_Id='{$T013_Id}'");
            
            while ($mT014 = mysql_fetch_array($cSql_T014)) {
                
                mysql_query("T014_Gravar_Totalizacao('{$mT014['T014_Id']})");
                mysql_query("T014_Gravar_Totalizacao_Custo('{$mT014['T014_Id']})");
                
                // $retorno = $CAD002->D001_reprocessa_historico(false, $mT014['T014_D009_Id'],100);
                $retornoProcessa = $CAD002->D001_reprocessa_historico(false, $mT014['T014_D009_Id'],100);
                if($retornoProcessa !== true){
                    registrarLog(array("D009", $mT014['T014_D009_Id'], $g['empresaAtual'], " Erro ao reprocessar histórico - EST004 - CANCELAR NOTA ENTRADA:", $retornoProcessa, "S",  3));
                    $retorno = $CAD002->D001_reprocessa_historico(0, $mT014['T014_D009_Id']);
    
                    if ($retorno != true) {
                        mysql_query("ROLLBACK");
                        return "Erro no reprocessa histórico.<br />Erro: " . $retorno;
                    }
                }
                
            }
            mysql_query("CALL T013_Gravar_Totalizacao('{$T013_Id}')");
            
            mysql_query("UPDATE T072
                      LEFT JOIN T013 on T013_Id=T072_T013_Id
                            SET T072_Flag_Atualizado='S'
                          WHERE T013_Flag_Cancelada='N'");

            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return "6. Erro: " . $erro;
            }
            
            mysql_query("COMMIT"); */
    
        
        return true;
        
    }
    
    /**
     * est004ConcluirInclusaoItensOS
     *
     * @param int $T013_Id
     * @param string $extraSql
     * @return true/string
     */
    public function est004ConcluirInclusaoItensOS($T013_Id, $extraSql)
    {
        global $g;
        $nT013_Id = $T013_Id;
        
        $extra = base64_decode($extraSql);
        
        if (empty($nT013_Id)) {
            return 'T013_Id não recebido';
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        //-- CONSULTA DA NOTA FISCAL
        $cClausula = " select ";
        $cClausula .= " * ";
        $cClausula .= " from T013  ";
        $cClausula .= " left join C004 on C004_Id=T013_C004_Id ";
        $cClausula .= " where T013_Id='$nT013_Id' ";
        $cSql_T013 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
        }
        $mT013 = mysql_fetch_array($cSql_T013);
        
        //-- VERIFICA SE A NOTA FISCAL PODE SER ALTERADA
        if ($mT013['T013_Flag_Travar_Nota'] == 'S') {
            return "ESSA NOTA JA FOI FINALIZADA!";
        }
        
        $cClausula = " select ";
        $cClausula .= " D018_Id, ";
        $cClausula .= " D018_UF ";
        $cClausula .= " from D024 ";
        $cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
        $cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
        $cClausula .= " where D024_Id='{$mT013['T013_D024_Id']}' ";
        $cSql_D024 = mysql_query("$cClausula");
        $mD024     = mysql_fetch_array($cSql_D024);
        
        
        $cClausula = " select ";
        $cClausula .= " T128_C004_Id ";
        $cClausula .= " from T129  ";
        $cClausula .= " left join T128 on T128_Id=T129_T128_Id ";
        $cClausula .= " left join D009 on D009_Id=T129_D009_Id ";
        $cClausula .= " WHERE T129_C004_Id!='{$mT013['T013_C004_Id']}' ";
        $cSql_T086 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
        }
        
        if (mysql_num_rows($cSql_T086) > 0) {
            return "EXISTEM ITENS QUE SAO DE OUTRAS EMPRESAS NESTA(S) O.C.(S)!";
        }
        
        
        $extraNovo = gInsertExtraWhere($extra, "T129_Quantidade_Recebida>0");
        
        $cClausula = " SELECT * FROM T129 " . $extraNovo;
        
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
        }
        
        $cSql_T129 = mysql_query($cClausula);
        if (mysql_num_rows($cSql_T129) == 0) {
            return " * * * * Nenhum item foi selecionado * * * *";
        }
        
        while ($mT129 = mysql_fetch_array($cSql_T129)) {
            /**
             * Se a nota veio de um XML, verifica se tem algum produto para importar que já tenha sido importado pelo XML e apenas associa.
             */
            $T014 = mysql_query("SELECT * FROM T014 WHERE T014_D009_Id='{$mT129['T129_D009_Id']}' AND T014_T013_Id='$nT013_Id'");
            $rows = mysql_num_rows($T014);
            if ($mT013['T013_Chave_Acesso_Nfe'] != "" && $rows > 0) {
                $mT014 = mysql_fetch_array($T014);
                
                mysql_query("UPDATE T014 SET T014_T129_Id='{$mT129['T129_Id']}' WHERE T014_Id='{$mT014['T014_Id']}'");
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
                }
                $T014_Id = $mT014['T014_Id'];
                
                mysql_query("UPDATE T129 SET T129_Quantidade_Recebida=0 WHERE T129_Id='{$mT129['T129_Id']}'");
                
            } else {
                
                //////////////////////////////////////////////////////////
                //-- FAZ A INCLUSAO DOS ITENS
                $cClausula = " insert into T014 ( ";
                $cClausula .= " T014_C004_Id, ";
                $cClausula .= " T014_T013_Id, ";
                $cClausula .= " T014_D006_Id, ";
                $cClausula .= " T014_D009_Id, ";
                $cClausula .= " T014_T129_Id, ";
                $cClausula .= " T014_Codigo_Produto, ";
                $cClausula .= " T014_Codigo_Produto_Fornecedor, ";
                $cClausula .= " T014_Descricao_Produto, ";
                $cClausula .= " T014_Quantidade, ";
                $cClausula .= " T014_Valor_Custo_Unitario, ";
                $cClausula .= " T014_Aliquota_IPI, ";
                $cClausula .= " T014_Aliquota_ICMS, ";
                $cClausula .= " T014_Classificacao_Fiscal, ";
                $cClausula .= " T014_Unidade ";
                $cClausula .= " ) values ( ";
                $cClausula .= " '{$mT129['T128_C004_Id']}', ";
                $cClausula .= " '{$nT013_Id}', ";
                $cClausula .= " '{$mT013['T013_D006_Id']}', ";
                $cClausula .= " '{$mT129['T129_D009_Id']}', ";
                $cClausula .= " '{$mT129['T129_Id']}', ";
                $cClausula .= " '{$mT129['D001_Codigo_Produto']}', ";
                $cClausula .= " '{$mT129['D083_Codigo_Produto_Fornecedor']}', ";
                $cClausula .= " '{$mT129['D001_Descricao_Produto']}', ";
                $cClausula .= " '{$mT129['T129_Quantidade_Recebida']}', ";
                $cClausula .= " '{$mT129['T129_Valor_Custo_Unitario']}', ";
                $cClausula .= " '{$mT129['T129_Aliquota_IPI']}', ";
                $cClausula .= " '{$mT129['T129_Aliquota_ICMS']}', ";
                $cClausula .= " '{$mT129['D005_Classificacao_Fiscal']}', ";
                $cClausula .= " ' ' ";
                $cClausula .= " ) ";
                
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
                }
                $T014_Id = $g['mysqlLastId'];
                
                mysql_query("UPDATE T129 SET T129_Quantidade_Recebida=0 WHERE T129_Id='{$mT129['T129_Id']}'");
            }
            
            
            mysql_query("call T014_Gravar_Totalizacao('{$T014_Id}')");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
            }
            
            mysql_query("call T128_Gravar_Totalizacao('{$mT129['T128_Id']}')");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Clausula: " . $cClausula . "<br />Erro:" . $cErro;
            }
            
        }
        
        mysql_query("COMMIT");
        
        return true;
    }
    
    /**
     * est004ConcluirInclusaoItensNovo
     *
     * @param int $T013_Id
     * @param string $extraSql
     * @return true/string
     */
    public function est004ConcluirInclusaoItensNovo($T013_Id, $extraSql)
    {
        global $g;
        $extra = base64_decode($extraSql);
        
        if (empty($T013_Id)) {
            return 'T013_Id não recebido';
        }
        
        //-- CONSULTA DA NOTA FISCAL
        $T013 = mysql_query("select T013_Flag_Travar_Nota,
                                    T013_D024_Id,
                                    T013_C004_Id
                               from T013  
                          left join C004 on C004_Id=T013_C004_Id 
                              where T013_Id='$T013_Id'");

        $mT013 = mysql_query($T013);
        $erro     = mysql_error();
        if (!empty($erro)) {
            return $erro;
        }
        $mT013 = mysql_fetch_array($T013);
        
        //-- VERIFICA SE A NOTA FISCAL PODE SER ALTERADA
        if ($mT013['T013_Flag_Travar_Nota'] == 'S') {
            return "Nota fiscal já foi finalizada";
        }
        
        $D024 = mysql_query("select D018_Id,
                                    D018_UF
                               from D024
                          left join D020 on D020_Id=D024_D020_Id
                          left join D018 on D018_Id=D020_D018_Id
                              where D024_Id='{$mT013['T013_D024_Id']}'");
        $D024 = mysql_query($D024);
        $mD024     = mysql_fetch_array($D024);
        
        $T225 = mysql_query("select T224_C004_Id
                               from T013 
                          left join T014 on T014_T013_Id=T013_Id
                          left join T225 on T225_Id=T014_T225_Id
                          left join T224 on T224_Id=T225_T224_Id
                          left join D009 on D009_Id=T225_D009_Id
                              WHERE T013_Id='$T013_Id'
                                AND (T225_C004_Id!='{$mT013['T013_C004_Id']}'
                                 OR T224_C004_Id!='{$mT013['T013_C004_Id']}')");

        $mT225 = mysql_query($T225);
        $erro = mysql_error();
        if (!empty($erro)) {
            return $erro;
        }
        
        if (mysql_num_rows($T225) > 0) {
            return "Existem itens de outras empresas na oc, por favor avise o suporte técnico";
        }
        
        $extraNovo = gInsertExtraWhere($extra, "T225_Quantidade_Recebida>0");
        
        $T225 = mysql_query("SELECT T014_Id,
                                    T014_Valor_Total_Custo,
                                    T014_Valor_Total_Desconto,
                                    T014_Valor_Total_Preco_Recebida,
                                    T014_Valor_ICMS,
                                    T014_Valor_ICMS_Recebida,
                                    T014_Valor_IPI,
                                    T014_Valor_IPI_Recebida,
                                    T014_Valor_ICMS_Substituicao,
                                    T014_Valor_ICMS_Substituicao_Retencao_Recebida,
                                    T014_Valor_Custo_Final_Unitario,
                                    T014_Valor_Custo_Unitario_Recebida,
                                    D005_Classificacao_Fiscal,
                                    T014_Classificacao_Fiscal,
                                    T225_Flag_Autorizado,
                                    T014_Codigo_Produto,
                                    T225_Id,
                                    T225_D006_Id,
                                    T224_C004_Id,
                                    T225_D009_Id,
                                    D001_Codigo_Produto,
                                    D083_Codigo_Produto_Fornecedor,
                                    D001_Descricao_Produto,
                                    T225_Quantidade_Recebida,
                                    T225_Valor_Custo_Unitario,
                                    T225_Aliquota_IPI,
                                    T225_Aliquota_ICMS,
                                    T225_Aliquota_PIS,
                                    T225_Aliquota_COFINS,
                                    T225_Valor_Total_Custo,
                                    T225_Valor_Custo_Final_Unitario,
                                    T225_Valor_Total_Custo_Final,
                                    T225_Valor_Desconto_Unitario,
                                    T225_Valor_Total_Desconto,
                                    T225_D005_Id,
                                    T225_Valor_Frete_Unitario,
                                    T225_Valor_Despesas_Acessorias_Unitario,
                                    T225_Percentual_Reducao_ICMS,
                                    T225_Valor_Base_ICMS,
                                    T225_Valor_ICMS,
                                    T225_Valor_ICMS_Substituicao,
                                    T225_Percentual_Reducao_IPI,
                                    T225_Valor_Base_IPI,
                                    T225_Valor_IPI,
                                    T225_D037_Id,
                                    T225_Modalidade_ICMS_Substituicao,
                                    T225_Modalidade_ICMS,
                                    T225_ICMS_Inter_Estadual,
                                    T225_Situacao_Tributaria_IPI,
                                    T225_Valor_Base_PIS,
                                    T225_Valor_PIS,
                                    T225_Flag_Isento_PIS,
                                    T225_Valor_Base_ICMS_Substituicao,
                                    T225_Situacao_Tributaria_COFINS,
                                    T225_Flag_Situacao_Tributaria,
                                    T225_Flag_Isento_COFINS,
                                    T225_Valor_Base_COFINS,
                                    T225_Valor_COFINS,T224_Id,
                                    (SELECT SUM(T014_Valor_ICMS_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id WHERE T014B.T014_Id=T014.T014_Id OR T014B.T014_Id_Duplicado=T014.T014_Id OR T014B.T014_Id=T014.T014_Id_Duplicado OR T014B.T014_Id_Duplicado=T014.T014_Id_Duplicado) as T014_Valor_ICMS_Recebida,
                                    (SELECT SUM(T014_Valor_IPI_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id WHERE T014B.T014_Id=T014.T014_Id OR T014B.T014_Id_Duplicado=T014.T014_Id OR T014B.T014_Id=T014.T014_Id_Duplicado OR T014B.T014_Id_Duplicado=T014.T014_Id_Duplicado) as T014_Valor_IPI_Recebida,
                                    (SELECT SUM(T014_Valor_ICMS_Substituicao_Retencao_Recebida) FROM T225 T225B left join T014 T014B on T014B.T014_T225_Id=T225B.T225_Id WHERE T014B.T014_Id=T014.T014_Id OR T014B.T014_Id_Duplicado=T014.T014_Id OR T014B.T014_Id=T014.T014_Id_Duplicado OR T014B.T014_Id_Duplicado=T014.T014_Id_Duplicado) as T014_Valor_ICMS_Substituicao_Retencao_Recebida
                                    FROM T014
                                    {$extraNovo}");

        $erro = mysql_error();
        if (!empty($erro)) {
            return $erro;
        }
        if (mysql_num_rows($T225) <= 0) {
            return "Não existem itens vinculados da NF vinculados com a OC.";
        }
        
        $count = array(
            'ok' => 0,
            'erro' => 0
        );

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
      

        while ($mT225 = mysql_fetch_array($T225)) {
            /**
             * Se a nota veio de um XML, verifica se tem algum produto para importar que já tenha sido importado pelo XML e apenas associa.
             */
            //if($mT013['T013_Chave_Acesso_Nfe'] != ""){
            $difTotalPreco = abs($mT225['T014_Valor_Total_Custo'] - $mT225['T014_Valor_Total_Desconto'] - $mT225['T014_Valor_Total_Preco_Recebida']);
            $difTotalICMS  = abs($mT225['T014_Valor_ICMS'] - $mT225['T014_Valor_ICMS_Recebida']);
            $difTotalIPI   = abs($mT225['T014_Valor_IPI'] - $mT225['T014_Valor_IPI_Recebida']);
            $difTotalST    = abs($mT225['T014_Valor_ICMS_Substituicao'] - $mT225['T014_Valor_ICMS_Substituicao_Retencao_Recebida']);
            $difCusto      = abs($mT225['T014_Valor_Custo_Final_Unitario'] - $mT225['T014_Valor_Custo_Unitario_Recebida']);
            $difNCM        = '';
            if ($mT225['D005_Classificacao_Fiscal'] != $mT225['T014_Classificacao_Fiscal']) {
                $difNCM = 'NCM Divergente';
            }
            $toleranciaDiferencaOCNF = str_replace(",", ".", $g['C031']['toleranciaDiferencaOCNF']);
            
            if ($mT225['T225_Flag_Autorizado'] != 'S' and ($difTotalPreco > $toleranciaDiferencaOCNF or (($difTotalICMS > $toleranciaDiferencaOCNF or $difTotalIPI > $toleranciaDiferencaOCNF or $difTotalST > $toleranciaDiferencaOCNF or !empty($difNCM) or $difCusto > $toleranciaDiferencaOCNF) && $g['C031']['verificarImpostosNotaRecebida'] == 'S'))) {
                
                mysql_query("UPDATE T014 SET T014_Flag_Divergencia='S', T014_Observacao_Divergencia='Diferenca total preco: $difTotalPreco<br>Diferença total ICMS: $difTotalICMS<br>Diferença total IPI: $difTotalIPI<br>Diferença total ST: $difTotalST<br>Diferença total IPI: $difTotalIPI <br> $difNCM' WHERE T014_Id='{$mT225['T014_Id']}'");
                $count['erro']++;
                
            } else {
                
                mysql_query("UPDATE T014 SET T014_Flag_Divergencia='N',T014_Observacao_Divergencia='' WHERE T014_Id='{$mT225['T014_Id']}'");
                
                mysqli_query("UPDATE T225 SET T225_Quantidade_Recebida=0, T225_Flag_OC_Importada = 'S' WHERE T225_Id='{$mT225['T225_Id']}'");
                $count['ok']++;

            }
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return $erro;
            }
            
            mysql_query("call T014_Gravar_Totalizacao({$mT225['T014_Id']})");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return $erro;
            }
            
            mysql_query("call T224_Gravar_Totalizacao_3({$mT225['T224_Id']})");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return $erro;
            }
            
        }
        
        mysql_query("COMMIT");
        
        return $count;
    }
    
    /**
     * finalizarNotaCompras
     *
     * @param int $T013_Id
     * @return true/string
     */
    public function finalizarNotaCompras($T013_Id)
    {
        global $g;
        
        //-- Conex�o com o banco de dados
        require "old/lib/gravar_entidade.php3";
        
        //-- INCLUSAO DA CLASSE DE PROCESSAMENTOS
        require "old/lib/processamentos.class.php3";
        
        // Classe produtos 
        require_once("bibliotecas/classes/CAD002.php");
        
        $CAD002 = new CAD002();
        
        if (empty($T013_Id)) {
            return "Erro: T013_Id não encontrado.";
        }
        $cSql_T013 = mysql_query("select * 
                                    from T013 
                               left join D006 on D006_Id=T013_D006_Id
                               left join D024 on D024_Id=T013_D024_Id
                               left join C004 on D024_Id=C004_D024_Id                   
                                   where T013_Id='$T013_Id'");
        $mT013     = mysql_fetch_array($cSql_T013);

        if ($mT013['D024_Flag_Pre_Cadastro'] == 'S') {
            return "Cliente está em Pré Cadastro, complete o cadastro e tente novamente";
        }

        if($mT013['D006_Id'] <= 0 || !isset($mT013['D006_Id'])){
            return "Não foi selecionada a natureza da operação no cabeçalho da nota. Verifique!";
        }
        //Trocado valor da variável $g['empresaAtual']. Pois há empresas que vão administrar todas as notas de uma vez
        $g['empresaAtual'] = $mT013['T013_C004_Id'];
        
        //-- INSTANCIA CLASSE DE PROCESSAMENTOS
        $Pendencias = NEW \Processamentos($g['empresaAtual']);
        
        //echo "<script src='../func/funcoes_genericas.js'></script>";
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Atualizacao dos calculos dos itens
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $Msg       = "";
        $cSql_T014 = mysql_query("select T014_Id 
                                    from T014 
                                   where T014_T013_Id='{$T013_Id}' 
                                     and T014_Quantidade=0 AND T014_Valor_Custo_Unitario > 0
                                     and T014_Classificacao_Fiscal != '00000000'");

        if (mysql_num_rows($cSql_T014) > 0 && ($mT013['T013_Flag_Finalidade_NFe'] == '1' || $mT013['T013_Flag_Finalidade_NFe'] == '4')) {
            return "Existem ítens sem quantidade. Verfifique!";
        }
        
        $cSql_T014 = mysql_query("select T014_Id 
                                    from T014 
                               left join D009 ON D009_Id=T014_D009_Id 
                                   where T014_T013_Id='{$T013_Id}' 
                                     and D009_C004_Id!='{$g['empresaAtual']}'");

        if (mysql_num_rows($cSql_T014) > 0) {
            return 'Existem itens com o D009 incorreto. Por favor, avise o suporte técnico';
        }
        
        $cSql_T014 = mysql_query("select T014_Id 
                                    from T014 
                               left join D006 ON D006_Id=T014_D006_Id 
                                   where T014_T013_Id='{$T013_Id}' 
                                     and ifnull(T014_T225_Id,0)<=0 
                                     and T014_Valor_Custo_Unitario > 0 
                                     and T014_Classificacao_Fiscal != '00000000' 
                                     and (D006_Flag_Venda_Compra_Outros = 'C' OR D006_Flag_Venda_Compra_Outros = 'T')");

        if (mysql_num_rows($cSql_T014) > 0 && ($mT013['D006_Flag_Venda_Compra_Outros'] == 'C' || $mT013['D006_Flag_Venda_Compra_Outros'] == 'T') && $mT013['T013_Flag_Obrigatorio_OC'] == 'S' && ($mT013['T013_Flag_Finalidade_NFe'] == '1' || $mT013['T013_Flag_Finalidade_NFe'] == '4')) {
            
            if($mT013['D006_Flag_Venda_Compra_Outros'] == 'C'){
                return "Nota fiscal de compra e existem itens não vinculados a ordem de compra, verifique!";
            } elseif($mT013['D006_Flag_Venda_Compra_Outros'] == 'T' && $mT013['C004_D024_Id'] > 0){
                return "Nota de transferência entre empresas e existem itens não vinculados a ordem de compra, verifique!";
            }
        }

        $cSql_T014 = mysql_query("select T014_Id
                                    from T014
                               left join T225 on T225_Id=T014_T225_Id
                               left join D006 on D006_Id=T014_D006_Id
                                   where T014_T013_Id='$T013_Id' and D006_Flag_Venda_Compra_Outros='C' and T014_Flag_Divergencia='S' and T225_Flag_Autorizado!='S'");
                                   
        if (mysql_num_rows($cSql_T014) > 0) {
            return "Existem ítens com divergência e sem autorização. Verfifique!";
        }
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        ////-- Verificando se existem itens em pré-cadsastro
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $T014 = mysql_query("select D001_Flag_Pre_Cadastro
                               from T014
                          left join D009 ON D009_Id=T014_D009_Id
                          left join D049 ON D049_Id=D009_D049_Id
                          left join D001 ON D001_Id=D049_D001_Id
                              where T014_T013_Id='{$T013_Id}' 
                                and D001_Flag_Pre_Cadastro='S';
            ");
        if (mysql_num_Rows($T014) > 0) {
            return "Existem itens em PRÉ-CADASTRO. Complete o cadastro e tente novamente.";
        }
        
        $D048_Id = 1;
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Atualizacao dos calculos dos itens
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $Msg       = "";
        $cSql_T014 = mysql_query("select T014_Id,
                                         T014_Aliquota_IPI,
                                         T014_Classificacao_Fiscal,
                                         T014_Codigo_Produto
                                    from T014
                                   where T014_T013_Id='$T013_Id'
                                     and T014_C004_Id='{$g['empresaAtual']}'
                                     and T014_Quantidade>0");
        
        $nTotal_Itens_Nota = mysql_num_rows($cSql_T014);
        
        while ($mT014 = mysql_fetch_array($cSql_T014)) {
            //    sql_query("call T014_Gravar_Totalizacao($mT014[T014_Id])");
            if (($mT014['T014_Aliquota_IPI'] > 0) and (empty($mT014['T014_Classificacao_Fiscal']))) {
                //$Msg.="/pItem: $mT014[T014_Codigo_Produto]";
            }
            //if ( ( $mT014[T014_Aliquota_IPI]==0 ) and ( !empty($mT014[T014_Classificacao_Fiscal] ) ) ) {
            //$Msg.="/pItem: $mT014[T014_Codigo_Produto]";
            //}
        }
        if (!empty($Msg)) {
            return "Os itens abaixo não possuem Classifição/IPI/p" . $Msg;
        }
        
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Verifica��o dos Valores do cabecalho
        ////////////////////////////////////////////////////////////////////////////////////////////////
        if ($mT013['T013_Valor_Dolar'] <= 0) {
            //   msg_alerta("Dolar n�o encontrado!");
            //   exit;
        }
        if ($mT013['T013_C004_Id'] != $g['empresaAtual']) {
            return "A Empresa que esta selecionada não é a mesma da nota fiscal. <br />Selecione a empresa correta: {$mT013['T013_C004_Id']}";
            exit;
        }
        
        If ($mT013['D006_Id'] <= 0) {
            return "Informe a CFOP no cabeçalho da NF";
        }
        $cSql_T014 = mysql_query("select T014_D006_Id
                                    from T014
                                   where T014_T013_Id='$T013_Id'
                                     and ifnull(T014_D006_Id,0)<=0");

        if (mysql_num_rows($cSql_T014) > 0) {
            return "Existe itens sem CFOP, verifique.";
        }
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Verifica se o item esta cadastrado e se a natureza gera estoque
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $cSql_T014 = mysql_query("select T014_Id
                                    from T014
                               left join D006 on D006_Id=T014_D006_Id        
                                   where T014_T013_Id='$T013_Id'
                                     and T014_C004_Id='{$g['empresaAtual']}'
                                     and T014_D009_Id=0
                                     and T014_Quantidade>0
                                     and D006_Flag_Estoque!='N'");
        if (mysql_num_rows($cSql_T014) > 0) {
            return "Devido a natureza da operação estar definida para movimentar o estoque e existirem itens sem cadastro, <br />você não podera finalizar esta nota, cadastre os itens e tente novamente!";
        }
        
        if ($mT013['T013_Flag_Travar_Nota'] == 'S') {
            
            return "Nota Fiscal já foi finalizada, não pode ser finalizada novamente!";
        }

        // T013_Tipo 6 -  Nota Fiscal/Conta de Energia Elétrica (Código 06)
        if(($mT013['T013_Tipo'] == '6' || $mT013['T013_Tipo'] == '11') && (empty($mT013['T013_Classe_Consumo']) || empty($mT013['T013_Tipo_Ligacao']) || empty($mT013['T013_Grupo_Tensao']))) {
            return "Os campos Classe de Consumo, Tipo de Ligação e Grupo de Tensão na aba Outros, devem ser preenchidos quando for uma Nota Fiscal/Conta de Energia Elétrica";
        }
        // T013_Tipo 7 e 8 - NFS de Comunicação/Telecomunicação
        if(($mT013['T013_Tipo'] == '7' || $mT013['T013_Tipo'] == '8')  && (empty($mT013['T013_Tipo_Assinante']))) {
            return "O campo Tipo Assinante na aba Outros, deve ser preenchido quando for uma Nota Fiscal de Serviço de Comunicação ou Telecomunicação";
        }

        //totalizações agora serão executadas no content em etapas - Lígia Nakazato
       // mysql_query("call T013_Gravar_Totalizacao('$T013_Id')");
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE FORAM SELECIONADOS ITENS DE SUBS.TRIBUTARIA
        ////////////////////////////////////////////////////////////////////////////////////////////////
        /*
        if ($mT013['D006_Flag_Substituicao_Tributaria'] == 'S') {
            $cSql_T014 = mysql_query("select T014_Id
                                    from T014
                                left join T013 on T013_Id=T014_T013_Id
                                left join D006 on D006_Id=T013_D006_Id
                                    where T014_T013_Id='$T013_Id'
                                        and T014_C004_Id='{$g['empresaAtual']}'
                                        and (SELECT D067_Simbolo
                                                FROM D009
                                            LEFT JOIN D049 ON D049_Id=D009_D049_Id 
                                            LEFT JOIN D001 ON D001_Id=D049_D001_Id 
                                            LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id 
                                            LEFT JOIN D068 ON D068_D005_Id=T025_D005_Id
                                            LEFT JOIN D067 ON D067_Id=D068_D067_Id AND D068_C004_Id=D009_C004_Id
                                                WHERE D009_Id=69316
                                                AND D068_C004_Id=D009_C004_Id
                                                LIMIT 1)!='ST'");
            if (mysql_num_rows($cSql_T014) > 0) {
                //      msg_alerta("AVISO! A CFOP DESTA NOTA � SUBS.TRIB. E EXISTEM ITENS QUE NAO S�O SUBS.TRIB./pVERIFIQUE!!!");
                
            }
            
        } else if ($mT013['D006_Flag_Devolucao'] != 'S') {
            if ($mT013['D006_Flag_Substituicao_Tributaria'] == 'S') {
                $cSql_T014 = mysql_query("select T014_Id
                                        from T014
                                    left join D009 on D009_Id=T014_D009_Id
                                    left join D049 on D049_Id=D009_D049_Id                            
                                        where T014_T013_Id='$T013_Id'
                                            and T014_C004_Id='{$g['empresaAtual']}'
                                            and D049_Flag_Substituicao_Tributaria!='S'");
                if (mysql_num_rows($cSql_T014) > 0) {
                    return "AVISO! A CFOP DESTA NOTA É SUBS.TRIB. E EXISTEM ITENS QUE NAO S�O SUBS.TRIB. <br />VERIFIQUE!!!";
                }
            } else {
                
                $cSql_T014 = mysql_query("select T014_Id
                                        from T014
                                left join D009 on D009_Id=T014_D009_Id
                                left join D049 on D049_Id=D009_D049_Id                            
                                    where T014_T013_Id='$T013_Id'
                                        and T014_C004_Id='{$g['empresaAtual']}'
                                        and D049_Flag_Substituicao_Tributaria='S'");
                if (mysql_num_rows($cSql_T014) > 0) {
                    return "AVISO! EXISTEM ITENS QUE SÃO SUBS.TRIB. E A CFOP DESTA NOTA NÃO É SUBS.TRIB. <br />VERIFIQUE!!!";
                    
                }
            }
        }
        */
        
        $Total_Geral = 0;
        $Total_Geral += $mT013['T013_Valor_Total_Produtos'];
        
        if (($mT013['T013_Numero_Conhecimento'] == 'NAO TEM') and ($mT013['T013_Valor_Frete'] > 0)) {
            $Total_Geral += $mT013['T013_Valor_Frete'];
            $Msg_Erro = "Valor Produtos + Valor Frete + Valor Seguro + Valor IPI + Despesas + Mao Obra + ST - Desconto";
        } else {
            $Msg_Erro = "Valor Produtos + Valor Seguro + Valor IPI + Despesas + Mao Obra + ST - Desconto";
        }
        
        $Total_Geral += $mT013['T013_Valor_Seguro'];
        //$Total_Geral+=$mT013['T013_Valor_Embalagem'];
        $Total_Geral += $mT013['T013_Valor_Total_IPI'];
        $Total_Geral += $mT013['T013_Valor_Mao_Obra'];
        $Total_Geral += $mT013['T013_Valor_Despesas_Acessorias'];
        $Total_Geral += $mT013['T013_Valor_ICMS_Substituicao'];
        $Total_Geral -= $mT013['T013_Valor_Desconto'];
        $Total_Geral += $mT013['T013_Valor_Total_FCP_ST'];

        //$Total_Geral-=$mT013['T013_Valor_Total_ICMS'];
        $mensagem = "<b><span style='color:red'>Verifique as divergencias! </span></b><br /><br />Na aba Princial, o campo Valor Total Nota fiscal " . number_format($mT013['T013_Valor_Total'], 2, ',', '.') . " <br/>é diferente do somatório dos valores dos produtos:<br/><br/>
                
                <table>
                    <tr>
                        <td>Valor Produtos<td>
                        <td> " .  number_format($mT013['T013_Valor_Total_Produtos'], 2, ',', '.') . "<td>
                    </tr>
                    <tr>
                        <td>Valor Frete<td>
                        <td> +" . number_format($mT013['T013_Valor_Frete'], 2, ',', '.') . "<td>
                    </tr>
                    <tr>
                        <td>Valor Seguro<td>
                        <td>+" . number_format($mT013['T013_Valor_Seguro'], 2, ',', '.') . "<td>
                    </tr>
                    <tr>
                        <td>Valor IPI<td>
                        <td>+" . number_format($mT013['T013_Valor_Total_IPI'], 2, ',', '.') . "<td>
                    </tr>
                    <tr>
                        <td>Valor Mão de Obra<td>
                        <td>+" . number_format($mT013['T013_Valor_Mao_Obra'], 2, ',', '.') . "<td>
                    </tr>
                    <tr>
                        <td>Valor Despesas<td>
                        <td>+" . number_format($mT013['T013_Valor_Despesas_Acessorias'], 2, ',', '.') . "<td>
                    </tr>
                    <tr>
                        <td>Valor ICMS ST<td>
                        <td>+" . number_format($mT013['T013_Valor_ICMS_Substituicao'], 2, ',', '.') . "<td>
                    </tr>
                        <td>Valor FCP ST<td>
                        <td>+" . number_format($mT013['T013_Valor_Total_FCP_ST'], 2, ',', '.') . "<td>
                    </tr>
                    <tr>
                        <td>Valor Desconto<td>
                        <td>-" . number_format($mT013['T013_Valor_Desconto'], 2, ',', '.') . "<td>
                    </tr>
                    <tr>
                        <td>Valor Calculado <td>
                        <td>=" .  number_format($Total_Geral, 2, ',', '.') . "<td>
                    </tr>
                </table>";
        if ((number_format($mT013['T013_Valor_Total'], 0) != number_format($Total_Geral, 0)) and $mT013['T013_Flag_Validacoes'] == 'S') {
            //return "<b><span style='color:red'>Valores Divergentes </span></b><br /><br />Total da Nota é diferente de: <br />Valor Produtos + Valor Frete + Valor Seguro + Valor IPI + Despesas - Desconto <br />Valor digitado: " . number_format($mT013['T013_Valor_Total'], 2, ',', '.') . " Valor Apurado: " . number_format($Total_Geral, 2, ',', '.');
            return $mensagem;
        }
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Verifica o custo
        ////////////////////////////////////////////////////////////////////////////////////////////////
        /*
        $T014Custo=mysql_query("SELECT * FROM T014 
        LEFT JOIN D006 ON D006_Id=T014_D006_Id 
        LEFT JOIN D009 ON D009_Id=T014_D009_Id
        LEFT JOIN D049 ON D049_Id=D009_D049_Id
        LEFT JOIN D001 ON D001_Id=D049_D001_Id
        WHERE T014_T013_Id='{$T013_Id}'");
        $validacaoCusto="";
        while($mT014Custo=mysql_fetch_array($T014Custo)){
        
        if($mT014Custo['D006_Flag_Atualiza_Custo']=="S"){
        
        // SQL que calcula o custo
        // T014_Valor_Custo_Final_Unitario = ((T014_Valor_Total_Custo + IF(xFlag_Substituicao_Tributaria='S',T014_Valor_ICMS_Substituicao,0) + T014_Valor_Frete + IF(xFlag_Credita_IPI='N',T014_Valor_IPI,0) + T014_Valor_Seguro + T014_Valor_Despesas_Acessorias - IF(xFlag_Deb_Crd_ICMS='S', T014_Valor_ICMS, 0)) - T014_Valor_Total_Desconto)/T014_Quantidade,
        // T014_Valor_Total_Custo_Final    = T014_Valor_Custo_Final_Unitario * T014_Quantidade
        $mT014Custo['T014_Valor_ICMS_Substituicao'] = $mT014Custo['D006_Flag_Substituicao_Tributaria']=='S' ? $mT014Custo['T014_Valor_ICMS_Substituicao'] : 0.00;
        $mT014Custo['T014_Valor_IPI']               = $mT014Custo['D006_Flag_Credita_IPI']=='N'             ? $mT014Custo['T014_Valor_IPI'] : 0.00;
        $mT014Custo['T014_Valor_ICMS']              = $mT014Custo['D006_Flag_Deb_Crd_ICMS']=='S'            ? $mT014Custo['T014_Valor_ICMS'] : 0.00;
        
        $Custo_Nota         = $mT014Custo['T014_Valor_Custo_Final_Unitario'];
        $custo_Calculado    = (($mT014Custo['T014_Valor_Total_Custo'] + $mT014Custo['T014_Valor_ICMS_Substituicao'] + $mT014Custo['T014_Valor_Frete'] + $mT014Custo['T014_Valor_IPI'] + $mT014Custo['T014_Valor_Seguro'] + $mT014Custo['T014_Valor_Despesas_Acessorias'] - $mT014Custo['T014_Valor_ICMS']) - $mT014Custo['T014_Valor_Total_Desconto'])/$mT014Custo['T014_Quantidade'];
        
        log("Total: {$custo_Calculado} = (({$mT014Custo['T014_Valor_Total_Custo']} + {$mT014Custo['T014_Valor_ICMS_Substituicao']} + {$mT014Custo['T014_Valor_Frete']} + {$mT014Custo['T014_Valor_IPI']} + {$mT014Custo['T014_Valor_Seguro']} + {$mT014Custo['T014_Valor_Despesas_Acessorias']} - {$mT014Custo['T014_Valor_ICMS']}) - {$mT014Custo['T014_Valor_Total_Desconto']})/{$mT014Custo['T014_Quantidade']}");
        
        $Custo_Nota         = number_format($Custo_Nota, 2);
        $custo_Calculado    = number_format($custo_Calculado, 2);
        
        if($Custo_Nota <= 0){
        $validacaoCusto.="O produto código {$mT014Custo['D001_Codigo_Produto']} está com o valor do custo igual á zero.<br />";
        continue;
        }
        
        if($Custo_Nota != $custo_Calculado){
        $validacaoCusto.="O produto código {$mT014Custo['D001_Codigo_Produto']} está com o valor do custo errado. Valor calculado: {$custo_Calculado} Valor apurado: {$Custo_Nota}<br />";
        }
        }
        }
        log("Custo nota: ".$Custo_Nota. " Custo calc: ".$custo_Calculado);
        
        if($validacaoCusto!=''){
        $validacaoCusto.="Utilize o botão 'Calcular' para refazer o cálculo do custo.";
        return $validacaoCusto;
        }
        */
        // Fim Veriicação CUSTO
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Compara
        ////////////////////////////////////////////////////////////////////////////////////////////////
        
        if ($nTotal_Itens_Nota == 0) {
            $mT014['T014_Valor_Base_ICMS']      = 0;
            $mT014['T014_Valor_ICMS']           = 0;
            $mT014['T014_Valor_Base_IPI']       = 0;
            $mT014['T014_Valor_IPI']            = 0;
            $mT014['T014_Valor_Total_Custo']    = 0;
            $mT014['T014_Valor_Total_Desconto'] = 0;
            $mT014['T014_Valor_FCP_ST']         = 0;
        } else {
            $cSql_T014 = mysql_query("select sum(T014_Valor_Base_ICMS) as T014_Valor_Base_ICMS,
                                             round(sum(T014_Valor_ICMS),2) as T014_Valor_ICMS,
                                             sum(T014_Valor_Base_IPI) as T014_Valor_Base_IPI,
                                             sum(T014_Valor_IPI) as T014_Valor_IPI,
                                             sum(T014_Valor_Total_Custo) as T014_Valor_Total_Custo,
                                             sum(T014_Valor_Total_Desconto) as T014_Valor_Total_Desconto,
                                             sum(T014_Valor_FCP_ST) as T014_Valor_FCP_ST
                                        from T014
                                       where T014_T013_Id='$T013_Id'
                                         and T014_Quantidade>0");
            $mT014     = mysql_fetch_array($cSql_T014);
        }
        
        if (($mT013['T013_Valor_Total_Produtos'] == 0) && ($mT013['T013_Valor_Mao_Obra'] == 0) && $mT013['T013_Flag_Validacoes'] == 'S') {
            return "Não foi informado o valor total dos produtos e/ou valor da mão-de-obra";
        }
        
        if (($mT013['T013_Valor_Total_Produtos'] == 0) && ($mT013['T013_Valor_Mao_Obra'] > 0) && $mT013['T013_Flag_Validacoes'] == 'S') {
            if (empty($mT013['T013_Mao_Obra_1'])) {
                return "Não foi informado a descrição da mão-de-obra";
            }
        }
        
        $mT014['T014_Valor_Total_Custo']    = number_format($mT014['T014_Valor_Total_Custo'] - $mT014['T014_Valor_Total_Desconto'], 2, '.', '');
        $mT014['T014_Valor_Total_Desconto'] = round($mT014['T014_Valor_Total_Desconto'], 2);
        
        $Msg_Erro = "";
        
        $Total_Desconto = $mT013['T013_Valor_Desconto_Produtos'] + $mT013['T013_Valor_Desconto'];
        
        if ($mT014['T014_Valor_Total_Desconto'] != $Total_Desconto) {
            $Msg_Erro .= "VALOR DOS DESCONTOS<br />";
            $Msg_Erro .= "Valor Digitado: $Total_Desconto<br/>";
            $Msg_Erro .= "Valor Apurado: {$mT014['T014_Valor_Total_Desconto']} <br />";
        }
        
        if ($mT013['T013_Valor_Total_Base_ICMS_Diferido'] > 0) {
            $mT013['T013_Valor_Total_Base_ICMS'] = $mT013['T013_Valor_Total_Base_ICMS'] - $mT013['T013_Valor_Total_Base_ICMS_Diferido'];
        }
        
        if ($mT014['T014_Valor_ICMS'] != $mT013['T013_Valor_Total_ICMS'] && $mT013['T013_Flag_Validacoes'] == 'S') {
            if (($mT014['T014_Valor_ICMS'] >= ($mT013['T013_Valor_Total_ICMS'] + 0.05)) or ($mT014['T014_Valor_ICMS'] <= ($mT013['T013_Valor_Total_ICMS'] - 0.05))) {
                return "VALOR DO ICMS <br />Valor Digitado: {$mT013['T013_Valor_Total_ICMS']} <br />Valor Apurado: {$mT014['T014_Valor_ICMS']}<br />SUGESTÃO: Ajuste as casas decimais do valor do ICMS para corrigir.<br />" . ($mT013['T013_Valor_Total_ICMS'] - 0.05);
            } else {
                //             $Msg_Erro.= 'Diferença desconsiderada no valor de até 5 centavos para o valor do ICMS';
            }
        }
        
        /*
        if ($mT014[T014_Valor_ICMS]!=$mT013[T013_Valor_Total_ICMS]) {
        $Msg_Erro.="VALOR DO ICMS/pValor Digitado: $mT013[T013_Valor_Total_ICMS]/pValor Apurado: $mT014[T014_Valor_ICMS]/pSUGEST�O: Ajuste as casas decimais do valor do ICMS para corrigir./p/p";
        }
        */
        
        /*
        if ($mT014[T014_Valor_Base_IPI]!=$mT013[T013_Valor_Total_Base_IPI]) {
        $Msg_Erro.="VALOR BASE IPI/pValor Digitado: $mT013[T013_Valor_Total_Base_IPI]/pValor Apurado: $mT014[T014_Valor_Base_IPI]/p/p";
        }
        */
        
        if ($mT014['T014_Valor_IPI'] != $mT013['T013_Valor_Total_IPI']) {            

            $Tolerancia  = 0.5;
            $Valor_Maior = round($mT013['T013_Valor_Total_IPI'] + $Tolerancia, 2);
            $Valor_Menor = round($mT013['T013_Valor_Total_IPI'] - $Tolerancia, 2);
            
            if ($mT013['T013_Flag_Validacoes'] == 'S') {
                if (($mT014['T014_Valor_IPI'] >= $Valor_Maior) || ($mT014['T014_Valor_IPI'] <= $Valor_Menor)) {
                    return "Valor IPI do cabeçalho da NFe não confere com a soma dos produtos da NFe (IPI Cebaçalho: {$mT013['T013_Valor_Total_IPI']} IPI Produdos: {$mT014['T014_Valor_IPI']}), Será desconsiderada difereça de até $Tolerancia centavo(s)";
                } else {
                  //  $Msg_Erro .= "VALOR DO IPI<br />";
                  //  $Msg_Erro .= "Valor Digitado: {$mT013['T013_Valor_Total_IPI']}<br />";
                  //  $Msg_Erro .= "Valor Apurado: {$mT014['T014_Valor_IPI']}<br />";
                }
            }
        }        
        /*
        if ($mT014[T014_Valor_IPI]!=$mT013[T013_Valor_Total_IPI]) {
        $Msg_Erro.="VALOR DO IPI/pValor Digitado: $mT013[T013_Valor_Total_IPI]/pValor Apurado: $mT014[T014_Valor_IPI]/p/p";
        }
        */
        $Valor_Produto_Desconto = 0;
        if ($mT013['T013_Valor_Total_Produtos'] > 0) {
            $Valor_Produto_Desconto = $mT013['T013_Valor_Total_Produtos'] - $Total_Desconto;
        }
        
        if (number_format($mT014['T014_Valor_Total_Custo'], 2) != number_format($Valor_Produto_Desconto, 2)) {
            
            $tolerancia = 0.05;
            $valorMaior = round($Valor_Produto_Desconto + $tolerancia, 2);
            $valorMenor = round($Valor_Produto_Desconto - $tolerancia, 2);

            if($mT014['T014_Valor_Total_Custo'] <= $valorMenor || $mT014['T014_Valor_Total_Custo'] >= $valorMaior){
                $Msg_Erro .= "VALOR DOS PRODUTOS<br />Valor Digitado: " . $Valor_Produto_Desconto . " <br />Valor Apurado: " . $mT014['T014_Valor_Total_Custo'];
            }
        }
        
        
        if (!empty($Msg_Erro) && $mT013['T013_Flag_Validacoes'] == 'S') {
            return "Foram encontradas divergencias:<br />" . $Msg_Erro;
        }
        
        if (!empty($Msg_Aviso)) {
            return "AVISO!<br />" . $Msg_Aviso;
        }
        
        $Mensagem = "";
        
        if ($mT014['T014_Valor_Base_ICMS'] != $mT013['T013_Valor_Total_Base_ICMS']) {
            $Mensagem .= "VALOR BASE ICMS<br />Valor Digitado: {$mT013['T013_Valor_Total_Base_ICMS']}<br />Valor Apurado: {$mT014['T014_Valor_Base_ICMS']}<br />";
        }
        if (!empty($Mensagem) && $mT013['T013_Flag_Validacoes'] == 'S') {
            return $Mensagem;
        }

        if($mT014['T014_Valor_FCP_ST'] != $mT013['T013_Valor_Total_FCP_ST'] && $mT013['T013_Flag_Validacoes'] == 'S'){
            return "Valor do FCP ST no cabeçalho não confere com a soma dos produtos na nota, por favor verificar <br /><br /> Valor cabeçalho: {$mT013['T013_Valor_Total_FCP_ST']} <br /> Valor apurado: {$mT014['T014_Valor_FCP_ST']}";
        }

        $Mensagem = "";
        
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VALIDACAO DO CONHECIMENTO
        ////////////////////////////////////////////////////////////////////////////////////////////////
        if (($mT013['T013_Numero_Conhecimento'] != 'NAO TEM') && ($mT013['T013_Numero_Conhecimento'] > 0)) {
            if (empty($mT013['T013_D022_Id'])) {
                return "VOCE DEVE SELECIONAR UMA TRANSPORTADORA";
            }
            
            $FechouConhecimento = 'S';
            
            $cSql_T085_1 = mysql_query("select * 
                                          from T085 
                                     left join T084 on T084_Id=T085_T084_Id 
                                         where T084_Numero_Conhecimento='{$mT013['T013_Numero_Conhecimento']}' 
                                           and T084_D024_Id='{$mT013['T013_D024_Id']}' 
                                           and T085_Numero_Nota_FIscal='{$mT013['T013_Numero_Nota_Fiscal']}'");

            if (mysql_num_rows($cSql_T085_1) == 0) {
                return "Esta nota fiscal não está lançada no conhecimento de transporte. Verifique se o conhecimento foi registrado e a nota lançada.";
            }
            
            while ($mT085 = mysql_fetch_array($cSql_T085_1)) {
                if ($mT013['T013_D022_Id'] != $mT085['T084_D022_Id']) {
                    return "A transportadora da nota fiscal deve ser a mesma do conhecimento de transporte";
                }
                
                $cSql_T085 = mysql_query("select SUM(T085_Valor_Rateio) as SomaRateio, 
                                                 (T084_Valor_Conhecimento+IFNULL(T084_Valor_ICMS_ST,0)) as Valor_Conhecimento, 
                                                 ABS(SUM(T085_Valor_Rateio)-(T084_Valor_Conhecimento+IFNULL(T084_Valor_ICMS_ST,0))) as diferenca
                                            from T085
                                       LEFT JOIN T084 ON T085_T084_Id=T084_Id 
                                           where T085_T084_Id='{$mT085['T085_T084_Id']}'");
                $resT085   = mysql_fetch_array($cSql_T085);
                if ($resT085['diferenca']>0.01) {
                    return "O valor total do conhecimento não confere com o valor dos rateios, soma rateio R$ {$resT085['SomaRateio']}, valor conhecimento R$ {$resT085['Valor_Conhecimento']}.";
                }
                
                $cSql_T085 = mysql_query("select T085_Id 
                                            from T085
                                       LEFT JOIN T084 ON T085_T084_Id=T084_Id 
                                           where T085_T084_Id='{$mT085['T085_T084_Id']}' 
                                             and T084_D024_Id!='{$mT013['T013_D024_Id']}' 
                                             and T085_Numero_Nota_FIscal!='{$mT013['T013_Numero_Nota_Fiscal']}'
                                             and T085_Flag_Fechado='N'");
                
                if (mysql_num_rows($cSql_T085) > 0) {
                    
                    mysql_query("update T085 LEFT JOIN T084 ON T085_T084_Id=T084_Id 
                                    set T085_Flag_Fechado='S' 
                                  where T085_T084_Id='{$mT085['T085_T084_Id']}'
                                    and T084_D024_Id='{$mT013['T013_D024_Id']}' 
                                    and T085_Numero_Nota_FIscal='{$mT013['T013_Numero_Nota_Fiscal']}'");
                    $FechouConhecimento = 'N';
                    
                } else {
                    mysql_query("update T085 LEFT JOIN T084 ON T085_T084_Id=T084_Id 
                                    set T085_Flag_Fechado='S' 
                                  where T085_T084_Id='{$mT085['T085_T084_Id']}'
                                    and T084_D024_Id='{$mT013['T013_D024_Id']}' 
                                    and T085_Numero_Nota_FIscal='{$mT013['T013_Numero_Nota_Fiscal']}'");

                    mysql_query("update T084 set T084_Flag_Fechado='S' where T084_Id='{$mT085['T085_T084_Id']}'");
                }
            }
            if ($FechouConhecimento == 'N') {
                //-- CHAMA O FORMULARIO DE NOTAS PARA DIGITA��O DA PROXIMA NOTA FISCAL  
                return "OK, Para finalizar esta nota, termine de lancar as outras notas que compoem este conhecimento";
                
                //echo "parent.location.replace('../monta/monta_formularios.php3?Entidade=T013&operacao=1&Codigo_Consulta=001&Id_Entidade_Principal=$mT013[T013_C008_Id]&Entidade_Principal=C008');";
            } else {
                
                //-- VERIFICA SE O CONHECIMENTO FOI LANCANDO CORRETAMENTE JUNTO COM A NOTA FISCAL
                if (empty($mT013['T013_D022_Id'])) {
                    return "VOCE DEVE SELECIONAR UMA TRANSPORTADORA";
                }
                if (empty($mT013['T013_D024_Id'])) {
                    return "VOCE DEVE SELECIONAR UM CLIENTE";
                }
                if (empty($mT013['T013_Numero_Conhecimento'])) {
                    return "Digite o numero do conhecimento";
                }
                
                $cSql_D022 = mysql_query("select D022_Id,
                                                 D022_Nome_Empresa 
                                            from D022 
                                           where D022_Id='{$mT013['T013_D022_Id']}'");
                $mD022     = mysql_fetch_array($cSql_D022);
                
                $cSql_D024 = mysql_query("select D024_Id,
                                                 D024_Nome_Empresa 
                                            from D024 
                                           WHERE D024_Id='{$mT013['T013_D024_Id']}'");
                $mD024     = mysql_fetch_array($cSql_D024);
                
                $mNumero_Conhecimento = explode(',', $mT013['T013_Numero_Conhecimento']);
                $nT013_Valor_Frete    = 0;
                
                for ($x = 0; $x < count($mNumero_Conhecimento); $x++) {
                    
                    $Clausula = "select T084_Id,T084_Flag_Cancelado ";
                    $Clausula .= "from T084 ";
                    $Clausula .= "where T084_Numero_Conhecimento='$mNumero_Conhecimento[$x]' ";
                    if (count($mNumero_Conhecimento) == 1) {
                        $Clausula .= "and T084_D022_Id='{$mT013['T013_D022_Id']}' ";
                    }
                    $Clausula .= "and T084_Flag_Cancelado!='S' ";
                    $Clausula .= "and T084_D024_Id='{$mT013['T013_D024_Id']}' ";
                    $Clausula .= "and T084_C004_Id='{$mT013['T013_C004_Id']}' ";
                    
                    $cSql_T084 = mysql_query($Clausula);
                    $mT084     = mysql_fetch_array($cSql_T084);
                    if ($mT084['T084_Flag_Cancelado'] == 'S') {
                        return "CONHECIMENTO " . $mNumero_Conhecimento[$x] . "ESTA CANCELADO";
                    }
                    if (mysql_num_rows($cSql_T084) == 0) {
                        $msg = "CONHECIMENTO NÃO ENCONTRADO!<br ></count>";
                        $msg .= "Verifique se existe o conhecimento: " . $mNumero_Conhecimento[$x] . "<br />";
                        if ($x == 1) {
                            $msg .= "para a transportadora: {$mD022['D022_Id']} - " . substr($mD022['D022_Nome_Empresa'], 0, 20) . " e<br >";
                        }
                        $msg .= "para fornecedor:  {$mD024['D024_Id']} - " . substr($mD024['D024_Nome_Empresa'], 0, 20) . "<br />";
                        $msg .= "na tela de Conhecimentos.<br />";
                        return $msg;
                    }
                    
                    $cSql_T085 = mysql_query("select T085_Valor_Rateio,
                                                     T085_Valor_Total_Nota,
                                                     T085_Id,
                                                     T085_Flag_Fechado
                                                from T085
                                               where T085_T084_Id='{$mT084['T084_Id']}'
                                                 and T085_Numero_Nota_Fiscal='{$mT013['T013_Numero_Nota_Fiscal']}'");
                    $mT085     = mysql_fetch_array($cSql_T085);

                    if (mysql_num_rows($cSql_T085) == 0) {
                        $msg = "NOTA FISCAL NÃO ENCONTRADA PARA ESTE CONHECIMENTO!<br />";
                        $msg .= "Verifique se no conhecimento: " . $mNumero_Conhecimento[$x] . "<br />";
                        $msg .= "existe a nota fiscal: " . $mT013['T013_Numero_Nota_Fiscal'];
                        return $msg;
                    }
                    if ($mT085['T085_Valor_Total_Nota'] != $mT013['T013_Valor_Total']) {
                        return "VALOR TOTAL DA NOTA NAO CONFERE COM OS LANCAMENTOS DO CONHECIMENTO " . $mNumero_Conhecimento[$x];
                    }
                    $nT013_Valor_Frete += $mT085['T085_Valor_Rateio'];
                }
                
                //$nDiferenca_Frete = abs(bcsub($mT013['T013_Valor_Frete'], $nT013_Valor_Frete, 2));
                $nDiferenca_Frete = abs(number_format($mT013['T013_Valor_Frete'],2) - number_format($nT013_Valor_Frete, 2));
                
                if ($nDiferenca_Frete > 0) {
                    return 'VALOR DO FRETE NÃO CONFERE COM O CONHECIMENTO, VERIFIQUE SE O VALOR DO FRETE NESTA NOTA CONFERE COM O VALOR DO RATEIO DE FRETE LANÇADO NO CONHECIMENTO PARA ESTA NOTA.';
                }
            }
            
        }
        
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VALIDACAO DO ULTIMO NUMERO DE CONTROLE
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $cSql_C004 = mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
        $mC004     = mysql_fetch_array($cSql_C004);
        
        $cSql  = mysql_query("select T013_Numero_Controle
                                from T013
                               where T013_C004_Id='{$g['empresaAtual']}'
                            order by T013_Numero_Controle desc limit 0,1");
        $mT013 = mysql_fetch_array($cSql);
        
        if ($mT013['T013_Numero_Controle'] != $mC004['C004_Ultimo_Numero_Controle']) {
            //return "Numero de controle esta incorreto!!!/pInforme o ultimo numero de controle!!! ";
            
            //echo "<script>parent.location.replace('T013_formulario_informe_controle.php3');</script>";
            
        }
        
        //totalizações agora serão executadas no content em etapas - Lígia Nakazato
        // $sql = mysql_query("SELECT T014_Id FROM T014 where T014_T013_Id = '{$T013_Id}'");
        // while ($res = mysql_fetch_array($sql)) {
        //     log("T014_Id no finalizar compras= ".$res['T014_Id']);
        //     mysql_query("CALL T014_Gravar_Totalizacao('{$res['T014_Id']}')");
        // }
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- Verifica o custo
        ////////////////////////////////////////////////////////////////////////////////////////////////
        $validacaoCusto = $this->validacaoCusto($T013_Id);
        
        if ($validacaoCusto != '') {
            $validacaoCusto .= "Utilize o botão 'Calcular' para refazer o cálculo do custo.";
            return $validacaoCusto;
        }
        
        // Fim Veriicação CUSTO
        
        $mC004['C004_Ultimo_Numero_Controle']++;
        $Classificacao = '';
        mysql_query("update T013 set T013_Flag_Classificacao='$Classificacao',T013_Flag_Travar_Nota='S',T013_Numero_Controle='{$mC004['C004_Ultimo_Numero_Controle']}' where T013_Id='$T013_Id'");
        mysql_query("update C004 set C004_Ultimo_Numero_Controle='{$mC004['C004_Ultimo_Numero_Controle']}' where C004_Id='{$g['empresaAtual']}'");
        mysql_query("update T014
                       left join D009 on D009_Id=T014_D009_Id
                       left join D049 on D049_Id=D009_D049_Id
                       left join D001 on D001_Id=D049_D001_Id
                       left join T013 on T013_Id=T014_T013_Id
                       set T014_D003_Id=D001_D003_Id,T014_Flag_Classificacao=T013_Flag_Classificacao
                       where T014_T013_Id='$T013_Id'");
        mysql_query("UPDATE T224
                        LEFT JOIN T225 ON T225_T224_Id = T224_Id AND T225_Flag_Cancelada != 'S'
                        LEFT JOIN T014 ON T014_T225_Id = T225_Id
                        LEFT JOIN T013 ON T014_T013_Id = T013_Id
                        SET T224_Flag_Status = '8',
                        T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id)
                        WHERE T013_Id = '{$T013_Id}'
                        AND T224_Flag_Status = '3'");

        $sqlT014 = mysql_query("SELECT T014_Id, T014_D009_Id FROM T014 WHERE T014_T013_Id = '{$T013_Id}'");
        while($rowT014 = mysql_fetch_array($sqlT014)){
            $sqlT066 = mysql_query("SELECT T066_Id FROM T066 WHERE T066_D009_Id = '{$rowT014['T014_D009_Id']}' GROUP BY T066_Id");
            if(mysql_num_rows($sqlT066) == 1){
                $rowT066 = mysql_fetch_array($sqlT066);
                mysql_query("UPDATE T014 SET T014_T066_Id = '{$rowT066['T066_Id']}' WHERE T014_Id = '{$rowT014['T014_Id']}'");
            } 
        }
        //Carlos Carvalho
        $this->recebimentoMateriais($T013_Id);
        return true;
    }
    
    /**
     * finalizarNotaFinanceiro
     *
     * @param int $T013_Id
     * @return true/string
     */
    public function finalizarNotaFinanceiro($T013_Id)
    {
        
        global $g;
        
        //-- Conex�o com o banco de dados
        require "old/lib/gravar_entidade.php3";
        
        //-- INCLUSAO DA CLASSE DE PROCESSAMENTOS
        require "old/lib/processamentos.class.php3";
        
        // Classe produtos 
        require_once("bibliotecas/classes/CAD002.php");
        
        $CAD002 = new CAD002();
        
        if (empty($T013_Id)) {
            return "Erro: T013_Id não encontrado.";
        }
        
        
        $cSql_T013 = mysql_query("select * 
                                from T013 
                           left join D006 on D006_Id=T013_D006_Id
                           left join D024 on D024_Id=T013_D024_Id                   
                               where T013_Id='$T013_Id'");
        $mT013 = mysql_fetch_array($cSql_T013);
        
        if ($mT013['T013_Flag_Travar_Nota'] == 'N' or empty($mT013['T013_Flag_Travar_Nota'])) {
            return "Antes de finalizar no financeiro é necessário finalizar compras.";
        }
        
        if ($mT013['T013_Flag_Financeiro_Finalizado'] == 'S') {
            return "Esta nota já foi finalizada no financeiro. Não é possível executar novamente esse processo!";
        }
        
        $mC004['C004_Ultimo_Numero_Controle'] = $mT013['T013_Numero_Controle'];
        
       
        $cSql_C004 = mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
        $mC004     = mysql_fetch_array($cSql_C004);
        
        $D006_Flag_Gera_Contas = $mT013['D006_Flag_Gera_Contas'];
        $D006_D014_Id          = $mT013['D006_D014_Id'];
        $D006_D073_Id          = $mT013['D006_D073_Id'];
        $T013_D014_Id          = $mT013['T013_D014_Id'];
        
        $T014 = mysql_query("select D006_Flag_Gera_Contas, D006_D014_Id, D006_D073_Id
                                from T014 
                           left join D006 on D006_Id=T014_D006_Id
                               where T014_T013_Id='$T013_Id'
                                 and D006_Flag_Gera_Contas!='N'");
        
        $mT014 = mysql_fetch_array($T014);
        
        if (mysql_num_rows($T014) > 0) {
            $D006_Flag_Gera_Contas = $mT014['D006_Flag_Gera_Contas'];
            $D006_D014_Id          = $mT014['D006_D014_Id'];
            $D006_D073_Id          = $mT014['D006_D073_Id'];
        }

        if($D006_Flag_Gera_Contas == 'N'){
            return "A CFOP está definida para não gerar contas a pagar/receber";
        }
        
        if ($D006_Flag_Gera_Contas != 'N') {
            if($D006_Flag_Gera_Contas == 'P'){
                if(empty($D006_D073_Id) || $D006_D073_Id <= 0){
                    //mysql_query("ROLLBACK");
                    return "A CFOP gera contas a pagar e está sem um centro de custo definido! Verifique o cadastro da CFOP.";
                }
            }
            //Verifica se a CFOP gera contas e se a conta está definida
            if($D006_Flag_Gera_Contas == 'P' || $D006_Flag_Gera_Contas == 'R') {
                if(empty($mT013['T013_D014_Id']) || $mT013['T013_D014_Id'] <= 0){
                    if(empty($D006_D014_Id) || $D006_D014_Id <= 0) {
                        $tipoConta = ($D006_Flag_Gera_Contas == 'P') ? 'pagar' : 'receber';
                        //mysql_query("ROLLBACK");
                        return "A CFOP gera contas a {$tipoConta} e está sem uma conta definida! Verifique o cadastro da CFOP.";
                    } else {
                        $sqlD032 = mysql_query("SELECT D014_D032_Id FROM D014 WHERE D014_Id = '{$D006_D014_Id}'");
                        $D032 = mysql_fetch_array($sqlD032);
                        $D014_D032_Id = $D032['D014_D032_Id'];
                    }
                } else {
                    $sqlD032 = mysql_query("SELECT D014_D032_Id FROM D014 WHERE D014_Id = '{$mT013['T013_D014_Id']}'");
                    $D032 = mysql_fetch_array($sqlD032);
                    $D014_D032_Id = $D032['D014_D032_Id'];
                    $D006_D014_Id = $mT013['T013_D014_Id'];
                }
            }
            
            ////////////////////////////////////////////////////////////////////////////////////////////////
            //-- CONFIRMACAO DOS VENCIMENTOS E VALORES PARA CRIAR AS PARCELAS 
            ////////////////////////////////////////////////////////////////////////////////////////////////
            
            /*
            if (empty($mT013['T013_Numero_Dias'])) {
            mysql_query("ROLLBACK");
            return "Não foi preenchido os numero de dias entre as parcelas,verifique!";
            }
            */
            
            
            if ($mT013['T013_Valor_Entrada'] > $mT013['T013_Valor_Total']) {
                //mysql_query("ROLLBACK");
                return "Valor da entrada é maior que o valor total da nota fiscal";
            }
            
            if ($D006_Flag_Gera_Contas == 'P') {
                if (($mT013['T013_Valor_Entrada'] > 0) and (empty($mT013['T013_D031_Id_Entrada']))) {
                    //mysql_query("ROLLBACK");
                    return "Selecione o portador referente ao valor de entrada";
                }
                
                /*
                 * as parcelas agora são feitas em local diferente
                 */
                /*
                if ((($mT013['T013_Valor_Total']-$mT013['T013_Valor_Entrada'])>0) && (empty($mT013['T013_D031_Id']))) {
                mysql_query("ROLLBACK");          
                return "Selecione o portador referente as parcelas";
                }
                */
            }
            
            
            $T013_Valor_Total = $mT013['T013_Valor_Total'];
            /*
            if ( [T013_Valor_Entrada]>0) {
            $T013_Valor_Total=$T013_Valor_Total-$mT013[T013_Valor_Entrada];
            $Total_Duplicatas++;        
            }
            */
            if ($T013_Valor_Total > 0) {
                $Total_Duplicatas = $mT013['T013_Numero_Parcelas'];
            }
            
            
            ////////////////////////////////////////////////////////////////////////////////////////////////
            //-- VALIDACAO DO VALOR TOTAL DA NOTA COM O VALOR DA SOMA DOS TITULOS E DATAS VENCIMENTO
            ////////////////////////////////////////////////////////////////////////////////////////////////
            list($Dia_Anterior, $Mes_Anterior, $Ano_Anterior) = explode("/", hoje());
            $Data_Vencimento_Anterior = mktime(0, 0, 0, $Mes_Anterior, $Dia_Anterior, $Ano_Anterior);
            $Total_Geral += $mT013['T013_Valor_Juros'];
            
            
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // GERAR CONTAS A RECEBER E A PAGAR
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            $mLetras[1]  = "A";
            $mLetras[2]  = "B";
            $mLetras[3]  = "C";
            $mLetras[4]  = "D";
            $mLetras[5]  = "E";
            $mLetras[6]  = "F";
            $mLetras[7]  = "G";
            $mLetras[8]  = "H";
            $mLetras[9]  = "I";
            $mLetras[10] = "J";
            $mLetras[11] = "K";
            $mLetras[12] = "L";
            $mLetras[13] = "M";
            $mLetras[14] = "N";
            $mLetras[15] = "O";
            $mLetras[16] = "P";
            $mLetras[17] = "Q";
            $mLetras[18] = "R";
            $mLetras[19] = "S";
            $mLetras[20] = "T";
            $mLetras[21] = "U";
            $mLetras[22] = "V";
            $mLetras[23] = "W";
            $mLetras[24] = "X";
            $mLetras[25] = "Y";
            $mLetras[26] = "Z";
            $mLetras[27] = "AA";
            $mLetras[28] = "AB";
            $mLetras[29] = "AC";
            $mLetras[30] = "AD";
            $mLetras[31] = "AE";
            $mLetras[32] = "AF";
            $mLetras[33] = "AG";
            $mLetras[34] = "AH";
            $mLetras[35] = "AI";
            $mLetras[36] = "AJ";
            $mLetras[37] = "AK";
            $mLetras[38] = "AL";
            $mLetras[39] = "AM";
            $mLetras[40] = "AN";
            $mLetras[41] = "AO";
            $mLetras[42] = "AP";
            $mLetras[43] = "AQ";
            $mLetras[44] = "AR";
            $mLetras[45] = "AS";
            $mLetras[46] = "AT";
            $mLetras[47] = "AU";
            $mLetras[48] = "AV";
                        
            $Valor_Total_Base_Comissao  = $mT013["T013_Valor_Total_Produtos"] + $mT013["T013_Valor_Mao_Obra"];
            $Valor_Base_Comissao_Titulo = $Valor_Total_Base_Comissao / $Total_Duplicatas;
            
            
            /*
             * Esta parte foi refeita, pois no sistema antigo havia outro form para definir o pagamento
             * agora as parcelas a gerar contas vem da tabela T154, e são cadastradas no form de notas recebidas (aba pagamento)
             */
            
            $cSql_T154          = mysql_query("select * from T154 where T154_T013_Id='$T013_Id'");
            $count_Sql_T154     = mysql_query("select COUNT(T154_Id) as Total_Parcelas from T154 where T154_T013_Id='$T013_Id'");
            $mCount_T154        = mysql_fetch_array($count_Sql_T154);
            $multiEmpresa = $g['conf']->ini('faturamentoMultiEmpresa');
            if (mysql_num_rows($cSql_T154) <= 0 && $multiEmpresa == false) {
                //mysql_query("ROLLBACK");
                return 'Não foram gerados os pagamentos para esta nota';
            }

            //mysql_query("LOCK TABLE C004");
            //mysql_query("LOCK TABLE T013");
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            
            if ($D006_Flag_Gera_Contas == 'R') {
                
                //mysql_query("START TRANSACTION");
                
                while ($mT154 = mysql_fetch_array($cSql_T154)) {
                    
                    //unset($mGravar);
                    $i              = $mT154['T154_Numero_Parcela'];
                    $horaLancamento = strftime('%T');
                    $dataLancamento = troca_data_amd(hoje());
                    mysql_query("INSERT INTO T002 (T002_D024_Id,
                                                  T002_T013_Id,
                                                  T002_C004_Id,
                                                  T002_D062_Id,
                                                  T002_D021_Id,
                                                  T002_D014_Id,
                                                  T002_D032_Id,
                                                  T002_Nome_Cliente,
                                                  T002_Numero_Duplicata,
                                                  T002_Numero_Documento,
                                                  T002_Data_Emissao,
                                                  T002_Data_Vencimento,
                                                  T002_Data_Recebimento,
                                                  T002_Valor_Duplicata,
                                                  T002_Valor_Juros,
                                                  T002_Valor_Desconto,
                                                  T002_Valor_Total,
                                                  T002_Valor_Base_Comissao,
                                                  T002_Percentual_Comissao,
                                                  T002_Data_Lancamento,
                                                  T002_Hora_Lancamento,
                                                  T002_Numero_Parcela,
                                                  T002_Total_Parcelas
                                            ) VALUES (
                                                  '{$mT013['T013_D024_Id']}',
                                                  '{$T013_Id}',
                                                  '{$mT013['T013_C004_Id']}',
                                                  '{$mT013['D024_D062_Id']}',
                                                  '{$mT013['T013_D021_Id']}',
                                                  '{$D006_D014_Id}',
                                                  '{$D014_D032_Id}',
                                                  '',
                                                  '{$mT013['T013_Numero_Nota_Fiscal']}" . "$mLetras[$i]',
                                                  '{$mT013['T013_Numero_Nota_Fiscal']}',
                                                  '{$mT013['T013_Data_Emissao']}',
                                                  '{$mT154['T154_Data_Vencimento']}',
                                                  '0000-00-00',
                                                  '{$mT154['T154_Valor_Parcela']}',
                                                  '0',
                                                  '0',
                                                  '{$mT154['T154_Valor_Parcela']}',
                                                  '$Valor_Base_Comissao_Titulo',
                                                  '{$mT013['T013_Percentual_Comissao_Representante']}',
                                                  '{$dataLancamento}',
                                                  '{$horaLancamento}',
                                                  '{$i}',
                                                  '{$mCount_T154['Total_Parcelas']}')");
                    
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return 'falha na geração de contas a receber';
                    }
                    /*            
                    $mGravar['T002_D024_Id']="{$mT013['T013_D024_Id']}";
                    $mGravar['T002_T013_Id']="$T013_Id";
                    $mGravar['T002_C004_Id']="{$mT013['T013_C004_Id']}";
                    $mGravar['T002_D062_Id']="{$mT013['D024_D062_Id']}";
                    $mGravar['T002_D021_Id']="{$mT013['T013_D021_Id']}";
                    $mGravar['T002_Nome_Cliente']="";
                    $mGravar['T002_Numero_Duplicata']="{$mT013['T013_Numero_Nota_Fiscal']}"."$mLetras[$i]";
                    $mGravar['T002_Numero_Documento']="{$mT013['T013_Numero_Nota_Fiscal']}";
                    $mGravar['T002_Data_Emissao']="{$mT013['T013_Data_Emissao']}";
                    $mGravar['T002_Data_Vencimento']="{$mT154['T154_Data_Vencimento']}";
                    $mGravar['T002_Data_Recebimento']="0000-00-00";
                    $mGravar['T002_Valor_Duplicata']="{$mT154['T154_Valor_Parcela']}";
                    $mGravar['T002_Valor_Juros']="0";
                    $mGravar['T002_Valor_Desconto']="0";
                    $mGravar['T002_Valor_Total']="{$mT154['T154_Valor_Parcela']}";
                    $mGravar['T002_Valor_Base_Comissao']=$Valor_Base_Comissao_Titulo;
                    $mGravar['T002_Percentual_Comissao']=$mT013['T013_Percentual_Comissao_Representante'];
                    $mGravar['T002_Data_Lancamento']=troca_data_amd(hoje());
                    $mGravar['T002_Hora_Lancamento']=strftime('%T');
                    $mGravar['T002_Numero_Parcela']=$i;
                    
                    gravar_entidade($mGravar,'T002','','','','','MANUAL','001');
                    */
                }
                //mysql_query("COMMIT");
                
            } elseif ($D006_Flag_Gera_Contas == 'P') {
                $mT013['D024_Nome_Empresa'] = mysql_real_escape_string($mT013['D024_Nome_Empresa']);
                //mysql_query("START TRANSACTION");
                
                while ($mT154 = mysql_fetch_array($cSql_T154)) {
                    
                    //unset($mGravar);
                    $i              = $mT154['T154_Numero_Parcela'];
                    $horaLancamento = strftime('%T');
                    mysql_query("insert into T015 (T015_D031_Id,
                                                              T015_D024_Id,
                                                              T015_T013_Id,
                                                              T015_C004_Id,
                                                              T015_D021_Id,
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
                                                              T015_Valor_Juros,
                                                              T015_Valor_Desconto,
                                                              T015_Valor_Total,
                                                              T015_Valor_Base_Comissao,
                                                              T015_Percentual_Comissao,
                                                              T015_Data_Lancamento,
                                                              T015_Hora_Lancamento,
                                                              T015_Numero_Parcela,
                                                              T015_Total_Parcelas
                                                              )
                                                              values 
                                                              ('{$mT154['T154_D031_Id']}',
                                                              '{$mT013['T013_D024_Id']}',
                                                              '{$T013_Id}',
                                                              '{$mT013['T013_C004_Id']}',
                                                              '{$mT013['T013_D021_Id']}',
                                                              '{$D006_D014_Id}',
                                                              '{$D014_D032_Id}',
                                                              '{$D006_D073_Id}',
                                                              '{$mT013['D024_Nome_Empresa']}',
                                                              '{$mT013['T013_Numero_Nota_Fiscal']}" . "$mLetras[$i]',
                                                              '{$mT013['T013_Numero_Nota_Fiscal']}',
                                                              '{$mT013['T013_Data_Emissao']}',
                                                              '{$mT154['T154_Data_Vencimento']}',
                                                              '0000-00-00',
                                                              '{$mT154['T154_Valor_Parcela']}',
                                                              '0',
                                                              '0',
                                                              '{$mT154['T154_Valor_Parcela']}',
                                                              '$Valor_Base_Comissao_Titulo',
                                                              '{$mT013['T013_Percentual_Comissao_Representante']}',
                                                              '{$mT013['T013_Data_Entrada']}',
                                                              '{$horaLancamento}',
                                                              '{$i}',
                                                              '{$mCount_T154['Total_Parcelas']}')");
                    
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return 'falha na geração de contas a pagar: ' . $erro;
                    }
                }
                //mysql_query("COMMIT");
                
            }
        } 
        
        mysql_query("UPDATE T013 SET T013_Flag_Financeiro_Finalizado='S', T013_Data_Financeiro = NOW() WHERE T013_Id='{$T013_Id}'");

        $parametrosGatilho = array('T013_Id' => $T013_Id);           
        
        $retorno = $this->gatilhoAposFinalizarFinanceiro($parametrosGatilho);

        if($retorno !== true)
        {
            mysql_query("ROLLBACK");
            return "Erro ao apos finalizar financeiro, por favor entre em contato com o suporte técnico. Erro: {$retorno}";
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        //mysql_query("UNLOCK TABLE C004");
        //mysql_query("UNLOCK TABLE T013");

        return true;
    }
    
    /**
     * finalizarNotaEstoque
     *
     * @param int $T013_Id
     * @return true/string
     */
    public function finalizarNotaEstoque($T013_Id)
    {
        global $g;
        //-- Conexão com o banco de dados
        //require "old/lib/gravar_entidade.php3";

        //-- INCLUSAO DA CLASSE DE PROCESSAMENTOS
        //require "old/lib/processamentos.class.php3";

        // Classe produtos
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        if (empty($T013_Id)) {
            return "Erro: T013_Id não encontrado.";
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $cSql_T013 = mysql_query("select * 
                                from T013 
                           left join D006 on D006_Id=T013_D006_Id
                           left join D024 on D024_Id=T013_D024_Id
                               where T013_Id='$T013_Id'");
        $mT013     = mysql_fetch_array($cSql_T013);

        if ($mT013['T013_Flag_Travar_Nota'] == 'N' or empty($mT013['T013_Flag_Travar_Nota'])) {
            return "É necessário finalizar compras antes de finalizar o estoque";
        }

        /*
        if ($mT013['T013_Flag_Financeiro_Finalizado']=='N' or empty($mT013['T013_Flag_Financeiro_Finalizado'])){
        return "É necessário finalizar o financeiro antes de finalizar o estoque";  
        }
        */

        if ($mT013['T013_Flag_Estoque_Finalizado'] == 'S') {
            return "Esta nota já foi finalizada no estoque. Não é possível executar novamente esse processo!";
        }

        ///////////////////////////////////////////////////////////////////////////////////
        //-- LANCAMENTO NO HISTORICO E NO ESTOQUE
        ///////////////////////////////////////////////////////////////////////////////////
        $cSql_T014 = mysql_query("SELECT T014_Id,
                                        T014_Id_Duplicado,
                                        T014_T013_Id,
                                        T014_D009_Id,
                                        T014_Quantidade,
                                        D006_Flag_Estoque,
                                        T014_Valor_Custo_Unitario,
                                        T014_Valor_Desconto_Unitario,
                                        T014_Aliquota_ICMS,
                                        T014_Valor_ICMS,
                                        D006_Flag_Credita_IPI,
                                        T014_Aliquota_IPI,
                                        T014_Valor_IPI,
                                        D006_Flag_Substituicao_Tributaria,
                                        D006_Flag_Venda_Compra_Outros,
                                        T014_Valor_ICMS_Substituicao_Unitario,
                                        T014_Valor_Frete,
                                        T014_Valor_Seguro,
                                        T014_Valor_Despesas_Acessorias,
                                        T014_Valor_Custo_Final_Unitario,
                                        D049_Flag_Nacional_Importado,
                                        T014_Percentual_Reducao_ICMS,
                                        T014_Valor_Base_ICMS,
                                        T014_Percentual_Reducao_IPI,
                                        T014_Valor_Base_IPI,
                                        T014_Valor_ICMS_Substituicao,
                                        T014_Valor_PIS,
                                        T014_Valor_COFINS,
                                        D001_Codigo_Produto,
                                        T014_Valor_ICMS_Diferenca_Aliquotas,
                                        T014_Quantidade,
                                        T014_Aliquota_ICMS_Diferenca_Aliquotas,
                                        T014_T066_Id,
                                        T014_C004_Id,
                                        T014_D006_Id,
                                        T014_Codigo_Produto,
                                        T014_Quantidade,
                                        D006_Flag_Atualiza_Custo,
                                        D006_Flag_Devolucao,
                                        T014_Aliquota_PIS,
                                        T014_Aliquota_COFINS,
                                        T014_Flag_Origem_Produto,
                                        D049_Id,
                                        T014_Codigo_Produto_Fornecedor,
                                        T014A_Valor_Custo_Final_Liquido_Unitario,
                                        T014A_Valor_Acrescimo_Custo,
                                        T014A_Ficha_Conteudo_Importacao
                                    from T014
                              left join D009 on D009_Id=T014_D009_Id
                              left join D049 on D049_Id=D009_D049_Id
                              left join D001 on D001_Id=D049_D001_Id
                              left join D006 on D006_Id=T014_D006_Id
                              left join T001 on T001_T014_Id=T014_Id
                              left join T014A ON T014_Id=T014A_T014_Id
                                  where T014_T013_Id='$T013_Id'
                                    and T001_Id IS NULL");
                                     //and D006_Flag_Estoque!='N'

        $D006_Flag_Estoque = "N";
        while ($mT014 = mysql_fetch_array($cSql_T014)) {
            //Verifica se o item foi duplicado na entrada da nota fiscal, caso o item tenha sido duplicado, o calculo unitário dos impostos deve ser feito sobre a quantidade total do produto na nota fiscal
            // !!! Se o item já veio duplicado na nota fiscal (XML), o valor unitario do impostos não precisam ser calculados sobre a quantidade total do produto na nota fiscal !!!
            $sqlItemDuplicado = mysql_query("SELECT T014_Id FROM T014 WHERE T014_Id_Duplicado = '{$mT014['T014_Id']}'");
            if(mysql_num_rows($sqlItemDuplicado) > 0 || $mT014['T014_Id_Duplicado'] > 0){
                $sqlQtdSoma = "SELECT SUM(T014_Quantidade) as T014_Quantidade FROM T014 WHERE T014_T013_Id = '{$mT014['T014_T013_Id']}' AND T014_D009_Id = '{$mT014['T014_D009_Id']}'";
                $resQtdSoma = mysql_query($sqlQtdSoma);
                $mQtdSoma = mysql_fetch_assoc($resQtdSoma);
                $QtdSoma = $mQtdSoma['T014_Quantidade'];
            } else {
                $QtdSoma = $mT014['T014_Quantidade'];
            }

            ///////////////////////////////////////////////////////////////////////////////////
            // ENCONTRA O ID DO T066
            ///////////////////////////////////////////////////////////////////////////////////
            $cSql_T066 = mysql_query("select T066_Id from T066 where T066_D048_Id='$D048_Id' and T066_D009_Id='{$mT014['T014_D009_Id']}'");
            $mT066     = mysql_fetch_array($cSql_T066);

            ///////////////////////////////////////////////////////////////////////////////////
            // MOVIMENTA��O DO ESTOQUE
            ///////////////////////////////////////////////////////////////////////////////////
            if ($mT014['D006_Flag_Estoque'] == 'D') {
                $Flag_Operacao_Historico = "S";
                $D006_Flag_Estoque       = "S";
            } elseif ($mT014['D006_Flag_Estoque'] == 'C') {
                $Flag_Operacao_Historico = "E";
                $D006_Flag_Estoque       = "S";
            } elseif ($mT014['D006_Flag_Estoque'] == 'N') {
                $Flag_Operacao_Historico = "N";
            }

            $QtdSoma = ($QtdSoma < 0) ? 1 : $QtdSoma;
            unset($mDemonstrativo_Custo);
            $mDemonstrativo_Custo[] = " ,,Preço,," . number_format($mT014['T014_Valor_Custo_Unitario'], 2, ',', '.');
            $mDemonstrativo_Custo[] = "-,,Desconto nos Itens,," . number_format($mT014['T014_Valor_Desconto_Unitario'], 2, ',', '.');
            $mDemonstrativo_Custo[] = " ,,Preço Liquido,," . number_format($mT014['T014_Valor_Custo_Unitario'] - $mT014['T014_Valor_Desconto_Unitario'], 2, ',', '.');
            if ($mC004['C004_Flag_Empresa_Simples'] != '1') {
                $mDemonstrativo_Custo[] = "-,,{$mT014['T014_Aliquota_ICMS']}% ICMS,," . number_format($mT014['T014_Valor_ICMS'] / $QtdSoma, 2, ',', '.');
            }

            if ($mT014['D006_Flag_Credita_IPI'] != 'S') {
                $mDemonstrativo_Custo[] = "+,,{$mT014['T014_Aliquota_IPI']}% IPI,," . number_format($mT014['T014_Valor_IPI'] / $QtdSoma, 2, ',', '.');
            }
            if ($mT013['D006_Flag_Substituicao_Tributaria'] == 'S' && $mT013['D006_Flag_Venda_Compra_Outros'] == 'T' && $mT013['T013_C004_Id'] == 2) {
                $mDemonstrativo_Custo[] = " ,,ICMS Substituição,," . number_format($mT014['T014_Valor_ICMS_Substituicao_Unitario'], 2, ',', '.');
            } else {
                $mDemonstrativo_Custo[] = "+,,ICMS Substituição,," . number_format($mT014['T014_Valor_ICMS_Substituicao_Unitario'], 2, ',', '.');
            }
            $mDemonstrativo_Custo[] = "+,,Frete,," . number_format($mT014['T014_Valor_Frete'] / $QtdSoma, 2, ',', '.');
            $mDemonstrativo_Custo[] = "+,,Seguro,," . number_format($mT014['T014_Valor_Seguro'] / $QtdSoma, 2, ',', '.');
            $mDemonstrativo_Custo[] = "+,,Despesas Acessórias,," . number_format($mT014['T014_Valor_Despesas_Acessorias'] / $QtdSoma, 2, ',', '.');

            if($mT014['T014A_Valor_Acrescimo_Custo'] > 0){
                $mDemonstrativo_Custo[] = "+,,Acrescimo,," . number_format($mT014['T014A_Valor_Acrescimo_Custo'], 2, ',', '.');
            }
            $mDemonstrativo_Custo[] = "=,,Custo Final,," . number_format($mT014['T014_Valor_Custo_Final_Unitario'], 2, ',', '.');

            if ($mT014['D049_Flag_Nacional_Importado'] == 'I') {
                $mT014['T014_Valor_Custo_Final_Unitario'] = $mT014['T014_Valor_Custo_Final_Unitario'] / $mT013['T013_Valor_Dolar'];
                $mDemonstrativo_Custo[]                   = "=,,Dólar,," . number_format($mT013['T013_Valor_Dolar'], 4, ',', '.');
                $mDemonstrativo_Custo[]                   = "=,,Custo Final US,," . number_format($mT014['T014_Valor_Custo_Final_Unitario'], 2, ',', '.');
            }

            $mDemonstrativo_Custo[] = "";
            $mDemonstrativo_Custo[] = ",,% Redução ICMS,," . number_format($mT014['T014_Percentual_Reducao_ICMS'], 2, ',', '.');
            $mDemonstrativo_Custo[] = ",,Base ICMS,," . number_format($mT014['T014_Valor_Base_ICMS'] / $QtdSoma, 2, ',', '.');

            $mDemonstrativo_Custo[] = "";
            $mDemonstrativo_Custo[] = ",,% Redução IPI,," . number_format($mT014['T014_Percentual_Reducao_IPI'], 2, ',', '.');
            $mDemonstrativo_Custo[] = ",,Base IPI,," . number_format($mT014['T014_Valor_Base_IPI'] / $QtdSoma, 2, ',', '.');

            $Demonstrativo_Custo = implode("\n", $mDemonstrativo_Custo);

            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // HISTORICO DO PRODUTO
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // '{$mT066['T066_Id']}',

            $valorICMSUnitario     = $mT014['T014_Valor_ICMS'] / $QtdSoma;
            $valorIPIUnitario      = $mT014['T014_Valor_IPI'] / $QtdSoma;
            $valorSTUnitario       = $mT014['T014_Valor_ICMS_Substituicao'] / $QtdSoma;
            $valorFreteUnitario    = $mT014['T014_Valor_Frete'] / $QtdSoma;
            $valorSeguroUnitario   = $mT014['T014_Valor_Seguro'] / $QtdSoma;
            $valorDespesasUnitario = $mT014['T014_Valor_Despesas_Acessorias'] / $QtdSoma;
            $valorPISUnitario      = $mT014['T014_Valor_PIS'] / $QtdSoma;
            $valorCOFINSUnitario   = $mT014['T014_Valor_COFINS'] / $QtdSoma;
            $valorICMSUnitario     = empty($valorICMSUnitario) ? 0 : $valorICMSUnitario;
            $valorIPIUnitario      = empty($valorIPIUnitario) ? 0 : $valorIPIUnitario;
            $valorSTUnitario       = empty($valorSTUnitario) ? 0 : $valorSTUnitario;
            $valorFreteUnitario    = empty($valorFreteUnitario) ? 0 : $valorFreteUnitario;
            $valorSeguroUnitario   = empty($valorSeguroUnitario) ? 0 : $valorSeguroUnitario;
            $valorDespesasUnitario = empty($valorDespesasUnitario) ? 0 : $valorDespesasUnitario;
            $valorPISUnitario      = empty($valorPISUnitario) ? 0 : $valorPISUnitario;
            $valorCOFINSUnitario   = empty($valorCOFINSUnitario) ? 0 : $valorCOFINSUnitario;

            $percentualSTUnitario  = ($valorSTUnitario / ($mT014['T014_Valor_Custo_Unitario'] + $valorIPIUnitario))*100;
            $percentualSTUnitario  = ($percentualSTUnitario > 0) ? $percentualSTUnitario : 0;

            $sqlT001 = mysql_query("SELECT T001_Id FROM T001 WHERE T001_D009_Id = '{$mT014['T014_D009_Id']}' AND T001_T014_Id = '{$mT014['T014_Id']}' AND T001_Flag_Operacao != 'C'");
            if(mysql_num_rows($sqlT001) > 0){
                mysql_query("ROLLBACK");
                return "Erro ao inserir histórico do produto. O produto {$mT014['D001_Codigo_Produto']} já possui um registro no histórico para esta nota fiscal.";
            }

            $mT014['T014_Valor_ICMS_Diferenca_Aliquotas'] = $mT014['T014_Valor_ICMS_Diferenca_Aliquotas'] / $mT014['T014_Quantidade'];
            $mT014['T014_Aliquota_ICMS_Diferenca_Aliquotas'] = $mT014['T014_Aliquota_ICMS_Diferenca_Aliquotas'];

            $tmpD004_Id = getD004($mT014['T014_T066_Id']);
            $T001_Id    = mysql_query("insert into T001 (T001_D024_Id,
                                                              T001_T014_Id,
                                                              T001_T013_Id,
                                                              T001_D009_Id,
                                                              T001_C004_Id,
                                                              T001_D006_Id,
                                                              T001_T066_Id,
                                                              T001_Codigo_Produto,
                                                              T001_Data_Lancamento,
                                                              T001_Descricao_Operacao,
                                                              T001_Quantidade,
                                                              T001_Flag_Operacao,
                                                              T001_Valor_Preco_Unitario,
                                                              T001_Valor_Custo_Unitario,
                                                              T001_Flag_Atualiza_Custo,
                                                              T001_Quantidade_Saldo,
                                                              T001_Demonstrativo_Custo,
                                                              T001_Flag_Devolucao,
                                                              T001_Valor_ST_Unitario,
                                                              T001_Valor_ICMS,
                                                              T001_Aliquota_ICMS,
                                                              T001_Valor_IPI,
                                                              T001_Aliquota_IPI,
                                                              T001_Valor_ST,
                                                              T001_Percentual_ST,
                                                              T001_Valor_Frete,
                                                              T001_Valor_Seguro,
                                                              T001_Valor_Despesas_Acessorias,
                                                              T001_Valor_Desconto_Unitario,
                                                              T001_Valor_ICMS_Diferenca_Aliquotas,
                                                              T001_Aliquota_PIS,
                                                              T001_Valor_PIS,
                                                              T001_Aliquota_COFINS,
                                                              T001_Valor_COFINS,
                                                              T001_D004_Id,
                                                              T001_Aliquota_ICMS_Diferenca_Aliquotas
                                                              )
                                                              values 
                                                              ('{$mT013['T013_D024_Id']}',
                                                              '{$mT014['T014_Id']}',
                                                              '{$mT013['T013_Id']}',
                                                              '{$mT014['T014_D009_Id']}',
                                                              '{$mT014['T014_C004_Id']}',
                                                              '{$mT014['T014_D006_Id']}',
                                                              '{$mT014['T014_T066_Id']}',
                                                              '{$mT014['T014_Codigo_Produto']}',
                                                              curdate(),
                                                              'NF-{$mT013['T013_Numero_Nota_Fiscal']}',
                                                              '{$mT014['T014_Quantidade']}',
                                                              '$Flag_Operacao_Historico',
                                                              '{$mT014['T014_Valor_Custo_Unitario']}',
                                                              '{$mT014['T014_Valor_Custo_Final_Unitario']}',
                                                              '{$mT014['D006_Flag_Atualiza_Custo']}',
                                                              '0',
                                                              '$Demonstrativo_Custo',
                                                              '{$mT014['D006_Flag_Devolucao']}',
                                                              '{$mT014['T014_Valor_ICMS_Substituicao_Unitario']}',
                                                              '{$valorICMSUnitario}',
                                                              '{$mT014['T014_Aliquota_ICMS']}',
                                                              '{$valorIPIUnitario}',
                                                              '{$mT014['T014_Aliquota_IPI']}',
                                                              '{$valorSTUnitario}',
                                                              '{$percentualSTUnitario}',
                                                              '{$valorFreteUnitario}',
                                                              '{$valorSeguroUnitario}',
                                                              '{$valorDespesasUnitario}',
                                                              '{$mT014['T014_Valor_Desconto_Unitario']}',
                                                              '{$mT014['T014_Valor_ICMS_Diferenca_Aliquotas']}',
                                                              '{$mT014['T014_Aliquota_PIS']}',
                                                              '{$valorPISUnitario}',
                                                              '{$mT014['T014_Aliquota_COFINS']}',
                                                              '{$valorCOFINSUnitario}',
                                                              '{$tmpD004_Id}',
                                                              '{$mT014['T014_Aliquota_ICMS_Diferenca_Aliquotas']}'
                                                              )");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return 'Erro ao inserir o histórico do produto. <br />Erro: '.$erro;
            }

            $T014_ID = $mT014['T014_Id'];
            $D009_ID = $mT014['T014_D009_Id'];
            $T066_ID = $mT014['T014_T066_Id'];
            
            $T001_Id = $g['mysqlLastId'];

            $T001_Id    = mysql_query("INSERT INTO T001A (T001A_T001_Id,
                                                          T001A_Valor_Custo_Unitario_Liquido,
                                                          T001A_Valor_Acrescimo_Custo
                                                  )VALUES(
                                                          '{$T001_Id}', 
                                                          '{$mT014['T014A_Valor_Custo_Final_Liquido_Unitario']}',
                                                          '{$mT014['T014A_Valor_Acrescimo_Custo']}')");

            if (strlen($mT014['T014_Flag_Origem_Produto']) > 0 and $mT014['D006_Flag_Venda_Compra_Outros'] == 'C' && $g['C031']['atualizarOrigemProdutoComNotaRecebida'] == 'S') {

                $origemProduto = $this->conversaoOrigemProdutoEntrada($mT014['T014_Flag_Origem_Produto']);

                mysql_query("UPDATE D049 LEFT JOIN D009 ON D009_D049_Id = D049_Id SET D049_Origem_Mercadoria = '{$origemProduto}'  WHERE D009_Id = '{$mT014['T014_D009_Id']}'");

                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return 'Erro ao finalizar o estoque da nota fiscal.';
                }
            }

            if($g['C031']['zeraCustoManualFinalizarEstoqueEntrada'] == 'S' && $mT014['D006_Flag_Atualiza_Custo'] == 'S'){
                mysqli_query("UPDATE D009 SET D009_Valor_Preco_Tabela = 0 WHERE D009_Id = '{$mT014['T014_D009_Id']}' AND D009_Valor_Preco_Tabela > 0");
            }

            if(!empty($mT014['T014A_Ficha_Conteudo_Importacao'])) {
                mysqli_query("UPDATE D009A SET D009A_Ficha_Conteudo_Importacao = '{$mT014['T014A_Ficha_Conteudo_Importacao']}' WHERE D009A_D009_Id = '{$mT014['T014_D009_Id']}'");
            }
        }

        // Verifica se vai escriturar no SPED Contribuições
        // Só não lança se todas as CFOPs dos produtos estiverem para não escriturar
        $sqlContribuicoes = mysql_query("SELECT T014_Id
                                           FROM T014
                                      LEFT JOIN D006 ON D006_Id = T014_D006_Id
                                          WHERE T014_T013_Id = '{$T013_Id}'
                                            AND D006_Flag_Escritura_Sped_Contribuicoes = 'S'
                                          LIMIT 1");
                                              
        if(mysql_num_rows($sqlContribuicoes) <= 0){
            mysqli_query("UPDATE T013A SET T013A_Flag_Escritura_Sped_Contribuicoes = 'N' WHERE T013A_T013_Id = '{$T013_Id}' AND T013A_Flag_Escritura_Sped_Contribuicoes != 'N'");
        } else {
            mysqli_query("UPDATE T013A SET T013A_Flag_Escritura_Sped_Contribuicoes = 'S' WHERE T013A_T013_Id = '{$T013_Id}' AND T013A_Flag_Escritura_Sped_Contribuicoes != 'S'");
        }

        mysql_query("UPDATE T013 set T013_Flag_Estoque_Finalizado='S', T013_Data_Entrada_Estoque = NOW() WHERE T013_Id='$T013_Id'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return 'Erro ao finalizar o estoque da nota fiscal.';
        }

        $T014 = mysql_query("select T014_Id
                               from T014
                          left join D006 on D006_Id=T014_D006_Id
                          left join T001 on T001_T014_Id=T014_Id
                              where T014_T013_Id='$T013_Id'
                                and D006_Flag_Estoque!='N'
                                and (T001_Id IS NULL OR T001_Id='')");

        if(mysql_num_rows($T014)>0){
            mysql_query("ROLLBACK");
            return "Houve algum erro ao incluir o histórico dos produtos. Por favor, tente novamente. Se o problema persistir entre em contato com nosso suporte técnico";
        }

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

        $cSql_T014 = mysql_query("SELECT T014_Id,
                                        T014_Id_Duplicado,
                                        T014_T013_Id,
                                        T014_D009_Id,
                                        T014_Quantidade,
                                        D006_Flag_Estoque,
                                        T014_Valor_Custo_Unitario,
                                        T014_Valor_Desconto_Unitario,
                                        T014_Aliquota_ICMS,
                                        T014_Valor_ICMS,
                                        D006_Flag_Credita_IPI,
                                        T014_Aliquota_IPI,
                                        T014_Valor_IPI,
                                        D006_Flag_Substituicao_Tributaria,
                                        D006_Flag_Venda_Compra_Outros,
                                        T014_Valor_ICMS_Substituicao_Unitario,
                                        T014_Valor_Frete,
                                        T014_Valor_Seguro,
                                        T014_Valor_Despesas_Acessorias,
                                        T014_Valor_Custo_Final_Unitario,
                                        D049_Flag_Nacional_Importado,
                                        T014_Percentual_Reducao_ICMS,
                                        T014_Valor_Base_ICMS,
                                        T014_Percentual_Reducao_IPI,
                                        T014_Valor_Base_IPI,
                                        T014_Valor_ICMS_Substituicao,
                                        T014_Valor_PIS,
                                        T014_Valor_COFINS,
                                        D001_Codigo_Produto,
                                        T014_Valor_ICMS_Diferenca_Aliquotas,
                                        T014_Quantidade,
                                        T014_Aliquota_ICMS_Diferenca_Aliquotas,
                                        T014_T066_Id,
                                        T014_C004_Id,
                                        T014_D006_Id,
                                        T014_Codigo_Produto,
                                        T014_Quantidade,
                                        D006_Flag_Atualiza_Custo,
                                        D006_Flag_Devolucao,
                                        T014_Aliquota_PIS,
                                        T014_Aliquota_COFINS,
                                        T014_Flag_Origem_Produto,
                                        D049_Id,
                                        T014_Codigo_Produto_Fornecedor,
                                        D001_Id,
                                        T014_Codigo_Barras,
                                        T014A_Codigo_Barras_Tributavel
                                    from T014
                              left join T014A on T014A_T014_Id = T014_Id
                              left join D009 on D009_Id=T014_D009_Id
                              left join D049 on D049_Id=D009_D049_Id
                              left join D001 on D001_Id=D049_D001_Id
                              left join D006 on D006_Id=T014_D006_Id
                              left join T001 on T001_T014_Id=T014_Id
                                  where T014_T013_Id='$T013_Id'");
                                     //and D006_Flag_Estoque!='N'

        $D006_Flag_Estoque = "N";
        while ($mT014 = mysql_fetch_array($cSql_T014)) {

            $sqlT225 = mysql_query("SELECT IFNULL(T219_T006_Id,0) as T219_T006_Id, 
                                                  T225_Id, 
                                                  T219_Id 
                                             FROM T014 
                                        LEFT JOIN T225 ON T225_Id=T014_T225_Id 
                                        LEFT JOIN T219 ON T219_Id=T225_T219_Id 
                                            WHERE T014_Id = '{$mT014['T014_Id']}'");
            $rowT225 = mysql_fetch_array($sqlT225);
            if(!empty($rowT225['T219_T006_Id'])){
                mysql_query("UPDATE T006 SET T006_T219_Id = '{$rowT225['T219_Id']}', T006_T225_Id = '{$rowT225['T225_Id']}' WHERE T006_Id = '{$rowT225['T219_T006_Id']}'");
                $erro = mysql_error();
                if (!empty($erro)) {
                    return 'Erro ao finalizar o estoque da nota fiscal.';
                }
            
                mysql_query("UPDATE T006 SET T006_T219_Id = '{$rowT225['T219_Id']}', T006_T225_Id = '{$rowT225['T225_Id']}' WHERE T006_T006_Id_Dividir = '{$rowT225['T219_T006_Id']}'");
                $erro = mysql_error();
                if (!empty($erro)) {
                    return 'Erro ao finalizar o estoque da nota fiscal.';
                }
            }
            //reprocessa histórico agora será executado no content em etapas - Lígia Nakazato
            //$CAD002->D001_reprocessa_historico(0, $mT014['T014_D009_Id']);
            
            mysql_query("UPDATE D049 SET D049_ICF=0, D049_Flag_ICF_Temporario='X' WHERE D049_Id='{$mT014['D049_Id']}' AND D049_Flag_ICF_Temporario='S'");
            $erro = mysql_error();
            if (!empty($erro)) {
                return 'Atualizando ICF Temporario';
            }
            
            /*
            $D083 = mysql_query("select D083_Id,D083_Flag_Fornecedor from D083 where D083_D001_Id='{$mT014['D049_D001_Id']}' and D083_Flag_Fornecedor='S' and D083_D049_Id='{$mT014['D049_Id']}' and D083_D024_Id='{$mT013['T013_D024_Id']}'");
            if (mysql_num_rows($D083) <= 0) {
                mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Fornecedor,D083_Codigo_Produto_Fornecedor) values ('{$mT014['D049_Id']}','{$mT014['D049_D001_Id']}','{$mT013['T013_D024_Id']}',curdate(),'S','{$mT014['T014_Codigo_Produto_Fornecedor']}')");
            } else {
                $mD083 = mysql_fetch_array($D083);
                mysql_query("update D083 set D083_Flag_Fornecedor='S',D083_Codigo_Produto_Fornecedor='{$mT014['T014_Codigo_Produto_Fornecedor']}' where D083_Id='{$mD083['D083_Id']}'");
            }
            */

            
            /* Caso a opção em Estoque->Nota de Entrada->"Cadastrar GTIN com nota de entrada" estiver ativa
            ao importar um XML de nota de entrada verifica se o produto tem GTIN(cEAN e cEANTrib) se tiver substitui
            no cadastro do produto(D001_Codigo_Barras e D001_Codigo_Barras_Tributavel), se não mantém o que já existe no produto. */
            if($g['C031']['cadastrarGtinApartirDeNF'] == 'S' AND !empty($mT014['D001_Id']))
            {
                $GTIN_D001_Id = $mT014['D001_Id'];
                $cEAN = $mT014['T014_Codigo_Barras'];
                $cEANTrib = $mT014['T014A_Codigo_Barras_Tributavel'];

                $clausula_cean = array();
                if(!empty($cEAN))
                {
                    $clausula_cean[] = "D001_Codigo_Barras = '$cEAN'";
                }

                if(!empty($cEANTrib))
                {
                    $clausula_cean[] = "D001_Codigo_Barras_Tributavel = '$cEANTrib'";
                }

                if(count($clausula_cean) > 0)
                {
                    $clausula_cean = implode(",", $clausula_cean);

                    $cClausula = "UPDATE D001 SET $clausula_cean WHERE D001_Id = '$GTIN_D001_Id'"; 
                    mysql_query($cClausula);
                    if($cErro = mysql_error())
                    {
                        $cErro = "Erro Cadastro GTIN query $cErro";
                        log($cErro);
                        return "Erro: " . $cErro . "\n" . $cClausula;
                    }
                }
            }   
            //Já realiza o processo mais a baixo
            //$CAD002->definirCodigoFornecedor($mT014['T014_D009_Id'], $mT013['T013_D024_Id'], $mT014['T014_Codigo_Produto_Fornecedor'], '', false, true);
            
        }

        //////////////////////////////////////////////////////////////////
        //                                                              //
        //       ATUALIZA CLASSIFICACOES DA NOTA RECEBIDA               //
        //                                                              //
        //////////////////////////////////////////////////////////////////
        $Empresa_Grupo = 'N';
        // COMPRA
        if (($mT013['D006_Flag_Entrada_Saida'] == 'E') && ($mT013['D006_Flag_Devolucao'] != 'S') && (($mT013['D006_Flag_Venda_Compra_Outros'] == 'C') || ($mT013['D006_Flag_Venda_Compra_Outros'] == 'T')) && ($mT013['D006_Flag_Gera_Contas'] == 'P')) {
            $Classificacao = 1;
        }

        // DEVOLUCAO VENDA
        if (($mT013['D006_Flag_Entrada_Saida'] == 'E') && ($mT013['D006_Flag_Venda_Maquina'] != 'S') && ($mT013['D006_Flag_Devolucao'] == 'S') && ($mT013['D006_Flag_Venda_Compra_Outros'] == 'C') && ($mT013['D024_Flag_Mono'] != '1') && ($Empresa_Grupo == 'N')) {
            $Classificacao = 2;
        }

        if ($D006_Flag_Estoque == 'N') {
            //Verificação ocorre antes de chamar o metodo
            //$Msg .= "<br />Não foram gerados movimentaçães no estoque";
        }
        //atualiza os codigos dos forncedores

        $T014 = mysql_query("select T014_Codigo_Produto,
                                  D001_Codigo_Produto,
                                  D001_Id,
                                  D049_Id,
                                  D024_Id,
                                  T014_D009_Id
                             from T014
                        LEFT JOIN D009 ON D009_Id=T014_D009_Id
                        left join D049 on D049_Id=D009_D049_Id
                        left join D001 on D001_Id=D049_D001_Id
                        left join T013 on T013_Id=T014_T013_Id
                        left join D024 on D024_Id=T013_D024_Id
                        where T014_T013_Id='$T013_Id'
                          and T014_Codigo_Produto!=D001_Codigo_Produto
                         group by T014_Codigo_Produto");
        while ($mT014 = mysql_fetch_array($T014)) {
            /*
            $D083 = mysql_query("select D083_Id,D083_Flag_Cliente from D083 where D083_D001_Id='{$mT014['D001_Id']}' and D083_D049_Id='{$mT014['D049_Id']}' and D083_D024_Id='{$mT014['D024_Id']}'");
            if (mysql_num_rows($D083) <= 0) {
                mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Fornecedor,D083_Codigo_Produto_Fornecedor) values ('{$mT014['D049_Id']}','{$mT014['D001_Id']}','{$mT014['D024_Id']}',curdate(),'S','{$mT014['T014_Codigo_Produto']}')");
            }
            */
            $CAD002->definirCodigoFornecedor($mT014['T014_D009_Id'], $mT014['D024_Id'], $mT014['T014_Codigo_Produto'], '', false, true);
        }

        if($mT013['T013_Flag_Finalidade_NFe']==1){
            // Cadastra os fornecedores dos produtos
            $T014 = mysql_query("SELECT D001_Id, 
                                        T013_D024_Id, 
                                        D006_Flag_Venda_Compra_Outros
                                   FROM T014 
                              LEFT JOIN D009 ON D009_Id=T014_D009_Id
                              LEFT JOIN D049 ON D049_Id=D009_D049_Id
                              LEFT JOIN D001 ON D001_Id=D049_D001_Id
                              LEFT JOIN T013 ON T013_Id=T014_T013_Id
                              LEFT JOIN T241 ON T241_D001_Id=D001_Id AND T241_D024_Id=T013_D024_Id
                              LEFT JOIN D006 ON T014_D006_Id=D006_Id
                                  WHERE T014_T013_Id = '{$T013_Id}'
                                    AND T241_Id IS NULL
                               GROUP BY D001_Id");
            while ($mT014 = mysql_fetch_array($T014)) {
                if($mT014['D006_Flag_Venda_Compra_Outros'] == 'C'){
                    $CAD002->cadastrarFornecedorProduto($mT014['D001_Id'], $mT014['T013_D024_Id']);
                }
            }
        }
        
        //-- PROCESSA AS PENDENCIAS
        // $Pendencias -> processarPendencias();
        if (!empty($Msg)) {
            return "Devido a Natureza da operação que foi selecionada," . $Msg;
        }

		$retornoAlterarOC = $this->alterarStatusOCVinculada($T013_Id);

        if($retornoAlterarOC!==true){
            return $retornoAlterarOC;
        }

        $this->atualizaLote($T013_Id);

        $alocarItensNaLocacao = $this->AlocarSaldoLocacao($D009_ID, $T014_ID, $T066_ID);

        return true;
    }

    /**
     * conversaoOrigemProdutoEntrada
     * 
     * @param int $origemEntrada
     * @return string
     */
    public function conversaoOrigemProdutoEntrada($origemEntrada)
    {

        $conversaoOrigem[0] = 0;
        $conversaoOrigem[1] = 1;
        $conversaoOrigem[2] = 2;
        $conversaoOrigem[3] = 3;
        $conversaoOrigem[4] = 4;
        $conversaoOrigem[5] = 5;
        $conversaoOrigem[6] = 6;
        $conversaoOrigem[7] = 7;
        $conversaoOrigem[8] = 8;

        $origemProduto = $conversaoOrigem[$origemEntrada];

        return $origemProduto;

    }

    /**
     * finalizarNotaEstoqueValidar
     *
     * @param int $T013_Id
     * @return true/string
     */
    public function finalizarNotaEstoqueValidar($T013_Id)
    {
        global $g;
        //-- Conexão com o banco de dados
        //require "old/lib/gravar_entidade.php3";

        //-- INCLUSAO DA CLASSE DE PROCESSAMENTOS
        //require "old/lib/processamentos.class.php3";

        // Classe produtos
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        if (empty($T013_Id)) {
            return "Erro: T013_Id não encontrado.";
        }

        $cSql_T013 = mysql_query("select T013_Flag_Finalidade_NFe,
                                         T013_Numero_Conhecimento,
                                         T013_D024_Id,
                                         T013_Numero_Nota_Fiscal,
                                         T013_D022_Id,
                                         T013_C004_Id,
                                         T013_Valor_Total,
                                         T013_Valor_Frete
                                    from T013
                               left join D006 on D006_Id=T013_D006_Id
                               left join D024 on D024_Id=T013_D024_Id
                                   where T013_Id='$T013_Id'");
        $mT013     = mysql_fetch_array($cSql_T013);

        $T014 = mysql_query("SELECT T014_Id FROM T014 WHERE T014_T013_Id='{$T013_Id}' AND T014_Flag_Conferencia_Cega!='S' AND T014_Valor_Custo_Unitario > 0 AND T014_Classificacao_Fiscal != '00000000'");
        if($g['C031']['conferenciaCegaCompras'] == 'S' && ($mT013['T013_Flag_Finalidade_NFe'] == '1' || $mT013['T013_Flag_Finalidade_NFe'] == '4')){
            if (mysql_num_rows($T014) > 0) {
                return "Antes de finalizar a nota é necessário fazer a conferência cega.";
            }
        }
        $retorno = $this->conferenciaMateriais($T013_Id);
        if(!$retorno['code'] ){
            return $retorno['data'];
        }
        
        $T014_T066 = mysql_query("SELECT T014_Id FROM T014 LEFT JOIN T066 ON T066_Id=T014_T066_Id LEFT JOIN D006 ON D006_Id = T014_D006_Id WHERE T014_T013_Id='{$T013_Id}' AND IFNULL(T066_D004_Id,0) <= 0 AND T014_Valor_Custo_Unitario > 0 AND T014_Classificacao_Fiscal != '00000000' AND D006_Flag_Estoque != 'N'");
        if (mysql_num_rows($T014_T066) > 0 && ($mT013['T013_Flag_Finalidade_NFe'] == '1' || $mT013['T013_Flag_Finalidade_NFe'] == '4')) {
            return "Existem locações não definidas ou em branco.";
        }

        // Valida Status OC´s vinculadas
        $retornoValidaOCVinculada = $this->validaStatusOCVinculada($T013_Id);

        if ($retorno!==true) {
            return $retornoValidaOCVinculada;
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VALIDACAO DO CONHECIMENTO
        ////////////////////////////////////////////////////////////////////////////////////////////////
        if (($mT013['T013_Numero_Conhecimento'] != 'NAO TEM') && ($mT013['T013_Numero_Conhecimento'] > 0)) {
            if (empty($mT013['T013_D022_Id'])) {
                return "VOCE DEVE SELECIONAR UMA TRANSPORTADORA";
            }

            $FechouConhecimento = 'S';

            $cSql_T085_1 = mysql_query("select * from T085 left join T084 on T084_Id=T085_T084_Id where T084_Numero_Conhecimento='{$mT013['T013_Numero_Conhecimento']}' and T084_D024_Id='{$mT013['T013_D024_Id']}' and T085_Numero_Nota_FIscal='{$mT013['T013_Numero_Nota_Fiscal']}'");
            if (mysql_num_rows($cSql_T085_1) == 0) {
                return "Esta nota fiscal não está lançada no conhecimento de transporte. Verifique se o conhecimento foi registrado e a nota lançada.";
            }

            while ($mT085 = mysql_fetch_array($cSql_T085_1)) {
                if ($mT013['T013_D022_Id'] != $mT085['T084_D022_Id']) {
                    return "A transportadora da nota fiscal deve ser a mesma do conhecimento de transporte";
                }

                $cSql_T085 = mysql_query("select SUM(T085_Valor_Rateio) as SomaRateio, (T084_Valor_Conhecimento+IFNULL(T084_Valor_ICMS_ST,0)) as Valor_Conhecimento
                                       from T085
                                       LEFT JOIN T084 ON T085_T084_Id=T084_Id 
                                       where T085_T084_Id='{$mT085['T085_T084_Id']}'");
                $resT085   = mysql_fetch_array($cSql_T085);
                if ($resT085['SomaRateio'] != $resT085['Valor_Conhecimento']) {
                    return "O valor total do conhecimento não confere com o valor dos rateios, verifique o valor de rateio das notas lançadas no conhecimento.";
                }

                $cSql_T085 = mysql_query("select T085_Id 
                                        from T085
                                        LEFT JOIN T084 ON T085_T084_Id=T084_Id 
                                        where T085_T084_Id='{$mT085['T085_T084_Id']}' 
                                        and T084_D024_Id!='{$mT013['T013_D024_Id']}' 
                                        and T085_Numero_Nota_FIscal!='{$mT013['T013_Numero_Nota_Fiscal']}'
                                        and T085_Flag_Fechado='N'");

                if (mysql_num_rows($cSql_T085) > 0) {

                    mysql_query("update T085 LEFT JOIN T084 ON T085_T084_Id=T084_Id set T085_Flag_Fechado='S' 
                                 where T085_T084_Id='{$mT085['T085_T084_Id']}'
                                 and T084_D024_Id='{$mT013['T013_D024_Id']}' 
                                 and T085_Numero_Nota_FIscal='{$mT013['T013_Numero_Nota_Fiscal']}'");
                    $FechouConhecimento = 'N';

                } else {
                    mysql_query("update T085 LEFT JOIN T084 ON T085_T084_Id=T084_Id set T085_Flag_Fechado='S' 
                                where T085_T084_Id='{$mT085['T085_T084_Id']}'
                                and T084_D024_Id='{$mT013['T013_D024_Id']}' 
                                and T085_Numero_Nota_FIscal='{$mT013['T013_Numero_Nota_Fiscal']}'");
                    mysql_query("update T084 set T084_Flag_Fechado='S' where T084_Id='{$mT085['T085_T084_Id']}'");
                }
            }

            if ($FechouConhecimento == 'N') {
                //-- CHAMA O FORMULARIO DE NOTAS PARA DIGITA��O DA PROXIMA NOTA FISCAL
                return "OK, Para finalizar esta nota, termine de lancar as outras notas que compoem este conhecimento";
                //echo "parent.location.replace('../monta/monta_formularios.php3?Entidade=T013&operacao=1&Codigo_Consulta=001&Id_Entidade_Principal=$mT013[T013_C008_Id]&Entidade_Principal=C008');";
            } else {
                //-- VERIFICA SE O CONHECIMENTO FOI LANCANDO CORRETAMENTE JUNTO COM A NOTA FISCAL
                if (empty($mT013['T013_D022_Id'])) {
                    return "VOCE DEVE SELECIONAR UMA TRANSPORTADORA";
                }
                if (empty($mT013['T013_D024_Id'])) {
                    return "VOCE DEVE SELECIONAR UM CLIENTE";
                }
                if (empty($mT013['T013_Numero_Conhecimento'])) {
                    return "Digite o numero do conhecimento";
                }

                $cSql_D022 = mysql_query("select D022_Id,D022_Nome_Empresa from D022 where D022_Id='{$mT013['T013_D022_Id']}'");
                $mD022     = mysql_fetch_array($cSql_D022);
                $cSql_D024 = mysql_query("select D024_Id,D024_Nome_Empresa from D024 WHERE D024_Id='{$mT013['T013_D024_Id']}'");
                $mD024     = mysql_fetch_array($cSql_D024);

                $mNumero_Conhecimento = explode(',', $mT013['T013_Numero_Conhecimento']);
                $nT013_Valor_Frete    = 0;

                for ($x = 0; $x < count($mNumero_Conhecimento); $x++) {
                    $Clausula = "select T084_Id,T084_Flag_Cancelado ";
                    $Clausula .= "from T084 ";
                    $Clausula .= "where T084_Numero_Conhecimento='$mNumero_Conhecimento[$x]' ";
                    if (count($mNumero_Conhecimento) == 1) {
                        $Clausula .= "and T084_D022_Id='{$mT013['T013_D022_Id']}' ";
                    }
                    $Clausula .= "and T084_Flag_Cancelado!='S' ";
                    $Clausula .= "and T084_D024_Id='{$mT013['T013_D024_Id']}' ";
                    $Clausula .= "and T084_C004_Id='{$mT013['T013_C004_Id']}' ";

                    $cSql_T084 = mysql_query($Clausula);
                    $mT084     = mysql_fetch_array($cSql_T084);
                    if ($mT084['T084_Flag_Cancelado'] == 'S') {
                        return "CONHECIMENTO " . $mNumero_Conhecimento[$x] . "ESTA CANCELADO";
                    }
                    if (mysql_num_rows($cSql_T084) == 0) {
                        $msg = "CONHECIMENTO N�O ENCONTRADO!<br ></count>";
                        $msg .= "Verifique se existe o conhecimento: " . $mNumero_Conhecimento[$x] . "<br />";
                        if ($x == 1) {
                            $msg .= "para a transportadora: {$mD022['D022_Id']} - " . substr($mD022['D022_Nome_Empresa'], 0, 20) . " e<br >";
                        }
                        $msg .= "para fornecedor:  {$mD024['D024_Id']} - " . substr($mD024['D024_Nome_Empresa'], 0, 20) . "<br />";
                        $msg .= "na tela de Conhecimentos.<br />";
                        return $msg;
                    }

                    $cSql_T085 = mysql_query("select T085_Valor_Rateio,
                                                    T085_Valor_Total_Nota,
                                                    T085_Id,
                                                    T085_Flag_Fechado
                                            from T085
                                            where T085_T084_Id='{$mT084['T084_Id']}'
                                            and T085_Numero_Nota_Fiscal='{$mT013['T013_Numero_Nota_Fiscal']}'");
                    $mT085     = mysql_fetch_array($cSql_T085);
                    if (mysql_num_rows($cSql_T085) == 0) {
                        $msg = "NOTA FISCAL NÃO ENCONTRADA PARA ESTE CONHECIMENTO!<br />";
                        $msg .= "Verifique se no conhecimento: " . $mNumero_Conhecimento[$x] . "<br />";
                        $msg .= "existe a nota fiscal: " . $mT013['T013_Numero_Nota_Fiscal'];
                        return $msg;
                    }
                    if ($mT085['T085_Valor_Total_Nota'] != $mT013['T013_Valor_Total']) {
                        return "VALOR TOTAL DA NOTA NAO CONFERE COM OS LANCAMENTOS DO CONHECIMENTO " . $mNumero_Conhecimento[$x];
                    }
                    $nT013_Valor_Frete += $mT085['T085_Valor_Rateio'];
                }

                //$nDiferenca_Frete = abs(bcsub($mT013['T013_Valor_Frete'], $nT013_Valor_Frete, 2));
                $nDiferenca_Frete = abs(number_format($mT013['T013_Valor_Frete'],2) - number_format($nT013_Valor_Frete, 2));

                if ($nDiferenca_Frete > 0) {
                    return 'VALOR DO FRETE NÃO CONFERE COM O CONHECIMENTO, VERIFIQUE SE O VALOR DO FRETE NESTA NOTA CONFERE COM O VALOR DO RATEIO DE FRETE LANÇADO NO CONHECIMENTO PARA ESTA NOTA.';
                }
            }
        }

        return true;

    }

    /**
     * validaStatusOCVinculada
     * Método que verifica se todos os produtos da NF de entrada estão vinculados a uma OC
     *
     * @param string $T224_Id
    */
    public function validaStatusOCVinculada($T013_Id=0){
        global $g;

        if ($T013_Id>0){
            $where = "WHERE T013_Id = '{$T013_Id}'";
        }else{
            return "Erro ao alterar status OC.<br>Contate o suporte técnico e informe o seguinte erro: Necessário passar o ID da NF-e.";
        }

        $sqlT224 = mysql_query("SELECT T224_Id,
                                        T224_Flag_Status,
                                        T224_Status_Pedido(T224_Flag_Status, T224_Id) as Status, 
                                        ROUND(SUM(DISTINCT CONCAT(IFNULL(T2.T014_Quantidade,'0.00000'),T2.T014_Id))/SUM(DISTINCT CONCAT(T225_Quantidade,T225_Id)),2)*100 AS Percent 
                                FROM T013 
                                LEFT JOIN T014 AS T1 ON T1.T014_T013_Id = T013_Id
                                LEFT JOIN T224 ON (SELECT T225_T224_Id FROM T225 WHERE T1.T014_T225_Id = T225_Id) = T224_Id
                                LEFT JOIN T225 ON T225_T224_Id = T224_Id AND T225_Flag_Cancelada != 'S'
                                LEFT JOIN T014 AS T2 ON T2.T014_T225_Id = T225_Id AND T2.T014_Flag_Cancelada != 'S'
                                {$where}
                                GROUP BY T224_Id");
        

        while($rowT224 = mysql_fetch_array($sqlT224)){

            if ($rowT224['T224_Id']<=0){
                continue;
            }
            
            // Se o status for 5 (Coletar) ou 6 (Coletando) não deixa finalizar a nota, é necessário primeiro concluir a coleta da OC
            if($rowT224['T224_Flag_Status']=='6' || $rowT224['T224_Flag_Status']=='5'){
                return "A OC ".$rowT224['T224_Id']." está com o status ".$rowT224['Status'].". É necessário primeiro concluir a coleta da OC para depois {$mensagem}.";
            }
            if($rowT224['T224_Flag_Status']=='10'){
                return "OC já está finalizada, não é permitido finalizar novamente.";
            }
        }

        return true;
    }

    /**
     * alterarStatusOCVinculada
     * Método que verifica se todos os produtos da NF de entrada estão vinculados a uma OC e altera status da OC.
     *
     * @param int $T013_Id
     * @param string $T224_Id
    */
    public function alterarStatusOCVinculada($T013_Id=0,$T224_Id=0){
        global $g;

        if($T013_Id>0){
            $sqlT224 = mysql_query("SELECT T224_Id,
                                           T224_Flag_Status,
                                           T224_Status_Pedido(T224_Flag_Status, T224_Id) as Status, 
                                           T224_Valor_Pendente,
                                           ROUND(SUM(DISTINCT CONCAT(IFNULL(T2.T014_Quantidade,'0.00000'),T2.T014_Id))/SUM(DISTINCT CONCAT(T225_Quantidade,T225_Id)),2)*100 AS Percent 
                                      FROM T013 
                                 LEFT JOIN T014 AS T1 ON T1.T014_T013_Id = T013_Id
                                 LEFT JOIN T224 ON (SELECT T225_T224_Id FROM T225 WHERE T1.T014_T225_Id = T225_Id) = T224_Id
                                 LEFT JOIN T225 ON T225_T224_Id = T224_Id AND T225_Flag_Cancelada != 'S'
                                 LEFT JOIN T014 AS T2 ON T2.T014_T225_Id = T225_Id AND T2.T014_Flag_Cancelada != 'S' AND (SELECT T013_Flag_Estoque_Finalizado FROM T013 WHERE T013_Id = T2.T014_T013_Id ) = 'S'
                                     WHERE T013_Id = '{$T013_Id}'
                                  GROUP BY T224_Id");       
            $mensagem = "finalizar o estoque da nota fiscal";
        }else if($T224_Id>0){
            $sqlT224 = mysql_query("SELECT T224_Id,
                                           T224_Flag_Status,
                                           T224_Status_Pedido(T224_Flag_Status, T224_Id) AS STATUS,
                                           T224_Valor_Pendente,
                                           ROUND(SUM(DISTINCT CONCAT(IFNULL(T014_Quantidade,'0.00000'),T014_Id))/SUM(DISTINCT CONCAT(T225_Quantidade,T225_Id)),2)*100 AS Percent 
                                      FROM T225
                                 LEFT JOIN T014 ON T014_T225_Id = T225_Id
                                 LEFT JOIN T224 ON T224_Id = T225_T224_Id
                                     WHERE T224_Id='{$T224_Id}'
                                       AND T014_Flag_Cancelada != 'S'
                                       AND T225_Flag_Cancelada != 'S'
                                  GROUP BY T224_Id");
            $mensagem = "finalizar OC manualmente";
        }else{
            return "Erro ao alterar status OC.<br>Contate o suporte técnico e informe o seguinte erro: Necessário passar o ID da NF ou ID da OC.";
        }

        $retorno = "Não é possível finalizar a OC:<br><br> Existem produtos sem vinculo com NF de entrada.";        

        while($rowT224 = mysql_fetch_array($sqlT224)){

            // Se o status for 5 (Coletar) ou 6 (Coletando) não deixa finalizar a nota, é necessário primeiro concluir a coleta da OC
            if($rowT224['T224_Flag_Status']=='6' || $rowT224['T224_Flag_Status']=='5'){
                return "A OC ".$rowT224['T224_Id']." está com o status ".$rowT224['Status'].". É necessário primeiro concluir a coleta da OC para depois {$mensagem}.";
            }
            if($rowT224['T224_Flag_Status']=='10'){
                return "OC já está finalizada, não é permitido finalizar novamente.";
            }

            if($rowT224['T224_Valor_Pendente'] > 0 && $T224_Id > 0){
                // Retornar essa mensagem quando o usuário tenta finalizar manual a OC
                // Quando está finalizando o estoque da NF passa por essa função também, mas não tem problema ter saldo disponível na OC, pois podem vir outras notas para abater esse saldo
                return "OC possui valor pendente, favor verificar.";
            }

            if($rowT224['Percent'] >= 100){
                $status = ($T224_Id > 0) ? '13' : '10';
                mysql_query("UPDATE T224 SET T224_Flag_Status = '{$status}', T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id) WHERE T224_Id = '{$rowT224['T224_Id']}'");
                $erro = mysql_error();
                if (!empty($erro)) {
                    return "Erro ao {$mensagem}<br><br>{$erro}";
                }
               $retorno = "OC Nº {$rowT224['T224_Id']} finalizada com sucesso";
            } else {
                mysql_query("UPDATE T224 SET T224_Flag_Status = '9', T224_Nome_Status = T224_Status_Pedido(T224_Flag_Status, T224_Id) WHERE T224_Id = '{$rowT224['T224_Id']}'");
                $erro = mysql_error();
                if (!empty($erro)) {
                    return "Erro ao finalizar o {$mensagem}<br><br> {$erro}";
                }
            }
        }

        if($T224_Id>0){
            return $retorno;
        }else{
            return true;
        }
    }

    /**
     * Preencher Locação
     * Método que preenche a locação de todos os produtos da nota fiscal, exceto aqueles que possuem mais de uma locação.
     *
     * @param int $T013_Id
     */
    public function preencherLocacao($T013_Id)
    {
        // busca se o produto possui mais de uma locação
        $T014 = mysql_query("SELECT D004_Id, 
                              COUNT(DISTINCT(T066_Id)) as variasLocacoes,
                                    D001_Codigo_Produto, 
                                    T066_Id,
                                    T014_Id,
                                    D001_Id
                               FROM T014
                          LEFT JOIN D009 ON T014_D009_Id=D009_Id
                          LEFT JOIN D049 ON D009_D049_Id=D049_Id
                          LEFT JOIN D001 ON D049_D001_Id=D001_ID
                          LEFT JOIN T066 ON T066_D009_id=D009_Id
                          LEFT JOIN D004 ON D004_Id=T066_D004_Id
                              WHERE T014_T013_Id='$T013_Id' 
                                AND D009_Id IS NOT NULL
                                AND IFNULL(T014_T066_Id,0) <= 0 
                           GROUP BY T014_Id, D001_Id");
        
        $cErro=mysql_error($T014);
        if (!empty($cErro)) {
            return "Erro: $cErro <br /> $cClausula";
        }
        $errosVariasLocacoes = "";
        $erro = "";
        $cont=0;
        $D001 = array();
        if(mysql_num_rows($T014) < 1){
            return "Todos produtos estão com locação.";
        }
        while($mT014 = mysql_fetch_array($T014)){
            // Verifica se o produto possui mais de uma locações, pois se ele possui não dará pra preencher a locação
            if($mT014['variasLocacoes'] > 1){
                $erro = "O Produto ". $mT014['D001_Codigo_Produto'] . " <b>possui mais de uma locação</b>, necessário selecionar manualmente.<br/>";
            }else if(is_null($mT014['T066_Id'])){
                $erro = "O Produto ". $mT014['D001_Codigo_Produto'] . " <b>não possui locação</b>, necessário cadastrar locação.<br/>";
            }else if($mT014['variasLocacoes'] == 1 AND $mT014['T066_Id'] > 0){
                mysql_query("UPDATE T014 SET
                                    T014_T066_Id='{$mT014['T066_Id']}',
                                    T014_D004_Id='{$mT014['D004_Id']}'
                                    WHERE T014_Id='{$mT014['T014_Id']}'
                            ");
                $cErro=mysql_error();
                if (!empty($cErro)) {
                    return "Erro: Não foi possível preencher locações <br/>$cErro <br />";
                }else{
                    $cont++;
                }

            }

            if(!empty($erro) && !in_array($mT014['D001_Id'], $D001)) {
                $errosVariasLocacoes .= $erro;
                $D001[] = $mT014['D001_Id'];
                $erro = "";
            }
        }
                
        if(!empty($errosVariasLocacoes)){
            $errosVariasLocacoes ="<span style='font-weight: bold;font-size: 14px;'>Locações preenchidas: ".$cont."</span></br></br><span style='font-weight: bold; color: red;'> Não foi possível preencher a locação dos produtos abaixo:</span></br></br>".$errosVariasLocacoes;
            return $errosVariasLocacoes;
        }else{
            
            return true;
        } 
    }   


    /**
     * Preencher Locação
     * Método que preenche a locação de todos os produtos da nota fiscal, exceto aqueles que possuem mais de uma locação.
     *
     * @param int $T013_Id
     */
    public function popularOCNotaFiscal($T013_Id, $D024_Id)
    {
        global $g;

        // busca se o produto possui mais de uma locação
        $T014 = mysql_query("SELECT T014_Id, 
                                    D009_Id,
                                    T014_Valor_Custo_Unitario,
                                    T014_Quantidade,
                                    T014_Classificacao_Fiscal,
                                    T014_Descricao_Produto,
                                    T224_Id,
                                    T014_T225_Id
                               FROM T014
                          LEFT JOIN D009 ON T014_D009_Id=D009_Id
                          LEFT JOIN D049 ON D009_D049_Id=D049_Id
                          LEFT JOIN D001 ON D049_D001_Id=D001_ID
                          LEFT JOIN T013 ON T013_Id=T014_T013_Id
                          LEFT JOIN T224 ON T224_Id=TRIM(LEADING 0 FROM TRIM(REGEX_REPLACE('[^0-9 ]','',T014_Numero_Pedido_Compra))) AND T224_D024_Id=T013_D024_Id
                              WHERE T014_T013_Id='$T013_Id'");
        
        $erro=mysql_error();
        if (!empty($erro)) {
            return "Erro: $erro";
        }

        $T225_Sql ="select D009_Id,
                                    T225_Valor_Preco_Sem_Desconto_Unitario,
                                    T225_Quantidade_Pendente_3(T225_Id) as pendente,
                                    T225_D006_Id,
                                    T225_Id,
                                    T224_Id,
                                    T014_Quantidade,
                                    D005_Classificacao_Fiscal,
                                    D001_Descricao_Produto
                               from T225    
                          left join T224 on T224_Id=T225_T224_Id
                          left join D009 on T225_D009_Id=D009_Id
                          left join D049 on D049_Id=D009_D049_Id
                          left join D082 on D082_Id=D049_D082_Id
                          left join D001 on D049_D001_Id=D001_Id
                          left join D083 on D049_Id=D083_D049_Id and D083_D024_Id='{$D024_Id}'
                          left join T014 on T014_T225_Id=T225_Id
                          left join C007 on C007_Id=T224_C007_Id_Vendedor_Interno
                          left join D005 on D005_Id=T225_D005_Id
                              where T224_C004_Id='{$g['empresaAtual']}'
                                and D009_C004_Id='{$g['empresaAtual']}'
                                and (T224_Flag_Status_Liberacao = '2')
                                and T224_Flag_Status != '10'
                                and T225_Flag_Cancelada!='S'
                                and T224_D024_Id='{$D024_Id}'
                                and (ifnull(T225_Quantidade_Pendente_3(T225_Id),0) > 0 OR ifnull(T014_Flag_Divergencia,'N') = 'S')
                                and T224_Data_Emissao>=date_sub(current_date(), interval 6 month)
                           group by T225_Id
                           order by T224_Id,T225_Descricao_Produto";

        $erro=mysql_error();
        if (!empty($erro)) {
            return "Erro: $erro";
        }

        $erros = '';

        require_once('bibliotecas/classes/GFormWrap.php');
        while ($mT014 = mysql_fetch_array($T014)) {
            if ($mT014['T014_T225_Id']>0) {
                // Já possui vinculo com uma OC
                continue;
            }
            $T225 = mysql_query($T225_Sql);
            while ($mT225 = mysql_fetch_array($T225)) {
                if ($mT014['D009_Id']<=0) {
                    if ($mT014['T014_Classificacao_Fiscal']==$mT225['D005_Classificacao_Fiscal']){
                        if (gCorrigeNumero($mT014['T014_Valor_Custo_Unitario'])==gCorrigeNumero($mT225['T225_Valor_Preco_Sem_Desconto_Unitario'])){
                            if (gCorrigeNumero($mT014['T014_Quantidade'])==gCorrigeNumero($mT225['pendente'])){
                                similar_text($mT014['T014_Descricao_Produto'],$mT225['D001_Descricao_Produto'],$percentualDescricao);
                                if ($percentualDescricao>50){
                                    $form = new GFormWrap(array('D009_Id' => $mT225['D009_Id'], 'T014_Id' => $mT014['T014_Id']));
                                    $retorno = $this->associarProduto($form);
                                    if ($retorno!==true){
                                        $erros .= "Erro ao vincular produto $retorno";
                                    } else {
                                        $mT014['D009_Id'] = $mT225['D009_Id'];
                                    }
                                }
                            }
                        }
                    }
                }
                if ($mT014['D009_Id'] == $mT225['D009_Id']) {
                    // Verifica se existe OC no XML
                    if (!empty($mT014['T224_Id']) && $mT014['T224_Id']!=$mT225['T224_Id']) {
                        continue;
                    }
                    // Associa o item da nota com o item da OC
                    if (($mT014['T014_Quantidade']<=$mT225['pendente']) || ($g['C031']['vincularProdutoNFRecebidaOCQtdDiferentes']=="S")){
                        if($g['C031']['associarCfopOcParaNf'] == 'S'){
                            mysql_query("UPDATE T014 SET T014_T225_Id='{$mT225['T225_Id']}', T014_Flag_Divergencia='S', T014_D006_Id = '{$mT225['T225_D006_Id']}' WHERE T014_Id='{$mT014['T014_Id']}'");
                        } else {
                            mysql_query("UPDATE T014 SET T014_T225_Id='{$mT225['T225_Id']}', T014_Flag_Divergencia='S' WHERE T014_Id='{$mT014['T014_Id']}'");
                        }
                        mysql_query("UPDATE T225 SET T225_Quantidade_Recebida='{$mT014['T014_Quantidade']}' WHERE T225_Id='{$mT225['T225_Id']}'");
                        break;
                    }
                }
             }
             mysql_data_seek($T225, 0);
        }
        
        if (!empty($erros)){
            return "Erros $erros";
        } else {
            return true;
        }

    }   

    public function insereRegistroT001($T014_Id)
    {

        ///////////////////////////////////////////////////////////////////////////////////
        //-- LANCAMENTO NO HISTORICO E NO ESTOQUE
        ///////////////////////////////////////////////////////////////////////////////////
        $cSql_T014 = mysql_query("select *
                                      from T014
                                 left join D009 on D009_Id=T014_D009_Id
                                 left join D049 on D049_Id=D009_D049_Id
                                 left join D001 on D001_Id=D049_D001_Id
                                 left join D006 on D006_Id=T014_D006_Id
                                 left join T001 on T001_T014_Id=T014_Id
                                    where T014_Id='$T014_Id'
                                      AND T001_Id IS NULL");

        $mT014 = mysql_fetch_array($cSql_T014);

        $cSql_T013 = mysql_query("select * 
                                      from T013 
                                 left join D006 on D006_Id=T013_D006_Id
                                 left join D024 on D024_Id=T013_D024_Id
                                     where T013_Id='{$mT014['T014_T013_Id']}'");
        $mT013     = mysql_fetch_array($cSql_T013);
      
        $D006_Flag_Estoque = "N";
        //Verifica se o item foi duplicado na entrada da nota fiscal, caso o item tenha sido duplicado, o calculo unitário dos impostos deve ser feito sobre a quantidade total do produto na nota fiscal
        // !!! Se o item já veio duplicado na nota fiscal (XML), o valor unitario do impostos não precisam ser calculados sobre a quantidade total do produto na nota fiscal !!!
        $sqlItemDuplicado = mysql_query("SELECT * FROM T014 WHERE T014_Id_Duplicado = '{$mT014['T014_Id']}'");
        if(mysql_num_rows($sqlItemDuplicado) > 0 || $mT014['T014_Id_Duplicado'] > 0){
            $sqlQtdSoma = "SELECT SUM(T014_Quantidade) as T014_Quantidade FROM T014 WHERE T014_T013_Id = '{$mT014['T014_T013_Id']}' AND T014_D009_Id = '{$mT014['T014_D009_Id']}'";
            $resQtdSoma = mysql_query($sqlQtdSoma);
            $mQtdSoma = mysql_fetch_assoc($resQtdSoma);
            $QtdSoma = $mQtdSoma['T014_Quantidade'];
        } else {
            $QtdSoma = $mT014['T014_Quantidade'];
        }

        ///////////////////////////////////////////////////////////////////////////////////
        // ENCONTRA O ID DO T066
        ///////////////////////////////////////////////////////////////////////////////////
        $cSql_T066 = mysql_query("select T066_Id from T066 where T066_D048_Id='$D048_Id' and T066_D009_Id='{$mT014['T014_D009_Id']}'");
        $mT066     = mysql_fetch_array($cSql_T066);

        ///////////////////////////////////////////////////////////////////////////////////
        // MOVIMENTA��O DO ESTOQUE
        ///////////////////////////////////////////////////////////////////////////////////
        if ($mT014['D006_Flag_Estoque'] == 'D') {
            $Flag_Operacao_Historico = "S";
            $D006_Flag_Estoque       = "S";
        } elseif ($mT014['D006_Flag_Estoque'] == 'C') {
            $Flag_Operacao_Historico = "E";
            $D006_Flag_Estoque       = "S";
        } elseif ($mT014['D006_Flag_Estoque'] == 'N') {
            $Flag_Operacao_Historico = "N";
        }

        $QtdSoma = ($QtdSoma < 0) ? 1 : $QtdSoma;
        unset($mDemonstrativo_Custo);
        $mDemonstrativo_Custo[] = " ,,Preço,," . number_format($mT014['T014_Valor_Custo_Unitario'], 2, ',', '.');
        $mDemonstrativo_Custo[] = "-,,Desconto nos Itens,," . number_format($mT014['T014_Valor_Desconto_Unitario'], 2, ',', '.');
        $mDemonstrativo_Custo[] = " ,,Preço Liquido,," . number_format($mT014['T014_Valor_Custo_Unitario'] - $mT014['T014_Valor_Desconto_Unitario'], 2, ',', '.');
        if ($mC004['C004_Flag_Empresa_Simples'] != '1') {
            $mDemonstrativo_Custo[] = "-,,{$mT014['T014_Aliquota_ICMS']}% ICMS,," . number_format($mT014['T014_Valor_ICMS'] / $QtdSoma, 2, ',', '.');
        }

        if ($mT014['D006_Flag_Credita_IPI'] != 'S') {
            $mDemonstrativo_Custo[] = "+,,{$mT014['T014_Aliquota_IPI']}% IPI,," . number_format($mT014['T014_Valor_IPI'] / $QtdSoma, 2, ',', '.');
        }
        if ($mT013['D006_Flag_Substituicao_Tributaria'] == 'S' && $mT013['D006_Flag_Venda_Compra_Outros'] == 'T' && $mT013['T013_C004_Id'] == 2) {
            $mDemonstrativo_Custo[] = " ,,ICMS Substituição,," . number_format($mT014['T014_Valor_ICMS_Substituicao_Unitario'], 2, ',', '.');
        } else {
            $mDemonstrativo_Custo[] = "+,,ICMS Substituição,," . number_format($mT014['T014_Valor_ICMS_Substituicao_Unitario'], 2, ',', '.');
        }
        $mDemonstrativo_Custo[] = "+,,Frete,," . number_format($mT014['T014_Valor_Frete'] / $QtdSoma, 2, ',', '.');
        $mDemonstrativo_Custo[] = "+,,Seguro,," . number_format($mT014['T014_Valor_Seguro'] / $QtdSoma, 2, ',', '.');
        $mDemonstrativo_Custo[] = "+,,Despesas Acessórias,," . number_format($mT014['T014_Valor_Despesas_Acessorias'] / $QtdSoma, 2, ',', '.');

        $mDemonstrativo_Custo[] = "=,,Custo Final,," . number_format($mT014['T014_Valor_Custo_Final_Unitario'], 2, ',', '.');

        if ($mT014['D049_Flag_Nacional_Importado'] == 'I') {
            $mT014['T014_Valor_Custo_Final_Unitario'] = $mT014['T014_Valor_Custo_Final_Unitario'] / $mT013['T013_Valor_Dolar'];
            $mDemonstrativo_Custo[]                   = "=,,Dólar,," . number_format($mT013['T013_Valor_Dolar'], 4, ',', '.');
            $mDemonstrativo_Custo[]                   = "=,,Custo Final US,," . number_format($mT014['T014_Valor_Custo_Final_Unitario'], 2, ',', '.');
        }
        
        $mDemonstrativo_Custo[] = "";
        $mDemonstrativo_Custo[] = ",,% Redução ICMS,," . number_format($mT014['T014_Percentual_Reducao_ICMS'], 2, ',', '.');
        $mDemonstrativo_Custo[] = ",,Base ICMS,," . number_format($mT014['T014_Valor_Base_ICMS'] / $QtdSoma, 2, ',', '.');

        $mDemonstrativo_Custo[] = "";
        $mDemonstrativo_Custo[] = ",,% Redução IPI,," . number_format($mT014['T014_Percentual_Reducao_IPI'], 2, ',', '.');
        $mDemonstrativo_Custo[] = ",,Base IPI,," . number_format($mT014['T014_Valor_Base_IPI'] / $QtdSoma, 2, ',', '.');

        $Demonstrativo_Custo = implode("\n", $mDemonstrativo_Custo);

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // HISTORICO DO PRODUTO
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // '{$mT066['T066_Id']}',

        $valorICMSUnitario     = $mT014['T014_Valor_ICMS'] / $QtdSoma;
        $valorIPIUnitario      = $mT014['T014_Valor_IPI'] / $QtdSoma;
        $valorSTUnitario       = $mT014['T014_Valor_ICMS_Substituicao'] / $QtdSoma;
        $valorFreteUnitario    = $mT014['T014_Valor_Frete'] / $QtdSoma;
        $valorSeguroUnitario   = $mT014['T014_Valor_Seguro'] / $QtdSoma;
        $valorDespesasUnitario = $mT014['T014_Valor_Despesas_Acessorias'] / $QtdSoma;
        $valorPISUnitario      = $mT014['T014_Valor_PIS'] / $QtdSoma;
        $valorCOFINSUnitario   = $mT014['T014_Valor_COFINS'] / $QtdSoma;
        $valorICMSUnitario     = empty($valorICMSUnitario) ? 0 : $valorICMSUnitario;
        $valorIPIUnitario      = empty($valorIPIUnitario) ? 0 : $valorIPIUnitario;
        $valorSTUnitario       = empty($valorSTUnitario) ? 0 : $valorSTUnitario;
        $valorFreteUnitario    = empty($valorFreteUnitario) ? 0 : $valorFreteUnitario;
        $valorSeguroUnitario   = empty($valorSeguroUnitario) ? 0 : $valorSeguroUnitario;
        $valorDespesasUnitario = empty($valorDespesasUnitario) ? 0 : $valorDespesasUnitario;
        $valorPISUnitario      = empty($valorPISUnitario) ? 0 : $valorPISUnitario;
        $valorCOFINSUnitario   = empty($valorCOFINSUnitario) ? 0 : $valorCOFINSUnitario;

        $sqlT001 = mysql_query("SELECT * FROM T001 WHERE T001_D009_Id = '{$mT014['T014_D009_Id']}' AND T001_T014_Id = '{$mT014['T014_Id']}' AND T001_Flag_Operacao != 'C'");
        if(mysql_num_rows($sqlT001) > 0){
            return "Erro ao inserir histórico do produto. O produto {$mT014['D001_Codigo_Produto']} já possui um registro no histórico para esta nota fiscal.";
        }

        $mT014['T014_Valor_ICMS_Diferenca_Aliquotas'] = $mT014['T014_Valor_ICMS_Diferenca_Aliquotas'] / $mT014['T014_Quantidade'];
        $mT014['T014_Aliquota_ICMS_Diferenca_Aliquotas'] = $mT014['T014_Aliquota_ICMS_Diferenca_Aliquotas'];

        $tmpD004_Id = getD004($mT014['T014_T066_Id']);
        $T001_Id    = mysql_query("insert into T001 (T001_D024_Id,
                                                            T001_T014_Id,
                                                            T001_T013_Id,
                                                            T001_D009_Id,
                                                            T001_C004_Id,
                                                            T001_D006_Id,
                                                            T001_T066_Id,
                                                            T001_Codigo_Produto,
                                                            T001_Data_Lancamento,
                                                            T001_Descricao_Operacao,
                                                            T001_Quantidade,
                                                            T001_Flag_Operacao,
                                                            T001_Valor_Preco_Unitario,
                                                            T001_Valor_Custo_Unitario,
                                                            T001_Flag_Atualiza_Custo,
                                                            T001_Quantidade_Saldo,
                                                            T001_Demonstrativo_Custo,
                                                            T001_Flag_Devolucao,
                                                            T001_Valor_ST_Unitario,
                                                            T001_Valor_ICMS,
                                                            T001_Aliquota_ICMS,
                                                            T001_Valor_IPI,
                                                            T001_Aliquota_IPI,
                                                            T001_Valor_ST,
                                                            T001_Percentual_ST,
                                                            T001_Valor_Frete,
                                                            T001_Valor_Seguro,
                                                            T001_Valor_Despesas_Acessorias,
                                                            T001_Valor_Desconto_Unitario,
                                                            T001_Valor_ICMS_Diferenca_Aliquotas,
                                                            T001_Aliquota_PIS,
                                                            T001_Valor_PIS,
                                                            T001_Aliquota_COFINS,
                                                            T001_Valor_COFINS,
                                                            T001_D004_Id,
                                                            T001_Aliquota_ICMS_Diferenca_Aliquotas

                                                            )
                                                            values 
                                                            ('{$mT013['T013_D024_Id']}',
                                                            '{$mT014['T014_Id']}',
                                                            '{$mT013['T013_Id']}',
                                                            '{$mT014['T014_D009_Id']}',
                                                            '{$mT014['T014_C004_Id']}',
                                                            '{$mT014['T014_D006_Id']}',
                                                            '{$mT014['T014_T066_Id']}',
                                                            '{$mT014['T014_Codigo_Produto']}',
                                                            curdate(),
                                                            'NF-{$mT013['T013_Numero_Nota_Fiscal']}',
                                                            '{$mT014['T014_Quantidade']}',
                                                            '$Flag_Operacao_Historico',
                                                            '{$mT014['T014_Valor_Custo_Unitario']}',
                                                            '{$mT014['T014_Valor_Custo_Final_Unitario']}',
                                                            '{$mT014['D006_Flag_Atualiza_Custo']}',
                                                            '0',
                                                            '$Demonstrativo_Custo',
                                                            '{$mT014['D006_Flag_Devolucao']}',
                                                            '{$mT014['T014_Valor_ICMS_Substituicao_Unitario']}',
                                                            '{$valorICMSUnitario}',
                                                            '{$mT014['T014_Aliquota_ICMS']}',
                                                            '{$valorIPIUnitario}',
                                                            '{$mT014['T014_Aliquota_IPI']}',
                                                            '{$valorSTUnitario}',
                                                            (T001_Valor_ST/(T001_Valor_Preco_Unitario+$valorIPIUnitario))*100,
                                                            '{$valorFreteUnitario}',
                                                            '{$valorSeguroUnitario}',
                                                            '{$valorDespesasUnitario}',
                                                            '{$mT014['T014_Valor_Desconto_Unitario']}',
                                                            '{$mT014['T014_Valor_ICMS_Diferenca_Aliquotas']}',
                                                            '{$mT014['T014_Aliquota_PIS']}',
                                                            '{$valorPISUnitario}',
                                                            '{$mT014['T014_Aliquota_COFINS']}',
                                                            '{$valorCOFINSUnitario}',
                                                            '{$tmpD004_Id}',
                                                            '{$mT014['T014_Aliquota_ICMS_Diferenca_Aliquotas']}'
                                                            )");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return 'Erro ao inserir o histórico do produto. <br />Erro: '.$erro;
        }

        if (strlen($mT014['T014_Flag_Origem_Produto']) > 0 and $mT014['D006_Flag_Venda_Compra_Outros'] == 'C' && $g['C031']['atualizarOrigemProdutoComNotaRecebida'] == 'S') {
            mysql_query("UPDATE D049 LEFT JOIN D009 ON D009_D049_Id = D049_Id SET D049_Origem_Mercadoria = '{$mT014['T014_Flag_Origem_Produto']}'  WHERE D009_Id = '{$mT014['T014_D009_Id']}'");

            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return 'Erro ao finalizar o estoque da nota fiscal.';
            }
        }

        $sqlT225 = mysql_query("SELECT IFNULL(T219_T006_Id,0) as T219_T006_Id, T225_Id, T219_Id FROM T014 LEFT JOIN T225 ON T225_Id=T014_T225_Id LEFT JOIN T219 ON T219_Id=T225_T219_Id WHERE T014_Id = '{$mT014['T014_Id']}'");
        $rowT225 = mysql_fetch_array($sqlT225);
        if(!empty($rowT225['T219_T006_Id'])){
            mysql_query("UPDATE T006 SET T006_T219_Id = '{$rowT225['T219_Id']}', T006_T225_Id = '{$rowT225['T225_Id']}' WHERE T006_Id = '{$rowT225['T219_T006_Id']}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return 'Erro ao finalizar o estoque da nota fiscal.';
            }

            mysql_query("UPDATE T006 SET T006_T219_Id = '{$rowT225['T219_Id']}', T006_T225_Id = '{$rowT225['T225_Id']}' WHERE T006_T006_Id_Dividir = '{$rowT225['T219_T006_Id']}'");
            $erro = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return 'Erro ao finalizar o estoque da nota fiscal.';
            }
        }
        //reprocessa histórico agora será executado no content em etapas - Lígia Nakazato
        //$CAD002->D001_reprocessa_historico(0, $mT014['T014_D009_Id']);

        mysql_query("UPDATE D049 SET D049_ICF=0, D049_Flag_ICF_Temporario='X' WHERE D049_Id='{$mT014['D049_Id']}' AND D049_Flag_ICF_Temporario='S'");
        $erro = mysql_error();
        if (!empty($erro)) {
            mysql_query("ROLLBACK");
            return 'Atualizando ICF Temporario';
        }

        /*
        $D083 = mysql_query("select D083_Id,D083_Flag_Fornecedor from D083 where D083_D001_Id='{$mT014['D049_D001_Id']}' and D083_Flag_Fornecedor='S' and D083_D049_Id='{$mT014['D049_Id']}' and D083_D024_Id='{$mT013['T013_D024_Id']}'");
        if (mysql_num_rows($D083) <= 0) {
            mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Fornecedor,D083_Codigo_Produto_Fornecedor) values ('{$mT014['D049_Id']}','{$mT014['D049_D001_Id']}','{$mT013['T013_D024_Id']}',curdate(),'S','{$mT014['T014_Codigo_Produto_Fornecedor']}')");
        } else {
            $mD083 = mysql_fetch_array($D083);
            mysql_query("update D083 set D083_Flag_Fornecedor='S',D083_Codigo_Produto_Fornecedor='{$mT014['T014_Codigo_Produto_Fornecedor']}' where D083_Id='{$mD083['D083_Id']}'");
        }
        */
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $CAD002->definirCodigoFornecedor($mT014['T014_D009_Id'], $mT013['T013_D024_Id'], $mT014['T014_Codigo_Produto_Fornecedor'], '', false, true);

        return true;

    }
 
    public function EST004AplicarConversao($mT014, $mT014Anterior)
    {
        if($mT014['T013_Flag_Travar_Nota'] === 'S')
            return "Não é possível aplicar conversão de unidade após a nota estar finalizada";

        $T014_Id = $mT014['T014_Id'];
        $T014_Unidade = $mT014['T014_Unidade'];
        $D001_Id = $mT014['D001_Id'];
        $D001_D037_Id = $mT014['D037_Id_Prod'];
        $T014_Quantidade = $mT014['T014A_Quantidade'];
        $T014_Valor_Custo_Unitario = $mT014['T014A_Valor_Custo_Unitario'];

        $resultSql = mysql_query("SELECT D037_Id FROM D037 WHERE D037_Unidade = '{$T014_Unidade}' OR D037_Variacao_Unidade REGEXP '{$T014_Unidade}[\s,]'");
        $D037_Id = mysql_fetch_assoc($resultSql);

        /* 14/03 Melhorar gProcessaEtapas para incluir parametro que permita não interromper o processo */
        /* Não foi encontrada a variação */
        if (empty($D037_Id))
            return true;

        $D037_Id = $D037_Id['D037_Id'];
        
        mysql_query("UPDATE T014 SET T014_D037_Id = {$D037_Id} WHERE T014_Id = {$T014_Id}"); //Atualizar a nota com unidade/variação encontrada
        
        if (empty($D001_D037_Id))
            return true;
        
        if ($D037_Id === $D001_D037_Id)
            return true;

        // buscar regras de conversões para unidade definida na nota
        $resultSql = mysql_query("SELECT D184_Tipo_Operacao, D184_Quantidade_Conversao FROM D184 WHERE D184_D001_Id='$D001_Id' and D184_D037_Id='$D037_Id'");        
        $row = mysql_fetch_assoc($resultSql);

        /* não tem conversão registrada para essa unidade no produto */;
        if (empty($row))
            return true;
        
        $tipoOperacao = $row['D184_Tipo_Operacao'];
        $quantidadeConversao =  $row['D184_Quantidade_Conversao'];

        list($novaQuantidade, $novoValorUnitario) = $this->calcularUnidadeMedida($T014_Quantidade,  $T014_Valor_Custo_Unitario, $quantidadeConversao, $tipoOperacao);

        mysql_query("UPDATE T014 SET T014_Quantidade = {$novaQuantidade}, T014_Valor_Custo_Unitario = {$novoValorUnitario} WHERE T014_Id = {$T014_Id}");        
        $this->atualizaQuantidadeConferencia($novaQuantidade, $T014_Id);

        return true;
    }

    function calcularUnidadeMedida($quantidade,  $valorUnitario, $quantidadeConversao, $tipoOperacao)
    {
        $novaQuantidade = 0;
        $novoValorUnitario = 0;

        $operacao = [
            'D' => $quantidade / $quantidadeConversao,
            'M' => $quantidade * $quantidadeConversao,            
        ];

        $novaQuantidade  = $operacao[$tipoOperacao];            
        
        $valorTotal = ($valorUnitario  * $quantidade);
        $novoValorUnitario = ($valorTotal / $novaQuantidade);

        return [$novaQuantidade, $novoValorUnitario];
    }    

    function EST004AtualizarCabecalhoNota($sqlComprimida, $T013_Id) 
    {                            
        $T014_Valor_Total_IPI = 0;
        $T014_Valor_Total_PIS = 0;
        $T014_Valor_Total_COFINS = 0;        

        $sqlT014 = mysql_query("select  sum(T014_Valor_IPI) as T014_Valor_Total_IPI,
                                        sum(T014_Valor_PIS) as T014_Valor_Total_PIS,
                                        sum(T014_Valor_COFINS) as T014_Valor_Total_COFINS,
                                        sum(T014_Valor_ICMS) as T014_Valor_Total_ICMS,
                                        sum(T014_Valor_Base_ICMS) as T014_Valor_Total_Base_ICMS,
                                        sum(T014_Valor_Base_ICMS_Substituicao) as T014_Valor_Total_Base_ICMS_Substituicao,
                                        sum(T014_Valor_ICMS_Substituicao) as T014_Valor_Total_ICMS_Substituicao,
                                        sum(T014_Valor_FCP_ST) as T014_Valor_Total_FCP_ST
                                from T014
                                where T014_T013_Id = '{$T013_Id}'");

        $resultado = mysql_fetch_array($sqlT014);

        $T014_Valor_Total_IPI = $resultado['T014_Valor_Total_IPI'];
        $T014_Valor_Total_PIS = $resultado['T014_Valor_Total_PIS'];
        $T014_Valor_Total_COFINS = $resultado['T014_Valor_Total_COFINS'];
        $T014_Valor_Total_ICMS = $resultado['T014_Valor_Total_ICMS'];
        $T014_Valor_Total_Base_ICMS = $resultado['T014_Valor_Total_Base_ICMS'];
        $T014_Valor_Total_Base_ICMS_Substituicao = $resultado['T014_Valor_Total_Base_ICMS_Substituicao'];;
        $T014_Valor_Total_ICMS_Substituicao = $resultado['T014_Valor_Total_ICMS_Substituicao'];
        $T014_Valor_Total_FCP_ST = $resultado['T014_Valor_Total_FCP_ST'];
     
        $sqlT013 = mysql_query("select 
                                    T013_Valor_Total_Produtos,
                                    T013_Valor_Seguro,
                                    T013_Valor_Total_IPI,
                                    T013_Valor_Mao_Obra,
                                    T013_Valor_Despesas_Acessorias,
                                    T013_Valor_ICMS_Substituicao,
                                    T013_Valor_Desconto,
                                    T013_Valor_Total_FCP_ST
                                    from T013 WHERE T013_Id = '$T013_Id'");        
        $resultado = mysql_fetch_array($sqlT013);
             
 
        $valor_Total_Nota = (float)($resultado['T013_Valor_Total_Produtos'] 
            + $resultado['T013_Valor_Seguro']            
            + $resultado['T013_Valor_Mao_Obra'] 
            + $resultado['T013_Valor_Despesas_Acessorias']
            + $T014_Valor_Total_ICMS_Substituicao
            - $resultado['T013_Valor_Desconto']
            + $T014_Valor_Total_FCP_ST) + $T014_Valor_Total_IPI;        
        
        $sql = "update T013 set 
                    T013_Valor_Total_IPI = {$T014_Valor_Total_IPI},
                    T013_Valor_Total_PIS = {$T014_Valor_Total_PIS},
                    T013_Valor_Total_COFINS = {$T014_Valor_Total_COFINS},
                    T013_Valor_Total = {$valor_Total_Nota},
                    T013_Valor_Total_ICMS = {$T014_Valor_Total_ICMS},
                    T013_Valor_Total_Base_ICMS  = {$T014_Valor_Total_Base_ICMS},
                    T013_Valor_Total_Base_ICMS_Substituicao = {$T014_Valor_Total_Base_ICMS_Substituicao},
                    T013_Valor_ICMS_Substituicao = {$T014_Valor_Total_ICMS_Substituicao},
                    T013_Valor_Total_FCP_ST = {$T014_Valor_Total_FCP_ST}
                where T013_Id = '$T013_Id'";                           
        mysql_query($sql);
        return true;     
    }   


    function EST004AtualizarObservacoes($T013_Id) {        

        $D188 = mysql_query("select  D188_Id
        from T014
            left join D006 on D006_Id = T014_D006_Id
            inner join D188 on D188_Id = D006_D188_Id
            left join T297 on T297_T013_Id = {$T013_Id}  and T297_D188_Id = D188_Id
        where T014_T013_Id = {$T013_Id} and T297_Id is null
        group by D188_Id");
        
        
        while($mD188 = mysql_fetch_array($D188)) {            
            mysql_query("insert into T297 (T297_T013_Id,T297_D188_Id) values('{$T013_Id}', '{$mD188['D188_Id']}')");                   
        }
        
        $T014 = mysql_query("select  T014_Id, D189_Id, T297_Id, T297_Id , T298_Id 
        from T014
            left join D006 ON D006_Id = T014_D006_Id
            left join D188 ON D188_Id = D006_D188_Id
            inner join D189 ON D189_Id = D188_D189_Id
            inner join T297 ON T297_D188_Id = D188_Id AND T297_T013_Id = '{$T013_Id}'
            left join T298 ON T298_T297_Id = T297_Id AND T298_T014_Id = T014_Id
        where T014_T013_Id =  '{$T013_Id}' ");
        

        while($mT014 = mysql_fetch_array($T014)) {
            if(empty($mT014['T298_Id'])) {
                mysql_query("insert into T298 (T298_T014_Id, T298_D189_Id, T298_T297_Id) values ({$mT014['T014_Id']},{$mT014['D189_Id']}, {$mT014['T297_Id']})");                
            } else  {
                mysql_query("update T298 set T298_D189_Id = '{$mT014['D189_Id']}', T298_T297_Id = '{$mT014['T297_Id']}' where T298_Id = '{$mT014['T298_Id']}'");                
            }
        }
                    
        return true;
    }

    function EST004IncluirProduto($T014_Id) {        
        global $g;        

        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        
        $T014 = mysql_query("select T014_Descricao_Produto,T014_Classificacao_Fiscal,T014_Unidade,T014_Flag_Origem_Produto, D037_Id, D005_Id
                                from T014
                                    left join D037 ON D037_Unidade = T014_Unidade
                                    left join D005 ON  D005_Classificacao_Fiscal = T014_Classificacao_Fiscal 
                                where T014_Id = '{$T014_Id}'");       

        $mT014 = mysql_fetch_array($T014);

        $D037_Id = $mT014['D037_Id'];
        $D005_Id = $mT014['D005_Id'];
        
        if(empty($D037_Id)) {
            mysql_query("insert into D037 (D037_Unidade,
                                        D037_Descricao_Unidade) 
                                    values(
                                        '{$mT014['T014_Unidade']}', 
                                        '{$mT014['T014_Unidade']}')");          

            $D037_Id = $g['mysqlLastId'];
        }        
        
        if(empty($D005_Id)) {
            mysql_query("insert into D005 (
                                        D005_Classificacao_Fiscal,
                                        D005_Flag_Aplicacao,
                                        D005_Observacao,
                                        D005_Flag_Ativo,
                                        D005_D037_Id_Tributavel) 
                                values (
                                    '{$mT014['T014_Classificacao_Fiscal']}','0', '', 'S', '0')");
            $D005_Id = $g['mysqlLastId'];
        }       
        
        $produtoValorPadrao = $this->EST004RetornarValorPadraoCadastroProduto();

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        mysql_query("insert into D001 (D001_Descricao_Produto,
                                        D001_D005_Id,
                                        D001_D037_Id,
                                        D001_C008_Id,
                                        D001_Flag_Pre_Cadastro)
                                    values
                                        (
                                        '{$mT014['T014_Descricao_Produto']}',
                                        '{$D005_Id}',
                                        '{$D037_Id}',
                                        '{$produtoValorPadrao['Departamento']}',
                                        '{$produtoValorPadrao['PreCadastro']}')");

        $cError = mysql_error();
        if (!empty($cError)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cError;
        }    

        $D001_Id = $g['mysqlLastId'];
        mysql_query("insert into D001A(D001A_D001_Id) values($D001_Id)");
        
        $preCadastro = $produtoValorPadrao['PreCadastro'] == 'S' ? true : false;        
        $proxCodigo = $CAD002->obterNovoCodigoProduto($D001_Id,false,$preCadastro);                    
        $proxEAN = $CAD002->obterNovoCodigoEAN();
        mysql_query("update D001 set D001_Codigo_Produto = '{$proxCodigo}', D001_Codigo_Barras = '{$proxEAN}' where D001_Id = '{$D001_Id}'");
        
        mysql_query( "insert into D049 (D049_D001_Id, 
                                    D049_D082_Id, 
                                    D049_Flag_Nacional_Importado, 
                                    D049_Flag_Ativo, 
                                    D049_Origem_Mercadoria, 
                                    D049_Flag_Substituicao_Tributaria)
                                values 
                                (   '$D001_Id',
                                    '{$produtoValorPadrao['Marca']}',
                                    'N',
                                    'S',
                                    '{$mT014['T014_Flag_Origem_Produto']}',
                                    'N')");

        $cError = mysql_error();
        if (!empty($cError)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cError;
        }    


        $D049_Id =  $g['mysqlLastId'];
       
        $C004 = mysql_query("select C004_Id from C004 order by C004_Id");
        $D009_Id = 0;
        while($mC004 = mysql_fetch_array($C004)) {
            mysql_query("insert into D009 (D009_D049_Id,
                                        D009_D001_Id,
                                        D009_C004_Id,
                                        D009_Data_Cadastro,
                                        D009_Flag_Ativo)
                                    values
                                    (   '$D049_Id',
                                        '$D001_Id',
                                        '{$mC004['C004_Id']}',
                                        '',
                                        'S')");

            if($mC004['C004_Id'] === $g['empresaAtual'])   {                                     
                $D009_Id = $g['mysqlLastId'];
            }

            $cError = mysql_error();
            if (!empty($cError)) {
                mysql_query("ROLLBACK");
                return "Erro: " . $cError;
            }     
    
        }
              
        mysql_query("COMMIT");           
        mysql_query("SET AUTOCOMMIT=1");   
        $retorno =  $this->EST004VincularProdutoNota($D009_Id, $T014_Id);    

        if($retorno !== true) 
            return "Produto cadastrado com sucesso<br>" . $retorno;
        
        return true;
    }

    function EST004VincularProdutoNota($D009_Id, $T014_Id) {
        global $g;        
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        mysql_query("update T014 set  
                T014_D009_Id='{$D009_Id}',                
                T014_C004_Id='{$g['empresaAtual']}',
                T014_Codigo_Produto_Fornecedor=T014_Codigo_Produto
                where T014_Id = '{$T014_Id}'");

        $cErro = mysql_error();
        if(!empty($cErro))
            return "Erro: Erro ao vincular item da nota fiscal.";

        $T014    = mysql_query("select T013_D024_Id, T014_Codigo_Produto from T014 left join T013 on T013_Id=T014_T013_Id where T014_Id='{$T014_Id}'");
        $mT014 = mysql_fetch_array($T014);

        $CAD002->definirCodigoFornecedor($D009_Id, $mT014['T013_D024_Id'], $mT014['T014_Codigo_Produto'], '', false, true);
        return true;
    }

    function EST004RetornarValorPadraoCadastroProduto() {
        return [
            'Departamento' =>'',
            'Marca' => '',
            'PreCadastro'=> 'S'
        ];
    }


    function EST004AtualizarAliquotas($mT014, $mT014Anterior = null) {                                                
        global $g;
       
        if($mT014['T013_Flag_Travar_Nota'] === 'S'){
            return "Não é possível atualizar as alíquotas após a nota estar finalizada";  
        }
            
        require_once('bibliotecas/classes/EST015.php');
        $EST015 = new EST015();

        $D009_Id                    = $mT014['D009_Id'];
        $T014_Id                    = $mT014['T014_Id']; 
        $T013_Id                    = $mT014['T013_Id'];                           

        $Aliquotas = $EST015->retornaAliquotas($D009_Id, false, false, $T013_Id, false, false, $T014_Id, false, false,false);      

        //Id da NCM
        $D005_Id                    = $Aliquotas['D005_Id'];        
        $aliquotaIPI                = $Aliquotas['Aliquota_IPI'];
        $aliquotaPIS                = $Aliquotas['Aliquota_PIS'];
        $aliquotaCOFINS             = $Aliquotas['Aliquota_COFINS'];
        $aliquotaICMS               = $Aliquotas['Aliquota_ICMS'];  
        $T014_Valor_Base_IPI        = 0;
        $T014_Valor_Base_COFINS     = 0;
        $T014_Valor_Base_PIS        = 0;    
        $T014_Valor_Base_ICMS       = 0; 
        $valorDespesas              = ($mT014['T014_Valor_Frete'] + $mT014['T014_Valor_Despesas_Acessorias'] + $mT014['T014_Valor_Seguro']);     

        //Calculo IPI    
        if($aliquotaIPI > 0) {
            $despesasIPI         = ($mT014['D006_Flag_Somar_Despesas_Base_IPI'] === 'S') ? $valorDespesas : 0;
            $reducao             = $Aliquotas['Percentual_Reducao_IPI'] > 0 ? ((float)$Aliquotas['Percentual_Reducao_IPI']/100) : 1;
            $T014_Valor_Base_IPI = (($mT014['T014_Valor_Total_Custo'] + $despesasIPI) - $mT014['T014_Valor_Total_Desconto']) * $reducao;
        }

        $valorIPI = ($T014_Valor_Base_IPI * ($aliquotaIPI / 100));
                
        //Calculo ICMS
        if($aliquotaICMS > 0) {
                                                                
            $Flag_ST       = $Aliquotas['Flag_ST'] ;                             
            $percentualFCP = $Aliquotas['Percentual_FCP'];

            if($mT014['D006_Flag_Ajustar_MVA'] == 'S') {
                $TRIB_ST_VA_Ajustada  = mysql_query("SELECT TRIB_ST_VA_Ajustada('{$mT014['D006_Flag_Ajustar_MVA']}', '{$Aliquotas['ST_VA']}', '{$Aliquotas['ICMS_Inter_Estadual']}','{$Aliquotas['ST_ICMS_Interno']}', '', '',  '{$Aliquotas['Percentual_Reducao_ICMS']}', '{$Aliquotas['Percentual_Reducao_ICMS_IE_ST']}', '{$Aliquotas['Percentual_Reducao_ICMS_INT_ST']}','','','','') as ST_VA_Ajustada");                    
                $mTRIB_ST_VA_Ajustada = mysql_fetch_array($TRIB_ST_VA_Ajustada);                    
                $Aliquotas['ST_VA']   = $mTRIB_ST_VA_Ajustada['ST_VA_Ajustada'];
            }
            
                            
            $T014Total  = mysql_query("SELECT SUM(T014_Valor_Total_Custo) AS Valor_Total_Preco FROM T014 WHERE T014_T013_Id = '{$mT014['T013_Id']}'");
            $mT014Total = mysql_fetch_array($T014Total);

            //Calculo das despesas do item
            $Multiplicador_Despesa = ($mT014['T013_Valor_Frete'] + $mT014['T013_Valor_Despesas_Acessorias'] + $mT014['T013_Valor_Seguro']) / $mT014Total['Valor_Total_Preco'];            
            $Valor_Total_Despesas  = $mT014['T014_Valor_Total_Custo'] * $Multiplicador_Despesa;
            
            //Calculo da base do ICMS
            $Valor_Base_ICMS_Sem_Reducao = $mT014['T014_Valor_Total_Custo'] 
                                            +  ($mT014['T013_Flag_Calcular_ICMS_Despesas_Acessorias'] == 'S' ? $Valor_Total_Despesas : 0 )
                                            + (($mT014['D006_Flag_Somar_IPI_Base_ICMS'] == 'S') ? $valorIPI : 0)
                                            - $mT014['T014_Valor_Total_Desconto'];
            
            $T014_Valor_Base_ICMS = $Valor_Base_ICMS_Sem_Reducao * ($Aliquotas['Percentual_Reducao_ICMS'] > 0 ? ((100 - $Aliquotas['Percentual_Reducao_ICMS'])/100) : 1);                                                            
                                   
            $T014_Valor_Base_ICMS = ($mT014['D006_Flag_Somar_Fator_ICMS_Dif_Base'] == 'S') 
                                            ? $T014_Valor_Base_ICMS/((100 - $Aliquotas['ST_ICMS_Interno']) / 100)
                                            : $T014_Valor_Base_ICMS;                                                                                                                                               

            //Calculo valor do ICMS                                                
            $valorICMS = ($T014_Valor_Base_ICMS * ($aliquotaICMS / 100));

            //ICMS ST
            $Valor_Base_ICMS_Substituicao = 0;
            $destino                      = $mT014['T014A_Flag_Destino_Produto'];
            $Calculo_ST_Por_Dentro        = empty($mT014['D053_Calculo_ST_Por_Dentro_Compra']) ? 'N' : $mT014['D053_Calculo_ST_Por_Dentro_Compra'] ;    
            
            //Calculo valor da base ICMS ST
            if($Flag_ST == "S") {
                if($mT014['D006_Modalidade_ICMS_Substituicao'] == 5) {
                    $Valor_Base_ICMS_Substituicao = ($mT014['D009_Preco_Pauta_ST']  * $mT014['T014_Quantidade']) + $Valor_Total_Despesas + $valorIPI;
                } else if($destino == "3" && $Calculo_ST_Por_Dentro == "S") {                                
                    $Valor_Base_ICMS_Substituicao = (($mT014['T014_Valor_Total_Custo'] + $Valor_Total_Despesas + $valorIPI) * (( $Aliquotas['ST_VA']/100)+1)
                                                    - (($valorICMS > 0) ? $valorICMS :  $mT014['T014_Valor_Total_Custo'] * ($Aliquotas['ICMS_Inter_Estadual']/100)))
                                                    /  (($Aliquotas['ST_ICMS_Interno']/100-1) * -1);
                } else {                        
                    $Valor_Base_ICMS_Substituicao = ($mT014['T014_Valor_Total_Custo'] + $Valor_Total_Despesas + $valorIPI) * (($Aliquotas['ST_VA']/100)+1);                                                
                }
            }

            //Calculo valor do ICMS ST                
            $Valor_Total_Custo_Final = $Valor_Base_ICMS_Sem_Reducao;                                
            $ValorOrigem             = 0;
            $ValorDestino            = 0;
            $ValorST                 = 0;
       
            $ValorOrigem                           = (($Valor_Base_ICMS_Substituicao * ($Aliquotas['Percentual_Reducao_ICMS_ST'] > 0 ?( (100-$Aliquotas['Percentual_Reducao_ICMS_ST'])/100) : 1))*($Aliquotas['ST_ICMS_Interno']/100));
            $ValorDestino                          = (($Valor_Total_Custo_Final *($Aliquotas['Percentual_Reducao_ICMS_ST'] > 0 ? ((100-$Aliquotas['Percentual_Reducao_ICMS_ST'])/100) : 1)) *  $Aliquotas['ICMS_Inter_Estadual']/100);
            $ValorST                               = $ValorOrigem - $ValorDestino;
            $T014_Valor_ICMS_Substituicao_Retencao = $ValorST < 0 ? 0 : $ValorST;                 
            
            // Calculo FCP ST
            $Valor_Base_FCP_ST = ($mT014['D006_Flag_Calcula_FCP_ST'] == 'S') ? $Valor_Base_ICMS_Substituicao : 0;
            $Valor_FCP_ST      = 0;
            
            if($T014_Valor_ICMS_Substituicao_Retencao > 0) {
                $Valor_FCP_ST = $Valor_Base_FCP_ST * ($percentualFCP / 100);
                $Valor_FCP_ST = $Valor_FCP_ST < 0 ? $Valor_FCP_ST * (-1) : $Valor_FCP_ST;
            }

            // $T014_Valor_ICMS_Substituicao_Retencao  -= $Valor_FCP_ST;            
        }        
          
        //Calculo COFINS
        if($Aliquotas['Flag_Isento_COFINS'] !== 'S') {     
            
            $T014_Valor_Base_COFINS = $mT014['D006_Flag_Forma_Calculo_COFINS'] === 'L' 
                                        ? $mT014['T014_Valor_Total_Custo_Final'] - $mT014['T014_Valor_Total_Custo'] - $mT014['T014_Valor_Total_Desconto']
                                        : $mT014['T014_Valor_Total_Custo'] - $mT014['T014_Valor_Total_Desconto'];
                                     
            $T014_Valor_Base_COFINS +=  $mT014['D006_Flag_Somar_Despesas_Base_COFINS'] === 'S' ? $valorDespesas : 0;

            if($mT014['D006_Flag_Deduz_ICMS_Base_COFINS'] === 'S') {                                        
                $T014_Valor_Base_COFINS -= $valorICMS;                    
            }         

        } else {
            $aliquotaCOFINS = 0;
        }

        $valorCOFINS = ($T014_Valor_Base_COFINS * ($aliquotaCOFINS / 100));     
        
        //Calculo PIS
        if($Aliquotas['Flag_Isento_PIS'] !== 'S') {

            $T014_Valor_Base_PIS =  $mT014['D006_Flag_Forma_Calculo_PIS']  === 'L' 
                                    ? $mT014['T014_Valor_Total_Custo_Final'] - $mT014['T014_Valor_Total_Custo'] - $mT014['T014_Valor_Total_Desconto']
                                    : $mT014['T014_Valor_Total_Custo'] - $mT014['T014_Valor_Total_Desconto']; 
            
            $T014_Valor_Base_PIS +=  $mT014['D006_Flag_Somar_Despesas_Base_PIS'] === 'S' ? $valorDespesas : 0;

            if($mT014['D006_Flag_Deduz_ICMS_Base_PIS'] === 'S') {                                        
                $T014_Valor_Base_PIS -= $valorICMS;                    
            }  
                                              
        } else {
            $aliquotaPIS = 0;
        }

        $valorPIS = ($T014_Valor_Base_PIS * ($aliquotaPIS / 100));
               
        //CST
        $Situacao_Tributaria_COFINS = $Aliquotas['Situacao_Tributaria_COFINS'];
        $Situacao_Tributaria_PIS    = $Aliquotas['Situacao_Tributaria_PIS'];
        $Situacao_Tributaria_IPI    = $Aliquotas['Situacao_Tributaria_IPI'];
        $Situacao_Tributaria_ICMS   = $Aliquotas['Flag_Situacao_Tributaria'];
           
        $sqlQuery = "UPDATE T014 SET T014_Aliquota_PIS                 = '{$aliquotaPIS}',
                                     T014_Aliquota_IPI                 = '{$aliquotaIPI}',
                                     T014_Aliquota_COFINS              = '{$aliquotaCOFINS}',
                                     T014_D005_Id                      = '{$D005_Id}',
                                     T014_Valor_IPI                    = '{$valorIPI}',
                                     T014_Valor_PIS                    = '{$valorPIS}',
                                     T014_Valor_COFINS                 = '{$valorCOFINS}',
                                     T014_Valor_Base_IPI               = '{$T014_Valor_Base_IPI}',
                                     T014_Valor_Base_PIS               = '{$T014_Valor_Base_PIS}',
                                     T014_Valor_Base_COFINS            = '{$T014_Valor_Base_COFINS}',
                                     T014_Situacao_Tributaria_PIS      = '{$Situacao_Tributaria_PIS}',
                                     T014_Situacao_Tributaria_COFINS   = '{$Situacao_Tributaria_COFINS}',
                                     T014_Situacao_Tributaria_IPI      = '{$Situacao_Tributaria_IPI}',
                                     T014_Flag_Situacao_Tributaria     = '{$Situacao_Tributaria_ICMS}',
                                     T014_Valor_Base_ICMS              = '{$T014_Valor_Base_ICMS}',
                                     T014_Aliquota_ICMS                = '{$aliquotaICMS}',
                                     T014_Valor_ICMS                   = '{$valorICMS}',
                                     T014_Valor_Base_ICMS_Substituicao = '{$Valor_Base_ICMS_Substituicao}',    
                                     T014_Valor_ICMS_Substituicao      = '{$T014_Valor_ICMS_Substituicao_Retencao}',
                                     T014_ICMS_Inter_Estadual          = '{$Aliquotas['ICMS_Inter_Estadual']}',
                                     T014_Aliquota_ICMS_Interno        = '{$Aliquotas['ST_ICMS_Interno']}',
                                     T014_Valor_Base_FCP_ST            = '{$Valor_Base_FCP_ST}',
                                     T014_Percentual_FCP_ST            = '{$percentualFCP}',
                                     T014_Valor_FCP_ST                 = '{$Valor_FCP_ST}',
                                     T014_MVA_Ajustada                 = '{$Aliquotas['ST_VA']}',
                                     T014_Percentual_Reducao_ICMS      = '{$Aliquotas['Percentual_Reducao_ICMS']}',
                                     T014_Percentual_Reducao_IPI       = '{$Aliquotas['Percentual_Reducao_IPI']}'                    
                               WHERE T014_Id = '{$T014_Id}'";          
        mysql_query($sqlQuery);        
        return true;
    }    
    
    function EST004ValidarVinculoProduto($sqlComprimida, $T013_Id)  {

        $sqlT014 = mysql_query("select COUNT(*) as ProdutoSemVinculo
                                from T014
                            left join D009 ON D009_Id = T014_D009_Id 
                            where D009_Id is null and T014_T013_Id = '{$T013_Id}'");

        
        $mT014 = mysql_fetch_array($sqlT014);                                
        if($mT014['ProdutoSemVinculo'] > 0)  {
            return "Não foi possível atualizar as alíquotas, existem itens sem vínculo com produto.";
        }
        
        return true;
    }

    /**
     * incluirProduto
     *
     * @param Form $form
     * @param int $T013_Id
     * @return String/bool
     */ 
    public function incluirProduto($form, $T013_Id)
    {
        global $g;        
        if (empty($T013_Id)) {
            return "Nao foi recebido o id da nota = T013_Id";
        }
        
        $T013                      =  mysql_query("select T013_D006_Id,T013_Aliquota_ICMS,T013_C004_Id from T013 where T013_Id='{$T013_Id}'");
        $mT013                     = mysql_fetch_array($T013);
        $D009_Id                   = $form->campoValorEnviado('T014_D009_Id');                        
        $D006_Id                   = $form->campoValorEnviado('T014_D006_Id');
        $T014_Quantidade           = gCorrigeNumeroInverte($form->campoValorEnviado('T014_Quantidade'));
        $T014_Valor_Custo_Unitario = gCorrigeNumeroInverte($form->campoValorEnviado('T014_Valor_Custo_Unitario'));
        $T066_Id = $form->campoValorEnviado("T014_T066_Id");

        $erros = '';
        if(empty($D009_Id)) {
             $erros .="Não foi selecionado nenhum produto <br/>";
        }
      
        if($T014_Quantidade <= 0){
            $erros .= "Nâo foi informado a Quantidade <br/>";
        }
        if($T014_Valor_Custo_Unitario <= 0) {
            $erros .= "Nâo foi informado o Preço Unitário <br/>";
        }        
        if(empty($D006_Id)) {
            $erros .= "Nâo selecionado a CFOP <br/>";
        }
        if(empty($T066_Id)) {
            $erros .= "Nâo foi selecionado a Locação <br/>";
        }

        if(!empty($erros)) {
            return $erros;
        }

        $cClausula = <<<EOT
			SELECT 
				D009_Id, 
				D001_D037_Id, 
				D001_Codigo_Produto, 
				D049_Codigo_Produto_Fornecedor, 
				D037_Unidade, 
				D037_Id, 
				D005_Classificacao_Fiscal, 
				D005_Aliquota_IPI, 
				D001_Descricao_Produto, 
				D005_Id,
                D149_Aliquota_IPI,
                D049_Origem_Mercadoria,
                T066_Id,
                D004_Id
			from D009 
			left join D049 on D049_Id=D009_D049_Id 
			left join D001 on D001_Id=D049_D001_Id 
			left join D037 on D037_Id=D001_D037_Id 
			left join D005 on D009_D005_Id(D009_Id)=D005_Id 
            left join T066 on T066_Id='{$T066_Id}'            
            left join D004 on D004_Id=T066_D004_Id
            left join D149 on D149_D005_Id=D005_Id and D149_C004_Id='{$g['empresaAtual']}'
			where
				D009_Id = '{$D009_Id}' 
EOT;
        $D009  = mysql_query($cClausula);        
        $mD009 = mysql_fetch_array($D009);
        if (mysql_num_rows($D009) == 0) {
            return "Não foi selecionado nenhum produto";
        }
                
        $mD009['D001_Descricao_Produto'] = mysql_real_escape_string($mD009['D001_Descricao_Produto']);
        
        $cClausula = <<<EOT
			insert into T014 (
				T014_T013_Id, 
				T014_D009_Id, 
				T014_D006_Id, 
				T014_D037_Id, 
				T014_C004_Id, 				
				T014_Codigo_Produto, 
				T014_Codigo_Produto_Fornecedor, 
				T014_Quantidade, 				
				T014_Classificacao_Fiscal, 
				T014_Descricao_Produto, 
                T014_Valor_Custo_Unitario,
				T014_Flag_Origem_Produto,
                T014_T066_Id,
                T014_D004_Id,
                T014_Aliquota_ICMS, 
				T014_Aliquota_IPI
			) values ( 
				'$T013_Id', 
				'$D009_Id', 
				'{$D006_Id}', 
				'{$mD009['D037_Id']}', 
				'{$mT013['T013_C004_Id']}', 				
				'{$mD009['D001_Codigo_Produto']}', 
				'{$mD009['D049_Codigo_Produto_Fornecedor']}', 
				'{$T014_Quantidade}', 				
				'{$mD009['D005_Classificacao_Fiscal']}', 
                '{$mD009['D001_Descricao_Produto']}', 
                '{$T014_Valor_Custo_Unitario}',
				'{$mD009['D049_Origem_Mercadoria']}',
                '{$mD009['T066_Id']}',
                '{$mD009['D004_Id']}',
                '{$mT013['T013_Aliquota_ICMS']}', 
				'{$mD009['D149_Aliquota_IPI']}'
			) 
EOT;
        mysql_query($cClausula);
        log("QUERY INSERT T014: $cClausula");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro <br /> $cClausula";
        } else {
            return true;
        }
    }

    function EST004AtualizarCFOP($sqlComprimida, $T013_Id, $T014_Id = false)  {   
        global $g;        


        $T013 = mysql_query("SELECT T013_Flag_Travar_Nota FROM T013 WHERE T013_Id = {$T013_Id}");
        $mT013 = mysql_fetch_array($T013);

        if($mT013['T013_Flag_Travar_Nota'] === 'S') {
            return "Não é possível Aplicar Regra de CFOP após a nota estar finalizada";  
        }

        require_once("bibliotecas/classes/EST015.php");
        $EST015 = new EST015();

        $extra = '';
        if($T014_Id !== false){
            $extra = "AND T014_Id = '{$T014_Id}'";
        }

        $T014 = mysql_query("SELECT T014_Id,
                                    T013_D024_Id,
                                    T014_D009_Id,
                                    T014_Flag_Origem_Produto,
                                    T014A_Flag_Destino_Produto
                               FROM T013 
                          LEFT JOIN T014 ON T013_Id = T014_T013_Id 
                          LEFT JOIN T014A ON T014A_T014_Id = T014_Id
                              WHERE T013_Id = '{$T013_Id}' {$extra}");
        

        while ($mT014 = mysql_fetch_array($T014)) {
            $D006_Id = $EST015->retornaCFOP($mT014['T013_D024_Id'], $mT014['T014_D009_Id'], $g['empresaAtual'], $mT014['T014_Flag_Origem_Produto'], false,false, $mT014['T014A_Flag_Destino_Produto']);
            $demonstrativoCFOP = $EST015->retornaCFOP($mT014['T013_D024_Id'], $mT014['T014_D009_Id'], $g['empresaAtual'], $mT014['T014_Flag_Origem_Produto'], true,false, $mT014['T014A_Flag_Destino_Produto']);
            $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);
            
            mysqli_query("UPDATE T014                            
                      LEFT JOIN T014A ON T014A_T014_Id = T014_Id
                            SET T014_D006_Id       = '{$D006_Id}',
                                T014A_D006_Id_Info = '{$demonstrativoCFOP}'
                          WHERE T014_Id = {$mT014['T014_Id']}");                                                  
        }
        
        return true;
    }

    public function gatilhoAposFinalizarFinanceiro($parametros) {
        global $g;

        return true;
    }

    public function gatilhoAposFinalizarInclusaoNota($parametros) {
        global $g;

        return true;
    }

    /**
     * validacaoCusto
     *
     * 
     * @param int $T013_Id
     * @return String
     */ 
    public function validacaoCusto($T013_Id) {
        global $g;

        $T014Custo = mysql_query("SELECT *, D009_IPV_ICMS(T014_C004_Id, T013_D024_Id, T014_D009_Id, T014_D006_Id, D024_D018_Id, D024_Flag_Revenda_Consumidor, 0.00, 6, 2) AS Aliquota_ICMS FROM T014 
                                LEFT JOIN D006 ON D006_Id=T014_D006_Id 
                                LEFT JOIN D009 ON D009_Id=T014_D009_Id
                                LEFT JOIN D049 ON D049_Id=D009_D049_Id
                                LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                LEFT JOIN T013 ON T013_Id=T014_T013_Id     
                                LEFT JOIN D024 ON D024_Id=T013_D024_Id
                                LEFT JOIN T014A ON T014_Id=T014A_T014_Id
                                WHERE T014_T013_Id='{$T013_Id}'");
        $validacaoCusto = "";
        while ($mT014Custo = mysql_fetch_array($T014Custo)) {
            
            if ($mT014Custo['D006_Flag_Atualiza_Custo'] == "S") {
                                
                // SQL que calcula o custo
                // T014_Valor_Custo_Final_Unitario = ((T014_Valor_Total_Custo + IF(xFlag_Substituicao_Tributaria='S',T014_Valor_ICMS_Substituicao,0) + T014_Valor_Frete + IF(xFlag_Credita_IPI='N',T014_Valor_IPI,0) + T014_Valor_Seguro + T014_Valor_Despesas_Acessorias - IF(xFlag_Deb_Crd_ICMS='S', T014_Valor_ICMS, 0)) - T014_Valor_Total_Desconto)/T014_Quantidade,
                // T014_Valor_Total_Custo_Final    = T014_Valor_Custo_Final_Unitario * T014_Quantidade
                $mT014Custo['T014_Valor_ICMS_Substituicao'] = $mT014Custo['D006_Flag_Substituicao_Tributaria'] == 'S' ? $mT014Custo['T014_Valor_ICMS_Substituicao'] : 0.00;
                $mT014Custo['T014_Valor_IPI']               = $mT014Custo['D006_Flag_Credita_IPI'] == 'N' ? $mT014Custo['T014_Valor_IPI'] : 0.00;
                $mT014Custo['T014_Valor_ICMS']              = $mT014Custo['D006_Flag_Deb_Crd_ICMS'] == 'S' ? $mT014Custo['T014_Valor_ICMS'] : 0.00;
                $mT014Custo['T014A_Valor_Acrescimo_Custo']  = ($mT014Custo['T014A_Valor_Acrescimo_Custo'] == '') ? 0 : $mT014Custo['T014A_Valor_Acrescimo_Custo']; 
                
                $cClausula = "SELECT SUM(T014_Quantidade) AS T014_Quantidade
                                FROM T014 
                               WHERE T014_Id = '{$mT014Custo['T014_Id']}' 
                                  OR T014_Id_Duplicado = '{$mT014Custo['T014_Id']}' ";

                if($mT014Custo['T014_Id_Duplicado'] > 0){
                    $cClausula .= " OR T014_Id = '{$mT014Custo['T014_Id_Duplicado']}' 
                                    OR T014_Id_Duplicado = '{$mT014Custo['T014_Id_Duplicado']}'";
                }

                $sqlQtde = mysql_query($cClausula);
                //$sqlQtde = mysql_query("SELECT SUM(T014_Quantidade) as T014_Quantidade FROM T014 WHERE T014_Id = '{$mT014Custo['T014_Id']}' OR T014_Id_Duplicado = '{$mT014Custo['T014_Id']}' OR T014_Id = '{$mT014Custo['T014_Id_Duplicado']}' OR T014_Id_Duplicado = '{$mT014Custo['T014_Id_Duplicado']}'");
                $resQtde = mysql_fetch_array($sqlQtde);
                
                $Custo_Nota      = $mT014Custo['T014_Valor_Custo_Final_Unitario'];
                
                // Somar o difal no custo
                $xValorDiferencaAliquota = 0;
                if($mT014Custo['D006_Flag_Somar_DIFAL_Custo'] == 'S'){                    
                    $xDiferencaAliquota = $mT014Custo['T014_ICMS_Inter_Estadual'] - $mT014Custo['T014_Aliquota_ICMS'];
                    $xDiferencaAliquota = round(($mT014Custo['T014_Aliquota_ICMS_Interno'] - ($mT014Custo['T014_Aliquota_ICMS_Interno'] * $mT014Custo['T014_Percentual_Reducao_ICMS']/100)) - $mT014Custo['T014_Aliquota_ICMS'],2);
                    if ($xDiferencaAliquota>0){
                         $xValorDiferencaAliquota = round(($xDiferencaAliquota / 100) * $mT014Custo['T014_Valor_Base_ICMS'],5);

                    } else {
                        $xValorDiferencaAliquota = 0;
                    }
                }

                // A função ROUND arredonda o número para 6, como acontece ao gravar o campo T014_Valor_Custo_Final_Unitario.
                $custo_Calculado = round((((($resQtde['T014_Quantidade'] * $mT014Custo['T014_Valor_Custo_Unitario']) +
                                            $mT014Custo['T014_Valor_ICMS_Substituicao'] + 
                                            $mT014Custo['T014_Valor_Frete'] + 
                                            $mT014Custo['T014_Valor_IPI'] + 
                                            $mT014Custo['T014_Valor_Seguro'] +
                                            $mT014Custo['T014_Valor_Despesas_Acessorias'] + 
                                            $xValorDiferencaAliquota) - 
                                            $mT014Custo['T014_Valor_Total_Desconto']) / $resQtde['T014_Quantidade']) + 
                                            $mT014Custo['T014A_Valor_Acrescimo_Custo'],6);

                $Custo_Nota      = number_format($Custo_Nota, 2);
                $custo_Calculado = number_format($custo_Calculado, 2);

                if (abs($Custo_Nota - $custo_Calculado) > 0.001) {
                    $validacaoCusto .= "O produto código {$mT014Custo['D001_Codigo_Produto']} está com o valor do custo errado. Valor calculado: {$custo_Calculado} Valor apurado: {$Custo_Nota}<br />";
                }
            }
        }

        return $validacaoCusto;
    }

    public function AlocarSaldoLocacao ($D009, $T014, $T066) {
        return true;
    }
}

$EST004 = new EST004();
