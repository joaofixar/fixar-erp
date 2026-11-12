<?php
namespace hardness;
definirClasse('IMP003');
class IMP003_
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
        $T015_Id = mysql_real_escape_string($T015_Id);
        $sql     = "DELETE FROM T015 WHERE T015_Id='$T015_Id'";
        mysql_query($sql);
        return true;
    }
    
    /**
     * updateT161_Flag_Ok
     *
     * @param String $D122_Id
     * @param String $T075_Id
     * @param String $selected
     * @return bool
     */     
    public function updateT161_Flag_Ok($D122_Id, $T075_Id, $selected)
    {
        global $g;
        
        $T161  = mysql_query("SELECT T161_Id FROM T161 WHERE T161_D122_Id='$D122_Id' AND T161_T075_Id='$T075_Id'");
        $mT161 = mysql_fetch_array($T161);
        
        if (mysql_num_rows($T161) <= 0) {
            mysql_query("INSERT INTO T161 (T161_D122_Id,T161_T075_Id,T161_Flag_Ok, T161_Data)
                                   VALUES ('$D122_Id','$T075_Id','S', curdate())");
        } else {
            mysql_query("UPDATE T161 SET T161_Flag_Ok='$selected', T161_Data=curdate()
                          WHERE T161_Id='{$mT161['T161_Id']}'");
        }
        return true;  
    }
    
    /**
     * insereRegistrosT161
     *
     * @param String $T075_Id
     */     
    public function insereRegistrosT161($T075_Id)
    {
        
        $sql = "SELECT * FROM T075 WHERE T075_Id='{$T075_Id}'";
        
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        
        $sql = "SELECT D122_Id FROM D122";
        
        $resultado = mysql_query($sql);
        
        while ($row = mysql_fetch_array($resultado)) {
            
            $T161  = mysql_query("SELECT T161_Id FROM T161 WHERE T161_D122_Id='{$row['D122_Id']}' AND T161_T075_Id='$T075_Id'");
            $mT161 = mysql_fetch_array($T161);
            
            if (mysql_num_rows($T161) <= 0) {
                mysql_query("INSERT INTO T161 (T161_D122_Id,T161_T075_Id,T161_Flag_Ok)
                                       VALUES ('{$row['D122_Id']}','$T075_Id','N')");
            } 
        } 
    }
    
    /**
     * montarEmail
     *
     * @param String $T075_Id
     * @return array
     */     
    public function montarEmail($T075_Id)
    {
        global $g;
        $T075_Id = mysql_real_escape_string($T075_Id);
        $sql     = <<<EOT
        SELECT * FROM T075
        LEFT JOIN D024 as FORNECEDOR ON FORNECEDOR.D024_Id=T075_D024_Id
        LEFT JOIN D024 as DESPACHANTE ON DESPACHANTE.D024_Id=T075_D119_Id
        LEFT JOIN D119 ON D119_Id=T075_D119_Id
        LEFT JOIN D118 ON D118_Id=T075_D118_Id
        LEFT JOIN D044 ON D044_Id=T075_D044_Id
        WHERE T075_Id='{$T075_Id}'
EOT;
        $mT075   = mysql_query($sql);
        
        $array = array();
        
        $array['Para']    = $mT075['FORNECEDOR.D024_E_Mail'];
        $array['Assunto'] = "Declaração de Importação [{$T075_Id}]";
        
        $array['Corpo'] = "";
        
        return $array;
    }
    
    /**
     * Calcular
     *
     * @param String $T075_Id
     * @return bool
     */     
    public function Calcular($T075_Id)
    {
        $T055 = mysql_query("SELECT T055_Id
                               FROM T055 
                              WHERE T055_T075_Id='{$T075_Id}'");
        
        while ($mT055 = mysql_fetch_array($T055)) {
            mysql_query("call T055_Gravar_Totalizacao('$mT055[T055_Id]')");
            $error = mysql_error();
            if(!empty($error)){
                return $error;
            }
        }
        return true;
    }
    
    /**
     * Atualza OC
     *
     * @param String $T075_Id
     * @return bool
     */     
    public function atualizaOC($T075_Id)
    {
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        $T055 = mysql_query("SELECT T055_D009_Id
                               FROM T055 
                              WHERE T055_T075_Id='{$T075_Id}'");
        
        while ($mT055 = mysql_fetch_array($T055)) {
            //atualiza a quantidade OC do produto
            mysql_query("update D009 set D009_Quantidade_OC=D009_Quantidade_OC_3('{$mT055['T055_D009_Id']}',0) where D009_Id = '{$mT055['T055_D009_Id']}'");
            //atualiza a quantidade OC dos pedidos
            $CAD002->reprocessaOCPedidos($mT055['T055_D009_Id']);
        }
        return true;
    }

    /**
     * alterarClientePreCadastro
     *
     * @param String $T075_Id
     * @return String/bool
     */     
    public function gerarNotaFiscal($T075_Id)
    {
        global $g, $T007_Id, $T007_Id_aux;
        
        $T007 = mysql_query("SELECT T007_Id,
                                    T007_Numero_Nota_Fiscal,
                                    T007_Data_Emissao 
                               FROM T007 
                              WHERE T007_T075_Id='{$T075_Id}' 
                                AND T007_Flag_Cancelada!='S'
                                AND T007_Flag_Inutilizado !='S'");
        
        if (mysql_num_rows($T007) > 0) {
            $cMsg_Faturado = "Esta importação já foi faturada: ";
            while ($mT007 = mysql_fetch_array($T007)) {
                $mT007['T007_Data_Emissao'] = gCorrigeData($mT007['T007_Data_Emissao']);
                $cMsg_Faturado .= "NF: " . $mT007['T007_Numero_Nota_Fiscal'] . " no dia " . $mT007['T007_Data_Emissao'] . ", ";
                $T007_Id_aux = $mT007['T007_Id'];
                
            }
            return $cMsg_Faturado;
        }

        $validaDadosNFe = $this->validaDadosNFe($T075_Id);
        if($validaDadosNFe !== true){
            return $validaDadosNFe;
        }

        $T075  = mysql_query("SELECT * 
                               FROM T075 
                          LEFT JOIN D006 ON D006_Id=T075_D006_Id
                          LEFT JOIN D118 ON D118_Id=T075_D118_Id
                          LEFT JOIN D018 ON D018_UF=D118_Estado
                              WHERE T075_Id='{$T075_Id}'");
        $mT075 = mysql_fetch_array($T075);

        $C004 = mysql_query("SELECT C004_Flag_Tipo_Preenchimento_Reforma_Tributaria
                               FROM C004
                              WHERE C004_Id = '{$mT075['T075_C004_Id']}'");
        $mC004 = mysql_fetch_array($C004);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Erro: " . $cErro;
        }

        $preenchimentoReformaTributariaAutomatico = ($mC004['C004_Flag_Tipo_Preenchimento_Reforma_Tributaria'] == '1');
        if ($preenchimentoReformaTributariaAutomatico) {
            require_once('bibliotecas/classes/ReformaTributaria.php');
            $reformaTributaria = new ReformaTributaria();
        }
        
        $D024  = mysql_query("SELECT * 
                               FROM D024 
                          LEFT JOIN D018 ON D018_Id=D024_D018_Id
                          LEFT JOIN D020 ON D020_Id=D024_D020_Id
                          LEFT JOIN D030 ON D030_Id=D024_D030_Id
                          LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id='{$mT075['T075_C004_Id']}'
                              WHERE D024_Id = '{$mT075['T075_D024_Id']}'");
        $mD024 = mysql_fetch_array($D024);
        
        if ($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
            $T007_Cliente_CNPJ_CPF = "{$mD024['D024_Cnpj']}";
        } elseif ($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'I') {
            $T007_Cliente_CNPJ_CPF = "";
        } else {
            $T007_Cliente_CNPJ_CPF = "{$mD024['D024_Cpf']}";
        } 
        
        $T055 = mysql_query("SELECT *,
                                  sum(T055_Valor_Total_Custo*$mT075[T075_Cotacao_ME]) T055_Valor_Total_Custo_Reais,
                                  sum(T055_Valor_Total_Custo) T055_Valor_Total_Custo,
                                  sum(T055_Valor_Aduaneira) T055_Valor_Aduaneira,
                                  sum(T055_Valor_Imposto_Importacao) T055_Valor_Imposto_Importacao,
                                  sum(T055_Valor_IPI) T055_Valor_IPI,
                                  sum(T055_Valor_PIS) T055_Valor_PIS,
                                  sum(T055_Valor_COFINS) T055_Valor_COFINS,
                                  sum(T055_Valor_ICMS) T055_Valor_ICMS,
                                  sum(T055_Valor_ICMS_Presumido) T055_Valor_ICMS_Presumido,
                                  sum(T055_Valor_ICMS_Pagar) T055_Valor_ICMS_Pagar,
                                  sum(T055_Valor_Base_ICMS_Sem_Reducao) T055_Valor_Base_ICMS_Sem_Reducao,
                                  sum(T055_Valor_Base_ICMS_Normal) T055_Valor_Base_ICMS_Normal,
                                  sum(T055_Peso_Kg_Liquido_Unitario) T055_Peso_Kg_Liquido_Unitario,
                                  sum(T055_Valor_Total_Despesas) T055_Valor_Total_Despesas,
                                  sum(T055_Valor_Total_Despesas_Aduaneira) T055_Valor_Despesas_Aduaneiras,
                                  sum(T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal) T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal,
                                  sum(T055_Valor_Total_Frete) T055_Valor_Frete,
                                  sum(T055_Valor_Total_Siscomex) T055_Valor_Siscomex,
                                  sum(T055_Valor_Total_THC) T055_Valor_THC,
                                  sum(T055_Valor_Total_AFRMM) T055_Valor_AFRMM,
                                  sum(T055_Valor_Total_Custo_Final) T055_Valor_Total_Custo_Final,
                                  sum(T055_Valor_Total_Preco_Nota_Fiscal) T055_Valor_Total_Preco_Nota_Fiscal,
                                  sum(T055_Valor_Total_Nota_Fiscal) T055_Valor_Total_Nota_Fiscal
                             FROM T055
                            WHERE T055_T075_Id='{$T075_Id}'");
        
        $mT055 = mysql_fetch_array($T055);
        $T055_T075 = array_merge($mT075, $mT055);
        
        if ($mT075['D006_Flag_Isento_ICMS']=='S'){
            $mT055['T055_Valor_Base_ICMS_Sem_Reducao']   = 0;
            $mT055['T055_Valor_Base_ICMS_Normal']   = 0;
        }

        $mICMS     = $this->retornaICMS($mT075['D118_Flag_Tipo']);
        $baseICMS  = $mICMS['baseICMS'];
        $valorICMS = $mICMS['valorICMS'];
        $valorTotalNota = $mT055['T055_Valor_Total_Nota_Fiscal'];

        // Valores que podem ser retornados e irão para a nota fiscal de acordo com a necessidade do cliente
        $mGatilhoT007     = $this->gatilhoAntesInsertT007($T055_T075);
        $T055_Valor_Frete = $mGatilhoT007['T055_Valor_Frete'];

        $mT055['T055_Valor_Total_Custo_Reais']           = number_format($mT055['T055_Valor_Total_Custo_Reais'], 2, ',', '.');
        $mT055['T055_Valor_Total_Custo']                 = number_format($mT055['T055_Valor_Total_Custo'], 2, ',', '.');
        $mT075['T075_Cotacao_ME']                        = number_format($mT075['T075_Cotacao_ME'], 4, ',', '.');
        $mT055['T055_Valor_Total_Frete']                 = number_format($mT055['T055_Valor_Total_Frete'], 2, ',', '.');
        $mT055['T055_Valor_Aduaneira']                   = number_format($mT055['T055_Valor_Aduaneira'], 2, ',', '.');
        $mT055['T055_Valor_Imposto_Importacao']          = number_format($mT055['T055_Valor_Imposto_Importacao'], 2, ',', '.');
        $mT055['T055_Valor_IPI']                         = number_format($mT055['T055_Valor_IPI'], 2, ',', '.');
        $mT055['T055_Valor_PIS']                         = number_format($mT055['T055_Valor_PIS'], 2, ',', '.');
        $mT055['T055_Valor_COFINS']                      = number_format($mT055['T055_Valor_COFINS'], 2, ',', '.');
        $mT055['T055_Valor_ICMS']                        = number_format($mT055['T055_Valor_ICMS'], 2, ',', '.');
        $mT055['T055_Valor_ICMS_Presumido']              = number_format($mT055['T055_Valor_ICMS_Presumido'], 2, ',', '.');
        $mT055['T055_Valor_ICMS_Pagar']                  = number_format($mT055['T055_Valor_ICMS_Pagar'], 2, ',', '.');
        $mT055['T055_Peso_Kg_Liquido_Unitario']          = number_format($mT055['T055_Peso_Kg_Liquido_Unitario'], 2, ',', '.');
        $mT055['T055_Valor_Total_Preco_Nota_Fiscal']     = number_format($mT055['T055_Valor_Total_Preco_Nota_Fiscal'], 2, ',', '.');
        $mT055['T055_Valor_Despesas_Aduaneiras']         = number_format($mT055['T055_Valor_Despesas_Aduaneiras'], 2, ',', '.');
        $mT055['T055_Valor_Despesas_Acessorias']         = number_format($mT055['T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal'], 2, ',', '.');
        $mT055['T055_Valor_Total_Despesas']              = number_format($mT055['T055_Valor_Total_Despesas'], 2, ',', '.');
        $mT055['T055_Valor_Total_Custo_Final']           = number_format($mT055['T055_Valor_Total_Custo_Final'], 2, ',', '.');
        $mT055['T055_Valor_Base_ICMS_Sem_Reducao']       = number_format($mT055['T055_Valor_Base_ICMS_Sem_Reducao'], 2, ',', '.');
        $mT075['T075_Aliquota_ICMS_Credito_Presumido']   = number_format($mT075['T075_Aliquota_ICMS_Credito_Presumido'], 2, ',', '.');
        $mT075['T075_Percentual_Reducao_ICMS_Presumido'] = number_format($mT075['T075_Percentual_Reducao_ICMS_Presumido'], 2, ',', '.');
        $mT055['T055_Valor_AFRMM']                       = number_format($mT055['T055_Valor_AFRMM'], 2, ',', '.');
        $mT055['T055_Valor_Frete']                       = number_format($mT055['T055_Valor_Frete'], 2, ',', '.');
        $mT055['T055_Valor_Siscomex']                    = number_format($mT055['T055_Valor_Siscomex'], 2, ',', '.');
        $mT055['T055_Valor_THC']                         = number_format($mT055['T055_Valor_THC'], 2, ',', '.');

        $observacaoFiscal = "II: {$mT055['T055_Valor_Imposto_Importacao']}, PIS: {$mT055['T055_Valor_PIS']}, Cofins: {$mT055['T055_Valor_COFINS']}, Siscomex: {$mT055['T055_Valor_Total_Despesas_Aduaneira']}, Frete: {$mT055['T055_Valor_Total_Frete']}, THC: {$mT055['T055_Valor_Aduaneira']}, AFRMM: {$mT055['T055_Valor_Total_AFRMM']}, Base ICMS: {$mT055[$baseICMS]}, Devido: {$mT055['T055_Valor_ICMS']}, Presumido: {$mT055['T055_Valor_ICMS_Presumido']}, Recolhido: {$mT055['T055_Valor_ICMS_Pagar']}. BASE DE CALCULO REDUZIDA EM {$mT075['T075_Percentual_Reducao_ICMS_Presumido']}% CREDITO PRESUMIDO DE {$mT075['T075_Aliquota_ICMS_Credito_Presumido']}% VALOR CREDITO PRESUMIDO R\$ {$mT055['T055_Valor_ICMS_Presumido']} ICMS RECOLHIDO R\$ {$mT055['T055_Valor_ICMS_Pagar']} DI: {$mT075['T075_Numero_DI']} PO: {$mT075['T075_Ordem_Compra']}";
        $observacaoFiscal='';
        if($mT075['D006_Observacao'] != ''){
            $observacaoFiscal .= $mT075['D006_Observacao'];
        }
        // Váriaveis dentro da Observação Fiscal
        foreach ($T055_T075 as $key => $value) {
            $key = preg_replace('#^[^_]+_#', '', $key);
            $key = strtoupper($key);
            if (preg_match('#^([0-9])+\.([0-9])+$#', $value)) {
                $value = gCorrigeNumero($value);
            }
            $observacaoFiscal = str_replace('[' . strtoupper($key) . ']', $value, $observacaoFiscal);
        }

/*         $sqlT007_Numero = mysql_query("SELECT T007_Id,T007_Numero_Nota_Fiscal
                                         FROM T007
                                        WHERE T007_Flag_ACP='6'
                                          AND T007_C004_Id='{$g['c004']['C004_Id']}'
                                          AND T007_Serie='1'
                                          AND T007_Flag_Emitida_Recebida!='R'
                                     ORDER BY round(T007_Numero_Nota_Fiscal) DESC LIMIT 0,1;");
        $cErro = mysql_error();
        if (!empty($cErro)) {
           return 'Erro: ' . $cErro;
        }

        $mT007_Numero = mysql_fetch_array($sqlT007_Numero);

        if(empty($mT007_Numero['T007_Numero_Nota_Fiscal'])){
                $T007_Numero_Nota_Fiscal = 1;
        }else{
                $T007_Numero_Nota_Fiscal = $mT007_Numero['T007_Numero_Nota_Fiscal'];
                $T007_Numero_Nota_Fiscal++;
        } */

        require_once("bibliotecas/classes/VEN012.php");
        $VEN012 = new VEN012();
        //retorna a proxima numeração e série da nota fiscal
        $proximoNumeroSerie = $VEN012->retornaProximaNF('6', $g['empresaAtual']);

        list($proximoNumero,$serie) = $proximoNumeroSerie;
        if($proximoNumero === false){
            $serie = str_replace(array('<b>','</b>'), '', $serie);
            return $serie;
        }

        //$nNumero_Serie            = '1';
        $T007_Flag_Finalidade_NFe = '1';
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $cClausula = " INSERT INTO T007 ( ";
        $cClausula .= " T007_D006_Id, "; // 1
        $cClausula .= " T007_D024_Id, "; // 2
        $cClausula .= " T007_D022_Id, "; // 3
        $cClausula .= " T007_D021_Id, "; // 4
        $cClausula .= " T007_C004_Id, "; // 5
        $cClausula .= " T007_D036_Id, "; // 6
        $cClausula .= " T007_T134_Id, "; // 7
        $cClausula .= " T007_Flag_Cancelada, "; // 8
        $cClausula .= " T007_Flag_ACP, "; // 9
        $cClausula .= " T007_Numero_Nota_Fiscal, "; // 10
        $cClausula .= " T007_Ambiente_NFe, "; // 11
        $cClausula .= " T007_Serie, "; // 12
        $cClausula .= " T007_Numero_Nota_Devolvida, "; // 13
        $cClausula .= " T007_Data_Emissao, "; // 14
        $cClausula .= " T007_Data_Saida, "; // 15
        $cClausula .= " T007_Nome_Transportadora, "; // 16
        $cClausula .= " T007_Tipo_Transporte, "; // 17
        $cClausula .= " T007_Observacao_Nota_Fiscal_2, "; // 18
        $cClausula .= " T007_Flag_Frete, "; // 19
        $cClausula .= " T007_Flag_IPI, "; // 20
        $cClausula .= " T007_Obs_Classificacao_Fiscal, "; // 21
        $cClausula .= " T007_Valor_Frete, "; // 22
        $cClausula .= " T007_Valor_Seguro, "; // 23
        $cClausula .= " T007_Percentual_Comissao_Representante, "; // 24
        $cClausula .= " T007_Valor_Despesas_Acessorias, "; // 25
        $cClausula .= " T007_Valor_Entrada, "; // 26
        $cClausula .= " T007_Valor_Desconto, "; // 27
        $cClausula .= " T007_C007_Id_Vendedor_Interno, "; // 28
        $cClausula .= " T007_C007_Id_Vendedor_Externo, "; // 29
        $cClausula .= " T007_C007_Id_Devolucao,  "; // 30
        $cClausula .= " T007_Cliente_Razao, "; // 31
        $cClausula .= " T007_Cliente_CNPJ_CPF, "; // 32
        $cClausula .= " T007_Cliente_Inscricao_Estadual, "; // 33
        $cClausula .= " T007_Cliente_Endereco, "; // 34
        $cClausula .= " T007_Cliente_Endereco_Numero, "; // 35
        $cClausula .= " T007_Cliente_Bairro, "; // 36
        $cClausula .= " T007_Cliente_CEP, "; // 37
        $cClausula .= " T007_Cliente_Cidade, "; // 38
        $cClausula .= " T007_Cliente_Codigo_Cidade_IBGE, "; // 39
        $cClausula .= " T007_Cliente_Telefone, "; // 40
        $cClausula .= " T007_Cliente_Estado, "; // 41
        $cClausula .= " T007_Cliente_Codigo_Estado_IBGE, "; // 42
        $cClausula .= " T007_Cliente_Pais, "; // 43
        $cClausula .= " T007_Cliente_Codigo_Pais, "; // 44
        $cClausula .= " T007_Hora_Saida, "; // 45
        $cClausula .= " T007_Transportadora_Placa_Veiculo,"; // 46
        $cClausula .= " T007_Estado_Veiculo, "; // 47
        $cClausula .= " T007_Transportadora_CNPJ, "; // 48
        $cClausula .= " T007_Transportadora_Inscricao_Estadual, "; // 49
        $cClausula .= " T007_Transportadora_Endereco, "; // 50
        $cClausula .= " T007_Transportadora_Cidade, "; // 51
        $cClausula .= " T007_Transportadora_Estado, "; // 52
        $cClausula .= " T007_Quantidade_Volumes, "; // 53
        $cClausula .= " T007_Especie, "; // 54
        $cClausula .= " T007_Marca, "; // 55
        $cClausula .= " T007_Numero_Caixas, "; // 56
        $cClausula .= " T007_Peso_Bruto, "; // 57
        $cClausula .= " T007_Peso_Liquido, "; // 58
        $cClausula .= " T007_Inscricao_Municipal, "; // 59
        $cClausula .= " T007_Aliquota_ISS, "; // 60
        $cClausula .= " T007_Empresa_Usuaria_Nome, "; // 61
        $cClausula .= " T007_Empresa_Usuaria_Endereco, "; // 62
        $cClausula .= " T007_Empresa_Usuaria_Numero, "; // 63
        $cClausula .= " T007_Empresa_Usuaria_Bairro, "; // 64
        $cClausula .= " T007_Empresa_Usuaria_CEP, "; // 65
        $cClausula .= " T007_Empresa_Usuaria_Cidade, "; // 66
        $cClausula .= " T007_Empresa_Usuaria_Estado, "; // 67
        $cClausula .= " T007_Empresa_Usuaria_Uf, "; // 68
        $cClausula .= " T007_Empresa_Usuaria_Codigo_Cidade_IBGE, "; // 69
        $cClausula .= " T007_Empresa_Usuaria_Codigo_Estado_IBGE, "; // 70
        $cClausula .= " T007_Empresa_Usuaria_Pais, "; // 71
        $cClausula .= " T007_Empresa_Usuaria_Telefone, "; // 72
        $cClausula .= " T007_Empresa_Usuaria_Fax, "; // 73
        $cClausula .= " T007_Empresa_Usuaria_CNPJ, "; // 74
        $cClausula .= " T007_Empresa_Usuaria_Inscricao_Estadual, "; // 75
        $cClausula .= " T007_Empresa_Usuaria_CNAE_Fiscal, "; // 76
        $cClausula .= " T007_Flag_Impresso, "; // 77
        $cClausula .= " T007_Percentual_Desconto, "; // 78
        $cClausula .= " T007_Valor_Percentual_Desconto, "; // 79
        $cClausula .= " T007_Valor_Total_Base_ICMS, "; // 80
        $cClausula .= " T007_Valor_Total_ICMS, "; // 81
        $cClausula .= " T007_Valor_Total_Base_IPI, "; // 82
        $cClausula .= " T007_Valor_Total_IPI, "; // 83
        $cClausula .= " T007_Valor_ISS, "; // 84
        $cClausula .= " T007_Valor_Total_Custo, "; // 85
        $cClausula .= " T007_Valor_Total_Produtos, "; // 86
        $cClausula .= " T007_Valor_Total, "; // 87
        $cClausula .= " T007_IPV, "; // 88
        $cClausula .= " T007_IPV_Cliente, "; // 89
        $cClausula .= " T007_Flag_Entrada_Saida, "; // 90
        $cClausula .= " T007_Flag_Finalidade_NFe, "; // 91
        $cClausula .= " T007_Origem_Chave_Acesso, "; // 92
        $cClausula .= " T007_Flag_Venda_Compra_Outros, "; // 93
        $cClausula .= " T007_Flag_Expedicao, "; // 94
        $cClausula .= " T007_Numero_Parcelas, "; // 95
        $cClausula .= " T007_Numero_Dias_Entrada, "; // 96
        $cClausula .= " T007_Numero_Dias, "; // 97
        $cClausula .= " T007_Flag_Revenda_Consumidor, "; // 98
        $cClausula .= " T007_Casas_Decimais_Quantidade, "; // 99
        $cClausula .= " T007_Casas_Decimais_Preco, "; // 100
        $cClausula .= " T007_Descricao_CFOP, "; // 101
        $cClausula .= " T007_Codigo_CFOP, "; // 102
        $cClausula .= " T007_Exportacao_UF_Embarque, "; // 103
        $cClausula .= " T007_Exportacao_Local_Embarque, "; // 104
        $cClausula .= " T007_Empresa_Usuaria_CRT, "; // 105
        $cClausula .= " T007_Versao_NFe, "; // 106
        $cClausula .= " T007_Observacao_Nota_Fiscal_1, "; // 107
        $cClausula .= " T007_T075_Id, "; // 108
        $cClausula .= " T007_Flag_Destino_Operacao, "; // 109
        $cClausula .= " T007_Hora_Emissao, "; // 110
        $cClausula .= " T007_Fuso_Horario_Emissao, "; // 111
        $cClausula .= " T007_Fuso_Horario_Saida, "; // 112
        $cClausula .= " T007_Flag_Indicador_Presenca "; // 113
        $cClausula .= " ) VALUES ("; // 109
        $cClausula .= " '{$mT075['T075_D006_Id']}', "; // CFOP // 1
        $cClausula .= " '{$mT075['T075_D024_Id']}', "; // Fornecedor //2
        $cClausula .= " '', "; // Transportadora // 3
        $cClausula .= " '', "; // Representante //4
        $cClausula .= " {$g['c004']['C004_Id']}, "; // Empresa_Atual //5
        $cClausula .= " '', "; // D036_Id - Tipo Transporte // 6
        $cClausula .= " '', "; // T134_Id - Faixa simples //7
        $cClausula .= " 'N', "; // Flag_Cancelada //8
        $cClausula .= " '6', "; // Flag ACP //9
        $cClausula .= " '{$proximoNumero}', "; // NUMERO DA NOTA FISCAL //10
        $cClausula .= " {$g['c004']['C004_Ambiente_NFe']}, "; // Ambiente NFe //11
        $cClausula .= " '{$serie}', "; // Numero serie NFe //12
        $cClausula .= " '', "; //-- NUMERO DA NOTA FISCAL DE DEVOLUCAO //13
        $cClausula .= " curdate(), "; // DATA DE EMISSAO //14
        $cClausula .= " curdate(), "; // Data Saida //15
        $cClausula .= " '', "; // Nome Transportadora //16
        $cClausula .= " '', "; // D036_Tipo_Transporte - Tipo Transporte //17
        $cClausula .= " '', "; // OBSERVACAO DO CLIENTE QUE SAI NO CORPO DA NOTA FISCAL //18
        $cClausula .= " '', "; // Flag Frete //19
        $cClausula .= " '', "; // Flag IPI //20
        $cClausula .= " '', "; // Observacao Fiscal //21
        $cClausula .= " '{$T055_Valor_Frete}', "; // Valor Frete //22
        $cClausula .= " '', "; // Valor Seguro //23
        $cClausula .= " '', "; // Percentual Comissao //24 
        $cClausula .= " '{$mT055['T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal']}', "; // Valor Despesas Acessorias //25
        $cClausula .= " '', "; // Valor Entrada //26
        $cClausula .= " '', "; // VALOR DO DESCONTO //27
        $cClausula .= " '', "; // ID DO USUARIO VENDEDOR INTERNO //28
        $cClausula .= " '0', "; // ID DO USUARIO VENDEDOR EXTERNO //29
        $cClausula .= " '0', "; // ID DO USUARIO QUE FEZ A  DEVOLUCAO //30
        $cClausula .= " '{$mD024['D024_Nome_Empresa']}', "; // D024_Nome_Empresa //31
        $cClausula .= " '{$T007_Cliente_CNPJ_CPF}', "; // CNPJ ou CPF //32
        $cClausula .= " '{$mD024['D024_Inscricao_Estadual']}', "; //33
        $cClausula .= " '{$mD024['D024_Endereco']}', "; //34
        $cClausula .= " '{$mD024['D024_Numero']}', "; //35
        $cClausula .= " '{$mD024['D024_Bairro']}', "; //36
        $cClausula .= " '{$mD024['D024_Cep']}', "; //37
        $cClausula .= " '{$mD024['D020_Nome_Cidade']}', "; //38
        $cClausula .= " '{$mD024['D020_Codigo_IBGE']}', "; //39
        $cClausula .= " '{$mD024['D024_DDD_Telefone_1']}{$mD024['D024_Telefone_1']}', "; //40
        $cClausula .= " '{$mD024['D018_UF']}', "; // 41
        $cClausula .= " '{$mD024['D018_Codigo_IBGE']}', "; //42
        $cClausula .= " '{$mD024['D030_Nome_Pais']}', "; // 43
        $cClausula .= " '{$mD024['D030_Codigo_Pais']}', "; //44
        $cClausula .= " '', "; // Hora Saida //45
        $cClausula .= " '', "; // Placa do veiculo de transpote //46
        $cClausula .= " '', "; // UF do veiculo de transpote //47
        $cClausula .= " '', "; // D022_CNPJ //48
        $cClausula .= " '', "; // D022_Inscricao_Estadual //49
        $cClausula .= " '', "; // D022_Endereco // 50
        $cClausula .= " '', "; // D022 D020_Nome_Cidade //51
        $cClausula .= " '', "; // D022 D018_UF //52
        $cClausula .= " '', "; // QUANTIDADE DE VOLUMES  //53
        $cClausula .= " '', "; // Especie Volumes //54
        $cClausula .= " '', "; // Marca //55
        $cClausula .= " '', "; // Numero Caixas //56
        $cClausula .= " '{$mT075['T075_Peso_Bruto']}', "; // Peso Bruto //57
        $cClausula .= " '{$mT075['T075_Peso_Liquido']}', "; // Peso Liquido //58
        $cClausula .= " '', "; //59
        $cClausula .= " '0', "; // Aliquota ISS //60
        $cClausula .= " '{$g['C004']['C004_Nome_Empresa']}', "; //61
        $cClausula .= " '{$g['C004']['C004_Logradouro']}', "; //62
        $cClausula .= " '{$g['C004']['C004_Numero']}', "; //63
        $cClausula .= " '{$g['C004']['C004_Bairro']}', "; //64
        $cClausula .= " '{$g['C004']['C004_Cep']}', "; // 65 
        $cClausula .= " '{$g['C004']['D020_Nome_Cidade']}', "; //66
        $cClausula .= " '{$g['C004']['D018_Estado']}', "; //67
        $cClausula .= " '{$g['C004']['D018_UF']}', "; //68
        $cClausula .= " '{$g['C004']['D020_Codigo_IBGE']}', "; //69
        $cClausula .= " '{$g['C004']['D018_Codigo_IBGE']}', "; //70
        $cClausula .= " 'BR', "; //71
        $cClausula .= " '{$g['C004']['C004_Telefone']}', "; //72
        $cClausula .= " '{$g['C004']['C004_Fax']}', "; //73
        $cClausula .= " '{$g['C004']['C004_Cnpj']}', "; //74
        $cClausula .= " '{$g['C004']['C004_Inscricao_Estadual']}', "; //75
        $cClausula .= " '{$g['C004']['C004_CNAE_Fiscal']}', "; //76 
        $cClausula .= " 'N', "; //77
        $cClausula .= " '0', "; // Percentual Desconto //78
        $cClausula .= " '0', "; // Valor Percentual Desconto //79
        $cClausula .= " '', "; // Valor Base ICMS //80
        $cClausula .= " '', "; // Valor ICMS //81
        $cClausula .= " '', "; // Valor Base IPI //82
        $cClausula .= " '', "; // Valor IPI //83
        $cClausula .= " '0', "; // Valor ISS //84
        $cClausula .= " '0', "; // Valor Total Custo //85 
        $cClausula .= " '', "; // Valor Total Produtos //86
        $cClausula .= " '', "; // Valor Total //87 
        $cClausula .= " '0', "; // IPV //88
        $cClausula .= " '0', "; // IPV Cliente //89
        $cClausula .= " 'E', "; // Flag Entrada Saida //90
        $cClausula .= " '$T007_Flag_Finalidade_NFe', "; // Finalidade NFE //91
        $cClausula .= " '', "; // Chave Acesso da Nota Geradora //92
        $cClausula .= " '{$mT075['D006_Flag_Venda_Compra_Outros']}', "; // Flag Venda / Compras / Outros //93
        $cClausula .= " '1', "; // Flag Expedicao // 94
        $cClausula .= " '', "; // Numero de Parcelas // 95
        $cClausula .= " '', "; // Numero dias na entrada (Prazo) //96
        $cClausula .= " '', "; // Numero Dias //97
        $cClausula .= " '', "; // Flag Revenda Consumidor //98
        $cClausula .= " '', "; // Casas decimais quantidade //99
        $cClausula .= " '7', "; // Casas decimais preco //100
        $cClausula .= " '', "; // Descricao CFOP //101
        $cClausula .= " '', "; // Codigo CFOP //102
        $cClausula .= " '', "; // Exportacao UF Embarque //103                     
        $cClausula .= " '', "; // Exportacao Local Embarque  //104
        $cClausula .= " {$g['C004']['C004_Codigo_Regime_Tributario']}, "; //105
        //$cClausula .= " '{$g['C020']['C020_Versao_NFe']}', "; // Versao NFe   // 106
        $cClausula .= " '{$g['c004']['C004_Versao_NFe']}', "; // Versao NFe   // 106
        $cClausula .= " '{$observacaoFiscal}', "; // Observacao NFE //107
        $cClausula .= " '{$T075_Id}', "; // Id DI //108
        $cClausula .= " '3', "; // Flag_Destino_Operacao //109
        $cClausula .= " current_time(), "; // Hora Emissao //110
        $cClausula .= "'".date('P')."', "; // Fuso horario emissao // 111
        $cClausula .= "'".date('P')."', "; // Fuso horario saida // 112
        $cClausula .= " '1' "; // T007_Flag_Indicador_Presenca //113
        $cClausula .= " )";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cErro . "<br />" . $cClausula;
        }

        $T007_Id = $g['mysqlLastId'];

        $T007A_Flag_Calcular_Reforma_Tributaria_Nota_Devolucao = ($preenchimentoReformaTributariaAutomatico) ? 'S' : 'N';
        mysqli_query("INSERT INTO T007A (T007A_T007_Id, T007A_Flag_Calcular_Reforma_Tributaria_Nota_Devolucao) VALUES ('{$T007_Id}', '{$T007A_Flag_Calcular_Reforma_Tributaria_Nota_Devolucao}')");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cErro;
        }

        /**
         * Verificar se todos itens tem locação
         */
        $T055 = mysql_query("SELECT T066_D004_Id,
                                    T066_Id,
                                    D001_Codigo_Produto,
                                    D001A_Flag_Validade,
                                    T055A_Data_Validade,
                                    T055_T066_Id,
                                    T066A_Data_Validade,
                                    T066_Quantidade_Estoque,
                                    T055A_Id
                               FROM T055
                          LEFT JOIN D009 ON D009_Id=T055_D009_Id
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id
                          LEFT JOIN D001A ON D001_Id=D001A_D001_Id
                          LEFT JOIN T055A ON T055_Id=T055A_T055_Id
                          LEFT JOIN T066 ON T055_T066_Id=T066_Id
                          LEFT JOIN T066A ON T066_Id = T066A_T066_Id
                              WHERE T055_T075_Id='{$T075_Id}'
                           GROUP BY T055_Id");

        $mensagemProduto      = '';
        $cfopMovimentaEstoque = $mT075['D006_Flag_Estoque'];

        while ($mT055 = mysql_fetch_array($T055)) {

            $erroProdutos = '';

            if (($mT055['T066_Id'] <=0 || $mT055['T066_D004_Id'] <=0) && $cfopMovimentaEstoque == 'C'){
                $erroProdutos .= "- Está sem locação preenchida e CFOP está para creditar estoque.\n";
            }
            if($mT055['D001A_Flag_Validade'] == 'S' && ($mT055['T055A_Data_Validade'] == '0000-00-00' || empty($mT055['T055A_Data_Validade']))){
                $erroProdutos .= "- Está sem data de validade e produto tem controle de validade.\n";
            }

            if($mT055['T066_Id'] > 0 && $mT055['T066_Quantidade_Estoque'] > 0 && ($mT055['T055A_Data_Validade'] != '0000-00-00' || empty($mT055['T055A_Data_Validade'])) && $mT055['T066A_Data_Validade'] != $mT055['T055A_Data_Validade'] && $cfopMovimentaEstoque == 'C' && $mT055['T055A_Id'] > 0){
                $erroProdutos .= "- A locação selecionada possui estoque e data de validade diferente do produto da DI, verificar ou cadastrar nova locação.\n";
            }

            if(!empty($erroProdutos)){
                $mensagemProduto .= "CÓDIGO ".$mT055['D001_Codigo_Produto'].":\n".$erroProdutos."\n";
            }            
        }

        if (!empty($mensagemProduto)){                
            mysql_query("ROLLBACK");
            return $mensagemProduto;
        }
                
        $T055 = mysql_query("SELECT * 
                               FROM T055 
                          LEFT JOIN D005 ON D005_Id=T055_D005_Id
                          LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id='{$g['empresaAtual']}'
                          LEFT JOIN D088 ON D088_Id=D149_D088_Id
                          LEFT JOIN D037 ON D037_Id=T055_D037_Id
                          LEFT JOIN T055A ON T055_Id=T055A_T055_Id                        
                              WHERE T055_T075_Id='{$T075_Id}'");
        while ($mT055 = mysql_fetch_array($T055)) {
            $T008A_Situacao_Tributaria_CBS_IBS = '';
            $T008A_Classificacao_Tributaria_CBS_IBS = '';
            $T008A_Aliquota_CBS = 0;
            $T008A_Aliquota_IBS_Municipal = 0;
            $T008A_Aliquota_IBS_UF = 0;
            $T008A_Percentual_Reducao_CBS = 0;
            $T008A_Percentual_Reducao_IBS_Municipal = 0;
            $T008A_Percentual_Reducao_IBS_UF = 0;
            $T008A_Percentual_Diferimento_CBS = 0;
            $T008A_Percentual_Diferimento_IBS_UF = 0;
            $T008A_Percentual_Diferimento_IBS_Municipal = 0;
            $T008A_Situacao_Tributaria_CBS_IBS_Regular = '';
            $T008A_Classificacao_Tributaria_CBS_IBS_Regular = '';
            $T008A_Aliquota_CBS_Regular = 0;
            $T008A_Aliquota_IBS_Municipal_Regular = 0;
            $T008A_Aliquota_IBS_UF_Regular = 0;
            $T008A_Percentual_Reducao_CBS_Regular = 0;
            $T008A_Percentual_Reducao_IBS_Municipal_Regular = 0;
            $T008A_Percentual_Reducao_IBS_UF_Regular = 0;

            if ($preenchimentoReformaTributariaAutomatico) {
                if (!empty($mT075['D006_Situacao_Tributaria_CBS_IBS']) && !empty($mT075['D006_Classificacao_Tributaria_CBS_IBS'])) {
                    $T008A_Situacao_Tributaria_CBS_IBS = $mT075['D006_Situacao_Tributaria_CBS_IBS'];
                    $T008A_Classificacao_Tributaria_CBS_IBS = $mT075['D006_Classificacao_Tributaria_CBS_IBS'];
                    $T008A_Situacao_Tributaria_CBS_IBS_Regular = $mT075['D006_Situacao_Tributaria_CBS_IBS_Regular'];
                    $T008A_Classificacao_Tributaria_CBS_IBS_Regular = $mT075['D006_Classificacao_Tributaria_CBS_IBS_Regular'];
                } else {
                    $T008A_Situacao_Tributaria_CBS_IBS = $mT055['D005_Situacao_Tributaria_CBS_IBS'];
                    $T008A_Classificacao_Tributaria_CBS_IBS = $mT055['D005_Classificacao_Tributaria_CBS_IBS'];
                    $T008A_Situacao_Tributaria_CBS_IBS_Regular = $mT055['D005_Situacao_Tributaria_CBS_IBS_Regular'];
                    $T008A_Classificacao_Tributaria_CBS_IBS_Regular = $mT055['D005_Classificacao_Tributaria_CBS_IBS_Regular'];
                }

                $aliquotasReformaTributaria = $reformaTributaria->getAliquotas($T008A_Classificacao_Tributaria_CBS_IBS ?? '');
                $aliquotasReformaTributariaRegular = $reformaTributaria->getAliquotas($T008A_Classificacao_Tributaria_CBS_IBS_Regular ?? '');

                $T008A_Aliquota_CBS = $aliquotasReformaTributaria['aliquotaCbs'] ?? 0;
                $T008A_Aliquota_IBS_Municipal = $aliquotasReformaTributaria['aliquotaIbsMunicipal'] ?? 0;
                $T008A_Aliquota_IBS_UF = $aliquotasReformaTributaria['aliquotaIbsUf'] ?? 0;
                $T008A_Percentual_Reducao_CBS = $aliquotasReformaTributaria['percentualReducaoCbs'] ?? 0;
                $T008A_Percentual_Reducao_IBS_Municipal = $aliquotasReformaTributaria['percentualReducaoIbsMunicipal'] ?? 0;
                $T008A_Percentual_Reducao_IBS_UF = $aliquotasReformaTributaria['percentualReducaoIbsUf'] ?? 0;
                $T008A_Percentual_Diferimento_CBS = $aliquotasReformaTributaria['percentualDiferimentoCbs'] ?? 0;
                $T008A_Percentual_Diferimento_IBS_UF = $aliquotasReformaTributaria['percentualDiferimentoIbsUf'] ?? 0;
                $T008A_Percentual_Diferimento_IBS_Municipal = $aliquotasReformaTributaria['percentualDiferimentoIbsMunicipal'] ?? 0;
                $T008A_Aliquota_CBS_Regular = $aliquotasReformaTributariaRegular['aliquotaCbs'] ?? 0;
                $T008A_Aliquota_IBS_Municipal_Regular = $aliquotasReformaTributariaRegular['aliquotaIbsMunicipal'] ?? 0;
                $T008A_Aliquota_IBS_UF_Regular = $aliquotasReformaTributariaRegular['aliquotaIbsUf'] ?? 0;
                $T008A_Percentual_Reducao_CBS_Regular = $aliquotasReformaTributariaRegular['percentualReducaoCbs'] ?? 0;
                $T008A_Percentual_Reducao_IBS_Municipal_Regular = $aliquotasReformaTributariaRegular['percentualReducaoIbsMunicipal'] ?? 0;
                $T008A_Percentual_Reducao_IBS_UF_Regular = $aliquotasReformaTributariaRegular['percentualReducaoIbsUf'] ?? 0;
            }
            
            if($mT075['T075_Aliquota_ICMS'] > 0 && $mT055['T055_Aliquota_ICMS'] <= 0){
                // Se estiver preenchido aliquota no cabeçalho e zerado no produto, usa do cabeçalho
                $mT055['T055_Aliquota_ICMS'] = $mT075['T075_Aliquota_ICMS'];
            }

            if($mT075['T075_Percentual_Reducao_ICMS'] > 0 && $mT055['T055_Percentual_Reducao_ICMS'] <= 0){
                // Se estiver preenchido reducao no cabeçalho e zerado no produto, usa do cabeçalho
                $mT055['T055_Percentual_Reducao_ICMS'] = $mT075['T075_Percentual_Reducao_ICMS'];
            }


            if ($mT075['D006_Flag_Isento_ICMS']=='S'){
                $mT055['T055_Valor_Base_ICMS_Sem_Reducao']   = 0;
                $mT055['T055_Valor_Base_ICMS_Normal']   = 0;
            }

            $mT055['T055_Valor_Custo_Unitario'] = $mT055['T055_Valor_Custo_Unitario'] * $mT075['T075_Cotacao_ME'];
            
            if ($mT055['T055_Aliquota_IPI'] > 0) {
                $mT055['D088_Codigo_ST'] = '00';
            } else {
                $mT055['D088_Codigo_ST'] = '03';
            }
            $mT055['T055_Descricao_Produto'] = mysql_real_escape_string($mT055['T055_Descricao_Produto']);

            // retorna FCP 
            if($mT075['D006_Percentual_FCP'] > 0){
                $mD024['D053_Percentual_FCP'] = $mT075['D006_Percentual_FCP'];
            }
            $mFCP        = $this->retornaFCP($mD024['D053_Percentual_FCP'], $mT075['D006_Flag_Calcula_FCP'], $mT055[$baseICMS]);
            $aliquotaFCP = $mFCP['aliquotaFCP'];
            $baseFCP     = $mFCP['baseFCP'];
            $valorFCP    = $mFCP['valorFCP'];

            if($mT075['D006_Flag_Calcula_FCP'] == 'S' && $aliquotaFCP > 0){
                // Se calcula FCP precisa descontar a aliquota e valor FCP do valor da aliquota e valor ICMS, na nota fiscal é enviado em campos separados
                $mT055['T055_Aliquota_ICMS'] -= $aliquotaFCP;
                $mT055[$valorICMS]           -= $valorFCP;
            }
        

            $T066 = mysql_query("select T055_T066_Id, T066_D004_Id from T055 lef join T066 on T066_Id=T055_T066_Id where T055_Id='{$mT055['T055_Id']}'");
            $mT066=mysql_fetch_array($T066);
            if (mysql_num_rows($T066)>1){
                $mT066[T066_Id]=0;
                $mT066[T066_D004_Id]=0;
            }

            // Valores que podem ser retornados e irão para a nota fiscal de acordo com a necessidade do cliente
            $mGatilhoT008 = $this->gatilhoAntesInsertT008($mT055);
            $T055_Valor_Total_Frete              = $mGatilhoT008['T055_Valor_Total_Frete']; 
            $T055_Valor_Total_Despesas_Aduaneira = $mGatilhoT008['T055_Valor_Total_Despesas_Aduaneira']; 

            $cClausula  = "INSERT INTO T008 (";
            $cClausula .= "T008_C004_Id,"; // 1
            $cClausula .= "T008_T007_Id,"; // 2
            $cClausula .= "T008_D006_Id,"; // 3
            $cClausula .= "T008_D037_Id,"; // 4
            $cClausula .= "T008_D059_Id,"; // 5
            $cClausula .= "T008_Unidade,"; // 6
            $cClausula .= "T008_Codigo_CFOP,"; // 7
            $cClausula .= "T008_Aliquota_IPI,"; // 8
            $cClausula .= "T008_Percentual_Reducao_IPI,"; // 9
            $cClausula .= "T008_Aliquota_ICMS,"; // 10
            $cClausula .= "T008_Percentual_Reducao_ICMS,"; // 11
            $cClausula .= "T008_Peso_Unitario,"; // 12
            $cClausula .= "T008_D005_Id,"; // 13
            $cClausula .= "T008_Flag_Situacao_Tributaria,"; // 14
            $cClausula .= "T008_Flag_Preco_Especial,"; // 15
            $cClausula .= "T008_Classificacao_Fiscal,"; // 16
            $cClausula .= "T008_Situacao_Tributaria_IPI, "; // 17
            $cClausula .= "T008_Quantidade,"; // 18
            $cClausula .= "T008_Codigo_Produto,"; // 19
            $cClausula .= "T008_Codigo_Barras,"; // 20
            $cClausula .= "T008_Codigo_Substituto,"; // 21
            $cClausula .= "T008_D009_Id,"; // 22
            $cClausula .= "T008_Descricao_Produto,"; // 23
            $cClausula .= "T008_Valor_Custo_Unitario,"; // 24
            $cClausula .= "T008_Valor_Preco_Sem_Desconto_Unitario,"; // 25
            $cClausula .= "T008_Flag_ST,"; // 26
            $cClausula .= "T008_ST_VA,"; // 27
            $cClausula .= "T008_ICMS_Inter_Estadual,"; // 28
            $cClausula .= "T008_ST_ICMS_Interno,"; // 29
            $cClausula .= "T008_Flag_Isento_PIS,"; // 30
            $cClausula .= "T008_Aliquota_PIS,"; // 31
            $cClausula .= "T008_Situacao_Tributaria_PIS,"; // 32
            $cClausula .= "T008_Flag_Isento_COFINS,"; // 33
            $cClausula .= "T008_Aliquota_COFINS,"; // 34
            $cClausula .= "T008_Situacao_Tributaria_COFINS, "; // 35
            $cClausula .= "T008_Valor_Total_Base_Imposto_Importacao, "; // 36
            $cClausula .= "T008_Valor_Total_Imposto_Importacao, "; // 37
            $cClausula .= "T008_Valor_Total_Despesas_Aduaneira, "; // 38
            $cClausula .= "T008_Valor_Total_IOF, "; // 39
            $cClausula .= "T008_Numero_Documento_Importacao, "; // 40
            $cClausula .= "T008_Data_Registro_Documento_Importacao, "; // 41
            $cClausula .= "T008_Local_Desembaraco, "; // 42
            $cClausula .= "T008_Data_Desembaraco, "; // 43
            $cClausula .= "T008_UF_Desembaraco, "; // 44
            $cClausula .= "T008_Codigo_Exportador, "; // 45
            $cClausula .= "T008_Numero_Adicao, "; // 46
            $cClausula .= "T008_Numero_Sequencia_Adicao, "; // 47
            $cClausula .= "T008_Codigo_Fabricante, "; // 48
            $cClausula .= "T008_Valor_Desconto_Item_Adicao, "; // 49
            $cClausula .= "T008_Numero_Pedido_Compra, "; // 50
            $cClausula .= "T008_Item_Pedido_Compra, "; // 51
            $cClausula .= "T008_Percentual_Base_Operacao_Propria, "; // 52
            $cClausula .= "T008_Aliquota_ICMS_Credito, "; // 53
            $cClausula .= "T008_Valor_ICMS_Credito, "; // 54
            $cClausula .= "T008_Valor_ICMS_Substituicao_Destino, "; // 55
            $cClausula .= "T008_Valor_Base_ICMS_Substituicao_Destino, "; // 56
            $cClausula .= "T008_Valor_ICMS_Substituicao_Retido, "; // 57
            $cClausula .= "T008_Valor_Base_ICMS_Substituicao_Retido, "; // 58
            $cClausula .= "T008_Percentual_Reducao_ICMS_ST, "; // 59
            $cClausula .= "T008_Modalidade_ICMS, "; // 60
            $cClausula .= "T008_Modalidade_ICMS_Substituicao, "; // 61
            $cClausula .= "T008_Flag_Calculo_Manual, "; // 62
            $cClausula .= "T008_UF_ICMS_Substituicao_Devido, "; // 63
            $cClausula .= "T008_Valor_Base_ICMS_Impresso, "; // 64
            $cClausula .= "T008_Valor_ICMS, "; // 65
            $cClausula .= "T008_Valor_Base_IPI, "; // 66
            $cClausula .= "T008_Valor_IPI, "; // 67
            $cClausula .= "T008_Valor_Base_PIS, "; // 68
            $cClausula .= "T008_Valor_PIS, "; // 69
            $cClausula .= "T008_Valor_Base_COFINS, "; // 70
            $cClausula .= "T008_Valor_COFINS, "; // 71
            $cClausula .= "T008_Valor_Base_ICMS, "; // 72
            $cClausula .= "T008_Flag_Transporte_DI, "; // 73
            $cClausula .= "T008_Flag_Tipo_Intermedio_DI, "; // 74
            $cClausula .= "T008_CNPJ_Importador, "; // 75
            $cClausula .= "T008_UF_Importador, "; // 76
            $cClausula .= "T008_Numero_Drawback_Importacao, "; // 77
            $cClausula .= "T008_Valor_Preco_Original, "; // 78
            $cClausula .= "T008_T066_Id, "; // 79
            $cClausula .= "T008_D004_Id, "; // 80
            $cClausula .= "T008_T055_Id, "; // 81
            $cClausula .= "T008_Valor_Total_Frete, "; // 82
            $cClausula .= "T008_Percentual_FCP, "; // 83
            $cClausula .= "T008_Valor_FCP, "; // 84
            $cClausula .= "T008_Valor_Base_FCP "; // 85
            $cClausula .= ") VALUES (";
            $cClausula .= "'{$g['c004']['C004_Id']}',"; // Empresa Atual  // 1
            $cClausula .= "'{$T007_Id}',"; // T007_Id // 2
            $cClausula .= "'{$mT075['T075_D006_Id']}',"; // D006_Id // 3
            $cClausula .= "'{$mT055['T055_D037_Id']}',"; // D037_Id // 4
            $cClausula .= "'{$mT075['D006_D059_Id']}',"; // D059_Id // 5
            $cClausula .= "'{$mT055['D037_Unidade']}',"; // D037_Id // 6
            $cClausula .= "'{$mT075['D006_Codigo_CFOP']}',"; // D006_Codigo_CFOP // 7
            $cClausula .= "'{$mT055['T055_Aliquota_IPI']}',"; // Aliquota IPI // 8
            $cClausula .= "'0',"; // Percentual Reducao IPI // 9
            $cClausula .= "'{$mT055['T055_Aliquota_ICMS']}',"; // Aliquota ICMS // 10
            $cClausula .= "'{$mT055['T055_Percentual_Reducao_ICMS']}',"; // Percentual Reducao ICMs // 11
            $cClausula .= "'{$mT055['T055_Peso_Kg_Liquido_Unitario']}',"; // Peso Unitario // 12
            $cClausula .= "'{$mT055['T055_D005_Id']}',"; // D005_Id // 13
            $cClausula .= "'100',"; // Flag situacao tributaria ICMS // 14
            $cClausula .= "'',"; // Flag Preco Especial // 15
            $cClausula .= "'{$mT055['D005_Classificacao_Fiscal']}',"; // NCM // 16
            $cClausula .= "'{$mT055['D088_Codigo_ST']}', "; // Situacao tributaria IPI // 17                                               
            $cClausula .= "'{$mT055['T055_Quantidade']}',"; // Quantidade // 18
            $cClausula .= "'{$mT055['T055_Codigo_Produto']}',"; // Codigo Produto // 19
            $cClausula .= "'',"; // Codigo Barras // 20
            $cClausula .= "'',"; // Codigo Substituto // 21
            $cClausula .= "'{$mT055['T055_D009_Id']}',"; // D009_Id // 22
            $cClausula .= "'{$mT055['T055_Descricao_Produto']}',"; // Descricao Produto // 23
            $cClausula .= "'{$mT055['T055_Valor_Custo_Final_Unitario']}',"; // Valor Custo Unitario // 24
            $cClausula .= "'{$mT055['T055_Valor_Preco_Unitario_Nota_Fiscal']}',"; // Valor Preco sem desconto unitario // 25
            $cClausula .= "'N',"; // Flag ST // 26
            $cClausula .= "'N',"; // ST VA // 27
            $cClausula .= "'0',"; // ICMS Inter Estadual // 28
            $cClausula .= "'0',"; // ST ICMS Interno // 29
            $cClausula .= "'{$mT075['D006_Flag_Isento_PIS']}',"; // Flag Isento PIS // 30
            $cClausula .= "'{$mT055['T055_Aliquota_PIS']}',"; // Aliquota PIS // 31
            $cClausula .= "'{$mT075['D006_Situacao_Tributaria_PIS']}',"; // Situacao Tributaria PIS // 32
            $cClausula .= "'{$mT075['D006_Flag_Isento_COFINS']}',"; // Flag Isento COFINS // 33
            $cClausula .= "'{$mT055['T055_Aliquota_COFINS']}',"; // Aliquota COFINS // 34
            $cClausula .= "'{$mT075['D006_Situacao_Tributaria_COFINS']}', "; // Situacao Tributaria COFINS // 35
            $cClausula .= "'{$mT055['T055_Valor_Base_Imposto_Importacao']}', "; // Base II // 36
            $cClausula .= "'{$mT055['T055_Valor_Imposto_Importacao']}', "; // Valor II // 37
            $cClausula .= "'{$T055_Valor_Total_Despesas_Aduaneira}', "; // Despesas Aduaneira // 38
            $cClausula .= "'', "; // Valor IOF // 39
            $cClausula .= "'{$mT075['T075_Numero_DI']}', "; // Numero DI // 40
            $cClausula .= "'{$mT075['T075_Data_Emissao_DI']}', "; // Data registro DI // 41
            $cClausula .= "'{$mT075['D118_Nome']}', "; // Local Desembaraco // 42
            $cClausula .= "'{$mT075['T075_Data_Emissao_DI']}', "; // Data Desenbaraco // 43
            $cClausula .= "'{$mT075['D018_UF']}', "; // UF Desenbaraco // 44
            $cClausula .= "'{$mT075['T075_D024_Id']}', "; // Codigo Exportador // 45
            $cClausula .= "'{$mT055['T055_Valor_Adicao']}', "; // Numero Adicao // 46
            $cClausula .= "'1', "; // Numero sequencia adicao // 47
            $cClausula .= "'{$mT075['T075_D024_Id']}', "; // Codigo Fabricante // 48
            $cClausula .= "'', "; // Valor desconto Item adicao // 49
            $cClausula .= "'', "; // Numero Pedido compra // 50
            $cClausula .= "'', "; // Item pedido compra // 51
            $cClausula .= "'', "; // Percentual Base Operacao Propria // 52
            $cClausula .= "'', "; // Aliquota ICMS Credito // 53
            $cClausula .= "'', "; // Valor ICMS Credito // 54
            $cClausula .= "'', "; // Valor ICMS substituicao destino // 55
            $cClausula .= "'', "; // Valor Base ICMS substituicao destino // 56
            $cClausula .= "'', "; // Valor ICMS subsituicao Retido // 57
            $cClausula .= "'', "; // Valor Base ICMS Substituicao retido // 58
            $cClausula .= "'', "; // Percentual Reducao ICMS ST // 59
            $cClausula .= "'', "; // Modalidade ICMS // 60
            $cClausula .= "'', "; // Modalidade ICMS Substituicao // 61
            $cClausula .= "'S', "; // Flag Calculo Manual // 62
            $cClausula .= "'', "; // UF ICMS Substituticao Devido // 63
            $cClausula .= "'{$mT055[$baseICMS]}', "; // 64
            $cClausula .= "'{$mT055[$valorICMS]}', "; // 65
            $cClausula .= "'{$mT055['T055_Valor_Base_IPI']}', "; // 66
            $cClausula .= "'{$mT055['T055_Valor_IPI']}', "; // 67
            $cClausula .= "'{$mT055['T055_Valor_Base_PIS']}', "; // 68
            $cClausula .= "'{$mT055['T055_Valor_PIS']}', "; // 69
            $cClausula .= "'{$mT055['T055_Valor_Base_COFINS']}', "; // 70
            $cClausula .= "'{$mT055['T055_Valor_COFINS']}', "; // 71
            $cClausula .= "'{$mT055['T055_Valor_Base_ICMS_Normal']}', "; // 72
            $cClausula .= "'{$mT075['T075_Flag_Transporte_DI']}', "; // 73
            $cClausula .= "'{$mT075['T075_Flag_Tipo_Intermedio_DI']}', "; // 74
            $cClausula .= "'{$mT075['T075_CNPJ_Importador']}', "; // 75
            $cClausula .= "'{$mT075['T075_UF_Importador']}', "; // 76
            $cClausula .= "'{$mT075['T075_Numero_Drawback_Importacao']}', "; // 77
            $cClausula .= "'{$mT055['T055_Valor_Preco_Unitario_Nota_Fiscal']}', "; // Valor Preco sem desconto unitario // 78
            $cClausula .= "'{$mT066['T055_T066_Id']}', "; // 79
            $cClausula .= "'{$mT066['T066_D004_Id']}', "; // 80
            $cClausula .= "'{$mT055['T055_Id']}', "; // 81
            $cClausula .= "'{$T055_Valor_Total_Frete}', "; // 82
            $cClausula .= "'{$aliquotaFCP}', "; // 83
            $cClausula .= "'{$valorFCP}', "; // 84
            $cClausula .= "'{$baseFCP}' "; // 85
            $cClausula .= ")";
            
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: " . $cErro . "<br />" . $cClausula;
            }   

            $T008_Id = $g['mysqlLastId'];

            mysql_query("CALL T008_Gravar_Totalizacao_4('$T008_Id')");

            if ($preenchimentoReformaTributariaAutomatico) {
                $cClausula = "INSERT INTO T008A (
                                    T008A_T008_Id,
                                    T008A_Situacao_Tributaria_CBS_IBS,
                                    T008A_Classificacao_Tributaria_CBS_IBS,
                                    T008A_Aliquota_CBS,
                                    T008A_Aliquota_IBS_Municipal,
                                    T008A_Aliquota_IBS_UF,
                                    T008A_Percentual_Reducao_CBS,
                                    T008A_Percentual_Reducao_IBS_Municipal,
                                    T008A_Percentual_Reducao_IBS_UF,
                                    T008A_Percentual_Diferimento_CBS,
                                    T008A_Percentual_Diferimento_IBS_UF,
                                    T008A_Percentual_Diferimento_IBS_Municipal,
                                    T008A_Situacao_Tributaria_CBS_IBS_Regular,
                                    T008A_Classificacao_Tributaria_CBS_IBS_Regular,
                                    T008A_Aliquota_CBS_Regular,
                                    T008A_Aliquota_IBS_Municipal_Regular,
                                    T008A_Aliquota_IBS_UF_Regular,
                                    T008A_Percentual_Reducao_CBS_Regular,
                                    T008A_Percentual_Reducao_IBS_Municipal_Regular,
                                    T008A_Percentual_Reducao_IBS_UF_Regular
                              ) VALUES (
                                    '{$T008_Id}',
                                    '{$T008A_Situacao_Tributaria_CBS_IBS}',
                                    '{$T008A_Classificacao_Tributaria_CBS_IBS}',
                                    '{$T008A_Aliquota_CBS}',
                                    '{$T008A_Aliquota_IBS_Municipal}',
                                    '{$T008A_Aliquota_IBS_UF}',
                                    '{$T008A_Percentual_Reducao_CBS}',
                                    '{$T008A_Percentual_Reducao_IBS_Municipal}',
                                    '{$T008A_Percentual_Reducao_IBS_UF}',
                                    '{$T008A_Percentual_Diferimento_CBS}',
                                    '{$T008A_Percentual_Diferimento_IBS_UF}',
                                    '{$T008A_Percentual_Diferimento_IBS_Municipal}',
                                    '{$T008A_Situacao_Tributaria_CBS_IBS_Regular}',
                                    '{$T008A_Classificacao_Tributaria_CBS_IBS_Regular}',
                                    '{$T008A_Aliquota_CBS_Regular}',
                                    '{$T008A_Aliquota_IBS_Municipal_Regular}',
                                    '{$T008A_Aliquota_IBS_UF_Regular}',
                                    '{$T008A_Percentual_Reducao_CBS_Regular}',
                                    '{$T008A_Percentual_Reducao_IBS_Municipal_Regular}',
                                    '{$T008A_Percentual_Reducao_IBS_UF_Regular}'
                              )";
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . $cErro . "<br />" . $cClausula;
                }

                mysql_query("CALL T008_Gravar_Totalizacao_4('$T008_Id')");
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . $cErro;
                }
            }
            
            if($mT055['T055A_Data_Validade'] != '0000-00-00' && !empty($mT055['T055A_Data_Validade']) && $mT055['T055_T066_Id'] > 0 && $cfopMovimentaEstoque == 'C'){
                // VERIFICA SE JA EXISTE REGISTRO NA TABELA ESTENDIDA
                $T066A = mysql_query("SELECT T066A_Id FROM T066A WHERE T066A_T066_Id = '{$mT055['T055_T066_Id']}'");
                if(mysql_num_rows($T066A) > 0){
                    mysql_query("UPDATE T066A SET T066A_Data_Validade = '{$mT055['T055A_Data_Validade']}' WHERE T066A_T066_Id = '{$mT055['T055_T066_Id']}'");
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return "3 " . $erro;
                    }

                } else {
                    mysql_query("INSERT INTO T066A (T066A_T066_Id,T066A_Data_Validade) VALUES  ('{$mT055['T055_T066_Id']}','{$mT055['T055A_Data_Validade']}')");
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return "3 " . $erro;
                    }
                }
            }
        }

        if(!empty($mT075['D006_Observacao_Produto'])){
            // Busca só o FCP, se precisar de mais campos incluir no select
            $T008 = mysql_query("SELECT T008_Valor_FCP,
                                        T008_Id
                                   FROM T008
                                  WHERE T008_T007_Id = {$T007_Id}");

            while ($mT008 = mysql_fetch_array($T008)) {
                $observacaoFiscalProduto = $mT075['D006_Observacao_Produto'] . ';';

                // Váriaveis dentro da Observação Fiscal
                foreach ($mT008 as $key => $value) {
                    $key = preg_replace('#^[^_]+_#', '', $key);
                    $key = strtoupper($key);
                    if (preg_match('#^([0-9])+\.([0-9])+$#', $value)) {
                        $value = gCorrigeNumero($value);
                    }
                    $observacaoFiscalProduto = str_replace('[' . strtoupper($key) . ']', $value, $observacaoFiscalProduto);
                }
                mysql_query("UPDATE T008 SET T008_Informacoes_Adicionais='{$observacaoFiscalProduto}' WHERE T008_Id='{$mT008['T008_Id']}'");
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . $cErro;
                }      
            }
        }        
        
        //mysql_query("UPDATE T007 SET T007_Numero_Nota_fiscal='' WHERE T007_Id='$T007_Id'");

        require_once('bibliotecas/classes/VEN003.php');
        $VEN003 = new VEN003();
        $retornoReformaTributaria = $VEN003->atualizarCamposReformaTributariaNF($T007_Id);
        if ($retornoReformaTributaria !== true) {
            mysql_query("ROLLBACK");
            return $retornoReformaTributaria;
        }

        mysql_query("CALL T007_Gravar_Totalizacao_4('$T007_Id')");

        // Cadastra os fornecedores dos produtos
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        $T008 = mysql_query("SELECT D001_Id, T007_D024_Id FROM T008 
                            LEFT JOIN D009 ON D009_Id=T008_D009_Id
                            LEFT JOIN D049 ON D049_Id=D009_D049_Id
                            LEFT JOIN D001 ON D001_Id=D049_D001_Id
                            LEFT JOIN T007 ON T007_Id=T008_T007_Id
                            LEFT JOIN T241 ON T241_D001_Id=D001_Id AND T241_D024_Id=T007_D024_Id
                            WHERE T008_T007_Id = '{$T007_Id}'
                            AND T241_Id IS NULL
                            GROUP BY D001_Id");
        while ($mT008 = mysql_fetch_array($T008)) {
            $CAD002->cadastrarFornecedorProduto($mT008['D001_Id'], $mT008['T007_D024_Id']);
        }

        // Para manter a compatibilidade com o modelo 1
        if($mT075['T075_Modelo_Calculos'] == 2){
            mysqli_query("update T007 set T007_Flag_Calcular='N' where T007_Id='{$T007_Id}'");

            $valorTotalNota = $this->retornaValorTotalNota($valorTotalNota, $T007_Id);
    
            mysqli_query("update T007 set T007_Valor_Total='{$valorTotalNota}' where T007_Id='{$T007_Id}'");
        }

        // Lança o pagamento da nota após ter o valor total fechado
        $D027 = mysql_query("SELECT D027_Id 
                               FROM D027
                              WHERE D027_Tipo_Forma_Pagamento_NFCE = '90'");
        $mD027 = mysql_fetch_array($D027);

        /*$T007 = mysql_query("SELECT T007_Valor_Total
                               FROM T007
                              WHERE T007_Id = '{$T007_Id}'");
        $mT007 = mysql_fetch_array($T007);*/

        mysql_query("INSERT INTO T118 (T118_T007_Id,
                                       T118_D027_Id,
                                       T118_Valor_Parcela,
                                       T118_Data_Vencimento,
                                       T118_Prazos_Dias,
                                       T118_Numero_Parcela
                               )VALUES('{$T007_Id}',
                                       '{$mD027['D027_Id']}',
                                       '0',
                                       now(),
                                       '0',
                                       '1')");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cErro;
        }     

        // Atualiza o numero da nota na empresa
        mysql_query("UPDATE C004 SET C004_Ultimo_Numero_Nota_Fiscal_Eletronica = '{$proximoNumero}' 
                               WHERE C004_Id = '{$g['empresaAtual']}'");
        $cErro = mysql_error();
        if (!empty($cErro)) {
            $retorno = "Clausula: " . $cClausula;
            $retorno .= "ERRO: " . $cErro;
            mysql_query("ROLLBACK");
            return $retorno;
        }

        mysql_query("COMMIT");

        return true;
    }
    
    /**
     * imp007Finalizar
     *
     * @param String $T075_Id
     * @return String/bool
     */     
    public function imp007Finalizar($T075_Id)
    {
        global $g;
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $T075  = mysql_query("SELECT T075_Data_Estoque FROM T075 WHERE T075_Id='{$T075_Id}'");
        $mT075 = mysql_fetch_array($T075);
        if ($mT075['T075_Data_Estoque'] != null && $mT075['T075_Data_Estoque'] != '0000-00-00') {
            return "Esse processo já foi realizado";
        }
        
        $T055 = mysql_query("SELECT T066_D004_Id,
                                    T066_Id,
                                    D001_Codigo_Produto,
                                    D001A_Flag_Validade,
                                    T055A_Data_Validade,
                                    T055_T066_Id,
                                    T066A_Data_Validade,
                                    T066_Quantidade_Estoque,
                                    T055A_Id
                               FROM T055
                          LEFT JOIN D009 ON D009_Id=T055_D009_Id
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id
                          LEFT JOIN D001A ON D001_Id=D001A_D001_Id
                          LEFT JOIN T055A ON T055_Id=T055A_T055_Id
                          LEFT JOIN T066 ON T055_T066_Id=T066_Id
                          LEFT JOIN T066A ON T066_Id = T066A_T066_Id
                              WHERE T055_T075_Id='{$T075_Id}'
                           GROUP BY T055_Id");

        $mensagemProduto      = '';
        while ($mT055 = mysql_fetch_array($T055)) {

            $erroProdutos = '';

            if (($mT055['T055_T066_Id']<=0 || $mT055['T066_D004_Id'] <=0)){
                $erroProdutos .= "- Está sem locação preenchida.\n";
            }
            if($mT055['D001A_Flag_Validade'] == 'S' && ($mT055['T055A_Data_Validade'] == '0000-00-00' || empty($mT055['T055A_Data_Validade']))){
                $erroProdutos .= "- Está sem data de validade e produto tem controle de validade.\n";
            }

            if($g['C031']['incluirLocacaoAutomaticaItemDiComValidade'] == 'N' && $mT055['T055_T066_Id'] > 0 && $mT055['T066_Quantidade_Estoque'] > 0 && ($mT055['T055A_Data_Validade'] != '0000-00-00' || empty($mT055['T055A_Data_Validade'])) && $mT055['T066A_Data_Validade'] != $mT055['T055A_Data_Validade'] && $mT055['T055A_Id'] > 0){
                $erroProdutos .= "- A locação selecionada possui estoque e data de validade diferente da DI, verifique ou cadastre uma nova locação.\n";
            }

            if(!empty($erroProdutos)){
                $mensagemProduto .= "CÓDIGO ".$mT055['D001_Codigo_Produto'].":\n".$erroProdutos."\n";
            }   
        }

        if (!empty($mensagemProduto)){                
            mysql_query("ROLLBACK");
            return $mensagemProduto;
        }

        /**
         * SELECIONANDO TODOS OS ITENS DA DI
         */
        $T055 = mysql_query("SELECT T075_Id,
                                    T055_Id,
                                    T055_Quantidade,
                                    T055_D009_Id,
                                    T055_D006_Id,
                                    T075_D024_Id,
                                    T066_D004_Id,
                                    T066_Id,
                                    D049_Id,
                                    D001_Flag_Produto_Serie,
                                    D001_Id,
                                    IFNULL(D001A_Id,0) AS D001A_Id,
                                    T001_Id,
                                    T055_T066_Id,
                                    T055A_Id,
                                    T055A_Data_Validade,
                                    T055A_Largura,
                                    T055A_Altura,
                                    T055A_Comprimento,
                                    T066A_Data_Validade,
                                    T066_Quantidade_Estoque,
                                    T055A_Flag_Credita_Estoque
                            FROM T055 
                            LEFT JOIN T075 ON T075_Id='{$T075_Id}' 
                            LEFT JOIN D009 ON D009_Id=T055_D009_Id
                            LEFT JOIN D049 ON D049_Id=D009_D049_Id
                            LEFT JOIN D001 ON D001_Id=D049_D001_Id
                            LEFT JOIN D001A ON D001_Id=D001A_D001_Id
                            LEFT JOIN T066 ON T066_Id=T055_T066_Id
                            LEFT JOIN T066A ON T066_Id=T066A_T066_Id
                            LEFT JOIN T001 ON T001_T055_Id=T055_Id AND T001_Flag_Operacao!='C' AND T001_Descricao_Operacao like 'IMP-%'
                            LEFT JOIN T055A ON T055_Id = T055A_T055_Id
                            WHERE T055_T075_Id='{$T075_Id}'
                            GROUP BY T055_Id");
                
        if (mysql_num_rows($T055) == 0) {
            return "Não existem produtos nessa DI";
        }

        // LOOPING PARA INSERIR NO HISTÓRICO DO PRODUTO
        while ($mT055 = mysql_fetch_array($T055)) {
            if ($mT055['T001_Id']>0){
                continue;
            }

            if($g['C031']['incluirLocacaoAutomaticaItemDiComValidade'] == 'S' && $mT055['T055_T066_Id'] > 0 && $mT055['T066_Quantidade_Estoque'] > 0 && ($mT055['T055A_Data_Validade'] != '0000-00-00' || empty($mT055['T055A_Data_Validade'])) && $mT055['T066A_Data_Validade'] != $mT055['T055A_Data_Validade'] && $mT055['T055A_Id'] > 0){
                //Lança um novo T066 com o mesmo D004 que vai entrar com nova data de validade
                mysql_query("INSERT INTO T066 (T066_D009_Id, T066_D004_Id) VALUES ('{$mT055['T055_D009_Id']}', '{$mT055['T066_D004_Id']}')");

                $mT055['T055_T066_Id'] = $g['mysqlLastId'];
                mysqli_query("UPDATE T055 SET T055_T066_Id = '{$mT055['T055_T066_Id']}' WHERE T055_Id = '{$mT055['T055_Id']}'");
            }

            $creditaEstoque = ($mT055['T055A_Flag_Credita_Estoque'] == 'N') ? 'N' : 'E';
            
            $sql = <<<EOT
            INSERT INTO T001 (
                T001_Descricao_Operacao,
                T001_Flag_Operacao,
                T001_Quantidade,
                T001_Data_Lancamento,
                T001_T055_Id,
                T001_D009_Id,
                T001_D006_Id,
                T001_D024_Id,
                T001_D004_Id,
                T001_T066_Id
            ) VALUES (
                'IMP-{$T075_Id}',
                '{$creditaEstoque}',
                '{$mT055['T055_Quantidade']}',
                CURDATE(),
                '{$mT055['T055_Id']}',
                '{$mT055['T055_D009_Id']}',
                '{$mT055['T055_D006_Id']}',
                '{$mT055['T075_D024_Id']}',
                '{$mT055['T066_D004_Id']}',
                '{$mT055['T055_T066_Id']}'
            );
EOT;
            mysql_query($sql);
            $erro = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return "1 " . $erro;
            }
            
            $T006 = mysql_query("UPDATE T006 SET T006_T075_Id='' WHERE T006_T075_Id='{$T075_Id}' and T006_D009_Id='{$mT055['T055_D009_Id']}'");
            $erro = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return "2 " . $erro;
            }

            // $retorno = $CAD002->D001_reprocessa_historico(0, $mT055['T055_D009_Id'],100);
            $retornoProcessa = $CAD002->D001_reprocessa_historico(0, $mT055['T055_D009_Id'],100);
            if($retornoProcessa !== true){
                registrarLog(array("D009", $mT055['T055_D009_Id'], $g['empresaAtual'], " Erro ao reprocessar histórico - IMP003 - ATUALIZAR CUSTO:", $retornoProcessa, "S",  3));
                $retorno = $CAD002->D001_reprocessa_historico(0, $mT055['T055_D009_Id']);

                if ($retorno != true) {
                    mysql_query("ROLLBACK");
                    return "Erro reprocessa histórico: " . $retorno;
                }
            }
            
            if ($mT055['D001_Flag_Produto_Serie'] == 'S') {
                
                $quantidade = $mT055['T055_Quantidade'];
                
                for ($i = 1; $i <= $quantidade; $i++) {
                    
                    $D144 = <<<EOT
                        INSERT INTO D144 (
                            D144_D049_Id,
                            D144_T055_Id
                        ) VALUES (
                            '{$mT055['D049_Id']}',
                            '{$mT055['T055_Id']}'
                        );
EOT;
                    $retorno = mysql_query($D144);
                    if ($retorno != true) {
                        mysql_query("ROLLBACK");
                        return "Erro ao inserir o número de série.";
                    }
                }
            }

            if($mT055['T055A_Data_Validade'] != '0000-00-00' && !empty($mT055['T055A_Data_Validade'])){
                //VERIFICA SE JA EXISTE REGISTRO NA TABELA ESTENDIDA
                $T066A = mysql_query("SELECT T066A_Id FROM T066A WHERE T066A_T066_Id = '{$mT055['T055_T066_Id']}'");
                if(mysql_num_rows($T066A) > 0){
                    mysql_query("UPDATE T066A SET T066A_Data_Validade = '{$mT055['T055A_Data_Validade']}' WHERE T066A_T066_Id = '{$mT055['T055_T066_Id']}'");
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return "3 " . $erro;
                    }

                } else {
                    mysql_query("INSERT INTO T066A (T066A_T066_Id,T066A_Data_Validade) VALUES  ('{$mT055['T055_T066_Id']}','{$mT055['T055A_Data_Validade']}')");
                    $erro = mysql_error();
                    if (!empty($erro)) {
                        mysql_query("ROLLBACK");
                        return "3 " . $erro;
                    }
                }
            }

            if($mT055['D001A_Id'] <= 0){
                mysql_query("INSERT INTO D001A (D001A_D001_Id,
                                                D001A_Largura,
                                                D001A_Altura,
                                                D001A_Comprimento) 
                                        VALUES ('{$mT055['D001_Id']}',
                                                '{$mT055['T055A_Largura']}',
                                                '{$mT055['T055A_Altura']}',
                                                '{$mT055['T055A_Comprimento']}')");
            } else {
                mysqli_query("UPDATE D001A 
                                 SET D001A_Largura     = IF('{$mT055['T055A_Largura']}' > 0, '{$mT055['T055A_Largura']}', D001A_Largura),
                                     D001A_Altura      = IF('{$mT055['T055A_Altura']}' > 0, '{$mT055['T055A_Altura']}', D001A_Altura),
                                     D001A_Comprimento = IF('{$mT055['T055A_Comprimento']}' > 0, '{$mT055['T055A_Comprimento']}', D001A_Comprimento)
                               WHERE D001A_Id = '{$mT055['D001A_Id']}'");

            }

            if($mT055['T055A_Altura'] > 0 && $mT055['T055A_Comprimento'] > 0 && $mT055['T055A_Largura'] > 0){
                $cubagem = $mT055['T055A_Altura'] * $mT055['T055A_Largura'] * $mT055['T055A_Comprimento'];

                mysqli_query("UPDATE D001 
                                 SET D001_Cubagem_Unitaria = '{$cubagem}' 
                               WHERE D001_Id = '{$mT055['D001_Id']}'");
            }
        }

        $T075 = mysql_query("UPDATE T075 SET T075_Data_Estoque=CURDATE(), 
                                             T075_C007_Id_Finalizou_Estoque = '{$g['usuarioAtual']}'
                                       WHERE T075_Id='{$T075_Id}'");
        $erro = mysql_error();
        if ($erro) {
            mysql_query("ROLLBACK");
            return "4 " . $erro;
        }

        mysql_query("COMMIT");
        return true;
    }

    /**
     * imp003AtualizarCusto
     *
     * @param String $T075_Id
     * @return String/bool
     */     
    public function imp003AtualizarCusto($T075_Id)
    {
        global $g;
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        
        mysql_query("SET autocommit=0");
        mysql_query("START TRANSACTION");
        
        /**
         * SELECIONANDO TODOS OS ITENS DA DI
         */
        $T055 = mysql_query("SELECT *, T001_Valor_Custo_Unitario, T055_Valor_Custo_Final_Unitario, T055_Codigo_Produto FROM T075
                            LEFT JOIN T007 ON T007_T075_Id = T075_Id
                            LEFT JOIN T008 ON T008_T007_Id = T007_Id
                            LEFT JOIN T001 ON T001_T008_Id = T008_Id
                            LEFT JOIN T055 ON T055_D009_Id = T001_D009_Id AND T055_T075_Id = T075_Id 
                            WHERE T075_Id = '{$T075_Id}'");
        
        if (mysql_num_rows($T055) == 0) {
            return "Não existem produtos nessa DI";
        }
        
        // Loop para atualizar os custos na T001 (Histórico do Produto)
        while ($mT055 = mysql_fetch_array($T055)) {
            mysql_query("UPDATE T001 SET T001_Valor_Custo_Unitario = '{$mT055['T055_Valor_Custo_Final_Unitario']}' WHERE T001_Id ='{$mT055['T001_Id']}'");
            $erro = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return "1 " . $erro;
            }
            
            // $retorno = $CAD002->D001_reprocessa_historico(0, $mT055['T055_D009_Id'],100);
            $retornoProcessa = $CAD002->D001_reprocessa_historico(0, $mT055['T055_D009_Id'],100);
            if($retornoProcessa !== true){
                registrarLog(array("D009", $mT055['T055_D009_Id'], $g['empresaAtual'], " Erro ao reprocessar histórico - IMP003 - FINALIZAR:", $retornoProcessa, "S",  3));
                $retorno = $CAD002->D001_reprocessa_historico(0, $mT055['T055_D009_Id']);

                if ($retorno != true) {
                    mysql_query("ROLLBACK");
                    return "Erro reprocessa histórico: " . $retorno;
                }
            }
        }
        mysql_query("UPDATE T075 SET T075_Data_Atualizacao_Custo = now() WHERE T075_Id = '{$T075_Id}'");
        mysql_query("COMMIT");
        return true;
    }

    /**
     * preencherQuantidadeItensOC
     *
     * @param String $T024_Id
     * @param String $sql
     * @param String $quantidade
     * @return String/bool
     */
    public function preencherQuantidadeItensOC($D024_Id, $sql, $quantidade)
    {
        global $g;
        $extra = gPegarParteWhere($sql);
        $quantidade = ($quantidade == 'total') ? 'T225_Quantidade' : 0;

        mysql_query("UPDATE T225 LEFT JOIN T224 ON T224_Id=T225_T224_Id LEFT JOIN D024 ON D024_Id=T224_D024_Id SET T225_Quantidade_Dividir = if(T225_Quantidade_Dividido > 0 && {$quantidade} > 0, {$quantidade}-T225_Quantidade_Dividido, {$quantidade})  WHERE {$extra}");
        $erro = mysql_error();
        if ($erro) {
            return $erro;
        }
        return true;
    }

    /**
     * preencherQuantidadeItensOC
     *
     * @param String $T075_Id
     * @param String $D024_Id
     * @return String/bool
     */
    public function importarItensOC($T075_Id, $D024_Id)
    {
        global $g;
        $sqlT225 = mysql_query("SELECT T225_Id, T225_Descricao_Produto, T225_Quantidade_Dividir, T225_D037_Id, T225_D005_Id, D149_Imposto_Importacao, 
                                       D149_Importacao_IPI, D149_Importacao_PIS, D149_Importacao_COFINS, T225_D009_Id, T225_Codigo_Produto, T224_C004_Id, T225_Valor_Preco_Unitario 
                                FROM T225 
                                LEFT JOIN T224 ON T224_Id=T225_T224_Id 
                                LEFT JOIN D149 ON D149_D005_Id=T225_D005_Id AND D149_C004_Id=T224_C004_Id 
                                WHERE T224_D024_Id = '{$D024_Id}' AND T225_Quantidade_Dividir > 0 GROUP BY T225_Id");
        while($T225 = mysql_fetch_array($sqlT225)){
            $sql = <<<EOT
                INSERT INTO T055 (
                    T055_Descricao_Produto,
                    T055_Quantidade,
                    T055_D037_Id,
                    T055_D005_Id,
                    T055_Valor_Custo_Unitario,
                    T055_Flag_ME,
                    T055_Aliquota_Imposto_Importacao,
                    T055_Aliquota_IPI,
                    T055_Aliquota_PIS,
                    T055_Aliquota_COFINS,
                    T055_D009_Id,
                    T055_Codigo_Produto,
                    T055_T075_Id,
                    T055_C004_Id,
                    T055_T225_Id
                ) VALUES (
                    '{$T225['T225_Descricao_Produto']}',
                    '{$T225['T225_Quantidade_Dividir']}',
                    '{$T225['T225_D037_Id']}',
                    '{$T225['T225_D005_Id']}',
                    '{$T225['T225_Valor_Preco_Unitario']}',
                    'S',
                    '{$T225['D149_Imposto_Importacao']}',
                    '{$T225['D149_Importacao_IPI']}',
                    '{$T225['D149_Importacao_PIS']}',
                    '{$T225['D149_Importacao_COFINS']}',
                    '{$T225['T225_D009_Id']}',
                    '{$T225['T225_Codigo_Produto']}',
                    '{$T075_Id}',
                    '{$T225['T224_C004_Id']}',
                    '{$T225['T225_Id']}'
                );
EOT;
            mysql_query($sql);
            $erro = mysql_error();
            if ($erro) {
                return $erro;
            }

            mysql_query("UPDATE T225 SET T225_Quantidade_Dividido = T225_Quantidade_Dividido+T225_Quantidade_Dividir, T225_Quantidade_Dividir = 0 WHERE T225_Id = '{$T225['T225_Id']}'");
            $erro = mysql_error();
            if ($erro) {
                return $erro;
            }
        }
        return true;
    }

    
    public function __construct()
    {
        $teste='';
    }

    public function GerarXMLLI($T075_Id)
    {
        global $g;

        set_time_limit(0);
        $dir = $g['pathDados'] . 'tmp/';
        is_dir($dir) OR mkdir($dir, 0777, true);
        $filename             = "xml-li-{$T075_Id}.xml";
        $file                 = $dir . $filename;
        $fileFull             = $g['pathRaiz'] . '/' . $file;
        
      /*   if(!file_exists($fileFull)){
          echo "O arquivo não existe";
          exit;
        }
        
      header("Content-Type: application/force-download");
        header("Content-type: application/octet-stream;");
        header("Content-Length: " . filesize( $fileFull ) );
        header("Content-disposition: attachment; filename=" . $fileName );
        header("Pragma: no-cache");
        header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");
        readfile($fileFull);
        flush();
        readfile($filename);
*/
    /*    $cClausula = <<<EOT
                   select
                   *
                   from T075
                   left join C004 on C004_Id=T075_C004_Id
                   left join C020 on C020_C004_Id=C004_Id
                   left join D024 on D024_Id=T075_D024_Id
                   left join D030 on D030_Id=D024_D030_Id
                   left join D020 on D020_Id=D024_D020_Id
                   left join D018 on D018_Id=D024_D018_Id
                   left join D053 on D053_D018_Id=D024_D018_Id AND D053_C004_Id=T075_C004_Id
                   left join T055 on T055_T075_Id=T075_Id  
                   left join D005 on D005_Id=T055_D005_Id
                   left join T270 on T270_T075_Id=T075_Id

                   where T075_Id='{$T075_Id}'
                   group by D005_Id
                   order by D005_Classificacao_Fiscal
EOT;
*/
 
        $cClausula = <<<EOT
                 SELECT T270_Numero_LI,
                        T270_Id,
                        T270_Numero_Processo_Anuente, 
                        T270_Sigla_Orgao_Anuente, 
                        T055_Item_Ac_Drawback,
                        T270_Regime_Acordo_Tributario, 
                        T270_Fundamento_Legal_Regime,
                        T270_Tipo_Acordo_Tarifario,
                        T270_Codigo_Acordo_Alardi,
                        T270_Numero_Dias_Limite_Pagamento, 
                        T270_Codigo_Orgao_Financeiro_Internacional,
                        T270_Codigo_Motivo_Sem_Cobertura,
                        T270_Numero_Destaque_NCM,
                        T270_Informacoes_Complementares, 
                        T270_Drawback_Ato_Isencao, 
                        T270_Drawback_Ato_Suspensao,
                        C004_Cnpj,
                        D030_Nome_Pais,
                        D018_UF,
                        D020_Nome_Cidade,
                        D024_Flag_Pessoa_Fisica_Juridica,
                        D024_Codigo_Fornecedor,
                        D024_Nome_Empresa,
                        D024_E_Mail,
                        D024_Nome_Contato,
                        D024_Endereco,
                        D024_Numero, 
                        D024_Complemento,
                        D005_Classificacao_Fiscal,
                        T075_Numero_Drawback_Importacao,
                        T075_Codigo_URF_Entrada,   
                        T075_Codigo_URF_Despacho,                    
                        T075_Codigo_Moeda,
                        T075_Numero_DI,
                        T075_Codigo_Incoterms,
                        T075_Condicao_Mercadoria,
                        T075_Enquadramento_Material_Usado,
                        T075_Operacao_Enquadramento_Material_Usado ,
                        T270_Cobertura_Cambial,
                        T270_Modalidade_Pagamento          
                FROM T270
                LEFT JOIN T075 ON T075_id=T270_T075_id
                LEFT JOIN C004 ON C004_Id=T075_C004_Id
                LEFT JOIN C020 ON C020_C004_Id=C004_Id
                LEFT JOIN D024 ON D024_Id=T075_D024_Id
                LEFT JOIN D030 ON D030_Id=D024_D030_Id
                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                LEFT JOIN D018 ON D018_Id=D024_D018_Id
                LEFT JOIN t055 ON T055_id=T270_id
                LEFT JOIN D005 ON D005_Id=T055_D005_Id
                
                WHERE T075_Id='{$T075_Id}'
                ORDER BY T270_Numero_LI
EOT;
        $cSql_T075 = mysql_query($cClausula);
        echo mysql_error();

        $dom = new \DOMDocument("1.0", "UTF-8");
        //indica se os espaços em branco devem ser preservados no conteúdo do elemento.
        $dom->preserveWhiteSpace = false;
        //Serve como a raiz da árvore do documento.
        $dom->formatOutput = true;

        $loteLi = $dom->createElement("lote-li");
        $loteLi->setAttribute("xsi:schemaLocation","http://www.serpro.gov.br/liweb/schema/LoteLiWeb.html LoteLiWeb.xsd");
        $loteLi->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
        $loteLi->setAttribute("xmlns","http://www.serpro.gov.br/liweb/schema/LoteLiWeb.html");
        $loteLi->setAttribute("idArquivoLote","A00001");

        while ($mT075 = mysql_fetch_array($cSql_T075)) {

              $li = $dom->createElement("li");

              $item = $dom->createElement("identificador-li","{$mT075['T270_Numero_LI']}");
              $li->appendChild($item);

              if($mT075['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){
                $tipoPessoa = 1;
              } else{
                $tipoPessoa = 2;
              }
              $item = $dom->createElement("tipo-importador","{$tipoPessoa}");
              $li->appendChild($item);
              $item = $dom->createElement("identificacao-importador","{$mT075['C004_Cnpj']}");
              $li->appendChild($item);
              $item = $dom->createElement("pais-procedencia-mercadoria","{$mT075['D030_Nome_Pais']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-urf-entrada","{$mT075['T075_Codigo_URF_Entrada']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-urf-despacho","{$mT075['T075_Codigo_URF_Despacho']}");
              $li->appendChild($item);
              $item = $dom->createElement("texto-informacoes-complementares","{$mT075['T270_Informacoes_Complementares']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-fornecedor","{$tipoPessoa}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-nome","{$mT075['D024_Nome_Empresa']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-email","{$mT075['D024_E_Mail']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-responsavel","{$mT075['D024_Nome_Contato']}");
              $li->appendChild($item);
              $item = $dom->createElement("pais-aquisicao-mercadoria","{$mT075['D030_Nome_Pais']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-logradouro","{$mT075['D024_Endereco']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-numero","{$mT075['D024_Numero']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-complemento","{$mT075['D024_Complemento']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-cidade","{$mT075['D020_Nome_Cidade']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-uf","{$mT075['D018_UF']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-nome", "{$mT075['D024_Nome_Empresa']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-email","{$mT075['D024_E_Mail']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-responsavel","D024_Nome_Contato");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-logradoudo","{$mT075['D024_Endereco']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-numero","{$mT075['D024_Numero']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-complemento", "{$mT075['D024_Complemento']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-cidade","{$mT075['D020_Nome_Cidade']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-estado","{$mT075['D018_UF']}");
              $li->appendChild($item);
              $item = $dom->createElement("pais-origem-mercadoria","{$mT075['D030_Nome_Pais']}");
              $li->appendChild($item);
              $item = $dom->createElement("subitem-ncm","{$mT075['D005_Classificacao_Fiscal']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-mercadoria-naladi","{$mT075['D005_Classificacao_Fiscal']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-moeda","{$mT075['T075_Codigo_Moeda']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-incoterms","{$mT075['T075_Codigo_Incoterms']}");
              $li->appendChild($item);
              $item = $dom->createElement("condicao-mercadoria","{$mT075['T075_Condicao_Mercadoria']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-enquadramento-material-usado", "{$mT075['T075_Enquadramento_Material_Usado']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-operacao-enquadramento-material-usado", "{$mT075['T075_Operacao_Enquadramento_Material_Usado']}");
              $li->appendChild($item);
              $item = $dom->createElement("destaque-ncm");
                $subItem = $dom->createElement("numero-destaque-ncm","{$mT075['T270_Numero_Destaque_NCM']}");
                $item->appendChild($subItem);
              $li->appendChild($item);
              $item = $dom->createElement("processo-anuente");
                $subItem = $dom->createElement("numero-processo-anuente", "{$mT075['T270_Numero_Processo_Anuente']}");
                $item->appendChild($subItem);
                $subItem = $dom->createElement("sigla-orgao-anuente", "{$mT075['T270_Sigla_Orgao_Anuente']}");
                $item->appendChild($subItem);
              $li->appendChild($item);
              $item = $dom->createElement("drawback-regime","{$mT075['T075_Numero_Drawback_Importacao']}");
              $li->appendChild($item);
              $item = $dom->createElement("drawback-numero-ato-isencao", "{$mT075['T270_Drawback_Ato_Isencao']}");
              $li->appendChild($item);
              $item = $dom->createElement("drawback-numero-ato-suspencao", "{$mT075['T270_Drawback_Ato_Suspensao']}");
              $li->appendChild($item);            
              $item = $dom->createElement("detalhe-ncm-item-drawback");
                $seqItem = 1;
                $T055 = mysql_query("select D037_Unidade, 
                                            T055_Peso_Kg_Liquido_Unitario, 
                                            T055_Quantidade, 
                                            T055_Valor_Custo_Unitario, 
                                            T055_Descricao_Produto, 
                                            D082_Marca,
                                            T055_Item_Ac_Drawback 
                                      FROM T055 
                                      LEFT JOIN D037 on D037_Id=T055_D037_Id 
                                      LEFT JOIN D009 ON D009_Id=T055_d009_Id 
                                      LEFT JOIN D049 ON D049_id=D009_D049_Id
                                      LEFT JOIN D082 ON D082_id=D049_D082_Id 
                                      WHERE T055_T270_Id='{$mT075['T270_Id']}'");
                while ($mT055 = mysql_fetch_array($T055)){

                        $subItem = $dom->createElement("numero-sequencial-produto","{$seqItem}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("nome-unidade-medida-comercializada","{$mT055['D037_Unidade']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("peso-liquido","{$mT055['T055_Peso_Kg_Liquido_Unitario']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("qtd-mercadoria-unidade-xcomercializada","{$mT055['T055_Quantidade']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("qtd-mercadoria-unidade-estatistica","{$mT055['T055_Quantidade']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("valor-unidade-condicao-venda","{$mT055['T055_Valor_Custo_Unitario']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("valor-unidade-local-embarque","{$mT055['T055_Valor_Custo_Unitario']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("descricao-produto","{$mT055['T055_Descricao_Produto']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("marca", "{$mT055['D082_Marca']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("modelo");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("numero-serie");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("ano-fabricacao");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("item-ac-drawback", "{$mT055['T055_Item_Ac_Drawback']}");
                        $item->appendChild($subItem);
                        $seqItem++;
                }
              $li->appendChild($item);
              $item = $dom->createElement("regime-acordo-tributario","{$mT075['T270_Regime_Acordo_Tributario']}");
              $li->appendChild($item);
              $item = $dom->createElement("fundamento-legal-regime", "{$mT075['T270_Fundamento_Legal_Regime']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-acordo-tarifario", "{$mT075['T270_Tipo_Acordo_Tarifario']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-acordo-aladi", "{$mT075['T270_Codigo_Acordo_Alardi']}");
              $li->appendChild($item);
              $item = $dom->createElement("cobertura-cambial","{$mT075['T270_Cobertura_Cambial']}");
              $li->appendChild($item);
              $item = $dom->createElement("modalidade-pagamento","{$mT075['T270_Modalidade_Pagamento']}");
              $li->appendChild($item);
              $item = $dom->createElement("numero-dias-limite-pagamento", "{$mT075['T270_Numero_Dias_Limite_Pagamento']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-orgao-financeiro-internacional", "{$mT075['T270_Codigo_Orgao_Financeiro_Internacional']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-motivo-sem-cobertura", "{$mT075['T270_Codigo_Motivo_Sem_Cobertura']}");
              $li->appendChild($item);
              $item = $dom->createElement("numero-li-substituida", "{$mT075['T270_Numero_LI']}");
              $li->appendChild($item);
      
              $loteLi->appendChild($li);

        }

        $dom->appendChild($loteLi);

        $dom->normalize();
        $dom->save("{$fileFull}");
        unset($dom);
      
        return '/hardness3/' . $file;

    }

   /**
     * geraDownloadXMLI
     *
     * @param String $T002_T035_Id
     * @return array
     */
    public function geraDownloadXMLLI($T075_Id)
    {
        global $g;
        
        $dir = $g['pathDados'] . 'tmp/';
        $filename             = "xml-li-{$T075_Id}.xml";
        $file                 = $dir . $filename;    

        if (file_exists($file)){
          $handle = fopen($file, 'r');
          $conteudo = fread($handle, filesize($file));
          fclose ($handle);        
        } else {
          $conteudo = "O arquivo ".$file." não existe";
        }

        header('Content-type: application/x-msdownload');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        return $conteudo;
    }

    public function importarCSV($arquivo, $acaoId)
    {
   
      global $g;
        
      $mArquivo = $arquivo;
        
      $cArquivo          = $mArquivo["name"];
      $cTipo_Arquivo     = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
      $cTipo_Arquivo     = StrToLower($cTipo_Arquivo);
      $acaoId            = $acaoId;

      log("Acaoid= ". $acaoID);

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
      $array_cols=array('PRODUTO','QUANTIDADE','UNIDADE','NCM','PRECO UNITARIO','ME','PESO UNITARIO','II','IPI','PIS','COFINS','ADICAO', 'ITEM','LOCAL','AC_DRAWBACK','LI','ICMS', 'ICMS PRESUMIDO','REDUCAO ICMS','REDUCAO ICMS PRESUMIDO', 'ICMS PIS/COFINS', 'ICMS CREDITO PRESUMIDO');
      //posicao do array  <0>         <1>         <2>      <3>      <4>         <5>       <6>         <7>   <8>   <9>    <10>     <11>     <12>   <13>      <14>   
      //  <15>  <16>         <17>           <18>                <19>                  <20>                     <21>

      //linha não pode ser 0 pois no excel começa no 1 
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
         $array_linha[14] = trim($array_linha[14]);
         $array_linha[15] = trim($array_linha[15]);
         $array_linha[16] = trim($array_linha[16]);
         $array_linha[17] = trim($array_linha[17]);
         $array_linha[18] = trim($array_linha[18]);
         $array_linha[19] = trim($array_linha[19]);
         $array_linha[20] = trim($array_linha[20]);
         $array_linha[21] = trim($array_linha[21]);
                              

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
            $array_linha[1]= mb_strtoupper($array_linha[1]);

            if(!empty($array_linha[0])){
               // Validar se código existe
               // $codigoProduto = str_pad($array_linha[0], 5, "0", STR_PAD_LEFT);
              $codigoProduto = $array_linha[0];
               log("Produto" . $codigoProduto);
               $cClausula = "SELECT D001_Codigo_Produto, D001_Descricao_Produto, D082_Marca, D009_Id 
                             FROM D001 
                             LEFT JOIN D049 ON D049_D001_Id=D001_Id 
                             LEFT JOIN D009 ON D009_D049_Id=D049_Id 
                             LEFT JOIN D082 ON D082_Id=D049_D082_Id 
                             WHERE D001_Codigo_Produto='$codigoProduto'";

               $cSQL_D001 = mysql_query($cClausula);
               $mD001    = mysql_fetch_array($cSQL_D001);
               $mcodigoProduto[$incrementaResult] = $mD001['D001_Codigo_Produto'];
               $D009_Id[$incrementaResult] = $mD001['D009_Id'];
               log("incrementa result= ". $incrementaResult);
               log("produto = ".$codigoProduto[$incrementaResult]);
               log("query = ".$cClausula);
               $descricaoProduto[$incrementaResult] = $mD001['D001_Descricao_Produto'];
               $Marca[$incrementaResult] = $mD001['D082_Marca'];
               $cErro     = mysql_error();
               if(!empty($cErro)){
                  $return.= "Erro: ".$cErro."\\nClausula: ".$cClausula; 
               }                              
               if(mysql_num_rows($cSQL_D001) == 0){
                  $return .= $msgLinha . "O produto  ". $codigoProduto ." não está cadastrado!\\n";
               }
                 
            }

            //Valida se a UNIDADE existe
            if(!empty($array_linha[2])){
               $cSelectUnidade = "SELECT D037_Id FROM D037 where D037_Descricao_Unidade='$array_linha[2]' ";
               $cSQL_D037 = mysql_query($cSelectUnidade);
               $mD037 = mysql_fetch_array($cSQL_D037);
               $unidade[$incrementaResult] = $mD037['D037_Id'];
               $cErro = mysql_error();
               if(!empty($cErro)){
                  $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectUnidade; 
               }
               if(mysql_num_rows($cSQL_D037) == 0){
                  $return .= $msgLinha . "A unidade ". $array_linha[2] ." não está cadastrada!\\n";
               } 
            }

            //Valida se a NCM existe
            if(!empty($array_linha[3])){
               $cSelectUnidade = "SELECT D005_Id FROM D005 where D005_Classificacao_Fiscal='$array_linha[3]' ";
               $cSQL_D005 = mysql_query($cSelectUnidade);
               $mD005 = mysql_fetch_array($cSQL_D005);
               $ncm[$incrementaResult] = $mD005['D005_Id'];
               $cErro = mysql_error();
               if(!empty($cErro)){
                  $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectUnidade; 
               }
               if(mysql_num_rows($cSQL_D005) == 0){
                  $return .= $msgLinha . "A NCM ". $array_linha[3] ." não está cadastrada!\\n";
               } 
            }


            //Valida Flag ME
            if(!empty($array_linha[5])){
               if(($array_linha[5] < 1) || ($array_linha[5] > 2 )){
                  $return .= $msgLinha . 'O conteúdo "' . $array_linha[5] .'" da coluna "ME" não é válido! Digite 1 ou 2.\\n';    
               }
            }

            //Valida se a Locação existe
            if(!empty($array_linha[13])){
               $mD009_Id = $D009_Id[$incrementaResult];
               //VALIDA SE A LOCAÇÃO ESTÁ CADASTRADA 
               $cSelectLocacao = "SELECT D004_Id
                                    FROM D004                                 
                                   WHERE D004_Local='$array_linha[13]'";
               $cSQL_D009 = mysql_query($cSelectLocacao);

               $cErro = mysql_error();
               if(!empty($cErro)){
                  $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectLocacao;
               }
               //VALIDA SE A LOCAÇÃO ESTÁ CADASTRADA NO PRODUTO
               $cSelectLocacaoProduto = "SELECT T066_Id
                                           FROM D009 
                                      LEFT JOIN T066 ON T066_D009_Id = D009_Id  
                                      LEFT JOIN D004 ON D004_Id = T066_D004_Id
                                          WHERE T066_D009_Id = '{$mD009_Id}'
                                            AND D004_Local='{$array_linha[13]}' ";
               $cSQL_D009_Locacao_Produto = mysql_query($cSelectLocacaoProduto);
               $mD009LocacaoProduto = mysql_fetch_array($cSQL_D009_Locacao_Produto);

               $cErro = mysql_error();
               if(!empty($cErro)){
                  $return.= "Erro: ".$cErro."\\nClausula: ".$cSelectLocacaoProduto; 
               }

               $locacao[$incrementaResult] = $mD009LocacaoProduto['T066_Id'];
               
               log("$locacao[$incrementaResult]");

               if(mysql_num_rows($cSQL_D009) == 0){
                  $return .= $msgLinha . "A locacao ". $array_linha[13] ." não está cadastrada!\\n"; 
               }

               if(empty($locacao[$incrementaResult])){
                  $return .= $msgLinha . "A locacao ". $array_linha[13] ." não está cadastrada no produto!\\n";  
               }

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
                    $array_linha[14] = trim($array_linha[14]);
                    $array_linha[15] = trim($array_linha[15]);
                    $array_linha[16] = trim($array_linha[16]);
                    $array_linha[17] = trim($array_linha[17]);
                    $array_linha[18] = trim($array_linha[18]);
                    $array_linha[19] = trim($array_linha[19]);
                    $array_linha[20] = trim($array_linha[20]);
                    $array_linha[21] = trim($array_linha[21]);

                    $cInsert = "INSERT INTO T055 
                                      (T055_T075_Id, /*acaoId*/
                                       T055_D009_Id, /*Id Produto Marca*/
                                       T055_Codigo_Produto, /*[0]*/  
                                       T055_Descricao_Produto, /*descricao produto*/ 
                                       T055_Quantidade,    /*[1]*/ 
                                       T055_D037_Id,/*[2] - unidade*/
                                       T055_D005_Id,/*[3] - ncm*/
                                       T055_Valor_Custo_Unitario, /*[4]*/ 
                                       T055_Flag_ME,  /*[5]*/ 
                                       T055_Peso_Kg_Liquido_Unitario, /*[6]*/ 
                                       T055_Aliquota_Imposto_Importacao, /*[7]*/ 
                                       T055_Aliquota_IPI, /*[8]*/ 
                                       T055_Aliquota_PIS, /*[9]*/ 
                                       T055_Aliquota_COFINS,/*[10]*/ 
                                       T055_Valor_Adicao,/*[11]*/
                                       T055_Adicao_Item, /*Adicao item*/ 
                                       T055_T066_Id,/*[12] - Local*/
                                       T055_Item_Ac_Drawback,/*[13]*/ 
                                       T055_T270_Id, /*[14] LI*/
                                       T055_Aliquota_ICMS,/*[15]*/ 
                                       T055_Aliquota_ICMS_Presumido,/*[16]*/ 
                                       T055_Percentual_Reducao_ICMS,/*[17]*/ 
                                       T055_Percentual_Reducao_ICMS_Presumido,/*[18]*/ 
                                       T055_Aliquota_ICMS_PIS_COFINS,/*[19]*/ 
                                       T055_Aliquota_ICMS_Credito_Presumido)/*[20]*/ 
                               VALUES ('$acaoId', /*acaoId*/
                                       '$D009_Id[$incrementaResult]', /*Id Produto Marca*/
                                       '$mcodigoProduto[$incrementaResult]', /*[0]*/ 
                                       '$descricaoProduto[$incrementaResult]', /*descricao produto*/ 
                                       '$array_linha[1]',  /*[1]*/ 
                                       '$unidade[$incrementaResult]', /*[2]*/ 
                                       '$ncm[$incrementaResult]', /*[3]*/ 
                                       '$array_linha[4]', /*[4]*/ 
                                       '$array_linha[5]',/*[5]*/ 
                                       '$array_linha[6]',/*[6]*/ 
                                       '$array_linha[7]',/*[7]*/ 
                                       '$array_linha[8]',/*[8]*/ 
                                       '$array_linha[9]',/*[9]*/ 
                                       '$array_linha[10]',/*[10]*/ 
                                       '$array_linha[11]',/*[11]*/ 
                                       '$array_linha[12]',/*adicao item*/
                                       '$locacao[$incrementaResult]',/*[12]*/ 
                                       '$array_linha[14]',/*[13]*/ 
                                       '$array_linha[15]',/*[14]*/ 
                                       '$array_linha[16]',/*[15]*/ 
                                       '$array_linha[17]',/*[16]*/ 
                                       '$array_linha[18]',/*[17]*/ 
                                       '$array_linha[19]',/*[18]*/ 
                                       '$array_linha[20]',/*[19]*/ 
                                       '$array_linha[21]')";/*[20]*/ 


                    $cSQL_T055 = mysql_query($cInsert);
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
            
            return "Processamento de arquivo concluído com sucesso!!\\n************************************************** \\n\\n Foram inseridos ".($linha-2)." Produtos!";

      
        } else {
            return $return;
        }        
   }

     /**
     * retornaICMS
     *
     * @param String $D118_Flag_Tipo
     * @return array
     */

    public function retornaICMS($D118_Flag_Tipo)
    {        
        if ($D118_Flag_Tipo == 'N') {
            //caso o tipo da D.I no cadastro do porto seja Normal
            $baseICMS  = "T055_Valor_Base_ICMS_Sem_Reducao";
            $valorICMS = "T055_Valor_ICMS";
        } else if ($D118_Flag_Tipo == 'S') {
            //caso o tipo da D.I no cadastro do porto seja Simplificada
            $baseICMS  = "T055_Valor_Base_ICMS_Normal";
            $valorICMS = "T055_Valor_ICMS_Presumido";
        } else {
            $baseICMS  = "";
            $valorICMS = "";
        }

        $array = array();        
        $array['baseICMS']  = $baseICMS;
        $array['valorICMS'] = $valorICMS;

        return $array;
    }

     /**
     * retornaFreteTotal
     *
     * @param String $T055_Valor_Total_Frete
     * @return array
     */

    public function retornaFreteTotal($T055_Valor_Total_Frete)
    {        
        // recebe a soma do T055_Valor_Total_Frete
        $array = array();
        $array['valorTotalFrete'] = 0;

        return $array;
    }
    
     /**
     * retornaFrete
     *
     * @param String $T055_Valor_Total_Frete
     * @return array
     */

    public function retornaFrete($T055_Valor_Total_Frete)
    {     
        $array = array();
        $array['valorFrete'] = 0;

        return $array;
    }

     /**
     * gatilhoAntesInsertT007
     *
     * @param array $mT055 $mT075
     * @return array
     */

     public function gatilhoAntesInsertT007($mT055_T075)
     { 
        global $g;

        $array = array();
        $array['T055_Valor_Frete'] = 0;

        return $array;
     }

    /**
     * gatilhoAntesInsertT008
     *
     * @param array $mT055
     * @return array
     */

     public function gatilhoAntesInsertT008($mT055)
     { 
        global $g;

        $array = array();
        $array['T055_Valor_Total_Frete']              = 0;
        $array['T055_Valor_Total_Despesas_Aduaneira'] = 0;
 
        return $array;
     }

    /**
     * retornaFrete
     *
     * @param String $D053_Percentual_FCP $D006_Flag_Calcula_FCP $baseICMS
     * @return array
     */
    public function retornaFCP($D053_Percentual_FCP, $D006_Flag_Calcula_FCP, $baseICMS)
    {     
        $array = array();
        $array['aliquotaFCP'] = 0;
        $array['baseFCP']     = 0;
        $array['valorFCP']    = 0;
        return $array;
    }

    /**
     * retornaAliquotas
     *
     * @param int $D009_Id
     */
    public function retornaAliquotas($D009_Id, $T293_Id = false, $T295_Id = false, $T075_Id = false)
    {       
        global $g;
        $mProduto = array();      
        
        log("entrou no retornaAliquotas importação IMP003");
                           
        $cClausula = " select ";
        $cClausula .= " D149_Imposto_Importacao, ";
        $cClausula .= " D149_Importacao_IPI, ";            
        $cClausula .= " D149_Importacao_PIS, ";
        $cClausula .= " D149_Importacao_COFINS, ";       
        $cClausula .= " D005_Classificacao_Fiscal, ";    
        $cClausula .= " D005_Id ";     
        $cClausula .= " from D001 ";
        $cClausula .= " left join D049 on D001_Id=D049_D001_Id ";
        $cClausula .= " left join D009 on D049_Id=D009_D049_Id ";
        $cClausula .= " left join D005 on D005_Id=D001_D005_Id ";
        $cClausula .= " left join D149 on D005_Id=D149_D005_Id AND D149_C004_Id = {$g['empresaAtual']}";
        $cClausula .= " where D009_Id='{$D009_Id}' ";
        $Produto  = mysql_query($cClausula);
        $mProduto = mysql_fetch_array($Produto); 
        
        if($T293_Id > 0){
            
            $cabecalho = mysql_query("SELECT D053_Aliquota_ICMS_Importacao,
                                             D053_Aliquota_ICMS_Presumido_Importacao,
                                             D053_Percentual_Reducao_ICMS_Importacao,
                                             D053_Percentual_Reducao_ICMS_Presumido_Importacao,
                                             D053_Aliquota_ICMS_PIS_COFINS_Importacao,
                                             D053_Aliquota_ICMS_Credito_Presumido_Importacao
                                        FROM T293
                                   LEFT JOIN D118 ON D118_Id = T293_D118_Id
                                   LEFT JOIN D018 ON D018_UF = D118_Estado 
                                   LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id=T293_C004_Id 
                                       WHERE T293_Id = '{$T293_Id}'");
            $mCabecalho = mysql_fetch_array($cabecalho);

        }else if($T295_Id > 0){
            
            $cabecalho = mysql_query("SELECT D053_Aliquota_ICMS_Importacao,
                                             D053_Aliquota_ICMS_Presumido_Importacao,
                                             D053_Percentual_Reducao_ICMS_Importacao,
                                             D053_Percentual_Reducao_ICMS_Presumido_Importacao,
                                             D053_Aliquota_ICMS_PIS_COFINS_Importacao,
                                             D053_Aliquota_ICMS_Credito_Presumido_Importacao
                                        FROM T295
                                   LEFT JOIN D118 ON D118_Id = T295_D118_Id
                                   LEFT JOIN D018 ON D018_UF = D118_Estado 
                                   LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id=T295_C004_Id 
                                       WHERE T295_Id = '{$T295_Id}'");
            $mCabecalho = mysql_fetch_array($cabecalho);

        }else if($T075_Id > 0){
           
            $cabecalho = mysql_query("SELECT D053_Aliquota_ICMS_Importacao,
                                             D053_Aliquota_ICMS_Presumido_Importacao,
                                             D053_Percentual_Reducao_ICMS_Importacao,
                                             D053_Percentual_Reducao_ICMS_Presumido_Importacao,
                                             D053_Aliquota_ICMS_PIS_COFINS_Importacao,
                                             D053_Aliquota_ICMS_Credito_Presumido_Importacao
                                        FROM T075
                                   LEFT JOIN D118 ON D118_Id = T075_D118_Id
                                   LEFT JOIN D018 ON D018_UF = D118_Estado 
                                   LEFT JOIN D053 ON D053_D018_Id=D018_Id AND D053_C004_Id=T075_C004_Id 
                                       WHERE T075_Id = '{$T075_Id}'");
            $mCabecalho = mysql_fetch_array($cabecalho);
        }

        // Definindo o array de retorno
        $array = array();
        $array['Imposto_Importacao']                           = $mProduto['D149_Imposto_Importacao'];
        $array['Aliquota_IPI']                                 = $mProduto['D149_Importacao_IPI'];
        $array['Aliquota_PIS']                                 = $mProduto['D149_Importacao_PIS'];
        $array['Aliquota_COFINS']                              = $mProduto['D149_Importacao_COFINS'];
        $array['D005_Id']                                      = $mProduto['D005_Id'];
        $array['D005_Classificacao_Fiscal']                    = $mProduto['D005_Classificacao_Fiscal'];
        $array['Aliquota_ICMS_Importacao']                     = $mCabecalho['D053_Aliquota_ICMS_Importacao'];
        $array['Aliquota_ICMS_Presumido_Importacao']           = $mCabecalho['D053_Aliquota_ICMS_Presumido_Importacao'];
        $array['Percentual_Reducao_ICMS_Importacao']           = $mCabecalho['D053_Percentual_Reducao_ICMS_Importacao'];
        $array['Percentual_Reducao_ICMS_Presumido_Importacao'] = $mCabecalho['D053_Percentual_Reducao_ICMS_Presumido_Importacao'];
        $array['Aliquota_ICMS_PIS_COFINS_Importacao']          = $mCabecalho['D053_Aliquota_ICMS_PIS_COFINS_Importacao'];
        $array['Aliquota_ICMS_Credito_Presumido_Importacao']   = $mCabecalho['D053_Aliquota_ICMS_Credito_Presumido_Importacao'];

        return $array;
    }

    /**
     * imp003AtualizarPesoProdutos
     *
     * @param int $T075_Id
     */
    public function imp003AtualizarPesoProdutos($T075_Id)
    {       
        global $g;
        
        $T055 = mysql_query("SELECT D001_Id,
                                    T055_Peso_Kg_Liquido_Unitario 
                               FROM T055
                          LEFT JOIN D009 ON D009_Id = T055_D009_Id
                          LEFT JOIN D049 ON D049_Id = D009_D049_Id
                          LEFT JOIN D001 ON D001_Id = D049_D001_Id
                              WHERE T055_T075_Id =  '{$T075_Id}'"); 
        
        while($mT055 = mysql_fetch_array($T055)){
        
            mysql_query("UPDATE D001 SET D001_Peso_Unitario_Kg    = '{$mT055['T055_Peso_Kg_Liquido_Unitario']}',
                                         D001_Peso_Unitario_Bruto = '{$mT055['T055_Peso_Kg_Liquido_Unitario']}'
                                   WHERE D001_Id = '{$mT055['D001_Id']}'");

            $error = mysql_error();
            if(!empty($error)){
                return $error;
            }
        }

        return true;
    }

    /**
     * imp003AtualizarDescricaoProdutos
     *
     * @param int $T075_Id
     */
    public function imp003AtualizarDescricaoProdutos($T075_Id)
    {       
        global $g;
        
        $T055 = mysql_query("SELECT D001_Id,
                                    T055_Descricao_Produto_Ingles 
                               FROM T055
                          LEFT JOIN D009 ON D009_Id = T055_D009_Id
                          LEFT JOIN D049 ON D049_Id = D009_D049_Id
                          LEFT JOIN D001 ON D001_Id = D049_D001_Id
                              WHERE T055_T075_Id =  '{$T075_Id}'"); 
        
        while($mT055 = mysql_fetch_array($T055)){
            
            mysql_query("UPDATE D001 SET D001_Descricao_Ingles = '{$mT055['T055_Descricao_Produto_Ingles']}'
                                   WHERE D001_Id = '{$mT055['D001_Id']}'");

            $error = mysql_error();
            if(!empty($error)){
                return $error;
            }
        }

        return true;
    }

    /**
     * imp003AtualizarCustoME
     *
     * @param int $T075_Id
     */
    public function imp003AtualizarCustoME($T075_Id)
    {       
        global $g;
        $T055 = mysql_query("SELECT T055_D009_Id,
                                    T055_Valor_Custo_Unitario 
                               FROM T055
                              WHERE T055_T075_Id =  '{$T075_Id}'"); 
        
        while($mT055 = mysql_fetch_array($T055)){

            mysql_query("UPDATE D009 SET D009_Valor_Custo_Importado = '{$mT055['T055_Valor_Custo_Unitario']}'
                                   WHERE D009_Id = '{$mT055['T055_D009_Id']}'");

            $error = mysql_error();
            if(!empty($error)){
                return $error;
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
    public function prepararImportarXML($arquivo, $incluirItens, $T075_Id)
    {
        global $g;
        $mArquivo          = $arquivo;
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
                        $tmp = $this->ImportarXML($zipArquivo);
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
            return $this->ImportarXML($arquivo, $incluirItens, $T075_Id);
        }
    }
    
    /**
     * ImportarXML
     *
     * @param string $arquivo
     * @param boolean $incluirItens
     * @return true/string
     */
    public function ImportarXML($arquivo, $incluirItens, $T075_Id)
    {
        global $g;

        $mArquivo      = $arquivo;
        $cArquivo      = $mArquivo["name"];
        $cTipo_Arquivo = substr($cArquivo, strrpos($cArquivo, ".") + 1, 3);
        $cTipo_Arquivo = StrToLower($cTipo_Arquivo);
        
        if (empty($cArquivo)) {
            return "SELECIONE O ARQUIVO";
        }
        
        if ($cTipo_Arquivo != 'xml') {
            return "O ARQUIVO DEVE SER NO FORMATO XML";
        }

        //-- ABRE O ARQUIVO XML
        $Ponteiro      = fopen($mArquivo["tmp_name"], "r");
        $cConteudo_XML = fread($Ponteiro, filesize($mArquivo["tmp_name"]));
        
        $T075_Arquivo_XML = addslashes($cConteudo_XML);
        
        //-- INSTANCIA A CLASSE "DOM" DO PHP PARA LER OS "NOS" DO XML
        $dom = new \DomDocument;
        $dom->loadXML($cConteudo_XML);
        $declaracaoImportacao          = $dom->getElementsByTagName("declaracaoImportacao")->item(0);

        //-- INICIA UMA TRANSACÃO NO BANCO DE DADOS
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        // Dados do cabeçalho da DI
        $localEmbarqueTotalReais       = $this->trataNumeros($dom->getElementsByTagName("localEmbarqueTotalReais")->item(0)->nodeValue,-2);
        $localEmbarqueTotalDolares     = $this->trataNumeros($dom->getElementsByTagName("localEmbarqueTotalDolares")->item(0)->nodeValue,-2);  #T075_Valor_VMLE
        $cotacaoME                     = $localEmbarqueTotalReais / $localEmbarqueTotalDolares;                                                #T075_Cotacao_ME
        $localDescargaTotalDolares     = $this->trataNumeros($dom->getElementsByTagName("localDescargaTotalDolares")->item(0)->nodeValue,-2);  #T075_Valor_VMLD
        $numeroDI                      = $dom->getElementsByTagName("numeroDI")->item(0)->nodeValue;                                           #T075_Numero_DI                   
        $importadorNumero              = $dom->getElementsByTagName("importadorNumero")->item(0)->nodeValue;                                   #T075_CNPJ_Importador 
        $freteTotalDolares             = $this->trataNumeros($dom->getElementsByTagName("freteTotalDolares")->item(0)->nodeValue,-2);          #T075_Valor_Frete 
        $freteTotalReais               = $this->trataNumeros($dom->getElementsByTagName("freteTotalReais")->item(0)->nodeValue,-2);  
        $freteTotalMoeda               = $this->trataNumeros($dom->getElementsByTagName("freteTotalMoeda")->item(0)->nodeValue,-4);           
        $conhecimentoCargaEmbarqueData = $dom->getElementsByTagName("conhecimentoCargaEmbarqueData")->item(0)->nodeValue;                      #T075_Data_Embarque
        $dataRegistro                  = $dom->getElementsByTagName("dataRegistro")->item(0)->nodeValue;                                       #T075_Data_Emissao_DI
        $cargaPesoBruto                = $this->trataNumeros($dom->getElementsByTagName("cargaPesoBruto")->item(0)->nodeValue,-5);             #T075_Peso_Bruto
        $cargaPesoLiquido              = $this->trataNumeros($dom->getElementsByTagName("cargaPesoLiquido")->item(0)->nodeValue,-5);           #T075_Peso_Liquido
        $seguroTotalDolares            = $this->trataNumeros($dom->getElementsByTagName("seguroTotalDolares")->item(0)->nodeValue,-2);         #T075_Valor_Seguro
        $seguroTotalReais              = $this->trataNumeros($dom->getElementsByTagName("seguroTotalReais")->item(0)->nodeValue,-2);           
        $seguroTotalMoedaNegociada     = $this->trataNumeros($dom->getElementsByTagName("seguroTotalMoedaNegociada")->item(0)->nodeValue,-2);  

        //-- Verifica se ja existe o XML no SERVIDOR     
        $cCaminho_Destino = $g['pathDadosAntigo'] . "/di/xml/recebido/" . $numeroDI . ".xml";

        if (file_exists($cCaminho_Destino)) {
            mysql_query("ROLLBACK");
            return "Já existe este XML no servidor, não pode ser importado novamente.";
        }
       
        //-- VAI VERIFICAR SE ESTÁ PARA INCLUIR OS ITENS E VAI FAZER A BUSCA DO PRODUTO, SE NÃO ENCONTRAR NÃO IMPORTA O XML
        if($incluirItens == 'on'){
            
            // -- VAI VERIFICAR SE VAI FAZER INSERT DOS ITENS NA DI OU NA TABELA T310
            // -- SE NÃO TIVER ITENS FAZ O INSERT NA DI (T055) E SE JA TIVER ITENS NA DI FAZ INSERT NA T310 (ITENS DO XML)
            $possuiProdutoNaDI = false;
            $T055 = mysql_query("SELECT T055_Id FROM T055 WHERE T055_T075_Id = '{$T075_Id}'");
            if(mysql_num_rows($T055) > 0 ){
                $possuiProdutoNaDI = true;
            }

            $T075 = mysql_query("SELECT T075_D024_Id FROM T075 WHERE T075_Id = '{$T075_Id}'");
            $mT075 = mysql_fetch_array($T075);

            //-- PEGA O TOTAL DE ADIÇÕES
            $adicoes       = $dom->getElementsByTagName("totalAdicoes")->item(0)->nodeValue;  
            $errosNaAdicao = '';
            for ($x = 0; $x < $adicoes; $x++) {
                
                $adicao = $dom->getElementsByTagName("adicao")->item($x); 

                //-- OS DADOS DA ADIÇÃO VALE PARA TODOS OS PRODUTOS 
                $numeroAdicao              = $adicao->getElementsByTagName("numeroAdicao")->item(0)->nodeValue;                                      #T055_Valor_Adicao
                $cofinsAliquotaAdValorem   = $this->trataNumeros($adicao->getElementsByTagName("cofinsAliquotaAdValorem")->item(0)->nodeValue,-2);   #T055_Aliquota_COFINS
                $iiAliquotaAdValorem       = $this->trataNumeros($adicao->getElementsByTagName("iiAliquotaAdValorem")->item(0)->nodeValue,-2);       #T055_Aliquota_Imposto_Importacao
                $ipiAliquotaAdValorem      = $this->trataNumeros($adicao->getElementsByTagName("ipiAliquotaAdValorem")->item(0)->nodeValue,-2);      #T055_Aliquota_IPI
                $pisPasepAliquotaAdValorem = $this->trataNumeros($adicao->getElementsByTagName("pisPasepAliquotaAdValorem")->item(0)->nodeValue,-2); #T055_Aliquota_PIS    
                $dadosMercadoriaCodigoNcm  = $adicao->getElementsByTagName("dadosMercadoriaCodigoNcm")->item(0)->nodeValue; 

                //-- PROCURA O ID DA NCM DA ADIÇÃO
                $D005 = mysql_query("SELECT D005_Id 
                                       FROM D005 
                                      WHERE D005_Classificacao_Fiscal = '{$dadosMercadoriaCodigoNcm}'");
                $mD005 = mysql_fetch_array($D005); 
                
                //-- PEGA O TOTAL DE ITENS 
                $mercadoria       = $adicao->getElementsByTagName("mercadoria");   
                $totalItensAdicao = 0;

                foreach($mercadoria as $total){
                    $totalItensAdicao++;
                }

                //-- BUSCA AS ALIQUOTA DE ICMS QUE NÃO VEM NA DI (D.I PRECISA ESTAR COM O PORTO PARA BUSCAR AS ALIQUOTAS) 
                $icms = $this->retornaAliquotas(0, false, false, $T075_Id);

                for ($y = 0; $y < $totalItensAdicao; $y++) {

                    //-- ACESSA OS DADOS DE CADA ITEM DA ADIÇÃO
                    $descricaoMercadoria  = $mercadoria->item($y)->getElementsByTagName("descricaoMercadoria")->item(0)->nodeValue; 
                    $descricaoMercadoria  = str_replace("'","",$descricaoMercadoria);
                    $numeroSequencialItem = $mercadoria->item($y)->getElementsByTagName("numeroSequencialItem")->item(0)->nodeValue;                  #T055_Adicao_Item
                    $quantidade           = $this->trataNumeros($mercadoria->item($y)->getElementsByTagName("quantidade")->item(0)->nodeValue,-5);    #T055_Quantidade
                    $valorUnitario        = $this->trataNumeros($mercadoria->item($y)->getElementsByTagName("valorUnitario")->item(0)->nodeValue,-7); #T055_Valor_Custo_Unitario

                    //-- FUNCÃO QUE PROCURA O ITEM, É PERSONALIZADA POR CLIENTE
                    $item = $this->procuraCadastroItem($descricaoMercadoria);

                    if($possuiProdutoNaDI === false){ 

                        //-- VERIFICA SE É ARRAY DA FUNCAO procuraCadastroItem 
                        //-- FUNCÃO É PERSONALIZADA POR CLIENTE
                        if(!is_array($item)){
                            //Se não estiver personalizado a funcao, vai incluir os itens com os dados da propria DI
                            $D037_Id            = 0;
                            $D009_Id            = 0; 
                            $codigoProduto      = '';  
                            $descricaoProduto   = $descricaoMercadoria;   
                            $pesoUnitario       = 0; 
                            $larguraProduto     = 0;    
                            $alturaProduto      = 0;
                            $comprimentoProduto = 0;                   
                        } else {
                            $D037_Id            = $item['D037_Id'];
                            $D009_Id            = $item['D009_Id']; 
                            $codigoProduto      = $item['D001_Codigo_Produto'];  
                            $descricaoProduto   = $item['D001_Descricao_Produto'];   
                            $pesoUnitario       = $item['D001_Peso_Unitario_Kg']; 
                            $larguraProduto     = $item['D001A_Largura'];    
                            $alturaProduto      = $item['D001A_Altura']; 
                            $comprimentoProduto = $item['D001A_Comprimento'];                   
                        }

                        mysql_query("INSERT INTO T055 (T055_T075_Id,
                                                        T055_C004_Id,
                                                        T055_D005_Id,
                                                        T055_D037_Id,
                                                        T055_D009_Id,
                                                        T055_D024_Id,
                                                        T055_Valor_Adicao,
                                                        T055_Codigo_Produto,
                                                        T055_Descricao_Produto,
                                                        T055_Quantidade,
                                                        T055_Valor_Custo_Unitario,
                                                        T055_Adicao_Item,
                                                        T055_Aliquota_COFINS,
                                                        T055_Aliquota_Imposto_Importacao,
                                                        T055_Aliquota_IPI,
                                                        T055_Aliquota_PIS,
                                                        T055_Peso_Kg_Liquido_Unitario,
                                                        T055_Aliquota_ICMS,
                                                        T055_Aliquota_ICMS_Presumido,
                                                        T055_Percentual_Reducao_ICMS,
                                                        T055_Percentual_Reducao_ICMS_Presumido,
                                                        T055_Aliquota_ICMS_PIS_COFINS,
                                                        T055_Aliquota_ICMS_Credito_Presumido
                                            ) VALUES (
                                                        '{$T075_Id}',
                                                        '{$g['empresaAtual']}',
                                                        '{$mD005['D005_Id']}',
                                                        '{$D037_Id}',
                                                        '{$D009_Id}',
                                                        '{$mT075['T075_D024_Id']}',
                                                        '{$numeroAdicao}',
                                                        '{$codigoProduto}',
                                                        '{$descricaoProduto}',
                                                        '{$quantidade}',
                                                        '{$valorUnitario}',
                                                        '{$numeroSequencialItem}',
                                                        '{$cofinsAliquotaAdValorem}',
                                                        '{$iiAliquotaAdValorem}',
                                                        '{$ipiAliquotaAdValorem}',
                                                        '{$pisPasepAliquotaAdValorem}',
                                                        '{$pesoUnitario}',
                                                        '{$icms['Aliquota_ICMS_Importacao']}',
                                                        '{$icms['Aliquota_ICMS_Presumido_Importacao']}',
                                                        '{$icms['Percentual_Reducao_ICMS_Importacao']}',
                                                        '{$icms['Percentual_Reducao_ICMS_Presumido_Importacao']}',
                                                        '{$icms['Aliquota_ICMS_PIS_COFINS_Importacao']}',
                                                        '{$icms['Aliquota_ICMS_Credito_Presumido_Importacao']}')");
                        $error = mysql_error();
                        if(!empty($error)){
                            mysql_query("ROLLBACK");
                            return $error;
                        }  

                        $T055_Id = $g['mysqlLastId'];    
                        mysql_query("INSERT INTO T055A (T055A_T055_Id,
                                                        T055A_Descricao_Produto_Fornecedor,
                                                        T055A_Flag_Item_Importado_XML,
                                                        T055A_Largura,
                                                        T055A_Altura,
                                                        T055A_Comprimento) 
                                               VALUES ('{$T055_Id}',
                                                       '{$descricaoProduto}',
                                                       'S',
                                                       '{$larguraProduto}',
                                                       '{$alturaProduto}',
                                                       '{$comprimentoProduto}')"); 
                        $error = mysql_error();
                        if(!empty($error)){
                            mysql_query("ROLLBACK");
                            return $error;
                        }     

                    } else {    

                        mysql_query("INSERT INTO T310 (T310_T075_Id,
                                                        T310_Valor_Adicao,
                                                        T310_Adicao_Item,
                                                        T310_Descricao_Produto,
                                                        T310_Quantidade,
                                                        T310_Valor_Custo_Unitario,
                                                        T310_Aliquota_COFINS,
                                                        T310_Aliquota_Imposto_Importacao,
                                                        T310_Aliquota_IPI,
                                                        T310_Aliquota_PIS,
                                                        T310_Classificacao_Fiscal
                                             ) VALUES (
                                                        '{$T075_Id}',
                                                        '{$numeroAdicao}',
                                                        '{$numeroSequencialItem}',
                                                        '{$descricaoMercadoria}',
                                                        '{$quantidade}',
                                                        '{$valorUnitario}',
                                                        '{$cofinsAliquotaAdValorem}',
                                                        '{$iiAliquotaAdValorem}',
                                                        '{$ipiAliquotaAdValorem}',
                                                        '{$pisPasepAliquotaAdValorem}',
                                                        '{$dadosMercadoriaCodigoNcm}')");
                        $error = mysql_error();
                        if(!empty($error)){
                            mysql_query("ROLLBACK");
                            return $error;
                        } 

                        $T310_Id = $g['mysqlLastId'];

                        //-- VERIFICA SE É ARRAY DA FUNCAO procuraCadastroItem 
                        //-- FUNCÃO É PERSONALIZADA POR CLIENTE
                        if(is_array($item)){

                            $D009_Id = $item['D009_Id']; 
                            $T055 = mysql_query("SELECT T055_Id 
                                                   FROM T055 
                                                  WHERE T055_T075_Id = '{$T075_Id}'
                                                    AND T055_D009_Id = '{$D009_Id}'");

                            $mT055 = mysql_fetch_array($T055);
                           
                            if($mT055['T055_Id'] > 0){

                                // Vincula o item da DI T055 com a tabela T310 (itens XML DI)
                                mysql_query("UPDATE T310 SET T310_T055_Id = '{$mT055['T055_Id']}'
                                                    WHERE T310_Id = '{$T310_Id}'");

                                $error = mysql_error();
                                if(!empty($error)){
                                    mysql_query("ROLLBACK");
                                    return $error;
                                } 

                                // Atualiza dados do T055 com os dados da tabela T310 (itens XML DI)
                                mysql_query("UPDATE T055 
                                          LEFT JOIN T310 ON T055_Id = T310_T055_Id
                                                SET T055_Valor_Adicao                = T310_Valor_Adicao,
                                                    T055_Adicao_Item                 = T310_Adicao_Item,
                                                    T055_Aliquota_Imposto_Importacao = T310_Aliquota_Imposto_Importacao,
                                                    T055_Aliquota_IPI                = T310_Aliquota_IPI,
                                                    T055_Aliquota_PIS                = T310_Aliquota_PIS,
                                                    T055_Aliquota_COFINS             = T310_Aliquota_COFINS
                                            WHERE T055_Id = '{$mT055['T055_Id']}'");
                                            
                                $error = mysql_error();
                                if(!empty($error)){
                                    mysql_query("ROLLBACK");
                                    return $error;
                                } 
                            }
                        } 
                    }
                }                                        
            }
        }
       
        $icms                          = $dom->getElementsByTagName("icms")->item(0);
        $pagamento                     = $dom->getElementsByTagName("pagamento");   

        //-- VERIFICA O TOTAL DE TAGS DE PAGAMENTO
        $totalTagPagamento = 0;
        foreach($pagamento as $total){
            $totalTagPagamento++;
        }

        $valorTotalSiscomex = 0;
        $valorTotalIPI      = 0;
        $valorTotalCofins   = 0;
        $valorTotalPIS      = 0;
        $valorTotalII       = 0;

        //-- CÓDIGOS DE PAGAMENTO DA RECEITA FEDERAL 
        //-- 7811 - SISCOMEX 
        //-- 1038 - IPI
        //-- 5629 - COFINS
        //-- 5602 - PIS
        //-- 0086 - IMPOSTO IMPORTAÇÃO
        //------------------------------------------
        for ($x = 0; $x < $totalTagPagamento; $x++) {
            $codigoReceita     = $pagamento->item($x)->getElementsByTagName("codigoReceita")->item(0)->nodeValue;
            $valorReceita      = $this->trataNumeros($pagamento->item($x)->getElementsByTagName("valorReceita")->item(0)->nodeValue,-2); 
            $dataPagamento     = $pagamento->item($x)->getElementsByTagName("dataPagamento")->item(0)->nodeValue;
            $dataPagamento     = substr($dataPagamento,0,4).'-'.substr($dataPagamento,4,2).'-'.substr($dataPagamento,6,2);

            // Sempre vai entrar como false no for porque é escolhido por imposto se vai lançar ou não
            $geraContaPagar    = false;

            if($codigoReceita == '7811'){
                
                $valorTotalSiscomex = $valorReceita;
                $geraContaPagar     = ($g['C031']['geraContaPagarSiscomexAoImportarXml'] == 'S') ? true : false;
                $conta              = $g['C031']['contaXmlContasPagarSiscomex'];  

            }elseif($codigoReceita == '1038'){

                $valorTotalIPI  = $valorReceita;
                $geraContaPagar = ($g['C031']['geraContaPagarIPIAoImportarXml'] == 'S') ? true : false;
                $conta          = $g['C031']['contaXmlContasPagarIPI'];               

            }elseif($codigoReceita == '5629'){

                $valorTotalCofins = $valorReceita;
                $geraContaPagar   = ($g['C031']['geraContaPagarCofinsAoImportarXml'] == 'S') ? true : false;
                $conta            = $g['C031']['contaXmlContasPagarCofins'];               

            }elseif($codigoReceita == '5602'){

                $valorTotalPIS  = $valorReceita;
                $geraContaPagar = ($g['C031']['geraContaPagarPISAoImportarXml'] == 'S') ? true : false;
                $conta          = $g['C031']['contaXmlContasPagarPIS'];               

            }elseif($codigoReceita == '0086'){

                $valorTotalII   = $valorReceita;
                $geraContaPagar = ($g['C031']['geraContaPagarIIAoImportarXml'] == 'S') ? true : false;
                $conta          = $g['C031']['contaXmlContasPagarII'];   

            } 

            if($geraContaPagar === true){

                $cClausula = " INSERT INTO T015 ( ";
                $cClausula .= " T015_D024_Id, "; 
                $cClausula .= " T015_T075_Id, "; 
                $cClausula .= " T015_C004_Id, ";  
                $cClausula .= " T015_D014_Id, ";  
                $cClausula .= " T015_D032_Id, ";  
                $cClausula .= " T015_D031_Id, ";  
                $cClausula .= " T015_D073_Id, ";  
                $cClausula .= " T015_Nome_Cliente, "; 
                $cClausula .= " T015_Numero_Duplicata, ";  
                $cClausula .= " T015_Numero_Documento, ";  
                $cClausula .= " T015_Data_Emissao, "; 
                $cClausula .= " T015_Data_Vencimento, "; 
                $cClausula .= " T015_Valor_Duplicata, ";  
                $cClausula .= " T015_Valor_Total, "; 
                $cClausula .= " T015_Observacao, ";  
                $cClausula .= " T015_Data_Lancamento, ";                 
                $cClausula .= " T015_Hora_Lancamento "; 
                $cClausula .= " ) values ( ";
                $cClausula .= " '{$g['C031']['fornecedorXmlContasPagar']}', "; 
                $cClausula .= " '{$T075_Id}', ";             
                $cClausula .= " '{$g['empresaAtual']}', ";  
                $cClausula .= " '{$conta}', ";  
                $cClausula .= " '{$g['C031']['grupoXmlContasPagarDi']}', ";  
                $cClausula .= " '{$g['C031']['portardorXmlContasPagar']}', ";  
                $cClausula .= " '{$g['C031']['centroCustosXmlContasPagar']}', "; 
                $cClausula .= " '{$g['C031']['clientefornecedorXmlContasPagar']}', "; 
                $cClausula .= " 'DI {$numeroDI}', ";  
                $cClausula .= " 'DI {$numeroDI}', ";  
                $cClausula .= " current_date(), "; 
                $cClausula .= " '{$dataPagamento}', "; 
                $cClausula .= " '{$valorReceita}', "; 
                $cClausula .= " '{$valorReceita}', "; 
                $cClausula .= " 'LANÇADO AUTOMÁTICAMENTE AO IMPORTAR XML DA D.I, CÓDIGO DA CONTA NA RECEITA: {$codigoReceita}', "; 
                $cClausula .= " current_date(), "; 
                $cClausula .= " current_time() ";
                $cClausula .= " ) ";

                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    $retorno = "Clausula: " . $cClausula;
                    $retorno .= "ERRO: " . $cErro;
                    mysql_query("ROLLBACK");
                    return $retorno;
                }           
            }
        }
        
        if(!empty($icms)){
            $valorTotalIcms = $this->trataNumeros($icms->getElementsByTagName("valorTotalIcms")->item(0)->nodeValue,-2);
        }                                                             

        // trata o número da DI
        $numeroDI                      = substr($numeroDI,0,2).'/'. substr($numeroDI,2,7).'-'.substr($numeroDI,-1);
        //trata o cnpj do importador
        $importadorNumero              = substr($importadorNumero,0,2).'.'.substr($importadorNumero,2,3).'.'.substr($importadorNumero,5,3).'/'.substr($importadorNumero,8,4).'-'.substr($importadorNumero,12,2);
        // trata a data de embarque
        $conhecimentoCargaEmbarqueData = substr($conhecimentoCargaEmbarqueData,0,4).'-'.substr($conhecimentoCargaEmbarqueData,4,2).'-'.substr($conhecimentoCargaEmbarqueData,6,2);
        // trata a data de emissao da DI
        $dataRegistro                  = substr($dataRegistro,0,4).'-'.substr($dataRegistro,4,2).'-'.substr($dataRegistro,6,2);
    
        //-- VERIFICA SE O CNPJ QUE ESTA NO XML E O MESMO DA EMPRESA USUARIA
        $C004  = mysql_query("SELECT C004_Cnpj FROM C004 WHERE C004_Id='{$g['empresaAtual']}'");
        $mC004 = mysql_fetch_array($C004);
        
        if ($mC004['C004_Cnpj'] != $importadorNumero) {
            mysql_query("ROLLBACK");
            return 'CNPJ IMPORTARDOR DO XML NÃO É O MESMO CNPJ CADASTRADO NA EMPRESA\n\nXML: ' . $importadorNumero . '\nEMPRESA : ' . $mC004['C004_Cnpj'];
        } 

        $T075 = mysql_query("SELECT T075_Numero_DI, T075_Modelo_Calculos FROM T075 WHERE T075_Id = '{$T075_Id}'");
        $mT075 = mysql_fetch_array($T075);

        if(!empty($mT075['T075_Numero_DI']) && ($numeroDI != $mT075['T075_Numero_DI'])){
            mysql_query("ROLLBACK");
            return 'O NUMERO DA DI NO XML NÃO É O MESMO PREENHECIDO NO CABEÇALHO:\n\nXML: ' . $numeroDI . '\nCABEÇALHO: ' . $mT075['T075_Numero_DI'];
        }

        // UPDATE NO CABEÇALHO DA DI
        mysql_query("UPDATE T075 SET T075_Arquivo_XML              = '{$T075_Arquivo_XML}', 
                                     T075_Valor_VMLE               = '{$localEmbarqueTotalDolares}', 
                                     T075_Valor_VMLD               = '{$localDescargaTotalDolares}',
                                     T075_Cotacao_ME               = '{$cotacaoME}',
                                     T075_Numero_DI                = '{$numeroDI}',
                                     T075_CNPJ_Importador          = '{$importadorNumero}', 
                                     T075_Valor_Frete              = '{$freteTotalDolares}', 
                                     T075_Data_Embarque            = '{$conhecimentoCargaEmbarqueData}', 
                                     T075_Peso_Bruto               = '{$cargaPesoBruto}',
                                     T075_Peso_Liquido             = '{$cargaPesoLiquido}', 
                                     T075_Valor_COFINS             = '{$valorTotalCofins}', 
                                     T075_Valor_Imposto_Importacao = '{$valorTotalII}',
                                     T075_Valor_IPI                = '{$valorTotalIPI}', 
                                     T075_Valor_PIS                = '{$valorTotalPIS}', 
                                     T075_Valor_ICMS               = '{$valorTotalIcms}', 
                                     T075_Valor_SISCOMEX           = '{$valorTotalSiscomex}',
                                     T075_Valor_Seguro             = '{$seguroTotalDolares}',
                                     T075_Data_Emissao_DI          = '{$dataRegistro}'
                               WHERE T075_Id                       = '{$T075_Id}'");
        
        $error = mysql_error();
        if(!empty($error)){
            mysql_query("ROLLBACK");
            return $error;
        }   

        if($mT075['T075_Modelo_Calculos'] == '2'){
            //T168_Tipo_Despesa = 2 THC (Não encontrado a tag no XML)
            //T168_Tipo_Despesa = 3 Seguro
            //T168_Tipo_Despesa = 4 Siscomex
            //T168_Tipo_Despesa = 5 AFRMM (Não encontrado a tag no XML)
            //T168_Tipo_Despesa = 6 Frete

            $T168 = mysql_query("SELECT T168_Id,
                                        T168_Tipo_Despesa,
                                        T168_Flag_ME
                                   FROM T168
                                  WHERE T168_T075_Id = '{$T075_Id}'
                                    AND T168_Tipo_Despesa != '1'");

            if(mysql_num_rows($T168) > 0){
                
                while($mT168 = mysql_fetch_array($T168)){
                    
                    if($mT168['T168_Tipo_Despesa'] == '3'){

                        $cotacaoSeguro = ($mT168['T168_Flag_ME'] == 'S') ? $seguroTotalMoedaNegociada : 0 ;

                        mysql_query("UPDATE T168 SET T168_Valor_Confirmado = '{$seguroTotalReais}',
                                                     T168_Cotacao_ME       = '{$cotacaoSeguro}'
                                               WHERE T168_Id = '{$mT168['T168_Id']}'");
                    }

                    if($mT168['T168_Tipo_Despesa'] == '4'){

                        mysql_query("UPDATE T168 SET T168_Valor_Confirmado = '{$valorTotalSiscomex}'
                                               WHERE T168_Id = '{$mT168['T168_Id']}'");
                    }

                    if($mT168['T168_Tipo_Despesa'] == '6'){

                        $cotacaoFrete = ($mT168['T168_Flag_ME'] == 'S') ? $freteTotalMoeda : 0 ;

                        mysql_query("UPDATE T168 SET T168_Valor_Confirmado = '{$freteTotalReais}',
                                                     T168_Cotacao_ME       = '{$cotacaoFrete}'
                                               WHERE T168_Id = '{$mT168['T168_Id']}'");
                    }                    
                }
            }
        }

        // Chama o gravar totalização da DI
        $this->Calcular($T075_Id);

        // Deu tudo certo, grava o XML no servidor
        if (!is_dir("{$g['pathDadosAntigo']}/di/xml/recebido/")){
            mkdir("{$g['pathDadosAntigo']}/di/xml/recebido/", 0777, true);
        }
        if (isset($mArquivo["interno"]) || !move_uploaded_file($mArquivo["tmp_name"], $cCaminho_Destino)) {
            if (!copy($mArquivo["tmp_name"], $cCaminho_Destino)) {
                mysql_query("ROLLBACK");
                return "Nao foi possivel completar o upload do arquivo. Arquivo: ".$mArquivo["tmp_name"]." Caminho: ".$cCaminho_Destino;
            }
        } 

        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");

        return true;
    }

    /**
     * trataNumeros
     *
     * @param string $str
     * @param string $pos
     * @return true/string
     */
    public function trataNumeros($str, $pos){

        // retira 0 esquerda
        $numeroSemZeros = ltrim($str, "0");

        // Adicionar zeros à esquerda se necessário
        if ($pos < 0) {
            $numeroSemZeros = str_pad($numeroSemZeros, abs($pos) + strlen($numeroSemZeros), "0", STR_PAD_LEFT);
        }
    
        // Inserir o ponto decimal na posição desejada
        $parteInteira = substr($numeroSemZeros, 0, $pos);
        $parteDecimal = substr($numeroSemZeros, $pos);
    
        // Formatar o número resultante
        $numeroFormatado = "$parteInteira.$parteDecimal";
    
        // Remover zeros à esquerda do resultado
        $numeroFormatadoSemZeros = ltrim($numeroFormatado, "0");
    
        return $numeroFormatadoSemZeros;
    }  

    /**
     * procuraCadastroItem
     *
     * @param string $dadoItem
     * @return array
     */
    public function procuraCadastroItem($dadoItem){

        global $g;
        // Funcao personalizada por empresa

        return '';
        
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
        $T055_Id = $form->campoValorEnviado('T055_Id');

        $T055 = mysql_query("SELECT D009_Id,
                                    D001_Codigo_Produto,
                                    D001_Descricao_Produto,
                                    D001_Peso_Unitario_Kg,
                                    D037_Id,
                                    D001A_Largura,
                                    D001A_Altura,
                                    D001A_Comprimento
                               FROM D009
                          LEFT JOIN D049 ON D049_Id = D009_D049_Id
                          LEFT JOIN D001 ON D001_Id = D049_D001_Id
                          LEFT JOIN D001A ON D001_Id = D001A_D001_Id
                          LEFT JOIN D037 ON D037_Id = D001_D037_Id
                              WHERE D009_Id = '{$D009_Id}'");
        $mT055 = mysql_fetch_array($T055);
        if (mysql_num_rows($T055) <= 0) {
            return "Produto não foi encontrado.";
        }
        
        mysql_query("UPDATE T055 SET T055_D009_Id                  = '{$D009_Id}', 
                                     T055_C004_Id                  = '{$g['empresaAtual']}',
                                     T055_D037_Id                  = '{$mT055['D037_Id']}',
                                     T055_Codigo_Produto           = '{$mT055['D001_Codigo_Produto']}',
                                     T055_Descricao_Produto        = '{$mT055['D001_Descricao_Produto']}',
                                     T055_Peso_Kg_Liquido_Unitario = '{$mT055['D001_Peso_Unitario_Kg']}'
                               WHERE T055_Id='{$T055_Id}'");
        $error = mysql_error();
        if (!empty($error)) {
            return "Erro: Erro ao vincular item da nota fiscal.";
        }     
        
        $T055A = mysql_query("SELECT T055A_Id 
                                FROM T055A
                               WHERE T055A_T055_Id = '{$T055_Id}'");
        
        if(mysql_num_rows($T055A) <= 0){
            mysql_query("INSERT INTO T055A (T055A_T055_Id,
                                            T055A_Largura,
                                            T055A_Altura,
                                            T055A_Comprimento) 
                                    VALUES ('{$T055_Id}',
                                           '{$mT055['D001A_Largura']}',
                                           '{$mT055['D001A_Altura']}',
                                           '{$mT055['D001A_Comprimento']}')"); 

        } else {
            mysql_query("UPDATE T055A SET T055A_Largura     = '{$mT055['D001A_Largura']}',
                                          T055A_Altura      = '{$mT055['D001A_Altura']}',
                                          T055A_Comprimento = '{$mT055['D001A_Comprimento']}'
                                    WHERE T055A_T055_Id = '{$T055_Id}'");

        }
 
    /*** No momento não vamos realizar o vinculo do fornecedor no produto  
      require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        $CAD002->definirCodigoFornecedor($D009_Id, $fornecedor, '', '', false, true, $descricaoProduto);  
    ***/
        return true;
    }


    /**
     * incluirPacoteDespesas
     *
     * @param int $D190
     * @param int $T075_Id
     * @param boolean $excluirDespesas
     */
    public function incluirPacoteDespesas($D190_Id,$T075_Id,$excluirDespesas = false)
    {       
        global $g;
        
        if($excluirDespesas){
            mysql_query("DELETE FROM T168 WHERE T168_T075_Id = '{$T075_Id}'");
        }

        // Faz o select buscando o pacote
        $D192 = mysql_query("SELECT D192_D190_Id,
                                    D192_D191_Id,
                                    D191_Descricao_Despesa,
                                    D191_Tipo_Despesa,
                                    D191_Tipo,
                                    D192_Aliquota,
                                    D192_Flag_Tipo,
                                    D192_Valor,
                                    D192_Formula,
                                    D192_Ordem,
                                    D192_Flag_Soma_Base_IPI,
                                    D192_Flag_Soma_Base_ICMS,
                                    D192_Flag_Soma_Base_PIS,
                                    D192_Flag_Soma_Base_COFINS,
                                    D192_Flag_Soma_Base_II,
                                    D192_Flag_Soma_Custo,
                                    D192_Flag_Soma_Preco_NFe,
                                    D192_Flag_Tipo_Rateio,
                                    D192_Flag_Base_Rateio,
                                    D192_Flag_ME
                               FROM D192
                          LEFT JOIN D191 ON D192_D191_Id = D191_Id
                              WHERE D192_D190_Id = '{$D190_Id}'
                           ORDER BY D192_Ordem");

        while($mD192 = mysql_fetch_array($D192)){
 
            //faz o insert do pacote na DI
            mysql_query("INSERT INTO T168 (T168_T075_Id,
                                           T168_D190_Id,
                                           T168_Aliquota,
                                           T168_Flag_Tipo,
                                           T168_Descricao,
                                           T168_Valor,
                                           T168_Formula,
                                           T168_Ordem,
                                           T168_Flag_Soma_Base_IPI,
                                           T168_Flag_Soma_Base_ICMS,
                                           T168_Flag_Soma_Base_PIS,
                                           T168_Flag_Soma_Base_COFINS,
                                           T168_Flag_Soma_Base_II,
                                           T168_Flag_Soma_Custo,
                                           T168_Flag_Soma_Preco_NFe,
                                           T168_Flag_Tipo_Rateio,
                                           T168_Flag_Base_Rateio,
                                           T168_Flag_ME,
                                           T168_Tipo_Despesa,
                                           T168_Tipo,
                                           T168_Data
                                   )VALUES(
                                           '{$T075_Id}', 
                                           '{$D190_Id}',  
                                           '{$mD192['D192_Aliquota']}',
                                           '{$mD192['D192_Flag_Tipo']}',
                                           '{$mD192['D191_Descricao_Despesa']}',
                                           '{$mD192['D192_Valor']}',
                                           '{$mD192['D192_Formula']}',
                                           '{$mD192['D192_Ordem']}',
                                           '{$mD192['D192_Flag_Soma_Base_IPI']}',
                                           '{$mD192['D192_Flag_Soma_Base_ICMS']}',
                                           '{$mD192['D192_Flag_Soma_Base_PIS']}',
                                           '{$mD192['D192_Flag_Soma_Base_COFINS']}',
                                           '{$mD192['D192_Flag_Soma_Base_II']}',
                                           '{$mD192['D192_Flag_Soma_Custo']}',
                                           '{$mD192['D192_Flag_Soma_Preco_NFe']}',
                                           '{$mD192['D192_Flag_Tipo_Rateio']}',
                                           '{$mD192['D192_Flag_Base_Rateio']}',
                                           '{$mD192['D192_Flag_ME']}',
                                           '{$mD192['D191_Tipo_Despesa']}',
                                           '{$mD192['D191_Tipo']}',
                                           NOW())");

            $error = mysql_error();
            if(!empty($error)){
                return 'Houve um erro ao tentar incluir o pacote de depesas <br>'.$error;
            }
        } 

        return true;
    }

    /**
     * processarOrdemDespesa
     *
     * @param String $T168_Id
     * @return Bool
     */
    public function processarOrdemDespesa($T168_Id, $novoNumeroItem)
    {
        global $g;
        $contadorItem = 1;
        $sql = mysql_query("SELECT T168_Ordem, 
                                   T168_Id 
                              FROM T168 
                             WHERE T168_T075_Id = (SELECT T168_T075_Id FROM T168 WHERE T168_Id = '{$T168_Id}') 
                               AND T168_Id != '{$T168_Id}' 
                          ORDER BY T168_Ordem");
        while($row = mysql_fetch_array($sql)){
            if($contadorItem == $novoNumeroItem){ $contadorItem++; }
            mysqli_query("UPDATE T168 SET T168_Ordem = '{$contadorItem}' WHERE T168_Id = '{$row['T168_Id']}'");
            $contadorItem++;
        }
        return true;
    }

    /**
     * calcularDepesas
     *
     * @param String $T075_Id
     * @return Bool
     */
    public function calcularDespesas($T075_Id)
    {
        global $g;
        
        $T075 = mysql_query("SELECT COUNT(DISTINCT T055_Valor_Adicao) AS totalAdicao,
                                    SUM(T055_Valor_Custo_Unitario * T055_Quantidade) AS T055_Valor_Custo_Unitario
                               FROM T075    
                          LEFT JOIN T055 ON T055_T075_Id=T075_Id
                              WHERE T075_Id='{$T075_Id}'");
        $mT075 = mysql_fetch_array($T075);

        // variavel utilizada na formula do siscomex
        $totalAdicoes = $mT075['totalAdicao'];

        $T168 = mysql_query("SELECT T168_Id,
                                    T168_Aliquota,
                                    T168_Flag_Tipo,
                                    T168_Valor,
                                    T168_Formula,
                                    T168_Flag_Tipo,
                                    T168_Cotacao_ME,
                                    T168_Descricao,
                                    T075_Valor_VMLE,
                                    T168_Flag_ME,
                                    T075_Cotacao_ME
                               FROM T168
                          LEFT JOIN T075 ON T075_Id = T168_T075_Id
                              WHERE T168_T075_Id = '{$T075_Id}'
                           ORDER BY T168_Ordem ASC");

        $variavel   = array();
        $valorME    = array();
        $valorReais = array();

        while($mT168 = mysql_fetch_array($T168)){
            //monta o nome da variavel em tempo real, essa variavel poderá ser utilizada em qual formula
            $nomeVariavel    = strToUpper(str_replace(" ","_", $mT168['T168_Descricao'])); 
            $valorCustoTotal = $mT075['T055_Valor_Custo_Unitario'] * $mT168['T075_Cotacao_ME'];
        
            if($mT168['T168_Flag_Tipo'] == 'F'){
                // verifica se é a despesa é do tipo formula
                $mT168['T168_Formula'] = str_replace('<?php','', $mT168['T168_Formula']);
                $mT168['T168_Formula'] = str_replace("ALIQUOTA",$mT168['T168_Aliquota'], $mT168['T168_Formula']);
                $mT168['T168_Formula'] = str_replace("COTACAO_ME",$mT168['T168_Cotacao_ME'], $mT168['T168_Formula']);
                $mT168['T168_Formula'] = str_replace("VALOR_VMLE_REAL", $valorCustoTotal, $mT168['T168_Formula']);

                // procura se existe uma variavel montada em tempo real e pega o valor
                if ($mT168['T168_Flag_ME'] == 'S'){
                    $mT168['T168_Formula'] = str_replace($variavel, $valorME, $mT168['T168_Formula']);
                } else {
                    $mT168['T168_Formula'] = str_replace($variavel, $valorReais, $mT168['T168_Formula']);
                }
                
                $variavel[] = $nomeVariavel; 
                $resultado  = eval($mT168['T168_Formula']);
                if ($mT168['T168_Flag_ME']=='S'){
                    $valorReais[] = $resultado * $mT168['T168_Cotacao_ME'];
                    $valorME[]    = $resultado;
                } else  {
                    $valorReais[] = $resultado;
                    $valorME[]    = $resultado;
                }

                mysql_query("update T168 set T168_Valor = '{$resultado}' where T168_Id='{$mT168['T168_Id']}'");

            }else{             

                $variavel[] = $nomeVariavel;
                if ($mT168['T168_Flag_ME']=='S'){
                    $valorReais[] = $mT168['T168_Valor'] * $mT168['T168_Cotacao_ME'];
                    $valorME[]    = $mT168['T168_Valor'];
                }else{
                    $valorReais[] = $mT168['T168_Valor'];
                    $valorME[]    = $mT168['T168_Valor'];
                }
            }
        }

        return true;
    }

    /**
     * calcularDepesas
     *
     * @param String $T075_Id
     * @return Bool
     */
    public function associarItemXML($T055_Id, $T310_Id)
    {
        global $g;

        $T300 = mysql_query("SELECT T310_Valor_Adicao,
                                    T310_Adicao_Item,
                                    T310_Descricao_Produto,
                                    T310_Quantidade,
                                    T310_Valor_Custo_Unitario,
                                    T310_Aliquota_COFINS,
                                    T310_Aliquota_Imposto_Importacao,
                                    T310_Aliquota_IPI,
                                    T310_Aliquota_PIS,
                                    T310_Classificacao_Fiscal
                               FROM T310
                              WHERE T310_Id = '{$T310_Id}'");
        
        $mT310 = mysql_fetch_array($T300);       
        
        
        // Atualiza dados do T055 com os dados da tabela T310 (itens XML DI)
        mysql_query("UPDATE T055 
                        SET T055_Valor_Adicao                = '{$mT310['T310_Valor_Adicao']}',
                            T055_Adicao_Item                 = '{$mT310['T310_Adicao_Item']}',
                            T055_Aliquota_Imposto_Importacao = '{$mT310['T310_Aliquota_Imposto_Importacao']}',
                            T055_Aliquota_IPI                = '{$mT310['T310_Aliquota_IPI']}',
                            T055_Aliquota_PIS                = '{$mT310['T310_Aliquota_PIS']}',
                            T055_Aliquota_COFINS             = '{$mT310['T310_Aliquota_COFINS']}'
                      WHERE T055_Id = '{$T055_Id}'");
        
        $error = mysql_error();
        if(!empty($error)){
            return $error;
        }

        mysql_query("UPDATE T310 SET T310_T055_Id = '{$T055_Id}'
                               WHERE T310_Id = '{$T310_Id}'");
                      
        $error = mysql_error();
        if(!empty($error)){
            return $error;
        }

        return true;
    }

    /**
     * validaDadosNFe
     *
     * @param String $T075_Id
     * 
     */

    public function validaDadosNFe($T075_Id)
    {        
     
        $T075 = mysql_query("SELECT T075_Data_Emissao_DI,
                                    T075_Flag_Transporte_DI,
                                    T075_Flag_Tipo_Intermedio_DI,
                                    T075_D118_Id,
                                    T075_UF_Importador,
                                    T075_D006_Id
                               FROM T075
                              WHERE T075_Id = '{$T075_Id}'");

        $mT075 = mysql_fetch_array($T075);   
        
        $erros = '';

        if($mT075['T075_Data_Emissao_DI'] == '0000-00-00' || empty($mT075['T075_Data_Emissao_DI'])){
            $erros .= "01 - Data de emissão.\n";
        }

        if($mT075['T075_Flag_Transporte_DI'] == '' || empty($mT075['T075_Flag_Transporte_DI'])){
            $erros .= "02 - Forma de transporte.\n";
        }

        if($mT075['T075_Flag_Tipo_Intermedio_DI'] == '' || empty($mT075['T075_Data_Emissao_DI'])){
            $erros .= "03 - Forma de importação.\n";
        }

        if($mT075['T075_D118_Id'] <= 0 || empty($mT075['T075_D118_Id'])){
            $erros .= "04 - Porto de destino.\n";
        }

        if($mT075['T075_UF_Importador'] == '' || empty($mT075['T075_UF_Importador'])){
            $erros .= "05 - UF do importador.\n";
        }

        if($mT075['T075_D006_Id'] <= 0 || empty($mT075['T075_D006_Id'])){
            $erros .= "06 - CFOP.\n";
        }

        if($erros != ''){
            return "As informações abaixo não foram preenchidas, favor verificar:\n\n".$erros;
        }

        return true;
    }

    /**
     * retornaFreteTotal
     *
     * @param String $T055_Valor_Total_Frete
     * 
     */

    public function retornaValorTotalNota($valorTotalNota, $T007_Id)
    {        
        // Personalizado no cliente caso precise acrescentar um valor de um campo que não exista no T075/T055
     
        return $valorTotalNota;
    }

    /**
     * excluirDI
     *
     * @param String $T075_Id
     * @return String/bool
     */
    public function excluirDI($T075_Id)
    {
        global $g;
        
        if (empty($T075_Id)) {
            return "Não foi passado o Id da DI, contate o suporte técnico";
        }
       
        $T075 = mysql_query("SELECT T007_Numero_Nota_Fiscal,
                                    T075_Data_Estoque,
                                    T075_Numero_DI
                               FROM T075 
                          LEFT JOIN T007 ON T075_Id = T007_T075_Id
                              WHERE T075_Id = '{$T075_Id}'");
        $mT075 = mysql_fetch_array($T075);
            
        if(!empty($mT075['T007_Numero_Nota_Fiscal'])){
            return "D.I não pode ser excluída, já possui nota fiscal número {$mT075['T007_Numero_Nota_Fiscal']}";
        }

        if($mT075['T075_Data_Estoque'] > '0000-00-00'){
            return "D.I não pode ser excluída, já deu entrada dos produtos no estoque em {$mT075['T075_Data_Estoque']}";
        }          
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $cClausula = "DELETE FROM T168 WHERE T168_T075_Id='{$T075_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro <br /><br /> $cClausula";
        }
        
        $T055 = mysql_query("SELECT T055_Id FROM T055 WHERE T055_T075_Id='{$T075_Id}'");
        while ($mT055 = mysql_fetch_array($T055)) {

            $cClausula = "DELETE FROM T055A WHERE T055A_T055_Id='{$mT055['T055_Id']}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /> <br /> $cClausula";
            }

            $cClausula = "DELETE FROM T055 WHERE T055_Id='{$mT055['T055_Id']}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /> <br /> $cClausula";
            }
        }

        $cClausula = "DELETE FROM T002 WHERE T002_T075_Id='{$T075_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }

        $cClausula = "DELETE FROM T015 WHERE T015_T075_Id='{$T075_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }

        $cClausula = "DELETE FROM T270 WHERE T270_T075_Id='{$T075_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }

        $cClausula = "DELETE FROM T161 WHERE T161_T075_Id='{$T075_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }

        $cClausula = "DELETE FROM T155 WHERE T155_T075_Id='{$T075_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }
        
        $cClausula = "DELETE FROM T075 WHERE T075_Id='{$T075_Id}'";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: $cErro<br /><br />$cClausula";
        }

        $mT075['T075_Numero_DI'] = str_replace(array('/','.','-',',',' '),'',$mT075['T075_Numero_DI']);
        $arquivo = $g['pathDadosAntigo'] . "/di/xml/recebido/" . $mT075['T075_Numero_DI'] . ".xml";

        if (file_exists($arquivo)) {
            mkdir($arquivo, 0777, true);
            unlink($arquivo);
        }
        
        mysql_query("COMMIT");
        mysql_query("SET AUTOCOMMIT=1");
        
        return true;
    }
}

?>
