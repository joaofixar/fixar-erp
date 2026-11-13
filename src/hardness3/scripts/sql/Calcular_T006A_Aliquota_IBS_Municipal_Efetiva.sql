CREATE FUNCTION `Calcular_T006A_Aliquota_IBS_Municipal_Efetiva`(xT006_Id bigint, xT006A_Id bigint, xT006A_Aliquota_IBS_Municipal decimal(7,4), xT006A_Percentual_Reducao_IBS_Municipal decimal(7,4)) RETURNS decimal(7,4)
BEGIN
    RETURN xT006A_Aliquota_IBS_Municipal * (1 - xT006A_Percentual_Reducao_IBS_Municipal / 100);
END
