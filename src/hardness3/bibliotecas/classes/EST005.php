<?php
namespace hardness;

definirClasse('EST005');
class EST005_
{
    /**
     * campoExcluir
     *
     * @param int $T084_Id
     * @return true/string
     */
    public function campoExcluir($T084_Id)
    {
        if ($T084_Id <= 0) {
            return "Não encontrei o T084_Id";
        }
        
        $T015 = mysql_query("SELECT T019_Id
                                FROM T015
                            LEFT JOIN T019 ON T019_T015_Id=T015_Id
                            WHERE T015_T084_Id='{$T084_Id}'");

        $mT015 = mysql_fetch_array($T015);
        // Verifica se tem lançamentos bancarios
        if ($mT015['T019_Id'] > 0) {
            return "Titulo não pode ser excluido pois possui lançamentos bancários. Favor verificar.";
        }

        mysql_query("UPDATE T015 
                        SET T015_Flag_Cancelada='S',
                            T015_Data_Pagamento=current_date(),
                            T015_Observacao_2='*** CTE Excluido ***',
                            T015_Valor_Duplicata=0,
                            T015_Valor_Total=0
                        WHERE T015_T084_Id='{$T084_Id}'");
        
        $retorno  = mysql_query("delete FROM T085 WHERE T085_T084_Id = '$T084_Id'");
        
        $retorno2 = mysql_query("delete FROM T084 WHERE T084_Id = '$T084_Id'");
        
        if (($retorno && $retorno2) == 1) {
            return true;
        } else {
            return mysql_error();
        }
    }
    
    /**
     * campoCancelar
     *
     * @param int $T084_Id
     * @return true/string
     */
    public function campoCancelar($T084_Id)
    {
        global $g;
        $cSql_T084 = mysql_query("SELECT * FROM T084 WHERE T084_Id = '$T084_Id'");
        $mT084     = mysql_fetch_array($cSql_T084);
        
        if ($mT084['T084_Flag_Cancelado'] == 'S') {
            return "Este conhecimento já foi cancelado!";
        }
        
        // Verifica se ja existem contas a receber lancadas para esse conhecimento
        $cSql_T015 = mysql_query("SELECT * FROM T015 WHERE  T015_T084_Id='$T084_Id' and T015_Flag_Cancelada!='S'");
        if (mysql_num_rows($cSql_T015) > 0) {
            return "Existem contas a pagar referente a este conhecimento. Cancele-os para depois cancelar este conhecimento!";
        }
        
        // Verifica se n�o existem notas fiscais relacionadas aos conhecimento
        $cSql_T013 = mysql_query("SELECT * FROM T084 LEFT JOIN T085 ON T085_T084_Id = T084_Id LEFT JOIN T013 ON T013_Id = T085_T013_Id WHERE T084_Id = '$T084_Id' AND T013_Flag_Cancelada != 'S'");
        if (mysql_num_rows($cSql_T013) > 0) {
            return "Existem notas fiscais referente a este conhecimento. Cancele as notas fiscais para cancelar este conhecimento!";
        }
        
        if ($mT084['T084_Flag_Fechado'] == 'N') {
            return "Este conhecimento não foi finalizado ainda, utilize a opção excluir";
        }
        
        $retorno  = mysql_query("UPDATE T084 SET T084_Flag_Cancelado = 'S' WHERE T084_Id = '$T084_Id' AND T084_C004_Id='{$g['empresaAtual']}'");
        $retorno1 = mysql_query("UPDATE T015 SET T015_Flag_Cancelada='S', T015_Data_Pagamento=current_date(), T015_Valor_Duplicata=0, T015_Valor_Total=0,T015_Valor_Saldo=0  WHERE T015_T084_Id='$T084_Id'");
        if (($retorno && $retorno1) == 1) {
            return true;
        } else {
            return mysql_error();
        }
    }
    
    /**
     * gerarContasAPagar
     *
     * @param int $T084_Id
     * @return true/string
     */
    public function gerarContasAPagar($T084_Id)
    {
        global $g;
        $sqlT084 = mysql_query("SELECT * 
                                  FROM T084 
                             LEFT JOIN D006 ON D006_Id = T084_D006_Id 
                             LEFT JOIN D014 ON D014_Id = D006_D014_Id 
                             LEFT JOIN D022 ON D022_Id = T084_D022_Id 
                             LEFT JOIN T084A ON T084_Id = T084A_T084_Id
                                 WHERE T084_Id = '{$T084_Id}'");
        $T084    = mysql_fetch_array($sqlT084);

        if ((float) $T084['T084_Valor_Conhecimento'] <= 0) {
            return "Não é possível gerar contas a pagar para conhecimento com valor zerado ou negativo.";
        }

        //-- NAO PERMITE INCLUIR O MESMO TITULO
        $Numero_Conhecimento = $T084['T084_Numero_Conhecimento'];
        $cClausula = "SELECT T015_Id 
                        FROM T015 
                       WHERE T015_T084_Id ='{$T084_Id}' 
                         AND T015_Flag_Cancelada != 'S'";
        
        $cSql_T015 = mysql_query($cClausula);
        $cErro     = mysql_error();

        if (!empty($cErro)) {
            return "Erro: " . $cErro . "        " . $cClausula;
        }

        if (mysql_num_rows($cSql_T015) > 0) {
            return "Já foi gerado o título a pagar Nº $Numero_Conhecimento para este conhecimento!";
        }

        
        if((empty($T084['T084_D073_Id'])) || $T084['T084_D073_Id'] == NULL) {
            return "Não foi definido nenhum Centro de custo!";
        }

        //if($T084['D006_Flag_Gera_Contas'] == 'P' && (empty($T084['D006_D073_Id']) || $T084['D006_D073_Id'] <= 0)) {
        //    return "A CFOP gera contas a pagar e está sem um centro de custo definido! Verifique o cadastro da CFOP.";
        //}

        if(($T084['D006_Flag_Gera_Contas'] == 'P' || $T084['D006_Flag_Gera_Contas'] == 'R') && (empty($T084['D006_D014_Id']) || $T084['D006_D014_Id'] <= 0)){
            $tipoConta = ($T084['D006_Flag_Gera_Contas'] == 'P') ? 'pagar' : 'receber';
            return "A CFOP gera contas a {$tipoConta} e está sem uma conta definida! Verifique o cadastro da CFOP.";
        }

        if(!empty($T084['T084A_D014_Id']) && $T084['T084A_D014_Id'] > 0){
            $D032 = mysql_query("SELECT D014_D032_Id FROM D014 WHERE D014_Id = '{$T084['T084A_D014_Id']}'");
            $mD032 = mysql_fetch_array($D032);
            $T084['D006_D014_Id'] = $T084['T084A_D014_Id'];
            $T084['D014_D032_Id'] = $mD032['D014_D032_Id'];
        }

        $sqlD024 = mysql_query("SELECT * FROM D024 WHERE D024_Cnpj = '{$T084['D022_Cnpj']}' AND D024_Flag_Ativo != 'N'");
        if (mysql_num_rows($sqlD024) <= 0) {
            return "Não foi possível incluir a conta a pagar, verifique se a transportadora está cadastrada na opção Cliente/Fornecedor";
        } else {
            $D024 = mysql_fetch_array($sqlD024);
        }
        
        if (!isset($T084['T084_Data_Vencimento']) || $T084['T084_Data_Vencimento'] == '0000-00-00') {
            return "Não foi definida a data de vencimento do conhecimento";
        }
        
        $sql     = "INSERT INTO T015 (
                                T015_D024_Id,
                                T015_Data_Emissao,
                                T015_Data_Vencimento,
                                T015_Numero_Documento,
                                T015_Numero_Duplicata,
                                T015_Valor_Duplicata,
                                T015_Valor_Juros,
                                T015_Valor_Desconto,
                                T015_Valor_Pago,
                                T015_Valor_Total,
                                T015_Observacao,
                                T015_Valor_Original,
                                T015_Data_Vencimento_Original,
                                T015_C004_Id,
                                T015_Data_Pagamento,
                                T015_T084_Id,
                                T015_D014_Id,
                                T015_D032_Id,
                                T015_D073_Id
                            ) VALUES (
                                '{$D024['D024_Id']}',
                                CURDATE(),
                                '{$T084['T084_Data_Vencimento']}',
                                '{$T084['T084_Numero_Conhecimento']}',
                                '{$T084['T084_Numero_Conhecimento']}',
                                '{$T084['T084_Valor_Conhecimento']}',
                                '0',
                                '0',
                                '0',
                                '{$T084['T084_Valor_Conhecimento']}',
                                'Conhecimento de Transporte {$T084['T084_Numero_Conhecimento']}',
                                '{$T084['T084_Valor_Conhecimento']}',
                                '{$T084['T084_Data_Vencimento']}',
                                '{$g['empresaAtual']}',
                                '0000-00-00',
                                '{$T084['T084_Id']}',
                                '{$T084['D006_D014_Id']}',
                                '{$T084['D014_D032_Id']}',
                                '{$T084['T084_D073_Id']}'
                            )";
        $retorno = mysql_query($sql);
        if (!$retorno) {
            return "Houve um erro ao inclui a conta a pagar, por favor contate o suporte técnico";
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
    public function prepararImportarXML($arquivo, $incluirItens)
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
                        
                        $tmp = $this->ImportarXML($zipArquivo, $incluirItens);
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
                    return "Conhecimentos importados com sucesso: {$count}\\nConhecimentos com erro: {$erro}\\n\\n" . $retorno;
                }
                
            }
            zip_close($zip);
            
        } else {
            return $this->ImportarXML($arquivo, $incluirItens);
        }
    }
    
