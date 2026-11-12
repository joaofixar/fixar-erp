<?php
namespace hardness;
definirClasse('FIS003');
class FIS003_
{

    /**
     * cadastrarNCM
     *
     * @param String $D005_Id
     * @return String
     */	
    public function cadastrarNCM($D005_Id)
    {
        global $g;
        $sqlC004 = mysqli_query("SELECT C004_Id FROM C004");
        while($resC004 = mysql_fetch_array($sqlC004)){
            $D149=mysqli_query("select D149_Id from D149 where D149_D005_Id='{$D005_Id}' and D149_C004_Id='{$resC004['C004_Id']}'");
            if (mysql_num_rows($D149)<=0) {
                mysqli_query("INSERT INTO D149 (D149_D005_Id, D149_C004_Id) VALUES ('{$D005_Id}', '{$resC004['C004_Id']}')");
                if($resC004['C004_Id'] == $g['empresaAtual']){
                    $D149_Id = $g['mysqlLastId'];
                }
            }
        }
        return $D149_Id;
    }

    public function verificarNCMDuplicadas()
    {
        global $g;
        $sqlIndex = mysql_query("SHOW INDEXES FROM D005 WHERE Key_name='I_UNIQUE_D005'");
        if(mysql_num_rows($sqlIndex)<=0){
            $sqlD005 = mysql_query("SELECT D005_Id,D005_Flag_Aplicacao, D005_Classificacao_Fiscal, COUNT(*) c FROM D005 WHERE D005_Flag_Ativo='S' GROUP BY D005_Classificacao_Fiscal, D005_Flag_Aplicacao HAVING c > 1 limit 20");
            if(mysql_num_rows($sqlD005)<=0){
                mysql_query("ALTER TABLE D005 ADD CONSTRAINT I_UNIQUE_D005 UNIQUE (D005_Flag_Aplicacao,D005_Classificacao_Fiscal)");
                mysql_query("ALTER TABLE D149 ADD CONSTRAINT I_UNIQUE_D149 UNIQUE (D149_D005_Id,D149_C004_Id)");
                return false;
            }else{
                $NCMDuplicadas = array();
                while($D005 = mysql_fetch_array($sqlD005)){
                    $NCMDuplicadas[] = $D005['D005_Classificacao_Fiscal']." - ".$D005['D005_Flag_Aplicacao'];
                }
                $NCMRetorno = implode(", ",$NCMDuplicadas);
                $mensagem = <<<EOT
                    Atenção! NCM's abaixo estão duplicadas: {$NCMRetorno}
EOT;
                return $mensagem;
            }
        }
        return false;
    }

