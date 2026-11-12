<?php
namespace hardness;
/**
* Alterar (sobre-escrever) Métodos de uma Classe
*/

class VEN001 extends VEN001_ {
    // defina os métodos para sobreescrever
	
	
	public function xretornaAliquotas($D009_Id, $T003_Id = false, $T005_Id = false, $T007_Id = false, $T004_Id = false, $T006_Id = false, $T008_Id = false, $CFOP = false, $D024_Id_Enviado = false)
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
            $Flag_Contribuinte        = $mCab['T003_Flag_Contribuinte'];
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

        if (!empty($Flag_Origem_Produto)) {
            $mProduto['D049_Origem_Mercadoria'] = $Flag_Origem_Produto;
        }

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
		log("Aliquota IPI: ".$mProduto['Aliquota_IPI']);
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

        mysql_free_result($Cab);
        mysql_free_result($sqlT004);
        mysql_free_result($sqlT006);
        mysql_free_result($sqlT008);
        mysql_free_result($sqlD009);
        mysql_free_result($D006);
        mysql_free_result($Produto);

        return $array;
    
	
	}
    
     /**
     * calcularComissao
     * (T004/T006/T008)
     * @param String $acaoId 
     * @param String $tabela
     * @return array(percentualComissao, valorComissao)
     */    
    public function calcularComissao($acaoId, $tabela)
    {
		/*switch ($tabela) {
            case 'T004':
                $sql = mysql_query("SELECT T004_Valor_Total_Preco as valorTotalPreco, T004_Percentual_Desconto as percentualDesconto FROM T004 WHERE T004_Id = '{$acaoId}'");
            break;
            case 'T006':
                $sql = mysql_query("SELECT T006_Valor_Total_Preco as valorTotalPreco, T006_Percentual_Desconto as percentualDesconto FROM T006 WHERE T006_Id = '{$acaoId}'");
            break;
            case 'T008':
                $sql = mysql_query("SELECT T008_Valor_Total_Preco as valorTotalPreco, T008_Percentual_Desconto as percentualDesconto FROM T008 WHERE T008_Id = '{$acaoId}'");
            break;
        }
        $row = mysql_fetch_array($sql);
        $percentualDesconto = $row['percentualDesconto'];
        $valorTotalPreco    = $row['valorTotalPreco'];
        
        $percentualComissao = (-0.1249 * ($percentualDesconto / 100) + 0.03);
        if ($percentualDesconto<=0){
            $percentualComissao=0.03;
        }
        if ($percentualComissao>0.03){
            $percentualComissao=0.03;
        }elseif($percentualComissao <0.005){
            $percentualComissao=0.005;
        }
        $valorComissao = $valorTotalPreco * $percentualComissao;
        $return = array();
        $return['percentualComissao'] = $percentualComissao;
        $return['valorComissao'] = $valorComissao;*/
		//$retorn=0;
		//return $return;
		
		/* VARIAVEIS DISPONIVEIS PARA CALCULO DE COMISSÃO 
        * percentualComissaoRepresentante (Representante)
        * percentualComissaoExterno (Vendedor Externo)
        * percentualComissaoUsuario (Usuario)
		D009_Preco_1
		D009_Preco_2
		D009_Preco_3
		D009_Preco_4
        */
        switch ($tabela) {
            case 'T004':
                $sql = mysql_query("SELECT T004_Percentual_Desconto as Percentual_Desconto, T003_Flag_Lista_Preco as listaPreco, T004_Valor_Total_Preco as valorTotalProdutos,D009_Preco_1, D009_Preco_2, D009_Preco_3, D009_Preco_4 FROM T004 LEFT JOIN T003 ON T003_Id=T004_T003_Id LEFT JOIN D009 ON D009_Id=T004_D009_Id WHERE T004_Id = '{$acaoId}'");
            break;
            case 'T006':
                $sql = mysql_query("SELECT T006_Percentual_Desconto as Percentual_Desconto, T005_Flag_Lista_Preco as listaPreco, T006_Valor_Total_Preco as valorTotalProdutos,D009_Preco_1, D009_Preco_2, D009_Preco_3, D009_Preco_4 FROM T006 LEFT JOIN T005 ON T005_Id=T006_T005_Id LEFT JOIN D009 ON D009_Id=T006_D009_Id WHERE T006_Id = '{$acaoId}'");
            break;
            case 'T008':
                $sql = mysql_query("SELECT T008_Percentual_Desconto as Percentual_Desconto, T005_Flag_Lista_Preco as listaPreco, T008_Valor_Total_Preco_Sem_Desconto as valorTotalProdutos, D009_Preco_1, D009_Preco_2, D009_Preco_3, D009_Preco_4 FROM T008 LEFT JOIN T007 ON T007_Id=T008_T007_Id LEFT JOIN T005 ON T005_Id=T007_T005_Id LEFT JOIN D009 ON D009_Id=T008_D009_Id WHERE T008_Id = '{$acaoId}'");
            break;
        }
        $row = mysql_fetch_assoc($sql);
		
		// T004_Percentual_Desconto
		// T006_Percentual_Desconto
		// T008_Percentual_Desconto
		$row['percentualComissao'] = (-2.90/35*$row['Percentual_Desconto']+3.30);
		
		/*
		 TERCEIRA TENTATIVA
		*/
		if($row['Percentual_Desconto'] <= 0){
			$row['percentualComissao'] = 3.30;
		} else if($row['Percentual_Desconto'] >= 35){
			$row['percentualComissao'] = 0.40;
		}
		
		/*
		
		------- SEGUNDA TENTATIVA
		
		if($row['valorProdutos'] >= $row['D009_Preco_1']){
			$row['percentualComissao'] = 3.30;
		} else if(($row['valorProdutos'] < $row['D009_Preco_1']) && ($row['valorProdutos'] > $row['D009_Preco_2'])){
			$row['percentualComissao'] = 2.40;
		} else if(($row['valorProdutos'] < $row['D009_Preco_2']) && ($row['valorProdutos'] > $row['D009_Preco_3'])){
			$row['percentualComissao'] = 0.90;
		} else {
			$row['percentualComissao'] = 0.40;
		}
		*/
		/*
		
		----- PRIMEIRA TENTATIVA
		
		if ($row['listaPreco']==1){
		    $row['percentualComissao'] = 3.30;
		} else if ($row['listaPreco']==2){
			$row['percentualComissao'] = 2.00;
		} else if ($row['listaPreco']==3){
			$row['percentualComissao'] = 0.90;
		} else if ($row['listaPreco']==4){
			$row['percentualComissao'] = 0.40;
		}
		*/
		$row['valorComissao'] = $row['valorTotalProdutos'] * ($row['percentualComissao']/100);
		log("chegou: ".$row['valorComissao']);

        $retorno                                    = array();
        $retorno['percentualComissao']              = $row['percentualComissao'];
        $retorno['valorComissao']                   = $row['valorComissao'];
        return $retorno;
    }
	
    /**
     * calcularComissaoTotal
     *
     * @param String $acaoId_Item
     * @param String $tabela
     * @return array(percentualComissao, valorComissao)
     */    
    public function calcularComissaoTotal($acaoId, $tabela)
    {
        /* VARIAVEIS DISPONIVEIS PARA CALCULO DE COMISSÃO 
        * percentualComissaoRepresentante (Representante)
        * percentualComissaoExterno (Vendedor Externo)
        * percentualComissaoUsuario (Usuario)
        */
        switch ($tabela) {
            case 'T003':
				$sql = mysql_query("SELECT SUM(T004_Valor_Comissao) as valorComissao, AVG(T004_Percentual_Comissao) as percentualComissao FROM T004 WHERE T004_T003_Id = '{$acaoId}'");
            break;
            case 'T005':
                $sql = mysql_query("SELECT SUM(T006_Valor_Comissao) as valorComissao, AVG(T006_Percentual_Comissao) as percentualComissao FROM T006 WHERE T006_T005_Id = '{$acaoId}'");
            break;
            case 'T007':
                $sql = mysql_query("SELECT SUM(T008_Valor_Comissao) as valorComissao, AVG(T008_Percentual_Comissao) as percentualComissao FROM T008 WHERE T008_T007_Id = '{$acaoId}'");
            break;
        }
        $row = mysql_fetch_assoc($sql);
		/*
		if ($row['listaPreco']==1){
		    $row['percentualComissao'] = 3.30;
		} else if ($row['listaPreco']==2){
			$row['percentualComissao'] = 2.00;
		} else if ($row['listaPreco']==3){
			$row['percentualComissao'] = 0.90;
		} else if ($row['listaPreco']==4){
			$row['percentualComissao'] = 0.40;
		}
		$row['valorComissao'] = $row['valorTotalProdutos'] * ($row['percentualComissao']/100);
		*/

        $retorno                                    = array();
        $retorno['percentualComissao']              = $row['percentualComissao'];
        $retorno['valorComissao']                   = $row['valorComissao'];
        $retorno['percentualComissaoRepresentante'] = $row['percentualComissaoRepresentante'];
        $retorno['valorComissaoRepresentante']      = $row['valorTotalProdutos'] * ($row['percentualComissaoRepresentante']/100);
        $retorno['percentualComissaoExterno']       = $row['percentualComissaoExterno'];
        $retorno['valorComissaoExterno']            = $row['valorTotalProdutos'] * ($row['percentualComissaoExterno']/100);
        $retorno['percentualComissaoUsuario']       = $row['percentualComissaoUsuario'];
        $retorno['valorComissaoUsuario']            = $row['valorTotalProdutos'] * ($row['percentualComissaoUsuario']/100);
		
        return $retorno;
    }    
    
    public function xmontarEmailPdf($T003_Id, $imprimirHtml = false, $moeda = '1')
    {
        global $g, $confUsuario;
        
        if (!$imprimirHtml) {
            // Arquivo
            $dir = $g['pathDados'] . 'tmp/';
            is_dir($dir) OR mkdir($dir, 0777, true);
            $filename             = "orcamento-{$T003_Id}.pdf";
            $file                 = $dir . $filename;
            $fileFull             = $g['pathRaiz'] . '/' . $file;
            $retorno['Anexos']    = array(
                $file
            );
            $retorno['AnexosWeb'] = array(
                '/hardness3/' . $file
            );
            
            $T003 = <<<EOT
                SELECT * FROM T003 
                LEFT JOIN D013 ON D013_Id=T003_D013_Id
                LEFT JOIN D024 ON D024_Id=T003_D024_Id
                WHERE T003_Id='{$T003_Id}';
EOT;
            
            $T003 = mysql_query($T003);
            $T003 = mysql_fetch_array($T003);
            
            // Array de retorno para criação do e-mail
            $retorno['Para']    = (!empty($T003['D013_E_Mail'])) ? $T003['D013_E_Mail'] : $T003['D024_E_Mail'];
            $retorno['Assunto'] = "Orçamento Nº[{$T003_Id}]";
            
            $retorno['Corpo'] = "<img src='{$caminhoImagem}' style='margin-left:10px'><font size='3'><br /><br /><br /><br /></font><font style='font-size:16px'>Segue orçamento de venda número <b>$T003_Id</b> em anexo.</font><font size='3'><br /><br /></font>";
            
            $r_link = '/ven/ven001/form_func-ajax/ven001ImprimirOrcamento/?ajax=true&gerarHtml=1&T003_Id=' . $T003_Id . '&moeda=' . $moeda; // .'&alcadaUpdate='.$alcadaUpdate;
            $r_link = str_replace(' ', '+', $r_link);
            shell_exec("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
            log("xvfb-run --server-num " . mt_rand(0, 98) . " -s \"-screen 0 640x480x16\" wkhtmltopdf --quiet --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 \"{$g['urlRaiz']}{$r_link}&noEmp=true&p={$g['empresaAtual']}&u={$g['usuarioAtual']}&db={$confUsuario['dbDatabase']}\" \"{$fileFull}\"");
            
            return $retorno;
        }
        
        /**
        * SELECT's
        */
        $C004 = mysql_query("SELECT * FROM C004 WHERE C004_Id = '{$g['empresaAtual']}'");
        $resC004 = mysql_fetch_array($C004);
        $footer = $resC004['C004_Observacao_Rodape_Orcamento'];

        $T003_Id = mysql_real_escape_string($T003_Id);
        $sql = "SELECT * FROM T003 
                   LEFT JOIN D013 ON D013_Id=T003_D013_Id 
                   LEFT JOIN D024 ON D024_Id=T003_D024_Id 
                   LEFT JOIN D020 ON D020_Id=D024_D020_Id
                   LEFT JOIN D018 ON D018_Id=D024_D018_Id
                   LEFT JOIN D021 ON D021_Id=T003_D021_Id
                   LEFT JOIN C007 ON C007_Id=D021_C007_Id 
                   WHERE T003_Id='{$T003_Id}'";
        $T003 = mysql_query($sql);
        $T003 = mysql_fetch_array($T003);

        $sql = <<<EOT
             SELECT * FROM T004 
            LEFT JOIN D009 ON D009_Id=T004_D009_Id 
            LEFT JOIN D049 ON D049_Id=D009_D049_Id
            LEFT JOIN D001 ON D001_Id=D049_D001_Id
            LEFT JOIN D003 ON D003_Id=D001_D003_Id
            LEFT JOIN T144 ON T144_D001_Id=D001_Id
            LEFT JOIN D037 ON D037_Id=T004_D037_Id
            LEFT JOIN D005 ON D005_Id=T004_D005_Id
            WHERE T004_T003_Id = '{$T003_Id}' 
            GROUP BY T004_Id
            ORDER BY T004_ITem
EOT;
        $T004_sql = mysql_query($sql);              
        
        $sql = "SELECT D024_E_Mail FROM D024 WHERE D024_Id='{$T003['T003_D024_Id']}'";
        $D024 = mysql_query($sql);
        $D024 = mysql_fetch_array($D024);   
        
        $sql = "SELECT * FROM D013 WHERE D013_Id='{$T003['T003_D013_Id']}'";
        $D013 = mysql_query($sql);
        $D013 = mysql_fetch_array($D013);
        
        // Array de retorno para criação do e-mail
        $array = array();
        $array['Para'] = (isset($T003['T003_D013_Id'])) ? $T003['D013_E_Mail'] : $D024['D024_E_Mail'];
        $array['Assunto'] = "Orçamento Nº[{$T003_Id}]";
        
        $html = '';
        /**
        * PDF - STYLE
        */
        $html .= <<<EOT
        <style>
            body {
                font-family: Helvetica;
            }
            .header .text {
                font-size: 12px;
            }
            .header .text2 {
                font-size: 12px;
                font-weight: bold;
            }
            .titulo {
                background-color: #E1ECF1;
            }
            .titulo_superior {
                font-size: 14px;
            }
            .itens td {
                border: 1px solid black;
            }
            .textIncluso {
                font-size: 12px;
                color: red;
                background-color: #E1ECF1;
            }
            
        </style>
EOT;
        
        /**
        * PDF - HEADER
        */
        if(!empty($g['c004']['C004_Logo_Marca'])){
            $pathinfo = pathinfo($g['c004']['C004_Logo_Marca']);
            $caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
            $caminhoImagem = "<img src=\"{$caminhoImagem}\">";
        }else{
            $caminhoImagem="";
        }
        if(!empty($T003['D024_Cnpj'])){
            $cpfCnpj = $T003['D024_Cnpj'];
        }else{
            $cpfCnpj = $T003['D024_Cpf'];
        }
        $tel="";
        if(!empty($D013['D013_Telefone_1'])){
            $tel = '('.$D013['D013_DDD_Telefone_1'].') '.$D013['D013_Telefone_1'];
        }else if(!empty($T003['D024_Telefone_1'])){
            $tel = '('.$T003['D024_DDD_Telefone_1'].') '.$T003['D024_Telefone_1'];
        }
        $T003['D024_Nome_Empresa']=substr($T003['D024_Nome_Empresa'],0,60);
        $dataEmissao = gCorrigeData($T003['T003_Data_Emissao']);
        // $html .= <<<EOT
            // <table class="header">
                // <tr>
                    // <td width="150" align="center">{$caminhoImagem}</td>
                    // <td width="40"></td>
                    // <td align="left" class="text" width="330">
                        // <b>{$T003['D024_Id']} - {$T003['D024_Nome_Empresa']}</b><br>
                        // CNPJ: $cpfCnpj<br>
                        // <b>Att: {$D013['D013_Nome_Contato']} - {$tel}</b><br>
                        // {$T003['D024_Endereco']} - {$T003['D024_Numero']} <br>
                        // {$T003['D024_Bairro']} - {$T003['D024_Cep']} - {$T003['D020_Nome_Cidade']} - {$T003['D018_UF']}
                    // </td>
                    // <td align="center" width="150" class="text2">
                        // <br>
                        // ORÇAMENTO<br>Nº {$T003['T003_Id']}<br>{$dataEmissao}
                    // </td>
                // </tr>
            // </table>
            // <br />
// EOT;
        $sqlC004 = "SELECT * FROM C004 left join D018 on D018_Id=C004_D018_Id left join D020 on D020_Id=C004_D020_Id WHERE C004_Id='{$g['empresaAtual']}'";
        $resultC004 = mysql_query($sqlC004);
        $linhaC004 = mysql_fetch_array($resultC004);
        
        $sqlC007 = "SELECT * FROM C007 WHERE C007_Id='{$T003['T003_C007_Id_Vendedor_Interno']}'";
        $resultC007 = mysql_query($sqlC007);
        $linhaC007 = mysql_fetch_array($resultC007);
        
        if(($T003['D024_Cnpj'] != "") || (!empty($T003['D024_Cnpj']))){
            $cpfCnpj = "<b>CNPJ:</b> ".$T003['D024_Cnpj'];
        }else{
            $cpfCnpj = "<b>CPF:</b> ".$T003['D024_Cpf'];
        }
        
        if($T003['D024_Nome_Empresa'] == $T003['D024_Nome_Fantasia']){
            $Nome_Empresa = <<<EOT
            <tr>
                <td class="titulo_superior">{$T003['D024_Nome_Empresa']}</td>
            </tr>
EOT;
        }else{
            $Nome_Empresa = <<<EOT
            <tr>
                <td class="titulo_superior">{$T003['D024_Nome_Empresa']}</td>
            </tr>
            <tr>
                <td class="titulo_superior">{$T003['D024_Nome_Fantasia']}</td>
            </tr>
EOT;
        }
        $T003_Data_Emissao = gCorrigeData($T003['T003_Data_Emissao']);
        $html .=<<<EOT
            <table style="width: 100%">
            <tr>
                <td align="left">{$caminhoImagem}</td>
                <td align="right" style="font-size: 32px"><b>Orçamento</b> Nº {$T003_Id}          </td>
            </tr>
        </table>
        <br />
        <table class="header" width="100%">
            <tr>
                <td style="vertical-align: top; width: 33%">
                    <table align="left"  cellpadding="0">
                        <tr>
                            <td class="titulo_superior"><b><u>Fornecedor</u></b></td>
                        </tr>
                        <tr>
                            <td class="titulo_superior">{$linhaC004['C004_Nome_Empresa']}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior">{$linhaC004['C004_Logradouro']}, {$linhaC004['C004_Numero']} - {$linhaC004['C004_Bairro']}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior"><b>CEP:</b> {$linhaC004['C004_Cep']} {$linhaC004['D020_Nome_Cidade']} - {$linhaC004['D018_UF']}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior"><b>CNPJ:</b> {$linhaC004['C004_Cnpj']}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior"><b>Incr. Estadual:</b> {$linhaC004['C004_Inscricao_Estadual']}</td>
                        </tr>
						<tr>
								<td class="titulo_superior"><b>Telefone:</b> ({$linhaC004['C004_DDD_Telefone']}) {$linhaC004['C004_Telefone']}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior">{$linhaC004['C004_Site']}</td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: top; width: 33%">
                    <table align="left"  cellpadding="0">
                        <tr>
                            <td class="titulo_superior"><b><u>Cliente</u></b></td>
                        </tr>
                        <tr>
                            <td class="titulo_superior"><b>Cód.:</b> {$T003['D024_Id']}</td>
                        </tr>
                        {$Nome_Empresa}
                        <tr>
                            <td class="titulo_superior">{$T003['D024_Endereco']}, {$T003['D024_Numero']} - {$T003['D024_Bairro']}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior"><b>CEP:</b> {$T003['D024_Cep']} {$T003['D020_Nome_Cidade']} - {$T003['D018_UF']}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior">{$cpfCnpj}</td>
                        </tr>
                        <tr>    
                            <td class="titulo_superior"><b>Incr. Estadual:</b> {$T003['D024_Inscricao_Estadual']}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior"><b>Contato: </b>{$D013['D013_Nome_Contato']}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior"><b>Tel. </b>{$tel}</td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: top; width: 33%">
                    <table align="left" cellpadding="0">
                        <tr>
                            <td class="titulo_superior"><b><u>Vendedor</u></b></td>
                        </tr>
                        <tr>
                            <td class="titulo_superior"><b>Nº Orçamento:</b> {$T003_Id}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior"><b>Data:</b> {$T003_Data_Emissao}</td>
                        </tr>
                        <tr>
                            <td class="titulo_superior"><b>Vendedor:</b> {$linhaC007['C007_Primeiro_Nome']}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
EOT;
        
        /**
        * PDF - TABELA PRODUTOS
        */
        $html .= <<<EOT
        <div style="height:20px; width:100%;"></div>
            <table class="itens" cellspacing="0" cellpadding="2" style="width: 100%">
                <tr class="titulo" style="font-size: 13px;">
                    <td width="3%">It</td>
                    <td align="center" width="10%">Código</td>
                    <td align="center" width="10%">Cód. Cli</td>
                    <td width="29%">Descrição</td>
                    <td align="center" width="4%">Und</td>
                    <td align="center" width="8%">NCM</td>
                    <td align="right" width="7%">Quant</td>
EOT;
    //Verifica se existe algum produto com ST
    $sqlVerificaST = mysql_query("SELECT * FROM T004 WHERE T004_T003_Id = '{$T003_Id}' AND T004_Flag_ST='S' GROUP BY T004_Id");
    if(mysql_num_rows($sqlVerificaST) <= 0){
        $html .= <<<EOT
                    <td align="right" width="10%">Unitário R\$</td>
                    <td align="right" width="10%">Total R\$</td>
                    <td align="center" width="4%">IPI</td>
                    <td align="center" width="6%">R$ IPI</td>
                    <!--
                    <td align="right" width="13%">Unit <font style="color:red;"><b>sem</b></font> IPI R\$</td>
                    <td align="right" width="14%">Total <font style="color:red;"><b>com</b></font> IPI R\$</td>
                    -->
EOT;
    }else{
        $html .= <<<EOT
                    <td align="right" width="10%">Unitário R\$</td>
                    <td align="right" width="10%">Total R\$</td>
                    <td align="center" width="4%">IPI</td> 
                    <td align="center" width="6%">R$ IPI</td> 
                    <!--
                    <td align="right" width="13%">Unit <font style="color:red;"><b>sem</b></font> IPI e ST R\$</td>
                    <td align="right" width="14%">Total <font style="color:red;"><b>com</b></font> IPI e ST R\$</td>
                    -->
EOT;
    }
        $html .= <<<EOT
                </tr>
EOT;
        $count = 1;
        $valorTotalProd = 0;
        while($T004 = mysql_fetch_array($T004_sql)){
            $codigo = $T004['T004_Codigo_Produto'];
            $codigoCliente = $T004['T004_Codigo_Substituto'];
            $qtd = gCorrigeNumero($T004['T004_Quantidade']);
            //$unitario = gCorrigeNumero($T004['T004_Valor_Preco_Unitario']);
            //$unitario = gCorrigeNumero($T004['T004_Valor_Preco_Unitario'] + $T004['T004_Valor_IPI']+$T004['T004_Valor_ST']);
            //$total = $T004['T004_Valor_Total_Preco']+$T004['T004_Valor_IPI']+$T004['T004_Valor_ST'];
            $unitario = gCorrigeNumero($T004['T004_Valor_Preco_Unitario']);
            $total = $T004['T004_Valor_Preco_Unitario']*$T004['T004_Quantidade'];
            $valorTotalProd += $total;
            $ipi = number_format($T004['T004_Aliquota_IPI'],0);
            $vlrIpi = gCorrigeNumero($T004['T004_Valor_IPI']);
            $icm = number_format($T004['T004_Aliquota_ICMS'],0);
            $link = '';
            if ($T004["T144_Url"]) {
                $T004["T144_Url"] = strpos($T004["T144_Url"], 'http') !== false ? $T004["T144_Url"] : 'http://' . $T004["T144_Url"];
                $link = "<a href=\"{$T004["T144_Url"]}\">ver produto</a>";
            }
            //if (!empty($T004["T144_Url"]))
            //  $corpo.='<a href="'.$T004["T144_Url"].'" target=_blank style="text-decoration:none;font-weight:bold;color:#000">Ver Produto</a>';
            
            $dirEncode = urlencode($dir);
            $imgLink = "<a target=\"_blank\" href=\"{$confUsuario['urlRaiz']}/sistema/funcoes/imagem/orcamento/?ajax=true&p=1&noEmp=true&T003_Id={$T003_Id}&T004_Id={$T004['T004_Id']}&src={$dirEncode}\">";

            $html .= <<<EOT
            
            <tr style="font-size: 13px">
EOT;
            $D001 = mysql_query("SELECT D001_D003_Id FROM D001 WHERE D001_Id='{$T004['D001_Id']}'");
            //$T004['T004_Descricao_Produto'] = substr($T004['T004_Descricao_Produto'],0,36);
            
            // Contando o número de caracteres da descrição para adicionar br's nos agregados
            $numCaracteres = strlen($T004['T004_Descricao_Produto']);
            if($numCaracteres > 42){
                $br = "<br />";
            }else if($numCaracteres > 84){
                $br = "<br /><br />";
            }else{
                $br = "";
            }
            //$T004['T004_Observacao'] = substr($T004['T004_Observacao'],0,36);
            $T004['T004_Observacao'] = (!empty($T004['T004_Observacao']) ? "<br /><b><i>Obs.: {$T004['T004_Observacao']}</i></b>" : "");                    
            $html .=<<<EOT
                <td align="center">
                    {$T004['T004_Item']}
                </td>
                <td align="center">
                    {$codigo}
                </td>
                <td align="center">
                    {$codigoCliente}
                </td>
                <td align="left">
                    <b>{$T004['T004_Descricao_Produto']}</b>
EOT;
                //Imprime descrição dos agregados dos produtos
                $sqlAgregados = "select * from T202 where T202_T004_Id='{$T004['T004_Id']}' order by T202_Id ASC";
                $resultAgregados = mysql_query($sqlAgregados);
                $totalAgregados = 0;
                while($T202 = mysql_fetch_array($resultAgregados)){
                    $totalAgregados += $T202['T202_Valor_Unitario'];
                    $html .= <<<EOT
                        <br ></EOT>{$T202['T202_Descricao']}
EOT;
                }
                $totalGeral = '';
                $stringTotal = '';
                if(mysql_num_rows($resultAgregados) > 0){
                    $totalGeral = "<br /><b>" . gCorrigeNumero($totalAgregados+$total) . "</b>";
                    $stringTotal = "<br /><b><i>Total</i></b>";
                }
                
                $html .=<<<EOT
                    {$stringTotal}
                    {$T004['T004_Observacao']}
                </td>
                <td>
                    {$T004['D037_Unidade']}
                </td>
                <td>
                    {$T004['D005_Classificacao_Fiscal']}
                </td>
                <td align="right">
                    <b>{$qtd}</b>
                </td>
                <!--<td align="right">
                    <b>{$unitario}</b>
EOT;
                $sqlAgregados = "select * from T202 where T202_T004_Id='{$T004['T004_Id']}' order by T202_Id ASC";
                $resultAgregados = mysql_query($sqlAgregados);
                while($T202 = mysql_fetch_array($resultAgregados)){
                    $T202['T202_Valor_Unitario'] = gCorrigeNumero($T202['T202_Valor_Unitario']);
                    $html .= <<<EOT
                    <br ></EOT>{$T202['T202_Valor_Unitario']}
EOT;
                }
                $total = gCorrigeNumero($total);
                
                $html .= <<<EOT
                {$totalGeral}
                </td>
                -->
                <td align="right">
                    <b>{$unitario}</b>
                </td>
                <td align="right">
                    <b>{$total}</b>
EOT;
                $html .= $br;
                $sqlAgregados = "select * from T202 where T202_T004_Id='{$T004['T004_Id']}' order by T202_Id ASC";
                $resultAgregados = mysql_query($sqlAgregados);
                while($T202 = mysql_fetch_array($resultAgregados)){
                
                $T202['T202_Valor_Unitario'] = gCorrigeNumero($T202['T202_Valor_Unitario']);
                $html .= <<<EOT
                    <br ></EOT>{$T202['T202_Valor_Unitario']}
EOT;
                }
                
                $html .= <<<EOT
                {$totalGeral}
                </td>
                <td align="center">
                    {$ipi}%
                </td>
                <td align="right">
                    {$vlrIpi}
                </td>
            </tr>
EOT;
            $count = $count + 1;
        }
        $html .= '</table>';

        /**
        * PDF - TABELA SERVIÇOS
        */
        $sql = <<<EOT
        SELECT * FROM T145 
        WHERE T145_T003_Id='{$T003_Id}'
EOT;
        $result = mysql_query($sql);
        
        $valorTotalOrcamento = $T003['T003_Valor_Total'];
        if (mysql_num_rows($result) > 0) {
            $html .= <<<EOT
                <br>
                <span align="left">Seguem abaixo os serviços do orçamento:</span>
                <br>
                <table class="itens" cellspacing="0" cellpadding="2">
                    <tr class="titulo">
                        <td align="center" width="10%">Código</td>
                        <td align="center" width="60%">Descrição</td>
                        <td align="right" width="10%">Qte</td>
                        <td align="right" width="20%">Preço</td>
                    </tr>
EOT;
            $valorTotal = 0;
            while($T145 = mysql_fetch_array($result)){
                $titulo = $T145['T145_Titulo'] . " - " . $T145['T145_Descricao'];
                $preco = gCorrigeNumero($T145['T145_Valor_Unitario']);
                $valorTotal += ($T145['T145_Quantidade'] * $T145['T145_Valor_Unitario']);
                $html .= <<<EOT
                <tr>
                    <td align="center">
                        {$T145['T145_Id']}
                    </td>
                    <td align="left">
                        {$titulo}
                    </td>
                    <td align="right">
                        {$T145['T145_Quantidade']}
                    </td>
                    <td align="right">
                        {$preco}
                    </td>
                </tr>
EOT;

            }
            $html .= '</table>';
            $html .= '<span align="left">Valor total dos Serviços: </b>' . gCorrigeNumero($valorTotal) . '</b></span>';
            $valorTotalOrcamento += $valorTotal;
        }
        
        /**
        * PDF - TOTAIS
        */
        //$valorTotalProd = gCorrigeNumero($T003['T003_Valor_Total_Produtos']);
        //$valorTotal = gCorrigeNumero($valorTotalProd+$T003['T003_Frete']+$resultAgregados['Total_Agregados']);
        $valorTotal = $valorTotalProd;
        $ipi = gCorrigeNumero($T003['T003_Valor_Total_IPI']);
        //$valorTotal = $T003['T003_Valor_Total'];
        $T003['T003_Valor_ST'] = gCorrigeNumero($T003['T003_Valor_ST']);
        $valorTotalProd = gCorrigeNumero($valorTotalProd);
        $html .= <<<EOT
            <br><br />
            <table style="text-align: left; font-size: 13px;">
                <tr>
                    <td style="width: 120px">Prazo de Entrega: </td>
                    <td style="width: 500px"><b>{$T003['T003_Prazo_Entrega']}</b></td>
                    <td style="width: 150px">Valor total Produtos: </td>
                    <td style="width: 500px"><b>R$ {$valorTotalProd}</b></td>
                </tr>
EOT;
        $sqlAgregados=mysql_query("SELECT SUM(T202_Valor_Unitario) as Total_Agregados FROM T202 LEFT JOIN T004 ON T004_Id=T202_T004_Id LEFT JOIN T003 ON T003_Id=T004_T003_Id WHERE T003_Id='{$T003_Id}'"); 
        $resultAgregados=mysql_fetch_array($sqlAgregados);
        if($resultAgregados['Total_Agregados'] > 0){
        $valorTotal = $T003['T003_Valor_Total'];
        $valorTotal += $resultAgregados['Total_Agregados'];
        $valorTotal = gCorrigeNumero($valorTotal);
        //$valorTotal = gCorrigeNumero($valorTotalProd+$resultAgregados['Total_Agregados']);
        $resultAgregados['Total_Agregados'] = gCorrigeNumero($resultAgregados['Total_Agregados']);
        $html .= <<<EOT
                <tr>
                    <td style="width: 130px">Frete: </td>
                    <td style="width: 500px"><b>{$T003['T003_Frete']}</b></td>
                    <td style="width: 150px">Valor total Serviços: </td>
                    <td style="width: 500px"><b>R$ {$resultAgregados['Total_Agregados']}</b></td>
                </tr>
                <tr>
                    <td style="width: 130px">Forma pagamento: </td>
                    <td style="width: 500px"><b>{$T003['T003_Forma_Pagamento']}</b></td>
                    <td style="width: 150px">Valor total IPI: </td>
                    <td style="width: 500px"><b>R$ {$ipi}</b></td>
                </tr>
                <tr>
                    <td style="width: 130px">Validade proposta: </td>
                    <td style="width: 500px"><b>{$T003['T003_Validade_Proposta']}</b></td>
                    <td style="width: 150px">Valor frete: </td>
                    <td style="width: 500px"><b>R$ {$T003['T003_Valor_Frete']}</b></td>
                </tr>
                <tr>
                    <td style="width: 130px"></td>
                    <td style="width: 500px"></td>
                    <td style="width: 150px">Valor total: </td>
                    <td style="width: 500px"><b>R$ {$valorTotal}</b></td>
                </tr>
            
EOT;
        }else{
            $valorTotal = gCorrigeNumero($T003['T003_Valor_Total']);
            $T003['T003_Valor_Frete'] = gCorrigeNumero($T003['T003_Valor_Frete']);
            $html .= <<<EOT
                <tr>
                    <td style="width: 130px">Frete: </td>
                    <td style="width: 500px"><b>{$T003['T003_Frete']}</b></td>
                    <td style="width: 150px">Valor total IPI: </td>
                    <td style="width: 500px"><b>R$ {$ipi}</b></td>
                </tr>
                <tr>
                    <td style="width: 130px">Forma pagamento: </td>
                    <td style="width: 500px"><b>{$T003['T003_Forma_Pagamento']}</b></td>     
                    <td style="width: 150px">Valor frete: </td>
                    <td style="width: 500px"><b>R$ {$T003['T003_Valor_Frete']}</b></td>
                </tr>
                <tr>
                    <td style="width: 130px">Validade proposta: </td>
                    <td style="width: 500px"><b>{$T003['T003_Validade_Proposta']}</b></td>
                    <td style="width: 150px">Valor total: </td>
                    <td style="width: 500px"><b>R$ {$valorTotal}</b></td>
                </tr>
EOT;
            
        }
        $html .= "</table>
            <br>";
        if (!empty($T003["T003_Observacao"])) {
            $html .= '<br><span align="left">Observação: <br><b>'.str_replace(chr(10),'<br>',$T003['T003_Observacao']).'</b></span>';
        }
        // Assinatura
        if (!empty($g['c007']['C007_Email_Assinatura']) && trim($g['c007']['C007_Email_Assinatura']) != '<br>') {
            $assinatura = gCorrigeAssinatura($g['c007']['C007_Email_Assinatura']);
            $html .= "<br /><br /><span align=\"left\" style=\"font-size: 14px;\">{$assinatura}</span>";
        }
        return $html;
    }
	
	/**
     * incluirProduto
     *
     * @param Form $form
     * @param String $D009_Id
     * @param String $T003_Id
     * @return String/bool
     */    
    public function incluirProduto($form, $D009_Id = false, $T003_Id = false)
    {
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();
        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();
        global $g, $T004_Id;
        if ($D009_Id > 0) {
            
            /*
            T004_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',1) as Preco_Consultado, 
            T004_Ultimo_Preco(D001_Id,'$mT003[T003_D024_Id]','$T003_Id',2) as Preco_Confirmado 
            */
            
            $sql       = "SELECT T003_D024_Id FROM T003 WHERE T003_Id = '{$T003_Id}'";
            $resultado = mysql_query($sql);
            $rowT003   = mysql_fetch_assoc($resultado);
            
            
            $sql = <<<EOT
            SELECT  *
                    FROM D001
                LEFT JOIN D049 ON D049_D001_Id=D001_Id
                LEFT JOIN D083 ON D083_D001_Id=D001_Id AND D083_D024_Id = '{$rowT003['T003_D024_Id']}'
                LEFT JOIN D009 ON D009_D049_Id=D049_Id
                LEFT JOIN T001 ON T001_Id=D009_T001_Id
                LEFT JOIN D082 ON D082_Id=D049_D082_Id
                LEFT JOIN D041 ON D041_D049_Id=D049_Id
                LEFT JOIN D005 ON D005_Id=D001_D005_Id
                    WHERE D009_Id = '{$D009_Id}'
EOT;
            
            $resultado = mysql_query($sql);
            $row       = mysql_fetch_assoc($resultado);
            
            // Aliquota IPI
            //$row['D005_Aliquota_IPI'] = number_format($row['D005_Aliquota_IPI'],2,',','.');
            $cClausula = " SELECT *, ";
            $cClausula .= " D053_ST_ICMS_Interno, 
                          D053_ST_ICMS_Inter_Estadual, 
                          D053_Flag_ST, 
                          D018_Id, 
                          C004_D018_Id, 
                          C004_CFOP_ST_Venda_Dentro_Estado, 
                          C004_CFOP_ST_Venda_Fora_Estado,
                          C004_CFOP_CONSUMO_Venda_Dentro_Estado,
                          C004_CFOP_CONSUMO_Venda_Fora_Estado,
                          T003_Flag_Lista_Preco,
                          D024_Flag_Contribuinte ";
            $cClausula .= " FROM T003 ";
            $cClausula .= " LEFT JOIN D024 ON D024_Id=T003_D024_Id ";
            $cClausula .= " LEFT JOIN D018 ON D018_Id=D024_D018_Id ";
            $cClausula .= " LEFT JOIN D053 ON D053_D018_Id=D024_D018_Id AND D053_C004_Id=T003_C004_Id ";
            $cClausula .= " LEFT JOIN C004 ON C004_Id=T003_C004_Id ";
            $cClausula .= " WHERE T003_Id='{$T003_Id}'";
            $T003  = mysql_query($cClausula);
            $cErro = mysql_error();
            if (!empty($cErro)) {
                return "$cErro<br ></EOT>$cClausula";
                exit;
            }
            $mT003 = mysql_fetch_array($T003);
            
            /*
            $sql = <<<EOT
            SELECT 
            D009_Id, 
            D009_IPI('{$mT003['T003_C004_Id']}',D009_Id,'{$mT003['T003_D024_Id']}','{$mT003['T003_D006_Id']}','1','{$mT003['T003_Flag_Acp']}') as Aliquota_IPI 
            FROM 
            D009 
            WHERE D009_Id='{$D009_Id}'
            EOT;
            $result = mysql_query($sql);
            $row2 = mysql_fetch_assoc($result);
            */
            
            /**
             * Os campos que estão atribuidos o valor zero que é não são selecionados nos selects acima.
             */
            $T004_D009_Id              = $D009_Id;
            $T004_Quantidade           = 0;
            $T004_Valor_Preco_Unitario = $Preco_Tabelado;
            $T004_D037_Id              = $row['D001_D037_Id'];
            $T004_Descricao_Produto    = mysql_real_escape_string($row['D001_Descricao_Produto']);
            $T004_Codigo_Produto       = $row['D001_Codigo_Produto'];
            $T004_IPV                  = "";
            $T003_Id;
            $T004_Codigo_Substituto       = $row['D083_Codigo_Produto_Fornecedor'];
            $T004_Numero_Pedido_Compra    = '';
            $T004_Item_Pedido_Compra      = 'NULL';
            $T004_Observacao              = '';
            $T004_Observacao_Entrega      = $form->campoValorEnviado('T004_Observacao_Entrega');
            $T004_Aliquota_IPI            = $row['Aliquota_IPI'];
            $idVeiculoOrcamento           = 0;
            $T004_Id                      = '';
            $T004_Percentual_Desconto     = 0;
            $T004_Valor_Desconto_Unitario = 0;
            $T004_Quantidade              = gCorrigeNumeroInverte($T004_Quantidade);
            $T004_Valor_Preco_Unitario    = gCorrigeNumeroInverte($T004_Valor_Preco_Unitario);
        } else {
            
            /**
             * ATENÇÃO!!!
             * 
             * Sempre que for utlizar a variável $form, é necessário adicioná-la dentro desse else
             * esse método de incluir produto no orçamento é usado também quando o orçamento é gerado
             * via catálogo no CRM, e nesse caso, nunca teremos a variável form e não irá funcionar a inclusão
             * dos itens via tab CATÁLOGO
             */
            
            $T004_D009_Id                 = $form->campoValorEnviado('T004_D009_Id');
            $T004_D144_Id                 = $form->campoValorEnviado('T004_D144_Id');
            $T004_Quantidade              = $form->campoValorEnviado('T004_Quantidade');
            $T004_Valor_Preco_Unitario    = $form->campoValorEnviado('T004_Valor_Preco_Sem_Desconto_Unitario');
            $T004_D037_Id                 = $form->campoValorEnviado('T004_D037_Id');
            $T004_Descricao_Produto       = mysql_real_escape_string($form->campoValorEnviado('T004_Descricao_Produto'));
            $T004_Codigo_Produto          = $form->campoValorEnviado('T004_Codigo_Produto');
            $T004_IPV                     = $form->campoValorEnviado('T004_IPV');
            $T004_D006_Id                 = $form->campoValorEnviado('T004_D006_Id');
            $T003_Id                      = $form->campoValorEnviado('T004_T003_Id');
            $T004_Codigo_Substituto       = $form->campoValorEnviado('T004_Codigo_Substituto');
            $T004_Numero_Pedido_Compra    = $form->campoValorEnviado('T004_Numero_Pedido_Compra');
            $T004_Item_Pedido_Compra      = $form->campoValorEnviado('T004_Item_Pedido_Compra');
            $T004_Item_Pedido_Compra      = (empty($T004_Item_Pedido_Compra) ? 'NULL' : $T004_Item_Pedido_Compra);
            $T004_Observacao              = $form->campoValorEnviado('T004_Observacao');
            $T004_Observacao_Entrega      = $form->campoValorEnviado('T004_Observacao_Entrega');
            $T004_Aliquota_IPI            = $form->campoValorEnviado('T004_Aliquota_IPI');
            $idVeiculoOrcamento           = $form->campoValorEnviado('idVeiculoOrcamento');
            $T004_D005_Id                 = $form->campoValorEnviado('T004_D005_Id');
            $T004_Percentual_Desconto     = gCorrigeNumeroInverte($form->campoValorEnviado('T004_Percentual_Desconto'));
            $T004_Id                      = '';
            $T004_Flag_Destino_Produto    = $form->campoValorEnviado('T004_Flag_Destino_Produto');
            //$NcmProduto=$form->campoValorEnviado('NcmProduto');
            //$T004_Percentual_Desconto=0;
            $T004_Valor_Desconto_Unitario = 0;
            
            $T004_Quantidade           = gCorrigeNumeroInverte($T004_Quantidade);
            $T004_Valor_Preco_Unitario = gCorrigeNumeroInverte($T004_Valor_Preco_Unitario);
            
            //$Aliquotas = $this->retornaAliquotas($D009_Id, $T003_Id, false);
        }
        
        $D001  = mysql_query("SELECT D001_Id,
                                  D082_Id,
                                  D001_Codigo_Produto,
                                  D049_Origem_Mercadoria,
                                  D001_Flag_Tipo,
                                  D049_Flag_Nacional_Importado,
                                  D001_Cubagem_Unitaria,
                                  T001_Id
                             FROM D001
                        LEFT JOIN D049 ON D049_D001_Id=D001_Id
                        LEFT JOIN D009 ON D009_D049_Id=D049_Id
                        LEFT JOIN T001 ON T001_Id=D009_T001_Id
                        LEFT JOIN D082 ON D082_Id=D049_D082_Id
                            WHERE D009_Id='$T004_D009_Id'");
        $mD001 = mysql_fetch_array($D001);
        
        $T004_D001_Id        = $mD001['D001_Id'];
        $T004_Codigo_Produto = $mD001['D001_Codigo_Produto'];
        
        $C007  = mysql_query("SELECT *
                             FROM C007
                            WHERE C007_Id={$g['usuarioAtual']}");
        $mC007 = mysql_fetch_array($C007);
        
        $T004  = mysql_query("SELECT T004_Item
                                      FROM T004
                                     WHERE T004_T003_Id='$T003_Id'
                                  order by T004_Item desc
                                     limit 1");
        $mT004 = mysql_fetch_array($T004);
        
        $T004_Item = $mT004['T004_Item'] + 1;
        
        $cClausula = " SELECT ";
        $cClausula .= " D009_Id, ";
        $cClausula .= " D049_Id, ";
        $cClausula .= " D001_Id, ";
        $cClausula .= " D005_Flag_ST, ";
        $cClausula .= " D009_Valor_Custo_Unitario, ";
        $cClausula .= " IFNULL(D009_Flag_Preco_Tabelado,'') as D009_Flag_Preco_Tabelado, ";
        $cClausula .= " D049_Origem_Mercadoria, ";
        $cClausula .= " D009_Origem_Mercadoria, ";
        $cClausula .= " D001_Flag_Pre_Cadastro ";
        $cClausula .= " FROM D001 ";
        $cClausula .= " LEFT JOIN D049 ON D001_Id=D049_D001_Id ";
        $cClausula .= " LEFT JOIN D009 ON D049_Id=D009_D049_Id ";
        $cClausula .= " LEFT JOIN D005 ON D005_Id=D001_D005_Id ";
        $cClausula .= " WHERE D009_Id='$T004_D009_Id' ";
        $T004  = mysql_query($cClausula);
        $mT004 = mysql_fetch_array($T004);
        
        //Caso a origem da mercadoria esteja preenchida no D009, utilizamos este campo
        if($mT004['D009_Origem_Mercadoria'] != ''){
            $mT004['D049_Origem_Mercadoria'] = $mT004['D009_Origem_Mercadoria'];
        }

        $erro = mysql_error();
        if (!empty($erro) && !$D009_Id) {
            return "Erro consulta custo produto: $erro";
        }
        
        if (($mT004['D009_Id']) <= 0) {
            return "Erro: Produto não encontrado";
        }
        
        // verifica qual preço usar, baseado na lista preço do orçamento ou do cliente
        $listPreco    = mysql_query("SELECT T003_Flag_Lista_Preco, T003_D024_Id, T003_C004_Id, T003_Flag_ACP, T003_C004_Id_Estoque FROM T003 WHERE T003_Id='{$T003_Id}'");
        $mLista_Preco = mysql_fetch_array($listPreco);
        $D024_Id      = $mLista_Preco['T003_D024_Id'];
        
        $cfop = $this->retornaCFOP($mLista_Preco['T003_D024_Id'], $T004_D009_Id, $mLista_Preco['T003_C004_Id'], $T004_Flag_Destino_Produto, false, false, false, $mLista_Preco['T003_C004_Id_Estoque']);
        $demonstrativoCFOP = $this->retornaCFOP($mLista_Preco['T003_D024_Id'], $T004_D009_Id, $mLista_Preco['T003_C004_Id'], $T004_Flag_Destino_Produto, '', true, false, $mLista_Preco['T003_C004_Id_Estoque']);

        $T004_D006_Id = $cfop;
        
        $Aliquotas = $this->retornaAliquotas($T004_D009_Id, $T003_Id, false, false, false, false, false, $cfop);
        
        $demonstrativoT001 = $mD001['T001_Id'];
        //////////////////////////////////////////////////////////////////////////////////////
        // Método retornaPrecoVenda atualizado com demonstrativo unico
        $dadosPrecoVenda         = $CAD002->retornaPrecoVenda($mT004['D009_Id'], $mLista_Preco['T003_Flag_Lista_Preco'], $D024_Id, false, $cfop, $Aliquotas['Aliquota_ICMS'], false, false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
        $Preco_Tabelado          = gCorrigeNumeroInverte($dadosPrecoVenda[0]);
        $demonstrativoPrecoVenda = mysql_real_escape_string(serialize($dadosPrecoVenda[1]));
        $custoUnitario           = $dadosPrecoVenda[4];


        if ($T004_Aliquota_IPI > 0 || $Aliquotas['Aliquota_IPI']) {
            $D088_Codigo_ST = '50';
        } else {
            $D088_Codigo_ST = '53';
        }
        
        if ($T004_Percentual_Desconto > 0) {            
            $T004_Valor_Preco_Unitario = $T004_Valor_Preco_Unitario - ($T004_Valor_Preco_Unitario * ($T004_Percentual_Desconto / 100));            
            if ($Preco_Tabelado > 0) {
                $T004_Percentual_Desconto     = ((1 - ($T004_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $T004_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T004_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $T004_Valor_Preco_Unitario;
            }            
        } else {            
            if ($Preco_Tabelado > 0) {
                $T004_Percentual_Desconto     = ((1 - ($T004_Valor_Preco_Unitario / $Preco_Tabelado)) * 100);
                $T004_Valor_Desconto_Unitario = (($Preco_Tabelado * ($T004_Valor_Preco_Unitario / 100)));
            } else {
                $Preco_Tabelado = $T004_Valor_Preco_Unitario;
            }            
        }
        
        // Buscando o percentual de tributos (lei transparências)           
        $D149  = mysql_query("SELECT D149_Percentual_Trib_Transparencia FROM D149 WHERE D149_Id='{$Aliquotas['D149_Id']}'");
        $mD149 = mysql_fetch_array($D149);

        $D006  = mysql_query("SELECT D006_Percentual_Trib_Transparencia, D088_Codigo_ST FROM D006 LEFT JOIN D088 ON D088_Id=D006_D088_Id WHERE D006_Id='{$T004_D006_Id}'");
        $mD006 = mysql_fetch_array($D006);
        $D088_Codigo_ST = $mD006['D088_Codigo_ST'];

        if (!empty($mD149['D149_Percentual_Trib_Transparencia']) AND $mD149['D149_Percentual_Trib_Transparencia'] > 0) {
            $T004_Percentual_Trib_Transparencia = $mD149['D149_Percentual_Trib_Transparencia'];
        } else if (!empty($mD006['D006_Percentual_Trib_Transparencia']) AND $mD006['D006_Percentual_Trib_Transparencia'] > 0) {
            $T004_Percentual_Trib_Transparencia = $mD006['D006_Percentual_Trib_Transparencia'];
        } else {
            $T004_Percentual_Trib_Transparencia = 0;
        }

        if ($Aliquotas['D005_Id'] == '') {
            $demonstrativoNCM = $FIS003->demonstrativoNCM($T004_D005_Id);
        } else {
            $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
        }

        //////////////////////////////////////////////////////////////////////////////////////
        //Métodos que montam os demonstrativos do preço do histórico e da tabela do fornecedor
        $precoTabela                      = $CAD002->cad002RetornaPrecoTabela($mT004['D009_Id'], $mLista_Preco['T003_Flag_Lista_Preco'], $D024_Id, $mLista_Preco['T003_Flag_ACP'], $cfop, $Aliquotas['Aliquota_ICMS'], $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI']);
        $demonstrativoPrecoTabela         = $precoTabela[1];
        $valorPrecoTabela                 = gCorrigeNumeroInverte($precoTabela[0]);

        $precoCompra                      = $CAD002->cad002RetornaPrecoUltimaCompra($mT004['D009_Id'], $mLista_Preco['T003_Flag_Lista_Preco'], $D024_Id, $mLista_Preco['T003_Flag_ACP'], $cfop, $Aliquotas['Aliquota_ICMS'], 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI']);
        $demonstrativo                    = $precoCompra[1];
        $valorPrecoCompra                 = gCorrigeNumeroInverte($precoCompra[0]);

        $demonstrativoCustoTabela         = $CAD002->cad002demonstrativoCustoTabela($mT004['D009_Id']);


        $sqlD009Transf = mysql_query("SELECT D009_Id FROM D009 WHERE D009_D049_Id='{$mT004['D049_Id']}' AND D009_C004_Id = '{$g['C031']['C004PrecoTransferenciaGridProdutos']}'");
        $resD009Transf = mysql_fetch_array($sqlD009Transf);
        $dadosPrecoTransferencia          = $CAD002->cad002RetornaPrecoTransferencia($resD009Transf['D009_Id'], $g['C031']['C004PrecoTransferenciaGridProdutos'], $g['C004']['C004_D024_Id'], $T003_Id);
        $valorPrecoTransferencia          = gCorrigeNumeroInverte($dadosPrecoTransferencia[0]);
        $demonstrativoPrecoTransferencia  = $dadosPrecoTransferencia[1];
        $valorCustoTransferencia          = $dadosPrecoTransferencia[4];
        $origemProdutoTransferencia       = $dadosPrecoTransferencia[5];
        // Demonstrativo
        $demonstrativo = mysql_real_escape_string(serialize($demonstrativo));
        $demonstrativoPrecoTabela = mysql_real_escape_string(serialize($demonstrativoPrecoTabela));
        $demonstrativoPrecoTransferencia = mysql_real_escape_string(serialize($demonstrativoPrecoTransferencia));
        $demonstrativoCFOP = mysql_real_escape_string($demonstrativoCFOP);
        $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);
        $demonstrativoCustoTabela = mysql_real_escape_string($demonstrativoCustoTabela);

        $cClausula = " insert into T004 (";
        $cClausula .= " T004_C004_Id, ";
        $cClausula .= " T004_Item, ";
        $cClausula .= " T004_Codigo_Produto, ";
        $cClausula .= " T004_Codigo_Substituto, ";
        $cClausula .= " T004_Descricao_Produto, ";
        $cClausula .= " T004_T003_Id, ";
        $cClausula .= " T004_D024_Id, ";
        $cClausula .= " T004_D009_Id, ";
        $cClausula .= " T004_D037_Id, ";
        $cClausula .= " T004_D082_Id, ";
        $cClausula .= " T004_Quantidade, ";
        $cClausula .= " T004_Valor_Custo_Unitario, ";
        $cClausula .= " T004_Valor_Preco_Unitario, ";
        $cClausula .= " T004_Valor_Preco_Sem_Desconto_Unitario, ";
        $cClausula .= " T004_Valor_Preco_Original, ";
        $cClausula .= " T004_Valor_Preco_Original_Bruto, ";
        $cClausula .= " T004_Percentual_Desconto, ";
        $cClausula .= " T004_Valor_Desconto_Unitario, ";
        $cClausula .= " T004_Percentual_Desconto_Bruto, ";
        $cClausula .= " T004_Valor_Desconto_Unitario_Bruto, ";
        $cClausula .= " T004_IPV, ";
        $cClausula .= " T004_Peso_Unitario, ";
        $cClausula .= " T004_Peso_Bruto, ";
        $cClausula .= " T004_D005_Id,";
        $cClausula .= " T004_Classificacao_Fiscal,";
        $cClausula .= " T004_D006_Id,";
        $cClausula .= " T004_Aliquota_IPI,";
        $cClausula .= " T004_Aliquota_ICMS,";
        $cClausula .= " T004_Percentual_Reducao_ICMS,";
        $cClausula .= " T004_Percentual_Reducao_ICMS_ST,";
        $cClausula .= " T004_ST_VA,";
        $cClausula .= " T004_Flag_ST,";
        $cClausula .= " T004_Flag_Situacao_Tributaria,";
        $cClausula .= " T004_ICMS_Inter_Estadual,";
        $cClausula .= " T004_ST_ICMS_Interno,";
        $cClausula .= " T004_ICMS_Inter_Estadual_MVA,";
        $cClausula .= " T004_ST_ICMS_Interno_MVA,";
        $cClausula .= " T004_Flag_Isento_PIS,";
        $cClausula .= " T004_Aliquota_PIS,";
        $cClausula .= " T004_Situacao_Tributaria_PIS,";
        $cClausula .= " T004_Flag_Isento_COFINS,";
        $cClausula .= " T004_Aliquota_COFINS, ";
        $cClausula .= " T004_Flag_Preco_Especial, ";
        $cClausula .= " T004_Situacao_Tributaria_COFINS, ";
        $cClausula .= " T004_Situacao_Tributaria_IPI, ";
        $cClausula .= " T004_D059_Id, ";
        $cClausula .= " T004_Observacao, ";
        $cClausula .= " T004_Numero_Pedido_Compra, ";
        
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " T004_Item_Pedido_Compra, ";
            $cClausula .= " T004_Valor_Preco_Bruto, ";
        } else {
            $cClausula .= " T004_Item_Pedido_Compra, ";
        }
        $cClausula .= " T004_Cubagem_Unitaria, ";
        $cClausula .= " T004_Percentual_Trib_Transparencia, ";
        $cClausula .= " T004_Flag_Pre_Cadastro, ";
        $cClausula .= " T004_D144_Id, ";
        $cClausula .= " T004_Flag_Origem_Produto, ";
        $cClausula .= " T004_Demonstrativo_Preco, ";
        $cClausula .= " T004_Demonstrativo_Preco_Tabela, ";
        $cClausula .= " T004_Demonstrativo_Preco_Transferencia, ";
        $cClausula .= " T004_Valor_Preco_Compra, ";
        $cClausula .= " T004_Valor_Preco_Tabela, ";
        $cClausula .= " T004_Valor_Preco_Transferencia, ";
        $cClausula .= " T004_Valor_Custo_Transferencia, ";
        $cClausula .= " T004_Flag_Origem_Produto_Transferencia, ";
        $cClausula .= " T004_T001_Id, ";
        $cClausula .= " T004_Observacao_Entrega, ";
        $cClausula .= " T004_Flag_Destino_Produto, ";
        $cClausula .= " T004_D006_Id_Info, ";
        $cClausula .= " T004_D005_Id_Info, ";
        $cClausula .= " T004_Demonstrativo_Custo_Tabela, ";
        $cClausula .= " T004_Demonstrativo_Preco_Venda, ";
        $cClausula .= " T004_Aliquota_IRPJ, ";
        $cClausula .= " T004_Flag_Isento_IRPJ,";
        $cClausula .= " T004_Aliquota_CSLL, ";
        $cClausula .= " T004_Flag_Isento_CSLL, ";
        $cClausula .= " T004_Percentual_Base_Operacao_Propria, ";
        $cClausula .= " T004_UF_ICMS_Substituicao_Devido ";
        $cClausula .= " ) VALUES ( ";
        $cClausula .= " '{$g['empresaAtual']}', ";
        $cClausula .= " '{$T004_Item}', ";
        $cClausula .= " '{$T004_Codigo_Produto}', ";
        $cClausula .= " '{$T004_Codigo_Substituto}', ";
        $cClausula .= " '{$T004_Descricao_Produto}', ";
        $cClausula .= " '{$T003_Id}', ";
        $cClausula .= " '{$D024_Id}', ";
        $cClausula .= " '{$T004_D009_Id}', ";
        $cClausula .= " '{$T004_D037_Id}', ";
        $cClausula .= " '{$Aliquotas['D082_Id']}', ";
        $cClausula .= " '{$T004_Quantidade}', ";
        $cClausula .= " '{$custoUnitario}', ";
        $cClausula .= " '{$T004_Valor_Preco_Unitario}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$Preco_Tabelado}', ";
        $cClausula .= " '{$T004_Percentual_Desconto}', ";
        $cClausula .= " '{$T004_Valor_Desconto_Unitario}', ";
        $cClausula .= " '{$T004_Percentual_Desconto}', ";
        $cClausula .= " '{$T004_Valor_Desconto_Unitario}', ";
        $cClausula .= " '{$T004_Valor_Preco_Unitario}/{$mT004['D009_Valor_Custo_Unitario']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Kg']}', ";
        $cClausula .= " '{$Aliquotas['D001_Peso_Unitario_Bruto']}', ";
        if ($Aliquotas['D005_Id'] == '') {
            $cClausula .= " '{$T004_D005_Id}',";
        } else {
            $cClausula .= " '{$Aliquotas['D005_Id']}',";
        }
        $cClausula .= " '{$Aliquotas['Classificacao_Fiscal']}',";
        $cClausula .= " '{$T004_D006_Id}',";
        //$cClausula .= " '{$mT004['Aliquota_IPI']}',";
        if ($T004_Aliquota_IPI <= 0) {
            $cClausula .= " '{$Aliquotas['Aliquota_IPI']}',";
        } else {
            $cClausula .= " '{$T004_Aliquota_IPI}',";
        }
        $cClausula .= " '{$Aliquotas['Aliquota_ICMS']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Reducao_ICMS']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Reducao_ICMS_ST']}',";
        $cClausula .= " '{$Aliquotas['ST_VA']}',";
        $cClausula .= " '{$Aliquotas['Flag_ST']}',";
        $cClausula .= " '{$Aliquotas['Flag_Situacao_Tributaria']}',";
        $cClausula .= " '{$Aliquotas['ICMS_Inter_Estadual']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Interno']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',";
        $cClausula .= " '{$Aliquotas['ST_ICMS_Interno_MVA']}',";
        $cClausula .= " '{$Aliquotas['Flag_Isento_PIS']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_PIS']}',";
        $cClausula .= " '{$Aliquotas['Situacao_Tributaria_PIS']}',";
        $cClausula .= " '{$Aliquotas['Flag_Isento_COFINS']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_COFINS']}', ";
        $cClausula .= " '',";
        $cClausula .= " '{$Aliquotas['Situacao_Tributaria_COFINS']}',";
        $cClausula .= " '{$D088_Codigo_ST}',";
        $cClausula .= " '{$Aliquotas['D059_Id']}',";
        $cClausula .= " '$T004_Observacao', ";
        $cClausula .= " '$T004_Numero_Pedido_Compra', ";
        if ($g['C031']['precopadraoVendaProduto'] == 'bruto') {
            $cClausula .= " '{$T004_Item_Pedido_Compra}', ";
            $cClausula .= " '{$T004_Valor_Preco_Unitario}', ";
        } else {
            $cClausula .= " '{$T004_Item_Pedido_Compra}', ";
        }
        $cClausula .= " '{$mD001['D001_Cubagem_Unitaria']}', ";
        $cClausula .= " '{$T004_Percentual_Trib_Transparencia}', ";
        $cClausula .= " '{$mT004['D001_Flag_Pre_Cadastro']}', ";
        $cClausula .= " '{$T004_D144_Id}', ";
        $cClausula .= " '{$mT004['D049_Origem_Mercadoria']}', ";
        $cClausula .= " '{$demonstrativo}', ";
        $cClausula .= " '{$demonstrativoPrecoTabela}', ";
        $cClausula .= " '{$demonstrativoPrecoTransferencia}', ";
        $cClausula .= " '{$valorPrecoCompra}', ";
        $cClausula .= " '{$valorPrecoTabela}', ";
        $cClausula .= " '{$valorPrecoTransferencia}', ";
        $cClausula .= " '{$valorCustoTransferencia}', ";
        $cClausula .= " '{$origemProdutoTransferencia}', ";
        $cClausula .= " '{$demonstrativoT001}', ";
        $cClausula .= " '{$T004_Observacao_Entrega}', ";
        $cClausula .= " '{$T004_Flag_Destino_Produto}', ";
        $cClausula .= " '{$demonstrativoCFOP}', ";
        $cClausula .= " '{$demonstrativoNCM}', ";
        $cClausula .= " '{$demonstrativoCustoTabela}', ";
        $cClausula .= " '{$demonstrativoPrecoVenda}', ";
        $cClausula .= " '{$Aliquotas['Aliquota_IRPJ']}', ";
        $cClausula .= " '{$Aliquotas['Flag_Isento_IRPJ']}',";
        $cClausula .= " '{$Aliquotas['Aliquota_CSLL']}', ";
        $cClausula .= " '{$Aliquotas['Flag_Isento_CSLL']}',";
        $cClausula .= " '{$Aliquotas['Percentual_Base_Operacao_Propria']}',";
        $cClausula .= " '{$Aliquotas['UF_ICMS_Substituicao_Devido']}'";
        $cClausula .= " ); ";
        mysql_query($cClausula);
        $T004_Id = $g['mysqlLastId'];
        $cErro   = mysql_error();
        if (!empty($cErro)) {
            return "Erro: $cErro";
        } else {

            if (!empty($T004_Codigo_Substituto)) {
                /*
                $D083 = mysql_query("SELECT D083_Id,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor FROM D083 WHERE D083_D001_Id='{$mT004['D001_Id']}' AND D083_D049_Id='{$mT004['D049_Id']}' AND D083_D024_Id='$D024_Id'");
                if (mysql_num_rows($D083) <= 0) {
                    mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente,D083_Codigo_Produto_Fornecedor) VALUES ('{$mT004['D049_Id']}','{$mT004['D001_Id']}','$D024_Id',curdate(),'S','$T004_Codigo_Substituto')");
                } else {
                    $mD083 = mysql_fetch_array($D083);
                    mysql_query("UPDATE D083 SET D083_Flag_Cliente='S',D083_Codigo_Produto_Fornecedor='$T004_Codigo_Substituto' WHERE D083_Id='{$mD083['D083_Id']}'");
                }
                */
                require_once("bibliotecas/classes/CAD002.php");
                $CAD002 = new CAD002();
                $CAD002->definirCodigoFornecedor($mT004['D009_Id'], $D024_Id, $T004_Codigo_Substituto, '', true, false);
            }
            //Atualiza as aliquotas com o D006 correto do T004
            $this->atualizarAliquotas($T003_Id, false, $T004_Id);
            $cSql = mysql_query("call T004_Gravar_Totalizacao_4('$T004_Id');");
            $cSql = mysql_query("call T003_Gravar_Totalizacao_4('$T003_Id');");  
        }

        // caso o produto possua serviços associados, insere estes serviços na T145
        $result  = mysql_query("SELECT D001_Id FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id WHERE D009_Id='{$T004_D009_Id}'");
        $retorno = mysql_fetch_array($result);
        $D001_Id = $retorno['D001_Id'];
        $result  = mysql_query("SELECT * FROM D135 LEFT JOIN D112 ON D112_Id=D135_D112_Id WHERE D135_D001_Id='{$D001_Id}'");
        
        if (mysql_num_rows($result) > 0) {
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");
            while ($D135 = mysql_fetch_array($result)) {
                $cClausula = " insert into T145 (";
                $cClausula .= " T145_T003_Id, ";
                $cClausula .= " T145_D112_Id, ";
                $cClausula .= " T145_D110_Id, ";
                $cClausula .= " T145_T004_Id, ";
                $cClausula .= " T145_Titulo, ";
                $cClausula .= " T145_Descricao, ";
                $cClausula .= " T145_Tempo_Estimado, ";
                $cClausula .= " T145_Quantidade, ";
                $cClausula .= " T145_Quantidade_Confirmada, ";
                $cClausula .= " T145_Valor_Unitario, ";
                $cClausula .= " T145_Valor_Total, ";
                $cClausula .= " T145_D116_Id ";
                $cClausula .= " ) VALUES ( ";
                $cClausula .= " '$T003_Id', ";
                $cClausula .= " '{$D135['D135_D112_Id']}', ";
                $cClausula .= " '{$D135['D112_D110_Id']}', ";
                $cClausula .= " '$T004_Id', ";
                $cClausula .= " '{$D135['D112_Titulo']}', ";
                $cClausula .= " '{$D135['D112_Descricao']}', ";
                $cClausula .= " '{$D135['D112_Tempo_Estimado']}', ";
                $cClausula .= " '1', ";
                $cClausula .= " '1', ";
                $cClausula .= " '{$D135['D112_Valor_Unitario']}', ";
                $cClausula .= " '{$D135['D112_Valor_Unitario']}', ";
                $cClausula .= " '{$idVeiculoOrcamento}' ";
                $cClausula .= " ) ";
                mysql_query($cClausula);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'falha ao copiar os serviços do produto';
                }
                
                $T145_Id = $g['mysqlLastId'];
                
                $sql   = "SELECT * FROM D113 WHERE D113_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    // D113_D112_Id, D113_D110_Id, D113_Titulo, D113_Descricao
                    $sql = "INSERT INTO T146 (T146_T145_Id, T146_Titulo, T146_Descricao) VALUES ('" . mysql_real_escape_string($T145_Id) . "', '"
                    //.mysql_real_escape_string($row['D113_D110_Id'])."', '"
                        . mysql_real_escape_string($row['D113_Titulo']) . "', '" . mysql_real_escape_string($row['D113_Descricao']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
                
                $sql   = "SELECT * FROM D114 WHERE D114_D112_Id='{$D135['D135_D112_Id']}'";
                $array = mysql_query($sql);
                
                while ($row = mysql_fetch_assoc($array)) {
                    $sql = "INSERT INTO T152 (T152_D037_Id, T152_D009_Id, T152_T145_Id, T152_Quantidade) VALUES ('" . mysql_real_escape_string($row['D114_D037_Id']) . "', '" . mysql_real_escape_string($row['D114_D009_Id']) . "', '" . mysql_real_escape_string($T145_Id) . "', '" . mysql_real_escape_string($row['D114_Quantidade']) . "');";
                    mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return 'Erro: ' . mysql_error();
                    }
                }
            }
            
            mysql_query("COMMIT");
        }
        
        mysql_query("UPDATE T004 SET T004_Valor_Preco_Bruto=T004_Valor_Preco_Bruto WHERE T004_Id='$T004_Id'");
        
        return true;
    
		
	}
	
	/**
     * gerarPedido
     *
     * @param String $T003_Id
     * @return String/bool
     */
    public function gerarPedido($T003_Id)
    {
        global $g, $T005_Id;
        $T003_Id = (isset($T003_Id)) ? $T003_Id : false;

        //$T003_D024_Id;
        //$T003_Data_Emissao;
        // inserir um novo registro na T005 levando: T005_T003_Id, e os dois acima
        if ($T003_Id) {
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query("START TRANSACTION");

            // Verificando se os T006 vinculados ao T004 possuem um T005
            $T004=mysql_query("SELECT T006_Id,T006_T005_Id,T006_T004_Id FROM T004 LEFT JOIN T006 ON T006_T004_Id=T004_Id WHERE T004_T003_Id ='{$T003_Id}'");
            while ($mT004 = mysql_fetch_array($T004)) {
                if($mT004['T006_Id'] > 0){
                    $T005=mysql_query("SELECT T005_Id FROM T005 WHERE T005_Id='{$mT004['T006_T005_Id']}'");
                    $obs="T006_T004_Id: ".$mT004['T006_T004_Id'];
                    if(mysql_num_rows($T005)==0){mysql_query("UPDATE T006 set T006_Observacao='{$obs}', T006_T004_Id='' WHERE T006_Id='{$mT004['T006_Id']}'");}
                }
            }
            
            $sql2   = "SELECT SUM(T004_Quantidade_Confirmada) FROM T004 LEFT JOIN T006 ON T006_T004_Id=T004_Id WHERE T004_T003_Id ='$T003_Id' AND T006_T004_Id IS NULL";
            $array2 = mysql_query($sql2);
            $linha  = mysql_fetch_array($array2);

            $sql3   = "SELECT SUM(T145_Quantidade_Confirmada) FROM T145 LEFT JOIN T147 ON T147_T145_Id=T145_Id WHERE T145_T003_Id ='$T003_Id' AND T147_T145_Id IS NULL";
            $array3 = mysql_query($sql3);
            $linha2 = mysql_fetch_array($array3);

            $sqlT138    = "SELECT * FROM T138 WHERE T138_T003_Id='{$T003_Id}' AND T138_T005_Id IS NULL";
            $resultT138 = mysql_query($sqlT138);
            $linhaT138  = mysql_num_rows($resultT138);

            if ($linha[0] <= 0 && $linha2[0] <= 0 && $linhaT138 <= 0) {
                return "É necessário confirmar a quantidade de pelo menos um item, serviço ou troca";
            }

            $T089  = mysql_query("SELECT sum(T089_Valor_Parcela) as totalParcelas, T003_Valor_Total, T003_Flag_Acp FROM T003 LEFT JOIN T089 ON T089_T003_Id = T003_Id WHERE T003_Id='{$T003_Id}'");
            $mT089 = mysql_fetch_array($T089);

            $obrigatorioGerarParcelaOrcamento = $g['C031']['obrigatorioGerarParcelaOrcamento'];
            if ($mT089['T003_Flag_Acp'] != 4 && number_format($mT089['totalParcelas'], 2) != number_format($mT089['T003_Valor_Total'], 2) && $obrigatorioGerarParcelaOrcamento == 'S') {
                return "O valor total das parcelas não confere com o total do orçamento, verifique a forma de pagamento.";
            }

            // Verifica se o banco vinculado aos portadores da forma de pagamento está ativo
            $T089 = mysql_query("SELECT IFNULL(D007_Flag_Ativo,0) as D007_Flag_Ativo, IFNULL(D027_D007_Id,0) as D027_D007_Id,D027_Portador from T089
                                LEFT JOIN D027 ON D027_Id=T089_D027_Id
                                LEFT JOIN D007 ON D007_Id=D027_D007_Id
                                WHERE T089_T003_Id='{$T003_Id}'");
            while($mT089=mysql_fetch_array($T089)){
                if($mT089['D027_D007_Id'] > 0 && $mT089['D007_Flag_Ativo'] != 'S'){
                    return "O portador \"".$mT089['D027_Portador']."\" está com a conta bancária inativa. Por favor verifique com o setor financeiro";
                }
            }

            //Verifica se há itens do orçamento sem CFOP, NCM ou com origem em branco
            $retornoRestricao = $this->verificaDadosOrcamento($T003_Id);
            if($retornoRestricao !== true){
                return $retornoRestricao;
            }

            $permitirVenda = $g['C031']['permitirVendaSemEstoque'];
            $permitirVenda = (!empty($permitirVenda) ? $permitirVenda : 'N');

            $sqlEstoque  = mysql_query("SELECT T004_Id 
                                       FROM T004 
                                  LEFT JOIN T055 ON T055_D009_Id=T004_D009_Id AND T055_T075_Id=T004_T075_Id
                                  LEFT JOIN D009 ON D009_Id=T004_D009_Id 
                                  LEFT JOIN T006 ON T006_T004_Id=T004_Id 
                                  left join D006 on D006_Id=T004_D006_Id
                                      WHERE T004_T003_Id='{$T003_Id}' 
                                        AND (T004_Quantidade_Confirmada>IF(IFNULL(T004_T075_Id,0)>0,IFNULL(T055_Quantidade,0),IF(IFNULL(T004_T206_Id,0)>0,D009_Quantidade_DF(T004_D009_Id,T004_T206_Id, 0),IFNULL(D009_Quantidade_Estoque_Liquido,0))))
                                        AND T006_Id IS NULL 
                                        AND '{$permitirVenda}'='N'
                                        AND D006_Flag_Estoque ='D'");
            $rowsEstoque = mysql_num_rows($sqlEstoque);
            if ($rowsEstoque > 0) {
                $msg = ($rowsEstoque > 1 ? "Há itens sem estoque suficiente, favor verificar" : "Existe item sem estoque suficiente, favor verificar");
                return $msg;
            }

            $sqlEstoque  = mysql_query("SELECT T004_Id 
                                       FROM T004 
                                  LEFT JOIN D009 ON D009_Id=T004_D009_Id 
                  LEFT JOIN D049 ON D049_Id=D009_D049_Id
                  LEFT JOIN D001 ON D001_Id=D049_D001_Id
                                  LEFT JOIN T006 ON T006_T004_Id=T004_Id 
                                      WHERE T004_T003_Id='{$T003_Id}' 
                                        AND D001_Flag_Pre_Cadastro='S'
                    AND (T004_T219_Id IS NULL OR T004_T219_Id <= 0)
                    AND T004_Valor_Custo_Unitario <= 0
                    AND T006_Id IS NULL
                    AND T004_Quantidade_Confirmada>0");
            $rowsEstoque = mysql_num_rows($sqlEstoque);
            if ($rowsEstoque > 0 && $g['C031']['permitePedidoProdutoPreCadastro'] == 'N') {
                $msg = ($rowsEstoque > 1 ? "Há itens PRÉ-CADASTRO que não possuem retorno de cotação de compra, favor verificar" : "Existe item PRÉ-CADASTRO que não possui retorno de cotação de compra, favor verificar");
                return $msg;
            }

            $sql = "SELECT * FROM T003 LEFT JOIN D006 ON D006_Id=T003_D006_Id LEFT JOIN D024 ON D024_Id=T003_D024_Id LEFT JOIN D020 ON D020_Id=D024_D020_Id WHERE T003_Id='$T003_Id'";

            $array = mysql_query($sql);

            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '1 Erro: ' . mysql_error();
            }

            $orcamento = mysql_fetch_assoc($array);

            $T005_Data_Entrega = $g['conf']->camposValorPadrao('T005_Data_Entrega');
            $T005_Data_Entrega = (!empty($T005_Data_Entrega)) ? $T005_Data_Entrega : '';

            $totalizarPeso = ($g['C031']['totalizarPesoVenda'] == 'S') ? 'S' : 'N';

            //if ($orcamento['D024_Flag_Pessoa_Fisica_Juridica'] == "J") {
            //    if (empty($orcamento['D024_Cnpj'])) {
            //        return "CNPJ da empresa em branco";
            //    }
            //} else if ($orcamento['D024_Flag_Pessoa_Fisica_Juridica'] == "F" && $orcamento['T003_Flag_Acp'] != 4) {
            //    if (empty($orcamento['D024_Cpf'])) {
            //        return "Cpf do cliente em branco";
            //    }
            //}

            //Ajuste flag status liberação
            //Caso o orçamento não gerou parcela, mudamos a flag para 1 (não passou por liberação), pois no pedido se for gerado parcela, é preciso passar por análise
            $sqlT089 = mysql_query("SELECT T089_Id FROM T089 WHERE T089_T003_Id = '{$T003_Id}'");
            if(mysql_num_rows($sqlT089) <= 0){
                $orcamento['T003_Flag_Status_Liberacao'] = '1';
            }

            $sql = "INSERT INTO T005 (T005_T003_Id,
                                      T005_D013_Id,
                                      T005_D024_Id,
                                      T005_D021_Id,
                                      T005_C004_Id,
                                      T005_D006_Id,
                                      T005_C007_Id,
                                      T005_C007_Id_Vendedor_Interno,
                                      T005_C007_Id_Vendedor_Externo,
                                      T005_Ordem_Compra,
                                      T005_Observacao,
                                      T005_Observacao_Comercial,
                                      T005_Observacao_Nota_Fiscal,
                                      T005_Flag_ACP,
                                      T005_Flag_Status,
                                      T005_Data_Emissao,
                                      T005_Hora_Emissao,
                                      T005_Flag_Situacao,
                                      T005_Flag_Revenda_Consumidor,
                                      T005_D022_Id,
                                      T005_D022_Id_Default,
                                      T005_Observacao_Pagamento,
                                      T005_Pgto_Parcelas,
                                      T005_Pgto_Primeiro_Vcto_Tipo,
                                      T005_Pgto_Primeiro_Vcto_Valor,
                                      T005_Pgto_Dias_Vcto_Tipo,
                                      T005_Pgto_Dias_Vcto_Valor,
                                      T005_Pgto_Portador,
                                      T005_Pgto_Data,
                                      T005_Pgto_C007_Id,
                                      T005_D116_Id,
                                      T005_Liberou_Alcada_C007_Id,
                                      T005_Liberou_Alcada_Data,
                                      T005_Liberou_Alcada_IPV,
                                      T005_Liberou_Alcada_Valor_Total,
                                      T005_Liberou_Alcada_Prazo_Medio,
                                      T005_Liberou_Alcada_Desconto,
                                      T005_ST_ICMS_Interno,
                                      T005_ICMS_Inter_Estadual,
                                      T005_Flag_Frete,
                                      T005_Flag_Lista_Preco,
                                      T005_Valor_Frete,
                                      T005_Flag_Expedicao,
                                      T005_Percentual_Comissao,
                                      T005_Valor_Total_Comissao,
                                      T005_Percentual_Comissao_Representante,
                                      T005_Valor_Comissao_Representante,
                                      T005_Percentual_Comissao_Externo,
                                      T005_Valor_Total_Comissao_Externo,
                                      T005_Percentual_Comissao_Usuario,
                                      T005_Valor_Total_Comissao_Usuario,
                                      T005_Percentual_Comissao_Manual,
                                      T005_Valor_Desconto,
                                      T005_Valor_Desconto_Manual,
                                      T005_Flag_Almoxarifado,
                                      T005_Total_Cubagem,
                                      T005_Flag_Destino_Produto,
                                      T005_D036_Id,
                                      T005_D148_Id_Entrega,
                                      T005_D148_Id_Cobranca,
                                      T005_Flag_Status_Liberacao,
                                      T005_Cliente_Area,
                                      T005_Casas_Decimais_Preco,
                                      T005_Data_Entrega,
                                      T005_Flag_Totalizar_Peso,
                                      T005_C004_Id_Estoque,
                                      T005_Valor_Despesas_Acessorias,
									  T005_Pgto_Primeiro_Vcto_Valor_Data,
                                      T005_D027_Id,
                                      T005_Flag_Frete_Primeira_Parcela,
                                      T005_Flag_ST_Primeira_Parcela,
                                      T005_Flag_IPI_Primeira_Parcela
                                      ) VALUES (
                                       '" . mysql_real_escape_string($T003_Id) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D013_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D024_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D021_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_C004_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D006_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_C007_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_C007_Id_Vendedor_Interno']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_C007_Id_Vendedor_Externo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Ordem_Compra']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Observacao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Observacao_Comercial']) . "',
                                       '',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Acp']) . "',
                                       '0',
                                       CURDATE(),
                                       CURTIME(),
                                       '1',
                                       '" . mysql_real_escape_string($orcamento['D024_Flag_Revenda_Consumidor']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D022_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['D020_D022_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Observacao_Pagamento']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Parcelas']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Primeiro_Vcto_Tipo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Primeiro_Vcto_Valor']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Dias_Vcto_Tipo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Dias_Vcto_Valor']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Portador']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_Data']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Pgto_C007_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D116_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_C007_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_Data']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_IPV']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_Valor_Total']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_Prazo_Medio']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Liberou_Alcada_Desconto']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_ST_ICMS_Interno']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_ICMS_Inter_Estadual']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Frete']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Lista_Preco']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Frete']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Expedicao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Percentual_Comissao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Total_Comissao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Percentual_Comissao_Representante']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Comissao_Representante']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Percentual_Comissao_Externo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Total_Comissao_Externo']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Percentual_Comissao_Usuario']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Total_Comissao_Usuario']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Percentual_Comissao_Manual']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Desconto']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Valor_Desconto_Manual']) . "',
                                       'N',
                                       '" . mysql_real_escape_string($orcamento['T003_Total_Cubagem']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Destino_Produto']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D036_Id']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D148_Id_Entrega']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_D148_Id_Cobranca']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Flag_Status_Liberacao']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Cliente_Area']) . "',
                                       '" . mysql_real_escape_string($orcamento['T003_Casas_Decimais_Preco']) . "',
                                       '{$T005_Data_Entrega}',
                                       '{$totalizarPeso}',
                                       '" . mysql_real_escape_string($orcamento['T003_C004_Id_Estoque']) . "',
                                        '" . mysql_real_escape_string($orcamento['T003_Valor_Despesas_Acessorias']) . "',
                                        '" . mysql_real_escape_string($orcamento['T003_Pgto_Primeiro_Vcto_Valor_Data']) . "',
                                        '" . mysql_real_escape_string($orcamento['T003_D027_Id']) . "',
                                        '" . mysql_real_escape_string($orcamento['T003_Flag_Frete_Primeira_Parcela']) . "',
                                        '" . mysql_real_escape_string($orcamento['T003_Flag_ST_Primeira_Parcela']) . "',
                                        '" . mysql_real_escape_string($orcamento['T003_Flag_IPI_Primeira_Parcela']) . "'
                                       );";
            log($sql);
            $retorno = mysql_query($sql);
            $erro    = mysql_error();
            if (!empty($erro)) {
                mysql_query("ROLLBACK");
                return '2 Erro: ' . $erro;
            }
            $T005_Id = $g['mysqlLastId'];

            //Insere registro do pedido na tarefa
            //require_once('bibliotecas/classes/ETC017.php');
            //$ETC017 = new ETC017();
            //$ETC017->inserirRegistroTarefa($T005_Id, 'T005');

            //Insere registros no histórico do pedido (T178)
            mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'Gerou Pedido','')");
            //Define o Orçamento como Fechado
            mysql_query("UPDATE T003 SET T003_Flag_Perdido='F' WHERE T003_Id={$T003_Id}");

            /* Personalização: Não inserir as parcelas, apenas gravar a forma de pagamento na T005
            //selecionar da T089 e inserir na T090 (pagamento)
            $query7 = "SELECT * FROM T089 WHERE T089_T003_Id='$T003_Id'";
            $result9 = mysql_query($query7);
            while($row = mysql_fetch_array($result9)){
            $sql90 = <<<EOT
            INSERT INTO T090 (T090_T005_Id, T090_D027_Id, T090_Numero_Parcela, T090_Prazos_Dias, T090_Data_Vencimento, T090_Valor_Parcela) 
            VALUES
            ('{$T005_Id}', '{$row['T089_D027_Id']}', '{$row['T089_Numero_Parcela']}', '{$row['T089_Prazos_Dias']}', '{$row['T089_Data_Vencimento']}', '{$row['T089_Valor_Parcela']}')
            EOT;
            $retorno .= $sql90."<br ></EOT>";
            $result = mysql_query($sql90);
            if (mysql_error()) {
            mysql_query("ROLLBACK");
            return '22: Erro Parcelas: ' . mysql_error();
            }
            }*/

            //Insere os documentos do orçamento no pedido.
            $sqlDocumentos    = "SELECT * FROM T188 WHERE T188_T003_Id='{$T003_Id}'";
            $resultDocumentos = mysql_query($sqlDocumentos);
            while ($linhaDocumentos = mysql_fetch_array($resultDocumentos)) {
                $sql            = <<<EOT
                INSERT INTO T167 
                (T167_T005_Id, T167_C007_Id, T167_Nome_Arquivo, T167_Descricao, T167_Data_Hora_Upload)
                VALUES
                ('{$T005_Id}','{$linhaDocumentos['T188_C007_Id']}','{$linhaDocumentos['T188_Nome_Arquivo']}','{$linhaDocumentos['T188_Descricao']}','{$linhaDocumentos['T188_Data_Hora_Upload']}')
EOT;
                $resultCopiaDoc = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '19 Erro: ' . mysql_error();
                }

                //----Copia o arquivo da pasta de orçamentos para pasta de pedidos
                //Pega a extensão do arquivo
                $pathinfo              = pathinfo($linhaDocumentos['T188_Nome_Arquivo']);
                $pathinfo['extension'] = empty($pathinfo['extension']) ? '.tmp' : $pathinfo['extension'];

                //Pega o caminho do arquivo no orçamento
                $dirOrigem = "{$g['pathDados']}orcamento";
                is_dir($dirOrigem) OR mkdir($dirOrigem, 0777, true);
                $fileOrigem = $dirOrigem . '/' . $linhaDocumentos['T188_Id'] . '.' . $pathinfo['extension'];

                $dirDestino = "{$g['pathDados']}pedido";
                is_dir($dirDestino) OR mkdir($dirDestino, 0777, true);
                $fileDestino = $dirDestino . '/' . $g['mysqlLastId'] . '.' . $pathinfo['extension'];
                copy($fileOrigem, $fileDestino);
            }

            // neste momento insere apenas os serviços (T145) que nao estão associados a produtos (avulsos)
            $sql    = "SELECT * FROM T145 WHERE T145_T003_Id='$T003_Id' AND T145_T004_Id IS NULL";
            $result = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '3 Erro: ' . mysql_error();
            }

            while ($orcamentoSer = mysql_fetch_array($result)) {

                $sql     = "INSERT INTO T147 (T147_T005_Id, 
                                              T147_T145_Id, 
                                              T147_D110_Id,
                                              T147_D112_Id,
                                              T147_Titulo, 
                                              T147_Descricao, 
                                              T147_Tempo_Estimado, 
                                              T147_Quantidade,
                                              T147_Valor_Unitario,
                                              T147_Valor_Total,
                                              T147_Hora_Inicio,
                                              T147_Hora_Fim,
                                              T147_Status,
                                              T147_D116_Id) 
                                              VALUES 
                                              ('" . mysql_real_escape_string($T005_Id) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_D110_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_D112_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Titulo']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Descricao']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Tempo_Estimado']) . "',
                                                                     '1',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Valor_Unitario']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Valor_Unitario']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Hora_Inicio']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Hora_Fim']) . "',
                                                                     'P',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_D116_Id']) . "'
                                                                     );";
                $retorno = mysql_query($sql);              
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '4 Erro: ' . mysql_error();
                }
                $T147_Id = $g['mysqlLastId'];
                
                //selecionar todos da T146 onde T146_T145_Id = T145_Id
                //mais um looping para cada T146 inserir um registro na T148
                
                $sql     = "SELECT * FROM T146 WHERE T146_T145_Id=" . $orcamentoSer['T145_Id'];
                $result2 = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '5 Erro: ' . mysql_error();
                }
                
                while ($processo = mysql_fetch_assoc($result2)) {
                    $sql     = "INSERT INTO T148 (T148_Id,
                                                  T148_T147_Id,
                                                  T148_Titulo,
                                                  T148_Descricao)
                                                  VALUES
                                                  ('',
                                                   '" . mysql_real_escape_string($T147_Id) . "',
                                                   '" . mysql_real_escape_string($processo['T146_Titulo']) . "',
                                                   '" . mysql_real_escape_string($processo['T146_Descricao']) . "'
                                                   );";
                    $retorno = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '11.2 Erro: ' . mysql_error();
                    }
                }
                $sql     = "SELECT * FROM T152 WHERE T152_T145_Id=" . $orcamentoSer['T145_Id'];
                $result3 = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return '5 Erro: ' . mysql_error();
                }

                while ($material = mysql_fetch_assoc($result3)) {
                    $sql = "INSERT INTO T149(T149_Id,
                                                T149_D009_Id,
                                                T149_D037_Id,
                                                T149_T147_Id,
                                                T149_Quantidade)
                                                VALUES
                                                ('',
                                                 '" . mysql_real_escape_string($material['T152_D009_Id']) . "',
                                                 '" . mysql_real_escape_string($material['T152_D037_Id']) . "',
                                                 '" . mysql_real_escape_string($T147_Id) . "',
                                                 '" . mysql_real_escape_string($material['T152_Quantidade']) . "'
                                                 );";
                    $retorno = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '7 Erro: ' . mysql_error();
                    }
                }
            }

            //selecionar todos da T004 onde t004_T003_Id = $T003
            //inserir na t006 todos da T004 selecionados
            $sql             = <<<EOT
            SELECT *, D009_Quantidade_OC_3(D009_Id,0) as D009_Quantidade_OC FROM T004 
            LEFT JOIN T006 ON T006_T004_Id=T004_Id 
            LEFT JOIN D009 ON D009_Id=T004_D009_Id
            LEFT JOIN D005 ON D005_Id=T004_D005_Id
            WHERE T004_T003_Id='{$T003_Id}' AND T006_Id is null AND T004_Quantidade_Confirmada>0;