    /**
     * ImportarXML
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function ImportarXML($arquivo, $incluirItens, $vinculoOcAuto = false, $D006_Id = false)
    {
        global $g, $nT013_Id;
        
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
        $infCTe      = $dom->getElementsByTagName("infCTe")->item(0);
        $ide         = $dom->getElementsByTagName("ide")->item(0);
        $emit        = $dom->getElementsByTagName("emit")->item(0);
        $rem         = $dom->getElementsByTagName("rem")->item(0);
        $exped       = $dom->getElementsByTagName("exped")->item(0);
        $receb       = $dom->getElementsByTagName("receb")->item(0);
        $dest        = $dom->getElementsByTagName("dest")->item(0);
        $toma        = $dom->getElementsByTagName("toma")->item(0);
        $vPrest      = $dom->getElementsByTagName("vPrest")->item(0);
        $imp         = $dom->getElementsByTagName("imp")->item(0);
        $infCTeNorm  = $dom->getElementsByTagName("infCTeNorm")->item(0);
        $ICMS        = $dom->getElementsByTagName("ICMS")->item(0);
        $ICMS00      = $dom->getElementsByTagName("ICMS00")->item(0);
        $ICMSOutraUF = $dom->getElementsByTagName("ICMSOutraUF")->item(0);
        $enderExped  = $dom->getElementsByTagName("enderExped")->item(0);
        $enderReceb  = $dom->getElementsByTagName("enderReceb")->item(0);
        $dup         = $dom->getElementsByTagName("dup")->item(0);
        $toma3       = $dom->getElementsByTagName('toma3')->item(0);
        $toma4       = $dom->getElementsByTagName("toma4")->item(0);
        $enderEmit   = $dom->getElementsByTagName("enderEmit")->item(0);
        $enderReme   = $dom->getElementsByTagName("enderReme")->item(0);
        $enderDest   = $dom->getElementsByTagName("enderDest")->item(0);
        $enderToma   = $dom->getElementsByTagName("enderToma")->item(0);
        $infCteComp  = $dom->getElementsByTagName("infCteComp")->item(0);
        $infCteSub   = $dom->getElementsByTagName("infCteSub")->item(0);
        $infCarga    = $dom->getElementsByTagName("infCarga")->item(0);

        // Pega chave de acesso do CTe original
        if (!empty($infCteComp)) {
            $chaveAcessoCteOriginal = $infCteComp->getElementsByTagName("chCTe")->item(0)->nodeValue;
        } elseif (!empty($infCteSub)) {
            $chaveAcessoCteOriginal = $infCteSub->getElementsByTagName("chCte")->item(0)->nodeValue;
        }
        
        $protCTe    = $dom->getElementsByTagName("protCTe")->item(0);       
        if (empty($protCTe)) {
            return "Este arquivo XML não está completo, favor verificar.";
        }
        //-- PEGA O CNPJ DO DESTINATARIO
        // = $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue;
        /*
        if (empty($nC004_Cnpj)) {
            $nCpf = $dest->getElementsByTagName("CPF")->item(0)->nodeValue;
            if (!empty($nCpf)) {
                return "Esse conhecimento foi emitida para Pessoa Física. CPF: " . $nCpf;
            }
        } 
        */       
     
        //-- PEGA OS DADOS DA SEFAZ
        $Protocolo = $protCTe->getElementsByTagName("nProt")->item(0)->nodeValue;
      

        if (empty($Protocolo)) {
            return "Este arquivo XML não possui protocolo de autorização, favor verificar.";
        }
       
        //-- PEGA OS DADOS DA SEFAZ
        $Ambiente          = $protCTe->getElementsByTagName("tpAmb")->item(0)->nodeValue;
        $Chave_Acesso      = $protCTe->getElementsByTagName("chCTe")->item(0)->nodeValue;
        $Data_Recibo       = $protCTe->getElementsByTagName("dhRecbto")->item(0)->nodeValue;
        $Numero_Retorno    = $protCTe->getElementsByTagName("cStat")->item(0)->nodeValue;
        $Descricao_Retorno = $protCTe->getElementsByTagName("xMotivo")->item(0)->nodeValue;
        
        
        //-- FAZ UMA COPIA DO ARQUIVO XML   
        $cCaminho_Destino = $g['pathDadosAntigo'] . "/cte/xml/recebido/" . $Chave_Acesso . "-" . $Protocolo . ".xml";
        if (!is_dir("{$g['pathDadosAntigo']}/cte/xml/recebido/"))
            mkdir("{$g['pathDadosAntigo']}/cte/xml/recebido/", 0777, true);
        
        if (isset($mArquivo["interno"]) || !move_uploaded_file($mArquivo["tmp_name"], $cCaminho_Destino)) {
            if (!copy($mArquivo["tmp_name"], $cCaminho_Destino)) {
                return "Nao foi possivel completar o upload do arquivo. Arquivo: ".$mArquivo["tmp_name"]." Caminho: ".$cCaminho_Destino;
            }
        }
        //-- PEGA OS DADOS DA TRANSPORTADORA (EMITENTE)
        $CNPJemi    = $emit->getElementsByTagName("CNPJ")->item(0)->nodeValue;
        $IE         = $emit->getElementsByTagName("IE")->item(0)->nodeValue;
        $xNome      = $emit->getElementsByTagName("xNome")->item(0)->nodeValue;
        $xFant      = $emit->getElementsByTagName("xFant")->item(0)->nodeValue;
        $lgr        = $enderEmit->getElementsByTagName("xLgr")->item(0)->nodeValue;
        $nro        = $enderEmit->getElementsByTagName("nro")->item(0)->nodeValue;
        $xBairro    = $enderEmit->getElementsByTagName("xBairro")->item(0)->nodeValue;
        $cMun       = $enderEmit->getElementsByTagName("cMun")->item(0)->nodeValue;
        $xMun       = $enderEmit->getElementsByTagName("xMun")->item(0)->nodeValue;
        $CEP        = $enderEmit->getElementsByTagName("CEP")->item(0)->nodeValue;
        $UF         = $enderEmit->getElementsByTagName("UF")->item(0)->nodeValue;
        $fone       = $enderEmit->getElementsByTagName("fone")->item(0)->nodeValue;

        //-- SEPARA O DDD DO NUMERO DO TELEFONE
        $telefone = substr($fone,2,16);
        $DDDfone  = substr($fone,0,2);     

        //-- PEGA OS DADOS DO REMETENTE(fornecedor) E O DESTINATÁRIO(cliente) E O TOMADOR(pagador)
        if(!empty($rem)){
            $CNPJreme   = $rem->getElementsByTagName("CNPJ")->item(0)->nodeValue;
            $CPFreme    = $rem->getElementsByTagName("CPF")->item(0)->nodeValue;
        }

        if(!empty($dest)){
            $CNPJdest   = $dest->getElementsByTagName("CNPJ")->item(0)->nodeValue;
            $CPFdest    = $dest->getElementsByTagName("CPF")->item(0)->nodeValue;
        } elseif(!empty($toma)){
            $CNPJdest   = $toma->getElementsByTagName("CNPJ")->item(0)->nodeValue;
            $CPFdest    = $toma->getElementsByTagName("CPF")->item(0)->nodeValue;
        }

        $tomador = '';

        if(!empty($toma4)){
            $CNPJtoma4  = $toma4->getElementsByTagName("CNPJ")->item(0)->nodeValue;
        }
        if(!empty($toma3)) {
            $tomador = $toma3->getElementsByTagName("toma")->item(0)->nodeValue;            
        }
        if(!empty($exped)) {
            $CNPJexpedidor = $exped->getElementsByTagName("CNPJ")->item(0)->nodeValue;            
        }

        //TRATAMENTO DOS CNPJ/CPF RETIRADOS DAS TAGS
        $CNPJemi       = (!empty($CNPJemi)) ? substr($CNPJemi, 0, 2) . "." . substr($CNPJemi, 2, 3) . "." . substr($CNPJemi, 5, 3) . "/" . substr($CNPJemi, 8, 4) . "-" . substr($CNPJemi, 12, 2) : '';
        $CNPJreme      = (!empty($CNPJreme)) ? substr($CNPJreme, 0, 2) . "." . substr($CNPJreme, 2, 3) . "." . substr($CNPJreme, 5, 3) . "/" . substr($CNPJreme, 8, 4) . "-" . substr($CNPJreme, 12, 2) : '';
        $CNPJdest      = (!empty($CNPJdest)) ? substr($CNPJdest , 0, 2) . "." . substr($CNPJdest , 2, 3) . "." . substr($CNPJdest, 5, 3) . "/" . substr($CNPJdest, 8, 4) . "-" . substr($CNPJdest, 12, 2) : '';
        $CPFdest       = (!empty($CPFdest)) ? substr($CPFdest , 0, 3) . "." . substr($CPFdest , 3, 3) . "." . substr($CPFdest, 6, 3) . "-" . substr($CPFdest, 9, 2) : '';
        $CNPJtoma4     = (!empty($CNPJtoma4)) ? substr($CNPJtoma4 , 0, 2) . "." . substr($CNPJtoma4 , 2, 3) . "." . substr($CNPJtoma4, 5, 3) . "/" . substr($CNPJtoma4, 8, 4) . "-" . substr($CNPJtoma4, 12, 2) : '';
        $CPFreme       = (!empty($CPFreme)) ? substr($CPFreme , 0, 3) . "." . substr($CPFreme , 3, 3) . "." . substr($CPFreme, 6, 3) . "-" . substr($CPFreme, 9, 2) : '';    
        $CNPJexpedidor = (!empty($CNPJexpedidor)) ? substr($CNPJexpedidor, 0, 2) . "." . substr($CNPJexpedidor, 2, 3) . "." . substr($CNPJexpedidor, 5, 3) . "/" . substr($CNPJexpedidor, 8, 4) . "-" . substr($CNPJexpedidor, 12, 2) : '';

