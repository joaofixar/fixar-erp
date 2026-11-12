<?php
namespace hardness;
/**
* Alterar (sobre-escrever) Métodos de uma Classe
*/

class VEN001 extends VEN001_ {
	// defina os métodos para sobreescrever	
    public function retornaAliquotas($D009_Id, $T003_Id = false, $T005_Id = false, $T007_Id = false, $T004_Id = false, $T006_Id = false, $T008_Id = false, $CFOP = false, $D024_Id_Enviado = false)
    {
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        global $g;

        log("Retorna aliquotas D009_Id = {$D009_Id}");
        
        $mProduto = array();
        
        if ($T003_Id > 0) {
            $cClausula = " SELECT * ";
            $cClausula .= " FROM T003 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T003_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T003_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T003_C004_Id ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=T003_D006_Id ";
            $cClausula .= " WHERE T003_Id='{$T003_Id}' ";
            
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);
            
            $D024_Id                  = $mCab['T003_D024_Id'];
            $C004_Id                  = $mCab['T003_C004_Id'];
            $D018_Id                  = $mCab['D024_D018_Id'];
            $Flag_Acp                 = $mCab['T003_Flag_Acp'];
            $Flag_Revenda_Consumidor  = $mCab['D024_Flag_Revenda_Consumidor'];
            $Codigo_Regime_Tributario = $mCab['D024_Codigo_Regime_Tributario'];
            $Flag_Contribuinte        = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao      = $mCab['T003_Percentual_Comissao'];
            $Aliquota_ICMS_Credito    = "";
            
            $cClausula = " SELECT * FROM T004 WHERE T004_Id='{$T004_Id}'";
            $sqlT004   = mysql_query($cClausula);
            $cErro     = mysql_error();
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            
            $T004                = mysql_fetch_array($sqlT004);
            $D006_Id             = $T004['T004_D006_Id'];
            $Flag_Origem_Produto = $T004['T004_Flag_Origem_Produto'];
            
        } else if ($T005_Id > 0) {
            $cClausula = " SELECT * ";
            $cClausula .= " FROM T005 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T005_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T005_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T005_C004_Id ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=T005_D006_Id ";
            $cClausula .= " WHERE T005_Id='$T005_Id' ";
            
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);
            
            $D024_Id                  = $mCab['T005_D024_Id'];
            $C004_Id                  = $mCab['T005_C004_Id'];
            $D018_Id                  = $mCab['D024_D018_Id'];
            $Flag_Acp                 = $mCab['T005_Flag_ACP'];
            $Flag_Revenda_Consumidor  = $mCab['D024_Flag_Revenda_Consumidor'];
            $Codigo_Regime_Tributario = $mCab['D024_Codigo_Regime_Tributario'];
            $Flag_Contribuinte        = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao      = $mCab['T005_Percentual_Comissao'];
            $Aliquota_ICMS_Credito    = "";
            
