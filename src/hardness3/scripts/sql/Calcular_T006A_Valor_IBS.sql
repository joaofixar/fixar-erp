CREATE FUNCTION `Calcular_T006A_Valor_IBS`(xT006_Id bigint, xT006A_Id bigint, xT006A_Valor_IBS_Municipal decimal(15,2), xT006A_Valor_IBS_UF decimal(15,2)) RETURNS decimal(15,2)
BEGIN
    RETURN xT006A_Valor_IBS_Municipal + xT006A_Valor_IBS_UF;
END