        $xmlValor = function($node, $tag) {
            if (empty($node)) {
                return '';
            }

            $item = $node->getElementsByTagName($tag)->item(0);
            return (!empty($item)) ? $item->nodeValue : '';
        };

        $somenteDigitos = function($valor) {
            return preg_replace('/\D+/', '', (string) $valor);
        };

        $participanteXml = function($node, $enderecoNode = null) use ($xmlValor, $somenteDigitos) {
            return array(
                'cnpj' => $somenteDigitos($xmlValor($node, 'CNPJ')),
                'cpf' => $somenteDigitos($xmlValor($node, 'CPF')),
                'ie' => $xmlValor($node, 'IE'),
                'nome' => $xmlValor($node, 'xNome'),
                'fantasia' => $xmlValor($node, 'xFant'),
                'telefone' => $somenteDigitos($xmlValor($enderecoNode, 'fone')),
                'logradouro' => $xmlValor($enderecoNode, 'xLgr'),
                'numero' => $xmlValor($enderecoNode, 'nro'),
                'bairro' => $xmlValor($enderecoNode, 'xBairro'),
                'codigo_cidade' => $xmlValor($enderecoNode, 'cMun'),
                'cidade' => $xmlValor($enderecoNode, 'xMun'),
                'cep' => $somenteDigitos($xmlValor($enderecoNode, 'CEP')),
                'uf' => $xmlValor($enderecoNode, 'UF'),
                'codigo_pais' => $xmlValor($node, 'cPais'),
                'nome_pais' => $xmlValor($node, 'xPais'),
                'email' => $xmlValor($node, 'email'),
            );
        };

        $emitenteXML = $participanteXml($emit, $enderEmit);
        $remetenteXML = $participanteXml($rem, $enderReme);
        $expedidorXML = $participanteXml($exped, $enderExped);
        $recebedorXML = $participanteXml($receb, $enderReceb);
        $destinatarioXML = $participanteXml($dest, $enderDest);
        $tomadorXML = $participanteXml($toma4, $enderToma);

        if (empty($tomadorXML['nome']) && !empty($toma3)) {
            if ($tomador == '0') {
                $tomadorXML = $remetenteXML;
            } elseif ($tomador == '1') {
                $tomadorXML = $expedidorXML;
            } elseif ($tomador == '2') {
                $tomadorXML = $recebedorXML;
            } elseif ($tomador == '3') {
                $tomadorXML = $destinatarioXML;
            }
        }

        if (empty($tomadorXML['fantasia'])) {
            $tomadorXML['fantasia'] = $tomadorXML['nome'];
        }

        //-- BUSCA O CNPJ DA EMPRESA USUARIA NO BANCO
        $cClausula = "SELECT C004_Id,C004_Cnpj from C004 where C004_Cnpj='$CNPJreme'";
        $cSql_C004 = mysql_query($cClausula);
        $mC004     = mysql_fetch_array($cSql_C004);

        //-- BUSCA O CNPJ DA EMPRESA USUARIA NO BANCO
        $cClausula = "SELECT C004_Id,C004_Cnpj from C004 where C004_Cnpj='$CNPJdest'";
        $cSql_C004 = mysql_query($cClausula);
        $mC004R    = mysql_fetch_array($cSql_C004);

        //-- BUSCA O CNPJ DA EMPRESA USUARIA NO BANCO
        $cClausula = "SELECT C004_Id,C004_Cnpj from C004 where C004_Cnpj='$CNPJtoma4'";
        $cSql_C004 = mysql_query($cClausula);
        $mC004T    = mysql_fetch_array($cSql_C004);

        //-- BUSCA O CNPJ DA EMPRESA USUARIA NO BANCO
        $cClausula = "SELECT C004_Id,C004_Cnpj from C004 where C004_Cnpj='$CNPJexpedidor'";
        $cSql_C004 = mysql_query($cClausula);
        $mC004E    = mysql_fetch_array($cSql_C004);

        $Tipo_Conhecimento   = 0;
        $empresaConhecimento = '';

        if($mC004T['C004_Id'] > 0){
            $mC004 = $mC004T;      
            $Tipo_Conhecimento   = 3;
            $Tipo_Nota           = 'TOMADOR';
            $empresaConhecimento = $mC004['C004_Id'];
                       
        }else{

            if($mC004['C004_Id'] > 0 && $mC004R['C004_Id'] > 0){
                //aqui vai validar em qual empresa entrar se o rementete e destinario existirem no C004 (conhecimento entre empresas)
                if($mC004['C004_Id'] > 0 && $tomador == '0'){
                    $Tipo_Conhecimento   = 2;
                    $Tipo_Nota           = 'EMITIDA';
                    $empresaConhecimento = $mC004['C004_Id'];

                }elseif($mC004R['C004_Id'] > 0 && $tomador == '3'){
                    $mC004 = $mC004R;
                    if ($mC004['C004_Id'] > 0) {               
                        $Tipo_Conhecimento   = 1;
                        $Tipo_Nota           = 'RECEBIDA';
                        $empresaConhecimento = $mC004['C004_Id'];
                    }
                }
            }

            if($empresaConhecimento == ''){
                //VERIFICA SE A EMPRESA É A REMETENTE
                if(($mC004['C004_Id'] > 0) && ($mC004R['C004_Id'] <= 0)){
                    $Tipo_Conhecimento   = 2;
                    $Tipo_Nota           = 'EMITIDA';
                    $empresaConhecimento = $mC004['C004_Id'];

                }else{
                    $mC004 = $mC004R;
                    //VERIFICA SE A EMPRESA É A DESTINATARIA
                    if ($mC004['C004_Id'] > 0) {   
                        $Tipo_Conhecimento   = 1;
                        $Tipo_Nota           = 'RECEBIDA';
                        $empresaConhecimento = $mC004['C004_Id'];

                    } elseif($mC004E['C004_Id'] > 0 && $Tipo_Conhecimento == 0){
                        //VERIFICA SE A EMPRESA É O EXPEDIDOR APENAS SE NÃO FOR REMETENTE/DESTINATARIA/TOMADORA
                        $mC004 = $mC004E;      
                        $Tipo_Conhecimento   = 4;
                        $Tipo_Nota           = 'TOMADOR';
                        $empresaConhecimento = $mC004['C004_Id'];
                    }
                }
            }
        }   
        
        if(empty($empresaConhecimento)){
            return "O CNPJ do remetente: " . $CNPJreme . " e destinatário: " .$CNPJdest . " não conferem com o da empresa: " . $mC004['C004_Cnpj'];
            exit;
        }

        $Indicador_Tipo_Frete = '';
        $empresaTomadora      = 'N';
        if(!empty($toma3) || !empty($toma4)) {
            if($tomador == '0' || $tomador == '3') {
                $Indicador_Tipo_Frete = '1';
            }

            // Indicador do "papel" do tomador do serviço no CT-e. tag toma3
            //0-Remetente;
            //1-Expedidor;
            //2-Recebedor;
            //3-Destinatário.
 
            // Vai verificar se a empresa (C004) é tomadora do serviço
            if($tomador == '0' && $Tipo_Conhecimento == '2'){
                $empresaTomadora = 'S';
            } elseif($tomador == '3' && $Tipo_Conhecimento == '1'){
                $empresaTomadora = 'S';
            } elseif($tomador == '1' && $Tipo_Conhecimento == '4'){
                $empresaTomadora = 'S';            
            } elseif($Tipo_Conhecimento == '3'){
                $empresaTomadora = 'S';
            }
        }

