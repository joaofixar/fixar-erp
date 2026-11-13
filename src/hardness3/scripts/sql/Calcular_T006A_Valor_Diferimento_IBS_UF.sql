CREATE FUNCTION `Calcular_T006A_Valor_Diferimento_IBS_UF`(xT006_Id bigint, xT006A_Id bigint, xT006A_Valor_Base_CBS_IBS decimal(15,2), xT006A_Aliquota_IBS_UF decimal(7,4), xT006A_Percentual_Diferimento_IBS_UF decimal(7,4)) RETURNS decimal(15,2)
BEGIN
    RETURN xT006A_Valor_Base_CBS_IBS * (xT006A_Aliquota_IBS_UF / 100) * (xT006A_Percentual_Diferimento_IBS_UF / 100);
END
