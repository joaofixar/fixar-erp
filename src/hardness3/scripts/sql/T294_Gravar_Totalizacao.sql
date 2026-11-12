CREATE PROCEDURE `T294_Gravar_Totalizacao`(xT294_Id int(11))
BEGIN
    DECLARE xT293_Somar_PIS_Custo CHAR(1);
    DECLARE xT293_Somar_COFINS_Custo CHAR(1);
    DECLARE xT293_Somar_Seguro_Custo CHAR(1);
    DECLARE xT293_Somar_AFRMM_Custo CHAR(1);
    DECLARE xT293_Somar_ICMS_Pagar_Custo CHAR(1);
    DECLARE xT293_Somar_IPI_Custo CHAR(1);
    DECLARE xAliquota_ICMS DECIMAL(5,2);
    DECLARE xAliquota_ICMS_Normal DECIMAL(5,2);
    DECLARE xAliquota_ICMS_Presumido DECIMAL(5,2);
    DECLARE xAliquota_ICMS_PIS_COFINS DECIMAL(5,2);
    DECLARE xPercentual_Reducao_ICMS DECIMAL(19,4);
    DECLARE xPercentual_Reducao_ICMS_Presumido DECIMAL(19,4);
    DECLARE xValor_Total_Despesas DECIMAL(19,4);
    DECLARE xValor_Base_PIS DECIMAL(19,6);
    DECLARE xValor_Base_COFINS DECIMAL(19,6);
    DECLARE xValor_Base_ICMS_Sem_Reducao DECIMAL(19,6);
    DECLARE xValor_Total_Preco_Nota_Fiscal DECIMAL(19,6);
    DECLARE xValor_Total_Custo_Final DECIMAL(19,6);
    DECLARE xValor_Base_IPI DECIMAL(19,6);
    DECLARE xValor_Base_II DECIMAL(19,6);

    SELECT (T294_Aliquota_ICMS/100),
           (100-T294_Aliquota_ICMS),
           (T294_Aliquota_ICMS_Presumido/100),
           if(T294_Percentual_Reducao_ICMS>0,(T294_Percentual_Reducao_ICMS/100),1),
           if(ifnull(T294_Percentual_Reducao_ICMS_Presumido,0)>0,(T294_Percentual_Reducao_ICMS_Presumido/100),0),
           (T294_Aliquota_ICMS_PIS_COFINS/100),
           T294_Valor_Total_Despesas,
           T294_Valor_Base_Imposto_Importacao,
           T294_Valor_Base_IPI,
           T294_Valor_Base_PIS,
           T294_Valor_Base_COFINS,
           T294_Valor_Base_ICMS_Sem_Reducao,
           T294_Valor_Total_Preco_Nota_Fiscal,
           T294_Valor_Total_Custo_Final
      INTO xAliquota_ICMS,
           xAliquota_ICMS_Normal,
           xAliquota_ICMS_Presumido,
           xPercentual_Reducao_ICMS,
           xPercentual_Reducao_ICMS_Presumido,
           xAliquota_ICMS_PIS_COFINS,
           xValor_Total_Despesas,
           xValor_Base_II,
           xValor_Base_IPI,
           xValor_Base_PIS,
           xValor_Base_COFINS,
           xValor_Base_ICMS_Sem_Reducao,
           xValor_Total_Preco_Nota_Fiscal,
           xValor_Total_Custo_Final
      FROM T294
     WHERE T294_Id=xT294_Id; 
      
    UPDATE T294 SET T294_Valor_Total_Custo                   = T294_Valor_Custo_Unitario * T294_Quantidade,
                    T294_Valor_Total_Despesas                = xValor_Total_Despesas,
                    T294_Valor_Despesas                      = T294_Valor_Total_Despesas / T294_Quantidade,
                    T294_Valor_Base_Imposto_Importacao       = xValor_Base_II,
                    T294_Valor_Imposto_Importacao            = T294_Valor_Base_Imposto_Importacao*(T294_Aliquota_Imposto_Importacao/100),
                    T294_Valor_Base_IPI                      = xValor_Base_IPI + T294_Valor_Imposto_Importacao,
                    T294_Valor_IPI                           = T294_Valor_Base_IPI * ( T294_Aliquota_IPI / 100 ),
                    T294_Valor_Base_PIS                      = xValor_Base_PIS,
                    T294_Valor_PIS                           = ( T294_Aliquota_PIS/100 ) *  T294_Valor_Base_PIS,
                    T294_Valor_Base_COFINS                   = xValor_Base_COFINS,
                    T294_Valor_COFINS                        = ( T294_Aliquota_COFINS/100 ) *  T294_Valor_Base_COFINS,
                    T294_Valor_Base_ICMS_Sem_Reducao         = xValor_Base_ICMS_Sem_Reducao + T294_Valor_PIS + T294_Valor_COFINS + T294_Valor_IPI + T294_Valor_Imposto_Importacao,                   
                    T294_Valor_Base_ICMS_Normal              = round( ( T294_Valor_Base_ICMS_Sem_Reducao * 100 ) / xAliquota_ICMS_Normal , 2),                                                                                                                           
                    T294_Valor_Base_ICMS_Presumido           = round( ( ( T294_Valor_Base_ICMS_Sem_Reducao * 100 ) / xAliquota_ICMS_Normal ) * IF(xPercentual_Reducao_ICMS_Presumido>0, xPercentual_Reducao_ICMS_Presumido, 1) , 2 ),
                    T294_Valor_ICMS                          = (T294_Valor_Base_ICMS_Normal * xAliquota_ICMS) - ((T294_Valor_Base_ICMS_Normal * xAliquota_ICMS) * xPercentual_Reducao_ICMS_Presumido),
                    T294_Valor_ICMS_Presumido                = T294_Valor_Base_ICMS_Presumido * xAliquota_ICMS_Presumido,
                    T294_Valor_ICMS_Pagar                    = T294_Valor_ICMS - T294_Valor_ICMS_Presumido,
                    T294_Peso_Kg_Liquido_Total               = T294_Peso_Kg_Liquido_Unitario * T294_Quantidade,
                    T294_Valor_Total_Preco_Nota_Fiscal       = xValor_Total_Preco_Nota_Fiscal + T294_Valor_Base_Imposto_Importacao + T294_Valor_Imposto_Importacao + T294_Valor_PIS + T294_Valor_COFINS,
                    T294_Valor_Preco_Unitario_Nota_Fiscal    = T294_Valor_Total_Preco_Nota_Fiscal / T294_Quantidade,
                    T294_Valor_Total_Custo_Final             = xValor_Total_Custo_Final +
                                                               T294_Valor_Imposto_Importacao +
                                                               T294_Valor_PIS +
                                                               T294_Valor_COFINS +
                                                               T294_Valor_ICMS_Pagar +
                                                               T294_Valor_IPI,
                    T294_Valor_Custo_Final_Unitario          = T294_Valor_Total_Custo_Final / T294_Quantidade
              WHERE T294_Id=xT294_Id;
  
END;