        //SE A EMPRESA FOR O DESTINATÁRIO
        if($Tipo_Conhecimento==1){
            if(!empty($CNPJreme)){
                //-- VERIFICA SE O FORNECEDOR JA EXISTE ATRAVES DO CNPJ            
                $cClausula   = "SELECT D024_Id,
                                       D024_Nome_Empresa 
                                  FROM D024 
                                 WHERE D024_Cnpj='$CNPJreme' 
                                   AND D024_Flag_Ativo='S'";

                $documento = "CNPJ ".$CNPJreme;

            }else if (!empty($CPFreme)){
                //-- VERIFICA SE O FORNECEDOR JA EXISTE ATRAVES DO CPF            
                $cClausula   = "SELECT D024_Id,
                                       D024_Nome_Empresa 
                                  FROM D024 
                                 WHERE D024_Cpf='$CPFreme' 
                                   AND D024_Flag_Ativo='S'";

                $documento = "CPF ".$CPFreme;
            }

            $cSql_D024 = mysql_query($cClausula);
            $mD024     = mysql_fetch_array($cSql_D024);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                return "Erro: " . $cErro . "    " . $cClausula;
            }
            if (mysql_num_rows($cSql_D024) <= 0 && !empty($rem)) {
                return "O remetente com ".$documento." não está cadastrado ou está inativo. Favor Verificar.";
            }
        }
        
        //SE A EMPRESA FOR O REMETENTE
        if($Tipo_Conhecimento==2){
            //-- VERIFICA SE O CLIENTE JA EXISTE ATRAVES DO CNPJ            
            if($CNPJdest!=''){
                $cClausula   = "SELECT D024_Id,
                                       D024_Nome_Empresa 
                                  FROM D024 
                                 WHERE D024_Cnpj='$CNPJdest' 
                                   AND D024_Flag_Ativo='S'";

                $documento = "CNPJ ".$CNPJdest;

            } else if($CPFdest!=''){
                $cClausula   = "SELECT D024_Id,
                                       D024_Nome_Empresa 
                                  FROM D024 
                                 WHERE D024_Cpf='$CPFdest' 
                                   AND D024_Flag_Ativo='S'";

                $documento = "CPF ".$CPFdest;
            }

            $cSql_D024 = mysql_query($cClausula);
            $mD024     = mysql_fetch_array($cSql_D024);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                return "Erro: " . $cErro . "    " . $cClausula;
            }
            if (mysql_num_rows($cSql_D024) <= 0) {
                return "O destinatário com ".$documento." não está cadastrado ou está inativo. Favor Verificar.";
            }

        }

        //SE A EMPRESA FOR O TOMADOR        
        if($Tipo_Conhecimento==3 || $Tipo_Conhecimento==4){
            $encontrou = false;
            //-- VERIFICA SE O TOMADOR JA EXISTE ATRAVES DO CNPJ 
            if($CNPJtoma4 !=''){
                $tomador = "select D024_Id,
                                   D024_Nome_Empresa 
                              from D024 
                             where D024_Cnpj='$CNPJtoma4' 
                               and D024_Flag_Ativo='S'";

                $contador = mysql_query($tomador);
                
                if(mysql_num_rows($contador)>0){
                    $cClausula = $tomador;
                }   
            }      

            //-- VERIFICA SE O REMETENTE JA EXISTE ATRAVES DO CNPJ 
            if($CNPJreme!=''){
                $remetente = "select D024_Id,
                                     D024_Nome_Empresa 
                                from D024 
                               where D024_Cnpj='$CNPJreme' 
                                 and D024_Flag_Ativo='S'";

                $contador = mysql_query($remetente);

                if(mysql_num_rows($contador)>0){
                    $cClausula = $remetente;
                    $encontrou = true;
                }
            }  
            //-- VERIFICA SE O DESTINATARIO JA EXISTE ATRAVES DO CPF            
            if($CPFdest!='' && $encontrou = false){
                $destinoCpf = "select D024_Id,
                                      D024_Nome_Empresa 
                                 from D024
                                where D024_Cpf='$CPFdest' 
                                  and D024_Flag_Ativo='S'";

                $contador = mysql_query($destinoCpf);

                if(mysql_num_rows($contador)>0){
                    $cClausula = $destinoCpf;
                }

            }elseif($encontrou = false){
               //-- VERIFICA SE O DESTINATARIO JA EXISTE ATRAVES DO CNPJ         
                $destino = "select D024_Id,
                                   D024_Nome_Empresa
                              from D024 
                             where D024_Cnpj='$CNPJdest' 
                               and D024_Flag_Ativo='S'";

                $contador = mysql_query($destino);

                if(mysql_num_rows($contador)>0){
                    $cClausula = $destino;
                }
            }                   

            $cSql_D024 = mysql_query($cClausula);
            $mD024     = mysql_fetch_array($cSql_D024);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                return "Erro: " . $cErro . "    " . $cClausula;
            }  
            if ($mD024['D024_Id'] <= 0) {
                return "Este conhecimento é do tipo TOMADOR DE SERVIÇO e não foram encontrados os CNPJ/CPF do destinatário, remetente e tomador! Favor verificar";
            }      
        }

        //-- ENCONTRA A CFOP
        $CFOP = $ide->getElementsByTagName("CFOP")->item(0)->nodeValue;
        $CFOP = substr($CFOP,0,1).".".substr($CFOP,1,3);
        
        // Busca primeiro se existe CFOP vinculada a empresa e é igual a empresa do conhecimento
        $D006  = mysql_query("SELECT D006_Id, 
                                     D006_Flag_Gera_Contas, 
                                     D006_D014_Id, 
                                     D006_D073_Id, 
                                     D006_Codigo_CFOP 
                                FROM D006 
                               WHERE D006_C004_Id = '{$empresaConhecimento}'
                                 AND (replace(replace(replace(D006_Codigo_CFOP,'.',''),',',''),'-','')='$CFOP' 
                           OR LOCATE (REPLACE('$CFOP','.',''), REPLACE(REPLACE(D006_Codigo_CFOP_Contra_Partida,'.',''),'-','')) > 0) 
                                 AND (D006_Flag_Venda_Compra_Outros='TR' 
                                  OR D006_Flag_Venda_Compra_Outros='C' 
                                  OR D006_Flag_Venda_Compra_Outros='DV' 
                                  OR D006_Flag_Venda_Compra_Outros='DC' 
                                  OR (D006_Flag_Venda_Compra_Outros='O' 
                                 AND D006_Flag_Entrada_Saida = 'E') 
                                  OR (D006_Flag_Venda_Compra_Outros='T' 
                                 AND D006_Flag_Entrada_Saida='E')) 
                                 AND D006_Flag_Inativo='N'");

        if(mysql_num_rows($D006)<=0){
           
            // Verificando no campo contra partida, se não achar vê direto no código da CFOP
            $D006  = mysql_query("SELECT D006_Id, 
                                         D006_Flag_Gera_Contas, 
                                         D006_D014_Id, 
                                         D006_D073_Id, 
                                         D006_Codigo_CFOP 
                                    FROM D006 
                                   WHERE (replace(replace(replace(D006_Codigo_CFOP,'.',''),',',''),'-','')='$CFOP' 
                               OR LOCATE (REPLACE('$CFOP','.',''), REPLACE(REPLACE(D006_Codigo_CFOP_Contra_Partida,'.',''),'-','')) > 0) 
                                     AND (D006_Flag_Venda_Compra_Outros='TR' 
                                      OR D006_Flag_Venda_Compra_Outros='C' 
                                      OR D006_Flag_Venda_Compra_Outros='DV' 
                                      OR D006_Flag_Venda_Compra_Outros='DC' 
                                      OR (D006_Flag_Venda_Compra_Outros='O' 
                                     AND D006_Flag_Entrada_Saida = 'E') 
                                      OR (D006_Flag_Venda_Compra_Outros='T' 
                                     AND D006_Flag_Entrada_Saida='E')) 
                                     AND D006_Flag_Inativo='N'");
        
            if(mysql_num_rows($D006)<=0){
                
                $D006 = mysql_query("SELECT D006_Id, 
                                            D006_Flag_Gera_Contas, 
                                            D006_D014_Id, 
                                            D006_D073_Id, 
                                            D006_Codigo_CFOP 
                                       FROM D006 
                                      WHERE D006_Id='$D006_Id' 
                                        AND D006_Flag_Inativo='N'");
            }
        } 

        $mD006 = mysql_fetch_array($D006);
       
        if ($mD006['D006_Id'] <= 0) {
            return "A CFOP " . $CFOP . " não está cadastrada e nem relacionada no campo \"contra partida\". Cadastre e tente novamente."." CFOP:".$mD006['D006_Codigo_CFOP'];
        }

        //PEGANDO OS DADOS RESTANTES RELACIONADO COM OS VALORES DO CONHECIMENTO E DATAS
        $Valor_Conhecimento =$vPrest->getElementsByTagName("vTPrest")->item(0)->nodeValue;
        if (!empty($ICMS00)){
            $Aliquota_ICMS   = $ICMS00->getElementsByTagName("pICMS")->item(0)->nodeValue; 
            $Valor_ICMS      = $ICMS00->getElementsByTagName("vICMS")->item(0)->nodeValue;
            $Valor_Base_ICMS = $ICMS00->getElementsByTagName("vBC")->item(0)->nodeValue;

        }elseif(!empty($ICMSOutraUF)){
            $Aliquota_ICMS   = $ICMSOutraUF->getElementsByTagName("pICMSOutraUF")->item(0)->nodeValue; 
            $Valor_ICMS      = $ICMSOutraUF->getElementsByTagName("vICMSOutraUF")->item(0)->nodeValue;
            $Valor_Base_ICMS = $ICMSOutraUF->getElementsByTagName("vBCOutraUF")->item(0)->nodeValue;

        }else {
            $Aliquota_ICMS   = 0;
            $Valor_ICMS      = 0;
            $Valor_Base_ICMS = 0;
        }

        $Data_Emissao           = $ide->getElementsByTagName("dhEmi")->item(0)->nodeValue;
        $vICMSSTRet             = $dom->getElementsByTagName("vICMSSTRet")->item(0)->nodeValue;
        $Numero_Conhecimento    = $ide->getElementsByTagName("nCT")->item(0)->nodeValue;
        $Modelo                 = $ide->getElementsByTagName("mod")->item(0)->nodeValue;
        $Serie                  = $ide->getElementsByTagName("serie")->item(0)->nodeValue;
        $TipoCTe                = $ide->getElementsByTagName("tpCTe")->item(0)->nodeValue;                

        // CALCULO DATA VENCIMENTO
        //Usa a global para definir quantos dias a partir da Data de Emissão irá somar para calcular a Data de Vencimento
        $dias = $g['C031']['diasGerarDataVencimento'];           
        $Data_Vencimento = date('Y-m-d', strtotime("$dias days",strtotime(substr($Data_Emissao, 0, 10)))); 
        
        //-- INICIA UMA TRANSACAO NO BANCO DE DADOS
        mysql_query('START TRANSACTION');

        $cClausula = "SELECT D022_Id 
                        FROM D022 
                       WHERE D022_Cnpj='{$CNPJemi}'";
        $cSql_D022 = mysql_query($cClausula);
        $mD022     = mysql_fetch_array($cSql_D022);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cErro . "     " . $cClausula;
        }     

        //-- VERIFICA SE A TRANSPORTADORA JA EXISTE NO D022 ATRAVES DO CNPJ
        if (mysql_num_rows($cSql_D022) <= 0) {  
            //-- PEGA A CIDADE E O ESTADO CONFORME O CODIGO IBGE
            $D020 = mysql_query("select D020_Id,
                                        D020_D018_Id
                                   from D020 
                                  where D020_Codigo_IBGE='{$cMun}'") ;

            $mD020 = mysql_fetch_array($D020);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: " . $cErro . "<br />" . $cClausula;
            }

            $D030 = mysql_query("select D030_Id                                  
                                    from D030                              
                                where D030_Codigo_Pais='1058'");

            $mD030 = mysql_fetch_array($D030);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: ".$cErro."<br />".$cClausula;
            }
            
            $cClausula  = " insert into D022 ( ";                
            $cClausula .= " D022_D018_Id, ";                        # 1
            $cClausula .= " D022_D020_Id, ";                        # 2
            $cClausula .= " D022_D036_Id, ";                        # 3
            $cClausula .= " D022_D030_Id, ";                        # 4
            $cClausula .= " D022_Nome_Empresa, ";                   # 5
            $cClausula .= " D022_Flag_Pessoa_Fisica_Juridica, ";    # 6
            $cClausula .= " D022_Cnpj, ";                           # 7
            $cClausula .= " D022_Inscricao_Estadual, ";             # 8
            $cClausula .= " D022_Cep, ";                            # 9
            $cClausula .= " D022_Endereco, ";                       # 10
            $cClausula .= " D022_Bairro, ";                         # 11
            $cClausula .= " D022_DDD_Telefone_1, ";                 # 12
            $cClausula .= " D022_Telefone_1 ";                      # 13
            $cClausula .= " ) values ( ";
            $cClausula .= " '{$mD020['D020_D018_Id']}', ";          # 1 
            $cClausula .= " '{$mD020['D020_Id']}', ";               # 2 
            $cClausula .= " '5', ";                                 # 3 
            $cClausula .= " '{$mD030['D030_Id']}', ";               # 4
            $cClausula .= " '{$xNome}', ";                          # 5 
            $cClausula .= " 'J', ";                                 # 6 
            $cClausula .= " '{$CNPJemi}', ";                        # 7
            $cClausula .= " '{$IE}', ";                             # 8
            $cClausula .= " '{$CEP}', ";                            # 9
            $cClausula .= " '{$lgr}', ";                            # 10
            $cClausula .= " '{$xBairro}', ";                        # 11
            $cClausula .= " '{$DDDfone}', ";                        # 12
            $cClausula .= " '{$telefone}' ";                        # 13
            $cClausula .= " ) ";
            mysql_query($cClausula);
            $mD022['D022_Id'] = $g['mysqlLastId'];
            $cErro = mysql_error();
            if (!empty($cErro)) {
                $retorno = "Clausula: " . $cClausula;
                $retorno .= "ERRO: " . $cErro;
                mysql_query("ROLLBACK");
                return $retorno;
            }
        }

        //-- NAO PERMITE INCLUIR O MESMO CONHECIMENTO COM MESMO NUMERO E MESMA TRANSPORTADORA
        $cClausula = "SELECT T084_Id 
                        FROM T084
                       WHERE T084_Numero_Conhecimento = '{$Numero_Conhecimento}' 
                         AND T084_Chave_Acesso_Cte = '{$Chave_Acesso}'
                         AND T084_D022_Id='{$mD022['D022_Id']}' 
                         AND T084_C004_Id='{$empresaConhecimento}'";
        $cSql_T084 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: ".$cErro."<br />" . $cClausula;
        }

        if (mysql_num_rows($cSql_T084) > 0) {
            mysql_query("ROLLBACK");
            return "O conhecimento $Numero_Conhecimento já foi lançado para essa transportadora. Importação cancelada!";
        }

        //-- VERIFICA SE A TRANSPORTADORA JA EXISTE NO D024 ATRAVES DO CNPJ
        $cClausula = "SELECT D024_Id 
                        FROM D024 
                       WHERE D024_Cnpj='{$CNPJemi}'";
        $cSql_D024 = mysql_query($cClausula);
        $cErro     = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: ".$cErro."<br />".$cClausula;
        }

        if (mysql_num_rows($cSql_D024) <= 0) {  
            //-- PEGA A CIDADE E O ESTADO CONFORME O CODIGO IBGE
            $D020 = mysql_query("select D020_Id,
                                        D020_D018_Id
                                   from D020 
                                  where D020_Codigo_IBGE='{$cMun}'") ;

            $mD020 = mysql_fetch_array($D020);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: ".$cErro."<br />".$cClausula;
            }
            
            $D030 = mysql_query("select D030_Id                                  
                                   from D030                              
                                  where D030_Codigo_Pais='1058'");

            $mD030 = mysql_fetch_array($D030);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: ".$cErro."<br />".$cClausula;
            }
            
            $cClausula  = " insert into D024 ( ";                
            $cClausula .= " D024_D018_Id, ";                        # 1
            $cClausula .= " D024_D020_Id, ";                        # 2
            $cClausula .= " D024_D030_Id, ";                        # 3
            $cClausula .= " D024_Nome_Empresa, ";                   # 4
            $cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";    # 5
            $cClausula .= " D024_Nome_Fantasia, ";                  # 6
            $cClausula .= " D024_Flag_Ativo, ";                     # 7
            $cClausula .= " D024_Inscricao_Estadual, ";             # 8
            $cClausula .= " D024_Cnpj, ";                           # 9
            $cClausula .= " D024_Endereco, ";                       # 10
            $cClausula .= " D024_Cep, ";                            # 11
            $cClausula .= " D024_Numero, ";                         # 12
            $cClausula .= " D024_Bairro, ";                         # 13
            $cClausula .= " D024_DDD_Telefone_1, ";                 # 14
            $cClausula .= " D024_Telefone_1, ";                     # 15
            $cClausula .= " D024_Flag_Pre_Cadastro, ";              # 16
            $cClausula .= " D024_Data_Cadastro, ";                  # 17
            $cClausula .= " D024_Flag_Cliente, ";                   # 18
            $cClausula .= " D024_Observacoes, ";                    # 19
            $cClausula .= " D024_Flag_Fornecedor ";                 # 20
            $cClausula .= " ) values ( ";
            $cClausula .= " '{$mD020['D020_D018_Id']}', ";          # 1 
            $cClausula .= " '{$mD020['D020_Id']}', ";               # 2 
            $cClausula .= " '{$mD030['D030_Id']}', ";               # 3
            $cClausula .= " '{$xNome}', ";                          # 4 
            $cClausula .= " 'J', ";                                 # 5 
            $cClausula .= " '{$xFant}', ";                          # 6
            $cClausula .= " 'S', ";                                 # 7
            $cClausula .= " '{$IE}', ";                             # 8
            $cClausula .= " '{$CNPJemi}', ";                        # 9
            $cClausula .= " '{$lgr}', ";                            # 10
            $cClausula .= " '{$CEP}', ";                            # 11
            $cClausula .= " '{$nro}', ";                            # 12
            $cClausula .= " '{$xBairro}', ";                        # 13
            $cClausula .= " '{$DDDfone}', ";                        # 14
            $cClausula .= " '{$telefone}', ";                       # 15
            $cClausula .= " 'N', ";                                 # 16
            $cClausula .= "  now(), ";                              # 17
            $cClausula .= " 'N', ";                                 # 18
            $cClausula .= " 'FORNECEDOR INCLUIDO AUTOMATICAMENTE AO IMPORTAR XML DO CONHECIMENTO {$Numero_Conhecimento}', "; # 19
            $cClausula .= " 'S' ";                                  # 20
            $cClausula .= " ) ";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                
                $retorno  = "Clausula: " . $cClausula;
                $retorno .= "ERRO: " . $cErro;
                mysql_query("ROLLBACK");
                return $retorno;
            }
        }
        
        // Popular campos com base na CFOP
        $cClausula = mysql_query("SELECT D006_Base_Calculo_Credito, D006_Indicador_Natureza_Frete  FROM D006 WHERE D006_Id = '{$mD006['D006_Id']}'");
        $mD006Consulta = mysql_fetch_array($cClausula);        
        
        // Popula origem e destino
        $cClausula = mysql_query("SELECT 
                                        D020_Origem.D020_Id as D020Origem,
                                        D020_Origem.D020_D018_Id as D018Origem,
                                        D020_Destino.D020_Id as D020Destino,
                                        D020_Destino.D020_D018_Id as D018Destino
                                    FROM C004                                    
                                    LEFT JOIN D020 AS D020_Origem ON D020_Origem.D020_Id = C004_D020_Id                                    
                                    LEFT JOIN D024 ON D024_Id = {$mD024['D024_Id']}
                                    LEFT JOIN D020 AS D020_Destino ON D020_Destino.D020_Id = D024_D020_Id                                
                                    WHERE C004_Id = '{$empresaConhecimento}'
                                ");
       
        $mD020OrigemDestino = mysql_fetch_array($cClausula);
        
        $OrigemD020_Id = $mD020OrigemDestino['D020Origem'];
        $OrigemD018_Id = $mD020OrigemDestino['D018Origem'];
        $DestinoD020_Id = $mD020OrigemDestino['D020Destino'];
        $DestinoD018_Id = $mD020OrigemDestino['D018Destino'];            
        

        if($Tipo_Nota != 'EMITIDA') {
            $OrigemD020_Id = $mD020OrigemDestino['D020Destino'];
            $OrigemD018_Id = $mD020OrigemDestino['D018Destino'];
            $DestinoD020_Id = $mD020OrigemDestino['D020Origem'];
            $DestinoD018_Id = $mD020OrigemDestino['D018Origem'];
        }         
        // INCLUI O REGISTRO NA TABELA DE CONHECIMENTOS
        // T084_Situacao valor padrão 00 - Documento
        $cClausula = <<<EOT
            insert into T084 (
                T084_C004_Id,
                T084_D022_Id,          
                T084_D024_Id,           
                T084_D006_Id,          
                T084_D073_Id,   
                T084_Tipo_Nota,       
                T084_Numero_Conhecimento,
                T084_Data_Lancamento,
                T084_Data_Emissao,
                T084_Data_Vencimento,
                T084_Valor_Base_ICMS,
                T084_Valor_Conhecimento,
                T084_Aliquota_ICMS,
                T084_Valor_ICMS,
                T084_Valor_ICMS_ST,
                T084_Chave_Acesso_Cte,
                T084_Numero_Protocolo_Cte,
                T084_C007_Id,
                T084_Modelo,
                T084_Serie,
                T084_Tipo_Conhecimento,
                T084_Situacao ,
                T084_Base_Calculo_Credito,
                T084_Indicador_Natureza_Frete,
                T084_Indicador_Tipo_Frete,
                T084_D018_Id_Origem, 
                T084_D020_Id_Origem,  
                T084_D018_Id_Destino, 
                T084_D020_Id_Destino
                )
                values
                ('$empresaConhecimento',
                 '{$mD022['D022_Id']}',
                 '{$mD024['D024_Id']}',
                 '{$mD006['D006_Id']}',
                 '{$mD006['D006_D073_Id']}',
                 '$Tipo_Nota',
                 '$Numero_Conhecimento',
                  current_date(),
                 '$Data_Emissao',
                 '$Data_Vencimento',
                 '$Valor_Base_ICMS',
                 '$Valor_Conhecimento',
                 '$Aliquota_ICMS ',
                 '$Valor_ICMS',
                 '$vICMSSTRet',
                 '$Chave_Acesso',
                 '$Protocolo',
                 '{$g['usuarioAtual']}',
                 '$Modelo',
                 '$Serie',
                 '$TipoCTe',
                 '00',
                 '{$mD006Consulta['D006_Base_Calculo_Credito']}',
                 '{$mD006Consulta['D006_Indicador_Natureza_Frete']}',
                 '$Indicador_Tipo_Frete',
                 '{$OrigemD018_Id}',
                 '{$OrigemD020_Id}',
                 '{$DestinoD018_Id}',
                 '{$DestinoD020_Id}')
EOT;        

        $cSql  = mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cErro . "<br />" . $cClausula;
        } else {
            $T084_Id = $g['mysqlLastId'];
        }

        $camposParticipantes = array(
            "T084_Tomador_CNPJ = '" . mysql_real_escape_string($tomadorXML['cnpj']) . "'",
            "T084_Tomador_CPF = '" . mysql_real_escape_string($tomadorXML['cpf']) . "'",
            "T084_Tomador_IE = '" . mysql_real_escape_string($tomadorXML['ie']) . "'",
            "T084_Tomador_Nome = '" . mysql_real_escape_string($tomadorXML['nome']) . "'",
            "T084_Tomador_Nome_Fantasia = '" . mysql_real_escape_string($tomadorXML['fantasia']) . "'",
            "T084_Tomador_Rua = '" . mysql_real_escape_string($tomadorXML['logradouro']) . "'",
            "T084_Tomador_Numero = '" . mysql_real_escape_string($tomadorXML['numero']) . "'",
            "T084_Tomador_Bairro = '" . mysql_real_escape_string($tomadorXML['bairro']) . "'",
            "T084_Tomador_Codigo_Cidade_IBGE = '" . mysql_real_escape_string($tomadorXML['codigo_cidade']) . "'",
            "T084_Tomador_Cidade = '" . mysql_real_escape_string($tomadorXML['cidade']) . "'",
            "T084_Tomador_UF = '" . mysql_real_escape_string($tomadorXML['uf']) . "'",
            "T084_Emitente_CNPJ = '" . mysql_real_escape_string($emitenteXML['cnpj']) . "'",
            "T084_Emitente_IE = '" . mysql_real_escape_string($emitenteXML['ie']) . "'",
            "T084_Emitente_Nome = '" . mysql_real_escape_string($emitenteXML['nome']) . "'",
            "T084_Emitente_Rua = '" . mysql_real_escape_string($emitenteXML['logradouro']) . "'",
            "T084_Emitente_Numero = '" . mysql_real_escape_string($emitenteXML['numero']) . "'",
            "T084_Emitente_Bairro = '" . mysql_real_escape_string($emitenteXML['bairro']) . "'",
            "T084_Emitente_Codigo_Cidade_IBGE = '" . mysql_real_escape_string($emitenteXML['codigo_cidade']) . "'",
            "T084_Emitente_Cidade = '" . mysql_real_escape_string($emitenteXML['cidade']) . "'",
            "T084_Emitente_CEP = '" . mysql_real_escape_string($emitenteXML['cep']) . "'",
            "T084_Emitente_UF = '" . mysql_real_escape_string($emitenteXML['uf']) . "'",
            "T084_Emitente_Telefone = '" . mysql_real_escape_string($emitenteXML['telefone']) . "'",
            "T084_Remetente_CNPJ = '" . mysql_real_escape_string($remetenteXML['cnpj']) . "'",
            "T084_Remetente_CPF = '" . mysql_real_escape_string($remetenteXML['cpf']) . "'",
            "T084_Remetente_IE = '" . mysql_real_escape_string($remetenteXML['ie']) . "'",
            "T084_Remetente_Nome = '" . mysql_real_escape_string($remetenteXML['nome']) . "'",
            "T084_Remetente_Telefone = '" . mysql_real_escape_string($remetenteXML['telefone']) . "'",
            "T084_Remetente_Rua = '" . mysql_real_escape_string($remetenteXML['logradouro']) . "'",
            "T084_Remetente_Numero = '" . mysql_real_escape_string($remetenteXML['numero']) . "'",
            "T084_Remetente_Bairro = '" . mysql_real_escape_string($remetenteXML['bairro']) . "'",
            "T084_Remetente_Codigo_Cidade_IBGE = '" . mysql_real_escape_string($remetenteXML['codigo_cidade']) . "'",
            "T084_Remetente_Cidade = '" . mysql_real_escape_string($remetenteXML['cidade']) . "'",
            "T084_Remetente_CEP = '" . mysql_real_escape_string($remetenteXML['cep']) . "'",
            "T084_Remetente_UF = '" . mysql_real_escape_string($remetenteXML['uf']) . "'",
            "T084_Remetente_Codigo_Pais = '" . mysql_real_escape_string($remetenteXML['codigo_pais']) . "'",
            "T084_Remetente_Nome_Pais = '" . mysql_real_escape_string($remetenteXML['nome_pais']) . "'",
            "T084_Expedidor_CNPJ = '" . mysql_real_escape_string($expedidorXML['cnpj']) . "'",
            "T084_Expedidor_CPF = '" . mysql_real_escape_string($expedidorXML['cpf']) . "'",
            "T084_Expedidor_IE = '" . mysql_real_escape_string($expedidorXML['ie']) . "'",
            "T084_Expedidor_Nome = '" . mysql_real_escape_string($expedidorXML['nome']) . "'",
            "T084_Expedidor_Rua = '" . mysql_real_escape_string($expedidorXML['logradouro']) . "'",
            "T084_Expedidor_Numero = '" . mysql_real_escape_string($expedidorXML['numero']) . "'",
            "T084_Expedidor_Bairro = '" . mysql_real_escape_string($expedidorXML['bairro']) . "'",
            "T084_Expedidor_Codigo_Cidade_IBGE = '" . mysql_real_escape_string($expedidorXML['codigo_cidade']) . "'",
            "T084_Expedidor_Cidade = '" . mysql_real_escape_string($expedidorXML['cidade']) . "'",
            "T084_Expedidor_CEP = '" . mysql_real_escape_string($expedidorXML['cep']) . "'",
            "T084_Expedidor_UF = '" . mysql_real_escape_string($expedidorXML['uf']) . "'",
            "T084_Recebedor_CNPJ = '" . mysql_real_escape_string($recebedorXML['cnpj']) . "'",
            "T084_Recebedor_CPF = '" . mysql_real_escape_string($recebedorXML['cpf']) . "'",
            "T084_Recebedor_IE = '" . mysql_real_escape_string($recebedorXML['ie']) . "'",
            "T084_Recebedor_Nome = '" . mysql_real_escape_string($recebedorXML['nome']) . "'",
            "T084_Recebedor_Rua = '" . mysql_real_escape_string($recebedorXML['logradouro']) . "'",
            "T084_Recebedor_Numero = '" . mysql_real_escape_string($recebedorXML['numero']) . "'",
            "T084_Recebedor_Bairro = '" . mysql_real_escape_string($recebedorXML['bairro']) . "'",
            "T084_Recebedor_Codigo_Cidade_IBGE = '" . mysql_real_escape_string($recebedorXML['codigo_cidade']) . "'",
            "T084_Recebedor_Cidade = '" . mysql_real_escape_string($recebedorXML['cidade']) . "'",
            "T084_Recebedor_CEP = '" . mysql_real_escape_string($recebedorXML['cep']) . "'",
            "T084_Recebedor_UF = '" . mysql_real_escape_string($recebedorXML['uf']) . "'",
            "T084_Destinatario_CNPJ = '" . mysql_real_escape_string($destinatarioXML['cnpj']) . "'",
            "T084_Destinatario_CPF = '" . mysql_real_escape_string($destinatarioXML['cpf']) . "'",
            "T084_Destinatario_IE = '" . mysql_real_escape_string($destinatarioXML['ie']) . "'",
            "T084_Destinatario_Nome = '" . mysql_real_escape_string($destinatarioXML['nome']) . "'",
            "T084_Destinatario_Telefone = '" . mysql_real_escape_string($destinatarioXML['telefone']) . "'",
            "T084_Destinatario_Rua = '" . mysql_real_escape_string($destinatarioXML['logradouro']) . "'",
            "T084_Destinatario_Numero = '" . mysql_real_escape_string($destinatarioXML['numero']) . "'",
            "T084_Destinatario_Bairro = '" . mysql_real_escape_string($destinatarioXML['bairro']) . "'",
            "T084_Destinatario_Codigo_Cidade_IBGE = '" . mysql_real_escape_string($destinatarioXML['codigo_cidade']) . "'",
            "T084_Destinatario_Cidade = '" . mysql_real_escape_string($destinatarioXML['cidade']) . "'",
            "T084_Destinatario_CEP = '" . mysql_real_escape_string($destinatarioXML['cep']) . "'",
            "T084_Destinatario_UF = '" . mysql_real_escape_string($destinatarioXML['uf']) . "'",
            "T084_Destinatario_Codigo_Pais = '" . mysql_real_escape_string($destinatarioXML['codigo_pais']) . "'",
            "T084_Destinatario_Nome_Pais = '" . mysql_real_escape_string($destinatarioXML['nome_pais']) . "'",
            "T084_Destinatario_Email = '" . mysql_real_escape_string($destinatarioXML['email']) . "'"
        );

        $cClausula = "UPDATE T084 SET " . implode(", ", $camposParticipantes) . " WHERE T084_Id = '{$T084_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cErro . "<br />" . $cClausula;
        }

        // Pega valores de peso
        if (!empty($infCarga)) {
            $infQs = $dom->getElementsByTagName('infQ');
            
            if (!empty($infQs)) {
                foreach ($infQs as $infQ) {
                    $tpMed = $infQ->getElementsByTagName('tpMed')->item(0)->nodeValue;
                    $qCarga = $infQ->getElementsByTagName('qCarga')->item(0)->nodeValue;
                    
                    if ($tpMed == "PESO BASE DE CALCULO") {
                        $vlrPesoBase = $qCarga;
                    } else if ($tpMed == "PESO CUBADO") {
                        $vlrPesoCubado = $qCarga;
                    } else if ($tpMed == "PESO REAL") {
                        $vlrPesoReal = $qCarga;
                    } 
                }
            }
        }

        // Se tem chv de compl pega o id do CTe original
        if (!empty($chaveAcessoCteOriginal)) {
            $sqlT084 = mysql_query("SELECT T084_Id FROM T084 WHERE T084_Chave_Acesso_Cte = '{$chaveAcessoCteOriginal}' AND T084_C004_Id = '{$empresaConhecimento}'");
            $mSql = mysql_fetch_array($sqlT084);
            $cteOriginal = $mSql['T084_Id'];
        }

        // Preenche ligação entre conhecimento original e complemento
        $cClausula = <<<EOT
            insert into T084A (
                T084A_T084_Id,
                T084A_Cte_Original_Id,
                T084A_Chave_Acesso_Cte_Original,
                T084A_Peso_Base_Calculo,
                T084A_Peso_Cubado,
                T084A_Peso_Real,
                T084A_Flag_Empresa_Tomadora
                )
            values
                ('{$T084_Id}',
                 '{$cteOriginal}',
                 '{$chaveAcessoCteOriginal}',
                 '{$vlrPesoBase}',
                 '{$vlrPesoCubado}',
                 '{$vlrPesoReal}',
                 '{$empresaTomadora}')
