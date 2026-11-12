<?php
namespace hardness;
definirClasse('IMP004');
class IMP004_
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
     * @param String $T293_Id
     * @param String $selected
     * @return bool
     */     
    public function updateT161_Flag_Ok($D122_Id, $T293_Id, $selected)
    {
        global $g;
        
        $T161  = mysql_query("SELECT T161_Id FROM T161 WHERE T161_D122_Id='$D122_Id' AND T161_T293_Id='$T293_Id'");
        $mT161 = mysql_fetch_array($T161);
        
        if (mysql_num_rows($T161) <= 0) {
            mysql_query("INSERT INTO T161 (T161_D122_Id,T161_T293_Id,T161_Flag_Ok, T161_Data)
                                   VALUES ('$D122_Id','$T293_Id','S', curdate())");
        } else {
            mysql_query("UPDATE T161 SET T161_Flag_Ok='$selected', T161_Data=curdate()
                          WHERE T161_Id='{$mT161['T161_Id']}'");
        }
        return true;  
    }
    
    /**
     * insereRegistrosT161
     *
     * @param String $T293_Id
     */     
    public function insereRegistrosT161($T293_Id)
    {
        
        $sql = "SELECT * FROM T293 WHERE T293_Id='{$T293_Id}'";
        
        $resultado = mysql_query($sql);
        $row       = mysql_fetch_assoc($resultado);
        
        $sql = "SELECT D122_Id FROM D122";
        
        $resultado = mysql_query($sql);
        
        while ($row = mysql_fetch_array($resultado)) {
            
            $T161  = mysql_query("SELECT T161_Id FROM T161 WHERE T161_D122_Id='{$row['D122_Id']}' AND T161_T293_Id='$T293_Id'");
            $mT161 = mysql_fetch_array($T161);
            
            if (mysql_num_rows($T161) <= 0) {
                mysql_query("INSERT INTO T161 (T161_D122_Id,T161_T293_Id,T161_Flag_Ok)
                                       VALUES ('{$row['D122_Id']}','$T293_Id','N')");
            } 
        } 
    }
    
    /**
     * montarEmail
     *
     * @param String $T293_Id
     * @return array
     */     
    public function montarEmail($T293_Id)
    {
        global $g;
        $T293_Id = mysql_real_escape_string($T293_Id);
        $sql     = <<<EOT
        SELECT * FROM T293
        LEFT JOIN D024 as FORNECEDOR ON FORNECEDOR.D024_Id=T293_D024_Id
        LEFT JOIN D024 as DESPACHANTE ON DESPACHANTE.D024_Id=T293_D119_Id
        LEFT JOIN D119 ON D119_Id=T293_D119_Id
        LEFT JOIN D118 ON D118_Id=T293_D118_Id
        LEFT JOIN D044 ON D044_Id=T293_D044_Id
        WHERE T293_Id='{$T293_Id}'
EOT;
        $mT293   = mysql_query($sql);
        
        $array = array();
        
        $array['Para']    = $mT293['FORNECEDOR.D024_E_Mail'];
        $array['Assunto'] = "Declaração de Importação [{$T293_Id}]";
        
        $array['Corpo'] = "";
        
        return $array;
    }
    
    /**
     * Calcular
     *
     * @param String $T293_Id
     * @return bool
     */     
    public function Calcular($T293_Id)
    {
        $T294 = mysql_query("SELECT T294_Id
                               FROM T294 
                              WHERE T294_T293_Id='{$T293_Id}'");
                              
        while ($mT294 = mysql_fetch_array($T294)) {
            mysql_query("call T294_Gravar_Totalizacao('$mT294[T294_Id]')");
        }
        return true;
    }
    
    /**
     * Atualza OC
     *
     * @param String $T293_Id
     * @return bool
     */     
    public function atualizaOC($T293_Id)
    {
        $T294 = mysql_query("SELECT T294_D009_Id
                               FROM T294 
                              WHERE T294_T293_Id='{$T293_Id}'");
        
        while ($mT294 = mysql_fetch_array($T294)) {
            mysql_query("update D009 set D009_Quantidade_OC=D009_Quantidade_OC_3('{$mT294['T294_D009_Id']}',0) where D009_Id = '{$mT294['T294_D009_Id']}'");
        }
        return true;
    }

    /**
     * alterarClientePreCadastro
     *
     * @param String $T293_Id
     * @return String/bool
     */     
    public function gerarNotaFiscal($T293_Id)
    {
        global $g, $T007_Id, $T007_Id_aux;
        
        $T007 = mysql_query("SELECT T007_Id,T007_Numero_Nota_Fiscal,
                                 T007_Data_Emissao 
                            FROM T007 
                           WHERE T007_T293_Id='{$T293_Id}' 
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

        $T293  = mysql_query("SELECT * 
                               FROM T293 
                          LEFT JOIN D006 ON D006_Id=T293_D006_Id
                          LEFT JOIN D118 ON D118_Id=T293_D118_Id
                          LEFT JOIN D018 ON D018_UF=D118_Estado
                              WHERE T293_Id='{$T293_Id}'");
        $mT293 = mysql_fetch_array($T293);
        
        $D024  = mysql_query("SELECT * 
                               FROM D024 
                          LEFT JOIN D018 ON D018_Id=D024_D018_Id
                          LEFT JOIN D020 ON D020_Id=D024_D020_Id
                          LEFT JOIN D030 ON D030_Id=D024_D030_Id
                          LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id='{$mT293['T293_C004_Id']}'
                              WHERE D024_Id = '{$mT293['T293_D024_Id']}'");
        $mD024 = mysql_fetch_array($D024);
        
        if ($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'J') {
            $T007_Cliente_CNPJ_CPF = "{$mD024['D024_Cnpj']}";
        } elseif ($mD024['D024_Flag_Pessoa_Fisica_Juridica'] == 'I') {
            $T007_Cliente_CNPJ_CPF = "";
        } else {
            $T007_Cliente_CNPJ_CPF = "{$mD024['D024_Cpf']}";
        } 
        
        $T294 = mysql_query("SELECT *,sum(T294_Valor_Total_Frete) T294_Valor_Total_Frete,
                                  sum(T294_Valor_Total_Custo*$mT293[T293_Cotacao_ME]) T294_Valor_Total_Custo_Reais,
                                  sum(T294_Valor_Total_Custo) T294_Valor_Total_Custo,
                                  sum(T294_Valor_Aduaneira) T294_Valor_Aduaneira,
                                  sum(T294_Valor_Imposto_Importacao) T294_Valor_Imposto_Importacao,
                                  sum(T294_Valor_IPI) T294_Valor_IPI,
                                  sum(T294_Valor_PIS) T294_Valor_PIS,
                                  sum(T294_Valor_COFINS) T294_Valor_COFINS,
                                  sum(T294_Valor_ICMS) T294_Valor_ICMS,
                                  sum(T294_Valor_ICMS_Presumido) T294_Valor_ICMS_Presumido,
                                  sum(T294_Valor_ICMS_Pagar) T294_Valor_ICMS_Pagar,
                                  sum(T294_Valor_Base_ICMS_Sem_Reducao) T294_Valor_Base_ICMS_Sem_Reducao,
                                  sum(T294_Valor_Base_ICMS_Normal) T294_Valor_Base_ICMS_Normal,
                                  sum(T294_Peso_Kg_Liquido_Unitario) T294_Peso_Kg_Liquido_Unitario,
                                  sum(T294_Valor_Total_Despesas) T294_Valor_Total_Despesas,
                                  sum(T294_Valor_Total_Despesas_Aduaneira) T294_Valor_Total_Despesas_Aduaneira,
                                  sum(T294_Valor_Total_Custo_Final) T294_Valor_Total_Custo_Final,
                                  sum(T294_Valor_Total_AFRMM) T294_Valor_Total_AFRMM,
                                  sum(T294_Valor_Total_Preco_Nota_Fiscal) T294_Valor_Total_Preco_Nota_Fiscal
                             FROM T294
                            WHERE T294_T293_Id='{$T293_Id}'");
        
        $mT294 = mysql_fetch_array($T294);
        $T294_T293 = array_merge($mT293, $mT294);
        
        if ($mT293['D006_Flag_Isento_ICMS']=='S'){
            $mT294['T294_Valor_Base_ICMS_Sem_Reducao']   = 0;
            $mT294['T294_Valor_Base_ICMS_Normal']   = 0;
        }

        $mICMS     = $this->retornaICMS($mT293['D118_Flag_Tipo']);
        $baseICMS  = $mICMS['baseICMS'];
        $valorICMS = $mICMS['valorICMS'];

        $mFreteTotal     = $this->retornaFreteTotal($mT294['T294_Valor_Total_Frete']);
        $valorTotalFrete = $mFreteTotal['valorTotalFrete'];

        $mT294['T294_Valor_Total_Custo_Reais']           = number_format($mT294['T294_Valor_Total_Custo_Reais'], 2, ',', '.');
        $mT294['T294_Valor_Total_Custo']                 = number_format($mT294['T294_Valor_Total_Custo'], 2, ',', '.');
        $mT293['T293_Cotacao_ME']                        = number_format($mT293['T293_Cotacao_ME'], 4, ',', '.');
        $mT294['T294_Valor_Total_Frete']                 = number_format($mT294['T294_Valor_Total_Frete'], 2, ',', '.');
        $mT294['T294_Valor_Aduaneira']                   = number_format($mT294['T294_Valor_Aduaneira'], 2, ',', '.');
        $mT294['T294_Valor_Imposto_Importacao']          = number_format($mT294['T294_Valor_Imposto_Importacao'], 2, ',', '.');
        $mT294['T294_Valor_IPI']                         = number_format($mT294['T294_Valor_IPI'], 2, ',', '.');
        $mT294['T294_Valor_PIS']                         = number_format($mT294['T294_Valor_PIS'], 2, ',', '.');
        $mT294['T294_Valor_COFINS']                      = number_format($mT294['T294_Valor_COFINS'], 2, ',', '.');
        $mT294['T294_Valor_Total_AFRMM']                 = number_format($mT294['T294_Valor_Total_AFRMM'], 2, ',', '.');
        $mT294['T294_Valor_ICMS']                        = number_format($mT294['T294_Valor_ICMS'], 2, ',', '.');
        $mT294['T294_Valor_ICMS_Presumido']              = number_format($mT294['T294_Valor_ICMS_Presumido'], 2, ',', '.');
        $mT294['T294_Valor_ICMS_Pagar']                  = number_format($mT294['T294_Valor_ICMS_Pagar'], 2, ',', '.');
        $mT294['T294_Peso_Kg_Liquido_Unitario']          = number_format($mT294['T294_Peso_Kg_Liquido_Unitario'], 2, ',', '.');
        $mT294['T294_Valor_Total_Preco_Nota_Fiscal']     = number_format($mT294['T294_Valor_Total_Preco_Nota_Fiscal'], 2, ',', '.');
        $mT294['T294_Valor_Total_Despesas_Aduaneira']    = number_format($mT294['T294_Valor_Total_Despesas_Aduaneira'], 2, ',', '.');
        $mT294['T294_Valor_Total_Despesas']              = number_format($mT294['T294_Valor_Total_Despesas'], 2, ',', '.');
        $mT294['T294_Valor_Total_Custo_Final']           = number_format($mT294['T294_Valor_Total_Custo_Final'], 2, ',', '.');
        $mT294['T294_Valor_Base_ICMS_Sem_Reducao']       = number_format($mT294['T294_Valor_Base_ICMS_Sem_Reducao'], 2, ',', '.');
        $mT293['T293_Aliquota_ICMS_Credito_Presumido']   = number_format($mT293['T293_Aliquota_ICMS_Credito_Presumido'], 2, ',', '.');
        $mT293['T293_Percentual_Reducao_ICMS_Presumido'] = number_format($mT293['T293_Percentual_Reducao_ICMS_Presumido'], 2, ',', '.');

        $observacaoFiscal = "II: {$mT294['T294_Valor_Imposto_Importacao']}, PIS: {$mT294['T294_Valor_PIS']}, Cofins: {$mT294['T294_Valor_COFINS']}, Siscomex: {$mT294['T294_Valor_Total_Despesas_Aduaneira']}, Frete: {$mT294['T294_Valor_Total_Frete']}, THC: {$mT294['T294_Valor_Aduaneira']}, AFRMM: {$mT294['T294_Valor_Total_AFRMM']}, Base ICMS: {$mT294[$baseICMS]}, Devido: {$mT294['T294_Valor_ICMS']}, Presumido: {$mT294['T294_Valor_ICMS_Presumido']}, Recolhido: {$mT294['T294_Valor_ICMS_Pagar']}. BASE DE CALCULO REDUZIDA EM {$mT293['T293_Percentual_Reducao_ICMS_Presumido']}% CREDITO PRESUMIDO DE {$mT293['T293_Aliquota_ICMS_Credito_Presumido']}% VALOR CREDITO PRESUMIDO R\$ {$mT294['T294_Valor_ICMS_Presumido']} ICMS RECOLHIDO R\$ {$mT294['T294_Valor_ICMS_Pagar']} DI: {$mT293['T293_Numero_DI']} PO: {$mT293['T293_Ordem_Compra']}";
        $observacaoFiscal='';
        if($mT293['D006_Observacao'] != ''){
            $observacaoFiscal .= $mT293['D006_Observacao'];
        }
        // Váriaveis dentro da Observação Fiscal
        foreach ($T294_T293 as $key => $value) {
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
        $cClausula .= " '{$mT293['T293_D006_Id']}', "; // CFOP // 1
        $cClausula .= " '{$mT293['T293_D024_Id']}', "; // Fornecedor //2
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
        $cClausula .= " '{$mT293['T293_Peso_Bruto']}', "; // Peso Bruto //57
        $cClausula .= " '{$mT293['T293_Peso_Liquido']}', "; // Peso Liquido //58
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
        $cClausula .= " '{$mT293['D006_Flag_Venda_Compra_Outros']}', "; // Flag Venda / Compras / Outros //93
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
        $cClausula .= " '{$T293_Id}', "; // Id DI //108
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
        $T294 = mysql_query("SELECT T066_D004_Id,
                                    T066_Id,
                                    D001_Codigo_Produto,
                                    T294_T066_Id
                            FROM T294
                            LEFT JOIN D009 ON D009_Id=T294_D009_Id
                            LEFT JOIN T066 ON T294_T066_Id=T006_Id
                            WHERE T294_T293_Id='{$T293_Id}'
                            GROUP BY T294_Id");
        $erro = '';
        while ($mT294 = mysql_fetch_array($T294)) {
            if ($mT294['T066_Id']<=0 || $mT294['T066_D004_Id']<=0){
                $erro .= "O produto {$mT294['D001_Codigo_Produto']} está sem locação.<br>";
                continue;
            }
        }
        if (!empty($erro)){                
            mysql_query("ROLLBACK");
            return $erro;
        }
        
        $T007_Id = $g['mysqlLastId'];
        
        $T294 = mysql_query("SELECT * 
                               FROM T294 
                          LEFT JOIN D005 ON D005_Id=T294_D005_Id
                          LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id='{$g['empresaAtual']}'
                          LEFT JOIN D088 ON D088_Id=D149_D088_Id
                          LEFT JOIN D037 ON D037_Id=T294_D037_Id
                              WHERE T294_T293_Id='{$T293_Id}'");
        while ($mT294 = mysql_fetch_array($T294)) {
            

            if ($mT293['D006_Flag_Isento_ICMS']=='S'){
                $mT294['T294_Valor_Base_ICMS_Sem_Reducao']   = 0;
                $mT294['T294_Valor_Base_ICMS_Normal']   = 0;
            }

            $mT294['T294_Valor_Custo_Unitario'] = $mT294['T294_Valor_Custo_Unitario'] * $mT293['T293_Cotacao_ME'];
            
            if ($mT294['T294_Aliquota_IPI'] > 0) {
                $mT294['D088_Codigo_ST'] = '00';
            } else {
                $mT294['D088_Codigo_ST'] = '03';
            }
            $mT294['T294_Descricao_Produto'] = mysql_real_escape_string($mT294['T294_Descricao_Produto']);

            // retorna valor do frete
            $mFrete = $this->retornaFrete($mT294['T294_Valor_Total_Frete']);
            $valorFrete   = $mFrete['valorFrete'];
            
            // retorna FCP 
            $mFCP        = $this->retornaFCP($mD024['D053_Percentual_FCP'], $mT293['D006_Flag_Calcula_FCP'], $mT294[$baseICMS]);
            $aliquotaFCP = $mFCP['aliquotaFCP'];
            $baseFCP     = $mFCP['baseFCP'];
            $valorFCP    = $mFCP['valorFCP'];

            if($mT293['D006_Flag_Calcula_FCP'] && $aliquotaFCP > 0){
                // Se calcula FCP precisa descontar a aliquota e valor FCP do valor da aliquota e valor ICMS, na nota fiscal é enviado em campos separados
                $mT294['T294_Aliquota_ICMS'] -= $aliquotaFCP;
                $mT294[$valorICMS]           -= $valorFCP;
            }
        

            $T066 = mysql_query("select T294_T066_Id, T066_D004_Id from T294 lef join T066 on T066_Id=T294_T066_Id where T294_Id='{$mT294['T294_Id']}'");
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
            $cClausula .= "T008_T294_Id, "; // 81
            $cClausula .= "T008_Valor_Total_Frete, "; // 82
            $cClausula .= "T008_Percentual_FCP, "; // 83
            $cClausula .= "T008_Valor_FCP, "; // 84
            $cClausula .= "T008_Valor_Base_FCP "; // 85
            $cClausula .= ") VALUES (";
            $cClausula .= "'{$g['c004']['C004_Id']}',"; // Empresa Atual  // 1
            $cClausula .= "'{$T007_Id}',"; // T007_Id // 2
            $cClausula .= "'{$mT293['T293_D006_Id']}',"; // D006_Id // 3
            $cClausula .= "'{$mT294['T294_D037_Id']}',"; // D037_Id // 4
            $cClausula .= "'{$mT293['D006_D059_Id']}',"; // D059_Id // 5
            $cClausula .= "'{$mT294['D037_Unidade']}',"; // D037_Id // 6
            $cClausula .= "'{$mT293['D006_Codigo_CFOP']}',"; // D006_Codigo_CFOP // 7
            $cClausula .= "'{$mT294['T294_Aliquota_IPI']}',"; // Aliquota IPI // 8
            $cClausula .= "'0',"; // Percentual Reducao IPI // 9
            $cClausula .= "'{$mT294['T294_Aliquota_ICMS']}',"; // Aliquota ICMS // 10
            $cClausula .= "'{$mT294['T294_Percentual_Reducao_ICMS']}',"; // Percentual Reducao ICMs // 11
            $cClausula .= "'{$mT294['T294_Peso_Kg_Liquido_Unitario']}',"; // Peso Unitario // 12
            $cClausula .= "'{$mT294['T294_D005_Id']}',"; // D005_Id // 13
            $cClausula .= "'100',"; // Flag situacao tributaria ICMS // 14
            $cClausula .= "'',"; // Flag Preco Especial // 15
            $cClausula .= "'{$mT294['D005_Classificacao_Fiscal']}',"; // NCM // 16
            $cClausula .= "'{$mT294['D088_Codigo_ST']}', "; // Situacao tributaria IPI // 17                                               
            $cClausula .= "'{$mT294['T294_Quantidade']}',"; // Quantidade // 18
            $cClausula .= "'{$mT294['T294_Codigo_Produto']}',"; // Codigo Produto // 19
            $cClausula .= "'',"; // Codigo Barras // 20
            $cClausula .= "'',"; // Codigo Substituto // 21
            $cClausula .= "'{$mT294['T294_D009_Id']}',"; // D009_Id // 22
            $cClausula .= "'{$mT294['T294_Descricao_Produto']}',"; // Descricao Produto // 23
            $cClausula .= "'{$mT294['T294_Valor_Custo_Final_Unitario']}',"; // Valor Custo Unitario // 24
            $cClausula .= "'{$mT294['T294_Valor_Preco_Unitario_Nota_Fiscal']}',"; // Valor Preco sem desconto unitario // 25
            $cClausula .= "'N',"; // Flag ST // 26
            $cClausula .= "'N',"; // ST VA // 27
            $cClausula .= "'0',"; // ICMS Inter Estadual // 28
            $cClausula .= "'0',"; // ST ICMS Interno // 29
            $cClausula .= "'{$mT293['D006_Flag_Isento_PIS']}',"; // Flag Isento PIS // 30
            $cClausula .= "'{$mT294['T294_Aliquota_PIS']}',"; // Aliquota PIS // 31
            $cClausula .= "'{$mT293['D006_Situacao_Tributaria_PIS']}',"; // Situacao Tributaria PIS // 32
            $cClausula .= "'{$mT293['D006_Flag_Isento_COFINS']}',"; // Flag Isento COFINS // 33
            $cClausula .= "'{$mT294['T294_Aliquota_COFINS']}',"; // Aliquota COFINS // 34
            $cClausula .= "'{$mT293['D006_Situacao_Tributaria_COFINS']}', "; // Situacao Tributaria COFINS // 35
            $cClausula .= "'{$mT294['T294_Valor_Base_Imposto_Importacao']}', "; // Base II // 36
            $cClausula .= "'{$mT294['T294_Valor_Imposto_Importacao']}', "; // Valor II // 37
            $cClausula .= "'', "; // Despesas Aduaneira // 38
            $cClausula .= "'', "; // Valor IOF // 39
            $cClausula .= "'{$mT293['T293_Numero_DI']}', "; // Numero DI // 40
            $cClausula .= "'{$mT293['T293_Data_Emissao']}', "; // Data registro DI // 41
            $cClausula .= "'{$mT293['D118_Nome']}', "; // Local Desembaraco // 42
            $cClausula .= "'{$mT293['T293_Data_Emissao']}', "; // Data Desenbaraco // 43
            $cClausula .= "'{$mT293['D018_UF']}', "; // UF Desenbaraco // 44
            $cClausula .= "'{$mT293['T293_D024_Id']}', "; // Codigo Exportador // 45
            $cClausula .= "'{$mT294['T294_Valor_Adicao']}', "; // Numero Adicao // 46
            $cClausula .= "'1', "; // Numero sequencia adicao // 47
            $cClausula .= "'{$mT293['T293_D024_Id']}', "; // Codigo Fabricante // 48
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
            $cClausula .= "'{$mT294[$baseICMS]}', "; // 64
            $cClausula .= "'{$mT294[$valorICMS]}', "; // 65
            $cClausula .= "'{$mT294['T294_Valor_Base_IPI']}', "; // 66
            $cClausula .= "'{$mT294['T294_Valor_IPI']}', "; // 67
            $cClausula .= "'{$mT294['T294_Valor_Base_PIS']}', "; // 68
            $cClausula .= "'{$mT294['T294_Valor_PIS']}', "; // 69
            $cClausula .= "'{$mT294['T294_Valor_Base_COFINS']}', "; // 70
            $cClausula .= "'{$mT294['T294_Valor_COFINS']}', "; // 71
            $cClausula .= "'{$mT294['T294_Valor_Base_ICMS_Normal']}', "; // 72
            $cClausula .= "'{$mT293['T293_Flag_Transporte_DI']}', "; // 73
            $cClausula .= "'{$mT293['T293_Flag_Tipo_Intermedio_DI']}', "; // 74
            $cClausula .= "'{$mT293['T293_CNPJ_Importador']}', "; // 75
            $cClausula .= "'{$mT293['T293_UF_Importador']}', "; // 76
            $cClausula .= "'{$mT293['T293_Numero_Drawback_Importacao']}', "; // 77
            $cClausula .= "'{$mT294['T294_Valor_Preco_Unitario_Nota_Fiscal']}', "; // Valor Preco sem desconto unitario // 78
            $cClausula .= "'{$mT066['T294_T066_Id']}', "; // 79
            $cClausula .= "'{$mT066['T066_D004_Id']}', "; // 80
            $cClausula .= "'{$mT294['T294_Id']}', "; // 81
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

        if(!empty($mT293['D006_Observacao_Produto'])){
            // Busca só o FCP, se precisar de mais campos incluir no select
            $T008 = mysql_query("SELECT T008_Valor_FCP,
                                        T008_Id
                                   FROM T008
                                  WHERE T008_T007_Id = {$T007_Id}");

            while ($mT008 = mysql_fetch_array($T008)) {
                $observacaoFiscalProduto = $mT293['D006_Observacao_Produto'] . ';';

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
     * @param String $T293_Id
     * @return String/bool
     */     
    public function imp007Finalizar($T293_Id)
    {
        global $g;
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        

        /**
         * Verificar se todos itens tem locação
         */
        $T294 = mysql_query("SELECT T066_D004_Id,
                                    T066_Id,
                                    D001_Codigo_Produto,
                            FROM T294
                            LEFT JOIN D009 ON D009_Id=T294_D009_Id
                            LEFT JOIN T066 ON T066_D009_Id=D009_Id
                            WHERE T294_T293_Id='{$T293_Id}'
                            GROUP BY T294_Id");
        $erro = '';
        while ($mT294 = mysql_fetch_array($T294)) {
            if ($mT294['T066_Id']<=0 || $mT294['T066_D004_Id']<=0){
                $erro .= "O produto {$mT294['D001_Codigo_Produto']} está sem locação.<br>";
                continue;
            }
        }

        if (!empty($erro)){
            return $erro;
        }

        $T294_T066_Id = mysql_query("SELECT T294_T066_Id FROM T294 WHERE T294_T293_Id='{$T293_Id}' and ifnull(T294_T066_Id,0)<=0");        
        if(mysql_num_rows($T294_T066_Id)>0){
          return "Existem produtos sem locação, favor preencher antes de atualizar o estoque.";
        }

        /**
         * SELECIONANDO TODOS OS ITENS DA DI
         */
        $T294 = mysql_query("SELECT T293_Id,
                                    T294_Id,
                                    T294_Quantidade,
                                    T294_D009_Id,
                                    T294_D006_Id,
                                    T293_D024_Id,
                                    T066_D004_Id,
                                    T066_Id,
                                    D049_Id,
                                    D001_Flag_Produto_Serie,
                                    T001_Id,
                                    T294_T066_Id
                            FROM T294 
                            LEFT JOIN T293 ON T293_Id='{$T293_Id}' 
                            LEFT JOIN D009 ON D009_Id=T294_D009_Id
                            LEFT JOIN D049 ON D049_Id=D009_D049_Id
                            LEFT JOIN D001 ON D001_Id=D049_D001_Id
                            LEFT JOIN T066 ON T066_Id=T294_T066_Id
                            LEFT JOIN T001 ON T001_T294_Id=T294_Id AND T001_Flag_Operacao!='C' AND T001_Descricao_Operacao like 'IMP-%'
                            WHERE T294_T293_Id='{$T293_Id}'
                            GROUP BY T294_Id");
        
        $T293  = mysql_query("SELECT T293_Data_Estoque FROM T293 WHERE T293_Id='{$T293_Id}'");
        $mT293 = mysql_fetch_array($T293);
        if ($mT293['T293_Data_Estoque'] != null && $mT293['T293_Data_Estoque'] != '0000-00-00') {
            return "Esse processo já foi realizado";
        }
        
        if (mysql_num_rows($T294) == 0) {
            return "Não existem produtos nessa DI";
        }
        
        // LOOPING PARA INSERIR NO HISTÓRICO DO PRODUTO
        while ($mT294 = mysql_fetch_array($T294)) {
            if ($mT294['T001_Id']>0){
                continue;
            }
            $sql = <<<EOT
            INSERT INTO T001 (
                T001_Descricao_Operacao,
                T001_Flag_Operacao,
                T001_Quantidade,
                T001_Data_Lancamento,
                T001_T294_Id,
                T001_D009_Id,
                T001_D006_Id,
                T001_D024_Id,
                T001_D004_Id,
                T001_T066_Id
            ) VALUES (
                'IMP-{$T293_Id}',
                'E',
                '{$mT294['T294_Quantidade']}',
                CURDATE(),
                '{$mT294['T294_Id']}',
                '{$mT294['T294_D009_Id']}',
                '{$mT294['T294_D006_Id']}',
                '{$mT294['T293_D024_Id']}',
                '{$mT294['T066_D004_Id']}',
                '{$mT294['T294_T066_Id']}'
            );
EOT;
            mysql_query($sql);
            $erro = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return "1 " . $erro;
            }
            
            $T006 = mysql_query("UPDATE T006 SET T006_T293_Id='' WHERE T006_T293_Id='{$T293_Id}' and T006_D009_Id='{$mT294['T294_D009_Id']}'");
            $erro = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return "2 " . $erro;
            }

            // $retorno = $CAD002->D001_reprocessa_historico(0, $mT294['T294_D009_Id'],100);
            $retornoProcessa = $CAD002->D001_reprocessa_historico(0, $mT294['T294_D009_Id'],100);
            if($retornoProcessa !== true){
                registrarLog(array("D009", $mT294['T294_D009_Id'], $g['empresaAtual'], " Erro ao reprocessar histórico - IMP004 - ATUALIZAR CUSTO:", $retornoProcessa, "S",  3));
                $retorno = $CAD002->D001_reprocessa_historico(0, $mT294['T294_D009_Id']);

                if ($retorno != true) {
                    mysql_query("ROLLBACK");
                    return "Erro reprocessa histórico: " . $retorno;
                }
            }
            
            if ($mT294['D001_Flag_Produto_Serie'] == 'S') {
                
                $quantidade = $mT294['T294_Quantidade'];
                
                for ($i = 1; $i <= $quantidade; $i++) {
                    
                    $D144 = <<<EOT
                        INSERT INTO D144 (
                            D144_D049_Id,
                            D144_T294_Id
                        ) VALUES (
                            '{$mT294['D049_Id']}',
                            '{$mT294['T294_Id']}'
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

        $T293 = mysql_query("UPDATE T293 SET T293_Data_Estoque=CURDATE() WHERE T293_Id='{$T293_Id}'");
        $erro = mysql_error();
        if ($erro) {
            mysql_query("ROLLBACK");
            return "3 " . $erro;
        }

        mysql_query("COMMIT");
        return true;
    }

    /**
     * IMP004AtualizarCusto
     *
     * @param String $T293_Id
     * @return String/bool
     */     
    public function IMP004AtualizarCusto($T293_Id)
    {
        global $g;
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        
        mysql_query("SET autocommit=0");
        mysql_query("START TRANSACTION");
        
        /**
         * SELECIONANDO TODOS OS ITENS DA DI
         */
        $T294 = mysql_query("SELECT *, T001_Valor_Custo_Unitario, T294_Valor_Custo_Final_Unitario, T294_Codigo_Produto FROM T293
                            LEFT JOIN T008 ON T008_T007_Id = T007_Id
                            LEFT JOIN T001 ON T001_T008_Id = T008_Id
                            LEFT JOIN T294 ON T294_D009_Id = T001_D009_Id AND T294_T293_Id = T293_Id 
                            WHERE T293_Id = '{$T293_Id}'");
        
        if (mysql_num_rows($T294) == 0) {
            return "Não existem produtos nessa DI";
        }
        
        // Loop para atualizar os custos na T001 (Histórico do Produto)
        while ($mT294 = mysql_fetch_array($T294)) {
            mysql_query("UPDATE T001 SET T001_Valor_Custo_Unitario = '{$mT294['T294_Valor_Custo_Final_Unitario']}' WHERE T001_Id ='{$mT294['T001_Id']}'");
            $erro = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return "1 " . $erro;
            }
            
            // $retorno = $CAD002->D001_reprocessa_historico(0, $mT294['T294_D009_Id'],100);
            $retornoProcessa = $CAD002->D001_reprocessa_historico(0, $mT294['T294_D009_Id'],100);
            if($retornoProcessa !== true){
                registrarLog(array("D009", $mT294['T294_D009_Id'], $g['empresaAtual'], " Erro ao reprocessar histórico - IMP004 - FINALIZAR:", $retornoProcessa, "S",  3));
                $retorno = $CAD002->D001_reprocessa_historico(0, $mT294['T294_D009_Id']);

                if ($retorno != true) {
                    mysql_query("ROLLBACK");
                    return "Erro reprocessa histórico: " . $retorno;
                }
            }
        }
        mysql_query("UPDATE T293 SET T293_Data_Atualizacao_Custo = now() WHERE T293_Id = '{$T293_Id}'");
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
     * @param String $T293_Id
     * @param String $D024_Id
     * @return String/bool
     */
    public function importarItensOC($T293_Id, $D024_Id)
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
                INSERT INTO T294 (
                    T294_Descricao_Produto,
                    T294_Quantidade,
                    T294_D037_Id,
                    T294_D005_Id,
                    T294_Valor_Custo_Unitario,
                    T294_Flag_ME,
                    T294_Aliquota_Imposto_Importacao,
                    T294_Aliquota_IPI,
                    T294_Aliquota_PIS,
                    T294_Aliquota_COFINS,
                    T294_D009_Id,
                    T294_Codigo_Produto,
                    T294_T293_Id,
                    T294_C004_Id,
                    T294_T225_Id
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
                    '{$T293_Id}',
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

    public function GerarXMLLI($T293_Id)
    {
        global $g;

        set_time_limit(0);
        $dir = $g['pathDados'] . 'tmp/';
        is_dir($dir) OR mkdir($dir, 0777, true);
        $filename             = "xml-li-{$T293_Id}.xml";
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
                   from T293
                   left join C004 on C004_Id=T293_C004_Id
                   left join C020 on C020_C004_Id=C004_Id
                   left join D024 on D024_Id=T293_D024_Id
                   left join D030 on D030_Id=D024_D030_Id
                   left join D020 on D020_Id=D024_D020_Id
                   left join D018 on D018_Id=D024_D018_Id
                   left join D053 on D053_D018_Id=D024_D018_Id AND D053_C004_Id=T293_C004_Id
                   left join T294 on T294_T293_Id=T293_Id  
                   left join D005 on D005_Id=T294_D005_Id
                   left join T270 on T270_T293_Id=T293_Id

                   where T293_Id='{$T293_Id}'
                   group by D005_Id
                   order by D005_Classificacao_Fiscal
EOT;
*/
 
        $cClausula = <<<EOT
                 SELECT T270_Numero_LI,
                        T270_Id,
                        T270_Numero_Processo_Anuente, 
                        T270_Sigla_Orgao_Anuente, 
                        T294_Item_Ac_Drawback,
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
                        T293_Numero_Drawback_Importacao,
                        T293_Codigo_URF_Entrada,   
                        T293_Codigo_URF_Despacho,                    
                        T293_Codigo_Moeda,
                        T293_Numero_DI,
                        T293_Codigo_Incoterms,
                        T293_Condicao_Mercadoria,
                        T293_Enquadramento_Material_Usado,
                        T293_Operacao_Enquadramento_Material_Usado ,
                        T270_Cobertura_Cambial,
                        T270_Modalidade_Pagamento          
                FROM T270
                LEFT JOIN T293 ON T293_id=T270_T293_id
                LEFT JOIN C004 ON C004_Id=T293_C004_Id
                LEFT JOIN C020 ON C020_C004_Id=C004_Id
                LEFT JOIN D024 ON D024_Id=T293_D024_Id
                LEFT JOIN D030 ON D030_Id=D024_D030_Id
                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                LEFT JOIN D018 ON D018_Id=D024_D018_Id
                LEFT JOIN T294 ON T294_id=T270_id
                LEFT JOIN D005 ON D005_Id=T294_D005_Id
                
                WHERE T293_Id='{$T293_Id}'
                ORDER BY T270_Numero_LI
EOT;
        $cSql_T293 = mysql_query($cClausula);
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

        while ($mT293 = mysql_fetch_array($cSql_T293)) {

              $li = $dom->createElement("li");

              $item = $dom->createElement("identificador-li","{$mT293['T270_Numero_LI']}");
              $li->appendChild($item);

              if($mT293['D024_Flag_Pessoa_Fisica_Juridica'] == 'J'){
                $tipoPessoa = 1;
              } else{
                $tipoPessoa = 2;
              }
              $item = $dom->createElement("tipo-importador","{$tipoPessoa}");
              $li->appendChild($item);
              $item = $dom->createElement("identificacao-importador","{$mT293['C004_Cnpj']}");
              $li->appendChild($item);
              $item = $dom->createElement("pais-procedencia-mercadoria","{$mT293['D030_Nome_Pais']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-urf-entrada","{$mT293['T293_Codigo_URF_Entrada']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-urf-despacho","{$mT293['T293_Codigo_URF_Despacho']}");
              $li->appendChild($item);
              $item = $dom->createElement("texto-informacoes-complementares","{$mT293['T270_Informacoes_Complementares']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-fornecedor","{$tipoPessoa}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-nome","{$mT293['D024_Nome_Empresa']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-email","{$mT293['D024_E_Mail']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-responsavel","{$mT293['D024_Nome_Contato']}");
              $li->appendChild($item);
              $item = $dom->createElement("pais-aquisicao-mercadoria","{$mT293['D030_Nome_Pais']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-logradouro","{$mT293['D024_Endereco']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-numero","{$mT293['D024_Numero']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-complemento","{$mT293['D024_Complemento']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-cidade","{$mT293['D020_Nome_Cidade']}");
              $li->appendChild($item);
              $item = $dom->createElement("fornecedor-estrangeiro-uf","{$mT293['D018_UF']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-nome", "{$mT293['D024_Nome_Empresa']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-email","{$mT293['D024_E_Mail']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-responsavel","D024_Nome_Contato");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-logradoudo","{$mT293['D024_Endereco']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-numero","{$mT293['D024_Numero']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-complemento", "{$mT293['D024_Complemento']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-cidade","{$mT293['D020_Nome_Cidade']}");
              $li->appendChild($item);
              $item = $dom->createElement("fabricante-endereco-estado","{$mT293['D018_UF']}");
              $li->appendChild($item);
              $item = $dom->createElement("pais-origem-mercadoria","{$mT293['D030_Nome_Pais']}");
              $li->appendChild($item);
              $item = $dom->createElement("subitem-ncm","{$mT293['D005_Classificacao_Fiscal']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-mercadoria-naladi","{$mT293['D005_Classificacao_Fiscal']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-moeda","{$mT293['T293_Codigo_Moeda']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-incoterms","{$mT293['T293_Codigo_Incoterms']}");
              $li->appendChild($item);
              $item = $dom->createElement("condicao-mercadoria","{$mT293['T293_Condicao_Mercadoria']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-enquadramento-material-usado", "{$mT293['T293_Enquadramento_Material_Usado']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-operacao-enquadramento-material-usado", "{$mT293['T293_Operacao_Enquadramento_Material_Usado']}");
              $li->appendChild($item);
              $item = $dom->createElement("destaque-ncm");
                $subItem = $dom->createElement("numero-destaque-ncm","{$mT293['T270_Numero_Destaque_NCM']}");
                $item->appendChild($subItem);
              $li->appendChild($item);
              $item = $dom->createElement("processo-anuente");
                $subItem = $dom->createElement("numero-processo-anuente", "{$mT293['T270_Numero_Processo_Anuente']}");
                $item->appendChild($subItem);
                $subItem = $dom->createElement("sigla-orgao-anuente", "{$mT293['T270_Sigla_Orgao_Anuente']}");
                $item->appendChild($subItem);
              $li->appendChild($item);
              $item = $dom->createElement("drawback-regime","{$mT293['T293_Numero_Drawback_Importacao']}");
              $li->appendChild($item);
              $item = $dom->createElement("drawback-numero-ato-isencao", "{$mT293['T270_Drawback_Ato_Isencao']}");
              $li->appendChild($item);
              $item = $dom->createElement("drawback-numero-ato-suspencao", "{$mT293['T270_Drawback_Ato_Suspensao']}");
              $li->appendChild($item);            
              $item = $dom->createElement("detalhe-ncm-item-drawback");
                $seqItem = 1;
                $T294 = mysql_query("select D037_Unidade, 
                                            T294_Peso_Kg_Liquido_Unitario, 
                                            T294_Quantidade, 
                                            T294_Valor_Custo_Unitario, 
                                            T294_Descricao_Produto, 
                                            D082_Marca,
                                            T294_Item_Ac_Drawback 
                                      FROM T294 
                                      LEFT JOIN D037 on D037_Id=T294_D037_Id 
                                      LEFT JOIN D009 ON D009_Id=T294_d009_Id 
                                      LEFT JOIN D049 ON D049_id=D009_D049_Id
                                      LEFT JOIN D082 ON D082_id=D049_D082_Id 
                                      WHERE T294_T270_Id='{$mT293['T270_Id']}'");
                while ($mT294 = mysql_fetch_array($T294)){

                        $subItem = $dom->createElement("numero-sequencial-produto","{$seqItem}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("nome-unidade-medida-comercializada","{$mT294['D037_Unidade']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("peso-liquido","{$mT294['T294_Peso_Kg_Liquido_Unitario']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("qtd-mercadoria-unidade-xcomercializada","{$mT294['T294_Quantidade']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("qtd-mercadoria-unidade-estatistica","{$mT294['T294_Quantidade']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("valor-unidade-condicao-venda","{$mT294['T294_Valor_Custo_Unitario']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("valor-unidade-local-embarque","{$mT294['T294_Valor_Custo_Unitario']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("descricao-produto","{$mT294['T294_Descricao_Produto']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("marca", "{$mT294['D082_Marca']}");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("modelo");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("numero-serie");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("ano-fabricacao");
                        $item->appendChild($subItem);
                        $subItem = $dom->createElement("item-ac-drawback", "{$mT294['T294_Item_Ac_Drawback']}");
                        $item->appendChild($subItem);
                        $seqItem++;
                }
              $li->appendChild($item);
              $item = $dom->createElement("regime-acordo-tributario","{$mT293['T270_Regime_Acordo_Tributario']}");
              $li->appendChild($item);
              $item = $dom->createElement("fundamento-legal-regime", "{$mT293['T270_Fundamento_Legal_Regime']}");
              $li->appendChild($item);
              $item = $dom->createElement("tipo-acordo-tarifario", "{$mT293['T270_Tipo_Acordo_Tarifario']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-acordo-aladi", "{$mT293['T270_Codigo_Acordo_Alardi']}");
              $li->appendChild($item);
              $item = $dom->createElement("cobertura-cambial","{$mT293['T270_Cobertura_Cambial']}");
              $li->appendChild($item);
              $item = $dom->createElement("modalidade-pagamento","{$mT293['T270_Modalidade_Pagamento']}");
              $li->appendChild($item);
              $item = $dom->createElement("numero-dias-limite-pagamento", "{$mT293['T270_Numero_Dias_Limite_Pagamento']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-orgao-financeiro-internacional", "{$mT293['T270_Codigo_Orgao_Financeiro_Internacional']}");
              $li->appendChild($item);
              $item = $dom->createElement("codigo-motivo-sem-cobertura", "{$mT293['T270_Codigo_Motivo_Sem_Cobertura']}");
              $li->appendChild($item);
              $item = $dom->createElement("numero-li-substituida", "{$mT293['T270_Numero_LI']}");
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
    public function geraDownloadXMLLI($T293_Id)
    {
        global $g;
        
        $dir = $g['pathDados'] . 'tmp/';
        $filename             = "xml-li-{$T293_Id}.xml";
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

                    $cInsert = "INSERT INTO T294 
                                      (T294_T293_Id, /*acaoId*/
                                       T294_D009_Id, /*Id Produto Marca*/
                                       T294_Codigo_Produto, /*[0]*/  
                                       T294_Descricao_Produto, /*descricao produto*/ 
                                       T294_Quantidade,    /*[1]*/ 
                                       T294_D037_Id,/*[2] - unidade*/
                                       T294_D005_Id,/*[3] - ncm*/
                                       T294_Valor_Custo_Unitario, /*[4]*/ 
                                       T294_Flag_ME,  /*[5]*/ 
                                       T294_Peso_Kg_Liquido_Unitario, /*[6]*/ 
                                       T294_Aliquota_Imposto_Importacao, /*[7]*/ 
                                       T294_Aliquota_IPI, /*[8]*/ 
                                       T294_Aliquota_PIS, /*[9]*/ 
                                       T294_Aliquota_COFINS,/*[10]*/ 
                                       T294_Valor_Adicao,/*[11]*/
                                       T294_Adicao_Item, /*Adicao item*/ 
                                       T294_T066_Id,/*[12] - Local*/
                                       T294_Item_Ac_Drawback,/*[13]*/ 
                                       T294_T270_Id, /*[14] LI*/
                                       T294_Aliquota_ICMS,/*[15]*/ 
                                       T294_Aliquota_ICMS_Presumido,/*[16]*/ 
                                       T294_Percentual_Reducao_ICMS,/*[17]*/ 
                                       T294_Percentual_Reducao_ICMS_Presumido,/*[18]*/ 
                                       T294_Aliquota_ICMS_PIS_COFINS,/*[19]*/ 
                                       T294_Aliquota_ICMS_Credito_Presumido)/*[20]*/ 
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


                    $cSQL_T294 = mysql_query($cInsert);
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
            $baseICMS  = "T294_Valor_Base_ICMS_Sem_Reducao";
            $valorICMS = "T294_Valor_ICMS";
        } else {
            //caso o tipo da D.I no cadastro do porto seja Simplificada
            $baseICMS  = "T294_Valor_Base_ICMS_Normal";
            $valorICMS = "T294_Valor_ICMS_Presumido";
        }

        $array = array();        
        $array['baseICMS']  = $baseICMS;
        $array['valorICMS'] = $valorICMS;

        return $array;
    }

     /**
     * retornaFreteTotal
     *
     * @param String $T294_Valor_Total_Frete
     * @return array
     */

    public function retornaFreteTotal($T294_Valor_Total_Frete)
    {        
        // recebe a soma do T294_Valor_Total_Frete
        $array = array();
        $array['valorTotalFrete'] = 0;

        return $array;
    }
    
     /**
     * retornaFrete
     *
     * @param String $T294_Valor_Total_Frete
     * @return array
     */

    public function retornaFrete($T294_Valor_Total_Frete)
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
     * @param Int $T293_Id $D024_Id
     * @return String/bool
     */
    public function gerarPO($T293_Id, $D024_Id){
        global $g;

        if($D024_Id){
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            $T293    = mysql_query("SELECT * FROM T293 WHERE T293_Id='{$T293_Id}'");
            $rowT293 = mysql_fetch_array($T293);

            $C004 = mysql_query("SELECT C004_Observao_Purchase_Order 
                                   FROM C004
                                  WHERE C004_Id = '{$g['empresaAtual']}'");
            $mC004 = mysql_fetch_array($C004);
            
            $insertT295 = array(
                "T295_D018_Id"                               => $rowT293['T293_D018_Id'],
                "T295_T054_Id"                               => $rowT293['T293_T054_Id'],
                "T295_Data_Emissao"                          => date('Y-m-d'),
                "T295_Numero_DI"                             => $rowT293['T293_Numero_DI'],
                "T295_Valor_Produtos"                        => $rowT293['T293_Valor_Produtos'],
                "T295_Valor_Frete"                           => $rowT293['T293_Valor_Frete'],
                "T295_Valor_CIF"                             => $rowT293['T293_Valor_CIF'],
                "T295_Valor_THC"                             => $rowT293['T293_Valor_THC'],
                "T295_Valor_SISCOMEX"                        => $rowT293['T293_Valor_SISCOMEX'],
                "T295_Valor_Imposto_Importacao"              => $rowT293['T293_Valor_Imposto_Importacao'],
                "T295_Valor_IPI"                             => $rowT293['T293_Valor_IPI'],
                "T295_Valor_PIS"                             => $rowT293['T293_Valor_PIS'],
                "T295_Valor_COFINS"                          => $rowT293['T293_Valor_COFINS'],
                "T295_Valor_DI"                              => $rowT293['T293_Valor_DI'],
                "T295_Local_Desembaraco"                     => $rowT293['T293_Local_Desembaraco'],
                "T295_Data_Desembaraco"                      => $rowT293['T293_Data_Desembaraco'],
                "T295_Codigo_Exportador"                     => $rowT293['T293_Codigo_Exportador'],
                "T295_D024_Id"                               => $rowT293['T293_D024_Id'],
                "T295_D118_Id"                               => $rowT293['T293_D118_Id'],
                "T295_D119_Id"                               => $rowT293['T293_D119_Id'],
                "T295_Cotacao_ME"                            => $rowT293['T293_Cotacao_ME'],
                "T295_Valor_Frete_Incluso"                   => $rowT293['T293_Valor_Frete_Incluso'],
                "T295_Valor_ICMS"                            => $rowT293['T293_Valor_ICMS'],
                "T295_Valor_Seguro"                          => $rowT293['T293_Valor_Seguro'],
                "T295_Valor_Despesa"                         => $rowT293['T293_Valor_Despesa'],
                "T295_Observacao_DI"                         => $rowT293['T293_Observacao_DI'],
                "T295_Observacao_NF"                         => $rowT293['T293_Observacao_NF'],
                "T295_C004_Id"                               => $rowT293['T293_C004_Id'],
                "T295_Data_Embarque"                         => $rowT293['T293_Data_Embarque'],
                "T295_ETA_Data"                              => $rowT293['T293_ETA_Data'],
                "T295_ETA_Hora"                              => $rowT293['T293_ETA_Hora'],
                "T295_D044_Id"                               => $rowT293['T293_D044_Id'],
                "T295_Aliquota_ICMS"                         => $rowT293['T293_Aliquota_ICMS'],
                "T295_Aliquota_ICMS_Presumido"               => $rowT293['T293_Aliquota_ICMS_Presumido'],
                "T295_Percentual_Reducao_ICMS"               => $rowT293['T293_Percentual_Reducao_ICMS'],
                "T295_Percentual_Reducao_ICMS_Presumido"     => $rowT293['T293_Percentual_Reducao_ICMS_Presumido'],
                "T295_Valor_VMLE"                            => $rowT293['T293_Valor_VMLE'],
                "T295_Valor_VMLD"                            => $rowT293['T293_Valor_VMLD'],
                "T295_Aliquota_ICMS_PIS_COFINS"              => $rowT293['T293_Aliquota_ICMS_PIS_COFINS'],
                "T295_D006_Id"                               => $rowT293['T293_D006_Id'],
                "T295_Aliquota_ICMS_Credito_Presumido"       => $rowT293['T293_Aliquota_ICMS_Credito_Presumido'],
                "T295_Peso_Liquido"                          => $rowT293['T293_Peso_Liquido'],
                "T295_Peso_Bruto"                            => $rowT293['T293_Peso_Bruto'],
                "T295_C007_Id"                               => $rowT293['T293_C007_Id'],
                "T295_Ordem_Compra"                          => $rowT293['T293_Ordem_Compra'],
                "T295_Data_Previsao"                         => $rowT293['T293_Data_Previsao'],
                "T295_Data_Estoque"                          => $rowT293['T293_Data_Estoque'],
                "T295_Valor_AFRMM"                           => $rowT293['T293_Valor_AFRMM'],
                "T295_Data_Atualizacao_Custo"                => $rowT293['T293_Data_Atualizacao_Custo'],
                "T295_Flag_MP"                               => $rowT293['T293_Flag_MP'],
                "T295_Data_Licenca_Importacao"               => $rowT293['T293_Data_Licenca_Importacao'],
                "T295_Calculo_THC"                           => $rowT293['T293_Calculo_THC'],
                "T295_Somar_PIS_Custo"                       => $rowT293['T293_Somar_PIS_Custo'],
                "T295_Somar_COFINS_Custo"                    => $rowT293['T293_Somar_COFINS_Custo'],
                "T295_Somar_Seguro_Custo"                    => $rowT293['T293_Somar_Seguro_Custo'],
                "T295_Somar_AFRMM_Custo"                     => $rowT293['T293_Somar_AFRMM_Custo'],
                "T295_Somar_ICMS_Pagar_Custo"                => $rowT293['T293_Somar_ICMS_Pagar_Custo'],
                "T295_Somar_IPI_Custo"                       => $rowT293['T293_Somar_IPI_Custo'],
                "T295_UF_Desembaraco"                        => $rowT293['T293_UF_Desembaraco'],
                "T295_Codigo_Fabricante"                     => $rowT293['T293_Codigo_Fabricante'],
                "T295_Flag_Transporte_DI"                    => $rowT293['T293_Flag_Transporte_DI'],
                "T295_Flag_Tipo_Intermedio_DI"               => $rowT293['T293_Flag_Tipo_Intermedio_DI'],
                "T295_CNPJ_Importador"                       => $rowT293['T293_CNPJ_Importador'],
                "T295_UF_Importador"                         => $rowT293['T293_UF_Importador'],
                "T295_Numero_Drawback_Importacao"            => $rowT293['T293_Numero_Drawback_Importacao'],
                "T295_Cotacao_CC"                            => $rowT293['T293_Cotacao_CC'],
                "T295_Codigo_URF_Entrada"                    => $rowT293['T293_Codigo_URF_Entrada'],
                "T295_Codigo_URF_Despacho"                   => $rowT293['T293_Codigo_URF_Despacho'],
                "T295_Codigo_Moeda"                          => $rowT293['T293_Codigo_Moeda'],
                "T295_Codigo_Incoterms"                      => $rowT293['T293_Codigo_Incoterms'],
                "T295_Condicao_Mercadoria"                   => $rowT293['T293_Condicao_Mercadoria'],
                "T295_Enquadramento_Material_Usado"          => $rowT293['T293_Enquadramento_Material_Usado'],
                "T295_Operacao_Enquadramento_Material_Usado" => $rowT293['T293_Operacao_Enquadramento_Material_Usado'],
                "T295_Data_Inicio_Demurrage"                 => $rowT293['T293_Data_Inicio_Demurrage'],
                "T295_Siscoserv_RAS"                         => $rowT293['T293_Siscoserv_RAS'],
                "T295_Siscoserv_RP"                          => $rowT293['T293_Siscoserv_RP'],
                "T295_Orgao_Desembaraco"                     => $rowT293['T293_Orgao_Desembaraco'],
                "T295_D190_Id"                               => $rowT293['T293_D190_Id'],
                "T295_Important_Remark"                      => $mC004['C004_Observao_Purchase_Order']
            );

            $sqlChaves = '';
            $sqlDados = '';
            foreach ($insertT295 as $key => $value) {
                $sqlChaves .= $sqlChaves ? ',' : '';
                $sqlChaves .= $key;
                $sqlDados .= $sqlDados ? ',' : '';
                $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
            }
            $sql = "INSERT INTO T295 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
            mysql_query($sql);

            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return '1 Erro: ' . $error;
            }

            $T295_Id = $g['mysqlLastId'];

            // insere pacote de despesas
            $T299 = mysql_query("SELECT * 
                                   FROM T299 
                                  WHERE T299_T293_Id = '{$T293_Id}'");
            while($mT299 = mysql_fetch_array($T299)){

                //faz o insert do pacote na PO
                mysql_query("INSERT INTO T300 (T300_T295_Id,
                                               T300_D190_Id,
                                               T300_D191_Id,
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
                                     )VALUES(
                                               '{$T295_Id}',  
                                               '{$mT299['T299_D190_Id']}',
                                               '{$mT299['T299_D191_Id']}',
                                               '{$mT299['T299_Aliquota']}',
                                               '{$mT299['T299_Flag_Tipo']}',
                                               '{$mT299['T299_Valor']}',
                                               '{$mT299['T299_Formula']}',
                                               '{$mT299['T299_Data']}',
                                               '{$mT299['T299_Ordem']}',
                                               '{$mT299['T299_Flag_Soma_Base_IPI']}',
                                               '{$mT299['T299_Flag_Soma_Base_ICMS']}',
                                               '{$mT299['T299_Flag_Soma_Base_PIS']}',
                                               '{$mT299['T299_Flag_Soma_Base_COFINS']}',
                                               '{$mT299['T299_Flag_Soma_Base_II']}',
                                               '{$mT299['T299_Flag_Soma_Custo']}',
                                               '{$mT299['T299_Flag_Soma_Preco_NFe']}',
                                               '{$mT299['T299_Flag_Tipo_Rateio']}',
                                               '{$mT299['T299_Flag_ME']}',
                                               '{$mT299['T299_Cotacao_ME']}')");
                $error = mysql_error();
                if(!empty($error)){
                    mysql_query("ROLLBACK");
                    return 'Houve um erro ao tentar incluir o pacote de depesas <br>'.$error;
                }
            }
            
            $T294 = mysql_query("SELECT * 
                                  FROM T294
                             LEFT JOIN D009 ON D009_Id=T294_D009_Id
                             LEFT JOIN D049 ON D049_Id=D009_D049_Id
                             LEFT JOIN D001 ON D001_Id=D049_D001_Id
                             LEFT JOIN T293 ON T293_Id=T294_T293_Id
                             LEFT JOIN T004 ON T004_Id=T294_T004_Id
                             LEFT JOIN T003 ON T003_Id=T004_T003_Id
                             LEFT JOIN T006 ON T004_Id=T006_T004_Id
                                 WHERE T293_D024_Id = '{$D024_Id}' 
                                   AND T294_Flag_Finalizar_Compra = 'N'
                                   AND T294_Quantidade_PO > 0");
                               
            // vai verifivar se a contation está vinculada a um orçamento e o mesmo ja gerou pedido                   
            $temPedido = true;
            $msg = 'Quotations vinculada a um orçamento, é necessário a geração do pedido para gerar a P.O<br><br>';       

            // Grava todos os T293_Id para atualizar o status     
            $arrayT293 = array();

            while($rowT294 = mysql_fetch_assoc($T294)) {

                if($rowT294['T294_Quantidade_PO'] == 0){
                    continue;
                }    

                $arrayT293[] = $rowT294['T293_Id'];

                if($rowT294['T294_T004_Id'] > 0 && ($rowT294['T006_T004_Id'] <= 0 || empty($rowT294['T006_T004_Id']))){
                    $temPedido = false;
                    $msg .= "<b>Quotation {$rowT294['T293_Id']}</b> - Código {$rowT294['T294_Codigo_Produto']} está vinculado ao orçamento {$rowT294['T003_Id']}<br>";
                    continue;
                }
        
                $insertT296 = array(
                    "T296_T295_Id"                                     => $T295_Id,
                    "T296_T294_Id"                                     => $rowT294['T294_Id'],
                    "T296_T060_Id"                                     => $rowT294['T294_T060_Id'],
                    "T296_T054_Id"                                     => $rowT294['T294_T054_Id'],
                    "T296_D037_Id"                                     => $rowT294['T294_D037_Id'],
                    "T296_D005_Id"                                     => $rowT294['T294_D005_Id'],
                    "T296_D009_Id"                                     => $rowT294['T294_D009_Id'],
                    "T296_T053_Id"                                     => $rowT294['T294_T053_Id'],
                    "T296_C004_Id"                                     => $rowT294['T294_C004_Id'],
                    "T296_T270_Id"                                     => $rowT294['T294_T270_Id'],
                    "T296_T066_Id"                                     => $rowT294['T294_T066_Id'],
                    "T296_D006_Id"                                     => $rowT294['T294_D006_Id'],
                    "T296_Codigo_Produto"                              => $rowT294['T294_Codigo_Produto'],
                    "T296_Codigo_Produto_Fornecedor"                   => $rowT294['T294_Codigo_Produto_Fornecedor'],
                    "T296_Descricao_Produto"                           => $rowT294['T294_Descricao_Produto'],
                    "T296_Classificacao_Fiscal"                        => $rowT294['T294_Classificacao_Fiscal'],
                    "T296_Quantidade"                                  => $rowT294['T294_Quantidade_PO'],
                    "T296_Valor_Custo_Unitario"                        => $rowT294['T294_Valor_Custo_Unitario'],
                    "T296_Peso_Kg_Liquido_Unitario"                    => $rowT294['T294_Peso_Kg_Liquido_Unitario'],
                    "T296_Valor_Adicao"                                => $rowT294['T294_Valor_Adicao'],
                    "T296_Adicao_Item"                                 => $rowT294['T294_Adicao_Item'],
                    "T296_Flag_ME"                                     => $rowT294['T294_Flag_ME'],
                    "T296_Flag_Frete"                                  => $rowT294['T294_Flag_Frete'],
                    "T296_Aliquota_ICMS"                               => $rowT294['T294_Aliquota_ICMS'],
                    "T296_Aliquota_ICMS_Presumido"                     => $rowT294['T294_Aliquota_ICMS_Presumido'],
                    "T296_Percentual_Reducao_ICMS"                     => $rowT294['T294_Percentual_Reducao_ICMS'],
                    "T296_Percentual_Reducao_ICMS_Presumido"           => $rowT294['T294_Percentual_Reducao_ICMS_Presumido'],
                    "T296_Aliquota_ICMS_PIS_COFINS"                    => $rowT294['T294_Aliquota_ICMS_PIS_COFINS'],
                    "T296_Aliquota_ICMS_Credito_Presumido"             => $rowT294['T294_Aliquota_ICMS_Credito_Presumido'],
                    "T296_Aliquota_IPI"                                => $rowT294['T294_Aliquota_IPI'],
                    "T296_Aliquota_Imposto_Importacao"                 => $rowT294['T294_Aliquota_Imposto_Importacao'],
                    "T296_Aliquota_COFINS"                             => $rowT294['T294_Aliquota_COFINS'],
                    "T296_Aliquota_PIS"                                => $rowT294['T294_Aliquota_PIS'],
                    "T296_Flag_Calculo_Manual"                         => $rowT294['T294_Flag_Calculo_Manual'],
                    "T296_Quantidade_Saldo"                            => $rowT294['T294_Quantidade_PO'],
                    "T296_Valor_Base_Imposto_Importacao"               => $rowT294['T294_Valor_Base_Imposto_Importacao'],
                    "T296_Valor_Base_IPI"                              => $rowT294['T294_Valor_Base_IPI'],
                    "T296_Valor_Base_ICMS_Sem_Reducao"                 => $rowT294['T294_Valor_Base_ICMS_Sem_Reducao'],
                    "T296_Valor_Base_ICMS_Normal"                      => $rowT294['T294_Valor_Base_ICMS_Normal'],
                    "T296_Valor_Base_ICMS_Presumido"                   => $rowT294['T294_Valor_Base_ICMS_Presumido'],
                    "T296_Valor_Base_COFINS"                           => $rowT294['T294_Valor_Base_COFINS'],
                    "T296_Valor_Base_PIS"                              => $rowT294['T294_Valor_Base_PIS'],
                    "T296_Quantidade_Container"                        => $rowT294['T294_Quantidade_Container'],
                    //"T296_Prazo_Entrega"                               => $rowT294['T294_Prazo_Entrega'],
                    //"T296_Valor_Custo_Unitario_Anterior"               => $rowT294['T294_Valor_Custo_Unitario_Anterior'],
                    //"T296_Valor_Total_Custo"                           => $rowT294['T294_Valor_Total_Custo'],
                    //"T296_Valor_Preco_Unitario_Nota_Fiscal"            => $rowT294['T294_Valor_Preco_Unitario_Nota_Fiscal'],
                    //"T296_Valor_Total_Preco_Nota_Fiscal"               => $rowT294['T294_Valor_Total_Preco_Nota_Fiscal'],
                    //"T296_Valor_Total_Despesas_Acessorias_Nota_Fiscal" => $rowT294['T294_Valor_Total_Despesas_Acessorias_Nota_Fiscal'],
                    //"T296_Valor_Custo_Unitario_Original"               => $rowT294['T294_Valor_Custo_Unitario_Original'],
                    //"T296_Valor_Total_Custo_Original"                  => $rowT294['T294_Valor_Total_Custo_Original'],
                    //"T296_Valor_Base_Extra"                            => $rowT294['T294_Valor_Base_Extra'],
                    //"T296_Aliquota_Extra"                              => $rowT294['T294_Aliquota_Extra'],
                    //"T296_Valor_ICMS"                                  => $rowT294['T294_Valor_ICMS'],
                    //"T296_Valor_ICMS_Presumido"                        => $rowT294['T294_Valor_ICMS_Presumido'],
                    //"T296_Valor_ICMS_Pagar"                            => $rowT294['T294_Valor_ICMS_Pagar'],
                    //"T296_Valor_IPI"                                   => $rowT294['T294_Valor_IPI'],
                    //"T296_Valor_Imposto_Importacao"                    => $rowT294['T294_Valor_Imposto_Importacao'],
                    //"T296_Valor_COFINS"                                => $rowT294['T294_Valor_COFINS'],
                    //"T296_Valor_PIS"                                   => $rowT294['T294_Valor_PIS'],
                    //"T296_Valor_Extra"                                 => $rowT294['T294_Valor_Extra'],
                    //"T296_Valor_Frete"                                 => $rowT294['T294_Valor_Frete'],
                    //"T296_Valor_Despesas"                              => $rowT294['T294_Valor_Despesas'],
                    //"T296_Valor_Despesas_Fatura"                       => $rowT294['T294_Valor_Despesas_Fatura'],
                    //"T296_Valor_Aduaneira"                             => $rowT294['T294_Valor_Aduaneira'],
                    //"T296_Valor_Despesas_Aduaneira"                    => $rowT294['T294_Valor_Despesas_Aduaneira'],
                    //"T296_Valor_Custo_Final_Unitario"                  => $rowT294['T294_Valor_Custo_Final_Unitario'],
                    //"T296_Valor_Total_Custo_Final"                     => $rowT294['T294_Valor_Total_Custo_Final'],
                    //"T296_Peso_Kg_Liquido_Total"                       => $rowT294['T294_Peso_Kg_Liquido_Total'],
                    //"T296_Peso_Lb_Liquido_Unitario"                    => $rowT294['T294_Peso_Lb_Liquido_Unitario'],
                    //"T296_Peso_Lb_Liquido_Total"                       => $rowT294['T294_Peso_Lb_Liquido_Total'],
                    //"T296_Multiplicador_X"                             => $rowT294['T294_Multiplicador_X'],
                    //"T296_Multiplicador_Y"                             => $rowT294['T294_Multiplicador_Y'],
                    //"T296_Data_Transferencia_Estoque"                  => $rowT294['T294_Data_Transferencia_Estoque'],
                    //"T296_Valor_Total_ICMS"                            => $rowT294['T294_Valor_Total_ICMS'],
                    //"T296_Valor_Total_ICMS_Presumido"                  => $rowT294['T294_Valor_Total_ICMS_Presumido'],
                    //"T296_Valor_Total_ICMS_Pagar"                      => $rowT294['T294_Valor_Total_ICMS_Pagar'],
                    //"T296_Valor_Total_IPI"                             => $rowT294['T294_Valor_Total_IPI'],
                    //"T296_Valor_Total_Imposto_Importacao"              => $rowT294['T294_Valor_Total_Imposto_Importacao'],
                    //"T296_Valor_Total_COFINS"                          => $rowT294['T294_Valor_Total_COFINS'],
                    //"T296_Valor_Total_PIS"                             => $rowT294['T294_Valor_Total_PIS'],
                    //"T296_Valor_Total_Extra"                           => $rowT294['T294_Valor_Total_Extra'],
                    //"T296_Valor_Total_Frete"                           => $rowT294['T294_Valor_Total_Frete'],
                    //"T296_Valor_Total_Despesas"                        => $rowT294['T294_Valor_Total_Despesas'],
                    //"T296_Valor_Total_Despesas_Fatura"                 => $rowT294['T294_Valor_Total_Despesas_Fatura'],
                    //"T296_Valor_Total_Aduaneira"                       => $rowT294['T294_Valor_Total_Aduaneira'],
                    //"T296_Valor_Total_Despesas_Aduaneira"              => $rowT294['T294_Valor_Total_Despesas_Aduaneira'],
                    //"T296_Valor_Total_Base_IPI"                        => $rowT294['T294_Valor_Total_Base_IPI'],
                    //"T296_Valor_Total_Base_ICMS_Sem_Reducao"           => $rowT294['T294_Valor_Total_Base_ICMS_Sem_Reducao'],
                    //"T296_Valor_Total_Base_ICMS_Normal"                => $rowT294['T294_Valor_Total_Base_ICMS_Normal'],
                    //"T296_Valor_Total_Base_ICMS_Presumido"             => $rowT294['T294_Valor_Total_Base_ICMS_Presumido'],
                    //"T296_Valor_Total_Base_COFINS"                     => $rowT294['T294_Valor_Total_Base_COFINS'],
                    //"T296_Valor_Total_Base_PIS"                        => $rowT294['T294_Valor_Total_Base_PIS'],
                    //"T296_Valor_Total_Base_Extra"                      => $rowT294['T294_Valor_Total_Base_Extra'],
                    //"T296_Valor_Total_Base_Imposto_Importacao"         => $rowT294['T294_Valor_Total_Base_Imposto_Importacao'],
                    //"T296_Peso_Kg_Liquido_Unitario_Fatura"             => $rowT294['T294_Peso_Kg_Liquido_Unitario_Fatura'],
                    //"T296_Peso_Kg_Liquido_Total_Fatura"                => $rowT294['T294_Peso_Kg_Liquido_Total_Fatura'],
                    //"T296_Valor_AFRMM"                                 => $rowT294['T294_Valor_AFRMM'],
                    //"T296_Valor_Total_AFRMM"                           => $rowT294['T294_Valor_Total_AFRMM'],
                    //"T296_Valor_Total_Multa"                           => $rowT294['T294_Valor_Total_Multa'],
                    //"T296_Valor_Juros_Multa"                           => $rowT294['T294_Valor_Juros_Multa'],
                    //"T296_Valor_Dumping"                               => $rowT294['T294_Valor_Dumping'],
                    //"T296_Valor_Seguro"                                => $rowT294['T294_Valor_Seguro'],
                    //"T296_Valor_Total_Seguro"                          => $rowT294['T294_Valor_Total_Seguro'],
                    //"T296_Valor_Custo_Debug"                           => $rowT294['T294_Valor_Custo_Debug'],
                    //"T296_Valor_Base_II_Debug"                         => $rowT294['T294_Valor_Base_II_Debug'],
                    //"T296_Cotacao_CC"                                  => $rowT294['T294_Cotacao_CC'],
                    //"T296_Item_Ac_Drawback"                            => $rowT294['T294_Item_Ac_Drawback'],
                    //"T296_Unidade_Medida"                              => $rowT294['T294_Unidade_Medida'], 
                    //"T296_Percentual_Acrescimo_Impostos"               => $rowT294['T294_Percentual_Acrescimo_Impostos'],                    
                );

                $sqlChaves = '';
                $sqlDados = '';
                foreach ($insertT296 as $key => $value) {
                    $sqlChaves .= $sqlChaves ? ',' : '';
                    $sqlChaves .= $key;
                    $sqlDados .= $sqlDados ? ',' : '';
                    $sqlDados .= "'" . mysql_real_escape_string($value) . "'";
                }
                $sql = "INSERT INTO T296 ( {$sqlChaves} ) VALUES ( {$sqlDados} )";
                mysql_query($sql);
                $error = mysql_error();
                
                if (!empty($error)) {
                    mysql_query("ROLLBACK");
                    return '2 Erro: ' . $error;
                }
                
            }

            if($temPedido === false){
                mysql_query("ROLLBACK");
                return $msg;
            }

            $atualizaStatus = $this->alteraStatusQuotation($arrayT293);
            if($atualizaStatus !== true){
                mysql_query("ROLLBACK");
                return $atualizaStatus;
            }

            $retorno = $this->limparQuantidadePO($D024_Id);

            if ($retorno === true) {
                mysql_query("COMMIT");                
                return array('T295_Id' => $T295_Id);
            } else {
                mysql_query("ROLLBACK");
                return $retorno;
            }
        } else {
            return "ERRO: D024_Id não existe";
        }

    }

    public function limparQuantidadePO($D024_Id) {
        $erro=0;
		mysql_query("START_TRANSACTION");
		$T294 = mysql_query("SELECT T294_Id
                               FROM T294
                          LEFT JOIN T293 ON T293_Id=T294_T293_Id
                              WHERE T293_D024_Id = $D024_Id");

		while($mT294 = mysql_fetch_array($T294)){

			$upT294 = mysqli_query("UPDATE T294 SET T294_Quantidade_PO = 0 WHERE T294_Id='{$mT294['T294_Id']}'");
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
     * incluiPacoteDespesa
     *
     * @param int $T293_Id
     * @param int $D190_Id
     * @return bool
     */  
    public function incluirPacoteDespesaQuotation($T293_Id, $D190_Id) {
        global $g;

        //Deleta pacote que já está cadastrado, despesas incluídas manualmente não são deletadas
        mysql_query("DELETE FROM T299 WHERE T299_T293_Id = '{$T293_Id}' AND T299_D190_Id > 0");
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
                mysql_query("INSERT INTO T299 (T299_T293_Id,
                                            T299_D190_Id,
                                            T299_D191_Id,
                                            T299_Aliquota,
                                            T299_Flag_Tipo,
                                            T299_Valor,
                                            T299_Formula,
                                            T299_Ordem,
                                            T299_Flag_Soma_Base_IPI,
                                            T299_Flag_Soma_Base_ICMS,
                                            T299_Flag_Soma_Base_PIS,
                                            T299_Flag_Soma_Base_COFINS,
                                            T299_Flag_Soma_Base_II,
                                            T299_Flag_Soma_Custo,
                                            T299_Flag_Soma_Preco_NFe,
                                            T299_Flag_Tipo_Rateio,
                                            T299_Flag_ME
                                    )VALUES(
                                            '{$T293_Id}',  
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
     * @param String $T299_Id
     * @return Bool
     */
    public function processarOrdemDespesa($T299_Id, $novoNumeroItem)
    {
        global $g;
        $contadorItem = 1;
        $sql = mysql_query("SELECT T299_Ordem, 
                                   T299_Id 
                              FROM T299 
                             WHERE T299_T293_Id = (SELECT T299_T293_Id FROM T299 WHERE T299_Id = '{$T299_Id}') 
                               AND T299_Id != '{$T299_Id}' 
                          ORDER BY T299_Ordem");
        while($row = mysql_fetch_array($sql)){
            if($contadorItem == $novoNumeroItem){ $contadorItem++; }
            mysqli_query("UPDATE T299 SET T299_Ordem = '{$contadorItem}' WHERE T299_Id = '{$row['T299_Id']}'");
            $contadorItem++;
        }
        return true;
    }

    /**
     * calcularDepesas
     *
     * @param String $T293_Id
     * @return Bool
     */
    public function calcularDespesas($T293_Id)
    {
        global $g;
        
        $T293 = mysql_query("SELECT COUNT(DISTINCT T294_Valor_Adicao) AS totalAdicao,
                                    SUM(T294_Valor_Custo_Unitario * T294_Quantidade) AS T294_Valor_Custo_Unitario
                               FROM T293    
                          LEFT JOIN T294 ON T294_T293_Id=T293_Id
                              WHERE T293_Id='{$T293_Id}'");
        $mT293 = mysql_fetch_array($T293);

        $totalAdicoes = $mT293['totalAdicao'];

        $T299 = mysql_query("SELECT T299_Id,
                                    T299_Aliquota,
                                    T299_Flag_Tipo,
                                    T299_Valor,
                                    T299_Formula,
                                    T299_Flag_Tipo,
                                    T299_Cotacao_ME,
                                    D191_Descricao_Despesa,
                                    T293_Valor_VMLE,
                                    T299_Flag_ME,
                                    T293_Cotacao_ME
                               FROM T299
                          LEFT JOIN D191 ON D191_Id = T299_D191_Id 
                          LEFT JOIN T293 ON T293_Id = T299_T293_Id
                              WHERE T299_T293_Id = '{$T293_Id}'
                           ORDER BY T299_Ordem ASC");

        $variavel   = array();
        $valorME    = array();
        $valorReais = array();

        while($mT299 = mysql_fetch_array($T299)){
            //monta o nome da variavel em tempo real, essa variavel poderá ser utilizada em qual formula
            $nomeVariavel    = strToUpper(str_replace(" ","_", $mT299['D191_Descricao_Despesa'])); 
            $valorCustoTotal = $mT293['T294_Valor_Custo_Unitario'] * $mT299['T293_Cotacao_ME'];
        
            if($mT299['T299_Flag_Tipo'] == 'F'){
                // verifica se é a despesa é do tipo formula
                $mT299['T299_Formula'] = str_replace('<?php','', $mT299['T299_Formula']);
                $mT299['T299_Formula'] = str_replace("ALIQUOTA",$mT299['T299_Aliquota'], $mT299['T299_Formula']);
                $mT299['T299_Formula'] = str_replace("COTACAO_ME",$mT299['T299_Cotacao_ME'], $mT299['T299_Formula']);
                $mT299['T299_Formula'] = str_replace("VALOR_VMLE_REAL", $valorCustoTotal, $mT299['T299_Formula']);

                // procura se existe uma variavel montada em tempo real e pega o valor
                if ($mT299['T299_Flag_ME'] == 'S'){
                    $mT299['T299_Formula'] = str_replace($variavel, $valorME, $mT299['T299_Formula']);
                } else {
                    $mT299['T299_Formula'] = str_replace($variavel, $valorReais, $mT299['T299_Formula']);
                }
                
                $variavel[] = $nomeVariavel; 
                $resultado  = eval($mT299['T299_Formula']);
                if ($mT299['T299_Flag_ME']=='S'){
                    $valorReais[] = $resultado * $mT299['T299_Cotacao_ME'];
                    $valorME[]    = $resultado;
                } else  {
                    $valorReais[] = $resultado;
                    $valorME[]    = $resultado;
                }

                mysql_query("update T299 set T299_Valor = '{$resultado}' where T299_Id='{$mT299['T299_Id']}'");

            }else{             

                $variavel[] = $nomeVariavel;
                if ($mT299['T299_Flag_ME']=='S'){
                    $valorReais[] = $mT299['T299_Valor'] * $mT299['T299_Cotacao_ME'];
                    $valorME[]    = $mT299['T299_Valor'];
                }else{
                    $valorReais[] = $mT299['T299_Valor'];
                    $valorME[]    = $mT299['T299_Valor'];
                }
            }
        }
 
        return true;
    }

    /**
     * calculaCustoProduto
     *
     * @param String $T293_Id
     * @return Bool
     */
    public function calculaCustoProduto($T293_Id)
    {
        global $g;
        
        $total = mysql_query("SELECT SUM(T294_Valor_Custo_Unitario * T294_Quantidade) AS Valor_Total_Custo,
                                     SUM(T294_Peso_Kg_Liquido_Unitario * T294_Quantidade) AS Total_Peso
                                FROM T294
                               WHERE T294_T293_Id = '{$T293_Id}'");
        $mTotal = mysql_fetch_array($total);

        $T294 = mysql_query("SELECT T294_Id,
                                    (T294_Valor_Custo_Unitario * T294_Quantidade) AS Valor_Custo_Produto, 
                                    T294_Quantidade, 
                                    (T294_Peso_Kg_Liquido_Unitario * T294_Quantidade) AS Peso_Kg_Liquido_Unitario_Total,
                                    T293_Cotacao_ME 
                               FROM T294
                          LEFT JOIN T293 ON T294_T293_Id = T293_Id  
                              WHERE T294_T293_Id = '{$T293_Id}'");

        while($mT294 = mysql_fetch_array($T294)){

            $custoProduto    = $mT294['Valor_Custo_Produto'] * $mT294['T293_Cotacao_ME'];
            $valorTotalCusto = $mTotal['Valor_Total_Custo']  * $mT294['T293_Cotacao_ME'];
          
            $T299 = mysql_query("SELECT *
                                   FROM T299 
                                  WHERE T299_T293_Id = '{$T293_Id}'
                               ORDER BY T299_Ordem");

            $somaBaseIPI        = $custoProduto;
            $somaBaseICMS       = $custoProduto;
            $somaBasePIS        = $custoProduto;
            $somaBaseCOFINS     = $custoProduto;
            $somaBaseII         = $custoProduto;
            $somaCusto          = $custoProduto;
            $somaPrecoNFE       = $custoProduto;
            $valorTotalDespesas = 0;

            while($mT299 = mysql_fetch_array($T299)){

                if ($mT299['T299_Flag_ME'] == 'S') {
                    $mT299['T299_Valor'] = $mT299['T299_Valor'] * $mT299['T299_Cotacao_ME'];
                }
                
                if($mT299['T299_Flag_Tipo_Rateio'] == 'C'){
                    $mT299['T299_Valor'] = ($mT299['T299_Valor'] * ($custoProduto/$valorTotalCusto));
                }else{
                    $mT299['T299_Valor'] = ($mT299['T299_Valor'] * ($mT294['Peso_Kg_Liquido_Unitario_Total']/$mTotal['Total_Peso']));
                }

                if($mT299['T299_Flag_Soma_Base_IPI'] == 'S' || $mT299['T299_Flag_Soma_Base_ICMS'] == 'S' || $mT299['T299_Flag_Soma_Base_PIS'] == 'S' || $mT299['T299_Flag_Soma_Base_COFINS'] == 'S' || $mT299['T299_Flag_Soma_Base_II'] == 'S' || $mT299['T299_Flag_Soma_Custo'] == 'S' || $mT299['T299_Flag_Soma_Preco_NFe'] == 'S'){
                    $valorTotalDespesas += $mT299['T299_Valor'];              
                }

                if($mT299['T299_Flag_Soma_Base_IPI'] == 'S'){
                    $somaBaseIPI += $mT299['T299_Valor'];
                }

                if($mT299['T299_Flag_Soma_Base_ICMS'] == 'S'){
                    $somaBaseICMS += $mT299['T299_Valor'];
                }

                if($mT299['T299_Flag_Soma_Base_PIS'] == 'S'){
                    $somaBasePIS += $mT299['T299_Valor'];
                }

                if($mT299['T299_Flag_Soma_Base_COFINS'] == 'S'){
                    $somaBaseCOFINS += $mT299['T299_Valor'];
                }

                if($mT299['T299_Flag_Soma_Base_II'] == 'S'){
                    $somaBaseII += $mT299['T299_Valor'];
                }

                if($mT299['T299_Flag_Soma_Custo'] == 'S'){
                    $somaCusto += $mT299['T299_Valor'];

                }

                if($mT299['T299_Flag_Soma_Preco_NFe'] == 'S'){
                    $somaPrecoNFE += $mT299['T299_Valor'];
                }
            }

            mysqli_query("UPDATE T294 SET T294_Valor_Base_IPI                = '{$somaBaseIPI}',
                                          T294_Valor_Base_ICMS_Sem_Reducao   = '{$somaBaseICMS}',
                                          T294_Valor_Base_PIS                = '{$somaBasePIS}',
                                          T294_Valor_Base_COFINS             = '{$somaBaseCOFINS}',
                                          T294_Valor_Base_Imposto_Importacao = '{$somaBaseII}',
                                          T294_Valor_Total_Custo_Final       = '{$somaCusto}',
                                          T294_Valor_Total_Preco_Nota_Fiscal = '{$somaPrecoNFE}',
                                          T294_Valor_Total_Despesas          = '{$valorTotalDespesas}'
                                   WHERE T294_Id = '{$mT294['T294_Id']}'");
        } 

        return true;
    }

    /**
     * duplicar_IMP004
     * Chamada DUPLICAR do menu automatico da janela
     *
     * @param String 
     * @return String
     */    
	public Function duplicar_imp004FormProdutoIncluir($divId, $divIdRoot, $tabela, $T293_Id_Antigo)
    {
        global $g;

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        // Selecionando o orçamento
        $T293  = mysql_query("SELECT * 
                                FROM T293
                               WHERE T293_Id='{$T293_Id_Antigo}'");
        $mT293 = mysql_fetch_assoc($T293);
        
        if (mysql_num_rows($T293) == 0) {
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
        foreach ($mT293 as $key => $value) {
            if ($key == 'T293_Id') {
                continue;
            } elseif ($key == 'T293_D190_Id'){
                continue;
            } elseif ($key == 'T293_Flag_Transporte_DI'){
                continue;
            }elseif ($key == 'T293_Data_Emissao') {
                $campos.=$key.", ";
                $valores.= "curdate(),";
            }  else {
                $campos .= $key . ", ";
                $valores .= "'" . mysql_real_escape_string($value) . "',";
            }
        }

        $campos  = trim($campos, ', ');
        $valores = trim($valores, ',');
        $insT293 = "INSERT INTO T293 ({$campos}) VALUES ({$valores})";
        $resT293 = mysql_query($insT293);
        $error = mysql_error();
        if (!empty($error)) {
            mysql_query("ROLLBACK");
            return "Erro 1: " . $error;
        }

        $T293_Id_Novo = $g['mysqlLastId'];
        
        // Selecionando todos os itens da Quotation
        $T294 = mysql_query("SELECT * FROM T294 WHERE T294_T293_Id='{$T293_Id_Antigo}'");
        
        while ($mT294 = mysql_fetch_assoc($T294)) {
            $campos  = "";
            $valores = "";
            foreach ($mT294 as $key => $value) {
                if ($key == 'T294_Id') {
                    continue;
                } elseif ($key == "T294_T293_Id") {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($T293_Id_Novo) . "',";
                } else {
                    $campos .= $key . ", ";
                    $valores .= "'" . mysql_real_escape_string($value) . "',";
                }
            }
            $campos  = trim($campos, ', ');
            $valores = trim($valores, ',');
            $insT294 = "INSERT INTO T294 ({$campos}) VALUES ({$valores})";
            $resT294 = mysql_query($insT294);
            $error = mysql_error();
            if (!empty($error)) {
                mysql_query("ROLLBACK");
                return "Erro 2: " . $error;
            }
        }
        mysql_query("COMMIT");

        $retorno = "dialogConfirm('Duplicar', 'Quotation duplicada com sucesso');";
        return $retorno;
    }

    /**
     * alteraStatusQuotation
     * Verifica o status da Quotation ao gerar uma PO
     *
     * @param array T293_Id
    */
    public function alteraStatusQuotation($T293){
        global $g;
       
        foreach(array_unique($T293) as $T293_Id){

            $T293 = mysql_query("SELECT T293_Id,
                                        (SUM(T294_Quantidade_Saldo)/SUM(T294_Quantidade))*100 as percentual
                                   FROM T293 
                              LEFT JOIN T294 ON T293_Id=T294_T293_Id AND T294_Flag_Finalizar_Compra !='S'
                                  WHERE T293_Id ='{$T293_Id}'");

            $mT293 = mysql_fetch_array($T293);
            
            if($mT293['percentual'] > 0){
                mysql_query("UPDATE T293 SET T293_Flag_Status = '4', 
                                             T293_Nome_Status = T293_Status_Pedido(T293_Flag_Status, T293_Id) 
                                       WHERE T293_Id = '{$mT293['T293_Id']}'");

                $erro = mysql_error();
                if (!empty($erro)) {
                    return "Erro ao gerar PO<br><br>{$erro}";
                }
            } else {
                mysql_query("UPDATE T293 SET T293_Flag_Status = '5', 
                                             T293_Nome_Status = T293_Status_Pedido(T293_Flag_Status, T293_Id) 
                                       WHERE T293_Id = '{$mT293['T293_Id']}'");

                $erro = mysql_error();
                if (!empty($erro)) {
                    return "Erro ao gerar PO<br><br>{$erro}";
                }
            }        
        }

        return true;
    } 
}

?>