    /**
     * demonstrativoNCM
     *
     * @param String $D005_Id
     * @return String
     */ 
    public function demonstrativoNCM($D005_Id)
    {
        global $g;
        $sql = mysql_query("SELECT D005_Classificacao_Fiscal,
                                   D005_Classificacao_Fiscal,
                                   D005_Observacao,
                                   D149_Aliquota_IPI,
                                   D149_Aliquota_PIS,
                                   D149_Aliquota_COFINS,
                                   D149_Flag_ST,
                                   D149_Valor_VA,
                                   D149_Percentual_Trib_Transparencia,
                                   D149_Imposto_Importacao,
                                   D149_Importacao_IPI,
                                   D149_Importacao_PIS,
                                   D149_Importacao_COFINS,
                                   D149_UF_ST,
                                   D149_Regras_MVA,
                                   D149_Aliquota_IRPJ,
                                   D149_Aliquota_CSLL,
                                   D037_Unidade 
                              FROM D005 
                         LEFT JOIN D149 ON D149_D005_Id=D005_Id AND D149_C004_Id = '{$g['empresaAtual']}' 
                         LEFT JOIN D037 ON D037_Id=D005_D037_Id_Tributavel
                             WHERE D005_Id = '{$D005_Id}'");
        $res = mysql_fetch_array($sql);
        $res['D149_Aliquota_IPI']                  = gCorrigeNumero($res['D149_Aliquota_IPI']);
        $res['D149_Valor_VA']                      = gCorrigeNumero($res['D149_Valor_VA']);
        $res['D149_Aliquota_PIS']                  = gCorrigeNumero($res['D149_Aliquota_PIS']);
        $res['D149_Aliquota_COFINS']               = gCorrigeNumero($res['D149_Aliquota_COFINS']);
        $res['D149_Percentual_Trib_Transparencia'] = gCorrigeNumero($res['D149_Percentual_Trib_Transparencia']);
        $res['D149_Imposto_Importacao']            = gCorrigeNumero($res['D149_Imposto_Importacao']);
        $res['D149_Importacao_IPI']                = gCorrigeNumero($res['D149_Importacao_IPI']);
        $res['D149_Importacao_PIS']                = gCorrigeNumero($res['D149_Importacao_PIS']);
        $res['D149_Importacao_COFINS']             = gCorrigeNumero($res['D149_Importacao_COFINS']);
        $res['D149_Importacao_IRPJ']               = gCorrigeNumero($res['D149_Importacao_IRPJ']);
        $res['D149_Importacao_CSLL']               = gCorrigeNumero($res['D149_Importacao_CSLL']);
        $res['D149_Flag_ST']                       = ($res['D149_Flag_ST'] == 'S') ? 'Sim' : 'Não';

        $arrayCST = array();
        $arrayCST["1"] = "00 - ENTRADA COM RECUPERAÇÃO DE CRÉDITO";
        $arrayCST["2"] = "01 - ENTRADA TRIBUTADA COM ALÍQUOTA ZERO";
        $arrayCST["3"] = "02 - ENTRADA ISENTA";
        $arrayCST["4"] = "03 - ENTRADA NÃO-TRIBUTADA";
        $arrayCST["5"] = "04 - ENTRADA IMUNE";
        $arrayCST["6"] = "05 - ENTRADA COM SUSPENSÃO";
        $arrayCST["7"] = "49 - OUTRAS ENTRADAS";
        $arrayCST["8"] = "50 - SAÍDA TRIBUTADA";
        $arrayCST["9"] = "51 - SAÍDA TRIBUTADA COM ALÍQUOTA ZERO";
        $arrayCST["10"] = "52 - SAÍDA ISENTA";
        $arrayCST["11"] = "53 - SAÍDA NÃO-TRIBUTADA";
        $arrayCST["12"] = "54 - SAÍDA IMUNE";
        $arrayCST["13"] = "55 - SAÍDA COM SUSPENSÃO";
        $arrayCST["14"] = "99 - OUTRAS SAÍDAS</option>";

        $dataHora = date('d/m/Y H:i:s');
        
        $retorno = <<<EOT
            <style>
                .tableDemonstrativo td {
                    border: 1px solid #ccc;
                    width: 25%;
                    background: #ede7de url(/hardness3/static/css/temas/padrao/images/ui-bg_dots-small_100_ede7de_2x2.png) 50% 50% repeat;
                }
                .tableDemonstrativo {
                    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                    //border-radius: 25px 25px 25px 25px;
                    padding: 3px 5px;
                }
            </style>
            <table class="tableDemonstrativo" cellpading=0 cellspacing=0 style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td colspan="4" style="text-align: center; font-weight: bold; background: #eee;">NCM: {$res['D005_Classificacao_Fiscal']}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">Observação:</td>
                    <td colspan="3" style="white-space: normal;">{$res['D005_Observacao']}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">IPI:</td>
                    <td>{$res['D149_Aliquota_IPI']}</td>
                    <td style="text-align: right;">Unidade Tributável:</td>
                    <td>{$res['D037_Unidade']}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">Substituição tributária:</td>
                    <td>{$res['D149_Flag_ST']}</td>
                    <td style="text-align: right;">Margem Valor Agregado:</td>
                    <td>{$res['D149_Valor_VA']}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">PIS:</td>
                    <td>{$res['D149_Aliquota_PIS']}</td>
                    <td style="text-align: right;">COFINS:</td>
                    <td>{$res['D149_Aliquota_COFINS']}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">IRPJ:</td>
                    <td>{$res['D149_Aliquota_IRPJ']}</td>
                    <td style="text-align: right;">CSLL:</td>
                    <td>{$res['D149_Aliquota_CSLL']}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">Excessões<br />ICM/IPI/MVA:</td>
                    <td colspan="3">{$res['D149_Regras_MVA']}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">UF ST:</td>
                    <td colspan="3">{$res['D149_UF_ST']}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">Percentual tributos<br />(Lei transp.):</td>
                    <td colspan="3">{$res['D149_Percentual_Trib_Transparencia']}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center; background: #eee;"><b>Importação</b></td>
                </tr>
                <tr>
                    <td style="text-align: right;">II%:</td>
                    <td>{$res['D149_Imposto_Importacao']}</td>
                    <td style="text-align: right;">IPI%:</td>
                    <td>{$res['D149_Importacao_IPI']}</td>
                </tr>
                <tr>
                    <td style="text-align: right;">PIS%:</td>
                    <td>{$res['D149_Importacao_PIS']}</td>
                    <td style="text-align: right;">COFINS%:</td>
                    <td>{$res['D149_Importacao_COFINS']}</td>
                </tr>
            </table><br />
            <span style="float: right; font-weight: bold;">{$dataHora}</span>      
EOT;
        return $retorno;
    }
}