EOT;    

        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cErro . "<br />" . $cClausula;
        }

        // Se é compl pega as NF do original
        if ($cteOriginal > 0) {
            $selectT085 = mysql_query(" SELECT
                                            T085_Id,
                                            T085_T013_Id,
                                            T085_T084_Id,
                                            T085_Numero_Nota_Fiscal,
                                            T085_Valor_Total_Nota,
                                            T085_Valor_Rateio,
                                            T085_Flag_Fechado,
                                            T085_T007_Id,
                                            T085_Flag_Tipo_Nota,
                                            T085_Chave_Acesso_Nfe,
                                            T085_Flag_Tipo_Documento,
                                            T085_Data_Prevista_Entrega,
                                            T085_C004_Origem_Id,
                                            T085_Tipo_Documento_Originario,
                                            T085_Descricao_Documento,
                                            T085_Data_Emissao
                                        FROM T085
                                            LEFT JOIN T084 ON T084_Id = T085_T084_Id
                                        WHERE
                                            T084_Chave_Acesso_Cte = '{$chaveAcessoCteOriginal}'
                                            AND T084_C004_Id = '{$empresaConhecimento}'
                                        ORDER BY T085_Id");
            
            // Insere todas as notas no conhecimento complementar
            while($mT085 = mysql_fetch_array($selectT085)) {
                $insertT085 = <<<EOT
                    insert into T085 (
                        T085_T013_Id,
                        T085_T084_Id,
                        T085_Numero_Nota_Fiscal,
                        T085_Valor_Total_Nota,
                        T085_Valor_Rateio,
                        T085_Flag_Fechado,
                        T085_T007_Id,
                        T085_Flag_Tipo_Nota,
                        T085_Chave_Acesso_Nfe,
                        T085_Flag_Tipo_Documento,
                        T085_Data_Prevista_Entrega,
                        T085_C004_Origem_Id,
                        T085_Tipo_Documento_Originario,
                        T085_Descricao_Documento,
                        T085_Data_Emissao
                    ) values (
                        '{$mT085['T085_T013_Id']}',
                        '{$T084_Id}',
                        '{$mT085['T085_Numero_Nota_Fiscal']}',
                        '{$mT085['T085_Valor_Total_Nota']}',
                        '{$mT085['T085_Valor_Rateio']}',
                        '{$mT085['T085_Flag_Fechado']}',
                        '{$mT085['T085_T007_Id']}',
                        '{$mT085['T085_Flag_Tipo_Nota']}',
                        '{$mT085['T085_Chave_Acesso_Nfe']}',
                        '{$mT085['T085_Flag_Tipo_Documento']}',
                        '{$mT085['T085_Data_Prevista_Entrega']}',
                        '{$mT085['T085_C004_Origem_Id']}',
                        '{$mT085['T085_Tipo_Documento_Originario']}',
                        '{$mT085['T085_Descricao_Documento']}',
                        '{$mT085['T085_Data_Emissao']}'
                    )
EOT;
        
                mysql_query($insertT085);
                $cErro     = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . $cErro . "     " . $cClausula;
                }
            } 
        }

        // VINCULA AS NOTAS AO CONHECIMENTO
        $countNFe = 0;
        while (!empty($dom->getElementsByTagName("infNFe")->item($countNFe))) {

            // O XML Contem a chave de acesso da NF-e referente ao conhecimento
            $infNF    = $dom->getElementsByTagName("infNFe")->item($countNFe++);
            $ChaveNfe = $infNF->getElementsByTagName("chave")->item(0)->nodeValue;

            if (!empty($ChaveNfe)){

                //VERIFICA SE A NF-E EMITIDA EXISTE CASO O CONHECIMENTO CONTENHA A CHAVE DE ACESSO
                $cClausula = "SELECT T007_Id,
                                     T007_Numero_Nota_Fiscal,
                                     T007_Valor_Total,
                                     T007_C004_Id 
                                FROM T007 
                               WHERE T007_Chave_Acesso_Nfe='$ChaveNfe' 
                                 AND T007_D024_Id='{$mD024['D024_Id']}' 
                                 AND T007_Flag_Cancelada='N'";

                $cSql_T007 = mysql_query($cClausula);
                $mT007     = mysql_fetch_array($cSql_T007);
                $cErro     = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . $cErro . "     " . $cClausula;
                }

                if (mysql_num_rows($cSql_T007) > 0) {
                    
                     $Numero_Nota_Fiscal  = $mT007['T007_Numero_Nota_Fiscal'];
                     $Valor_Total_Nota    = $mT007['T007_Valor_Total']; 
                     $empresaConhecimento = (!empty($mT007['T007_C004_Id'])) ? $mT007['T007_C004_Id'] : $g['empresaAtual'];
                     $tipoNota            = 'S';
                     $fechado             = 'S';
                     
                }else{
                    
                    //VERIFICA SE A NF-E RECEBIDA EXISTE
                    $cClausula  = "SELECT T013_Id,
                                          T013_Numero_Nota_Fiscal,
                                          T013_Valor_Total,
                                          T013_C004_Id 
                                     FROM T013 
                                    WHERE T013_Chave_Acesso_Nfe='$ChaveNfe' 
                                      AND T013_D024_Id='{$mD024['D024_Id']}' 
                                      AND T013_Flag_Cancelada = 'N'";

                    $cSql_T013 = mysql_query($cClausula);
                    $mT013     = mysql_fetch_array($cSql_T013);
                    $cErro     = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "Erro: " . $cErro . "     " . $cClausula;
                    }

                    if (mysql_num_rows($cSql_T013) <= 0) {
                        //Caso a empresa seja tomadora, vamos fazer o lançamento do CT-e, mesmo que não haja notas
                        if ((!empty($toma4) || $Tipo_Conhecimento == 4) && $empresaTomadora == 'S') {
                            continue;
                        }
                        mysql_query("ROLLBACK");
                        return "Não foi encontrada NF-e recebida ou emitida com chave de acesso $ChaveNfe.\\n\\nPor favor verifique:\\n\\nSe possui NFe no sistema pela chave de acesso para o cliente \\n\\nSe o cliente da nota tem o mesmo cliente encontrado no arquivo: {$mD024['D024_Nome_Empresa']} - $documento\\n\\nSe a nota não está cancelada.\\n\\nSe a chave de acesso é válida no site da sefaz, através do link: http://www.nfe.fazenda.gov.br/portal/consultaRecaptcha.aspx?tipoConsulta=completa&tipoConteudo=XbSeqxE8pl8=\\n\\n";
                    }
                    
                    $Numero_Nota_Fiscal  = $mT013['T013_Numero_Nota_Fiscal'];
                    $Valor_Total_Nota    = $mT013['T013_Valor_Total']; 
                    $empresaConhecimento = (!empty($mT013['T013_C004_Id'])) ? $mT013['T013_C004_Id'] : $g['empresaAtual'];
                    $tipoNota            = 'E';
                    $fechado             = '';
                }

                // INCLUI O REGISTRO DAS NOTAS FISCAIS RECEBIDAS RELACIONADAS AO CONHECIMENTO, CASO EXISTAM.
                $cClausula = <<<EOT
                    insert into T085 (
                                T085_T013_Id,
                                T085_T007_Id,
                                T085_T084_Id,
                                T085_Numero_Nota_Fiscal,
                                T085_Valor_Total_Nota,
                                T085_Valor_Rateio,
                                T085_Flag_Fechado,
                                T085_Flag_Tipo_Nota,
                                T085_Chave_Acesso_Nfe
                                )
                                values
                                ('{$mT013['T013_Id']}',
                                 '{$mT007['T007_Id']}',
                                 '{$T084_Id}',
                                 '{$Numero_Nota_Fiscal}',
                                 '{$Valor_Total_Nota}',
                                 '0',
                                 '{$fechado}',
                                 '{$tipoNota}',
                                 '{$ChaveNfe}')
