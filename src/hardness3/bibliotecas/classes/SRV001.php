<?php
namespace hardness;
definirClasse('SRV001');
class SRV001_
{
 
    /**
     * incluirContratosMensais
     *
     * @param Form $form
     * @return String/bool
     */   
    public function incluirContratosMensais($form)
    {
        global $g;
        
        $T135 = mysql_query("SELECT * FROM T135 WHERE T135_Tipo='M' AND T135_Flag_Gerar_Recebimento='S'");
        
        if (mysql_num_rows($T135) <= 0) {
            return "Por favor, selecione algum contrato para gerar os recebimentos.";
        }
        
        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($mT135 = mysql_fetch_array($T135)) {
            
            $T135_D024_Id         = $mT135['T135_D024_Id'];
            $T135_Data_Inicio     = $mT135['T135_Data_Inicio'];
            $T135_Data_Termino    = $mT135['T135_Data_Termino'];
            $T135_Valor_Contrato  = $mT135['T135_Valor_Contrato'];
            $T135_Tipo            = $mT135['T135_Tipo'];
            $T135_Dia_Vencimento  = $mT135['T135_Dia_Vencimento'];
            $T135_Mes_Inicial     = $mT135['T135_Mes_Inicial'];
            $T135_Numero_Parcelas = $mT135['T135_Numero_Parcelas'];
            $T135_D014_Id         = $mT135['T135_D014_Id'];
            $T135_D032_Id         = $mT135['T135_D032_Id'];
            $Mes                  = $form->campoValorEnviado('Mes');
            $Ano                  = $form->campoValorEnviado('Ano');
            
            $D027  = mysql_query("SELECT D027_Id FROM D027 WHERE D027_D007_Id > 0 AND D027_Flag_Boleto = 'S' LIMIT 1");
            $mD027 = mysql_fetch_array($D027);
            
            $D024  = mysql_query("SELECT D024_Nome_Empresa FROM D024 WHERE D024_Id='{$T135_D024_Id}'");
            $mD024 = mysql_fetch_array($D024);
            
            $contrado = str_pad($mT135['T135_Id'], 3, "00", STR_PAD_LEFT);
            
            $Numero_Duplicata = $contrado . $Mes . $Ano;
            
            $Data_Vencimento = $Ano . '-' . $Mes . '-' . $mT135['T135_Dia_Vencimento'];
            
            $T002 = mysql_query("SELECT * FROM T002 WHERE T002_D024_Id='{$T135_D024_Id}' AND T002_Data_Vencimento='{$Data_Vencimento}' AND T002_T135_Id>0");
            if (mysql_num_rows($T002)) {
                mysql_query("ROLLBACK");
                return "1. Já foram gerados recebimentos para o cliente: {$mD024['D024_Nome_Empresa']}";
            }
            
            $queryT002 = "INSERT INTO T002 
	                        (T002_D024_Id,
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
							 T002_T135_Id,
							 T002_Total_Parcelas
							 )
					   VALUES
					       ('{$T135_D024_Id}',
							'{$g['empresaAtual']}',
                            '{$mD027['D027_Id']}',
							'{$T135_D014_Id}',
                            '{$T135_D032_Id}',
							'{$mD024['D024_Nome_Empresa']}',
							'{$Numero_Duplicata}',
							'{$Numero_Duplicata}',
							curdate(),
							'{$Data_Vencimento}',
							'0000-00-00',
							'{$T135_Valor_Contrato}',
							'{$T135_Valor_Contrato}',
							'0',
							'0',
							'{$T135_Valor_Contrato}',
							curdate(),
							'" . strftime('%T') . "',
							'1',
							'{$mT135['T135_Id']}',
							'1'
							)";
            mysql_query($queryT002);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }
            $lastT002 = $g['mysqlLastId'];
            
            /////////////////////////////////////////////////////////////////////////////////////////////
            //			GERAÇÃO DO BOLETO
            /////////////////////////////////////////////////////////////////////////////////////////////
            $cClausula = " SELECT * FROM T002 ";
            $cClausula .= " WHERE T002_Id='{$lastT002}' ";
            $cSql_T002 = mysql_query($cClausula);
            $mT002     = mysql_fetch_array($cSql_T002);
            
            $cClausula = " SELECT * ";
            $cClausula .= " FROM D007 ";
            $cClausula .= " LEFT JOIN D027 ON D027_D007_Id=D007_Id ";
            $cClausula .= " WHERE D027_Id='{$mD027['D027_Id']}' ";
            $cSql_D007 = mysql_query($cClausula);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                $retorno = "Clausula: " . $cClausula;
                $retorno .= "ERRO: " . $cErro;
                mysql_query("ROLLBACK");
                return $retorno;
            }
            // se para esta parcela 
            if (mysql_num_rows($cSql_D007) > 0) {
                $mD007 = mysql_fetch_array($cSql_D007);
                
                if (!empty($mD007['D007_Codigo_Cedente'])) {
                    $xCodigo_Cedente = $mD007['D007_Codigo_Cedente'];
                } else {
                    mysql_query("ROLLBACK");
                    return "Erro ao gerar boletos, código do cedente no cadastro do banco deve estar preenchido!";
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
                $cClausula .= " T117_Uso_Banco, ";
                $cClausula .= " T117_T002_Id ";
                $cClausula .= " ) VALUES (";
                $cClausula .= " '{$g['empresaAtual']}', ";
                $cClausula .= " '{$mT002['T002_D024_Id']}', ";
                $cClausula .= " '{$mD007['D007_Id']}', ";
                $cClausula .= " current_date(), ";
                $cClausula .= " '{$Data_Vencimento}', ";
                $cClausula .= " '{$mT002['T002_Valor_Duplicata']}', ";
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
                $cClausula .= " '{$mD007['D007_Uso_Banco']}', ";
                $cClausula .= " '{$lastT002}' ";
                $cClausula .= " )";
                mysql_query($cClausula);
                log($cClausula);
                $cErro    = mysql_error();
                $nT117_Id = $g['mysqlLastId'];
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "ERRO: $cErro/p/pClausula: $cClausula";
                }
                
                $nT117_Id  = $g['mysqlLastId'];
                $cClausula = " UPDATE T002 SET ";
                $cClausula .= " T002_T117_Id='$nT117_Id', ";
                $cClausula .= " T002_D007_Id='{$mD007['D007_Id']}' ";
                // clausula para tirar a flag de geração de boleto
                $cClausula .= " WHERE T002_Id='$lastT002' ";
                //mysql_query("ROLLBACK");
                mysql_query($cClausula);
                $cErro = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "ERRO: $cErro/p/pClausula: $cClausula";
                }
                
