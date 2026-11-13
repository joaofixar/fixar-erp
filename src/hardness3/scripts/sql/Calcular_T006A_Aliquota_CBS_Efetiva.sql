CREATE FUNCTION `Calcular_T006A_Aliquota_CBS_Efetiva`(xT006_Id bigint, xT006A_Id bigint, xT006A_Aliquota_CBS decimal(7,4), xT006A_Percentual_Reducao_CBS decimal(7,4)) RETURNS decimal(7,4)
BEGIN
    RETURN xT006A_Aliquota_CBS * (1 - xT006A_Percentual_Reducao_CBS / 100);
END