EOT;
            $retornoConsulta = mysql_query($sql);
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '8 Erro: ' . mysql_error();
            }

            if (mysql_num_rows($retornoConsulta) <= 0) {
                //mysql_query("ROLLBACK");
                // return 'Não existem ítens para gerar pedido.';
                // O produto pode ter apenas serviços. Regra retirada
            }
            // variavel que soma quantos itens do orÃ§amento foram inseridos no pedido
            $countConfirmados = 0;
            $restricaoProduto = '';
            while ($linha = mysql_fetch_assoc($retornoConsulta)) {
                /*              // seleciona todos os pedidos gerados a partir do orÃ§amento atual
                $sql = "SELECT * FROM T005 WHERE T005_T003_Id='$T003_Id';";
                $ret = mysql_query($sql);
                // variavel que verifica se o item do orÃ§amento atual jÃ¡ estÃ¡ em algum pedido ja gerado deste orÃ§amento
                $incluir=true;
                // para cada pedido jÃ¡ gerado a partir do orÃ§amento atual
                while($reg = mysql_fetch_assoc($ret)){
                // seleciona os itens deste pedido
                // e verifica se algum destes itens é igual ao item atual
                $sql1 = "SELECT * FROM T006 WHERE T006_T005_Id='{$reg['T005_Id']}';";
                $ret1 = mysql_query($sql1);
                $item=mysql_fetch_assoc($ret1);
                if (!empty($item['T006_D009_Id'])){
                if ($item['T006_T004_Id']==$linha['T004_Id']){
                $incluir=false; 
                }
                }
                }
                */
                if ($g['C031']['permitePedidoProdutoPreCadastro'] == 'N') {
                    //Verifica se existe algum produto pré cadastro
                    $sqlRestricao     = "SELECT T004_Codigo_Produto,D001_Flag_Pre_Cadastro FROM T004 LEFT JOIN D009 ON D009_Id=T004_D009_Id LEFT JOIN D001 ON D001_Id=D009_D001_Id WHERE T004_T003_Id='$T003_Id' AND T004_Quantidade_Confirmada>0 AND T004_Id={$linha['T004_Id']}";
                    $retornoRestricao = mysql_query($sqlRestricao);
                    $rowRestricao     = mysql_fetch_array($retornoRestricao);
                    if ($rowRestricao['D001_Flag_Pre_Cadastro'] == 'S') {
                        $restricaoProduto .= "O produto " . $rowRestricao['T004_Codigo_Produto'] . " é pré-cadastro.<br />";
                    }
                }

                $linha['T004_Valor_Preco_Original'] = ($linha['T004_Valor_Preco_Original'] > 0 ? $linha['T004_Valor_Preco_Original'] : $linha['T004_Valor_Preco_Sem_Desconto_Unitario']);
                $countConfirmados++;
                $sql  = "INSERT INTO T006 (T006_Codigo_Substituto,
                                          T006_Flag_Status,
                                          T006_Item,
                                          T006_T004_Id,
                                          T006_D037_Id,
                                          T006_D005_Id,
                                          T006_D006_Id,
                                          T006_Classificacao_Fiscal,
                                          T006_D009_Id,
                                          T006_Codigo_Produto,
                                          T006_Marca,
                                          T006_Descricao_Produto,
                                          T006_Quantidade,
                                          T006_Valor_Preco_Bruto,
                                          T006_Valor_Custo_Unitario,
                                          T006_Valor_Preco_Unitario,
                                          T006_Valor_Preco_Sem_Desconto_Unitario,
                                          T006_Valor_Preco_Original_Bruto,
                                          T006_Percentual_Desconto_Bruto,
                                          T006_IPV,
                                          T006_T005_Id,
                                          T006_C004_Id,
                                          T006_Numero_Pedido_Compra,
                                          T006_Item_Pedido_Compra,
                                          T006_Aliquota_IPI,
                                          T006_Aliquota_ICMS,
                                          T006_Percentual_Reducao_ICMS,
                                          T006_Percentual_Reducao_ICMS_ST,
                                          T006_ST_VA,
                                          T006_Flag_ST,
                                          T006_Flag_Situacao_Tributaria,
                                          T006_ICMS_Inter_Estadual,
                                          T006_ST_ICMS_Interno,
                                          T006_Flag_Isento_PIS,
                                          T006_Aliquota_PIS,
                                          T006_Situacao_Tributaria_PIS,
                                          T006_Flag_Isento_COFINS,
                                          T006_Aliquota_COFINS,
                                          T006_Flag_Preco_Especial,
                                          T006_Situacao_Tributaria_COFINS,
                                          T006_Situacao_Tributaria_IPI,
                                          T006_D059_Id,
                                          T006_Valor_Preco_Original,
                                          T006_Percentual_Desconto,
                                          T006_Liberou_Alcada_Saldo_Futuro,
                                          T006_Quantidade_Estoque,
                                          T006_T075_Id,
                                          T006_T206_Id,
                                          T006_Cubagem_Unitaria,
                                          T006_Total_Cubagem,
                                          T006_Flag_Pre_Cadastro,
                                          T006_Demonstrativo_Preco,
                                          T006_Demonstrativo_Preco_Tabela,
                                          T006_Demonstrativo_Preco_Transferencia,
                                          T006_Demonstrativo_Preco_Cotacao,
                                          T006_Valor_Preco_Compra,
                                          T006_Valor_Preco_Tabela,
                                          T006_Valor_Preco_Transferencia,
                                          T006_Valor_Custo_Transferencia,
                                          T006_Flag_Origem_Produto_Transferencia,
                                          T006_Valor_Preco_Cotacao,
                                          T006_T001_Id,
                                          T006_Flag_Destino_Produto,
                                          T006_Flag_Origem_Produto,
                                          T006_Observacao_Entrega,
                                          T006_Observacao,
                                          T006_Observacao_Compra,
                                          T006_D006_Id_Info,
                                          T006_D005_Id_Info,
                                          T006_T219_Id,
                                          T006_Valor_ICMS_Desonerado,
                                          T006_Motivo_Desoneracao_ICMS,
                                          T006_Demonstrativo_Custo_Tabela,
                                          T006_Quantidade_OC,
                                          T006_Demonstrativo_Preco_Venda,
                                          T006_Aliquota_IRPJ,
                                          T006_Aliquota_CSLL,
                                          T006_Flag_Isento_IRPJ,
                                          T006_Flag_Isento_CSLL,
                                          T006_Flag_Transferencia,
                                          T006_C004_Id_Transferencia,
                                          T006_UF_ICMS_Substituicao_Devido,
                                          T006_Percentual_Base_Operacao_Propria
                                          )
                                      VALUES
                                         ('" . mysql_real_escape_string($linha['T004_Codigo_Substituto']) . "',
                                          '',
                                          '" . mysql_real_escape_string($linha['T004_Item']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D037_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D005_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D006_Id']) . "',
                                          '" . mysql_real_escape_string($linha['D005_Classificacao_Fiscal']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D009_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Codigo_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Marca']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Descricao_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Quantidade_Confirmada']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Bruto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Custo_Unitario']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Unitario']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Unitario']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Original_Bruto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Desconto_Bruto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_IPV']) . "',
                                          '" . mysql_real_escape_string($T005_Id) . "',
                                          '" . mysql_real_escape_string($linha['T004_C004_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Numero_Pedido_Compra']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Item_Pedido_Compra']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_IPI']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_ICMS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Reducao_ICMS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Reducao_ICMS_ST']) . "',
                                          '" . mysql_real_escape_string($linha['T004_ST_VA']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_ST']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Situacao_Tributaria']) . "',
                                          '" . mysql_real_escape_string($linha['T004_ICMS_Inter_Estadual']) . "',
                                          '" . mysql_real_escape_string($linha['T004_ST_ICMS_Interno']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Isento_PIS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_PIS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Situacao_Tributaria_PIS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Isento_COFINS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_COFINS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Preco_Especial']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Situacao_Tributaria_COFINS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Situacao_Tributaria_IPI']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D059_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Original']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Desconto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Liberou_Alcada_Saldo_Futuro']) . "',
                                          '" . mysql_real_escape_string($linha['D009_Quantidade_Estoque_Liquido']) . "',
                                          '" . mysql_real_escape_string($linha['T004_T075_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_T206_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Cubagem_Unitaria']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Total_Cubagem']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Pre_Cadastro']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Preco']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Preco_Tabela']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Preco_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Preco_Cotacao']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Compra']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Tabela']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Custo_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Origem_Produto_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_Preco_Cotacao']) . "',
                                          '" . mysql_real_escape_string($linha['T004_T001_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Destino_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Origem_Produto']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Observacao_Entrega']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Observacao']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Observacao_Compra']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D006_Id_Info']) . "',
                                          '" . mysql_real_escape_string($linha['T004_D005_Id_Info']) . "',
                                          '" . mysql_real_escape_string($linha['T004_T219_Id']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Valor_ICMS_Desonerado']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Motivo_Desoneracao_ICMS']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Custo_Tabela']) . "',
                                          '" . mysql_real_escape_string($linha['D009_Quantidade_OC']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Demonstrativo_Preco_Venda']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_IRPJ']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Aliquota_CSLL']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Isento_IRPJ']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Isento_CSLL']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Flag_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_C004_Id_Transferencia']) . "',
                                          '" . mysql_real_escape_string($linha['T004_UF_ICMS_Substituicao_Devido']) . "',
                                          '" . mysql_real_escape_string($linha['T004_Percentual_Base_Operacao_Propria']) . "'
                                          )";
                $ret  = mysql_query($sql, false);
                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return '9 Erro: ' . $erro;
                }

                // Processa estoque por locação
                require_once('bibliotecas/classes/CAD002.php');
                $CAD002 = new CAD002();
                $CAD002->processaEstoqueLiquidoPorLocacao($linha['T004_D009_Id']);

                $T006_Id = $g['mysqlLastId'];
                //ATUALIZA UMA REQUISIÇÃO JA CRIADA COM T006_Id
                mysql_query("UPDATE T189 SET T189_T006_Id = '{$T006_Id}', T189_Flag_Status = 'N' WHERE T189_T004_Id = '{$linha['T004_Id']}'");
                // mysql_query("UPDATE T189 SET T189_Flag_Status = 'N' WHERE T189_T006_Id = '{$g['mysqlLastId']}'");

                mysql_query("call T006_Gravar_Totalizacao_4($T006_Id)");
                $erro = mysql_error();
                if (!empty($erro)) {
                    mysql_query("ROLLBACK");
                    return '10 Erro: ' . mysql_error();
                }

                //Atualiza a Cotação já existente para o orçamento, com o número do pedido
                mysql_query("UPDATE T219 SET T219_T006_Id = '{$T006_Id}' WHERE T219_T004_Id = '{$linha['T004_Id']}'");

                //Limpa o campo T004_Flag_Comprar para não gerar lentidão
                mysql_query("UPDATE T004 SET T004_Flag_Comprar='N' WHERE T004_Id = '{$linha['T004_Id']}'");

                //SELECT T006_Id, T066_Id FROM T006 LEFT JOIN T066 ON T066_D009_Id=T006_D009_Id WHERE T006_Id = '294670'

                // verificar se tem algum T145 com T145_T004_Id igual ao T004_Id que acabou de ser inserido na T006
                // ou seja, insere na T147 os serviços que estão associados ao ítem que acabou de ser inserido 

                $sql    = "SELECT * FROM T145 WHERE T145_T003_Id='$T003_Id' AND T145_T004_Id='{$linha['T004_Id']}'";
                $result = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }

                while ($orcamentoSer = mysql_fetch_array($result)) {

                    $sql     = "INSERT INTO T147 (T147_T005_Id, 
                                              T147_T145_Id, 
                                              T147_D110_Id,
                                              T147_D112_Id,
                                              T147_Titulo, 
                                              T147_Descricao, 
                                              T147_Tempo_Estimado, 
                                              T147_Quantidade,
                                              T147_Valor_Unitario,
                                              T147_Valor_Total,
                                              T147_Hora_Inicio,
                                              T147_Hora_Fim,
                                              T147_Status,
                                              T147_T006_Id, 
                                              T147_D116_Id)
                                              VALUES 
                                              ('" . mysql_real_escape_string($T005_Id) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_D110_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_D112_Id']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Titulo']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Descricao']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Tempo_Estimado']) . "',
                                                                     '1',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Valor_Unitario']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Valor_Unitario']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Hora_Inicio']) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_Hora_Fim']) . "',
                                                                     'P',
                                                                     '" . mysql_real_escape_string($T006_Id) . "',
                                                                     '" . mysql_real_escape_string($orcamentoSer['T145_D116_Id']) . "'
                                                                     );";
                    $retorno = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '4 Erro: ' . mysql_error();
                    }
                    $T147_Id = $g['mysqlLastId'];

                    //selecionar todos da T146 onde T146_T145_Id = T145_Id
                    //mais um looping para cada T146 inserir um registro na T148

                    $sql     = "SELECT * FROM T146 WHERE T146_T145_Id=" . $orcamentoSer['T145_Id'];
                    $result2 = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '5 Erro: ' . mysql_error();
                    }

                    while ($processo = mysql_fetch_assoc($result2)) {
                        $sql     = "INSERT INTO T148 (T148_Id,
                                                  T148_T147_Id,
                                                  T148_Titulo,
                                                  T148_Descricao)
                                                  VALUES
                                                  ('',
                                                   '" . mysql_real_escape_string($T147_Id) . "',
                                                   '" . mysql_real_escape_string($processo['T146_Titulo']) . "',
                                                   '" . mysql_real_escape_string($processo['T146_Descricao']) . "'
                                                   );";
                        $retorno = mysql_query($sql);
                        if (mysql_error()) {
                            mysql_query("ROLLBACK");
                            return '11.3 Erro: ' . mysql_error();
                        }
                    }
                    $sql     = "SELECT * FROM T152 WHERE T152_T145_Id=" . $orcamentoSer['T145_Id'];
                    $result3 = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '5 Erro: ' . mysql_error();
                    }
                    
                    while ($material = mysql_fetch_assoc($result3)) {
                        $sql = "INSERT INTO T149(T149_Id,
                                                T149_D009_Id,
                                                T149_D037_Id,
                                                T149_T147_Id,
                                                T149_Quantidade)
                                                VALUES
                                                ('',
                                                 '" . mysql_real_escape_string($material['T152_D009_Id']) . "',
                                                 '" . mysql_real_escape_string($material['T152_D037_Id']) . "',
                                                 '" . mysql_real_escape_string($T147_Id) . "',
                                                 '" . mysql_real_escape_string($material['T152_Quantidade']) . "'
                                                 );";
                        
                        $retorno = mysql_query($sql);
                        if (mysql_error()) {
                            mysql_query("ROLLBACK");
                            return '7 Erro: ' . mysql_error();
                        }
                    }    
                }
                
                //Copia agregados do produto
                $sql = "SELECT * FROM T202 WHERE T202_T004_Id='{$linha['T004_Id']}'";
                
                $result = mysql_query($sql);
                if (mysql_error()) {
                    mysql_query("ROLLBACK");
                    return 'Erro: ' . mysql_error();
                }
                
                while ($orcamentoSer = mysql_fetch_array($result)) {
                    $sql     = "INSERT INTO T204 (T204_T006_Id, 
                                                      T204_Descricao, 
                                                      T204_Observacao,
                                                      T204_Valor_Unitario)
                                                      VALUES 
                                                      ('" . mysql_real_escape_string($T006_Id) . "',
                                                                               '" . mysql_real_escape_string($orcamentoSer['T202_Descricao']) . "',
                                                                               '" . mysql_real_escape_string($orcamentoSer['T202_Observacao']) . "',
                                                                               '" . mysql_real_escape_string($orcamentoSer['T202_Valor_Unitario']) . "'
                                                                               );";
                    $retorno = mysql_query($sql);
                    //return $sql;                 
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '4 Erro: ' . mysql_error();
                    }
                }
            }
            
            
            $sql     = "SELECT * FROM T147 WHERE T147_T005_Id='{$T005_Id}'";
            $result9 = mysql_query($sql);
            while ($linhaVeiculo = mysql_fetch_array($result9)) {
                //Verificando se já existe o veículo de T147 cadastrado na T171
                $sql    = "SELECT * FROM T171 WHERE T171_D116_Id='{$linhaVeiculo['T147_D116_Id']}' AND T171_T005_Id='{$T005_Id}'";
                $result = mysql_query($sql);
                if (mysql_num_rows($result) == 0) {
                    $sql           = <<<EOT
                    INSERT INTO T171 
                    (T171_D116_Id, T171_T005_Id, T171_T147_Id)
                    VALUES
                    ('{$linhaVeiculo['T147_D116_Id']}','{$T005_Id}','{$linhaVeiculo['T147_Id']}')
EOT;
                    $resultEntrega = mysql_query($sql);
                    if (mysql_error()) {
                        mysql_query("ROLLBACK");
                        return '19 Erro: ' . mysql_error();
                    }
                }
            }
            
            
            mysql_query("UPDATE T138 SET T138_T005_Id='$T005_Id' WHERE T138_T003_Id='$T003_Id'");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . mysql_error();
            }
            
            mysql_query("call T005_Gravar_Totalizacao_4($T005_Id)");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '11 Erro: ' . mysql_error();
            }

            mysql_query("call T003_Gravar_Totalizacao_4($T003_Id)");
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return '11 Erro: ' . mysql_error();
            }

            if (!empty($restricaoProduto)) {
                mysql_query("ROLLBACK");
                return $restricaoProduto . "<br ></EOT>Por favor conclua o cadastro dos itens acima.";
            }
            
            $sqlT089 = mysql_query("SELECT T089_Numero_Parcela,T089_D027_Id,T089_Valor_Parcela,AVG(T089_Prazos_Dias) as PrazoMedio FROM T089 WHERE T089_T003_Id = '{$T003_Id}'");
            $T089    = mysql_fetch_array($sqlT089);
            mysql_query("UPDATE T005 SET T005_Prazo_Medio_Orcamento = '{$T089['PrazoMedio']}' WHERE T005_Id = '{$T005_Id}'");
            
            require_once("bibliotecas/classes/VEN002.php");
            $VEN002 = new VEN002();
            
            $creditoLiberado = $VEN002->verificaCreditoLiberado($T005_Id);
            log("chegou");
            $mT005 = mysql_fetch_array(mysql_query("SELECT * FROM T005 WHERE T005_Id = '{$T005_Id}'"));
            if ($orcamento['T003_Flag_Acp'] != 4 && $creditoLiberado == false && $g['C031']['verificaCreditoLiberadoGerarPedido'] == 'S') {
                
                mysql_query("UPDATE T005 SET T005_Flag_Status=6, T005_Nome_Status = T005_Status_Pedido (T005_Flag_Status, 1), T005_Flag_Separacao_Liberar='S', T005_Flag_Separacao='S',T005_Flag_Primeira_Liberacao_Credito = '0'  WHERE T005_Id='{$T005_Id}'");
                
                //Insere registros no histórico do pedido (T178)
                $mT005['T005_Motivo_Pedido_Liberacao'] = str_replace("Enviou para análise de crédito", "Enviou para análise de crédito (automaticamente ao gerar pedido)", $mT005['T005_Motivo_Pedido_Liberacao']);
                
                $mT005['T005_Motivo_Pedido_Liberacao'] = mysql_real_escape_string($mT005['T005_Motivo_Pedido_Liberacao']);
                mysql_query("insert into T178(T178_T005_Id,T178_C007_Id,T178_Data,T178_Descricao,T178_Observacao) VALUES ({$T005_Id},{$g['usuarioAtual']},CURRENT_TIMESTAMP(),'{$mT005['T005_Motivo_Pedido_Liberacao']}','')");
            } else {
                mysql_query("UPDATE T005 SET T005_Flag_Primeira_Liberacao_Credito = '1'  WHERE T005_Id='{$T005_Id}'");
            }
            
            // T185
            include('prog/outros/distribuicaoItemPedidoEstoque.php');
            
            $dadosTabela = array('tabela' => 'T005',
                                             'tabelaId' =>  $T005_Id,
                                             'campos' => 'T005_Pgto_Primeiro_Vcto_Tipo as primeiroVencimentoTipo,
                                                 T005_Pgto_Primeiro_Vcto_Valor as primeiroVencimentoNum,
                                                 T005_Pgto_Primeiro_Vcto_Valor_Data as primeiroVencimentoData,
                                                 T005_Pgto_Dias_Vcto_Tipo as diasVctoTipo,
                                                 T005_Pgto_Dias_Vcto_Valor as valorIntervaloDiaFixo,
                                                 T005_D027_Id as D027_Id,
                                                 T005_Flag_Frete_Primeira_Parcela as campoUsarFreteParcela,
                                                 T005_Flag_ST_Primeira_Parcela as campoUsarSTParcela,
                                                 T005_Flag_IPI_Primeira_Parcela as campoUsarIPIParcela,
                                                 T005_Pgto_Parcelas as Total_Parcelas,
                                                 T005_Valor_Total_IPI as valorTotalIPI,
                                                 T005_Valor_Total as valorTotal,
                                                 T005_Valor_Frete as valorTotalFrete,
                                                 T005_Valor_ICMS_Substituicao_Retencao as valorTotalST',
                                             'where' => "T005_Id='{$T005_Id}'",
                                             'tabelaPgto' => 'T090',
                                             'camposPgto' => 'T090_Prazos_Dias as prazoDias,T090_D027_Id as D027Pagamento,T090_Numero_Parcela as numeroParcela',
                                             'wherePgto' => "T090_T005_Id='{$T005_Id}'");
            // Gerar Parcelas
            $retorno = $this->atualizarParcelas($dadosTabela);
            $retorno = $VEN002->atualizarAliquotasPedidos($T005_Id);
            
            //ALTERAR TAMBEM OS DADOS DO PAGAMENTO QUANDO GERA O PEDIDO, CASO A PESSOA FAÇA ALGUMA ALTERAÇÃO NO PORTADOR OU NO VALOR DA PARCELA ---LUCAS 31/07/2015
            $T090 = mysql_query("SELECT * FROM T090 WHERE T090_T005_Id = '{$T005_Id}'");
            $mT090 = mysql_fetch_array($T090);
           
            $sql = mysql_query("SELECT T089_Valor_Parcela, D027_Portador, T089_D027_Id,T089_Numero_Parcela FROM T089 left join D027 on D027_Id=T089_D027_Id WHERE T089_T003_Id = '{$T003_Id}'");
            while ($row = mysql_fetch_array($sql)){      
                if($mT089['T003_Valor_Total'] == $mT005['T005_Valor_Total']){
                    if($row['T089_D027_Id'] != $mT090['T090_D027_Id'] || $row['T089_Valor_Parcela'] != $mT090['T090_Valor_Parcela']){
                        mysql_query("UPDATE T090 SET T090_D027_Id = '{$row['T089_D027_Id']}', T090_Valor_Parcela = {$row['T089_Valor_Parcela']} WHERE T090_T005_Id = '{$T005_Id}' and T090_Numero_Parcela = '{$row['T089_Numero_Parcela']}'");   
                    }
                } else if($mT090['T090_D027_Id'] != $row['T089_D027_Id']){
                    mysql_query("UPDATE T090 SET T090_D027_Id = '{$row['T089_D027_Id']}' WHERE T090_T005_Id = '{$T005_Id}' and T090_Numero_Parcela = '{$row['T089_Numero_Parcela']}'");
                }
            }
            if (mysql_error()) {
                mysql_query("ROLLBACK");
                return 'Erro: ' . mysql_error();
            }
            //FIM

            // VERIFICANDO SE GEROU CORRETAMENTE O T005 E OS T006
            $T005_T006=mysql_query("SELECT T005_Id,T006_Id from T005 LEFT JOIN T006 ON T006_T005_Id=T005_Id WHERE T005_Id='{$T005_Id}'");
            $mT005_T006=mysql_fetch_array($T005_T006);
            if($mT005_T006['T005_Id'] <=0 OR $mT005_T006['T006_Id'] <=0 ){
                mysql_query("ROLLBACK");
                return "Houve um problema na gravação dos dados. Por favor, refaça a operação";
            }

            mysql_query("COMMIT");
            return true;
        } else {
            return "ERRO: T003_Id não existe";
        }
    
		
	}
	
	/**
     * atualizarAliquotas
     *
     * @param String $T003_Id
     * @param String $C004_Id
     * @return String/bool
     */
    public function xatualizarAliquotas($T003_Id, $C004_Id = false, $T004_Id = false)
    {
        global $g;
        require_once('bibliotecas/classes/FIS003.php');
        $FIS003 = new FIS003();
        require_once('bibliotecas/classes/CAD002.php');
        $CAD002 = new CAD002();

        $C004_Id = ($C004_Id == false) ? $g['empresaAtual'] : $C004_Id;

        //Verifica se foi passado um T004 específico
        if($T004_Id !== false){
            $extra = "AND T004_Id = '{$T004_Id}'";
        }

        if (empty($T003_Id)) {
            return "Não foi passado o Id do Orçamento, contate o suporte técnico";
        }
        $cmysql_T003 = mysql_query("SELECT T006_Id FROM T004 LEFT JOIN T006 ON T006_T004_Id=T004_Id WHERE T004_T003_Id='{$T003_Id}' AND T006_Id>0");
        if (mysql_num_rows($cmysql_T003) > 0) {
            return 'Este orçamento já foi faturado, não é permitido mudar o CFOP';
        }

        $cSql_T003 = mysql_query("SELECT * FROM T003 LEFT JOIN D006 ON D006_Id=T003_D006_Id WHERE T003_Id='{$T003_Id}'");
        $mT003     = mysql_fetch_array($cSql_T003);

        //========================================================================================
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //-- VERIFICA SE OS IMPOSTOS ESTAO CORRETOS
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $cClausula = "SELECT
        T004_Id,
        D009_Id,
        D001_Codigo_Produto,
        D001_Peso_Unitario_Kg,
        T003_Flag_Lista_Preco,
        T003_D024_Id,
        T003_Flag_Acp,
        T004_D006_Id,
        ifnull(T004_T219_Id,0) as T004_T219_Id
        FROM T003
        LEFT JOIN T004 ON T003_Id=T004_T003_Id
        LEFT JOIN T006 ON T004_Id=T006_T004_Id
        LEFT JOIN D009 ON D009_Id=T004_D009_Id
        LEFT JOIN D049 ON D049_Id=D009_D049_Id
        LEFT JOIN D001 ON D001_Id=D049_D001_Id
            WHERE T003_Id='{$T003_Id}'
            AND T006_Id IS NULL {$extra}";
        $cmysql_T004 = mysql_query("$cClausula");
        $erro = mysql_error();
        if (!empty($erro)) {
            return "Erro consultando itens do orcamento: $erro $cClausula";
        }

        while ($mT004 = mysql_fetch_array($cmysql_T004)) {
            $Aliquotas = $this->retornaAliquotas($mT004['D009_Id'], $T003_Id, false, false, $mT004['T004_Id']);
            $demonstrativoNCM = $FIS003->demonstrativoNCM($Aliquotas['D005_Id']);
            $demonstrativoNCM = mysql_real_escape_string($demonstrativoNCM);

            //Método retornaPrecoVenda atualizado com apenas um demonstrativo
            $dadosPrecoVenda = $CAD002->retornaPrecoVenda($mT004['D009_Id'], $mT004['T003_Flag_Lista_Preco'], $mT004['T003_D024_Id'], false, $mT004['T004_D006_Id'], $Aliquotas['Aliquota_ICMS'], $mT004['T004_T219_Id'], false, 4, $Aliquotas['Aliquota_PIS'], $Aliquotas['Aliquota_COFINS'], $Aliquotas['Aliquota_IPI'], $Aliquotas['ST_VA'], $Aliquotas['Percentual_Reducao_ICMS_ST'], $Aliquotas['ST_ICMS_Interno'], $Aliquotas['Percentual_Reducao_ICMS'], $Aliquotas['ICMS_Inter_Estadual'], $Aliquotas['Aliquota_IRPJ'], $Aliquotas['Aliquota_CSLL']);
            $origemProduto   = $dadosPrecoVenda[5];

            if ($g['C031']['atualizarAliquotaUnidadeProduto']=='S'){
                $updateD037 =  "T004_D037_Id = D001_D037_Id, ";
            }

            mysql_query("UPDATE T004 LEFT JOIN D009 ON D009_ID=T004_D009_Id LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 on D001_Id=D049_D001_Id SET
                                        T004_D005_Id                            = '{$Aliquotas['D005_Id']}',
                                        T004_Classificacao_Fiscal               = '{$Aliquotas['D005_Classificacao_Fiscal']}',
                                        T004_Aliquota_IPI                       = '{$Aliquotas['Aliquota_IPI']}',
                                        T004_Percentual_Reducao_IPI             = '{$Aliquotas['Percentual_Reducao_IPI']}',
                                        T004_Aliquota_ICMS                      = '{$Aliquotas['Aliquota_ICMS']}',
                                        T004_Aliquota_ICMS_Oculto               = '{$Aliquotas['Aliquota_ICMS_Oculto']}',
                                        T004_Percentual_Reducao_ICMS            = '{$Aliquotas['Percentual_Reducao_ICMS']}',
                                        T004_Percentual_Reducao_ICMS_ST         = '{$Aliquotas['Percentual_Reducao_ICMS_ST']}',
                                        T004_Flag_ST                            = '{$Aliquotas['Flag_ST']}',
                                        T004_ST_VA                              = '{$Aliquotas['ST_VA']}',
                                        T004_ICMS_Inter_Estadual                = '{$Aliquotas['ICMS_Inter_Estadual']}',
                                        T004_ST_ICMS_Interno                    = '{$Aliquotas['ST_ICMS_Interno']}',
                                        T004_D059_Id                            = '{$Aliquotas['D059_Id']}',
                                        T004_Flag_Situacao_Tributaria           = '{$Aliquotas['Flag_Situacao_Tributaria']}',
                                        T004_ICMS_Inter_Estadual_MVA            = '{$Aliquotas['ST_ICMS_Inter_Estadual_MVA']}',
                                        T004_ST_ICMS_Interno_MVA                = '{$Aliquotas['ST_ICMS_Interno_MVA']}',
                                        T004_Flag_Isento_PIS                    = '{$Aliquotas['Flag_Isento_PIS']}',
                                        T004_Aliquota_PIS                       = '{$Aliquotas['Aliquota_PIS']}',
                                        T004_Situacao_Tributaria_PIS            = '{$Aliquotas['Situacao_Tributaria_PIS']}',
                                        T004_Aliquota_COFINS                    = '{$Aliquotas['Aliquota_COFINS']}',
                                        T004_Situacao_Tributaria_COFINS         = '{$Aliquotas['Situacao_Tributaria_COFINS']}',
                                        T004_Flag_Isento_COFINS                 = '{$Aliquotas['Flag_Isento_COFINS']}',
                                        T004_Situacao_Tributaria_IPI            = '{$Aliquotas['Situacao_Tributaria_IPI']}',
                                        T004_Peso_Unitario                      = '{$mT004['D001_Peso_Unitario_Kg']}',
                                        T004_Peso_Bruto                         = '{$Aliquotas['D001_Peso_Unitario_Bruto']}',
                                        T004_Flag_Origem_Produto                = '{$origemProduto}',
                                        T004_Percentual_Desconto                = ((T004_Valor_Preco_Original - T004_Valor_Preco_Unitario) / T004_Valor_Preco_Original) * 100,
                                        T004_D005_Id_Info                       = '{$demonstrativoNCM}',
                                        $updateD037
                                        T004_Motivo_Desoneracao_ICMS            = '{$Aliquotas['Motivo_Desoneracao_ICMS']}',
                                        T004_IPV_Sem_Lucro                      = '{$Aliquotas['IPV_Sem_Lucro']}',
                                        T004_Aliquota_IRPJ                      = '{$Aliquotas['Aliquota_IRPJ']}',
                                        T004_Flag_Isento_IRPJ                   = '{$Aliquotas['Flag_Isento_IRPJ']}',
                                        T004_Aliquota_CSLL                      = '{$Aliquotas['Aliquota_CSLL']}',
                                        T004_Flag_Isento_CSLL                   = '{$Aliquotas['Flag_Isento_CSLL']}',
                                        T004_UF_ICMS_Substituicao_Devido        = '{$Aliquotas['UF_ICMS_Substituicao_Devido']}',
                                        T004_Percentual_Base_Operacao_Propria   = '{$Aliquotas['Percentual_Base_Operacao_Propria']}'
                                   WHERE T004_Id='{$mT004['T004_Id']}'");
            $erro = mysql_error();
            
            if (!empty($erro)) {
                return "Erro atualizando item do orcamento ID {$mT004['T004_Id']}: $erro";
            }
            
            mysql_query("call T004_Gravar_Totalizacao_4('{$mT004['T004_Id']}');");
        }

        mysql_free_result($cmysql_T004);
        mysql_free_result($cmysql_T003);

        return true;
    
		
	}
}






















