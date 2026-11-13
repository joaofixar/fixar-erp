<?php
namespace hardness;
definirClasse('IMP005');
class IMP005_
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
     * cancelarPO
     *
     * @param String $T295_Id
     * @return true
     */     
    public function cancelarPO($T295_Id) {
        global $g;

        if (!($T295_Id > 0)) {
            return "Id da Purchase Order não informado!";
        }

        $sql = "SELECT * FROM T295 WHERE T295_Id='$T295_Id'";
        $T295 = mysql_fetch_assoc(mysql_query($sql));

        if (!($T295['T295_Flag_Status'] == '0' || $T295['T295_Flag_Status'] == '2')) {
            return "A Purchase Order só pode ser cancelada se estiver com status Pendente ou Negado!";
        }

        $sql = "UPDATE T295 SET T295_Flag_Cancelado = 'S', T295_Flag_Status = '6', T295_Nome_Status = T295_Status_Pedido(T295_Flag_Status, '{$T295_Id}') WHERE T295_Id = '{$T295_Id}'";
        mysql_query($sql);

        if (mysql_error()) {
            $divId = 'sql_error_' . uniqid();
            $erro = 'Erro ao cancelar Purchase Order: <br><b>' . mysql_error() . '</b><br><br>';
            $erro .= '<a href="#" onclick="var el=document.getElementById(\''.$divId.'\'); el.style.display = (el.style.display == \'none\') ? \'block\' : \'none\'; return false;" style="text-decoration:underline;">Ver SQL:</a>';
            $erro .= '<div id="'.$divId.'" style="display:none; margin-top:10px; border:1px solid #ccc; background-color:#f9f9f9; padding:10px; font-family:monospace;">' . $sql . '</div>';

            return $erro;
        }
        
        return true;
    }
    
    /**
     * updateT161_Flag_Ok
     *
     * @param String $D122_Id
     * @param String $T295_Id
     * @param String $selected
     * @return bool
     */     
    public function updateT161_Flag_Ok($D122_Id, $T295_Id, $selected)
    {
        global $g;
        
        $T161  = mysql_query("SELECT T161_Id FROM T161 WHERE T161_D122_Id='$D122_Id' AND T161_T295_Id='$T295_Id'");
        $mT161 = mysql_fetch_array($T161);
        
        if (mysql_num_rows($T161) <= 0) {
            mysql_query("INSERT INTO T161 (T161_D122_Id,T161_T295_Id,T161_Flag_Ok, T161_Data)
                                   VALUES ('$D122_Id','$T295_Id','S', curdate())");
        } else {
            mysql_query("UPDATE T161 SET T161_Flag_Ok='$selected', T161_Data=curdate()
                          WHERE T161_Id='{$mT161['T161_Id']}'");
        }
        return true;  
    }
    
    /**
     * insereRegistrosT161
     *
     * @param String $T295_Id
     */     
    public function insereRegistrosT161($T295_Id)
    {
        
        $sql = "SELECT * FROM T295 WHERE T295_Id='{$T295_Id}'";
        
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        
        $sql = "SELECT D122_Id FROM D122";
        
        $resultado = mysql_query($sql);
        
        while ($row = mysql_fetch_array($resultado)) {
            
            $T161  = mysql_query("SELECT T161_Id FROM T161 WHERE T161_D122_Id='{$row['D122_Id']}' AND T161_T295_Id='$T295_Id'");
            $mT161 = mysql_fetch_array($T161);
            
            if (mysql_num_rows($T161) <= 0) {
                mysql_query("INSERT INTO T161 (T161_D122_Id,T161_T295_Id,T161_Flag_Ok)
                                       VALUES ('{$row['D122_Id']}','$T295_Id','N')");
            } 
        } 
    }
    
    /**
     * montarEmail
     *
     * @param String $T295_Id
     * @return array
     */     
    public function montarEmail($T295_Id)
    {
        global $g;
        $T295_Id = mysql_real_escape_string($T295_Id);
        $sql     = <<<EOT
        SELECT * FROM T295
        LEFT JOIN D024 as FORNECEDOR ON FORNECEDOR.D024_Id=T295_D024_Id
        LEFT JOIN D024 as DESPACHANTE ON DESPACHANTE.D024_Id=T295_D119_Id
        LEFT JOIN D119 ON D119_Id=T295_D119_Id
        LEFT JOIN D118 ON D118_Id=T295_D118_Id
        LEFT JOIN D044 ON D044_Id=T295_D044_Id
        WHERE T295_Id='{$T295_Id}'
EOT;
        $mT295   = mysql_query($sql);
        
        $array = array();
        
        $array['Para']    = $mT295['FORNECEDOR.D024_E_Mail'];
        $array['Assunto'] = "Declaração de Importação [{$T295_Id}]";
        
        $array['Corpo'] = "";
        
        return $array;
    }
    
    /**
     * Calcular
     *
     * @param String $T295_Id
     * @return bool
     */     
    public function Calcular($T295_Id)
    {
        $T296 = mysql_query("SELECT T296_Id
                               FROM T296 
                              WHERE T296_T295_Id='{$T295_Id}'");
        
        while ($mT296 = mysql_fetch_array($T296)) {
            mysql_query("call T296_Gravar_Totalizacao('$mT296[T296_Id]')");
        }
        return true;
    }
    
    /**
     * Atualza OC
     *
     * @param String $T295_Id
     * @return bool
     */     
    public function atualizaOC($T295_Id)
    {
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();

        $T296 = mysql_query("SELECT T296_D009_Id
                               FROM T296 
                              WHERE T296_T295_Id='{$T295_Id}'");
        
        while ($mT296 = mysql_fetch_array($T296)) {
            //atualiza a quantidade OC do produto
            mysql_query("update D009 set D009_Quantidade_OC=D009_Quantidade_OC_3('{$mT296['T296_D009_Id']}',0) where D009_Id = '{$mT296['T296_D009_Id']}'");
            //atualiza a quantidade OC dos pedidos
            $CAD002->reprocessaOCPedidos($mT296['T296_D009_Id']);
        }
        return true;
    }

    /**
     * alterarClientePreCadastro
     *
     * @param String $T295_Id
     * @return String/bool
     */     
    public function gerarNotaFiscal($T295_Id)
    {
        global $g, $T007_Id, $T007_Id_aux;
        
        $T007 = mysql_query("SELECT T007_Id,T007_Numero_Nota_Fiscal,
                                 T007_Data_Emissao 
                            FROM T007 
                           WHERE T007_T295_Id='{$T295_Id}' 
                             AND T007_Flag_Cancelada!='S'");
        
        if (mysql_num_rows($T007) > 0) {
            $cMsg_Faturado = "Esta importação já foi faturada: ";
            while ($mT007 = mysql_fetch_array($T007)) {
                $mT007['T007_Data_Emissao'] = gCorrigeData($mT007['T007_Data_Emissao']);
                $cMsg_Faturado .= "NF: " . $mT007['T007_Numero_Nota_Fiscal'] . " no dia " . $mT007['T007_Data_Emissao'] . ", ";
                $T007_Id_aux = $mT007['T007_Id'];
                
            }
            return $cMsg_Faturado;
        }

        $T295  = mysql_query("SELECT * 
                               FROM T295 
                          LEFT JOIN D006 ON D006_Id=T295_D006_Id
                          LEFT JOIN D118 ON D118_Id=T295_D118_Id
                          LEFT JOIN D018 ON D018_UF=D118_Estado
                              WHERE T295_Id='{$T295_Id}'");
        $mT295 = mysql_fetch_array($T295);
        
        $D024  = mysql_query("SELECT * 
                               FROM D024 
                          LEFT JOIN D018 ON D018_Id=D024_D018_Id
                          LEFT JOIN D020 ON D020_Id=D024_D020_Id
                          LEFT JOIN D030 ON D030_Id=D024_D030_Id
                          LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id='{$mT295['T295_C004_Id']}'
                              WHERE D024_Id = '{$mT295['T295_D024_Id']}'");
        $mD024 = mysql_fetch_array($D024);
        
        if ($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
            $T007_Cliente_CNPJ_CPF = "{$mD024['D024_Cnpj']}";
        } elseif ($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'I') {
            $T007_Cliente_CNPJ_CPF = "";
        } else {
            $T007_Cliente_CNPJ_CPF = "{$mD024['D024_Cpf']}";
        } 
        
        $T296 = mysql_query("SELECT *,sum(T296_Valor_Total_Frete) T296_Valor_Total_Frete,
                                  sum(T296_Valor_Total_Custo*$mT295[T295_Cotacao_ME]) T296_Valor_Total_Custo_Reais,
                                  sum(T296_Valor_Total_Custo) T296_Valor_Total_Custo,
                                  sum(T296_Valor_Aduaneira) T296_Valor_Aduaneira,
                                  sum(T296_Valor_Imposto_Importacao) T296_Valor_Imposto_Importacao,
                                  sum(T296_Valor_IPI) T296_Valor_IPI,
                                  sum(T296_Valor_PIS) T296_Valor_PIS,
                                  sum(T296_Valor_COFINS) T296_Valor_COFINS,
                                  sum(T296_Valor_ICMS) T296_Valor_ICMS,
                                  sum(T296_Valor_ICMS_Presumido) T296_Valor_ICMS_Presumido,
                                  sum(T296_Valor_ICMS_Pagar) T296_Valor_ICMS_Pagar,
                                  sum(T296_Valor_Base_ICMS_Sem_Reducao) T296_Valor_Base_ICMS_Sem_Reducao,
                                  sum(T296_Valor_Base_ICMS_Normal) T296_Valor_Base_ICMS_Normal,
                                  sum(T296_Peso_Kg_Liquido_Unitario) T296_Peso_Kg_Liquido_Unitario,
                                  sum(T296_Valor_Total_Despesas) T296_Valor_Total_Despesas,
                                  sum(T296_Valor_Total_Despesas_Aduaneira) T296_Valor_Total_Despesas_Aduaneira,
                                  sum(T296_Valor_Total_Custo_Final) T296_Valor_Total_Custo_Final,
                                  sum(T296_Valor_Total_AFRMM) T296_Valor_Total_AFRMM,
                                  sum(T296_Valor_Total_Preco_Nota_Fiscal) T296_Valor_Total_Preco_Nota_Fiscal
                             FROM T296
                            WHERE T296_T295_Id='{$T295_Id}'");
        
        $mT296 = mysql_fetch_array($T296);
        $T296_T295 = array_merge($mT295, $mT296);
        
        if ($mT295['D006_Flag_Isento_ICMS']=='S'){
            $mT296['T296_Valor_Base_ICMS_Sem_Reducao']   = 0;
            $mT296['T296_Valor_Base_ICMS_Normal']   = 0;
        }

        $mICMS     = $this->retornaICMS($mT295['D118_Flag_Tipo']);
        $baseICMS  = $mICMS['baseICMS'];
        $valorICMS = $mICMS['valorICMS'];

        $mFreteTotal     = $this->retornaFreteTotal($mT296['T296_Valor_Total_Frete']);
        $valorTotalFrete = $mFreteTotal['valorTotalFrete'];

        $mT296['T296_Valor_Total_Custo_Reais']           = number_format($mT296['T296_Valor_Total_Custo_Reais'], 2, ',', '.');
        $mT296['T296_Valor_Total_Custo']                 = number_format($mT296['T296_Valor_Total_Custo'], 2, ',', '.');
        $mT295['T295_Cotacao_ME']                        = number_format($mT295['T295_Cotacao_ME'], 4, ',', '.');
        $mT296['T296_Valor_Total_Frete']                 = number_format($mT296['T296_Valor_Total_Frete'], 2, ',', '.');
        $mT296['T296_Valor_Aduaneira']                   = number_format($mT296['T296_Valor_Aduaneira'], 2, ',', '.');
        $mT296['T296_Valor_Imposto_Importacao']          = number_format($mT296['T296_Valor_Imposto_Importacao'], 2, ',', '.');
        $mT296['T296_Valor_IPI']                         = number_format($mT296['T296_Valor_IPI'], 2, ',', '.');
        $mT296['T296_Valor_PIS']                         = number_format($mT296['T296_Valor_PIS'], 2, ',', '.');
        $mT296['T296_Valor_COFINS']                      = number_format($mT296['T296_Valor_COFINS'], 2, ',', '.');
        $mT296['T296_Valor_Total_AFRMM']                 = number_format($mT296['T296_Valor_Total_AFRMM'], 2, ',', '.');
        $mT296['T296_Valor_ICMS']                        = number_format($mT296['T296_Valor_ICMS'], 2, ',', '.');
        $mT296['T296_Valor_ICMS_Presumido']              = number_format($mT296['T296_Valor_ICMS_Presumido'], 2, ',', '.');
        $mT296['T296_Valor_ICMS_Pagar']                  = number_format($mT296['T296_Valor_ICMS_Pagar'], 2, ',', '.');
        $mT296['T296_Peso_Kg_Liquido_Unitario']          = number_format($mT296['T296_Peso_Kg_Liquido_Unitario'], 2, ',', '.');
        $mT296['T296_Valor_Total_Preco_Nota_Fiscal']     = number_format($mT296['T296_Valor_Total_Preco_Nota_Fiscal'], 2, ',', '.');
        $mT296['T296_Valor_Total_Despesas_Aduaneira']    = number_format($mT296['T296_Valor_Total_Despesas_Aduaneira'], 2, ',', '.');
        $mT296['T296_Valor_Total_Despesas']              = number_format($mT296['T296_Valor_Total_Despesas'], 2, ',', '.');
        $mT296['T296_Valor_Total_Custo_Final']           = number_format($mT296['T296_Valor_Total_Custo_Final'], 2, ',', '.');
        $mT296['T296_Valor_Base_ICMS_Sem_Reducao']       = number_format($mT296['T296_Valor_Base_ICMS_Sem_Reducao'], 2, ',', '.');
        $mT295['T295_Aliquota_ICMS_Credito_Presumido']   = number_format($mT295['T295_Aliquota_ICMS_Credito_Presumido'], 2, ',', '.');
        $mT295['T295_Percentual_Reducao_ICMS_Presumido'] = number_format($mT295['T295_Percentual_Reducao_ICMS_Presumido'], 2, ',', '.');

        $observacaoFiscal = "II: {$mT296['T296_Valor_Imposto_Importacao']}, PIS: {$mT296['T296_Valor_PIS']}, Cofins: {$mT296['T296_Valor_COFINS']}, Siscomex: {$mT296['T296_Valor_Total_Despesas_Aduaneira']}, Frete: {$mT296['T296_Valor_Total_Frete']}, THC: {$mT296['T296_Valor_Aduaneira']}, AFRMM: {$mT296['T296_Valor_Total_AFRMM']}, Base ICMS: {$mT296[$baseICMS]}, Devido: {$mT296['T296_Valor_ICMS']}, Presumido: {$mT296['T296_Valor_ICMS_Presumido']}, Recolhido: {$mT296['T296_Valor_ICMS_Pagar']}. BASE DE CALCULO REDUZIDA EM {$mT295['T295_Percentual_Reducao_ICMS_Presumido']}% CREDITO PRESUMIDO DE {$mT295['T295_Aliquota_ICMS_Credito_Presumido']}% VALOR CREDITO PRESUMIDO R\$ {$mT296['T296_Valor_ICMS_Presumido']} ICMS RECOLHIDO R\$ {$mT296['T296_Valor_ICMS_Pagar']} DI: {$mT295['T295_Numero_DI']} PO: {$mT295['T295_Ordem_Compra']}";
        $observacaoFiscal='';
        if($mT295['D006_Observacao'] != ''){
            $observacaoFiscal .= $mT295['D006_Observacao'];
        }
        // Váriaveis dentro da Observação Fiscal
        foreach ($T296_T295 as $key => $value) {
            $key = preg_replace('#^[^_]+_#', '', $key);
            $key = strtoupper($key);
            if (preg_match('#^([0-9])+\.([0-9])+$#', $value)) {
                $value = gCorrigeNumero($value);
            }
            $observacaoFiscal = str_replace('[' . strtoupper($key) . ']', $value, $observacaoFiscal);
        }

        $sqlT007_Numero = mysql_query("SELECT T007_Id,T007_Numero_Nota_Fiscal
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
        }

        $nNumero_Serie            = '1';
        $T007_Flag_Finalidade_NFe = '1';
        
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
        $cClausula .= " T007_Flag_Destino_Operacao, "; // 109
        $cClausula .= " T007_Hora_Emissao, "; // 110
        $cClausula .= " T007_Fuso_Horario_Emissao, "; // 111
        $cClausula .= " T007_Fuso_Horario_Saida "; // 112
        $cClausula .= " ) VALUES ("; // 109
        $cClausula .= " '{$mT295['T295_D006_Id']}', "; // CFOP // 1
        $cClausula .= " '{$mT295['T295_D024_Id']}', "; // Fornecedor //2
        $cClausula .= " '', "; // Transportadora // 3
        $cClausula .= " '', "; // Representante //4
        $cClausula .= " {$g['c004']['C004_Id']}, "; // Empresa_Atual //5
        $cClausula .= " '', "; // D036_Id - Tipo Transporte // 6
        $cClausula .= " '', "; // T134_Id - Faixa simples //7
        $cClausula .= " 'N', "; // Flag_Cancelada //8
        $cClausula .= " '6', "; // Flag ACP //9
        $cClausula .= " '{$T007_Numero_Nota_Fiscal}', "; // NUMERO DA NOTA FISCAL //10
        $cClausula .= " {$g['c004']['C004_Ambiente_NFe']}, "; // Ambiente NFe //11
        $cClausula .= " '{$nNumero_Serie}', "; // Numero serie NFe //12
        $cClausula .= " '', "; //-- NUMERO DA NOTA FISCAL DE DEVOLUCAO //13
        $cClausula .= " curdate(), "; // DATA DE EMISSAO //14
        $cClausula .= " curdate(), "; // Data Saida //15
        $cClausula .= " '', "; // Nome Transportadora //16
        $cClausula .= " '', "; // D036_Tipo_Transporte - Tipo Transporte //17
        $cClausula .= " '', "; // OBSERVACAO DO CLIENTE QUE SAI NO CORPO DA NOTA FISCAL //18
        $cClausula .= " '', "; // Flag Frete //19
        $cClausula .= " '', "; // Flag IPI //20
        $cClausula .= " '', "; // Observacao Fiscal //21
        $cClausula .= " '{$valorTotalFrete}', "; // Valor Frete //22
        $cClausula .= " '', "; // Valor Seguro //23
        $cClausula .= " '', "; // Percentual Comissao //24 
        $cClausula .= " '', "; // Valor Despesas Acessorias //25
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
        $cClausula .= " '{$mT295['T295_Peso_Bruto']}', "; // Peso Bruto //57
        $cClausula .= " '{$mT295['T295_Peso_Liquido']}', "; // Peso Liquido //58
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
        $cClausula .= " '{$mT295['D006_Flag_Venda_Compra_Outros']}', "; // Flag Venda / Compras / Outros //93
        $cClausula .= " '1', "; // Flag Expedicao // 94
        $cClausula .= " '', "; // Numero de Parcelas // 95
        $cClausula .= " '', "; // Numero dias na entrada (Prazo) //96
        $cClausula .= " '', "; // Numero Dias //97
        $cClausula .= " '', "; // Flag Revenda Consumidor //98
        $cClausula .= " '', "; // Casas decimais quantidade //99
        $cClausula .= " '5', "; // Casas decimais preco //100
        $cClausula .= " '', "; // Descricao CFOP //101
        $cClausula .= " '', "; // Codigo CFOP //102
        $cClausula .= " '', "; // Exportacao UF Embarque //103                     
        $cClausula .= " '', "; // Exportacao Local Embarque  //104
        $cClausula .= " {$g['C004']['C004_Codigo_Regime_Tributario']}, "; //105
        //$cClausula .= " '{$g['C020']['C020_Versao_NFe']}', "; // Versao NFe   // 106
        $cClausula .= " '{$g['c004']['C004_Versao_NFe']}', "; // Versao NFe   // 106
        $cClausula .= " '{$observacaoFiscal}', "; // Observacao NFE //107
        $cClausula .= " '{$T295_Id}', "; // Id DI //108
        $cClausula .= " '3', "; // Flag_Destino_Operacao //109
        $cClausula .= " current_time(), "; // Hora Emissao //110
        $cClausula .= "'".date('P')."', "; // Fuso horario emissao // 111
        $cClausula .= "'".date('P')."' "; // Fuso horario saida // 112
        $cClausula .= " )";
        mysql_query($cClausula);
        $cErro = mysql_error();
        if (!empty($cErro)) {
            mysql_query("ROLLBACK");
            return "Erro: " . $cErro . "<br />" . $cClausula;
        }
        
        /**
         * Verificar se todos itens tem locação
         */
        $T296 = mysql_query("SELECT T066_D004_Id,
                                    T066_Id,
                                    D001_Codigo_Produto,
                                    T296_T066_Id
                            FROM T296
                            LEFT JOIN D009 ON D009_Id=T296_D009_Id
                            LEFT JOIN T066 ON T296_T066_Id=T006_Id
                            WHERE T296_T295_Id='{$T295_Id}'
                            GROUP BY T296_Id");
        $erro = '';
        while ($mT296 = mysql_fetch_array($T296)) {
            if ($mT296['T066_Id']<=0 || $mT296['T066_D004_Id']<=0){
                $erro .= "O produto {$mT296['D001_Codigo_Produto']} está sem locação.<br>";
                continue;
            }
        }
        if (!empty($erro)){                
            mysql_query("ROLLBACK");
            return $erro;
        }
        
        $T007_Id = $g['mysqlLastId'];
        
        $T296 = mysql_query("SELECT * 
                               FROM T296 
                          LEFT JOIN D005 ON D005_Id=T296_D005_Id
                          LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id='{$g['empresaAtual']}'
                          LEFT JOIN D088 ON D088_Id=D149_D088_Id
                          LEFT JOIN D037 ON D037_Id=T296_D037_Id
                              WHERE T296_T295_Id='{$T295_Id}'");
        while ($mT296 = mysql_fetch_array($T296)) {
            

            if ($mT295['D006_Flag_Isento_ICMS']=='S'){
                $mT296['T296_Valor_Base_ICMS_Sem_Reducao']   = 0;
                $mT296['T296_Valor_Base_ICMS_Normal']   = 0;
            }

            $mT296['T296_Valor_Custo_Unitario'] = $mT296['T296_Valor_Custo_Unitario'] * $mT295['T295_Cotacao_ME'];
            
            if ($mT296['T296_Aliquota_IPI'] > 0) {
                $mT296['D088_Codigo_ST'] = '00';
            } else {
                $mT296['D088_Codigo_ST'] = '03';
            }
            $mT296['T296_Descricao_Produto'] = mysql_real_escape_string($mT296['T296_Descricao_Produto']);

            // retorna valor do frete
            $mFrete = $this->retornaFrete($mT296['T296_Valor_Total_Frete']);
            $valorFrete   = $mFrete['valorFrete'];
            
            // retorna FCP 
            $mFCP        = $this->retornaFCP($mD024['D053_Percentual_FCP'], $mT295['D006_Flag_Calcula_FCP'], $mT296[$baseICMS]);
            $aliquotaFCP = $mFCP['aliquotaFCP'];
            $baseFCP     = $mFCP['baseFCP'];
            $valorFCP    = $mFCP['valorFCP'];

            if($mT295['D006_Flag_Calcula_FCP'] && $aliquotaFCP > 0){
                // Se calcula FCP precisa descontar a aliquota e valor FCP do valor da aliquota e valor ICMS, na nota fiscal é enviado em campos separados
                $mT296['T296_Aliquota_ICMS'] -= $aliquotaFCP;
                $mT296[$valorICMS]           -= $valorFCP;
            }
        

            $T066 = mysql_query("select T296_T066_Id, T066_D004_Id from T296 lef join T066 on T066_Id=T296_T066_Id where T296_Id='{$mT296['T296_Id']}'");
            $mT066=mysql_fetch_array($T066);
            if (mysql_num_rows($T066)>1){
                $mT066['T066_Id']=0;
                $mT066['T066_D004_Id']=0;
            }

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
            $cClausula .= "T008_T296_Id, "; // 81
            $cClausula .= "T008_Valor_Total_Frete, "; // 82
            $cClausula .= "T008_Percentual_FCP, "; // 83
            $cClausula .= "T008_Valor_FCP, "; // 84
            $cClausula .= "T008_Valor_Base_FCP "; // 85
            $cClausula .= ") VALUES (";
            $cClausula .= "'{$g['c004']['C004_Id']}',"; // Empresa Atual  // 1
            $cClausula .= "'{$T007_Id}',"; // T007_Id // 2
            $cClausula .= "'{$mT295['T295_D006_Id']}',"; // D006_Id // 3
            $cClausula .= "'{$mT296['T296_D037_Id']}',"; // D037_Id // 4
            $cClausula .= "'{$mT295['D006_D059_Id']}',"; // D059_Id // 5
            $cClausula .= "'{$mT296['D037_Unidade']}',"; // D037_Id // 6
            $cClausula .= "'{$mT295['D006_Codigo_CFOP']}',"; // D006_Codigo_CFOP // 7
            $cClausula .= "'{$mT296['T296_Aliquota_IPI']}',"; // Aliquota IPI // 8
            $cClausula .= "'0',"; // Percentual Reducao IPI // 9
            $cClausula .= "'{$mT296['T296_Aliquota_ICMS']}',"; // Aliquota ICMS // 10
            $cClausula .= "'{$mT296['T296_Percentual_Reducao_ICMS']}',"; // Percentual Reducao ICMs // 11
            $cClausula .= "'{$mT296['T296_Peso_Kg_Liquido_Unitario']}',"; // Peso Unitario // 12
            $cClausula .= "'{$mT296['T296_D005_Id']}',"; // D005_Id // 13
            $cClausula .= "'100',"; // Flag situacao tributaria ICMS // 14
            $cClausula .= "'',"; // Flag Preco Especial // 15
            $cClausula .= "'{$mT296['D005_Classificacao_Fiscal']}',"; // NCM // 16
            $cClausula .= "'{$mT296['D088_Codigo_ST']}', "; // Situacao tributaria IPI // 17                                               
            $cClausula .= "'{$mT296['T296_Quantidade']}',"; // Quantidade // 18
            $cClausula .= "'{$mT296['T296_Codigo_Produto']}',"; // Codigo Produto // 19
            $cClausula .= "'',"; // Codigo Barras // 20
            $cClausula .= "'',"; // Codigo Substituto // 21
            $cClausula .= "'{$mT296['T296_D009_Id']}',"; // D009_Id // 22
            $cClausula .= "'{$mT296['T296_Descricao_Produto']}',"; // Descricao Produto // 23
            $cClausula .= "'{$mT296['T296_Valor_Custo_Final_Unitario']}',"; // Valor Custo Unitario // 24
            $cClausula .= "'{$mT296['T296_Valor_Preco_Unitario_Nota_Fiscal']}',"; // Valor Preco sem desconto unitario // 25
            $cClausula .= "'N',"; // Flag ST // 26
            $cClausula .= "'N',"; // ST VA // 27
            $cClausula .= "'0',"; // ICMS Inter Estadual // 28
            $cClausula .= "'0',"; // ST ICMS Interno // 29
            $cClausula .= "'{$mT295['D006_Flag_Isento_PIS']}',"; // Flag Isento PIS // 30
            $cClausula .= "'{$mT296['T296_Aliquota_PIS']}',"; // Aliquota PIS // 31
            $cClausula .= "'{$mT295['D006_Situacao_Tributaria_PIS']}',"; // Situacao Tributaria PIS // 32
            $cClausula .= "'{$mT295['D006_Flag_Isento_COFINS']}',"; // Flag Isento COFINS // 33
            $cClausula .= "'{$mT296['T296_Aliquota_COFINS']}',"; // Aliquota COFINS // 34
            $cClausula .= "'{$mT295['D006_Situacao_Tributaria_COFINS']}', "; // Situacao Tributaria COFINS // 35
            $cClausula .= "'{$mT296['T296_Valor_Base_Imposto_Importacao']}', "; // Base II // 36
            $cClausula .= "'{$mT296['T296_Valor_Imposto_Importacao']}', "; // Valor II // 37
            $cClausula .= "'', "; // Despesas Aduaneira // 38
            $cClausula .= "'', "; // Valor IOF // 39
            $cClausula .= "'{$mT295['T295_Numero_DI']}', "; // Numero DI // 40
            $cClausula .= "'{$mT295['T295_Data_Emissao']}', "; // Data registro DI // 41
            $cClausula .= "'{$mT295['D118_Nome']}', "; // Local Desembaraco // 42
            $cClausula .= "'{$mT295['T295_Data_Emissao']}', "; // Data Desenbaraco // 43
            $cClausula .= "'{$mT295['D018_UF']}', "; // UF Desenbaraco // 44
            $cClausula .= "'{$mT295['T295_D024_Id']}', "; // Codigo Exportador // 45
            $cClausula .= "'{$mT296['T296_Valor_Adicao']}', "; // Numero Adicao // 46
            $cClausula .= "'1', "; // Numero sequencia adicao // 47
            $cClausula .= "'{$mT295['T295_D024_Id']}', "; // Codigo Fabricante // 48
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
            $cClausula .= "'{$mT296[$baseICMS]}', "; // 64
            $cClausula .= "'{$mT296[$valorICMS]}', "; // 65
            $cClausula .= "'{$mT296['T296_Valor_Base_IPI']}', "; // 66
            $cClausula .= "'{$mT296['T296_Valor_IPI']}', "; // 67
            $cClausula .= "'{$mT296['T296_Valor_Base_PIS']}', "; // 68
            $cClausula .= "'{$mT296['T296_Valor_PIS']}', "; // 69
            $cClausula .= "'{$mT296['T296_Valor_Base_COFINS']}', "; // 70
            $cClausula .= "'{$mT296['T296_Valor_COFINS']}', "; // 71
            $cClausula .= "'{$mT296['T296_Valor_Base_ICMS_Normal']}', "; // 72
            $cClausula .= "'{$mT295['T295_Flag_Transporte_DI']}', "; // 73
            $cClausula .= "'{$mT295['T295_Flag_Tipo_Intermedio_DI']}', "; // 74
            $cClausula .= "'{$mT295['T295_CNPJ_Importador']}', "; // 75
            $cClausula .= "'{$mT295['T295_UF_Importador']}', "; // 76
            $cClausula .= "'{$mT295['T295_Numero_Drawback_Importacao']}', "; // 77
            $cClausula .= "'{$mT296['T296_Valor_Preco_Unitario_Nota_Fiscal']}', "; // Valor Preco sem desconto unitario // 78
            $cClausula .= "'{$mT066['T296_T066_Id']}', "; // 79
            $cClausula .= "'{$mT066['T066_D004_Id']}', "; // 80
            $cClausula .= "'{$mT296['T296_Id']}', "; // 81
            $cClausula .= "'{$valorFrete}', "; // 82
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
        }

        if(!empty($mT295['D006_Observacao_Produto'])){
            // Busca só o FCP, se precisar de mais campos incluir no select
            $T008 = mysql_query("SELECT T008_Valor_FCP,
                                        T008_Id
                                   FROM T008
                                  WHERE T008_T007_Id = {$T007_Id}");

            while ($mT008 = mysql_fetch_array($T008)) {
                $observacaoFiscalProduto = $mT295['D006_Observacao_Produto'] . ';';

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
       
        mysql_query("CALL T007_Gravar_Totalizacao('$T007_Id')");

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

        mysql_query("COMMIT");
        mysql_query("UNLOCK ALL");
        
        return true;
    }
    
    /**
     * imp007Finalizar
     *
     * @param String $T295_Id
     * @return String/bool
     */     
    public function imp007Finalizar($T295_Id)
    {
        global $g;
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        

        /**
         * Verificar se todos itens tem locação
         */
        $T296 = mysql_query("SELECT T066_D004_Id,
                                    T066_Id,
                                    D001_Codigo_Produto,
                            FROM T296
                            LEFT JOIN D009 ON D009_Id=T296_D009_Id
                            LEFT JOIN T066 ON T066_D009_Id=D009_Id
                            WHERE T296_T295_Id='{$T295_Id}'
                            GROUP BY T296_Id");
        $erro = '';
        while ($mT296 = mysql_fetch_array($T296)) {
            if ($mT296['T066_Id']<=0 || $mT296['T066_D004_Id']<=0){
                $erro .= "O produto {$mT296['D001_Codigo_Produto']} está sem locação.<br>";
                continue;
            }
        }

        if (!empty($erro)){
            return $erro;
        }

        $T296_T066_Id = mysql_query("SELECT T296_T066_Id FROM T296 WHERE T296_T295_Id='{$T295_Id}' and ifnull(T296_T066_Id,0)<=0");        
        if(mysql_num_rows($T296_T066_Id)>0){
          return "Existem produtos sem locação, favor preencher antes de atualizar o estoque.";
        }

        /**
         * SELECIONANDO TODOS OS ITENS DA DI
         */
        $T296 = mysql_query("SELECT T295_Id,
                                    T296_Id,
                                    T296_Quantidade,
                                    T296_D009_Id,
                                    T296_D006_Id,
                                    T295_D024_Id,
                                    T066_D004_Id,
                                    T066_Id,
                                    D049_Id,
                                    D001_Flag_Produto_Serie,
                                    T001_Id,
                                    T296_T066_Id
                            FROM T296 
                            LEFT JOIN T295 ON T295_Id='{$T295_Id}' 
                            LEFT JOIN D009 ON D009_Id=T296_D009_Id
                            LEFT JOIN D049 ON D049_Id=D009_D049_Id
                            LEFT JOIN D001 ON D001_Id=D049_D001_Id
                            LEFT JOIN T066 ON T066_Id=T296_T066_Id
                            LEFT JOIN T001 ON T001_T296_Id=T296_Id AND T001_Flag_Operacao!='C' AND T001_Descricao_Operacao like 'IMP-%'
                            WHERE T296_T295_Id='{$T295_Id}'
                            GROUP BY T296_Id");
        
        $T295  = mysql_query("SELECT T295_Data_Estoque FROM T295 WHERE T295_Id='{$T295_Id}'");
        $mT295 = mysql_fetch_array($T295);
        if ($mT295['T295_Data_Estoque'] != null && $mT295['T295_Data_Estoque'] != '0000-00-00') {
            return "Esse processo já foi realizado";
        }
        
        if (mysql_num_rows($T296) == 0) {
            return "Não existem produtos nessa DI";
        }
        
        // LOOPING PARA INSERIR NO HISTÓRICO DO PRODUTO
        while ($mT296 = mysql_fetch_array($T296)) {
            if ($mT296['T001_Id']>0){
                continue;
            }
            $sql = <<<EOT
            INSERT INTO T001 (
                T001_Descricao_Operacao,
                T001_Flag_Operacao,
                T001_Quantidade,
                T001_Data_Lancamento,
                T001_T296_Id,
                T001_D009_Id,
                T001_D006_Id,
                T001_D024_Id,
                T001_D004_Id,
                T001_T066_Id
            ) VALUES (
                'IMP-{$T295_Id}',
                'E',
                '{$mT296['T296_Quantidade']}',
                CURDATE(),
                '{$mT296['T296_Id']}',
                '{$mT296['T296_D009_Id']}',
                '{$mT296['T296_D006_Id']}',
                '{$mT296['T295_D024_Id']}',
                '{$mT296['T066_D004_Id']}',
                '{$mT296['T296_T066_Id']}'
            );
EOT;
            mysql_query($sql);
            $erro = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return "1 " . $erro;
            }
            
            $T006 = mysql_query("UPDATE T006 SET T006_T295_Id='' WHERE T006_T295_Id='{$T295_Id}' and T006_D009_Id='{$mT296['T296_D009_Id']}'");
            $erro = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return "2 " . $erro;
            }

            // $retorno = $CAD002->D001_reprocessa_historico(0, $mT296['T296_D009_Id'],100);
            $retornoProcessa = $CAD002->D001_reprocessa_historico(0, $mT296['T296_D009_Id'],100);
            if($retornoProcessa !== true){
                registrarLog(array("D009", $mT296['T296_D009_Id'], $g['empresaAtual'], " Erro ao reprocessar histórico - IMP005 - ATUALIZAR CUSTO:", $retornoProcessa, "S",  3));
                $retorno = $CAD002->D001_reprocessa_historico(0, $mT296['T296_D009_Id']);

                if ($retorno != true) {
                    mysql_query("ROLLBACK");
                    return "Erro reprocessa histórico: " . $retorno;
                }
            }
            
            if ($mT296['D001_Flag_Produto_Serie'] == 'S') {
                
                $quantidade = $mT296['T296_Quantidade'];
                
                for ($i = 1; $i <= $quantidade; $i++) {
                    
                    $D144 = <<<EOT
                        INSERT INTO D144 (
                            D144_D049_Id,
                            D144_T296_Id
                        ) VALUES (
                            '{$mT296['D049_Id']}',
                            '{$mT296['T296_Id']}'
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

        $T295 = mysql_query("UPDATE T295 SET T295_Data_Estoque=CURDATE() WHERE T295_Id='{$T295_Id}'");
        $erro = mysql_error();
        if ($erro) {
            mysql_query("ROLLBACK");
            return "3 " . $erro;
        }

        mysql_query("COMMIT");
        return true;
    }

    /**
     * IMP005AtualizarCusto
     *
     * @param String $T295_Id
     * @return String/bool
     */     
    public function IMP005AtualizarCusto($T295_Id)
    {
        global $g;
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        
        mysql_query("SET autocommit=0");
        mysql_query("START TRANSACTION");
        
        /**
         * SELECIONANDO TODOS OS ITENS DA DI
         */
        $T296 = mysql_query("SELECT *, T001_Valor_Custo_Unitario, T296_Valor_Custo_Final_Unitario, T296_Codigo_Produto FROM T295
                            LEFT JOIN T008 ON T008_T007_Id = T007_Id
                            LEFT JOIN T001 ON T001_T008_Id = T008_Id
                            LEFT JOIN T296 ON T296_D009_Id = T001_D009_Id AND T296_T295_Id = T295_Id 
                            WHERE T295_Id = '{$T295_Id}'");
        
        if (mysql_num_rows($T296) == 0) {
            return "Não existem produtos nessa DI";
        }
        
        // Loop para atualizar os custos na T001 (Histórico do Produto)
        while ($mT296 = mysql_fetch_array($T296)) {
            mysql_query("UPDATE T001 SET T001_Valor_Custo_Unitario = '{$mT296['T296_Valor_Custo_Final_Unitario']}' WHERE T001_Id ='{$mT296['T001_Id']}'");
            $erro = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return "1 " . $erro;
            }
            
            // $retorno = $CAD002->D001_reprocessa_historico(0, $mT296['T296_D009_Id'],100);
            $retornoProcessa = $CAD002->D001_reprocessa_historico(0, $mT296['T296_D009_Id'],100);
            if($retornoProcessa !== true){
                registrarLog(array("D009", $mT296['T296_D009_Id'], $g['empresaAtual'], " Erro ao reprocessar histórico - IMP005 - FINALIZAR:", $retornoProcessa, "S",  3));
                $retorno = $CAD002->D001_reprocessa_historico(0, $mT296['T296_D009_Id']);

                if ($retorno != true) {
                    mysql_query("ROLLBACK");
                    return "Erro reprocessa histórico: " . $retorno;
                }
            }
        }
        mysql_query("UPDATE T295 SET T295_Data_Atualizacao_Custo = now() WHERE T295_Id = '{$T295_Id}'");
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
     * @param String $T295_Id
     * @param String $D024_Id
     * @return String/bool
     */
    public function importarItensOC($T295_Id, $D024_Id)
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
                INSERT INTO T296 (
                    T296_Descricao_Produto,
                    T296_Quantidade,
                    T296_D037_Id,
                    T296_D005_Id,
                    T296_Valor_Custo_Unitario,
                    T296_Flag_ME,
                    T296_Aliquota_Imposto_Importacao,
                    T296_Aliquota_IPI,
                    T296_Aliquota_PIS,
                    T296_Aliquota_COFINS,
                    T296_D009_Id,
                    T296_Codigo_Produto,
                    T296_T295_Id,
                    T296_C004_Id,
                    T296_T225_Id
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
                    '{$T295_Id}',
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

    public function GerarXMLLI($T295_Id)
    {
        global $g;

        set_time_limit(0);
        $dir = $g['pathDados'] . 'tmp/';
        is_dir($dir) OR mkdir($dir, 0777, true);
        $filename             = "xml-li-{$T295_Id}.xml";
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
                   from T295
                   left join C004 on C004_Id=T295_C004_Id
                   left join C020 on C020_C004_Id=C004_Id
                   left join D024 on D024_Id=T295_D024_Id
                   left join D030 on D030_Id=D024_D030_Id
                   left join D020 on D020_Id=D024_D020_Id
                   left join D018 on D018_Id=D024_D018_Id
                   left join D053 on D053_D018_Id=D024_D018_Id AND D053_C004_Id=T295_C004_Id
                   left join T296 on T296_T295_Id=T295_Id  
                   left join D005 on D005_Id=T296_D005_Id
                   left join T270 on T270_T295_Id=T295_Id

                   where T295_Id='{$T295_Id}'
                   group by D005_Id
                   order by D005_Classificacao_Fiscal
EOT;
*/
 
        $cClausula = <<<EOT
                 SELECT T270_Numero_LI,
                        T270_Id,
                        T270_Numero_Processo_Anuente, 
                        T270_Sigla_Orgao_Anuente, 
                        T296_Item_Ac_Drawback,
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
                        T295_Numero_Drawback_Importacao,
                        T295_Codigo_URF_Entrada,   
                        T295_Codigo_URF_Despacho,                    
                        T295_Codigo_Moeda,
                        T295_Numero_DI,
                        T295_Codigo_Incoterms,
                        T295_Condicao_Mercadoria,
                        T295_Enquadramento_Material_Usado,
                        T295_Operacao_Enquadramento_Material_Usado ,
                        T270_Cobertura_Cambial,
                        T270_Modalidade_Pagamento          
                FROM T270
                LEFT JOIN T295 ON T295_id=T270_T295_id
                LEFT JOIN C004 ON C004_Id=T295_C004_Id
                LEFT JOIN C020 ON C020_C004_Id=C004_Id
                LEFT JOIN D024 ON D024_Id=T295_D024_Id
                LEFT JOIN D030 ON D030_Id=D024_D030_Id
                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                LEFT JOIN D018 ON D018_Id=D024_D018_Id
                LEFT JOIN T296 ON T296_id=T270_id
                LEFT JOIN D005 ON D005_Id=T296_D005_Id
                
                WHERE T295_Id='{$T295_Id}'
                ORDER BY T270_Numero_LI
EOT;
        $cSql_T295 = mysql_query($cClausula);
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

        while ($mT295 = mysql_fetch_array($cSql_T295)) {

              $li = $dom->createElement("li");

              $item = $dom->createElement("identificador-li","{$mT295['T270_Numero_LI']}");
              $li->appendChild($item);

              if($mT295['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){
                $tipoPessoa = 1;
              } else{
                $tipoPessoa = 2;
              }
              $item = $dom->createElement("tipo-importador","{$tipoPessoa}");
              $li->appendChild($item);
              $item = $dom->createElement("identificacao-importador","{$mT295['C004_Cnpj']}");
              $li->appendChild($item);
              $item = $dom->createElement("pais-procedencia-mercadoria","{$mT295['D030_Nome_Pais']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-urf-entrada","{$mT295['T295_Codigo_URF_Entrada']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-urf-despacho","{$mT295['T295_Codigo_URF_Despacho']}");
              $li->appendChild($item);
              $item = $dom->createElement("texto-informacoes-complementares","{$mT295['T270_Informacoes_Complementares']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-fornecedor","{$tipoPessoa}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-nome","{$mT295['D024_Nome_Empresa']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-email","{$mT295['D024_E_Mail']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-responsavel","{$mT295['D024_Nome_Contato']}");
              $li->appendChild($item);
              $item = $dom->createElement("pais-aquisicao-mercadoria","{$mT295['D030_Nome_Pais']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-logradouro","{$mT295['D024_Endereco']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-numero","{$mT295['D024_Numero']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-complemento","{$mT295['D024_Complemento']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-cidade","{$mT295['D020_Nome_Cidade']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-uf","{$mT295['D018_UF']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-nome", "{$mT295['D024_Nome_Empresa']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-email","{$mT295['D024_E_Mail']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-responsavel","D024_Nome_Contato");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-logradoudo","{$mT295['D024_Endereco']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-numero","{$mT295['D024_Numero']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-complemento", "{$mT295['D024_Complemento']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-cidade","{$mT295['D020_Nome_Cidade']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-estado","{$mT295['D018_UF']}");
              $li->appendChild($item);
              $item = $dom->createElement("pais-origem-mercadoria","{$mT295['D030_Nome_Pais']}");
              $li->appendChild($item);
              $item = $dom->createElement("subitem-ncm","{$mT295['D005_Classificacao_Fiscal']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-mercadoria-naladi","{$mT295['D005_Classificacao_Fiscal']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-moeda","{$mT295['T295_Codigo_Moeda']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-incoterms","{$mT295['T295_Codigo_Incoterms']}");
              $li->appendChild($item);
              $item = $dom->createElement("condicao-mercadoria","{$mT295['T295_Condicao_Mercadoria']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-enquadramento-material-usado", "{$mT295['T295_Enquadramento_Material_Usado']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-operacao-enquadramento-material-usado", "{$mT295['T295_Operacao_Enquadramento_Material_Usado']}");
              $li->appendChild($item);
              $item = $dom->createElement("destaque-ncm");
                $subItem = $dom->createElement("numero-destaque-ncm","{$mT295['T270_Numero_Destaque_NCM']}");
                $item->appendChild($subItem);
              $li->appendChild($item);
              $item = $dom->createElement("processo-anuente");
                $subItem = $dom->createElement("numero-processo-anuente", "{$mT295['T270_Numero_Processo_Anuente']}");
                $item->appendChild($subItem);
                $subItem = $dom->createElement("sigla-orgao-anuente", "{$mT295['T270_Sigla_Orgao_Anuente']}");
                $item->appendChild($subItem);
              $li->appendChild($item);
              $item = $dom->createElement("drawback-regime","{$mT295['T295_Numero_Drawback_Importacao']}");
              $li->appendChild($item);
              $item = $dom->createElement("drawback-numero-ato-isencao", "{$mT295['T270_Drawback_Ato_Isencao']}");
              $li->appendChild($item);
              $item = $dom->createElement("drawback-numero-ato-suspencao", "{$mT295['T270_Drawback_Ato_Suspensao']}");
              $li->appendChild($item);            
              $item = $dom->createElement("detalhe-ncm-item-drawback");
                $seqItem = 1;
                $T296 = mysql_query("select D037_Unidade, 
                                            T296_Peso_Kg_Liquido_Unitario, 
                                            T296_Quantidade, 
                                            T296_Valor_Custo_Unitario, 
                                            T296_Descricao_Produto, 
                                            D082_Marca,
                                            T296_Item_Ac_Drawback 
                                      FROM T296 
                                      LEFT JOIN D037 on D037_Id=T296_D037_Id 
                                      LEFT JOIN D009 ON D009_Id=T296_d009_Id 
                                      LEFT JOIN D049 ON D049_id=D009_D049_Id
                                      LEFT JOIN D082 ON D082_id=D049_D082_Id 
                                      WHERE T296_T270_Id='{$mT295['T270_Id']}'");
                while ($mT296 = mysql_fetch_array($T296)){

                        $subItem = $dom->createElement("numero-sequencial-produto","{$seqItem}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("nome-unidade-medida-comercializada","{$mT296['D037_Unidade']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("peso-liquido","{$mT296['T296_Peso_Kg_Liquido_Unitario']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("qtd-mercadoria-unidade-xcomercializada","{$mT296['T296_Quantidade']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("qtd-mercadoria-unidade-estatistica","{$mT296['T296_Quantidade']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("valor-unidade-condicao-venda","{$mT296['T296_Valor_Custo_Unitario']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("valor-unidade-local-embarque","{$mT296['T296_Valor_Custo_Unitario']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("descricao-produto","{$mT296['T296_Descricao_Produto']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("marca", "{$mT296['D082_Marca']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("modelo");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("numero-serie");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("ano-fabricacao");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("item-ac-drawback", "{$mT296['T296_Item_Ac_Drawback']}");
                        $item->appendChild($subItem);
                        $seqItem++;
                }
              $li->appendChild($item);
              $item = $dom->createElement("regime-acordo-tributario","{$mT295['T270_Regime_Acordo_Tributario']}");
              $li->appendChild($item);
              $item = $dom->createElement("fundamento-legal-regime", "{$mT295['T270_Fundamento_Legal_Regime']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-acordo-tarifario", "{$mT295['T270_Tipo_Acordo_Tarifario']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-acordo-aladi", "{$mT295['T270_Codigo_Acordo_Alardi']}");
              $li->appendChild($item);
              $item = $dom->createElement("cobertura-cambial","{$mT295['T270_Cobertura_Cambial']}");
              $li->appendChild($item);
              $item = $dom->createElement("modalidade-pagamento","{$mT295['T270_Modalidade_Pagamento']}");
              $li->appendChild($item);
              $item = $dom->createElement("numero-dias-limite-pagamento", "{$mT295['T270_Numero_Dias_Limite_Pagamento']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-orgao-financeiro-internacional", "{$mT295['T270_Codigo_Orgao_Financeiro_Internacional']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-motivo-sem-cobertura", "{$mT295['T270_Codigo_Motivo_Sem_Cobertura']}");
              $li->appendChild($item);
              $item = $dom->createElement("numero-li-substituida", "{$mT295['T270_Numero_LI']}");
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
    public function geraDownloadXMLLI($T295_Id)
    {
        global $g;
        
        $dir = $g['pathDados'] . 'tmp/';
        $filename             = "xml-li-{$T295_Id}.xml";
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

      log("Acaoid= ". $acaoId);

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

                    $cInsert = "INSERT INTO T296 
                                      (T296_T295_Id, /*acaoId*/
                                       T296_D009_Id, /*Id Produto Marca*/
                                       T296_Codigo_Produto, /*[0]*/  
                                       T296_Descricao_Produto, /*descricao produto*/ 
                                       T296_Quantidade,    /*[1]*/ 
                                       T296_D037_Id,/*[2] - unidade*/
                                       T296_D005_Id,/*[3] - ncm*/
                                       T296_Valor_Custo_Unitario, /*[4]*/ 
                                       T296_Flag_ME,  /*[5]*/ 
                                       T296_Peso_Kg_Liquido_Unitario, /*[6]*/ 
                                       T296_Aliquota_Imposto_Importacao, /*[7]*/ 
                                       T296_Aliquota_IPI, /*[8]*/ 
                                       T296_Aliquota_PIS, /*[9]*/ 
                                       T296_Aliquota_COFINS,/*[10]*/ 
                                       T296_Valor_Adicao,/*[11]*/
                                       T296_Adicao_Item, /*Adicao item*/ 
                                       T296_T066_Id,/*[12] - Local*/
                                       T296_Item_Ac_Drawback,/*[13]*/ 
                                       T296_T270_Id, /*[14] LI*/
                                       T296_Aliquota_ICMS,/*[15]*/ 
                                       T296_Aliquota_ICMS_Presumido,/*[16]*/ 
                                       T296_Percentual_Reducao_ICMS,/*[17]*/ 
                                       T296_Percentual_Reducao_ICMS_Presumido,/*[18]*/ 
                                       T296_Aliquota_ICMS_PIS_COFINS,/*[19]*/ 
                                       T296_Aliquota_ICMS_Credito_Presumido)/*[20]*/ 
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


                    $cSQL_T296 = mysql_query($cInsert);
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
            $baseICMS  = "T296_Valor_Base_ICMS_Sem_Reducao";
            $valorICMS = "T296_Valor_ICMS";
        } else {
            //caso o tipo da D.I no cadastro do porto seja Simplificada
            $baseICMS  = "T296_Valor_Base_ICMS_Normal";
            $valorICMS = "T296_Valor_ICMS_Presumido";
        }

        $array = array();        
        $array['baseICMS']  = $baseICMS;
        $array['valorICMS'] = $valorICMS;

        return $array;
    }

     /**
     * retornaFreteTotal
     *
     * @param String $T296_Valor_Total_Frete
     * @return array
     */

    public function retornaFreteTotal($T296_Valor_Total_Frete)
    {        
        // recebe a soma do T296_Valor_Total_Frete
        $array = array();
        $array['valorTotalFrete'] = 0;

        return $array;
    }
    
     /**
     * retornaFrete
     *
     * @param String $T296_Valor_Total_Frete
     * @return array
     */

    public function retornaFrete($T296_Valor_Total_Frete)
    {     
        $array = array();
        $array['valorFrete'] = 0;

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
     * gerarPedido
     *
     * @param String $T295_Id
     * @return String/bool
     */
    public function gerarDI($T295_Id, $D024_Id){
        global $g;

        if($D024_Id){
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            $T295 = mysql_query("SELECT * FROM T295 WHERE T295_Id='{$T295_Id}'");
            $rowT295 = mysql_fetch_array($T295);
            
            $insertT075 = array(
                "T075_D018_Id"                               => $rowT295['T295_D018_Id'],
                "T075_T054_Id"                               => $rowT295['T295_T054_Id'],
                "T075_Data_Emissao_DI"                       => date('Y-m-d'),
                "T075_Numero_DI"                             => $rowT295['T295_Numero_DI'],
                "T075_Valor_Produtos"                        => $rowT295['T295_Valor_Produtos'],
                "T075_Valor_Frete"                           => $rowT295['T295_Valor_Frete'],
                "T075_Valor_CIF"                             => $rowT295['T295_Valor_CIF'],
                "T075_Valor_THC"                             => $rowT295['T295_Valor_THC'],
                "T075_Valor_SISCOMEX"                        => $rowT295['T295_Valor_SISCOMEX'],
                "T075_Valor_Imposto_Importacao"              => $rowT295['T295_Valor_Imposto_Importacao'],
                "T075_Valor_IPI"                             => $rowT295['T295_Valor_IPI'],
                "T075_Valor_PIS"                             => $rowT295['T295_Valor_PIS'],
                "T075_Valor_COFINS"                          => $rowT295['T295_Valor_COFINS'],
                "T075_Valor_DI"                              => $rowT295['T295_Valor_DI'],
                "T075_Local_Desembaraco"                     => $rowT295['T295_Local_Desembaraco'],
                "T075_Data_Desembaraco"                      => $rowT295['T295_Data_Desembaraco'],
                "T075_Codigo_Exportador"                     => $rowT295['T295_Codigo_Exportador'],
                "T075_D024_Id"                               => $rowT295['T295_D024_Id'],
                "T075_D118_Id"                               => $rowT295['T295_D118_Id'],
                "T075_D119_Id"                               => $rowT295['T295_D119_Id'],
                "T075_Cotacao_ME"                            => $rowT295['T295_Cotacao_ME'],
                "T075_Valor_Frete_Incluso"                   => $rowT295['T295_Valor_Frete_Incluso'],
                "T075_Valor_ICMS"                            => $rowT295['T295_Valor_ICMS'],
                "T075_Valor_Seguro"                          => $rowT295['T295_Valor_Seguro'],
                "T075_Valor_Despesa"                         => $rowT295['T295_Valor_Despesa'],
                "T075_Observacao_DI"                         => $rowT295['T295_Observacao_DI'],
                "T075_Observacao_NF"                         => $rowT295['T295_Observacao_NF'],
                "T075_C004_Id"                               => $rowT295['T295_C004_Id'],
                "T075_Data_Embarque"                         => $rowT295['T295_Data_Embarque'],
                "T075_ETA_Data"                              => $rowT295['T295_ETA_Data'],
                "T075_ETA_Hora"                              => $rowT295['T295_ETA_Hora'],
                "T075_D044_Id"                               => $rowT295['T295_D044_Id'],
                "T075_Aliquota_ICMS"                         => $rowT295['T295_Aliquota_ICMS'],
                "T075_Aliquota_ICMS_Presumido"               => $rowT295['T295_Aliquota_ICMS_Presumido'],
                "T075_Percentual_Reducao_ICMS"               => $rowT295['T295_Percentual_Reducao_ICMS'],
                "T075_Percentual_Reducao_ICMS_Presumido"     => $rowT295['T295_Percentual_Reducao_ICMS_Presumido'],
                "T075_Valor_VMLE"                            => $rowT295['T295_Valor_VMLE'],
                "T075_Valor_VMLD"                            => $rowT295['T295_Valor_VMLD'],
                "T075_Aliquota_ICMS_PIS_COFINS"              => $rowT295['T295_Aliquota_ICMS_PIS_COFINS'],
                "T075_D006_Id"                               => $rowT295['T295_D006_Id'],
                "T075_Aliquota_ICMS_Credito_Presumido"       => $rowT295['T295_Aliquota_ICMS_Credito_Presumido'],
                "T075_Peso_Liquido"                          => $rowT295['T295_Peso_Liquido'],
                "T075_Peso_Bruto"                            => $rowT295['T295_Peso_Bruto'],
                "T075_C007_Id"                               => $rowT295['T295_C007_Id'],
                "T075_Ordem_Compra"                          => $rowT295['T295_Ordem_Compra'],
                "T075_Data_Previsao"                         => $rowT295['T295_Data_Previsao'],
                "T075_Data_Estoque"                          => $rowT295['T295_Data_Estoque'],
                "T075_Valor_AFRMM"                           => $rowT295['T295_Valor_AFRMM'],
                "T075_Data_Atualizacao_Custo"                => $rowT295['T295_Data_Atualizacao_Custo'],
                "T075_Flag_MP"                               => $rowT295['T295_Flag_MP'],
                "T075_Data_Licenca_Importacao"               => $rowT295['T295_Data_Licenca_Importacao'],
                "T075_Calculo_THC"                           => $rowT295['T295_Calculo_THC'],
                "T075_Somar_PIS_Custo"                       => $rowT295['T295_Somar_PIS_Custo'],
                "T075_Somar_COFINS_Custo"                    => $rowT295['T295_Somar_COFINS_Custo'],
                "T075_Somar_Seguro_Custo"                    => $rowT295['T295_Somar_Seguro_Custo'],
                "T075_Somar_AFRMM_Custo"                     => $rowT295['T295_Somar_AFRMM_Custo'],
                "T075_Somar_ICMS_Pagar_Custo"                => $rowT295['T295_Somar_ICMS_Pagar_Custo'],
                "T075_Somar_IPI_Custo"                       => $rowT295['T295_Somar_IPI_Custo'],
                "T075_UF_Desembaraco"                        => $rowT295['T295_UF_Desembaraco'],
                "T075_Codigo_Fabricante"                     => $rowT295['T295_Codigo_Fabricante'],
                "T075_Flag_Transporte_DI"                    => $rowT295['T295_Flag_Transporte_DI'],
                "T075_Flag_Tipo_Intermedio_DI"               => $rowT295['T295_Flag_Tipo_Intermedio_DI'],
                "T075_CNPJ_Importador"                       => $rowT295['T295_CNPJ_Importador'],
                "T075_UF_Importador"                         => $rowT295['T295_UF_Importador'],
                "T075_Numero_Drawback_Importacao"            => $rowT295['T295_Numero_Drawback_Importacao'],
                "T075_Cotacao_CC"                            => $rowT295['T295_Cotacao_CC'],
                "T075_Codigo_URF_Entrada"                    => $rowT295['T295_Codigo_URF_Entrada'],
                "T075_Codigo_URF_Despacho"                   => $rowT295['T295_Codigo_URF_Despacho'],
                "T075_Codigo_Moeda"                          => $rowT295['T295_Codigo_Moeda'],
                "T075_Codigo_Incoterms"                      => $rowT295['T295_Codigo_Incoterms'],
                "T075_Condicao_Mercadoria"                   => $rowT295['T295_Condicao_Mercadoria'],
                "T075_Enquadramento_Material_Usado"          => $rowT295['T295_Enquadramento_Material_Usado'],
                "T075_Operacao_Enquadramento_Material_Usado" => $rowT295['T295_Operacao_Enquadramento_Material_Usado'],
                "T075_Data_Inicio_Demurrage"                 => $rowT295['T295_Data_Inicio_Demurrage'],
                "T075_Siscoserv_RAS"                         => $rowT295['T295_Siscoserv_RAS'],
                "T075_Siscoserv_RP"                          => $rowT295['T295_Siscoserv_RP'],
                "T075_Orgao_Desembaraco"                     => $rowT295['T295_Orgao_Desembaraco'],
                "T075_Modelo_Calculos"                       => $g['C031']['modeloCalculosDI']
            );

            $sqlChaves = '';
            $sqlDados = '';
            foreach ($insertT075 as $key => $value) {
                $sqlChaves .= $sqlChaves ? ',' : '';
                $sqlChaves .= $key;
                $sqlDados .= $sqlDados ? ',' : '';
                $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
            }
            $sql = "INSERT INTO T075 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
            mysql_query($sql);
            $error = mysql_error();

            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return '1 Erro: ' . $error;
            }

            $T075_Id = $g['mysqlLastId'];

            /*if($g['C031']['modeloCalculosDI'] == '2'){
                // insere pacote de despesas se o modelo da DI for tipo 2 (calculos formados pelas despesas)
                $T300 = mysql_query("SELECT D191_Descricao_Despesa,
                                            D191_Tipo_Despesa,
                                            T300_D190_Id,
                                            T300_Aliquota,
                                            T300_Flag_Tipo,
                                            T300_Valor,
                                            T300_Formula,
                                            T300_Data,
                                            T300_Ordem,
                                            T300_Flag_Soma_Base_IPI,
                                            T300_Flag_Soma_Base_ICMS,
                                            T300_Flag_Soma_Base_PIS,
                                            T300_Flag_Soma_Base_COFINS,
                                            T300_Flag_Soma_Base_II,
                                            T300_Flag_Soma_Custo,
                                            T300_Flag_Soma_Preco_NFe,
                                            T300_Flag_Tipo_Rateio,
                                            T300_Flag_ME,
                                            T300_Cotacao_ME                                            
                                       FROM T300 
                                  LEFT JOIN D191 ON T300_D191_Id = D191_Id
                                      WHERE T300_T295_Id = '{$T295_Id}'
                                   ORDER BY T300_Ordem ASC");
                while($mT300 = mysql_fetch_array($T300)){

                    //faz o insert do pacote na DI
                    mysql_query("INSERT INTO T168 (T168_T075_Id,                    
                                                   T168_D190_Id,
                                                   T168_Descricao,
                                                   T168_Valor,
                                                   T168_Flag_ME,
                                                   T168_Aliquota,
                                                   T168_Flag_Tipo,
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
                                                   T168_Cotacao_ME,
                                                   T168_Tipo_Despesa,
                                                   T168_Data
                                          )VALUES(
                                                  '{$T075_Id}', 
                                                  '{$mT300['T300_D190_Id']}',   
                                                  '{$mT300['D191_Descricao_Despesa']}',
                                                  '{$mT300['T300_Valor']}',
                                                  '{$mT300['T300_Flag_ME']}',
                                                  '{$mT300['T300_Aliquota']}',
                                                  '{$mT300['T300_Flag_Tipo']}',
                                                  '{$mT300['T300_Formula']}',
                                                  '{$mT300['T300_Ordem']}',
                                                  '{$mT300['T300_Flag_Soma_Base_IPI']}',
                                                  '{$mT300['T300_Flag_Soma_Base_ICMS']}',
                                                  '{$mT300['T300_Flag_Soma_Base_PIS']}',
                                                  '{$mT300['T300_Flag_Soma_Base_COFINS']}',
                                                  '{$mT300['T300_Flag_Soma_Base_II']}',
                                                  '{$mT300['T300_Flag_Soma_Custo']}',
                                                  '{$mT300['T300_Flag_Soma_Preco_NFe']}',
                                                  '{$mT300['T300_Flag_Tipo_Rateio']}',
                                                  '{$mT300['T300_Cotacao_ME']}',
                                                  '{$mT300['D191_Tipo_Despesa']}',
                                                  now())");
                    $error = mysql_error();

                    if(!empty($error)){
                        mysql_query("ROLLBACK");
                        return 'Houve um erro ao tentar incluir o pacote de depesas <br>'.$error;
                    }
                }
            }*/
            
            $T296 = mysql_query("SELECT * 
                                   FROM T296
                              LEFT JOIN D009 ON D009_Id=T296_D009_Id
                              LEFT JOIN T066 ON T066_Id=T296_T066_Id
                              LEFT JOIN D004 ON D004_Id=T066_D004_Id
                              LEFT JOIN D005 ON D005_Id=T296_D005_Id
                              LEFT JOIN D049 ON D049_Id=D009_D049_Id
                              LEFT JOIN D001 ON D001_Id=D049_D001_Id
                              LEFT JOIN D001A ON D001_Id=D001A_D001_Id
                              LEFT JOIN D082 ON D082_Id=D049_D082_Id
                              LEFT JOIN T225 ON T225_Id=T296_T225_Id
                              LEFT JOIN T219 ON T219_Id=T225_T219_Id
                              LEFT JOIN T006 ON T006_Id=T219_T006_Id
                              LEFT JOIN T270 ON T270_Id=T296_T270_Id
                              LEFT JOIN T295 ON T295_Id=T296_T295_Id
                                  WHERE T295_D024_Id = '{$D024_Id}' 
                                    AND T296_Flag_Finalizar_Compra = 'N'
                                    AND T296_Quantidade_DI > 0");

            // Vai gravar todos os T295_Id para atualizar o status            
            $arrayT295 = array();
            while($rowT296 = mysql_fetch_assoc($T296)) {
                if($rowT296['T296_Quantidade_DI'] == 0){
                    continue;
                }

                $arrayT295[] = $rowT296['T295_Id'];
                
                $insertT055 = array(
                    "T055_T075_Id"                                     => $T075_Id,
                    "T055_T296_Id"                                     => $rowT296['T296_Id'],
                    "T055_T060_Id"                                     => $rowT296['T296_T060_Id'],
                    "T055_T054_Id"                                     => $rowT296['T296_T054_Id'],
                    "T055_D037_Id"                                     => $rowT296['T296_D037_Id'],
                    "T055_D005_Id"                                     => $rowT296['T296_D005_Id'],
                    "T055_D009_Id"                                     => $rowT296['T296_D009_Id'],
                    "T055_T053_Id"                                     => $rowT296['T296_T053_Id'],
                    "T055_C004_Id"                                     => $rowT296['T296_C004_Id'],
                    "T055_D024_Id"                                     => $rowT295['T295_D024_Id'],
                    "T055_Codigo_Produto"                              => $rowT296['T296_Codigo_Produto'],
                    "T055_Codigo_Produto_Fornecedor"                   => $rowT296['T296_Codigo_Produto_Fornecedor'],
                    "T055_Descricao_Produto"                           => $rowT296['D001_Descricao_Produto'],
                    "T055_Descricao_Produto_Ingles"                    => $rowT296['T296_Descricao_Produto'],
                    "T055_Classificacao_Fiscal"                        => $rowT296['T296_Classificacao_Fiscal'],
                    "T055_Prazo_Entrega"                               => $rowT296['T296_Prazo_Entrega'],
                    "T055_Quantidade"                                  => $rowT296['T296_Quantidade_DI'],
                    "T055_Valor_Custo_Unitario_Anterior"               => $rowT296['T296_Valor_Custo_Unitario_Anterior'],
                    "T055_Valor_Custo_Unitario"                        => $rowT296['T296_Valor_Custo_Unitario'],
                    "T055_Valor_Total_Custo"                           => $rowT296['T296_Valor_Total_Custo'],
                    "T055_Valor_Preco_Unitario_Nota_Fiscal"            => $rowT296['T296_Valor_Preco_Unitario_Nota_Fiscal'],
                    "T055_Valor_Total_Preco_Nota_Fiscal"               => $rowT296['T296_Valor_Total_Preco_Nota_Fiscal'],
                    "T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal" => $rowT296['T296_Valor_Total_Despesas_Acessorias_Nota_Fiscal'],
                    "T055_Valor_Custo_Unitario_Original"               => $rowT296['T296_Valor_Custo_Unitario_Original'],
                    "T055_Valor_Total_Custo_Original"                  => $rowT296['T296_Valor_Total_Custo_Original'],
                    "T055_Valor_Base_IPI"                              => $rowT296['T296_Valor_Base_IPI'],
                    "T055_Valor_Base_ICMS_Sem_Reducao"                 => $rowT296['T296_Valor_Base_ICMS_Sem_Reducao'],
                    "T055_Valor_Base_ICMS_Normal"                      => $rowT296['T296_Valor_Base_ICMS_Normal'],
                    "T055_Valor_Base_ICMS_Presumido"                   => $rowT296['T296_Valor_Base_ICMS_Presumido'],
                    "T055_Valor_Base_COFINS"                           => $rowT296['T296_Valor_Base_COFINS'],
                    "T055_Valor_Base_PIS"                              => $rowT296['T296_Valor_Base_PIS'],
                    "T055_Valor_Base_Extra"                            => $rowT296['T296_Valor_Base_Extra'],
                    "T055_Valor_Base_Imposto_Importacao"               => $rowT296['T296_Valor_Base_Imposto_Importacao'],
                    "T055_Aliquota_IPI"                                => $rowT296['T296_Aliquota_IPI'],
                    "T055_Aliquota_Imposto_Importacao"                 => $rowT296['T296_Aliquota_Imposto_Importacao'],
                    "T055_Aliquota_COFINS"                             => $rowT296['T296_Aliquota_COFINS'],
                    "T055_Aliquota_PIS"                                => $rowT296['T296_Aliquota_PIS'],
                    "T055_Aliquota_Extra"                              => $rowT296['T296_Aliquota_Extra'],
                    "T055_Valor_ICMS"                                  => $rowT296['T296_Valor_ICMS'],
                    "T055_Valor_ICMS_Presumido"                        => $rowT296['T296_Valor_ICMS_Presumido'],
                    "T055_Valor_ICMS_Pagar"                            => $rowT296['T296_Valor_ICMS_Pagar'],
                    "T055_Valor_IPI"                                   => $rowT296['T296_Valor_IPI'],
                    "T055_Valor_Imposto_Importacao"                    => $rowT296['T296_Valor_Imposto_Importacao'],
                    "T055_Valor_COFINS"                                => $rowT296['T296_Valor_COFINS'],
                    "T055_Valor_PIS"                                   => $rowT296['T296_Valor_PIS'],
                    "T055_Valor_Extra"                                 => $rowT296['T296_Valor_Extra'],
                    "T055_Valor_Frete"                                 => $rowT296['T296_Valor_Frete'],
                    "T055_Valor_Despesas"                              => $rowT296['T296_Valor_Despesas'],
                    "T055_Valor_Despesas_Fatura"                       => $rowT296['T296_Valor_Despesas_Fatura'],
                    "T055_Valor_Aduaneira"                             => $rowT296['T296_Valor_Aduaneira'],
                    "T055_Valor_Despesas_Aduaneira"                    => $rowT296['T296_Valor_Despesas_Aduaneira'],
                    "T055_Valor_Custo_Final_Unitario"                  => $rowT296['T296_Valor_Custo_Final_Unitario'],
                    "T055_Valor_Total_Custo_Final"                     => $rowT296['T296_Valor_Total_Custo_Final'],
                    "T055_Peso_Kg_Liquido_Unitario"                    => $rowT296['T296_Peso_Kg_Liquido_Unitario'],
                    "T055_Peso_Kg_Liquido_Total"                       => $rowT296['T296_Peso_Kg_Liquido_Total'],
                    "T055_Peso_Lb_Liquido_Unitario"                    => $rowT296['T296_Peso_Lb_Liquido_Unitario'],
                    "T055_Peso_Lb_Liquido_Total"                       => $rowT296['T296_Peso_Lb_Liquido_Total'],
                    "T055_Multiplicador_X"                             => $rowT296['T296_Multiplicador_X'],
                    "T055_Multiplicador_Y"                             => $rowT296['T296_Multiplicador_Y'],
                    "T055_Data_Transferencia_Estoque"                  => $rowT296['T296_Data_Transferencia_Estoque'],
                    "T055_Valor_Total_ICMS"                            => $rowT296['T296_Valor_Total_ICMS'],
                    "T055_Valor_Total_ICMS_Presumido"                  => $rowT296['T296_Valor_Total_ICMS_Presumido'],
                    "T055_Valor_Total_ICMS_Pagar"                      => $rowT296['T296_Valor_Total_ICMS_Pagar'],
                    "T055_Valor_Total_IPI"                             => $rowT296['T296_Valor_Total_IPI'],
                    "T055_Valor_Total_Imposto_Importacao"              => $rowT296['T296_Valor_Total_Imposto_Importacao'],
                    "T055_Valor_Total_COFINS"                          => $rowT296['T296_Valor_Total_COFINS'],
                    "T055_Valor_Total_PIS"                             => $rowT296['T296_Valor_Total_PIS'],
                    "T055_Valor_Total_Extra"                           => $rowT296['T296_Valor_Total_Extra'],
                    "T055_Valor_Total_Frete"                           => $rowT296['T296_Valor_Total_Frete'],
                    "T055_Valor_Total_Despesas"                        => $rowT296['T296_Valor_Total_Despesas'],
                    "T055_Valor_Total_Despesas_Fatura"                 => $rowT296['T296_Valor_Total_Despesas_Fatura'],
                    "T055_Valor_Total_Aduaneira"                       => $rowT296['T296_Valor_Total_Aduaneira'],
                    "T055_Valor_Total_Despesas_Aduaneira"              => $rowT296['T296_Valor_Total_Despesas_Aduaneira'],
                    "T055_Valor_Total_Base_IPI"                        => $rowT296['T296_Valor_Total_Base_IPI'],
                    "T055_Valor_Total_Base_ICMS_Sem_Reducao"           => $rowT296['T296_Valor_Total_Base_ICMS_Sem_Reducao'],
                    "T055_Valor_Total_Base_ICMS_Normal"                => $rowT296['T296_Valor_Total_Base_ICMS_Normal'],
                    "T055_Valor_Total_Base_ICMS_Presumido"             => $rowT296['T296_Valor_Total_Base_ICMS_Presumido'],
                    "T055_Valor_Total_Base_COFINS"                     => $rowT296['T296_Valor_Total_Base_COFINS'],
                    "T055_Valor_Total_Base_PIS"                        => $rowT296['T296_Valor_Total_Base_PIS'],
                    "T055_Valor_Total_Base_Extra"                      => $rowT296['T296_Valor_Total_Base_Extra'],
                    "T055_Valor_Total_Base_Imposto_Importacao"         => $rowT296['T296_Valor_Total_Base_Imposto_Importacao'],
                    "T055_Peso_Kg_Liquido_Unitario_Fatura"             => $rowT296['T296_Peso_Kg_Liquido_Unitario_Fatura'],
                    "T055_Peso_Kg_Liquido_Total_Fatura"                => $rowT296['T296_Peso_Kg_Liquido_Total_Fatura'],
                    "T055_Valor_Adicao"                                => $rowT296['T296_Valor_Adicao'],
                    "T055_Flag_ME"                                     => $rowT296['T296_Flag_ME'],
                    "T055_D006_Id"                                     => $rowT296['T296_D006_Id'],
                    "T055_Flag_Frete"                                  => $rowT296['T296_Flag_Frete'],
                    "T055_Valor_AFRMM"                                 => $rowT296['T296_Valor_AFRMM'],
                    "T055_Valor_Total_AFRMM"                           => $rowT296['T296_Valor_Total_AFRMM'],
                    "T055_Aliquota_ICMS"                               => $rowT296['T296_Aliquota_ICMS'],
                    "T055_Aliquota_ICMS_Presumido"                     => $rowT296['T296_Aliquota_ICMS_Presumido'],
                    "T055_Percentual_Reducao_ICMS"                     => $rowT296['T296_Percentual_Reducao_ICMS'],
                    "T055_Percentual_Reducao_ICMS_Presumido"           => $rowT296['T296_Percentual_Reducao_ICMS_Presumido'],
                    "T055_Aliquota_ICMS_PIS_COFINS"                    => $rowT296['T296_Aliquota_ICMS_PIS_COFINS'],
                    "T055_Aliquota_ICMS_Credito_Presumido"             => $rowT296['T296_Aliquota_ICMS_Credito_Presumido'],
                    "T055_Adicao_Item"                                 => $rowT296['T296_Adicao_Item'],
                    "T055_T225_Id"                                     => $rowT296['T296_T225_Id'],
                    "T055_Flag_Calculo_Manual"                         => $rowT296['T296_Flag_Calculo_Manual'],
                    "T055_Valor_Total_Multa"                           => $rowT296['T296_Valor_Total_Multa'],
                    "T055_Valor_Juros_Multa"                           => $rowT296['T296_Valor_Juros_Multa'],
                    "T055_Valor_Dumping"                               => $rowT296['T296_Valor_Dumping'],
                    "T055_Valor_Seguro"                                => $rowT296['T296_Valor_Seguro'],
                    "T055_Valor_Total_Seguro"                          => $rowT296['T296_Valor_Total_Seguro'],
                    "T055_Valor_Custo_Debug"                           => $rowT296['T296_Valor_Custo_Debug'],
                    "T055_Valor_Base_II_Debug"                         => $rowT296['T296_Valor_Base_II_Debug'],
                    "T055_Cotacao_CC"                                  => $rowT296['T296_Cotacao_CC'],
                    "T055_Item_Ac_Drawback"                            => $rowT296['T296_Item_Ac_Drawback'],
                    "T055_Unidade_Medida"                              => $rowT296['T296_Unidade_Medida'],
                    "T055_T270_Id"                                     => $rowT296['T296_T270_Id'],
                    "T055_T066_Id"                                     => $rowT296['T296_T066_Id'],
                    "T055_Percentual_Acrescimo_Impostos"               => $rowT296['T296_Percentual_Acrescimo_Impostos']
                );
                
                $sqlChaves = '';
                $sqlDados = '';
                foreach ($insertT055 as $key => $value) {
                    $sqlChaves .= $sqlChaves ? ',' : '';
                    $sqlChaves .= $key;
                    $sqlDados .= $sqlDados ? ',' : '';
                    $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
                }
                $sql = "INSERT INTO T055 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
                mysql_query($sql);

   
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '2 Erro: ' . mysql_error();
                }

                $T055_Id = $g['mysqlLastId'];
                mysql_query("INSERT INTO T055A (T055A_T055_Id,
                                                T055A_Largura,
                                                T055A_Altura,
                                                T055A_Comprimento)
                                        VALUES ('{$T055_Id}',
                                                '{$rowT296['D001A_Largura']}',
                                                '{$rowT296['D001A_Altura']}',
                                                '{$rowT296['D001A_Comprimento']}')");                
            }

            $atualizaStatus = $this->alteraStatusPO($arrayT295);
            if($atualizaStatus !== true){
                mysql_query("ROLLBACK");
                return $atualizaStatus;
            }

            $ret = $this->limparQuantidadeDI($D024_Id);

            if ($ret === true) {
                mysql_query("COMMIT");
                return array('T075_Id' => $T075_Id);
            } else {
                mysql_query("ROLLBACK");
                return $ret;
            }
        } else {
            return "ERRO: D024_Id não existe";
        }

    }

    public function limparQuantidadeDI($D024_Id) {
        $erro=0;
		mysql_query("START_TRANSACTION");
		$T296 = mysql_query("SELECT T296_Id
                            FROM T296
                            LEFT JOIN T295 ON T295_Id=T296_T295_Id
                            WHERE T295_D024_Id = $D024_Id");
		while($mT296 = mysql_fetch_array($T296)){
			$upT296 = mysqli_query("UPDATE T296 SET T296_Quantidade_DI = 0 WHERE T296_Id='{$mT296['T296_Id']}'");
			$erro = mysql_error();
			if($erro){
				mysql_query("ROLLBACK");
				$erro=1;
			}
		}
		mysql_query("COMMIT");

		if($erro==0){
			return true;
		} else {
			return "Houve algum erro ao atualizar a quantidade. Por favor, contato o suporte técnico.<br><br>" . $erro;
		}
    }

    /**
     * aplicarAlcadaOC
     *
     * @param int $T295_Id
     */
    public function aplicarAlcadaPO($T295_Id)
    {
        global $g;
        
        $sqlT295 = mysql_query("SELECT * 
                                  FROM T295 
                             LEFT JOIN C007 ON T295_C007_Id = C007_Id 
                                 WHERE T295_Id = '{$T295_Id}'");
        $T295    = mysql_fetch_array($sqlT295);

        $dataInicial        = date("Y-m-1 00:00:00");
        $dataFinal          = date("Y-m-t 23:59:59");
        $alcada             = 'D';
        
        if($T295['C007_Flag_Vendedor'] != 'D'){

            // monta tabela de mensagens de alçada
            $msg = <<<EOT
            <style>
                .xtdTitulo {
                    border:1px solid #000000;
                    font-size:12px;
                    background:#4F5F6E;
                    color:black;
                }  
                .xtdValores {
                    border:1px solid #000000;
                    font-size:11px;
                    background:#000000;
                    color:dark;
                }
            </style>

            <tr>
                <td colspan="1" style="padding:0px 0px 0px 0px">
                    <table cellspacing="1" cellpadding="4px" width="100%" height="100%" style="border:0px;" class="xtdTitulo">
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Alçada
                        </td>
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Limite / Comprado
                        </td>
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Total mês
                        </td>
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Total fornecedor mês
                        </td>
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Total dia
                        </td>
                        <td align="center" width="100px" style="background:#C9DFEA">
                            Total PO
                        </td>
                    </table>
                </td>
            </tr>   
EOT; 
            
            if($T295['C007_Flag_Vendedor'] == 'C' || $T295['C007_Flag_Vendedor'] == 'V'){

                // Verifica o total aprovado no mês pelo comprador                
                $sqlTotalCompradorMes = mysql_query("SELECT SUM(T295_Valor_Total) as TotalComprador
                                                       FROM T295
                                                      WHERE T295_Flag_Status_Liberacao = '2' 
                                                        AND T295_C007_Id = '{$T295['T295_C007_Id']}' 
                                                        AND T295_Liberou_Alcada_Data >= '{$dataInicial}' 
                                                        AND T295_Liberou_Alcada_Data <= '{$dataFinal}'");
                $row1 = mysql_fetch_array($sqlTotalCompradorMes);
                
                 // Verifica o total aprovado no mês para o forncedor pelo comprador  
                $sqlTotalFornecedorMes = mysql_query("SELECT SUM(T295_Valor_Total) as TotalFornecedorMes 
                                                        FROM T295 
                                                       WHERE T295_Flag_Status_Liberacao = '2' 
                                                         AND T295_C007_Id = '{$T295['T295_C007_Id']}' 
                                                         AND T295_D024_Id='{$T295['T295_D024_Id']}' 
                                                         AND T295_Liberou_Alcada_Data >= '{$dataInicial}' 
                                                         AND T295_Liberou_Alcada_Data <= '{$dataFinal}'");
                $row2  = mysql_fetch_array($sqlTotalFornecedorMes);
                
                // Verifica o total aprovado no dia pelo comprador  
                $sqlTotalCompradorDia = mysql_query("SELECT SUM(T295_Valor_Total) as TotalCompradorDia
                                                       FROM T295
                                                      WHERE T295_Flag_Status_Liberacao = '2' 
                                                        AND T295_C007_Id = '{$T295['T295_C007_Id']}' 
                                                        AND T295_Liberou_Alcada_Data LIKE concat(curdate(),'%')");
                $row3 = mysql_fetch_array($sqlTotalCompradorDia);

                //alçadas comprador
                $C007_Valor_Maximo_PO_Mes        = gCorrigeNumero($T295['C007_Valor_Maximo_PO_Mes']);
                $C007_Valor_Maximo_PO_Fornecedor = gCorrigeNumero($T295['C007_Valor_Maximo_PO_Fornecedor']);
                $C007_Valor_Maximo_PO_Dia        = gCorrigeNumero($T295['C007_Valor_Maximo_PO_Dia']);
                $C007_Valor_Maximo_PO            = gCorrigeNumero($T295['C007_Valor_Maximo_PO']);

                // soma os valores das POs aprovadas + a PO que está sendo analisada
                $row1['TotalComprador']     += $T295['T295_Valor_Total']; 
                $row2['TotalFornecedorMes'] += $T295['T295_Valor_Total']; 
                $row3['TotalCompradorDia']  += $T295['T295_Valor_Total'];

                // Total POs aprovadas comprador, retorna em vermelho se não tiver alçada, retorna em verde se tiver alçada
                $valorTotalPO       = ($T295['T295_Valor_Total']   > $T295['C007_Valor_Maximo_PO'])            ? '<span style="color: red;">' . gCorrigeNumero($T295['T295_Valor_Total'])   . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($T295['T295_Valor_Total'])  . '</span>';
                $TotalComprador     = ($row1['TotalComprador']     > $T295['C007_Valor_Maximo_PO_Mes'])        ? '<span style="color: red;">' . gCorrigeNumero($row1['TotalComprador'])     . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row1['TotalComprador'] )   . '</span>';
                $TotalFornecedorMes = ($row2['TotalFornecedorMes'] > $T295['C007_Valor_Maximo_PO_Fornecedor']) ? '<span style="color: red;">' . gCorrigeNumero($row2['TotalFornecedorMes']) . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row2['TotalFornecedorMes']). '</span>';
                $TotalCompradorDia  = ($row3['TotalCompradorDia']  > $T295['C007_Valor_Maximo_PO_Dia'])        ? '<span style="color: red;">' . gCorrigeNumero($row3['TotalCompradorDia'])  . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row3['TotalCompradorDia']) . '</span>';

                $msg .= <<<EOT
                <tr>
                    <td colspan="1" >
                        <table cellspacing="1" cellpadding="4px" width="100%" height="40px" style="border:0px" class="xtdValores">
                            <tr>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    Comprador
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    Limite
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    <b>{$C007_Valor_Maximo_PO_Mes}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    <b>{$C007_Valor_Maximo_PO_Fornecedor}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    <b>{$C007_Valor_Maximo_PO_Dia}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    <b>{$C007_Valor_Maximo_PO}</b>
                                </td>
                            </tr>             
                            <tr>
                                <td align="center" width="100px" style="background:#FFFFFF">
                                    <b>{$T295['C007_Primeiro_Nome']}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF" >
                                    Comprado
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF" >
                                    <b>{$TotalComprador}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF" >
                                    <b>{$TotalFornecedorMes}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF" >
                                    <b>{$TotalCompradorDia}</b>
                                </td>
                                <td align="center" width="100px" style="background:#FFFFFF" >
                                    <b>{$valorTotalPO}</b>
                                </td>                            
                            </tr>   
                        </table>
                    </td>
                </tr>
EOT;                                
            
                if($T295['T295_Valor_Total'] <= $T295['C007_Valor_Maximo_PO'] && $row1['TotalComprador'] <= $T295['C007_Valor_Maximo_PO_Mes'] && $row2['TotalFornecedorMes'] <= $T295['C007_Valor_Maximo_PO_Fornecedor'] && $row3['TotalCompradorDia'] <= $T295['C007_Valor_Maximo_PO_Dia']){
                    // Se comprador tiver alçada
                    $alcada = 'C';  
                } else {
                    //Caso o comprador não tenha alçada suficiente, verificamos a alçada do seu supervisor
                    $sqlC007 = mysql_query("SELECT * FROM C007 WHERE C007_Id = '{$T295['C007_C007_Id']}'");
                    $C007    = mysql_fetch_array($sqlC007);

                    $T295['C007_Valor_Maximo_PO']            = $C007['C007_Valor_Maximo_PO'];
                    $T295['C007_Valor_Maximo_PO_Mes']        = $C007['C007_Valor_Maximo_PO_Mes'];
                    $T295['C007_Valor_Maximo_PO_Fornecedor'] = $C007['C007_Valor_Maximo_PO_Fornecedor'];
                    $T295['C007_Valor_Maximo_PO_Dia']        = $C007['C007_Valor_Maximo_PO_Dia'];
                    $T295['C007_Id']                         = $C007['C007_Id'];
                }
            }
            
            //Caso o usuario seja um supervisor ou o comprador tenha um supervisor
            if($alcada == 'D' && $T295['C007_Id'] > 0){
                
                // Verifica o total aprovado no mês pelo supervisor
                $sqlTotalSupervisorMes = mysql_query("SELECT SUM(T295_Valor_Total) as TotalSupervisorMes 
                                                        FROM T295 
                                                   LEFT JOIN C007 ON C007_Id = T295_C007_Id
                                                       WHERE T295_Flag_Status_Liberacao = '2' 
                                                         AND (T295_C007_Id = '{$T295['C007_Id']}' OR T295_Liberou_Alcada_C007_Id = '{$T295['C007_Id']}') 
                                                         AND T295_Liberou_Alcada_Data >= '{$dataInicial}' 
                                                         AND T295_Liberou_Alcada_Data <= '{$dataFinal}'");
                $row1 = mysql_fetch_array($sqlTotalSupervisorMes);
                
                // Verifica o total aprovado no mês para o forncedor pelo supervisor
                $sqlTotalFornecedorSupervisorMes = mysql_query("SELECT SUM(T295_Valor_Total) as TotalFornecedorSupervisorMes 
                                                                  FROM T295 
                                                             LEFT JOIN C007 ON C007_Id = T295_C007_Id 
                                                                 WHERE T295_Flag_Status_Liberacao = '2' 
                                                                   AND (T295_C007_Id = '{$T295['C007_Id']}' OR T295_Liberou_Alcada_C007_Id = '{$T295['C007_Id']}') 
                                                                   AND T295_D024_Id='{$T295['T295_D024_Id']}' 
                                                                   AND T295_Liberou_Alcada_Data >= '{$dataInicial}' 
                                                                   AND T295_Liberou_Alcada_Data <= '{$dataFinal}'");
                $row2  = mysql_fetch_array($sqlTotalFornecedorSupervisorMes);
                
                // Verifica o total aprovado no dia pelo supervisor
                $sqlTotalSupervisorDia = mysql_query("SELECT SUM(T295_Valor_Total) as TotalSupervisorDia
                                                        FROM T295 
                                                   LEFT JOIN C007 ON C007_Id = T295_C007_Id 
                                                       WHERE T295_Flag_Status_Liberacao = '2' 
                                                         AND T295_Liberou_Alcada_Data LIKE concat(curdate(),'%') 
                                                         AND (T295_C007_Id = '{$T295['C007_Id']}' OR T295_Liberou_Alcada_C007_Id = '{$T295['C007_Id']}')");
                $row3 = mysql_fetch_array($sqlTotalSupervisorDia);

                //alçadas supervisor
                $C007_Valor_Maximo_PO_Mes        = gCorrigeNumero($T295['C007_Valor_Maximo_PO_Mes']);
                $C007_Valor_Maximo_PO_Fornecedor = gCorrigeNumero($T295['C007_Valor_Maximo_PO_Fornecedor']);
                $C007_Valor_Maximo_PO_Dia        = gCorrigeNumero($T295['C007_Valor_Maximo_PO_Dia']);
                $C007_Valor_Maximo_PO            = gCorrigeNumero($T295['C007_Valor_Maximo_PO']);

                // soma os valores das POs aprovadas + a PO que está sendo analisada
                $row1['TotalSupervisorMes']           += $T295['T295_Valor_Total']; 
                $row2['TotalFornecedorSupervisorMes'] += $T295['T295_Valor_Total']; 
                $row3['TotalSupervisorDia']           += $T295['T295_Valor_Total'];

                // Total POs aprovadas supervisor, retorna em vermelho se não tiver alçada, retorna em verde se tiver alçada
                $valorTotalPO                 = ($T295['T295_Valor_Total']             > $T295['C007_Valor_Maximo_PO'])            ? '<span style="color: red;">' . gCorrigeNumero($T295['T295_Valor_Total'])             . '</span>' : '<span style="color: darkgreen;">'  . gCorrigeNumero($T295['T295_Valor_Total'])                    . '</span>';
                $TotalSupervisorMes           = ($row1['TotalSupervisorMes']           > $T295['C007_Valor_Maximo_PO_Mes'])        ? '<span style="color: red;">' . gCorrigeNumero($row1['TotalSupervisorMes'])           . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row1['TotalSupervisorMes'] )         . '</span>';
                $TotalFornecedorSupervisorMes = ($row2['TotalFornecedorSupervisorMes'] > $T295['C007_Valor_Maximo_PO_Fornecedor']) ? '<span style="color: red;">' . gCorrigeNumero($row2['TotalFornecedorSupervisorMes']) . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row2['TotalFornecedorSupervisorMes']). '</span>';
                $TotalSupervisorDia           = ($row3['TotalSupervisorDia']           > $T295['C007_Valor_Maximo_PO_Dia'])        ? '<span style="color: red;">' . gCorrigeNumero($row3['TotalSupervisorDia'])           . '</span>' : '<span style="color: darkgreen ;">' . gCorrigeNumero($row3['TotalSupervisorDia'])          . '</span>';

                if($g['nivelAlcada'] > 1){
                    // monta a tabela de alçadas supervisor
                    $msg .= <<<EOT
                        <tr>
                            <td colspan="1" >
                                <table cellspacing="1" cellpadding="4px" width="100%" height="40px" style="border:0px" class="xtdValores">
                                    <tr>
                                        <td align="center" width="100px" style="background:#FFFFFF" >
                                            Supervisor
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            Limite
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            <b>{$C007_Valor_Maximo_PO_Mes}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            <b>{$C007_Valor_Maximo_PO_Fornecedor}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            <b>{$C007_Valor_Maximo_PO_Dia}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            <b>{$C007_Valor_Maximo_PO}</b>
                                        </td>
                                    </tr>             
                                    <tr>
                                        <td align="center" width="100px" style="background:#FFFFFF">
                                            <b>{$C007['C007_Primeiro_Nome']}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF" >
                                            Comprado
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF" >
                                            <b>{$TotalSupervisorMes}</b>
                                        </td>
                                        <td align="center" width="100px style="background:#FFFFFF" >
                                            <b>{$TotalFornecedorSupervisorMes}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF" >
                                            <b>{$TotalSupervisorDia}</b>
                                        </td>
                                        <td align="center" width="100px" style="background:#FFFFFF" >
                                            <b>{$valorTotalPO}</b> 
                                        </td>                            
                                    </tr>   
                                </table>
                            </td>
                        </tr>
EOT;    
                }

                if($T295['T295_Valor_Total'] <= $T295['C007_Valor_Maximo_PO'] && $row1['TotalSupervisorMes'] <= $T295['C007_Valor_Maximo_PO_Mes'] && $row2['TotalFornecedorSupervisorMes'] <= $T295['C007_Valor_Maximo_PO_Fornecedor'] && $row3['TotalSupervisorDia'] <= $T295['C007_Valor_Maximo_PO_Dia']){
                    // Se supervisor tiver alçada
                    $alcada = 'S';
                }
            }
        }
        
        // Update na alçada e msg
        mysqli_query("UPDATE T295 SET T295_Flag_Alcada             = '{$alcada}', 
                                      T295_Motivo_Pedido_Liberacao = '{$msg}' 
                                WHERE T295_Id = '{$T295_Id}'");

        return true;
    }

    /**
     * imp005AprovarPedido
     *
     * @param int $T295_Id
     */
    public function imp005AprovarPO($T295_Id)
    {
        global $g;
        
        $T295 = mysql_query("SELECT T295_Valor_Total
                               FROM T295 
                              WHERE T295_Id='{$T295_Id}'");
        $mT295 = mysql_fetch_array($T295);                        
        
        mysql_query("UPDATE T295 SET T295_Liberou_Alcada_C007_Id='{$g['usuarioAtual']}',
                                     T295_Liberou_Alcada_Data=now(),
                                     T295_Flag_Status='3',
                                     T295_Nome_Status = T295_Status_Pedido(T295_Flag_Status, T295_Id),
                                     T295_Flag_Status_Liberacao='2',
                                     T295_Liberou_Alcada_Valor_Total='{$mT295['T295_Valor_Total']}',                                      
                                     T295_Liberou_Alcada_Observacao_Nao_Liberado=''
                               WHERE T295_Id='{$T295_Id}'");

        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro<br />Erro:{$cErro}";
        }

        // reprocessa OC dos itens e pedidos
        $this->atualizaOC($T295_Id);
                
        return "PO aprovada com sucesso!";
    }

    /**
     * imp005NaoLiberarPedido
     *
     * @param Form $form
     */
    public function imp005NaoLiberarPedido($form)
    {
        global $g;
        $T295_Id                                     = $form->campoValorEnviado('T295_Id');
        $T295_Liberou_Alcada_Observacao_Nao_Liberado = $form->campoValorEnviado('T295_Liberou_Alcada_Observacao_Nao_Liberado');
        
        if (empty($T295_Id)) {
            return "T295_Id não existe";
        }

        $T295_Liberou_Alcada_Observacao_Nao_Liberado = trim($T295_Liberou_Alcada_Observacao_Nao_Liberado);
        $T295_Liberou_Alcada_Observacao_Nao_Liberado = addslashes($T295_Liberou_Alcada_Observacao_Nao_Liberado);
        
        if (empty($T295_Liberou_Alcada_Observacao_Nao_Liberado)) {
            return "Preencha o campo Motivo.";
        }
        
        mysql_query("UPDATE T295 SET T295_Liberou_Alcada_C007_Id='{$g['usuarioAtual']}',
                                     T295_Liberou_Alcada_Data=now(),
                                     T295_Flag_Status='2',
                                     T295_Nome_Status = T295_Status_Pedido(T295_Flag_Status, T295_Id),
                                     T295_Flag_Status_Liberacao='3',
                                     T295_Liberou_Alcada_Observacao_Nao_Liberado='{$T295_Liberou_Alcada_Observacao_Nao_Liberado}'
                               WHERE T295_Id='{$T295_Id}'");

        $cErro = mysql_error();
        if (!empty($cErro)) {
            return "Ocorreu um erro ao tentar alterar o registro/p/pErro: $cErro";
        } else {
            return true;
        }
    }

     /**
     * imp005enviarParaAnalise
     *
     * @param int $T295_Id
     * 
     */
    public function imp005enviarParaAnalise($T295_Id)
    {
        mysql_query("UPDATE T295 SET T295_Flag_Status_Liberacao ='1', 
                                     T295_Flag_Status           ='1', 
                                     T295_Nome_Status           = T295_Status_Pedido(T295_Flag_Status, T295_Id)
                               WHERE T295_Id = '{$T295_Id}'");
        $error = mysql_error(); 
        if (!empty($error)){
            return $error;
        } else {
            return true;
        }
    }

    /**
     * imp005VerificaAlcada
     *
     * @param int $T295_Id
     */
    public function imp005VerificaAlcada($T295_Id)
    {
        global $g;
        
        $T295 = mysql_query("SELECT T295_Flag_Status_Liberacao,
                                    T295_Liberou_Alcada_Valor_Total,
                                    T295_Valor_Total
                               FROM T295 
                              WHERE T295_Id='{$T295_Id}'");
        $mT295 = mysql_fetch_array($T295);
                   
        $analise = true;    
        //PO ja passou pela analise?
        if ($mT295['T295_Flag_Status_Liberacao'] == "2") {
            // Valor total da Venda liberado é diferente do valor total da Venda atual?
            if ($mT295['T295_Valor_Total'] != $mT295['T295_Liberou_Alcada_Valor_Total']){
                $analise = false;
            }
                    
            if ($analise === true) {
                return true;
            }
        }

        mysql_query("UPDATE T295 SET T295_Flag_Status_Liberacao = '4' WHERE T295_Id='{$T295_Id}'");
        return true;
    }

     /**
     * incluiPacoteDespesa
     *
     * @param int $T295_Id
     * @param int $D190_Id
     * @return bool
     */  
    public function incluirPacoteDespesaPO($T295_Id, $D190_Id) {
        global $g;

        //Deleta pacote que já está cadastrado, despesas incluídas manualmente não são deletadas
        mysql_query("DELETE FROM T300 WHERE T300_T295_Id = '{$T295_Id}' AND T300_D190_Id > 0");
        $error = mysql_error();
        if(!empty($error)){
            return 'Houve um erro ao tentar excluir o pacote de depesas <br>'.$error;
        }

        if($D190_Id > 0){
            // Faz o select buscando o pacote
            $D192 = mysql_query("SELECT D192_D190_Id,
                                        D192_D191_Id,
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
                                        D192_Flag_ME
                                FROM D192
                                WHERE D192_D190_Id = '{$D190_Id}'");

            while($mD192 = mysql_fetch_array($D192)){

                //faz o insert do pacote na Quotation
                mysql_query("INSERT INTO T300 (T300_T295_Id,
                                               T300_D190_Id,
                                               T300_D191_Id,
                                               T300_Aliquota,
                                               T300_Flag_Tipo,
                                               T300_Valor,
                                               T300_Formula,
                                               T300_Ordem,
                                               T300_Flag_Soma_Base_IPI,
                                               T300_Flag_Soma_Base_ICMS,
                                               T300_Flag_Soma_Base_PIS,
                                               T300_Flag_Soma_Base_COFINS,
                                               T300_Flag_Soma_Base_II,
                                               T300_Flag_Soma_Custo,
                                               T300_Flag_Soma_Preco_NFe,
                                               T300_Flag_Tipo_Rateio,
                                               T300_Flag_ME
                                      )VALUES(
                                               '{$T295_Id}',  
                                               '{$mD192['D192_D190_Id']}',
                                               '{$mD192['D192_D191_Id']}',
                                               '{$mD192['D192_Aliquota']}',
                                               '{$mD192['D192_Flag_Tipo']}',
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
                                               '{$mD192['D192_Flag_ME']}')");
                $error = mysql_error();
                if(!empty($error)){
                    return 'Houve um erro ao tentar incluir o pacote de depesas <br>'.$error;
                }
            }   
        }

        return true;
    }

        /**
     * processarOrdemDespesa
     *
     * @param String $T300_Id
     * @return Bool
     */
    public function processarOrdemDespesa($T300_Id, $novoNumeroItem)
    {
        global $g;
        $contadorItem = 1;
        $sql = mysql_query("SELECT T300_Ordem, 
                                   T300_Id 
                              FROM T300 
                             WHERE T300_T295_Id = (SELECT T300_T295_Id FROM T300 WHERE T300_Id = '{$T300_Id}') 
                               AND T300_Id != '{$T300_Id}' 
                          ORDER BY T300_Ordem");
        while($row = mysql_fetch_array($sql)){
            if($contadorItem == $novoNumeroItem){ $contadorItem++; }
            mysqli_query("UPDATE T300 SET T300_Ordem = '{$contadorItem}' WHERE T300_Id = '{$row['T300_Id']}'");
            $contadorItem++;
        }
        return true;
    }

    /**
     * calcularDepesas
     *
     * @param String $T295_Id
     * @return Bool
     */
    public function calcularDespesas($T295_Id)
    {
        global $g;
        
        $T295 = mysql_query("SELECT COUNT(DISTINCT T296_Valor_Adicao) AS totalAdicao,
                                    SUM(T296_Valor_Custo_Unitario * T296_Quantidade) AS T296_Valor_Custo_Unitario
                               FROM T295    
                          LEFT JOIN T296 ON T296_T295_Id=T295_Id
                              WHERE T295_Id='{$T295_Id}'");
        $mT295 = mysql_fetch_array($T295);

        // variavel utilizada na formula do siscomex
        $totalAdicoes = $mT295['totalAdicao'];

        $T300 = mysql_query("SELECT T300_Id,
                                    T300_Aliquota,
                                    T300_Flag_Tipo,
                                    T300_Valor,
                                    T300_Formula,
                                    T300_Flag_Tipo,
                                    T300_Cotacao_ME,
                                    D191_Descricao_Despesa,
                                    T295_Valor_VMLE,
                                    T300_Flag_ME,
                                    T295_Cotacao_ME
                               FROM T300
                          LEFT JOIN D191 ON D191_Id = T300_D191_Id 
                          LEFT JOIN T295 ON T295_Id = T300_T295_Id
                              WHERE T300_T295_Id = '{$T295_Id}'
                           ORDER BY T300_Ordem ASC");

        $variavel   = array();
        $valorME    = array();
        $valorReais = array();

        while($mT300 = mysql_fetch_array($T300)){
            //monta o nome da variavel em tempo real, essa variavel poderá ser utilizada em qual formula
            $nomeVariavel    = strToUpper(str_replace(" ","_", $mT300['D191_Descricao_Despesa'])); 
            $valorCustoTotal = $mT295['T296_Valor_Custo_Unitario'] * $mT300['T295_Cotacao_ME'];
        
            if($mT300['T300_Flag_Tipo'] == 'F'){
                // verifica se é a despesa é do tipo formula
                $mT300['T300_Formula'] = str_replace('<?php','', $mT300['T300_Formula']);
                $mT300['T300_Formula'] = str_replace("ALIQUOTA",$mT300['T300_Aliquota'], $mT300['T300_Formula']);
                $mT300['T300_Formula'] = str_replace("COTACAO_ME",$mT300['T300_Cotacao_ME'], $mT300['T300_Formula']);
                $mT300['T300_Formula'] = str_replace("VALOR_VMLE_REAL", $valorCustoTotal, $mT300['T300_Formula']);

                // procura se existe uma variavel montada em tempo real e pega o valor
                if ($mT300['T300_Flag_ME'] == 'S'){
                    $mT300['T300_Formula'] = str_replace($variavel, $valorME, $mT300['T300_Formula']);
                } else {
                    $mT300['T300_Formula'] = str_replace($variavel, $valorReais, $mT300['T300_Formula']);
                }
                
                $variavel[] = $nomeVariavel; 
                $resultado  = eval($mT300['T300_Formula']);
                if ($mT300['T300_Flag_ME']=='S'){
                    $valorReais[] = $resultado * $mT300['T300_Cotacao_ME'];
                    $valorME[]    = $resultado;
                } else  {
                    $valorReais[] = $resultado;
                    $valorME[]    = $resultado;
                }

                mysql_query("update T300 set T300_Valor = '{$resultado}' where T300_Id='{$mT300['T300_Id']}'");

            }else{             

                $variavel[] = $nomeVariavel;
                if ($mT300['T300_Flag_ME']=='S'){
                    $valorReais[] = $mT300['T300_Valor'] * $mT300['T300_Cotacao_ME'];
                    $valorME[]    = $mT300['T300_Valor'];
                }else{
                    $valorReais[] = $mT300['T300_Valor'];
                    $valorME[]    = $mT300['T300_Valor'];
                }
            }
        }

        return true;
    }

    /**
     * calculaCustoProduto
     *
     * @param String $T295_Id
     * @return Bool
     */
    public function calculaCustoProduto($T295_Id)
    {
        global $g;
        log("entrou no calculaCusto ".$T295_Id);
        
        $total = mysql_query("SELECT SUM(T296_Valor_Custo_Unitario * T296_Quantidade) AS Valor_Total_Custo,
                                     SUM(T296_Peso_Kg_Liquido_Unitario * T296_Quantidade) AS Total_Peso
                                FROM T296
                               WHERE T296_T295_Id = '{$T295_Id}'");
        $mTotal = mysql_fetch_array($total);

        $T296 = mysql_query("SELECT T296_Id,
                                    (T296_Valor_Custo_Unitario * T296_Quantidade) AS Valor_Custo_Produto, 
                                    T296_Quantidade, 
                                    (T296_Peso_Kg_Liquido_Unitario * T296_Quantidade) AS Peso_Kg_Liquido_Unitario_Total,
                                    T295_Cotacao_ME 
                               FROM T296
                          LEFT JOIN T295 ON T296_T295_Id = T295_Id  
                              WHERE T296_T295_Id = '{$T295_Id}'");

        while($mT296 = mysql_fetch_array($T296)){

            $custoProduto    = $mT296['Valor_Custo_Produto'] * $mT296['T295_Cotacao_ME'];
            $valorTotalCusto = $mTotal['Valor_Total_Custo']  * $mT296['T295_Cotacao_ME'];
        
            $T300 = mysql_query("SELECT *
                                FROM T300 
                                WHERE T300_T295_Id = '{$T295_Id}'
                            ORDER BY T300_Ordem");

            $somaBaseIPI        = $custoProduto;
            $somaBaseICMS       = $custoProduto;
            $somaBasePIS        = $custoProduto;
            $somaBaseCOFINS     = $custoProduto;
            $somaBaseII         = $custoProduto;
            $somaCusto          = $custoProduto;
            $somaPrecoNFE       = $custoProduto;
            $valorTotalDespesas = 0;

            while($mT300 = mysql_fetch_array($T300)){

                if ($mT300['T300_Flag_ME'] == 'S') {
                    $mT300['T300_Valor'] = $mT300['T300_Valor'] * $mT300['T300_Cotacao_ME'];
                }
                
                if($mT300['T300_Flag_Tipo_Rateio'] == 'C'){
                    $mT300['T300_Valor'] = ($mT300['T300_Valor'] * ($custoProduto/$valorTotalCusto));
                }else{
                    $mT300['T300_Valor'] = ($mT300['T300_Valor'] * ($mT296['Peso_Kg_Liquido_Unitario_Total']/$mTotal['Total_Peso']));
                }

                if($mT300['T300_Flag_Soma_Base_IPI'] == 'S' || $mT300['T300_Flag_Soma_Base_ICMS'] == 'S' || $mT300['T300_Flag_Soma_Base_PIS'] == 'S' || $mT300['T300_Flag_Soma_Base_COFINS'] == 'S' || $mT300['T300_Flag_Soma_Base_II'] == 'S' || $mT300['T300_Flag_Soma_Custo'] == 'S' || $mT300['T300_Flag_Soma_Preco_NFe'] == 'S'){
                    $valorTotalDespesas += $mT300['T300_Valor'];              
                }

                if($mT300['T300_Flag_Soma_Base_IPI'] == 'S'){
                    $somaBaseIPI += $mT300['T300_Valor'];
                }

                if($mT300['T300_Flag_Soma_Base_ICMS'] == 'S'){
                    $somaBaseICMS += $mT300['T300_Valor'];
                }

                if($mT300['T300_Flag_Soma_Base_PIS'] == 'S'){
                    $somaBasePIS += $mT300['T300_Valor'];
                }

                if($mT300['T300_Flag_Soma_Base_COFINS'] == 'S'){
                    $somaBaseCOFINS += $mT300['T300_Valor'];
                }

                if($mT300['T300_Flag_Soma_Base_II'] == 'S'){
                    $somaBaseII += $mT300['T300_Valor'];
                }

                if($mT300['T300_Flag_Soma_Custo'] == 'S'){
                    $somaCusto += $mT300['T300_Valor'];

                }

                if($mT300['T300_Flag_Soma_Preco_NFe'] == 'S'){
                    $somaPrecoNFE += $mT300['T300_Valor'];
                }
            }

            mysqli_query("UPDATE T296 SET T296_Valor_Base_IPI              = '{$somaBaseIPI}',
                                          T296_Valor_Base_ICMS_Sem_Reducao   = '{$somaBaseICMS}',
                                          T296_Valor_Base_PIS                = '{$somaBasePIS}',
                                          T296_Valor_Base_COFINS             = '{$somaBaseCOFINS}',
                                          T296_Valor_Base_Imposto_Importacao = '{$somaBaseII}',
                                          T296_Valor_Total_Custo_Final       = '{$somaCusto}',
                                          T296_Valor_Total_Preco_Nota_Fiscal = '{$somaPrecoNFE}',
                                          T296_Valor_Total_Despesas          = '{$valorTotalDespesas}'
                                    WHERE T296_Id = '{$mT296['T296_Id']}'");
        } 

        return true;
    }

    /**
     * duplicar_IMP005
     * Chamada DUPLICAR do menu automatico da janela
     *
     * @param String 
     * @return String
     */    
	public Function duplicar_imp005FormProdutoIncluir($divId, $divIdRoot, $tabela, $T295_Id_Antigo)
    {
        global $g;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        // Selecionando o orçamento
        $T295  = mysql_query("SELECT * 
                                FROM T295
                               WHERE T295_Id='{$T295_Id_Antigo}'");
        $mT295 = mysql_fetch_assoc($T295);
        log("fez a query agora");
        
        if (mysql_num_rows($T295) == 0) {
            $retorno = <<<EOT
                var \$buttons = {
                    "Fechar": function() { $(this).dialog("destroy").remove(); }
                };
                dialogConfirm("Erro", "Quotation não encontrada", \$buttons);
EOT;
            return $retorno;
        }
        
        $campos  = "";
        $valores = "";
        foreach ($mT295 as $key => $value) {
            if ($key == 'T295_Id') {
                continue;
            } elseif ($key == 'T295_D190_Id'){
                continue;
            } elseif ($key == 'T295_Flag_Transporte_DI'){
                continue;
            }elseif ($key == 'T295_Data_Emissao') {
                $campos.=$key.", ";
                $valores.= "curdate(),";
            }  else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }
        
        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT295 = "INSERT INTO T295 ({$campos}) VALUES ({$valores})";
        $resT295 = mysql_query($insT295);
        $error = mysql_error();
        if (!empty($error)) {
            mysql_query("ROLLBACK");
            return "Erro 1: " . $error;
        }

        $T295_Id_Novo = $g['mysqlLastId'];
        
        // Selecionando todos os itens da Quotation
        $T296 = mysql_query("SELECT * FROM T296 WHERE T296_T295_Id='{$T295_Id_Antigo}'");
        
        while ($mT296 = mysql_fetch_assoc($T296)) {
            $campos  = "";
            $valores = "";
            foreach ($mT296 as $key => $value) {
                if ($key == 'T296_Id') {
                    continue;
                } elseif ($key == "T296_T295_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T295_Id_Novo) . "',";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT296 = "INSERT INTO T296 ({$campos}) VALUES ({$valores})";
            $resT296 = mysql_query($insT296);
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return "Erro 2: " . $error;
            }
        }

        mysql_query("COMMIT");

        $retorno = "dialogConfirm('Duplicar', 'PO duplicada com sucesso');";
        return $retorno;
    }

    /**
     * alteraStatusQuotation
     * Verifica o status da PO ao gerar uma DI
     *
     * @param array T295_Id
    */
    public function alteraStatusPO($T295){
        global $g;
        
        foreach(array_unique($T295) as $T295_Id){

            $T295 = mysql_query("SELECT T295_Id,
                                        (SUM(T296_Quantidade_Saldo)/SUM(T296_Quantidade))*100 as percentual
                                   FROM T295 
                              LEFT JOIN T296 ON T295_Id=T296_T295_Id AND T296_Flag_Finalizar_Compra !='S'
                                  WHERE T295_Id ='{$T295_Id}'");

            $mT295 = mysql_fetch_array($T295);
            
            if($mT295['percentual'] > 0){
                mysql_query("UPDATE T295 SET T295_Flag_Status = '4', 
                                             T295_Nome_Status = T295_Status_Pedido(T295_Flag_Status, T295_Id) 
                                       WHERE T295_Id = '{$mT295['T295_Id']}'");

                $erro = mysql_error();
                if (!empty($erro)) {
                    return "Erro ao gerar DI<br><br>{$erro}";
                }
            } else {
                mysql_query("UPDATE T295 SET T295_Flag_Status = '5', 
                                             T295_Nome_Status = T295_Status_Pedido(T295_Flag_Status, T295_Id) 
                                       WHERE T295_Id = '{$mT295['T295_Id']}'");

                $erro = mysql_error();
                if (!empty($erro)) {
                    return "Erro ao gerar DI<br><br>{$erro}";
                }
            }        
        }

        return true;
    } 

    /**
     * adicionarProdutosDI
     * Adiciona itens em DI ja existente
     *
     * @param int T075_Id  
     * @param int D024_Id 
    */
    public function adicionarProdutosDI($T075_Id, $D024_Id)
    {
        global $g;

        // precisa do D024 para fazer a busca dos itens das POs, porque pode ser incluido itens de várias POs do mesmo fornecedor
        if($D024_Id <= 0 || empty($D024_Id)){
            return "Erro, não foi possível localizar a PO";
        }

        $T075 = mysql_query("SELECT T075_D024_Id
                               FROM T075
                              WHERE T075_Id = '{$T075_Id}'");
        $mT075 = mysql_fetch_array($T075);

        // Verifica se é permitido incluir itens na DI sendo fornecedores diferentes PO x DI
        if($D024_Id != $mT075['T075_D024_Id'] && $g['C031']['incluirItensPOnaDIFornecedorDiferente'] == 'N'){
            return 'Não é permitido a inclusão de itens na DI via PO com fornecedores diferentes<br>Para liberar, verifique a configuração da empresa';
        }

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        $T296 = mysql_query("SELECT T296_Id,
                                    T296_T060_Id,
                                    T296_T054_Id,
                                    T296_D037_Id,
                                    T296_D005_Id,
                                    T296_D009_Id,
                                    T296_T053_Id,
                                    T296_C004_Id,
                                    T296_T270_Id,
                                    T296_T066_Id,
                                    T296_D006_Id,
                                    T296_Codigo_Produto,
                                    T296_Codigo_Produto_Fornecedor,
                                    D001_Descricao_Produto,
                                    T296_Classificacao_Fiscal,
                                    T296_Prazo_Entrega,
                                    T296_Quantidade_DI,
                                    T296_Valor_Custo_Unitario_Anterior,
                                    T296_Valor_Custo_Unitario,
                                    T296_Valor_Total_Custo,
                                    T296_Valor_Preco_Unitario_Nota_Fiscal,
                                    T296_Valor_Total_Preco_Nota_Fiscal,
                                    T296_Valor_Total_Despesas_Acessorias_Nota_Fiscal,
                                    T296_Valor_Custo_Unitario_Original,
                                    T296_Valor_Total_Custo_Original,
                                    T296_Valor_Base_IPI,
                                    T296_Valor_Base_ICMS_Sem_Reducao,
                                    T296_Valor_Base_ICMS_Normal,
                                    T296_Valor_Base_ICMS_Presumido,
                                    T296_Valor_Base_COFINS,
                                    T296_Valor_Base_PIS,
                                    T296_Valor_Base_Extra,
                                    T296_Valor_Base_Imposto_Importacao,
                                    T296_Aliquota_IPI,
                                    T296_Aliquota_Imposto_Importacao,
                                    T296_Aliquota_COFINS,
                                    T296_Aliquota_PIS,
                                    T296_Aliquota_Extra,
                                    T296_Valor_ICMS,
                                    T296_Valor_ICMS_Presumido,
                                    T296_Valor_ICMS_Pagar,
                                    T296_Valor_IPI,
                                    T296_Valor_Imposto_Importacao,
                                    T296_Valor_COFINS,
                                    T296_Valor_PIS,
                                    T296_Valor_Extra,
                                    T296_Aliquota_ICMS,
                                    T296_Aliquota_ICMS_Presumido,
                                    T296_Percentual_Reducao_ICMS,
                                    T296_Percentual_Reducao_ICMS_Presumido,
                                    T296_Aliquota_ICMS_PIS_COFINS,
                                    T296_Aliquota_ICMS_Credito_Presumido,
                                    T296_Adicao_Item,
                                    T296_Flag_Calculo_Manual,
                                    T296_Peso_Kg_Liquido_Unitario,
                                    T296_Peso_Kg_Liquido_Total,
                                    T296_Peso_Lb_Liquido_Unitario,
                                    T296_Peso_Lb_Liquido_Total,
                                    T295_Id,
                                    D001A_Altura,
                                    D001A_Largura,
                                    D001A_Comprimento 
                               FROM T296
                          LEFT JOIN D009 ON D009_Id=T296_D009_Id
                          LEFT JOIN D049 ON D049_Id=D009_D049_Id
                          LEFT JOIN D001 ON D001_Id=D049_D001_Id
                          LEFT JOIN D001A ON D001_Id=D001A_D001_Id
                          LEFT JOIN T295 ON T295_Id=T296_T295_Id
                              WHERE T295_D024_Id = '{$D024_Id}'
                                AND T296_Flag_Finalizar_Compra = 'N'
                                AND T296_Quantidade_DI > 0");

        // Vai gravar todos os T295_Id para atualizar o status            
        $arrayT295 = array();

        while($rowT296 = mysql_fetch_assoc($T296)) {
            if($rowT296['T296_Quantidade_DI'] == 0){
                continue;
            }
            
            $arrayT295[] = $rowT296['T295_Id'];
            
            $insertT055 = array(
                "T055_T075_Id"                                     => $T075_Id,
                "T055_T296_Id"                                     => $rowT296['T296_Id'],
                "T055_T060_Id"                                     => $rowT296['T296_T060_Id'],
                "T055_T054_Id"                                     => $rowT296['T296_T054_Id'],
                "T055_D037_Id"                                     => $rowT296['T296_D037_Id'],
                "T055_D005_Id"                                     => $rowT296['T296_D005_Id'],
                "T055_D009_Id"                                     => $rowT296['T296_D009_Id'],
                "T055_T053_Id"                                     => $rowT296['T296_T053_Id'],
                "T055_C004_Id"                                     => $rowT296['T296_C004_Id'],
                "T055_T270_Id"                                     => $rowT296['T296_T270_Id'],
                "T055_T066_Id"                                     => $rowT296['T296_T066_Id'],
                "T055_D006_Id"                                     => $rowT296['T296_D006_Id'],
                "T055_Codigo_Produto"                              => $rowT296['T296_Codigo_Produto'],
                "T055_Codigo_Produto_Fornecedor"                   => $rowT296['T296_Codigo_Produto_Fornecedor'],
                "T055_Descricao_Produto"                           => $rowT296['D001_Descricao_Produto'],
                "T055_Classificacao_Fiscal"                        => $rowT296['T296_Classificacao_Fiscal'],
                "T055_Prazo_Entrega"                               => $rowT296['T296_Prazo_Entrega'],
                "T055_Quantidade"                                  => $rowT296['T296_Quantidade_DI'],
                "T055_Valor_Custo_Unitario_Anterior"               => $rowT296['T296_Valor_Custo_Unitario_Anterior'],
                "T055_Valor_Custo_Unitario"                        => $rowT296['T296_Valor_Custo_Unitario'],
                "T055_Valor_Total_Custo"                           => $rowT296['T296_Valor_Total_Custo'],
                "T055_Valor_Preco_Unitario_Nota_Fiscal"            => $rowT296['T296_Valor_Preco_Unitario_Nota_Fiscal'],
                "T055_Valor_Total_Preco_Nota_Fiscal"               => $rowT296['T296_Valor_Total_Preco_Nota_Fiscal'],
                "T055_Valor_Total_Despesas_Acessorias_Nota_Fiscal" => $rowT296['T296_Valor_Total_Despesas_Acessorias_Nota_Fiscal'],
                "T055_Valor_Custo_Unitario_Original"               => $rowT296['T296_Valor_Custo_Unitario_Original'],
                "T055_Valor_Total_Custo_Original"                  => $rowT296['T296_Valor_Total_Custo_Original'],
                "T055_Valor_Base_IPI"                              => $rowT296['T296_Valor_Base_IPI'],
                "T055_Valor_Base_ICMS_Sem_Reducao"                 => $rowT296['T296_Valor_Base_ICMS_Sem_Reducao'],
                "T055_Valor_Base_ICMS_Normal"                      => $rowT296['T296_Valor_Base_ICMS_Normal'],
                "T055_Valor_Base_ICMS_Presumido"                   => $rowT296['T296_Valor_Base_ICMS_Presumido'],
                "T055_Valor_Base_COFINS"                           => $rowT296['T296_Valor_Base_COFINS'],
                "T055_Valor_Base_PIS"                              => $rowT296['T296_Valor_Base_PIS'],
                "T055_Valor_Base_Extra"                            => $rowT296['T296_Valor_Base_Extra'],
                "T055_Valor_Base_Imposto_Importacao"               => $rowT296['T296_Valor_Base_Imposto_Importacao'],
                "T055_Aliquota_IPI"                                => $rowT296['T296_Aliquota_IPI'],
                "T055_Aliquota_Imposto_Importacao"                 => $rowT296['T296_Aliquota_Imposto_Importacao'],
                "T055_Aliquota_COFINS"                             => $rowT296['T296_Aliquota_COFINS'],
                "T055_Aliquota_PIS"                                => $rowT296['T296_Aliquota_PIS'],
                "T055_Aliquota_Extra"                              => $rowT296['T296_Aliquota_Extra'],
                "T055_Valor_ICMS"                                  => $rowT296['T296_Valor_ICMS'],
                "T055_Valor_ICMS_Presumido"                        => $rowT296['T296_Valor_ICMS_Presumido'],
                "T055_Valor_ICMS_Pagar"                            => $rowT296['T296_Valor_ICMS_Pagar'],
                "T055_Valor_IPI"                                   => $rowT296['T296_Valor_IPI'],
                "T055_Valor_Imposto_Importacao"                    => $rowT296['T296_Valor_Imposto_Importacao'],
                "T055_Valor_COFINS"                                => $rowT296['T296_Valor_COFINS'],
                "T055_Valor_PIS"                                   => $rowT296['T296_Valor_PIS'],
                "T055_Valor_Extra"                                 => $rowT296['T296_Valor_Extra'],
                "T055_Aliquota_ICMS"                               => $rowT296['T296_Aliquota_ICMS'],
                "T055_Aliquota_ICMS_Presumido"                     => $rowT296['T296_Aliquota_ICMS_Presumido'],
                "T055_Percentual_Reducao_ICMS"                     => $rowT296['T296_Percentual_Reducao_ICMS'],
                "T055_Percentual_Reducao_ICMS_Presumido"           => $rowT296['T296_Percentual_Reducao_ICMS_Presumido'],
                "T055_Aliquota_ICMS_PIS_COFINS"                    => $rowT296['T296_Aliquota_ICMS_PIS_COFINS'],
                "T055_Aliquota_ICMS_Credito_Presumido"             => $rowT296['T296_Aliquota_ICMS_Credito_Presumido'],
                "T055_Adicao_Item"                                 => $rowT296['T296_Adicao_Item'],
                "T055_Flag_Calculo_Manual"                         => $rowT296['T296_Flag_Calculo_Manual'],
                "T055_Peso_Kg_Liquido_Unitario"                    => $rowT296['T296_Peso_Kg_Liquido_Unitario'],
                "T055_Peso_Kg_Liquido_Total"                       => $rowT296['T296_Peso_Kg_Liquido_Total'],
                "T055_Peso_Lb_Liquido_Unitario"                    => $rowT296['T296_Peso_Lb_Liquido_Unitario'],
                "T055_Peso_Lb_Liquido_Total"                       => $rowT296['T296_Peso_Lb_Liquido_Total'],
            );
            
            $sqlChaves = '';
            $sqlDados = '';
            foreach ($insertT055 as $key => $value) {
                $sqlChaves .= $sqlChaves ? ',' : '';
                $sqlChaves .= $key;
                $sqlDados .= $sqlDados ? ',' : '';
                $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
            }
            $sql = "INSERT INTO T055 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
            mysql_query($sql);

            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '2 Erro: ' . mysql_error();
            }  

            $T055_Id = $g['mysqlLastId'];
            mysql_query("INSERT INTO T055A (T055A_T055_Id,
                                            T055A_Largura,
                                            T055A_Altura,
                                            T055A_Comprimento)
                                    VALUES ('{$T055_Id}',
                                            '{$rowT296['D001A_Largura']}',
                                            '{$rowT296['D001A_Altura']}',
                                            '{$rowT296['D001A_Comprimento']}')"); 
        }

        $atualizaStatus = $this->alteraStatusPO($arrayT295);
        if($atualizaStatus !== true){
            mysql_query("ROLLBACK");
            return $atualizaStatus;
        }

        $retorno = $this->limparQuantidadeDI($D024_Id);
        if($retorno === false){
            mysql_query("ROLLBACK");
            return $retorno;
        }

        mysql_query("COMMIT");
        return true;
    }    

    /**
     * conferenciaPO
     * Assina conferencia PO ao realizar conferencia da DI
     *
     * @param int T075_Id  
    */
    public function conferenciaPO($T075_Id)
    {
        global $g;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");

        $T296 = mysql_query("SELECT T296_Id,
                                    T295_Id,
                                    T055_Flag_Produto_Divergencia,
                                    T055A_Observacao_Divergencia 
                               FROM T055
                          LEFT JOIN T296 ON T296_Id  = T055_T296_Id
                          LEFT JOIN T295 ON T295_Id  = T296_T295_Id
                          LEFT JOIN T055A ON T055_Id = T055A_T055_Id
                              WHERE T055_T075_Id = '{$T075_Id}'
                                AND T296_Flag_Finalizar_Compra = 'N'");        

        $arrayT295 = array();
        while($mT296 = mysql_fetch_array($T296)){
            
            // Guarda o T295_Id para assinar a PO como conferida
            $arrayT295[] = $mT296['T295_Id'];

            mysql_query("UPDATE T296 SET T296_C007_Id_Conferiu         = '{$g['usuarioAtual']}',
                                         T296_Flag_Produto_Divergencia = '{$mT296['T055_Flag_Produto_Divergencia']}',
                                         T296_Observacao_Divergencia   = '{$mT296['T055A_Observacao_Divergencia']}'
                                   WHERE T296_Id = '{$mT296['T296_Id']}'");
        }

        foreach(array_unique($arrayT295) as $T295_Id){
            // se todos os itens da OC estiverem como conferido e a compra não estiver finaliza, vai assinar a PO
            $T295 = mysql_query("SELECT T296_Id
                                   FROM T296 
                                  WHERE T296_T295_Id ='{$T295_Id}'
                                    AND T296_C007_Id_Conferiu <= '0' 
                                    AND T296_Flag_Finalizar_Compra = 'N'");
            if(mysql_num_rows($T295) <= 0){
                mysql_query("UPDATE T295 SET T295_C007_Id_Conferiu = '{$g['usuarioAtual']}' WHERE T295_Id = '{$T295_Id}'");                
            }     
        }

        mysql_query("COMMIT");

        return true;
    }  
}

?>