                //-- ESTE ARQUIVO IR� GERAR O CODIGO DE BARRAS A LINHA DIGITAVEL E O NOSSO NUMERO COM DIGITO VERIFICADOR
                $cClausula = " SELECT D062_Numero_Banco ";
                $cClausula .= " FROM D062 ";
                $cClausula .= " WHERE D062_Id='{$mD007['D007_D062_Id']}' ";
                $cSql_D062                  = mysql_query($cClausula);
                $mD062                      = mysql_fetch_array($cSql_D062);
                $mD007['D062_Numero_Banco'] = $mD062['D062_Numero_Banco'];
                
                // parametros necessários para o include
                $T117_Data_Vencimento = $Data_Vencimento;
                $nValor_Total         = $mT002['T002_Valor_Duplicata'];
                if (empty($mD007['D007_Nosso_Numero'])) {
                    $mD007['D007_Nosso_Numero'] = 1;
                } else {
                    $mD007['D007_Nosso_Numero']++;
                }
                
                if ($mD007['D062_Numero_Banco'] == '399') {
                    $mD007['D007_Nosso_Numero'] = STR_PAD($mD007['D007_Nosso_Numero'], 5, "0", STR_PAD_LEFT);
                }
                
                $cClausula = " UPDATE D007 SET ";
                $cClausula .= " D007_Nosso_Numero='{$mD007['D007_Nosso_Numero']}' ";
                $cClausula .= " WHERE D007_Id='{$mD007['D007_Id']}' ";
                $cSql_D007 = mysql_query($cClausula);
                $cErro     = mysql_error();
                if (!empty($cErro)) {
                    mysql_query("ROLLBACK");
                    return "ERRO: $cErro/p/pClausula: $cClausula";
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
            
            mysql_query("UPDATE T135 SET T135_Flag_Gerar_Recebimento='N' WHERE T135_Id='{$mT135['T135_Id']}'");
        }
        
