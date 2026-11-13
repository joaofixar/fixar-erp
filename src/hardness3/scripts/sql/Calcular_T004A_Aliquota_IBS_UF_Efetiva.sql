CREATE FUNCTION `Calcular_T004A_Aliquota_IBS_UF_Efetiva`(
    -- Ids para usar com SELECT (devem ser usados principalmente para personalização para evitar personalizar o T004_Gravar_Totalizacao_4 )
    xT004_Id bigint,
    xT004A_Id bigint,

    -- Parâmetros para cálculo
    xT004A_Aliquota_IBS_UF decimal(7, 4),
    xT004A_Percentual_Reducao_IBS_UF decimal(7, 4)
) RETURNS decimal(7, 4)
BEGIN
    RETURN xT004A_Aliquota_IBS_UF * (1 - xT004A_Percentual_Reducao_IBS_UF / 100);
END