            $sqlT006 = mysql_query("SELECT * FROM T006 WHERE T006_Id = '{$T006_Id}'");
            $T006    = mysql_fetch_array($sqlT006);
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }

            $Flag_Origem_Produto = $T006['T006_Flag_Origem_Produto'];
            $D006_Id             = $T006['T006_D006_Id'];
            
        } else if ($T007_Id > 0) {
            
            $cClausula = " SELECT * ";
            $cClausula .= " FROM T007 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T007_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T007_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T007_C004_Id ";
            $cClausula .= " LEFT JOIN T134 ON T134_Id=T007_T134_Id ";
            $cClausula .= " LEFT JOIN D006 ON D006_Id=T007_D006_Id ";
            $cClausula .= " WHERE T007_Id='$T007_Id' ";
            
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);
            
            $D024_Id                  = $mCab['T007_D024_Id'];
            $C004_Id                  = $mCab['T007_C004_Id'];
            $D018_Id                  = $mCab['D024_D018_Id'];
            $Flag_Acp                 = $mCab['T007_Flag_ACP'];
            $Flag_Revenda_Consumidor  = $mCab['D024_Flag_Revenda_Consumidor'];
            $Codigo_Regime_Tributario = $mCab['D024_Codigo_Regime_Tributario'];
            $Flag_Contribuinte        = $mCab['D024_Flag_Contribuinte'];
            $Percentual_Comissao      = $mCab['T007_Percentual_Comissao_Representante'];
            $Aliquota_ICMS_Credito    = $mCab['T134_ICMS'];
            
            $sqlT008 = mysql_query("SELECT *,substring(T008_Flag_Situacao_Tributaria,1,1) as T008_Flag_Origem_Produto FROM T008 WHERE T008_Id = '{$T008_Id}'");
            $cErro   = mysql_error();
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $T008 = mysql_fetch_array($sqlT008);
            
            $Flag_Origem_Produto = $T008['T008_Flag_Origem_Produto'];
            $D006_Id = $T008['T008_D006_Id'];
            
        } else {
            // Nenhum dado recebido
        }
        if($CFOP > 0) {
            $D006_Id = $CFOP;
        }
       
        if(empty($D024_Id) && !empty($D024_Id_Enviado)){
            $D024_Id = $D024_Id_Enviado;
            $sqlD009 = mysql_query("SELECT * FROM D009 WHERE D009_Id = '{$D009_Id}'");
            $mD009 = mysql_fetch_array($sqlD009);
            $C004_Id = $mD009['D009_C004_Id'];

            $cClausula = " SELECT * FROM D024 ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=$C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=$C004_Id ";
            $cClausula .= " WHERE D024_Id='$D024_Id' ";
            $Cab   = mysql_query($cClausula);
            $cErro = mysql_error();
            
            if (!empty($cErro)) {
                return "{$cErro}<br />{$cClausula}";
            }
            $mCab = mysql_fetch_array($Cab);

            $D018_Id                  = $mCab['D024_D018_Id'];
            $Flag_Revenda_Consumidor  = $mCab['D024_Flag_Revenda_Consumidor'];
            $Codigo_Regime_Tributario = $mCab['D024_Codigo_Regime_Tributario'];
            $Flag_Contribuinte        = $mCab['D024_Flag_Contribuinte'];
            $Flag_Acp                 = 6;
        }

        $cClausula = " SELECT *,D088_Com_Aliquota.D088_Codigo_ST as D088_Codigo_ST, D088_Sem_Aliquota.D088_Codigo_ST as D088_Codigo_ST_Sem_Aliquota";
        $cClausula .= " FROM D006 ";
        $cClausula .= " LEFT JOIN D059 ON D059_Id=D006_D059_Id ";
        $cClausula .= " LEFT JOIN D088 as D088_Com_Aliquota ON D088_Com_Aliquota.D088_Id=D006_D088_Id ";
        $cClausula .= " LEFT JOIN D088 as D088_Sem_Aliquota ON D088_Sem_Aliquota.D088_Id=D006_D088_Id_Sem_Aliquota ";
        $cClausula .= " WHERE D006_Id='{$D006_Id}'";
        $D006 = mysql_query($cClausula);
        if (!empty($cErro)) {
            return "$cErro<br />$cClausula";
        }
        $mD006 = mysql_fetch_array($D006);

        log("Retorna aliquotas - Iniciando query produtos D009_Id = {$D009_Id}");
        
        $cClausula = " SELECT ";
        $cClausula .= " *, ";
        $cClausula .= " D009_Id, ";
        $cClausula .= " D001_Id, ";
        $cClausula .= " D049_Id, ";
        $cClausula .= " D001_Codigo_Produto, ";
        $cClausula .= " D001_Descricao_Produto, ";
        $cClausula .= " D001_D037_Id, ";
        $cClausula .= " D009_Valor_Custo_Unitario, ";
        $cClausula .= " D009_Flag_Preco_Tabelado, ";
        $cClausula .= " D041_Valor_Preco_Consumidor, ";
        $cClausula .= " D041_Valor_Base, ";
        $cClausula .= " D041_Valor_Preco_Revenda, ";
        $cClausula .= " D024_Horario_Entrega, ";
        $cClausula .= " D001_Peso_Unitario_Kg, ";
        $cClausula .= " D001_Peso_Unitario_Kg, ";
        $cClausula .= " D001_Peso_Total_Kg, ";
        $cClausula .= " D001_Qte_Embalagem, ";
        $cClausula .= " D037_Unidade, ";
        $cClausula .= " D037_Id, ";
        $cClausula .= " D001_Flag_Tipo, ";
        $cClausula .= " D049_Origem_Mercadoria, ";
        $cClausula .= " IFNULL(D009_Origem_Mercadoria, '') as D009_Origem_Mercadoria, ";
        $cClausula .= " D009_IPI_3('$C004_Id',D009_Id,'$D024_Id','$D006_Id','1','$Flag_Acp') as Aliquota_IPI, ";
        $cClausula .= " D009_IPI_3('$C004_Id',D009_Id,'$D024_Id','$D006_Id','2','$Flag_Acp') as Percentual_Reducao_IPI, ";
        $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',2) as Aliquota_ICMS, ";
        $cClausula .= " D009_IPV_ICMS('$C004_Id','$D024_Id',D009_Id,'$D006_Id','$D018_Id','$Flag_Revenda_Consumidor','$Percentual_Comissao','$Flag_Acp',3) as Percentual_Reducao_ICMS, ";
        $cClausula .= " D005_Id, ";
        $cClausula .= " D049_Flag_Substituicao_Tributaria, ";
        $cClausula .= " D049_Flag_Nacional_Importado, ";
        $cClausula .= " D005_Flag_ST, ";
        $cClausula .= " D005_Classificacao_Fiscal, ";
        $cClausula .= " D088_Codigo_ST, ";
        $cClausula .= " D088_Id, ";
        $cClausula .= " D005_Aliquota_PIS, ";
        $cClausula .= " D005_Aliquota_COFINS, ";
        $cClausula .= " D149_Valor_VA, ";
        $cClausula .= " D009_Valor_Custo_ST_Unitario, ";
        $cClausula .= " D009_Valor_Custo_ST_Medio_Unitario ";
        $cClausula .= " FROM D001 as D1";
        $cClausula .= " LEFT JOIN D049 ON D001_Id=D049_D001_Id ";
        $cClausula .= " LEFT JOIN D009 ON D049_Id=D009_D049_Id ";
        $cClausula .= " LEFT JOIN D041 ON D041_D049_Id=D049_Id ";
        $cClausula .= " LEFT JOIN D037 ON D037_Id=D001_D037_Id ";
        $cClausula .= " LEFT JOIN D024 ON D024_Id={$D024_Id} ";
        $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
        $cClausula .= " LEFT JOIN D005 ON D005_Id=D001_D005_Id OR D005_Id = (SELECT T025_D005_Id FROM D001 LEFT JOIN T025 ON T025_D015_Id=D001_D015_Id AND T025_D002_Id=D001_D002_Id WHERE D001_Id=D1.D001_Id LIMIT 1)";
        $cClausula .= " LEFT JOIN D149 ON D005_Id=D149_D005_Id AND D149_C004_Id={$C004_Id} ";
        $cClausula .= " LEFT JOIN D088 ON D088_Id=D149_D088_Id ";
        $cClausula .= " WHERE D009_Id='$D009_Id' GROUP BY D009_Id";
        $Produto  = mysql_query($cClausula);
        $mProduto = mysql_fetch_array($Produto);
        require_once("bibliotecas/classes/CAD002.php");
        $CAD002 = new CAD002();
        log($cClausula);

        if($mProduto['D009_Origem_Mercadoria'] != ''){
            $mProduto['D049_Origem_Mercadoria'] = $mProduto['D009_Origem_Mercadoria'];
        }
        log("Flag CST: ".$mProduto['D049_Origem_Mercadoria']);

        if (!empty($Flag_Origem_Produto)) {
            //$mProduto['D049_Origem_Mercadoria'] = $Flag_Origem_Produto;
        }
        log("Flag CST: ".$mProduto['D049_Origem_Mercadoria']);

        //Verifica as exceções de valor de MVA da NCM
        /*
        if (!empty($mProduto['D149_Regras_MVA'])) {
            preg_match_all("/(MVA|MVAA) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_MVA);
            $valoresRegras = $D149_Regras_MVA[3];
            if (in_array($mProduto['D018_UF'], $D149_Regras_MVA[2])) {
                $regra                     = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);
                $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
            }
        }*/

        $Aliquota_ICMS           = $CAD002->retornaICMS($C004_Id, $D024_Id, $mProduto['D009_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '2');
        $Percentual_Reducao_ICMS = $CAD002->retornaICMS($C004_Id, $D024_Id, $mProduto['D009_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '3');


        // Quando a CFOP é isenta de ICMS, o sistema vai gerar o valor do ICMS nos campos com extenção "Oculto", para que estes valores possam ser utilizados na observação da nota
        // Os parametros finais = 22 ou 33 é para retornar as aliquotas mesmo que a CFOP seja isenta
        // Marcelo de Paula - 18-09-2014
        $Aliquota_ICMS_Oculto    = $CAD002->retornaICMS($C004_Id, $D024_Id, $mProduto['D009_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '22');
        $Percentual_Reducao_ICMS_Oculto = $CAD002->retornaICMS($C004_Id, $D024_Id, $mProduto['D009_Id'], $D006_Id, $D018_Id, $Flag_Revenda_Consumidor, $Percentual_Comissao, $Flag_Acp, '33');

        $mProduto['Aliquota_ICMS']           = $Aliquota_ICMS;
        $mProduto['Percentual_Reducao_ICMS'] = $Percentual_Reducao_ICMS;

        //Verifica as exceções de valor de MVA e ICMS da NCM
        if (!empty($mProduto['D149_Regras_MVA'])) {
            //1 Regra - MVA ajustado por UF
            preg_match_all("/(MVA|MVAA) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_MVA);
            $valoresRegras = $D149_Regras_MVA[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_MVA[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array("XX", $D149_Regras_MVA[2])) {
                $regra = array_search("XX", $D149_Regras_MVA[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }
            preg_match_all("/(MVACRT".$Codigo_Regime_Tributario."|MVAA) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_MVA);
            $valoresRegras = $D149_Regras_MVA[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_MVA[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_MVA[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array("XX", $D149_Regras_MVA[2])) {
                $regra = array_search("XX", $D149_Regras_MVA[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mProduto['D149_Valor_VA'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }
            if ($mProduto['Aliquota_ICMS']>0) {
                log("/(ICM|ICMS) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
                preg_match_all("/(ICM|ICMS) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }
            }
            log("/(ICMIE|ICMSIE) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
            preg_match_all("/(ICMIE|ICMSIE) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
            $valoresRegras = $D149_Regras_ICM[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Inter_Estadual'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array('XX', $D149_Regras_ICM[2])) {
                $regra = array_search('XX', $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Inter_Estadual'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }
            log("/(ICMINT|ICMSINT) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
            preg_match_all("/(ICMINT|ICMSINT) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
            $valoresRegras = $D149_Regras_ICM[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Interno'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array('XX', $D149_Regras_ICM[2])) {
                $regra = array_search('XX', $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Interno'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }

            //****************************
            // ICMS por tipo contribuinte
            //****************************
            if ($mProduto['Aliquota_ICMS']>0) {
                log("/(ICM".$Flag_Contribuinte."|ICMS".$Flag_Contribuinte.") +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
                preg_match_all("/(ICM".$Flag_Contribuinte."|ICMS".$Flag_Contribuinte.") +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
                $valoresRegras = $D149_Regras_ICM[3];
                if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                    $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                } else if (in_array('XX', $D149_Regras_ICM[2])) {
                    $regra = array_search('XX', $D149_Regras_ICM[2]);
                    if (!empty($valoresRegras[$regra])) {
                        $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                    }
                }
            }
            log("/(ICMIE".$Flag_Contribuinte."|ICMSIE".$Flag_Contribuinte.") +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
            preg_match_all("/(ICMIE".$Flag_Contribuinte."|ICMSIE".$Flag_Contribuinte.") +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
            $valoresRegras = $D149_Regras_ICM[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Inter_Estadual'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array('XX', $D149_Regras_ICM[2])) {
                $regra = array_search('XX', $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Inter_Estadual'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }
            log("/(ICMINT".$Flag_Contribuinte."|ICMSINT".$Flag_Contribuinte.") +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
            preg_match_all("/(ICMINT".$Flag_Contribuinte."|ICMSINT".$Flag_Contribuinte.") +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
            $valoresRegras = $D149_Regras_ICM[3];
            if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Interno'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            } else if (in_array('XX', $D149_Regras_ICM[2])) {
                $regra = array_search('XX', $D149_Regras_ICM[2]);
                if (!empty($valoresRegras[$regra])) {
                    $mCab['D053_ST_ICMS_Interno'] = str_replace(",", ".", $valoresRegras[$regra]);
                }
            }

        }


        if (($mProduto['D049_Origem_Mercadoria'] == '1' || $mProduto['D049_Origem_Mercadoria'] == '2' || $mProduto['D049_Origem_Mercadoria'] == '3') && $mCab['D024_D018_Id'] != $mCab['C004_D018_Id'] && $mD006['D006_Flag_Isento_ICMS'] != 'S') {
            if (!empty($mProduto['D149_Regras_MVA'])) {    
                if ($mProduto['Aliquota_ICMS']>0) { 
                    log("/(ICMIMP|ICMSIMP) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims {$mProduto['D149_Regras_MVA']}, {$D149_Regras_ICM}");
                    preg_match_all("/(ICMIMP|ICMSIMP) +([a-z]{2}) *[^[0-9]]? *(([0-9]|\,|\.)+)( |\n|\r|)/ims", $mProduto['D149_Regras_MVA'], $D149_Regras_ICM);
                    $valoresRegras = $D149_Regras_ICM[3];
                    if (in_array($mCab['D018_UF'], $D149_Regras_ICM[2])) {
                        $regra = array_search($mProduto['D018_UF'], $D149_Regras_ICM[2]);
                        if (!empty($valoresRegras[$regra])) {
                            $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                        }
                    } else if (in_array('XX', $D149_Regras_ICM[2])) {
                        $regra = array_search('XX', $D149_Regras_ICM[2]);
                        if (!empty($valoresRegras[$regra])) {
                            $mProduto['Aliquota_ICMS'] = str_replace(",", ".", $valoresRegras[$regra]);
                        }
                    }
                }
            }
            $mCab['D053_ST_ICMS_Inter_Estadual'] = $mProduto['Aliquota_ICMS'] ;
            $Aliquota_ICMS_Oculto = $mProduto['Aliquota_ICMS'] ;
        }

        if($mD006['D006_Somar_FCP_ICMS_Interno'] == 'S'){
            $mCab['D053_ST_ICMS_Interno'] = $mCab['D053_ST_ICMS_Interno'] + $mCab['D053_Percentual_FCP'];
        }

        $D053_ST_ICMS_Inter_Estadual = $mCab['D053_ST_ICMS_Inter_Estadual'];
        $D053_ST_ICMS_Interno        = $mCab['D053_ST_ICMS_Interno'];

        if ($mD006['D006_ST_ICMS_Inter_Estadual_MVA'] > 0)
            $mCab['D053_ST_ICMS_Inter_Estadual'] = $mD006['D006_ST_ICMS_Inter_Estadual_MVA'];
        if ($mD006['D006_ST_ICMS_Interno_MVA'] > 0)
            $mCab['D053_ST_ICMS_Interno'] = $mD006['D006_ST_ICMS_Interno_MVA'];
        
        if ($mD006['D006_ST_MVA'] > 0)
            $mProduto['D149_Valor_VA'] = $mD006['D006_ST_MVA'];
        
        if ($mProduto['D149_Valor_VA'] > 0 and $mD006['D006_Flag_Ajustar_MVA']!='N') {
            $Variavel_A                = 1 + ($mProduto['D149_Valor_VA'] / 100);
            $Variavel_B                = (1 - ($mCab['D053_ST_ICMS_Inter_Estadual'] / 100)) / (1 - ($mCab['D053_ST_ICMS_Interno'] / 100));
            $Variavel_AB               = (($Variavel_A * $Variavel_B) - 1) * 100;
            $mProduto['D149_Valor_VA'] = $Variavel_AB;
        }

        $mCab['D053_ST_ICMS_Inter_Estadual'] = $D053_ST_ICMS_Inter_Estadual;
        $mCab['D053_ST_ICMS_Interno']        = $D053_ST_ICMS_Interno;
        
        if ($mD006['D006_ST_ICMS_Inter_Estadual'] > 0)
            $mCab['D053_ST_ICMS_Inter_Estadual'] = $mD006['D006_ST_ICMS_Inter_Estadual'];
        if ($mD006['D006_ST_ICMS_Interno'] > 0)
            $mCab['D053_ST_ICMS_Interno'] = $mD006['D006_ST_ICMS_Interno'];
        
        if ($mD006['D006_Flag_ST_VA'] == "N") {
            $mProduto['D149_Valor_VA'] = 0;
        }
        if ((($Flag_Acp != '1') AND ($Flag_Acp != '6')) OR ($mD006['D006_Flag_Substituicao_Tributaria'] != 'S')) {
            $mProduto['D149_Flag_ST']  = "N";
            $mProduto['D149_Valor_VA'] = 0;
        }
        
        if ($mD006['D006_Flag_Substituicao_Tributaria'] == 'S' && $mProduto['D149_Flag_ST'] == 'S') {
            $Flag_ST = 'S';
        } else {
            $Flag_ST                               = 'N';
            //$mCab['D053_ST_ICMS_Interno']          = 0;
            //$mCab['D053_ST_ICMS_Inter_Estadual']   = 0;
            //$mD006['D006_ST_ICMS_Interno_Reducao'] = 0;
            //$mProduto['D149_Valor_VA']             = 0;
        }

        //Aplica a redução de MVA
        if ($mD006['D006_Reducao_MVA'] > 0) {
            $mProduto['D149_Valor_VA'] = $mProduto['D149_Valor_VA'] - (( ($mD006['D006_Reducao_MVA'] / 100) * $mProduto['D149_Valor_VA']) / 100);
        }
        
        $Flag_Situacao_Tributaria = '0' . $mD006['D059_Codigo_ST'];
        
        
        // Verificando a Situação tributária
        if ((!empty($mProduto['D049_Origem_Mercadoria']) OR $mProduto['D049_Origem_Mercadoria'] == '0') AND $mProduto['D049_Origem_Mercadoria'] != '') {
            $Flag_Situacao_Tributaria = $mProduto['D049_Origem_Mercadoria'] . $mD006['D059_Codigo_ST'];
            
        } else if (!empty($mProduto['D049_Flag_Nacional_Importado'])) {
            if ($mProduto['D049_Flag_Nacional_Importado'] == "N") {
                $Flag_Situacao_Tributaria = '0' . $mD006['D059_Codigo_ST'];
            } else {
                $Flag_Situacao_Tributaria = '1' . $mD006['D059_Codigo_ST'];
            }
        } else if (!empty($mProduto['D001_Flag_Tipo'])) {
            if ($mProduto['D001_Flag_Tipo'] == "PRODUZIDO") {
                $Flag_Situacao_Tributaria = '1' . $mD006['D059_Codigo_ST'];
            } else {
                $Flag_Situacao_Tributaria = '0' . $mD006['D059_Codigo_ST'];
            }
        }

        if($mD006['D006_Flag_Isento_ICMS'] == 'D' && ($mD006['D006_D059_Id'] == '5' || $mD006['D006_D059_Id'] == '6' || $mD006['D006_D059_Id'] == '7')){
            $mProduto['Aliquota_ICMS'] = 0;
        }
        
        if ($mProduto['D149_Aliquota_PIS'] > 0) {
            $mCab['C004_Percentual_PIS'] = $mProduto['D149_Aliquota_PIS'];
        }
        
        if ($mProduto['D149_Aliquota_COFINS'] > 0) {
            $mCab['C004_Percentual_COFINS'] = $mProduto['D149_Aliquota_COFINS'];
        }

        if ($mProduto['D149_Aliquota_IRPJ'] > 0) {
            $mCab['C004_Percentual_IRPJ'] = $mProduto['D149_Aliquota_IRPJ'];
        }
        
        if ($mProduto['D149_Aliquota_CSLL'] > 0) {
            $mCab['C004_Percentual_CSLL'] = $mProduto['D149_Aliquota_CSLL'];
        }
        
        // Buscado dados dos tributos da lei da transparência
        
        // Buscando o percentual de tributos (lei transparências)
        $D149a  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$mProduto['D149_Id']}'");
        $mD149a = mysql_fetch_array($D149a);
        
        $D006a  = mysql_query("SELECT D006_Percentual_Trib_Transparencia FROM D006 WHERE D006_Id='{$D006_Id}'");
        $mD006a = mysql_fetch_array($D006a);
        
        if (!empty($mD149a['D149_Percentual_Trib_Transparencia']) AND $mD149a['D149_Percentual_Trib_Transparencia'] > 0) {
            
            $Percentual_Trib_Transparencia = $mD149a['D149_Percentual_Trib_Transparencia'];
            
        } else if (!empty($mD006a['D006_Percentual_Trib_Transparencia']) AND $mD006a['D006_Percentual_Trib_Transparencia'] > 0) {
            
            $Percentual_Trib_Transparencia = $mD006a['D006_Percentual_Trib_Transparencia'];
            
        } else {
            
            $Percentual_Trib_Transparencia = 0;
        }

        if ($mD006['D006_Flag_Venda_Compra_Outros']=='V' and $mProduto['D088_Id']>0){
            $mD006['D088_Codigo_ST'] = $mProduto['D088_Codigo_ST'];
        } else if ($mProduto['Aliquota_IPI']<=0 and $mD006['D006_D088_Id_Sem_Aliquota']>0){
            $mD006['D088_Codigo_ST'] = $mD006['D088_Codigo_ST_Sem_Aliquota'];
        }

        // Definindo o array de retorno
        $array = array();

        $array['D005_Id']                          = $mProduto['D005_Id'];
        $array['D149_Id']                          = $mProduto['D149_Id'];
        $array['Classificacao_Fiscal']             = $mProduto['D005_Classificacao_Fiscal'];
        $array['D006_Id']                          = $D006_Id;
        $array['Aliquota_IPI']                     = $mProduto['Aliquota_IPI'];
        $array['Aliquota_ICMS']                    = $mProduto['Aliquota_ICMS'];
        $array['Aliquota_ICMS_Oculto']             = $Aliquota_ICMS_Oculto;
        $array['Percentual_Reducao_ICMS']          = $mProduto['Percentual_Reducao_ICMS'];
        $array['Percentual_Reducao_ICMS_Oculto']   = $Percentual_Reducao_ICMS_Oculto;
        $array['Percentual_Reducao_ICMS_ST']       = $mD006['D006_ST_ICMS_Interno_Reducao'];
        $array['ST_VA']                            = $mProduto['D149_Valor_VA'];
        $array['Flag_ST']                          = $Flag_ST;
        $array['Flag_Situacao_Tributaria']         = $Flag_Situacao_Tributaria;
        $array['ICMS_Inter_Estadual']              = $mCab['D053_ST_ICMS_Inter_Estadual'];
        $array['ST_ICMS_Interno']                  = $mCab['D053_ST_ICMS_Interno'];
        $array['ST_ICMS_Inter_Estadual_MVA']       = $mD006['D006_ST_ICMS_Inter_Estadual_MVA'];
        $array['ST_ICMS_Interno_MVA']              = $mD006['D006_ST_ICMS_Interno_MVA'];
        $array['Flag_Isento_PIS']                  = $mD006['D006_Flag_Isento_PIS'];
        $array['Aliquota_PIS']                     = $mCab['C004_Percentual_PIS'];
        $array['Situacao_Tributaria_PIS']          = $mD006['D006_Situacao_Tributaria_PIS'];
        $array['Flag_Isento_COFINS']               = $mD006['D006_Flag_Isento_COFINS'];
        $array['Aliquota_COFINS']                  = $mCab['C004_Percentual_COFINS'];
        $array['Situacao_Tributaria_COFINS']       = $mD006['D006_Situacao_Tributaria_COFINS'];
        $array['Situacao_Tributaria_IPI']          = $mD006['D088_Codigo_ST'];
        $array['D059_Id']                          = $mD006['D059_Id'];
        $array['D082_Id']                          = $mProduto['D049_D082_Id'];
        $array['Valor_Custo_Unitario']             = $mProduto['D009_Valor_Custo_Unitario'];
        $array['D001_Peso_Unitario_Kg']            = $mProduto['D001_Peso_Unitario_Kg'];
        $array['D001_Peso_Unitario_Bruto']         = $mProduto['D001_Peso_Unitario_Bruto'];
        $array['Percentual_Reducao_IPI']           = $mProduto['Percentual_Reducao_IPI'];
        $array['Aliquota_ICMS_Credito']            = $Aliquota_ICMS_Credito;
        $array['Percentual_Trib_Transparencia']    = $Percentual_Trib_Transparencia;
        $array['Flag_Pre_Cadastro']                = $mProduto['D001_Flag_Pre_Cadastro'];
        $array['Valor_Custo_ST_Unitario']          = $mProduto['D009_Valor_Custo_ST_Unitario'];
        $array['Valor_Custo_ST_Medio_Unitario']    = $mProduto['D009_Valor_Custo_ST_Medio_Unitario'];
        $array['Motivo_Desoneracao_ICMS']          = $mD006['D006_Motivo_Desoneracao_ICMS'];
        $array['IPV_Sem_Lucro']                    = $mCab['C004_IPV_Padrao_Sem_Lucro'];
        $array['Aliquota_IRPJ']                    = $mCab['C004_Percentual_IRPJ'];
        $array['Flag_Isento_IRPJ']                 = $mD006['D006_Flag_Isento_IRPJ'];
        $array['Aliquota_CSLL']                    = $mCab['C004_Percentual_CSLL'];
        $array['Flag_Isento_CSLL']                 = $mD006['D006_Flag_Isento_CSLL'];

        $array['Percentual_Partilha_ICMS_Destino'] = $mD006['D006_Percentual_Partilha_ICMS_Destino'];
        $array['Percentual_Partilha_ICMS_Origem']  = $mD006['D006_Percentual_Partilha_ICMS_Origem'];
        $array['Codigo_CEST']                      = $mProduto['D149_Codigo_CEST'];
        $array['Percentual_FCP']                   = $mCab['D053_Percentual_FCP'];
        $array['Codigo_Enq_Legal_IPI']             = $mD006['D006_Codigo_Enq_Legal_IPI'];
        $array['UF_ICMS_Substituicao_Devido']      = $mD006['D006_UF_ICMS_Substituicao_Devido'];
        $array['Percentual_Base_Operacao_Propria'] = $mD006['D006_Percentual_Base_Operacao_Propria'];

        // Zera tributos quando for ACP
        if ($Flag_Acp == 3) {
            $array['Aliquota_IPI']                     = 0;
            $array['Aliquota_ICMS']                    = 0;
            $array['Aliquota_ICMS_Oculto']             = 0;
            $array['Percentual_Reducao_ICMS']          = 0;
            $array['Percentual_Reducao_ICMS_Oculto']   = 0;
            $array['Percentual_Reducao_ICMS_ST']       = 0;
            $array['ST_VA']                            = 0;
            $array['Flag_ST']                          = 'N';
            // Comentado Andrey 12/04/2017 para não colocar mais N e manter a origem do produto mesmo quando for acp (o primeiro caracter do Flag_Situacao_Tributaria é a origem da mercadoria)
            //$array['Flag_Situacao_Tributaria']         = 'N';
            $array['ICMS_Inter_Estadual']              = 0;
            $array['ST_ICMS_Interno']                  = 0;
            $array['ST_ICMS_Inter_Estadual_MVA']       = 0;
            $array['ST_ICMS_Interno_MVA']              = 0;
            $array['Flag_Isento_PIS']                  = 'S';
            $array['Aliquota_PIS']                     = 0;
            $array['Flag_Isento_COFINS']               = 'S';
            $array['Aliquota_COFINS']                  = 0;
            $array['Percentual_Reducao_IPI']           = 0;
            $array['Aliquota_ICMS_Credito']            = 0;
            $array['Percentual_Trib_Transparencia']    = 0;
            $array['Aliquota_IRPJ']                    = 0;
            $array['Flag_Isento_IRPJ']                 = 'S';
            $array['Aliquota_CSLL']                    = 0;
            $array['Flag_Isento_CSLL']                 = 'S';
            $array['Percentual_Partilha_ICMS_Destino'] = 0;
            $array['Percentual_Partilha_ICMS_Origem']  = 0;
            $array['Percentual_FCP']                   = 0;
            $array['UF_ICMS_Substituicao_Devido']      = 0;
            $array['UF_ICMS_Substituicao_Devido']      = 0;
        }

        mysql_free_result($Cab);
        mysql_free_result($sqlT004);
        mysql_free_result($sqlT006);
        mysql_free_result($sqlT008);
        mysql_free_result($sqlD009);
        mysql_free_result($D006);
        mysql_free_result($Produto);

        return $array;
    
        
    }
}