EOT;
                $cSql  = mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . $cErro . "<br/>" . $cClausula;
                }                
                
                if($g['C031']['atualizarAliquotasConhecimentoAoImportarXML']  === 'S')  {
                    $this->atualizarAliquotasConhecimento($T084_Id);
                }
            }        
        }


        // INCLUI O REGISTRO DOS TITULOS A PAGAR RELACIONADOS AO CONHECIMENTO
        $sqlT084 = mysql_query("SELECT * 
                                  FROM T084 
                             LEFT JOIN D006 ON D006_Id = T084_D006_Id 
                             LEFT JOIN D014 ON D014_Id = D006_D014_Id 
                             LEFT JOIN D022 ON D022_Id = T084_D022_Id 
                                 WHERE T084_Id = '{$T084_Id}'");
        $T084    = mysql_fetch_array($sqlT084);

        if($T084['D006_Flag_Gera_Contas'] == 'P' && $g['C031']['gerarPagarAutoAoImportarCTe']  === 'S'){            
            if ((float) $T084['T084_Valor_Conhecimento'] <= 0) {
                mysql_query("ROLLBACK");
                return "Não é possível gerar contas a pagar para conhecimento com valor zerado ou negativo.";
            }

            if((empty($T084['T084_D073_Id'])) || $T084['T084_D073_Id'] == NULL) {
                mysql_query("ROLLBACK");
                return "Não foi definido nenhum Centro de custo!";
            }

            if(($T084['D006_Flag_Gera_Contas'] == 'P' || $T084['D006_Flag_Gera_Contas'] == 'R') && (empty($T084['D006_D014_Id']) || $T084['D006_D014_Id'] <= 0)){
                $tipoConta = ($T084['D006_Flag_Gera_Contas'] == 'P') ? 'pagar' : 'receber';
                mysql_query("ROLLBACK");
                return "A CFOP gera contas a {$tipoConta} e está sem uma conta definida! Verifique o cadastro da CFOP.";
            }

            $sqlD024 = mysql_query("SELECT * 
                                      FROM D024 
                                     WHERE D024_Cnpj = '{$T084['D022_Cnpj']}' 
                                       AND D024_Flag_Ativo != 'N'");

            if (mysql_num_rows($sqlD024) <= 0) {
                mysql_query("ROLLBACK");
                return "Não foi possível incluir a conta a pagar, verifique se a transportadora está cadastrada na opção Cliente/Fornecedor";
            } else {
                $D024 = mysql_fetch_array($sqlD024);
            }
            
            $sql     = "INSERT INTO T015 (
                                    T015_D024_Id,
                                    T015_Data_Emissao,
                                    T015_Data_Vencimento,
                                    T015_Numero_Documento,
                                    T015_Numero_Duplicata,
                                    T015_Valor_Duplicata,
                                    T015_Valor_Juros,
                                    T015_Valor_Desconto,
                                    T015_Valor_Pago,
                                    T015_Valor_Total,
                                    T015_Observacao,
                                    T015_Valor_Original,
                                    T015_Data_Vencimento_Original,
                                    T015_C004_Id,
                                    T015_Data_Pagamento,
                                    T015_T084_Id,
                                    T015_D014_Id,
                                    T015_D032_Id,
                                    T015_D073_Id
                                ) VALUES (
                                    '{$D024['D024_Id']}',
                                    '{$T084['T084_Data_Emissao']}',
                                    '{$T084['T084_Data_Vencimento']}',
                                    '{$T084['T084_Numero_Conhecimento']}',
                                    '{$T084['T084_Numero_Conhecimento']}',
                                    '{$T084['T084_Valor_Conhecimento']}',
                                    '0',
                                    '0',
                                    '0',
                                    '{$T084['T084_Valor_Conhecimento']}',
                                    'Conhecimento de Transporte {$T084['T084_Numero_Conhecimento']}',
                                    '{$T084['T084_Valor_Conhecimento']}',
                                    '{$T084['T084_Data_Vencimento']}',
                                    '{$empresaConhecimento}',
                                    '0000-00-00',
                                    '{$T084['T084_Id']}',
                                    '{$T084['D006_D014_Id']}',
                                    '{$T084['D014_D032_Id']}',
                                    '{$T084['T084_D073_Id']}'
                                )";

            $retorno = mysql_query($sql);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: " . $cErro . "<br/>" . $sql;
            }
        }
                        
        mysql_query("COMMIT");        

        return true;
    }

     /**
     * atualizarAliquotasConhecimento
     *
     * @param int $T084_Id
     * @return true/string
     */
    public function atualizarAliquotasConhecimento($T084_Id)
    {
        global $g;    
        $valorBasePIS    = 0;
        $aliquotaPIS     = 0;
        $valorBaseCOFINS = 0;
        $aliquotaCOFINS  = 0;

        $T084 = mysql_query("SELECT T084_Valor_Conhecimento,
                                    D006_Flag_Isento_PIS,
                                    D006_Flag_Isento_COFINS,
                                    C004_Percentual_PIS,
                                    C004_Percentual_COFINS,
                                    T084_Valor_ICMS,
                                    D006_Flag_Deduz_ICMS_Base_PIS,
                                    D006_Flag_Deduz_ICMS_Base_COFINS,
                                    D006_Aliquota_PIS,
                                    D006_Aliquota_COFINS,
                                    D006_Percentual_Reducao_PIS,
                                    D006_Percentual_Reducao_COFINS
                               FROM T084 
                          LEFT JOIN D006 ON D006_Id = T084_D006_Id 
                          LEFT JOIN C004 ON C004_Id = '{$g['empresaAtual']}'
                              WHERE T084_Id = '{$T084_Id}'"
        );
    
        $mT084 = mysql_fetch_array($T084);
        
        if($mT084['D006_Flag_Isento_PIS'] !== 'S') {
            $aliquotaPIS = $mT084['C004_Percentual_PIS'];
            if($mT084['D006_Aliquota_PIS'] > 0){
                $aliquotaPIS = $mT084['D006_Aliquota_PIS'];
            }
        }
        
        if($mT084['D006_Flag_Isento_COFINS'] !== 'S') {
            $aliquotaCOFINS = $mT084['C004_Percentual_COFINS'];
            if($mT084['D006_Aliquota_COFINS'] > 0){
                $aliquotaCOFINS = $mT084['D006_Aliquota_COFINS'];
            }
        }

        // Montagem da base, valida dedução ICMS e percentual redução Base
        $valorBasePIS    = ($mT084['T084_Valor_Conhecimento'] - (($mT084['D006_Flag_Deduz_ICMS_Base_PIS'] == 'S')    ? $mT084['T084_Valor_ICMS'] : 0)) * (($mT084['D006_Percentual_Reducao_PIS'] > 0)    ? ((100 - $mT084['D006_Percentual_Reducao_PIS']) / 100) : 1);
        $valorBaseCOFINS = ($mT084['T084_Valor_Conhecimento'] - (($mT084['D006_Flag_Deduz_ICMS_Base_COFINS'] == 'S') ? $mT084['T084_Valor_ICMS'] : 0)) * (($mT084['D006_Percentual_Reducao_COFINS'] > 0) ? ((100 - $mT084['D006_Percentual_Reducao_COFINS']) / 100) : 1);

        $valorPIS    = $valorBasePIS * ($aliquotaPIS / 100);
        $valorCOFINS = $valorBaseCOFINS * ($aliquotaCOFINS / 100);


        mysql_query("UPDATE T084 SET T084_Valor_Base_PIS    = '{$valorBasePIS}',
                                     T084_Valor_Base_COFINS = '{$valorBaseCOFINS}',
                                     T084_Aliquota_PIS      = '{$aliquotaPIS}',
                                     T084_Aliquota_COFINS   = '{$aliquotaCOFINS}',
                                     T084_Valor_PIS         = '{$valorPIS}',
                                     T084_Valor_COFINS      = '{$valorCOFINS}'
                               WHERE T084_Id = '{$T084_Id}'");

        return true;
    }
}


