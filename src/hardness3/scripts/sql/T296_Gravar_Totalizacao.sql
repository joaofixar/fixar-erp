CREATE PROCEDURE `T296_Gravar_Totalizacao`(xT296_Id int(11))
BEGIN
    DECLARE xT295_Somar_PIS_Custo CHAR(1);
    DECLARE xT295_Somar_COFINS_Custo CHAR(1);
    DECLARE xT295_Somar_Seguro_Custo CHAR(1);
    DECLARE xT295_Somar_AFRMM_Custo CHAR(1);
    DECLARE xT295_Somar_ICMS_Pagar_Custo CHAR(1);
    DECLARE xT295_Somar_IPI_Custo CHAR(1);
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

    SELECT (T296_Aliquota_ICMS/100),
           (100-T296_Aliquota_ICMS),
           (T296_Aliquota_ICMS_Presumido/100),
           if(T296_Percentual_Reducao_ICMS>0,(T296_Percentual_Reducao_ICMS/100),1),
           if(ifnull(T296_Percentual_Reducao_ICMS_Presumido,0)>0,(T296_Percentual_Reducao_ICMS_Presumido/100),0),
           (T296_Aliquota_ICMS_PIS_COFINS/100),
           T296_Valor_Total_Despesas,
           T296_Valor_Base_Imposto_Importacao,
           T296_Valor_Base_IPI,
           T296_Valor_Base_PIS,
           T296_Valor_Base_COFINS,
           T296_Valor_Base_ICMS_Sem_Reducao,
           T296_Valor_Total_Preco_Nota_Fiscal,
           T296_Valor_Total_Custo_Final
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
      FROM T296
     WHERE T296_Id=xT296_Id; 
      
    UPDATE T296 SET T296_Valor_Total_Custo                   = T296_Valor_Custo_Unitario * T296_Quantidade,
                    T296_Valor_Custo_Total_ME_Origem         = T296_Valor_Custo_Unitario_ME_Origem * T296_Quantidade,
                    T296_Valor_Total_Despesas                = xValor_Total_Despesas,
                    T296_Valor_Despesas                      = T296_Valor_Total_Despesas / T296_Quantidade,
                    T296_Valor_Base_Imposto_Importacao       = xValor_Base_II,
                    T296_Valor_Imposto_Importacao            = T296_Valor_Base_Imposto_Importacao*(T296_Aliquota_Imposto_Importacao/100),
                    T296_Valor_Base_IPI                      = xValor_Base_IPI + T296_Valor_Imposto_Importacao,
                    T296_Valor_IPI                           = T296_Valor_Base_IPI * ( T296_Aliquota_IPI / 100 ),
                    T296_Valor_Base_PIS                      = xValor_Base_PIS,
                    T296_Valor_PIS                           = ( T296_Aliquota_PIS/100 ) *  T296_Valor_Base_PIS,
                    T296_Valor_Base_COFINS                   = xValor_Base_COFINS,
                    T296_Valor_COFINS                        = ( T296_Aliquota_COFINS/100 ) *  T296_Valor_Base_COFINS,
                    T296_Valor_Base_ICMS_Sem_Reducao         = xValor_Base_ICMS_Sem_Reducao + T296_Valor_PIS + T296_Valor_COFINS + T296_Valor_IPI + T296_Valor_Imposto_Importacao,                   
                    T296_Valor_Base_ICMS_Normal              = round( ( T296_Valor_Base_ICMS_Sem_Reducao * 100 ) / xAliquota_ICMS_Normal , 2),                                                                                                                           
                    T296_Valor_Base_ICMS_Presumido           = round( ( ( T296_Valor_Base_ICMS_Sem_Reducao * 100 ) / xAliquota_ICMS_Normal ) * IF(xPercentual_Reducao_ICMS_Presumido>0, xPercentual_Reducao_ICMS_Presumido, 1) , 2 ),
                    T296_Valor_ICMS                          = (T296_Valor_Base_ICMS_Normal * xAliquota_ICMS) - ((T296_Valor_Base_ICMS_Normal * xAliquota_ICMS) * xPercentual_Reducao_ICMS_Presumido),
                    T296_Valor_ICMS_Presumido                = T296_Valor_Base_ICMS_Presumido * xAliquota_ICMS_Presumido,
                    T296_Valor_ICMS_Pagar                    = T296_Valor_ICMS - T296_Valor_ICMS_Presumido,
                    T296_Peso_Kg_Liquido_Total               = T296_Peso_Kg_Liquido_Unitario * T296_Quantidade,
                    T296_Valor_Total_Preco_Nota_Fiscal       = xValor_Total_Preco_Nota_Fiscal + T296_Valor_Base_Imposto_Importacao + T296_Valor_Imposto_Importacao + T296_Valor_PIS + T296_Valor_COFINS,
                    T296_Valor_Preco_Unitario_Nota_Fiscal    = T296_Valor_Total_Preco_Nota_Fiscal / T296_Quantidade,
                    T296_Valor_Total_Custo_Final             = xValor_Total_Custo_Final +
                                                               T296_Valor_Imposto_Importacao +
                                                               T296_Valor_PIS +
                                                               T296_Valor_COFINS +
                                                               T296_Valor_ICMS_Pagar +
                                                               T296_Valor_IPI,
                    T296_Valor_Custo_Final_Unitario          = T296_Valor_Total_Custo_Final / T296_Quantidade
              WHERE T296_Id=xT296_Id;
  
END;