        mysql_query("COMMIT");
        return true;
    }
    
    /**
     * incluirContratosAnuais
     *
     * @param String $D024_Id
     * @return String/bool
     */    
    public function incluirContratosAnuais($form)
    {
        global $g;
        
        $T135 = mysql_query("SELECT * FROM T135 WHERE T135_Tipo='A' AND T135_Flag_Gerar_Recebimento='S' AND T135_Flag_Cancelado!='S'");
        
        if (mysql_num_rows($T135) <= 0) {
            return "Por favor, selecione algum contrato para gerar os recebiimentos.";
        }
        
        $xAno = $form->campoValorEnviado('Ano');

        mysql_query("SET AUTOCOMMIT=0");
        mysql_query("START TRANSACTION");
        
        while ($mT135 = mysql_fetch_array($T135)) {
            
            $T135_D024_Id         = $mT135['T135_D024_Id'];
            $T135_Data_Inicio     = $mT135['T135_Data_Inicio'];
            $T135_Data_Termino    = $mT135['T135_Data_Termino'];
            $T135_Valor_Contrato  = $mT135['T135_Valor_Contrato'];
            $T135_Tipo            = $mT135['T135_Tipo'];
            $T135_Dia_Vencimento  = $mT135['T135_Dia_Vencimento'];
            $T135_Mes_Inicial     = $mT135['T135_Mes_Inicial'];
            $T135_Numero_Parcelas = $mT135['T135_Numero_Parcelas'];
            $T135_D014_Id         = $mT135['T135_D014_Id'];
            $T135_D032_Id         = $mT135['T135_D032_Id'];
            $ano                  = $xAno;
            
            $D027  = mysql_query("SELECT D027_Id FROM D027 WHERE D027_D007_Id > 0 AND D027_Flag_Boleto = 'S' LIMIT 1"); // D027_Portador LIKE '%BOLETO%'
            $mD027 = mysql_fetch_array($D027);
            
            $D024  = mysql_query("SELECT D024_Nome_Empresa FROM D024 WHERE D024_Id='{$T135_D024_Id}'");
            $mD024 = mysql_fetch_array($D024);
            
            $T135_Valor_Contrato = $T135_Valor_Contrato / $T135_Numero_Parcelas;
            $count               = 0;

            //$ano = date("Y");
            
            for ($i = 1; $i <= $T135_Numero_Parcelas; $i++) {
                
                $contrado = str_pad($mT135['T135_Id'], 3, "00", STR_PAD_LEFT);
                
                $Numero_Duplicata = $contrado . date('Y') . chr($i + 64);
                
                if ($T135_Mes_Inicial == 13) {
                    $T135_Mes_Inicial = 1;
                    $ano++;
                }
                
                $Data_Vencimento = $ano . '-' . $T135_Mes_Inicial . '-' . $T135_Dia_Vencimento;
                $Mes_Vencimento  = $ano . '-' . str_pad($T135_Mes_Inicial, 2, "0", STR_PAD_LEFT);
                
                $T002 = mysql_query("SELECT * FROM T002 WHERE T002_D024_Id='{$T135_D024_Id}' AND T002_Data_Vencimento like '{$Mes_Vencimento}%' AND T002_C004_Id={$g['empresaAtual']} AND (T002_T002_Id_Agrupado<=0 or T002_T002_Id_Agrupado is null)");
                log("SELECT * FROM T002 WHERE T002_D024_Id='{$T135_D024_Id}' AND T002_Data_Vencimento like '{$Mes_Vencimento}%'");
                if (mysql_num_rows($T002)) {
                    //mysql_query("ROLLBACK");
                    //return "2. Já foram gerados recebimentos para o cliente: {$mD024['D024_Nome_Empresa']}";
                    $T135_Mes_Inicial++;
                    log("continue $T135_Mes_Inicial - parcela: $i");
                    continue;
                }
                
                // Calcula a diferença de segundos entre as duas datas:
                $diferenca = strtotime($Data_Vencimento) - strtotime(date("Y-m-d"));
                $dias      = (int) floor($diferenca / (60 * 60 * 24));
                if ($dias < 7) {
                    $dias            = 7 - $dias;
                    $Data_Vencimento = date("Y-m-d", strtotime("+$dias days", strtotime($Data_Vencimento)));
                }
                
                $queryT002 = "INSERT INTO T002 
	                            (T002_D024_Id,
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
								 T002_T135_Id,
								 T002_Total_Parcelas
								 )
						   VALUES
						       ('{$T135_D024_Id}',
								'{$g['empresaAtual']}',
								'{$mD027['D027_Id']}',
                                '{$T135_D014_Id}',
                                '{$T135_D032_Id}',
								'{$mD024['D024_Nome_Empresa']}',
								'{$Numero_Duplicata}',
								'{$Numero_Duplicata}',
								curdate(),
								'{$Data_Vencimento}',
								'0000-00-00',
								'{$T135_Valor_Contrato}',
								'{$T135_Valor_Contrato}',
								'0',
								'0',
								'{$T135_Valor_Contrato}',
								curdate(),
								'" . strftime('%T') . "',
								'{$i}',
								'{$mT135['T135_Id']}',
								'{$T135_Numero_Parcelas}'
								)";
                log($queryT002);
                //die($queryT002);
                mysql_query($queryT002);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return "Erro: " . mysql_error();
                }
                $count++;
                $lastT002 = $g['mysqlLastId'];
                
                /////////////////////////////////////////////////////////////////////////////////////////////
                //			GERAÇÃO DO BOLETO
                /////////////////////////////////////////////////////////////////////////////////////////////
                $cClausula = " SELECT * FROM T002 ";
                $cClausula .= " WHERE T002_Id='{$lastT002}' ";
                $cSql_T002 = mysql_query($cClausula);
                $mT002     = mysql_fetch_array($cSql_T002);
                
                $cClausula = " SELECT * ";
                $cClausula .= " FROM D007 ";
                $cClausula .= " LEFT JOIN D027 ON D027_D007_Id=D007_Id ";
                $cClausula .= " WHERE D027_Id='{$mD027['D027_Id']}' ";
                $cSql_D007 = mysql_query($cClausula);
                $cErro     = mysql_error();
                if (!empty($cErro)) {
                    $retorno = "Clausula: " . $cClausula;
                    $retorno .= "ERRO: " . $cErro;
                    mysql_query("ROLLBACK");
                    return $retorno;
                }
                // se para esta parcela 
                if (mysql_num_rows($cSql_D007) > 0) {
                    $mD007 = mysql_fetch_array($cSql_D007);
                    
                    if (!empty($mD007['D007_Codigo_Cedente'])) {
                        $xCodigo_Cedente = $mD007['D007_Codigo_Cedente'];
                    } else {
                        mysql_query("ROLLBACK");
                        return "Erro ao gerar boletos, código do cedente no cadastro do banco deve estar preenchido!";
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
                    $cClausula .= " T117_Uso_Banco, ";
                    $cClausula .= " T117_T002_Id ";
                    $cClausula .= " ) VALUES (";
                    $cClausula .= " '{$g['empresaAtual']}', ";
                    $cClausula .= " '{$mT002['T002_D024_Id']}', ";
                    $cClausula .= " '{$mD007['D007_Id']}', ";
                    $cClausula .= " current_date(), ";
                    $cClausula .= " '{$Data_Vencimento}', ";
                    $cClausula .= " '{$mT002['T002_Valor_Duplicata']}', ";
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
                    $cClausula .= " '{$mD007['D007_Uso_Banco']}', ";
                    $cClausula .= " '{$lastT002}' ";
                    $cClausula .= " )";
                    mysql_query($cClausula);
                    log($cClausula);
                    $cErro    = mysql_error();
                    $nT117_Id = $g['mysqlLastId'];
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "ERRO: $cErro/p/pClausula: $cClausula";
                    }
                    
                    $nT117_Id  = $g['mysqlLastId'];
                    $cClausula = " UPDATE T002 SET ";
                    $cClausula .= " T002_T117_Id='$nT117_Id', ";
                    $cClausula .= " T002_D007_Id='{$mD007['D007_Id']}' ";
                    // clausula para tirar a flag de geração de boleto
                    $cClausula .= " WHERE T002_Id='$lastT002' ";
                    //mysql_query("ROLLBACK");
                    //die($cClausula);
                    mysql_query($cClausula);
                    $cErro = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "ERRO: $cErro/p/pClausula: $cClausula";
                    }
                    
                    //-- ESTE ARQUIVO IR� GERAR O CODIGO DE BARRAS A LINHA DIGITAVEL E O NOSSO NUMERO COM DIGITO VERIFICADOR
                    $cClausula = " SELECT D062_Numero_Banco ";
                    $cClausula .= " FROM D062 ";
                    $cClausula .= " WHERE D062_Id='{$mD007['D007_D062_Id']}' ";
                    $cSql_D062                  = mysql_query($cClausula);
                    $mD062                      = mysql_fetch_array($cSql_D062);
                    $mD007['D062_Numero_Banco'] = $mD062['D062_Numero_Banco'];
                    
                    // parametros necessários para o include
                    $T117_Data_Vencimento = $Data_Vencimento;
                    $nValor_Total         = $mT002['T002_Valor_Duplicata'];
                    if (empty($mD007['D007_Nosso_Numero'])) {
                        $mD007['D007_Nosso_Numero'] = 1;
                    } else {
                        $mD007['D007_Nosso_Numero']++;
                    }
                    
                    if ($mD007['D062_Numero_Banco'] == '399') {
                        $mD007['D007_Nosso_Numero'] = STR_PAD($mD007['D007_Nosso_Numero'], 5, "0", STR_PAD_LEFT);
                    }
                    
                    $cClausula = " UPDATE D007 SET ";
                    $cClausula .= " D007_Nosso_Numero='{$mD007['D007_Nosso_Numero']}' ";
                    $cClausula .= " WHERE D007_Id='{$mD007['D007_Id']}' ";
                    $cSql_D007 = mysql_query($cClausula);
                    $cErro     = mysql_error();
                    if (!empty($cErro)) {
                        mysql_query("ROLLBACK");
                        return "ERRO: $cErro/p/pClausula: $cClausula";
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
                log($queryT002);
                $T135_Mes_Inicial++;
            }
            
            if ($count <= 0) {
                mysql_query("ROLLBACK");
                return "3. Já foram gerados recebimentos para o cliente: {$mD024['D024_Nome_Empresa']}";
                
            }
            mysql_query("UPDATE T135 SET T135_Flag_Gerar_Recebimento='N' WHERE T135_Id='{$mT135['T135_Id']}'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return "Erro: " . mysql_error();
            }
        }
        
        mysql_query("COMMIT");
        return true;
    }   
}
