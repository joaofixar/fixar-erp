CREATE FUNCTION `Calcular_T006A_Valor_CBS`(xT006_Id bigint, xT006A_Id bigint, xT006A_Valor_Base_CBS_IBS decimal(15,2), xT006A_Aliquota_CBS decimal(7,4), xT006A_Valor_Diferimento_CBS decimal(15,2)) RETURNS decimal(15,2)
BEGIN
    RETURN IF(xT006A_Aliquota_CBS > 0, (xT006A_Valor_Base_CBS_IBS * (xT006A_Aliquota_CBS / 100)) - xT006A_Valor_Diferimento_CBS, 0);
END
