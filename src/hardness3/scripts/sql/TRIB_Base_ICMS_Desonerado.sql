CREATE FUNCTION `TRIB_Base_ICMS_Desonerado`(xValor_Total_Custo_Final decimal(20,2), xValor_IPI decimal(20,2), xPercentual_Reducao_ICMS decimal(5,2), xValor_Base_ICMS decimal(20,2),xAliquota_ICMS_Oculto decimal(10,2),xD006_Flag_Somar_IPI_Base_ICMS varchar(1), xSituacao_Tributaria int(11)) RETURNS decimal(20,2)
BEGIN

    #IF(xAliquota_ICMS_Oculto>0 AND (xSituacao_Tributaria = 3 OR xSituacao_Tributaria = 4 OR xSituacao_Tributaria = 5 OR xSituacao_Tributaria = 6 OR xSituacao_Tributaria = 7 OR xSituacao_Tributaria = 10 OR xSituacao_Tributaria = 11 OR xSituacao_Tributaria = 13 OR xSituacao_Tributaria = 14)) THEN
    IF(xAliquota_ICMS_Oculto>0) THEN
   
        RETURN xValor_Total_Custo_Final + if(xD006_Flag_Somar_IPI_Base_ICMS='S',xValor_IPI,0) - IF(xPercentual_Reducao_ICMS>0,xValor_Base_ICMS,0);

    ELSE 

        RETURN 0;

    END IF;
END



