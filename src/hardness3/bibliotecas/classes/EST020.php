<?php

namespace hardness {

    use InvalidArgumentException;

    set_time_limit(0);
    require_once("old/lib/funcoes.php");
    require_once("hardness/class/cte/cte3.class.php");
    require_once("hardness/class/cte/certificate.php");    
    require_once("hardness/class/cte/builder.php");    
    require_once("hardness/class/cte/logCTE.php");
    require_once("bibliotecas/classes/ReformaTributaria.php");    

    definirClasse('EST020');
    class EST020_
    {
        /**
         * incluirCTe         
         * @param Form $form
         * @param String $T084_Id
         * @return String/bool
         */
        public function incluirCTe($form, $T084_Id)
        {

            global $g;
            $Operacao                                = (empty($T084_Id)) ? 'I' : 'A';
            $D006_Id                                = $form->campoValorEnviado('T084_D006_Id');            
            $T084_Data_Emissao                      = troca_data_amd($form->campoValorEnviado('T084_Data_Emissao'));
            $T084_Hora_Emissao                      = $form->campoValorEnviado('T084_Hora_Emissao');
            $T084_Flag_Modalidade_Transporte        = $form->campoValorEnviado('T084_Flag_Modalidade_Transporte');
            $T084_Tipo_Conhecimento                 = $form->campoValorEnviado('T084_Tipo_Conhecimento');
            $T084_Flag_Tipo_Servico                 = $form->campoValorEnviado('T084_Flag_Tipo_Servico');
            $T084_Flag_Retira                       = $form->campoValorEnviado('T084_Flag_Retira');
            $T084_Informacoes_Adicionais_Retira     = $form->campoValorEnviado('T084_Informacoes_Adicionais_Retira');
            $T084_D018_Id_Origem                    = $form->campoValorEnviado('T084_D018_Id_Origem');
            $T084_D020_Id_Origem                    = $form->campoValorEnviado('T084_D020_Id_Origem');
            $T084_D018_Id_Destino                   = $form->campoValorEnviado('T084_D018_Id_Destino');
            $T084_D020_Id_Destino                   = $form->campoValorEnviado('T084_D020_Id_Destino');
            $T084_D024_Tomador_Id                   = $form->campoValorEnviado('T084_D024_Tomador_Id');
            $T084_D024_Remetente_Id                 = $form->campoValorEnviado('T084_D024_Remetente_Id');
            $T084_D024_Destinatario_Id              = $form->campoValorEnviado('T084_D024_Destinatario_Id');
            $T084A_D024_Recebedor_Id              = $form->campoValorEnviado('T084A_D024_Recebedor_Id');
            $T084_Flag_Indicador_IE                 = $form->campoValorEnviado('T084_Flag_Indicador_IE');
            $T084_D006_Id                           = $form->campoValorEnviado('T084_D006_Id');
            // $T084_C004_Id                           = $form->campoValorEnviado('T084_C004_Id');
            $T084_C007_Id                           = $form->campoValorEnviado('T084_C007_Id');
            // $T084_Flag_Entrada_Saida                = $form->campoValorEnviado('T084_Flag_Entrada_Saida');


            $D024Tomador = mysql_query("SELECT * FROM D024
                                                LEFT JOIN D030 ON D030_Id=D024_D030_Id
                                                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                                                LEFT JOIN D018 ON D018_Id=D020_D018_Id
                                            WHERE D024_Id = '{$T084_D024_Tomador_Id}'");
            $mD024Tomador = mysql_fetch_array($D024Tomador);  
            
            if($mD024Tomador['D024_Flag_Contribuinte'] != '0' && empty($mD024Tomador['D024_Inscricao_Estadual']))  {
                $mD024Tomador['D024_Inscricao_Estadual']= 'ISENTO';
            }
            
            $D024Remetente = mysql_query("SELECT * FROM D024
                                                LEFT JOIN D030 ON D030_Id=D024_D030_Id
                                                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                                                LEFT JOIN D018 ON D018_Id=D020_D018_Id
                                            WHERE D024_Id = '{$T084_D024_Remetente_Id}'");
            $mD024Remetente = mysql_fetch_array($D024Remetente);

            if($mD024Remetente['D024_Flag_Contribuinte'] != '0' && empty($mD024Remetente['D024_Inscricao_Estadual']))  {
                $mD024Remetente['D024_Inscricao_Estadual']= 'ISENTO';
            }
            
            $D024Destinatario = mysql_query("SELECT * FROM D024
                                                LEFT JOIN D030 ON D030_Id=D024_D030_Id
                                                LEFT JOIN D020 ON D020_Id=D024_D020_Id
                                                LEFT JOIN D018 ON D018_Id=D020_D018_Id
                                            WHERE D024_Id = '{$T084_D024_Destinatario_Id}'");
            $mD024Destinatario = mysql_fetch_array($D024Destinatario);

            if($mD024Destinatario['D024_Flag_Contribuinte'] != '0' && empty($mD024Destinatario['D024_Inscricao_Estadual']))  {
                $mD024Destinatario['D024_Inscricao_Estadual'] = 'ISENTO';
            }
            
            if ($T084A_D024_Recebedor_Id > 0) {
                $D148 = mysql_query("SELECT
                                        D148_Cnpj,
                                        D148_Cpf,
                                        D148_Inscricao_Estadual,
                                        D148_Nome,
                                        D148_Rua,
                                        D148_Numero,
                                        D148_Bairro,
                                        D024_Flag_Contribuinte,
                                        D024_Cep,
                                        D020_Codigo_IBGE,
                                        D020_Nome_Cidade,
                                        D018_UF
                                    FROM D148
                                        LEFT JOIN D020 ON D020_Id=D148_D020_Id
                                        LEFT JOIN D018 ON D018_Id=D148_D018_Id
                                        LEFT JOIN D024 ON D024_Id = D148_D024_Id
                                    WHERE
                                        D148_Id ='{$T084A_D024_Recebedor_Id}'");
                $mD148 = mysql_fetch_array($D148);
            }

            if($mD148['D024_Flag_Contribuinte'] != '0' && empty($mD148['D148_Inscricao_Estadual']))  {
                $mD148['D148_Inscricao_Estadual'] = 'ISENTO';
            }
            
            // Limpa campos
            $mD024Tomador['D024_Cnpj'] = $this->removeNaoDigitos($mD024Tomador['D024_Cnpj']);
            $mD024Tomador['D024_Cpf']  = $this->removeNaoDigitos($mD024Tomador['D024_Cpf']);
            $mD024Remetente['D024_Cnpj'] = $this->removeNaoDigitos($mD024Remetente['D024_Cnpj']);
            $mD024Remetente['D024_Cpf']  = $this->removeNaoDigitos($mD024Remetente['D024_Cpf']);                
            $mD024Destinatario['D024_Cnpj'] = $this->removeNaoDigitos($mD024Destinatario['D024_Cnpj']);
            $mD024Destinatario['D024_Cpf']  = $this->removeNaoDigitos($mD024Destinatario['D024_Cpf']);
            $mD148['D148_Cnpj'] = $this->removeNaoDigitos($mD148['D148_Cnpj']);
            $mD148['D148_Cpf']  = $this->removeNaoDigitos($mD148['D148_Cpf']);

            $D006 = mysql_query("SELECT *, D059_Codigo_ST FROM D006
                                    LEFT JOIN D059 ON D059_Id = D006_D059_Id 
                                    WHERE D006_Id = {$D006_Id}");
            $mD006 = mysql_fetch_array($D006);

            $C004 = mysql_query("SELECT * FROM C004                                     
                                    LEFT JOIN D020 ON D020_Id=C004_D020_Id
                                    LEFT JOIN D018 ON D018_Id=D020_D018_Id
                                    WHERE C004_Id = '{$g['empresaAtual']}'");
            $mC004 = mysql_fetch_array($C004);
            
            //Limpa dados
            $mD006['D006_Codigo_CFOP'] = $this->removeNaoDigitos($mD006['D006_Codigo_CFOP']);
            $mC004['C004_Cnpj'] = $this->removeNaoDigitos($mC004['C004_Cnpj']);

                    
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
                        
            if ($Operacao == 'I') {
                    
                #region                
                $cClausula = " INSERT INTO T084 ( ";
                $cClausula .= " T084_C004_Id, ";
                $cClausula .= " T084_C007_Id, ";
                // $cClausula .= " T084_D022_Id, ";
                // $cClausula .= " T084_D024_Id, ";
                $cClausula .= " T084_D006_Id, ";
                // $cClausula .= " T084_Cnpj_Transportadora, ";
                // $cClausula .= " T084_Numero_Conhecimento, ";
                // $cClausula .= " T084_Valor_Conhecimento, ";
                $cClausula .= " T084_Data_Emissao, ";
                $cClausula .= " T084_Hora_Emissao, ";
                // $cClausula .= " T084_Data_Vencimento, ";
                // $cClausula .= " T084_Flag_Fechado, ";
                // $cClausula .= " T084_Aliquota_ICMS, ";
                // $cClausula .= " T084_Valor_Base_ICMS, ";
                // $cClausula .= " T084_Valor_ICMS, ";
                // $cClausula .= " T084_Data_Lancamento, ";
                $cClausula .= " T084_Flag_Cancelado, ";
                // $cClausula .= " T084_D073_Id, ";
                // $cClausula .= " T084_Valor_ICMS_ST, ";
                // $cClausula .= " T084_Tipo_Nota, ";
                // $cClausula .= " T084_Chave_Acesso_Cte, ";
                // $cClausula .= " T084_Numero_Protocolo_Cte, ";
                // $cClausula .= " T084_Valor_Base_PIS, ";
                // $cClausula .= " T084_Valor_Base_COFINS, ";
                // $cClausula .= " T084_Aliquota_PIS, ";
                // $cClausula .= " T084_Aliquota_COFINS, ";
                // $cClausula .= " T084_Valor_PIS, ";
                // $cClausula .= " T084_Valor_COFINS, ";
                // $cClausula .= " T084_Situacao, ";
                // $cClausula .= " T084_Indicador_Tipo_Frete, ";
                $cClausula .= " T084_Modelo, ";
                // $cClausula .= " T084_Serie, ";
                // $cClausula .= " T084_Subserie, ";
                $cClausula .= " T084_Tipo_Conhecimento, ";
                // $cClausula .= " T084_Indicador_Natureza_Frete, ";
                // $cClausula .= " T084_Base_Calculo_Credito, ";
                // $cClausula .= " T084_Numero_Protocolo_Cancelamento_Cte, ";
                $cClausula .= " T084_Flag_Modalidade_Transporte, ";
                $cClausula .= " T084_Flag_Tipo_Servico, ";
                $cClausula .= " T084_Flag_Retira, ";
                $cClausula .= " T084_Informacoes_Adicionais_Retira, ";
                $cClausula .= " T084_Natureza_Operacao, ";
                $cClausula .= " T084_Flag_Indicador_IE, ";
                // $cClausula .= " T084_Observacoes_Gerais, ";
                $cClausula .= " T084_D018_Id_Origem, ";
                $cClausula .= " T084_D020_Id_Origem, ";
                $cClausula .= " T084_D018_Id_Destino, ";
                $cClausula .= " T084_D020_Id_Destino, ";
                // $cClausula .= " T084_Valor_Total_Carga, ";
                // $cClausula .= " T084_Produto_Predominante, ";
                // $cClausula .= " T084_Produto_Predominante_Outras_Caracteristicas, ";
                // $cClausula .= " T084_Numero_Recibo_Cte, ";
                $cClausula .= " T084_Codigo_CFOP, ";
                $cClausula .= " T084_Descricao_CFOP, ";
                $cClausula .= " T084_Flag_Travar, ";
                // $cClausula .= " T084_Hora_Emissao, ";
                // $cClausula .= " T084_Observacao_Cancelamento, ";
                $cClausula .= " T084_Versao_Cte, ";
                $cClausula .= " T084_Tomador_CNPJ, ";
                $cClausula .= " T084_Tomador_CPF, ";
                $cClausula .= " T084_Tomador_IE, ";
                $cClausula .= " T084_Tomador_Nome, ";
                $cClausula .= " T084_Tomador_Nome_Fantasia, ";
                $cClausula .= " T084_Tomador_Rua, ";
                $cClausula .= " T084_Tomador_Numero, ";
                $cClausula .= " T084_Tomador_Bairro, ";
                $cClausula .= " T084_Tomador_Codigo_Cidade_IBGE, ";
                $cClausula .= " T084_Tomador_Cidade, ";
                $cClausula .= " T084_Tomador_UF, ";
                $cClausula .= " T084_Emitente_CNPJ, ";
                $cClausula .= " T084_Emitente_IE, ";
                $cClausula .= " T084_Emitente_Nome, ";
                $cClausula .= " T084_Emitente_Rua, ";
                $cClausula .= " T084_Emitente_Numero, ";
                $cClausula .= " T084_Emitente_Bairro, ";
                $cClausula .= " T084_Emitente_Codigo_Cidade_IBGE, ";
                $cClausula .= " T084_Emitente_Cidade, ";
                $cClausula .= " T084_Emitente_CEP, ";
                $cClausula .= " T084_Emitente_UF, ";
                $cClausula .= " T084_Emitente_UF_IBGE, ";
                $cClausula .= " T084_Emitente_Telefone, ";                
                $cClausula .= " T084_Remetente_CNPJ, ";
                $cClausula .= " T084_Remetente_CPF, ";
                $cClausula .= " T084_Remetente_IE, ";
                $cClausula .= " T084_Remetente_Nome, ";
                $cClausula .= " T084_Remetente_Telefone, ";
                $cClausula .= " T084_Remetente_Rua, ";
                $cClausula .= " T084_Remetente_Numero, ";
                $cClausula .= " T084_Remetente_Bairro, ";
                $cClausula .= " T084_Remetente_Codigo_Cidade_IBGE, ";
                $cClausula .= " T084_Remetente_Cidade, ";
                $cClausula .= " T084_Remetente_CEP, ";
                $cClausula .= " T084_Remetente_UF, ";
                $cClausula .= " T084_Remetente_Codigo_Pais, ";
                $cClausula .= " T084_Remetente_Nome_Pais, ";
                // $cClausula .= " T084_Expedidor_CNPJ, ";
                // $cClausula .= " T084_Expedidor_CPF, ";
                // $cClausula .= " T084_Expedidor_IE, ";
                // $cClausula .= " T084_Expedidor_Nome, ";
                // $cClausula .= " T084_Expedidor_Rua, ";
                // $cClausula .= " T084_Expedidor_Numero, ";
                // $cClausula .= " T084_Expedidor_Bairro, ";
                // $cClausula .= " T084_Expedidor_Codigo_Cidade_IBGE, ";
                // $cClausula .= " T084_Expedidor_Cidade, ";
                // $cClausula .= " T084_Expedidor_CEP, ";
                // $cClausula .= " T084_Expedidor_UF, ";
                $cClausula .= " T084_Recebedor_CNPJ, ";
                $cClausula .= " T084_Recebedor_CPF, ";
                $cClausula .= " T084_Recebedor_IE, ";
                $cClausula .= " T084_Recebedor_Nome, ";
                $cClausula .= " T084_Recebedor_Rua, ";
                $cClausula .= " T084_Recebedor_Numero, ";
                $cClausula .= " T084_Recebedor_Bairro, ";
                $cClausula .= " T084_Recebedor_Codigo_Cidade_IBGE, ";
                $cClausula .= " T084_Recebedor_Cidade, ";
                $cClausula .= " T084_Recebedor_CEP, ";
                $cClausula .= " T084_Recebedor_UF, ";
                $cClausula .= " T084_Destinatario_CNPJ, ";
                $cClausula .= " T084_Destinatario_CPF, ";
                $cClausula .= " T084_Destinatario_IE, ";
                $cClausula .= " T084_Destinatario_Nome, ";
                $cClausula .= " T084_Destinatario_Telefone, ";
                $cClausula .= " T084_Destinatario_Rua, ";
                $cClausula .= " T084_Destinatario_Numero, ";
                $cClausula .= " T084_Destinatario_Bairro, ";
                $cClausula .= " T084_Destinatario_Codigo_Cidade_IBGE, ";
                $cClausula .= " T084_Destinatario_Cidade, ";
                $cClausula .= " T084_Destinatario_CEP, ";
                $cClausula .= " T084_Destinatario_UF, ";
                $cClausula .= " T084_Destinatario_Codigo_Pais, ";
                $cClausula .= " T084_Destinatario_Nome_Pais, ";
                $cClausula .= " T084_Destinatario_Email, ";
                $cClausula .= " T084_Flag_Situacao_Tributaria, ";
                // $cClausula .= " T084_Percentual_Reducao_ICMS, ";
                // $cClausula .= " T084_Valor_Base_ICMS_Substituicao_Retido, ";
                // $cClausula .= " T084_Valor_ICMS_Substituicao_Retido, ";
                // $cClausula .= " T084_Valor_Averbacao, ";
                $cClausula .= " T084_Flag_Entrada_Saida, ";                
                $cClausula .= " T084_D024_Tomador_Id, ";
                $cClausula .= " T084_D024_Remetente_Id, ";
                $cClausula .= " T084_D024_Destinatario_Id, ";
                
                //CAMPOS PAGAMENTO
                // $cClausula .= " T084_Pgto_Primeiro_Vcto_Tipo, ";
                // $cClausula .= " T084_Pgto_Primeiro_Vcto_Valor, ";
                // $cClausula .= " T084_Pgto_Dias_Vcto_Tipo, ";
                // $cClausula .= " T084_Pgto_Dias_Vcto_Valor, ";
                // $cClausula .= " T084_D027_Id, ";
                // $cClausula .= " T084_Pgto_Parcelas, ";

                // $cClausula .= " T084_Valor_Receber, ";
                // $cClausula .= " T084_Pgto_C007_Id, ";
                // $cClausula .= " T084_Pgto_Portador, ";
                // $cClausula .= " T084_Pgto_Data, ";
                $cClausula .= " T084_Fuso_Horario_Emissao, ";                
                $cClausula .= " T084_Ambiente_CTe ";                
                $cClausula .= " ) VALUES (";
                $cClausula .= " '{$g['empresaAtual']}', ";
                $cClausula .= " '{$T084_C007_Id}', ";
                $cClausula .= " '{$T084_D006_Id}', ";
                // $cClausula .= " '{$proximoNumero}', ";
                $cClausula .= " '{$T084_Data_Emissao}', ";
                $cClausula .= " '{$T084_Hora_Emissao}',";
                $cClausula .= " 'N', ";
                $cClausula .= " '57', "; //fixo 57 CT-e
                // $cClausula .= " '{$ultimaSerie}', ";
                $cClausula .= " '{$T084_Tipo_Conhecimento}', ";
                $cClausula .= " '{$T084_Flag_Modalidade_Transporte}', ";
                $cClausula .= " '{$T084_Flag_Tipo_Servico}', ";
                $cClausula .= " '{$T084_Flag_Retira}', ";
                $cClausula .= " '{$T084_Informacoes_Adicionais_Retira}', ";
                $cClausula .= " '{$mD006['D006_Descricao']}', ";
                $cClausula .= " '{$T084_Flag_Indicador_IE}', ";
                $cClausula .= " '{$T084_D018_Id_Origem}', ";
                $cClausula .= " '{$T084_D020_Id_Origem}', ";                
                $cClausula .= " '{$T084_D018_Id_Destino}', ";                
                $cClausula .= " '{$T084_D020_Id_Destino}', ";                
                $cClausula .= " '{$mD006['D006_Codigo_CFOP']}', ";                
                $cClausula .= " '{$mD006['D006_Descricao']}', ";                
                $cClausula .= " 'N', ";                
                $cClausula .= " '{$mC004['C004_Versao_CTe']}', ";                
                $cClausula .= " '{$mD024Tomador['D024_Cnpj']}', ";                
                $cClausula .= " '{$mD024Tomador['D024_Cpf']}', ";                
                $cClausula .= " '{$mD024Tomador['D024_Inscricao_Estadual']}', ";                
                $cClausula .= " '{$mD024Tomador['D024_Nome_Empresa']}', ";                
                $cClausula .= " '{$mD024Tomador['D024_Nome_Fantasia']}', ";                
                $cClausula .= " '{$mD024Tomador['D024_Endereco']}', ";                
                $cClausula .= " '{$mD024Tomador['D024_Numero']}', ";                
                $cClausula .= " '{$mD024Tomador['D024_Bairro']}', ";                
                $cClausula .= " '{$mD024Tomador['D020_Codigo_IBGE']}', ";                
                $cClausula .= " '{$mD024Tomador['D020_Nome_Cidade']}', ";                
                $cClausula .= " '{$mD024Tomador['D018_UF']}', ";                                
                $cClausula .= " '{$mC004['C004_Cnpj']}', ";
                $cClausula .= " '{$mC004['C004_Inscricao_Estadual']}', ";
                $cClausula .= " '{$mC004['C004_Nome_Empresa']}', ";
                $cClausula .= " '{$mC004['C004_Logradouro']}', ";
                $cClausula .= " '{$mC004['C004_Numero']}', ";
                $cClausula .= " '{$mC004['C004_Bairro']}', ";
                $cClausula .= " '{$mC004['D020_Codigo_IBGE']}', ";
                $cClausula .= " '{$mC004['D020_Nome_Cidade']}', ";
                $cClausula .= " '{$mC004['C004_Cep']}', ";                
                $cClausula .= " '{$mC004['D018_UF']}', ";                
                $cClausula .= " '{$mC004['D018_Codigo_IBGE']}', ";                
                $cClausula .= " '{$mC004['C004_DDD_Telefone']}{$mC004['C004_Telefone']}', ";                
                $cClausula .= " '{$mD024Remetente['D024_Cnpj']}', ";                
                $cClausula .= " '{$mD024Remetente['D024_Cpf']}', ";                
                $cClausula .= " '{$mD024Remetente['D024_Inscricao_Estadual']}', ";                
                $cClausula .= " '{$mD024Remetente['D024_Nome_Empresa']}', ";
                $cClausula .= " '{$mD024Remetente['D024_DDD_Telefone_1']}{$mD024Remetente['D024_Telefone_1']}', ";                                          
                $cClausula .= " '{$mD024Remetente['D024_Endereco']}', ";                
                $cClausula .= " '{$mD024Remetente['D024_Numero']}', ";                
                $cClausula .= " '{$mD024Remetente['D024_Bairro']}', ";                
                $cClausula .= " '{$mD024Remetente['D020_Codigo_IBGE']}', ";                
                $cClausula .= " '{$mD024Remetente['D020_Nome_Cidade']}', ";                
                $cClausula .= " '{$mD024Remetente['D024_Cep']}', ";                
                $cClausula .= " '{$mD024Remetente['D018_UF']}', ";                                                                                                                                                                                                                                 
                $cClausula .= " '{$mD024Remetente['D030_Codigo_Pais']}', ";                                                                                                                                                                                                                                 
                $cClausula .= " '{$mD024Remetente['D030_Nome_Pais']}', ";       
                $cClausula .= " '{$mD148['D148_Cnpj']}', ";
                $cClausula .= " '{$mD148['D148_Cpf']}', ";
                $cClausula .= " '{$mD148['D148_Inscricao_Estadual']}', ";
                $cClausula .= " '{$mD148['D148_Nome']}', ";
                $cClausula .= " '{$mD148['D148_Rua']}', ";
                $cClausula .= " '{$mD148['D148_Numero']}', ";
                $cClausula .= " '{$mD148['D148_Bairro']}', ";
                $cClausula .= " '{$mD148['D020_Codigo_IBGE']}', ";
                $cClausula .= " '{$mD148['D020_Nome_Cidade']}', ";
                $cClausula .= " '{$mD148['D024_Cep']}', ";
                $cClausula .= " '{$mD148['D018_UF']}', ";
                $cClausula .= " '{$mD024Destinatario['D024_Cnpj']}', ";                
                $cClausula .= " '{$mD024Destinatario['D024_Cpf']}', ";                
                $cClausula .= " '{$mD024Destinatario['D024_Inscricao_Estadual']}', ";                
                $cClausula .= " '{$mD024Destinatario['D024_Nome_Empresa']}', ";
                $cClausula .= " '{$mD024Destinatario['D024_DDD_Telefone_1']}{$mD024Destinatario['D024_Telefone_1']}', ";                                          
                $cClausula .= " '{$mD024Destinatario['D024_Endereco']}', ";                
                $cClausula .= " '{$mD024Destinatario['D024_Numero']}', ";                
                $cClausula .= " '{$mD024Destinatario['D024_Bairro']}', ";                
                $cClausula .= " '{$mD024Destinatario['D020_Codigo_IBGE']}', ";                
                $cClausula .= " '{$mD024Destinatario['D020_Nome_Cidade']}', ";                
                $cClausula .= " '{$mD024Destinatario['D024_Cep']}', ";                
                $cClausula .= " '{$mD024Destinatario['D018_UF']}', ";                                                                                                                                                                                                                                 
                $cClausula .= " '{$mD024Destinatario['D030_Codigo_Pais']}', ";                                                                                                                                                                                                                                 
                $cClausula .= " '{$mD024Destinatario['D030_Nome_Pais']}', ";    
                $cClausula .= " '{$mD024Destinatario['D024_E_Mail']}', ";                    
                $cClausula .= " '{$mD006['D059_Codigo_ST']}', ";                    
                $cClausula .= " 'S', ";                    
                $cClausula .= " '{$T084_D024_Tomador_Id}', ";                    
                $cClausula .= " '{$T084_D024_Remetente_Id}', ";                    
                $cClausula .= " '{$T084_D024_Destinatario_Id}', ";                    
                $cClausula .= " '" . date("P") . "', ";
                $cClausula .= " '{$mC004['C004_Ambiente_CTe']}' ";                    
                $cClausula .= " )";

               log("Insert $cClausula");

                mysql_query($cClausula);
                $T084_Id = $g['mysqlLastId'];
                $cErro   = mysql_error();
                $cErroNumero = mysql_errno();
                if (!empty($cErro)) {
                    $retorno = "Clausula: " . $cClausula . "<br />";
                    $retorno .= "ERRO " . $cErroNumero . " - " . $cErro;
                    mysql_query("ROLLBACK");
                    return $retorno;
                }
                $g['mysqlLastId'] = $T084_Id;

                mysql_query("INSERT INTO T084 T084A_T084_Id, T084A_D024_Recebedor_Id VALUES '{$T084_Id}', '{$T084A_D024_Recebedor_Id}'");

            } else if ($Operacao == 'A') {                

                $cClausula = " UPDATE T084 SET ";
                $cClausula .= " T084_D006_Id='" . mysql_real_escape_string($T084_D006_Id) . "', ";
                $cClausula .= " T084_Data_Emissao='" . mysql_real_escape_string($T084_Data_Emissao) . "', ";
                $cClausula .= " T084_Tipo_Conhecimento='" . mysql_real_escape_string($T084_Tipo_Conhecimento) . "', ";
                $cClausula .= " T084_Flag_Modalidade_Transporte='" . mysql_real_escape_string($T084_Flag_Modalidade_Transporte) . "', ";
                $cClausula .= " T084_Flag_Tipo_Servico='" . mysql_real_escape_string($T084_Flag_Tipo_Servico) . "', ";
                $cClausula .= " T084_Flag_Retira='" . mysql_real_escape_string($T084_Flag_Retira) . "', ";
                $cClausula .= " T084_Informacoes_Adicionais_Retira='" . mysql_real_escape_string($T084_Informacoes_Adicionais_Retira) . "', ";
                $cClausula .= " T084_Natureza_Operacao='" . mysql_real_escape_string($mD006['D006_Descricao']) . "', ";
                $cClausula .= " T084_Flag_Indicador_IE='" . mysql_real_escape_string($T084_Flag_Indicador_IE) . "', ";
                $cClausula .= " T084_D018_Id_Origem='" . mysql_real_escape_string($T084_D018_Id_Origem) . "', ";
                $cClausula .= " T084_D020_Id_Origem='" . mysql_real_escape_string($T084_D020_Id_Origem) . "', ";
                $cClausula .= " T084_D018_Id_Destino='" . mysql_real_escape_string($T084_D018_Id_Destino) . "', ";
                $cClausula .= " T084_D020_Id_Destino='" . mysql_real_escape_string($T084_D020_Id_Destino) . "', ";
                $cClausula .= " T084_D020_Id_Destino='" . mysql_real_escape_string($T084_D020_Id_Destino) . "', ";
                $cClausula .= " T084_Codigo_CFOP='" . mysql_real_escape_string($mD006['D006_Codigo_CFOP']) . "', ";
                $cClausula .= " T084_Descricao_CFOP='" . mysql_real_escape_string($mD006['D006_Descricao']) . "', ";
                $cClausula .= " T084_Tomador_CNPJ='" . mysql_real_escape_string($mD024Tomador['D024_Cnpj']) . "', ";
                $cClausula .= " T084_Tomador_CPF='" . mysql_real_escape_string($mD024Tomador['D024_Cpf']) . "', ";
                $cClausula .= " T084_Tomador_IE='" . mysql_real_escape_string($mD024Tomador['D024_Inscricao_Estadual']) . "', ";
                $cClausula .= " T084_Tomador_Nome='" . mysql_real_escape_string($mD024Tomador['D024_Nome_Empresa']) . "', ";
                $cClausula .= " T084_Tomador_Nome_Fantasia='" . mysql_real_escape_string($mD024Tomador['D024_Nome_Fantasia']) . "', ";
                $cClausula .= " T084_Tomador_Rua='" . mysql_real_escape_string($mD024Tomador['D024_Endereco']) . "', ";
                $cClausula .= " T084_Tomador_Numero='" . mysql_real_escape_string($mD024Tomador['D024_Numero']) . "', ";
                $cClausula .= " T084_Tomador_Bairro='" . mysql_real_escape_string($mD024Tomador['D024_Bairro']) . "', ";
                $cClausula .= " T084_Tomador_Codigo_Cidade_IBGE='" . mysql_real_escape_string($mD024Tomador['D020_Codigo_IBGE']) . "', ";
                $cClausula .= " T084_Tomador_Cidade='" . mysql_real_escape_string($mD024Tomador['D020_Nome_Cidade']) . "', ";
                $cClausula .= " T084_Tomador_UF='" . mysql_real_escape_string($mD024Tomador['D018_UF']) . "', ";                         
                $cClausula .= " T084_Remetente_CNPJ='" . mysql_real_escape_string($mD024Remetente['D024_Cnpj']) . "', ";
                $cClausula .= " T084_Remetente_CPF='" . mysql_real_escape_string($mD024Remetente['D024_Cpf']) . "', ";
                $cClausula .= " T084_Remetente_IE='" . mysql_real_escape_string($mD024Remetente['D024_Inscricao_Estadual']) . "', ";
                $cClausula .= " T084_Remetente_Nome='" . mysql_real_escape_string($mD024Remetente['D024_Nome_Empresa']) . "', ";
                $cClausula .= " T084_Remetente_Telefone='" . mysql_real_escape_string($mD024Remetente['D024_DDD_Telefone_1'].$mD024Remetente['D024_Telefone_1']) . "', ";
                $cClausula .= " T084_Remetente_Rua='" . mysql_real_escape_string($mD024Remetente['D024_Endereco']) . "', ";
                $cClausula .= " T084_Remetente_Numero='" . mysql_real_escape_string($mD024Remetente['D024_Numero']) . "', ";
                $cClausula .= " T084_Remetente_Bairro='" . mysql_real_escape_string($mD024Remetente['D024_Bairro']) . "', ";
                $cClausula .= " T084_Remetente_Codigo_Cidade_IBGE='" . mysql_real_escape_string($mD024Remetente['D020_Codigo_IBGE']) . "', ";
                $cClausula .= " T084_Remetente_Cidade='" . mysql_real_escape_string($mD024Remetente['D020_Nome_Cidade']) . "', ";
                $cClausula .= " T084_Remetente_CEP='" . mysql_real_escape_string($mD024Remetente['D024_Cep']) . "', ";
                $cClausula .= " T084_Remetente_UF='" . mysql_real_escape_string($mD024Remetente['D018_UF']) . "', ";
                $cClausula .= " T084_Emitente_CNPJ='" . mysql_real_escape_string($mC004['C004_Cnpj']) . "', ";
                $cClausula .= " T084_Emitente_IE='" . mysql_real_escape_string($mC004['C004_Inscricao_Estadual']) . "', ";
                $cClausula .= " T084_Emitente_Nome='" . mysql_real_escape_string($mC004['C004_Nome_Empresa']) . "', ";
                $cClausula .= " T084_Emitente_Rua='" . mysql_real_escape_string($mC004['C004_Logradouro']) . "', ";
                $cClausula .= " T084_Emitente_Numero='" . mysql_real_escape_string($mC004['C004_Numero']) . "', ";
                $cClausula .= " T084_Emitente_Bairro='" . mysql_real_escape_string($mC004['C004_Bairro']) . "', ";
                $cClausula .= " T084_Emitente_CEP='" . mysql_real_escape_string($mC004['C004_Cep']) . "', ";
                $cClausula .= " T084_Remetente_Codigo_Pais='" . mysql_real_escape_string($mD024Remetente['D030_Codigo_Pais']) . "', ";
                $cClausula .= " T084_Remetente_Nome_Pais='" . mysql_real_escape_string($mD024Remetente['D030_Nome_Pais']) . "', ";
                $cClausula .= " T084_Recebedor_CNPJ='" . mysql_real_escape_string($mD148['D148_Cnpj']) . "', ";
                $cClausula .= " T084_Recebedor_CPF='" . mysql_real_escape_string($mD148['D148_Cpf']) . "', ";
                $cClausula .= " T084_Recebedor_IE='" . mysql_real_escape_string($mD148['D148_Inscricao_Estadual']) . "', ";
                $cClausula .= " T084_Recebedor_Nome='" . mysql_real_escape_string($mD148['D148_Nome']) . "', ";
                $cClausula .= " T084_Recebedor_Rua='" . mysql_real_escape_string($mD148['D148_Rua']) . "', ";
                $cClausula .= " T084_Recebedor_Numero='" . mysql_real_escape_string($mD148['D148_Numero']) . "', ";
                $cClausula .= " T084_Recebedor_Bairro='" . mysql_real_escape_string($mD148['D148_Bairro']) . "', ";
                $cClausula .= " T084_Recebedor_Codigo_Cidade_IBGE='" . mysql_real_escape_string($mD148['D020_Codigo_IBGE']) . "', ";
                $cClausula .= " T084_Recebedor_Cidade='" . mysql_real_escape_string($mD148['D020_Nome_Cidade']) . "', ";
                $cClausula .= " T084_Recebedor_CEP='" . mysql_real_escape_string($mD148['D024_Cep']) . "', ";
                $cClausula .= " T084_Recebedor_UF='" . mysql_real_escape_string($mD148['D018_UF']) . "', ";
                $cClausula .= " T084_Destinatario_CNPJ='" . mysql_real_escape_string($mD024Destinatario['D024_Cnpj']) . "', ";
                $cClausula .= " T084_Destinatario_CPF='" . mysql_real_escape_string($mD024Destinatario['D024_Cpf']) . "', ";
                $cClausula .= " T084_Destinatario_IE='" . mysql_real_escape_string($mD024Destinatario['D024_Inscricao_Estadual']) . "', ";
                $cClausula .= " T084_Destinatario_Nome='" . mysql_real_escape_string($mD024Destinatario['D024_Nome_Empresa']) . "', ";
                $cClausula .= " T084_Destinatario_Telefone='" . mysql_real_escape_string($mD024Destinatario['D024_DDD_Telefone_1'].$mD024Destinatario['D024_Telefone_1']) . "', ";
                $cClausula .= " T084_Destinatario_Rua='" . mysql_real_escape_string($mD024Destinatario['D024_Endereco']) . "', ";
                $cClausula .= " T084_Destinatario_Numero='" . mysql_real_escape_string($mD024Destinatario['D024_Numero']) . "', ";
                $cClausula .= " T084_Destinatario_Bairro='" . mysql_real_escape_string($mD024Destinatario['D024_Bairro']) . "', ";
                $cClausula .= " T084_Destinatario_Codigo_Cidade_IBGE='" . mysql_real_escape_string($mD024Destinatario['D020_Codigo_IBGE']) . "', ";
                $cClausula .= " T084_Destinatario_Cidade='" . mysql_real_escape_string($mD024Destinatario['D020_Nome_Cidade']) . "', ";
                $cClausula .= " T084_Destinatario_CEP='" . mysql_real_escape_string($mD024Destinatario['D024_Cep']) . "', ";
                $cClausula .= " T084_Destinatario_UF='" . mysql_real_escape_string($mD024Destinatario['D018_UF']) . "', ";
                $cClausula .= " T084_Destinatario_Codigo_Pais='" . mysql_real_escape_string($mD024Destinatario['D030_Codigo_Pais']) . "', ";
                $cClausula .= " T084_Destinatario_Nome_Pais='" . mysql_real_escape_string($mD024Destinatario['D030_Nome_Pais']) . "', ";
                $cClausula .= " T084_Destinatario_Email='" . mysql_real_escape_string($mD024Destinatario['D024_E_Mail']) . "', ";
                $cClausula .= " T084_Flag_Situacao_Tributaria='" . mysql_real_escape_string($mD006['D059_Codigo_ST']) . "', ";
                $cClausula .= " T084_D024_Tomador_Id='" . mysql_real_escape_string($T084_D024_Tomador_Id) . "', ";
                $cClausula .= " T084_D024_Remetente_Id='" . mysql_real_escape_string($T084_D024_Remetente_Id) . "', ";
                $cClausula .= " T084_Fuso_Horario_Emissao='" . mysql_real_escape_string(date("P")) . "', ";
                $cClausula .= " T084_D024_Destinatario_Id='" . mysql_real_escape_string($T084_D024_Destinatario_Id) . "'";
                $cClausula .= " WHERE T084_Id='$T084_Id' ";                
                
                log("UPDATE T084: $cClausula");
                mysql_query($cClausula);
                mysql_query("UPDATE T084A SET T084A_D024_Recebedor_Id=" . mysql_real_escape_string($T084A_D024_Recebedor_Id)) . " WHERE T084A_T084_Id='$T084_Id'";
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Clausula: $cClausula/p/pERRO: $cErro";
                }
            }
            
            mysql_query("COMMIT");
            return true;
        }

        /**
         * finalizarCTe         
         * @param String $T084_Id
         * @return String/bool
        */
        public function finalizarCTe($T084_Id){
            global $g;
        
            if(empty($T084_Id)){
                return "Não encontrado o ID do CT-e (T084_Id)";
            }
        
            // Validação dados gerais CTe
            $T084 = mysql_query("SELECT T084_Versao_Cte,
                            T084_Numero_Protocolo_Cte,
                            T084_Flag_Cancelado,                                                                                    
                            T084_Data_Emissao,
                            T084_Numero_Conhecimento,
                            T084_C004_Id,
                            D006_Codigo_CFOP,
                            D006_Flag_Gera_Contas,
                            T084_Tipo_Conhecimento                                           
                       FROM T084       
                       LEFT JOIN D006 ON T084_D006_Id=D006_Id                                  
                      WHERE T084_Id='{$T084_Id}' 
                        AND T084_Flag_Entrada_Saida = 'S'");
            $mT084 = mysql_fetch_assoc($T084);
        
        
            if($mT084['T084_Versao_Cte'] != '3.00' && $mT084['T084_Versao_Cte'] != '4.00'){
                return "Versão ".$mT084['T084_Versao_Cte']." do CT-e, no cadastro da empresa está incorreta.";
            }

            if(!empty($mT084['T084_Numero_Protocolo_Cte'])) {
                return 'CT-e possui protocolo, não pode ser alterado';
            }

            if($mT084['T084_Flag_Cancelado'] == 'S'){
                return "CT-e esta CANCELADO!, não pode ser alterado.";
            }

            if($g['empresaAtual'] != $mT084['T084_C004_Id']) {
                return "A Empresa selecionada não é a mesma do CT-e. <br />Empresa Selecionada: " . $g['empresaAtual'];
            }
        
            // Validação dados da nota do CTe
            $T085 = mysql_query("SELECT T085_Id,
                                        T085_Chave_Acesso_Nfe                                        
                                   FROM T085                              
                                  WHERE T085_T084_Id='{$T084_Id}'");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return 'Erro: ' . $cErro;
            }
            $resultNotasCte = '';

            if(mysql_num_rows($T085) <= 0 and $mT084['T084_Tipo_Conhecimento'] != 1) {
                return "CT-e não possui notas fiscais, não é possível finalizar.";
            }

            // CT-e Normal
            if($mT084['T084_Tipo_Conhecimento'] == 1) {
                while($mT085 = mysql_fetch_array($T085)){                                        
                    if($mT085['T085_Chave_Acesso_Nfe'] <= 0){
                        $resultNotasCte .=" Necessário preencher a chave de acesso da Nota Fiscal <br/>";
                    }
                    
                    $notasCTe = mysql_fetch_assoc(mysql_query("SELECT COUNT(T085_Id) as total 
                                                                        FROM T085
                                                                LEFT JOIN T084 ON T085_T084_Id=T084_Id
                                                                    WHERE T085_Chave_Acesso_Nfe='{$mT085['T085_Chave_Acesso_Nfe']}' 
                                                                    AND T084_Flag_Cancelado='N'
                                                                    GROUP BY T085_Chave_Acesso_Nfe"));
        
                    if($notasCTe['total'] > 1 ){
                        return "A chave de acesso {$mT085['T085_Chave_Acesso_Nfe']}  já está vinculada a outro CT-e";
                    }
        
                    if(!empty($resultNotasCte)){
                        $resultNotasCte .= "<br/>";
                    }
                }
            }
        
            if(!empty($resultNotasCte)){
                return $resultNotasCte;
            }
            
            $cSql_T084_CFOP = mysql_query("SELECT D006_Flag_Gera_Contas 
                                            FROM T084
                                            LEFT JOIN D006 ON T084_D006_Id = D006_Id
                                            WHERE T084_Id = '{$T084_Id}'");
            $mT084_CFOP = mysql_fetch_array($cSql_T084_CFOP);          
            if(($mT084_CFOP['D006_Flag_Gera_Contas'] != 'N') && ($mT084_CFOP['D006_Flag_Gera_Contas'] != 'P') && ($mT084_CFOP['D006_Flag_Gera_Contas'] != 'R')) {
                return "O campo 'Gera contas' do cadastro da CFOP está com um valor inválido. Acesse: Fiscal-> CFOP-> Selecione a CFOP -> aba Financeiro -> Campo 'Gera Contas'. Deve estar preenchido com: 'NÃO ALTERA CONTAS', 'RECEBER' ou 'PAGAR'. Por favor, verifique";
            }

            // Insere o Numero do cte
            if(empty($mT084['T084_Numero_Conhecimento'])){
                        
                list($proximoNumero, $retorno) = $this->retornaProximoCTe($g['empresaAtual']);
                if ($proximoNumero === false) {
                    return   $retorno;
                }
                                        
                mysql_query("UPDATE T084 SET T084_Numero_Conhecimento='{$proximoNumero}', T084_Serie='{$retorno}'  WHERE T084_Id='{$T084_Id}'");
                $erro = mysql_error();
                if ($erro) {
                    mysql_query("ROLLBACK");
                    return $erro;
                }
                
            }
                     
            $erro = mysql_error();
            if ($erro) {
                mysql_query("ROLLBACK");
                return $erro;
            }
            return true;
        }
            
        /**
         * continuarLiberacao
         * Gera as parcelas e realiza ultimas validações         
         * @param String $T084_Id
         * @return String/bool
        */
        public function continuarLiberacao($T084_Id) {
            global $g;

            log("continuarLiberacao 1");

            //Gravar no T002 Contas a Receber
            //Atualizar observação geral

            // Busca as informações da empresa
            $cC004 = mysql_query("SELECT    C004_Ambiente_NFe,
                                            C004_Banco_Dados,
                                            C004_Cnpj,
                                            C004_Codigo_Regime_Tributario,
                                            C004_Observacao_Fiscal,
                                            C004_Ultimo_Numero_CTe,
                                            C004_Ultimo_Numero_Serie_CTe,
                                            C004_Ambiente_CTe,
                                            C004_Versao_CTe
                                            FROM C004 
                                            WHERE C004_Id='1'");
            $mC004 = mysql_fetch_array($cC004);

            $cT084 = mysql_query("SELECT * 
                                    FROM T084
                                    LEFT JOIN T085 ON T085_T084_Id=T084_Id
                                    LEFT JOIN D024 ON T084_D024_Tomador_Id=D024_Id
                                    LEFT JOIN D006 ON T084_D006_Id=D006_Id
                                    LEFT JOIN C007 ON T084_C007_Id=C007_Id 
                                    WHERE T084_Id = '{$T084_Id}'");
            $mT084 = mysql_fetch_array($cT084);

            
            // Verifica se já existe remessa bancaria
            // Remessa bancaria é um documento que você envia para o banco registrar o boleto
            // Se ja exister tem que barrar 
            $sqlT084 = mysql_query("SELECT T084_Id 
                                    FROM T084 
                                        LEFT JOIN T002 ON T002_T084_Id = T084_Id 
                                    WHERE T084_Id = '{$T084_Id}' 
                                    AND T002_T035_Id > 0 AND T002_Flag_Cancelada !='S'");
            if (mysql_num_rows($sqlT084) > 0) {
                return (mysql_num_rows($sqlT084) > 1) ? "Existem títulos a receber que estão em remessa bancária, por favor verifique." : "Existe título a receber que está em remessa bancária, por favor verifique.";
            }

            //VERIFICAR
            $sqlT002 = mysql_query("SELECT T002_Id 
                                    FROM T002 
                                    WHERE T002_T084_Id != '{$T084_Id}' 
                                    AND IFNULL(T002_T117_Id,0)>0 
                                    AND T002_T117_Id IN (SELECT DISTINCT T002_T117_Id FROM T002 WHERE T002_T084_Id = '{$T084_Id}')");

            if (mysql_num_rows($sqlT002) > 0) {
                return (mysql_num_rows($sqlT002) > 1) ? "Existem boletos que estão relacionados a outras notas fiscais, por favor verifique." : "Existe boleto relacionado a outra nota fiscal, por favor verifique.";
            }        

            // REMOVE CONTAS A RECEBER
            // Remove todos os boletos vinculados aos titulos
            log("liberacao 1");
            $sqlT002 = mysql_query("SELECT DISTINCT T002_T117_Id FROM T002 WHERE T002_T084_Id = '{$T084_Id}' AND T002_T117_Id > 0");
            while ($T002 = mysql_fetch_array($sqlT002)) {
                mysql_query("DELETE FROM T117 WHERE T117_Id = '{$T002['T002_T117_Id']}'");
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . mysql_error();
                }
            }
            //Remove os titulos vinculados ao CT-e
            log("liberacao 2 Remove os titulos vinculados ao CT-e");
            $cClausula = "DELETE FROM T002 WHERE T002_T084_Id='{$T084_Id}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }

            // REMOVE CONTAS A PAGAR
            log("liberacao 3 - REMOVE CONTAS A PAGAR");
            $cClausula = "DELETE FROM T015 WHERE T015_T084_Id='{$T084_Id}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }
            
            // log("TESTE continuarLiberacao 8 ");
            // $cClausula = "DELETE FROM T019 WHERE T019_T084_Id='{$T084_Id}'";
            // mysql_query($cClausula);
            // $cErro = mysql_error();
            // if (!empty($cErro)) {
            //     mysql_query("ROLLBACK");
            //     return "Erro: $cErro<br /><br />$cClausula";
            // }
                       
            $sqlCheckDB                = mysql_query("SELECT DATABASE()");
            $rowCheckDB                = mysql_fetch_array($sqlCheckDB);
            $dbCheck                   = strtolower($rowCheckDB[0]);
            $dbCheck                   = str_replace('_novo', '', $dbCheck); //Removendo _novo da base das empresa que foram atualizadas
            $C004dbCheck               = strtolower($mC004['C004_Banco_Dados']);
            $C004dbCheck               = str_replace('_novo', '', $C004dbCheck);            

            //Se o banco de dados aberto for diferente do que esta configurado na empresa
            if($C004dbCheck != $dbCheck){
                return "O banco de dados atual (".$dbCheck.") é diferente do banco de dados configurado nesta empresa (".$C004dbCheck."). Informar o suporte técnico.";
            }

            // VALIDACAO DA CFOP
            $cSql_T084_CFOP = mysql_query("SELECT D006_Flag_Gera_Contas, 
                                             D006_D014_Id, 
                                             D006_D073_Id 
                                        FROM T084
                                    LEFT JOIN D006 ON D006_Id=T084_D006_Id 
                                    WHERE T084_Id='{$T084_Id}'");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return 'Erro: ' . $cErro;
            }

            $mT084_CFOP        = mysql_fetch_array($cSql_T084_CFOP);
            $cFlag_Gera_Contas = $mT084_CFOP['D006_Flag_Gera_Contas'];

            if($cFlag_Gera_Contas == 'P' && (empty($mT084_CFOP['D006_D073_Id']) || $mT084_CFOP['D006_D073_Id'] <= 0)) {
                mysql_query("ROLLBACK");
                return "A CFOP gera contas a pagar e está sem um centro de custo definido! Verifique o cadastro da CFOP.";
            } else {
                $CentroCusto = $mT084_CFOP['D006_D073_Id'];
            }

            if(($cFlag_Gera_Contas == 'P' || $cFlag_Gera_Contas == 'R') && (empty($mT084_CFOP['D006_D014_Id']) || $mT084_CFOP['D006_D014_Id'] <= 0)){
                $tipoConta = ($cFlag_Gera_Contas == 'P') ? 'pagar' : 'receber';
                mysql_query("ROLLBACK");
                return "A CFOP gera contas a {$tipoConta} e está sem uma conta definida! Verifique o cadastro da CFOP.";
            } else {
                $Conta = $mT084_CFOP['D006_D014_Id'];
                $sqlD032 = mysql_query("SELECT D014_D032_Id FROM D014 WHERE D014_Id = '{$Conta}'");
                $D032 = mysql_fetch_array($sqlD032);
                $Grupo = $D032['D014_D032_Id'];
            }

            log("continuarLiberacao 12 - ANTES GERAR CONTAS");
            if (($cFlag_Gera_Contas != 'N') AND ($cFlag_Gera_Contas != 'P') AND ($cFlag_Gera_Contas != 'R')) {
                return "O campo: D006_Flag_Gera_Contas (" . $cFlag_Gera_Contas . ") está incorreto";
            }
                                
            //GERAR CONTAS A PAGAR OU A RECEBER
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

            // TRAZ TODAS AS PARCELAS
            $cClausula = "SELECT    T307_Id,
                                    D027_Portador,
                                    T307_Valor_Parcela,
                                    T307_Numero_Parcela,
                                    T307_Numero_Duplicata,
                                    T307_Prazos_Dias,                                    
                                    D027_Flag_Baixa_Automatica,
                                    D027_D007_Id,
                                    D027_Baixa_Automatica_Dias,
                                    T307_D027_Id,
                                    D007_Numero_Agencia,
                                    D007_Numero_Conta,
                                    D027_Portador,
                                    D027_Tipo_Forma_Pagamento_NFCE
                            FROM T307 
                            LEFT JOIN D027 ON D027_Id=T307_D027_Id
                            LEFT JOIN D007 on D027_D007_Id=D007_Id
                            WHERE T307_T084_Id = '{$T084_Id}'
                            ORDER BY T307_Numero_Parcela";

            $cSql_T307 = mysql_query($cClausula);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                $retorno = "Clausula: " . $cClausula;
                $retorno .= "ERRO: " . $cErro;
                mysql_query("ROLLBACK");
                return $retorno;
            }

            // TRAZ INFORMACOES DA PARCELA E PORTADOR
            $T307_Parcelas=mysql_query("SELECT  T307_Id,
                                                T307_Valor_Parcela, 
                                                D027_Tipo_Forma_Pagamento_NFCE,
                                                D027_Portador
                                            FROM T307 
                                                LEFT JOIN D027 ON D027_Id=T307_D027_Id 
                                                LEFT JOIN D007 ON D007_Id=D027_D007_Id
                                            WHERE T307_T084_Id='{$T084_Id}'");                                
            
            $Total_Parcelas = 0;
            $erroPortador = '';
            log("Antes Validação portador");
            while($parcela = mysql_fetch_array($T307_Parcelas)) {
                if(empty($parcela['D027_Tipo_Forma_Pagamento_NFCE'])){
                    $erroPortador .= $parcela['D027_Portador'] . ", ";
                }
                $Total_Parcelas++;                
            }

            if($Total_Parcelas <= 0){
                return "É obrigatório ter pelo menos uma parcela inserida no CT-e. <br /><br /> Caso o CT-e não possua movimentação financeira, insira uma parcela com um portador SEM PAGAMENTO selecionado.";
            }
                      
            if(!empty($erroPortador)){
                $erroPortador = trim($erroPortador, ",");
                return "O(s) portadore(s) {$erroPortador}  está(ão) com o campo \"Forma de pagamento\" em branco no cadastro. Por favor, verifique.";
            }

            log("Validação portador");            

            //GERACAO DOS TITULOS A PAGAR OU A RECEBER
            if($Total_Parcelas > 0) {
                //TITULO A RECEBER
                if($cFlag_Gera_Contas == 'R') {
                    log("Entrou gerar titulos a receber");
                    $cSql_T307_T084 = mysql_query("SELECT   sum(T307_Valor_Parcela) as Total_Parcelas, 
                                                            T084_Valor_Conhecimento 
                                                    FROM T307 
                                                        LEFT JOIN T084 ON T084_Id=T307_T084_Id 
                                                    WHERE T307_T084_Id='{$T084_Id}' 
                                                    GROUP BY T307_T084_Id");
                    $mT307_T084 = mysql_fetch_array($cSql_T307_T084);
                    
                    //VALOR TOTAL DO CONHECIMENTO DIFERENTE DO VALOR TOTAL DAS PARCELAS
                    if ($mT307_T084['T084_Valor_Conhecimento'] != $mT307_T084['Total_Parcelas']) {
                        mysql_query("ROLLBACK");
                        return "O VALOR TOTAL DAS PARCELAS ESTA DIVERGENTE COM O CT-e";
                    }
                    log("Validação divergencia de valores");                    
                    while ($mT307 = mysql_fetch_array($cSql_T307)) {
                        $Numero_Parcela = $mT307['T307_Numero_Parcela'];

                        if (empty($mT307['T307_Numero_Duplicata'])) {
                            $Numero_Duplicata = $mT084['T084_Numero_Conhecimento'] . $mLetras[$Numero_Parcela];
                        } else {
                            $Numero_Duplicata = $mT307['T307_Numero_Duplicata'] . $mLetras[$Numero_Parcela];
                        }

                        $cSql_Vencimento = mysql_query("SELECT DATE_ADD('{$mT084['T084_Data_Emissao']}',INTERVAL {$mT307['T307_Prazos_Dias']} DAY) as Vencimento");
                        $mVencimento     = mysql_fetch_array($cSql_Vencimento);

                        
                        mysql_query("UPDATE T307 SET T307_Data_Vencimento='{$mVencimento[0]}' WHERE T307_Id='{$mT307['T307_Id']}'");                        
                        log("VENCIMENTO: QUERY: UPDATE T307 SET T307_Data_Vencimento='{$mVencimento[0]}' WHERE T307_Id='{$mT307['T307_Id']}'");
                                                                    
                        if ($mT307['D027_Flag_Baixa_Automatica'] == 'S' and $mT307['D027_D007_Id'] > 0) {
                            $data_Emissao_Lancamento = date("Y-m-d");
                            //Caso exista prazo para a baixa automatica, o lançamento irá ser gravado com emissão igual a data atual mais o prazo
                            if($mT307['D027_Baixa_Automatica_Dias'] > 0){
                                $cSql_Vencimento = mysql_query("SELECT DATE_ADD(current_date(),INTERVAL {$mT307['D027_Baixa_Automatica_Dias']} DAY) as Vencimento");
                                $mVencimento     = mysql_fetch_array($cSql_Vencimento);
                                $data_Emissao_Lancamento = $mVencimento[0];
                            }
                        }

                        log("ANTES INSERT T002");                        
                        $cClausula = " INSERT INTO T002 ( ";
                        $cClausula .= " T002_D024_Id, ";
                        // $cClausula .= " T002_D021_Id, ";
                        $cClausula .= " T002_D027_Id, ";
                        $cClausula .= " T002_T084_Id, ";
                        $cClausula .= " T002_D014_Id, ";
                        $cClausula .= " T002_D032_Id, ";
                        $cClausula .= " T002_C004_Id, ";
                        $cClausula .= " T002_D062_Id, ";
                        $cClausula .= " T002_Nome_Cliente, ";
                        $cClausula .= " T002_Numero_Duplicata, ";
                        $cClausula .= " T002_Numero_Documento, ";
                        $cClausula .= " T002_Data_Emissao, ";
                        $cClausula .= " T002_Data_Vencimento, ";
                        $cClausula .= " T002_Data_Vencimento_Original, ";
                        $cClausula .= " T002_Data_Recebimento, ";
                        $cClausula .= " T002_Valor_Duplicata, ";
                        $cClausula .= " T002_Valor_Saldo, ";
                        $cClausula .= " T002_Valor_Juros, ";
                        $cClausula .= " T002_Valor_Desconto, ";
                        $cClausula .= " T002_Flag_Enviar_Banco_Protestar, ";
                        $cClausula .= " T002_Valor_Total, ";
                        // $cClausula .= " T002_Valor_Base_Comissao, ";
                        // $cClausula .= " T002_Percentual_Comissao, ";
                        // $cClausula .= " T002_Valor_Comissao, ";
                        // $cClausula .= " T002_Percentual_Comissao_Representante, ";
                        // $cClausula .= " T002_Valor_Comissao_Representante, ";
                        // $cClausula .= " T002_Percentual_Comissao_Externo, ";
                        // $cClausula .= " T002_Valor_Total_Comissao_Externo, ";
                        // $cClausula .= " T002_Percentual_Comissao_Usuario, ";
                        // $cClausula .= " T002_Valor_Total_Comissao_Usuario, ";
                        $cClausula .= " T002_Data_Lancamento, ";
                        $cClausula .= " T002_Hora_Lancamento, ";
                        $cClausula .= " T002_Numero_Parcela, ";
                        $cClausula .= " T002_Total_Parcelas ";
                        $cClausula .= " ) VALUES ( ";
                        $cClausula .= " '{$mT084['T084_D024_Tomador_Id']}', ";
                        // $cClausula .= " '{$mT084['T007_D021_Id']}', ";
                        $cClausula .= " '{$mT307['T307_D027_Id']}', ";
                        $cClausula .= " '{$T084_Id}', ";
                        $cClausula .= " '{$Conta}', ";
                        $cClausula .= " '{$Grupo}', ";
                        $cClausula .= " '{$mT084['T084_C004_Id']}', ";
                        $cClausula .= " '{$mT084['D024_D062_Id']}', ";
                        $cClausula .= " '{$mT084['D024_Nome_Empresa']}', ";
                        $cClausula .= " '{$Numero_Duplicata}', ";
                        $cClausula .= " '{$mT084['T084_Numero_Conhecimento']}', ";
                        $cClausula .= " current_date(), ";
                        $cClausula .= " '{$mVencimento[0]}', ";
                        $cClausula .= " '{$mVencimento[0]}', ";
                        $cClausula .= " '0000-00-00', ";
                        $cClausula .= " '{$mT307['T307_Valor_Parcela']}', ";
                        $cClausula .= " '{$mT307['T307_Valor_Parcela']}', ";
                        $cClausula .= " '0', ";
                        $cClausula .= " '0', ";
                        $cClausula .= " '{$mT084['D024_Flag_Enviar_Banco_Protestar']}', ";
                        $cClausula .= " '{$mT307['T307_Valor_Parcela']}', ";
                        // $cClausula .= " '{$Valor_Base_Comissao_Titulo}', ";
                        // $cClausula .= " '{$mT084['T007_Percentual_Comissao']}', ";
                        // $cClausula .= " '{$Valor_Comissao}', ";
                        // $cClausula .= " '{$mT084['T007_Percentual_Comissao_Representante']}', ";
                        // $cClausula .= " '{$Valor_Comissao_Representante}', ";
                        // $cClausula .= " '{$mT084['T007_Percentual_Comissao_Externo']}', ";
                        // $cClausula .= " '{$Valor_Comissao_Externo}', ";
                        // $cClausula .= " '{$mT084['T007_Percentual_Comissao_Usuario']}', ";
                        // $cClausula .= " '{$Valor_Comissao_Usuario}', ";
                        $cClausula .= " current_date(), ";
                        $cClausula .= " current_time(), ";
                        $cClausula .= " '{$Numero_Parcela}', ";
                        $cClausula .= " '{$Total_Parcelas}' ";
                        $cClausula .= " ) ";
                        log("%%SQL= ".$cClausula);
                        mysql_query($cClausula);                        
                        $cErro = mysql_error();

                        if (!empty($cErro)) {
                            mysql_query("ROLLBACK");
                            return "Erro: " . $cErro;
                        }
                        $lastT002 = $g['mysqlLastId'];
                  
                        log("DEPOIS INSERT T002");

                        // *****************************************************
                        // Gerar BOLETO
                        // *****************************************************
                        log("gerar boleto");
                        $cClausula = " SELECT   T002_D024_Id,
                                                T002_Valor_Duplicata
                                        FROM T002 ";
                        $cClausula .= " WHERE T002_Id='{$lastT002}' ";
                        $cSql_T002 = mysql_query($cClausula);
                        $mT002     = mysql_fetch_array($cSql_T002);

                        $cClausula = " SELECT * ";
                        $cClausula .= " FROM D007 ";
                        $cClausula .= " LEFT JOIN D062 ON D007_D062_Id=D062_Id ";
                        $cClausula .= " LEFT JOIN D027 ON D027_D007_Id=D007_Id ";
                        $cClausula .= " LEFT JOIN T307 ON T307_D027_Id=D027_Id ";
                        $cClausula .= " WHERE T307_Id='{$mT307['T307_Id']}' ";
                        $cClausula .= " AND T307_D027_Id='{$mT307['T307_D027_Id']}' ";
                        $cClausula .= " AND D027_Flag_Boleto='S' ";

                        $cSql_D007 = mysql_query($cClausula);
                        $cErro     = mysql_error();
                        if (!empty($cErro)) {
                            $retorno = "Clausula1: " . $cClausula;
                            $retorno .= "ERRO: " . $cErro;
                            mysql_query("ROLLBACK");
                            return $retorno;
                        }
                        log("cheguei em 6 ");
                        // se para esta parcela 
                        log("QUERY GERA BOLETO: $cClausula");                        
                        if (mysql_num_rows($cSql_D007) > 0) {
                            log("cheguei em 7 ");
                            $mD007 = mysql_fetch_array($cSql_D007);
                            
                            if (!empty($mD007['D007_Codigo_Cedente'])) {
                                $xCodigo_Cedente = $mD007['D007_Codigo_Cedente'];
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
                            log("ANTES DE INSERIR O BOLETO T117");                            
                            $cClausula = " INSERT INTO T117 ( ";
                            $cClausula .= " T117_C004_Id, ";
                            $cClausula .= " T117_D024_Id, ";
                            $cClausula .= " T117_D007_Id, ";
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
                            // $cClausula .= " T117_D148_Id_Entrega, ";
                            // $cClausula .= " T117_D148_Id_Cobranca ";
                            $cClausula .= " ) VALUES (";
                            $cClausula .= " '{$g['empresaAtual']}', ";
                            $cClausula .= " '{$mT002['T002_D024_Id']}', ";
                            $cClausula .= " '{$mD007['D007_Id']}', ";
                            $cClausula .= " current_date(), ";
                            $cClausula .= " '{$mVencimento[0]}', ";
                            $cClausula .= " '{$mT002['T002_Valor_Duplicata']}', ";
                            $cClausula .= " '{$mD007['D007_Percentual_Juros_Dia']}', ";
                            $cClausula .= " '{$mD007['D007_Percentual_Multa']}', ";
                            $cClausula .= " '{$mD007['D007_Instrucoes_Boleto']}', ";
                            $cClausula .= " '{$mD007['D007_Nosso_Numero']}', ";
                            $cClausula .= " '{$T117_Flag_Layout}', ";
                            $cClausula .= " '{$D007_Flag_Com_Registro}', ";
                            $cClausula .= " '{$xCodigo_Cedente}', ";
                            $cClausula .= " '{$mD007['D007_Aceite']}', ";
                            $cClausula .= " '{$mD007['D007_Especie']}', ";
                            $cClausula .= " '{$mD007['D007_Especie_Documento']}', ";
                            $cClausula .= " '{$mD007['D007_Uso_Banco']}' ";
                            // $cClausula .= " '" . mysql_real_escape_string($mT084['T007_D148_Id_Entrega']) . "', ";
                            // $cClausula .= " '" . mysql_real_escape_string($mT007['T007_D148_Id_Cobranca']) . "' ";
                            $cClausula .= " )";
                            log("Insert boleto: $cClausula");                            
                            mysql_query($cClausula);
                            $cErro    = mysql_error();
                            $nT117_Id = $g['mysqlLastId'];
                            if (!empty($cErro)) {
                                mysql_query("ROLLBACK");
                                return "ERRO: JÁ EXISTE UM BOLETO COM NOSSO NÚMERO {$mD007 ['D007_Nosso_Numero']}, POR FAVOR ALTERE PARA O PRÓXIMO NÚMERO EM BANCOS/CAIXAS <br /><br /> $cErro - Clausula: $cClausula";
                            }
                            log("cheguei em 8");
                            
                            $nT117_Id  = $g['mysqlLastId'];
                            $cClausula = " UPDATE T002 SET ";
                            $cClausula .= " T002_T117_Id='{$nT117_Id}', ";
                            $cClausula .= " T002_D007_Id='{$mD007['D007_Id']}' ";
                            // clausula para tirar a flag de geração de boleto
                            $cClausula .= " WHERE T002_Id='{$lastT002}' ";                          
                            mysql_query($cClausula);
                            $cErro = mysql_error();
                            if (!empty($cErro)) {
                                mysql_query("ROLLBACK");
                                return "ERRO: $cErro/p/pClausula: $cClausula";
                            }
                            
                            //-- ESTE ARQUIVO IRA GERAR O CODIGO DE BARRAS A LINHA DIGITAVEL E O NOSSO NUMERO COM DIGITO VERIFICADOR
                            $cClausula = " SELECT D062_Numero_Banco ";
                            $cClausula .= " FROM D062 ";
                            $cClausula .= " WHERE D062_Id='{$mD007['D007_D062_Id']}' ";
                            $cSql_D062                  = mysql_query($cClausula);
                            $mD062                      = mysql_fetch_array($cSql_D062);
                            $mD007['D062_Numero_Banco'] = $mD062['D062_Numero_Banco'];

                            log("cheguei em 10 ");
                            // parametros necessários para o include
                            $T117_Data_Vencimento = $mVencimento[0];
                            $nValor_Total         = $mT002['T002_Valor_Duplicata'];
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

                            log("cheguei em 11 ");
                            $cClausula = " UPDATE D007 SET ";
                            $cClausula .= " D007_Nosso_Numero='{$mD007['D007_Nosso_Numero']}' ";
                            $cClausula .= " WHERE D007_Id='{$mD007['D007_Id']}' ";
                            $cSql_D007 = mysql_query($cClausula);
                            $cErro     = mysql_error();
                            if (!empty($cErro)) {
                                mysql_query("ROLLBACK");
                                return "ERRO: $cErro/p/pClausula: $cClausula";
                            }
                            
                            log("cheguei em 12 ");
                            chdir($g['pathRaiz'] . 'hardness/boleto/');
                            $cArquivo_Gerar = "./include/gerar_" . $mD007['D062_Numero_Banco'] . ".php";
                            log("cheguei em 12.1 ");
                            if (file_exists($cArquivo_Gerar)) {
                                log("cheguei em 12.2 : " . $cArquivo_Gerar);
                                $arquivo = include($cArquivo_Gerar);
                                log("cheguei em 13.1 ");
                                if (!empty($arquivo) && $arquivo !== 1) {
                                    chdir($g['pathRaiz']);
                                    return $arquivo;
                                }
                                log("cheguei em 13.2 ");
                            } else {
                                log("cheguei em 14.0 ");
                                mysql_query("ROLLBACK");
                                chdir($g['pathRaiz']);
                                log("cheguei em 14 ");
                                return "Arquivo inválido: $cArquivo_Gerar";
                            }
                            log("cheguei em 15 ");
                            chdir($g['pathRaiz']);
                            
                        }                        
                        // *****************************************************
                    }                    
                } else if($cFlag_Gera_Contas == 'P') {
                    while ($mT307 = mysql_fetch_array($cSql_T307)) {
                        log("cheguei em 18 ");
                        $Numero_Parcela   = $mT307['T307_Numero_Parcela'];
                        $Numero_Duplicata = $mT084['T084_Numero_Conhecimento'] . $mLetras[$Numero_Parcela];
                        
                        $cSql_Vencimento = mysql_query("SELECT DATE_ADD('{$mT084['T084_Data_Emissao']}',INTERVAL {$mT307['T307_Prazos_Dias']} DAY) as Vencimento");
                        $mVencimento     = mysql_fetch_array($cSql_Vencimento);
                        log("cheguei em 19 ");
                                                
                        
                        mysql_query("UPDATE T307 SET T307_Data_Vencimento='$mVencimento[0]' WHERE T307_Id='{$mT307['T307_Id']}'");
                        log("cheguei em 20 ");
                        $cClausula = " INSERT INTO T015 ( ";
                        $cClausula .= " T015_D024_Id, ";                        
                        $cClausula .= " T015_T084_Id, ";
                        $cClausula .= " T015_C004_Id, ";
                        $cClausula .= " T015_D014_Id, ";
                        $cClausula .= " T015_D032_Id, ";
                        $cClausula .= " T015_D073_Id, ";
                        $cClausula .= " T015_Nome_Cliente, ";
                        $cClausula .= " T015_Numero_Duplicata, ";
                        $cClausula .= " T015_Numero_Documento, ";
                        $cClausula .= " T015_Data_Emissao, ";
                        $cClausula .= " T015_Data_Vencimento, ";
                        $cClausula .= " T015_Data_Pagamento, ";
                        $cClausula .= " T015_Valor_Duplicata, ";
                        $cClausula .= " T015_Valor_Juros, ";
                        $cClausula .= " T015_Valor_Desconto, ";
                        $cClausula .= " T015_Valor_Total, ";
                        $cClausula .= " T015_Valor_Saldo, ";
                        // $cClausula .= " T015_Valor_Base_Comissao, ";
                        // $cClausula .= " T015_Percentual_Comissao, ";
                        $cClausula .= " T015_Data_Lancamento, ";
                        $cClausula .= " T015_Hora_Lancamento, ";
                        $cClausula .= " T015_Numero_Parcela ";
                        $cClausula .= " ) VALUES ( ";
                        $cClausula .= " '{$mT084['T084_D024_Tomador_Id']}', ";                        
                        $cClausula .= " '{$T084_Id}', ";
                        $cClausula .= " '{$mT084['T084_C004_Id']}', ";
                        $cClausula .= " '{$Conta}', ";
                        $cClausula .= " '{$Grupo}', ";
                        $cClausula .= " '{$CentroCusto}', ";
                        $cClausula .= " '{$mD024['D024_Nome_Empresa']}', ";
                        $cClausula .= " '{$Numero_Duplicata}', ";
                        $cClausula .= " '{$mT084['T084_Numero_Conhecimento']}', ";
                        $cClausula .= " current_date(), ";
                        $cClausula .= " '{$mVencimento[0]}', ";
                        $cClausula .= " '0000-00-00', ";
                        $cClausula .= " '{$mT307['T307_Valor_Parcela']}', ";
                        $cClausula .= " '0', ";
                        $cClausula .= " '0', ";
                        $cClausula .= " '{$mT307['T307_Valor_Parcela']}', ";
                        $cClausula .= " '{$mT307['T307_Valor_Parcela']}', ";
                        // $cClausula .= " '$Valor_Base_Comissao_Titulo', ";
                        // $cClausula .= " '{$mT084['T007_Percentual_Comissao']}', ";
                        $cClausula .= " current_date(), ";
                        $cClausula .= " current_time(), ";
                        $cClausula .= " '{$Numero_Parcela}' ";
                        $cClausula .= " ) ";
                        mysql_query($cClausula);
                        $cErro = mysql_error();
                        if (!empty($cErro)) {
                            mysql_query("ROLLBACK");
                            return "Erro: " . $cErro;
                        }
                    }
                    log("cheguei em 21");
                } else {
                    log("cheguei em 22 ");
                    if ($cFlag_Gera_Contas != 'N') {
                        return "CT-e não pode ser gerado. Não foram definidas as formas de pagamento.";
                    }   
                }
            }

            //Atualiza Numeracao do CT-e na empresa
            if($mT084['T084_Numero_Conhecimento'] > $mC004['C004_Ultimo_Numero_CTe']) {
                $cClausula = " UPDATE C004 SET ";
                $cClausula .= " C004_Ultimo_Numero_CTe='{$mT084['T084_Numero_Conhecimento']}' ";
                $cClausula .= " WHERE C004_Id='{$mT084['T084_C004_Id']}' ";
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . $cErro;
                }
            }

            //Trava a nota
            mysql_query("UPDATE T084 SET T084_Flag_Travar='S' WHERE T084_Id='{$T084_Id}'");

            // Váriaveis dentro da Observação Fiscal

            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            log("Cheguei aqui no final da liberação FINAL");
            return true;
        }

        /**
         * retornaProximaCTe
         *         
         * @param int C004_Id
         * @return array (ProximoNumero | UltimaSerie) | (false,retorno)
         */
        public function retornaProximoCTe($C004_Id)
        {
            global $g;

            $C004 = mysql_query("select C004_Ultimo_Numero_CTe,
                                    C004_Ultimo_Numero_Serie_CTe                                   
                               from C004 
                              where C004_Id= '{$C004_Id}' ");
            $mC004 = mysql_fetch_array($C004);

            $ultimoNumero = $mC004['C004_Ultimo_Numero_CTe'];
            $ultimaSerie  = $mC004['C004_Ultimo_Numero_Serie_CTe'];
            $proximoNumero = (int)$ultimoNumero + 1;
            log("Ultimo Numero: $ultimoNumero");
            log("Proximo numero: $proximoNumero");

            //verifica se existe o ultimo número
            $ultimo = mysql_query("select T084_Id 
                                 from T084
                                where T084_C004_Id= '{$C004_Id}'                                  
                                  and T084_Serie = '{$ultimaSerie}' 
                                  and T084_Flag_Entrada_Saida = 'S'
                                  and T084_Numero_Conhecimento = '{$ultimoNumero}'");

            if (mysql_num_rows($ultimo) <= 0 && $ultimoNumero > 0) {
                return array(false, "Falta CT-e com Nº {$ultimoNumero}. Verifique o último CT-e emitido se confere com o <b>\"Último Nº CT-e\"</b> no cadastro da empresa");
            }

            //verifica se existe o próximo número
            $proximo = mysql_query("select T084_Id 
                                  from T084
                                 where T084_C004_Id = '{$C004_Id}'                                   
                                   and T084_Serie = '{$ultimaSerie}' 
                                   and T084_Flag_Entrada_Saida = 'S'
                                   and T084_Numero_Conhecimento = '{$proximoNumero}'");

            if (mysql_num_rows($proximo) > 0) {
                return array(false, "Já existe CT-e com Nº {$proximoNumero}. Verifique a último CT-e emitido se confere com o <b>\"Último Nº CT-e\"</b> no cadastro da empresa");
            }

            log("Proximo Numero: $proximoNumero");
            log("Ultima serie: $ultimaSerie");
            return array($proximoNumero, $ultimaSerie);
        }

        public function removeNaoDigitos($value) {
            return preg_replace("/\D/", "",$value);
        } 

        /**
        * enviaCTe
        *
        * @param String $T084_Id
        * @return true
        **/   
        public function enviaCTe($T084_Id) {            
                        
            if(empty($T084_Id))
                return false;
            
            //Monta a classe Cte
            $cte = $this->getCteBuilder();            
                                      
            $T084 = mysql_query("SELECT T084_Chave_Acesso_Cte,
                                        T084_Numero_Conhecimento 
                                   FROM T084 
                                  WHERE T084_Id = '{$T084_Id}'");
                                
            $mT084 = mysql_fetch_array($T084);                

            //Regra: CT-e deve estar finalizado para transmitir

            logCTE::logArquivo("--------------------------------------------------------------------");
            logCTE::logArquivo("\n\nIniciando transmissão CT-e: {$mT084['T084_Numero_Conhecimento']}");                        
            
            //Monta e assina o XML          
            list($status,$mensagem) = $cte->preparaCte($T084_Id, $mT084['T084_Chave_Acesso_Cte']);                                            
            if(!$status)
                return $mensagem;       
                
            return $cte->enviaCTeSEFAZ($T084_Id);                                                             
        }     
        
        /**
        * consultarRecibo
        *
        * @param String $T084_Id
        * @return true
        **/   
        public function consultarRecibo($T084_Id) {              
            
            if(empty($T084_Id))
                return false;

            $cte = $this->getCteBuilder();

            $T084 = mysql_query("SELECT                                         
                                        T084_Numero_Recibo_Cte,
                                        T084_Numero_Protocolo_Cte,
                                        T084_Ambiente_CTe                                       
                                    FROM T084                                    
                                    WHERE T084_Id = '{$T084_Id}'");
            $mT084 = mysql_fetch_array($T084);          

            if(!empty($mT084['T084_Numero_Protocolo_Cte'])) {
                return false;
            }

            logCTE::logArquivo("Consultando Recibo: {$mT084['T084_Numero_Recibo_Cte']}");
            //Consulta o Recibo
            return  $cte->consultaReciboSEFAZ($mT084['T084_Numero_Recibo_Cte'],$mT084['T084_Ambiente_CTe']);                
            
        }

        /** Recupera uma autorização já existente na SEFAZ após rejeição 204. */
        public function recuperarDuplicidadeCTe($T084_Id) {
            if (empty($T084_Id)) {
                return array(false, 'T084_Id não informado.');
            }
            $cte = $this->getCteBuilder();
            return $cte->recuperaAutorizacaoDuplicidade($T084_Id, true);
        }

        /**
        * getCteBuilder
        *
        * @param String $T084_Id        
        **/   
        private function getCteBuilder() {
            global $g;
            
            $C004 = mysql_query("SELECT                                         
                                        C004_Localizacao_Certificados,
                                        C004_Id,
                                        C004_Senha_Certificados,
                                        C004_Versao_CTe,
                                        C004_Ambiente_CTe,
                                        D018_UF,
                                        D018_Codigo_IBGE,
                                        C004_Cnpj
                                    FROM C004 
                                    LEFT JOIN D020 ON D020_Id=C004_D020_Id
                                    LEFT JOIN D018 ON D018_Id=D020_D018_Id                                   
                                    WHERE C004_Id = '{$g['empresaAtual']}'");
            $mC004 = mysql_fetch_array($C004);            
                     
            //Monta Certificado            
            $certificado = null;            
            if($content = file_get_contents("{$mC004['C004_Localizacao_Certificados']}{$mC004['C004_Id']}.pfx")) {            
                $certificado = Certificate::carregaCertP12($content, $mC004['C004_Senha_Certificados']);
            }
                        
            $certificado->setPathPublicKey("{$mC004['C004_Localizacao_Certificados']}{$mC004['C004_Id']}_key.pem");            
            $certificado->setPathCert("{$mC004['C004_Localizacao_Certificados']}{$mC004['C004_Id']}_cert.pem");
            
            // Monta array com as configurações
            $configuracoes = [
                'versao' => $mC004['C004_Versao_CTe'],
                'ambiente' => $mC004['C004_Ambiente_CTe'],
                'UF' => $mC004['D018_UF'],
                'codUF' => $mC004['D018_Codigo_IBGE'],
                'cnpj' => $this->removeNaoDigitos($mC004['C004_Cnpj']),
                'path' => $g['pathDadosAntigo'] . 'cte/xml'
            ];
                    
            logCTE::logArquivo("\n\nCONFIGURACOES:\n\n");
            logCTE::logArquivo($configuracoes,true);

            
            $xml = new Builder();
            $cte = new classeCTe($certificado,$xml,$configuracoes);               
                    
            return $cte;
        }

        /**
        * atualizarAliquota
        *
        * @param String $T084_Id
        * @return true|string
        **/   
        public function atualizarAliquota($T084_Id) {

            $Base_ICMS = 0;
            $Valor_ICMS = 0;
            
            $T084 = mysql_query("SELECT 
                                    T084_Flag_Travar,
                                    T084_Tipo_Conhecimento,
                                    D006_Id,
                                    /* [REFORMA TRIBUTÁRIA CTe] Gabriel Cegantini 08/12/2025
                                    =================================================================== */
                                    D006_Situacao_Tributaria_CBS_IBS,
                                    D006_Classificacao_Tributaria_CBS_IBS,
                                    /* ================================================================ */
                                    T084_D018_Id_Origem,
                                    T084_D018_Id_Destino,
                                    T084_Valor_Conhecimento,
                                    D059_Codigo_ST
                                FROM T084  
                                LEFT JOIN D006 ON D006_Id=T084_D006_Id                                                            
                                LEFT JOIN D059 ON D059_Id=D006_D059_Id                                
                                WHERE T084_Id = '{$T084_Id}'");

            $mT084 = mysql_fetch_array($T084);            

            if($mT084['T084_Flag_Travar'] == 'S')
                return "Não é possível atualizar alíquotas após o CT-e estar finalizado";
                
            //Complemento de valores
            if($mT084['T084_Tipo_Conhecimento'] == 1) {
                return true;
            }

            // Pega dados do ICMS
            list($aliquotaICMS,$percentualReducaoICMS) = $this->retornaICMS($mT084['T084_D018_Id_Origem'], $mT084['T084_D018_Id_Destino'], $mT084['D006_Id']);
            if($aliquotaICMS > 0) {
                $Base_ICMS = $mT084['T084_Valor_Conhecimento'];
                $Base_ICMS *=  ($percentualReducaoICMS > 0 ? ((100 - $percentualReducaoICMS)/100) : 1);                                                            
                $Valor_ICMS = ($Base_ICMS * ($aliquotaICMS / 100));
            }
            $CST_ICMS  = $mT084['D059_Codigo_ST'];

            /* [REFORMA TRIBUTÁRIA CTe] - Gabriel Cegantini 08/12/2025
            Pega os dados da reforma tributária para salvar dentro do CTe
            ========================================================================== */
            $reformaTributaria = new ReformaTributaria();
            $aliquotasReforma = $reformaTributaria->getAliquotas($mT084['D006_Classificacao_Tributaria_CBS_IBS']);
            /* ======================================================================= */
            
            $cClausula = "UPDATE T084
                       LEFT JOIN T084A ON T084A_T084_Id = T084_Id
                             SET T084_Valor_ICMS = '{$Valor_ICMS}' ,
                                 T084_Aliquota_ICMS = '{$aliquotaICMS}' ,
                                 T084_Percentual_Reducao_ICMS = '{$percentualReducaoICMS}',
                                 T084_Valor_Base_ICMS = '{$Base_ICMS}' ,
                                 T084_Flag_Situacao_Tributaria = '{$CST_ICMS}',

                                 /* [REFORMA TRIBUTÁRIA CTe] Gabriel Cegantini 08/12/2025
                                 ============================================================== */
                                 T084A_Situacao_Tributaria_CBS_IBS = '{$mT084['D006_Situacao_Tributaria_CBS_IBS']}',
                                 T084A_Classificacao_Tributaria_CBS_IBS = '{$mT084['D006_Classificacao_Tributaria_CBS_IBS']}',

                                 T084A_Aliquota_CBS = '{$aliquotasReforma['aliquotaCbs']}',
                                 T084A_Aliquota_IBS_UF = '{$aliquotasReforma['aliquotaIbsUf']}',
                                 T084A_Aliquota_IBS_Municipal = '{$aliquotasReforma['aliquotaIbsMunicipal']}',

                                 T084A_Percentual_Reducao_CBS = '{$aliquotasReforma['percentualReducaoCbs']}',
                                 T084A_Percentual_Reducao_IBS_UF = '{$aliquotasReforma['percentualReducaoIbsUf']}',
                                 T084A_Percentual_Reducao_IBS_Municipal = '{$aliquotasReforma['percentualReducaoIbsMunicipal']}',

                                 T084A_Percentual_Diferimento_CBS = '{$aliquotasReforma['percentualDiferimentoCbs']}',
                                 T084A_Percentual_Diferimento_IBS_UF = '{$aliquotasReforma['percentualDiferimentoIbsUf']}',
                                 T084A_Percentual_Diferimento_IBS_Municipal = '{$aliquotasReforma['percentualDiferimentoIbsMunicipal']}'
                                 /* =========================================================== */                                 
                           WHERE T084_Id = '{$T084_Id}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "Erro: $cErro <br /> $cClausula";
            }

            /* [REFORMA TRIBUTÁRIA CTe] - Gabriel Cegantini 08/12/2025:
            Após clicar em "Atualizar alíquotas" dentro do CTe, faz os cálculos necessários para a reforma 
            ============================================================*/
            mysql_query("CALL T084_Gravar_Totalizacao_Reforma('{$T084_Id}')");
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "Erro cálculos reforma: $cErro";
            }
            /* ========================================================= */

            return true;
        }

        /**
        * retornaICMS
        *
        * @param String $T084_Id
        * @return array
        **/   
        public function retornaICMS($D018_Origem, $D018_Destino, $D006_Id) {
            global $g;

            if($D018_Origem != $D018_Destino) {
                $D018 = mysql_query("SELECT             
                                    D006_Flag_Isento_ICMS,
                                    C004_Flag_Isento_ICMS,
                                    D196_Aliquota_ICMS_Inter_Estadual,                                    
                                    D196_Aliquota_ICMS_Interno,
                                    D006_Percentual_Reducao_ICMS,
                                    D006_Aliquota_ICMS                                                                  
                                FROM D196
                                LEFT JOIN D006 ON D006_Id = '{$D006_Id}'
                                LEFT JOIN C004 ON C004_Id = '{$g['empresaAtual']}'
                                WHERE D196_D018_Id_Origem = '{$D018_Origem}' AND D196_D018_Id_Destino = '{$D018_Destino}'");
            } else {
                $D018 = mysql_query("SELECT             
                                    D006_Flag_Isento_ICMS,
                                    C004_Flag_Isento_ICMS,
                                    D196_Aliquota_ICMS_Inter_Estadual,                                    
                                    D196_Aliquota_ICMS_Interno,
                                    D006_Percentual_Reducao_ICMS,
                                    D006_Aliquota_ICMS                                                                  
                                FROM D196
                                LEFT JOIN D006 ON D006_Id = '{$D006_Id}'
                                LEFT JOIN C004 ON C004_Id = '{$g['empresaAtual']}'
                                WHERE D196_D018_Id_Origem = '{$D018_Origem}' LIMIT 1");
            }

            $mD018 = mysql_fetch_array($D018);

            $aliquotaICMS = $D018_Origem != $D018_Destino
                                                    ? $mD018['D196_Aliquota_ICMS_Inter_Estadual'] 
                                                    : $mD018['D196_Aliquota_ICMS_Interno'];
                                                    
            $percentualReducaoICMS = $mD018['D006_Percentual_Reducao_ICMS'];
            
            if($mD018['C004_Flag_Isento_ICMS'] == 1 || $mD018['D006_Flag_Isento_ICMS'] == 'S') {
                $aliquotaICMS = 0;
                $percentualReducaoICMS = 0;
            } else if($mD018['D006_Aliquota_ICMS'] > 0) {
                $aliquotaICMS = $mD018['D006_Aliquota_ICMS'];
            }
            
            return array($aliquotaICMS, $percentualReducaoICMS);
        }

        /**
        * cancelarCTe
        *
        * @param String $T084_Id
        * @return true
        **/   
        public function cancelarCTe($form, $T084_Id) {
            global $g;

            $observacaoCancelamento = $form->campoValorEnviado("T084_Observacao_Cancelamento");
            $observacaoCancelamento = str_replace(["\n","\r"], "", $observacaoCancelamento);

            if(empty($observacaoCancelamento)) {
                return "Preencha o motivo do cancelamento";
            }

            $T084 = mysql_query("SELECT T084_Numero_Protocolo_Cte,
                                        T084_Chave_Acesso_Cte
                                    FROM T084
                                WHERE T084_Id = '{$T084_Id}'");

            $mT084 = mysql_fetch_array($T084);
            $cte = $this->getCteBuilder();
            
            $chave = $mT084['T084_Chave_Acesso_Cte'];
            $protocolo = $mT084['T084_Numero_Protocolo_Cte'];            

            if(empty($protocolo))
                return "É necessario ter protocolo para registrar o cancelamento";

            $retornoCancelamento = $cte->cancelarCTe($chave,$protocolo,$observacaoCancelamento);

            logCTE::logArquivo("Retorno Cancelamento:\n\n");
            logCTE::logArquivo($retornoCancelamento, true);

            if(empty($retornoCancelamento->cStat))
                return "ERRO! Problema na comunicação com o SEFAZ";

            //Evento protocolado
            if($retornoCancelamento->cStat == '135') {
                $dataRegistroEvento  = str_replace("T", " ", $retornoCancelamento->dhRegEvento);
                $dataRegistroEvento  = substr($dataRegistroEvento, 0, 19);

                mysql_query("UPDATE T084 
                                SET T084_Numero_Protocolo_Cancelamento_Cte = '{$retornoCancelamento->nProt}' ,
                                    T084_Flag_Cancelado = 'S',
                                    T084_Data_Hora_Cancelamento_Cte = '{$dataRegistroEvento}'
                              WHERE T084_Id = '{$T084_Id}'");                   
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Ocorreu um erro ao gravar o protocolo de cancelamento no CT-e\nPor favor entre em contato com o suporte \nErro:$cErro";
                }

                //Zera os titulos a pagar e a receber
                $this->gravarCancelameto($T084_Id);

                return true;
            }
            
            return "ERRO! Retorno SEFAZ: {$retornoCancelamento->cStat} - {$retornoCancelamento->xMotivo}";
        }

        /**
        * gravarCancelamento
        *
        * @param String $T084_Id
        * @return true
        **/   
        public function gravarCancelameto($T084_Id){

            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
                        
            //CTe
            mysql_query("UPDATE T084 
                        SET T084_Valor_Conhecimento = 0,
                            T084_Valor_Base_ICMS = 0,
                            T084_Valor_ICMS = 0,
                            T084_Valor_Receber = 0
                        WHERE T084_Id = '{$T084_Id}'");

            //Cancela Contas a receber
            mysql_query("UPDATE T002 
                    SET T002_Flag_Cancelada='S',
                        T002_Data_Recebimento=current_date(),
                        T002_Observacao_2='*** CTE CANCELADO ***',
                        T002_Valor_Duplicata=0,
                        T002_Valor_Total=0                            
                    WHERE T002_T084_Id='{$T084_Id}'");

            //Cancela boleto se existir
            $T002 = mysql_query("SELECT T002_T117_Id
                                    FROM T002
                                WHERE T002_T084_Id='{$T084_Id}'
                                AND IFNULL(T002_T117_Id,0) > 0");

            while($mT002 = mysql_fetch_assoc($T002)){                
                    mysql_query("UPDATE T117
                                    SET T117_Flag_Cancelado = 'S'
                                WHERE T117_Id = '{$mT002['T002_T117_Id']}'");
            }

            //Cancela Contas a pagar
            mysql_query("UPDATE T015 
                            SET T015_Flag_Cancelada='S',
                                T015_Data_Pagamento=current_date(),
                                T015_Observacao_2='*** CTE CANCELADO ***',
                                T015_Valor_Duplicata=0,
                                T015_Valor_Total=0
                            WHERE T015_T084_Id='{$T084_Id}'");

            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
        }

        /**
        * excluirCTe
        *
        * @param String $T084_Id
        * @return true
        **/ 
        public function excluirCTe($T084_Id) {

            $T084 = mysql_query("SELECT T084_Id, 
                                        C004_Id,
                                        T084_Numero_Recibo_Cte,
                                        T084_Numero_Conhecimento,
                                        T084_Numero_Protocolo_Cte,                                        
                                        T084_Chave_Acesso_Cte,
                                        C004_Ultimo_Numero_CTe                                        
                                FROM T084
                                LEFT JOIN C004 ON C004_Id = T084_C004_Id
                                WHERE T084_Id = '{$T084_Id}'");

            $mT084 = mysql_fetch_array($T084);

            if(!empty($mT084['T084_Numero_Protocolo_Cte'])) {
                return "Já existe Protocolo para o CT-e {$mT084['T084_Numero_Conhecimento']}, ele não pode ser excluído";
            }

            if(!empty($mT084['T084_Numero_Recibo_Cte'])) {
                return "Já existe Recibo para o CT-e {$mT084['T084_Numero_Conhecimento']}, ele não pode ser excluído";
            }
                    
            // Verifica se já existe remessa bancaria            
            // Se ja exister tem que barrar 
            $sqlT084 = mysql_query("SELECT T084_Id 
                                    FROM T084 
                                        LEFT JOIN T002 ON T002_T084_Id = T084_Id 
                                    WHERE T084_Id = '{$T084_Id}' 
                                    AND T002_T035_Id > 0 AND T002_Flag_Cancelada !='S'");
            if (mysql_num_rows($sqlT084) > 0) {
                return (mysql_num_rows($sqlT084) > 1) ? "Existem títulos a receber que estão em remessa bancária, por favor verifique." : "Existe título a receber que está em remessa bancária, por favor verifique.";
            }

            //VERIFICAR
            $sqlT002 = mysql_query("SELECT T002_Id 
                                    FROM T002 
                                    WHERE T002_T084_Id != '{$T084_Id}' 
                                    AND IFNULL(T002_T117_Id,0)>0 
                                    AND T002_T117_Id IN (SELECT DISTINCT T002_T117_Id FROM T002 WHERE T002_T084_Id = '{$T084_Id}')");

            if (mysql_num_rows($sqlT002) > 0) {
                return (mysql_num_rows($sqlT002) > 1) ? "Existem boletos que estão relacionados a outras notas fiscais, por favor verifique." : "Existe boleto relacionado a outra nota fiscal, por favor verifique.";
            }   


            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            //Remove as notas vinculadas
            mysql_query("DELETE FROM T085 WHERE T085_T084_Id = '{$T084_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }

            mysql_query("DELETE FROM T304 WHERE T304_T084_Id = '{$T084_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }
            
            mysql_query("DELETE FROM T305 WHERE T305_T084_Id = '{$T084_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }
             
            mysql_query("DELETE FROM T306 WHERE T306_T084_Id = '{$T084_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }
            
            //Parcelas
            mysql_query("DELETE FROM T307 WHERE T307_T084_Id  = '{$T084_Id}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }

            //Titulos e boletos
            $sqlT002 = mysql_query("SELECT DISTINCT T002_T117_Id FROM T002 WHERE T002_T084_Id = '{$T084_Id}' AND T002_T117_Id > 0");
            while ($T002 = mysql_fetch_array($sqlT002)) {
                mysql_query("DELETE FROM T117 WHERE T117_Id = '{$T002['T002_T117_Id']}'");
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . mysql_error();
                }
            }
            
            //Remove os titulos vinculados ao CT-e              
            $cClausula = "DELETE FROM T002 WHERE T002_T084_Id='{$T084_Id}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }
  
            // REMOVE CONTAS A PAGAR
            
            $cClausula = "DELETE FROM T015 WHERE T015_T084_Id='{$T084_Id}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }
            
            if($mT084['C004_Ultimo_Numero_CTe'] == $mT084['T084_Numero_Conhecimento']) {
                $numeroAnterior = $mT084['T084_Numero_Conhecimento'] - 1;
                mysql_query("UPDATE C004 SET C004_Ultimo_Numero_CTe = '{$numeroAnterior}' WHERE C004_Id = '{$mT084['C004_Id']}'");
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "Erro: $cErro <br /><br /> $cClausula";
                }
            }
            //Remove CT-e
            $cClausula = "DELETE FROM T084 WHERE T084_Id ='{$T084_Id}'";
            mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                mysql_query("ROLLBACK");
                return "Erro: $cErro <br /><br /> $cClausula";
            }

            mysql_query("COMMIT");
            return true;
        }

         /**
        * enviarLoteCTe
        *
        * @param String $numLote
        * @return true
        **/ 
        public function enviarLoteCTe($numLote) {
            global $g;
        
            if(empty($numLote))
                return "Não foi informado o número do Lote";
                
            $T084 = mysql_query("SELECT T084_Id
                                    FROM T084
                                WHERE T084_Numero_Lote = '{$numLote}'
                                AND T084_C004_Id = '{$g['empresaAtual']}'");
            
            if(mysql_num_rows($T084) > 50)
                return array(false, 'Não é possível enviar mais do que 50 CT-e no mesmo lote');
                
            //Monta a classe Cte
            $cte = $this->getCteBuilder();      
                   
            return $cte->enviaCTeLoteSEFAZ($numLote);        
        }

        public function formataCnpjCpf($cnpj_cpf) {     
            $length = strlen($cnpj_cpf);
            if ($length == 11) {
                return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
            } else if($length == 14) {
                return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
            }
            return $cnpj_cpf;
        }
        
        public function formataCEP($cep) {                 
            return preg_replace("/(\d{5})(\d{3})/", "\$1-\$2", $cep);                        
        }

        public function gerarQRCode($qrCodCTe,$fileName) {                                                
            global $g;

            // Já está sendo feita a requisção na própria IDE para usar o qrCode em etiquetas
            //require_once("hardness/phpqrcode/qrlib.php");            
            
            $path = $g['pathDadosAntigo'] . 'cte/qrcode/';
            if (!is_dir($path)) mkdir($path, 0777,true);
        
            $file = "{$path}{$fileName}.jpg";
            \QRcode::png($qrCodCTe,$file);      

            $content = file_get_contents($file);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($content);

            unlink($file);
            return $base64;
        }
    }
                    
}

